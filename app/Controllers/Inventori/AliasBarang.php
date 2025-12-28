<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSalesModel;
use App\Models\BarangMasterSpesifikasiModel;
use Exception;

class AliasBarang extends BaseController
{
    protected $this_company_id;
    protected $barangMasterSalesModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
    }

    public function indexBarangSales()
    {
        $type = $this->request->getVar('type');

        if (empty($type) || $type  == "barang_sales") {
            return view('Warehouse/aliasBarang/indexSales');
        } else {
            return view('Warehouse/aliasBarang/indexBarangImpor');
        }
    }

    public function allBarangSales()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "barang_master_sales.company_id"  => $this->this_company_id,
            "barang_master_sales.deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => trim($this->request->getGet("search")),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataResult = $this->barangMasterSalesModel->getListAlias(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $barangResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataResult['data'] as $data) {
            array_push($barangResult, [
                "no"                => $no++,
                "id"                => encrypt($data['id']),
                "type_barang_sales" => $data['type_barang_sales'],
                "barang_sales_name" => $data['barang_sales_name'],
                "barang_inventori_name" => $data['barang_inventori_name'],
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $dataResult['totalData'],
            "recordsFiltered"   => $dataResult['totalFilteredData'],
            "data"              => $barangResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function updateAliasBarangSales()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $barangMasterId = $this->request->getVar('barang_master_id');

            $validasi = $this->barangMasterSalesModel->where('barang_master_id', $barangMasterId)->first();

            if ($validasi) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "barang inventori sudah pernah di sinkronkan",
                    'status' => false
                ]);
            }

            $this->barangMasterSalesModel->update($id, [
                'barang_master_id' => $barangMasterId
            ]);

            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Data updated",
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function allBarangImport()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "barang_master.company_id"  => $this->this_company_id,
            "barang_master_spesifikasi.deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => trim($this->request->getGet("search")),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataResult = $this->barangMasterModel->getListBarangAlias(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $barangResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataResult['data'] as $data) {
            array_push($barangResult, [
                "no" => $no++,
                "id" => encrypt($data['id']),
                "type_barang" => strtoupper(str_replace('_', ' ', $data['type_barang'])),
                "barang_impor" => $data['barang_impor'],
                "barang_alias" =>  $data['barang_alias'],
                "barang_name_alias" => $data['barang_name_alias'] ?? "",
                "spesifikasi_alias" => $data['spesifikasi_alias'] ?? ""
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $dataResult['totalData'],
            "recordsFiltered"   => $dataResult['totalFilteredData'],
            "data"              => $barangResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function updateAliasBarangImpor()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $barangNameAlias = $this->request->getVar('barang_name_alias');
            $spesifikasiAlias = $this->request->getVar('spesifikasi_alias');

            $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->where('id', $id)->first();

            $this->barangMasterSpesifikasiModel->update($id, [
                'spesifikasi_alias' => $spesifikasiAlias
            ]);

            $this->barangMasterModel->update($barangMasterSpesifikasi['barang_master_id'], [
                'barang_name_alias' => $barangNameAlias
            ]);

            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Data updated",
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }
}
