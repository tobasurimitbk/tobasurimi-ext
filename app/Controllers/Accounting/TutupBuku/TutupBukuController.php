<?php

namespace App\Controllers\Accounting\TutupBuku;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\SaldoTutupBukuModel;
use App\Models\StockTutupBukuModel;
use App\Models\TutupBukuModel;

class TutupBukuController extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $divisisModel;
    protected $tutupBukuModel;
    protected $stockTutupBukuModel;
    protected $saldoTutupBukuModel;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = \Config\Services::encrypter();
        $this->divisisModel = new DivisisModel();
        $this->tutupBukuModel = new TutupBukuModel();
        $this->stockTutupBukuModel = new StockTutupBukuModel();
        $this->saldoTutupBukuModel = new SaldoTutupBukuModel();
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
            }

            if ($saldoTutupBuku) {
                $saldoTutupBukuVar = 1;
            }

            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
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

    public function saveAccountSupplier()
    {

        $this->AccountSupplierModel->insert([
            'supplier_id' => $this->request->getVar('supplier_id'),
            'ap_id' => $this->request->getVar('akun_ap_id'),
            'ar_id' => $this->request->getVar('akun_ar_id')
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Account Supplier Berhasil Ditambahkan"
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
