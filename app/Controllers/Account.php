<?php

namespace App\Controllers;

class Account extends BaseController
{

    public function __construct()
    {
    }

    public function account()
    {
        $token = session()->get("login")->token;

        //Get Kelompok Akun by Metadata
        $responseKelompokAkun = curl_request("GET", "/metadata/all", $token);

        $dataKelompokAkun = [];
        if ($responseKelompokAkun["code"] === 200) {
            $dataKelompokAkun = json_decode($responseKelompokAkun["body"])->data;
        }

        $data = [
            "dataKelompokAkun" => $dataKelompokAkun
        ];

        return view('account/index', $data);
    }
}
?>