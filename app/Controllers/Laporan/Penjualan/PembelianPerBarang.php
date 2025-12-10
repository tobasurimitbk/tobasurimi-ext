<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CustomerModel;
use App\Models\SalesFakturModel;
use App\Models\SupplierLokalModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PembelianPerBarang extends BaseController
{
    protected $this_company_id;
    protected $barangMasterSalesModel;
    protected $salesFakturModel;
    protected $supplierLokalModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->salesFakturModel = new SalesFakturModel();
        $this->supplierLokalModel = new SupplierLokalModel();
    }

    public function index()
    {
        $barangMasterSalesData = $this->barangMasterSalesModel->asObject()->findAll();
        $data = [
            'barangMasterSalesData' => $barangMasterSalesData
        ];
        return view('Laporan/LaporanSales/LaporanPembelianPerBarang/index', $data);
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
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_barang" => $this->request->getGet("filter"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderInvoice = $this->salesFakturModel
            ->getSalesOrderLokalPerBarang($condition, $addCondition, $pageSize, $offset);

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

    public function printPDFAll($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesFakturModel
            ->getSalesOrderLokalPerBarang($condition, $addCondition, null, null);

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
            "filter_barang" => $filter != "all" ? $this->barangMasterSalesModel->find($filter)['barang_name'] : "All",
            "search" => $search != "all" ? $search : "All"
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPembelianPerBarang/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Pembelian Per Barang.pdf", array("Attachment" => false));
        exit(0);
    }

    public function printExcelAll($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesFakturModel
            ->getSalesOrderLokalPerBarang($condition, $addCondition, 0, 0);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul laporan
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A2', 'LAPORAN PEMBELIAN PER BARANG');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set informasi filter
        $sheet->setCellValue('A3', 'Periode: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A3:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Keterangan Barang');
        $sheet->setCellValue('C5', 'Kuantitas');
        $sheet->setCellValue('D5', 'Satuan');
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

        $totalAllQtyInvoice = 0;
        $totalAllInvoice = 0;
        $totalAllHpp = 0;
        $totalAllLaba = 0;
        $totalAllCountInvoice = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $totalQtyInvoice = floatval($data->sum_qty_invoice);
            $totalInvoice = floatval($data->sum_amount_invoice);
            $totalHpp = floatval($data->amt_harga_pokok);
            $totalLaba = floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);
            $totalCountInvoice = floatval($data->count_invoice);

            $totalAllQtyInvoice += $totalQtyInvoice;
            $totalAllInvoice += $totalInvoice;
            $totalAllHpp += $totalHpp;
            $totalAllLaba += $totalLaba;
            $totalAllCountInvoice += $totalCountInvoice;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data->barang_name);
            $sheet->setCellValue('C' . $row, $totalQtyInvoice);
            $sheet->setCellValue('D' . $row, $data->kode_satuan);
            $sheet->setCellValue('E' . $row, $totalInvoice);

            $row++;
        }

        // Total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, $totalAllQtyInvoice);
        $sheet->setCellValue('E' . $row, $totalAllInvoice);

        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Format kolom jumlah
        $sheet->getStyle('C6:C' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E6:E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto size columns
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul file
        $filename = "Laporan Pembelian Per Barang.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function printPDFTotal($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesFakturModel
            ->getSalesOrderLokalPerBarang($condition, $addCondition, null, null);

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
            "filter_barang" => $filter != "all" ? $this->barangMasterSalesModel->find($filter)['barang_name'] : "All",
            "search" => $search != "all" ? $search : "All"
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPembelianPerBarang/print-total', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Pembelian Per Barang.pdf", array("Attachment" => false));
        exit(0);
    }

    public function printExcelTotal($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesFakturModel
            ->getSalesOrderLokalPerBarang($condition, $addCondition, null, null);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul laporan
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A2', 'Pembelian per Barang (Total)');
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set informasi filter
        $sheet->setCellValue('A3', 'Periode: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A3:D3');
        $sheet->getStyle('A3:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Keterangan Barang');
        $sheet->setCellValue('C5', 'No. Barang');
        $sheet->setCellValue('D5', 'Jumlah');

        // Style header tabel
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0']
            ]
        ];
        $sheet->getStyle('A5:D5')->applyFromArray($headerStyle);

        // Isi data
        $row = 6;
        $no = 1;

        $totalAllQtyInvoice = 0;
        $totalAllInvoice = 0;
        $totalAllHpp = 0;
        $totalAllLaba = 0;
        $totalAllCountInvoice = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $totalQtyInvoice = floatval($data->sum_qty_invoice);
            $totalInvoice = floatval($data->sum_amount_invoice);
            $totalHpp = floatval($data->amt_harga_pokok);
            $totalLaba = floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);
            $totalCountInvoice = floatval($data->count_invoice);

            $totalAllQtyInvoice += $totalQtyInvoice;
            $totalAllInvoice += $totalInvoice;
            $totalAllHpp += $totalHpp;
            $totalAllLaba += $totalLaba;
            $totalAllCountInvoice += $totalCountInvoice;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data->barang_name);
            $sheet->setCellValue('C' . $row, $data->kode_barang);
            $sheet->setCellValue('D' . $row, $totalInvoice);

            $row++;
        }

        // Total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('D' . $row, $totalAllInvoice);

        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Format kolom jumlah
        $sheet->getStyle('D6:D' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto size columns
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul file
        $filename = "Laporan Pembelian Per Barang (Total).xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function printPDFKuantitas($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesFakturModel
            ->getSalesOrderLokalPerBarang($condition, $addCondition, null, null);

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
            "filter_barang" => $filter != "all" ? $this->barangMasterSalesModel->find($filter)['barang_name'] : "All",
            "search" => $search != "all" ? $search : "All"
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPembelianPerBarang/print-kuantitas', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Pembelian Per Barang.pdf", array("Attachment" => false));
        exit(0);
    }

    public function printExcelKuantitas($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Get all data without pagination
        $dataSalesOrderInvoice = $this->salesFakturModel
            ->getSalesOrderLokalPerBarang($condition, $addCondition, 0, 0);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul laporan
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A2', 'Pembelian per Barang (Kuantitas)');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set informasi filter
        $sheet->setCellValue('A3', 'Periode: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A3:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Keterangan Barang');
        $sheet->setCellValue('C5', 'No. Barang');
        $sheet->setCellValue('D5', 'Satuan');
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

        $totalAllQtyInvoice = 0;
        $totalAllInvoice = 0;
        $totalAllHpp = 0;
        $totalAllLaba = 0;
        $totalAllCountInvoice = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $totalQtyInvoice = floatval($data->sum_qty_invoice);
            $totalInvoice = floatval($data->sum_amount_invoice);
            $totalHpp = floatval($data->amt_harga_pokok);
            $totalLaba = floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);
            $totalCountInvoice = floatval($data->count_invoice);

            $totalAllQtyInvoice += $totalQtyInvoice;
            $totalAllInvoice += $totalInvoice;
            $totalAllHpp += $totalHpp;
            $totalAllLaba += $totalLaba;
            $totalAllCountInvoice += $totalCountInvoice;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data->barang_name);
            $sheet->setCellValue('C' . $row, $data->kode_barang);
            $sheet->setCellValue('D' . $row, $data->kode_satuan);
            $sheet->setCellValue('E' . $row, $totalQtyInvoice);

            $row++;
        }

        // Total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, $totalAllQtyInvoice);

        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Format kolom jumlah
        $sheet->getStyle('E6:E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto size columns
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul file
        $filename = "Laporan Pembelian Per Barang (Kuantitas).xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function printPDFPemasok($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        $condition = [
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Ambil semua supplier yg punya transaksi
        $listSupplier = $this->supplierLokalModel
            ->select('supplier_lokals.id, supplier_lokals.name')
            ->join('sales_faktur', 'sales_faktur.id_customer = supplier_lokals.id', 'left')
            ->where('sales_faktur.deletedAt', null)
            ->groupBy('supplier_lokals.id')
            ->findAll();

        // Ambil data pivot dari model baru
        $rawData = $this->salesFakturModel
            ->getSalesOrderPivotPerSupplier($condition, $addCondition);

        // Pivoting manually
        $result = [];
        foreach ($rawData as $row) {

            $barangId = $row->id_barang;

            if (!isset($result[$barangId])) {
                $result[$barangId] = [
                    'barang_name' => $row->barang_name,
                    'kode_barang' => $row->kode_barang,
                    'kode_satuan' => $row->kode_satuan,
                    'suppliers' => []
                ];
            }

            $result[$barangId]['suppliers'][$row->supplier_id] = number_format(floatval($row->amount_supplier));
        }

        // Convert to final output array
        $finalData = [];
        $no = 1;

        foreach ($result as $barangId => $barang) {

            $row = [
                'no' => $no++,
                'barang_name' => $barang['barang_name'],
                'kode_barang' => $barang['kode_barang'],
                'kode_satuan' => $barang['kode_satuan'],
            ];

            // Tambahkan qty per supplier
            foreach ($listSupplier as $sup) {
                $row['sup_' . $sup['id']] =
                    $barang['suppliers'][$sup['id']] ?? 0;
            }

            $finalData[] = $row;
        }

        // Data yang akan dikirim ke view
        $data = [
            "data" => $finalData,
            "listSupplier" => $listSupplier,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPembelianPerBarang/print-pemasok', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Pembelian Barang per Supplier.pdf", array("Attachment" => false));
        exit(0);
    }

    public function printExcelPemasok($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        $condition = [
            "sales_faktur.deletedAt" => null,
            "sales_faktur.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search != "all" ? $search : null,
            "filter_barang" => $filter != "all" ? $filter : null,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        // Supplier dinamis (SAMA DGN PDF)
        $listSupplier = $this->supplierLokalModel
            ->select('supplier_lokals.id, supplier_lokals.name')
            ->join('sales_faktur', 'sales_faktur.id_customer = supplier_lokals.id', 'left')
            ->where('sales_faktur.deletedAt', null)
            ->groupBy('supplier_lokals.id')
            ->findAll();

        // Ambil pivot data (SAMA DGN PDF)
        $rawData = $this->salesFakturModel
            ->getSalesOrderPivotPerSupplier($condition, $addCondition);

        // Pivot manual (SAMA DGN PDF)
        $result = [];
        foreach ($rawData as $row) {

            $barangId = $row->id_barang;

            if (!isset($result[$barangId])) {
                $result[$barangId] = [
                    'barang_name' => $row->barang_name,
                    'kode_barang' => $row->kode_barang,
                    'kode_satuan' => $row->kode_satuan,
                    'suppliers' => []
                ];
            }

            $result[$barangId]['suppliers'][$row->supplier_id] =
                number_format(floatval($row->amount_supplier));
        }

        // Convert ke bentuk final (SAMA DGN PDF)
        $finalData = [];
        $no = 1;

        foreach ($result as $barangId => $barang) {

            $row = [
                'no' => $no++,
                'barang_name' => $barang['barang_name'],
                'kode_barang' => $barang['kode_barang'],
                'kode_satuan' => $barang['kode_satuan'],
            ];

            foreach ($listSupplier as $sup) {
                $row['sup_' . $sup['id']] =
                    $barang['suppliers'][$sup['id']] ?? 0;
            }

            $finalData[] = $row;
        }

        // ===================================
        //            MULAI EXCEL
        // ===================================

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // HEADER TIDAK DIUBAH SESUAI PERMINTAAN
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->mergeCells('A1:E1');

        $sheet->setCellValue('A2', 'Pembelian Barang per Pemasok');
        $sheet->mergeCells('A2:E2');

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        $sheet->setCellValue(
            'A3',
            'Periode: ' .
                ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All")
                . ' - ' .
                ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now")
        );
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A3:A3')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // Header tabel (MENYESUAIKAN PDF)
        $col = 'A';
        $sheet->setCellValue($col++ . '5', 'No');
        $sheet->setCellValue($col++ . '5', 'Keterangan Barang');

        // Supplier dinamis
        foreach ($listSupplier as $s) {
            $sheet->setCellValue($col++ . '5', $s['name']);
        }

        // STYLE HEADER (tidak diubah)
        $sheet->getStyle('A5:' . chr(ord('A') + count($listSupplier) + 1) . '5')
            ->getFont()->setBold(true);

        // Isi data
        $rowExcel = 6;
        foreach ($finalData as $row) {

            $col = 'A';

            $sheet->setCellValue($col++ . $rowExcel, $row['no']);
            $sheet->setCellValue($col++ . $rowExcel, $row['barang_name']);

            foreach ($listSupplier as $s) {
                $sheet->setCellValue(
                    $col++ . $rowExcel,
                    $row['sup_' . $s['id']]
                );
            }

            $rowExcel++;
        }

        // Auto size
        foreach (range('A', chr(ord('A') + count($listSupplier) + 1)) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Output
        $filename = "Laporan Pembelian Barang per Pemasok.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
