<?php

namespace App\Controllers;

class Tax extends BaseController
{

    public function __construct()
    {
    }

    public function dropdownTax()
    {
        $token = session()->get("login")->token;
        $type = $this->request->getGet("type");

        $responseTax = curl_request("GET", "/tax/getByType/$type", $token);

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