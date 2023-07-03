<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use Config\Services;

class Perijinan extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
    }

    public function perijinan()
    {
        return view('hr/perijinan/index');
    }


    public function createView()
    {
        $data = [
            "status" => ["IJIN", "ALPHA", "CUTI", "SAKIT", "LIBUR"]
        ];

        //Get Employee
        $responseEmployee = curl_request("GET", "/employees/selectOption", $this->token);

        $dataEmployee = [];
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

        $data["dataEmployee"] = $dataEmployee;

        return view('hr/perijinan/form', $data);
    }

    public function getById()
    {
        $data = [
            "status" => ["IJIN", "ALPHA", "CUTI", "SAKIT", "LIBUR"]
        ];

        //Get Employee
        $responseEmployee = curl_request("GET", "/employees/selectOption", $this->token);

        $dataEmployee = [];
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

        $data["dataEmployee"] = $dataEmployee;

        if (!empty($id)) {
            $id = $this->encrypter->decrypt(hex2bin($id));

            $resShift = curl_request("GET", "/perijinan/$id", $this->token);
            $dataShift = [];

            if ($resShift["code"] === 200) {
                $dataShift = json_decode($resShift["body"])->data;
            }

            $data["data"] = $dataShift;
        }

        return view('hr/perijinan/form', $data);
    }

    public function allPerijinan()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $response = curl_request("GET", "/perijinan", $this->token, $payload);
        $dataShift = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataShift, [
                    // "id" =>  bin2hex($this->encrypter->encrypt($data->id)),
                    // "nama_shift" => $data->nama_shift,
                    // "sot" => $data->SOT,
                    // "eot" => $data->EOT,
                    // "bsot" => $data->BSOT,
                    // "beot" => $data->BEOT,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataShift,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        $rules = [
            "employee_id" => [
                "rules" => "required"
            ],
            "start_date" => [
                "rules" => "required"
            ],
            "end_date" => [
                "rules" => "required"
            ],
            "status" => [
                "rules" => "required"
            ],
            "reason" => [
                "rules" => "required"
            ],
        ];


        if ($this->validate($rules)) {
            $payload = json_encode([
                "company_id" => $this->this_company_id,
                "employee_id" => $this->request->getPost("employee_id"),
                "start_date" => $this->request->getPost("start_date"),
                "end_date" => $this->request->getPost("end_date"),
                "status" => $this->request->getPost("status"),
                "reason" => $this->request->getPost("reason"),
            ]);

            $response = curl_request("POST", "/attendance/setAttendance", $this->token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => $response["message"],
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Data Gagal Disimpan",
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function update()
    {
        $rules = [
            // "nama_shift" => [
            //     "rules" => "required"
            // ],
            // "SOT" => [
            //     "rules" => "required"
            // ],
            // "EOT" => [
            //     "rules" => "required"
            // ],
            // "BSOT" => [
            //     "rules" => "required"
            // ],
            // "BEOT" => [
            //     "rules" => "required"
            // ],
        ];


        if ($this->validate($rules)) {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                // "nama_shift" => $this->request->getPost("nama_shift"),
                // "SOT" => $this->request->getPost("SOT"),
                // "EOT" => $this->request->getPost("EOT"),
                // "BSOT" => $this->request->getPost("BSOT"),
                // "BEOT" => $this->request->getPost("BEOT"),
            ]);

            $response = curl_request("PATCH", "/perijinan/$id", $this->token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    // "id" => json_decode($response["body"])->createdId,
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Data Gagal Diubah",
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }
}
