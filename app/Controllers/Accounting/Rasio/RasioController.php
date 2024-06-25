<?php

namespace App\Controllers\Accounting\Rasio;

use App\Controllers\BaseController;
use App\Controllers\JasaVendor\BiayaKepiting;
use App\Controllers\JasaVendor\BiayaUdang;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\AccountDivisisModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BiayaKepitingDetailModel;
use App\Models\BiayaKepitingGajiModel;
use App\Models\BiayaKepitingModel;
use App\Models\BiayaUdangDetailModel;
use App\Models\BiayaUdangModel;
use App\Models\JasaVendorInModel;
use App\Models\JurnalUmumModel;
use App\Models\KemasanModel;
use App\Models\KursModel;
use App\Models\MaterialRequestPenolongDetailsModel;
use App\Models\MaterialRequestsPenolongModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\ProductionResultDetailModel;
use App\Models\ProductionResultModel;
use App\Models\RasioBahanPenolongModel;
use App\Models\RasioBarangDigunakanModel;
use App\Models\RasioBarangJadiModel;
use App\Models\RasioCostModel;
use App\Models\RasioModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SatuansModel;
use App\Models\SettingCostingModel;
use App\Models\StockDetail2Model;
use App\Models\StockModel;

class RasioController extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $divisisModel;
    protected $subAkunModel;
    protected $productionResultModel;
    protected $productionResultDetailModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $rmPurchaseOrderModel;
    protected $rmPurchaseOrderDetailModel;
    protected $rmImportPOModel;
    protected $rmImportPODetailModel;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $rasioModel;
    protected $rasioBarangDigunakanModel;
    protected $rasioBarangJadiModel;
    protected $rasioBarangPenolongModel;
    protected $rasioCostModel;
    protected $jurnalUmumModel;
    protected $settingCosting;
    protected $materialRequestsPenolongModel;
    protected $materialRequestPenolongDetailsModel;
    protected $metadataModel;
    protected $kursModel;
    protected $biayaUdangModel;
    protected $biayaUdangDetailModel;
    protected $biayaKepitingModel;
    protected $biayaKepitingDetailModel;
    protected $biayaKepitingGajiModel;
    protected $jasaVendorInModel;
    protected $stockModel;
    protected $stockDetail2Model;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $satuanModel;
    protected $kemasanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisisModel = new DivisisModel();
        $this->subAkunModel = new Sub_AkunsModel();
        $this->productionResultModel = new ProductionResultModel();
        $this->productionResultDetailModel = new ProductionResultDetailModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->rasioModel = new RasioModel();
        $this->rasioBarangDigunakanModel = new RasioBarangDigunakanModel();
        $this->rasioBarangJadiModel = new RasioBarangJadiModel();
        $this->rasioBarangPenolongModel = new RasioBahanPenolongModel();
        $this->rasioCostModel = new RasioCostModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->settingCosting = new SettingCostingModel();
        $this->materialRequestsPenolongModel = new MaterialRequestsPenolongModel();
        $this->materialRequestPenolongDetailsModel = new MaterialRequestPenolongDetailsModel();
        $this->metadataModel = new MetadataModel();
        $this->kursModel = new KursModel();
        $this->biayaUdangModel = new BiayaUdangModel();
        $this->biayaUdangDetailModel = new BiayaUdangDetailModel();
        $this->biayaKepitingModel = new BiayaKepitingModel();
        $this->biayaKepitingDetailModel = new BiayaKepitingDetailModel();
        $this->biayaKepitingGajiModel = new BiayaKepitingGajiModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->stockModel = new StockModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->satuanModel = new SatuansModel();
        $this->kemasanModel = new KemasanModel();
    }

    public function index()
    {
        $subAkunsModel = $this->subAkunModel->asObject()->findAll();
        $data = [
            'dataDivisi' => $this->divisisModel->getDivisiAccess(),
            "subAkuns" => $subAkunsModel
        ];
        return view('Accounting/rasio/index', $data);
    }

    public function createRasio()
    {
        $subAkunsModel = $this->subAkunModel->asObject()->findAll();
        $data = [
            'dataDivisi' => $this->divisisModel->getDivisiAccess(),
            'kategoriBarangAkun' => $this->metadataModel->asObject()->where('name', 'kategori_barang_akun')->findAll(),
            "subAkuns" => $subAkunsModel
        ];
        return view('Accounting/rasio/form', $data);
    }

    public function saveRasio()
    {
        try {
            $tanggal_input = $this->request->getVar("tanggal") ? $this->request->getVar("tanggal") : "";
            $tanggal_parts = explode("/", $tanggal_input); // Memisahkan bulan dan tahun
            $tanggal_mysql = $tanggal_parts[1] . "-" . str_pad($tanggal_parts[0], 2, "0", STR_PAD_LEFT);

            $cekRasio = $this->rasioModel
                ->where("divisi_id", $this->request->getVar("divisi_id"))
                ->where("bulan", $tanggal_mysql)
                ->findAll();

            if ($cekRasio) {
                $data = [
                    "status"     => false,
                    "message"    => "Rasio untuk department dan bulan ini sudah ada",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "company_id" => $this->this_company_id,
                "divisi_id" => $this->request->getVar("divisi_id"),
                'bulan' => $tanggal_mysql,
                'kategori_barang_id' => $this->request->getVar("kategori"),
                'subsidi_coa_id' => $this->request->getVar("akun_coa_subsidi") ?? null,
                'biaya_coa_id' => $this->request->getVar("akun_coa_biaya") ?? null,
                'kopek_coa_id' => $this->request->getVar("akun_coa_kopek") ?? null,
                'total_subsidi' => $this->request->getVar("biayaSubsidi") ? number_format((float) str_replace(",", "", $this->request->getVar("biayaSubsidi")), 2, '.', '') : 0,
                'total_biaya' => $this->request->getVar("biayaLain") ? number_format((float) str_replace(["Rp", "."], "", $this->request->getVar("biayaLain")), 2, '.', '') : 0,
                "total_kopek" => $this->request->getVar("biayaKopek") ? number_format((float) str_replace(["Rp", "."], "", $this->request->getVar("biayaKopek")), 2, '.', '') : 0,
                'total_qty_po' => $this->request->getVar("qtyTotalPembelian") ? number_format((float) str_replace(",", "", $this->request->getVar("qtyTotalPembelian")), 2, '.', '') : 0,
                'harga_total_po' => $this->request->getVar("hargaTotalPembelian") ? number_format((float) str_replace(["Rp", "."], "", $this->request->getVar("hargaTotalPembelian")), 2, '.', '') : 0,
                "harga_average_po" => $this->request->getVar("hargaSatuanPembelian") ? number_format((float) str_replace(["Rp", "."], "", $this->request->getVar("hargaSatuanPembelian")), 2, '.', '') : 0,
                'total_qty_lpb' => $this->request->getVar('qtyTotalPenerimaan') ? number_format((float) str_replace(",", "", $this->request->getVar('qtyTotalPenerimaan')), 2, '.', '') : 0,
                'harga_total_lpb' => $this->request->getVar('hargaTotalPenerimaan') ? number_format((float) str_replace(["Rp", "."], "", $this->request->getVar('hargaTotalPenerimaan')), 2, '.', '') : 0,
                'harga_average_lpb' => $this->request->getVar('hargaSatuanPenerimaan') ? number_format((float) str_replace(["Rp", "."], "", $this->request->getVar('hargaSatuanPenerimaan')), 2, '.', '') : 0,
                'total_qty_po_bp' => $this->request->getVar("qtyTotalPembelian_material_2") ? number_format((float) str_replace(",", "", $this->request->getVar("qtyTotalPembelian_material_2")), 2, '.', '') : 0,
                'harga_total_po_bp' => $this->request->getVar("hargaTotalPembelian_material_2") ? number_format((float) str_replace(["Rp", "."], "", $this->request->getVar("hargaTotalPembelian_material_2")), 2, '.', '') : 0,
                "harga_average_po_bp" => $this->request->getVar("hargaSatuanPembelian_material_2") ? number_format((float) str_replace(["Rp", "."], "", $this->request->getVar("hargaSatuanPembelian_material_2")), 2, '.', '') : 0,
            ];

            $id = $this->rasioModel->insert($data);

            $barang_digunakan = json_decode($this->request->getVar("items_digunakan"));
            $barang_jadi = json_decode($this->request->getVar("items_jadi"));
            $barang_digunakan_material_2 = json_decode($this->request->getVar("items_digunakan_material_2"));
            $labor_cost = json_decode($this->request->getVar("labor_cost"));
            $overhead_cost = json_decode($this->request->getVar("overhead_cost"));
            $fixed_cost = json_decode($this->request->getVar("fixed_cost"));

            foreach ($barang_digunakan as $s) {
                $this->rasioBarangDigunakanModel->insert([
                    'rasio_id' => $id,
                    'barang1_id' => $s->barang1_id,
                    'barang2_id' => $s->barang2_id,
                    'barang_name' => $s->barang_name,
                    'spesifikasi' => $s->spesifikasi,
                    'qty_po' => $s->totalQtyPO ?? 0,
                    'harga_po_total' => $s->totalHargaPO ?? 0,
                    'harga_po_satuan' => $s->hargaSatuanPO ?? 0,
                    'satuan_po' => $s->satuanPO  ?? "-",
                    'qty_lpb' => $s->totalQtyLPB ?? 0,
                    'harga_lpb_total' => $s->totalHargaLPB ?? 0,
                    'harga_lpb_satuan' => $s->hargaSatuanLPB ?? 0,
                    'satuan_lpb' => $s->satuanLPB ?? "-",
                    'no_dokumen' => $s->no_dokumen,
                    'stock_dokumen' => $s->stock_dokumen,
                ]);
            }

            foreach ($barang_jadi as $s) {
                $this->rasioBarangJadiModel->insert([
                    'rasio_id' => $id,
                    'barang_name' => $s->barang_name,
                    'kode_barang' => $s->kode_barang,
                    'spesifikasi' => $s->spesifikasi,
                    'production_result_detail_id' => $s->production_result_detail_id,
                    'production_result_id' => $s->production_result_id,
                    'barang1_id' => $s->barang1_id,
                    'barang2_id' => $s->barang2_id,
                    'bc_id' => $s->bc_id,
                    'stock_id' => $s->stock_id,
                    'no_aju' => $s->no_aju,
                    'stock_dokumen' => $s->stock_dokumen,
                    'qty_barang' => $s->qty,
                    'rasio_barang' => $s->rasio,
                    'harga_barang' => $s->harga,
                    'kode_satuan' => $s->kode_satuan,
                ]);
            }

            foreach ($barang_digunakan_material_2 as $bd) {
                foreach ($bd->inputData as $bdm) {
                    $data = [
                        'rasio_id' => $id,
                        'production_result_id' => $bdm->id_production,
                        'production_result_detail_id' => $bdm->id_production_detail,
                        'barang1_id' => $bd->barang1_id,
                        'barang2_id' => $bd->barang2_id,
                        'barang1_id_production' => $bdm->barang1_id_production,
                        'barang2_id_production' => $bdm->barang2_id_production,
                        'parent_type_id' => $bd->parent_type_id,
                        'barang_name' => $bd->barang_name,
                        'kode_barang' => $bd->kode_barang,
                        'parent_name' => $bd->parent_name,
                        'spesifikasi' => $bd->spesifikasi,
                        'qty_barang' => $bdm->qty_input,
                        'total_barang' => $bdm->totalHarga_input,
                        'harga_barang' => $bdm->hargaSatuan_input,
                    ];
                    $this->rasioBarangPenolongModel->insert($data);
                }
            }

            foreach ($labor_cost as $bd) {
                foreach ($bd->inputData as $bdm) {
                    $data = [
                        'rasio_id' => $id,
                        'production_result_id' => $bdm->id_production,
                        'production_result_detail_id' => $bdm->id_production_detail,
                        'setting_costing_id' => $bd->id,
                        'setting_costing_parent_id' => $bd->parent_id,
                        'coa_id' => $bd->coa,
                        'barang1_id_production' => $bdm->barang1_id_production,
                        'barang2_id_production' => $bdm->barang2_id_production,
                        'name' => $bd->name,
                        'type' => "labor",
                        // 'jumlah_jurnal' => $bd->jmlhJurnal,
                        'qty_cost' => $bdm->qty_input,
                        'total_cost' => $bdm->totalHarga_input,
                        'harga_cost' => $bdm->hargaSatuan_input,
                    ];
                    $this->rasioCostModel->insert($data);
                }
            }

            foreach ($overhead_cost as $bd) {
                foreach ($bd->inputData as $bdm) {
                    $data = [
                        'rasio_id' => $id,
                        'production_result_id' => $bdm->id_production,
                        'production_result_detail_id' => $bdm->id_production_detail,
                        'setting_costing_id' => $bd->id,
                        'setting_costing_parent_id' => $bd->parent_id,
                        'coa_id' => $bd->coa,
                        'barang1_id_production' => $bdm->barang1_id_production,
                        'barang2_id_production' => $bdm->barang2_id_production,
                        'name' => $bd->name,
                        'type' => "overhead",
                        // 'jumlah_jurnal' => $bd->jmlhJurnal,
                        'qty_cost' => $bdm->qty_input,
                        'total_cost' => $bdm->totalHarga_input,
                        'harga_cost' => $bdm->hargaSatuan_input,
                    ];
                    $this->rasioCostModel->insert($data);
                }
            }

            foreach ($fixed_cost as $bd) {
                foreach ($bd->inputData as $bdm) {
                    $data = [
                        'rasio_id' => $id,
                        'production_result_id' => $bdm->id_production,
                        'production_result_detail_id' => $bdm->id_production_detail,
                        'setting_costing_id' => $bd->id,
                        'setting_costing_parent_id' => $bd->parent_id,
                        'coa_id' => $bd->coa,
                        'barang1_id_production' => $bdm->barang1_id_production,
                        'barang2_id_production' => $bdm->barang2_id_production,
                        'name' => $bd->name,
                        'type' => "fixed",
                        // 'jumlah_jurnal' => $bd->jmlhJurnal,
                        'qty_cost' => $bdm->qty_input,
                        'total_cost' => $bdm->totalHarga_input,
                        'harga_cost' => $bdm->hargaSatuan_input,
                    ];
                    $this->rasioCostModel->insert($data);
                }
            }

            return response()->setJSON([
                "id"      => encrypt($id),
                "status"  => true,
                "message" => "Data Berhasil disimpan",
                'token'   => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function getById($id = null)
    {
        $id = decrypt($id);
        $subAkunsModel = $this->subAkunModel->asObject()->findAll();
        $rasioModel = $this->rasioModel->asObject()->find($id);
        $rasioBarangDigunakanModel = $this->rasioBarangDigunakanModel->asObject()->where('rasio_id', $id)->findAll();
        $rasioBarangJadiModel = $this->rasioBarangJadiModel->asObject()->where('rasio_id', $id)->findAll();
        $rasioBarangPenolongModel = $this->rasioBarangPenolongModel->asObject()->where('rasio_id', $id)->findAll();
        $rasioCostModel = $this->rasioCostModel->asObject()->where('rasio_id', $id)->findAll();

        $totalQtyAll = 0;
        foreach ($rasioBarangJadiModel as &$value) {
            $totalQtyAll += $value->qty_barang;
            $value->totalQtyAll = $totalQtyAll;
        }
        $data = [
            'dataDivisi' => $this->divisisModel->getDivisiAccess(),
            "subAkuns" => $subAkunsModel,
            "rasio" => $rasioModel,
            "rasioBarangDigunakan" => $rasioBarangDigunakanModel,
            "rasioBarangJadi" => $rasioBarangJadiModel,
            "rasioBarangPenolong" => $rasioBarangPenolongModel,
            "rasioCost" => $rasioCostModel,
        ];
        // var_dump($data);
        // exit;
        return view('Accounting/rasio/form', $data);
    }

    public function allRasio()
    {
        $tanggal_input = $this->request->getGet("dateStart") ? $this->request->getGet("dateStart") : "";
        if ($tanggal_input != "") {
            $tanggal_parts = explode("/", $tanggal_input); // Memisahkan bulan dan tahun
            $tanggal_mysql = $tanggal_parts[1] . "-" . str_pad($tanggal_parts[0], 2, "0", STR_PAD_LEFT);
        }

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "rasio.company_id"  => $this->this_company_id,
            "rasio.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "month"         => $tanggal_input != "" ? $tanggal_mysql : "",
            "department"    => $this->request->getGet("divisi_id"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->rasioModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "divisi"                => $data['divisi'],
                "month"                 => $data['bulan'],
                "harga"                 => "" . number_format(formatter($data['harga_total_lpb'], "STR_TO_FLOAT"), 2, '.', ','),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
            "test" => $_GET
        ];

        return response()->setJSON($data);
    }

    public function getRasioBarangJadi()
    {
        if (!empty($this->request->getVar('bulan'))) {
            $monthData = $this->request->getVar('bulan');
            list($month, $year) = explode('/', $monthData);
            $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $conditionProduction = [
                'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
                'divisi_id' => $this->request->getVar('department'),
                'kategori_id' => $this->request->getVar('kategori'),
            ];
            $productionResultDataTitle = $this->productionResultModel->getDataProductionResultWithDetail($conditionProduction);

            $totalQtyAll = 0;
            foreach ($productionResultDataTitle as $value) {
                $totalQtyAll += $value['qtyTotal'];
            }
            foreach ($productionResultDataTitle as &$value) {
                $value['totalQtyAll'] = $totalQtyAll;
            }
            if ($productionResultDataTitle) {
                return response()->setJSON([
                    'data' => $productionResultDataTitle,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }
        }
    }

    public function getRasioBarangDigunakan()
    {
        if (!empty($this->request->getVar('bulan'))) {
            $monthData = $this->request->getVar('bulan');
            list($month, $year) = explode('/', $monthData);
            $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $conditionProduction = [
                'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
                'divisi_id' => $this->request->getVar('department'),
            ];
            $kursValue = 1;
            $productionResultDataTitle = $this->productionResultModel->getDataProductionResultBahanBakuWithDetail($conditionProduction);

            foreach ($productionResultDataTitle as &$value) {
                $stockDokumen = explode(' ', $value['stock_dokumen'])[0]; // Get the first part of the split string
                $poBBLokal = $this->rmPurchaseOrderModel->where('po_no', $stockDokumen)->first();
                $poBBImport = $this->rmImportPOModel->where('po_no', $stockDokumen)->first();
                $poBP = $this->amPurchaseOrderModel->where('po_no', $stockDokumen)->first();
                $jasaVendorIn = $this->jasaVendorInModel->where('no_penerimaan_surat_jalan', $stockDokumen)->first();
                $penerimaanBarang = $this->penerimaanBarangModel->where('no_penerimaan_barang', $value['no_dokumen'])->first();

                // if ($jasaVendorIn) {
                //     // Ensure $jasaVendorIn['id'] is wrapped in an array for whereIn
                //     $biayaVendorUdang = $this->biayaUdangModel
                //         ->whereIn('biaya_udang.multiple_jasa_vendor_in_id', [$jasaVendorIn['id']])
                //         ->first();
                //     $biayaVendorKepiting = $this->biayaKepitingModel
                //         ->where('biaya_kepiting.jasa_vendor_in_id', $jasaVendorIn['id'])
                //         ->first();

                //     if ($biayaVendorUdang) {
                //         var_dump($biayaVendorUdang);
                //     }

                //     if ($biayaVendorKepiting) {
                //         $biayaKepitingDetail = $this->biayaKepitingDetailModel
                //             ->where('barang_master_id', $value['barang1_id'])
                //             ->where('barang_master_spesifikasi_id', $value['barang2_id'])
                //             ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //             ->first();
                //         if ($biayaKepitingDetail['jumbo']) {
                //             $upahKopekKepiting = $this->biayaKepitingGajiModel
                //                 ->where('jumbo !=', 0)
                //                 ->where('jenis', 'Upah Kopek')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $komisiKepiting = $this->biayaKepitingGajiModel
                //                 ->where('jumbo !=', 0)
                //                 ->where('jenis', 'Komisi / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $bonusKepiting = $this->biayaKepitingGajiModel
                //                 ->where('jumbo !=', 0)
                //                 ->where('jenis', 'Bonus / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $tambahanKepiting = $this->biayaKepitingGajiModel
                //                 ->where('jumbo !=', 0)
                //                 ->where('jenis', 'Tamb. Upah Kopek Ex. Lump')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $biayaKepitingDetail['upah_kopek'] = $upahKopekKepiting['jumbo'] ?? 0;
                //             $biayaKepitingDetail['komisi'] = $komisiKepiting['jumbo'] ?? 0;
                //             $biayaKepitingDetail['bonus'] = $bonusKepiting['jumbo'] ?? 0;
                //             $biayaKepitingDetail['tambahan'] = $tambahanKepiting['jumbo'] ?? 0;
                //         } else if ($biayaKepitingDetail['ex_lump']) {
                //             $upahKopekKepiting = $this->biayaKepitingGajiModel
                //                 ->where('ex_lump !=', 0)
                //                 ->where('jenis', 'Upah Kopek')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $komisiKepiting = $this->biayaKepitingGajiModel
                //                 ->where('ex_lump !=', 0)
                //                 ->where('jenis', 'Komisi / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $bonusKepiting = $this->biayaKepitingGajiModel
                //                 ->where('ex_lump !=', 0)
                //                 ->where('jenis', 'Bonus / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $tambahanKepiting = $this->biayaKepitingGajiModel
                //                 ->where('ex_lump !=', 0)
                //                 ->where('jenis', 'Tamb. Upah Kopek Ex. Lump')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $biayaKepitingDetail['upah_kopek'] = $upahKopekKepiting['ex_lump'] ?? 0;
                //             $biayaKepitingDetail['komisi'] = $komisiKepiting['ex_lump'] ?? 0;
                //             $biayaKepitingDetail['bonus'] = $bonusKepiting['ex_lump'] ?? 0;
                //             $biayaKepitingDetail['tambahan'] = $tambahanKepiting['ex_lump'] ?? 0;
                //         } else if ($biayaKepitingDetail['lump']) {
                //             $upahKopekKepiting = $this->biayaKepitingGajiModel
                //                 ->where('lump !=', 0)
                //                 ->where('jenis', 'Upah Kopek')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $komisiKepiting = $this->biayaKepitingGajiModel
                //                 ->where('lump !=', 0)
                //                 ->where('jenis', 'Komisi / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $bonusKepiting = $this->biayaKepitingGajiModel
                //                 ->where('lump !=', 0)
                //                 ->where('jenis', 'Bonus / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $tambahanKepiting = $this->biayaKepitingGajiModel
                //                 ->where('lump !=', 0)
                //                 ->where('jenis', 'Tamb. Upah Kopek Ex. Lump')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $biayaKepitingDetail['upah_kopek'] = $upahKopekKepiting['lump'] ?? 0;
                //             $biayaKepitingDetail['komisi'] = $komisiKepiting['lump'] ?? 0;
                //             $biayaKepitingDetail['bonus'] = $bonusKepiting['lump'] ?? 0;
                //             $biayaKepitingDetail['tambahan'] = $tambahanKepiting['lump'] ?? 0;
                //         } else if ($biayaKepitingDetail['special']) {
                //             $upahKopekKepiting = $this->biayaKepitingGajiModel
                //                 ->where('special !=', 0)
                //                 ->where('jenis', 'Upah Kopek')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $komisiKepiting = $this->biayaKepitingGajiModel
                //                 ->where('special !=', 0)
                //                 ->where('jenis', 'Komisi / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $bonusKepiting = $this->biayaKepitingGajiModel
                //                 ->where('special !=', 0)
                //                 ->where('jenis', 'Bonus / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $tambahanKepiting = $this->biayaKepitingGajiModel
                //                 ->where('special !=', 0)
                //                 ->where('jenis', 'Tamb. Upah Kopek Ex. Lump')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $biayaKepitingDetail['upah_kopek'] = $upahKopekKepiting['special'] ?? 0;
                //             $biayaKepitingDetail['komisi'] = $komisiKepiting['special'] ?? 0;
                //             $biayaKepitingDetail['bonus'] = $bonusKepiting['special'] ?? 0;
                //             $biayaKepitingDetail['tambahan'] = $tambahanKepiting['special'] ?? 0;
                //         } else if ($biayaKepitingDetail['claw']) {
                //             $upahKopekKepiting = $this->biayaKepitingGajiModel
                //                 ->where('claw !=', 0)
                //                 ->where('jenis', 'Upah Kopek')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $komisiKepiting = $this->biayaKepitingGajiModel
                //                 ->where('claw !=', 0)
                //                 ->where('jenis', 'Komisi / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $bonusKepiting = $this->biayaKepitingGajiModel
                //                 ->where('claw !=', 0)
                //                 ->where('jenis', 'Bonus / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $tambahanKepiting = $this->biayaKepitingGajiModel
                //                 ->where('claw !=', 0)
                //                 ->where('jenis', 'Tamb. Upah Kopek Ex. Lump')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $biayaKepitingDetail['upah_kopek'] = $upahKopekKepiting['claw'] ?? 0;
                //             $biayaKepitingDetail['komisi'] = $komisiKepiting['claw'] ?? 0;
                //             $biayaKepitingDetail['bonus'] = $bonusKepiting['claw'] ?? 0;
                //             $biayaKepitingDetail['tambahan'] = $tambahanKepiting['claw'] ?? 0;
                //         } else if ($biayaKepitingDetail['mh']) {
                //             $upahKopekKepiting = $this->biayaKepitingGajiModel
                //                 ->where('mh !=', 0)
                //                 ->where('jenis', 'Upah Kopek')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $komisiKepiting = $this->biayaKepitingGajiModel
                //                 ->where('mh !=', 0)
                //                 ->where('jenis', 'Komisi / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $bonusKepiting = $this->biayaKepitingGajiModel
                //                 ->where('mh !=', 0)
                //                 ->where('jenis', 'Bonus / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $tambahanKepiting = $this->biayaKepitingGajiModel
                //                 ->where('mh !=', 0)
                //                 ->where('jenis', 'Tamb. Upah Kopek Ex. Lump')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $biayaKepitingDetail['upah_kopek'] = $upahKopekKepiting['mh'] ?? 0;
                //             $biayaKepitingDetail['komisi'] = $komisiKepiting['mh'] ?? 0;
                //             $biayaKepitingDetail['bonus'] = $bonusKepiting['mh'] ?? 0;
                //             $biayaKepitingDetail['tambahan'] = $tambahanKepiting['mh'] ?? 0;
                //         } else if ($biayaKepitingDetail['cf']) {
                //             $upahKopekKepiting = $this->biayaKepitingGajiModel
                //                 ->where('cf !=', 0)
                //                 ->where('jenis', 'Upah Kopek')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $komisiKepiting = $this->biayaKepitingGajiModel
                //                 ->where('cf !=', 0)
                //                 ->where('jenis', 'Komisi / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $bonusKepiting = $this->biayaKepitingGajiModel
                //                 ->where('cf !=', 0)
                //                 ->where('jenis', 'Bonus / Kg Daging')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $tambahanKepiting = $this->biayaKepitingGajiModel
                //                 ->where('cf !=', 0)
                //                 ->where('jenis', 'Tamb. Upah Kopek Ex. Lump')
                //                 ->where('biaya_kepiting_id', $biayaVendorKepiting['id'])
                //                 ->first();
                //             $biayaKepitingDetail['upah_kopek'] = $upahKopekKepiting['cf'] ?? 0;
                //             $biayaKepitingDetail['komisi'] = $komisiKepiting['cf'] ?? 0;
                //             $biayaKepitingDetail['bonus'] = $bonusKepiting['cf'] ?? 0;
                //             $biayaKepitingDetail['tambahan'] = $tambahanKepiting['cf'] ?? 0;
                //         }
                //         var_dump($biayaKepitingDetail);
                //     }
                //     $value['totalQtyKopek'] = $totalQty;
                //     $value['totalHargaKopek'] = $totalHarga;
                //     $value['hargaSatuanKopek'] = $hargaSatuan;
                //     $value['satuanKopek'] = $satuanPO;
                // }

                if ($poBBLokal) {
                    $totalQty = 0;
                    $totalHarga = 0;
                    $hargaSatuan = 0;
                    $satuanPO = "";
                    $poBBLokalDetail = $this->rmPurchaseOrderDetailModel
                        ->select('rm_purchase_order_details.*, satuans.kode_satuan')
                        ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
                        ->where('rm_purchase_order_id', $poBBLokal['id'])
                        ->where('barang1_id', $value['barang1_id'])
                        ->where('barang2_id', $value['barang2_id'])
                        ->findAll();
                    foreach ($poBBLokalDetail as $valuePoBBLokal) {
                        $kursValue = 1;
                        $hargaSatuan = $valuePoBBLokal['general_price'] + $valuePoBBLokal['daily_price'] + $valuePoBBLokal['monthly_price'];
                        $totalQty += $valuePoBBLokal['qty'];
                        $satuanPO = $valuePoBBLokal['kode_satuan'];
                    }
                    $totalHarga = $totalQty * $hargaSatuan;
                    $value['totalQtyPO'] = $totalQty;
                    $value['totalHargaPO'] = $totalHarga;
                    $value['hargaSatuanPO'] = $hargaSatuan;
                    $value['satuanPO'] = $satuanPO;
                }

                if ($poBBImport) {
                    $totalQty = 0;
                    $totalHarga = 0;
                    $hargaSatuan = 0;
                    $hargaSatuanDisc = 0;
                    $satuanPO = "";
                    $poBBImportDetail = $this->rmImportPODetailModel
                        ->select('rm_import_po_details.*, satuans.kode_satuan, rm_import_pos.currency, rm_import_pos.po_date')
                        ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
                        ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
                        ->where('rm_import_po_id', $poBBImport['id'])
                        ->where('barang_id', $value['barang1_id'])
                        ->where('spesifikasi_id', $value['barang2_id'])
                        ->findAll();
                    foreach ($poBBImportDetail as $valuePoBBImport) {
                        $kurs = $this->kursModel
                            ->where('metadata_id', $valuePoBBImport['currency'])
                            ->where('start_date <=', $valuePoBBImport['po_date'])
                            ->where('end_date >=', $valuePoBBImport['po_date'])
                            ->first();
                        $kursValue = $kurs ? $kurs['nilai_kurs'] : 1;
                        $hargaSatuanDisc = ($valuePoBBImport['price'] * $kursValue) * ($valuePoBBImport['disc'] / 100);
                        $hargaSatuan = ($valuePoBBImport['price'] * $kursValue) - $hargaSatuanDisc;
                        $totalQty += $valuePoBBImport['qty'];
                        $satuanPO = $valuePoBBImport['kode_satuan'];
                    }
                    $totalHarga = $totalQty * $hargaSatuan;
                    $value['totalQtyPO'] = $totalQty;
                    $value['totalHargaPO'] = $totalHarga;
                    $value['hargaSatuanPO'] = $hargaSatuan;
                    $value['satuanPO'] = $satuanPO;
                }
                if ($poBP) {
                    $totalQty = 0;
                    $totalHarga = 0;
                    $hargaSatuan = 0;
                    $satuanPO = "";
                    $poBPDetail = $this->amPurchaseOrderDetailModel
                        ->select('am_purchase_order_details.*, satuans.kode_satuan')
                        ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
                        ->where('am_purchase_order_id', $poBP['id'])
                        ->where('barang_id', $value['barang1_id'])
                        ->where('spesifikasi_id', $value['barang2_id'])
                        ->findAll();
                    foreach ($poBPDetail as $valuePoBPDetail) {
                        $kurs = $this->kursModel
                            ->where('metadata_id', $valuePoBBImport['currency'])
                            ->where('start_date <=', $valuePoBBImport['po_date'])
                            ->where('end_date >=', $valuePoBBImport['po_date'])
                            ->first();
                        $kursValue = $kurs ? $kurs['nilai_kurs'] : 1;
                        $disc = $valuePoBPDetail['disc'] / 100;
                        $hargaSetelahDisc = $valuePoBPDetail['price'] * $disc;
                        $hargaSatuan = $valuePoBPDetail['price'] - $hargaSetelahDisc;
                        $totalQty += $valuePoBPDetail['qty'];
                        $satuanPO = $valuePoBPDetail['kode_satuan'];
                    }
                    $totalHarga = $totalQty * $hargaSatuan;
                    $value['totalQtyPO'] = $totalQty;
                    $value['totalHargaPO'] = $totalHarga;
                    $value['hargaSatuanPO'] = $hargaSatuan;
                    $value['satuanPO'] = $satuanPO;
                }
                if ($penerimaanBarang) {
                    $totalQty = 0;
                    $totalHarga = 0;
                    $hargaSatuan = 0;
                    $satuanLPB = "";
                    $penerimaanBarangDetail = $this->penerimaanBarangDetailModel
                        ->select('penerimaan_barang_detail.*, satuans.kode_satuan')
                        ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
                        ->where('penerimaan_barang_id', $penerimaanBarang['id'])
                        ->where('barang_id', $value['barang1_id'])
                        ->where('spesifikasi_id', $value['barang2_id'])
                        ->findAll();
                    foreach ($penerimaanBarangDetail as $valuePenerimaanBarangDetail) {
                        $hargaSatuan = ($valuePenerimaanBarangDetail['harga'] + $valuePenerimaanBarangDetail['harga_harian'] + $valuePenerimaanBarangDetail['harga_bulanan']) * $kursValue;
                        $totalQty += $valuePenerimaanBarangDetail['qty'];
                        $satuanLPB = $valuePenerimaanBarangDetail['kode_satuan'];
                    }
                    $totalHarga = $totalQty * $hargaSatuan;
                    $value['totalQtyLPB'] = $totalQty;
                    $value['totalHargaLPB'] = $totalHarga;
                    $value['hargaSatuanLPB'] = $hargaSatuan;
                    $value['satuanLPB'] = $satuanLPB;
                }
            }

            // exit;

            if ($productionResultDataTitle) {
                return response()->setJSON([
                    'data' => $productionResultDataTitle,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }
        }
    }

    public function getRasioBarangDigunakanJadi()
    {
        if (!empty($this->request->getVar('bulan'))) {
            $monthData = $this->request->getVar('bulan');
            list($month, $year) = explode('/', $monthData);
            $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $conditionProduction = [
                'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
                'divisi_id' => $this->request->getVar('department'),
            ];
            $productionResultDataTitle = $this->productionResultModel->getDataProductionResultBahanBakuJadiWithDetail($conditionProduction);

            if ($productionResultDataTitle) {
                return response()->setJSON([
                    'data' => $productionResultDataTitle,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }
        }
    }

    public function getRasioBarangDigunakanPenolong()
    {
        if (!empty($this->request->getVar('bulan'))) {
            $monthData = $this->request->getVar('bulan');
            list($month, $year) = explode('/', $monthData);
            $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $conditionProduction = [
                'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
                'divisi_id' => $this->request->getVar('department'),
            ];
            $productionResultDataTitle = $this->materialRequestsPenolongModel->getDataProductionResultBahanPenolongWithDetail($conditionProduction);
            // $totalQtyAll = 0;
            foreach ($productionResultDataTitle as &$value) {
                $poBBLokal = $this->rmPurchaseOrderModel->where('po_no', $value['stock_dokumen'])->first();
                $poBBImport = $this->rmImportPOModel->where('po_no', $value['stock_dokumen'])->first();
                $poBP = $this->amPurchaseOrderModel->where('po_no', $value['stock_dokumen'])->first();
                $penerimaanBarang = $this->penerimaanBarangModel->where('no_penerimaan_barang', $value['no_dokumen'])->first();
                if ($poBBLokal) {
                    $totalQty = 0;
                    $totalHarga = 0;
                    $hargaSatuan = 0;
                    $satuanPO = "";
                    $poBBLokalDetail = $this->rmPurchaseOrderDetailModel
                        ->select('rm_purchase_order_details.*, satuans.kode_satuan')
                        ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
                        ->where('rm_purchase_order_id', $poBBLokal['id'])
                        ->where('barang1_id', $value['barang1_id'])
                        ->where('barang2_id', $value['barang2_id'])
                        ->findAll();
                    foreach ($poBBLokalDetail as $valuePoBBLokal) {
                        $hargaSatuan = $valuePoBBLokal['general_price'] + $valuePoBBLokal['daily_price'] + $valuePoBBLokal['monthly_price'];
                        $totalQty += $valuePoBBLokal['qty'];
                        $satuanPO = $valuePoBBLokal['kode_satuan'];
                    }
                    $totalHarga = $totalQty * $hargaSatuan;
                    $value['totalQtyPO'] = $totalQty;
                    $value['totalHargaPO'] = $totalHarga;
                    $value['hargaSatuanPO'] = $hargaSatuan;
                    $value['satuanPO'] = $satuanPO;
                }
                if ($poBBImport) {
                    $totalQty = 0;
                    $totalHarga = 0;
                    $hargaSatuan = 0;
                    $satuanPO = "";
                    $poBBImportDetail = $this->rmImportPODetailModel
                        ->select('rm_import_po_details.*, satuans.kode_satuan')
                        ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
                        ->where('rm_import_po_id', $poBBImport['id'])
                        ->where('barang1_id', $value['barang1_id'])
                        ->where('spesifikasi_id', $value['barang2_id'])
                        ->findAll();
                    foreach ($poBBImportDetail as $valuePoBBImport) {
                        $hargaSatuan = $valuePoBBImport['price'] * $valuePoBBImport['disc'] . '%';
                        $totalQty += $valuePoBBImport['qty'];
                        $satuanPO = $valuePoBBImport['kode_satuan'];
                    }
                    $totalHarga = $totalQty * $hargaSatuan;
                    $value['totalQtyPO'] = $totalQty;
                    $value['totalHargaPO'] = $totalHarga;
                    $value['hargaSatuanPO'] = $hargaSatuan;
                    $value['satuanPO'] = $satuanPO;
                }
                if ($poBP) {
                    $totalQty = 0;
                    $totalHarga = 0;
                    $hargaSatuan = 0;
                    $satuanPO = "";
                    $poBPDetail = $this->amPurchaseOrderDetailModel
                        ->select('am_purchase_order_details.*, satuans.kode_satuan')
                        ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
                        ->where('am_purchase_order_id', $poBP['id'])
                        ->where('barang_id', $value['barang1_id'])
                        ->where('spesifikasi_id', $value['barang2_id'])
                        ->findAll();
                    foreach ($poBPDetail as $valuePoBPDetail) {
                        $disc = $valuePoBPDetail['disc'] / 100;
                        $hargaSetelahDisc = $valuePoBPDetail['price'] * $disc;
                        $hargaSatuan = $valuePoBPDetail['price'] - $hargaSetelahDisc;
                        $totalQty += $valuePoBPDetail['qty'];
                        $satuanPO = $valuePoBPDetail['kode_satuan'];
                    }
                    $totalHarga = $totalQty * $hargaSatuan;
                    $value['totalQtyPO'] = $totalQty;
                    $value['totalHargaPO'] = $totalHarga;
                    $value['hargaSatuanPO'] = $hargaSatuan;
                    $value['satuanPO'] = $satuanPO;
                }
            }
            // var_dump($productionResultDataTitle);
            // exit;
            if ($productionResultDataTitle) {
                return response()->setJSON([
                    'data' => $productionResultDataTitle,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }
        }
    }

    public function getCost()
    {
        $monthData = $this->request->getVar('bulan');
        $department = $this->request->getVar('department');
        $id_coa = $this->request->getVar('id_coa');

        list($month, $year) = explode('/', $monthData);
        $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

        $settingCosting = $this->settingCosting->getSettingCosting();

        foreach ($settingCosting as &$valueSetting) {
            $condition = [
                'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
                'id_coa' => $valueSetting['coa_id'],
            ];
            $jurnalData = $this->jurnalUmumModel->getDataJurnalForCosting($condition);
            $valueSetting['jmlhJurnal'] = $jurnalData;
        }
        if ($settingCosting) {
            return response()->setJSON([
                'data' => $settingCosting,
                'token' => csrf_hash(),
                'status' => true
            ]);
        } else {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function getDataJurnal()
    {
        $monthData = $this->request->getVar('bulan');
        $department = $this->request->getVar('department');
        $id_coa = $this->request->getVar('id_coa');

        list($month, $year) = explode('/', $monthData);
        $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

        $condition = [
            'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
            'id_coa' => $id_coa,
        ];
        $jurnalData = $this->jurnalUmumModel->getDataJurnalForCosting($condition);
        if ($jurnalData) {
            return response()->setJSON([
                'data' => $jurnalData,
                'token' => csrf_hash(),
                'status' => true
            ]);
        } else {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function getSaldoAkhir()
    {
        $dataResults = [];
        $data = $this->stockModel->getBarangAndStockConditionWithoutWarehouse(
            "bahan_baku",
            $this->request->getVar('divisi_id')
        );
        foreach ($data as $value) {
            $dataResult = $this->stockDetail2Model->getStockListWithBCDocNoGroup(
                $value['stock_id']
            );
            $stock = $this->stockModel->find($value['stock_id']);
            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $this->barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $this->satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
            } else {
                $kemasan = $this->kemasanModel->find($stock['kemasan_id']);
                $satuan = $this->satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }
            for ($i = 0; $i < count($dataResult); $i++) {
                $bcType = $this->metadataModel->find($dataResult[$i]['bc_id']);

                $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                $dataResult[$i]['satuan'] = $satuan['kode_satuan'];
                $dataResult[$i]['barang'] = strtoupper($barangName);
                $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                $dataResult[$i]['type_barang'] = $stock['tipe_barang'];
                $dataResult[$i]['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                $dataResult[$i]['stok_total'] = ($dataResult[$i]['stok_total']);
            }

            // Merge current dataResult into dataResults
            $dataResults = array_merge($dataResults, $dataResult);
        }
        // var_dump($dataResults);
        return response()->setJSON([
            'data' => $dataResults,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getSaldoAwal()
    {
        $monthData = $this->request->getVar('bulan');
        list($month, $year) = explode('/', $monthData);
        $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $conditionProduction = [
            'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
            'divisi_id' => $this->request->getVar('divisi_id'),
        ];
        $kursValue = 1;
        $productionResultDataTitle = $this->productionResultModel->getDataProductionResultBahanBakuWithDetail($conditionProduction);

        $dataResults = [];
        $dataStockModel = $this->stockModel->getBarangAndStockConditionWithoutWarehouse(
            "bahan_baku",
            $this->request->getVar('divisi_id')
        );

        foreach ($dataStockModel as $value) {
            $dataResult = $this->stockDetail2Model->getStockListWithBCDocNoGroup(
                $value['stock_id']
            );
            $stock = $this->stockModel->find($value['stock_id']);
            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $this->barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $this->satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
            } else {
                $kemasan = $this->kemasanModel->find($stock['kemasan_id']);
                $satuan = $this->satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }
            for ($i = 0; $i < count($dataResult); $i++) {
                $bcType = $this->metadataModel->find($dataResult[$i]['bc_id']);

                $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                $dataResult[$i]['satuan'] = $satuan['kode_satuan'];
                $dataResult[$i]['barang'] = strtoupper($barangName);
                $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                $dataResult[$i]['type_barang'] = $stock['tipe_barang'];
                $dataResult[$i]['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                $dataResult[$i]['stok_total'] = ($dataResult[$i]['stok_total']);

                // Initialize stok_produksi to 0
                $dataResult[$i]['stok_produksi'] = 0;

                foreach ($productionResultDataTitle as $valueProductionResultData) {
                    if ($valueProductionResultData['stock_id'] == $dataResult[$i]['stock_id']) {
                        $dataResult[$i]['stok_produksi'] = $valueProductionResultData['qty'];
                        break; // Exit the loop once a match is found
                    }
                }
            }

            // Merge current dataResult into dataResults
            $dataResults = array_merge($dataResults, $dataResult);
        }
        return response()->setJSON([
            'data' => $dataResults,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function formatHarga($harga)
    {
        // Menghapus "Rp" dan karakter "." dari string harga
        $harga = str_replace(["Rp", "."], "", $harga);

        // Mengonversi string harga menjadi float
        $harga = (float) $harga;

        // Mengubah format angka menjadi string dengan dua desimal
        $harga = number_format($harga, 2, '.', '');

        // Memastikan panjang total menjadi 15 digit dengan memotong atau menambahkan nol di depan jika diperlukan
        $length = strlen($harga);
        if ($length < 15) {
            $harga = str_pad($harga, 15, '0', STR_PAD_LEFT);
        } elseif ($length > 15) {
            $harga = substr($harga, 0, 15);
        }

        return $harga;
    }

    public function load_content()
    {
        $page = $this->request->getGet('page') ?? "";

        $subAkunsModel = $this->subAkunModel->asObject()->findAll();
        $data = [
            'dataDivisi' => $this->divisisModel->getDivisiAccess(),
            'kategoriBarangAkun' => $this->metadataModel->asObject()->where('name', 'kategori_barang_akun')->findAll(),
            "subAkuns" => $subAkunsModel
        ];

        $validPages = [
            'bahan_digunakan',
            'bahan_proses_ulang',
            'saldo_awal',
            'saldo_akhir',
            'saldo_adjustment',
            'bahan_filling',
            'saldo_jual',
            'saldo_trimming',
            'saldo_kopek',
            'bahan_jadi'
        ];

        if (in_array($page, $validPages)) {
            return view('Accounting/rasio/' . $page, $data);
        } else {
            return view('default_view', $data);
        }
    }
}
