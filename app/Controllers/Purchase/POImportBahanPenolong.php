<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\MetadataModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\SppModel;
use App\Models\SupplierModel;
use App\Models\BeaCukaiModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\SatuansModel;
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
    protected $sppModel;
    protected $supplierModel;
    protected $beaCukaiModel;
    protected $companyModel;
    protected $barangMasterModel;
    protected $satuanModel;
    protected $divisionModel;
    protected $dompdf;

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
        $this->beaCukaiModel = new BeaCukaiModel();
        $this->companyModel = new CompaniesModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->satuanModel = new SatuansModel();
        $this->divisionModel = new DivisisModel();
        $this->dompdf = new Dompdf();
    }

    public function poImportBahanPenolong()
    {
        return view('Purchase/poImportBahanPenolong/index');
    }

    public function createPOImportBahanPenolong()
    {
        $data = [
            "company" => $this->companyModel->getCompanies(),
            "today" => date("d/m/Y"),
            "dataSupplier" =>  $this->supplierModel->getSupplierByType('INTERNASIONAL'),
            "barang" => $this->barangMasterModel->getBarangByType("bahan_penolong"),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "dataShipment" => $this->metadataModel->get_by_name('Shipment')
        ];

        return view('Purchase/poImportBahanPenolong/form', $data);
    }

    public function getByIdPOImportBahanPenolong($id = null)
    {
        $data = [
            "company" => $this->companyModel->getCompanies(),
            "today" => date("d/m/Y"),
            "dataSupplier" =>  $this->supplierModel->getSupplierByType('INTERNASIONAL'),
            "barang" => $this->barangMasterModel->getBarangByType("bahan_penolong"),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "dataShipment" => $this->metadataModel->get_by_name('Shipment'),
            "dataPOImport" => $this->amPurchaseOrderModel->getPOById($id),
            "dataPOImportDetail" => $this->amPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id)
        ];

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
            "am_purchase_order_details.deletedAt" => null
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
            array_push($dataPOImport, [
                "no"            => $no++,
                "id"            => $data->id,
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "po_no"         => $data->po_no,
                "supplierName"  => strtoupper($data->supplierName),
                "companyName"  => strtoupper($data->companyName),
                "total"         => number_format(formatter($data->total, "STR_TO_FLOAT"), 2, '.', ','),
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
            "payload"           => $payload
        ];

        return response()->setJson($data);
    }

    public function savePOImportBahanPenolong()
    {
        $divisi = $this->divisionModel->get_by_id(
            $this->request->getVar('divisionID')
        );

        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->amPurchaseOrderModel->get_no(
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
        $poID = $this->amPurchaseOrderModel->insert([
            'company_id' => $this->request->getVar('companyID'),
            'division_id' => $this->request->getVar('divisionID'),
            'po_no' => $noPoNew,
            'po_date' => $this->request->getPost("poDate") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("poDate")))) : "",
            'po_type' => "Import",
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

            $this->amPurchaseOrderDetailModel->insert([
                'am_purchase_order_id' => $poID,
                'barang_id' => $b->barang_id,
                'unit' => $b->satuan_id,
                'qty' => $b->qty,
                'price' => $b->harga_satuan,
                'disc' => $b->diskon,
                'additional_cost' => $b->biaya_tambahan,
                'remaining_qty' => $b->qty,
                'total' => repairDouble($b->total),
            ]);
        }

        return response()->setJSON([
            "status" => true,
            "message" => "Data PO Import BP Berhasil Disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function updatePOImportBahanPenolong()
    {
        $id = $this->request->getPost("id");

        $this->amPurchaseOrderModel->update($id, [
            'company_id' => $this->request->getVar('companyID'),
            'division_id' => $this->request->getVar('divisionID'),
            'po_type' => "Import",
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
        $this->amPurchaseOrderDetailModel->where('am_purchase_order_id', $id)->delete();
        // Insert Again
        $barang = json_decode($this->request->getVar("listBarang"));

        foreach ($barang as $b) {

            $this->amPurchaseOrderDetailModel->insert([
                'am_purchase_order_id' => $id,
                'barang_id' => $b->barang_id,
                'unit' => $b->satuan_id,
                'qty' => $b->qty,
                'price' => $b->harga_satuan,
                'disc' => $b->diskon,
                'additional_cost' => $b->biaya_tambahan,
                'remaining_qty' => $b->qty,
                'total' => repairDouble($b->total),
            ]);
        }

        return response()->setJSON([
            "status" => true,
            "message" => "Data PO Import BP Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function updateStatusPOImportBahanPenolong()
    {
        $this->amPurchaseOrderModel->update($this->request->getVar('id'), ['is_posted' => 1]);
        return response()->setJSON([
            "status" => true,
            "message" => "Data PO Import BP Berhasil Diposting",
            'token' => csrf_hash()
        ]);
    }

    public function closePOImportBahanPenolong()
    {
        $this->amPurchaseOrderModel->update($this->request->getVar('id'), ['status_penerimaan' => 1]);
        return response()->setJSON([
            "status" => true,
            "message" => "Close PO Berhasil",
            'token' => csrf_hash()
        ]);
    }

    public function deletePOImportBahanPenolong()
    {
        $id = $this->request->getPost("id");
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

        $data = [
            "dataPO" => $this->amPurchaseOrderModel->getPOById($id),
            "dataPODetail" => $this->amPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id)
        ];

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
}
