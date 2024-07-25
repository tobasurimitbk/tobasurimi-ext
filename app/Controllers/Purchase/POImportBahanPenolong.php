<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;

use App\Models\BarangModel;
use App\Models\MetadataModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\SppModel;
use App\Models\SupplierModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SatuansModel;
use App\Models\SppDetailModel;
use Dompdf\Dompdf;

class POImportBahanPenolong extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $user_id;
    protected $barangModel;
    protected $metadataModel;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $supplierModel;
    protected $companyModel;
    protected $barangMasterModel;
    protected $satuanModel;
    protected $divisionModel;
    protected $penerimaanBarangModel;
    protected $dompdf;
    protected $sppModel;
    protected $sppDetailModel;
    protected $jurnalController;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->user_id = session()->get("login")->user_id;
        $this->barangModel = new BarangModel();
        $this->metadataModel = new MetadataModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->sppModel = new SppModel();
        $this->supplierModel = new SupplierModel();
        $this->companyModel = new CompaniesModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->satuanModel = new SatuansModel();
        $this->divisionModel = new DivisisModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->dompdf = new Dompdf();
        $this->jurnalController = new JurnalUmum();
        $this->sppModel = new SppModel();
        $this->sppDetailModel = new SppDetailModel();
    }

    public function poImportBahanPenolong()
    {
        return view('Purchase/poImportBahanPenolong/index');
    }

    public function createPOImportBahanPenolong()
    {
        $data = [
            "divisi" => $this->divisionModel->getDivisiAccess(),
            "today" => date("d/m/Y"),
            "dataSupplier" =>  $this->supplierModel->getSupplierByType('INTERNASIONAL'),
            "barang" => $this->barangMasterModel->getBarangByTypeWithSpec([
                'barang_master.type_barang'  => 'bahan_penolong',
                'barang_master.company_id' => $this->this_company_id,
                'barang_master.deletedAt' => null,
                'barang_master_spesifikasi.deletedAt' => null
            ]),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "dataShipment" => $this->metadataModel->get_by_name('Shipment')
        ];

        return view('Purchase/poImportBahanPenolong/form', $data);
    }

    public function getByIdPOImportBahanPenolong($id = null)
    {
        $id = decrypt($id);
        $unPostingCheck = $this->penerimaanBarangModel->where('tipe_bahan', "PENOLONG")->where('status_penerimaan', "IMPORT")->like('multiple_po_id', $id)->first();

        $data = [
            "divisi" => $this->divisionModel->getDivisiAccess(),
            "today" => date("d/m/Y"),
            "dataSupplier" =>  $this->supplierModel->getSupplierByType('INTERNASIONAL'),
            "barang" => $this->barangMasterModel->getBarangByTypeWithSpec([
                'barang_master.type_barang'  => 'bahan_penolong',
                'barang_master.company_id' => $this->this_company_id,
                'barang_master.deletedAt' => null,
                'barang_master_spesifikasi.deletedAt' => null
            ]),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "dataShipment" => $this->metadataModel->get_by_name('Shipment'),
            "dataPOImport" => $this->amPurchaseOrderModel->getPOById($id),
            "dataPOImportDetail" => $this->amPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id),
            "unPosting" => $unPostingCheck == null ? 0 : 1,
        ];

        if ($data["dataPOImport"] == null) {
            return redirect()->to('po-import-bahan-penolong');
        }

        $data["dataListSPP"] = $this->sppModel->where('request_status', "waiting")->where('is_posted', '1')->where('spp_type', 'Import BP')->where('divisi_id', $data['dataPOImport']->division_id)->where('deletedAt', null)->where('spp_type', "Import BP")->findAll();

        return view('Purchase/poImportBahanPenolong/form', $data);
    }

    public function allPOImportBahanPenolong()
    {
        $payload = [
            "pageSize" => $this->request->getVar("length"),
            "currentPage" => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
            "dateStart" => $this->request->getVar("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd" => $this->request->getVar("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            "am_purchase_orders.po_type"        => "Import",
            "am_purchase_order_details.deletedAt" => null,
            "am_purchase_orders.company_id" => $this->this_company_id,
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $poImportData = $this->amPurchaseOrderModel->getPOList($condition, $addCondition, $limit, $offset);

        $dataPOImport = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poImportData['data'] as $data) {
            $unPostingCheck = $this->penerimaanBarangModel->where('tipe_bahan', "PENOLONG")->where('status_penerimaan', "IMPORT")->like('multiple_po_id', $data->id)->first();

            array_push($dataPOImport, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "po_no"         => $data->po_no,
                "supplierName"  => strtoupper($data->supplierName),
                "divisi"        => strtoupper($data->divisi),
                "total"         => number_format(formatter($data->total, "STR_TO_FLOAT"), 2, '.', ','),
                "currencyName"  => $data->currencyName,
                "itemCount"     => $data->itemCount,
                "is_posted"     => $data->is_posted,
                "status_penerimaan" => $data->status_penerimaan === "0" ? "OPEN" : "CLOSED",
                "un_posting" => $unPostingCheck == null ? 0 : 1,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $poImportData['totalData'],
            "recordsFiltered"   => $poImportData['totalFilteredData'],
            "data"              => $dataPOImport,
            "payload"           => $payload
        ];

        return response()->setJson($data);
    }

    public function savePOImportBahanPenolong()
    {

        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->amPurchaseOrderModel->get_new_no_po_import(
                date('m'),
                date('y'),
                getLastDay(),
                $this->this_company_id
            );
        }

        $first = $this->amPurchaseOrderModel
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

        // create new po
        $poID = $this->amPurchaseOrderModel->insert([
            'company_id' => $this->this_company_id,
            'division_id' => $this->request->getVar('divisionID'),
            'purchase_request_id' => $this->request->getVar('spp_id'),
            'po_no' => $noPoNew,
            'po_date' => $this->request->getPost("poDate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("poDate")))) : "",
            'po_type' => "Import",
            'potongan_harga' => $this->request->getVar('potongan_harga'),
            'currency' => formatter($this->request->getVar("currency"), "STR_TO_INT"),
            'supplier_id' => $this->request->getVar('supplierID'),
            'total' => $this->request->getVar('total') - $this->request->getVar('potongan_harga'),
            'payment_term' => $this->request->getVar('paymentTerm'),
            'payment_date' =>  $this->request->getPost("paymentDate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("paymentDate")))) : "",
            'note' => $this->request->getVar('note'),
            'shipper' => $this->request->getVar('shipper'),
            'consigne' => $this->request->getVar('consigne'),
            'port_origin' => $this->request->getVar('portOrigin'),
            'port_destination' => $this->request->getVar('portDestination'),
            'location_transaction' => $this->request->getVar('locationTransaction'),
            'shipment' => $this->request->getVar('shipment'),
            'latest_shipment_date' => $this->request->getVar('latestShipmentDate')  ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("latestShipmentDate")))) : "",
            'attn' => $this->request->getVar('attn'),
            'createdBy' => session()->get("login")->user_id,
            "direktur" => $this->request->getVar('direktur')
        ]);

        $barang = json_decode($this->request->getVar("listBarang"));

        foreach ($barang as $b) {

            $this->amPurchaseOrderDetailModel->insert([
                'am_purchase_order_id' => $poID,
                'barang_id' => $b->barang_id,
                'spesifikasi_id' => $b->spesifikasi_id,
                'unit' => $b->satuan_id,
                'qty' => $b->qty,
                'price' => $b->harga_satuan,
                'disc' => $b->diskon,
                'additional_cost' => $b->biaya_tambahan,
                'remaining_qty' => $b->qty,
                'total' => repairDouble($b->total),
            ]);
        }

        $this->sppModel->update($this->request->getVar('spp_id'), [
            'request_status' => 'finished'
        ]);

        return response()->setJSON([
            "status" => true,
            "message" => "Data PO Import BP Berhasil Disimpan",
            'token' => csrf_hash(),
            'id' => encrypt($poID)
        ]);
    }

    public function updatePOImportBahanPenolong()
    {
        $id = decrypt($this->request->getPost("id"));

        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->amPurchaseOrderModel->get_new_no_po_import(
                date('m'),
                date('Y'),
                getLastDay(),
                $this->this_company_id
            );
        }


        $first = $this->amPurchaseOrderModel
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

        $firstData = $this->amPurchaseOrderModel->find($id);

        if (empty($this->request->getVar('spp_id'))) {
            $sppID = $firstData['purchase_request_id'];
        } else {
            $sppID = $this->request->getVar('spp_id');
        }

        $this->sppModel->update($sppID, [
            'request_status' => 'waiting'
        ]);

        $this->amPurchaseOrderModel->update($id, [
            'company_id' => $this->this_company_id,
            'division_id' => $this->request->getVar('divisionID'),
            'purchase_request_id' => $sppID,
            'po_type' => "Import",
            'po_no' => $noPoNew,
            'currency' => formatter($this->request->getVar("currency"), "STR_TO_INT"),
            'supplier_id' => $this->request->getVar('supplierID'),
            'total' => $this->request->getVar('total') - $this->request->getVar('potongan_harga'),
            'potongan_harga' => $this->request->getVar('potongan_harga'),
            'payment_term' => $this->request->getVar('paymentTerm'),
            'payment_date' =>  $this->request->getPost("paymentDate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("paymentDate")))) : "",
            'note' => $this->request->getVar('note'),
            'shipper' => $this->request->getVar('shipper'),
            'consigne' => $this->request->getVar('consigne'),
            'port_origin' => $this->request->getVar('portOrigin'),
            'port_destination' => $this->request->getVar('portDestination'),
            'location_transaction' => $this->request->getVar('locationTransaction'),
            'shipment' => $this->request->getVar('shipment'),
            'latest_shipment_date' => $this->request->getVar('latestShipmentDate')  ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("latestShipmentDate")))) : "",
            'attn' => $this->request->getVar('attn'),
            "direktur" => $this->request->getVar('direktur')
        ]);

        // Insert Again
        $barang = json_decode($this->request->getVar("listBarang"));
        // get all id detail
        $id_detail_all = [];

        foreach ($barang as $b) {
            // UPDATE
            $check = $this->amPurchaseOrderDetailModel
                ->where('am_purchase_order_details.am_purchase_order_id', $id)
                ->where('spesifikasi_id', $b->spesifikasi_id)
                ->where('barang_id', $b->barang_id)
                ->first();

            if ($check != null) {
                // UPDATE
                $this->amPurchaseOrderDetailModel->update($check['id'], [
                    'am_purchase_order_id' => $id,
                    'barang_id' => $b->barang_id,
                    'spesifikasi_id' => $b->spesifikasi_id,
                    'unit' => $b->satuan_id,
                    'qty' => $b->qty,
                    'price' => $b->harga_satuan,
                    'disc' => $b->diskon,
                    'additional_cost' => $b->biaya_tambahan,
                    'remaining_qty' => $b->qty,
                    'total' => repairDouble($b->total),
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // NEW BARANG
                // DELETE
                $this->amPurchaseOrderDetailModel
                    ->where('am_purchase_order_details.am_purchase_order_id', $id)
                    ->where('spesifikasi_id', $b->spesifikasi_id)
                    ->where('barang_id', $b->barang_id)
                    ->delete();

                // INSERT NEW
                $id_detail_new = $this->amPurchaseOrderDetailModel->insert([
                    'am_purchase_order_id' => $id,
                    'barang_id' => $b->barang_id,
                    'spesifikasi_id' => $b->spesifikasi_id,
                    'unit' => $b->satuan_id,
                    'qty' => $b->qty,
                    'price' => $b->harga_satuan,
                    'disc' => $b->diskon,
                    'additional_cost' => $b->biaya_tambahan,
                    'remaining_qty' => $b->qty,
                    'total' => repairDouble($b->total),
                ]);
                array_push($id_detail_all, $id_detail_new);
            }
        }

        $this->sppModel->update($sppID, [
            'request_status' => 'finished'
        ]);

        $this->amPurchaseOrderDetailModel
            ->where('am_purchase_order_id', $id)
            ->whereNotIn('id', $id_detail_all)
            ->delete();

        return response()->setJSON([
            "status" => true,
            "message" => "Data PO Import BP Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function updateStatusPOImportBahanPenolong()
    {
        $data = [
            "status"    => true,
            "message"   => "Status Posting PO Import BP Berhasil Diupdate",
            'token'     => csrf_hash()
        ];
        $id = decrypt($this->request->getVar("id"));
        $result = $this->jurnalController->insertDataPembelian($id, "BAHAN PENOLONG", "IMPORT", "pembelian");
        if ($result) {
            $responseBody = json_decode($result->getBody(), true);
            if ($responseBody && isset($responseBody['status'])) {
                $data["status"] =  false;
                $data["message"] = $responseBody['message'];
                $data["token"] = csrf_hash();
            }
        } else {
            $this->amPurchaseOrderModel->update($id, ['is_posted' => $this->request->getVar('status')]);
        }
        return response()->setJSON($data);
    }

    public function closePOImportBahanPenolong()
    {
        $this->amPurchaseOrderModel->update(decrypt($this->request->getVar('id')), ['status_penerimaan' => 1]);
        return response()->setJSON([
            "status" => true,
            "message" => "Close PO Berhasil",
            'token' => csrf_hash()
        ]);
    }

    public function deletePOImportBahanPenolong()
    {
        $id = decrypt($this->request->getPost("id"));
        $this->amPurchaseOrderModel->delete($id);
        $this->amPurchaseOrderDetailModel->where('am_purchase_order_id', $id)->delete();
        return response()->setJSON([
            "status" => true,
            "message" => "PO Berhasil Dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function print($id = null)
    {
        $filename = "PO Import Bahan Penolong";
        $id = decrypt($id);

        $data = [
            "dataPO" => $this->amPurchaseOrderModel->getPOById($id),
            "dataPODetail" => $this->amPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id)
        ];

        if ($data['dataPO'] == null) {
            return redirect()->to('po-import-bahan-penolong');
        }

        $data["valuta"] =  $this->metadataModel->where('id', $data['dataPO']->currency)->first()['description'];
        $data["shipmentPO"] =  $this->metadataModel->where('name', 'Shipment PO')->first()['value'];
        $data["shipmentName"] = $this->metadataModel->where('id', $data['dataPO']->shipment)->first()['value'];
        $data["telpKantor"] = $this->metadataModel->where('name', 'Telp Kantor')->first()['value'];
        $data["faxKantor"] = $this->metadataModel->where('name', 'Fax Kantor')->first()['value'];
        $data["attnKantor"] = $this->metadataModel->where('name', 'Attn Kantor')->first()['value'];
        $data["company"] = $this->companyModel->where('id', $data['dataPO']->company_id)->first();
        $data["alamatKantor"] = $this->metadataModel->where('name', "Alamat Kantor")->first();

        $this->dompdf->loadHtml(view('Purchase/poImportBahanPenolong/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function dropdownPOImportBahanPenolong()
    {
        $id = formatter($this->request->getVar("id"), "STR_TO_INT");

        $dataPOImport = $this->amPurchaseOrderModel->getNoPenerimaanBarang("Import", $id);

        $data = [
            "data" => $dataPOImport
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOImportBahanPenolong()
    {
        $id = $this->request->getVar("id");

        $dataPOImport = $this->amPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOImport
        ];

        echo json_encode($data);
        return;
    }

    public function purchaseOrderPaymentDropdown($id)
    {
        $condition = [
            'company_id'    => $this->this_company_id,
            'supplier_id'   => $id,
            'po_type'       => 'Import',
            'is_posted'     => 1
        ];

        $selectQry = "am_purchase_orders.*,
                      metadata.value AS currency";
        $dataPOImport = $this->amPurchaseOrderModel->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'metadata.id = am_purchase_orders.currency')
            ->findAll();

        $data = [
            "data" => $dataPOImport
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownHistoriPenerimaanBarang()
    {
        $id = decrypt($this->request->getVar('id'));
        $listBarang = $this->amPurchaseOrderDetailModel
            ->select('
            am_purchase_order_details.qty_diterima AS diterima, 
            am_purchase_order_details.remaining_qty AS sisa, 
            CONCAT(barang_master.barang_name, " - ",barang_master_spesifikasi.spesifikasi) AS nama_barang, 
            barang_master.kode_barang, 
            am_purchase_order_details.qty')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->where('am_purchase_order_details.am_purchase_order_id', $id)
            ->where('am_purchase_order_details.deletedAt', null)
            ->findAll();

        $poDetail = $this->amPurchaseOrderModel->select('am_purchase_orders.po_no, suppliers.name AS supplierName')
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

    public function dropdownGetSppDetail()
    {
        $id = $this->request->getVar('spp_id');
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

            $result[] = [
                'barang_id' => $s['barang1_id'],
                'kode_barang' => $s['kode_barang'],
                'spesifikasi_id' => $s['barang2_id'],
                'nama_barang' => $s['nama_barang'],
                'satuan_id' => $s['unit'],
                'nama_satuan' => $s['kode_satuan'],
                'qty' => $s['qty'],
                'diskon' => '0',
                'harga_satuan' => '0',
                'biaya_tambahan' => '0',
                'total' => '0',
                'keterangan' => $s['note'],
            ];
        }

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function generateNoPo()
    {
        $noPoNew =  $this->amPurchaseOrderModel->get_new_no_po_import(
            date('m'),
            date('y'),
            getLastDay(),
            $this->this_company_id
        );

        return json_encode($noPoNew);
    }
}
