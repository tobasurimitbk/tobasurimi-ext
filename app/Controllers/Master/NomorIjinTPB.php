<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\NomorIjinTPBModel;

class NomorIjinTPB extends BaseController
{
    protected $nomorIjinTPBModel;

    public function __construct()
    {
        $this->nomorIjinTPBModel = new NomorIjinTPBModel();
    }

    public function index()
    {
        return view('Master/nomorTpb/index');
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
            'deletedAt' => null
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "search"        => $this->request->getVar('search')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $noIjinTpbData = $this->nomorIjinTPBModel->getList($condition, $addCondition, $limit, $offset);

        $res = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($noIjinTpbData['data'] as $data) {
            array_push($res, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "no_izin_tpb"       => $data->no_izin_tpb,
                "tanggal_skep_tpb"  => date('d/m/Y', strtotime($data->tanggal_skep_tpb)),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $noIjinTpbData['totalData'],
            "recordsFiltered"   => $noIjinTpbData['totalFilteredData'],
            "data"              => $res,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create()
    {
        $noIjinTpb = $this->nomorIjinTPBModel->where('no_izin_tpb', $this->request->getVar('no_izin_tpb'))->first();

        if ($noIjinTpb != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor izin TPB sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $this->nomorIjinTPBModel->insert([
            'no_izin_tpb' => $this->request->getVar('no_izin_tpb'),
            'tanggal_skep_tpb' => $this->request->getVar('tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('tanggal_skep_tpb')), "Y-m-d") : "",
        ]);

        return response()->setJSON([
            'message' => "Nomor izin TPB berhasil dibuat",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->nomorIjinTPBModel->update($id, [
            'no_izin_tpb' => $this->request->getVar('no_izin_tpb'),
            'tanggal_skep_tpb' => $this->request->getVar('tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('tanggal_skep_tpb')), "Y-m-d") : "",
        ]);

        return response()->setJSON([
            'message' => "Nomor izin TPB berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->nomorIjinTPBModel->delete($id);
        return response()->setJSON([
            'message' => "Nomor izin TPB berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $data = $this->nomorIjinTPBModel->find($id);
        $data['tanggal_skep_tpb'] = date('d/m/Y', strtotime($data['tanggal_skep_tpb']));
        $data['id'] = encrypt($data['id']);
        return response()->setJSON([
            'data' => $data,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }
}
