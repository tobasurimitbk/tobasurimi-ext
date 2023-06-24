<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Metadata extends BaseController
{
    protected $token;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
    }

    public function dropdownMetadata()
    {
        $name = $this->request->getGet("name");

        $responseMetadata = curl_request("GET", "/metadata/all?name=$name", $this->token);

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