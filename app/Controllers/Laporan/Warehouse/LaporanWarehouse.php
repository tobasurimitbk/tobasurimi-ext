<?php

namespace App\Controllers\Laporan\Warehouse;

use App\Controllers\BaseController;
use Config\Services;
use Dompdf\Dompdf;

use App\Models\CompaniesModel;
use App\Models\SalesOrderModel;
use App\Models\CustomerModel;
use App\Models\BarangMasterModel;
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
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\RMImportPODetailModel;
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
    protected $amPurchaseOrderDetailModel;
    protected $rmImportPODetailModel;

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
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();

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
}
