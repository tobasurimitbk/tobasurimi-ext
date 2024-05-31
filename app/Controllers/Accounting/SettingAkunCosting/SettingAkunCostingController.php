<?php

namespace App\Controllers\Accounting\SettingAkunCosting;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\AccountDivisisModel;
use App\Models\SettingCostingDetailModel;
use App\Models\SettingCostingModel;

class SettingAkunCostingController extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $settingCosting;
    protected $settingCostingDetail;
    protected $divisisModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->settingCosting = new SettingCostingModel();
        $this->settingCostingDetail = new SettingCostingDetailModel();
        $this->divisisModel = new DivisisModel();
    }

    public function index()
    {
        $subAkunsModel = $this->Sub_AkunsModel->asObject()->findAll();
        $settingCosting = $this->settingCosting->getSettingCosting();
        $data = [
            "subAkuns" => $subAkunsModel,
            'dataDivisi' => $this->divisisModel->getDivisiAccess(),
            "settingCosting" => $settingCosting,
        ];
        return view('Accounting/settingAkunCosting/index', $data);
    }

    public function get()
    {
        $id = decrypt($this->request->getPost('id'));
        $dataSettingCosting = $this->settingCosting
            ->join('setting_costing_details', 'setting_costing.id = setting_costing_details.setting_costing_id', 'left')
            ->where('setting_costing.id', $id)
            ->first();

        return response()->setJSON([
            'data' => $dataSettingCosting,
            'token' => csrf_hash(),
            'status' => true,
        ]);
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
            "setting_costing.deletedAt" => NULL,
        ];

        $addCondition = [
            "company_id"        => $this->this_company_id,
            "divisi_id"        => $this->request->getGet("divisi_id"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->settingCosting->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id_setting_costing']),
                "keterangan"                => $data['name'],
                "coa_id"                 => $data['coa_id'],
                "divisi_id"                 => $this->request->getGet("divisi_id"),
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

    public function saveCosting()
    {
        // Retrieve POST data
        $id = decrypt($this->request->getPost('id'));
        $coa = $this->request->getPost('coa');
        $divisi = $this->request->getPost('divisi');

        // Proceed with update if data is valid
        if ($id && $coa && $divisi) {
            $check = $this->settingCostingDetail->where('setting_costing_id', $id)->where('company_id', $this->this_company_id)->where('divisi_id', $divisi)->where('coa_id', $coa)->first();
            $data = [
                'setting_costing_id' => $id,
                'company_id' => $this->this_company_id,
                'divisi_id' => $divisi,
                'coa_id' => $coa,
            ];
            if ($check) {
                $this->settingCostingDetail->update($check['id'], $data);
            } else {
                $this->settingCostingDetail->insert($data);
            }

            return $this->response->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Setting Costing Berhasil Disimpan"
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => "Invalid Data"
            ]);
        }
    }
}
