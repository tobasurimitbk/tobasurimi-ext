<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesInvoiceList extends BaseController
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
        return view('Laporan/LaporanSales/LaporanSalesInvoiceList/index', $data);
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
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice_detail.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer" => $this->request->getGet("filter"),
            "filter_company" => $this->request->getGet("filter_company") ?? null,
            "filter_paid" => $this->request->getGet("filter_paid") ?? null,
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokal($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderInvoice = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $tanggalFaktur = new \DateTime($data->tanggal_faktur);
            $tanggalJatuhTempo = new \DateTime($data->tanggal_jatuh_tempo);

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $tanggalFaktur->format('d/m/Y'),
                "tanggal_jatuh_tempo" => $data->termin == "COD" ? $tanggalFaktur->format('d/m/Y') : $tanggalJatuhTempo->format('d/m/Y'),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "total_invoice" => number_format((float)$data->sum_amount_invoice),
                "pay_amount" => number_format((float)$data->pay_amount),
                "keterangan" => $data->keterangan,
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

    public function LaporanPenjualanPrint($tglAwal, $tglAkhir, $filter, $search)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $dompdf = new Dompdf();

        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice_detail.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search == "all" ? null : $search,
            "sort" => $this->request->getGet("sort") ?? "tanggal_jatuh_tempo",
            "sortType" => $this->request->getGet("sortType") ?? "asc",
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer" => $filter == "all" ? null : $filter,
            "filter_company" => $this->request->getGet("filter_company") ?? null,
            "filter_paid" => $this->request->getGet("filter_paid") ?? null,
            "dateStart" => $tglAwal ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal))) : "",
            "dateEnd" => $tglAkhir ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir))) : "",
        ];

        // Fetch sales order invoice data
        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokal($condition, $addCondition, null, null);

        $dataAllSalesOrderInvoice = [];
        $no = 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $tanggalFaktur = new \DateTime($data->tanggal_faktur);
            $tanggalJatuhTempo = new \DateTime($data->tanggal_jatuh_tempo);

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $tanggalFaktur->format('d/m/Y'),
                "tanggal_jatuh_tempo" => $data->termin == "COD" ? $tanggalFaktur->format('d/m/Y') : $tanggalJatuhTempo->format('d/m/Y'),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "total_invoice" => number_format((float)$data->sum_amount_invoice),
                "pay_amount" => number_format((float)$data->pay_amount),
                "keterangan" => $data->keterangan,
            ]);
        }

        $data = [
            "data" => $dataAllSalesOrderInvoice,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanSalesInvoiceList/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Sales Invoices List ", array("Attachment" => false));

        exit(0);
    }

    public function exportExcel($tglAwal, $tglAkhir, $filter, $search)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        $condition = [
            // "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice_detail.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $search == "all" ? null : $search,
            "sort" => $this->request->getGet("sort") ?? "tanggal_jatuh_tempo",
            "sortType" => $this->request->getGet("sortType") ?? "asc",
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer" => $filter == "all" ? "" : $filter,
            "filter_company" => $this->request->getGet("filter_company") ?? null,
            "filter_paid" => $this->request->getGet("filter_paid") ?? null,
            "dateStart" => $tglAwal ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal))) : "",
            "dateEnd" => $tglAkhir ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir))) : "",
        ];

        // Fetch sales order invoice data
        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokal($condition, $addCondition, null, null);

        $dataAllSalesOrderInvoice = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $totalHPPPerCustomer = 0;
        $totalLabaPerCustomer = 0;
        $no = 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            $tanggalFaktur = new \DateTime($data->tanggal_faktur);
            $tanggalJatuhTempo = new \DateTime($data->tanggal_jatuh_tempo);

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $tanggalFaktur->format('d/m/Y'),
                "tanggal_jatuh_tempo" => $data->termin == "COD" ? $tanggalFaktur->format('d/m/Y') : $tanggalJatuhTempo->format('d/m/Y'),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "total_invoice" => number_format((float)$data->sum_amount_invoice),
                "pay_amount" => number_format((float)$data->pay_amount),
                "keterangan" => $data->keterangan,
            ]);
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Your System")
            ->setLastModifiedBy("Your System")
            ->setTitle("Sales Invoices List")
            ->setSubject("Sales Invoices List")
            ->setDescription("Sales Invoices List")
            ->setKeywords("laporan sales invoices list")
            ->setCategory("Laporan");

        // Set header
        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->setCellValue('A2', 'LAPORAN SALES INVOICES LIST');
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
        $sheet->setCellValue('D5', 'Tanggal Jatuh Tempo');
        $sheet->setCellValue('E5', 'Nama Pelanggan');
        $sheet->setCellValue('F5', 'Nama Penjual');
        $sheet->setCellValue('G5', 'Total Invoice');
        $sheet->setCellValue('H5', 'Total Belum Dibayar');
        $sheet->setCellValue('I5', 'Keterangan');

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
            $sheet->setCellValue('A' . $row, $item['no']);
            $sheet->setCellValue('B' . $row, $item['no_faktur']);
            $sheet->setCellValue('C' . $row, $item['tanggal_faktur']);
            $sheet->setCellValue('D' . $row, $item['tanggal_jatuh_tempo']);
            $sheet->setCellValue('E' . $row, $item['nama_pelanggan']);
            $sheet->setCellValue('F' . $row, $item['nama_sales']);
            $sheet->setCellValue('G' . $row, $item['total_invoice']);
            $sheet->setCellValue('H' . $row, $item['pay_amount']);
            $sheet->setCellValue('I' . $row, $item['keterangan']);
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
        $sheet->getStyle('G6:H' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Set filename and headers for download
        $filename = "Laporan_Sales_Invoices_List_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
