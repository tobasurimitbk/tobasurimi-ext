<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use Config\Services;
use App\Models\FormPerijinanModel;
use App\Models\EmployeesModel;

class Perijinan extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $FormPerijinanModel;
    protected $EmployeesModel;

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
        $EmployeesModel = new EmployeesModel();

        $data = [
            "status" => ["IJIN", "ALPHA", "CUTI", "SAKIT", "LIBUR"]
        ];

        //Get Employee
        $dataEmployee = $EmployeesModel->getEmployees($this->this_company_id,);

        foreach (array_keys($dataEmployee) as $key) {
            $dataEmployee[$key] = (object)$dataEmployee[$key];
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
            $dataDetail = [];

            if ($resShift["code"] === 200) {
                $dataDetail = json_decode($resShift["body"])->data;
            }

            $data["data"] = $dataDetail;
        };

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
            "year" => $this->request->getGet("year"),
            "month" => $this->request->getGet("month"),
            "date" => $this->request->getGet("date"),
            "idCompany" => $this->this_company_id,
        ];

        $FormPerijinanModel = new FormPerijinanModel();

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $dataEmployee = [];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $FormData = $FormPerijinanModel->getPerijinanList($condition, $addCondition, $limit, $offset);

        foreach ($FormData['data'] as $data) {
            array_push($dataEmployee, [
                "no" => $no++,
                "id" =>  bin2hex($this->encrypter->encrypt($data->employee_id)),
                "employeeName" => $data->employeeName,
                // "employeeNip" => $data->employeeNip,
                // "divisionName" => $data->divisionName,
                "periode" => $data->periode,
                "status" => $data->status,
            ]);
        }


        var_dump($dataEmployee);
        die;

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $FormData['totalData'],
            "recordsFiltered" => $FormData['totalFilterData'],
            "data" => $dataEmployee,
            // "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        try {
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

            $FormPerijinan = new FormPerijinanModel();

            if ($this->validate($rules)) {
                $insertData = [
                    "company_id" => $this->this_company_id,
                    "employee_id" => $this->request->getPost("employee_id"),
                    "start_date" => $this->request->getPost("start_date"),
                    "end_date" => $this->request->getPost("end_date"),
                    "status" => $this->request->getPost("status"),
                    "reason" => $this->request->getPost("reason"),
                    // "is_posted" => !empty($this->request->getPost("is_posted")) ? true : false,
                ];

                $payload = json_encode($insertData);

                $tglAkhir = strtotime($insertData['end_date']);
                $tglAwal = strtotime($insertData['start_date']);

                $jarak = $tglAkhir - $tglAwal;

                $selisih = $jarak / 60 / 60 / 24;

                for ($i = 0; $i < $selisih; $i++) {
                    $insertData['periode'] = date("Y-m-d", $tglAwal + ($i * 3600 * 24));

                    $insert = $FormPerijinan->insert($insertData);
                }

                if ($insert) {
                    $data = [
                        "id" => $insert,
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Data Gagal Disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
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
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function update()
    {
        try {
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
                $id = $this->request->getPost("id");

                $payload = json_encode([
                    "company_id" => $this->this_company_id,
                    "employee_id" => $this->request->getPost("employee_id"),
                    "start_date" => $this->request->getPost("start_date"),
                    "end_date" => $this->request->getPost("end_date"),
                    "status" => $this->request->getPost("status"),
                    "reason" => $this->request->getPost("reason"),
                    "is_posted" => !empty($this->request->getPost("is_posted")) ? true : false,
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
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function delete()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/perijinan/$id", $this->token);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Dihapus';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }
}
