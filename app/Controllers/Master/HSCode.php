<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class HSCode extends BaseController
{
    protected $token;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
    }

    public function hsCode()
    {
        return view('Master/hsCode/index');
    }

    public function allHSCode()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ]; 

        $response = curl_request("GET", "/hscode", $this->token, $payload);
        $dataKodeHS = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataKodeHS, [
                    "no" => $no++,
                    "id" => $data->id,
                    "komoditi" => $data->komoditi,
                    "code" => $data->code,
                    "uraian_barang" => $data->uraian_barang,
                    "satuan_barang" => $data->satuan_barang,
                    "uraian_satuan" => $data->uraian_satuan
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataKodeHS,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveHSCode()
    {
        try{
            $rules = [
                "komoditi" => [
                    "rules" => "required"
                ],
                "code" => [
                    "rules" => "required"
                ],
                "uraian_barang" => [
                    "rules" => "required"
                ],
                "satuan_barang" => [
                    "rules" => "required"
                ],
                "uraian_satuan" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = json_encode([
                    "komoditi" => $this->request->getPost("komoditi"),
                    "code" => $this->request->getPost("code"),
                    "uraian_barang" => $this->request->getPost("uraian_barang"),
                    "satuan_barang" => $this->request->getPost("satuan_barang"),
                    "uraian_satuan" => $this->request->getPost("uraian_satuan")
                ]);

                $response = curl_request("POST", "/hscode", $this->token, $payload);

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
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateHSCode()
    {
        try{
            $rules = [
                "komoditi" => [
                    "rules" => "required"
                ],
                "code" => [
                    "rules" => "required"
                ],
                "uraian_barang" => [
                    "rules" => "required"
                ],
                "satuan_barang" => [
                    "rules" => "required"
                ],
                "uraian_satuan" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = json_encode([
                    "komoditi" => $this->request->getPost("komoditi"),
                    "code" => $this->request->getPost("code"),
                    "uraian_barang" => $this->request->getPost("uraian_barang"),
                    "satuan_barang" => $this->request->getPost("satuan_barang"),
                    "uraian_satuan" => $this->request->getPost("uraian_satuan")
                ]);

                $response = curl_request("PATCH", "/hscode/$id", $this->token, $payload);

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
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function getByIdHSCode($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/hscode/$id", $this->token);
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

    public function deleteHSCode()
    {
        try{
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/hscode/$id", $this->token);
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
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function dropdownHSCode()
    {
        $responseKodeHS = curl_request("GET", "/hscode/all", $this->token);

        $dataKodeHS = [];
        if ($responseKodeHS["code"] === 200) {
            $dataKodeHS = json_decode($responseKodeHS["body"])->data;
        }

        $data = [
            "data" => $dataKodeHS
        ];

        echo json_encode($data);
        return;
    }
}