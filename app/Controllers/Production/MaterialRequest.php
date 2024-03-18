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

class MaterialRequest extends BaseController
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

    public function __construct()
    {
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
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestDetailsModel = new MaterialRequestDetailsModel();
    }

    public function index()
    {
        return view('Production/materialRequest/index');
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
            ->where('work_orders.deletedAt', null)
            ->where('work_order_details.deletedAt', null)
            ->groupBy('work_order_details.work_order_id')
            ->find();

        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
            "dataWorkOrder" => $dataWorkOrder,
        ];

        return view('Production/materialRequest/form', $data);
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

        return view('Production/materialRequest/form-detail', $data);
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

        $condition = [];

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
                "nama_divisi"           => $data->divisi,
                "is_posted"           => $data->is_posted,
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

    public function allDataBarang()
    {
        // Mendapatkan nilai $id dan $spek_id dari request
        $id = $this->request->getVar('id');
        $spek_id = $this->request->getVar('spek_id');

        // Dekripsi nilai $id dan $spek_id jika perlu
        if ($id !== null || $id !== "") {
            $id = decrypt($id);
        }
        if ($spek_id !== null || $spek_id !== "") {
            $spek_id = decrypt($spek_id);
        }

        // Jika $id atau $spek_id kosong, kembalikan respons kosong
        if ($id === null || $spek_id === null || $id === "" || $spek_id === "" || $id === 0 || $spek_id === 0) {
            return response()->setJSON([
                "draw"              => intval($this->request->getVar("draw")),
                "recordsTotal"      => 0,
                "recordsFiltered"   => 0,
                "data"              => [],
                "payload"           => [],
            ]);
        }


        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "bc_id" => $this->request->getVar("bc_id"),
            "no_aju" => $this->request->getVar("no_aju"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $stok = $this->stockModel->where('barang1_id', $id)
            ->where('barang2_id', $spek_id)
            ->findAll(); // Menggunakan findAll() untuk mendapatkan semua hasil yang sesuai

        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($stok as $singleStok) {
            $condition = [
                "stock_details2.stock_id" => $singleStok['id'],
                "stock_details2.deletedAt" => null,
                "stock_details.deletedAt" => null,
            ];

            $dataQry = $this->stockDetail2Model->getListStokPerDokumen($condition, $addCondition, $limit, $offset);

            foreach ($dataQry['data'] as $data) {
                $dokumenBC = $this->metaDataModel->find($data->bc_id);
                $stockDetail = $this->stockDetailModel->find($data->stock_detail_id);
                $dataWarehouse = $this->warehousesModel->find($singleStok['warehouse_id']);

                $dokumen = substr(strrchr($data->no_aju, '-'), -6);
                $tgldokumen = str_replace("-", "", $stockDetail['stock_date']);

                if ($singleStok['kemasan_id'] == 0) {
                    // BARANG
                    $barang = $this->barangMasterSpesifikasiModel->find($singleStok['barang2_id']);
                    $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
                    $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                    $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                    $barangMaster = $this->barangMasterModel->find($singleStok['barang1_id']);
                    $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($singleStok['barang2_id']);

                    array_push($dataResult, [
                        "no" => $no++,
                        "dokumen" => $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'] . "/" . $dokumen . "/" . $tgldokumen,
                        "stock"   => $stockDetail['stock_date'],
                        "warehouse"   => $dataWarehouse['warehouse_name'],
                        "kode"  => strtoupper($barangMaster['kode_barang']),
                        "barang"  => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                        "satuan" => $satuan_1['kode_satuan'],
                        "qty" => $data->stok_total,
                        "stock_id" => encrypt($singleStok['id']),
                        "bc_id" => encrypt($data->bc_id),
                        "barang1_id" => encrypt($id),
                        "barang2_id" => encrypt($spek_id),
                        "no_aju" => $data->no_aju,
                    ]);
                }
            }
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
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
            $no = $this->materialRequestModel->get_no(date('d'), date('m'), date('Y'), $last_day);

            $dataMaterial = [
                "work_order_id" => $this->request->getPost("kode_produksi"),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                'warehouse_id' => $this->request->getVar("warehouse_id"),
                "production_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                "request_date" => $this->request->getVar("date_request") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_request")))) : "",
                "req_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("req_no"),
                'is_posted' => 0,
                'createdBy' =>  session()->get("login")->user_id,
            ];
            $id = $this->materialRequestModel->insert($dataMaterial);

            $mr_detail = json_decode($this->request->getVar("listMaterial"));


            foreach ($mr_detail as $s) {
                $stockBarang =  $this->stockModel->asObject()->find($s->stock_id);
                $stockDetailBarang =  $this->stockDetailModel->asObject()->find($s->stock_detail_id);
                $dataMaterialDetail = [
                    'material_request_id' => $id,
                    'barang1_id' => $stockBarang->barang1_id,
                    'barang2_id' => $stockBarang->barang2_id,
                    'nama_barang' => $s->barang,
                    'satuan' => $s->satuan,
                    'stock_id' => $s->stock_id,
                    'bc_id' => $s->bc_id,
                    'no_aju' => $s->no_aju,
                    'ref_no' => $s->bc_type,
                    'stock_date' => $stockDetailBarang->stock_date,
                    'qty' => $s->qty,
                ];
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
            $rules = [
                "barang_id" => [
                    "rules" => "required"
                ],
                "production_amt" => [
                    "rules" => "required"
                ],
                "satuan_id" => [
                    "rules" => "required"
                ],
                "target" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");
                $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                $no = $this->workOrdersModel->get_no(date('d'), date('m'), date('Y'), $last_day);
                // $no = $this->workOrdersModel->get_no();
                $payload = [
                    "wo_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("wo_no"),
                    "barang_id" => formatter($this->request->getPost("barang_id"), "STR_TO_INT"),
                    "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                    "target" => $this->request->getPost("target"),
                    "production_amt" => $this->request->getPost("production_amt")
                ];

                $condition = [
                    'id' => $id
                ];

                $response = $this->workOrdersModel->where($condition)->set($payload)->update();

                if ($response) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message =  'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            }
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
                "is_posted" => $this->request->getVar('status_posting')
            ];

            if (!empty($id)) {
                $this->materialRequestModel->update($id, $payload);
                $data = [
                    "status"    => true,
                    "message"   => "Status Posting Berhasil Diperbaharui",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];

                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
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
