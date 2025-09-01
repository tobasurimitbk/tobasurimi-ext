<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RincianPenjualanPerBarang extends BaseController
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
        return view('Laporan/LaporanSales/LaporanRincianPerBarang/index', $data);
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
            ->getAllSalesOrderInvoiceLokalBarang($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderInvoice = [];
        $currentBarang = null;
        $totalPerBarang = 0;
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentBarang !== $data->id_barang_invoice) {
                if ($currentBarang !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no" => '',
                        "id" => '',
                        "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerBarang)),
                        "tanggal_faktur" => '',
                        "keterangan" => '',
                        "total_invoice" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                        "id_barang" => '',
                        "kode_barang" => '',
                        "barang_name" => '',
                        "qty_invoice" => '',
                        "kode_satuan" => '',
                    ]);
                }

                $currentBarang = $data->id_barang_invoice;
                $totalPerBarang = 0;

                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "no_faktur" => $data->kode_barang . ' ' . $data->barang_name,
                    "tanggal_faktur" => '',
                    "keterangan" => '',
                    "total_invoice" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                    "id_barang" => '',
                    "kode_barang" => '',
                    "barang_name" => '',
                    "qty_invoice" => '',
                    "kode_satuan" => '',
                ]);
            }

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "total_invoice" => number_format(floatval($data->sum_amount_invoice)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "id_barang" => $data->id_barang_invoice,
                "kode_barang" => $data->kode_barang,
                "barang_name" => $data->barang_name,
                "qty_invoice" => $data->qty_invoice,
                "kode_satuan" => $data->kode_satuan,
                "sum_harga_pokok" => $data->sum_harga_pokok,
                "amt_harga_pokok" => number_format($data->amt_harga_pokok),
                "amt_laba" => number_format(floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok)),
            ]);

            $totalPerBarang += floatval($data->total_invoice);
        }

        if ($currentBarang !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => '',
                "id" => '',
                "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerBarang)),
                "tanggal_faktur" => '',
                "keterangan" => '',
                "total_invoice" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
                "id_barang" => '',
                "kode_barang" => '',
                "barang_name" => '',
                "qty_invoice" => '',
                "kode_satuan" => '',
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
        $condition = [
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

        // Pakai query rincian, bukan summary
        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokalBarang($condition, $addCondition, null, null);

        $dataAllSalesOrderInvoice = [];
        $currentBarang = null;
        $totalPerBarang = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentBarang !== $data->id_barang_invoice) {
                if ($currentBarang !== null) {
                    $dataAllSalesOrderInvoice[] = [
                        "is_total" => true,
                        "total_invoice" => number_format($totalPerBarang),
                    ];
                }

                $currentBarang = $data->id_barang_invoice;
                $totalPerBarang = 0;

                $dataAllSalesOrderInvoice[] = [
                    "is_customer" => true,
                    "kode_barang" => $data->kode_barang,
                    "barang_name" => $data->barang_name,
                ];
            }

            $dataAllSalesOrderInvoice[] = [
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "total_invoice" => number_format($data->sum_amount_invoice),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "kode_barang" => $data->kode_barang,
                "barang_name" => $data->barang_name,
                "qty_invoice" => $data->qty_invoice,
                "kode_satuan" => $data->kode_satuan,
                "sum_harga_pokok" => $data->sum_harga_pokok,
                "amt_harga_pokok" => number_format($data->amt_harga_pokok),
                "amt_laba" => number_format(floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok)),
            ];

            $totalPerBarang += floatval($data->total_invoice);
        }

        if ($currentBarang !== null) {
            $dataAllSalesOrderInvoice[] = [
                "is_total" => true,
                "total_invoice" => number_format($totalPerBarang),
            ];
        }

        $data = [
            "data" => $dataAllSalesOrderInvoice,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanRincianPerBarang/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Rincian Sales Per Barang.pdf", ["Attachment" => false]);
        exit(0);
    }

    public function printExcelAll($tglAwal = "all", $tglAkhir = "now", $filter = "all", $search = "all")
    {
        $condition = [
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

        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokalBarang($condition, $addCondition, null, null);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header laporan
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A2', 'LAPORAN RINCIAN SALES PER BARANG');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header tabel
        $sheet->fromArray([
            ["No Faktur", "Tanggal", "Keterangan", "Qty", "Satuan", "Total Invoice", "Nama Pelanggan", "Nama Sales"]
        ], null, 'A4');

        $row = 5;
        $currentBarang = null;
        $totalPerBarang = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentBarang !== $data->id_barang_invoice) {
                if ($currentBarang !== null) {
                    $sheet->setCellValue('A' . $row, 'Total Invoice');
                    $sheet->setCellValue('F' . $row, $totalPerBarang);
                    $sheet->getStyle('A' . $row . ':H' . $row)->getFont()->setBold(true);
                    $row++;
                }

                $currentBarang = $data->id_barang_invoice;
                $totalPerBarang = 0;

                $sheet->setCellValue('A' . $row, $data->kode_barang . ' - ' . $data->barang_name);
                $sheet->mergeCells('A' . $row . ':H' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;
            }

            $sheet->fromArray([
                $data->no_faktur,
                $data->tanggal_faktur,
                $data->keterangan,
                $data->qty_invoice,
                $data->kode_satuan,
                $data->total_invoice,
                $data->nama_pelanggan,
                $data->salesName,
            ], null, 'A' . $row);

            $totalPerBarang += floatval($data->total_invoice);
            $row++;
        }

        if ($currentBarang !== null) {
            $sheet->setCellValue('A' . $row, 'Total Invoice');
            $sheet->setCellValue('F' . $row, $totalPerBarang);
            $sheet->getStyle('A' . $row . ':H' . $row)->getFont()->setBold(true);
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Laporan Rincian Sales Per Barang.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
