<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class Penjualan extends BaseController
{
    protected $this_company_id;
    protected $customerModel;
    protected $transaksiPembelianModel;
    protected $metadataModel;
    protected $kursModel;
    protected $rMImportPODetailModel;
    protected $rMPurchaseOrderDetailModel;
    protected $aMPurchaseOrderDetailModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $SalesOrderInvoiceModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->customerModel = new CustomerModel();
        $this->SalesOrderInvoiceModel = new SalesOrderInvoiceModel();
    }
    public function index()
    {
        $customerData = $this->customerModel->asObject()->where('company_id', $this->this_company_id)->findAll();
        $data = [
            'customer' => $customerData
        ];
        return view('Laporan/LaporanPenjualan/index', $data);
    }
    public function allTransaksi()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = ['sales_order_invoice.deletedAt' => null, 'sales_order_invoice.id_company' => $this->this_company_id];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        // $res = $this->transaksiPembelianModel->getList($condition, $addCondition, $limit, $offset);
        $res = $this->SalesOrderInvoiceModel->getAllSalesOrderInvoiceReport($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $exchangeTransaksi = 1.00;
            $nominalTransaksi = $data->total_invoice;
            $nominalTransaksiIdr = $nominalTransaksi * $exchangeTransaksi;
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "po_date"               => $data->tanggal_faktur,
                "dokumen_num"           => "-",
                "evidance_num"          => $data->document_no != null ? implode(",", json_decode($data->document_no)) : "",
                "invoice_num"           => $data->no_faktur,
                "invoice_date"          => $data->tanggal_faktur,
                "tax_invoice"           => $data->ppn,
                "po_num"                => $data->document_no != null ? implode(",", json_decode($data->document_no)) : "",
                "supplier_name"         => $data->kode_pelanggan . " - " . $data->nama_pelanggan,
                "valas"                 => "IDR",
                "exchange"              => (floatval($exchangeTransaksi)),
                "nominal"               => (floatval($nominalTransaksi)),
                "nominal_idr"           => (floatval($nominalTransaksiIdr)),
            ]);

            // var_dump($rdata);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }

    public function LaporanPenjualanPrint($tglAwal, $tglAkhir, $filter, $search)
    {
        $dompdf = new Dompdf();
        $condition = ['sales_order_invoice.deletedAt' => null, 'sales_order_invoice.id_company' => $this->this_company_id];

        $addCondition = [
            "search"        => $search != "all" ? $search : "",
            "filter"        => $filter != "all" ? $filter : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate" => $tglAwal != "all" ? $tglAwal : "",
            "lastdate" => $tglAkhir != "now" ? $tglAkhir : "",
        ];

        $res = $this->SalesOrderInvoiceModel->getAllSalesOrderInvoiceReport($condition, $addCondition, 100000000, 0);
        $rdata = [];

        $no = 1;

        foreach ($res['data'] as $data) {
            $exchangeTransaksi = 1.00;
            $nominalTransaksi = $data->total_invoice;
            $nominalTransaksiIdr = $nominalTransaksi * $exchangeTransaksi;
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "po_date"               => $data->tanggal_faktur,
                "dokumen_num"           => "-",
                "evidance_num"          => $data->document_no != null ? implode(",", json_decode($data->document_no)) : "",
                "invoice_num"           => $data->no_faktur,
                "invoice_date"          => $data->tanggal_faktur,
                "tax_invoice"           => (floatval($data->ppn)),
                "po_num"                => $data->document_no != null ? implode(",", json_decode($data->document_no)) : "",
                "supplier_name"         => $data->kode_pelanggan . " - " . $data->nama_pelanggan,
                "valas"                 => "IDR",
                "exchange"              => (floatval($exchangeTransaksi)),
                "nominal"               => (floatval($nominalTransaksi)),
                "nominal_idr"           => (floatval($nominalTransaksiIdr)),
            ]);
        }


        $data = [
            "data"              => $rdata,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" =>  $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];
        $dompdf->loadHtml(view('Laporan/LaporanPenjualan/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Pembelian ", array("Attachment" => false));

        exit(0);
    }

    public function exportExcel($tglAwal, $tglAkhir, $filter, $search)
    {

        $spreadsheet = new Spreadsheet();


        $condition = ['sales_order_invoice.deletedAt' => null, 'sales_order_invoice.id_company' => $this->this_company_id];

        $addCondition = [
            "search"        => $search != "all" ? $search : "",
            "filter"        => $filter != "all" ? $filter : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate" => $tglAwal != "all" ? $tglAwal : "",
            "lastdate" => $tglAkhir != "now" ? $tglAkhir : "",
        ];


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Transaction Date')
            ->setCellValue('C1', 'Document')
            ->setCellValue('D1', 'Evidance Num')
            ->setCellValue('E1', 'Invoice')
            ->setCellValue('F1', 'Invoice Date')
            ->setCellValue('G1', 'Tax Invoice')
            ->setCellValue('H1', 'SO Num')
            ->setCellValue('I1', 'Buyer')
            ->setCellValue('J1', 'Valas')
            ->setCellValue('K1', 'Exchange Rate')
            ->setCellValue('L1', 'Nominal Value')
            ->setCellValue('M1', 'Nominal Value(IDR)');

        $res = $this->SalesOrderInvoiceModel->getAllSalesOrderInvoiceReport($condition, $addCondition, 100000000, 0);
        $rdata = [];

        $no = 1;

        foreach ($res['data'] as $data) {
            $exchangeTransaksi = 1.00;
            $nominalTransaksi = $data->total_invoice;
            $nominalTransaksiIdr = $nominalTransaksi * $exchangeTransaksi;
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "po_date"               => $data->tanggal_faktur,
                "dokumen_num"           => "-",
                "evidance_num"          => $data->document_no != null ? implode(",", json_decode($data->document_no)) : "",
                "invoice_num"           => $data->no_faktur,
                "invoice_date"          => $data->tanggal_faktur,
                "tax_invoice"           => (floatval($data->ppn)),
                "po_num"                => $data->document_no != null ? implode(",", json_decode($data->document_no)) : "",
                "supplier_name"         => $data->kode_pelanggan . " - " . $data->nama_pelanggan,
                "valas"                 => "IDR",
                "exchange"              => (floatval($exchangeTransaksi)),
                "nominal"               => (floatval($nominalTransaksi)),
                "nominal_idr"           => (floatval($nominalTransaksiIdr)),
            ]);
        }

        $no = 1;
        $column = 2;
        foreach ($rdata as $data) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column, $data['po_date'])
                ->setCellValue('C' . $column, $data['dokumen_num'])
                ->setCellValue('D' . $column, $data['evidance_num'])
                ->setCellValue('E' . $column, $data['invoice_num'])
                ->setCellValue('F' . $column, $data['invoice_date'])
                ->setCellValue('G' . $column, $data['tax_invoice'])
                ->setCellValue('H' . $column, $data['po_num'])
                ->setCellValue('I' . $column, $data['supplier_name'])
                ->setCellValue('J' . $column, $data['valas'])
                ->setCellValue('K' . $column, number_format(floatval($data['exchange'])))
                ->setCellValue('L' . $column, number_format(floatval($data['nominal'])))
                ->setCellValue('M' . $column, number_format(floatval($data['nominal_idr'])));
            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Penjualan';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
