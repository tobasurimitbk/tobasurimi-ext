<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\ParentBarangModel;

class ParentBarang extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        $metaDataModel = new MetadataModel();

        $type = "bahan_baku";

        if (!empty(@$_GET['type'])) {
            $type = $this->request->getGet('type');
        }

        $data = [
            'type' => $type,
            'kategoriBP' => $metaDataModel->where('name', 'Kelompok BP')->findAll()
        ];
        return view('Warehouse/parentBarang/index', $data);
    }

    public function create()
    {
        $parentBarangModel = new ParentBarangModel();

        $type = $this->request->getVar('type');
        $parentName = $this->request->getVar('parentName');
        $kategori = $this->request->getVar('kategori');

        if ($parentBarangModel->where('parent_name', $parentName)->where('parent_type', $type)->first() != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Ups Kelompok Barang $parentName Sudah Ada"
            ]);
        }

        $parentBarangModel->insert([
            'parent_type' => ($type == "") ? "bahan_baku" : $type,
            'parent_name' => $parentName,
            'kategori' => $kategori,
            'company_id' => $this->this_company_id
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Kelompok Barang $parentName Berhasil Ditambahkan"
        ]);
    }

    public function update()
    {
        $parentBarangModel = new ParentBarangModel();
        $parentName = $this->request->getVar('parentName');
        $kategori = $this->request->getVar('kategori');
        $id = $this->request->getVar('id');

        $parentBarangModel->update($id, [
            'parent_name' => $parentName,
            'kategori' => $kategori
        ]);

        return response()->setJSON([
            'token' => \csrf_hash(),
            'status' => true,
            'message' => "Kelompok Barang $parentName Berhasil Diupdate"
        ]);
    }

    public function delete()
    {
        $parentBarangModel = new ParentBarangModel();
        $id = $this->request->getVar('id');

        $rememberName = $parentBarangModel->where('id', $id)->first()['parent_name'];
        $parentBarangModel->update($id, [
            'deletedAt' => date('Y-m-d H:i:s')
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Kelompok Barang $rememberName Berhasil Dihapus"
        ]);
    }

    public function get()
    {
        $id = $this->request->getVar('id');
        $parentBarangModel = new ParentBarangModel();

        return response()->setJSON([
            'data' => $parentBarangModel->where('id', $id)->first(),
            'token' => csrf_hash(),
            'status' => true,
        ]);
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
            "company_id"  => $this->this_company_id,
            "parent_type" => $this->request->getGet('parent_type'),
            "deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $parentBarangModel = new ParentBarangModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $parentBarangModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data['id'],
                "parent_name"           => $data['parent_name'],
                "kategori"              => $data['kategori'] == null ? "-" : $data['kategori'],
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
            "test" => $_GET
        ];

        return response()->setJSON($data);
    }
}
