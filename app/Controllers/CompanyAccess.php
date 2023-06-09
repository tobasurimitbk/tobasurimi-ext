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

    public function allCompanyAccess()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this_company_id
        ];

        $response = curl_request("GET", "/users/userCompany", $token, $payload);
        $dataUser = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataUser, [
                    "id" => $data->id,
                    "username" => $data->username,
                    "name" => $data->name,
                    "status" => $data->status,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataUser,
            "response" => $response
        ];

        echo json_encode($data);
        return;
    }
}