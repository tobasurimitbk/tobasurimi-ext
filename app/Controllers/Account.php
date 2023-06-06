<?php

namespace App\Controllers;

class Account extends BaseController
{

    public function __construct()
    {
    }

    public function account()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        //Get Kelompok Akun For Kategori by Metadata
        $responseKelompokAkun = curl_request("GET", "/metadata/all", $token);

        $dataKelompokAkun = [];
        if ($responseKelompokAkun["code"] === 200) {
            $dataKelompokAkun = json_decode($responseKelompokAkun["body"])->data;
        }

        //Get Kelompok Akun For Header
        $responseKelompokAkunKategori = curl_request("GET", "/kategoriAkun/all?idCompany=$this_company_id", $token);

        $dataKelompokAkunKategori = [];
        if ($responseKelompokAkunKategori["code"] === 200) {
            $dataKelompokAkunKategori = json_decode($responseKelompokAkunKategori["body"])->data;
        }

        $data = [
            "dataKelompokAkun" => $dataKelompokAkun,
            "dataKelompokAkunKategori" => $dataKelompokAkunKategori
        ];

        return view('account/index', $data);
    }

    public function allKategoriAccount()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "idCompany" => $this_company_id
        ];

        $response = curl_request("GET", "/kategoriAkun", $token, $payload);
        $dataKategori = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataKategori, [
                    "id" => $data->id,
                    "kategori_akun" => $data->kategori_akun,
                    "no_kategori" => $data->no_kategori,
                    "nama_kategori" => $data->nama_kategori
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataKategori
        ];

        echo json_encode($data);
        return;
    }

    public function saveKategoriAccount()
    {
        $rules = [
            "kelompok_akun_id_kategori" => [
                "rules" => "required"
            ],
            "kode_akun_kategori" => [
                "rules" => "required"
            ],
            "nama_akun_kategori" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kategori_akun" => $this->request->getPost("kelompok_akun_id_kategori"),
                "no_kategori" => $this->request->getPost("kode_akun_kategori"),
                "nama_kategori" => $this->request->getPost("nama_akun_kategori"),
            ]);
            
            $response = curl_request("POST", "/kategoriAkun", $token, $payload);

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

    public function updateKategoriAccount()
    {
        $rules = [
            "kelompok_akun_id_kategori" => [
                "rules" => "required"
            ],
            "kode_akun_kategori" => [
                "rules" => "required"
            ],
            "nama_akun_kategori" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;
            $id = $this->request->getPost("id_kategori");

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kategori_akun" => $this->request->getPost("kelompok_akun_id_kategori"),
                "no_kategori" => $this->request->getPost("kode_akun_kategori"),
                "nama_kategori" => $this->request->getPost("nama_akun_kategori"),
            ]);
            
            $response = curl_request("PATCH", "/kategoriAkun/$id", $token, $payload);

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

    public function getByIdKategoriAccount($id = null)
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        if (!empty($id)) {
            $response = curl_request("GET", "/kategoriAkun/$id", $token);
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

    public function deleteKategoriAccount()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/kategoriAkun/$id", $token);
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

    public function allHeaderAccount()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "idCompany" => $this_company_id
        ];

        $response = curl_request("GET", "/headerAkun", $token, $payload);
        $dataHeader = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataHeader, [
                    "id" => $data->id,
                    "kategori_akun" => "",
                    "no_header" => $data->no_header,
                    "nama_header" => $data->nama_header
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataHeader
        ];

        echo json_encode($data);
        return;
    }

    public function saveHeaderAccount()
    {
        $rules = [
            "kelompok_akun_id_header" => [
                "rules" => "required"
            ],
            "kode_akun_header" => [
                "rules" => "required"
            ],
            "nama_akun_header" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kategori_id" => $this->request->getPost("kelompok_akun_id_header"),
                "no_header" => $this->request->getPost("kode_akun_header"),
                "nama_header" => $this->request->getPost("nama_akun_header"),
            ]);
            
            $response = curl_request("POST", "/headerAkun", $token, $payload);

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

    public function updateHeaderAccount()
    {
        $rules = [
            "kelompok_akun_id_header" => [
                "rules" => "required"
            ],
            "kode_akun_header" => [
                "rules" => "required"
            ],
            "nama_akun_header" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;
            $id = $this->request->getPost("id_header");

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kategori_id" => $this->request->getPost("kelompok_akun_id_header"),
                "no_header" => $this->request->getPost("kode_akun_header"),
                "nama_header" => $this->request->getPost("nama_akun_header"),
            ]);
            
            $response = curl_request("PATCH", "/headerAkun/$id", $token, $payload);

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

    public function getByIdHeaderAccount($id = null)
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        if (!empty($id)) {
            $response = curl_request("GET", "/headerAkun/$id", $token);
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

    public function deleteHeaderAccount()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/headerAkun/$id", $token);
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
?>