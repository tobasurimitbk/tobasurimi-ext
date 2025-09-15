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
        $id = decrypt($id);

        $res = $divisiModel->select('divisis.*')->where('divisis.id', $id)->first();
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
                "nama_bagian"           => strtoupper($data['nama_bagian']),
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

        $kodeBagian =  $this->request->getVar('kodeBagian');
        $divisionId = $this->request->getVar('divisionID');
        $namaBagian = $this->request->getVar('namaBagian');

        $check = $bagianModel
            ->where('kode_bagian', $kodeBagian)
            ->where('division_id', $divisionId)
            ->where('deletedAt', null)
            ->first();

        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Kode bagian " . $kodeBagian . " sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $bagianModel->insert([
            'company_id' => $this->this_company_id,
            'division_id' => $divisionId,
            'kode_bagian' => $kodeBagian,
            'nama_bagian' => $namaBagian,
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

        $kodeBagian =  $this->request->getVar('kodeBagian');
        $namaBagian = $this->request->getVar('namaBagian');

        $check = $bagianModel
            ->where('kode_bagian', $kodeBagian)
            ->where('id !=', $id)
            ->where('deletedAt', null)
            ->first();

        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Kode bagian " . $kodeBagian . " sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $bagianModel->update($id, [
            'kode_bagian' => $kodeBagian,
            'nama_bagian' => $namaBagian,
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
        $divisiModel = new DivisisModel();

        $divisi = $divisiModel->where('id', $this->request->getVar('divisionID'))->first();

        $codeName = substr(\strtoupper($divisi['divisi']), 0, 3);

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

    public function getBagianByDivision()
    {
        $bagianModel = new BagianModel();
        $res = $bagianModel->where('division_id', decrypt($this->request->getVar('divisionID')))->where('deletedAt', null)->findAll();
        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $res,
        ]);
    }

    public function getAllBagian()
    {
        $bagianModel = new BagianModel();
        $res = $bagianModel->where('deletedAt', null)->findAll();
        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $res,
        ]);
    }
}
