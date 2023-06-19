<?php

namespace App\Controllers;

class POLokal extends BaseController
{

    public function __construct()
    {
    }

    public function poLokal()
    {
        return view('poLokal/index');
    }

    public function createPOLokal()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        //Get Supplier
        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this_company_id", $token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        //Get Valuta Asing By Metadata
        $responseValuta = curl_request("GET", "/metadata/all?name=valuta_asing", $token);

        $dataValuta = [];
        if ($responseValuta["code"] === 200) {
            $dataValuta = json_decode($responseValuta["body"])->data;
        }
        
        $data = [
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        return view('poLokal/form', $data);
    }
}