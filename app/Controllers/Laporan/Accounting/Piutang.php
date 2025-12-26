<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderExportModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class Piutang extends BaseController
{
    protected $this_company_id;
    protected $customerModel;
    protected $salesOrderInvoiceModel;
    protected $salesOrderExportModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->customerModel = new CustomerModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
    }
    public function index()
    {
        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }
        
        $customerData = $this->customerModel
            ->select('GROUP_CONCAT(customers.id) AS id, customers.name ')
            ->whereIn('company_id', $companyId)
            ->asObject()
            ->groupBy('customers.name')
            ->findAll();
            
        $data = [
            'customer' => $customerData
        ];
        return view('Laporan/LaporanPiutang/index', $data);
    }

    public function allPiutang()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate"      => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            "customers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "divisi"        => $this->request->getGet("divisi"),
            "type_barang"   => $this->request->getGet("type_barang"),
            "summary"       => "summary",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"      => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];
        
        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $addCondition['companyId'] = [1, 2];
        } else if ($this->this_company_id == 15) {
            $addCondition['companyId'] = [15];
        } else {
            $addCondition['companyId'] = [16];
        }

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        if ($addCondition['type_barang'] == 'LOKAL') {
            $res = $this->salesOrderInvoiceModel->getDataInvoiceReportAccounting($condition, $addCondition, $limit, $offset);
        } else {
            $res = $this->salesOrderExportModel->getDataInvoiceReportAccounting($condition, $addCondition, $limit, $offset);
        }

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $totalRemaining = $data->sum_amount_invoice - $data->sum_harga_dibayar;
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "customer_name"         => $data->customer_name,
                "nominal_idr"           => number_format($data->sum_amount_invoice, 2, '.', ''),
                "remaining_idr"         => number_format($totalRemaining, 2, '.', ''),
            ]);
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

    public function printPiutang()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $rawFilter = $this->request->getGet("filter");
        $filter = [];
        $type_barang   = $this->request->getGet("type_barang");

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        }
        
        $startDate = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $endDate = $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";

        $condition = [
            "customers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("divisi"),
            "sort"          => $this->request->getGet("sort"),
            "summary"       => "summary",
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        if ($this->this_company_id != "16" && $this->this_company_id != "15") {
            $addCondition['companyId'] = [1, 2];
        } else if ($this->this_company_id == "15") {
            $addCondition['companyId'] = [15];
        } else if ($this->this_company_id == "16") {
            $addCondition['companyId'] = [16];
        } else {
            $addCondition['companyId'] = [];
        }

        $allData = [];

        if ($type_barang == 'LOKAL') {
            $allData = $this->salesOrderInvoiceModel->getDataInvoiceReportAccounting($condition, $addCondition, null, null);
        } else {
            $allData = $this->salesOrderExportModel->getDataInvoiceReportAccounting($condition, $addCondition, null, null);
        }

        $data = [
            'data' => $allData['data'],
            'title' => 'Laporan Piutang Customer',
            'date_range' => ($startDate && $endDate) ? date("d/m/Y", strtotime($startDate)) . " - " . date("d/m/Y", strtotime($endDate)) : "Semua Periode"
        ];

        // Render view to HTML
        $html = view('Laporan/LaporanPiutang/print_pdf', $data);

        // Dompdf setup
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan-piutang.pdf', ["Attachment" => false]);
        exit;
    }

    public function exportExcelPiutang()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $rawFilter = $this->request->getGet("filter");
        $filter = [];
        $type_barang   = $this->request->getGet("type_barang");

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        }
        
        $startDate = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $endDate = $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";

        $condition = [
            "customers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("divisi"),
            "sort"          => $this->request->getGet("sort"),
            "summary"       => "summary",
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        if ($this->this_company_id != "16" && $this->this_company_id != "15") {
            $addCondition['companyId'] = [1, 2];
        } else if ($this->this_company_id == "15") {
            $addCondition['companyId'] = [15];
        } else if ($this->this_company_id == "16") {
            $addCondition['companyId'] = [16];
        } else {
            $addCondition['companyId'] = [];
        }

        $allData = [];

        if ($type_barang == 'LOKAL') {
            $allData = $this->salesOrderInvoiceModel->getDataInvoiceReportAccounting($condition, $addCondition, null, null);
        } else {
            $allData = $this->salesOrderExportModel->getDataInvoiceReportAccounting($condition, $addCondition, null, null);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Piutang');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Customer');
        $sheet->setCellValue('C1', 'Nominal (Rp)');
        $sheet->setCellValue('D1', 'Remaining (Rp)');

        $no = 1;
        $row = 2;
        foreach ($allData['data'] as $item) {
            $totalRemaining = $item->sum_amount_invoice - $item->sum_harga_dibayar;
            $sheet->setCellValue("A$row", $no++);
            $sheet->setCellValue("B$row", $item->customer_name);
            $sheet->setCellValue("C$row", $item->sum_amount_invoice);
            $sheet->setCellValue("D$row", $totalRemaining);
            $row++;
        }

        $filename = 'Laporan-Piutang.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function detail($id, $tanggalAwal, $tanggalAkhir, $filter, $filterDivisi, $search, $tipeBarang)
    {
        $data = [
            'id'            => $id,
            'tanggalAwal'   => $tanggalAwal != "all" ? date("d/m/Y", strtotime($tanggalAwal)) : "",
            'tanggalAkhir'  => $tanggalAkhir != "all" ? date("d/m/Y", strtotime($tanggalAkhir)) : "",
            'filter'        => $filter,
            "filterDivisi"  => $filterDivisi,
            "search"        => $search,
            "tipeBarang"    => $tipeBarang,
        ];
        return view('Laporan/LaporanPiutang/detail', $data);
    }

    public function allDetailsInvoice($id)
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"      => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            "suppliers.id"  => $id,
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"      => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $checkSupplier = $this->supplierModel->find($id);

        // var_dump($condition, $addCondition, $limit, $offset);
        // exit;

        if ($checkSupplier['type'] == "BAHAN PENOLONG") {
            $res = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        } else if ($checkSupplier['type'] == "BAHAN BAKU") {
            $res = $this->rMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        } else {
            $res = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
            if (!$res) {
                $res = $this->rMImportPOModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
            }
        }

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $totalRemaining = $data->total - $data->remaining;
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "tanggal_invoice"       => $data->tanggal_invoice,
                "no_invoice"            => $data->no_invoice,
                "divisi_invoice"        => $data->divisi,
                "nominal_invoice"       => number_format($data->total, 2, '.', ''),
                "remaining_invoice"     => number_format($totalRemaining, 2, '.', ''),
            ]);
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
}
