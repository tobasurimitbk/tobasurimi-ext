<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;

class PinjamanKaryawan extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function pinjamanKaryawan()
    {
        return view('hr/pinjamanKaryawan/index');
    }
}
