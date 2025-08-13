<?php

namespace App\Controllers\BiayaExim\Vendor;

use App\Controllers\BaseController;
use App\Models\VendorPelayaranModel;

class VendorPelayaran extends BaseController
{
    protected $vendorPelayaranModel;
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->vendorPelayaranModel = new VendorPelayaranModel();
    }

    public function index()
    {
        return view('BiayaExim/Vendor/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "company_id"  => $this->this_company_id,
            "deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataResult = $this->vendorPelayaranModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $vendorResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataResult['data'] as $data) {
            array_push($vendorResult, [
                "no"                => $no++,
                "id"                => $data['id'],
                "nama_vendor"       => $data['nama_vendor'],
                "alamat"            => $data['alamat'],
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $dataResult['totalData'],
            "recordsFiltered"   => $dataResult['totalFilteredData'],
            "data"              => $vendorResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function store()
    {
        $namaVendor = $this->request->getVar('nama_vendor');
        $alamat = $this->request->getVar('alamat');

        $namaVendorFirst = $this->vendorPelayaranModel
            ->where('company_id', $this->this_company_id)
            ->where('nama_vendor', $namaVendor)
            ->first();

        if ($namaVendorFirst != null) {
            return response()->setJSON([
                'message' => "Nama vendor / pelayaran sudah digunakan",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $this->vendorPelayaranModel->insert([
            'company_id' => $this->this_company_id,
            'nama_vendor' => $namaVendor,
            'alamat' => $alamat
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Vendor / Pelayaran Berhasil Disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function update()
    {
        $id = $this->request->getVar('id');
        $namaVendor = $this->request->getVar('nama_vendor');
        $alamat = $this->request->getVar('alamat');

        $namaVendorFirst = $this->vendorPelayaranModel
            ->where('company_id', $this->this_company_id)
            ->where('id !=', $id)
            ->where('nama_vendor', $namaVendor)
            ->first();

        if ($namaVendorFirst != null) {
            return response()->setJSON([
                'message' => "Nama vendor / pelayaran sudah digunakan",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $this->vendorPelayaranModel->update($id, [
            'nama_vendor' => $namaVendor,
            'alamat' => $alamat
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Vendor / Pelayaran Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function destroy()
    {
        $id = $this->request->getVar('id');
        $this->vendorPelayaranModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Vendor / Pelayaran Berhasil Dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get()
    {
        $id = $this->request->getVar('id');
        $data = $this->vendorPelayaranModel->where('id', $id)->first();
        return response()->setJSON([
            'status' => true,
            'data' => $data,
            'token' => csrf_hash()
        ]);
    }
}
