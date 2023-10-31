<?php

namespace App\Controllers\Supplier;
use Dompdf\Dompdf;

use App\Controllers\BaseController;
use App\Models\CountryModel;
use App\Models\ProvinceModel;
use App\Models\SupplierModel;
use App\Models\SupplierHargaModel;
use App\Models\BarangMasterModel;
use App\Models\BagianModel;
use App\Models\WarehousesModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;

class Supplier extends BaseController
{
    protected $this_company_id, $provinceModel, $countryModel, $supplierModel, $supplierHargaModel, $barangMasterModel, $bagianModel;
    protected $RMPurchaseOrderModel;
    protected $RMPurchaseOrderDetailModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->provinceModel = new ProvinceModel();
        $this->countryModel = new CountryModel();
        $this->supplierModel = new SupplierModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->bagianModel = new BagianModel();
        $this->warehousesModel = new WarehousesModel();
        $this->RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
    }

    // bahan baku
    public function supplierBahanBaku()
    {
        $provinceData = $this->provinceModel->asObject()->findAll();
        $countryData = $this->countryModel->asObject()->findAll();
        $supplierData = $this->supplierModel->asObject()->findAll();
        $barangMasterData = $this->barangMasterModel->asObject()->findAll();
        $warehousesData = $this->warehousesModel->asObject()->findAll();

        $data = [
            "dataProvinces" => $provinceData,
            "dataSuppliers" => $supplierData,
            "dataBarangMasters" => $barangMasterData,
            "dataWarehouses" => $warehousesData,
            "country" => $countryData
        ];

        return view('Supplier/supplierBahanBaku/index', $data);
    }

    public function getSupplierBahanBakuHarga($id)
    {
        $supplierData = $this->supplierModel->getSupplierById($id);
        $barangData = $this->barangMasterModel->getBarangByType('bahan_baku');
        $bagianData =  $this->bagianModel->asObject()->findAll();

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
            "dataBagian" => $bagianData,
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
                "bagian_id" => $this->request->getPost("bagian"),
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

    public function supplierInternasional()
    {
        return view('Supplier/supplierInternasional/index');
    }

    public function allSupplierInternasional()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "type"          => "INTERNASIONAL"
        ];

        $condition = [
            "suppliers.type"        => "INTERNASIONAL"
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
                "no"                => $no++,
                "kode"              => $data->kode,
                "id"                => $data->id,
                "name"              => $data->name,
                "address"           => $data->address,
                "phone"             => $data->phone,
                "contact_person"    => $data->contact_person,
                "fax"               => $data->fax
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

    public function saveSupplierInternasional()
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
                "fax" => [
                    "rules" => "permit_empty|string"
                ],
                "phone" => [
                    "rules" => "permit_empty|string"
                ],
                "contact_person" => [
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
                "fax" => $this->request->getPost("fax"),
                "phone" => $this->request->getPost("phone"),
                "contact_person" => $this->request->getPost("contact_person"),
                "type"              => "INTERNASIONAL"
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

    public function updateSupplierInternasional()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "permit_empty|string"
                ],
                "fax" => [
                    "rules" => "permit_empty|string"
                ],
                "phone" => [
                    "rules" => "permit_empty|string"
                ],
                "contact_person" => [
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
                    "fax" => $this->request->getPost("fax"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
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
        $dataSupplier = $this->supplierModel->getSupplierByType($this->request->getGet('tipe'));

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

    public function printSupplierBahanBaku($laporan){
        $awalDate = $this->request->getPost('awal_date');
        $newAwalDate = date("Y-m-d", strtotime($awalDate));
        $akhirDate = $this->request->getPost('akhir_date');
        $newAkhirDate = date("Y-m-d", strtotime($akhirDate));
        
        switch ($laporan) {
            case 'laporan-pendapatan-supplier':
                $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReport($newAwalDate,$newAkhirDate,$this->request->getPost('barang_id'),$this->request->getPost('warehouse_id'));
                $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $this->request->getPost('barang_id'))->first();
                $dataWarehouse = $this->warehousesModel->asObject()->where('id', $this->request->getPost('warehouse_id'))->first();
                if (!empty($dataBBLokal)) {
                    foreach ($dataBBLokal as $row) {
                        $row->pphUmum       = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppUmum * 0.0025) : ($row->dppUmum * 0.005)) : 0;
                        $row->totalUmum     = $row->dppUmum - $row->pphUmum;
                        $row->pphHarian     = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppHarian * 0.0025) : ($row->dppHarian * 0.005)) : 0;
                        $row->totalHarian   = $row->dppHarian - $row->pphHarian;
                        $row->pphBulanan    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->dppBulanan * 0.0025) : ($row->dppBulanan * 0.005)) : 0;
                        $row->totalBulanan  = $row->dppBulanan - $row->pphBulanan;
                        $row->pphSubsidi    = ($row->poPPH != 'None') ? (!empty($row->supplierNpwp) ? ($row->subsidi * 0.0025) : ($row->subsidi * 0.005)) : 0;
                        $row->totalSubsidi  = $row->subsidi - $row->pphSubsidi;
                        $row->totalRow      = $row->totalUmum + $row->totalHarian + $row->totalBulanan + $row->totalSubsidi;
                    }
                }
                $no = 1;

                $data = [
                    'no'    => $no,
                    'header'   => "Laporan Pendapatan Supplier",
                    'tanggalAwal'=> $this->request->getPost('awal_date'),
                    'tanggalAkhir'       => $this->request->getPost('akhir_date'),
                    'bahanBaku'        => !empty($dataBahanBaku) ? $dataBahanBaku->barang_name : "",
                    'warehouse'      => !empty($dataWarehouse) ? $dataWarehouse->warehouse_name : "",
                    'dataOrder'      => $dataBBLokal
                ];
                break;
            case 'laporan-rincian-per-barang':
                # code...
                break;
            case 'laporan-rekap-all-supplier':
                # code...
                break;
            case 'laporan-rekap-per-supplier':
                # code...
                break;
            case 'laporan-rekap-all-barang':
                # code...
                break;
            case 'laporan-rekap-per-barang':
                # code...
                break;
            case 'laporan-bukti-penerimaaan-barang':
                # code...
                break;
            case 'laporan-kwitansi-tb':
                # code...
                break;
        }

        $domPdf = new Dompdf();

        $fileName = 'Order Form';
        
        // load HTML content
        $domPdf->loadHtml(view('Supplier/supplierBahanBaku/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('legal', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
        // return view('Supplier/supplierBahanBaku/print');
    }
}
