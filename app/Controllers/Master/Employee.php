<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\AttendancesUnitModel;
use App\Models\BagianModel;
use App\Models\CitiesModel;
use App\Models\DivisisModel;
use App\Models\ProvincesModel;
use App\Models\EmployeesModel;
use App\Models\EmployeesUnitsModel;
use App\Models\GajiConjunctionModel;
use App\Models\GajiDivisiModel;
use App\Models\GolonganModel;
use App\Models\JabatanModel;
use App\Models\MetadataModel;
use App\Models\PayrollsModel;
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
    protected $GolonganModel;
    protected $JabatanModel;
    protected $BagianModel;
    protected $CitiesModel;
    protected $PayrollModel;
    protected $AttendanceUnitModel;
    protected $EmployeesUnitsModel;
    protected $attendanceUnit;

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
        $this->GolonganModel = new GolonganModel();
        $this->JabatanModel = new JabatanModel();
        $this->BagianModel = new BagianModel();
        $this->CitiesModel = new CitiesModel();
        $this->PayrollModel = new PayrollsModel();
        $this->AttendanceUnitModel = new AttendancesUnitModel();
        $this->EmployeesUnitsModel = new EmployeesUnitsModel();
        $this->attendanceUnit = new AttendancesUnit();
    }

    public function employee()
    {
        //Get Provinces
        $dataAttendanceUnit = $this->AttendanceUnitModel->where('company_id', $this->this_company_id)->findAll();
        $divisi = $this->DivisionModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll();
        $tipeEmployee = $this->GolonganModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findall();

        $data = [
            "tipeEmployee" => $tipeEmployee,
            "dataAttendanceUnit" => $dataAttendanceUnit,
            "divisi" => $divisi,
        ];

        return view('Master/employee/index', $data);
    }

    public function dropdownEmployee()
    {
        $dataEmployee = $this->EmployeesModel->getEmployees($this->this_company_id);
        $finalDataEmployee = [];

        foreach ($dataEmployee as $d) {
            $check = $this->UserModel->where('employee_id', $d['id'])
                ->where('deletedAt', null)
                ->first();

            if ($check == null) {
                array_push($finalDataEmployee, $d);
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

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "company_id" => $this->this_company_id,
        ];

        $condition = [
            "employees.company_id" => $this->this_company_id,
            "employees.deletedAt" => null,
            // "divisis.deletedAt" => null,
            // "bagian.deletedAt" => null
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "search" => $this->request->getGet("search"),
            "division_id" => decrypt($this->request->getGet("division_id")),
            "bagian_id" => ($this->request->getGet("bagian_id")),
            "tipe" => ($this->request->getGet("tipe")),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataModel = $this->EmployeesModel->getList($condition, $addCondition, $limit, $offset);

        $dataEmployee = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataModel['data'] as $data) {

            array_push($dataEmployee, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "id_text" => $data->id,
                "nip" => $data->nip,
                "name" => $data->name,
                "divisionName" => $data->divisi,
                "dob" => $data->dob == "0000-00-00" ? '-' : date('d/m/Y', strtotime($data->dob)),
                'attendance_sync' => $data->attendance_sync,
                "gender" => strtoupper($data->gender),
                "acc_no" => strtoupper($data->acc_no),
                "tipe" => strtoupper($data->tipe),
                "status" => strtoupper($data->status),
                "bagianName" => strtoupper($data->nama_bagian)
            ]);
        }

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $dataModel['totalData'],
            "recordsFiltered" => $dataModel['totalFilteredData'],
            "data" => $dataEmployee,
            "payload" => $payload
        ];

        return response()->setJSON($data);
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
            "company_id" => $this->this_company_id,
            "search" => $search
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
                "id" => encrypt($res[$i]["id"]),
                "nip" => $res[$i]["nip"],
                "name" => $res[$i]["name"],
                "divisionName" => $res[$i]["divisionName"],
                // "email" => $res[$i]["email"] == null ? "-" : $res[$i]["email"],
                // "phone_no" => $res[$i]["phone_no"] == null ? "-" :  $res[$i]["phone_no"],
                "dob" => $res[$i]["dob"] == "0000-00-00" ? "-" : date("d/m/Y", strtotime($res[$i]["dob"])),
                "gender" => $res[$i]["gender"],
                "acc_no" => $res[$i]["acc_no"],
                "status" => $res[$i]["status"],
                "tipe" => $res[$i]['tipe'] == null ? "-" : $res[$i]['tipe'],
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
                "name" => strtoupper($this->request->getVar("name")), // required
                "gender" => $this->request->getPost("gender"), // required
                "join_date" => $this->request->getPost("join_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("join_date")))) : "",
                "dob" => $this->request->getPost("dob") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dob")))) : "",
                "division_id" => decrypt($this->request->getPost("division_id")), // required
                "phone_no" => $this->request->getPost("phone_no") ?? "",
                "acc_no" => $this->request->getPost("acc_no") ?? "",
                "email" => $this->request->getPost("email") ?? "",
                "address" => $this->request->getPost("address") ?? "",
                "status" => $this->request->getPost("status") ?? "",
                "nik" => $this->request->getPost("nik") ?? "",
                "child" => $this->request->getPost("child") ?? 0,
                "province_id" => decrypt($this->request->getPost("province_id")) ?? 0,
                "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT") ?? 0,
                "postal_code" => $this->request->getPost("zip_code") ?? "",
                "religion_id" => decrypt($this->request->getPost("religion_id")) ?? 0,
                "marriage_id" => decrypt($this->request->getPost("marriage_id")) ?? 0,
                "jabatan_id" => decrypt($this->request->getPost("jabatan_id")) ?? "", // required
                "bank_name" => $this->request->getPost("bank_name") ?? "",
                "owner_name" => $this->request->getPost("owner_name") ?? "",
                "pin" => $this->request->getPost("pin") ?? "",
                "pendidikan" => decrypt($this->request->getPost("pendidikan")) ?? 0,
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
                    decrypt($this->request->getPost("division_id")),
                    $this->this_company_id
                );

                $res = [];
                foreach ($gajiDivisi as $g) {
                    if (in_array($g, \array_keys($_POST))) {
                        $angka = $this->request->getVar($g);
                        $res[] = [
                            'employee_id' => $insert,
                            'tunjangan_id' => $g,
                            'nominal' => $angka
                        ];
                    }
                }

                $this->GajiConjunctionModel->insertBatch($res);

                if ($insert) {
                    return response()->setJSON([
                        "status" => true,
                        "message" => "Data Employee Baru Berhasil disimpan",
                        "id" => encrypt($insert),
                        'token' => csrf_hash()
                    ]);
                } else {
                    return response()->setJSON([
                        "status" => false,
                        "message" => "Data Employee Baru gagal disimpan",
                        'token' => csrf_hash()
                    ]);
                }
            } else {
                return response()->setJSON([
                    "status" => false,
                    "message" => "Format gambar harus bertipe png, jpg, jpeg",
                    'token' => csrf_hash()
                ]);
            }
        } catch (Exception $e) {

            return response()->setJSON([
                'message' => "Terjadi kesalahan saat input data employee baru (Code: 500)",
                'status' => false
            ]);
        }
    }

    public function updateEmployee()
    {
        try {

            $id = decrypt($this->request->getPost("id"));
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
                "name" => strtoupper($this->request->getVar("name")), // required
                "gender" => $this->request->getPost("gender"), // required
                "join_date" => $this->request->getPost("join_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("join_date")))) : "",
                "dob" => $this->request->getPost("dob") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dob")))) : "",
                "division_id" => decrypt($this->request->getPost("division_id")), // required
                "phone_no" => $this->request->getPost("phone_no") ?? "",
                "acc_no" => $this->request->getPost("acc_no") ?? "",
                "email" => $this->request->getPost("email") ?? "",
                "address" => $this->request->getPost("address") ?? "",
                "status" => $this->request->getPost("status") ?? "",
                "nik" => $this->request->getPost("nik") ?? "",
                "child" => $this->request->getPost("child") ?? 0,
                "province_id" => decrypt($this->request->getPost("province_id")) ?? 0,
                "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT") ?? 0,
                "postal_code" => $this->request->getPost("zip_code") ?? "",
                "religion_id" => decrypt($this->request->getPost("religion_id")) ?? 0,
                "marriage_id" => decrypt($this->request->getPost("marriage_id")) ?? 0,
                "jabatan_id" => decrypt($this->request->getPost("jabatan_id")) ?? "", // required
                "bank_name" => $this->request->getPost("bank_name") ?? "",
                "owner_name" => $this->request->getPost("owner_name") ?? "",
                "pin" => $this->request->getPost("pin") ?? "",
                "pendidikan" => decrypt($this->request->getPost("pendidikan")) ?? 0,
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
                        decrypt($this->request->getPost("division_id")),
                        $this->this_company_id
                    );

                    // delete first
                    $this->GajiConjunctionModel->where('employee_id', $id)
                        ->delete();

                    $res = [];
                    foreach ($gajiDivisi as $g) {
                        if (in_array($g, \array_keys($_POST))) {
                            $angka = $this->request->getVar($g);

                            $res[] = [
                                'employee_id' => $id,
                                'tunjangan_id' => $g,
                                'nominal' => $angka
                            ];
                        }
                    }

                    $this->GajiConjunctionModel->insertBatch($res);

                    return response()->setJSON([
                        "status" => true,
                        "message" => "Data Employee Berhasil Diupdate",
                        'token' => csrf_hash()
                    ]);
                } else {
                    return response()->setJSON([
                        "status" => false,
                        "message" => "Data Employee gagal diubah",
                        'token' => csrf_hash()
                    ]);
                }
            } else {
                return response()->setJSON([
                    "status" => false,
                    "message" => "Format gambar harus bertipe png, jpg, jpeg",
                    'token' => csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "Terjadi kesalahan saat input data employee baru (Code: 500)",
                'status' => false
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
                    "status" => true,
                    "data" => $res,
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditemukan';
                $data = [
                    "status" => false,
                    "message" => $message
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status" => false,
                "message" => "Tidak Ada Id"
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteEmployee()
    {
        try {
            $id = decrypt($this->request->getPost("id"));
            // $UserModel = new UserModel();

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                // $UserModel->where('employee_id', $id)->delete();
                if ($this->EmployeesModel->update($id, $values)) {
                    $this->GajiConjunctionModel->where('employee_id', $id)->delete();
                    $data = [
                        "status" => true,
                        "message" => "Data Berhasil dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Dihapus';
                    $data = [
                        "status" => false,
                        "message" => $message,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status" => false,
                    "message" => "Data Gagal Dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status" => false,
                "message" => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function createView()
    {
        $data = [
            'title' => "Tambah Karyawan",
            'dataProvinces' => $this->ProvincesModel->search_list(array(), 'province_name'),
            'tipeEmployee' => $this->GolonganModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
            'jabatan' => $this->JabatanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll(),
            'divisi' => $this->DivisionModel->getDivisiAccess(),
            'dataPernikahan' => $this->MetaDataModel->getByName("status pernikahan"),
            'dataAgama' => $this->MetaDataModel->getByName("religion"),
            'dataPendidikan' => $this->MetaDataModel->getByName("pendidikan")

        ];
        return view('Master/employee/form', $data);
    }

    public function updateView($id)
    {
        $id = decrypt($id);
        $data = $this->EmployeesModel->find($id);

        if ($data == null) {
            return redirect()->to('employee');
        }

        $data = [
            'title' => "Update Karyawan",
            'dataProvinces' => $this->ProvincesModel->search_list(array(), 'province_name'),
            'tipeEmployee' => $this->GolonganModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
            'jabatan' => $this->JabatanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll(),
            'divisi' => $this->DivisionModel->getDivisiAccess(),
            'dataPernikahan' => $this->MetaDataModel->getByName("status pernikahan"),
            'dataAgama' => $this->MetaDataModel->getByName("religion"),
            'dataPendidikan' => $this->MetaDataModel->getByName("pendidikan"),
            'bagianList' => $this->BagianModel->where('division_id', $data['division_id'])->findAll(),
            'cityList' => $this->CitiesModel->get_by_province_id($data['province_id']),
            'payrollList' => $this->PayrollModel->where('employee_id', $data['id'])->where('deletedAt', null)->findAll(),
            'data' => $data,

        ];
        return view('Master/employee/form', $data);
    }

    public function getKomponenGaji()
    {
        // variable declare
        $divisionID = decrypt($this->request->getVar('divisi_id'));
        $employeeID = decrypt($this->request->getVar('employee_id'));

        return response()->setJSON([
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

    public function dropdownEmployeeNotSyncByFinger()
    {
        $attendanceUnitId = $this->request->getVar('attendance_unit_id');
        $employeeUnitAll = $this->EmployeesUnitsModel->where('attendances_unit_id', $attendanceUnitId)->where('deletedAt', null)->findAll();

        // Get Employee Id Yang Sudah Terdaftar
        $employeeIdArr = array();
        foreach ($employeeUnitAll as $e) {
            array_push($employeeIdArr, $e['employee_id']);
        }

        // Get All Excepct
        $dataEmployee = $this->EmployeesModel->where('company_id', $this->this_company_id);
        if (count($employeeIdArr) != 0) {
            $dataEmployee->whereNotIn('id', $employeeIdArr);
        }
        $dataEmployeeList = $dataEmployee->orderBy('name', "asc")->findAll();

        $dataResult = array();
        foreach ($dataEmployeeList as $d) {
            array_push($dataResult, [
                'id' => $d['id'],
                'name' => $d['name']
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'data' => $dataResult
        ]);
    }

    public function syncEmployeeFinger()
    {
        try {
            $attendanceUnitId = $this->request->getVar('attendances_unit_id');
            $employeeUnitArr = [];
            $employeeFingerArr = [];
            $employeeData = [];

            $employeeIds = json_decode($this->request->getVar('employee_id'));
            $attendanceUnit = $this->AttendanceUnitModel->where('id', $attendanceUnitId)->where('deletedAt', null)->first();

            if (empty($employeeIds)) {
                return response()->setJSON([
                    'message' => "Tidak ada data karyawan",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            // TEST FINGER
            $timeout = 2;
            $timeout = max(1, min(5, $timeout));
            $isAlive = icmpPing($attendanceUnit['ip'], $timeout);

            if (!$isAlive) {
                return response()->setJSON([
                    'message' => "Mesin finger tidak terhubung",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            foreach ($employeeIds as $e) {
                $employee = $this->EmployeesModel->where('id', $e)->where('deletedAt', null)->first();

                if ($employee != null) {
                    $employeeUnitArr[] = [
                        'employee_id' => $e,
                        'attendances_unit_id' => $attendanceUnitId,
                    ];

                    $employeeFingerArr[] = [
                        'id' => $e,
                        'attendance_sync' => 1
                    ];

                    array_push($employeeData, [
                        'id' => $employee['id'],
                        'name' => $employee['name']
                    ]);
                }
            }

            foreach ($employeeData as $e) {
                $this->attendanceUnit->insert_finger_user(
                    $e['id'],
                    $attendanceUnit['ip'],
                    $attendanceUnit['unit_key'],
                    $e['name']
                );

                $this->EmployeesModel->update($e['id'], [
                    'attendance_sync' => 1,
                ]);
                $this->EmployeesUnitsModel->insert([
                    'employee_id' => $e['id'],
                    'attendances_unit_id' => $attendanceUnitId
                ]);
            }

            // $this->EmployeesModel->updateBatch($employeeFingerArr, 'id');
            // $this->EmployeesUnitsModel->insertBatch($employeeUnitArr);

            return response()->setJSON([
                'message' => "Berhasil sinkronisasi data",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function getListStatusFinger()
    {
        try {
            $employeeId = decrypt($this->request->getVar('employee_id'));
            $attendanceUnit = $this->AttendanceUnitModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();
            $resultData = array();

            foreach ($attendanceUnit as $a) {
                $employeeUnit = $this->EmployeesUnitsModel
                    ->where('employee_id', $employeeId)
                    ->where('attendances_unit_id', $a['id'])
                    ->first();

                array_push($resultData, [
                    'id' => $a['id'],
                    'employee_id' => $employeeId,
                    'name' => $a['name'],
                    'ip' => $a['ip'],
                    'status' => $employeeUnit == null ? false : true,
                ]);
            }

            $employee = $this->EmployeesModel->where('id', $employeeId)->first();

            return response()->setJSON([
                'status' => true,
                'data' => [
                    'finger' => $resultData,
                    'employee' => $employee
                ],
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function deleteFinger()
    {
        try {
            $attendanceUnitId = $this->request->getVar('attendance_unit_id');
            $employeeId = $this->request->getVar('employee_id');
            $attendanceUnit = $this->AttendanceUnitModel->where('id', $attendanceUnitId)->where('deletedAt', null)->first();

            // TODO REMOVE DI FINGER
            // TEST FINGER
            $timeout = 2;
            $timeout = max(1, min(5, $timeout));
            $isAlive = icmpPing($attendanceUnit['ip'], $timeout);

            if (!$isAlive) {
                return response()->setJSON([
                    'message' => "Mesin finger tidak terhubung",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            $this->attendanceUnit->delete_finger_user(
                $employeeId,
                $attendanceUnit['ip'],
                $attendanceUnit['unit_key'],
            );

            $this->EmployeesUnitsModel->where('employee_id', $employeeId)->where('attendances_unit_id', $attendanceUnitId)->delete(null, true);
            $employeeUnit = $this->EmployeesUnitsModel
                ->where('employee_id', $employeeId)
                ->findAll();

            if (count($employeeUnit) == 0) {
                // UPDATE ATTENDANCE SYNC
                $this->EmployeesModel->update($employeeId, ['attendance_sync' => 0]);
            }


            return response()->setJSON([
                'status' => true,
                'message' => "Finger & Karyawan berhasil dihapus",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function allRiwayatPayroll()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            'payrolls.deletedAt' => null,
            "payrolls.employee_id" => decrypt($this->request->getVar('employee_id')),
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $year = $this->request->getGet('year');

        $payrollData = $this->PayrollModel->getListRiwayatPayroll(
            $condition,
            $addCondition,
            $year,
            $limit,
            $offset
        );
        $dataPayRolls = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($payrollData['data'] as $p) {
            array_push($dataPayRolls, [
                "no" => $no++,
                "id" => encrypt($p->id),
                "year_month" => $p->year_month,
                "nominal_gaji_diterima" => (float) $p->nominal_gaji_diterima,
            ]);
        }

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $payrollData['totalData'],
            "recordsFiltered" => $payrollData['totalFilteredData'],
            "data" => $dataPayRolls,
            "payload" => $payload,
            'test' => $addCondition
        ];

        return response()->setJSON($data);
    }

    public function syncInternalEmployee()
    {
        try {
            // ===============================
            // Cek koneksi internal
            // ===============================
            try {
                $internalDb = \Config\Database::connect('internal');
                $internalDb->initialize();
                if (!$internalDb->connID) {
                    throw new \Exception('Koneksi ke database INTERNAL gagal sync employee.');
                }
            } catch (\Throwable $e) {
                return response()->setJSON([
                    'message' => 'Gagal koneksi ke database INTERNAL sync employee: ' . $e->getMessage(),
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }

            $externalDb = \Config\Database::connect();

            // ===============================
            // 1️⃣ Ambil id terakhir dari EXTERNAL
            // ===============================
            $lastRow = $externalDb->table('employees')
                ->selectMax('id', 'max_id')
                ->get()
                ->getRowArray();

            $lastIdEmployee = (int) ($lastRow['max_id'] ?? 0);

            // ===============================
            // 2️⃣ Ambil data baru dari INTERNAL
            // ===============================
            $internalData = $internalDb->query(
                "SELECT * FROM employees WHERE id > ? ORDER BY id ASC",
                [$lastIdEmployee]
            )->getResultArray();

            if (empty($internalData)) {
                return response()->setJSON([
                    'message' => 'Tidak ada data karyawan internal yang baru',
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }

            // ===============================
            // 3️⃣ Filter yang belum ada di EXTERNAL (company_id + nip)
            // ===============================
            $existingRows = $externalDb->table('employees')
                ->select('company_id, nip')
                ->get()
                ->getResultArray();

            $existing = [];
            foreach ($existingRows as $r) {
                $existing[$r['company_id'] . '_' . $r['nip']] = true;
            }

            $newEmployees = [];
            $newEmployeeIds = [];

            foreach ($internalData as $row) {
                $key = $row['company_id'] . '_' . $row['nip'];
                if (isset($existing[$key])) {
                    continue;
                }
                $existing[$key] = true; // cegah duplikat dalam 1 batch
                $newEmployees[] = $row;
                $newEmployeeIds[] = $row['id'];
            }

            if (empty($newEmployees)) {
                return response()->setJSON([
                    'message' => 'Tidak ada data karyawan internal yang baru',
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }

            // ===============================
            // 4️⃣ Ambil SEMUA gaji_conjunction SEKALI (bukan N+1)
            // ===============================
            $gajiGrouped = [];
            if (!empty($newEmployeeIds)) {
                $gajiRows = $internalDb->table('gaji_conjunction')
                    ->whereIn('employee_id', $newEmployeeIds)
                    ->get()
                    ->getResultArray();

                foreach ($gajiRows as $g) {
                    $gajiGrouped[$g['employee_id']][] = $g;
                }
            }

            // Cek existing gaji di external untuk hindari duplikat
            $existingGaji = [];
            if (!empty($newEmployeeIds)) {
                $existingGajiRows = $externalDb->table('gaji_conjunction')
                    ->select('employee_id, tunjangan_id')
                    ->whereIn('employee_id', $newEmployeeIds)
                    ->get()
                    ->getResultArray();

                foreach ($existingGajiRows as $eg) {
                    $existingGaji[$eg['employee_id'] . '_' . $eg['tunjangan_id']] = true;
                }
            }

            // ===============================
            // 5️⃣ Susun data insert (employees & gaji_conjunction)
            // ===============================
            $insertData = [];
            $gajiInsertData = [];

            foreach ($newEmployees as $row) {
                $insertData[] = [
                    'id' => $row['id'],
                    'company_id' => $row['company_id'],
                    'division_id' => $row['division_id'],
                    'jabatan_id' => $row['jabatan_id'],
                    'bagian_id' => $row['bagian_id'],
                    'join_date' => $row['join_date'],
                    'nik' => $row['nik'],
                    'nip' => $row['nip'],
                    'pin' => $row['pin'],
                    'name' => $row['name'],
                    'gender' => $row['gender'],
                    'dob' => $row['dob'],
                    'phone_no' => $row['phone_no'],
                    'acc_no' => $row['acc_no'],
                    'bank_name' => $row['bank_name'],
                    'owner_name' => $row['owner_name'],
                    'email' => $row['email'],
                    'address' => $row['address'],
                    'province_id' => $row['province_id'],
                    'city_id' => $row['city_id'],
                    'postal_code' => $row['postal_code'],
                    'religion_id' => $row['religion_id'],
                    'marriage_id' => $row['marriage_id'],
                    'child' => $row['child'],
                    'employee_img' => $row['employee_img'],
                    'status' => $row['status'],
                    'pendidikan' => $row['pendidikan'],
                    'attendance_sync' => $row['attendance_sync'],
                    'tipe' => $row['tipe'],
                    'createdAt' => $row['createdAt'],
                    'updatedAt' => $row['updatedAt'],
                    'deletedAt' => $row['deletedAt'],
                    'sync_status' => $row['sync_status'],
                ];

                if (!empty($gajiGrouped[$row['id']])) {
                    foreach ($gajiGrouped[$row['id']] as $g) {
                        $gKey = $g['employee_id'] . '_' . $g['tunjangan_id'];
                        if (isset($existingGaji[$gKey])) {
                            continue;
                        }
                        $existingGaji[$gKey] = true;

                        $gajiInsertData[] = [
                            'employee_id' => $g['employee_id'],
                            'tunjangan_id' => $g['tunjangan_id'],
                            'nominal' => $g['nominal'],
                        ];
                    }
                }
            }

            // var_dump($insertData, $gajiInsertData);
            // die;

            // ===============================
            // 6️⃣ Transaksi + BULK INSERT (employees dulu, baru gaji)
            // ===============================
            $externalDb->transStart();

            if (!empty($insertData)) {
                $externalDb->table('employees')->insertBatch($insertData);
            }

            if (!empty($gajiInsertData)) {
                $externalDb->table('gaji_conjunction')->insertBatch($gajiInsertData);
            }

            $externalDb->transComplete();

            if ($externalDb->transStatus() === false) {
                return response()->setJSON([
                    'message' => 'Sinkronisasi gagal, transaksi di-rollback.',
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }

            return response()->setJSON([
                'message' => 'Sinkronisasi berhasil. '
                    . count($insertData) . ' karyawan, '
                    . count($gajiInsertData) . ' data gaji ditambahkan.',
                'token' => csrf_hash(),
                'status' => true
            ]);

        } catch (\Throwable $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }
}
