<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\MetadataModel;
use App\Models\StockDetailModel;

class Inventori extends BaseController
{
    public function stockHistoriView()
    {
        $metaDataModel = new MetadataModel();

        $typeSelected = 'bahan_baku';
        $typeAll = $metaDataModel->where('name', "Kategori Barang")->findAll();

        if (isset($_GET['type'])) {
            $typeSelected = $_GET['type'];
        }

        $res = [
            'typeAll' => $typeAll,
            'typeSelected' => $typeSelected
        ];
        return view('Warehouse/stock/stock_history', $res);
    }

    public function stockHistoriAll()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "barang_master.type_barang" => $this->request->getGet('typeBarang'),
            "barang_master.deletedAt" => NULL,
            "stock_details.deletedAt" => NULL
        ];

        $addCondition = [
            'search' => $this->request->getGet('search'),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $stockDetailModel = new StockDetailModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $stockDetailModel->getListHistori($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no" => $no++,
                "id" => $data['id'],
                "tanggal" => date('d/m/Y', strtotime($data['stock_date'])),
                "company" => $data['companyName'],
                "warehouse" => $data['warehousesName'],
                "kondisi" => $data['stock_type'] == "New" ? "Baru" : "Bekas",
                "barang" => $data['barang_name'],
                "spesifikasi" => $data['spesifikasi'],
                "satuan" => $data['nama_satuan'],
                "status" => strtoupper($data['status']),
                "stok" => $data['qty']
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }

    public function stockListView()
    {
        $metaDataModel = new MetadataModel();

        $typeSelected = 'bahan_baku';
        $typeAll = $metaDataModel->where('name', "Kategori Barang")->findAll();

        if (isset($_GET['type'])) {
            $typeSelected = $_GET['type'];
        }

        $res = [
            'typeAll' => $typeAll,
            'typeSelected' => $typeSelected
        ];
        return view('Warehouse/stock/stock_list', $res);
    }

    public function stockListAll()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "barang_master.type_barang" => $this->request->getGet('typeBarang'),
            "barang_master.deletedAt" => NULL,
            "stock_details.deletedAt" => NULL
        ];

        $addCondition = [
            'search' => $this->request->getGet('search'),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $stockDetailModel = new StockDetailModel();
        $barangMasterModel = new BarangMasterModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $stockDetailModel->getListStock($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $barangMaster = $barangMasterModel->where('id', $data['id'])->first();
            $stockTotal = ($barangMaster == null) ? 0 : $barangMaster['minimum_stock'];
            array_push($rdata, [
                "no" => $no++,
                "id" => $data['id'],
                "company" => $data['companyName'],
                "warehouse" => $data['warehousesName'],
                "kelompok" => $data['kelompok'],
                "barang" => $data['barang_name'],
                "satuan" => $data['nama_satuan'],
                "stok" => $data['totalStock'],
                "statusStock" => ($stockTotal <= $data['totalStock']) ? "Safety" : "Harus Restok",
                "minimumStock" => $barangMaster['minimum_stock'],
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }
}
