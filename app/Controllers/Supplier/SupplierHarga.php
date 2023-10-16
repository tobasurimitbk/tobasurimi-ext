<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;
use App\Models\SupplierHargaModel;

class SupplierHarga extends BaseController
{
    protected $SupplierHargaModel;

    public function __construct()
    {
        $this->SupplierHargaModel = new SupplierHargaModel();
    }

    public function saveSupplierHarga()
    {
        try {
            $rules = [
                "bahan_baku" => [
                    "rules" => "required"
                ],
                "bagian" => [
                    "rules" => "required"
                ],
                "spesifikasi" => [
                    "rules" => "required"
                ],
                "harga_umum" => [
                    "rules" => "required"
                ],
                "harga_harian" => [
                    "rules" => "required"
                ],
                "harga_bulanan" => [
                    "rules" => "required"
                ],
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
                "supplier_id"      => $this->request->getPost("id_supplier"),
                "bahan_baku_id"       => $this->request->getPost("bahan_baku"),
                "bagian_id"           => $this->request->getPost("bagian"),
                "spesifikasi"      => $this->request->getPost("spesifikasi"),
                "harga_umum"       => formatter($this->request->getPost("harga_umum"), "CURR_TO_FLOAT"),
                "harga_harian"     => formatter($this->request->getPost("harga_harian"), "CURR_TO_FLOAT"),
                "harga_bulanan"    => formatter($this->request->getPost("harga_bulanan"), "CURR_TO_FLOAT")
            ];
            $insert = $this->SupplierHargaModel->insert($insertData);

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

    public function updateSupplierHarga()
    {
        try {
            $rules = [
                "bahan_baku" => [
                    "rules" => "required"
                ],
                "bagian" => [
                    "rules" => "required"
                ],
                "spesifikasi" => [
                    "rules" => "required"
                ],
                "harga_umum" => [
                    "rules" => "required"
                ],
                "harga_harian" => [
                    "rules" => "required"
                ],
                "harga_bulanan" => [
                    "rules" => "required"
                ],
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

            $id = $this->request->getPost("id_supplier_harga");

            $insertData = [
                "supplier_id"      => $this->request->getPost("id_supplier"),
                "bahan_baku_id"       => $this->request->getPost("bahan_baku"),
                "bagian_id"           => $this->request->getPost("bagian"),
                "spesifikasi"      => $this->request->getPost("spesifikasi"),
                "harga_umum"       => formatter($this->request->getPost("harga_umum"), "CURR_TO_FLOAT"),
                "harga_harian"     => formatter($this->request->getPost("harga_harian"), "CURR_TO_FLOAT"),
                "harga_bulanan"    => formatter($this->request->getPost("harga_bulanan"), "CURR_TO_FLOAT")
            ];
            $insert = $this->SupplierHargaModel->update($id, $insertData);

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

    public function supplierHargaAjax()
    {
        $id = $this->request->getGet("id");

        $find = $this->SupplierHargaModel->getBySupplierId($id);

        if($find)
        {
            return json_encode([
                "data" => $find
            ]);
        }
        else
        {
            return json_encode([
                "data" => []
            ]);
        }
    }

    public function getByIdSupplierHarga($id)
    {
        $supplierData = $this->SupplierHargaModel->asObject()->find($id);

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

    public function deleteSupplierHarga()
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

            $this->SupplierHargaModel->delete($id);
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
}