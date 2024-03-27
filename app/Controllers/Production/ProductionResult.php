<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Controllers\Master\Divisi;
use App\Models\BarangMasterModel;
use App\Models\BarangModel;
use App\Models\DivisisModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestsModel;
use App\Models\ProductionResultModel;
use App\Models\ProductionResultDetailModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;

class ProductionResult extends BaseController
{
    private $this_company_id;
    protected $this_user_id;
    private $barangModel;
    private $barangMasterModel;
    private $productionResultModel;
    private $productionResultDetailModel;
    private $warehousesModel;
    private $workOrdersModel;
    private $workOrderDetailsModel;
    private $divisiModel;
    private $materialRequestModel;
    private $materialRequestDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->barangModel = new BarangModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->productionResultModel = new ProductionResultModel();
        $this->productionResultDetailModel = new ProductionResultDetailModel();
        $this->warehousesModel = new WarehousesModel();
        $this->workOrderDetailsModel = new WorkOrderDetailsModel();
        $this->workOrdersModel = new WorkOrdersModel();
        $this->divisiModel = new DivisisModel();
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestDetailModel = new MaterialRequestDetailsModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
    }

    public function index()
    {
        return view('Production/productionResult/index');
    }

    public function getAll()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id
        ];

        $condition = [
            // "suppliers.company_id"  => $this->this_company_id,
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $productionResultData = $this->productionResultModel->getProductResultList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($productionResultData['data'] as $data) {
            array_push($dataSupplier, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "pr_no"         => $data->pr_no,
                "wo_no"         => $data->wo_no,
                "barangCode"    => $data->barangCode,
                "barangName"    => $data->barangName,
                "receive_date"  => $data->receives_date
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $productionResultData['totalData'],
            "recordsFiltered"   => $productionResultData['totalFilteredData'],
            "data"              => $dataSupplier,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function getById($id)
    {
        $id = decrypt($id);
        $productionResData = $this->productionResultModel->asObject()
            ->select("*, DATE_FORMAT(receive_date, '%d/%m/%Y') AS receive_date")
            ->find($id);

        $productionResDetSelectBJ = "production_result_details.*, barang_master.barang_name AS barang_name, CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS nama_barang, barang_master.kode_barang AS kode_barang, satuans.kode_satuan";
        $productionResDetDataBJ = $this->productionResultDetailModel->asObject()
            ->select($productionResDetSelectBJ)
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('production_result_details.barang_type', 'bahan_jadi')
            ->where('production_result_details.production_result_id', $id)
            ->findAll();
        foreach ($productionResDetDataBJ as $key => &$value) {
            if ($value->barang_type == "bahan_baku") {
                $value->type_barang_text = "Bahan Baku";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "Bahan Penolong";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "Bahan Jadi";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "Bahan Scrap";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "Bahan Modal";
            }
        }
        $productionResDetSelectBS = "production_result_details.*, barang_master.barang_name AS barang_name, CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS nama_barang, barang_master.kode_barang AS kode_barang, satuans.kode_satuan, warehouses.warehouse_name as warehouse, divisis.divisi as divisi";
        $productionResDetDataBS = $this->productionResultDetailModel->asObject()
            ->select($productionResDetSelectBS)
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('stock', 'stock.id = production_result_details.stock_id', 'left')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = stock.divisi_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('production_result_details.barang_type', 'bahan_scrap')
            ->where('production_result_details.production_result_id', $id)
            ->findAll();
        foreach ($productionResDetDataBS as $key => &$value) {
            if ($value->barang_type == "bahan_baku") {
                $value->type_barang_text = "Bahan Baku";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "Bahan Penolong";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "Bahan Jadi";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "Bahan Scrap";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "Bahan Modal";
            }
        }
        $productionResDetSelectBD = "production_result_details.*, barang_master.barang_name AS barang_name, CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS nama_barang, barang_master.kode_barang AS kode_barang, satuans.kode_satuan";
        $productionResDetDataBD = $this->productionResultDetailModel->asObject()
            ->select($productionResDetSelectBD)
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('production_result_details.barang_type', 'bahan_baku')
            ->orWhere('production_result_details.barang_type', 'bahan_penolong')
            ->where('production_result_details.production_result_id', $id)
            ->findAll();
        foreach ($productionResDetDataBD as $key => &$value) {
            if ($value->barang_type == "bahan_baku") {
                $value->type_barang_text = "Bahan Baku";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "Bahan Penolong";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "Bahan Jadi";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "Bahan Scrap";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "Bahan Modal";
            }
        }

        $productionResDetSelectBR = "production_result_details.*, barang_master.barang_name AS barang_name, CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS nama_barang, barang_master.kode_barang AS kode_barang, satuans.kode_satuan";
        $productionResDetDataBR = $this->productionResultDetailModel->asObject()
            ->select($productionResDetSelectBR)
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('production_result_details.barang_type', 'bahan_return')
            ->where('production_result_details.production_result_id', $id)
            ->findAll();
        foreach ($productionResDetDataBR as $key => &$value) {
            if ($value->barang_type == "bahan_baku") {
                $value->type_barang_text = "Bahan Baku";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "Bahan Penolong";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "Bahan Jadi";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "Bahan Scrap";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "Bahan Modal";
            } else {
                $value->type_barang_text = "Bahan Return";
            }
        }

        // var_dump($productionResDetDataBR);

        $barangData = $this->barangMasterModel->asObject()
            ->select('barang_master.*')
            // ->join('satuans', 'satuans.id = work_orders.id', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', 'bahan_scrap')
            ->findAll();


        $dataWarehouse = $this->warehousesModel->asArray()->where('company_id', $this->this_company_id)->findAll();
        $dataDivisi = $this->divisiModel->asArray()->where('company_id', $this->this_company_id)->findAll();

        $dataWorkOrder = $this->workOrdersModel->asObject()
            ->select('work_orders.*, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR \', \') AS nama_barang')
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('work_orders.deletedAt', null)
            ->where('work_orders.id', $productionResData->work_order_id)
            ->where('work_orders.is_posted', "1")
            ->where('work_order_details.deletedAt', null)
            ->groupBy('work_order_details.work_order_id')
            ->find();

        // var_dump($productionResData);

        $dataMaterialRequest = $this->materialRequestModel->asObject()
            ->select('material_requests.*, GROUP_CONCAT(material_request_details.nama_barang SEPARATOR \', \') AS nama_barang, users.name AS user_name')
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'left')
            ->join('users', 'users.id = material_requests.createdBy', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('material_requests.id', $productionResData->material_request_id)
            ->where('material_requests.deletedAt', null)
            ->where('material_request_details.deletedAt', null)
            ->groupBy('material_request_details.material_request_id')
            ->find();
        // var_dump($productionResDetDataBS);

        $data = [
            'data'                  => $productionResData,
            'dataResultBarangJadi'                  => $productionResDetDataBJ,
            'dataResultBarangScrap'                  => $productionResDetDataBS,
            'dataResultBarangDigunakan'                  => $productionResDetDataBD,
            'dataResultBarangReturn'                  => $productionResDetDataBR,
            'dataWorkOrder' => $dataWorkOrder,
            'dataWarehouse' => $dataWarehouse,
            'dataDivisi' => $dataDivisi,
            'dataMaterialRequest' => $dataMaterialRequest,
        ];
        return view('Production/productionResult/form', $data);
    }

    public function createProductionResult()
    {
        $barangData = $this->barangMasterModel->asObject()
            ->select('barang_master.*')
            // ->join('satuans', 'satuans.id = work_orders.id', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', 'bahan_scrap')
            ->findAll();


        $dataWarehouse = $this->warehousesModel->asArray()->where('company_id', $this->this_company_id)->findAll();
        $dataDivisi = $this->divisiModel->asArray()->where('company_id', $this->this_company_id)->findAll();

        $dataWorkOrder = $this->workOrdersModel->asObject()
            ->select('work_orders.*, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR \', \') AS nama_barang')
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
            ->join('material_requests', 'material_requests.work_order_id = work_orders.id')
            ->where('work_orders.company_id', $this->this_company_id)
            ->where('work_orders.deletedAt', null)
            ->where('work_orders.is_posted', "1")
            ->where('work_orders.request_status', "waiting")
            ->where('material_requests.is_posted', '1')
            ->where('work_order_details.deletedAt', null)
            ->groupBy('work_order_details.work_order_id')
            ->find();

        $dataMaterialRequest = $this->materialRequestModel->asObject()
            ->select('material_requests.*, GROUP_CONCAT(material_request_details.nama_barang SEPARATOR \', \') AS nama_barang, users.name AS user_name')
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'left')
            ->join('users', 'users.id = material_requests.createdBy', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('material_requests.deletedAt', null)
            ->where('material_request_details.deletedAt', null)
            ->groupBy('material_request_details.material_request_id')
            ->find();

        $data = [
            'barangData' => $barangData,
            'dataWorkOrder' => $dataWorkOrder,
            'dataWarehouse' => $dataWarehouse,
            'dataDivisi' => $dataDivisi,
            'dataMaterialRequest' => $dataMaterialRequest,
        ];
        return view('Production/productionResult/form', $data);
    }

    public function saveProductionResult()
    {
        try {
            $datas = [
                "pr_no" => $this->request->getVar("res_no") == "AUTO GENERATE" ? $this->generatePRNo() : $this->request->getVar("res_no"),
                "work_order_id" => $this->request->getVar("kode_produksi"),
                "material_request_id" => $this->request->getVar("kode_request"),
                "receive_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : date("Y-m-d"),
            ];
            $department_id_order = $this->request->getVar("department_id_order");
            $department_id_request = $this->request->getVar("department_id_request");
            $warehouse_id_order = $this->request->getVar("warehouse_id_order");
            $warehouse_id_request = $this->request->getVar("warehouse_id_request");

            $barangJadi = json_decode($this->request->getVar("jadi"));
            $barangDigunakan = json_decode($this->request->getVar("digunakan"));
            $barangScrap = json_decode($this->request->getVar("scrap"));
            $barangReturn = json_decode($this->request->getVar("return"));

            $productionResID = $this->productionResultModel->insert($datas);

            $dataMaterialRequest = $this->materialRequestModel
                ->where('id', $this->request->getVar("kode_request"))
                ->where('deletedAt', null)
                ->find();

            $dataMaterialRequestDetail = $this->materialRequestDetailModel
                ->join('material_requests', 'material_requests.id = material_request_details.material_request_id', 'left')
                ->where('material_request_id', $this->request->getVar("kode_request"))
                ->where('material_request_details.deletedAt', null)
                ->find();

            $dataWorkOrder = $this->workOrdersModel
                ->where('id', $this->request->getVar("kode_produksi"))
                ->where('deletedAt', null)
                ->find();
            $dataWorkOrderDetail = $this->workOrderDetailsModel
                ->join('work_orders', 'work_orders.id = work_order_details.work_order_id', 'left')
                ->where('work_order_id', $this->request->getVar("kode_produksi"))
                ->where('work_orders.deletedAt', null)
                ->where('work_order_details.deletedAt', null)
                ->find();

            $productionResData = $this->productionResultModel->find($productionResID);

            $this->workOrdersModel->update($this->request->getVar("kode_produksi"), [
                'request_status' => 'finished'
            ]);

            foreach ($barangJadi as $bj) {
                foreach ($dataWorkOrderDetail as $key => $value) {
                    if ($value['barang1_id'] == $bj->barang1_id && $value['barang2_id'] == $bj->barang2_id) {
                        // var_dump($bj);
                        $stok = $this->stockModel->insertStok(
                            $value['company_id'],
                            $value['warehouse_id'],
                            $value['divisi_id'],
                            "bahan_jadi",
                            $value['barang1_id'],
                            $value['barang2_id'],
                            isset($bj->qty_jadi) ? (float) $bj->qty_jadi : (float) $bj->qty
                        );
                        $checkStokDetail =  $this->stockModel->isDefinedStockSubDetail(
                            $value['company_id'],
                            $value['warehouse_id'],
                            $value['divisi_id'],
                            "bahan_jadi",
                            $value['barang1_id'],
                            $value['barang2_id'],
                            0,
                            '-',
                            $stok
                        );

                        if ($checkStokDetail == null) {
                            // INSERT STOK INISIASI
                            $stokDetail = $this->stockDetailModel->insertStokDetail(
                                $stok,
                                0,
                                "In",
                                date('Y-m-d'),
                                $this->this_user_id,
                                "INISIASI",
                                "-",
                                "-"
                            );
                            $this->stockDetail2Model->insertStokDetail2(
                                0,
                                $stok,
                                $stokDetail,
                                0,
                                "-",
                                "-"
                            );
                        }

                        // // DETAIL
                        $stokDetail = $this->stockDetailModel->insertStokDetail(
                            $stok,
                            isset($bj->qty_jadi) ? (float) $bj->qty_jadi : (float) $bj->qty,
                            "In",
                            date('Y-m-d'),
                            $this->this_user_id,
                            "PRODUKSI",
                            $value['wo_no'],
                            "-"
                        );

                        // SUB DETAIL
                        $this->stockDetail2Model->insertStokDetail2(
                            0,
                            $stok,
                            $stokDetail,
                            isset($bj->qty_jadi) ? (float) $bj->qty_jadi : (float) $bj->qty,
                            '-',
                            $productionResData['pr_no']
                        );
                        $datasbj = [
                            "production_result_id" => $productionResID,
                            "barang1_id" => $bj->barang1_id,
                            "barang2_id" => $bj->barang2_id,
                            "bc_id" => 0,
                            "stock_id" => 0,
                            "no_aju" => "-",
                            "barang_type" => $bj->type_barang,
                            "qty" => isset($bj->qty_jadi) ? (float) $bj->qty_jadi : (float) $bj->qty,
                        ];
                        $this->productionResultDetailModel->insert($datasbj);
                        $this->workOrderDetailsModel->update($bj->detail_work_order, [
                            'qty_hasil' => isset($bj->qty_jadi) ? (float) $bj->qty_jadi : (float) $bj->qty
                        ]);
                    }
                }
            }
            foreach ($barangDigunakan as $bd) {
                $qtyAwal = isset($bd->qty_digunakan) ? (float) $bd->qty_digunakan : $bd->qty;
                $qtyAkhir = 0.0;
                foreach ($barangReturn as $br) {
                    if ($bd->barang1_id == $br->barang1_id && $bd->barang2_id == $br->barang2_id && $bd->stock_id == $br->stock_id && $bd->bc_id == $br->bc_id) {
                        if (isset($br->qty_dikembalikan)) {
                            $qtyAkhir = $qtyAwal - $br->qty_dikembalikan;
                        } else {
                            $qtyAkhir = $qtyAwal;
                        }
                    }
                }
                $datasbd = [
                    "production_result_id" => $productionResID,
                    "barang1_id" => $bd->barang1_id,
                    "barang2_id" => $bd->barang2_id,
                    "bc_id" => $bd->bc_id,
                    "stock_id" => $bd->stock_id,
                    "no_aju" => "-",
                    "barang_type" => $bd->type_barang,
                    "qty" => (float) $qtyAkhir,
                ];
                // var_dump($datasbd);
                $this->productionResultDetailModel->insert($datasbd);
            }
            // exit;

            foreach ($barangScrap as $bs) {
                $stok = $this->stockModel->insertStok(
                    $this->this_company_id,
                    $bs->warehouse_id,
                    $bs->divisi_id,
                    "bahan_scrap",
                    decrypt($bs->barang_id),
                    decrypt($bs->barang_spesifikasi_id),
                    (float) $bs->qty
                );
                $checkStokDetail =  $this->stockModel->isDefinedStockSubDetail(
                    $this->this_company_id,
                    $bs->warehouse_id,
                    $bs->divisi_id,
                    "bahan_scrap",
                    decrypt($bs->barang_id),
                    decrypt($bs->barang_spesifikasi_id),
                    0,
                    '-',
                    $stok
                );

                if ($checkStokDetail == null) {
                    // INSERT STOK INISIASI
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        0,
                        "In",
                        date('Y-m-d'),
                        $this->this_user_id,
                        "INISIASI",
                        "-",
                        "-"
                    );
                    $this->stockDetail2Model->insertStokDetail2(
                        0,
                        $stok,
                        $stokDetail,
                        0,
                        "-",
                        "-"
                    );
                }
                // // DETAIL
                $stokDetail = $this->stockDetailModel->insertStokDetail(
                    $stok,
                    (float) $bs->qty,
                    "In",
                    date('Y-m-d'),
                    $this->this_user_id,
                    "PRODUKSI",
                    $productionResData['pr_no'],
                    "-"
                );

                // SUB DETAIL
                $this->stockDetail2Model->insertStokDetail2(
                    0,
                    $stok,
                    $stokDetail,
                    (float) $bs->qty,
                    '-',
                    $productionResData['pr_no']
                );
                $datasbs = [
                    "production_result_id" => $productionResID,
                    "barang1_id" => decrypt($bs->barang_id),
                    "barang2_id" => decrypt($bs->barang_spesifikasi_id),
                    "bc_id" => 0,
                    "stock_id" => $stok,
                    "no_aju" => "-",
                    "barang_type" => "bahan_scrap",
                    "qty" => (float) $bs->qty,
                ];
                $this->productionResultDetailModel->insert($datasbs);
            }

            foreach ($barangReturn as $br) {
                if (isset($br->qty_dikembalikan)) {
                    $stok = $this->stockModel->insertStok(
                        $this->this_company_id,
                        $warehouse_id_request,
                        $department_id_request,
                        $br->type_barang,
                        $br->barang1_id,
                        $br->barang2_id,
                        isset($br->qty_dikembalikan) ? (float) $br->qty_dikembalikan : 0
                    );
                    $checkStokDetail =  $this->stockModel->isDefinedStockSubDetail(
                        $this->this_company_id,
                        $warehouse_id_request,
                        $department_id_request,
                        $br->type_barang,
                        $br->barang1_id,
                        $br->barang2_id,
                        0,
                        '-',
                        $stok
                    );

                    if ($checkStokDetail == null) {
                        // INSERT STOK INISIASI
                        $stokDetail = $this->stockDetailModel->insertStokDetail(
                            $stok,
                            0,
                            "In",
                            date('Y-m-d'),
                            $this->this_user_id,
                            "INISIASI",
                            "-",
                            "-"
                        );
                        $this->stockDetail2Model->insertStokDetail2(
                            0,
                            $stok,
                            $stokDetail,
                            0,
                            "-",
                            "-"
                        );
                    }
                    // // DETAIL
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        isset($br->qty_dikembalikan) ? (float) $br->qty_dikembalikan : 0,
                        "In",
                        date('Y-m-d'),
                        $this->this_user_id,
                        "PRODUKSI",
                        $productionResData['pr_no'],
                        "-"
                    );

                    // SUB DETAIL
                    $this->stockDetail2Model->insertStokDetail2(
                        0,
                        $stok,
                        $stokDetail,
                        isset($br->qty_dikembalikan) ? (float) $br->qty_dikembalikan : 0,
                        '-',
                        $productionResData['pr_no']
                    );
                    $datasbr = [
                        "production_result_id" => $productionResID,
                        "barang1_id" => $br->barang1_id,
                        "barang2_id" => $br->barang2_id,
                        "bc_id" => $br->bc_id,
                        "stock_id" => $br->stock_id,
                        "no_aju" => $br->no_aju == "-" ? "-" : $br->no_aju,
                        "barang_type" => "bahan_return",
                        "qty" => isset($br->qty_dikembalikan) ? (float) $br->qty_dikembalikan : 0,
                    ];
                    $this->productionResultDetailModel->insert($datasbr);
                }
            }

            $data = [
                "status"    => true,
                "id"    => encrypt($productionResID),
                "message"   => 'Success',
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    private function generatePRNo(): string
    {
        $month = idate('m');
        $year = date('y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "PR/$romanMonth/$year/";

        $lastData = $this->productionResultModel->asObject()
            ->like('pr_no', $numberTemplate)
            ->orderBy('createdAt', 'DESC')
            ->first();

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->pr_no);
            $lastIncrement = intval($asd[3]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $numberTemplate . $paddedNumber;
        } else {
            $invNumber = $numberTemplate . '001';
        }

        return $invNumber;
    }

    public function getListWorkOrderByID()
    {
        if (!empty($this->request->getVar('kode_produksi'))) {
            $dataResult = $this->workOrderDetailsModel->getWorkOrderDetailByWorkOrderID(
                $this->request->getVar('kode_produksi')
            );
            foreach ($dataResult as $key => &$value) {
                if ($value['type_barang'] == "bahan_baku") {
                    $value['type_barang_text'] = "Bahan Baku";
                } elseif ($value['type_barang'] == "bahan_penolong") {
                    $value['type_barang_text'] = "Bahan Penolong";
                } elseif ($value['type_barang'] == "bahan_jadi") {
                    $value['type_barang_text'] = "Bahan Jadi";
                } elseif ($value['type_barang'] == "bahan_scrap") {
                    $value['type_barang_text'] = "Bahan Scrap";
                } elseif ($value['type_barang'] == "bahan_modal") {
                    $value['type_barang_text'] = "Bahan Modal";
                }
            }
            return response()->setJSON([
                'data' => $dataResult,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }

    public function getListMaterialRequestByID()
    {
        if (!empty($this->request->getVar('kode_request'))) {
            $dataResult = $this->materialRequestDetailModel->getMaterialRequestDetailByMaterialRequestID(
                $this->request->getVar('kode_request')
            );
            foreach ($dataResult as $key => &$value) {
                if ($value['type_barang'] == "bahan_baku") {
                    $value['type_barang_text'] = "Bahan Baku";
                } elseif ($value['type_barang'] == "bahan_penolong") {
                    $value['type_barang_text'] = "Bahan Penolong";
                } elseif ($value['type_barang'] == "bahan_jadi") {
                    $value['type_barang_text'] = "Bahan Jadi";
                } elseif ($value['type_barang'] == "bahan_scrap") {
                    $value['type_barang_text'] = "Bahan Scrap";
                } elseif ($value['type_barang'] == "bahan_modal") {
                    $value['type_barang_text'] = "Bahan Modal";
                }
            }
            return response()->setJSON([
                'data' => $dataResult,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }

    public function getListMaterialRequestByWOID()
    {
        if (!empty($this->request->getVar('kode_produksi'))) {
            $dataMaterialRequest = $this->materialRequestModel->asObject()
                ->select('material_requests.*, GROUP_CONCAT(material_request_details.nama_barang SEPARATOR \', \') AS nama_barang, users.name AS user_name')
                ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'left')
                ->join('users', 'users.id = material_requests.createdBy', 'left')
                ->where('company_id', $this->this_company_id)
                ->where('material_requests.is_posted', 1)
                ->where('material_requests.deletedAt', null)
                ->where('material_request_details.deletedAt', null)
                ->where('material_requests.work_order_id', $this->request->getVar('kode_produksi'))
                ->groupBy('material_request_details.material_request_id')
                ->find();
            if ($dataMaterialRequest) {
                return response()->setJSON([
                    'data' => $dataMaterialRequest,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }
        }
    }
}
