<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ProvincesModel;
use App\Models\EmployeesModel;
use App\Models\UserModel;

class Employee extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $ProvincesModel;
    protected $EmployeesModel;
    protected $UserModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->ProvincesModel = new ProvincesModel();
        $this->EmployeesModel = new EmployeesModel();
        $this->UserModel = new UserModel();
    }

    public function employee()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $data = [
            "dataProvinces" => $dataProvinces,
        ];

        return view('Master/employee/index', $data);
    }

    public function dropdownEmployee()
    {
        $dataEmployee = [];
        $dataEmployee = $this->EmployeesModel->getEmployees($this->this_company_id);

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
        $columnIndex = $temp[0]['column']; // Column index

        $temp = $this->request->getVar('columns');
        $columnName = $temp[$columnIndex]['data']; // Column index

        $temp = $this->request->getVar('order');
        $columnSortOrder = $temp[0]['dir']; // Column index

        $search = $this->request->getVar('search');
        //$searchValue = $temp['value']; // Column index

        $values = [
            "company_id"    => $this->this_company_id,
            "search"        => $search
        ];

        $totalRecords = $this->EmployeesModel->total_list(array());
        $totalRecordwithFilter = $this->EmployeesModel->total_list($values);

        $res = $this->EmployeesModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "nip" => $res[$i]["nip"],
                "name" => $res[$i]["name"],
                "divisionName" => $res[$i]["divisionName"],
                "email" => $res[$i]["email"],
                "phone_no" => $res[$i]["phone_no"],
                "dob" => date("d/m/Y", strtotime($res[$i]["dob"])),
                "gender" => $res[$i]["gender"],
                "acc_no" => $res[$i]["acc_no"],
                "status" => $res[$i]["status"],
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
            $rules = [
                "nip" => [
                    "rules" => "required"
                ],
                "name" => [
                    "rules" => "required"
                ],
                "gender" => [
                    "rules" => "required"
                ],
                "dob" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "required"
                ],
                "division_id" => [
                    "rules" => "required"
                ],
                "acc_no" => [
                    "rules" => "required"
                ],
                "nik" => [
                    "rules" => "required"
                ],
                "child" => [
                    "rules" => "required"
                ],
                "religion_id" => [
                    "rules" => "required"
                ],
                "marriage_id" => [
                    "rules" => "required"
                ],
                "child" => [
                    "rules" => "required"
                ],
                "province_id" => [
                    "rules" => "required"
                ],
                "city_id" => [
                    "rules" => "required"
                ],
                "join_date" => [
                    "rules" => "required"
                ],
                "bank_name" => [
                    "rules" => "required"
                ],
                "owner_name" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = '';

                $file = $this->request->getFile("employeeImg");
                $values = [
                    "company_id" => $this->this_company_id,
                    "nip" => $this->request->getPost("nip"),
                    "name" => $this->request->getPost("name"),
                    "gender" => $this->request->getPost("gender"),
                    "join_date" => $this->request->getPost("join_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("join_date")))) : "",
                    "dob" => $this->request->getPost("dob") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("dob")))) : "",
                    "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
                    "phone_no" => $this->request->getPost("phone_no"),
                    "acc_no" => $this->request->getPost("acc_no"),
                    "email" => $this->request->getPost("email"),
                    "address" => $this->request->getPost("address"),
                    "status" => $this->request->getPost("status"),
                    "nik" => $this->request->getPost("nik"),
                    "child" => $this->request->getPost("child"),
                    "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                    "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT"),
                    "postal_code" => $this->request->getPost("zip_code"),
                    "religion_id" => formatter($this->request->getPost("religion_id"), "STR_TO_INT"),
                    "marriage_id" => formatter($this->request->getPost("marriage_id"), "STR_TO_INT"),
                    "jabatan" => $this->request->getPost("jabatan"),
                    "bank_name" => $this->request->getPost("bank_name"),
                    "owner_name" => $this->request->getPost("owner_name"),
                    "pin"  => $this->request->getPost("pin")
                ];
                if (!empty($file->getName())) {
                    $mime = $file->getMimeType();
                    if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                        $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));
                        $values["employee_img"] = $image;
                    }
                }

                if (isset($values)) {
                    if ($this->EmployeesModel->insert($values)) {
                        $data = [
                            "status"            => true,
                            "message"   => "Data Berhasil disimpan",
                            "payload"   => "",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    } else {
                        $message = 'Data Gagal Disimpan';
                        $data = [
                            "status"            => false,
                            "message"    => $message,
                            "payload"   => "",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    }
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Format gambar harus bertipe png, jpg, jpeg",
                        "payload"   => '',
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Disimpan",
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

    public function updateEmployee()
    {
        try {
            $rules = [
                "nip" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'NIP Tidak Boleh Kosong',
                    ]
                ],
                "name" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama Karyawan Tidak Boleh Kosong',
                    ]
                ],
                "gender" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Jenis Kelamin Tidak Boleh Kosong',
                    ]
                ],
                "dob" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal Lahir Tidak Boleh Kosong',
                    ]
                ],
                "address" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Alamat Tidak Boleh Kosong',
                    ]
                ],
                "division_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Divisi Tidak Boleh Kosong',
                    ]
                ],
                "acc_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No Rekening Tidak Boleh Kosong',
                    ]
                ],
                "nik" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'NIK Tidak Boleh Kosong',
                    ]
                ],
                "religion_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Agama Tidak Boleh Kosong',
                    ]
                ],
                "marriage_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Status Kawin Tidak Boleh Kosong',
                    ]
                ],
                "child" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Jumlah Anak Tidak Boleh Kosong',
                    ]
                ],
                "province_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Provinsi Tidak Boleh Kosong',
                    ]
                ],
                "city_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Kota Tidak Boleh Kosong',
                    ]
                ],
                "join_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal Bergabung Tidak Boleh Kosong',
                    ]
                ],
                "bank_name" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama Bank Tidak Boleh Kosong',
                    ]
                ],
                "owner_name" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Owner Name Tidak Boleh Kosong',
                    ]
                ]
            ];

            if ($this->validate($rules)) {
                $payload = '';

                $id = $this->request->getPost("id");
                $status = $this->request->getPost("status") === "Aktif" ? 'Aktif' : 'Non Aktif';
                $name = $this->request->getPost("name");

                // update status user when employee status changed
                $user = $this->UserModel->getByEmployeeId($id);

                if($user)
                {
                    $this->UserModel->where(['id' => $user->id])->set(['status' => $status, 'name' => $name])->update();
                }

                $values = [
                    "company_id" => $this->this_company_id,
                    "nip" => $this->request->getPost("nip"),
                    "name" => $this->request->getPost("name"),
                    "gender" => $this->request->getPost("gender"),
                    "join_date" => $this->request->getPost("join_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("join_date")))) : "",
                    "dob" => $this->request->getPost("dob") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("dob")))) : "",
                    "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
                    "phone_no" => $this->request->getPost("phone_no"),
                    "email" => $this->request->getPost("email"),
                    "address" => $this->request->getPost("address"),
                    "status" => $this->request->getPost("status"),
                    "nik" => $this->request->getPost("nik"),
                    "child" => $this->request->getPost("child"),
                    "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                    "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT"),
                    "postal_code" => $this->request->getPost("zip_code"),
                    "religion_id" => formatter($this->request->getPost("religion_id"), "STR_TO_INT"),
                    "marriage_id" => formatter($this->request->getPost("marriage_id"), "STR_TO_INT"),
                    "jabatan" => $this->request->getPost("jabatan"),
                    "acc_no" => $this->request->getPost("acc_no"),
                    "bank_name" => $this->request->getPost("bank_name"),
                    "owner_name" => $this->request->getPost("owner_name"),
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
                        $data = [
                            "status"    => true,
                            "message"   => "Data Berhasil diubah",
                            "payload"   => "",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    } else {
                        $message = 'Data Gagal Diubah';
                        $data = [
                            "status"    => false,
                            "message"   => $message,
                            "payload"   => "",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    }
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Format gambar harus bertipe png, jpg, jpeg",
                        "payload"   => '',
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $errorMsgs = $this->validator->getErrors();
                $data = [
                    "status"     => false,
                    "message"    => $errorMsgs[array_key_first($errorMsgs)],
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

    public function getByIdEmployee($id = null)
    {
        if (!empty($id)) {
            $res = $this->EmployeesModel->get_by_id($id);
            $res[0]["join_date"] = date("d/m/Y", strtotime($res[0]["join_date"]));
            $res[0]["dob"] = date("d/m/Y", strtotime($res[0]["dob"]));
            //            $response = curl_request("GET", "/employees/$id?idCompany=$this->this_company_id", $this->token);
            if (count($res)) {
                $data = [
                    "status"  => true,
                    "data"  => (object) $res[0],
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

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->EmployeesModel->update($id, $values)) {
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
}
