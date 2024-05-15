<?php

namespace App\Controllers\Accounting\Rasio;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\AccountDivisisModel;
use App\Models\ProductionResultDetailModel;
use App\Models\ProductionResultModel;

class RasioController extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $divisisModel;
    protected $subAkunModel;
    protected $productionResultModel;
    protected $productionResultDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisisModel = new DivisisModel();
        $this->subAkunModel = new Sub_AkunsModel();
        $this->productionResultModel = new ProductionResultModel();
        $this->productionResultDetailModel = new ProductionResultDetailModel();
    }

    public function index()
    {
        $subAkunsModel = $this->subAkunModel->asObject()->findAll();
        $data = [
            'dataDivisi' => $this->divisisModel->getDivisiAccess(),
            "subAkuns" => $subAkunsModel
        ];
        return view('Accounting/rasio/index', $data);
    }

    public function createRasio()
    {
        $subAkunsModel = $this->subAkunModel->asObject()->findAll();
        $data = [
            'dataDivisi' => $this->divisisModel->getDivisiAccess(),
            "subAkuns" => $subAkunsModel
        ];
        return view('Accounting/rasio/form', $data);
    }

    public function saveRasio()
    {
        // $divisisId = $this->request->getVar('id');
        // $getDataAccountDivisis = $this->accountDivisisModel->where('divisis_id', $divisisId)->where('deleted_at', NULL)->first();

        // if ($getDataAccountDivisis != null) {
        //     $this->accountDivisisModel->update($getDataAccountDivisis['id'], [
        //         'divisis_id' => $divisisId,
        //         'ap_id' => $this->request->getVar('akun_ap_id'),
        //         'ar_id' => $this->request->getVar('akun_ar_id')
        //     ]);
        // } else {
        //     $this->accountDivisisModel->insert([
        //         'divisis_id' => $divisisId,
        //         'ap_id' => $this->request->getVar('akun_ap_id'),
        //         'ar_id' => $this->request->getVar('akun_ar_id')
        //     ]);
        // }

        // return response()->setJSON([
        //     'token' => csrf_hash(),
        //     'status' => true,
        //     'message' => "Akun Department Berhasil Ditambahkan"
        // ]);
    }
    public function get()
    {
        $id = $this->request->getVar('id');
        // $dataAccountDivisis = $this->divisisModel
        //     ->join('account_divisis', 'divisis.id = account_divisis.divisis_id', 'left')
        //     ->where('divisis.id', $id)
        //     ->first();

        // return response()->setJSON([
        //     'data' => $dataAccountDivisis,
        //     'token' => csrf_hash(),
        //     'status' => true,
        // ]);
    }

    public function allRasio()
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
            "deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $Sub_AkunsModel = new Sub_AkunsModel();
        $dataNamaAP = "";
        $dataNamaAR = "";

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->divisisModel->getListForAccount($condition, $addCondition, $limit, $offset);
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
                "parent_name"           => $data['divisi'],
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

    public function getRasioBarangJadi()
    {
        if (!empty($this->request->getVar('bulan'))) {
            $monthData = $this->request->getVar('bulan');
            list($month, $year) = explode('/', $monthData);
            $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $conditionProduction = [
                'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
                'divisi_id' => $this->request->getVar('department'),
            ];
            $productionResultDataTitle = $this->productionResultModel->getDataProductionResultWithDetail($conditionProduction);
            $totalQtyAll = 0;
            foreach ($productionResultDataTitle as $value) {
                $totalQtyAll += $value['qtyTotal'];
            }
            foreach ($productionResultDataTitle as &$value) {
                $value['totalQtyAll'] = $totalQtyAll;
            }
            if ($productionResultDataTitle) {
                return response()->setJSON([
                    'data' => $productionResultDataTitle,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }
        }
    }

    public function getRasioBarangDigunakan()
    {
        if (!empty($this->request->getVar('bulan'))) {
            $monthData = $this->request->getVar('bulan');
            list($month, $year) = explode('/', $monthData);
            $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $conditionProduction = [
                'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
                'divisi_id' => $this->request->getVar('department'),
            ];
            $productionResultDataTitle = $this->productionResultModel->getDataProductionResultBahanBakuWithDetail($conditionProduction);
            $totalQtyAll = 0;
            foreach ($productionResultDataTitle as $value) {
                $totalQtyAll += $value['qtyTotal'];
            }
            foreach ($productionResultDataTitle as &$value) {
                $value['totalQtyAll'] = $totalQtyAll;
            }
            if ($productionResultDataTitle) {
                return response()->setJSON([
                    'data' => $productionResultDataTitle,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }
        }
    }
}
