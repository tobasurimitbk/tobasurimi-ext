<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BarangModel;
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

class MaterialWarehouse extends BaseController
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

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
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
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestDetailsModel = new MaterialRequestDetailsModel();
    }

    public function index()
    {
        return view('Production/materialWarehouse/index');
    }

    public function createView()
    {
        //Get Barang
        $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $dataWarehouse = $this->warehousesModel->asObject()->where('company_id', $this->this_company_id)->find();
        $dataDivisi = $this->divisiModel->asObject()->where('company_id', $this->this_company_id)->find();

        $data = [
            "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse
        ];

        return view('Production/materialWarehouse/form', $data);
    }

    public function getById($id = null)
    {
        $id = decrypt($id);
        //Get Barang
        $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $dataWarehouse = $this->warehousesModel->asObject()->where('company_id', $this->this_company_id)->find();
        $dataDivisi = $this->divisiModel->asObject()->where('company_id', $this->this_company_id)->find();

        $data = [
            "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
        ];

        if (!empty($id)) {
            $dataMaterialRequests = $this->materialRequestModel->asObject()->find($id);
            $dataMaterialRequestswithwo = $this->materialRequestModel->getMaterialWithWorkOrder($id);
            $dataMaterialRequestDetails = $this->materialRequestDetailsModel->asObject()->select('material_request_details.*, barang_master.kode_barang, satuans.nama_satuan')
                ->join('barang_master', 'barang_master.id = material_request_details.barang1_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = material_request_details.barang2_id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->where('material_request_id', $id)
                ->get()->getResult();
            $data["dataMaterialRequests"] = $dataMaterialRequests;
            $data["dataMaterialRequestDetails"] = $dataMaterialRequestDetails;
            $data["dataMaterialRequestswithwo"] = $dataMaterialRequestswithwo;
            $data["ids"] = $id;
        }

        return view('Production/materialWarehouse/form-detail', $data);
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
            "material_requests.is_posted"        => 1,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        // var_dump($limit);
        // var_dump($offset);
        // var_dump($payload);
        // var_dump($condition);
        $materialRequestData = $this->materialRequestModel->getMaterialRequestListForMaterialWarehouse($condition, $addCondition, $limit, $offset);
        // var_dump($materialRequestData);

        $dataMaterialRequest = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($materialRequestData['data'] as $data) {
            array_push($dataMaterialRequest, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "request_date"                 => date('d/m/Y', strtotime($data->request_date)),
                "req_no"                 => $data->req_no,
                "wo_no"                 => $data->wo_no,
                "nama_barang"           => $data->nama_barang,
                "nama_divisi"           => $data->divisi,
                "warehouse"           => $data->warehouse_name,
                "user"           => $data->name,
                "satuan"           => $data->satuan,
                "is_posted"           => $data->is_posted,
                "is_approve"           => $data->is_approve,
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
            "material_requests.is_posted"        => 1,
            "material_request_details.material_request_id"        => decrypt($this->request->getGet("id")),
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        // var_dump($limit);
        // var_dump($offset);
        // var_dump($payload);
        // var_dump($condition);
        $materialRequestData = $this->materialRequestModel->getMaterialRequestListForMaterialWarehouseDetail($condition, $addCondition, $limit, $offset);


        $dataMaterialRequest = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($materialRequestData['data'] as $data) {
            array_push($dataMaterialRequest, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "ref_no"                 => $data->ref_no,
                "nama_barang"           => $data->nama_barang,
                "satuan"           => $data->satuan,
                "total"           => $data->total,
                "note"           => $data->note,
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

    public function updateStatusApproveMaterialRequest()
    {
        try {

            $id = $this->request->getVar('id');
            $id = decrypt($id);

            // po posting
            $payload = [
                "is_approve" => $this->request->getVar('status_posting'),
                "approveBy" => session()->get("login")->user_id,
            ];


            if (!empty($id)) {
                $materialRequestData = $this->materialRequestModel->find($id);
                $materialRequestDetailData = $this->materialRequestDetailsModel->where('material_request_id', $id)->groupBy('material_request_details.barang1_id')->findAll();
                $workOrderData = $this->workOrdersModel->find($materialRequestData['work_order_id']);
                // var_dump($materialRequestData);
                foreach ($materialRequestDetailData as $key => $value) {
                    $barangMasterData = $this->barangMasterModel->find($value['barang1_id']);

                    // $data = [
                    //     'this_company_id' => $this->this_company_id,
                    //     'warehouse_id' => $materialRequestData['warehouse_id'],
                    //     'divisi_id' => $materialRequestData['divisi_id'],
                    //     'type_barang' => $barangMasterData['type_barang'],
                    //     'barang1_id' => $value['barang1_id'],
                    //     'barang2_id' => $value['barang2_id'],
                    //     'qty' => ($value['qty'] * -1),
                    // ];
                    // var_dump($data);
                    // exit;
                    $stok = $this->stockModel->insertStok(
                        $this->this_company_id,
                        "3",
                        "28",
                        $barangMasterData['type_barang'],
                        $value['barang1_id'],
                        $value['barang2_id'],
                        ($value['qty'] * -1),
                    );

                    // DETAIL
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        $value['qty'],
                        "Out",
                        date('Y-m-d'),
                        $this->this_user_id,
                        "PRODUKSI",
                        $workOrderData['wo_no'],
                        $workOrderData['note'] ? $workOrderData['note'] : "-",
                    );

                    // SUB DETAIL
                    $stokDetail2 = $this->stockDetail2Model->insertStokDetail2(
                        $value['bc_id'],
                        $stok,
                        $stokDetail,
                        $value['qty'],
                        $value['no_aju'],
                        $materialRequestData['req_no']
                    );
                }
                $this->materialRequestModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "message"   => "Material Request Berhasil Diapprove",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];

                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Diapprove",
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
