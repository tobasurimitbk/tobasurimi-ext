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

        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
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

            // 🔸 Hitung proporsi qty detail terhadap total qty PO
            $qtyDetail = floatval($row->qtyPO ?? 0);
            $qtyTotalPO = floatval($row->sum_qtyPO ?? 0);
            $proporsi = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // 🔸 Hitung subsidi proporsional per detail
            $dppSubsidi    = floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphSubsidi    = floatval($row->sum_pph_tambahan ?? 0) * $proporsi;
            $totalSubsidi  = floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$entry) {
                if ($entry['supplierName'] === $supplier && $entry['spekName'] === $spek) {
                    $entry['qtyPO']        += $qtyDetail;
                    $entry['dppUmum']      += floatval($row->dpp_umum ?? 0);
                    $entry['pphUmum']      += floatval($row->pph_umum ?? 0);
                    $entry['totalUmum']    += floatval($row->nilai_total_umum ?? 0);
                    $entry['dppHarian']    += floatval($row->dpp_harian ?? 0);
                    $entry['pphHarian']    += floatval($row->pph_harian ?? 0);
                    $entry['totalHarian']  += floatval($row->nilai_total_harian ?? 0);
                    $entry['dppBulanan']   += floatval($row->dpp_bulanan ?? 0);
                    $entry['pphBulanan']   += floatval($row->pph_bulanan ?? 0);
                    $entry['totalBulanan'] += floatval($row->nilai_total_bulanan ?? 0);
                    $entry['dppSubsidi']   += $dppSubsidi;
                    $entry['pphSubsidi']   += $pphSubsidi;
                    $entry['totalSubsidi'] += $totalSubsidi;
                    $entry['totalRow']     += (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        $totalSubsidi
                    );
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $grouped[$barang][] = [
                    'no'            => $no++,
                    'supplierName'  => $supplier,
                    'divisiName'    => $row->divisiName ?? '-',
                    'barangName'    => $barang,
                    'spekName'      => $spek,
                    'satuanName'    => $row->satuanName ?? '-',
                    'qtyPO'         => $qtyDetail,
                    'dppUmum'       => floatval($row->dpp_umum ?? 0),
                    'pphUmum'       => floatval($row->pph_umum ?? 0),
                    'totalUmum'     => floatval($row->nilai_total_umum ?? 0),
                    'dppHarian'     => floatval($row->dpp_harian ?? 0),
                    'pphHarian'     => floatval($row->pph_harian ?? 0),
                    'totalHarian'   => floatval($row->nilai_total_harian ?? 0),
                    'dppBulanan'    => floatval($row->dpp_bulanan ?? 0),
                    'pphBulanan'    => floatval($row->pph_bulanan ?? 0),
                    'totalBulanan'  => floatval($row->nilai_total_bulanan ?? 0),
                    'dppSubsidi'    => $dppSubsidi,
                    'pphSubsidi'    => $pphSubsidi,
                    'totalSubsidi'  => $totalSubsidi,
                    'totalRow'      => (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        $totalSubsidi
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

        // 🔸 Ganti ambil data pakai yang sama seperti di PDF
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // --- Grouping per barang dan supplier, dengan hitung subsidi proporsional ---
        $grouped = [];
        foreach ($allData as $row) {
            $barang   = $row->barangName ?? '-';
            $supplier = $row->supplierName ?? '-';
            $divisi   = $row->divisiName ?? '-';
            $spek     = $row->spekName ?? '-';
            $satuan   = $row->satuanName ?? '-';

            $qtyDetail = floatval($row->qtyPO ?? 0);
            $qtyTotalPO = floatval($row->sum_qtyPO ?? 0);
            $proporsi = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            $dppSubsidi   = floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphSubsidi   = floatval($row->sum_pph_tambahan ?? 0) * $proporsi;
            $totalSubsidi = floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

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
                    $item['Qty']            += $qtyDetail;
                    $item['DPP Harian']     += floatval($row->dpp_umum ?? 0);
                    $item['PPh Harian']     += floatval($row->pph_umum ?? 0);
                    $item['Total Harian']   += floatval($row->nilai_total_umum ?? 0);
                    $item['DPP Tamb Har']   += floatval($row->dpp_harian ?? 0);
                    $item['PPh Tamb Har']   += floatval($row->pph_harian ?? 0);
                    $item['Total Tamb Har'] += floatval($row->nilai_total_harian ?? 0);
                    $item['DPP Bulanan']    += floatval($row->dpp_bulanan ?? 0);
                    $item['PPh Bulanan']    += floatval($row->pph_bulanan ?? 0);
                    $item['Total Bulanan']  += floatval($row->nilai_total_bulanan ?? 0);
                    $item['DPP Langsung']   += $dppSubsidi;
                    $item['PPh Langsung']   += $pphSubsidi;
                    $item['Total Langsung'] += $totalSubsidi;
                    $item['Total']          += (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        $totalSubsidi
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
                    'Qty'             => $qtyDetail,
                    'DPP Harian'      => floatval($row->dpp_umum ?? 0),
                    'PPh Harian'      => floatval($row->pph_umum ?? 0),
                    'Total Harian'    => floatval($row->nilai_total_umum ?? 0),
                    'DPP Tamb Har'    => floatval($row->dpp_harian ?? 0),
                    'PPh Tamb Har'    => floatval($row->pph_harian ?? 0),
                    'Total Tamb Har'  => floatval($row->nilai_total_harian ?? 0),
                    'DPP Bulanan'     => floatval($row->dpp_bulanan ?? 0),
                    'PPh Bulanan'     => floatval($row->pph_bulanan ?? 0),
                    'Total Bulanan'   => floatval($row->nilai_total_bulanan ?? 0),
                    'DPP Langsung'    => $dppSubsidi,
                    'PPh Langsung'    => $pphSubsidi,
                    'Total Langsung'  => $totalSubsidi,
                    'Total'           => (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        $totalSubsidi
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
                if ($entry['supplierName'] === $supplier) {
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
                    $item['Divisi'] === $divisi
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
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", "Tanggal: " . ($addCondition["dateStart"] ?? '') . " s/d " . ($addCondition["dateEnd"] ?? ''));
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;
        $row += 2;

        foreach ($grouped as $barangName => $rows) {
            // Judul bahan baku
            $sheet->setCellValue("A{$row}", "Bahan Baku: " . strtoupper($barangName));
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("A{$row}")->getFill()->setFillType('solid')->getStartColor()->setARGB('FF666666');
            $row++;

            // === Header dua baris ===
            $sheet->setCellValue("A{$row}", "No");
            $sheet->setCellValue("B{$row}", "Supplier");
            $sheet->setCellValue("C{$row}", "Divisi");
            $sheet->setCellValue("D{$row}", "Barang");
            $sheet->setCellValue("E{$row}", "Satuan");
            $sheet->setCellValue("F{$row}", "Qty");

            $sheet->setCellValue("G{$row}", "Tambahan Bulanan");
            $sheet->setCellValue("H{$row}", "TOTAL");
            $sheet->mergeCells("H{$row}:H" . ($row + 1));

            // Subheader
            $row2 = $row + 1;
            $sheet->setCellValue("G{$row2}", "Total");

            foreach (range('A', 'F') as $col) {
                $sheet->mergeCells("{$col}{$row}:{$col}{$row2}");
            }

            $sheet->getStyle("A{$row}:H{$row2}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:H{$row2}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:H{$row2}")
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
                $sheet->setCellValue("E{$row}", $r['Satuan']);
                $sheet->setCellValue("F{$row}", $r['Qty']);
                $sheet->setCellValue("G{$row}", $r['Total Bulanan']);
                $sheet->setCellValue("H{$row}", $r['Total']);
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                foreach ($totals as $k => &$v) $v += $r[$k];
                $row++;
            }

            // === Subtotal per barang ===
            $sheet->setCellValue("A{$row}", "TOTAL " . strtoupper($barangName));
            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:H{$row}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $col = 'F';
            foreach ($totals as $val) {
                $sheet->setCellValue($col++ . $row, $val);
            }
            $row += 2;
        }

        // Format dan output
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("G1:H{$row}")
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
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"   => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "divisiId"  => $this->request->getGet("filter_divisi"),
            "barangId"  => $this->request->getGet("filter_barang"),
        ];

        $availableSort = [
            'barangName' => 'barang_master.barang_name',
            'spekName'   => 'spekName',
            'bagianName' => 'bagianName',
        ];

        // Ambil data
        $dataResult = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        );
        $data = $dataResult['data'];

        // =============== Pengelompokan dan Perhitungan ===============
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
            'dppSubsidi' => 0,
            'pphSubsidi' => 0,
            'totalSubsidi' => 0,
            'totalRow' => 0
        ];

        foreach ($data as $row) {
            $barangName = $row->barangName;
            $subKey = $row->spekName . '|' . $row->divisiName;

            if (!isset($groupedByBarang[$barangName])) {
                $groupedByBarang[$barangName] = [];
            }

            if (!isset($groupedByBarang[$barangName][$subKey])) {
                $groupedByBarang[$barangName][$subKey] = [
                    'barangName' => $barangName,
                    'divisiName' => $row->divisiName,
                    'spekName'   => $row->spekName,
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
                    'dppSubsidi' => 0,
                    'pphSubsidi' => 0,
                    'totalSubsidi' => 0,
                    'totalRow' => 0,
                ];
            }

            $g = &$groupedByBarang[$barangName][$subKey];

            // Ambil nilai langsung dari field hasil query (bukan hitungan manual)
            $qtyPO         = floatval($row->qtyPO ?? 0);
            $dppUmum       = floatval($row->dpp_umum ?? 0);
            $pphUmum       = floatval($row->pph_umum ?? 0);
            $totalUmum     = floatval($row->nilai_total_umum ?? 0);
            $dppHarian     = floatval($row->dpp_harian ?? 0);
            $pphHarian     = floatval($row->pph_harian ?? 0);
            $totalHarian   = floatval($row->nilai_total_harian ?? 0);
            $dppBulanan    = floatval($row->dpp_bulanan ?? 0);
            $pphBulanan    = floatval($row->pph_bulanan ?? 0);
            $totalBulanan  = floatval($row->nilai_total_bulanan ?? 0);
            $dppSubsidi    = floatval($row->dpp_tambahan ?? 0);
            $pphSubsidi    = floatval($row->pph_tambahan ?? 0);
            $totalSubsidi  = floatval($row->nilai_total_tambahan ?? 0);
            $totalRow      = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

            // Akumulasi per grup
            $g['qtyPO']        += $qtyPO;
            $g['dppUmum']      += $dppUmum;
            $g['pphUmum']      += $pphUmum;
            $g['totalUmum']    += $totalUmum;
            $g['dppHarian']    += $dppHarian;
            $g['pphHarian']    += $pphHarian;
            $g['totalHarian']  += $totalHarian;
            $g['dppBulanan']   += $dppBulanan;
            $g['pphBulanan']   += $pphBulanan;
            $g['totalBulanan'] += $totalBulanan;
            $g['dppSubsidi']   += $dppSubsidi;
            $g['pphSubsidi']   += $pphSubsidi;
            $g['totalSubsidi'] += $totalSubsidi;
            $g['totalRow']     += $totalRow;

            // Total keseluruhan
            $totalSummary['qtyPO']        += $qtyPO;
            $totalSummary['dppUmum']      += $dppUmum;
            $totalSummary['pphUmum']      += $pphUmum;
            $totalSummary['totalUmum']    += $totalUmum;
            $totalSummary['dppHarian']    += $dppHarian;
            $totalSummary['pphHarian']    += $pphHarian;
            $totalSummary['totalHarian']  += $totalHarian;
            $totalSummary['dppBulanan']   += $dppBulanan;
            $totalSummary['pphBulanan']   += $pphBulanan;
            $totalSummary['totalBulanan'] += $totalBulanan;
            $totalSummary['dppSubsidi']   += $dppSubsidi;
            $totalSummary['pphSubsidi']   += $pphSubsidi;
            $totalSummary['totalSubsidi'] += $totalSubsidi;
            $totalSummary['totalRow']     += $totalRow;
        }

        // Flatten data
        $flatData = [];
        foreach ($groupedByBarang as $barang => $subGroup) {
            foreach ($subGroup as $record) {
                $flatData[] = $record;
            }
        }

        // Sort berdasarkan nama barang
        usort($flatData, fn($a, $b) => strcmp($a['barangName'], $b['barangName']));

        // Pagination
        $totalGrouped = count($flatData);
        $paginatedData = array_slice($flatData, ($currentPage - 1) * $pageSize, $pageSize);

        // Format angka
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
            $formattedSummary[$key] = number_format($val, 2, '.', ',');
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
            'totalSummary' => $formattedSummary,
        ]);
    }

    public function exportExcelLaporanRekapAllBarang()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"   => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "divisiId"  => $this->request->getGet("filter_divisi"),
            "barangId"  => $this->request->getGet("filter_barang"),
        ];

        $availableSort = [
            'barangName' => 'barang_master.barang_name',
            'spekName'   => 'spekName',
            'bagianName' => 'bagianName',
        ];

        // Ambil data sama seperti PDF
        $dataResult = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        );
        $data = $dataResult['data'];

        // ================= Pengelompokan =================
        $groupedData = [];
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
            'totalRow' => 0
        ];

        foreach ($data as $row) {
            $barangName = $row->barangName;
            $subKey = $row->spekName . '|' . $row->divisiName;

            if (!isset($groupedData[$barangName])) {
                $groupedData[$barangName] = [];
            }

            if (!isset($groupedData[$barangName][$subKey])) {
                $groupedData[$barangName][$subKey] = [
                    'barangName' => $barangName,
                    'divisiName' => $row->divisiName,
                    'spekName'   => $row->spekName,
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
                    'totalRow' => 0
                ];
            }

            $g = &$groupedData[$barangName][$subKey];

            $qtyPO         = floatval($row->qtyPO ?? 0);
            $dppUmum       = floatval($row->dpp_umum ?? 0);
            $pphUmum       = floatval($row->pph_umum ?? 0);
            $totalUmum     = floatval($row->nilai_total_umum ?? 0);
            $dppHarian     = floatval($row->dpp_harian ?? 0);
            $pphHarian     = floatval($row->pph_harian ?? 0);
            $totalHarian   = floatval($row->nilai_total_harian ?? 0);
            $dppBulanan    = floatval($row->dpp_bulanan ?? 0);
            $pphBulanan    = floatval($row->pph_bulanan ?? 0);
            $totalBulanan  = floatval($row->nilai_total_bulanan ?? 0);
            $dppSubsidi    = floatval($row->dpp_tambahan ?? 0);
            $pphSubsidi    = floatval($row->pph_tambahan ?? 0);
            $totalSubsidi  = floatval($row->nilai_total_tambahan ?? 0);
            $totalRow      = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

            $g['qtyPO']        += $qtyPO;
            $g['dppUmum']      += $dppUmum;
            $g['pphUmum']      += $pphUmum;
            $g['totalUmum']    += $totalUmum;
            $g['dppHarian']    += $dppHarian;
            $g['pphHarian']    += $pphHarian;
            $g['totalHarian']  += $totalHarian;
            $g['dppBulanan']   += $dppBulanan;
            $g['pphBulanan']   += $pphBulanan;
            $g['totalBulanan'] += $totalBulanan;
            $g['subsidi']      += $dppSubsidi;
            $g['pphSubsidi']   += $pphSubsidi;
            $g['totalSubsidi'] += $totalSubsidi;
            $g['totalRow']     += $totalRow;

            // Grand total
            $grandTotals['qtyPO']        += $qtyPO;
            $grandTotals['dppUmum']      += $dppUmum;
            $grandTotals['pphUmum']      += $pphUmum;
            $grandTotals['totalUmum']    += $totalUmum;
            $grandTotals['dppHarian']    += $dppHarian;
            $grandTotals['pphHarian']    += $pphHarian;
            $grandTotals['totalHarian']  += $totalHarian;
            $grandTotals['dppBulanan']   += $dppBulanan;
            $grandTotals['pphBulanan']   += $pphBulanan;
            $grandTotals['totalBulanan'] += $totalBulanan;
            $grandTotals['subsidi']      += $dppSubsidi;
            $grandTotals['pphSubsidi']   += $pphSubsidi;
            $grandTotals['totalSubsidi'] += $totalSubsidi;
            $grandTotals['totalRow']     += $totalRow;
        }

        // ================= Spreadsheet =================
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap All Barang');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN REKAP ALL BARANG (SUMMARY)');
        $sheet->mergeCells('A1:S1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Table Header
        $headers = [
            'NO',
            'BARANG',
            'SPEK',
            'DEPARTEMEN',
            'QTY',
            'SATUAN',
            'DPP HARIAN',
            'PPh HARIAN',
            'TOTAL HARIAN',
            'DPP TAMBAHAN HARIAN',
            'PPh TAMBAHAN HARIAN',
            'TOTAL TAMBAHAN HARIAN',
            'DPP TAMBAHAN BULANAN',
            'PPh TAMBAHAN BULANAN',
            'TOTAL TAMBAHAN BULANAN',
            'DPP TAMBAHAN LANGSUNG',
            'PPh TAMBAHAN LANGSUNG',
            'TOTAL TAMBAHAN LANGSUNG',
            'TOTAL'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Data rows
        $rowNum = 4;
        $no = 1;

        foreach ($groupedData as $barangName => $subGroups) {
            // Group Header
            $sheet->setCellValue('A' . $rowNum, "Bahan Baku: $barangName");
            $sheet->mergeCells("A{$rowNum}:S{$rowNum}");
            $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
            $rowNum++;

            $groupTotals = array_fill_keys(array_keys($grandTotals), 0);

            foreach ($subGroups as $record) {
                $sheet->setCellValue("A{$rowNum}", $no++);
                $sheet->setCellValue("B{$rowNum}", $record['barangName']);
                $sheet->setCellValue("C{$rowNum}", $record['spekName']);
                $sheet->setCellValue("D{$rowNum}", $record['divisiName']);
                $sheet->setCellValue("E{$rowNum}", $record['qtyPO']);
                $sheet->setCellValue("F{$rowNum}", $record['satuanName']);
                $sheet->setCellValue("G{$rowNum}", $record['dppUmum']);
                $sheet->setCellValue("H{$rowNum}", $record['pphUmum']);
                $sheet->setCellValue("I{$rowNum}", $record['totalUmum']);
                $sheet->setCellValue("J{$rowNum}", $record['dppHarian']);
                $sheet->setCellValue("K{$rowNum}", $record['pphHarian']);
                $sheet->setCellValue("L{$rowNum}", $record['totalHarian']);
                $sheet->setCellValue("M{$rowNum}", $record['dppBulanan']);
                $sheet->setCellValue("N{$rowNum}", $record['pphBulanan']);
                $sheet->setCellValue("O{$rowNum}", $record['totalBulanan']);
                $sheet->setCellValue("P{$rowNum}", $record['subsidi']);
                $sheet->setCellValue("Q{$rowNum}", $record['pphSubsidi']);
                $sheet->setCellValue("R{$rowNum}", $record['totalSubsidi']);
                $sheet->setCellValue("S{$rowNum}", $record['totalRow']);

                // Format angka
                $sheet->getStyle("E{$rowNum}:S{$rowNum}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                foreach ($groupTotals as $key => $v) {
                    if (isset($record[$key])) {
                        $groupTotals[$key] += $record[$key];
                    }
                }

                $rowNum++;
            }

            // Subtotal row
            $sheet->setCellValue("A{$rowNum}", "TOTAL $barangName");
            $sheet->mergeCells("A{$rowNum}:D{$rowNum}");
            $sheet->setCellValue("E{$rowNum}", $groupTotals['qtyPO']);
            $sheet->setCellValue("G{$rowNum}", $groupTotals['dppUmum']);
            $sheet->setCellValue("H{$rowNum}", $groupTotals['pphUmum']);
            $sheet->setCellValue("I{$rowNum}", $groupTotals['totalUmum']);
            $sheet->setCellValue("J{$rowNum}", $groupTotals['dppHarian']);
            $sheet->setCellValue("K{$rowNum}", $groupTotals['pphHarian']);
            $sheet->setCellValue("L{$rowNum}", $groupTotals['totalHarian']);
            $sheet->setCellValue("M{$rowNum}", $groupTotals['dppBulanan']);
            $sheet->setCellValue("N{$rowNum}", $groupTotals['pphBulanan']);
            $sheet->setCellValue("O{$rowNum}", $groupTotals['totalBulanan']);
            $sheet->setCellValue("P{$rowNum}", $groupTotals['subsidi']);
            $sheet->setCellValue("Q{$rowNum}", $groupTotals['pphSubsidi']);
            $sheet->setCellValue("R{$rowNum}", $groupTotals['totalSubsidi']);
            $sheet->setCellValue("S{$rowNum}", $groupTotals['totalRow']);

            $sheet->getStyle("A{$rowNum}:S{$rowNum}")->getFont()->setBold(true);
            $sheet->getStyle("E{$rowNum}:S{$rowNum}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $rowNum++;
        }

        // Grand total row
        $sheet->setCellValue("A{$rowNum}", "GRAND TOTAL");
        $sheet->mergeCells("A{$rowNum}:D{$rowNum}");
        $sheet->setCellValue("E{$rowNum}", $grandTotals['qtyPO']);
        $sheet->setCellValue("G{$rowNum}", $grandTotals['dppUmum']);
        $sheet->setCellValue("H{$rowNum}", $grandTotals['pphUmum']);
        $sheet->setCellValue("I{$rowNum}", $grandTotals['totalUmum']);
        $sheet->setCellValue("J{$rowNum}", $grandTotals['dppHarian']);
        $sheet->setCellValue("K{$rowNum}", $grandTotals['pphHarian']);
        $sheet->setCellValue("L{$rowNum}", $grandTotals['totalHarian']);
        $sheet->setCellValue("M{$rowNum}", $grandTotals['dppBulanan']);
        $sheet->setCellValue("N{$rowNum}", $grandTotals['pphBulanan']);
        $sheet->setCellValue("O{$rowNum}", $grandTotals['totalBulanan']);
        $sheet->setCellValue("P{$rowNum}", $grandTotals['subsidi']);
        $sheet->setCellValue("Q{$rowNum}", $grandTotals['pphSubsidi']);
        $sheet->setCellValue("R{$rowNum}", $grandTotals['totalSubsidi']);
        $sheet->setCellValue("S{$rowNum}", $grandTotals['totalRow']);

        $sheet->getStyle("A{$rowNum}:S{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("E{$rowNum}:S{$rowNum}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // Border untuk semua sel yang terisi
        $sheet->getStyle("A3:S{$rowNum}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Export
        $fileName = "Rekap_All_Barang_Summary.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function exportPDFLaporanRekapAllBarang()
    {
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_orders.company_id' => $this->this_company_id,
            'rm_purchase_orders.status_external' => 'no',
        ];

        $addCondition = [
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"   => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "divisiId"  => $this->request->getGet("filter_divisi"),
            "barangId"  => $this->request->getGet("filter_barang"),
        ];

        $availableSort = [
            'barangName' => 'barang_master.barang_name',
            'spekName'   => 'spekName',
            'bagianName' => 'bagianName',
        ];

        // Ambil data yang sama dengan di method all()
        $dataResult = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierPerPO(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        );
        $data = $dataResult['data'];

        // ================= Pengelompokan Sesuai View =================
        $groupedData = [];
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
            $barangName = $row->barangName;
            $subKey = $row->spekName . '|' . $row->divisiName;

            if (!isset($groupedData[$barangName])) {
                $groupedData[$barangName] = [];
            }

            if (!isset($groupedData[$barangName][$subKey])) {
                $groupedData[$barangName][$subKey] = [
                    'barangName' => $barangName,
                    'divisiName' => $row->divisiName,
                    'spekName'   => $row->spekName,
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

            $g = &$groupedData[$barangName][$subKey];

            // Ambil nilai dari field query (langsung, tanpa perhitungan manual)
            $qtyPO         = floatval($row->qtyPO ?? 0);
            $dppUmum       = floatval($row->dpp_umum ?? 0);
            $pphUmum       = floatval($row->pph_umum ?? 0);
            $totalUmum     = floatval($row->nilai_total_umum ?? 0);
            $dppHarian     = floatval($row->dpp_harian ?? 0);
            $pphHarian     = floatval($row->pph_harian ?? 0);
            $totalHarian   = floatval($row->nilai_total_harian ?? 0);
            $dppBulanan    = floatval($row->dpp_bulanan ?? 0);
            $pphBulanan    = floatval($row->pph_bulanan ?? 0);
            $totalBulanan  = floatval($row->nilai_total_bulanan ?? 0);
            $dppSubsidi    = floatval($row->dpp_tambahan ?? 0);
            $pphSubsidi    = floatval($row->pph_tambahan ?? 0);
            $totalSubsidi  = floatval($row->nilai_total_tambahan ?? 0);
            $totalRow      = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

            // Tambahkan ke group
            $g['qtyPO']        += $qtyPO;
            $g['dppUmum']      += $dppUmum;
            $g['pphUmum']      += $pphUmum;
            $g['totalUmum']    += $totalUmum;
            $g['dppHarian']    += $dppHarian;
            $g['pphHarian']    += $pphHarian;
            $g['totalHarian']  += $totalHarian;
            $g['dppBulanan']   += $dppBulanan;
            $g['pphBulanan']   += $pphBulanan;
            $g['totalBulanan'] += $totalBulanan;
            $g['subsidi']      += $dppSubsidi;
            $g['pphSubsidi']   += $pphSubsidi;
            $g['totalSubsidi'] += $totalSubsidi;
            $g['totalRow']     += $totalRow;

            // Total keseluruhan
            $grandTotals['qtyPO']        += $qtyPO;
            $grandTotals['dppUmum']      += $dppUmum;
            $grandTotals['pphUmum']      += $pphUmum;
            $grandTotals['totalUmum']    += $totalUmum;
            $grandTotals['dppHarian']    += $dppHarian;
            $grandTotals['pphHarian']    += $pphHarian;
            $grandTotals['totalHarian']  += $totalHarian;
            $grandTotals['dppBulanan']   += $dppBulanan;
            $grandTotals['pphBulanan']   += $pphBulanan;
            $grandTotals['totalBulanan'] += $totalBulanan;
            $grandTotals['subsidi']      += $dppSubsidi;
            $grandTotals['pphSubsidi']   += $pphSubsidi;
            $grandTotals['totalSubsidi'] += $totalSubsidi;
            $grandTotals['totalRow']     += $totalRow;
        }

        // Format angka
        // foreach ($groupedData as &$group) {
        //     foreach ($group as &$row) {
        //         foreach ($row as $key => $val) {
        //             if (is_numeric($val)) {
        //                 $row[$key] = number_format($val, 2, '.', ',');
        //             }
        //         }
        //     }
        // }
        // foreach ($grandTotals as $key => &$val) {
        //     $val = number_format($val, 2, '.', ',');
        // }

        // === Kirim ke view PDF ===
        $data = [
            'header' => "LAPORAN REKAP ALL BARANG (SUMMARY)",
            'tanggalAwal' => $addCondition['dateStart'],
            'tanggalAkhir' => $addCondition['dateEnd'],
            'groupedData' => $groupedData, // <-- sesuai struktur yang diharapkan print.php
            'grandTotals' => $grandTotals
        ];

        // return view('Laporan/SupplierLokalBB/RekapAllBarang/print', $data);

        $domPdf = new \Dompdf\Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapAllBarang/print', $data));
        $domPdf->setPaper('legal', 'landscape');

        // Tambahkan scaling agar tabel muat semua
        $canvas = $domPdf->getCanvas();
        $canvas->scale(0.8, 0.8, 0, 0);
        $domPdf->render();
        $domPdf->stream('Rekap_All_Barang_Summary.pdf', ["Attachment" => false]);
        exit();
    }
}
