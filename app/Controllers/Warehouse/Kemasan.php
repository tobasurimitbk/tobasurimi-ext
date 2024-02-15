<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\KemasanModel;
use App\Models\ParentBarangModel;
use App\Models\SatuansModel;
use Exception;

class Kemasan extends BaseController
{
    protected $this_company_id;
    protected $kemasanModel;
    protected $parentBarangModel;
    protected $satuanModel;

    public function __construct()
    {
        $this->satuanModel = new SatuansModel();
        $this->parentBarangModel = new ParentBarangModel();
        $this->kemasanModel = new KemasanModel();
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        $data = [
            'kelompokBarang' => $this->parentBarangModel
                ->where('parent_type', "kemasan")
                ->where('deletedAt', null)
                ->where('company_id', $this->this_company_id)
                ->orderBy('parent_name', 'asc')
                ->findAll(),
            'satuan' => $this->satuanModel->where('deletedAt', null)
                ->findAll()
        ];

        return view('Warehouse/kemasan/index', $data);
    }

    public function create()
    {
        $first = $this->kemasanModel
            ->where('company_id', $this->this_company_id)
            ->where('name', strtoupper($this->request->getVar('name')))
            ->where('satuan_id', $this->request->getVar('satuan_id'))
            ->where('parent_type_id', $this->request->getVar('parent_type_id'))
            ->where('deletedAt', null)
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kemasan " . strtoupper($this->request->getVar('name')) . " sudah ada"
            ]);
        }

        $kodeKemasan = $this->kemasanModel
            ->where('company_id', $this->this_company_id)
            ->where('kode', $this->request->getVar('kode'))
            ->first();

        if ($kodeKemasan != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kode kemasan sudah ada",
            ]);
        }

        $this->kemasanModel->insert([
            'company_id' => $this->this_company_id,
            'kode' => $this->request->getVar('kode'),
            'name' => strtoupper($this->request->getVar('name')),
            'satuan_id' => $this->request->getVar('satuan_id'),
            'parent_type_id' => $this->request->getVar('parent_type_id')
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil ditambah"
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->kemasanModel->update($id, [
            'company_id' => $this->this_company_id,
            'kode' => $this->request->getVar('kode'),
            'name' => strtoupper($this->request->getVar('name')),
            'satuan_id' => $this->request->getVar('satuan_id'),
            'parent_type_id' => $this->request->getVar('parent_type_id')
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil diupdate"
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->kemasanModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil dihapus"
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $res = $this->kemasanModel->find($id);
        if ($res != null) {
            $res['id'] = encrypt($res['id']);
        }
        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'data' => $res
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
            "kemasan.company_id"  => $this->this_company_id,
            "kemasan.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "parent_type_id" => $this->request->getGet('parent_type_id'),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->kemasanModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "kode"                  => $data['kode'],
                "name"                  => $data['name'],
                "kode_satuan"                => $data['kode_satuan'],
                "parent_name"           => $data['parent_name']
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

    public function generateNewKode()
    {
        $codeName = "KS";

        $last = $this->kemasanModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->like('kode', $codeName . '-____')
            ->orderBy('kode', 'DESC')
            ->first();


        if (empty($last)) {
            return response()->setJSON([
                'codeNew' => "$codeName-0001",
                'token' => csrf_hash(),
            ]);
        }
        try {

            $lastCode = $last['kode'];
            $lastCodeExp = explode('-', $lastCode);
            $lastIncrement = (int)$lastCodeExp[1];
            $newIncrement = str_pad(($lastIncrement + 1), 4, '0', STR_PAD_LEFT);

            return response()->setJSON([
                'codeNew' => $codeName . "-" . $newIncrement,
                'token' => csrf_hash(),

            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $codeName . "-????",
                'token' => csrf_hash()
            ]);
        }
    }
}
