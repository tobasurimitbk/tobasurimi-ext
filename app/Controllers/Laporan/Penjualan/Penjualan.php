<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;

class Penjualan extends BaseController
{
    public function index()
    {
        return view('Laporan/LaporanSales/index/index');
    }
}
