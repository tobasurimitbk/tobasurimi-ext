<?php

namespace App\Controllers;

class Supplier extends BaseController
{

    public function __construct()
    {
    }

    public function supplier()
    {
        $token = session()->get("login")->token;

        //Get Provinces
        $responseProvinces = curl_request("GET", "/provinces/all", $token);

        $dataProvinces = [];
        if ($responseProvinces["code"] === 200) {
            $dataProvinces = json_decode($responseProvinces["body"])->data;
        }


        $data = [
            "dataProvinces" => $dataProvinces,
        ];

        return view('supplier/index', $data);
    }

    public function allSupplier()
    {
        $token = session()->get("login")->token;


        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search")
        ];

        $response = curl_request("GET", "/suppliers", $token, $payload);
        $dataSupplier = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataSupplier, [
                    "id" => $data->id,
                    "code" => $data->code,
                    "name" => $data->name,
                    "address" => $data->address,
                    "province_id" => $data->province_id,
                    "city_id" => $data->city_id,
                    "zip_code" => $data->zip_code,
                    "phone" => $data->phone,
                    "email" => $data->email,
                    "province_name" => $data->province_name,
                    "city_name" => $data->city_name,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataSupplier,
        ];

        echo json_encode($data);
        return;
    }

    public function saveSupplier()
    {
        $rules = [
            "code" => [
                "rules" => "required"
            ],
            "name" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "province_id" => [
                "rules" => "required"
            ],
            "city_id" => [
                "rules" => "required"
            ],
            "zip_code" => [
                "rules" => "required"
            ],
            "phone" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],

        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;


            $payload = json_encode([
                "code" => $this->request->getPost("code"),
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "province_id" => $this->request->getPost("province_id"),
                "city_id" => $this->request->getPost("city_id"),
                "zip_code" => $this->request->getPost("zip_code"),
                "phone" => $this->request->getPost("phone"),
                "email" => $this->request->getPost("email"),
            ]);

            $response = curl_request("POST", "/suppliers", $token, $payload);

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
                "message"    => "Data Gagal Disimpan",
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateSupplier()
    {
        $rules = [
            "code" => [
                "rules" => "required"
            ],
            "name" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "province_id" => [
                "rules" => "required"
            ],
            "city_id" => [
                "rules" => "required"
            ],
            "zip_code" => [
                "rules" => "required"
            ],
            "phone" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;

            $id = $this->request->getPost("id");

            $payload = json_encode([
                "code" => $this->request->getPost("code"),
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "province_id" => $this->request->getPost("province_id"),
                "city_id" => $this->request->getPost("city_id"),
                "zip_code" => $this->request->getPost("zip_code"),
                "phone" => $this->request->getPost("phone"),
                "email" => $this->request->getPost("email"),
            ]);
        }

        if ($payload) {
            $response = curl_request("PATCH", "/suppliers/$id", $token, $payload);

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

        return;
    }

    public function getByIdSupplier($id = null)
    {
        $token = session()->get("login")->token;

        if (!empty($id)) {
            $response = curl_request("GET", "/suppliers/$id", $token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditampilkan';
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

    public function deleteSupplier()
    {
        $token = session()->get("login")->token;

        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/suppliers/$id", $token);
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
