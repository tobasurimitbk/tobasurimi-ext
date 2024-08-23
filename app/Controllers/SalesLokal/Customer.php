<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CustomerModel;
use App\Models\EmployeesModel;
use App\Models\ProvincesModel;

class Customer extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $ProvincesModel;
    protected $BanksModel;
    protected $soInvModel;
    protected $countryModel;
    protected $CustomerModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->ProvincesModel = new ProvincesModel();
        $this->BanksModel = new BanksModel();
        $this->CustomerModel = new CustomerModel();
        $this->employeeModel = new EmployeesModel();
    }

    public function index()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataBanks = $this->BanksModel->search_list(array(), 'name');

        $condition = [
            'jabatan_name' => "SALES"
        ];

        $sales = $this->employeeModel->getEmployeesComplete($this->this_company_id, $condition);

        $data = [
            "dataProvinces" => $dataProvinces,
            "dataBanks" => $dataBanks,
            "dataSales" => $sales,
        ];

        return view('SalesLokal/Customer/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        if ($this->is_admin == '1') {
            $condition = [
                'tipe_customer' => $this->request->getGet('customerType'),
                // 'customers.company_id' => $this->this_company_id,
                'customers.deletedAt' => null,
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                'tipe_customer' => $this->request->getGet('customerType'),
                // 'customers.company_id' => $this->this_company_id,
                'customers.deletedAt' => null,
                'customers.sales_id' => session()->get('login')->user_id
            ];
        }

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            'company_id' => ''
        ];

        $dataCompanyUserLogin = [$this->this_company_id];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $customerData = $this->CustomerModel->getList($condition, $dataCompanyUserLogin, $addCondition, $limit, $offset);

        $dataCustomer = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($customerData['data'] as $data) {
            array_push($dataCustomer, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "kode"          => $data->kode,
                "namaSales"     => $data->namaSales,
                "name"          => $data->name,
                "phone"         => $data->phone,
                "contact_person" => $data->contact_person,
                "saldo"         => number_format($data->saldo),
                "currencyName"  => $data->currencyName,
                "countryName"   => $data->countryName,
                "address"       => $data->address
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $customerData['totalData'],
            "recordsFiltered"   => $customerData['totalFilteredData'],
            "data"              => $dataCustomer,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }
}
