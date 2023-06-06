<?php

namespace App\Controllers;

class Metadata extends BaseController
{

    public function __construct()
    {
    }

    public function dropdownMetadata()
    {
        $token = session()->get("login")->token;
        $name = $this->request->getGet("name");

        $responseMetadata = curl_request("GET", "/metadata/all?name=$name", $token);

        $dataMetadata = [];
        if ($responseMetadata["code"] === 200) {
            $dataMetadata = json_decode($responseMetadata["body"])->data;
        }

        $data = [
            "data" => $dataMetadata
        ];

        echo json_encode($data);
        return;
    }
}