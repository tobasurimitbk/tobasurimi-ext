<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use Config\Services;

class Shift extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
    }

    public function shift()
    {
        return view('Master/shift/index');
    }

    public function createView()
    {
        return view('Master/shift/form');
    }

    public function allShift()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $response = curl_request("GET", "/shift", $this->token, $payload);
        $dataShift = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataShift, [
                    "no" => $no++,
                    "id" =>  bin2hex($this->encrypter->encrypt($data->id)),
                    "nama_shift" => $data->nama_shift,
                    "sot" => $data->SOT,
                    "eot" => $data->EOT,
                    "bsot" => $data->BSOT,
                    "beot" => $data->BEOT,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataShift,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        try{
            $rules = [
                "nama_shift" => [
                    "rules" => "required"
                ],
                "SOT" => [
                    "rules" => "required"
                ],
                "EOT" => [
                    "rules" => "required"
                ],
                "BSOT" => [
                    "rules" => "required"
                ],
                "BEOT" => [
                    "rules" => "required"
                ],
            ];


            if ($this->validate($rules)) {
                $payload = json_encode([
                    "nama_shift" => $this->request->getPost("nama_shift"),
                    "SOT" => $this->request->getPost("SOT"),
                    "EOT" => $this->request->getPost("EOT"),
                    "BSOT" => $this->request->getPost("BSOT"),
                    "BEOT" => $this->request->getPost("BEOT"),
                ]);

                $response = curl_request("POST", "/shift", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => $response["message"],
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        'code' => $response["code"]
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        'code' => $response["code"]
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

    public function getById($id = null)
    {
        if (!empty($id)) {
            $id = $this->encrypter->decrypt(hex2bin($id));

            $resShift = curl_request("GET", "/shift/$id", $this->token);
            $dataShift = [];

            if ($resShift["code"] === 200) {
                $dataShift = json_decode($resShift["body"])->data;
            }

            $data = [
                "data" => $dataShift
            ];
        }

        return view('Master/shift/form', $data);
    }

    public function update()
    {
        try{
            $rules = [
                "nama_shift" => [
                    "rules" => "required"
                ],
                "SOT" => [
                    "rules" => "required"
                ],
                "EOT" => [
                    "rules" => "required"
                ],
                "BSOT" => [
                    "rules" => "required"
                ],
                "BEOT" => [
                    "rules" => "required"
                ],
            ];


            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = json_encode([
                    "nama_shift" => $this->request->getPost("nama_shift"),
                    "SOT" => $this->request->getPost("SOT"),
                    "EOT" => $this->request->getPost("EOT"),
                    "BSOT" => $this->request->getPost("BSOT"),
                    "BEOT" => $this->request->getPost("BEOT"),
                ]);

                $response = curl_request("PATCH", "/shift/$id", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        // "id" => json_decode($response["body"])->createdId,
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        'code' => $response["code"]
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        'code' => $response["code"]
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

    public function dropdownShift()
    {
        $responseShift = curl_request("GET", "/shift/all", $this->token);

        $dataShift = [];
        if ($responseShift["code"] === 200) {
            $dataShift = json_decode($responseShift["body"])->data;
        }

        $data = [
            "data" => $dataShift
        ];

        echo json_encode($data);
        return;
    }
}
