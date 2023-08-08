<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\AttendancesUnitModel;

class AttendancesUnit extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $AttendancesUnitModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->AttendancesUnitModel = new AttendancesUnitModel();
    }

    public function ListData()
    {
        return view('Master/AttendancesUnit/index');
    }

    public function AllData()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "company_id"    => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $res = $this->AttendancesUnitModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "ip"                    => $data->ip,
                "unit_key"              => $data->unit_key,
                "name"                  => $data->name,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveData()
    {
        try {
            $rules = [
                "nama" => [
                    "rules" => "required"
                ],
                "ip" => [
                    "rules" => "required"
                ],
                "unit_key" => [
                    "rules" => "required"
                ]


            ];

            if ($this->validate($rules)) {
                if ($this->request->getPost("master") == 1) {
                    $this->AttendancesUnitModel->set('master', 0)->where('company_id', $this->this_company_id)->update();
                }

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("nama"),
                    "ip" => $this->request->getPost("ip"),
                    "unit_key" => $this->request->getPost("unit_key"),
                    "master" => $this->request->getPost("master")
                ];
                if ($this->AttendancesUnitModel->insert($values)) {
                    $data = [
                        "status"    => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
                        'token' => csrf_hash()
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

    public function updateData()
    {
        try {
            $rules = [
                "nama" => [
                    "rules" => "required"
                ],
                "ip" => [
                    "rules" => "required"
                ],
                "unit_key" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                if ($this->request->getPost("master") == 1) {
                    $this->AttendancesUnitModel->set('master', 0)->where('company_id', $this->this_company_id)->update();
                }

                $id = $this->request->getPost("id");

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("nama"),
                    "ip" => $this->request->getPost("ip"),
                    "unit_key" => $this->request->getPost("unit_key"),
                    "master" => $this->request->getPost("master")
                ];

                if ($this->AttendancesUnitModel->update($id, $values)) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
                        'token' => csrf_hash()
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

    public function getById($id = null)
    {
        if (!empty($id)) {
            $res = $this->AttendancesUnitModel->getById($id);
            if ($res) {
                $data = [
                    "status"  => true,
                    "data"  => $res,
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditemukan';
                $data = [
                    "status" => false,
                    "message"  => $message
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Tidak Ada Id"
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteData()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->AttendancesUnitModel->update($id, $values)) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Dihapus';
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

    public function dropdownData()
    {
        $dataDivisi = $this->AttendancesUnitModel->get_by_company_id($this->this_company_id);

        /*
        $responseDivisi = curl_request("GET", "/divisis/all", $this->token);
        $dataDivisi = [];
        if ($responseDivisi["code"] === 200) {
            $dataDivisi = json_decode($responseDivisi["body"])->data;
        }
        */

        $data = [
            "data" => $dataDivisi
        ];

        echo json_encode($data);
        return;
    }
}
