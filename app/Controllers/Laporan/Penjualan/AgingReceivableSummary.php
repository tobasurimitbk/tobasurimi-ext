<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AgingReceivableSummary extends BaseController
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
        return view('Laporan/LaporanSales/LaporanAgingReceivableSummary/index', $data);
    }

    public function allTransaksi()
    {
        $limit  = $this->request->getGet("length") ?? 25;
        $offset = $this->request->getGet("start") ?? 0;

        $condition = [
            'sales_order_invoice.deletedAt' => null,
            'sales_order_invoice.tipe_invoice' => 'LOKAL'
        ];

        $addCondition = [
            'filter_customer' => $this->request->getGet("filter"),
            'dateStart' => $this->request->getGet("dateStart")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart"))))
                : null,
            'dateEnd' => $this->request->getGet("dateEnd")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd"))))
                : null,
        ];

        $result = $this->salesOrderInvoiceModel
            ->getAgingReceivableSummary($condition, $addCondition, $limit, $offset);

        $data = [];
        foreach ($result['data'] as $row) {
            $data[] = [
                'customer_id' => $row->customer_id,
                'customer_name' => $row->customer_name,
                'total_invoice' => number_format($row->total_invoice),
                'not_yet'       => number_format($row->not_yet),
                '1_30'          => number_format($row->aging_1_30),
                '31_60'         => number_format($row->aging_31_60),
                '61_90'         => number_format($row->aging_61_90),
                '91_120'        => number_format($row->aging_91_120),
                'over_120'      => number_format($row->aging_over_120),
            ];
        }

        echo json_encode([
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $result['totalData'],
            "recordsFiltered" => $result['totalFilteredData'],
            "data" => $data
        ]);
    }

    public function detailAgingInvoice()
    {
        $customerId = $this->request->getGet('customer_id');
        $agingType  = $this->request->getGet('aging');

        $data = $this->salesOrderInvoiceModel
            ->getInvoiceByAging($customerId, $agingType);

        echo json_encode(['data' => $data]);
    }

    public function LaporanPenjualanPrint($tglAwal, $tglAkhir, $filter, $search)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $dompdf = new \Dompdf\Dompdf();

        $condition = [
            'sales_order_invoice.deletedAt' => null,
            'sales_order_invoice.tipe_invoice' => 'LOKAL'
        ];

        $addCondition = [
            'filter_customer' => $filter === 'all' ? null : $filter,
            'dateStart' => $tglAwal !== 'all'
                ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal)))
                : null,
            'dateEnd' => $tglAkhir !== 'now'
                ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir)))
                : null,
        ];

        // ⚠️ LIMIT & OFFSET NULL → ambil semua data (sesuai print)
        $result = $this->salesOrderInvoiceModel
            ->getAgingReceivableSummary($condition, $addCondition, null, null);

        $dataPrint = [];
        foreach ($result['data'] as $row) {
            $dataPrint[] = [
                'customer_name' => $row->customer_name,
                'total_invoice' => number_format($row->total_invoice),
                'not_yet'       => number_format($row->not_yet),
                'aging_1_30'    => number_format($row->aging_1_30),
                'aging_31_60'   => number_format($row->aging_31_60),
                'aging_61_90'   => number_format($row->aging_61_90),
                'aging_91_120'  => number_format($row->aging_91_120),
                'over_120'      => number_format($row->aging_over_120),
            ];
        }

        $data = [
            'data' => $dataPrint,
            'dateStart' => $tglAwal !== 'all' ? date('d/m/Y', strtotime($tglAwal)) : 'All',
            'dateEnd' => $tglAkhir !== 'now' ? date('d/m/Y', strtotime($tglAkhir)) : 'Now',
        ];

        $dompdf->loadHtml(view(
            'Laporan/LaporanSales/LaporanAgingReceivableSummary/print',
            $data
        ));

        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream(
            'Laporan_Aging_Receivable_Summary',
            ['Attachment' => false]
        );

        exit;
    }

    public function exportExcel($tglAwal, $tglAkhir, $filter, $search)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $condition = [
            'sales_order_invoice.deletedAt' => null,
            'sales_order_invoice.tipe_invoice' => 'LOKAL'
        ];

        $addCondition = [
            'filter_customer' => $filter === 'all' ? null : $filter,
            'dateStart' => $tglAwal !== 'all'
                ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal)))
                : null,
            'dateEnd' => $tglAkhir !== 'now'
                ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir)))
                : null,
        ];

        // Ambil SEMUA data (tanpa pagination)
        $result = $this->salesOrderInvoiceModel
            ->getAgingReceivableSummary($condition, $addCondition, null, null);

        // =========================
        // CREATE EXCEL
        // =========================
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Document properties
        $spreadsheet->getProperties()
            ->setCreator("System")
            ->setTitle("Aging Receivable Summary")
            ->setSubject("Aging Receivable Summary")
            ->setDescription("Laporan Aging Receivable Summary");

        // =========================
        // HEADER
        // =========================
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->setCellValue('A2', 'AGING RECEIVABLE SUMMARY');
        $sheet->setCellValue(
            'A3',
            'Hingga: ' .
            ($tglAkhir !== 'now' ? date('d/m/Y', strtotime($tglAkhir)) : 'Now')
        );

        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $sheet->getStyle('A1:H3')->getFont()->setBold(true);
        $sheet->getStyle('A1:H3')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // =========================
        // COLUMN HEADER
        // =========================
        $sheet->setCellValue('A5', 'Customer Name');
        $sheet->setCellValue('B5', 'Total Invoice');
        $sheet->setCellValue('C5', 'Not Yet');
        $sheet->setCellValue('D5', '1 - 30');
        $sheet->setCellValue('E5', '31 - 60');
        $sheet->setCellValue('F5', '61 - 90');
        $sheet->setCellValue('G5', '91 - 120');
        $sheet->setCellValue('H5', '> 120');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9D9D9']
            ]
        ];
        $sheet->getStyle('A5:H5')->applyFromArray($headerStyle);

        // =========================
        // DATA
        // =========================
        $rowExcel = 6;
        foreach ($result['data'] as $row) {
            $sheet->setCellValue('A' . $rowExcel, $row->customer_name);
            $sheet->setCellValueExplicit('B' . $rowExcel, (float)$row->total_invoice, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit('C' . $rowExcel, (float)$row->not_yet, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit('D' . $rowExcel, (float)$row->aging_1_30, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit('E' . $rowExcel, (float)$row->aging_31_60, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit('F' . $rowExcel, (float)$row->aging_61_90, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit('G' . $rowExcel, (float)$row->aging_91_120, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit('H' . $rowExcel, (float)$row->aging_over_120, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

            $rowExcel++;
        }

        $lastRow = $rowExcel - 1;

        $sheet->getStyle('B6:H' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');


        // =========================
        // FORMAT
        // =========================
        $sheet->getStyle('B6:H' . $lastRow)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle('A5:H' . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column width
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);

        // =========================
        // DOWNLOAD
        // =========================
        $filename = 'Aging_Receivable_Summary_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
