<?php

namespace App\Controllers;

class CompanyAccess extends BaseController
{

    public function __construct()
    {

    }

    public function companyAccess()
    {
        return view('companyAccess/index');
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
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }
}