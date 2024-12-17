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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PDO;

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
            'bc27' => $bc27,
            'divisi' => $bc27['penerimaan_otomatis'] == 1 ? $this->divisiModel->where('company_id', $bc27['company_tujuan_id'])->where('deletedAt', null)->findAll() : [],
            'warehouse' => $bc27['penerimaan_otomatis'] == 1 ? $this->warehouseModel->where('divisi_id', $bc27['divisi_tujuan_id'])->where('deletedAt', null)->findAll() : [],
        ];

        return view('BeaCukai/bc-27/form', $data);
    }

    public function createAction()
    {
        $db = \Config\Database::connect();
        $db->transStart();

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
                $this->mutasiGlobalDetailModel->update($b->mutasi_global_detail_id, [
                    'company_tujuan_id' => $bc27['company_tujuan_id'],
                    'divisi_tujuan_id' => $bc27['divisi_tujuan_id'],
                    'warehouse_tujuan_id' => $bc27['warehouse_tujuan_id'],
                    'stock_mutasi_id' => $b->stock_mutasi_id,
                    'qty_diterima' => $b->qty_diterima
                ]);
            }
        } else {
            // Bukan Penrimaan Otomatis
            foreach ($barang as $b) {
                $this->mutasiGlobalDetailModel->update($b->mutasi_global_detail_id, [
                    'company_tujuan_id' => null,
                    'divisi_tujuan_id' => null,
                    'warehouse_tujuan_id' => null,
                    'stock_mutasi_id' => null,
                    'qty_diterima' => null,
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() == false) {
            $db->transRollback();
        }

        $db->transCommit();

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 27 Berhasil Disimpan"
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $db = \Config\Database::connect();
        $db->transStart();

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

        $barang = json_decode($_POST['barang']);
        $bc27 = $this->bc27Model->where('id', $id)->first();
        if ($bc27['penerimaan_otomatis'] == 1) {
            // Penerimaan Otomatis
            foreach ($barang as $b) {
                $this->mutasiGlobalDetailModel->update($b->mutasi_global_detail_id, [
                    'company_tujuan_id' => $bc27['company_tujuan_id'],
                    'divisi_tujuan_id' => $bc27['divisi_tujuan_id'],
                    'warehouse_tujuan_id' => $bc27['warehouse_tujuan_id'],
                    'stock_mutasi_id' => $b->stock_mutasi_id,
                    'qty_diterima' => $b->qty_diterima
                ]);
            }
        } else {
            // Bukan Penrimaan Otomatis
            foreach ($barang as $b) {
                $this->mutasiGlobalDetailModel->update($b->mutasi_global_detail_id, [
                    'company_tujuan_id' => null,
                    'divisi_tujuan_id' => null,
                    'warehouse_tujuan_id' => null,
                    'stock_mutasi_id' => null,
                    'qty_diterima' => null,
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() == false) {
            $db->transRollback();
        }

        $db->transCommit();

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

        $db = \Config\Database::connect();
        $db->transStart();

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
        if ($bc27['penerimaan_otomatis'] == 1) {
            $this->automaticInsertPenerimaanMutasi($mutasiGlobal['id']);
        }

        $db->transComplete();

        if ($db->transStatus() == false) {
            $db->transRollback();
        }

        $db->transCommit();

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 2.7 Berhasil Diposting"
        ]);
    }

    private function automaticInsertPenerimaanMutasi($mutasiGlobalId)
    {

        $mutasiGlobal = $this->mutasiGlobalModel->where('id', $mutasiGlobalId)->first();
        $bc27 = $this->bc27Model->where('mutasi_global_id', $mutasiGlobalId)->first();
        $barang = $this->mutasiGlobalDetailModel->getMutasiDetail(
            $mutasiGlobalId
        );
        $no = $this->getNomorPenerimaanMutasiGlobal($bc27['divisi_tujuan_id']);
        $id = $this->penerimaanMutasiGlobalModel->insert([
            'company_penerima_id' => $bc27['company_tujuan_id'],
            'company_pengirim_id' => $this->this_company_id,
            'divisi_penerima_id' => $bc27['divisi_tujuan_id'],
            'warehouse_penerima_id' => $bc27['warehouse_tujuan_id'],
            'penerimaan_mutasi_no' => $no,
            'multiple_mutasi_id' => "[" . $mutasiGlobal['id'] . "]",
            'multiple_no_mutasi' => "[" . $mutasiGlobal['no_mutasi'] . "]",
            'tanggal' => $mutasiGlobal['tanggal'],
            'keterangan' => null,
            'status_posting' => '1',
            'createdBy' => $this->this_user_id
        ]);

        foreach ($barang as $b) {
            $this->penerimaanMutasiGlobalDetailModel->insert([
                'penerimaan_mutasi_global_id' => $id,
                'mutasi_global_id' => $b['mutasi_global_id'],
                'mutasi_global_detail_id' => $b['mutasi_global_detail_id'],
                'stock_mutasi_id' => $b['stock_mutasi_id'],
                'bc_mutasi_id' => $b['bc_mutasi_id'],
                'no_aju_mutasi' => $b['no_aju_mutasi'],
                'stock_dokumen_asal' => $b['stock_dokumen'],
                'qty' => $b['qty']
            ]);
        }

        $this->postingPenerimaanMutasiGlobal($id);
    }

    private function getNomorPenerimaanMutasiGlobal($divisiPenerimaId)
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisi = $this->divisiModel->where('id', $divisiPenerimaId)->first();
        if ($divisi != null) {
            $no = $this->penerimaanMutasiGlobalModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisiPenerimaId);
        } else {
            $no = "-";
        }
        return $no;
    }

    private function postingPenerimaanMutasiGlobal($penerimaanMutasiId)
    {
        $id = $penerimaanMutasiId;
        // Insert To Inventori (-)
        $penerimaanMutasiGlobal = $this->penerimaanMutasiGlobalModel->find($id);
        $penerimaanMutasiGlobalList = $this->penerimaanMutasiGlobalDetailModel->where('penerimaan_mutasi_global_id', $penerimaanMutasiGlobal['id'])->where('deletedAt', null)->findAll();
        // Inventori Stok Minus
        foreach ($penerimaanMutasiGlobalList as $p) {
            $mutasiGlobal = $this->mutasiGlobalModel->where('id', $p['mutasi_global_id'])->first();
            $stockMutasi = $this->stockModel->find($p['stock_mutasi_id']);

            if ($stockMutasi['tipe_barang'] == "kemasan") {
                $barang2Id = $stockMutasi['kemasan_id'];
            } else {
                $barang2Id = $stockMutasi['barang2_id'];
            }

            // INIT STOK NYA (KARENA BARANG NYA BISA AJA TIDAK ADA DI INVENTORI)
            $stok = $this->stockModel->getStokMaster(
                $penerimaanMutasiGlobal['company_penerima_id'],
                $penerimaanMutasiGlobal['warehouse_penerima_id'],
                $penerimaanMutasiGlobal['divisi_penerima_id'],
                $stockMutasi['tipe_barang'],
                $stockMutasi['barang1_id'],
                $barang2Id
            );

            if ($stok == null) {
                $stok = $this->stockModel->insertStok(
                    $penerimaanMutasiGlobal['company_penerima_id'],
                    $penerimaanMutasiGlobal['warehouse_penerima_id'],
                    $penerimaanMutasiGlobal['divisi_penerima_id'],
                    $stockMutasi['tipe_barang'],
                    $stockMutasi['barang1_id'],
                    $barang2Id,
                    0
                );
            }

            // INSERT LEVEL 1
            $stok = $this->stockModel->insertStok(
                $penerimaanMutasiGlobal['company_penerima_id'],
                $penerimaanMutasiGlobal['warehouse_penerima_id'],
                $penerimaanMutasiGlobal['divisi_penerima_id'],
                $stockMutasi['tipe_barang'],
                $stockMutasi['barang1_id'],
                $barang2Id,
                $p['qty']
            );

            // INSERT LEVEL 2
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $p['qty'],
                'In',
                date('Y-m-d'),
                $this->this_user_id,
                "MUTASI",
                $penerimaanMutasiGlobal['penerimaan_mutasi_no'],
                "-",
            );

            // STOK OLD 
            $mutasiGlobalDetail =  $this->mutasiGlobalDetailModel
                ->where('mutasi_global_id', $p['mutasi_global_id'])
                ->where('id', $p['mutasi_global_detail_id'])
                ->first();

            $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
                $mutasiGlobalDetail['stock_id'],
                $mutasiGlobalDetail['bc_id'],
                $mutasiGlobalDetail['no_aju'],
                $mutasiGlobalDetail['stock_dokumen']
            );

            // INSERT LEVEL 3 
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_mutasi_id'],
                $stok,
                $stokDetail,
                $p['qty'],
                $p['no_aju_mutasi'],
                $mutasiGlobal['no_mutasi'],
                $mutasiGlobal['no_mutasi'] . " (" . $stockOldDetail['no_po'] . ") ",
                $stockOldDetail['supplier_id'],
                $stockOldDetail['harga_umum'],
                $stockOldDetail['harga_harian'],
                $stockOldDetail['harga_bulanan'],
                $stockOldDetail['no_po']
            );
        }

        $this->penerimaanMutasiGlobalModel->update($id, ['status_posting' => '1']);
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
    public function viewOutstanding()
    {
        return view('BeaCukai/bc-27/bc27outstanding');
    }

    public function allOutstanding()
    {
        $mutasiGlobalUsed = $this->bc27Model
            ->select('mutasi_global_id')
            ->where('company_asal_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $mutasiGlobalAll = $this->mutasiGlobalModel->where('company_asal_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        $allmutasiGlobalIdArr = [];
        $mutasiGlobalIdUsedArr = [];
        $mutasiGlobalIdNotUsedArr = [];

        foreach ($mutasiGlobalUsed as $m) {
            array_push($mutasiGlobalIdUsedArr, $m['mutasi_global_id']);
        }
        foreach ($mutasiGlobalAll as $i) {
            array_push($allmutasiGlobalIdArr, $i['id']);
        }

        $mutasiGlobalIdNotUsedArr = array_diff($allmutasiGlobalIdArr, $mutasiGlobalIdUsedArr);
        $list = [];

        foreach ($mutasiGlobalIdNotUsedArr as $id) {
            $data = $this->mutasiGlobalModel
                ->select('
                    mutasi_global.id as mutasi_id,
                    no_mutasi,
                    company_tujuan_id,
                    company_asal_id,
                    divisi_asal_id,
                    divisi,
                    warehouse_asal_id,
                    warehouse_name,
                    tanggal')
                ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id')
                ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id')
                ->where('mutasi_global.id', $id)
                ->first();
            $company_asal = $this->companyModel
                ->select('company')
                ->where('id', $data['company_asal_id'])
                ->first();
            $company_tujuan = $this->companyModel
                ->select('company')
                ->where('id', $data['company_tujuan_id'])
                ->first();
            $detailCount = $this->mutasiGlobalDetailModel
                ->select('count(*) as jumlah_barang')
                ->where('mutasi_global_id', $id)
                ->first();
            $detailMutasi = $this->mutasiGlobalDetailModel
                ->where('mutasi_global_id', $id)
                ->findAll();

            $nilaiBarang = 0;
            foreach ($detailMutasi as $dm) {
                $stockListDetail = $this->stockDetail2Model
                    ->getStockListDetail($dm['stock_id'], $dm['bc_id'], $dm['no_aju'], $dm['stock_dokumen']);
                $nilaiBarang = intval($stockListDetail['harga_harian']) + intval($stockListDetail['harga_umum']) + intval($stockListDetail['harga_bulanan']);
            }



            if ($data != null) {
                array_push($list, [
                    'id' => $data['mutasi_id'],
                    'no_mutasi' => $data['no_mutasi'],
                    'company_asal' => $company_asal['company'],
                    'company_tujuan' => $company_tujuan['company'],
                    'divisi' => $data['divisi'],
                    'warehouse_name' => $data['warehouse_name'],
                    'tanggal' => date('d/m/Y', strtotime($data['tanggal'])),
                    'jumlah_barang' => $detailCount['jumlah_barang'],
                    'total_harga' => number_format($nilaiBarang, 2)
                ]);
            }
        }
        return json_encode($list);
    }

    public function OutstandingSheet()
    {
        $list = json_decode($this->allOutstanding());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'No Mutasi')
            ->setCellValue('C1', 'Company Asal')
            ->setCellValue('D1', 'Company Tujuan')
            ->setCellValue('E1', 'Divisi / Warehouse Pengeluaran')
            ->setCellValue('F1', 'Tanggal')
            ->setCellValue('G1', 'Jumlah Barang')
            ->setCellValue('H1', 'Nilai Barang');
        $no = 1;
        $column = 2;

        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->no_mutasi)
                ->setCellValue('C' . $column,  $l->company_asal)
                ->setCellValue('D' . $column,  $l->company_tujuan)
                ->setCellValue('E' . $column,  $l->divisi . " / " . $l->warehouse_name)
                ->setCellValue('F' . $column,  $l->tanggal)
                ->setCellValue('G' . $column,  $l->jumlah_barang)
                ->setCellValue('H' . $column,  $l->total_harga);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC27';
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Outstanding-BC-2.7';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
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
