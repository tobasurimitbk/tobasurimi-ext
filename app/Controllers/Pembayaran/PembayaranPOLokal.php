<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;

use App\Models\SupplierModel;
use App\Models\LocalPOPaymentModel;
use App\Models\LocalPOPaymentDetailModel;
use App\Models\LocalPOInvSummaryModel;
use App\Models\LocalPOInvSumDetailModel;
use App\Models\PajakTandaTerimaFakturModel;
use App\Models\PenerimaanBarangModel;
use App\Models\TandaTerimaFakturDetailModel;
use App\Models\TandaTerimaFakturModel;
use App\Models\Sub_AkunsModel;
use Dompdf\Dompdf;
use Exception;

class PembayaranPOLokal extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
    }

    public function pembayaranPOLokalBP()
    {
        return view('Pembayaran/pembayaranPOLokal/bahanPenolong');
    }

    public function createPembayaranPOLokalBP()
    {
        $supplierModel = new SupplierModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $supplierList = $supplierModel->asObject()
            ->where('deletedAt', null)
            ->where('type', "BAHAN PENOLONG")
            ->orderBy('name', "ASC")
            ->findAll();

        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
            ->findAll();

        $data = [
            "suppliers" => $supplierList,
            "subsAkuns" => $subAkunsModel
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanPenolong', $data);
    }

    public function getTandaTerimaFaktur($supplierID)
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $res = $tandaTerimaFakturModel->getListTandaTerimaFakturNotProcessed($supplierID);
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

        return response()->setJSON([
            'detail' => $tandaTerimaFakturModel->getByID($tandaTerimaFakturID),
            'list' => $tandaTerimaFakturDetailModel->getListTandaTerimaItemFaktur($tandaTerimaFakturID),
            'tax_dipungut_negara' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dipungut oleh negara", $tandaTerimaFakturID),
            'tax_dikembalikan_lagi' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dikembalikan lagi", $tandaTerimaFakturID)
        ]);
    }

    public function createPembayaranPOLokalBPAction()
    {
        try {
            $localPOPaymentModel = new LocalPOPaymentModel();

            $id = $localPOPaymentModel->insert([
                'payment_no' => $this->request->getVar('no_bukti_pembayaran'),
                'supplier_id' => $this->request->getVar('supplier_id'),
                'tanda_terima_faktur_id' => $this->request->getVar('tanda_terima_faktur_id'),
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('jatuh_tempo')))),
                'amount' => repairDouble($this->request->getVar('nominal_pembayaran')),
                'payment_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'payment_method' => $this->request->getVar('payment_method'),
                'type_po' => "Bahan Penolong",
                'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih'),
            ]);

            return response()->setJSON([
                'message' => "Kwitansi pembayaran lokal bahan penolong berhasil dibuat",
                'status' => true,
                'token' => csrf_hash(),
                'id' => $id
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
            $localPOPaymentModel = new LocalPOPaymentModel();
            $penerimaanBarangModel = new PenerimaanBarangModel();

            $poIDAmt = json_decode($this->request->getVar('poIDList'));
            $poNOAmt = json_decode($this->request->getVar('poNoList'));

            $poIDArr = [];
            $poNoArr = [];

            foreach ($poIDAmt as $p) {
                \array_push($poIDArr, $p->poID);
            }

            foreach ($poNOAmt as $p) {
                array_push($poNoArr, $p->poNo);
            }

            $resPoID =  str_replace('"', "", json_encode(array_values(array_unique($poIDArr))));
            $resPoNo =  str_replace("\\", "", json_encode(array_values(array_unique($poNoArr))));
            $resPoNo =  str_replace('"', "", json_encode(array_values(array_unique($poNoArr))));

            $lpb = $penerimaanBarangModel->where('id', $this->request->getVar('lpb'))->first();

            $id = $localPOPaymentModel->insert([
                'payment_no' => $this->request->getVar('no_bukti_pembayaran'),
                'supplier_id' => $this->request->getVar('supplier_id'),
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('jatuh_tempo')))),
                'amount' => repairDouble($this->request->getVar('nominal_pembayaran')),
                'payment_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'payment_method' => $this->request->getVar('payment_method'),
                'type_po' => "Bahan Baku",
                'type_bayar' => ucfirst($this->request->getVar('tipe_pembayaran')),
                'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
                'multiple_po_no' => $resPoNo,
                'multiple_po_id' => $resPoID,
                'lpb_no' => $lpb == null ? null : $lpb['no_penerimaan_barang'],
                'month' => $this->request->getVar('bulan'),
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih'),

            ]);

            return response()->setJSON([
                'message' => "Kwitansi pembayaran lokal bahan baku berhasil dibuat",
                'status' => true,
                'token' => csrf_hash(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan " . $e->getMessage() . $e->getLine(),
                'status' => true,
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
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
            "local_po_payments.type_po"  => $this->request->getGet('type_po'),
            "local_po_payments.deletedAt" => null
        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "dueDate" => $this->request->getGet('dueDate'),
            "paymentDate" => $this->request->getGet('paymentDate'),
            "typeBayar" => $typeBayar
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $paymentData = $localPOPaymentModel->getList($condition, $addCondition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentData['data'] as $data) {
            array_push($dataPembayaranPOLokal, [
                "no"                => $no++,
                "id"                => $data->id,
                "payment_no"        => $data->payment_no,
                "faktur_no"         => $data->faktur_no,
                "supplier"          => $data->supplierName,
                "due_date"          => $data->due_date,
                "payment_date"      => $data->payment_date,
                "payment_method"    => $data->payment_method,
                "amount"            =>  number_format($data->amount ?? 0, 0, ',', '.'),
                "tipe_bayar"        => strtoupper($data->type_bayar)
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
        $localPOPaymentModel = new LocalPOPaymentModel();
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

        $detail = $localPOPaymentModel->get($id);

        $data = [
            "suppliers" => $supplierList,
            "detail" => $localPOPaymentModel->get($id),
            "subsAkuns" => $subAkunsModel,
            'tax_dipungut_negara' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dipungut oleh negara", $detail['tandaTerimaSupplier']['id']),
            'tax_dikembalikan_lagi' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dikembalikan lagi", $detail['tandaTerimaSupplier']['id'])
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanPenolong', $data);
    }

    public function getPembayaranPOLokalBB($id)
    {
        $supplierModel = new SupplierModel();
        $localPOPaymentModel = new LocalPOPaymentModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $supplierList = $supplierModel->asObject()
            ->where('deletedAt', null)
            ->where('type', "BAHAN BAKU")
            ->orderBy('name', "ASC")
            ->findAll();


        $subAkunsModel = $Sub_AkunsModel->asObject()
            ->where('deletedAt', null)
            ->findAll();

        if ($localPOPaymentModel->where('id', $id)->first() == null) {
            return redirect()->to('pembayaran-po-lokal-bb');
        }

        $data = [
            "suppliers" => $supplierList,
            "detail" => $localPOPaymentModel->getBB($id, $this->this_company_id),
            "subsAkuns" => $subAkunsModel
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanBaku', $data);
    }
    // PRINT PEMBAYARAN PO BP
    public function pembayaranPOLokalBPPrint($id)
    {
        $dompdf = new Dompdf();
        $localPOPaymentModel = new LocalPOPaymentModel();
        $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();

        if ($localPOPaymentModel->where('id', $id)->first() == null) {
            return redirect()->to('pembayaran-po-lokal-bp');
        }

        $detail = $localPOPaymentModel->get($id);
        $data = [
            "detail" => $localPOPaymentModel->get($id),
            'tax_dipungut_negara' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dipungut oleh negara", $detail['tandaTerimaSupplier']['id']),
            'tax_dikembalikan_lagi' => $pajakTandaTerimaFakturModel->getTaxDetail("Pajak dikembalikan lagi", $detail['tandaTerimaSupplier']['id'])
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

        if ($localPOPaymentModel->where('id', $id)->first() == null) {
            return redirect()->to('pembayaran-po-lokal-bb');
        }

        $data = [
            "detail" => $localPOPaymentModel->getBB($id, $this->this_company_id)
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



        $data = [
            "suppliers" => $supplierList,
            "subsAkuns" => $subAkunsModel
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanBaku', $data);
    }


    public function delete()
    {
        $id = $this->request->getVar('id');
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
        $localPOPaymentModel = new LocalPOPaymentModel();
        $res = $localPOPaymentModel->getListLPBNotPaid($supplierID);
        return response()->setJson([
            'data' => $res,
            'token' => csrf_hash()
        ]);
    }

    public function getListBarangLPBNotPaidBB()
    {
        $supplierID = $this->request->getVar('supplierID');
        $lpbID = $this->request->getVar('lpbID');
        $month = $this->request->getVar('bulan');

        $localPOPaymentModel = new LocalPOPaymentModel();

        if (!empty($lpbID)) {
            // HARIAN
            return response()->setJson([
                'token' => csrf_hash(),
                'data' => $localPOPaymentModel->getListPONotPaidByLPB(
                    $lpbID,
                    $supplierID
                ),
            ]);
        } else {
            // BULANAN (KWITANSI TB)
            return response()->setJson([
                'token' => csrf_hash(),
                'data' => $localPOPaymentModel->getListPONotPaidByMonth(
                    $supplierID,
                    $month
                )
            ]);
        }
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
}
