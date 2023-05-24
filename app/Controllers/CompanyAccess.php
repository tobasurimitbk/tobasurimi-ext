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

        //Get User
        $responseUser = curl_request("GET", "/users/selectOption", $token);

        $dataUser = [];
        if ($responseUser["code"] === 200) {
            $dataUser = json_decode($responseUser["body"])->data;
        }
         
        //Get Company
        $responseCompany = curl_request("GET", "/companies/all", $token);

        $dataCompany = [];
        if ($responseCompany["code"] === 200) {
            $dataCompany = json_decode($responseCompany["body"])->data;
        }

        $data = [
            "dataRole" => $dataRole,
            "dataUser" => $dataUser,
            "dataCompany" => $dataCompany
        ];

        return view('companyAccess/index', $data);
    }
}