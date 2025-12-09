<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenjualanPerPelanggan extends BaseController
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
        $customerData = $this->customerModel->asObject()->findAll();
        $data = [
            'customer' => $customerData
        ];
        return view('Laporan/LaporanSales/LaporanPerPelanggan/index', $data);
    }

    public function allTransaksi()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize" => $pageSize,
            "currentPage" => $currentPage,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen") ?? null,
            "filter_customer" => $this->request->getGet("filter"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getLaporanPenjualanPerPelanggan($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderInvoice = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $namaPenjual = "";
            if ($data->jenis_penjualan == 1) {
                $namaPenjual = $data->salesName;
            } elseif ($data->jenis_penjualan == 2) {
                $namaPenjual = "E-Commerce";
            } else {
                $namaPenjual = "Office";
            }
            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "total_invoice" => number_format(floatval($data->sum_amount_invoice)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "kode_pelanggan" => $data->kode_pelanggan,
                "nama_penjual" => $namaPenjual,
                "count_invoice" => $data->count_invoice,
            ]);
        }

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $dataSalesOrderInvoice['totalData'],
            "recordsFiltered" => $dataSalesOrderInvoice['totalFilteredData'],
            "data" => $dataAllSalesOrderInvoice,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function printPDF($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen") ?? null,
            "filter_customer" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getLaporanPenjualanPerPelanggan($condition, $addCondition, 0, 0);

        $dataAllSalesOrderInvoice = [];
        $no = 1;
        $totalAllInvoice = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $totalInvoice = floatval($data->sum_amount_invoice);
            $totalAllInvoice += $totalInvoice;

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "total_invoice" => number_format($totalInvoice),
                "nama_pelanggan" => $data->nama_pelanggan,
                "kode_pelanggan" => $data->kode_pelanggan,
                "count_invoice" => $data->count_invoice,
                "raw_total" => $totalInvoice
            ]);
        }

        $data = [
            "data" => $dataAllSalesOrderInvoice,
            "totalAllInvoice" => number_format($totalAllInvoice),
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
            "filter_customer" => $filter != "all" ? $this->customerModel->find($filter)['name'] : "All",
            "search" => $search != "all" ? $search : "All"
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPerPelanggan/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Penjualan Per Pelanggan.pdf", array("Attachment" => false));
        exit(0);
    }

    public function printExcel($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen") ?? null,
            "filter_customer" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getLaporanPenjualanPerPelanggan($condition, $addCondition, 0, 0);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul laporan
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->setCellValue('A2', 'LAPORAN PENJUALAN PER PELANGGAN');
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set informasi filter
        $sheet->setCellValue('A3', 'Periode: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A3:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Nama Pelanggan');
        $sheet->setCellValue('C5', 'Kode Pelanggan');
        $sheet->setCellValue('D5', 'Jumlah Data');
        $sheet->setCellValue('E5', 'Jumlah');

        // Style header tabel
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0']
            ]
        ];
        $sheet->getStyle('A5:E5')->applyFromArray($headerStyle);

        // Isi data
        $row = 6;
        $no = 1;
        $totalAllInvoice = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $totalInvoice = floatval($data->sum_amount_invoice);
            $totalAllInvoice += $totalInvoice;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data->nama_pelanggan);
            $sheet->setCellValue('C' . $row, $data->kode_pelanggan);
            $sheet->setCellValue('D' . $row, $data->count_invoice);
            $sheet->setCellValue('E' . $row, $totalInvoice);

            $row++;
        }

        // Total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, $totalAllInvoice);

        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Format kolom jumlah
        $sheet->getStyle('E7:E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto size columns
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul file
        $filename = "Laporan Penjualan Per Pelanggan.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function printPDFPerPelangganPerPenjual($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $condition = [
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen") ?? null,
            "filter_customer" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getLaporanPenjualanPerPelanggan($condition, $addCondition, 0, 0);

        // Kelompokkan data per penjual
        $groupedData = [];
        $totalAllInvoice = 0;
        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $totalInvoice = floatval($data->sum_amount_invoice);
            $totalAllInvoice += $totalInvoice;

            $namaPenjual = "";
            if ($data->jenis_penjualan == 1) {
                $namaPenjual = $data->salesName;
            } elseif ($data->jenis_penjualan == 2) {
                $namaPenjual = "E-Commerce";
            } else {
                $namaPenjual = "Office";
            }

            $groupedData[$namaPenjual][] = [
                "nama_pelanggan" => $data->nama_pelanggan,
                "kode_pelanggan" => $data->kode_pelanggan,
                "count_invoice" => $data->count_invoice,
                "total_invoice" => number_format($totalInvoice),
                "raw_total" => $totalInvoice
            ];
        }

        $data = [
            "groupedData" => $groupedData,
            "totalAllInvoice" => number_format($totalAllInvoice),
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
            "filter_customer" => $filter != "all" ? $this->customerModel->find($filter)['name'] : "All",
            "search" => $search != "all" ? $search : "All"
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPerPelanggan/printPerPenjual', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Penjualan Per Pelanggan.pdf", ["Attachment" => false]);
        exit(0);
    }

    public function printExcelPerPelangganPerPenjual($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $condition = [
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen") ?? null,
            "filter_customer" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getLaporanPenjualanPerPelanggan($condition, $addCondition, 0, 0);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul laporan
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->setCellValue('A2', 'LAPORAN PENJUALAN PER PELANGGAN');
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set informasi filter
        $sheet->setCellValue('A3', 'Periode: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A3:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row = 5;
        $totalAllInvoice = 0;

        // Kelompokkan data berdasarkan nama penjual
        $groupedData = [];
        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $namaPenjual = "";
            if ($data->jenis_penjualan == 1) {
                $namaPenjual = $data->salesName;
            } elseif ($data->jenis_penjualan == 2) {
                $namaPenjual = "E-Commerce";
            } else {
                $namaPenjual = "Office";
            }

            $groupedData[$namaPenjual][] = $data;
        }

        // Loop per penjual
        foreach ($groupedData as $penjual => $pelangganList) {
            // Tulis nama penjual
            $sheet->setCellValue('A' . $row, $penjual);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            // Set header tabel
            $sheet->setCellValue('A' . $row, 'No');
            $sheet->setCellValue('B' . $row, 'Nama Pelanggan');
            $sheet->setCellValue('C' . $row, 'Kode Pelanggan');
            $sheet->setCellValue('D' . $row, 'Jumlah Data');
            $sheet->setCellValue('E' . $row, 'Jumlah');

            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE0E0E0']
                ]
            ];
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($headerStyle);
            $row++;

            $no = 1;
            $totalPerPenjual = 0;

            foreach ($pelangganList as $data) {
                $totalInvoice = floatval($data->sum_amount_invoice);
                $totalPerPenjual += $totalInvoice;
                $totalAllInvoice += $totalInvoice;

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $data->nama_pelanggan);
                $sheet->setCellValue('C' . $row, $data->kode_pelanggan);
                $sheet->setCellValue('D' . $row, $data->count_invoice);
                $sheet->setCellValue('E' . $row, $totalInvoice);

                $row++;
            }

            // Total per penjual
            $sheet->setCellValue('A' . $row, 'TOTAL ' . $penjual);
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->setCellValue('E' . $row, $totalPerPenjual);

            $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD9D9D9');

            $row += 2; // beri jarak antar penjual
        }

        // Auto size columns
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set format kolom jumlah
        $sheet->getStyle('E6:E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $filename = "Laporan Penjualan Per Pelanggan.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
