<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;

class Invoice extends BaseController
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
        return view('SalesLokal/Invoice/index');
    }

    public function createView()
    {
        return view('SalesLokal/Invoice/form');
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

        $response = curl_request("GET", "/salesOrderInvoice", $this->token, $payload);
        $dataInvoice = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataInvoice, [
                    "id" =>  bin2hex($this->encrypter->encrypt($data->id)),
                    "no_faktur" => $data->no_faktur,
                    "ship_via" => $data->ship_via,
                    "keterangan" => $data->keterangan,
                    "total_invoice" => $data->total_invoice,
                    "kode_pelanggan" => $data->kode_pelanggan,
                    "nama_pelanggan" => $data->nama_pelanggan,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataInvoice,
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
