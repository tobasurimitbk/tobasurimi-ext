<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Controllers\Master\Kurs;
use App\Models\DivisisModel;
use App\Models\SupplierModel;
use App\Models\TransaksiPembelianModel;
use App\Models\LocalPOPaymentModel;
use App\Models\MetadataModel;
use App\Models\KursModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\TransaksiJurnalModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class Hutang extends BaseController
{
    protected $this_company_id;
    protected $supplierModel;
    protected $transaksiPembelianModel;
    protected $metadataModel;
    protected $kursModel;
    protected $rMImportPOModel;
    protected $rMImportPODetailModel;
    protected $rMPurchaseOrderModel;
    protected $rMPurchaseOrderDetailModel;
    protected $aMPurchaseOrderModel;
    protected $aMPurchaseOrderDetailModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $transaksiJurnalModel;
    protected $divisisModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
        $this->divisisModel = new DivisisModel();
        $this->transaksiPembelianModel = new TransaksiPembelianModel();
        $this->metadataModel = new MetadataModel();
        $this->kursModel = new KursModel();
        $this->rMImportPOModel = new RMImportPOModel();
        $this->rMImportPODetailModel = new RMImportPODetailModel();
        $this->rMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->aMPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->aMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
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

        $supplierData = $this->supplierModel
            ->select('GROUP_CONCAT(suppliers.id) AS id, suppliers.name, companies.company')
            ->join('companies', 'companies.id = suppliers.company_id')
            ->whereIn('company_id', $companyId)
            ->asObject()
            ->groupBy('suppliers.name')
            ->findAll();

        $divisiData = $this->divisisModel
            ->select('divisis.id, divisis.divisi, companies.company')
            ->join('companies', 'companies.id = divisis.company_id')
            ->whereIn('company_id', $companyId)
            ->asObject()
            ->findAll();
        $data = [
            'suppliers' => $supplierData,
            'divisis' => $divisiData,
        ];
        return view('Laporan/LaporanHutang/index', $data);
    }

    public function detail($id, $tanggalAwal, $tanggalAkhir, $filter, $filterDivisi, $search)
    {
        $data = [
            'id'            => $id,
            'tanggalAwal'   => $tanggalAwal != "all" ? date("d/m/Y", strtotime($tanggalAwal)) : "",
            'tanggalAkhir'  => $tanggalAkhir != "all" ? date("d/m/Y", strtotime($tanggalAkhir)) : "",
            'filter'        => $filter,
            "filterDivisi"  => $filterDivisi,
            "search"        => $search
        ];
        return view('Laporan/LaporanHutang/detail', $data);
    }

    public function allHutang()
    {
        $rawFilter = $this->request->getGet("filter");
        $filter = [];

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        }

        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate"      => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $condition = [
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("divisi"),
            "type_barang"   => $this->request->getGet("type_barang"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate"      => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");


        $res = $this->supplierModel->getSupplierHutangList($condition, $addCondition, $limit, $offset, $companyId);

        // var_dump($res['data']);
        // exit;

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data['id'],
                "supplier"              => $data['supplier'],
                "no_penerimaan_barang"  => $data['no_penerimaan_barang'],
                "nominal_idr"           => number_format($data['nominal_idr'], 2, '.', ''),
                "remaining_idr"         => number_format($data['remaining_idr'], 2, '.', ''),
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

    public function allDetailsInvoice($id)
    {
        $rawFilter = $this->request->getGet("filter") == "all" ? "" : $this->request->getGet("filter");

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        } else {
            $filter = "";
        }
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("filter_divisi") == "all" ? "" : $this->request->getGet("filter_divisi"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            "suppliers.id"  => $id,
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("filter_divisi") == "all" ? "" : $this->request->getGet("filter_divisi"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
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
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
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

    public function printHutang()
    {
        $filterRaw = $this->request->getGet("filter");
        $filter = ($filterRaw) ? array_filter(explode(',', $filterRaw)) : [];
        $startDate = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $endDate = $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $condition = [
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("divisi"),
            "type_barang"   => $this->request->getGet("type_barang"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $startDate,
            "lastdate"      => $endDate,
        ];

        $res = $this->supplierModel->getSupplierHutangList($condition, $addCondition, null, null, $companyId);

        $data = [
            'data' => $res['data'],
            'title' => 'Laporan Hutang Supplier',
            'date_range' => ($startDate && $endDate) ? date("d/m/Y", strtotime($startDate)) . " - " . date("d/m/Y", strtotime($endDate)) : "Semua Periode"
        ];

        // Render view to HTML
        $html = view('Laporan/LaporanHutang/print_pdf', $data);

        // Dompdf setup
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan-hutang.pdf', ["Attachment" => false]);
        exit;
    }

    public function exportExcelHutang()
    {
        $filterRaw = $this->request->getGet("filter");
        $filter = ($filterRaw) ? array_filter(explode(',', $filterRaw)) : [];
        $startDate = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $endDate = $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $condition = [
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("divisi"),
            "type_barang"   => $this->request->getGet("type_barang"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $startDate,
            "lastdate"      => $endDate,
        ];

        $res = $this->supplierModel->getSupplierHutangList($condition, $addCondition, null, null, $companyId);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Hutang');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No Penerimaan Barang');
        $sheet->setCellValue('C1', 'Supplier');
        $sheet->setCellValue('D1', 'Nominal (Rp)');
        $sheet->setCellValue('E1', 'Remaining (Rp)');

        $no = 1;
        $row = 2;
        foreach ($res['data'] as $item) {
            $sheet->setCellValue("A$row", $no++);
            $sheet->setCellValue("B$row", $item['no_penerimaan_barang']);
            $sheet->setCellValue("C$row", $item['supplier']);
            $sheet->setCellValue("D$row", $item['nominal_idr']);
            $sheet->setCellValue("E$row", $item['remaining_idr']);
            $row++;
        }

        $filename = 'Laporan-Hutang.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function printHutangDetail()
    {
        $filterRaw = $this->request->getGet("filter");
        $filter = ($filterRaw) ? array_filter(explode(',', $filterRaw)) : [];
        $startDate = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $endDate = $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $condition = [
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("divisi"),
            "type_barang"   => $this->request->getGet("type_barang"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $startDate,
            "lastdate"      => $endDate,
        ];

        $res = $this->supplierModel->getSupplierHutangList($condition, $addCondition, null, null, $companyId);

        $data = [
            'data' => $res['data'],
            'title' => 'Laporan Hutang Supplier',
            'date_range' => ($startDate && $endDate) ? date("d/m/Y", strtotime($startDate)) . " - " . date("d/m/Y", strtotime($endDate)) : "Semua Periode"
        ];

        // Render view to HTML
        $html = view('Laporan/LaporanHutang/print_pdf', $data);

        // Dompdf setup
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan-hutang.pdf', ["Attachment" => false]);
        exit;
    }

    public function exportExcelHutangDetail()
    {
        $id = $this->request->getGet("supplierId");
        $rawFilter = $this->request->getGet("filter") == "all" ? "" : $this->request->getGet("filter");

        $filter = $rawFilter ? explode(',', $rawFilter) : [];

        $dateStart = $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "";
        $dateEnd = $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "";

        $condition = [
            "suppliers.id" => $id,
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("filter_divisi") == "all" ? "" : $this->request->getGet("filter_divisi"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $dateStart,
            "dateEnd"       => $dateEnd,
        ];

        $checkSupplier = $this->supplierModel->find($id);

        if ($checkSupplier['type'] == "BAHAN PENOLONG") {
            $res = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($checkSupplier['type'] == "BAHAN BAKU") {
            $res = $this->rMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else {
            $res = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
            if (!$res) {
                $res = $this->rMImportPOModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Detail Hutang Supplier');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Invoice');
        $sheet->setCellValue('C1', 'No Invoice');
        $sheet->setCellValue('D1', 'No Penerimaan Barang');
        $sheet->setCellValue('E1', 'Divisi');
        $sheet->setCellValue('F1', 'Nominal (Rp)');
        $sheet->setCellValue('G1', 'Remaining (Rp)');

        $no = 1;
        $row = 2;

        foreach ($res['data'] as $item) {
            $remaining = $item->total - $item->remaining;

            $sheet->setCellValue("A$row", $no++);
            $sheet->setCellValue("B$row", $item->tanggal_invoice);
            $sheet->setCellValue("C$row", $item->no_invoice);
            $sheet->setCellValue("D$row", $item->no_penerimaan_barang);
            $sheet->setCellValue("E$row", $item->divisi);
            $sheet->setCellValue("F$row", $item->total);
            $sheet->setCellValue("G$row", $remaining);
            $row++;
        }

        $filename = 'Detail-Hutang-Supplier.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
