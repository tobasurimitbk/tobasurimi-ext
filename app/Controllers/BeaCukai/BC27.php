<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\BC27Model;
use App\Models\CeisaSettingModel;
use App\Models\CompaniesModel;
use App\Models\MetadataModel;
use App\Models\MutasiGlobalDetailModel;
use App\Models\MutasiGlobalModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54
class BC27 extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $companyModel;
    protected $mutasiGlobalModel;
    protected $metaDataModel;
    protected $bc27Model;
    protected $mutasiGlobalDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;

    public function __construct()
    {
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->companyModel = new CompaniesModel();
        $this->mutasiGlobalModel = new MutasiGlobalModel();
        $this->metaDataModel = new MetadataModel();
        $this->bc27Model = new BC27Model();
        $this->mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first()
        ];

        return view('BeaCukai/bc-27/index', $data);
    }

    public function online()
    {
        $data = [
            'baseUrl' => $this->metaDataModel->where('name', "Base Url BC")->first()['value']
        ];

        return view('BeaCukai/bc-27/online', $data);
    }

    public function allOnline()
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);
        $dataOnline = $beacukaiApi->getListStatusResponseAll();

        $newDataResult = [];
        foreach ($dataOnline->dataRespon as $d) {
            if ($d->kodeDokumen == "27") {
                $newDataResult[] = $d;
            }
        }
        $dataOnline->dataRespon = $newDataResult;

        if ($dataOnline->status == false) {
            return response()->setJSON($dataOnline);
        } else {
            return response()->setJSON([
                'data' => $dataOnline,
                'status' => true
            ]);
        }
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
            "type"          => "BC 2.7"
        ];

        $condition = [
            "bc_27.company_asal_id"  => $this->this_company_id,
            "bc_27.deletedAt" => null,
            "mutasi_global.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC27" => $this->request->getGet("mulaiTanggalBC27"),
            "selesaiTanggalBC27" => $this->request->getGet('selesaiTanggalBC27'),
            "noBC27" => $this->request->getGet('noBC27'),
            "noAju" => $this->request->getGet('noAju'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc27Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "company_asal_name"     => strtoupper(session()->get('login')->this_company),
                "divisi_asal_name"      => strtoupper($data->divisi),
                "warehouse_asal_name"   => strtoupper($data->warehouse_name),
                "company_tujuan_name"   => strtoupper($data->company),
                "no_mutasi"             => $data->no_mutasi,
                "bc_no_lokal"           => $data->bc_no_lokal,
                "tanggal_bc_27"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "no_aju"                => $data->no_aju . " / " . ($data->no_daftar == "" ? "-" : $data->no_daftar),
                "status_posting"        => $data->status_posting,
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

    public function create()
    {
        $data = [
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'companyAsalName' => session()->get('login')->this_company,
            'noAju' => $this->generateNomorAju(),
        ];

        return view('BeaCukai/bc-27/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->getBC27($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $data = [
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'companyAsalName' => session()->get('login')->this_company,
            'noAju' => $this->generateNomorAju(),
            'bc27' => $bc27
        ];

        return view('BeaCukai/bc-27/form', $data);
    }

    public function createAction()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));

        $this->bc27Model->insert([
            'company_asal_id' => $this->this_company_id,
            'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
            'mutasi_global_id' => $this->request->getVar('mutasi_global_id'),
            'bc_no_lokal' => $this->bc27Model->getNo(date('m'), date('Y'), $last_day),
            'no_aju' => $this->request->getVar('no_aju'),
            'status_posting' => '0',
            'no_daftar' => $this->request->getVar('no_daftar'),
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 27 Berhasil Disimpan"
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->bc27Model->update($id, [
            'company_asal_id' => $this->this_company_id,
            'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
            'mutasi_global_id' => $this->request->getVar('mutasi_global_id'),
            'no_aju' => $this->request->getVar('no_aju'),
            'no_daftar' => $this->request->getVar('no_daftar'),
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 27 Berhasil Diupdate"
        ]);
    }

    public function getListMutasiDetail()
    {
        $mutasiGlobalId = $this->request->getVar('mutasi_global_id');
        $dataResultDetail = $this->mutasiGlobalDetailModel->getMutasiDetail($mutasiGlobalId);

        return response()->setJSON([
            'status' => true,
            'data' => $dataResultDetail,
        ]);
    }

    public function dropdownMutasiGlobal()
    {
        $companyTujuanId = $this->request->getVar('company_tujuan_id');
        $result = $this->mutasiGlobalModel->getMutasiGlobalList($companyTujuanId, $this->this_company_id);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $result
        ]);
    }

    public function checkNoAju()
    {
        $id = decrypt($this->request->getVar('id'));
        $noAju = $this->request->getVar('no_aju');
        $isUsed = true;

        if (!empty($this->request->getVar('id'))) {
            // UPDATE
            $first = $this->bc27Model
                ->where('company_asal_id', $this->this_company_id)
                ->where(
                    'no_aju',
                    $noAju
                )
                ->where('id != ', $id)
                ->first();

            if ($first != null) {
                $isUsed = false;
            }
        } else {
            // CREATE
            $first = $this->bc27Model
                ->where('company_asal_id', $this->this_company_id)
                ->where(
                    'no_aju',
                    $noAju
                )
                ->first();

            if ($first != null) {
                $isUsed = false;
            }
        }

        if (!$isUsed) {
            return response()->setJSON([
                'status' => false,
                'message' => "No aju sudah digunakan"
            ]);
        } else {
            return response()->setJSON([
                'status' => true,
                'message' => "No aju tersedia"
            ]);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->bc27Model->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 27 Berhasil Dihapus"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        // BC 27 FIRST
        $bc27 = $this->bc27Model->find($id);
        // KURANGI STOK NYA
        $mutasiGlobal = $this->mutasiGlobalModel->find($bc27['mutasi_global_id']);
        $mutasiGlobalList = $this->mutasiGlobalDetailModel->where('mutasi_global_id', $bc27['mutasi_global_id'])->where('deletedAt', null)->findAll();

        foreach ($mutasiGlobalList as $m) {
            $stock = $this->stockModel->find($m['stock_id']);
            $qty = $m['qty'];

            if ($stock['tipe_barang'] == "kemasan") {
                $barang2_id = $stock['kemasan_id'];
            } else {
                $barang2_id = $stock['barang2_id'];
            }

            // BARANG LAMA
            $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
                $m['stock_id'],
                $m['bc_id'],
                $m['no_aju'],
                $m['stock_dokumen']
            );

            $stok = $this->stockModel->insertStok(
                $mutasiGlobal['company_asal_id'],
                $mutasiGlobal['warehouse_asal_id'],
                $mutasiGlobal['divisi_asal_id'],
                $stock['tipe_barang'],
                $stock['barang1_id'],
                $barang2_id,
                ($qty * -1),
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                "Out",
                date('Y-m-d'),
                $this->this_user_id,
                "MUTASI",
                "-", // NO PENERIMAAN MUTASI
                $mutasiGlobal['keterangan'],
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $m['bc_id'],
                $stok,
                $stokDetail,
                $qty,
                $m['no_aju'],
                $mutasiGlobal['no_mutasi'],
                $m['stock_dokumen'],
                $stockOldDetail['supplier_id'],
                $stockOldDetail['harga_umum'],
                $stockOldDetail['harga_harian'],
                $stockOldDetail['harga_bulanan'],
                $stockOldDetail['no_po']
            );
        }

        $this->bc27Model->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 2.7 Berhasil Diposting"
        ]);
    }

    public function generateNomorAju()
    {
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $kodeDokumenbc40Static = $this->metaDataModel->where('name', "Kode BC27 Static")->first();

        $kodeKantorStatic = $ceisaSetting['kode_kantor_pabean'];
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = "";

        $bc27Last = $this->bc27Model->orderBy('createdAt', "DESC")->limit(1)->first();

        if ($bc27Last == null) {
            $sequenceNoUrutPengajuan = "000001";
        } else {
            if ($bc27Last['no_aju'] == null) {
                $sequenceNoUrutPengajuan = "000001";
            } else {
                // Buatkan auto increment
                $arrNo = explode('-', $bc27Last['no_aju']);
                $lastNomor = $arrNo[3];
                // lakukan increment
                $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
                $sequenceNoUrutPengajuan = $nextNomor;
            }
        }

        return $kodeDokumenbc40Static['value'] . '-' . $kodeKantorStatic . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan;
    }
}
