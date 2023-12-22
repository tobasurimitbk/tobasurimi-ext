<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\BC23BarangDokumenModel;
use App\Models\BC23BarangModel;
use App\Models\BC23BarangTarifModel;
use App\Models\BC23DokumenModel;
use App\Models\BC23EntitasModel;
use App\Models\BC23KemasanModel;
use App\Models\BC23KontainerModel;
use App\Models\BC23Model;
use App\Models\BC23PengangkutModel;
use App\Models\BeaCukaiModel;
use App\Models\CountryModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use Exception;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54
class BC23 extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        return \view('BeaCukai/bc-23/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 2.3"
        ];

        $condition = [
            "penerimaan_barang.company_id"  => $this->this_company_id,
            "penerimaan_barang.deletedAt" => null,
            "penerimaan_barang.bc_type" => 48
        ];


        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusBC" => $this->request->getGet("statusBC"),
            "statusLPB" => $this->request->getGet("statusLPB"),
            "noBC23" => $this->request->getGet("noBC23"),
            "mulaiTanggalBC23" => $this->request->getGet("mulaiTanggalBC23"),
            "selesaiTanggalBC23" => $this->request->getGet('selesaiTanggalBC23'),
            "noPenerimaanBarang" => $this->request->getGet('noPenerimaanBarang'),
            "noAju" => $this->request->getGet('noAju')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc23Model = new BC23Model();

        $beaCukaiData = $bc23Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $status = $data->status_posting == null ? "BELUM DIBUAT" : strtoupper($data->status_posting);
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_barang_id"  => encrypt($data->penerimaan_barang_id),
                "bc_no_lokal"           => $data->bc_no_lokal,
                "tanggal_bc_23"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "no_aju"                => $data->no_aju,
                "jenis_lpb"             => $data->status_penerimaan . " " . ($data->tipe_bahan == "PENOLONG" ? "BP" : "BB"),
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "warehouse_name"        => strtoupper($data->warehouse_name),
                "status"                => $status
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create($id)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $metaDataModel = new MetadataModel();
        $countryModel = new CountryModel();
        $kantorBeaCukaiModel = new KantorBeaCukaiModel();

        $id = decrypt($id);

        $lpb = $penerimaanBarangModel->getById($id);

        if ($lpb == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        $data = [
            // data helper passing in form select
            'kodeFasilitasTarif' => $metaDataModel->where('name', "Kode Fasilitas Tarif BC")->findAll(),
            'kodeJenisTarif' => $metaDataModel->where('name', "Kode Jenis Tarif BC")->findAll(),
            'kodeAsalBahanBaku' => $metaDataModel->where('name', "Kode Asal Bahan Baku BC")->findAll(),
            'kodeSatuanBarang' => $metaDataModel->where('name', "Kode Satuan BC")->orderBy('value', "ASC")->findAll(),
            'kodePerhitungan' => $metaDataModel->where('name', 'Kode Perhitungan BC')->orderBy('value', "ASC")->findAll(),
            'kodeNegaraAsal' => $countryModel->findAll(),
            'kodeJenisKemasan' => $metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeKategoriBarang' => $metaDataModel->where('name', 'Kategori Barang BC')->orderBy('description', "ASC")->findAll(),
            'kodeDokumen' => $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'kodeAsuransi' => $metaDataModel->where('name', "Kode Asuransi BC")->findAll(),
            'kodeIncoterm' => $metaDataModel->where('name', "Kode Incoterm BC")->findAll(),
            'kodeKantor' => $kantorBeaCukaiModel->findAll(),
            'kodeTujunTpb' => $metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTutupPu' => $metaDataModel->where('name', "Kode Tutup Pu BC")->findAll(),
            'kodeValuta' => $metaDataModel->where('name', "Valuta")->findAll(),
            'kodeJenisAPI' => $metaDataModel->where('name', "Jenis API")->findAll(),
            'kodeKenaPajak' => $metaDataModel->where('name', "Kode Kena Pajak BC")->findAll(),
            'kodeJenisIdentas' => $metaDataModel->where('name', "Jenis Identitas")->findAll(),
            'kodeTipeKontainer' => $metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'kodePengangkutan' => $metaDataModel->where('name', "Pengangkutan")->findAll(),

            // data detail
            'lpb' => $penerimaanBarangModel->getById($id),
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $lpb->tipe_bahan, $lpb->status_penerimaan)
        ];

        return view('BeaCukai/bc-23/form', $data);
    }

    public function update($id, $bc23ID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $metaDataModel = new MetadataModel();
        $countryModel = new CountryModel();
        $kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $bc23Model = new BC23Model();

        $id = decrypt($id);
        $bc23ID = decrypt($bc23ID);

        $lpb = $penerimaanBarangModel->getById($id);
        $bc23Detail = $bc23Model->where('id', $bc23ID)->where('deletedAt', null)->first();

        if ($lpb == null || $bc23Detail == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        $data = [
            // data helper passing in form select
            'kodeFasilitasTarif' => $metaDataModel->where('name', "Kode Fasilitas Tarif BC")->findAll(),
            'kodeJenisTarif' => $metaDataModel->where('name', "Kode Jenis Tarif BC")->findAll(),
            'kodeAsalBahanBaku' => $metaDataModel->where('name', "Kode Asal Bahan Baku BC")->findAll(),
            'kodeSatuanBarang' => $metaDataModel->where('name', "Kode Satuan BC")->orderBy('value', "ASC")->findAll(),
            'kodePerhitungan' => $metaDataModel->where('name', 'Kode Perhitungan BC')->orderBy('value', "ASC")->findAll(),
            'kodeNegaraAsal' => $countryModel->findAll(),
            'kodeJenisKemasan' => $metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeKategoriBarang' => $metaDataModel->where('name', 'Kategori Barang BC')->orderBy('description', "ASC")->findAll(),
            'kodeDokumen' => $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'kodeAsuransi' => $metaDataModel->where('name', "Kode Asuransi BC")->findAll(),
            'kodeIncoterm' => $metaDataModel->where('name', "Kode Incoterm BC")->findAll(),
            'kodeKantor' => $kantorBeaCukaiModel->findAll(),
            'kodeTujunTpb' => $metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTutupPu' => $metaDataModel->where('name', "Kode Tutup Pu BC")->findAll(),
            'kodeValuta' => $metaDataModel->where('name', "Valuta")->findAll(),
            'kodeJenisAPI' => $metaDataModel->where('name', "Jenis API")->findAll(),
            'kodeKenaPajak' => $metaDataModel->where('name', "Kode Kena Pajak BC")->findAll(),
            'kodeJenisIdentas' => $metaDataModel->where('name', "Jenis Identitas")->findAll(),
            'kodeTipeKontainer' => $metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'kodePengangkutan' => $metaDataModel->where('name', "Pengangkutan")->findAll(),
            'kodeSatuanBarang' => $metaDataModel->getKodeSatuanBarang(10, 0),

            // data detail
            'lpb' => $penerimaanBarangModel->getById($id),
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $lpb->tipe_bahan, $lpb->status_penerimaan),
            'bc23Detail' => $bc23Detail,
            'bc23Json' => $bc23Model->writeJsonUpdate($bc23ID)
        ];


        return view('BeaCukai/bc-23/form', $data);
    }

    public function createAction()
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $bc23Model = new BC23Model();
        $bc23BarangModel = new BC23BarangModel();
        $bc23BarangDokumenModel = new BC23BarangDokumenModel();
        $bc23BarangTarifModel = new BC23BarangTarifModel();
        $bc23DokumenModel = new BC23DokumenModel();
        $bc23EntitasModel = new BC23EntitasModel();
        $bc23KemasanModel = new BC23KemasanModel();
        $bc23KontainerModel = new BC23KontainerModel();
        $bc23PengangkutModel = new BC23PengangkutModel();

        // $helper = [
        //     '$_POST' => $_POST,
        //     'barang' => json_decode($_POST['barang']),
        //     'dokumen' => json_decode($_POST['dokumen']),
        //     'entitas' => json_decode($_POST['entitas']),
        //     'kemasan' => json_decode($_POST['kemasan']),
        //     'kontainer' => json_decode($_POST['kontainer']),
        //     'pengangkut' => json_decode($_POST['pengangkut']),
        // ];
        // return response()->setJSON([
        //     'message' => "Terjadi kesalahan di sisi server",
        //     'token' => csrf_hash(),
        //     'error' => "Kon",
        //     'status' => false,
        //     'helper' => $helper
        // ]);

        try {
            // UPDATE TGL LPB
            $penerimaanBarangModel->update(
                decrypt($this->request->getVar('penerimaan_barang_id')),
                [
                    "tanggal" => $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "",
                ]
            );


            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));

            // BC 23 
            $bc23ID = $bc23Model->insert([
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'bc_no_lokal' => $bc23Model->getNo(date('m'), date('Y'), $last_day),
                'asal_data' => 'S',
                'asuransi' => $this->request->getVar('root_asuransi'),
                'biaya_pengurang' => $this->request->getVar('root_biaya_pengurang'),
                'biaya_tambahan' => $this->request->getVar('root_biaya_tambahan'),
                'bruto' => $this->request->getVar('root_bruto'),
                'cif' => $this->request->getVar('root_cif'),
                'fob' => $this->request->getVar('root_fob'),
                'freight' => $this->request->getVar('root_freight'),
                'harga_penyerahan' => $this->request->getVar('root_harga_penyerahan'),
                'jabatan_pengusaha_ttd' => $this->request->getVar('root_jabatan_pengusaha_ttd'),
                'jumlah_kontainer' => $this->request->getVar('root_jumlah_kontainer'),
                'kode_asuransi' => decrypt($this->request->getVar('root_kode_asuransi')),
                'kode_dokumen' => '23',
                'kode_incoterm' => decrypt($this->request->getVar('root_kode_incoterm')),
                'kode_kantor' => decrypt($this->request->getVar('root_kode_kantor')),
                'kode_kantor_bongkar' => decrypt($this->request->getVar('root_kode_kantor_bongkar')),
                'kode_kena_pajak' => decrypt($this->request->getVar('root_kode_kena_pajak')),
                'kode_pelabuhan_bongkar' => $this->request->getVar('root_kode_pelabuhan_bongkar'),
                'kode_pelabuhan_muat' => $this->request->getVar('root_kode_pelabuhan_muat'),
                'kode_pelabuhan_transit' => $this->request->getVar('root_kode_pelabuhan_transit'),
                'kode_tps' => $this->request->getVar('root_kode_tps'),
                'kode_tujuan_tpb' => decrypt($this->request->getVar('root_kode_tujuan_tpb')),
                'kode_tutup_pu' => decrypt($this->request->getVar('root_kode_tutup_pu')),
                'kode_valuta' => decrypt($this->request->getVar('root_kode_valuta')),
                'kota_ttd' => $this->request->getVar('root_kota_ttd'),
                'nama_ttd' => $this->request->getVar('root_nama_ttd'),
                'ndpbm' => $this->request->getVar('root_ndpbm'),
                'netto' => $this->request->getVar('root_netto'),
                'nik' => decrypt($this->request->getVar('root_nik')),
                'nilai_barang' => $this->request->getVar('root_nilai_barang'),
                'no_aju' => $this->request->getVar('root_no_aju'),
                'no_bc_11' => $this->request->getVar('root_no_bc_11'),
                'pos_bc_11' => $this->request->getVar('root_pos_bc_11'),
                'seri' => $this->request->getVar('root_seri'),
                'sub_pos_bc_11' => $this->request->getVar('root_sub_pos_bc_11'),
                'tanggal_bc_11' => $this->request->getVar("root_tanggal_bc_11") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("root_tanggal_bc_11")), "Y-m-d") : "",
                'tanggal_tiba' => $this->request->getVar("root_tanggal_tiba") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("root_tanggal_tiba")), "Y-m-d") : "",
                'tanggal_ttd' =>  $this->request->getVar("root_tanggal_ttd") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("root_tanggal_ttd")), "Y-m-d") : "",
                'status_posting' => 'Belum Posting'
            ]);

            // BARANG DETAIL BC 23
            if (!empty(json_decode($this->request->getVar('barang')))) {
                foreach (json_decode($this->request->getVar('barang')) as $b) {
                    $bc23BarangID = $bc23BarangModel->insert([
                        'bc_23_id' => $bc23ID,
                        'penerimaan_barang_id' => decrypt($b->penerimaan_barang_id),
                        'penerimaan_barang_detail_id' => decrypt($b->id),
                        'purchase_order_id' => decrypt($b->purchase_order_id),
                        'purchase_order_details_id' => decrypt($b->purchase_order_details_id),
                        'asuransi' => $b->detail_barang_dok->asuransi,
                        'cif_rupiah' => $b->detail_barang_dok->cif_rupiah,
                        'diskon' => $b->detail_barang_dok->diskon,
                        'fob' => $b->detail_barang_dok->fob,
                        'freight' => $b->detail_barang_dok->freight,
                        'harga_cif' => ($b->detail_barang_dok->harga_cif),
                        'harga_ekspor' => ($b->detail_barang_dok->harga_ekspor),
                        'harga_penyerahan_barang' => ($b->detail_barang_dok->harga_penyerahan_barang),
                        'harga_perolehan_barang' => ($b->detail_barang_dok->harga_perolehan_barang),
                        'harga_satuan_barang' => ($b->detail_barang_dok->harga_satuan_barang),
                        'isi_per_kemasan' => $b->detail_barang_dok->isi_per_kemasan,
                        'jumlah_kemasan' => $b->detail_barang_dok->jumlah_kemasan,
                        'jumlah_satuan' => $b->detail_barang_dok->jumlah_satuan,
                        'kode_asal_bahan_baku' => decrypt($b->detail_barang_dok->kode_asal_bahan_baku),
                        'kode_dokumen' => decrypt($b->detail_barang_dok->kode_dokumen),
                        'kode_jenis_kemasan' => decrypt($b->detail_barang_dok->kode_jenis_kemasan),
                        'kode_kategori_barang' => decrypt($b->detail_barang_dok->kode_kategori_barang),
                        'kode_negara_asal' => decrypt($b->detail_barang_dok->kode_negara_asal),
                        'kode_perhitungan' => decrypt($b->detail_barang_dok->kode_perhitungan),
                        'kode_satuan_barang' => decrypt($b->detail_barang_dok->kode_satuan_barang),
                        'merk_barang' => $b->detail_barang_dok->merk_barang,
                        'ndpm' => $b->detail_barang_dok->ndpm,
                        'netto' => $b->detail_barang_dok->netto,
                        'nilai_barang' => $b->detail_barang_dok->nilai_barang,
                        'nilai_tambah' => $b->detail_barang_dok->nilai_tambah,
                        'pos_tarif' => $b->detail_barang_dok->pos_tarif,
                        'seri_barang' => $b->detail_barang_dok->seri_barang,
                        'spesifikasi_lain' => $b->detail_barang_dok->spesifikasi_lain,
                        'tipe_barang' => $b->detail_barang_dok->tipe_barang,
                        'ukuran_barang' => $b->detail_barang_dok->ukuran_barang,
                        'uraian' => $b->detail_barang_dok->uraian
                    ]);

                    // BARANG DOKUMEN
                    foreach ($b->detail_barang_dok->barangDokumen as $bd) {
                        $bc23BarangDokumenModel->insert([
                            'bc_23_barang_id' => $bc23BarangID,
                            'no_seri_dokumen' => $bd->no_seri_dokumen
                        ]);
                    }
                    // BARANG TARIF
                    foreach ($b->detail_barang_dok->barangTarif as $bt) {
                        $bc23BarangTarifModel->insert([
                            'bc_23_barang_id' => $bc23BarangID,
                            'kode_fasilitas_tarif' => decrypt($bt->barang_tarif_kode_fasilitas_tarif),
                            'kode_jenis_tarif' => decrypt($bt->barang_tarif_kode_jenis_tarif),
                            'kode_satuan_barang' => decrypt($bt->barang_tarif_kode_satuan_barang),
                            'seri_barang' => $bt->barang_tarif_seri_barang,
                            'jumlah_satuan_bea_masuk' => $bt->jumlah_satuan_bm,
                            'kode_jenis_pungutan' => 'BM',
                            'nilai_bayar' => ($bt->nilai_bayar),
                            'nilai_fasilitas' => ($bt->nilai_fasilitas),
                            'nilai_sudah_dilunasi' => ($bt->nilai_sudah_dilunasi),
                            'tarif_bea_masuk' => ($bt->tarif_bm),
                            'tarif_fasilitas' => ($bt->tarif_fasilitas)
                        ]);
                    }
                }
            }

            // DOKUMEN BC23
            if (!empty(json_decode($this->request->getVar('dokumen')))) {
                foreach (json_decode($this->request->getVar('dokumen')) as $d) {
                    $bc23DokumenModel->insert([
                        'bc_23_id' => $bc23ID,
                        'kode_dokumen' => '380',
                        'nomor_dokumen' => $d->dokumen_pelengkap_nomor_dokumen,
                        'seri_dokumen' => $d->dokumen_pelengkap_seri_dokumen,
                        'tanggal_dokumen' => $d->dokumen_pelengkap_tanggal_dokumen ? date_format(date_create_from_format("d/m/Y", $d->dokumen_pelengkap_tanggal_dokumen), "Y-m-d") : "",
                    ]);
                }
            }

            // ENTITAS BC23
            if (!empty(json_decode($this->request->getVar('entitas')))) {
                foreach (json_decode($this->request->getVar('entitas')) as $e) {
                    $bc23EntitasModel->insert([
                        'bc_23_id' => $bc23ID,
                        'alamat_entitas' => $e->entitas_alamat_entitas,
                        'kode_entitas' => '3',
                        'kode_jenis_entitas' => decrypt($e->entitas_kode_jenis_identitas),
                        'nama_entitas' => $e->entitas_nama_entitas,
                        'nib_entitas' => $e->entitas_nib_entitas,
                        'nomor_identitas' => $e->entitas_nomor_identitas,
                        'nomor_ijin_entitas' => $e->entitas_nomor_ijin_entitas,
                        'tanggal_ijin_entitas' => $e->entitas_tanggal_ijin_entitas ? date_format(date_create_from_format("d/m/Y", $e->entitas_tanggal_ijin_entitas), "Y-m-d") : "",
                        'seri_entitas' => $e->entitas_seri_entitas
                    ]);
                }
            }

            // KEMASAN BC 23
            if (!empty(json_decode($this->request->getVar('kemasan')))) {
                foreach (json_decode($this->request->getVar('kemasan')) as $k) {
                    $bc23KemasanModel->insert([
                        'bc_23_id' => $bc23ID,
                        'jumlah_kemasan' => $k->kemasan_jumlah_kemasan,
                        'kode_jenis_kemasan' => decrypt($k->kemasan_kode_jenis_kemasan),
                        'merk_kemasan' => $k->kemasan_merk_kemasan,
                        'seri_kemasan' => $k->kemasan_seri_kemasan
                    ]);
                }
            }

            // KONTAINER BC 23
            if (!empty(json_decode($this->request->getVar('kontainer')))) {
                foreach (json_decode($this->request->getVar('kontainer')) as $k) {
                    $bc23KontainerModel->insert([
                        'bc_23_id' => $bc23ID,
                        'kode_jenis_kontainer' => decrypt($k->kontainer_kode_jenis_kontainer),
                        'kode_tipe_kontainer' => decrypt($k->kontainer_kode_tipe_kontainer),
                        'kode_ukuran_kontainer' => decrypt($k->kontainer_kode_ukuran_kontainer),
                        'nomor_kontainer' => $k->kontainer_nomor_kontainer,
                        'seri_kontainer' => $k->kontainer_seri_kontainer,
                    ]);
                }
            }

            // PENGANGKUT BC 23
            if (!empty(json_decode($this->request->getVar('pengangkut')))) {
                foreach (json_decode($this->request->getVar('pengangkut')) as $p) {
                    $bc23PengangkutModel->insert([
                        'bc_23_id' => $bc23ID,
                        'kode_bendera' => $p->pengangkut_kode_bendera,
                        'kode_cara_angkut' => decrypt($p->pengangkut_kode_cara_angkut),
                        'nama_sarana_pengangkut' => $p->pengangkut_seri_pengangkut,
                        'nomor_pengangkut' => $p->pengangkut_nomor_pengangkut,
                        'seri_pengangkut' => $p->pengangkut_seri_pengangkut
                    ]);
                }
            }

            return response()->setJSON([
                'message' => "Dokumen BC 2.3 berhasil dibuat",
                'id' => encrypt($bc23ID),
                'penerimaan_barang_id' => encrypt($this->request->getVar('penerimaan_barang_id')),
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "Terjadi kesalahan di sisi server",
                'token' => csrf_hash(),
                'error' => $e->getMessage(),
                'status' => false,
            ]);
        }
    }

    public function deleteAction()
    {
        $bc23Model = new BC23Model();
        $bc23BarangModel = new BC23BarangModel();
        $bc23BarangDokumenModel = new BC23BarangDokumenModel();
        $bc23BarangTarifModel = new BC23BarangTarifModel();
        $bc23DokumenModel = new BC23DokumenModel();
        $bc23EntitasModel = new BC23EntitasModel();
        $bc23KemasanModel = new BC23KemasanModel();
        $bc23KontainerModel = new BC23KontainerModel();
        $bc23PengangkutModel = new BC23PengangkutModel();

        try {
            $id = decrypt($this->request->getVar('id'));
            // Get Barang Dokumen
            $barangDok = $bc23BarangModel->where('bc_23_id', $id)->where('deletedAt', null)->first();
            // Delete BC 23
            $bc23Model->delete($id);
            // Delete BC Barang Dokumen
            $bc23BarangDokumenModel->where('bc_23_barang_id', $barangDok['id'])->delete();
            // Delete BC Barang Tarif
            $bc23BarangTarifModel->where('bc_23_barang_id', $barangDok['id'])->delete();
            // Delete Barang Dok (BC23BarangModel)
            $bc23BarangModel->where('bc_23_id', $id)->delete();
            // BC 23 Dokumen
            $bc23DokumenModel->where('bc_23_id', $id)->delete();
            //BC 23 Entitas
            $bc23EntitasModel->where('bc_23_id', $id)->delete();
            //BC 23 Kemasan
            $bc23KemasanModel->where('bc_23_id', $id)->delete();
            // BC 23 Kontainer
            $bc23KontainerModel->where('bc_23_id', $id)->delete();
            // BC 23 Pengangkut
            $bc23PengangkutModel->where('bc_23_id', $id)->delete();

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Dokumen BC 2.3 berhasil dihapus"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "Terjadi kesalahan di sisi server",
                'token' => csrf_hash(),
                'error' => $e->getMessage(),
                'status' => false,
            ]);
        }
    }

    public function postingAction()
    {
        $bc23Model = new BC23Model();

        try {
            $id = decrypt($this->request->getVar('id'));
            // SUBMIT KE SERVER BEA CUKAI
            // (To do)
            // UPDATE STATUS
            $bc23Model->update($id, [
                'status_posting' => "Sudah Posting"
            ]);
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Dokumen BC 2.3 berhasil diupload ke platform Ceisa 4.0 Bea Cukai"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "Terjadi kesalahan di sisi server",
                'token' => csrf_hash(),
                'error' => $e->getMessage(),
                'status' => false,
            ]);
        }
    }

    public function updateAction()
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $bc23Model = new BC23Model();
        $bc23BarangModel = new BC23BarangModel();
        $bc23BarangDokumenModel = new BC23BarangDokumenModel();
        $bc23BarangTarifModel = new BC23BarangTarifModel();
        $bc23DokumenModel = new BC23DokumenModel();
        $bc23EntitasModel = new BC23EntitasModel();
        $bc23KemasanModel = new BC23KemasanModel();
        $bc23KontainerModel = new BC23KontainerModel();
        $bc23PengangkutModel = new BC23PengangkutModel();

        try {
            $id = decrypt($this->request->getVar('id'));

            // UPDATE TGL LPB
            $penerimaanBarangModel->update(
                decrypt($this->request->getVar('penerimaan_barang_id')),
                [
                    "tanggal" => $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "",
                ]
            );

            $bc23Model->update($id, [
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'asal_data' => 'S',
                'asuransi' => $this->request->getVar('root_asuransi'),
                'biaya_pengurang' => $this->request->getVar('root_biaya_pengurang'),
                'biaya_tambahan' => $this->request->getVar('root_biaya_tambahan'),
                'bruto' => $this->request->getVar('root_bruto'),
                'cif' => $this->request->getVar('root_cif'),
                'fob' => $this->request->getVar('root_fob'),
                'freight' => $this->request->getVar('root_freight'),
                'harga_penyerahan' => $this->request->getVar('root_harga_penyerahan'),
                'jabatan_pengusaha_ttd' => $this->request->getVar('root_jabatan_pengusaha_ttd'),
                'jumlah_kontainer' => $this->request->getVar('root_jumlah_kontainer'),
                'kode_asuransi' => decrypt($this->request->getVar('root_kode_asuransi')),
                'kode_dokumen' => '23',
                'kode_incoterm' => decrypt($this->request->getVar('root_kode_incoterm')),
                'kode_kantor' => decrypt($this->request->getVar('root_kode_kantor')),
                'kode_kantor_bongkar' => decrypt($this->request->getVar('root_kode_kantor_bongkar')),
                'kode_kena_pajak' => decrypt($this->request->getVar('root_kode_kena_pajak')),
                'kode_pelabuhan_bongkar' => $this->request->getVar('root_kode_pelabuhan_bongkar'),
                'kode_pelabuhan_muat' => $this->request->getVar('root_kode_pelabuhan_muat'),
                'kode_pelabuhan_transit' => $this->request->getVar('root_kode_pelabuhan_transit'),
                'kode_tps' => $this->request->getVar('root_kode_tps'),
                'kode_tujuan_tpb' => decrypt($this->request->getVar('root_kode_tujuan_tpb')),
                'kode_tutup_pu' => decrypt($this->request->getVar('root_kode_tutup_pu')),
                'kode_valuta' => decrypt($this->request->getVar('root_kode_valuta')),
                'kota_ttd' => $this->request->getVar('root_kota_ttd'),
                'nama_ttd' => $this->request->getVar('root_nama_ttd'),
                'ndpbm' => $this->request->getVar('root_ndpbm'),
                'netto' => $this->request->getVar('root_netto'),
                'nik' => decrypt($this->request->getVar('root_nik')),
                'nilai_barang' => $this->request->getVar('root_nilai_barang'),
                'no_aju' => $this->request->getVar('root_no_aju'),
                'no_bc_11' => $this->request->getVar('root_no_bc_11'),
                'pos_bc_11' => $this->request->getVar('root_pos_bc_11'),
                'seri' => $this->request->getVar('root_seri'),
                'sub_pos_bc_11' => $this->request->getVar('root_sub_pos_bc_11'),
                'tanggal_bc_11' => $this->request->getVar("root_tanggal_bc_11") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("root_tanggal_bc_11")), "Y-m-d") : "",
                'tanggal_tiba' => $this->request->getVar("root_tanggal_tiba") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("root_tanggal_tiba")), "Y-m-d") : "",
                'tanggal_ttd' =>  $this->request->getVar("root_tanggal_ttd") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("root_tanggal_ttd")), "Y-m-d") : "",
                'status_posting' => 'Belum Posting'
            ]);

            // Delete first
            // Get Barang Dokumen
            $barangDok = $bc23BarangModel->where('bc_23_id', $id)->where('deletedAt', null)->first();
            // Delete BC Barang Dokumen
            $bc23BarangDokumenModel->where('bc_23_barang_id', $barangDok['id'])->delete();
            // Delete BC Barang Tarif
            $bc23BarangTarifModel->where('bc_23_barang_id', $barangDok['id'])->delete();
            // Delete Barang Dok (BC23BarangModel)
            $bc23BarangModel->where('bc_23_id', $id)->delete();
            // BC 23 Dokumen
            $bc23DokumenModel->where('bc_23_id', $id)->delete();
            //BC 23 Entitas
            $bc23EntitasModel->where('bc_23_id', $id)->delete();
            //BC 23 Kemasan
            $bc23KemasanModel->where('bc_23_id', $id)->delete();
            // BC 23 Kontainer
            $bc23KontainerModel->where('bc_23_id', $id)->delete();
            // BC 23 Pengangkut
            $bc23PengangkutModel->where('bc_23_id', $id)->delete();

            // Insert Again
            // BARANG DETAIL BC 23
            if (!empty(json_decode($this->request->getVar('barang')))) {
                foreach (json_decode($this->request->getVar('barang')) as $b) {
                    $bc23BarangID = $bc23BarangModel->insert([
                        'bc_23_id' => $id,
                        'penerimaan_barang_id' => decrypt($b->penerimaan_barang_id),
                        'penerimaan_barang_detail_id' => decrypt($b->id),
                        'purchase_order_id' => decrypt($b->purchase_order_id),
                        'purchase_order_details_id' => decrypt($b->purchase_order_details_id),
                        'asuransi' => $b->detail_barang_dok->asuransi,
                        'cif_rupiah' => $b->detail_barang_dok->cif_rupiah,
                        'diskon' => $b->detail_barang_dok->diskon,
                        'fob' => $b->detail_barang_dok->fob,
                        'freight' => $b->detail_barang_dok->freight,
                        'harga_cif' => ($b->detail_barang_dok->harga_cif),
                        'harga_ekspor' => ($b->detail_barang_dok->harga_ekspor),
                        'harga_penyerahan_barang' => ($b->detail_barang_dok->harga_penyerahan_barang),
                        'harga_perolehan_barang' => ($b->detail_barang_dok->harga_perolehan_barang),
                        'harga_satuan_barang' => ($b->detail_barang_dok->harga_satuan_barang),
                        'isi_per_kemasan' => $b->detail_barang_dok->isi_per_kemasan,
                        'jumlah_kemasan' => $b->detail_barang_dok->jumlah_kemasan,
                        'jumlah_satuan' => $b->detail_barang_dok->jumlah_satuan,
                        'kode_asal_bahan_baku' => decrypt($b->detail_barang_dok->kode_asal_bahan_baku),
                        'kode_dokumen' => decrypt($b->detail_barang_dok->kode_dokumen),
                        'kode_jenis_kemasan' => decrypt($b->detail_barang_dok->kode_jenis_kemasan),
                        'kode_kategori_barang' => decrypt($b->detail_barang_dok->kode_kategori_barang),
                        'kode_negara_asal' => decrypt($b->detail_barang_dok->kode_negara_asal),
                        'kode_perhitungan' => decrypt($b->detail_barang_dok->kode_perhitungan),
                        'kode_satuan_barang' => decrypt($b->detail_barang_dok->kode_satuan_barang),
                        'merk_barang' => $b->detail_barang_dok->merk_barang,
                        'ndpm' => $b->detail_barang_dok->ndpm,
                        'netto' => $b->detail_barang_dok->netto,
                        'nilai_barang' => $b->detail_barang_dok->nilai_barang,
                        'nilai_tambah' => $b->detail_barang_dok->nilai_tambah,
                        'pos_tarif' => $b->detail_barang_dok->pos_tarif,
                        'seri_barang' => $b->detail_barang_dok->seri_barang,
                        'spesifikasi_lain' => $b->detail_barang_dok->spesifikasi_lain,
                        'tipe_barang' => $b->detail_barang_dok->tipe_barang,
                        'ukuran_barang' => $b->detail_barang_dok->ukuran_barang,
                        'uraian' => $b->detail_barang_dok->uraian
                    ]);

                    // BARANG DOKUMEN
                    foreach ($b->detail_barang_dok->barangDokumen as $bd) {
                        $bc23BarangDokumenModel->insert([
                            'bc_23_barang_id' => $bc23BarangID,
                            'no_seri_dokumen' => $bd->no_seri_dokumen
                        ]);
                    }
                    // BARANG TARIF
                    foreach ($b->detail_barang_dok->barangTarif as $bt) {
                        $bc23BarangTarifModel->insert([
                            'bc_23_barang_id' => $bc23BarangID,
                            'kode_fasilitas_tarif' => decrypt($bt->barang_tarif_kode_fasilitas_tarif),
                            'kode_jenis_tarif' => decrypt($bt->barang_tarif_kode_jenis_tarif),
                            'kode_satuan_barang' => decrypt($bt->barang_tarif_kode_satuan_barang),
                            'seri_barang' => $bt->barang_tarif_seri_barang,
                            'jumlah_satuan_bea_masuk' => $bt->jumlah_satuan_bm,
                            'kode_jenis_pungutan' => 'BM',
                            'nilai_bayar' => ($bt->nilai_bayar),
                            'nilai_fasilitas' => ($bt->nilai_fasilitas),
                            'nilai_sudah_dilunasi' => ($bt->nilai_sudah_dilunasi),
                            'tarif_bea_masuk' => ($bt->tarif_bm),
                            'tarif_fasilitas' => ($bt->tarif_fasilitas)
                        ]);
                    }
                }
            }

            // DOKUMEN BC23
            if (!empty(json_decode($this->request->getVar('dokumen')))) {
                foreach (json_decode($this->request->getVar('dokumen')) as $d) {
                    $bc23DokumenModel->insert([
                        'bc_23_id' => $id,
                        'kode_dokumen' => '380',
                        'nomor_dokumen' => $d->dokumen_pelengkap_nomor_dokumen,
                        'seri_dokumen' => $d->dokumen_pelengkap_seri_dokumen,
                        'tanggal_dokumen' => $d->dokumen_pelengkap_tanggal_dokumen ? date_format(date_create_from_format("d/m/Y", $d->dokumen_pelengkap_tanggal_dokumen), "Y-m-d") : "",
                    ]);
                }
            }

            // ENTITAS BC23
            if (!empty(json_decode($this->request->getVar('entitas')))) {
                foreach (json_decode($this->request->getVar('entitas')) as $e) {
                    $bc23EntitasModel->insert([
                        'bc_23_id' => $id,
                        'alamat_entitas' => $e->entitas_alamat_entitas,
                        'kode_entitas' => '3',
                        'kode_jenis_entitas' => decrypt($e->entitas_kode_jenis_identitas),
                        'nama_entitas' => $e->entitas_nama_entitas,
                        'nib_entitas' => $e->entitas_nib_entitas,
                        'nomor_identitas' => $e->entitas_nomor_identitas,
                        'nomor_ijin_entitas' => $e->entitas_nomor_ijin_entitas,
                        'tanggal_ijin_entitas' => $e->entitas_tanggal_ijin_entitas ? date_format(date_create_from_format("d/m/Y", $e->entitas_tanggal_ijin_entitas), "Y-m-d") : "",
                        'seri_entitas' => $e->entitas_seri_entitas
                    ]);
                }
            }

            // KEMASAN BC 23
            if (!empty(json_decode($this->request->getVar('kemasan')))) {
                foreach (json_decode($this->request->getVar('kemasan')) as $k) {
                    $bc23KemasanModel->insert([
                        'bc_23_id' => $id,
                        'jumlah_kemasan' => $k->kemasan_jumlah_kemasan,
                        'kode_jenis_kemasan' => decrypt($k->kemasan_kode_jenis_kemasan),
                        'merk_kemasan' => $k->kemasan_merk_kemasan,
                        'seri_kemasan' => $k->kemasan_seri_kemasan
                    ]);
                }
            }

            // KONTAINER BC 23
            if (!empty(json_decode($this->request->getVar('kontainer')))) {
                foreach (json_decode($this->request->getVar('kontainer')) as $k) {
                    $bc23KontainerModel->insert([
                        'bc_23_id' => $id,
                        'kode_jenis_kontainer' => decrypt($k->kontainer_kode_jenis_kontainer),
                        'kode_tipe_kontainer' => decrypt($k->kontainer_kode_tipe_kontainer),
                        'kode_ukuran_kontainer' => decrypt($k->kontainer_kode_ukuran_kontainer),
                        'nomor_kontainer' => $k->kontainer_nomor_kontainer,
                        'seri_kontainer' => $k->kontainer_seri_kontainer,
                    ]);
                }
            }

            // PENGANGKUT BC 23
            if (!empty(json_decode($this->request->getVar('pengangkut')))) {
                foreach (json_decode($this->request->getVar('pengangkut')) as $p) {
                    $bc23PengangkutModel->insert([
                        'bc_23_id' => $id,
                        'kode_bendera' => $p->pengangkut_kode_bendera,
                        'kode_cara_angkut' => decrypt($p->pengangkut_kode_cara_angkut),
                        'nama_sarana_pengangkut' => $p->pengangkut_seri_pengangkut,
                        'nomor_pengangkut' => $p->pengangkut_nomor_pengangkut,
                        'seri_pengangkut' => $p->pengangkut_seri_pengangkut
                    ]);
                }
            }

            return response()->setJSON([
                'message' => "Dokumen BC 2.3 berhasil diupdate",
                'id' => encrypt($id),
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "Terjadi kesalahan di sisi server",
                'token' => csrf_hash(),
                'error' => $e->getTrace(),
                'status' => false,
            ]);
        }
    }

    public function getKodeSatuanBarang()
    {
        $metaDataModel = new MetadataModel();
        $page = $this->request->getVar('page');
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $data = $metaDataModel->getKodeSatuanBarang($limit, $offset);

        $response = [
            'results' => $data,
            'pagination' => [
                'more' => count($data) == $limit
            ]
        ];

        return $this->response->setJSON($response);
    }

    public function generateNomorAju()
    {
        $kodeKantor = $this->request->getVar('kode_kantor');
        $kodeDokumenBC23 = '23';
        $kodeUniqPerusahaan = generateUniqueCode(6);
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = generateUniqueCode(6);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'noAju' => decrypt($kodeKantor) . '-' . $kodeDokumenBC23 . '-' . $kodeUniqPerusahaan . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan
        ]);
    }
}
