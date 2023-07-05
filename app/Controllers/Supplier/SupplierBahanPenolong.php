<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;

class SupplierBahanPenolong extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function supplierBahanPenolong()
    {
        //Get Provinces
        $responseProvinces = curl_request("GET", "/provinces/all", $this->token);

        $dataProvinces = [];
        if ($responseProvinces["code"] === 200) {
            $dataProvinces = json_decode($responseProvinces["body"])->data;
        }

        $data = [
            "dataProvinces" => $dataProvinces,
        ];

        return view('Supplier/supplierBahanPenolong/index', $data);
    }

    public function allSupplierBahanPenolong()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this->this_company_id,
            "kategori" => "LOKAL",
            "type" => "BAHAN PENOLONG"
        ];

        $response = curl_request("GET", "/suppliers", $this->token, $payload);
        $dataSupplier = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataSupplier, [
                    "id" => $data->id,
                    "kode" => $data->kode,
                    "name" => $data->name,
                    "address" => $data->address,
                    "province_name" => $data->province_name,
                    "city_name" => $data->city_name,
                    "postal_code" => $data->postal_code, 
                    "no_npwp" => $data->no_npwp,
                    "phone" => $data->phone,
                    "contact_person" => $data->contact_person,
                    "email" => $data->email,
                    "no_rekening" => $data->no_rekening,
                    "supplier_buyer" => $data->supplier_buyer,
                    "ap_name" => $data->ap_name,
                    "ar_name" => $data->ar_name
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataSupplier,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSupplierBahanPenolong()
    {
        try{
        $rules = [
            "kode" => [
                "rules" => "required"
            ],
            "name" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "no_npwp" => [
                "rules" => "required"
            ],
            "phone" => [
                "rules" => "required"
            ],
            "contact_person" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
            "no_rekening" => [
                "rules" => "required"
            ],
            "supplier_buyer" => [
                "rules" => "required"
            ],
            "province_parent_id" => [
                "rules" => "required"
            ],
            "city_parent_id" => [
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
            $payload = json_encode([
                "company_id" => $this->this_company_id,
                "kode" => $this->request->getPost("kode"),
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "no_npwp" => $this->request->getPost("no_npwp"),
                "phone" => $this->request->getPost("phone"),
                "contact_person" => $this->request->getPost("contact_person"),
                "email" => $this->request->getPost("email"),
                "no_rekening" => $this->request->getPost("no_rekening"),
                "supplier_buyer" => $this->request->getPost("supplier_buyer"),
                "province_id" => $this->request->getPost("province_parent_id"),
                "city_id" => $this->request->getPost("city_parent_id"),
                "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                "kategori" => "LOKAL",
                "type" => "BAHAN PENOLONG"
                // "list_address" => json_decode(stripslashes($this->request->getPost("list_address")))
            ]);

            $response = curl_request("POST", "/suppliers", $this->token, $payload);

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

    public function updateSupplierBahanPenolong()
    {
        try{
        $rules = [
            "kode" => [
                "rules" => "required"
            ],
            "name" => [
                "rules" => "required"
            ],
            "address" => [
                "rules" => "required"
            ],
            "no_npwp" => [
                "rules" => "required"
            ],
            "phone" => [
                "rules" => "required"
            ],
            "contact_person" => [
                "rules" => "required"
            ],
            "email" => [
                "rules" => "required"
            ],
            "no_rekening" => [
                "rules" => "required"
            ],
            "supplier_buyer" => [
                "rules" => "required"
            ],
            "province_parent_id" => [
                "rules" => "required"
            ],
            "city_parent_id" => [
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
                "kode" => $this->request->getPost("kode"),
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "no_npwp" => $this->request->getPost("no_npwp"),
                "phone" => $this->request->getPost("phone"),
                "contact_person" => $this->request->getPost("contact_person"),
                "email" => $this->request->getPost("email"),
                "no_rekening" => $this->request->getPost("no_rekening"),
                "supplier_buyer" => $this->request->getPost("supplier_buyer"),
                "province_id" => $this->request->getPost("province_parent_id"),
                "city_id" => $this->request->getPost("city_parent_id"),
                "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                "kategori" => "LOKAL",
                "type" => "BAHAN PENOLONG"
                // "list_address" => json_decode(stripslashes($this->request->getPost("list_address")))
            ]);
        }

        if ($payload) {
            $response = curl_request("PATCH", "/suppliers/$id", $this->token, $payload);

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

    public function getByIdSupplierBahanPenolong($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/suppliers/$id?idCompany=$this->this_company_id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditampilkan';
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

    public function deleteSupplierBahanPenolong()
    {
        try{
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/suppliers/$id", $this->token);
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

    public function dropdownSupplier()
    {
        $responseSupplier = curl_request("GET", "/suppliers/all?kategori=LOKAL&type=BAHAN%20PENOLONG&idCompany=$this->this_company_id", $this->token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        $data = [
            "data" => $dataSupplier
        ];

        echo json_encode($data);
        return;
    }
}
