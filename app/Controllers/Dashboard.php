<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function dashboard()
    {
        return view('dashboard/index');
    }
}
