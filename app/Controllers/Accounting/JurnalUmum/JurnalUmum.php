<?php

namespace App\Controllers\Accounting\JurnalUmum;

use App\Controllers\BaseController;
use App\Models\AccountModuleModel;
use App\Models\Sub_AkunsModel;
use App\Models\JurnalUmumModel;
use App\Models\TransaksiJurnalModel;
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

class JurnalUmum extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $transaksiJurnalModel;
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

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
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
                $debitValue = isset($_POST['debit'][$key]) ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['debit'][$key])) : 0;
                $kreditValue = isset($_POST['kredit'][$key]) ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['kredit'][$key])) : 0;

                if ($debitValue == 0) {
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' => $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getPost('tgl_transaksi')))),
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
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getPost('tgl_transaksi')))),
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
                'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getPost('tgl_transaksi')))),
                'total_debit' => $total_debit,
                'total_kredit' => $total_credit,
                'metode_input' => 'manual',
                'type_transaksi' => $this->encrypter->decrypt(hex2bin($this->request->getPost('type_transaksi'))),
            ];
            $this->jurnalUmumModel->insertJurnalBatch($result);
            $this->transaksiJurnalModel->insertTransaksiJurnal($dataTransaksiJurnal);

            session()->setFlashdata('success_message', 'Data Berhasil disimpan');
        } catch (\Exception $e) {
            session()->setFlashdata('error_message', $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
        return redirect()->to('jurnal');
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

                        $id_transaksi_jurnal = $this->transaksiJurnalModel->getIdTransaksiLast();

                        // start inisialisasi account
                        // foreach ($dataDepartment as $value) {
                        //     $KasAP = $value->ap_id;
                        //     $KasAR = $value->ar_id;
                        // }
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
                        // start input jurnal dari banyak detail barang

                        foreach ($dataPOBBDetail as $dataBBDetail) {
                            $totalPOqty = (($dataBBDetail->general_price * $dataBBDetail->qty));
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
                            'type_transaksi' => $idTransaksi,
                        );
                    }
                    $this->jurnalUmumModel->insertJurnalBatch($result);
                    $this->transaksiJurnalModel->insertBatchTransaksiJurnal($resultTransaksiJurnal);
                }
            } else {
                $dataPOBB = $this->rmImportPOModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
                if ($dataPOBB) {
                    $result = array();
                    $resultTransaksiJurnal = array();
                    foreach ($dataPOBB as $dataBB) {
                        $totalPO = 0;
                        $kodeTransaksi = "";
                        $idTransaksi = "";

                        $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBB->division_id);
                        $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBB->supplier_id);
                        $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                        $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                        $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                        // var_dump($dataAccountModule);
                        // exit;

                        $dataPOBBDetail = $this->rmImportPODetailModel->asObject()->where('deletedAt', null)->where('rm_import_po_id', $dataBB->id)->findAll();
                        $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembelian')->findAll();

                        $id_transaksi_jurnal = $this->transaksiJurnalModel->getIdTransaksiLast();

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
                                    'debit' => repairDouble($dataBBDetail->total),
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
                        //untuk insert ke jurnal umum
                        $result[] = array(
                            'id_transaksi' => $id_transaksi_jurnal,
                            'id_coa' =>  $UtangAP,
                            'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'debit' => 0,
                            'kredit' => $totalPO,
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
                            'type_transaksi' => $idTransaksi,
                        );
                    }
                    $this->jurnalUmumModel->insertJurnalBatch($result);
                    $this->transaksiJurnalModel->insertBatchTransaksiJurnal($resultTransaksiJurnal);
                }
            }
        } else {
            $dataPOBP = $this->aMPurchaseOrderModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
            if ($dataPOBP) {
                $result = array();
                $resultTransaksiJurnal = array();
                foreach ($dataPOBP as $dataBP) {
                    $totalPO = 0;
                    $kodeTransaksi = "";
                    $idTransaksi = "";

                    $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBP->division_id);
                    $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBP->supplier_id);
                    $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                    $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                    $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                    // var_dump($dataAccountModule);
                    // exit;

                    $dataPOBPDetail = $this->aMPurchaseOrderDetailModel->asObject()->where('deletedAt', null)->where('am_purchase_order_id', $dataBP->id)->findAll();
                    $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembelian')->findAll();

                    $id_transaksi_jurnal = $this->transaksiJurnalModel->getIdTransaksiLast();

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
                                'debit' => repairDouble($dataBPDetail->total),
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
                    //untuk insert ke jurnal umum
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' =>  $UtangAP,
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                        'debit' => 0,
                        'kredit' => $totalPO,
                        'keterangan' => $dataBP->po_no,
                        'id_inputer' => session()->get("login")->user_id
                    );

                    $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                    $resultTransaksiJurnal[] = array(
                        'no_transaksi' => $no_transaksi_jurnal,
                        'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                        'total_debit' => $totalPO,
                        'total_kredit' => $totalPO,
                        'metode_input' => 'system',
                        'type_transaksi' => $idTransaksi,
                    );
                }
                // var_dump($result);
                // var_dump($resultTransaksiJurnal);
                // exit;
                $this->jurnalUmumModel->insertJurnalBatch($result);
                $this->transaksiJurnalModel->insertBatchTransaksiJurnal($resultTransaksiJurnal);
            }
        }
    }
}
