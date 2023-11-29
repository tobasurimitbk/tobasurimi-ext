<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\PenerimaanBarangModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use App\Models\BeaCukaiModel;
use App\Models\SupplierHargaModel;
use Dompdf\Dompdf;

class PenerimaanBarangImportBP extends BaseController
{
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $barangMasterModel;
    protected $metadataModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $rmPurchaseOrderModel;
    protected $rmPurchaseOrderDetailModel;
    protected $supplierModel;
    protected $warehousesModel;
    protected $satuanModel;
    protected $beaCukaiModel;
    protected $supplierHargaModel;
    protected $stockDetailModel;
    protected $warehouseModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->beaCukaiModel = new BeaCukaiModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->warehouseModel = new WarehousesModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('Warehouse/penerimaanBarangImport/bahanPenolong/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
            "statuspenerimaan" => "IMPORT",
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "status_penerimaan" => "IMPORT",
            "penerimaan_barang.deletedAt" => null,
            "penerimaan_barang_detail.deletedAt" => null,
            "tipe_bahan" => "PENOLONG"
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $penerimaanBarangData = $this->penerimaanBarangModel->getPenerimaanBarangList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($penerimaanBarangData['data'] as $data) {
            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "warehouse_name"        => $data->warehouse_name,
                "tipe_bahan"            => $data->tipe_bahan,
                "createdAt"             => $data->createdAt ? date("d/m/Y", strtotime($data->createdAt)) : "",
                "supplier_name"         => $data->supplier_name,
                "itemCount"             => $data->itemCount,
                "multiple_po_no"        => str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no),
                "status_post"           => $data->status_post,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $penerimaanBarangData['totalData'],
            "recordsFiltered"   => $penerimaanBarangData['totalFilteredData'],
            "data"              => $dataPenerimaanBarang,
            "payload"           => $payload,

        ];

        echo json_encode($data);
        return;
    }

    public function create()
    {
        $dataAJU = $this->metadataModel->get_by_name('jenis_dok_aju');
        $dataSupplier = $this->supplierModel->getSupplierByType('INTERNASIONAL');
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);
        $dataSatuan = $this->satuanModel->asObject()->find();

        $data = [
            "dataSatuan" => $dataSatuan,
            "dataWarehouse" => $dataWarehouse,
            "dataSupplier" => $dataSupplier,
            "dataAJU" => $dataAJU
        ];

        return view('Warehouse/penerimaanBarangImport/bahanPenolong/form', $data);
    }

    public function createAction()
    {
        $barangs = $this->request->getVar('barangs');

        $penerimaanBarangID = $this->penerimaanBarangModel->insert([
            'company_id' => $this->this_company_id,
            'bc_type' => $this->request->getVar('aju_document_type'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_penerimaan_barang' => $this->request->getVar('no_penerimaan_barang'),
            'acceptance_type' => $this->request->getVar('acceptance_type'),
            'multiple_po_id' => json_encode($this->request->getVar('multiple_po_id')),
            'multiple_po_no' => json_encode($this->request->getVar('multiple_po_no')),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'kemasan' => $this->request->getVar('kemasan'),
            'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            "tipe_bahan" => "PENOLONG",
            "status_post" => "WAITING",
            "status_penerimaan" => "IMPORT",
        ]);

        foreach (json_decode($barangs) as $b) {
            $poDetail = $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->first();
            $barang = null;
            if ($poDetail != null) {
                $barang = $this->barangMasterModel->where('id', $poDetail['barang_id'])->first();
            }
            $this->penerimaanBarangDetailModel->insert([
                'purchase_order_id' => $b->am_purchase_order_id,
                'purchase_order_details_id' => $b->am_purchase_order_details_id,
                'penerimaan_barang_id' => $penerimaanBarangID,
                'barang_id' => $barang == null ? 0 : $barang['id'],
                'unit' => $poDetail == null ? 0 : $poDetail['unit'],
                'harga' => $b->harga,
                'sub_total' => $b->sub_total,
                'qty' => $b->jml_order,
                'nama_barang_dok' => $barang == null ? 0 : $barang['barang_name'],
                'jml_masuk' => $b->jml_diterima_lpb,
            ]);
            // update remeaning di detail po
            $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->where('am_purchase_order_id', $b->am_purchase_order_id)
                ->set('remaining_qty', $b->sisa_total)
                ->set('qty_diterima', $b->jml_diterima_total)
                ->update();
        }

        return response()->setJSON([
            'message' => "Penerimaan barang Import BP berhasil disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => $penerimaanBarangID
        ]);
    }

    public function update($id)
    {
        $dataAJU = $this->metadataModel->get_by_name('jenis_dok_aju');
        $dataSupplier = $this->supplierModel->getSupplierByType('INTERNASIONAL');
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);
        $dataSatuan = $this->satuanModel->asObject()->find();

        $data = [
            "dataSatuan" => $dataSatuan,
            "dataWarehouse" => $dataWarehouse,
            "dataSupplier" => $dataSupplier,
            "dataAJU" => $dataAJU,
            "dataPenerimaanBarang" => $this->penerimaanBarangModel->where('id', $id)->first()
        ];

        return view('Warehouse/penerimaanBarangImport/bahanPenolong/form', $data);
    }

    public function updateAction()
    {
        $id = $this->request->getVar('id');
        $barangs = $this->request->getVar('barangs');

        $this->penerimaanBarangModel->update($id, [
            'company_id' => $this->this_company_id,
            'bc_type' => $this->request->getVar('aju_document_type'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_penerimaan_barang' => $this->request->getVar('no_penerimaan_barang'),
            'acceptance_type' => $this->request->getVar('acceptance_type'),
            'multiple_po_id' => json_encode($this->request->getVar('multiple_po_id')),
            'multiple_po_no' => json_encode($this->request->getVar('multiple_po_no')),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'kemasan' => $this->request->getVar('kemasan'),
            'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
        ]);

        // delete first in penerimaan_barang_detail
        $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->delete();

        foreach (json_decode($barangs) as $b) {
            $poDetail = $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->first();
            $barang = null;
            if ($poDetail != null) {
                $barang = $this->barangMasterModel->where('id', $poDetail['barang_id'])->first();
            }
            $this->penerimaanBarangDetailModel->insert([
                'purchase_order_id' => $b->am_purchase_order_id,
                'purchase_order_details_id' => $b->am_purchase_order_details_id,
                'penerimaan_barang_id' => $id,
                'barang_id' => $barang == null ? 0 : $barang['id'],
                'unit' => $poDetail == null ? 0 : $poDetail['unit'],
                'harga' => $b->harga,
                'sub_total' => $b->sub_total,
                'qty' => $b->jml_order,
                'nama_barang_dok' => $barang == null ? 0 : $barang['barang_name'],
                'jml_masuk' => $b->jml_diterima_lpb,
            ]);
            // update remeaning di detail po
            $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->where('am_purchase_order_id', $b->am_purchase_order_id)
                ->set('remaining_qty', $b->sisa_total)
                ->set('qty_diterima', $b->jml_diterima_total)
                ->update();
        }

        return response()->setJSON([
            'message' => "Berhasil update penerimaan barang",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function print($id)
    {
        if ($id) {
            $filename = "Penerimaan Barang Import";

            $data = [
                'dataPenerimaanBarang' => $this->penerimaanBarangModel->getById($id),
                'dataPenerimaanBarangDetail' => $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail($id)
            ];
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangImport/bahanPenolong/print', $data));
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function delete()
    {
        $id = $this->request->getVar('id');

        $this->penerimaanBarangModel->where('id', $id)->delete();
        $penerimaanBarangList = $this->penerimaanBarangDetailModel->asObject()->where('penerimaan_barang_id', $id)->where('deletedAt', null)->findAll();
        foreach ($penerimaanBarangList as $b) {
            // update remeaning di detail po
            $last = $this->amPurchaseOrderDetailModel->where('id', $b->purchase_order_details_id)
                ->where('am_purchase_order_id', $b->purchase_order_id)
                ->first();

            $this->amPurchaseOrderDetailModel->where('id', $b->purchase_order_details_id)
                ->where('am_purchase_order_id', $b->purchase_order_id)
                ->set('remaining_qty', $last['remaining_qty'] + $b->jml_masuk)
                ->set('qty_diterima', $last['qty_diterima'] - $b->jml_masuk)
                ->update();
        }
        $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "LPB berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function posting()
    {
        $id = $this->request->getVar('id');
        $this->penerimaanBarangModel
            ->where(['id' => $id])
            ->set(['status_post' => 'FINISH'])
            ->update();

        $penerimaanBarangFirst = $this->penerimaanBarangModel->where('id', $id)->first();
        $penerimaanBarangList = $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->where('deletedAt', null)->findAll();

        $test = $this->penerimaanBarangModel->autoClosePO($id);

        foreach ($penerimaanBarangList as $b) {
            $this->stockDetailModel->addOrReduceStock(
                $b['barang_id'],
                $penerimaanBarangFirst['warehouse_id'],
                'New',
                $b['jml_masuk'],
                'IN',
                ''
            );
        }

        return response()->setJSON([
            'status' => true,
            'message' => "LPB berhasil diposting",
            'token' => csrf_hash()
        ]);
    }

    public function listBarangLPB()
    {
        $penerimaanBarangID = empty($this->request->getVar('penerimaan_barang_id')) ? null : $this->request->getVar('penerimaan_barang_id');
        $amPurchaseOrderID = json_decode($this->request->getVar('am_purchase_order_id'));

        return response()->setJSON($this->amPurchaseOrderDetailModel->getListLPBBahanPenolong($amPurchaseOrderID, $penerimaanBarangID));
    }
}
