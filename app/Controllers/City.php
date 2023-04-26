<?php

namespace App\Controllers;

class City extends BaseController
{

    public function __construct()
    {

    }

    public function getCityByProvince($id = null)
    {
        $token = session()->get("login")->token;
        $dataCity = [];
        $responseCity = curl_request("GET", "/cities/all?idProvince=$id", $token);
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