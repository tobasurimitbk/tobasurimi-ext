<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CustomerModel;
use App\Models\BarangMasterSalesModel;
use App\Models\CompaniesModel;
use App\Models\CountryModel;
use App\Models\EmployeesModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\SalesContractRevisionModel;
use App\Models\SalesContractSizeBreakdownModel;
use App\Models\SalesKontrakModel;
use App\Models\SalesKontrakDetailModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use Dompdf\Dompdf;

class SalesKontrak extends BaseController
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
    protected $dompdf;
    protected $employessModel;
    protected $divisiModel;
    protected $bankModel;
    protected $salesContractSizeBreakdownModel;
    protected $salesContractRevisionModel;
    protected $salesOrderDetailExportModel;
    protected $salesOrderExportDetailModel;
    protected $companyModel;

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
        $this->employessModel = new EmployeesModel();
        $this->dompdf = new Dompdf();
        $this->divisiModel = new DivisisModel();
        $this->bankModel = new BanksModel();
        $this->salesContractSizeBreakdownModel = new SalesContractSizeBreakdownModel();
        $this->salesContractRevisionModel = new SalesContractRevisionModel();
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $this->companyModel = new CompaniesModel();
    }

    public function index()
    {
        $dataCompany = $this->companyModel->whereIn('id', [1, 2])->findAll();
        $data = [
            "dataCompany" => $dataCompany

        ];
        return view('SalesInternasional/SalesKontrak/index', $data);
    }

    public function createView()
    {
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );

        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataDivisi = $this->divisiModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->where('type_barang_sales', 'EKSPOR')->orderBy('createdAt', "DESC")->findAll();
        $condition = [
            'jabatan_name' => "SALES INTERNASIONAL"
        ];
        $sales = $this->employessModel->getEmployeesComplete($this->this_company_id, $condition);
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataCompany = $this->companyModel->whereIn('id', [1, 2])->findAll();

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataDivisi" => $dataDivisi,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            "dataSales" => $sales,
            "dataBank" => $dataBank,
            "dataCompany" => $dataCompany
        ];


        return view('SalesInternasional/SalesKontrak/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);

        $dataSalesKontrak = $this->salesKontrakModel->find($id);
        $dataSalesKontrakDetail = $this->salesKontrakDetailModel->detail($id);
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );
        $dataDivisi = $this->divisiModel->where('company_id', $this->this_company_id)->findAll();
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        $isClosed = $this->salesOrderExportModel->where('sales_contract_id', $id)->findAll();
        $condition = [
            'jabatan_name' => "SALES INTERNASIONAL"
        ];
        $sales = $this->employessModel->getEmployeesComplete($this->this_company_id, $condition);
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataCompany = $this->companyModel->whereIn('id', [1, 2])->findAll();

        if ($dataSalesKontrak == null) {
            return redirect()->to('sales-kontrak');
        }

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            "dataDivisi" => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            'dataSalesKontrak' => $dataSalesKontrak,
            'dataSalesKontrakDetail' => $dataSalesKontrakDetail,
            'isClosed' => count($isClosed) == 0 ? '0' : '1',
            "dataSales" => $sales,
            "dataBank" => $dataBank,
            "dataCompany" => $dataCompany
        ];

        return view('SalesInternasional/SalesKontrak/form', $data);
    }


    public function duplicate($id)
    {
        $id = decrypt($id);

        $dataSalesKontrak = $this->salesKontrakModel->find($id);
        $dataSalesKontrakDetail = $this->salesKontrakDetailModel->detail($id);
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        $isClosed = $this->salesOrderExportModel->where('sales_contract_id', $id)->findAll();
        $dataDivisi = $this->divisiModel->where('company_id', $this->this_company_id)->findAll();
        $condition = [
            'jabatan_name' => "SALES"
        ];
        $sales = $this->employessModel->getEmployeesComplete($this->this_company_id, $condition);
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();

        if ($dataSalesKontrak == null) {
            return redirect()->to('sales-kontrak');
        }

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            "dataDivisi" => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            'dataSalesKontrak' => $dataSalesKontrak,
            'dataSalesKontrakDetail' => $dataSalesKontrakDetail,
            'isClosed' => count($isClosed) == 0 ? '0' : '1',
            "dataSales" => $sales,
            "dataBank" => $dataBank
        ];

        return view('SalesInternasional/SalesKontrak/form_duplicate', $data);
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
                "sales_contract.company_id"    => $this->this_company_id,
                "sales_contract.deletedAt" => null
            ];
        } else {
            $condition = [
                "sales_contract.company_id"    => $this->this_company_id,
                "createdBy" => $this->this_user_id,
                "sales_contract.deletedAt" => null
            ];
        }


        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status_posting"  => $this->request->getGet("status_posting"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesKontrakData = $this->salesKontrakModel->getList($condition, $addCondition, $limit, $offset);

        $dataSalesKontrak = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesKontrakData['data'] as $data) {
            $isClosed = $this->salesOrderExportModel->where('sales_contract_id', $data->id)->where('deletedAt', null)->where('status', "POSTED")->findAll();
            // var_dump($isClosed);
            // exit;

            array_push($dataSalesKontrak, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "sales_contract_no"     => $data->sales_contract_no,
                "customer_po_no"        => $data->customer_po_no,
                "customer_name"         => $data->customer_name,
                "dicharge_port"         => $data->dicharge_port,
                "shipment_date"         => $data->shipment_date,
                "createdAt"             => date('d/m/Y', strtotime($data->createdAt)),
                "status_posting"        => $data->status_posting,
                "status_closed"         => count($isClosed) > 0 ? '1' : '0',
                "keterangan_unpost"         => $data->keterangan_unpost,
                "jumlah_unpost"             => $data->jumlah_unpost,
                "divisi" => $data->divisi
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesKontrakData['totalData'],
            "recordsFiltered"   => $salesKontrakData['totalFilteredData'],
            "data"              => $dataSalesKontrak,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        $barangs = json_decode($_POST['listBarang']);

        if (count($barangs) == 0) {
            return response()->setJSON([
                'message' => "List barang belum ada",
                'status' => false,
                'token' => csrf_token()
            ]);
        }

        // Validasi Nomor Sales Kontrak
        $salesContractNo = $this->request->getVar('sales_contract_no');
        $salesContract = $this->salesKontrakModel->where('sales_contract_no', $salesContractNo)
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->first();

        if ($salesContract != null) {
            return response()->setJSON([
                'message' => "Nomor sales kontrak sudah digunakan oleh anda atau sales lain",
                'status' => false,
                'token' => csrf_token()
            ]);
        }

        $id = $this->salesKontrakModel->insert([
            'company_id' => $this->this_company_id,
            'bank_id' => $this->request->getVar('bank_id'),
            'banking_information' => $this->request->getVar('banking_information'),
            'currency' => $this->request->getVar('currency'),
            'customer_id' => $this->request->getVar('customer_id'),
            'customer_po_no' => $this->request->getVar('customer_po_no'),
            'dicharge_port' => $this->request->getVar('dicharge_port'),
            // 'divisi_id' => $this->request->getVar('divisi_id'),
            'documents_required' => $this->request->getVar('documents_required'),
            'keterangan' => $this->request->getVar('keterangan'),
            'komisi' => $this->request->getVar('komisi'),
            'loading_port' => $this->request->getVar('loading_port'),
            'no_container' => $this->request->getVar('no_container'),
            'payment_term' => $this->request->getVar('payment_term'),
            'potongan_harga' => $this->request->getVar('potongan_harga'),
            'sales_contract_no' => $this->request->getVar('sales_contract_no'),
            'shipment_date' => $this->request->getVar("shipment_date"),
            'shipment_insurance' => $this->request->getVar('shipment_insurance'),
            'signature_by' => $this->request->getVar('signature_by'),
            'special_instructions' => $this->request->getVar('special_instructions'),
            'tipe_harga' => $this->request->getVar('tipe_harga'),
            'tolerance' => $this->request->getVar('tolerance'),
            'total_amount' => $this->request->getVar('total_amount'),
            'total_container' => $this->request->getVar('total_container'),
            'royalty' => $this->request->getVar('royalty'),
            'royalty_price' => $this->request->getVar('royalty_price'),
            'rebate' => $this->request->getVar('rebate'),
            'rebate_price' => $this->request->getVar('rebate_price'),
            'can_deduction' => $this->request->getVar('can_deduction'),
            'can_deduction_price' => $this->request->getVar('can_deduction_price'),
            'estimated_freight' => $this->request->getVar('estimated_freight'),
            'estimated_freight_price' => $this->request->getVar('estimated_freight_price'),
            'others' => $this->request->getVar('others'),
            'others_price' => $this->request->getVar('others_price'),
            'others_type' => $this->request->getVar('others_type'),
            'createdBy' => $this->this_user_id,
            'status_posting' => '0',
        ]);

        foreach ($barangs as $b) {
            $salesKontrakDetailId = $this->salesKontrakDetailModel->insert([
                'sales_contract_id' => $id,
                'barang_master_sales_id' => $b->barang_master_sales_id,
                'brand' => $b->brand,
                'harga' => $b->harga,
                'kemasan' => $b->packing,
                'qty' => $b->qty,
                'species' => $b->species,
                'specs' => $b->specs,
                'total_harga' => $b->total,
            ]);

            foreach ($b->size_breakdown as $sb) {
                $this->salesContractSizeBreakdownModel->insert([
                    'sales_contract_detail_id' => $salesKontrakDetailId,
                    'bag' => $sb->bag,
                    'can' => $sb->can,
                    'cased' => $sb->cased,
                    'grade' => $sb->grade,
                    'inner_box' => $sb->inner_box,
                    'kg' => $sb->kg,
                    'lb' => $sb->lb,
                    'packing' => $sb->packing,
                    'pc' => $sb->pc,
                    'persen' => $sb->persen,
                    'remark' => $sb->remark,
                    'size' => $sb->size,
                    'qty' => $sb->qty,
                    'harga' => $sb->harga,
                    'total' => $sb->total,
                    'palet' => $sb->palet,
                    'satuan_size_id' => $sb->satuan_size_id
                ]);
            }
        }

        return response()->setJSON([
            'id' => encrypt($id),
            'message' => "Sales Kontrak Created",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $barangs = json_decode($_POST['listBarang']);

        if (count($barangs) == 0) {
            return response()->setJSON([
                'message' => "List barang belum ada",
                'status' => false,
                'token' => csrf_token()
            ]);
        }


        // Validasi Nomor Sales Kontrak
        $salesContractNo = $this->request->getVar('sales_contract_no');
        $salesContract = $this->salesKontrakModel->where('sales_contract_no', $salesContractNo)
            ->where('company_id', $this->this_company_id)
            ->where('id !=', $id)
            ->where('deletedAt', null)
            ->first();

        if ($salesContract != null) {
            // JIKA SUDAH DIGUNAKEN SALES LAIN MAKA GENERATE YANG BARU
            $salesContractNo = $this->getNoStr();
        }

        $this->salesKontrakModel->update($id, [
            'company_id' => $this->this_company_id,
            'bank_id' => $this->request->getVar('bank_id'),
            'banking_information' => $this->request->getVar('banking_information'),
            'currency' => $this->request->getVar('currency'),
            'customer_id' => $this->request->getVar('customer_id'),
            'customer_po_no' => $this->request->getVar('customer_po_no'),
            'dicharge_port' => $this->request->getVar('dicharge_port'),
            // 'divisi_id' => $this->request->getVar('divisi_id'),
            'documents_required' => $this->request->getVar('documents_required'),
            'keterangan' => $this->request->getVar('keterangan'),
            'komisi' => $this->request->getVar('komisi'),
            'loading_port' => $this->request->getVar('loading_port'),
            'no_container' => $this->request->getVar('no_container'),
            'payment_term' => $this->request->getVar('payment_term'),
            'potongan_harga' => $this->request->getVar('potongan_harga'),
            'sales_contract_no' => $salesContractNo,
            'total_container' => $this->request->getVar('total_container'),
            'shipment_date' => $this->request->getVar("shipment_date"),
            'shipment_insurance' => $this->request->getVar('shipment_insurance'),
            'signature_by' => $this->request->getVar('signature_by'),
            'special_instructions' => $this->request->getVar('special_instructions'),
            'tipe_harga' => $this->request->getVar('tipe_harga'),
            'tolerance' => $this->request->getVar('tolerance'),
            'total_amount' => $this->request->getVar('total_amount'),
            'royalty' => $this->request->getVar('royalty'),
            'royalty_price' => $this->request->getVar('royalty_price'),
            'rebate' => $this->request->getVar('rebate'),
            'rebate_price' => $this->request->getVar('rebate_price'),
            'can_deduction' => $this->request->getVar('can_deduction'),
            'can_deduction_price' => $this->request->getVar('can_deduction_price'),
            'estimated_freight' => $this->request->getVar('estimated_freight'),
            'estimated_freight_price' => $this->request->getVar('estimated_freight_price'),
            'others' => $this->request->getVar('others'),
            'others_price' => $this->request->getVar('others_price'),
            'others_type' => $this->request->getVar('others_type'),
            // 'createdBy' => $this->this_user_id,
        ]);

        // get all id detail
        $id_detail_all = [];
        $id_size_breakdown = [];

        foreach ($barangs as $b) {
            // CHECK
            $check = $this->salesKontrakDetailModel
                ->where('id', $b->id_detail)
                ->first();

            if ($check != null) {
                $this->salesKontrakDetailModel->update($check['id'], [
                    'barang_master_sales_id' => $b->barang_master_sales_id,
                    'brand' => $b->brand,
                    'harga' => $b->harga,
                    'kemasan' => $b->packing,
                    'qty' => $b->qty,
                    'species' => $b->species,
                    'specs' => $b->specs,
                    'total_harga' => $b->total,
                ]);

                // Hapus Dulu
                // $check = $this->salesContractSizeBreakdownModel
                //     ->where('sales_contract_detail_id', $check['id'])
                //     ->delete();

                foreach ($b->size_breakdown as $sb) {
                    $checkBreakdown = $this->salesContractSizeBreakdownModel->where('id', $sb->id_detail_breakdown)->first();
                    if ($checkBreakdown != null) {
                        // UPDATE
                        $this->salesContractSizeBreakdownModel->update($checkBreakdown['id'], [
                            'sales_contract_detail_id' => $check['id'],
                            'bag' => $sb->bag,
                            'can' => $sb->can,
                            'cased' => $sb->cased,
                            'grade' => $sb->grade,
                            'inner_box' => $sb->inner_box,
                            'kg' => $sb->kg,
                            'lb' => $sb->lb,
                            'packing' => $sb->packing,
                            'pc' => $sb->pc,
                            'persen' => $sb->persen,
                            'remark' => $sb->remark,
                            'size' => $sb->size,
                            'qty' => $sb->qty,
                            'harga' => $sb->harga,
                            'total' => $sb->total,
                            'palet' => $sb->palet,
                            'satuan_size_id' => $sb->satuan_size_id
                        ]);

                        array_push($id_size_breakdown, $checkBreakdown['id']);
                        // Hapus Di 
                    } else {
                        // INSERT
                        $id_size_breakdown_new = $this->salesContractSizeBreakdownModel->insert([
                            'sales_contract_detail_id' => $check['id'],
                            'bag' => $sb->bag,
                            'can' => $sb->can,
                            'cased' => $sb->cased,
                            'grade' => $sb->grade,
                            'inner_box' => $sb->inner_box,
                            'kg' => $sb->kg,
                            'lb' => $sb->lb,
                            'packing' => $sb->packing,
                            'pc' => $sb->pc,
                            'persen' => $sb->persen,
                            'remark' => $sb->remark,
                            'size' => $sb->size,
                            'qty' => $sb->qty,
                            'harga' => $sb->harga,
                            'total' => $sb->total,
                            'palet' => $sb->palet,
                            'satuan_size_id' => $sb->satuan_size_id
                        ]);

                        array_push($id_size_breakdown, $id_size_breakdown_new);
                    }
                }

                array_push($id_detail_all, $check['id']);
            } else {
                // $this->salesKontrakDetailModel
                //     ->where('sales_contract_id', $id)
                //     ->where('barang_master_sales_id', $b->barang_master_sales_id)
                //     ->delete();

                // INSERT
                $id_detail_new = $this->salesKontrakDetailModel->insert([
                    'sales_contract_id' => $id,
                    'barang_master_sales_id' => $b->barang_master_sales_id,
                    'brand' => $b->brand,
                    'harga' => $b->harga,
                    'kemasan' => $b->packing,
                    'qty' => $b->qty,
                    'species' => $b->species,
                    'specs' => $b->specs,
                    'total_harga' => $b->total,
                ]);

                foreach ($b->size_breakdown as $sb) {
                    $id_size_breakdown_new = $this->salesContractSizeBreakdownModel->insert([
                        'sales_contract_detail_id' => $id_detail_new,
                        'bag' => $sb->bag,
                        'can' => $sb->can,
                        'cased' => $sb->cased,
                        'grade' => $sb->grade,
                        'inner_box' => $sb->inner_box,
                        'kg' => $sb->kg,
                        'lb' => $sb->lb,
                        'packing' => $sb->packing,
                        'pc' => $sb->pc,
                        'persen' => $sb->persen,
                        'remark' => $sb->remark,
                        'size' => $sb->size,
                        'qty' => $sb->qty,
                        'harga' => $sb->harga,
                        'total' => $sb->total,
                        'satuan_size_id' => $sb->satuan_size_id
                    ]);

                    array_push($id_size_breakdown, $id_size_breakdown_new);
                }


                array_push($id_detail_all,  $id_detail_new);
            }
        }

        $this->salesKontrakDetailModel->where('sales_contract_id', $id)->whereNotIn('id', $id_detail_all)->delete();
        $this->salesContractSizeBreakdownModel->whereNotIn('id', $id_size_breakdown)->delete();
        $this->salesOrderExportDetailModel->whereNotIn('sales_contract_size_breakdown_id', $id_size_breakdown)->delete();

        return response()->setJSON([
            'message' => "Sales Kontrak Updated",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function updateStatus()
    {
        $id = decrypt($this->request->getVar('id'));
        $statusPosting = $this->request->getVar('status');
        $keterangan = $this->request->getPost("keterangan");

        if ($statusPosting == "0") {
            // INI UNPOST
            $this->salesContractRevisionModel->insert([
                'sales_contract_id' => $id,
                'date_revision' => $this->request->getVar("date_revision") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("date_revision")))) : "",
                'note' => $this->request->getVar('keterangan')
            ]);

            $salesContractRevision = $this->salesContractRevisionModel
                ->where('sales_contract_id', $id)
                ->findAll();

            $this->salesKontrakModel->update($id, [
                'status_posting' => $statusPosting,
                "keterangan_unpost" => $keterangan,
                "jumlah_unpost" => count($salesContractRevision),
            ]);
        } else {
            // INI POSTING
            $this->salesKontrakModel->update($id, [
                'status_posting' => $statusPosting,
            ]);
        }

        return response()->setJSON([
            'message' => "Status Post Updated",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->salesKontrakModel->delete($id);
        $salesContractDetail =  $this->salesKontrakDetailModel->where('sales_contract_id', $id)->findAll();
        foreach ($salesContractDetail as $s) {
            $this->salesContractSizeBreakdownModel->where('sales_contract_detail_id', $s['id'])->delete();
        }
        $this->salesKontrakDetailModel->where('sales_contract_id', $id)->delete();

        return response()->setJSON([
            'message' => "Sales Kontrak Deleted",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function print($id)
    {
        $filename = "Sales Kontrak";
        $id = decrypt($id);

        $salesKontrak = $this->salesKontrakModel
            ->select(
                '
                sales_contract.*,
                customers.name as customer_name,
                banks.kode_bank,
                banks.name as nama_bank,
                banks.atas_nama,
                banks.no_rekening,
                metadata.value as mata_uang
                '
            )
            ->join('customers', 'sales_contract.customer_id = customers.id', 'left')
            ->join('banks', 'banks.id = sales_contract.bank_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
            ->find($id);
        $salesKontrakDetail = $this->salesKontrakDetailModel->detail($id);
        if ($salesKontrak == null) {
            return redirect()->to('sales-kontrak');
        }

        $data = [
            'salesKontrak' => $salesKontrak,
            'customer' => $this->customerModel->find($salesKontrak['customer_id']),
            'salesKontrakdetail' => $salesKontrakDetail,
            'revisionList' => $this->salesContractRevisionModel->where('sales_contract_id', $id)->findAll(),
            "company" => $this->companyModel->where('id', $salesKontrak['company_id'])->first(),
        ];

        $letterHeadCompany = $this->request->getGet('letter_head_company');
        if (!empty($letterHeadCompany)) {
            $data['company'] =  $this->companyModel->where('id', $letterHeadCompany)->first();
        }

        if ($data['company'] == null) {
            var_dump("State Exception : company not found, please back to previous page");
            die;
        }

        $this->dompdf->loadHtml(view('SalesInternasional/SalesKontrak/print', $data));
        $this->dompdf->setPaper('legal', 'portrait');
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


    public function dropdownCustomer()
    {
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );
        return response()->setJSON([
            'data' => $dataCustomer,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownMasterBarang()
    {
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)
            ->where('type_barang_sales', 'EKSPOR')
            ->orderBy('createdAt', "DESC")
            ->findAll();
        return response()->setJSON([
            'data' => $dataBarang,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getNo()
    {
        $no = $this->getNoStr();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $no
        ]);
    }

    private function getNoStr()
    {
        $no = $this->salesKontrakModel->get_no(date('Y'), date('y'), $this->this_company_id);
        return $no;
    }
}
