<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

class Barang extends BaseController
{
    protected $token;
    protected $this_company_id;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function barang()
    {
        // Get Kategori
        $responseKategori = curl_request("GET", "/metadata/all?name=kategori_barang", $this->token);

        $dataKategori = [];
        if ($responseKategori["code"] === 200) {
            $dataKategori = json_decode($responseKategori["body"])->data;
        }
         
        $data = [
            "dataKategori" => $dataKategori
        ];

        return view('Warehouse/barang/index', $data);
    }

    public function allBarang()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "idCategory" => formatter($this->request->getGet("kategori"), "STR_TO_INT"),
            "status" => $this->request->getGet("status"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this->this_company_id
        ]; 

        $response = curl_request("GET", "/barang", $this->token, $payload);
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
                    "harga_barang" => $data->harga_barang,
                    "kode_satuan" => $data->kode_satuan,
                    "kategori" => $data->kategori,
                    "code_hs" => $data->code_hs,
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
            "response" => $response,
            "payload" => $payload
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
            "harga_barang" => [
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
            $payload = json_encode([
                "company_id" => $this->this_company_id,
                "kode_barang" => $this->request->getPost("kode_barang"),
                "nama_barang" => $this->request->getPost("nama_barang"),
                "harga_barang" => formatter($this->request->getPost("harga_barang"), "CURR_TO_INT"),
                "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                "kategori_id" => formatter($this->request->getPost("kategori_id"), "STR_TO_INT"),
                "hs_id" => formatter($this->request->getPost("hs_id"), "STR_TO_INT"),
                "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                "stok" => $this->request->getPost("stok") ? formatter($this->request->getPost("stok"), "STR_TO_INT") : 0,
                "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif",
                "spek" => json_decode($this->request->getPost("spek"))
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);

            $response = curl_request("POST", "/barang", $this->token, $payload);

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
            "harga_barang" => [
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
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "company_id" => $this->this_company_id,
                "kode_barang" => $this->request->getPost("kode_barang"),
                "nama_barang" => $this->request->getPost("nama_barang"),
                "harga_barang" => formatter($this->request->getPost("harga_barang"), "CURR_TO_INT"),
                "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                "kategori_id" => formatter($this->request->getPost("kategori_id"), "STR_TO_INT"),
                "hs_id" => formatter($this->request->getPost("hs_id"), "STR_TO_INT"),
                "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif",
                "spek" => json_decode($this->request->getPost("spek"))
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);

            $response = curl_request("PATCH", "/barang/$id", $this->token, $payload);

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
        $id = $this->request->getPost("id");

        $payload = json_encode([
            "company_id" => $this->this_company_id,
            "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif"
        ]);
        
        $response = curl_request("PATCH", "/barang/$id", $this->token, $payload);

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
        if (!empty($id)) {
            $response = curl_request("GET", "/barang/$id", $this->token);
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
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/barang/$id", $this->token);
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

    public function dropdownBarang()
    {
        $responseBarang = curl_request("GET", "/barang/all?idCompany=$this->this_company_id", $this->token);

        $dataBarang = [];
        if ($responseBarang["code"] === 200) {
            $dataBarang = json_decode($responseBarang["body"])->data;
        }

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangKategori()
    {
        $kategori = $this->request->getGet("kategori");
        $responseBarang = curl_request("GET", "/barang/getByCategory/$kategori", $this->token);

        $dataBarang = [];
        if ($responseBarang["code"] === 200) {
            $dataBarang = json_decode($responseBarang["body"])->data;
        }

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }
}
?>