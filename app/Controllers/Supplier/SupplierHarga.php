<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\SupplierHargaModel;

class SupplierHarga extends BaseController
{
    protected $SupplierHargaModel;
    protected $BarangMasterSpesifikasiModel;
    protected $BarangMasterModel;

    public function __construct()
    {
        $this->SupplierHargaModel = new SupplierHargaModel();
        $this->BarangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->BarangMasterModel = new BarangMasterModel();
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
            "supplier_harga.supplier_id"  => $this->request->getGet("supplier_id")
        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->SupplierHargaModel->getList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            array_push($dataSupplier, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "bahan_baku"        => $data->bahan_baku_id,
                "spesifikasi_id"    => $data->spesifikasi_id,
                "divisi_id"         => $data->divisi_id,
                "divisi"         => $data->divisi,
                "bahan_baku_name"   => $data->bahan_baku_name,
                "spesifikasi"       => $data->spesifikasi,
                "harga_umum_normal" => $data->harga_umum,
                "harga_bulanan_normal" => $data->harga_bulanan,
                "harga_harian_normal" => $data->harga_harian,
                "harga_umum"        =>  number_format($data->harga_umum, 2, '.', ','),
                "harga_harian"      =>  number_format($data->harga_harian, 2, '.', ','),
                "harga_bulanan"     =>  number_format($data->harga_bulanan, 2, '.', ',')
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
        $checkDuplicate = $this->SupplierHargaModel->where('divisi_id', $this->request->getVar('divisi_id'))
            ->where('bahan_baku_id', $this->request->getVar('bahan_baku'))
            ->where('spesifikasi_id', $this->request->getVar('spesifikasi_id'))
            ->where('supplier_id', $this->request->getVar('supplier_id'))
            ->where('deletedAt', null)
            ->first();

        if ($checkDuplicate != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Spesifikasi harga barang sudah diset"
            ]);
        }

        $barangMaster = $this->BarangMasterModel->find($this->request->getVar('bahan_baku'));
        $barangSpesifikasi = $this->BarangMasterSpesifikasiModel->find($this->request->getVar('spesifikasi_id'));

        if ($barangMaster == null || $barangSpesifikasi == null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Terjadi kesalahan pada sistem"
            ]);
        }

        $this->SupplierHargaModel->insert([
            'divisi_id' => $this->request->getVar('divisi_id'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'bahan_baku_id' => $this->request->getVar('bahan_baku'),
            'spesifikasi_id' => $this->request->getVar('spesifikasi_id'),
            'spesifikasi' => $barangSpesifikasi['spesifikasi'],
            'nama_barang' => $barangMaster['barang_name'] . " " . $barangSpesifikasi['spesifikasi'],
            "harga_umum"       => formatter($this->request->getVar('harga_umum'), "CURR_TO_FLOAT"),
            "harga_harian"     => formatter($this->request->getVar('harga_harian'), "CURR_TO_FLOAT"),
            "harga_bulanan"    => formatter($this->request->getVar('harga_bulanan'), "CURR_TO_FLOAT")
        ]);
        return response()->setJSON([
            "status"    => true,
            "message"   => "Data Berhasil disimpan",
            'token'     => csrf_hash()
        ]);
    }

    public function updateSupplierHarga()
    {
        $id = decrypt($this->request->getVar('id'));

        $barangMaster = $this->BarangMasterModel->find($this->request->getVar('bahan_baku'));
        $barangSpesifikasi = $this->BarangMasterSpesifikasiModel->find($this->request->getVar('spesifikasi_id'));

        if ($barangMaster == null || $barangSpesifikasi == null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Terjadi kesalahan pada sistem"
            ]);
        }


        $this->SupplierHargaModel->update($id, [
            'divisi_id' => $this->request->getVar('divisi_id'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'bahan_baku_id' => $this->request->getVar('bahan_baku'),
            'spesifikasi_id' => $this->request->getVar('spesifikasi_id'),
            'spesifikasi' => $barangSpesifikasi['spesifikasi'],
            'nama_barang' => $barangMaster['barang_name'] . " " . $barangSpesifikasi['spesifikasi'],
            "harga_umum"       => formatter($this->request->getVar('harga_umum'), "CURR_TO_FLOAT"),
            "harga_harian"     => formatter($this->request->getVar('harga_harian'), "CURR_TO_FLOAT"),
            "harga_bulanan"    => formatter($this->request->getVar('harga_bulanan'), "CURR_TO_FLOAT")
        ]);

        return response()->setJSON([
            "status"    => true,
            "message"   => "Data Berhasil diupdate",
            'token'     => csrf_hash()
        ]);
    }

    public function deleteSupplierHarga()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->SupplierHargaModel->delete($id);

        return response()->setJSON([
            "status"    => true,
            "message"   => "Data Berhasil dihapus",
            'token'     => csrf_hash()
        ]);
    }

    public function getListSpesifikasiBarang()
    {
        $id = $this->request->getVar('id');
        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $this->BarangMasterSpesifikasiModel->getBarangSpesifikasiByBarangMasterID($id),
        ]);
    }

    // public function supplierHargaAjax()
    // {
    //     $id = $this->request->getGet("id");

    //     $find = $this->SupplierHargaModel->getBySupplierId($id);

    //     if ($find) {
    //         return json_encode([
    //             "data" => $find
    //         ]);
    //     } else {
    //         return json_encode([
    //             "data" => []
    //         ]);
    //     }
    // }

    // public function getByIdSupplierHarga($id)
    // {
    //     $supplierData = $this->SupplierHargaModel->asObject()->find($id);

    //     if (!$supplierData) {
    //         $data = [
    //             "status"    => false,
    //             "message"   => 'Not Found!'
    //         ];
    //         echo json_encode($data);
    //         return;
    //     }

    //     $data = [
    //         "status"    => true,
    //         "data"      => $supplierData,
    //     ];
    //     echo json_encode($data);

    //     return;
    // }

    // public function getByIdSupplier($id)
    // {
    //     $supplierData = $this->SupplierHargaModel->getBySupplierId($id);

    //     if (!$supplierData) {
    //         $data = [
    //             "status"    => false,
    //             "message"   => 'Not Found!'
    //         ];
    //         echo json_encode($data);
    //         return;
    //     }

    //     $data = [
    //         "status"    => true,
    //         "data"      => $supplierData,
    //     ];
    //     echo json_encode($data);

    //     return;
    // }

    // public function getByBarangandSupplierId()
    // {
    //     $barang_id = $this->request->getGet("barang_id");
    //     $supplier_id = $this->request->getGet("supplier_id");

    //     $find = $this->SupplierHargaModel->getByBarangandSupplier($barang_id, $supplier_id);

    //     if ($find) {
    //         return json_encode([
    //             "data" => $find
    //         ]);
    //     } else {
    //         return json_encode([
    //             "data" => []
    //         ]);
    //     }
    // }


}
