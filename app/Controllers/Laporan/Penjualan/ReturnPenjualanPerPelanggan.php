<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderReturnModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReturnPenjualanPerPelanggan extends BaseController
{
    protected $this_company_id;
    protected $customerModel;
    protected $salesOrderReturnModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->customerModel = new CustomerModel();
        $this->salesOrderReturnModel = new SalesOrderReturnModel();
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
        return view('Laporan/LaporanSales/LaporanReturnPerPelanggan/index', $data);
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
            "sales_order_return.deletedAt" => null,
            "sales_order_return_detail.deletedAt" => null
        ];

        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_customer" => $this->request->getGet("filter"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderReturn = $this->salesOrderReturnModel
            ->getAllSalesOrderReturnLokal($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderInvoice = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrderReturn['data'] as $data) {
            if ($currentCustomer !== $data->nama_pelanggan) {
                // Push the total row for the previous customer, if applicable
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no" => '',
                        "id" => '',
                        "no_return" => 'Total Return: Rp ' . number_format(floatval($totalPerCustomer)),
                        "no_dokumen" => '',
                        "tanggal_return" => '',
                        "note" => '',
                        "sum_amount_return" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                    ]);
                }

                // Reset for the new customer
                $currentCustomer = $data->nama_pelanggan;

                // Add a row for the customer's name
                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "no_return" => $data->nama_pelanggan,
                    "no_dokumen" => '',
                    "tanggal_return" => '',
                    "note" => '',
                    "sum_amount_return" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                ]);
            }

            // Add the regular invoice data
            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_return" => $data->no_return,
                "no_dokumen" => $data->no_dokumen,
                "tanggal_return" => $data->tanggal_return,
                "note" => $data->note,
                "sum_amount_return" => number_format(floatval($data->sum_amount_return)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => '',
            ]);

            // Accumulate the totals per customer
            $totalPerCustomer += floatval($data->sum_amount_return);
        }

        // Add the total for the last customer
        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => '',
                "id" => '',
                "no_return" => 'Total Return: Rp ' . number_format(floatval($totalPerCustomer)),
                "no_dokumen" => '',
                "tanggal_return" => '',
                "note" => '',
                "sum_amount_return" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
            ]);
        }

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $dataSalesOrderReturn['totalData'],
            "recordsFiltered" => $dataSalesOrderReturn['totalFilteredData'],
            "data" => $dataAllSalesOrderInvoice,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function LaporanPenjualanPrint($tglAwal, $tglAkhir, $filter, $search)
    {
        $dompdf = new Dompdf();

        $condition = [
            // "sales_order_return.id_company" => $this->this_company_id,
            "sales_order_return.deletedAt" => null,
            "sales_order_return_detail.deletedAt" => null
        ];

        $addCondition = [
            "search" => $search == "all" ? null : $search,
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer" => $filter == "all" ? null : $filter,
            "dateStart" => $tglAwal ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal))) : "",
            "dateEnd" => $tglAkhir ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir))) : "",
        ];

        // Fetch sales order invoice data
        $dataSalesOrderReturn = $this->salesOrderReturnModel
            ->getAllSalesOrderInvoiceLokal($condition, $addCondition, null, null);

        $dataAllSalesOrderInvoice = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $totalHPPPerCustomer = 0;
        $totalLabaPerCustomer = 0;
        $no = 1;

        foreach ($dataSalesOrderReturn['data'] as $data) {
            // Check if the customer has changed
            if ($currentCustomer !== $data->nama_pelanggan) {
                // If there's a previous customer, push their total row
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no" => '',
                        "id" => '',
                        "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerCustomer)),
                        "tanggal_faktur" => '',
                        "keterangan" => '',
                        "total_invoice" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "amt_harga_pokok" => '',
                        "amt_laba" => '',
                        "is_total" => true,
                    ]);
                }

                // Reset totals for the new customer
                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;
                $totalHPPPerCustomer = 0;
                $totalLabaPerCustomer = 0;

                // Add a row for the new customer's name
                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "no_faktur" => $data->nama_pelanggan,
                    "tanggal_faktur" => '',
                    "keterangan" => '',
                    "total_invoice" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "amt_harga_pokok" => '',
                    "amt_laba" => '',
                    "is_customer" => true,
                ]);
            }

            // Calculate laba
            $laba = floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);

            // Add the regular invoice data for this customer
            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "total_invoice" => number_format(floatval($data->sum_amount_invoice)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "amt_harga_pokok" => number_format(floatval($data->amt_harga_pokok)),
                "amt_laba" => number_format($laba),
            ]);

            // Accumulate the totals for this customer
            $totalPerCustomer += floatval($data->sum_amount_invoice);
            $totalHPPPerCustomer += floatval($data->amt_harga_pokok);
            $totalLabaPerCustomer += $laba;
        }

        // After looping through all data, push the total row for the last customer
        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => '',
                "id" => '',
                "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerCustomer)),
                "tanggal_faktur" => '',
                "keterangan" => '',
                "total_invoice" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "amt_harga_pokok" => 'Total HPP: Rp ' . number_format(floatval($totalHPPPerCustomer)),
                "amt_laba" => 'Total Laba: Rp ' . number_format(floatval($totalLabaPerCustomer)),
                "is_total" => true,
            ]);
        }

        $data = [
            "data" => $dataAllSalesOrderInvoice,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanReturnPerPelanggan/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Rincian Penjualan Per Pelanggan ", array("Attachment" => false));

        exit(0);
    }

    public function exportExcel($tglAwal, $tglAkhir, $filter, $search)
    {
        $condition = [
            // "sales_order_return.id_company" => $this->this_company_id,
            "sales_order_return.deletedAt" => null,
            "sales_order_return_detail.deletedAt" => null
        ];

        $addCondition = [
            "search" => $search == "all" ? null : $search,
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer" => $filter == "all" ? "" : $filter,
            "dateStart" => $tglAwal ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal))) : "",
            "dateEnd" => $tglAkhir ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir))) : "",
        ];

        // Fetch sales order invoice data
        $dataSalesOrderReturn = $this->salesOrderReturnModel
            ->getAllSalesOrderInvoiceLokal($condition, $addCondition, null, null);

        $dataAllSalesOrderInvoice = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $totalHPPPerCustomer = 0;
        $totalLabaPerCustomer = 0;
        $no = 1;

        foreach ($dataSalesOrderReturn['data'] as $data) {
            // Check if the customer has changed
            if ($currentCustomer !== $data->nama_pelanggan) {
                // If there's a previous customer, push their total row
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no" => '',
                        "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerCustomer)),
                        "tanggal_faktur" => '',
                        "keterangan" => '',
                        "total_invoice" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "amt_harga_pokok" => '',
                        "amt_laba" => '',
                        "is_total" => true,
                    ]);
                }

                // Reset totals for the new customer
                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;
                $totalHPPPerCustomer = 0;
                $totalLabaPerCustomer = 0;

                // Add a row for the new customer's name
                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "no_faktur" => $data->nama_pelanggan,
                    "tanggal_faktur" => '',
                    "keterangan" => '',
                    "total_invoice" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "amt_harga_pokok" => '',
                    "amt_laba" => '',
                    "is_customer" => true,
                ]);
            }

            // Calculate laba
            $laba = floatval($data->sum_amount_invoice) - floatval($data->amt_harga_pokok);

            // Add the regular invoice data for this customer
            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "total_invoice" => number_format(floatval($data->sum_amount_invoice)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "amt_harga_pokok" => number_format(floatval($data->amt_harga_pokok)),
                "amt_laba" => number_format($laba),
            ]);

            // Accumulate the totals for this customer
            $totalPerCustomer += floatval($data->sum_amount_invoice);
            $totalHPPPerCustomer += floatval($data->amt_harga_pokok);
            $totalLabaPerCustomer += $laba;
        }

        // After looping through all data, push the total row for the last customer
        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => '',
                "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerCustomer)),
                "tanggal_faktur" => '',
                "keterangan" => '',
                "total_invoice" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "amt_harga_pokok" => '',
                "amt_laba" => '',
                "is_total" => true,
            ]);
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Your System")
            ->setLastModifiedBy("Your System")
            ->setTitle("Rincian Penjualan Per Pelanggan")
            ->setSubject("Rincian Penjualan Per Pelanggan")
            ->setDescription("Rincian Penjualan Per Pelanggan")
            ->setKeywords("laporan penjualan pelanggan")
            ->setCategory("Laporan");

        // Set header
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->setCellValue('A2', 'LAPORAN RINCIAN PENJUALAN PER PELANGGAN');
        $sheet->setCellValue('A3', 'Periode: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        // Set style for header
        $sheet->getStyle('A1:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:H3')->getFont()->setBold(true);

        // Set column headers
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'No. Faktur');
        $sheet->setCellValue('C5', 'Tanggal Faktur');
        $sheet->setCellValue('D5', 'Keterangan');
        $sheet->setCellValue('E5', 'Jumlah');
        $sheet->setCellValue('F5', 'Nilai HPP');
        $sheet->setCellValue('G5', 'Laba Kotor');
        $sheet->setCellValue('H5', 'Nama Pelanggan');
        $sheet->setCellValue('I5', 'Nama Penjual');

        // Set style for column headers
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']]
        ];
        $sheet->getStyle('A5:I5')->applyFromArray($headerStyle);

        // Add data
        $row = 6;
        foreach ($dataAllSalesOrderInvoice as $item) {
            if (isset($item['is_customer']) && $item['is_customer']) {
                // Customer row - bold and merged
                $sheet->setCellValue('A' . $row, $item['no_faktur']);
                $sheet->mergeCells('A' . $row . ':I' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE6E6E6');
            } elseif (isset($item['is_total']) && $item['is_total']) {
                // Total row - bold and with background
                $sheet->setCellValue('A' . $row, $item['no_faktur']);
                $sheet->setCellValue('F' . $row, $item['amt_harga_pokok']);
                $sheet->setCellValue('G' . $row, $item['amt_laba']);

                $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
            } else {
                // Regular data row
                $sheet->setCellValue('A' . $row, $item['no']);
                $sheet->setCellValue('B' . $row, $item['no_faktur']);
                $sheet->setCellValue('C' . $row, $item['tanggal_faktur']);
                $sheet->setCellValue('D' . $row, $item['keterangan']);
                $sheet->setCellValue('E' . $row, $item['total_invoice']);
                $sheet->setCellValue('F' . $row, $item['amt_harga_pokok']);
                $sheet->setCellValue('G' . $row, $item['amt_laba']);
                $sheet->setCellValue('H' . $row, $item['nama_pelanggan']);
                $sheet->setCellValue('I' . $row, $item['nama_sales']);
            }
            $row++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(20);

        // Set borders for data
        $lastRow = $row - 1;
        $sheet->getStyle('A5:I' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Set alignment for numeric columns
        $sheet->getStyle('E6:G' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Set filename and headers for download
        $filename = "Laporan_Rincian_Penjualan_Per_Pelanggan_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
