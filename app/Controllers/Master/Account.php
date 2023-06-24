<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Account extends BaseController
{
    protected $token;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
    }

    public function account()
    {
        return view('account/index');
    }

    public function dropdownKategoriAccount()
    {
        $responseKelompokAkunKategori = curl_request("GET", "/kategoriAkun/all", $this->token);

        $dataKelompokAkunKategori = [];
        if ($responseKelompokAkunKategori["code"] === 200) {
            $dataKelompokAkunKategori = json_decode($responseKelompokAkunKategori["body"])->data;
        }

        $data = [
            "data" => $dataKelompokAkunKategori
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownHeaderAccount()
    {
        $responseKelompokAkunKategori = curl_request("GET", "/headerAkun/all", $this->token);

        $dataKelompokAkunKategori = [];
        if ($responseKelompokAkunKategori["code"] === 200) {
            $dataKelompokAkunKategori = json_decode($responseKelompokAkunKategori["body"])->data;
        }

        $data = [
            "data" => $dataKelompokAkunKategori
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownSubAccount()
    {
        $responseKelompokAkunSub = curl_request("GET", "/subAkun/all", $this->token);

        $dataKelompokAkunSub = [];
        if ($responseKelompokAkunSub["code"] === 200) {
            $dataKelompokAkunSub = json_decode($responseKelompokAkunSub["body"])->data;
        }

        $data = [
            "data" => $dataKelompokAkunSub
        ];

        echo json_encode($data);
        return;
    }

    public function allKategoriAccount()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $response = curl_request("GET", "/kategoriAkun", $this->token, $payload);
        $dataKategori = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataKategori, [
                    "id" => $data->id,
                    "kelompok_akun" => $data->kelompok_akun,
                    "no_kategori" => $data->no_kategori,
                    "nama_kategori" => $data->nama_kategori
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataKategori,
            "response" => $response,
            "payload" => $payload
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
            $payload = json_encode([
                "kelompok_id" => formatter($this->request->getPost("kelompok_akun_id_kategori"), "STR_TO_INT"),
                "no_kategori" => $this->request->getPost("kode_akun_kategori"),
                "nama_kategori" => $this->request->getPost("nama_akun_kategori"),
            ]);
            
            $response = curl_request("POST", "/kategoriAkun", $this->token, $payload);

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
            $id = $this->request->getPost("id_kategori");

            $payload = json_encode([
                "kelompok_id" => formatter($this->request->getPost("kelompok_akun_id_kategori"), "STR_TO_INT"),
                "no_kategori" => $this->request->getPost("kode_akun_kategori"),
                "nama_kategori" => $this->request->getPost("nama_akun_kategori"),
            ]);
            
            $response = curl_request("PATCH", "/kategoriAkun/$id", $this->token, $payload);

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
        if (!empty($id)) {
            $response = curl_request("GET", "/kategoriAkun/$id", $this->token);
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
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/kategoriAkun/$id", $this->token);
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
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $response = curl_request("GET", "/headerAkun", $this->token, $payload);
        $dataHeader = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataHeader, [
                    "id" => $data->id,
                    "nama_kategori" => $data->nama_kategori,
                    "no_header" => $data->no_header,
                    "nama_header" => $data->nama_header
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataHeader,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveHeaderAccount()
    {
        $rules = [
            "category_id_header" => [
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
            $payload = json_encode([
                "kategori_id" => formatter($this->request->getPost("category_id_header"), "STR_TO_INT"),
                "no_header" => $this->request->getPost("kode_akun_header"),
                "nama_header" => $this->request->getPost("nama_akun_header"),
            ]);
            
            $response = curl_request("POST", "/headerAkun", $this->token, $payload);

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
            "category_id_header" => [
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
            $id = $this->request->getPost("id_header");

            $payload = json_encode([
                "kategori_id" => formatter($this->request->getPost("category_id_header"), "STR_TO_INT"),
                "no_header" => $this->request->getPost("kode_akun_header"),
                "nama_header" => $this->request->getPost("nama_akun_header"),
            ]);
            
            $response = curl_request("PATCH", "/headerAkun/$id", $this->token, $payload);

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
        if (!empty($id)) {
            $response = curl_request("GET", "/headerAkun/$id", $this->token);
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
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/headerAkun/$id", $this->token);
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

    public function allSubAccount()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "status" => $this->request->getGet("status"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $response = curl_request("GET", "/subAkun", $this->token, $payload);
        $dataSub = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataSub, [
                    "id" => $data->id,
                    "kategori_id" => $data->kategori_id,
                    "header_id" => $data->header_id,
                    "coa_id" => $data->coa_id,
                    "nama_kategori" => $data->nama_kategori,
                    "no_header" => $data->no_header,
                    "no_sub" => $data->no_sub,
                    "nama_sub" => $data->nama_sub,
                    "nama_header" => $data->nama_header,
                    "akun_coa" => $data->akun_coa,
                    "status" => $data->status
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataSub,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSubAccount()
    {
        $rules = [
            "header_id_sub" => [
                "rules" => "required"
            ],
            "coa_id_sub" => [
                "rules" => "required"
            ],
            "kode_akun_sub" => [
                "rules" => "required"
            ],
            "nama_akun_sub" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = json_encode([
                "header_id" => formatter($this->request->getPost("header_id_sub"), "STR_TO_INT"),
                "kategori_id" => formatter($this->request->getPost("category_id_sub"), "STR_TO_INT"),
                "coa_id" => formatter($this->request->getPost("coa_id_sub"), "STR_TO_INT"),
                "no_sub" => $this->request->getPost("kode_akun_sub"),
                "nama_sub" => $this->request->getPost("nama_akun_sub"),
                "status" => !empty($this->request->getPost("status_sub")) ? "Aktif" : "Void"
            ]);
            
            $response = curl_request("POST", "/subAkun", $this->token, $payload);

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

    public function updateSubAccount()
    {
        $rules = [
            "header_id_sub" => [
                "rules" => "required"
            ],
            "coa_id_sub" => [
                "rules" => "required"
            ],
            "kode_akun_sub" => [
                "rules" => "required"
            ],
            "nama_akun_sub" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $id = $this->request->getPost("id_sub");

            $payload = json_encode([
                "header_id" => formatter($this->request->getPost("header_id_sub"), "STR_TO_INT"),
                "kategori_id" => formatter($this->request->getPost("category_id_sub"), "STR_TO_INT"),
                "coa_id" => formatter($this->request->getPost("coa_id_sub"), "STR_TO_INT"),
                "no_sub" => $this->request->getPost("kode_akun_sub"),
                "nama_sub" => $this->request->getPost("nama_akun_sub"),
                "status" => !empty($this->request->getPost("status_sub")) ? "Aktif" : "Void"
            ]);
            
            $response = curl_request("PATCH", "/subAkun/$id", $this->token, $payload);

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

    public function updateStatusSubAccount()
    {
        $id = $this->request->getPost("id");

        $payload = json_encode([
            "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Void"
        ]);
        
        $response = curl_request("PATCH", "/subAkun/$id", $this->token, $payload);

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
        return;
    }

    public function getByIdSubAccount($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/subAkun/$id", $this->token);
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

    public function deleteSubAccount()
    {
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/subAkun/$id", $this->token);
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