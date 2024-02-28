<?php

namespace App\Controllers\Accounting\JurnalUmum;

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
use App\Models\KursModel;
use App\Models\PenerimaanBarangModel;
use Exception;

class JurnalUmum extends BaseController
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
    protected $metadataModel;
    protected $kursModel;

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
        $this->metadataModel = new MetadataModel();
        $this->kursModel = new KursModel();
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

        $dataMetadataTipeTransaksi = $this->MetadataModel
            ->asObject()
            ->where('name', 'tipe_transaksi')
            ->findAll();
        foreach ($dataMetadataTipeTransaksi as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }

        $data = [
            "dataAccountModule" => $accountModuleData,
            "dataMetadataTipeTransaksi" => $dataMetadataTipeTransaksi,
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
                        'id_inputer' => session()->get("login")->user_id
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
                        'id_inputer' => session()->get("login")->user_id
                    );
                    $total_debit += $debitValue;
                }
            }
            $kodeTransaksi = "";
            $dataMetadataTipeTransaksi = $this->MetadataModel
                ->asObject()
                ->where('id', $this->encrypter->decrypt(hex2bin($this->request->getPost('type_transaksi'))))
                ->findAll();
            foreach ($dataMetadataTipeTransaksi as $val) {
                $kodeTransaksi = $val->description;
            }

            $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
            $dataTransaksiJurnal = [
                'no_transaksi' => $no_transaksi_jurnal,
                'tanggal_transaksi' => date('Y-m-d'),
                'total_debit' => $total_debit,
                'total_kredit' => $total_credit,
                'metode_input' => 'manual',
                'type_transaksi' => $this->encrypter->decrypt(hex2bin($this->request->getPost('type_transaksi'))),
                'no_bukti' => $this->request->getPost('no_bukti') ? $this->request->getPost('no_bukti') : $no_transaksi_jurnal,
                'valas' => 'IDR',
                'exchange_rate' => 1,
            ];
            $this->jurnalUmumModel->insertJurnalBatch($result);
            $this->transaksiJurnalModel->insertTransaksiJurnal($dataTransaksiJurnal);

            session()->setFlashdata('success_message', 'Data Berhasil disimpan');
        } catch (\Exception $e) {
            session()->setFlashdata('error_message', 'Gagal Coba Cek Kembali Semua Field');
        }
        return redirect()->to('jurnal');
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

    public function generateNoBukti()
    {
        $kodeTransaksi = "";
        $no_transaksi_jurnal = "";
        try {
            $dataMetadataTipeTransaksi = $this->MetadataModel
                ->asObject()
                ->where('id', $this->encrypter->decrypt(hex2bin($this->request->getPost('transaksi'))))
                ->findAll();
            foreach ($dataMetadataTipeTransaksi as $val) {
                $kodeTransaksi = $val->description;
            }

            $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);

            return response()->setJSON([
                'codeNew' => $no_transaksi_jurnal,
                'token' => csrf_hash(),

            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $no_transaksi_jurnal . "-????",
                'token' => csrf_hash()
            ]);
        }
    }

    public function insertDataPembelian($poID, $type, $kategori, $module)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";

        if ($type == "BAHAN BAKU") {
            if ($kategori == "LOKAL") {
                $dataPOBB = $this->rMPurchaseOrderModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
                if ($dataPOBB) {
                    $result = array();
                    $resultTransaksiJurnal = array();
                    $resultTransaksiPembelian = array();
                    foreach ($dataPOBB as $dataBB) {
                        $totalPO = 0;
                        $kodeTransaksi = "";
                        $idTransaksi = "";

                        // $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBB->division_id);
                        $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBB->supplier_id);
                        $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                        $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                        $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();

                        $dataPOBBDetail = $this->rMPurchaseOrderDetailModel->asObject()->where('deletedAt', null)->where('rm_purchase_order_id', $dataBB->id)->findAll();
                        $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembelian')->findAll();

                        foreach ($dataSupplier as $value) {
                            foreach ($dataAccountSupplier as $valueAccount) {
                                if ($value->id == $valueAccount->supplier_id) {
                                    $UtangAP = $valueAccount->ap_id;
                                    $UtangAR = $valueAccount->ar_id;
                                }
                            }
                            foreach ($dataAccountModule as $valueModule) {
                                if ($valueModule->type == $type && $valueModule->kategori == $kategori && $valueModule->module == $module) {
                                    $UtangAP = $valueModule->ap_id;
                                    $UtangAR = $valueModule->ar_id;
                                }
                            }
                        }
                        // end inisialisasi account

                        // start inisialisasi kode transaksi
                        foreach ($dataMetadataTipeTransaksi as $val) {
                            $kodeTransaksi = $val->description;
                            $idTransaksi = $val->id;
                        }
                        // end inisialisasi kode transaksi
                        $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                        $resultTransaksiJurnal = array(
                            'no_transaksi' => $no_transaksi_jurnal,
                            'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'total_debit' => $totalPO,
                            'total_kredit' => $totalPO,
                            'metode_input' => 'system',
                            'type_transaksi' => $idTransaksi,
                        );

                        // ambil id dari transaksi jurnal untuk jurnal umum
                        $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                        // start input jurnal dari banyak detail barang

                        foreach ($dataPOBBDetail as $dataBBDetail) {
                            $totalPOqty = (($dataBBDetail->general_price * $dataBBDetail->qty) + ($dataBBDetail->daily_price * $dataBBDetail->qty) + ($dataBBDetail->monthly_price * $dataBBDetail->qty));
                            $totalPO += $totalPOqty;
                            $barangAPFound = false;
                            foreach ($dataAccountBarang as $value) {
                                if ($dataBB->barang_id == $value->barang_master_id) {
                                    $barangAP = $value->ap_id;
                                    $barangAR = $value->ar_id;
                                    $barangAPFound = true;
                                }
                            }
                            if (!$barangAPFound) {
                                // Collect errors
                                $errors[] = "Barang Tidak Memiliki Akun COA";
                            } else {
                                $result[] = array(
                                    'id_transaksi' => $id_transaksi_jurnal,
                                    'id_coa' =>  $barangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => (repairDouble($totalPOqty)),
                                    'kredit' => 0,
                                    'keterangan' => $dataBB->po_no,
                                    'id_inputer' => session()->get("login")->user_id
                                );
                            }
                            if (!empty($errors)) {
                                return response()->setJSON([
                                    "status" => false,
                                    "message" => implode(', ', $errors),
                                    'token' => csrf_hash()
                                ]);
                            }
                        }
                        // end input jurnal dari banyak detail barang

                        // update total debit dan kredit dari total nilai pada jurnal umum
                        $this->transaksiJurnalModel->update(
                            $id_transaksi_jurnal,
                            [
                                'total_debit' => $totalPO,
                                'total_kredit' => $totalPO,
                            ]
                        );

                        //untuk insert ke jurnal umum
                        $result[] = array(
                            'id_transaksi' => $id_transaksi_jurnal,
                            'id_coa' =>  $UtangAP,
                            'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'debit' => 0,
                            'kredit' => repairDouble($totalPO),
                            'keterangan' => $dataBB->po_no,
                            'id_inputer' => session()->get("login")->user_id
                        );

                        $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                        $resultTransaksiJurnal[] = array(
                            'no_transaksi' => $no_transaksi_jurnal,
                            'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'total_debit' => $totalPO,
                            'total_kredit' => $totalPO,
                            'metode_input' => 'system',
                            'tipe_barang' => $type,
                            'kategori_barang' => $kategori,
                            'po_id' => $poID,
                            'type_transaksi' => $idTransaksi,
                            'no_bukti' => $no_transaksi_jurnal,
                            'valas' => 'IDR',
                            'exchange_rate' => 1,
                        );

                        $resultTransaksiPembelian[] = array(
                            'id_local_bb' => $poID,
                            'id_supplier' => $dataBB->supplier_id,
                            'id_transaksi_jurnal' => $id_transaksi_jurnal,
                            'tgl_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                        );
                    }
                    $this->jurnalUmumModel->insertJurnalBatch($result);
                    $this->transaksiPembelianModel->insertBatchTransaksiPembelian($resultTransaksiPembelian);
                }
            } else {
                $dataPOBB = $this->rmImportPOModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
                if ($dataPOBB) {
                    $result = array();
                    $resultTransaksiJurnal = array();
                    $resultTransaksiPembelian = array();
                    foreach ($dataPOBB as $dataBB) {
                        $totalPO = 0;
                        $kodeTransaksi = "";
                        $idTransaksi = "";

                        $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBB->division_id);
                        $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBB->supplier_id);
                        $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                        $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                        $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                        $kursData = $this->kursModel->getByMetaId($dataBB->currency, $dataBB->po_date);
                        $metaValuta = $this->metadataModel->get_by_name('Valuta');
                        foreach ($metaValuta as $valueValuta) {
                            if ($dataBB->currency == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                if ($kursData) {
                                    $exchangeTransaksi = $kursData->nilai_kurs;
                                } else {
                                    $exchangeTransaksi = 1;
                                }
                            }
                        }

                        $dataPOBBDetail = $this->rmImportPODetailModel->asObject()->where('deletedAt', null)->where('rm_import_po_id', $dataBB->id)->findAll();
                        $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembelian')->findAll();

                        // $id_transaksi_jurnal = $this->transaksiJurnalModel->getIdTransaksiLast();

                        // start inisialisasi account
                        foreach ($dataDepartment as $value) {
                            $KasAP = $value->ap_id;
                            $KasAR = $value->ar_id;
                        }
                        foreach ($dataSupplier as $value) {
                            foreach ($dataAccountSupplier as $valueAccount) {
                                if ($value->id == $valueAccount->supplier_id) {
                                    $UtangAP = $valueAccount->ap_id;
                                    $UtangAR = $valueAccount->ar_id;
                                }
                            }
                            foreach ($dataAccountModule as $valueModule) {
                                if ($valueModule->type == $type && $valueModule->kategori == $kategori && $valueModule->module == $module) {
                                    $UtangAP = $valueModule->ap_id;
                                    $UtangAR = $valueModule->ar_id;
                                }
                            }
                        }
                        // end inisialisasi account
                        // start inisialisasi kode transaksi
                        foreach ($dataMetadataTipeTransaksi as $val) {
                            $kodeTransaksi = $val->description;
                            $idTransaksi = $val->id;
                        }
                        // input ke transaksi jurnal
                        $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                        $resultTransaksiJurnal = array(
                            'no_transaksi' => $no_transaksi_jurnal,
                            'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'total_debit' => $totalPO * $exchangeTransaksi,
                            'total_kredit' => $totalPO * $exchangeTransaksi,
                            'metode_input' => 'system',
                            'tipe_barang' => $type,
                            'kategori_barang' => $kategori,
                            'po_id' => $poID,
                            'type_transaksi' => $idTransaksi,
                            'no_bukti' => $no_transaksi_jurnal,
                            'valas' => $valasTransaksi,
                            'exchange_rate' => $exchangeTransaksi,
                        );

                        // ambil id dari transaksi jurnal untuk jurnal umum
                        $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                        // end inisialisasi kode transaksi
                        // start input jurnal dari banyak detail barang
                        foreach ($dataPOBBDetail as $dataBBDetail) {
                            $totalPO += repairDouble($dataBBDetail->total);
                            $barangAPFound = false;
                            foreach ($dataAccountBarang as $value) {
                                if ($dataBBDetail->barang_id == $value->barang_master_id) {
                                    $barangAP = $value->ap_id;
                                    $barangAR = $value->ar_id;
                                    $barangAPFound = true;
                                }
                            }
                            if (!$barangAPFound) {
                                // Collect errors
                                $errors[] = "Barang Tidak Memiliki Akun COA";
                            } else {
                                $result[] = array(
                                    'id_transaksi' => $id_transaksi_jurnal,
                                    'id_coa' =>  $barangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => repairDouble($dataBBDetail->total) * $exchangeTransaksi,
                                    'kredit' => 0,
                                    'keterangan' => $dataBB->po_no,
                                    'id_inputer' => session()->get("login")->user_id
                                );
                            }
                            if (!empty($errors)) {
                                return response()->setJSON([
                                    "status" => false,
                                    "message" => implode(', ', $errors),
                                    'token' => csrf_hash()
                                ]);
                            }
                        }
                        // end input jurnal dari banyak detail barang
                        // update total debit dan kredit dari total nilai pada jurnal umum
                        $this->transaksiJurnalModel->update(
                            $id_transaksi_jurnal,
                            [
                                'total_debit' => $totalPO,
                                'total_kredit' => $totalPO,
                            ]
                        );
                        //untuk insert ke jurnal umum
                        $result[] = array(
                            'id_transaksi' => $id_transaksi_jurnal,
                            'id_coa' =>  $UtangAP,
                            'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'debit' => 0,
                            'kredit' => $totalPO * $exchangeTransaksi,
                            'keterangan' => $dataBB->po_no,
                            'id_inputer' => session()->get("login")->user_id
                        );

                        $resultTransaksiPembelian[] = array(
                            'id_import_bb' => $poID,
                            'id_supplier' => $dataBB->supplier_id,
                            'id_transaksi_jurnal' => $id_transaksi_jurnal,
                            'tgl_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                        );
                    }
                    $this->jurnalUmumModel->insertJurnalBatch($result);
                    $this->transaksiPembelianModel->insertBatchTransaksiPembelian($resultTransaksiPembelian);
                }
            }
        } else {
            $dataPOBP = $this->aMPurchaseOrderModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
            if ($dataPOBP) {
                $result = array();
                $resultTransaksiJurnal = "";
                $resultTransaksiPembelian = array();
                foreach ($dataPOBP as $dataBP) {
                    $totalPO = 0;
                    $kodeTransaksi = "";
                    $idTransaksi = "";

                    $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBP->division_id);
                    $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBP->supplier_id);
                    $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                    $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                    $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                    $kursData = $this->kursModel->getByMetaId($dataBP->currency, $dataBP->po_date);
                    $metaValuta = $this->metadataModel->get_by_name('Valuta');
                    foreach ($metaValuta as $valueValuta) {
                        if ($dataBP->currency == $valueValuta['id']) {
                            $valasTransaksi = $valueValuta['value'];
                            if ($kursData) {
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            } else {
                                $exchangeTransaksi = 1;
                            }
                        }
                    }

                    $dataPOBPDetail = $this->aMPurchaseOrderDetailModel->asObject()->where('deletedAt', null)->where('am_purchase_order_id', $dataBP->id)->findAll();
                    $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembelian')->findAll();

                    // $id_transaksi_jurnal = $this->transaksiJurnalModel->getIdTransaksiLast();

                    // start inisialisasi account
                    foreach ($dataDepartment as $value) {
                        $KasAP = $value->ap_id;
                        $KasAR = $value->ar_id;
                    }
                    foreach ($dataSupplier as $value) {
                        foreach ($dataAccountSupplier as $valueAccount) {
                            if ($value->id == $valueAccount->supplier_id) {
                                $UtangAP = $valueAccount->ap_id;
                                $UtangAR = $valueAccount->ar_id;
                            }
                        }
                        foreach ($dataAccountModule as $valueModule) {
                            if ($valueModule->type == $type && $valueModule->kategori == $kategori && $valueModule->module == $module) {
                                $UtangAP = $valueModule->ap_id;
                                $UtangAR = $valueModule->ar_id;
                            }
                        }
                    }
                    // end inisialisasi account
                    // start inisialisasi kode transaksi
                    foreach ($dataMetadataTipeTransaksi as $val) {
                        $kodeTransaksi = $val->description;
                        $idTransaksi = $val->id;
                    }
                    // end inisialisasi kode transaksi
                    // input ke transaksi jurnal
                    $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                    $resultTransaksiJurnal = array(
                        'no_transaksi' => $no_transaksi_jurnal,
                        'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                        'total_debit' => $totalPO * $exchangeTransaksi,
                        'total_kredit' => $totalPO * $exchangeTransaksi,
                        'metode_input' => 'system',
                        'tipe_barang' => $type,
                        'kategori_barang' => $kategori,
                        'po_id' => $poID,
                        'type_transaksi' => $idTransaksi,
                        'no_bukti' => $no_transaksi_jurnal,
                        'valas' => $valasTransaksi,
                        'exchange_rate' => $exchangeTransaksi,
                    );

                    // ambil id dari transaksi jurnal untuk jurnal umum
                    $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

                    // start input jurnal dari banyak detail barang
                    foreach ($dataPOBPDetail as $dataBPDetail) {
                        $totalPO += repairDouble($dataBPDetail->total);
                        $barangAPFound = false;
                        foreach ($dataAccountBarang as $value) {
                            if ($dataBPDetail->barang_id == $value->barang_master_id) {
                                $barangAP = $value->ap_id;
                                $barangAR = $value->ar_id;
                                $barangAPFound = true;
                            }
                        }
                        if (!$barangAPFound) {
                            // Collect errors
                            $errors[] = "Barang Tidak Memiliki Akun COA";
                        } else {
                            $result[] = array(
                                'id_transaksi' => $id_transaksi_jurnal,
                                'id_coa' =>  $barangAP,
                                'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                                'debit' => repairDouble($dataBPDetail->total) * $exchangeTransaksi,
                                'kredit' => 0,
                                'keterangan' => $dataBP->po_no,
                                'id_inputer' => session()->get("login")->user_id
                            );
                        }
                        if (!empty($errors)) {
                            return response()->setJSON([
                                "status" => false,
                                "message" => implode(', ', $errors),
                                'token' => csrf_hash()
                            ]);
                        }
                    }
                    // end input jurnal dari banyak detail barang

                    // update total debit dan kredit dari total nilai pada jurnal umum
                    $this->transaksiJurnalModel->update(
                        $id_transaksi_jurnal,
                        [
                            'total_debit' => $totalPO,
                            'total_kredit' => $totalPO,
                        ]
                    );

                    //untuk insert ke jurnal umum
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' =>  $UtangAP,
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                        'debit' => 0,
                        'kredit' => $totalPO * $exchangeTransaksi,
                        'keterangan' => $dataBP->po_no,
                        'id_inputer' => session()->get("login")->user_id
                    );

                    $resultTransaksiPembelian[] = array(
                        'id_po_bp' => $poID,
                        'id_supplier' => $dataBP->supplier_id,
                        'id_transaksi_jurnal' => $id_transaksi_jurnal,
                        'tgl_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                    );
                }

                $this->jurnalUmumModel->insertJurnalBatch($result);
                $this->transaksiPembelianModel->insertBatchTransaksiPembelian($resultTransaksiPembelian);
            }
        }
    }

    public function insertDataPembayaran($payID, $module)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";
        $dataPO = "";
        if ($module == "LOKAL") {
            $result = array();
            $POlocal = $this->localPOPaymentModel->asObject()->where('deletedAt', null)->where('id', $payID)->first();
            if ($POlocal) {
                $dataSupplier = $this->supplierModel->getSupplierForJurnal($POlocal->supplier_id);
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembayaran')->findAll();

                $dataPenerimaan = $this->penerimaanBarangModel->asObject()
                    ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
                    ->where('penerimaan_barang.id', $POlocal->lpb_no)
                    ->where('penerimaan_barang.deletedAt', null)
                    ->where('penerimaan_barang_detail.deletedAt', null)
                    ->findAll();
                foreach ($dataSupplier as $value) {
                    foreach ($dataAccountSupplier as $valueAccount) {
                        if ($value->id == $valueAccount->supplier_id) {
                            $UtangAP = $valueAccount->ap_id;
                            $UtangAR = $valueAccount->ar_id;
                        }
                    }
                    foreach ($dataAccountModule as $valueModule) {
                        if ($valueModule->type == strtoupper($POlocal->type_po) && $valueModule->kategori == $module && $valueModule->module == "pembelian") {
                            $UtangAP = $valueModule->ap_id;
                            $UtangAR = $valueModule->ar_id;
                        }
                    }
                }
                // start inisialisasi kode transaksi
                foreach ($dataMetadataTipeTransaksi as $val) {
                    $kodeTransaksi = $val->description;
                    $idTransaksi = $val->id;
                }
                // end inisialisasi kode transaksi
                // input ke transaksi jurnal
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                    'total_debit' => $POlocal->amount,
                    'total_kredit' => $POlocal->amount,
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => 'IDR',
                    'exchange_rate' => 1,
                );

                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                //untuk insert ke jurnal umum

                foreach ($dataPenerimaan as $value) {
                    $dataPO = str_replace(['[', ']', '"', "\\"], '', $value->multiple_po_no);
                }
                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' =>  $POlocal->akun_kas,
                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                    'debit' => 0,
                    'kredit' => repairDouble($POlocal->amount),
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' =>  $POlocal->akun_selisih == 0 || $POlocal->akun_selisih == NULL ? $UtangAR : $POlocal->akun_selisih,
                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                    'debit' => repairDouble($POlocal->amount),
                    'kredit' => 0,
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        } else {
            $POimport = $this->importPOPaymentModel->asObject()->where('deletedAt', null)->where('id', $payID)->first();
            if ($POimport) {
                $totalPO = 0;
                $dataSupplier = $this->supplierModel->getSupplierForJurnal($POimport->supplier_id);
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembayaran')->findAll();

                $rmImportPO = $this->rmImportPOModel->asObject()
                    ->where('rm_import_pos.id', $POimport->po_id)
                    ->where('rm_import_pos.deletedAt', null)
                    ->findAll();
                $rmImportPODetail = $this->rmImportPODetailModel->asObject()
                    ->where('rm_import_po_details.rm_import_po_id', $POimport->po_id)
                    ->where('rm_import_po_details.deletedAt', null)
                    ->findAll();
                foreach ($dataSupplier as $value) {
                    foreach ($dataAccountSupplier as $valueAccount) {
                        if ($value->id == $valueAccount->supplier_id) {
                            $UtangAP = $valueAccount->ap_id;
                            $UtangAR = $valueAccount->ar_id;
                        }
                    }
                    foreach ($dataAccountModule as $valueModule) {
                        if ($valueModule->type == strtoupper($POimport->po_type) && $valueModule->kategori == $module && $valueModule->module == "pembelian") {
                            $UtangAP = $valueModule->ap_id;
                            $UtangAR = $valueModule->ar_id;
                        }
                    }
                }
                // start inisialisasi kode transaksi
                foreach ($dataMetadataTipeTransaksi as $val) {
                    $kodeTransaksi = $val->description;
                    $idTransaksi = $val->id;
                }
                // end inisialisasi kode transaksi
                // input ke transaksi jurnal
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'total_debit' => repairDouble($POimport->payment_amt)  * repairDouble($POimport->current_exchange_rate),
                    'total_kredit' => repairDouble($POimport->payment_amt)  * repairDouble($POimport->current_exchange_rate),
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => $POimport->currency,
                    'exchange_rate' => $POimport->current_exchange_rate,
                );

                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                //untuk insert ke jurnal umum

                foreach ($rmImportPO as $value) {
                    $dataPO = str_replace(['[', ']', '"', "\\"], '', $value->po_no);
                }

                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' =>  $POimport->akun_kas,
                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'debit' => 0,
                    'kredit' => repairDouble($POimport->payment_amt) * repairDouble($POimport->current_exchange_rate),
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' =>  $POimport->akun_selisih == 0 || $POimport->akun_selisih == NULL ? $UtangAR : $POimport->akun_selisih,
                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'debit' => repairDouble($POimport->payment_amt) * repairDouble($POimport->current_exchange_rate),
                    'kredit' => 0,
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        }
    }
}
