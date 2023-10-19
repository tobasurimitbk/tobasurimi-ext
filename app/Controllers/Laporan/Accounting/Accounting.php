<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;

class Accounting extends BaseController
{
    public function index()
    {
        return view('Laporan/index/index');
    }
}
