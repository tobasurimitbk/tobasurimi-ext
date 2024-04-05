<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\AllNoModel;
use App\Models\BanksModel;
use App\Models\BarangMasterSalesModel;
use App\Models\CompaniesModel;
use Config\Services;
use App\Models\CustomerModel;
use App\Models\EmployeesModel;
use App\Models\MetadataModel;
use App\Models\ProvincesModel;
use App\models\SalesOrderDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderExportDetailModel;
use App\models\SalesOrderModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class OrderForm extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $customerModel;
    protected $salesOrderExportModel;
    protected $salesOrderExportDetailModel;
    protected $dompdf;

    private $companyModel;
    protected $SalesOrderModel;
    protected $BarangMasterSalesModel;
    protected $WarehousesModel;
    private $stockDetailModel;
    protected $SalesOrderDetailModel;
    protected $db;
    protected $AllNoModel;
    protected $MetaDataModel;
    protected $employeeModel;
    protected $ProvincesModel;
    protected $BanksModel;
    protected $satuanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->customerModel = new CustomerModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $this->dompdf = new Dompdf();

        $this->companyModel = new CompaniesModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->BarangMasterSalesModel = new BarangMasterSalesModel();
        $this->WarehousesModel = new WarehousesModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
        $this->AllNoModel = new AllNoModel();
        $this->MetaDataModel = new MetadataModel();
        $this->employeeModel = new EmployeesModel();
        $this->ProvincesModel = new ProvincesModel();
        $this->BanksModel = new BanksModel();
        $this->satuanModel = new SatuansModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return view('SalesInternasional/OrderForm/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "status"      => $this->request->getGet("status")
        ];

        $condition = [
            "sales_order_export.company_id"    => $this->this_company_id,
            "status"      => $this->request->getGet("status"),
            "sales_order_export.deletedAt" => null
        ];
        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status"      => $this->request->getGet("status")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportModel->getList($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => $data->sales_order_export_id,
                "sales_order_export_no"     => $data->sales_order_export_no,
                "customer_po_no"            => $data->customer_po_no,
                "customer_name"             => $data->customer_name,
                "dicharge_port"             => $data->dicharge_port,
                "shipment_date"             => $data->shipment_date,
                "createdAt"                 => date('Y-m-d', strtotime($data->createdAt)),
                "status"                    => $data->status
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesData['totalData'],
            "recordsFiltered"   => $salesData['totalFilteredData'],
            "data"              => $dataSales,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function createView()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataBanks = $this->BanksModel->search_list(array(), 'name');
        $dataSatuan = $this->satuanModel->findAll();
        //Get Customers
        $customers = $this->customerModel->getCustomerLokal();

        $condition = [
            'jabatan_name' => "SALES"
        ];

        $sales = $this->employeeModel->getEmployeesComplete($this->this_company_id, $condition);

        $dataCompany = $this->companyModel->where('deletedAt', NULL)->asObject()->findAll();

        $dataTermin = $this->MetaDataModel
            ->where('metadata.deletedAt', null)
            ->where('metadata.name', 'Termin')
            ->orderBy('CAST(metadata.value AS DECIMAL)', 'ASC')
            ->findAll();


        $data = [
            "dataCustomers" => $customers,
            "dataSales" => $sales,
            "companies" => $dataCompany,
            "dataTermin" => $dataTermin,
            "dataProvinces" => $dataProvinces,
            "dataBanks" => $dataBanks,
            'dataSatuan' => $dataSatuan,
            "id_user" => session()->get('login')->user_id,
            "seller_name" => session()->get('login')->name,

        ];

        return view('SalesInternasional/OrderForm/form', $data);
    }

    public function getById($id = null)
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataBanks = $this->BanksModel->search_list(array(), 'name');
        $dataSatuan = $this->satuanModel->findAll();
        $dataCustomer = $this->customerModel->getCustomer();

        $data = [
            "dataProvinces" => $dataProvinces,
            "dataBanks" => $dataBanks,
            'dataSatuan' => $dataSatuan,
            "dataCustomer" => $dataCustomer
        ];

        if (!empty($id)) {
            $dataSO = $this->salesOrderExportModel->getById($id);
            $data["dataSO"] = $dataSO;

            $dataSODetail = $this->salesOrderExportDetailModel->getSalesOrderExportDetailBySalesOrderExportId($id);

            if ($dataSODetail) {
                $data["dataSODetail"] = $dataSODetail;
            }
        }

        return view('SalesInternasional/OrderForm/form', $data);
    }

    public function update()
    {
        try {
            $id = $this->request->getPost("id");

            $payload = [
                "director_name" => $this->request->getPost("director_name"),
                "marketing_name" => $this->request->getPost("marketing_name"),
                "exim_name" => $this->request->getPost("exim_name"),
                "procurement_name" => $this->request->getPost("procurement_name"),
                "production_name" => $this->request->getPost("production_name"),
                "qc_name" => $this->request->getPost("qc_name"),
            ];

            $condition = [
                'sales_order_export_id' => $id
            ];

            $response = $this->salesOrderExportModel->where($condition)->set($payload)->update();

            if ($response) {
                $data = [
                    "id" => "",
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    "response" => $response,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Diubah';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateStatus()
    {
        try {
            $id = $this->request->getPost("id");
            $status = $this->request->getPost("status");

            $payload = [
                "status" => $status
            ];

            $condition = [
                'sales_order_export_id' => $id
            ];

            $response = $this->salesOrderExportModel->where($condition)->set($payload)->update();

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => $status === "POSTED" ? "Data Berhasil diposting" : "Data Berhasil diunposting",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = $status === "POSTED" ? "Data Gagal diposting" : "Data Gagal diunposting";
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function print($id = null)
    {
        if ($id) {
            $filename = "ORDER FORM";

            $data = [];
            $dataSO = $this->salesOrderExportModel->getById($id);

            if ($dataSO) {
                $dataSODetail = $this->salesOrderExportDetailModel->getSalesOrderExportDetailBySalesOrderExportId($id);

                // var_dump($dataSO);
                // die;

                if ($dataSODetail) {
                    $data["dataSO"] = $dataSO;
                    $data["dataSODetail"] = $dataSODetail;
                }
            }

            // load HTML content
            $this->dompdf->loadHtml(view('SalesInternasional/OrderForm/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'portrait');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Purchase/poImportBahanPenolong/print', $data);
        }
    }
}
