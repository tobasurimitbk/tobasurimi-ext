<?php

namespace App\Controllers\Production;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestPenolongDetailsModel;
use App\Models\MaterialRequestsModel;
use App\Models\MaterialRequestsPenolongModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;
use Dompdf\Dompdf;
use Exception;

class MaterialRequestPenolong extends BaseController
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
    protected $stockRevampDetailModel;
    protected $stockRevampModel;

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
        $this->materialRequestModel = new MaterialRequestsPenolongModel();
        $this->materialRequestDetailsModel = new MaterialRequestPenolongDetailsModel();
        $this->stockRevampDetailModel = new StockRevampDetailModel();
        $this->stockRevampModel = new StockRevampModel();

        $this->jurnalUmumController = new JurnalUmum();
    }

    public function index()
    {
        return view('Production/materialRequestPenolong/index');
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
            ->orderBy('work_orders.id', 'DESC')
            ->find();
        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description', "bahan_penolong")
            ->findAll();

        $data = [
            'tipeBarang' => $dataTipeBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
            "dataWorkOrder" => $dataWorkOrder,
        ];

        return view('Production/materialRequestPenolong/form', $data);
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

        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->where('description', "bahan_penolong")->findAll(),
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
            "dataWorkOrder" => $dataWorkOrder,
        ];

        if (!empty($id)) {
            $dataMaterialRequests = $this->materialRequestModel->asObject()->find($id);
            $dataMaterialRequestswithwo = $this->materialRequestModel->getMaterialWithWorkOrder($id);
            $dataMaterialRequestDetails = $this->materialRequestDetailsModel->asObject()
                ->select('material_request_penolong_details.*, 
                        barang_master.kode_barang, 
                        satuans.kode_satuan, 
                        warehouses.warehouse_name as warehouse_text, 
                        divisis.divisi as divisi_text
                ')
                ->join('barang_master', 'barang_master.id = material_request_penolong_details.barang1_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = material_request_penolong_details.barang2_id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->join('warehouses', 'warehouses.id = material_request_penolong_details.warehouse_id', 'left')
                ->join('divisis', 'divisis.id = material_request_penolong_details.divisi_id', 'left')
                ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
                ->where('material_request_id', $id)
                ->where('parent_barang.parent_name !=', "KIMIA")
                ->where('material_request_penolong_details.deletedAt', null)
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
                }
                $dataMaterialRequestNotApprove = $this->materialRequestDetailsModel->getMaterialRequestNotApprove(
                    $value->stock_id,
                    $value->bc_id,
                    $value->no_aju,
                    date('Y-m-d', strtotime(str_replace('/', '-', $value->stock_date))),
                    $value->stock_dokumen
                );

                if ($dataMaterialRequestNotApprove) {
                    $value->is_requested = true;
                    $datas = [
                        'stock_revamp_detail.id' => $value->stock_detail_id,
                    ];

                    $stokTotal = $this->stockRevampDetailModel->getStockListWithCondition($datas)[0]['stok_total'];

                    $selisih = $stokTotal - (float) $dataMaterialRequestNotApprove['qty'];

                    $value->realStok = round(max(0, $selisih), 2);
                    $value->stok_total = $stokTotal;
                } else {
                    $value->is_requested = false;
                }
            }
            $data["dataMaterialRequests"] = $dataMaterialRequests;
            $data["dataMaterialRequestDetails"] = $dataMaterialRequestDetails;
            $data["dataMaterialRequestswithwo"] = isset($dataMaterialRequestswithwo[0]) ? $dataMaterialRequestswithwo[0] : null;
            $data["ids"] = $ids;
        }

        return view('Production/materialRequestPenolong/form', $data);
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
            'material_requests_penolong.company_id' => $this->this_company_id,
            'parent_barang.parent_name !=' => "KIMIA"
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
                "nama_barang"           => $data->nama_barang,
                "wo_no"           => "",
                "is_posted"           => $data->is_posted,
                "request_status"           => "",
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
            "material_requests_penolong.id"        => decrypt($this->request->getGet("id"))
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
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            $no = $this->materialRequestModel->get_no(date('d'), date('m'), date('Y'), $last_day, $this->this_company_id);

            $dataMaterial = [
                "work_order_id" => $this->request->getPost("kode_produksi"),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                'warehouse_id' => $this->request->getVar("warehouse_id"),
                "production_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                "request_date" => $this->request->getVar("date_request") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_request")))) : "",
                "req_no" => $this->request->getVar("req_no"),
                'is_posted' => 0,
                'createdBy' =>  session()->get("login")->user_id,
            ];
            $id = $this->materialRequestModel->insert($dataMaterial);

            $mr_detail = json_decode($this->request->getVar("listMaterial"));
            // var_dump($mr_detail);
            // exit;

            foreach ($mr_detail as $s) {
                $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);
                if ($s->type_barang == "bahan_jadi") {
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
                        'no_aju' => $s->no_aju,
                        'ref_no' => $s->bc_type,
                        'stock_date' => $stockDetailBarang->stock_date,
                        'stock_dokumen' => $s->stock_dokumen,
                        'barang_type' => $s->type_barang,
                        'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                        'qty2' => $s->qty2,
                        'qty_isi' => $s->qty_isi,
                    ];
                } else {
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
                        'no_aju' => $s->no_aju,
                        'ref_no' => $s->bc_type,
                        'stock_date' => $stockDetailBarang->stock_date,
                        'stock_dokumen' => $s->stock_dokumen,
                        'barang_type' => $s->type_barang,
                        'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                        'qty2' => $s->qty2,
                        'qty_isi' => $s->qty_isi,
                    ];
                }
                $this->materialRequestDetailsModel->insert($dataMaterialDetail);
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

            $mr_detail = json_decode($this->request->getVar("listMaterial"));

            // var_dump($mr_detail, $dataMaterial);
            // exit;
            $id = $this->materialRequestModel->insert($dataMaterial);

            foreach ($mr_detail as $item) {
                $stockId = ($item->stock_id);
                $stockDetailId = ($item->stock_detail_id);
                $stockData = $this->stockRevampModel->asObject()->where('id', $stockId)->first();

                $dataMaterialDetail = [
                    'material_request_id' => $id,
                    'divisi_id' => $item->departmentID,
                    'warehouse_id' => $item->warehouseID,
                    'divisi_tujuan_id' => $item->departmentTujuanID,
                    'warehouse_tujuan_id' => $item->warehouseTujuanID,
                    'barang1_id' => $stockData->barang_master_id,
                    'barang2_id' => $stockData->spesifikasi_id,
                    'nama_barang' => $item->barang,
                    'satuan' => $item->satuan,
                    'stock_id' => $stockId,
                    'stock_detail_id' => $stockDetailId,
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
                    'note' => $item->note ?? null,
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
            // var_dump($mr_detail);
            // exit;

            foreach ($mr_detail as $s) {
                if (!empty($s->id_material_request_detail)) {
                    if ($s->type_barang == "bahan_jadi") {
                        $dataMaterialDetail = [
                            'qty' => $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                        ];
                    } else {
                        $dataMaterialDetail = [
                            'qty' => $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                        ];
                    }
                    $this->materialRequestDetailsModel->update($s->id_material_request_detail, $dataMaterialDetail);
                } else {
                    $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                    $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);
                    if ($s->type_barang == "bahan_jadi") {
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
                            'no_aju' => $s->no_aju,
                            'ref_no' => $s->bc_type,
                            'stock_date' => $stockDetailBarang->stock_date,
                            'stock_dokumen' => $s->stock_dokumen,
                            'barang_type' => $s->type_barang,
                            'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                        ];
                    } else {
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
                            'no_aju' => $s->no_aju,
                            'ref_no' => $s->bc_type,
                            'stock_date' => $stockDetailBarang->stock_date,
                            'stock_dokumen' => $s->stock_dokumen,
                            'barang_type' => $s->type_barang,
                            'qty' => $s->qty == 0 ? $s->stok_total : $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                        ];
                    }
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

    public function updateNew()
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
                return response()->setJSON([
                    "status" => false,
                    "message" => "No Material Requests sudah ada",
                    'token' => csrf_hash()
                ]);
            }

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
                    $stockData = $this->stockRevampModel->asObject()->where('id', $stockId)->first();

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
                        'note' => $item->note ?? null,
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
                        $stockData = $this->stockRevampModel->asObject()->find($item->stock_id);
                        $stockDetailData = $this->stockRevampDetailModel->asObject()->find($item->stock_detail_id);

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
                            'note' => $item->note ?? null,
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
            "stock_revamp.company_id" => $this->this_company_id,
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
        $data = $this->stockRevampDetailModel->getBarangAndStockCondition(
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

    public function printMaterialRequestPenolongPDF($id)
    {
        $id = decrypt($id);
        $dompdf = new Dompdf();

        $dataMaterialRequestDetails = $this->materialRequestDetailsModel->asObject()->select('material_request_penolong_details.*, barang_master.kode_barang, satuans.kode_satuan, warehouses.warehouse_name as warehouse_text, divisis.divisi as divisi_text')
            ->join('barang_master', 'barang_master.id = material_request_penolong_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = material_request_penolong_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('warehouses', 'warehouses.id = material_request_penolong_details.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = material_request_penolong_details.divisi_id', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
            ->where('material_request_id', $id)
            ->where('parent_barang.parent_name !=', "KIMIA")
            ->where('material_request_penolong_details.deletedAt', null)
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

        $dompdf->loadHtml(view('Production/materialRequestPenolong/printMaterialRequestPenolong', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Print Material Request Penolong", array("Attachment" => false));

        exit(0);
    }

    public function generateKodeRequest()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $no = $this->materialRequestModel->get_no(date('d'), date('m'), date('Y'), $last_day, $this->this_company_id);

        return json_encode($no);
    }

    public function getListStockByStockID()
    {
        if (!empty($this->request->getVar('stock_id'))) {
            $datas = [
                'stock_revamp.id' => $this->request->getVar('stock_id'),
            ];

            $dataResult = $this->stockRevampDetailModel->getStockListPenolongWithCondition($datas);

            if (!is_array($dataResult)) {
                $dataResult = []; // Ensure $dataResult is an array if the method does not return one
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

                $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                $dataResult[$i]['supplier_name'] = !isset($dataResult[$i]['supplier_name']) ? "-" : $dataResult[$i]['supplier_name'];
                $dataResult[$i]['id'] = encrypt($dataResult[$i]['stock_id']) . '-' . encrypt($dataResult[$i]['id']);
                $dataResult[$i]['bc_id'] =  $dataResult[$i]['bc_id'] == "-" ? "-" : $dataResult[$i]['bc_id'];
                $dataResult[$i]['no_aju'] =  empty($dataResult[$i]['no_aju']) ? "-" : $dataResult[$i]['no_aju'];
                $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] ?? "-";
                $dataResult[$i]['no_dokumen_2'] = $dataResult[$i]['stock_dokumen'] ?? "-";
                $dataResult[$i]['supplier_id'] = null;
                $dataResult[$i]['harga_umum'] = $dataResult[$i]['harga_umum'] == null ? "0" : $dataResult[$i]['harga_umum'];
                $dataResult[$i]['harga_harian'] = $dataResult[$i]['harga_harian'] == null ? "0" : $dataResult[$i]['harga_harian'];
                $dataResult[$i]['harga_bulanan'] = $dataResult[$i]['harga_bulanan'] == null ? "0" : $dataResult[$i]['harga_bulanan'];
                $dataResult[$i]['no_po'] = "-";
                $dataResult[$i]['barang_name'] = $dataResult[$i]['barang_name'] == null ? "-" : $dataResult[$i]['barang_name'];
                $dataResult[$i]['spesifikasi'] = $dataResult[$i]['spesifikasi'] == null ? "-" : $dataResult[$i]['spesifikasi'];
                $dataResult[$i]['kode_satuan'] = $dataResult[$i]['kode_satuan'] == null ? "-" : $dataResult[$i]['kode_satuan'];
                $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                $dataResult[$i]['bc_type'] = $dataResult[$i]['type_bc'] == null ? "NON PABEAN" : $dataResult[$i]['type_bc'];
                $dataResult[$i]['no_daftar'] = $dataResult[$i]['no_daftar'] == null ? "-" : $dataResult[$i]['no_daftar'];
                $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total_diterima']);
                $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                $dataResult[$i]['barang'] = $dataResult[$i]['barang'];
                $dataResult[$i]['sepsifikasi'] = $dataResult[$i]['spesifikasi'];
                $dataResult[$i]['type_barang'] = $dataResult[$i]['type_barang'];
                $dataResult[$i]['type_barang_text'] = strtoupper(str_replace("_", " ", $dataResult[$i]['type_barang']));
                $dataResult[$i]['supplier_id'] = $dataResult[$i]['supplier_id'] ?? null;
                $dataResult[$i]['stock_detail_id'] = $dataResult[$i]['stock_detail_id'];
                $dataResult[$i]['sumber'] = "LPB";

                $dataMaterialRequestNotApprove = $this->materialRequestDetailsModel->getMaterialRequestNotApprove(
                    $dataResult[$i]['stock_id'],
                    $dataResult[$i]['stock_detail_id'],
                    $dataResult[$i]['no_aju'],
                    date('Y-m-d', strtotime(str_replace('/', '-', $dataResult[$i]['stock_date']))),
                    $dataResult[$i]['stock_dokumen']
                );

                if ($dataMaterialRequestNotApprove) {
                    $dataResult[$i]['is_requested'] = true;
                    $dataResult[$i]['stok_total'] = round(max(0, (float)$dataResult[$i]['stok_total'] - (float)$dataMaterialRequestNotApprove['qty']), 2);
                } else {
                    $dataResult[$i]['is_requested'] = false;
                }

                if ($dataResult[$i]['stok_total'] > 0.01) {
                    array_push($resultArr, $dataResult[$i]);
                }
            }
            return response()->setJSON([
                'data' => $dataResult,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }
}
