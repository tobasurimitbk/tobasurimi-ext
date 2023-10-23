<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\BagianModel;
use App\Models\DivisisModel;
use Exception;

class Bagian extends BaseController
{
    protected $this_company_id;
    private $kode;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->kode = "BAG";
    }

    public function index($id)
    {
        $divisiModel = new DivisisModel();
        $res = $divisiModel->select('divisis.*,jam_kerja.jenis')->join('jam_kerja', 'jam_kerja.id = divisis.jam_kerja_id')->where('divisis.id', $id)->first();
        if ($res == null) {
            return redirect()->to('divisi');
        }
        return view('Master/divisi/bagian', [
            'divisi' => $res
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
            "bagian.company_id"  => $this->this_company_id,
            "bagian.division_id" => $this->request->getGet('divisionID'),
            "bagian.deletedAt" => NULL
        ];

        $addCondition = [
            'search' => $this->request->getGet('search'),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $bagianModel = new BagianModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $bagianModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data['id'],
                "kode_bagian"           => $data['kode_bagian'],
                "nama_bagian"           => $data['nama_bagian'],
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
        $bagianModel = new BagianModel();

        $check = $bagianModel->where('nama_bagian', $this->request->getVar('namaBagian'))->first();
        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nama bagian " . $check['nama_bagian'] . " sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $bagianModel->insert([
            'company_id' => $this->this_company_id,
            'division_id' => $this->request->getVar('divisionID'),
            'kode_bagian' => $this->request->getVar('kodeBagian'),
            'nama_bagian' => $this->request->getVar('namaBagian'),
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bagian berhasil ditambahkan",
            'token' => csrf_hash()
        ]);
    }

    public function get($id)
    {
        $bagianModel = new BagianModel();
        $res = $bagianModel->where('id', $id)->first();
        return response()->setJSON([
            'status' => true,
            'data' => $res,
            'token' => csrf_hash()
        ]);
    }

    public function update($id)
    {
        $bagianModel = new BagianModel();

        $bagianModel->update($id, [
            'nama_bagian' => $this->request->getVar('namaBagian'),
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bagian berhasil diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        $bagianModel = new BagianModel();

        $bagianModel->update($id, [
            'deletedAt' => date('Y-m-d H:i:s'),
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bagian berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function generateKode()
    {
        $bagianModel = new BagianModel();

        $codeName = $this->kode;

        $last = $bagianModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('division_id', $this->request->getVar('divisionID'))
            ->where('deletedAt', null)
            ->like('kode_bagian', $codeName . '-____')
            ->orderBy('createdAt', 'DESC')
            ->first();

        if (empty($last)) {
            return response()->setJSON([
                'codeNew' => "$codeName-0001",
                'token' => csrf_hash(),
            ]);
        }

        try {

            $lastCode = $last->kode_bagian;
            $lastCodeExp = explode('-', $lastCode);
            $lastIncrement = (int)$lastCodeExp[1];
            $newIncrement = str_pad(($lastIncrement + 1), 4, '0', STR_PAD_LEFT);

            return response()->setJSON([
                'codeNew' => $codeName . "-" . $newIncrement,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $codeName . "-????",
                'token' => csrf_hash()
            ]);
        }
    }
}
