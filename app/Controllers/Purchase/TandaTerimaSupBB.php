<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

class TandaTerimaSupBB extends BaseController
{



    public function __construct()
    {
    }

    public function index()
    {
        return view('Purchase/terimaSupplierLokal/bp/index');
    }

    public function create()
    {
        $data = [];
        return view('Purchase/terimaSupplierLokal/bp/form');
    }
}
