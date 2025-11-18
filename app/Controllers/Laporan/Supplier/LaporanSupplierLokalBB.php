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
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // Ambil data dengan pagination
        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            $pageSize,
            $start,
            "PO"
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

        foreach ($allData as $i => &$row) {

            $qtyAll     = floatval($row->qtyPO ?? 0);

            $dppUmum    = floatval($row->dpp_umum ?? 0);
            $pphUmum    = floatval($row->pph_umum ?? 0);
            $totalUmum  = floatval($row->nilai_total_umum ?? 0);

            $dppHarian    = floatval($row->dpp_harian ?? 0);
            $pphHarian    = floatval($row->pph_harian ?? 0);
            $totalHarian  = floatval($row->nilai_total_harian ?? 0);

            $dppBulanan   = floatval($row->dpp_bulanan ?? 0);
            $pphBulanan   = floatval($row->pph_bulanan ?? 0);
            $totalBulanan = floatval($row->nilai_total_bulanan ?? 0);

            $qtyDetail = floatval($row->qtyPO ?? 0);
            $qtyTotalPO = floatval($row->sum_qtyPO ?? 0);
            $proporsi = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            $dppTambahan   = floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphTambahan   = floatval($row->sum_pph_tambahan ?? 0) * $proporsi;
            $totalTambahan = floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

            // ✔ MASUKKAN KEMBALI KE ARRAY UTAMA
            $row->dpp_tambahan = $dppTambahan;
            $row->pph_tambahan = $pphTambahan;
            $row->nilai_total_tambahan = $totalTambahan;

            $totalRow = $totalUmum + $totalHarian + $totalBulanan + $totalTambahan;

            // total ALL (tetap sama)
            $totalsRaw['qtyAll']       += $qtyAll;
            $totalsRaw['dppUmum']      += $dppUmum;
            $totalsRaw['pphUmum']      += $pphUmum;
            $totalsRaw['totalUmum']    += $totalUmum;
            $totalsRaw['dppHarian']    += $dppHarian;
            $totalsRaw['pphHarian']    += $pphHarian;
            $totalsRaw['totalHarian']  += $totalHarian;
            $totalsRaw['dppBulanan']   += $dppBulanan;
            $totalsRaw['pphBulanan']   += $pphBulanan;
            $totalsRaw['totalBulanan'] += $totalBulanan;
            $totalsRaw['dppTambahan']  += $dppTambahan;
            $totalsRaw['pphTambahan']  += $pphTambahan;
            $totalsRaw['totalTambahan'] += $totalTambahan;
            $totalsRaw['totalRow']     += $totalRow;
        }

        // --- Format row (pagination) ---
        foreach ($paginatedData as $i => &$row) {

            $row->no = $start + $i + 1;

            $row->qtyPO = $row->sum_qtyPO;
            $row->dpp_umum =
                floatval($row->sum_dpp_umum ?? 0);
            $row->pph_umum =
                floatval($row->sum_pph_umum ?? 0);
            $row->nilai_total_umum =
                floatval($row->sum_nilai_total_umum ?? 0);
            $row->dpp_harian =
                floatval($row->sum_dpp_harian ?? 0);
            $row->pph_harian =
                floatval($row->sum_pph_harian ?? 0);
            $row->nilai_total_harian =
                floatval($row->sum_nilai_total_harian ?? 0);
            $row->dpp_bulanan =
                floatval($row->sum_dpp_bulanan ?? 0);
            $row->pph_bulanan =
                floatval($row->sum_pph_bulanan ?? 0);
            $row->nilai_total_bulanan =
                floatval($row->sum_nilai_total_bulanan ?? 0);
            $row->dpp_tambahan =
                floatval($row->sum_dpp_tambahan ?? 0);

            $row->pph_tambahan =
                floatval($row->sum_pph_tambahan ?? 0);

            $row->nilai_total_tambahan =
                floatval($row->sum_nilai_total_tambahan ?? 0);

            // ===============================
            //  TOTAL ROW (setelah ada tambahan)
            // ===============================
            $totalRow =
                floatval($row->nilai_total_umum ?? 0)
                + floatval($row->nilai_total_harian ?? 0)
                + floatval($row->nilai_total_bulanan ?? 0)
                + floatval($row->nilai_total_tambahan ?? 0);

            // ===============================
            //  FORMAT NUMBER (TETAP)
            // ===============================
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

        // ambil data sama dengan laporan PDF
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // ==========================
        // 🔸 GROUPING DATA
        // ==========================
        $grouped = [];
        foreach ($allData as $row) {
            $barang   = $row->barangName ?? '-';
            $supplier = $row->supplierName ?? '-';
            $divisi   = $row->divisiName ?? '-';
            $satuan   = $row->satuanName ?? '-';
            $poNum    = $row->poNum ?? '-';

            $qtyDetail  = floatval($row->qtyPO ?? 0);
            $qtyTotalPO = floatval($row->sum_qtyPO ?? 0);
            $proporsi   = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // proporsional untuk tambahan (subsidi)
            $dppTambahan   = floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphTambahan   = floatval($row->sum_pph_tambahan ?? 0) * $proporsi;
            $totalTambahan = floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$item) {
                if (
                    $item['Supplier'] === $supplier &&
                    $item['Divisi'] === $divisi &&
                    $item['poNum'] === $poNum
                ) {
                    $item['Qty']            += $qtyDetail;
                    $item['DPP Umum']       += floatval($row->dpp_umum ?? 0);
                    $item['PPh Umum']       += floatval($row->pph_umum ?? 0);
                    $item['Total Umum']     += floatval($row->nilai_total_umum ?? 0);
                    $item['DPP Harian']     += floatval($row->dpp_harian ?? 0);
                    $item['PPh Harian']     += floatval($row->pph_harian ?? 0);
                    $item['Total Harian']   += floatval($row->nilai_total_harian ?? 0);
                    $item['DPP Bulanan']    += floatval($row->dpp_bulanan ?? 0);
                    $item['PPh Bulanan']    += floatval($row->pph_bulanan ?? 0);
                    $item['Total Bulanan']  += floatval($row->nilai_total_bulanan ?? 0);
                    $item['DPP Tambahan']   += $dppTambahan;
                    $item['PPh Tambahan']   += $pphTambahan;
                    $item['Total Tambahan'] += $totalTambahan;
                    $item['Total']          += (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        $totalTambahan
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
                    'poNum'           => $poNum,
                    'Satuan'          => $satuan,
                    'Qty'             => $qtyDetail,
                    'poDate'          => $row->poDate ?? '-',
                    'warehouseName'   => $row->warehouseName ?? '-',
                    'companyName'     => $row->companyName ?? '-',
                    'DPP Umum'        => floatval($row->dpp_umum ?? 0),
                    'PPh Umum'        => floatval($row->pph_umum ?? 0),
                    'Total Umum'      => floatval($row->nilai_total_umum ?? 0),
                    'DPP Harian'      => floatval($row->dpp_harian ?? 0),
                    'PPh Harian'      => floatval($row->pph_harian ?? 0),
                    'Total Harian'    => floatval($row->nilai_total_harian ?? 0),
                    'DPP Bulanan'     => floatval($row->dpp_bulanan ?? 0),
                    'PPh Bulanan'     => floatval($row->pph_bulanan ?? 0),
                    'Total Bulanan'   => floatval($row->nilai_total_bulanan ?? 0),
                    'DPP Tambahan'    => $dppTambahan,
                    'PPh Tambahan'    => $pphTambahan,
                    'Total Tambahan'  => $totalTambahan,
                    'Total'           => (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        $totalTambahan
                    ),
                ];
            }
        }

        ksort($grouped);

        // ==========================
        // 🔸 EXCEL SETUP
        // ==========================
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pendapatan Supplier');

        $rowNum = 1;
        $sheet->setCellValue("A{$rowNum}", "Laporan Pendapatan Supplier Lokal BB");
        $sheet->mergeCells("A{$rowNum}:V{$rowNum}");
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true)->setSize(14);
        $rowNum += 2;

        // Header (tidak diubah)
        $headers = [
            ["No", "Supplier", "No PO", "Tgl PO", "Department", "Gudang", "Qty", "Satuan", "Unit", "Umum", "", "", "Harian", "", "", "Bulanan", "", "", "Tambahan", "", "", "Total"],
            ["", "", "", "", "", "", "", "", "", "DPP", "PPh", "Dibayarkan", "DPP", "PPh", "Dibayarkan", "DPP", "PPh", "Dibayarkan", "DPP", "PPh", "Dibayarkan", ""]
        ];

        $sheet->fromArray($headers[0], null, "A{$rowNum}");
        $sheet->fromArray($headers[1], null, "A" . ($rowNum + 1));

        // Merge header persis sebelumnya
        $sheet->mergeCells("A{$rowNum}:A" . ($rowNum + 1));
        $sheet->mergeCells("B{$rowNum}:B" . ($rowNum + 1));
        $sheet->mergeCells("C{$rowNum}:C" . ($rowNum + 1));
        $sheet->mergeCells("D{$rowNum}:D" . ($rowNum + 1));
        $sheet->mergeCells("E{$rowNum}:E" . ($rowNum + 1));
        $sheet->mergeCells("F{$rowNum}:F" . ($rowNum + 1));
        $sheet->mergeCells("G{$rowNum}:G" . ($rowNum + 1));
        $sheet->mergeCells("H{$rowNum}:H" . ($rowNum + 1));
        $sheet->mergeCells("I{$rowNum}:I" . ($rowNum + 1));
        $sheet->mergeCells("J{$rowNum}:L{$rowNum}");
        $sheet->mergeCells("M{$rowNum}:O{$rowNum}");
        $sheet->mergeCells("P{$rowNum}:R{$rowNum}");
        $sheet->mergeCells("S{$rowNum}:U{$rowNum}");
        $sheet->mergeCells("V{$rowNum}:V" . ($rowNum + 1));

        $sheet->getStyle("A{$rowNum}:V" . ($rowNum + 1))->getFont()->setBold(true);
        $sheet->getStyle("A{$rowNum}:V" . ($rowNum + 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $rowNum += 2;

        $cols = range('A', 'V');
        $no = 1;
        $grandTotal = 0;

        // ==========================
        // 🔸 ISI DATA + SUBTOTAL
        // ==========================
        foreach ($grouped as $barangName => $rows) {
            $sheet->setCellValue("A{$rowNum}", "Bahan Baku: " . $barangName);
            $sheet->mergeCells("A{$rowNum}:V{$rowNum}");
            $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
            $rowNum++;

            $subtotal = array_fill_keys([
                'Qty',
                'DPP Umum',
                'PPh Umum',
                'Total Umum',
                'DPP Harian',
                'PPh Harian',
                'Total Harian',
                'DPP Bulanan',
                'PPh Bulanan',
                'Total Bulanan',
                'DPP Tambahan',
                'PPh Tambahan',
                'Total Tambahan',
                'Total'
            ], 0);

            foreach ($rows as $row) {
                $idx = 0;
                $sheet->setCellValue($cols[$idx++] . $rowNum, $no++);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Supplier']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['poNum']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['poDate']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Divisi']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['warehouseName']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Qty']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Satuan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['companyName']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['DPP Umum']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['PPh Umum']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Total Umum']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['DPP Harian']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['PPh Harian']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Total Harian']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['DPP Bulanan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['PPh Bulanan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Total Bulanan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['DPP Tambahan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['PPh Tambahan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Total Tambahan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Total']);
                $rowNum++;

                foreach ($subtotal as $k => &$v) $v += $row[$k];
                $grandTotal += $row['Total'];
            }

            // subtotal
            $sheet->setCellValue("A{$rowNum}", "Total {$barangName}");
            $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
            $sheet->getStyle("A{$rowNum}:V{$rowNum}")->getFont()->setBold(true);
            $idx = 6;
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Qty']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, '');
            $sheet->setCellValue($cols[$idx++] . $rowNum, '');
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['DPP Umum']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['PPh Umum']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Total Umum']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['DPP Harian']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['PPh Harian']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Total Harian']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['DPP Bulanan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['PPh Bulanan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Total Bulanan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['DPP Tambahan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['PPh Tambahan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Total Tambahan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Total']);
            $rowNum++;
        }

        // ==========================
        // 🔸 FORMAT ANGKA
        // ==========================
        $lastRow = $rowNum;
        $sheet->getStyle("G3:V{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00'); // Format ribuan & 2 desimal tanpa simbol

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        // Terapkan border ke seluruh area dari header (baris 3) sampai baris terakhir
        $sheet->getStyle("A3:V{$lastRow}")->applyFromArray($borderStyle);

        // Ratakan isi tengah untuk header
        $sheet->getStyle("A3:V4")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Bold + shading abu untuk baris subtotal
        for ($i = 3; $i <= $lastRow; $i++) {
            $cellVal = $sheet->getCell("A{$i}")->getValue();
            if (is_string($cellVal) && str_starts_with($cellVal, 'Total ')) {
                $sheet->getStyle("A{$i}:V{$i}")->getFont()->setBold(true);
                $sheet->getStyle("A{$i}:V{$i}")
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF2F2F2');
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Laporan_Pendapatan_Supplier_Lokal_BB.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $writer->save("php://output");
        exit();
    }

    public function exportPDFPendapatanSupplier()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

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

        // ambil data sama seperti Excel
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // ==========================
        // 🔸 GROUPING DATA PER BARANG
        // ==========================
        $grouped = [];

        foreach ($allData as $row) {
            $r = is_array($row) ? (object)$row : $row;

            $barang   = $r->barangName ?? '-';
            $supplier = $r->supplierName ?? '-';
            $divisi   = $r->divisiName ?? '-';
            $satuan   = $r->satuanName ?? '-';
            $poNum    = $r->poNum ?? '-';

            $qtyDetail  = floatval($r->qtyPO ?? 0);
            $qtyTotalPO = floatval($r->sum_qtyPO ?? 0);
            $proporsi   = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // proporsional tambahan
            $dppTambahan   = floatval($r->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphTambahan   = floatval($r->sum_pph_tambahan ?? 0) * $proporsi;
            $totalTambahan = floatval($r->sum_nilai_total_tambahan ?? 0) * $proporsi;

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$item) {
                if (
                    $item['Supplier'] === $supplier &&
                    $item['Divisi'] === $divisi &&
                    $item['poNum'] === $poNum
                ) {
                    $item['Qty']            += $qtyDetail;
                    $item['DPP Umum']       += floatval($r->dpp_umum ?? 0);
                    $item['PPh Umum']       += floatval($r->pph_umum ?? 0);
                    $item['Total Umum']     += floatval($r->nilai_total_umum ?? 0);
                    $item['DPP Harian']     += floatval($r->dpp_harian ?? 0);
                    $item['PPh Harian']     += floatval($r->pph_harian ?? 0);
                    $item['Total Harian']   += floatval($r->nilai_total_harian ?? 0);
                    $item['DPP Bulanan']    += floatval($r->dpp_bulanan ?? 0);
                    $item['PPh Bulanan']    += floatval($r->pph_bulanan ?? 0);
                    $item['Total Bulanan']  += floatval($r->nilai_total_bulanan ?? 0);
                    $item['DPP Tambahan']   += $dppTambahan;
                    $item['PPh Tambahan']   += $pphTambahan;
                    $item['Total Tambahan'] += $totalTambahan;
                    $item['Total']          += (
                        floatval($r->nilai_total_umum ?? 0) +
                        floatval($r->nilai_total_harian ?? 0) +
                        floatval($r->nilai_total_bulanan ?? 0) +
                        $totalTambahan
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
                    'poNum'           => $poNum,
                    'Satuan'          => $satuan,
                    'Qty'             => $qtyDetail,
                    'poDate'          => $r->poDate ?? '-',
                    'warehouseName'   => $r->warehouseName ?? '-',
                    'companyName'     => $r->companyName ?? '-',
                    'DPP Umum'        => floatval($r->dpp_umum ?? 0),
                    'PPh Umum'        => floatval($r->pph_umum ?? 0),
                    'Total Umum'      => floatval($r->nilai_total_umum ?? 0),
                    'DPP Harian'      => floatval($r->dpp_harian ?? 0),
                    'PPh Harian'      => floatval($r->pph_harian ?? 0),
                    'Total Harian'    => floatval($r->nilai_total_harian ?? 0),
                    'DPP Bulanan'     => floatval($r->dpp_bulanan ?? 0),
                    'PPh Bulanan'     => floatval($r->pph_bulanan ?? 0),
                    'Total Bulanan'   => floatval($r->nilai_total_bulanan ?? 0),
                    'DPP Tambahan'    => $dppTambahan,
                    'PPh Tambahan'    => $pphTambahan,
                    'Total Tambahan'  => $totalTambahan,
                    'Total'           => (
                        floatval($r->nilai_total_umum ?? 0) +
                        floatval($r->nilai_total_harian ?? 0) +
                        floatval($r->nilai_total_bulanan ?? 0) +
                        $totalTambahan
                    ),
                ];
            }
        }

        ksort($grouped);

        // ==========================
        // 🔸 KIRIM KE VIEW
        // ==========================
        $data = [
            'header' => "Laporan Pendapatan Supplier Lokal BB",
            'tanggalAwal' => $this->request->getVar('dateStart'),
            'tanggalAkhir' => $this->request->getVar('dateEnd'),
            'data' => $grouped,
        ];

        $domPdf = new \Dompdf\Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/PendapatanSupplier/print', $data));
        $domPdf->setPaper('a4', 'landscape');
        $domPdf->render();
        $domPdf->stream('Pendapatan Supplier Lokal BB', array("Attachment" => false));
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

        // ambil data sama dengan laporan PDF
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // ==========================
        // 🔸 GROUPING DATA
        // ==========================
        $grouped = [];
        foreach ($allData as $row) {
            $barang   = $row->barangName ?? '-';
            $supplier = $row->supplierName ?? '-';
            $divisi   = $row->divisiName ?? '-';
            $satuan   = $row->satuanName ?? '-';
            $poNum    = $row->poNum ?? '-';

            $qtyDetail  = floatval($row->qtyPO ?? 0);
            $qtyTotalPO = floatval($row->sum_qtyPO ?? 0);
            $proporsi   = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // proporsional untuk tambahan (subsidi)
            $dppTambahan   = floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphTambahan   = floatval($row->sum_pph_tambahan ?? 0) * $proporsi;
            $totalTambahan = floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$item) {
                if (
                    $item['Supplier'] === $supplier &&
                    $item['Divisi'] === $divisi &&
                    $item['poNum'] === $poNum
                ) {
                    $item['Qty']            += $qtyDetail;
                    $item['DPP Umum']       += floatval($row->dpp_umum ?? 0);
                    $item['PPh Umum']       += floatval($row->pph_umum ?? 0);
                    $item['Total Umum']     += floatval($row->nilai_total_umum ?? 0);
                    $item['DPP Harian']     += floatval($row->dpp_harian ?? 0);
                    $item['PPh Harian']     += floatval($row->pph_harian ?? 0);
                    $item['Total Harian']   += floatval($row->nilai_total_harian ?? 0);
                    $item['DPP Bulanan']    += floatval($row->dpp_bulanan ?? 0);
                    $item['PPh Bulanan']    += floatval($row->pph_bulanan ?? 0);
                    $item['Total Bulanan']  += floatval($row->nilai_total_bulanan ?? 0);
                    $item['DPP Tambahan']   += $dppTambahan;
                    $item['PPh Tambahan']   += $pphTambahan;
                    $item['Total Tambahan'] += $totalTambahan;
                    $item['Total']          += (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        $totalTambahan
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
                    'poNum'           => $poNum,
                    'Satuan'          => $satuan,
                    'Qty'             => $qtyDetail,
                    'poDate'          => $row->poDate ?? '-',
                    'warehouseName'   => $row->warehouseName ?? '-',
                    'companyName'     => $row->companyName ?? '-',
                    'DPP Umum'        => floatval($row->dpp_umum ?? 0),
                    'PPh Umum'        => floatval($row->pph_umum ?? 0),
                    'Total Umum'      => floatval($row->nilai_total_umum ?? 0),
                    'DPP Harian'      => floatval($row->dpp_harian ?? 0),
                    'PPh Harian'      => floatval($row->pph_harian ?? 0),
                    'Total Harian'    => floatval($row->nilai_total_harian ?? 0),
                    'DPP Bulanan'     => floatval($row->dpp_bulanan ?? 0),
                    'PPh Bulanan'     => floatval($row->pph_bulanan ?? 0),
                    'Total Bulanan'   => floatval($row->nilai_total_bulanan ?? 0),
                    'DPP Tambahan'    => $dppTambahan,
                    'PPh Tambahan'    => $pphTambahan,
                    'Total Tambahan'  => $totalTambahan,
                    'Total'           => (
                        floatval($row->nilai_total_umum ?? 0) +
                        floatval($row->nilai_total_harian ?? 0) +
                        floatval($row->nilai_total_bulanan ?? 0) +
                        $totalTambahan
                    ),
                ];
            }
        }

        ksort($grouped);

        // ==========================
        // 🔸 EXCEL SETUP
        // ==========================
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pendapatan Supplier');

        $rowNum = 1;
        $sheet->setCellValue("A{$rowNum}", "Laporan Pendapatan Supplier Lokal BB");
        $sheet->mergeCells("A{$rowNum}:J{$rowNum}");
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true)->setSize(14);
        $rowNum += 2;

        // Header (tidak diubah)
        $headers = [
            ["No", "Supplier", "No PO", "Tgl PO", "Department", "Gudang", "Qty", "Satuan", "Tambahan Bulanan", "Total"],
            ["", "", "", "", "", "", "", "", "Total", ""]
        ];

        $sheet->fromArray($headers[0], null, "A{$rowNum}");
        $sheet->fromArray($headers[1], null, "A" . ($rowNum + 1));

        // Merge header persis sebelumnya
        $sheet->mergeCells("A{$rowNum}:A" . ($rowNum + 1));
        $sheet->mergeCells("B{$rowNum}:B" . ($rowNum + 1));
        $sheet->mergeCells("C{$rowNum}:C" . ($rowNum + 1));
        $sheet->mergeCells("D{$rowNum}:D" . ($rowNum + 1));
        $sheet->mergeCells("E{$rowNum}:E" . ($rowNum + 1));
        $sheet->mergeCells("F{$rowNum}:F" . ($rowNum + 1));
        $sheet->mergeCells("G{$rowNum}:G" . ($rowNum + 1));
        $sheet->mergeCells("H{$rowNum}:H" . ($rowNum + 1));
        $sheet->mergeCells("J{$rowNum}:J" . ($rowNum + 1));

        $sheet->getStyle("A{$rowNum}:J" . ($rowNum + 1))->getFont()->setBold(true);
        $sheet->getStyle("A{$rowNum}:J" . ($rowNum + 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $rowNum += 2;

        $cols = range('A', 'J');
        $no = 1;
        $grandTotal = 0;

        // ==========================
        // 🔸 ISI DATA + SUBTOTAL
        // ==========================
        foreach ($grouped as $barangName => $rows) {
            $sheet->setCellValue("A{$rowNum}", "Bahan Baku: " . $barangName);
            $sheet->mergeCells("A{$rowNum}:J{$rowNum}");
            $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
            $rowNum++;

            $subtotal = array_fill_keys([
                'Qty',
                'Total Bulanan',
                'Total'
            ], 0);

            foreach ($rows as $row) {
                $idx = 0;
                $sheet->setCellValue($cols[$idx++] . $rowNum, $no++);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Supplier']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['poNum']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['poDate']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Divisi']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['warehouseName']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Qty']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Satuan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Total Bulanan']);
                $sheet->setCellValue($cols[$idx++] . $rowNum, $row['Total']);
                $rowNum++;

                foreach ($subtotal as $k => &$v) $v += $row[$k];
                $grandTotal += $row['Total'];
            }

            // subtotal
            $sheet->setCellValue("A{$rowNum}", "Total {$barangName}");
            $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFont()->setBold(true);
            $idx = 6;
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Qty']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, '');
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Total Bulanan']);
            $sheet->setCellValue($cols[$idx++] . $rowNum, $subtotal['Total']);
            $rowNum++;
        }

        // ==========================
        // 🔸 FORMAT ANGKA
        // ==========================
        $lastRow = $rowNum;
        $sheet->getStyle("G3:J{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00'); // Format ribuan & 2 desimal tanpa simbol

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        // Terapkan border ke seluruh area dari header (baris 3) sampai baris terakhir
        $sheet->getStyle("A3:J{$lastRow}")->applyFromArray($borderStyle);

        // Ratakan isi tengah untuk header
        $sheet->getStyle("A3:J4")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Bold + shading abu untuk baris subtotal
        for ($i = 3; $i <= $lastRow; $i++) {
            $cellVal = $sheet->getCell("A{$i}")->getValue();
            if (is_string($cellVal) && str_starts_with($cellVal, 'Total ')) {
                $sheet->getStyle("A{$i}:J{$i}")->getFont()->setBold(true);
                $sheet->getStyle("A{$i}:J{$i}")
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF2F2F2');
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Laporan_Pendapatan_Supplier_Lokal_BB.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $writer->save("php://output");
        exit();
    }

    public function exportPDFPendapatanSupplierPembelian()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

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

        // ambil data sama seperti Excel
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // ==========================
        // 🔸 GROUPING DATA PER BARANG
        // ==========================
        $grouped = [];

        foreach ($allData as $row) {
            $r = is_array($row) ? (object)$row : $row;

            $barang   = $r->barangName ?? '-';
            $supplier = $r->supplierName ?? '-';
            $divisi   = $r->divisiName ?? '-';
            $satuan   = $r->satuanName ?? '-';
            $poNum    = $r->poNum ?? '-';

            $qtyDetail  = floatval($r->qtyPO ?? 0);
            $qtyTotalPO = floatval($r->sum_qtyPO ?? 0);
            $proporsi   = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // proporsional tambahan
            $dppTambahan   = floatval($r->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphTambahan   = floatval($r->sum_pph_tambahan ?? 0) * $proporsi;
            $totalTambahan = floatval($r->sum_nilai_total_tambahan ?? 0) * $proporsi;

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $found = false;
            foreach ($grouped[$barang] as &$item) {
                if (
                    $item['Supplier'] === $supplier &&
                    $item['Divisi'] === $divisi &&
                    $item['poNum'] === $poNum
                ) {
                    $item['Qty']            += $qtyDetail;
                    $item['DPP Umum']       += floatval($r->dpp_umum ?? 0);
                    $item['PPh Umum']       += floatval($r->pph_umum ?? 0);
                    $item['Total Umum']     += floatval($r->nilai_total_umum ?? 0);
                    $item['DPP Harian']     += floatval($r->dpp_harian ?? 0);
                    $item['PPh Harian']     += floatval($r->pph_harian ?? 0);
                    $item['Total Harian']   += floatval($r->nilai_total_harian ?? 0);
                    $item['DPP Bulanan']    += floatval($r->dpp_bulanan ?? 0);
                    $item['PPh Bulanan']    += floatval($r->pph_bulanan ?? 0);
                    $item['Total Bulanan']  += floatval($r->nilai_total_bulanan ?? 0);
                    $item['DPP Tambahan']   += $dppTambahan;
                    $item['PPh Tambahan']   += $pphTambahan;
                    $item['Total Tambahan'] += $totalTambahan;
                    $item['Total']          += (
                        floatval($r->nilai_total_umum ?? 0) +
                        floatval($r->nilai_total_harian ?? 0) +
                        floatval($r->nilai_total_bulanan ?? 0) +
                        $totalTambahan
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
                    'poNum'           => $poNum,
                    'Satuan'          => $satuan,
                    'Qty'             => $qtyDetail,
                    'poDate'          => $r->poDate ?? '-',
                    'warehouseName'   => $r->warehouseName ?? '-',
                    'companyName'     => $r->companyName ?? '-',
                    'DPP Umum'        => floatval($r->dpp_umum ?? 0),
                    'PPh Umum'        => floatval($r->pph_umum ?? 0),
                    'Total Umum'      => floatval($r->nilai_total_umum ?? 0),
                    'DPP Harian'      => floatval($r->dpp_harian ?? 0),
                    'PPh Harian'      => floatval($r->pph_harian ?? 0),
                    'Total Harian'    => floatval($r->nilai_total_harian ?? 0),
                    'DPP Bulanan'     => floatval($r->dpp_bulanan ?? 0),
                    'PPh Bulanan'     => floatval($r->pph_bulanan ?? 0),
                    'Total Bulanan'   => floatval($r->nilai_total_bulanan ?? 0),
                    'DPP Tambahan'    => $dppTambahan,
                    'PPh Tambahan'    => $pphTambahan,
                    'Total Tambahan'  => $totalTambahan,
                    'Total'           => (
                        floatval($r->nilai_total_umum ?? 0) +
                        floatval($r->nilai_total_harian ?? 0) +
                        floatval($r->nilai_total_bulanan ?? 0) +
                        $totalTambahan
                    ),
                ];
            }
        }

        ksort($grouped);

        // ==========================
        // 🔸 KIRIM KE VIEW
        // ==========================
        $data = [
            'header' => "Laporan Pendapatan Supplier Lokal BB",
            'tanggalAwal' => $this->request->getVar('dateStart'),
            'tanggalAkhir' => $this->request->getVar('dateEnd'),
            'data' => $grouped,
        ];

        $domPdf = new \Dompdf\Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/PendapatanSupplierPembelian/print', $data));
        $domPdf->setPaper('a4', 'landscape');
        $domPdf->render();
        $domPdf->stream('Pendapatan Supplier Lokal BB', array("Attachment" => false));
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

        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        $grouped = [];
        foreach ($allData as &$row) {
            $supplier = $row->supplierName ?? '-';
            $barang   = $row->barangName ?? '-';
            $key = $supplier . '||' . $barang;

            $qtyDetail   = floatval($row->qtyPO ?? 0);
            $qtyTotalPO  = floatval($row->sum_qtyPO ?? 0);
            $proporsi    = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // Tambahan (hasil proporsional)
            $row->dpp_tambahan =
                floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;

            $row->pph_tambahan =
                floatval($row->sum_pph_tambahan ?? 0) * $proporsi;

            $row->nilai_total_tambahan =
                floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

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
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

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

        // 🔹 Group per barang
        $grouped = [];

        foreach ($allData as $row) {
            $barang   = trim($row->barangName ?? '-');
            $supplier = trim($row->supplierName ?? '-');

            $qtyDetail   = floatval($row->qtyPO ?? 0);
            $qtyTotalPO  = floatval($row->sum_qtyPO ?? 0);
            $proporsi    = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // Tambahan (hasil proporsional)
            $row->dpp_tambahan =
                floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;

            $row->pph_tambahan =
                floatval($row->sum_pph_tambahan ?? 0) * $proporsi;

            $row->nilai_total_tambahan =
                floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

            if (!isset($grouped[$barang])) {
                $grouped[$barang] = [];
            }

            $key = $supplier;

            if (!isset($grouped[$barang][$key])) {
                $grouped[$barang][$key] = [
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

            $g = &$grouped[$barang][$key];
            $g['qtyPO']        += floatval($row->qtyPO ?? 0);
            $g['dppUmum']      += floatval($row->dpp_umum ?? 0);
            $g['pphUmum']      += floatval($row->pph_umum ?? 0);
            $g['totalUmum']    += floatval($row->nilai_total_umum ?? 0);
            $g['dppHarian']    += floatval($row->dpp_harian ?? 0);
            $g['pphHarian']    += floatval($row->pph_harian ?? 0);
            $g['totalHarian']  += floatval($row->nilai_total_harian ?? 0);
            $g['dppBulanan']   += floatval($row->dpp_bulanan ?? 0);
            $g['pphBulanan']   += floatval($row->pph_bulanan ?? 0);
            $g['totalBulanan'] += floatval($row->nilai_total_bulanan ?? 0);
            $g['dppSubsidi']   += floatval($row->dpp_tambahan ?? 0);
            $g['pphSubsidi']   += floatval($row->pph_tambahan ?? 0);
            $g['totalSubsidi'] += floatval($row->nilai_total_tambahan ?? 0);
            $g['totalRow']     += (
                floatval($row->nilai_total_umum ?? 0) +
                floatval($row->nilai_total_harian ?? 0) +
                floatval($row->nilai_total_bulanan ?? 0) +
                floatval($row->nilai_total_tambahan ?? 0)
            );
        }

        ksort($grouped);

        $data = [
            'header'        => "Laporan Rekap All Supplier",
            'tanggalAwal'   => $addCondition['dateStart'],
            'tanggalAkhir'  => $addCondition['dateEnd'],
            'groupedData'   => $grouped,
        ];

        // ✅ Pastikan view bisa looping per barang, lalu per supplier di dalamnya
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

        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        )['data'];

        // ===========================================================
        // 🔹 Group data seperti di PDF: per barang lalu per supplier
        // ===========================================================
        $grouped = [];

        foreach ($allData as $row) {
            $barang   = trim($row->barangName ?? '-');
            $supplier = trim($row->supplierName ?? '-');
            $divisi   = trim($row->divisiName ?? '-');
            $satuan   = trim($row->satuanName ?? '-');

            $barangKey   = preg_replace('/\s+/', ' ', $barang);
            $supplierKey = preg_replace('/\s+/', ' ', $supplier);

            $qtyDetail   = floatval($row->qtyPO ?? 0);
            $qtyTotalPO  = floatval($row->sum_qtyPO ?? 0);
            $proporsi    = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // Tambahan (hasil proporsional)
            $row->dpp_tambahan =
                floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;

            $row->pph_tambahan =
                floatval($row->sum_pph_tambahan ?? 0) * $proporsi;

            $row->nilai_total_tambahan =
                floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

            if (!isset($grouped[$barangKey])) {
                $grouped[$barangKey] = [];
            }

            if (!isset($grouped[$barangKey][$supplierKey])) {
                $grouped[$barangKey][$supplierKey] = [
                    'supplierName' => $supplier,
                    'divisiName'   => $divisi,
                    'barangName'   => $barang,
                    'satuanName'   => $satuan,
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

            $g = &$grouped[$barangKey][$supplierKey];

            $g['qtyPO']        += floatval($row->qtyPO ?? 0);
            $g['dppUmum']      += floatval($row->dpp_umum ?? 0);
            $g['pphUmum']      += floatval($row->pph_umum ?? 0);
            $g['totalUmum']    += floatval($row->nilai_total_umum ?? 0);

            $g['dppHarian']    += floatval($row->dpp_harian ?? 0);
            $g['pphHarian']    += floatval($row->pph_harian ?? 0);
            $g['totalHarian']  += floatval($row->nilai_total_harian ?? 0);

            $g['dppBulanan']   += floatval($row->dpp_bulanan ?? 0);
            $g['pphBulanan']   += floatval($row->pph_bulanan ?? 0);
            $g['totalBulanan'] += floatval($row->nilai_total_bulanan ?? 0);

            $g['dppSubsidi']   += floatval($row->dpp_tambahan ?? 0);
            $g['pphSubsidi']   += floatval($row->pph_tambahan ?? 0);
            $g['totalSubsidi'] += floatval($row->nilai_total_tambahan ?? 0);

            $g['totalRow']     += (
                floatval($row->nilai_total_umum ?? 0) +
                floatval($row->nilai_total_harian ?? 0) +
                floatval($row->nilai_total_bulanan ?? 0) +
                floatval($row->nilai_total_tambahan ?? 0)
            );
        }

        ksort($grouped);

        // ===========================================================
        // 🔹 Generate Excel (tampilan tetap sama seperti sebelumnya)
        // ===========================================================
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
        $row += 3;

        foreach ($grouped as $barangName => $rows) {
            // Judul bahan baku
            $sheet->setCellValue("A{$row}", "Bahan Baku: " . strtoupper($barangName));
            $sheet->mergeCells("A{$row}:S{$row}");
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

            $sheet->setCellValue("G{$row}", "Harian");
            $sheet->mergeCells("G{$row}:I{$row}");
            $sheet->setCellValue("J{$row}", "Tambahan Harian");
            $sheet->mergeCells("J{$row}:L{$row}");
            $sheet->setCellValue("M{$row}", "Tambahan Bulanan");
            $sheet->mergeCells("M{$row}:O{$row}");
            $sheet->setCellValue("P{$row}", "Tambahan Langsung");
            $sheet->mergeCells("P{$row}:R{$row}");
            $sheet->setCellValue("S{$row}", "TOTAL");

            // Subheader
            $row2 = $row + 1;
            $sheet->setCellValue("G{$row2}", "DPP");
            $sheet->setCellValue("H{$row2}", "PPh");
            $sheet->setCellValue("I{$row2}", "Dibayar");
            $sheet->setCellValue("J{$row2}", "DPP");
            $sheet->setCellValue("K{$row2}", "PPh");
            $sheet->setCellValue("L{$row2}", "Dibayar");
            $sheet->setCellValue("M{$row2}", "DPP");
            $sheet->setCellValue("N{$row2}", "PPh");
            $sheet->setCellValue("O{$row2}", "Dibayar");
            $sheet->setCellValue("P{$row2}", "DPP");
            $sheet->setCellValue("Q{$row2}", "PPh");
            $sheet->setCellValue("R{$row2}", "Dibayar");

            foreach (range('A', 'F') as $col) {
                $sheet->mergeCells("{$col}{$row}:{$col}{$row2}");
            }

            $sheet->getStyle("A{$row}:S{$row2}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:S{$row2}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:S{$row2}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row = $row2 + 1;

            // === Isi data ===
            $no = 1;
            $totals = [
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

            foreach ($rows as $r) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValue("B{$row}", $r['supplierName']);
                $sheet->setCellValue("C{$row}", $r['divisiName']);
                $sheet->setCellValue("D{$row}", $r['barangName']);
                $sheet->setCellValue("E{$row}", $r['satuanName']);
                $sheet->setCellValue("F{$row}", $r['qtyPO']);
                $sheet->setCellValue("G{$row}", $r['dppUmum']);
                $sheet->setCellValue("H{$row}", $r['pphUmum']);
                $sheet->setCellValue("I{$row}", $r['totalUmum']);
                $sheet->setCellValue("J{$row}", $r['dppHarian']);
                $sheet->setCellValue("K{$row}", $r['pphHarian']);
                $sheet->setCellValue("L{$row}", $r['totalHarian']);
                $sheet->setCellValue("M{$row}", $r['dppBulanan']);
                $sheet->setCellValue("N{$row}", $r['pphBulanan']);
                $sheet->setCellValue("O{$row}", $r['totalBulanan']);
                $sheet->setCellValue("P{$row}", $r['dppSubsidi']);
                $sheet->setCellValue("Q{$row}", $r['pphSubsidi']);
                $sheet->setCellValue("R{$row}", $r['totalSubsidi']);
                $sheet->setCellValue("S{$row}", $r['totalRow']);
                $sheet->getStyle("A{$row}:S{$row}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                foreach ($totals as $k => &$v) $v += $r[$k];
                $row++;
            }

            // === Subtotal per barang ===
            $sheet->setCellValue("A{$row}", "TOTAL " . strtoupper($barangName));
            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->getStyle("A{$row}:S{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:S{$row}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $col = 'F';
            foreach ($totals as $val) {
                $sheet->setCellValue($col++ . $row, $val);
            }
            $row += 2;
        }

        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("G1:S{$row}")
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

    public function exportPDFLaporanRekapAllSupplierPerSpek()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
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
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapAllSupplier/print-spek', $data));
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream('Rekap_All_Supplier.pdf', ["Attachment" => false]);
    }

    public function exportExcelLaporanRekapAllSupplierPerSpek()
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
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
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

            $qtyDetail   = floatval($row->qtyPO ?? 0);
            $qtyTotalPO  = floatval($row->sum_qtyPO ?? 0);
            $proporsi    = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // Tambahan (hasil proporsional)
            $row->dpp_tambahan =
                floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;

            $row->pph_tambahan =
                floatval($row->sum_pph_tambahan ?? 0) * $proporsi;

            $row->nilai_total_tambahan =
                floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

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

            $qtyDetail   = floatval($row->qtyPO ?? 0);
            $qtyTotalPO  = floatval($row->sum_qtyPO ?? 0);
            $proporsi    = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // Tambahan (hasil proporsional)
            $row->dpp_tambahan =
                floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;

            $row->pph_tambahan =
                floatval($row->sum_pph_tambahan ?? 0) * $proporsi;

            $row->nilai_total_tambahan =
                floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

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
        $dataResult = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
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

            // 🔸 Hitung proporsi qty detail terhadap total qty PO
            $qtyDetail = floatval($row->qtyPO ?? 0);
            $qtyTotalPO = floatval($row->sum_qtyPO ?? 0);
            $proporsi = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            // 🔸 Hitung subsidi proporsional per detail
            $dppSubsidi    = floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphSubsidi    = floatval($row->sum_pph_tambahan ?? 0) * $proporsi;
            $totalSubsidi  = floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

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
            $qtyPO         = floatval($qtyDetail ?? 0);
            $dppUmum       = floatval($row->dpp_umum ?? 0);
            $pphUmum       = floatval($row->pph_umum ?? 0);
            $totalUmum     = floatval($row->nilai_total_umum ?? 0);
            $dppHarian     = floatval($row->dpp_harian ?? 0);
            $pphHarian     = floatval($row->pph_harian ?? 0);
            $totalHarian   = floatval($row->nilai_total_harian ?? 0);
            $dppBulanan    = floatval($row->dpp_bulanan ?? 0);
            $pphBulanan    = floatval($row->pph_bulanan ?? 0);
            $totalBulanan  = floatval($row->nilai_total_bulanan ?? 0);
            $dppSubsidi    = floatval($dppSubsidi ?? 0);
            $pphSubsidi    = floatval($pphSubsidi ?? 0);
            $totalSubsidi  = floatval($totalSubsidi ?? 0);
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
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

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
        $dataResult = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        );
        $data = $dataResult['data'];

        // ================= Pengelompokan dan Perhitungan =================
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
            'dppSubsidi' => 0,
            'pphSubsidi' => 0,
            'totalSubsidi' => 0,
            'totalRow' => 0,
        ];

        foreach ($data as $row) {
            $barangName = $row->barangName;
            $subKey = $row->spekName . '|' . $row->divisiName;

            // Hitung proporsi subsidi sama seperti method all()
            $qtyDetail = floatval($row->qtyPO ?? 0);
            $qtyTotalPO = floatval($row->sum_qtyPO ?? 0);
            $proporsi = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            $dppSubsidiProp = floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphSubsidiProp = floatval($row->sum_pph_tambahan ?? 0) * $proporsi;
            $totalSubsidiProp = floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

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
                    'dppSubsidi' => 0,
                    'pphSubsidi' => 0,
                    'totalSubsidi' => 0,
                    'totalRow' => 0,
                ];
            }

            $g = &$groupedData[$barangName][$subKey];

            // Nilai utama (langsung dari query)
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

            // Subsidi proporsional (sesuai all())
            $dppSubsidi    = $dppSubsidiProp;
            $pphSubsidi    = $pphSubsidiProp;
            $totalSubsidi  = $totalSubsidiProp;

            $totalRow = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

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

            // Akumulasi total keseluruhan
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
            $grandTotals['dppSubsidi']   += $dppSubsidi;
            $grandTotals['pphSubsidi']   += $pphSubsidi;
            $grandTotals['totalSubsidi'] += $totalSubsidi;
            $grandTotals['totalRow']     += $totalRow;
        }

        // === Kirim ke view PDF ===
        $data = [
            'header' => "LAPORAN REKAP ALL BARANG (SUMMARY)",
            'tanggalAwal' => $addCondition['dateStart'],
            'tanggalAkhir' => $addCondition['dateEnd'],
            'groupedData' => $groupedData, // tetap grouped per barang
            'grandTotals' => $grandTotals
        ];

        // ================= SPREADSHEET =================
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap All Barang');

        $sheet->setCellValue('A1', 'LAPORAN REKAP ALL BARANG (SUMMARY)');
        $sheet->mergeCells('A1:S1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // === HEADER 1 ===
        $headers = [
            'NO',
            'BARANG',
            'SPEK',
            'DEPARTEMEN',
            'QTY',
            'SATUAN',
            'HARIAN',
            '',
            '',
            'TAMBAHAN HARIAN',
            '',
            '',
            'TAMBAHAN BULANAN',
            '',
            '',
            'TAMBAHAN LANGSUNG',
            '',
            '',
            'TOTAL'
        ];

        // === HEADER 2 ===
        $headers2 = [
            '',
            '',
            '',
            '',
            '',
            '',
            'DPP',
            'PPh',
            'TOTAL',
            'DPP',
            'PPh',
            'TOTAL',
            'DPP',
            'PPh',
            'TOTAL',
            'DPP',
            'PPh',
            'TOTAL',
            'TOTAL'
        ];

        // Baris header
        $headerRow1 = 3;
        $headerRow2 = 4;

        // Tulis baris pertama
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $headerRow1, $header);
            $sheet->getStyle($col . $headerRow1)->getFont()->setBold(true);
            $sheet->getStyle($col . $headerRow1)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }

        // Tulis baris kedua
        $col = 'A';
        foreach ($headers2 as $header2) {
            $sheet->setCellValue($col . $headerRow2, $header2);
            $sheet->getStyle($col . $headerRow2)->getFont()->setBold(true);
            $sheet->getStyle($col . $headerRow2)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // === MERGE SESUAI KEINGINAN ===

        // Merge kolom vertikal (NO–SATUAN & TOTAL)
        $mergeVert = ['A', 'B', 'C', 'D', 'E', 'F', 'S'];
        foreach ($mergeVert as $col) {
            $sheet->mergeCells("{$col}{$headerRow1}:{$col}{$headerRow2}");
        }

        // Merge grup horizontal (kategori utama)
        $sheet->mergeCells("G{$headerRow1}:I{$headerRow1}"); // HARIAN
        $sheet->mergeCells("J{$headerRow1}:L{$headerRow1}"); // TAMBAHAN HARIAN
        $sheet->mergeCells("M{$headerRow1}:O{$headerRow1}"); // TAMBAHAN BULANAN
        $sheet->mergeCells("P{$headerRow1}:R{$headerRow1}"); // TAMBAHAN LANGSUNG

        // Border dan alignment global
        $sheet->getStyle("A{$headerRow1}:S{$headerRow2}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle("A{$headerRow1}:S{$headerRow2}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $rowNum = $headerRow2 + 1; // baris awal data
        $no = 1;

        foreach ($groupedData as $barangName => $subGroups) {
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
                $sheet->setCellValue("P{$rowNum}", $record['dppSubsidi']);
                $sheet->setCellValue("Q{$rowNum}", $record['pphSubsidi']);
                $sheet->setCellValue("R{$rowNum}", $record['totalSubsidi']);
                $sheet->setCellValue("S{$rowNum}", $record['totalRow']);

                $sheet->getStyle("E{$rowNum}:S{$rowNum}")
                    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                foreach ($groupTotals as $key => $v) {
                    if (isset($record[$key])) {
                        $groupTotals[$key] += $record[$key];
                    }
                }

                $rowNum++;
            }

            // ✅ SUBTOTAL BARANG LENGKAP
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
            $sheet->setCellValue("P{$rowNum}", $groupTotals['dppSubsidi']);
            $sheet->setCellValue("Q{$rowNum}", $groupTotals['pphSubsidi']);
            $sheet->setCellValue("R{$rowNum}", $groupTotals['totalSubsidi']);
            $sheet->setCellValue("S{$rowNum}", $groupTotals['totalRow']);

            $sheet->getStyle("A{$rowNum}:S{$rowNum}")->getFont()->setBold(true);
            $sheet->getStyle("E{$rowNum}:S{$rowNum}")
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $rowNum++;
        }

        // ✅ GRAND TOTAL
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
        $sheet->setCellValue("P{$rowNum}", $grandTotals['dppSubsidi']);
        $sheet->setCellValue("Q{$rowNum}", $grandTotals['pphSubsidi']);
        $sheet->setCellValue("R{$rowNum}", $grandTotals['totalSubsidi']);
        $sheet->setCellValue("S{$rowNum}", $grandTotals['totalRow']);

        $sheet->getStyle("A{$rowNum}:S{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("E{$rowNum}:S{$rowNum}")
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle("A3:S{$rowNum}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // OUTPUT
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
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

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
        $dataResult = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierNew(
            $availableSort,
            $condition,
            $addCondition,
            null,
            null
        );
        $data = $dataResult['data'];

        // ================= Pengelompokan dan Perhitungan =================
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
            'dppSubsidi' => 0,
            'pphSubsidi' => 0,
            'totalSubsidi' => 0,
            'totalRow' => 0,
        ];

        foreach ($data as $row) {
            $barangName = $row->barangName;
            $subKey = $row->spekName . '|' . $row->divisiName;

            // Hitung proporsi subsidi sama seperti method all()
            $qtyDetail = floatval($row->qtyPO ?? 0);
            $qtyTotalPO = floatval($row->sum_qtyPO ?? 0);
            $proporsi = ($qtyTotalPO > 0) ? ($qtyDetail / $qtyTotalPO) : 0;

            $dppSubsidiProp = floatval($row->sum_dpp_tambahan ?? 0) * $proporsi;
            $pphSubsidiProp = floatval($row->sum_pph_tambahan ?? 0) * $proporsi;
            $totalSubsidiProp = floatval($row->sum_nilai_total_tambahan ?? 0) * $proporsi;

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
                    'dppSubsidi' => 0,
                    'pphSubsidi' => 0,
                    'totalSubsidi' => 0,
                    'totalRow' => 0,
                ];
            }

            $g = &$groupedData[$barangName][$subKey];

            // Nilai utama (langsung dari query)
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

            // Subsidi proporsional (sesuai all())
            $dppSubsidi    = $dppSubsidiProp;
            $pphSubsidi    = $pphSubsidiProp;
            $totalSubsidi  = $totalSubsidiProp;

            $totalRow = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

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

            // Akumulasi total keseluruhan
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
            $grandTotals['dppSubsidi']   += $dppSubsidi;
            $grandTotals['pphSubsidi']   += $pphSubsidi;
            $grandTotals['totalSubsidi'] += $totalSubsidi;
            $grandTotals['totalRow']     += $totalRow;
        }

        // === Kirim ke view PDF ===
        $data = [
            'header' => "LAPORAN REKAP ALL BARANG (SUMMARY)",
            'tanggalAwal' => $addCondition['dateStart'],
            'tanggalAkhir' => $addCondition['dateEnd'],
            'groupedData' => $groupedData, // tetap grouped per barang
            'grandTotals' => $grandTotals
        ];

        $domPdf = new \Dompdf\Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/RekapAllBarang/print', $data));
        $domPdf->setPaper('legal', 'landscape');

        // Perkecil tampilan agar tabel muat
        $canvas = $domPdf->getCanvas();
        $canvas->scale(0.8, 0.8, 0, 0);

        $domPdf->render();
        $domPdf->stream('Rekap_All_Barang_Summary.pdf', ["Attachment" => false]);
        exit();
    }
}
