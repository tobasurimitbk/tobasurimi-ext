<?php

namespace App\Controllers\Production;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BarangModel;
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
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;
use Exception;

class MaterialRequestPenolong extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $barangModel;
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

    protected $jurnalUmumController;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangModel = new BarangModel();
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

        $this->jurnalUmumController = new JurnalUmum();
    }

    public function index()
    {
        return view('Production/materialRequestPenolong/index');
    }

    public function createView()
    {
        //Get Barang
        $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

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
            ->where('description', "bahan_penolong")
            ->findAll();

        $data = [
            'tipeBarang' => $dataTipeBarang,
            "dataBarang" => $dataBarang,
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
        //Get Barang
        $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $dataWarehouse = $this->warehousesModel->asObject()->where('company_id', $this->this_company_id)->find();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataWorkOrder = $this->workOrdersModel->asObject()
            ->select('work_orders.*, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR \', \') AS nama_barang')
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('work_orders.is_posted', "0")
            ->where('work_orders.deletedAt', null)
            ->where('work_order_details.deletedAt', null)
            ->groupBy('work_order_details.work_order_id')
            ->find();

        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->where('description', "bahan_penolong")->findAll(),
            "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
            "dataWorkOrder" => $dataWorkOrder,
        ];

        if (!empty($id)) {
            $dataMaterialRequests = $this->materialRequestModel->asObject()->find($id);
            $dataMaterialRequestswithwo = $this->materialRequestModel->getMaterialWithWorkOrder($id);
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
            $data["dataMaterialRequests"] = $dataMaterialRequests;
            $data["dataMaterialRequestDetails"] = $dataMaterialRequestDetails;
            $data["dataMaterialRequestswithwo"] = $dataMaterialRequestswithwo;
            $data["ids"] = $ids;
        }

        // var_dump($data);
        // exit;

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
                "wo_no"           => $data->wo_no,
                "is_posted"           => $data->is_posted,
                "request_status"           => $data->request_status,
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
                // "work_order_id" => $this->request->getPost("kode_produksi"),
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
                        'qty' => $s->qty,
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
                        'qty' => $s->qty,
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
                            'qty' => $s->qty,
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
                            'qty' => $s->qty,
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
                $materialRequestData = $this->materialRequestModel->find($id);
                $materialRequestDetailData = $this->materialRequestDetailsModel->where('material_request_id', $id)->findAll();

                foreach ($materialRequestDetailData as $key => $value) {
                    // $statusOUT = $this->jurnalUmumController->TransaksiJurnalStockBarang($this->this_company_id, $value['divisi_id'], $value['barang1_id'], $value['barang2_id'], $value['barang_type'], $value['stock_dokumen'], 'OUT');
                    // if ($statusOUT) {
                    //     $responseBody = json_decode($statusOUT->getBody(), true);
                    //     $data = [
                    //         "status"    => false,
                    //         "id"    => $this->request->getVar('id'),
                    //         "message"   => $responseBody['message'],
                    //         "payload"   => json_encode($payload),
                    //         'token'     => csrf_hash()
                    //     ];
                    //     echo json_encode($data);
                    //     return;
                    // } else {
                    $stok = $this->stockModel->insertStok(
                        $materialRequestData['company_id'],
                        $value['warehouse_id'],
                        $value['divisi_id'],
                        $value['barang_type'],
                        $value['barang1_id'],
                        $value['barang2_id'],
                        ($value['qty2'] * -1)
                    );

                    // DETAIL
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        $value['qty2'],
                        "Out",
                        date('Y-m-d'),
                        $this->this_user_id,
                        "PRODUKSI",
                        $materialRequestData['req_no'],
                        $value['note'] ? $value['note'] : "-"
                    );

                    // SUB DETAIL
                    $this->stockDetail2Model->insertStokDetail2(
                        $value['bc_id'],
                        $value['stock_id'],
                        $stokDetail,
                        $value['qty2'],
                        $value['no_aju'],
                        $materialRequestData['req_no'],
                        $value['stock_dokumen'],
                    );
                    // }

                    // -----
                    // BARANG IN KE INVENTORI
                    // $stokIn = $this->stockModel->insertStok(
                    //     $materialRequestData['company_id'],
                    //     $value['warehouse_tujuan_id'],
                    //     $value['divisi_tujuan_id'],
                    //     $value['barang_type'],
                    //     $value['barang1_id'],
                    //     $value['barang2_id'],
                    //     $value['qty2']
                    // );

                    // $checkStokDetailIn =  $this->stockModel->isDefinedStockSubDetail(
                    //     $materialRequestData['company_id'],
                    //     $value['warehouse_tujuan_id'],
                    //     $value['divisi_tujuan_id'],
                    //     $value['barang_type'],
                    //     $value['barang1_id'],
                    //     $value['barang2_id'],
                    //     $value['bc_id'],
                    //     $value['no_aju'],
                    //     $stokIn
                    // );

                    // if ($checkStokDetailIn == null) {
                    //     // INSERT STOK INISIASI
                    //     $stokDetailIn = $this->stockDetailModel->insertStokDetail(
                    //         $stokIn,
                    //         0,
                    //         "In",
                    //         date('Y-m-d'),
                    //         $this->this_user_id,
                    //         "INISIASI",
                    //         "-",
                    //         "-"
                    //     );
                    //     $this->stockDetail2Model->insertStokDetail2(
                    //         $value['bc_id'],
                    //         $value['stock_id'],
                    //         $stokDetailIn,
                    //         0,
                    //         $value['no_aju'],
                    //         "-"
                    //     );
                    // }

                    // $stockRebusDetailIn = $this->stockDetail2Model->getStockListDetail(
                    //     $value['bc_id'],
                    //     $value['stock_id'],
                    //     $value['no_aju'],
                    //     $value['stock_dokumen']
                    // );

                    // // DETAIL
                    // $stokDetailIn = $this->stockDetailModel->insertStokDetail(
                    //     $stokIn,
                    //     $value['qty2'],
                    //     "In",
                    //     date('Y-m-d'),
                    //     $this->this_user_id,
                    //     "PRODUKSI",
                    //     $materialRequestData['req_no'],
                    //     $value['note'] ? $value['note'] : "-"
                    // );

                    // // SUB DETAIL
                    // $this->stockDetail2Model->insertStokDetail2(
                    //     $value['bc_id'],
                    //     $stokIn,
                    //     $stokDetailIn,
                    //     $value['qty2'],
                    //     $value['no_aju'],
                    //     $materialRequestData['req_no'],
                    //     $value['stock_dokumen'],
                    // );
                }

                // $this->workOrdersModel->update($materialRequestData['work_order_id'], [
                //     'is_posted' => 1
                // ]);
                $data = [
                    "status"    => true,
                    "id"    => $this->request->getVar('id'),
                    "message"   => "Status Posting Berhasil Diperbaharui",
                    "payload"   => json_encode($payload),
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

    public function deleteMR()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->materialRequestDetailsModel->delete($id);
        // $this->workOrderDetailsModel->where('work_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "Material Request Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListBarangIsInit()
    {
        $addCondition = [];
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
}
