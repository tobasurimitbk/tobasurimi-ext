<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
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
        $type = "bahan_baku";

        if (!empty(@$_GET['type'])) {
            $type = $this->request->getGet('type');
        }

        $data = [
            'type' => $type,
        ];
        return view('Warehouse/parentBarang/index', $data);
    }

    public function create()
    {
        $parentBarangModel = new ParentBarangModel();

        $type = $this->request->getVar('type');
        $parentName = $this->request->getVar('parentName');

        if ($parentBarangModel->where('parent_name', $parentName)->where('parent_type', $type)->where('deletedAt', null)->first() != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Ups Kelompok Barang $parentName Sudah Ada"
            ]);
        }

        $parentBarangModel->insert([
            'parent_type' => ($type == "") ? "bahan_baku" : $type,
            'parent_name' => $parentName,
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
        $id = decrypt($this->request->getVar('id'));

        $parentBarangModel->update($id, [
            'parent_name' => $parentName,
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Kelompok Barang $parentName Berhasil Diupdate"
        ]);
    }

    public function delete()
    {
        $parentBarangModel = new ParentBarangModel();
        $id = decrypt($this->request->getVar('id'));

        $rememberName = $parentBarangModel->where('id', $id)->first()['parent_name'];
        $parentBarangModel->delete($id);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Kelompok Barang $rememberName Berhasil Dihapus"
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $parentBarangModel = new ParentBarangModel();
        $res = $parentBarangModel->where('id', $id)->first();
        $res['id'] = encrypt($res['id']);

        return response()->setJSON([
            'data' => $res,
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
                "id"                    => encrypt($data['id']),
                "parent_name"           => $data['parent_name'],
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

    public function dropdownKategoriBarang()
    {
        $parentBarangModel = new ParentBarangModel();

        $parent_type = $this->request->getVar('parent_type');
        return response()->setJSON([
            'data' => $parentBarangModel->where('parent_type', $parent_type)->findAll(),
            'status' => true,
            'token' => csrf_hash()
        ]);
    }
}
