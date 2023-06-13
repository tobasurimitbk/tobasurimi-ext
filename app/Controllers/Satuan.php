<?php

namespace App\Controllers;

class Satuan extends BaseController
{

    public function __construct()
    {
    }

    public function dropdownSatuan()
    {
        $token = session()->get("login")->token;

        $responseSatuan = curl_request("GET", "/satuan/all", $token);

        $dataSatuan = [];
        if ($responseSatuan["code"] === 200) {
            $dataSatuan = json_decode($responseSatuan["body"])->data;
        }

        $data = [
            "data" => $dataSatuan
        ];

        echo json_encode($data);
        return;
    }
}