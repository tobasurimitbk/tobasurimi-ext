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
        return view('Laporan/LaporanHutang/detail', $data);
    }

    public function allHutang()
    {
        $rawFilter = $this->request->getGet("filter");
        $filter = [];
        $type_barang   = $this->request->getGet("type_barang");

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

        // dd($this->this_company_id);

        $condition = [
            "suppliers.deletedAt" => NULL
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

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $allData = [];

        if ($type_barang == "BAHAN BAKU LOKAL") {
            $condition['rm_purchase_orders.deletedAt'] = NULL;
            // var_dump($condition, $addCondition, $limit, $offset);
            // exit;
            $allData = $this->rMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        } else if ($type_barang == "BAHAN PENOLONG LOKAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Lokal";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        } else if ($type_barang == "BAHAN BAKU INTERNASIONAL") {
            $condition['rm_import_pos.deletedAt'] = NULL;
            $allData = $this->rMImportPOModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        } else if ($type_barang == "BAHAN PENOLONG INTERNASIONAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Import";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        }

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $rdata = [];
        foreach ($allData['data'] as $d) {
            $totalRemaining = $d->sum_total - $d->sum_remaining;
            $rdata[] = [
                'no' => $no++,
                'id' => $d->id,
                'supplier' => $d->supplier_name,
                'no_penerimaan_barang' => $d->list_no_penerimaan_barang,
                'nominal_idr' => $d->sum_total,
                'remaining_idr' => $totalRemaining,
            ];
        }

        return response()->setJSON([
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $allData['totalData'],
            "recordsFiltered" => $allData['totalFilteredData'],
            "data" => $rdata,
            "payload" => $payload,
        ]);
    }

    public function allDetailsInvoice($id)
    {
        $rawFilter = $this->request->getGet("filter") == "all" ? "" : $this->request->getGet("filter");

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        } else {
            $filter = "";
        }

        if ($id) {
            $filterId = explode(',', $id);
        } else {
            $filterId = "";
        }

        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter ?? $filterId,
            "divisi"        => $this->request->getGet("filter_divisi") == "all" ? "" : $this->request->getGet("filter_divisi"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            // "suppliers.id"  => $id,
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => !empty($filter) ? $filter : $filterId,
            "divisi"        => $this->request->getGet("filter_divisi") == "all" ? "" : $this->request->getGet("filter_divisi"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
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

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $checkSupplier = $this->supplierModel->find($id);
        $type_barang   = $this->request->getGet("tipe_barang");

        if ($type_barang == "BAHAN BAKU LOKAL") {
            $condition['rm_purchase_orders.deletedAt'] = NULL;
            $res = $this->rMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        } else if ($type_barang == "BAHAN PENOLONG LOKAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Lokal";
            $res = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        } else if ($type_barang == "BAHAN BAKU INTERNASIONAL") {
            $condition['rm_import_pos.deletedAt'] = NULL;
            $res = $this->rMImportPOModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        } else if ($type_barang == "BAHAN PENOLONG INTERNASIONAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Import";
            $res = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, $limit, $offset);
        }

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $totalRemaining = $data->sum_total - $data->sum_remaining;
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "tanggal_invoice"       => date("d/m/Y", strtotime($data->tanggal_invoice)),
                "no_invoice"            => $data->no_invoice,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "divisi_invoice"        => $data->divisi,
                "nominal_invoice"       => number_format($data->sum_total, 2, '.', ''),
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
            "suppliers.deletedAt" => NULL
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

        if ($type_barang == "BAHAN BAKU LOKAL") {
            $condition['rm_purchase_orders.deletedAt'] = NULL;
            // var_dump($condition, $addCondition);
            // exit;
            $allData = $this->rMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN PENOLONG LOKAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Lokal";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN BAKU INTERNASIONAL") {
            $condition['rm_import_pos.deletedAt'] = NULL;
            $allData = $this->rMImportPOModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN PENOLONG INTERNASIONAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Import";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        }

        $data = [
            'data' => $allData['data'],
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
            "suppliers.deletedAt" => NULL
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

        if ($type_barang == "BAHAN BAKU LOKAL") {
            $condition['rm_purchase_orders.deletedAt'] = NULL;
            // var_dump($condition, $addCondition);
            // exit;
            $allData = $this->rMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN PENOLONG LOKAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Lokal";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN BAKU INTERNASIONAL") {
            $condition['rm_import_pos.deletedAt'] = NULL;
            $allData = $this->rMImportPOModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN PENOLONG INTERNASIONAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Import";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        }

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
        foreach ($allData['data'] as $item) {
            $totalRemaining = $item->sum_total - $item->sum_remaining;
            $sheet->setCellValue("A$row", $no++);
            $sheet->setCellValue("B$row", $item->list_no_penerimaan_barang);
            $sheet->setCellValue("C$row", $item->supplier_name);
            $sheet->setCellValue("D$row", $item->sum_total);
            $sheet->setCellValue("E$row", $totalRemaining);
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
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $rawFilter = $this->request->getGet("filter");
        $filter = [];
        $filterId = [];
        $type_barang   = $this->request->getGet("tipe_barang");
        $id = $this->request->getGet("supplierId");
        
        if ($rawFilter != "all") {
            $filter = explode(',', $rawFilter);
        }

        if ($id) {
            $filterId = explode(',', $id);
        }
        
        $startDate = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $endDate = $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";

        $condition = [
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => !empty($filter) ? $filter : $filterId,
            "divisi"        => $this->request->getGet("divisi"),
            "sort"          => $this->request->getGet("sort"),
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

        if ($type_barang == "BAHAN BAKU LOKAL") {
            $condition['rm_purchase_orders.deletedAt'] = NULL;
            $allData = $this->rMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN PENOLONG LOKAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Lokal";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN BAKU INTERNASIONAL") {
            $condition['rm_import_pos.deletedAt'] = NULL;
            $allData = $this->rMImportPOModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN PENOLONG INTERNASIONAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Import";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        }
        
        $data = [
            'data' => $allData['data'],
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
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ob_end_clean();
        ob_start();

        $rawFilter = $this->request->getGet("filter");
        $filter = [];
        $filterId = [];
        $type_barang   = $this->request->getGet("tipe_barang");
        $id = $this->request->getGet("supplierId");
        
        if ($rawFilter != "all") {
            $filter = explode(',', $rawFilter);
        }

        if ($id) {
            $filterId = explode(',', $id);
        }
        
        $startDate = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $endDate = $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";

        $condition = [
            "suppliers.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => !empty($filter) ? $filter : $filterId,
            "divisi"        => $this->request->getGet("divisi"),
            "sort"          => $this->request->getGet("sort"),
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

        if ($type_barang == "BAHAN BAKU LOKAL") {
            $condition['rm_purchase_orders.deletedAt'] = NULL;
            $allData = $this->rMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN PENOLONG LOKAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Lokal";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN BAKU INTERNASIONAL") {
            $condition['rm_import_pos.deletedAt'] = NULL;
            $allData = $this->rMImportPOModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
        } else if ($type_barang == "BAHAN PENOLONG INTERNASIONAL") {
            $condition['am_purchase_orders.deletedAt'] = NULL;
            $condition['am_purchase_orders.po_type'] = "Import";
            $allData = $this->aMPurchaseOrderModel->getPOByIdSupplierWithInvoice($condition, $addCondition, null, null);
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

        foreach ($allData['data'] as $item) {
            $remaining = $item->sum_total - $item->sum_remaining;

            $sheet->setCellValue("A$row", $no++);
            $sheet->setCellValue("B$row", $item->tanggal_invoice);
            $sheet->setCellValue("C$row", $item->no_invoice);
            $sheet->setCellValue("D$row", $item->no_penerimaan_barang);
            $sheet->setCellValue("E$row", $item->divisi);
            $sheet->setCellValue("F$row", $item->sum_total);
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
