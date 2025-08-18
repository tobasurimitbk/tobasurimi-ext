<?php

namespace App\Controllers\BiayaExim\BiayaLokal;

use App\Controllers\BaseController;

class BiayaLokal extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        return \view('BiayaExim/BiayaLokal/index');
    }

    public function create()
    {
        return \view('BiayaExim/');
    }
}
