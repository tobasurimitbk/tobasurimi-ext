<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Models\WorkOrdersModel;

class MaterialRequest extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $workOrdersModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->workOrdersModel = new WorkOrdersModel();
    }

    public function index()
    {
        return view('Production/materialRequest/index');
    }
}