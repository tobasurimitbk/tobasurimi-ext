<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;

class StokList extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $warehouseModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $kemasanModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $satuanModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $supplierModel;

    public function __construct()
    {

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->stockModel = new StockModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->satuanModel = new SatuansModel();
        $this->kemasanModel = new KemasanModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehouseModel = new WarehousesModel();
    }

    public function index()
    {
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Warehouse/stock/stock_list', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "search" => $this->request->getVar("search"),
            "parent_type" => $this->request->getVar("parent_type"),
            "parent_name" => $this->request->getVar("parent_name"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar("warehouse_id"),
            "status_stok" => $this->request->getVar("status_stok")
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        if ($addCondition['parent_type'] != "kemasan") {

            $condition = [
                "barang_master.company_id" => $this->this_company_id,
                "barang_master.deletedAt" => null,
                "barang_master_spesifikasi.deletedAt" => null,
                "stock.company_id" => $this->this_company_id,
                "stock.deletedAt" => null,
            ];

            $dataQry = $this->stockModel->getStockListBarang($condition, $addCondition, $limit, $offset);
            $dataResult = [];
            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($dataQry['data'] as $data) {
                $satuan1 = $this->satuanModel->find($data->satuan_1);
                $satuan2 = $this->satuanModel->find($data->satuan_2);
                $satuan3 = $this->satuanModel->find($data->satuan_3);

                if ($addCondition['status_stok'] == "ALL") {
                    array_push($dataResult, [
                        "no"                    => $no++,
                        "id"                    => encrypt($data->id),
                        "parent_type"           => str_replace("_", " ", strtoupper($data->parent_type)),
                        "parent_name"           => $data->parent_name,
                        "kode_barang"           => $data->kode_barang,
                        "barang"                => $data->barang_name . " " . $data->spesifikasi,
                        "divisi"                => $data->divisi,
                        "warehouse"             => $data->warehouse,
                        "stok_1"                => $satuan1 == null ? '-' : number_format($data->qty) . " " . $satuan1['kode_satuan'],
                        "stok_2"                => $satuan2 == null ? '-' : number_format(sprintf("%.2f", $data->qty / $data->konversi_satuan_2)) . " " . $satuan2['kode_satuan'],
                        "stok_3"                => $satuan3 == null ? '-' : number_format(sprintf("%.2f", $data->qty / $data->konversi_satuan_3)) . " " . $satuan3['kode_satuan'],
                    ]);
                } else {
                    if ($addCondition['status_stok'] == 1) {
                        if ($data->qty > 0) {
                            array_push($dataResult, [
                                "no"                    => $no++,
                                "id"                    => encrypt($data->id),
                                "parent_type"           => str_replace("_", " ", strtoupper($data->parent_type)),
                                "parent_name"           => $data->parent_name,
                                "kode_barang"           => $data->kode_barang,
                                "barang"                => $data->barang_name . " " . $data->spesifikasi,
                                "divisi"                => $data->divisi,
                                "warehouse"             => $data->warehouse,
                                "stok_1"                => $satuan1 == null ? '-' : number_format($data->qty) . " " . $satuan1['kode_satuan'],
                                "stok_2"                => $satuan2 == null ? '-' : number_format(sprintf("%.2f", $data->qty / $data->konversi_satuan_2)) . " " . $satuan2['kode_satuan'],
                                "stok_3"                => $satuan3 == null ? '-' : number_format(sprintf("%.2f", $data->qty / $data->konversi_satuan_3)) . " " . $satuan3['kode_satuan'],
                            ]);
                        }
                    } else {
                        if ($data->qty == 0) {
                            array_push($dataResult, [
                                "no"                    => $no++,
                                "id"                    => encrypt($data->id),
                                "parent_type"           => str_replace("_", " ", strtoupper($data->parent_type)),
                                "parent_name"           => $data->parent_name,
                                "kode_barang"           => $data->kode_barang,
                                "barang"                => $data->barang_name . " " . $data->spesifikasi,
                                "divisi"                => $data->divisi,
                                "warehouse"             => $data->warehouse,
                                "stok_1"                => $satuan1 == null ? '-' : number_format($data->qty) . " " . $satuan1['kode_satuan'],
                                "stok_2"                => $satuan2 == null ? '-' : number_format(sprintf("%.2f", $data->qty / $data->konversi_satuan_2)) . " " . $satuan2['kode_satuan'],
                                "stok_3"                => $satuan3 == null ? '-' : number_format(sprintf("%.2f", $data->qty / $data->konversi_satuan_3)) . " " . $satuan3['kode_satuan'],
                            ]);
                        }
                    }
                }
            }
        } else {
            $condition = [
                "kemasan.company_id" => $this->this_company_id,
                "kemasan.deletedAt" => null,
                "stock.company_id" => $this->this_company_id,
                "stock.deletedAt" => null,
            ];

            $dataQry = $this->stockModel->getStockListKemasan($condition, $addCondition, $limit, $offset);
            $dataResult = [];
            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($dataQry['data'] as $data) {
                $satuan1 = $this->satuanModel->find($data->satuan_id);

                if ($addCondition['status_stok'] == "ALL") {
                    array_push($dataResult, [
                        "no"                    => $no++,
                        "id"                    => encrypt($data->id),
                        "parent_type"           => str_replace("_", " ", strtoupper($data->parent_type)),
                        "parent_name"           => $data->parent_name,
                        "kode_barang"           => $data->kode,
                        "barang"                => $data->name,
                        "divisi"                => $data->divisi,
                        "warehouse"             => $data->warehouse,
                        "stok_1"                => number_format($data->qty) . " " . $satuan1['kode_satuan'],
                    ]);
                } else {
                    if ($addCondition['status_stok'] == 1) {
                        if ($data->qty > 0) {
                            array_push($dataResult, [
                                "no"                    => $no++,
                                "id"                    => encrypt($data->id),
                                "parent_type"           => str_replace("_", " ", strtoupper($data->parent_type)),
                                "parent_name"           => $data->parent_name,
                                "kode_barang"           => $data->kode,
                                "barang"                => $data->name,
                                "divisi"                => $data->divisi,
                                "warehouse"             => $data->warehouse,
                                "stok_1"                => number_format($data->qty) . " " .  $satuan1['kode_satuan'],
                            ]);
                        }
                    } else {
                        array_push($dataResult, [
                            "no"                    => $no++,
                            "id"                    => encrypt($data->id),
                            "parent_type"           => str_replace("_", " ", strtoupper($data->parent_type)),
                            "parent_name"           => $data->parent_name,
                            "kode_barang"           => $data->kode,
                            "barang"                => $data->name,
                            "divisi"                => $data->divisi,
                            "warehouse"             => $data->warehouse,
                            "stok_1"                => number_format($data->qty) . " " .  $satuan1['kode_satuan'],
                        ]);
                    }
                }
            }
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'jenisDokAju' => $this->metaDataModel->getByName("jenis_dok_aju")

        ];

        return view('Warehouse/stock/stock_init', $data);
    }

    public function getListBarangNotInit()
    {
        $warehouse_id = $this->request->getVar('warehouse_id');
        $divisi_id = $this->request->getVar('divisi_id');
        $type_barang = $this->request->getVar('type_barang');

        if (!empty($warehouse_id) && !empty($divisi_id) && !empty($type_barang)) {
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => $this->stockModel->getListMasterBarang(
                    $this->this_company_id,
                    $type_barang
                )
            ]);
        }
    }

    public function createInitStok()
    {
        $type_barang = $this->request->getVar('type_barang');
        $divisi_id = $this->request->getVar('divisi_id');
        $warehouse_id = $this->request->getVar('warehouse_id');
        $spesifikasi_id = $this->request->getVar('spesifikasi_id');
        $qty_total = $this->request->getVar('qty_total');

        if ($type_barang == "kemasan") {
            $barang_id = 0;
        } else {
            $barang_id = $this->barangMasterSpesifikasiModel->find($spesifikasi_id)['barang_master_id'];
        }

        if (
            $this->stockModel->isDefinedStockMaster(
                $this->this_company_id,
                $warehouse_id,
                $divisi_id,
                $type_barang,
                $barang_id,
                $spesifikasi_id
            )
        ) {
            // ADA STOK MASTER
            $stokMaster = $this->stockModel->getStokMaster(
                $this->this_company_id,
                $warehouse_id,
                $divisi_id,
                $type_barang,
                $barang_id,
                $spesifikasi_id
            );

            foreach (json_decode($this->request->getVar('list_stock')) as $l) {
                $stokSubDetail = $this->stockModel->isDefinedStockSubDetail(
                    $this->this_company_id,
                    $warehouse_id,
                    $divisi_id,
                    $type_barang,
                    $barang_id,
                    $spesifikasi_id,
                    $l->bc_id,
                    $l->no_aju,
                    $stokMaster['id']
                );

                if ($stokSubDetail != null) {
                    if ($type_barang == "kemasan") {
                        $barangFirst = $this->kemasanModel->select('kemasan.name AS barang')
                            ->find($stokMaster['kemasan_id']);
                    } else {
                        $barangFirst = $this->barangMasterSpesifikasiModel
                            ->select("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang")
                            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
                            ->where('barang_master_spesifikasi.id', $stokMaster['barang2_id'])
                            ->first();
                    }

                    $bcDetail = $this->metaDataModel->find($stokSubDetail['bc_id']);
                    $bcDetailName = $bcDetail == null ? "NON PABEAN" : $bcDetail['value'];
                    $noAju = $bcDetail == null ? "" : $l->no_aju;

                    return response()->setJSON([
                        'status' => false,
                        'token' => csrf_hash(),
                        'message' => 'Gagal inisiasi stok dikarenakan barang ' . $barangFirst['barang'] . ' dengan dokumen ' . $bcDetailName . '. dengan nomor aju ' . $noAju . ' sudah pernah diinisiasi'
                    ]);
                    break;
                }
            }
        }

        // STOK SIAP DI INISIASI

        $stok = $this->stockModel->insertStok(
            $this->this_company_id,
            $warehouse_id,
            $divisi_id,
            $type_barang,
            $barang_id,
            $spesifikasi_id,
            $qty_total
        );

        $stokDetail = $this->stockDetailModel->insertStokDetail(
            $stok,
            $qty_total,
            "In",
            date('Y-m-d'),
            $this->this_user_id,
            "INISIASI",
            "-",
            "-"
        );

        foreach (json_decode($this->request->getVar('list_stock')) as $l) {
            $this->stockDetail2Model->insertStokDetail2(
                $l->bc_id,
                $stok,
                $stokDetail,
                $l->qty,
                $l->no_aju,
                '-'
            );
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'message' => "Stok berhasil di inisiasi",
            'status' => true
        ]);
    }

    public function detail($id)
    {
        $id = decrypt($id);

        if ($this->stockModel->where('company_id', $this->this_company_id)->find($id) == null) {
            return redirect()->to('stock-list');
        }

        $conditionPerDokumen = [
            "stock_details2.stock_id" => $id,
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $conditionInisiasi = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "INISIASI",
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $conditionInisiasi = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "INISIASI",
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $conditionPemasukkanBarang = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "LPB",
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $conditionAdjusment = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "ADJUSMENT",
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $conditionMutasi = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "MUTASI",
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $conditionJasaVendor = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "JASA VENDOR",
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $totalStokPerDokumen = $this->stockDetail2Model->getTotalStockLog($conditionPerDokumen);
        $totalStokInit = $this->stockDetail2Model->getTotalStockLog($conditionInisiasi);
        $totalStokPemasukkanBarang = $this->stockDetail2Model->getTotalStockLog($conditionPemasukkanBarang);
        $totalStokAdjusment = $this->stockDetail2Model->getTotalStockLog($conditionAdjusment);
        $totalStokMutasi = $this->stockDetail2Model->getTotalStockLog($conditionMutasi);
        $totalStokJasaVendor = $this->stockDetail2Model->getTotalStockLog($conditionJasaVendor);

        $stok =  $this->stockModel->find($id);

        $data = [
            'jenisDokAju' => $this->metaDataModel->getByName("jenis_dok_aju"),
            'stok' => $stok,
            'detail' => $this->stockModel->detailStock($id),
            'total' => [
                'totalPerDokumen' => $totalStokPerDokumen,
                'totalPerInit' => $totalStokInit,
                'totalPerPemasukkan' => $totalStokPemasukkanBarang,
                'totalPerAdjusment' => $totalStokAdjusment,
                'totalMutasi' => $totalStokMutasi,
                'totalJasaVendor' => $totalStokJasaVendor
            ],
            'divisi' => $this->divisiModel->find($stok['divisi_id']),
            'warehouse' => $this->warehouseModel->find($stok['warehouse_id'])
        ];

        return view('Warehouse/stock/stock_list_detail', $data);
    }

    public function allStokPerDokumen()
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
            "bc_id" => $this->request->getVar("bc_id"),
            "no_aju" => $this->request->getVar("no_aju"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $stok_id = decrypt($this->request->getVar('stok_id'));
        $stok = $this->stockModel->find($stok_id);

        $condition = [
            "stock_details2.stock_id" => $stok_id,
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $dataQry = $this->stockDetail2Model->getListStokPerDokumen($condition, $addCondition, $limit, $offset);
        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($stok['kemasan_id'] == 0) {
            // BARANG
            $barang = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);
        } else {
            // KEMASAN
            $barang = $this->kemasanModel->find($stok['kemasan_id']);
        }

        foreach ($dataQry['data'] as $data) {
            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($stok['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);

                array_push($dataResult, [
                    "no" => $no++,
                    "bc_type" => $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'],
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "no_aju" => $data->no_aju,
                    "stok_1" => $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                ]);
            } else {
                // KEMASAN
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);

                array_push($dataResult, [
                    "no" => $no++,
                    "bc_type" => $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'],
                    "no_aju" => $data->no_aju,
                    "stok_1" => $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => '-',
                    "stok_3" => '-',
                ]);
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

    public function allStokFiltered()
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
            "bc_id" => $this->request->getVar("bc_id"),
            "no_aju" => $this->request->getVar("no_aju"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $stok_id = decrypt($this->request->getVar('stok_id'));
        $stok = $this->stockModel->find($stok_id);

        $condition = [
            "stock_details2.stock_id" => $stok_id,
            "stock_details.sumber" => trim(($this->request->getVar('sumber'))),
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $dataQry = $this->stockDetail2Model->getListStokPerDokumen($condition, $addCondition, $limit, $offset);
        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($stok['kemasan_id'] == 0) {
            // BARANG
            $barang = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);
        } else {
            // KEMASAN
            $barang = $this->kemasanModel->find($stok['kemasan_id']);
        }

        foreach ($dataQry['data'] as $data) {
            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($stok['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);

                array_push($dataResult, [
                    "no" => $no++,
                    "bc_type" => $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'],
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "no_aju" => $data->no_aju,
                    "stok_1" => $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                ]);
            } else {
                // KEMASAN
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
                array_push($dataResult, [
                    "no" => $no++,
                    "bc_type" => $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'],
                    "barang" => strtoupper($barang['name']),
                    "no_aju" => $data->no_aju,
                    "stok_1" => $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => '-',
                    "stok_3" => '-',
                ]);
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

    public function allStokLogPemasukkanBarang()
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
            "bc_id" => $this->request->getVar("bc_id"),
            "search" => $this->request->getVar("search"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $stok_id = decrypt($this->request->getVar('stok_id'));
        $stok = $this->stockModel->find($stok_id);

        $condition = [
            "stock_details2.stock_id" => $stok_id,
            "stock_details2.deletedAt" => null,
            "stock.deletedAt" => null,
            "stock_details.sumber" => trim($this->request->getVar('sumber')),
            "stock_details.deletedAt" => null,
        ];

        $dataQry = $this->stockDetail2Model->getListStokLog($condition, $addCondition, $limit, $offset);
        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($stok['kemasan_id'] == 0) {
            // BARANG
            $barang = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);
            $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
        } else {
            // KEMASAN
            $barang = $this->kemasanModel->find($stok['kemasan_id']);
            $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
        }

        foreach ($dataQry['data'] as $data) {

            $lpb = $this->penerimaanBarangModel->where('no_penerimaan_barang', $data->no_dokumen1)->first();
            $supplier = $this->supplierModel->find($lpb['supplier_id']);
            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];

            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($data->barang1_id);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($data->barang2_id);
                $harga = $this->penerimaanBarangDetailModel->getHargaTotalPenerimaan($lpb['id']);
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);

                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "po" => $data->no_dokumen2,
                    "dokumen" => $data->no_dokumen1 . " - " . $bcName,
                    "supplier" => $supplier == null ? "-" : $supplier['name'],
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "stok_1" => $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                    "harga" => number_format($harga[0]['harga'])
                ]);
            } else {
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "po" => '-',
                    "dokumen" => $data->no_dokumen1 . " - " . $bcName,
                    "supplier" => $supplier == null ? "-" : $supplier['name'],
                    "barang" => strtoupper($barang['name']),
                    "stok_1" => $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => "-",
                    "stok_3" => "-",
                    "harga" => "-"
                ]);
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

    public function allStokAdjusment()
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
            "bc_id" => $this->request->getVar("bc_id"),
            "search" => $this->request->getVar("search"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $stok_id = decrypt($this->request->getVar('stok_id'));
        $stok = $this->stockModel->find($stok_id);

        $condition = [
            "stock_details2.stock_id" => $stok_id,
            "stock_details2.deletedAt" => null,
            "stock.deletedAt" => null,
            "stock_details.sumber" => trim($this->request->getVar('sumber')),
            "stock_details.deletedAt" => null,
        ];

        $dataQry = $this->stockDetail2Model->getListStokLog($condition, $addCondition, $limit, $offset);
        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($stok['kemasan_id'] == 0) {
            // BARANG
            $barang = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);
            $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
        } else {
            // KEMASAN
            $barang = $this->kemasanModel->find($stok['kemasan_id']);
            $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
        }

        foreach ($dataQry['data'] as $data) {

            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
            $in_out = $data->status == "In" ? "(+)" : "(-)";

            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($data->barang1_id);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($data->barang2_id);
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);

                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "dokumen" => $bcName . " / " . $data->no_aju,
                    "no_adjusment" => $data->no_dokumen1,
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "stok_1" =>  $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                ]);
            } else {
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "dokumen" => $bcName . " / " . $data->no_aju,
                    "no_adjusment" => $data->no_dokumen1,
                    "barang" => strtoupper($barang['name']),
                    "stok_1" => $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => "-",
                    "stok_3" => "-",
                ]);
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

    public function allStokMutasi()
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
            "bc_id" => $this->request->getVar("bc_id"),
            "search" => $this->request->getVar("search"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $stok_id = decrypt($this->request->getVar('stok_id'));
        $stok = $this->stockModel->find($stok_id);

        $condition = [
            "stock_details2.stock_id" => $stok_id,
            "stock_details2.deletedAt" => null,
            "stock.deletedAt" => null,
            "stock_details.sumber" => trim($this->request->getVar('sumber')),
            "stock_details.deletedAt" => null,
        ];

        $dataQry = $this->stockDetail2Model->getListStokLog($condition, $addCondition, $limit, $offset);
        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($stok['kemasan_id'] == 0) {
            // BARANG
            $barang = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);
            $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
        } else {
            // KEMASAN
            $barang = $this->kemasanModel->find($stok['kemasan_id']);
            $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
        }

        foreach ($dataQry['data'] as $data) {

            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
            $in_out = $data->status == "In" ? "(+)" : "(-)";

            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($data->barang1_id);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($data->barang2_id);
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);

                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "dokumen" => $bcName . " / " . $data->no_aju,
                    "no_penerimaan_mutasi" => $data->no_dokumen1,
                    "no_mutasi" => $data->no_dokumen2,
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "stok_1" =>  $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                ]);
            } else {
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "dokumen" => $bcName . " / " . $data->no_aju,
                    "no_penerimaan_mutasi" => $data->no_dokumen1,
                    "no_mutasi" => $data->no_dokumen2,
                    "barang" => strtoupper($barang['name']),
                    "stok_1" => $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => "-",
                    "stok_3" => "-",
                ]);
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

    public function allStokJasaVendor()
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
            "bc_id" => $this->request->getVar("bc_id"),
            "search" => $this->request->getVar("search"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $stok_id = decrypt($this->request->getVar('stok_id'));
        $stok = $this->stockModel->find($stok_id);

        $condition = [
            "stock_details2.stock_id" => $stok_id,
            "stock_details2.deletedAt" => null,
            "stock.deletedAt" => null,
            "stock_details.sumber" => trim($this->request->getVar('sumber')),
            "stock_details.deletedAt" => null,
        ];

        $dataQry = $this->stockDetail2Model->getListStokLog($condition, $addCondition, $limit, $offset);
        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        if ($stok['kemasan_id'] == 0) {
            // BARANG
            $barang = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);
            $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
        } else {
            // KEMASAN
            $barang = $this->kemasanModel->find($stok['kemasan_id']);
            $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
        }

        foreach ($dataQry['data'] as $data) {

            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
            $in_out = $data->status == "In" ? "(+)" : "(-)";

            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($data->barang1_id);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($data->barang2_id);
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);

                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "dokumen" => $bcName . " / " . $data->no_aju,
                    "no_penerimaan_surat_jalan" => $data->no_dokumen1,
                    "no_surat_jalan" => $data->no_dokumen2,
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "stok_1" =>  $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                ]);
            } else {
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "dokumen" => $bcName . " / " . $data->no_aju,
                    "no_penerimaan_surat_jalan" => $data->no_dokumen1,
                    "no_surat_jalan" => $data->no_dokumen2,
                    "barang" => strtoupper($barang['name']),
                    "stok_1" => $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                    "stok_2" => "-",
                    "stok_3" => "-",
                ]);
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
