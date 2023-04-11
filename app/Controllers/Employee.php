<?php

namespace App\Controllers;

class Employee extends BaseController
{

    public function __construct()
    {

    }

    public function employee()
    {
        $token = session()->get("login")->token;
         //Get User
        $responseEmployee = curl_request("GET", "/employees", $token);

        $dataEmployee = [];
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }
         
        $data = [
            "dataEmployee" => $dataEmployee,
        ];

        return view('employee/index', $data);
    }
}