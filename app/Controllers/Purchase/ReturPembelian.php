<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

class ReturPembelian extends BaseController
{
    protected $token;
    protected $user_id;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function returPembelian()
    {
        return view('Purchase/returPembelian/index');
    }

    public function createReturPembelian()
    {
        return view('Purchase/returPembelian/form');
    }
}