<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class CustomersSalesByItems extends BaseController
{
    protected $this_company_id;
    protected $customerModel;
    protected $salesOrderInvoiceModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->customerModel = new CustomerModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
    }

    public function index()
    {
        $customerData = $this->customerModel->asObject()->where([
            'deletedAt' => null,
            'tipe_customer' => 'LOKAL'
        ])->orderBy('name', 'ASC')->findAll();
        $data = [
            'customer' => $customerData
        ];
        return view('Laporan/LaporanSales/LaporanCustomersSalesByItems/index', $data);
    }

    public function allTransaksi()
    {
        $headerOnly = $this->request->getGet('headerOnly');

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
        // HEADER ONLY (AJAX PERTAMA)
        // =============================
        if ($headerOnly) {

            $headerData = $this->salesOrderInvoiceModel
                ->getPivotHeader($condition, $addCondition);

            $footerBarang = [];
            $grandTotal = 0;

            foreach ($headerData as $h) {
                $footerBarang[$h->barang_id] = (float)$h->amount;
                $grandTotal += (float)$h->amount;
            }

            return $this->response->setJSON([
                'header' => array_map(fn($h) => [
                    'barang_id' => $h->barang_id,
                    'nama_barang' => $h->nama_barang
                ], $headerData),
                'footer' => [
                    'per_barang' => $footerBarang,
                    'grand_total' => $grandTotal
                ]
            ]);
        }

        // =============================
        // DATA TABLE (AJAX KEDUA)
        // =============================
        $length = (int)($this->request->getGet('length') ?? 25);
        $start  = (int)($this->request->getGet('start') ?? 0);
        $draw   = (int)($this->request->getGet('draw') ?? 1);

        $dataDetail = $this->salesOrderInvoiceModel
            ->getPivotCustomerData($condition, $addCondition);

        // Header barang (urutan sama dengan header ajax)
        $dataHeader = [];
        foreach ($dataDetail as $row) {
            $dataHeader[$row->barang_id] = $row->barang_id;
        }
        $dataHeader = array_values($dataHeader);

        $pivot = [];
        foreach ($dataDetail as $row) {
            $pivot[$row->customer_id]['customer_name'] = $row->customer_name;
            $pivot[$row->customer_id]['items'][$row->barang_id] =
                ($pivot[$row->customer_id]['items'][$row->barang_id] ?? 0) + (float)$row->amount;
        }

        foreach ($pivot as &$p) {
            $p['total'] = array_sum($p['items']);
        }
        unset($p);

        $recordsTotal = count($pivot);
        $paged = array_slice($pivot, $start, $length, true);

        $data = [];
        foreach ($paged as $row) {
            $item = [
                'customer_name' => $row['customer_name'],
                'total' => $row['total']
            ];

            foreach ($dataHeader as $bId) {
                $item[$bId] = $row['items'][$bId] ?? 0;
            }

            $data[] = $item;
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
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

        // Ambil data header & detail
        $dataHeader = $this->salesOrderInvoiceModel->getPivotHeader($condition, $addCondition);
        $dataDetail = $this->salesOrderInvoiceModel->getCustomerItemDetail($condition, $addCondition);

        // =============================
        // HEADER BARANG (sesuai DataTable)
        // =============================
        $header = [];
        foreach ($dataHeader as $h) {
            $header[$h->barang_id] = [
                'barang_id' => $h->barang_id,
                'nama_barang' => $h->nama_barang
            ];
        }
        $header = array_values($header); // agar urutannya sama seperti DataTable

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
        usort($pivotCustomer, fn($a, $b) => strcmp($a['customer_name'], $b['customer_name']));

        // =============================
        // FOOTER TOTAL
        // =============================
        $footerTotal = [];
        $footerGrand = 0;

        foreach ($pivotCustomer as $customer) {
            foreach ($header as $h) {
                $bId = $h['barang_id'];
                $amount = $customer['items'][$bId] ?? 0;

                $footerTotal[$bId] = ($footerTotal[$bId] ?? 0) + $amount;
                $footerGrand += $amount;
            }
        }

        $data = [
            'header' => $header,
            'rows'   => $pivotCustomer,
            'footer' => [
                'per_barang' => $footerTotal,
                'grand_total' => $footerGrand
            ],
            'dateStart' => $tglAwal ?: 'All',
            'dateEnd'   => $tglAkhir ?: 'All'
        ];

        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanCustomersSalesByItems/print', $data));
        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();
        $dompdf->stream(
            "Laporan_Customers_Sales_By_Items_" . date('Ymd_His') . ".pdf",
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
        // AMBIL HEADER & DATA DETAIL
        // =============================
        $dataHeader = $this->salesOrderInvoiceModel->getPivotHeader($condition, $addCondition);
        $dataDetail = $this->salesOrderInvoiceModel->getCustomerItemDetail($condition, $addCondition);

        // =============================
        // HEADER BARANG (URUTAN SAMA SEPERTI DATATABLE)
        // =============================
        $headerBarang = [];
        foreach ($dataHeader as $h) {
            $headerBarang[$h->barang_id] = [
                'id'   => $h->barang_id,
                'nama' => $h->nama_barang
            ];
        }
        $headerBarang = array_values($headerBarang);

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
        usort($pivot, fn($a, $b) => strcmp($a['customer'], $b['customer']));

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
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
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

        $lastColIndex = count($headerBarang) + 2; // +1 Customer, +1 Total
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        $sheet->getStyle("A1:A3")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:A3")->getFont()->setBold(true);

        // =============================
        // HEADER TABLE
        // =============================
        $rowHeader = 5;
        $colIndex = 1;

        $sheet->setCellValueByColumnAndRow($colIndex++, $rowHeader, 'Customer');

        foreach ($headerBarang as $h) {
            $sheet->setCellValueByColumnAndRow($colIndex++, $rowHeader, $h['nama']);
        }

        $sheet->setCellValueByColumnAndRow($colIndex++, $rowHeader, 'Total');

        $sheet->getStyle("A{$rowHeader}:{$lastCol}{$rowHeader}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9D9D9']
            ]
        ]);

        // =============================
        // ISI DATA
        // =============================
        $rowExcel = $rowHeader + 1;

        foreach ($pivot as $p) {
            $colIndex = 1;
            $sheet->setCellValueByColumnAndRow($colIndex++, $rowExcel, $p['customer']);

            foreach ($headerBarang as $h) {
                $sheet->setCellValueByColumnAndRow($colIndex, $rowExcel, $p['items'][$h['id']] ?? 0);
                $sheet->getStyleByColumnAndRow($colIndex, $rowExcel)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00'); // <-- nominal numeric dengan ribuan + 2 desimal
                $sheet->getStyleByColumnAndRow($colIndex, $rowExcel)
                    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $colIndex++;
            }

            $sheet->setCellValueByColumnAndRow($colIndex, $rowExcel, $p['total']);
            $sheet->getStyleByColumnAndRow($colIndex, $rowExcel)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $sheet->getStyleByColumnAndRow($colIndex, $rowExcel)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $rowExcel++;
        }

        $lastDataRow = $rowExcel;

        // =============================
        // FOOTER TOTAL
        $colIndex = 1;
        $sheet->setCellValueByColumnAndRow($colIndex++, $rowExcel, 'TOTAL');

        foreach ($headerBarang as $h) {
            $sheet->setCellValueByColumnAndRow($colIndex, $rowExcel, $footerBarang[$h['id']] ?? 0);
            $sheet->getStyleByColumnAndRow($colIndex, $rowExcel)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $sheet->getStyleByColumnAndRow($colIndex, $rowExcel)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $colIndex++;
        }

        $sheet->setCellValueByColumnAndRow($colIndex, $rowExcel, $grandTotal);
        $sheet->getStyleByColumnAndRow($colIndex, $rowExcel)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $sheet->getStyleByColumnAndRow($colIndex, $rowExcel)
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("A{$rowExcel}:{$lastCol}{$rowExcel}")->getFont()->setBold(true);

        // =============================
        // AUTO WIDTH
        for ($i = 1; $i <= $colIndex; $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))
                ->setAutoSize(true);
        }

        // =============================
        // OUTPUT
        // =============================
        $filename = "Customers_Sales_By_Items_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
