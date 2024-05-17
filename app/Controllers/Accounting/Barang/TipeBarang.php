<?php

namespace App\Controllers\Accounting\Barang;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\BarangMasterModel;
use App\Models\Sub_AkunsModel;
use App\Models\AccountBarangModel;
use App\Models\DivisisModel;

class TipeBarang extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $barangMasterModel;
    protected $accountBarangModel;
    protected $divisiModel;


    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->accountBarangModel = new AccountBarangModel();
        $this->divisiModel = new DivisisModel();
    }

    public function index()
    {
        $metaDataModel = new MetadataModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $type = "bahan_baku";
        $subAkunsModel = $Sub_AkunsModel->asObject()->findAll();
        $divisi = $this->divisiModel->getDivisiAccess();

        if (!empty(@$_GET['type'])) {
            $type = $this->request->getGet('type');
        }

        $data = [
            'type' => $type,
            'kategoriBP' => $metaDataModel->where('name', 'Kelompok BP')->findAll(),
            "subAkuns" => $subAkunsModel,
            "divisi" => $divisi
        ];
        // return view('Warehouse/parentBarang/index', $data);
        return view('Accounting/parentBarang/index', $data);
    }

    public function saveTipeBarang()
    {
        $barangMasterId = $this->request->getVar('id');
        $divisiId = $this->request->getVar('divisi_id');
        $getDataAccountBarang = $this->accountBarangModel->where('divisi_id', $divisiId)->where('barang_master_id', $barangMasterId)->where('deleted_at', NULL)->first();

        if ($getDataAccountBarang != null) {
            $this->accountBarangModel->update($getDataAccountBarang['id'], [
                'barang_master_id' => $barangMasterId,
                'divisi_id' => $divisiId,
                'ap_id' => $this->request->getVar('akun_ap_id'),
                'ar_id' => $this->request->getVar('akun_ar_id')
            ]);
        } else {
            $this->accountBarangModel->insert([
                'barang_master_id' => $barangMasterId,
                'divisi_id' => $divisiId,
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
        $divisiId = $this->request->getVar('divisi_id');
        $dataAccountBarang = $this->barangMasterModel
            ->select('barang_master.*,account_barang.divisi_id,account_barang.ar_id,account_barang.ap_id')
            ->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left')
            ->where('barang_master.id', $id)
            ->where('divisi_id', $divisiId)
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
            "type_barang" => $this->request->getGet('parent_type'),
            'barang_master.company_id' => $this->this_company_id,
            'divisis.company_id' => $this->this_company_id,
            "barang_master.deletedAt" => NULL,
            'divisis.deletedAt' => null
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter_coa"        => $this->request->getGet("filter_coa"),
            'filter_divisi' => $this->request->getGet('filter_divisi'),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $divisiAccess = $this->divisiModel->getDivisiAccess();
        $divisiAccessArr = [];

        foreach ($divisiAccess as $d) {
            array_push($divisiAccessArr, $d['id']);
        }

        $dataNamaAP = "";
        $dataNamaAR = "";

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->barangMasterModel->getListForAccount($condition, $divisiAccessArr, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            // var_dump($res);
            // exit;
            $dataNamaAP = "-";
            $dataNamaAR = "-";

            $dataAccountBarang = $this->barangMasterModel
                ->select('barang_master.*,account_barang.divisi_id,account_barang.ar_id,account_barang.ap_id')
                ->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left')
                ->where('barang_master.id', $data['id'])
                ->where('divisi_id', $data['divisi_id'])
                ->first();

            // dd($dataAccountBarang);

            if ($dataAccountBarang != null) {
                $dataAR = $this->Sub_AkunsModel->where('id', $dataAccountBarang['ar_id'])->first();
                $dataAP = $this->Sub_AkunsModel->where('id', $dataAccountBarang['ap_id'])->first();

                if ($dataAR != null) {
                    $dataNamaAR = $dataAR['no_sub'];
                } else {
                    $dataNamaAR = "-";
                }

                if ($dataAP != null) {
                    $dataNamaAP = $dataAP['no_sub'];
                } else {
                    $dataNamaAP = "-";
                }
            }

            if ($addCondition['filter_coa'] == "belum") {
                if ($dataNamaAP == "-" || $dataAP == "-") {
                    array_push($rdata, [
                        "no"                    => $no++,
                        "id"                    => $data['id'],
                        "divisi_id"             => $data['divisi_id'],
                        "parent_name"           => str_replace(' ', '', $data['kode_barang']) . "  " . $data['barang_name'],
                        "divisi"                => strtoupper($data['divisi']),
                        "ap_id"                 => $dataNamaAP,
                        "ar_id"                 => $dataNamaAR,

                    ]);
                }
            } elseif ($addCondition['filter_coa'] == "sudah") {
                if ($dataNamaAP != "-" && $dataAP != "-") {
                    array_push($rdata, [
                        "no"                    => $no++,
                        "id"                    => $data['id'],
                        "divisi_id"             => $data['divisi_id'],
                        "parent_name"           => str_replace(' ', '', $data['kode_barang']) . "  " . $data['barang_name'],
                        "divisi"                => strtoupper($data['divisi']),
                        "ap_id"                 => $dataNamaAP,
                        "ar_id"                 => $dataNamaAR,

                    ]);
                }
            } else {
                array_push($rdata, [
                    "no"                    => $no++,
                    "id"                    => $data['id'],
                    "divisi_id"             => $data['divisi_id'],
                    "parent_name"           => str_replace(' ', '', $data['kode_barang']) . "  " . $data['barang_name'],
                    "divisi"                => strtoupper($data['divisi']),
                    "ap_id"                 => $dataNamaAP,
                    "ar_id"                 => $dataNamaAR,

                ]);
            }

            // if ($searchBarangId == false && $searchDivisiId !== true) {

            // }
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
