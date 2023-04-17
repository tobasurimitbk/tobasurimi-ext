<?php

namespace App\Controllers;

class Company extends BaseController
{

    public function __construct()
    {

    }

    public function company()
    {
        $token = session()->get("login")->token;

        return view('company/index');
    }

    public function allCompany()
    {
        $token = session()->get("login")->token;

        $payload = [
            "limit" => $this->request->getGet("length"),
            "page" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search")
        ];

        $response = curl_request("GET", "/companies", $token, $payload);
        $dataCompany = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->totalRows;

            foreach ($body as $data) {
                array_push($dataCompany, [
                    "id" => $data->id,
                    "company" => $data->company,
                    "holding_company" => $data->holding_company,
                    "address" => $data->address,
                    "phone" => $data->phone,
                    "email" => $data->email
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataCompany
        ];

        echo json_encode($data);
        return;
    }
}