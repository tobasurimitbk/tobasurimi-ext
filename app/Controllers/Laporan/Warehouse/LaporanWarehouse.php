<?php

namespace App\Controllers\Laporan\Warehouse;

use App\Controllers\BaseController;
use App\Controllers\BeaCukai\BC27;
use Config\Services;
use Dompdf\Dompdf;

use App\Models\CompaniesModel;
use App\Models\SalesOrderModel;
use App\Models\CustomerModel;

use App\Models\WarehousesModel;
use App\Models\DetailStockBarang;
use App\Models\StockDetailModel;
use App\Models\SalesOrderDetailModel;
use App\Models\AllNoModel;
use App\Models\BanksModel;
use App\Models\BarangMasterSalesModel;
use App\Models\EmployeesModel;
use App\Models\MetadataModel;
use App\Models\ProvincesModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use App\Models\SuratJalanModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\PenerimaanMutasiGlobalModel;
use App\Models\PenerimaanMutasiModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\KemasanModel;
use App\Models\StockModel;
use App\Models\StockDetail2Model;
use App\Models\MaterialRequestsModel;
use App\Models\MaterialRequestsPenolongModel;
use App\Models\DivisisModel;
use App\Models\BC23Model;
use App\Models\BC25Model;
use App\Models\BC27Model;
use App\Models\BC30Model;
use App\Models\BC40Model;
use App\Models\BC41Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Error;
use ErrorException;

class LaporanWarehouse extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;

    private $companyModel;
    protected $SalesOrderModel;
    protected $CustomerModel;
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
    protected $suratJalanModel;
    protected $salesOrderInvoiceModel;
    protected $salesOrderExportModel;
    protected $rmPurchaseOrderDetailModel;
    protected $rmPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $amPurchaseOrderModel;
    protected $rmImportPODetailModel;
    protected $rmImportPOModel;
    protected $penerimaanBarangModel;
    protected $bcPurchaseOrderModel;
    protected $penerimaanMutasiGlobalModel;
    protected $penerimaanMutasiModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $kemasanModel;
    protected $stockModel;
    protected $stockDetail2Model;
    protected $materialRequestModel;
    protected $materialRequestPenolongModel;
    protected $divisisModel;
    protected $bc23Model;
    protected $bc25Model;
    protected $bc27Model;
    protected $bc30Model;
    protected $bc40Model;
    protected $bc41Model;


    private $userId;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();

        $this->companyModel = new CompaniesModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->CustomerModel = new CustomerModel();
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
        $this->suratJalanModel = new SuratJalanModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $this->penerimaanMutasiGlobalModel = new PenerimaanMutasiGlobalModel();
        $this->penerimaanMutasiModel = new PenerimaanMutasiModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->kemasanModel = new KemasanModel();
        $this->stockModel = new StockModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestPenolongModel = new MaterialRequestsPenolongModel();
        $this->divisisModel = new DivisisModel();
        $this->bc23Model = new BC23Model();
        $this->bc25Model = new BC25Model();
        $this->bc27Model = new BC27Model();
        $this->bc30Model = new BC30Model();
        $this->bc40Model = new BC40Model();
        $this->bc41Model = new BC41Model();


        $this->userId = session()->get("login")->user_id;
    }

    public function index()
    {

        return view('Laporan/Warehouse/index/index');
    }

    public function laporanSalesOrder()
    {
        return view('Laporan/Warehouse/LaporanSalesOrder/index');
    }

    public function allLaporanSalesOrder()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $condition = [
            'sales_order_invoice.id_company'  => $this->this_company_id,
            'sales_order_invoice.status_posting'  => '1',
        ];

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];


        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "filter_jenis_dokumen"        => $this->request->getGet("filter_jenis_dokumen"),
            "filter_status"        => $this->request->getGet("filter_status"),
        ];


        $dataSalesOrder = $this->salesOrderInvoiceModel->getAllSalesOrderInvoiceReportLaporan($condition, $addCondition, $pageSize, $offset);

        // var_dump($dataSalesOrder['data']);
        // die();


        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataAllSalesOrderInvoice = [];

        foreach ($dataSalesOrder['data'] as $data) {


            // Karakter yang akan dihapus
            $unwanted_characters = array('[', '"', ']');

            // Gantikan karakter tidak diinginkan dengan string kosong
            $cleaned_string_document_no = str_replace($unwanted_characters, ' ', $data->document_no);

            array_push($dataAllSalesOrderInvoice, [
                "no"                => $no++,
                "document_type"     => $data->document_type,
                "no_faktur"         => $data->no_faktur,
                "tanggal_faktur"    => $data->tanggal_faktur,
                "nama_pelanggan"    => $data->nama_pelanggan,
                "kode_barang"    => $data->kode_barang,
                "nama_barang"    => $data->nama_barang,
                "satuan"    => $data->satuan,
                "document_no"       => $cleaned_string_document_no,
                "qty"    => $data->qty,
                "qty_invoice"    => $data->qty_invoice,
                "amount_invoice"     => number_format(floatval($data->amount_invoice)),
                "qty_sekarang"    => $data->qty_sekarang,

            ]);
        }



        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataSalesOrder['totalData'],
            "recordsFiltered" => $dataSalesOrder['totalFilteredData'],
            'data'      => $dataAllSalesOrderInvoice,
            "payload" => $payload,

        ];

        echo json_encode($data);
        return;
    }

    public function exportPDFLaporanSalesOrder()
    {

        $condition = [
            'sales_order_invoice.id_company'  => $this->this_company_id,
            'sales_order_invoice.status_posting'  => '1',
        ];



        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "filter_jenis_dokumen"        => $this->request->getGet("filter_jenis_dokumen"),
            "filter_status"        => $this->request->getGet("filter_status"),
        ];


        $selectQry = "sales_order_invoice.*,
                      barang_master_sales.kode_barang AS kode_barang,
                      barang_master_sales.barang_name AS nama_barang,
                       satuans.kode_satuan AS satuan,
                       sales_order_invoice_detail.qty_invoice AS qty_invoice,
                        sales_order_invoice_detail.amount_invoice AS amount_invoice,
                        sales_order_invoice_detail.qty_invoice_awal AS qty,
                       sales_order_invoice_detail.qty_invoice_sisa AS qty_sekarang,
                      DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
                      customers.name AS nama_pelanggan,
                      customers.kode AS kode_pelanggan,
                      
                      ";

        $salesOrderInvoice = $this->salesOrderInvoiceModel->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice', 'LEFT')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            ->where($condition);

        if ($addCondition['filter_jenis_dokumen'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesOrderInvoice->groupStart();
        }


        if ($addCondition['filter_jenis_dokumen']) {
            $salesOrderInvoice->where('sales_order_invoice.document_type', $addCondition['filter_jenis_dokumen']);
        }

        // if ($addCondition['filter_status']) {
        //     $salesOrderInvoice->where('sales_order_invoice.status_posting', $addCondition['filter_status']);
        // }

        if ($addCondition['dateStart']) {
            $salesOrderInvoice->where('sales_order_invoice.tanggal_faktur >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $salesOrderInvoice->where('sales_order_invoice.tanggal_faktur <=', $addCondition['dateEnd']);
        }

        if ($addCondition['filter_jenis_dokumen'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesOrderInvoice->groupEnd();
        }

        $getDataSalesOrderInvoice = $salesOrderInvoice->findAll();

        $dataAllSalesOrderInvoice = [];
        $no = 1;

        foreach ($getDataSalesOrderInvoice as $data) {

            // Karakter yang akan dihapus
            $unwanted_characters = array('[', '"', ']');

            // Gantikan karakter tidak diinginkan dengan string kosong
            $cleaned_string_document_no = str_replace($unwanted_characters, ' ', $data->document_no);

            array_push($dataAllSalesOrderInvoice, [
                "no"                => $no++,
                "document_type"     => $data->document_type,
                "no_faktur"         => $data->no_faktur,
                "tanggal_faktur"    => $data->tanggal_faktur,
                "nama_pelanggan"    => $data->nama_pelanggan,
                "kode_barang"    => $data->kode_barang,
                "nama_barang"    => $data->nama_barang,
                "satuan"    => $data->satuan,
                "document_no"       => $cleaned_string_document_no,
                "qty"    => $data->qty,
                "qty_invoice"    => $data->qty_invoice,
                "amount_invoice"     => number_format(floatval($data->amount_invoice)),
                "qty_sekarang"    => $data->qty_sekarang,

            ]);
        }

        $data = [
            'no' => 1,
            'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            'filter_jenis_dokumen' =>  $this->request->getGet("filter_jenis_dokumen"),
            'dataAllSalesOrderInvoice' => $dataAllSalesOrderInvoice,

        ];

        $domPdf = new Dompdf();

        $fileName = 'Laporan Sales Order';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/Warehouse/LaporanSalesOrder/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanSalesOrderEkspor()
    {
        return view('Laporan/Warehouse/LaporanSalesOrderEkspor/index');
    }

    public function allLaporanSalesOrderEkspor()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $condition = [
            'sales_order_export.company_id'  => $this->this_company_id,
            'sales_order_export.status'  => 'POSTED',
            'sales_order_detail_export.tipe_input'  => 'order_form',
        ];

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];


        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];


        $dataSalesOrderExport = $this->salesOrderExportModel->getAllSalesOrderExportReport($condition, $addCondition, $pageSize, $offset);




        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataAllSalesOrderExport = [];

        foreach ($dataSalesOrderExport['data'] as $data) {


            array_push($dataAllSalesOrderExport, [
                "no"                => $no++,
                "sales_order_export_no"         => $data->sales_order_export_no,
                "tanggal"    => date('Y-m-d', strtotime($data->updatedAt)),
                "nama_pelanggan"    => $data->customer_name,
                "kode_barang"    => $data->barang_kode,
                "nama_barang"    => $data->barang_name,
                "satuan"    => $data->satuan,
                "qty"    => $data->qty_awal,
                "qty_diterima"    => $data->qty,
                "total_harga_barang"     => number_format(floatval($data->total_harga_barang)),
                "qty_sisa"    => $data->qty_sisa,

            ]);
        }



        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataSalesOrderExport['totalData'],
            "recordsFiltered" => $dataSalesOrderExport['totalFilteredData'],
            'data'      => $dataAllSalesOrderExport,
            "payload" => $payload,

        ];

        echo json_encode($data);
        return;
    }

    public function exportPDFLaporanSalesOrderEkspor()
    {



        $condition = [
            'sales_order_export.company_id'  => $this->this_company_id,
            'sales_order_export.status'  => 'POSTED',
            'sales_order_detail_export.tipe_input'  => 'order_form',
        ];


        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];


        $dataSalesOrderExport = $this->salesOrderExportModel->getAllSalesOrderExportReportPDF($condition, $addCondition);


        $no =  1;
        $dataAllSalesOrderExport = [];

        foreach ($dataSalesOrderExport['data'] as $data) {


            array_push($dataAllSalesOrderExport, [
                "no"                => $no++,
                "sales_order_export_no"         => $data->sales_order_export_no,
                "tanggal"    => date('Y-m-d', strtotime($data->updatedAt)),
                "nama_pelanggan"    => $data->customer_name,
                "kode_barang"    => $data->barang_kode,
                "nama_barang"    => $data->barang_name,
                "satuan"    => $data->satuan,
                "qty"    => $data->qty_awal,
                "qty_diterima"    => $data->qty,
                "total_harga_barang"     => number_format(floatval($data->total_harga_barang)),
                "qty_sisa"    => $data->qty_sisa,

            ]);
        }



        $data = [
            'no' => 1,
            'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            'data'      => $dataAllSalesOrderExport,


        ];


        $domPdf = new Dompdf();

        $fileName = 'Laporan Sales Order Ekspor';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/Warehouse/LaporanSalesOrderEkspor/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanPurchaseOrder()
    {
        return view('Laporan/Warehouse/LaporanPurchaseOrder/index');
    }

    public function allLaporanPurchaseOrder()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),

        ];

        $po_type        = $this->request->getGet("filter_po_type");

        if (empty($po_type)) {
            $condition = [
                'rm_purchase_orders.deletedAt' => null,
                'rm_purchase_order_details.deletedAt' => null,
                'rm_purchase_orders.is_posted' => '1',
                'rm_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->rmPurchaseOrderDetailModel->getListLPBBahanBakuReport($condition,  $addCondition, "LOKAL", "BAKU", $pageSize, $offset);
        } elseif ($po_type == "PO LOKAL BB") {
            $condition = [
                'rm_purchase_orders.deletedAt' => null,
                'rm_purchase_order_details.deletedAt' => null,
                'rm_purchase_orders.is_posted' => '1',
                'rm_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->rmPurchaseOrderDetailModel->getListLPBBahanBakuReport($condition,  $addCondition, "LOKAL", "BAKU", $pageSize, $offset);
        } elseif ($po_type == "PO LOKAL BP") {
            $condition = [
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_orders.is_posted' => '1',
                'am_purchase_orders.po_type' => 'Lokal',
                'am_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->amPurchaseOrderDetailModel->getListLPBBahanPenolongReport($condition,  $addCondition, "LOKAL", "PENOLONG", $pageSize, $offset);
        } elseif ($po_type == "PO IMPOR BB") {
            $condition = [
                'rm_import_pos.deletedAt' => null,
                'rm_import_po_details.deletedAt' => null,
                'rm_import_pos.is_posted' => '1',
                'rm_import_pos.company_id' => $this->this_company_id
            ];


            $dataPurchaseOrder = $this->rmImportPODetailModel->getListLPBBahanBakuReport($condition,  $addCondition, "IMPORT", "BAKU", $pageSize, $offset);
        } elseif ($po_type == "PO IMPOR BP") {
            $condition = [
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_orders.is_posted' => '1',
                'am_purchase_orders.po_type' => 'Import',
                'am_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->amPurchaseOrderDetailModel->getListLPBBahanPenolongReport($condition,  $addCondition, "IMPORT", "PENOLONG", $pageSize, $offset);
        }





        // var_dump($dataPurchaseOrder['result']);
        // die();



        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataAllPurchaseOrderInvoice = [];

        foreach ($dataPurchaseOrder['result'] as $data) {
            if ($data['sub_total'] != 0) {
                array_push($dataAllPurchaseOrderInvoice, [

                    "no"                => $no++,
                    "po_no"     => $data['po_no'],
                    "po_date"     => $data['po_date'],
                    "nama_supplier"     => $data['nama_supplier'],
                    "kode_barang"     => $data['kode_barang'],
                    "nama_barang"     => $data['nama_barang'],
                    "satuan"     => $data['satuan'],
                    "spesifikasi_name"     => $data['spesifikasi_name'],
                    "jml_order"     => number_format($data['jml_order']),
                    "jml_diterima_lpb"     => number_format($data['jml_diterima_lpb']),
                    "sisa_total"     => number_format($data['sisa_total']),
                    "sub_total"     => number_format($data['sub_total']),


                ]);
            }
        }



        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataPurchaseOrder['totalData'],
            "recordsFiltered" => $dataPurchaseOrder['totalFilteredData'],
            'data'      => $dataAllPurchaseOrderInvoice,
            "payload" => $payload,

        ];

        echo json_encode($data);
        return;
    }

    public function exportPDFLaporanPurchaseOrder()
    {



        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),

        ];

        $po_type        = $this->request->getGet("filter_po_type");

        if (empty($po_type)) {
            $condition = [
                'rm_purchase_orders.deletedAt' => null,
                'rm_purchase_order_details.deletedAt' => null,
                'rm_purchase_orders.is_posted' => '1',
                'rm_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->rmPurchaseOrderDetailModel->getListLPBBahanBakuReportPDF($condition,  $addCondition, "LOKAL", "BAKU");
        } elseif ($po_type == "PO LOKAL BB") {
            $condition = [
                'rm_purchase_orders.deletedAt' => null,
                'rm_purchase_order_details.deletedAt' => null,
                'rm_purchase_orders.is_posted' => '1',
                'rm_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->rmPurchaseOrderDetailModel->getListLPBBahanBakuReportPDF($condition,  $addCondition, "LOKAL", "BAKU");
        } elseif ($po_type == "PO LOKAL BP") {
            $condition = [
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_orders.is_posted' => '1',
                'am_purchase_orders.po_type' => 'Lokal',
                'am_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->amPurchaseOrderDetailModel->getListLPBBahanPenolongReportPDF($condition,  $addCondition, "LOKAL", "PENOLONG");
        } elseif ($po_type == "PO IMPOR BB") {
            $condition = [
                'rm_import_pos.deletedAt' => null,
                'rm_import_po_details.deletedAt' => null,
                'rm_import_pos.is_posted' => '1',
                'rm_import_pos.company_id' => $this->this_company_id
            ];


            $dataPurchaseOrder = $this->rmImportPODetailModel->getListLPBBahanBakuReportPDF($condition,  $addCondition, "IMPORT", "BAKU");
        } elseif ($po_type == "PO IMPOR BP") {
            $condition = [
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_orders.is_posted' => '1',
                'am_purchase_orders.po_type' => 'Import',
                'am_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->amPurchaseOrderDetailModel->getListLPBBahanPenolongReportPDF($condition,  $addCondition, "IMPORT", "PENOLONG");
        }


        $no =  1;
        $dataAllPurchaseOrderInvoice = [];

        foreach ($dataPurchaseOrder['result'] as $data) {

            array_push($dataAllPurchaseOrderInvoice, [
                "no"                => $no++,
                "po_no"     => $data['po_no'],
                "po_date"     => $data['po_date'],
                "nama_supplier"     => $data['nama_supplier'],
                "kode_barang"     => $data['kode_barang'],
                "nama_barang"     => $data['nama_barang'],
                "satuan"     => $data['satuan'],
                "spesifikasi_name"     => $data['spesifikasi_name'],
                "jml_order"     => number_format($data['jml_order']),
                "jml_diterima_lpb"     => number_format($data['jml_diterima_lpb']),
                "sisa_total"     => number_format($data['sisa_total']),
                "sub_total"     => number_format($data['sub_total']),


            ]);
        }

        $data = [
            'no' => 1,
            'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            'po' =>  $po_type,
            'warehouse' => !empty($dataWarehouse) ? $dataWarehouse->warehouse_name : "All",
            'supplier' => !empty($dataSupplier) ? $dataSupplier->name : "ALL",
            'dataAllSalesOrderInvoice' => $dataAllPurchaseOrderInvoice,

        ];

        $domPdf = new Dompdf();

        $fileName = 'Laporan Purchase Order';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/Warehouse/LaporanPurchaseOrder/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanPenerimaanBarang()
    {
        return view('Laporan/Warehouse/LaporanPenerimaanBarang/index');
    }

    public function allLaporanPenerimaanBarang()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];


        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "company_id"        => $this->this_company_id,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),

        ];

        $filter_bc_type        = $this->request->getGet("filter_bc_type");

        if ($filter_bc_type  == "BC 2.3") {
            $bcPurchaseOrderBc23 = $this->bcPurchaseOrderModel->getPenerimaanBarangListReportBc23($addCondition, $pageSize, $offset);


            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
            $dataAllPenerimaanBarang = [];

            foreach ($bcPurchaseOrderBc23['data'] as $data) {

                $bcPenerimaanBarangIDArr = json_decode($data->multiple_lpb_id);
                $bcPurchaseOrderIDArr = json_decode($data->multiple_po_id);



                if ($data->po_type == "IMPORT BAKU") {
                    // PO IMPORT BAHAN BAKU
                    $po = $this->penerimaanBarangModel->asObject()
                        ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                   
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    rm_import_pos.po_no,
                    rm_import_pos.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi
                ')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->join('rm_import_pos', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
                        ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                        ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                        ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
                        ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                        // ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                        // ->where('penerimaan_barang.tipe_bahan', "BAKU")
                        // ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                        ->where('penerimaan_barang.bc_type', '48')
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                        ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                        // ->groupBy('barang_id')
                        // ->groupBy('penerimaan_barang_id')
                        ->first();
                } elseif ($data->po_type == "IMPORT PENOLONG") {
                    // PO IMPORT BAHAN PENOLONG
                    $po = $this->penerimaanBarangModel->asObject()
                        ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                     penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                   
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    am_purchase_orders.po_no,
                    am_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi
                ')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                        ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                        ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                        ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
                        ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                        // ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                        // ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                        ->where('penerimaan_barang.bc_type', '48')
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                        ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                        // ->groupBy('barang_id')
                        // ->groupBy('penerimaan_barang_id')
                        ->first();
                }




                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "BC 2.3",
                    "tanggal_bc"         => date('Y-m-d', strtotime($data->updatedAt)),
                    "no_daftar"    => $data->no_daftar,
                    "no_aju"    => $data->no_aju,
                    "no_penerimaan_barang"    => $po->no_penerimaan_barang,
                    "tanggal_lpb"    => $po->lpb_date,
                    "po_no"    => $po->po_no,
                    "po_date"    => $po->po_date,
                    "divisi"    => $po->divisi,
                    "kode_barang"       => $po->kode_barang,
                    "nama_barang_dok"    => $po->nama_barang_dok,
                    "kode_satuan"    => $po->kode_satuan,
                    "qty"    => $po->qty,
                    "jml_masuk"    => $po->jml_masuk,

                ]);
            }

            if ($addCondition['dateStart'] && $addCondition['dateEnd']) {
                $filteredData = array_filter($dataAllPenerimaanBarang, function ($item) use ($addCondition) {
                    $tanggal_bc = date('Y-m-d', strtotime($item['tanggal_bc']));
                    // var_dump($tanggal_bc, $addCondition['dateStart'], $addCondition['dateEnd']);
                    // die();
                    return $tanggal_bc >= $addCondition['dateStart'] && $tanggal_bc <= $addCondition['dateEnd'];
                });
            } else {
                $filteredData = $dataAllPenerimaanBarang;
            }


            $data = [
                "draw"            => intval($this->request->getGet("draw")),
                "recordsTotal"    => $bcPurchaseOrderBc23['totalData'],
                "recordsFiltered" => count($filteredData),
                'data'      => $filteredData,
                "payload" => $payload,

            ];

            echo json_encode($data);
            return;
        } elseif ($filter_bc_type  == "BC 4.0") {
            $bcPurchaseOrderBc40 = $this->bcPurchaseOrderModel->getPenerimaanBarangListReportBc40($addCondition, $pageSize, $offset);


            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
            $dataAllPenerimaanBarang = [];

            foreach ($bcPurchaseOrderBc40['data'] as $data) {

                $bcPenerimaanBarangIDArr = json_decode($data->multiple_lpb_id);
                $bcPurchaseOrderIDArr = json_decode($data->multiple_po_id);



                if ($data->po_type == "LOKAL BAKU") {
                    // PO LOKAL BAHAN BAKU
                    $po = $this->penerimaanBarangModel->asObject()
                        ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                   
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    rm_purchase_orders.po_no,
                    rm_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi
                ')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id', 'left')
                        ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                        ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                        ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
                        ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                        // ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                        // ->where('penerimaan_barang.tipe_bahan', "BAKU")
                        // ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                        ->where('penerimaan_barang.bc_type', '53')
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                        ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                        // ->groupBy('barang_id')
                        // ->groupBy('penerimaan_barang_id')
                        ->first();
                } elseif ($data->po_type == "LOKAL PENOLONG") {
                    // PO LOKAL BAHAN PENOLONG
                    $po = $this->penerimaanBarangModel->asObject()
                        ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                     penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                   
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    am_purchase_orders.po_no,
                    am_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi
                ')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                        ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                        ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                        ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
                        ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                        // ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                        // ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                        ->where('penerimaan_barang.bc_type', '53')
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                        ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                        // ->groupBy('barang_id')
                        // ->groupBy('penerimaan_barang_id')
                        ->first();
                }




                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "BC 4.0",
                    "tanggal_bc"         =>  date('Y-m-d', strtotime($data->updatedAt)),
                    "no_daftar"    => $data->no_daftar,
                    "no_aju"    => $data->no_aju,
                    "no_penerimaan_barang"    => $po->no_penerimaan_barang,
                    "tanggal_lpb"    => $po->lpb_date,
                    "po_no"    => $po->po_no,
                    "po_date"    => $po->po_date,
                    "divisi"    => $po->divisi,
                    "kode_barang"       => $po->kode_barang,
                    "nama_barang_dok"    => $po->nama_barang_dok,
                    "kode_satuan"    => $po->kode_satuan,
                    "qty"    => $po->qty,
                    "jml_masuk"    => $po->jml_masuk,

                ]);
            }

            if ($addCondition['dateStart'] && $addCondition['dateEnd']) {
                $filteredData = array_filter($dataAllPenerimaanBarang, function ($item) use ($addCondition) {
                    $tanggal_bc = date('Y-m-d', strtotime($item['tanggal_bc']));
                    // var_dump($tanggal_bc, $addCondition['dateStart'], $addCondition['dateEnd']);
                    // die();
                    return $tanggal_bc >= $addCondition['dateStart'] && $tanggal_bc <= $addCondition['dateEnd'];
                });
            } else {
                $filteredData = $dataAllPenerimaanBarang;
            }


            $data = [
                "draw"            => intval($this->request->getGet("draw")),
                "recordsTotal"    => $bcPurchaseOrderBc40['totalData'],
                "recordsFiltered" => count($filteredData),
                'data'      => $filteredData,
                "payload" => $payload,

            ];

            echo json_encode($data);
            return;
        } elseif ($filter_bc_type  == "Non Pabean") {
            $penerimaanBarang = $this->bcPurchaseOrderModel->getPenerimaanBarangListReportNoPabean($addCondition, $pageSize, $offset);

            // var_dump($penerimaanBarang['data']);
            // die();

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
            $dataAllPenerimaanBarang = [];

            foreach ($penerimaanBarang['data'] as $data) {

                $po_id_array = json_decode($data->multiple_po_id);



                if (($data->status_penerimaan == "LOKAL") && ($data->tipe_bahan == "BAKU")) {

                    $po = $this->rmPurchaseOrderModel->asObject()
                        ->select('
                            rm_purchase_orders.po_no ,
                            rm_purchase_orders.po_date 
                        ')
                        ->whereIn('rm_purchase_orders.id', $po_id_array)
                        ->first();
                } elseif (($data->status_penerimaan == "LOKAL") && ($data->tipe_bahan == "PENOLONG")) {
                    $po = $this->amPurchaseOrderModel->asObject()
                        ->select('
                            am_purchase_orders.po_no ,
                            am_purchase_orders.po_date 
                        ')
                        ->whereIn('am_purchase_orders.id', $po_id_array)
                        ->first();
                } elseif (($data->status_penerimaan == "IMPORT") && ($data->tipe_bahan == "BAKU")) {
                    $po = $this->rmImportPOModel->asObject()
                        ->select('
                            rm_import_pos.po_no ,
                            rm_import_pos.po_date 
                        ')
                        ->whereIn('rm_import_pos.id', $po_id_array)
                        ->first();
                } elseif (($data->status_penerimaan == "IMPORT") && ($data->tipe_bahan == "PENOLONG")) {
                    $po = $this->amPurchaseOrderModel->asObject()
                        ->select('
                            am_purchase_orders.po_no ,
                            am_purchase_orders.po_date 
                        ')
                        ->whereIn('am_purchase_orders.id', $po_id_array)
                        ->first();
                }




                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "Non Pabean",
                    "tanggal_bc"         =>  $data->lpb_date,
                    "no_daftar"    => "-",
                    "no_aju"    => "-",
                    "no_penerimaan_barang"    => $data->no_penerimaan_barang,
                    "tanggal_lpb"    => $data->lpb_date,
                    "po_no"    => !empty($po->po_no) ? $po->po_no : "",
                    "po_date"    => !empty($po->po_date) ? $po->po_date : "",
                    "divisi"    => $data->divisi,
                    "kode_barang"       => $data->kode_barang,
                    "nama_barang_dok"    => $data->nama_barang_dok,
                    "kode_satuan"    => $data->kode_satuan,
                    "qty"    => $data->qty,
                    "jml_masuk"    => $data->jml_masuk,

                ]);
            }




            $data = [
                "draw"            => intval($this->request->getGet("draw")),
                "recordsTotal"    => $penerimaanBarang['totalData'],
                "recordsFiltered" => $penerimaanBarang['totalFilteredData'],
                'data'      => $dataAllPenerimaanBarang,
                "payload" => $payload,

            ];

            echo json_encode($data);
            return;
        } elseif ($filter_bc_type  == "BC 2.7") {
            $penerimaanBarang = $this->penerimaanMutasiGlobalModel->getPenerimaanBarangListReportBc27($addCondition, $pageSize, $offset);

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
            $dataAllPenerimaanBarang = [];

            // var_dump($penerimaanBarang['data']);
            // die();

            foreach ($penerimaanBarang['data'] as $data) {


                if ($data->tipe_barang == 'kemasan') {
                    // Kemasan
                    $kemasan = $this->kemasanModel->find($data->kemasan_id);
                    $satuan = $this->satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                    $barang = $kemasan['name'];
                    $kodeBarang = $kemasan['kode'];
                } else {
                    // Barang
                    $barangSpesifikasi = $this->barangMasterModel
                        ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                        ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                        ->where('barang_master_spesifikasi.id', $data->barang2_id)
                        ->where('barang_master_spesifikasi.barang_master_id', $data->barang1_id)
                        ->first();
                    $satuan = $this->satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                    $barang = $barangSpesifikasi['barang'];
                    $kodeBarang = $barangSpesifikasi['kode_barang'];
                }

                $stockListDetailAsal = $this->stockDetail2Model->getStockListDetail(
                    $data->stock_id_asal,
                    $data->bc_id_asal,
                    $data->no_aju_asal,
                    $data->stock_dokumen_asal,
                );

                $getRmPo = $this->rmPurchaseOrderModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();
                $getAmPo = $this->amPurchaseOrderModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();
                $getRmImportPo = $this->rmImportPOModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();

                if (!empty($getRmPo)) {
                    $po_date = $getRmPo['po_date'];
                } elseif (!empty($getAmPo)) {
                    $po_date = $getAmPo['po_date'];
                } elseif (!empty($getRmImportPo)) {
                    $po_date = $getRmImportPo['po_date'];
                }


                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "BC 2.7",
                    "tanggal_bc"         =>  $data->tanggal_bc,
                    "no_daftar"    => $data->no_daftar,
                    "no_aju"    => $data->no_aju,
                    "no_penerimaan_barang"    => $data->penerimaan_mutasi_no,
                    "tanggal_lpb"    => $data->tanggal,
                    "po_no"    => !empty($stockListDetailAsal['no_po']) ? $stockListDetailAsal['no_po'] : "",
                    "po_date"    => !empty($po_date) ? $po_date  : "",
                    "divisi"    => $data->divisi_penerima,
                    "kode_barang"       => $kodeBarang,
                    "nama_barang_dok"    => $barang,
                    "kode_satuan"    => $satuan,
                    "qty"    => !empty($data->qty) ? $data->qty : 0,
                    "jml_masuk"    => $data->jml_masuk,

                ]);
            }

            $data = [
                "draw"            => intval($this->request->getGet("draw")),
                "recordsTotal"    => $penerimaanBarang['totalData'],
                "recordsFiltered" => $penerimaanBarang['totalFilteredData'],
                'data'      => $dataAllPenerimaanBarang,
                "payload" => $payload,

            ];

            echo json_encode($data);
            return;
        } elseif ($filter_bc_type  == "PPB KB") {
            $penerimaanBarang = $this->penerimaanMutasiModel->getPenerimaanBarangListReportPPBKB($addCondition, $pageSize, $offset);

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
            $dataAllPenerimaanBarang = [];

            // var_dump($penerimaanBarang['data']);
            // die();

            foreach ($penerimaanBarang['data'] as $data) {


                if ($data->tipe_barang == 'kemasan') {
                    // Kemasan
                    $kemasan = $this->kemasanModel->find($data->kemasan_id);
                    $satuan = $this->satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                    $barang = $kemasan['name'];
                    $kodeBarang = $kemasan['kode'];
                } else {
                    // Barang
                    $barangSpesifikasi = $this->barangMasterModel
                        ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                        ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                        ->where('barang_master_spesifikasi.id', $data->barang2_id)
                        ->where('barang_master_spesifikasi.barang_master_id', $data->barang1_id)
                        ->first();
                    $satuan = $this->satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                    $barang = $barangSpesifikasi['barang'];
                    $kodeBarang = $barangSpesifikasi['kode_barang'];
                }

                $stockListDetailAsal = $this->stockDetail2Model->getStockListDetail(
                    $data->stock_id_asal,
                    $data->bc_id_asal,
                    $data->no_aju_asal,
                    $data->stock_dokumen_asal,
                );

                $getRmPo = $this->rmPurchaseOrderModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();
                $getAmPo = $this->amPurchaseOrderModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();
                $getRmImportPo = $this->rmImportPOModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();

                if (!empty($getRmPo)) {
                    $po_date = $getRmPo['po_date'];
                } elseif (!empty($getAmPo)) {
                    $po_date = $getAmPo['po_date'];
                } elseif (!empty($getRmImportPo)) {
                    $po_date = $getRmImportPo['po_date'];
                }


                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "PPB KB",
                    "tanggal_bc"         =>  $data->tanggal_bc,
                    "no_daftar"    => $data->no_daftar,
                    "no_aju"    => $data->no_ppbkb,
                    "no_penerimaan_barang"    => $data->penerimaan_mutasi_no,
                    "tanggal_lpb"    => $data->tanggal,
                    "po_no"    => !empty($stockListDetailAsal['no_po']) ? $stockListDetailAsal['no_po'] : "",
                    "po_date"    => !empty($po_date) ? $po_date  : "",
                    "divisi"    => $data->divisi_penerima,
                    "kode_barang"       => $kodeBarang,
                    "nama_barang_dok"    => $barang,
                    "kode_satuan"    => $satuan,
                    "qty"    => !empty($data->qty) ? $data->qty : 0,
                    "jml_masuk"    => $data->jml_masuk,

                ]);
            }

            $data = [
                "draw"            => intval($this->request->getGet("draw")),
                "recordsTotal"    => $penerimaanBarang['totalData'],
                "recordsFiltered" => $penerimaanBarang['totalFilteredData'],
                'data'      => $dataAllPenerimaanBarang,
                "payload" => $payload,

            ];

            echo json_encode($data);
            return;
        }
    }

    public function exportPDFLaporanPenerimaanBarang()
    {

        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "company_id"        => $this->this_company_id,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),

        ];

        $filter_bc_type        = $this->request->getGet("filter_bc_type");

        if ($filter_bc_type  == "BC 2.3") {
            $bcPurchaseOrderBc23 = $this->bcPurchaseOrderModel->getPenerimaanBarangListReportBc23($addCondition);


            $no = 1;
            $dataAllPenerimaanBarang = [];

            foreach ($bcPurchaseOrderBc23['data'] as $data) {

                $bcPenerimaanBarangIDArr = json_decode($data->multiple_lpb_id);
                $bcPurchaseOrderIDArr = json_decode($data->multiple_po_id);



                if ($data->po_type == "IMPORT BAKU") {
                    // PO IMPORT BAHAN BAKU
                    $po = $this->penerimaanBarangModel->asObject()
                        ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                   
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    rm_import_pos.po_no,
                    rm_import_pos.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi
                ')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->join('rm_import_pos', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
                        ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                        ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                        ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
                        ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                        // ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                        // ->where('penerimaan_barang.tipe_bahan', "BAKU")
                        // ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                        ->where('penerimaan_barang.bc_type', '48')
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                        ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                        // ->groupBy('barang_id')
                        // ->groupBy('penerimaan_barang_id')
                        ->first();
                } elseif ($data->po_type == "IMPORT PENOLONG") {
                    // PO IMPORT BAHAN PENOLONG
                    $po = $this->penerimaanBarangModel->asObject()
                        ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                     penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                   
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    am_purchase_orders.po_no,
                    am_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi
                ')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                        ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                        ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                        ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
                        ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                        // ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                        // ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                        ->where('penerimaan_barang.bc_type', '48')
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                        ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                        // ->groupBy('barang_id')
                        // ->groupBy('penerimaan_barang_id')
                        ->first();
                }




                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "BC 2.3",
                    "tanggal_bc"         => date('Y-m-d', strtotime($data->updatedAt)),
                    "no_daftar"    => $data->no_daftar,
                    "no_aju"    => $data->no_aju,
                    "no_penerimaan_barang"    => $po->no_penerimaan_barang,
                    "tanggal_lpb"    => $po->lpb_date,
                    "po_no"    => $po->po_no,
                    "po_date"    => $po->po_date,
                    "divisi"    => $po->divisi,
                    "kode_barang"       => $po->kode_barang,
                    "nama_barang_dok"    => $po->nama_barang_dok,
                    "kode_satuan"    => $po->kode_satuan,
                    "qty"    => $po->qty,
                    "jml_masuk"    => $po->jml_masuk,

                ]);
            }

            if ($addCondition['dateStart'] && $addCondition['dateEnd']) {
                $filteredData = array_filter($dataAllPenerimaanBarang, function ($item) use ($addCondition) {
                    $tanggal_bc = date('Y-m-d', strtotime($item['tanggal_bc']));
                    // var_dump($tanggal_bc, $addCondition['dateStart'], $addCondition['dateEnd']);
                    // die();
                    return $tanggal_bc >= $addCondition['dateStart'] && $tanggal_bc <= $addCondition['dateEnd'];
                });
            } else {
                $filteredData = $dataAllPenerimaanBarang;
            }


            $data = [

                'dataAllPenerimaanBarang' => $filteredData,
                'no' => 1,
                'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
                'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
                'filter_bc_type' =>  $this->request->getGet("filter_bc_type"),

            ];
        } elseif ($filter_bc_type  == "BC 4.0") {
            $bcPurchaseOrderBc40 = $this->bcPurchaseOrderModel->getPenerimaanBarangListReportBc40($addCondition);


            $no = 1;
            $dataAllPenerimaanBarang = [];

            foreach ($bcPurchaseOrderBc40['data'] as $data) {

                $bcPenerimaanBarangIDArr = json_decode($data->multiple_lpb_id);
                $bcPurchaseOrderIDArr = json_decode($data->multiple_po_id);



                if ($data->po_type == "LOKAL BAKU") {
                    // PO LOKAL BAHAN BAKU
                    $po = $this->penerimaanBarangModel->asObject()
                        ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                   
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    rm_purchase_orders.po_no,
                    rm_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi
                ')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id', 'left')
                        ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                        ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                        ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
                        ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                        // ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                        // ->where('penerimaan_barang.tipe_bahan', "BAKU")
                        // ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                        ->where('penerimaan_barang.bc_type', '53')
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                        ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                        // ->groupBy('barang_id')
                        // ->groupBy('penerimaan_barang_id')
                        ->first();
                } elseif ($data->po_type == "LOKAL PENOLONG") {
                    // PO LOKAL BAHAN PENOLONG
                    $po = $this->penerimaanBarangModel->asObject()
                        ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                     penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                   
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    am_purchase_orders.po_no,
                    am_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi
                ')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                        ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                        ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                        ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
                        ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                        // ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                        // ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                        ->where('penerimaan_barang.bc_type', '53')
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                        ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                        // ->groupBy('barang_id')
                        // ->groupBy('penerimaan_barang_id')
                        ->first();
                }




                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "BC 4.0",
                    "tanggal_bc"         =>  date('Y-m-d', strtotime($data->updatedAt)),
                    "no_daftar"    => $data->no_daftar,
                    "no_aju"    => $data->no_aju,
                    "no_penerimaan_barang"    => $po->no_penerimaan_barang,
                    "tanggal_lpb"    => $po->lpb_date,
                    "po_no"    => $po->po_no,
                    "po_date"    => $po->po_date,
                    "divisi"    => $po->divisi,
                    "kode_barang"       => $po->kode_barang,
                    "nama_barang_dok"    => $po->nama_barang_dok,
                    "kode_satuan"    => $po->kode_satuan,
                    "qty"    => $po->qty,
                    "jml_masuk"    => $po->jml_masuk,

                ]);
            }

            if ($addCondition['dateStart'] && $addCondition['dateEnd']) {
                $filteredData = array_filter($dataAllPenerimaanBarang, function ($item) use ($addCondition) {
                    $tanggal_bc = date('Y-m-d', strtotime($item['tanggal_bc']));
                    // var_dump($tanggal_bc, $addCondition['dateStart'], $addCondition['dateEnd']);
                    // die();
                    return $tanggal_bc >= $addCondition['dateStart'] && $tanggal_bc <= $addCondition['dateEnd'];
                });
            } else {
                $filteredData = $dataAllPenerimaanBarang;
            }


            $data = [

                'dataAllPenerimaanBarang' => $filteredData,
                'no' => 1,
                'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
                'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
                'filter_bc_type' =>  $this->request->getGet("filter_bc_type"),

            ];
        } elseif ($filter_bc_type  == "Non Pabean") {
            $penerimaanBarang = $this->bcPurchaseOrderModel->getPenerimaanBarangListReportNoPabean($addCondition);

            // var_dump($penerimaanBarang['data']);
            // die();

            $no = 1;
            $dataAllPenerimaanBarang = [];

            foreach ($penerimaanBarang['data'] as $data) {

                $po_id_array = json_decode($data->multiple_po_id);



                if (($data->status_penerimaan == "LOKAL") && ($data->tipe_bahan == "BAKU")) {

                    $po = $this->rmPurchaseOrderModel->asObject()
                        ->select('
                            rm_purchase_orders.po_no ,
                            rm_purchase_orders.po_date 
                        ')
                        ->whereIn('rm_purchase_orders.id', $po_id_array)
                        ->first();
                } elseif (($data->status_penerimaan == "LOKAL") && ($data->tipe_bahan == "PENOLONG")) {
                    $po = $this->amPurchaseOrderModel->asObject()
                        ->select('
                            am_purchase_orders.po_no ,
                            am_purchase_orders.po_date 
                        ')
                        ->whereIn('am_purchase_orders.id', $po_id_array)
                        ->first();
                } elseif (($data->status_penerimaan == "IMPORT") && ($data->tipe_bahan == "BAKU")) {
                    $po = $this->rmImportPOModel->asObject()
                        ->select('
                            rm_import_pos.po_no ,
                            rm_import_pos.po_date 
                        ')
                        ->whereIn('rm_import_pos.id', $po_id_array)
                        ->first();
                } elseif (($data->status_penerimaan == "IMPORT") && ($data->tipe_bahan == "PENOLONG")) {
                    $po = $this->amPurchaseOrderModel->asObject()
                        ->select('
                            am_purchase_orders.po_no ,
                            am_purchase_orders.po_date 
                        ')
                        ->whereIn('am_purchase_orders.id', $po_id_array)
                        ->first();
                }




                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "Non Pabean",
                    "tanggal_bc"         =>  $data->lpb_date,
                    "no_daftar"    => "-",
                    "no_aju"    => "-",
                    "no_penerimaan_barang"    => $data->no_penerimaan_barang,
                    "tanggal_lpb"    => $data->lpb_date,
                    "po_no"    => !empty($po->po_no) ? $po->po_no : "",
                    "po_date"    => !empty($po->po_date) ? $po->po_date : "",
                    "divisi"    => $data->divisi,
                    "kode_barang"       => $data->kode_barang,
                    "nama_barang_dok"    => $data->nama_barang_dok,
                    "kode_satuan"    => $data->kode_satuan,
                    "qty"    => $data->qty,
                    "jml_masuk"    => $data->jml_masuk,

                ]);
            }


            $data = [

                'dataAllPenerimaanBarang' => $dataAllPenerimaanBarang,
                'no' => 1,
                'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
                'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
                'filter_bc_type' =>  $this->request->getGet("filter_bc_type"),

            ];
        } elseif ($filter_bc_type  == "BC 2.7") {
            $penerimaanBarang = $this->penerimaanMutasiGlobalModel->getPenerimaanBarangListReportBc27PDF($addCondition);

            $no = 1;
            $dataAllPenerimaanBarang = [];


            foreach ($penerimaanBarang['data'] as $data) {


                if ($data->tipe_barang == 'kemasan') {
                    // Kemasan
                    $kemasan = $this->kemasanModel->find($data->kemasan_id);
                    $satuan = $this->satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                    $barang = $kemasan['name'];
                    $kodeBarang = $kemasan['kode'];
                } else {
                    // Barang
                    $barangSpesifikasi = $this->barangMasterModel
                        ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                        ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                        ->where('barang_master_spesifikasi.id', $data->barang2_id)
                        ->where('barang_master_spesifikasi.barang_master_id', $data->barang1_id)
                        ->first();
                    $satuan = $this->satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                    $barang = $barangSpesifikasi['barang'];
                    $kodeBarang = $barangSpesifikasi['kode_barang'];
                }

                $stockListDetailAsal = $this->stockDetail2Model->getStockListDetail(
                    $data->stock_id_asal,
                    $data->bc_id_asal,
                    $data->no_aju_asal,
                    $data->stock_dokumen_asal,
                );

                $getRmPo = $this->rmPurchaseOrderModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();
                $getAmPo = $this->amPurchaseOrderModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();
                $getRmImportPo = $this->rmImportPOModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();

                if (!empty($getRmPo)) {
                    $po_date = $getRmPo['po_date'];
                } elseif (!empty($getAmPo)) {
                    $po_date = $getAmPo['po_date'];
                } elseif (!empty($getRmImportPo)) {
                    $po_date = $getRmImportPo['po_date'];
                }


                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "BC 2.7",
                    "tanggal_bc"         =>  date('Y-m-d', strtotime($data->tanggal_bc)),
                    "no_daftar"    => $data->no_daftar,
                    "no_aju"    => $data->no_aju,
                    "no_penerimaan_barang"    => $data->penerimaan_mutasi_no,
                    "tanggal_lpb"    => $data->tanggal,
                    "po_no"    => !empty($stockListDetailAsal['no_po']) ? $stockListDetailAsal['no_po'] : "",
                    "po_date"    => !empty($po_date) ? $po_date  : "",
                    "divisi"    => $data->divisi_penerima,
                    "kode_barang"       => $kodeBarang,
                    "nama_barang_dok"    => $barang,
                    "kode_satuan"    => $satuan,
                    "qty"    => !empty($data->qty) ? $data->qty : 0,
                    "jml_masuk"    => $data->jml_masuk,

                ]);
            }

            $data = [

                'dataAllPenerimaanBarang' => $dataAllPenerimaanBarang,
                'no' => 1,
                'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
                'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
                'filter_bc_type' =>  $this->request->getGet("filter_bc_type"),

            ];
        } elseif ($filter_bc_type  == "PPB KB") {
            $penerimaanBarang =  $penerimaanBarang = $this->penerimaanMutasiModel->getPenerimaanBarangListReportPPBKBPDF($addCondition);

            $no = 1;
            $dataAllPenerimaanBarang = [];


            foreach ($penerimaanBarang['data'] as $data) {


                if ($data->tipe_barang == 'kemasan') {
                    // Kemasan
                    $kemasan = $this->kemasanModel->find($data->kemasan_id);
                    $satuan = $this->satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                    $barang = $kemasan['name'];
                    $kodeBarang = $kemasan['kode'];
                } else {
                    // Barang
                    $barangSpesifikasi = $this->barangMasterModel
                        ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                        ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                        ->where('barang_master_spesifikasi.id', $data->barang2_id)
                        ->where('barang_master_spesifikasi.barang_master_id', $data->barang1_id)
                        ->first();
                    $satuan = $this->satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                    $barang = $barangSpesifikasi['barang'];
                    $kodeBarang = $barangSpesifikasi['kode_barang'];
                }

                $stockListDetailAsal = $this->stockDetail2Model->getStockListDetail(
                    $data->stock_id_asal,
                    $data->bc_id_asal,
                    $data->no_aju_asal,
                    $data->stock_dokumen_asal,
                );

                $getRmPo = $this->rmPurchaseOrderModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();
                $getAmPo = $this->amPurchaseOrderModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();
                $getRmImportPo = $this->rmImportPOModel->select('po_date')->where('po_no', $stockListDetailAsal['no_po'])->first();

                if (!empty($getRmPo)) {
                    $po_date = $getRmPo['po_date'];
                } elseif (!empty($getAmPo)) {
                    $po_date = $getAmPo['po_date'];
                } elseif (!empty($getRmImportPo)) {
                    $po_date = $getRmImportPo['po_date'];
                }


                array_push($dataAllPenerimaanBarang, [
                    "no"                => $no++,
                    "bc_type"     => "PPB KB",
                    "tanggal_bc"         =>  $data->tanggal_bc,
                    "no_daftar"    => $data->no_daftar,
                    "no_aju"    => $data->no_ppbkb,
                    "no_penerimaan_barang"    => $data->penerimaan_mutasi_no,
                    "tanggal_lpb"    => $data->tanggal,
                    "po_no"    => !empty($stockListDetailAsal['no_po']) ? $stockListDetailAsal['no_po'] : "",
                    "po_date"    => !empty($po_date) ? $po_date  : "",
                    "divisi"    => $data->divisi_penerima,
                    "kode_barang"       => $kodeBarang,
                    "nama_barang_dok"    => $barang,
                    "kode_satuan"    => $satuan,
                    "qty"    => !empty($data->qty) ? $data->qty : 0,
                    "jml_masuk"    => $data->jml_masuk,

                ]);
            }

            $data = [

                'dataAllPenerimaanBarang' => $dataAllPenerimaanBarang,
                'no' => 1,
                'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
                'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
                'filter_bc_type' =>  $this->request->getGet("filter_bc_type"),

            ];
        }


        $domPdf = new Dompdf();

        $fileName = 'Laporan Penerimaan Barang';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/Warehouse/LaporanPenerimaanBarang/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }



    public function laporanMaterialRequest()
    {
        return view('Laporan/Warehouse/LaporanMaterialRequest/index');
    }

    public function allLaporanMaterialRequest()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];


        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"        => $this->this_company_id,
            "filter_tipe_barang" => $this->request->getGet("filter_tipe_barang"),
        ];

        $filter_tipe_barang        = $this->request->getGet("filter_tipe_barang");

        if ($filter_tipe_barang == "bahan_penolong") {
            $dataMaterialRequest = $this->materialRequestPenolongModel->getAllMaterialRequestPenolongReport($addCondition, $pageSize, $offset);
        } else {
            $dataMaterialRequest = $this->materialRequestModel->getAllMaterialRequestReport($addCondition, $pageSize, $offset);
        }


        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataAllMaterialRequest = [];

        foreach ($dataMaterialRequest['data'] as $data) {

            if ($data->tipe_barang == 'kemasan') {
                // Kemasan
                $kemasan = $this->kemasanModel->find($data->kemasan_id);
                $satuan = $this->satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                $barang = $kemasan['name'];
                $kodeBarang = $kemasan['kode'];
            } else {
                // Barang
                $barangSpesifikasi = $this->barangMasterModel
                    ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                    ->where('barang_master_spesifikasi.id', $data->barang2_id)
                    ->where('barang_master_spesifikasi.barang_master_id', $data->barang1_id)
                    ->first();
                $satuan = $this->satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                $barang = $barangSpesifikasi['barang'];
                $kodeBarang = $barangSpesifikasi['kode_barang'];
            }

            array_push($dataAllMaterialRequest, [
                "no"                => $no++,
                "wo_no"         => $data->wo_no,
                "req_no"         => $data->req_no,
                "request_date"    => $data->request_date,
                "divisi"    => $data->divisi,
                "kode_barang"    => $kodeBarang,
                "nama_barang"    => $data->nama_barang,
                "qty2"    => $data->qty2,
                "satuan"    => $data->satuan,
                "ref_no"    => $data->ref_no,

            ]);
        }



        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataMaterialRequest['totalData'],
            "recordsFiltered" => $dataMaterialRequest['totalFilteredData'],
            'data'      => $dataAllMaterialRequest,
            "payload" => $payload,

        ];

        echo json_encode($data);
        return;
    }

    public function exportPDFLaporanMaterialRequest()
    {

        $addCondition = [
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"        => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"        => $this->this_company_id,
            "filter_tipe_barang" => $this->request->getGet("filter_tipe_barang"),
        ];


        $filter_tipe_barang        = $this->request->getGet("filter_tipe_barang");

        if ($filter_tipe_barang == "bahan_penolong") {
            $dataMaterialRequest = $this->materialRequestPenolongModel->getAllMaterialRequestPenolongReport($addCondition);
        } else {
            $dataMaterialRequest = $this->materialRequestModel->getAllMaterialRequestReport($addCondition);
        }


        $no =  1;
        $dataAllMaterialRequest = [];

        foreach ($dataMaterialRequest['data'] as $data) {

            if ($data->tipe_barang == 'kemasan') {
                // Kemasan
                $kemasan = $this->kemasanModel->find($data->kemasan_id);
                $satuan = $this->satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                $barang = $kemasan['name'];
                $kodeBarang = $kemasan['kode'];
            } else {
                // Barang
                $barangSpesifikasi = $this->barangMasterModel
                    ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                    ->where('barang_master_spesifikasi.id', $data->barang2_id)
                    ->where('barang_master_spesifikasi.barang_master_id', $data->barang1_id)
                    ->first();
                $satuan = $this->satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                $barang = $barangSpesifikasi['barang'];
                $kodeBarang = $barangSpesifikasi['kode_barang'];
            }

            array_push($dataAllMaterialRequest, [
                "no"                => $no++,
                "wo_no"         => $data->wo_no,
                "req_no"         => $data->req_no,
                "request_date"    => $data->request_date,
                "divisi"    => $data->divisi,
                "kode_barang"    => $kodeBarang,
                "nama_barang"    => $data->nama_barang,
                "qty2"    => $data->qty2,
                "satuan"    => $data->satuan,
                "ref_no"    => $data->ref_no,

            ]);
        }


        $data = [
            'no' => 1,
            'tanggalAwal' => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            'tanggalAkhir' => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            'dataAllMaterialRequest'      => $dataAllMaterialRequest,
            'filter_tipe_barang' =>  $this->request->getGet("filter_tipe_barang"),
        ];




        $domPdf = new Dompdf();

        $fileName = 'Laporan Material Request Ekspor';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/Warehouse/LaporanMaterialRequest/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanKartuStock()
    {
        $selectQry = '
            suppliers.name AS supplier_name,
            warehouses.warehouse_name,
            divisis.divisi,
            stock.tipe_barang,
            stock.barang1_id,
            stock.barang2_id,
            stock.kemasan_id,
            stock.divisi_id,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            stock_details2.stock_dokumen,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_po,
            stock_details.no_dokumen AS no_dokumen_1,
            stock_details.stock_date,
            stock_details.sumber,
                (SUM(CASE WHEN stock_details.status = "In" 
                THEN stock_details2.qty ELSE 0 END) - 
                SUM(CASE WHEN stock_details.status = "Out" 
                THEN stock_details2.qty ELSE 0 END)) 
                AS stok_total';

        $dataQry = $this->stockDetail2Model->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id')
            ->join('divisis', 'divisis.id = stock.divisi_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('kemasan', 'kemasan.id = stock.kemasan_id', 'left')
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.stock_id')
            ->groupBy('stock_details2.no_aju')
            ->where('stock.company_id', $this->this_company_id)
            ->having('stok_total >', 0)
            ->findAll();
        $divisiName = [];
        $supplier = [];
        $warehouse = [];
        $tipe_barang = [];
        foreach ($dataQry as $d) {
            array_push($divisiName, $d['divisi']);
            array_push($supplier, $d['supplier_name']);
            array_push($warehouse, $d['warehouse_name']);
            array_push($tipe_barang, $d['tipe_barang']);
        }


        $data = [
            'divisi' => array_unique($divisiName),
            'supplier' => array_unique($supplier),
            'warehouse' => array_unique($warehouse),
            'tipe_barang' => array_unique($tipe_barang)
        ];
        return view('Laporan/Warehouse/LaporanKartuStock/index', $data);
    }

    public function AlllaporanKartuStock()
    {
        $pageSize = intval($this->request->getVar("length"));
        $currentPage = (intval($this->request->getVar("start")) / $pageSize) + 1;
        $offset = ($currentPage - 1) * $pageSize;


        $payload = [
            "pageSize"    => $pageSize,
            "currentPage" => $currentPage,
            "search"      => $this->request->getGet("search"),
            "sort"        => $this->request->getGet("sort"),
            "sortType"    => $this->request->getGet("sortType"),
            "dateStart"   => $this->request->getGet("dateStart"),
            "dateEnd"     => $this->request->getGet("dateEnd")
        ];

        $addCondition = [
            "dateStart"  => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"    => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            'tipeBarang' => $this->request->getVar('tipeBarang'),
            'divisi'     => $this->request->getVar('divisi')  == "null" ? NULL :  $this->request->getVar('divisi'),
            'warehouse'   => $this->request->getVar('warehouse') == "null" ? NULL : $this->request->getVar('warehouse'),
            'search'     => $this->request->getVar('search') == "null" ? NULL :  $this->request->getVar('search'),
            "sort"       => $this->request->getGet("sort"),
            "sortType"   => $this->request->getGet("sortType"),
            "company_id" => $this->this_company_id,
        ];

        $availableSort = [
            'tipe_barang'           => 'stock.tipe_barang',
            'department'            => 'divisis.divisi',
            'warehouse'             => 'warehouses.warehouse_name',
            'supplier'              => 'suppliers.name',
            'tanggal_penerimaan'    => 'stock_details.stock_date',
            'no_dok'                => 'stock_details2.stock_dokumen'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_details2.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';



        $no = ($pageSize * ($currentPage - 1)) + 1;

        $selectQry = '
            suppliers.name AS supplier_name,
            warehouses.warehouse_name,
            divisis.divisi,
            stock.tipe_barang,
            stock.barang1_id,
            stock.barang2_id,
            stock.kemasan_id,
            stock.divisi_id,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            stock_details2.stock_dokumen,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_po,
            stock_details.no_dokumen AS no_dokumen_1,
            stock_details.stock_date,
            stock_details.sumber,
                (SUM(CASE WHEN stock_details.status = "In" 
                THEN stock_details2.qty ELSE 0 END) - 
                SUM(CASE WHEN stock_details.status = "Out" 
                THEN stock_details2.qty ELSE 0 END)) 
                AS stok_total';

        $dataQry = $this->stockDetail2Model->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id')
            ->join('divisis', 'divisis.id = stock.divisi_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('kemasan', 'kemasan.id = stock.kemasan_id', 'left')
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.stock_id')
            ->groupBy('stock_details2.no_aju')
            ->where('stock.company_id', $this->this_company_id)
            ->having('stok_total >', 0)
            ->orderBy($sort, $sortType);

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd']) || !empty($addCondition['tipeBarang']) || !empty($addCondition['divisi']) || !empty($addCondition['warehouse']) || !empty($addCondition['search'])) {
            $dataQry->groupStart();
            if (!empty($addCondition['search'])) {
                $dataQry
                    ->like('barang_master.barang_name', $addCondition['search'])
                    ->orLike('kemasan.name', $addCondition['search'])
                    ->orLike('barang_master.kode_barang', $addCondition['search'])
                    ->orLike('kemasan.kode', $addCondition['search']);
            }
            if (!empty($addCondition['dateStart'])) {
                $dataQry->where('stock_details.stock_date >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $dataQry->where('stock_details.stock_date <=', $addCondition['dateEnd']);
            }
            if (!empty($addCondition['tipeBarang'])) {
                $dataQry->where('stock.tipe_barang', $addCondition['tipeBarang']);
            }
            if (!empty($addCondition['divisi'])) {
                $dataQry->where('divisis.divisi', $addCondition['divisi']);
            }
            if (!empty($addCondition['warehouse'])) {
                $dataQry->where('warehouses.warehouse_name', $addCondition['warehouse']);
            }
            $dataQry->groupEnd();
        }

        $totalFilteredRecords = $dataQry->countAllResults(false);
        $dataResult = $dataQry->findAll($pageSize, $offset);
        $dataAllKartuStock = [];

        foreach ($dataResult as $data) {
            $namaBarang = "";
            $spesifikasi = "-";
            $satuan = "";
            $kodeBarang = "";
            $sumberBCName = "NON PABEAN";

            if (!empty($data['bc_id'])) {
                $sumberBC = $this->MetaDataModel
                    ->where('id', $data['bc_id'])
                    ->where('deletedAt', null)
                    ->first();
                $sumberBCName = $sumberBC['value'];
            }

            if ($data['tipe_barang'] == 'kemasan') {
                $kemasanData = $this->kemasanModel
                    ->select('kemasan.*, satuans.nama_satuan')
                    ->join('satuans', 'satuans.id = kemasan.satuan_id')
                    ->where('kemasan.id', $data['kemasan_id'])
                    ->first();
                $satuan = $kemasanData == null ? "" : $kemasanData['nama_satuan'];
                $namaBarang = $kemasanData['name'];
                $kodeBarang = $kemasanData['kode'];
            } else {
                $barangData = $this->barangMasterModel
                    ->select('barang_master.*')
                    ->where('barang_master.id', $data['barang1_id'])
                    ->first();
                $barangSpesifikasiData = $this->barangMasterSpesifikasiModel
                    ->select('*')
                    ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
                    ->where('barang_master_spesifikasi.id', $data['barang2_id'])
                    ->first();

                $satuan = $barangSpesifikasiData == null ? "" : $barangSpesifikasiData['nama_satuan'];
                $namaBarang = $barangData == null ? "" : $barangData['barang_name'];
                $kodeBarang = $barangData == null ? "" : $barangData['kode_barang'];
                $spesifikasi = $barangSpesifikasiData == null ? "" : $barangSpesifikasiData['spesifikasi'];
            }


            $dataAllKartuStock[] = [
                'no' => $no++,
                'tipe_barang' => strtoupper(str_replace('_', " ", $data['tipe_barang'])),
                'sumber_barang' => $sumberBCName . " / " .  $data['no_aju'] . " / " . $data['sumber'],
                'supplier' => $data['supplier_name'],
                'warehouse_name' => $data['warehouse_name'],
                'tanggal_penerimaan' => date('d/m/Y', strtotime($data['stock_date'])),
                'nomor' => $data['stock_dokumen'],
                'department' => $data['divisi'],
                'kode_barang' => $kodeBarang,
                'nama_barang' => $namaBarang,
                'spesifikasi' => $spesifikasi,
                'satuan' => $satuan,
                'qty' => $data['stok_total']
            ];
        }

        $totalRecords = $this->stockDetail2Model->countAllResults();

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            'data' => $dataAllKartuStock,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFilteredRecords,
            "payload" => $payload,

        ];


        return json_encode($data);
    }

    public function exportSheetLaporanKartuStock()
    {
        $jsonList = $this->AlllaporanKartuStock();
        $list = json_decode($jsonList, true);


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $no = 1;
        $column = 2;


        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $dataStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Tipe Barang')
            ->setCellValue('C1', 'Sumber Barang (BC / No Aju)')
            ->setCellValue('D1', 'Supplier')
            ->setCellValue('E1', 'Warehouse')
            ->setCellValue('F1', 'Tanggal Penerimaan')
            ->setCellValue('G1', 'Nomor')
            ->setCellValue('H1', 'Department')
            ->setCellValue('I1', 'Kode Barang')
            ->setCellValue('J1', 'Nama Barang')
            ->setCellValue('K1', 'Spesifikasi')
            ->setCellValue('L1', 'Satuan')
            ->setCellValue('M1', 'Qty');
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyleArray);
        foreach ($list['data'] as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $l['no'])
                ->setCellValue('B' . $column, $l['tipe_barang'])
                ->setCellValue('C' . $column, $l['sumber_barang'])
                ->setCellValue('D' . $column, $l['supplier'])
                ->setCellValue('E' . $column, $l['warehouse_name'])
                ->setCellValue('F' . $column, $l['tanggal_penerimaan'])
                ->setCellValue('G' . $column, $l['nomor'])
                ->setCellValue('H' . $column, $l['department'])
                ->setCellValue('I' . $column, $l['kode_barang'])
                ->setCellValue('J' . $column, $l['nama_barang'])
                ->setCellValue('K' . $column, $l['spesifikasi'])
                ->setCellValue('L' . $column, $l['satuan'])
                ->setCellValue('M' . $column, $l['qty']);
            $sheet->getStyle('A' . $column . ':M' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        foreach (range('A', 'M') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Kartu-Stock';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
