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
            'filter_customer' => $this->request->getGet('filter') ?: null
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

        $condition = [
            'h.deletedAt'    => null,
            'h.tipe_invoice' => 'LOKAL'
        ];

        $addCondition = [
            'dateStart' => $tglAwal ? date('Y-m-d', strtotime(str_replace('/', '-', $tglAwal))) : null,
            'dateEnd'   => $tglAkhir ? date('Y-m-d', strtotime(str_replace('/', '-', $tglAkhir))) : null,
            'filter_customer' => $filter === 'all' ? null : $filter
        ];

        $dataDetail = $this->salesOrderInvoiceModel
            ->getCustomerItemDetail($condition, $addCondition);

        // =============================
        // HEADER BARANG (SORT BY BARANG)
        // =============================
        $dataHeader = [];
        foreach ($dataDetail as $row) {
            $dataHeader[$row->barang_id] = [
                'barang_id'   => $row->barang_id,
                'nama_barang' => $row->nama_barang
            ];
        }
        $dataHeader = array_values($dataHeader);

        usort($dataHeader, fn($a, $b) =>
            strcmp($a['nama_barang'], $b['nama_barang'])
        );

        // =============================
        // PIVOT CUSTOMER
        // =============================
        $pivotCustomer = [];

        foreach ($dataDetail as $row) {
            $cId = $row->customer_id;
            $bId = $row->barang_id;
            $amount = (float)$row->amount;

            if (!isset($pivotCustomer[$cId])) {
                $pivotCustomer[$cId] = [
                    'customer_name' => $row->customer_name,
                    'items' => [],
                    'total' => 0
                ];
            }

            $pivotCustomer[$cId]['items'][$bId] =
                ($pivotCustomer[$cId]['items'][$bId] ?? 0) + $amount;
        }

        foreach ($pivotCustomer as &$c) {
            $c['total'] = array_sum($c['items']);
        }
        unset($c);

        // SORT CUSTOMER (A–Z)
        usort($pivotCustomer, fn($a, $b) =>
            strcmp($a['customer_name'], $b['customer_name'])
        );

        // =============================
        // FOOTER TOTAL
        // =============================
        $footerTotal = [];
        $footerGrand = 0;

        foreach ($pivotCustomer as $customer) {
            foreach ($dataHeader as $h) {
                $bId = $h['barang_id'];
                $amount = $customer['items'][$bId] ?? 0;

                $footerTotal[$bId] = ($footerTotal[$bId] ?? 0) + $amount;
                $footerGrand += $amount;
            }
        }

        $data = [
            'header' => $dataHeader,
            'rows'   => $pivotCustomer,
            'footer' => [
                'per_barang' => $footerTotal,
                'grand_total' => $footerGrand
            ],
            'dateStart' => $tglAwal ?: 'All',
            'dateEnd'   => $tglAkhir ?: 'All'
        ];

        $dompdf->loadHtml(
            view('Laporan/LaporanSales/LaporanItemsSalesByCustomers/print', $data)
        );

        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();
        $dompdf->stream(
            "Laporan_Customers_Sales_By_Items" . date('Ymd_His') . ".pdf",
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
            'dateStart' => $tglAwal
                ? date('Y-m-d', strtotime(str_replace('/', '-', $tglAwal)))
                : null,
            'dateEnd' => $tglAkhir
                ? date('Y-m-d', strtotime(str_replace('/', '-', $tglAkhir)))
                : null,
            'filter_customer' => $filter === 'all' ? null : $filter
        ];

        // =============================
        // AMBIL DATA DETAIL
        // =============================
        $dataDetail = $this->salesOrderInvoiceModel
            ->getCustomerItemDetail($condition, $addCondition);

        // =============================
        // HEADER BARANG (UNIK + SORT A–Z)
        // =============================
        $headerBarang = [];
        foreach ($dataDetail as $row) {
            $headerBarang[$row->barang_id] = [
                'id'   => $row->barang_id,
                'nama' => $row->nama_barang
            ];
        }
        $headerBarang = array_values($headerBarang);

        usort($headerBarang, fn($a, $b) =>
            strcmp($a['nama'], $b['nama'])
        );

        // =============================
        // PIVOT CUSTOMER
        // =============================
        $pivot = [];

        foreach ($dataDetail as $row) {
            $cId = $row->customer_id;
            $bId = $row->barang_id;
            $amount = (float)$row->amount;

            if (!isset($pivot[$cId])) {
                $pivot[$cId] = [
                    'customer' => $row->customer_name,
                    'items' => [],
                    'total' => 0
                ];
            }

            $pivot[$cId]['items'][$bId] =
                ($pivot[$cId]['items'][$bId] ?? 0) + $amount;
        }

        foreach ($pivot as &$p) {
            $p['total'] = array_sum($p['items']);
        }
        unset($p);

        // SORT CUSTOMER A–Z
        usort($pivot, fn($a, $b) =>
            strcmp($a['customer'], $b['customer'])
        );

        // =============================
        // FOOTER TOTAL
        // =============================
        $footerBarang = [];
        $grandTotal = 0;

        foreach ($pivot as $p) {
            foreach ($headerBarang as $h) {
                $val = $p['items'][$h['id']] ?? 0;
                $footerBarang[$h['id']] = ($footerBarang[$h['id']] ?? 0) + $val;
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
        $sheet->setCellValue('A2', 'CUSTOMERS SALES BY ITEMS');
        $sheet->setCellValue(
            'A3',
            'Periode: ' .
            ($tglAwal ? date('d/m/Y', strtotime($tglAwal)) : 'All') .
            ' - ' .
            ($tglAkhir ? date('d/m/Y', strtotime($tglAkhir)) : 'All')
        );

        $lastCol = chr(65 + count($headerBarang) + 1);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        $sheet->getStyle("A1:A3")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:A3")->getFont()->setBold(true);

        // =============================
        // HEADER TABLE
        // =============================
        $rowHeader = 5;
        $col = 'A';

        $sheet->setCellValue($col.$rowHeader, 'Customer');
        $col++;

        foreach ($headerBarang as $h) {
            $sheet->setCellValue($col.$rowHeader, $h['nama']);
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
            $sheet->setCellValue($col.$rowExcel, $p['customer']);
            $col++;

            foreach ($headerBarang as $h) {
                $sheet->setCellValueExplicit(
                    $col.$rowExcel,
                    $p['items'][$h['id']] ?? 0,
                    DataType::TYPE_NUMERIC
                );

                $sheet->getStyle($col.$rowExcel)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $col++;
            }

            $sheet->setCellValueExplicit(
                $col.$rowExcel,
                $p['total'],
                DataType::TYPE_NUMERIC
            );

            $sheet->getStyle($col.$rowExcel)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $rowExcel++;
        }

        // =============================
        // FOOTER TOTAL
        // =============================
        $col = 'A';
        $sheet->setCellValue($col.$rowExcel, 'TOTAL');
        $col++;

        foreach ($headerBarang as $h) {
            $sheet->setCellValueExplicit(
                $col.$rowExcel,
                $footerBarang[$h['id']] ?? 0,
                DataType::TYPE_NUMERIC
            );

            $sheet->getStyle($col.$rowExcel)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $col++;
        }

        $sheet->setCellValueExplicit(
            $col.$rowExcel,
            $grandTotal,
            DataType::TYPE_NUMERIC
        );

        $sheet->getStyle($col.$rowExcel)
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle("A{$rowExcel}:{$col}{$rowExcel}")
            ->getFont()->setBold(true);

        // =============================
        // AUTO WIDTH
        // =============================
        foreach (range('A', $col) as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // =============================
        // OUTPUT
        // =============================
        $filename = "Customers_Sales_By_Items_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
