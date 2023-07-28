<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use Config\Services;
use App\Models\MetadataModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderDetailModel;

class OrderForm extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $MetadataModel;
    protected $SalesOrderModel;
    protected $SalesOrderDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->MetadataModel = new MetadataModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
    }

    public function index()
    {
        return view('SalesInternasional/OrderForm/index');
    }

    public function createView()
    {
        //Get Valuta Asing By Metadata
        $dataValuta = $this->MetadataModel->get_by_name('Valuta Asing');

        $data = [
            "dataValuta" => $dataValuta
        ];
        return view('SalesInternasional/OrderForm/form', $data);
    }

    public function all()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize" => $pageSize,
            "currentPage" => $currentPage,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];


        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataOrderForm = $this->SalesOrderModel
            ->getAllSalesOrderImport($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $dataSalesOrder = [];
        foreach ($dataOrderForm['data'] as $data) {
            array_push($dataSalesOrder, [
                "no"            => $no++,
                "id"            => $data->id,
                "no_sales_order"      => $data->no_sales_order,
                "destination"        => $data->destination,
                "qty_barang" => $data->qty_barang,
                "total_harga"         => number_format($data->total_harga),
                "keterangan"  => $data->keterangan
            ]);
        }


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataOrderForm['totalData'],
            "recordsFiltered" => $dataOrderForm['totalFilteredData'],
            "data"              => $dataSalesOrder,
            "payload"           => $payload
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
