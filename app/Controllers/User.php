<?php

namespace App\Controllers;

class User extends BaseController
{

    public function __construct()
    {

    }

    public function login()
    {
        return view('login/index');
    }

    public function user()
    {
        return view('user/index');
    }
}
