<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\BC40Model;
use App\Models\BCEntitasModel;
use App\Models\CountryModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\NomorIjinTPBModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;

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
            'kodeTujuanPengiriman' => $metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', '40')->where('deletedAt', null)->findAll(),
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
                'bc_type' => '40',
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
                'bc_type' => '40',
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

    // Navigator display
    private function setFlashDataNavigatorSession($penerimaanBarangID)
    {
        $bc40Model = new BC40Model();
        $isCompleteFormHeader = $bc40Model->isCompleteFormHeader($penerimaanBarangID);
        $isCompleteFormEntitas = $bc40Model->isCompleteFormEntitas($penerimaanBarangID);

        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);

        if (
            $isCompleteFormHeader && $isCompleteFormEntitas
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
