<?php

namespace App\Controllers\Production;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInModel;
use App\Models\KemasanModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestsModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;
use Exception;
use Dompdf\Dompdf;

class MaterialRequest extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $satuanModel;
    protected $workOrdersModel;
    protected $workOrderDetailsModel;
    protected $warehousesModel;
    protected $divisiModel;
    protected $woModel;
    protected $metaDataModel;
    protected $stockModel;
    protected $barangMasterSpesifikasiModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $kemasanModel;
    protected $penerimaanBarangModel;
    protected $barangMasterModel;
    protected $penerimaanBarangDetailModel;
    protected $supplierModel;
    protected $materialRequestModel;
    protected $materialRequestDetailsModel;
    protected $this_user_id;
    protected $vendorModel;
    protected $rmPurchaseOrderModel;
    protected $jasaVendorInModel;

    protected $jurnalUmumController;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->satuanModel = new SatuansModel();
        $this->workOrdersModel = new WorkOrdersModel();
        $this->workOrderDetailsModel = new WorkOrderDetailsModel();
        $this->warehousesModel = new WarehousesModel();
        $this->divisiModel = new DivisisModel();
        $this->woModel = new WorkOrdersModel();
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->stockModel = new StockModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->kemasanModel = new KemasanModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestDetailsModel = new MaterialRequestDetailsModel();
        $this->vendorModel = new VendorModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->jasaVendorInModel = new JasaVendorInModel();

        $this->jurnalUmumController = new JurnalUmum();
    }

    public function index()
    {
        return view('Production/materialRequest/index');
    }

    public function createView()
    {

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $dataWarehouse = $this->warehousesModel->asObject()->where('company_id', $this->this_company_id)->find();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataWorkOrder = $this->workOrdersModel->asObject()
            ->select('work_orders.*, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR \', \') AS nama_barang')
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
            ->where('company_id', $this->this_company_id)
            // ->where('work_orders.is_posted', "0")
            ->where('work_orders.deletedAt', null)
            ->where('work_order_details.deletedAt', null)
            ->groupBy('work_order_details.work_order_id')
            ->find();
        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description', "bahan_baku")
            ->orWhere('description', "bahan_jadi")
            ->orWhere('description', "bahan_scrap")
            ->orWhere('description', "bahan_setengah_jadi")
            ->findAll();

        $dataVendor = $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll();
        $dataSupplierBahanBaku = $this->supplierModel->getSupplierByType("BAHAN BAKU");

        $data = [
            'tipeBarang' => $dataTipeBarang,
            // "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
            "dataWorkOrder" => $dataWorkOrder,
            "dataVendor" => $dataVendor,
            "dataSupplierBahanBaku" => $dataSupplierBahanBaku
        ];

        return view('Production/materialRequest/form', $data);
    }

    public function getById($id = null)
    {
        $ids = $id;
        $id = decrypt($id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $dataWarehouse = $this->warehousesModel->asObject()->where('company_id', $this->this_company_id)->find();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataWorkOrder = $this->workOrdersModel->asObject()
            ->select('work_orders.*, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR \', \') AS nama_barang')
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
            ->where('company_id', $this->this_company_id)
            // ->where('work_orders.is_posted', "0")
            ->where('work_orders.deletedAt', null)
            ->where('work_order_details.deletedAt', null)
            ->groupBy('work_order_details.work_order_id')
            ->find();

        $dataVendor = $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll();
        $dataSupplierBahanBaku = $this->supplierModel->getSupplierByType("BAHAN BAKU");

        $data = [
            'tipeBarang' => $this->metaDataModel
                ->where('deletedAt', null)
                ->where('name', "Kategori Barang")
                ->where('description', "bahan_baku")
                ->orWhere('description', "bahan_penolong")
                ->orWhere('description', "bahan_jadi")
                ->orWhere('description', "bahan_scrap")
                ->orWhere('description', "bahan_setengah_jadi")
                ->findAll(),
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
            "dataVendor" => $dataVendor,
            "dataSupplierBahanBaku" => $dataSupplierBahanBaku,
            "dataWorkOrder" => $dataWorkOrder,
        ];

        if (!empty($id)) {
            $dataMaterialRequests = $this->materialRequestModel->asObject()->find($id);
            $dataMaterialRequestDetailsBahanBaku = $this->materialRequestDetailsModel->getMaterialRequestBahanBakuDetailNew($id);
            $dataMaterialRequestDetails = $this->materialRequestDetailsModel->asObject()
                ->select(
                    '
                        material_request_details.*, 
                        barang_master.kode_barang, 
                        satuans.kode_satuan, 
                        warehouse_asal.warehouse_name as warehouse_asal_text, 
                        divisi_asal.divisi as divisi_asal_text,
                        warehouse_tujuan.warehouse_name as warehouse_tujuan_text,
                        divisi_tujuan.divisi as divisi_tujuan_text
                    '
                )
                ->join('barang_master', 'barang_master.id = material_request_details.barang1_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = material_request_details.barang2_id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->join('divisis as divisi_asal', 'divisi_asal.id = material_request_details.divisi_id', 'left')
                ->join('divisis as divisi_tujuan', 'divisi_tujuan.id = material_request_details.divisi_tujuan_id', 'left')
                ->join('warehouses as warehouse_asal', 'warehouse_asal.id = material_request_details.warehouse_id', 'left')
                ->join('warehouses as warehouse_tujuan', 'warehouse_tujuan.id = material_request_details.warehouse_tujuan_id', 'left')
                ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
                ->where('material_request_id', $id)
                ->where('parent_barang.parent_name !=', "KIMIA")
                ->where('material_request_details.deletedAt', null)

                // ->groupBy('material_request_details.barang1_id, material_request_details.barang2_id, material_request_details.stock_tujuan_id')
                ->get()->getResult();
            foreach ($dataMaterialRequestDetails as $key => &$value) {
                // Penambahan Nomor Daftar
                $value->no_daftar = $this->stockDetail2Model->getNomorDaftar($value->no_aju, $value->bc_id);
                if ($value->barang_type == "bahan_baku") {
                    $value->barang_type_text = "BAHAN BAKU";
                } elseif ($value->barang_type == "bahan_penolong") {
                    $value->barang_type_text = "BAHAN PENOLONG";
                } elseif ($value->barang_type == "bahan_jadi") {
                    $value->barang_type_text = "BARANG JADI";
                } elseif ($value->barang_type == "bahan_scrap") {
                    $value->barang_type_text = "BARANG SCRAP";
                } elseif ($value->barang_type == "bahan_modal") {
                    $value->barang_type_text = "BARANG MODAL";
                } elseif ($value->barang_type == "bahan_setengah_jadi") {
                    $value->barang_type_text = "BARANG SETENGAH JADI";
                }
            }

            foreach ($dataMaterialRequestDetailsBahanBaku as $key => &$value) {
                $dataMaterialRequestNotApprove = $this->materialRequestDetailsModel->getMaterialRequestNotApprove(
                    $value['stock_id'],
                    $value['bc_id'],
                    $value['no_aju'],
                    date('Y-m-d', strtotime(str_replace('/', '-', $value['stock_date']))),
                    $value['stock_dokumen']
                );

                if ($dataMaterialRequestNotApprove) {
                    $value['is_requested'] = true;
                    $stokTotal = $this->stockDetail2Model->getStockListDetail(
                        $value['stock_id'],
                        $value['bc_id'],
                        $value['no_aju'],
                        $value['stock_dokumen']
                    )['stok_total'];

                    $selisih = $stokTotal - $dataMaterialRequestNotApprove['qty'];

                    // hasil string, contoh "6.60"
                    $value['realStok'] = sprintf(
                        '%.2f',
                        floor(max(0, $selisih) * 100) / 100
                    );
                } else {
                    $value['is_requested'] = false;
                }
            }

            $data["dataMaterialRequests"] = $dataMaterialRequests;
            $data["dataMaterialRequestDetails"] = $dataMaterialRequestDetails;
            $data["dataMaterialRequestDetailsBahanBaku"] = $dataMaterialRequestDetailsBahanBaku;
            $data["ids"] = $ids;
        }


        return view('Production/materialRequest/form', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            'material_requests.company_id' => $this->this_company_id,
            // 'parent_barang.parent_name !=' => "KIMIA"
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $materialRequestData = $this->materialRequestModel->getMaterialRequestList($condition, $addCondition, $limit, $offset);

        $dataMaterialRequest = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($materialRequestData['data'] as $data) {
            array_push($dataMaterialRequest, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "req_no"                 => $data->req_no,
                // "nama_barang"           => $data->nama_barang,
                "is_posted"           => $data->is_posted,
                "request_date"           => date('d/m/Y', strtotime($data->request_date)),
                "production_date"           => date('d/m/Y', strtotime($data->production_date)),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $materialRequestData['totalData'],
            "recordsFiltered"   => $materialRequestData['totalFilteredData'],
            "data"              => $dataMaterialRequest,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function allDetailMaterialRequest()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "material_requests.id"        => decrypt($this->request->getGet("id"))
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $materialRequestData = $this->materialRequestModel->getMaterialRequestList($condition, $addCondition, $limit, $offset);

        $dataMaterialRequest = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($materialRequestData['data'] as $data) {
            array_push($dataMaterialRequest, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "req_no"                 => $data->req_no,
                "tgl_req"                 => $data->request_date,
                "nama_divisi"           => $data->divisi,
                "nama_warehouse"           => $data->warehouse_name,
                "nama_barang"           => $data->nama_barang,
                "satuan"           => $data->satuan,
                "total"           => $data->total,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $materialRequestData['totalData'],
            "recordsFiltered"   => $materialRequestData['totalFilteredData'],
            "data"              => $dataMaterialRequest,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function create()
    {
        try {
            $date_request =  formatDMYtoYMD($this->request->getVar('date_request'));
            $last_day = getLastDay();
            $date_request_explode = explode("-", $date_request);
            $reqNo = $this->request->getVar('req_no');
            if ($reqNo == "AUTO GENERATE") {
                $no = $this->materialRequestModel->get_no($date_request_explode[2], $date_request_explode[1], $date_request_explode[0], $last_day, $this->this_company_id);
            } else {
                $no = $reqNo;
            }

            // $checkDuplicate = $this->materialRequestModel->where('company_id', $this->this_company_id)
            //     ->where('req_no', $no)
            //     ->where('deletedAt', null)
            //     ->first();

            // if ($checkDuplicate != null) {
            //     return \response()->setJSON([
            //         "status"            => false,
            //         "message"    => "No Material Requests sudah ada",
            //         'token' => csrf_hash()
            //     ]);
            // }

            $dataMaterial = [
                "work_order_id" => $this->request->getPost("kode_produksi"),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                'warehouse_id' => $this->request->getVar("warehouse_id"),
                "production_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                "request_date" => $this->request->getVar("date_request") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_request")))) : "",
                "req_no" => $no,
                'is_posted' => 0,
                'createdBy' =>  session()->get("login")->user_id,
            ];
            $id = $this->materialRequestModel->insert($dataMaterial);

            $mr_detail = json_decode($this->request->getVar("listMaterial"));
            // var_dump($mr_detail);
            // die;

            // Khusus Bahan Baku 
            foreach ($mr_detail as $s) {
                if ($s->type_barang == "bahan_baku") {
                    // Bahan Baku Po
                    $stockId = decrypt2($s->id);
                    $stockIdArr = json_decode($stockId);
                    if ($s->sumber != "JASA VENDOR") {
                        // STOK DARI SUPPLIER
                        $qtyDiambil = 0;

                        foreach ($stockIdArr as $si) {
                            $stockDetail = $this->stockDetail2Model->getStockListDetail(
                                $si,
                                $s->bc_id,
                                $s->no_aju,
                                $s->stock_dokumen
                            );

                            if ($stockDetail) {
                                $qtyYangTersedia = $stockDetail['stok_total'];
                                $qtyYangDiperlukan = $s->qty2 - $qtyDiambil;
                                $qtyDiambilSekarang = min($qtyYangTersedia, $qtyYangDiperlukan);
                                $qtyDiambil += $qtyDiambilSekarang;

                                $this->materialRequestDetailsModel->insert([
                                    'material_request_id' => $id,
                                    'divisi_id' => $s->departmentID,
                                    'warehouse_id' => $s->warehouseID,
                                    'divisi_tujuan_id' => $s->departmentTujuanID,
                                    'warehouse_tujuan_id' => $s->warehouseTujuanID,
                                    'barang1_id' => $stockDetail['barang1_id'],
                                    'barang2_id' => $stockDetail['barang2_id'],
                                    'nama_barang' => $stockDetail['barang'],
                                    'satuan' => $s->satuan,
                                    'stock_id' => $stockDetail['stock_id'],
                                    'bc_id' => $s->bc_id,
                                    'supplier_id' => $stockDetail['supplier_id'],
                                    'no_aju' => $s->no_aju,
                                    'ref_no' => $s->bc_type,
                                    'stock_date' => $stockDetail['stock_date'],
                                    'stock_dokumen' => $s->stock_dokumen,
                                    'barang_type' => $s->type_barang,
                                    'qty' => $qtyDiambilSekarang,
                                    'qty2' => $qtyDiambilSekarang,
                                    'qty_isi' => 0,
                                    'qty_now' => $qtyDiambilSekarang,
                                    'harga_umum' => $stockDetail['harga_umum'],
                                    'harga_harian' => $stockDetail['harga_harian'],
                                    'harga_bulanan' => $stockDetail['harga_bulanan'],
                                    'kondisi_barang' => 'request',
                                    'keterangan' => null,
                                ]);

                                if ($qtyDiambil >= $s->qty2) {
                                    break;
                                }
                            }
                        }
                    } else {
                        // Bahan Baku Vendor
                        $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                        $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);

                        $dataMaterialDetail = [
                            'material_request_id' => $id,
                            'divisi_id' => $s->departmentID,
                            'warehouse_id' => $s->warehouseID,
                            'divisi_tujuan_id' => $s->departmentTujuanID,
                            'warehouse_tujuan_id' => $s->warehouseTujuanID,
                            'barang1_id' => $stockBarang->barang1_id,
                            'barang2_id' => $stockBarang->barang2_id,
                            'nama_barang' => $s->barang,
                            'satuan' => $s->satuan,
                            'stock_id' => $s->stock_id,
                            'bc_id' => $s->bc_id,
                            'supplier_id' => $s->supplier_id,
                            'no_aju' => $s->no_aju,
                            'ref_no' => $s->bc_type,
                            'stock_date' => $stockDetailBarang->stock_date,
                            'stock_dokumen' => $s->stock_dokumen,
                            'barang_type' => $s->type_barang,
                            'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                            'qty_now' => $s->qty2,
                            'harga_umum' => (float)$s->harga_umum,
                            'harga_harian' => (float)$s->harga_harian,
                            'harga_bulanan' => (float)$s->harga_bulanan,
                            'kondisi_barang' => 'request',
                            'keterangan' => $s->keterangan,
                        ];
                        $this->materialRequestDetailsModel->insert($dataMaterialDetail);
                    }
                }
            }

            // Selain Bahan Baku
            foreach ($mr_detail as $s) {

                if ($s->type_barang == "bahan_jadi") {
                    $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                    $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);

                    $dataMaterialDetail = [
                        'material_request_id' => $id,
                        'divisi_id' => $s->departmentID,
                        'warehouse_id' => $s->warehouseID,
                        'divisi_tujuan_id' => $s->departmentTujuanID,
                        'warehouse_tujuan_id' => $s->warehouseTujuanID,
                        'barang1_id' => $stockBarang->barang1_id,
                        'barang2_id' => $stockBarang->barang2_id,
                        'nama_barang' => $s->barang,
                        'satuan' => $s->satuan,
                        'stock_id' => $s->stock_id,
                        'bc_id' => $s->bc_id,
                        'supplier_id' => $s->supplier_id,
                        'no_aju' => $s->no_aju,
                        'ref_no' => $s->bc_type,
                        'stock_date' => $stockDetailBarang->stock_date,
                        'stock_dokumen' => $s->stock_dokumen,
                        'barang_type' => $s->type_barang,
                        'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                        'qty2' => $s->qty2,
                        'qty_isi' => $s->qty_isi,
                        'qty_now' => $s->qty_isi,
                        'harga_umum' => (float)$s->harga_umum,
                        'harga_harian' => (float)$s->harga_harian,
                        'harga_bulanan' => (float)$s->harga_bulanan,
                        'kondisi_barang' => 'request',
                        'keterangan' => $s->keterangan,
                    ];
                    $this->materialRequestDetailsModel->insert($dataMaterialDetail);
                } elseif ($s->type_barang != "bahan_baku") {
                    $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                    $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);

                    $dataMaterialDetail = [
                        'material_request_id' => $id,
                        'divisi_id' => $s->departmentID,
                        'warehouse_id' => $s->warehouseID,
                        'divisi_tujuan_id' => $s->departmentTujuanID,
                        'warehouse_tujuan_id' => $s->warehouseTujuanID,
                        'barang1_id' => $stockBarang->barang1_id,
                        'barang2_id' => $stockBarang->barang2_id,
                        'nama_barang' => $s->barang,
                        'satuan' => $s->satuan,
                        'stock_id' => $s->stock_id,
                        'bc_id' => $s->bc_id,
                        'supplier_id' => $s->supplier_id,
                        'no_aju' => $s->no_aju,
                        'ref_no' => $s->bc_type,
                        'stock_date' => $stockDetailBarang->stock_date,
                        'stock_dokumen' => $s->stock_dokumen,
                        'barang_type' => $s->type_barang,
                        'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                        'qty2' => $s->qty2,
                        'qty_isi' => $s->qty_isi,
                        'qty_now' => $s->qty2,
                        'harga_umum' => (float)$s->harga_umum,
                        'harga_harian' => (float)$s->harga_harian,
                        'harga_bulanan' => (float)$s->harga_bulanan,
                        'kondisi_barang' => 'request',
                        'keterangan' => $s->keterangan,
                    ];
                    $this->materialRequestDetailsModel->insert($dataMaterialDetail);
                }
            }

            return response()->setJSON([
                "id"      => encrypt($id),
                "status"  => true,
                "message" => "Data Berhasil disimpan",
                'token'   => csrf_hash(),
            ]);
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


    public function createNew()
    {
        try {
            $date_request = formatDMYtoYMD($this->request->getVar('date_request'));
            $last_day = getLastDay();
            $date_request_explode = explode("-", $date_request);
            $reqNo = $this->request->getVar('req_no');

            if ($reqNo == "AUTO GENERATE") {
                $no = $this->materialRequestModel->get_no($date_request_explode[2], $date_request_explode[1], $date_request_explode[0], $last_day, $this->this_company_id);
            } else {
                $no = $reqNo;
            }

            $dataMaterial = [
                'work_order_id' => implode(",", $this->request->getVar("kode_produksi")),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                'warehouse_id' => $this->request->getVar("warehouse_id"),
                "production_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                "request_date" => $this->request->getVar("date_request") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_request")))) : "",
                "req_no" => $no,
                'is_posted' => 0,
                'createdBy' => session()->get("login")->user_id,
            ];

            $id = $this->materialRequestModel->insert($dataMaterial);
            $mr_detail = json_decode($this->request->getVar("listMaterial"));

            foreach ($mr_detail as $item) {
                $stockId = ($item->stock_id);
                $stockData = $this->stockModel->asObject()->where('id', $stockId)->first();


                $dataMaterialDetail = [
                    'material_request_id' => $id,
                    'divisi_id' => $item->departmentID,
                    'warehouse_id' => $item->warehouseID,
                    'divisi_tujuan_id' => $item->departmentTujuanID,
                    'warehouse_tujuan_id' => $item->warehouseTujuanID,
                    'barang1_id' => $stockData->barang1_id,
                    'barang2_id' => $stockData->barang2_id,
                    'nama_barang' => $item->barang,
                    'satuan' => $item->satuan,
                    'stock_id' => $stockId,
                    'bc_id' => $item->bc_id,
                    'supplier_id' => $item->supplier_id ?? null,
                    'no_aju' => $item->no_aju,
                    'ref_no' => $item->bc_type,
                    'stock_date' => date('Y-m-d', strtotime(str_replace('/', '-', $item->stock_date))),
                    'stock_dokumen' => $item->stock_dokumen,
                    'barang_type' => $item->type_barang,
                    'qty' => $item->qty2,
                    'qty2' => $item->qty2,
                    'qty_isi' => 0,
                    'qty_now' => $item->qty2,
                    'harga_umum' => (float)$item->harga_umum,
                    'harga_harian' => (float)$item->harga_harian,
                    'harga_bulanan' => (float)$item->harga_bulanan,
                    'kondisi_barang' => 'request',
                    'keterangan' => $item->keterangan ?? null,
                ];

                // Handle vendor items differently if needed
                if ($item->type_barang == "bahan_baku" && isset($item->sumber) && $item->sumber == "JASA VENDOR") {
                    $dataMaterialDetail['supplier_id'] = $item->vendor_id ?? null;
                }

                $this->materialRequestDetailsModel->insert($dataMaterialDetail);
            }

            return response()->setJSON([
                "id" => encrypt($id),
                "status" => true,
                "message" => "Data Berhasil disimpan",
                'token' => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                "status" => false,
                "message" => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ]);
        }
    }


    public function update()
    {
        try {
            $id = decrypt($this->request->getPost("id"));
            $mr_detail = json_decode($this->request->getVar("listMaterial"));
            $no = $this->request->getVar('req_no');

            $checkDuplicate = $this->materialRequestModel->where('company_id', $this->this_company_id)
                ->where('req_no', $no)
                ->where('id !=', $id)
                ->where('deletedAt', null)
                ->first();

            if ($checkDuplicate != null) {
                return \response()->setJSON([
                    "status"            => false,
                    "message"    => "No Material Requests sudah ada",
                    'token' => csrf_hash()
                ]);
            }

            $dataMaterial = [
                'work_order_id' => implode(",", $this->request->getVar("kode_produksi")),
                "production_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                "request_date" => $this->request->getVar("date_request") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_request")))) : "",
                "req_no" => $no,
            ];


            $this->materialRequestDetailsModel->where('material_request_id', $id)->where('barang_type', "bahan_baku")->delete();
            $this->materialRequestModel->update($id, $dataMaterial);
            // Delete first

            foreach ($mr_detail as $s) {
                if (!empty($s->id_material_request_detail)) {
                    if ($s->type_barang == "bahan_jadi") {
                        $dataMaterialDetail = [
                            'qty' => $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                            'qty_now' => $s->qty_isi,
                        ];
                    } elseif ($s->type_barang != "bahan_baku") {
                        $dataMaterialDetail = [
                            'qty' => $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                            'qty_now' => $s->qty2
                        ];
                    }
                    $this->materialRequestDetailsModel->update($s->id_material_request_detail, $dataMaterialDetail);
                } else {
                    if ($s->type_barang == "bahan_jadi") {
                        $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                        $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);

                        $dataMaterialDetail = [
                            'material_request_id' => $id,
                            'divisi_id' => $s->departmentID,
                            'warehouse_id' => $s->warehouseID,
                            'divisi_tujuan_id' => $s->departmentTujuanID,
                            'warehouse_tujuan_id' => $s->warehouseTujuanID,
                            'barang1_id' => $stockBarang->barang1_id,
                            'barang2_id' => $stockBarang->barang2_id,
                            'nama_barang' => $s->barang,
                            'satuan' => $s->satuan,
                            'stock_id' => $s->stock_id,
                            'bc_id' => $s->bc_id,
                            'supplier_id' => $s->supplier_id,
                            'no_aju' => $s->no_aju,
                            'ref_no' => $s->bc_type,
                            'stock_date' => $stockDetailBarang->stock_date,
                            'stock_dokumen' => $s->stock_dokumen,
                            'barang_type' => $s->type_barang,
                            'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                            'qty_now' => $s->qty_isi,
                            'harga_umum' => (float)$s->harga_umum,
                            'harga_harian' => (float)$s->harga_harian,
                            'harga_bulanan' => (float)$s->harga_bulanan,
                            'kondisi_barang' => 'request',
                            'keterangan' => $s->keterangan,
                        ];
                        $this->materialRequestDetailsModel->insert($dataMaterialDetail);
                    } elseif ($s->type_barang == "bahan_baku") {

                        // Bahan Baku
                        $stockId = decrypt2($s->id);
                        $stockIdArr = json_decode($stockId);
                        // Hapus Dulu
                        if (is_array($stockIdArr)) {
                            // STOK DARI SUPPLIER
                            $qtyDiambil = 0;


                            foreach ($stockIdArr as $si) {
                                $stockDetail = $this->stockDetail2Model->getStockListDetail(
                                    $si,
                                    $s->bc_id,
                                    $s->no_aju,
                                    $s->stock_dokumen
                                );

                                if ($stockDetail) {
                                    $qtyYangTersedia = $stockDetail['stok_total'];
                                    $qtyYangDiperlukan = $s->qty2 - $qtyDiambil;
                                    $qtyDiambilSekarang = min($qtyYangTersedia, $qtyYangDiperlukan);
                                    $qtyDiambil += $qtyDiambilSekarang;

                                    $this->materialRequestDetailsModel->insert([
                                        'material_request_id' => $id,
                                        'divisi_id' => $s->departmentID,
                                        'warehouse_id' => $s->warehouseID,
                                        'divisi_tujuan_id' => $s->departmentTujuanID,
                                        'warehouse_tujuan_id' => $s->warehouseTujuanID,
                                        'barang1_id' => $stockDetail['barang1_id'],
                                        'barang2_id' => $stockDetail['barang2_id'],
                                        'nama_barang' => $stockDetail['barang'],
                                        'satuan' => $s->satuan,
                                        'stock_id' => $stockDetail['stock_id'],
                                        'bc_id' => $s->bc_id,
                                        'supplier_id' => $stockDetail['supplier_id'],
                                        'no_aju' => $s->no_aju,
                                        'ref_no' => $s->bc_type,
                                        'stock_date' => $stockDetail['stock_date'],
                                        'stock_dokumen' => $s->stock_dokumen,
                                        'barang_type' => $s->type_barang,
                                        'qty' => $qtyDiambilSekarang,
                                        'qty2' => $qtyDiambilSekarang,
                                        'qty_isi' => 0,
                                        'qty_now' => $qtyDiambilSekarang,
                                        'harga_umum' => $stockDetail['harga_umum'],
                                        'harga_harian' => $stockDetail['harga_harian'],
                                        'harga_bulanan' => $stockDetail['harga_bulanan'],
                                        'kondisi_barang' => 'request',
                                        'keterangan' => null,
                                    ]);

                                    if ($qtyDiambil >= $s->qty2) {
                                        break;
                                    }
                                }
                            }
                        } else {
                            $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                            $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);

                            $dataMaterialDetail = [
                                'material_request_id' => $id,
                                'divisi_id' => $s->departmentID,
                                'warehouse_id' => $s->warehouseID,
                                'divisi_tujuan_id' => $s->departmentTujuanID,
                                'warehouse_tujuan_id' => $s->warehouseTujuanID,
                                'barang1_id' => $stockBarang->barang1_id,
                                'barang2_id' => $stockBarang->barang2_id,
                                'nama_barang' => $s->barang,
                                'satuan' => $s->satuan,
                                'stock_id' => $s->stock_id,
                                'bc_id' => $s->bc_id,
                                'supplier_id' => $s->supplier_id,
                                'no_aju' => $s->no_aju,
                                'ref_no' => $s->bc_type,
                                'stock_date' => $stockDetailBarang->stock_date,
                                'stock_dokumen' => $s->stock_dokumen,
                                'barang_type' => $s->type_barang,
                                'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                                'qty2' => $s->qty2,
                                'qty_isi' => $s->qty_isi,
                                'qty_now' => $s->qty2,
                                'harga_umum' => (float)$s->harga_umum,
                                'harga_harian' => (float)$s->harga_harian,
                                'harga_bulanan' => (float)$s->harga_bulanan,
                                'kondisi_barang' => 'request',
                                'keterangan' => $s->keterangan,
                            ];
                            $this->materialRequestDetailsModel->insert($dataMaterialDetail);
                        }
                    } else {
                        $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                        $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);

                        $dataMaterialDetail = [
                            'material_request_id' => $id,
                            'divisi_id' => $s->departmentID,
                            'warehouse_id' => $s->warehouseID,
                            'divisi_tujuan_id' => $s->departmentTujuanID,
                            'warehouse_tujuan_id' => $s->warehouseTujuanID,
                            'barang1_id' => $stockBarang->barang1_id,
                            'barang2_id' => $stockBarang->barang2_id,
                            'nama_barang' => $s->barang,
                            'satuan' => $s->satuan,
                            'stock_id' => $s->stock_id,
                            'bc_id' => $s->bc_id,
                            'supplier_id' => $s->supplier_id,
                            'no_aju' => $s->no_aju,
                            'ref_no' => $s->bc_type,
                            'stock_date' => $stockDetailBarang->stock_date,
                            'stock_dokumen' => $s->stock_dokumen,
                            'barang_type' => $s->type_barang,
                            'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                            'qty_now' => $s->qty2,
                            'harga_umum' => (float)$s->harga_umum,
                            'harga_harian' => (float)$s->harga_harian,
                            'harga_bulanan' => (float)$s->harga_bulanan,
                            'kondisi_barang' => 'request',
                            'keterangan' => $s->keterangan,
                        ];
                        $this->materialRequestDetailsModel->insert($dataMaterialDetail);
                    }
                }
            }

            return response()->setJSON([
                "id"      => encrypt($id),
                "status"  => true,
                "message" => "Data Berhasil disimpan",
                'token'   => csrf_hash(),
            ]);
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

    public function updateNew()
    {
        try {
            $id = decrypt($this->request->getPost("id"));
            $mr_detail = json_decode($this->request->getVar("listMaterial"));

            $no = $this->request->getVar('req_no');

            // Check for duplicate request number
            $checkDuplicate = $this->materialRequestModel->where('company_id', $this->this_company_id)
                ->where('req_no', $no)
                ->where('id !=', $id)
                ->where('deletedAt', null)
                ->first();

            if ($checkDuplicate != null) {
                return response()->setJSON([
                    "status" => false,
                    "message" => "No Material Requests sudah ada",
                    'token' => csrf_hash()
                ]);
            }

            // Update main material request data
            $dataMaterial = [
                'work_order_id' => implode(",", $this->request->getVar("kode_produksi")),
                "production_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                "request_date" => $this->request->getVar("date_request") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_request")))) : "",
                "req_no" => $no,
            ];
            $this->materialRequestModel->update($id, $dataMaterial);

            // Delete existing details for bahan_baku
            $this->materialRequestDetailsModel->where('material_request_id', $id)->where('barang_type', "bahan_baku")->delete();

            // // Process each item in the material request

            foreach ($mr_detail as $item) {
                if ($item->type_barang == "bahan_baku") {
                    // Handle bahan baku items
                    $stockId = $item->stock_id;
                    $stockData = $this->stockModel->asObject()->where('id', $stockId)->first();

                    $dataMaterialDetail = [
                        'material_request_id' => $id,
                        'divisi_id' => $item->departmentID,
                        'warehouse_id' => $item->warehouseID,
                        'divisi_tujuan_id' => $item->departmentTujuanID,
                        'warehouse_tujuan_id' => $item->warehouseTujuanID,
                        'barang1_id' => $stockData->barang1_id,
                        'barang2_id' => $stockData->barang2_id,
                        'nama_barang' => $item->barang,
                        'satuan' => $item->satuan,
                        'stock_id' => $stockId,
                        'bc_id' => $item->bc_id,
                        'supplier_id' => $item->supplier_id ?? null,
                        'no_aju' => $item->no_aju,
                        'ref_no' => $item->bc_type,
                        'stock_date' => date('Y-m-d', strtotime(str_replace('/', '-', $item->stock_date))),
                        'stock_dokumen' => $item->stock_dokumen,
                        'barang_type' => $item->type_barang,
                        'qty' => $item->qty2,
                        'qty2' => $item->qty2,
                        'qty_isi' => 0,
                        'qty_now' => $item->qty2,
                        'harga_umum' => (float)$item->harga_umum,
                        'harga_harian' => (float)$item->harga_harian,
                        'harga_bulanan' => (float)$item->harga_bulanan,
                        'kondisi_barang' => 'request',
                        'keterangan' => $item->keterangan ?? null,
                    ];

                    // Handle vendor items differently if needed
                    if (isset($item->sumber) && $item->sumber == "JASA VENDOR") {
                        $dataMaterialDetail['supplier_id'] = $item->vendor_id ?? null;
                    }

                    $this->materialRequestDetailsModel->insert($dataMaterialDetail);
                } else {
                    // Handle other types (bahan_jadi, etc.)
                    if (!empty($item->id_material_request_detail)) {
                        // Update existing detail
                        $dataMaterialDetail = [
                            'qty' => $item->qty,
                            'qty2' => $item->qty2,
                            'qty_isi' => $item->qty_isi,
                            'qty_now' => ($item->type_barang == "bahan_jadi") ? $item->qty_isi : $item->qty2,
                        ];
                        $this->materialRequestDetailsModel->update($item->id_material_request_detail, $dataMaterialDetail);
                    } else {
                        // Insert new detail
                        $stockData = $this->stockModel->asObject()->find($item->stock_id);
                        $stockDetailData = $this->stockDetailModel->asObject()->find($item->stock_detail_id);

                        $dataMaterialDetail = [
                            'material_request_id' => $id,
                            'divisi_id' => $item->departmentID,
                            'warehouse_id' => $item->warehouseID,
                            'divisi_tujuan_id' => $item->departmentTujuanID,
                            'warehouse_tujuan_id' => $item->warehouseTujuanID,
                            'barang1_id' => $stockData->barang1_id,
                            'barang2_id' => $stockData->barang2_id,
                            'nama_barang' => $item->barang,
                            'satuan' => $item->satuan,
                            'stock_id' => $item->stock_id,
                            'bc_id' => $item->bc_id,
                            'supplier_id' => $item->supplier_id,
                            'no_aju' => $item->no_aju,
                            'ref_no' => $item->bc_type,
                            'stock_date' => $stockDetailData->stock_date,
                            'stock_dokumen' => $item->stock_dokumen,
                            'barang_type' => $item->type_barang,
                            'qty' => $item->qty == 0 ? $item->stok_total : $item->qty,
                            'qty2' => $item->qty2,
                            'qty_isi' => $item->qty_isi,
                            'qty_now' => ($item->type_barang == "bahan_jadi") ? $item->qty_isi : $item->qty2,
                            'harga_umum' => (float)$item->harga_umum,
                            'harga_harian' => (float)$item->harga_harian,
                            'harga_bulanan' => (float)$item->harga_bulanan,
                            'kondisi_barang' => 'request',
                            'keterangan' => $item->keterangan,
                        ];
                        $this->materialRequestDetailsModel->insert($dataMaterialDetail);
                    }
                }
            }

            return response()->setJSON([
                "id" => encrypt($id),
                "status" => true,
                "message" => "Data Berhasil disimpan",
                'token' => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                "status" => false,
                "message" => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function updateStatusPostedMaterialRequest()
    {
        try {

            $id = $this->request->getVar('id');
            $id = decrypt($id);

            // po posting
            $payload = [
                "is_posted" => $this->request->getVar('status_posting') == '1' ? $this->request->getVar('status_posting') : null,
                "note_approve" => $this->request->getVar('keterangan_posting')
            ];

            if (!empty($id)) {

                $this->materialRequestModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "id"    => $this->request->getVar('id'),
                    "message"   => "Status Posting Berhasil Diperbaharui",
                    'token'     => csrf_hash()
                ];

                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "id"    => $this->request->getVar('id'),
                    "message"   => "Data Gagal Disimpan",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteMRDetail()
    {
        $id = ($this->request->getVar('id'));
        $this->materialRequestDetailsModel->delete($id);
        // $this->workOrderDetailsModel->where('work_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "Material Request Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function deleteMR()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->materialRequestModel->delete($id);
        // $this->workOrderDetailsModel->where('work_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "Material Request Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListBarangIsInit()
    {
        $addCondition = [
            "stock.company_id" => $this->this_company_id,
            "barang_master.company_id" => $this->this_company_id
        ];
        if ($this->request->getVar('type_barang') == "bahan_penolong" && $this->request->getVar('kondisi') == "nonkimia") {
            $addCondition = [
                "parent_name !=" => "KIMIA"
            ];
        } else if ($this->request->getVar('type_barang') == "bahan_penolong" && $this->request->getVar('kondisi') == "kimia") {
            $addCondition = [
                "parent_name" => "KIMIA"
            ];
        }
        $data = $this->stockModel->getBarangAndStockCondition(
            $this->request->getVar('type_barang'),
            $this->request->getVar('divisi_id'),
            $this->request->getVar('warehouse_id'),
            $addCondition
        );
        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListSupplier()
    {
        $addCondition = [
            "company_id" => $this->this_company_id,
        ];
        $data = $this->supplierModel->getSupplierByType(
            $this->request->getVar('type_barang') == "bahan_baku" ? "BAHAN BAKU" : "BAHAN PENOLONG",
        );
        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListStockByStockID()
    {
        if (!empty($this->request->getVar('stock_id'))) {
            $isAdjusment = !empty($this->request->getVar('isAdjusment')) ? true : false;
            $supplierId = !empty($this->request->getVar('supplier_id')) ? $this->request->getVar('supplier_id') : null;

            $dataResult = $this->stockDetail2Model->getStockListWithBCDoc(
                $this->request->getVar('stock_id'),
                $isAdjusment,
                $supplierId
            );

            if (!is_array($dataResult)) {
                $dataResult = []; // Ensure $dataResult is an array if the method does not return one
            }

            $stock = $this->stockModel->find($this->request->getVar('stock_id'));
            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $this->barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $this->satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
            } else {
                $kemasan = $this->kemasanModel->find($stock['kemasan_id']);
                $satuan = $this->satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }
            for ($i = 0; $i < count($dataResult); $i++) {
                $bcType = $this->metaDataModel->find($dataResult[$i]['bc_id']);
                $noDaftar = $this->stockDetail2Model->getNomorDaftar($dataResult[$i]['no_aju'], $dataResult[$i]['bc_id']);

                $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                $dataResult[$i]['satuan'] = $satuan['kode_satuan'];
                $dataResult[$i]['barang'] = strtoupper($barangName);
                $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                $dataResult[$i]['type_barang'] = $stock['tipe_barang'];
                $dataResult[$i]['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                $dataResult[$i]['no_daftar'] = $noDaftar;
            }
            return response()->setJSON([
                'data' => $dataResult,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }

    public function printMaterialRequestPDF($id)
    {
        $id = decrypt($id);
        $dompdf = new Dompdf();

        $dataMaterialRequestDetails = $this->materialRequestDetailsModel->asObject()->select('material_request_details.*, barang_master.kode_barang, satuans.kode_satuan, warehouses.warehouse_name as warehouse_text, divisis.divisi as divisi_text')
            ->join('barang_master', 'barang_master.id = material_request_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = material_request_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('warehouses', 'warehouses.id = material_request_details.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = material_request_details.divisi_id', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
            ->where('material_request_id', $id)
            ->where('parent_barang.parent_name !=', "KIMIA")
            ->where('material_request_details.deletedAt', null)
            // ->groupBy('material_request_details.barang1_id, material_request_details.barang2_id, material_request_details.stock_tujuan_id')
            ->get()->getResult();
        foreach ($dataMaterialRequestDetails as $key => &$value) {
            if ($value->barang_type == "bahan_baku") {
                $value->barang_type_text = "Bahan Baku";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->barang_type_text = "Bahan Penolong";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->barang_type_text = "Bahan Jadi";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->barang_type_text = "Bahan Scrap";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->barang_type_text = "Bahan Modal";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->barang_type_text = "Bahan Setengah Jadi";
            }
        }
        $totalQty = 0;
        $totalQty2 = 0;

        foreach ($dataMaterialRequestDetails as $d) {
            $totalQty += $d->qty;
            $totalQty2 += $d->qty2;
        }


        $data  = [
            'data' => $dataMaterialRequestDetails,
            'totalQty' => number_format($totalQty, 2),
            'totalQty2' => number_format($totalQty2, 2)
        ];

        $dompdf->loadHtml(view('Production/materialRequest/printMaterialRequest', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Print Material Request", array("Attachment" => false));

        exit(0);
    }

    public function dropdownListBarangIsInit()
    {

        $data = $this->barangMasterModel->getListBarangmaster(
            $this->request->getVar('type_barang')
        );

        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListStockMaterialRequestBahanBaku()
    {
        $barangMasterId = $this->request->getVar('barang_master_id');
        $supplierId = $this->request->getVar('supplier_id');
        $vendorId = $this->request->getVar('vendor_id');
        $divisiAsalId = $this->request->getVar('divisi_asal_bahan_baku_id');
        $warehouseAsalId = $this->request->getVar('warehouse_asal_bahan_baku_id');

        if ((!empty($this->request->getVar('stock_id')) || !empty($this->request->getVar('barang_master_id'))) && (!empty($supplierId) || !empty($vendorId))) {

            if (!empty($supplierId) && !empty($barangMasterId)) {
                // Untuk Dari Po & Supplier
                $condition = [
                    'stock.divisi_id' => $divisiAsalId,
                    'stock.warehouse_id' => $warehouseAsalId,
                    'stock_details2.supplier_id' => $this->request->getVar('supplier_id'),
                    'stock.barang1_id' => $barangMasterId,
                ];

                $dataResult = $this->stockDetail2Model->getStockListMaterialRequestFromLpb(
                    $condition
                );
            } else {
                // Untuk Dari Jasa Vendor
                $condition = [
                    'stock.divisi_id' => $divisiAsalId,
                    'stock.warehouse_id' => $warehouseAsalId,
                    'stock.barang1_id' => $barangMasterId,
                    'vendor_id' => $vendorId
                ];

                $dataResult = $this->stockDetail2Model->getStockListMaterialRequestFromJasaVendor(
                    $condition
                );
            }


            $resultArr = array();

            if (!empty($supplierId)) {
                // Khsus Dari Supplier
                for ($i = 0; $i < count($dataResult); $i++) {
                    // $bcType = $this->metaDataModel->find($dataResult[$i]['bc_id']);
                    // $stock = $this->stockModel->find($dataResult[$i]['stock_id']);

                    // $rmPurchaseOrder = $this->rmPurchaseOrderModel->where('po_no', $dataResult[$i]['stock_dokumen'])
                    //     ->where('company_id', $stock['company_id'])
                    //     ->first();

                    // $stockDetail = $this->stockDetail2Model->getStockListDetail(
                    //     $dataResult[$i]['stock_id'],
                    //     $dataResult[$i]['bc_id'],
                    //     $dataResult[$i]['no_aju'],
                    //     $dataResult[$i]['stock_dokumen']
                    // );

                    $noDaftar = "";

                    if (!empty($dataResult[$i]['no_daftar_bc23'])) {
                        $noDaftar = $dataResult[$i]['no_daftar_bc23'];
                    } elseif (!empty($dataResult[$i]['no_daftar_bc27'])) {
                        $noDaftar = $dataResult[$i]['no_daftar_bc27'];
                    } elseif (!empty($dataResult[$i]['no_daftar_bc40'])) {
                        $noDaftar = $dataResult[$i]['no_daftar_bc40'];
                    } elseif (!empty($dataResult[$i]['no_daftar_ppbkb'])) {
                        $noDaftar = $dataResult[$i]['no_daftar_ppbkb'];
                    }
                    $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                    $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                    $dataResult[$i]['bc_type'] = $dataResult[$i]['bc_type'] == null ? "NON PABEAN" : $dataResult[$i]['bc_type'];
                    $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                    $dataResult[$i]['barang'] = $dataResult[$i]['barang_name'];
                    $dataResult[$i]['sepsifikasi'] = $dataResult[$i]['spesifikasi'];
                    $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['type_barang'] = "bahan_baku";
                    $dataResult[$i]['type_barang_text'] = "BAHAN BAKU";
                    $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                    $dataResult[$i]['no_daftar'] = $noDaftar;

                    if ($dataResult[$i]['stok_total'] > 0) {
                        array_push($resultArr, $dataResult[$i]);
                    }
                }

                $grouped = [];

                foreach ($resultArr as $item) {
                    $key = $item['stock_dokumen'] . '|' . $item['stock_date'];
                    $spec = $item['spesifikasi'] ?? $item['sepsifikasi'] ?? '';
                    $barang_name = $item['barang_name'];

                    if (!isset($grouped[$key])) {
                        $grouped[$key] = [
                            'sumber' => $item['sumber'],
                            'supplier_name' => $item['supplier_name'],
                            'bc_type' => $item['bc_type'],
                            'no_aju' => $item['no_aju'],
                            'satuan' => $item['satuan'],
                            'stock_dokumen' => $item['stock_dokumen'],
                            'stock_date' => $item['stock_date'],
                            'stok_total' => 0,
                            'id' => [],
                            'barang_name' => $barang_name,
                            'spesifikasi_list' => [],
                            'bc_id' => $item['bc_id'],
                            'no_daftar' => $item['no_daftar'],
                            'type_barang' => $item['type_barang'],
                            'type_barang_text' => $item['type_barang_text']
                        ];
                    }

                    $grouped[$key]['stok_total'] += $item['stok_total'];
                    $grouped[$key]['id'][] = $item['stock_id'];
                    $grouped[$key]['spesifikasi_list'][] = $spec;
                }

                // Hilangkan duplikat `id` dan `spesifikasi`, lalu susun ulang nama barang
                foreach ($grouped as &$group) {
                    $group['stok_total'] = floatval(number_format($group['stok_total'], 2));
                    $group['id'] = encrypt2(json_encode(array_values(array_unique($group['id']))));
                    $group['spesifikasi_list'] = array_unique($group['spesifikasi_list']);
                    $group['barang'] = trim($group['barang_name'] . ' ' . implode(', ', $group['spesifikasi_list']));
                    unset($group['barang_name'], $group['spesifikasi_list']); // opsional, kalau mau lebih ringkas
                }


                $resultArr =  array_values($grouped);
            } else {

                // Grupp
                for ($i = 0; $i < count($dataResult); $i++) {

                    $noDaftar = "";

                    if (!empty($dataResult[$i]['no_daftar_bc23'])) {
                        $noDaftar = $dataResult[$i]['no_daftar_bc23'];
                    } elseif (!empty($dataResult[$i]['no_daftar_bc27'])) {
                        $noDaftar = $dataResult[$i]['no_daftar_bc27'];
                    } elseif (!empty($dataResult[$i]['no_daftar_bc40'])) {
                        $noDaftar = $dataResult[$i]['no_daftar_bc40'];
                    } elseif (!empty($dataResult[$i]['no_daftar_ppbkb'])) {
                        $noDaftar = $dataResult[$i]['no_daftar_ppbkb'];
                    }

                    $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                    $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                    $dataResult[$i]['bc_type'] = $dataResult[$i]['bc_type'] == null ? "NON PABEAN" : $dataResult[$i]['bc_type'];
                    $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                    $dataResult[$i]['barang'] = $dataResult[$i]['barang_name'];
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                    $dataResult[$i]['type_barang'] = "bahan_baku";
                    $dataResult[$i]['type_barang_text'] = "BAHAN BAKU";
                    $dataResult[$i]['supplier_name'] = $dataResult[$i]['supplier_name'] . ' / ' . $dataResult[$i]['nama_vendor'];
                    $dataResult[$i]['id'] = encrypt($dataResult[$i]['id']);
                    $dataResult[$i]['no_daftar'] = $noDaftar;

                    if ($dataResult[$i]['stok_total'] > 0) {
                        array_push($resultArr, $dataResult[$i]);
                    }
                }



                $grouped = [];

                foreach ($resultArr as $item) {
                    $key = $item['stock_dokumen'] . '|' . $item['stock_date'];
                    $spec = $item['spesifikasi'] ?? $item['sepsifikasi'] ?? '';
                    $barang_name = $item['barang_name'];

                    if (!isset($grouped[$key])) {
                        $grouped[$key] = [
                            'sumber' => "JASA VENDOR",
                            'supplier_name' => $item['supplier_name'],
                            'bc_type' => $item['bc_type'],
                            'no_aju' => $item['no_aju'],
                            'satuan' => $item['satuan'],
                            'stock_dokumen' => $item['stock_dokumen'],
                            'stock_date' => $item['stock_date'],
                            'stok_total' => 0,
                            'id' => [],
                            'barang_name' => $barang_name,
                            'spesifikasi_list' => [],
                            'bc_id' => $item['bc_id'],
                            'no_daftar' => $item['no_daftar'],
                            'type_barang' => $item['type_barang'],
                            'type_barang_text' => $item['type_barang_text']
                        ];
                    }

                    $grouped[$key]['stok_total'] += $item['stok_total'];
                    $grouped[$key]['id'][] = $item['stock_id'];
                    $grouped[$key]['spesifikasi_list'][] = $spec;
                }

                // Hilangkan duplikat `id` dan `spesifikasi`, lalu susun ulang nama barang
                foreach ($grouped as &$group) {
                    $group['stok_total'] = floatval(number_format($group['stok_total'], 2));
                    $group['id'] = encrypt2(json_encode(array_values(array_unique($group['id']))));
                    $group['spesifikasi_list'] = array_unique($group['spesifikasi_list']);
                    $group['barang'] = trim($group['barang_name'] . ' ' . implode(', ', $group['spesifikasi_list']));
                    unset($group['barang_name'], $group['spesifikasi_list']); // opsional, kalau mau lebih ringkas
                }


                $resultArr =  array_values($grouped);
            }


            return response()->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }


    public function getListStockMaterialRequestBahanBakuNew()
    {
        $barangMasterId = $this->request->getVar('barang_master_id');
        $supplierId = $this->request->getVar('supplier_id');
        $vendorId = $this->request->getVar('vendor_id');
        $divisiAsalId = $this->request->getVar('divisi_asal_bahan_baku_id');
        $warehouseAsalId = $this->request->getVar('warehouse_asal_bahan_baku_id');
        $typeAsalBarang = $this->request->getVar('type_asal_barang');

        if ((!empty($this->request->getVar('stock_id')) || !empty($this->request->getVar('barang_master_id'))) || !empty($typeAsalBarang)) {

            if (!empty($typeAsalBarang) && $typeAsalBarang == "SUPPLIER") {
                if (!empty($supplierId)) {
                    // Untuk Dari Po & Supplier
                    $condition = [
                        'stock.divisi_id' => $divisiAsalId,
                        'stock.warehouse_id' => $warehouseAsalId,
                        'stock_details2.supplier_id' => $this->request->getVar('supplier_id'),
                        'stock.barang1_id' => $barangMasterId,
                    ];
                } else {
                    $condition = [
                        'stock.divisi_id' => $divisiAsalId,
                        'stock.warehouse_id' => $warehouseAsalId,
                        'stock.barang1_id' => $barangMasterId,
                    ];
                }

                $dataResult = $this->stockDetail2Model->getStockListMaterialRequestFromLpb(
                    $condition
                );
            } else {
                // Untuk Dari Jasa Vendor
                $condition = [
                    'stock.divisi_id' => $divisiAsalId,
                    'stock.warehouse_id' => $warehouseAsalId,
                    'stock.barang1_id' => $barangMasterId,
                    'vendor_id' => $vendorId
                ];

                $dataResult = $this->stockDetail2Model->getStockListMaterialRequestFromJasaVendor(
                    $condition
                );
            }

            $resultArr = array();

            for ($i = 0; $i < count($dataResult); $i++) {
                $noDaftar = "";

                if (!empty($dataResult[$i]['no_daftar_bc23'])) {
                    $noDaftar = $dataResult[$i]['no_daftar_bc23'];
                } elseif (!empty($dataResult[$i]['no_daftar_bc27'])) {
                    $noDaftar = $dataResult[$i]['no_daftar_bc27'];
                } elseif (!empty($dataResult[$i]['no_daftar_bc40'])) {
                    $noDaftar = $dataResult[$i]['no_daftar_bc40'];
                } elseif (!empty($dataResult[$i]['no_daftar_ppbkb'])) {
                    $noDaftar = $dataResult[$i]['no_daftar_ppbkb'];
                }

                $spesifikasi = isset($dataResult[$i]['spesifikasi']) ? $dataResult[$i]['spesifikasi'] : '';
                $barangName = $dataResult[$i]['barang_name'];

                if (empty($vendorId)) {
                    $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                    $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                    $dataResult[$i]['bc_type'] = $dataResult[$i]['bc_type'] == null ? "NON PABEAN" : $dataResult[$i]['bc_type'];
                    $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                    $dataResult[$i]['barang'] = $barangName . (!empty($spesifikasi) ? " - " . $spesifikasi : "");
                    $dataResult[$i]['sepsifikasi'] = $dataResult[$i]['spesifikasi'];
                    $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['type_barang'] = "bahan_baku";
                    $dataResult[$i]['type_barang_text'] = "BAHAN BAKU";
                    $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                    $dataResult[$i]['no_daftar'] = $noDaftar;
                    $dataResult[$i]['id'] = encrypt($dataResult[$i]['stock_id']) . '-' . encrypt($dataResult[$i]['id']);
                } else {
                    $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                    $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                    $dataResult[$i]['bc_type'] = $dataResult[$i]['bc_type'] == null ? "NON PABEAN" : $dataResult[$i]['bc_type'];
                    $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                    $dataResult[$i]['barang'] = $barangName . (!empty($spesifikasi) ? " - " . $spesifikasi : "");
                    $dataResult[$i]['sepsifikasi'] = $dataResult[$i]['spesifikasi'];
                    $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['type_barang'] = "bahan_baku";
                    $dataResult[$i]['type_barang_text'] = "BAHAN BAKU";
                    $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                    $dataResult[$i]['no_daftar'] = $noDaftar;
                    $dataResult[$i]['id'] = encrypt($dataResult[$i]['stock_id']) . '-' . encrypt($dataResult[$i]['id']);
                    $dataResult[$i]['supplier_name'] = $dataResult[$i]['supplier_name'] . ' / ' . $dataResult[$i]['nama_vendor'];
                    $dataResult[$i]['sumber'] = "JASA VENDOR";
                }

                $dataMaterialRequestNotApprove = $this->materialRequestDetailsModel->getMaterialRequestNotApprove(
                    $dataResult[$i]['stock_id'],
                    $dataResult[$i]['bc_id'],
                    $dataResult[$i]['no_aju'],
                    date('Y-m-d', strtotime(str_replace('/', '-', $dataResult[$i]['stock_date']))),
                    $dataResult[$i]['stock_dokumen']
                );

                if ($dataMaterialRequestNotApprove) {
                    $dataResult[$i]['is_requested'] = true;
                    $dataResult[$i]['stok_total'] = sprintf(
                        '%.2f',
                        floor(
                            max(
                                0,
                                $dataResult[$i]['stok_total'] - $dataMaterialRequestNotApprove['qty']
                            ) * 100
                        ) / 100
                    );
                } else {
                    $dataResult[$i]['is_requested'] = false;
                }

                if ($dataResult[$i]['stok_total'] > 0) {
                    array_push($resultArr, $dataResult[$i]);
                }
            }

            return response()->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }
}
