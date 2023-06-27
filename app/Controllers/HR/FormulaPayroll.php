<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;

class FormulaPayroll extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function formulaPayroll()
    {
        return view('hr/formulaPayroll/index');
    }
}
