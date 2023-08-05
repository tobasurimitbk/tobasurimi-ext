<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use Config\Services;

class SalesContract extends BaseController
{
    protected $this_company_id;
    protected $encrypter;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
    }

    public function index()
    {
        return view('SalesInternasional/SalesContract/index');
    }

    public function ajax_list_sales_contract()
    {

    }

}
