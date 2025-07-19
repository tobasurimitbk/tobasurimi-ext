<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\CountryModel;
use App\Models\CustomerModel;
use App\Models\EmployeesModel;

class Customer extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $ProvincesModel;
    protected $countryModel;
    protected $CustomerModel;
    protected $employessModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->countryModel = new CountryModel();
        $this->CustomerModel = new CustomerModel();
        $this->employessModel = new EmployeesModel();
    }

    public function index()
    {
        $dataCountry = $this->countryModel->findAll();
        $condition = [
            'jabatan_name' => "SALES INTERNASIONAL"
        ];
        $sales = $this->employessModel->getEmployeesComplete($this->this_company_id, $condition);

        $data = [
            "dataCountry" => $dataCountry,
            "dataSales" => $sales,
        ];

        return view('SalesInternasional/Customer/index', $data);
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
        } else {
            $condition = [
                'tipe_customer' => $this->request->getGet('customerType'),
                // 'customers.company_id' => $this->this_company_id,
                'customers.deletedAt' => null,
                'customers.user_id' => session()->get('login')->user_id
            ];
        }

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $customerData = $this->CustomerModel->getListCustomerDetail($condition, $addCondition, $limit, $offset);

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
