<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;

class JamKerja extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }


    public function jamKerja()
    {
        return view('hr/jamKerja/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $response = curl_request("GET", "/employeeLoan", $this->token, $payload);
        $dataJamKerja = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataJamKerja, [
                    // "id" => $data->id,
                    // "amount" => $data->amount,
                    // "installment_month" => $data->installment_month,
                    // "remaining_amount" => $data->remaining_amount,
                    // "loan_date" => $data->loan_date,
                    // "term" => $data->term,
                    // "status" => $data->status,
                    // "approve_by" => $data->approve_by,
                    // "nip" => $data->nip,
                    // "employeeName" => $data->employeeName,
                    // "is_posted" => $data->is_posted,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataJamKerja,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }
}
