<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Models\AccountBarangModel;
// use App\Models\BarangModel;
use App\Models\DivisisModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestsModel;
use App\Models\ProductionResultDetailModel;
use App\Models\ProductionResultModel;
use App\Models\SatuansModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;

class WorkOrder extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $barangModel;
    protected $satuanModel;
    protected $workOrdersModel;
    protected $workOrderDetailsModel;
    protected $warehousesModel;
    protected $divisiModel;
    protected $materialRequestModel;
    protected $materialRequestDetailsModel;
    protected $productionResultModel;
    protected $productionResultDetailsModel;
    protected $accountBarangModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        // $this->barangModel = new BarangModel();
        $this->satuanModel = new SatuansModel();
        $this->workOrdersModel = new WorkOrdersModel();
        $this->workOrderDetailsModel = new WorkOrderDetailsModel();
        $this->warehousesModel = new WarehousesModel();
        $this->divisiModel = new DivisisModel();
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestDetailsModel = new MaterialRequestDetailsModel();
        $this->productionResultModel = new ProductionResultModel();
        $this->productionResultDetailsModel = new ProductionResultDetailModel();
        $this->accountBarangModel = new AccountBarangModel();
    }

    public function index()
    {
        return view('Production/workOrder/index');
    }

    public function createView()
    {
        //Get Barang
        // $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $dataWarehouse = $this->warehousesModel->asObject()->where('company_id', $this->this_company_id)->find();
        $dataDivisi = $this->divisiModel->asObject()->where('company_id', $this->this_company_id)->find();

        $data = [
            // "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
        ];

        return view('Production/workOrder/form', $data);
    }

    public function getById($id = null)
    {
        $id = decrypt($id);
        //Get Barang
        // $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $dataWarehouse = $this->warehousesModel->asObject()->where('company_id', $this->this_company_id)->find();
        $dataDivisi = $this->divisiModel->asObject()->where('company_id', $this->this_company_id)->find();

        $data = [
            // "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
        ];

        if (!empty($id)) {
            $dataWorkOrders = $this->workOrdersModel->asObject()->find($id);
            $dataWorkOrderDetails = $this->workOrderDetailsModel->asObject()->select('work_order_details.*, barang_master.kode_barang')
                ->join('barang_master', 'barang_master.id = work_order_details.barang1_id', 'left')
                ->where('work_order_id', $id)
                ->where('work_order_details.deletedAt', null)
                ->get()->getResult();
            $data["dataWorkOrders"] = $dataWorkOrders;
            $data["dataWorkOrderDetails"] = $dataWorkOrderDetails;
        }

        return view('Production/workOrder/form', $data);
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
            "work_orders.company_id"        => $this->this_company_id
        ];

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
            // $materialRequestData = $this->materialRequestModel->asObject()->where('work_order_id', $data->id)->where('deletedAt', null)->where('is_posted', 1)->find();
            // $materialRequestId = isset($materialRequestData[0]->id) ? $materialRequestData[0]->id : null;
            $productionData = $this->productionResultModel->asObject()->where('work_order_id', $data->id)->where('deletedAt', null)->find();
            $productionResultId = isset($productionData[0]->id) ? $productionData[0]->id : null;
            array_push($dataWorkOrders, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                // "id_material_request"   => $materialRequestId ? encrypt($materialRequestId) : 0,
                "id_production_result"  => $productionResultId ? encrypt($productionResultId) : 0,
                "wo_no"                 => $data->wo_no,
                "nama_barang"           => $data->nama_barang,
                "nama_divisi"           => $data->divisi,
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

    public function create()
    {
        try {

            $id = $this->workOrdersModel->insert([
                "wo_no" => $this->request->getVar("wo_no"),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                'warehouse_id' => $this->request->getVar("warehouse_id"),
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
                    'nama_barang' => $s->nama_barang,
                    'qty' => $s->qty,
                    'note' => $s->keterangan,
                ]);
                $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('department_id'), $s->barang_id);
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
            $id = ($this->request->getPost("id"));
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            $no = $this->workOrdersModel->get_no(date('d'), date('m'), date('Y'), $last_day);
            // $no = $this->workOrdersModel->get_no();
            $payload = [
                "wo_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("wo_no"),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                'warehouse_id' => $this->request->getVar("warehouse_id"),
                "request_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                'standart_production' => $this->request->getVar('standart_production'),
                'note' => $this->request->getVar('note'),
                'is_posted' => 0,
                'request_status' => "waiting",
                'createdBy' =>  session()->get("login")->user_id,
            ];

            // $condition = [
            //     'id' => $id
            // ];

            $response = $this->workOrdersModel->update($id, [
                "wo_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("wo_no"),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                'warehouse_id' => $this->request->getVar("warehouse_id"),
                "request_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                'standart_production' => $this->request->getVar('standart_production'),
                'note' => $this->request->getVar('note'),
                'is_posted' => 0,
                'request_status' => "waiting",
                'createdBy' =>  session()->get("login")->user_id,
            ]);

            $wo_detail = json_decode($this->request->getVar("items"));

            foreach ($wo_detail as $s) {
                if (decrypt($s->work_order_detail_id) != "") {
                    $this->workOrderDetailsModel->update(decrypt($s->work_order_detail_id), [
                        'barang1_id' => decrypt($s->barang_id),
                        'nama_barang' => $s->nama_barang,
                        'qty' => $s->qty,
                        'note' => $s->keterangan,
                    ]);
                } else {
                    $this->workOrderDetailsModel->insert([
                        'work_order_id' => $id,
                        'barang1_id' => decrypt($s->barang_id),
                        'nama_barang' => $s->nama_barang,
                        'qty' => $s->qty,
                        'note' => $s->keterangan,
                    ]);
                }
            }

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message =  'Data Gagal Diubah';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
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

    public function deleteWO()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->workOrdersModel->delete($id);
        $this->workOrderDetailsModel->where('work_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "Work Order Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function deleteWODetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->workOrderDetailsModel->where('id', $id)->delete();

        return response()->setJSON([
            'message' => "Barang Work Order Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function generateKodeProduksi()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $no = $this->workOrdersModel->get_no(date('d'), date('m'), date('Y'), $last_day, $this->this_company_id);

        return json_encode($no);
    }
}
