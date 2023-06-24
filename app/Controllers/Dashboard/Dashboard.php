<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function dashboard()
    {
        return view('dashboard/index');
    }
}
