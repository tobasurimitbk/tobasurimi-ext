<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Employee extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function employee()
    {
        //Get Provinces
        $responseProvinces = curl_request("GET", "/provinces/all", $this->token);

        $dataProvinces = [];
        if ($responseProvinces["code"] === 200) {
            $dataProvinces = json_decode($responseProvinces["body"])->data;
        }

        $data = [
            "dataProvinces" => $dataProvinces,
        ];

        return view('Master/employee/index', $data);
    }

    public function dropdownEmployee()
    {
        $dataEmployee = [];
        $responseEmployee = curl_request("GET", "/employees/selectOption?idCompany=$this->this_company_id", $this->token);
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

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
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this->this_company_id
        ];

        $response = curl_request("GET", "/employees", $this->token, $payload);
        $dataRole = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataRole, [
                    "id" => $data->id,
                    "nip" => $data->nip,
                    "name" => $data->name,
                    "divisionName" => $data->divisionName,
                    "email" => $data->email,
                    "phone_no" => $data->phone_no,
                    "dob" => $data->dob,
                    "gender" => $data->gender,
                    "acc_no" => $data->acc_no,
                    "status" => $data->status,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataRole,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveEmployee()
    {
        try{
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
            "phone_no" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
            "acc_no" => [
                "rules" => "required"
            ],
            "pin" => [
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

            if (!empty($file->getName())) {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));

                    $payload = json_encode([
                        "company_id" => $this->this_company_id,
                        "employeeImg" => $image,
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
                        "pin" => $this->request->getPost("pin"),
                        "nik" => $this->request->getPost("nik"),
                        "child" => $this->request->getPost("child"),
                        "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                        "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT"),
                        "religion_id" => formatter($this->request->getPost("religion_id"), "STR_TO_INT"),
                        "marriage_id" => formatter($this->request->getPost("marriage_id"), "STR_TO_INT"),
                        "jabatan" => $this->request->getPost("jabatan"),
                        "bank_name" => $this->request->getPost("bank_name"),
                        "owner_name" => $this->request->getPost("owner_name"),
                    ]);
                }
            }

            if ($payload) {
                $response = curl_request("POST", "/employees", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
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
        }
        catch(\Exception $e)
        {
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
        try{
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
            "phone_no" => [
                "rules" => "required"
            ],
            "email" => [
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

            $id = $this->request->getPost("id");

            $file = $this->request->getFile("employeeImg");
            if (!empty($file->getName())) {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));

                    $payload = json_encode([
                        "company_id" => $this->this_company_id,
                        "employeeImg" => $image,
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
                        "religion_id" => formatter($this->request->getPost("religion_id"), "STR_TO_INT"),
                        "marriage_id" => formatter($this->request->getPost("marriage_id"), "STR_TO_INT"),
                        "jabatan" => $this->request->getPost("jabatan"),
                        "bank_name" => $this->request->getPost("bank_name"),
                        "owner_name" => $this->request->getPost("owner_name"),
                    ]);
                }
            } else {
                $payload = json_encode([
                    "company_id" => $this->this_company_id,
                    "nip" => $this->request->getPost("nip"),
                    "name" => $this->request->getPost("name"),
                    "gender" => $this->request->getPost("gender"),
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
                    "religion_id" => formatter($this->request->getPost("religion_id"), "STR_TO_INT"),
                    "marriage_id" => formatter($this->request->getPost("marriage_id"), "STR_TO_INT"),
                    "jabatan" => $this->request->getPost("jabatan"),
                    "bank_name" => $this->request->getPost("bank_name"),
                    "owner_name" => $this->request->getPost("owner_name"),
                ]);
            }

            if ($payload) {
                $response = curl_request("PATCH", "/employees/$id", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
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
                "message"    => "Data Gagal Diubah",
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        }
        catch(\Exception $e)
        {
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
            $response = curl_request("GET", "/employees/$id?idCompany=$this->this_company_id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditemukan';
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
        try{
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/employees/$id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Dihapus';
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
        }
        catch(\Exception $e)
        {
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
