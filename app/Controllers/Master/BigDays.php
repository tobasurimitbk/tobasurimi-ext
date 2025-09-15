<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\BigDaysModel;

class BigDays extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $BigDaysModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->BigDaysModel = new BigDaysModel();
    }

    public function ListBigDay()
    {
        return view('Master/BigDay/index');
    }

    public function allBigDay()
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
            "year"          => $this->request->getGet("year"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "search"        => $this->request->getGet('search')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $res = $this->BigDaysModel->getBigDayList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "date"                  => date("d/m/Y", strtotime($data->date)),
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

    public function saveBigDay()
    {
        $name = $this->request->getPost("nama");
        $company_id = $this->this_company_id;

        $getYear = date("Y", strtotime(str_replace("/", "-", $this->request->getPost("date_create"))));


        $getNameNull = $this->BigDaysModel->select('id')
            ->where('name', $name)
            ->where('YEAR(date)', $getYear)
            ->where('company_id', $company_id)
            ->where('deletedAt', null)
            ->findAll();


        //cek nama duplikatnya sama yang ada? jika ada is_unique, jika tidak ada lolosin
        if (!empty($getNameNull)) {
            $rule_is_unique = 'required|is_unique[big_days.name]';
        } else {
            $rule_is_unique = 'required';
        }

        try {
            $rules = [
                "date_create" => [
                    "rules" => "required"
                ],
                "nama" => [
                    "rules" => $rule_is_unique,
                    'errors' => [
                        'required' => 'Nama harus diisi',
                        'is_unique' => 'Nama sudah ada'
                    ]
                ]

            ];

            if ($this->validate($rules)) {
                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("nama"),
                    "date" => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("date_create"))))
                ];
                if ($this->BigDaysModel->insert($values)) {
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
                $errors = '';
                foreach ($this->validator->getErrors() as $key => $row) {
                    $errors .= $row . '. ';
                }
                $data = [
                    "status"     => false,
                    "message"    => $errors,
                    'token'      => csrf_hash()
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

    public function updateBigDay()
    {
        $id = $this->request->getPost("id");
        $company_id = $this->this_company_id;
        $name = $this->request->getPost("nama");

        $getYear = date("Y", strtotime(str_replace("/", "-", $this->request->getPost("date_create"))));

        $getNameNull = $this->BigDaysModel->select('id')
            ->where('name', $name)
            ->where('YEAR(date)', $getYear)
            ->where('company_id', $company_id)
            ->where('deletedAt', null)
            ->where('id !=', $id)
            ->findAll();

        //cek name duplikatnya sama yang ada? jika ada is_unique, jika tidak ada lolosin
        if (!empty($getNameNull)) {

            //cek name yg diedit masih sama dengan yg di ID?
            $getNameNow = $this->BigDaysModel->select('id')
                ->where('name', $name)
                ->where('YEAR(date)', $getYear)
                ->where('company_id', $company_id)
                ->where('deletedAt', null)
                ->where('id', $id)
                ->first();

            //jika sama
            if (!empty($getNameNow)) {
                $rule_is_unique = 'required';
            } else {
                $rule_is_unique = 'required|is_unique[big_days.name]';
            }
        } else {
            $rule_is_unique = 'required';
        }

        try {
            $rules = [
                "date_create" => [
                    "rules" => "required"
                ],
                "nama" => [
                    "rules" => $rule_is_unique,
                    'errors' => [
                        'required' => 'Nama harus diisi',
                        'is_unique' => 'Nama sudah ada'
                    ]
                ]
            ];

            if ($this->validate($rules)) {



                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("nama"),
                    "date" => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("date_create"))))
                ];

                if ($this->BigDaysModel->update($id, $values)) {
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
                $errors = '';
                foreach ($this->validator->getErrors() as $key => $row) {
                    $errors .= $row . '. ';
                }
                $data = [
                    "status"     => false,
                    "message"    => $errors,
                    'token'      => csrf_hash()
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
            $res = $this->BigDaysModel->getById($id);
            if ($res) {
                $res->date = date("d/m/Y", strtotime($res->date));
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

    public function deleteBigDay()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->BigDaysModel->update($id, $values)) {
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

    public function dropdownBigDay()
    {
        $dataDivisi = $this->BigDaysModel->get_by_company_id($this->this_company_id);

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
