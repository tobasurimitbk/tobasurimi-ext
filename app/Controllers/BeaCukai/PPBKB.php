<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\CeisaSettingModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\NomorIjinTPBModel;
use App\Models\PengusahaTPBModel;
use App\Models\PPBKBDetailModel;
use App\Models\PPBKBModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    protected $dompdf;

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
        $this->dompdf = new Dompdf();

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
            "type"          => "PPBKB"
        ];

        $condition = [
            "ppbkb.company_id"  => $this->this_company_id,
            "ppbkb.deletedAt" => null,
            "mutasi.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalPPBKB" => $this->request->getGet("mulaiTanggalPPBKB"),
            "selesaiTanggalPPBKB" => $this->request->getGet('selesaiTanggalPPBKB'),
            "noPPBKB" => $this->request->getGet('noPPBKB'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->ppbkbModel->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $divisi = $this->divisiModel->find($data->divisi_tujuan_id);
            $warehouse = $this->warehouseModel->find($data->warehouse_tujuan_id);

            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "divisi_asal_name"      => strtoupper($data->divisi_asal_name),
                "warehouse_asal_name"   => strtoupper($data->warehouse_asal_name),
                "divisi_tujuan_name"    => $divisi == null ? '-' : strtoupper($divisi['divisi']),
                "warehouse_tujuan_name" => $warehouse == null ? '-' : strtoupper($warehouse['warehouse_name']),
                "no_mutasi"             => $data->no_mutasi,
                "no_ppbkb"              => $data->no_ppbkb . " / " . ($data->no_daftar == "" ? "-" : $data->no_daftar),
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
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
            'hsCode' => $this->hsCodeModel->where('deletedAt', null)->findAll(),
            'akunCeisa' => $this->akunCeisa,
            'noPPBKB' => $this->ppbkbModel->getNo($this->this_company_id),
            'pengusahaTPB' => $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
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

        $data = [
            'hsCode' => $this->hsCodeModel->where('deletedAt', null)->findAll(),
            'akunCeisa' => $this->akunCeisa,
            'pengusahaTPB' => $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'ppbkb' => $ppbkb,
            'warehouseTujuan' => $this->warehouseModel->find($ppbkb['warehouse_tujuan_id']),
            'divisiTujuan' => $this->divisiModel->find($ppbkb['divisi_tujuan_id'])
        ];

        return view('BeaCukai/ppbkb/form', $data);
    }

    public function createAction()
    {
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

        $id = $this->ppbkbModel->insert([
            'company_id' => $this->this_company_id,
            'mutasi_id' => $this->request->getVar('mutasi_id'),
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
            'no_daftar' => $this->request->getVar('no_daftar')
        ]);

        foreach (json_decode($_POST['listData']) as $d) {
            $this->ppbkbDetailModel->insert([
                'ppbkb_id' => $id,
                'mutasi_id' => $d->mutasi_id,
                'mutasi_detail_id' => $d->mutasi_detail_id,
                'hs_code_id' => $d->hs_code_id
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen PPBKB Berhasil Disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function updateAction()
    {
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
            'no_daftar' => $this->request->getVar('no_daftar')
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach (json_decode($_POST['listData']) as $d) {
            // CHECK
            $check = $this->ppbkbDetailModel
                ->where('mutasi_id', $d->mutasi_id)
                ->where('mutasi_detail_id', $d->mutasi_detail_id)
                ->where('ppbkb_id', $id)
                ->first();
            if ($check != null) {
                $this->ppbkbDetailModel->update($check['id'], [
                    'ppbkb_id' => $id,
                    'mutasi_id' => $d->mutasi_id,
                    'mutasi_detail_id' => $d->mutasi_detail_id,
                    'hs_code_id' => $d->hs_code_id
                ]);
            } else {
                $this->ppbkbDetailModel
                    ->where('mutasi_id', $d->mutasi_id)
                    ->where('mutasi_detail_id', $d->mutasi_detail_id)
                    ->where('ppbkb_id', $id)
                    ->delete();
                $id_detail_new =  $this->ppbkbDetailModel->insert([
                    'ppbkb_id' => $id,
                    'mutasi_id' => $d->mutasi_id,
                    'mutasi_detail_id' => $d->mutasi_detail_id,
                    'hs_code_id' => $d->hs_code_id
                ]);

                array_push($id_detail_all,  $id_detail_new);
            }
        }

        if (!empty($id_detail_all)) {
            $this->ppbkbDetailModel->where('ppbkb_id', $id)->whereNotIn('id', $id_detail_all)->delete();
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen PPBKB Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->ppbkbModel->delete($id);
        $this->ppbkbDetailModel->where('ppbkb_id', $id)->delete();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen PPBKB Berhasil Dihapus"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        // PPBKB FIRST
        $ppbkb = $this->ppbkbModel->find($id);
        // KURANGI STOK NYA
        $mutasi = $this->mutasiModel->find($ppbkb['mutasi_id']);
        $mutasiList = $this->mutasiDetailModel->where('mutasi_id', $ppbkb['mutasi_id'])->where('deletedAt', null)->findAll();

        foreach ($mutasiList as $m) {
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
                $mutasi['company_id'],
                $mutasi['warehouse_asal_id'],
                $mutasi['divisi_asal_id'],
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
                $mutasi['keterangan'],
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $m['bc_id'],
                $stok,
                $stokDetail,
                $qty,
                $m['no_aju'],
                $mutasi['no_mutasi'],
                $m['stock_dokumen'],
                $stockOldDetail['supplier_id'],
                $stockOldDetail['harga_umum'],
                $stockOldDetail['harga_harian'],
                $stockOldDetail['harga_bulanan'],
                $stockOldDetail['no_po']
            );
        }

        $this->ppbkbModel->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen PPBKB Berhasil Diposting"
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $ppbkb = $this->ppbkbModel->getDetail($id);

        if ($ppbkb == null) {
            return redirect()->to('bea-cukai-ppbkb');
        }
        // TO DO
        $data = [
            'ppbkb' => $ppbkb,
            'detailBarang' => $this->mutasiDetailModel->getMutasiDetail($ppbkb['mutasi_id'])
        ];

        $this->dompdf->loadHtml(view('BeaCukai/ppbkb/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("PP-BKB | " . $ppbkb['no_ppbkb'], array("Attachment" => false));
    }

    public function getListMutasiDetail()
    {
        $mutasiId = $this->request->getVar('mutasi_id');
        $dataResultDetail = $this->mutasiDetailModel->getMutasiDetail($mutasiId);

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
        $mutasiUsed = $this->ppbkbModel
            ->select('mutasi_id')
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $mutasiAll = $this->mutasiModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        $allmutasiIdArr = [];
        $mutasiIdUsedArr = [];
        $mutasiIdNotUsedArr = [];

        foreach ($mutasiUsed as $m) {
            array_push($mutasiIdUsedArr, $m['mutasi_id']);
        }
        foreach ($mutasiAll as $i) {
            array_push($allmutasiIdArr, $i['id']);
        }

        $mutasiIdNotUsedArr = array_diff($allmutasiIdArr, $mutasiIdUsedArr);
        $list = [];
        foreach ($mutasiIdNotUsedArr as $id) {
            $data = $this->mutasiModel
                ->select('
                    mutasi.id as mutasi_id,
                    no_mutasi,
                    divisi_asal_id,
                    divisi_tujuan_id,
                    warehouse_asal_id,
                    warehouse_tujuan_id,
                    tanggal')
                ->where('id', $id)
                ->first();
            $divisiAwal = $this->divisiModel
                ->select('divisi')
                ->where('id', $data['divisi_asal_id'])
                ->first();
            $divisiTujuan = $this->divisiModel
                ->select('divisi')
                ->where('id', $data['divisi_tujuan_id'])
                ->first();
            $warehouseAwal = $this->warehouseModel
                ->select('warehouse_name')
                ->where('id', $data['warehouse_asal_id'])
                ->first();
            $warehouseTujuan = $this->warehouseModel
                ->select('warehouse_name')
                ->where('id', $data['warehouse_tujuan_id'])
                ->first();
            $countDetail = $this->mutasiDetailModel
                ->select('count(*) as jumlah_barang')
                ->where('mutasi_id', $id)
                ->first();
            $detailMutasi = $this->mutasiDetailModel
                ->where('mutasi_id', $id)
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
                    'divisi_awal' => $divisiAwal['divisi'],
                    'warehouse_awal' => $warehouseAwal['warehouse_name'],
                    'divisi_tujuan' => $divisiTujuan['divisi'],
                    'warehouse_tujuan' => $warehouseTujuan['warehouse_name'],
                    'tanggal' => date("d/m/Y", strtotime($data['tanggal'])),
                    'jumlah_barang' => $countDetail['jumlah_barang'],
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
            ->setCellValue('B1', 'No Mutasi ')
            ->setCellValue('C1', 'Divisi / Warehouse Asal ')
            ->setCellValue('D1', 'Divisi / Warehouse Tujuan ')
            ->setCellValue('E1', 'Tanggal')
            ->setCellValue('F1', 'Jumlah Barang')
            ->setCellValue('G1', 'Nilai Barang');

        $no = 1;
        $column = 2;

        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->no_mutasi)
                ->setCellValue('C' . $column,  $l->divisi_awal . " / " . $l->warehouse_awal)
                ->setCellValue('D' . $column,  $l->divisi_tujuan . " / " . $l->warehouse_tujuan)
                ->setCellValue('E' . $column,  $l->tanggal)
                ->setCellValue('F' . $column,  $l->jumlah_barang)
                ->setCellValue('G' . $column,  $l->total_harga);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap PPBKB';
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Outstanding-PPBKB';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
