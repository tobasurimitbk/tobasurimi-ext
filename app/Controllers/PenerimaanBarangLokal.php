<?php

namespace App\Controllers;

class PenerimaanBarangLokal extends BaseController
{

    public function __construct()
    {
    }

    public function penerimaanBarangLokal()
    {
        return view('penerimaanBarangLokal/index');
    }

    public function allPenerimaanBarangLokal()
    {
        $token = session()->get("login")->token;

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

        $response = curl_request("GET", "/penerimaanBarang", $token, $payload);
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
                    "no_penerimaan_barang" => $data->no_penerimaan_barang,
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
}