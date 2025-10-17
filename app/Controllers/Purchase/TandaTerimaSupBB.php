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
use Exception;

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
            "tipe_bahan"            => "PENOLONG",
            "tanda_terima_faktur.deletedAt" => null,
            "tanda_terima_faktur_detail.deletedAt" => null
        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "start"     => $this->request->getGet('dateStart') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('dateStart')), "Y-m-d") : "",
            "finish"     => $this->request->getGet('dateEnd') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('dateEnd')), "Y-m-d") : "",
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            'status_lunas' => $this->request->getGet("status_lunas"),
            'divisi_id' => $this->request->getGet('divisi_id')
        ];

        // if ($addCondition['status_lunas'] == "BELUM LUNAS") {
        //     $addCondition['start'] = "";
        //     $addCondition['finish'] = "";
        // }

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->tandaTerimaFakturModel->getInvoiceList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            // \var_dump($data);
            // die;
            array_push($dataSupplier, [
                "no"             => $no++,
                "id"             => encrypt($data->id),
                "faktur_no"      => $data->faktur_no,
                "divisi"         => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_divisi_name)),
                "supplier_name"  => strtoupper($data->supplierName),
                "nominal_faktur" => $data->nominal_faktur,
                "jumlah_item"    => $data->jumlah_item,
                "invoice_date"   => date('d/m/Y', strtotime($data->invoice_date)),
                "receive_date"   => date('d/m/Y', strtotime($data->receive_date)),
                "recipient"      => $data->recipient,
                'is_used'        => $data->total_dibayar  == 0 ? false : true,
            ]);
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
            'noTandaTerima' => "",
            'noTandaKeluar' => "",
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'isUsed' => false
        ];
        return view('Purchase/terimaSupplierLokal/bp/form', $data);
    }

    public function update($id)
    {
        $id = decrypt($id);
        $tandaTerimaFaktur = $this->tandaTerimaFakturModel->find($id);

        if ($tandaTerimaFaktur == null) {
            return redirect()->to('tanda-terima-faktur-lokal-bp');
        }

        $selectedDivisi = json_decode($tandaTerimaFaktur['multiple_divisi_id'], true);

        $data = [
            'dataSupplier' => $this->supplierModel->getSupplierByType("BAHAN PENOLONG"),
            'dataTandaTerimaFaktur' => $this->tandaTerimaFakturModel->find($id),
            'dataDetailTandaTerimaFaktur' => $this->tandaTerimaFakturDetailModel->getDetail($id),
            'dataPajak' => $this->pajakTandaTerimaFakturModel->where('tanda_terima_faktur_id', $id)->where('deletedAt', null)->findAll(),
            'dataPenerimaanBarang' => $this->tandaTerimaFakturModel->getListPenerimaanBarangLokalBPNotProcessed(
                $tandaTerimaFaktur['supplier_id'],
                json_decode($tandaTerimaFaktur['multiple_divisi_id']),
                $this->this_company_id
            ),
            'isUsed' => $this->tandaTerimaFakturModel->getTandaTerimaFakturInPembayaran($id) == null ? false : true,
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'selectedDivisi' => count($selectedDivisi) == 0 ? [] : $selectedDivisi
        ];

        return view('Purchase/terimaSupplierLokal/bp/form', $data);
    }

    public function createAction()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            $fakturNo = $this->request->getVar('no_tanda_terima_faktur');
            $checkFakturNo = $this->tandaTerimaFakturModel
                ->where('faktur_no', $fakturNo)
                ->where('company_id', $this->this_company_id)
                ->first();
            $dataListPenerimaanBarang = json_decode($_POST['listPenerimaanBarang']);
            if ($checkFakturNo != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Nomor faktur sudah ada",
                    'status' => false
                ]);
            }

            $id = $this->tandaTerimaFakturModel->insert([
                'company_id' => $this->this_company_id,
                'supplier_id' => $dataListPenerimaanBarang[0]->supplier_id,
                'divisi_id' => null,
                'multiple_divisi_id' => null,
                'multiple_divisi_name' => null,
                'jatuh_tempo' => $this->request->getVar("jatuh_tempo") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("jatuh_tempo")), "Y-m-d") : date('Y-m-d'),
                'faktur_no' => $this->request->getVar('no_tanda_terima_faktur'),
                'faktur_keluar_no' => $this->request->getVar('no_tanda_keluar_faktur'),
                'nominal_faktur' => $this->request->getVar('total_tambahan_potongan'),
                'invoice_date' => date('Y-m-d'),
                'receive_date' => $this->request->getVar("tanggal_terima") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_terima")), "Y-m-d") : "",
                'potongan' => $this->request->getVar('potongan') ? $this->request->getVar('potongan') : 0,
                'tambahan' => $this->request->getVar('tambahan') ? $this->request->getVar('tambahan') : 0,
                'recipient' => $this->request->getVar('penerima'),
                'faktur_type' => 'LOKAL',
                'information_tambahan' => $this->request->getVar('keterangan_tambahan'),
                'information_potongan' => $this->request->getVar('keterangan_potongan'),
                'tipe_bahan' => 'PENOLONG',
                'user_id' => $this->user_id,
            ]);

            // detail faktur
            foreach ($dataListPenerimaanBarang as $l) {
                $this->tandaTerimaFakturDetailModel->insert([
                    'tanda_terima_faktur_id' => $id,
                    'penerimaan_barang_detail_id' => $l->penerimaan_barang_detail_id,
                    'lpb_date' => $l->tanggal ? date_format(date_create_from_format("d/m/Y", $l->tanggal), "Y-m-d") : "",
                    'lpb_no' => $l->no_penerimaan_barang,
                    'item_name' => $l->nama_barang_dok,
                    'unit' => $l->kode_satuan,
                    'qty' => $l->qty_akan_diterima,
                    'po_no' => $l->po_no,
                    'price' => $l->harga_total,
                    'price_single' => $l->harga,
                    'divisi_id' => $l->divisi_id
                ]);
            }

            // pajak
            foreach (json_decode($_POST['listPajak']) as $l) {
                // Get Tax Id
                $taxId = $this->pajakTandaTerimaFakturModel->getTaxId(
                    $l->tax_type,
                    $this->this_company_id
                );
                $this->pajakTandaTerimaFakturModel->insert([
                    'tanda_terima_faktur_id' => $id,
                    'tax_inv_date' => $l->tax_inv_date ? date_format(date_create_from_format("d/m/Y", $l->tax_inv_date), "Y-m-d") : "",
                    'tax_inv_no' => $l->tax_inv_no,
                    'tax_type' => $l->tax_type,
                    'tax_amt' => $l->tax_amt,
                    'tax_status' => $l->tax_status,
                    'tax_note' => $l->tax_note,
                    'tax_id' => $taxId
                ]);
            }

            // Update Multiple DivisiId
            $this->updateMultipleDivisi($id);
            $db->transCommit();

            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Tanda terima faktur berhasil dibuat",
                'status' => true,
                'id' => encrypt($id)
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateAction()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            $id = decrypt($this->request->getVar('id'));
            $fakturNo = $this->request->getVar('no_tanda_terima_faktur');
            $checkFakturNo = $this->tandaTerimaFakturModel
                ->where('faktur_no', $fakturNo)
                ->where('company_id', $this->this_company_id)
                ->where('id <>', $id)
                ->first();

            if ($checkFakturNo != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Nomor faktur sudah ada",
                    'status' => false
                ]);
            }

            $dataListPenerimaanBarang = json_decode($_POST['listPenerimaanBarang']);

            $this->tandaTerimaFakturModel->update($id, [
                'supplier_id' => $this->request->getVar('supplier_id'),
                'faktur_no' => $this->request->getVar('no_tanda_terima_faktur'),
                'faktur_keluar_no' => $this->request->getVar('no_tanda_keluar_faktur'),
                'jatuh_tempo' => $this->request->getVar("jatuh_tempo") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("jatuh_tempo")), "Y-m-d") : "",
                'nominal_faktur' => $this->request->getVar('total_tambahan_potongan'),
                'invoice_date' => date('Y-m-d'),
                'receive_date' => $this->request->getVar("tanggal_terima") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_terima")), "Y-m-d") : "",
                'potongan' => $this->request->getVar('potongan') ? $this->request->getVar('potongan') : 0,
                'tambahan' => $this->request->getVar('tambahan') ? $this->request->getVar('tambahan') : 0,
                'recipient' => $this->request->getVar('penerima'),
                'faktur_type' => 'LOKAL',
                'information_tambahan' => $this->request->getVar('keterangan_tambahan'),
                'information_potongan' => $this->request->getVar('keterangan_potongan'),
                'tipe_bahan' => 'PENOLONG',
            ]);

            // delete detail first and insert again
            $this->tandaTerimaFakturDetailModel->where('tanda_terima_faktur_id', $id)->delete();
            foreach ($dataListPenerimaanBarang as $l) {
                $this->tandaTerimaFakturDetailModel->insert([
                    'tanda_terima_faktur_id' => $id,
                    'penerimaan_barang_detail_id' => $l->penerimaan_barang_detail_id,
                    'lpb_date' => $l->tanggal ? date_format(date_create_from_format("d/m/Y", $l->tanggal), "Y-m-d") : "",
                    'lpb_no' => $l->no_penerimaan_barang,
                    'item_name' => $l->nama_barang_dok,
                    'unit' => $l->kode_satuan,
                    'qty' => $l->qty_akan_diterima,
                    'po_no' => $l->po_no,
                    'price' => $l->harga_total,
                    'price_single' => $l->harga,
                    'divisi_id' => $l->divisi_id
                ]);
            }

            // delete pajak first and insert again
            $this->pajakTandaTerimaFakturModel->where('tanda_terima_faktur_id', $id)->delete();
            foreach (json_decode($_POST['listPajak']) as $l) {
                $taxId = $this->pajakTandaTerimaFakturModel->getTaxId(
                    $l->tax_type,
                    $this->this_company_id
                );
                $this->pajakTandaTerimaFakturModel->insert([
                    'tanda_terima_faktur_id' => $id,
                    'tax_inv_date' => $l->tax_inv_date ? date_format(date_create_from_format("d/m/Y", $l->tax_inv_date), "Y-m-d") : "",
                    'tax_inv_no' => $l->tax_inv_no,
                    'tax_type' => $l->tax_type,
                    'tax_amt' => $l->tax_amt,
                    'tax_status' => $l->tax_status,
                    'tax_note' => $l->tax_note,
                    'tax_id' => $taxId
                ]);
            }

            $this->updateMultipleDivisi($id);
            $db->transCommit();

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Tanda terima faktur berhasil diupdate",
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
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
            ->join('suppliers', 'suppliers.id = tanda_terima_faktur.supplier_id', 'left')
            ->find($id);

        $dataDet = $tandaTerimaFakturDetModel->asObject()
            ->where('tanda_terima_faktur_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        // Pajak Bukti Pengeluaran (Bon Putih)
        $taxData = $pajakTandaTerimaFakturModel->asObject()
            ->where('tanda_terima_faktur_id', $id)
            ->whereIn('tax_type', ['PPN Masukan', 'PPN Masukan 11%'])
            ->where('deletedAt', null)
            ->findAll();

        // Pajak Bukti Pemasukkan (Bon Merah)
        $taxReturnData = $pajakTandaTerimaFakturModel->asObject()
            ->where('tanda_terima_faktur_id', $id)
            ->whereIn('tax_type', ['PPN Masukan', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('deletedAt', null)
            ->findAll();

        foreach ($dataDet as $det) {
            $noList[] = "$det->lpb_no";
            $itemsList[] = "$det->qty $det->unit $det->item_name";
            $itemTotal += $det->price;
        }

        $noList = array_unique($noList);

        // foreach ($taxData as $tax) {
        //     $taxList[] = "{$tax->tax_type}: {$tax->tax_inv_no}";
        //     if ($tax->tax_type == "PPh Pasal 23") {
        //         $taxPph23 += $tax->tax_amt;
        //     } else {
        //         $taxTotal += $tax->tax_amt;
        //     }
        // }

        foreach ($taxData as $tax) {
            $taxList[] = "{$tax->tax_type}: {$tax->tax_inv_no}";
            $taxTotal += $tax->tax_amt;
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
        $data['invKeluarNo'] = $dataInv->faktur_keluar_no;
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

        // Bon Merah Bukti Penerimaan
        $totalDikembalikan = 0;
        foreach ($taxReturnData as $t) :
            $totalDikembalikan += $t->tax_amt;
        endforeach;
        $totalDikembalikan += $data['potongan'];
        // dd($data['taxData']);
        $data['totalDikembalikan'] = $totalDikembalikan;

        $this->dompdf->loadHtml(view('Purchase/terimaSupplierLokal/bp/print', $data));
        $this->dompdf->setPaper('legal', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function deleteDetailTandaTerimaFaktur()
    {
        $tandaTerimaFakturID = $this->request->getVar('tanda_terima_faktur_id');
        $penerimaanBarangDetailID = $this->request->getVar('penerimaan_barang_detail_id');

        // Hitung dulu
        $counter = $this->tandaTerimaFakturDetailModel
            ->where('tanda_terima_faktur_id', $tandaTerimaFakturID)
            ->where('deletedAt', null)
            ->findAll();

        if (count($counter) == 1) {
            return response()->setJSON([
                'message' => "Gagal hapus : minimal harus ada 1 barang di tanda terima faktur",
                'token' => csrf_hash(),
                'status' => false
            ]);
        }

        $this->tandaTerimaFakturDetailModel
            ->where('tanda_terima_faktur_id', $tandaTerimaFakturID)
            ->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)
            ->delete();

        $this->updateMultipleDivisi($tandaTerimaFakturID);

        return response()->setJSON([
            'message' => "Daftar penerimaan barang berhasil dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function listPenerimaanBarang()
    {
        $supplierID = $this->request->getVar('supplierID');
        $divisiID = $this->request->getVar('divisiID');
        $companyID = $this->this_company_id;

        $divisiIds = json_decode($divisiID);
        if (count($divisiIds) == 0) {
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => []
            ]);
        } else {
            $dataTt = $this->tandaTerimaFakturModel->getListPenerimaanBarangLokalBPNotProcessed(
                $supplierID,
                $divisiIds,
                $companyID
            );
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => $dataTt
            ]);
        }
    }

    public function generateTandaTerimaFakturNumber()
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $tanggalTerima = $this->request->getVar('tanggal_terima');

        // Jika tanggal kosong, langsung return
        if (empty($tanggalTerima)) {
            return $this->response->setJSON([
                'data' => '',
                'status' => true
            ]);
        }

        // Pastikan format tanggal valid (misal: dd/mm/yyyy)
        $tanggalParts = explode('/', $tanggalTerima);
        if (count($tanggalParts) !== 3) {
            return $this->response->setJSON([
                'data' => '',
                'status' => false,
                'message' => 'Format tanggal tidak valid. Gunakan dd/mm/yyyy.'
            ]);
        }

        $day = $tanggalParts[0];
        $month = $tanggalParts[1];
        $year = $tanggalParts[2];

        $romanMonth = romanMonthNumber((int)$month);
        $companyId = $this->this_company_id;

        // Tentukan template berdasarkan company
        switch ($companyId) {
            case 1: // KIM 1 (FRZ)
                $numberTemplate = "/F/TT/$romanMonth/" . substr($year, -2);
                break;
            case 2: // KIM 2
                $numberTemplate = "/TT/$romanMonth/" . substr($year, -2);
                break;
            case 15: // GLOBAL
                $numberTemplate = "/G/TT/$romanMonth/" . substr($year, -2);
                break;
            default: // OCS atau lainnya
                $numberTemplate = "/TT/$romanMonth/" . substr($year, -2);
                break;
        }

        // Cari nomor terakhir berdasarkan template
        $lastData = $tandaTerimaFakturModel
            ->select('faktur_no')
            ->where('company_id', $companyId)
            ->like('faktur_no', $numberTemplate, 'before')
            ->where('deletedAt', null)
            ->orderBy('faktur_no', 'DESC')
            ->first();

        // Nomor awal default
        $invNumber = '001' . $numberTemplate;

        if ($lastData && !empty($lastData['faktur_no'])) {
            // Ambil angka urutan terakhir
            $parts = explode('/', $lastData['faktur_no']);
            $lastIncrement = isset($parts[0]) ? (int)$parts[0] : 0;
            $newIncrement = $lastIncrement + 1;
            $paddedNumber = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }

        return $this->response->setJSON([
            'data' => $invNumber,
            'status' => true
        ]);
    }


    public function generateTandaKeluarFakturNumber()
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $tanggalTerima = $this->request->getVar('tanggal_terima');

        // Cek tanggal kosong
        if (empty($tanggalTerima)) {
            return $this->response->setJSON([
                'data' => '',
                'status' => true
            ]);
        }

        // Validasi format tanggal
        $tanggalParts = explode('/', $tanggalTerima);
        if (count($tanggalParts) !== 3) {
            return $this->response->setJSON([
                'data' => '',
                'status' => false,
                'message' => 'Format tanggal tidak valid. Gunakan format dd/mm/yyyy.'
            ]);
        }

        $month = (int)$tanggalParts[1];
        $yearFull = $tanggalParts[2];
        $yearShort = substr($yearFull, -2);
        $romanMonth = romanMonthNumber($month);
        $companyId = $this->this_company_id;

        // Tentukan template berdasarkan company
        switch ($companyId) {
            case 1: // KIM 1 (FRZ)
                $numberTemplate = "/F/TT/$romanMonth/$yearShort";
                break;
            case 2: // KIM 2
                $numberTemplate = "/TT/$romanMonth/$yearShort";
                break;
            case 15: // GLOBAL
                $numberTemplate = "/G/TT/$romanMonth/$yearShort";
                break;
            default: // OCS atau lainnya
                $numberTemplate = "/TT/$romanMonth/$yearShort";
                break;
        }

        // Ambil data terakhir berdasarkan pola faktur_keluar_no
        $lastData = $tandaTerimaFakturModel
            ->select('faktur_keluar_no')
            ->where('company_id', $companyId)
            ->like('faktur_keluar_no', $numberTemplate, 'before')
            ->where('deletedAt', null)
            ->orderBy('faktur_keluar_no', 'DESC')
            ->first();

        // Nomor default
        $invNumber = '001' . $numberTemplate;

        if (!empty($lastData) && !empty($lastData['faktur_keluar_no'])) {
            $parts = explode('/', $lastData['faktur_keluar_no']);
            $lastIncrement = isset($parts[0]) ? (int)$parts[0] : 0;
            $newIncrement = $lastIncrement + 1;
            $paddedNumber = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }

        return $this->response->setJSON([
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

    public function updateMultipleDivisi($id)
    {
        $tandaTerimaFakturDetail = $this->tandaTerimaFakturDetailModel
            ->where('tanda_terima_faktur_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        $divisiIds = array_unique(array_column($tandaTerimaFakturDetail, 'divisi_id'));
        $divisis = $this->divisiModel->whereIn('id', $divisiIds)->findAll();
        $divisiName = array_column($divisis, 'divisi');

        // ubah array ke bentuk string literal seperti [1,2,3]
        $divisiIdString = '[' . implode(',', $divisiIds) . ']';

        // ubah array string ke bentuk ["abc","def"]
        $divisiNameString = '["' . implode('","', $divisiName) . '"]';

        $this->tandaTerimaFakturModel->update($id, [
            'multiple_divisi_id' => $divisiIdString,
            'multiple_divisi_name' => $divisiNameString
        ]);

        return;
    }
}
