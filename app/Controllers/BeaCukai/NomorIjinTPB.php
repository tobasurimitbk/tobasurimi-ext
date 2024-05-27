<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\NomorIjinTPBModel;
use App\Models\PengusahaTPBModel;

class NomorIjinTPB extends BaseController
{
    protected $nomorIjinTPBModel;
    protected $pengusahaTPBModel;
    protected $this_company_id;

    public function __construct()
    {
        $this->nomorIjinTPBModel = new NomorIjinTPBModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index($id)
    {
        $pengusahaTPBId = decrypt($id);
        $data = [
            'pengusahaTPB' => $this->pengusahaTPBModel->find($pengusahaTPBId)
        ];
        if ($data['pengusahaTPB'] == null) {
            return redirect()->to('setting-akun-bc/pengusaha-tpb');
        }
        return view('BeaCukai/settingAkun/settingTpb/index', $data);
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
            'nomor_ijin_tpb.deletedAt' => null,
            'nomor_ijin_tpb.company_id' => $this->this_company_id,
            'pengusaha_tpb_id' => ($this->request->getVar('pengusaha_tpb_id')),
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
                "no_ijin_tpb"       => $data->no_ijin_tpb,
                "tanggal_skep_tpb"  => date('d/m/Y', strtotime($data->tanggal_skep_tpb)),
                "alamat_pemilik_barang" => strtoupper($data->alamat_pemilik_barang),
                "status"            => $data->status == "1" ? "AKTIF" : "TIDAK AKTIF"
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
        $noIjinTpb = $this->nomorIjinTPBModel
            ->where('company_id', $this->this_company_id)
            ->where('pengusaha_tpb_id', $this->request->getVar('pengusaha_tpb_id'))
            ->where(
                'no_ijin_tpb',
                $this->request->getVar('no_ijin_tpb')
            )->first();

        if ($noIjinTpb != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor izin TPB sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $this->nomorIjinTPBModel->insert([
            'company_id' => $this->this_company_id,
            'pengusaha_tpb_id' => $this->request->getVar('pengusaha_tpb_id'),
            'no_ijin_tpb' => $this->request->getVar('no_ijin_tpb'),
            'tanggal_skep_tpb' => $this->request->getVar('tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('tanggal_skep_tpb')), "Y-m-d") : "",
            'alamat_pemilik_barang' => strtoupper($this->request->getVar('alamat_pemilik_barang')),
            'status' => $this->request->getVar('status'),
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

        $noIjinTpb = $this->nomorIjinTPBModel
            ->where('company_id', $this->this_company_id)
            ->where(
                'no_ijin_tpb',
                $this->request->getVar('no_ijin_tpb')
            )
            ->where('pengusaha_tpb_id', $this->request->getVar('pengusaha_tpb_id'))
            ->where('id != ', $id)
            ->first();

        if ($noIjinTpb != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor izin TPB sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $this->nomorIjinTPBModel->update($id, [
            'company_id' => $this->this_company_id,
            'pengusaha_tpb_id' => $this->request->getVar('pengusaha_tpb_id'),
            'no_ijin_tpb' => $this->request->getVar('no_ijin_tpb'),
            'tanggal_skep_tpb' => $this->request->getVar('tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('tanggal_skep_tpb')), "Y-m-d") : "",
            'alamat_pemilik_barang' => strtoupper($this->request->getVar('alamat_pemilik_barang')),
            'status' => $this->request->getVar('status'),
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
        $data['id'] = encrypt($data['id']);
        $data['tanggal_skep_tpb'] = date('d/m/Y', strtotime($data['tanggal_skep_tpb']));
        return response()->setJSON([
            'data' => $data,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }
}
