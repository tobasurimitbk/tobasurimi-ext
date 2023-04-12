<?php

namespace App\Controllers;

class Employee extends BaseController
{

    public function __construct()
    {

    }

    public function employee()
    {
        $token = session()->get("login")->token;
         //Get User
        $responseEmployee = curl_request("GET", "/employees", $token);

        $dataEmployee = [];
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }
         
        $data = [
            "dataEmployee" => $dataEmployee,
        ];

        return view('employee/index', $data);
    }

    public function saveEmployee()
    {
        $rules = [
            "nip" => [
                "rules" => "required"
            ],
            "name" => [
                "rules" => "required"
            ],
            "gender" => [
                "rules" => "required"
            ],
            "dob" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $file = $this->request->getFile("employeeImg");

            if (empty($file->getName())) 
            {
                $payload = json_encode([
                    "employeeImg" => '',
                    "nip" => $this->request->getPost("nip"),
                    "name" => $this->request->getPost("name"),
                    "gender" => $this->request->getPost("gender"),
                    "dob" => $this->request->getPost("dob"),
                    "phone_no" => formatter($this->request->getPost("phone_no"), "STR_TO_INT"),
                    "acc_no" => formatter($this->request->getPost("acc_no"), "STR_TO_INT"),
                    "email" => $this->request->getPost("email"),
                    "address" => $this->request->getPost("address"),
                    "status" => $this->request->getPost("status")
                ]);

                $response = curl_request("POST", "/employees", $token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload
                    ];
                    echo json_encode($data);
                } else {
                    $message = json_decode($response["body"])->message;
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload
                    ];
                    echo json_encode($data);
                }
            }
            else
            {
                $mime = $file->getMimeType();
                if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                    $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));

                    $payload = json_encode([
                        "employeeImg" => $image,
                        "nip" => $this->request->getPost("nip"),
                        "name" => $this->request->getPost("name"),
                        "gender" => $this->request->getPost("gender"),
                        "dob" => $this->request->getPost("dob"),
                        "phone_no" => formatter($this->request->getPost("phone_no"), "STR_TO_INT"),
                        "acc_no" => formatter($this->request->getPost("acc_no"), "STR_TO_INT"),
                        "email" => $this->request->getPost("email"),
                        "address" => $this->request->getPost("address"),
                        "status" => $this->request->getPost("status")
                    ]);

                    $response = curl_request("POST", "/employees", $token, $payload);

                    if ($response["code"] === 200) {
                        $data = [
                            "status"            => true,
                            "message"   => "Data Berhasil disimpan",
                            "payload"   => $payload
                        ];
                        echo json_encode($data);
                    } else {
                        $message = json_decode($response["body"])->message;
                        $data = [
                            "status"            => false,
                            "message"    => $message,
                            "payload"   => $payload
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
}