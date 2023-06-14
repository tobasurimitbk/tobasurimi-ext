<?php

namespace App\Controllers;

class SPP extends BaseController
{

    public function __construct()
    {
    }

    public function spp()
    {
        return view('spp/index');
    }
}