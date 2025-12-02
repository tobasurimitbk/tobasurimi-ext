<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\CeisaSettingModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\MetadataModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\NomorIjinTPBModel;
use App\Models\PenerimaanMutasiDetailModel;
use App\Models\PenerimaanMutasiModel;
use App\Models\PengusahaTPBModel;
use App\Models\PPBKBDetailModel;
use App\Models\PPBKBModel;
use App\Models\PPBKBMutasiModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PPBKB extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $divisiModel;
    protected $ppbkbModel;
    protected $ppbkbDetailModel;
    protected $mutasiModel;
    protected $pengusahaTPBModel;
    protected $noIjinTPBModel;
    protected $mutasiDetailModel;
    protected $hsCodeModel;
    protected $warehouseModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $penerimaanMutasiModel;
    protected $penerimaanMutasiDetailModel;
    protected $metaDataModel;
    protected $dompdf;
    protected $ppbkbMutasiModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->divisiModel = new DivisisModel();
        $this->ppbkbModel = new PPBKBModel();
        $this->ppbkbDetailModel = new PPBKBDetailModel();
        $this->mutasiModel = new MutasiModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->noIjinTPBModel = new NomorIjinTPBModel();
        $this->mutasiDetailModel = new MutasiDetailModel();
        $this->hsCodeModel = new HsCodesModel();
        $this->warehouseModel = new WarehousesModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->penerimaanMutasiModel = new PenerimaanMutasiModel();
        $this->penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $this->metaDataModel = new MetadataModel();
        $this->dompdf = new Dompdf();
        $this->ppbkbMutasiModel = new PPBKBMutasiModel();
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first()
        ];

        return view('BeaCukai/ppbkb/index', $data);
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
        ];

        $condition = [
            "ppbkb.company_id"  => $this->this_company_id,
            "ppbkb.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalPPBKB" =>  $this->request->getVar("mulaiTanggalPPBKB") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("mulaiTanggalPPBKB")))) : "",
            "selesaiTanggalPPBKB" => $this->request->getVar("selesaiTanggalPPBKB") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("selesaiTanggalPPBKB")))) : "",
            "search" => $this->request->getGet('search'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->ppbkbModel->getList(
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
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "multiple_mutasi_no"    => str_replace(['"', ']', '['], " ",  $data->multiple_mutasi_no),
                "no_ppbkb"              => $data->no_ppbkb,
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
        $hsCode = $this->hsCodeModel->where('deletedAt', null)->findAll();
        $noPpbkb = $this->ppbkbModel->getNo($this->this_company_id);
        $akunCeisa = $this->akunCeisa;
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        $mutasi = $this->ppbkbModel->getdropdownMutasi($this->this_company_id);

        $data = [
            'hsCode' => $hsCode,
            'akunCeisa' => $akunCeisa,
            'noPPBKB' => $noPpbkb,
            'pengusahaTPB' => $pengusahaTPB,
            'mutasi' => $mutasi
        ];

        return view('BeaCukai/ppbkb/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $ppbkb = $this->ppbkbModel->getDetail($id);

        if ($ppbkb == null) {
            return redirect()->to('bea-cukai-ppbkb');
        }

        $hsCode = $this->hsCodeModel->where('deletedAt', null)->findAll();
        $akunCeisa = $this->akunCeisa;
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        $mutasi = $this->mutasiModel->whereIn('id', json_decode($ppbkb['multiple_mutasi_id']))->where('deletedAt', null)->findAll();

        $data = [
            'hsCode' => $hsCode,
            'akunCeisa' => $akunCeisa,
            'pengusahaTPB' => $pengusahaTPB,
            'ppbkb' => $ppbkb,
            'mutasi' => $mutasi
        ];

        return view('BeaCukai/ppbkb/form', $data);
    }

    public function createAction()
    {
        // return response()->setJSON([
        //     '$_POST' => $_POST,
        //     'status' => false,
        //     'listData' => json_decode($_POST['listData'])
        // ]);
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $multipleMutasiIdArr = $this->request->getVar('multiple_mutasi_id');

            if (empty($multipleMutasiIdArr) || count($multipleMutasiIdArr) == 0) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Pilih nomor mutasi terlebih dahulu"
                ]);
            }

            $first = $this->ppbkbModel
                ->where('company_id', $this->this_company_id)
                ->where('no_ppbkb', $this->request->getVar('no_ppbkb'))
                ->first();

            if ($first != null) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "No PPBKB Sudah Ada"
                ]);
            }

            $multipleMutasiNo = $this->mutasiModel->getNoMutasi(
                $multipleMutasiIdArr
            );
            $multipleMutasiId = "[" . implode(",", $multipleMutasiIdArr) . "]";

            $id = $this->ppbkbModel->insert([
                'company_id' => $this->this_company_id,
                'multiple_mutasi_id' => $multipleMutasiId,
                'multiple_mutasi_no' => $multipleMutasiNo,
                'no_ppbkb' => $this->request->getVar('no_ppbkb'),
                'npwp' => $this->request->getVar('npwp'),
                'nama_perusahaan' => $this->request->getVar('nama_perusahaan'),
                'no_ijin_tpb' => $this->request->getVar('no_ijin_tpb'),
                'lokasi_asal_barang' => $this->request->getVar('lokasi_asal_barang'),
                'lokasi_tujuan_barang' => $this->request->getVar('lokasi_tujuan_barang'),
                'tempat' => $this->request->getVar('tempat'),
                'tanggal' => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
                'nama' => $this->request->getVar('nama'),
                'jabatan' => $this->request->getVar('jabatan'),
                'status_posting' => '0',
                'no_daftar' => $this->request->getVar('no_daftar'),
                'penerimaan_otomatis' => $this->request->getVar('penerimaan_otomatis'),
            ]);

            foreach ($multipleMutasiIdArr as $m) {
                $this->ppbkbMutasiModel->insert([
                    'mutasi_id' => $m,
                    'ppbkb_id' => $id
                ]);
            }

            foreach (json_decode($_POST['listData']) as $d) {
                $this->ppbkbDetailModel->insert([
                    'ppbkb_id' => $id,
                    'mutasi_id' => $d->mutasi->mutasi_id,
                    'mutasi_detail_id' => $d->mutasi->mutasi_detail_id,
                    'hs_code_id' => $d->hs_code_id
                ]);
            }
            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'message' => "Dokumen PPBKB Berhasil Disimpan",
                'token' => csrf_hash()
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

    public function updateAction()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $id = decrypt($this->request->getVar('id'));
            $this->ppbkbModel->update($id, [
                'npwp' => $this->request->getVar('npwp'),
                'nama_perusahaan' => $this->request->getVar('nama_perusahaan'),
                'no_ijin_tpb' => $this->request->getVar('no_ijin_tpb'),
                'lokasi_asal_barang' => $this->request->getVar('lokasi_asal_barang'),
                'lokasi_tujuan_barang' => $this->request->getVar('lokasi_tujuan_barang'),
                'tempat' => $this->request->getVar('tempat'),
                'tanggal' => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
                'nama' => $this->request->getVar('nama'),
                'jabatan' => $this->request->getVar('jabatan'),
                'no_daftar' => $this->request->getVar('no_daftar'),
                'penerimaan_otomatis' => $this->request->getVar('penerimaan_otomatis'),
            ]);

            // get all id detail
            $id_detail_all = [];

            foreach (json_decode($_POST['listData']) as $d) {
                // CHECK
                $check = $this->ppbkbDetailModel
                    ->where('mutasi_id', $d->mutasi->mutasi_id)
                    ->where('mutasi_detail_id', $d->mutasi->mutasi_detail_id)
                    ->where('ppbkb_id', $id)
                    ->first();

                if ($check != null) {
                    $this->ppbkbDetailModel->update($check['id'], [
                        'ppbkb_id' => $id,
                        'mutasi_id' => $d->mutasi->mutasi_id,
                        'mutasi_detail_id' => $d->mutasi->mutasi_detail_id,
                        'hs_code_id' => $d->hs_code_id
                    ]);
                } else {
                    $this->ppbkbDetailModel
                        ->where('mutasi_id', $d->mutasi->mutasi_id)
                        ->where('mutasi_detail_id', $d->mutasi->mutasi_detail_id)
                        ->where('ppbkb_id', $id)
                        ->delete();

                    $id_detail_new =  $this->ppbkbDetailModel->insert([
                        'ppbkb_id' => $id,
                        'mutasi_id' => $d->mutasi->mutasi_id,
                        'mutasi_detail_id' => $d->mutasi->mutasi_detail_id,
                        'hs_code_id' => $d->hs_code_id
                    ]);

                    array_push($id_detail_all,  $id_detail_new);
                }
            }

            if (!empty($id_detail_all)) {
                $this->ppbkbDetailModel->where('ppbkb_id', $id)->whereNotIn('id', $id_detail_all)->delete();
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'message' => "Dokumen PPBKB Berhasil Diupdate",
                'token' => csrf_hash()
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

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->ppbkbModel->delete($id);
        $this->ppbkbDetailModel->where('ppbkb_id', $id)->delete();
        $this->ppbkbMutasiModel->where('ppbkb_id', $id)->delete(null, true);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen PPBKB Berhasil Dihapus"
        ]);
    }

    public function posting()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            $id = decrypt($this->request->getVar('id'));
            $ppbkb = $this->ppbkbModel->where('id', $id)->first();

            $this->ppbkbModel->update($id, ['status_posting' => '1']);
            if ($ppbkb['penerimaan_otomatis']) {
                // OTOMATIS
                $this->terimaOtomatis(
                    $id,
                    $db
                );
            }

            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_token(),
                'status' => true,
                'message' => "Dokumen PPBKB Berhasil Diposting"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    private function terimaOtomatis($ppbkbId, $db)
    {
        $ppbkb = $this->ppbkbModel->where('id', $ppbkbId)->first();
        foreach (json_decode($ppbkb['multiple_mutasi_id']) as $m) {
            $mutasi = $this->mutasiModel->where('id', $m)->where('deletedAt', null)->first();
            $mutasiDetail = $this->mutasiDetailModel->where('mutasi_id', $m)->where('deletedAt', null)->findAll();

            $penerimaanMutasiNo = $this->get_no_str(
                $ppbkb['tanggal'],
                "PPBKB"
            );

            $id = $this->penerimaanMutasiModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $mutasi['divisi_tujuan_id'],
                'tipe_mutasi' => "PPBKB",
                'penerimaan_mutasi_no' => $penerimaanMutasiNo,
                'multiple_mutasi_id' => "[$mutasi[id]]",
                'multiple_no_mutasi' => '[' . $mutasi['no_mutasi'] . ']',
                'tanggal' =>  $ppbkb['tanggal'],
                'keterangan' => null,
                'status_posting' => '1',
                'createdBy' => $this->this_user_id,
                'ppbkb_id' => $ppbkb['id']
            ]);

            foreach ($mutasiDetail as $m) {
                $this->penerimaanMutasiDetailModel->insert([
                    'penerimaan_mutasi_id' => $id,
                    'mutasi_id' => $m['mutasi_id'],
                    'mutasi_detail_id' => $m['id'],
                    'stock_detail_id' => null,
                    'qty' => $m['qty_konversi']
                ]);
            }

            $this->penerimaanMutasiModel->posting($id, $db);
        }

        return true;
        // INSERT KEDALAM STOK TODO
    }

    private function get_no_str($tanggal, $tipe_mutasi)
    {
        $tanggalParts = explode('-', $tanggal);
        $month = $tanggalParts[1];
        $year = $tanggalParts[0];

        $no = $this->penerimaanMutasiModel->get_no(
            $month,
            $year,
            $this->this_company_id,
            $tipe_mutasi
        );

        return $no;
    }

    public function print($id)
    {
        $id = decrypt($id);
        $ppbkb = $this->ppbkbModel->getDetail($id);

        if ($ppbkb == null) {
            return redirect()->to('bea-cukai-ppbkb');
        }

        $detailBarang = $this->mutasiDetailModel->getDetail(
            json_decode($ppbkb['multiple_mutasi_id'])
        );

        $data = [
            'ppbkb' => $ppbkb,
            'detailBarang' => $detailBarang
        ];

        $this->dompdf->loadHtml(view('BeaCukai/ppbkb/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("PP-BKB | " . $ppbkb['no_ppbkb'], array("Attachment" => false));
    }

    public function getListMutasiDetail()
    {
        $mutasiId = $this->request->getVar('multiple_mutasi_id');

        if (empty($mutasiId)) {
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => [],
            ]);
        }

        $mutasiIdArr = json_decode($mutasiId);
        $dataResultDetail = $this->mutasiDetailModel->getDetail(
            $mutasiIdArr
        );

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'data' => $dataResultDetail,
        ]);
    }

    public function dropdownMutasi()
    {
        $divisiAsalId = $this->request->getVar('divisi_asal_id');
        $result = $this->mutasiModel->getMutasiList($divisiAsalId);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'data' => $result
        ]);
    }

    public function dropdownNoIjinTPB()
    {
        $pengusahaTPBId = $this->request->getVar('id');
        $result = $this->noIjinTPBModel->where('company_id', $this->this_company_id)
            ->where('status', '1')
            ->where('pengusaha_tpb_id', $pengusahaTPBId)
            ->where('deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'status' => true,
            'data' => $result,
            'token' => csrf_hash()
        ]);
    }

    public function getNo()
    {
        return response()->setJSON([
            'data' => $this->ppbkbModel->getNo($this->this_company_id),
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
    public function viewOutstanding()
    {
        return view('BeaCukai/ppbkb/ppbkboutstanding');
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
            "mutasi.company_id"  => $this->this_company_id,
            "mutasi.tipe_mutasi" => "PPBKB",
            "mutasi_detail.deletedAt" => null,
            "mutasi.status_posting" => 1
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "search" => $this->request->getGet('search'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->ppbkbModel->getListOutstanding(
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
                "no_mutasi"    => $data->no_mutasi,
                "divisi_asal"    => $data->divisi_asal,
                "warehouse_asal"    => $data->warehouse_asal,
                "divisi_tujuan"    => $data->divisi_tujuan,
                "warehouse_tujuan"    => $data->warehouse_tujuan,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "kode_barang"      => $data->kode_barang,
                "barang_name"   => $data->barang_name,
                "spesifikasi" => $data->spesifikasi,
                "qty_konversi"        => $data->qty_konversi,
                "kode_satuan"              => $data->kode_satuan,
                "type_bc" => $data->type_bc,
                "no_aju"        => $data->no_aju,
                "no_daftar" => $data->no_daftar,
                "hs_code_id" => $data->hs_code_id
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
            "mutasi.company_id"  => $this->this_company_id,
            "mutasi.tipe_mutasi" => "PPBKB",
            "mutasi_detail.deletedAt" => null,
            "mutasi.status_posting" => 1
        ];

        $addCondition = [
            "sort" => "mutasi.tanggal",
            "sortType" => "desc",
            "search" => ""
        ];

        $beaCukaiData = $this->ppbkbModel->getListOutstanding(
            $condition,
            $addCondition,
            100000000,
            0
        );

        $dataBeaCukai = [];
        $no = 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_mutasi" => $data->no_mutasi,
                "divisi_asal" => $data->divisi_asal,
                "warehouse_asal" => $data->warehouse_asal,
                "divisi_tujuan" => $data->divisi_tujuan,
                "warehouse_tujuan" => $data->warehouse_tujuan,
                "tanggal" => date('d/m/Y', strtotime($data->tanggal)),
                "kode_barang" => $data->kode_barang,
                "barang_name" => $data->barang_name,
                "spesifikasi" => $data->spesifikasi,
                "qty_konversi" => $data->qty_konversi,
                "kode_satuan" => $data->kode_satuan,
                "type_bc" => $data->type_bc,
                "no_aju" => $data->no_aju,
                "no_daftar" => $data->no_daftar
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1️⃣ Set header
        $headers = [
            "No",
            "No Mutasi",
            "Dept Asal",
            "Warehouse Asal",
            "Dept Tujuan",
            "Warehouse Tujuan",
            "Tgl Mutasi",
            "Kode Barang",
            "Barang",
            "Spesifikasi",
            "Qty Mutasi",
            "Satuan",
            "Doc Masuk",
            "No Aju",
            "No Daftar"
        ];

        $columnIndex = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $header);
            $columnIndex++;
        }

        // 2️⃣ Isi data
        $rowIndex = 2; // mulai dari baris 2
        foreach ($dataBeaCukai as $row) {
            $sheet->setCellValueByColumnAndRow(1, $rowIndex, $row['no']);
            $sheet->setCellValueByColumnAndRow(2, $rowIndex, $row['no_mutasi']);
            $sheet->setCellValueByColumnAndRow(3, $rowIndex, $row['divisi_asal']);
            $sheet->setCellValueByColumnAndRow(4, $rowIndex, $row['warehouse_asal']);
            $sheet->setCellValueByColumnAndRow(5, $rowIndex, $row['divisi_tujuan']);
            $sheet->setCellValueByColumnAndRow(6, $rowIndex, $row['warehouse_tujuan']);
            $sheet->setCellValueByColumnAndRow(7, $rowIndex, $row['tanggal']);
            $sheet->setCellValueByColumnAndRow(8, $rowIndex, $row['kode_barang']);
            $sheet->setCellValueByColumnAndRow(9, $rowIndex, $row['barang_name']);
            $sheet->setCellValueByColumnAndRow(10, $rowIndex, $row['spesifikasi']);
            $sheet->setCellValueByColumnAndRow(11, $rowIndex, $row['qty_konversi']);
            $sheet->setCellValueByColumnAndRow(12, $rowIndex, $row['kode_satuan']);
            $sheet->setCellValueByColumnAndRow(13, $rowIndex, $row['type_bc']);
            $sheet->setCellValueByColumnAndRow(14, $rowIndex, $row['no_aju']);
            $sheet->setCellValueByColumnAndRow(15, $rowIndex, $row['no_daftar']);
            $rowIndex++;
        }

        // 3️⃣ Auto size kolom
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // 4️⃣ Border semua data
        $lastRow = $rowIndex - 1;
        $sheet->getStyle("A1:O{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 5️⃣ Export
        $writer = new Xlsx($spreadsheet);
        $fileName = "Outstanding_PPBKB_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
