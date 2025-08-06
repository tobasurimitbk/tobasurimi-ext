<?php

namespace App\Controllers\Production;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Controllers\Master\Account;
use App\Controllers\Master\Divisi;
use App\Models\AccountBarangModel;
use App\Models\BarangMasterModel;
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
use CodeIgniter\HTTP\Request;
use DateTime;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductionResult extends BaseController
{
    private $this_company_id;
    protected $this_user_id;
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
    protected $jurnalUmumController;
    protected $accountBarangModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;;
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
        $this->accountBarangModel = new AccountBarangModel();
        $this->jurnalUmumController = new JurnalUmum();
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
            "dateStart"    =>  $this->request->getGet("dateStart") ? date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getGet("dateStart")))) : "",
            "dateEnd"    =>  $this->request->getGet("dateEnd") ? date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getGet("dateEnd")))) : "",
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "month" => ""
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $productionResultData = $this->productionResultModel->getProductResultList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $currentCompanyId =  session()->get('login')->this_company_id;
        foreach ($productionResultData['data'] as &$data) {
            $qtyHasilProduksi = 0; // Dalam Kg
            $qtyHasilProduksiKaleng = 0; // Dalam Kaleng

            $productionResDetailData = $this->productionResultDetailModel->where('production_result_id', $data->id)->where('type', 'JADI')->findAll();
            foreach ($productionResDetailData as $value) {
                $qtyHasilProduksi += (float) $value['qty_isi']; // Dalam Kg
                $qtyHasilProduksiKaleng += (float) $value['qty']; // Dalam Kaleng
            }
            $data->qtyHasilProduksi = $qtyHasilProduksi;
            array_push($dataSupplier, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "pr_no"         => $data->pr_no,
                "wo_no"         => $data->wo_no,
                "barangCode"    => $data->barangCode,
                "barangName"    => $data->barangName,
                "is_posted"    => $data->is_posted,
                "receive_date"  => $data->receives_date,
                "qty_hasil"  => (in_array($currentCompanyId, [2, 16])) ?  number_format($qtyHasilProduksiKaleng) . " KALENG" : number_format($data->qtyHasilProduksi) . " KG",
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
                $value->type_barang_text = "BAHAN BAKU";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "BAHAN PENOLONG";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "BARANG JADI";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "BARANG SCRAP";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "BARANG MODAL";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "BAHAN SETENGAH JADI";
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
                $value->type_barang_text = "BAHAN BAKU";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "BAHAN PENOLONG";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "BARANG JADI";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "BARANG SCRAP";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "BARANG MODAL";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "BAHAN SETENGAH JADI";
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
                $value->type_barang_text = "BAHAN BAKU";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "BAHAN PENOLONG";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "BARANG JADI";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "BARANG SCRAP";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "BARANG MODAL";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "BAHAN SETENGAH JADI";
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
                $value->type_barang_text = "BAHAN BAKU";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "BAHAN PENOLONG";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "BARANG JADI";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "BARANG SCRAP";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "BARANG MODAL";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "BAHAN SETENGAH JADI";
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
            // ->where('work_orders.is_posted', "1")
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
                    "harga_umum" => (float) isset($bd->harga_umum) ? $bd->harga_umum :  0,
                    "harga_harian" => (float) isset($bd->harga_harian) ? $bd->harga_harian : 0,
                    "harga_bulanan" => (float) isset($bd->harga_bulanan) ? $bd->harga_bulanan : 0,
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
                        "harga_umum" => (float) isset($bd->harga_umum) ? $bd->harga_umum :  0,
                        "harga_harian" => (float) isset($bd->harga_harian) ? $bd->harga_harian : 0,
                        "harga_bulanan" => (float) isset($bd->harga_bulanan) ? $bd->harga_bulanan : 0,
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
                    "harga_umum" => (float) isset($bs->harga_umum) ? $bs->harga_umum :  0,
                    "harga_harian" => (float) isset($bs->harga_harian) ? $bs->harga_harian : 0,
                    "harga_bulanan" => (float) isset($bs->harga_bulanan) ? $bs->harga_bulanan : 0,
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
                    "harga_umum" => (float) isset($bf->harga_umum) ? $bf->harga_umum :  0,
                    "harga_harian" => (float) isset($bf->harga_harian) ? $bf->harga_harian : 0,
                    "harga_bulanan" => (float) isset($bf->harga_bulanan) ? $bf->harga_bulanan : 0,
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

            $productionResData = $this->productionResultModel->find($productionResID);

            foreach ($barangJadi as $bj) {
                $qty = isset($bj->qty_jadi) ? (float) $bj->qty_jadi : (float) $bj->qty;
                // var_dump($bj);
                if (isset($bj->production_result_detail_id)) {
                    if ($qty && $qty != 0) {
                        $datasbj = [
                            "qty" => (float) $qty,
                            "qty2" => (float) $bj->berat_isi_jadi,
                            "qty_isi" => (float) $bj->qty_isi_jadi,
                        ];
                        $this->productionResultDetailModel->update($bj->production_result_detail_id, $datasbj);
                    }
                } else {
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
            // exit;

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
        $numberTemplate = "PRD/$romanMonth/$year/";

        $lastData = $this->productionResultModel->asObject()
            ->like('pr_no', $numberTemplate)
            ->where('company_id', $this->this_company_id)
            ->orderBy('createdAt', 'DESC')
            ->first();

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->pr_no);
            $lastIncrement = intval($asd[3]) + 1;
            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);

            $invNumber = $numberTemplate . $paddedNumber;
        } else {
            $invNumber = $numberTemplate . '0001';
        }

        return $invNumber;
    }

    public function getListWorkOrderByID()
    {
        $kodeProduksi = $this->request->getVar('kode_produksi');
        
        if (!empty($kodeProduksi)) {
            // Jika multiple ID, konversi ke array
            $woIds = is_array($kodeProduksi) ? $kodeProduksi : [$kodeProduksi];
            
            // Modifikasi query untuk menangani multiple ID
            $dataResult = $this->workOrderDetailsModel
                ->select('work_order_details.*, barang.nama_barang, barang.spesifikasi, barang.kode_barang, satuan.unit')
                ->join('barang', 'barang.id = work_order_details.barang1_id', 'left')
                ->join('satuan', 'satuan.id = barang.satuan_id', 'left')
                ->whereIn('work_order_details.work_order_id', $woIds) // Gunakan whereIn untuk multiple ID
                ->where('work_order_details.deletedAt', null)
                ->findAll();
                
            foreach ($dataResult as $key => &$value) {
                if ($value['type_barang'] == "bahan_baku") {
                    $value['type_barang_text'] = "BAHAN BAKU";
                } elseif ($value['type_barang'] == "bahan_penolong") {
                    $value['type_barang_text'] = "BAHAN PENOLONG";
                } elseif ($value['type_barang'] == "bahan_jadi") {
                    $value['type_barang_text'] = "BARANG JADI";
                } elseif ($value['type_barang'] == "bahan_scrap") {
                    $value['type_barang_text'] = "BARANG SCRAP";
                } elseif ($value['type_barang'] == "bahan_modal") {
                    $value['type_barang_text'] = "BARANG MODAL";
                } elseif ($value['type_barang'] == "bahan_setengah_jadi") {
                    $value['type_barang_text'] = "BAHAN SETENGAH JADI";
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
                    $value['type_barang_text'] = "BAHAN BAKU";
                } elseif ($value['type_barang'] == "bahan_penolong") {
                    $value['type_barang_text'] = "BAHAN PENOLONG";
                } elseif ($value['type_barang'] == "bahan_jadi") {
                    $value['type_barang_text'] = "BARANG JADI";
                } elseif ($value['type_barang'] == "bahan_scrap") {
                    $value['type_barang_text'] = "BARANG SCRAP";
                } elseif ($value['type_barang'] == "bahan_modal") {
                    $value['type_barang_text'] = "BARANG MODAL";
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
        $kodeProduksi = $this->request->getVar('kode_produksi');
        
        // Jika multiple ID, konversi ke array
        $woIds = is_array($kodeProduksi) ? $kodeProduksi : [$kodeProduksi];
        
        $dataMaterialRequest = $this->materialRequestModel->asObject()
            ->select('material_requests.*, GROUP_CONCAT(material_request_details.nama_barang SEPARATOR \', \') AS nama_barang, users.name AS user_name')
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'left')
            ->join('users', 'users.id = material_requests.createdBy', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('material_requests.is_posted', 1)
            ->where('material_requests.deletedAt', null)
            ->where('material_request_details.deletedAt', null)
            ->where('material_request_details.qty_now >', 0)
            ->whereIn('material_requests.work_order_id', $woIds) // Gunakan whereIn untuk multiple ID
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
                    $cekAccount = $this->accountBarangModel->checkAccountBarangCOA($this->this_company_id, $value['divisi_id'], $value['barang1_id']);
                    if ($cekAccount) {
                        $data = [
                            "status"    => false,
                            "message"   => "Barang belum memiliki Akun COA",
                            'token'     => csrf_hash()
                        ];
                        echo json_encode($data);
                        return;
                    } else {
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
                                $resultData['receive_date'],
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

    public function deletePR()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->productionResultModel->delete($id);
        $resultDetail = $this->productionResultDetailModel->where('production_result_id', $id)->findAll();
        foreach ($resultDetail as $value) {
            $this->productionResultDetailModel->delete($value['id']);
        }
        // $this->workOrderDetailsModel->where('work_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "Hasil Produksi Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function deletePRDetail()
    {
        $id = ($this->request->getVar('id'));
        $this->productionResultDetailModel->delete($id);
        // $this->workOrderDetailsModel->where('work_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "Detail Hasil Produksi Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function printProductionResultPDF($id)
    {
        $id = decrypt($id);
        $dompdf = new Dompdf();


        //barang sisa digunakan
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
                $value->type_barang_text = "BAHAN BAKU";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "BAHAN PENOLONG";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "BARANG JADI";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "BARANG SCRAP";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "BARANG MODAL";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "BAHAN SETENGAH JADI";
            } else {
                $value->type_barang_text = "Bahan Return";
            }
        }
        //barang jadi
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
                $value->type_barang_text = "BAHAN BAKU";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "BAHAN PENOLONG";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "BARANG JADI";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "BARANG SCRAP";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "BARANG MODAL";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "BAHAN SETENGAH JADI";
            }
        }

        //barang scrap
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
                $value->type_barang_text = "BAHAN BAKU";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "BAHAN PENOLONG";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "BARANG JADI";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "BARANG SCRAP";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "BARANG MODAL";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "BAHAN SETENGAH JADI";
            }
        }

        //barang digunakan
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
                $value->type_barang_text = "BAHAN BAKU";
            } elseif ($value->barang_type == "bahan_penolong") {
                $value->type_barang_text = "BAHAN PENOLONG";
            } elseif ($value->barang_type == "bahan_jadi") {
                $value->type_barang_text = "BARANG JADI";
            } elseif ($value->barang_type == "bahan_scrap") {
                $value->type_barang_text = "BARANG SCRAP";
            } elseif ($value->barang_type == "bahan_modal") {
                $value->type_barang_text = "BARANG MODAL";
            } elseif ($value->barang_type == "bahan_setengah_jadi") {
                $value->type_barang_text = "BAHAN SETENGAH JADI";
            }
        }
        $data = [
            'barang_jadi' => $productionResDetDataBJ,
            'barang_digunakan' => $productionResDetDataBD,
            'barang_scrap' => $productionResDetDataBS,
            'barang_sisa' => $productionResDetDataBR
        ];

        $dompdf->loadHtml(view('Production/productionResult/printProductionResult', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Print Production Result", array("Attachment" => false));

        exit(0);
    }

    public function generateKodePenerimaan()
    {
        $no = $this->generatePRNo();
        return json_encode($no);
    }

    public function exportHasilProduksi()
    {
        $date_production = $this->request->getVar('date_production');
        $tipe_export = $this->request->getVar('tipe_export');
        $dateObj = DateTime::createFromFormat('d/m/Y', $date_production);

        if ($dateObj) {
            $date_production = $dateObj->format('Y-m-d');
        } else {
            $date_production = now('Y-m-d');
        }

        $data = [
            'date_production' => $date_production,
            'company_id' => $this->this_company_id,
        ];

        if ($tipe_export == 'excel') {
            $this->exportExcelHasilProduksi($data);
        } else {
            $this->exportPdfHasilProduksi($data);
        }
    }

    public function exportExcelHasilProduksi($data)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->mergeCells('A1:C1')
            ->setCellValue('A1', 'Hasil Produksi')
            ->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $spreadsheet->setActiveSheetIndex(0)
            ->mergeCells('A2:C2')
            ->setCellValue('A2', 'Tanggal Produksi : ' . date('d-m-Y', strtotime($data['date_production'])))
            ->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $dataProduksi = $this->productionResultModel->getDataProductionResultBahanBaku($data);

        $bahanBakuList = [];
        $hasilProduksiList = [];

        foreach ($dataProduksi as $item) {
            if ($item['type'] == 'DIGUNAKAN') {
                $bahanBakuList[] = $item;
            } else if ($item['type'] == 'JADI') {
                $hasilProduksiList[] = $item;
            }
        }

        // Ambil nama hasil unik sebagai kolom dinamis
        $namaHasilUnik = [];
        foreach ($hasilProduksiList as $hasil) {
            if (!in_array($hasil['barang_name'], $namaHasilUnik)) {
                $namaHasilUnik[] = $hasil['barang_name'];
            }
        }

        $sheet = $spreadsheet->getActiveSheet();

        // HEADER
        $sheet->setCellValue('A3', 'No.')
            ->setCellValue('B3', 'Nama Bahan')
            ->setCellValue('C3', 'Qty Digunakan');

        $colIndex = 4;
        $colMap = [];

        foreach ($namaHasilUnik as $hasilName) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '3', $hasilName);
            $colMap[$hasilName] = $colLetter;
            $colIndex++;
        }

        // Kolom untuk total per baris
        $totalColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($totalColLetter . '3', 'Total');

        // ISI DATA
        $row = 4;
        $no = 1;
        $colTotals = [];

        foreach ($bahanBakuList as $bahan) {
            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $bahan['barang_name'])
                ->setCellValue('C' . $row, $bahan['qty_produksi']);

            $rowTotal = 0;

            foreach ($hasilProduksiList as $hasil) {
                if ($hasil['production_result_id'] == $bahan['production_result_id']) {
                    $col = $colMap[$hasil['barang_name']] ?? null;
                    if ($col) {
                        $sheet->setCellValue($col . $row, $hasil['qty_produksi']);
                        $rowTotal += $hasil['qty_produksi'];

                        // Total per kolom
                        if (!isset($colTotals[$col])) {
                            $colTotals[$col] = 0;
                        }
                        $colTotals[$col] += $hasil['qty_produksi'];
                    }
                }
            }

            $sheet->setCellValue($totalColLetter . $row, $rowTotal);
            $row++;
        }

        // Baris total di bawah
        $sheet->setCellValue('B' . $row, 'Total');

        // Total Qty Digunakan
        $totalQtyDigunakan = array_sum(array_column($bahanBakuList, 'qty_produksi'));
        $sheet->setCellValue('C' . $row, $totalQtyDigunakan);

        $grandTotal = 0;

        foreach ($colMap as $hasilName => $colLetter) {
            $total = $colTotals[$colLetter] ?? 0;
            $sheet->setCellValue($colLetter . $row, $total);
            $grandTotal += $total;
        }

        $sheet->setCellValue($totalColLetter . $row, $grandTotal);

        // (Optional) auto-size semua kolom
        foreach (range('A', $totalColLetter) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan Hasil Produksi PT. TOBA SURIMI INDUSTRIES, Tbk (' . session()->get("login")->this_company . ') ' . date('d-m-Y', strtotime($data['date_production']));
        $encodedFilename = rawurlencode($filename . '.xlsx');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $encodedFilename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }

    public function exportPdfHasilProduksi($data)
    {
        $dataProduksi = $this->productionResultModel->getDataProductionResultBahanBaku($data);

        $bahanBakuList = [];
        $hasilProduksiList = [];

        foreach ($dataProduksi as $item) {
            if ($item['type'] == 'DIGUNAKAN') {
                $bahanBakuList[] = $item;
            } else if ($item['type'] == 'JADI') {
                $hasilProduksiList[] = $item;
            }
        }

        // Ambil nama hasil unik sebagai kolom dinamis
        $namaHasilUnik = [];
        foreach ($hasilProduksiList as $hasil) {
            if (!in_array($hasil['barang_name'], $namaHasilUnik)) {
                $namaHasilUnik[] = $hasil['barang_name'];
            }
        }

        // Susun array data untuk dikirim ke view
        $tableData = [];
        $no = 1;
        foreach ($bahanBakuList as $bahan) {
            $rowData = [
                'no' => $no++,
                'nama_bahan' => $bahan['barang_name'],
                'qty_digunakan' => $bahan['qty_produksi'],
                'hasil' => [],
                'total' => 0,
            ];

            foreach ($namaHasilUnik as $hasilNama) {
                $rowData['hasil'][$hasilNama] = 0;
            }

            foreach ($hasilProduksiList as $hasil) {
                if ($hasil['production_result_id'] == $bahan['production_result_id']) {
                    $rowData['hasil'][$hasil['barang_name']] += $hasil['qty_produksi'];
                    $rowData['total'] += $hasil['qty_produksi'];
                }
            }

            $tableData[] = $rowData;
        }

        // Hitung total per kolom
        $totalQtyDigunakan = array_sum(array_column($bahanBakuList, 'qty_produksi'));
        $colTotals = [];
        foreach ($namaHasilUnik as $hasilNama) {
            $colTotals[$hasilNama] = 0;
        }
        $grandTotal = 0;
        foreach ($tableData as $row) {
            foreach ($namaHasilUnik as $hasilNama) {
                $colTotals[$hasilNama] += $row['hasil'][$hasilNama];
            }
            $grandTotal += $row['total'];
        }

        $datas = [
            'tableData' => $tableData,
            'totalQtyDigunakan' => $totalQtyDigunakan,
            'colTotals' => $colTotals,
            'grandTotal' => $grandTotal,
            'namaHasilUnik' => $namaHasilUnik,
            'date_production' => date('d-m-Y', strtotime($data['date_production'])),
        ];

        $filename = 'Laporan Hasil Produksi PT. TOBA SURIMI INDUSTRIES, Tbk (' . session()->get("login")->this_company . ')';
        $encodedFilename = rawurlencode($filename . '.pdf');

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Production/productionResult/print', $datas));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($encodedFilename, array("Attachment" => false));
    }
}
