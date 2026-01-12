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
use App\Models\TaxModel;
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

        $supplierList = $supplierModel->getSupplier("BAHAN PENOLONG");

        $panjarSupplierList = $panjarSupplierModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->findAll();

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        $divisiList = $this->divisiModel->getDivisiAccessAllCompany();

        $bankList = $this->banksModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('name', "ASC")
            ->findAll();

        $data = [
            "suppliers" => $supplierList,
            "panjar_supplier" => $panjarSupplierList,
            "subsAkuns" => $subAkunsModel,
            "divisi" => $divisiList,
            "bankList" => $bankList,
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanPenolong', $data);
    }

    public function getTandaTerimaFaktur($supplier)
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $res = $tandaTerimaFakturModel->getListTandaTerimaFakturNotProcessed($supplier);
        return response()->setJSON([
            'data' => $res,
            'status' => true
        ]);
    }

    public function getItemListByTandaTerimaFaktur($tandaTerimaFakturID)
    {

        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();
        $localPOPaymentBPModel = new LocalPOPaymentBPModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

        $ids = array_filter(array_map('trim', explode(',', $tandaTerimaFakturID)));
        $data = []; // siapkan array penampung

        foreach ($ids as $id) {
            $detail = $tandaTerimaFakturModel->getByID($id);
            if (!empty($detail)) {
                $data[] = $detail;
            }
        }
        return $this->response->setJSON([
            'data' => $data
        ]);

    }


    public function createPembayaranPOLokalBPAction()
    {
        try {
            $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
            $localPOPaymentBPModel = new LocalPOPaymentBPModel();
            $tandaTerimaFakturModel = new TandaTerimaFakturModel();

            $pembayaranList = json_decode($this->request->getVar('pembayaranList'));

            // CHECK
            // $check = $localPOPaymentBPModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->where('payment_no',  $this->request->getVar('no_bukti_pembayaran'))->first();

            // if ($check != null) {
            //     return response()->setJSON([
            //         'token' => csrf_hash(),
            //         'message' => "No pembayaran sudah digunakan",
            //         'status' => false
            //     ]);
            // }

            $id = $localPOPaymentBPModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'bank_id' => $this->request->getVar('bank_id'),
                'payment_no' => $this->request->getVar('no_bukti_pembayaran'),
                'supplier_id' => $this->request->getVar('supplier_id'),
                'jenis_pembayaran' => $this->request->getVar('jenis_pembayaran'),
                'amount' => $this->request->getVar('total_pembayaran'),
                'payment_method' => $this->request->getVar('payment_method'),
                'keterangan' => $this->request->getVar('keterangan'),
                'supplier' => $this->request->getVar('supplier'),
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih'),
                'status_posting' => '0',
                'pembayaran_oleh'   => $this->request->getVar('pembayaran_oleh'),
                'payment_date'      => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'supplier' => $this->request->getVar('supplier'),
            ]);

            foreach ($pembayaranList as $l) {
                $localPOPaymentDetailModel->insert([
                    "local_po_payment_id"    => $id,
                    "tanda_terima_faktur_id" => $l->id,
                    "tipe"                   => "BP",
                    "total"                  => repairDouble($l->nominal_pembayaran)
                ]);

                $current = $tandaTerimaFakturModel->select('total_paid')->where('id', $l->id)->first();
        
                if (!$current) continue; // skip kalau gak ketemu

                $totalPaidLama = repairDouble($current['total_paid']) ?? 0;
                $totalBaru = $totalPaidLama + repairDouble($l->nominal_pembayaran);

                if ($totalBaru != $totalPaidLama) {
                    $tandaTerimaFakturModel->update($l->id, [
                        'total_paid' => $totalBaru
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
        $db = \Config\Database::connect();

        try {
            $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
            $localPOPaymentBPModel     = new LocalPOPaymentBPModel();
            $tandaTerimaFakturModel    = new TandaTerimaFakturModel();

            // ===============================
            // VALIDASI INPUT
            // ===============================
            if (!$this->request->getVar('id') || !$this->request->getVar('pembayaranList')) {
                throw new \Exception("Data input tidak lengkap");
            }

            $id = decrypt($this->request->getVar('id'));
            $pembayaranList = json_decode($this->request->getVar('pembayaranList'));

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Format pembayaranList tidak valid");
            }

            // ===============================
            // START TRANSACTION
            // ===============================
            $db->transBegin();

            // ===============================
            // 1️⃣ UPDATE HEADER
            // ===============================
            $updateData = [
                'payment_no'        => $this->request->getVar('no_bukti_pembayaran'),
                'company_id'        => $this->this_company_id,
                'divisi_id'         => $this->request->getVar('divisi_id'),
                'bank_id'           => $this->request->getVar('bank_id'),
                'supplier_id'       => $this->request->getVar('supplier_id'),
                'jenis_pembayaran'  => $this->request->getVar('jenis_pembayaran'),
                'amount'            => repairDouble($this->request->getVar('total_pembayaran')),
                'payment_method'    => $this->request->getVar('payment_method'),
                'keterangan'        => $this->request->getVar('keterangan'),
                'supplier'          => $this->request->getVar('supplier'),
                'akun_kas'          => $this->request->getVar('akun_kas'),
                'akun_selisih'      => $this->request->getVar('akun_selisih'),
                'status_posting'    => '0',
                'pembayaran_oleh'   => $this->request->getVar('pembayaran_oleh'),
                'payment_date'      => date(
                    'Y-m-d',
                    strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))
                ),
            ];

            if (!$localPOPaymentBPModel->update($id, $updateData)) {
                throw new \Exception("Gagal update pembayaran utama");
            }

            // ===============================
            // 2️⃣ HAPUS DETAIL LAMA (SOFT DELETE)
            // ===============================
            $localPOPaymentDetailModel
                ->where('local_po_payment_id', $id)
                ->where('tipe', 'BP')
                ->delete();

            // ===============================
            // 3️⃣ INSERT DETAIL BARU
            // ===============================
            $affectedTTF = [];

            foreach ($pembayaranList as $l) {
                $nominal = repairDouble($l->nominal_pembayaran);

                if ($nominal <= 0) continue;

                $localPOPaymentDetailModel->insert([
                    "local_po_payment_id"    => $id,
                    "tanda_terima_faktur_id" => $l->id,
                    "tipe"                   => "BP",
                    "total"                  => $nominal
                ]);

                $affectedTTF[] = $l->id;
            }

            // ===============================
            // 4️⃣ RECALCULATE total_paid (ANTI ERROR)
            // ===============================
            $affectedTTF = array_unique($affectedTTF);

            foreach ($affectedTTF as $ttfId) {

                $totalPaid = $localPOPaymentDetailModel
                    ->selectSum('total')
                    ->where('tanda_terima_faktur_id', $ttfId)
                    ->where('deletedAt', null)
                    ->first()['total'] ?? 0;

                $tandaTerimaFakturModel->update($ttfId, [
                    'total_paid' => $totalPaid
                ]);
            }

            // ===============================
            // COMMIT
            // ===============================
            if ($db->transStatus() === false) {
                throw new \Exception("Transaksi gagal");
            }

            $db->transCommit();

            return response()->setJSON([
                'message' => "Kwitansi pembayaran lokal BP berhasil diupdate",
                'status'  => true,
                'token'   => csrf_hash(),
                'id'      => encrypt($id)
            ]);

        } catch (\Exception $e) {

            $db->transRollback();

            return response()->setJSON([
                'message' => "Terjadi kesalahan: " . $e->getMessage(),
                'status'  => false,
                'token'   => csrf_hash()
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
                $total_bayar_panjar_tb += intval($p->bayar_panjar_tb);
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

            // HITUNG TOTAL DARI DETAIL PEMBAYARAN (FIX DOUBLE COUNT)
            $totalFromDetails = 0;
            $totalPPHFromDetails = 0;
            
            if (!empty($pembayaranList)) {
                foreach ($pembayaranList as $l) {
                    // Untuk CREATE mode, ambil dari sisa_tagihan atau total_po_dibayar_create
                    $totalToPay = 0;
                    
                    // Prioritas 1: dari field CREATE khusus (total_po_dibayar_create)
                    if (isset($l['total_po_dibayar_create']) && floatval($l['total_po_dibayar_create']) > 0) {
                        $totalToPay = floatval($l['total_po_dibayar_create']);
                    }
                    // Prioritas 2: dari sisa_tagihan (fallback)
                    elseif (isset($l['sisa_tagihan']) && floatval($l['sisa_tagihan']) > 0) {
                        $totalToPay = floatval($l['sisa_tagihan']);
                    }
                    // Prioritas 3: dari total_paid (jika ada)
                    elseif (isset($l['total_paid']) && floatval($l['total_paid']) > 0) {
                        $totalToPay = floatval($l['total_paid']);
                    }
                    
                    // Untuk PPH, ambil dari total_paid_pph
                    $totalPPH = isset($l['total_paid_pph']) ? floatval($l['total_paid_pph']) : 0;
                    
                    $totalFromDetails += $totalToPay;
                    $totalPPHFromDetails += $totalPPH;
                }
            }

            // CHECK
            $check = $localPOPaymentModel->where('company_id', $this->this_company_id)
                ->where('deletedAt', null)
                ->where('payment_no',  $this->request->getVar('no_bukti_pembayaran'))->first();

            if ($check != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "No pembayaran sudah digunakan",
                    'status' => false
                ]);
            }

            // VALIDASI TOTAL
            if ($totalFromDetails < 0) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Total pembayaran tidak valid",
                    'status' => false
                ]);
            }

            if ($totalFromDetails < $total_bayar_panjar) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Total pembayaran panjar tidak valid",
                    'status' => false
                ]);
            }

            $potongan = $this->request->getVar('potongan');
            $potongan = is_numeric($potongan) ? $potongan : 0;
            
            // TOTAL FINAL setelah potongan
            $totalFinalBayar = max(0, $totalFromDetails - $potongan);

            if ($bulan = $this->request->getVar("bulan")) {
                $bulan = date("F Y", strtotime($bulan));
            } else {
                $bulan = null;
            }

            $taxModel = new TaxModel();
            $akunPajakId = $taxModel->where('name', 'PPH PASAL 22')->first();

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
                'jenis_bayar'        => ucfirst($this->request->getVar('jenis_pembayaran')),
                'bulan'             => $bulan,
                'multiple_po_no'    => $resPoNo,
                'multiple_po_id'    => $resPoID,
                'pembayaran_oleh'   => $this->request->getVar('pembayaran_oleh'),
                'potongan_harga'    => $potongan,
                'amount'            => $totalFinalBayar,
                'amount_pajak'      => $totalPPHFromDetails,
                'status_posting'    => '0',
                'keterangan'        => $this->request->getVar('keterangan'),
                'akun_kas'          => $this->request->getVar('akun_kas'),
                'akun_selisih'      => $this->request->getVar('akun_selisih'),
                'akun_pajak'        =>  $akunPajakId['akun_kredit'],
            ]);

            foreach ($panjarList as $p) {
                if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                    $localPOPaymentPanjarModel->insert([
                        "company_id" => $this->this_company_id,
                        "jenis_panjar" => "PANJAR",
                        "local_po_payment_id" => $id,
                        "type"  => "BB",
                        "panjar_id" => $p->panjar_id,
                        "akun_kas" => $p->akun_kas_panjar,
                        "akun_selisih" => $p->akun_selisih_panjar,
                        "keterangan" => $p->keterangan_panjar,
                        "bayar_panjar" => $p->bayar_panjar,
                    ]);
                }
            }

            foreach ($panjarTBList as $p) {
                if ($p->bayar_panjar_tb != '') {
                    if (intval($p->bayar_panjar_tb) != 0) {
                        $localPOPaymentPanjarModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "jenis_panjar" => "PANJAR_TB",
                            "type" => "BB",
                            "panjar_id" => $p->panjar_tb_id,
                            "akun_kas" => $p->akun_kas_panjar_tb,
                            "akun_selisih" => $p->akun_selisih_panjar_tb,
                            "keterangan" => $p->keterangan_panjar_tb,
                            "bayar_panjar" => $p->bayar_panjar_tb,
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
                            "pinjaman_id" => $p->pinjaman_id,
                            "akun_kas" => $p->akun_kas_pinjaman,
                            "akun_selisih" => $p->akun_selisih_pinjaman,
                            "keterangan" => $p->keterangan_pinjaman,
                            "bayar_pinjaman" => $p->bayar_pinjaman,
                        ]);
                    }
                }
            }

            // INSERT DETAIL PEMBAYARAN (sekalian sudah hitung total di atas)
            foreach ($pembayaranList as $l) {
                // Untuk CREATE mode, ambil dari sisa_tagihan atau total_po_dibayar_create
                $totalToPay = 0;
                
                // Prioritas 1: dari field CREATE khusus (total_po_dibayar_create)
                if (isset($l['total_po_dibayar_create']) && floatval($l['total_po_dibayar_create']) > 0) {
                    $totalToPay = floatval($l['total_po_dibayar_create']);
                }
                // Prioritas 2: dari sisa_tagihan (fallback)
                elseif (isset($l['sisa_tagihan']) && floatval($l['sisa_tagihan']) > 0) {
                    $totalToPay = floatval($l['sisa_tagihan']);
                }
                // Prioritas 3: dari total_paid (jika ada)
                elseif (isset($l['total_paid']) && floatval($l['total_paid']) > 0) {
                    $totalToPay = floatval($l['total_paid']);
                }
                
                // Untuk PPH, ambil dari total_paid_pph
                $totalPPH = isset($l['total_paid_pph']) ? floatval($l['total_paid_pph']) : 0;

                $localPOPaymentDetailModel->insert([
                    'tipe' => "BB",
                    "local_po_payment_id"          => $id,
                    "rm_purchase_order_id"         => $l['rm_purchase_order_id'],
                    "rm_purchase_order_details_id" => $l['rm_purchase_order_detail_id'] ?? null,
                    "total"                        => number_format($totalToPay, 2, '.', ''),
                    "total_pay_pph"                => number_format($totalPPH, 2, '.', '')
                ]);
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
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function updatePembayaranPOLokalBBAction()
    {
        try {
            $localPOPaymentModel = new LocalPOPaymentModel();
            $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
            $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
            $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

            $id = decrypt($this->request->getVar('id'));
            $panjarList = json_decode($this->request->getVar('panjarList'));
            $pinjamanList = json_decode($this->request->getVar('pinjamanList'));
            $panjarTBList = json_decode($this->request->getVar('panjarTBList'));
            $pembayaranList = json_decode($this->request->getVar('pembayaranList'), true);

            $poIDAmt = json_decode($this->request->getVar('poIDList'));
            $poNOAmt = json_decode($this->request->getVar('poNoList'));
            $poIDArr = [];
            $poNoArr = [];

            // Hitung total panjar, panjarTB, pinjaman untuk validasi
            $total_bayar_panjar = 0;
            $total_bayar_panjar_tb = 0;
            $total_bayar_pinjaman = 0;

            foreach ($panjarList as $p) {
                if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                    $total_bayar_panjar += intval($p->bayar_panjar);
                }
            }

            foreach ($panjarTBList as $p) {
                if (isset($p->bayar_panjar_tb) && intval($p->bayar_panjar_tb) !=  0) {
                    $total_bayar_panjar_tb += intval($p->bayar_panjar_tb);
                }
            }

            foreach ($pinjamanList as $p) {
                if (isset($p->bayar_pinjaman) && intval($p->bayar_pinjaman) !=  0) {
                    $total_bayar_pinjaman += intval($p->bayar_pinjaman);
                }
            }

            foreach ($poIDAmt as $p) {
                array_push($poIDArr, $p->poID);
            }

            foreach ($poNOAmt as $p) {
                array_push($poNoArr, $p->poNo);
            }

            $resPoID =  str_replace('"', "", json_encode(array_values(array_unique($poIDArr))));
            $resPoNo =   json_encode(array_values(array_unique($poNoArr)));

            // HITUNG TOTAL DARI DETAIL PEMBAYARAN (SAMA SEPERTI CREATE)
            $totalFromDetails = 0;
            $totalPPHFromDetails = 0;
            
            $validPembayaranList = array_filter($pembayaranList, function ($item) {
                return isset($item['rm_purchase_order_id']);
            });

            if (!empty($validPembayaranList)) {
                foreach ($validPembayaranList as $l) {
                    // Untuk UPDATE mode, ambil dari total_po_dibayar_edit atau total_paid
                    $totalToPay = 0;
                    
                    // Prioritas 1: dari field EDIT khusus (total_po_dibayar_edit)
                    if (isset($l['total_po_dibayar_edit']) && floatval($l['total_po_dibayar_edit']) > 0) {
                        $totalToPay = floatval($l['total_po_dibayar_edit']);
                    }
                    // Prioritas 2: dari total_paid (data lama + update)
                    elseif (isset($l["total_paid"]) && floatval($l["total_paid"]) > 0) {
                        $totalToPay = floatval($l["total_paid"]);
                    }
                    // Prioritas 3: dari sisa_tagihan (fallback)
                    elseif (isset($l['sisa_tagihan']) && floatval($l['sisa_tagihan']) > 0) {
                        $totalToPay = floatval($l['sisa_tagihan']);
                    }
                    
                    // Untuk PPH, ambil dari total_paid_pph
                    $totalPPH = isset($l['total_paid_pph']) ? floatval($l['total_paid_pph']) : 0;
                    
                    $totalFromDetails += $totalToPay;
                    $totalPPHFromDetails += $totalPPH;
                }
            }

            $bulan = $this->request->getVar("bulan") ? date("F Y", strtotime($this->request->getVar("bulan"))) : null;

            // Cek apakah data pembayaran ada
            $payment = $localPOPaymentModel->find($id);
            if (!$payment) {
                return response()->setJSON([
                    'message' => "Data pembayaran tidak ditemukan",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            // VALIDASI TOTAL (SAMA SEPERTI CREATE)
            if ($totalFromDetails < 0) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Total pembayaran tidak valid",
                    'status' => false
                ]);
            }

            if ($totalFromDetails < $total_bayar_panjar) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Total pembayaran panjar tidak valid",
                    'status' => false
                ]);
            }

            // Update data pembayaran utama
            $taxModel = new TaxModel();
            $akunPajakId = $taxModel->where('name', 'PPH PASAL 22')->first();

            $potongan = $this->request->getVar('potongan');
            $potongan = is_numeric($potongan) ? $potongan : 0;
            
            // TOTAL FINAL setelah potongan
            $totalFinalBayar = max(0, $totalFromDetails - $potongan);

            $localPOPaymentModel->update($id, [
                'divisi_id'         => $this->request->getVar('divisi_id'),
                'supplier_id'       => $this->request->getVar('supplier_id'),
                'bank_id'           => $this->request->getVar('bank_id'),
                'payment_no'        => $this->request->getVar('no_bukti_pembayaran'),
                'payment_panjar_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_panjar_date')))),
                'payment_date'      => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'payment_method'    => $this->request->getVar('payment_method'),
                'type_bayar'        => ucfirst($this->request->getVar('tipe_pembayaran')),
                'jenis_bayar'       => ucfirst($this->request->getVar('jenis_pembayaran')),
                'bulan'             => $bulan,
                'multiple_po_no'    => $resPoNo,
                'multiple_po_id'    => $resPoID,
                'pembayaran_oleh'   => $this->request->getVar('pembayaran_oleh'),
                'potongan_harga'    => $potongan,
                'amount'            => $totalFinalBayar,
                'amount_pajak'      => $totalPPHFromDetails,
                'keterangan'        => $this->request->getVar('keterangan'),
                'akun_pajak'        =>  $akunPajakId['akun_kredit'],
                'akun_kas'          => $this->request->getVar('akun_kas'),
                'akun_selisih'      => $this->request->getVar('akun_selisih'),
            ]);

            // Hapus detail pembayaran lama sebelum insert baru
            $localPOPaymentDetailModel->where('local_po_payment_id', $id)->delete();
            
            // INSERT DETAIL PEMBAYARAN BARU
            if (!empty($validPembayaranList)) {
                foreach ($validPembayaranList as $l) {
                    // Untuk UPDATE mode, ambil dari total_po_dibayar_edit atau total_paid
                    $totalToPay = 0;
                    
                    // Prioritas 1: dari field EDIT khusus (total_po_dibayar_edit)
                    if (isset($l['total_po_dibayar_edit']) && floatval($l['total_po_dibayar_edit']) > 0) {
                        $totalToPay = floatval($l['total_po_dibayar_edit']);
                    }
                    // Prioritas 2: dari total_paid (data lama + update)
                    elseif (isset($l["total_paid"]) && floatval($l["total_paid"]) > 0) {
                        $totalToPay = floatval($l["total_paid"]);
                    }
                    // Prioritas 3: dari sisa_tagihan (fallback)
                    elseif (isset($l['sisa_tagihan']) && floatval($l['sisa_tagihan']) > 0) {
                        $totalToPay = floatval($l['sisa_tagihan']);
                    }

                    $localPOPaymentDetailModel->insert([
                        "tipe" => "BB",
                        "local_po_payment_id" => $id,
                        "rm_purchase_order_id" => $l["rm_purchase_order_id"],
                        "rm_purchase_order_details_id" => $l['rm_purchase_order_detail_id'] ?? null,
                        "total" => $totalToPay,
                        "total_pay_pph" => floatval($l["total_paid_pph"] ?? 0),
                    ]);
                }
            }

            // Update panjar (jika ada perubahan)
            foreach ($panjarList as $p) {
                if (isset($p->bayar_panjar) && intval($p->bayar_panjar) != 0) {
                    // Cek apakah sudah ada record untuk panjar ini
                    $existingPanjar = $localPOPaymentPanjarModel
                        ->where("local_po_payment_id", $id)
                        ->where("panjar_id", $p->panjar_id)
                        ->first();
                    
                    if ($existingPanjar) {
                        // Update existing
                        $localPOPaymentPanjarModel
                            ->where("local_po_payment_id", $id)
                            ->where("panjar_id", $p->panjar_id)
                            ->update([
                                "company_id" => $this->this_company_id,
                                "jenis_panjar" => "PANJAR",
                                "type" => "BB",
                                "akun_kas" => $p->akun_kas_panjar,
                                "akun_selisih" => $p->akun_selisih_panjar,
                                "keterangan" => $p->keterangan_panjar,
                                "bayar_panjar" => $p->bayar_panjar,
                            ]);
                    } else {
                        // Insert baru
                        $localPOPaymentPanjarModel->insert([
                            "company_id" => $this->this_company_id,
                            "jenis_panjar" => "PANJAR",
                            "local_po_payment_id" => $id,
                            "type" => "BB",
                            "panjar_id" => $p->panjar_id,
                            "akun_kas" => $p->akun_kas_panjar,
                            "akun_selisih" => $p->akun_selisih_panjar,
                            "keterangan" => $p->keterangan_panjar,
                            "bayar_panjar" => $p->bayar_panjar,
                        ]);
                    }
                }
            }

            // Update panjarTB (jika ada perubahan)
            foreach ($panjarTBList as $p) {
                if (isset($p->bayar_panjar_tb) && intval($p->bayar_panjar_tb) != 0) {
                    // Cek apakah sudah ada record untuk panjarTB ini
                    $existingPanjarTB = $localPOPaymentPanjarModel
                        ->where("local_po_payment_id", $id)
                        ->where("panjar_id", $p->panjar_tb_id)
                        ->where("jenis_panjar", "PANJAR_TB")
                        ->first();
                    
                    if ($existingPanjarTB) {
                        // Update existing
                        $localPOPaymentPanjarModel
                            ->where("local_po_payment_id", $id)
                            ->where("panjar_id", $p->panjar_tb_id)
                            ->where("jenis_panjar", "PANJAR_TB")
                            ->update([
                                "company_id" => $this->this_company_id,
                                "jenis_panjar" => "PANJAR_TB",
                                "type" => "BB",
                                "akun_kas" => $p->akun_kas_panjar_tb,
                                "akun_selisih" => $p->akun_selisih_panjar_tb,
                                "keterangan" => $p->keterangan_panjar_tb,
                                "bayar_panjar" => $p->bayar_panjar_tb,
                            ]);
                    } else {
                        // Insert baru
                        $localPOPaymentPanjarModel->insert([
                            "company_id" => $this->this_company_id,
                            "jenis_panjar" => "PANJAR_TB",
                            "local_po_payment_id" => $id,
                            "type" => "BB",
                            "panjar_id" => $p->panjar_tb_id,
                            "akun_kas" => $p->akun_kas_panjar_tb,
                            "akun_selisih" => $p->akun_selisih_panjar_tb,
                            "keterangan" => $p->keterangan_panjar_tb,
                            "bayar_panjar" => $p->bayar_panjar_tb,
                        ]);
                    }
                }
            }

            // Update pinjaman (jika ada perubahan)
            foreach ($pinjamanList as $p) {
                if (isset($p->bayar_pinjaman) && intval($p->bayar_pinjaman) != 0) {
                    // Cek apakah sudah ada record untuk pinjaman ini
                    $existingPinjaman = $localPOPaymentPinjamanModel
                        ->where("local_po_payment_id", $id)
                        ->where("pinjaman_id", $p->pinjaman_id)
                        ->first();
                    
                    if ($existingPinjaman) {
                        // Update existing
                        $localPOPaymentPinjamanModel
                            ->where("local_po_payment_id", $id)
                            ->where("pinjaman_id", $p->pinjaman_id)
                            ->update([
                                "company_id" => $this->this_company_id,
                                "type" => "BB",
                                "akun_kas" => $p->akun_kas_pinjaman,
                                "akun_selisih" => $p->akun_selisih_pinjaman,
                                "keterangan" => $p->keterangan_pinjaman,
                                "bayar_pinjaman" => $p->bayar_pinjaman,
                            ]);
                    } else {
                        // Insert baru
                        $localPOPaymentPinjamanModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "type" => "BB",
                            "pinjaman_id" => $p->pinjaman_id,
                            "akun_kas" => $p->akun_kas_pinjaman,
                            "akun_selisih" => $p->akun_selisih_pinjaman,
                            "keterangan" => $p->keterangan_pinjaman,
                            "bayar_pinjaman" => $p->bayar_pinjaman,
                        ]);
                    }
                }
            }

            return response()->setJSON([
                'message' => "Kwitansi pembayaran lokal bahan baku berhasil diperbarui",
                'status' => true,
                'token' => csrf_hash(),
                'id' => encrypt($id)
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "Terjadi kesalahan " . $e->getMessage() . " di baris " . $e->getLine(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }


    // PEMBAYARAN BAHAN BAKU ATAU PENOLONG
    public function allPembayaranPOLokal()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();

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
            $multiple_po_no = json_decode($data->multiple_po_no, true);
            $resultlpb = is_array($multiple_po_no) ? implode(" | ", array_unique($multiple_po_no)) : '';

            array_push($dataPembayaranPOLokal, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "divisi_id"         => encrypt($data->divisi_id),
                "payment_no"        => $data->payment_no,
                "supplier"          => $data->supplierName,
                "po_number"         => $resultlpb,
                "payment_date"      => $data->payment_date,
                "payment_method"    => strtoupper($data->payment_method),
                "amount"            => number_format($data->amount ?? 0, 0, ',', '.'),
                "total_sum_amount"  => number_format($data->total_sum_amount ?? 0, 0, ',', '.'),
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
            "dueDate" =>  $this->request->getVar("dueDate") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dueDate")))) : "",
            "paymentDate" =>  $this->request->getVar("paymentDate") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("paymentDate")))) : "",
            "statusPosting" => $this->request->getGet('status_posting'),
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
                "divisi_id"                => encrypt($p->divisi_id),
                "payment_no"        => $p->payment_no,
                "faktur_no"         => $p->faktur_no,
                "due_date"          => $p->due_date,
                "supplier"          => $p->supplierName,
                "payment_date"      => $p->payment_date,
                "payment_method"    => strtoupper($p->payment_method),
                "amount"            => $p->amount + $p->amount_pajak,
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

        $supplierList = $supplierModel->getSupplier("BAHAN PENOLONG");

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        $bankList = $this->banksModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('name', "ASC")
            ->findAll();

        $id = decrypt($id);
        $paymentDetail = $localPOPaymentBPModel->where('id', $id)->first();

        if ($paymentDetail == null) {
            return redirect()->to('pembayaran-po-lokal-bp');
        }

        $detail = $localPOPaymentBPModel->get($id);
        $divisiList = $this->divisiModel->getDivisiAccessAllCompany();


        $data = [
            "suppliers" => $supplierList,
            "detail" => $localPOPaymentBPModel->getBahanPenolong($id, $this->this_company_id),
            "subsAkuns" => $subAkunsModel,
            "divisi" => $divisiList,
            "bankList" => $bankList
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanPenolong', $data);
    }


    public function getPanjarSupplier()
    {
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

        $supplierId = $this->request->getVar('supplier_id');
        // var_dump($supplierId);
        // die;
        $pembayaranId = decrypt($this->request->getVar('pembayaran_id')); // Ambil pembayaran_id dari request

        $panjarTBResult = $this->panjarSupplierModel->getPanjarTBSupplierbySupplierId($supplierId, $this->this_company_id);
        $pinjamanResult = $this->pinjamanSupplierModel->getPinjamanSupplierbySupplierId($supplierId, $this->this_company_id);
        $panjarResult = $this->panjarSupplierModel->getPanjarSupplierbySupplierId($supplierId, $this->this_company_id);

        // Inisialisasi array untuk menyimpan data panjar
        $panjarList = [];
        foreach ($panjarResult as $p) {
            $bayar_panjar = $localPOPaymentPanjarModel
                ->where('panjar_id', $p->id)
                ->findAll();

            $total_bayar_panjar = 0;

            // Hanya hitung total_bayar_panjar jika pembayaran_id ada

            foreach ($bayar_panjar as $b) {
                $total_bayar_panjar += $b['bayar_panjar'];
            }

            $panjarList[$p->id] = [
                'pembayaran_id'  => "NULL",
                'panjar_id'     => $p->id,
                'bayar_panjar'  => 0,
                'no_panjar'     => $p->no_panjar,
                'payment_date'  => date('d/m/Y', strtotime($p->payment_date)),
                'total_panjar_number'  => $p->total_panjar,
                'total_panjar'         => number_format($p->total_panjar, 2),
                'sisa_panjar_number'   => $p->total_panjar - $total_bayar_panjar,
                'sisa_panjar'          => number_format($p->total_panjar - $total_bayar_panjar, 2)
            ];
        }

        // Jika ada pembayaranId, tambahkan data yang sudah ada pembayaran
        if (!empty($pembayaranId)) {
            $panjarListWithPayment = $localPOPaymentPanjarModel
                ->select('local_po_payment_panjar.*, panjar_supplier.id as panjar_id, panjar_supplier.payment_date, panjar_supplier.no_panjar, panjar_supplier.total_panjar, local_po_payments.potongan_harga, 
                kas_akun.nama_sub AS akun_kas_name, selisih_akun.nama_sub AS akun_selisih_name,  kas_akun.id AS akun_kas_id,  selisih_akun.id AS akun_selisih_id')
                ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
                ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
                ->join('sub_akuns AS kas_akun', 'local_po_payment_panjar.akun_kas = kas_akun.id', 'left')
                ->join('sub_akuns AS selisih_akun', 'local_po_payment_panjar.akun_selisih = selisih_akun.id', 'left')
                ->where('panjar_supplier.jenis_panjar', "PANJAR")
                ->where('local_po_payments.deletedAt', null)
                ->where('local_po_payments.id', $pembayaranId)
                ->where('type', 'BB')
                ->findAll();

            foreach ($panjarListWithPayment as $p) {

                // Jika ada pembayaran, pastikan data awal dengan panjar_id ini dihapus
                unset($panjarList[$p['panjar_id']]);

                $panjarList[$p['panjar_id']] = [
                    'pembayaran_id'       => $pembayaranId,
                    'id'                  => $p['id'],
                    'panjar_id'           => $p['panjar_id'],
                    'bayar_panjar'        => intval($p['bayar_panjar']),
                    'no_panjar'           => $p['no_panjar'],
                    'payment_date'        => date('d/m/Y', strtotime($p['payment_date'])),
                    'total_panjar_number' => intval($p['total_panjar']),
                    'total_panjar'        => number_format($p['total_panjar'], 2),
                    'sisa_panjar_number'  => intval($p['total_panjar']) - $localPOPaymentPanjarModel->getTotalPembayaranPanjar($p['panjar_id'], "BB")['total_bayar_panjar'],
                    'sisa_panjar'         => number_format(intval($p['total_panjar']) - $localPOPaymentPanjarModel->getTotalPembayaranPanjar($p['panjar_id'], "BB")['total_bayar_panjar'], 2),
                    'akun_kas_name'       => $p['akun_kas_name'],
                    'akun_selisih_name'   => $p['akun_selisih_name'],
                    'akun_kas_id'       => $p['akun_kas_id'],
                    'akun_selisih_id'       => $p['akun_selisih_id']
                ];
            }
        }

        // Konversi kembali ke array index numerik
        $panjarList = array_values($panjarList);

        // Proses yang sama untuk panjar TB dan pinjaman
        $panjarTBList = [];
        foreach ($panjarTBResult as $p) {
            $bayar_panjar = $localPOPaymentPanjarModel
                ->where('panjar_id', $p->id)
                ->findAll();

            $total_bayar_panjar = 0;

            // Hanya hitung total_bayar_panjar jika pembayaran_id ada
            foreach ($bayar_panjar as $b) {
                $total_bayar_panjar += $b['bayar_panjar'];
            }


            $panjarTBList[$p->id] = [
                'pembayaran_id'  => "NULL",
                'panjar_tb_id'  => $p->id,
                'bayar_panjar_tb'  => 0,
                'no_panjar_tb'     => $p->no_panjar,
                'payment_date'  => date('d/m/Y', strtotime($p->payment_date)),
                'total_panjar_tb_number'  => $p->total_panjar,
                'total_panjar_tb'         => number_format($p->total_panjar, 2),
                'sisa_panjar_tb_number'   => $p->total_panjar - $total_bayar_panjar,
                'sisa_panjar_tb'          => number_format($p->total_panjar - $total_bayar_panjar, 2)
            ];
        }

        if (!empty($pembayaranId)) {

            $panjarTBListWithPayment = $localPOPaymentPanjarModel
                ->select('local_po_payment_panjar.*, panjar_supplier.id as panjar_id, panjar_supplier.no_panjar, panjar_supplier.payment_date, panjar_supplier.total_panjar, local_po_payments.potongan_harga, 
                    kas_akun.nama_sub AS akun_kas_name, selisih_akun.nama_sub AS akun_selisih_name, kas_akun.id AS akun_kas_id,  selisih_akun.id AS akun_selisih_id')
                ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
                ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
                ->join('sub_akuns AS kas_akun', 'local_po_payment_panjar.akun_kas = kas_akun.id', 'left')
                ->join('sub_akuns AS selisih_akun', 'local_po_payment_panjar.akun_selisih = selisih_akun.id', 'left')
                ->where('panjar_supplier.jenis_panjar', "PANJAR_TB")
                ->where('local_po_payments.deletedAt', null)
                ->where('local_po_payments.id', $pembayaranId)
                ->where('type', 'BB')
                ->findAll();

            foreach ($panjarTBListWithPayment as $p) {

                // Jika ada pembayaran, pastikan data awal dengan panjar_id ini dihapus
                unset($panjarTBList[$p['panjar_id']]);

                $panjarTBList[$p['panjar_id']] = [
                    'pembayaran_id' => $pembayaranId,
                    'id'                  => $p['id'],
                    'panjar_tb_id'           => $p['panjar_id'],
                    'bayar_panjar_tb'        => intval($p['bayar_panjar']),
                    'no_panjar_tb'           => $p['no_panjar'],
                    'payment_date'        => date('d/m/Y', strtotime($p['payment_date'])),
                    'total_panjar_tb_number' => intval($p['total_panjar']),
                    'total_panjar_tb'        => number_format($p['total_panjar'], 2),
                    'sisa_panjar_tb_number'  => intval($p['total_panjar']) - $localPOPaymentPanjarModel->getTotalPembayaranPanjar($p['panjar_id'], "BB")['total_bayar_panjar'],
                    'sisa_panjar_tb'         => number_format(intval($p['total_panjar']) - $localPOPaymentPanjarModel->getTotalPembayaranPanjar($p['panjar_id'], "BB")['total_bayar_panjar'], 2),
                    'akun_kas_name'       => $p['akun_kas_name'],
                    'akun_selisih_name'   => $p['akun_selisih_name'],
                    'akun_kas_id'       => $p['akun_kas_id'],
                    'akun_selisih_id'       => $p['akun_selisih_id']
                ];
            }
        }

        $panjarTBList = array_values($panjarTBList);

        $pinjamanList = [];
        foreach ($pinjamanResult as $p) {
            $bayar_pinjaman = $localPOPaymentPinjamanModel
                ->where('pinjaman_id', $p->id)
                ->findAll();

            $total_bayar_pinjaman = 0;

            // Hanya hitung total_bayar_pinjaman jika pembayaran_id ada

            foreach ($bayar_pinjaman as $b) {
                $total_bayar_pinjaman += $b['bayar_pinjaman'];
            }

            $pinjamanList[$p->id] = [
                'pembayaran_id'   => "NULL",
                'pinjaman_id'     => $p->id,
                'bayar_pinjaman'  => 0,
                'no_pinjaman'     => $p->no_pinjaman,
                'payment_date'  => date('d/m/Y', strtotime($p->payment_date)),
                'total_pinjaman_number'  => $p->total_pinjaman,
                'total_pinjaman'         => number_format($p->total_pinjaman, 2),
                'sisa_pinjaman_number'   => $p->total_pinjaman - $total_bayar_pinjaman,
                'sisa_pinjaman'          => number_format($p->total_pinjaman - $total_bayar_pinjaman, 2)
            ];
        }

        if (!empty($pembayaranId)) {
            $pinjamanListWithPayment = $localPOPaymentPinjamanModel
                ->select('local_po_payment_pinjaman.*, pinjaman_supplier.id as pinjaman_id, pinjaman_supplier.payment_date, pinjaman_supplier.no_pinjaman, pinjaman_supplier.total_pinjaman, local_po_payments.potongan_harga, 
                    kas_akun.nama_sub AS akun_kas_name, selisih_akun.nama_sub AS akun_selisih_name, kas_akun.id AS akun_kas_id,  selisih_akun.id AS akun_selisih_id')
                ->join("local_po_payments", 'local_po_payment_pinjaman.local_po_payment_id = local_po_payments.id')
                ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id')
                ->join('sub_akuns AS kas_akun', 'local_po_payment_pinjaman.akun_kas = kas_akun.id', 'left')
                ->join('sub_akuns AS selisih_akun', 'local_po_payment_pinjaman.akun_selisih = selisih_akun.id', 'left')
                ->where('local_po_payments.deletedAt', null)
                ->where('local_po_payments.id', $pembayaranId)
                ->where('type', 'BB')
                ->findAll();

            foreach ($pinjamanListWithPayment as $p) {

                // Jika ada pembayaran, pastikan data awal dengan panjar_id ini dihapus
                unset($pinjamanList[$p['pinjaman_id']]);

                $pinjamanList[$p['pinjaman_id']] = [
                    'pembayaran_id'  => $pembayaranId,
                    'id'                  => $p['id'],
                    'pinjaman_id'                  => $p['pinjaman_id'],
                    'bayar_pinjaman'      => intval($p['bayar_pinjaman']),
                    'no_pinjaman'         => $p['no_pinjaman'],
                    'payment_date'        => date('d/m/Y', strtotime($p['payment_date'])),
                    'total_pinjaman_number' => intval($p['total_pinjaman']),
                    'total_pinjaman'      => number_format($p['total_pinjaman'], 2),
                    'sisa_pinjaman_number' => intval($p['total_pinjaman']) - $localPOPaymentPinjamanModel->getTotalPembayaranPinjaman($p['pinjaman_id'], "BB")['total_bayar_pinjaman'],
                    'sisa_pinjaman'       => number_format(intval($p['total_pinjaman']) - $localPOPaymentPinjamanModel->getTotalPembayaranPinjaman($p['pinjaman_id'], "BB")['total_bayar_pinjaman'], 2),
                    'akun_kas_name'       => $p['akun_kas_name'],
                    'akun_selisih_name'   => $p['akun_selisih_name'],
                    'akun_kas_id'       => $p['akun_kas_id'],
                    'akun_selisih_id'       => $p['akun_selisih_id']
                ];
            }
        }

        $pinjamanList = array_values($pinjamanList);

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
            ->where('deletedAt', null)
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
            "detail" => $localPOPaymentModel->getLocalPOPayment($id, $this->this_company_id),
            "subsAkuns" => $subAkunsModel,
            "penerimaanData" => $penerimaanData,
            "bankList" => $bankList,
            "divisi" => $divisiList,
            "panjar" => $dataPembayaranPanjar,
        ];

    //    var_dump($data['detail']['pembayaranDetail']['bulan']);
    //    die;

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
        $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();

        $id = decrypt($id);

        $parentData = $localPOPaymentModel
            ->where('local_po_payments.id', $id)
            ->where('local_po_payments.deletedAt', null)
            ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id', 'left')
            ->select('local_po_payments.*, suppliers.name as supplier_name')
            ->first();

        $childData = $localPOPaymentDetailModel
            ->select("
                local_po_payment_details.*,
                rm_purchase_orders.po_date,
                rm_purchase_orders.po_no,
                rm_purchase_orders.id AS rm_purchase_order_id,
                rm_purchase_orders.total AS total_dibayar,
                barang_master.barang_name AS barang,
                barang_master_spesifikasi.spesifikasi AS spek,
                COALESCE(SUM(rm_purchase_order_details.qty_diterima), 0) AS total_qty_diterima,
            ")
            ->where('local_po_payment_details.local_po_payment_id', $id)
            ->where('local_po_payment_details.deletedAt', null)
            ->join('rm_purchase_orders', 'local_po_payment_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'rm_purchase_order_details.barang1_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'rm_purchase_order_details.barang2_id = barang_master_spesifikasi.id', 'left')
            ->groupBy('rm_purchase_orders.id') // Group by PO untuk aggregasi SUM
            ->findAll();

        $data = [
            'company' => session()->get("login")->arr_company[0]['company'],
            "parentData" => $parentData,
            "childData" => $childData
        ];


        if ($data['parentData'] == null) {
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
            ->where('deletedAt', null)
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

        if (!$id) {
            return response()->setJSON([
                'message' => 'ID tidak valid',
                'status' => false
            ]);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $localPOPaymentBPModel       = new LocalPOPaymentBPModel();
            $localPOPaymentDetailModel   = new LocalPOPaymentDetailModel(); // pastikan model ini benar
            $tandaTerimaFakturModel     = new TandaTerimaFakturModel();

            // Ambil semua detail yang terkait dengan payment ini
            $details = $localPOPaymentDetailModel
                ->where('local_po_payment_id', $id)
                ->findAll();

            // Untuk tiap detail: kurangi total_paid di tanda_terima_faktur
            if (!empty($details)) {
                foreach ($details as $d) {
                    $fakturId = $d['tanda_terima_faktur_id'] ?? $d->tanda_terima_faktur_id ?? null;
                    $amount = repairDouble($d['total'] ?? $d->total ?? 0);

                    if (!$fakturId) continue;

                    // ambil current total_paid
                    $current = $tandaTerimaFakturModel->select('total_paid')->find($fakturId);
                    $currentTotal = (float) (repairDouble($current['total_paid'] ?? $current->total_paid ?? 0) ?: 0);

                    // hitung total baru (jangan sampai negatif)
                    $totalBaru = $currentTotal - (float) $amount;
                    if ($totalBaru < 0) $totalBaru = 0;

                    // pakai set+where supaya tidak error "There is no data to update"
                    $tandaTerimaFakturModel
                        ->set('total_paid', $totalBaru)
                        ->where('id', $fakturId)
                        ->update();
                }
            }

            // Hapus detail pembayaran
            $localPOPaymentDetailModel->where('local_po_payment_id', $id)->delete();

            // Hapus header pembayaran (BP)
            $localPOPaymentBPModel->delete($id);

            // Commit transaction
            if ($db->transStatus() === false) {
                $db->transRollback();
                return response()->setJSON([
                    'message' => 'Gagal menghapus pembayaran (transaksi gagal).',
                    'status' => false
                ]);
            }

            $db->transCommit();
            return response()->setJSON([
                'message' => "Pembayaran lokal bahan baku berhasil dihapus",
                'status' => true
            ]);
        } catch (\Throwable $e) {
            // rollback & return error
            $db->transRollback();
            log_message('error', 'deleteBP error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->setJSON([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'status' => false
            ]);
        }
    }


    public function getListDokumenLPBNotPaidBB()
    {
        $poSelected = $this->request->getVar("selectedPo");
        $supplierID = $this->request->getVar('supplierID');
        $divisiID = $this->request->getVar('divisiID');
        $localPOPaymentModel = new LocalPOPaymentModel();
        if (!empty($supplierID) && !empty($divisiID)) {
            $res = $localPOPaymentModel->getListPONotPaid($poSelected, $supplierID, $divisiID, $this->this_company_id);
        } else {
            $res = [];
        }
        return response()->setJson([
            'data' => $res,
            'token' => csrf_hash()
        ]);
    }


    public function getListDokumenPoNotPaidBB()
    {
        $poSelected = $this->request->getVar("selectedPo");
        $supplierID = $this->request->getVar('supplierID');
        $divisiID = $this->request->getVar('divisiID');
        $tipe_pembayaran = $this->request->getVar('tipe_pembayaran');
        $localPOPaymentModel = new LocalPOPaymentModel();
        if ($tipe_pembayaran == 'HARIAN' && !empty($supplierID) && !empty($divisiID)) {
            $res = $localPOPaymentModel->getListPONotPaid($poSelected, $supplierID, $divisiID, $this->this_company_id);
        } elseif ($tipe_pembayaran == 'BULANAN_PER_PO' && !empty($supplierID)) {
            $res = $localPOPaymentModel->getListPONotPaidTB($poSelected, $supplierID, $divisiID, $this->this_company_id);
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
        $poID = json_decode($this->request->getVar('poID'));
        $month = $this->request->getVar('bulan');


        $localPOPaymentModel = new LocalPOPaymentModel();


        if (!empty($poID)) {
            // HARIAN
            return response()->setJson([
                'token' => csrf_hash(),
                'data' => $localPOPaymentModel->getListHarianPONotPaidByLPB(
                    $poID,
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

    public function getListBarangPoNotPaidBB()
    {
        $supplierID = $this->request->getVar('supplierID');
        $poID = json_decode($this->request->getVar('poID'));
        $monthInput = $this->request->getVar('bulan'); // contoh: 09/2025
        $tipeBayar = $this->request->getVar('tipeBayar');
        $month = null;

        if (!empty($monthInput)) {
            // ubah dari mm/yyyy → yyyy-mm
            $parts = explode('/', $monthInput);
            if (count($parts) === 2) {
                $month = $parts[1] . '-' . $parts[0]; // hasil: 2025-09
            }
        }
    

        $localPOPaymentModel = new LocalPOPaymentModel();


        if ($tipeBayar == 'HARIAN' && !empty($poID)) {
            // HARIAN
            $data = $localPOPaymentModel->getListHarianPONotPaid($poID, $supplierID);
            foreach ($data as &$row) {
                $row['group_key'] = $row['rm_purchase_order_id']; // Harian group by PO
            }
            return response()->setJson([
                'token' => csrf_hash(),
                'data' => $data,
            ]);
        } elseif ($tipeBayar == 'BULANAN_PER_PO' && !empty($supplierID)) {
            // BULANAN PER PO
            $data = $localPOPaymentModel->getListBulananPONotPaidTB($poID, $supplierID);
            foreach ($data as &$row) {
                $row['group_key'] = $row['rm_purchase_order_id']; // Bulanan per PO group by PO
            }

            return response()->setJson([
                'token' => csrf_hash(),
                'data' => $data,
            ]);
        } else {
            // BULANAN
            $data = $localPOPaymentModel->getListBulananPONotPaidByLPBNew($supplierID, $month);
            foreach ($data as &$row) {
                $row['group_key'] = $row['rm_purchase_order_detail_id']; // Bulanan group by detail
            }
            return response()->setJson([
                'token' => csrf_hash(),
                'data' => $data,
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
        $jenis = $this->request->getGet('jenisPembayaran');
        $metodePembayaran = $this->request->getGet('paymentMethod');
        $divisi = str_replace(' ', '', trim($this->request->getGet('divisiId')));
        $divisiId = $this->request->getGet('divisiIdInt');
        $bank = str_replace(' ', '', trim($this->request->getGet('bankId')));
        $tanggalPembayaran = $this->request->getGet('tanggalPembayaran');
        $noTransaksi = $this->request->getGet('noTransaksi');
        $id = decrypt($this->request->getGet('id'));
        $payData = $localPOPaymentModel->where('id', $id)->where('deletedAt', NULL)->first();
        

        $paymentNo = $localPOPaymentModel->get_new_no(
            $id,
            $jenis,
            $divisi,
            $metodePembayaran,
            $bank,
            date('m'),
            date('Y'),
            getLastDay(),
            $this->this_company_id,
            $tanggalPembayaran,
            $divisiId ?? null,
            $payData['payment_no'] ?? null,
            $payData['divisi_id'] ?? null,
        );

        return response()->setJSON([
            'paymentNo' => $paymentNo,
            'token' => csrf_hash(),
            'success' => true,
        ]);
    }

    // public function generatePaymentNoBP()
    // {
    //     $localPOPaymentBPModel = new LocalPOPaymentBPModel();

    //     $paymentNo = "BP/";
    //     $month = date('m');
    //     $year = date('Y');

    //     $numberTemplate = $paymentNo . "$year/$month/";
    //     $lastData = $localPOPaymentBPModel->asObject()
    //         ->like('payment_no', $numberTemplate, 'after')
    //         ->orderBy('createdAt', 'DESC')
    //         ->first();

    //     $paymentNo = "{$numberTemplate}001";

    //     if (!empty($lastData)) {
    //         $exploded = explode('/', $lastData->payment_no);
    //         $lastIncrement = (int)$exploded[3] + 1;

    //         $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);
    //         $paymentNo = $numberTemplate . $paddedNumber;
    //     }

    //     return response()->setJSON([
    //         'paymentNo' => $paymentNo,
    //         'token' => csrf_hash(),
    //         'success' => true,

    //     ]);
    // }

    public function generatePaymentNoBPNew()
    {   
        $localPOPaymentBPModel = new LocalPOPaymentBPModel();
        $jenis = $this->request->getvar('jenisPembayaran');
        $paymentMethod = $this->request->getvar('paymentMethod');
        $divisi = str_replace(' ', '', trim($this->request->getvar('divisiId')));
        $divisiId = $this->request->getGet('divisiIdInt');
        $bank = str_replace(' ', '', trim($this->request->getvar('bankId')));
        $tanggalPembayaran = $this->request->getGet('tanggalPembayaran');
        $noTransaksi = $this->request->getGet('noTransaksi');
        $id = decrypt($this->request->getGet('id'));
        $payData = $localPOPaymentBPModel->where('id', $id)->where('deletedAt', NULL)->first();

        $paymentNo = $localPOPaymentBPModel->get_new_no(
            $id,
            $jenis,
            $divisi,
            $paymentMethod,
            $bank,
            date('m'),
            date('Y'),
            getLastDay(),
            $this->this_company_id,
            $tanggalPembayaran,
            $divisiId ?? null,
            $payData['payment_no'] ?? null,
            $payData['divisi_id'] ?? null,
        );

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

    public function postingPoLokalBB()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $divisiId = decrypt($this->request->getVar('divisi_id'));
            $currentStatus = $this->request->getVar('status');

            $localPOPaymentModel = new LocalPOPaymentModel();
            $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
            $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

            // Update main status
            $localPOPaymentModel->update($id, ['status_posting' => $currentStatus]);

            // Get related panjar/pinjaman
            $dataPayPanjar = $localPOPaymentPanjarModel->where('local_po_payment_id', $id)
                                ->select('id')
                                ->findAll() ?: [];
            
            $dataPayPinjaman = $localPOPaymentPinjamanModel->where('local_po_payment_id', $id)
                                ->select('id')
                                ->findAll() ?: [];

            if ($currentStatus == 1) {
                // POSTING LOGIC
                foreach ($dataPayPanjar as $payPanjar) {
                    if (!empty($payPanjar['id'])) {
                        $result = $this->jurnalController->inserDataPembayaranPanjar($payPanjar['id'], "PANJAR", $divisiId);
                    }
                }
                
                foreach ($dataPayPinjaman as $payPinjaman) {
                    if (!empty($payPinjaman['id'])) {
                        $result = $this->jurnalController->inserDataPembayaranPinjaman($payPinjaman['id'], "PINJAMAN", $divisiId);
                    }
                }
                
                $result = $this->jurnalController->insertDataPembayaran($id, "LOKAL BB", $divisiId);
                $finalResult = $result;
                $message = "Pembayaran berhasil diposting";
            } else {
                // UNPOSTING LOGIC
                foreach ($dataPayPanjar as $payPanjar) {
                    if (!empty($payPanjar['id'])) {
                        $result = $this->jurnalController->unpostDataPembayaranPanjar($payPanjar['id'], "PANJAR", $divisiId);
                    }
                }
                
                foreach ($dataPayPinjaman as $payPinjaman) {
                    if (!empty($payPinjaman['id'])) {
                        $result = $this->jurnalController->unpostDataPembayaranPinjaman($payPinjaman['id'], "PINJAMAN", $divisiId);
                    }
                }
                
                $result = $this->jurnalController->unpostDataPembayaran($id, "LOKAL BB", $divisiId);
                $finalResult = $result;
                $message = "Pembayaran berhasil diunpost";
            }

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => $message,
                'new_status' => $currentStatus
            ]);

        } catch (\Exception $e) {
            
            log_message('error', 'Error in postingPoLokalBB: ' . $e->getMessage());
            
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal memproses: " . $e->getMessage()
            ]);
        }
    }

    public function postingPoLokalBP()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $currentStatus = $this->request->getVar('status');

            $localPoPaymentBpModel = new LocalPOPaymentBPModel();
            
            // Update main status
            $localPoPaymentBpModel->update($id, ['status_posting' => $currentStatus]);

            if ($currentStatus == 1) {
                // POSTING LOGIC
                $this->jurnalController->insertDataPembayaran($id, "LOKAL BP", '');
                $message = "Pembayaran berhasil diposting";
            } else {
                // UNPOSTING LOGIC
                $this->jurnalController->unpostDataPembayaran($id, "LOKAL BP", '');
                $message = "Pembayaran berhasil diunpost";
            }


            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => $message,
                'new_status' => $currentStatus
            ]);

        } catch (\Exception $e) {
            // Rollback status on error
            if (isset($localPoPaymentBpModel)) {
                $localPoPaymentBpModel->update($id, ['status_posting' => ($currentStatus == 1) ? 0 : 1]);
            }
            
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal memproses: " . $e->getMessage()
            ]);
        }
    }
    
}
