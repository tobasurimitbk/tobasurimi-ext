<?php

namespace App\Controllers;

class SPP extends BaseController
{

    public function __construct()
    {
    }

    public function spp()
    {
        return view('spp/index');
    }

    public function createSPP()
    {
        $token = session()->get("login")->token;

        //Get Order Type By Metadata
        $responseOrderType = curl_request("GET", "/metadata/all?name=tipe_po", $token);

        $dataOrderType = [];
        if ($responseOrderType["code"] === 200) {
            $dataOrderType = json_decode($responseOrderType["body"])->data;
        }

        //Get Warehouse
        $responseWarehouse = curl_request("GET", "/warehouses/all", $token);

        $dataWarehouse = [];
        if ($responseWarehouse["code"] === 200) {
            $dataWarehouse = json_decode($responseWarehouse["body"])->data;
        }
        
        $data = [
            "dataOrderType" => $dataOrderType,
            "dataWarehouse" => $dataWarehouse
        ];

        return view('spp/form', $data);
    }

    public function getByIdSPP($id = null)
    {
        $token = session()->get("login")->token;

        //Get Order Type By Metadata
        $responseOrderType = curl_request("GET", "/metadata/all?name=tipe_po", $token);

        $dataOrderType = [];
        if ($responseOrderType["code"] === 200) {
            $dataOrderType = json_decode($responseOrderType["body"])->data;
        }

        //Get Warehouse
        $responseWarehouse = curl_request("GET", "/warehouses/all", $token);

        $dataWarehouse = [];
        if ($responseWarehouse["code"] === 200) {
            $dataWarehouse = json_decode($responseWarehouse["body"])->data;
        }
        
        $data = [
            "dataOrderType" => $dataOrderType,
            "dataWarehouse" => $dataWarehouse
        ];

        if (!empty($id)) {
            $responseSPP = curl_request("GET", "/purchaseRequest/$id", $token);
            $dataSPP = [];
            if ($responseSPP["code"] === 200) {
                $dataSPP = json_decode($responseSPP["body"])->data;
            }
            $data["dataSPP"] = $dataSPP;
        }

        return view('spp/form', $data);
        
        return;
    }

    public function getByIdSPPAjax()
    {
        $token = session()->get("login")->token;

        $id = $this->request->getGet("id");

        if (!empty($id)) {
            $response = curl_request("GET", "/purchaseRequest/$id", $token);
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

    public function allSPP()
    {
        $token = session()->get("login")->token;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            // "requestStatus" => $this->request->getGet("status"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $response = curl_request("GET", "/purchaseRequest", $token, $payload);
        $dataSPP = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataSPP, [
                    "no" => $no++,
                    "id" => $data->id,
                    "spp_type" => $data->spp_type,
                    "spp_no" => $data->spp_no,
                    "warehouseName" => $data->warehouseName,
                    "orderTypeName" => $data->orderTypeName,
                    "total" => $data->total,
                    "request_date" => $data->request_date,
                    "approvedByHeadwarehouseName" => $data->approvedByHeadwarehouseName,
                    "approvedByHeadofPurchasingName" => $data->approvedByHeadofPurchasingName,
                    "approvedByDirectorName" => $data->approvedByDirectorName,
                    "is_posted" => $data->is_posted
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataSPP,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSPP()
    {
        $rules = [
            "request_date" => [
                "rules" => "required"
            ],
            "spp_type" => [
                "rules" => "required"
            ],
            "spp_no" => [
                "rules" => "required"
            ],
            "order_type" => [
                "rules" => "required"
            ],
            "warehouse_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $payload = json_encode([
                "request_date" => $this->request->getPost("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("request_date")))) : "",
                "order_type" => formatter($this->request->getPost("order_type"), "STR_TO_INT"),
                "spp_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("spp_no"),
                "spp_type" => $this->request->getPost("spp_type"),
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                "note" => $this->request->getPost("note"),
                "is_posted" => false,
                "items" => json_decode(stripslashes($this->request->getPost("items")))
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);
            
            $response = curl_request("POST", "/purchaseRequest", $token, $payload);

            if ($response["code"] === 201) {
                $data = [
                    "id" => json_decode($response["body"])->createdId,
                    "status"            => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
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

    public function updateSPP()
    {
        $rules = [
            "request_date" => [
                "rules" => "required"
            ],
            "spp_type" => [
                "rules" => "required"
            ],
            "spp_no" => [
                "rules" => "required"
            ],
            "order_type" => [
                "rules" => "required"
            ],
            "warehouse_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $id = $this->request->getPost("id");

            $payload = json_encode([
                "request_date" => $this->request->getPost("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("request_date")))) : "",
                "order_type" => formatter($this->request->getPost("order_type"), "STR_TO_INT"),
                "spp_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("spp_no"),
                "spp_type" => $this->request->getPost("spp_type"),
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                "note" => $this->request->getPost("note"),
                "items" => json_decode(stripslashes($this->request->getPost("items")))
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);

            $response = curl_request("PATCH", "/purchaseRequest/$id", $token, $payload);

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
                "message"    => "Data Gagal Diubah",
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateStatusSPP()
    {
        $token = session()->get("login")->token;

        $id = $this->request->getPost("id");

        $payload = json_encode([
            "is_posted" => true
        ]);
        
        $response = curl_request("PATCH", "/purchaseRequest/$id", $token, $payload);

        if ($response["code"] === 200) {
            $data = [
                "status"            => true,
                "message"   => "Data Berhasil diposting",
                "payload"   => $payload,
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        } else {
            $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diposting';
            $data = [
                "status"            => false,
                "message"    => $message,
                "payload"   => $payload,
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function approveSPP()
    {
        $token = session()->get("login")->token;
        $name = session()->get("login")->name;

        $id = $this->request->getPost("id");

        $payload = json_encode([

        ]);
        
        $response = curl_request("PATCH", "/purchaseRequest/approve/$id", $token, $payload);

        if ($response["code"] === 200) {
            $data = [
                "status"            => true,
                "message"   => "Approve Berhasil diubah",
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
        return;
    }

    public function deleteSPP()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/purchaseRequest/$id", $token);
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