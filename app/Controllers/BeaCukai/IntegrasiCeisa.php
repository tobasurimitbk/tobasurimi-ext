<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\CeisaSettingModel;
use App\Models\KantorBeaCukaiModel;

class IntegrasiCeisa extends BaseController
{
    protected $this_company_id;
    protected $ceisaSettingModel;
    protected $kantorBeaCukaiModel;
    protected $beaCukaiApi;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->kantorBeaCukaiModel = new KantorBeaCukaiModel();
    }

    public function index()
    {
        $data = [
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first()
        ];

        return view('BeaCukai/settingAkun/akun/index', $data);
    }

    public function createOrUpdate()
    {
        $ceisaFirst = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $loginCeisa = $this->loginCeisa(
            $this->request->getVar('username'),
            $this->request->getVar('password')
        );

        if ($ceisaFirst) {
            // UPDATE
            $this->ceisaSettingModel->update($ceisaFirst['id'], [
                'company_id' => $this->this_company_id,
                'kode_kantor_pabean' => $this->request->getVar('kode_kantor_pabean'),
                'username' => $this->request->getVar('username'),
                'password' => $this->request->getVar('password'),
                'npwp_perusahaan' => $this->request->getVar('npwp_perusahaan'),
                'status_integrasi' => $loginCeisa
            ]);
        } else {
            // INSERT
            $this->ceisaSettingModel->insert([
                'company_id' => $this->this_company_id,
                'kode_kantor_pabean' => $this->request->getVar('kode_kantor_pabean'),
                'username' => $this->request->getVar('username'),
                'password' => $this->request->getVar('password'),
                'npwp_perusahaan' => $this->request->getVar('npwp_perusahaan'),
                'status_integrasi' => $loginCeisa
            ]);
        }

        $statusValidasiMessage = $loginCeisa == '1' ? "Akun Bea Cukai Berhasil Terhubung Dengan Ceisa" : "Akun Bea Cukai Gagal Terhubung Dengan Ceisa";

        return response()->setJSON([
            'message' => $statusValidasiMessage,
            'token' => csrf_hash(),
            'status' => $loginCeisa == '1' ? true : false
        ]);
    }

    private function loginCeisa($username, $password)
    {
        $beaCukaiApi = new BeaCukaiApi($username, $password);
        $result = $beaCukaiApi->getTokenApi();
        if ($result['status'] == 200) {
            return '1';
        } else {
            return '0';
        }
    }
}
