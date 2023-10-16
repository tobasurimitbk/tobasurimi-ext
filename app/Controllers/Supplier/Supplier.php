<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;
use App\Models\SupplierModel;

class Supplier extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    // bahan baku
    public function supplierBahanBaku()
    {
        return view('Supplier/supplierBahanBaku/index');
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
            "type"          => "BAHAN BAKU"
        ];

        $supplierModel = new SupplierModel();
        $condition = [
            "suppliers.company_id"  => $this->this_company_id,
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
            $supplierModel = new SupplierModel();

            $rules = [
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "permit_empty|string"
                ],
                "no_npwp" => [
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

            $payload = json_encode([
                "company_id"        => $this->this_company_id,
                "name"              => $this->request->getPost("name"),
                "address"           => $this->request->getPost("address"),
                "no_npwp"           => $this->request->getPost("no_npwp"),
                "type"              => "BAHAN BAKU"
            ]);

            $insertData = [
                "company_id"        => $this->this_company_id,
                "name"              => $this->request->getPost("name"),
                "address"           => $this->request->getPost("address"),
                "no_npwp"           => $this->request->getPost("no_npwp"),
                "type"              => "BAHAN BAKU"
            ];
            $insert = $supplierModel->insert($insertData);

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
            $supplierModel = new SupplierModel();

            $rules = [
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "permit_empty|string"
                ],
                "no_npwp" => [
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
                    "company_id"        => $this->this_company_id,
                    "name"              => $this->request->getPost("name"),
                    "address"           => $this->request->getPost("address"),
                    "no_npwp"           => $this->request->getPost("no_npwp"),
                    "type"              => "BAHAN BAKU"
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
        return view('Supplier/supplierBahanPenolong/index');
    }

    public function allSupplierBahanPenolong()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "type"          => "BAHAN PENOLONG"
        ];

        $supplierModel = new SupplierModel();
        $condition = [
            "suppliers.company_id"  => $this->this_company_id,
            "suppliers.type"        => "BAHAN PENOLONG"
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
            $supplierModel = new SupplierModel();

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
                "name" => $this->request->getPost("name"),
                "address" => $this->request->getPost("address"),
                "no_npwp" => $this->request->getPost("no_npwp"),
                "phone" => $this->request->getPost("phone"),
                "type" => "BAHAN PENOLONG"
            ]);

            $insertData = [
                "company_id"        => $this->this_company_id,
                "name"              => $this->request->getPost("name"),
                "address"           => $this->request->getPost("address"),
                "no_npwp"           => $this->request->getPost("no_npwp"),
                "phone"             => $this->request->getPost("phone"),
                "type"              => "BAHAN PENOLONG"
            ];
            $insert = $supplierModel->insert($insertData);

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
            $supplierModel = new SupplierModel();

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
                    "name"              => $this->request->getPost("name"),
                    "address"           => $this->request->getPost("address"),
                    "no_npwp"           => $this->request->getPost("no_npwp"),
                    "phone"             => $this->request->getPost("phone")
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

        $supplierData->list_address = []; // cek nanti
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
        $supplierModel = new SupplierModel();
        $dataSupplier = $supplierModel->getSupplierByKategoriAndType('', 'BAHAN BAKU', $this->this_company_id);

        $data = [
            "data" => $dataSupplier
        ];

        echo json_encode($data);
        return;
    }

    public function supplierAjax()
    {
        $supplierModel = new SupplierModel();
        $id = $this->request->getGet("id");

        if (!empty($id)) {
            $response = $supplierModel->getSupplierById($id);
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
}
