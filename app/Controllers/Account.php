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

        return view('account/index');
    }

    public function dropdownKategoriAccount()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        $responseKelompokAkunKategori = curl_request("GET", "/kategoriAkun/all?idCompany=$this_company_id", $token);

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
        $token = session()->get("login")->token;
        $this_company_id = session()->get("login")->this_company_id;

        $responseKelompokAkunKategori = curl_request("GET", "/headerAkun/all?idCompany=$this_company_id", $token);

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
                "kelompok_id" => formatter($this->request->getPost("kelompok_akun_id_kategori"), "STR_TO_INT"),
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
                "kelompok_id" => formatter($this->request->getPost("kelompok_akun_id_kategori"), "STR_TO_INT"),
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
            "data" => $dataHeader
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
            $token = session()->get("login")->token;

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kategori_id" => formatter($this->request->getPost("category_id_header"), "STR_TO_INT"),
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
            $token = session()->get("login")->token;
            $id = $this->request->getPost("id_header");

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kategori_id" => formatter($this->request->getPost("category_id_header"), "STR_TO_INT"),
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

    public function allSubAccount()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "status" => $this->request->getGet("status"),
            "idCompany" => $this_company_id
        ];

        $response = curl_request("GET", "/subAkun", $token, $payload);
        $dataHeader = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataHeader, [
                    "id" => $data->id,
                    "kategori_id" => $data->kategori_id,
                    "header_id" => $data->header_id,
                    "coa_id" => $data->coa_id,
                    "nama_kategori" => $data->nama_kategori,
                    "no_header" => $data->no_header,
                    "no_sub" => $data->no_sub,
                    "nama_sub" => $data->nama_sub,
                    "nama_header" => $data->nama_header,
                    "status" => $data->status
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
            $token = session()->get("login")->token;

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "header_id" => formatter($this->request->getPost("header_id_sub"), "STR_TO_INT"),
                "kategori_id" => formatter($this->request->getPost("category_id_sub"), "STR_TO_INT"),
                "coa_id" => formatter($this->request->getPost("coa_id_sub"), "STR_TO_INT"),
                "no_sub" => $this->request->getPost("kode_akun_sub"),
                "nama_sub" => $this->request->getPost("nama_akun_sub"),
                "status" => !empty($this->request->getPost("status_sub")) ? "Aktif" : "Void"
            ]);
            
            $response = curl_request("POST", "/subAkun", $token, $payload);

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
            $token = session()->get("login")->token;
            $id = $this->request->getPost("id_sub");

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "header_id" => formatter($this->request->getPost("header_id_sub"), "STR_TO_INT"),
                "kategori_id" => formatter($this->request->getPost("category_id_sub"), "STR_TO_INT"),
                "coa_id" => formatter($this->request->getPost("coa_id_sub"), "STR_TO_INT"),
                "no_sub" => $this->request->getPost("kode_akun_sub"),
                "nama_sub" => $this->request->getPost("nama_akun_sub"),
                "status" => !empty($this->request->getPost("status_sub")) ? "Aktif" : "Void"
            ]);
            
            $response = curl_request("PATCH", "/subAkun/$id", $token, $payload);

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
        $token = session()->get("login")->token;

        $id = $this->request->getPost("id");

        $this_company_id = session()->get("login")->this_company_id;

        $payload = json_encode([
            "company_id" => $this_company_id,
            "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Void"
        ]);
        
        $response = curl_request("PATCH", "/subAkun/$id", $token, $payload);

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
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        if (!empty($id)) {
            $response = curl_request("GET", "/subAkun/$id", $token);
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
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/subAkun/$id", $token);
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