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

    public function getById($id)
    {
        $EmployeesModel = new EmployeesModel();

        $data = [
            "status" => ["HADIR", "IJIN", "ALPHA", "CUTI", "SAKIT", "LIBUR"]
        ];

        $dataEmployee = $EmployeesModel->getEmployees($this->this_company_id,);

        foreach (array_keys($dataEmployee) as $key) {
            $dataEmployee[$key] = (object)$dataEmployee[$key];
        }

        $data["dataEmployee"] = $dataEmployee;

        if (!empty($id)) {
            $FormPerijinanModel = new FormPerijinanModel();

            $dataPerijinan = $FormPerijinanModel->getPerijinanById($id);

            $data["data"] = $dataPerijinan;
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
            "dateStart"     => $this->request->getGet("dateStart"),
            "dateEnd"       => $this->request->getGet("dateEnd"),
        ];

        $dataEmployee = [];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $FormData = $FormPerijinanModel->getPerijinanList($condition, $addCondition, $limit, $offset);

        foreach ($FormData['data'] as $data) {
            array_push($dataEmployee, [
                "no" => $no++,
                "id" =>  $data->id,
                "employeeName" => $data->employeeName,
                "employeeNip" => $data->employeeNip,
                "divisionName" => $data->divisionName,
                "periode" => $data->periode,
                "status" => $data->status,
            ]);
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $FormData['totalData'],
            "recordsFiltered" => $FormData['totalFilteredData'],
            "data" => $dataEmployee,
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
                "status" => [
                    "rules" => "required"
                ],
                "reason" => [
                    "rules" => "required"
                ],
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = [
                    "status" => $this->request->getPost("status"),
                    "reason" => $this->request->getPost("reason")
                ];

                $FormPerijinanModel = new FormPerijinanModel();
                $FormPerijinanModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Diubah",
                    'token'     => csrf_hash()
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
