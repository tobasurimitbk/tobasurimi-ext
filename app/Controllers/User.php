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

                $session = (object) [
                    "isLogin" => true,
                    "token" => $data->token,
                    "name" => $data->name,
                ];
                session()->setTempdata("login", $session, 36000);

                return redirect()->to("/dashboard")->with("success", "Login Berhasil");
            } else {
                $message = json_decode($response["body"])->message;
                return redirect()->back()->withInput()->with("errors", $message);
            }
        } else {
            return redirect()->back()->withInput()->with("errors", "Login Gagal, Coba Lagi");
        }
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
}
