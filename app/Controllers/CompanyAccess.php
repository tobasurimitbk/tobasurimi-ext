<?php

namespace App\Controllers;

class CompanyAccess extends BaseController
{

    public function __construct()
    {

    }

    public function companyAccess()
    {
        return view('companyAccess/index');
    }
}