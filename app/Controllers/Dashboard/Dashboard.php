<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function dashboard()
    {
        dd($_SESSION);
        return view('Dashboard/dashboard/index');
    }
}
