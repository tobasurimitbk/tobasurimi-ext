<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CompaniesModel;
use App\Models\CountryModel;
use App\Models\CustomerModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\SalesKontrakDetailModel;
use App\Models\SalesKontrakModel;
use App\Models\SalesOrderExportAdditionalModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderExportRevisionModel;
use App\Models\SalesOrderExportSpecsDetailModel;
use App\Models\SalesOrderExportSpecsModel;
use App\Models\SalesOrderExportSubtitleModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use Dompdf\Dompdf;
use Exception;

class OrderForm extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $customerModel;
    protected $salesKontrakModel;
    protected $salesKontrakDetailModel;
    protected $barangMasterModel;
    protected $stokDetailModel;
    protected $countryModel;
    protected $metaDataModel;
    protected $satuanModel;
    protected $barangMasterSalesModel;
    protected $salesOrderExportModel;
    protected $salesOrderExportDetailModel;
    protected $divisiModel;
    protected $salesOrderExportSpecsModel;
    protected $salesOrderExportRevisionModel;
    protected $salesOrderExportAdditionalModel;
    protected $salesOrderExportSubtitleModel;
    protected $salesOrderExportSpecsDetailModel;
    protected $companyModel;
    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->customerModel = new CustomerModel();
        $this->salesKontrakModel = new SalesKontrakModel();
        $this->salesKontrakDetailModel = new SalesKontrakDetailModel();
        $this->stokDetailModel = new StockDetailModel();
        $this->countryModel = new CountryModel();
        $this->metaDataModel = new MetadataModel();
        $this->satuanModel = new SatuansModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $this->divisiModel = new DivisisModel();
        $this->salesOrderExportSpecsModel = new SalesOrderExportSpecsModel();
        $this->salesOrderExportRevisionModel = new SalesOrderExportRevisionModel();
        $this->salesOrderExportAdditionalModel = new SalesOrderExportAdditionalModel();
        $this->salesOrderExportSubtitleModel = new SalesOrderExportSubtitleModel();
        $this->companyModel = new CompaniesModel();
        $this->salesOrderExportSpecsDetailModel = new SalesOrderExportSpecsDetailModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('SalesInternasional/OrderForm/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "status"      => $this->request->getGet("status")
        ];
        if ($this->is_admin == '1') {

            $condition = [
                "sales_order_export.company_id"    => $this->this_company_id,
                "sales_order_export.deletedAt" => null,
            ];
        } else {
            $condition = [
                "sales_order_export.company_id"    => $this->this_company_id,
                "sales_order_export.deletedAt" => null,
                'sales_order_export.user_id' => $this->this_user_id
            ];
        }

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status"      => $this->request->getGet("status"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportModel->getList($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => encrypt($data->sales_order_export_id),
                "sales_order_export_no"     => $data->sales_order_export_no,
                "customer_po_no"            => $data->customer_po_no,
                "customer_name"             => $data->customer_name,
                "dicharge_port"             => $data->dicharge_port,
                "shipment_date"             => $data->shipment_date,
                "tanggal"                   => date('d/m/Y', strtotime($data->tanggal)),
                "status"                    => $data->status,
                "used"                      => $data->used,
                "keterangan_unpost"         => $data->keterangan_unpost,
                "jumlah_unpost"             => $data->jumlah_unpost,
                "divisi"                    => $data->divisi
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesData['totalData'],
            "recordsFiltered"   => $salesData['totalFilteredData'],
            "data"              => $dataSales,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function createView()
    {
        $dataSatuan = $this->satuanModel->findAll();
        $dataAJU = $this->metaDataModel->getBCUsed('so_internasional');
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataSalesKontrak = $this->salesKontrakModel->getSalesKontrakList();

        $data = [
            'dataSatuan' => $dataSatuan,
            "dataAJU" => $dataAJU,
            "dataDivisi" => $dataDivisi,
            "dataSalesKontrak" => $dataSalesKontrak
        ];

        return view('SalesInternasional/OrderForm/form', $data);
    }

    public function saveOrder()
    {

        $salesOrderNo = $this->request->getVar('sales_order_export_no');

        if ($salesOrderNo == "AUTO GENERATE") {
            // JIKA AUTO GENERATE MAKA GENERATE NOMOR BARU
            $salesOrderNo = $this->generateNomorSalesOrderInternasional(
                $this->request->getVar('sales_contract_id')
            );
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $salesOrderExportId = $this->salesOrderExportModel->insert([
                'sales_order_export_no' => $salesOrderNo,
                'sales_contract_id' => $this->request->getVar('sales_contract_id'),
                'company_id' => $this->this_company_id,
                'user_id' => $this->this_user_id,
                'bc_type' => $this->request->getVar('aju_document_type'),
                "tanggal" => $this->request->getVar("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
                "actualy_shipment_date" => $this->request->getVar("actualy_shipment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("actualy_shipment_date")))) : "",
                'freight' => $this->request->getVar('freight'),
                'additional' => $this->request->getVar('additional'),
                // 'divisi_id' => $this->request->getVar('divisi_id'),
                'tax_id' => $this->request->getVar('tax_id'),
                'royalty_price' => $this->request->getVar('royalty_price'),
                'rebate_price' => $this->request->getVar('rebate_price'),
                'can_deduction_price' => $this->request->getVar('can_deduction_price'),
                'estimated_freight_price' => $this->request->getVar('estimated_freight_price'),
                'others_type' => $this->request->getVar('others_type'),
                'others_price' => $this->request->getVar('others_price'),
                'commision' => $this->request->getVar('commision'),
                'palet_fumigation' => $this->request->getVar('palet_fumigation'),
                'palet_fumigation_price' => $this->request->getVar('palet_fumigation_price'),
                'additional_detail' => $this->request->getVar('additional_detail'),
                'additional_detail_type' => $this->request->getVar('additional_detail_type'),
                'additional_detail_price' => $this->request->getVar('additional_detail_price'),
                'payment_term' => $this->request->getVar('payment_term'),
                'shipment_an' => $this->request->getVar('shipment_an'),
                'consigne_docs' => $this->request->getVar('consigne_docs'),
                'notify_party' => $this->request->getVar('notify_party'),
                'additional_detail_docs' => $this->request->getVar('additional_detail_docs'),
                'product_specs' => $this->request->getVar('product_specs'),
                'processing_method' => $this->request->getVar('processing_method'),
                'packaging' => $this->request->getVar('packaging'),
                'code_stamping' => $this->request->getVar('code_stamping'),
                'loading' => $this->request->getVar('loading'),
                'foto_loading' => $this->request->getVar('foto_loading'),
                'stuffing' => $this->request->getVar('stuffing'),
                'deadline' => $this->request->getVar('deadline'),
                'container' => $this->request->getVar('container'),
                'document_required' => $this->request->getVar('document_required'),
                'status' => "NEW",
                'used' => "NOT USED",
            ]);

            // Insert Detail Specs
            foreach (json_decode($_POST['listDetailSpecs']) as $l) {
                $salesOrderExportSpecsId = $this->salesOrderExportSpecsModel->insert([
                    'sales_order_export_id' => $salesOrderExportId,
                    'size_packing' => $l->size_packing,
                ]);

                foreach ($l->grade_specs as $g) {
                    $this->salesOrderExportSpecsDetailModel->insert([
                        'sales_order_export_specs_id' => $salesOrderExportSpecsId,
                        'grade' => $g->grade,
                        'specification' => $g->specification
                    ]);
                }
            }


            // Insert Sales Order Detail Export
            $listDataSalesKontrak = json_decode($_POST['listDataSalesKontrak']);

            foreach ($listDataSalesKontrak->salesContractDetailList as $s) {
                $this->salesOrderExportSubtitleModel->insert([
                    'sales_order_export_id' => $salesOrderExportId,
                    'sales_contract_detail_id' => $s->id,
                    'divisi_id' => $s->divisi_id,
                    'brand' => $s->brand,
                    'packing' => $s->packing,
                    'species' => $s->species,
                    'specs' => $s->specs
                ]);

                foreach ($s->size_breakdown as $sb) {
                    $this->salesOrderExportDetailModel->insert([
                        'sales_order_export_id' => $salesOrderExportId,
                        'sales_contract_size_breakdown_id' => $sb->id_detail_breakdown,
                        'sales_contract_detail_id' => $s->id,
                        'satuan_id' => $sb->satuan_size_id,
                        'qty' => $sb->qty_input,
                        'harga_barang' => $sb->harga,
                        'total_harga_barang' => $sb->total_input,
                        // Konversinya
                        'satuan_convertion_id' => $sb->satuan_convertion_id,
                        'qty_convertion' => $sb->qty_convertion,
                        // Note
                        'note_size' => $sb->note_size,
                        'note_grade' => $sb->note_grade,
                        'note_packing' => $sb->note_packing,
                        'note_can' => $sb->note_can,
                        'note_case' => $sb->note_case,
                        'note_kg' => $sb->note_kg,
                        'note_lb' => $sb->note_lb,
                        'note_inner_box' => $sb->note_inner_box,
                        'note_pc' => $sb->note_pc,
                        'note_bag' => $sb->note_bag,
                        'note_persen' => $sb->note_persen,
                        'note_cup' => $sb->note_cup,
                        'note_palet' => $sb->note_palet,
                    ]);
                }
            }

            // Insert List Additonal
            foreach (json_decode($_POST['listAdditional']) as $l) {
                $this->salesOrderExportAdditionalModel->insert([
                    'sales_order_export_id' => $salesOrderExportId,
                    'additional_detail' => $l->additional_detail,
                    'additional_detail_type' => $l->additional_detail_type,
                    'additional_detail_price' => $l->additional_detail_price
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data saved",
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => "Internal server error " . $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function dropdownSalesKontrak()
    {
        $dataSalesKontrak = $this->salesKontrakModel->getSalesKontrakList();

        return response()->setJSON([
            'data' => $dataSalesKontrak,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getDetailSalesKontrak()
    {
        $salesContractId = $this->request->getVar("sales_contract_id");
        $id = decrypt($this->request->getVar('id'));

        if (empty($salesContractId)) {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => true
            ]);
        }

        if ($id == 0) {
            $id = null;
        }

        $data = $this->salesOrderExportModel->getDetailSalesKontrakInOrderForm(
            $salesContractId,
            $id
        );

        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getDetailInfoSalesKontrak()
    {
        $id = $this->request->getGet("id");
        $selectQry = "
            sales_contract.*,
            customers.name as customer_name,
            metadata.value as valuta,
            metadata.description as valuta_description
        ";
        $dataSalesKontak = $this->salesKontrakModel->select($selectQry)
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency')
            ->where('sales_contract.id', $id)
            ->first();

        if ($dataSalesKontak != null) {
            $dataSalesKontak['potongan_harga'] = floatval($dataSalesKontak['potongan_harga']);
            $dataSalesKontak['shipment_date'] = $dataSalesKontak['shipment_date'] != null ? date('d/m/Y', strtotime($dataSalesKontak['shipment_date'])) : "";
            $dataSalesKontak['due_date'] = $dataSalesKontak['due_date'] != null ? date('d/m/Y', strtotime($dataSalesKontak['due_date'])) : "";

            return response()->setJSON([
                'data' => $dataSalesKontak,
                'token' => csrf_hash(),
                'status' => true
            ]);
        } else {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    private function generateNomorSalesOrderInternasional($salesContractId)
    {
        // Ambil sales order terakhir untuk kontrak ini
        $lastSalesOrder = $this->salesOrderExportModel
            ->where('sales_contract_id', $salesContractId)
            ->where('deletedAt', null)
            ->orderBy('createdAt', 'DESC')
            ->first();

        $nextSequence = 1; // Default jika belum ada

        if ($lastSalesOrder && !empty($lastSalesOrder['sales_order_export_no'])) {
            // Split nomor terakhir berdasarkan karakter '-'
            $parts = explode('-', $lastSalesOrder['sales_order_export_no']);

            // Ambil bagian terakhir, trim whitespace dan convert ke integer
            $lastSequence = (int)trim(end($parts));

            // Increment sequence
            $nextSequence = $lastSequence + 1;
        }

        // Format nomor baru
        $formattedNumber = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        $salesContract = $this->salesKontrakModel->where('id', $salesContractId)->first();

        return $salesContract['sales_contract_no'] . " - " . $formattedNumber;
    }


    public function getById($id = null)
    {
        $id = decrypt($id);
        $dataSatuan = $this->satuanModel->findAll();
        $dataSalesExport = $this->salesOrderExportModel
            ->asObject()
            ->select('
                sales_order_export.*, 
                sales_contract.sales_contract_no,
                sales_contract.due_date,
                sales_contract.shipment_date,
                customers.name AS customer_name,
                CONCAT(metadata.value, " - ", metadata.description) AS currencyName,
                sales_contract.dicharge_port,
                sales_contract.customer_po_no,
                divisis.divisi
            ')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
            ->join('divisis', 'divisis.id = sales_order_export.divisi_id', 'left')
            ->where('sales_order_export.sales_order_export_id', $id)
            ->orderBy('sales_order_export.createdAt', "DESC")
            ->first();
        $dataSalesExportDetail =  $this->salesOrderExportModel
            ->getDetailSalesKontrakInOrderForm(
                $dataSalesExport->sales_contract_id,
                $id
            );

        $dataSalesExportSpecs = $this->salesOrderExportModel->getSalesOrderSpecs($id);
        $dataSalesKontrak = $this->salesKontrakModel
            ->where('id', $dataSalesExport->sales_contract_id)
            ->first();

        $dataSalesExportAdditional = $this->salesOrderExportAdditionalModel
            ->where('sales_order_export_id', $id)
            ->findAll();

        $dataAJU = $this->metaDataModel->getBCUsed('so_internasional');
        $dataDivisi = $this->divisiModel->getDivisiAccess();

        $data = [
            "id" => encrypt($id),
            'dataSatuan' => $dataSatuan,
            'dataSalesExport' => $dataSalesExport,
            "dataAJU" => $dataAJU,
            "dataDivisi" => $dataDivisi,
            "dataSalesExportDetail" => $dataSalesExportDetail,
            "dataSalesExportSpecs" => $dataSalesExportSpecs,
            "dataSalesKontrak" => $dataSalesKontrak,
            "dataSalesExportAdditional" => $dataSalesExportAdditional
        ];

        return view('SalesInternasional/OrderForm/form', $data);
    }

    public function update()
    {
        $id = decrypt($this->request->getPost("id"));

        $salesOrderNo = $this->request->getVar('sales_order_export_no');

        $checkNoSalesOrder = $this->salesOrderExportModel
            ->where('sales_order_export_no', $salesOrderNo)
            ->where('company_id', $this->this_company_id)
            ->where('sales_order_export_id !=', $id)
            ->first();

        if ($checkNoSalesOrder != null) {
            // NO Sales Order Already Exists
            return response()->setJSON([
                'message' => "Sales order no already exists",
                'status' => false
            ]);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $this->salesOrderExportModel->update($id, [
                'sales_order_export_no' => $salesOrderNo,
                // 'sales_contract_id' => $this->request->getVar('sales_contract_id'),
                'company_id' => $this->this_company_id,
                // 'user_id' => $this->this_user_id,
                'bc_type' => $this->request->getVar('aju_document_type'),
                "tanggal" => $this->request->getVar("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
                // 'divisi_id' => $this->request->getVar('divisi_id'),
                "actualy_shipment_date" => $this->request->getVar("actualy_shipment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("actualy_shipment_date")))) : "",
                'freight' => $this->request->getVar('freight'),
                'additional' => $this->request->getVar('additional'),
                'tax_id' => $this->request->getVar('tax_id'),
                'royalty_price' => $this->request->getVar('royalty_price'),
                'rebate_price' => $this->request->getVar('rebate_price'),
                'can_deduction_price' => $this->request->getVar('can_deduction_price'),
                'estimated_freight_price' => $this->request->getVar('estimated_freight_price'),
                'others_type' => $this->request->getVar('others_type'),
                'others_price' => $this->request->getVar('others_price'),
                'commision' => $this->request->getVar('commision'),
                'palet_fumigation' => $this->request->getVar('palet_fumigation'),
                'palet_fumigation_price' => $this->request->getVar('palet_fumigation_price'),
                'additional_detail' => $this->request->getVar('additional_detail'),
                'additional_detail_type' => $this->request->getVar('additional_detail_type'),
                'additional_detail_price' => $this->request->getVar('additional_detail_price'),
                'payment_term' => $this->request->getVar('payment_term'),
                'shipment_an' => $this->request->getVar('shipment_an'),
                'consigne_docs' => $this->request->getVar('consigne_docs'),
                'notify_party' => $this->request->getVar('notify_party'),
                'additional_detail_docs' => $this->request->getVar('additional_detail_docs'),
                'product_specs' => $this->request->getVar('product_specs'),
                'processing_method' => $this->request->getVar('processing_method'),
                'packaging' => $this->request->getVar('packaging'),
                'code_stamping' => $this->request->getVar('code_stamping'),
                'loading' => $this->request->getVar('loading'),
                'foto_loading' => $this->request->getVar('foto_loading'),
                'stuffing' => $this->request->getVar('stuffing'),
                'deadline' => $this->request->getVar('deadline'),
                'container' => $this->request->getVar('container'),
                'document_required' => $this->request->getVar('document_required'),
            ]);

            $listDataSalesKontrak = json_decode($_POST['listDataSalesKontrak']);

            // Hapus SalesOrderExportSubtitle
            $this->salesOrderExportSubtitleModel
                ->where('sales_order_export_id', $id)
                ->delete();

            $idSalesOrderDetailExportNotDeleted = [];
            foreach ($listDataSalesKontrak->salesContractDetailList as $s) {

                $this->salesOrderExportSubtitleModel->insert([
                    'sales_order_export_id' => $id,
                    'sales_contract_detail_id' => $s->id,
                    'divisi_id' => $s->divisi_id,
                    'brand' => $s->brand,
                    'packing' => $s->packing,
                    'species' => $s->species,
                    'specs' => $s->specs
                ]);

                foreach ($s->size_breakdown as $sb) {
                    $check = $this->salesOrderExportDetailModel
                        ->where('sales_order_export_id', $id)
                        ->where('sales_contract_size_breakdown_id', $sb->id_detail_breakdown)
                        ->first();

                    if ($check != null) {
                        // Update
                        $this->salesOrderExportDetailModel->update($check['sales_order_export_detail_id'], [
                            'sales_contract_size_breakdown_id' => $sb->id_detail_breakdown,
                            'sales_contract_detail_id' => $s->id,
                            'satuan_id' => $sb->satuan_size_id,
                            'qty' => $sb->qty_input,
                            'harga_barang' => $sb->harga,
                            'total_harga_barang' => $sb->total_input,
                            // Konversinya
                            'satuan_convertion_id' => $sb->satuan_convertion_id,
                            'qty_convertion' => $sb->qty_convertion,
                            // Note
                            'note_size' => $sb->note_size,
                            'note_grade' => $sb->note_grade,
                            'note_packing' => $sb->note_packing,
                            'note_can' => $sb->note_can,
                            'note_case' => $sb->note_case,
                            'note_kg' => $sb->note_kg,
                            'note_lb' => $sb->note_lb,
                            'note_inner_box' => $sb->note_inner_box,
                            'note_pc' => $sb->note_pc,
                            'note_bag' => $sb->note_bag,
                            'note_persen' => $sb->note_persen,
                            'note_cup' => $sb->note_cup,
                            'note_palet' => $sb->note_palet,
                        ]);

                        array_push($idSalesOrderDetailExportNotDeleted, $check['sales_order_export_detail_id']);
                    } else {
                        // Create
                        $idSalesOrderDetailExport = $this->salesOrderExportDetailModel->insert([
                            'sales_order_export_id' => $id,
                            'sales_contract_size_breakdown_id' => $sb->id_detail_breakdown,
                            'sales_contract_detail_id' => $s->id,
                            'satuan_id' => $sb->satuan_size_id,
                            'qty' => $sb->qty_input,
                            'harga_barang' => $sb->harga,
                            'total_harga_barang' => $sb->total_input,
                            // Konversinya
                            'satuan_convertion_id' => $sb->satuan_convertion_id,
                            'qty_convertion' => $sb->qty_convertion,
                            // Note
                            'note_size' => $sb->note_size,
                            'note_grade' => $sb->note_grade,
                            'note_packing' => $sb->note_packing,
                            'note_can' => $sb->note_can,
                            'note_case' => $sb->note_case,
                            'note_kg' => $sb->note_kg,
                            'note_lb' => $sb->note_lb,
                            'note_inner_box' => $sb->note_inner_box,
                            'note_pc' => $sb->note_pc,
                            'note_bag' => $sb->note_bag,
                            'note_persen' => $sb->note_persen,
                            'note_cup' => $sb->note_cup,
                            'note_palet' => $sb->note_palet,
                        ]);

                        array_push($idSalesOrderDetailExportNotDeleted, $idSalesOrderDetailExport);
                    }
                }
            }

            $this->salesOrderExportDetailModel
                ->whereNotIn('sales_order_export_detail_id', $idSalesOrderDetailExportNotDeleted)
                ->where('sales_order_export_id', $id)
                ->delete();


            // Old
            $this->salesOrderExportSpecsModel->where('sales_order_export_id', $id)->delete();
            foreach (json_decode($_POST['listDetailSpecs']) as $l) {
                $salesOrderExportSpecsId = $this->salesOrderExportSpecsModel->insert([
                    'sales_order_export_id' => $id,
                    'size_packing' => $l->size_packing,
                ]);

                foreach ($l->grade_specs as $g) {
                    $this->salesOrderExportSpecsDetailModel->insert([
                        'sales_order_export_specs_id' => $salesOrderExportSpecsId,
                        'grade' => $g->grade,
                        'specification' => $g->specification
                    ]);
                }
            }

            $this->salesOrderExportAdditionalModel
                ->where('sales_order_export_id', $id)
                ->delete();

            foreach (json_decode($_POST['listAdditional']) as $l) {
                $this->salesOrderExportAdditionalModel->insert([
                    'sales_order_export_id' => $id,
                    'additional_detail' => $l->additional_detail,
                    'additional_detail_type' => $l->additional_detail_type,
                    'additional_detail_price' => $l->additional_detail_price
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data updated",
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => "Internal server error " . $e->getMessage() . " In File " . $e->getFile() . " In Line " . $e->getLine(),
                'status' => false
            ]);
        }
    }

    public function updateStatus()
    {
        $id = decrypt($this->request->getVar('id'));
        $statusPosting = $this->request->getVar('status');
        $keterangan = $this->request->getPost("keterangan");

        if ($statusPosting == "0") {
            // INI UNPOST
            $this->salesOrderExportRevisionModel->insert([
                'sales_order_export_id' => $id,
                'date_revision' => $this->request->getVar("date_revision") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("date_revision")))) : "",
                'note' => $this->request->getVar('keterangan')
            ]);

            $salesOrderRevision = $this->salesOrderExportRevisionModel
                ->where('sales_order_export_id', $id)
                ->findAll();

            $this->salesOrderExportModel->update($id, [
                'status' => "NEW",
                "keterangan_unpost" => $keterangan,
                "jumlah_unpost" => count($salesOrderRevision),
            ]);
        } else {
            // INI POSTING
            $this->salesOrderExportModel->update($id, [
                'status' => "POSTED",
            ]);
        }

        return response()->setJSON([
            'message' => "Status Post Updated",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function updateRemark()
    {
        try {
            $id = $this->request->getPost("id");
            $remark = $this->request->getPost("remark");


            $payload = [
                "remark" => $remark,
            ];

            $response = $this->salesOrderExportDetailModel->update($id, $payload);

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => "Berhasil Ubah Remark",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = "Gagal Ubah Remark";
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function print($id = null)
    {
        $id = decrypt($id);

        $dataSO = $this->salesOrderExportModel->getById($id);
        $displayPrice = $this->request->getVar('display_price');

        if ($dataSO == null || empty($displayPrice)) {
            return redirect()->to('order-form-internasional');
        }

        $filename = $dataSO->sales_order_export_no;

        $dataSODetail =  $this->salesOrderExportModel
            ->getDetailSalesKontrakInOrderForm(
                $dataSO->sales_contract_id,
                $id,
                true
            );
        $dataSalesOrderRevision = $this->salesOrderExportRevisionModel
            ->where('sales_order_export_id', $dataSO->sales_order_export_id)
            ->where('deletedAt',  null)
            ->orderBy('id', 'asc')
            ->findAll();

        $dataSalesExportSpecs = $this->salesOrderExportModel->getSalesOrderSpecs($id);

        $dataSalesExportAdditional = $this->salesOrderExportAdditionalModel
            ->where('sales_order_export_id', $id)
            ->findAll();

        $data = [
            "displayPrice" => $displayPrice,
            "dataSO" => $dataSO,
            "dataSODetail" => $dataSODetail,
            "dataSalesOrderRevision" => $dataSalesOrderRevision,
            "dataSalesOrderSpecs" => $dataSalesExportSpecs,
            "dataSalesExportAdditional" => $dataSalesExportAdditional,
            "company" => $this->companyModel->where('id', $dataSO->company_id)->first(),
        ];


        $this->dompdf->loadHtml(view('SalesInternasional/OrderForm/print', $data));
        $this->dompdf->setPaper('Legal', 'portrait');

        $this->dompdf->render();

        // Tambahkan penomoran halaman
        $canvas = $this->dompdf->getCanvas();
        $font = $this->dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
        $fontSize = 9;

        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($font, $fontSize) {
            $text = "Page $pageNumber of $pageCount";
            $textWidth = $fontMetrics->getTextWidth($text, $font, $fontSize);
            $x = $canvas->get_width() - $textWidth - 20;
            $y = $canvas->get_height() - 20;
            $canvas->text($x, $y, $text, $font, $fontSize);
        });

        // Output PDF
        $this->dompdf->stream($filename, array("Attachment" => false));


        exit(0);
    }

    public function destroy()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->salesOrderExportModel->delete($id);
        $this->salesOrderExportDetailModel->where('sales_order_export_id', $id)->delete();
        $this->salesOrderExportSpecsModel->where('sales_order_export_id', $id)->delete();
        $this->salesOrderExportDetailModel->where('sales_order_export_id', $id)->delete();

        return response()->setJSON([
            'message' => "Order Form Deleted",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }
}
