<?php

namespace App\Controllers\Accounting\Barang;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\BarangMasterModel;
use App\Models\Sub_AkunsModel;
use App\Models\AccountBarangModel;

class TipeBarang extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $barangMasterModel;
    protected $accountBarangModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->accountBarangModel = new AccountBarangModel();
    }

    public function index()
    {
        $metaDataModel = new MetadataModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $type = "bahan_baku";
        $subAkunsModel = $Sub_AkunsModel->asObject()->findAll();

        if (!empty(@$_GET['type'])) {
            $type = $this->request->getGet('type');
        }

        $data = [
            'type' => $type,
            'kategoriBP' => $metaDataModel->where('name', 'Kelompok BP')->findAll(),
            "subAkuns" => $subAkunsModel
        ];
        // return view('Warehouse/parentBarang/index', $data);
        return view('Accounting/parentBarang/index', $data);
    }

    public function saveTipeBarang()
    {

        $type = $this->request->getVar('type');
        $barangMasterId = $this->request->getVar('id');
        $getDataAccountBarang = $this->accountBarangModel->where('barang_master_id', $barangMasterId)->where('deleted_at', NULL)->first();

        if ($getDataAccountBarang != null) {
            $this->accountBarangModel->update($getDataAccountBarang['id'], [
                'barang_master_id' => $barangMasterId,
                'ap_id' => $this->request->getVar('akun_ap_id'),
                'ar_id' => $this->request->getVar('akun_ar_id')
            ]);
        } else {
            $this->accountBarangModel->insert([
                'barang_master_id' => $barangMasterId,
                'ap_id' => $this->request->getVar('akun_ap_id'),
                'ar_id' => $this->request->getVar('akun_ar_id')
            ]);
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Akun Barang Berhasil Ditambahkan"
        ]);
    }
    public function get()
    {
        $id = $this->request->getVar('id');
        $dataAccountBarang = $this->barangMasterModel
            ->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left')
            ->where('barang_master.id', $id)
            ->first();

        return response()->setJSON([
            'data' => $dataAccountBarang,
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function allTipeBarang()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "company_id"  => $this->this_company_id,
            "type_barang" => $this->request->getGet('parent_type'),
            "deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter_coa"        => $this->request->getGet("filter_coa"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $Sub_AkunsModel = new Sub_AkunsModel();
        $dataNamaAP = "";
        $dataNamaAR = "";

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->barangMasterModel->getListForAccount($condition, $addCondition, $limit, $offset);
        $subAkunsModel = $Sub_AkunsModel->asObject()->findAll();

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($res['data'] as $data) {
            // var_dump($res);
            // exit;
            foreach ($subAkunsModel as $datas) {
                if ($data['ap_id'] == $datas->id) {
                    $dataNamaAP = $datas->no_sub;
                } elseif ($data['ap_id'] == NULL) {
                    $dataNamaAP = "-";
                }
                if ($data['ar_id'] == $datas->id) {
                    $dataNamaAR = $datas->no_sub;
                } elseif ($data['ar_id'] == NULL) {
                    $dataNamaAR = "-";
                }
            }
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data['id'],
                "parent_name"           => $data['barang_name'],
                "ap_id"                 => $dataNamaAP,
                "ar_id"                 => $dataNamaAR,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
            "test" => $_GET
        ];

        return response()->setJSON($data);
    }
}
