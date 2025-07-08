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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'penerimaan_barang.deletedAt' => null,
            'penerimaan_barang_detail.deletedAt' => null,
        ];

        $payload = [
            "pageSize"    => $pageSize,
            "currentPage" => $currentPage,
            "search"      => $this->request->getGet("search"),
            "sort"        => $this->request->getGet("sort"),
            "sortType"    => $this->request->getGet("sortType"),
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => $this->request->getGet("sort"),
            "sortType"     => $this->request->getGet("sortType"),
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, $pageSize, $offset);
        // var_dump($dataBBLokal);
        // exit;
        $groupedData = [];
        $totalTotalRow = 0;
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataBBLokal['data'] as $row) {
            $poId = $row->poNum;
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
            $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            $qty = $row->qtyPO;

            // UMUM
            if ($pphMode === "Company") {
                $dppUmum = ($row->dppUmum / $nilai_pph) * $qty;
                $pphUmum = ($row->dppUmum / $nilai_pph * $nilai_pph2) * $qty;
                $totalUmum = $dppUmum - $pphUmum;
            } else {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($pphMode === "Supplier") ? ($row->dppUmum * $nilai_pph2) * $qty : 0;
                $totalUmum = $dppUmum + $pphUmum;
            }

            // HARIAN
            if ($pphMode === "Company") {
                $dppHarian = ($row->dppHarian / $nilai_pph) * $qty;
                $pphHarian = ($row->dppHarian / $nilai_pph * $nilai_pph2) * $qty;
                $totalHarian = $dppHarian - $pphHarian;
            } else {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = ($pphMode === "Supplier") ? ($row->dppHarian * $nilai_pph2) * $qty : 0;
                $totalHarian = $dppHarian + $pphHarian;
            }

            // BULANAN
            if ($pphMode === "Company") {
                $dppBulanan = ($row->dppBulanan / $nilai_pph) * $qty;
                $pphBulanan = ($row->dppBulanan / $nilai_pph * $nilai_pph2) * $qty;
                $totalBulanan = $dppBulanan - $pphBulanan;
            } else {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = ($pphMode === "Supplier") ? ($row->dppBulanan * $nilai_pph2) * $qty : 0;
                $totalBulanan = $dppBulanan + $pphBulanan;
            }

            $totalRow = $totalUmum + $totalHarian + $totalBulanan;

            if (!isset($groupedData[$poId])) {
                // Hitung subsidi hanya 1x saat pertama kali
                if ($pphMode === "Company") {
                    $dppSubsidi = $row->subsidi / $nilai_pph;
                    $pphSubsidi = $dppSubsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($pphMode === "Supplier") ? ($row->subsidi * $nilai_pph2) : 0;
                    $totalSubsidi = $dppSubsidi + $pphSubsidi;
                }

                $groupedData[$poId] = [
                    'no' => $no++,
                    'supplierName' => $row->supplierName,
                    'poNum' => $row->poNum,
                    'poDate' => $row->poDate,
                    'satuanName' => $row->satuanName,
                    'companyName' => $row->companyName,
                    'barangName' => $row->barangName,
                    'spekName' => $row->spekName,
                    'warehouseName' => $row->warehouseName,
                    'qtyPO' => 0,
                    'dppUmum' => 0,
                    'pphUmum' => 0,
                    'totalUmum' => 0,
                    'dppHarian' => 0,
                    'pphHarian' => 0,
                    'totalHarian' => 0,
                    'dppBulanan' => 0,
                    'pphBulanan' => 0,
                    'totalBulanan' => 0,
                    'subsidi' => $dppSubsidi,
                    'pphSubsidi' => $pphSubsidi,
                    'totalSubsidi' => $totalSubsidi,
                    'totalRow' => $totalRow + $totalSubsidi, // subsidi ditambahkan 1x di awal
                ];

                $totalTotalRow += $totalRow + $totalSubsidi;
            } else {
                // PO sudah pernah ada, tambahkan nilai akumulatif
                $groupedData[$poId]['totalRow'] += $totalRow;
                $totalTotalRow += $totalRow;
            }

            // Akumulasi ke field lainnya
            $groupedData[$poId]['qtyPO'] += $qty;
            $groupedData[$poId]['dppUmum'] += $dppUmum;
            $groupedData[$poId]['pphUmum'] += $pphUmum;
            $groupedData[$poId]['totalUmum'] += $totalUmum;

            $groupedData[$poId]['dppHarian'] += $dppHarian;
            $groupedData[$poId]['pphHarian'] += $pphHarian;
            $groupedData[$poId]['totalHarian'] += $totalHarian;

            $groupedData[$poId]['dppBulanan'] += $dppBulanan;
            $groupedData[$poId]['pphBulanan'] += $pphBulanan;
            $groupedData[$poId]['totalBulanan'] += $totalBulanan;
        }

        // Format angka
        foreach ($groupedData as &$row) {
            foreach ($row as $key => $val) {
                if (is_numeric($val) && $key !== 'no') {
                    $row[$key] = number_format($val, 2, '.', ',');
                }
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataBBLokal['totalData'],
            "recordsFiltered" => $dataBBLokal['totalFilteredData'],
            'data'            => array_values($groupedData),
            "payload"         => $payload,
            'totalTotalRow'   => number_format($totalTotalRow, 2, '.', ','),
        ];

        echo json_encode($data);
        return;
    }

    public function exportPendapatanSupplierLokalBBToExcel()
    {
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'penerimaan_barang.deletedAt' => null,
            'penerimaan_barang_detail.deletedAt' => null,
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, 99999, 0); // ambil semua tanpa paginasi

        // Ambil hasil yang sudah digroup + total subsidi seperti allLaporanPendapatanSupplier
        $groupedData = $this->generateLaporanPendapatanSupplierData($dataBBLokal['data']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $header1 = [
            "No",
            "Supplier",
            "No PO",
            "Tgl PO",
            "Bahan Baku",
            "Gudang",
            "Qty",
            "Satuan",
            "Unit",
            "DPP Umum",
            "PPh Umum",
            "Dibayarkan Umum",
            "DPP Harian",
            "PPh Harian",
            "Dibayarkan Harian",
            "DPP Bulanan",
            "PPh Bulanan",
            "Dibayarkan Bulanan",
            "DPP Subsidi",
            "PPh Subsidi",
            "Total Subsidi",
            "Total"
        ];

        $sheet->fromArray($header1, NULL, 'A1');

        $rowNo = 2;
        foreach ($groupedData as $row) {
            $sheet->fromArray([
                $row['no'],
                $row['supplierName'],
                $row['poNum'],
                $row['poDate'] ?? '', // bisa ambil dari model
                $row['barangName'],
                $row['warehouseName'],
                $row['qtyPO'],
                $row['satuanName'],
                $row['companyName'] ?? '',

                $row['dppUmum'],
                $row['pphUmum'],
                $row['totalUmum'],

                $row['dppHarian'],
                $row['pphHarian'],
                $row['totalHarian'],

                $row['dppBulanan'],
                $row['pphBulanan'],
                $row['totalBulanan'],

                $row['subsidi'],
                $row['pphSubsidi'],
                $row['totalSubsidi'],

                $row['totalRow'],
            ], NULL, 'A' . $rowNo);
            $rowNo++;
        }

        // Auto size
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'Laporan_Pendapatan_Supplier_BB_Lokal_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function generateLaporanPendapatanSupplierData($data)
    {
        $groupedData = [];
        $no = 1;

        foreach ($data as $row) {
            $poId = $row->poNum;
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
            $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            $qty = $row->qtyPO;

            // UMUM
            if ($pphMode === "Company") {
                $dppUmum = ($row->dppUmum / $nilai_pph) * $qty;
                $pphUmum = ($row->dppUmum / $nilai_pph * $nilai_pph2) * $qty;
                $totalUmum = $dppUmum - $pphUmum;
            } else {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($pphMode === "Supplier") ? ($row->dppUmum * $nilai_pph2) * $qty : 0;
                $totalUmum = $dppUmum + $pphUmum;
            }

            // HARIAN
            if ($pphMode === "Company") {
                $dppHarian = ($row->dppHarian / $nilai_pph) * $qty;
                $pphHarian = ($row->dppHarian / $nilai_pph * $nilai_pph2) * $qty;
                $totalHarian = $dppHarian - $pphHarian;
            } else {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = ($pphMode === "Supplier") ? ($row->dppHarian * $nilai_pph2) * $qty : 0;
                $totalHarian = $dppHarian + $pphHarian;
            }

            // BULANAN
            if ($pphMode === "Company") {
                $dppBulanan = ($row->dppBulanan / $nilai_pph) * $qty;
                $pphBulanan = ($row->dppBulanan / $nilai_pph * $nilai_pph2) * $qty;
                $totalBulanan = $dppBulanan - $pphBulanan;
            } else {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = ($pphMode === "Supplier") ? ($row->dppBulanan * $nilai_pph2) * $qty : 0;
                $totalBulanan = $dppBulanan + $pphBulanan;
            }

            $totalRow = $totalUmum + $totalHarian + $totalBulanan;

            if (!isset($groupedData[$poId])) {
                if ($pphMode === "Company") {
                    $dppSubsidi = $row->subsidi / $nilai_pph;
                    $pphSubsidi = $dppSubsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($pphMode === "Supplier") ? ($row->subsidi * $nilai_pph2) : 0;
                    $totalSubsidi = $dppSubsidi + $pphSubsidi;
                }

                $groupedData[$poId] = [
                    'no' => $no++,
                    'supplierName' => $row->supplierName,
                    'poNum' => $row->poNum,
                    'poDate' => $row->poDate ?? '',
                    'barangName' => $row->barangName,
                    'warehouseName' => $row->warehouseName,
                    'qtyPO' => 0,
                    'satuanName' => $row->satuanName,
                    'companyName' => $row->companyName ?? '',
                    'dppUmum' => 0,
                    'pphUmum' => 0,
                    'totalUmum' => 0,
                    'dppHarian' => 0,
                    'pphHarian' => 0,
                    'totalHarian' => 0,
                    'dppBulanan' => 0,
                    'pphBulanan' => 0,
                    'totalBulanan' => 0,
                    'subsidi' => $dppSubsidi,
                    'pphSubsidi' => $pphSubsidi,
                    'totalSubsidi' => $totalSubsidi,
                    'totalRow' => $totalRow + $totalSubsidi,
                ];
            } else {
                $groupedData[$poId]['totalRow'] += $totalRow;
            }

            // Add all values
            $groupedData[$poId]['qtyPO'] += $qty;
            $groupedData[$poId]['dppUmum'] += $dppUmum;
            $groupedData[$poId]['pphUmum'] += $pphUmum;
            $groupedData[$poId]['totalUmum'] += $totalUmum;

            $groupedData[$poId]['dppHarian'] += $dppHarian;
            $groupedData[$poId]['pphHarian'] += $pphHarian;
            $groupedData[$poId]['totalHarian'] += $totalHarian;

            $groupedData[$poId]['dppBulanan'] += $dppBulanan;
            $groupedData[$poId]['pphBulanan'] += $pphBulanan;
            $groupedData[$poId]['totalBulanan'] += $totalBulanan;
        }

        // Format numbers
        foreach ($groupedData as &$r) {
            foreach ($r as $k => $v) {
                if (is_numeric($v) && $k !== 'no') {
                    $r[$k] = number_format($v, 2, '.', ',');
                }
            }
        }

        return $groupedData;
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
                $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
                $row->totalUmum     = ($row->dppUmum + $row->pphUmum) * $row->qtyPO;
                $row->pphUmum       = $row->pphUmum * $row->qtyPO;

                $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
                $row->totalHarian   = ($row->dppHarian + $row->pphHarian) * $row->qtyPO;
                $row->pphHarian       = $row->pphHarian * $row->qtyPO;

                $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
                $row->totalBulanan  = ($row->dppBulanan + $row->pphBulanan) * $row->qtyPO;
                $row->pphBulanan = $row->pphBulanan * $row->qtyPO;

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

            $pphDetailGeneralPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                $this->this_company_id,
                $row->supplier_id,
                $row->barang_id,
                $addCondition['dateStart'],
                $addCondition['dateEnd'],
                'general_price'
            );

            $pphDetailDailyPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                $this->this_company_id,
                $row->supplier_id,
                $row->barang_id,
                $addCondition['dateStart'],
                $addCondition['dateEnd'],
                'daily_price'
            );

            $pphDetailMonthlyPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                $this->this_company_id,
                $row->supplier_id,
                $row->barang_id,
                $addCondition['dateStart'],
                $addCondition['dateEnd'],
                'monthly_price'
            );

            $row->pphUmum = $pphDetailGeneralPrice['pphTotal'];
            $row->totalUmum = $pphDetailGeneralPrice['dibayarkan'];
            $row->pphHarian = $pphDetailDailyPrice['pphTotal'];
            $row->totalHarian = $pphDetailDailyPrice['dibayarkan'];
            $row->pphBulanan = $pphDetailMonthlyPrice['pphTotal'];
            $row->totalBulanan = $pphDetailMonthlyPrice['dibayarkan'];

            $row->pphSubsidi    = ($row->poPPH != 'None') ? (($row->supplierNpwp != "") ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
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
                $pphDetailGeneralPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                    $this->this_company_id,
                    $row->supplier_id,
                    $row->barang_id,
                    $newDateStart,
                    $newDateEnd,
                    'general_price'
                );

                $pphDetailDailyPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                    $this->this_company_id,
                    $row->supplier_id,
                    $row->barang_id,
                    $newDateStart,
                    $newDateEnd,
                    'daily_price'
                );

                $pphDetailMonthlyPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                    $this->this_company_id,
                    $row->supplier_id,
                    $row->barang_id,
                    $newDateStart,
                    $newDateEnd,
                    'monthly_price'
                );

                $row->pphUmum = $pphDetailGeneralPrice['pphTotal'];
                $row->totalUmum = $pphDetailGeneralPrice['dibayarkan'];
                $row->pphHarian = $pphDetailDailyPrice['pphTotal'];
                $row->totalHarian = $pphDetailDailyPrice['dibayarkan'];
                $row->pphBulanan = $pphDetailMonthlyPrice['pphTotal'];
                $row->totalBulanan = $pphDetailMonthlyPrice['dibayarkan'];

                $row->pphSubsidi    = ($row->poPPH != 'None') ? (($row->supplierNpwp != "") ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
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

    public function exportExcelLaporanRekapAllSupplier()
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
                $pphDetailGeneralPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                    $this->this_company_id,
                    $row->supplier_id,
                    $row->barang_id,
                    $newDateStart,
                    $newDateEnd,
                    'general_price'
                );

                $pphDetailDailyPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                    $this->this_company_id,
                    $row->supplier_id,
                    $row->barang_id,
                    $newDateStart,
                    $newDateEnd,
                    'daily_price'
                );

                $pphDetailMonthlyPrice = $this->RMPurchaseOrderModel->pphPendapatanSupplier(
                    $this->this_company_id,
                    $row->supplier_id,
                    $row->barang_id,
                    $newDateStart,
                    $newDateEnd,
                    'monthly_price'
                );

                $row->pphUmum = $pphDetailGeneralPrice['pphTotal'];
                $row->totalUmum = $pphDetailGeneralPrice['dibayarkan'];
                $row->pphHarian = $pphDetailDailyPrice['pphTotal'];
                $row->totalHarian = $pphDetailDailyPrice['dibayarkan'];
                $row->pphBulanan = $pphDetailMonthlyPrice['pphTotal'];
                $row->totalBulanan = $pphDetailMonthlyPrice['dibayarkan'];

                $row->pphSubsidi    = ($row->poPPH != 'None') ? (($row->supplierNpwp != "") ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

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

        $sheet
            ->setCellValue('A2', 'Tanggal')
            ->setCellValue('B2', !empty($dateStart || $dateEnd) ? $dateStart . " SD " . $dateEnd : "ALL")
            ->setCellValue('A3', 'No')
            ->setCellValue('B3', 'Supplier')
            ->setCellValue('C3', 'Bahan Baku')

            ->setCellValue('D3', 'Harian')
            ->setCellValue('D4', 'DDP')
            ->setCellValue('E4', 'PPh')
            ->setCellValue('F4', 'Dibayarkan')

            ->setCellValue('G3', 'Tambahan Harian')
            ->setCellValue('G4', 'DDP')
            ->setCellValue('H4', 'PPh')
            ->setCellValue('I4', 'Dibayarkan')

            ->setCellValue('J3', 'Tambahan Bulanan')
            ->setCellValue('J4', 'DDP')
            ->setCellValue('K4', 'PPh')
            ->setCellValue('L4', 'Dibayarkan')

            ->setCellValue('M3', 'Subsidi')
            ->setCellValue('M4', 'DDP')
            ->setCellValue('N4', 'PPh')
            ->setCellValue('O4', 'Dibayarkan')

            ->setCellValue('P3', 'Total');
        $sheet->mergeCells('A3:A4');
        $sheet->mergeCells('B3:B4');
        $sheet->mergeCells('C3:C4');
        $sheet->mergeCells('P3:P4');
        $sheet->mergeCells('D3:F3');
        $sheet->mergeCells('G3:I3');
        $sheet->mergeCells('J3:L3');
        $sheet->mergeCells('M3:O3');

        $row = 5;
        foreach ($dataBBLokal as $d) {
            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $d->supplierName)
                ->setCellValue('C' . $row, $d->barangName)
                ->setCellValue('D' . $row, number_format($d->dppUmum))
                ->setCellValue('E' . $row, number_format($d->pphUmum))
                ->setCellValue('F' . $row, number_format($d->totalUmum))
                ->setCellValue('G' . $row, number_format($d->dppHarian))
                ->setCellValue('H' . $row, number_format($d->pphHarian))
                ->setCellValue('I' . $row, number_format($d->totalHarian))
                ->setCellValue('J' . $row, number_format($d->dppBulanan))
                ->setCellValue('K' . $row, number_format($d->pphBulanan))
                ->setCellValue('L' . $row, number_format($d->totalBulanan))
                ->setCellValue('M' . $row, number_format($d->subsidi))
                ->setCellValue('N' . $row, number_format($d->pphSubsidi))
                ->setCellValue('O' . $row, number_format($d->totalSubsidi))
                ->setCellValue('P' . $row, number_format($d->totalRow));
            $row++;
        }
        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "Laporan Rekap All Supplier ";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
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
            'spekName'             => 'spekName',
            'bagianName' => 'bagianName'
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
            $row->totalUmum     = ($row->dppUmum + $row->pphUmum) * $row->qtyPO;
            $row->pphUmum       = $row->pphUmum * $row->qtyPO;

            $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
            $row->totalHarian   = ($row->dppHarian + $row->pphHarian) * $row->qtyPO;
            $row->pphHarian       = $row->pphHarian * $row->qtyPO;

            $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
            $row->totalBulanan  = ($row->dppBulanan + $row->pphBulanan) * $row->qtyPO;
            $row->pphBulanan = $row->pphBulanan * $row->qtyPO;

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
            $row->qtyPO = number_format($row->qtyPO);
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
