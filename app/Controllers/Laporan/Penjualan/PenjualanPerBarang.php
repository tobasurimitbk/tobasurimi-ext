<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;

class PenjualanPerBarang extends BaseController
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
        $barangMasterSalesData = $this->barangMasterSalesModel->asObject()->findAll();
        $data = [
            'barangMasterSalesData' => $barangMasterSalesData
        ];
        return view('Laporan/LaporanSales/LaporanPerBarang/index', $data);
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
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_barang" => $this->request->getGet("filter"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getSalesOrderInvoiceLokalPerBarang($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderInvoice = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "barang_name" => $data->barang_name,
                "qty_invoice" => $data->sum_qty_invoice,
                "kode_satuan" => $data->kode_satuan,
                "sum_amount_invoice" => number_format(floatval($data->sum_amount_invoice)),
                "amt_harga_pokok" => number_format(floatval($data->amt_harga_pokok)),
                "amt_laba" => number_format(floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok)),
                "count_invoice" => $data->count_invoice,
                "kode_barang" => $data->kode_barang,
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
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen") ?? null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getSalesOrderInvoiceLokalPerBarang($condition, $addCondition, null, null);

        $dataAllSalesOrderInvoice = [];
        $no = 1;
        $totalQty = 0;
        $totalInvoice = 0;
        $totalHpp = 0;
        $totalLabaKotor = 0;
        $totalData = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "barang_name" => $data->barang_name,
                "qty_invoice" => $data->sum_qty_invoice,
                "kode_satuan" => $data->kode_satuan,
                "sum_amount_invoice" => number_format(floatval($data->sum_amount_invoice)),
                "amt_harga_pokok" => number_format(floatval($data->amt_harga_pokok)),
                "amt_laba" => number_format(floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok)),
                "count_invoice" => $data->count_invoice,
                "kode_barang" => $data->kode_barang,
            ]);
            $totalQty += floatval($data->sum_qty_invoice);
            $totalInvoice += floatval($data->sum_amount_invoice);
            $totalHpp += floatval($data->amt_harga_pokok);
            $totalLabaKotor += floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);
            $totalData += floatval($data->count_invoice);
        }

        $data = [
            "data" => $dataAllSalesOrderInvoice,
            "totalQty" => number_format($totalQty),
            "totalInvoice" => number_format($totalInvoice),
            "totalHpp" => number_format($totalHpp),
            "totalLabaKotor" => number_format($totalLabaKotor),
            "totalData" => number_format($totalData),
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
            "filter_customer" => $filter != "all" ? $this->barangMasterSalesModel->find($filter)['barang_name'] : "All",
            "search" => $search != "all" ? $search : "All"
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPerBarang/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Penjualan Per Barang.pdf", array("Attachment" => false));
        exit(0);
    }

    public function printExcel($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
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
}
