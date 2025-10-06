<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC25Model;
use App\Models\BC27Model;
use App\Models\BC30Model;
use App\Models\BC41Model;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\ParentBarangModel;
use App\Models\PPBKBModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampLogModel;
use App\Models\WarehousesModel;

class StokHistori extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $divisiModel;
    protected $metaDataModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $kemasanModel;
    protected $satuanModel;
    protected $parentBarangModel;
    protected $warehouseModel;
    protected $bc30Model;
    protected $bc25Model;
    protected $bc41Model;
    protected $bc27Model;
    protected $ppbkbModel;
    protected $stockRevampLogModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->metaDataModel = new MetadataModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->kemasanModel = new KemasanModel();
        $this->satuanModel = new SatuansModel();
        $this->parentBarangModel = new ParentBarangModel();
        $this->warehouseModel = new WarehousesModel();
        $this->bc30Model = new BC30Model();
        $this->bc25Model = new BC25Model();
        $this->bc41Model = new BC41Model();
        $this->bc27Model = new BC27Model();
        $this->ppbkbModel = new PPBKBModel();
        $this->stockRevampLogModel = new StockRevampLogModel();
    }

    public function index()
    {
        $sumberBarang = $this->metaDataModel->where('name', "sumber_barang")->first();
        $sumberBarangArr = explode(',', $sumberBarang['value']);

        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'sumberBarang' => $sumberBarangArr
        ];

        return view('Warehouse/stock/stock_history', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "divisi_id"     => $this->request->getVar("divisi_id"),
            "warehouse_id"  => $this->request->getVar('warehouse_id'),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "sumber_barang" => $this->request->getVar('sumber_barang'),
            "search"        => $this->request->getVar("search"),

        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        if ($addCondition['sumber_barang'] == "PO LOKAL BAKU") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "LPB",
                "stock_revamp_detail.po_type" => "LOKAL BAKU"
            ];

            $dataQry = $this->stockRevampLogModel->getListLogPoLokalBb(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        } elseif ($addCondition['sumber_barang'] == "PO LOKAL PENOLONG") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "LPB",
                "stock_revamp_detail.po_type" => "LOKAL PENOLONG"
            ];

            $dataQry = $this->stockRevampLogModel->getListLogPoLokalBp(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        } elseif ($addCondition['sumber_barang'] == "PO IMPORT BAKU") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "LPB",
                "stock_revamp_detail.po_type" => "IMPORT BAKU"
            ];

            $dataQry = $this->stockRevampLogModel->getListLogPoLokalBp(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        } elseif ($addCondition['sumber_barang'] == "PO IMPORT PENOLONG") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "LPB",
                "stock_revamp_detail.po_type" => "IMPORT PENOLONG"
            ];

            $dataQry = $this->stockRevampLogModel->getListLogPoLokalBp(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        } elseif ($addCondition['sumber_barang'] == "PROSES REBUS") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "PROSES REBUS",
            ];

            $dataQry = $this->stockRevampLogModel->getListLogProsesRebus(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        } elseif ($addCondition['sumber_barang'] == "JASA VENDOR") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "JASA VENDOR",
            ];

            $dataQry = $this->stockRevampLogModel->getListLogJasaVendor(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        } elseif ($addCondition['sumber_barang'] == "HASIL PRODUKSI") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "HASIL PRODUKSI",
            ];

            $dataQry = $this->stockRevampLogModel->getListLogHasilProduksi(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        } elseif ($addCondition['sumber_barang'] == "MATERIAL REQUEST BAKU") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "MATERIAL REQUEST BAKU",
            ];

            $dataQry = $this->stockRevampLogModel->getListLogMaterialRequestBaku(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        } elseif ($addCondition['sumber_barang'] == "MATERIAL REQUEST PENOLONG") {
            $condition = [
                "stock_revamp.company_id" => $this->this_company_id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp_log.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "MATERIAL REQUEST PENOLONG",
            ];

            $dataQry = $this->stockRevampLogModel->getListLogMaterialRequestPenolong(
                $condition,
                $addCondition,
                $limit,
                $offset
            );
        }

        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $d) {
            array_push($dataResult, [
                'no' => $no++,
                'supplier_name' => isset($d['supplier_name']) ? $d['supplier_name'] : "",
                'kode_barang' => $d['kode_barang'],
                'barang_name' => $d['barang_name'],
                'spesifikasi' => $d['spesifikasi'],
                'divisi' => $d['divisi'],
                'warehouse_name' => $d['warehouse_name'],
                'type_bc' => $d['type_bc'],
                'ref_no' => $d['ref_no'],
                'po_no' => isset($d['po_no']) ? $d['po_no'] : "",
                'tanggal_po' => isset($d['tanggal_po']) ? date('d/m/Y', strtotime($d['tanggal_po'])) : "",
                'createdAt' => date('d/m/Y H:i:s', strtotime($d['createdAt'])),
                'qty' => (float)$d['qty_diterima'],
                'status' => $d['status'],
                'kode_satuan' => $d['kode_satuan']
            ]);
        }


        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }
}
