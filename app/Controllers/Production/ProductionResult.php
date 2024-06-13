<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Controllers\Master\Divisi;
use App\Models\BarangMasterModel;
use App\Models\BarangModel;
use App\Models\DivisisModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestsModel;
use App\Models\MetadataModel;
use App\Models\ProductionResultModel;
use App\Models\ProductionResultDetailModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;
use Exception;

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
    protected $metaDataModel;

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
        $this->metaDataModel = new MetadataModel();
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
            "production_results.company_id"  => $this->this_company_id,
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
                "is_posted"    => $data->is_posted,
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

        $idMaterialRequest = json_decode($productionResData->material_request_id);

        $productionResDetSelectBJ = "production_result_details.*, barang_master.barang_name AS barang_name, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS nama_barang, barang_master.kode_barang AS kode_barang, satuans.kode_satuan";
        $productionResDetDataBJ = $this->productionResultDetailModel->asObject()
            ->select($productionResDetSelectBJ)
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('production_result_details.type', 'JADI')
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
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "Bahan Setengah Jadi";
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
            ->where('production_result_details.type', 'SCRAP')
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
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "Bahan Setengah Jadi";
            }
        }
        $productionResDetSelectBD = "production_result_details.*, barang_master.barang_name AS barang_name, CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS nama_barang, barang_master.kode_barang AS kode_barang, satuans.kode_satuan, warehouses.warehouse_name, divisis.divisi";
        $productionResDetDataBD = $this->productionResultDetailModel->asObject()
            ->select($productionResDetSelectBD)
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('divisis', 'divisis.id = production_result_details.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = production_result_details.warehouse_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('production_result_details.production_result_id', $id)
            ->where('production_result_details.type', 'DIGUNAKAN')
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
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "Bahan Setengah Jadi";
            }
        }

        $productionResDetSelectBR = "production_result_details.*, barang_master.barang_name AS barang_name, CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS nama_barang, barang_master.kode_barang AS kode_barang, satuans.kode_satuan";
        $productionResDetDataBR = $this->productionResultDetailModel->asObject()
            ->select($productionResDetSelectBR)
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('production_result_details.type', 'RETURN')
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
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "Bahan Setengah Jadi";
            } else {
                $value->type_barang_text = "Bahan Return";
            }
        }

        $barangData = $this->barangMasterModel->asObject()
            ->select('barang_master.*')
            // ->join('satuans', 'satuans.id = work_orders.id', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', 'bahan_scrap')
            ->findAll();

        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description', "bahan_baku")
            ->orWhere('description', "bahan_jadi")
            ->orWhere('description', "bahan_scrap")
            ->orWhere('description', "bahan_setengah_jadi")
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

        $dataMaterialRequest = $this->materialRequestModel->asObject()
            ->select('material_requests.*, GROUP_CONCAT(material_request_details.nama_barang SEPARATOR \', \') AS nama_barang, users.name AS user_name')
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'left')
            ->join('users', 'users.id = material_requests.createdBy', 'left')
            ->where('company_id', $this->this_company_id)
            ->whereIn('material_requests.id', $idMaterialRequest)
            ->where('material_requests.deletedAt', null)
            ->where('material_request_details.deletedAt', null)
            ->groupBy('material_requests.id')
            ->find();

        // Inisialisasi variabel untuk menyimpan req_no dan request_date
        $reqNos = [];
        $requestDates = [];

        // Iterasi hasil query untuk menggabungkan req_no dan request_date
        foreach ($dataMaterialRequest as $request) {
            $reqNos[] = $request->req_no;
            $requestDates[] = date('d/m/Y', strtotime($request->request_date));
        }

        // Gabungkan semua req_no dan request_date menjadi satu string dipisahkan oleh koma
        $combinedReqNos = implode(', ', $reqNos);
        $combinedRequestDates = implode(', ', $requestDates);

        $data = [
            'data'                  => $productionResData,
            'dataResultBarangJadi'                  => $productionResDetDataBJ,
            'dataResultBarangScrap'                  => $productionResDetDataBS,
            'dataResultBarangDigunakan'                  => $productionResDetDataBD,
            'dataResultBarangReturn'                  => $productionResDetDataBR,
            'dataWorkOrder' => $dataWorkOrder,
            'dataWarehouse' => $dataWarehouse,
            'dataDivisi' => $dataDivisi,
            'tipeBarang' => $dataTipeBarang,
            'dataMaterialRequest' => $dataMaterialRequest,
            'dataMaterialRequestNo' => $combinedReqNos,
            'dataMaterialRequestDate' => $combinedRequestDates,
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
            ->where('work_orders.company_id', $this->this_company_id)
            ->where('work_orders.deletedAt', null)
            ->where('work_orders.is_posted', "1")
            ->where('work_orders.request_status', "waiting")
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
        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description', "bahan_baku")
            ->orWhere('description', "bahan_jadi")
            ->orWhere('description', "bahan_scrap")
            ->findAll();

        $data = [
            'barangData' => $barangData,
            'dataWorkOrder' => $dataWorkOrder,
            'dataWarehouse' => $dataWarehouse,
            'dataDivisi' => $dataDivisi,
            'dataMaterialRequest' => $dataMaterialRequest,
            'tipeBarang' => $dataTipeBarang,
        ];
        return view('Production/productionResult/form', $data);
    }

    public function saveProductionResult()
    {
        try {
            $datas = [
                "company_id" => $this->this_company_id,
                "pr_no" => $this->request->getVar("res_no") == "AUTO GENERATE" ? $this->generatePRNo() : $this->request->getVar("res_no"),
                "material_request_id" => json_encode($this->request->getPost("kode_request")),
                "work_order_id" => $this->request->getVar("kode_produksi"),
                "receive_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : date("Y-m-d"),
            ];


            $barangJadi = json_decode($this->request->getVar("jadi"));
            $barangDigunakan = json_decode($this->request->getVar("digunakan"));
            $barangScrap = json_decode($this->request->getVar("scrap"));
            $barangFilling = json_decode($this->request->getVar("filling"));
            $productionResID = $this->productionResultModel->insert($datas);

            $productionResData = $this->productionResultModel->find($productionResID);

            // $this->workOrdersModel->update($this->request->getVar("kode_produksi"), [
            //     'request_status' => 'finished'
            // ]);
            foreach ($barangJadi as $bj) {
                $qty = isset($bj->qty_jadi) ? (float) $bj->qty_jadi : (float) $bj->qty;
                if ($qty && $qty != 0) {
                    $datasbj = [
                        "production_result_id" => $productionResID,
                        "barang1_id" => $bj->barang1_id,
                        "barang2_id" => $bj->barang2_id,
                        "warehouse_id" => $bj->warehouse_id,
                        "divisi_id" => $bj->divisi_id,
                        "bc_id" => 0,
                        "stock_dokumen" => $productionResData['pr_no'],
                        "stock_id" => 0,
                        "no_aju" => "-",
                        "barang_type" => $bj->type_barang,
                        "type" => "JADI",
                        "no_ref" => "NON PABEAN",
                        "qty" => (float) $qty,
                        "qty2" => (float) $bj->berat_isi_jadi,
                        "qty_isi" => (float) $bj->qty_isi_jadi,
                    ];
                    $this->productionResultDetailModel->insert($datasbj);
                }
            }

            foreach ($barangDigunakan as $bd) {
                $qty = (float) $bd->qty;
                $qty2 = isset($bd->qty2) ? (float) $bd->qty2 : 0;
                $qtySisa = $qty - $qty2;
                $datasbd = [
                    "production_result_id" => $productionResID,
                    "material_request_detail_id" => $bd->material_request_detail_id,
                    "material_request_id" => $bd->material_request_id,
                    "barang1_id" => $bd->barang1_id,
                    "barang2_id" => $bd->barang2_id,
                    "warehouse_id" => $bd->warehouse_id,
                    "divisi_id" => $bd->divisi_id,
                    "bc_id" => $bd->bc_id,
                    "stock_dokumen" => $bd->stock_dokumen,
                    "stock_date" => $bd->stock_date,
                    "stock_id" => $bd->stock_id ?? 0,
                    "no_aju" => $bd->no_aju == "-" ? "-" : $bd->no_aju,
                    "barang_type" => $bd->type_barang,
                    "type" => "DIGUNAKAN",
                    "no_ref" => $bd->ref_no,
                    "qty" => isset($bd->qty2) ? $qty2 : $qty,
                    "kondisi_barang" => $bd->kondisi_barang,
                    "harga_umum" => (float) $bd->harga_umum,
                    "harga_harian" => (float) $bd->harga_harian,
                    "harga_bulanan" => (float) $bd->harga_bulanan,
                ];
                $this->productionResultDetailModel->insert($datasbd);

                if (!$barangFilling && $qty2 != 0) {
                    $datasbr = [
                        "production_result_id" => $productionResID,
                        "material_request_detail_id" => $bd->material_request_detail_id,
                        "material_request_id" => $bd->material_request_id,
                        "barang1_id" => $bd->barang1_id,
                        "barang2_id" => $bd->barang2_id,
                        "warehouse_id" => $bd->warehouse_id,
                        "divisi_id" => $bd->divisi_id,
                        "bc_id" => $bd->bc_id,
                        "stock_dokumen" => $bd->stock_dokumen,
                        "stock_date" => $bd->stock_date,
                        "stock_id" => $bd->stock_id ?? 0,
                        "no_aju" => $bd->no_aju == "-" ? "-" : $bd->no_aju,
                        "barang_type" => $bd->type_barang,
                        "type" => "RETURN",
                        "no_ref" => $bd->ref_no,
                        "qty" => $qtySisa,
                        "kondisi_barang" => "ditapak",
                        "harga_umum" => (float) $bd->harga_umum,
                        "harga_harian" => (float) $bd->harga_harian,
                        "harga_bulanan" => (float) $bd->harga_bulanan,
                    ];
                    $this->productionResultDetailModel->insert($datasbr);
                }
            }

            foreach ($barangScrap as $bs) {
                $datasbs = [
                    "production_result_id" => $productionResID,
                    "barang1_id" => decrypt($bs->barang_id),
                    "barang2_id" => decrypt($bs->barang_spesifikasi_id),
                    "warehouse_id" => $bs->warehouse_id,
                    "divisi_id" => $bs->divisi_id,
                    "bc_id" => 0,
                    "stock_dokumen" => $productionResData['pr_no'],
                    "stock_id" => 0,
                    "no_aju" => "-",
                    "barang_type" => "bahan_scrap",
                    "type" => "SCRAP",
                    "no_ref" => "NON PABEAN",
                    "qty" => (float) $bs->qty,
                    "harga_umum" => (float) $bs->harga_umum,
                    "harga_harian" => (float) $bs->harga_harian,
                    "harga_bulanan" => (float) $bs->harga_bulanan,
                ];
                $this->productionResultDetailModel->insert($datasbs);
            }

            foreach ($barangFilling as $bf) {
                $datasbf = [
                    "production_result_id" => $productionResID,
                    "material_request_detail_id" => $bf->material_request_detail_id,
                    "material_request_id" => $bf->material_request_id,
                    "barang1_id" => $bf->barang1_id,
                    "barang2_id" => $bf->barang2_id,
                    "warehouse_id" => $bf->warehouse_id,
                    "divisi_id" => $bf->divisi_id,
                    "bc_id" => $bf->bc_id,
                    "stock_id" => $bf->stock_id,
                    "stock_date" => $bf->stock_date,
                    "stock_dokumen" => $bf->stock_dokumen,
                    "no_aju" => $bf->no_aju,
                    "barang_type" => $bf->type_barang,
                    "type" => "RETURN",
                    "no_ref" => $bf->ref_no,
                    "qty" => (float) $bf->qty,
                    "kondisi_barang" => $bf->kondisi_barang,
                    "harga_umum" => (float) $bf->harga_umum,
                    "harga_harian" => (float) $bf->harga_harian,
                    "harga_bulanan" => (float) $bf->harga_bulanan,
                ];
                $this->productionResultDetailModel->insert($datasbf);
            }

            $data = [
                "status"    => true,
                "id"    => encrypt($productionResID),
                "message"   => 'Data produksi berhasil disimpan',
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

    public function updateProductionResult()
    {
        try {
            $productionResID = $this->request->getVar("id");
            $barangJadi = json_decode($this->request->getVar("jadi"));
            $barangDigunakan = json_decode($this->request->getVar("digunakan"));
            $barangScrap = json_decode($this->request->getVar("scrap"));
            $barangFilling = json_decode($this->request->getVar("filling"));

            foreach ($barangJadi as $bj) {
                $qty = isset($bj->qty_jadi) ? (float) $bj->qty_jadi : (float) $bj->qty;
                if ($qty && $qty != 0) {
                    $datasbj = [
                        "qty" => (float) $qty,
                        "qty2" => (float) $bj->berat_isi_jadi,
                        "qty_isi" => (float) $bj->qty_isi_jadi,
                    ];
                    $this->productionResultDetailModel->update($bj->production_result_detail_id, $datasbj);
                }
            }

            foreach ($barangDigunakan as $bd) {
                $qty = (float) $bd->qty;
                $qty2 = isset($bd->qty2) ? (float) $bd->qty2 : 0;
                $qtySisa = $qty - $qty2;
                $datasbd = [
                    "qty" => isset($bd->qty2) ? $qty2 : $qty,
                ];
                $this->productionResultDetailModel->update($bd->production_result_detail_id, $datasbd);
            }

            foreach ($barangScrap as $bs) {
                $datasbs = [
                    "qty" => (float) $bs->qty,
                ];
                $this->productionResultDetailModel->update($bs->production_result_detail_id, $datasbs);
            }

            foreach ($barangFilling as $bf) {
                $datasbf = [
                    "qty" => (float) $bf->qty,
                ];
                $this->productionResultDetailModel->update($bf->production_result_detail_id, $datasbf);
            }

            $data = [
                "status"    => true,
                "id"    => encrypt($productionResID),
                "message"   => 'Data produksi berhasil diupdate',
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
            ->where('company_id', $this->this_company_id)
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
                } elseif ($value['type_barang'] == "bahan_setengah_jadi") {
                    $value['type_barang_text'] = "Bahan Setengah Jadi";
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
                ->where('material_request_details.qty_now >', 0)
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

    public function updateStatusPostedProductionResult()
    {
        try {

            $id = $this->request->getVar('id');
            $id = decrypt($id);

            // po posting
            $payload = [
                "is_posted" => $this->request->getVar('status_posting') == '1' ? $this->request->getVar('status_posting') : 0,
            ];

            if (!empty($id)) {
                $resultData = $this->productionResultModel->find($id);
                $resultDetailData = $this->productionResultDetailModel->where('production_result_id', $id)->findAll();

                $materialRequestData = [];
                foreach ($resultDetailData as $key => $value) {
                    if ($value['type'] == 'RETURN') {
                        $materialRequest = $this->materialRequestDetailModel
                            ->where('material_request_id', $value['material_request_id'])
                            ->where('id', $value['material_request_detail_id'])
                            ->findAll();
                        foreach ($materialRequest as $materialRequestData) {
                            if ($value['kondisi_barang'] == "ditapak") {
                                $datas = [
                                    'qty_now' => $value['qty'],
                                    'kondisi_barang' => $value['kondisi_barang'],
                                ];
                                $this->materialRequestDetailModel->update($materialRequestData['id'], $datas);
                            } else {
                                $dataMaterialDetail = [
                                    'material_request_id' => $materialRequestData['material_request_id'],
                                    'divisi_id' => $materialRequestData['divisi_id'],
                                    'warehouse_id' => $materialRequestData['warehouse_id'],
                                    'divisi_tujuan_id' => $materialRequestData['divisi_tujuan_id'],
                                    'warehouse_tujuan_id' => $materialRequestData['warehouse_tujuan_id'],
                                    'stock_tujuan_id' => $materialRequestData['stock_tujuan_id'],
                                    'barang1_id' => $materialRequestData['barang1_id'],
                                    'barang2_id' => $materialRequestData['barang2_id'],
                                    'nama_barang' => $materialRequestData['nama_barang'],
                                    'satuan' => $materialRequestData['satuan'],
                                    'stock_id' => $materialRequestData['stock_id'],
                                    'bc_id' => $materialRequestData['bc_id'],
                                    'no_aju' => $materialRequestData['no_aju'],
                                    'ref_no' => $materialRequestData['ref_no'],
                                    'stock_date' => $materialRequestData['stock_date'],
                                    'stock_dokumen' => $materialRequestData['stock_dokumen'],
                                    'barang_type' => $materialRequestData['barang_type'],
                                    'qty' => $materialRequestData['qty'],
                                    'qty2' => $materialRequestData['qty2'],
                                    'qty_isi' => $materialRequestData['qty_isi'],
                                    'qty_now' => $value['qty'],
                                    'kondisi_barang' => $value['kondisi_barang'],
                                ];
                                $this->materialRequestDetailModel->insert($dataMaterialDetail);
                            }
                        }
                    } else if ($value['type'] == 'DIGUNAKAN') {
                        $materialRequest = $this->materialRequestDetailModel
                            ->where('material_request_id', $value['material_request_id'])
                            ->where('id', $value['material_request_detail_id'])
                            ->findAll();
                        foreach ($materialRequest as $materialRequestData) {
                            $qtyNow = (float) $materialRequestData['qty_now'];
                            $qtyProduksi = (float) $value['qty'];
                            $qtyHasil = $qtyNow - $qtyProduksi;
                            if ($qtyHasil == 0) {
                                $datas = [
                                    'qty_now' => $qtyHasil,
                                ];
                                $this->materialRequestDetailModel->update($materialRequestData['id'], $datas);
                            }
                        }
                    }

                    if ($value['type'] == 'JADI') {
                        // -----
                        // BARANG IN KE INVENTORI
                        $stokIn = $this->stockModel->insertStok(
                            $resultData['company_id'],
                            $value['warehouse_id'],
                            $value['divisi_id'],
                            $value['barang_type'],
                            $value['barang1_id'],
                            $value['barang2_id'],
                            $value['qty']
                        );

                        $this->productionResultDetailModel->update($value['id'], [
                            'stock_id' => $stokIn
                        ]);

                        $checkStokDetailIn =  $this->stockModel->isDefinedStockSubDetail(
                            $resultData['company_id'],
                            $value['warehouse_id'],
                            $value['divisi_id'],
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
                            $value['qty'],
                            "In",
                            date('Y-m-d'),
                            $this->this_user_id,
                            "PRODUKSI",
                            $resultData['pr_no'],
                            "-"
                        );

                        // SUB DETAIL
                        $this->stockDetail2Model->insertStokDetail2(
                            $value['bc_id'],
                            $stokIn,
                            $stokDetailIn,
                            $value['qty'],
                            $value['no_aju'],
                            $resultData['pr_no'],
                            $value['stock_dokumen'],
                        );
                    } else {
                        // Stok OUT
                        $stok = $this->stockModel->insertStok(
                            $resultData['company_id'],
                            $value['warehouse_id'],
                            $value['divisi_id'],
                            $value['barang_type'],
                            $value['barang1_id'],
                            $value['barang2_id'],
                            ($value['qty'] * -1)
                        );

                        // DETAIL
                        $stokDetail = $this->stockDetailModel->insertStokDetail(
                            $stok,
                            $value['qty'],
                            "Out",
                            date('Y-m-d'),
                            $this->this_user_id,
                            "PRODUKSI",
                            $resultData['pr_no'],
                            "-"
                        );

                        // SUB DETAIL
                        $this->stockDetail2Model->insertStokDetail2(
                            $value['bc_id'],
                            $value['stock_id'],
                            $stokDetail,
                            $value['qty'],
                            $value['no_aju'],
                            $resultData['pr_no'],
                            $value['stock_dokumen'],
                        );
                    }
                }
                // exit;
                $this->productionResultModel->update($id, $payload);

                // $this->workOrdersModel->update($resultData['work_order_id'], [
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
}
