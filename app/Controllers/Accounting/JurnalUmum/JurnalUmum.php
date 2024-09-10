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
use App\Models\AccountDivisisModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\LocalPOPaymentModel;
use App\Models\LocalPOPaymentDetailModel;
use App\Models\ImportPOPaymentModel;
use App\Models\KursModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\SalesOrderLainModel;
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
    protected $accountDivisisModel;
    protected $localPOPaymentModel;
    protected $localPOPaymentDetailModel;
    protected $importPOPaymentModel;
    protected $penerimaanBarangModel;
    protected $metadataModel;
    protected $kursModel;

    protected $salesOrderLainModel;
    protected $salesOrderLainDetailModel;

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
        $this->accountDivisisModel = new AccountDivisisModel();
        $this->localPOPaymentModel = new LocalPOPaymentModel();
        $this->localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $this->importPOPaymentModel = new ImportPOPaymentModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->metadataModel = new MetadataModel();
        $this->kursModel = new KursModel();

        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
    }

    public function index()
    {
        $accountModuleModel = new AccountModuleModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $accountModuleData = $accountModuleModel->asObject()->findAll();
        $subAkunsModel = $Sub_AkunsModel->getAPAR($this->this_company_id);
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
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
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

                if (isset($_POST['kurs'][$key])) {
                    $kursValue = ($_POST['kurs'][$key] != "") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['kurs'][$key])) : 0;
                } else {
                    $kursValue = 0;
                }

                if ($debitValue == 0) {
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' => $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tgl_transaksi'][$key]))),
                        'debit' => $debitValue,
                        'kredit' => $kreditValue,
                        'valas' => $_POST['valas'][$key],
                        'kurs' => $kursValue,
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
                        'valas' => $_POST['valas'][$key],
                        'kurs' => $kursValue,
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
        $query = strtolower(str_replace(' ', '', $this->request->getPost('query')));

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

    public function searchSubAkunExact()
    {
        $query = strtolower(str_replace(' ', '', $this->request->getPost('query')));

        $subAkunModel = new Sub_AkunsModel();
        $subAkuns = $subAkunModel->searchSubAkunExact($query);

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
                        $dataMetadataValutaIDR = $this->MetadataModel->asObject()->where('name', 'Valuta')->where('value', 'IDR')->first();

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
                                if ($dataBB->barang_id == $value->barang_master_id && $dataBB->divisi_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
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
                                    'divisi_id' => $dataBB->divisi_id,
                                    'company_id' => $this->this_company_id,
                                    'id_coa' =>  $barangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => (repairDouble($totalPOqty)),
                                    'kredit' => 0,
                                    'valas' => $dataMetadataValutaIDR->id,
                                    'kurs' => 1,
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
                            'divisi_id' => $dataBB->divisi_id,
                            'company_id' => $this->this_company_id,
                            'id_coa' =>  $UtangAP,
                            'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'debit' => 0,
                            'kredit' => repairDouble($totalPO),
                            'valas' => $dataMetadataValutaIDR->id,
                            'kurs' => 1,
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

                        $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBB->division_id, $dataBB->company_id);
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
                            $KasAP = $value->coa_kas_id;
                            $KasAR = $value->coa_piutang_id;
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
                                if ($dataBBDetail->barang_id == $value->barang_master_id && $dataBB->division_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
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
                                    'divisi_id' => $dataBB->division_id,
                                    'company_id' => $this->this_company_id,
                                    'id_coa' =>  $barangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => repairDouble($dataBBDetail->total) * $exchangeTransaksi,
                                    'kredit' => 0,
                                    'valas' => $valasTransaksi,
                                    'kurs' => $exchangeTransaksi,
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
                            'divisi_id' => $dataBB->division_id,
                            'company_id' => $this->this_company_id,
                            'id_coa' =>  $UtangAP,
                            'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'debit' => 0,
                            'kredit' => $totalPO * $exchangeTransaksi,
                            'valas' => $valasTransaksi,
                            'kurs' => $exchangeTransaksi,
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
                $valasTransaksi = "IDR";
                $exchangeTransaksi = 1;
                foreach ($dataPOBP as $dataBP) {
                    $totalPO = 0;
                    $kodeTransaksi = "";
                    $idTransaksi = "";

                    $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBP->division_id, $dataBP->company_id);
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
                        $KasAP = $value->coa_kas_id;
                        $KasAR = $value->coa_piutang_id;
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
                            if ($dataBPDetail->barang_id == $value->barang_master_id && $dataBP->division_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
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
                                'divisi_id' => $dataBP->division_id,
                                'company_id' => $this->this_company_id,
                                'id_coa' =>  $barangAP,
                                'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                                'debit' => repairDouble($dataBPDetail->total) * $exchangeTransaksi,
                                'kredit' => 0,
                                'valas' => $valasTransaksi,
                                'kurs' => $exchangeTransaksi,
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
                        'divisi_id' => $dataBP->division_id,
                        'company_id' => $this->this_company_id,
                        'id_coa' =>  $UtangAP,
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                        'debit' => 0,
                        'kredit' => $totalPO * $exchangeTransaksi,
                        'valas' => $valasTransaksi,
                        'kurs' => $exchangeTransaksi,
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
            $POlocal = $this->localPOPaymentModel->asObject()->find($payID);
            if ($POlocal) {
                $dataSupplier = $this->supplierModel->getSupplierForJurnal($POlocal->supplier_id);
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'PEMBAYARAN')->findAll();

                foreach ($dataSupplier as $value) {
                    foreach ($dataAccountSupplier as $valueAccount) {
                        if ($value->id == $valueAccount->supplier_id) {
                            $UtangAP = $valueAccount->ap_id;
                            $UtangAR = $valueAccount->ar_id;
                        }
                    }
                    foreach ($dataAccountModule as $valueModule) {
                        if ($valueModule->type == strtoupper($value->type) && $valueModule->kategori == $module && $valueModule->module == "pembelian") {
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

                $multipleLpbIds = str_replace(['[', ']'], '', $POlocal->multiple_lpb_id); // Remove brackets
                $lpbIdsArray = explode(',', $multipleLpbIds); // Split the string into an array by comma

                foreach ($lpbIdsArray as $lpbId) {
                    $sumValue = 0;
                    $dataPenerimaan = $this->penerimaanBarangModel->asObject()
                        ->select('penerimaan_barang.*, penerimaan_barang_detail.*, local_po_payment_details.*')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
                        ->join('local_po_payment_details', 'local_po_payment_details.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->where('penerimaan_barang.id', $lpbId)
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->where('local_po_payment_details.deletedAt', null)
                        ->where('local_po_payment_details.local_po_payment_id', $payID)
                        ->groupBy('local_po_payment_details.penerimaan_barang_id, local_po_payment_details.penerimaan_barang_detail_id')
                        ->findAll();

                    // var_dump($dataPenerimaan);
                    foreach ($dataPenerimaan as $value) {
                        $dataPO = str_replace(['[', ']', '"', "\\"], '', $value->multiple_po_no);
                        $sumValue = $value->total;
                        $result[] = array(
                            'id_transaksi'      => $id_transaksi_jurnal,
                            'id_coa'            => $POlocal->akun_kas == 0 || $POlocal->akun_kas == NULL ? $UtangAR : $POlocal->akun_kas,
                            'company_id'            => $POlocal->company_id,
                            'divisi_id'            => $POlocal->divisi_id,
                            'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                            'debit'             => ($sumValue),
                            'kredit'            => 0,
                            'valas'             => 'IDR',
                            'kurs'              => 1,
                            'keterangan'        => "Pembayaran PO " . $dataPO,
                            'id_inputer'        => session()->get("login")->user_id
                        );
                        $result[] = array(
                            'id_transaksi'      => $id_transaksi_jurnal,
                            'id_coa'            => $POlocal->akun_selisih,
                            'company_id'            => $POlocal->company_id,
                            'divisi_id'            => $POlocal->divisi_id,
                            'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                            'debit'             => 0,
                            'kredit'            => ($sumValue),
                            'valas'             => 'IDR',
                            'kurs'              => 1,
                            'keterangan'        => "Pembayaran PO " . $dataPO,
                            'id_inputer'        => session()->get("login")->user_id
                        );
                    }
                }
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
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'PEMBAYARAN')->findAll();

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
                    'valas' => $POimport->currency,
                    'kurs' => $POimport->current_exchange_rate,
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' =>  $POimport->akun_selisih == 0 || $POimport->akun_selisih == NULL ? $UtangAR : $POimport->akun_selisih,
                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'debit' => repairDouble($POimport->payment_amt) * repairDouble($POimport->current_exchange_rate),
                    'kredit' => 0,
                    'valas' => $POimport->currency,
                    'kurs' => $POimport->current_exchange_rate,
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        }
    }

    public function TransaksiJurnalStockBarang($companyID, $divisiID, $barang1ID, $barang2ID, $typeBarang, $noPO, $operasi)
    {
        $kategori = "";
        $valas = "";
        $kurs = "";
        $barangAPFound = false;

        $conditionAccountBarang = [
            'company_id' => $companyID,
            'divisi_id' => $divisiID,
            'barang_master_id' => $barang1ID,
        ];

        // definisi data
        $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal($conditionAccountBarang);
        $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'MUTASI')->findAll();
        $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
        $dataAccountDivisi = $this->accountDivisisModel->getAccountDivisiForJurnal();
        $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
        $dataMetadataValutaIDR = $this->MetadataModel->asObject()->where('name', 'Valuta')->where('value', 'IDR')->first();

        if ($typeBarang == "bahan_penolong") {
            $dataPO = $this->aMPurchaseOrderModel->getPOByNoPO($noPO, $this->this_company_id, $barang1ID, $barang2ID);
            $kategori = $dataPO["dataPO"]["po_type"];
            $kursData = $this->kursModel->getByMetaId($dataPO["dataPO"]["currency"], $dataPO["dataPO"]["po_date"]);
            if ($kursData) {
                $kurs = $kursData->nilai_kurs;
            } else {
                $kurs = 1;
            }
            $valas = $dataPO["dataPO"]["currency"];
            $valasText = $this->MetadataModel->asObject()->find($dataPO["dataPO"]["currency"]);
        } else if ($typeBarang == "bahan_baku") {
            $dataPO = $this->rMPurchaseOrderModel->getPOByNoPO($noPO, $this->this_company_id, $barang1ID, $barang2ID);
            if ($dataPO) {
                $kategori = "LOKAL";
                $kurs = 1;
                $valas = $dataMetadataValutaIDR->id;
                $valasText = "IDR";
            } else {
                $dataPO = $this->rmImportPOModel->getPOByNoPO($noPO, $this->this_company_id, $barang1ID, $barang2ID);
                $kursData = $this->kursModel->getByMetaId($dataPO["dataPO"]["currency"], $dataPO["dataPO"]["po_date"]);
                if ($kursData) {
                    $kurs = $kursData->nilai_kurs;
                } else {
                    $kurs = 1;
                }
                $kategori = "IMPORT";
                $valas = $dataPO["dataPO"]["currency"];
                $valasText = $this->MetadataModel->asObject()->find($dataPO["dataPO"]["currency"]);
            }
        } else {
            $kategori = "LOKAL";
            $kurs = 1;
            $valas = $dataMetadataValutaIDR->id;
            $valasText = "IDR";
        }

        foreach ($dataMetadataTipeTransaksi as $val) {
            $kodeTransaksi = $val->description;
            $idTransaksi = $val->id;
        }

        if ($typeBarang != "bahan_jadi" || $typeBarang != "bahan_setengah_jadi") {
            if ($dataAccountSupplier) {
                foreach ($dataAccountSupplier as $valueAccount) {
                    if ($dataPO["dataPO"]["supplier_id"] == $valueAccount->supplier_id) {
                        $UtangAP = $valueAccount->ap_id;
                        $UtangAR = $valueAccount->ar_id;
                    }
                }
            }

            foreach ($dataAccountModule as $valueModule) {
                if ($valueModule->type == strtoupper(str_replace("_", " ", $typeBarang)) && $valueModule->kategori == strtoupper($kategori) && $valueModule->module == "pembelian") {
                    $UtangAP = $valueModule->ap_id;
                    $UtangAR = $valueModule->ar_id;
                }
            }

            foreach ($dataAccountBarang as $value) {
                if ($barang1ID == $value->barang_master_id && $divisiID == $value->divisi_id && $companyID == $value->company_id) {
                    $barangAP = $value->ap_id;
                    $barangAR = $value->ar_id;
                    $barangAPFound = true;
                }
            }
        } else {
            foreach ($dataAccountDivisi as $valueAccount) {
                if ($divisiID == $valueAccount->divisis_id) {
                    $UtangAP = $valueAccount->ap_id;
                    $UtangAR = $valueAccount->ar_id;
                }
            }

            foreach ($dataAccountBarang as $value) {
                if ($barang1ID == $value->barang_master_id && $divisiID == $value->divisi_id && $companyID == $value->company_id) {
                    $barangAP = $value->ap_id;
                    $barangAR = $value->ar_id;
                    $barangAPFound = true;
                }
            }
        }

        if (!$barangAPFound) {
            return response()->setJSON([
                "status" => false,
                "message" => "Barang Tidak Memiliki Akun COA",
                'token' => csrf_hash()
            ]);
        }

        $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);

        $resultTransaksiJurnal = array(
            'no_transaksi' => $no_transaksi_jurnal,
            'tanggal_transaksi' => date('Y-m-d'),
            'total_debit' => $dataPO['hargaTerakhirNumber'],
            'total_kredit' => $dataPO['hargaTerakhirNumber'],
            'metode_input' => 'system',
            'type_transaksi' => $idTransaksi,
            'no_bukti' => $no_transaksi_jurnal,
            'valas' => $valasText,
            'exchange_rate' => $kurs,
        );

        $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

        if ($operasi == "IN") {
            $result[] = array(
                'id_transaksi' => $id_transaksi_jurnal,
                'id_coa' =>  $barangAP,
                'company_id' =>  $companyID,
                'divisi_id' =>  $divisiID,
                'tanggal_jurnal' => date('Y-m-d'),
                'debit' => 0,
                'kredit' => repairDouble($dataPO['hargaTerakhirNumber']),
                'valas' => $valas,
                'kurs' => $kurs,
                'keterangan' => "Pindah Saldo ",
                'id_inputer' => session()->get("login")->user_id
            );

            $result[] = array(
                'id_transaksi' => $id_transaksi_jurnal,
                'id_coa' =>  $UtangAR,
                'company_id' =>  $companyID,
                'divisi_id' =>  $divisiID,
                'tanggal_jurnal' => date('Y-m-d'),
                'debit' => repairDouble($dataPO['hargaTerakhirNumber']),
                'kredit' => 0,
                'valas' => $valas,
                'kurs' => $kurs,
                'keterangan' => "Pindah Saldo ",
                'id_inputer' => session()->get("login")->user_id
            );
        } else {
            $result[] = array(
                'id_transaksi' => $id_transaksi_jurnal,
                'id_coa' =>  $barangAP,
                'company_id' =>  $companyID,
                'divisi_id' =>  $divisiID,
                'tanggal_jurnal' => date('Y-m-d'),
                'debit' => repairDouble($dataPO['hargaTerakhirNumber']),
                'kredit' => 0,
                'valas' => $valas,
                'kurs' => $kurs,
                'keterangan' => "Pindah Saldo ",
                'id_inputer' => session()->get("login")->user_id
            );

            $result[] = array(
                'id_transaksi' => $id_transaksi_jurnal,
                'id_coa' =>  $UtangAR,
                'company_id' =>  $companyID,
                'divisi_id' =>  $divisiID,
                'tanggal_jurnal' => date('Y-m-d'),
                'debit' => 0,
                'kredit' => repairDouble($dataPO['hargaTerakhirNumber']),
                'valas' => $valas,
                'kurs' => $kurs,
                'keterangan' => "Pindah Saldo ",
                'id_inputer' => session()->get("login")->user_id
            );
        }
        $this->jurnalUmumModel->insertJurnalBatch($result);
    }


    public function insertDataPenjualan($poID, $typePenjualan = ['LAIN', 'LOKAL', 'INTERNASIONAL'])
    {
        $kasDepartment = "";
        $piutangDepartment = "";
        $gajiDepartment = "";
        $hppDepartment = "";
        // $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBP->supplier_id);
        // $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
        // $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
        // $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();

        if ($typePenjualan == 'LAIN') {
            $salesOrderLain = $this->salesOrderLainModel->find($poID);
            if ($salesOrderLain) {
                $dataDepartment = $this->divisionModel->getAccountKasForJurnal($salesOrderLain['divisi_id'], $salesOrderLain['company_id']);
                $salesOrderLainDetail = $this->salesOrderLainDetailModel->where('sales_order_lain_id', $poID)->findAll();

                foreach ($dataDepartment as $value) {
                    $kasDepartment = $value->coa_kas_id;
                    $piutangDepartment = $value->coa_piutang_id;
                    $gajiDepartment = $value->coa_gaji_id;
                    $hppDepartment = $value->coa_hpp_id;
                }

                foreach ($salesOrderLainDetail as $value) {
                    # code...
                }
            }
        } elseif ($typePenjualan == "LOKAL") {
            # code...
        } elseif ($typePenjualan == "INTERNASIONAL") {
            # code...
        }

        var_dump($kasDepartment);
        var_dump($piutangDepartment);
        var_dump($gajiDepartment);
        var_dump($hppDepartment);
    }
}
