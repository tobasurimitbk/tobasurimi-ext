<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;

class OrderForm extends BaseController
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
        return view('SalesLokal/OrderForm/index');
    }

    public function createView()
    {
        //Get Customers
        $responseEmployee = curl_request("GET", "/customers/all?idCompany=$this->this_company_id", $this->token);

        $dataCustomers = [];
        if ($responseEmployee["code"] === 200) {
            $dataCustomers = json_decode($responseEmployee["body"])->data;
        }

        $data = [
            "dataCustomers" => $dataCustomers,
        ];

        return view('SalesLokal/OrderForm/form', $data);
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

        $response = curl_request("GET", "/salesOrderLokal", $this->token, $payload);
        $dataOrderForm = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataOrderForm, [
                    "id" =>  bin2hex($this->encrypter->encrypt($data->id)),
                    "no_sales_order" => $data->no_sales_order,
                    "destination" => $data->destination,
                    "qty_barang" => $data->qty_barang,
                    "total_harga" => $data->total_harga,
                    "keterangan" => $data->keterangan,
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


    public function getAllBarang()
    {
        //Get Barang
        $responseBarang = curl_request("GET", "/barang/all?idCompany=$this->this_company_id", $this->token);

        $dataBarang = [];
        if ($responseBarang["code"] === 200) {
            $dataBarang = json_decode($responseBarang["body"])->data;
        }

        $data = [
            "dataBarang" => $dataBarang,
        ];


        echo json_encode($data);
        return;
    }
}
