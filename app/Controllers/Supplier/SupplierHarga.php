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

    public function supplierHargaAll()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "id"            => $this->request->getGet("id")
        ];

        $condition = [
            "supplier_harga.supplier_id"     => $this->request->getGet("id")
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->SupplierHargaModel->getList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            array_push($dataSupplier, [
                "no"                => $no++,
                "bahan_baku_name"   => $data->bahan_baku_name,
                "createdAt"         => $data->createdAt ? date("d/m/Y", strtotime($data->createdAt)) : "",
                "spesifikasi"       => $data->spesifikasi,
                "harga_umum"        => "Rp " . number_format($data->harga_umum, 2, '.', ','),
                "harga_harian"      => "Rp " . number_format($data->harga_harian, 2, '.', ','),
                "harga_bulanan"     => "Rp " . number_format($data->harga_bulanan, 2, '.', ',')
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

    public function saveSupplierHarga()
    {
        try {
            $list_item = json_decode($this->request->getPost("list_item"));
            $list_delete = json_decode($this->request->getPost("list_delete"));

            // for create and update
            foreach($list_item as $item)
            {
                $insertData = [
                    "supplier_id"      => $this->request->getPost("supplier_id"),
                    "bahan_baku_id"    => $item->bahan_baku_id,
                    "spesifikasi"      => $item->spesifikasi,
                    "harga_umum"       => formatter($item->harga_umum, "CURR_TO_FLOAT"),
                    "harga_harian"     => formatter($item->harga_harian, "CURR_TO_FLOAT"),
                    "harga_bulanan"    => formatter($item->harga_bulanan, "CURR_TO_FLOAT")
                ];

                // update
                if($item->id) {
                    $insert = $this->SupplierHargaModel->update($item->id, $insertData);

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
                }
                // create
                else {
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
                }
            }

            // for delete
            foreach($list_delete as $item)
            {
                $id = $item->id;

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

    public function getByIdSupplier($id)
    {
        $supplierData = $this->SupplierHargaModel->getBySupplierId($id);

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
}