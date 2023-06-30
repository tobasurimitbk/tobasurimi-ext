<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;

class Penawaran extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function penawaran()
    {
        return view('Sales/penawaran/index');
    }
}