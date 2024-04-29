<?php

namespace App\Controllers\Accounting\SettingAkunCosting;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\AccountDivisisModel;

class SettingAkunCostingController extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $divisisModel;
    protected $accountDivisisModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->divisisModel = new DivisisModel();
        $this->accountDivisisModel = new AccountDivisisModel();
    }

    public function index()
    {
        $Sub_AkunsModel = new Sub_AkunsModel();
        $subAkunsModel = $Sub_AkunsModel->asObject()->findAll();
        $data = [
            "subAkuns" => $subAkunsModel
        ];
        return view('Accounting/settingAkunCosting/index', $data);
    }

    public function saveAccountDepartment()
    {
        $divisisId = $this->request->getVar('id');
        $getDataAccountDivisis = $this->accountDivisisModel->where('divisis_id', $divisisId)->where('deleted_at', NULL)->first();

        if ($getDataAccountDivisis != null) {
            $this->accountDivisisModel->update($getDataAccountDivisis['id'], [
                'divisis_id' => $divisisId,
                'ap_id' => $this->request->getVar('akun_ap_id'),
                'ar_id' => $this->request->getVar('akun_ar_id')
            ]);
        } else {
            $this->accountDivisisModel->insert([
                'divisis_id' => $divisisId,
                'ap_id' => $this->request->getVar('akun_ap_id'),
                'ar_id' => $this->request->getVar('akun_ar_id')
            ]);
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Akun Department Berhasil Ditambahkan"
        ]);
    }
    public function get()
    {
        $id = $this->request->getVar('id');
        $dataAccountDivisis = $this->divisisModel
            ->join('account_divisis', 'divisis.id = account_divisis.divisis_id', 'left')
            ->where('divisis.id', $id)
            ->first();

        return response()->setJSON([
            'data' => $dataAccountDivisis,
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function allAccountDepartment()
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
}
