<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\PajakTandaTerimaFakturModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SupplierModel;
use App\Models\TandaTerimaFakturDetailModel;
use App\Models\TandaTerimaFakturModel;
use App\Models\LocalPOPaymentBPModel;
use Dompdf\Dompdf;

class TandaTerimaSupBB extends BaseController
{
    protected $supplierModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $tandaTerimaFakturModel;
    protected $tandaTerimaFakturDetailModel;
    protected $pajakTandaTerimaFakturModel;
    protected $localPOPaymentBPModel;
    protected $divisiModel;
    protected $dompdf;
    protected $user_id;
    protected $this_company_id;

    public function __construct()
    {
        $this->user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $this->tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $this->pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();
        $this->divisiModel = new DivisisModel();
        $this->dompdf = new Dompdf();
        $this->localPOPaymentBPModel = new LocalPOPaymentBPModel();
    }

    public function index()
    {
        return view('Purchase/terimaSupplierLokal/bp/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getVar("length"),
            "currentPage" => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "kategori"      => "LOKAL",
            "type"          => "BAHAN PENOLONG"
        ];

        $condition = [
            "tanda_terima_faktur.company_id"  => $this->this_company_id,
            "faktur_type"           => "LOKAL",
            "tipe_bahan"            => "PENOLONG"
        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "start"     => $this->request->getGet('dateStart') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('dateStart')), "Y-m-d") : "",
            "finish"     => $this->request->getGet('dateEnd') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('dateEnd')), "Y-m-d") : "",
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            'status_lunas' => $this->request->getGet("status_lunas")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->tandaTerimaFakturModel->getInvoiceList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            $jumlahItem = $this->tandaTerimaFakturDetailModel->where('deletedAt', null)->where('tanda_terima_faktur_id', $data->id)->findAll();
            $pembayaranBP = $this->localPOPaymentBPModel
                ->select("sum(amount) as amount, tanda_terima_faktur_id")
                ->where('local_po_payment_bp.tanda_terima_faktur_id', $data->id)
                ->groupBy('local_po_payment_bp.tanda_terima_faktur_id')
                ->first();


            if (empty($addCondition['status_lunas'])) {
                array_push($dataSupplier, [
                    "no"             => $no++,
                    "id"             => encrypt($data->id),
                    "faktur_no"      => $data->faktur_no,
                    "divisi"         => $data->divisi,
                    "supplier_name"  => strtoupper($data->supplierName),
                    "nominal_faktur" => str_replace('Rp', '', toRupiah($data->nominal_faktur, 0, ',', '.')),
                    "jumlah_item"    => count($jumlahItem),
                    "invoice_date"   => $data->invoice_date,
                    "receive_date"   => date('d/m/Y', strtotime($data->receive_date)),
                    "recipient"      => $data->recipient,
                    'is_used' => $this->tandaTerimaFakturModel->getTandaTerimaFakturInPembayaran($data->id) == null ? false : true,
                ]);
            } else {
                if (isset($pembayaranBP) && $addCondition['status_lunas'] == "LUNAS" && $pembayaranBP['amount'] != NULL) {
                    if ($data->nominal_faktur <= $pembayaranBP['amount']) {
                        array_push($dataSupplier, [
                            "no"             => $no++,
                            "id"             => encrypt($data->id),
                            "faktur_no"      => $data->faktur_no,
                            "divisi"         => $data->divisi,
                            "supplier_name"  => strtoupper($data->supplierName),
                            "nominal_faktur" => str_replace('Rp', '', toRupiah($data->nominal_faktur, 0, ',', '.')),
                            "jumlah_item"    => count($jumlahItem),
                            "invoice_date"   => $data->invoice_date,
                            "receive_date"   => date('d/m/Y', strtotime($data->receive_date)),
                            "recipient"      => $data->recipient,
                            'is_used' => $this->tandaTerimaFakturModel->getTandaTerimaFakturInPembayaran($data->id) == null ? false : true,
                        ]);
                    }
                } elseif ($addCondition['status_lunas'] == "BELUM LUNAS") {
                    if (empty($pembayaranBP) || (isset($pembayaranBP['amount']) && $data->nominal_faktur > $pembayaranBP['amount'])) {
                        array_push($dataSupplier, [
                            "no"             => $no++,
                            "id"             => encrypt($data->id),
                            "faktur_no"      => $data->faktur_no,
                            "divisi"         => $data->divisi,
                            "supplier_name"  => strtoupper($data->supplierName),
                            "nominal_faktur" => str_replace('Rp', '', toRupiah($data->nominal_faktur, 0, ',', '.')),
                            "jumlah_item"    => count($jumlahItem),
                            "invoice_date"   => $data->invoice_date,
                            "receive_date"   => date('d/m/Y', strtotime($data->receive_date)),
                            "recipient"      => $data->recipient,
                            'is_used' => $this->tandaTerimaFakturModel->getTandaTerimaFakturInPembayaran($data->id) == null ? false : true,
                        ]);
                    }
                }
            }
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $supplierData['totalData'],
            "recordsFiltered"   => $supplierData['totalFilteredData'],
            "data"              => $dataSupplier,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create()
    {
        $data = [
            'dataSupplier' => $this->supplierModel->getSupplierByType("BAHAN PENOLONG"),
            'noTandaTerima' => $this->tandaTerimaFakturModel->getNo(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'isUsed' => false
        ];
        return view('Purchase/terimaSupplierLokal/bp/form', $data);
    }

    public function update($id)
    {
        $id = decrypt($id);
        $tandaTerimaFakturDetail = $this->tandaTerimaFakturModel->find($id);

        if ($tandaTerimaFakturDetail == null) {
            return redirect()->to('tanda-terima-faktur-lokal-bp');
        }

        $data = [
            'dataSupplier' => $this->supplierModel->getSupplierByType("BAHAN PENOLONG"),
            'dataTandaTerimaFaktur' => $this->tandaTerimaFakturModel->find($id),
            'dataDetailTandaTerimaFaktur' => $this->tandaTerimaFakturDetailModel->getDetail($id),
            'dataPajak' => $this->pajakTandaTerimaFakturModel->where('tanda_terima_faktur_id', $id)->where('deletedAt', null)->findAll(),
            'dataPenerimaanBarang' => $this->tandaTerimaFakturModel->getListPenerimaanBarangLokalBPNotProcessed($tandaTerimaFakturDetail['supplier_id'], $tandaTerimaFakturDetail['divisi_id']),
            'isUsed' => $this->tandaTerimaFakturModel->getTandaTerimaFakturInPembayaran($id) == null ? false : true,
            'divisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('Purchase/terimaSupplierLokal/bp/form', $data);
    }

    public function createAction()
    {
        $fakturNo = $this->request->getVar('no_tanda_terima_faktur');
        $check = $this->tandaTerimaFakturModel->where('faktur_no', $fakturNo)->first();

        if ($check != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Nomor faktur sudah ada",
                'status' => false
            ]);
        }

        $id = $this->tandaTerimaFakturModel->insert([
            'company_id' => $this->this_company_id,
            'supplier_id' => $this->request->getVar('supplier_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'jatuh_tempo' => $this->request->getVar("jatuh_tempo") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("jatuh_tempo")), "Y-m-d") : date('Y-m-d'),
            'faktur_no' => $this->request->getVar('no_tanda_terima_faktur'),
            'nominal_faktur' => repairDouble($this->request->getVar('total_tambahan_potongan')),
            'invoice_date' => date('Y-m-d'),
            'receive_date' => $this->request->getVar("tanggal_terima") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_terima")), "Y-m-d") : "",
            'potongan' => $this->request->getVar('potongan') ? repairDouble($this->request->getVar('potongan')) : 0,
            'tambahan' => $this->request->getVar('tambahan') ? repairDouble($this->request->getVar('tambahan')) : 0,
            'recipient' => $this->request->getVar('penerima'),
            'faktur_type' => 'LOKAL',
            'information' => $this->request->getVar('keterangan'),
            'tipe_bahan' => 'PENOLONG',
            'user_id' => $this->user_id,
        ]);

        // detail faktur
        foreach (json_decode($_POST['listPenerimaanBarang']) as $l) {
            $this->tandaTerimaFakturDetailModel->insert([
                'tanda_terima_faktur_id' => $id,
                'penerimaan_barang_detail_id' => $l->penerimaan_barang_detail_id,
                'lpb_date' => $l->tanggal ? date_format(date_create_from_format("d/m/Y", $l->tanggal), "Y-m-d") : "",
                'lpb_no' => $l->no_penerimaan_barang,
                'item_name' => $l->nama_barang_dok,
                'unit' => $l->kode_satuan,
                'qty' => $l->qty_akan_diterima,
                'po_no' => $l->po_no,
                'price' => ($l->qty_akan_diterima * $l->harga),
                'price_single' => $l->harga
            ]);
        }

        // pajak
        foreach (json_decode($_POST['listPajak']) as $l) {
            $this->pajakTandaTerimaFakturModel->insert([
                'tanda_terima_faktur_id' => $id,
                'tax_inv_date' => $l->tax_inv_date ? date_format(date_create_from_format("d/m/Y", $l->tax_inv_date), "Y-m-d") : "",
                'tax_inv_no' => $l->tax_inv_no,
                'tax_type' => $l->tax_type,
                'tax_amt' => repairDouble($l->tax_amt),
                'tax_status' => $l->tax_status,
                'tax_note' => $l->tax_note,

            ]);
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'message' => "Tanda terima faktur berhasil dibuat",
            'status' => true,
            'id' => encrypt($id)
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->tandaTerimaFakturModel->update($id, [
            'jatuh_tempo' => $this->request->getVar("jatuh_tempo") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("jatuh_tempo")), "Y-m-d") : "",
            'nominal_faktur' => repairDouble($this->request->getVar('total_tambahan_potongan')),
            'invoice_date' => date('Y-m-d'),
            'receive_date' => $this->request->getVar("tanggal_terima") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_terima")), "Y-m-d") : "",
            'potongan' => $this->request->getVar('potongan') ? repairDouble($this->request->getVar('potongan')) : 0,
            'tambahan' => $this->request->getVar('tambahan') ? repairDouble($this->request->getVar('tambahan')) : 0,
            'recipient' => $this->request->getVar('penerima'),
            'faktur_type' => 'LOKAL',
            'information' => $this->request->getVar('keterangan'),
            'tipe_bahan' => 'PENOLONG',
            'user_id' => $this->user_id,
        ]);

        // delete detail first and insert again
        $this->tandaTerimaFakturDetailModel->where('tanda_terima_faktur_id', $id)->delete();
        foreach (json_decode($_POST['listPenerimaanBarang']) as $l) {
            $this->tandaTerimaFakturDetailModel->insert([
                'tanda_terima_faktur_id' => $id,
                'penerimaan_barang_detail_id' => $l->penerimaan_barang_detail_id,
                'lpb_date' => $l->tanggal ? date_format(date_create_from_format("d/m/Y", $l->tanggal), "Y-m-d") : "",
                'lpb_no' => $l->no_penerimaan_barang,
                'item_name' => $l->nama_barang_dok,
                'unit' => $l->kode_satuan,
                'qty' => $l->qty_akan_diterima,
                'po_no' => $l->po_no,
                'price' => ($l->qty_akan_diterima * $l->harga),
                'price_single' => $l->harga
            ]);
        }

        // delete pajak first and insert again
        $this->pajakTandaTerimaFakturModel->where('tanda_terima_faktur_id', $id)->delete();
        foreach (json_decode($_POST['listPajak']) as $l) {
            $this->pajakTandaTerimaFakturModel->insert([
                'tanda_terima_faktur_id' => $id,
                'tax_inv_date' => $l->tax_inv_date ? date_format(date_create_from_format("d/m/Y", $l->tax_inv_date), "Y-m-d") : "",
                'tax_inv_no' => $l->tax_inv_no,
                'tax_type' => $l->tax_type,
                'tax_amt' => repairDouble($l->tax_amt),
                'tax_status' => $l->tax_status,
                'tax_note' => $l->tax_note,

            ]);
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'message' => "Tanda terima faktur berhasil diupdate",
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->tandaTerimaFakturModel->where('id', $id)->delete();
        $this->tandaTerimaFakturDetailModel->where('tanda_terima_faktur_id', $id)->delete();
        $this->pajakTandaTerimaFakturModel->where('tanda_terima_faktur_id', $id)->delete();

        return response()->setJSON([
            'token' => csrf_hash(),
            'message' => "Tanda terima faktur berhasil dihapus",
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        if ($this->tandaTerimaFakturModel->find($id) == null) {
            return redirect()->to('tanda-terima-faktur-lokal-bp');
        }

        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $tandaTerimaFakturDetModel = new TandaTerimaFakturDetailModel();
        $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();

        $filename = "Tanda Terima Penerimaan Lokal Bahan Penolong";

        $data = [];
        $noList = [];
        $itemsList = [];
        $taxList = [];
        $taxReturnList = [];
        $itemTotal = 0;
        $taxTotal = 0;
        $taxReturnTotal = 0;
        $taxPph23 = 0;

        $dataInv = $tandaTerimaFakturModel->asObject()
            ->select("tanda_terima_faktur.*, DATE_FORMAT(tanda_terima_faktur.invoice_date, '%d/%m/%Y') AS invoice_date, suppliers.name AS supplier_name")
            ->join('suppliers', 'suppliers.id = tanda_terima_faktur.supplier_id')
            ->find($id);

        $dataDet = $tandaTerimaFakturDetModel->asObject()
            ->where('tanda_terima_faktur_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        $taxData = $pajakTandaTerimaFakturModel->asObject()
            ->where('tanda_terima_faktur_id', $id)
            ->where('tax_status', 'Pajak dipungut oleh negara')
            ->where('deletedAt', null)
            ->findAll();

        $taxReturnData = $pajakTandaTerimaFakturModel->asObject()
            ->where('tanda_terima_faktur_id', $id)
            ->where('tax_status', 'Pajak dikembalikan lagi')
            ->where('deletedAt', null)
            ->findAll();

        foreach ($dataDet as $det) {
            $noList[] = "$det->lpb_no";
            $itemsList[] = "$det->qty $det->unit $det->item_name";
            $itemTotal += $det->qty * $det->price_single;
        }

        foreach ($taxData as $tax) {
            $taxList[] = "{$tax->tax_type}: {$tax->tax_inv_no}";
            if ($tax->tax_type == "PPh Pasal 23") {
                $taxPph23 += $tax->tax_amt;
            } else {
                $taxTotal += $tax->tax_amt;
            }
        }

        foreach ($taxReturnData as $tax) {
            $taxReturnList[] = "{$tax->tax_type}: {$tax->tax_inv_no}";
            $taxReturnTotal += $tax->tax_amt;
        }

        // \var_dump($itemTotal, $dataInv->tambahan, $taxTotal, $taxReturnTotal);
        // die;

        // $total = ($itemTotal + $dataInv->tambahan + $taxTotal - $dataInv->potongan) - $taxPph23;
        $total = ($itemTotal + $dataInv->tambahan + $taxReturnTotal) - $taxPph23;
        $taxTotal += $dataInv->potongan;

        $data["data"] = $dataInv;
        $data['invNo'] = $dataInv->faktur_no;
        $data["lpbNo"] = implode(', ', $noList);
        $data["itemName"] = implode(', ', $itemsList);
        $data['taxList'] = implode(', ', $taxList);
        $data['taxReturnList'] = implode(', ', $taxReturnList);
        $data["itemTotal"] = $itemTotal;
        $data["potongan"] = $dataInv->potongan;
        $data["tambahan"] = $dataInv->tambahan;
        $data['total'] = $total;
        $data['taxTotal'] = $taxTotal;
        $data['taxReturnTotal'] = $taxReturnTotal;
        $data['terbilang'] = penyebut($total < 0 ? $total * -1 : $total);
        $data['taxReturnTerbilang'] = $taxReturnTotal > 0 ? penyebut($taxReturnTotal) : 'nol';
        $data['taxReturnData'] = $taxReturnData;
        $data['taxData'] = $taxData;

        // dd($data['taxData']);

        $this->dompdf->loadHtml(view('Purchase/terimaSupplierLokal/bp/print', $data));
        $this->dompdf->setPaper('A5', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function deleteDetailTandaTerimaFaktur()
    {
        $tandaTerimaFakturID = $this->request->getVar('tanda_terima_faktur_id');
        $penerimaanBarangDetailID = $this->request->getVar('penerimaan_barang_detail_id');

        $this->tandaTerimaFakturDetailModel->where('tanda_terima_faktur_id', $tandaTerimaFakturID)->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)->delete();

        return response()->setJSON([
            'message' => "Daftar penerimaan barang berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function listPenerimaanBarang()
    {
        $supplierID = $this->request->getVar('supplierID');
        $divisiID = $this->request->getVar('divisiID');

        return response()->setJSON([
            'status' => true,
            'data' => $this->tandaTerimaFakturModel->getListPenerimaanBarangLokalBPNotProcessed($supplierID, $divisiID)
        ]);
    }

    public function generateTandaTerimaFakturNumber()
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();

        $month = idate('m');
        $year = date('y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "/TT/$romanMonth/$year";

        $lastData = $tandaTerimaFakturModel->asObject()
            ->like('faktur_no', $numberTemplate, 'before')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $invNumber = '001' . $numberTemplate;

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->faktur_no);
            $lastIncrement = intval($asd[0]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }

        return response()->setJSON([
            'data' => $invNumber,
            'status' => true
        ]);
    }

    public function historyPembayaran()
    {
        $tandaTerimaFakturid = decrypt($this->request->getVar('id'));

        $history = $this->tandaTerimaFakturModel->getAllTandaTerimaFakturInPembayaran($tandaTerimaFakturid);
        foreach ($history as &$h) {
            $h['payment_date'] = date('d/m/Y', strtotime($h['payment_date']));
            $h['amount'] = number_format($h['amount'], 2);
        }
        return response()->setJSON($history);
    }
}
