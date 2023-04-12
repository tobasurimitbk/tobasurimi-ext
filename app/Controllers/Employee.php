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

         //Get Divisi
        $responseDivisi = curl_request("GET", "/divisis/all", $token);

        $dataDivisi = [];
        if ($responseDivisi["code"] === 200) {
            $dataDivisi = json_decode($responseDivisi["body"])->data;
        }
         
        $data = [
            "dataEmployee" => $dataEmployee,
            "dataDivisi" => $dataDivisi,
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
            $payload = '';
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
                    "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
                    "phone_no" => $this->request->getPost("phone_no"),
                    "acc_no" => $this->request->getPost("acc_no"),
                    "email" => $this->request->getPost("email"),
                    "address" => $this->request->getPost("address"),
                    "status" => $this->request->getPost("status")
                ]);
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
                        "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
                        "phone_no" => $this->request->getPost("phone_no"),
                        "acc_no" => $this->request->getPost("acc_no"),
                        "email" => $this->request->getPost("email"),
                        "address" => $this->request->getPost("address"),
                        "status" => $this->request->getPost("status")
                    ]);
                }
            }

            if($payload)
            {
                $response = curl_request("POST", "/employees", $token, $payload);

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
}