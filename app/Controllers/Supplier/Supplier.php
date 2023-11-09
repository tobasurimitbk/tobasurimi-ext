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
        $barangMasterData = $this->barangMasterModel->asObject()->where('type_barang', 'bahan_baku')->where('deletedAt', null)->findAll();
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
        $bagianData =  $this->bagianModel->where('deletedAt', null)->asObject()->findAll();

        $dataSupplier = [];
        $dataSupplierHarga = [];

        if ($supplierData) {
            if ($supplierData->type === "BAHAN BAKU") {
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

    public function supplierGenerate($type)
    {
        $response = $this->supplierModel->generateSupplierCode($type);

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

    public function printSupplierBahanBaku($laporan)
    {

        switch ($laporan) {
            case 'laporan-pendapatan-supplier':
                $totalDppUmum = 0;
                $totalPphUmum = 0;
                $totalTotalUmum = 0;
                $totalDppHarian = 0;
                $totalPphHarian = 0;
                $totalTotalHarian = 0;
                $totalDppBulanan = 0;
                $totalPphBulanan = 0;
                $totalTotalBulanan = 0;
                $totalDppSubsidi = 0;
                $totalPphSubsidi = 0;
                $totalTotalSubsidi = 0;
                $totalTotalRow = 0;

                $awalDate = $this->request->getPost('awal_date');
                $newAwalDate = date("Y-m-d", strtotime($awalDate));
                $akhirDate = $this->request->getPost('akhir_date');
                $newAkhirDate = date("Y-m-d", strtotime($akhirDate));
                $supplierId = $this->request->getPost('supplier_id');
                $barangId = $this->request->getPost('barang_id');
                $warehouseId = $this->request->getPost('warehouse_id');

                $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReport($newAwalDate, $newAkhirDate, $supplierId, $barangId, $warehouseId);
                $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $barangId)->where('type_barang', 'bahan_baku')->first();
                $dataWarehouse = $this->warehousesModel->asObject()->where('id', $warehouseId)->first();
                $dataTotalBBLokal = [];
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
                        $totalDppUmum += $row->dppUmum;
                        $totalPphUmum += $row->pphUmum;
                        $totalTotalUmum += $row->totalUmum;
                        $totalDppHarian += $row->dppHarian;
                        $totalPphHarian += $row->pphHarian;
                        $totalTotalHarian += $row->totalHarian;
                        $totalDppBulanan += $row->dppBulanan;
                        $totalPphBulanan += $row->pphBulanan;
                        $totalTotalBulanan += $row->totalBulanan;
                        $totalDppSubsidi += $row->subsidi;
                        $totalPphSubsidi += $row->pphSubsidi;
                        $totalTotalSubsidi += $row->totalSubsidi;
                    }
                    $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
                }
                $no = 1;

                $data = [
                    'no'    => $no,
                    'header'   => "Laporan Pendapatan Supplier",
                    'tanggalAwal' => $awalDate,
                    'tanggalAkhir'       => $akhirDate,
                    'bahanBaku'        => !empty($dataBahanBaku) ? $dataBahanBaku->barang_name : "",
                    'warehouse'      => !empty($dataWarehouse) ? $dataWarehouse->warehouse_name : "",
                    'dataOrder'      => $dataBBLokal,
                    'totalDppUmum' => $totalDppUmum,
                    'totalPphUmum' => $totalPphUmum,
                    'totalTotalUmum' => $totalTotalUmum,
                    'totalDppHarian' => $totalDppHarian,
                    'totalPphHarian' => $totalPphHarian,
                    'totalTotalHarian' => $totalTotalHarian,
                    'totalDppBulanan' => $totalDppBulanan,
                    'totalPphBulanan' => $totalPphBulanan,
                    'totalTotalBulanan' => $totalTotalBulanan,
                    'totalDppSubsidi' => $totalDppSubsidi,
                    'totalPphSubsidi' => $totalPphSubsidi,
                    'totalTotalSubsidi' => $totalTotalSubsidi,
                    'totalTotalRow' => $totalTotalRow
                ];
                break;
            case 'laporan-rincian-per-barang':
                $totalDppUmum = 0;
                $totalPphUmum = 0;
                $totalTotalUmum = 0;
                $totalDppHarian = 0;
                $totalPphHarian = 0;
                $totalTotalHarian = 0;
                $totalDppBulanan = 0;
                $totalPphBulanan = 0;
                $totalTotalBulanan = 0;
                $totalDppSubsidi = 0;
                $totalPphSubsidi = 0;
                $totalTotalSubsidi = 0;
                $totalTotalRow = 0;

                $awalDate = $this->request->getPost('awal_date_per_barang');
                $newAwalDate = date("Y-m-d", strtotime($awalDate));
                $akhirDate = $this->request->getPost('akhir_date_per_barang');
                $newAkhirDate = date("Y-m-d", strtotime($akhirDate));
                $barangId = $this->request->getPost('barang_id_per_barang');

                $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReport($newAwalDate, $newAkhirDate, '', $barangId, '');
                $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $barangId)->where('type_barang', 'bahan_baku')->first();

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
                        $totalDppUmum += $row->dppUmum;
                        $totalPphUmum += $row->pphUmum;
                        $totalTotalUmum += $row->totalUmum;
                        $totalDppHarian += $row->dppHarian;
                        $totalPphHarian += $row->pphHarian;
                        $totalTotalHarian += $row->totalHarian;
                        $totalDppBulanan += $row->dppBulanan;
                        $totalPphBulanan += $row->pphBulanan;
                        $totalTotalBulanan += $row->totalBulanan;
                        $totalDppSubsidi += $row->subsidi;
                        $totalPphSubsidi += $row->pphSubsidi;
                        $totalTotalSubsidi += $row->totalSubsidi;
                    }
                    $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
                }
                $no = 1;

                $data = [
                    'no'    => $no,
                    'header'   => "Laporan Pendapatan Supplier",
                    'tanggalAwal' => $awalDate,
                    'tanggalAkhir'       => $akhirDate,
                    'bahanBaku'        => !empty($dataBahanBaku) ? $dataBahanBaku->barang_name : "",
                    'dataOrder'      => $dataBBLokal,
                    'totalDppUmum' => $totalDppUmum,
                    'totalPphUmum' => $totalPphUmum,
                    'totalTotalUmum' => $totalTotalUmum,
                    'totalDppHarian' => $totalDppHarian,
                    'totalPphHarian' => $totalPphHarian,
                    'totalTotalHarian' => $totalTotalHarian,
                    'totalDppBulanan' => $totalDppBulanan,
                    'totalPphBulanan' => $totalPphBulanan,
                    'totalTotalBulanan' => $totalTotalBulanan,
                    'totalDppSubsidi' => $totalDppSubsidi,
                    'totalPphSubsidi' => $totalPphSubsidi,
                    'totalTotalSubsidi' => $totalTotalSubsidi,
                    'totalTotalRow' => $totalTotalRow
                ];
                break;
            case 'laporan-rekap-all-supplier':
                $totalDppUmum = 0;
                $totalPphUmum = 0;
                $totalTotalUmum = 0;
                $totalDppHarian = 0;
                $totalPphHarian = 0;
                $totalTotalHarian = 0;
                $totalDppBulanan = 0;
                $totalPphBulanan = 0;
                $totalTotalBulanan = 0;
                $totalDppSubsidi = 0;
                $totalPphSubsidi = 0;
                $totalTotalSubsidi = 0;
                $totalTotalRow = 0;

                $awalDate = $this->request->getPost('awal_date_all_supplier');
                $newAwalDate = date("Y-m-d", strtotime($awalDate));
                $akhirDate = $this->request->getPost('akhir_date_all_supplier');
                $newAkhirDate = date("Y-m-d", strtotime($akhirDate));

                $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForAllSupplierReport($newAwalDate, $newAkhirDate);

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
                        $totalDppUmum += $row->dppUmum;
                        $totalPphUmum += $row->pphUmum;
                        $totalTotalUmum += $row->totalUmum;
                        $totalDppHarian += $row->dppHarian;
                        $totalPphHarian += $row->pphHarian;
                        $totalTotalHarian += $row->totalHarian;
                        $totalDppBulanan += $row->dppBulanan;
                        $totalPphBulanan += $row->pphBulanan;
                        $totalTotalBulanan += $row->totalBulanan;
                        $totalDppSubsidi += $row->subsidi;
                        $totalPphSubsidi += $row->pphSubsidi;
                        $totalTotalSubsidi += $row->totalSubsidi;
                    }
                    $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
                }
                $no = 1;

                $data = [
                    'no'    => $no,
                    'header'   => "Laporan Rekap Pendapatan Supplier",
                    'tanggalAwal' => $awalDate,
                    'tanggalAkhir'       => $akhirDate,
                    'dataOrder'      => $dataBBLokal,
                    'totalDppUmum' => $totalDppUmum,
                    'totalPphUmum' => $totalPphUmum,
                    'totalTotalUmum' => $totalTotalUmum,
                    'totalDppHarian' => $totalDppHarian,
                    'totalPphHarian' => $totalPphHarian,
                    'totalTotalHarian' => $totalTotalHarian,
                    'totalDppBulanan' => $totalDppBulanan,
                    'totalPphBulanan' => $totalPphBulanan,
                    'totalTotalBulanan' => $totalTotalBulanan,
                    'totalDppSubsidi' => $totalDppSubsidi,
                    'totalPphSubsidi' => $totalPphSubsidi,
                    'totalTotalSubsidi' => $totalTotalSubsidi,
                    'totalTotalRow' => $totalTotalRow
                ];
                break;
            case 'laporan-rekap-per-supplier':
                $totalDppUmum = 0;
                $totalPphUmum = 0;
                $totalTotalUmum = 0;
                $totalDppHarian = 0;
                $totalPphHarian = 0;
                $totalTotalHarian = 0;
                $totalDppBulanan = 0;
                $totalPphBulanan = 0;
                $totalTotalBulanan = 0;
                $totalDppSubsidi = 0;
                $totalPphSubsidi = 0;
                $totalTotalSubsidi = 0;
                $totalTotalRow = 0;

                $awalDate = $this->request->getPost('awal_date_per_supplier');
                $newAwalDate = date("Y-m-d", strtotime($awalDate));
                $akhirDate = $this->request->getPost('akhir_date_per_supplier');
                $newAkhirDate = date("Y-m-d", strtotime($akhirDate));
                $supplierId = $this->request->getPost('supplier_id_per_supplier');

                $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReport($newAwalDate, $newAkhirDate, $supplierId, '', '');
                $dataSupplier = $this->supplierModel->asObject()->where('id', $supplierId)->first();
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
                        $totalDppUmum += $row->dppUmum;
                        $totalPphUmum += $row->pphUmum;
                        $totalTotalUmum += $row->totalUmum;
                        $totalDppHarian += $row->dppHarian;
                        $totalPphHarian += $row->pphHarian;
                        $totalTotalHarian += $row->totalHarian;
                        $totalDppBulanan += $row->dppBulanan;
                        $totalPphBulanan += $row->pphBulanan;
                        $totalTotalBulanan += $row->totalBulanan;
                        $totalDppSubsidi += $row->subsidi;
                        $totalPphSubsidi += $row->pphSubsidi;
                        $totalTotalSubsidi += $row->totalSubsidi;
                    }
                    $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
                }
                $no = 1;

                $data = [
                    'no'    => $no,
                    'header'   => "Laporan Detail Pendapatan Supplier",
                    'tanggalAwal' => $awalDate,
                    'tanggalAkhir'       => $akhirDate,
                    'supplierName'        => !empty($dataSupplier) ? $dataSupplier->name : "",
                    'supplierAddress'      => !empty($dataSupplier) ? $dataSupplier->address : "",
                    'dataOrder'      => $dataBBLokal,
                    'totalDppUmum' => $totalDppUmum,
                    'totalPphUmum' => $totalPphUmum,
                    'totalTotalUmum' => $totalTotalUmum,
                    'totalDppHarian' => $totalDppHarian,
                    'totalPphHarian' => $totalPphHarian,
                    'totalTotalHarian' => $totalTotalHarian,
                    'totalDppBulanan' => $totalDppBulanan,
                    'totalPphBulanan' => $totalPphBulanan,
                    'totalTotalBulanan' => $totalTotalBulanan,
                    'totalDppSubsidi' => $totalDppSubsidi,
                    'totalPphSubsidi' => $totalPphSubsidi,
                    'totalTotalSubsidi' => $totalTotalSubsidi,
                    'totalTotalRow' => $totalTotalRow
                ];
                break;
            case 'laporan-rekap-all-barang':
                $totalDppUmum = 0;
                $totalPphUmum = 0;
                $totalTotalUmum = 0;
                $totalDppHarian = 0;
                $totalPphHarian = 0;
                $totalTotalHarian = 0;
                $totalDppBulanan = 0;
                $totalPphBulanan = 0;
                $totalTotalBulanan = 0;
                $totalDppSubsidi = 0;
                $totalPphSubsidi = 0;
                $totalTotalSubsidi = 0;
                $totalTotalRow = 0;

                $awalDate = $this->request->getPost('awal_date_rekap_all_barang');
                $newAwalDate = date("Y-m-d", strtotime($awalDate));
                $akhirDate = $this->request->getPost('akhir_date_rekap_all_barang');
                $newAkhirDate = date("Y-m-d", strtotime($akhirDate));
                $warehouseId = $this->request->getPost('warehouse_id_rekap_all_barang');

                $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReportRekap($newAwalDate, $newAkhirDate, '', '', $warehouseId);

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
                        $totalDppUmum += $row->dppUmum;
                        $totalPphUmum += $row->pphUmum;
                        $totalTotalUmum += $row->totalUmum;
                        $totalDppHarian += $row->dppHarian;
                        $totalPphHarian += $row->pphHarian;
                        $totalTotalHarian += $row->totalHarian;
                        $totalDppBulanan += $row->dppBulanan;
                        $totalPphBulanan += $row->pphBulanan;
                        $totalTotalBulanan += $row->totalBulanan;
                        $totalDppSubsidi += $row->subsidi;
                        $totalPphSubsidi += $row->pphSubsidi;
                        $totalTotalSubsidi += $row->totalSubsidi;
                    }
                    $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
                }
                $no = 1;

                $data = [
                    'no'    => $no,
                    'header'   => "Laporan Detail Pendapatan Supplier",
                    'tanggalAwal' => $awalDate,
                    'tanggalAkhir'       => $akhirDate,
                    'supplierName'        => !empty($dataSupplier) ? $dataSupplier->name : "",
                    'supplierAddress'      => !empty($dataSupplier) ? $dataSupplier->address : "",
                    'dataOrder'      => $dataBBLokal,
                    'totalDppUmum' => $totalDppUmum,
                    'totalPphUmum' => $totalPphUmum,
                    'totalTotalUmum' => $totalTotalUmum,
                    'totalDppHarian' => $totalDppHarian,
                    'totalPphHarian' => $totalPphHarian,
                    'totalTotalHarian' => $totalTotalHarian,
                    'totalDppBulanan' => $totalDppBulanan,
                    'totalPphBulanan' => $totalPphBulanan,
                    'totalTotalBulanan' => $totalTotalBulanan,
                    'totalDppSubsidi' => $totalDppSubsidi,
                    'totalPphSubsidi' => $totalPphSubsidi,
                    'totalTotalSubsidi' => $totalTotalSubsidi,
                    'totalTotalRow' => $totalTotalRow
                ];
                break;
            case 'laporan-rekap-per-barang':
                $totalDppUmum = 0;
                $totalPphUmum = 0;
                $totalTotalUmum = 0;
                $totalDppHarian = 0;
                $totalPphHarian = 0;
                $totalTotalHarian = 0;
                $totalDppBulanan = 0;
                $totalPphBulanan = 0;
                $totalTotalBulanan = 0;
                $totalDppSubsidi = 0;
                $totalPphSubsidi = 0;
                $totalTotalSubsidi = 0;
                $totalTotalRow = 0;

                $awalDate = $this->request->getPost('awal_date_rekap_per_barang');
                $newAwalDate = date("Y-m-d", strtotime($awalDate));
                $akhirDate = $this->request->getPost('akhir_date_rekap_per_barang');
                $newAkhirDate = date("Y-m-d", strtotime($akhirDate));
                $barangId = $this->request->getPost('barang_id_rekap_per_barang');
                $warehouseId = $this->request->getPost('warehouse_id_rekap_per_barang');

                $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReportRekap($newAwalDate, $newAkhirDate, '', '', $warehouseId);
                $dataBahanBaku = $this->barangMasterModel->asObject()->where('id', $barangId)->where('type_barang', 'bahan_baku')->first();
                $dataWarehouse = $this->warehousesModel->asObject()->where('id', $warehouseId)->first();

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
                        $totalPphUmum += $row->pphUmum;
                        $totalTotalUmum += $row->totalUmum;
                        $totalDppHarian += $row->dppHarian;
                        $totalPphHarian += $row->pphHarian;
                        $totalTotalHarian += $row->totalHarian;
                        $totalDppBulanan += $row->dppBulanan;
                        $totalPphBulanan += $row->pphBulanan;
                        $totalTotalBulanan += $row->totalBulanan;
                        $totalDppSubsidi += $row->subsidi;
                        $totalPphSubsidi += $row->pphSubsidi;
                        $totalTotalSubsidi += $row->totalSubsidi;
                    }
                    $totalTotalRow = $totalTotalUmum + $totalTotalHarian + $totalTotalBulanan + $totalTotalSubsidi;
                }
                $no = 1;

                $data = [
                    'no'    => $no,
                    'header'   => "Laporan Detail Pendapatan Supplier",
                    'tanggalAwal' => $awalDate,
                    'tanggalAkhir'       => $akhirDate,
                    'bahanBaku'        => !empty($dataBahanBaku) ? $dataBahanBaku->barang_name : "",
                    'warehouse'      => !empty($dataWarehouse) ? $dataWarehouse->warehouse_name : "",
                    'dataOrder'      => $dataBBLokal,
                    'totalDppUmum' => $totalDppUmum,
                    'totalPphUmum' => $totalPphUmum,
                    'totalTotalUmum' => $totalTotalUmum,
                    'totalDppHarian' => $totalDppHarian,
                    'totalPphHarian' => $totalPphHarian,
                    'totalTotalHarian' => $totalTotalHarian,
                    'totalDppBulanan' => $totalDppBulanan,
                    'totalPphBulanan' => $totalPphBulanan,
                    'totalTotalBulanan' => $totalTotalBulanan,
                    'totalDppSubsidi' => $totalDppSubsidi,
                    'totalPphSubsidi' => $totalPphSubsidi,
                    'totalTotalSubsidi' => $totalTotalSubsidi,
                    'totalTotalRow' => $totalTotalRow
                ];
                break;
        }

        $domPdf = new Dompdf();

        $fileName = 'Order Form';

        // load HTML content
        $domPdf->loadHtml(view('Supplier/supplierBahanBaku/print-' . $laporan . '', $data));

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
