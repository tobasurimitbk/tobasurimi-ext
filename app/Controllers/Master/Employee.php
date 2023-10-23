<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\BagianModel;
use App\Models\DivisisModel;
use App\Models\ProvincesModel;
use App\Models\EmployeesModel;
use App\Models\GajiConjunctionModel;
use App\Models\GajiDivisiModel;
use App\Models\MetadataModel;
use App\Models\UserModel;
use App\Models\TunjanganModel;
use Exception;

class Employee extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $ProvincesModel;
    protected $EmployeesModel;
    protected $GajiConjunctionModel;
    protected $UserModel;
    protected $TunjanganModel;
    protected $GajiDivisiModel;
    protected $DivisionModel;
    protected $MetaDataModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->ProvincesModel = new ProvincesModel();
        $this->EmployeesModel = new EmployeesModel();
        $this->GajiConjunctionModel = new GajiConjunctionModel();
        $this->UserModel = new UserModel();
        $this->TunjanganModel = new TunjanganModel();
        $this->GajiDivisiModel = new GajiDivisiModel();
        $this->DivisionModel = new DivisisModel();
        $this->MetaDataModel = new MetadataModel();
    }

    public function employee()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $data = [
            "dataProvinces" => $dataProvinces,
            "tipeEmployee" => $this->MetaDataModel->where('name', "Tipe Karyawan")->findAll()
        ];

        return view('Master/employee/index', $data);
    }

    public function dropdownEmployee()
    {
        $dataEmployee = [];
        // employee no user
        $dataEmployee = $this->EmployeesModel->getEmployees($this->this_company_id);

        // employee with user when user deleted
        $secondDataEmployee = $this->EmployeesModel->getEmployeesUserDelete($this->this_company_id);

        if ($secondDataEmployee) {
            foreach ($secondDataEmployee as $item) {
                array_push($dataEmployee, $item);
            }
        }

        $finalDataEmployee = [];

        foreach ($dataEmployee as $item) {
            // check employee with user when user not deleted
            $check = $this->UserModel->countUserByEmployeeId($item["id"]);

            if ($check < 1) {
                array_push($finalDataEmployee, $item);
            }
        }
        $data = [
            "data" => $finalDataEmployee
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownEmployeeByDivision()
    {
        $division = $this->request->getGet("division");
        $dataEmployee = $this->EmployeesModel->getEmployeesByDivision($this->this_company_id, $division);

        $data = [
            "data" => $dataEmployee
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownEmployeePIC()
    {
        $dataEmployee = [];
        $responseEmployee = curl_request("GET", "/employees/all?idCompany=$this->this_company_id", $this->token);
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

        $data = [
            "data" => $dataEmployee
        ];

        echo json_encode($data);
        return;
    }

    public function allEmployee()
    {
        $draw = $this->request->getVar('draw');
        $row = $this->request->getVar('start');
        $rowperpage = $this->request->getVar('length');
        $temp = $this->request->getVar('order');
        $columnIndex = $temp[0]['column'];

        $temp = $this->request->getVar('columns');
        $columnName = $temp[$columnIndex]['data'];

        $temp = $this->request->getVar('order');
        $columnSortOrder = $temp[0]['dir'];

        $search = $this->request->getVar('search');

        $values = [
            "company_id"    => $this->this_company_id,
            "search"        => $search
        ];

        $totalRecords = $this->EmployeesModel->total_list(array());
        $totalRecordwithFilter = $this->EmployeesModel->total_list($values);

        $res = $this->EmployeesModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        $bagianModel = new BagianModel();

        for ($i = 0; $i < count($res); $i++) {

            $bagian = $bagianModel->where('id', $res[$i]['bagian_id'])->first();

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "nip" => $res[$i]["nip"],
                "name" => $res[$i]["name"],
                "divisionName" => $res[$i]["divisionName"],
                // "email" => $res[$i]["email"] == null ? "-" : $res[$i]["email"],
                // "phone_no" => $res[$i]["phone_no"] == null ? "-" :  $res[$i]["phone_no"],
                "dob" => $res[$i]["dob"] == "0000-00-00" ? "-" : date("d/m/Y", strtotime($res[$i]["dob"])),
                "gender" => $res[$i]["gender"],
                "acc_no" => $res[$i]["acc_no"],
                "status" => $res[$i]["status"],
                "tipe" => $res[$i]['tipe'] ==  null ? "-" : $res[$i]['tipe'],
                "bagianName" => ($bagian == null) ? "-" : $bagian['nama_bagian']
            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $this->response->setJSON($response);
    }

    public function saveEmployee()
    {
        try {

            $file = $this->request->getFile("employeeImg");
            $values = [
                "company_id" => $this->this_company_id,
                "nip" => $this->request->getPost("nip"), // required
                "name" => $this->request->getPost("name"), // required
                "gender" => $this->request->getPost("gender"), // required
                "join_date" => $this->request->getPost("join_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("join_date")))) : "",
                "dob" => $this->request->getPost("dob") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dob")))) : "",
                "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"), // required
                "phone_no" => $this->request->getPost("phone_no") ?? "",
                "acc_no" => $this->request->getPost("acc_no") ?? "",
                "email" => $this->request->getPost("email") ?? "",
                "address" => $this->request->getPost("address") ?? "",
                "status" => $this->request->getPost("status") ?? "",
                "nik" => $this->request->getPost("nik") ?? "",
                "child" => $this->request->getPost("child") ?? 0,
                "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT") ?? 0,
                "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT") ?? 0,
                "postal_code" => $this->request->getPost("zip_code") ?? "",
                "religion_id" => formatter($this->request->getPost("religion_id"), "STR_TO_INT") ?? 0,
                "marriage_id" => formatter($this->request->getPost("marriage_id"), "STR_TO_INT") ?? 0,
                "jabatan_id" => $this->request->getPost("jabatan_id") ?? "", // required
                "bank_name" => $this->request->getPost("bank_name") ?? "",
                "owner_name" => $this->request->getPost("owner_name") ?? "",
                "pin"  => $this->request->getPost("pin") ?? "",
                "pendidikan" => formatter($this->request->getPost("pendidikan"), "STR_TO_INT") ?? 0,
                "tipe" => $this->request->getPost('tipe'),
                "bagian_id" => $this->request->getPost('bagian_id')
            ];
            if (!empty($file->getName())) {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));
                    $values["employee_img"] = $image;
                }
            }

            if (isset($values)) {
                // insert employee
                $insert = $this->EmployeesModel->insert($values);
                $gajiDivisi = $this->GajiDivisiModel->getGajiByDivisionReturnIDOnArray(
                    $this->request->getPost("division_id"),
                    $this->this_company_id
                );

                $res = [];
                foreach ($gajiDivisi as $g) {
                    if (in_array($g, \array_keys($_POST))) {
                        $angka = preg_replace("/[^0-9,]/", "", $this->request->getVar($g));
                        $angka = str_replace(",", ".", $angka);
                        $angkaDesimal = number_format((float) $angka, 3, '.', '');

                        $res[] = [
                            'employee_id' => $insert,
                            'tunjangan_id' => $g,
                            'nominal' => $angkaDesimal
                        ];
                    }
                }

                $this->GajiConjunctionModel->insertBatch($res);

                if ($insert) {
                    return \response()->setJSON([
                        "status" => true,
                        "message" => "Data Employee Baru Berhasil disimpan",
                        'token' => csrf_hash()
                    ]);
                } else {
                    return \response()->setJSON([
                        "status" => \false,
                        "message" => "Data Employee Baru gagal disimpan",
                        'token' => csrf_hash()
                    ]);
                }
            } else {
                return \response()->setJSON([
                    "status" => \false,
                    "message" => "Format gambar harus bertipe png, jpg, jpeg",
                    'token' => csrf_hash()
                ]);
            }
        } catch (Exception $e) {

            return \response()->setJSON([
                'message' => "Terjadi kesalahan saat input data employee baru (Code: 500)",
                'status' => \false
            ]);
        }
    }

    public function updateEmployee()
    {
        try {

            $id = $this->request->getPost("id");
            $status = $this->request->getPost("status") === "Aktif" ? 'Aktif' : 'Non Aktif';
            $name = $this->request->getPost("name");

            // update status user when employee status changed
            $user = $this->UserModel->getByEmployeeId($id);

            if ($user) {
                $this->UserModel->where(['id' => $user->id])->set(['status' => $status, 'name' => $name])->update();
            }

            $values = [
                "company_id" => $this->this_company_id,
                "nip" => $this->request->getPost("nip"), // required
                "name" => $this->request->getPost("name"), // required
                "gender" => $this->request->getPost("gender"), // required
                "join_date" => $this->request->getPost("join_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("join_date")))) : "",
                "dob" => $this->request->getPost("dob") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dob")))) : "",
                "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"), // required
                "phone_no" => $this->request->getPost("phone_no") ?? "",
                "acc_no" => $this->request->getPost("acc_no") ?? "",
                "email" => $this->request->getPost("email") ?? "",
                "address" => $this->request->getPost("address") ?? "",
                "status" => $this->request->getPost("status") ?? "",
                "nik" => $this->request->getPost("nik") ?? "",
                "child" => $this->request->getPost("child") ?? 0,
                "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT") ?? 0,
                "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT") ?? 0,
                "postal_code" => $this->request->getPost("zip_code") ?? "",
                "religion_id" => formatter($this->request->getPost("religion_id"), "STR_TO_INT") ?? 0,
                "marriage_id" => formatter($this->request->getPost("marriage_id"), "STR_TO_INT") ?? 0,
                "jabatan_id" => $this->request->getPost("jabatan_id") ?? "", // required
                "bank_name" => $this->request->getPost("bank_name") ?? "",
                "owner_name" => $this->request->getPost("owner_name") ?? "",
                "pin"  => $this->request->getPost("pin") ?? "",
                "pendidikan" => formatter($this->request->getPost("pendidikan"), "STR_TO_INT") ?? 0,
                "tipe" => $this->request->getPost('tipe'),
                "bagian_id" => $this->request->getPost('bagian_id')
            ];
            $file = $this->request->getFile("employeeImg");
            if (!empty($file->getName())) {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));
                    $values["employee_img"] = $image;
                }
            }

            if (isset($values)) {
                if ($this->EmployeesModel->update($id, $values)) {

                    $gajiDivisi = $this->GajiDivisiModel->getGajiByDivisionReturnIDOnArray(
                        $this->request->getPost("division_id"),
                        $this->this_company_id
                    );

                    // delete first
                    $this->GajiConjunctionModel->where('employee_id', $id)
                        ->delete();

                    $res = [];
                    foreach ($gajiDivisi as $g) {
                        if (in_array($g, \array_keys($_POST))) {
                            $angka = preg_replace("/[^0-9,]/", "", $this->request->getVar($g));
                            $angka = str_replace(",", ".", $angka);
                            $angkaDesimal = number_format((float) $angka, 3, '.', '');

                            $res[] = [
                                'employee_id' => $id,
                                'tunjangan_id' => $g,
                                'nominal' => $angkaDesimal
                            ];
                        }
                    }

                    $this->GajiConjunctionModel->insertBatch($res);

                    return \response()->setJSON([
                        "status"    => true,
                        "message"   => "Data Employee Berhasil Diupdate",
                        'token' => csrf_hash()
                    ]);
                } else {
                    return \response()->setJSON([
                        "status" => \false,
                        "message" => "Data Employee gagal diubah",
                        'token' => csrf_hash()
                    ]);
                }
            } else {
                return \response()->setJSON([
                    "status" => \false,
                    "message" => "Format gambar harus bertipe png, jpg, jpeg",
                    'token' => csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            return \response()->setJSON([
                'message' => "Terjadi kesalahan saat input data employee baru (Code: 500)",
                'status' => \false
            ]);
        }
    }

    public function getByIdEmployee($id = null)
    {
        if (!empty($id)) {
            $res = $this->EmployeesModel->get_by_id($id);
            $res[0]["join_date"] = date("d/m/Y", strtotime($res[0]["join_date"]));
            $res[0]["dob"] = date("d/m/Y", strtotime($res[0]["dob"]));
            //            $response = curl_request("GET", "/employees/$id?idCompany=$this->this_company_id", $this->token);
            $data = $this->GajiConjunctionModel->getKomponenByEmployeeId($id);

            $res = (object) $res[0];

            $res->employee_img = $res->employee_img ? $res->employee_img : "";

            $res->komponen_gaji = $data;

            if ($res) {
                $data = [
                    "status"  => true,
                    "data"  => $res,
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditemukan';
                $data = [
                    "status" => false,
                    "message"  => $message
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Tidak Ada Id"
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteEmployee()
    {
        try {
            $id = $this->request->getPost("id");
            $UserModel = new UserModel();

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                $UserModel->where('employee_id', $id)->delete();
                if ($this->EmployeesModel->update($id, $values)) {
                    $this->GajiConjunctionModel->where('employee_id', $id)->delete();
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Dihapus';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function getKomponenGaji()
    {
        // variable declare
        $divisionID = $this->request->getVar('divisi_id');
        $employeeID = $this->request->getVar('employee_id');

        return \response()->setJSON([
            'status' => true,
            'komponenGaji' => (empty($employeeID)) ?
                $this->GajiDivisiModel->getGajiByDivision(
                    $divisionID,
                    $this->this_company_id
                )
                : $this->GajiDivisiModel->getGajiByDivisionAndEmployee(
                    $divisionID,
                    $this->this_company_id,
                    $employeeID
                ),
            'employeeID' => $employeeID,
            'divisi' => $this->DivisionModel->where('id', $divisionID)->first(),
            'divisionIID' => $divisionID
        ]);
    }
}
