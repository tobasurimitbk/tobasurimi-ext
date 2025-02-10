<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Models\BanksModel;
use App\Models\DivisisModel;
use App\Models\SupplierModel;
use App\Models\LocalPOPaymentModel;
use App\Models\LocalPOPaymentBPModel;
use App\Models\LocalPOPaymentPanjarModel;
use App\Models\LocalPOPaymentPinjamanModel;
use App\Models\PanjarSupplierModel;
use App\Models\PinjamanSupplierModel;
use App\Models\LocalPOPaymentDetailModel;
use App\Models\PajakTandaTerimaFakturModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\TandaTerimaFakturDetailModel;
use App\Models\TandaTerimaFakturModel;
use App\Models\Sub_AkunsModel;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;
use PhpParser\Node\Stmt\TryCatch;

class PembayaranPOLokal extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalController;
    protected $banksModel;
    protected $divisiModel;
    protected $panjarSupplierModel;
    protected $pinjamanSupplierModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->jurnalController = new JurnalUmum();
        $this->banksModel = new BanksModel();
        $this->divisiModel = new DivisisModel();
        $this->panjarSupplierModel = new PanjarSupplierModel();
        $this->pinjamanSupplierModel = new PinjamanSupplierModel();
    }

    public function pembayaranPOLokalBP()
    {
        return view('Pembayaran/pembayaranPOLokal/bahanPenolong');
    }

    public function createPembayaranPOLokalBP()
    {
        $supplierModel = new SupplierModel();
        $panjarSupplierModel = new PanjarSupplierModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $supplierList = $supplierModel->getSupplierByType("BAHAN PENOLONG");

        $panjarSupplierList = $panjarSupplierModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->findAll();

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        $divisiList = $this->divisiModel->getDivisiAccess();

        $data = [
            "suppliers" => $supplierList,
            "panjar_supplier" => $panjarSupplierList,
            "subsAkuns" => $subAkunsModel,
            "divisi" => $divisiList
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanPenolong', $data);
    }

    public function getTandaTerimaFaktur($supplierID, $divisiID)
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $res = $tandaTerimaFakturModel->getListTandaTerimaFakturNotProcessed($supplierID, $divisiID);
        return response()->setJSON([
            'data' => $res,
            'status' => true
        ]);
    }

    public function getItemListByTandaTerimaFaktur($tandaTerimaFakturID)
    {
        $pembayaranId = decrypt($this->request->getVar('id'));

        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();
        $localPOPaymentBPModel = new LocalPOPaymentBPModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

        $statusPph = $this->request->getVar('status_pph');
        $detail = $tandaTerimaFakturModel->getByID($tandaTerimaFakturID);

        $taxDipungutNegara = $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dipungut oleh negara", $tandaTerimaFakturID);
        $pphNilai = $statusPph == '1' ? 0.0025 : 0;
        $pphResult = $pphNilai * ($detail['nominal_faktur'] + $taxDipungutNegara['taxAmt']);

        return response()->setJSON([
            'detail' => $tandaTerimaFakturModel->getByID($tandaTerimaFakturID),
            'paymentDetail' => $localPOPaymentBPModel->getPembayaranDetailByid($pembayaranId),
            'list' => $tandaTerimaFakturDetailModel->getListTandaTerimaItemFaktur($tandaTerimaFakturID),
            'panjar_list' => $localPOPaymentPanjarModel->getPembayaranPanjarDetailsbyIdandType($pembayaranId, "BP"),
            'panjar_tb_list' => $localPOPaymentPanjarModel->getPembayaranPanjarTBDetailsbyIdandType($pembayaranId, "BP"),
            'pinjaman_list' => $localPOPaymentPinjamanModel->getPembayaranPinjamanDetailsbyIdandType($pembayaranId, "BP"),
            'tax_dipungut_negara' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dipungut oleh negara", $tandaTerimaFakturID),
            'tax_dikembalikan_lagi' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dikembalikan lagi", $tandaTerimaFakturID),
            'pph' => $pphResult

        ]);
    }


    public function createPembayaranPOLokalBPAction()
    {

        try {
            $localPOPaymentModel = new LocalPOPaymentModel();
            $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
            $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();
            $localPOPaymentBPModel = new LocalPOPaymentBPModel();
            $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();

            $pembayaranList = json_decode($this->request->getVar('pembayaranList'));
            $panjarList = json_decode($this->request->getVar('panjarList'));
            $pinjamanList = json_decode($this->request->getVar('pinjamanList'));
            $panjarTBList = json_decode($this->request->getVar('panjarTBList'));

            $total_bayar_panjar = 0;
            foreach ($panjarList as $p) {
                $total_bayar_panjar += intval($p->bayar_panjar);
            }

            $total_bayar_panjar_tb = 0;
            foreach ($panjarTBList as $p) {
                $total_bayar_panjar_tb += intval($p->bayar_panjar);
            }

            $total_bayar_pinjaman = 0;
            foreach ($pinjamanList as $p) {
                $total_bayar_pinjaman += intval($p->bayar_pinjaman);
            }

            // CHECK
            $check = $localPOPaymentBPModel->where('company_id', $this->this_company_id)->where('payment_no',  $this->request->getVar('no_bukti_pembayaran'))->first();

            if ($check != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "No pembayaran sudah digunakan",
                    'status' => false
                ]);
            }
            $id = $localPOPaymentBPModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'payment_no' => $this->request->getVar('no_bukti_pembayaran'),
                'supplier_id' => $this->request->getVar('supplier_id'),
                'tanda_terima_faktur_id' => $this->request->getVar('tanda_terima_faktur_id'),

                'amount' => repairDouble($this->request->getVar('nominal_pembayaran')),
                'payment_panjar_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_panjar_date')))),
                'payment_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'payment_method' => $this->request->getVar('payment_method'),

                'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
                'status_pph' => $this->request->getVar('status_pph'),
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih'),
                'status_posting' => '0'
            ]);

            foreach ($panjarList as $p) {
                if ($p->bayar_panjar != '') {

                    if (intval($p->bayar_panjar) != 0) {
                        $localPOPaymentPanjarModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "jenis_panjar" => "PANJAR",
                            "type" => "BP",
                            "panjar_id" => $p->id,
                            "bayar_panjar" => repairDouble($p->bayar_panjar)

                        ]);
                    }
                }
            }


            foreach ($panjarTBList as $p) {
                if ($p->bayar_panjar != '') {

                    if (intval($p->bayar_panjar) != 0) {
                        $localPOPaymentPanjarModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "jenis_panjar" => "PANJAR_TB",
                            "type" => "BP",
                            "panjar_id" => $p->id,
                            "bayar_panjar" => repairDouble($p->bayar_panjar)
                        ]);
                    }
                }
            }


            foreach ($pinjamanList as $p) {
                if ($p->bayar_pinjaman != '') {

                    if (intval($p->bayar_pinjaman) != 0) {
                        $localPOPaymentPinjamanModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "type" => "BP",
                            "pinjaman_id" => $p->id,
                            "bayar_pinjaman" => repairDouble($p->bayar_pinjaman)
                        ]);
                    }
                }
            }


            foreach ($pembayaranList as $l) {
                if (intval($l->price != 0) && isset($l->price)) {

                    $insertLocalPoPaymentDetail = $localPOPaymentDetailModel->insert([
                        "local_po_payment_id"           => $id,
                        "penerimaan_barang_detail_id"   => intval($l->penerimaan_barang_detail_id),
                        "total"                         => repairDouble($l->price)
                    ]);
                }
            }


            return response()->setJSON([
                'message' => "Kwitansi pembayaran lokal bahan penolong berhasil dibuat",
                'status' => true,
                'token' => csrf_hash(),
                'id' => \encrypt($id)
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan " . $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function updatePembayaranPOLokalBPAction()
    {
        try {

            $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
            $localPOPaymentBPModel = new LocalPOPaymentBPModel();
            $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
            $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();
            $id = decrypt($this->request->getVar('id'));

            $panjarList = json_decode($this->request->getVar('panjarList'));
            $panjarTBList = json_decode($this->request->getVar('panjarTBList'));
            $pinjamanList = json_decode($this->request->getVar('pinjamanList'));


            $total_bayar_panjar = 0;
            foreach ($panjarList as $p) {
                $total_bayar_panjar += intval($p->bayar_panjar);
            }

            $total_bayar_panjar_tb = 0;
            foreach ($panjarTBList as $p) {
                $total_bayar_panjar_tb += intval($p->bayar_panjar);
            }


            $total_bayar_pinjaman = 0;
            foreach ($pinjamanList as $p) {
                $total_bayar_pinjaman += intval($p->bayar_pinjaman);
            }



            $lastAmount = $localPOPaymentBPModel->select('amount')
                ->where('id', $id)
                ->first();


            $localPOPaymentBPModel->update($id, [

                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                // 'payment_no' => $this->request->getVar('no_bukti_pembayaran'),
                'supplier_id' => $this->request->getVar('supplier_id'),
                'tanda_terima_faktur_id' => $this->request->getVar('tanda_terima_faktur_id'),

                'amount' => $lastAmount["amount"] + repairDouble($this->request->getVar('nominal_pembayaran')),
                'payment_panjar_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_panjar_date')))),
                'payment_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'payment_method' => $this->request->getVar('payment_method'),

                'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
                'status_pph' => $this->request->getVar('status_pph'),
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih'),
                'status_posting' => '0'
            ]);


            foreach ($panjarList as $p) {
                if ($p->bayar_panjar != '') {

                    if (intval($p->bayar_panjar) != 0) {
                        $localPOPaymentPanjarModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "type" => "BP",
                            "panjar_id" => $p->id,
                            "bayar_panjar" => repairDouble($p->bayar_panjar)

                        ]);
                    }
                }
            }


            foreach ($panjarTBList as $p) {

                if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                    $insertPanjar = $localPOPaymentPanjarModel->update($p->id, [
                        "company_id" => $this->this_company_id,
                        "local_po_payment_id" => $id,
                        "type"  => "BP",
                        "jenis_panjar" => "PANJAR_TB",
                        "panjar_id" => $p->panjar_id,
                        "bayar_panjar" => $p->bayar_panjar
                    ]);
                }
            }

            foreach ($pinjamanList as $p) {

                if (isset($p->bayar_pinjaman) && intval($p->bayar_pinjaman) !=  0) {
                    $insertPinjaman = $localPOPaymentPinjamanModel->update($p->id, [
                        "company_id" => $this->this_company_id,
                        "local_po_payment_id" => $id,
                        "type"  => "BP",
                        "pinjaman_id" => $p->pinjaman_id,
                        "bayar_pinjaman" => $p->bayar_pinjaman
                    ]);
                }
            }

            return response()->setJSON([
                'message' => "Kwitansi pembayaran lokal bahan penolong berhasil diupdate",
                'status' => true,
                'token' => csrf_hash(),
                'id' => \encrypt($id)
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan " . $e->getMessage(),
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    public function createPembayaranPOLokalBBAction()
    {
        try {
            $penerimaanBarangModel = new PenerimaanBarangModel();
            $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();
            $localPOPaymentModel = new LocalPOPaymentModel();
            $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
            $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();

            $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

            $panjarList = json_decode($this->request->getVar('panjarList'));
            $pinjamanList = json_decode($this->request->getVar('pinjamanList'));
            $panjarTBList = json_decode($this->request->getVar('panjarTBList'));
            $pembayaranList = json_decode($this->request->getVar('pembayaranList'), true);


            $poIDAmt = json_decode($this->request->getVar('poIDList'));
            $poNOAmt = json_decode($this->request->getVar('poNoList'));
            $poIDArr = [];
            $poNoArr = [];

            $total_bayar_panjar = 0;
            foreach ($panjarList as $p) {
                if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                    $total_bayar_panjar += intval($p->bayar_panjar);
                }
            }


            $total_bayar_panjar_tb = 0;
            foreach ($panjarTBList as $p) {
                $total_bayar_panjar_tb += intval($p->bayar_panjar);
            }

            $total_bayar_pinjaman = 0;
            foreach ($pinjamanList as $p) {
                $total_bayar_pinjaman += intval($p->bayar_pinjaman);
            }

            foreach ($poIDAmt as $p) {
                array_push($poIDArr, $p->poID);
            }

            foreach ($poNOAmt as $p) {
                array_push($poNoArr, $p->poNo);
            }

            $resPoID =  str_replace('"', "", json_encode(array_values(array_unique($poIDArr))));
            $resPoNo =   json_encode(array_values(array_unique($poNoArr)));


            $total_pembayaran = $this->request->getVar('total_pembayaran');

            // CHECK
            $check = $localPOPaymentModel->where('company_id', $this->this_company_id)
                // ->where('type_po', "Bahan Baku")
                ->where('payment_no',  $this->request->getVar('no_bukti_pembayaran'))->first();

            if ($check != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "No pembayaran sudah digunakan",
                    'status' => false
                ]);
            }

            if ($total_pembayaran < 0) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Potongan harus lebih kecil dari sisa pembayaran",
                    'status' => false
                ]);
            }

            if ($total_pembayaran < $total_bayar_panjar) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "total pembayaran panjar tidak valid",
                    'status' => false
                ]);
            }


            if ($bulan = $this->request->getVar("bulan")) {
                $bulan = date("F Y", strtotime($bulan));
            } else {
                $bulan = null;
            }



            $id = $localPOPaymentModel->insert([
                'company_id'        => $this->this_company_id,
                'divisi_id'         => $this->request->getVar('divisi_id'),
                'supplier_id'       => $this->request->getVar('supplier_id'),
                'bank_id'           => $this->request->getVar('bank_id'),
                'payment_no'        => $this->request->getVar('no_bukti_pembayaran'),
                'payment_panjar_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_panjar_date')))),
                'payment_date'      => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'payment_method'    => $this->request->getVar('payment_method'),
                'type_bayar'        => ucfirst($this->request->getVar('tipe_pembayaran')),
                'bulan'             => $bulan,
                'multiple_lpb_no'   => $resPoNo,
                'multiple_lpb_id'   => $resPoID,
                'pembayaran_oleh'   => $this->request->getVar('pembayaran_oleh'),
                'potongan_harga'    => $this->request->getVar('potongan'),
                'amount' => repairDouble($this->request->getVar('grand_total')),
                'status_posting' => '0',
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih')

            ]);

            foreach ($panjarList as $p) {

                if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                    $insertPanjar = $localPOPaymentPanjarModel->insert([
                        "company_id" => $this->this_company_id,
                        "jenis_panjar" => "PANJAR",
                        "local_po_payment_id" => $id,
                        "type"  => "BB",
                        "panjar_id" => $p->id,
                        "bayar_panjar" => $p->bayar_panjar,
                    ]);
                }
            }


            foreach ($panjarTBList as $p) {
                if ($p->bayar_panjar != '') {

                    if (intval($p->bayar_panjar) != 0) {
                        $localPOPaymentPanjarModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "jenis_panjar" => "PANJAR_TB",
                            "type" => "BB",
                            "panjar_id" => $p->id,
                            "bayar_panjar" => $p->bayar_panjar,
                        ]);
                    }
                }
            }


            foreach ($pinjamanList as $p) {
                if ($p->bayar_pinjaman != '') {

                    if (intval($p->bayar_pinjaman) != 0) {
                        $localPOPaymentPinjamanModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "type" => "BB",
                            "pinjaman_id" => $p->id,
                            "bayar_pinjaman" => $p->bayar_pinjaman,
                        ]);
                    }
                }
            }


            foreach ($pembayaranList as $l) {
                if (
                    isset($l['pembayaran_user_input']) &&
                    intval($l['pembayaran_user_input']) != 0 &&
                    isset($l['penerimaan_barang_id'], $l['penerimaan_barang_detail_id'], $l['rm_purchase_orders_id'], $l['rm_purchase_order_details_id'])
                ) {
                    $localPOPaymentDetailModel->insert([
                        "local_po_payment_id"           => $id,
                        "penerimaan_barang_id"          => intval($l['penerimaan_barang_id']),
                        "penerimaan_barang_detail_id"   => intval($l['penerimaan_barang_detail_id']),
                        "rm_purchase_order_id"          => intval($l['rm_purchase_orders_id']),
                        "rm_purchase_order_details_id"  => intval($l['rm_purchase_order_details_id']),
                        "total"                         => repairDouble($l['pembayaran_user_input'])
                    ]);
                }
            }

            return response()->setJSON([
                'message' => "Kwitansi pembayaran lokal bahan baku berhasil dibuat",
                'status' => true,
                'token' => csrf_hash(),
                'id' => encrypt($id)
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan " . $e->getMessage() . $e->getLine(),
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    public function updatePembayaranPOLokalBBAction()
    {

        $localPOPaymentModel = new LocalPOPaymentModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

        $id = decrypt($this->request->getVar('id'));
        $panjarList = json_decode($this->request->getVar('panjarList'));
        $panjarTBList = json_decode($this->request->getVar('panjarTBList'));
        $pinjamanList = json_decode($this->request->getVar('pinjamanList'));
        $pembayaranList = json_decode($this->request->getVar('pembayaranList'));

        $poIDAmt = json_decode($this->request->getVar('poIDList'));
        $poNOAmt = json_decode($this->request->getVar('poNoList'));
        $poIDArr = [];
        $poNoArr = [];



        foreach ($poIDAmt as $p) {
            array_push($poIDArr, $p->poID);
        }

        foreach ($poNOAmt as $p) {
            array_push($poNoArr, $p->poNo);
        }

        $resPoID =  str_replace('"', "", json_encode(array_values(array_unique($poIDArr))));
        $resPoNo =   json_encode(array_values(array_unique($poNoArr)));


        if ($bulan = $this->request->getVar("bulan")) {
            $bulan = date("F Y", strtotime($bulan));
        } else {
            $bulan = null;
        }


        $total_bayar_panjar = 0;
        foreach ($panjarList as $p) {
            if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                $total_bayar_panjar += intval($p->bayar_panjar);
            }
        }

        $total_bayar_panjar_tb = 0;
        foreach ($panjarTBList as $p) {
            if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                $total_bayar_panjar_tb += intval($p->bayar_panjar);
            }
        }

        $total_bayar_pinjaman = 0;
        foreach ($pinjamanList as $p) {
            if (isset($p->bayar_pinjaman) && intval($p->bayar_pinjaman) !=  0) {
                $total_bayar_pinjaman += intval($p->bayar_pinjaman);
            }
        }



        $total_pembayaran = $this->request->getVar('total_pembayaran');


        if ($total_pembayaran < 0) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Potongan harus lebih kecil dari sisa pembayaran",
                'status' => false
            ]);
        }

        $total_all_pay = $total_bayar_panjar + $total_bayar_panjar_tb + $total_bayar_pinjaman;

        if ($total_pembayaran < $total_all_pay) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "total pembayaran panjar tidak valid",
                'status' => false
            ]);
        }

        $localPOPaymentModel->update($id, [
            'company_id'        => $this->this_company_id,
            'divisi_id'         => $this->request->getVar('divisi_id'),
            'supplier_id'       => $this->request->getVar('supplier_id'),
            'bank_id'           => $this->request->getVar('bank_id'),
            // 'payment_no'        => $this->request->getVar('no_bukti_pembayaran'),
            'payment_panjar_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_panjar_date')))),
            'payment_date'      => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
            'payment_method'    => $this->request->getVar('payment_method'),
            'type_bayar'        => ucfirst($this->request->getVar('tipe_pembayaran')),
            'bulan'             => $bulan,
            'multiple_lpb_no'   => $resPoNo,
            'multiple_lpb_id'   => $resPoID,
            'pembayaran_oleh'   => $this->request->getVar('pembayaran_oleh'),
            'potongan_harga'    => $this->request->getVar('potongan'),
            'amount'            => repairDouble($this->request->getVar('grand_total')),
            'status_posting'    => '0',
            'akun_kas' => $this->request->getVar('akun_kas'),
            'akun_selisih' => $this->request->getVar('akun_selisih')
        ]);

        foreach ($panjarList as $p) {
            if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                $insertPanjar = $localPOPaymentPanjarModel->update($p->id, [
                    "company_id" => $this->this_company_id,
                    "local_po_payment_id" => $id,
                    "type"  => "BB",
                    "jenis_panjar"  => "PANJAR",
                    "panjar_id" => $p->panjar_id,
                    "bayar_panjar" => $p->bayar_panjar,
                ]);
            }
        }

        foreach ($panjarTBList as $p) {

            if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                $insertPanjar = $localPOPaymentPanjarModel->update($p->id, [
                    "company_id" => $this->this_company_id,
                    "local_po_payment_id" => $id,
                    "type"  => "BB",
                    "jenis_panjar" => "PANJAR_TB",
                    "panjar_id" => $p->panjar_id,
                    "bayar_panjar" => $p->bayar_panjar
                ]);
            }
        }

        foreach ($pinjamanList as $p) {

            if (isset($p->bayar_pinjaman) && intval($p->bayar_pinjaman) !=  0) {
                $insertPinjaman = $localPOPaymentPinjamanModel->update($p->id, [
                    "company_id" => $this->this_company_id,
                    "local_po_payment_id" => $id,
                    "type"  => "BB",
                    "jenis_pinjaman" => "Pinjaman_TB",
                    "pinjaman_id" => $p->pinjaman_id,
                    "bayar_pinjaman" => $p->bayar_pinjaman
                ]);
            }
        }



        foreach ($pembayaranList->detail as $l) {
            if (intval($l->total_tagihan != 0) && isset($l->total_tagihan)) {


                $insertLocalPoPaymentDetail = $localPOPaymentDetailModel->update($l->local_po_payment_details_id, [
                    "local_po_payment_id"           => $id,
                    "penerimaan_barang_id"          => intval($l->penerimaan_barang_id),
                    "penerimaan_barang_detail_id"   => intval($l->penerimaan_barang_detail_id),
                    "rm_purchase_order_id"          => intval($l->rm_purchase_orders_id),
                    "rm_purchase_order_details_id"  => intval($l->rm_purchase_order_details_id),
                    "total"                         => repairDouble($l->total_tagihan)
                ]);
            }
        }
        return response()->setJSON([
            'message' => "Kwitansi pembayaran lokal bahan baku berhasil diupdate",
            'status' => true,
            'token' => csrf_hash(),
            'id' => encrypt($id)
        ]);
    }

    // PEMBAYARAN BAHAN BAKU ATAU PENOLONG
    public function allPembayaranPOLokal()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getGet("paymentDate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("paymentDate")))) : "",
        ];

        $dataPembayaranPOLokal = [];

        $typeBayar = [];
        $type = $this->request->getGet('type_bayar');
        if ($type == "All") {
            $typeBayar = ['Bulanan', 'Harian'];
        } else {
            $typeBayar = [$type];
        }

        $condition = [
            "local_po_payments.company_id" => $this->this_company_id,
            // "local_po_payments.type_po"  => $this->request->getGet('type_po'),
            "local_po_payments.deletedAt" => null
        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "startDate" =>  $this->request->getVar("startDate") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("startDate")))) : "",
            "paymentDate" =>  $this->request->getVar("paymentDate") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("paymentDate")))) : "",
            "typeBayar" => $typeBayar,
            "status_posting" => $this->request->getGet('status_posting'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $paymentData = $localPOPaymentModel->getList($condition, $addCondition, $limit, $offset);



        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentData['data'] as $data) {

            $datalpb = $penerimaanBarangModel
                ->select("multiple_po_no")
                ->join('local_po_payment_details', 'local_po_payment_details.penerimaan_barang_id = penerimaan_barang.id')
                ->join('local_po_payments', 'local_po_payments.id = local_po_payment_details.local_po_payment_id')
                ->where('local_po_payments.id', $data->id)
                ->where('local_po_payment_details.deletedAt', null)
                ->findAll();

            $resultlpb = [];


            foreach ($datalpb as $d) {
                array_push($resultlpb,  trim($d['multiple_po_no'], '[]"'));
            }


            array_push($dataPembayaranPOLokal, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "payment_no"        => $data->payment_no,
                // "faktur_no"         => $data->faktur_no,
                "supplier"          => $data->supplierName,
                "po_number"         => implode(" ", array_unique($resultlpb)),
                "payment_date"      => $data->payment_date,
                "payment_method"    => strtoupper($data->payment_method),
                "amount"            => number_format($data->amount ?? 0, 0, ',', '.'),
                "tipe_bayar"        => strtoupper($data->type_bayar),
                'status_posting'    => $data->status_posting
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $paymentData['totalData'],
            "recordsFiltered"   => $paymentData['totalFilteredData'],
            "data"              => $dataPembayaranPOLokal,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function allPembayaranPOLokalBP()
    {
        $localPOPaymentBPModel = new LocalPOPaymentBPModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getGet("paymentDate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("paymentDate")))) : "",
        ];

        $condition = [
            "local_po_payment_bp.company_id" => $this->this_company_id,
            "local_po_payment_bp.deletedAt" => null
        ];


        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "startDate" =>  $this->request->getVar("startDate") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("startDate")))) : "",
            "paymentDate" =>  $this->request->getVar("paymentDate") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("paymentDate")))) : "",
            "status_posting" => $this->request->getGet('status_posting'),
        ];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $paymentData = $localPOPaymentBPModel->getListBP($condition, $addCondition, $limit, $offset);

        $dataPembayaranPOLokal = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentData['data'] as $p) {
            array_push($dataPembayaranPOLokal, [
                "no"                => $no++,
                "id"                => encrypt($p->id),
                "payment_no"        => $p->payment_no,
                "faktur_no"         => $p->faktur_no,
                "due_date"          => $p->due_date,
                "supplier"          => $p->supplierName,
                "payment_date"      => $p->payment_date,
                "payment_method"    => strtoupper($p->payment_method),
                "amount"            => $p->amount,
                'status_posting'    => $p->status_posting
            ]);
        }




        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $paymentData['totalData'],
            "recordsFiltered"   => $paymentData['totalFilteredData'],
            "data"              => $dataPembayaranPOLokal,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function getPembayaranPOLokalBP($id)
    {
        $supplierModel = new SupplierModel();
        $localPOPaymentBPModel = new LocalPOPaymentBPModel();
        $Sub_AkunsModel = new Sub_AkunsModel();
        $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();

        $supplierList = $supplierModel->getSupplierByType("BAHAN PENOLONG");


        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        $id = decrypt($id);
        $paymentDetail = $localPOPaymentBPModel->find($id);

        if ($paymentDetail == null) {
            return redirect()->to('pembayaran-po-lokal-bp');
        }

        $detail = $localPOPaymentBPModel->get($id);
        $divisiList = $this->divisiModel->getDivisiAccess();


        $data = [
            "suppliers" => $supplierList,
            "detail" => $localPOPaymentBPModel->getBahanPenolong($id, $this->this_company_id),
            "subsAkuns" => $subAkunsModel,
            // 'tax_dipungut_negara' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dipungut oleh negara", $detail['tandaTerimaSupplier']['id']),
            // 'tax_dikembalikan_lagi' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dikembalikan lagi", $detail['tandaTerimaSupplier']['id']),
            "divisi" => $divisiList
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanPenolong', $data);
    }

    public function getPanjarSupplier()
    {
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

        $supplierId = $this->request->getVar('supplier_id');
        $panjarTBResult = $this->panjarSupplierModel->getPanjarTBSupplierbySupplierId($supplierId, $this->this_company_id);
        $pinjamanResult = $this->pinjamanSupplierModel->getPinjamanSupplierbySupplierId($supplierId, $this->this_company_id);
        $panjarResult = $this->panjarSupplierModel->getPanjarSupplierbySupplierId($supplierId, $this->this_company_id);

        $panjarList = [];
        foreach ($panjarResult as $p) {
            $bayar_panjar = $localPOPaymentPanjarModel
                ->where('panjar_id', $p->id)
                ->findAll();

            $total_bayar_panjar = 0;

            foreach ($bayar_panjar as $b) {
                $total_bayar_panjar += $b['bayar_panjar'];
            }

            array_push($panjarList, [
                'id'            => $p->id,
                'bayar_panjar'  => $total_bayar_panjar,
                'no_panjar'     => $p->no_panjar,
                'payment_date'  => date('d/m/Y', strtotime($p->payment_date)),
                'total_panjar_number'  => $p->total_panjar,
                'total_panjar'         => number_format($p->total_panjar, 2),
                'sisa_panjar_number'   => $p->total_panjar - $total_bayar_panjar,
                'sisa_panjar'          => number_format($p->total_panjar - $total_bayar_panjar, 2)
            ]);
        }


        $panjarTBList = [];
        foreach ($panjarTBResult as $p) {
            $bayar_panjar = $localPOPaymentPanjarModel
                ->where('panjar_id', $p->id)
                ->findAll();

            $total_bayar_panjar = 0;

            foreach ($bayar_panjar as $b) {
                $total_bayar_panjar += $b['bayar_panjar'];
            }

            array_push($panjarTBList, [
                'id'            => $p->id,
                'bayar_panjar'  => $total_bayar_panjar,
                'no_panjar'     => $p->no_panjar,
                'payment_date'  => date('d/m/Y', strtotime($p->payment_date)),
                'total_panjar_number'  => $p->total_panjar,
                'total_panjar'         => number_format($p->total_panjar, 2),
                'sisa_panjar_number'   => $p->total_panjar - $total_bayar_panjar,
                'sisa_panjar'          => number_format($p->total_panjar - $total_bayar_panjar, 2)
            ]);
        }


        $pinjamanList = [];
        foreach ($pinjamanResult as $p) {
            $bayar_pinjaman = $localPOPaymentPinjamanModel
                ->where('pinjaman_id', $p->id)
                ->findAll();

            $total_bayar_pinjaman = 0;

            foreach ($bayar_pinjaman as $b) {
                $total_bayar_pinjaman += $b['bayar_pinjaman'];
            }

            array_push($pinjamanList, [
                'id'            => $p->id,
                'bayar_pinjaman'  => $total_bayar_pinjaman,
                'no_pinjaman'     => $p->no_pinjaman,
                'payment_date'  => date('d/m/Y', strtotime($p->payment_date)),
                'total_pinjaman_number'  => $p->total_pinjaman,
                'total_pinjaman'         => number_format($p->total_pinjaman, 2),
                'sisa_pinjaman_number'   => $p->total_pinjaman - $total_bayar_pinjaman,
                'sisa_pinjaman'          => number_format($p->total_pinjaman - $total_bayar_pinjaman, 2)
            ]);
        }

        $data = [
            "panjarList" =>  $panjarList,
            "pinjamList" => $pinjamanList,
            "panjarTBList" => $panjarTBList
        ];

        return response()->setJSON([
            'data' => $data,
            'status' => true
        ]);
    }

    public function getPembayaranPOLokalBB($id)
    {
        $supplierModel = new SupplierModel();
        $localPOPaymentModel = new LocalPOPaymentModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $Sub_AkunsModel = new Sub_AkunsModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();

        $id = decrypt($id);

        if ($localPOPaymentModel->where('id', $id)->first() == null) {
            return redirect()->to('pembayaran-po-lokal-bb');
        }

        $supplierList = $supplierModel->getSupplierByType("BAHAN BAKU");

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
            ->where('company_id', $this->this_company_id)
            ->findAll();

        $penerimaanData = $penerimaanBarangModel->asObject()
            ->where('deletedAt', NULL)
            ->where('company_id', $this->this_company_id)
            ->findAll();
        $bankList = $this->banksModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->orderBy('name', "ASC")
            ->findAll();

        $divisiList = $this->divisiModel->getDivisiAccess();

        $panjarPembayaranList = $localPOPaymentPanjarModel->getPembayaranPanjarDetails($id);


        $dataPembayaranPanjar = [];
        foreach ($panjarPembayaranList as $p) {
            // $total_bayar_panjar = 0;
            $total_bayar_panjar = $localPOPaymentPanjarModel->getTotalPembayaranPanjar($p->panjar_id, "BB");

            array_push($dataPembayaranPanjar, [
                'bayar_panjar'  => number_format($p->bayar_panjar, 2),
                'no_panjar'     => $p->no_panjar,
                'payment_date'  => date('d/m/Y', strtotime($p->payment_date)),
                'total_panjar'  => number_format($p->total_panjar, 2),
                'sisa_panjar'   => number_format(intval($p->total_panjar) - intval($total_bayar_panjar['total_bayar_panjar']), 2)
            ]);
        }


        $data = [
            "suppliers" => $supplierList,
            "detail" => $localPOPaymentModel->getBahanBaku($id, $this->this_company_id),
            "subsAkuns" => $subAkunsModel,
            "penerimaanData" => $penerimaanData,
            "bankList" => $bankList,
            "divisi" => $divisiList,
            "panjar" => $dataPembayaranPanjar,
        ];


        return view('Pembayaran/pembayaranPOLokal/formBahanBaku', $data);
    }
    // PRINT PEMBAYARAN PO BP
    public function pembayaranPOLokalBPPrint($id)
    {
        $id = decrypt($id);
        $dompdf = new Dompdf();

        $localPOPaymentBPModel = new LocalPOPaymentBPModel();
        $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();

        if ($localPOPaymentBPModel->where('id', $id)->first() == null) {
            return redirect()->to('pembayaran-po-lokal-bp');
        }

        $detail = $localPOPaymentBPModel->getBahanPenolong($id, $this->this_company_id);

        $data = [
            "detail" => $localPOPaymentBPModel->getBahanPenolong($id, $this->this_company_id),
            // 'tax_dipungut_negara' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dipungut oleh negara", $detail['tandaTerimaSupplier']['id']),
            // 'tax_dikembalikan_lagi' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dikembalikan lagi", $detail['tandaTerimaSupplier']['id'])
        ];

        if ($data['detail'] == null) {
            return redirect()->to('pembayaran-po-lokal-bp');
        }

        $dompdf->loadHtml(view('Pembayaran/pembayaranPOLokal/printBahanPenolong', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Pembayaran PO Lokal Bahan Penolong ", array("Attachment" => false));

        exit(0);
    }

    public function pembayaranPOLokalBBPrint($id)
    {
        $dompdf = new Dompdf();
        $localPOPaymentModel = new LocalPOPaymentModel();

        $id = decrypt($id);

        if ($localPOPaymentModel->where('id', $id)->first() == null) {
            return redirect()->to('pembayaran-po-lokal-bb');
        }

        $data = [
            "detail" => $localPOPaymentModel->getBahanBaku($id, $this->this_company_id)
        ];


        if ($data['detail'] == null) {
            return redirect()->to('pembayaran-po-lokal-bb');
        }

        $dompdf->loadHtml(view('Pembayaran/pembayaranPOLokal/printBahanBaku', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Pembayaran PO Lokal Bahan Baku ", array("Attachment" => false));

        exit(0);
    }

    // BAHAN BAKU
    public function pembayaranPOLokalBB()
    {
        return view('Pembayaran/pembayaranPOLokal/bahanBaku');
    }

    public function createPembayaranPOLokalBB()
    {
        $supplierModel = new SupplierModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $supplierList = $supplierModel->getSupplierByType("BAHAN BAKU");

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
            ->where('company_id', $this->this_company_id)
            ->findAll();

        $bankList = $this->banksModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->orderBy('name', "ASC")
            ->findAll();

        $divisiList = $this->divisiModel->getDivisiAccess();

        $data = [
            "suppliers" => $supplierList,
            "subsAkuns" => $subAkunsModel,
            "bankList" => $bankList,
            "divisi" => $divisiList
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanBaku', $data);
    }


    public function deleteBB()
    {
        $id = decrypt($this->request->getVar('id'));
        $localPOPaymentModel = new LocalPOPaymentModel();
        $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

        $localPOPaymentModel->where('id', $id)->delete();
        $localPOPaymentDetailModel->where('local_po_payment_id', $id)->delete();
        $localPOPaymentPanjarModel->where('local_po_payment_id', $id)->where('type', 'BB')->delete();
        $localPOPaymentPinjamanModel->where('local_po_payment_id', $id)->where('type', 'BB')->delete();

        return response()->setJSON([
            'message' => "Pembayaran lokal bahan baku berhasil dihapus",
            'status' => true
        ]);
    }

    public function deleteBP()
    {
        $id = decrypt($this->request->getVar('id'));
        $localPOPaymentBPModel = new LocalPOPaymentBPModel();;
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();

        $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();
        $localPOPaymentBPModel->where('id', $id)->delete();
        $localPOPaymentPanjarModel->where('local_po_payment_id', $id)->where('type', 'BP')->delete();
        $localPOPaymentPinjamanModel->where('local_po_payment_id', $id)->where('type', 'BP')->delete();

        return response()->setJSON([
            'message' => "Pembayaran lokal bahan baku berhasil dihapus",
            'status' => true
        ]);
    }

    public function getListDokumenLPBNotPaidBB()
    {
        $supplierID = $this->request->getVar('supplierID');
        $divisiID = $this->request->getVar('divisiID');
        $localPOPaymentModel = new LocalPOPaymentModel();
        if (!empty($supplierID) && !empty($divisiID)) {
            $res = $localPOPaymentModel->getListLPBNotPaid($supplierID, $divisiID, $this->this_company_id);
        } else {
            $res = [];
        }
        return response()->setJson([
            'data' => $res,
            'token' => csrf_hash()
        ]);
    }

    public function getListBarangLPBNotPaidBB()
    {
        $supplierID = $this->request->getVar('supplierID');
        $lpbID = json_decode($this->request->getVar('lpbID'));
        $month = $this->request->getVar('bulan');


        $localPOPaymentModel = new LocalPOPaymentModel();


        if (!empty($lpbID)) {
            // HARIAN
            return response()->setJson([
                'token' => csrf_hash(),
                'data' => $localPOPaymentModel->getListHarianPONotPaidByLPB(
                    $lpbID,
                    $supplierID
                ),
            ]);
        } else {
            // BULANAN (KWITANSI TB)
            return response()->setJson([
                'token' => csrf_hash(),
                'data' => $localPOPaymentModel->getListBulananPONotPaidByLPB(
                    $supplierID,
                    $month
                )
            ]);
        }
    }

    public function getListBarangLPBPaidBB()
    {
        $id = decrypt($this->request->getVar('pembayaran_id'));

        $localPOPaymentModel = new LocalPOPaymentModel();
        // var_dump($localPOPaymentModel->getListHarianPOPaidByLPB($id, $this->this_company_id));
        // die;

        return response()->setJson([
            'token' => csrf_hash(),
            'data'  => $localPOPaymentModel->getListHarianPOPaidByLPB($id, $this->this_company_id)
        ]);
    }

    public function generatePaymentNoLokalBB()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();
        $divisi = str_replace(' ', '', trim($this->request->getGet('divisiId')));
        $bank = str_replace(' ', '', trim($this->request->getGet('bankId')));

        $paymentNo = $localPOPaymentModel->get_new_no_po(
                        $divisi,
                        $bank,
                        date('m'),
                        date('Y'),
                        getLastDay(),
                        $this->this_company_id
                    );
        
        return response()->setJSON([
            'paymentNo' => $paymentNo,
            'token' => csrf_hash(),
            'success' => true,
        ]);
    }

    public function generatePaymentNoBP()
    {
        $localPOPaymentBPModel = new LocalPOPaymentBPModel();

        $paymentNo = "BP/";
        $month = date('m');
        $year = date('Y');

        $numberTemplate = $paymentNo . "$year/$month/";
        $lastData = $localPOPaymentBPModel->asObject()
            ->like('payment_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $paymentNo = "{$numberTemplate}001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->payment_no);
            $lastIncrement = (int)$exploded[3] + 1;

            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);
            $paymentNo = $numberTemplate . $paddedNumber;
        }

        return response()->setJSON([
            'paymentNo' => $paymentNo,
            'token' => csrf_hash(),
            'success' => true,

        ]);
    }


    public function generatePaymentNo()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();
        $type = $this->request->getVar('type');
        $kode = $type == "Bahan Baku" ? "BB" : "BP";

        $month = idate('m');
        $year = date('Y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "PAY/$kode/$romanMonth/$year/";

        $lastData = $localPOPaymentModel->asObject()
            ->where('type_po', $type)
            ->like('payment_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $paymentNo = "{$numberTemplate}0001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->payment_no);
            $lastIncrement = (int)$exploded[4] + 1;

            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $paymentNo = $numberTemplate . $paddedNumber;
        }

        return response()->setJSON([
            'paymentNo' => $paymentNo,
            'token' => csrf_hash(),
            'success' => true,
            'console' => $type
        ]);
    }

    public function posting()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();

        $id = decrypt($this->request->getVar('id'));
        $localPOPaymentModel->update($id, ['status_posting' => '1']);
        $result = $this->jurnalController->insertDataPembayaran($id, "LOKAL");
        // exit;

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Pembayaran berhasil diposting"
        ]);
    }
    public function unposting()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();

        $id = decrypt($this->request->getVar('id'));
        $localPOPaymentModel->update($id, ['status_posting' => '0']);
        // $result = $this->jurnalController->insertDataPembayaran($id, "LOKAL");

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Pembayaran berhasil diunposting"
        ]);
    }
}
