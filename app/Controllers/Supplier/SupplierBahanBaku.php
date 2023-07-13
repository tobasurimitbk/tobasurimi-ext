<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;

use App\Models\ProvinceModel;
use App\Models\SupplierModel;

class SupplierBahanBaku extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function supplierBahanBaku()
    {
        $provinceModel = new ProvinceModel();
        //Get Provinces
        $provinceData = $provinceModel->asObject()->findAll();

        $data = [
            "dataProvinces" => $provinceData,
        ];

        return view('Supplier/supplierBahanBaku/index', $data);
    }

    public function allSupplierBahanBaku()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "kategori"      => "LOKAL",
            "type"          => "BAHAN BAKU"
        ];

        $supplierModel = new SupplierModel();
        $condition = [
            "suppliers.company_id"  => $this->this_company_id,
            "kategori"              => "LOKAL",
            "suppliers.type"        => "BAHAN BAKU"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $supplierModel->getSupplierList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            array_push($dataSupplier, [
                "no"            => $no++,
                "id"            => $data->id,
                "kode"          => $data->kode,
                "name"          => $data->name,
                "address"       => $data->address,
                "province_name" => $data->province_name,
                "city_name"     => $data->city_name,
                "postal_code"   => $data->postal_code, 
                "no_npwp"       => $data->no_npwp,
                "phone"         => $data->phone,
                "contact_person"=> $data->contact_person,
                "email"         => $data->email,
                "no_rekening"   => $data->no_rekening,
                "supplier_buyer"=> $data->supplier_buyer,
                "ap_name"       => $data->ap_name,
                "ar_name"       => $data->ar_name
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $supplierData['totalData'],
            "recordsFiltered"   => $supplierData['totalFilteredData'],
            "data"              => $dataSupplier,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSupplierBahanBaku()
    {
        try{
            $supplierModel = new SupplierModel();

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

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

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
                "type" => "BAHAN BAKU"
                // "list_address" => json_decode(stripslashes($this->request->getPost("list_address")))
            ]);

            $insertData = [
                "company_id"        => $this->this_company_id,
                "kode"              => $this->request->getPost("kode"),
                "name"              => $this->request->getPost("name"),
                "address"           => $this->request->getPost("address"),
                "no_npwp"           => $this->request->getPost("no_npwp"),
                "phone"             => $this->request->getPost("phone"),
                "contact_person"    => $this->request->getPost("contact_person"),
                "email"             => $this->request->getPost("email"),
                "no_rekening"       => $this->request->getPost("no_rekening"),
                "supplier_buyer"    => $this->request->getPost("supplier_buyer"),
                "province_id"       => $this->request->getPost("province_parent_id"),
                "city_id"           => $this->request->getPost("city_parent_id"),
                "ap_id"             => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                "ar_id"             => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                "kategori"          => "LOKAL",
                "type"              => "BAHAN BAKU"
            ];
            $insert = $supplierModel->insert($insertData);
            $response = curl_request("POST", "/suppliers", $this->token, $payload);

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => $payload,
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
                
            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $payload,
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function updateSupplierBahanBaku()
    {
        try{
            $supplierModel = new SupplierModel();

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

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = [
                    "company_id"        => $this->this_company_id,
                    "kode"              => $this->request->getPost("kode"),
                    "name"              => $this->request->getPost("name"),
                    "address"           => $this->request->getPost("address"),
                    "no_npwp"           => $this->request->getPost("no_npwp"),
                    "phone"             => $this->request->getPost("phone"),
                    "contact_person"    => $this->request->getPost("contact_person"),
                    "email"             => $this->request->getPost("email"),
                    "no_rekening"       => $this->request->getPost("no_rekening"),
                    "supplier_buyer"    => $this->request->getPost("supplier_buyer"),
                    "province_id"       => $this->request->getPost("province_parent_id"),
                    "city_id"           => $this->request->getPost("city_parent_id"),
                    "ap_id"             => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id"             => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    "kategori"          => "LOKAL",
                    "type"              => "BAHAN BAKU"
                    // "list_address" => json_decode(stripslashes($this->request->getPost("list_address")))
                ];
            }

            if ($payload) {
                $supplierModel->update($id, $payload);

                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
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
            return;
        }
    }

    public function getByIdSupplierBahanBaku($id)
    {
        $supplierModel = new SupplierModel();
        $supplierData = $supplierModel->getSupplierById($id);

        if (!$supplierData) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }
        
        // $response = curl_request("GET", "/suppliers/$id?idCompany=$this->this_company_id", $this->token);

        $supplierData->list_address = []; // cek nanti
        $data = [
            "status"    => true,
            "data"      => $supplierData,
        ];
        echo json_encode($data);
        
        return;
    }

    public function deleteSupplierBahanBaku()
    {
        try{
            $supplierModel = new SupplierModel();
            $id = $this->request->getPost("id");

            if (empty($id)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $supplierModel->delete($id);
            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function dropdownSupplier()
    {
        $responseSupplier = curl_request("GET", "/suppliers/all?kategori=LOKAL&type=BAHAN%20BAKU&idCompany=$this->this_company_id", $this->token);

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
