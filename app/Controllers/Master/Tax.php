<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

use App\Models\TaxModel;

class Tax extends BaseController
{
    protected $token;
    protected $taxModel;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->taxModel = new TaxModel();
    }

    public function dropdownTax()
    {
        $type = $this->request->getGet("type");
        $dataTax = $this->taxModel->getTaxByType($type);

        $data = [
            "data" => $dataTax
        ];

        echo json_encode($data);
        return;
    }
}