<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class City extends BaseController
{
    protected $token;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
    }

    public function getCityByProvince($id = null)
    {
        $dataCity = [];
        $responseCity = curl_request("GET", "/cities/all?idProvince=$id", $this->token);
        if ($responseCity["code"] === 200) {
            $dataCity = json_decode($responseCity["body"])->data;
        }

        $data = [
            "data" => $dataCity
        ];

        echo json_encode($data);
        return;
    }
}