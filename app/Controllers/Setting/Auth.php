<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;

use DateTime;

class Auth extends BaseController
{

    public function __construct()
    {

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
        try{
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
        catch(\Exception $e)
        {
            return redirect()->back()->with("errors",  $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    public function doLogout()
    {
        session()->destroy();

        return redirect()->to("/");
    }
}
