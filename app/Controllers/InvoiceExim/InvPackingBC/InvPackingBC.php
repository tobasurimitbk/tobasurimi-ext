<?php

namespace App\Controllers\InvoiceExim\InvPackingBC;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use Dompdf\Dompdf;

class InvPackingBC extends BaseController
{

    protected $this_company_id;
    protected $salesOrderExportModel;
    protected $metaDataModel;
    protected $divisiModel;
    protected $bankModel;
    protected $satuanModel;
    protected $companyModel;
    protected $hsCodeModel;
    protected $dompdf;

    public function __construct()
    {
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->bankModel = new BanksModel();
        $this->satuanModel = new SatuansModel();
        $this->companyModel = new CompaniesModel();
        $this->hsCodeModel = new HsCodesModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('InvoiceExim/InvPackingBC/index');
    }

    public function allOrderForm()
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
            "sales_order_export.deletedAt" => null,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportModel->getListPeb($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {

            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => \encrypt($data->sales_order_export_id),
                "no_invoice"                => $data->no_invoice,
                "tanggal_invoice"           => $data->tanggal_invoice != "" ? date('d/m/Y', strtotime($data->tanggal_invoice)) : "-",
                "customer_name"             => $data->customer_name,
                "sales_order_export_no"     => $data->sales_order_export_no,
                "dicharge_port"             => $data->dicharge_port,
                "status_invoice"            => 0,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesData['totalData'],
            "recordsFiltered"   => $salesData['totalFilteredData'],
            "data"              => $dataSales,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function indexInvPackingBC($id)
    {
        $id = decrypt($id);
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($id);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-bc');
        }

        // dd($dataSalesOrderExport);

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport
        ];

        return view('InvoiceExim/InvPackingBC/indexInv', $data);
    }

    public function createPackingCustomer($id)
    {
        $id = decrypt($id);
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($id);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-bc');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataHsCode = $this->hsCodeModel->findAll();

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataHsCode' => $dataHsCode
        ];

        //dd($data['dataSalesOrderExport']);

        return view('InvoiceExim/InvPackingBC/formInv', $data);
    }
}
