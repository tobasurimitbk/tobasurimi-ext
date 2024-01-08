<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
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
use App\Models\HsCodesModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SupplierHargaModel;
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
        return view('BeaCukai/bc-23/index');
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
            "penerimaan_barang.bc_type" => 48,
            "penerimaan_barang.status_post" => "FINISH"
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


    public function createHeaderView($penerimaanBarangID)
    {
        $bc23Model = new BC23Model();
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $metaDataModel = new MetadataModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $bc23 = $bc23Model->get($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'bc23' => $bc23,
            'noAju' => $bc23 == null ? $this->generateNomorAju() : $bc23['no_aju'],
            'kodeKantor' => $kantorBeaCukaiModel->findAll(),
            'kodeTujuanTpb' => $metaDataModel->where('name', "Jenis TPB")->findAll(),
            'selectedKantor' => $metaDataModel->where('name', "Kode Kantor Pabean Pengawas Static")->first(),
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-23/form-header', $data);
    }

    public function createHeaderAction()
    {
        $bc23Model = new BC23Model();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $bc23Model->get($penerimaanBarangID);

        if ($lastData == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $bc23Model->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_no_lokal' => $bc23Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

                'kode_pelabuhan_bongkar' => $this->request->getVar('header_pelabuhan_bongkar'),
                'kode_kantor_bongkar' => decrypt($this->request->getVar('header_kantor_pabean_bongkar')),
                'kode_kantor' => decrypt($this->request->getVar('header_kantor_pabean_pengawas')),
                'kode_tujuan_tpb' => decrypt($this->request->getVar('header_kode_tujuan_tpb')),
            ]);
        } else {
            // update
            $bc23Model->update($lastData['id'], [
                'penerimaan_barang_id' => $penerimaanBarangID,
                'kode_pelabuhan_bongkar' => $this->request->getVar('header_pelabuhan_bongkar'),
                'kode_kantor_bongkar' => decrypt($this->request->getVar('header_kantor_pabean_bongkar')),
                'kode_kantor' => decrypt($this->request->getVar('header_kantor_pabean_pengawas')),
                'kode_tujuan_tpb' => decrypt($this->request->getVar('header_kode_tujuan_tpb')),
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Header berhasil diupdate",
        ]);
    }

    public function createEntitasView($penerimaanBarangID)
    {

        $penerimaanBarangModel = new PenerimaanBarangModel();
        $metaDataModel = new MetadataModel();
        $countryModel = new CountryModel();
        $bc23EntitasModel = new BC23EntitasModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $bc23Entitas = $bc23EntitasModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->first();

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'bc23Entitas' => $bc23Entitas,
            'npwpDefault' => $metaDataModel->where('name', "NPWP Importir Default BC")->first(),
            'namaImportirDefault' => $metaDataModel->where('name', "Nama Importir Default BC")->first(),
            'alamatImportirDefault' => $metaDataModel->where('name', "Alamat Importir Default BC")->first(),
            'nibDefault' => $metaDataModel->where('name', "NIB Default BC")->first(),
            'kodeNegaraAsal' => $countryModel->findAll(),
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-23/form-entitas', $data);
    }

    public function createEntitasAction()
    {
        $bc23EntitasModel = new BC23EntitasModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $bc23EntitasModel->get($penerimaanBarangID);

        if ($lastData == null) {
            // insert
            $bc23EntitasModel->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'alamat_entitas' => $this->request->getVar('entitas_alamat_importir'),
                'nama_entitas' => $this->request->getVar('entitas_nama_importir'),
                'nib_entitas' => $this->request->getVar('entitas_nib'),
                'nomor_identitas' => $this->request->getVar('entitas_npwp_importir'),
                'nomor_ijin_entitas' => $this->request->getVar('entitas_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('entitas_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('entitas_nama_pemasok'),
                'alamat_pemasok' => $this->request->getVar('entitas_alamat_pemasok'),
                'kode_negara_pemasok' => decrypt($this->request->getVar('entitas_negara')),
                'npwp_pemilik_barang' => $this->request->getVar('entitas_npwp_pemilik_barang'),
                'nama_pemilik_barang' => $this->request->getVar('entitas_nama_pemilik_barang'),
                'alamat_pemilik_barang' => $this->request->getVar('entitas_alamat_pemilik_barang')
            ]);
        } else {
            // update
            $bc23EntitasModel->update($lastData['id'], [
                'penerimaan_barang_id' => $penerimaanBarangID,
                'alamat_entitas' => $this->request->getVar('entitas_alamat_importir'),
                'nama_entitas' => $this->request->getVar('entitas_nama_importir'),
                'nib_entitas' => $this->request->getVar('entitas_nib'),
                'nomor_identitas' => $this->request->getVar('entitas_npwp_importir'),
                'nomor_ijin_entitas' => $this->request->getVar('entitas_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('entitas_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('entitas_nama_pemasok'),
                'alamat_pemasok' => $this->request->getVar('entitas_alamat_pemasok'),
                'kode_negara_pemasok' => decrypt($this->request->getVar('entitas_negara')),
                'npwp_pemilik_barang' => $this->request->getVar('entitas_npwp_pemilik_barang'),
                'nama_pemilik_barang' => $this->request->getVar('entitas_nama_pemilik_barang'),
                'alamat_pemilik_barang' => $this->request->getVar('entitas_alamat_pemilik_barang')
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

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'kodeDokumen' => $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-23/form-dokumen', $data);
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
            "bc_23_dokumen.deletedAt"  => null,
            "bc_23_dokumen.penerimaan_barang_id" => $penerimaanBarangID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc23DokumenModel = new BC23DokumenModel();
        $metaDataModel = new MetadataModel();


        $beaCukaiDokumen = $bc23DokumenModel->getList($condition, $limit, $offset);
        $resDokumen = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiDokumen['data'] as $data) {
            $jenisDokumen = $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->where('description', $data->id_dokumen)->first();
            array_push($resDokumen, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_barang_id"  => encrypt($data->penerimaan_barang_id),
                "id_dokumen"             => $data->id_dokumen . ' - ' . $jenisDokumen['value'],
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
        $bc23DokumenModel = new BC23DokumenModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $bc23DokumenModel->where('penerimaan_barang_id', $penerimaanBarangID)->orderBy('createdAt', "DESC")->first();
        $seriDokumen = $lastData == null ? 1 : $lastData['seri_dokumen'] + 1;

        $bc23DokumenModel->insert([
            'penerimaan_barang_id' => $penerimaanBarangID,
            'id_dokumen' => decrypt($this->request->getVar('dokumen_jenis_dokumen')),
            'nomor_dokumen' => $this->request->getVar('dokumen_nomor_dokumen'),
            'seri_dokumen' => $seriDokumen,
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
        $bc23DokumenModel = new BC23DokumenModel();
        $id = decrypt($this->request->getVar('id'));
        $bc23DokumenModel->delete($id);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Dokumen berhasil dihapus",
        ]);
    }

    public function createPengangkutView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $metaDataModel = new MetadataModel();
        $countryModel = new CountryModel();
        $bc23Model = new BC23Model();
        $bc23PengangkutModel = new BC23PengangkutModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'kodePengangkutan' => $metaDataModel->where('name', "Pengangkutan")->findAll(),
            'kodeBendera' => $countryModel->findAll(),
            'bc23' => $bc23Model->get($penerimaanBarangID),
            'bc23Pengangkut' => $bc23PengangkutModel->get($penerimaanBarangID),
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-23/form-pengangkut', $data);
    }

    public function createPengangkutAction()
    {
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $bc23Model = new BC23Model();
        $bc23PengangkutModel = new BC23PengangkutModel();

        $lastDataBC23 = $bc23Model->get($penerimaanBarangID);
        $lastPengangkutBC23 = $bc23PengangkutModel->get($penerimaanBarangID);

        if ($lastDataBC23 == null) {
            // insert
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $bc23Model->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_no_lokal' => $bc23Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

                'no_bc_11' => $this->request->getVar('bc_11_no_bc_11'),
                'tanggal_bc_11' => $this->request->getVar('bc_11_tanggal_bc_11') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('bc_11_tanggal_bc_11')), "Y-m-d") : "",
                'pos_bc_11' => $this->request->getVar('bc_11_pos_bc_11'),
                'sub_pos_bc_11' => $this->request->getVar('bc_11_sub_pos_bc_11'),
                'sub_pos_pos_bc_11' => $this->request->getVar('bc_11_sub_sub_pos_bc_11'),
                'kode_pelabuhan_muat' => $this->request->getVar('pengangkutan_pelabuhan_muat'),
                'kode_pelabuhan_transit' => $this->request->getVar('pengangkutan_pelabuhan_transit'),
                'kode_tps' => $this->request->getVar('pengangkutan_tempat_penimbunan')
            ]);
        } else {
            // update
            $bc23Model->update($lastDataBC23['id'], [
                'no_bc_11' => $this->request->getVar('bc_11_no_bc_11'),
                'tanggal_bc_11' => $this->request->getVar('bc_11_tanggal_bc_11') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('bc_11_tanggal_bc_11')), "Y-m-d") : "",
                'pos_bc_11' => $this->request->getVar('bc_11_pos_bc_11'),
                'sub_pos_bc_11' => $this->request->getVar('bc_11_sub_pos_bc_11'),
                'sub_pos_pos_bc_11' => $this->request->getVar('bc_11_sub_sub_pos_bc_11'),
                'kode_pelabuhan_muat' => $this->request->getVar('pengangkutan_pelabuhan_muat'),
                'kode_pelabuhan_transit' => $this->request->getVar('pengangkutan_pelabuhan_transit'),
                'kode_tps' => $this->request->getVar('pengangkutan_tempat_penimbunan')
            ]);
        }

        if ($lastPengangkutBC23 == null) {
            $bc23PengangkutModel->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'kode_cara_angkut' => decrypt($this->request->getVar('pengangkutan_cara_pengangkutan')),
                'nama_sarana_pengangkut' => $this->request->getVar('pengangkutan_nama_sarana_pengangkut'),
                'nomor_pengangkut' => $this->request->getVar('pengangkutan_nomor_pengangkut'),
                'kode_bendera' => decrypt($this->request->getVar('pengangkutan_kode_bendera')),
            ]);
        } else {
            $bc23PengangkutModel->update($lastPengangkutBC23['id'], [
                'penerimaan_barang_id' => $penerimaanBarangID,
                'kode_cara_angkut' => decrypt($this->request->getVar('pengangkutan_cara_pengangkutan')),
                'nama_sarana_pengangkut' => $this->request->getVar('pengangkutan_nama_sarana_pengangkut'),
                'nomor_pengangkut' => $this->request->getVar('pengangkutan_nomor_pengangkut'),
                'kode_bendera' => decrypt($this->request->getVar('pengangkutan_kode_bendera')),
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pengangkut berhasil diupdate",
        ]);
    }

    public function createKemasanPetiKemas($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $metaDataModel = new MetadataModel();
        $bc23KemasanModel = new BC23KemasanModel();
        $bc23KontainerModel = new BC23KontainerModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $kemasanLast = $bc23KemasanModel->getLast($penerimaanBarangID);
        $kontainerLast = $bc23KontainerModel->getLast($penerimaanBarangID);


        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
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

        return view('BeaCukai/bc-23/form-kemasan-peti-kemas', $data);
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
            "bc_23_kemasan.deletedAt"  => null,
            "bc_23_kemasan.penerimaan_barang_id" => $penerimaanBarangID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc23KemasanModel = new BC23KemasanModel();
        $metaDataModel = new MetadataModel();

        $bc23Kemasan = $bc23KemasanModel->getList($condition, $limit, $offset);
        $resKemasan = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc23Kemasan['data'] as $data) {
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
            "recordsTotal"      => $bc23Kemasan['totalData'],
            "recordsFiltered"   => $bc23Kemasan['totalFilteredData'],
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
            "bc_23_kontainer.deletedAt"  => null,
            "bc_23_kontainer.penerimaan_barang_id" => $penerimaanBarangID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc23KontainerModel = new BC23KontainerModel();
        $metaDataModel = new MetadataModel();

        $bc23Kontainer = $bc23KontainerModel->getList($condition, $limit, $offset);
        $resKontainer = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc23Kontainer['data'] as $data) {
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
            "recordsTotal"      => $bc23Kontainer['totalData'],
            "recordsFiltered"   => $bc23Kontainer['totalFilteredData'],
            "data"              => $resKontainer,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createKemasanAction()
    {
        $bc23KemasanModel = new BC23KemasanModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $bc23KemasanModel->insert([
            'penerimaan_barang_id' => $penerimaanBarangID,
            'seri_kemasan' => $this->request->getVar('kemasan_seri_kemasan'),
            'jumlah_kemasan' => $this->request->getVar('kemasan_jumlah_kemasan'),
            'kode_jenis_kemasan' => decrypt($this->request->getVar('kemasan_jenis_kemasan')),
            'merk_kemasan' => $this->request->getVar('kemasan_merk_kemasan')
        ]);

        $kemasanLast = $bc23KemasanModel->getLast($penerimaanBarangID);

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
        $bc23KemasanModel = new BC23KemasanModel();
        $penerimaanBarangID = $bc23KemasanModel->find($id)['penerimaan_barang_id'];
        $bc23KemasanModel->delete($id);

        $kemasanLast = $bc23KemasanModel->getLast($penerimaanBarangID);

        return response()->setJSON([
            'status' => true,
            'kemasan_seri_kemasan' => $kemasanLast != null ? $kemasanLast['seri_kemasan'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil dihapus"
        ]);
    }

    public function createKontainerAction()
    {
        $bc23KontainerModel = new BC23KontainerModel();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $bc23KontainerModel->insert([
            'penerimaan_barang_id' => $penerimaanBarangID,
            'nomor_kontainer' => $this->request->getVar('kontainer_nomor'),
            'kode_ukuran_kontainer' => decrypt($this->request->getVar('kontainer_ukuran')),
            'kode_jenis_kontainer' => decrypt($this->request->getVar('kontainer_jenis')),
            'kode_tipe_kontainer' => decrypt($this->request->getVar('kontainer_tipe')),
            'seri_kontainer' => $this->request->getVar('kontainer_seri')
        ]);

        $kontainerLast = $bc23KontainerModel->getLast($penerimaanBarangID);

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
        $bc23KontainerModel = new BC23KontainerModel();
        $penerimaanBarangID = $bc23KontainerModel->find($id)['penerimaan_barang_id'];
        $bc23KontainerModel->delete($id);

        $kontainerLast = $bc23KontainerModel->getLast($penerimaanBarangID);

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
        $metaDataModel = new MetadataModel();
        $bc23Model = new BC23Model();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $bc23 = $bc23Model->get($penerimaanBarangID);

        if ($bc23 != null) {
            $bc23['ndpbm'] = formatRupiah($bc23['ndpbm']);
            $bc23['nilai_barang'] = formatRupiah($bc23['nilai_barang']);
            $bc23['cif'] = formatRupiah($bc23['cif']);
            $bc23['harga_penyerahan'] = formatRupiah($bc23['harga_penyerahan']);
            $bc23['biaya_tambahan'] = formatRupiah($bc23['biaya_tambahan']);
            $bc23['biaya_pengurang'] = formatRupiah($bc23['biaya_pengurang']);
            $bc23['fob'] = formatRupiah($bc23['fob']);
            $bc23['freight'] = formatRupiah($bc23['freight']);
            $bc23['asuransi'] = formatRupiah($bc23['asuransi']);
            $bc23['bruto'] = formatRupiah($bc23['bruto']);
            $bc23['netto'] = formatRupiah($bc23['netto']);
        }

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'kodeValuta' => $metaDataModel->where('name', "Valuta")->findAll(),
            'kodeIncoterm' => $metaDataModel->where('name', "Kode Incoterm BC")->findAll(),
            'kodeAsuransi' => $metaDataModel->where('name', "Kode Asuransi BC")->findAll(),
            'kodeKenaPajak' => $metaDataModel->where('name', "Kode Kena Pajak BC")->findAll(),
            'bc23' => $bc23,
            'lpb' => $lpb
        ];

        return view('BeaCukai/bc-23/form-transaksi', $data);
    }

    public function createTransaksiAction()
    {
        $bc23Model = new BC23Model();

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastDataBC23 = $bc23Model->get($penerimaanBarangID);
        if ($lastDataBC23 == null) {
            // insert
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $bc23Model->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_no_lokal' => $bc23Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

                'kode_valuta' => decrypt($this->request->getVar('harga_kode_valuta')),
                'ndpbm' => convertRupiahToNumber($this->request->getVar('harga_ndpbm')),
                'kode_incoterm' => decrypt($this->request->getVar('harga_kode_harga_barang')),
                'nilai_barang' => convertRupiahToNumber($this->request->getVar('harga_nilai_barang')),
                'cif' => convertRupiahToNumber($this->request->getVar('harga_cif')),
                'harga_penyerahan' => convertRupiahToNumber($this->request->getVar('harga_nilai_pabean')),
                'biaya_tambahan' => convertRupiahToNumber($this->request->getVar('harga_lainnya_biaya_penambah')),
                'biaya_pengurang' => convertRupiahToNumber($this->request->getVar('harga_lainnya_biaya_pengurang')),
                'fob' => convertRupiahToNumber($this->request->getVar('harga_lainnya_free_on_board')),
                'freight' => convertRupiahToNumber($this->request->getVar('harga_lainnya_freight')),
                'kode_asuransi' => decrypt($this->request->getVar('harga_lainnya_kode_asuransi')),
                'asuransi' => convertRupiahToNumber($this->request->getVar('harga_lainnya_nilai_asuransi')),
                'bruto' => convertRupiahToNumber($this->request->getVar('berat_bruto')),
                'netto' => convertRupiahToNumber($this->request->getVar('berat_netto')),
                'kode_kena_pajak' => decrypt($this->request->getVar('pajak_jasa_kena_pajak'))
            ]);
        } else {
            $bc23Model->update($lastDataBC23['id'], [
                'kode_valuta' => decrypt($this->request->getVar('harga_kode_valuta')),
                'ndpbm' => convertRupiahToNumber($this->request->getVar('harga_ndpbm')),
                'kode_incoterm' => decrypt($this->request->getVar('harga_kode_harga_barang')),
                'nilai_barang' => convertRupiahToNumber($this->request->getVar('harga_nilai_barang')),
                'cif' => convertRupiahToNumber($this->request->getVar('harga_cif')),
                'harga_penyerahan' => convertRupiahToNumber($this->request->getVar('harga_nilai_pabean')),
                'biaya_tambahan' => convertRupiahToNumber($this->request->getVar('harga_lainnya_biaya_penambah')),
                'biaya_pengurang' => convertRupiahToNumber($this->request->getVar('harga_lainnya_biaya_pengurang')),
                'fob' => convertRupiahToNumber($this->request->getVar('harga_lainnya_free_on_board')),
                'freight' => convertRupiahToNumber($this->request->getVar('harga_lainnya_freight')),
                'kode_asuransi' => decrypt($this->request->getVar('harga_lainnya_kode_asuransi')),
                'asuransi' => convertRupiahToNumber($this->request->getVar('harga_lainnya_nilai_asuransi')),
                'bruto' => convertRupiahToNumber($this->request->getVar('berat_bruto')),
                'netto' => convertRupiahToNumber($this->request->getVar('berat_netto')),
                'kode_kena_pajak' => decrypt($this->request->getVar('pajak_jasa_kena_pajak'))
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

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'lpb' => $lpb,
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($penerimaanBarangID, $lpb->tipe_bahan, $lpb->status_penerimaan)
        ];

        return view('BeaCukai/bc-23/form-barang', $data);
    }

    public function createBarangDetailView($penerimaanBarangID, $penerimaanBarangDetailID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $metaDataModel = new MetadataModel();
        $hsCodeModel = new HsCodesModel();
        $countryModel = new CountryModel();
        $bc23BarangModel = new BC23BarangModel();
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
        $bc23DokumenBarang = null;

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        if ($lpb == null || $lpbDetail == null) {
            return redirect()->to('bea-cukai-bc-23');
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

        $seriBarang = $bc23BarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->orderBy('createdAt', "DESC")->first();
        $bc23DokumenBarang =  $bc23BarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)->first();
        if ($bc23DokumenBarang != null) {
            $kodeSatuanBarang = $metaDataModel->where('name', "Kode Satuan BC")->where('value', $bc23DokumenBarang['kode_satuan_barang'])->first();
        }

        $data = [
            'kodeFasilitasTarif' => $metaDataModel->where('name', "Kode Fasilitas Tarif BC")->findAll(),
            'kodeJenisTarif' => $metaDataModel->where('name', "Kode Jenis Tarif BC")->findAll(),
            'kodeJenisPungutan' => $metaDataModel->where('name', "Kode Jenis Pungutan BC")->findAll(),
            'kodeNegaraAsal' => $countryModel->findAll(),
            'kodeHS' => $hsCodeModel->findAll(),
            'kodeKategoriBarang' => $metaDataModel->where('name', 'Kategori Barang BC')->orderBy('description', "ASC")->findAll(),
            'kodeJenisKemasan' => $metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'lpb' => $lpb,
            'lpbDetail' => $lpbDetail,
            'poDetail' => $poDetail,
            'barangDetail' => $barangDetail,
            'seriBarang' => $seriBarang == null ? 1 : $seriBarang['seri_barang'] + 1,
            'bc23DokumenBarang' => $bc23DokumenBarang,
            'kodeSatuanBarang' => $kodeSatuanBarang
        ];

        return view('BeaCukai/bc-23/form-detail-barang', $data);
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
            "bc_23_barang_tarif.deletedAt"  => null,
            "bc_23_barang_tarif.penerimaan_barang_id" => $penerimaanBarangID,
            "bc_23_barang_tarif.penerimaan_barang_detail_id" => $penerimaanBarangDetailID
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc23BarangTarifModel = new BC23BarangTarifModel();
        $metaDataModel = new MetadataModel();

        $bc23BarangTarif = $bc23BarangTarifModel->getList($condition, $limit, $offset);
        $res = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc23BarangTarif['data'] as $data) {
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
            "recordsTotal"      => $bc23BarangTarif['totalData'],
            "recordsFiltered"   => $bc23BarangTarif['totalFilteredData'],
            "data"              => $res,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createPungutanAction()
    {
        $bc23BarangTarifModel = new BC23BarangTarifModel();
        $bc23BarangModel = new BC23BarangModel();

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $penerimaanBarangDetailID = decrypt($this->request->getVar('penerimaan_barang_detail_id'));

        $bc23Barang = $bc23BarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)->first();

        if ($bc23Barang == null) {
            return response()->setJSON([
                'message' => "Isi dokumen barang detail terlebih dahulu sebelum mengisi pungutan & dokumen barang",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $bc23BarangTarifModel->insert([
            'penerimaan_barang_id' => $penerimaanBarangID,
            'penerimaan_barang_detail_id' => $penerimaanBarangDetailID,
            'kode_jenis_pungutan' => decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')),
            'kode_jenis_tarif' => decrypt($this->request->getVar('barang_detail_kode_jenis_tarif')),
            'tarif_bea_masuk' => convertRupiahToNumber($this->request->getVar('barang_detail_nilai_tarif')),
            'kode_fasilitas_tarif' => decrypt($this->request->getVar('barang_detail_kode_fasilitas_tarif')),
            'tarif_fasilitas' => convertRupiahToNumber($this->request->getVar('barang_detail_tarif_fasilitas')),
            'seri_barang' => $bc23Barang['seri_barang'],
        ]);

        return response()->setJSON([
            'message' => "Pungutan berhasil ditambahkan",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function deletePungutanAction()
    {
        $bc23BarangTarifModel = new BC23BarangTarifModel();
        $bc23BarangTarifModel->delete(decrypt($this->request->getVar('id')));

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
            "bc_23_dokumen.deletedAt"  => null,
            "bc_23_dokumen.penerimaan_barang_id" => $penerimaanBarangID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc23DokumenModel = new BC23DokumenModel();
        $metaDataModel = new MetadataModel();
        $bc23BarangDokumenModel = new BC23BarangDokumenModel();

        $beaCukaiDokumen = $bc23DokumenModel->getList($condition, $limit, $offset);
        $resDokumen = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiDokumen['data'] as $data) {
            $jenisDokumen = $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->where('description', $data->id_dokumen)->first();
            $barangDokumen = $bc23BarangDokumenModel->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)
                ->where('seri_dokumen', $data->seri_dokumen)->where('deletedAt', null)
                ->first();

            array_push($resDokumen, [
                "no"                    => $no++,
                "bc_23_dokumen_id"      => encrypt($data->id),
                "penerimaan_barang_id"  => encrypt($data->penerimaan_barang_id),
                "id_dokumen"            => $data->id_dokumen . ' - ' . $jenisDokumen['value'],
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
        $bc23BarangDokumenModel = new BC23BarangDokumenModel();

        $barangDokumenID = decrypt($this->request->getVar('bc_23_dokumen_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $penerimaanBarangDetailID = decrypt($this->request->getVar('penerimaan_barang_detail_id'));

        $bc23BarangDokumenModel->insert([
            'penerimaan_barang_id' => $penerimaanBarangID,
            'penerimaan_barang_detail_id' => $penerimaanBarangDetailID,
            'bc_23_dokumen_id' => $barangDokumenID,
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
        $bc23BarangDokumenModel = new BC23BarangDokumenModel();
        $bc23BarangDokumenModel->delete(decrypt($this->request->getVar('id')));

        return response()->setJSON([
            'message' => "Dokumen berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function createBarangDetailAction()
    {
        $bc23BarangModel = new BC23BarangModel();

        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $penerimaanBarangDetailID = decrypt($this->request->getVar('penerimaan_barang_detail_id'));

        $bc23Barang = $bc23BarangModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('penerimaan_barang_detail_id', $penerimaanBarangDetailID)->first();

        if ($bc23Barang == null) {
            $bc23BarangModel->insert([
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'penerimaan_barang_detail_id' => decrypt($this->request->getVar('penerimaan_barang_detail_id')),
                'seri_barang' => $this->request->getVar('barang_detail_seri_barang'),
                'pos_tarif' => decrypt($this->request->getVar('barang_detail_kode_hs')),
                'kode_barang' => $this->request->getVar('barang_detail_kode_barang'),
                'uraian' => $this->request->getVar('barang_detail_uraian'),
                'merk_barang' => $this->request->getVar('barang_detail_merk_barang'),
                'tipe_barang' => $this->request->getVar('barang_detail_tipe_barang'),
                'spesifikasi_lain' => $this->request->getVar('barang_detail_spesifikasi_lain'),
                'kode_kategori_barang' => decrypt($this->request->getVar('barang_detail_kategori_barang')),
                'kode_negara_asal' => decrypt($this->request->getVar('barang_detail_negara')),
                'harga_perolehan_barang' => convertRupiahToNumber($this->request->getVar('barang_detail_harga')),
                'nilai_tambah' => convertRupiahToNumber($this->request->getVar('barang_detail_biaya_tambahan')),
                'fob' => convertRupiahToNumber($this->request->getVar('barang_detail_fob')),
                'harga_satuan_barang' => convertRupiahToNumber($this->request->getVar('barang_detail_harga_satuan')),
                'freight' => convertRupiahToNumber($this->request->getVar('barang_detail_freight')),
                'asuransi' => convertRupiahToNumber($this->request->getVar('barang_detail_asuransi')),
                'cif_rupiah' => convertRupiahToNumber($this->request->getVar('barang_detail_cif')),
                'harga_ekspor' => convertRupiahToNumber($this->request->getVar('barang_detail_nilai_pabean')),
                'jumlah_satuan' => $this->request->getVar('barang_detail_jumlah_satuan'),
                'kode_satuan_barang' => decrypt($this->request->getVar('barang_detail_kode_satuan_barang')),
                'jumlah_kemasan' => $this->request->getvar('barang_detail_jumlah_kemasan'),
                'kode_jenis_kemasan' => decrypt($this->request->getVar('barang_detail_kode_jenis_kemasan')),
                'netto' => $this->request->getVar('barang_detail_berat_bersih')
            ]);
        } else {
            $bc23BarangModel->update($bc23Barang['id'], [
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'penerimaan_barang_detail_id' => decrypt($this->request->getVar('penerimaan_barang_detail_id')),
                'seri_barang' => $this->request->getVar('barang_detail_seri_barang'),
                'pos_tarif' => decrypt($this->request->getVar('barang_detail_kode_hs')),
                'kode_barang' => $this->request->getVar('barang_detail_kode_barang'),
                'uraian' => $this->request->getVar('barang_detail_uraian'),
                'merk_barang' => $this->request->getVar('barang_detail_merk_barang'),
                'tipe_barang' => $this->request->getVar('barang_detail_tipe_barang'),
                'spesifikasi_lain' => $this->request->getVar('barang_detail_spesifikasi_lain'),
                'kode_kategori_barang' => decrypt($this->request->getVar('barang_detail_kategori_barang')),
                'kode_negara_asal' => decrypt($this->request->getVar('barang_detail_negara')),
                'harga_perolehan_barang' => convertRupiahToNumber($this->request->getVar('barang_detail_harga')),
                'nilai_tambah' => convertRupiahToNumber($this->request->getVar('barang_detail_biaya_tambahan')),
                'fob' => convertRupiahToNumber($this->request->getVar('barang_detail_fob')),
                'harga_satuan_barang' => convertRupiahToNumber($this->request->getVar('barang_detail_harga_satuan')),
                'freight' => convertRupiahToNumber($this->request->getVar('barang_detail_freight')),
                'asuransi' => convertRupiahToNumber($this->request->getVar('barang_detail_asuransi')),
                'cif_rupiah' => convertRupiahToNumber($this->request->getVar('barang_detail_cif')),
                'harga_ekspor' => convertRupiahToNumber($this->request->getVar('barang_detail_nilai_pabean')),
                'jumlah_satuan' => $this->request->getVar('barang_detail_jumlah_satuan'),
                'kode_satuan_barang' => decrypt($this->request->getVar('barang_detail_kode_satuan_barang')),
                'jumlah_kemasan' => $this->request->getvar('barang_detail_jumlah_kemasan'),
                'kode_jenis_kemasan' => decrypt($this->request->getVar('barang_detail_kode_jenis_kemasan')),
                'netto' => $this->request->getVar('barang_detail_berat_bersih')
            ]);
        }

        return response()->setJSON([
            'message' => "Detail Barang Dokumen Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function createPungutanView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'lpb' => $lpb,
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($penerimaanBarangID, $lpb->tipe_bahan, $lpb->status_penerimaan)
        ];

        return view('BeaCukai/bc-23/form-pungutan', $data);
    }

    public function createPernyataanView($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $bc23Model = new BC23Model();

        $penerimaanBarangID = decrypt($penerimaanBarangID);

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $bc23 = $bc23Model->get($penerimaanBarangID);

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($penerimaanBarangID);

        $data = [
            'lpb' => $lpb,
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($penerimaanBarangID, $lpb->tipe_bahan, $lpb->status_penerimaan),
            'bc23' => $bc23

        ];

        return view('BeaCukai/bc-23/form-pernyataan', $data);
    }

    public function createPernyataanAction()
    {
        $bc23Model = new BC23Model();
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $lastData = $bc23Model->get($penerimaanBarangID);

        if ($lastData == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $bc23Model->insert([
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_no_lokal' => $bc23Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

                'nama_ttd' => $this->request->getVar('pernyatan_nama'),
                'kota_ttd' => $this->request->getVar('pernyatan_tempat'),
                'tanggal_ttd' => $this->request->getVar('pernyataan_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pernyataan_tanggal')), "Y-m-d") : "",
                'jabatan_pengusaha_ttd' => $this->request->getVar('pernyatan_jabatan'),
            ]);
        } else {
            // update
            $bc23Model->update($lastData['id'], [
                'penerimaan_barang_id' => $penerimaanBarangID,
                'nama_ttd' => $this->request->getVar('pernyatan_nama'),
                'kota_ttd' => $this->request->getVar('pernyatan_tempat'),
                'tanggal_ttd' => $this->request->getVar('pernyataan_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pernyataan_tanggal')), "Y-m-d") : "",
                'jabatan_pengusaha_ttd' => $this->request->getVar('pernyatan_jabatan')
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pernyataan berhasil diupdate",
        ]);
    }

    // Navigator display
    private function setFlashDataNavigatorSession($penerimaanBarangID)
    {
        $bc23Model = new BC23Model();
        $isCompleteFormPernyataan = $bc23Model->isCompleteFormPernyataan($penerimaanBarangID);
        $isCompleteFormHeader = $bc23Model->isCompleteFormHeader($penerimaanBarangID);
        $isCompleteFormEntitas = $bc23Model->isCompleteFormEntitas($penerimaanBarangID);
        $isCompleteFormDokumen = $bc23Model->isCompleteFormDokumen($penerimaanBarangID);
        $isCompleteFormPengangkut = $bc23Model->isCompleteFormPengangkut($penerimaanBarangID);
        $isCompleteFormPetiKemas = $bc23Model->isCompleteFormPetiKemas($penerimaanBarangID);
        $isCompleteFormFormTransaksi = $bc23Model->isCompleteFormTransaksi($penerimaanBarangID);
        $isCompleteFormBarang = $bc23Model->isCompleteFormBarang($penerimaanBarangID);

        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormPernyataan', $isCompleteFormPernyataan);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormFormTransaksi);
        session()->setFlashdata('isCompleteFormBarang', $isCompleteFormBarang);
    }



    // API GET
    public function getValuta()
    {
        $beacukaiApi = new BeaCukaiApi();
        $kodeValuta = decrypt($this->request->getVar('harga_kode_valuta'));
        $res = $beacukaiApi->getNilaiValuta($kodeValuta);

        return response()->setJSON([
            'data' => $res,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function apiPelabuhanTest()
    {
        $kantorBeaCukaiModel = new KantorBeaCukaiModel();

        $jwtToken = "eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICIzbXo1OTYwdE9MbkhhT0JLTHRQSG15N2VmT0plQVo5UmVZdkJMOGxVZDhFIn0.eyJleHAiOjE3MDM1ODI2NDgsImlhdCI6MTcwMzU3MTg0OCwianRpIjoiMWM2MWFkN2ItNzkwYy00ZTA4LTk1N2YtYTk0MjBlYTY5NzA5IiwiaXNzIjoiaHR0cHM6Ly9zc28tc3NvLXBvcnRhbC5hcHBzLnByb2RkYy5jdXN0b21zLmdvLmlkL2F1dGgvcmVhbG1zL3BvcnRhbF9oMmgiLCJzdWIiOiIzZTk4NmE0NS0zNzFjLTRlZTMtOTZhNi04MTg2OTBjYmFkNWIiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJwb3J0YWxfaDJoX2NsaWVudCIsInNlc3Npb25fc3RhdGUiOiI3MjYzYmM2Ni05OWY0LTQxM2MtODY1Zi05ZTBlNDJjZGNlN2YiLCJhY3IiOiIxIiwic2NvcGUiOiJlbWFpbCBwcm9maWxlIiwic2lkIjoiNzI2M2JjNjYtOTlmNC00MTNjLTg2NWYtOWUwZTQyY2RjZTdmIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJuYW1lIjoiRmVicnkgU2ludG9zbyIsImlkZW50aXRhcyI6IjAxNzE4OTE4NDEyNTAwMCIsInByZWZlcnJlZF91c2VybmFtZSI6InRvYmFzdXJpbWkiLCJnaXZlbl9uYW1lIjoiRmVicnkgU2ludG9zbyIsImZhbWlseV9uYW1lIjoiIiwiZW1haWwiOiJleGltQHRvYmFzdXJpbWkuY29tIn0.Oy97v-86zIAUzlyXaFMWGb5MY9DQtmUKoF-SWJ6jPWjC1tySaZwZSnRAx4RgWrSqpMJup5jcO-cMSNWeHNm53vDOKyXfcNxBx5HomGjvMgZp7uz1OJKABBrPu3ksv_31q02qi3SawvPBMk8xgkPLS80MQpvZ92f6sUdHbj9JUMwZfnT6V0siJ0Td0LGXRGtIgceVFNjothuKNucqT9W0iw-PBR7-NbEc3o4JY1qsOdnmSUL1_i4OeIfCu3VorFJRpmezwaMz4a1Xy1XQV5zVgxU5eJDARpi63YGUlXb8WlE178hHQpx0vdUGVgKaUM4qVxg5pgGlGXOskMnJCT6Akw";

        foreach ($kantorBeaCukaiModel->findAll() as $k) {
            $baseUrl = "https://apis-gw.beacukai.go.id/openapi/pelabuhan/kodeKantor/" . $k['kode'];

            $headers = array(
                'Authorization: Bearer ' . $jwtToken,
                'Content-Type: application/json',
            );

            // Inisialisasi cURL session
            $ch = curl_init();

            // Set konfigurasi cURL
            curl_setopt($ch, CURLOPT_URL, $baseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Eksekusi permintaan cURL dan simpan responsnya
            $response = curl_exec($ch);

            // Cek apakah ada kesalahan
            if (curl_errno($ch)) {
                echo 'Error: ' . curl_error($ch);
            }

            // Tutup session cURL
            curl_close($ch);

            $responseData = json_decode($response);

            if (isset($responseData->data) && count($responseData->data) != 0) {
                print_r($responseData);
                break;
            }
        }

        echo "KOSONG";
    }

    // LAMA

    public function create($id)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $metaDataModel = new MetadataModel();
        $countryModel = new CountryModel();
        $kantorBeaCukaiModel = new KantorBeaCukaiModel();

        $id = decrypt($id);

        $lpb = $penerimaanBarangModel->getById($id);

        if ($lpb == null || $lpb->bc_type != "BC 2.3") {
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
        $search = $this->request->getVar('search');
        $data = $metaDataModel->getKodeSatuanBarang($search);
        return $this->response->setJSON(['results' => $data,]);
    }

    public function generateNomorAju()
    {
        $metaDataModel = new MetadataModel();
        $bc23Model = new BC23Model();

        $kodeKantorStatic = $metaDataModel->where('name', "Kode Kantor BC Static")->first();
        $kodeDokumenBC23Static = $metaDataModel->where('name', "Kode BC23 Static")->first();
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = "";

        $bc23Last = $bc23Model->orderBy('createdAt', "DESC")->limit(1)->first();

        if ($bc23Last == null) {
            $sequenceNoUrutPengajuan = "000001";
        } else {
            // Buatkan auto increment
            $arrNo = explode('-', $bc23Last['no_aju']);
            $lastNomor = $arrNo[3];
            // lakukan increment
            $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
            $sequenceNoUrutPengajuan = $nextNomor;
        }

        return $kodeDokumenBC23Static['value'] . '-' . $kodeKantorStatic['value'] . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan;
    }
}
