<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Controllers\Master\Divisi;
use App\Models\BarangModel;
use App\Models\DivisisModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestsModel;
use App\Models\ProductionResultModel;
use App\Models\ProductionResultDetailModel;
use App\Models\StockDetailModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;

class ProductionResult extends BaseController
{
    private $this_company_id;

    private $barangModel;
    private $productionResultModel;
    private $productionResultDetailModel;
    private $stockDetailModel;
    private $warehousesModel;
    private $workOrdersModel;
    private $workOrderDetailsModel;
    private $divisiModel;
    private $materialRequestModel;
    private $materialRequestDetailModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;

        $this->barangModel = new BarangModel();
        $this->productionResultModel = new ProductionResultModel();
        $this->productionResultDetailModel = new ProductionResultDetailModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->warehousesModel = new WarehousesModel();
        $this->workOrderDetailsModel = new WorkOrderDetailsModel();
        $this->workOrdersModel = new WorkOrdersModel();
        $this->divisiModel = new DivisisModel();
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestDetailModel = new MaterialRequestDetailsModel();
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
                "id"            => $data->id,
                "pr_no"         => $data->pr_no,
                "wo_no"         => $data->wo_no,
                "barangCode"    => $data->barangCode,
                "barangName"    => $data->barangName,
                "warehouseName" => $data->warehouseName,
                "receive_date"  => $data->receive_date
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
        $productionResData = $this->productionResultModel->asObject()
            ->select("*, DATE_FORMAT(receive_date, '%d/%m/%Y') AS receive_date")
            ->find($id);

        $productionResDetSelect = "production_result_details.*, 
                                   barangs.nama_barang AS nama_barang, 
                                   barangs.kode_barang AS kode_barang, 
                                   satuans.kode_satuan AS nama_satuan, 
                                   qty AS jumlah";
        $productionResDetData = $this->productionResultDetailModel->asObject()
            ->select($productionResDetSelect)
            ->join('barangs', 'barangs.id = production_result_details.barang_id')
            ->join('satuans', 'satuans.id = barangs.satuan_id')
            ->where('production_result_id', $id)
            ->findAll();

        $barangData = $this->barangModel->asObject()
            ->select('barangs.*, satuans.kode_satuan AS unit')
            ->where('company_id', $this->this_company_id)
            ->where('parent_id !=', 0)
            ->join('satuans', 'satuans.id = barangs.satuan_id')
            ->findAll();

        $warehouseData = $this->warehousesModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->findAll();

        $workOrderData = $this->workOrdersModel->asObject()
            ->select("work_orders.id AS id, CONCAT(wo_no, ' - ', barangs.nama_barang) AS wo_no")
            ->join('barangs', 'barangs.id = work_orders.barang_id')
            ->findAll();

        $barangJadi = (object)[];
        $barangSetengahJadiList = [];
        $scrapList = [];
        $materialReturnList = [];

        foreach ($productionResDetData as $detData) {

            if ($detData->barang_type === 'Barang Jadi') {
                $barangJadi = $detData;
            } elseif ($detData->barang_type === 'Barang Setengah Jadi') {
                $barangSetengahJadiList[] = $detData;
            } elseif ($detData->barang_type === 'Scrap') {
                $scrapList[] = $detData;
            } elseif ($detData->barang_type === 'Material Return') {
                $materialReturnList[] = $detData;
            }
        }

        $data = [
            'data'                  => $productionResData,
            'barangJadi'            => $barangJadi,
            'barangSetengahJadi'    => $barangSetengahJadiList,
            'scrap'                 => $scrapList,
            'materialReturn'        => $materialReturnList,
            'barangData'            => $barangData,
            'workOrders'            => $workOrderData,
            'warehouses'            => $warehouseData
        ];
        return view('Production/productionResult/form', $data);
    }

    public function createProductionResult()
    {
        $barangData = $this->barangModel->asObject()
            ->select('barangs.*, satuans.kode_satuan AS unit')
            ->where('company_id', $this->this_company_id)
            ->where('parent_id !=', 0)
            ->join('satuans', 'satuans.id = barangs.satuan_id')
            ->findAll();


        $dataWarehouse = $this->warehousesModel->asArray()->where('company_id', $this->this_company_id)->findAll();
        $dataDivisi = $this->divisiModel->asArray()->where('company_id', $this->this_company_id)->findAll();

        $dataWorkOrder = $this->workOrdersModel->asObject()
            ->select('work_orders.*, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR \', \') AS nama_barang')
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
            ->where('company_id', $this->this_company_id)
            ->where('work_orders.deletedAt', null)
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
            $postData = $this->request->getPost();
            $postData["barang_setengah_jadi"] = json_decode($postData["barang_setengah_jadi"]);
            $postData["scrap"] = json_decode($postData["scrap"]);
            $postData["material_return"] = json_decode($postData["material_return"]);

            $rules = [
                "work_order" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "warehouse" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "receive_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "barang_jadi_qty" => [
                    "rules" => "required|greater_than_equal_to[0]"
                ],
                "barang_setengah_jadi.*.id" => [
                    "rules" => "permit_empty|is_natural_no_zero"
                ],
                "barang_setengah_jadi.*.jumlah" => [
                    "rules" => "permit_empty|greater_than_equal_to[0]"
                ],
                "scrap.*.id" => [
                    "rules" => "permit_empty|is_natural_no_zero"
                ],
                "scrap.*.jumlah" => [
                    "rules" => "permit_empty|greater_than_equal_to[0]"
                ],
                "material_return.*.id" => [
                    "rules" => "permit_empty|is_natural_no_zero"
                ],
                "material_return.*.jumlah" => [
                    "rules" => "permit_empty|greater_than_equal_to[0]"
                ]
            ];

            if (!$this->validateData($postData, $rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $workOrderData = $this->workOrdersModel->asObject()
                ->find($postData['work_order']);

            if (empty($workOrderData)) {
                $data = [
                    "status"     => false,
                    "message"    => 'Work Order not Found!',
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $warehouseData = $this->warehousesModel->asObject()
                ->where('company_id', $this->this_company_id)
                ->find($postData['warehouse']);

            if (empty($warehouseData)) {
                $data = [
                    "status"     => false,
                    "message"    => 'Warehouse not Found!',
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $insertData = [
                'work_order_id' => $postData['work_order'],
                'warehouse_id'  => $postData['warehouse'],
                'pr_no'         => $this->generatePRNo(),
                'receive_date'  => date("Y-m-d", strtotime(str_replace("/", "-", $postData['receive_date'])))
            ];

            $arrProductionResDet = [
                'barang_id'     => $workOrderData->barang_id,
                'qty'           => $postData['barang_jadi_qty'],
                'barang_type'   => 'Barang Setengah Jadi'
            ];

            foreach ($postData['barang_setengah_jadi'] as $barangSetengahJadi) {
                $setengahJadiId = $barangSetengahJadi->id;
                $setengahJadiQty = $barangSetengahJadi->jumlah;

                $arrProductionResDet[] = [
                    'barang_id'     => $setengahJadiId,
                    'qty'           => $setengahJadiQty,
                    'barang_type'   => 'Barang Setengah Jadi'
                ];
            }

            foreach ($postData['scrap'] as $scrap) {
                $scrapId = $scrap->id;
                $scrapQty = $scrap->jumlah;

                $arrProductionResDet[] = [
                    'barang_id'     => $scrapId,
                    'qty'           => $scrapQty,
                    'barang_type'   => 'Scrap'
                ];
            }

            foreach ($postData['material_return'] as $materialReturn) {
                $materialReturnId = $materialReturn->id;
                $materialReturnQty = $materialReturn->jumlah;

                $arrProductionResDet[] = [
                    'barang_id'     => $materialReturnId,
                    'qty'           => $materialReturnQty,
                    'barang_type'   => 'Material Return'
                ];
            }

            $this->productionResultModel->db->transException(true)->transStart();
            $productionResultId = $this->productionResultModel->insert($insertData);

            foreach ($arrProductionResDet as &$det) {
                $det['production_result_id'] = $productionResultId;

                // increase stock
                $this->barangModel->where('id', $det['barang_id'])
                    ->increment('stok', $det['qty']);

                //insert to stock detail
                $this->stockDetailModel->addStock($det['barang_id'], $postData['warehouse'], $det['qty']);
            }

            $this->productionResultDetailModel->insertBatch($arrProductionResDet);

            $this->productionResultModel->db->transComplete();

            $data = [
                "status"    => true,
                "id"        => $productionResultId,
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
        $numberTemplate = "/PR/$romanMonth/$year";

        $lastData = $this->productionResultModel->asObject()
            ->like('pr_no', $numberTemplate, 'before')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $invNumber = '001' . $numberTemplate;

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->pr_no);
            $lastIncrement = intval($asd[0]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
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
