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

                // token add bearer
                $session = (object) [
                    "isLogin" => true,
                    "token" => $data->token,
                    "name" => $data->name,
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

    public function doLogout()
    {
        session()->destroy();

        return redirect()->to("/");
    }

    public function user()
    {
        $token = session()->get("login")->token;
         //Get Employee
        $responseEmployee = curl_request("GET", "/employees/selectOption", $token);

        $dataEmployee = [];
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

         //Get Role
         $responseRole = curl_request("GET", "/roles/selectOption", $token);

         $dataRole = [];
         if ($responseRole["code"] === 200) {
             $dataRole = json_decode($responseRole["body"])->data;
         }
         
        $data = [
            "dataEmployee" => $dataEmployee,
            "dataRole" => $dataRole
        ];

        return view('user/index', $data);
    }

    public function allUser()
    {
        $token = session()->get("login")->token;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "limit" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search")
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
                    "roleName" => $data->roleName,
                    "status" => $data->status,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataUser,
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
            "role_id" => [
                "rules" => "required"
            ],
            "employee_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $payload = json_encode([
                "name" => $this->request->getPost("name"),
                "username" => $this->request->getPost("username"),
                "password" => $this->request->getPost("password"),
                "role_id" => formatter($this->request->getPost("role_id"), "STR_TO_INT"),
                "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT")
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
            "role_id" => [
                "rules" => "required"
            ],
            "employee_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "name" => $this->request->getPost("name"),
                "username" => $this->request->getPost("username"),
                "password" => $this->request->getPost("password"),
                "role_id" => formatter($this->request->getPost("role_id"), "STR_TO_INT"),
                "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT")
            ]);
            
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
            ];
            echo json_encode($data);
        }
        return;
    }

    public function getByIdUser($id = null)
    {
        $token = session()->get("login")->token;

        if (!empty($id)) {
            $response = curl_request("GET", "/users/$id", $token);
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
