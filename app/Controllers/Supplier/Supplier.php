<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;
use App\Models\CountryModel;
use App\Models\ProvinceModel;
use App\Models\SupplierModel;
use App\Models\SupplierHargaModel;
use App\Models\BarangMasterModel;

class Supplier extends BaseController
{
    protected $this_company_id, $provinceModel, $countryModel, $supplierModel, $supplierHargaModel, $barangMasterModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->provinceModel = new ProvinceModel();
        $this->countryModel = new CountryModel();
        $this->supplierModel = new SupplierModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->barangMasterModel = new BarangMasterModel();
    }

    // bahan baku
    public function supplierBahanBaku()
    {
        $provinceData = $this->provinceModel->asObject()->findAll();
        $countryData = $this->countryModel->asObject()->findAll();

        $data = [
            "dataProvinces" => $provinceData,
            "country" => $countryData
        ];

        return view('Supplier/supplierBahanBaku/index', $data);
    }

    public function getSupplierBahanBakuHarga($id)
    {
        $supplierData = $this->supplierModel->getSupplierById($id);
        $barangData = $this->barangMasterModel->getBarangByType('bahan_baku');

        $dataSupplier = [];
        $dataSupplierHarga = [];

        if($supplierData)
        {
            if($supplierData->type === "BAHAN BAKU")
            {
                $dataSupplier = $supplierData;
                $dataSupplierHarga = $this->supplierHargaModel->getBySupplierId($id);
            }
        }

        $data = [
            "dataSupplier" => $dataSupplier,
            "dataSupplierHarga" => $dataSupplierHarga,
            "dataBarang" => $barangData
        ];

        return view('Supplier/supplierBahanBaku/harga', $data);
    }

    public function allSupplierBahanBaku()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "type"          => "BAHAN BAKU"
        ];

        $condition = [
            "suppliers.type"        => "BAHAN BAKU"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->supplierModel->getSupplierList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            array_push($dataSupplier, [
                "no"            => $no++,
                "kode"          => $data->kode,
                "id"            => $data->id,
                "name"          => $data->name,
                "address"       => $data->address,
                "no_npwp"       => $data->no_npwp
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $supplierData['totalData'],
            "recordsFiltered"   => $supplierData['totalFilteredData'],
            "data"              => $dataSupplier,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSupplierBahanBaku()
    {
        try {
            $rules = [
                "kode" => [
                    "rules" => "required"
                ],
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "permit_empty|string"
                ],
                "no_npwp" => [
                    "rules" => "permit_empty|string"
                ],
                "phone" => [
                    "rules" => "permit_empty|string"
                ],
                "contact_person" => [
                    "rules" => "permit_empty|string"
                ],
                "email" => [
                    "rules" => "permit_empty|valid_email"
                ],
                "country_code" => [
                    "rules" => "permit_empty"
                ],
                "postal_code" => [
                    "rules" => "permit_empty|numeric"
                ],
                "province_parent_id" => [
                    "rules" => "permit_empty|numeric"
                ],
                "city_parent_id" => [
                    "rules" => "permit_empty|numeric"
                ],
                "account_receivable" => [
                    "rules" => "permit_empty|string"
                ],
                "account_payable" => [
                    "rules" => "permit_empty|string"
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

            $insertData = [
                "kode" => $this->request->getPost("kode"),
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "no_npwp" => $this->request->getPost("no_npwp"),
                "phone" => $this->request->getPost("phone"),
                "contact_person" => $this->request->getPost("contact_person"),
                "email" => $this->request->getPost("email"),
                "province_id" => $this->request->getPost("province_parent_id"),
                "city_id" => $this->request->getPost("city_parent_id"),
                "postal_code" => $this->request->getPost("postal_code"),
                "country_code"      => $this->request->getPost("country_code"),
                "account_receivable" => $this->request->getPost("account_receivable"),
                "account_payable" => $this->request->getPost("account_payable"),
                "type"              => "BAHAN BAKU"
            ];
            $insert = $this->supplierModel->insert($insertData);

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => json_encode($insertData),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => json_encode($insertData),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function updateSupplierBahanBaku()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "permit_empty|string"
                ],
                "no_npwp" => [
                    "rules" => "permit_empty|string"
                ],
                "phone" => [
                    "rules" => "permit_empty|string"
                ],
                "contact_person" => [
                    "rules" => "permit_empty|string"
                ],
                "email" => [
                    "rules" => "permit_empty|valid_email"
                ],
                "country_code" => [
                    "rules" => "permit_empty"
                ],
                "postal_code" => [
                    "rules" => "permit_empty|numeric"
                ],
                "province_parent_id" => [
                    "rules" => "permit_empty|numeric"
                ],
                "city_parent_id" => [
                    "rules" => "permit_empty|numeric"
                ],
                "account_receivable" => [
                    "rules" => "permit_empty|string"
                ],
                "account_payable" => [
                    "rules" => "permit_empty|string"
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
                    "name" => $this->request->getPost("name"),
                    "address" => $this->request->getPost("address"),
                    "no_npwp" => $this->request->getPost("no_npwp"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
                    "email" => $this->request->getPost("email"),
                    "province_id" => $this->request->getPost("province_parent_id"),
                    "city_id" => $this->request->getPost("city_parent_id"),
                    "postal_code" => $this->request->getPost("postal_code"),
                    "country_code"      => $this->request->getPost("country_code"),
                    "account_receivable" => $this->request->getPost("account_receivable"),
                    "account_payable" => $this->request->getPost("account_payable"),
                ];
            }

            if ($payload) {
                $this->supplierModel->update($id, $payload);

                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function supplierBahanPenolong()
    {
        $provinceData = $this->provinceModel->asObject()->findAll();
        $countryData = $this->countryModel->asObject()->findAll();

        $data = [
            "dataProvinces" => $provinceData,
            "country" => $countryData
        ];

        return view('Supplier/supplierBahanPenolong/index', $data);
    }

    public function allSupplierBahanPenolong()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "type"          => "BAHAN PENOLONG"
        ];

        $condition = [
            "suppliers.type"        => "BAHAN PENOLONG"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->supplierModel->getSupplierList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            array_push($dataSupplier, [
                "no"            => $no++,
                "kode"          => $data->kode,
                "id"            => $data->id,
                "name"          => $data->name,
                "address"       => $data->address,
                "no_npwp"       => $data->no_npwp,
                "phone"         => $data->phone
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $supplierData['totalData'],
            "recordsFiltered"   => $supplierData['totalFilteredData'],
            "data"              => $dataSupplier,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSupplierBahanPenolong()
    {
        try {
            $rules = [
                "kode" => [
                    "rules" => "required"
                ],
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "permit_empty|string"
                ],
                "no_npwp" => [
                    "rules" => "permit_empty|string"
                ],
                "phone" => [
                    "rules" => "permit_empty|string"
                ],
                "contact_person" => [
                    "rules" => "permit_empty|string"
                ],
                "email" => [
                    "rules" => "permit_empty|valid_email"
                ],
                "country_code" => [
                    "rules" => "permit_empty"
                ],
                "postal_code" => [
                    "rules" => "permit_empty|numeric"
                ],
                "province_parent_id" => [
                    "rules" => "permit_empty|numeric"
                ],
                "city_parent_id" => [
                    "rules" => "permit_empty|numeric"
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

            $insertData = [
                "kode" => $this->request->getPost("kode"),
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "no_npwp" => $this->request->getPost("no_npwp"),
                "phone" => $this->request->getPost("phone"),
                "contact_person" => $this->request->getPost("contact_person"),
                "email" => $this->request->getPost("email"),
                "province_id" => $this->request->getPost("province_parent_id"),
                "city_id" => $this->request->getPost("city_parent_id"),
                "postal_code" => $this->request->getPost("postal_code"),
                "country_code"      => $this->request->getPost("country_code"),
                "type"              => "BAHAN PENOLONG"
            ];

            $insert = $this->supplierModel->insert($insertData);

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => json_encode($insertData),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => json_encode($insertData),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function updateSupplierBahanPenolong()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "permit_empty|string"
                ],
                "no_npwp" => [
                    "rules" => "permit_empty|string"
                ],
                "phone" => [
                    "rules" => "permit_empty|string"
                ],
                "contact_person" => [
                    "rules" => "permit_empty|string"
                ],
                "email" => [
                    "rules" => "permit_empty|valid_email"
                ],
                "country_code" => [
                    "rules" => "permit_empty"
                ],
                "postal_code" => [
                    "rules" => "permit_empty|numeric"
                ],
                "province_parent_id" => [
                    "rules" => "permit_empty|numeric"
                ],
                "city_parent_id" => [
                    "rules" => "permit_empty|numeric"
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
                    "name" => $this->request->getPost("name"),
                    "address" => $this->request->getPost("address"),
                    "no_npwp" => $this->request->getPost("no_npwp"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
                    "email" => $this->request->getPost("email"),
                    "province_id" => $this->request->getPost("province_parent_id"),
                    "city_id" => $this->request->getPost("city_parent_id"),
                    "postal_code" => $this->request->getPost("postal_code"),
                    "country_code"      => $this->request->getPost("country_code")
                ];
            }

            if ($payload) {
                $this->supplierModel->update($id, $payload);

                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function getByIdSupplier($id)
    {
        $supplierData = $this->supplierModel->getSupplierById($id);

        if (!$supplierData) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }

        $data = [
            "status"    => true,
            "data"      => $supplierData,
        ];
        echo json_encode($data);

        return;
    }

    public function deleteSupplier()
    {
        try {
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

            $this->supplierModel->delete($id);
            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
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
        $dataSupplier = $this->supplierModel->getSupplierByType('BAHAN BAKU');

        $data = [
            "data" => $dataSupplier
        ];

        echo json_encode($data);
        return;
    }

    public function supplierAjax()
    {
        $id = $this->request->getGet("id");

        if (!empty($id)) {
            $response = $this->supplierModel->getSupplierById($id);
            if ($response) {
                $data = [
                    "status"  => true,
                    "data"  => $response
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditemukan';
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

    public function supplierGenerate()
    {
        $response = $this->supplierModel->generateSupplierCode();

        if ($response) {
            $data = [
                "status"  => true,
                "data"  => $response
            ];
            echo json_encode($data);
        } else {
            $message = 'Kode Gagal di Generate!!!';
            $data = [
                "status" => false,
                "message"  => $message
            ];
            echo json_encode($data);
        }
        return;
    }
}
