<?php

namespace App\Controllers;

class CompanyAccess extends BaseController
{

    public function __construct()
    {

    }

    public function companyAccess()
    {
        $token = session()->get("login")->token;

         //Get Role
         $responseRole = curl_request("GET", "/roles/selectOption", $token);

         $dataRole = [];
         if ($responseRole["code"] === 200) {
             $dataRole = json_decode($responseRole["body"])->data;
         }
         
        $data = [
            "dataRole" => $dataRole
        ];

        return view('companyAccess/index', $data);
    }
}