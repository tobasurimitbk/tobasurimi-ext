<?php

namespace App\Controllers;

class Employee extends BaseController
{

    public function __construct()
    {

    }

    public function employee()
    {
        $token = session()->get("login")->token;
         //Get Divisi
        $responseDivisi = curl_request("GET", "/divisis/all", $token);

        $dataDivisi = [];
        if ($responseDivisi["code"] === 200) {
            $dataDivisi = json_decode($responseDivisi["body"])->data;
        }
         
        $data = [
            "dataDivisi" => $dataDivisi,
        ];

        return view('employee/index', $data);
    }

    public function dropdownEmployee()
    {
        $token = session()->get("login")->token;
        $dataEmployee = [];
        $responseEmployee = curl_request("GET", "/employees/selectOption", $token);
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
        $token = session()->get("login")->token;
        $this_company_id = session()->get("login")->this_company_id;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this_company_id
        ];

        $response = curl_request("GET", "/employees", $token, $payload);
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
                    "address" => $data->address,
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
            ]
        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;

            $this_company_id = session()->get("login")->this_company_id;

            $file = $this->request->getFile("employeeImg");

            if (!empty($file->getName())) 
            {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));

                    $payload = json_encode([
                        "company_id" => $this_company_id,
                        "employeeImg" => $image,
                        "nip" => $this->request->getPost("nip"),
                        "name" => $this->request->getPost("name"),
                        "gender" => $this->request->getPost("gender"),
                        "dob" => $this->request->getPost("dob") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("dob")))) : "",
                        "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
                        "phone_no" => $this->request->getPost("phone_no"),
                        "acc_no" => $this->request->getPost("acc_no"),
                        "email" => $this->request->getPost("email"),
                        "address" => $this->request->getPost("address"),
                        "status" => $this->request->getPost("status")
                    ]);
                }
            }

            if($payload)
            {
                $response = curl_request("POST", "/employees", $token, $payload);

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
            }
            else
            {
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
        return;
    }

    public function updateEmployee()
    {
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
            ]
        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;

            $id = $this->request->getPost("id");

            $this_company_id = session()->get("login")->this_company_id;

            $file = $this->request->getFile("employeeImg");
            if (!empty($file->getName())) 
            {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));

                    $payload = json_encode([
                        "company_id" => $this_company_id,
                        "employeeImg" => $image,
                        "nip" => $this->request->getPost("nip"),
                        "name" => $this->request->getPost("name"),
                        "gender" => $this->request->getPost("gender"),
                        "dob" => $this->request->getPost("dob") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("dob")))) : "",
                        "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
                        "phone_no" => $this->request->getPost("phone_no"),
                        "acc_no" => $this->request->getPost("acc_no"),
                        "email" => $this->request->getPost("email"),
                        "address" => $this->request->getPost("address"),
                        "status" => $this->request->getPost("status")
                    ]);
                }
            }
            else
            {
                $payload = json_encode([
                    "company_id" => $this_company_id,
                    "nip" => $this->request->getPost("nip"),
                    "name" => $this->request->getPost("name"),
                    "gender" => $this->request->getPost("gender"),
                    "dob" => $this->request->getPost("dob") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("dob")))) : "",
                    "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
                    "phone_no" => $this->request->getPost("phone_no"),
                    "acc_no" => $this->request->getPost("acc_no"),
                    "email" => $this->request->getPost("email"),
                    "address" => $this->request->getPost("address"),
                    "status" => $this->request->getPost("status")
                ]);
            }

            if($payload)
            {
                $response = curl_request("PATCH", "/employees/$id", $token, $payload);

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
            }
            else
            {
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
        return;
    }

    public function getByIdEmployee($id = null)
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        if (!empty($id)) {
            $response = curl_request("GET", "/employees/$id?idCompany=$this_company_id", $token);
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
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/employees/$id", $token);
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
        return;
    }
}