<?php

namespace App\Controllers;

class HSCode extends BaseController
{

    public function __construct()
    {
    }

    public function hsCode()
    {
        return view('hsCode/index');
    }

    public function allHSCode()
    {
        $token = session()->get("login")->token;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ]; 

        $response = curl_request("GET", "/hscode", $token, $payload);
        $dataKodeHS = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataKodeHS, [
                    "id" => $data->id,
                    "komoditi" => $data->komoditi,
                    "code" => $data->code,
                    "uraian_barang" => $data->uraian_barang,
                    "satuan_barang" => $data->satuan_barang,
                    "uraian_satuan" => $data->uraian_satuan
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataKodeHS,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownHSCode()
    {
        $token = session()->get("login")->token;

        $responseKodeHS = curl_request("GET", "/hscode/all", $token);

        $dataKodeHS = [];
        if ($responseKodeHS["code"] === 200) {
            $dataKodeHS = json_decode($responseKodeHS["body"])->data;
        }

        $data = [
            "data" => $dataKodeHS
        ];

        echo json_encode($data);
        return;
    }
}