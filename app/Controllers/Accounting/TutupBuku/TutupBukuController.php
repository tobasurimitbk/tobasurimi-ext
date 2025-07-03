<?php

namespace App\Controllers\Accounting\TutupBuku;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JurnalUmumModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SaldoTutupBukuModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockModel;
use App\Models\StockTutupBukuModel;
use App\Models\Sub_AkunsModel;
use App\Models\TutupBukuModel;
use App\Models\TransaksiJurnalModel;
use Exception;

class TutupBukuController extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $divisisModel;
    protected $tutupBukuModel;
    protected $stockTutupBukuModel;
    protected $saldoTutupBukuModel;
    protected $stockModel;
    protected $satuanModel;
    protected $subAkunModel;
    protected $encrypter;
    protected $stockDetail2Model;

    protected $transaksiJurnalModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $jurnalumumcontroller;
    protected $jurnalUmumModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = \Config\Services::encrypter();
        // $this->divisisModel = new DivisisModel();
        // $this->tutupBukuModel = new TutupBukuModel();
        // $this->stockTutupBukuModel = new StockTutupBukuModel();
        // $this->saldoTutupBukuModel = new SaldoTutupBukuModel();
        // $this->stockModel = new StockModel();
        // $this->stockDetail2Model = new StockDetail2Model();
        // $this->satuanModel = new SatuansModel();
        // $this->subAkunModel = new Sub_AkunsModel();

        $this->transaksiJurnalModel = new TransaksiJurnalModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->jurnalumumcontroller = new JurnalUmum();
        $this->jurnalUmumModel = new JurnalUmumModel();
    }
    // public function index()
    // {
    //     $data = [
    //         'dataDivisi' => $this->divisisModel->getDivisiAccess(),
    //     ];
    //     return view('Accounting/tutupBuku/index', $data);
    // }

    // public function all()
    // {
    //     $payload = [
    //         "pageSize" => $this->request->getGet("length"),
    //         "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
    //         "search" => $this->request->getGet("search"),
    //         "sort" => $this->request->getGet("sort"),
    //         "sortType" => $this->request->getGet("sortType"),
    //     ];

    //     $condition = [
    //         "tutup_buku.company_id"  => $this->this_company_id,
    //         "tutup_buku.deletedAt" => NULL
    //     ];

    //     $addCondition = [
    //         "search"        => $this->request->getGet("search"),
    //         "sort"          => $this->request->getGet("sort"),
    //         "sortType"      => $this->request->getGet("sortType")
    //     ];

    //     $limit = $this->request->getGet("length");
    //     $offset = $this->request->getGet("start");

    //     $res = $this->tutupBukuModel->getList($condition, $addCondition, $limit, $offset);

    //     $rdata = [];

    //     $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
    //     $stockTutupBukuVar = 0;
    //     $saldoTutupBukuVar = "-";


    //     foreach ($res['data'] as $data) {
    //         $stockTutupBuku = $this->stockTutupBukuModel->where('deletedAt', null)->where('tutup_buku_id', $data->id)->first();
    //         $saldoTutupBuku = $this->saldoTutupBukuModel->where('deletedAt', null)->where('tutup_buku_id', $data->id)->first();

    //         if ($stockTutupBuku) {
    //             $stockTutupBukuVar = 1;
    //         } else {
    //             $stockTutupBukuVar = 0;
    //         }

    //         if ($saldoTutupBuku) {
    //             $saldoTutupBukuVar = 1;
    //         } else {
    //             $saldoTutupBukuVar = 0;
    //         }

    //         array_push($rdata, [
    //             "no"                    => $no++,
    //             "id"                    => encrypt($data->id),
    //             "divisi"                 => $data->divisi,
    //             "bulan"                 => $data->bulan,
    //             "stock"                 => $stockTutupBukuVar,
    //             "saldo"                 => $saldoTutupBukuVar,
    //         ]);
    //     }

    //     $data = [
    //         "draw"              => intval($this->request->getGet("draw")),
    //         "recordsTotal"      => $res['totalData'],
    //         "recordsFiltered"   => $res['totalFilteredData'],
    //         "data"              => $rdata,
    //         "payload"           => $payload,
    //     ];

    //     return response()->setJSON($data);
    // }

    // public function get()
    // {
    //     $id = $this->request->getVar('id');
    //     $res = $this->AccountSupplierModel->where('id', $id)->first();
    //     // var_dump($this->request->getVar('id'));

    //     return response()->setJSON([
    //         'data' => $res,
    //         'token' => csrf_hash(),
    //         'status' => true,
    //     ]);
    // }

    // public function save()
    // {
    //     $addCondition = [
    //         "sort"   => $this->request->getVar("sort") ?? "createdAt",
    //         "sortType"  => $this->request->getVar("sortType") ?? "desc",
    //         "search" => $this->request->getVar("search") ?? "",
    //         "parent_type" => $this->request->getVar("parent_type") ?? "",
    //         "parent_name" => $this->request->getVar("parent_name") ?? "",
    //         "divisi_id" => $this->request->getVar("divisi_id"),
    //         "warehouse_id" => $this->request->getVar("warehouse_id") ?? "",
    //         "status_stok" => $this->request->getVar("status_stok") ?? "ALL",
    //         "kode" =>  $this->request->getVar("search") ?? "",
    //         "kode_barang" =>  $this->request->getVar("search") ?? "",
    //     ];
    //     $conditionStock = [
    //         "barang_master.company_id" => $this->this_company_id,
    //         "barang_master.deletedAt" => null,
    //         "barang_master_spesifikasi.deletedAt" => null,
    //         "stock.company_id" => $this->this_company_id,
    //         "stock.deletedAt" => null,
    //     ];
    //     $bulan_closing_input = $this->request->getVar("bulan_closing");

    //     // Konversi format MM/YYYY ke format YYYY-MM
    //     list($month, $year) = explode('/', $bulan_closing_input);
    //     $bulan_closing = "$year-$month";

    //     $conditionSaldo = [
    //         "sub_akuns.company_id" => $this->this_company_id,
    //         "sub_akuns.deletedAt" => null,
    //         "jurnal_umum.company_id" => $this->this_company_id,
    //         "jurnal_umum.deletedAt" => null,
    //         "DATE_FORMAT(jurnal_umum.tanggal_jurnal, '%Y-%m') =" => $bulan_closing,
    //     ];

    //     $dataQrySaldo = $this->subAkunModel->getSubsAkunWithDataJurnal($conditionSaldo);
    //     $dataQry = $this->stockModel->getStockListBarang($conditionStock, $addCondition, 0, 0);
    //     // var_dump($conditionStock);
    //     // var_dump($addCondition);
    //     // var_dump($dataQry['data']);
    //     // exit;
    //     $idTutupBuku = $this->tutupBukuModel->insert([
    //         'company_id' => $this->this_company_id,
    //         'divisi_id' => $this->request->getVar('divisi_id'),
    //         'bulan' => $this->request->getVar('bulan_closing')
    //     ]);

    //     foreach ($dataQry['data'] as &$data) {
    //         $satuan1 = $this->satuanModel->find($data->satuan_1);
    //         $satuan2 = $this->satuanModel->find($data->satuan_2);
    //         $satuan3 = $this->satuanModel->find($data->satuan_3);
    //         $data->qty = $this->stockModel->detailStock($data->id)['stok']['stokSekarang'];
    //         $avgHarga = $this->stockDetail2Model->getAverageHargaStockList($data->id);
    //         // var_dump($avgHarga);
    //         $this->stockTutupBukuModel->insert([
    //             'tutup_buku_id' => $idTutupBuku,
    //             'divisi_id' => $this->request->getVar('divisi_id'),
    //             'stock_id' => $data->id,
    //             'barang1_id' => $data->barang1_id,
    //             'barang2_id' => $data->barang2_id,
    //             'avg_harga_umum' => $avgHarga['avg_harga_umum'] ?? 0,
    //             'avg_harga_harian' => $avgHarga['avg_harga_harian'] ?? 0,
    //             'avg_harga_bulanan' => $avgHarga['avg_harga_bulanan'] ?? 0,
    //             'qty' => $data->qty
    //         ]);
    //     }
    //     // exit;

    //     foreach ($dataQrySaldo as &$data) {
    //         $this->saldoTutupBukuModel->insert([
    //             'tutup_buku_id' => $idTutupBuku,
    //             'divisi_id' => $this->request->getVar('divisi_id'),
    //             'coa_id' => $data->id_coa,
    //             'saldo' => $data->saldo_akhir,
    //         ]);
    //     }

    //     return response()->setJSON([
    //         'token' => csrf_hash(),
    //         'status' => true,
    //         'message' => "Tutup Buku Berhasil Ditambahkan"
    //     ]);
    // }

    public function deleteTransaksiJurnalLama()
    {
        $companyId = 16;
        $typeTransaksi = 1406;
        $tanggalAwalTransaksi = "2025-06-01";
        $tanggalAkhirTransaksi = "2025-06-31";
        $kategoriBarang = null;

        $db = \Config\Database::connect();
        $db->transBegin();

        $dataTransaksi = $this->transaksiJurnalModel
            ->select('transaksi_jurnal.id, transaksi_jurnal.penerimaan_barang_id')
            ->join('jurnal_umum', 'transaksi_jurnal.id = jurnal_umum.id_transaksi', 'left')
            ->where('DATE(tanggal_transaksi) >=', $tanggalAwalTransaksi)
            ->where('DATE(tanggal_transaksi) <=', $tanggalAkhirTransaksi)
            ->where('kategori_barang', $kategoriBarang)
            ->where('type_transaksi', $typeTransaksi)
            ->where('jurnal_umum.company_id', $companyId)
            ->where('transaksi_jurnal.deleted_at', null)
            ->groupBy('transaksi_jurnal.id')
            ->get();

        // dd($dataTransaksi->getResult());

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($dataTransaksi->getResult() as $transaksi) {
            $idTransaksi = $transaksi->id;

            $deleted = $this->jurnalUmumModel
                ->where('id_transaksi', $idTransaksi)
                ->where('company_id', $companyId)
                ->delete();

            if ($deleted) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Gagal hapus jurnal dengan id_transaksi: $idTransaksi";
            }
        }

        if ($errorCount > 0) {
            $db->transRollback();
            return json_encode([
                'status' => false,
                'message' => "Rollback karena ada error. Berhasil: $successCount, Gagal: $errorCount",
                'errors' => $errors,
                'token' => csrf_hash()
            ]);
        } else {
            $db->transCommit();
            return json_encode([
                'status' => true,
                'message' => "Hapus selesai. Berhasil: $successCount, Gagal: $errorCount",
                'token' => csrf_hash()
            ]);
        }
    }

    public function updateTransaksiJurnalBahanBaku()
    {
        $companyId = 16;
        $typeTransaksi = 1406;
        $tanggalAwalTransaksi = "2025-06-01";
        $tanggalAkhirTransaksi = "2025-06-31";
        $kategoriBarang = null;

        $dataTransaksi = $this->transaksiJurnalModel
            ->select('transaksi_jurnal.id, transaksi_jurnal.penerimaan_barang_id')
            ->join('jurnal_umum', 'transaksi_jurnal.id = jurnal_umum.id_transaksi', 'left')
            ->where('DATE(tanggal_transaksi) >=', $tanggalAwalTransaksi)
            ->where('DATE(tanggal_transaksi) <=', $tanggalAkhirTransaksi)
            ->where('kategori_barang', $kategoriBarang)
            ->where('type_transaksi', $typeTransaksi)
            ->where('jurnal_umum.company_id', $companyId)
            ->where('transaksi_jurnal.deleted_at', null)
            ->groupBy('transaksi_jurnal.id')
            ->get();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($dataTransaksi->getResult() as $transaksi) {
            $idTransaksi = $transaksi->id;
            $penerimaanBarangId = $transaksi->penerimaan_barang_id;

            $penerimaanBarangData = $this->penerimaanBarangModel->find($penerimaanBarangId);
            if (!$penerimaanBarangData) {
                $errorCount++;
                $errors[] = "ID $idTransaksi: Data penerimaan barang tidak ditemukan.";
                continue;
            }

            $multiplePO = json_decode($penerimaanBarangData['multiple_po_id'], true);
            $poID = $multiplePO[0] ?? null;

            if (!$poID) {
                $errorCount++;
                $errors[] = "ID $idTransaksi: multiple_po_id kosong atau tidak valid.";
                continue;
            }

            try {
                $this->jurnalumumcontroller->insertDataPembelianBB(
                    $poID,
                    "BAHAN " . $penerimaanBarangData['tipe_bahan'],
                    $penerimaanBarangData['status_penerimaan'],
                    "pembelian",
                    $idTransaksi,
                    $companyId
                );
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "ID $idTransaksi: " . $e->getMessage();
            }
        }

        return $this->response->setJSON([
            "status" => $errorCount === 0,
            "message" => "Update selesai. Berhasil: $successCount, Gagal: $errorCount",
            "errors" => $errors,
            "token" => csrf_hash()
        ]);
    }
}
