<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PesananPenjualanPerPelanggan extends BaseController
{
    protected $this_company_id;
    protected $customerModel;
    protected $salesOrderLokal;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->customerModel = new CustomerModel();
        $this->salesOrderLokal = new SalesOrderModel();
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
        return view('Laporan/LaporanSales/LaporanPesananPerPelanggan/index', $data);
    }

    public function allTransaksi()
    {
        $pageSize = (int) $this->request->getGet("length");
        $start    = (int) $this->request->getGet("start");
        $currentPage = ($start / $pageSize) + 1; // hanya untuk info

        $payload = [
            "pageSize"    => $pageSize,
            "currentPage" => $currentPage,
            "search"      => $this->request->getGet("search"),
            "sort"        => $this->request->getGet("sort"),
            "sortType"    => $this->request->getGet("sortType"),
        ];

        $condition = [
            "sales_order.deletedAt"        => null,
            "sales_order.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search"          => $this->request->getGet("search"),
            "sort"            => $this->request->getGet("sort"),
            "sortType"        => $this->request->getGet("sortType"),
            "filter_customer" => $this->request->getGet("filter"),
            "filter_status" => $this->request->getGet("filter_status"),
            "dateStart"       => $this->request->getGet("dateStart")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart"))))
                : "",
            "dateEnd"         => $this->request->getGet("dateEnd")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd"))))
                : "",
            "dateStartShip"       => $this->request->getGet("dateStartShip")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStartShip"))))
                : "",
            "dateEndShip"         => $this->request->getGet("dateEndShip")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEndShip"))))
                : "",
        ];

        // Ambil data dari model
        $dataSalesOrderInvoice = $this->salesOrderLokal
            ->getLaporanPesananPenjualanPerPelanggan($condition, $addCondition, $pageSize, $start);

        $dataAllSalesOrderInvoice = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = $start + 1; // nomor urut sesuai paging

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentCustomer !== $data->nama_pelanggan) {
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no"             => '',
                        "id"             => '',
                        "no_sales_order" => number_format(floatval($totalPerCustomer)),
                        "order_date"     => '',
                        "shipping_date"  => '',
                        "nama_pelanggan" => '',
                        "sum_amount"     => '',
                        "status"         => '',
                        "is_total"       => true,
                    ]);
                }

                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;

                array_push($dataAllSalesOrderInvoice, [
                    "no"             => '',
                    "id"             => '',
                    "no_sales_order" => $data->nama_pelanggan,
                    "order_date"     => '',
                    "shipping_date"  => '',
                    "nama_pelanggan" => '',
                    "sum_amount"     => '',
                    "status"         => '',
                    "is_customer"    => true,
                ]);
            }

            array_push($dataAllSalesOrderInvoice, [
                "no"             => $no++,
                "id"             => encrypt($data->id),
                "no_sales_order" => $data->no_sales_order,
                "order_date"     => $data->order_date,
                "shipping_date"  => $data->shipping_date,
                "nama_pelanggan" => $data->nama_pelanggan,
                "sum_amount"     => number_format(floatval($data->sum_amount)),
                "status"         => !$data->surat_jalan_so_id && !$data->sales_order_invoice_id ? 'Mengantri' : 'Selesai',
            ]);

            $totalPerCustomer += floatval($data->sum_amount);
        }

        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no"             => '',
                "id"             => '',
                "no_sales_order" => number_format(floatval($totalPerCustomer)),
                "order_date"     => '',
                "shipping_date"  => '',
                "nama_pelanggan" => '',
                "sum_amount"     => '',
                "status"         => '',
                "is_total"       => true,
            ]);
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataSalesOrderInvoice['totalData'],
            "recordsFiltered" => $dataSalesOrderInvoice['totalFilteredData'],
            "data"            => $dataAllSalesOrderInvoice,
            "payload"         => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function printPDF($tglAwal, $tglAkhir, $filter, $filter_status, $search)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $dompdf = new Dompdf();

        $condition = [
            "sales_order.deletedAt"        => null,
            "sales_order.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search"          => $search == "all" ? null : $search,
            "sort"            => "createdAt",
            "sortType"        => "desc",
            "filter_customer" => $filter == "all" ? null : $filter,
            "filter_status"   => $filter_status == "all" ? null : $filter_status,
            "dateStart"       => $tglAwal != "all" ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal))) : "",
            "dateEnd"         => $tglAkhir != "now" ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir))) : "",
            "dateStartShip"   => $this->request->getGet("dateStartShip")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStartShip"))))
                : "",
            "dateEndShip"     => $this->request->getGet("dateEndShip")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEndShip"))))
                : "",
        ];

        // Ambil data dari model
        $dataSalesOrder = $this->salesOrderLokal
            ->getLaporanPesananPenjualanPerPelanggan($condition, $addCondition, null, null);

        $dataAllSalesOrder = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = 1;

        foreach ($dataSalesOrder['data'] as $data) {
            if ($currentCustomer !== $data->nama_pelanggan) {
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrder, [
                        "no"             => '',
                        "id"             => '',
                        "no_sales_order" => number_format(floatval($totalPerCustomer)),
                        "order_date"     => '',
                        "shipping_date"  => '',
                        "nama_pelanggan" => '',
                        "sum_amount"     => '',
                        "status"         => '',
                        "is_total"       => true,
                    ]);
                }

                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;

                array_push($dataAllSalesOrder, [
                    "no"             => '',
                    "id"             => '',
                    "no_sales_order" => $data->nama_pelanggan,
                    "order_date"     => '',
                    "shipping_date"  => '',
                    "nama_pelanggan" => '',
                    "sum_amount"     => '',
                    "status"         => '',
                    "is_customer"    => true,
                ]);
            }

            array_push($dataAllSalesOrder, [
                "no"             => $no++,
                "id"             => encrypt($data->id),
                "no_sales_order" => $data->no_sales_order,
                "order_date"     => $data->order_date,
                "shipping_date"  => $data->shipping_date,
                "nama_pelanggan" => $data->nama_pelanggan,
                "sum_amount"     => number_format(floatval($data->sum_amount)),
                "status"         => !$data->surat_jalan_so_id && !$data->sales_order_invoice_id ? 'Mengantri' : 'Selesai',
            ]);

            $totalPerCustomer += floatval($data->sum_amount);
        }

        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrder, [
                "no"             => '',
                "id"             => '',
                "no_sales_order" => number_format(floatval($totalPerCustomer)),
                "order_date"     => '',
                "shipping_date"  => '',
                "nama_pelanggan" => '',
                "sum_amount"     => '',
                "status"         => '',
                "is_total"       => true,
            ]);
        }

        $data = [
            "data" => $dataAllSalesOrder,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
            "dateStartShip" => $addCondition['dateStartShip'] ? date("d/m/Y", strtotime($addCondition['dateStartShip'])) : "All",
            "dateEndShip" => $addCondition['dateEndShip'] ? date("d/m/Y", strtotime($addCondition['dateEndShip'])) : "Now",
            "filter_customer" => $filter != "all" ? $this->customerModel->find($filter)->name : "All",
            "filter_status" => $addCondition['filter_status'] ?: "All",
        ];

        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPesananPerPelanggan/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Pesanan Penjualan Per Pelanggan", array("Attachment" => false));

        exit(0);
    }

    public function printExcel($tglAwal, $tglAkhir, $filter, $filter_status, $search)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $condition = [
            "sales_order.deletedAt"        => null,
            "sales_order.tipe_sales_order" => 'LOKAL'
        ];

        $addCondition = [
            "search"          => $search == "all" ? null : $search,
            "sort"            => "createdAt",
            "sortType"        => "desc",
            "filter_customer" => $filter == "all" ? null : $filter,
            "filter_status"   => $filter_status == "all" ? null : $filter_status,
            "dateStart"       => $tglAwal != "all" ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal))) : "",
            "dateEnd"         => $tglAkhir != "now" ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir))) : "",
            "dateStartShip"   => $this->request->getGet("dateStartShip")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStartShip"))))
                : "",
            "dateEndShip"     => $this->request->getGet("dateEndShip")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEndShip"))))
                : "",
        ];

        // Ambil data dari model
        $dataSalesOrder = $this->salesOrderLokal
            ->getLaporanPesananPenjualanPerPelanggan($condition, $addCondition, null, null);

        $dataAllSalesOrder = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = 1;

        foreach ($dataSalesOrder['data'] as $data) {
            if ($currentCustomer !== $data->nama_pelanggan) {
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrder, [
                        "no"             => '',
                        "no_sales_order" => 'Total: Rp ' . number_format(floatval($totalPerCustomer)),
                        "order_date"     => '',
                        "shipping_date"  => '',
                        "sum_amount"     => '',
                        "status"         => '',
                        "is_total"       => true,
                    ]);
                }

                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;

                array_push($dataAllSalesOrder, [
                    "no"             => '',
                    "no_sales_order" => $data->nama_pelanggan,
                    "order_date"     => '',
                    "shipping_date"  => '',
                    "sum_amount"     => '',
                    "status"         => '',
                    "is_customer"    => true,
                ]);
            }

            array_push($dataAllSalesOrder, [
                "no"             => $no++,
                "no_sales_order" => $data->no_sales_order,
                "order_date"     => $data->order_date,
                "shipping_date"  => $data->shipping_date,
                "sum_amount"     => number_format(floatval($data->sum_amount)),
                "status"         => !$data->surat_jalan_so_id && !$data->sales_order_invoice_id ? 'Mengantri' : 'Selesai',
            ]);

            $totalPerCustomer += floatval($data->sum_amount);
        }

        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrder, [
                "no"             => '',
                "no_sales_order" => 'Total: Rp ' . number_format(floatval($totalPerCustomer)),
                "order_date"     => '',
                "shipping_date"  => '',
                "sum_amount"     => '',
                "status"         => '',
                "is_total"       => true,
            ]);
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'TOBA FISH');
        $statusText = '';
        if ($filter_status != '') {
            $statusText = $filter_status == 'belum' ? 'Belum Proses' : 'Selesai';
        }

        $sheet->setCellValue('A2', "Pesanan Penjualan per Pelanggan ($statusText)");
        $sheet->setCellValue('A3', 'Periode: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');

        // Set style for header
        $sheet->getStyle('A1:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:G3')->getFont()->setBold(true);

        // Set column headers
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'No. Pesanan');
        $sheet->setCellValue('C5', 'Tanggal Pesan');
        $sheet->setCellValue('D5', 'Tanggal Pengiriman');
        $sheet->setCellValue('E5', 'Jumlah');
        $sheet->setCellValue('F5', 'Status');

        // Set style for column headers
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']]
        ];
        $sheet->getStyle('A5:F5')->applyFromArray($headerStyle);

        // Add data
        $row = 6;
        foreach ($dataAllSalesOrder as $item) {
            if (isset($item['is_customer']) && $item['is_customer']) {
                // Customer row - bold and merged
                $sheet->setCellValue('A' . $row, $item['no_sales_order']);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE6E6E6');
            } elseif (isset($item['is_total']) && $item['is_total']) {
                // Total row - bold and with background
                $sheet->setCellValue('A' . $row, $item['no_sales_order']);
                $sheet->mergeCells('A' . $row . ':E' . $row);
                $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
            } else {
                // Regular data row
                $sheet->setCellValue('A' . $row, $item['no']);
                $sheet->setCellValue('B' . $row, $item['no_sales_order']);
                $sheet->setCellValue('C' . $row, $item['order_date']);
                $sheet->setCellValue('D' . $row, $item['shipping_date']);
                $sheet->setCellValue('E' . $row, $item['sum_amount']);
                $sheet->setCellValue('F' . $row, $item['status']);
            }
            $row++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        // Set borders for data
        $lastRow = $row - 1;
        $sheet->getStyle('A5:F' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Set alignment for numeric columns
        $sheet->getStyle('E6:E' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Set filename and headers for download
        $filename = "Laporan_Pesanan_Penjualan_Per_Pelanggan_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
