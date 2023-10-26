<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\StockDetailModel;
use App\Models\StockSafetyModel;

class Inventori extends BaseController
{
    public function stockSafetyView()
    {

        return view('Warehouse/stock/stock_safety');
    }

    public function stockSafetyAll()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "deletedAt"     => null
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $stockSafetyModel = new StockSafetyModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $res = $stockSafetyModel->getList($condition, $addCondition, $limit, $offset);
        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "tipeBarang"            => $data->tipe_barang,
                "safetyNumber"          => $data->safety_number,
                "reStockNumber"         => $data->re_stock_number,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function stockSafetyGet($id)
    {
        $stockSafetyModel = new StockSafetyModel();
        $res = $stockSafetyModel->where('id', $id)->first();
        return response()->setJSON([
            'data' => $res,
            'status' => true
        ]);
    }

    public function stockSafetyUpdate()
    {
        $stockSafetyModel = new StockSafetyModel();

        $safetyNumber = $this->request->getVar('safetyNumber');
        $resStockNumber = $this->request->getVar('reStockNumber');

        $stockSafetyModel->update($this->request->getVar('id'), [
            'safety_number' => $safetyNumber,
            're_stock_number' => $resStockNumber,
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Stok safety berhasil diupdate"
        ]);
    }

    public function stockHistoriView()
    {
        $stockSafetyModel = new StockSafetyModel();

        $typeSelected = 'bahan_baku';
        $typeAll = $stockSafetyModel->where('deletedAt', null)->findAll();

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
        $stockSafetyModel = new StockSafetyModel();

        $stockSafetyNumber = $stockSafetyModel->where('kode', $this->request->getGet('typeBarang'))->first();
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $stockDetailModel->getListStock($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $stockTotal = ($stockSafetyNumber == null) ? 0 : $stockSafetyNumber['safety'];
            array_push($rdata, [
                "no" => $no++,
                "id" => $data['id'],
                "company" => $data['companyName'],
                "warehouse" => $data['warehousesName'],
                "kelompok" => $data['kelompok'],
                "barang" => $data['barang_name'],
                "satuan" => $data['nama_satuan'],
                "stok" => $data['totalStock'],
                "statusStock" => ($stockTotal >= $data['totalStock']) ? "Safety" : "Harus Restok"
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
