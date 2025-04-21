<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Models\MaterialRequestDetailsModel;
use App\Models\SatuansModel;
use App\Models\MaterialRequestsModel;
use App\Models\MaterialRequestsPenolongModel;
use App\Models\MaterialRequestPenolongDetailsModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Models\AccountBarangModel;

class RequestStock extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $satuanModel;
    protected $warehousesModel;
    protected $materialRequestModel;
    protected $materialRequestPenolongModel;
    protected $materialRequestPenolongDetailsModel;
    protected $materialRequestDetailsModel;
    protected $this_user_id;
    protected $workOrdersModel;
    protected $workOrderDetailsModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $divisiModel;
    protected $woModel;
    protected $metaDataModel;
    protected $stockModel;
    protected $accountBarangModel;

    protected $jurnalUmumController;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestPenolongModel = new MaterialRequestsPenolongModel();
        $this->materialRequestPenolongDetailsModel = new MaterialRequestPenolongDetailsModel();
        $this->materialRequestDetailsModel = new MaterialRequestDetailsModel();
        $this->satuanModel = new SatuansModel();
        $this->warehousesModel = new WarehousesModel();
        $this->workOrdersModel = new WorkOrdersModel();
        $this->workOrderDetailsModel = new WorkOrderDetailsModel();
        $this->warehousesModel = new WarehousesModel();
        $this->divisiModel = new DivisisModel();
        $this->woModel = new WorkOrdersModel();
        $this->metaDataModel = new MetadataModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->stockModel = new StockModel();
        $this->jurnalUmumController = new JurnalUmum();
        $this->accountBarangModel = new AccountBarangModel();
    }

    public function index()
    {
        return view('Production/requestStock/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "material_type" => $this->request->getGet("material_type")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        // Buat condition berdasarkan material_type
        if ($payload["material_type"] === "material_request") {
            $condition = [
                'material_requests.company_id' => $this->this_company_id,
                'material_requests.is_posted' => 1
            ];
        } elseif ($payload["material_type"] === "material_kimia") {
            $condition = [
                'material_requests_penolong.company_id' => $this->this_company_id,
                'parent_barang.parent_name' => "KIMIA",
                'material_requests_penolong.is_posted' => 1
            ];
        } elseif ($payload["material_type"] === "material_penolong") {
            $condition = [
                'material_requests_penolong.company_id' => $this->this_company_id,
                'parent_barang.parent_type' => "bahan_penolong",
                'material_requests_penolong.is_posted' => 1
            ];
        } else {
            // Default jika material_type tidak sesuai
            $condition = [
                'material_requests.company_id' => $this->this_company_id,
                'material_requests.is_posted' => 1
            ];
        }

        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        // Ambil data sesuai kondisi yang telah dibuat
        if ($payload["material_type"] === "material_request" ||  empty($payload["material_type"])) {
            $type = "Material Request";
            $materialRequestData = $this->materialRequestModel->getMaterialRequestList($condition, $addCondition, $limit, $offset);
        } elseif ($payload["material_type"] === "material_kimia") {
            $type = "Material Kimia";
            $materialRequestData = $this->materialRequestPenolongModel->getMaterialRequestList($condition, $addCondition, $limit, $offset);
        } elseif ($payload["material_type"] === "material_penolong") {
            $type = "Material Penolong";
            $materialRequestData = $this->materialRequestPenolongModel->getMaterialRequestList($condition, $addCondition, $limit, $offset);
        }


        $dataMaterialRequest = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($materialRequestData['data'] as $data) {
            array_push($dataMaterialRequest, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "req_no" => $data->req_no,
                "nama_barang" => $data->nama_barang,
                "wo_no" => $data->wo_no,
                "is_posted" => $data->is_posted,
                "is_approve" => $data->is_approve,
                "request_status" => $data->request_status,
                "type" => $type,
            ]);
        }

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $materialRequestData['totalData'],
            "recordsFiltered" => $materialRequestData['totalFilteredData'],
            "data" => $dataMaterialRequest,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
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
            ->where('work_orders.is_posted', "0")
            ->where('work_orders.deletedAt', null)
            ->where('work_order_details.deletedAt', null)
            ->groupBy('work_order_details.work_order_id')
            ->find();

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
            "dataWorkOrder" => $dataWorkOrder,
        ];

        if (!empty($id)) {
            $dataMaterialRequests = $this->materialRequestModel->asObject()->find($id);
            $dataMaterialRequestswithwo = $this->materialRequestModel->getMaterialWithWorkOrder($id);
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
            $data["dataMaterialRequests"] = $dataMaterialRequests;
            $data["dataMaterialRequestDetails"] = $dataMaterialRequestDetails;
            $data["dataMaterialRequestswithwo"] = $dataMaterialRequestswithwo;
            $data["ids"] = $ids;
        }



        return view('Production/requestStock/form', $data);
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


    public function approve()
    {
        try {

            $id = $this->request->getVar('id');
            $id = decrypt($id);

            $data = [
                'is_approve' => $this->request->getVar('status_approve'),
            ];

            if (!empty($id)) {
                $materialRequestData = $this->materialRequestModel->find($id);
                $materialRequestDetailData = $this->materialRequestDetailsModel->where('material_request_id', $id)->findAll();

                foreach ($materialRequestDetailData as $key => $value) {

                    $statusOUT = $this->accountBarangModel->checkAccountBarang($this->this_company_id, $value['divisi_id'], $value['barang1_id'], $value['barang2_id'], $value['keterangan']);
                    $statusIN = $this->accountBarangModel->checkAccountBarang($this->this_company_id, $value['divisi_tujuan_id'], $value['barang1_id'], $value['barang2_id'], $value['keterangan']);


                    // $statusOUT = $this->jurnalUmumController->TransaksiJurnalStockBarang($this->this_company_id, $value['divisi_id'], $value['barang1_id'], $value['barang2_id'], $value['barang_type'], $value['stock_dokumen'], 'OUT');
                    // $statusIN = $this->jurnalUmumController->TransaksiJurnalStockBarang($this->this_company_id, $value['divisi_tujuan_id'], $value['barang1_id'], $value['barang2_id'], $value['barang_type'], $value['stock_dokumen'], 'IN');
                    // var_dump($statusOUT, $statusIN);
                    // exit;

                    if (!$statusOUT && !$statusIN) {
                        $data = [
                            "status"    => false,
                            "message"   => "Barang belum memiliki Akun COA",
                            'token'     => csrf_hash()
                        ];
                        echo json_encode($data);
                        return;
                    } else {
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
                            $value['supplier_id'],
                            $value['harga_umum'],
                            $value['harga_harian'],
                            $value['harga_bulanan'],
                        );

                        // -----
                        // BARANG IN KE INVENTORI
                        $stokIn = $this->stockModel->insertStok(
                            $materialRequestData['company_id'],
                            $value['warehouse_tujuan_id'],
                            $value['divisi_tujuan_id'],
                            $value['barang_type'],
                            $value['barang1_id'],
                            $value['barang2_id'],
                            $value['qty2']
                        );

                        $this->materialRequestDetailsModel->update($value['id'], [
                            'stock_tujuan_id' => $stokIn
                        ]);

                        $checkStokDetailIn =  $this->stockModel->isDefinedStockSubDetail(
                            $materialRequestData['company_id'],
                            $value['warehouse_tujuan_id'],
                            $value['divisi_tujuan_id'],
                            $value['barang_type'],
                            $value['barang1_id'],
                            $value['barang2_id'],
                            $value['bc_id'],
                            $value['no_aju'],
                            $stokIn
                        );

                        if ($checkStokDetailIn == null) {
                            // INSERT STOK INISIASI
                            $stokDetailIn = $this->stockDetailModel->insertStokDetail(
                                $stokIn,
                                0,
                                "In",
                                date('Y-m-d'),
                                $this->this_user_id,
                                "INISIASI",
                                "-",
                                "-"
                            );
                            $this->stockDetail2Model->insertStokDetail2(
                                $value['bc_id'],
                                $value['stock_id'],
                                $stokDetailIn,
                                0,
                                $value['no_aju'],
                                "-"
                            );
                        }

                        $stockRebusDetailIn = $this->stockDetail2Model->getStockListDetail(
                            $value['bc_id'],
                            $value['stock_id'],
                            $value['no_aju'],
                            $value['stock_dokumen']
                        );

                        // DETAIL
                        $stokDetailIn = $this->stockDetailModel->insertStokDetail(
                            $stokIn,
                            $value['qty2'],
                            "In",
                            date('Y-m-d'),
                            $this->this_user_id,
                            "PRODUKSI",
                            $materialRequestData['req_no'],
                            $value['note'] ? $value['note'] : "-"
                        );

                        // SUB DETAIL
                        $this->stockDetail2Model->insertStokDetail2(
                            $value['bc_id'],
                            $stokIn,
                            $stokDetailIn,
                            $value['qty2'],
                            $value['no_aju'],
                            $materialRequestData['req_no'],
                            $value['stock_dokumen'],
                            $value['supplier_id'],
                            $value['harga_umum'],
                            $value['harga_harian'],
                            $value['harga_bulanan'],
                        );
                        $this->materialRequestModel->update($id, $data);

                        $this->workOrdersModel->update($materialRequestData['work_order_id'], [
                            'is_posted' => 1
                        ]);
                    }
                }
                $data = [
                    "status"    => true,
                    "message"   => "Status Approve Berhasil Diperbaharui",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Not Found",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (Exception $e) {
            $data = [
                "status"     => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }

        return;
    }

    public function approvePenolong()
    {
        try {

            $id = $this->request->getVar('id');
            $id = decrypt($id);

            $data = [
                'is_approve' => $this->request->getVar('status_approve'),
            ];

            if (!empty($id)) {
                $materialRequestData = $this->materialRequestPenolongModel->find($id);
                $materialRequestPenolongDetailData = $this->materialRequestPenolongDetailsModel->where('material_request_id', $id)->findAll();

                foreach ($materialRequestPenolongDetailData as $key => $value) {

                    $statusOUT = $this->accountBarangModel->checkAccountBarangCOA($this->this_company_id, $value['divisi_id'], $value['barang1_id']);
                    $statusIN = $this->accountBarangModel->checkAccountBarangCOA($this->this_company_id, $value['divisi_tujuan_id'], $value['barang1_id']);


                    // $statusOUT = $this->jurnalUmumController->TransaksiJurnalStockBarang($this->this_company_id, $value['divisi_id'], $value['barang1_id'], $value['barang2_id'], $value['barang_type'], $value['stock_dokumen'], 'OUT');
                    // $statusIN = $this->jurnalUmumController->TransaksiJurnalStockBarang($this->this_company_id, $value['divisi_tujuan_id'], $value['barang1_id'], $value['barang2_id'], $value['barang_type'], $value['stock_dokumen'], 'IN');
                    // var_dump($statusOUT, $statusIN);
                    // exit;

                    if ($statusOUT && $statusIN) {
                        $data = [
                            "status"    => false,
                            "message"   => "Barang belum memiliki Akun COA",
                            'token'     => csrf_hash()
                        ];
                        echo json_encode($data);
                        return;
                    } else {
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
                            $value['supplier_id'],
                            $value['harga_umum'],
                            $value['harga_harian'],
                            $value['harga_bulanan'],
                        );

                        // -----
                        // BARANG IN KE INVENTORI
                        $stokIn = $this->stockModel->insertStok(
                            $materialRequestData['company_id'],
                            $value['warehouse_tujuan_id'],
                            $value['divisi_tujuan_id'],
                            $value['barang_type'],
                            $value['barang1_id'],
                            $value['barang2_id'],
                            $value['qty2']
                        );

                        $this->materialRequestPenolongDetailsModel->update($value['id'], [
                            'stock_tujuan_id' => $stokIn
                        ]);

                        $checkStokDetailIn =  $this->stockModel->isDefinedStockSubDetail(
                            $materialRequestData['company_id'],
                            $value['warehouse_tujuan_id'],
                            $value['divisi_tujuan_id'],
                            $value['barang_type'],
                            $value['barang1_id'],
                            $value['barang2_id'],
                            $value['bc_id'],
                            $value['no_aju'],
                            $stokIn
                        );

                        if ($checkStokDetailIn == null) {
                            // INSERT STOK INISIASI
                            $stokDetailIn = $this->stockDetailModel->insertStokDetail(
                                $stokIn,
                                0,
                                "In",
                                date('Y-m-d'),
                                $this->this_user_id,
                                "INISIASI",
                                "-",
                                "-"
                            );
                            $this->stockDetail2Model->insertStokDetail2(
                                $value['bc_id'],
                                $value['stock_id'],
                                $stokDetailIn,
                                0,
                                $value['no_aju'],
                                "-"
                            );
                        }

                        $stockRebusDetailIn = $this->stockDetail2Model->getStockListDetail(
                            $value['bc_id'],
                            $value['stock_id'],
                            $value['no_aju'],
                            $value['stock_dokumen']
                        );

                        // DETAIL
                        $stokDetailIn = $this->stockDetailModel->insertStokDetail(
                            $stokIn,
                            $value['qty2'],
                            "In",
                            date('Y-m-d'),
                            $this->this_user_id,
                            "PRODUKSI",
                            $materialRequestData['req_no'],
                            $value['note'] ? $value['note'] : "-"
                        );

                        // SUB DETAIL
                        $this->stockDetail2Model->insertStokDetail2(
                            $value['bc_id'],
                            $stokIn,
                            $stokDetailIn,
                            $value['qty2'],
                            $value['no_aju'],
                            $materialRequestData['req_no'],
                            $value['stock_dokumen'],
                            $value['supplier_id'],
                            $value['harga_umum'],
                            $value['harga_harian'],
                            $value['harga_bulanan'],
                        );
                        $this->materialRequestPenolongModel->update($id, $data);

                        $this->workOrdersModel->update($materialRequestData['work_order_id'], [
                            'is_posted' => 1
                        ]);
                    }
                }
                $data = [
                    "status"    => true,
                    "message"   => "Status Approve Berhasil Diperbaharui",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Not Found",
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
}
