<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\KawasanModel;

class Kawasan extends BaseController
{
    protected $this_company_id;
    protected $kawasanModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->kawasanModel = new KawasanModel();
    }

    public function index()
    {
        return view('Master/kawasan/index');
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
            "kawasan.company_id"  => $this->this_company_id,
            "kawasan.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "parent_type_id" => $this->request->getGet('parent_type_id'),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->kawasanModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "name"                  => $data['name'],
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

    public function create()
    {
        $name = strtoupper($this->request->getVar('name'));
        $kawasanFirst = $this->kawasanModel->where('name', $name)->where('company_id', $this->this_company_id)->first();
        if ($kawasanFirst != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Kawasan " . $kawasanFirst['name'] . " sudah ada",
                'token' => csrf_hash()
            ]);
        } else {
            $this->kawasanModel->insert([
                'name' => $name,
                'company_id' => $this->this_company_id
            ]);

            return response()->setJSON([
                'status' => true,
                'message' => "Kawasan warehouse berhasil disimpan",
                'token' => csrf_hash()
            ]);
        }
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $name = strtoupper($this->request->getVar('name'));
        $this->kawasanModel->update($id, [
            'name' => $name
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kawasan warehouse berhasil diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->kawasanModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Kawasan warehouse berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        return response()->setJSON([
            'status' => true,
            'data' => $this->kawasanModel->find($id),
            'token' => csrf_hash()
        ]);
    }
}
