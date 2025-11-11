<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JamKerjaDetailModel;
use App\Models\JamKerjaModel;
use App\Models\MetadataModel;
use Exception;

class JamKerja extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $divisiModel;
    protected $jamKerjaModel;
    protected $metadataModel;
    protected $jamKerjaDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->jamKerjaModel = new JamKerjaModel();
        $this->metadataModel = new MetadataModel();
        $this->jamKerjaDetailModel = new JamKerjaDetailModel();
    }

    public function indexDivisi()
    {
        return view('Master/jamKerja/index_divisi');
    }

    public function allDivisi()
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

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $condition = [
            'divisis.deletedAt' => null,
            'divisis.company_id' => $this->this_company_id
        ];

        $divisiData = $this->divisiModel->getDivisiJamKerja(
            $condition,
            $addCondition,
            $limit,
            $offset
        );
        $dataDivisi = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($divisiData['data'] as $j) {

            array_push($dataDivisi, [
                "no" => $no++,
                "id" => encrypt($j->id),
                "divisi" => $j->divisi,
                "jenis_shift" => $j->jenis_shift,
                "total_jam_kerja" =>  $j->total_jam_kerja
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $divisiData['totalData'],
            "recordsFiltered"   => $divisiData['totalFilteredData'],
            "data"              => $dataDivisi,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function index($divisiId)
    {
        $divisiId = decrypt($divisiId);
        $dataDivisi = $this->divisiModel->where('id', $divisiId)->findAll();
        if (count($dataDivisi) == 0) {
            return redirect()->back();
        }

        $data = [
            'divisis' => $dataDivisi
        ];

        return view('Master/jamKerja/index', $data);
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
            "divisi_id"     => $this->request->getGet('divisi_id'),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $condition = [
            'jam_kerja.company_id' => $this->this_company_id,
            'jam_kerja.divisi_id' => $addCondition['divisi_id']
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $jamKerjaData = $this->jamKerjaModel->getList($condition, $addCondition, $limit, $offset);
        $dataJamKerja = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($jamKerjaData['data'] as $j) {
            array_push($dataJamKerja, [
                "no" => $no++,
                "id" => encrypt($j->id),
                "jenis" => $j->jenis,
                "shift" => $j->shift,
                "divisi" => $j->divisi,
                "jam_terlambat" => $j->jam_terlambat,
                "lintas_hari" => $j->lintas_hari
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $jamKerjaData['totalData'],
            "recordsFiltered"   => $jamKerjaData['totalFilteredData'],
            "data"              => $dataJamKerja,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createView($divisiId)
    {
        $divisiId = decrypt($divisiId);

        $shift = array("PAGI", "NORMAL", "SIANG");
        foreach (range(1, 50) as $s) {
            array_push($shift, "SHIFT " . $s);
        }
        $divisi = $this->divisiModel->where('id', $divisiId)->findAll();
        $data = [
            'jamKerja' => null,
            'hari' => $this->metadataModel->where('name', "hari")->findAll(),
            'divisi' => $divisi,
            'shift' => $shift
        ];

        return view('Master/jamKerja/form', $data);
    }

    public function create()
    {
        $divisiId = $this->request->getVar('divisiId');
        $shift = $this->request->getVar('shift');
        $jamTerlambat = $this->request->getVar('jamTerlambat');
        $jenis = $this->request->getVar('jenis');
        $jamKerjaLintasHari = $this->request->getVar('jam_kerja_lintas_hari');
        $jamKerjaLintasHari = empty($jamKerjaLintasHari) ? "no" : "yes";

        $jamKerja = $this->jamKerjaModel->insert([
            'company_id' =>  $this->this_company_id,
            'divisi_id' => $divisiId,
            'shift' => $shift,
            'jenis' => $jenis,
            'jam_terlambat' => $jamTerlambat,
            'lintas_hari' => $jamKerjaLintasHari
        ]);

        foreach ($this->metadataModel->where('name', "hari")->findAll() as $h) {
            $this->jamKerjaDetailModel->insert([
                'jam_kerja_id' => $jamKerja,
                'hari' => $h['value'],
                'jam_masuk' => $this->request->getVar($h['value'] . "_mulaiMasuk"),
                'jam_istirahat_mulai' => $this->request->getVar($h['value'] . "_mulaiIstirahat"),
                'jam_istirahat_selesai' => $this->request->getVar($h['value'] . "_selesaiIstirahat"),
                'jam_pulang' => $this->request->getVar($h['value'] . "_mulaiPulang"),
            ]);
        }

        return response()->setJSON([
            'message' => "Jam kerja berhasil disimpan",
            'status' => true,
            'token' => csrf_hash(),
        ]);
    }

    public function delete()
    {
        $jamKerjaID = decrypt($this->request->getVar('jamKerjaID'));

        $this->jamKerjaModel->delete($jamKerjaID);
        $this->jamKerjaDetailModel->where('jam_kerja_id', $jamKerjaID)->delete();

        return response()->setJSON([
            'message' => "Jam kerja berhasil dihapus",
            'status' => true,
            'token' => csrf_hash(),
        ]);
    }

    public function getById($id)
    {
        $id = decrypt($id);
        $shift = array("PAGI", "NORMAL", "SIANG");
        foreach (range(1, 50) as $s) {
            array_push($shift, "SHIFT " . $s);
        }

        $dataJamKerja = $this->jamKerjaModel->select('jam_kerja.*')->where('jam_kerja.id', $id)->first();

        $data = [
            'jamKerja' => $dataJamKerja,
            'hari' => $this->metadataModel->where('name', "hari")->findAll(),
            'divisi' => $this->divisiModel->asArray()->where('id', $dataJamKerja['divisi_id'])->where('deletedAt', null)->orderBy('divisi', "asc")->findAll(),
            'shift' => $shift
        ];

        if ($data['jamKerja'] == null) {
            return redirect()->to('jam-kerja');
        }

        return view('Master/jamKerja/form', $data);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('jamKerjaID'));
        $divisiId = $this->request->getVar('divisiId');
        $shift = $this->request->getVar('shift');
        $jamTerlambat = $this->request->getVar('jamTerlambat');
        $jenis = $this->request->getVar('jenis');
        $jamKerjaLintasHari = $this->request->getVar('jam_kerja_lintas_hari');
        $jamKerjaLintasHari = empty($jamKerjaLintasHari) ? "no" : "yes";

        $this->jamKerjaModel->update($id, [
            'divisi_id' => $divisiId,
            'shift' => $shift,
            'jenis' => $jenis,
            'jam_terlambat' => $jamTerlambat,
            'lintas_hari' => $jamKerjaLintasHari
        ]);

        // delete first
        $this->jamKerjaDetailModel->where('jam_kerja_id', $id)->delete();

        foreach ($this->metadataModel->where('name', "hari")->findAll() as $h) {
            $this->jamKerjaDetailModel->insert([
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
            'status' => true,
            'token' => csrf_hash(),
        ]);
    }

    public function updateJamKerjaDefault()
    {
        $jamKerjaId = $this->request->getVar('jam_kerja_id');
        $divisiId = decrypt($this->request->getVar('divisi_id'));

        $this->divisiModel->update($divisiId, [
            'jam_kerja_id' => $jamKerjaId
        ]);

        return response()->setJSON([
            'message' => "Jam kerja default berhasil di setting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownJamKerja()
    {
        try {
            $divisiId = decrypt($this->request->getVar('divisi_id'));

            $divisi = $this->divisiModel->where('id', $divisiId)->first();
            $jamKerja = $this->jamKerjaModel->where('divisi_id', $divisiId)->where('deletedAt', null)->orderBy('jenis', "asc")->findAll();
            $jamKerjaList = array();
            foreach ($jamKerja as $j) {
                array_push($jamKerjaList, [
                    'id' => $j['id'],
                    'jenis_shift' => $j['jenis'] . " " . $j['shift']
                ]);
            }

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => [
                    'jamKerjaList' => $jamKerjaList,
                    'id_selected' => $divisi['jam_kerja_id'],
                    'divisi' => $divisi['divisi']
                ]
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash(),
            ]);
        }
    }
}
