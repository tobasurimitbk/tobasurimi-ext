<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;

use DateTime;

class User extends BaseController
{
    protected $company_role;
    protected $token;
    protected $this_company_id;
    protected $session;

    public function __construct()
    {
        $this->company_role = session()->get("login")->company_role;
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->session = session()->get("login");
    }

    public function changeCompany()
    {
        $id = $this->request->getGet("id");
        $name = $this->request->getGet("name");
        $role_id = $this->request->getGet("role_id");
        $role_name = $this->request->getGet("role_name");

        if ($id) {
            $this_access = "";

            foreach ($this->company_role as $item) {
                if ($item->company_id == $id) {
                    $this_access = $item->access_list;
                }
            }

            $this->session->this_company_id = $id;
            $this->session->this_company = $name;
            $this->session->this_role_id = $role_id;
            $this->session->this_role_name = $role_name;
            $this->session->this_access = $this_access;

            session()->setTempdata("login", $this->session, 36000);

            $data = [
                "status"            => true
            ];
            echo json_encode($data);
        } else {
            return redirect()->to("/dashboard");
        }
        return;
    }

    public function user()
    {
        return view('Setting/user/index');
    }

    public function allUser()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this->this_company_id
        ];

        $response = curl_request("GET", "/users", $this->token, $payload);
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
                    "employeeName" => $data->employeeName,
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

    public function saveUser()
    {
        $rules = [
            "name" => [
                "rules" => "required"
            ],
            "username" => [
                "rules" => "required"
            ],
            "password" => [
                "rules" => "required"
            ],
            "employee_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = json_encode([
                "company_id" => $this->this_company_id,
                "name" => $this->request->getPost("name"),
                "username" => $this->request->getPost("username"),
                "password" => $this->request->getPost("password"),
                "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT"),
                "company_role" => json_decode(stripslashes($this->request->getPost("company_role"))),
                "status" => $this->request->getPost("status")
            ]);

            $response = curl_request("POST", "/users", $this->token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
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
        return;
    }

    public function updateUser()
    {
        $rules = [
            "name" => [
                "rules" => "required"
            ],
            "username" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $id = $this->request->getPost("id");

            if ($this->request->getPost("employee_id")) {
                $payload = json_encode([
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("name"),
                    "username" => $this->request->getPost("username"),
                    "password" => $this->request->getPost("password"),
                    "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT"),
                    "company_role" => json_decode(stripslashes($this->request->getPost("company_role"))),
                    "status" => $this->request->getPost("status")
                ]);
            } else {
                $payload = json_encode([
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("name"),
                    "username" => $this->request->getPost("username"),
                    "password" => $this->request->getPost("password"),
                    "company_role" => json_decode(stripslashes($this->request->getPost("company_role"))),
                    "status" => $this->request->getPost("status")
                ]);
            }


            $response = curl_request("PATCH", "/users/$id", $this->token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
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
        return;
    }

    public function getByIdUser($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/users/$id?idCompany=$this->this_company_id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditemukan';
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

    public function deleteUser()
    {
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/users/$id", $this->token);
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
        return;
    }

    public function dropdownUser()
    {
        $responseUser = curl_request("GET", "/users/selectOption", $this->token);

        $dataUser = [];
        if ($responseUser["code"] === 200) {
            $dataUser = json_decode($responseUser["body"])->data;
        }

        $data = [
            "data" => $dataUser
        ];

        echo json_encode($data);
        return;
    }
}
