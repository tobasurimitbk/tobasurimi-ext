<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;

class SuratJalan extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
    }

    public function index()
    {
        return view('SalesLokal/SuratJalan/index');
    }

    public function createView()
    {
        return view('SalesLokal/SuratJalan/form');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $response = curl_request("GET", "/suratJalan", $this->token, $payload);
        $dataOrderForm = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataOrderForm, [
                    "no" => $no++,
                    "id" =>  bin2hex($this->encrypter->encrypt($data->id)),
                    "kode_pelanggan" => $data->kode_pelanggan,
                    "nama_pelanggan" => $data->nama_pelanggan,
                    "multiple_no_so" => $data->multiple_no_so,
                    "no_surat_jalan" => $data->no_surat_jalan,
                    "shipping_date" => $data->shipping_date,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataOrderForm,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
    }

    public function getById()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
