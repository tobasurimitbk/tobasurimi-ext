<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

class TerimaFaktur extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function terimaFaktur()
    {
        return view('Purchase//index');
    }
}

?>