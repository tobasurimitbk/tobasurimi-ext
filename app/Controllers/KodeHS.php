<?php

namespace App\Controllers;

class KodeHS extends BaseController
{

    public function __construct()
    {
    }

    public function dropdownKodeHS()
    {
        $token = session()->get("login")->token;

        $responseKodeHS = curl_request("GET", "/hscode", $token);

        $dataKodeHS = [];
        if ($responseKodeHS["code"] === 200) {
            $dataKodeHS = json_decode($responseKodeHS["body"])->data;
        }

        $data = [
            "data" => $dataKodeHS
        ];

        echo json_encode($data);
        return;
    }
}