<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function dashboard()
    {
        return view('dashboard/dashboard');
    }

    public function blank()
    {
        return view('dashboard/blank');
    }
}
