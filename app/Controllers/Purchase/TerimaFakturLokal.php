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
        $responseSupplier = curl_request("GET", "/suppliers/all?kategori=LOKAL&type=BAHAN%20BAKU&idCompany=$this->this_company_id", $this->token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        //Get Customer
        $responseCustomer = curl_request("GET", "/customers/all?idCompany=$this->this_company_id", $this->token);

        $dataCustomer = [];
        if ($responseCustomer["code"] === 200) {
            $dataCustomer = json_decode($responseCustomer["body"])->data;
        }

        $data = [
            "dataSupplier" => $dataSupplier,
            "dataCustomer" => $dataCustomer
        ];

        return view('Purchase/terimaFakturLokal/form', $data);
    }

    public function getByIdTerimaFakturLokal($id = null)
    {
        //Get Customer
        $responseCustomer = curl_request("GET", "/customers/all?idCompany=$this->this_company_id", $this->token);

        $dataCustomer = [];
        if ($responseCustomer["code"] === 200) {
            $dataCustomer = json_decode($responseCustomer["body"])->data;
        }

        $data = [
            "dataCustomer" => $dataCustomer
        ];

        if (!empty($id)) {
            $responseTerimaFaktur = curl_request("GET", "/tandaTerimaFaktur/$id", $this->token);
            $dataTerimaFaktur = [];
            if ($responseTerimaFaktur["code"] === 200) {
                $dataTerimaFaktur = json_decode($responseTerimaFaktur["body"])->data;

                $tipe_bahan = json_decode($responseTerimaFaktur["body"])->data->tipe_bahan;
                if($tipe_bahan === "BAKU")
                {
                    $dataNo = [];
                    $arr = json_decode($responseTerimaFaktur["body"])->data->supplier_id;
                    $responseNo = curl_request("GET", "/penerimaanBarang/drop-down-po?potype=LOKAL&tipebahan=BAKU&supplierid=$arr", $this->token);
                    if ($responseNo["code"] === 200) {
                        $dataNo = json_decode($responseNo["body"])->data;
                    }

                    //Get Supplier
                    $responseSupplier = curl_request("GET", "/suppliers/all?kategori=LOKAL&type=BAHAN%20BAKU&idCompany=$this->this_company_id", $this->token);

                    $dataSupplier = [];
                    if ($responseSupplier["code"] === 200) {
                        $dataSupplier = json_decode($responseSupplier["body"])->data;
                    }
                    
                    $data["dataNo"] = $dataNo;
                    $data["dataSupplier"] = $dataSupplier;
                }
                if($tipe_bahan === "PENOLONG")
                {
                    $dataNo = [];
                    $arr = json_decode($responseTerimaFaktur["body"])->data->supplier_id;
                    $responseNo = curl_request("GET", "/penerimaanBarang/drop-down-po?potype=LOKAL&tipebahan=PENOLONG&supplierid=$arr", $this->token);
                    if ($responseNo["code"] === 200) {
                        $dataNo = json_decode($responseNo["body"])->data;
                    }

                    //Get Supplier
                    $responseSupplier = curl_request("GET", "/suppliers/all?kategori=LOKAL&type=BAHAN%20PENOLONG&idCompany=$this->this_company_id", $this->token);

                    $dataSupplier = [];
                    if ($responseSupplier["code"] === 200) {
                        $dataSupplier = json_decode($responseSupplier["body"])->data;
                    }

                    $data["dataNo"] = $dataNo;
                    $data["dataSupplier"] = $dataSupplier;
                }
            }
            $data["dataTerimaFaktur"] = $dataTerimaFaktur;
        }

        return view('Purchase/terimaFakturLokal/form', $data);
    }

    public function getBySupplierId($supplierId = null)
    {
        //Get Faktur
        $responseFaktur = curl_request("GET", "/tandaTerimaFaktur/getBySupplier/$supplierId", $this->token);

        $dataFaktur = [];
        if ($responseFaktur["code"] === 200) {
            $dataFaktur = json_decode($responseFaktur["body"])->data;
        }

        $data = [
            "data" => $dataFaktur
        ];

        echo json_encode($data);
        return;
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
        try{
            $rules = [
                "customer_id" => [
                    "rules" => "required"
                ],
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
            ];
    
            if ($this->validate($rules)) {
                $payload = json_encode([
                    "customer_id" => formatter($this->request->getPost("customer_id"), "STR_TO_INT"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "multiple_po_id" => formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT"),
                    "multiple_po_no" => json_decode($this->request->getPost("multiple_po_no")),
                    "nominal_faktur" => formatter($this->request->getPost("nominal_faktur"), "CURR_TO_INT"),
                    "due_date" => $this->request->getPost("due_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))) : "",
                    "date_of_receipt" => $this->request->getPost("date_of_receipt") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("date_of_receipt")))) : "",
                    "recipient" => $this->request->getPost("recipient"),
                    "sender" => $this->request->getPost("sender"),
                    "information" => $this->request->getPost("information"),
                    "faktur_type" => "LOKAL",
                    "tipe_bahan" => $this->request->getPost("tipe_bahan")
                ]);
    
                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);
                
                $response = curl_request("POST", "/tandaTerimaFaktur", $this->token, $payload);
    
                if ($response["code"] === 200) {
                    $data = [
                        "id" => json_decode($response["body"])->data->id,
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
}
?>