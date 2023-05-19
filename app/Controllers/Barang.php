<?php

namespace App\Controllers;

class Barang extends BaseController
{

    public function __construct()
    {
    }

    public function barang()
    {
        $token = session()->get("login")->token;

        return view('barang/index');
    }
}
?>