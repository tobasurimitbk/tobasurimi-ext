<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

class PenerimaanBarangImport extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function penerimaanBarangImport()
    {
        return view('Warehouse/penerimaanBarangImport/index');
    }

    public function createPenerimaanBarangImport()
    {
        //Get Supplier
        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this->this_company_id", $this->token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        $data = [
            "dataSupplier" => $dataSupplier
        ];

        return view('Warehouse/penerimaanBarangImport/form', $data);
    }

    public function allPenerimaanBarangImport()
    {
        $payload = [
            "pagesize" => $this->request->getGet("length"),
            "currentpage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sorttype" => $this->request->getGet("sortType"),
            "statuspenerimaan" => "IMPORT",
            "status" => $this->request->getGet("status"),
            "startdate" => $this->request->getGet("startdate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("startdate")))) : "",
            "lastdate" => $this->request->getGet("lastdate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("lastdate")))) : "",
        ];

        $response = curl_request("GET", "/penerimaanBarang", $this->token, $payload);
        $dataPenerimaanBarangImport = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = 0;

            $no = ($payload["pagesize"] * ($payload["currentpage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataPenerimaanBarangImport, [
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
            "data" => $dataPenerimaanBarangImport,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }
}