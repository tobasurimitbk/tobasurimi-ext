<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\BanksModel;

class Bank extends BaseController
{
    protected $this_company_id;
    protected $banksModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->banksModel = new BanksModel();
    }

    public function index()
    {
        return view('Master/bank/index');
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
            "banks.company_id"  => $this->this_company_id,
            "banks.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "company_id"    => $this->this_company_id,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->banksModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "kode_bank"             => $data['kode_bank'],
                "name"                  => $data['name'],
                "atas_nama"             => $data['atas_nama'],
                "no_rekening"           => $data['no_rekening']
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
        $kodeBank = strtoupper($this->request->getVar('kode_bank'));
        $bankFirst = $this->banksModel->where('company_id', $this->this_company_id)->where('kode_bank', $kodeBank)->first();

        if ($bankFirst != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Kode bank " . $bankFirst['kode_bank'] . " sudah ada",
                'token' => csrf_hash()
            ]);
        } else {
            $this->banksModel->insert([
                'company_id' => $this->this_company_id,
                'kode_bank' => $kodeBank,
                'name' => $this->request->getVar('name'),
                'atas_nama' => $this->request->getVar('atas_nama'),
                'no_rekening' => $this->request->getVar('no_rekening')
            ]);

            return response()->setJSON([
                'status' => true,
                'message' => "Bank berhasil disimpan",
                'token' => csrf_hash()
            ]);
        }
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $kodeBank = strtoupper($this->request->getVar('kode_bank'));

        $bankWithSameCode = $this->banksModel
            ->where('company_id', $this->this_company_id)
            ->where('kode_bank', $kodeBank)
            ->where('id !=', $id)
            ->first();

        if ($bankWithSameCode) {
            return response()->setJSON([
                'status' => false,
                'message' => "Kode bank sudah digunakan oleh bank lain.",
                'token' => csrf_hash()
            ]);
        }
        $this->banksModel->update($id, [
            'name' => $this->request->getVar('name'),
            'atas_nama' => $this->request->getVar('atas_nama'),
            'no_rekening' => $this->request->getVar('no_rekening')
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bank berhasil diupdate",
            'token' => csrf_hash()
        ]);
    }


    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->banksModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Bank berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        return response()->setJSON([
            'status' => true,
            'data' => $this->banksModel->find($id),
            'token' => csrf_hash()
        ]);
    }
}
