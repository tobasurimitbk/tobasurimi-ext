<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;

use App\Models\BarangMasterModel;
use App\Models\CompaniesModel;
use App\Models\MetadataModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\SupplierModel;
use App\Models\DivisisModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SatuansModel;
use App\Models\SppDetailModel;
use App\Models\SppModel;
use Dompdf\Dompdf;

class POImportBahanBaku extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $user_id;
    protected $companyModel;
    protected $metadataModel;
    protected $rmImportPOModel;
    protected $rmImportPODetailModel;
    protected $supplierModel;
    protected $beaCukaiModel;
    protected $divisisModel;
    protected $barangMasterModel;
    protected $satuanModel;
    protected $penerimaanBarangModel;
    protected $dompdf;
    protected $jurnalController;
    protected $sppModel;
    protected $sppDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->user_id = session()->get("login")->user_id;
        $this->companyModel = new CompaniesModel();
        $this->metadataModel = new MetadataModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();
        $this->supplierModel = new SupplierModel();
        $this->divisisModel = new DivisisModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->satuanModel = new SatuansModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->dompdf = new Dompdf();
        $this->jurnalController = new JurnalUmum();
        $this->sppModel = new SppModel();
        $this->sppDetailModel = new SppDetailModel();
    }

    public function poImportBahanBaku()
    {
        return view('Purchase/poImportBahanBaku/index');
    }

    public function createPOImportBahanBaku()
    {
        $data = [
            "divisi" => $this->divisisModel->getDivisiAccess(),
            "today" => date("d/m/Y"),
            "dataSupplier" =>  $this->supplierModel->getSupplierByType('INTERNASIONAL'),
            "barang" => $this->barangMasterModel->getBarangByTypeWithSpec([
                'barang_master.type_barang'  => 'bahan_baku',
                'barang_master.company_id' => $this->this_company_id,
                'barang_master.deletedAt' => null,
                'barang_master_spesifikasi.deletedAt' => null
            ]),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "dataShipment" => $this->metadataModel->get_by_name('Shipment')
        ];


        return view('Purchase/poImportBahanBaku/form', $data);
    }

    public function getByIdPOImportBahanBaku($id = null)
    {
        $id = decrypt($id);
        $unPostingCheck = $this->penerimaanBarangModel->where('tipe_bahan', "BAKU")->where('status_penerimaan', "IMPORT")->like('multiple_po_id', $id)->first();
        $data = [
            "divisi" => $this->divisisModel->getDivisiAccess(),
            "dataCompany" => $this->companyModel->getCompanies(),
            "today" => date("d/m/Y"),
            "dataSupplier" =>  $this->supplierModel->getSupplierByType('INTERNASIONAL'),
            "barang" => $this->barangMasterModel->getBarangByTypeWithSpec([
                'barang_master.type_barang'  => 'bahan_baku',
                'barang_master.company_id' => $this->this_company_id,
                'barang_master.deletedAt' => null,
                'barang_master_spesifikasi.deletedAt' => null
            ]),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "dataShipment" => $this->metadataModel->get_by_name('Shipment'),
            "dataPOImport" => $this->rmImportPOModel->getPOById($id),
            "dataPOImportDetail" => $this->rmImportPODetailModel->getPurchaseOrderDetailByPurchaseOrderId($id),
            "unPosting" => $unPostingCheck == null ? 0 : 1,
        ];

        if ($data["dataPOImport"] == null) {
            return redirect()->to('po-import-bahan-baku');
        }

        $data["dataListSPP"] = $this->sppModel->where('request_status', "waiting")->where('is_posted', '1')->where('divisi_id', $data['dataPOImport']->division_id)->where('spp_type', "Import BB")->where('deletedAt', null)->findAll();

        return view('Purchase/poImportBahanBaku/form', $data);
    }

    public function allPOImportBahanBaku()
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
            "rm_import_pos.company_id" => $this->this_company_id,
            "rm_import_po_details.deletedAt" => null
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
        $poImportData = $this->rmImportPOModel->getPOList($condition, $addCondition, $limit, $offset);

        $dataPOImport = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poImportData['data'] as $data) {
            $unPostingCheck = $this->penerimaanBarangModel->where('tipe_bahan', "BAKU")->where('status_penerimaan', "IMPORT")->like('multiple_po_id', $data->id)->first();

            array_push($dataPOImport, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "po_no"         => $data->po_no,
                "divisi"        => strtoupper($data->divisi),
                "supplierName"  => strtoupper($data->supplierName),
                "total"         => number_format($data->total),
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
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePOImportBahanBaku()
    {
        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->rmImportPOModel->get_new_no_po(
                date('m'),
                date('Y'),
                getLastDay()
            );
        }

        $first = $this->rmImportPOModel
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
        $poID = $this->rmImportPOModel->insert([
            'company_id' => $this->this_company_id,
            'division_id' => $this->request->getVar('divisionID'),
            'po_no' => $noPoNew,
            'purchase_request_id' => $this->request->getVar('spp_id'),
            'po_date' => $this->request->getPost("poDate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("poDate")))) : "",
            'currency' => formatter($this->request->getVar("currency"), "STR_TO_INT"),
            'supplier_id' => $this->request->getVar('supplierID'),
            'potongan_harga' => $this->request->getVar('potongan_harga'),
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

            $this->rmImportPODetailModel->insert([
                'rm_import_po_id' => $poID,
                'barang_id' => $b->barang_id,
                'spesifikasi_id' => $b->spesifikasi_id,
                'unit' => $b->satuan_id,
                'qty' => $b->qty,
                'price' => $b->harga_satuan,
                'disc' => $b->diskon,
                'additional_cost' => $b->biaya_tambahan,
                'remaining_qty' => $b->qty,
                'note' => $b->keterangan,
                'total' => repairDouble($b->total),
            ]);
        }

        $this->sppModel->update($this->request->getVar('spp_id'), [
            'request_status' => 'finished'
        ]);


        return response()->setJSON([
            "status" => true,
            "message" => "Data PO Import BB Berhasil Disimpan",
            'id' => encrypt($poID),
            'token' => csrf_hash()
        ]);
    }

    public function updatePOImportBahanBaku()
    {
        $id = decrypt($this->request->getPost("id"));

        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->rmImportPOModel->get_new_no_po(
                date('m'),
                date('Y'),
                getLastDay()
            );
        }

        $first = $this->rmImportPOModel
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

        $firstData = $this->rmImportPOModel->find($id);

        if (empty($this->request->getVar('spp_id'))) {
            $sppID = $firstData['purchase_request_id'];
        } else {
            $sppID = $this->request->getVar('spp_id');
        }

        $this->sppModel->update($sppID, [
            'request_status' => 'waiting'
        ]);

        $this->rmImportPOModel->update($id, [
            'company_id' => $this->this_company_id,
            'division_id' => $this->request->getVar('divisionID'),
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

        // get all id detail
        $id_detail_all = [];

        $barang = json_decode($this->request->getVar("listBarang"));

        foreach ($barang as $b) {
            $check = $this->rmImportPODetailModel
                ->where('rm_import_po_details.rm_import_po_id', $id)
                ->where('spesifikasi_id', $b->spesifikasi_id)
                ->where('barang_id', $b->barang_id)
                ->first();

            if ($check != null) {
                // UPDATE
                $this->rmImportPODetailModel->update($check['id'], [
                    'rm_import_po_id' => $id,
                    'barang_id' => $b->barang_id,
                    'spesifikasi_id' => $b->spesifikasi_id,
                    'unit' => $b->satuan_id,
                    'qty' => $b->qty,
                    'price' => $b->harga_satuan,
                    'disc' => $b->diskon,
                    'additional_cost' => $b->biaya_tambahan,
                    'remaining_qty' => $b->qty,
                    'note' => trim($b->keterangan),
                    'total' => repairDouble($b->total),
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // NEW BARANG
                // DELETE
                $this->rmImportPODetailModel
                    ->where('rm_import_po_details.rm_import_po_id', $id)
                    ->where('spesifikasi_id', $b->spesifikasi_id)
                    ->where('barang_id', $b->barang_id)
                    ->delete();

                // INSERT NEW
                $id_detail_new = $this->rmImportPODetailModel->insert([
                    'rm_import_po_id' => $id,
                    'barang_id' => $b->barang_id,
                    'spesifikasi_id' => $b->spesifikasi_id,
                    'unit' => $b->satuan_id,
                    'qty' => $b->qty,
                    'price' => $b->harga_satuan,
                    'disc' => $b->diskon,
                    'additional_cost' => $b->biaya_tambahan,
                    'remaining_qty' => $b->qty,
                    'note' => trim($b->keterangan),
                    'total' => repairDouble($b->total),
                ]);
                array_push($id_detail_all, $id_detail_new);
            }
        }

        $this->sppModel->update($sppID, [
            'request_status' => 'finished'
        ]);

        $this->rmImportPODetailModel
            ->where('rm_import_po_id', $id)
            ->whereNotIn('id', $id_detail_all)
            ->delete();

        return response()->setJSON([
            "status" => true,
            "message" => "Data PO Import BB Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function updateStatusPOImportBahanBaku()
    {
        $data = [
            "status"    => true,
            "message"   => "Status Posting PO Import BB Berhasil Diupdate",
            'token'     => csrf_hash()
        ];

        $result = $this->jurnalController->insertDataPembelian(decrypt($this->request->getVar('id')), "BAHAN BAKU", "IMPORT", "pembelian");

        if ($result) {
            $responseBody = json_decode($result->getBody(), true);
            if ($responseBody && isset($responseBody['status'])) {
                $data["status"] =  false;
                $data["message"] = $responseBody['message'];
                $data["token"] = csrf_hash();
            }
        } else {
            $this->rmImportPOModel->update(decrypt($this->request->getVar('id')), ['is_posted' => $this->request->getVar('status')]);
        }
        return response()->setJSON($data);
    }

    public function closePOImportBahanBaku()
    {
        $this->rmImportPOModel->update(decrypt($this->request->getVar('id')), ['status_penerimaan' => 1]);
        return response()->setJSON([
            "status" => true,
            "message" => "Close PO Berhasil",
            'token' => csrf_hash()
        ]);
    }

    public function deletePOImportBahanBaku()
    {
        $id = decrypt($this->request->getPost("id"));

        $firstData = $this->rmImportPOModel->find($id);

        $this->sppModel->update($firstData['purchase_request_id'], [
            'request_status' => 'waiting'
        ]);

        $this->rmImportPOModel->delete($id);
        $this->rmImportPODetailModel->where('rm_import_po_id', $id)->delete();
        return response()->setJSON([
            "status" => true,
            "message" => "PO Berhasil Dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function print($id = null)
    {
        if ($id) {
            $filename = "PO Import Bahan Baku";

            $id = decrypt($id);
            $data = [];
            $dataPO = $this->rmImportPOModel->getPOById($id);

            if ($dataPO == null) {
                return redirect()->to('po-import-bahan-baku');
            }

            if ($dataPO) {
                $dataPODetail = $this->rmImportPODetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

                if ($dataPODetail) {
                    $data["valuta"] =  $this->metadataModel->where('id', $dataPO->currency)->first()['description'];
                    $data["shipmentPO"] =  $this->metadataModel->where('name', 'Shipment PO')->first()['value'];
                    $data["shipmentName"] = $this->metadataModel->where('id', $dataPO->shipment)->first()['value'];
                    $data["telpKantor"] = $this->metadataModel->where('name', 'Telp Kantor')->first()['value'];
                    $data["faxKantor"] = $this->metadataModel->where('name', 'Fax Kantor')->first()['value'];
                    $data["attnKantor"] = $this->metadataModel->where('name', 'Attn Kantor')->first()['value'];
                    $data["dataPO"] = $dataPO;
                    $data["company"] = $this->companyModel->where('id', $dataPO->company_id)->first();
                    $data["alamatKantor"] = $this->metadataModel->where('name', "Alamat Kantor")->first();
                    $data["dataPODetail"] = $dataPODetail;
                }
            }
            $this->dompdf->loadHtml(view('Purchase/poImportBahanBaku/print', $data));
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function dropdownPOImportBahanBaku()
    {
        $id = formatter($this->request->getVar("id"), "STR_TO_INT");

        $dataPOImport = $this->rmImportPOModel->getNoPenerimaanBarang($id, $this->this_company_id);

        $data = [
            "data" => $dataPOImport
        ];

        echo json_encode($data);
        return;
    }

    public function purchaseOrderPaymentDropdown($id)
    {
        $condition = [
            'company_id'    => $this->this_company_id,
            'supplier_id'   => $id,
            'is_posted'     => 1
        ];

        $selectQry = "rm_import_pos.*,
                      metadata.value AS currency";
        $dataPOImport = $this->rmImportPOModel->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'metadata.id = rm_import_pos.currency')
            ->findAll();

        $data = [
            "data" => $dataPOImport
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOImportBahanBaku()
    {
        $id = $this->request->getVar("id");

        $dataPOImport = $this->rmImportPODetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOImport
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownHistoriPenerimaanBarang()
    {
        $id = decrypt($this->request->getVar('id'));
        $listBarang = $this->rmImportPODetailModel
            ->select('rm_import_po_details.qty_diterima AS diterima, 
            rm_import_po_details.remaining_qty AS sisa, 
            CONCAT(barang_master.barang_name, " - ",barang_master_spesifikasi.spesifikasi) AS nama_barang, 
            barang_master.kode_barang, 
            rm_import_po_details.qty')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_import_po_details.spesifikasi_id', 'left')
            ->where('rm_import_po_details.rm_import_po_id', $id)
            ->where('rm_import_po_details.deletedAt', null)
            ->findAll();

        $poDetail = $this->rmImportPOModel->select('rm_import_pos.po_no, suppliers.name AS supplierName')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id')
            ->where('rm_import_pos.id', $id)
            ->where('rm_import_pos.deletedAt', null)
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
}
