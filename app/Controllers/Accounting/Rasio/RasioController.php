<?php

namespace App\Controllers\Accounting\Rasio;

use App\Controllers\BaseController;
use App\Controllers\JasaVendor\BiayaKepiting;
use App\Controllers\JasaVendor\BiayaUdang;
use App\Controllers\JasaVendor\JasaVendorOut;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\AccountDivisisModel;
use App\Models\AdjusmentDetailModel;
use App\Models\AdjusmentModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BiayaKepitingDetailModel;
use App\Models\BiayaKepitingGajiModel;
use App\Models\BiayaKepitingModel;
use App\Models\BiayaUdangDetailModel;
use App\Models\BiayaUdangModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\JasaVendorInModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\JasaVendorOutModel;
use App\Models\JurnalUmumModel;
use App\Models\KemasanModel;
use App\Models\KursModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestPenolongDetailsModel;
use App\Models\MaterialRequestsModel;
use App\Models\MaterialRequestsPenolongModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\ProductionResultDetailModel;
use App\Models\ProductionResultModel;
use App\Models\RasioBahanPenolongModel;
use App\Models\RasioBarangDigunakanAlokasiModel;
use App\Models\RasioBarangDigunakanModel;
use App\Models\RasioBarangJadiModel;
use App\Models\RasioCostModel;
use App\Models\RasioModel;
use App\Models\RasioSaldoAkhirModel;
use App\Models\RasioSaldoAwalModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SatuansModel;
use App\Models\SettingCostingModel;
use App\Models\StockDetail2Model;
use App\Models\StockModel;
use App\Models\StockTutupBukuModel;
use App\Models\TutupBukuModel;

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
    protected $rasioBarangDigunakanAlokasiModel;
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
    protected $jasaVendorInDetailModel;
    protected $stockModel;
    protected $stockDetail2Model;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $satuanModel;
    protected $kemasanModel;
    protected $rasioSaldoAwalModel;
    protected $rasioSaldoAkhirModel;
    protected $adjusmentModel;
    protected $adjusmentDetailModel;
    protected $mutasiModel;
    protected $mutasiDetailModel;
    protected $jasaVendorOutModel;
    protected $jasaVendorOutDetailModel;
    protected $tutupBukuModel;
    protected $stockTutupBukuModel;
    protected $materialRequestsModel;
    protected $materialRequestsDetailsModel;

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
        $this->rasioBarangDigunakanAlokasiModel = new RasioBarangDigunakanAlokasiModel();
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
        $this->jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $this->jasaVendorOutModel = new JasaVendorOutModel();
        $this->jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->satuanModel = new SatuansModel();
        $this->kemasanModel = new KemasanModel();
        $this->rasioSaldoAwalModel = new RasioSaldoAwalModel();
        $this->rasioSaldoAkhirModel = new RasioSaldoAkhirModel();
        $this->adjusmentModel = new AdjusmentModel();
        $this->adjusmentDetailModel = new AdjusmentDetailModel();
        $this->mutasiModel = new MutasiModel();
        $this->mutasiDetailModel = new MutasiDetailModel();
        $this->tutupBukuModel = new TutupBukuModel();
        $this->stockTutupBukuModel = new StockTutupBukuModel();

        $this->materialRequestsModel = new MaterialRequestsModel();
        $this->materialRequestsDetailsModel = new MaterialRequestDetailsModel();
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
            $tanggal_awal_input = $this->request->getVar("tanggal_awal") ? $this->request->getVar("tanggal_awal") : "";
            $tanggal_awal_parts = explode("/", $tanggal_awal_input); // Memisahkan bulan dan tahun
            $tanggal_awal = $tanggal_awal_parts[2] . "-" . $tanggal_awal_parts[1] . "-" . str_pad($tanggal_awal_parts[0], 2, "0", STR_PAD_LEFT);
            $tanggal_akhir_input = $this->request->getVar("tanggal_akhir") ? $this->request->getVar("tanggal_akhir") : "";
            $tanggal_akhir_parts = explode("/", $tanggal_akhir_input); // Memisahkan bulan dan tahun
            $tanggal_akhir = $tanggal_akhir_parts[2] . "-" . $tanggal_akhir_parts[1] . "-" . str_pad($tanggal_akhir_parts[0], 2, "0", STR_PAD_LEFT);

            $cekRasio = $this->rasioModel
                ->where("divisi_id", $this->request->getVar("divisi_id"))
                ->where("tanggal_awal >=", $tanggal_awal)
                ->where("tanggal_akhir <=", $tanggal_akhir)
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
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
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
                "tipe_bahan" => $this->request->getVar('tipe_bahan'),
            ];


            $barang_digunakan = json_decode($this->request->getVar("items_digunakan"));
            $barang_digunakan_pembelian = json_decode($this->request->getVar("items_digunakan_pembelian"));
            $barang_digunakan_alokasi = json_decode($this->request->getVar("items_digunakan_alokasi"));
            $barang_jadi = json_decode($this->request->getVar("items_jadi"));
            $barang_digunakan_material_2 = json_decode($this->request->getVar("items_digunakan_material_2"));
            $saldo_awal = json_decode($this->request->getVar("saldo_awal"));
            $saldo_akhir = json_decode($this->request->getVar("saldo_akhir"));
            $saldo_adjustment = json_decode($this->request->getVar("saldo_adjustment"));
            $saldo_jual = json_decode($this->request->getVar("saldo_jual"));
            $saldo_trimming = json_decode($this->request->getVar("saldo_trimming"));
            $labor_cost = json_decode($this->request->getVar("labor_cost"));
            $overhead_cost = json_decode($this->request->getVar("overhead_cost"));
            $fixed_cost = json_decode($this->request->getVar("fixed_cost"));
            $barang_frozen_jadi = json_decode(($this->request->getVar("item_jadi_frozen")));

            // var_dump($barang_digunakan);
            // die;
            // var_dump($barang_digunakan_alokasi);
            // var_dump($barang_frozen_jadi);
            // var_dump($barang_jadi);
            // exit;
            $id = $this->rasioModel->insert($data);

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
                    //'no_dokumen' => $s->no_dokumen,
                    'stock_dokumen' => $s->stock_dokumen,
                    'type' => "digunakan",
                ]);
            }

            foreach ($barang_digunakan_pembelian as $s) {
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
                    // 'no_dokumen' => $s->no_dokumen,
                    // 'stock_dokumen' => $s->stock_dokumen,
                    'type' => "pembelian",
                ]);
            }

            foreach ($barang_digunakan_alokasi as $s) {
                $this->rasioBarangDigunakanAlokasiModel->insert([
                    'rasio_id' => $id,
                    'barang1_id' => $s->barang1_id,
                    'barang2_id' => $s->barang2_id,
                    'barang_name' => $s->barang_name,
                    'spesifikasi' => $s->spesifikasi,
                    'qty' => $s->totalQty,
                    'harga_total' => $s->totalHarga,
                    'harga_satuan' => $s->hargaSatuan,
                    'satuan' => $s->satuanPO  ?? "-",
                    // 'no_dokumen' => $s->no_dokumen,
                    'stock_dokumen' => $s->stock_dokumen,
                ]);
            }

            if (!empty($barang_jadi)) {
                foreach ($barang_jadi as $s) {
                    $this->rasioBarangJadiModel->insert([
                        'rasio_id'                      => $id,
                        'barang_name'                   => $s->barang_name,
                        'kode_barang'                   => $s->kode_barang,
                        'spesifikasi'                   => $s->spesifikasi,
                        'production_result_detail_id'   => $s->production_result_detail_id,
                        'production_result_id'          => $s->production_result_id,
                        'barang1_id'                    => $s->barang1_id,
                        'barang2_id'                    => $s->barang2_id,
                        'bc_id'                         => $s->bc_id,
                        'stock_id'                      => $s->stock_id,
                        'no_aju'                        => $s->no_aju,
                        'stock_dokumen'                 => $s->stock_dokumen,
                        'qty_barang'                    => $s->qty,
                        'qty2'                          => $s->qty2,
                        'qty_isi'                       => $s->qty_isi,
                        'rasio_barang'                  => $s->rasio,
                        'harga_barang'                  => $s->harga,
                        'kode_satuan'                   => $s->kode_satuan,
                        'tipe_bahan'                    => $s->tipe_bahan,
                        'hasilWithPersentase'           => $s->hasilWithPersentase,
                        'banyakData'                    => $s->banyakData,
                    ]);
                }
            } else {
                foreach ($barang_frozen_jadi as $f) {
                    foreach ($f as $i) {
                        $this->rasioBarangJadiModel->insert([
                            'rasio_id' => $id,
                            'barang_name'                   => $i->barang_name,
                            'kode_barang'                   => $i->kode_barang,
                            'spesifikasi'                   => $i->spesifikasi,
                            'production_result_detail_id'   => $i->production_result_detail_id,
                            'production_result_id'          => $i->production_result_id,
                            'barang1_id'                    => $i->barang1_id,
                            'barang2_id'                    => $i->barang2_id,
                            'bc_id'                         => $i->bc_id,
                            'stock_id'                      => $i->stock_id,
                            'no_aju'                        => $i->no_aju,
                            'stock_dokumen'                 => $i->stock_dokumen,
                            'qty_barang'                    => $i->qty,
                            'qty2'                          => $i->qty2,
                            'qty_isi'                       => $i->qty_isi,
                            'rasio_barang'                  => $i->rasio,
                            'harga_barang'                  => $i->harga_satuan,
                            'kode_satuan'                   => $i->kode_satuan,
                            'tipe_bahan'                    => $i->tipe_bahan,
                            'satuan_id'                     => $i->satuan_id,
                            'hasilWithPersentase'           => $i->hasilWithPersentase,
                            'banyakData'                    => $i->banyakData,
                        ]);
                    }
                }
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

            foreach ($saldo_awal as $s) {
                $this->rasioSaldoAwalModel->insert([
                    'rasio_id' => $id,
                    'barang1_id' => $s->barang1_id,
                    'barang2_id' => $s->barang2_id,
                    'stock_id' => $s->stock_id,
                    'harga_umum' => $s->harga_umum ?? 0,
                    'harga_harian' => $s->harga_harian ?? 0,
                    'harga_bulanan' => $s->harga_bulanan ?? 0,
                    'stok_total' => $s->stok_total,
                    'satuan' => $s->satuan,
                    'barang' => $s->barang,
                ]);
            }

            foreach ($saldo_akhir as $s) {
                $this->rasioSaldoAkhirModel->insert([
                    'rasio_id' => $id,
                    'barang1_id' => $s->barang1_id,
                    'barang2_id' => $s->barang2_id,
                    'supplier_name' => $s->supplier_name,
                    'bc_id' => $s->bc_id,
                    'stock_detail_id' => $s->stock_detail_id,
                    'no_aju' => $s->no_aju,
                    'stock_id' => $s->stock_id,
                    'stock_dokumen' => $s->stock_dokumen,
                    'no_dokumen_2' => $s->no_dokumen_2,
                    'supplier_id' => $s->supplier_id,
                    'harga_umum' => $s->harga_umum ?? 0,
                    'harga_harian' => $s->harga_harian ?? 0,
                    'harga_bulanan' => $s->harga_bulanan ?? 0,
                    'no_po' => $s->no_po,
                    'no_dokumen_1' => $s->no_dokumen_1,
                    'stock_date' => $s->stock_date,
                    'sumber' => $s->sumber,
                    'stok_total' => $s->stok_total,
                    'bc_type' => $s->bc_type,
                    'satuan' => $s->satuan,
                    'barang' => $s->barang,
                    'type_barang' => $s->type_barang,
                    'type_barang_text' => $s->type_barang_text,
                    'stok_produksi' => $s->stok_produksi ?? 0,
                ]);
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
        $rasioBarangDigunakanAlokasiModel = $this->rasioBarangDigunakanAlokasiModel->asObject()->where('rasio_id', $id)->findAll();
        $rasioBarangDigunakanModel = $this->rasioBarangDigunakanModel->asObject()->where('rasio_id', $id)->where('type', 'digunakan')->findAll();
        $rasioBarangPembelianModel = $this->rasioBarangDigunakanModel->asObject()->where('rasio_id', $id)->where('type', 'pembelian')->findAll();
        $rasioSaldoAwalModel = $this->rasioSaldoAwalModel->asObject()->where('rasio_id', $id)->findAll();
        $rasioSaldoAkhirModel = $this->rasioSaldoAkhirModel->asObject()->where('rasio_id', $id)->findAll();
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
            'kategoriBarangAkun' => $this->metadataModel->asObject()->where('name', 'kategori_barang_akun')->findAll(),
            "rasio" => $rasioModel,
            "rasioBarangDigunakanAlokasi" => $rasioBarangDigunakanAlokasiModel,
            "rasioBarangDigunakan" => $rasioBarangDigunakanModel,
            "rasioBarangPembelian" => $rasioBarangPembelianModel,
            "rasioBarangJadi" => $rasioBarangJadiModel,
            "rasioSaldoAwalModel" => $rasioSaldoAwalModel,
            "rasioSaldoAkhirModel" => $rasioSaldoAkhirModel,
            // "rasioBarangPenolong" => $rasioBarangPenolongModel,
            // "rasioCost" => $rasioCostModel,
        ];
        return view('Accounting/rasio/form', $data);
    }

    public function allRasio()
    {
        $tanggal_awal_input = $this->request->getVar("tanggal_awal") ? $this->request->getVar("tanggal_awal") : "";
        $tanggal_akhir_input = $this->request->getVar("tanggal_akhir") ? $this->request->getVar("tanggal_akhir") : "";

        if ($tanggal_awal_input != "") {
            $tanggal_awal_parts = explode("/", $tanggal_awal_input); // Memisahkan bulan dan tahun
            $tanggal_awal = $tanggal_awal_parts[2] . "-" . $tanggal_awal_parts[1] . "-" . str_pad($tanggal_awal_parts[0], 2, "0", STR_PAD_LEFT);
        }

        if ($tanggal_akhir_input != "") {
            $tanggal_akhir_parts = explode("/", $tanggal_akhir_input); // Memisahkan bulan dan tahun
            $tanggal_akhir = $tanggal_akhir_parts[2] . "-" . $tanggal_akhir_parts[1] . "-" . str_pad($tanggal_akhir_parts[0], 2, "0", STR_PAD_LEFT);
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
            "tanggal_awal"  => $tanggal_awal_input != "" ? $tanggal_awal : "",
            "tanggal_akhir" => $tanggal_akhir_input != "" ? $tanggal_akhir : "",
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
                "tanggal_awal"          => date('d/m/Y', strtotime($data['tanggal_awal'])),
                "tanggal_akhir"         => date('d/m/Y', strtotime($data['tanggal_akhir'])),
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

    public function getRawMaterialI()
    {
        if (!empty($this->request->getVar('tanggal_awal')) && !empty($this->request->getVar('tanggal_akhir'))) {
            $dataBahanDigunakanPO = [];
            $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
            $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));
            $divisiID = $this->request->getVar('department');
            $kategoriID = $this->request->getVar('kategori');

            $conditionProduction = [
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'divisi_id' => $divisiID,
                'kategori_id' => $kategoriID,
                'company_id' => $this->this_company_id,
            ];

            $kursValue = 1;
            // definisi untuk bahan digunakan
            $poBBLokal = $this->rmPurchaseOrderModel->getPOBBCondition($divisiID,  $tanggal_awal, $tanggal_akhir, $kategoriID, $this->this_company_id);
            $poBBImport = $this->rmImportPOModel->getPOBBCondition($divisiID,  $tanggal_awal, $tanggal_akhir, $kategoriID, $this->this_company_id);
            $dataBahanDigunakanPO = array_merge($dataBahanDigunakanPO, $poBBLokal, $poBBImport);
            // var_dump($poBBLokal);
            // var_dump($poBBImport);
            // var_dump($dataBahanDigunakanPO);

            // var_dump($this->this_company_id);
            // var_dump($divisiID);
            // exit;
            $dataProduksiBahanDigunakan = $this->productionResultModel->getDataProductionResultBahanBakuWithDetail($conditionProduction);
            // awal fungsi untuk bahan digunakan
            foreach ($dataBahanDigunakanPO as &$value) {
                // Use object properties instead of array syntax
                $poNo = $value->po_no;
                $barang1_id = $value->barang1_id;
                $barang2_id = $value->barang2_id;
                $totalQtyPO = floatval($value->qtyPO);
                $price1 = isset($value->price1) ? floatval($value->price1) : 0;
                $price2 = isset($value->price2) ? floatval($value->price2) : 0;
                $price3 = isset($value->price3) ? floatval($value->price3) : 0;
                $avgprice = isset($value->avg_price_per_qty) ? floatval($value->avg_price_per_qty) : 0;
                $discount = isset($value->disc) ? floatval($value->disc) : 0;
                $hargaSatuan = $price1 + $price2 + $price3;
                $hargaDiscount = $hargaSatuan * ($discount / 100);
                $additional_cost = isset($value->additional_cost) ? floatval($value->additional_cost) : 0;
                $hargaSatuanPO = $avgprice + $additional_cost - $hargaDiscount;
                $totalHargaPO = $hargaSatuanPO * $totalQtyPO;

                $value->totalQtyPO = $totalQtyPO;
                $value->totalHargaPO = $totalHargaPO;
                $value->hargaSatuanPO = $hargaSatuanPO;
                $value->satuanPO =  $value->satuanName;
                $value->barang_name =  $value->barangName;
                $value->spesifikasi =  $value->spekName;


                $parsePoNo = explode(',', $poNo);
                $totalQty = 0;
                $totalHarga = 0;
                $hargaSatuan = 0;
                $satuanLPB = "";
                foreach ($parsePoNo as $key => $valuePoNo) {
                    $penerimaanBarang = $this->penerimaanBarangModel
                        ->where("JSON_CONTAINS(multiple_po_no, '\"" . $valuePoNo . "\"')")
                        ->where("company_id", $this->this_company_id)
                        ->first();

                    // var_dump($penerimaanBarang);

                    if ($penerimaanBarang) {
                        $penerimaanBarangDetail = $this->penerimaanBarangDetailModel
                            ->select('penerimaan_barang_detail.*, SUM(penerimaan_barang_detail.harga) AS harga, SUM(penerimaan_barang_detail.harga_harian) AS harga_harian, SUM(penerimaan_barang_detail.harga_bulanan) AS harga_bulanan, SUM(penerimaan_barang_detail.qty) AS qty, satuans.kode_satuan')
                            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
                            ->where('penerimaan_barang_id', $penerimaanBarang['id'])
                            ->where('penerimaan_barang_detail.deletedAt', null)
                            ->where('barang_id', $barang1_id)
                            ->where('spesifikasi_id', $barang2_id)
                            ->groupBy('barang_id, spesifikasi_id')
                            ->findAll();
                        // var_dump($penerimaanBarangDetail);
                        foreach ($penerimaanBarangDetail as $valuePenerimaanBarangDetail) {
                            $hargaSatuan += (floatval($valuePenerimaanBarangDetail['harga']) + floatval($valuePenerimaanBarangDetail['harga_harian']) + floatval($valuePenerimaanBarangDetail['harga_bulanan'])) * $kursValue;
                            $totalQty += floatval($valuePenerimaanBarangDetail['qty']);
                            $satuanLPB = $valuePenerimaanBarangDetail['kode_satuan'];
                        }
                    } else {
                        $hargaSatuan += floatval(0);
                        $totalQty += floatval(0);
                        $satuanLPB = $value->satuanPO;
                    }
                }
                $totalHarga = $totalQty * $hargaSatuan;
                $value->totalQtyLPB = $totalQty;
                $value->totalHargaLPB = $hargaSatuan;
                $value->hargaSatuanLPB = $hargaSatuan != 0 && $totalQty != 0 ? $hargaSatuan / $totalQty : 0;
                $value->satuanLPB = $satuanLPB;
            }

            foreach ($dataProduksiBahanDigunakan as &$value) {
                $stockDokumenResult = explode(',', $value['stock_dokumen']);

                foreach ($stockDokumenResult as $key => $stockDokumen) {
                    if (preg_match('/\((PO\/[^)]+)\)/', $stockDokumen, $matches)) {
                        // Jika ada teks dalam kurung, gunakan yang di dalam kurung
                        $poNo = $matches[1];
                        $jasaVendorNo = trim(explode(' (', $stockDokumen)[0]);
                    } else {
                        // Jika tidak ada kurung, gunakan nilai langsung
                        $poNo = $stockDokumen;
                        $jasaVendorNo = 0;
                    }

                    $poBBLokal = $this->rmPurchaseOrderModel->where('po_no', $poNo)->first();
                    $poBBImport = $this->rmImportPOModel->where('po_no', $poNo)->first();
                    $poBP = $this->amPurchaseOrderModel->where('po_no', $poNo)->first();
                    $jasaVendorIn = $this->jasaVendorInModel->where('no_penerimaan_surat_jalan', $jasaVendorNo)->first();

                    if ($jasaVendorNo != 0) {
                        $jasaVendorInDetailCheck = $this->jasaVendorInDetailModel
                            ->join('stock', "stock.id = jasa_vendor_in_detail.stock_in_id")
                            ->where('jasa_vendor_in_id', $jasaVendorIn['id'])
                            ->where('stock.barang1_id', $value['barang1_id'])
                            ->where('stock.barang2_id', $value['barang2_id'])
                            ->where('jasa_vendor_in_detail.no_aju_in', $value['no_aju'])
                            ->findAll();
                        // var_dump($jasaVendorInDetailCheck);
                        foreach ($jasaVendorInDetailCheck as $valueJasaVendorIn) {
                            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel
                                ->join('stock', "stock.id = jasa_vendor_out_detail.stock_out_id")
                                ->where('jasa_vendor_out_detail.id', $valueJasaVendorIn['jasa_vendor_out_detail_id'])
                                ->where('jasa_vendor_out_detail.no_aju_out', $value['no_aju'])
                                ->findAll();
                            foreach ($jasaVendorOutDetail as $valueJasaVendorOut) {
                                $totalQty = 0;
                                $totalHarga = 0;
                                $hargaSatuan = 0;
                                $satuanPO = "";
                                if ($poBBLokal) {
                                    $poBBLokalDetail = $this->rmPurchaseOrderDetailModel
                                        ->select('rm_purchase_order_details.*, satuans.kode_satuan')
                                        ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
                                        ->where('rm_purchase_order_id', $poBBLokal['id'])
                                        ->where('barang1_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('barang2_id', $valueJasaVendorOut['barang2_id'])
                                        ->findAll();
                                    foreach ($poBBLokalDetail as $valuePoBBLokal) {
                                        $kursValue = 1;
                                        $hargaSatuan = $valuePoBBLokal['general_price'] + $valuePoBBLokal['daily_price'] + $valuePoBBLokal['monthly_price'];
                                        $totalQty += $valuePoBBLokal['qty'];
                                        $satuanPO = $valuePoBBLokal['kode_satuan'];
                                    }
                                    $totalHarga = $value['qty'] * $hargaSatuan;
                                }

                                if ($poBBImport) {
                                    $poBBImportDetail = $this->rmImportPODetailModel
                                        ->select('rm_import_po_details.*, satuans.kode_satuan, rm_import_pos.currency, rm_import_pos.po_date')
                                        ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
                                        ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
                                        ->where('rm_import_po_id', $poBBImport['id'])
                                        ->where('barang_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('spesifikasi_id', $valueJasaVendorOut['barang2_id'])
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
                                }
                                if ($poBP) {
                                    $poBPDetail = $this->amPurchaseOrderDetailModel
                                        ->select('am_purchase_order_details.*, satuans.kode_satuan')
                                        ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
                                        ->where('am_purchase_order_id', $poBP['id'])
                                        ->where('barang_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('spesifikasi_id', $valueJasaVendorOut['barang2_id'])
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
                                }
                                $value['totalQtyPO'] = $value['qty'];
                                $value['totalHargaPO'] = $totalHarga;
                                $value['hargaSatuanPO'] = $hargaSatuan;
                                $value['satuanPO'] = $satuanPO;
                            }
                        }
                    } else {
                        $totalQty = 0;
                        $totalHarga = 0;
                        $hargaSatuan = 0;
                        $satuanPO = "";
                        if ($poBBLokal) {
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
                            $totalHarga = $value['qty'] * $hargaSatuan;
                        }

                        if ($poBBImport) {
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
                        }
                        if ($poBP) {
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
                        }
                        $value['totalQtyPO'] = $value['qty'];
                        $value['totalHargaPO'] = $totalHarga;
                        $value['hargaSatuanPO'] = $hargaSatuan;
                        $value['satuanPO'] = $satuanPO;
                    }
                }
            }
            // akhir fungsi untuk bahan digunakan

            // definisi bahan digunakan proses ulang
            // awal fungsi untuk bahan digunakan proses ulang
            $dataProduksiBahanDigunakanProsesUlang = $this->productionResultModel->getDataProductionResultBahanBakuJadiWithDetail($conditionProduction);
            // akhir fungsi untuk bahan digunakan proses ulang

            // definisi bahan Filling dan Ditapak
            // awal fungsi untuk bahan Filling dan Ditapak
            // $dataProduksiBahanDigunakanProsesUlang = $this->productionResultModel->getDataProductionResultBahanBakuJadiWithDetail($conditionProduction);
            // akhir fungsi untuk bahan Filling dan Ditapak

            // definisi Saldo Awal
            // awal fungsi untuk saldo Awal
            $dataSaldoAwal = [];
            $dataBahanBakuDigunakanSaldoAwal = $this->stockTutupBukuModel->getStockTutupBukuWithAddCondition($conditionProduction);
            $dataRequest = $this->materialRequestsDetailsModel->getMaterialRequestForRasio($conditionProduction);
            foreach ($dataBahanBakuDigunakanSaldoAwal as $key => $valueSaldoAwal) {
                foreach ($dataRequest as $valueRequest) {
                    if ($valueSaldoAwal['barang1_id'] == $valueRequest['barang1_id'] && $valueSaldoAwal['barang2_id'] == $valueRequest['barang2_id'] && $valueSaldoAwal['stock_id'] == $valueRequest['stock_tujuan_id']) {
                        $dataSaldoAwal[] = $valueSaldoAwal;
                    }
                }
            }
            // akhir fungsi untuk saldo Awal

            // definisi saldo akhir
            // awal fungsi untuk saldo akhir
            $dataSaldoAkhir = [];
            $dataSaldoAkhirDone = [];
            $dataSaldoAkhirModel = $this->stockModel->getBarangAndStockConditionWithoutWarehouse(
                "bahan_baku",
                $divisiID
            );
            $dataRequest = $this->materialRequestsDetailsModel->getMaterialRequestForRasio($conditionProduction);
            foreach ($dataSaldoAkhirModel as $value) {
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
                $dataSaldoAkhir = array_merge($dataSaldoAkhir, $dataResult);
            }
            foreach ($dataSaldoAkhir as $value) {
                foreach ($dataRequest as $valueRequest) {
                    // var_dump($value);
                    if ($value['barang1_id'] == $valueRequest['barang1_id'] && $value['barang2_id'] == $valueRequest['barang2_id']) {
                        $dataSaldoAkhirDone[] = $value;
                        // var_dump($value);
                    }
                }
            }
            // akhir fungsi untuk saldo akhir

            // definisi saldo adjustment
            // awal fungsi untuk saldo adjustment
            $dataSaldoAdjusment = $this->adjusmentModel
                ->select('adjusment.*, adjusment_detail.*')
                ->join('adjusment_detail', 'adjusment_detail.adjusment_id = adjusment.id', 'left')
                ->where('adjusment.tanggal >=', $tanggal_awal)
                ->where('adjusment.tanggal <=', $tanggal_akhir)
                ->where('adjusment.divisi_id', $divisiID)
                ->where('adjusment.company_id', $this->this_company_id)
                ->where('adjusment.status_posting', "1")
                ->where('adjusment.deletedAt', null)
                ->where('adjusment_detail.deletedAt', null)
                ->findAll();

            foreach ($dataSaldoAdjusment as &$value) {
                $stockDetail2Model = $this->stockDetail2Model
                    ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
                    ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
                    ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                    ->where('stock_dokumen', $value['stock_dokumen'])
                    ->where('bc_id', $value['bc_id'])
                    ->where('no_aju', $value['no_aju'])
                    ->where('barang1_id', $value['barang1_id'])
                    ->where('barang2_id', $value['barang2_id'])
                    ->first();
                $value['harga_umum'] = $stockDetail2Model['harga_umum'];
                $value['harga_harian'] = $stockDetail2Model['harga_harian'];
                $value['harga_bulanan'] = $stockDetail2Model['harga_bulanan'];
                $value['barang'] = $stockDetail2Model['barang_name'] . ' - ' . $stockDetail2Model['spesifikasi'];
                $value['satuan'] = $stockDetail2Model['kode_satuan'];
            }
            // akhir fungsi untuk saldo adjustment

            // definisi saldo jual
            // awal fungsi untuk saldo jual
            $dataSaldoMutasi = $this->mutasiModel
                ->select('mutasi.*, mutasi_detail.*')
                ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
                ->where('mutasi.tanggal >=', $tanggal_awal)
                ->where('mutasi.tanggal <=', $tanggal_akhir)
                ->where('mutasi.divisi_asal_id', $divisiID)
                ->where('mutasi.company_id', $this->this_company_id)
                ->where('mutasi.status_posting', "1")
                ->where('mutasi.deletedAt', null)
                ->where('mutasi_detail.deletedAt', null)
                ->findAll();

            foreach ($dataSaldoMutasi as &$value) {
                $stockDetail2Model = $this->stockDetail2Model
                    ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
                    ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
                    ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                    ->where('stock_details2.stock_dokumen', $value['stock_dokumen'])
                    ->where('stock_details2.bc_id', $value['bc_id'])
                    ->where('stock_details2.no_aju', $value['no_aju'])
                    ->where('stock_details2.stock_id', $value['stock_id'])
                    ->first();
                $value['harga_umum'] = $stockDetail2Model['harga_umum'];
                $value['harga_harian'] = $stockDetail2Model['harga_harian'];
                $value['harga_bulanan'] = $stockDetail2Model['harga_bulanan'];
                $value['barang'] = $stockDetail2Model['barang_name'] . ' - ' . $stockDetail2Model['spesifikasi'];
                $value['satuan'] = $stockDetail2Model['kode_satuan'];
            }

            // definisi saldo kopek
            // awal fungsi untuk saldo kopek
            $dataProduksiBahanDigunakanKopek = $this->productionResultModel->getDataProductionResultBahanBakuWithDetail($conditionProduction);
            foreach ($dataProduksiBahanDigunakanKopek as &$value) {
                $stockDokumenResult = explode(',', $value['stock_dokumen']);

                foreach ($stockDokumenResult as $key => $stockDokumen) {
                    if (preg_match('/\((PO\/[^)]+)\)/', $stockDokumen, $matches)) {
                        // Jika ada teks dalam kurung, gunakan yang di dalam kurung
                        $poNo = $matches[1];
                        $jasaVendorNo = trim(explode(' (', $stockDokumen)[0]);
                    }

                    $jasaVendorIn = $this->jasaVendorInModel->where('no_penerimaan_surat_jalan', $jasaVendorNo)->first();

                    if ($jasaVendorNo != 0) {
                        $poBBLokal = $this->rmPurchaseOrderModel->where('po_no', $poNo)->first();
                        $poBBImport = $this->rmImportPOModel->where('po_no', $poNo)->first();
                        $poBP = $this->amPurchaseOrderModel->where('po_no', $poNo)->first();
                        $jasaVendorInDetailCheck = $this->jasaVendorInDetailModel
                            ->join('stock', "stock.id = jasa_vendor_in_detail.stock_in_id")
                            ->where('jasa_vendor_in_id', $jasaVendorIn['id'])
                            ->where('stock.barang1_id', $value['barang1_id'])
                            ->where('stock.barang2_id', $value['barang2_id'])
                            ->where('jasa_vendor_in_detail.no_aju_in', $value['no_aju'])
                            ->findAll();
                        // var_dump($jasaVendorInDetailCheck);
                        foreach ($jasaVendorInDetailCheck as $valueJasaVendorIn) {
                            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel
                                ->join('stock', "stock.id = jasa_vendor_out_detail.stock_out_id")
                                ->where('jasa_vendor_out_detail.id', $valueJasaVendorIn['jasa_vendor_out_detail_id'])
                                ->where('jasa_vendor_out_detail.no_aju_out', $value['no_aju'])
                                ->findAll();
                            foreach ($jasaVendorOutDetail as $valueJasaVendorOut) {
                                $totalQty = 0;
                                $totalHarga = 0;
                                $hargaSatuan = 0;
                                $satuanPO = "";
                                if ($poBBLokal) {
                                    $poBBLokalDetail = $this->rmPurchaseOrderDetailModel
                                        ->select('rm_purchase_order_details.*, satuans.kode_satuan')
                                        ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
                                        ->where('rm_purchase_order_id', $poBBLokal['id'])
                                        ->where('barang1_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('barang2_id', $valueJasaVendorOut['barang2_id'])
                                        ->findAll();
                                    foreach ($poBBLokalDetail as $valuePoBBLokal) {
                                        $kursValue = 1;
                                        $hargaSatuan = $valuePoBBLokal['general_price'] + $valuePoBBLokal['daily_price'] + $valuePoBBLokal['monthly_price'];
                                        $totalQty += $valuePoBBLokal['qty'];
                                        $satuanPO = $valuePoBBLokal['kode_satuan'];
                                    }
                                    $totalHarga = $value['qty'] * $hargaSatuan;
                                }

                                if ($poBBImport) {
                                    $poBBImportDetail = $this->rmImportPODetailModel
                                        ->select('rm_import_po_details.*, satuans.kode_satuan, rm_import_pos.currency, rm_import_pos.po_date')
                                        ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
                                        ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
                                        ->where('rm_import_po_id', $poBBImport['id'])
                                        ->where('barang_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('spesifikasi_id', $valueJasaVendorOut['barang2_id'])
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
                                }
                                if ($poBP) {
                                    $poBPDetail = $this->amPurchaseOrderDetailModel
                                        ->select('am_purchase_order_details.*, satuans.kode_satuan')
                                        ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
                                        ->where('am_purchase_order_id', $poBP['id'])
                                        ->where('barang_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('spesifikasi_id', $valueJasaVendorOut['barang2_id'])
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
                                }
                                $value['totalQtyPO'] = $value['qty'];
                                $value['totalHargaPO'] = $totalHarga;
                                $value['hargaSatuanPO'] = $hargaSatuan;
                                $value['satuanPO'] = $satuanPO;
                            }
                        }
                    }
                }
            }
            // akhir fungsi untuk saldo kopek

            // definisi bahan jadi
            // awal fungsi untuk bahan jadi
            $productionResultDataTitleJadi = $this->productionResultModel->getDataProductionResultWithDetail($conditionProduction);
            $productionResultDataBBTitle = $this->productionResultModel->getDataBBForResult($conditionProduction);

            $totalSumQtyBB = 0;
            foreach ($productionResultDataTitleJadi as &$value) {
                $totalQtyAll = 0;
                $productionResultIds = explode(',', $value['production_result_id']);
                $productionResultQty = explode(',', $value['qtyProduksi']);

                foreach ($productionResultIds as $key => $productionResultId) {
                    $sumQtyBB = 0;
                    foreach ($productionResultDataBBTitle as $valueBB) {
                        if ($productionResultId == $valueBB['production_result_id']) {
                            $sumQtyBB += $valueBB['qty'];
                        }
                    }

                    foreach ($productionResultDataTitleJadi as &$valueProduction) {
                        $valueProductionResultIds = explode(',', $valueProduction['production_result_id']);

                        foreach ($valueProductionResultIds as $key => $valueProductionResultId) {
                            if ($productionResultId == $valueProductionResultId && $value['barang1_id'] == $valueProduction['barang1_id']) {
                                $totalQtyAll += $valueProduction['qtyTotal'];
                            }
                        }
                    }
                }
                $persentasePerBarangJadi = floatval($value['qtyTotal']) / floatval($totalQtyAll);

                $totalSumQtyBB = $sumQtyBB;
                $hasilWithPersentase = round($persentasePerBarangJadi * $totalSumQtyBB, 2);
                // var_dump($value['qtyTotal']);
                // var_dump($totalQtyAll);
                // var_dump($persentasePerBarangJadi);
                // var_dump($totalSumQtyBB);
                // var_dump($hasilWithPersentase);
                $value['totalQtyAll'] = $totalQtyAll;
                $value['hasilWithPersentase'] = $hasilWithPersentase;
            }
            // akhir fungsi untuk bahan jadi

            // var_dump($productionResultDataTitleJadi);
            // exit;


            if ($dataProduksiBahanDigunakan) {
                return response()->setJSON([
                    'dataProduksiBahanDigunakan'            => $dataProduksiBahanDigunakan,
                    'dataBahanDigunakanPO'                  => $dataBahanDigunakanPO,
                    'dataProduksiBahanDigunakanProsesUlang' => $dataProduksiBahanDigunakanProsesUlang,
                    'dataSaldoAwal'                         => $dataSaldoAwal,
                    'dataSaldoAkhir'                        => $dataSaldoAkhirDone,
                    'dataSaldoAdjusment'                    => $dataSaldoAdjusment,
                    'dataSaldoMutasi'                       => $dataSaldoMutasi,
                    'dataSaldoKopek'                        => $dataProduksiBahanDigunakanKopek,
                    'dataBarangJadi'                        => $productionResultDataTitleJadi,
                    'token'                                 => csrf_hash(),
                    'status'                                => true
                ]);
            } else {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }
        }
    }

    public function getRasioBarangJadi()
    {
        if (!empty($this->request->getVar('tanggal_awal')) && !empty($this->request->getVar('tanggal_akhir'))) {
            // Ambil input tanggal awal dan akhir
            $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
            $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));

            // Kondisi produksi berdasarkan input
            $conditionProduction = [
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'divisi_id' => $this->request->getVar('department'),
                'kategori_id' => $this->request->getVar('kategori'),
            ];

            // Ambil data hasil produksi dan data BB untuk hasil produksi
            $productionResultDataTitle = $this->productionResultModel->getDataProductionResultWithDetail($conditionProduction);
            $productionResultDataBBTitle = $this->productionResultModel->getDataBBForResult($conditionProduction);

            $totalQtyAll = 0;
            foreach ($productionResultDataTitle as &$value) {
                $totalQtyAll += $value['qtyTotal'];
            }

            $totalSumQtyBB = 0;
            foreach ($productionResultDataTitle as &$value) {
                $productionResultIds = explode(',', $value['production_result_id']);
                $productionResultQty = explode(',', $value['qtyProduksi']);

                $oldBarang1ID = "";
                $oldBarang2ID = "";
                $persentasePerBarangJadi = round(floatval($value['qtyTotal']) / floatval($totalQtyAll), 2);

                foreach ($productionResultIds as $key => $productionResultId) {
                    $sumQtyBB = 0;
                    $oldBarang1ID = $value['barang1_id'];
                    $oldBarang2ID = $value['barang2_id'];
                    foreach ($productionResultDataBBTitle as $valueBB) {
                        $sumQtyBB += $valueBB['qty'];
                    }
                }
                $totalSumQtyBB = $sumQtyBB;
                $hasilWithPersentase = round($persentasePerBarangJadi * $totalSumQtyBB, 2);
                // var_dump(floatval($value['qty']));
                // var_dump(floatval($totalQtyAll));
                // var_dump($persentasePerBarangJadi);
                // var_dump($hasilWithPersentase);
                // var_dump($totalSumQtyBB);
                $value['totalQtyAll'] = $totalQtyAll;
                $value['hasilWithPersentase'] = $hasilWithPersentase;
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

    public function getRasioBarangDigunakan()
    {
        if (!empty($this->request->getVar('tanggal_awal')) && !empty($this->request->getVar('tanggal_akhir'))) {
            // if (!empty($this->request->getVar('bulan'))) {
            $dataResultPO = [];
            $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
            $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));

            // $monthData = $this->request->getVar('bulan');
            // list($month, $year) = explode('/', $monthData);

            // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $divisiID = $this->request->getVar('department');
            $kategoriID = $this->request->getVar('kategori');

            $conditionProduction = [
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'divisi_id' => $this->request->getVar('department'),
                'kategori_id' => $this->request->getVar('kategori'),
            ];
            $kursValue = 1;
            $poBBLokal = $this->rmPurchaseOrderModel->getPOBBCondition($divisiID,  $tanggal_awal, $tanggal_akhir, $kategoriID, $this->this_company_id);
            $poBBImport = $this->rmImportPOModel->getPOBBCondition($divisiID,  $tanggal_awal, $tanggal_akhir, $kategoriID, $this->this_company_id);

            $dataResultPO = array_merge($dataResultPO, $poBBLokal, $poBBImport);
            $productionResultDataTitle = $this->productionResultModel->getDataProductionResultBahanBakuWithDetail($conditionProduction);

            foreach ($dataResultPO as &$value) {
                // Use object properties instead of array syntax
                $poNo = $value->po_no;
                $barang1_id = $value->barang1_id;
                $barang2_id = $value->barang2_id;
                $totalQtyPO = floatval($value->qtyPO);
                $price1 = isset($value->price1) ? floatval($value->price1) : 0;
                $price2 = isset($value->price2) ? floatval($value->price2) : 0;
                $price3 = isset($value->price3) ? floatval($value->price3) : 0;
                $avgprice = isset($value->avg_price_per_qty) ? floatval($value->avg_price_per_qty) : 0;
                $discount = isset($value->disc) ? floatval($value->disc) : 0;
                $hargaSatuan = $price1 + $price2 + $price3;
                $hargaDiscount = $hargaSatuan * ($discount / 100);
                $additional_cost = isset($value->additional_cost) ? floatval($value->additional_cost) : 0;
                $hargaSatuanPO = $avgprice + $additional_cost - $hargaDiscount;
                $totalHargaPO = $hargaSatuanPO * $totalQtyPO;

                $value->totalQtyPO = $totalQtyPO;
                $value->totalHargaPO = $totalHargaPO;
                $value->hargaSatuanPO = $hargaSatuanPO;
                $value->satuanPO =  $value->satuanName;
                $value->barang_name =  $value->barangName;
                $value->spesifikasi =  $value->spekName;

                // var_dump(explode(',', $poNo));

                $parsePoNo = explode(',', $poNo);

                $totalQty = 0;
                $totalHarga = 0;
                $hargaSatuan = 0;
                $satuanLPB = "";
                foreach ($parsePoNo as $key => $valuePoNo) {
                    $penerimaanBarang = $this->penerimaanBarangModel
                        ->where("JSON_CONTAINS(multiple_po_no, '\"" . $valuePoNo . "\"')")
                        ->first();

                    if ($penerimaanBarang) {
                        $penerimaanBarangDetail = $this->penerimaanBarangDetailModel
                            ->select('penerimaan_barang_detail.*, SUM(penerimaan_barang_detail.harga) AS harga, SUM(penerimaan_barang_detail.harga_harian) AS harga_harian, SUM(penerimaan_barang_detail.harga_bulanan) AS harga_bulanan, SUM(penerimaan_barang_detail.qty) AS qty, satuans.kode_satuan')
                            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
                            ->where('penerimaan_barang_id', $penerimaanBarang['id'])
                            ->where('barang_id', $barang1_id)
                            ->where('spesifikasi_id', $barang2_id)
                            ->groupBy('barang_id, spesifikasi_id')
                            ->findAll();
                        // var_dump($penerimaanBarangDetail);
                        foreach ($penerimaanBarangDetail as $valuePenerimaanBarangDetail) {
                            $hargaSatuan += ($valuePenerimaanBarangDetail['harga'] + $valuePenerimaanBarangDetail['harga_harian'] + $valuePenerimaanBarangDetail['harga_bulanan']) * $kursValue;
                            $totalQty += $valuePenerimaanBarangDetail['qty'];
                            $satuanLPB = $valuePenerimaanBarangDetail['kode_satuan'];
                        }
                    }
                }
                $totalHarga = $totalQty * $hargaSatuan;
                $value->totalQtyLPB = $totalQty;
                $value->totalHargaLPB = $hargaSatuan;
                $value->hargaSatuanLPB = $hargaSatuan / $totalQty;
                $value->satuanLPB = $satuanLPB;
            }

            foreach ($productionResultDataTitle as &$value) {
                $stockDokumenResult = explode(',', $value['stock_dokumen']);

                foreach ($stockDokumenResult as $key => $stockDokumen) {
                    if (preg_match('/\((PO\/[^)]+)\)/', $stockDokumen, $matches)) {
                        // Jika ada teks dalam kurung, gunakan yang di dalam kurung
                        $poNo = $matches[1];
                        $jasaVendorNo = trim(explode(' (', $stockDokumen)[0]);
                    } else {
                        // Jika tidak ada kurung, gunakan nilai langsung
                        $poNo = $stockDokumen;
                        $jasaVendorNo = 0;
                    }

                    $poBBLokal = $this->rmPurchaseOrderModel->where('po_no', $poNo)->first();
                    $poBBImport = $this->rmImportPOModel->where('po_no', $poNo)->first();
                    $poBP = $this->amPurchaseOrderModel->where('po_no', $poNo)->first();
                    $jasaVendorIn = $this->jasaVendorInModel->where('no_penerimaan_surat_jalan', $jasaVendorNo)->first();

                    if ($jasaVendorNo != 0) {
                        $jasaVendorInDetailCheck = $this->jasaVendorInDetailModel
                            ->join('stock', "stock.id = jasa_vendor_in_detail.stock_in_id")
                            ->where('jasa_vendor_in_id', $jasaVendorIn['id'])
                            ->where('stock.barang1_id', $value['barang1_id'])
                            ->where('stock.barang2_id', $value['barang2_id'])
                            ->where('jasa_vendor_in_detail.no_aju_in', $value['no_aju'])
                            ->findAll();
                        // var_dump($jasaVendorInDetailCheck);
                        foreach ($jasaVendorInDetailCheck as $valueJasaVendorIn) {
                            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel
                                ->join('stock', "stock.id = jasa_vendor_out_detail.stock_out_id")
                                ->where('jasa_vendor_out_detail.id', $valueJasaVendorIn['jasa_vendor_out_detail_id'])
                                ->where('jasa_vendor_out_detail.no_aju_out', $value['no_aju'])
                                ->findAll();
                            foreach ($jasaVendorOutDetail as $valueJasaVendorOut) {
                                $totalQty = 0;
                                $totalHarga = 0;
                                $hargaSatuan = 0;
                                $satuanPO = "";
                                if ($poBBLokal) {
                                    $poBBLokalDetail = $this->rmPurchaseOrderDetailModel
                                        ->select('rm_purchase_order_details.*, satuans.kode_satuan')
                                        ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
                                        ->where('rm_purchase_order_id', $poBBLokal['id'])
                                        ->where('barang1_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('barang2_id', $valueJasaVendorOut['barang2_id'])
                                        ->findAll();
                                    foreach ($poBBLokalDetail as $valuePoBBLokal) {
                                        $kursValue = 1;
                                        $hargaSatuan = $valuePoBBLokal['general_price'] + $valuePoBBLokal['daily_price'] + $valuePoBBLokal['monthly_price'];
                                        $totalQty += $valuePoBBLokal['qty'];
                                        $satuanPO = $valuePoBBLokal['kode_satuan'];
                                    }
                                    $totalHarga = $value['qty'] * $hargaSatuan;
                                }

                                if ($poBBImport) {
                                    $poBBImportDetail = $this->rmImportPODetailModel
                                        ->select('rm_import_po_details.*, satuans.kode_satuan, rm_import_pos.currency, rm_import_pos.po_date')
                                        ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
                                        ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
                                        ->where('rm_import_po_id', $poBBImport['id'])
                                        ->where('barang_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('spesifikasi_id', $valueJasaVendorOut['barang2_id'])
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
                                }
                                if ($poBP) {
                                    $poBPDetail = $this->amPurchaseOrderDetailModel
                                        ->select('am_purchase_order_details.*, satuans.kode_satuan')
                                        ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
                                        ->where('am_purchase_order_id', $poBP['id'])
                                        ->where('barang_id', $valueJasaVendorOut['barang1_id'])
                                        ->where('spesifikasi_id', $valueJasaVendorOut['barang2_id'])
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
                                }
                                $value['totalQtyPO'] = $value['qty'];
                                $value['totalHargaPO'] = $totalHarga;
                                $value['hargaSatuanPO'] = $hargaSatuan;
                                $value['satuanPO'] = $satuanPO;
                            }
                        }
                    } else {
                        $totalQty = 0;
                        $totalHarga = 0;
                        $hargaSatuan = 0;
                        $satuanPO = "";
                        if ($poBBLokal) {
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
                            $totalHarga = $value['qty'] * $hargaSatuan;
                        }

                        if ($poBBImport) {
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
                        }
                        if ($poBP) {
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
                        }
                        $value['totalQtyPO'] = $value['qty'];
                        $value['totalHargaPO'] = $totalHarga;
                        $value['hargaSatuanPO'] = $hargaSatuan;
                        $value['satuanPO'] = $satuanPO;
                    }
                }
            }
            // exit;

            if ($productionResultDataTitle) {
                return response()->setJSON([
                    'data' => $productionResultDataTitle,
                    'dataResultPO' => $dataResultPO,
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
        // if (!empty($this->request->getVar('bulan'))) {
        if (!empty($this->request->getVar('tanggal_awal')) && !empty($this->request->getVar('tanggal_akhir'))) {
            // $monthData = $this->request->getVar('bulan');
            // list($month, $year) = explode('/', $monthData);
            // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
            $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));
            $conditionProduction = [
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
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
        if (!empty($this->request->getVar('tanggal_awal')) && !empty($this->request->getVar('tanggal_akhir'))) {
            // if (!empty($this->request->getVar('bulan'))) {
            // $monthData = $this->request->getVar('bulan');
            // list($month, $year) = explode('/', $monthData);
            // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
            $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));
            $conditionProduction = [
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
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
        // $monthData = $this->request->getVar('bulan');
        // $department = $this->request->getVar('department');
        // $id_coa = $this->request->getVar('id_coa');

        // list($month, $year) = explode('/', $monthData);
        // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
        $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));

        $settingCosting = $this->settingCosting->getSettingCosting();

        foreach ($settingCosting as &$valueSetting) {
            $condition = [
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
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
        // $monthData = $this->request->getVar('bulan');
        // $department = $this->request->getVar('department');
        $id_coa = $this->request->getVar('id_coa');

        // list($month, $year) = explode('/', $monthData);
        // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
        $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));

        $condition = [
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
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
        // $monthData = $this->request->getVar('bulan');
        // list($month, $year) = explode('/', $monthData);
        // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
        $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));
        $conditionProduction = [
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'divisi_id' => $this->request->getVar('department'),
            'kategori_id' => $this->request->getVar('kategori'),
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
                        break;
                    }
                    // var_dump($dataResult);
                    // var_dump($valueProductionResultData);
                }
            }

            // Merge current dataResult into dataResults
            $dataResults = array_merge($dataResults, $dataResult);
        }
        // var_dump($productionResultDataTitle);
        // var_dump($dataResults);
        // exit;
        return response()->setJSON([
            'data' => $dataResults,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getSaldoAdjusment()
    {
        // $monthData = $this->request->getVar('bulan');
        // list($month, $year) = explode('/', $monthData);
        // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
        $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));

        $adjusment = $this->adjusmentModel
            ->select('adjusment.*, adjusment_detail.*')
            ->join('adjusment_detail', 'adjusment_detail.adjusment_id = adjusment.id', 'left')
            ->where('adjusment.tanggal >=', $tanggal_awal)
            ->where('adjusment.tanggal <=', $tanggal_akhir)
            ->where('adjusment.divisi_id', $this->request->getVar('divisi_id'))
            ->where('adjusment.company_id', $this->this_company_id)
            ->where('adjusment.status_posting', "1")
            ->where('adjusment.deletedAt', null)
            ->where('adjusment_detail.deletedAt', null)
            ->findAll();
        // var_dump($adjusment);
        // exit;
        foreach ($adjusment as &$value) {
            $stockDetail2Model = $this->stockDetail2Model
                ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
                ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->where('stock_dokumen', $value['stock_dokumen'])
                ->where('bc_id', $value['bc_id'])
                ->where('no_aju', $value['no_aju'])
                ->where('barang1_id', $value['barang1_id'])
                ->where('barang2_id', $value['barang2_id'])
                ->first();
            $value['harga_umum'] = $stockDetail2Model['harga_umum'];
            $value['harga_harian'] = $stockDetail2Model['harga_harian'];
            $value['harga_bulanan'] = $stockDetail2Model['harga_bulanan'];
            $value['barang'] = $stockDetail2Model['barang_name'] . ' - ' . $stockDetail2Model['spesifikasi'];
            $value['satuan'] = $stockDetail2Model['kode_satuan'];
        }
        // var_dump($adjusment);
        // exit;
        return response()->setJSON([
            'data' => $adjusment,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getSaldoJual()
    {
        // $monthData = $this->request->getVar('bulan');
        // list($month, $year) = explode('/', $monthData);
        // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
        $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));

        $mutasi = $this->mutasiModel
            ->select('mutasi.*, mutasi_detail.*')
            ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
            ->where('mutasi.tanggal >=', $tanggal_awal)
            ->where('mutasi.tanggal <=', $tanggal_akhir)
            ->where('mutasi.divisi_asal_id', $this->request->getVar('divisi_id'))
            ->where('mutasi.company_id', $this->this_company_id)
            ->where('mutasi.status_posting', "1")
            ->where('mutasi.deletedAt', null)
            ->where('mutasi_detail.deletedAt', null)
            ->findAll();
        // var_dump($mutasi);
        // exit;
        foreach ($mutasi as &$value) {
            $stockDetail2Model = $this->stockDetail2Model
                ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
                ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->where('stock_details2.stock_dokumen', $value['stock_dokumen'])
                ->where('stock_details2.bc_id', $value['bc_id'])
                ->where('stock_details2.no_aju', $value['no_aju'])
                ->where('stock_details2.stock_id', $value['stock_id'])
                ->first();
            $value['harga_umum'] = $stockDetail2Model['harga_umum'];
            $value['harga_harian'] = $stockDetail2Model['harga_harian'];
            $value['harga_bulanan'] = $stockDetail2Model['harga_bulanan'];
            $value['barang'] = $stockDetail2Model['barang_name'] . ' - ' . $stockDetail2Model['spesifikasi'];
            $value['satuan'] = $stockDetail2Model['kode_satuan'];
        }
        // var_dump($mutasi);
        // exit;
        return response()->setJSON([
            'data' => $mutasi,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getSaldoTrimming()
    {
        // $monthData = $this->request->getVar('bulan');
        // list($month, $year) = explode('/', $monthData);
        // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
        $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));
        $conditionProduction = [
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'divisi_id' => $this->request->getVar('department'),
            'kategori_id' => $this->request->getVar('kategori'),
        ];
        $kursValue = 1;
        $productionResultDataTitle = $this->productionResultModel->getDataProductionResultBahanBakuWithDetail($conditionProduction);

        $dataResults = [];
        $dataStockModel = $this->stockModel->getBarangAndStockConditionWithoutWarehouse(
            "bahan_setengah_jadi",
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
                        break;
                    }
                }
            }

            // Merge current dataResult into dataResults
            $dataResults = array_merge($dataResults, $dataResult);
        }
        // var_dump($dataResults);
        // exit;
        return response()->setJSON([
            'data' => $dataResults,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getSaldoKopek()
    {
        // $monthData = $this->request->getVar('bulan');
        // list($month, $year) = explode('/', $monthData);
        // $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_awal'))));
        $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal_akhir'))));
        $conditionProduction = [
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'divisi_id' => $this->request->getVar('department'),
            'kategori_id' => $this->request->getVar('kategori'),
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
                        break;
                    }
                }
            }

            // Merge current dataResult into dataResults
            $dataResults = array_merge($dataResults, $dataResult);
        }
        // var_dump($dataResults);
        // exit;
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
            'hasil_trimming',
            'hasil_kaleng',
            'hasil_frozen',
        ];

        if (in_array($page, $validPages)) {
            return view('Accounting/rasio/' . $page, $data);
        } else {
            return view('default_view', $data);
        }
    }
}
