<?php

namespace App\Controllers;

class Role extends BaseController
{

    public function __construct()
    {

    }

    public function role()
    {
        $token = session()->get("login")->token;
         //Get Role
         $responseRole = curl_request("GET", "/roles", $token);

         $dataRole = [];
         if ($responseRole["code"] === 200) {
             $dataRole = json_decode($responseRole["body"])->data;
         }
         
        $data = [
            "dataRole" => $dataRole
        ];

        return view('role/index', $data);
    }

    public function allRole()
    {
        $token = session()->get("login")->token;

        $response = curl_request("GET", "/roles", $token);

        if ($response["code"] === 200) {
            $data = [
                "status"            => true,
                "data"   => json_decode($response["body"])->data,
            ];
            echo json_encode($data);
        } else {
            $message = json_decode($response["body"])->message;
            $data = [
                "status"            => false,
                "message"    => $message,
                "data"   => '',
            ];
            echo json_encode($data);
        }
    }

    public function saveRole()
    {
        $rules = [
            "name" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $payload = json_encode([
                "name" => $this->request->getPost("name")
            ]);

            if($payload)
            {
                $response = curl_request("POST", "/roles", $token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = json_decode($response["body"])->message;
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            }
            else
            {
                $data = [
                    "status"            => false,
                    "message"    => "Format gambar harus bertipe png, jpg, jpeg",
                    "payload"   => ''
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

    public function updateRole()
    {
        $rules = [
            "name" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "name" => $this->request->getPost("name")
            ]);

            $response = curl_request("PATCH", "/roles/$id", $token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = json_decode($response["body"])->message;
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

    public function getByIdRole($id = null)
    {
        $token = session()->get("login")->token;

        if (!empty($id)) {
            $response = curl_request("GET", "/roles/$id", $token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = json_decode($response["body"])->message;
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

    public function deleteRole()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/roles/$id", $token);
            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = json_decode($response["body"])->message;
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