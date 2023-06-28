<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

class TerimaFakturLokal extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function terimaFakturLokal()
    {
        return view('Purchase/terimaFakturLokal/index');
    }

    public function createTerimaFakturLokal()
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

        return view('Purchase/terimaFakturLokal/form', $data);
    }

    public function getByIdTerimaFakturLokal($id = null)
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

        if (!empty($id)) {
            $responseTerimaFakturLokal = curl_request("GET", "/tandaTerimaFaktur/$id", $this->token);
            $dataTerimaFakturLokal = [];
            if ($responseTerimaFakturLokal["code"] === 200) {
                $dataTerimaFakturLokal = json_decode($responseTerimaFakturLokal["body"])->data;
            }
            $data["dataTerimaFakturLokal"] = $dataTerimaFakturLokal;
        }

        return view('Purchase/terimaFakturLokal/form', $data);
    }

    public function allTerimaFakturLokal()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "fakturtype" => "LOKAL",
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $response = curl_request("GET", "/tandaTerimaFaktur", $this->token, $payload);
        $dataTerimaFakturLokal = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataTerimaFakturLokal, [
                    "no" => $no++,
                    "id" => $data->id,
                    "faktur_no" => $data->faktur_no,
                    "sender" => $data->sender,
                    "nominal_faktur" => $data->nominal_faktur,
                    "due_date" => $data->due_date,
                    "date_of_receipt" => $data->date_of_receipt,
                    "recipient" => $data->recipient
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataTerimaFakturLokal,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }
    
    public function saveTerimaFakturLokal()
    {
        $rules = [
            "supplier_id" => [
                "rules" => "required"
            ],
            "nominal_faktur" => [
                "rules" => "required"
            ],
            "due_date" => [
                "rules" => "required"
            ],
            "date_of_receipt" => [
                "rules" => "required"
            ],
            "recipient" => [
                "rules" => "required"
            ],
            "sender" => [
                "rules" => "required"
            ],
        ];

        if ($this->validate($rules)) {
            $payload = json_encode([
                "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                "multiple_po_id" => json_decode(stripslashes($this->request->getPost("multiple_po_id"))),
                "multiple_po_no" => json_decode(stripslashes($this->request->getPost("multiple_po_no"))),
                "nominal_faktur" => formatter($this->request->getPost("nominal_faktur"), "CURR_TO_INT"),
                "due_date" => $this->request->getPost("due_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))) : "",
                "date_of_receipt" => $this->request->getPost("date_of_receipt") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("date_of_receipt")))) : "",
                "recipient" => $this->request->getPost("recipient"),
                "sender" => $this->request->getPost("sender"),
                "information" => $this->request->getPost("information"),
                "faktur_type" => "LOKAL"
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);
            
            $response = curl_request("POST", "/tandaTerimaFaktur", $this->token, $payload);

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

    public function updateTerimaFakturLokal()
    {
        $rules = [
            "supplier_id" => [
                "rules" => "required"
            ],
            "nominal_faktur" => [
                "rules" => "required"
            ],
            "due_date" => [
                "rules" => "required"
            ],
            "date_of_receipt" => [
                "rules" => "required"
            ],
            "recipient" => [
                "rules" => "required"
            ],
            "sender" => [
                "rules" => "required"
            ],
        ];

        if ($this->validate($rules)) {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                "multiple_po_id" => json_decode(stripslashes($this->request->getPost("multiple_po_id"))),
                "multiple_po_no" => json_decode(stripslashes($this->request->getPost("multiple_po_no"))),
                "nominal_faktur" => formatter($this->request->getPost("nominal_faktur"), "CURR_TO_INT"),
                "due_date" => $this->request->getPost("due_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))) : "",
                "date_of_receipt" => $this->request->getPost("date_of_receipt") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("date_of_receipt")))) : "",
                "recipient" => $this->request->getPost("recipient"),
                "sender" => $this->request->getPost("sender"),
                "information" => $this->request->getPost("information"),
                "faktur_type" => "LOKAL"
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);

            $response = curl_request("PATCH", "/tandaTerimaFaktur/$id", $this->token, $payload);

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

    public function deleteTerimaFakturLokal()
    {
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/tandaTerimaFaktur/$id", $this->token);
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

?>