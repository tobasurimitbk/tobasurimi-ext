<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;

class Retur extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
    }

    public function index()
    {
        return view('SalesLokal/Retur/index');
    }

    public function createView()
    {
        return view('SalesLokal/Retur/form');
    }

    public function all()
    {
    }

    public function save()
    {
    }

    public function getById()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
