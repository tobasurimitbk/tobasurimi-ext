<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use App\Models\JurnalUmumModel;

class Neraca extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $MetadataModel;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->KategoriAkunsModel = new KategoriAkunsModel();
        $this->HeaderAkunsModel = new HeaderAkunsModel();
        $this->MetadataModel = new MetadataModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->encrypter = \Config\Services::encrypter();
    }
    public function index()
    {
        if ($this->request->getPost('cariTanggal') != "" && $this->request->getPost('dateStart') != "") {
            $tanggalPilihan = $this->request->getPost('dateStart');
            $condition = [
                'tanggal_jurnal >=' => date('Y-m-01', strtotime($tanggalPilihan)),
                'tanggal_jurnal <=' => date('Y-m-t', strtotime($tanggalPilihan))
            ];
        } else {
            $condition = [
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-t')
            ];
        }

        $dataMetadata = $this->MetadataModel
            ->asObject()
            ->where('name', 'Kelompok Akun')
            ->groupStart()
            ->like('value', 'Aktiva')
            ->orLike('value', 'Kewajiban')
            ->orLike('value', 'Modal')
            ->groupEnd()
            ->findAll();
        $dataKategoriAkun = $this->KategoriAkunsModel->getAPAR("");
        $dataHeaderAkun = $this->HeaderAkunsModel->getAPAR("");
        $dataSubAkun = $this->Sub_AkunsModel->getAPAR("");
        $dataJurnalUmum = $this->jurnalUmumModel->getDataJurnal($condition);
        // var_dump($dataJurnalUmum);

        $data = [
            "dataMetadata" => $dataMetadata,
            "dataKategoriAkun" => $dataKategoriAkun,
            "dataHeaderAkun" => $dataHeaderAkun,
            "dataSubAkuns" => $dataSubAkun,
            "dataJurnalUmum" => $dataJurnalUmum,
        ];
        return view('Laporan/LaporanNeraca/index', $data);
    }
}
