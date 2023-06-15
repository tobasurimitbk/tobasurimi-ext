<?php

namespace App\Controllers;

class SPP extends BaseController
{

    public function __construct()
    {
    }

    public function spp()
    {
        return view('spp/index');
    }

    public function saveSPP()
    {
        $rules = [
            "request_date" => [
                "rules" => "required"
            ],
            "spp_type" => [
                "rules" => "required"
            ],
            "spp_no" => [
                "rules" => "required"
            ],
            "po_type" => [
                "rules" => "required"
            ],
            "warehouse_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $payload = json_encode([
                "request_date" => $this->request->getPost("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("request_date")))) : "",
                "spp_type" => $this->request->getPost("spp_type"),
                "spp_no" => $this->request->getPost("spp_no"),
                "po_type" => $this->request->getPost("po_type"),
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                "note" => $this->request->getPost("note"),
                "items" => json_decode(stripslashes($this->request->getPost("items")))
            ]);
            
            $response = curl_request("POST", "/purchaseRequest", $token, $payload);

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

    public function updateSPP()
    {
        $rules = [
            "request_date" => [
                "rules" => "required"
            ],
            "spp_type" => [
                "rules" => "required"
            ],
            "spp_no" => [
                "rules" => "required"
            ],
            "po_type" => [
                "rules" => "required"
            ],
            "warehouse_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $id = $this->request->getPost("id");

            $payload = json_encode([
                "request_date" => $this->request->getPost("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("request_date")))) : "",
                "spp_type" => $this->request->getPost("spp_type"),
                "spp_no" => $this->request->getPost("spp_no"),
                "po_type" => $this->request->getPost("po_type"),
                "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                "note" => $this->request->getPost("note"),
                "items" => json_decode(stripslashes($this->request->getPost("items")))
            ]);

            $response = curl_request("PATCH", "/purchaseRequest/$id", $token, $payload);

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
        } else {
            $data = [
                "status"            => false,
                "message"    => "Data Gagal Diubah",
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function getByIdSPP($id = null)
    {
        $token = session()->get("login")->token;

        if (!empty($id)) {
            $response = curl_request("GET", "/purchaseRequest/$id", $token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditemukan';
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

    public function deleteSPP()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/purchaseRequest/$id", $token);
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