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
                "uname" => $this->request->getPost("username"),
                "passwd" => $this->request->getPost("password")
            ]);
            $response = curl_request("POST", "/user/login", $token, $data);

            if ($response["code"] === 200) {
                $data = json_decode($response["body"]);
                $expired = date("Y-m-d H:i:s", strtotime($data->expires));

                $session = (object) [
                    "isLogin" => true,
                    "id" => $data->id,
                    "token" => $data->token,
                    "expired" => $expired,
                    "uname" => $data->uname,
                    "name" => $data->name,
                    "akses" => $data->akses,
                    "id_role" => $data->id_role,
                    "role" => $data->role
                ];

                //cara 2
                $date1 = new DateTime($expired);
                $now = new DateTime();

                $difference_in_seconds = $date1->format('U') - $now->format('U');

                //cara 1
                //$sessionExpired = date_diff(new DateTime($expired), new DateTime())->h * 60 * 60;

                //session()->setTempdata("login", $session);
                session()->setTempdata("login", $session, $difference_in_seconds);

                return redirect()->to("/dashboard")->with("success", "Login Berhasil");
            } else {

                return redirect()->back()->withInput();
            }
        } else {
            return redirect()->back()->withInput();
        }
    }

    public function user()
    {
        return view('user/index');
    }
}
