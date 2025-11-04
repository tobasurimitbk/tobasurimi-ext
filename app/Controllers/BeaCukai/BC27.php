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
use App\Models\KantorBeaCukaiModel;
use App\Models\PengusahaTPBModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\HsCodesModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\DivisisModel;
use App\Models\PenerimaanMutasiGlobalDetailModel;
use App\Models\PenerimaanMutasiGlobalModel;
use App\Models\WarehousesModel;
use Exception;
use Throwable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
    protected $kantorBeaCukaiModel;
    protected $pengusahaTPBModel;
    protected $barangMasterSpesifikasiModel;
    protected $hsCodeModel;
    protected $bcPurchaseOrderModel;
    protected $penerimaanMutasiGlobalModel;
    protected $penerimaanMutasiGlobalDetailModel;
    protected $divisiModel;
    protected $warehouseModel;

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
        $this->kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->hsCodeModel = new HsCodesModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $this->penerimaanMutasiGlobalModel = new PenerimaanMutasiGlobalModel();
        $this->penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();
        $this->divisiModel = new DivisisModel();
        $this->warehouseModel = new WarehousesModel();

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
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC27" =>  $this->request->getVar("mulaiTanggalBC27") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("mulaiTanggalBC27")))) : "",
            "selesaiTanggalBC27" => $this->request->getVar("selesaiTanggalBC27") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("selesaiTanggalBC27")))) : "",
            "search" => $this->request->getGet('search'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc27Model->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "tanggal"               => date('d/m/Y', strtotime($data->createdAt)),
                "no_mutasi"             => $data->no_mutasi,
                "company_asal"     => $data->company_asal,
                "divisi_asal"      => $data->divisi_asal,
                "warehouse_asal"   => $data->warehouse_asal,
                "company_tujuan"   => $data->company_tujuan,
                "no_aju"                => $data->no_aju . " / " . $data->no_daftar,
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
            'noAju' => $this->generateNomorAjuRevamp(date('Y-m-d')),
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
            'noAju' => $bc27['no_aju'],
            'bc27' => $bc27,
            'divisi' => $bc27['penerimaan_otomatis'] == 1 ? $this->divisiModel->where('company_id', $bc27['company_tujuan_id'])->where('deletedAt', null)->findAll() : [],
            'warehouse' => $bc27['penerimaan_otomatis'] == 1 ? $this->warehouseModel->where('divisi_id', $bc27['divisi_tujuan_id'])->where('deletedAt', null)->findAll() : [],
        ];

        return view('BeaCukai/bc-27/form', $data);
    }

    public function createAction()
    {
        // return response()->setJSON([
        //     '$_POST' => $_POST,
        //     'barang' => json_decode($_POST['barang']),
        //     'status' => false
        // ]);

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = $this->bc27Model->insert([
                'company_asal_id' => $this->this_company_id,
                'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
                'mutasi_global_id' => $this->request->getVar('mutasi_global_id'),
                'no_aju' => $this->request->getVar('no_aju'),
                'status_posting' => '0',
                'no_daftar' => $this->request->getVar('no_daftar'),
                'penerimaan_otomatis' => empty($this->request->getVar('penerimaan_otomatis')) ? 0 : $this->request->getVar('penerimaan_otomatis'),
                'divisi_tujuan_id' => empty($this->request->getVar('penerimaan_otomatis')) ? null : $this->request->getVar('divisi_tujuan_id'),
                'warehouse_tujuan_id' => empty($this->request->getVar('penerimaan_otomatis')) ? null : $this->request->getVar('warehouse_tujuan_id'),
                'createdAt' => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))))
            ]);

            $bc27 = $this->bc27Model->where('id', $id)->first();
            $barang = json_decode($_POST['barang']);
            if ($bc27['penerimaan_otomatis'] == 1) {
                // Penerimaan Otomatis
                foreach ($barang as $b) {
                    $this->mutasiGlobalDetailModel->update($b->mutasi->mutasi_detail_id, [
                        'company_tujuan_id' => $bc27['company_tujuan_id'],
                        'divisi_tujuan_id' => $bc27['divisi_tujuan_id'],
                        'warehouse_tujuan_id' => $bc27['warehouse_tujuan_id'],
                        'spesifikasi_hasil_id' => $b->mutasi->spesifikasi_hasil_id,
                        'unit_hasil_id' => $b->mutasi->unit_hasil_id
                    ]);
                }
            } else {
                // Bukan Penrimaan Otomatis
                foreach ($barang as $b) {
                    $this->mutasiGlobalDetailModel->update($b->mutasi->mutasi_detail_id, [
                        'company_tujuan_id' => null,
                        'divisi_tujuan_id' => null,
                        'warehouse_tujuan_id' => null,
                        'spesifikasi_hasil_id' => null,
                        'unit_hasil_id' => null
                    ]);
                }
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'message' => "Dokumen BC 27 Berhasil Disimpan",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false,
            ]);
        }
    }

    public function updateAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));

            $this->bc27Model->update($id, [
                'company_asal_id' => $this->this_company_id,
                'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
                'mutasi_global_id' => $this->request->getVar('mutasi_global_id'),
                'no_aju' => $this->request->getVar('no_aju'),
                'no_daftar' => $this->request->getVar('no_daftar'),
                'penerimaan_otomatis' => empty($this->request->getVar('penerimaan_otomatis')) ? 0 : $this->request->getVar('penerimaan_otomatis'),
                'divisi_tujuan_id' => empty($this->request->getVar('penerimaan_otomatis')) ? null : $this->request->getVar('divisi_tujuan_id'),
                'warehouse_tujuan_id' => empty($this->request->getVar('penerimaan_otomatis')) ? null : $this->request->getVar('warehouse_tujuan_id'),
                'createdAt' => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))))
            ]);

            $bc27 = $this->bc27Model->where('id', $id)->first();
            $barang = json_decode($_POST['barang']);

            if ($bc27['penerimaan_otomatis'] == 1) {
                // Penerimaan Otomatis
                foreach ($barang as $b) {
                    $this->mutasiGlobalDetailModel->update($b->mutasi->mutasi_detail_id, [
                        'company_tujuan_id' => $bc27['company_tujuan_id'],
                        'divisi_tujuan_id' => $bc27['divisi_tujuan_id'],
                        'warehouse_tujuan_id' => $bc27['warehouse_tujuan_id'],
                        'spesifikasi_hasil_id' => $b->mutasi->spesifikasi_hasil_id,
                        'unit_hasil_id' => $b->mutasi->unit_hasil_id
                    ]);
                }
            } else {
                // Bukan Penrimaan Otomatis
                foreach ($barang as $b) {
                    $this->mutasiGlobalDetailModel->update($b->mutasi->mutasi_detail_id, [
                        'company_tujuan_id' => null,
                        'divisi_tujuan_id' => null,
                        'warehouse_tujuan_id' => null,
                        'spesifikasi_hasil_id' => null,
                        'unit_hasil_id' => null
                    ]);
                }
            }
            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'message' => "Dokumen BC 27 Berhasil Diupdate",
                'token' => csrf_hash()
            ]);
        } catch (Throwable $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false,
            ]);
        }
    }

    public function getListMutasiDetail()
    {
        $mutasiGlobalId = $this->request->getVar('mutasi_global_id');
        $dataResultDetail = $this->mutasiGlobalDetailModel->getDetail($mutasiGlobalId);

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

    public function getNomorAju()
    {
        try {
            $tanggalStr = $this->request->getVar('tanggal');
            if (empty($tanggalStr)) {
                return response()->setJSON([
                    'data' => "",
                    'success' => true,
                    'token' => csrf_hash()
                ]);
            }

            $tanggal = formatDMYtoYMD($tanggalStr);
            $noAju = $this->generateNomorAjuRevamp($tanggal);
            return response()->setJSON([
                'data' => $noAju,
                'success' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
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
        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            $id = decrypt($this->request->getVar('id'));
            $bc27 = $this->bc27Model->where('id', $id)->first();

            $this->bc27Model->update($id, ['status_posting' => '1']);
            if ($bc27['penerimaan_otomatis'] == 1) {
                $this->terimaOtomatis(
                    $bc27['mutasi_global_id']
                );
            }

            $db->transCommit();
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Dokumen BC 2.7 Berhasil Diposting"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    private function terimaOtomatis($mutasiGlobalId)
    {
        $mutasiGlobal = $this->mutasiGlobalModel->where('id', $mutasiGlobalId)->first();
        $bc27 = $this->bc27Model->where('mutasi_global_id', $mutasiGlobalId)->first();
        $tanggal = date('Y-m-d', strtotime($bc27['createdAt']));

        $barang = $this->mutasiGlobalDetailModel->getDetail($mutasiGlobalId);
        $no = $this->get_no_str($tanggal);

        $id = $this->penerimaanMutasiGlobalModel->insert([
            'company_penerima_id' => $bc27['company_tujuan_id'],
            'company_pengirim_id' => $bc27['company_asal_id'],
            'divisi_penerima_id' => $bc27['divisi_tujuan_id'],
            'warehouse_penerima_id' => $bc27['warehouse_tujuan_id'],
            'penerimaan_mutasi_no' => $no,
            'multiple_mutasi_id' => "[" . $mutasiGlobal['id'] . "]",
            'multiple_no_mutasi' => json_encode([$mutasiGlobal['no_mutasi']], JSON_UNESCAPED_SLASHES),
            'tanggal' => $mutasiGlobal['tanggal'],
            'keterangan' => null,
            'status_posting' => '1',
            'createdBy' => $this->this_user_id
        ]);

        foreach ($barang as $b) {
            $this->penerimaanMutasiGlobalDetailModel->insert([
                'penerimaan_mutasi_global_id' => $id,
                'mutasi_global_id' => $mutasiGlobal['id'],
                'mutasi_global_detail_id' => $b['mutasi']['mutasi_detail_id'],
                'stock_detail_id' => null,
                'spesifikasi_hasil_id' => $b['mutasi']['spesifikasi_hasil_id'],
                'unit_hasil_id' => $b['mutasi']['unit_hasil_id'],
                'qty' => $b['mutasi']['qty_konversi'],
            ]);
        }

        // Insert Stok
        return true;
    }


    private function get_no_str($tanggal)
    {
        $tanggalParts = explode('-', $tanggal);
        $month = $tanggalParts[1];
        $year = $tanggalParts[0];

        $no = $this->penerimaanMutasiGlobalModel->get_no(
            $month,
            $year,
            $this->this_company_id,
        );

        return $no;
    }

    public function dropdownDivisiByCompany()
    {
        $companyTujuanId = $this->request->getVar('company_tujuan_id');
        $divisi = $this->divisiModel->where('company_id', $companyTujuanId)->where('deletedAt', null)->findAll();

        return response()->setJSON([
            'data' => $divisi,
            'status' => true
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

    public function generateNomorAjuRevamp($tanggalDokumen)
    {
        if ($this->this_company_id == 1 || $this->this_company_id == 2) {
            $company_id_arr = [1, 2];
        } else {
            $company_id_arr = [$this->this_company_id];
        }
        $ceisaSetting = $this->ceisaSettingModel
            ->where('company_id', $this->this_company_id)
            ->first();

        $kodeDokumenbc27Static = $this->metaDataModel
            ->where('name', "Kode BC27 Static")
            ->first();

        $kodeKantorStatic = $ceisaSetting['kode_unik']
            ? $ceisaSetting['kode_unik']
            : $ceisaSetting['kode_kantor_pabean'];

        $tanggalAju = date('Ymd', strtotime($tanggalDokumen));
        $tahunAjuSekarang = date('Y', strtotime($tanggalDokumen));

        // Ambil BC40 terakhir berdasarkan urutan no_aju terbaru
        $bc27Last = $this->bc27Model
            ->select('bc_27.*')
            ->whereIn('bc_27.company_asal_id', $company_id_arr)
            ->orderBy('bc_27.no_aju', "DESC")
            ->limit(1)
            ->first();

        // Default urutan
        $sequenceNoUrutPengajuan = "000001";

        if ($bc27Last && $bc27Last['no_aju']) {
            $arrNo = explode('-', $bc27Last['no_aju']);

            // Pastikan format sesuai: DOC-KANTOR-YYYYMMDD-NOMOR
            if (count($arrNo) === 4) {
                $tanggalTerakhir = $arrNo[2];
                $tahunTerakhir = substr($tanggalTerakhir, 0, 4);
                $lastNomor = $arrNo[3];

                if ($tahunTerakhir === $tahunAjuSekarang) {
                    // Masih tahun yang sama → lanjutkan nomor urut
                    $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
                    $sequenceNoUrutPengajuan = $nextNomor;
                } else {
                    // Tahun baru → reset ke 000001
                    $sequenceNoUrutPengajuan = "000001";
                }
            }
        }

        return $kodeDokumenbc27Static['value']
            . '-' . $kodeKantorStatic
            . '-' . $tanggalAju
            . '-' . $sequenceNoUrutPengajuan;
    }

    public function viewOutstanding()
    {
        return view('BeaCukai/bc-27/bc27outstanding');
    }

    public function allOutstanding()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = [
            "mutasi_global.company_asal_id"  => $this->this_company_id,
            "mutasi_global.deletedAt" => null,
            "mutasi_global.status_posting" => 1
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "search" => $this->request->getGet('search'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc27Model->getListOutstanding(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_mutasi"    => $data->no_mutasi,
                "company_asal" => $data->company_asal,
                "divisi_asal"    => $data->divisi_asal,
                "warehouse_asal"    => $data->warehouse_asal,
                "company_tujuan"    => $data->company_tujuan,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "kode_barang"      => $data->kode_barang,
                "barang_name"   => $data->barang_name,
                "spesifikasi" => $data->spesifikasi,
                "qty_konversi"        => $data->qty_konversi,
                "kode_satuan"              => $data->kode_satuan,
                "type_bc" => $data->type_bc,
                "no_aju"        => $data->no_aju,
                "no_daftar" => $data->no_daftar,
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

    public function OutstandingSheet()
    {
        $condition = [
            "mutasi_global.company_asal_id"  => $this->this_company_id,
            "mutasi_global.deletedAt" => null,
            "mutasi_global.status_posting" => 1
        ];

        $addCondition = [
            "sort" => "mutasi_global.tanggal",
            "sortType" => "desc",
            "search" => ""
        ];

        $beaCukaiData = $this->bc27Model->getListOutstanding(
            $condition,
            $addCondition,
            100000000,
            0
        );

        $dataBeaCukai = [];

        $no = 1;
        foreach ($beaCukaiData['data'] as $data) {
            $dataBeaCukai[] = [
                "no" => $no++,
                "no_mutasi"    => $data->no_mutasi,
                "company_asal" => $data->company_asal,
                "divisi_asal"  => $data->divisi_asal,
                "warehouse_asal" => $data->warehouse_asal,
                "company_tujuan" => $data->company_tujuan,
                "tanggal"      => date('d/m/Y', strtotime($data->tanggal)),
                "kode_barang"  => $data->kode_barang,
                "barang_name"  => $data->barang_name,
                "spesifikasi"  => $data->spesifikasi,
                "qty_konversi" => $data->qty_konversi,
                "kode_satuan"  => $data->kode_satuan,
                "type_bc"      => $data->type_bc,
                "no_aju"       => $data->no_aju,
                "no_daftar"    => $data->no_daftar,
            ];
        }

        // --- Buat Spreadsheet ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Outstanding Mutasi');

        // Header kolom
        $headers = [
            'No',
            'No Mutasi',
            'Company Asal',
            'Dept Asal',
            'Warehouse Asal',
            'Company Tujuan',
            'Tgl Mutasi',
            'Kode Barang',
            'Barang',
            'Spesifikasi',
            'Qty Mutasi',
            'Satuan',
            'Doc Masuk',
            'No Aju',
            'No Daftar'
        ];

        // Tulis header
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Styling header
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ];
        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Isi data
        $row = 2;
        foreach ($dataBeaCukai as $d) {
            $sheet->setCellValue("A$row", $d['no']);
            $sheet->setCellValue("B$row", $d['no_mutasi']);
            $sheet->setCellValue("C$row", $d['company_asal']);
            $sheet->setCellValue("D$row", $d['divisi_asal']);
            $sheet->setCellValue("E$row", $d['warehouse_asal']);
            $sheet->setCellValue("F$row", $d['company_tujuan']);
            $sheet->setCellValue("G$row", $d['tanggal']);
            $sheet->setCellValue("H$row", $d['kode_barang']);
            $sheet->setCellValue("I$row", $d['barang_name']);
            $sheet->setCellValue("J$row", $d['spesifikasi']);
            $sheet->setCellValue("K$row", $d['qty_konversi']);
            $sheet->setCellValue("L$row", $d['kode_satuan']);
            $sheet->setCellValue("M$row", $d['type_bc']);
            $sheet->setCellValue("N$row", $d['no_aju']);
            $sheet->setCellValue("O$row", $d['no_daftar']);
            $row++;
        }

        // Auto size semua kolom
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border untuk seluruh tabel
        $sheet->getStyle('A1:O' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        // Output ke browser
        $filename = 'Outstanding_Mutasi_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    public function kirimCeisa($id)
    {
        $id = decrypt($id);
        $payload = json_decode($this->bc27Model->find($id)['payload']);
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);

        // return response()->setJSON([
        //     'payload' => $payload
        // ]);
        // die;
        $res = $beacukaiApi->kirimDokumenBC($payload, false);
        if ($res['status'] == false) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal Kirim Ceisa Karena : " . $res['message'],
            ]);
        }
        // // UPDATE STATUS
        $this->bc27Model->set('status_dokumen', "Sudah Kirim")->where('id', $id)->update();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 2.7 Berhasil Diposting",
            'res' => $res
        ]);
    }

    private function setFlashDataNavigatorSession($id)
    {
        $isCompleteFormHeader = $this->bc27Model->isCompleteFormHeader($id);
        $isCompleteFormEntitas = $this->bc27Model->isCompleteFormEntitas($id);
        $isCompleteFormDokumen = $this->bc27Model->isCompleteFormDokumen($id);
        $isCompleteFormPengangkut = $this->bc27Model->isCompleteFormPengangkut($id);
        $isCompleteFormPetiKemas = $this->bc27Model->isCompleteFormPetiKemas($id);
        $isCompleteFormTransaksi = $this->bc27Model->isCompleteFormTransaksi($id);
        $isCompleteFormBarang = $this->bc27Model->isCompleteFormBarang($id);
        $isCompleteFormPernyataan = $this->bc27Model->isCompleteFormPernyataan($id);


        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormTransaksi);
        session()->setFlashdata('isCompleteFormBarang', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPungutan', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPernyataan', $isCompleteFormPernyataan);

        if (
            $isCompleteFormHeader && $isCompleteFormEntitas &&
            $isCompleteFormEntitas && $isCompleteFormDokumen && $isCompleteFormPengangkut &&
            $isCompleteFormPetiKemas && $isCompleteFormTransaksi
        ) {
            $this->bc27Model->set('status_dokumen', "Siap Kirim")->where('id', $id)->update();
        } else {
            $this->bc27Model->set('status_dokumen', "Belum Lengkap")->where('id', $id)->update();
        }
    }

    //ceisa router
    public function header($id)
    {
        $id = decrypt($id);

        $bc27 = $this->bc27Model->find($id);
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $data = [
            'bc27' => $bc27,
            'noAju' => $bc27 == null ? $this->generateNomorAju() : $bc27['no_aju'],
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'kodeLokasiBayar' => $this->metaDataModel->where('name', "KODE LOKASI BAYAR")->findAll(),
            'kodeTujuanTpb' => $this->metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTujuanPengiriman' => $this->metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', 40)->where('deletedAt', null)->findAll(),
            'kodeCaraBayar' => $this->metaDataModel->where('name', "CARA BAYAR")->findAll(),
            'selectedKantor' => $ceisaSetting['kode_kantor_pabean'],
            'payload' => json_decode($bc27['payload'])
        ];

        return view('BeaCukai/bc-27/form-header', $data);
    }
    public function updateHeader()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);

        $payload->asalData = "S";
        $payload->nomorAju =  str_replace('-', '', $this->request->getVar('nomorAju'));
        $payload->tanggalAju = getDateFromNomorAju($this->request->getVar('nomorAju'));
        $payload->kodeKantor = $this->request->getVar('kodeKantor');
        $payload->kodeKantorTujuan = $this->request->getVar('kodeKantorTujuan');
        $payload->kodeJenisTpb = $this->request->getVar('kodeJenisTpb');
        $payload->kodeTujuanTpb = $this->request->getVar('kodeTujuanTpb');
        $payload->kodeTujuanPengiriman = $this->request->getVar('kodeTujuanPengiriman');
        $payload->seri = 1;
        $payload->disclaimer = "1";
        $payload->kodeDokumen = "27";

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Header berhasil disimpan"
        ]);
    }

    public function entitas($id)
    {
        $id = decrypt($id);

        $bc27 = $this->bc27Model->find($id);
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->findAll();

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);

        $payload = json_decode($bc27['payload']);
        // JIKA MASIH KOSONG SET DULU BOSQ
        if (!is_array($payload->entitas)) {
            $payload->entitas = [
                [
                    'alamatEntitas' => "",
                    'kodeEntitas' => "3",
                    'kodeJenisApi' => "2",
                    'kodeJenisIdentitas' => "5",
                    'kodeStatus' => '3',
                    'namaEntitas' => "",
                    'nibEntitas' => "",
                    'nomorIdentitas' => "",
                    'nomorIjinEntitas' => "",
                    'tanggalIjinEntitas' => "",
                    'seriEntitas' => 1,
                ],
                [
                    'alamatEntitas' => "",
                    'kodeEntitas' => "7",
                    'kodeJenisIdentitas' => "5",
                    'kodeStatus' => '3',
                    'namaEntitas' => "",
                    'nomorIdentitas' => "",
                    'seriEntitas' => 2,
                    'kodeJenisApi' => "2",

                ],
                [
                    'alamatEntitas' => "",
                    'kodeEntitas' => "3",
                    'kodeJenisApi' => "2",
                    'kodeJenisIdentitas' => "5",
                    'kodeStatus' => '3',
                    'namaEntitas' => "",
                    'nomorIdentitas' => "",
                    'seriEntitas' => 3,
                    'nomorIjinEntitas' => "",
                    'tanggalIjinEntitas' => "",
                ]
            ];
            $this->bc27Model->update($id, ['payload' => json_encode($payload)]);
        }

        $data = [
            'bc27' => $bc27,
            'pengusahaTPB' => $pengusahaTPB,
            'payload' => json_decode($this->bc27Model->find($id)['payload'])
        ];

        return view('BeaCukai/bc-27/form-entitas', $data);
    }

    public function updateEntitas()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);


        $payload->entitas[0] = [
            'alamatEntitas' => $this->request->getVar('entitas_alamat_pengusaha'),
            'kodeEntitas' => "3",
            'kodeJenisApi' => "2",
            'kodeJenisIdentitas' => "5",
            'kodeStatus' => '3',
            'namaEntitas' => $this->request->getVar('entitas_nama_pengusaha'),
            'nibEntitas' => $this->request->getVar('entitas_nib'),
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_pengusaha'),
            'nomorIjinEntitas' => $this->request->getVar('entitas_nomor_ijin_tpb'),
            'tanggalIjinEntitas' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb')), "Y-m-d"),
            'seriEntitas' => 1,
        ];

        $payload->entitas[1] = [
            'alamatEntitas' => $this->request->getVar('entitas_alamat_pemilik_barang'),
            'kodeEntitas' => "7",
            'kodeJenisIdentitas' => "5",
            'kodeStatus' => '3',
            'namaEntitas' => $this->request->getVar('entitas_nama_pemilik_barang'),
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_pemilik_barang'),
            'kodeJenisApi' => "2",
            'seriEntitas' => 2,
        ];

        $payload->entitas[2] = [
            'alamatEntitas' => $this->request->getVar('entitas_alamat_penerima_barang'),
            'kodeEntitas' => "8",
            'kodeJenisApi' => "2",
            'kodeJenisIdentitas' => "5",
            'kodeStatus' => '3',
            'namaEntitas' => $this->request->getVar('entitas_nama_penerima_barang'),
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_penerima_barang'),
            'seriEntitas' => 3,
            'nomorIjinEntitas' => $this->request->getVar('entitas_nomor_ijin_tpb_penerima_barang'),
            'tanggalIjinEntitas' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb_penerima_barang')), "Y-m-d"),

        ];

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);
        return response()->setJSON([
            'status' => true,
            'message' => "Entitas berhasil disimpan"
        ]);
    }

    public function dokumen($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

        if (!is_array($payload->dokumen)) {
            $payload->dokumen = [];
            $this->bc27Model->update($id, ['payload' => json_encode($payload)]);
        }
        $dokumen = [];
        foreach (json_decode($this->bc27Model->find($id)['payload'])->dokumen as $d) {
            $dokumenDetail = $this->metaDataModel->where('name', "Dokumen")->where('description', $d->kodeDokumen)->first();
            $value = ($dokumenDetail == null) ? "" : $dokumenDetail['value'];
            array_push($dokumen, [
                'seriDokumen' => $d->seriDokumen,
                'kodeDokumen' => $d->kodeDokumen . " - " . $value,
                'nomorDokumen' => $d->nomorDokumen,
                'tanggalDokumen' => date('d/m/Y', strtotime($d->tanggalDokumen))
            ]);
        }

        $data = [
            'bc27' => $bc27,
            'kodeDokumen' => $this->metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'dokumen' => $dokumen,
        ];

        return view('BeaCukai/bc-27/form-dokumen', $data);
    }

    public function updateDokumen()
    {
        // INVOICE = 30
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc27Model->find($id)['payload']);
        $indexLast = count($payload->dokumen) == 0 ? 0 : count($payload->dokumen) - 1;
        $seriDokumen = count($payload->dokumen) == 0 ? 1 : $payload->dokumen[$indexLast]->seriDokumen + 1;

        $kodeDokumen = $this->request->getVar('dokumen_jenis_dokumen');
        if ($seriDokumen == 1) {
            // HARUS INVOICE
            if ($kodeDokumen != 380) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Dokumen seri pertama wajib invoice ",
                ]);
            }
        }

        array_push($payload->dokumen, [
            'idDokumen' =>  generateUniqueCode(5),
            'kodeDokumen' => $kodeDokumen,
            'nomorDokumen' => $this->request->getVar('dokumen_nomor_dokumen'),
            'seriDokumen' => $seriDokumen,
            'tanggalDokumen' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('dokumen_tanggal')), "Y-m-d"),
        ]);

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil disimpan"
        ]);
    }

    public function deleteDokumen()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        unset($payload->dokumen[$indexDelete]);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function pengangkut($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

        $data = [
            'pengangkut' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'bc27' => $bc27,
            'payload' => $payload,
        ];

        return view('BeaCukai/bc-27/form-pengangkut', $data);
    }

    public function pengangkutUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        $payload->pengangkut[0] = [
            'namaPengangkut' => $this->request->getVar('pengangkut_nama_pengangkut'),
            'nomorPengangkut' => $this->request->getVar('pengangkut_nomor_pengangkut'),
            'kodeCaraAngkut' => $this->request->getVar('pengangkut_kode_cara_angkut'),
            'seriPengangkut' => '1'
        ];

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengangkut berhasil disimpan"
        ]);
    }
    public function kemasanPetiKemas($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

        $indexLastKemasan = count($payload->kemasan) == 0 ? 0 : count($payload->kemasan) - 1;
        $indexLastKontainer = count($payload->kontainer) == 0 ? 0 : count($payload->kontainer) - 1;
        $seriKemasan = count($payload->kemasan) == 0 ? 1 : $payload->kemasan[$indexLastKemasan]->seriKemasan + 1;
        $seriKontainer = count($payload->kontainer) == 0 ? 1 : $payload->kontainer[$indexLastKontainer]->seriKontainer + 1;

        $dataKemasan = [];
        $dataKontainer = [];

        // KEMASAN
        foreach ($payload->kemasan as $k) {
            $jenisKemasanDetail = $this->metaDataModel->where('name', 'Jenis Kemasan')->where('description', $k->kodeJenisKemasan)->first();

            $kemasanDetail = $this->barangMasterSpesifikasiModel->select("
                CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang, 

            ")
                ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
                ->where('barang_master_spesifikasi.id', $k->kemasanInventoriId)
                ->first();

            array_push($dataKemasan, [
                'jumlahKemasan' => $k->jumlahKemasan,
                'kodeJenisKemasan' => $k->kodeJenisKemasan . " - " . strtoupper($jenisKemasanDetail['value']),
                'merkKemasan' => $k->merkKemasan,
                'seriKemasan' => $k->seriKemasan,
                'namaKemasanInventori' => $kemasanDetail == null ? "-" : $kemasanDetail['barang']
            ]);
        }

        // KONTAINER
        foreach ($payload->kontainer as $k) {
            $jenisKontainerDetail = $this->metaDataModel->where('name', "Jenis Kontainer")->where('description', $k->kodeJenisKontainer)->first();
            $tipeKontainerDetail = $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->where('value', $k->kodeTipeKontainer)->first();
            $ukuranKontainerDetail = $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->where('value', $k->kodeUkuranKontainer)->first();

            array_push($dataKontainer, [
                'kodeJenisKontainer' => $k->kodeJenisKontainer . " - " .  $jenisKontainerDetail['value'],
                'kodeTipeKontainer' => $k->kodeTipeKontainer . " - " .  $tipeKontainerDetail['description'],
                'kodeUkuranKontainer' => $k->kodeUkuranKontainer . " - " . $ukuranKontainerDetail['description'],
                'nomorKontainer' => $k->nomorKontainer,
                'seriKontainer' => $k->seriKontainer
            ]);
        }

        $data = [
            'bc27' => $bc27,
            'payload' => $payload,
            'seriKemasan' => $seriKemasan,
            'seriKontainer' => $seriKontainer,
            'dropdownKemasan' => $this->bc27Model->dropdownKemasan($bc27['mutasi_global_id']),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $this->metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'dataKemasan' => $dataKemasan,
            'dataKontainer' => $dataKontainer,
        ];

        return view('BeaCukai/bc-27/form-kemasan-peti-kemas', $data);
    }

    public function kemasanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);

        array_push($payload->kemasan, [
            'jumlahKemasan' => (float)$this->request->getVar('kemasan_jumlah_kemasan'),
            'kodeJenisKemasan' => $this->request->getVar('kemasan_jenis_kemasan'),
            'merkKemasan' => $this->request->getVar('kemasan_merk_kemasan'),
            'seriKemasan' => (int)$this->request->getVar('kemasan_seri_kemasan'),
            'kemasanInventoriId' => $this->request->getVar('kemasan_inventori_id'),
        ]);

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kemasan berhasil disimpan"
        ]);
    }

    public function deleteKemasan()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        unset($payload->kemasan[$indexDelete]);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function kontainerUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);

        array_push($payload->kontainer, [
            'kodeJenisKontainer' => $this->request->getVar('kontainer_jenis'),
            'kodeTipeKontainer' => $this->request->getVar('kontainer_tipe'),
            'kodeUkuranKontainer' => $this->request->getVar('kontainer_ukuran'),
            'nomorKontainer' => $this->request->getVar('kontainer_nomor'),
            'seriKontainer' => (int)$this->request->getVar('kontainer_seri'),
        ]);

        $payload->jumlahKontainer = count($payload->kontainer);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil disimpan"
        ]);
    }

    public function deleteKontainer()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        unset($payload->kontainer[$indexDelete]);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil dihapus"
        ]);
    }

    public function transaksi($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

        $totalHargaBarang = 0;

        $data = [
            'kodeValuta' => $this->metaDataModel->where('name', "Valuta")->findAll(),
            'kodeCaraBayar' => $this->metaDataModel->where('name', "CARA BAYAR")->findAll(),
            'kodeLokasiBayar' => $this->metaDataModel->where('name', "Lokasi Bayar")->findAll(),
            'bc27' => $bc27,
            'payload' => $payload,
        ];

        return view('BeaCukai/bc-27/form-transaksi', $data);
    }

    public function transaksiUpdate()
    {
        $id = decrypt($this->request->getVar('id'));

        if ($this->request->getVar('berat_bruto') < $this->request->getVar('berat_netto')) {
            return response()->setJSON([
                'status' => false,
                'message' => "Berat Bruto harus lebih besar daripada Berat Netto"
            ]);
        }
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        $payload->kodeValuta = $this->request->getVar('harga_kode_valuta');
        $payload->ndpbm = (float)convertRupiahToNumber($this->request->getVar('harga_ndpbm'));
        $payload->cif = (float)convertRupiahToNumber($this->request->getVar('harga_cif'));
        $payload->hargaPenyerahan = (float)convertRupiahToNumber($this->request->getVar('harga_nilai_penyerahan'));
        $payload->nilaiJasa = (float)convertRupiahToNumber($this->request->getVar('nilai_jasa'));
        $payload->uangMuka = (float)convertRupiahToNumber($this->request->getVar('nilai_muka'));
        // $payload->kodeCaraBayar = $this->request->getVar('cara_bayar');
        // $payload->kodeLokasiBayar = $this->request->getVar('lokasi_bayar');

        $payload->bruto = intval($this->request->getVar('berat_bruto'));
        $payload->netto = intval($this->request->getVar('berat_netto'));

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Transaksi berhasil diupdate"
        ]);
    }

    public function barang($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);


        $data = [
            'bc27' => $bc27,
            'payload' => $payload,
            'barang' => $this->bc27Model->barang($bc27['mutasi_global_id'])
        ];

        return view('BeaCukai/bc-27/form-barang', $data);
    }

    public function barangDetail($id, $kodeBarang)
    {
        $id = decrypt($id);
        $this->setFlashDataNavigatorSession($id);

        $kodeBarang = decrypt($kodeBarang);
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($this->bc27Model->find($id)['payload']);
        $detailBarang = $this->bc27Model->detailBarang($bc27['id'], $kodeBarang, $bc27['mutasi_global_id']);
        $totalBarang = count($this->bc27Model->barang($bc27['mutasi_global_id']));

        if ($detailBarang['bcDetail'] == NULL) {
            $indexLast = count($payload->barang) == 0 ? 0 : count($payload->barang) - 1;
            $seriBarang = count($payload->barang) == 0 ? 1 :  intval($payload->barang[$indexLast]->seriBarang) + 1;
            $ndpbm = $payload->ndpbm / $totalBarang;
            $cif = $payload->cif / $totalBarang;
            $bruto = $payload->bruto / $totalBarang;


            array_push($payload->barang, [
                'cif' => intval($cif),
                'cifRupiah' => intval($cif),
                'hargaEkspor' => 0,
                'hargaPenyerahan' => 0,
                'isiPerKemasan' => 0,
                'jumlahSatuan' => $detailBarang['barangDetail']['qty'],
                'kodeBarang' => $detailBarang['barangDetail']['kode_barang'],
                'kodeDokumen' => '27',
                'kodeSatuanBarang' => '',
                'merk' => '',
                'netto' => 0,
                'nilaiBarang' => 0,
                'posTarif' => "",
                'seriBarang' => $seriBarang,
                'spesifikasiLain' => "",
                'tipe' => "",
                'ukuran' => '',
                'uraian' => '',
                'hargaPerolehan' => 0,
                'ndpbm' => $ndpbm,
                'uangMuka' => 0,
                'nilaiJasa' => 0,
                // 'barangDokumen' => "",
                // 'barangTarif' => "",
                // 'diskon' =>  " ",
                // 'fob' => '',
                // 'freight' => 0,
                // 'jumlahKemasan' => "",
                // 'kodeDokAsal' => '',
                // 'kodeGunaBarang' => "",
                // 'kodeJenisKemasan' => "",
                // 'kodeKategoriBarang' => "",
                // 'kodeKondisiBarang' => "",
                // 'kodePerhitungan' => "",

                'bahanBaku' => [],
            ]);
        }

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        $data = [
            'bc27' => $bc27,
            'barang' => $this->bc27Model->detailBarang($bc27['id'], $kodeBarang, $bc27['mutasi_global_id']),
            'kodeHS' => $this->hsCodeModel->findAll(),
            'kodeGunaBarang' => $this->metaDataModel->where('name', "KODE GUNA BARANG")->findAll(),
            'kodeKategoriBarang' => $this->metaDataModel->where('name', "Kategori Barang BC")->like('description', '25')->findAll(),
            'kodeKondisiBarang' => $this->metaDataModel->where('name', "KONDISI BARANG")->findAll(),
            'kodePerhitungan' =>  $this->metaDataModel->where('name', "KODE PERHITUNGAN")->findAll(),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', "Jenis Kemasan")->findAll(),
            'kodeJenisPungutan' => $this->metaDataModel->where('name', "Kode Jenis Pungutan BC")->whereIn('value', ['BM', 'PPN', 'PPH'])->findAll(),
            'kodeJenisTarif' => $this->metaDataModel->where('name', "Kode Jenis Tarif BC")->findAll(),
            'kodeFasilitasTarif' => $this->metaDataModel->where('name', "Kode Fasilitas Tarif BC")->whereIn('value', ['1', '2', '5', '7'])->findAll(),
            'kodeSatuanBarang' => null,
            'dokumen' => [],
            'dokumenSelected' => [], // SERI DOKUMEN YANG DI CHEKLIST
            'pungutan' => [],
            'bahanBakuLokal' => [],
            'bahanBakuImpor' => []
        ];
        $data['kodeSatuanBarang'] = $this->metaDataModel->where('name', "Kode Satuan BC")->where('value', $data['barang']['bcDetail']->kodeSatuanBarang)->findAll();

        foreach ($data['barang']['bcDetail']->bahanBaku as $i => $b) {
            if ($b->kodeDokAsal == "23") {
                // EKSPOR
                array_push($data['bahanBakuImpor'], [
                    'indexDelete' => $i,
                    'seriBarang' => $b->seriBarang,
                    'posTarif' => $b->posTarif,
                    'uraianBarang' => $b->uraianBarang,
                    'hargaPenyerahan' => $b->hargaPenyerahan,
                    'kodeSatuanBarang' => $b->kodeSatuanBarang
                ]);
            } else {
                // LOKAL
                array_push($data['bahanBakuLokal'], [
                    'indexDelete' => $i,
                    'seriBarang' => $b->seriBarang,
                    'posTarif' => $b->posTarif,
                    'uraianBarang' => $b->uraianBarang,
                    'hargaPenyerahan' => $b->hargaPenyerahan,
                    'kodeSatuanBarang' => $b->kodeSatuanBarang
                ]);
            }
        }

        return view('BeaCukai/bc-27/form-detail-barang', $data);
    }



    public function barangDetailUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        $seriBarang = $this->request->getVar('seriBarang');

        $nettoTotal = 0;
        $hargaPenyerahanTotal = 0;
        // $volumeTotal = 0;
        for ($i = 0; $i < count($payload->barang); $i++) {
            if ($payload->barang[$i]->seriBarang == $seriBarang) {
                $payload->barang[$i]->cif = (float) $this->request->getVar('cif');
                $payload->barang[$i]->cifRupiah = (float) $this->request->getVar('cif');
                $payload->barang[$i]->hargaEkspor =  (float)convertRupiahToNumber($this->request->getVar('hargaEkspor'));
                $payload->barang[$i]->hargaPenyerahan = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
                $payload->barang[$i]->isiPerKemasan = 1; //idk man
                $payload->barang[$i]->jumlahSatuan = (float)$this->request->getVar('jumlahSatuan');
                $payload->barang[$i]->kodeBarang = $this->request->getVar('kodeBarang');
                $payload->barang[$i]->kodeDokumen = "";
                $payload->barang[$i]->kodeSatuanBarang = "" . decrypt($this->request->getVar('kodeSatuanBarang'));
                $payload->barang[$i]->merk = $this->request->getVar('merk');
                $payload->barang[$i]->netto = $this->request->getVar('netto');
                $payload->barang[$i]->nilaiBarang = 0; //idk
                $payload->barang[$i]->posTarif = $this->request->getVar('posTarif');
                $payload->barang[$i]->seriBarang = $this->request->getVar('seriBarang');
                $payload->barang[$i]->spesifikasiLain = $this->request->getVar('spesifikasiLain');
                $payload->barang[$i]->tipe = $this->request->getVar('tipe');
                $payload->barang[$i]->netto = (float)$this->request->getVar('netto');
                $payload->barang[$i]->uraian = $this->request->getVar('uraian');
                $payload->barang[$i]->hargaPerolehan = (float)convertRupiahToNumber($this->request->getVar('hargaPerolehan'));
                $payload->barang[$i]->ndpbm = 0; //idk
                $payload->barang[$i]->nilaiJasa = (float)convertRupiahToNumber($this->request->getVar('nilaiJasa'));
                // $payload->barang[$i]->volume = $this->request->getVar('volume');
            }
            $nettoTotal += $payload->barang[$i]->netto;
            $hargaPenyerahanTotal += $payload->barang[$i]->hargaPenyerahan;
            // $volumeTotal += $payload->barang[$i]->volume;
        }

        $payload->netto = $nettoTotal;
        $payload->hargaPenyerahan = $hargaPenyerahanTotal;
        // $payload->volume = $volumeTotal;

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);


        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil update detail barang"
        ]);
    }

    public function bahanBakuUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $bcPurchaseOrderId = $this->request->getVar('bcPurchaseOrderId');

        $bahanBakuBCList = json_decode($this->request->getVar('bahanBakuBCList'));
        $payload = json_decode($this->bc27Model->find($id)['payload']);
        $indexBarang = 0;
        $seriBahanBakuLast = 1;

        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
                break;
            }
        }


        if (count($payload->barang[$indexBarang]->bahanBaku) != 0) {
            $seriBahanBakuLast = count($payload->barang[$indexBarang]->bahanBaku) + 1;
        }

        $bcPurchaseOrder = $this->bcPurchaseOrderModel->find($bcPurchaseOrderId);


        foreach ($bahanBakuBCList->barang as $b) {
            array_push($payload->barang[$indexBarang]->bahanBaku, [
                'cif' => (float) $b->cif,
                'cifRupiah' => (float) $b->cifRupiah,
                'hargaPenyerahan' => $b->hargaPenyerahan,
                'hargaPerolehan' => 0,
                'jumlahSatuan' => $b->jumlahSatuan,
                'kodeSatuanBarang' => $b->kodeSatuanBarang,
                'kodeAsalBahanBaku' => $b->kodeAsalBahanBaku,
                'kodeBarang' => $b->kodeBarang,
                'kodeDokAsal' => $bahanBakuBCList->kodeDokumen,
                'kodeKantor' => $bahanBakuBCList->kodeKantor,
                'merkBarang' => $b->merk,
                'ndpbm' => $b->ndpbm,
                'netto' => $b->netto,
                'nomorAjuDokAsal' => $bahanBakuBCList->nomorAju,
                'nomorDaftarDokAsal' => $bcPurchaseOrder['no_daftar'],
                'posTarif' => $b->posTarif,
                'seriBahanBaku' => $seriBahanBakuLast,
                'seriBarang' => $seriBahanBakuLast,
                'seriBarangDokAsal' => $b->seriBarang,
                'seriIjin' => 0,
                'spesifikasiLainBarang' => $b->spesifikasiLain,
                'tanggalDaftarDokAsal' => $bahanBakuBCList->tanggalTtd,
                'tipeBarang' => $b->tipe,
                'ukuranBarang' => $b->ukuran,
                'uraianBarang' => $b->uraian,
                'nilaiJasa' => $payload->barang[$indexBarang]->nilaiJasa,
                'bahanBakuTarif' => $b->barangTarif,

            ]);
            // foreach ($payload->barang[$indexBarang]->bahanBaku as $bb) {
            //     foreach ($bb->bahanBakuTarif as $bf) {
            //         $bf->seriBahanBaku = $seriBahanBakuLast;
            //         $bf->kodeAsalBahanBaku = $b->kodeAsalBahanBaku;
            //         // $bb[$i]['seriBahanBaku'] = $seriBahanBakuLast;
            //         // $bb[$i]['kodeAsalBahanBaku'] = $b->kodeAsalBahanBaku;
            //     }
            // }

            $seriBahanBakuLast++;
        }

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Bahan baku berhasil disimpan",
            'status' => true,
        ]);
    }

    public function bahanBakuDelete()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $indexDelete = $this->request->getVar('indexDelete');
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        $indexBarang = 0;

        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        unset($payload->barang[$indexBarang]->bahanBaku[$indexDelete]);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Bahan baku berhasil dihapus",
            'status' => true,
        ]);
    }

    public function pungutan($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $this->generatePungutan($id);
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);
        $kodeJenisPungutan = $this->metaDataModel
            ->where('name', "Kode Jenis Pungutan BC")
            ->whereIn('value', ['BM', 'PPN', 'PPH'])
            ->findAll();

        $pungutanList = [];

        $bmDibayar = 0;
        $bmDitanggung = 0;
        $bmDibebaskan = 0;
        $bmSudahDilunasi = 0;

        $pphDibayar = 0;
        $pphDitanggung = 0;
        $pphDibebaskan = 0;
        $pphSudahDilunasi = 0;

        $ppnDibayar = 0;
        $ppnDitanggung = 0;
        $ppnDibebaskan = 0;
        $ppnSudahDilunasi = 0;


        foreach ($payload->pungutan as $p) {
            if ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 1) {
                $bmDibayar += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 2) {
                $bmDitanggung += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 5) {
                $bmDibebaskan += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 7) {
                $bmSudahDilunasi += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 1) {
                $ppnDibayar += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 2) {
                $ppnDitanggung  += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 5) {
                $ppnDibebaskan  += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 7) {
                $ppnSudahDilunasi  += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 1) {
                $pphDibayar  += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 2) {
                $pphDitanggung  += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 5) {
                $pphDibebaskan  += $p->nilaiPungutan;
            } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 7) {
                $pphSudahDilunasi  += $p->nilaiPungutan;
            }
        }

        foreach ($kodeJenisPungutan as $k) {
            if ($k['value'] == "BM") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $bmDibayar,
                    'ditanggung' => $bmDitanggung,
                    'dibebaskan' => $bmDibebaskan,
                    'sudahDilunasi' => $bmSudahDilunasi

                ];
            } elseif ($k['value'] == "PPH") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $pphDibayar,
                    'ditanggung' => $pphDitanggung,
                    'dibebaskan' => $pphDibebaskan,
                    'sudahDilunasi' => $pphSudahDilunasi

                ];
            } elseif ($k['value'] == "PPN") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $ppnDibayar,
                    'ditanggung' => $ppnDitanggung,
                    'dibebaskan' => $ppnDibebaskan,
                    'sudahDilunasi' => $ppnSudahDilunasi
                ];
            }
        }

        $data = [
            'bc27' => $bc27,
            'payload' => $payload,
            'pungutanList' => $pungutanList,
        ];


        return view('BeaCukai/bc-27/form-pungutan', $data);
    }

    private function generatePungutan($id)
    {
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);
        $idPungutan = 1;

        $payload->pungutan = [];
        foreach ($payload->barang as $b) {
            foreach ($b->bahanBaku as $bb) {
                foreach ($bb->bahanBakuTarif as $t) {
                    array_push($payload->pungutan, [
                        "idPungutan" => "$idPungutan",
                        "kodeFasilitasTarif" => $t->kodeFasilitasTarif,
                        "kodeJenisPungutan" => $t->kodeJenisPungutan,
                        "nilaiPungutan" => $t->nilaiBayar
                    ]);
                    $idPungutan++;
                }
            }
        }
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);
    }

    public function pernyataan($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

        $data = [
            'bc27' => $bc27,
            'payload' => $payload,
            'ceisaSetting' => $ceisaSetting
        ];


        return view('BeaCukai/bc-27/form-penyataan', $data);
    }

    public function pernyataanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        $payload->kotaTtd = $this->request->getVar('kotaTtd');
        $payload->tanggalTtd = date('Y-m-d', strtotime($this->request->getVar('tanggalTtd')));
        $payload->namaTtd = $this->request->getVar('namaTtd');
        $payload->jabatanTtd = $this->request->getVar('jabatanTtd');

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pernyataan berhasil diupdate"
        ]);
    }
}
