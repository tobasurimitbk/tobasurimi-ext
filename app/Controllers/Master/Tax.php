<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Tax extends BaseController
{
    protected $token;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
    }

    public function dropdownTax()
    {
        $type = $this->request->getGet("type");

        $responseTax = curl_request("GET", "/tax/getByType/$type", $this->token);

        $dataTax = [];
        if ($responseTax["code"] === 200) {
            $dataTax = json_decode($responseTax["body"])->data;
        }

        $data = [
            "data" => $dataTax
        ];

        echo json_encode($data);
        return;
    }
}