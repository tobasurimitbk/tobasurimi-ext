<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
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
            'jam_kerja.company_id' => $this->this_company_id
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
                "id" => encrypt($j->id),
                "jenis" => $j->jenis . " - " . $j->shift,
                "divisi" => $j->divisi
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
        $divisiModel = new DivisisModel();
        $shift = array("PAGI", "NORMAL");
        foreach (range(1, 50) as $s) {
            array_push($shift, "SHIFT " . $s);
        }

        $data = [
            'jamKerja' => null,
            'hari' => $modelMetaData->where('name', "hari")->findAll(),
            'divisi' => $divisiModel->asArray()->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('divisi', "asc")->findAll(),
            'shift' => $shift
        ];

        return view('Master/jamKerja/form', $data);
    }

    public function create()
    {
        $modelJamKerja = new JamKerjaModel();
        $modelJamKerjaDetail = new JamKerjaDetailModel();
        $modelMetaData = new MetadataModel();
        $modelDivisis = new DivisisModel();

        $divisiId = $this->request->getVar('divisiId');
        $shift = $this->request->getVar('shift');
        $jamTerlambat = $this->request->getVar('jamTerlambat');
        $jenis = $this->request->getVar('jenis');
        $jamKerjaDefault = $this->request->getVar('jamKerjaDefault');

        $jamKerja = $modelJamKerja->insert([
            'company_id' =>  $this->this_company_id,
            'divisi_id' => $divisiId,
            'shift' => $shift,
            'jenis' => $jenis,
            'jam_terlambat' => $jamTerlambat,
        ]);

        if ($jamKerjaDefault == 1) {
            $modelDivisis->update($divisiId, ['jam_kerja_id' => null]);
            $modelDivisis->update($divisiId, ['jam_kerja_id' => $jamKerja]);
        }

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

        return response()->setJSON([
            'message' => "Jam kerja berhasil disimpan",
            'status' => true
        ]);
    }

    public function delete()
    {
        $modelJamKerja = new JamKerjaModel();
        $modelJamKerjaDetail = new JamKerjaDetailModel();

        $jamKerjaID = decrypt($this->request->getVar('jamKerjaID'));

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
        $divisiModel = new DivisisModel();

        $id = decrypt($id);
        $shift = array("PAGI", "NORMAL");
        foreach (range(1, 50) as $s) {
            array_push($shift, "SHIFT " . $s);
        }

        $data = [
            'jamKerja' => $modelJamKerja->select('jam_kerja.*')
                ->where('jam_kerja.id', $id)
                ->first(),
            'jamKerjaDefault' => $divisiModel->where('jam_kerja_id', $id)->first(),
            'hari' => $modelMetaData->where('name', "hari")->findAll(),
            'divisi' => $divisiModel->asArray()->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('divisi', "asc")->findAll(),
            'shift' => $shift
        ];

        // dd($data['jamKerja'], $id);

        if ($data['jamKerja'] == null) {
            return redirect()->to('jam-kerja');
        }

        return view('Master/jamKerja/form', $data);
    }

    public function update()
    {
        $modelJamKerja = new JamKerjaModel();
        $modelJamKerjaDetail = new JamKerjaDetailModel();
        $modelMetaData = new MetadataModel();
        $modelDivisis = new DivisisModel();

        $id = decrypt($this->request->getVar('jamKerjaID'));
        $divisiId = $this->request->getVar('divisiId');
        $shift = $this->request->getVar('shift');
        $jamTerlambat = $this->request->getVar('jamTerlambat');
        $jenis = $this->request->getVar('jenis');
        $jamKerjaDefault = $this->request->getVar('jamKerjaDefault');

        $modelJamKerja->update($id, [
            'divisi_id' => $divisiId,
            'shift' => $shift,
            'jenis' => $jenis,
            'jam_terlambat' => $jamTerlambat,
        ]);

        if ($jamKerjaDefault == 1) {
            $modelDivisis->update($divisiId, ['jam_kerja_id' => null]);
            $modelDivisis->update($divisiId, ['jam_kerja_id' => $id]);
        }

        // delete first
        $modelJamKerjaDetail->where('jam_kerja_id', $id)->delete();

        foreach ($modelMetaData->where('name', "hari")->findAll() as $h) {
            $modelJamKerjaDetail->insert([
                'jam_kerja_id' => $id,
                'hari' => $h['value'],
                'jam_masuk' => $this->request->getVar($h['value'] . "_mulaiMasuk"),
                'jam_istirahat_mulai' => $this->request->getVar($h['value'] . "_mulaiIstirahat"),
                'jam_istirahat_selesai' => $this->request->getVar($h['value'] . "_selesaiIstirahat"),
                'jam_pulang' => $this->request->getVar($h['value'] . "_mulaiPulang")
            ]);
        }

        return response()->setJSON([
            'message' => "Jam kerja berhasil diupdate",
            'status' => true
        ]);
    }
}
