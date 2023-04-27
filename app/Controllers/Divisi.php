<?php

namespace App\Controllers;

class Divisi extends BaseController
{

    public function __construct()
    {

    }

    public function divisi()
    {
        $token = session()->get("login")->token;

        //Get Divisi
        $responseDivisi = curl_request("GET", "/divisis/all", $token);

        $dataDivisi = [];
        if ($responseDivisi["code"] === 200) {
            $dataDivisi = json_decode($responseDivisi["body"])->data;
        }

        $data = [
            "dataDivisi" => $dataDivisi,
        ];

        return view('divisi/index', $data);
    }

    public function allDivisi()
    {
        $token = session()->get("login")->token;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search")
        ];

        $response = curl_request("GET", "/divisis", $token, $payload);
        $dataCompany = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataCompany, [
                    "id" => $data->id,
                    "divisi" => $data->divisi,
                    "libur" => $data->libur,
                    "jam_kerja" => $data->jam_kerja,
                    "jam_istirahat" => $data->jam_istirahat,
                    "jam_masuk" => $data->jam_masuk,
                    "jam_pulang" => $data->jam_pulang,
                    "mulai_istirahat" => $data->mulai_istirahat,
                    "selesai_istirahat" => $data->selesai_istirahat,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataCompany
        ];

        echo json_encode($data);
        return;
    }

    public function saveDivisi()
    {
        $rules = [
            "divisi" => [
                "rules" => "required"
            ],
            "libur" => [
                "rules" => "required"
            ],
            "jam_kerja" => [
                "rules" => "required"
            ],
            "jam_istirahat" => [
                "rules" => "required"
            ],
            "jam_masuk" => [
                "rules" => "required"
            ],
            "jam_pulang" => [
                "rules" => "required"
            ],
            "mulai_istirahat" => [
                "rules" => "required"
            ],
            "selesai_istirahat" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $payload = json_encode([
                "divisi" => $this->request->getPost("divisi"),
                "libur" => $this->request->getPost("libur"),
                "jam_kerja" => $this->request->getPost("jam_kerja"),
                "jam_istirahat" => $this->request->getPost("jam_istirahat"),
                "jam_masuk" => $this->request->getPost("jam_masuk"),
                "jam_pulang" => $this->request->getPost("jam_pulang"),
                "mulai_istirahat" => $this->request->getPost("mulai_istirahat"),
                "selesai_istirahat" => $this->request->getPost("selesai_istirahat")
            ]);
            
            $response = curl_request("POST", "/divisis", $token, $payload);

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

    public function updateDivisi()
    {
        $rules = [
            "divisi" => [
                "rules" => "required"
            ],
            "libur" => [
                "rules" => "required"
            ],
            "jam_kerja" => [
                "rules" => "required"
            ],
            "jam_istirahat" => [
                "rules" => "required"
            ],
            "jam_masuk" => [
                "rules" => "required"
            ],
            "jam_pulang" => [
                "rules" => "required"
            ],
            "mulai_istirahat" => [
                "rules" => "required"
            ],
            "selesai_istirahat" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "divisi" => $this->request->getPost("divisi"),
                "libur" => $this->request->getPost("libur"),
                "jam_kerja" => $this->request->getPost("jam_kerja"),
                "jam_istirahat" => $this->request->getPost("jam_istirahat"),
                "jam_masuk" => $this->request->getPost("jam_masuk"),
                "jam_pulang" => $this->request->getPost("jam_pulang"),
                "mulai_istirahat" => $this->request->getPost("mulai_istirahat"),
                "selesai_istirahat" => $this->request->getPost("selesai_istirahat")
            ]);
            
            $response = curl_request("PATCH", "/divisis/$id", $token, $payload);

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
            ];
            echo json_encode($data);
        }
        return;
    }

    public function getByIdDivisi($id = null)
    {
        $token = session()->get("login")->token;

        if (!empty($id)) {
            $response = curl_request("GET", "/divisis/$id", $token);
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

    public function deleteDivisi()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/divisis/$id", $token);
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