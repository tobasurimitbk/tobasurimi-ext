<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\BC40Model;
use App\Models\BCBarangDokumenModel;
use App\Models\BCBarangModel;
use App\Models\BCBarangTarifModel;
use App\Models\BCDokumenModel;
use App\Models\BCEntitasModel;
use App\Models\BCKemasanModel;
use App\Models\BCKontainerModel;
use App\Models\BCPengangkutModel;
use App\Models\HsCodesModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\NomorIjinTPBModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierHargaModel;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54
class BC40 extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {

        return view('BeaCukai/bc-40/index');
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
            "type"          => "BC 4.0"
        ];

        $condition = [
            "penerimaan_barang.company_id"  => $this->this_company_id,
            "penerimaan_barang.deletedAt" => null,
            "penerimaan_barang.bc_type" => 53,
            "penerimaan_barang.status_post" => "FINISH"
        ];


        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusBC" => $this->request->getGet("statusBC"),
            "statusLPB" => $this->request->getGet("statusLPB"),
            "noBC40" => $this->request->getGet("noBC40"),
            "mulaiTanggalBC40" => $this->request->getGet("mulaiTanggalBC40"),
            "selesaiTanggalBC40" => $this->request->getGet('selesaiTanggalBC40'),
            "noPenerimaanBarang" => $this->request->getGet('noPenerimaanBarang'),
            "noAju" => $this->request->getGet('noAju')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc40Model = new BC40Model();

        $beaCukaiData = $bc40Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $status = $data->status_dokumen == null ? "BELUM DIBUAT" : strtoupper($data->status_dokumen);
            $bc40 = $bc40Model->get($data->penerimaan_barang_id);

            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_barang_id"  => encrypt($data->penerimaan_barang_id),
                "bc_no_lokal"           => $data->bc_no_lokal,
                "tanggal_bc_40"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "no_aju"                => $data->no_aju,
                "jenis_lpb"             => $data->status_penerimaan . " " . ($data->tipe_bahan == "PENOLONG" ? "BP" : "BB"),
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "warehouse_name"        => strtoupper($data->warehouse_name),
                "status"                => $status,
                "is_update_no_aju"      => $bc40 == null ? false : ($bc40['no_aju'] == null ? false : true),
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

    public function createHeaderView($penerimaanBarangID)
    {
        $bc40Model = new BC40Model();
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $metaDataModel = new MetadataModel();
        $kantorBeaCukaiModel = new KantorBeaCukaiModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $bc40 = $bc40Model->get($penerimaanBarangID);

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $data = [
            'bc40' => $bc40,
            'noAju' => $bc40 == null ? $this->generateNomorAju() : $bc40['no_aju'],
            'kodeKantor' => $kantorBeaCukaiModel->findAll(),
            'kodeTujuanTpb' => $metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTujuanPengiriman' => $metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', 40)->where('deletedAt', null)->findAll(),
            'selectedKantor' => $metaDataModel->where('name', "Kode Kantor Pabean Pengawas Static")->first(),
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-40/form-header', $data);
    }

    public function createHeaderAction()
    {
        $bc40Model = new BC40Model();
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $bc40Model->get($penerimaanBarangID);
        if ($lastData == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $bc40Model->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_no_lokal' => $bc40Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

                'kode_kantor' => decrypt($this->request->getVar('header_kantor_pabean')),
                'kode_jenis_tpb' => decrypt($this->request->getVar('header_kode_jenis_tpb')),
                'kode_tujuan_pengiriman' => decrypt($this->request->getVar('header_kode_tujuan_pengiriman'))
            ]);
        } else {
            // update
            $bc40Model->update($lastData['id'], [
                'kode_kantor' => decrypt($this->request->getVar('header_kantor_pabean')),
                'kode_jenis_tpb' => decrypt($this->request->getVar('header_kode_jenis_tpb')),
                'kode_tujuan_pengiriman' => decrypt($this->request->getVar('header_kode_tujuan_pengiriman'))
            ]);
        }

        $penerimaanBarangModel->update($penerimaanBarangID, [
            "tanggal" => $this->request->getVar("tanggal_penerimaan_barang") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_barang")), "Y-m-d") : "",
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Header berhasil diupdate",
        ]);
    }

    public function createEntitasView($penerimaanBarangID)
    {
        $bc40Model = new BC40Model();
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $metaDataModel = new MetadataModel();
        $nomorIjinTPBModel = new NomorIjinTPBModel();
        $bcEntitasModel = new BCEntitasModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);
        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $bc40 = $bc40Model->get($penerimaanBarangID);

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $data = [
            'bc40' => $bc40,
            'npwpDefault' => $metaDataModel->where('name', "NPWP Importir Default BC")->first(),
            'namaImportirDefault' => $metaDataModel->where('name', "Nama Importir Default BC")->first(),
            'alamatImportirDefault' => $metaDataModel->where('name', "Alamat Importir Default BC")->first(),
            'nibDefault' => $metaDataModel->where('name', "NIB Default BC")->first(),
            'nomorIjinTPB' => $nomorIjinTPBModel->findAll(),
            'bcEntitas' => $bcEntitasModel->get($penerimaanBarangID),
            'lpb' => $lpb

        ];

        return view('BeaCukai/bc-40/form-entitas', $data);
    }

    public function createEntitasAction()
    {
        $bcEntitasModel = new BCEntitasModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $bcEntitasModel->get($penerimaanBarangID);
        if ($lastData == null) {
            // insert
            $bcEntitasModel->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_type' => 40,
                'alamat_entitas' => $this->request->getVar('pengusaha_tpb_alamat'),
                'nama_entitas' => $this->request->getVar('pengusaha_tpb_nama'),
                'nib_entitas' => $this->request->getVar('pengusaha_tpb_nib'),
                'nomor_identitas' => $this->request->getVar('pengusaha_tpb_npwp'),
                'nomor_ijin_entitas' => $this->request->getVar('pengusaha_tpb_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('pengirim_nama'),
                'alamat_pemasok' => $this->request->getVar('pengirim_alamat'),
                'npwp_pemasok' => $this->request->getVar('pengirim_npwp'),
                'npwp_pemilik_barang' => $this->request->getVar('pemilik_barang_npwp'),
                'nama_pemilik_barang' => $this->request->getVar('pemilik_barang_nama'),
                'alamat_pemilik_barang' => $this->request->getVar('pemilik_barang_alamat')
            ]);
        } else {
            // update
            $bcEntitasModel->update($lastData['id'], [
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_type' => 40,
                'alamat_entitas' => $this->request->getVar('pengusaha_tpb_alamat'),
                'nama_entitas' => $this->request->getVar('pengusaha_tpb_nama'),
                'nib_entitas' => $this->request->getVar('pengusaha_tpb_nib'),
                'nomor_identitas' => $this->request->getVar('pengusaha_tpb_npwp'),
                'nomor_ijin_entitas' => $this->request->getVar('pengusaha_tpb_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('pengirim_nama'),
                'alamat_pemasok' => $this->request->getVar('pengirim_alamat'),
                'npwp_pemasok' => $this->request->getVar('pengirim_npwp'),
                'npwp_pemilik_barang' => $this->request->getVar('pemilik_barang_npwp'),
                'nama_pemilik_barang' => $this->request->getVar('pemilik_barang_nama'),
                'alamat_pemilik_barang' => $this->request->getVar('pemilik_barang_alamat')
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Entitas berhasil diupdate",
        ]);
    }

    public function createDokumenView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $metaDataModel = new MetadataModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'kodeDokumen' => $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-40/form-dokumen', $data);
    }

    public function allDokumen()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $condition = [
            "bc_dokumen.deletedAt"  => null,
            "bc_dokumen.penerimaan_barang_id" => $penerimaanBarangID,
            "bc_dokumen.bc_type" => 40
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $BCDokumenModel = new BCDokumenModel();
        $metaDataModel = new MetadataModel();


        $beaCukaiDokumen = $BCDokumenModel->getList($condition, $limit, $offset);
        $resDokumen = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;



        foreach ($beaCukaiDokumen['data'] as $data) {
            $jenisDokumen = $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->where('description', $data->kode_dokumen)->first();
            array_push($resDokumen, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_barang_id"  => encrypt($data->penerimaan_barang_id),
                "kode_dokumen"          => $data->kode_dokumen . ' - ' . $jenisDokumen['value'],
                "nomor_dokumen"         => $data->nomor_dokumen,
                "seri_dokumen"          => $data->seri_dokumen,
                "tanggal_dokumen"       => date('d/m/Y', strtotime($data->tanggal_dokumen))
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiDokumen['totalData'],
            "recordsFiltered"   => $beaCukaiDokumen['totalFilteredData'],
            "data"              => $resDokumen,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createDokumenAction()
    {
        $BCDokumenModel = new BCDokumenModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $BCDokumenModel->where('penerimaan_barang_id', $penerimaanBarangID)->orderBy('createdAt', "DESC")->first();
        $seriDokumen = $lastData == null ? 1 : $lastData['seri_dokumen'] + 1;

        $BCDokumenModel->insert([
            'bc_type' => 40,
            'penerimaan_barang_id' => $penerimaanBarangID,
            'id_dokumen' => generateUniqueCode(5),
            'nomor_dokumen' => $this->request->getVar('dokumen_nomor_dokumen'),
            'seri_dokumen' => $seriDokumen,
            'kode_dokumen' => decrypt($this->request->getVar('dokumen_jenis_dokumen')),
            'tanggal_dokumen' => $this->request->getVar('dokumen_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('dokumen_tanggal')), "Y-m-d") : "",
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Dokumen berhasil ditambah",
        ]);
    }

    public function deleteDokumenAction()
    {
        $BCDokumenModel = new BCDokumenModel();
        $id = decrypt($this->request->getVar('id'));
        $BCDokumenModel->delete($id);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Dokumen berhasil dihapus",
        ]);
    }

    public function createPengangkutView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $BCPengangkutModel = new BCPengangkutModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'bcPengangkut' => $BCPengangkutModel->get($penerimaanBarangID),
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-40/form-pengangkut', $data);
    }

    public function createPengangkutAction()
    {
        $BCPengangkutModel = new BCPengangkutModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $lastData = $BCPengangkutModel->get($penerimaanBarangID);

        if ($lastData == null) {
            $BCPengangkutModel->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'nama_sarana_pengangkut' => $this->request->getVar('jenis_sarana_pengangkut'),
                'nomor_pengangkut' => $this->request->getVar('nomor_sarana_pengangkut'),
                'seri_pengangkut' => 1,
                'bc_type' => 40
            ]);
        } else {
            $BCPengangkutModel->update($lastData['id'], [
                'penerimaan_barang_id' => $penerimaanBarangID,
                'nama_sarana_pengangkut' => $this->request->getVar('jenis_sarana_pengangkut'),
                'nomor_pengangkut' => $this->request->getVar('nomor_sarana_pengangkut'),
                'seri_pengangkut' => 1,
                'bc_type' => 40
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pengangkut berhasil diupdate"
        ]);
    }

    public function createKemasanPetiKemas($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $metaDataModel = new MetadataModel();
        $BCKemasanModel = new BCKemasanModel();
        $BCKontainerModel = new BCKontainerModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $kemasanLast = $BCKemasanModel->getLast($penerimaanBarangID);
        $kontainerLast = $BCKontainerModel->getLast($penerimaanBarangID);


        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'kodeJenisKemasan' => $metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'seriKemasan' => $kemasanLast == null ? 1 : $kemasanLast['seri_kemasan'] + 1,
            'seriKontainer' => $kontainerLast == null ? 1 : $kontainerLast['seri_kontainer'] + 1,
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-40/form-kemasan-peti-kemas', $data);
    }

    public function allKemasan()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $condition = [
            "bc_kemasan.deletedAt"  => null,
            "bc_kemasan.penerimaan_barang_id" => $penerimaanBarangID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $BCKemasanModel = new BCKemasanModel();
        $metaDataModel = new MetadataModel();

        $bc40Kemasan = $BCKemasanModel->getList($condition, $limit, $offset);
        $resKemasan = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc40Kemasan['data'] as $data) {
            $kemasan = $metaDataModel->where('name', 'Jenis Kemasan')->where('description', $data->kode_jenis_kemasan)->first();
            array_push($resKemasan, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_barang_id"  => encrypt($data->penerimaan_barang_id),
                "jumlah_kemasan"        => $data->jumlah_kemasan,
                "kode_jenis_kemasan"    => $data->kode_jenis_kemasan . ' - ' . $kemasan['value'],
                "merk_kemasan"          => $data->merk_kemasan,
                "seri_kemasan"          => $data->seri_kemasan
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $bc40Kemasan['totalData'],
            "recordsFiltered"   => $bc40Kemasan['totalFilteredData'],
            "data"              => $resKemasan,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function allKontainer()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $condition = [
            "bc_kontainer.deletedAt"  => null,
            "bc_kontainer.penerimaan_barang_id" => $penerimaanBarangID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $BCKontainerModel = new BCKontainerModel();
        $metaDataModel = new MetadataModel();

        $bc40Kontainer = $BCKontainerModel->getList($condition, $limit, $offset);
        $resKontainer = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc40Kontainer['data'] as $data) {
            $ukuranKontainer = $metaDataModel->where('name', "Kode Ukuran Kontainer BC")->where('value', $data->kode_ukuran_kontainer)->first();
            $jenisKontainer = $metaDataModel->where('name', "Jenis Kontainer")->where('description', $data->kode_jenis_kontainer)->first();
            $tipeKontainer = $metaDataModel->where('name', "Kode Tipe Kontainer BC")->where('value', $data->kode_tipe_kontainer)->first();

            array_push($resKontainer, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_barang_id"  => encrypt($data->penerimaan_barang_id),
                "kode_tipe_kontainer"   => $data->kode_tipe_kontainer . ' - ' . $tipeKontainer['description'],
                "kode_ukuran_kontainer" => $ukuranKontainer['description'],
                "nomor_kontainer"       => $data->nomor_kontainer,
                "seri_kontainer"        => $data->seri_kontainer,
                "kode_jenis_kontainer"  => $data->kode_jenis_kontainer . ' - ' . $jenisKontainer['value']
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $bc40Kontainer['totalData'],
            "recordsFiltered"   => $bc40Kontainer['totalFilteredData'],
            "data"              => $resKontainer,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createKemasanAction()
    {
        $BCKemasanModel = new BCKemasanModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $BCKemasanModel->insert([
            'bc_type' => 40,
            'penerimaan_barang_id' => $penerimaanBarangID,
            'seri_kemasan' => $this->request->getVar('kemasan_seri_kemasan'),
            'jumlah_kemasan' => $this->request->getVar('kemasan_jumlah_kemasan'),
            'kode_jenis_kemasan' => decrypt($this->request->getVar('kemasan_jenis_kemasan')),
            'merk_kemasan' => $this->request->getVar('kemasan_merk_kemasan')
        ]);

        $kemasanLast = $BCKemasanModel->getLast($penerimaanBarangID);

        return response()->setJSON([
            'status' => true,
            'kemasan_seri_kemasan' => $kemasanLast != null ? $kemasanLast['seri_kemasan'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil ditambahkan"
        ]);
    }

    public function deleteKemasanAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $BCKemasanModel = new BCKemasanModel();
        $penerimaanBarangID = $BCKemasanModel->find($id)['penerimaan_barang_id'];
        $BCKemasanModel->delete($id);

        $kemasanLast = $BCKemasanModel->getLast($penerimaanBarangID);

        return response()->setJSON([
            'status' => true,
            'kemasan_seri_kemasan' => $kemasanLast != null ? $kemasanLast['seri_kemasan'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil dihapus"
        ]);
    }

    public function createKontainerAction()
    {
        $BCKontainerModel = new BCKontainerModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $BCKontainerModel->insert([
            'bc_type' => 40,
            'penerimaan_barang_id' => $penerimaanBarangID,
            'nomor_kontainer' => $this->request->getVar('kontainer_nomor'),
            'kode_ukuran_kontainer' => decrypt($this->request->getVar('kontainer_ukuran')),
            'kode_jenis_kontainer' => decrypt($this->request->getVar('kontainer_jenis')),
            'kode_tipe_kontainer' => decrypt($this->request->getVar('kontainer_tipe')),
            'seri_kontainer' => $this->request->getVar('kontainer_seri')
        ]);

        $kontainerLast = $BCKontainerModel->getLast($penerimaanBarangID);

        return response()->setJSON([
            'status' => true,
            'kontainer_seri' => $kontainerLast != null ? $kontainerLast['seri_kontainer'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kontainer berhasil ditambahkan"
        ]);
    }

    public function deleteKontainerAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $BCKontainerModel = new BCKontainerModel();
        $penerimaanBarangID = $BCKontainerModel->find($id)['penerimaan_barang_id'];
        $BCKontainerModel->delete($id);

        $kontainerLast = $BCKontainerModel->getLast($penerimaanBarangID);

        return response()->setJSON([
            'status' => true,
            'kontainer_seri' => $kontainerLast != null ? $kontainerLast['seri_kontainer'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kontainer berhasil dihapus"
        ]);
    }


    public function createTransaksiView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $bc40Model = new BC40Model();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $lastData = $bc40Model->get($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        if ($lastData != null) {
            $lastData['harga_penyerahan'] = ($lastData['harga_penyerahan']);
            $lastData['nilai_jasa'] = ($lastData['nilai_jasa']);
            $lastData['uang_muka'] = ($lastData['uang_muka']);
            $lastData['harga_perolehan'] = ($lastData['harga_perolehan']);
            $lastData['volume'] = ($lastData['volume']);
            $lastData['bruto'] = ($lastData['bruto']);
            $lastData['netto'] = ($lastData['netto']);
        }

        $data = [
            'lpb' => $lpb,
            'bc40' => $lastData,
        ];

        return view('BeaCukai/bc-40/form-transaksi', $data);
    }

    public function createTransaksiAction()
    {
        $bc40Model = new BC40Model();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $bc40Model->get($penerimaanBarangID);
        if ($lastData == null) {
            // insert
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $bc40Model->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_no_lokal' => $bc40Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

                'harga_penyerahan' => convertRupiahToNumber($this->request->getVar('harga_nilai_pabean')),
                'nilai_jasa' => convertRupiahToNumber($this->request->getVar('nilai_jasa')),
                'uang_muka' => convertRupiahToNumber($this->request->getVar('nilai_uang_muka')),
                'harga_perolehan' => convertRupiahToNumber($this->request->getVar('harga_perolehan')),
                'volume' => convertRupiahToNumber($this->request->getVar('volume')),
                'bruto' => convertRupiahToNumber($this->request->getVar('berat_kotor')),
                'netto' => convertRupiahToNumber($this->request->getVar('berat_bersih')),
            ]);
        } else {
            $bc40Model->update($lastData['id'], [
                'harga_penyerahan' => convertRupiahToNumber($this->request->getVar('harga_nilai_pabean')),
                'nilai_jasa' => convertRupiahToNumber($this->request->getVar('nilai_jasa')),
                'uang_muka' => convertRupiahToNumber($this->request->getVar('nilai_uang_muka')),
                'harga_perolehan' => convertRupiahToNumber($this->request->getVar('harga_perolehan')),
                'volume' => convertRupiahToNumber($this->request->getVar('volume')),
                'bruto' => convertRupiahToNumber($this->request->getVar('berat_kotor')),
                'netto' => convertRupiahToNumber($this->request->getVar('berat_bersih')),
            ]);
        }

        return response()->setJSON([
            'message' => "Transaksi berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function createBarangView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'lpb' => $lpb,
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($penerimaanBarangID, $lpb->tipe_bahan, $lpb->status_penerimaan)
        ];

        return view('BeaCukai/bc-40/form-barang', $data);
    }

    public function createBarangDetailView($penerimaanBarangID, $penerimaanBarangDetailID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $metaDataModel = new MetadataModel();
        $hsCodeModel = new HsCodesModel();
        $BCBarangModel = new BCBarangModel();
        $rmImportPoDetailModel = new RMImportPODetailModel(); // import bahan baku
        $rmLokalPoDetailModel = new RMPurchaseOrderDetailModel(); // lokal bahan baku
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel(); // lokal | import penolong
        $supplierHargaModel = new SupplierHargaModel();
        $barangMasterModel = new BarangMasterModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);
        $penerimaanBarangDetailID = decrypt($penerimaanBarangDetailID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $lpbDetail = $penerimaanBarangDetailModel->find($penerimaanBarangDetailID);
        $poDetail = null;
        $barangDetail = null;
        $kodeSatuanBarang = null;
        $bc40DokumenBarang = null;

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        if ($lpb == null || $lpbDetail == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        // get po detail
        if ($lpb->status_penerimaan == "LOKAL" && $lpb->tipe_bahan == "BAKU") {
            // PO LOKAL BAKU 
            $poDetail = $rmLokalPoDetailModel->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id')
                ->where('penerimaan_barang_detail.id', $penerimaanBarangDetailID)
                ->first();

            $barangDetail = $supplierHargaModel->select('barang_master.*')->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id')
                ->where('supplier_harga.id', $poDetail['supplier_harga_id'])
                ->first();
        } else if ($lpb->status_penerimaan == "IMPORT" && $lpb->tipe_bahan == "BAKU") {
            // PO IMPORT BAKU
            $poDetail = $rmImportPoDetailModel->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_import_po_details.id')
                ->where('penerimaan_barang_detail.id', $penerimaanBarangDetailID)
                ->first();

            $barangDetail = $barangMasterModel->find($poDetail['barang_id']);
        } else {
            // PO LOKAL | IMPORT PENOLONG
            $poDetail = $amPurchaseOrderDetailModel->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = am_purchase_order_details.id')
                ->where('penerimaan_barang_detail.id', $penerimaanBarangDetailID)
                ->first();

            $barangDetail = $barangMasterModel->find($poDetail['barang_id']);
        }

        $seriBarang = $BCBarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->orderBy('createdAt', "DESC")->first();
        $bc40DokumenBarang =  $BCBarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)->first();

        if ($bc40DokumenBarang != null) {
            $kodeSatuanBarang = $metaDataModel->where('name', "Kode Satuan BC")->where('value', $bc40DokumenBarang['kode_satuan_barang'])->first();
        }

        $data = [
            'kodeFasilitasTarif' => $metaDataModel->where('name', "Kode Fasilitas Tarif BC")->whereIn('description', ['TIDAK DIPUNGUT', 'DIBEBASKAN', 'DITANGGUHKAN'])->findAll(),
            'kodeJenisTarif' => $metaDataModel->where('name', "Kode Jenis Tarif BC")->findAll(),
            'kodeJenisPungutan' => $metaDataModel->where('name', "Kode Jenis Pungutan BC")->whereIn('value', ['PPN'])->findAll(),
            'kodeHS' => $hsCodeModel->findAll(),
            'kodeJenisKemasan' => $metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'lpb' => $lpb,
            'lpbDetail' => $lpbDetail,
            'poDetail' => $poDetail,
            'barangDetail' => $barangDetail,
            'seriBarang' => $seriBarang == null ? 1 : $seriBarang['seri_barang'] + 1,
            'bc40DokumenBarang' => $bc40DokumenBarang,
            'kodeSatuanBarang' => $kodeSatuanBarang,
        ];

        $diskon = array_key_exists('disc', $data['poDetail']) ? ($data['poDetail']['disc'] == null ? 0 : $data['poDetail']['disc']) : 0;

        if (array_key_exists('price', $data['poDetail'])) {
            // PO LOKAL BP
            $data['hargaSebelumDiskon'] = ($data['poDetail']['price'] + $data['poDetail']['additional_cost']) * $data['lpbDetail']['qty'];
            $data['diskon'] = $data['hargaSebelumDiskon'] * ($diskon / 100);
        } else {
            // PO LOKAL BB
            $data['hargaSebelumDiskon'] = ($data['poDetail']['general_price'] + $data['poDetail']['daily_price'] + $data['poDetail']['monthly_price']) * $data['lpbDetail']['qty'];
            $data['diskon'] = $data['hargaSebelumDiskon'] * ($diskon / 100);
        }


        return view('BeaCukai/bc-40/form-detail-barang', $data);
    }

    public function createBarangDetailAction()
    {
        $BCBarangModel = new BCBarangModel();
        $BC40Model = new BC40Model();

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $penerimaanBarangDetailID = decrypt($this->request->getVar('penerimaan_barang_detail_id'));

        $bcBarang = $BCBarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)->first();

        if ($bcBarang == null) {
            $BCBarangModel->insert([
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'penerimaan_barang_detail_id' => decrypt($this->request->getVar('penerimaan_barang_detail_id')),
                'bc_type' => 40,
                'kode_dokumen' => 40,
                'seri_barang' => $this->request->getVar('barang_detail_seri_barang'),
                'pos_tarif' => decrypt($this->request->getVar('barang_detail_kode_hs')),
                'kode_barang' => $this->request->getVar('barang_detail_kode_barang'),
                'uraian' => $this->request->getVar('barang_detail_uraian'),
                'merk_barang' => $this->request->getVar('barang_detail_merk_barang'),
                'tipe_barang' => $this->request->getVar('barang_detail_tipe_barang'),
                'ukuran_barang' => $this->request->getVar('barang_detail_ukuran_barang'),
                'spesifikasi_lain' => $this->request->getVar('barang_detail_spesifikasi_lain'),
                'jumlah_satuan' => $this->request->getVar('barang_detail_jumlah_satuan'),
                'kode_satuan_barang' => decrypt($this->request->getVar('barang_detail_kode_satuan_barang')),
                'jumlah_kemasan' => $this->request->getvar('barang_detail_jumlah_kemasan'),
                'kode_jenis_kemasan' => decrypt($this->request->getVar('barang_detail_kode_jenis_kemasan')),
                'netto' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_berat_bersih'))),
                'harga_ekspor' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_harga_penyerahan'))),
                'nilai_tambah' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_harga_penggantian'))),
                'diskon' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_diskon'))),

            ]);
        } else {
            $BCBarangModel->update($bcBarang['id'], [
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'penerimaan_barang_detail_id' => decrypt($this->request->getVar('penerimaan_barang_detail_id')),
                'bc_type' => 40,
                'kode_dokumen' => 40,
                'seri_barang' => $this->request->getVar('barang_detail_seri_barang'),
                'pos_tarif' => decrypt($this->request->getVar('barang_detail_kode_hs')),
                'kode_barang' => $this->request->getVar('barang_detail_kode_barang'),
                'uraian' => $this->request->getVar('barang_detail_uraian'),
                'merk_barang' => $this->request->getVar('barang_detail_merk_barang'),
                'tipe_barang' => $this->request->getVar('barang_detail_tipe_barang'),
                'ukuran_barang' => $this->request->getVar('barang_detail_ukuran_barang'),
                'spesifikasi_lain' => $this->request->getVar('barang_detail_spesifikasi_lain'),
                'jumlah_satuan' => $this->request->getVar('barang_detail_jumlah_satuan'),
                'kode_satuan_barang' => decrypt($this->request->getVar('barang_detail_kode_satuan_barang')),
                'jumlah_kemasan' => $this->request->getvar('barang_detail_jumlah_kemasan'),
                'kode_jenis_kemasan' => decrypt($this->request->getVar('barang_detail_kode_jenis_kemasan')),
                'netto' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_berat_bersih'))),
                'harga_ekspor' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_harga_penyerahan'))),
                'nilai_tambah' => trim(convertRupiahToNumber(trim($this->request->getVar('barang_detail_harga_penggantian')))),
                'diskon' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_diskon'))),
            ]);
        }

        // UPDATE DATA DI TRANSAKSI
        $bc40 = $BC40Model->get($penerimaanBarangID);

        if ($bc40 == null) {
            $BC40Model->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'netto' => $BCBarangModel->totalBeratBersih($penerimaanBarangID),
                'harga_penyerahan' => $BCBarangModel->totalHargaPenyerahan($penerimaanBarangID)
            ]);
        } else {
            $BC40Model->update($bc40['id'], [
                'penerimaan_barang_id' => $penerimaanBarangID,
                'netto' => $BCBarangModel->totalBeratBersih($penerimaanBarangID),
                'harga_penyerahan' => $BCBarangModel->totalHargaPenyerahan($penerimaanBarangID)
            ]);
        }

        return response()->setJSON([
            'message' => "Detail Barang Dokumen Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function allPungutan()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $penerimaanBarangDetailID = decrypt($this->request->getVar('penerimaan_barang_detail_id'));

        $condition = [
            "bc_barang_tarif.deletedAt"  => null,
            "bc_barang_tarif.penerimaan_barang_id" => $penerimaanBarangID,
            "bc_barang_tarif.penerimaan_barang_detail_id" => $penerimaanBarangDetailID
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $BCBarangTarifModel = new BCBarangTarifModel();
        $metaDataModel = new MetadataModel();

        $bc40BarangTarif = $BCBarangTarifModel->getList($condition, $limit, $offset);
        $res = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc40BarangTarif['data'] as $data) {
            $kodeJenisPungutan = $metaDataModel->where('name', "Kode Jenis Pungutan BC")->where('value', $data->kode_jenis_pungutan)->first();
            $kodeJenisTarif = $metaDataModel->where('name', "Kode Jenis Tarif BC")->where('value', $data->kode_jenis_tarif)->first();
            $kodeFasilitasTarif =  $metaDataModel->where('name', "Kode Fasilitas Tarif BC")->where('value', $data->kode_fasilitas_tarif)->first();

            array_push($res, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "kode_jenis_pungutan"   => $data->kode_jenis_pungutan . ' - ' . $kodeJenisPungutan['description'],
                "kode_jenis_tarif"      => $data->kode_jenis_tarif . ' - ' . $kodeJenisTarif['description'],
                "tarif_bea_masuk"       => ($data->tarif_bea_masuk),
                "kode_fasilitas_tarif"  => $data->kode_fasilitas_tarif . ' - ' . $kodeFasilitasTarif['description'],
                "tarif_fasilitas"       => ($data->tarif_fasilitas)
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $bc40BarangTarif['totalData'],
            "recordsFiltered"   => $bc40BarangTarif['totalFilteredData'],
            "data"              => $res,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createPungutanAction()
    {
        $BCBarangTarifModel = new BCBarangTarifModel();
        $BCBarangModel = new BCBarangModel();
        $metaDataModel = new MetadataModel();

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $penerimaanBarangDetailID = decrypt($this->request->getVar('penerimaan_barang_detail_id'));

        $bc40Barang = $BCBarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)->first();

        if ($bc40Barang == null) {
            return response()->setJSON([
                'message' => "Isi dokumen barang detail terlebih dahulu sebelum mengisi pungutan & dokumen barang",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $pungutan = $BCBarangTarifModel->where('penerimaan_barang_id', $penerimaanBarangID)
            ->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)
            ->where('kode_jenis_pungutan', decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')))
            ->first();

        if ($pungutan != null) {
            $kodeJenisPungutan = $metaDataModel->where('name', "Kode Jenis Pungutan BC")->where('value', $pungutan['kode_jenis_pungutan'])->first();
            return response()->setJSON([
                'message' => "Jenis pungutan " . $kodeJenisPungutan['description'] . " sudah ada",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $nilaiTarif =  ($this->request->getVar('barang_detail_nilai_tarif'));
        $tarifFasilitas = ($this->request->getVar('barang_detail_tarif_fasilitas'));
        $tarifFasilitas = ($tarifFasilitas == 0) ? 1 : $tarifFasilitas;

        $nilaiBayar100 = $bc40Barang['harga_ekspor'] * ($nilaiTarif / 100);
        $nilaiBayar = $nilaiBayar100 / $tarifFasilitas;


        $BCBarangTarifModel->insert([
            'penerimaan_barang_id' => $penerimaanBarangID,
            'penerimaan_barang_detail_id' => $penerimaanBarangDetailID,
            'bc_type' => 40,
            'kode_jenis_pungutan' => decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')),
            'kode_jenis_tarif' => decrypt($this->request->getVar('barang_detail_kode_jenis_tarif')),
            'tarif_bea_masuk' => convertRupiahToNumber($this->request->getVar('barang_detail_nilai_tarif')),
            'kode_fasilitas_tarif' => decrypt($this->request->getVar('barang_detail_kode_fasilitas_tarif')),
            'tarif_fasilitas' => convertRupiahToNumber($this->request->getVar('barang_detail_tarif_fasilitas')),
            'seri_barang' => $bc40Barang['seri_barang'],
            'kode_satuan_barang' => $bc40Barang['kode_satuan_barang'],
            'nilai_bayar' => $nilaiBayar
        ]);

        return response()->setJSON([
            'message' => "Pungutan berhasil ditambahkan",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function deletePungutanAction()
    {
        $BCBarangTarifModel = new BCBarangTarifModel();
        $BCBarangTarifModel->delete(decrypt($this->request->getVar('id')));

        return response()->setJSON([
            'message' => "Pungutan berhasil dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function allDokumenBarang()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $penerimaanBarangDetailID = decrypt($this->request->getVar('penerimaan_barang_detail_id'));

        $condition = [
            "bc_dokumen.deletedAt"  => null,
            "bc_dokumen.penerimaan_barang_id" => $penerimaanBarangID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $BCDokumenModel = new BCDokumenModel();
        $metaDataModel = new MetadataModel();
        $BCBarangDokumenModel = new BCBarangDokumenModel();

        $beaCukaiDokumen = $BCDokumenModel->getList($condition, $limit, $offset);
        $resDokumen = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiDokumen['data'] as $data) {
            $jenisDokumen = $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->where('description', $data->kode_dokumen)->first();
            $barangDokumen = $BCBarangDokumenModel->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)
                ->where('seri_dokumen', $data->seri_dokumen)->where('deletedAt', null)
                ->first();

            array_push($resDokumen, [
                "no"                    => $no++,
                "bc_dokumen_id"      => encrypt($data->id),
                "penerimaan_barang_id"  => encrypt($data->penerimaan_barang_id),
                "kode_dokumen"          => $data->kode_dokumen . ' - ' . $jenisDokumen['value'],
                "nomor_dokumen"         => $data->nomor_dokumen,
                "seri_dokumen"          => $data->seri_dokumen,
                "tanggal_dokumen"       => date('d/m/Y', strtotime($data->tanggal_dokumen)),
                "is_used"               => $barangDokumen == null ? false : true,
                "barang_dokumen_id"     => $barangDokumen == null ? 0 : encrypt($barangDokumen['id'])
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiDokumen['totalData'],
            "recordsFiltered"   => $beaCukaiDokumen['totalFilteredData'],
            "data"              => $resDokumen,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createBarangDokumenAction()
    {
        $BCBarangDokumenModel = new BCBarangDokumenModel();

        $barangDokumenID = decrypt($this->request->getVar('bc_dokumen_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $penerimaanBarangDetailID = decrypt($this->request->getVar('penerimaan_barang_detail_id'));

        $BCBarangDokumenModel->insert([
            'penerimaan_barang_id' => $penerimaanBarangID,
            'penerimaan_barang_detail_id' => $penerimaanBarangDetailID,
            'bc_dokumen_id' => $barangDokumenID,
            'bc_type' => 40,
            'seri_dokumen' => $this->request->getVar('seri_dokumen')
        ]);

        return response()->setJSON([
            'message' => "Dokumen berhasil ditambahkan",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function deleteBarangDokumenAction()
    {
        $BCBarangDokumenModel = new BCBarangDokumenModel();
        $BCBarangDokumenModel->delete(decrypt($this->request->getVar('id')));

        return response()->setJSON([
            'message' => "Dokumen berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function createPungutanView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $BCBarangTarifModel = new BCBarangTarifModel();
        $metaDataModel = new MetadataModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $kodeJenisPungutan = $metaDataModel->where('name', "Kode Jenis Pungutan BC")->whereIn('value', ['PPN'])->findAll();

        $barangTarif = $BCBarangTarifModel
            ->where('penerimaan_barang_id', $penerimaanBarangID)
            ->findAll();

        $pungutanList = [];

        $ppnDitangguhkan = 0;
        $ppnDibebaskan = 0;
        $ppnTidakDipungut = 0;

        foreach ($barangTarif as $b) {
            if ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 3) {
                $ppnDitangguhkan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 5) {
                $ppnDibebaskan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 6) {
                $ppnTidakDipungut += $b['nilai_bayar'];
            }
        }

        foreach ($kodeJenisPungutan as $k) {
            if ($k['value'] == "PPN") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'tidak_dipungut' => $ppnTidakDipungut,
                    'dibebaskan' => $ppnDibebaskan,
                    'ditangguhkan' => $ppnDitangguhkan

                ];
            }
        }

        $data = [
            'lpb' => $lpb,
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($penerimaanBarangID, $lpb->tipe_bahan, $lpb->status_penerimaan),
            'pungutanList' => $pungutanList,
            'barangTarif' => $barangTarif
        ];

        return view('BeaCukai/bc-40/form-pungutan', $data);
    }

    public function createPernyataanView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $BC40Model = new BC40Model();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $bc40 = $BC40Model->get($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 4.0") {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'lpb' => $lpb,
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($penerimaanBarangID, $lpb->tipe_bahan, $lpb->status_penerimaan),
            'bc40' => $bc40

        ];

        return view('BeaCukai/bc-40/form-pernyataan', $data);
    }

    public function createPernyataanAction()
    {
        $bc40Model = new bc40Model();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $bc40Model->get($penerimaanBarangID);

        if ($lastData == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $bc40Model->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_no_lokal' => $bc40Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

                'nama_ttd' => $this->request->getVar('pernyatan_nama'),
                'kota_ttd' => $this->request->getVar('pernyatan_tempat'),
                'tanggal_ttd' => $this->request->getVar('pernyataan_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pernyataan_tanggal')), "Y-m-d") : "",
                'jabatan_ttd' => $this->request->getVar('pernyatan_jabatan'),
            ]);
        } else {
            // update
            $bc40Model->update($lastData['id'], [
                'penerimaan_barang_id' => $penerimaanBarangID,
                'nama_ttd' => $this->request->getVar('pernyatan_nama'),
                'kota_ttd' => $this->request->getVar('pernyatan_tempat'),
                'tanggal_ttd' => $this->request->getVar('pernyataan_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pernyataan_tanggal')), "Y-m-d") : "",
                'jabatan_ttd' => $this->request->getVar('pernyatan_jabatan')
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pernyataan berhasil diupdate",
        ]);
    }

    public function updateNoAju()
    {
        $bc40Model = new BC40Model();

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $noAju = $this->request->getVar('no_pengajuan');

        $bc40 = $bc40Model->where('no_aju', $noAju)->whereNotIn('penerimaan_barang_id', [$penerimaanBarangID])->first();

        if ($bc40 != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Nomor aju sudah ada",
                'status' => false,
            ]);
        }

        $bc40Model->set('no_aju', $noAju)->where('penerimaan_barang_id', $penerimaanBarangID)->update();
        return response()->setJSON([
            'message' => "No Aju berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function delete()
    {
        $bc40Model = new BC40Model();
        $BCBarangModel = new BCBarangModel();
        $BCBarangDokumenModel = new BCBarangDokumenModel();
        $BCBarangTarifModel = new BCBarangTarifModel();
        $BCEntitasModel = new BCEntitasModel();
        $BCKemasanModel = new BCKemasanModel();
        $BCKontainerModel = new BCKontainerModel();
        $BCPengangkutModel = new BCPengangkutModel();
        $BCDokumenModel = new BCDokumenModel();
        $BCPengangkutModel = new BCPengangkutModel();

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $bc40Model->where('penerimaan_barang_id', $penerimaanBarangID)->delete();
        $BCBarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->delete();
        $BCEntitasModel->where('penerimaan_barang_id', $penerimaanBarangID)->delete();
        $BCKemasanModel->where('penerimaan_barang_id', $penerimaanBarangID)->delete();
        $BCKontainerModel->where('penerimaan_barang_id', $penerimaanBarangID)->delete();
        $BCPengangkutModel->where('penerimaan_barang_id', $penerimaanBarangID)->delete();
        $BCDokumenModel->where('penerimaan_barang_id', $penerimaanBarangID)->delete();
        $BCBarangDokumenModel->where('penerimaan_barang_id', $penerimaanBarangID)->delete();
        $BCBarangTarifModel->where('penerimaan_barang_id', $penerimaanBarangID)->delete();

        return response()->setJSON([
            'message' => "Dokumen BC 4.0 Berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    // KIRIM BC.40 KE CEISA
    public function kirimCeisa($penerimaanBarangID)
    {
        $bc40Model = new BC40Model();
        $BCBarangModel = new BCBarangModel();
        $BCEntitasModel = new BCEntitasModel();
        $BCKemasanModel = new BCKemasanModel();
        $BCKontainerModel = new BCKontainerModel();
        $BCPengangkutModel = new BCPengangkutModel();
        $BCDokumenModel = new BCDokumenModel();
        $BCPengangkutModel = new BCPengangkutModel();
        $BCBarangTarifModel = new BCBarangTarifModel();
        $beacukaiApi = new BeaCukaiApi();

        $penerimaanBarangID = decrypt($penerimaanBarangID);
        $bc40Data = $bc40Model->get($penerimaanBarangID);

        if ($bc40Data == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $bc23Kontainer = $BCKontainerModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->findAll();
        $bc23Barang = $BCBarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->findAll();
        $bc23Entitas = $BCEntitasModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->findAll();
        $bc23Kemasan = $BCKemasanModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->findAll();
        $bc23Dokumen = $BCDokumenModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->findAll();
        $bc23Pengangkut = $BCPengangkutModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->findAll();
        $bcBarangTarif = $BCBarangTarifModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->findAll();

        $payload = $beacukaiApi->payloadTempleateKirimBC40(
            $bc40Data,
            $bc23Kontainer,
            $bc23Barang,
            $bc23Entitas,
            $bc23Kemasan,
            $bc23Dokumen,
            $bc23Pengangkut,
            $bcBarangTarif
        );

        // return response()->setJSON($payload);
        $res = $beacukaiApi->kirimDokumenBC23($payload, false);
        return response()->setJSON($res);
    }


    // Navigator display
    private function setFlashDataNavigatorSession($penerimaanBarangID)
    {
        $bc40Model = new BC40Model();
        $isCompleteFormHeader = $bc40Model->isCompleteFormHeader($penerimaanBarangID);
        $isCompleteFormEntitas = $bc40Model->isCompleteFormEntitas($penerimaanBarangID);
        $isCompleteFormDokumen = $bc40Model->isCompleteFormDokumen($penerimaanBarangID);
        $isCompleteFormPengangkut = $bc40Model->isCompleteFormPengangkut($penerimaanBarangID);
        $isCompleteFormPetiKemas = $bc40Model->isCompleteFormPetiKemas($penerimaanBarangID);
        $isCompleteFormFormTransaksi = $bc40Model->isCompleteFormTransaksi($penerimaanBarangID);
        $isCompleteFormBarang = $bc40Model->isCompleteFormBarang($penerimaanBarangID);
        $isCompleteFormPungutan = $bc40Model->isCompleteFormPungutan($penerimaanBarangID);
        $isCompleteFormPernyataan = $bc40Model->isCompleteFormPernyataan($penerimaanBarangID);

        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormPernyataan', $isCompleteFormPernyataan);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormFormTransaksi);
        session()->setFlashdata('isCompleteFormBarang', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPungutan', $isCompleteFormPungutan);

        if (
            $isCompleteFormPernyataan && $isCompleteFormHeader && $isCompleteFormEntitas &&
            $isCompleteFormEntitas && $isCompleteFormDokumen && $isCompleteFormPengangkut &&
            $isCompleteFormPetiKemas && $isCompleteFormFormTransaksi && $isCompleteFormBarang
        ) {
            $bc40Model->set('status_dokumen', "Siap Kirim")->where('penerimaan_barang_id', $penerimaanBarangID)->update();
        } else {
            $bc40Model->set('status_dokumen', "Belum Lengkap")->where('penerimaan_barang_id', $penerimaanBarangID)->update();
        }
    }

    public function generateNomorAju()
    {
        $metaDataModel = new MetadataModel();
        $bc40Model = new bc40Model();

        $kodeKantorStatic = $metaDataModel->where('name', "Kode Kantor BC Static")->first();
        $kodeDokumenbc40Static = $metaDataModel->where('name', "Kode BC40 Static")->first();
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = "";

        $bc40Last = $bc40Model->orderBy('createdAt', "DESC")->limit(1)->first();

        if ($bc40Last == null) {
            $sequenceNoUrutPengajuan = "000001";
        } else {
            // Buatkan auto increment
            $arrNo = explode('-', $bc40Last['no_aju']);
            $lastNomor = $arrNo[3];
            // lakukan increment
            $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
            $sequenceNoUrutPengajuan = $nextNomor;
        }

        return $kodeDokumenbc40Static['value'] . '-' . $kodeKantorStatic['value'] . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan;
    }
}
