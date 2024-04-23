<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\JamKerjaDetailModel;
use App\Models\JamKerjaModel;
use App\Models\MetadataModel;

class JamKerja extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }


    public function index()
    {
        return view('Master/jamKerja/index');
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

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $condition = [
            'company_id' => $this->this_company_id
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $jamKerjaModel = new JamKerjaModel();

        $jamKerjaData = $jamKerjaModel->getList($condition, $addCondition, $limit, $offset);
        $dataJamKerja = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($jamKerjaData['data'] as $j) {
            array_push($dataJamKerja, [
                "no" => $no++,
                "id" => $j->id,
                "jenis" => $j->jenis,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $jamKerjaData['totalData'],
            "recordsFiltered"   => $jamKerjaData['totalFilteredData'],
            "data"              => $dataJamKerja,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function createView()
    {
        $modelMetaData = new MetadataModel();

        $data = [
            'jamKerja' => null,
            'hari' => $modelMetaData->where('name', "hari")->findAll()
        ];

        return \view('Master/jamKerja/form', $data);
    }

    public function create()
    {
        $modelJamKerja = new JamKerjaModel();
        $modelJamKerjaDetail = new JamKerjaDetailModel();
        $modelMetaData = new MetadataModel();

        $jenisJamKerja = $this->request->getVar('jenisJamKerja');

        // check duplikat
        if ($modelJamKerja->where('company_id', $this->this_company_id)->where('jenis', $jenisJamKerja)->first() != null) {
            return \response()->setJSON([
                'message' => "Jam Kerja $jenisJamKerja sudah ada, silahkan coba dengan nama lain",
                'status' => false
            ]);
        }

        $jamKerja = $modelJamKerja->insert([
            'jenis' => strtoupper($this->request->getVar('jenisJamKerja')),
            'jam_terlambat' => $this->request->getVar('jamTerlambat'),
            'company_id' =>  $this->this_company_id
        ]);

        foreach ($modelMetaData->where('name', "hari")->findAll() as $h) {
            $modelJamKerjaDetail->insert([
                'jam_kerja_id' => $jamKerja,
                'hari' => $h['value'],
                'jam_masuk' => $this->request->getVar($h['value'] . "_mulaiMasuk"),
                'jam_istirahat_mulai' => $this->request->getVar($h['value'] . "_mulaiIstirahat"),
                'jam_istirahat_selesai' => $this->request->getVar($h['value'] . "_selesaiIstirahat"),
                'jam_pulang' => $this->request->getVar($h['value'] . "_mulaiPulang")
            ]);
        }

        return \response()->setJSON([
            'message' => "Jam kerja berhasil disimpan",
            'status' => true
        ]);
    }

    public function delete()
    {
        $modelJamKerja = new JamKerjaModel();
        $modelJamKerjaDetail = new JamKerjaDetailModel();

        $jamKerjaID = $this->request->getVar('jamKerjaID');

        $modelJamKerja->delete($jamKerjaID);
        $modelJamKerjaDetail->where('jam_kerja_id', $jamKerjaID)->delete();

        return \response()->setJSON([
            'message' => "Jam kerja berhasil dihapus",
            'status' => true
        ]);
    }

    public function getById($id)
    {
        $modelJamKerja = new JamKerjaModel();
        $modelMetaData = new MetadataModel();

        $data = [
            'jamKerja' => $modelJamKerja->where('id', $id)->first(),
            'hari' => $modelMetaData->where('name', "hari")->findAll()
        ];

        return \view('Master/jamKerja/form', $data);
    }

    public function update()
    {
        $modelJamKerja = new JamKerjaModel();
        $modelJamKerjaDetail = new JamKerjaDetailModel();
        $modelMetaData = new MetadataModel();

        $jamKerjaSameName = $modelJamKerja
            ->where('company_id', $this->this_company_id)
            ->where('jenis', strtoupper($this->request->getVar('jenisJamKerja')))
            ->where('id !=', $this->request->getVar('jamKerjaID'))
            ->first();

        if ($jamKerjaSameName) {
            return response()->setJSON([
                'status' => false,
                'message' => "Jam kerja sudah digunakan.",
                'token' => csrf_hash()
            ]);
        }


        $modelJamKerja->set('jenis', strtoupper($this->request->getVar('jenisJamKerja')))
            ->set('company_id', $this->this_company_id)
            ->set('jam_terlambat', $this->request->getVar('jamTerlambat'))
            ->where('id', $this->request->getVar('jamKerjaID'))
            ->update();

        // delete first
        $modelJamKerjaDetail->where('jam_kerja_id', $this->request->getVar('jamKerjaID'))->delete();

        foreach ($modelMetaData->where('name', "hari")->findAll() as $h) {
            $modelJamKerjaDetail->insert([
                'jam_kerja_id' => $this->request->getVar('jamKerjaID'),
                'hari' => $h['value'],
                'jam_masuk' => $this->request->getVar($h['value'] . "_mulaiMasuk"),
                'jam_istirahat_mulai' => $this->request->getVar($h['value'] . "_mulaiIstirahat"),
                'jam_istirahat_selesai' => $this->request->getVar($h['value'] . "_selesaiIstirahat"),
                'jam_pulang' => $this->request->getVar($h['value'] . "_mulaiPulang")
            ]);
        }

        return \response()->setJSON([
            'message' => "Jam kerja berhasil diupdate",
            'status' => true
        ]);
    }
}
