<?php

namespace App\Controllers;

class Company extends BaseController
{

    public function __construct()
    {

    }

    public function company()
    {
        $token = session()->get("login")->token;

        //Get Provinces
        $responseProvinces = curl_request("GET", "/provinces/all", $token);

        $dataProvinces = [];
        if ($responseProvinces["code"] === 200) {
            $dataProvinces = json_decode($responseProvinces["body"])->data;
        }

         //Get Employees
         $responseEmployees = curl_request("GET", "/employees/selectOption", $token);

         $dataEmployees = [];
         if ($responseEmployees["code"] === 200) {
             $dataEmployees = json_decode($responseEmployees["body"])->data;
         }

        $data = [
            "dataProvinces" => $dataProvinces,
            "dataEmployees" => $dataEmployees
        ];

        return view('company/index', $data);
    }

    public function allCompany()
    {
        $token = session()->get("login")->token;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $response = curl_request("GET", "/companies", $token, $payload);
        $dataCompany = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataCompany, [
                    "id" => $data->id,
                    "company" => $data->company,
                    "holding_company" => $data->holding_company,
                    "address" => $data->address,
                    "phone" => $data->phone,
                    "email" => $data->email
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataCompany
        ];

        echo json_encode($data);
        return;
    }

    public function saveCompany()
    {
        $rules = [
            "holding_company" => [
                "rules" => "required"
            ],
            "company" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "phone" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
            "zip_code" => [
                "rules" => "required"
            ],
            "province_id" => [
                "rules" => "required"
            ],
            "city_id" => [
                "rules" => "required"
            ],
            "pic_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;

            $file = $this->request->getFile("logo");

            $logo = "";

            if (!empty($file->getName())) 
            {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $logo = "data:$mime;base64, " . base64_encode(file_get_contents($file));

                    $payload = json_encode([
                        "logo" => $logo,
                        "company" => $this->request->getPost("company"),
                        "holding_company" => $this->request->getPost("holding_company"),
                        "address" => $this->request->getPost("address"),
                        "phone" => $this->request->getPost("phone"),
                        "email" => $this->request->getPost("email"),
                        "pic_id" => formatter($this->request->getPost("pic_id"), "STR_TO_INT"),
                        "zip_code" => $this->request->getPost("zip_code"),
                        "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                        "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT")
                    ]);
                }
            }

            if($payload)
            {
                $response = curl_request("POST", "/companies", $token, $payload);
    
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

    public function updateCompany()
    {
        $rules = [
            "holding_company" => [
                "rules" => "required"
            ],
            "company" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "phone" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
            "zip_code" => [
                "rules" => "required"
            ],
            "province_id" => [
                "rules" => "required"
            ],
            "city_id" => [
                "rules" => "required"
            ],
            "pic_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;

            $id = $this->request->getPost("id");

            $file = $this->request->getFile("logo");
            if (!empty($file->getName())) 
            {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $logo = "data:$mime;base64, " . base64_encode(file_get_contents($file));

                    $payload = json_encode([
                        "logo" => $logo,
                        "company" => $this->request->getPost("company"),
                        "holding_company" => $this->request->getPost("holding_company"),
                        "address" => $this->request->getPost("address"),
                        "phone" => $this->request->getPost("phone"),
                        "email" => $this->request->getPost("email"),
                        "pic_id" => formatter($this->request->getPost("pic_id"), "STR_TO_INT"),
                        "zip_code" => $this->request->getPost("zip_code"),
                        "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                        "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT")
                    ]);
                }
            }
            else
            {
                $payload = json_encode([
                    "company" => $this->request->getPost("company"),
                    "holding_company" => $this->request->getPost("holding_company"),
                    "address" => $this->request->getPost("address"),
                    "phone" => $this->request->getPost("phone"),
                    "email" => $this->request->getPost("email"),
                    "pic_id" => formatter($this->request->getPost("pic_id"), "STR_TO_INT"),
                    "zip_code" => $this->request->getPost("zip_code"),
                    "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                    "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT")
                ]);
            }

            if($payload)
            {
                $response = curl_request("PATCH", "/companies/$id", $token, $payload);

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

    public function getByIdCompany($id = null)
    {
        $token = session()->get("login")->token;

        if (!empty($id)) {
            $response = curl_request("GET", "/companies/$id", $token);
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

    public function deleteCompany()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/companies/$id", $token);
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