<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Warehouse extends BaseController
{
    protected $token;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
    }

    public function warehouse()
    {
        //Get Warehouses
        $responseWarehouses = curl_request("GET", "/warehouses", $this->token);

        $dataWarehouses = [];
        if ($responseWarehouses["code"] === 200) {
            $dataWarehouses = json_decode($responseWarehouses["body"])->data;
        }

        //Get Provinces
        $responseProvinces = curl_request("GET", "/provinces/all", $this->token);

        $dataProvinces = [];
        if ($responseProvinces["code"] === 200) {
            $dataProvinces = json_decode($responseProvinces["body"])->data;
        }

        //Get Pic
        $responsePic = curl_request("GET", "/employees/selectOption", $this->token);

        $dataPic = [];
        if ($responsePic["code"] === 200) {
            $dataPic = json_decode($responsePic["body"])->data;
        }

        $data = [
            "dataPic" => $dataPic,
            "dataWarehouses" => $dataWarehouses,
            "dataProvinces" => $dataProvinces
        ];

        return view('Master/warehouse/index', $data);
    }

    public function allWarehouse()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $response = curl_request("GET", "/warehouses", $this->token, $payload);
        $dataWarehouse = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataWarehouse, [
                    "id" => $data->id,
                    "warehouse_name" => $data->warehouse_name,
                    "address" => $data->address,
                    "province_id" => $data->province_id,
                    "city_id" => $data->city_id,
                    "zip_code" => $data->zip_code,
                    "phone" => $data->phone,
                    "email" => $data->email,
                    "province_name" => $data->province_name,
                    "city_name" => $data->city_name,
                    "pic_name" => $data->pic_name
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataWarehouse,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveWarehouse()
    {
        try{
        $rules = [
            "warehouse_name" => [
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
            "pic_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = json_encode([
                "warehouse_name" => $this->request->getPost("warehouse_name"),
                "address" => $this->request->getPost("address"),
                "province_id" => $this->request->getPost("province_id"),
                "city_id" => $this->request->getPost("city_id"),
                "zip_code" => $this->request->getPost("zip_code"),
                "phone" => $this->request->getPost("phone"),
                "email" => $this->request->getPost("email"),
                "pic_id" => $this->request->getPost("pic_id"),
            ]);

            $response = curl_request("POST", "/warehouses", $this->token, $payload);

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

    public function updateWarehouse()
    {
        try{
        $rules = [
            "warehouse_name" => [
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
            "pic_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "warehouse_name" => $this->request->getPost("warehouse_name"),
                "address" => $this->request->getPost("address"),
                "province_id" => $this->request->getPost("province_id"),
                "city_id" => $this->request->getPost("city_id"),
                "zip_code" => $this->request->getPost("zip_code"),
                "phone" => $this->request->getPost("phone"),
                "email" => $this->request->getPost("email"),
                "pic_id" => $this->request->getPost("pic_id"),
            ]);
        }

        if ($payload) {
            $response = curl_request("PATCH", "/warehouses/$id", $this->token, $payload);

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

    public function getByIdWarehouse($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/warehouses/$id", $this->token);
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

    public function deleteWarehouse()
    {
        try{
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/warehouses/$id", $this->token);
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

    public function dropdownWarehouse()
    {
        $dataWarehouse = [];
        $responseWarehouse = curl_request("GET", "/warehouses/all", $this->token);
        if ($responseWarehouse["code"] === 200) {
            $dataWarehouse = json_decode($responseWarehouse["body"])->data;
        }

        $data = [
            "data" => $dataWarehouse
        ];

        echo json_encode($data);
        return;
    }
}
