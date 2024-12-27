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
            "bc_id" => "",
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
            // NO AJU REFERENSI
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

            // REFERENSI
            if ($data->sumber == "PENJUALAN" || $data->sumber == "MUTASI" || $data->sumber == "RETUR") {
                $dokumenBCReferensi = $this->metaDataModel->find($data->bc_id);
                $bcNameReferensi = $dokumenBCReferensi == null ? "NON PABEAN" : $dokumenBCReferensi['value'];
                if ($data->sumber != "MUTASI") {
                    $data->no_aju_referensi = $data->no_aju;
                } else {
                    $data->no_aju_referensi = "-";
                }
            } else {
                $bcNameReferensi = "-";
                $data->no_aju_referensi = "-";
            }


            // TEMPELKAN SAJA NO BC 3.0 JIKA PENJUALAN
            if ($data->sumber == "PENJUALAN" || $data->sumber == "MUTASI" || $data->sumber == "RETUR") {
                $bc30Internasional = $this->bc30Model
                    ->select('bc_30.no_aju, sales_order_export.bc_type')
                    ->join('sales_order_export', 'sales_order_export.sales_order_export_id = bc_30.sales_order_id', 'left')
                    ->join('stuffing_internasional', 'stuffing_internasional.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
                    ->where('stuffing_internasional.no_stuffing', $data->no_dokumen2)
                    ->where('bc_30.company_id', $this->this_company_id)
                    ->first();

                $bc30PengembalianBarang = $this->bc30Model
                    ->select('bc_30.no_aju, pengembalian_barang.bc_pengeluaran_id')
                    ->join('pengembalian_barang', 'pengembalian_barang.id = bc_30.pengembalian_barang_id', 'left')
                    ->where('pengembalian_barang.no_surat_jalan', $data->no_dokumen2)
                    ->where('bc_30.company_id', $this->this_company_id)
                    ->first();

                $bc30SalesOrderLain = $this->bc30Model
                    ->select('bc_30.no_aju, sales_order_lain.bc_id')
                    ->join('sales_order_lain', 'sales_order_lain.id = bc_30.sales_order_lain_id', 'left')
                    ->where('sales_order_lain.no_sales_order', $data->no_dokumen2)
                    ->where('bc_30.company_id', $this->this_company_id)
                    ->first();

                $bc25SalesOrderLain = $this->bc25Model
                    ->select('bc_25.no_aju, sales_order_lain.bc_id')
                    ->join('sales_order_lain', 'sales_order_lain.id = bc_25.sales_order_lain_id', 'left')
                    ->where('sales_order_lain.no_sales_order', $data->no_dokumen2)
                    ->where('bc_25.company_id', $this->this_company_id)
                    ->first();

                $bc25PengembalianBarang = $this->bc25Model
                    ->select('bc_25.no_aju, pengembalian_barang.bc_pengeluaran_id as bc_id')
                    ->join('pengembalian_barang', 'pengembalian_barang.id = bc_25.pengembalian_barang_id', 'left')
                    ->where('pengembalian_barang.no_surat_jalan', $data->no_dokumen2)
                    ->where('bc_25.company_id', $this->this_company_id)
                    ->first();

                $bc25OrderFormLokal = $this->bc25Model
                    ->select('bc_25.no_aju, sales_order.bc_type')
                    ->join('sales_order', 'sales_order.id = bc_25.sales_order_id', 'left')
                    ->join('stuffing_lokal', 'stuffing_lokal.sales_order_id = sales_order.id', 'left')
                    ->where('bc_25.company_id', $this->this_company_id)
                    ->where('stuffing_lokal.no_stuffing', $data->no_dokumen2)
                    ->first();

                $bc41SalesOrderLain = $this->bc41Model
                    ->select('bc_41.no_aju, sales_order_lain.bc_id')
                    ->join('sales_order_lain', 'sales_order_lain.id = bc_41.sales_order_lain_id', 'left')
                    ->where('sales_order_lain.no_sales_order', $data->no_dokumen2)
                    ->where('bc_41.company_id', $this->this_company_id)
                    ->first();

                $bc41PengeluaranBarang = $this->bc41Model
                    ->select('bc_41.no_aju, pengembalian_barang.bc_pengeluaran_id as bc_id')
                    ->join('pengembalian_barang', 'pengembalian_barang.id = bc_41.pengembalian_barang_id', 'left')
                    ->where('pengembalian_barang.no_surat_jalan', $data->no_dokumen2)
                    ->where('bc_41.company_id', $this->this_company_id)
                    ->first();

                $ppbkbMutasi = $this->ppbkbModel
                    ->select('ppbkb.no_ppbkb')
                    ->join('mutasi', 'mutasi.id = ppbkb.mutasi_id', 'left')
                    ->where('mutasi.company_id', $this->this_company_id)
                    ->where('mutasi.no_mutasi', $data->no_dokumen2)
                    ->first();

                $bc27MutasiGlobal = $this->bc27Model
                    ->select('bc_27.no_aju')
                    ->join('mutasi_global', 'mutasi_global.id = bc_27.mutasi_global_id', 'left')
                    // ->where('mutasi_global.company_asal_id', $this->this_company_id)
                    ->where('mutasi_global.no_mutasi', $data->no_dokumen2)
                    ->first();

                if ($bc30SalesOrderLain != null) {
                    // SALES ORDER LAIN
                    $dokumenBC = $this->metaDataModel->find($bc30SalesOrderLain['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc30SalesOrderLain['no_aju'];
                } elseif ($bc30PengembalianBarang != null) {
                    // RETUR
                    $dokumenBC = $this->metaDataModel->find($bc30PengembalianBarang['bc_pengeluaran_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc30PengembalianBarang['no_aju'];
                } elseif ($bc30Internasional != null) {
                    // STUFFING INTERNASIONAL BC 3.0 SUDAH DIBUAT
                    $dokumenBC = $this->metaDataModel->find($bc30Internasional['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju_referensi = $data->no_aju;
                    $data->no_aju = $bc30Internasional['no_aju'];
                } elseif ($bc25OrderFormLokal != null) {
                    // ORDER FORM LOKAL
                    $dokumenBC = $this->metaDataModel->find($bc25OrderFormLokal['bc_type']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju_referensi = $data->no_aju;
                    $data->no_aju = $bc25OrderFormLokal['no_aju'];
                } elseif ($bc25SalesOrderLain != null) {
                    // BEA CUKAI 2.5 SALES ORDER
                    $dokumenBC = $this->metaDataModel->find($bc25SalesOrderLain['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc25SalesOrderLain['no_aju'];
                } elseif ($bc25PengembalianBarang != null) {
                    // BEA CUKAI 2.5 PENGEMBALIAN BARANG
                    $dokumenBC = $this->metaDataModel->find($bc25PengembalianBarang['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc25PengembalianBarang['no_aju'];
                } elseif ($bc41SalesOrderLain != null) {
                    // BEA CUKAI 4.1 SALES ORDER LAIN
                    $dokumenBC = $this->metaDataModel->find($bc41SalesOrderLain['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc41SalesOrderLain['no_aju'];
                } elseif ($bc41PengeluaranBarang != null) {
                    // BEA CUKAI 4.1 PENGELUARAN BARANG
                    $dokumenBC = $this->metaDataModel->find($bc41PengeluaranBarang['bc_id']);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                    $data->no_aju = $bc41PengeluaranBarang['no_aju'];
                } elseif ($ppbkbMutasi != null) {
                    // PPBKB'
                    $bcName = "PPBKB";
                    $data->no_aju_referensi = $data->no_aju;
                    $data->no_aju = $ppbkbMutasi['no_ppbkb'];
                } elseif ($bc27MutasiGlobal != null) {
                    $bcName = "BC 2.7";
                    $data->no_aju_referensi = $data->no_aju;
                    $data->no_aju = $bc27MutasiGlobal['no_aju'];
                } else {
                    // BELUM DIBUAT SAMA SEKALI DOKUMEN BC 3.O NYA
                    $dokumenBC = $this->metaDataModel->find($data->bc_id);
                    $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
                }
            } else {
                $dokumenBC = $this->metaDataModel->find($data->bc_id);
                $bcName = $dokumenBC == null ? "NON PABEAN" : $dokumenBC['value'];
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
                        "supplier_name" => $data->supplier_name == null ? "-" : strtoupper($data->supplier_name),
                        'parent_type' => $parentBarang != null ? strtoupper(str_replace('_', ' ', $parentBarang['parent_type'])) : '',
                        'parent_name' => $parentBarang != null ? $parentBarang['parent_name'] : '',
                        'kode_barang' => $barangMaster['kode_barang'] ?? '',
                        "barang" => strtoupper(($barangMaster['barang_name'] ?? '') . " - " . ($barangMasterSpesifikasi['spesifikasi'] ?? '')),
                        "divisi" => $divisi != null ? strtoupper($divisi['divisi']) : '',
                        "warehouse" => $warehouse != null ? strtoupper($warehouse['warehouse_name']) : '',
                        "dokumen_pabean" => $bcName . " / " . $data->no_aju,
                        "dokumen_referensi" => $bcNameReferensi . " / " . $data->no_aju_referensi,
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
                        "supplier_name" => $data->supplier_name == null ? "-" : strtoupper($data->supplier_name),
                        'parent_type' => $parentBarang != null ? strtoupper(str_replace('_', ' ', $parentBarang['parent_type'])) : '',
                        'parent_name' => $parentBarang != null ? $parentBarang['parent_name'] : '',
                        'kode_barang' => $kemasan['kode'] ?? '',
                        "barang" => strtoupper($kemasan['name'] ?? ''),
                        "divisi" => $divisi != null ? strtoupper($divisi['divisi']) : '',
                        "warehouse" => $warehouse != null ? strtoupper($warehouse['warehouse_name']) : '',
                        "dokumen_pabean" => $bcName . " / " . $data->no_aju,
                        "dokumen_referensi" => $bcNameReferensi . " / " . $data->no_aju_referensi,
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
