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
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
            'getPoNo' => $this->RMPurchaseOrderModel->select('id ,po_no')->where('deletedAt', NULL)->where('company_id', $this->this_company_id)->findAll(),
            'getSupplier' => $this->supplierModel->where('deletedAt', NULL)->where('type', 'BAHAN BAKU')->findAll(),
            'getWarehouse' => $this->warehousesModel->get_by_company_id($this->this_company_id),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
            'getDivisi' => $this->divisiModel->getDivisiAccess(),
        ];


        return view('Laporan/SupplierLokalBB/PendapatanSupplier/index', $data);
    }

    public function allLaporanPendapatanSupplier()
    {
        $pageSize = $this->request->getGet("length") ?? 25;
        $currentPage = ($this->request->getGet("start") / $pageSize) + 1;
        $start = ($currentPage - 1) * $pageSize;

        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            // 'rm_purchase_orders.is_posted' => '1',
            // 'rm_purchase_orders.status_penerimaan' => '1',
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => $this->request->getGet("sort") ?? 'poDate',
            "sortType"     => $this->request->getGet("sortType") ?? 'asc',
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        // var_dump($condition, $addCondition, $availableSort);

        // Ambil semua data untuk total footer
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // Ambil data dengan pagination
        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            $pageSize,
            $start
        );

        $paginatedData = $dataBBLokal['data'];

        // --- Hitung total footer ---
        $totalsRaw = [
            'qtyAll' => 0,
            'dppUmum' => 0,
            'pphUmum' => 0,
            'totalUmum' => 0,
            'dppHarian' => 0,
            'pphHarian' => 0,
            'totalHarian' => 0,
            'dppBulanan' => 0,
            'pphBulanan' => 0,
            'totalBulanan' => 0,
            'dppTambahan' => 0,
            'pphTambahan' => 0,
            'totalTambahan' => 0,
            'totalRow' => 0
        ];

        foreach ($allData as $row) {
            $qtyAll       = floatval($row->qtyPO ?? 0);

            $dppUmum       = floatval($row->dpp_umum ?? 0);
            $pphUmum       = floatval($row->pph_umum ?? 0);
            $totalUmum     = floatval($row->nilai_total_umum ?? 0);

            $dppHarian     = floatval($row->dpp_harian ?? 0);
            $pphHarian     = floatval($row->pph_harian ?? 0);
            $totalHarian   = floatval($row->nilai_total_harian ?? 0);

            $dppBulanan    = floatval($row->dpp_bulanan ?? 0);
            $pphBulanan    = floatval($row->pph_bulanan ?? 0);
            $totalBulanan  = floatval($row->nilai_total_bulanan ?? 0);

            $dppTambahan   = floatval($row->dpp_tambahan ?? 0);
            $pphTambahan   = floatval($row->pph_tambahan ?? 0);
            $totalTambahan = floatval($row->nilai_total_tambahan ?? 0);

            $totalRow = $totalUmum + $totalHarian + $totalBulanan + $totalTambahan;

            $totalsRaw['qtyAll']       += $qtyAll;
            $totalsRaw['dppUmum']       += $dppUmum;
            $totalsRaw['pphUmum']       += $pphUmum;
            $totalsRaw['totalUmum']     += $totalUmum;
            $totalsRaw['dppHarian']     += $dppHarian;
            $totalsRaw['pphHarian']     += $pphHarian;
            $totalsRaw['totalHarian']   += $totalHarian;
            $totalsRaw['dppBulanan']    += $dppBulanan;
            $totalsRaw['pphBulanan']    += $pphBulanan;
            $totalsRaw['totalBulanan']  += $totalBulanan;
            $totalsRaw['dppTambahan']   += $dppTambahan;
            $totalsRaw['pphTambahan']   += $pphTambahan;
            $totalsRaw['totalTambahan'] += $totalTambahan;
            $totalsRaw['totalRow']      += $totalRow;
        }

        // --- Format row (pagination) ---
        foreach ($paginatedData as $i => &$row) {
            $row->no = $start + $i + 1;
            $totalRow = floatval($row->nilai_total_umum ?? 0)
                + floatval($row->nilai_total_harian ?? 0)
                + floatval($row->nilai_total_bulanan ?? 0)
                + floatval($row->nilai_total_tambahan ?? 0);

            foreach (
                [
                    'dpp_umum',
                    'pph_umum',
                    'nilai_total_umum',
                    'dpp_harian',
                    'pph_harian',
                    'nilai_total_harian',
                    'dpp_bulanan',
                    'pph_bulanan',
                    'nilai_total_bulanan',
                    'dpp_tambahan',
                    'pph_tambahan',
                    'nilai_total_tambahan'
                ] as $key
            ) {
                $row->$key = number_format(floatval($row->$key ?? 0), 2, '.', ',');
            }
            $row->totalRow = number_format($totalRow, 2, '.', ',');
        }

        $footerTotals = [];
        foreach ($totalsRaw as $k => $v) {
            $footerTotals[$k] = number_format($v, 2, '.', ',');
        }

        echo json_encode([
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $dataBBLokal['totalData'],
            "recordsFiltered" => $dataBBLokal['totalFilteredData'],
            'data' => $paginatedData,
            'footerTotals' => $footerTotals
        ]);
    }

    public function exportPendapatanSupplierLokalBBToExcel()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            // 'rm_purchase_orders.is_posted' => '1',
            // 'rm_purchase_orders.status_penerimaan' => '1',
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => 'rm_purchase_orders.po_date, divisis.id',
            "sortType"     => $this->request->getGet("sortType"),
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        // Ambil semua data (tanpa paging)
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // Group data berdasarkan barangName
        $groupedData = [];
        foreach ($allData as $row) {
            $barangName = $row->barangName ?? 'UNKNOWN';
            if (!isset($groupedData[$barangName])) {
                $groupedData[$barangName] = [
                    'data' => [],
                    'summary' => [
                        'totalQtyPO' => 0,
                        'dppUmum' => 0,
                        'pphUmum' => 0,
                        'totalUmum' => 0,
                        'dppHarian' => 0,
                        'pphHarian' => 0,
                        'totalHarian' => 0,
                        'dppBulanan' => 0,
                        'pphBulanan' => 0,
                        'totalBulanan' => 0,
                        'dppTambahan' => 0,
                        'pphTambahan' => 0,
                        'totalTambahan' => 0,
                        'totalRow' => 0,
                    ]
                ];
            }

            // Hitung totalRow per baris
            $row->totalRow =
                floatval($row->nilai_total_umum ?? 0) +
                floatval($row->nilai_total_harian ?? 0) +
                floatval($row->nilai_total_bulanan ?? 0) +
                floatval($row->nilai_total_tambahan ?? 0);

            $groupedData[$barangName]['data'][] = $row;

            // Update summary
            $groupedData[$barangName]['summary']['totalQtyPO']   += floatval($row->qtyPO ?? 0);
            $groupedData[$barangName]['summary']['dppUmum']      += floatval($row->dpp_umum ?? 0);
            $groupedData[$barangName]['summary']['pphUmum']      += floatval($row->pph_umum ?? 0);
            $groupedData[$barangName]['summary']['totalUmum']    += floatval($row->nilai_total_umum ?? 0);
            $groupedData[$barangName]['summary']['dppHarian']    += floatval($row->dpp_harian ?? 0);
            $groupedData[$barangName]['summary']['pphHarian']    += floatval($row->pph_harian ?? 0);
            $groupedData[$barangName]['summary']['totalHarian']  += floatval($row->nilai_total_harian ?? 0);
            $groupedData[$barangName]['summary']['dppBulanan']   += floatval($row->dpp_bulanan ?? 0);
            $groupedData[$barangName]['summary']['pphBulanan']   += floatval($row->pph_bulanan ?? 0);
            $groupedData[$barangName]['summary']['totalBulanan'] += floatval($row->nilai_total_bulanan ?? 0);
            $groupedData[$barangName]['summary']['dppTambahan']  += floatval($row->dpp_tambahan ?? 0);
            $groupedData[$barangName]['summary']['pphTambahan']  += floatval($row->pph_tambahan ?? 0);
            $groupedData[$barangName]['summary']['totalTambahan'] += floatval($row->nilai_total_tambahan ?? 0);
            $groupedData[$barangName]['summary']['totalRow']     += $row->totalRow;
        }

        // Inisialisasi Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pendapatan Supplier');

        $rowNum = 1;
        $sheet->setCellValue("A{$rowNum}", "Laporan Pendapatan Supplier Lokal BB");
        $sheet->mergeCells("A{$rowNum}:V{$rowNum}");
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true)->setSize(14);
        $rowNum += 2;

        // Header multi-row
        $sheet->setCellValue("A{$rowNum}", "No")->mergeCells("A{$rowNum}:A" . ($rowNum + 1));
        $sheet->setCellValue("B{$rowNum}", "Supplier")->mergeCells("B{$rowNum}:B" . ($rowNum + 1));
        $sheet->setCellValue("C{$rowNum}", "No PO")->mergeCells("C{$rowNum}:C" . ($rowNum + 1));
        $sheet->setCellValue("D{$rowNum}", "Tgl PO")->mergeCells("D{$rowNum}:D" . ($rowNum + 1));
        $sheet->setCellValue("E{$rowNum}", "Department")->mergeCells("E{$rowNum}:E" . ($rowNum + 1));
        $sheet->setCellValue("F{$rowNum}", "Gudang")->mergeCells("F{$rowNum}:F" . ($rowNum + 1));
        $sheet->setCellValue("G{$rowNum}", "Qty")->mergeCells("G{$rowNum}:G" . ($rowNum + 1));
        $sheet->setCellValue("H{$rowNum}", "Satuan")->mergeCells("H{$rowNum}:H" . ($rowNum + 1));
        $sheet->setCellValue("I{$rowNum}", "Unit")->mergeCells("I{$rowNum}:I" . ($rowNum + 1));

        $sheet->setCellValue("J{$rowNum}", "Umum")->mergeCells("J{$rowNum}:L{$rowNum}");
        $sheet->setCellValue("M{$rowNum}", "Harian")->mergeCells("M{$rowNum}:O{$rowNum}");
        $sheet->setCellValue("P{$rowNum}", "Bulanan")->mergeCells("P{$rowNum}:R{$rowNum}");
        $sheet->setCellValue("S{$rowNum}", "Tambahan")->mergeCells("S{$rowNum}:U{$rowNum}");
        $sheet->setCellValue("V{$rowNum}", "Total")->mergeCells("V{$rowNum}:V" . ($rowNum + 1));

        $sheet->setCellValue("J" . ($rowNum + 1), "DPP");
        $sheet->setCellValue("K" . ($rowNum + 1), "PPh");
        $sheet->setCellValue("L" . ($rowNum + 1), "Dibayarkan");

        $sheet->setCellValue("M" . ($rowNum + 1), "DPP");
        $sheet->setCellValue("N" . ($rowNum + 1), "PPh");
        $sheet->setCellValue("O" . ($rowNum + 1), "Dibayarkan");

        $sheet->setCellValue("P" . ($rowNum + 1), "DPP");
        $sheet->setCellValue("Q" . ($rowNum + 1), "PPh");
        $sheet->setCellValue("R" . ($rowNum + 1), "Dibayarkan");

        $sheet->setCellValue("S" . ($rowNum + 1), "DPP");
        $sheet->setCellValue("T" . ($rowNum + 1), "PPh");
        $sheet->setCellValue("U" . ($rowNum + 1), "Dibayarkan");

        $sheet->getStyle("A{$rowNum}:V" . ($rowNum + 1))
            ->getFont()->setBold(true);
        $sheet->getStyle("A{$rowNum}:V" . ($rowNum + 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $rowNum += 2;

        $cols = range('A', 'V');
        $grandTotals = [
            'qty' => 0,
            'dppUmum' => 0,
            'pphUmum' => 0,
            'totalUmum' => 0,
            'dppHarian' => 0,
            'pphHarian' => 0,
            'totalHarian' => 0,
            'dppBulanan' => 0,
            'pphBulanan' => 0,
            'totalBulanan' => 0,
            'dppTambahan' => 0,
            'pphTambahan' => 0,
            'totalTambahan' => 0,
            'totalRow' => 0
        ];

        // Loop group per barang
        foreach ($groupedData as $barangName => $group) {
            $sheet->setCellValue("A{$rowNum}", "Bahan Baku: " . $barangName);
            $sheet->mergeCells("A{$rowNum}:V{$rowNum}");
            $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
            $rowNum++;

            $no = 1;
            foreach ($group['data'] as $row) {
                $idx = 0;
                $sheet->setCellValue($cols[$idx++] . $rowNum, $no++);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->supplierName);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->poNum);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->poDate);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->divisiName);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->warehouseName);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->qtyPO);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->satuanName);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->companyName);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->dpp_umum);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->pph_umum);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->nilai_total_umum);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->dpp_harian);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->pph_harian);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->nilai_total_harian);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->dpp_bulanan);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->pph_bulanan);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->nilai_total_bulanan);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->dpp_tambahan);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->pph_tambahan);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->nilai_total_tambahan);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row->totalRow);
                $rowNum++;
            }

            // Subtotal
            $sheet->setCellValue("A{$rowNum}", "TOTAL " . strtoupper($barangName));
            $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
            $sheet->getStyle("A{$rowNum}:V{$rowNum}")->getFont()->setBold(true);

            $idx = 6; // start di kolom G
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['totalQtyPO']);
            $idx++; // skip Satuan
            $idx++; // skip Unit
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['dppUmum']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['pphUmum']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['totalUmum']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['dppHarian']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['pphHarian']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['totalHarian']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['dppBulanan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['pphBulanan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['totalBulanan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['dppTambahan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['pphTambahan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['totalTambahan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $group['summary']['totalRow']);
            $rowNum++;

            // Tambah ke grand total
            foreach ($grandTotals as $k => &$v) {
                if ($k == 'qty') {
                    $v += $group['summary']['totalQtyPO'];
                } else {
                    $v += $group['summary'][$k] ?? 0;
                }
            }
        }

        // Grand Total
        $sheet->setCellValue("A{$rowNum}", "GRAND TOTAL");
        $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
        $sheet->getStyle("A{$rowNum}:V{$rowNum}")->getFont()->setBold(true);

        $idx = 6; // start di G
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['qty']);
        $idx++; // skip Satuan
        $idx++; // skip Unit
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['dppUmum']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['pphUmum']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['totalUmum']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['dppHarian']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['pphHarian']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['totalHarian']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['dppBulanan']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['pphBulanan']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['totalBulanan']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['dppTambahan']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['pphTambahan']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['totalTambahan']);
        $sheet->setCellValue($cols[$idx++] . $rowNum, $grandTotals['totalRow']);

        // Output Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Laporan_Pendapatan_Supplier_Lokal_BB.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $writer->save("php://output");
        exit();
    }

    public function exportPDFPendapatanSupplier()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            // 'rm_purchase_orders.is_posted' => '1',
            // 'rm_purchase_orders.status_penerimaan' => '1',
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => 'rm_purchase_orders.po_date, divisis.id',
            "sortType"     => $this->request->getGet("sortType"),
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // Hitung total sama persis
        $totals = [
            'dppUmum' => 0,
            'pphUmum' => 0,
            'totalUmum' => 0,
            'dppHarian' => 0,
            'pphHarian' => 0,
            'totalHarian' => 0,
            'dppBulanan' => 0,
            'pphBulanan' => 0,
            'totalBulanan' => 0,
            'dppTambahan' => 0,
            'pphTambahan' => 0,
            'totalTambahan' => 0,
            'totalRow' => 0
        ];

        foreach ($allData as $row) {
            $totals['dppUmum']       += floatval($row->dpp_umum ?? 0);
            $totals['pphUmum']       += floatval($row->pph_umum ?? 0);
            $totals['totalUmum']     += floatval($row->nilai_total_umum ?? 0);
            $totals['dppHarian']     += floatval($row->dpp_harian ?? 0);
            $totals['pphHarian']     += floatval($row->pph_harian ?? 0);
            $totals['totalHarian']   += floatval($row->nilai_total_harian ?? 0);
            $totals['dppBulanan']    += floatval($row->dpp_bulanan ?? 0);
            $totals['pphBulanan']    += floatval($row->pph_bulanan ?? 0);
            $totals['totalBulanan']  += floatval($row->nilai_total_bulanan ?? 0);
            $totals['dppTambahan']   += floatval($row->dpp_tambahan ?? 0);
            $totals['pphTambahan']   += floatval($row->pph_tambahan ?? 0);
            $totals['totalTambahan'] += floatval($row->nilai_total_tambahan ?? 0);

            $totals['totalRow']      += floatval($row->nilai_total_umum ?? 0)
                + floatval($row->nilai_total_harian ?? 0)
                + floatval($row->nilai_total_bulanan ?? 0)
                + floatval($row->nilai_total_tambahan ?? 0);
        }

        $data = [
            'header' => "Laporan Pendapatan Supplier",
            'tanggalAwal' => $this->request->getVar('dateStart'),
            'tanggalAkhir' => $this->request->getVar('dateEnd'),
            'poNo' => $addCondition['poNo'] ?? 'ALL',
            'data' => $allData,
            'footerTotals' => $totals
        ];

        $domPdf = new Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/PendapatanSupplier/print', $data));
        $domPdf->setPaper('a4', 'landscape');
        $domPdf->render();
        $domPdf->stream('Pendapatan Supplier', array("Attachment" => false));
        exit();
    }

    public function laporanPendapatanSupplierPembelian()
    {
        $data = [
            'getPoNo' => $this->RMPurchaseOrderModel->select('id ,po_no')->where('deletedAt', NULL)->where('is_posted', '1')->where('company_id', $this->this_company_id)->findAll(),
            'getSupplier' => $this->supplierModel->where('deletedAt', NULL)->where('type', 'BAHAN BAKU')->findAll(),
            'getWarehouse' => $this->warehousesModel->get_by_company_id($this->this_company_id),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
            'getDivisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('Laporan/SupplierLokalBB/PendapatanSupplierPembelian/index', $data);
    }

    public function exportPendapatanSupplierLokalBBToExcelPembelian()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => 'rm_purchase_orders.po_date, divisis.id',
            "sortType"     => $this->request->getGet("sortType"),
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        // === Ambil semua data tanpa paging ===
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // === Susun nested: Divisi -> Gudang -> Barang ===
        $nestedData = [];
        foreach ($allData as $row) {
            $divisi = $row->divisiName ?? "LAINNYA";
            $gudang = $row->warehouseName ?? "LAINNYA";
            $barang = $row->barangName ?? "LAINNYA";

            $totalRow = floatval($row->nilai_total_umum ?? 0)
                + floatval($row->nilai_total_harian ?? 0)
                + floatval($row->nilai_total_bulanan ?? 0)
                + floatval($row->nilai_total_tambahan ?? 0);

            $nestedData[$divisi][$gudang][$barang]['data'][] = [
                'supplierName' => $row->supplierName,
                'poNum'        => $row->poNum,
                'poDate'       => $row->poDate,
                'qtyPO'        => floatval($row->qtyPO),
                'satuanName'   => $row->satuanName,
                'nilai_total_bulanan' => floatval($row->nilai_total_bulanan),
                'totalRow'     => $totalRow,
            ];

            if (!isset($nestedData[$divisi][$gudang][$barang]['summary'])) {
                $nestedData[$divisi][$gudang][$barang]['summary'] = [
                    'totalQtyPO'   => 0,
                    'totalBulanan' => 0,
                    'totalRow'     => 0,
                ];
            }

            $nestedData[$divisi][$gudang][$barang]['summary']['totalQtyPO']   += floatval($row->qtyPO);
            $nestedData[$divisi][$gudang][$barang]['summary']['totalBulanan'] += floatval($row->nilai_total_bulanan);
            $nestedData[$divisi][$gudang][$barang]['summary']['totalRow']     += $totalRow;
        }

        // === Inisialisasi Excel ===
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pendapatan Supplier');

        $rowNum = 1;
        $sheet->setCellValue("A{$rowNum}", "Laporan Pendapatan Supplier Lokal BB");
        $sheet->mergeCells("A{$rowNum}:H{$rowNum}");
        $sheet->getStyle("A{$rowNum}")
            ->getFont()
            ->setBold(true)
            ->setSize(14);
        $sheet->getStyle("A{$rowNum}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $rowNum = 2;
        $sheet->setCellValue("A{$rowNum}", "Tanggal: " . ($addCondition['dateStart'] ? date('d/m/Y', strtotime($addCondition['dateStart'])) : 'ALL') . " s/d " . ($addCondition['dateEnd'] ? date('d/m/Y', strtotime($addCondition['dateEnd'])) : 'ALL'));
        $sheet->mergeCells("A{$rowNum}:H{$rowNum}");
        $sheet->getStyle("A{$rowNum}")
            ->getFont()
            ->setBold(true)
            ->setSize(12);
        $sheet->getStyle("A{$rowNum}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $rowNum += 2;

        $grandTotals = [
            'qty' => 0,
            'totalBulanan' => 0,
            'totalRow' => 0,
        ];

        foreach ($nestedData as $divisiName => $gudangList) {

            $deptTotals = ['qty' => 0, 'totalBulanan' => 0, 'totalRow' => 0];

            foreach ($gudangList as $warehouseName => $barangList) {

                $whTotals = ['qty' => 0, 'totalBulanan' => 0, 'totalRow' => 0];

                foreach ($barangList as $barangName => $group) {

                    // === 3 Baris Judul (selalu muncul di setiap bahan baku) ===
                    $sheet->setCellValue("A{$rowNum}", "Department : {$divisiName}");
                    $sheet->mergeCells("A{$rowNum}:H{$rowNum}");
                    $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
                    $rowNum++;

                    $sheet->setCellValue("A{$rowNum}", "Gudang     : {$warehouseName}");
                    $sheet->mergeCells("A{$rowNum}:H{$rowNum}");
                    $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
                    $rowNum++;

                    $sheet->setCellValue("A{$rowNum}", "Bahan Baku : {$barangName}");
                    $sheet->mergeCells("A{$rowNum}:H{$rowNum}");
                    $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
                    $rowNum++;

                    // === Header tabel ===
                    $headers = ['No', 'Supplier', 'Nomor PO', 'Tanggal PO', 'Qty', 'Satuan', 'Harga Bulanan', 'Total'];
                    $col = 'A';
                    foreach ($headers as $header) {
                        $sheet->setCellValue("{$col}{$rowNum}", $header);
                        $col++;
                    }
                    $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getFont()->setBold(true);
                    $sheet->getStyle("A{$rowNum}:H{$rowNum}")
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $rowNum++;

                    // === Isi data ===
                    $no = 1;
                    foreach ($group['data'] as $row) {
                        $sheet->setCellValue("A{$rowNum}", $no++);
                        $sheet->setCellValue("B{$rowNum}", $row['supplierName']);
                        $sheet->setCellValue("C{$rowNum}", $row['poNum']);
                        $sheet->setCellValue("D{$rowNum}", $row['poDate']);
                        $sheet->setCellValue("E{$rowNum}", $row['qtyPO']);
                        $sheet->setCellValue("F{$rowNum}", $row['satuanName']);
                        $sheet->setCellValue("G{$rowNum}", $row['nilai_total_bulanan']);
                        $sheet->setCellValue("H{$rowNum}", $row['totalRow']);
                        $rowNum++;
                    }

                    // === Subtotal per bahan baku ===
                    $sheet->setCellValue("A{$rowNum}", "Total {$barangName}");
                    $sheet->mergeCells("A{$rowNum}:D{$rowNum}");
                    $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getFont()->setBold(true);
                    $sheet->setCellValue("E{$rowNum}", $group['summary']['totalQtyPO']);
                    $sheet->setCellValue("G{$rowNum}", $group['summary']['totalBulanan']);
                    $sheet->setCellValue("H{$rowNum}", $group['summary']['totalRow']);
                    $rowNum += 2;

                    // Tambah ke total gudang
                    $whTotals['qty'] += $group['summary']['totalQtyPO'];
                    $whTotals['totalBulanan'] += $group['summary']['totalBulanan'];
                    $whTotals['totalRow'] += $group['summary']['totalRow'];
                }

                // Tambah ke total department
                $deptTotals['qty'] += $whTotals['qty'];
                $deptTotals['totalBulanan'] += $whTotals['totalBulanan'];
                $deptTotals['totalRow'] += $whTotals['totalRow'];
            }

            // Tambah ke grand total
            $grandTotals['qty'] += $deptTotals['qty'];
            $grandTotals['totalBulanan'] += $deptTotals['totalBulanan'];
            $grandTotals['totalRow'] += $deptTotals['totalRow'];
        }

        // === Format angka & lebar kolom ===
        $sheet->getStyle("E2:E{$rowNum}")
            ->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("G2:H{$rowNum}")
            ->getNumberFormat()->setFormatCode('#,##0.00');

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // === Output Excel ===
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Laporan_Pendapatan_Supplier_Lokal_BB.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $writer->save("php://output");
        exit();
    }

    public function exportPDFPendapatanSupplierPembelian()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            // 'rm_purchase_orders.is_posted' => '1',
            // 'rm_purchase_orders.status_penerimaan' => '1',
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => 'rm_purchase_orders.po_date, divisis.id',
            "sortType"     => $this->request->getGet("sortType"),
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // Hitung total sama persis
        $totals = [
            'dppUmum' => 0,
            'pphUmum' => 0,
            'totalUmum' => 0,
            'dppHarian' => 0,
            'pphHarian' => 0,
            'totalHarian' => 0,
            'dppBulanan' => 0,
            'pphBulanan' => 0,
            'totalBulanan' => 0,
            'dppTambahan' => 0,
            'pphTambahan' => 0,
            'totalTambahan' => 0,
            'totalRow' => 0
        ];

        foreach ($allData as $row) {
            $totals['dppUmum']       += floatval($row->dpp_umum ?? 0);
            $totals['pphUmum']       += floatval($row->pph_umum ?? 0);
            $totals['totalUmum']     += floatval($row->nilai_total_umum ?? 0);
            $totals['dppHarian']     += floatval($row->dpp_harian ?? 0);
            $totals['pphHarian']     += floatval($row->pph_harian ?? 0);
            $totals['totalHarian']   += floatval($row->nilai_total_harian ?? 0);
            $totals['dppBulanan']    += floatval($row->dpp_bulanan ?? 0);
            $totals['pphBulanan']    += floatval($row->pph_bulanan ?? 0);
            $totals['totalBulanan']  += floatval($row->nilai_total_bulanan ?? 0);
            $totals['dppTambahan']   += floatval($row->dpp_tambahan ?? 0);
            $totals['pphTambahan']   += floatval($row->pph_tambahan ?? 0);
            $totals['totalTambahan'] += floatval($row->nilai_total_tambahan ?? 0);

            $totals['totalRow']      += floatval($row->nilai_total_umum ?? 0)
                + floatval($row->nilai_total_harian ?? 0)
                + floatval($row->nilai_total_bulanan ?? 0)
                + floatval($row->nilai_total_tambahan ?? 0);
        }

        $data = [
            'header' => "Laporan Pendapatan Supplier",
            'tanggalAwal' => $this->request->getVar('dateStart'),
            'tanggalAkhir' => $this->request->getVar('dateEnd'),
            'poNo' => $addCondition['poNo'] ?? 'ALL',
            'data' => $allData,
            'footerTotals' => $totals
        ];

        $domPdf = new Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/PendapatanSupplierPembelian/print', $data));
        $domPdf->setPaper('a4', 'landscape');
        $domPdf->render();
        $domPdf->stream('Pendapatan Supplier', array("Attachment" => false));
        exit();
    }

    public function laporanRekapAllSupplier()
    {
        $data = [
            'getSupplier' => $this->supplierModel->where('deletedAt', NULL)->where('type', 'BAHAN BAKU')->findAll(),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
            'getDivisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('Laporan/SupplierLokalBB/RekapAllSupplier/index', $data);
    }

    public function allLaporanRekapAllSupplier()
    {
        $pageSize = $this->request->getGet("length") ?? 25;
        $currentPage = ($this->request->getGet("start") / $pageSize) + 1;
        $start = ($currentPage - 1) * $pageSize;

        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => $this->request->getGet("sort") ?? 'poDate',
            "sortType"     => $this->request->getGet("sortType") ?? 'asc',
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        $grouped = [];
        foreach ($allData as $row) {
            $supplier = $row->supplierName ?? '-';
            $barang   = $row->barangName ?? '-';
            $key = $supplier . '||' . $barang;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'supplierName' => $supplier,
                    'divisiName'   => $row->divisiName ?? '-',
                    'barangName'   => $barang,
                    'satuanName'   => $row->satuanName ?? '-',
                    'qtyPO'        => 0,
                    'dppUmum'      => 0,
                    'pphUmum'      => 0,
                    'totalUmum'    => 0,
                    'dppHarian'    => 0,
                    'pphHarian'    => 0,
                    'totalHarian'  => 0,
                    'dppBulanan'   => 0,
                    'pphBulanan'   => 0,
                    'totalBulanan' => 0,
                    'dppSubsidi'   => 0,
                    'pphSubsidi'   => 0,
                    'totalSubsidi' => 0,
                    'totalRow'     => 0,
                ];
            }

            $grouped[$key]['qtyPO']        += floatval($row->qtyPO ?? 0);
            $grouped[$key]['dppUmum']      += floatval($row->dpp_umum ?? 0);
            $grouped[$key]['pphUmum']      += floatval($row->pph_umum ?? 0);
            $grouped[$key]['totalUmum']    += floatval($row->nilai_total_umum ?? 0);
            $grouped[$key]['dppHarian']    += floatval($row->dpp_harian ?? 0);
            $grouped[$key]['pphHarian']    += floatval($row->pph_harian ?? 0);
            $grouped[$key]['totalHarian']  += floatval($row->nilai_total_harian ?? 0);
            $grouped[$key]['dppBulanan']   += floatval($row->dpp_bulanan ?? 0);
            $grouped[$key]['pphBulanan']   += floatval($row->pph_bulanan ?? 0);
            $grouped[$key]['totalBulanan'] += floatval($row->nilai_total_bulanan ?? 0);
            $grouped[$key]['dppSubsidi']   += floatval($row->dpp_tambahan ?? 0);
            $grouped[$key]['pphSubsidi']   += floatval($row->pph_tambahan ?? 0);
            $grouped[$key]['totalSubsidi'] += floatval($row->nilai_total_tambahan ?? 0);
            $grouped[$key]['totalRow']     += (
                floatval($row->nilai_total_umum ?? 0) +
                floatval($row->nilai_total_harian ?? 0) +
                floatval($row->nilai_total_bulanan ?? 0) +
                floatval($row->nilai_total_tambahan ?? 0)
            );
        }

        ksort($grouped);

        $footer = [
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
            'dppSubsidi' => 0,
            'pphSubsidi' => 0,
            'totalSubsidi' => 0,
            'totalRow' => 0
        ];
        foreach ($grouped as $g) {
            foreach ($footer as $k => &$v) $v += floatval($g[$k]);
        }
        foreach ($footer as &$v) $v = number_format($v, 2, '.', ',');

        $totalRecords = count($grouped);
        $groupedKeys = array_keys($grouped);
        $paginatedKeys = array_slice($groupedKeys, $start, $pageSize, true);

        $flatData = [];
        $no = $start + 1;
        foreach ($paginatedKeys as $key) {
            $g = $grouped[$key];
            $flatData[] = [
                'no'            => $no++,
                'supplierName'  => $g['supplierName'],
                'divisiName'    => $g['divisiName'],
                'barangName'    => $g['barangName'],
                'satuanName'    => $g['satuanName'],
                'qtyPO'         => number_format($g['qtyPO'], 2, '.', ','),
                'dppUmum'       => number_format($g['dppUmum'], 2, '.', ','),
                'pphUmum'       => number_format($g['pphUmum'], 2, '.', ','),
                'totalUmum'     => number_format($g['totalUmum'], 2, '.', ','),
                'dppHarian'     => number_format($g['dppHarian'], 2, '.', ','),
                'pphHarian'     => number_format($g['pphHarian'], 2, '.', ','),
                'totalHarian'   => number_format($g['totalHarian'], 2, '.', ','),
                'dppBulanan'    => number_format($g['dppBulanan'], 2, '.', ','),
                'pphBulanan'    => number_format($g['pphBulanan'], 2, '.', ','),
                'totalBulanan'  => number_format($g['totalBulanan'], 2, '.', ','),
                'dppSubsidi'    => number_format($g['dppSubsidi'], 2, '.', ','),
                'pphSubsidi'    => number_format($g['pphSubsidi'], 2, '.', ','),
                'totalSubsidi'  => number_format($g['totalSubsidi'], 2, '.', ','),
                'totalRow'      => number_format($g['totalRow'], 2, '.', ','),
            ];
        }

        echo json_encode([
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $flatData,
            "footerTotals" => $footer
        ]);
    }

    public function exportPDFLaporanRekapAllSupplier()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => $this->request->getGet("sort") ?? 'poDate',
            "sortType"     => $this->request->getGet("sortType") ?? 'asc',
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        $grouped = [];
        $no = 1;

        foreach ($allData as $row) {
            $barang   = $row->barangName ?? '-';
            $supplier = $row->supplierName ?? '-';
            $spek     = $row->spekName ?? '-';

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$entry) {
                // 💡 tambahkan spekName dalam kondisi pengecekan
                if ($entry['supplierName'] === $supplier && $entry['spekName'] === $spek) {
                    $entry['qtyPO']        += floatval($row->qtyPO ?? 0);
                    $entry['dppUmum']      += floatval($row->dpp_umum ?? 0);
                    $entry['pphUmum']      += floatval($row->pph_umum ?? 0);
                    $entry['totalUmum']    += floatval($row->nilai_total_umum ?? 0);
                    $entry['dppHarian']    += floatval($row->dpp_harian ?? 0);
                    $entry['pphHarian']    += floatval($row->pph_harian ?? 0);
                    $entry['totalHarian']  += floatval($row->nilai_total_harian ?? 0);
                    $entry['dppBulanan']   += floatval($row->dpp_bulanan ?? 0);
                    $entry['pphBulanan']   += floatval($row->pph_bulanan ?? 0);
                    $entry['totalBulanan'] += floatval($row->nilai_total_bulanan ?? 0);
                    $entry['dppSubsidi']   += floatval($row->dpp_tambahan ?? 0);
                    $entry['pphSubsidi']   += floatval($row->pph_tambahan ?? 0);
                    $entry['totalSubsidi'] += floatval($row->nilai_total_tambahan ?? 0);
                    $entry['totalRow']     += (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        floatval($row->nilai_total_tambahan ?? 0)
                    );
                    $found = true;
                    break;
                }
            }

            // jika belum ada kombinasi supplier + spek, buat baris baru
            if (!$found) {
                $grouped[$barang][] = [
                    'no'            => $no++,
                    'supplierName'  => $supplier,
                    'divisiName'    => $row->divisiName ?? '-',
                    'barangName'    => $barang,
                    'spekName'      => $spek,
                    'satuanName'    => $row->satuanName ?? '-',
                    'qtyPO'         => floatval($row->qtyPO ?? 0),
                    'dppUmum'       => floatval($row->dpp_umum ?? 0),
                    'pphUmum'       => floatval($row->pph_umum ?? 0),
                    'totalUmum'     => floatval($row->nilai_total_umum ?? 0),
                    'dppHarian'     => floatval($row->dpp_harian ?? 0),
                    'pphHarian'     => floatval($row->pph_harian ?? 0),
                    'totalHarian'   => floatval($row->nilai_total_harian ?? 0),
                    'dppBulanan'    => floatval($row->dpp_bulanan ?? 0),
                    'pphBulanan'    => floatval($row->pph_bulanan ?? 0),
                    'totalBulanan'  => floatval($row->nilai_total_bulanan ?? 0),
                    'dppSubsidi'    => floatval($row->dpp_tambahan ?? 0),
                    'pphSubsidi'    => floatval($row->pph_tambahan ?? 0),
                    'totalSubsidi'  => floatval($row->nilai_total_tambahan ?? 0),
                    'totalRow'      => (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        floatval($row->nilai_total_tambahan ?? 0)
                    ),
                ];
            }
        }

        ksort($grouped);

        $data = [
            'header' => "Laporan Rekap All Supplier",
            'tanggalAwal' => $addCondition['dateStart'],
            'tanggalAkhir' => $addCondition['dateEnd'],
            'groupedData' => $grouped,
        ];

        // return view('Laporan/SupplierLokalBB/RekapAllSupplier/print', $data);

        $domPdf = new \Dompdf\Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapAllSupplier/print', $data));
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream('Rekap_All_Supplier.pdf', ["Attachment" => false]);
    }

    public function exportExcelLaporanRekapAllSupplier()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => $this->request->getGet("sort") ?? 'poDate',
            "sortType"     => $this->request->getGet("sortType") ?? 'asc',
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        );

        $rawData = $dataBBLokal['data'];

        // --- Grouping per barang, tetap tampil baris baru kalau spek beda ---
        $grouped = [];
        foreach ($rawData as $row) {
            $barang   = $row->barangName ?? '-';
            $supplier = $row->supplierName ?? '-';
            $divisi   = $row->divisiName ?? '-';
            $spek     = $row->spekName ?? '-';
            $satuan   = $row->satuanName ?? '-';

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$item) {
                if (
                    $item['Supplier'] === $supplier &&
                    $item['Divisi'] === $divisi &&
                    $item['Spek'] === $spek
                ) {
                    $item['Qty']           += floatval($row->qtyPO ?? 0);
                    $item['DPP Harian']    += floatval($row->dpp_umum ?? 0);
                    $item['PPh Harian']    += floatval($row->pph_umum ?? 0);
                    $item['Total Harian']  += floatval($row->nilai_total_umum ?? 0);
                    $item['DPP Tamb Har']  += floatval($row->dpp_harian ?? 0);
                    $item['PPh Tamb Har']  += floatval($row->pph_harian ?? 0);
                    $item['Total Tamb Har'] += floatval($row->nilai_total_harian ?? 0);
                    $item['DPP Bulanan']   += floatval($row->dpp_bulanan ?? 0);
                    $item['PPh Bulanan']   += floatval($row->pph_bulanan ?? 0);
                    $item['Total Bulanan'] += floatval($row->nilai_total_bulanan ?? 0);
                    $item['DPP Langsung']  += floatval($row->dpp_tambahan ?? 0);
                    $item['PPh Langsung']  += floatval($row->pph_tambahan ?? 0);
                    $item['Total Langsung'] += floatval($row->nilai_total_tambahan ?? 0);
                    $item['Total']         += (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        floatval($row->nilai_total_tambahan ?? 0)
                    );
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $grouped[$barang][] = [
                    'Supplier'        => $supplier,
                    'Divisi'          => $divisi,
                    'Barang'          => $barang,
                    'Spek'            => $spek,
                    'Satuan'          => $satuan,
                    'Qty'             => floatval($row->qtyPO ?? 0),
                    'DPP Harian'      => floatval($row->dpp_umum ?? 0),
                    'PPh Harian'      => floatval($row->pph_umum ?? 0),
                    'Total Harian'    => floatval($row->nilai_total_umum ?? 0),
                    'DPP Tamb Har'    => floatval($row->dpp_harian ?? 0),
                    'PPh Tamb Har'    => floatval($row->pph_harian ?? 0),
                    'Total Tamb Har'  => floatval($row->nilai_total_harian ?? 0),
                    'DPP Bulanan'     => floatval($row->dpp_bulanan ?? 0),
                    'PPh Bulanan'     => floatval($row->pph_bulanan ?? 0),
                    'Total Bulanan'   => floatval($row->nilai_total_bulanan ?? 0),
                    'DPP Langsung'    => floatval($row->dpp_tambahan ?? 0),
                    'PPh Langsung'    => floatval($row->pph_tambahan ?? 0),
                    'Total Langsung'  => floatval($row->nilai_total_tambahan ?? 0),
                    'Total'           => (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        floatval($row->nilai_total_tambahan ?? 0)
                    ),
                ];
            }
        }

        ksort($grouped);

        // --- Generate Excel ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap All Supplier');
        $row = 1;

        // Header laporan
        $sheet->setCellValue("A{$row}", "Laporan Rekap All Supplier");
        $sheet->mergeCells("A{$row}:T{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", "Tanggal: " . ($addCondition["dateStart"] ?? '') . " s/d " . ($addCondition["dateEnd"] ?? ''));
        $sheet->mergeCells("A{$row}:T{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;
        $row += 2;

        foreach ($grouped as $barangName => $rows) {
            // Judul bahan baku
            $sheet->setCellValue("A{$row}", "Bahan Baku: " . strtoupper($barangName));
            $sheet->mergeCells("A{$row}:T{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("A{$row}")->getFill()->setFillType('solid')->getStartColor()->setARGB('FF666666');
            $row++;

            // === Header dua baris ===
            $sheet->setCellValue("A{$row}", "No");
            $sheet->setCellValue("B{$row}", "Supplier");
            $sheet->setCellValue("C{$row}", "Divisi");
            $sheet->setCellValue("D{$row}", "Barang");
            $sheet->setCellValue("E{$row}", "Spek");
            $sheet->setCellValue("F{$row}", "Satuan");
            $sheet->setCellValue("G{$row}", "Qty");

            $sheet->setCellValue("H{$row}", "Harian");
            $sheet->mergeCells("H{$row}:J{$row}");
            $sheet->setCellValue("K{$row}", "Tambahan Harian");
            $sheet->mergeCells("K{$row}:M{$row}");
            $sheet->setCellValue("N{$row}", "Tambahan Bulanan");
            $sheet->mergeCells("N{$row}:P{$row}");
            $sheet->setCellValue("Q{$row}", "Tambahan Langsung");
            $sheet->mergeCells("Q{$row}:S{$row}");
            $sheet->setCellValue("T{$row}", "TOTAL");
            $sheet->mergeCells("T{$row}:T" . ($row + 1));

            // Subheader
            $row2 = $row + 1;
            $sheet->setCellValue("H{$row2}", "DPP");
            $sheet->setCellValue("I{$row2}", "PPh");
            $sheet->setCellValue("J{$row2}", "Dibayar");
            $sheet->setCellValue("K{$row2}", "DPP");
            $sheet->setCellValue("L{$row2}", "PPh");
            $sheet->setCellValue("M{$row2}", "Dibayar");
            $sheet->setCellValue("N{$row2}", "DPP");
            $sheet->setCellValue("O{$row2}", "PPh");
            $sheet->setCellValue("P{$row2}", "Dibayar");
            $sheet->setCellValue("Q{$row2}", "DPP");
            $sheet->setCellValue("R{$row2}", "PPh");
            $sheet->setCellValue("S{$row2}", "Dibayar");

            foreach (range('A', 'G') as $col) {
                $sheet->mergeCells("{$col}{$row}:{$col}{$row2}");
            }

            $sheet->getStyle("A{$row}:T{$row2}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:T{$row2}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:T{$row2}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row = $row2 + 1;

            // === Isi data ===
            $no = 1;
            $totals = array_fill_keys(['Qty', 'DPP Harian', 'PPh Harian', 'Total Harian', 'DPP Tamb Har', 'PPh Tamb Har', 'Total Tamb Har', 'DPP Bulanan', 'PPh Bulanan', 'Total Bulanan', 'DPP Langsung', 'PPh Langsung', 'Total Langsung', 'Total'], 0);

            foreach ($rows as $r) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValue("B{$row}", $r['Supplier']);
                $sheet->setCellValue("C{$row}", $r['Divisi']);
                $sheet->setCellValue("D{$row}", $r['Barang']);
                $sheet->setCellValue("E{$row}", $r['Spek']);
                $sheet->setCellValue("F{$row}", $r['Satuan']);
                $sheet->setCellValue("G{$row}", $r['Qty']);
                $sheet->setCellValue("H{$row}", $r['DPP Harian']);
                $sheet->setCellValue("I{$row}", $r['PPh Harian']);
                $sheet->setCellValue("J{$row}", $r['Total Harian']);
                $sheet->setCellValue("K{$row}", $r['DPP Tamb Har']);
                $sheet->setCellValue("L{$row}", $r['PPh Tamb Har']);
                $sheet->setCellValue("M{$row}", $r['Total Tamb Har']);
                $sheet->setCellValue("N{$row}", $r['DPP Bulanan']);
                $sheet->setCellValue("O{$row}", $r['PPh Bulanan']);
                $sheet->setCellValue("P{$row}", $r['Total Bulanan']);
                $sheet->setCellValue("Q{$row}", $r['DPP Langsung']);
                $sheet->setCellValue("R{$row}", $r['PPh Langsung']);
                $sheet->setCellValue("S{$row}", $r['Total Langsung']);
                $sheet->setCellValue("T{$row}", $r['Total']);
                $sheet->getStyle("A{$row}:T{$row}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                foreach ($totals as $k => &$v) $v += $r[$k];
                $row++;
            }

            // === Subtotal per barang ===
            $sheet->setCellValue("A{$row}", "TOTAL " . strtoupper($barangName));
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->getStyle("A{$row}:T{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:T{$row}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $col = 'G';
            foreach ($totals as $val) {
                $sheet->setCellValue($col++ . $row, $val);
            }
            $row += 2;
        }

        // Format dan output
        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("G1:T{$row}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $fileName = 'Rekap_All_Supplier_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function laporanRekapAllSupplierPembelian()
    {
        $data = [
            'getSupplier' => $this->supplierModel->where('deletedAt', NULL)->where('type', 'BAHAN BAKU')->findAll(),
            'getBarang' => $this->barangMasterModel->getBarangByType("bahan_baku"),
            'getDivisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('Laporan/SupplierLokalBB/RekapAllSupplierPembelian/index', $data);
    }

    public function exportPDFLaporanRekapAllSupplierPembelian()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => $this->request->getGet("sort") ?? 'poDate',
            "sortType"     => $this->request->getGet("sortType") ?? 'asc',
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        $grouped = [];
        $no = 1;

        foreach ($allData as $row) {
            $barang   = $row->barangName ?? '-';
            $supplier = $row->supplierName ?? '-';
            $spek     = $row->spekName ?? '-';

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$entry) {
                // 💡 tambahkan spekName dalam kondisi pengecekan
                if ($entry['supplierName'] === $supplier && $entry['spekName'] === $spek) {
                    $entry['qtyPO']        += floatval($row->qtyPO ?? 0);
                    $entry['dppUmum']      += floatval($row->dpp_umum ?? 0);
                    $entry['pphUmum']      += floatval($row->pph_umum ?? 0);
                    $entry['totalUmum']    += floatval($row->nilai_total_umum ?? 0);
                    $entry['dppHarian']    += floatval($row->dpp_harian ?? 0);
                    $entry['pphHarian']    += floatval($row->pph_harian ?? 0);
                    $entry['totalHarian']  += floatval($row->nilai_total_harian ?? 0);
                    $entry['dppBulanan']   += floatval($row->dpp_bulanan ?? 0);
                    $entry['pphBulanan']   += floatval($row->pph_bulanan ?? 0);
                    $entry['totalBulanan'] += floatval($row->nilai_total_bulanan ?? 0);
                    $entry['dppSubsidi']   += floatval($row->dpp_tambahan ?? 0);
                    $entry['pphSubsidi']   += floatval($row->pph_tambahan ?? 0);
                    $entry['totalSubsidi'] += floatval($row->nilai_total_tambahan ?? 0);
                    $entry['totalRow']     += (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        floatval($row->nilai_total_tambahan ?? 0)
                    );
                    $found = true;
                    break;
                }
            }

            // jika belum ada kombinasi supplier + spek, buat baris baru
            if (!$found) {
                $grouped[$barang][] = [
                    'no'            => $no++,
                    'supplierName'  => $supplier,
                    'divisiName'    => $row->divisiName ?? '-',
                    'barangName'    => $barang,
                    'spekName'      => $spek,
                    'satuanName'    => $row->satuanName ?? '-',
                    'qtyPO'         => floatval($row->qtyPO ?? 0),
                    'dppUmum'       => floatval($row->dpp_umum ?? 0),
                    'pphUmum'       => floatval($row->pph_umum ?? 0),
                    'totalUmum'     => floatval($row->nilai_total_umum ?? 0),
                    'dppHarian'     => floatval($row->dpp_harian ?? 0),
                    'pphHarian'     => floatval($row->pph_harian ?? 0),
                    'totalHarian'   => floatval($row->nilai_total_harian ?? 0),
                    'dppBulanan'    => floatval($row->dpp_bulanan ?? 0),
                    'pphBulanan'    => floatval($row->pph_bulanan ?? 0),
                    'totalBulanan'  => floatval($row->nilai_total_bulanan ?? 0),
                    'dppSubsidi'    => floatval($row->dpp_tambahan ?? 0),
                    'pphSubsidi'    => floatval($row->pph_tambahan ?? 0),
                    'totalSubsidi'  => floatval($row->nilai_total_tambahan ?? 0),
                    'totalRow'      => (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        floatval($row->nilai_total_tambahan ?? 0)
                    ),
                ];
            }
        }

        ksort($grouped);

        $data = [
            'header' => "Laporan Rekap All Supplier",
            'tanggalAwal' => $addCondition['dateStart'],
            'tanggalAkhir' => $addCondition['dateEnd'],
            'groupedData' => $grouped,
        ];

        // return view('Laporan/SupplierLokalBB/RekapAllSupplier/print', $data);

        $domPdf = new Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapAllSupplierPembelian/print', $data));
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream('Rekap All Supplier', ["Attachment" => false]);
        exit();
    }

    public function exportExcelLaporanRekapAllSupplierPembelian()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "sort"         => $this->request->getGet("sort") ?? 'poDate',
            "sortType"     => $this->request->getGet("sortType") ?? 'asc',
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        );

        $rawData = $dataBBLokal['data'];

        // --- Grouping per barang, tetap tampil baris baru kalau spek beda ---
        $grouped = [];
        foreach ($rawData as $row) {
            $barang   = $row->barangName ?? '-';
            $supplier = $row->supplierName ?? '-';
            $divisi   = $row->divisiName ?? '-';
            $spek     = $row->spekName ?? '-';
            $satuan   = $row->satuanName ?? '-';

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$item) {
                if (
                    $item['Supplier'] === $supplier &&
                    $item['Divisi'] === $divisi &&
                    $item['Spek'] === $spek
                ) {
                    $item['Qty']           += floatval($row->qtyPO ?? 0);
                    $item['DPP Harian']    += floatval($row->dpp_umum ?? 0);
                    $item['PPh Harian']    += floatval($row->pph_umum ?? 0);
                    $item['Total Harian']  += floatval($row->nilai_total_umum ?? 0);
                    $item['DPP Tamb Har']  += floatval($row->dpp_harian ?? 0);
                    $item['PPh Tamb Har']  += floatval($row->pph_harian ?? 0);
                    $item['Total Tamb Har'] += floatval($row->nilai_total_harian ?? 0);
                    $item['DPP Bulanan']   += floatval($row->dpp_bulanan ?? 0);
                    $item['PPh Bulanan']   += floatval($row->pph_bulanan ?? 0);
                    $item['Total Bulanan'] += floatval($row->nilai_total_bulanan ?? 0);
                    $item['DPP Langsung']  += floatval($row->dpp_tambahan ?? 0);
                    $item['PPh Langsung']  += floatval($row->pph_tambahan ?? 0);
                    $item['Total Langsung'] += floatval($row->nilai_total_tambahan ?? 0);
                    $item['Total']         += (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        floatval($row->nilai_total_tambahan ?? 0)
                    );
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $grouped[$barang][] = [
                    'Supplier'        => $supplier,
                    'Divisi'          => $divisi,
                    'Barang'          => $barang,
                    'Spek'            => $spek,
                    'Satuan'          => $satuan,
                    'Qty'             => floatval($row->qtyPO ?? 0),
                    'DPP Harian'      => floatval($row->dpp_umum ?? 0),
                    'PPh Harian'      => floatval($row->pph_umum ?? 0),
                    'Total Harian'    => floatval($row->nilai_total_umum ?? 0),
                    'DPP Tamb Har'    => floatval($row->dpp_harian ?? 0),
                    'PPh Tamb Har'    => floatval($row->pph_harian ?? 0),
                    'Total Tamb Har'  => floatval($row->nilai_total_harian ?? 0),
                    'DPP Bulanan'     => floatval($row->dpp_bulanan ?? 0),
                    'PPh Bulanan'     => floatval($row->pph_bulanan ?? 0),
                    'Total Bulanan'   => floatval($row->nilai_total_bulanan ?? 0),
                    'DPP Langsung'    => floatval($row->dpp_tambahan ?? 0),
                    'PPh Langsung'    => floatval($row->pph_tambahan ?? 0),
                    'Total Langsung'  => floatval($row->nilai_total_tambahan ?? 0),
                    'Total'           => (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        floatval($row->nilai_total_tambahan ?? 0)
                    ),
                ];
            }
        }

        ksort($grouped);

        // --- Generate Excel ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap All Supplier');
        $row = 1;

        // Header laporan
        $sheet->setCellValue("A{$row}", "Laporan Rekap All Supplier");
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", "Tanggal: " . ($addCondition["dateStart"] ?? '') . " s/d " . ($addCondition["dateEnd"] ?? ''));
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;
        $row += 2;

        foreach ($grouped as $barangName => $rows) {
            // Judul bahan baku
            $sheet->setCellValue("A{$row}", "Bahan Baku: " . strtoupper($barangName));
            $sheet->mergeCells("A{$row}:I{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("A{$row}")->getFill()->setFillType('solid')->getStartColor()->setARGB('FF666666');
            $row++;

            // === Header dua baris ===
            $sheet->setCellValue("A{$row}", "No");
            $sheet->setCellValue("B{$row}", "Supplier");
            $sheet->setCellValue("C{$row}", "Divisi");
            $sheet->setCellValue("D{$row}", "Barang");
            $sheet->setCellValue("E{$row}", "Spek");
            $sheet->setCellValue("F{$row}", "Satuan");
            $sheet->setCellValue("G{$row}", "Qty");

            $sheet->setCellValue("H{$row}", "Tambahan Bulanan");
            $sheet->setCellValue("I{$row}", "TOTAL");
            $sheet->mergeCells("I{$row}:I" . ($row + 1));

            // Subheader
            $row2 = $row + 1;
            $sheet->setCellValue("H{$row2}", "Total");

            foreach (range('A', 'G') as $col) {
                $sheet->mergeCells("{$col}{$row}:{$col}{$row2}");
            }

            $sheet->getStyle("A{$row}:I{$row2}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:I{$row2}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:I{$row2}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row = $row2 + 1;

            // === Isi data ===
            $no = 1;
            $totals = array_fill_keys(['Qty', 'Total Bulanan', 'Total'], 0);

            foreach ($rows as $r) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValue("B{$row}", $r['Supplier']);
                $sheet->setCellValue("C{$row}", $r['Divisi']);
                $sheet->setCellValue("D{$row}", $r['Barang']);
                $sheet->setCellValue("E{$row}", $r['Spek']);
                $sheet->setCellValue("F{$row}", $r['Satuan']);
                $sheet->setCellValue("G{$row}", $r['Qty']);
                $sheet->setCellValue("H{$row}", $r['Total Bulanan']);
                $sheet->setCellValue("I{$row}", $r['Total']);
                $sheet->getStyle("A{$row}:I{$row}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                foreach ($totals as $k => &$v) $v += $r[$k];
                $row++;
            }

            // === Subtotal per barang ===
            $sheet->setCellValue("A{$row}", "TOTAL " . strtoupper($barangName));
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:I{$row}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $col = 'G';
            foreach ($totals as $val) {
                $sheet->setCellValue($col++ . $row, $val);
            }
            $row += 2;
        }

        // Format dan output
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("G1:I{$row}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $fileName = 'Rekap_All_Supplier_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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
        $currentPage = ($this->request->getGet("start") / $pageSize) + 1;

        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "divisiId" => $this->request->getGet("filter_divisi"),
            "barangId" => $this->request->getGet("filter_barang"),
        ];

        $availableSort = [
            'barangName' => 'barang_master.barang_name',
            'spekName' => 'spekName',
            'bagianName' => 'bagianName',
        ];

        $dataResult = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);
        $data = $dataResult['data'];

        // ================= PERHITUNGAN SUBSIDI PER PO =================
        $totalQtyPerPO = [];
        foreach ($data as $row) {
            $poKey = $row->poNum;
            $totalQtyPerPO[$poKey] = ($totalQtyPerPO[$poKey] ?? 0) + $row->qtyPO;
        }

        $subsidiPerPO = [];
        foreach ($data as $row) {
            $poKey = $row->poNum;
            if (!isset($subsidiPerPO[$poKey])) {
                $hasNpwp = !empty($row->supplierNpwp);
                $pphMode = $row->poPPH;

                // Tentukan tarif PPh berdasarkan tanggal
                $tarifPPh = ($row->poDate <= '2025-06-30')
                    ? ['pph' => $hasNpwp ? 0.9975 : 0.995, 'pph2' => $hasNpwp ? 0.0025 : 0.005]
                    : ['pph' => 0.9975, 'pph2' => 0.0025];

                $subsidi_value = (float)$row->subsidi;
                $cong_batasan = (float)$row->cong_batasan;
                $cong_sebenarnya = (float)$row->cong_sebenarnya;
                $qtyTotal = $totalQtyPerPO[$poKey] ?? 1;

                // Hitung total subsidi untuk PO
                $totalSubsidiValue = ($subsidi_value != 0)
                    ? $subsidi_value
                    : ($cong_batasan - $cong_sebenarnya) * $qtyTotal;

                // Hitung DPP, PPh, Total Subsidi untuk PO
                if ($pphMode === "Company") {
                    $dppSubsidi = $totalSubsidiValue / $tarifPPh['pph'];
                    $pphSubsidi = $dppSubsidi * $tarifPPh['pph2'];
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } elseif ($pphMode === "Supplier") {
                    $dppSubsidi = $totalSubsidiValue;
                    $pphSubsidi = $dppSubsidi * $tarifPPh['pph2'];
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $totalSubsidiValue;
                    $pphSubsidi = 0;
                    $totalSubsidi = $dppSubsidi;
                }

                $subsidiPerPO[$poKey] = [
                    'dppSubsidi' => $dppSubsidi,
                    'pphSubsidi' => $pphSubsidi,
                    'totalSubsidi' => $totalSubsidi,
                    'tarifPPh' => $tarifPPh,
                ];
            }
        }
        // ================ END PERHITUNGAN SUBSIDI PER PO ================

        // Ubah struktur pengelompokan data
        $groupedByBarang = [];
        $totalSummary = [
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
            'subsidi' => 0,       // DPP Subsidi
            'pphSubsidi' => 0,    // PPh Subsidi
            'totalSubsidi' => 0,  // Total Subsidi
            'totalRow' => 0
        ];

        foreach ($data as $row) {
            $qty = $row->qtyPO;
            $barangName = $row->barangName;
            $pphMode = $row->poPPH;
            $poKey = $row->poNum;
            $subKey = $row->spekName . '|' . $row->divisiName;

            // Gunakan tarif PPh dari data PO
            $tarifPPh = $subsidiPerPO[$poKey]['tarifPPh'] ?? [
                'pph' => 0.9975,
                'pph2' => 0.0025
            ];

            // Fungsi perhitungan DPP, PPh, dan Total
            $hitungNilai = function ($nilai, $qty) use ($pphMode, $tarifPPh) {
                if ($pphMode === "Company") {
                    $dpp = ($nilai / $tarifPPh['pph']) * $qty;
                    $pph = $dpp * $tarifPPh['pph2'];
                    $total = $dpp - $pph;
                } elseif ($pphMode === "Supplier") {
                    $dpp = $nilai * $qty;
                    $pph = $dpp * $tarifPPh['pph2'];
                    $total = $dpp - $pph;
                } else {
                    $dpp = $nilai * $qty;
                    $pph = 0;
                    $total = $dpp;
                }
                return [$dpp, $pph, $total];
            };

            // Hitung nilai utama
            [$dppUmum, $pphUmum, $totalUmum] = $hitungNilai($row->dppUmum, $qty);
            [$dppHarian, $pphHarian, $totalHarian] = $hitungNilai($row->dppHarian, $qty);
            [$dppBulanan, $pphBulanan, $totalBulanan] = $hitungNilai($row->dppBulanan, $qty);

            // Alokasikan subsidi dari perhitungan per PO
            $poSubsidi = $subsidiPerPO[$poKey] ?? null;
            if ($poSubsidi) {
                $proporsi = $qty / $totalQtyPerPO[$poKey];
                $dppSubsidi = $poSubsidi['dppSubsidi'] * $proporsi;
                $pphSubsidi = $poSubsidi['pphSubsidi'] * $proporsi;
                $totalSubsidi = $poSubsidi['totalSubsidi'] * $proporsi;
            } else {
                $dppSubsidi = $pphSubsidi = $totalSubsidi = 0;
            }

            $totalRow = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

            // Kelompokkan data
            if (!isset($groupedByBarang[$barangName])) {
                $groupedByBarang[$barangName] = [];
            }

            if (!isset($groupedByBarang[$barangName][$subKey])) {
                $groupedByBarang[$barangName][$subKey] = [
                    'barangName' => $barangName,
                    'divisiName' => $row->divisiName,
                    'spekName' => $row->spekName,
                    'satuanName' => $row->satuanName,
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
                    'subsidi' => 0,
                    'pphSubsidi' => 0,
                    'totalSubsidi' => 0,
                    'totalRow' => 0,
                ];
            }

            // Akumulasi nilai
            $g = &$groupedByBarang[$barangName][$subKey];
            $g['qtyPO'] += $qty;
            $g['dppUmum'] += $dppUmum;
            $g['pphUmum'] += $pphUmum;
            $g['totalUmum'] += $totalUmum;
            $g['dppHarian'] += $dppHarian;
            $g['pphHarian'] += $pphHarian;
            $g['totalHarian'] += $totalHarian;
            $g['dppBulanan'] += $dppBulanan;
            $g['pphBulanan'] += $pphBulanan;
            $g['totalBulanan'] += $totalBulanan;
            $g['subsidi'] += $dppSubsidi;
            $g['pphSubsidi'] += $pphSubsidi;
            $g['totalSubsidi'] += $totalSubsidi;
            $g['totalRow'] += $totalRow;

            // Akumulasi total summary
            $totalSummary['qtyPO'] += $qty;
            $totalSummary['dppUmum'] += $dppUmum;
            $totalSummary['pphUmum'] += $pphUmum;
            $totalSummary['totalUmum'] += $totalUmum;
            $totalSummary['dppHarian'] += $dppHarian;
            $totalSummary['pphHarian'] += $pphHarian;
            $totalSummary['totalHarian'] += $totalHarian;
            $totalSummary['dppBulanan'] += $dppBulanan;
            $totalSummary['pphBulanan'] += $pphBulanan;
            $totalSummary['totalBulanan'] += $totalBulanan;
            $totalSummary['subsidi'] += $dppSubsidi;
            $totalSummary['pphSubsidi'] += $pphSubsidi;
            // $totalSummary['totalSubsidi'] += $totalSubsidi;
            $totalSummary['totalRow'] += $totalRow;
        }

        // Ubah struktur untuk pagination
        $flatData = [];
        foreach ($groupedByBarang as $barangName => $subGroups) {
            foreach ($subGroups as $subKey => $record) {
                $flatData[] = $record;
            }
        }

        // Urutkan data
        usort($flatData, function ($a, $b) {
            return strcmp($a['barangName'], $b['barangName']);
        });

        $totalGrouped = count($flatData);

        $totalSubsidiFinal = 0;
        $totalRowFinal = 0;
        foreach ($flatData as $key => $row) {
            $row['totalSubsidi'] = $row['subsidi'] - $row['pphSubsidi'];
            $row['totalRow'] = $row['totalUmum'] + $row['totalHarian'] + $row['totalBulanan'] + $row['totalSubsidi'];
            $totalSubsidiFinal += $row['totalSubsidi'];
            $totalRowFinal += $row['totalRow'];
        }

        $paginatedData = array_slice($flatData, ($currentPage - 1) * $pageSize, $pageSize);

        // Format output
        foreach ($paginatedData as $i => &$row) {
            $row['no'] = ($currentPage - 1) * $pageSize + $i + 1;
            foreach ($row as $key => $val) {
                if (is_numeric($val) && $key !== 'no') {
                    $row[$key] = number_format($val, 2, '.', ',');
                }
            }
        }

        // Format total summary
        $formattedSummary = [];
        foreach ($totalSummary as $key => $val) {
            if ($key === 'totalSubsidi') {
                $formattedSummary[$key] = number_format($totalSubsidiFinal, 2, '.', ',');
            } else if ($key === 'totalRow') {
                $formattedSummary[$key] = number_format($totalRowFinal, 2, '.', ',');
            } else {
                $formattedSummary[$key] = number_format($val, 2, '.', ',');
            }
        }

        echo json_encode([
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $totalGrouped,
            "recordsFiltered" => $totalGrouped,
            'data' => $paginatedData,
            "payload" => [
                "pageSize" => $pageSize,
                "currentPage" => $currentPage
            ],
            'totalTotalRow' => $formattedSummary['totalRow'],
            'totalSummary' => $formattedSummary,
        ]);
    }

    public function exportExcelLaporanRekapAllBarang()
    {
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $this->this_company_id,
            'rm_purchase_orders.deletedAt'  => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "divisiId"     => $this->request->getGet("filter_divisi"),
            "sort"         => 'rm_purchase_orders.po_date, divisis.id',
            "sortType"     => $this->request->getGet("sortType"),
            "barangId"     => $this->request->getGet("filter_barang"),
        ];

        $availableSort = [];

        $data = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null)['data'];

        // Hitung total qty per poNum untuk subsidi proporsional
        $totalQtyPerPO = [];
        foreach ($data as $row) {
            if (!isset($totalQtyPerPO[$row->poNum])) {
                $totalQtyPerPO[$row->poNum] = 0;
            }
            $totalQtyPerPO[$row->poNum] += $row->qtyPO;
        }

        $groupedByBarang = [];
        $grandTotals = [
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
            'subsidi' => 0,
            'pphSubsidi' => 0,
            'totalSubsidi' => 0,
            'totalRow' => 0,
        ];

        foreach ($data as $row) {
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            $nilai_pph = ($row->poDate <= '2025-06-30') ? ($hasNpwp ? 0.9975 : 0.995) : 0.9975;
            $nilai_pph2 = ($row->poDate <= '2025-06-30') ? ($hasNpwp ? 0.0025 : 0.005) : 0.0025;

            $qty = $row->qtyPO;
            $barangName = $row->barangName;
            $poKey = $row->poNum;
            $subKey = $row->spekName . '|' . $row->divisiName; // Gabungkan spek dan divisi

            $calc = function ($val) use ($pphMode, $nilai_pph, $nilai_pph2, $qty) {
                if ($pphMode === "Company") {
                    $dpp = ($val / $nilai_pph) * $qty;
                    $pph = ($val / $nilai_pph * $nilai_pph2) * $qty;
                } elseif ($pphMode === "Supplier") {
                    $dpp = $val * $qty;
                    $pph = $val * $nilai_pph2 * $qty;
                } else {
                    $dpp = $val * $qty;
                    $pph = 0;
                }
                return [$dpp, $pph, $dpp - $pph];
            };

            [$dppUmum, $pphUmum, $totalUmum] = $calc($row->dppUmum);
            [$dppHarian, $pphHarian, $totalHarian] = $calc($row->dppHarian);
            [$dppBulanan, $pphBulanan, $totalBulanan] = $calc($row->dppBulanan);

            // === Subsidi ===
            $isFixed = $row->subsidi != 0;
            $subsidiRow = $isFixed
                ? ($row->subsidi * ($qty / ($totalQtyPerPO[$poKey] ?: 1)))
                : (($row->cong_batasan - $row->cong_sebenarnya) * $qty);

            if ($pphMode === "Company") {
                $dppSubsidi = $subsidiRow / $nilai_pph;
                $pphSubsidi = $dppSubsidi * $nilai_pph2;
            } elseif ($pphMode === "Supplier") {
                $dppSubsidi = $subsidiRow;
                $pphSubsidi = $dppSubsidi * $nilai_pph2;
            } else {
                $dppSubsidi = $subsidiRow;
                $pphSubsidi = 0;
            }
            $totalSubsidi = $dppSubsidi - $pphSubsidi;
            $totalRow = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

            // Inisialisasi grup barang jika belum ada
            if (!isset($groupedByBarang[$barangName])) {
                $groupedByBarang[$barangName] = [];
            }

            // Inisialisasi sub grup jika belum ada
            if (!isset($groupedByBarang[$barangName][$subKey])) {
                $groupedByBarang[$barangName][$subKey] = [
                    'barangName' => $barangName,
                    'spekName' => $row->spekName,
                    'divisiName' => $row->divisiName,
                    'satuanName' => $row->satuanName,
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
                    'subsidi' => 0,
                    'pphSubsidi' => 0,
                    'totalSubsidi' => 0,
                    'totalRow' => 0,
                ];
            }

            // Akumulasi nilai ke sub grup
            $group = &$groupedByBarang[$barangName][$subKey];
            $group['qtyPO'] += $qty;
            $group['dppUmum'] += $dppUmum;
            $group['pphUmum'] += $pphUmum;
            $group['totalUmum'] += $totalUmum;
            $group['dppHarian'] += $dppHarian;
            $group['pphHarian'] += $pphHarian;
            $group['totalHarian'] += $totalHarian;
            $group['dppBulanan'] += $dppBulanan;
            $group['pphBulanan'] += $pphBulanan;
            $group['totalBulanan'] += $totalBulanan;
            $group['subsidi'] += $dppSubsidi;
            $group['pphSubsidi'] += $pphSubsidi;
            $group['totalSubsidi'] += $totalSubsidi;
            $group['totalRow'] += $totalRow;

            // Akumulasi grand total
            $grandTotals['qtyPO'] += $qty;
            $grandTotals['dppUmum'] += $dppUmum;
            $grandTotals['pphUmum'] += $pphUmum;
            $grandTotals['totalUmum'] += $totalUmum;
            $grandTotals['dppHarian'] += $dppHarian;
            $grandTotals['pphHarian'] += $pphHarian;
            $grandTotals['totalHarian'] += $totalHarian;
            $grandTotals['dppBulanan'] += $dppBulanan;
            $grandTotals['pphBulanan'] += $pphBulanan;
            $grandTotals['totalBulanan'] += $totalBulanan;
            $grandTotals['subsidi'] += $dppSubsidi;
            $grandTotals['pphSubsidi'] += $pphSubsidi;
            $grandTotals['totalSubsidi'] += $totalSubsidi;
            $grandTotals['totalRow'] += $totalRow;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul utama
        $sheet->setCellValue('A1', 'LAPORAN REKAP ALL BARANG (SUMMARY)');
        $sheet->mergeCells('A1:S1');
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Tanggal
        $sheet->setCellValue('A2', 'Tanggal');
        $sheet->setCellValue('B2', ':');
        if ($addCondition['dateStart'] && $addCondition['dateEnd']) {
            $sheet->setCellValue('C2', $addCondition['dateStart']);
            $sheet->setCellValue('D2', 's/d');
            $sheet->setCellValue('E2', $addCondition['dateEnd']);
        } else {
            $sheet->setCellValue('C2', 'ALL');
        }

        $headers = [
            ['NO.', 'BARANG', 'SPESIFIKASI', 'DEPARTEMEN', 'QTY', 'SATUAN', 'HARIAN', '', '', 'TAMBAHAN HARIAN', '', '', 'TAMBAHAN BULANAN', '', '', 'TAMBAHAN LANGSUNG', '', '', 'TOTAL'],
            ['', '', '', '', '', '', 'DPP', 'PPh', 'Dibayarkan', 'DPP', 'PPh', 'Dibayarkan', 'DPP', 'PPh', 'Dibayarkan', 'DPP', 'PPh', 'Dibayarkan', '']
        ];

        // Mulai dari baris 4
        $sheet->fromArray($headers[0], null, 'A4');
        $sheet->fromArray($headers[1], null, 'A5');

        // Merge header
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:D5');
        $sheet->mergeCells('E4:E5');
        $sheet->mergeCells('F4:F5');
        $sheet->mergeCells('G4:I4');
        $sheet->mergeCells('J4:L4');
        $sheet->mergeCells('M4:O4');
        $sheet->mergeCells('P4:R4');
        $sheet->mergeCells('S4:S5');

        // Style header
        $sheet->getStyle('A4:S5')->getFont()->setBold(true);
        $sheet->getStyle('A4:S5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:S5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4:S5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $rowIndex = 6;
        $no = 1;

        // Mapping kolom numerik: E(4), G(6), H(7), I(8), J(9), K(10), L(11), M(12), N(13), O(14), P(15), Q(16), R(17), S(18)
        $numericColumns = [4, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];

        foreach ($groupedByBarang as $barangName => $subGroups) {
            // Header bahan baku
            $sheet->setCellValue("A{$rowIndex}", 'Bahan Baku: ' . $barangName);
            $sheet->mergeCells("A{$rowIndex}:S{$rowIndex}");
            $sheet->getStyle("A{$rowIndex}")->getFont()->setBold(true);
            $sheet->getStyle("A{$rowIndex}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');
            $rowIndex++;

            $barangTotals = [
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
                'subsidi' => 0,
                'pphSubsidi' => 0,
                'totalSubsidi' => 0,
                'totalRow' => 0,
            ];

            foreach ($subGroups as $subGroup) {
                // Siapkan data untuk semua kolom
                $rowData = [
                    'A' => $no++,
                    'B' => $subGroup['barangName'],
                    'C' => $subGroup['spekName'],
                    'D' => $subGroup['divisiName'],
                    'E' => $subGroup['qtyPO'] ?? 0,
                    'F' => $subGroup['satuanName'],
                    'G' => $subGroup['dppUmum'] ?? 0,
                    'H' => $subGroup['pphUmum'] ?? 0,
                    'I' => $subGroup['totalUmum'] ?? 0,
                    'J' => $subGroup['dppHarian'] ?? 0,
                    'K' => $subGroup['pphHarian'] ?? 0,
                    'L' => $subGroup['totalHarian'] ?? 0,
                    'M' => $subGroup['dppBulanan'] ?? 0,
                    'N' => $subGroup['pphBulanan'] ?? 0,
                    'O' => $subGroup['totalBulanan'] ?? 0,
                    'P' => $subGroup['subsidi'] ?? 0,
                    'Q' => $subGroup['pphSubsidi'] ?? 0,
                    'R' => $subGroup['totalSubsidi'] ?? 0,
                    'S' => $subGroup['totalRow'] ?? 0,
                ];

                // Tulis data per kolom
                foreach ($rowData as $col => $value) {
                    $cellAddress = $col . $rowIndex;
                    $sheet->setCellValue($cellAddress, $value);

                    // Format numerik untuk kolom tertentu
                    if (in_array(ord($col) - 65, $numericColumns)) {
                        $sheet->getStyle($cellAddress)
                            ->getNumberFormat()
                            ->setFormatCode('#,##0.00;[Red]-#,##0.00;"0.00"');
                    }
                }

                // Akumulasi total per barang
                foreach ($barangTotals as $key => $val) {
                    $barangTotals[$key] += $subGroup[$key];
                }

                $rowIndex++;
            }

            // Total per barangName
            $sheet->setCellValue("A{$rowIndex}", 'TOTAL');
            $sheet->mergeCells("A{$rowIndex}:D{$rowIndex}");

            // Data total
            $totalData = [
                'E' => $barangTotals['qtyPO'],
                'G' => $barangTotals['dppUmum'],
                'H' => $barangTotals['pphUmum'],
                'I' => $barangTotals['totalUmum'],
                'J' => $barangTotals['dppHarian'],
                'K' => $barangTotals['pphHarian'],
                'L' => $barangTotals['totalHarian'],
                'M' => $barangTotals['dppBulanan'],
                'N' => $barangTotals['pphBulanan'],
                'O' => $barangTotals['totalBulanan'],
                'P' => $barangTotals['subsidi'],
                'Q' => $barangTotals['pphSubsidi'],
                'R' => $barangTotals['totalSubsidi'],
                'S' => $barangTotals['totalRow']
            ];

            // Tulis data total
            foreach ($totalData as $col => $value) {
                $cellAddress = $col . $rowIndex;
                $sheet->setCellValue($cellAddress, $value);
                $sheet->getStyle($cellAddress)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00;[Red]-#,##0.00;"0.00"');
            }

            $sheet->getStyle("A{$rowIndex}:S{$rowIndex}")->getFont()->setBold(true);
            $sheet->getStyle("A{$rowIndex}:S{$rowIndex}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

            // Baris kosong antar grup
            $rowIndex += 2;
        }
        // Tambahkan border untuk seluruh data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A4:S' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto-size kolom
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Rekap_All_Barang_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportPDFLaporanRekapAllBarang()
    {
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $this->this_company_id,
            'rm_purchase_orders.deletedAt'  => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "divisiId"     => $this->request->getGet("filter_divisi"),
            "sort"         => 'rm_purchase_orders.po_date, divisis.id',
            "sortType"     => $this->request->getGet("sortType"),
            "barangId"     => $this->request->getGet("filter_barang"),
        ];

        $availableSort = [];
        $data = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null)['data'];

        // Hitung total qty per poNum untuk subsidi proporsional
        $totalQtyPerPO = [];
        foreach ($data as $row) {
            if (!isset($totalQtyPerPO[$row->poNum])) {
                $totalQtyPerPO[$row->poNum] = 0;
            }
            $totalQtyPerPO[$row->poNum] += $row->qtyPO;
        }

        $groupedByBarang = [];
        $grandTotals = [
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
            'subsidi' => 0,
            'pphSubsidi' => 0,
            'totalSubsidi' => 0,
            'totalRow' => 0,
        ];

        foreach ($data as $row) {
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            $nilai_pph = ($row->poDate <= '2025-06-30') ? ($hasNpwp ? 0.9975 : 0.995) : 0.9975;
            $nilai_pph2 = ($row->poDate <= '2025-06-30') ? ($hasNpwp ? 0.0025 : 0.005) : 0.0025;

            $qty = $row->qtyPO;
            $barangName = $row->barangName;
            $poKey = $row->poNum;
            $subKey = $row->spekName . '|' . $row->divisiName; // Gabungkan spek dan divisi

            $calc = function ($val) use ($pphMode, $nilai_pph, $nilai_pph2, $qty) {
                if ($pphMode === "Company") {
                    $dpp = ($val / $nilai_pph) * $qty;
                    $pph = ($val / $nilai_pph * $nilai_pph2) * $qty;
                } elseif ($pphMode === "Supplier") {
                    $dpp = $val * $qty;
                    $pph = $val * $nilai_pph2 * $qty;
                } else {
                    $dpp = $val * $qty;
                    $pph = 0;
                }
                return [$dpp, $pph, $dpp - $pph];
            };

            [$dppUmum, $pphUmum, $totalUmum] = $calc($row->dppUmum);
            [$dppHarian, $pphHarian, $totalHarian] = $calc($row->dppHarian);
            [$dppBulanan, $pphBulanan, $totalBulanan] = $calc($row->dppBulanan);

            // === Subsidi ===
            $isFixed = $row->subsidi != 0;
            $subsidiRow = $isFixed
                ? ($row->subsidi * ($qty / ($totalQtyPerPO[$poKey] ?: 1)))
                : (($row->cong_batasan - $row->cong_sebenarnya) * $qty);

            if ($pphMode === "Company") {
                $dppSubsidi = $subsidiRow / $nilai_pph;
                $pphSubsidi = $dppSubsidi * $nilai_pph2;
            } elseif ($pphMode === "Supplier") {
                $dppSubsidi = $subsidiRow;
                $pphSubsidi = $dppSubsidi * $nilai_pph2;
            } else {
                $dppSubsidi = $subsidiRow;
                $pphSubsidi = 0;
            }
            $totalSubsidi = $dppSubsidi - $pphSubsidi;
            $totalRow = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

            // Inisialisasi grup barang jika belum ada
            if (!isset($groupedByBarang[$barangName])) {
                $groupedByBarang[$barangName] = [];
            }

            // Inisialisasi sub grup jika belum ada
            if (!isset($groupedByBarang[$barangName][$subKey])) {
                $groupedByBarang[$barangName][$subKey] = [
                    'barangName' => $barangName,
                    'spekName' => $row->spekName,
                    'divisiName' => $row->divisiName,
                    'satuanName' => $row->satuanName,
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
                    'subsidi' => 0,
                    'pphSubsidi' => 0,
                    'totalSubsidi' => 0,
                    'totalRow' => 0,
                ];
            }

            // Akumulasi nilai ke sub grup
            $group = &$groupedByBarang[$barangName][$subKey];
            $group['qtyPO'] += $qty;
            $group['dppUmum'] += $dppUmum;
            $group['pphUmum'] += $pphUmum;
            $group['totalUmum'] += $totalUmum;
            $group['dppHarian'] += $dppHarian;
            $group['pphHarian'] += $pphHarian;
            $group['totalHarian'] += $totalHarian;
            $group['dppBulanan'] += $dppBulanan;
            $group['pphBulanan'] += $pphBulanan;
            $group['totalBulanan'] += $totalBulanan;
            $group['subsidi'] += $dppSubsidi;
            $group['pphSubsidi'] += $pphSubsidi;
            $group['totalSubsidi'] += $totalSubsidi;
            $group['totalRow'] += $totalRow;

            // Akumulasi grand total
            $grandTotals['qtyPO'] += $qty;
            $grandTotals['dppUmum'] += $dppUmum;
            $grandTotals['pphUmum'] += $pphUmum;
            $grandTotals['totalUmum'] += $totalUmum;
            $grandTotals['dppHarian'] += $dppHarian;
            $grandTotals['pphHarian'] += $pphHarian;
            $grandTotals['totalHarian'] += $totalHarian;
            $grandTotals['dppBulanan'] += $dppBulanan;
            $grandTotals['pphBulanan'] += $pphBulanan;
            $grandTotals['totalBulanan'] += $totalBulanan;
            $grandTotals['subsidi'] += $dppSubsidi;
            $grandTotals['pphSubsidi'] += $pphSubsidi;
            $grandTotals['totalSubsidi'] += $totalSubsidi;
            $grandTotals['totalRow'] += $totalRow;
        }

        $no = 1;
        $data = [
            'no' => $no,
            'header' => "LAPORAN REKAP ALL BARANG (SUMMARY)",
            'tanggalAwal' => $addCondition['dateStart'],
            'tanggalAkhir' => $addCondition['dateEnd'],
            'groupedData' => $groupedByBarang,
            'grandTotals' => $grandTotals
        ];

        $domPdf = new Dompdf();
        $fileName = 'Rekap All Barang (Summary)';
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapAllBarang/print', $data));
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream($fileName, array("Attachment" => false));
        exit();
    }
}
