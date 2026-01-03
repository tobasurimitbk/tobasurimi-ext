<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HistoriPesananPenjualan extends BaseController
{
    protected $this_company_id;
    protected $barangMasterSalesModel;
    protected $salesOrderInvoiceModel;
    protected $salesOrderModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $this->salesOrderModel = new SalesOrderModel();
    }

    public function index()
    {
        $barangMasterSalesData = $this->barangMasterSalesModel->asObject()->findAll();
        $data = [
            'barangMasterSalesData' => $barangMasterSalesData
        ];
        return view('Laporan/LaporanSales/LaporanHistoriPesananPenjualan/index', $data);
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

        $dataSalesOrder = $this->salesOrderModel
            ->getAllSalesOrderLokalBarang($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderInvoice = [];
        $currentSalesOrder = null;
        $totalPerBarang = 0;
        $qtyPerBarang = 0;
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrder['data'] as $data) {
            if ($currentSalesOrder !== $data->id) {
                if ($currentSalesOrder !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "tipe_proses" => 'Total',
                        "qty_faktur" => number_format($totalPerBarang, 0, ',', '.'),
                        "qty_order" => number_format($qtyPerBarang, 0, ',', '.'),
                        "is_total" => true,
                    ]);
                }

                $totalPerBarang = 0;
                $qtyPerBarang = 0;

                $currentSalesOrder = $data->id;

                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "tipe_proses" => $data->no_sales_order . "&nbsp;&nbsp;&nbsp;&nbsp;" . $data->tanggal_order . "&nbsp;&nbsp;&nbsp;&nbsp;" . $data->nama_pelanggan,
                    "no_faktur" => '',
                    "tanggal_faktur" => '',
                    "qty_faktur" => '',
                    "nama_pelanggan" => '',
                    "nama_barang" => '',
                    "nama_sales" => '',
                    "qty_order" => '',
                    "satuan" => '',
                    "keterangan" => '',
                    "is_customer" => true,
                ]);
            }

            if ($data->jenis_penjualan == "1") {
                $salesName = $data->salesName;
            }else if ($data->jenis_penjualan == "3") {
                $salesName = !empty($data->nama_ecommerce) ? $data->nama_ecommerce : "E COMMERCE";
            } else {
                $salesName = "OFFICE";
            }

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "tipe_proses" => isset($data->id_sales_order_invoice) ? 'Faktur Penjualan' : 'Surat Jalan',
                "no_faktur" => $data->document_no,
                "tanggal_faktur" => $data->tanggal_faktur,
                "qty_faktur" => $data->qty_faktur,
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_barang" => $data->barang_name,
                "nama_sales" => $salesName,
                "qty_order" => $data->qty_order,
                "satuan" => $data->kode_satuan,
                "keterangan" => $data->keterangan,
            ]);

            $totalPerBarang += floatval($data->qty_faktur);
            $qtyPerBarang += floatval($data->qty_order);
        }

        if ($currentSalesOrder !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "tipe_proses" => 'Total',
                "qty_faktur" => number_format($totalPerBarang, 0, ',', '.'),
                "qty_order" => number_format($qtyPerBarang, 0, ',', '.'),
                "is_total" => true,
            ]);
        }

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $dataSalesOrder['totalData'],
            "recordsFiltered" => $dataSalesOrder['totalFilteredData'],
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

        $dataSalesOrder = $this->salesOrderModel
            ->getAllSalesOrderLokalBarang($condition, $addCondition, null, null);

        $dataAllSalesOrderInvoice = [];
        $currentSalesOrder = null;
        $totalPerBarang = 0;
        $qtyPerBarang = 0;
        $no = 1;

        foreach ($dataSalesOrder['data'] as $data) {
            if ($currentSalesOrder !== $data->id) {
                if ($currentSalesOrder !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "tipe_proses" => 'Total',
                        "qty_faktur" => number_format($totalPerBarang, 0, ',', '.'),
                        "qty_order" => number_format($qtyPerBarang, 0, ',', '.'),
                        "is_total" => true,
                    ]);
                }

                $totalPerBarang = 0;
                $qtyPerBarang = 0;

                $currentSalesOrder = $data->id;

                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "tipe_proses" => $data->no_sales_order . "&nbsp;&nbsp;&nbsp;&nbsp;" . $data->tanggal_order . "&nbsp;&nbsp;&nbsp;&nbsp;" . $data->nama_pelanggan,
                    "no_faktur" => '',
                    "tanggal_faktur" => '',
                    "qty_faktur" => '',
                    "nama_pelanggan" => '',
                    "nama_barang" => '',
                    "nama_sales" => '',
                    "qty_order" => '',
                    "satuan" => '',
                    "keterangan" => '',
                    "is_customer" => true,
                ]);
            }

            if ($data->jenis_penjualan == "1") {
                $salesName = $data->salesName;
            }else if ($data->jenis_penjualan == "3") {
                $salesName = !empty($data->nama_ecommerce) ? $data->nama_ecommerce : "E COMMERCE";
            } else {
                $salesName = "OFFICE";
            }

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "tipe_proses" => isset($data->id_sales_order_invoice) ? 'Faktur Penjualan' : 'Surat Jalan',
                "no_faktur" => $data->document_no,
                "tanggal_faktur" => $data->tanggal_faktur,
                "qty_faktur" => $data->qty_faktur,
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_barang" => $data->barang_name,
                "nama_sales" => $salesName,
                "qty_order" => $data->qty_order,
                "satuan" => $data->kode_satuan,
                "keterangan" => $data->keterangan,
            ]);

            $totalPerBarang += floatval($data->qty_faktur);
            $qtyPerBarang += floatval($data->qty_order);
        }

        if ($currentSalesOrder !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "tipe_proses" => 'Total',
                "qty_faktur" => number_format($totalPerBarang, 0, ',', '.'),
                "qty_order" => number_format($qtyPerBarang, 0, ',', '.'),
                "is_total" => true,
            ]);
        }

        $data = [
            "data" => $dataAllSalesOrderInvoice,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanHistoriPesananPenjualan/print', $data));
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

        // SAMA seperti PDF
        $dataSalesOrder = $this->salesOrderModel
            ->getAllSalesOrderLokalBarang($condition, $addCondition, null, null);

        // Prepare Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // HEADER
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A2', 'Histori Pesanan Penjualan');
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A3', 'Periode: ' . 
            ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") 
            . ' - ' . 
            ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now")
        );
        $sheet->mergeCells('A3:J3');

        $sheet->getStyle('A1:J3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:J3')->getAlignment()->setHorizontal('center');

        // TABLE HEADER
        $sheet->fromArray([
            ["Tipe Proses", "No Faktur", "Tanggal Faktur", "Qty Faktur", "Nama Pelanggan",
            "Nama Barang", "Nama Penjual", "Kuantitas", "Satuan", "Keterangan"]
        ], null, 'A5');

        $sheet->getStyle('A5:J5')->getFont()->setBold(true);

        // DATA
        $row = 6;
        $currentSalesOrder = null;
        $totalFaktur = 0;
        $totalOrder = 0;

        foreach ($dataSalesOrder['data'] as $data) {

            if ($currentSalesOrder !== $data->id) {

                // Tambahkan total jika bukan SO pertama
                if ($currentSalesOrder !== null) {
                    $sheet->fromArray([
                        ["Total", "", "", ($totalFaktur), "", "", "",
                        ($totalOrder), "", ""]
                    ], null, 'A' . $row);

                    $sheet->getStyle("A{$row}:J{$row}")->getFont()->setBold(true);
                    $row++;
                }

                // Reset total
                $totalFaktur = 0;
                $totalOrder = 0;

                // Header customer (group header)
                $sheet->setCellValue('A' . $row,
                    $data->no_sales_order . "    " . $data->tanggal_order . "    " . $data->nama_pelanggan
                );
                $sheet->mergeCells("A{$row}:J{$row}");
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;

                $currentSalesOrder = $data->id;
            }

            // Nama sales
            if ($data->jenis_penjualan == "1") {
                $salesName = $data->salesName;
            } elseif ($data->jenis_penjualan == "3") {
                $salesName = !empty($data->nama_ecommerce) ? $data->nama_ecommerce : "E COMMERCE";
            } else {
                $salesName = "OFFICE";
            }

            // Detail row
            $sheet->fromArray([
                isset($data->id_sales_order_invoice) ? 'Faktur Penjualan' : 'Surat Jalan',
                $data->document_no,
                $data->tanggal_faktur,
                $data->qty_faktur,
                $data->nama_pelanggan,
                $data->barang_name,
                $salesName,
                $data->qty_order,
                $data->kode_satuan,
                $data->keterangan,
            ], null, 'A' . $row);

            // Akumulasikan total
            $totalFaktur += floatval($data->qty_faktur);
            $totalOrder += floatval($data->qty_order);

            $row++;
        }

        // Total terakhir
        if ($currentSalesOrder !== null) {
            $sheet->fromArray([
                ["Total", "", "", ($totalFaktur), "", "", "",
                ($totalOrder), "", ""]
            ], null, 'A' . $row);

            $sheet->getStyle("A{$row}:J{$row}")->getFont()->setBold(true);
        }

        // Autosize
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output Excel
        $filename = "Histori Pesanan Penjualan.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
