<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Models\AccountBarangModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\SupplierModel;
use App\Models\SppModel;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\CompaniesModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SatuansModel;
use App\Models\SppDetailModel;
use App\Models\TaxModel;
use Dompdf\Dompdf;

class POLokalBahanPenolong extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $aMPurchaseOrderModel;
    protected $aMPurchaseOrderDetailModel;
    protected $MetadataModel;
    protected $SppModel;
    protected $supplierModel;
    protected $divisionModel;
    protected $dompdf;
    protected $companyModel;
    protected $barangMasterModel;
    protected $satuanModel;
    protected $penerimaanBarangModel;
    protected $taxModel;
    protected $sppModel;
    protected $sppDetailModel;
    protected $accountBarangModel;
    protected $penerimaanBarangDetailModel;

    protected $jurnalController;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->aMPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->aMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->MetadataModel = new MetadataModel();
        $this->SppModel = new SppModel();
        $this->supplierModel = new SupplierModel();
        $this->divisionModel = new DivisisModel();
        $this->dompdf = new Dompdf();
        $this->companyModel = new CompaniesModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->satuanModel = new SatuansModel();
        $this->taxModel = new TaxModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->jurnalController = new JurnalUmum();
        $this->sppModel = new SppModel();
        $this->sppDetailModel = new SppDetailModel();
        $this->accountBarangModel = new AccountBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
    }

    public function poLokalBahanPenolong()
    {
        return view('Purchase/poLokalBahanPenolong/index');
    }

    public function createPOLokalBahanPenolong()
    {
        $data = [
            "divisi" => $this->divisionModel->getDivisiAccess(),
            "today" => date('Y-m-d'),
            "supplier" => $this->supplierModel->getSupplierByType("Bahan Penolong"),
            "barang" => $this->barangMasterModel->getBarangByTypeWithSpec([
                'barang_master.type_barang'  => 'bahan_penolong',
                'barang_master.company_id' => $this->this_company_id,
                'barang_master.deletedAt' => null,
                'barang_master_spesifikasi.deletedAt' => null
            ]),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "ppn" => $this->taxModel->getTaxByType("ppn"),
            "pph" => $this->taxModel->getTaxByType("pph")
        ];

        return view('Purchase/poLokalBahanPenolong/form', $data);
    }

    public function getDivisionByCompany()
    {
        $companyID = $this->request->getVar('companyID');
        $res = $this->divisionModel->get_by_company_id($companyID);
        return response()->setJSON([
            'data' => $res,
            'token' => csrf_hash(),
        ]);
    }

    public function savePOLokalBahanPenolong()
    {

        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->aMPurchaseOrderModel->get_new_no_po(
                date('m'),
                date('Y'),
                getLastDay(),
                $this->this_company_id
            );
        }

        $first = $this->aMPurchaseOrderModel
            ->where('company_id', $this->this_company_id)
            ->where('po_no', $noPoNew)
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "No Purchase Order Sudah Ada",
                'status' => false
            ]);
        }

        $dataAmPurchaseOrderData = [
            'po_no' => $noPoNew,
            'purchase_request_id' => $this->request->getVar('spp_id'),
            'po_date' => formatDMYtoYMD($this->request->getVar('poDate')),
            'payment_date' => formatDMYtoYMD($this->request->getVar('paymentDate')),
            'po_type' => "Lokal",
            'supplier_id' => $this->request->getVar('supplierID'),
            'company_id' => $this->this_company_id,
            'division_id' => $this->request->getVar('divisionID'),
            'total' => $this->request->getVar('total'),
            'note' => $this->request->getVar('note'),
            'status_closed_spp' => $this->request->getVar('status_closed_spp'),
            "createdBy" => session()->get("login")->user_id,
        ];


        // insert new po
        $poID = $this->aMPurchaseOrderModel->insert($dataAmPurchaseOrderData);
        $aMPurchaseOrderDetailData = json_decode($this->request->getVar('listBarang'));

        if ($dataAmPurchaseOrderData['status_closed_spp']) {
            // CLOSE SPP
            $this->sppModel->update($dataAmPurchaseOrderData['purchase_request_id'], [
                'request_status' => 'finished'
            ]);
        }

        foreach ($aMPurchaseOrderDetailData as $d) {
            $this->aMPurchaseOrderDetailModel->insert([
                'am_purchase_order_id' => $poID,
                'barang_id' => $d->barang_id,
                'spesifikasi_id' => $d->spesifikasi_id,
                'note' => $d->keterangan,
                'unit' => $d->satuan_id,
                'qty' => $d->qty,
                'price' => $d->harga_satuan,
                'disc' => $d->diskon,
                'additional_cost' => $d->biaya_tambahan,
                'ppn' => $d->ppn,
                'pph' => $d->pph,
                'total' => repairDouble($d->total),
                'remaining_qty' => $d->qty
            ]);
            $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisionID'), $d->barang_id, $d->spesifikasi_id, $d->keterangan);
        }


        // $this->jurnalController->insertDataPembelian($poID);

        return response()->setJSON([
            'message' => "PO Bahan penolong berhasil ditambah",
            'status' => true,
            'id' => encrypt($poID)
        ]);
    }

    public function allPOLokalBahanPenolong()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];
        if ($this->is_admin == '1') {
            $condition = [
                'po_type' => "Lokal",
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_orders.company_id' => $this->this_company_id,
                'am_purchase_order_details.deletedAt' => null,
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                'po_type' => "Lokal",
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_orders.company_id' => $this->this_company_id,
                'am_purchase_order_details.deletedAt' => null,
                'purchase_requests.user_id' => $this->this_user_id
            ];
        }


        $addCondition = [
            "is_posted"     => $this->request->getVar("is_posted"),
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        if ($addCondition['is_posted'] == "BELUM POSTING") {
            // Jika Belum Posting Matikan Filter Start Date End Date
            $addCondition['dateStart'] = "";
            $addCondition['dateEnd'] = "";
        }

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $poData = $this->aMPurchaseOrderModel->getPOLokalBPList($condition, $addCondition, $limit, $offset);

        $dataPOLokal = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poData['data'] as $data) {
            $unPostingCheck = $this->penerimaanBarangModel->where('tipe_bahan', "PENOLONG")->where('status_penerimaan', "LOKAL")->where('status_post', "FINISH")->where('company_id', $this->this_company_id)->like('multiple_po_id', $data->id)->first();

            array_push($dataPOLokal, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "spp_no"         => $data->spp_no,
                "po_no"         => $data->po_no,
                "companyName"  => $data->companyName,
                "divisiName"   => $data->divisi,
                "supplierName"  => $data->supplierName,
                "total"         => number_format($data->total, 2),
                "is_posted"     => $data->is_posted,
                "itemCount"     => $data->itemCount,
                "status_penerimaan" => $data->status_penerimaan === "0" ? "OPEN" : "CLOSED",
                "un_posting" => $unPostingCheck == null ? 0 : 1,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $poData['totalData'],
            "recordsFiltered"   => $poData['totalFilteredData'],
            "data"              => $dataPOLokal,
            "payload"           => $payload
        ];


        echo json_encode($data);
        return;
    }

    public function getByIdPOLokalBahanPenolong($id)
    {
        $id = decrypt($id);

        $amPurchaseOrderCondition = [
            'am_purchase_orders.id' => $id,
            'am_purchase_orders.deletedAt' => null
        ];

        $amPurchaseOrderDetailCondition = [
            'am_purchase_order_id' => $id,
            'am_purchase_order_details.deletedAt' => null
        ];

        $selectQryPurchaseOrderDetail = "
            am_purchase_order_details.barang_id,
            am_purchase_order_details.spesifikasi_id,
            am_purchase_order_details.additional_cost AS biaya_tambahan,
            am_purchase_order_details.disc AS diskon,
            am_purchase_order_details.price AS harga_satuan,
            am_purchase_order_details.note AS keterangan,
            barang_master.kode_barang,
            barang_master.barang_name AS nama_barang,
            barang_master_spesifikasi.spesifikasi AS spesifikasi_name,
            satuans.nama_satuan,
            am_purchase_order_details.pph,
            am_purchase_order_details.ppn,
            am_purchase_order_details.qty,
            am_purchase_order_details.unit AS satuan_id,

        ";

        $poDetail = $this->aMPurchaseOrderModel
            ->select('am_purchase_orders.*,purchase_requests.spp_no')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->where($amPurchaseOrderCondition)
            ->first();
        $listBarang = $this->aMPurchaseOrderDetailModel
            ->select($selectQryPurchaseOrderDetail)
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id')
            ->join('barang_master_spesifikasi', 'am_purchase_order_details.spesifikasi_id = barang_master_spesifikasi.id')
            ->join('satuans', 'barang_master_spesifikasi.satuan_1 = satuans.id')
            ->where($amPurchaseOrderDetailCondition)
            ->findAll();

        $checkLpb = $this->penerimaanBarangModel->where('tipe_bahan', "PENOLONG")->where('status_penerimaan', "LOKAL")->where('company_id', $this->this_company_id)->like('multiple_po_id', $id)->first();

        if ($poDetail == null) {
            return redirect()->to('po-lokal-bahan-penolong');
        }

        $unPostingCheck = $this->penerimaanBarangModel->where('tipe_bahan', "PENOLONG")->where('status_penerimaan', "LOKAL")->like('multiple_po_id', $id)->first();

        $data = [
            "divisi" => $this->divisionModel->getDivisiAccess(),
            "today" => date('Y-m-d'),
            "supplier" => $this->supplierModel->getSupplierByType("Bahan Penolong"),
            "barang" => $this->barangMasterModel->getBarangByTypeWithSpec([
                'barang_master.type_barang'  => 'bahan_penolong',
                'barang_master.company_id' => $this->this_company_id,
                'barang_master.deletedAt' => null,
                'barang_master_spesifikasi.deletedAt' => null
            ]),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "ppn" => $this->taxModel->getTaxByType("ppn"),
            "pph" => $this->taxModel->getTaxByType("pph"),
            "poDetail" => $poDetail,
            "unPosting" => $unPostingCheck == null ? 0 : 1,
            "listBarang" => $listBarang,
            'checkLpb' => $checkLpb // Cek apakah PO sudah dibuat LPB atau belum, jika sudah hanya diizinkan update harga aja jika belum bisa update qty

        ];

        $data["dataListSPP"] = $this->sppModel->where('id', $poDetail['purchase_request_id'])->findAll();
        return view('Purchase/poLokalBahanPenolong/form', $data);
    }

    public function updatePOLokalBahanPenolong()
    {
        $id = decrypt($this->request->getVar('id'));

        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->aMPurchaseOrderModel->get_new_no_po(
                date('m'),
                date('Y'),
                getLastDay(),
                $this->this_company_id
            );
        }

        $first = $this->aMPurchaseOrderModel
            ->where('company_id', $this->this_company_id)
            ->where('po_no', $noPoNew)
            ->where('id !=', $id)
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "No Purchase Order Sudah Ada",
                'status' => false
            ]);
        }

        $firstData = $this->aMPurchaseOrderModel->find($id);

        $this->sppModel->update($firstData['purchase_request_id'], [
            'request_status' => 'waiting'
        ]);

        $dataAmPurchaseOrderData = [
            'po_no' => $noPoNew,
            'po_date' => formatDMYtoYMD($this->request->getVar('poDate')),
            'payment_date' => formatDMYtoYMD($this->request->getVar('paymentDate')),
            'po_type' => "Lokal",
            'purchase_request_id' => !empty($this->request->getVar('spp_id')) ? $this->request->getVar('spp_id') : $firstData['purchase_request_id'],
            'supplier_id' => $this->request->getVar('supplierID'),
            'company_id' => $this->this_company_id,
            'division_id' => $this->request->getVar('divisionID'),
            'total' => $this->request->getVar('total'),
            'note' => $this->request->getVar('note'),
            'status_closed_spp' => $this->request->getVar('status_closed_spp'),
            "createdBy" => session()->get("login")->user_id,
        ];

        $this->aMPurchaseOrderModel->update($id, $dataAmPurchaseOrderData);

        if ($dataAmPurchaseOrderData['status_closed_spp']) {
            // CLOSE SPP
            $this->sppModel->update($dataAmPurchaseOrderData['purchase_request_id'], [
                'request_status' => 'finished'
            ]);
        }

        // insert again
        $aMPurchaseOrderDetailData = json_decode($this->request->getVar('listBarang'));
        // get all id detail
        $id_detail_all = [];

        foreach ($aMPurchaseOrderDetailData as $d) {
            // UPDATE
            $check = $this->aMPurchaseOrderDetailModel
                ->where('am_purchase_order_details.am_purchase_order_id', $id)
                ->where('spesifikasi_id', $d->spesifikasi_id)
                ->where('barang_id', $d->barang_id)
                ->where('note', $d->keterangan)
                ->first();

            if ($check != null) {
                // UPDATE
                $this->aMPurchaseOrderDetailModel->update($check['id'], [
                    'am_purchase_order_id' => $id,
                    'barang_id' => $d->barang_id,
                    'spesifikasi_id' => $d->spesifikasi_id,
                    'note' => $d->keterangan,
                    'unit' => $d->satuan_id,
                    'qty' => $d->qty,
                    'price' => $d->harga_satuan,
                    'disc' => $d->diskon,
                    'additional_cost' => $d->biaya_tambahan,
                    'ppn' => $d->ppn,
                    'pph' => $d->pph,
                    'total' => ($d->total),
                    'remaining_qty' => $d->qty
                ]);
                array_push($id_detail_all, $check['id']);

                $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisionID'), $d->barang_id, $d->spesifikasi_id, $d->keterangan);
            } else {
                // NEW BARANG
                // DELETE
                $this->aMPurchaseOrderDetailModel
                    ->where('am_purchase_order_details.am_purchase_order_id', $id)
                    ->where('spesifikasi_id', $d->spesifikasi_id)
                    ->where('barang_id', $d->barang_id)
                    ->delete();

                // INSERT NEW
                $id_detail_new = $this->aMPurchaseOrderDetailModel->insert([
                    'am_purchase_order_id' => $id,
                    'barang_id' => $d->barang_id,
                    'spesifikasi_id' => $d->spesifikasi_id,
                    'note' => $d->keterangan,
                    'unit' => $d->satuan_id,
                    'qty' => $d->qty,
                    'price' => $d->harga_satuan,
                    'disc' => $d->diskon,
                    'additional_cost' => $d->biaya_tambahan,
                    'ppn' => $d->ppn,
                    'pph' => $d->pph,
                    'total' => ($d->total),
                    'remaining_qty' => $d->qty
                ]);
                array_push($id_detail_all, $id_detail_new);

                $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisionID'), $d->barang_id, $d->spesifikasi_id, $d->keterangan);
            }
        }

        // Auto Update Harga di LPB
        $penerimaanBarang = $this->penerimaanBarangModel
            ->where('penerimaan_barang.deletedAt', null)
            ->where('tipe_bahan', "PENOLONG")
            ->where('status_penerimaan', "LOKAL")
            ->where('company_id', $this->this_company_id)
            ->like('multiple_po_id', $id)
            ->findAll();
        $penerimaanBarangIds = array();
        foreach ($penerimaanBarang as $p) {
            array_push($penerimaanBarangIds, $p['id']);
        }

        if (count($penerimaanBarangIds) != 0) {

            $poDetail = $this->aMPurchaseOrderDetailModel
                ->where('am_purchase_order_details.deletedAt', null)
                ->where('am_purchase_order_details.am_purchase_order_id', $id)
                ->findAll();

            foreach ($poDetail as $p) {

                // PO SUDAH DIBUATKAN LPB NYA
                $penerimaanBarangDetail = $this->penerimaanBarangDetailModel
                    ->whereIn('penerimaan_barang_id', $penerimaanBarangIds)
                    ->where('purchase_order_id', $p['am_purchase_order_id'])
                    ->where('purchase_order_details_id', $p['id'])
                    ->where('penerimaan_barang_detail.deletedAt', null)
                    ->findAll();

                foreach ($penerimaanBarangDetail as $pbd) {
                    $this->penerimaanBarangDetailModel->update($pbd['id'], [
                        'harga' => $p['price'],
                        'sub_total' => ($p['price'] * $pbd['jml_masuk_konversi']),
                    ]);
                }
            }
        }

        $this->aMPurchaseOrderDetailModel
            ->where('am_purchase_order_id', $id)
            ->whereNotIn('id', $id_detail_all)
            ->delete();

        return response()->setJSON([
            'message' => "PO Bahan penolong berhasil diubah",
            'status' => true,
        ]);
    }

    public function updateStatusPOLokalBahanPenolong()
    {
        $data = [
            "status"    => true,
            "message"   => "Status Posting PO Lokal BP Berhasil Diperbaruhi",
            'token'     => csrf_hash()
        ];

        $id = decrypt($this->request->getPost("id"));
        $status = $this->request->getVar('status');

        // $result = $this->jurnalController->insertDataPembelian($id, "BAHAN PENOLONG", "LOKAL", "pembelian");
        // if ($result) {
        //     $responseBody = json_decode($result->getBody(), true);
        //     if ($responseBody && isset($responseBody['status'])) {
        //         $data["status"] =  false;
        //         $data["message"] = $responseBody['message'];
        //         $data["token"] = csrf_hash();
        //     }
        // } else {
        $this->aMPurchaseOrderModel->update($id, [
            'is_posted' => $status == 1 ? true : false
        ]);
        // }


        return response()->setJSON($data);
    }

    public function closePOLokalBahanPenolong()
    {
        $id = decrypt($this->request->getPost("id"));

        $this->aMPurchaseOrderModel->update($id, [
            'status_penerimaan' => true
        ]);

        return response()->setJSON([
            'message' => "Close PO berhasil",
            'status' => true,
        ]);
    }

    public function deletePOLokalBahanPenolong()
    {
        $id = decrypt($this->request->getVar("id"));

        $firstData = $this->aMPurchaseOrderModel->find($id);
        $checkLpb = $this->penerimaanBarangModel->where('tipe_bahan', "PENOLONG")->where('status_penerimaan', "LOKAL")->where('company_id', $this->this_company_id)->like('multiple_po_id', $firstData['id'])->first();

        if ($checkLpb != null) {
            return response()->setJSON([
                'message' => "Gagal Hapus, PO Sudah Dibuatkan LPB dengan Nomor : " . $checkLpb['no_penerimaan_barang'],
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $this->sppModel->update($firstData['purchase_request_id'], [
            'request_status' => 'waiting'
        ]);

        $this->aMPurchaseOrderModel->delete($id);
        $this->aMPurchaseOrderDetailModel->where('am_purchase_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "PO berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function print($id = null)
    {
        if ($id) {
            $filename = "PO Lokal Bahan Penolong";
            $id = decrypt($id);

            if (!empty($id)) {
                $dataBPLokal = $this->aMPurchaseOrderModel->getPOById($id);
                $dataBPLokalDetail = $this->aMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);
                foreach (array_keys($dataBPLokalDetail) as $key) {
                    $dataBPLokalDetail[$key] = (object)$dataBPLokalDetail[$key];
                }

                $no = 0;
                $totalPrice = 0;
                $totalDisc = 0;
                $totalPpn = 0;
                $totalTambahan = 0;
                $keterangan = [];

                foreach ($dataBPLokalDetail as $value) {
                    $no++;
                    $value->no = $no;
                    $totalan = $value->price * formatter($value->qty, "STR_TO_FLOAT");
                    $value->nilaiPpn = number_format($totalan * (float)$value->ppnValue / 100);
                    $value->nilaiPph = number_format($totalan * (float)$value->pphValue / 100);
                    $totalTambahan += formatter($value->additional_cost, "CURR_TO_INT");
                    $totalPrice += $value->totalPriceWithoutAdditional;
                    $totalDisc += ($totalan) * (float)$value->disc / 100;
                    $totalPpn += $totalan * (float)$value->ppnValue / 100;
                    $keterangan[] = $value->note;
                }

                $dataBPLokal->totalTambahan = number_format(formatter($totalTambahan, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPrice = number_format(formatter($totalPrice, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalDisc = number_format(formatter($totalDisc, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPpn = number_format(formatter($totalPpn, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPo = number_format(formatter(($totalTambahan + $totalPrice - $totalDisc + $totalPpn), "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->keterangan = implode(",", array_unique($keterangan));
                $dataBPLokal->jatuhTempoHari = \totalDayInRange($dataBPLokal->po_date, $dataBPLokal->payment_date);
                $data["dataPOLokal"] = $dataBPLokal;
                $data["dataPOLokal"]->am_purchase_order_details = $dataBPLokalDetail;
            }
            $this->dompdf->loadHtml(view('Purchase/poLokalBahanPenolong/print', $data));
            $this->dompdf->setPaper('A4', 'landscape');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function getHistoriHarga()
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $res = $amPurchaseOrderModel->historiHargaPOBahanPenolongFirst(
            $this->request->getVar('id'),
            $this->request->getVar('spesifikasi_id'),
            "Lokal",
            $this->this_company_id
        );
        return response()->setJSON(['res' => $res]);
    }

    public function dropdownHistoriPenerimaanBarang()
    {
        $id = decrypt($this->request->getVar('id'));
        $listBarang = $this->aMPurchaseOrderDetailModel
            ->select(
                '
            am_purchase_order_details.qty_diterima AS diterima, 
            am_purchase_order_details.remaining_qty AS sisa, 
            CONCAT(barang_master.barang_name, " - ",barang_master_spesifikasi.spesifikasi) AS nama_barang, 
            barang_master.kode_barang, 
            am_purchase_order_details.qty'
            )
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->where('am_purchase_order_details.am_purchase_order_id', $id)
            ->where('am_purchase_order_details.deletedAt', null)
            ->findAll();

        $poDetail = $this->aMPurchaseOrderModel->select('am_purchase_orders.po_no, suppliers.name AS supplierName')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->where('am_purchase_orders.id', $id)
            ->where('am_purchase_orders.deletedAt', null)
            ->first();

        $lpbDetail = $this->penerimaanBarangModel->like('multiple_po_id', $id)->where('deletedAt', null)->findAll();

        $lpbNo = [];
        foreach ($lpbDetail as $l) {
            $lpbNo[] = $l['no_penerimaan_barang'];
        }

        return response()->setJSON([
            'lpb_no' => count($lpbNo) == 0 ? "BELUM ADA LPB" : str_replace(['[', ']', '"', "\\"], '', json_encode($lpbNo)),
            'po_detail' => $poDetail,
            'list_barang' => $listBarang,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownPOLokalBahanPenolong()
    {
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");
        $dataPOLokal = $this->aMPurchaseOrderModel->getNoPenerimaanBarang("LOKAL", $id);
        $data = [
            "data" => $dataPOLokal
        ];
        echo json_encode($data);
        return;
    }

    public function dropdownSPPBahanPenolong()
    {
        $result = [];
        $sppIdArr = [];

        $id = formatter($this->request->getGet("id"), "STR_TO_INT");
        $dataPOLokal = $this->aMPurchaseOrderModel->getNoPenerimaanBarang("LOKAL", $id);

        foreach ($dataPOLokal as $d) {
            array_push($sppIdArr, $d['purchase_request_id']);
        }

        if (empty($sppIdArr)) {
            return response()->setJSON(['data' => []]);
        } else {
            $result = $this->sppModel->whereIn('id', $sppIdArr)->findAll();
            return response()->setJSON(['data' => $result]);
        }
    }

    public function dropdownPOBySpp()
    {
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");
        $spp_id = json_decode($this->request->getVar('spp_id'));

        if (count($spp_id) == 0) {
            return response()->setJSON([
                'data' => []
            ]);
        } else {
            $dataPOLokal = $this->aMPurchaseOrderModel->getNoPenerimaanBarangBySPP("LOKAL", $id, $spp_id);

            return response()->setJSON([
                'data' => $dataPOLokal
            ]);
        }
    }

    public function dropdownBarangPOLokalBahanPenolong()
    {
        $id = $this->request->getGet("id");

        $dataPOLokal = $this->aMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownGetSppDetail()
    {
        $id = $this->request->getVar('spp_id');
        $poId = decrypt($this->request->getVar('po_id'));
        $spp = $this->sppModel->find($id);

        if ($spp == null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => 'Spp tidak ada'
            ]);
        }

        $condition = [
            'purchase_request_details.purchase_request_id' => $id,
            'purchase_request_details.deletedAt' => null
        ];

        $selectQry = "
            purchase_request_details.*,
            barang_master.barang_name,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan
        ";

        $sppDetail = $this->sppDetailModel->select($selectQry)
            ->join('barang_master', 'purchase_request_details.barang1_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'purchase_request_details.barang2_id=barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = purchase_request_details.unit', 'left')
            ->where($condition)->findAll();

        $result = [];

        foreach ($sppDetail as $s) {

            $totalQtyPO = $this->aMPurchaseOrderDetailModel->select('SUM(qty) AS qty_po')
                ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id')
                ->where('am_purchase_orders.deletedAt', null)
                ->where('am_purchase_order_details.deletedAt', null)
                ->where('am_purchase_orders.purchase_request_id', $id)
                ->where('barang_id', $s['barang1_id'])
                ->where('spesifikasi_id', $s['barang2_id'])
                ->where('am_purchase_order_details.note', $s['note'])
                ->first();

            $totalQtyPO = ($totalQtyPO == null) ? 0 : $totalQtyPO['qty_po'];
            $totalQtySisa = $s['qty'] - $totalQtyPO;

            if ($totalQtySisa > 0) {
                $hargaTerakhir = $this->aMPurchaseOrderModel->historiHargaPOBahanPenolongFirst(
                    $s['barang1_id'],
                    $s['barang2_id'],
                    "Lokal",
                    $this->this_company_id,
                );

                $result[] = [
                    'barang_id' => $s['barang1_id'],
                    'spesifikasi_id' => $s['barang2_id'],
                    'kode_barang' => $s['kode_barang'],
                    'nama_barang' => $s['barang_name'] . " " . $s['spesifikasi'],
                    'satuan_id' => $s['unit'],
                    'nama_satuan' => $s['kode_satuan'],
                    'harga_satuan' => round($hargaTerakhir['hargaTerakhirNumber'], 2),
                    'qty' => $totalQtySisa,
                    'diskon' => 0,
                    'biaya_tambahan' => 0,
                    'total' => (round($hargaTerakhir['hargaTerakhirNumber'], 2) * $totalQtySisa),
                    'keterangan' => $s['note'],
                    'ppn' => '',
                    'pph' => ''
                ];
            }
        }

        if (!empty($poId)) {
            $poDetail = $this->aMPurchaseOrderDetailModel
                ->select('
                am_purchase_order_details.*,
                barang_master.barang_name,
                barang_master.kode_barang,
                barang_master_spesifikasi.spesifikasi,
                satuans.kode_satuan
            ')
                ->join('barang_master', 'am_purchase_order_details.barang_id = barang_master.id', 'left')
                ->join('barang_master_spesifikasi', 'am_purchase_order_details.spesifikasi_id=barang_master_spesifikasi.id', 'left')
                ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
                ->where('am_purchase_order_details.deletedAt', null)
                ->where('am_purchase_order_id', $poId)
                ->findAll();

            foreach ($poDetail as $s) {
                $result[] = [
                    'barang_id' => $s['barang_id'],
                    'spesifikasi_id' => $s['spesifikasi_id'],
                    'kode_barang' => $s['kode_barang'],
                    'nama_barang' => $s['barang_name'] . " " . $s['spesifikasi'],
                    'satuan_id' => $s['unit'],
                    'nama_satuan' => $s['kode_satuan'],
                    'harga_satuan' => $s['price'],
                    'qty' => $s['qty'],
                    'diskon' => $s['disc'],
                    'biaya_tambahan' => $s['additional_cost'],
                    'total' => $s['total'],
                    'keterangan' => $s['note'],
                    'ppn' => $s['ppn'] == null ? '' : $s['ppn'],
                    'pph' => $s['pph'] == null ? '' : $s['pph']
                ];
            }
        }


        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function generateNoPO()
    {
        $noPoNew =  $this->aMPurchaseOrderModel->get_new_no_po(
            date('m'),
            date('Y'),
            getLastDay(),
            $this->this_company_id
        );

        return json_encode($noPoNew);
    }

    public function dropdownBarang()
    {
        $barang = $this->barangMasterModel->getBarangByTypeWithSpec([
            'barang_master.type_barang'  => 'bahan_penolong',
            'barang_master.company_id' => $this->this_company_id,
            'barang_master.deletedAt' => null,
            'barang_master_spesifikasi.deletedAt' => null
        ]);

        return response()->setJSON([
            'data' => $barang,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
}
