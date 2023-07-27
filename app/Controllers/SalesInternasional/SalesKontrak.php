<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use Config\Services;

use App\Models\CustomerModel;
use App\Models\SalesKontrakModel;

class SalesKontrak extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $customerModel;
    protected $salesKontrakModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->customerModel = new CustomerModel();
        $this->salesKontrakModel = new SalesKontrakModel();
    }

    public function index()
    {
        return view('SalesInternasional/SalesKontrak/index');
    }

    public function createView()
    {
        //Get Valuta Asing By Metadata
        $dataCustomer = $this->customerModel->getCustomer();
        
        $data = [
            "dataCustomer" => $dataCustomer
        ];

        return view('SalesInternasional/SalesKontrak/form', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id
        ];

        $condition = [
            "sales_contract.company_id"    => $this->this_company_id
        ];
        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesKontrakData = $this->salesKontrakModel->getList($condition, $addCondition, $limit, $offset);

        $dataSalesKontrak = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesKontrakData['data'] as $data) {
            array_push($dataSalesKontrak, [
                "no"                    => $no++,
                "sales_contract_id"     => $data->sales_contract_id,
                "sales_kontrak_no"      => $data->sales_kontrak_no,
                "customer_name"         => $data->customer_name,
                "due_date"              => $data->due_date,
                "shipment_date"         => $data->shipment_date,
                "createdAt"             => $data->createdAt
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesKontrakData['totalData'],
            "recordsFiltered"   => $salesKontrakData['totalFilteredData'],
            "data"              => $dataSalesKontrak,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }
}