<?php

namespace App\Controllers\Accounting\SettingAkunCosting;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\AccountDivisisModel;
use App\Models\SettingCostingModel;

class SettingAkunCostingController extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $settingCosting;
    protected $divisisModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->settingCosting = new SettingCostingModel();
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

    public function saveCosting()
    {
        $id = $this->request->getVar('id');
        $value = $this->request->getVar('value');

        var_dump($id);
        var_dump($value);
        exit;

        $this->settingCosting->update($id, [
            'coa' => $value
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Setting Costing Berhasil Disimpan"
        ]);
    }
}
