<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CompaniesModel;
use App\Models\RolesModel;
use App\Models\AccessListsModel;

use DateTime;

class Auth extends BaseController
{
    protected $userModel;
    protected $CompaniesModel;
    protected $RolesModel;
    protected $AccessListsModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->CompaniesModel = new CompaniesModel();
        $this->RolesModel = new RolesModel();
        $this->AccessListsModel = new AccessListsModel();
    }

    public function login()
    {
        if (!empty(is_login())) {
            return redirect()->to("/dashboard");
        }

        return view('Setting/login/index');
    }

    public function doLogin()
    {
        try {
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
                    $this_role_id = $data->company_role[0]->role_id;
                    $this_role_name = $data->company_role[0]->role_name;

                    // token add bearer
                    $session = (object) [
                        "isLogin" => true,
                        "token" => $data->token,
                        "name" => $data->name,
                        "username" => $data->username,
                        "this_role_id" => $this_role_id,
                        "this_role_name" => $this_role_name,
                        "company_role" => $data->company_role,
                        "this_company_id" => $this_company_id,
                        "this_company" => $this_company,
                        "this_access" => $this_access,
                        "user_id" => $data->id,
                        "employee_id" => $data->employee_id,
                        "status" => $data->status
                    ];
                    session()->setTempdata("login", $session, 36000);

                    $data = [
                        "status"            => true,
                        "message"   => "Berhasil Login",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Login Gagal, Coba Lagi';
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
                    "message"    => "Gagal Login, Coba Lagi",
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

    public function doLogout()
    {
        session()->destroy();

        return redirect()->to("/");
    }
}
