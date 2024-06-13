<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\AdjusmentModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC27Model;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\MutasiModel;
use App\Models\ParentBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanMutasiModel;
use App\Models\PPBKBModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    protected $adjusmentModel;
    protected $parentBarangModel;
    protected $ppbkbModel;
    protected $bc27Model;
    protected $mutasiModel;

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
        $this->adjusmentModel = new AdjusmentModel();
        $this->parentBarangModel = new ParentBarangModel();
        $this->ppbkbModel = new PPBKBModel();
        $this->bc27Model = new BC27Model();
        $this->mutasiModel = new MutasiModel();
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
            "status_stok" => $this->request->getVar("status_stok"),
            "kode" =>  $this->request->getVar("search"),
            "kode_barang" =>  $this->request->getVar("search"),
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
                $data->qty = $this->stockModel->detailStock($data->id)['stok']['stokSekarang'];
                if ($addCondition['status_stok'] == "ALL") {
                    array_push($dataResult, [
                        "no"                    => $no++,
                        "id"                    => encrypt($data->id),
                        "parent_type"           => strtoupper(str_replace("_", " ", strtoupper($data->parent_type))),
                        "parent_name"           => strtoupper($data->parent_name),
                        "kode_barang"           => strtoupper($data->kode_barang),
                        "barang"                => strtoupper($data->barang_name . "-" . $data->spesifikasi),
                        "divisi"                => strtoupper($data->divisi),
                        "warehouse"             => strtoupper($data->warehouse),
                        "stok_1"                => $satuan1 == null ? '-' : ($data->qty) . " " . $satuan1['kode_satuan'],
                        "stok_2"                => $satuan2 == null ? '-' : (sprintf("%.2f", $data->qty / $data->konversi_satuan_2)) . " " . $satuan2['kode_satuan'],
                        "stok_3"                => $satuan3 == null ? '-' : (sprintf("%.2f", $data->qty / $data->konversi_satuan_3)) . " " . $satuan3['kode_satuan'],
                    ]);
                } else {
                    if ($addCondition['status_stok'] == 1) {
                        if ($data->qty > 0) {
                            array_push($dataResult, [
                                "no"                    => $no++,
                                "id"                    => encrypt($data->id),
                                "parent_type"           => strtoupper(str_replace("_", " ", strtoupper($data->parent_type))),
                                "parent_name"           => strtoupper($data->parent_name),
                                "kode_barang"           => strtoupper($data->kode_barang),
                                "barang"                => strtoupper($data->barang_name . "-" . $data->spesifikasi),
                                "divisi"                => strtoupper($data->divisi),
                                "warehouse"             => strtoupper($data->warehouse),
                                "stok_1"                => $satuan1 == null ? '-' : ($data->qty) . " " . $satuan1['kode_satuan'],
                                "stok_2"                => $satuan2 == null ? '-' : (sprintf("%.2f", $data->qty / $data->konversi_satuan_2)) . " " . $satuan2['kode_satuan'],
                                "stok_3"                => $satuan3 == null ? '-' : (sprintf("%.2f", $data->qty / $data->konversi_satuan_3)) . " " . $satuan3['kode_satuan'],
                            ]);
                        }
                    } else {
                        if ($data->qty == 0) {
                            array_push($dataResult, [
                                "no"                    => $no++,
                                "id"                    => encrypt($data->id),
                                "parent_type"           => strtoupper(str_replace("_", " ", strtoupper($data->parent_type))),
                                "parent_name"           => strtoupper($data->parent_name),
                                "kode_barang"           => strtoupper($data->kode_barang),
                                "barang"                => strtoupper($data->barang_name . "-" . $data->spesifikasi),
                                "divisi"                => strtoupper($data->divisi),
                                "warehouse"             => strtoupper($data->warehouse),
                                "stok_1"                => $satuan1 == null ? '-' : ($data->qty) . " " . $satuan1['kode_satuan'],
                                "stok_2"                => $satuan2 == null ? '-' : (sprintf("%.2f", $data->qty / $data->konversi_satuan_2)) . " " . $satuan2['kode_satuan'],
                                "stok_3"                => $satuan3 == null ? '-' : (sprintf("%.2f", $data->qty / $data->konversi_satuan_3)) . " " . $satuan3['kode_satuan'],
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
                $data->qty = $this->stockModel->detailStock($data->id)['stok']['stokSekarang'];

                if ($addCondition['status_stok'] == "ALL") {
                    array_push($dataResult, [
                        "no"                    => $no++,
                        "id"                    => encrypt($data->id),
                        "parent_type"           => strtoupper(str_replace("_", " ", strtoupper($data->parent_type))),
                        "parent_name"           => strtoupper($data->parent_name),
                        "kode_barang"           => strtoupper($data->kode),
                        "barang"                => strtoupper($data->name),
                        "divisi"                => strtoupper($data->divisi),
                        "warehouse"             => strtoupper($data->warehouse),
                        "stok_1"                => ($data->qty) . " " . $satuan1['kode_satuan'],
                    ]);
                } else {
                    if ($addCondition['status_stok'] == 1) {
                        if ($data->qty > 0) {
                            array_push($dataResult, [
                                "no"                    => $no++,
                                "id"                    => encrypt($data->id),
                                "parent_type"           => strtoupper(str_replace("_", " ", strtoupper($data->parent_type))),
                                "parent_name"           => strtoupper($data->parent_name),
                                "kode_barang"           => strtoupper($data->kode),
                                "barang"                => strtoupper($data->name),
                                "divisi"                => strtoupper($data->divisi),
                                "warehouse"             => strtoupper($data->warehouse),
                                "stok_1"                => ($data->qty) . " " .  $satuan1['kode_satuan'],
                            ]);
                        }
                    } else {
                        if ($data->qty == 0) {
                            array_push($dataResult, [
                                "no"                    => $no++,
                                "id"                    => encrypt($data->id),
                                "parent_type"           => strtoupper(str_replace("_", " ", strtoupper($data->parent_type))),
                                "parent_name"           => strtoupper($data->parent_name),
                                "kode_barang"           => strtoupper($data->kode),
                                "barang"                => strtoupper($data->name),
                                "divisi"                => strtoupper($data->divisi),
                                "warehouse"             => strtoupper($data->warehouse),
                                "stok_1"                => ($data->qty) . " " .  $satuan1['kode_satuan'],
                            ]);
                        }
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

    public function import()
    {
        $rules = [
            "file" => [
                'rules' => 'uploaded[file]|ext_in[file,xlsx]',
                'errors' => [
                    'uploaded' => 'Tidak ada file yang di-upload.',
                    'ext_in' => 'File yang di-upload harus berupa file Excel (.xlsx).',
                ],

            ],
        ];

        if ($this->validate($rules)) {

            $file = $this->request->getFile('file');

            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $data = [];
            $rowIterator = $worksheet->getRowIterator(2);
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            $gagalArr = [];
            $berhasilTotal = 0;

            // INSERT INVENTORI
            for ($i = 0; $i < count($data); $i++) {

                $type_barang = str_replace(' ', '_', strtolower(trim($data[$i][0])));
                $type_barang = str_replace('barang', 'bahan', $type_barang);

                // VALIDASI TIPE BARANG
                $parentBarang = $this->parentBarangModel
                    ->where('parent_type', trim($type_barang))
                    ->where('parent_name', trim($data[$i][1]))
                    ->where('company_id', $this->this_company_id)
                    ->where('deletedAt', null)
                    ->first();

                // VALIDASI DEPARTEMEN
                $divisi = $this->divisiModel
                    ->where('company_id', $this->this_company_id)
                    ->where(
                        'divisi',
                        trim($data[$i][4])
                    )->first();

                if ($parentBarang != null && $divisi != null) {
                    // VALIDASI WAREHOUSE
                    $warehouse = $this->warehouseModel->where('divisi_id', $divisi['id'])->where('code_warehouse', trim($data[$i][5]))->first();
                    // VALIDASI JENIS DOK AJU (PABEAN)
                    if (trim($data[$i][6]) == "NON PABEAN") {
                        $bc_id = 0;
                        $no_aju = "-";
                    } else {
                        $bc = $this->metaDataModel->where('name', 'jenis_dok_aju')->where('value', trim($data[$i][6]))->first();
                        if ($bc != null) {
                            $bc_id = $bc['id'];
                            $no_aju = $data[$i][7];
                        } else {
                            $bc_id = null;
                            $no_aju = null;
                        }
                    }


                    if ($type_barang != "kemasan") {
                        // INI BARANG
                        // VALIDASI BARANG
                        $barangMaster = $this->barangMasterModel
                            ->where('kode_barang', trim($data[$i][2]))
                            ->where('company_id', $this->this_company_id)
                            ->first();

                        if ($barangMaster != null) {
                            // SPESIFIKASI
                            $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel
                                ->where('barang_master_id', $barangMaster['id'])
                                ->where('spesifikasi', trim($data[$i][3]))
                                ->first();

                            if ($barangMasterSpesifikasi != null) {
                                $spesifikasi_id = $barangMasterSpesifikasi['id'];
                            } else {
                                // GAGAL
                                $spesifikasi_id = null;
                            }
                            $barang_id = $barangMaster['id'];
                        } else {
                            // GAGAL
                            $barang_id = null;
                            $spesifikasi_id = null;
                        }
                    } else {
                        // INI KEMASAN
                        $barang_id = 0;
                        // CARI KEMASAN
                        $kemasan = $this->kemasanModel
                            ->where('company_id', $this->this_company_id)
                            ->where('kode', trim($data[$i][2]))
                            ->first();

                        if ($kemasan != null) {
                            $spesifikasi_id = $kemasan['id'];
                        } else {
                            $spesifikasi_id = null;
                        }
                    }

                    // START INISIASI
                    // VARIABEL
                    $warehouse_id = ($warehouse == null) ? null : $warehouse['id'];
                    $divisi_id = ($divisi == null) ? null : $divisi['id'];
                    $type_barang = $type_barang;
                    $barang_id = $barang_id;
                    $spesifikasi_id = $spesifikasi_id;
                    $bc_id = $bc_id;
                    $qty = $data[$i][8];
                    $is_init = true;

                    if ($divisi_id != null && $warehouse_id != null && $barang_id !== null && $spesifikasi_id != null && $bc_id !== null && $no_aju != null) {
                        // CHECK STOK
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

                            $stokSubDetail = $this->stockModel->isDefinedStockSubDetail(
                                $this->this_company_id,
                                $warehouse_id,
                                $divisi_id,
                                $type_barang,
                                $barang_id,
                                $spesifikasi_id,
                                $bc_id,
                                $no_aju,
                                $stokMaster['id']
                            );

                            if ($stokSubDetail != null) {
                                // GAGAL KARENA SUDAH INISIASI
                                $is_init = false;
                            } else {
                                // 
                                $is_init = true;
                            }
                        }

                        if ($is_init) {
                            $stok = $this->stockModel->insertStok(
                                $this->this_company_id,
                                $warehouse_id,
                                $divisi_id,
                                $type_barang,
                                $barang_id,
                                $spesifikasi_id,
                                $qty
                            );

                            $stokDetail = $this->stockDetailModel->insertStokDetail(
                                $stok,
                                $qty,
                                "In",
                                date('Y-m-d'),
                                $this->this_user_id,
                                "INISIASI",
                                "-",
                                "-"
                            );

                            $this->stockDetail2Model->insertStokDetail2(
                                $bc_id,
                                $stok,
                                $stokDetail,
                                $qty,
                                $no_aju,
                                '-'
                            );
                            // BERHASIL
                            $berhasilTotal++;
                        } else {
                            // GAGAL
                            array_push($gagalArr, $data[$i]);
                        }
                    } else {
                        array_push($gagalArr, $data[$i]);
                    }
                } else {
                    array_push($gagalArr, $data[$i]);
                }
            }

            $gagalTotal = count($gagalArr);

            return response()->setJSON([
                'message' => "Berhasil Import : $berhasilTotal Data, Gagal Import : $gagalTotal",
                'status' => true,
                'gagal' => $gagalArr,
                'token' => csrf_hash()
            ]);
        } else {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJSON($data);
        }
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

        $conditionProduksiOut = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "PRODUKSI",
            "stock_details2.deletedAt" => null,
            "stock_details.status" => "Out",
            "stock_details.deletedAt" => null,
        ];

        $conditionProduksiIn = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "PRODUKSI",
            "stock_details2.deletedAt" => null,
            "stock_details.status" => "In",
            "stock_details.deletedAt" => null,
        ];

        $conditionRebus = [
            "stock_details2.stock_id" => $id,
            "stock_details.sumber" => "REBUS",
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
        ];

        $totalStokPerDokumen = $this->stockDetail2Model->getTotalStockLog($conditionPerDokumen);
        $totalStokInit = $this->stockDetail2Model->getTotalStockLog($conditionInisiasi);
        $totalStokPemasukkanBarang = $this->stockDetail2Model->getTotalStockLog($conditionPemasukkanBarang);
        $totalStokAdjusment = $this->stockDetail2Model->getTotalStockLog($conditionAdjusment);
        $totalStokMutasi = $this->stockDetail2Model->getTotalStockLog($conditionMutasi);
        $totalStokJasaVendor = $this->stockDetail2Model->getTotalStockLog($conditionJasaVendor);
        $totalStokProduksiOut = $this->stockDetail2Model->getTotalStockLog($conditionProduksiOut);
        $totalStokProduksiIn = $this->stockDetail2Model->getTotalStockLog($conditionProduksiIn);
        $totalStokRebus = $this->stockDetail2Model->getTotalStockLog($conditionRebus);

        $stok =  $this->stockModel->find($id);

        $data = [
            'jenisDokAju' => $this->metaDataModel->getByName("jenis_dok_aju"),
            'tipeAdjusment' => $this->metaDataModel->where('deletedAt', null)->where('name', "Tipe Adjusment")->findAll(),
            'stok' => $stok,
            'detail' => $this->stockModel->detailStock($id),
            'total' => [
                'totalPerDokumen' => $totalStokPerDokumen,
                'totalPerInit' => $totalStokInit,
                'totalPerPemasukkan' => $totalStokPemasukkanBarang,
                'totalPerAdjusment' => $totalStokAdjusment,
                'totalMutasi' => $totalStokMutasi,
                'totalJasaVendor' => $totalStokJasaVendor,
                'totalProduksiIn' => $totalStokProduksiIn,
                'totalProduksiOut' => $totalStokProduksiOut,
                'totalRebus' => $totalStokRebus
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
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
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $lpb = $this->penerimaanBarangModel->like('multiple_po_no', $data->no_dokumen2)->first();
            } else {
                // KEMASAN
                $lpb = $this->penerimaanBarangModel->where('no_penerimaan_barang', $data->no_dokumen2)->first();
            }
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
                    "dokumen" => $data->no_dokumen1,
                    "dokumen_pabean" => $bcName . " / " . $data->no_aju,
                    "supplier" => $supplier == null ? "-" : strtoupper($supplier['name']),
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
                    "dokumen" => $data->no_dokumen1,
                    "dokumen_pabean" => $bcName . " / " . $data->no_aju,
                    "supplier" => $supplier == null ? "-" : strtoupper($supplier['name']),
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $stok_id = decrypt($this->request->getVar('stok_id'));
        $tipe_adjusment = $this->request->getVar('tipe_adjusment');
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
                $adjusment = $this->adjusmentModel->where('no_adjusment', $data->no_dokumen1)->first();

                if (empty($tipe_adjusment)) {
                    array_push($dataResult, [
                        "no" => $no++,
                        "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                        "dokumen" => $bcName . " / " . $data->no_aju,
                        "no_adjusment" => $data->no_dokumen1,
                        "tipe_adjusment" => $adjusment != null ? $adjusment['tipe_adjusment'] : "-",
                        "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                        "keterangan" => strtoupper($data->keterangan),
                        "stok_1" =>  $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                        "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                        "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                    ]);
                } else {
                    if ($adjusment != null) {
                        if ($tipe_adjusment == $adjusment['tipe_adjusment']) {
                            array_push($dataResult, [
                                "no" => $no++,
                                "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                                "dokumen" => $bcName . " / " . $data->no_aju,
                                "no_adjusment" => $data->no_dokumen1,
                                "tipe_adjusment" => $adjusment != null ? $adjusment['tipe_adjusment'] : "-",
                                "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                                "keterangan" => strtoupper($data->keterangan),
                                "stok_1" =>  $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                                "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                                "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", $data->stok_total / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                            ]);
                        }
                    }
                }
            } else {
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
                $adjusment = $this->adjusmentModel->where('no_adjusment', $data->no_dokumen1)->first();

                if (empty($tipe_adjusment)) {
                    array_push($dataResult, [
                        "no" => $no++,
                        "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                        "dokumen" => $bcName . " / " . $data->no_aju,
                        "no_adjusment" => $data->no_dokumen1,
                        "tipe_adjusment" => $adjusment != null ? $adjusment['tipe_adjusment'] : "-",
                        "barang" => strtoupper($barang['name']),
                        "keterangan" => strtoupper($data->keterangan),
                        "stok_1" => $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                        "stok_2" => "-",
                        "stok_3" => "-",
                    ]);
                } else {
                    if ($adjusment != null) {
                        if ($tipe_adjusment == $adjusment['tipe_adjusment']) {
                            array_push($dataResult, [
                                "no" => $no++,
                                "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                                "dokumen" => $bcName . " / " . $data->no_aju,
                                "no_adjusment" => $data->no_dokumen1,
                                "tipe_adjusment" => $adjusment != null ? $adjusment['tipe_adjusment'] : "-",
                                "barang" => strtoupper($barang['name']),
                                "keterangan" => strtoupper($data->keterangan),
                                "stok_1" => $in_out . " " . $data->stok_total . " " . $satuan_1['kode_satuan'],
                                "stok_2" => "-",
                                "stok_3" => "-",
                            ]);
                        }
                    }
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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

            $in_out = $data->status == "In" ? "(+)" : "(-)";

            $dokumenPabeanMutasi = "-";

            $ppbkb = $this->ppbkbModel
                ->join('mutasi', 'mutasi.id = ppbkb.mutasi_id', 'left')
                ->where('no_mutasi', $data->no_dokumen2)
                ->first();

            $bc27 = $this->bc27Model
                ->join('mutasi_global', 'mutasi_global.id = bc_27.mutasi_global_id', 'left')
                ->where('no_mutasi', $data->no_dokumen2)
                ->first();

            if ($ppbkb != null) {
                $dokumenPabeanMutasi = "PPB-KB / " . $ppbkb['no_ppbkb'];
            }

            if ($bc27 != null) {
                $dokumenPabeanMutasi = "BC 2.7 / " . $bc27['no_aju'];
            }

            if ($data->status == 'In') {
                // MASUK (CARI DI PENERIMAAN MUTASI)
                $mutasi = $this->mutasiModel
                    ->select('bc_id, no_aju')
                    ->join('mutasi_detail', 'mutasi.id = mutasi_detail.mutasi_id', 'left')
                    ->where('mutasi.no_mutasi', $data->no_dokumen2)
                    ->first();
                if ($mutasi != null) {
                    $dokumenBC = $this->metaDataModel->find($mutasi['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    // MASUK 
                    $dokumenAsal =  $bcName . " / " . $mutasi['no_aju'];
                } else {
                    $dokumenAsal =  "-";
                }
            } else {
                $dokumenBC = $this->metaDataModel->find($data->bc_id);
                $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                // KELUAR 
                $dokumenAsal =  $bcName . " / " . $data->no_aju;
            }

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
                    "dokumen_asal" => $dokumenAsal,
                    "no_penerimaan_mutasi" => $data->no_dokumen1,
                    "dokumen_pabean_mutasi" => $dokumenPabeanMutasi,
                    "no_mutasi" => $data->no_dokumen2,
                    "supplier_name" => $data->supplier_name,
                    "no_po" => $data->no_po,
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
                    "dokumen_asal" => $dokumenAsal,
                    "dokumen_pabean_mutasi" => $dokumenPabeanMutasi,
                    "dokumen" => $bcName . " / " . $data->no_aju,
                    "no_penerimaan_mutasi" => $data->no_dokumen1,
                    "no_mutasi" => $data->no_dokumen2,
                    "supplier_name" => $data->supplier_name,
                    "no_po" => "-",
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
                    "supplier_name" => $data->supplier_name,
                    "no_po" => $data->no_po,
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
                    "supplier_name" => $data->supplier_name,
                    "no_po" => "-",
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

    public function allStokProduksi()
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
            "stock_details.status" => trim($this->request->getVar('status')),
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
                    "no_dokumen1" => $data->no_dokumen1,
                    "no_dokumen2" => $data->no_dokumen2,
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
                    "no_dokumen1" => $data->no_dokumen1,
                    "no_dokumen2" => $data->no_dokumen2,
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

    public function allStokRebus()
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
                    "supplier_name" => $data->supplier_name,
                    "no_dokumen1" => $data->no_dokumen1,
                    "no_dokumen2" => $data->no_dokumen2,
                    "stock_dokumen" => $data->stock_dokumen,
                    "no_po" => $data->no_po,
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
                    "supplier_name" => $data->supplier_name,
                    "no_dokumen1" => $data->no_dokumen1,
                    "no_dokumen2" => $data->no_dokumen2,
                    "stock_dokumen" => $data->stock_dokumen,
                    "no_po" => "-",
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

    public function exportExcel()
    {

        $filename = "EXPORT_STOCK_LIST";


        $search        = $this->request->getVar("search");
        $parent_type      = $this->request->getVar("parent_type");
        $parent_name      = $this->request->getVar("parent_name");
        $divisi_id      = $this->request->getVar("divisi_id");
        $warehouse_id      = $this->request->getVar("warehouse_id");
        $status_stok      = $this->request->getVar("status_stok");
        $sort        = $this->request->getVar("sort");
        $sortType      = $this->request->getVar("sortType");

        if ($warehouse_id  == "null") {
            $warehouse_id = "";
        }



        // if ($status_stok  == "ALL") {
        //     $status_stok = "";
        // }




        if ($parent_type != "kemasan") {
            $condition = [
                "barang_master.company_id" => $this->this_company_id,
                "barang_master.deletedAt" => NULL,
                "barang_master_spesifikasi.deletedAt" => NULL,
                "stock.company_id" => $this->this_company_id,
                "stock.deletedAt" => NULL,
                "stock_details2.deletedAt" => null,
                "stock_details.deletedAt" => null,
            ];

            $availableSort = [
                'parent_barang.parent_type' => 'parent_barang.parent_type',
                'parent_barang.parent_name' => 'parent_barang.parent_name',
                'barang_master.kode_barang' => 'barang_master.kode_barang',
                'barang_master.barang_name' => 'barang_master.barang_name',
                'divisis.divisi' => 'divisis.divisi',
                'warehouses.warehouse_name' => 'warehouses.warehouse_name',
                'stock.qty' => 'stock.qty'
            ];

            $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

            $sort = $availableSort[$sort ?? 'createdAt'] ?? 'parent_barang.parent_type';
            $sortType = $availableSortType[$sortType ?? 'desc'] ?? 'DESC';

            $selectQry = '
                stock.id,
                stock_details.qty,
                parent_barang.parent_type,
                parent_barang.parent_name,
                barang_master.kode_barang,
                barang_master.barang_name,
                divisis.divisi,
                warehouses.warehouse_name AS warehouse,
                barang_master_spesifikasi.spesifikasi,
                barang_master_spesifikasi.satuan_1,
                barang_master_spesifikasi.satuan_2,
                barang_master_spesifikasi.satuan_3,
                barang_master_spesifikasi.konversi_satuan_2,
                barang_master_spesifikasi.konversi_satuan_3,
                stock_details2.bc_id,
                stock_details2.stock_detail_id,
                stock_details2.no_aju,
                stock_details.stock_date,
                (SUM(CASE WHEN stock_details.status = "In" 
                THEN stock_details2.qty ELSE 0 END) - 
                SUM(CASE WHEN stock_details.status = "Out" 
                THEN stock_details2.qty ELSE 0 END)) 
                AS stok_total,
            ';
            $stockListQry = $this->stockDetail2Model->select($selectQry)
                ->join('stock', 'stock.id = stock_details2.stock_id')
                ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
                ->join('barang_master', 'barang_master.id = stock.barang1_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
                ->join('warehouses', 'warehouses.id = stock.warehouse_id')
                ->join('divisis', 'divisis.id = stock.divisi_id')
                ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
                ->where($condition)
                ->groupBy('stock_details2.bc_id')
                ->groupBy('stock_details2.stock_id')
                ->groupBy('stock_details2.no_aju')
                ->orderBy($sort, $sortType)
                ->orderBy('stock_details2.stock_id', 'ASC')
                ->orderBy('stock_details2.bc_id', 'DESC');

            if ($parent_type || $parent_name || $divisi_id || $warehouse_id || $search) {
                $stockListQry->groupStart();
            }

            if ($parent_type) {
                $stockListQry->where('parent_barang.parent_type', $parent_type);
            }

            if ($parent_name) {
                $stockListQry->where('parent_barang.id', $parent_name);
            }


            if ($divisi_id) {

                $stockListQry->where('stock.divisi_id', $divisi_id);
            }

            if ($warehouse_id) {
                $stockListQry->where('stock.warehouse_id', $warehouse_id);
            }


            if ($search) {
                $stockListQry->like("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $search)
                    ->orLike('barang_master.kode_barang', $search);
            }

            if ($parent_type || $parent_name || $divisi_id || $warehouse_id || $search) {
                $stockListQry->groupEnd();
            }


            $getAllStockListData = $stockListQry->findAll();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle('A1:J1')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);


            if (empty($getAllStockListData)) {
                $sheet->setCellValue('A1', 'Tidak Ada Data Satuan');
            } else {
                $sheet->setCellValue('A1', 'NO');
                $sheet->setCellValue('B1', 'TIPE BARANG');
                $sheet->setCellValue('C1', 'KATEGORI');
                $sheet->setCellValue('D1', 'KODE');
                $sheet->setCellValue('E1', 'BARANG');
                $sheet->setCellValue('F1', 'DEPARTEMEN');
                $sheet->setCellValue('G1', 'WAREHOUSE');
                $sheet->setCellValue('H1', 'BC');
                $sheet->setCellValue('I1', 'NO AJU');
                $sheet->setCellValue('J1', 'QTY SATUAN 1');
                $sheet->setCellValue('K1', 'QTY SATUAN 2');
                $sheet->setCellValue('L1', 'QTY SATUAN 3');


                $sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);



                $no = 1;
                $numRow = 2;

                foreach ($getAllStockListData as $row) :
                    $dokumenBC = $this->metaDataModel->find($row['bc_id']);
                    $satuan1 = $this->satuanModel->find($row['satuan_1']);
                    $satuan2 = $this->satuanModel->find($row['satuan_2']);
                    $satuan3 = $this->satuanModel->find($row['satuan_3']);


                    if ($status_stok == "1") {
                        if ($row['stok_total'] > 0) {
                            $sheet->setCellValue('A' . $numRow, $no);
                            $sheet->setCellValue('B' . $numRow, $row['parent_type']);
                            $sheet->setCellValue('C' . $numRow, $row['parent_name']);
                            $sheet->setCellValue('D' . $numRow, $row['kode_barang']);
                            $sheet->setCellValue('E' . $numRow, $row['barang_name'] . '-' . $row['spesifikasi']);
                            $sheet->setCellValue('F' . $numRow, $row['divisi']);
                            $sheet->setCellValue('G' . $numRow, $row['warehouse']);
                            $sheet->setCellValue('H' . $numRow, $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value']);
                            $sheet->setCellValue('I' . $numRow, $row['no_aju']);
                            $sheet->setCellValue('J' . $numRow, $satuan1 == null ? '-' : ($row['stok_total']) . " " . $satuan1['kode_satuan']);
                            $sheet->setCellValue('K' . $numRow, $satuan2 == null ? '-' : ($row['stok_total']) . " " . $satuan2['kode_satuan']);
                            $sheet->setCellValue('L' . $numRow, $satuan3 == null ? '-' : ($row['stok_total']) . " " . $satuan3['kode_satuan']);


                            // Auto size columns A-J
                            $sheet->getColumnDimension('A')->setAutoSize(true);
                            $sheet->getColumnDimension('B')->setAutoSize(true);
                            $sheet->getColumnDimension('C')->setAutoSize(true);
                            $sheet->getColumnDimension('D')->setAutoSize(true);
                            $sheet->getColumnDimension('E')->setAutoSize(true);
                            $sheet->getColumnDimension('F')->setAutoSize(true);
                            $sheet->getColumnDimension('G')->setAutoSize(true);
                            $sheet->getColumnDimension('H')->setAutoSize(true);
                            $sheet->getColumnDimension('I')->setAutoSize(true);
                            $sheet->getColumnDimension('J')->setAutoSize(true);
                            $sheet->getColumnDimension('K')->setAutoSize(true);
                            $sheet->getColumnDimension('L')->setAutoSize(true);


                            $no++;
                            $numRow++;
                        }
                    } elseif ($status_stok == "0") {
                        if ($row['stok_total'] == 0) {
                            $sheet->setCellValue('A' . $numRow, $no);
                            $sheet->setCellValue('B' . $numRow, $row['parent_type']);
                            $sheet->setCellValue('C' . $numRow, $row['parent_name']);
                            $sheet->setCellValue('D' . $numRow, $row['kode_barang']);
                            $sheet->setCellValue('E' . $numRow, $row['barang_name'] . '-' . $row['spesifikasi']);
                            $sheet->setCellValue('F' . $numRow, $row['divisi']);
                            $sheet->setCellValue('G' . $numRow, $row['warehouse']);
                            $sheet->setCellValue('H' . $numRow, $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value']);
                            $sheet->setCellValue('I' . $numRow, $row['no_aju']);
                            $sheet->setCellValue('J' . $numRow, $satuan1 == null ? '-' : ($row['stok_total']) . " " . $satuan1['kode_satuan']);
                            $sheet->setCellValue('K' . $numRow, $satuan2 == null ? '-' : ($row['stok_total']) . " " . $satuan2['kode_satuan']);
                            $sheet->setCellValue('L' . $numRow, $satuan3 == null ? '-' : ($row['stok_total']) . " " . $satuan3['kode_satuan']);


                            // Auto size columns A-J
                            $sheet->getColumnDimension('A')->setAutoSize(true);
                            $sheet->getColumnDimension('B')->setAutoSize(true);
                            $sheet->getColumnDimension('C')->setAutoSize(true);
                            $sheet->getColumnDimension('D')->setAutoSize(true);
                            $sheet->getColumnDimension('E')->setAutoSize(true);
                            $sheet->getColumnDimension('F')->setAutoSize(true);
                            $sheet->getColumnDimension('G')->setAutoSize(true);
                            $sheet->getColumnDimension('H')->setAutoSize(true);
                            $sheet->getColumnDimension('I')->setAutoSize(true);
                            $sheet->getColumnDimension('J')->setAutoSize(true);
                            $sheet->getColumnDimension('K')->setAutoSize(true);
                            $sheet->getColumnDimension('L')->setAutoSize(true);


                            $no++;
                            $numRow++;
                        }
                    } else {
                        $sheet->setCellValue('A' . $numRow, $no);
                        $sheet->setCellValue('B' . $numRow, $row['parent_type']);
                        $sheet->setCellValue('C' . $numRow, $row['parent_name']);
                        $sheet->setCellValue('D' . $numRow, $row['kode_barang']);
                        $sheet->setCellValue('E' . $numRow, $row['barang_name'] . '-' . $row['spesifikasi']);
                        $sheet->setCellValue('F' . $numRow, $row['divisi']);
                        $sheet->setCellValue('G' . $numRow, $row['warehouse']);
                        $sheet->setCellValue('H' . $numRow, $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value']);
                        $sheet->setCellValue('I' . $numRow, $row['no_aju']);
                        $sheet->setCellValue('J' . $numRow, $satuan1 == null ? '-' : ($row['stok_total']) . " " . $satuan1['kode_satuan']);
                        $sheet->setCellValue('K' . $numRow, $satuan2 == null ? '-' : ($row['stok_total']) . " " . $satuan2['kode_satuan']);
                        $sheet->setCellValue('L' . $numRow, $satuan3 == null ? '-' : ($row['stok_total']) . " " . $satuan3['kode_satuan']);


                        // Auto size columns A-J
                        $sheet->getColumnDimension('A')->setAutoSize(true);
                        $sheet->getColumnDimension('B')->setAutoSize(true);
                        $sheet->getColumnDimension('C')->setAutoSize(true);
                        $sheet->getColumnDimension('D')->setAutoSize(true);
                        $sheet->getColumnDimension('E')->setAutoSize(true);
                        $sheet->getColumnDimension('F')->setAutoSize(true);
                        $sheet->getColumnDimension('G')->setAutoSize(true);
                        $sheet->getColumnDimension('H')->setAutoSize(true);
                        $sheet->getColumnDimension('I')->setAutoSize(true);
                        $sheet->getColumnDimension('J')->setAutoSize(true);
                        $sheet->getColumnDimension('K')->setAutoSize(true);
                        $sheet->getColumnDimension('L')->setAutoSize(true);


                        $no++;
                        $numRow++;
                    }
                endforeach;


                $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow())
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        } else {
            $condition = [
                "kemasan.company_id" => $this->this_company_id,
                "kemasan.deletedAt" => NULL,
                "stock.company_id" => $this->this_company_id,
                "stock.deletedAt" => NULL,
            ];

            $availableSort = [
                'parent_barang.parent_type' => 'parent_barang.parent_type',
                'parent_barang.parent_name' => 'parent_barang.parent_name',
                'barang_master.kode_barang' => 'kemasan.kode',
                'barang_master.barang_name' => 'kemasan.name',
                'divisis.divisi' => 'divisis.divisi',
                'warehouses.warehouse_name' => 'warehouses.warehouse_name',
                'stock.qty' => 'stock.qty'
            ];

            $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

            $sort = $availableSort[$sort ?? 'createdAt'] ?? 'parent_barang.parent_type';
            $sortType = $availableSortType[$sortType ?? 'desc'] ?? 'DESC';

            $selectQry = '
            stock.id,
            stock.qty,
            stock.createdAt,
            parent_barang.parent_type,
            parent_barang.parent_name,
            parent_barang.createdAt,
            kemasan.kode,
            kemasan.name,
            kemasan.satuan_id,
            divisis.divisi,
            warehouses.warehouse_name AS warehouse,
        ';
            $stockListQry = $this->stockModel->select($selectQry)
                ->join('kemasan', 'kemasan.id = stock.kemasan_id')
                ->join('warehouses', 'warehouses.id = stock.warehouse_id')
                ->join('divisis', 'divisis.id = stock.divisi_id')
                ->join('parent_barang', 'parent_barang.id = kemasan.parent_type_id')
                ->where($condition)
                ->orderBy($sort, $sortType);

            if (
                $parent_type || $parent_name || $divisi_id || $warehouse_id || $search
            ) {
                $stockListQry->groupStart();
            }

            if ($parent_type) {
                $stockListQry->where('parent_barang.parent_type', $parent_type);
            }

            if ($parent_name) {
                $stockListQry->where('parent_barang.id', $parent_name);
            }

            if ($divisi_id) {
                $stockListQry->where('stock.divisi_id', $divisi_id);
            }

            if ($warehouse_id) {
                $stockListQry->where('stock.warehouse_id', $warehouse_id);
            }



            if ($search) {
                $stockListQry->where('kemasan.kode', $search);
                $stockListQry->orLike('kemasan.name', $search);
            }



            if (
                $parent_type || $parent_name || $divisi_id || $warehouse_id || $search
            ) {
                $stockListQry->groupEnd();
            }

            $getAllStockListData = $stockListQry->findAll();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle('A1:H1')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);


            if (empty($getAllStockListData)) {
                $sheet->setCellValue('A1', 'Tidak Ada Data Satuan');
            } else {
                $sheet->setCellValue('A1', 'NO');
                $sheet->setCellValue('B1', 'TIPE BARANG');
                $sheet->setCellValue('C1', 'KATEGORI');
                $sheet->setCellValue('D1', 'KODE');
                $sheet->setCellValue('E1', 'BARANG');
                $sheet->setCellValue('F1', 'DEPARTEMEN');
                $sheet->setCellValue('G1', 'WAREHOUSE');
                $sheet->setCellValue('H1', 'QTY SATUAN 1');


                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);



                $no = 1;
                $numRow = 2;

                foreach ($getAllStockListData as $row) :


                    $satuan1 = $this->satuanModel->find($row['satuan_id']);
                    $row['qty'] = $this->stockModel->detailStock($row['id'])['stok']['stokSekarang'];

                    if ($status_stok == "1") {
                        if ($row['qty'] > 0) {
                            $sheet->setCellValue('A' . $numRow, $no);
                            $sheet->setCellValue('B' . $numRow, strtoupper(str_replace("_", " ", strtoupper($row['parent_type']))));
                            $sheet->setCellValue('C' . $numRow, $row['parent_name']);
                            $sheet->setCellValue('D' . $numRow, $row['kode']);
                            $sheet->setCellValue('E' . $numRow, $row['name']);
                            $sheet->setCellValue('F' . $numRow, $row['divisi']);
                            $sheet->setCellValue('G' . $numRow, $row['warehouse']);
                            $sheet->setCellValue('H' . $numRow, $satuan1 == null ? '-' : ($row['qty']) . " " . $satuan1['kode_satuan']);

                            // Auto size columns A-H
                            $sheet->getColumnDimension('A')->setAutoSize(true);
                            $sheet->getColumnDimension('B')->setAutoSize(true);
                            $sheet->getColumnDimension('C')->setAutoSize(true);
                            $sheet->getColumnDimension('D')->setAutoSize(true);
                            $sheet->getColumnDimension('E')->setAutoSize(true);
                            $sheet->getColumnDimension('F')->setAutoSize(true);
                            $sheet->getColumnDimension('G')->setAutoSize(true);
                            $sheet->getColumnDimension('H')->setAutoSize(true);



                            $no++;
                            $numRow++;
                        }
                    } elseif ($status_stok == "0") {
                        if ($row['qty'] == 0) {
                            $sheet->setCellValue('A' . $numRow, $no);
                            $sheet->setCellValue('B' . $numRow, strtoupper(str_replace("_", " ", strtoupper($row['parent_type']))));
                            $sheet->setCellValue('C' . $numRow, $row['parent_name']);
                            $sheet->setCellValue('D' . $numRow, $row['kode']);
                            $sheet->setCellValue('E' . $numRow, $row['name']);
                            $sheet->setCellValue('F' . $numRow, $row['divisi']);
                            $sheet->setCellValue('G' . $numRow, $row['warehouse']);
                            $sheet->setCellValue('H' . $numRow, $satuan1 == null ? '-' : ($row['qty']) . " " . $satuan1['kode_satuan']);

                            // Auto size columns A-H
                            $sheet->getColumnDimension('A')->setAutoSize(true);
                            $sheet->getColumnDimension('B')->setAutoSize(true);
                            $sheet->getColumnDimension('C')->setAutoSize(true);
                            $sheet->getColumnDimension('D')->setAutoSize(true);
                            $sheet->getColumnDimension('E')->setAutoSize(true);
                            $sheet->getColumnDimension('F')->setAutoSize(true);
                            $sheet->getColumnDimension('G')->setAutoSize(true);
                            $sheet->getColumnDimension('H')->setAutoSize(true);



                            $no++;
                            $numRow++;
                        }
                    } else {

                        $sheet->setCellValue('A' . $numRow, $no);
                        $sheet->setCellValue('B' . $numRow, strtoupper(str_replace("_", " ", strtoupper($row['parent_type']))));
                        $sheet->setCellValue('C' . $numRow, $row['parent_name']);
                        $sheet->setCellValue('D' . $numRow, $row['kode']);
                        $sheet->setCellValue('E' . $numRow, $row['name']);
                        $sheet->setCellValue('F' . $numRow, $row['divisi']);
                        $sheet->setCellValue('G' . $numRow, $row['warehouse']);
                        $sheet->setCellValue('H' . $numRow, $satuan1 == null ? '-' : ($row['qty']) . " " . $satuan1['kode_satuan']);

                        // Auto size columns A-H
                        $sheet->getColumnDimension('A')->setAutoSize(true);
                        $sheet->getColumnDimension('B')->setAutoSize(true);
                        $sheet->getColumnDimension('C')->setAutoSize(true);
                        $sheet->getColumnDimension('D')->setAutoSize(true);
                        $sheet->getColumnDimension('E')->setAutoSize(true);
                        $sheet->getColumnDimension('F')->setAutoSize(true);
                        $sheet->getColumnDimension('G')->setAutoSize(true);
                        $sheet->getColumnDimension('H')->setAutoSize(true);



                        $no++;
                        $numRow++;
                    }

                endforeach;


                $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow())
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }








        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . strlen($excelOutput));

        echo $excelOutput;
        exit();
    }
}
