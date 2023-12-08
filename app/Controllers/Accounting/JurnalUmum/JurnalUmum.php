<?php

namespace App\Controllers\Accounting\JurnalUmum;

use App\Controllers\BaseController;
use App\Models\AccountModuleModel;
use App\Models\Sub_AkunsModel;
use App\Models\JurnalUmumModel;

class JurnalUmum extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->encrypter = \Config\Services::encrypter();
    }

    public function index()
    {
        $accountModuleModel = new AccountModuleModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $accountModuleData = $accountModuleModel->asObject()->findAll();
        $subAkunsModel = $Sub_AkunsModel->getAPAR("");
        foreach ($subAkunsModel as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }

        $data = [
            "dataAccountModule" => $accountModuleData,
            "subAkuns" => $subAkunsModel
        ];

        return view('Accounting/jurnalUmum/index', $data);
    }

    public function save()
    {
        try {
            $nm = $this->request->getPost('cari');
            $result = array();
            foreach ($nm as $key => $val) {
                if ($_POST['debet'][$key] == "" || $_POST['debet'][$key] == 0) {
                    $result[] = array(
                        'id_transaksi' => 1,
                        'id_coa' => $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => $this->request->getPost('tgl_transaksi'),
                        'debit' => "0",
                        'kredit' => (float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['kredit'][$key])),
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id
                    );
                } else if ($_POST['kredit'][$key] == "" || $_POST['kredit'][$key] == 0) {
                    $result[] = array(
                        'id_transaksi' => 1,
                        'id_coa' =>  $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => $this->request->getPost('tgl_transaksi'),
                        'debit' => (float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['debet'][$key])),
                        'kredit' => "0",
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id
                    );
                }
            }
            $this->jurnalUmumModel->insertJurnalBatch($result);

            session()->setFlashdata('success_message', 'Data Berhasil disimpan');
        } catch (\Exception $e) {
            session()->setFlashdata('error_message', $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
        return redirect()->to('jurnal');
    }
}
