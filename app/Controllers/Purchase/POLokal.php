<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

class POLokal extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function poLokal()
    {
        return view('Purchase/poLokal/index');
    }

    public function createPOLokal()
    {
         //Get SPP Number
         $responseSPP = curl_request("GET", "/purchaseRequest/getByType/Lokal", $this->token);

        $dataSPP = [];
        if ($responseSPP["code"] === 200) {
            $dataSPP = json_decode($responseSPP["body"])->data;
        }

        //Get Supplier
        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this->this_company_id", $this->token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        //Get Valuta Asing By Metadata
        $responseValuta = curl_request("GET", "/metadata/all?name=valuta_asing", $this->token);

        $dataValuta = [];
        if ($responseValuta["code"] === 200) {
            $dataValuta = json_decode($responseValuta["body"])->data;
        }
        
        $data = [
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        return view('Purchase/poLokal/form', $data);
    }

    public function getByIdPOLokal($id = null)
    {
        //Get SPP Number
        $responseSPP = curl_request("GET", "/purchaseRequest/getByType/Lokal", $this->token);

        $dataSPP = [];
        if ($responseSPP["code"] === 200) {
            $dataSPP = json_decode($responseSPP["body"])->data;
        }

        //Get Supplier
        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this->this_company_id", $this->token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        //Get Valuta Asing By Metadata
        $responseValuta = curl_request("GET", "/metadata/all?name=valuta_asing", $this->token);

        $dataValuta = [];
        if ($responseValuta["code"] === 200) {
            $dataValuta = json_decode($responseValuta["body"])->data;
        }
        
        $data = [
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        if (!empty($id)) {
            $responsePOLokal = curl_request("GET", "/purchaseOrder/$id", $this->token);
            $dataPOLokal = [];
            if ($responsePOLokal["code"] === 200) {
                $dataPOLokal = json_decode($responsePOLokal["body"])->data;
            }
            $data["dataPOLokal"] = $dataPOLokal;

            // var_dump($dataPOLokal);
            // die;
        }

        return view('Purchase/poLokal/form', $data);
        
        return;
    }

    public function getByIdPOLokalAjax()
    {
        $id = $this->request->getGet("id");

        if (!empty($id)) {
            $response = curl_request("GET", "/purchaseOrder/$id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                    "message" => $response
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

    public function allPOLokal()
    {
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

        $response = curl_request("GET", "/purchaseOrder", $this->token, $payload);
        $dataPOLokal = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataPOLokal, [
                    "no" => $no++,
                    "id" => $data->id,
                    "po_date" => $data->po_date,
                    "po_no" => $data->po_no,
                    "orderTypeName" => $data->orderTypeName,
                    "supplierName" => $data->supplierName,
                    "total" => $data->total,
                    "foreignExchangeName" => $data->foreignExchangeName,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataPOLokal,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePOLokal()
    {
        $rules = [
            "purchase_request_id" => [
                "rules" => "required"
            ],
            "po_no" => [
                "rules" => "required"
            ],
            "po_date" => [
                "rules" => "required"
            ],
            "supplier_id" => [
                "rules" => "required"
            ],
            "payment_term" => [
                "rules" => "required"
            ],
            "foreign_exchange" => [
                "rules" => "required"
            ],
            "payment_date" => [
                "rules" => "required"
            ],
            "dpp" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = json_encode([
                "purchase_request_id" => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                "po_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                "order_type" => formatter($this->request->getPost("order_type"), "STR_TO_INT"),
                "po_type" => "lokal",
                "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                "payment_term" => formatter($this->request->getPost("payment_term"), "STR_TO_INT"),
                "foreign_exchange" => formatter($this->request->getPost("foreign_exchange"), "STR_TO_INT"),
                "payment_date" => $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))) : "",
                "dpp" => formatter($this->request->getPost("dpp"), "CURR_TO_INT"),
                "note" => $this->request->getPost("note"),
                "isPosted" => false,
                "items" => json_decode(stripslashes($this->request->getPost("items")))
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);
            
            $response = curl_request("POST", "/purchaseOrder", $this->token, $payload);

            if ($response["code"] === 201) {
                $data = [
                    "id" => "",
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

    public function updatePOLokal()
    {
        $rules = [
            "purchase_request_id" => [
                "rules" => "required"
            ],
            "po_no" => [
                "rules" => "required"
            ],
            "po_date" => [
                "rules" => "required"
            ],
            "supplier_id" => [
                "rules" => "required"
            ],
            "payment_term" => [
                "rules" => "required"
            ],
            "foreign_exchange" => [
                "rules" => "required"
            ],
            "payment_date" => [
                "rules" => "required"
            ],
            "dpp" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "purchase_request_id" => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                "po_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                "order_type" => formatter($this->request->getPost("order_type"), "STR_TO_INT"),
                "po_type" => "lokal",
                "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                "payment_term" => formatter($this->request->getPost("payment_term"), "STR_TO_INT"),
                "foreign_exchange" => formatter($this->request->getPost("foreign_exchange"), "STR_TO_INT"),
                "payment_date" => $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))) : "",
                "dpp" => formatter($this->request->getPost("dpp"), "CURR_TO_INT"),
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

            $response = curl_request("PATCH", "/purchaseOrder/$id", $this->token, $payload);

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

    public function updateStatusPOLokal()
    {
        $id = $this->request->getPost("id");

        $payload = json_encode([
            "is_posted" => true
        ]);
        
        $response = curl_request("PATCH", "/purchaseOrder/$id", $this->token, $payload);

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

    public function deletePOLokal()
    {
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/purchaseOrder/$id", $this->token);
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

    public function dropdownPOLokal()
    {
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");
        $dataPOLokal = [];
        $responsePOLokal = curl_request("GET", "/penerimaanBarang/drop-down-po?potype=LOKAL&supplierid=$id", $this->token);
        if ($responsePOLokal["code"] === 200) {
            $dataPOLokal = json_decode($responsePOLokal["body"])->data;
        }

        $data = [
            "data" => $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOLokal()
    {
        $payload = json_encode([
            "multiple_id_po" => json_decode(stripslashes($this->request->getGet("id")))
        ]);

        $dataPOLokal = [];
        $responsePOLokal = curl_request("POST", "/penerimaanBarang/list-po", $this->token, $payload);
        if ($responsePOLokal["code"] === 200) {
            $dataPOLokal = json_decode($responsePOLokal["body"])->data;
        }

        $data = [
            "data" =>  $dataPOLokal,
            "response" => $responsePOLokal
        ];

        echo json_encode($data);
        return;
    }
}