<?php

namespace App\Controllers\Production;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestsModel;
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

        $data = [
            'tipeBarang' => $dataTipeBarang,
            // "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
            "dataWorkOrder" => $dataWorkOrder,
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
        // $dataWorkOrder = $this->workOrdersModel->asObject()
        //     ->select('work_orders.*, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR \', \') AS nama_barang')
        //     ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
        //     ->where('company_id', $this->this_company_id)
        //     ->where('work_orders.is_posted', "0")
        //     ->where('work_orders.deletedAt', null)
        //     ->where('work_order_details.deletedAt', null)
        //     ->groupBy('work_order_details.work_order_id')
        //     ->find();

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
        ];

        if (!empty($id)) {
            $dataMaterialRequests = $this->materialRequestModel->asObject()->find($id);
            $dataMaterialRequest = $this->materialRequestModel->getMaterial($id);
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
            $data["dataMaterialRequests"] = $dataMaterialRequests;
            $data["dataMaterialRequestDetails"] = $dataMaterialRequestDetails;
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
                // "nama_barang"           => $data->nama_barang,
                "is_posted"           => $data->is_posted,
                "request_date"           => $data->request_date,
                "production_date"           => $data->production_date,
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
                            'qty_now' => $s->qty_isi,
                        ];
                    } else {
                        $dataMaterialDetail = [
                            'qty' => $s->qty,
                            'qty2' => $s->qty2,
                            'qty_isi' => $s->qty_isi,
                            'qty_now' => $s->qty2
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
}
