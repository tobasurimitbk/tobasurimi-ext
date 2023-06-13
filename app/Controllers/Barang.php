<?php

namespace App\Controllers;

class Barang extends BaseController
{

    public function __construct()
    {
    }

    public function barang()
    {
        $token = session()->get("login")->token;

        // Get Kategori
        $responseKategori = curl_request("GET", "/metadata/all?name=kategori_barang", $token);

        $dataKategori = [];
        if ($responseKategori["code"] === 200) {
            $dataKategori = json_decode($responseKategori["body"])->data;
        }
         
        $data = [
            "dataKategori" => $dataKategori
        ];

        return view('barang/index', $data);
    }

    public function allBarang()
    {
        $token = session()->get("login")->token;

        $this_company_id = session()->get("login")->this_company_id;

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this_company_id
        ];

        $response = curl_request("GET", "/barang", $token, $payload);
        $dataUser = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataUser, [
                    "id" => $data->id,
                    "kode_barang" => $data->kode_barang,
                    "nama_barang" => $data->nama_barang,
                    "kode_satuan" => $data->kode_satuan,
                    "kategori" => $data->kategori,
                    "kode_hs" => "",
                    "sub_akun_ap" => $data->sub_akun_ap,
                    "sub_akun_ar" => $data->sub_akun_ar,
                    "stok" => $data->stok,
                    "status" => $data->status,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataUser,
            "response" => $response
        ];

        echo json_encode($data);
        return;
    }

    public function saveBarang()
    {
        $rules = [
            "kode_barang" => [
                "rules" => "required"
            ],
            "nama_barang" => [
                "rules" => "required"
            ],
            "satuan_id" => [
                "rules" => "required"
            ],
            "kategori_id" => [
                "rules" => "required"
            ],
            "hs_id" => [
                "rules" => "required"
            ],
            "ap_id" => [
                "rules" => "required"
            ],
            "ar_id" => [
                "rules" => "required"
            ],
            "stok" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kode_barang" => $this->request->getPost("kode_barang"),
                "nama_barang" => $this->request->getPost("nama_barang"),
                "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                "kategori_id" => formatter($this->request->getPost("kategori_id"), "STR_TO_INT"),
                "hs_id" => formatter($this->request->getPost("hs_id"), "STR_TO_INT"),
                "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                "stok" => formatter($this->request->getPost("stok"), "STR_TO_INT"),
                "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif"
            ]);

            $response = curl_request("POST", "/barang", $token, $payload);

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

    public function updateBarang()
    {
        $rules = [
            "kode_barang" => [
                "rules" => "required"
            ],
            "nama_barang" => [
                "rules" => "required"
            ],
            "satuan_id" => [
                "rules" => "required"
            ],
            "kategori_id" => [
                "rules" => "required"
            ],
            "hs_id" => [
                "rules" => "required"
            ],
            "ap_id" => [
                "rules" => "required"
            ],
            "ar_id" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $token = session()->get("login")->token;

            $id = $this->request->getPost("id");

            $this_company_id = session()->get("login")->this_company_id;

            $payload = json_encode([
                "company_id" => $this_company_id,
                "kode_barang" => $this->request->getPost("kode_barang"),
                "nama_barang" => $this->request->getPost("nama_barang"),
                "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                "kategori_id" => formatter($this->request->getPost("kategori_id"), "STR_TO_INT"),
                "hs_id" => formatter($this->request->getPost("hs_id"), "STR_TO_INT"),
                "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif"
            ]);

            $response = curl_request("PATCH", "/barang/$id", $token, $payload);

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

    public function updateStatusBarang()
    {
        $token = session()->get("login")->token;

        $id = $this->request->getPost("id");

        $this_company_id = session()->get("login")->this_company_id;

        $payload = json_encode([
            "company_id" => $this_company_id,
            "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif"
        ]);
        
        $response = curl_request("PATCH", "/barang/$id", $token, $payload);

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

    public function getByIdBarang($id = null)
    {
        $token = session()->get("login")->token;

        if (!empty($id)) {
            $response = curl_request("GET", "/barang/$id", $token);
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

    public function deleteBarang()
    {
        $token = session()->get("login")->token;
        
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/barang/$id", $token);
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