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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
        $currentPage = ($this->request->getGet("start") / $pageSize) + 1;

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

        // Ambil SEMUA data dulu tanpa pagination
        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);

        $groupedData = [];
        $totalTotalRow = 0;

        foreach ($dataBBLokal['data'] as $row) {
            $poId = $row->poNum;
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            if ($row->poDate <=  '2025-06-30') {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            } else {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.0025);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.0025;
            }

            $qty = $row->qtyPO;

            // UMUM
            if ($pphMode === "Company") {
                $dppUmum = ($row->dppUmum / $nilai_pph) * $qty;
                $pphUmum = ($row->dppUmum / $nilai_pph * $nilai_pph2) * $qty;
                $totalUmum = $dppUmum - $pphUmum;
            } else {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($pphMode === "Supplier") ? (($row->dppUmum / $nilai_pph) * $nilai_pph2) * $qty : 0;
                // dd($pphUmum);
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
                    'totalRow' => $totalRow + $totalSubsidi,
                ];

                $totalTotalRow += $totalRow + $totalSubsidi;
            } else {
                $groupedData[$poId]['totalRow'] += $totalRow;
                $totalTotalRow += $totalRow;
            }

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

        $groupedData = array_values($groupedData); // reset index numerik
        $totalGrouped = count($groupedData);

        // Pagination manual
        $paginatedData = array_slice($groupedData, ($currentPage - 1) * $pageSize, $pageSize);

        // Format angka dan nomor urut
        foreach ($paginatedData as $i => &$row) {
            $row['no'] = ($currentPage - 1) * $pageSize + $i + 1;

            foreach ($row as $key => $val) {
                if (is_numeric($val) && $key !== 'no') {
                    $row[$key] = number_format($val, 2, '.', ',');
                }
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalGrouped,
            "recordsFiltered" => $totalGrouped,
            'data'            => $paginatedData,
            "payload"         => [
                "pageSize"    => $pageSize,
                "currentPage" => $currentPage,
            ],
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

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null); // ambil semua tanpa paginasi

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
            if ($row->poDate <=  '2025-06-30') {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            } else {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.0025);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.0025;
            }
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
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
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
            'supplierName'          => 'suppliers.name',
            'barangName'             => 'barang_master.barang_name',

        ];
        $totalTotalRow = 0;

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $groupedData = [];
        $usedSubsidiPo = []; // untuk track subsidi yang sudah dihitung per PO
        $totalTotalRow = 0;

        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;
            $poId = $row->poNum;
            $supplierId = $row->supplier_id;
            $barangId = $row->barang_id;
            $groupKey = $supplierId . '_' . $barangId;

            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            if ($row->poDate <=  '2025-06-30') {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            } else {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.0025);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.0025;
            }
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

            // Inisialisasi grup jika belum ada
            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'supplierName'   => $row->supplierName,
                    'barangName'     => $row->barangName,
                    'spekName'       => $row->spekName,
                    'satuanName'     => $row->satuanName,
                    'qtyPO'          => 0,
                    'dppUmum'        => 0,
                    'pphUmum'        => 0,
                    'totalUmum'      => 0,
                    'dppHarian'      => 0,
                    'pphHarian'      => 0,
                    'totalHarian'    => 0,
                    'dppBulanan'     => 0,
                    'pphBulanan'     => 0,
                    'totalBulanan'   => 0,
                    'subsidi'        => 0,
                    'pphSubsidi'     => 0,
                    'totalSubsidi'   => 0,
                    'totalRow'       => 0,
                ];
            }

            // Tambahkan nilai ke grup
            $groupedData[$groupKey]['qtyPO'] += $qty;
            $groupedData[$groupKey]['dppUmum'] += $dppUmum;
            $groupedData[$groupKey]['pphUmum'] += $pphUmum;
            $groupedData[$groupKey]['totalUmum'] += $totalUmum;
            $groupedData[$groupKey]['dppHarian'] += $dppHarian;
            $groupedData[$groupKey]['pphHarian'] += $pphHarian;
            $groupedData[$groupKey]['totalHarian'] += $totalHarian;
            $groupedData[$groupKey]['dppBulanan'] += $dppBulanan;
            $groupedData[$groupKey]['pphBulanan'] += $pphBulanan;
            $groupedData[$groupKey]['totalBulanan'] += $totalBulanan;
            $groupedData[$groupKey]['totalRow'] += $totalRow;
            $totalTotalRow += $totalRow;

            // Hitung subsidi hanya jika PO belum dihitung
            if (!in_array($poId, $usedSubsidiPo)) {
                if ($pphMode === "Company") {
                    $dppSubsidi = $row->subsidi / $nilai_pph;
                    $pphSubsidi = $dppSubsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($pphMode === "Supplier") ? ($row->subsidi * $nilai_pph2) : 0;
                    $totalSubsidi = $dppSubsidi + $pphSubsidi;
                }

                $usedSubsidiPo[] = $poId;

                $groupedData[$groupKey]['subsidi'] += $dppSubsidi;
                $groupedData[$groupKey]['pphSubsidi'] += $pphSubsidi;
                $groupedData[$groupKey]['totalSubsidi'] += $totalSubsidi;
                $groupedData[$groupKey]['totalRow'] += $totalSubsidi;
                $totalTotalRow += $totalSubsidi;
            }
        }

        $groupedData = array_values($groupedData); // reset index numerik
        $totalGrouped = count($groupedData);

        // Pagination manual
        $paginatedData = array_slice($groupedData, ($currentPage - 1) * $pageSize, $pageSize);

        // Format angka dan nomor urut
        foreach ($paginatedData as $i => &$row) {
            $row['no'] = ($currentPage - 1) * $pageSize + $i + 1;

            foreach ($row as $key => $val) {
                if (is_numeric($val) && $key !== 'no') {
                    $row[$key] = number_format($val, 2, '.', ',');
                }
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalGrouped,
            "recordsFiltered" => $totalGrouped,
            'data'            => $paginatedData,
            "payload"         => [
                "pageSize"    => $pageSize,
                "currentPage" => $currentPage,
            ],
            'totalTotalRow'   => number_format($totalTotalRow, 2, '.', ','),
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
            'supplierName'          => 'suppliers.name',
            'barangName'             => 'barang_master.barang_name',
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);
        $no = 1;
        $dataBahanBaku = null;

        if (!empty($addCondition['barangId'])) {
            $dataBahanBaku = $this->barangMasterModel->where('id', $addCondition['barangId'])->first();
        }

        $groupedData = [];
        $usedSubsidiPo = []; // untuk track subsidi yang sudah dihitung per PO
        $totalTotalRow = 0;

        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;
            $poId = $row->poNum;
            $supplierId = $row->supplier_id;
            $barangId = $row->barang_id;
            $groupKey = $supplierId . '_' . $barangId;

            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            if ($row->poDate <=  '2025-06-30') {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            } else {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.0025);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.0025;
            }
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

            // Inisialisasi grup jika belum ada
            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'supplierName'   => $row->supplierName,
                    'barangName'     => $row->barangName,
                    'spekName'       => $row->spekName,
                    'satuanName'     => $row->satuanName,
                    'qtyPO'          => 0,
                    'dppUmum'        => 0,
                    'pphUmum'        => 0,
                    'totalUmum'      => 0,
                    'dppHarian'      => 0,
                    'pphHarian'      => 0,
                    'totalHarian'    => 0,
                    'dppBulanan'     => 0,
                    'pphBulanan'     => 0,
                    'totalBulanan'   => 0,
                    'subsidi'        => 0,
                    'pphSubsidi'     => 0,
                    'totalSubsidi'   => 0,
                    'totalRow'       => 0,
                ];
            }

            // Tambahkan nilai ke grup
            $groupedData[$groupKey]['qtyPO'] += $qty;
            $groupedData[$groupKey]['dppUmum'] += $dppUmum;
            $groupedData[$groupKey]['pphUmum'] += $pphUmum;
            $groupedData[$groupKey]['totalUmum'] += $totalUmum;
            $groupedData[$groupKey]['dppHarian'] += $dppHarian;
            $groupedData[$groupKey]['pphHarian'] += $pphHarian;
            $groupedData[$groupKey]['totalHarian'] += $totalHarian;
            $groupedData[$groupKey]['dppBulanan'] += $dppBulanan;
            $groupedData[$groupKey]['pphBulanan'] += $pphBulanan;
            $groupedData[$groupKey]['totalBulanan'] += $totalBulanan;
            $groupedData[$groupKey]['totalRow'] += $totalRow;
            $totalTotalRow += $totalRow;

            // Hitung subsidi hanya jika PO belum dihitung
            if (!in_array($poId, $usedSubsidiPo)) {
                if ($pphMode === "Company") {
                    $dppSubsidi = $row->subsidi / $nilai_pph;
                    $pphSubsidi = $dppSubsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($pphMode === "Supplier") ? ($row->subsidi * $nilai_pph2) : 0;
                    $totalSubsidi = $dppSubsidi + $pphSubsidi;
                }

                $usedSubsidiPo[] = $poId;

                $groupedData[$groupKey]['subsidi'] += $dppSubsidi;
                $groupedData[$groupKey]['pphSubsidi'] += $pphSubsidi;
                $groupedData[$groupKey]['totalSubsidi'] += $totalSubsidi;
                $groupedData[$groupKey]['totalRow'] += $totalSubsidi;
                $totalTotalRow += $totalSubsidi;
            }
        }
        $no = 1;

        $data = [
            'no' => $no,
            'header' => "Laporan Rekap All Supplier",
            'tanggalAwal' => $addCondition['dateStart'],
            'tanggalAkhir' => $addCondition['dateEnd'],
            'bahanBaku' => $dataBahanBaku != null ? $dataBahanBaku['barang_name'] : "All",
            'dataOrder' => $groupedData,
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
            'supplierName'          => 'suppliers.name',
            'barangName'             => 'barang_master.barang_name',
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);
        $no = 1;
        $dataBahanBaku = null;

        if (!empty($addCondition['barangId'])) {
            $dataBahanBaku = $this->barangMasterModel->where('id', $addCondition['barangId'])->first();
        }

        $groupedData = [];
        $usedSubsidiPo = []; // untuk track subsidi yang sudah dihitung per PO
        $totalTotalRow = 0;

        foreach ($dataBBLokal['data'] as $row) {
            $row->no = $no++;
            $poId = $row->poNum;
            $supplierId = $row->supplier_id;
            $barangId = $row->barang_id;
            $groupKey = $supplierId . '_' . $barangId;

            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            if ($row->poDate <=  '2025-06-30') {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            } else {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.0025);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.0025;
            }
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

            // Inisialisasi grup jika belum ada
            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'supplierName'   => $row->supplierName,
                    'barangName'     => $row->barangName,
                    'spekName'       => $row->spekName,
                    'satuanName'     => $row->satuanName,
                    'qtyPO'          => 0,
                    'dppUmum'        => 0,
                    'pphUmum'        => 0,
                    'totalUmum'      => 0,
                    'dppHarian'      => 0,
                    'pphHarian'      => 0,
                    'totalHarian'    => 0,
                    'dppBulanan'     => 0,
                    'pphBulanan'     => 0,
                    'totalBulanan'   => 0,
                    'subsidi'        => 0,
                    'pphSubsidi'     => 0,
                    'totalSubsidi'   => 0,
                    'totalRow'       => 0,
                ];
            }

            // Tambahkan nilai ke grup
            $groupedData[$groupKey]['qtyPO'] += $qty;
            $groupedData[$groupKey]['dppUmum'] += $dppUmum;
            $groupedData[$groupKey]['pphUmum'] += $pphUmum;
            $groupedData[$groupKey]['totalUmum'] += $totalUmum;
            $groupedData[$groupKey]['dppHarian'] += $dppHarian;
            $groupedData[$groupKey]['pphHarian'] += $pphHarian;
            $groupedData[$groupKey]['totalHarian'] += $totalHarian;
            $groupedData[$groupKey]['dppBulanan'] += $dppBulanan;
            $groupedData[$groupKey]['pphBulanan'] += $pphBulanan;
            $groupedData[$groupKey]['totalBulanan'] += $totalBulanan;
            $groupedData[$groupKey]['totalRow'] += $totalRow;
            $totalTotalRow += $totalRow;

            // Hitung subsidi hanya jika PO belum dihitung
            if (!in_array($poId, $usedSubsidiPo)) {
                if ($pphMode === "Company") {
                    $dppSubsidi = $row->subsidi / $nilai_pph;
                    $pphSubsidi = $dppSubsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($pphMode === "Supplier") ? ($row->subsidi * $nilai_pph2) : 0;
                    $totalSubsidi = $dppSubsidi + $pphSubsidi;
                }

                $usedSubsidiPo[] = $poId;

                $groupedData[$groupKey]['subsidi'] += $dppSubsidi;
                $groupedData[$groupKey]['pphSubsidi'] += $pphSubsidi;
                $groupedData[$groupKey]['totalSubsidi'] += $totalSubsidi;
                $groupedData[$groupKey]['totalRow'] += $totalSubsidi;
                $totalTotalRow += $totalSubsidi;
            }
        }
        $no = 1;

        $data = [
            'no' => $no,
            'header' => "Laporan Rekap All Supplier",
            'tanggalAwal' => $addCondition['dateStart'],
            'tanggalAkhir' => $addCondition['dateEnd'],
            'bahanBaku' => $dataBahanBaku != null ? $dataBahanBaku['barang_name'] : "All",
            'dataOrder' => $groupedData,
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

        // Ambil data dari model atau hasil sebelumnya
        $dataOrder = $data['dataOrder']; // misal dari model
        $tanggalAwal = $this->request->getGet('dateStart');
        $tanggalAkhir = $this->request->getGet('dateEnd');
        $header = 'Laporan Rekap All Supplier';

        // Hitung total
        $totalDppUmum = $totalPphUmum = $totalTotalUmum = 0;
        $totalDppHarian = $totalPphHarian = $totalTotalHarian = 0;
        $totalDppBulanan = $totalPphBulanan = $totalTotalBulanan = 0;
        $totalDppSubsidi = $totalPphSubsidi = $totalTotalSubsidi = 0;
        $totalTotalRow = 0;

        foreach ($dataOrder as $item) {
            $totalDppUmum += $item['dppUmum'];
            $totalPphUmum += $item['pphUmum'];
            $totalTotalUmum += $item['totalUmum'];
            $totalDppHarian += $item['dppHarian'];
            $totalPphHarian += $item['pphHarian'];
            $totalTotalHarian += $item['totalHarian'];
            $totalDppBulanan += $item['dppBulanan'];
            $totalPphBulanan += $item['pphBulanan'];
            $totalTotalBulanan += $item['totalBulanan'];
            $totalDppSubsidi += $item['subsidi'];
            $totalPphSubsidi += $item['pphSubsidi'];
            $totalTotalSubsidi += $item['totalSubsidi'];
            $totalTotalRow += $item['totalRow'];
        }

        // Setup spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header & Tanggal
        $sheet->setCellValue('A1', $header);
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Tanggal');
        $sheet->setCellValue('B2', ':');
        if ($tanggalAwal && $tanggalAkhir) {
            $sheet->setCellValue('C2', $tanggalAwal);
            $sheet->setCellValue('D2', 's/d');
            $sheet->setCellValue('E2', $tanggalAkhir);
        } else {
            $sheet->setCellValue('C2', 'ALL');
        }

        // Header tabel
        $sheet->fromArray([
            [
                'No.',
                'Supplier',
                'Bahan Baku',
                'DPP Umum',
                'PPh Umum',
                'Total Umum',
                'DPP Harian',
                'PPh Harian',
                'Total Harian',
                'DPP Bulanan',
                'PPh Bulanan',
                'Total Bulanan',
                'DPP Subsidi',
                'PPh Subsidi',
                'Total Subsidi',
                'Total'
            ]
        ], NULL, 'A4');

        $sheet->getStyle('A4:P4')->getFont()->setBold(true);
        $sheet->getStyle('A4:P4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:P4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Isi data
        $startRow = 5;
        $no = 1;
        foreach ($dataOrder as $item) {
            $sheet->fromArray([
                $no++,
                $item['supplierName'],
                $item['barangName'],
                $item['dppUmum'],
                $item['pphUmum'],
                $item['totalUmum'],
                $item['dppHarian'],
                $item['pphHarian'],
                $item['totalHarian'],
                $item['dppBulanan'],
                $item['pphBulanan'],
                $item['totalBulanan'],
                $item['subsidi'],
                $item['pphSubsidi'],
                $item['totalSubsidi'],
                $item['totalRow']
            ], NULL, 'A' . $startRow);
            $startRow++;
        }

        // Baris total
        $sheet->fromArray([
            '',
            '',
            'Total',
            $totalDppUmum,
            $totalPphUmum,
            $totalTotalUmum,
            $totalDppHarian,
            $totalPphHarian,
            $totalTotalHarian,
            $totalDppBulanan,
            $totalPphBulanan,
            $totalTotalBulanan,
            $totalDppSubsidi,
            $totalPphSubsidi,
            $totalTotalSubsidi,
            $totalTotalRow
        ], NULL, 'A' . $startRow);
        $sheet->getStyle("A{$startRow}:P{$startRow}")->getFont()->setBold(true);

        // Format angka
        $cols = range('D', 'P');
        for ($r = 5; $r <= $startRow; $r++) {
            foreach ($cols as $col) {
                $sheet->getStyle("{$col}{$r}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }
        }

        // Autosize kolom
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download response
        $filename = 'Laporan_Rekap_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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

    public function laporanRekapAllBarang()
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
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            if ($row->po_date <=  '2025-06-30') {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            } else {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.0025);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.0025;
            }
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


            if ($pphMode === "Company") {
                $dppSubsidi = $row->subsidi / $nilai_pph;
                $pphSubsidi = $dppSubsidi * $nilai_pph2;
                $totalSubsidi = $dppSubsidi - $pphSubsidi;
            } else {
                $dppSubsidi = $row->subsidi;
                $pphSubsidi = ($pphMode === "Supplier") ? ($row->subsidi * $nilai_pph2) : 0;
                $totalSubsidi = $dppSubsidi + $pphSubsidi;
            }


            $row->dppUmum = $dppUmum;
            $row->pphUmum = $pphUmum;
            $row->totalUmum = $totalUmum;
            $row->dppHarian = $dppHarian;
            $row->pphHarian = $pphHarian;
            $row->totalHarian = $totalHarian;
            $row->dppBulanan = $dppBulanan;
            $row->pphBulanan = $pphBulanan;
            $row->totalBulanan = $totalBulanan;
            $row->dppSubsidi = $dppSubsidi;
            $row->pphSubsidi = $pphSubsidi;
            $row->totalSubsidi = $totalSubsidi;

            $row->totalRow = $row->totalUmum + $row->totalHarian + $row->totalBulanan;

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
            $row->pphUmum = number_format($row->pphUmum, 2);
            $row->dppUmum = number_format($row->dppUmum, 2);
            $row->totalUmum = number_format($row->totalUmum, 2);
            $row->dppHarian = number_format($row->dppHarian, 2);
            $row->pphHarian = number_format($row->pphHarian, 2);
            $row->totalHarian = number_format($row->totalHarian, 2);
            $row->dppBulanan = number_format($row->dppBulanan, 2);
            $row->pphBulanan = number_format($row->pphBulanan, 2);
            $row->totalBulanan = number_format($row->totalBulanan, 2);
            $row->subsidi = number_format($row->subsidi, 2);
            $row->pphSubsidi = number_format($row->pphSubsidi, 2);
            $row->totalSubsidi = number_format($row->totalSubsidi, 2);
            $row->totalRow = number_format($row->totalRow, 2);
            $row->qtyPO = number_format($row->qtyPO, 2);
        }


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

    public function exportExcelLaporanRekapAllBarang()
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
        foreach ($dataBBLokal as $row) {
            $pphMode = $row->poPPH;
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            if ($row->po_date <=  '2025-06-30') {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            } else {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.0025);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.0025;
            }
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


            if ($pphMode === "Company") {
                $dppSubsidi = $row->subsidi / $nilai_pph;
                $pphSubsidi = $dppSubsidi * $nilai_pph2;
                $totalSubsidi = $dppSubsidi - $pphSubsidi;
            } else {
                $dppSubsidi = $row->subsidi;
                $pphSubsidi = ($pphMode === "Supplier") ? ($row->subsidi * $nilai_pph2) : 0;
                $totalSubsidi = $dppSubsidi + $pphSubsidi;
            }

            $row->dppUmum = $dppUmum;
            $row->pphUmum = $pphUmum;
            $row->totalUmum = $totalUmum;
            $row->dppHarian = $dppHarian;
            $row->pphHarian = $pphHarian;
            $row->totalHarian = $totalHarian;
            $row->dppBulanan = $dppBulanan;
            $row->pphBulanan = $pphBulanan;
            $row->totalBulanan = $totalBulanan;
            $row->dppSubsidi = $dppSubsidi;
            $row->pphSubsidi = $pphSubsidi;
            $row->totalSubsidi = $totalSubsidi;

            $row->totalRow = $row->totalUmum + $row->totalHarian + $row->totalBulanan;

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

        $no = 1;

        // Setup Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Baris Pertama
        $sheet->setCellValue('A1', 'NO.');
        $sheet->setCellValue('B1', 'BARANG');
        $sheet->setCellValue('C1', 'SPESIFIKASI');
        $sheet->setCellValue('D1', 'DEPARTEMEN');
        $sheet->setCellValue('E1', 'QTY');
        $sheet->setCellValue('F1', 'SATUAN');
        $sheet->setCellValue('G1', 'Umum');
        $sheet->setCellValue('J1', 'Tambahan Harian');
        $sheet->setCellValue('M1', 'Tambahan Bulanan');
        $sheet->setCellValue('P1', 'Tambahan Langsung');
        $sheet->setCellValue('S1', 'Total');

        // Merge kolom yang colspan
        $sheet->mergeCells('G1:I1');
        $sheet->mergeCells('J1:L1');
        $sheet->mergeCells('M1:O1');
        $sheet->mergeCells('P1:R1');
        $sheet->mergeCells('S1:S2');

        // Merge kolom A–F (rowspan)
        foreach (range('A', 'F') as $col) {
            $sheet->mergeCells("{$col}1:{$col}2");
        }

        // Header Baris Kedua
        $sheet->setCellValue('G2', 'DPP');
        $sheet->setCellValue('H2', 'PPh');
        $sheet->setCellValue('I2', 'Dibayarkan');
        $sheet->setCellValue('J2', 'DPP');
        $sheet->setCellValue('K2', 'PPh');
        $sheet->setCellValue('L2', 'Dibayarkan');
        $sheet->setCellValue('M2', 'DPP');
        $sheet->setCellValue('N2', 'PPh');
        $sheet->setCellValue('O2', 'Dibayarkan');
        $sheet->setCellValue('P2', 'DPP');
        $sheet->setCellValue('Q2', 'PPh');
        $sheet->setCellValue('R2', 'Dibayarkan');

        // Bold Header
        $sheet->getStyle('A1:S2')->getFont()->setBold(true);

        // Tulis Data
        $row = 3;
        $no = 1;
        $totalQty = 0;
        foreach ($dataBBLokal as $do) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $do->barangName);
            $sheet->setCellValue("C{$row}", $do->spekName);
            $sheet->setCellValue("D{$row}", $do->bagianName);
            $sheet->setCellValue("E{$row}", $do->qtyPO);
            $sheet->setCellValue("F{$row}", $do->satuanName);

            $sheet->setCellValue("G{$row}", $do->dppUmum);
            $sheet->setCellValue("H{$row}", $do->pphUmum);
            $sheet->setCellValue("I{$row}", $do->totalUmum);

            $sheet->setCellValue("J{$row}", $do->dppHarian);
            $sheet->setCellValue("K{$row}", $do->pphHarian);
            $sheet->setCellValue("L{$row}", $do->totalHarian);

            $sheet->setCellValue("M{$row}", $do->dppBulanan);
            $sheet->setCellValue("N{$row}", $do->pphBulanan);
            $sheet->setCellValue("O{$row}", $do->totalBulanan);

            $sheet->setCellValue("P{$row}", $do->subsidi);
            $sheet->setCellValue("Q{$row}", $do->pphSubsidi);
            $sheet->setCellValue("R{$row}", $do->totalSubsidi);

            $sheet->setCellValue("S{$row}", $do->totalRow);
            $totalQty += $do->qtyPO;
            $row++;
        }

        $sheet->getStyle("A{$row}:S{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("A{$row}", 'Total');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("E{$row}", $totalQty);
        $sheet->setCellValue("F{$row}", '');

        $sheet->setCellValue("G{$row}", $totalDppUmum);
        $sheet->setCellValue("H{$row}", $totalPphUmum);
        $sheet->setCellValue("I{$row}", $totalTotalUmum);

        $sheet->setCellValue("J{$row}", $totalDppHarian);
        $sheet->setCellValue("K{$row}", $totalPphHarian);
        $sheet->setCellValue("L{$row}", $totalTotalHarian);

        $sheet->setCellValue("M{$row}", $totalDppBulanan);
        $sheet->setCellValue("N{$row}", $totalPphBulanan);
        $sheet->setCellValue("O{$row}", $totalTotalBulanan);

        $sheet->setCellValue("P{$row}", $totalDppSubsidi);
        $sheet->setCellValue("Q{$row}", $totalPphSubsidi);
        $sheet->setCellValue("R{$row}", $totalTotalSubsidi);

        $sheet->setCellValue("S{$row}", $totalTotalRow);

        // Bikin bold baris total
        $sheet->getStyle("A{$row}:S{$row}")->getFont()->setBold(true);

        // Format angka
        $sheet->getStyle("G3:S{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Auto size
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'laporan_rekap_barang.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
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
                $pphMode = $row->poPPH;
                $hasNpwp = !empty($row->supplierNpwp);
                if ($row->po_date <=  '2025-06-30') {
                    $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                    $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
                } else {
                    $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.0025);
                    $nilai_pph2 = $hasNpwp ? 0.0025 : 0.0025;
                }
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


                if ($pphMode === "Company") {
                    $dppSubsidi = $row->subsidi / $nilai_pph;
                    $pphSubsidi = $dppSubsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($pphMode === "Supplier") ? ($row->subsidi * $nilai_pph2) : 0;
                    $totalSubsidi = $dppSubsidi + $pphSubsidi;
                }

                $row->dppUmum = $dppUmum;
                $row->pphUmum = $pphUmum;
                $row->totalUmum = $totalUmum;
                $row->dppHarian = $dppHarian;
                $row->pphHarian = $pphHarian;
                $row->totalHarian = $totalHarian;
                $row->dppBulanan = $dppBulanan;
                $row->pphBulanan = $pphBulanan;
                $row->totalBulanan = $totalBulanan;
                $row->dppSubsidi = $dppSubsidi;
                $row->pphSubsidi = $pphSubsidi;
                $row->totalSubsidi = $totalSubsidi;

                $row->totalRow = $row->totalUmum + $row->totalHarian + $row->totalBulanan;

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
            'header' => "Laporan Rekap All Barang (Summary)",
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

        $fileName = 'Rekap All Barang (Summary)';

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
