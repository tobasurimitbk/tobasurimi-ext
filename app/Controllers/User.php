<?php

namespace App\Controllers;
use DateTime;

class User extends BaseController
{

    public function __construct()
    {

    }

    public function login()
    {
        if (!empty(is_login())) {
            return redirect()->to("/dashboard");
        }

        return view('login/index');
    }

    public function doLogin()
    {
        $rules = [
            "username" => [
                "rules" => "required"
            ],
            "password" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = "";
            $data = json_encode([
                "username" => $this->request->getPost("username"),
                "password" => $this->request->getPost("password")
            ]);
            $response = curl_request("POST", "/auth/login", $token, $data);

            if ($response["code"] === 200) {
                $data = json_decode($response["body"]);

                $this_company_id = $data->company_role[0]->company_id;
                $this_company = $data->company_role[0]->company_name;
                $this_access = $data->company_role[0]->access_list;

                // token add bearer
                $session = (object) [
                    "isLogin" => true,
                    "token" => $data->token,
                    "name" => $data->name,
                    "username" => $data->username,
                    "company_role" => $data->company_role,
                    "this_company_id" => $this_company_id,
                    "this_company" => $this_company,
                    "this_access" => $this_access,
                    "employee_id" => $data->employee_id,
                    "status" => $data->status
                ];
                session()->setTempdata("login", $session, 36000);

                return redirect()->to("/dashboard")->with("success", "Login Berhasil");
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Login Gagal, Coba Lagi';
                return redirect()->back()->with("errors", $message);
            }
        } else {
            return redirect()->back()->with("errors", "Login Gagal, Coba Lagi");
        }
    }

    public function changeCompany()
    {
        $id = $this->request->getGet("id");
        $name = $this->request->getGet("name");

        if ($id) {

            $company_role = session()->get("login")->company_role;
            $this_access = "";

            foreach($company_role as $item)
            {
                if($item->company_id == $id)
                {
                    $this_access = $item->access_list;
                }
            }

            $session = session()->get("login");

            $session->this_company_id = $id;
            $session->this_company = $name;
            $session->this_access = $this_access;

            session()->setTempdata("login", $session, 36000);

            $data = [
                "status"            => true
            ];
            echo json_encode($data);
        } else {
            return redirect()->to("/dashboard");
        }
        return;
    }

    public function doLogout()
    {
        session()->destroy();

        return redirect()->to("/");
    }

    public function user()
    {
        return view('user/index');
    }

    public function allUser()
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

        $response = curl_request("GET", "/users", $token, $payload);
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

    public function allUserHaveCompany()
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
            $token = session()->get("login")->token;

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "name" => $this->request->getPost("name"),
                "username" => $this->request->getPost("username"),
                "password" => $this->request->getPost("password"),
                "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT"),
                "status" => $this->request->getPost("status")
            ]);
            
            $response = curl_request("POST", "/users", $token, $payload);

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
            ],
            "employee_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;
            $id = $this->request->getPost("id");

            $this_company_id = session()->get("login")->this_company_id;

            if($this->request->getPost("company_role"))
            {
                $payload = json_encode([
                    "company_id" => $this_company_id,
                    "name" => $this->request->getPost("name"),
                    "username" => $this->request->getPost("username"),
                    "password" => $this->request->getPost("password"),
                    "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT"),
                    "status" => $this->request->getPost("status"),
                    "company_role" => json_decode(stripslashes($this->request->getPost("company_role")))
                ]);
            }
            else
            {
                $payload = json_encode([
                    "company_id" => $this_company_id,
                    "name" => $this->request->getPost("name"),
                    "username" => $this->request->getPost("username"),
                    "password" => $this->request->getPost("password"),
                    "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT"),
                    "status" => $this->request->getPost("status")
                ]);
            }
            
            $response = curl_request("PATCH", "/users/$id", $token, $payload);

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
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        if (!empty($id)) {
            $response = curl_request("GET", "/users/$id?idCompany=$this_company_id", $token);
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
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/users/$id", $token);
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
}
