<?php

namespace App\Controllers;

class Customer extends BaseController
{

    public function __construct()
    {
    }

    public function customer()
    {
        $token = session()->get("login")->token;
        //Get User
        $responseCustomers = curl_request("GET", "/customers", $token);

        $dataCustomers = [];
        if ($responseCustomers["code"] === 200) {
            $dataCustomers = json_decode($responseCustomers["body"])->data;
        }

        $data = [
            "dataCustomers" => $dataCustomers,
        ];

        return view('customer/index', $data);
    }

    // public function allEmployee()
    // {
    //     $token = session()->get("login")->token;

    //     $response = curl_request("GET", "/employees", $token);

    //     if ($response["code"] === 200) {
    //         $data = [
    //             "status"            => true,
    //             "data"   => json_decode($response["body"])->data,
    //         ];
    //         echo json_encode($data);
    //     } else {
    //         $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditampilkan';
    //         $data = [
    //             "status"            => false,
    //             "message"    => $message,
    //             "data"   => '',
    //         ];
    //         echo json_encode($data);
    //     }
    // }

    public function saveCustomer()
    {
        $rules = [
            "name" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "province" => [
                "rules" => "required"
            ],
            "city" => [
                "rules" => "required"
            ],
            "zip_code" => [
                "rules" => "required"
            ],
            "phone_no" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;


            $payload = json_encode([
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "province_id" => $this->request->getPost("province"),
                "city_id" => $this->request->getPost("city"),
                "zip_code" => $this->request->getPost("zip_code"),
                "phone" => $this->request->getPost("phone_no"),
                "email" => $this->request->getPost("email"),
            ]);

            $response = curl_request("POST", "/customers", $token, $payload);

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

    // public function updateEmployee()
    // {
    //     $rules = [
    //         "nip" => [
    //             "rules" => "required"
    //         ],
    //         "name" => [
    //             "rules" => "required"
    //         ],
    //         "gender" => [
    //             "rules" => "required"
    //         ],
    //         "dob" => [
    //             "rules" => "required"
    //         ],
    //         "address" => [
    //             "rules" => "required"
    //         ]
    //     ];

    //     if ($this->validate($rules)) {
    //         $payload = '';
    //         $token = session()->get("login")->token;

    //         $id = $this->request->getPost("id");

    //         $file = $this->request->getFile("employeeImg");
    //         if (!empty($file->getName())) 
    //         {
    //             $mime = $file->getMimeType();
    //             if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
    //                 $image = "data:$mime;base64, " . base64_encode(file_get_contents($file));

    //                 $payload = json_encode([
    //                     "employeeImg" => $image,
    //                     "nip" => $this->request->getPost("nip"),
    //                     "name" => $this->request->getPost("name"),
    //                     "gender" => $this->request->getPost("gender"),
    //                     "dob" => $this->request->getPost("dob"),
    //                     "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
    //                     "phone_no" => $this->request->getPost("phone_no"),
    //                     "acc_no" => $this->request->getPost("acc_no"),
    //                     "email" => $this->request->getPost("email"),
    //                     "address" => $this->request->getPost("address"),
    //                     "status" => $this->request->getPost("status")
    //                 ]);
    //             }
    //         }
    //         else
    //         {
    //             $payload = json_encode([
    //                 "employeeImg" => '',
    //                 "nip" => $this->request->getPost("nip"),
    //                 "name" => $this->request->getPost("name"),
    //                 "gender" => $this->request->getPost("gender"),
    //                 "dob" => $this->request->getPost("dob"),
    //                 "division_id" => formatter($this->request->getPost("division_id"), "STR_TO_INT"),
    //                 "phone_no" => $this->request->getPost("phone_no"),
    //                 "acc_no" => $this->request->getPost("acc_no"),
    //                 "email" => $this->request->getPost("email"),
    //                 "address" => $this->request->getPost("address"),
    //                 "status" => $this->request->getPost("status")
    //             ]);
    //         }

    //         if($payload)
    //         {
    //             $response = curl_request("PATCH", "/employees/$id", $token, $payload);

    //             if ($response["code"] === 200) {
    //                 $data = [
    //                     "status"            => true,
    //                     "message"   => "Data Berhasil diubah",
    //                     "payload"   => $payload,
    //                     'token' => csrf_hash()
    //                 ];
    //                 echo json_encode($data);
    //             } else {
    //                 $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
    //                 $data = [
    //                     "status"            => false,
    //                     "message"    => $message,
    //                     "payload"   => $payload,
    //                     'token' => csrf_hash()
    //                 ];
    //                 echo json_encode($data);
    //             }
    //         }
    //         else
    //         {
    //             $data = [
    //                 "status"            => false,
    //                 "message"    => "Format gambar harus bertipe png, jpg, jpeg",
    //                 "payload"   => ''
    //             ];
    //             echo json_encode($data);
    //         }
    //     } else {
    //         $data = [
    //             "status"            => false,
    //             "message"    => "Data Gagal Diubah",
    //         ];
    //         echo json_encode($data);
    //     }
    //     return;
    // }

    // public function getByIdEmployee($id = null)
    // {
    //     $token = session()->get("login")->token;

    //     if (!empty($id)) {
    //         $response = curl_request("GET", "/employees/$id", $token);
    //         if ($response["code"] === 200) {
    //             $data = [
    //                 "status"  => true,
    //                 "data"  => json_decode($response["body"])->data,
    //             ];
    //             echo json_encode($data);
    //         } else {
    //             $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditampilkan';
    //             $data = [
    //                 "status" => false,
    //                 "message"  => $message
    //             ];
    //             echo json_encode($data);
    //         }
    //     } else {
    //         $data = [
    //             "status"            => false,
    //             "message"    => "Tidak Ada Id"
    //         ];
    //         echo json_encode($data);
    //     }
    //     return;
    // }

    // public function deleteEmployee()
    // {
    //     $token = session()->get("login")->token;

    //     $id = $this->request->getPost("id");

    //     if (!empty($id)) {
    //         $response = curl_request("DELETE", "/employees/$id", $token);
    //         if ($response["code"] === 200) {
    //             $data = [
    //                 "status"            => true,
    //                 "message"   => "Data Berhasil dihapus",
    //                 'token' => csrf_hash()
    //             ];
    //             echo json_encode($data);
    //         } else {
    //             $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Dihapus';
    //             $data = [
    //                 "status"            => false,
    //                 "message"    => $message,
    //                 'token' => csrf_hash()
    //             ];
    //             echo json_encode($data);
    //         }
    //     } else {
    //         $data = [
    //             "status"            => false,
    //             "message"    => "Data Gagal Dihapus",
    //             'token' => csrf_hash()
    //         ];
    //         echo json_encode($data);
    //     }
    //     return;
    // }
}
