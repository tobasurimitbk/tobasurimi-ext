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
        $subAkunsModel = $Sub_AkunsModel->getAPAR($this->this_company_id);
        $divisi = $this->divisiModel->getDivisiAccess();

        if (!empty(@$_GET['type'])) {
            $type = $this->request->getGet('type');
        }

        $data = [
            'type' => $type,
            'kategoriBP' => $metaDataModel->where('name', 'Kelompok BP')->findAll(),
            'kategoriBarangAkun' => $metaDataModel->asObject()->where('name', 'kategori_barang_akun')->findAll(),
            "subAkuns" => $subAkunsModel,
            "divisi" => $divisi
        ];
        // return view('Warehouse/parentBarang/index', $data);
        return view('Accounting/parentBarang/index', $data);
    }

    public function saveTipeBarang()
    {
        $barangMasterId = $this->request->getVar('barang_id');
        $accountBarangId = $this->request->getVar('id');
        $divisiId = $this->request->getVar('divisi_id');
        $getDataAccountBarang = $this->accountBarangModel->where('divisi_id', $divisiId)->where('id', $accountBarangId)->where('deleted_at', NULL)->first();

        if ($getDataAccountBarang != null) {
            $this->accountBarangModel->update($getDataAccountBarang['id'], [
                'ap_id' => $this->request->getVar('akun_ap_id'),
                'ar_id' => $this->request->getVar('akun_ar_id'),
                'pemakaian_id' => $this->request->getVar('akun_pemakaian_id'),
                'kategori_id' => $this->request->getVar('kategori'),
            ]);
        } else {
            $this->accountBarangModel->insert([
                'barang_master_id' => $barangMasterId,
                'company_id' => $this->this_company_id,
                'divisi_id' => $divisiId,
                'ap_id' => $this->request->getVar('akun_ap_id'),
                'ar_id' => $this->request->getVar('akun_ar_id'),
                'pemakaian_id' => $this->request->getVar('akun_pemakaian_id'),
                'kategori_id' => $this->request->getVar('kategori'),
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
        $subAkunsModel = $this->Sub_AkunsModel->getAPAR($this->this_company_id);
        // $dataAccountBarang = $this->barangMasterModel
        //     ->select('barang_master.*,account_barang.divisi_id,account_barang.ar_id,account_barang.ap_id,account_barang.kategori_id,account_barang.pemakaian_id')
        //     ->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left')
        //     ->where('barang_master.id', $id)
        //     ->where('divisi_id', $divisiId)
        //     ->first();
        $dataAccountBarang = $this->accountBarangModel
            ->select('barang_master.*,account_barang.divisi_id,account_barang.ar_id,account_barang.ap_id,account_barang.kategori_id,account_barang.pemakaian_id, account_barang.barang_master_id')
            ->join('barang_master', 'barang_master.id = account_barang.barang_master_id', 'left')
            ->where('account_barang.id', $id)
            ->where('account_barang.divisi_id', $divisiId)
            ->first();

        // $data = [
        //     'dataAccountBarang' => $dataAccountBarang,
        //     "subAkuns" => $subAkunsModel,
        // ];

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
            "filter_coa"    => $this->request->getGet("filter_coa"),
            'filter_divisi' => $this->request->getGet('filter_divisi'),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        // Get the filtered result
        $res = $this->accountBarangModel->getListForAccount($condition, $addCondition, $limit, $offset);

        $rdata = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        // Filter the data and populate rdata
        foreach ($res['data'] as $data) {
            $dataNamaAP = "-";
            $dataNamaAR = "-";
            $dataNamaPemakaian = "-";

            if ($data['ar_id'] != null) {
                $dataAR = $this->Sub_AkunsModel->where('id', $data['ar_id'])->first();
                $dataNamaAR = $dataAR['no_sub'];
            }

            if ($data['ap_id'] != null) {
                $dataAP = $this->Sub_AkunsModel->where('id', $data['ap_id'])->first();
                $dataNamaAP = $dataAP['no_sub'];
            }

            if ($data['pemakaian_id'] != null || $data['pemakaian_id'] != "0") {
                $dataPemakaian = $this->Sub_AkunsModel->where('id', $data['pemakaian_id'])->first();
                $dataNamaPemakaian = $dataPemakaian['no_sub'] ?? "-";
            }
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data['id'],
                "divisi_id"             => $data['divisi_id'],
                "parent_name"           => str_replace(' ', '', "(" .  $data['kode_barang']) . ")  " . $data['barang_name'] . " - " . $data['spesifikasi'],
                "keterangan"            => strtoupper($data['keterangan']),
                "divisi"                => strtoupper($data['divisi']),
                "ap_id"                 => isset($data['ap_id']) ? $data['ap_id'] : "-",
                "ar_id"                 => isset($data['ar_id']) ? $data['ar_id'] : "-",
                "pemakaian_id"          => isset($data['pemakaian_id']) ? $data['pemakaian_id'] : "-",
                "ap_no"                 => $dataNamaAP,
                "ar_no"                 => $dataNamaAR,
                "pemakaian_no"          => $dataNamaPemakaian,
            ]);
        }

        // Calculate the total records and filtered records based on rdata
        $recordsFiltered = $res['totalFilteredData']; // Filtered data count
        $recordsTotal = $res['totalData']; // Total unfiltered records

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $recordsTotal,
            "recordsFiltered"   => $recordsFiltered,
            "data"              => $rdata,
            "payload"           => $payload,
            "test"              => $_GET
        ];

        return response()->setJSON($data);
    }


    // public function allTipeBarang()
    // {
    //     $payload = [
    //         "pageSize" => $this->request->getGet("length"),
    //         "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
    //         "search" => $this->request->getGet("search"),
    //         "sort" => $this->request->getGet("sort"),
    //         "sortType" => $this->request->getGet("sortType"),
    //     ];

    //     $condition = [
    //         "type_barang" => $this->request->getGet('parent_type'),
    //         'barang_master.company_id' => $this->this_company_id,
    //         'divisis.company_id' => $this->this_company_id,
    //         "barang_master.deletedAt" => NULL,
    //         'divisis.deletedAt' => null
    //     ];

    //     $addCondition = [
    //         "search"        => $this->request->getGet("search"),
    //         "filter_coa"        => $this->request->getGet("filter_coa"),
    //         'filter_divisi' => $this->request->getGet('filter_divisi'),
    //         "sort"          => $this->request->getGet("sort"),
    //         "sortType"      => $this->request->getGet("sortType")
    //     ];

    //     $divisiAccess = $this->divisiModel->getDivisiAccess();
    //     $divisiAccessArr = [];

    //     foreach ($divisiAccess as $d) {
    //         array_push($divisiAccessArr, $d['id']);
    //     }

    //     $dataNamaAP = "";
    //     $dataNamaAR = "";
    //     $dataNamaPemakaian = "";

    //     $limit = $this->request->getGet("length");
    //     $offset = $this->request->getGet("start");

    //     $res = $this->barangMasterModel->getListForAccount($condition, $divisiAccessArr, $addCondition, $limit, $offset);

    //     $rdata = [];

    //     $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

    //     foreach ($res['data'] as $data) {
    //         // var_dump($res);
    //         // exit;
    //         $dataNamaAP = "-";
    //         $dataNamaAR = "-";
    //         $dataNamaPemakaian = "-";

    //         $dataAccountBarang = $this->barangMasterModel
    //             ->select('barang_master.*,account_barang.divisi_id,account_barang.ar_id,account_barang.ap_id,account_barang.pemakaian_id')
    //             ->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left')
    //             ->where('barang_master.id', $data['id'])
    //             ->where('divisi_id', $data['divisi_id'])
    //             ->where('barang_master.company_id', $this->this_company_id)
    //             ->where('account_barang.deleted_at', null)
    //             ->where('barang_master.deletedAt', null)
    //             ->first();

    //         // var_dump($dataAccountBarang);
    //         // exit;

    //         if ($dataAccountBarang != null) {
    //             $dataAR = $this->Sub_AkunsModel->where('id', $dataAccountBarang['ar_id'])->first();
    //             $dataAP = $this->Sub_AkunsModel->where('id', $dataAccountBarang['ap_id'])->first();
    //             $dataPemakaian = $this->Sub_AkunsModel->where('id', $dataAccountBarang['pemakaian_id'])->first();

    //             if ($dataAR != null) {
    //                 $dataNamaAR = $dataAR['no_sub'];
    //             } else {
    //                 $dataNamaAR = "-";
    //             }

    //             if ($dataAP != null) {
    //                 $dataNamaAP = $dataAP['no_sub'];
    //             } else {
    //                 $dataNamaAP = "-";
    //             }

    //             if ($dataPemakaian != null) {
    //                 $dataNamaPemakaian = $dataPemakaian['no_sub'];
    //             } else {
    //                 $dataNamaPemakaian = "-";
    //             }
    //         }

    //         if ($addCondition['filter_coa'] == "belum") {
    //             if ($dataNamaAP == "-" || $dataAP == "-") {
    //                 array_push($rdata, [
    //                     "no"                    => $no++,
    //                     "id"                    => $data['id'],
    //                     "divisi_id"             => $data['divisi_id'],
    //                     "parent_name"           => str_replace(' ', '', $data['kode_barang']) . "  " . $data['barang_name'],
    //                     "divisi"                => strtoupper($data['divisi']),
    //                     "ap_id"                 => $dataAccountBarang['ap_id'],
    //                     "ar_id"                 => $dataAccountBarang['ar_id'],
    //                     "pemakaian_id"          => $dataAccountBarang['pemakaian_id'],
    //                     "ap_no"                 => $dataNamaAP,
    //                     "ar_no"                 => $dataNamaAR,
    //                     "pemakaian_no"          => $dataNamaPemakaian,
    //                 ]);
    //             }
    //         } elseif ($addCondition['filter_coa'] == "sudah") {
    //             if ($dataNamaAP != "-" && $dataAP != "-") {
    //                 array_push($rdata, [
    //                     "no"                    => $no++,
    //                     "id"                    => $data['id'],
    //                     "divisi_id"             => $data['divisi_id'],
    //                     "parent_name"           => str_replace(' ', '', $data['kode_barang']) . "  " . $data['barang_name'],
    //                     "divisi"                => strtoupper($data['divisi']),
    //                     "ap_id"                 => $dataAccountBarang['ap_id'],
    //                     "ar_id"                 => $dataAccountBarang['ar_id'],
    //                     "pemakaian_id"          => $dataAccountBarang['pemakaian_id'],
    //                     "ap_no"                 => $dataNamaAP,
    //                     "ar_no"                 => $dataNamaAR,
    //                     "pemakaian_no"          => $dataNamaPemakaian,
    //                 ]);
    //             }
    //         } else {
    //             array_push($rdata, [
    //                 "no"                    => $no++,
    //                 "id"                    => $data['id'],
    //                 "divisi_id"             => $data['divisi_id'],
    //                 "parent_name"           => str_replace(' ', '', $data['kode_barang']) . "  " . $data['barang_name'],
    //                 "divisi"                => strtoupper($data['divisi']),
    //                 "ap_id"                 => isset($dataAccountBarang['ap_id']) ? $dataAccountBarang['ap_id'] : "-",
    //                 "ar_id"                 => isset($dataAccountBarang['ar_id']) ? $dataAccountBarang['ar_id'] : "-",
    //                 "pemakaian_id"          => isset($dataAccountBarang['pemakaian_id']) ? $dataAccountBarang['pemakaian_id'] : "-",
    //                 "ap_no"                 => $dataNamaAP,
    //                 "ar_no"                 => $dataNamaAR,
    //                 "pemakaian_no"          => $dataNamaPemakaian,
    //             ]);
    //         }

    //         // if ($searchBarangId == false && $searchDivisiId !== true) {

    //         // }
    //     }

    //     $data = [
    //         "draw"              => intval($this->request->getGet("draw")),
    //         "recordsTotal"      => $res['totalData'],
    //         "recordsFiltered"   => $res['totalFilteredData'],
    //         "data"              => $rdata,
    //         "payload"           => $payload,
    //         "test" => $_GET
    //     ];

    //     return response()->setJSON($data);
    // }
}
