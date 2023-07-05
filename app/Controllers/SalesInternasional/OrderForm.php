<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use Config\Services;

class OrderForm extends BaseController
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
        return view('SalesInternasional/OrderForm/index');
    }

    public function createView()
    {
        return view('SalesInternasional/OrderForm/form');
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
