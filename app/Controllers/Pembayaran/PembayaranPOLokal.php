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
use App\Models\PanjarSupplierModel;
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


    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->jurnalController = new JurnalUmum();
        $this->banksModel = new BanksModel();
        $this->divisiModel = new DivisisModel();
        $this->panjarSupplierModel = new PanjarSupplierModel();
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

        $supplierList = $supplierModel->asObject()
            ->where('deletedAt', null)
            ->where('type', "BAHAN PENOLONG")
            ->orderBy('name', "ASC")
            ->findAll();

        $panjarSupplierList = $panjarSupplierModel->asObject()
            ->findAll();

        $subAkunsModel = $Sub_AkunsModel->asObject()
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
            $localPOPaymentBPModel = new LocalPOPaymentBPModel();
            $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();

            $panjarList = json_decode($this->request->getVar('panjarList'));

            $total_bayar_panjar = 0;
            foreach ($panjarList as $p) {
                $total_bayar_panjar += intval($p->bayar_panjar);
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
                        $insertPanjar = $localPOPaymentPanjarModel->insert([
                            "company_id" => $this->this_company_id,
                            "local_po_payment_id" => $id,
                            "type" => "BP",
                            "panjar_id" => $p->id,
                            "bayar_panjar" => $p->bayar_panjar

                        ]);
                    }
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
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    public function updatePembayaranPOLokalBPAction()
    {
        try {


            $localPOPaymentBPModel = new LocalPOPaymentBPModel();
            $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
            $id = decrypt($this->request->getVar('id'));

            $panjarList = json_decode($this->request->getVar('panjarList'));

            $total_bayar_panjar = 0;
            foreach ($panjarList as $p) {
                $total_bayar_panjar += intval($p->bayar_panjar);
            }

            $localPOPaymentBPModel->update($id, [

                'amount' => repairDouble($this->request->getVar('nominal_pembayaran')),
            ]);

            foreach ($panjarList as $p) {
                if ($p->bayar_panjar != '') {

                    if (intval($p->bayar_panjar) != 0) {
                        $insertPanjar = $localPOPaymentPanjarModel->update($p->id, [

                            "bayar_panjar" => intval($p->bayar_panjar)

                        ]);
                    }
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

            $localPOPaymentModel = new LocalPOPaymentModel();
            $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
            $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();

            $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

            $panjarList = json_decode($this->request->getVar('panjarList'));
            $pembayaranList = json_decode($this->request->getVar('pembayaranList'));

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

                'payment_date'      => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'payment_method'    => $this->request->getVar('payment_method'),

                'type_bayar'        => ucfirst($this->request->getVar('tipe_pembayaran')),
                'bulan'             => $bulan,

                'multiple_lpb_no'   => $resPoNo,
                'multiple_lpb_id'   => $resPoID,

                'pembayaran_oleh'   => $this->request->getVar('pembayaran_oleh'),
                'potongan_harga'    => $this->request->getVar('potongan'),


                'amount' => $this->request->getVar('grand_total'),
                'status_posting' => '0',

                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih')

            ]);

            foreach ($panjarList as $p) {

                if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                    $insertPanjar = $localPOPaymentPanjarModel->insert([
                        "company_id" => $this->this_company_id,
                        "local_po_payment_id" => $id,
                        "type"  => "BB",
                        "panjar_id" => $p->id,
                        "bayar_panjar" => $p->bayar_panjar
                    ]);
                }
            }




            foreach ($pembayaranList as $l) {
                if (intval($l->pembayaran_user_input != 0) && isset($l->pembayaran_user_input)) {

                    $insertLocalPoPaymentDetail = $localPOPaymentDetailModel->insert([
                        "local_po_payment_id"           => $id,
                        "penerimaan_barang_id"          => intval($l->penerimaan_barang_id),
                        "penerimaan_barang_detail_id"   => intval($l->penerimaan_barang_detail_id),
                        "rm_purchase_order_id"          => intval($l->rm_purchase_orders_id),
                        "rm_purchase_order_details_id"  => intval($l->rm_purchase_order_details_id),
                        "total"                         => intval($l->pembayaran_user_input)
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

        $id = decrypt($this->request->getVar('id'));
        $panjarList = json_decode($this->request->getVar('panjarList'));
        $pembayaranList = json_decode($this->request->getVar('pembayaranList'));


        $total_bayar_panjar = 0;
        foreach ($panjarList as $p) {
            if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                $total_bayar_panjar += intval($p->bayar_panjar);
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

        if ($total_pembayaran < $total_bayar_panjar) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "total pembayaran panjar tidak valid",
                'status' => false
            ]);
        }

        $localPOPaymentModel->update($id, [
            'amount' => $this->request->getVar('grand_total'),
        ]);

        foreach ($panjarList as $p) {
            if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                $insertPanjar = $localPOPaymentPanjarModel->update($p->id, [
                    "bayar_panjar" => $p->bayar_panjar
                ]);
            }
        }

        foreach ($pembayaranList as $l) {
            if (intval($l->pembayaran_user_input != 0) && isset($l->pembayaran_user_input)) {
                $insertLocalPoPaymentDetail = $localPOPaymentDetailModel->update($l->local_po_payment_details_id, [
                    "total" => intval($l->pembayaran_user_input)
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
                "amount"            => number_format($p->amount ?? 0, 0, ',', '.'),
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

        $supplierList = $supplierModel->asObject()
            ->where('deletedAt', null)
            ->where('type', "BAHAN PENOLONG")
            ->orderBy('name', "ASC")
            ->findAll();


        $subAkunsModel = $Sub_AkunsModel->asObject()
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

        $supplierId = $this->request->getVar('supplier_id');
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


        return response()->setJSON([
            'data' => $panjarList,
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

        $supplierList = $supplierModel->asObject()
            ->where('deletedAt', null)
            ->where('type', "BAHAN BAKU")
            ->orderBy('name', "ASC")
            ->findAll();


        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
            ->findAll();

        $penerimaanData = $penerimaanBarangModel->asObject()->where('deletedAt', NULL)->findAll();
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

        $supplierList = $supplierModel->asObject()
            ->where('deletedAt', null)
            ->where('type', "BAHAN BAKU")
            ->orderBy('name', "ASC")
            ->findAll();

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
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


    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $localPOPaymentModel = new LocalPOPaymentModel();

        $localPOPaymentModel->where('id', $id)->delete();

        return response()->setJSON([
            'message' => "Pembayaran lokal bahan penolong berhasil dihapus",
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


        $bankID = $this->request->getVar('bank_id');
        $paymentDate = $this->request->getVar('payment_date');
        $bank = $this->banksModel->find($bankID);

        $paymentNo = "";

        if (!empty($paymentDate)) {
            $paymentDataFormated = date_format(date_create_from_format("d/m/Y", $paymentDate), "Y-m-d");
            $month = date('m', strtotime($paymentDataFormated));
            $year = date('Y', strtotime($paymentDataFormated));

            $numberTemplate = $bank['kode_bank'] . "/$year/$month/";

            $lastData = $localPOPaymentModel->asObject()

                ->where('bank_id', $bank['id'])
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
        } else {
            $paymentNo = "";
        }

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
        // $result = $this->jurnalController->insertDataPembayaran($id, "LOKAL");

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
