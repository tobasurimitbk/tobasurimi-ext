<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BarangModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
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
        $dataDivisi = $this->divisiModel->asObject()->where('company_id', $this->this_company_id)->find();
        $dataWorkOrder = $this->woModel
            ->asObject()
            ->select('work_orders.*, GROUP_CONCAT(work_order_details.nama_barang) AS nama_barang')
            ->join('work_order_details', 'work_orders.id = work_order_details.work_order_id', 'left')
            ->where('company_id', $this->this_company_id)
            ->groupBy('work_order_details.work_order_id')
            ->find();

        $data = [
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
            $dataWorkOrders = $this->workOrdersModel->asObject()->find($id);
            $dataWorkOrderDetails = $this->workOrderDetailsModel->asObject()->select('work_order_details.*, barang_master.kode_barang, satuans.nama_satuan')
                ->join('barang_master', 'barang_master.id = work_order_details.barang1_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = work_order_details.barang2_id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->where('work_order_id', $id)
                ->get()->getResult();
            $data["dataWorkOrders"] = $dataWorkOrders;
            $data["dataWorkOrderDetails"] = $dataWorkOrderDetails;
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

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $workOrdersData = $this->workOrdersModel->getWorkOrderList($condition, $addCondition, $limit, $offset);

        $dataWorkOrders = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($workOrdersData['data'] as $data) {
            array_push($dataWorkOrders, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "wo_no"                 => $data->wo_no,
                "nama_barang"           => $data->nama_barang,
                "nama_divisi"           => $data->divisi,
                "standart_production"   => $data->standart_production,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $workOrdersData['totalData'],
            "recordsFiltered"   => $workOrdersData['totalFilteredData'],
            "data"              => $dataWorkOrders,
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
        $stok = $this->stockModel->where('barang1_id', $id)->where('barang2_id', $spek_id)->first();

        $condition = [
            "stock_details2.stock_id" => $stok['id'],
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $dataQry = $this->stockDetail2Model->getListStokPerDokumen($condition, $addCondition, $limit, $offset);

        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($stok['kemasan_id'] == 0) {
            // BARANG
            $barang = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);
        } else {
            // KEMASAN
            $barang = $this->kemasanModel->find($stok['kemasan_id']);
        }

        foreach ($dataQry['data'] as $data) {
            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            $stockDetail = $this->stockDetailModel->find($data->stock_detail_id);
            $dataWarehouse = $this->warehousesModel->find($stok['warehouse_id']);

            $dokumen = substr(strrchr($data->no_aju, '-'), -6);
            $tgldokumen = str_replace("-", "", $stockDetail['stock_date']);
            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($stok['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);

                array_push($dataResult, [
                    "no" => $no++,
                    "dokumen" => $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'] . "/" . $dokumen . "/" . $tgldokumen,
                    "stock"   => $stockDetail['stock_date'],
                    "warehouse"   => $dataWarehouse['warehouse_name'],
                    "kode"  => strtoupper($barangMaster['kode_barang']),
                    "barang"  => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "satuan" => $satuan_1['kode_satuan'],
                    "qty" => $data->stok_total,
                ]);
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

    public function create()
    {
        try {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            $no = $this->workOrdersModel->get_no(date('d'), date('m'), date('Y'), $last_day);
            $id = $this->workOrdersModel->insert([
                "wo_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("wo_no"),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                "request_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                'standart_production' => $this->request->getVar('standart_production'),
                'note' => $this->request->getVar('note'),
                'is_posted' => 0,
                'request_status' => "waiting",
                'createdBy' =>  session()->get("login")->user_id,
            ]);

            $wo_detail = json_decode($this->request->getVar("items"));

            foreach ($wo_detail as $s) {
                $this->workOrderDetailsModel->insert([
                    'work_order_id' => $id,
                    'barang1_id' => decrypt($s->barang_id),
                    'barang2_id' => decrypt($s->barang_spesifikasi_id),
                    'nama_barang' => $s->nama_barang,
                    'qty' => $s->qty,
                    'unit' => $s->satuan_id,
                    'note' => $s->keterangan,
                ]);
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
}
