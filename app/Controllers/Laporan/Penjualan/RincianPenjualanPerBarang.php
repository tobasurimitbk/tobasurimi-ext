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
            "sales_order_invoice_detail.deletedAt" => null,
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
        $totalHppPerBarang = 0;
        $totalLabaPerBarang = 0;
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentBarang !== $data->id_barang_invoice) {
                if ($currentBarang !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no_faktur" => 'Total',
                        "tanggal_faktur" => number_format($totalPerBarang, 0, ',', '.'),
                        "keterangan" => number_format($totalHppPerBarang, 0, ',', '.'),
                        "qty_invoice" => number_format($totalLabaPerBarang, 0, ',', '.'),
                        "is_total" => true,
                    ]);
                }

                $currentBarang = $data->id_barang_invoice;
                $totalPerBarang = 0;
                $totalHPPPerBarang = 0;
                $totalLabaPerBarang = 0;

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

            $laba = floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);

            if ($data->jenis_penjualan == "1") {
                $salesName = $data->salesName;
            } elseif ($data->jenis_penjualan == 2) {
                $salesName = "Office";
            } else {
                $salesName = "E-Commerce";
            }

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "qty_invoice" => $data->qty_invoice,
                "kode_satuan" => $data->kode_satuan,
                "total_invoice" => number_format(floatval($data->sum_amount_invoice), 0, ',', '.'),
                "amt_harga_pokok" => number_format(floatval($data->amt_harga_pokok), 0, ',', '.'),
                "amt_laba" => number_format($laba, 0, ',', '.'),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $salesName,
                "id_barang" => $data->id_barang_invoice,
                "kode_barang" => $data->kode_barang,
                "barang_name" => $data->barang_name,
            ]);

            $totalPerBarang += floatval($data->sum_amount_invoice);
            $totalHppPerBarang += floatval($data->amt_harga_pokok);
            $totalLabaPerBarang += floatval($laba);
        }

        if ($currentBarang !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no_faktur" => 'Total',
                "tanggal_faktur" => number_format($totalPerBarang, 0, ',', '.'),
                "keterangan" => number_format($totalHppPerBarang, 0, ',', '.'),
                "qty_invoice" => number_format($totalLabaPerBarang, 0, ',', '.'),
                "is_total" => true,
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
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice_detail.deletedAt" => null,
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

        $dataAllSalesOrderInvoice = [];
        $currentBarang = null;
        $totalPerBarang = 0;
        $totalHPPPerBarang = 0;
        $qtyPerBarang = 0;
        $totalLabaPerBarang = 0;
        $grandTotalInvoice = 0;
        $grandTotalHPP = 0;
        $grandTotalLaba = 0;
        $grandQtyPerBarang = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentBarang !== $data->id_barang_invoice) {
                if ($currentBarang !== null) {
                    $dataAllSalesOrderInvoice[] = [
                        "is_total" => true,
                        "total_qty" => number_format($qtyPerBarang, 2, ',', '.'),
                        "total_invoice" => number_format($totalPerBarang, 2, ',', '.'),
                        "total_hpp" => number_format($totalHPPPerBarang, 2, ',', '.'),
                        "total_laba" => number_format($totalLabaPerBarang, 2, ',', '.'),
                    ];
                }

                $currentBarang = $data->id_barang_invoice;
                $totalPerBarang = 0;
                $totalHPPPerBarang = 0;
                $totalLabaPerBarang = 0;
                $qtyPerBarang = 0;

                $dataAllSalesOrderInvoice[] = [
                    "is_customer" => true,
                    "kode_barang" => $data->kode_barang,
                    "barang_name" => $data->barang_name,
                ];
            }

            $laba = floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);
            
            $grandTotalInvoice += floatval($data->sum_amount_invoice);
            $grandTotalHPP     += floatval($data->amt_harga_pokok);
            $grandQtyPerBarang += floatval($data->qty_invoice);
            $grandTotalLaba    += $laba;

            if ($data->jenis_penjualan == "1") {
                $salesName = $data->salesName;
            } elseif ($data->jenis_penjualan == 2) {
                $salesName = "Office";
            } else {
                $salesName = "E-Commerce";
            }

            $dataAllSalesOrderInvoice[] = [
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "qty_invoice" => $data->qty_invoice,
                "kode_satuan" => $data->kode_satuan,
                "total_invoice" => number_format($data->sum_amount_invoice, 2, ',', '.'),
                "amt_harga_pokok" => number_format($data->amt_harga_pokok, 2, ',', '.'),
                "amt_laba" => number_format($laba, 0, ',', '.'),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $salesName,
            ];

            $totalPerBarang += floatval($data->sum_amount_invoice);
            $totalHPPPerBarang += floatval($data->amt_harga_pokok);
            $qtyPerBarang += floatval($data->qty_invoice);
            $totalLabaPerBarang += $laba;
        }

        if ($currentBarang !== null) {
            $dataAllSalesOrderInvoice[] = [
                "is_total" => true,
                "total_qty" => number_format($qtyPerBarang, 2, ',', '.'),
                "total_invoice" => number_format($totalPerBarang, 2, ',', '.'),
                "total_hpp" => number_format($totalHPPPerBarang, 2, ',', '.'),
                "total_laba" => number_format($totalLabaPerBarang, 2, ',', '.'),
            ];
        }

        $dataAllSalesOrderInvoice[] = [
            "is_grand_total" => true,
            "total_qty"      => number_format($grandQtyPerBarang, 2, ',', '.'),
            "total_invoice"  => number_format($grandTotalInvoice, 2, ',', '.'),
            "total_hpp"      => number_format($grandTotalHPP, 2, ',', '.'),
            "total_laba"     => number_format($grandTotalLaba, 2, ',', '.'),
        ];

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
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $condition = [
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice_detail.deletedAt" => null,
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
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A2', 'LAPORAN RINCIAN SALES PER BARANG');
        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A3', 'PERIODE: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A3:K3');

        // Header tabel
        $sheet->fromArray([
            ["No Faktur", "Tanggal", "Keterangan", "Qty", "Satuan", "Total Invoice", "Nilai HPP", "Laba Kotor", "Nama Barang", "Nama Pelanggan", "Nama Sales"]
        ], null, 'A5');

        $sheet->getStyle('A1:K4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5:K5')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:K5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row = 6;
        $currentBarang = null;
        $totalPerBarang = 0;
        $totalHPPPerBarang = 0;
        $qtyPerBarang = 0;
        $totalLabaPerBarang = 0;
        $grandTotalInvoice = 0;
        $grandTotalHPP = 0;
        $grandTotalLaba = 0;
        $grandQtyPerBarang = 0;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentBarang !== $data->id_barang_invoice) {
                if ($currentBarang !== null) {
                    // Baris total per barang
                    $sheet->setCellValue('A' . $row, '');
                    $sheet->mergeCells('A' . $row . ':C' . $row);
                    $sheet->setCellValue('D' . $row, $qtyPerBarang);
                    $sheet->setCellValue('F' . $row, $totalPerBarang);
                    $sheet->setCellValue('G' . $row, $totalHPPPerBarang);
                    $sheet->setCellValue('H' . $row, $totalLabaPerBarang);
                    $sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);
                    $row++;
                }

                $currentBarang = $data->id_barang_invoice;
                $totalPerBarang = 0;
                $totalHPPPerBarang = 0;
                $totalLabaPerBarang = 0;
                $qtyPerBarang = 0;

                // Baris header barang
                $sheet->setCellValue('A' . $row, $data->kode_barang . ' - ' . $data->barang_name);
                $sheet->mergeCells('A' . $row . ':K' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;
            }

            $laba = floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);

            $grandTotalInvoice += floatval($data->sum_amount_invoice);
            $grandTotalHPP     += floatval($data->amt_harga_pokok);
            $grandQtyPerBarang += floatval($data->qty_invoice);
            $grandTotalLaba    += $laba;
            
            if ($data->jenis_penjualan == "1") {
                $salesName = $data->salesName;
            } elseif ($data->jenis_penjualan == 2) {
                $salesName = "Office";
            } else {
                $salesName = "E-Commerce";
            }

            $sheet->fromArray([
                $data->no_faktur,
                $data->tanggal_faktur,
                $data->keterangan,
                $data->qty_invoice,
                $data->kode_satuan,
                $data->sum_amount_invoice,
                $data->amt_harga_pokok,
                $laba,
                $data->barang_name,
                $data->nama_pelanggan,
                $salesName,
            ], null, 'A' . $row);

            $totalPerBarang += floatval($data->sum_amount_invoice);
            $totalHPPPerBarang += floatval($data->amt_harga_pokok);
            $qtyPerBarang += floatval($data->qty_invoice);
            $totalLabaPerBarang += $laba;
            $row++;
        }

        if ($currentBarang !== null) {
            // Baris total per barang terakhir
            $sheet->setCellValue('A' . $row, '');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->setCellValue('D' . $row, $totalPerBarang);
            $sheet->setCellValue('F' . $row, $totalPerBarang);
            $sheet->setCellValue('G' . $row, $totalHPPPerBarang);
            $sheet->setCellValue('H' . $row, $totalLabaPerBarang);
            $sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);
        }

        $row++;

        // GRAND TOTAL
        $sheet->setCellValue('A' . $row, 'GRAND TOTAL');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('D' . $row, $grandQtyPerBarang);
        $sheet->setCellValue('F' . $row, $grandTotalInvoice);
        $sheet->setCellValue('G' . $row, $grandTotalHPP);
        $sheet->setCellValue('H' . $row, $grandTotalLaba);

        $sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);

        // Format kolom angka
        $sheet->getStyle('F5:F' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G5:G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H5:H' . $row)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'K') as $col) {
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
