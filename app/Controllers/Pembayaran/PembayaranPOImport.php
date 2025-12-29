<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\ImportPOPaymentModel;
use App\Models\SupplierModel;
use App\Models\RMImportPOModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\KursModel;
use App\Models\MetadataModel;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\ImportPOPaymentDetailModel;
use App\Models\LocalPOPaymentPanjarModel;
use Dompdf\Dompdf;
use App\Models\BanksModel;

class PembayaranPOImport extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $supplierModel;
    protected $importPOPaymentModel;
    protected $importPOPaymentDetailModel;
    protected $kursModel;
    protected $rmImportPOModel; // Import BB
    protected $amPurchaseOrderModel; // Import BP - Lokal BP
    protected $metaDataModel;
    protected $jurnalController;
    protected $divisiModel;
    protected $companyModel;
    protected $localPOPaymentPanjarModel;
    protected $dompdf;
    protected $banksModel;


    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
        $this->importPOPaymentModel = new ImportPOPaymentModel();
        $this->importPOPaymentDetailModel = new ImportPOPaymentDetailModel();
        $this->kursModel = new KursModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->metaDataModel = new MetadataModel();
        $this->jurnalController = new JurnalUmum();
        $this->divisiModel = new DivisisModel();
        $this->companyModel = new CompaniesModel();
        $this->localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $this->dompdf = new Dompdf();
        $this->banksModel = new BanksModel();
    }

    public function pembayaranPOImport()
    {
        return view('Pembayaran/pembayaranPOImport/index');
    }

    public function createPembayaranPOImportView()
    {
        $Sub_AkunsModel = new Sub_AkunsModel();


        $bankList = $this->banksModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('name', "ASC")
            ->findAll();

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
            ->where('company_id', $this->this_company_id)
            ->findAll();
        $divisi = $this->divisiModel->getDivisiAccess();
        $data = [
            'supplierList' => $this->supplierModel->getSupplierByType("INTERNASIONAL"),
            "subsAkuns" => $subAkunsModel,
            "bankList" => $bankList,
            'divisi' => $divisi
        ];
        return view('Pembayaran/pembayaranPOImport/form', $data);
    }

    public function updatePembayaranPOImportView($id)
    {
        $id = decrypt($id);
        $Sub_AkunsModel = new Sub_AkunsModel();

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
            ->findAll();
        $divisi = $this->divisiModel->getDivisiAccess();
        $bankList = $this->banksModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('name', "ASC")
            ->findAll();

        $data = [
            'supplierList' => $this->supplierModel->getSupplierByType("INTERNASIONAL"),
            'paymentData' => $this->importPOPaymentModel->asArray()->find($id),
            'poDetail' => null,
            'sisaBayar' => 0,
            "subsAkuns" => $subAkunsModel,
            "bankList" => $bankList,
            'divisi' => $divisi
        ];
        

        if ($data['paymentData'] == null) {
            return redirect()->to('pembayaran-po-import');
        }

        if ($data['paymentData']['po_type'] == "BAKU" || $data['paymentData']['po_type'] == "BAHAN BAKU") {
            $data['poDetail'] = $this->rmImportPOModel->asObject()->find($data['paymentData']['po_id']);
        } else {
            $data['poDetail'] = $this->amPurchaseOrderModel->asObject()->find($data['paymentData']['po_id']);
        }

        $data['sisaBayar'] = $data['poDetail']->total - $this->importPOPaymentModel->getTotalPembayaran($data['paymentData']['po_id']);

        return view('Pembayaran/pembayaranPOImport/form', $data);
    }

    public function listPembayaranPOImport($id)
    {
        $id = decrypt($id);
        $detailPembayaran = $this->importPOPaymentModel->asArray()->find($id);
        $addCondition = [
            'po_type' => $detailPembayaran['po_type'] == "BAHAN BAKU" ? "BAKU" : "PENOLONG",
        ];

        if ($addCondition['po_type'] == "BAKU") {

            $condition = [
                'rm_import_po_details.deletedAt' => null,
                'rm_import_po_details.rm_import_po_id' => $detailPembayaran['po_id']
            ];
        } else {
            // BP
            $condition = [
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_order_details.am_purchase_order_id' => $detailPembayaran['po_id']
            ];
        }

        $poList = $this->importPOPaymentModel->getPuchaseOrderList($condition, $addCondition);
        $responseData = [];
        foreach ($poList as $data) {
            $entry = [
                "detail_id"         => $data->id,
                "kode_barang"       => $data->kode_barang,
                "nama_barang"       => $data->barang_name . ' - ' . $data->spesifikasi,
                "qty_order"         => number_format($data->qty),
                "total_harga"       => number_format($data->total),
                "total_harga_number" => intval($data->total),
            ];
            $addCondition['po_type'] == "BAKU" ? $entry["id"] = $data->rm_import_po_id : $entry["id"] = $data->am_purchase_order_id;
            array_push($responseData, $entry);
        }
        //to find all pembayaran
        foreach ($responseData as $r => $i) {
            $condition = [
                'purchase_detail_id'   => $i['detail_id']
            ];
            $selectQry = "sum(amount) as totalPaid";
            $totalPaidAmount = $this->importPOPaymentDetailModel
                ->select($selectQry)
                ->where($condition)
                ->groupBy('purchase_detail_id')
                ->first();
            $responseData[$r]['sisa_pembayaran'] = $i['total_harga_number'] - intval($totalPaidAmount['totalPaid']);
        }
        //find the pembayaran in the specific form
        foreach ($responseData as $r => $i) {
            $condition = [
                'purchase_detail_id'   => $i['detail_id'],
                'import_po_payment_id' => $id
            ];
            $paidAmount = $this->importPOPaymentDetailModel
                ->select("amount")
                ->where($condition)
                ->first();

            $responseData[$r]['input_user'] = intval($paidAmount['amount']);
        }

        $panjarList = $this->localPOPaymentPanjarModel
            ->select('*, no_panjar')
            ->join("import_po_payments", 'local_po_payment_panjar.local_po_payment_id = import_po_payments.id')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
            ->where('import_po_payments.deletedAt', null)
            ->where('import_po_payments.id', $id)
            ->where('type', 'INTERNASIONAL')
            ->findAll();



        foreach ($panjarList as $i => $p) {
            $panjarList[$i]['bayar_panjar_number'] = intval($p['bayar_panjar']);
            $panjarList[$i]['total_panjar_number'] = intval($p['total_panjar']);
            $panjarList[$i]['total_bayar_panjar_number'] = intval($this->localPOPaymentPanjarModel
                ->getTotalPembayaranPanjar($p['panjar_id'], "INTERNASIONAL")['total_bayar_panjar']);
            $panjarList[$i]['sisa_panjar'] = $panjarList[$i]['total_panjar_number'] - $panjarList[$i]['total_bayar_panjar_number'];
        }

        $total_bayar_panjar = 0;
        foreach ($panjarList as $p) {
            $total_bayar_panjar += intval($p['bayar_panjar']);
        }

        $statusPph = $this->importPOPaymentModel->select("status_pph")->find($id);
        $currency = $this->importPOPaymentModel->select("currency")->find($id);
        $panjarPaid = $this->localPOPaymentPanjarModel
            ->select("sum(bayar_panjar) as total_paid")
            ->where('company_id', $this->this_company_id)
            ->where('local_po_payment_id', $id)
            ->where('type', 'INTERNASIONAL')
            ->first();

        return response()->setJson([
            'token' => csrf_hash(),
            'data'  => $responseData,
            'panjar_data' => $panjarList,
            'panjar_paid' => intval($panjarPaid['total_paid']),
            'status_pph' => $statusPph,
            'currency'   => $currency['currency']
        ]);
    }

    public function allPembayaranPOImport()
    {
        $importPOPaymentModel = new ImportPOPaymentModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $responseData = [];

        $condition = [
            'import_po_payments.company_id' => $this->this_company_id,
            'import_po_payments.deletedAt'  => null
        ];
        $addCondition = [
            "startDate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastDate"  => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "search"    => $this->request->getGet("search"),
            "status_posting" => $this->request->getGet('status_posting'),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $paymentList = $importPOPaymentModel->getPaymentList($condition, $addCondition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentList['data'] as $data) {
            array_push($responseData, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "divisi"            => $data->divisi,
                "payment_no"        => $data->payment_no,
                "supplier_name"     => $data->supplier_name,
                "currency"          => $data->currency,
                "amount"            => $data->payment_amt,
                "payment_date"      => $data->payment_date,
                "status_posting"    => $data->status_posting
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $paymentList['totalData'],
            "recordsFiltered"   => $paymentList['totalFilteredData'],
            "data"              => $responseData,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePembayaranPOImport()
    {
        $poType = $this->request->getVar('po_type');
        $poId = decrypt($this->request->getVar('import_po'));
        $aMPurchaseOrderModel = new AMPurchaseOrderModel();
        
        // Ambil data PO pertama berdasarkan tipe
        if ($poType == 'BAHAN BAKU') {
            $firstPo = $this->rmImportPOModel->where('id', $poId)->first();
            $poModel = $this->rmImportPOModel;
        } else if ($poType == 'BAHAN PENOLONG') {
            $firstPo = $aMPurchaseOrderModel->where('id', $poId)->first();
            $poModel = $aMPurchaseOrderModel;
        } else {
            return response()->setJSON([
                'status' => false,
                'message' => "Jenis PO tidak valid",
                'token' => csrf_hash()
            ]);
        }
        
        // Validasi keberadaan PO
        if (!$firstPo) {
            return response()->setJSON([
                'status' => false,
                'message' => "Data PO tidak ditemukan",
                'token' => csrf_hash()
            ]);
        }
        
        // Validasi nomor pembayaran duplikat
        $first = $this->importPOPaymentModel->where('payment_no', $this->request->getVar('no_pembayaran'))->where('company_id', $this->this_company_id)->first();
        if ($first != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "No pembayaran sudah digunakan",
                'token' => csrf_hash()
            ]);
        }
        
        $pembayaranList = json_decode($this->request->getVar('pembayaranList'));
        $panjarList     = json_decode($this->request->getVar('panjarList'));
        $allEmpty = true;
        
        // Validasi pembayaran tidak kosong
        foreach ($pembayaranList as $item) {
            if (!empty($item->pembayaran_user_input)) {
                $allEmpty = false;
                break;
            }
        }

        if ($allEmpty) {
            return $this->response->setJSON([
                'status' => false,
                'message' => "Pembayaran Kosong",
                'token' => csrf_hash()
            ]);
        }

        // Insert data pembayaran utama
        $id = $this->importPOPaymentModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'payment_no' => $this->request->getVar('no_pembayaran'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'po_type' => $poType,
            'po_id' => $poId,
            'voucher_no' => $this->request->getVar('voucher_no'),
            'currency' => $this->request->getVar('currency'),
            'valas_id' => $firstPo['currency'],
            'payment_amt' => intval(str_replace(',', '', $this->request->getVar('grand_total'))),
            'current_exchange_rate' => formatter($this->request->getVar('current_exchange_rate'), "CURR_TO_INT"),
            'payment_date' =>  $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("payment_date")))) : "",
            'termin' => $this->request->getVar('termin'),
            'payment_method' => $this->request->getVar('payment_method'),
            'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
            'note' => $this->request->getVar('note'),
            'no_invoice' => $this->request->getVar('no_invoice'),
            'invoice_emkl' => $this->request->getVar('invoice_emkl'),
            'no_aju' => $this->request->getVar('no_aju'),
            'akun_kas' => $this->request->getVar('akun_kas'),
            'akun_selisih' => $this->request->getVar('akun_selisih'),
            'status_pph' => $this->request->getVar('status_pph')
        ]);

        // Insert detail pembayaran
        foreach ($pembayaranList as $l) {
            if (intval($l->pembayaran_user_input != 0) && isset($l->pembayaran_user_input)) {
                $this->importPOPaymentDetailModel->insert([
                    "import_po_payment_id" => $id,
                    "purchase_id"          => intval(decrypt($l->id)),
                    "purchase_detail_id"   => intval(decrypt($l->detail_id)),
                    "amount"                => intval($l->pembayaran_user_input),
                ]);
            }
        }

        // Insert data panjar
        foreach ($panjarList as $p) {
            if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                $insertPanjar = $this->localPOPaymentPanjarModel->insert([
                    "company_id" => $this->this_company_id,
                    "local_po_payment_id" => $id,
                    "type"  => "INTERNASIONAL",
                    "panjar_id" => $p->id,
                    "bayar_panjar" => $p->bayar_panjar
                ]);
            }
        }

        return response()->setJSON([
            'id' => encrypt($id),
            'status' => true,
            'message' => "Pembayaran berhasil disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function updatePembayaranPOImport()
    {
        $id = decrypt($this->request->getVar('id'));
        $poType = $this->request->getVar('po_type');
        $poId = decrypt($this->request->getVar('import_po'));
        $aMPurchaseOrderModel = new AMPurchaseOrderModel();
        
        // Ambil data PO berdasarkan tipe
        if ($poType == 'BAHAN BAKU') {
            $poModel = $this->rmImportPOModel;
        } else if ($poType == 'BAHAN PENOLONG') {
            $poModel = $aMPurchaseOrderModel;
        } else {
            return response()->setJSON([
                'status' => false,
                'message' => "Jenis PO tidak valid",
                'token' => csrf_hash()
            ]);
        }
        
        // Validasi keberadaan PO
        $poData = $poModel->where('id', $poId)->first();
        if (!$poData) {
            return response()->setJSON([
                'status' => false,
                'message' => "Data PO tidak ditemukan",
                'token' => csrf_hash()
            ]);
        }
        
        $pembayaranList = json_decode($this->request->getVar('pembayaranList'));
        $panjarList     = json_decode($this->request->getVar('panjarList'));

        // Update data pembayaran utama
        $this->importPOPaymentModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'po_type' => $poType,
            'po_id' => $poId,
            'voucher_no' => $this->request->getVar('voucher_no'),
            'currency' => $this->request->getVar('currency'),
            'valas_id' => $poData['currency'],
            'payment_amt' => intval(str_replace(',', '', $this->request->getVar('grand_total'))),
            'current_exchange_rate' => formatter($this->request->getVar('current_exchange_rate'), "CURR_TO_INT"),
            'payment_date' =>  $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("payment_date")))) : "",
            'termin' => $this->request->getVar('termin'),
            'payment_method' => $this->request->getVar('payment_method'),
            'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
            'note' => $this->request->getVar('note'),
            'no_invoice' => $this->request->getVar('no_invoice'),
            'invoice_emkl' => $this->request->getVar('invoice_emkl'),
            'no_aju' => $this->request->getVar('no_aju'),
            'akun_kas' => $this->request->getVar('akun_kas'),
            'akun_selisih' => $this->request->getVar('akun_selisih'),
            'status_pph' => $this->request->getVar('status_pph')
        ]);

        // Hapus dan insert ulang detail pembayaran
        $this->importPOPaymentDetailModel->where('import_po_payment_id', $id)->delete();

        foreach ($pembayaranList as $l) {
            if (intval($l->pembayaran_user_input != 0) && isset($l->pembayaran_user_input)) {
                $this->importPOPaymentDetailModel->insert([
                    "import_po_payment_id" => $id,
                    "purchase_id"          => $l->id,
                    "purchase_detail_id"   => $l->detail_id,
                    "amount"               => intval($l->pembayaran_user_input),
                ]);
            }
        }

        // Hapus dan insert ulang data panjar
        $this->localPOPaymentPanjarModel->where('local_po_payment_id', $id)->where('type', 'INTERNASIONAL')->delete();
        
        foreach ($panjarList as $p) {
            if (isset($p->bayar_panjar) && intval($p->bayar_panjar) !=  0) {
                $insertPanjar = $this->localPOPaymentPanjarModel->insert([
                    "company_id" => $this->this_company_id,
                    "local_po_payment_id" => $id,
                    "type"  => "INTERNASIONAL",
                    "panjar_id" => $p->id,
                    "bayar_panjar" => $p->bayar_panjar
                ]);
            }
        }
        
        return response()->setJSON([
            'message' => "Pembayaran berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function deletePembayaranPOImport()
    {
        $this->importPOPaymentModel->delete(decrypt($this->request->getVar('id')));
        $this->importPOPaymentDetailModel->where('import_po_payment_id', decrypt($this->request->getVar('id')))->delete();
        $this->localPOPaymentPanjarModel->where('local_po_payment_id',  decrypt($this->request->getVar('id')))
            ->where('type', 'INTERNASIONAl')
            ->delete();

        return response()->setJSON([
            'message' => "Pembayaran berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $detail = $this->importPOPaymentModel->where('id', $id)->first();

        if ($detail == null) {
            return redirect()->to('pembayaran-po-import');
        }

        $data = [
            "detail" => $detail,
            "company" => $this->companyModel->find($detail['company_id']),
            'supplier' => $this->supplierModel->find($detail['supplier_id']),
            'poList' => null,
        ];

        if ($data['detail'] == null) {
            return redirect()->to('pembayaran-po-import');
        }


        if ($data['detail']['po_type'] == "BAHAN BAKU") {
            // BAHAN BAKU
            $condition = [
                'rm_import_po_details.deletedAt' => null,
                'rm_import_po_details.rm_import_po_id' => $detail['po_id']
            ];
            $addCondition = [
                'po_type' => "BAKU"
            ];
            $poDetail = $this->rmImportPOModel->find($detail['po_id']);
        } else {
            // BAHAN PENOLONG
            $condition = [
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_order_details.am_purchase_order_id' => $detail['po_id']
            ];
            $addCondition = [
                'po_type' => "PENOLONG"
            ];
            $poDetail = $this->amPurchaseOrderModel->find($detail['po_id']);
        }

        $poList = $this->importPOPaymentModel->getPuchaseOrderList($condition, $addCondition);
        $responseData = [];
        foreach ($poList as $p) {
            $entry = [
                "detail_id"         => $p->id,
                "tgl_po"            => $poDetail['po_date'],
                'po_no'             => $poDetail['po_no'],
                "kode_barang"       => $p->kode_barang,
                "nama_barang"       => $p->barang_name . ' - ' . $p->spesifikasi,
                'kode_satuan'       => $p->kode_satuan,
                "qty_order"         => number_format($p->qty),
                "total_harga"       => number_format($p->total),
                "total_harga_number" => intval($p->total),

            ];
            $addCondition['po_type'] == "BAKU" ? $entry["id"] = $p->rm_import_po_id : $entry["id"] = $p->am_purchase_order_id;
            array_push($responseData, $entry);
        }

        //find the pembayaran in the specific form
        foreach ($responseData as $r => $i) {
            $condition = [
                'purchase_detail_id'   => $i['detail_id'],
                'import_po_payment_id' => $id
            ];
            $paidAmount = $this->importPOPaymentDetailModel
                ->select("amount")
                ->where($condition)
                ->first();

            $responseData[$r]['input_user'] = intval($paidAmount['amount']);
        }
        $panjarList = $this->localPOPaymentPanjarModel
            ->select('*, no_panjar')
            ->join("import_po_payments", 'local_po_payment_panjar.local_po_payment_id = import_po_payments.id')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
            ->where('import_po_payments.deletedAt', null)
            ->where('import_po_payments.id', $id)
            ->where('type', 'INTERNASIONAL')
            ->findAll();



        foreach ($panjarList as $i => $p) {
            $panjarList[$i]['bayar_panjar_number'] = intval($p['bayar_panjar']);
            $panjarList[$i]['total_panjar_number'] = intval($p['total_panjar']);
            $panjarList[$i]['total_bayar_panjar_number'] = intval($this->localPOPaymentPanjarModel
                ->getTotalPembayaranPanjar($p['panjar_id'], "INTERNASIONAL")['total_bayar_panjar']);
            $panjarList[$i]['sisa_panjar'] = $panjarList[$i]['total_panjar_number'] - $panjarList[$i]['total_bayar_panjar_number'];
        }

        $total_bayar_panjar = 0;
        foreach ($panjarList as $p) {
            $total_bayar_panjar += intval($p['bayar_panjar']);
        }


        $data['poList'] = $responseData;
        $data['panjar_list'] = $panjarList;
        $data['total_bayar_panjar'] = $total_bayar_panjar;

        $this->dompdf->loadHtml(view('Pembayaran/pembayaranPOImport/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Pembayaran Purchase Order Internasional ", array("Attachment" => false));

        exit(0);
    }


    public function getNomorPembayaran()
    {

        $month = idate('m');
        $year = date('Y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "PAY/IM/$romanMonth/$year/";



        $lastData = $this->importPOPaymentModel->asObject()
            ->like('payment_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $summaryNo = "{$numberTemplate}0001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->payment_no);
            $lastIncrement = (int)$exploded[4] + 1;

            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $summaryNo = $numberTemplate . $paddedNumber;
        }

        return response()->setJSON([
            'data' => $summaryNo,
            'token' => csrf_hash()
        ]);
    }




    public function listPembayaranPOBelumLunas()
    {
        $poType = str_replace('BAHAN ', '', $this->request->getVar('po_type'));
        $supplierID = $this->request->getVar('supplier_id');
        $divisiID = $this->request->getVar('divisi_id');

        $poList = $this->getPOList($supplierID ?? 0, $poType ?? "", $divisiID);
        $poListBelumLunas = [];


        foreach ($poList as $p) {
            $condition = [
                'purchase_id' => $p->id,
                // 'purchase_detail_id' => $p->detail_id,
                'import_po_payments.deletedAt' => null,
                'import_po_payments.po_type' => $this->request->getVar('po_type')
            ];
            $totalBayar = $this->importPOPaymentDetailModel->select('SUM(amount) AS total_dibayar')
                ->join('import_po_payments', 'import_po_payments.id = import_po_payment_detail.import_po_payment_id')
                ->where($condition)
                ->first();


            if (intval($totalBayar['total_dibayar']) < $p->total || $totalBayar['total_dibayar'] == null) {
                $nilaiKurs = $this->kursModel->getKursCurrent($p->currency_id, $p->po_date);
                $poListBelumLunas[] = [
                    'id' => encrypt($p->id),
                    'po_no' => $p->po_no,
                    'total_bayar' => number_format($p->total),
                    // 'sisa_bayar' => number_format($p->total - $totalBayar['total_dibayar']),
                    'valas' => $p->currency,
                    'kurs' => number_format($nilaiKurs == null ? 0 : $nilaiKurs['nilai_kurs']),
                ];
            }
        }

        $poListBelumLunas = array_unique($poListBelumLunas, SORT_REGULAR);


        return response()->setJSON([
            'data' => $poListBelumLunas,
            'token' => csrf_hash()
        ]);
    }

    public function allRiwayatPembayaran()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $responseData = [];

        $condition = [
            'po_type' => $this->request->getVar('po_type') == "BAHAN BAKU" ? "BAHAN BAKU" : "BAHAN PENOLONG",
            'deletedAt' => null,
            'company_id' => $this->this_company_id,
            'po_id' => decrypt($this->request->getVar('po_id'))
        ];


        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $poList = $this->importPOPaymentModel->getRiwayatPembayaranList($condition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($condition['po_type'] == "BAHAN BAKU") {
            $poFirst = $this->rmImportPOModel->where('deletedAt', null)->where('id', $condition['po_id'])->first();
        } else {
            $poFirst = $this->amPurchaseOrderModel->where('deletedAt', null)->where('id', $condition['po_id'])->first();
        }

        $sisaBayar = ($poFirst == null) ? 0 : $poFirst['total'];

        foreach ($poList['data'] as $data) {
            $sisaBayar -= $data->payment_amt;
            array_push($responseData, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "no_pembayaran"     => $data->payment_no,
                "tanggal_bayar"     => date('d/m/Y', strtotime($data->payment_date)),
                "payment_method"    => $data->payment_method,
                "total_bayar"       => number_format($data->payment_amt),
                "sisa_bayar"        => number_format($sisaBayar)
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $poList['totalData'],
            "recordsFiltered"   => $poList['totalFilteredData'],
            "data"              => $responseData,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function allPO()
    {
        $statusPph = $this->request->getVar('status_pph');

        $materialType = null;
        // Sumber bisa dari po_type atau tipe_bahan
        if ($this->request->getVar('po_type')) {
            $materialType = $this->request->getVar('po_type') == "BAHAN BAKU"
                ? "BAKU"
                : "PENOLONG";
        }

        if ($this->request->getVar('tipe_bahan')) {
            $materialType = $this->request->getVar('tipe_bahan') == "BAHAN BAKU"
                ? "BAKU"
                : "PENOLONG";
        }


        $pphNilai = $statusPph == '1' ? 0.0025 : 0;

        $responseData = [];

        if ($this->request->getVar('payment_type') == "DOWN_PAYMENT") {

        $addCondition = [
            'po_type' => $this->request->getVar('po_type') == "BAHAN BAKU" ? "BAKU" : "PENOLONG",
        ];

        if ($addCondition['po_type'] == "BAKU") {
            // BB
            $condition = [
                'rm_import_po_details.deletedAt' => null,
                'rm_import_po_details.rm_import_po_id' => decrypt($this->request->getVar('po_id')),

            ];
        } else {
            // BP
            $condition = [
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_order_details.am_purchase_order_id' => decrypt($this->request->getVar('po_id')),

            ];
        }

            $poList = $this->importPOPaymentModel
                ->getPuchaseOrderList($condition, $addCondition);

        } else {

            $addCondition = [
                'material_type' => $materialType,
            ];

            $condition = [
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.status_penerimaan' => "IMPORT",
                'penerimaan_barang_detail.purchase_order_id' => decrypt($this->request->getVar('po_id')),
            ];

            $poList = $this->importPOPaymentModel
                ->getPuchaseOrderListLPB($condition, $addCondition);
        }


        // $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $total_harga_semua = 0;

        foreach ($poList as $data) {
            $entry = [
                "detail_id"          => encrypt($data->id),
                "kode_barang"        => $data->kode_barang,
                "nama_barang"        => $data->barang_name . ' - ' . $data->spesifikasi,
                "qty_order"          => $data->qty,
                "total_harga"        => $data->total,
                "total_harga_number" => $data->total,
            ];

            if ($materialType == "BAKU") {
                $entry["id"] = encrypt($data->rm_import_po_id ?? $data->purchase_order_id);
            } else {
                $entry["id"] = encrypt($data->am_purchase_order_id ?? $data->purchase_order_id);
            }

            $responseData[] = $entry;
        }


        foreach ($responseData as $r => $i) {
            $condition = [
                'purchase_detail_id'   => decrypt($i['detail_id']),
                'deletedAt' => null
            ];
            $selectQry = "sum(amount) as totalPaid";
            $totalPaidAmount = $this->importPOPaymentDetailModel
                ->select($selectQry)
                ->where($condition)
                ->groupBy('purchase_detail_id')
                ->first();
            if ($totalPaidAmount != null) {
                $responseData[$r]['sisa_pembayaran'] = $i['total_harga_number'] - $totalPaidAmount['totalPaid'];
            } else {
                $responseData[$r]['sisa_pembayaran'] = $i['total_harga_number'];
            }
        }


        return json_encode($responseData);
    }

    private function getPOList($supplierId, $poType, $divisiId): array
    {
        $condition = [
            'company_id'    => $this->this_company_id,
            'supplier_id'   => $supplierId,
            'is_posted'     => 1
        ];

        if ($poType == 'BAKU') {
            $rmImportPOModel = new RMImportPOModel();
            $selectQry = "rm_import_pos.*, rm_import_po_details.id as detail_id,
                      metadata.value AS currency, rm_import_pos.currency AS currency_id";

            $poData = $rmImportPOModel->asObject()
                ->select($selectQry)
                ->join('rm_import_po_details', 'rm_import_po_details.rm_import_po_id = rm_import_pos.id')
                ->join('metadata', 'metadata.id = rm_import_pos.currency')
                ->where($condition)
                ->where('division_id', $divisiId)
                ->findAll();

            return $poData;
        } else if ($poType == 'PENOLONG') {
            $aMPurchaseOrderModel = new AMPurchaseOrderModel();
            $selectQry = "am_purchase_orders.*, am_purchase_order_details.id as detail_id,
                      metadata.value AS currency, am_purchase_orders.currency AS currency_id";

            $poData = $aMPurchaseOrderModel->asObject()
                ->select($selectQry)
                ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency')
                ->where($condition)
                ->where('division_id', $divisiId)
                ->findAll();

            return $poData;
        }

        return [];
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        $data = $this->importPOPaymentModel->find($id);
        if (!$data) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        if ($data['status_posting'] == 1) {
            // 🔁 UNPOST
            $result = $this->jurnalController->unpostDataPembayaran(
                $id,
                'IMPORT',
                ''
            );

            if (!$result['status']) {
                $db->transRollback();
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => $result['message']
                ]);
            }

            $this->importPOPaymentModel->update($id, [
                'status_posting' => "0"
            ]);

            $message = 'Pembayaran berhasil di-unposting';

        } else {
            // 🚀 POST
            $this->importPOPaymentModel->update($id, [
                'status_posting' => "1"
            ]);

            $this->jurnalController->insertDataPembayaran(
                $id,
                'IMPORT',
                ''
            );

            $message = 'Pembayaran berhasil diposting';
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => 'Proses gagal'
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => $message
        ]);
    }


}
