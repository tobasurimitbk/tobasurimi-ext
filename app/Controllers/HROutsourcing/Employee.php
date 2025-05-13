<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\HROutsourcingCompanyModel;

class Employee extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $hrOutsourcingCompanyModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->hrOutsourcingCompanyModel = new HROutsourcingCompanyModel();
    }


    public function index()
    {
        //
    }
}
