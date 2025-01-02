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
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\WarehousesModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Supplier extends BaseController
{
    protected $this_company_id, $provinceModel, $countryModel, $supplierModel, $supplierHargaModel, $barangMasterModel, $bagianModel;
    protected $RMPurchaseOrderModel;
    protected $RMPurchaseOrderDetailModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $warehousesModel;
    protected $divisiModel;
    protected $is_admin;
    protected $this_user_id;
    protected $companyModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->this_user_id = session()->get("login")->user_id;
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
        $this->divisiModel = new DivisisModel();
        $this->companyModel = new CompaniesModel();
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

    public function generateKodeSupplierBB()
    {
        $supplierModel = new SupplierModel();
        $supplierCode = $supplierModel->generateSupplierCode("BB", $this->this_company_id);

        return json_encode($supplierCode);
    }

    public function getSupplierBahanBakuHarga($id)
    {
        $id = decrypt($id);

        $supplierData = $this->supplierModel->getSupplierById($id);
        $barangData = $this->barangMasterModel->getBarangByType('bahan_baku');
        $divisi = $this->divisiModel->getDivisiAccess();

        if ($supplierData == null) {
            return redirect()->to('supplier-bahan-baku');
        }

        $dataSupplier = [];

        if ($supplierData) {
            if ($supplierData->type === "BAHAN BAKU") {
                $dataSupplier = $supplierData;
            }
        }

        $data = [
            "dataSupplier" => $dataSupplier,
            "dataDivisi" => $divisi,
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


        if ($this->is_admin == '1') {

            $condition = [
                "suppliers.type"        => "BAHAN BAKU",
                "company_id" => $this->this_company_id
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                "suppliers.type"        => "BAHAN BAKU",
                "company_id" => $this->this_company_id,
                'suppliers.user_id' => $this->this_user_id
            ];
        }

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
                "id"            => encrypt($data->id),
                "name"          => strtoupper($data->name),
                "address"       => strtoupper($data->address),
                "no_npwp"       => formatNpwp($data->no_npwp),
                "phone"       => formatNpwp($data->phone)
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
                "company_id" => $this->this_company_id,
                "kode" => $this->request->getPost("kode"),
                "name" => strtoupper($this->request->getVar("name")),
                "address" => strtoupper($this->request->getVar("address")),
                "no_npwp" => str_replace(['.', '-'], '',  $this->request->getPost("no_npwp")),
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
                $id = decrypt($this->request->getPost("id"));

                $payload = [
                    "company_id" => $this->this_company_id,
                    "name" => strtoupper($this->request->getVar("name")),
                    "address" => strtoupper($this->request->getVar("address")),
                    "no_npwp" => str_replace(['.', '-'], '',  $this->request->getPost("no_npwp")),
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


    public function generateKodeSupplierBP()
    {
        $supplierModel = new SupplierModel();
        $supplierCode = $supplierModel->generateSupplierCode("BP", $this->this_company_id);

        return json_encode($supplierCode);
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


        if ($this->is_admin == '1') {

            $condition = [
                "suppliers.company_id" => $this->this_company_id,
                "suppliers.type"        => "BAHAN PENOLONG"
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                "suppliers.company_id" => $this->this_company_id,
                "suppliers.type"        => "BAHAN PENOLONG",
                'suppliers.user_id' => $this->this_user_id
            ];
        }


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
                "name"          => strtoupper($data->name),
                "address"       => strtoupper($data->address),
                "no_npwp"       => formatNpwp($data->no_npwp),
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
                "company_id" => $this->this_company_id,
                "kode" => $this->request->getPost("kode"),
                "name" => strtoupper($this->request->getVar("name")),
                "address" => strtoupper($this->request->getVar("address")),
                "no_npwp" => str_replace(['.', '-'], '',  $this->request->getPost("no_npwp")),
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
                    "company_id" => $this->this_company_id,
                    "name" => strtoupper($this->request->getVar("name")),
                    "address" => strtoupper($this->request->getVar("address")),
                    "no_npwp" => str_replace(['.', '-'], '',  $this->request->getPost("no_npwp")),
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


    public function generateKodeSupplierInternasional()
    {
        $supplierModel = new SupplierModel();
        $supplierCode = $supplierModel->generateSupplierCode("I", $this->this_company_id);

        return json_encode($supplierCode);
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

        if ($this->is_admin == '1') {
            $condition = [
                "suppliers.company_id" => $this->this_company_id,
                "suppliers.type"        => "INTERNASIONAL"
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                "suppliers.company_id" => $this->this_company_id,
                "suppliers.type"        => "INTERNASIONAL",
                'suppliers.user_id' => $this->this_user_id
            ];
        }

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
                "name"              => strtoupper($data->name),
                "address"           => strtoupper($data->address),
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
                "company_id" => $this->this_company_id,
                "kode" => $this->request->getPost("kode"),
                "name" => strtoupper($this->request->getVar("name")),
                "address" => strtoupper($this->request->getVar("address")),
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
                    "company_id" => $this->this_company_id,
                    "name" => strtoupper($this->request->getVar("name")),
                    "address" => strtoupper($this->request->getVar("address")),
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
        if (is_numeric($id)) {
            $id = $id;
        } else {
            $id = decrypt($id);
        }

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
            if (is_numeric($this->request->getPost('id'))) {
                $id = $this->request->getPost("id");
            } else {
                $id = decrypt($this->request->getPost("id"));
            }

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
        $response = $this->supplierModel->generateSupplierCode($type, $this->this_company_id);

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

                $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalForSupplierReport2($newAwalDate, $newAkhirDate, '', $barangId, '');
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

    public function importSupplier()
    {
        $rules = [
            "file" => [
                'rules' => 'uploaded[file]|ext_in[file,xlsx]',
                'errors' => [
                    'uploaded' => 'Tidak ada file yang di-upload.',
                    'ext_in' => 'File yang di-upload harus berupa file Excel (.xlsx).',
                ],

            ],
        ];

        if ($this->validate($rules)) {
            $file = $this->request->getFile('file');
            $type = $this->request->getVar('type');

            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $data = [];
            $rowIterator = $worksheet->getRowIterator(2);
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }


            $berhasilTotal = 0;

            for ($i = 0; $i < count($data); $i++) {

                $kodeSupplier = trim($data[$i][0]);
                $namaSupplier = trim($data[$i][1]);
                $alamat = trim($data[$i][2]);
                $kodePos = trim($data[$i][3]);
                $npwp = trim($data[$i][4]);
                $notelp = trim($data[$i][5]);
                $contactPerson = trim($data[$i][6]);
                $email = trim($data[$i][7]);

                if ($kodeSupplier != null || $kodeSupplier != "") {
                    $this->supplierModel->insert([
                        'company_id' => $this->this_company_id,
                        'user_id' => $this->this_user_id,
                        'kode' => $kodeSupplier,
                        'name' => $namaSupplier,
                        'address' => $alamat,
                        'no_npwp' => $npwp,
                        'phone' => $notelp,
                        'type' => $type,
                        'contact_person' => $contactPerson,
                        'postal_code' => $kodePos,
                        'email' => $email,
                        'user_id' => $this->this_user_id
                    ]);

                    $berhasilTotal++;
                }
            }

            return response()->setJSON([
                'message' => "Berhasil Import : $berhasilTotal Data",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } else {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJSON($data);
        }
    }

    public function exportExcel()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
        ];

        $condition = [
            "suppliers.type" => $this->request->getVar('type'),
            "company_id" => $this->this_company_id
        ];

        $addCondition = [
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            'search'    => ""
        ];

        $supplierData = $this->supplierModel->getSupplierList($condition, $addCondition, 10000000, 0);

        $list = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            array_push($list, [
                "no"            => $no++,
                "kode"          => $data->kode,
                "name"          => $data->name,
                "address"       => $data->address,
                "postal_code"   => $data->postal_code,
                "npwp"          => $data->no_npwp,
                "phone"         => $data->phone,
                "contact_person" => $data->contact_person,
                "email" => $data->email
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = 2;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'KODE SUPPLIER')
            ->setCellValue('B1', 'NAMA SUPPLIER')
            ->setCellValue('C1', 'ALAMAT')
            ->setCellValue('D1', 'KODE POS')
            ->setCellValue('E1', 'NPWP')
            ->setCellValue('F1', 'NO TELPON')
            ->setCellValue('G1', 'CONTACT PERSON')
            ->setCellValue('H1', 'EMAIL');



        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $l['kode'])
                ->setCellValue('B' . $column, $l['name'])
                ->setCellValue('C' . $column, $l['address'])
                ->setCellValue('D' . $column, $l['postal_code'])
                ->setCellValue('E' . $column, $l['npwp'])
                ->setCellValue('F' . $column, $l['phone'])
                ->setCellValue('G' . $column, $l['contact_person'])
                ->setCellValue('H' . $column, $l['email']);

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Export_Data_Supplier';
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
