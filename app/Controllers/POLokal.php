<?php

namespace App\Controllers;

class POLokal extends BaseController
{

    public function __construct()
    {
    }

    public function poLokal()
    {
        return view('poLokal/index');
    }

    public function createPOLokal()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

         //Get SPP Number
         $responseSPP = curl_request("GET", "/purchaseRequest/getByType/Lokal", $token);

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

        return view('poLokal/form', $data);
    }

    public function getByIdPOLokal($id = null)
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

         //Get SPP Number
         $responseSPP = curl_request("GET", "/penerimaanBarang?statuspenerimaan=LOKAL", $token);

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
            $responsePOLokal = curl_request("GET", "/purchaseOrder/$id", $token);
            $dataPOLokal = [];
            if ($responsePOLokal["code"] === 200) {
                $dataPOLokal = json_decode($responsePOLokal["body"])->data;
            }
            $data["dataPOLokal"] = $dataPOLokal;
        }

        return view('poLokal/form', $data);
        
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
            $token = session()->get("login")->token;

            $payload = json_encode([
                "purchase_request_id" => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                "po_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
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
            
            $response = curl_request("POST", "/purchaseOrder", $token, $payload);

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
            $token = session()->get("login")->token;

            $id = $this->request->getPost("id");

            $payload = json_encode([
                "purchase_request_id" => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                "po_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
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

    public function deletePOLokal()
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