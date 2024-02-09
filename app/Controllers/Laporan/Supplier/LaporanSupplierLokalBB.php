<?php

namespace App\Controllers\Laporan\Supplier;

use App\Controllers\BaseController;

class LaporanSupplierLokalBB extends BaseController
{
    public function index()
    {
        return view('Laporan/SupplierLokalBB/index/index');
    }
}
