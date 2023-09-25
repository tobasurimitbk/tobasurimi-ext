<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

use App\Models\BarangModel;
use App\Models\MetadataModel;
use App\Models\StockDetailModel;
use App\Models\WarehousesModel;

class Stock extends BaseController
{
    private $token;
    private $companyId;

    private $barangModel;
    private $metadataModel;
    private $stockDetModel;
    private $warehousesModel;
    
    public function __construct()
    {   
        $this->token = session()->get("login")->token;
        $this->companyId = session()->get("login")->this_company_id;
        
        $this->barangModel = new BarangModel();
        $this->metadataModel = new MetadataModel();
        $this->stockDetModel = new StockDetailModel();
        $this->warehousesModel = new WarehousesModel();
    }
    
    public function index()
    {
        // get stock list
        $barangData = $this->barangModel->asObject()
            ->where('company_id', $this->companyId)
            ->groupStart()
                ->where('spec_type', 'single')
                ->orGroupStart()
                    ->where('spec_type', 'multi')
                    ->where('parent_id !=', 0)
                ->groupEnd()
            ->groupEnd()
            ->findAll();

        $warehouseData = $this->warehousesModel->asObject()
            ->where('company_id', $this->companyId)
            ->findAll();

        // Get Kategori
        $kategoriData = $this->metadataModel->get_by_name('Kategori Barang');

        $data = [
            'barangData'    => $barangData,
            'warehouseData' => $warehouseData,
            'kategoriData'  => $kategoriData
        ];

        return view('Warehouse/stock/index', $data);
    }

    public function allStock()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "idCategory"    => formatter($this->request->getGet("kategori"), "STR_TO_INT"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            // "idCompany"     => $this->companyId
        ];

        $condition = [
            "barangs.company_id"    => $this->companyId
        ];
        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "kategori"      => formatter($this->request->getGet("kategori"), "STR_TO_INT")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $barangData = $this->barangModel->getStockList($condition, $addCondition, $limit, $offset);

        $dataBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($barangData['data'] as $data) {
            array_push($dataBarang, [
                'no'            => $no++,
                'id'            => $data->id,
                'parent_barang' => $data->barangParent,
                'kode_barang'   => $data->kodeBarang,
                'nama_barang'   => $data->barangName,
                'type'          => $data->type,
                'kode_satuan'   => $data->kodeSatuan,
                'warehouseId'   => $data->warehouseId,
                'warehouse'     => $data->warehouseName,
                'kategori'      => $data->kategori,
                'qty'           => floatval($data->qty)
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $barangData['totalData'],
            "recordsFiltered"   => $barangData['totalFilteredData'],
            "data"              => $dataBarang,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function addNewStock()
    {
        try {
            $rules = [
                "barang" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Barang tidak boleh kosong'
                    ]
                ],
                "warehouse" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Warehouse tidak boleh kosong'
                    ]
                ],
                "qty" => [
                    "rules" => "required|numeric|greater_than[0]",
                    'errors' => [
                        'required' => 'Qty tidak boleh kosong'
                    ]
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

            $barangId = (int) $this->request->getPost('barang');
            $warehouseId = (int) $this->request->getPost('warehouse');
            $qty = floatval($this->request->getPost('qty'));

            $barangData = $this->barangModel->asObject()
                ->where('company_id', $this->companyId)
                ->find($barangId);

            if (empty($barangData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Barang tidak Ditemukan!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $warehouseData = $this->warehousesModel->asObject()
                ->where('company_id', $this->companyId)
                ->find($warehouseId);

            if (empty($warehouseData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Warehouse tidak Ditemukan!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $this->barangModel->db->transException(true)->transStart();

            // increment stock qty in master barang
            $hah=$this->barangModel->where('id', $barangId)
                ->set(['stok' => "(`stok` + $qty)"])
                ->update();

            // add stock detail here
            $this->stockDetModel->addStock($barangId, $warehouseId, $qty);

            $this->barangModel->db->transComplete();

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
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

    public function getStockInfo($barangId, $warehouseId)
    {
        $stockData = $this->stockDetModel->asObject()
            ->select('qty, DATE_FORMAT(stock_date, "%d %b %Y") AS stockDate')
            ->where('qty >', 0)
            ->where('barang_id', $barangId)
            ->where('warehouse_id', $warehouseId)
            ->orderBy('stock_date', 'DESC')
            ->findAll();

        if (empty($stockData)) {
            $data = [
                "status"    => false,
                "message"   => 'Stock tidak Ditemukan!',
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }

        $no = 1;

        foreach ($stockData as &$stock) {
            $stock->no = $no;
            $stock->qty = floatval($stock->qty);

            $no++;
        }

        $data = [
            'status'    => true,
            'stockData' => $stockData
        ];
        echo json_encode($data);
        return;
    }
}
