<?php

namespace App\Controllers\Accounting\TutupBuku;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\SaldoTutupBukuModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockModel;
use App\Models\StockTutupBukuModel;
use App\Models\Sub_AkunsModel;
use App\Models\TutupBukuModel;

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

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = \Config\Services::encrypter();
        $this->divisisModel = new DivisisModel();
        $this->tutupBukuModel = new TutupBukuModel();
        $this->stockTutupBukuModel = new StockTutupBukuModel();
        $this->saldoTutupBukuModel = new SaldoTutupBukuModel();
        $this->stockModel = new StockModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->satuanModel = new SatuansModel();
        $this->subAkunModel = new Sub_AkunsModel();
    }
    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisisModel->getDivisiAccess(),
        ];
        return view('Accounting/tutupBuku/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "tutup_buku.company_id"  => $this->this_company_id,
            "tutup_buku.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->tutupBukuModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $stockTutupBukuVar = 0;
        $saldoTutupBukuVar = "-";


        foreach ($res['data'] as $data) {
            $stockTutupBuku = $this->stockTutupBukuModel->where('deletedAt', null)->where('tutup_buku_id', $data->id)->first();
            $saldoTutupBuku = $this->saldoTutupBukuModel->where('deletedAt', null)->where('tutup_buku_id', $data->id)->first();

            if ($stockTutupBuku) {
                $stockTutupBukuVar = 1;
            } else {
                $stockTutupBukuVar = 0;
            }

            if ($saldoTutupBuku) {
                $saldoTutupBukuVar = 1;
            } else {
                $saldoTutupBukuVar = 0;
            }

            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "divisi"                 => $data->divisi,
                "bulan"                 => $data->bulan,
                "stock"                 => $stockTutupBukuVar,
                "saldo"                 => $saldoTutupBukuVar,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }

    public function get()
    {
        $id = $this->request->getVar('id');
        $res = $this->AccountSupplierModel->where('id', $id)->first();
        // var_dump($this->request->getVar('id'));

        return response()->setJSON([
            'data' => $res,
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function save()
    {
        $addCondition = [
            "sort"   => $this->request->getVar("sort") ?? "createdAt",
            "sortType"  => $this->request->getVar("sortType") ?? "desc",
            "search" => $this->request->getVar("search") ?? "",
            "parent_type" => $this->request->getVar("parent_type") ?? "",
            "parent_name" => $this->request->getVar("parent_name") ?? "",
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar("warehouse_id") ?? "",
            "status_stok" => $this->request->getVar("status_stok") ?? "ALL",
            "kode" =>  $this->request->getVar("search") ?? "",
            "kode_barang" =>  $this->request->getVar("search") ?? "",
        ];
        $conditionStock = [
            "barang_master.company_id" => $this->this_company_id,
            "barang_master.deletedAt" => null,
            "barang_master_spesifikasi.deletedAt" => null,
            "stock.company_id" => $this->this_company_id,
            "stock.deletedAt" => null,
        ];
        $bulan_closing_input = $this->request->getVar("bulan_closing");

        // Konversi format MM/YYYY ke format YYYY-MM
        list($month, $year) = explode('/', $bulan_closing_input);
        $bulan_closing = "$year-$month";

        $conditionSaldo = [
            "sub_akuns.company_id" => $this->this_company_id,
            "sub_akuns.deletedAt" => null,
            "jurnal_umum.company_id" => $this->this_company_id,
            "jurnal_umum.deletedAt" => null,
            "DATE_FORMAT(jurnal_umum.tanggal_jurnal, '%Y-%m') =" => $bulan_closing,
        ];

        $dataQrySaldo = $this->subAkunModel->getSubsAkunWithDataJurnal($conditionSaldo);
        $dataQry = $this->stockModel->getStockListBarang($conditionStock, $addCondition, 0, 0);
        // var_dump($conditionStock);
        // var_dump($addCondition);
        // var_dump($dataQry['data']);
        // exit;
        $idTutupBuku = $this->tutupBukuModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'bulan' => $this->request->getVar('bulan_closing')
        ]);

        foreach ($dataQry['data'] as &$data) {
            $satuan1 = $this->satuanModel->find($data->satuan_1);
            $satuan2 = $this->satuanModel->find($data->satuan_2);
            $satuan3 = $this->satuanModel->find($data->satuan_3);
            $data->qty = $this->stockModel->detailStock($data->id)['stok']['stokSekarang'];
            $avgHarga = $this->stockDetail2Model->getAverageHargaStockList($data->id);
            // var_dump($avgHarga);
            $this->stockTutupBukuModel->insert([
                'tutup_buku_id' => $idTutupBuku,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'stock_id' => $data->id,
                'barang1_id' => $data->barang1_id,
                'barang2_id' => $data->barang2_id,
                'avg_harga_umum' => $avgHarga['avg_harga_umum'] ?? 0,
                'avg_harga_harian' => $avgHarga['avg_harga_harian'] ?? 0,
                'avg_harga_bulanan' => $avgHarga['avg_harga_bulanan'] ?? 0,
                'qty' => $data->qty
            ]);
        }
        // exit;

        foreach ($dataQrySaldo as &$data) {
            $this->saldoTutupBukuModel->insert([
                'tutup_buku_id' => $idTutupBuku,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'coa_id' => $data->id_coa,
                'saldo' => $data->saldo_akhir,
            ]);
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Tutup Buku Berhasil Ditambahkan"
        ]);
    }

    public function updateAccountSupplier()
    {
        $id = $this->request->getVar('id');

        $this->AccountSupplierModel->update($id, [
            'supplier_id' => $this->request->getVar('supplier_id'),
            'ap_id' => $this->request->getVar('akun_ap_id'),
            'ar_id' => $this->request->getVar('akun_ar_id')
        ]);

        return response()->setJSON([
            'token' => \csrf_hash(),
            'status' => true,
            'message' => "Account Supplier Berhasil Diupdate"
        ]);
    }

    public function deleteAccountSupplier()
    {
        $id = $this->request->getVar('id');

        $this->AccountSupplierModel->update($id, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Account Supplier Berhasil Dihapus"
        ]);
    }
}
