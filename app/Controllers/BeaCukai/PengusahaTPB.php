<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\NomorIjinTPBModel;
use App\Models\PengusahaTPBModel;

class PengusahaTPB extends BaseController
{
    protected $this_company_id;
    protected $pengusahaTpbModel;
    protected $noIjinTpbModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->pengusahaTpbModel = new PengusahaTPBModel();
        $this->noIjinTpbModel = new NomorIjinTPBModel();
    }

    public function index()
    {
        return view('BeaCukai/settingAkun/pengusahaTpb/index');
    }

    public function create()
    {
        $rules = [
            "npwp" => [
                'rules' => 'numeric|min_length[12]',
                'errors' => [
                    'min_length' => 'Nomor NPWP harus diisi minimal 12 digit'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJSON($data);
        }


        if ($this->validate($rules)) {
            $first = $this->pengusahaTpbModel->where('company_id', $this->this_company_id)->where('npwp', $this->request->getVar('npwp'))->first();
            if ($first != null) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Nomor NPWP Sudah Digunakan"
                ]);
            }

            $this->pengusahaTpbModel->insert([
                'company_id' => $this->this_company_id,
                'npwp' => $this->request->getVar('npwp'),
                'nama_pengusaha' => strtoupper($this->request->getVar('nama_pengusaha')),
                'alamat' => strtoupper($this->request->getVar('alamat')),
                'nib' => $this->request->getVar('nib'),
            ]);

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Pengusaha TPB Berhasil Ditambahkan"
            ]);
        }
    }

    public function update()
    {
        $rules = [
            "npwp" => [
                'rules' => 'numeric|min_length[12]',
                'errors' => [
                    'min_length' => 'Nomor NPWP harus diisi minimal 12 digit'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJSON($data);
        }

        $id = decrypt($this->request->getVar('id'));

        if ($this->validate($rules)) {
            $first = $this->pengusahaTpbModel->where('company_id', $this->this_company_id)->where('npwp', $this->request->getVar('npwp'))->where('id !=', $id)->first();
            if ($first != null) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Nomor NPWP Sudah Digunakan"
                ]);
            }

            $this->pengusahaTpbModel->update($id, [
                'company_id' => $this->this_company_id,
                'npwp' => $this->request->getVar('npwp'),
                'nama_pengusaha' => strtoupper($this->request->getVar('nama_pengusaha')),
                'alamat' => strtoupper($this->request->getVar('alamat')),
                'nib' => $this->request->getVar('nib'),
            ]);

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Pengusaha TPB Berhasil Diupdate"
            ]);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->pengusahaTpbModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pengusaha TPB Berhasil Dihapus"
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $first = $this->pengusahaTpbModel->find($id);
        $first['id'] = encrypt($first['id']);

        return response()->setJSON([
            'data' => $first,
            'token' => csrf_hash(),
            'status' => true
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
            'deletedAt' => null,
            'company_id' => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "search"        => $this->request->getVar('search')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $noIjinTpbData = $this->pengusahaTpbModel->getList($condition, $addCondition, $limit, $offset);

        $res = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($noIjinTpbData['data'] as $data) {
            $totalNoIzinTpb = $this->noIjinTpbModel->where('pengusaha_tpb_id', $data->id)->findAll();
            array_push($res, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "npwp"              => $data->npwp,
                "nama_pengusaha"    => $data->nama_pengusaha,
                "total_no_ijin_tpb" => count($totalNoIzinTpb),
                "alamat"            => $data->alamat,
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
}
