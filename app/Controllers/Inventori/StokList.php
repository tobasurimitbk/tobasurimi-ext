<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\AdjusmentModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC25Model;
use App\Models\BC27Model;
use App\Models\BC30Model;
use App\Models\BC41Model;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\MutasiGlobalModel;
use App\Models\MutasiModel;
use App\Models\ParentBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PPBKBModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampLogModel;
use App\Models\StockRevampModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
    protected $mutasiGlobalModel;
    protected $bc30Model;
    protected $bc25Model;
    protected $bc41Model;
    protected $stockRevampModel;
    protected $stockRevampDetailModel;
    protected $stockRevampLogModel;

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
        $this->mutasiGlobalModel = new MutasiGlobalModel();
        $this->bc30Model = new BC30Model();
        $this->bc25Model = new BC25Model();
        $this->bc41Model = new BC41Model();
        $this->stockRevampModel = new StockRevampModel();
        $this->stockRevampDetailModel = new StockRevampDetailModel();
        $this->stockRevampLogModel = new StockRevampLogModel();
    }

    public function index()
    {
        $tipeBarang = $this->metaDataModel->where('deletedAt', null)
            ->where('description !=', "kemasan")
            ->where('name', "Kategori Barang")
            ->findAll();
        $kategoriBarang = $this->parentBarangModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->where('parent_type', "bahan_baku")
            ->findAll();

        $data = [
            'kategoriBarang' => $kategoriBarang,
            'tipeBarang' => $tipeBarang,
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
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            'stock_revamp.deletedAt' => null,
            'stock_revamp.company_id' => $this->this_company_id,
            'barang_master.type_barang' => $this->request->getVar('parent_type')
        ];

        $dataQry = $this->stockRevampModel->getListStock(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $d) {
            array_push($dataResult, [
                'no' => $no++,
                'id' => encrypt($d['id']),
                'parent_name' => $d['parent_name'],
                'kode_barang' => $d['kode_barang'],
                'barang_name' => $d['barang_name'],
                'spesifikasi' => $d['spesifikasi'],
                'divisi' => $d['divisi'],
                'warehouse_name' => $d['warehouse_name'],
                'qty_diterima' => (float)$d['qty_diterima'],
                'kode_satuan' => $d['kode_satuan']
            ]);
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
        $tipeBarang = $this->metaDataModel->where('deletedAt', null)
            ->where('description !=', "kemasan")
            ->where('name', "Kategori Barang")
            ->findAll();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();

        $data = [
            'tipeBarang' => $tipeBarang,
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'satuan' => $dataSatuan
        ];

        return view('Warehouse/stock/stock_init', $data);
    }

    public function importView()
    {
        return view('Warehouse/stock/stock_import');
    }

    public function importPreview()
    {
        try {
            // ========================
            // 🔍 VALIDASI FILE
            // ========================
            $rules = [
                "file" => [
                    'rules' => 'uploaded[file]|ext_in[file,xlsx,xls]',
                    'errors' => [
                        'uploaded' => 'Tidak ada file yang di-upload.',
                        'ext_in'   => 'File yang di-upload harus berupa file Excel (.xlsx atau .xls).',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                return $this->response->setJSON([
                    "status"  => false,
                    "message" => $errorList[array_key_first($errorList)],
                    'token'   => csrf_hash()
                ]);
            }

            // ========================
            // 📂 BACA FILE EXCEL
            // ========================
            $file = $this->request->getFile('file');
            if (!$file->isValid()) {
                throw new \RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $worksheet   = $spreadsheet->getActiveSheet();

            $data = [];
            foreach ($worksheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = trim((string) $cell->getValue());
                }
                $data[] = $rowData;
            }

            // ========================
            // 🔍 VALIDASI DATA
            // ========================
            $dataResult  = [];
            $dataPreview = [];

            foreach ($data as $index => $val) {
                $status  = true;
                $message = null;

                $no         = trim($val[0] ?? '');
                $kodeBarang = trim($val[1] ?? '');
                $barangName = trim($val[2] ?? '');
                $spesifikasi = trim($val[3] ?? '');
                $divisi     = trim($val[4] ?? '');
                $warehouse  = trim($val[5] ?? '');
                $qty        = (float) ($val[6] ?? 0);

                // =========================
                // 🔸 Validasi master barang
                // =========================
                $barangMasterFirst = $this->barangMasterModel
                    ->where('company_id', $this->this_company_id)
                    ->where('kode_barang', $kodeBarang)
                    ->where('barang_name', $barangName)
                    ->where('deletedAt', null)
                    ->first();

                $barangMasterSpesifikasi = null;
                if ($barangMasterFirst) {
                    $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel
                        ->select('barang_master_spesifikasi.*, satuans.kode_satuan')
                        ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                        ->where('barang_master_id', $barangMasterFirst['id'])
                        ->where('TRIM(spesifikasi)', $spesifikasi)
                        ->where('barang_master_spesifikasi.deletedAt', null)
                        ->first();

                    if (!$barangMasterSpesifikasi) {
                        $status = false;
                        $message = "Spesifikasi tidak ditemukan di master barang.";
                    }
                } else {
                    $status = false;
                    $message = "Kode barang tidak ditemukan di master barang.";
                }

                // =========================
                // 🔸 Validasi Divisi dan Warehouse
                // =========================
                $divisiFirst = $this->divisiModel
                    ->where('divisi', $divisi)
                    ->where('deletedAt', null)
                    ->first();

                $warehouseFirst = null;
                if ($divisiFirst) {
                    $warehouseFirst = $this->warehouseModel
                        ->where('divisi_id', $divisiFirst['id'])
                        ->where('warehouse_name', $warehouse)
                        ->where('deletedAt', null)
                        ->first();

                    if (!$warehouseFirst) {
                        $status = false;
                        $message = "Warehouse tidak ditemukan di master data.";
                    }
                } else {
                    $status = false;
                    $message = "Departemen tidak ditemukan di master data.";
                }

                // =========================
                // 🔸 Validasi stok inisiasi
                // =========================
                if ($status && $barangMasterSpesifikasi && $divisiFirst && $warehouseFirst) {
                    $validasiStokInisiasi = $this->stockRevampModel->getBarangBelumDiinisiasi(
                        $this->this_company_id,
                        $divisiFirst['id'],
                        $warehouseFirst['id'],
                        $barangMasterSpesifikasi['id'],
                        $barangMasterSpesifikasi['satuan_1']
                    );

                    if ($validasiStokInisiasi != null) {
                        $status = false;
                        $message = "Stok sudah pernah diinisiasi.";
                    }
                }

                // =========================
                // 💾 Simpan ke array result
                // =========================
                $dataResult[] = [
                    'company_id' => $this->this_company_id,
                    'barang_master_id' => $barangMasterSpesifikasi['barang_master_id'] ?? null,
                    'spesifikasi_id'   => $barangMasterSpesifikasi['id'] ?? null,
                    'unit_id'          => $barangMasterSpesifikasi['satuan_1'] ?? null,
                    'divisi_id'        => $divisiFirst['id'] ?? null,
                    'warehouse_id'     => $warehouseFirst['id'] ?? null,
                    'qty_bersih'       => $qty,
                    'qty_diterima'     => $qty,
                    'bc_id'            => 0,
                    'type_bc'          => 'NON PABEAN',
                    'reference_id'     => null,
                    'po_type'          => null,
                    'po_id'            => null,
                    'reference_type'   => 'INISIASI',
                    'status'           => 'IN',
                    'keterangan'       => 'INISIASI',
                ];

                $dataPreview[] = [
                    'no'               => $no,
                    'type_barang'   => strtoupper(str_replace(['_'], ' ', $barangMasterFirst['type_barang'] ?? '')) ?? null,
                    'divisi'        => $divisiFirst['divisi'] ?? null,
                    'warehouse_name' => $warehouseFirst['warehouse_name'] ?? null,
                    'kode_barang'   => $barangMasterFirst['kode_barang'] ?? null,
                    'barang_name'   => $barangMasterFirst['barang_name'] ?? null,
                    'spesifikasi'   => $barangMasterSpesifikasi['spesifikasi'] ?? null,
                    'qty'           => $qty,
                    'kode_satuan'   => $barangMasterSpesifikasi['kode_satuan'] ?? null,
                    'status'        => $status,
                    'message'       => $message,
                ];
            }

            // =========================
            // ✅ RETURN RESPONSE
            // =========================
            return $this->response->setJSON([
                'data' => [
                    'dataResult'  => $dataResult,
                    'dataPreview' => $dataPreview
                ],
                'status' => true,
                'token'  => csrf_hash()
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'token'   => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function importInitStok()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            foreach (json_decode($_POST['list_stock']) as $l) {
                $data = (array)$l;
                $this->stockRevampModel->insertStockRevamp(
                    $db,
                    $data
                );
            }
            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Stok berhasil di inisiasi",
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return  response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }


    public function getListBarangNotInit()
    {
        try {
            $type_barang = $this->request->getVar('type_barang');
            $divisi_id = $this->request->getVar('divisi_id');
            $warehouse_id = $this->request->getVar('warehouse_id');
            $search = $this->request->getVar('q');

            if (empty($type_barang) || empty($divisi_id) || empty($warehouse_id)) {
                return response()->setJSON(['data' => []]);
            }

            $data = $this->stockRevampModel->getBarangBelumInisiasi(
                $type_barang,
                $divisi_id,
                $warehouse_id,
                $this->this_company_id,
                $search
            );
            $dataList = array();
            foreach ($data as $d) {
                array_push($dataList, [
                    'id' => $d['id'],
                    'text' => "(" . $d['kode_barang'] . ") " . trim(
                        str_replace(
                            ["\"", "\t"],
                            "'",
                            $d['barang_name'] . '- ' . $d['spesifikasi']
                        )
                    ),
                    // helper
                    'satuan_1' => $d['satuan_1'],
                    'kode_barang' => $d['kode_barang'],
                    'barang_name' => trim(
                        str_replace(
                            ["\"", "\t"],
                            "'",
                            $d['barang_name']
                        )
                    ),
                    'spesifikasi' => trim(
                        str_replace(
                            ["\"", "\t"],
                            "'",
                            $d['spesifikasi']
                        )
                    )
                ]);
            }

            return response()->setJSON([
                'results' => $dataList,
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return  response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function createInitStok()
    {
        // return response()->setJSON([
        //     '$_DATA' => json_decode($_POST['list_stock'])
        // ]);
        $db = \Config\Database::connect();
        $db->transBegin();
        try {

            foreach (json_decode($_POST['list_stock']) as $l) {
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->where('id', $l->spesifikasi_id)->first();
                $data = [
                    'company_id'        => $this->this_company_id,
                    'barang_master_id'  => $barangMasterSpesifikasi['barang_master_id'],
                    'spesifikasi_id'    => $l->spesifikasi_id,
                    'unit_id'           => $l->satuan_id,
                    'divisi_id'         => $l->divisi_id,
                    'warehouse_id'      => $l->warehouse_id,
                    'qty_bersih'        => $l->qty_inisiasi,
                    'qty_diterima'      => $l->qty_inisiasi,
                    'bc_id'             => 0,
                    'type_bc'           => 'NON PABEAN',
                    'reference_id'      => null,
                    'po_type'           => null,
                    'po_id'             => null,
                    'reference_type'    => 'INISIASI',
                    'status'            => 'IN',
                    'keterangan'        => 'INISIASI'
                ];

                $this->stockRevampModel->insertStockRevamp(
                    $db,
                    $data
                );
            }
            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Stok berhasil di inisiasi",
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return  response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    // public function import()
    // {

    //     $db = \Config\Database::connect();
    //     $db->transBegin();

    //     try {
    //         $rules = [
    //             "file" => [
    //                 'rules' => 'uploaded[file]|ext_in[file,xlsx]',
    //                 'errors' => [
    //                     'uploaded' => 'Tidak ada file yang di-upload.',
    //                     'ext_in' => 'File yang di-upload harus berupa file Excel (.xlsx).',
    //                 ],

    //             ],
    //         ];

    //         if ($this->validate($rules)) {

    //             $file = $this->request->getFile('file');

    //             $spreadsheet = IOFactory::load($file);
    //             $worksheet = $spreadsheet->getActiveSheet();

    //             $data = [];
    //             $rowIterator = $worksheet->getRowIterator(2);
    //             foreach ($rowIterator as $row) {
    //                 $cellIterator = $row->getCellIterator();
    //                 $rowData = [];
    //                 foreach ($cellIterator as $cell) {
    //                     $rowData[] = $cell->getValue();
    //                 }
    //                 $data[] = $rowData;
    //             }

    //             $gagalArr = [];
    //             $berhasilTotal = 0;

    //             // INSERT INVENTORI
    //             for ($i = 0; $i < count($data); $i++) {

    //                 $type_barang = str_replace(' ', '_', strtolower(trim($data[$i][0])));
    //                 $type_barang = str_replace('barang', 'bahan', $type_barang);

    //                 // VALIDASI TIPE BARANG
    //                 $parentBarang = $this->parentBarangModel
    //                     ->where('parent_type', trim($type_barang))
    //                     ->where('parent_name', trim($data[$i][1]))
    //                     ->where('company_id', $this->this_company_id)
    //                     ->where('deletedAt', null)
    //                     ->first();

    //                 // VALIDASI DEPARTEMEN
    //                 $divisi = $this->divisiModel
    //                     ->where('company_id', $this->this_company_id)
    //                     ->where(
    //                         'divisi',
    //                         trim($data[$i][4])
    //                     )->first();

    //                 // CARI SUPPLIER BY NAME
    //                 $supplier = $this->supplierModel->where('company_id', $this->this_company_id)
    //                     ->where('UPPER(name)', trim($data[$i][10]))
    //                     ->first();

    //                 if ($parentBarang != null && $divisi != null) {
    //                     // VALIDASI WAREHOUSE
    //                     $warehouse = $this->warehouseModel->where('divisi_id', $divisi['id'])->where('code_warehouse', trim($data[$i][5]))->first();
    //                     // VALIDASI JENIS DOK AJU (PABEAN)
    //                     if (trim($data[$i][6]) == "NON PABEAN") {
    //                         $bc_id = 0;
    //                         $no_aju = "-";
    //                     } else {
    //                         $bc = $this->metaDataModel->where('name', 'jenis_dok_aju')->where('value', trim($data[$i][6]))->first();
    //                         if ($bc != null) {
    //                             $bc_id = $bc['id'];
    //                             $no_aju = $data[$i][7];
    //                         } else {
    //                             $bc_id = null;
    //                             $no_aju = null;
    //                         }
    //                     }


    //                     if ($type_barang != "kemasan") {
    //                         // INI BARANG
    //                         // VALIDASI BARANG
    //                         $barangMaster = $this->barangMasterModel
    //                             ->where('kode_barang', trim($data[$i][2]))
    //                             ->where('company_id', $this->this_company_id)
    //                             ->first();

    //                         if ($barangMaster != null) {
    //                             // SPESIFIKASI
    //                             $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel
    //                                 ->where('barang_master_id', $barangMaster['id'])
    //                                 ->where('spesifikasi', trim($data[$i][3]))
    //                                 ->first();

    //                             if ($barangMasterSpesifikasi != null) {
    //                                 $spesifikasi_id = $barangMasterSpesifikasi['id'];
    //                             } else {
    //                                 // GAGAL
    //                                 $spesifikasi_id = null;
    //                             }
    //                             $barang_id = $barangMaster['id'];
    //                         } else {
    //                             // GAGAL
    //                             $barang_id = null;
    //                             $spesifikasi_id = null;
    //                         }
    //                     } else {
    //                         // INI KEMASAN
    //                         $barang_id = 0;
    //                         // CARI KEMASAN
    //                         $kemasan = $this->kemasanModel
    //                             ->where('company_id', $this->this_company_id)
    //                             ->where('kode', trim($data[$i][2]))
    //                             ->first();

    //                         if ($kemasan != null) {
    //                             $spesifikasi_id = $kemasan['id'];
    //                         } else {
    //                             $spesifikasi_id = null;
    //                         }
    //                     }

    //                     // START INISIASI
    //                     // VARIABEL
    //                     $warehouse_id = ($warehouse == null) ? null : $warehouse['id'];
    //                     $divisi_id = ($divisi == null) ? null : $divisi['id'];
    //                     $type_barang = $type_barang;
    //                     $barang_id = $barang_id;
    //                     $spesifikasi_id = $spesifikasi_id;
    //                     $bc_id = $bc_id;
    //                     $qty = $data[$i][8];
    //                     $supplier_id = $supplier == null ? null : $supplier['id'];
    //                     if (!empty($data[$i][9])) {
    //                         // Cek apakah nilai adalah angka (format serial Excel)
    //                         if (is_numeric($data[$i][9])) {
    //                             $stock_date = Date::excelToDateTimeObject($data[$i][9])->format('Y-m-d');
    //                         } else {
    //                             $stock_date = $data[$i][9]; // Jika sudah dalam format string
    //                         }
    //                     } else {
    //                         $stock_date = date('Y-m-d'); // Jika kosong, gunakan tanggal sekarang
    //                     }

    //                     $is_init = true;

    //                     if ($divisi_id != null && $warehouse_id != null && $barang_id !== null && $spesifikasi_id != null && $bc_id !== null && $no_aju != null) {
    //                         // CHECK STOK
    //                         if (
    //                             $this->stockModel->isDefinedStockMaster(
    //                                 $this->this_company_id,
    //                                 $warehouse_id,
    //                                 $divisi_id,
    //                                 $type_barang,
    //                                 $barang_id,
    //                                 $spesifikasi_id
    //                             )
    //                         ) {

    //                             // ADA STOK MASTER
    //                             $stokMaster = $this->stockModel->getStokMaster(
    //                                 $this->this_company_id,
    //                                 $warehouse_id,
    //                                 $divisi_id,
    //                                 $type_barang,
    //                                 $barang_id,
    //                                 $spesifikasi_id
    //                             );

    //                             $stokSubDetail = $this->stockModel->isDefinedStockSubDetail(
    //                                 $this->this_company_id,
    //                                 $warehouse_id,
    //                                 $divisi_id,
    //                                 $type_barang,
    //                                 $barang_id,
    //                                 $spesifikasi_id,
    //                                 $bc_id,
    //                                 $no_aju,
    //                                 $stokMaster['id']
    //                             );

    //                             if ($stokSubDetail != null) {
    //                                 // GAGAL KARENA SUDAH INISIASI
    //                                 $is_init = false;
    //                             } else {
    //                                 // 
    //                                 $is_init = true;
    //                             }
    //                         }

    //                         if ($is_init) {
    //                             $stok = $this->stockModel->insertStok(
    //                                 $this->this_company_id,
    //                                 $warehouse_id,
    //                                 $divisi_id,
    //                                 $type_barang,
    //                                 $barang_id,
    //                                 $spesifikasi_id,
    //                                 $qty
    //                             );

    //                             $stokDetail = $this->stockDetailModel->insertStokDetail(
    //                                 $stok,
    //                                 $qty,
    //                                 "In",
    //                                 $stock_date,
    //                                 $this->this_user_id,
    //                                 "INISIASI",
    //                                 "-",
    //                                 "-"
    //                             );

    //                             $this->stockDetail2Model->insertStokDetail2(
    //                                 $bc_id,
    //                                 $stok,
    //                                 $stokDetail,
    //                                 $qty,
    //                                 $no_aju,
    //                                 '-',
    //                                 '-',
    //                                 $supplier_id
    //                             );
    //                             // BERHASIL
    //                             $berhasilTotal++;
    //                         } else {
    //                             // GAGAL
    //                             array_push($gagalArr, $data[$i]);
    //                         }
    //                     } else {
    //                         array_push($gagalArr, $data[$i]);
    //                     }
    //                 } else {
    //                     array_push($gagalArr, $data[$i]);
    //                 }
    //             }

    //             $db->transCommit();
    //             $gagalTotal = count($gagalArr);

    //             return response()->setJSON([
    //                 'message' => "Berhasil Import : $berhasilTotal Data, Gagal Import : $gagalTotal",
    //                 'status' => true,
    //                 'gagal' => $gagalArr,
    //                 'token' => csrf_hash()
    //             ]);
    //         } else {
    //             $db->transRollback();
    //             $errorList = $this->validator->getErrors();
    //             $data = [
    //                 "status"    => false,
    //                 "message"   => $errorList[array_keys($errorList)[0]],
    //                 'token'     => csrf_hash()
    //             ];
    //             return response()->setJSON($data);
    //         }
    //     } catch (Exception $e) {
    //         $db->transRollback();
    //         $data = [
    //             "status"    => false,
    //             "message"   => "Terjadi Kesalahan",
    //             'token'     => csrf_hash()
    //         ];
    //         return response()->setJSON($data);
    //     }
    // }

    public function detail($id)
    {
        $id = decrypt($id);
        $stock =  $this->stockRevampModel->getStockIdentity($id);

        if ($stock == null) {
            return redirect()->to('stock-list');
        }

        $sumberBarang = $this->metaDataModel->where('name', "sumber_barang")->first();
        $sumberBarangArr = explode(',', $sumberBarang['value']);

        $data = [
            'stock' => $stock,
            'sumberBarang' => $sumberBarangArr
        ];

        return view('Warehouse/stock/stock_list_detail', $data);
    }

    public function allStockDetail()
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
            "search" => $this->request->getVar('search'),
            "sumber_barang" => $this->request->getVar('sumber_barang')
        ];

        $id = decrypt($this->request->getVar('id'));

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        if ($addCondition['sumber_barang'] == "PO LOKAL BAKU") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "LPB",
                "stock_revamp_detail.po_type" => "LOKAL BAKU"
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByPoLokalBb($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "PO LOKAL PENOLONG") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "LPB",
                "stock_revamp_detail.po_type" => "LOKAL PENOLONG"
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByPoBp($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "PO IMPORT BAKU") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "LPB",
                "stock_revamp_detail.po_type" => "IMPORT BAKU"
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByPoImportBb($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "PO IMPORT PENOLONG") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "LPB",
                "stock_revamp_detail.po_type" => "IMPORT PENOLONG"
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByPoBp($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "PROSES REBUS") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "PROSES REBUS",
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByProsesRebus($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "JASA VENDOR") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "JASA VENDOR",
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByJasaVendor($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "HASIL PRODUKSI") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "HASIL PRODUKSI",
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByHasilProduksi($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "MATERIAL REQUEST BAKU") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "MATERIAL REQUEST BAKU",
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByMaterialRequestBaku($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "MATERIAL REQUEST PENOLONG") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "MATERIAL REQUEST PENOLONG",
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByMaterialRequestPenolong($condition, $addCondition, $limit, $offset);
        } elseif ($addCondition['sumber_barang'] == "INISIASI") {
            $condition = [
                "stock_revamp_detail.stock_id" => $id,
                "stock_revamp_detail.deletedAt" => null,
                "stock_revamp.deletedAt" => null,
                "stock_revamp_detail.reference_type" => "INISIASI",
            ];
            $dataQry = $this->stockRevampDetailModel->getListStockDetailByInisiasi($condition, $addCondition, $limit, $offset);
        }


        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            array_push($dataResult, [
                'id' => $data['id'],
                'no' => $no++,
                'supplier_name' => $data['supplier_name'] ?? "",
                'kode_barang' => $data['kode_barang'],
                'barang_name' => $data['barang_name'],
                'spesifikasi' => $data['spesifikasi'],
                'type_bc' => $data['type_bc'],
                'po_no' => $data['po_no'] ?? "",
                'ref_no' => $data['ref_no'] ?? "",
                'po_date' => empty($data['po_date']) ? "" : date('d/m/Y', strtotime($data['po_date'])),
                'no_daftar' =>  $data['no_daftar'] ?? "",
                'no_aju' => $data['no_aju'] ?? "",
                'qty_diterima' => $data['qty_diterima'],
                'kode_satuan' => $data['kode_satuan']
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

    public function allStokPerSupplier()
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
            "supplier_id" => $this->request->getVar("supplier_id"),
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

        $dataQry = $this->stockDetail2Model->getListStokPerSupplier($condition, $addCondition, $limit, $offset);
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
            $bcType = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];

            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($stok['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stok['barang2_id']);

                array_push($dataResult, [
                    "no" => $no++,
                    "supplier_name" => $data->supplier_name,
                    "sumber" => $bcType . " / " . $data->no_aju . " / " . $data->sumber,
                    "divisi" => $data->divisi,
                    "warehouse" => $data->warehouse_name,
                    "tanggal_penerimaan" => date('d/m/Y', strtotime($data->stock_date)),
                    "nomor" => $data->stock_dokumen,
                    "kode_barang" => $barangMaster['kode_barang'],
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "no_aju" => $data->no_aju,
                    "stok_1" => floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                ]);
            } else {
                // KEMASAN
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
                $kemasan = $this->kemasanModel->find($data->kemasan_id);

                array_push($dataResult, [
                    "no" => $no++,
                    "supplier_name" => $data->supplier_name,
                    "sumber" => $bcType . " / " . $data->noAju . " / " . $data->sumber,
                    "divisi" => $data->divisi,
                    "warehouse" => $data->warehouse_name,
                    "tanggal_penerimaan" => date('d/m/Y', strtotime($data->stock_date)),
                    "nomor" => $data->stock_dokumen,
                    "no_aju" => $data->no_aju,
                    "kode_barang" => $kemasan['kode'],
                    "stok_1" => floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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
                    "stok_1" => floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
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
                    "stok_1" => floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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
            "stock_details.status" => "In"
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
            $supplier = $lpb == null ? null : $this->supplierModel->find($lpb['supplier_id']);
            $dokumenBC = $this->metaDataModel->find($data->bc_id);
            $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];

            if ($stok['kemasan_id'] == 0) {
                // BARANG
                $satuan_2 = $this->satuanModel->find($barang['satuan_2']);
                $satuan_3 = $this->satuanModel->find($barang['satuan_3']);
                $barangMaster = $this->barangMasterModel->find($data->barang1_id);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($data->barang2_id);
                $harga = $lpb != null ? $this->penerimaanBarangDetailModel->getHargaTotalPenerimaan($lpb['id']) : 0;
                $satuan_1 = $this->satuanModel->find($barang['satuan_1']);

                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "po" => $data->no_dokumen2,
                    "dokumen" => $data->no_dokumen1,
                    "dokumen_pabean" => $bcName . " / " . $data->no_aju,
                    "supplier" => $supplier == null ? "-" : strtoupper($supplier['name']),
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "stok_1" => floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                    "harga" => $harga == null ? 0 : number_format($harga[0]['harga'])
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
                    "stok_1" => floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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
                        "stok_1" =>  $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                        "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                        "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
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
                                "stok_1" =>  $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                                "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                                "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
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
                        "stok_1" => $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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
                                "stok_1" => $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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

                $mutasiGlobal =  $this->mutasiGlobalModel
                    ->select('bc_id, no_aju')
                    ->join('mutasi_global_detail', 'mutasi_global.id = mutasi_global_detail.mutasi_global_id', 'left')
                    ->where('mutasi_global.no_mutasi', $data->no_dokumen2)
                    ->first();

                if ($mutasi != null) {
                    $dokumenBC = $this->metaDataModel->find($mutasi['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    // MASUK 
                    $dokumenAsal =  $bcName . " / " . $mutasi['no_aju'];
                } elseif ($mutasiGlobal != null) {
                    $dokumenBC = $this->metaDataModel->find($mutasiGlobal['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    // MASUK 
                    $dokumenAsal =  $bcName . " / " . $mutasiGlobal['no_aju'];
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
                    "stok_1" =>  $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
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
                    "stok_1" => $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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
                    "stok_1" =>  $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
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
                    "stok_1" => $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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
                    "stok_1" =>  $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
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
                    "stok_1" => $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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
                    "stok_1" =>  $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
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
                    "stok_1" => $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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

    public function allStokPenjualan()
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
            "search" => $this->request->getVar("search"),
            "bc_id" => "",
            "divisi_id" => "",
            "warehouse_id" => "",
            "where_in_sumber" => explode(",", $this->request->getVar('sumber')),
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

            // NO AJU REFERENSI
            $data->no_aju_referensi = $data->no_aju;
            // TEMPELKAN SAJA NO BC 3.0 JIKA PENJUALAN DAN RETUR
            if ($data->sumber == "PENJUALAN" || $data->sumber == "RETUR") {
                $bc30Internasional = $this->bc30Model
                    ->select('bc_30.no_aju, bc_30.tipe_sales_order, sales_order_export.bc_type, customers.name AS customer_name')
                    ->join('sales_order_export', 'sales_order_export.sales_order_export_id = bc_30.sales_order_id', 'left')
                    ->join('stuffing_internasional', 'stuffing_internasional.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
                    ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
                    ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
                    ->where('stuffing_internasional.no_stuffing', $data->no_dokumen2)
                    ->first();

                $bc30PengembalianBarang = $this->bc30Model
                    ->select('bc_30.no_aju, pengembalian_barang.bc_pengeluaran_id as bc_type, suppliers.name as customer_name')
                    ->join('pengembalian_barang', 'pengembalian_barang.id = bc_30.pengembalian_barang_id', 'left')
                    ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
                    ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                    ->where('pengembalian_barang.no_surat_jalan', $data->no_dokumen2)
                    ->where('bc_30.company_id', $this->this_company_id)
                    ->first();

                $bc30SalesOrderLain = $this->bc30Model
                    ->select('bc_30.no_aju, sales_order_lain.bc_id AS bc_type, customers.name AS customer_name')
                    ->join('sales_order_lain', 'sales_order_lain.id = bc_30.sales_order_lain_id', 'left')
                    ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
                    ->where('sales_order_lain.no_sales_order', $data->no_dokumen2)
                    ->where('bc_30.company_id', $this->this_company_id)
                    ->first();

                $bc25SalesOrderLain = $this->bc25Model
                    ->select('bc_25.no_aju, sales_order_lain.bc_id as bc_type, customers.name AS customer_name')
                    ->join('sales_order_lain', 'sales_order_lain.id = bc_25.sales_order_lain_id', 'left')
                    ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
                    ->where('sales_order_lain.no_sales_order', $data->no_dokumen2)
                    ->where('bc_25.company_id', $this->this_company_id)
                    ->first();

                $bc25SalesOrderLokal = $this->bc25Model
                    ->select('bc_25.no_aju,bc_25.tipe_sales_order, sales_order.bc_type, customers.name AS customer_name')
                    ->join('sales_order', 'sales_order.id = bc_25.sales_order_id', 'left')
                    ->join('stuffing_lokal', 'stuffing_lokal.sales_order_id = sales_order.id', 'left')
                    ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                    ->where('bc_25.company_id', $this->this_company_id)
                    ->where('stuffing_lokal.no_stuffing', $data->no_dokumen2)
                    ->first();

                $bc25PengembalianBarang = $this->bc25Model
                    ->select('bc_25.no_aju, bc_25.tipe_sales_order, pengembalian_barang.bc_pengeluaran_id as bc_type, suppliers.name as customer_name')
                    ->join('pengembalian_barang', 'pengembalian_barang.id = bc_25.pengembalian_barang_id', 'left')
                    ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
                    ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id')
                    ->where('pengembalian_barang.no_surat_jalan', $data->no_dokumen2)
                    ->where('bc_25.company_id', $this->this_company_id)
                    ->first();

                $bc41SalesOrderLain = $this->bc41Model
                    ->select('bc_41.no_aju, sales_order_lain.bc_id, customers.name AS customer_name')
                    ->join('sales_order_lain', 'sales_order_lain.id = bc_41.sales_order_lain_id', 'left')
                    ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
                    ->where('sales_order_lain.no_sales_order', $data->no_dokumen2)
                    ->where('bc_41.company_id', $this->this_company_id)
                    ->first();

                $bc41SalesOrderLokal = $this->bc41Model
                    ->select('bc_41.no_aju,bc_41.tipe_sales_order, sales_order.bc_type, customers.name AS customer_name')
                    ->join('sales_order', 'sales_order.id = bc_41.sales_order_id', 'left')
                    ->join('stuffing_lokal', 'stuffing_lokal.sales_order_id = sales_order.id', 'left')
                    ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                    ->where('bc_41.company_id', $this->this_company_id)
                    ->where('stuffing_lokal.no_stuffing', $data->no_dokumen2)
                    ->first();

                $bc41PengembalianBarang = $this->bc41Model
                    ->select('bc_41.no_aju, bc_41.tipe_sales_order, pengembalian_barang.bc_pengeluaran_id as bc_type, suppliers.name as customer_name')
                    ->join('pengembalian_barang', 'pengembalian_barang.id = bc_41.pengembalian_barang_id', 'left')
                    ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
                    ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id')
                    ->where('pengembalian_barang.no_surat_jalan', $data->no_dokumen2)
                    ->where('bc_41.company_id', $this->this_company_id)
                    ->first();


                if ($bc30Internasional != null) {
                    // STUFFING INTERNASIONAL BC 3.0 SUDAH DIBUAT
                    $dokumenBC = $this->metaDataModel->find($bc30Internasional['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc30Internasional['no_aju'];
                    $data->customer_name = $bc30Internasional['customer_name'];
                    $data->tipe_sales_order = $bc30Internasional['tipe_sales_order'];
                } elseif ($bc30PengembalianBarang != null) {
                    // RETUR PEMBELIAN BC 3.0
                    $dokumenBC = $this->metaDataModel->find($bc30PengembalianBarang['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc30PengembalianBarang['no_aju'];
                    $data->customer_name = $bc30PengembalianBarang['customer_name'];
                    $data->tipe_sales_order = "RETUR PEMBELIAN";
                    $data->no_dokumen2 = "-"; // STUFFING NO GA ADA
                } elseif ($bc30SalesOrderLain != null) {
                    // SALES ORDER LAIN BC 3.0
                    $dokumenBC = $this->metaDataModel->find($bc30SalesOrderLain['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc30SalesOrderLain['no_aju'];
                    $data->customer_name = $bc30SalesOrderLain['customer_name'];
                    $data->tipe_sales_order = "PENJUALAN LAIN";
                    $data->no_dokumen2 = "-"; // STUFFING NO GA ADA
                } elseif ($bc25SalesOrderLain != null) {
                    // BEA CUKAI 2.5 SALES ORDER LAIN
                    $dokumenBC = $this->metaDataModel->find($bc25SalesOrderLain['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc25SalesOrderLain['no_aju'];
                    $data->customer_name = $bc25SalesOrderLain['customer_name'];
                    $data->tipe_sales_order = $bc25SalesOrderLain['tipe_sales_order'];
                    $data->no_dokumen2 = "-"; // STUFFING NO GA ADA
                } elseif ($bc25SalesOrderLokal != null) {
                    // BEA CUKAI 2.5 SALES ORDER LOKAL
                    $dokumenBC = $this->metaDataModel->find($bc25SalesOrderLokal['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc25SalesOrderLokal['no_aju'];
                    $data->customer_name = $bc25SalesOrderLokal['customer_name'];
                    $data->tipe_sales_order = $bc25SalesOrderLokal['tipe_sales_order'];
                } elseif ($bc25PengembalianBarang != null) {
                    // BEA CUKAI 2.5 RETUR
                    $dokumenBC = $this->metaDataModel->find($bc25PengembalianBarang['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc25PengembalianBarang['no_aju'];
                    $data->customer_name = $bc25PengembalianBarang['customer_name'];
                    $data->tipe_sales_order = $bc25PengembalianBarang['tipe_sales_order'];
                } elseif ($bc41SalesOrderLain != null) {
                    // BEA CUKAI 4.1 SALES ORDER LAIN
                    $dokumenBC = $this->metaDataModel->find($bc41SalesOrderLain['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc41SalesOrderLain['no_aju'];
                    $data->customer_name = $bc41SalesOrderLain['customer_name'];
                    $data->tipe_sales_order = "PENJUALAN LAIN";
                    $data->no_dokumen2 = "-"; // STUFFING NO GA ADA
                } elseif ($bc41SalesOrderLokal != null) {
                    // BEA CUKAI 4.1 SALES ORDER LOKAL
                    $dokumenBC = $this->metaDataModel->find($bc41SalesOrderLokal['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc41SalesOrderLokal['no_aju'];
                    $data->customer_name = $bc41SalesOrderLokal['customer_name'];
                    $data->tipe_sales_order = $bc41SalesOrderLokal['tipe_sales_order'];
                } elseif ($bc41PengembalianBarang != null) {
                    // BEA CUKAI 4.1 RETUR
                    $dokumenBC = $this->metaDataModel->find($bc41PengembalianBarang['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc41PengembalianBarang['no_aju'];
                    $data->customer_name = $bc41PengembalianBarang['customer_name'];
                    $data->tipe_sales_order = $bc41PengembalianBarang['tipe_sales_order'];
                } else {
                    // BELUM DIBUAT SAMA SEKALI DOKUMEN BC 3.O NYA
                    $dokumenBC = $this->metaDataModel->find($data->bc_id);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->customer_name = "-";
                    $data->tipe_sales_order = "-";
                    $data->no_dokumen2 = "-"; // STUFFING NO GA ADA
                }
            } else {
                $dokumenBC = $this->metaDataModel->find($data->bc_id);
                $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                $data->customer_name = "-";
                $data->tipe_sales_order = "-";
                $data->no_dokumen2 = "-"; // STUFFING NO GA ADA
            }

            // REFERENSI
            $dokumenBCReferensi = $this->metaDataModel->find($data->bc_id);
            $bcNameReferensi = $dokumenBCReferensi == null ? "NON PABEAN" : $dokumenBCReferensi['value'];

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
                    "dokumen_referensi" => $bcNameReferensi . " / " . $data->no_aju_referensi,
                    "supplier_name" => $data->supplier_name,
                    "no_dokumen1" => $data->no_dokumen1,
                    "no_dokumen2" => $data->no_dokumen2,
                    "stock_dokumen" => $data->stock_dokumen,
                    "no_po" => $data->no_po,
                    "barang" => strtoupper($barangMaster['barang_name'] . " - " . $barangMasterSpesifikasi['spesifikasi']),
                    "customer_name" => $data->customer_name,
                    "tipe_sales_order" => $data->tipe_sales_order,
                    "stok_1" =>  $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
                    "stok_2" => $satuan_2 == null ? "-" : $in_out . (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_2'])) . " " . $satuan_2['kode_satuan'],
                    "stok_3" => $satuan_3 == null ? "-" : $in_out .  (sprintf("%.2f", floatval($data->stok_total) / $barang['konversi_satuan_3'])) . " " . $satuan_3['kode_satuan'],
                ]);
            } else {
                $satuan_1 = $this->satuanModel->find($barang['satuan_id']);
                array_push($dataResult, [
                    "no" => $no++,
                    "tanggal" => date('d/m/Y', strtotime($data->stock_date)),
                    "dokumen" => $bcName . " / " . $data->no_aju,
                    "dokumen_referensi" => $bcNameReferensi . " / " . $data->no_aju_referensi,
                    "supplier_name" => $data->supplier_name,
                    "no_dokumen1" => $data->no_dokumen1,
                    "no_dokumen2" => $data->no_dokumen2,
                    "stock_dokumen" => $data->stock_dokumen,
                    "no_po" => "-",
                    "barang" => strtoupper($barang['name']),
                    "customer_name" => $data->customer_name,
                    "tipe_sales_order" => $data->tipe_sales_order,
                    "stok_1" => $in_out . " " . floatval($data->stok_total) . " " . $satuan_1['kode_satuan'],
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
        $parentType = $this->request->getVar('parent_type');
        $parentName = $this->request->getVar('parent_name');
        $divisiId = $this->request->getVar('divisi_id');
        $warehouseId = $this->request->getVar('warehouse_id');
        $warehouseId = $warehouseId == "null" ? "" : $warehouseId;

        $limit = 100000000;
        $offset = 0;

        $condition = [
            'stock_revamp.deletedAt' => null,
            'stock_revamp.company_id' => $this->this_company_id,
            'barang_master.type_barang' => $parentType,
        ];

        $addCondition = [
            "search" => "",
            "parent_type_id" => $parentName,
            "divisi_id" => $divisiId,
            "warehouse_id" => $warehouseId,
        ];

        $dataQry = $this->stockRevampModel->getListStock(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kategori Barang');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Barang');
        $sheet->setCellValue('E1', 'Spesifikasi');
        $sheet->setCellValue('F1', 'Divisi');
        $sheet->setCellValue('G1', 'Warehouse');
        $sheet->setCellValue('H1', 'Qty');
        $sheet->setCellValue('I1', 'Unit');

        $row = 2;
        $no = 1;

        foreach ($dataQry['data'] as $d) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $d['parent_name']);
            $sheet->setCellValue('C' . $row, $d['kode_barang']);
            $sheet->setCellValue('D' . $row, $d['barang_name']);
            $sheet->setCellValue('E' . $row, $d['spesifikasi']);
            $sheet->setCellValue('F' . $row, $d['divisi']);
            $sheet->setCellValue('G' . $row, $d['warehouse_name']);
            $sheet->setCellValue('H' . $row, (float)$d['qty_diterima']);
            $sheet->setCellValue('I' . $row, $d['kode_satuan']);
            $row++;
        }

        // Format kolom Qty (rata kanan + format ribuan)
        $lastRow = $row - 1;
        $sheet->getStyle('H2:H' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00'); // tampil seperti 25,000.23

        $sheet->getStyle('H2:H' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto width untuk semua kolom
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Bold header + rata tengah
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Output
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Stock_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function kartuStokView()
    {
        $tipeBarang = $this->metaDataModel->where('deletedAt', null)
            ->where('description !=', "kemasan")
            ->where('name', "Kategori Barang")
            ->findAll();

        $data = [
            'tipeBarang' => $tipeBarang,
            'divisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('Warehouse/stock/stock_card', $data);
    }

    public function getMasterBarang()
    {
        try {
            $type_barang = $this->request->getVar('type_barang');
            $dataList = $this->barangMasterModel->where('type_barang', $type_barang)
                ->where('company_id', $this->this_company_id)
                ->where('deletedAt', null)
                ->findAll();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => $dataList
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function allKartuStock()
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
            "start_date" => $this->request->getVar("start_date")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("start_date"))))
                : null,
            "end_date" => $this->request->getVar("end_date")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("end_date"))))
                : null,
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar("warehouse_id"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            'stock_revamp.deletedAt' => null,
            'stock_revamp.company_id' => $this->this_company_id,
            'stock_revamp.barang_master_id' => $this->request->getVar('barang_master_id')
        ];

        if (empty($condition['stock_revamp.barang_master_id']) || empty($addCondition['start_date']) || empty($addCondition['end_date'])) {
            return response()->setJSON([
                "draw"              => intval($this->request->getVar("draw")),
                "recordsTotal"      => 0,
                "recordsFiltered"   => 0,
                "data"              => [],
                "payload"           => $payload
            ]);
        }

        $dataQry = $this->stockRevampModel->getListKartuStock(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataTotalKartuStock = $this->getTotalKartuStockMasuk(
            $addCondition['start_date'],
            $addCondition['end_date']
        );

        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $d) {
            array_push($dataResult, [
                'no' => $no++,
                'id' => encrypt($d['id']),
                'divisi' => $d['divisi'],
                'warehouse_name' => $d['warehouse_name'],
                'kode_barang' => $d['kode_barang'],
                'barang_name' => $d['barang_name'],
                'spesifikasi' => $d['spesifikasi'],
                'qty_awal' => 0,
                'qty_masuk' => isset($dataTotalKartuStock[$d['id']]) ? (float)$dataTotalKartuStock[$d['id']] ?? 0 : 0,
                'qty_keluar' => 0,
                'qty_akhir' => 0,
                'kode_satuan' => $d['kode_satuan']
            ]);
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

    public function getStockIdentity()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $stock =  $this->stockRevampModel->getStockIdentity($id);
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => $stock
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    private function getTotalKartuStockMasuk($start_date, $end_date)
    {
        $condition = [
            'company_id' => $this->this_company_id,
            'dateStart'  => $start_date,
            'dateEnd'    => $end_date,
            'stock_id'   => "",
        ];

        $dataTotal =  $this->stockRevampLogModel->getKartuStockMasuk(
            $condition,
            0,
            "desc",
            100000000,
            0
        );

        $dataMap = [];

        foreach ($dataTotal['data'] as $d) {
            $stockId = $d['stock_id'];
            if (!isset($dataMap[$stockId])) {
                $dataMap[$stockId] = 0;
            }
            $dataMap[$stockId] += floatval($d['qty_diterima']);
        }

        return $dataMap;
    }


    public function allMasukKartuStock()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;

        $dateStart = $this->request->getVar("start_date")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("start_date"))))
            : null;

        $dateEnd = $this->request->getVar("end_date")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("end_date"))))
            : null;
        $stockId = ($this->request->getGet('stock_id'));
        $search = $this->request->getGet('search');

        $condition = [
            'company_id'        => $this->this_company_id,
            'dateStart'         => $dateStart,
            'dateEnd'           => $dateEnd,
            'stock_id'         => $stockId,
            'search'            => $search
        ];

        $dataTotal =  $this->stockRevampLogModel->getKartuStockMasuk(
            $condition,
            $orderColumnIndex,
            $orderDir,
            100000000,
            0
        );

        $totalMasuk = 0;
        foreach ($dataTotal['data'] as $d) {
            $totalMasuk += (float)$d['qty_diterima'];
        }

        $data = $this->stockRevampLogModel->getKartuStockMasuk(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $dataResult = array();
        $no = $start + 1;
        foreach ($data['data'] as $d) {
            array_push($dataResult, [
                'no' => $no++,
                'id' => $d['id'],
                'reference_type' => $d['reference_type'],
                'supplier_name' => $d['supplier_name'],
                'po_no' => $d['po_no'],
                'reference_no' => $d['reference_no'],
                'po_date' => !empty($d['po_date']) && $d['po_date'] != null ? date('d/m/Y', strtotime($d['po_date'])) : "",
                'lpb_date' => !empty($d['lpb_date']) && $d['lpb_date'] != null ? date('d/m/Y', strtotime($d['lpb_date'])) : "",
                'keterangan' => $d['keterangan'],
                'qty_diterima' => (float)$d['qty_diterima'],
                'kode_satuan' => $d['kode_satuan'],
            ]);
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($data['totalData'] ?? 0),
            'recordsFiltered' => intval($data['totalFilteredData'] ?? 0),
            'data' => $dataResult,
            'footerTotals' => $totalMasuk
        ]);
    }

    public function allKeluarKartuStock() {}
}
