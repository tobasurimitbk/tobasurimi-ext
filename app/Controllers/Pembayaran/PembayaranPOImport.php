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
use App\Models\Sub_AkunsModel;

class PembayaranPOImport extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $supplierModel;
    protected $importPOPaymentModel;
    protected $kursModel;
    protected $rmImportPOModel; // Import BB
    protected $amPurchaseOrderModel; // Import BP - Lokal BP
    protected $metaDataModel;
    protected $jurnalController;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
        $this->importPOPaymentModel = new ImportPOPaymentModel();
        $this->kursModel = new KursModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->metaDataModel = new MetadataModel();
        $this->jurnalController = new JurnalUmum();
    }

    public function pembayaranPOImport()
    {
        return view('Pembayaran/pembayaranPOImport/index');
    }

    public function createPembayaranPOImportView()
    {
        $Sub_AkunsModel = new Sub_AkunsModel();

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
            ->findAll();
        $data = [
            'supplierList' => $this->supplierModel->asObject()->where('deletedAt', null)->where('type', "INTERNASIONAL")->findAll(),
            "subsAkuns" => $subAkunsModel
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
        $data = [
            'supplierList' => $this->supplierModel->asObject()->where('deletedAt', null)->where('type', "INTERNASIONAL")->findAll(),
            'paymentData' => $this->importPOPaymentModel->asArray()->find($id),
            'poDetail' => null,
            'sisaBayar' => 0,
            "subsAkuns" => $subAkunsModel
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

        $condition = [];
        $addCondition = [
            "startDate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastDate"  => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "search"    => $this->request->getGet("search"),
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
                "payment_no"        => $data->payment_no,
                "supplier_name"     => $data->supplier_name,
                "currency"          => $data->currency,
                "amount"            => number_format($data->payment_amt),
                "payment_date"      => $data->payment_date
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
        if (formatter($this->request->getVar('payment_amt'), "CURR_TO_INT") < formatter($this->request->getVar('payment_amt'), "CURR_TO_INT")) {
            return response()->setJSON([
                'status' => false,
                'message' => "Total bayar tidak boleh melebihi sisa bayar",
                'token' => csrf_hash()
            ]);
        }

        $id = $this->importPOPaymentModel->insert([
            'company_id' => $this->this_company_id,
            'payment_no' => $this->request->getVar('no_pembayaran'),
            'payment_type' => $this->request->getVar('tipe_pembayaran'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'po_type' => $this->request->getVar('po_type'),
            'po_id' => decrypt($this->request->getVar('import_po')),
            'voucher_no' => $this->request->getVar('voucher_no'),
            'currency' => $this->request->getVar('currency'),
            'payment_amt' => formatter($this->request->getVar('payment_amt'), "CURR_TO_INT"),
            'current_exchange_rate' => formatter($this->request->getVar('current_exchange_rate'), "CURR_TO_INT"),
            'payment_date' =>  $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("payment_date")))) : "",
            'termin' => $this->request->getVar('termin'),
            'payment_method' => $this->request->getVar('payment_method'),
            'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
            'note' => $this->request->getVar('note'),
            'akun_kas' => $this->request->getVar('akun_kas'),
            'akun_selisih' => $this->request->getVar('akun_selisih'),
        ]);

        $result = $this->jurnalController->insertDataPembayaran($id, "IMPORT");

        return response()->setJSON([
            'id' => encrypt($id),
            'status' => true,
            'message' => "Pembayaran berhasil disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function updatePembayaranPOImport()
    {
        if (formatter($this->request->getVar('payment_amt'), "CURR_TO_INT") < formatter($this->request->getVar('payment_amt'), "CURR_TO_INT")) {
            return response()->setJSON([
                'status' => false,
                'message' => "Total bayar tidak boleh melebihi sisa bayar",
                'token' => csrf_hash()
            ]);
        }

        $id = decrypt($this->request->getVar('id'));
        $this->importPOPaymentModel->update($id, [
            'company_id' => $this->this_company_id,
            'payment_no' => $this->request->getVar('no_pembayaran'),
            'payment_type' => $this->request->getVar('tipe_pembayaran'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'po_type' => $this->request->getVar('po_type'),
            'po_id' => decrypt($this->request->getVar('import_po')),
            'voucher_no' => $this->request->getVar('voucher_no'),
            'currency' => $this->request->getVar('currency'),
            'payment_amt' => formatter($this->request->getVar('payment_amt'), "CURR_TO_INT"),
            'current_exchange_rate' => formatter($this->request->getVar('current_exchange_rate'), "CURR_TO_INT"),
            'payment_date' =>  $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("payment_date")))) : "",
            'termin' => $this->request->getVar('termin'),
            'payment_method' => $this->request->getVar('payment_method'),
            'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
            'note' => $this->request->getVar('note'),
            'akun_kas' => $this->request->getVar('akun_kas'),
            'akun_selisih' => $this->request->getVar('akun_selisih'),
        ]);

        return response()->setJSON([
            'message' => "Pembayaran berhasil diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function deletePembayaranPOImport()
    {
        $this->importPOPaymentModel->delete(decrypt($this->request->getVar('id')));
        return response()->setJSON([
            'message' => "Pembayaran berhasil dihapus",
            'token' => csrf_hash()
        ]);
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

        $poList = $this->getPOList($supplierID ?? 0, $poType ?? "");
        $poListBelumLunas = [];

        foreach ($poList as $p) {
            $totalBayar = $this->importPOPaymentModel->select('SUM(payment_amt) AS total_dibayar')
                ->where('po_id', $p->id)
                ->where('deletedAt', null)
                ->findAll();

            if ($totalBayar[0]['total_dibayar'] < $p->total) {
                $nilaiKurs = $this->kursModel->getKursCurrent($p->currency_id, $p->po_date);
                $poListBelumLunas[] = [
                    'id' => encrypt($p->id),
                    'po_no' => $p->po_no,
                    'total_bayar' => number_format($p->total),
                    'sisa_bayar' => number_format($p->total - $totalBayar[0]['total_dibayar']),
                    'valas' => $p->currency,
                    'kurs' => number_format($nilaiKurs == null ? 0 : $nilaiKurs['nilai_kurs']),
                ];
            }
        }

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
            'po_type' => $this->request->getVar('po_type') == "BAHAN BAKU" ? "BAKU" : "PENOLONG",
            'deletedAt' => null,
            'company_id' => $this->this_company_id,
            'po_id' => decrypt($this->request->getVar('po_id'))
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $poList = $this->importPOPaymentModel->getRiwayatPembayaranList($condition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($condition['po_type'] == "BAKU") {
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
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $responseData = [];

        $addCondition = [
            'po_type' => $this->request->getVar('po_type') == "BAHAN BAKU" ? "BAKU" : "PENOLONG",
        ];

        if ($addCondition['po_type'] == "BAKU") {
            // BB
            $condition = [
                'rm_import_po_details.deletedAt' => null,
                'rm_import_po_details.rm_import_po_id' => decrypt($this->request->getVar('po_id'))
            ];
        } else {
            // BP
            $condition = [
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_order_details.am_purchase_order_id' => decrypt($this->request->getVar('po_id'))
            ];
        }

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $poList = $this->importPOPaymentModel->getPuchaseOrderList($condition, $addCondition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poList['data'] as $data) {
            array_push($responseData, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "kode_barang"       => $data->kode_barang,
                "nama_barang"       => $data->barang_name,
                "qty_order"         => number_format($data->qty),
                "total_harga"       => number_format($data->total),
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

    private function getPOList($supplierId, $poType): array
    {
        $condition = [
            'company_id'    => $this->this_company_id,
            'supplier_id'   => $supplierId,
            'is_posted'     => 1
        ];

        if ($poType == 'BAKU') {
            $rmImportPOModel = new RMImportPOModel();
            $selectQry = "rm_import_pos.*,
                      metadata.value AS currency, rm_import_pos.currency AS currency_id";

            $poData = $rmImportPOModel->asObject()
                ->select($selectQry)
                ->join('metadata', 'metadata.id = rm_import_pos.currency')
                ->where($condition)
                ->findAll();

            return $poData;
        } else if ($poType == 'PENOLONG') {
            $aMPurchaseOrderModel = new AMPurchaseOrderModel();
            $selectQry = "am_purchase_orders.*,
                      metadata.value AS currency, am_purchase_orders.currency AS currency_id";

            $poData = $aMPurchaseOrderModel->asObject()
                ->select($selectQry)
                ->join('metadata', 'metadata.id = am_purchase_orders.currency')
                ->where($condition)
                ->findAll();

            return $poData;
        }

        return [];
    }
}
