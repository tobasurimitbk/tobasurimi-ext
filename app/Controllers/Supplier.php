<?php

namespace App\Controllers;

class Supplier extends BaseController
{

    public function __construct()
    {
    }

    public function supplier()
    {
        return view('supplier/index');
    }

    public function allSupplier()
    {
        $token = session()->get("login")->token;
        $this_company_id = session()->get("login")->this_company_id;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "idCompany" => $this_company_id
        ];

        $response = curl_request("GET", "/suppliers", $token, $payload);
        $dataSupplier = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataSupplier, [
                    "id" => $data->id,
                    "kode" => $data->kode,
                    "name" => $data->name,
                    "address" => $data->address,
                    "ap" => $data->ap_id,
                    "ar" => $data->ar_id
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataSupplier,
        ];

        echo json_encode($data);
        return;
    }

    public function saveSupplier()
    {
        $rules = [
            "kode" => [
                "rules" => "required"
            ],
            "name" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "no_npwp" => [
                "rules" => "required"
            ],
            "phone" => [
                "rules" => "required"
            ],
            "contact_person" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
            "no_rekening" => [
                "rules" => "required"
            ],
            "supplier_buyer" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;
            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kode" => $this->request->getPost("kode"),
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "no_npwp" => $this->request->getPost("no_npwp"),
                "phone" => $this->request->getPost("phone"),
                "contact_person" => $this->request->getPost("contact_person"),
                "email" => $this->request->getPost("email"),
                "no_rekening" => $this->request->getPost("no_rekening"),
                "supplier_buyer" => $this->request->getPost("supplier_buyer"),
                "ap_id" => 1,
                "ar_id" => 1,
                "list_address" => json_decode(stripslashes($this->request->getPost("list_address")))
            ]);

            $response = curl_request("POST", "/suppliers", $token, $payload);

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
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateSupplier()
    {
        $rules = [
            "kode" => [
                "rules" => "required"
            ],
            "name" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "no_npwp" => [
                "rules" => "required"
            ],
            "phone" => [
                "rules" => "required"
            ],
            "contact_person" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
            "no_rekening" => [
                "rules" => "required"
            ],
            "supplier_buyer" => [
                "rules" => "required"
            ],
        ];

        if ($this->validate($rules)) {
            $payload = '';
            $token = session()->get("login")->token;
            $this_company_id = session()->get("login")->this_company_id;

            $id = $this->request->getPost("id");

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kode" => $this->request->getPost("kode"),
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "no_npwp" => $this->request->getPost("no_npwp"),
                "phone" => $this->request->getPost("phone"),
                "contact_person" => $this->request->getPost("contact_person"),
                "email" => $this->request->getPost("email"),
                "no_rekening" => $this->request->getPost("no_rekening"),
                "supplier_buyer" => $this->request->getPost("supplier_buyer"),
                "ap_id" => 1,
                "ar_id" => 1,
                "list_address" => json_decode(stripslashes($this->request->getPost("list_address")))
            ]);
        }

        if ($payload) {
            $response = curl_request("PATCH", "/suppliers/$id", $token, $payload);

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
        }

        return;
    }

    public function getByIdSupplier($id = null)
    {
        $token = session()->get("login")->token;
        $this_company_id = session()->get("login")->this_company_id;

        if (!empty($id)) {
            $response = curl_request("GET", "/suppliers/$id?idCompany=$this_company_id", $token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditampilkan';
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

    public function deleteSupplier()
    {
        $token = session()->get("login")->token;

        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/suppliers/$id", $token);
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
