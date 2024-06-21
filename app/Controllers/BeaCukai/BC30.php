<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\BC30Model;
use App\Models\CeisaSettingModel;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54

class BC30 extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $bc30Model;

    public function __construct()
    {
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->bc30Model = new BC30Model();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first()
        ];

        return view('BeaCukai/bc-30/index', $data);
    }

    public function create()
    {
        $data = [];
        return view('BeaCukai/bc-30/form', $data);
    }

    public function dropdownSalesOrder()
    {
        $tipeSalesOrder = $this->request->getVar('tipe_sales_order');
        $result = $this->bc30Model->getListSalesOrder(
            $this->this_company_id,
            $tipeSalesOrder
        );

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
}
