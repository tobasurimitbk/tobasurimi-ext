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
                $message = json_decode($response["body"])->message;
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
         //Get User
        $responseUser = curl_request("GET", "/users", $token);

        $dataUser = [];
        if ($responseUser["code"] === 200) {
            $dataUser = json_decode($responseUser["body"])->data;
        }
         
        $data = [
            "dataUser" => $dataUser,
        ];

        return view('user/index', $data);
    }
}
