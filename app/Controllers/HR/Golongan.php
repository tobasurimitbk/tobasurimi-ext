<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\GolonganModel;

class Golongan extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        return view('hr/golongan/index');
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
            "company_id"    => $this->this_company_id,
            "deletedAt"     => null
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $golonganModel = new GolonganModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $res = $golonganModel->getList($condition, $addCondition, $limit, $offset);
        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "golonganName"          => $data->golongan_name,
                "nominalPinjaman"       => "Rp " . number_format($data->nominal_pinjaman, 2, ',', '.'),
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

    public function create()
    {
        $golonganModel = new GolonganModel();
        $nominalPinjaman = preg_replace("/[^0-9,]/", "", $this->request->getVar('nominalPinjaman'));
        $nominalPinjaman = str_replace(",", ".", $nominalPinjaman);
        $angkaDesimalNominal = number_format((float) $nominalPinjaman, 3, '.', '');

        $golonganModel->insert([
            'company_id' => $this->this_company_id,
            'golongan_name' => $this->request->getVar('golonganName'),
            'nominal_pinjaman' => $angkaDesimalNominal,
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Golongan pegawai berhasil disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function update()
    {
        $golonganModel = new GolonganModel();
        $nominalPinjaman = preg_replace("/[^0-9,]/", "", $this->request->getVar('nominalPinjaman'));
        $nominalPinjaman = str_replace(",", ".", $nominalPinjaman);
        $angkaDesimalNominal = number_format((float) $nominalPinjaman, 3, '.', '');

        $golonganModel->update($this->request->getVar('id'), [
            'company_id' => $this->this_company_id,
            'golongan_name' => $this->request->getVar('golonganName'),
            'nominal_pinjaman' => $angkaDesimalNominal,
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Golongan pegawai berhasil diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function delete()
    {
        $golonganModel = new GolonganModel();
        $golonganModel->update($this->request->getVar('id'), [
            'deletedAt' => date('Y-m-d H:i:s')
        ]);
        return response()->setJSON([
            'status' => true,
            'message' => "Golongan pegawai berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get($id)
    {
        $golonganModel = new GolonganModel();
        $res = $golonganModel->where("id", $id)->first();
        return response()->setJSON([
            'status' => true,
            'data' => $res
        ]);
    }
}
