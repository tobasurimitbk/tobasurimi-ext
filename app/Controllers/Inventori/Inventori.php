<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;

class Inventori extends BaseController
{
    public function stockSafetyView()
    {

        return view('Warehouse/stock/stock_safety');
    }
}
