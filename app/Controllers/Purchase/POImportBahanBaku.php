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
    }

    public function poImportBahanBaku()
    {
        return view('Purchase/poImportBahanBaku/index');
    }

    public function createPOImportBahanBaku()
    {
        $data = [
            "dataCompany" => $this->companyModel->getCompanies(),
            "today" => date("d/m/Y"),
            "dataSupplier" =>  $this->supplierModel->getSupplierByType('INTERNASIONAL'),
            "barang" => $this->barangMasterModel->getBarangByType("bahan_baku"),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "dataShipment" => $this->metadataModel->get_by_name('Shipment')
        ];

        return view('Purchase/poImportBahanBaku/form', $data);
    }

    public function getByIdPOImportBahanBaku($id = null)
    {
        $data = [
            "dataCompany" => $this->companyModel->getCompanies(),
            "today" => date("d/m/Y"),
            "dataSupplier" =>  $this->supplierModel->getSupplierByType('INTERNASIONAL'),
            "barang" => $this->barangMasterModel->getBarangByType("bahan_baku"),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "dataShipment" => $this->metadataModel->get_by_name('Shipment'),
            "dataPOImport" => $this->rmImportPOModel->getPOById($id),
            "dataPOImportDetail" => $this->rmImportPODetailModel->getPurchaseOrderDetailByPurchaseOrderId($id)
        ];

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
            array_push($dataPOImport, [
                "no"            => $no++,
                "id"            => $data->id,
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "po_no"         => $data->po_no,
                "companyName"   => strtoupper($data->companyName),
                "supplierName"  => strtoupper($data->supplierName),
                "total"         => number_format($data->total),
                "currencyName"  => $data->currencyName,
                "itemCount"     => $data->itemCount,
                "is_posted"     => $data->is_posted,
                "status_penerimaan" => $data->status_penerimaan === "0" ? "OPEN" : "CLOSED",
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
        $divisi = $this->divisisModel->get_by_id(
            $this->request->getVar('divisionID')
        );

        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->rmImportPOModel->get_no(
                date('d'),
                date('m'),
                date('Y'),
                $divisi[0]['divisi'],
                date('y'),
                $this->request->getPost("divisionID"),
                getLastDay()
            );
        }

        // create new po
        $poID = $this->rmImportPOModel->insert([
            'company_id' => $this->request->getVar('companyID'),
            'division_id' => $this->request->getVar('divisionID'),
            'po_no' => $noPoNew,
            'po_date' => $this->request->getPost("poDate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("poDate")))) : "",
            'currency' => formatter($this->request->getVar("currency"), "STR_TO_INT"),
            'supplier_id' => $this->request->getVar('supplierID'),
            'total' => $this->request->getVar('total'),
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
            'createdBy' => session()->get("login")->user_id
        ]);

        $barang = json_decode($this->request->getVar("listBarang"));

        foreach ($barang as $b) {

            $this->rmImportPODetailModel->insert([
                'rm_import_po_id' => $poID,
                'barang_id' => $b->barang_id,
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

        return response()->setJSON([
            "status" => true,
            "message" => "Data PO Import BB Berhasil Disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function updatePOImportBahanBaku()
    {
        $id = $this->request->getPost("id");

        $this->rmImportPOModel->update($id, [
            'company_id' => $this->request->getVar('companyID'),
            'division_id' => $this->request->getVar('divisionID'),
            'currency' => formatter($this->request->getVar("currency"), "STR_TO_INT"),
            'supplier_id' => $this->request->getVar('supplierID'),
            'total' => $this->request->getVar('total'),
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
        ]);

        // Delete First
        $this->rmImportPODetailModel->where('rm_import_po_id', $id)->delete();
        // Insert Again
        $barang = json_decode($this->request->getVar("listBarang"));

        foreach ($barang as $b) {

            $this->rmImportPODetailModel->insert([
                'rm_import_po_id' => $id,
                'barang_id' => $b->barang_id,
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
            "message"   => "Data PO Import BB Berhasil Diposting",
            'token'     => csrf_hash()
        ];

        $result = $this->jurnalController->insertDataPembelian($this->request->getVar('id'), "BAHAN BAKU", "IMPORT", "pembelian");

        if ($result) {
            $responseBody = json_decode($result->getBody(), true);
            if ($responseBody && isset($responseBody['status'])) {
                $data["status"] =  false;
                $data["message"] = $responseBody['message'];
                $data["token"] = csrf_hash();
            }
        } else {
            $this->rmImportPOModel->update($this->request->getVar('id'), ['is_posted' => 1]);
        }
        return response()->setJSON($data);
    }

    public function closePOImportBahanBaku()
    {
        $this->rmImportPOModel->update($this->request->getVar('id'), ['status_penerimaan' => 1]);
        return response()->setJSON([
            "status" => true,
            "message" => "Close PO Berhasil",
            'token' => csrf_hash()
        ]);
    }

    public function deletePOImportBahanBaku()
    {
        $id = $this->request->getPost("id");
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

            $data = [];
            $dataPO = $this->rmImportPOModel->getPOById($id);

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
        $id = $this->request->getVar('id');
        $listBarang = $this->rmImportPODetailModel
            ->select('rm_import_po_details.qty_diterima AS diterima, rm_import_po_details.remaining_qty AS sisa, barang_master.barang_name AS nama_barang, barang_master.kode_barang, rm_import_po_details.qty')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id')
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
}
