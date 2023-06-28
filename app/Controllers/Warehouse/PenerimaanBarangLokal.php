<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

class PenerimaanBarangLokal extends BaseController
{
    protected $token;
    protected $this_company_id;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function penerimaanBarangLokal()
    {
        return view('Warehouse/penerimaanBarangLokal/index');
    }

    public function createPenerimaanBarangLokal()
    {
        //Get Supplier
        $responseSupplier = curl_request("GET", "/suppliers/all?kategori=LOKAL&idCompany=$this->this_company_id", $this->token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        $data = [
            "dataSupplier" => $dataSupplier
        ];

        return view('Warehouse/penerimaanBarangLokal/form', $data);
    }

    public function allPenerimaanBarangLokal()
    {
        $payload = [
            "pagesize" => $this->request->getGet("length"),
            "currentpage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sorttype" => $this->request->getGet("sortType"),
            "statuspenerimaan" => "LOKAL",
            "status" => $this->request->getGet("status"),
            "startdate" => $this->request->getGet("startdate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("startdate")))) : "",
            "lastdate" => $this->request->getGet("lastdate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("lastdate")))) : "",
        ];

        $response = curl_request("GET", "/penerimaanBarang", $this->token, $payload);
        $dataPenerimaanBarangLokal = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = 0;

            $no = ($payload["pagesize"] * ($payload["currentpage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataPenerimaanBarangLokal, [
                    "no" => $no++,
                    "id" => $data->id,
                    "invoice_no" => $data->invoice_no,
                    "multiple_po_no" => $data->multiple_po_no,
                    "acceptance_type" => $data->acceptance_type,
                    "aju_document_type" => $data->aju_document_type,
                    "aju_no" => $data->aju_no,
                    "validation_date" => $data->validation_date,
                    "packaging" => $data->packaging,
                    "status_penerimaan" => $data->status_penerimaan
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataPenerimaanBarangLokal,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePenerimaanBarangLokal()
    {
        $rules = [
            "supplier_id" => [
                "rules" => "required"
            ],
            "aju_document_type" => [
                "rules" => "required"
            ],
            "aju_no" => [
                "rules" => "required"
            ],
            "validation_date" => [
                "rules" => "required"
            ],
            "no_registration" => [
                "rules" => "required"
            ],
            "letter_no" => [
                "rules" => "required"
            ],
            "invoice_no" => [
                "rules" => "required"
            ],
            "packaging" => [
                "rules" => "required"
            ],
            "total_weight" => [
                "rules" => "required"
            ],
            "shipping_cost" => [
                "rules" => "required"
            ],
            "biaya_masuk" => [
                "rules" => "required"
            ],
            "ppn" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = json_encode([
                "no_penerimaan_barang" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("no_penerimaan_barang"),
                "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                "multiple_po_id" => json_decode(stripslashes($this->request->getPost("multiple_po_id"))),
                "multiple_po_no" => json_decode(stripslashes($this->request->getPost("multiple_po_no"))),
                "aju_document_type" => $this->request->getPost("aju_document_type"),
                "aju_no" => $this->request->getPost("aju_no"),
                "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                "no_registration" => $this->request->getPost("no_registration"),
                "letter_no" => $this->request->getPost("letter_no"),
                "invoice_no" => $this->request->getPost("invoice_no"),
                "packaging" => $this->request->getPost("packaging"),
                "total_weight" => $this->request->getPost("total_weight"),
                "shipping_cost" => formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT"),
                "biaya_masuk" => formatter($this->request->getPost("biaya_masuk"), "CURR_TO_INT"),
                "ppnbm" => formatter($this->request->getPost("ppnbm"), "CURR_TO_INT"),
                "status_post" => "WAITING",
                "status_penerimaan" => "LOKAL",
                "penerimaan_barang_detail" => json_decode(stripslashes($this->request->getPost("items")))
            ]);

            $data = [
                "status"            => false,
                "message"    => $payload,
                "payload"   => $payload,
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            
            // $response = curl_request("POST", "/penerimaanBarang", $this->token, $payload);

            // if ($response["code"] === 201) {
            //     $data = [
            //         "id" => "",
            //         "status"            => true,
            //         "message"   => "Data Berhasil disimpan",
            //         "payload"   => $payload,
            //         'token' => csrf_hash(),
            //         'code' => $response["code"]
            //     ];
            //     echo json_encode($data);
            // } else {
            //     $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
            //     $data = [
            //         "status"            => false,
            //         "message"    => $message,
            //         "payload"   => $payload,
            //         'token' => csrf_hash(),
            //         'code' => $response["code"]
            //     ];
            //     echo json_encode($data);
            // }
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
}