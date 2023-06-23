<?php

namespace App\Controllers;

class POImport extends BaseController
{

    public function __construct()
    {
    }

    public function poImport()
    {
        return view('poImport/index');
    }

    public function createPOImport()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

         //Get SPP Number
         $responseSPP = curl_request("GET", "/purchaseRequest/getByType/Import", $token);

        $dataSPP = [];
        if ($responseSPP["code"] === 200) {
            $dataSPP = json_decode($responseSPP["body"])->data;
        }

        //Get Supplier
        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this_company_id", $token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        //Get Valuta Asing By Metadata
        $responseValuta = curl_request("GET", "/metadata/all?name=valuta_asing", $token);

        $dataValuta = [];
        if ($responseValuta["code"] === 200) {
            $dataValuta = json_decode($responseValuta["body"])->data;
        }
        
        $data = [
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        return view('poImport/form', $data);
    }

    public function getByIdPOImport($id = null)
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

         //Get SPP Number
         $responseSPP = curl_request("GET", "/purchaseRequest/getByType/Import", $token);

        $dataSPP = [];
        if ($responseSPP["code"] === 200) {
            $dataSPP = json_decode($responseSPP["body"])->data;
        }

        //Get Supplier
        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this_company_id", $token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        //Get Valuta Asing By Metadata
        $responseValuta = curl_request("GET", "/metadata/all?name=valuta_asing", $token);

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
            $responsePOImport = curl_request("GET", "/purchaseOrder/$id", $token);
            $dataPOImport = [];
            if ($responsePOImport["code"] === 200) {
                $dataPOImport = json_decode($responsePOImport["body"])->data;
            }
            $data["dataPOImport"] = $dataPOImport;
        }

        return view('poImport/form', $data);
        
        return;
    }

    public function allPOImport()
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

        $response = curl_request("GET", "/purchaseOrder", $token, $payload);
        $dataPOImport = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = 0;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataPOImport, [
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
            "data" => $dataPOImport,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePOImport()
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
            $token = session()->get("login")->token;

            $payload = json_encode([
                "purchase_request_id" => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                "po_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                "order_type" => formatter($this->request->getPost("order_type"), "STR_TO_INT"),
                "po_type" => "import",
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
            
            $response = curl_request("POST", "/purchaseOrder", $token, $payload);

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

    public function updatePOImport()
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
            $token = session()->get("login")->token;

            $id = $this->request->getPost("id");

            $payload = json_encode([
                "purchase_request_id" => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                "po_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                "order_type" => formatter($this->request->getPost("order_type"), "STR_TO_INT"),
                "po_type" => "import",
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

            $response = curl_request("PATCH", "/purchaseOrder/$id", $token, $payload);

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

    public function updateStatusPOImport()
    {
        $token = session()->get("login")->token;

        $id = $this->request->getPost("id");

        $payload = json_encode([
            "is_posted" => true
        ]);
        
        $response = curl_request("PATCH", "/purchaseOrder/$id", $token, $payload);

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

    public function deletePOImport()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/purchaseOrder/$id", $token);
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