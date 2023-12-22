<?php

namespace App\Controllers\Accounting\JurnalUmum;

use App\Controllers\BaseController;
use App\Models\AccountModuleModel;
use App\Models\Sub_AkunsModel;
use App\Models\JurnalUmumModel;
use App\Models\TransaksiJurnalModel;

class JurnalUmum extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $transaksiJurnalModel;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
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
            $total_debit = 0;
            $total_credit = 0;
            $result = array();

            $id_transaksi_jurnal = $this->transaksiJurnalModel->getIdTransaksiLast();
            foreach ($nm as $key => $val) {
                echo ((float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['kredit'][$key])));
                echo ((float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['debit'][$key])));
                if ($_POST['debit'][$key] == "" || $_POST['debit'][$key] == 0) {
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' => $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getPost('tgl_transaksi')))),
                        'debit' => "0",
                        'kredit' => (float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['kredit'][$key])),
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id
                    );
                    $total_credit += (float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['kredit'][$key]));
                } else if ($_POST['kredit'][$key] == "" || $_POST['kredit'][$key] == 0) {
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' =>  $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getPost('tgl_transaksi')))),
                        'debit' => (float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['debit'][$key])),
                        'kredit' => "0",
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id
                    );
                    $total_debit += (float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['debit'][$key]));
                }
            }

            $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($this->request->getPost('type_transaksi'));
            $dataTransaksiJurnal = [
                'no_transaksi' => $no_transaksi_jurnal,
                'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getPost('tgl_transaksi')))),
                'total_debit' => $total_debit,
                'total_kredit' => $total_credit,
                'metode_input' => 'manual',
                'type_transaksi' => $this->request->getPost('type_transaksi'),
            ];
            exit;
            // var_dump($result);
            $hasil = $this->jurnalUmumModel->insertJurnalBatch($result);
            // var_dump($hasil);
            $this->transaksiJurnalModel->insertTransaksiJurnal($dataTransaksiJurnal);

            session()->setFlashdata('success_message', 'Data Berhasil disimpan');
        } catch (\Exception $e) {
            session()->setFlashdata('error_message', $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
        return redirect()->to('jurnal');
    }
}
