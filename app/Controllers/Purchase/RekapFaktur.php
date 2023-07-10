<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

class RekapFaktur extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        return view('Purchase/rekapFaktur/index');
    }

    public function createRekapFaktur()
    {
        //Get Order Type By Metadata
        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this->this_company_id&kategori=lokal", $this->token);

        $supplierList = [];
        if ($responseSupplier["code"] === 200) {
            $supplierList = json_decode($responseSupplier["body"])->data;
        }

        $data = [
            "supplierList" => $supplierList
        ];

        return view('Purchase/rekapFaktur/form', $data);
    }

    public function saveRekapFaktur()
    {
        try { 
            $rules = [
                "supplier_id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "invoices.*" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "due_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $payload = json_encode([
                "supplier_id"   => (int)$this->request->getPost("supplier_id"),
                "invoices"      => $this->request->getPost("invoices"),
                "due_date"      => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))),
            ]);

            $response = curl_request("POST", "/localPOInvSummary", $this->token, $payload);

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

    public function getRekapFakturList()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            // "requestStatus" => $this->request->getGet("status"),
            // "dateStart" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            // "dateEnd" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $response = curl_request("GET", "/localPOInvSummary", $this->token, $payload);

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
                    "summary_no" => $data->summary_no,
                    "total" => number_format($data->total),
                    "due_date" => $data->due_date,
                    // "is_posted" => $data->is_posted,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataSPP,
            "response" => $response,
            "payload" => $payload,
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1
        ];

        echo json_encode($data);
        return;
    }

    public function getRekapFakturBySupplier($supplierId)
    {
        $response = curl_request("GET", "/localPOInvSummary/supplier/$supplierId", $this->token);

        $fakturList = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $fakturList = json_decode($response["body"])->data;
        }

        $data = [
            'data'=> $fakturList
        ];

        echo json_encode($data);
        return;
    }

    public function getRekapFakturById($id)
    {
        $rekapResponse = curl_request("GET", "/localPOInvSummary/$id", $this->token);
        $rekapData = [];
        if ($rekapResponse["code"] === 200) {
            $rekapData = json_decode($rekapResponse["body"])->data;
        }
        $selectedFaktur = array_column($rekapData->local_po_inv_sum_details, 'tanda_terima_faktur_id');

        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this->this_company_id&kategori=lokal", $this->token);
        $supplierList = [];
        if ($responseSupplier["code"] === 200) {
            $supplierList = json_decode($responseSupplier["body"])->data;
        }

        $fakturResponse = curl_request("GET", "/tandaTerimaFaktur/getBySupplier/$rekapData->supplier_id", $this->token);
        $fakturList = [];
        if ($fakturResponse["code"] === 200) {
            $fakturList = json_decode($fakturResponse["body"])->data;
        }

        $data = [
            "rekapData"     => $rekapData,
            "supplierList"  => $supplierList,
            "fakturList"    => $fakturList,
            "selectedFaktur"=> $selectedFaktur
        ];

        return view('Purchase/rekapFaktur/form', $data);
    }
}
