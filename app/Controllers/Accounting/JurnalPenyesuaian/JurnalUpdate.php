<?php

namespace App\Controllers\Accounting\JurnalPenyesuaian;

use App\Controllers\BaseController;
use App\Models\AccountModuleModel;
use App\Models\Sub_AkunsModel;
use App\Models\JurnalUmumModel;
use App\Models\TransaksiJurnalModel;
use App\Models\TransaksiPembelianModel;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\SupplierModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\AccountSupplierModel;
use App\Models\AccountBarangModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\LocalPOPaymentModel;
use App\Models\LocalPOPaymentDetailModel;
use App\Models\ImportPOPaymentModel;
use App\Models\PenerimaanBarangModel;
use Exception;

class JurnalUpdate extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $transaksiJurnalModel;
    protected $transaksiPembelianModel;
    protected $encrypter;
    protected $MetadataModel;
    protected $supplierModel;
    protected $divisionModel;
    protected $aMPurchaseOrderModel;
    protected $aMPurchaseOrderDetailModel;
    protected $rMPurchaseOrderModel;
    protected $rMPurchaseOrderDetailModel;
    protected $rmImportPOModel;
    protected $rmImportPODetailModel;
    protected $accountSupplierModel;
    protected $accountModuleModel;
    protected $accountBarangModel;
    protected $localPOPaymentModel;
    protected $localPOPaymentDetailModel;
    protected $importPOPaymentModel;
    protected $penerimaanBarangModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
        $this->transaksiPembelianModel = new TransaksiPembelianModel();
        $this->MetadataModel = new MetadataModel();
        $this->encrypter = \Config\Services::encrypter();
        $this->supplierModel = new SupplierModel();
        $this->divisionModel = new DivisisModel();
        $this->aMPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->aMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->rMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();
        $this->accountSupplierModel = new AccountSupplierModel();
        $this->accountModuleModel = new AccountModuleModel();
        $this->accountBarangModel = new AccountBarangModel();
        $this->localPOPaymentModel = new LocalPOPaymentModel();
        $this->localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $this->importPOPaymentModel = new ImportPOPaymentModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
    }

    public function index($id)
    {
        $ids = $this->encrypter->decrypt(hex2bin($id));

        $accountModuleData = $this->accountModuleModel->asObject()->findAll();

        $subAkunsModel = $this->Sub_AkunsModel->getAPAR($this->this_company_id);
        foreach ($subAkunsModel as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }

        $dataMetadataTipeTransaksi = $this->MetadataModel
            ->asObject()
            ->where('name', 'tipe_transaksi')
            ->findAll();
        foreach ($dataMetadataTipeTransaksi as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }

        $transaksiJurnal = $this->transaksiJurnalModel
            ->asObject()
            ->where('deleted_at', null)
            ->where('id', $ids)
            ->first();
        $transaksiJurnal->hexid = $id;

        $selectQry = "jurnal_umum.*, sub_akuns.no_sub, sub_akuns.nama_sub, sub_akuns.header_id";

        $jurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select($selectQry)
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->where('id_transaksi', $ids)
            ->findAll();
        foreach ($jurnalUmum as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
            $val->hexid_coa = bin2hex($this->encrypter->encrypt($val->id_coa));
        }

        $data = [
            "dataAccountModule" => $accountModuleData,
            "dataMetadataTipeTransaksi" => $dataMetadataTipeTransaksi,
            "subAkuns" => $subAkunsModel,
            "transaksiJurnal" => $transaksiJurnal,
            "jurnalUmum" => $jurnalUmum,
        ];

        return view('Accounting/jurnalPenyesuaian/updateJurnal', $data);
    }

    public function save()
    {
        // try {
        $jurnalOld = $this->request->getPost('cari_old');
        $jurnalNew = $this->request->getPost('cari');
        $idTransaksi = $this->request->getPost('id_transaksi') ? $this->encrypter->decrypt(hex2bin($this->request->getPost('id_transaksi'))) : null;
        $total_debit = 0;
        $total_credit = 0;
        $result = array();

        $id_transaksi_jurnal = $idTransaksi ? $idTransaksi : $this->transaksiJurnalModel->getIdTransaksiLast();
        if ($jurnalOld) {
            foreach ($jurnalOld as $key => $val) {
                if (isset($_POST['debit_old'][$key])) {
                    $debitValue = ($_POST['debit_old'][$key] != "") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['debit_old'][$key])) : 0;
                } else {
                    $debitValue = 0;
                }

                if (isset($_POST['kredit_old'][$key])) {
                    $kreditValue = ($_POST['kredit_old'][$key] != "") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['kredit_old'][$key])) : 0;
                } else {
                    $kreditValue = 0;
                }

                if ($debitValue == 0) {
                    $this->jurnalUmumModel->update($this->encrypter->decrypt(hex2bin($_POST['id_jurnal_old'][$key])), [
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' => $this->encrypter->decrypt(hex2bin($_POST['cari_old'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tgl_transaksi_old'][$key]))),
                        'debit' => $debitValue,
                        'kredit' => $kreditValue,
                        'keterangan' => $_POST['ket_old'][$key],
                        'id_inputer' => session()->get("login")->user_id,
                        'id_company_inputer' => session()->get("login")->this_company,
                    ]);
                    $total_credit += $kreditValue;
                } elseif ($kreditValue == 0) {
                    $this->jurnalUmumModel->update($this->encrypter->decrypt(hex2bin($_POST['id_jurnal_old'][$key])), [
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' =>  $this->encrypter->decrypt(hex2bin($_POST['cari_old'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tgl_transaksi_old'][$key]))),
                        'debit' => $debitValue,
                        'kredit' => $kreditValue,
                        'keterangan' => $_POST['ket_old'][$key],
                        'id_inputer' => session()->get("login")->user_id,
                        'id_company_inputer' => session()->get("login")->this_company,
                    ]);
                    $total_debit += $debitValue;
                }
            }
        }
        if ($jurnalNew) {
            foreach ($jurnalNew as $key => $val) {
                if (isset($_POST['debit'][$key])) {
                    $debitValue = ($_POST['debit'][$key] != "") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['debit'][$key])) : 0;
                } else {
                    $debitValue = 0;
                }

                if (isset($_POST['kredit'][$key])) {
                    $kreditValue = ($_POST['kredit'][$key] != "") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['kredit'][$key])) : 0;
                } else {
                    $kreditValue = 0;
                }

                if ($debitValue == 0) {
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' => $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tgl_transaksi'][$key]))),
                        'debit' => $debitValue,
                        'kredit' => $kreditValue,
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id,
                        'id_company_inputer' => session()->get("login")->this_company,
                    );
                    $total_credit += $kreditValue;
                } elseif ($kreditValue == 0) {
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' =>  $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tgl_transaksi'][$key]))),
                        'debit' => $debitValue,
                        'kredit' => $kreditValue,
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id,
                        'id_company_inputer' => session()->get("login")->this_company,
                    );
                    $total_debit += $debitValue;
                }
            }
            $this->jurnalUmumModel->insertJurnalBatch($result);
        }
        if ($idTransaksi) {
            $this->transaksiJurnalModel->update($idTransaksi, [
                'total_debit' => $total_debit,
                'total_kredit' => $total_credit,
            ]);
        }
        session()->setFlashdata('success_message', 'Data Berhasil disimpan');
        return redirect()->to('laporan-accounting/jurnalumum');
    }

    public function searchSubAkun()
    {
        $query = $this->request->getPost('query');

        $subAkunModel = new Sub_AkunsModel();
        $subAkuns = $subAkunModel->searchSubAkun($query);

        $output = array(); // Menggunakan array untuk menyimpan data
        foreach ($subAkuns as $sub_akun) {
            $sub_akun['hexid'] = bin2hex($this->encrypter->encrypt($sub_akun['id'])); // Menyimpan nilai yang dienkripsi dengan kunci 'hexid'
            $output[] = $sub_akun; // Menambahkan $sub_akun ke dalam array $output
        }

        // Mengembalikan output dalam format JSON
        echo json_encode($output);
        return;
    }
}
