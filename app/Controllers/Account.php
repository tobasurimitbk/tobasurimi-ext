<?php

namespace App\Controllers;

class Account extends BaseController
{

    public function __construct()
    {
    }

    public function account()
    {
        $token = session()->get("login")->token;

        return view('account/index');
    }
}
?>