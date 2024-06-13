<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\PenerimaanMutasiGlobalDetailModel;
use App\Models\PenerimaanMutasiGlobalModel;

class PenerimaanMutasiGlobal extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $companyModel;
    protected $divisiModel;
    protected $penerimaanMutasiGlobalModel;
    protected $penerimaanMutasiGlobalDetailModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->companyModel = new CompaniesModel();
        $this->divisiModel = new DivisisModel();
        $this->penerimaanMutasiGlobalModel = new PenerimaanMutasiGlobalModel();
        $this->penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();
    }

    public function index()
    {
        return view('Warehouse/PenerimaanMutasi/index_global');
    }

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Warehouse/PenerimaanMutasi/form_global', $data);
    }

    public function dropdownListNomorMutasi()
    {
        $companyPengirimId = $this->request->getVar('company_pengirim_id');

        if (empty($companyPengirimId)) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => [],
                'status' => true
            ]);
        }

        $data = $this->penerimaanMutasiGlobalModel->getListNomorMutasi(
            $companyPengirimId
        );

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $data,
            'status' => true
        ]);
    }


    public function dropdownListBarang()
    {
        $mutasiID = json_decode($this->request->getVar('mutasi_id'));
        $penerimaanMutasiID = decrypt($this->request->getVar('penerimaan_mutasi_id'));

        if (empty($mutasiID)) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => [],
                'status' => true
            ]);
        } else {

            $data = $this->penerimaanMutasiGlobalModel->getListBarangMutasi(
                $mutasiID,
                $penerimaanMutasiID
            );

            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => $data,
                'status' => true
            ]);
        }
    }

    public function getPenerimaanMutasiNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisiID = $this->request->getVar('divisi_penerima_id');

        if (empty($divisiID)) {
            $no = $this->penerimaanMutasiGlobalModel->get_no(date('m'), date('Y'), $last_day, "", $divisiID);
        } else {
            $divisi = $this->divisiModel->where('id', $divisiID)->first();
            $no = $this->penerimaanMutasiGlobalModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisiID);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
