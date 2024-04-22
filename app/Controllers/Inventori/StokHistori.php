<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\ParentBarangModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
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
    }

    public function index()
    {
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
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
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
            "search" => $this->request->getVar("search"),
            "bc_id" => ""
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            "stock.company_id" => $this->this_company_id,
            "stock_details2.deletedAt" => null,
            "stock.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $dataQry = $this->stockDetail2Model->getListStokLog($condition, $addCondition, $limit, $offset);
        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
            $in_out = $data->status == "In" ? "(+)" : "(-)";

            if ($data->sumber == "LPB" || $data->sumber == "JASA VENDOR") {
                if ($data->no_dokumen1 != "-" && $data->no_dokumen1 != $data->no_dokumen2) {
                    $no_dokumen = $data->no_dokumen1 . " <-> " . $data->no_dokumen2;
                } else {
                    $no_dokumen = $data->no_dokumen2;
                }
            } else {
                $no_dokumen = $data->no_dokumen1;
            }

            if ($data->kemasan_id == 0) {
                // BARANG
                $barang = $this->barangMasterSpesifikasiModel->find($data->barang2_id);
                $satuan_1 = null;
                if ($barang != null) {
                    $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
                }
                $barangMaster = $this->barangMasterModel->find($data->barang1_id);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($data->barang2_id);
                $parentBarang = null;
                if ($barangMaster != null) {
                    $parentBarang = $this->parentBarangModel->find($barangMaster['parent_type_id']);
                }
                $divisi = $this->divisiModel->find($data->divisi_id);
                $warehouse = $this->warehouseModel->find($data->warehouse_id);

                if ($barangMasterSpesifikasi != null && $barangMaster != null && $parentBarang != null) {
                    array_push($dataResult, [
                        "no" => $no++,
                        'parent_type' => $parentBarang != null ? strtoupper(str_replace('_', ' ', $parentBarang['parent_type'])) : '',
                        'parent_name' => $parentBarang != null ? $parentBarang['parent_name'] : '',
                        'kode_barang' => $barangMaster['kode_barang'] ?? '',
                        "barang" => strtoupper(($barangMaster['barang_name'] ?? '') . " - " . ($barangMasterSpesifikasi['spesifikasi'] ?? '')),
                        "divisi" => $divisi != null ? strtoupper($divisi['divisi']) : '',
                        "warehouse" => $warehouse != null ? strtoupper($warehouse['warehouse_name']) : '',
                        "dokumen_pabean" => $bcName . " / " . $data->no_aju,
                        "sumber" => $data->sumber,
                        "tanggal" => date('d/m/Y - H:i:s', strtotime($data->createdAt)),
                        "dokumen" => $no_dokumen,
                        "stok" =>  $in_out . " " . $data->stok_total . ($satuan_1 != null ? " " . $satuan_1['kode_satuan'] : ''),
                    ]);
                }
            } else {
                $kemasan = $this->kemasanModel->find($data->kemasan_id);
                $satuan_1 = null;
                if ($kemasan != null) {
                    $satuan_1 = $this->satuanModel->find($kemasan['satuan_id']);
                }
                $parentBarang = null;
                if ($kemasan != null) {
                    $parentBarang = $this->parentBarangModel->find($kemasan['parent_type_id']);
                }
                $divisi = $this->divisiModel->find($data->divisi_id);
                $warehouse = $this->warehouseModel->find($data->warehouse_id);

                if ($kemasan != null && $parentBarang != null) {
                    array_push($dataResult, [
                        "no" => $no++,
                        'parent_type' => $parentBarang != null ? strtoupper(str_replace('_', ' ', $parentBarang['parent_type'])) : '',
                        'parent_name' => $parentBarang != null ? $parentBarang['parent_name'] : '',
                        'kode_barang' => $kemasan['kode'] ?? '',
                        "barang" => strtoupper($kemasan['name'] ?? ''),
                        "divisi" => $divisi != null ? strtoupper($divisi['divisi']) : '',
                        "warehouse" => $warehouse != null ? strtoupper($warehouse['warehouse_name']) : '',
                        "dokumen_pabean" => $bcName . " / " . $data->no_aju,
                        "sumber" => $data->sumber,
                        "tanggal" => date('d/m/Y - H:i:s', strtotime($data->createdAt)),
                        "dokumen" => $no_dokumen,
                        "stok" =>  $in_out . " " . $data->stok_total . ($satuan_1 != null ? " " . $satuan_1['kode_satuan'] : ''),
                    ]);
                }
            }
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
