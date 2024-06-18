<?php

namespace App\Controllers\Laporan\Supplier;

use Dompdf\Dompdf;

use App\Controllers\BaseController;
use App\Models\CountryModel;
use App\Models\ProvinceModel;
use App\Models\SupplierModel;
use App\Models\SupplierHargaModel;
use App\Models\BarangMasterModel;
use App\Models\BagianModel;
use App\Models\DivisisModel;
use App\Models\WarehousesModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;

class LaporanSupplierLokalBB extends BaseController
{
    protected $this_company_id, $provinceModel, $countryModel, $supplierModel, $supplierHargaModel, $barangMasterModel, $bagianModel;
    protected $RMPurchaseOrderModel;
    protected $RMPurchaseOrderDetailModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $warehousesModel;
    protected $divisiModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->provinceModel = new ProvinceModel();
        $this->countryModel = new CountryModel();
        $this->supplierModel = new SupplierModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->bagianModel = new BagianModel();
        $this->warehousesModel = new WarehousesModel();
        $this->RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->divisiModel = new DivisisModel();
    }

    public function index()
    {

        return view('Laporan/SupplierLokalBB/index/index');
    }

    public function laporanPendapatanSupplier()
    {
        $data = [
            'getPoNo' => $this->RMPurchaseOrderModel->select('id ,po_no')->where('deletedAt', NULL)->where('is_posted', '1')->where('company_id', $this->this_company_id)->findAll(),
            'getSupplier' => $this->supplierModel->where('deletedAt', NULL)->where('type', 'BAHAN BAKU')->findAll(),
            'getWarehouse' => $this->warehousesModel->get_by_company_id($this->this_company_id),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
        ];


        return view('Laporan/SupplierLokalBB/PendapatanSupplier/index', $data);
    }

    public function allLaporanPendapatanSupplier()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $this->this_company_id,
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
            "supplierId"        => $this->request->getGet("filter_supplier"),
            "barangId"        => $this->request->getGet("filter_barang"),
            "warehouseId"        => $this->request->getGet("filter_warehouse"),
            "poNo"        => $this->request->getGet("filter_po_no"),

        ];

        $availableSort = [
            'supplierName'          => 'suppliers.name',
            'poNum'            => 'rm_purchase_orders.po_no',
            'poDate'             => 'rm_purchase_orders.po_date',
            'barangName'             => 'barang_master.barang_name',
            'warehouseName'      => 'warehouses.warehouse_name',
        ];

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;


        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, $pageSize, $offset);
        // $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $addCondition['barangId'])->where('type_barang', 'bahan_baku')->first();
        // $dataWarehouse = $this->warehousesModel->asObject()->where('id', $addCondition['warehouseId'])->first();



        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;  // Add the 'No' field
            $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
            $row->totalUmum     = $row->dppUmum - $row->pphUmum;
            $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
            $row->totalHarian   = $row->dppHarian - $row->pphHarian;
            $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
            $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
            $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
            $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
            $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;

            $totalDppUmum += $row->dppUmum;
            $totalPphUmum += $row->pphUmum;
            $totalTotalUmum += $row->totalUmum;
            $totalDppHarian += $row->dppHarian;
            $totalPphHarian += $row->pphHarian;
            $totalTotalHarian += $row->totalHarian;
            $totalDppBulanan += $row->dppBulanan;
            $totalPphBulanan += $row->pphBulanan;
            $totalTotalBulanan += $row->totalBulanan;
            $totalDppSubsidi += $row->subsidi;
            $totalPphSubsidi += $row->pphSubsidi;
            $totalTotalSubsidi += $row->totalSubsidi;
            $row->pphUmum = number_format($row->pphUmum);
            $row->dppUmum = number_format($row->dppUmum);
            $row->totalUmum = number_format($row->totalUmum);
            $row->dppHarian = number_format($row->dppHarian);
            $row->pphHarian = number_format($row->pphHarian);
            $row->totalHarian = number_format($row->totalHarian);
            $row->dppBulanan = number_format($row->dppBulanan);
            $row->pphBulanan = number_format($row->pphBulanan);
            $row->totalBulanan = number_format($row->totalBulanan);
            $row->subsidi = number_format($row->subsidi);
            $row->pphSubsidi = number_format($row->pphSubsidi);
            $row->totalSubsidi = number_format($row->totalSubsidi);
            $row->totalRow = number_format($row->totalRow);
        }
        $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataBBLokal['totalData'],
            "recordsFiltered" => $dataBBLokal['totalFilteredData'],
            'data'      => $dataBBLokal['data'],
            "payload" => $payload,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        echo json_encode($data);
        return;
    }


    public function exportPDFPendapatanSupplier()
    {

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;

        $dateStart = $this->request->getVar('dateStart');
        $newDateStart = $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "";
        $dateEnd = $this->request->getVar('dateEnd');
        $newDateEnd = $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "";
        $supplierId = $this->request->getVar('filter_supplier');
        $barangId = $this->request->getVar('filter_barang');
        $warehouseId = $this->request->getVar('filter_warehouse');
        $poNo = $this->request->getVar('filter_po_no');
        $companyId  = $this->this_company_id;

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReportPdf($newDateStart, $newDateEnd, $supplierId, $barangId, $warehouseId, $companyId, $poNo);
        $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $barangId)->where('type_barang', 'bahan_baku')->first();
        $dataWarehouse = $this->warehousesModel->asObject()->where('id', $warehouseId)->first();
        $dataSupplier = $this->supplierModel->asObject()->where('id', $supplierId)->first();
        $dataTotalBBLokal = [];
        if (!empty($dataBBLokal)) {
            foreach ($dataBBLokal as $row) {
                $row->pphUmum = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
                $row->totalUmum = $row->dppUmum - $row->pphUmum;
                $row->pphHarian = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
                $row->totalHarian = $row->dppHarian - $row->pphHarian;
                $row->pphBulanan = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
                $row->totalBulanan = $row->dppBulanan - $row->pphBulanan;
                $row->pphSubsidi = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
                $row->totalSubsidi = $row->subsidi - $row->pphSubsidi;
                $row->totalRow = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
                $totalDppUmum += $row->dppUmum;
                $totalPphUmum += $row->pphUmum;
                $totalTotalUmum += $row->totalUmum;
                $totalDppHarian += $row->dppHarian;
                $totalPphHarian += $row->pphHarian;
                $totalTotalHarian += $row->totalHarian;
                $totalDppBulanan += $row->dppBulanan;
                $totalPphBulanan += $row->pphBulanan;
                $totalTotalBulanan += $row->totalBulanan;
                $totalDppSubsidi += $row->subsidi;
                $totalPphSubsidi += $row->pphSubsidi;
                $totalTotalSubsidi += $row->totalSubsidi;
            }
            $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
        }
        $no = 1;

        $data = [
            'no' => $no,
            'header' => "Laporan Pendapatan Supplier",
            'tanggalAwal' => $dateStart,
            'tanggalAkhir' => $dateEnd,
            'bahanBaku' => !empty($dataBahanBaku) ? $dataBahanBaku->barang_name : "All",
            'warehouse' => !empty($dataWarehouse) ? $dataWarehouse->warehouse_name : "All",
            'supplier' => !empty($dataSupplier) ? $dataSupplier->name : "ALL",
            'poNo' => !empty($poNo) ? $poNo : "ALL",
            'dataOrder' => $dataBBLokal,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        $domPdf = new Dompdf();

        $fileName = 'Pendapatan Supplier';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/PendapatanSupplier/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanRincianPerbarang()
    {
        $data = [
            'getSupplier' => $this->supplierModel->where('deletedAt', NULL)->where('type', 'BAHAN BAKU')->findAll(),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
            'getWarehouse' => $this->warehousesModel->get_by_company_id($this->this_company_id),

        ];


        return view('Laporan/SupplierLokalBB/RincianPerbarang/index', $data);
    }

    public function allLaporanRincianPerbarang()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $this->this_company_id,
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
            "supplierId"        => $this->request->getGet("filter_supplier"),
            "barangId"        => $this->request->getGet("filter_barang"),
            "warehouseId"        => $this->request->getGet("filter_warehouse"),

        ];

        $availableSort = [
            'supplierName'          => 'suppliers.name',
            'supplierNpwp'          => 'suppliers.no_npwp',
            'poNum'            => 'rm_purchase_orders.po_no',
            'poDate'             => 'rm_purchase_orders.po_date',
            'barangName'             => 'barang_master.barang_name',
            'spekName'             => 'supplier_harga.spesifikasi',
            'warehouseName'      => 'warehouses.warehouse_name',
        ];

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;


        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, $pageSize, $offset);
        // $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $addCondition['barangId'])->where('type_barang', 'bahan_baku')->first();
        // $dataWarehouse = $this->warehousesModel->asObject()->where('id', $addCondition['warehouseId'])->first();



        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;  // Add the 'No' field
            $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
            $row->totalUmum     = $row->dppUmum - $row->pphUmum;
            $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
            $row->totalHarian   = $row->dppHarian - $row->pphHarian;
            $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
            $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
            $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
            $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
            $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
            $totalDppUmum += $row->dppUmum;
            $totalPphUmum += $row->pphUmum;
            $totalTotalUmum += $row->totalUmum;
            $totalDppHarian += $row->dppHarian;
            $totalPphHarian += $row->pphHarian;
            $totalTotalHarian += $row->totalHarian;
            $totalDppBulanan += $row->dppBulanan;
            $totalPphBulanan += $row->pphBulanan;
            $totalTotalBulanan += $row->totalBulanan;
            $totalDppSubsidi += $row->subsidi;
            $totalPphSubsidi += $row->pphSubsidi;
            $totalTotalSubsidi += $row->totalSubsidi;

            $row->pphUmum = number_format($row->pphUmum);
            $row->dppUmum = number_format($row->dppUmum);
            $row->totalUmum = number_format($row->totalUmum);
            $row->dppHarian = number_format($row->dppHarian);
            $row->pphHarian = number_format($row->pphHarian);
            $row->totalHarian = number_format($row->totalHarian);
            $row->dppBulanan = number_format($row->dppBulanan);
            $row->pphBulanan = number_format($row->pphBulanan);
            $row->totalBulanan = number_format($row->totalBulanan);
            $row->subsidi = number_format($row->subsidi);
            $row->pphSubsidi = number_format($row->pphSubsidi);
            $row->totalSubsidi = number_format($row->totalSubsidi);
            $row->totalRow = number_format($row->totalRow);
        }
        $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataBBLokal['totalData'],
            "recordsFiltered" => $dataBBLokal['totalFilteredData'],
            'data'      => $dataBBLokal['data'],
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        echo json_encode($data);
        return;
    }

    public function exportPDFLaporanRincianPerbarang()
    {

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;

        $dateStart = $this->request->getVar('dateStart');
        $newDateStart = $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "";
        $dateEnd = $this->request->getVar('dateEnd');
        $newDateEnd = $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "";
        $supplierId = $this->request->getVar('filter_supplier');
        $barangId = $this->request->getVar('filter_barang');
        $warehouseId = $this->request->getVar('filter_warehouse');
        $companyId  = $this->this_company_id;

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReportPdf($newDateStart, $newDateEnd, $supplierId, $barangId, $warehouseId, $companyId);
        $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $barangId)->where('type_barang', 'bahan_baku')->first();

        $dataTotalBBLokal = [];
        if (!empty($dataBBLokal)) {
            foreach ($dataBBLokal as $row) {
                $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
                $row->totalUmum     = $row->dppUmum - $row->pphUmum;
                $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
                $row->totalHarian   = $row->dppHarian - $row->pphHarian;
                $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
                $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
                $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
                $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
                $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
                $totalDppUmum += $row->dppUmum;
                $totalPphUmum += $row->pphUmum;
                $totalTotalUmum += $row->totalUmum;
                $totalDppHarian += $row->dppHarian;
                $totalPphHarian += $row->pphHarian;
                $totalTotalHarian += $row->totalHarian;
                $totalDppBulanan += $row->dppBulanan;
                $totalPphBulanan += $row->pphBulanan;
                $totalTotalBulanan += $row->totalBulanan;
                $totalDppSubsidi += $row->subsidi;
                $totalPphSubsidi += $row->pphSubsidi;
                $totalTotalSubsidi += $row->totalSubsidi;
            }
            $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
        }
        $no = 1;

        $data = [
            'no' => $no,
            'header' => "Laporan Rincian Perbarang",
            'tanggalAwal' => $dateStart,
            'tanggalAkhir' => $dateEnd,
            'bahanBaku' => !empty($dataBahanBaku) ? $dataBahanBaku->barang_name : "All",
            'warehouse' => !empty($dataWarehouse) ? $dataWarehouse->warehouse_name : "All",
            'dataOrder' => $dataBBLokal,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        $domPdf = new Dompdf();

        $fileName = 'Rincian Perbarang';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RincianPerbarang/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanRekapAllSupplier()
    {
        $data = [
            'getSupplier' => $this->supplierModel->where('deletedAt', NULL)->where('type', 'BAHAN BAKU')->findAll(),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
        ];


        return view('Laporan/SupplierLokalBB/RekapAllSupplier/index', $data);
    }

    public function allLaporanRekapAllSupplier()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $this->this_company_id,
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
            "supplierId"        => $this->request->getGet("filter_supplier"),
            "barangId"        => $this->request->getGet("filter_barang"),


        ];

        $availableSort = [
            'supplierName'          => 'suppliers.name',
            'barangName'             => 'barang_master.barang_name',

        ];

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;


        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForAllSupplier($availableSort, $condition, $addCondition, $pageSize, $offset);
        // $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $addCondition['barangId'])->where('type_barang', 'bahan_baku')->first();
        // $dataWarehouse = $this->warehousesModel->asObject()->where('id', $addCondition['warehouseId'])->first();



        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;  // Add the 'No' field
            $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
            $row->totalUmum     = $row->dppUmum - $row->pphUmum;
            $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
            $row->totalHarian   = $row->dppHarian - $row->pphHarian;
            $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
            $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
            $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
            $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
            $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
            $totalDppUmum += $row->dppUmum;
            $totalPphUmum += $row->pphUmum;
            $totalTotalUmum += $row->totalUmum;
            $totalDppHarian += $row->dppHarian;
            $totalPphHarian += $row->pphHarian;
            $totalTotalHarian += $row->totalHarian;
            $totalDppBulanan += $row->dppBulanan;
            $totalPphBulanan += $row->pphBulanan;
            $totalTotalBulanan += $row->totalBulanan;
            $totalDppSubsidi += $row->subsidi;
            $totalPphSubsidi += $row->pphSubsidi;
            $totalTotalSubsidi += $row->totalSubsidi;

            $row->pphUmum = number_format($row->pphUmum);
            $row->dppUmum = number_format($row->dppUmum);
            $row->totalUmum = number_format($row->totalUmum);
            $row->dppHarian = number_format($row->dppHarian);
            $row->pphHarian = number_format($row->pphHarian);
            $row->totalHarian = number_format($row->totalHarian);
            $row->dppBulanan = number_format($row->dppBulanan);
            $row->pphBulanan = number_format($row->pphBulanan);
            $row->totalBulanan = number_format($row->totalBulanan);
            $row->subsidi = number_format($row->subsidi);
            $row->pphSubsidi = number_format($row->pphSubsidi);
            $row->totalSubsidi = number_format($row->totalSubsidi);
            $row->totalRow = number_format($row->totalRow);
        }
        $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataBBLokal['totalData'],
            "recordsFiltered" => $dataBBLokal['totalFilteredData'],
            'data'      => $dataBBLokal['data'],
            "payload" => $payload,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        echo json_encode($data);
        return;
    }


    public function exportPDFLaporanRekapAllSupplier()
    {

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;

        $dateStart = $this->request->getVar('dateStart');
        $newDateStart = $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "";
        $dateEnd = $this->request->getVar('dateEnd');
        $newDateEnd = $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "";
        $supplierId = $this->request->getVar('filter_supplier');
        $barangId = $this->request->getVar('filter_barang');

        $companyId  = $this->this_company_id;

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForAllSupplierReportPdf($newDateStart, $newDateEnd, $supplierId, $barangId, $companyId);
        $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $barangId)->where('type_barang', 'bahan_baku')->first();

        $dataTotalBBLokal = [];
        if (!empty($dataBBLokal)) {
            foreach ($dataBBLokal as $row) {
                $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
                $row->totalUmum     = $row->dppUmum - $row->pphUmum;
                $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
                $row->totalHarian   = $row->dppHarian - $row->pphHarian;
                $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
                $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
                $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
                $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
                $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
                $totalDppUmum += $row->dppUmum;
                $totalPphUmum += $row->pphUmum;
                $totalTotalUmum += $row->totalUmum;
                $totalDppHarian += $row->dppHarian;
                $totalPphHarian += $row->pphHarian;
                $totalTotalHarian += $row->totalHarian;
                $totalDppBulanan += $row->dppBulanan;
                $totalPphBulanan += $row->pphBulanan;
                $totalTotalBulanan += $row->totalBulanan;
                $totalDppSubsidi += $row->subsidi;
                $totalPphSubsidi += $row->pphSubsidi;
                $totalTotalSubsidi += $row->totalSubsidi;
            }
            $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
        }
        $no = 1;

        $data = [
            'no' => $no,
            'header' => "Laporan Rekap All Supplier",
            'tanggalAwal' => $dateStart,
            'tanggalAkhir' => $dateEnd,
            'bahanBaku' => !empty($dataBahanBaku) ? $dataBahanBaku->barang_name : "All",
            'dataOrder' => $dataBBLokal,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        $domPdf = new Dompdf();

        $fileName = 'Rekap All Supplier';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapAllSupplier/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanRekapPerSupplier()
    {
        $data = [
            'getSupplier' => $this->supplierModel->where('deletedAt', NULL)->where('type', 'BAHAN BAKU')->findAll(),
            'getWarehouse' => $this->warehousesModel->get_by_company_id($this->this_company_id),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
        ];


        return view('Laporan/SupplierLokalBB/RekapPersupplier/index', $data);
    }

    public function allLaporanRekapPersupplier()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $this->this_company_id,
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
            "supplierId"        => $this->request->getGet("filter_supplier"),
            "barangId"        => $this->request->getGet("filter_barang"),
            "warehouseId"        => $this->request->getGet("filter_warehouse"),

        ];

        $availableSort = [
            'supplierName'          => 'suppliers.name',
            'poNum'            => 'rm_purchase_orders.po_no',
            'poDate'             => 'rm_purchase_orders.po_date',
            'barangName'             => 'barang_master.barang_name',
            'warehouseName'      => 'warehouses.warehouse_name',
        ];

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;


        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, $pageSize, $offset);
        // $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $addCondition['barangId'])->where('type_barang', 'bahan_baku')->first();
        // $dataWarehouse = $this->warehousesModel->asObject()->where('id', $addCondition['warehouseId'])->first();



        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;  // Add the 'No' field
            $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
            $row->totalUmum     = $row->dppUmum - $row->pphUmum;
            $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
            $row->totalHarian   = $row->dppHarian - $row->pphHarian;
            $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
            $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
            $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
            $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
            $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
            $totalDppUmum += $row->dppUmum;
            $totalPphUmum += $row->pphUmum;
            $totalTotalUmum += $row->totalUmum;
            $totalDppHarian += $row->dppHarian;
            $totalPphHarian += $row->pphHarian;
            $totalTotalHarian += $row->totalHarian;
            $totalDppBulanan += $row->dppBulanan;
            $totalPphBulanan += $row->pphBulanan;
            $totalTotalBulanan += $row->totalBulanan;
            $totalDppSubsidi += $row->subsidi;
            $totalPphSubsidi += $row->pphSubsidi;
            $totalTotalSubsidi += $row->totalSubsidi;

            $row->pphUmum = number_format($row->pphUmum);
            $row->dppUmum = number_format($row->dppUmum);
            $row->totalUmum = number_format($row->totalUmum);
            $row->dppHarian = number_format($row->dppHarian);
            $row->pphHarian = number_format($row->pphHarian);
            $row->totalHarian = number_format($row->totalHarian);
            $row->dppBulanan = number_format($row->dppBulanan);
            $row->pphBulanan = number_format($row->pphBulanan);
            $row->totalBulanan = number_format($row->totalBulanan);
            $row->subsidi = number_format($row->subsidi);
            $row->pphSubsidi = number_format($row->pphSubsidi);
            $row->totalSubsidi = number_format($row->totalSubsidi);
            $row->totalRow = number_format($row->totalRow);
        }
        $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataBBLokal['totalData'],
            "recordsFiltered" => $dataBBLokal['totalFilteredData'],
            'data'      => $dataBBLokal['data'],
            "payload" => $payload,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        echo json_encode($data);
        return;
    }


    public function exportPDFLaporanRekapPersupplier()
    {

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;

        $dateStart = $this->request->getVar('dateStart');
        $newDateStart = $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "";
        $dateEnd = $this->request->getVar('dateEnd');
        $newDateEnd = $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "";
        $supplierId = $this->request->getVar('filter_supplier');
        $barangId = $this->request->getVar('filter_barang');
        $warehouseId = $this->request->getVar('filter_warehouse');
        $companyId  = $this->this_company_id;

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReportPdf($newDateStart, $newDateEnd, $supplierId, $barangId, $warehouseId, $companyId);
        $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $barangId)->where('type_barang', 'bahan_baku')->first();
        $dataSupplier = $this->supplierModel->asObject()->where('id', $supplierId)->first();
        $dataTotalBBLokal = [];
        if (!empty($dataBBLokal)) {
            foreach ($dataBBLokal as $row) {
                $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
                $row->totalUmum     = $row->dppUmum - $row->pphUmum;
                $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
                $row->totalHarian   = $row->dppHarian - $row->pphHarian;
                $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
                $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
                $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
                $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
                $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
                $totalDppUmum += $row->dppUmum;
                $totalPphUmum += $row->pphUmum;
                $totalTotalUmum += $row->totalUmum;
                $totalDppHarian += $row->dppHarian;
                $totalPphHarian += $row->pphHarian;
                $totalTotalHarian += $row->totalHarian;
                $totalDppBulanan += $row->dppBulanan;
                $totalPphBulanan += $row->pphBulanan;
                $totalTotalBulanan += $row->totalBulanan;
                $totalDppSubsidi += $row->subsidi;
                $totalPphSubsidi += $row->pphSubsidi;
                $totalTotalSubsidi += $row->totalSubsidi;
            }
            $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
        }
        $no = 1;

        $data = [
            'no' => $no,
            'header' => "Laporan Rekap Persupplier",
            'tanggalAwal' => $dateStart,
            'tanggalAkhir' => $dateEnd,
            'supplierName'        => !empty($dataSupplier) ? $dataSupplier->name : "",
            'supplierAddress'      => !empty($dataSupplier) ? $dataSupplier->address : "",
            'dataOrder' => $dataBBLokal,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        $domPdf = new Dompdf();

        $fileName = 'Rekap Per Supplier';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapPersupplier/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanRekapAllbarang()
    {
        $data = [
            'getDivisi' => $this->divisiModel->getDivisiAccess(),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
        ];


        return view('Laporan/SupplierLokalBB/RekapAllBarang/index', $data);
    }

    public function allLaporanRekapAllBarang()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $this->this_company_id,
            'rm_purchase_orders.deletedAt'  => null,
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
            "divisiId"        => $this->request->getGet("filter_divisi"),
            "barangId"        => $this->request->getGet("filter_barang"),


        ];

        $availableSort = [
            'barangName'             => 'barang_master.barang_name',
            'bagianName'             => 'bagian.nama_bagian',

        ];

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;


        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierRekap($availableSort, $condition, $addCondition, $pageSize, $offset);
        // $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $addCondition['barangId'])->where('type_barang', 'bahan_baku')->first();
        // $dataWarehouse = $this->warehousesModel->asObject()->where('id', $addCondition['warehouseId'])->first();



        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;  // Add the 'No' field
            $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
            $row->totalUmum     = $row->dppUmum - $row->pphUmum;
            $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
            $row->totalHarian   = $row->dppHarian - $row->pphHarian;
            $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
            $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
            $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
            $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
            $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
            $totalDppUmum += $row->dppUmum;
            $totalPphUmum += $row->pphUmum;
            $totalTotalUmum += $row->totalUmum;
            $totalDppHarian += $row->dppHarian;
            $totalPphHarian += $row->pphHarian;
            $totalTotalHarian += $row->totalHarian;
            $totalDppBulanan += $row->dppBulanan;
            $totalPphBulanan += $row->pphBulanan;
            $totalTotalBulanan += $row->totalBulanan;
            $totalDppSubsidi += $row->subsidi;
            $totalPphSubsidi += $row->pphSubsidi;
            $totalTotalSubsidi += $row->totalSubsidi;
            $row->pphUmum = number_format($row->pphUmum);
            $row->dppUmum = number_format($row->dppUmum);
            $row->totalUmum = number_format($row->totalUmum);
            $row->dppHarian = number_format($row->dppHarian);
            $row->pphHarian = number_format($row->pphHarian);
            $row->totalHarian = number_format($row->totalHarian);
            $row->dppBulanan = number_format($row->dppBulanan);
            $row->pphBulanan = number_format($row->pphBulanan);
            $row->totalBulanan = number_format($row->totalBulanan);
            $row->subsidi = number_format($row->subsidi);
            $row->pphSubsidi = number_format($row->pphSubsidi);
            $row->totalSubsidi = number_format($row->totalSubsidi);
            $row->totalRow = number_format($row->totalRow);
        }
        $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataBBLokal['totalData'],
            "recordsFiltered" => $dataBBLokal['totalFilteredData'],
            'data'      => $dataBBLokal['data'],
            "payload" => $payload,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        echo json_encode($data);
        return;
    }


    public function exportPDFLaporanRekapAllBarang()
    {

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;

        $dateStart = $this->request->getVar('dateStart');
        $newDateStart = $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "";
        $dateEnd = $this->request->getVar('dateEnd');
        $newDateEnd = $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "";

        $barangId = $this->request->getVar('filter_barang');
        $divisiId = $this->request->getVar('filter_divisi');

        $companyId  = $this->this_company_id;

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReportRekapPdf($newDateStart, $newDateEnd, $divisiId, $barangId, $companyId);

        $dataTotalBBLokal = [];
        if (!empty($dataBBLokal)) {
            foreach ($dataBBLokal as $row) {
                $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
                $row->totalUmum     = $row->dppUmum - $row->pphUmum;
                $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
                $row->totalHarian   = $row->dppHarian - $row->pphHarian;
                $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
                $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
                $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
                $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
                $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
                $totalDppUmum += $row->dppUmum;
                $totalPphUmum += $row->pphUmum;
                $totalTotalUmum += $row->totalUmum;
                $totalDppHarian += $row->dppHarian;
                $totalPphHarian += $row->pphHarian;
                $totalTotalHarian += $row->totalHarian;
                $totalDppBulanan += $row->dppBulanan;
                $totalPphBulanan += $row->pphBulanan;
                $totalTotalBulanan += $row->totalBulanan;
                $totalDppSubsidi += $row->subsidi;
                $totalPphSubsidi += $row->pphSubsidi;
                $totalTotalSubsidi += $row->totalSubsidi;
            }
            $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
        }
        $no = 1;

        $data = [
            'no' => $no,
            'header' => "Laporan Rekap All Barang",
            'tanggalAwal' => $dateStart,
            'tanggalAkhir' => $dateEnd,
            'dataOrder' => $dataBBLokal,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        $domPdf = new Dompdf();

        $fileName = 'Rekap All Barang';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapAllBarang/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }

    public function laporanRekapPerbarang()
    {
        $data = [
            'getWarehouse' => $this->warehousesModel->get_by_company_id($this->this_company_id),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),

        ];


        return view('Laporan/SupplierLokalBB/RekapPerbarang/index', $data);
    }

    public function allLaporanRekapPerbarang()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $this->this_company_id,
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
            "warehouseId"        => $this->request->getGet("filter_warehouse"),
            "barangId"        => $this->request->getGet("filter_barang"),


        ];

        $availableSort = [

            'supplierName'          => 'suppliers.name',

        ];

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;


        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierRekap($availableSort, $condition, $addCondition, $pageSize, $offset);
        // $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $addCondition['barangId'])->where('type_barang', 'bahan_baku')->first();
        // $dataWarehouse = $this->warehousesModel->asObject()->where('id', $addCondition['warehouseId'])->first();



        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;  // Add the 'No' field
            $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
            $row->totalUmum     = $row->dppUmum - $row->pphUmum;
            $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
            $row->totalHarian   = $row->dppHarian - $row->pphHarian;
            $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
            $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
            $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
            $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
            $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
            $totalPphUmum += $row->pphUmum;
            $totalTotalUmum += $row->totalUmum;
            $totalDppHarian += $row->dppHarian;
            $totalPphHarian += $row->pphHarian;
            $totalTotalHarian += $row->totalHarian;
            $totalDppBulanan += $row->dppBulanan;
            $totalPphBulanan += $row->pphBulanan;
            $totalTotalBulanan += $row->totalBulanan;
            $totalDppSubsidi += $row->subsidi;
            $totalPphSubsidi += $row->pphSubsidi;
            $totalTotalSubsidi += $row->totalSubsidi;

            $row->pphUmum = number_format($row->pphUmum);
            $row->dppUmum = number_format($row->dppUmum);
            $row->totalUmum = number_format($row->totalUmum);
            $row->dppHarian = number_format($row->dppHarian);
            $row->pphHarian = number_format($row->pphHarian);
            $row->totalHarian = number_format($row->totalHarian);
            $row->dppBulanan = number_format($row->dppBulanan);
            $row->pphBulanan = number_format($row->pphBulanan);
            $row->totalBulanan = number_format($row->totalBulanan);
            $row->subsidi = number_format($row->subsidi);
            $row->pphSubsidi = number_format($row->pphSubsidi);
            $row->totalSubsidi = number_format($row->totalSubsidi);
            $row->totalRow = number_format($row->totalRow);
        }
        $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataBBLokal['totalData'],
            "recordsFiltered" => $dataBBLokal['totalFilteredData'],
            'data'      => $dataBBLokal['data'],
            "payload" => $payload,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        echo json_encode($data);
        return;
    }


    public function exportPDFLaporanRekapPerbarang()
    {

        $totalDppUmum = 0;
        $totalPphUmum = 0;
        $totalTotalUmum = 0;
        $totalDppHarian = 0;
        $totalPphHarian = 0;
        $totalTotalHarian = 0;
        $totalDppBulanan = 0;
        $totalPphBulanan = 0;
        $totalTotalBulanan = 0;
        $totalDppSubsidi = 0;
        $totalPphSubsidi = 0;
        $totalTotalSubsidi = 0;
        $totalTotalRow = 0;

        $dateStart = $this->request->getVar('dateStart');
        $newDateStart = $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "";
        $dateEnd = $this->request->getVar('dateEnd');
        $newDateEnd = $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "";

        $barangId = $this->request->getVar('filter_barang');

        $warehouseId = $this->request->getVar('filter_warehouse');

        $companyId  = $this->this_company_id;

        $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $barangId)->where('type_barang', 'bahan_baku')->first();
        $dataWarehouse = $this->warehousesModel->asObject()->where('id', $warehouseId)->first();

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReportRekapPdf($newDateStart, $newDateEnd,  $warehouseId, $barangId, $companyId);

        $dataTotalBBLokal = [];
        if (!empty($dataBBLokal)) {
            foreach ($dataBBLokal as $row) {
                $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
                $row->totalUmum     = $row->dppUmum - $row->pphUmum;
                $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
                $row->totalHarian   = $row->dppHarian - $row->pphHarian;
                $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
                $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
                $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
                $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
                $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
                $totalPphUmum += $row->pphUmum;
                $totalTotalUmum += $row->totalUmum;
                $totalDppHarian += $row->dppHarian;
                $totalPphHarian += $row->pphHarian;
                $totalTotalHarian += $row->totalHarian;
                $totalDppBulanan += $row->dppBulanan;
                $totalPphBulanan += $row->pphBulanan;
                $totalTotalBulanan += $row->totalBulanan;
                $totalDppSubsidi += $row->subsidi;
                $totalPphSubsidi += $row->pphSubsidi;
                $totalTotalSubsidi += $row->totalSubsidi;
            }
            $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
        }
        $no = 1;

        $data = [
            'no' => $no,
            'header' => "Laporan Rekap Per Barang",
            'tanggalAwal' => $dateStart,
            'tanggalAkhir' => $dateEnd,
            'bahanBaku'        => !empty($dataBahanBaku) ? $dataBahanBaku->barang_name : "All",
            'warehouse'      => !empty($dataWarehouse) ? $dataWarehouse->warehouse_name : "All",
            'dataOrder' => $dataBBLokal,
            'totalDppUmum' => $totalDppUmum,
            'totalPphUmum' => $totalPphUmum,
            'totalTotalUmum' => $totalTotalUmum,
            'totalDppHarian' => $totalDppHarian,
            'totalPphHarian' => $totalPphHarian,
            'totalTotalHarian' => $totalTotalHarian,
            'totalDppBulanan' => $totalDppBulanan,
            'totalPphBulanan' => $totalPphBulanan,
            'totalTotalBulanan' => $totalTotalBulanan,
            'totalDppSubsidi' => $totalDppSubsidi,
            'totalPphSubsidi' => $totalPphSubsidi,
            'totalTotalSubsidi' => $totalTotalSubsidi,
            'totalTotalRow' => $totalTotalRow
        ];

        $domPdf = new Dompdf();

        $fileName = 'Rekap Per Barang';

        // load HTML content
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapPerbarang/print', $data));

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
