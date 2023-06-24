<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Satuan extends BaseController
{
    protected $token;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
    }

    public function satuan()
    {
        return view('Master/satuan/index');
    }

    public function allSatuan()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $response = curl_request("GET", "/satuan", $this->token, $payload);
        $dataSatuan = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataSatuan, [
                    "id" => $data->id,
                    "kode_satuan" => $data->kode_satuan,
                    "nama_satuan" => $data->nama_satuan
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataSatuan,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSatuan()
    {
        $rules = [
            "kode_satuan" => [
                "rules" => "required"
            ],
            "nama_satuan" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = json_encode([
                "kode_satuan" => $this->request->getPost("kode_satuan"),
                "nama_satuan" => $this->request->getPost("nama_satuan")
            ]);

            $response = curl_request("POST", "/satuan", $this->token, $payload);

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

    public function updateSatuan()
    {
        $rules = [
            "kode_satuan" => [
                "rules" => "required"
            ],
            "nama_satuan" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "kode_satuan" => $this->request->getPost("kode_satuan"),
                "nama_satuan" => $this->request->getPost("nama_satuan")
            ]);

            $response = curl_request("PATCH", "/satuan/$id", $this->token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil disimpan",
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

    public function getByIdSatuan($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/satuan/$id", $this->token);
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

    public function deleteSatuan()
    {
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/satuan/$id", $this->token);
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

    public function dropdownSatuan()
    {
        $responseSatuan = curl_request("GET", "/satuan/all", $this->token);

        $dataSatuan = [];
        if ($responseSatuan["code"] === 200) {
            $dataSatuan = json_decode($responseSatuan["body"])->data;
        }

        $data = [
            "data" => $dataSatuan
        ];

        echo json_encode($data);
        return;
    }
}