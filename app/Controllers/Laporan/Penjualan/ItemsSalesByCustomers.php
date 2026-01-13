<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ItemsSalesByCustomers extends BaseController
{
    protected $this_company_id;
    protected $barangMasterSalesModel;
    protected $salesOrderInvoiceModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
    }

    public function index()
    {
        $barangData = $this->barangMasterSalesModel->asObject()->where([
            'deletedAt' => null
        ])->orderBy('barang_name', 'ASC')->findAll();
        $data = [
            'barang' => $barangData
        ];
        return view('Laporan/LaporanSales/LaporanItemsSalesByCustomers/index', $data);
    }

    public function allTransaksi()
    {
        $headerOnly = $this->request->getGet('headerOnly');

        // =============================
        // BASE CONDITION
        // =============================
        $condition = [
            'h.deletedAt'    => null,
            'h.tipe_invoice' => 'LOKAL'
        ];

        $addCondition = [
            'dateStart' => $this->request->getGet('dateStart')
                ? date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getGet('dateStart'))))
                : null,
            'dateEnd' => $this->request->getGet('dateEnd')
                ? date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getGet('dateEnd'))))
                : null,
            'filter_barang' => $this->request->getGet('filter') ?: null
        ];

        // =============================
        // 1️⃣ HEADER ONLY (CUSTOMER)
        // =============================
        if ($headerOnly) {

            $headerData = $this->salesOrderInvoiceModel
                ->getPivotHeaderCustomer($condition, $addCondition);

            $footerCustomer = [];
            $grandTotal = 0;

            foreach ($headerData as $h) {
                $footerCustomer[$h->customer_id] = (float)$h->amount;
                $grandTotal += (float)$h->amount;
            }

            return $this->response->setJSON([
                'header' => array_map(fn($h) => [
                    'customer_id'   => $h->customer_id,
                    'customer_name' => $h->customer_name
                ], $headerData),
                'footer' => [
                    'per_customer' => $footerCustomer,
                    'grand_total'  => $grandTotal
                ]
            ]);
        }

        // =============================
        // 2️⃣ DATA TABLE (ROW = BARANG)
        // =============================
        $length = (int)($this->request->getGet('length') ?? 25);
        $start  = (int)($this->request->getGet('start') ?? 0);
        $draw   = (int)($this->request->getGet('draw') ?? 1);

        $dataDetail = $this->salesOrderInvoiceModel
            ->getPivotBarangData($condition, $addCondition);

        // =============================
        // HEADER CUSTOMER (URUTAN KONSISTEN)
        // =============================
        $dataHeader = [];
        foreach ($dataDetail as $row) {
            $dataHeader[$row->customer_id] = $row->customer_id;
        }
        $dataHeader = array_values($dataHeader);

        // =============================
        // PIVOT BARANG
        // =============================
        $pivot = [];

        foreach ($dataDetail as $row) {
            $bId = $row->barang_id;
            $cId = $row->customer_id;

            if (!isset($pivot[$bId])) {
                $pivot[$bId] = [
                    'nama_barang' => $row->nama_barang,
                    'customers'   => [],
                    'total'       => 0
                ];
            }

            $pivot[$bId]['customers'][$cId] =
                ($pivot[$bId]['customers'][$cId] ?? 0) + (float)$row->amount;
        }

        foreach ($pivot as &$p) {
            $p['total'] = array_sum($p['customers']);
        }
        unset($p);

        // =============================
        // PAGINATION (SERVER SIDE)
        // =============================
        $recordsTotal = count($pivot);
        $paged = array_slice($pivot, $start, $length, true);

        // =============================
        // BUILD DATATABLE RESPONSE
        // =============================
        $data = [];

        foreach ($paged as $row) {

            $item = [
                'nama_barang' => $row['nama_barang'],
                'total'       => $row['total']
            ];

            foreach ($dataHeader as $cId) {
                $item[$cId] = $row['customers'][$cId] ?? 0;
            }

            $data[] = $item;
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data'            => $data
        ]);
    }

    public function LaporanPenjualanPrint($tglAwal, $tglAkhir, $filter)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $dompdf = new \Dompdf\Dompdf();

        // =============================
        // BASE CONDITION
        // =============================
        $condition = [
            'h.deletedAt'    => null,
            'h.tipe_invoice' => 'LOKAL'
        ];

        $addCondition = [
            'dateStart' => $tglAwal ? date('Y-m-d', strtotime(str_replace('/', '-', $tglAwal))) : null,
            'dateEnd'   => $tglAkhir ? date('Y-m-d', strtotime(str_replace('/', '-', $tglAkhir))) : null,
            'filter_barang' => $filter === 'all' ? null : $filter
        ];

        // =============================
        // AMBIL DATA DETAIL PERSIS DATATABLE
        // =============================
        $dataHeaders = $this->salesOrderInvoiceModel
            ->getPivotHeaderCustomer($condition, $addCondition);

        $dataDetail = $this->salesOrderInvoiceModel
            ->getPivotBarangData($condition, $addCondition);

        // =============================
        // HEADER CUSTOMER (kolom)
        // =============================
        $dataHeader = [];
        $customerNames = [];
        foreach ($dataHeaders as $row) {
            $dataHeader[$row->customer_id] = $row->customer_id;
            $customerNames[$row->customer_id] = $row->customer_name;
        }
        $dataHeader = array_values($dataHeader);
        $customerNames = array_unique($customerNames);

        // =============================
        // PIVOT BARANG (baris = barang)
        // =============================
        $pivotBarang = [];
        foreach ($dataDetail as $row) {
            $bId = $row->barang_id;
            $cId = $row->customer_id;

            if (!isset($pivotBarang[$bId])) {
                $pivotBarang[$bId] = [
                    'nama_barang' => $row->nama_barang,
                    'customers' => [],
                    'total' => 0
                ];
            }

            $pivotBarang[$bId]['customers'][$cId] =
                ($pivotBarang[$bId]['customers'][$cId] ?? 0) + (float)$row->amount;
        }

        foreach ($pivotBarang as &$p) {
            $p['total'] = array_sum($p['customers']);
        }
        unset($p);

        // =============================
        // FOOTER TOTAL PER CUSTOMER
        // =============================
        $footerTotal = [];
        $grandTotal = 0;

        foreach ($pivotBarang as $p) {
            foreach ($dataHeader as $cId) {
                $val = $p['customers'][$cId] ?? 0;
                $footerTotal[$cId] = ($footerTotal[$cId] ?? 0) + $val;
                $grandTotal += $val;
            }
        }

        $data = [
            'header'    => $dataHeader,
            'rows'      => $pivotBarang,
            'footer'    => [
                'per_customer' => $footerTotal,
                'grand_total'  => $grandTotal
            ],
            'customerNames' => $customerNames,
            'dateStart' => $tglAwal ?: 'All',
            'dateEnd'   => $tglAkhir ?: 'All'
        ];

        $dompdf->loadHtml(
            view('Laporan/LaporanSales/LaporanItemsSalesByCustomers/print', $data)
        );

        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();
        $dompdf->stream(
            "Items_Sales_By_Customers_" . date('Ymd_His') . ".pdf",
            ["Attachment" => false]
        );
        exit;
    }

    public function exportExcel($tglAwal, $tglAkhir, $filter)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        // =============================
        // BASE CONDITION
        // =============================
        $condition = [
            'h.deletedAt'    => null,
            'h.tipe_invoice' => 'LOKAL'
        ];

        $addCondition = [
            'dateStart' => $tglAwal ? date('Y-m-d', strtotime(str_replace('/', '-', $tglAwal))) : null,
            'dateEnd'   => $tglAkhir ? date('Y-m-d', strtotime(str_replace('/', '-', $tglAkhir))) : null,
            'filter_barang' => $filter === 'all' ? null : $filter
        ];

        // =============================
        // HEADER CUSTOMER
        // =============================
        $dataHeaders = $this->salesOrderInvoiceModel
            ->getPivotHeaderCustomer($condition, $addCondition);

        $customerIds = [];
        $customerNames = [];
        foreach ($dataHeaders as $h) {
            $customerIds[] = $h->customer_id;
            $customerNames[$h->customer_id] = $h->customer_name;
        }

        // =============================
        // DATA DETAIL (pivot barang)
        // =============================
        $dataDetail = $this->salesOrderInvoiceModel
            ->getPivotBarangData($condition, $addCondition);

        $pivot = [];
        $barangList = [];

        foreach ($dataDetail as $row) {
            $bId = $row->barang_id;
            $cId = $row->customer_id;

            // Simpan list barang unik
            if (!isset($barangList[$bId])) {
                $barangList[$bId] = $row->nama_barang;
            }

            if (!isset($pivot[$bId])) {
                $pivot[$bId] = [
                    'nama_barang' => $row->nama_barang,
                    'customers' => [],
                    'total' => 0
                ];
            }

            $pivot[$bId]['customers'][$cId] =
                ($pivot[$bId]['customers'][$cId] ?? 0) + (float)$row->amount;
        }

        // Hitung total per barang
        foreach ($pivot as &$p) {
            $p['total'] = array_sum($p['customers']);
        }
        unset($p);

        // =============================
        // FOOTER TOTAL PER CUSTOMER
        // =============================
        $footerTotal = [];
        $grandTotal = 0;

        foreach ($pivot as $p) {
            foreach ($customerIds as $cId) {
                $val = $p['customers'][$cId] ?? 0;
                $footerTotal[$cId] = ($footerTotal[$cId] ?? 0) + $val;
                $grandTotal += $val;
            }
        }

        // =============================
        // BUAT EXCEL
        // =============================
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // =============================
        // JUDUL
        // =============================
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->setCellValue('A2', 'ITEMS SALES BY CUSTOMERS');
        $sheet->setCellValue('A3', 'Periode: ' . 
            ($tglAwal ? date('d/m/Y', strtotime($tglAwal)) : 'All') . 
            ' - ' . 
            ($tglAkhir ? date('d/m/Y', strtotime($tglAkhir)) : 'All')
        );

        $lastCol = chr(65 + count($customerIds) + 1);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        $sheet->getStyle("A1:A3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:A3")->getFont()->setBold(true);

        // =============================
        // HEADER TABLE
        // =============================
        $rowHeader = 5;
        $col = 'A';

        $sheet->setCellValue($col.$rowHeader, 'Barang');
        $col++;

        foreach ($customerIds as $cId) {
            $sheet->setCellValue($col.$rowHeader, $customerNames[$cId]);
            $col++;
        }

        $sheet->setCellValue($col.$rowHeader, 'Total');

        $sheet->getStyle("A{$rowHeader}:{$col}{$rowHeader}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9D9D9']
            ]
        ]);

        // =============================
        // ISI DATA
        // =============================
        $rowExcel = $rowHeader + 1;

        foreach ($pivot as $p) {
            $col = 'A';
            $sheet->setCellValue($col.$rowExcel, $p['nama_barang']);
            $col++;

            foreach ($customerIds as $cId) {
                $sheet->setCellValueExplicit(
                    $col.$rowExcel,
                    $p['customers'][$cId] ?? 0,
                    DataType::TYPE_NUMERIC
                );

                $sheet->getStyle($col.$rowExcel)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                $col++;
            }

            $sheet->setCellValueExplicit(
                $col.$rowExcel,
                $p['total'],
                DataType::TYPE_NUMERIC
            );
            $sheet->getStyle($col.$rowExcel)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

            $rowExcel++;
        }

        // =============================
        // FOOTER TOTAL
        // =============================
        $col = 'A';
        $sheet->setCellValue($col.$rowExcel, 'TOTAL');
        $col++;

        foreach ($customerIds as $cId) {
            $sheet->setCellValueExplicit(
                $col.$rowExcel,
                $footerTotal[$cId] ?? 0,
                DataType::TYPE_NUMERIC
            );
            $sheet->getStyle($col.$rowExcel)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $col++;
        }

        $sheet->setCellValueExplicit(
            $col.$rowExcel,
            $grandTotal,
            DataType::TYPE_NUMERIC
        );
        $sheet->getStyle($col.$rowExcel)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle("A{$rowExcel}:{$col}{$rowExcel}")->getFont()->setBold(true);

        // =============================
        // AUTO WIDTH
        // =============================
        foreach (range('A', $col) as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // =============================
        // OUTPUT
        // =============================
        $filename = "Items_Sales_By_Customers_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
