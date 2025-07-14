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
            'getDivisi' => $this->divisiModel->getDivisiAccess(),
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
            "divisiId"         => $this->request->getGet("filter_divisi"),
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

        $totals = [
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
            'subsidi' => 0,
            'pphSubsidi' => 0,
            'totalSubsidi' => 0,
            'totalRow' => 0,
        ];

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
            } elseif ($pphMode === "Supplier") {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($row->dppUmum * $nilai_pph2) * $qty;
                $totalUmum = $dppUmum - $pphUmum;
            } else {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($pphMode === "Supplier") ? ($row->dppUmum / $nilai_pph * $nilai_pph2) * $qty : 0;
                // dd($pphUmum);
                $totalUmum = $dppUmum + $pphUmum;
            }

            // HARIAN
            if ($pphMode === "Company") {
                $dppHarian = ($row->dppHarian / $nilai_pph) * $qty;
                $pphHarian = ($row->dppHarian / $nilai_pph * $nilai_pph2) * $qty;
                $totalHarian = $dppHarian - $pphHarian;
            } elseif ($pphMode === "Supplier") {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = ($row->dppHarian * $nilai_pph2) * $qty;
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
            } elseif ($pphMode === "Supplier") {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = ($row->dppBulanan * $nilai_pph2) * $qty;
                $totalBulanan = $dppBulanan - $pphBulanan;
            } else {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = ($pphMode === "Supplier") ? ($row->dppBulanan * $nilai_pph2) * $qty : 0;
                $totalBulanan = $dppBulanan + $pphBulanan;
            }

            $totalRow = $totalUmum + $totalHarian + $totalBulanan;

            $totals['qtyAll'] += $qty;
            $totals['dppUmum'] += $dppUmum;
            $totals['pphUmum'] += $pphUmum;
            $totals['totalUmum'] += $totalUmum;
            $totals['dppHarian'] += $dppHarian;
            $totals['pphHarian'] += $pphHarian;
            $totals['totalHarian'] += $totalHarian;
            $totals['dppBulanan'] += $dppBulanan;
            $totals['pphBulanan'] += $pphBulanan;
            $totals['totalBulanan'] += $totalBulanan;
            $totals['subsidi'] += $dppSubsidi ?? 0;
            $totals['pphSubsidi'] += $pphSubsidi ?? 0;
            $totals['totalSubsidi'] += $totalSubsidi ?? 0;
            $totals['totalRow'] += $totalRow;

            if (!isset($groupedData[$poId])) {
                $subsidi = $row->subsidi == 0 ? ($row->cong_batasan - $row->cong_sebenarnya) : $row->subsidi;

                if ($pphMode === "Company") {
                    $dppSubsidi = $subsidi / $nilai_pph;
                    $pphSubsidi = $subsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } elseif ($pphMode === "Supplier") {
                    $dppSubsidi = $subsidi;
                    $pphSubsidi = ($subsidi * $nilai_pph2);
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $subsidi;
                    $pphSubsidi = ($pphMode === "Supplier") ? ($subsidi * $nilai_pph2) : 0;
                    $totalSubsidi = $dppSubsidi + $pphSubsidi;
                }

                $groupedData[$poId] = [
                    'supplierName' => $row->supplierName,
                    'poNum' => $row->poNum,
                    'poDate' => $row->poDate,
                    'satuanName' => $row->satuanName,
                    'companyName' => $row->companyName,
                    'divisiName' => $row->divisiName,
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
                    'tambahan' => $row->subsidi,
                    'subsidi' => $dppSubsidi,
                    'pphSubsidi' => $pphSubsidi,
                    'totalSubsidi' => $totalSubsidi,
                    'nilai_pph' => $nilai_pph,
                    'nilai_pph2' => $nilai_pph2,
                    'totalRow' => $totalRow,
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
            $subsidi = $row['tambahan'] == "0" ? $row['subsidi'] * $row['qtyPO'] : $row['subsidi'];
            $pphSubsidi = $subsidi * $row['nilai_pph2'];
            $totalSubsidi = $subsidi - $pphSubsidi;
            $row['subsidi'] = $subsidi;
            $row['pphSubsidi'] = $pphSubsidi;
            $row['totalSubsidi'] = $totalSubsidi;
            $row['totalRow'] = $row['totalRow'] + $totalSubsidi;

            foreach ($row as $key => $val) {
                if (is_numeric($val) && $key !== 'no') {
                    $row[$key] = number_format($val, 2, '.', ',');
                }
            }
        }

        foreach ($totals as $key => $val) {
            $totals[$key] = number_format($val, 2, '.', ',');
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
            'footerTotals'    => $totals, // <--- tambahkan ini
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
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);

        // Gunakan function yang sama seperti di allLaporanPendapatanSupplier()
        $processed = $this->processLaporanPendapatanSupplier($dataBBLokal['data']);
        $groupedData = $processed;

        // Kelompokkan data berdasarkan barangName
        $barangGrouped = [];
        foreach ($groupedData as $row) {
            $barangGrouped[$row['barangName']][] = $row;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pendapatan Supplier');

        $sheet->setCellValue("A1", 'Pendapatan Supplier Per PO');
        $sheet->mergeCells("A1:V1");
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $rowNo = 3;

        $headers1 = [
            'No',
            'Supplier',
            'No PO',
            'Tgl PO',
            'Department',
            'Gudang',
            'Qty',
            'Satuan',
            'Unit',
            'Harian',
            '',
            '',
            'Tambahan Harian',
            '',
            '',
            'Tambahan Bulanan',
            '',
            '',
            'Tambahan Langsung',
            '',
            '',
            'Total'
        ];
        $sheet->fromArray($headers1, null, "A{$rowNo}");

        foreach (range('A', 'I') as $col) {
            $sheet->mergeCells("{$col}{$rowNo}:{$col}" . ($rowNo + 1));
        }

        $sheet->mergeCells("J{$rowNo}:L{$rowNo}");
        $sheet->mergeCells("M{$rowNo}:O{$rowNo}");
        $sheet->mergeCells("P{$rowNo}:R{$rowNo}");
        $sheet->mergeCells("S{$rowNo}:U{$rowNo}");
        $sheet->mergeCells("V{$rowNo}:V" . ($rowNo + 1));

        $sheet->getStyle("A{$rowNo}:V" . ($rowNo + 1))->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowNo . ':V' . ($rowNo + 1))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $rowNo++;

        $headers2 = array_merge(array_fill(0, 9, ''), [
            'DPP',
            'PPh',
            'Total',
            'DPP',
            'PPh',
            'Total',
            'DPP',
            'PPh',
            'Total',
            'DPP',
            'PPh',
            'Total',
            '',
        ]);
        $sheet->fromArray($headers2, null, "A{$rowNo}");

        $rowNo++; // Data mulai baris ini
        $globalNo = 1;

        $columns = range('A', 'V');
        $numericColumns = [6, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];

        foreach ($barangGrouped as $barangName => $items) {
            $totals = [
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
                'subsidi' => 0,
                'pphSubsidi' => 0,
                'totalSubsidi' => 0,
                'totalRow' => 0,
            ];

            $sheet->setCellValue("A{$rowNo}", 'Bahan Baku: ' . $barangName);
            $sheet->mergeCells("A{$rowNo}:V{$rowNo}");
            $sheet->getStyle("A{$rowNo}")->getFont()->setBold(true);
            $rowNo++;

            foreach ($items as $item) {
                // Pastikan nilai numerik di-cast ke float
                foreach ($totals as $key => $_) {
                    $item[$key] = isset($item[$key]) && is_numeric($item[$key]) ? (float)$item[$key] : 0;
                }

                $item['subsidi'] = $item['tambahan'] == "0" ? $item['subsidi'] * $item['qtyPO'] : $item['subsidi'];
                $item['pphSubsidi'] = $item['subsidi'] * $item['nilai_pph2'];
                $item['totalSubsidi'] = $item['subsidi'] - $item['pphSubsidi'];
                $item['totalRow'] =
                    $item['totalUmum'] +
                    $item['totalHarian'] +
                    $item['totalBulanan'] +
                    $item['totalSubsidi'];

                $rowValues = [
                    $globalNo++,
                    $item['supplierName'],
                    $item['poNum'],
                    $item['poDate'],
                    $item['divisiName'],
                    $item['warehouseName'],
                    $item['qtyPO'],
                    $item['satuanName'],
                    $item['companyName'],
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
                ];

                foreach ($columns as $idx => $col) {
                    $value = $rowValues[$idx] ?? '';
                    if (in_array($idx, $numericColumns)) {
                        if (!is_numeric($value)) {
                            $value = 0;
                        }
                        $sheet->setCellValueExplicit($col . $rowNo, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    } else {
                        $sheet->setCellValue($col . $rowNo, $value);
                    }
                }

                // Akumulasi total
                foreach ($totals as $key => $_) {
                    $totals[$key] += $item[$key];
                }

                $rowNo++;
            }

            // Baris total per barang
            $totalRow = [
                '',
                '',
                '',
                '',
                '',
                'TOTAL',
                $totals['qtyAll'],
                '',
                '',
                $totals['dppUmum'],
                $totals['pphUmum'],
                $totals['totalUmum'],
                $totals['dppHarian'],
                $totals['pphHarian'],
                $totals['totalHarian'],
                $totals['dppBulanan'],
                $totals['pphBulanan'],
                $totals['totalBulanan'],
                $totals['subsidi'],
                $totals['pphSubsidi'],
                $totals['totalSubsidi'],
                $totals['totalRow']
            ];

            foreach ($columns as $idx => $col) {
                $value = $totalRow[$idx] ?? '';
                if (in_array($idx, $numericColumns)) {
                    if (!is_numeric($value)) {
                        $value = 0;
                    }
                    $sheet->setCellValueExplicit($col . $rowNo, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValue($col . $rowNo, $value);
                }
            }

            $sheet->getStyle("A{$rowNo}:V{$rowNo}")->getFont()->setBold(true);
            $rowNo += 3;
        }

        // Auto width
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format angka (0.00)
        $lastRow = $sheet->getHighestRow();
        $numberFormat = '#,##0.00;-#,##0.00;"0.00"';
        foreach (['G', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'] as $col) {
            $sheet->getStyle("{$col}4:{$col}{$lastRow}")
                ->getNumberFormat()
                ->setFormatCode($numberFormat);
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

    private function processLaporanPendapatanSupplier($data)
    {
        $groupedData = [];

        foreach ($data as $row) {
            // Pastikan semua nilai dasar diisi dengan default value
            $row->poPPH = $row->poPPH ?? '';
            $row->supplierNpwp = $row->supplierNpwp ?? '';
            $row->dppUmum = $row->dppUmum ?? 0;
            $row->dppHarian = $row->dppHarian ?? 0;
            $row->dppBulanan = $row->dppBulanan ?? 0;
            $row->subsidi = $row->subsidi ?? 0;
            $row->qtyPO = $row->qtyPO ?? 0;
            $row->poDate = $row->poDate ?? '';

            $poId = $row->poNum;
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            $qty = $row->qtyPO;

            // Tanggal cutoff untuk perhitungan PPh
            $cutoffDate = '2025-06-30';

            if ($row->poDate <= $cutoffDate) {
                $nilai_pph = $hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005);
                $nilai_pph2 = $hasNpwp ? 0.0025 : 0.005;
            } else {
                $nilai_pph = 1.00 - 0.0025;
                $nilai_pph2 = 0.0025;
            }

            // === UMUM ===
            $dppUmum = 0;
            $pphUmum = 0;
            $totalUmum = 0;

            if ($pphMode === "Company") {
                $dppUmum = ($row->dppUmum / $nilai_pph) * $qty;
                $pphUmum = ($row->dppUmum / $nilai_pph * $nilai_pph2) * $qty;
                $totalUmum = $dppUmum - $pphUmum;
            } elseif ($pphMode === "Supplier") {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($row->dppUmum * $nilai_pph2) * $qty;
                $totalUmum = $dppUmum - $pphUmum;
            } else {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = 0;
                $totalUmum = $dppUmum;
            }

            // === HARIAN ===
            $dppHarian = 0;
            $pphHarian = 0;
            $totalHarian = 0;

            if ($pphMode === "Company") {
                $dppHarian = ($row->dppHarian / $nilai_pph) * $qty;
                $pphHarian = ($row->dppHarian / $nilai_pph * $nilai_pph2) * $qty;
                $totalHarian = $dppHarian - $pphHarian;
            } elseif ($pphMode === "Supplier") {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = ($row->dppHarian * $nilai_pph2) * $qty;
                $totalHarian = $dppHarian - $pphHarian;
            } else {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = 0;
                $totalHarian = $dppHarian;
            }

            // === BULANAN ===
            $dppBulanan = 0;
            $pphBulanan = 0;
            $totalBulanan = 0;

            if ($pphMode === "Company") {
                $dppBulanan = ($row->dppBulanan / $nilai_pph) * $qty;
                $pphBulanan = ($row->dppBulanan / $nilai_pph * $nilai_pph2) * $qty;
                $totalBulanan = $dppBulanan - $pphBulanan;
            } elseif ($pphMode === "Supplier") {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = ($row->dppBulanan * $nilai_pph2) * $qty;
                $totalBulanan = $dppBulanan - $pphBulanan;
            } else {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = 0;
                $totalBulanan = $dppBulanan;
            }

            $totalRow = $totalUmum + $totalHarian + $totalBulanan;

            if (!isset($groupedData[$poId])) {
                // Subsidi hanya dihitung sekali per PO
                $dppSubsidi = 0;
                $pphSubsidi = 0;
                $totalSubsidi = 0;

                $subsidi = ($row->subsidi == 0) ? ($row->cong_batasan - $row->cong_sebenarnya) : $row->subsidi;

                if ($pphMode === "Company") {
                    $dppSubsidi = $subsidi / $nilai_pph;
                    $pphSubsidi = $dppSubsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } elseif ($pphMode === "Supplier") {
                    $dppSubsidi = $subsidi;
                    $pphSubsidi = $subsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $subsidi;
                    $pphSubsidi = 0;
                    $totalSubsidi = $dppSubsidi;
                }

                $groupedData[$poId] = [
                    'supplierName' => $row->supplierName ?? '',
                    'poNum' => $row->poNum ?? '',
                    'poDate' => $row->poDate ?? '',
                    'barangName' => $row->barangName ?? '',
                    'divisiName' => $row->divisiName ?? '',
                    'warehouseName' => $row->warehouseName ?? '',
                    'qtyPO' => 0,
                    'satuanName' => $row->satuanName ?? '',
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
                    'tambahan' => $row->subsidi,
                    'pphSubsidi' => $pphSubsidi,
                    'totalSubsidi' => $totalSubsidi,
                    'totalRow' => $totalRow + $totalSubsidi,
                    'poPPH' => $pphMode,
                    'nilai_pph' => $nilai_pph,
                    'nilai_pph2' => $nilai_pph2,
                ];
            } else {
                $groupedData[$poId]['totalRow'] += $totalRow;
            }

            // Akumulasi ke per-PO
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

        return array_values($groupedData);
    }

    public function exportPDFPendapatanSupplier()
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
            "divisiId"     => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'poNum'         => 'rm_purchase_orders.po_no',
            'poDate'        => 'rm_purchase_orders.po_date',
            'barangName'    => 'barang_master.barang_name',
            'warehouseName' => 'warehouses.warehouse_name',
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);
        $processedData = $this->processLaporanPendapatanSupplier($dataBBLokal['data']);
        // dd($processedData, $dataBBLokal);
        // Group by barangName
        $groupedData = [];
        foreach ($processedData as $item) {
            $barangName = $item['barangName'] ?? 'Lainnya';
            $groupedData[$barangName][] = $item;
        }

        $summaryPerBarang = [];
        foreach ($groupedData as $barang => $items) {
            foreach ($items as &$item) {
                $subsidi = $item['subsidi'] * $item['qtyPO'];

                $pphSubsidi = $subsidi * floatval($item['nilai_pph2']);
                $totalSubsidi = $subsidi - $pphSubsidi;

                $item['subsidi'] = $subsidi;
                $item['pphSubsidi'] = $pphSubsidi;
                $item['totalSubsidi'] = $totalSubsidi;
                $item['totalRow'] = $item['totalUmum'] + $item['totalHarian'] + $item['totalBulanan'] + $totalSubsidi;
            }

            $totals = [
                'totalDppUmum' => 0,
                'totalPphUmum' => 0,
                'totalTotalUmum' => 0,
                'totalDppHarian' => 0,
                'totalPphHarian' => 0,
                'totalTotalHarian' => 0,
                'totalDppBulanan' => 0,
                'totalPphBulanan' => 0,
                'totalTotalBulanan' => 0,
                'totalDppSubsidi' => 0,
                'totalPphSubsidi' => 0,
                'totalTotalSubsidi' => 0,
                'totalTotalRow' => 0,
            ];

            foreach ($items as $row) {
                $totals['totalDppUmum'] += $row['dppUmum'];
                $totals['totalPphUmum'] += $row['pphUmum'];
                $totals['totalTotalUmum'] += $row['totalUmum'];

                $totals['totalDppHarian'] += $row['dppHarian'];
                $totals['totalPphHarian'] += $row['pphHarian'];
                $totals['totalTotalHarian'] += $row['totalHarian'];

                $totals['totalDppBulanan'] += $row['dppBulanan'];
                $totals['totalPphBulanan'] += $row['pphBulanan'];
                $totals['totalTotalBulanan'] += $row['totalBulanan'];

                $totals['totalDppSubsidi'] += $row['subsidi'];
                $totals['totalPphSubsidi'] += $row['pphSubsidi'];
                $totals['totalTotalSubsidi'] += $row['totalSubsidi'];

                $totals['totalTotalRow'] += $row['totalRow'];
            }

            $summaryPerBarang[$barang] = [
                'data' => $items,
                'summary' => $totals,
            ];
        }

        $data = [
            'header' => "Laporan Pendapatan Supplier",
            'tanggalAwal' => $this->request->getVar('dateStart'),
            'tanggalAkhir' => $this->request->getVar('dateEnd'),
            'poNo' => $addCondition['poNo'] ?? 'ALL',
            'groupedData' => $summaryPerBarang,
        ];

        $domPdf = new Dompdf();
        $domPdf->loadHtml(view('Laporan/SupplierLokalBB/PendapatanSupplier/print', $data));
        $domPdf->setPaper('a4', 'landscape');
        $domPdf->render();
        $domPdf->stream('Pendapatan Supplier', array("Attachment" => false));
        exit();
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
            'getDivisi' => $this->divisiModel->getDivisiAccess(),
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

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "supplierId"   => $this->request->getGet("filter_supplier"),
            "barangId"     => $this->request->getGet("filter_barang"),
            "warehouseId"  => $this->request->getGet("filter_warehouse"),
            "poNo"         => $this->request->getGet("filter_po_no"),
            "divisiId"         => $this->request->getGet("filter_divisi"),
        ];

        $availableSort = [
            'supplierName' => 'suppliers.name',
            'barangName'   => 'barang_master.barang_name',
            'divisiName'   => 'divisis.divisi',
        ];

        // Ambil seluruh data untuk keperluan total
        $allData = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);

        $groupedData = [];
        $usedSubsidiPo = [];
        $totalTotalRow = 0;

        foreach ($allData['data'] as $row) {
            $supplierId = $row->supplier_id;
            $barangId = $row->barang_id;
            $divisiId = $row->divisi_id;
            $groupKey = $supplierId . '_' . $barangId . '_' . $divisiId;
            $poId = $row->poNum;

            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            $nilai_pph = ($row->poDate <= '2025-06-30') ? ($hasNpwp ? 0.9975 : 0.995) : 0.9975;
            $nilai_pph2 = ($row->poDate <= '2025-06-30') ? ($hasNpwp ? 0.0025 : 0.005) : 0.0025;

            $qty = $row->qtyPO;

            $dppUmum = $pphMode === "Company" ? ($row->dppUmum / $nilai_pph) * $qty : $row->dppUmum * $qty;
            $pphUmum = $pphMode === "Company" || $pphMode === "Supplier" ? ($dppUmum * $nilai_pph2) : 0;
            $totalUmum = $pphMode === "Company" || $pphMode === "Supplier" ? $dppUmum - $pphUmum : $dppUmum + $pphUmum;

            $dppHarian = $pphMode === "Company" ? ($row->dppHarian / $nilai_pph) * $qty : $row->dppHarian * $qty;
            $pphHarian = $pphMode === "Company" || $pphMode === "Supplier" ? ($dppHarian * $nilai_pph2) : 0;
            $totalHarian = $pphMode === "Company" || $pphMode === "Supplier" ? $dppHarian - $pphHarian : $dppHarian + $pphHarian;

            $dppBulanan = $pphMode === "Company" ? ($row->dppBulanan / $nilai_pph) * $qty : $row->dppBulanan * $qty;
            $pphBulanan = $pphMode === "Company" || $pphMode === "Supplier" ? ($dppBulanan * $nilai_pph2) : 0;
            $totalBulanan = $pphMode === "Company" || $pphMode === "Supplier" ? $dppBulanan - $pphBulanan : $dppBulanan + $pphBulanan;

            $totalRow = $totalUmum + $totalHarian + $totalBulanan;

            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'supplierName' => $row->supplierName,
                    'divisiName'   => $row->divisiName,
                    'barangName'   => $row->barangName,
                    'spekName'     => $row->spekName,
                    'satuanName'   => $row->satuanName,
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
                    'tambahan' => $row->subsidi,
                    'subsidi' => 0,
                    'pphSubsidi' => 0,
                    'totalSubsidi' => 0,
                    'nilai_pph' => $nilai_pph,
                    'nilai_pph2' => $nilai_pph2,
                    'totalRow'     => 0,
                ];
            }

            $g = &$groupedData[$groupKey];
            $g['qtyPO']        += $qty;
            $g['dppUmum']      += $dppUmum;
            $g['pphUmum']      += $pphUmum;
            $g['totalUmum']    += $totalUmum;
            $g['dppHarian']    += $dppHarian;
            $g['pphHarian']    += $pphHarian;
            $g['totalHarian']  += $totalHarian;
            $g['dppBulanan']   += $dppBulanan;
            $g['pphBulanan']   += $pphBulanan;
            $g['totalBulanan'] += $totalBulanan;
            $g['totalRow']     += $totalRow;
            $totalTotalRow     += $totalRow;

            if (!in_array($poId, $usedSubsidiPo)) {
                $usedSubsidiPo[] = $poId;
                $subsidi = $row->subsidi == 0 ? ($row->cong_batasan - $row->cong_sebenarnya) : $row->subsidi;

                if ($pphMode === "Company") {
                    $dppSubsidi = $subsidi / $nilai_pph;
                    $pphSubsidi = $subsidi * $nilai_pph2;
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } elseif ($pphMode === "Supplier") {
                    $dppSubsidi = $subsidi;
                    $pphSubsidi = ($subsidi * $nilai_pph2);
                    $totalSubsidi = $dppSubsidi - $pphSubsidi;
                } else {
                    $dppSubsidi = $subsidi;
                    $pphSubsidi = ($pphMode === "Supplier") ? ($subsidi * $nilai_pph2) : 0;
                    $totalSubsidi = $dppSubsidi + $pphSubsidi;
                }

                $g['subsidi'] += $dppSubsidi;
                $g['pphSubsidi'] += $pphSubsidi;
                $g['totalSubsidi'] += $totalSubsidi;
                // $g['totalRow'] += $totalSubsidi;
                // $totalTotalRow += $totalSubsidi;
            }
        }

        $groupedData = array_values($groupedData);
        $totalGrouped = count($groupedData);
        $paginatedData = array_slice($groupedData, ($currentPage - 1) * $pageSize, $pageSize);

        foreach ($paginatedData as $i => &$row) {
            $row['no'] = ($currentPage - 1) * $pageSize + $i + 1;
            $subsidi = $row['tambahan'] == "0" ? $row['subsidi'] * $row['qtyPO'] : $row['subsidi'];
            $pphSubsidi = $subsidi * $row['nilai_pph2'];
            $totalSubsidi = $subsidi - $pphSubsidi;
            $row['subsidi'] = $subsidi;
            $row['pphSubsidi'] = $pphSubsidi;
            $row['totalSubsidi'] = $totalSubsidi;
            $row['totalRow'] = $row['totalRow'] + $totalSubsidi;
            foreach ($row as $key => $val) {
                if (is_numeric($val) && $key !== 'no') {
                    $row[$key] = number_format($val, 2, '.', ',');
                }
            }
        }

        echo json_encode([
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalGrouped,
            "recordsFiltered" => $totalGrouped,
            'data'            => $paginatedData,
            'totalTotalRow'   => number_format($totalTotalRow, 2, '.', ','),
        ]);
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
            "divisiId"     => $this->request->getGet("filter_divisi"),
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
            } elseif ($pphMode === "Supplier") {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($row->dppUmum * $nilai_pph2) * $qty;
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
            } elseif ($pphMode === "Supplier") {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = ($row->dppHarian * $nilai_pph2) * $qty;
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
            } elseif ($pphMode === "Supplier") {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = ($row->dppBulanan * $nilai_pph2) * $qty;
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
                } elseif ($pphMode === "Supplier") {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($row->subsidi * $nilai_pph2);
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
            "divisiId"     => $this->request->getGet("filter_divisi"),
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
            } elseif ($pphMode === "Supplier") {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($row->dppUmum * $nilai_pph2) * $qty;
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
            } elseif ($pphMode === "Supplier") {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = ($row->dppHarian * $nilai_pph2) * $qty;
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
            } elseif ($pphMode === "Supplier") {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = ($row->dppBulanan * $nilai_pph2) * $qty;
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
                } elseif ($pphMode === "Supplier") {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($row->subsidi * $nilai_pph2);
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
        $totalQty = 0;

        foreach ($dataOrder as $item) {
            $totalQty += $item['qtyPO'] ?? 0;
            $totalDppUmum += $item['dppUmum'] ?? 0;
            $totalPphUmum += $item['pphUmum'] ?? 0;
            $totalTotalUmum += $item['totalUmum'] ?? 0;
            $totalDppHarian += $item['dppHarian'] ?? 0;
            $totalPphHarian += $item['pphHarian'] ?? 0;
            $totalTotalHarian += $item['totalHarian'] ?? 0;
            $totalDppBulanan += $item['dppBulanan'] ?? 0;
            $totalPphBulanan += $item['pphBulanan'] ?? 0;
            $totalTotalBulanan += $item['totalBulanan'] ?? 0;
            $totalDppSubsidi += $item['subsidi'] ?? 0;
            $totalPphSubsidi += $item['pphSubsidi'] ?? 0;
            $totalTotalSubsidi += $item['totalSubsidi'] ?? 0;
            $totalTotalRow += $item['totalRow'] ?? 0;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header utama
        $sheet->setCellValue('A1', "LAPORAN PENDAPATAN DETAIL SUPPLIER");
        $sheet->mergeCells('A1:Q1');
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
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

        $currentRow = 5;
        $no = 1;

        // Header (ditulis sekali saja)
        $sheet->setCellValue('A' . $currentRow, 'No.');
        $sheet->mergeCells('A' . $currentRow . ':A' . ($currentRow + 1));
        $sheet->setCellValue('B' . $currentRow, 'Supplier');
        $sheet->mergeCells('B' . $currentRow . ':B' . ($currentRow + 1));
        $sheet->setCellValue('C' . $currentRow, 'Nama Satuan');
        $sheet->mergeCells('C' . $currentRow . ':C' . ($currentRow + 1));
        $sheet->setCellValue('D' . $currentRow, 'QTY');
        $sheet->mergeCells('D' . $currentRow . ':D' . ($currentRow + 1));

        $sheet->setCellValue('E' . $currentRow, 'HARIAN');
        $sheet->mergeCells('E' . $currentRow . ':G' . $currentRow);
        $sheet->setCellValue('H' . $currentRow, 'TAMBAHAN HARIAN');
        $sheet->mergeCells('H' . $currentRow . ':J' . $currentRow);
        $sheet->setCellValue('K' . $currentRow, 'TAMBAHAN BULANAN');
        $sheet->mergeCells('K' . $currentRow . ':M' . $currentRow);
        $sheet->setCellValue('N' . $currentRow, 'TAMBAHAN LANGSUNG');
        $sheet->mergeCells('N' . $currentRow . ':P' . $currentRow);
        $sheet->setCellValue('Q' . $currentRow, 'TOTAL');
        $sheet->mergeCells('Q' . $currentRow . ':Q' . ($currentRow + 1));

        $subHeaders = [
            'E' => 'DPP',
            'F' => 'PPH',
            'G' => 'Total',
            'H' => 'DPP',
            'I' => 'PPH',
            'J' => 'Total',
            'K' => 'DPP',
            'L' => 'PPH',
            'M' => 'Total',
            'N' => 'DPP',
            'O' => 'PPH',
            'P' => 'Total',
        ];
        foreach ($subHeaders as $col => $label) {
            $sheet->setCellValue($col . ($currentRow + 1), $label);
        }

        $sheet->getStyle('A' . $currentRow . ':Q' . ($currentRow + 1))->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':Q' . ($currentRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $currentRow . ':Q' . ($currentRow + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $currentRow . ':Q' . ($currentRow + 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $currentRow += 2;

        // Group data berdasarkan barang
        $groupedByBarang = [];
        foreach ($dataOrder as $item) {
            $barangName = $item['barangName'];
            $groupedByBarang[$barangName][] = $item;
        }

        // Format angka untuk menampilkan 0.00
        $numberFormat = '#,##0.00;-#,##0.00;"0.00"';

        foreach ($groupedByBarang as $barangName => $items) {
            // Judul bahan
            $sheet->setCellValue('A' . $currentRow, 'Bahan Baku: ' . $barangName);
            $sheet->mergeCells('A' . $currentRow . ':Q' . $currentRow);
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

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
                'subsidi' => 0,
                'pphSubsidi' => 0,
                'totalSubsidi' => 0,
                'totalRow' => 0
            ];

            foreach ($items as $item) {
                // Pastikan semua nilai numerik diisi dengan 0 jika kosong
                $numericFields = [
                    'qtyPO',
                    'dppUmum',
                    'pphUmum',
                    'totalUmum',
                    'dppHarian',
                    'pphHarian',
                    'totalHarian',
                    'dppBulanan',
                    'pphBulanan',
                    'totalBulanan',
                    'subsidi',
                    'pphSubsidi',
                    'totalSubsidi',
                    'totalRow'
                ];

                foreach ($numericFields as $field) {
                    $item[$field] = isset($item[$field]) && is_numeric($item[$field])
                        ? (float)$item[$field]
                        : 0;
                }

                $sheet->setCellValue('A' . $currentRow, $no++);
                $sheet->setCellValue('B' . $currentRow, $item['supplierName'] ?? '');
                $sheet->setCellValue('C' . $currentRow, $item['satuanName'] ?? '');
                $sheet->setCellValue('D' . $currentRow, $item['qtyPO']);
                $sheet->setCellValue('E' . $currentRow, $item['dppUmum']);
                $sheet->setCellValue('F' . $currentRow, $item['pphUmum']);
                $sheet->setCellValue('G' . $currentRow, $item['totalUmum']);
                $sheet->setCellValue('H' . $currentRow, $item['dppHarian']);
                $sheet->setCellValue('I' . $currentRow, $item['pphHarian']);
                $sheet->setCellValue('J' . $currentRow, $item['totalHarian']);
                $sheet->setCellValue('K' . $currentRow, $item['dppBulanan']);
                $sheet->setCellValue('L' . $currentRow, $item['pphBulanan']);
                $sheet->setCellValue('M' . $currentRow, $item['totalBulanan']);
                $sheet->setCellValue('N' . $currentRow, $item['subsidi']);
                $sheet->setCellValue('O' . $currentRow, $item['pphSubsidi']);
                $sheet->setCellValue('P' . $currentRow, $item['totalSubsidi']);
                $sheet->setCellValue('Q' . $currentRow, $item['totalRow']);

                // Akumulasi total
                foreach ($totals as $key => &$val) {
                    $val += $item[$key];
                }

                $currentRow++;
            }

            // TOTAL untuk grup bahan
            $sheet->setCellValue('A' . $currentRow, 'TOTAL');
            $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
            $sheet->setCellValue('D' . $currentRow, $totals['qtyPO']);
            $sheet->setCellValue('E' . $currentRow, $totals['dppUmum']);
            $sheet->setCellValue('F' . $currentRow, $totals['pphUmum']);
            $sheet->setCellValue('G' . $currentRow, $totals['totalUmum']);
            $sheet->setCellValue('H' . $currentRow, $totals['dppHarian']);
            $sheet->setCellValue('I' . $currentRow, $totals['pphHarian']);
            $sheet->setCellValue('J' . $currentRow, $totals['totalHarian']);
            $sheet->setCellValue('K' . $currentRow, $totals['dppBulanan']);
            $sheet->setCellValue('L' . $currentRow, $totals['pphBulanan']);
            $sheet->setCellValue('M' . $currentRow, $totals['totalBulanan']);
            $sheet->setCellValue('N' . $currentRow, $totals['subsidi']);
            $sheet->setCellValue('O' . $currentRow, $totals['pphSubsidi']);
            $sheet->setCellValue('P' . $currentRow, $totals['totalSubsidi']);
            $sheet->setCellValue('Q' . $currentRow, $totals['totalRow']);

            $sheet->getStyle('A' . $currentRow . ':Q' . $currentRow)->getFont()->setBold(true);
            $currentRow += 2;
        }

        // Terapkan format angka ke semua kolom numerik
        $lastRow = $sheet->getHighestRow();
        $numericCols = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];

        foreach ($numericCols as $col) {
            $sheet->getStyle($col . '5:' . $col . $lastRow)
                ->getNumberFormat()
                ->setFormatCode($numberFormat);
        }

        // Tambahkan border untuk seluruh data
        // $sheet->getStyle('A5:Q' . $lastRow)->applyFromArray([
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //             'color' => ['argb' => 'FF000000'],
        //         ],
        //     ],
        // ]);

        // Auto-size kolom
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

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
        $totalTotalQty = 0;

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
                $totalTotalQty += $row->qtyPO;
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
            'totalQtyPO' => $totalTotalQty,
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
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "divisiId"     => $this->request->getGet("filter_divisi"),
            "barangId"     => $this->request->getGet("filter_barang"),
        ];

        $availableSort = [
            'barangName'   => 'barang_master.barang_name',
            'spekName'     => 'spekName',
            'bagianName'   => 'bagianName'
        ];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null);
        $groupedData = [];
        $usedSubsidiPo = [];
        $totalTotalRow = 0;

        $totalSummary = [
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

        foreach ($dataBBLokal['data'] as $row) {
            $poId = $row->poNum;
            $groupKey = $row->spekName . '_' . $row->divisiName;
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
            } elseif ($pphMode === "Supplier") {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = ($row->dppUmum * $nilai_pph2) * $qty;
                $totalUmum = $dppUmum - $pphUmum;
            } else {
                $dppUmum = $row->dppUmum * $qty;
                $pphUmum = 0;
                $totalUmum = $dppUmum;
            }

            // HARIAN
            if ($pphMode === "Company") {
                $dppHarian = ($row->dppHarian / $nilai_pph) * $qty;
                $pphHarian = ($row->dppHarian / $nilai_pph * $nilai_pph2) * $qty;
                $totalHarian = $dppHarian - $pphHarian;
            } elseif ($pphMode === "Supplier") {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = ($row->dppHarian * $nilai_pph2) * $qty;
                $totalHarian = $dppHarian - $pphHarian;
            } else {
                $dppHarian = $row->dppHarian * $qty;
                $pphHarian = 0;
                $totalHarian = $dppHarian;
            }

            // BULANAN
            if ($pphMode === "Company") {
                $dppBulanan = ($row->dppBulanan / $nilai_pph) * $qty;
                $pphBulanan = ($row->dppBulanan / $nilai_pph * $nilai_pph2) * $qty;
                $totalBulanan = $dppBulanan - $pphBulanan;
            } elseif ($pphMode === "Supplier") {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = ($row->dppBulanan * $nilai_pph2) * $qty;
                $totalBulanan = $dppBulanan - $pphBulanan;
            } else {
                $dppBulanan = $row->dppBulanan * $qty;
                $pphBulanan = 0;
                $totalBulanan = $dppBulanan;
            }

            $totalRow = $totalUmum + $totalHarian + $totalBulanan;

            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'no'             => 0,
                    'barangName'     => $row->barangName,
                    'divisiName'     => $row->divisiName,
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

            $g = &$groupedData[$groupKey];
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
            $g['totalRow'] += $totalRow;

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
            $totalSummary['totalRow'] += $totalRow;

            if (!in_array($poId, $usedSubsidiPo)) {
                $usedSubsidiPo[] = $poId;
                $dppSubsidi = ($pphMode === "Company") ? $row->subsidi / $nilai_pph : $row->subsidi;
                $pphSubsidi = ($pphMode === "Company" || $pphMode === "Supplier") ? ($dppSubsidi * $nilai_pph2) : 0;
                $totalSubsidi = ($pphMode === "Company" || $pphMode === "Supplier") ? ($dppSubsidi - $pphSubsidi) : ($dppSubsidi + $pphSubsidi);

                $g['subsidi'] += $dppSubsidi;
                $g['pphSubsidi'] += $pphSubsidi;
                $g['totalSubsidi'] += $totalSubsidi;
                $g['totalRow'] += $totalSubsidi;

                $totalSummary['subsidi'] += $dppSubsidi;
                $totalSummary['pphSubsidi'] += $pphSubsidi;
                $totalSummary['totalSubsidi'] += $totalSubsidi;
                $totalSummary['totalRow'] += $totalSubsidi;
            }
        }

        $groupedData = array_values($groupedData);
        $totalGrouped = count($groupedData);
        $paginatedData = array_slice($groupedData, ($currentPage - 1) * $pageSize, $pageSize);

        foreach ($paginatedData as $i => &$row) {
            $row['no'] = ($currentPage - 1) * $pageSize + $i + 1;
            foreach ($row as $key => $val) {
                if (is_numeric($val) && $key !== 'no') {
                    $row[$key] = number_format($val, 2, '.', ',');
                }
            }
        }

        $formattedSummary = [];
        foreach ($totalSummary as $key => $val) {
            $formattedSummary[$key] = number_format($val, 2, '.', ',');
        }

        echo json_encode([
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalGrouped,
            "recordsFiltered" => $totalGrouped,
            'data'            => $paginatedData,
            "payload"         => $payload,
            'totalTotalRow'   => number_format($totalSummary['totalRow'], 2, '.', ','),
            'totalSummary'    => $formattedSummary,
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
        ];

        $addCondition = [
            "dateStart"    => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"      => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "divisiId"     => $this->request->getGet("filter_divisi"),
            "barangId"     => $this->request->getGet("filter_barang"),
        ];

        $availableSort = [];

        $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplier($availableSort, $condition, $addCondition, null, null)['data'];
        $groupedByBarang = [];
        $usedSubsidiPo = [];

        foreach ($dataBBLokal as $row) {
            $pphMode = $row->poPPH;
            $hasNpwp = !empty($row->supplierNpwp);
            $nilai_pph = ($row->poDate <= '2025-06-30') ? ($hasNpwp ? (1.00 - 0.0025) : (1.00 - 0.005)) : 0.9975;
            $nilai_pph2 = ($row->poDate <= '2025-06-30') ? ($hasNpwp ? 0.0025 : 0.005) : 0.0025;

            $qty = $row->qtyPO;
            $barangName = $row->barangName;

            $calc = function ($dpp) use ($pphMode, $nilai_pph, $nilai_pph2, $qty) {
                if ($pphMode === "Company") {
                    $dppVal = ($dpp / $nilai_pph) * $qty;
                    $pph = ($dpp / $nilai_pph * $nilai_pph2) * $qty;
                    $total = $dppVal - $pph;
                } elseif ($pphMode === "Supplier") {
                    $dppVal = $dpp * $qty;
                    $pph = ($dpp * $nilai_pph2) * $qty;
                    $total = $dppVal - $pph;
                } else {
                    $dppVal = $dpp * $qty;
                    $pph = 0;
                    $total = $dppVal;
                }
                return [$dppVal, $pph, $total];
            };

            [$dppUmum, $pphUmum, $totalUmum] = $calc($row->dppUmum);
            [$dppHarian, $pphHarian, $totalHarian] = $calc($row->dppHarian);
            [$dppBulanan, $pphBulanan, $totalBulanan] = $calc($row->dppBulanan);

            $subsidi = 0;
            $pphSubsidi = 0;
            $totalSubsidi = 0;

            if (!in_array($row->poNum, $usedSubsidiPo)) {
                $usedSubsidiPo[] = $row->poNum;
                if ($pphMode === "Company") {
                    $subsidi = $row->subsidi / $nilai_pph;
                    $pphSubsidi = $subsidi * $nilai_pph2;
                    $totalSubsidi = $subsidi - $pphSubsidi;
                } elseif ($pphMode === "Supplier") {
                    $subsidi = $row->subsidi;
                    $pphSubsidi = $subsidi * $nilai_pph2;
                    $totalSubsidi = $subsidi - $pphSubsidi;
                } else {
                    $subsidi = $row->subsidi;
                    $pphSubsidi = 0;
                    $totalSubsidi = $subsidi;
                }
            }

            $totalRow = $totalUmum + $totalHarian + $totalBulanan + $totalSubsidi;

            $groupedByBarang[$barangName][] = (object)[
                'barangName' => $barangName,
                'spekName' => $row->spekName,
                'divisiName' => $row->divisiName,
                'qtyPO' => $qty,
                'satuanName' => $row->satuanName,
                'dppUmum' => $dppUmum,
                'pphUmum' => $pphUmum,
                'totalUmum' => $totalUmum,
                'dppHarian' => $dppHarian,
                'pphHarian' => $pphHarian,
                'totalHarian' => $totalHarian,
                'dppBulanan' => $dppBulanan,
                'pphBulanan' => $pphBulanan,
                'totalBulanan' => $totalBulanan,
                'subsidi' => $subsidi,
                'pphSubsidi' => $pphSubsidi,
                'totalSubsidi' => $totalSubsidi,
                'totalRow' => $totalRow,
            ];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul utama
        $sheet->setCellValue('A1', 'LAPORAN REKAP ALL BARANG (SUMMARY)');
        $sheet->mergeCells('A1:S1');
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
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

        foreach ($groupedByBarang as $barangName => $records) {
            // Header bahan baku
            $sheet->setCellValue("A{$rowIndex}", 'Bahan Baku: ' . $barangName);
            $sheet->mergeCells("A{$rowIndex}:S{$rowIndex}");
            $sheet->getStyle("A{$rowIndex}")->getFont()->setBold(true);
            $rowIndex++;

            $groupTotals = [
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

            foreach ($records as $record) {
                // Siapkan data untuk semua kolom
                $rowData = [
                    'A' => $no++,
                    'B' => $record->barangName,
                    'C' => $record->spekName,
                    'D' => $record->divisiName,
                    'E' => $record->qtyPO ?? 0,
                    'F' => $record->satuanName,
                    'G' => $record->dppUmum ?? 0,
                    'H' => $record->pphUmum ?? 0,
                    'I' => $record->totalUmum ?? 0,
                    'J' => $record->dppHarian ?? 0,
                    'K' => $record->pphHarian ?? 0,
                    'L' => $record->totalHarian ?? 0,
                    'M' => $record->dppBulanan ?? 0,
                    'N' => $record->pphBulanan ?? 0,
                    'O' => $record->totalBulanan ?? 0,
                    'P' => $record->subsidi ?? 0,
                    'Q' => $record->pphSubsidi ?? 0,
                    'R' => $record->totalSubsidi ?? 0,
                    'S' => $record->totalRow ?? 0,
                ];

                // Tulis data per kolom menggunakan setCellValueExplicit
                foreach ($rowData as $col => $value) {
                    $cellAddress = $col . $rowIndex;

                    if (in_array(ord($col) - 65, $numericColumns)) {
                        // Pastikan nilai numerik
                        $value = is_numeric($value) ? (float)$value : 0;
                        $sheet->setCellValueExplicit(
                            $cellAddress,
                            $value,
                            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                        );
                    } else {
                        $sheet->setCellValueExplicit(
                            $cellAddress,
                            $value,
                            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                        );
                    }
                }

                // Akumulasi total
                foreach ($groupTotals as $key => $val) {
                    $groupTotals[$key] += $record->$key;
                }

                $rowIndex++;
            }

            // Total per barangName
            $sheet->setCellValue("A{$rowIndex}", 'TOTAL');
            $sheet->mergeCells("A{$rowIndex}:F{$rowIndex}");

            // Data total
            $totalData = [
                'G' => $groupTotals['dppUmum'],
                'H' => $groupTotals['pphUmum'],
                'I' => $groupTotals['totalUmum'],
                'J' => $groupTotals['dppHarian'],
                'K' => $groupTotals['pphHarian'],
                'L' => $groupTotals['totalHarian'],
                'M' => $groupTotals['dppBulanan'],
                'N' => $groupTotals['pphBulanan'],
                'O' => $groupTotals['totalBulanan'],
                'P' => $groupTotals['subsidi'],
                'Q' => $groupTotals['pphSubsidi'],
                'R' => $groupTotals['totalSubsidi'],
                'S' => $groupTotals['totalRow']
            ];

            // Tulis data total menggunakan setCellValueExplicit
            foreach ($totalData as $col => $value) {
                $cellAddress = $col . $rowIndex;
                $value = is_numeric($value) ? (float)$value : 0;
                $sheet->setCellValueExplicit(
                    $cellAddress,
                    $value,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                );
            }

            $sheet->getStyle("A{$rowIndex}:S{$rowIndex}")->getFont()->setBold(true);

            // Baris kosong antar grup
            $rowIndex += 2;
        }

        // Format angka khusus untuk menampilkan 0.00
        $lastRow = $sheet->getHighestRow();
        $numberFormat = '#,##0.00;[Red]-#,##0.00;"0.00"';

        // Format kolom numerik
        $numericCols = ['E', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
        foreach ($numericCols as $col) {
            $sheet->getStyle("{$col}6:{$col}{$lastRow}")
                ->getNumberFormat()
                ->setFormatCode($numberFormat);
        }

        // Tambahkan border untuk seluruh data
        // $sheet->getStyle('A4:S' . $lastRow)->applyFromArray([
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => Border::BORDER_THIN,
        //             'color' => ['argb' => 'FF000000'],
        //         ],
        //     ],
        // ]);

        // Auto-size kolom
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'laporan_rekap_per_barang_' . date('Ymd_His') . '.xlsx';

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
                } elseif ($pphMode === "Supplier") {
                    $dppUmum = $row->dppUmum * $qty;
                    $pphUmum = ($row->dppUmum * $nilai_pph2) * $qty;
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
                } elseif ($pphMode === "Supplier") {
                    $dppHarian = $row->dppHarian * $qty;
                    $pphHarian = ($row->dppHarian * $nilai_pph2) * $qty;
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
                } elseif ($pphMode === "Supplier") {
                    $dppBulanan = $row->dppBulanan * $qty;
                    $pphBulanan = ($row->dppBulanan * $nilai_pph2) * $qty;
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
                } elseif ($pphMode === "Supplier") {
                    $dppSubsidi = $row->subsidi;
                    $pphSubsidi = ($row->subsidi * $nilai_pph2);
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
