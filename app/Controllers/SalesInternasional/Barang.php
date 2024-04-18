<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\MetadataModel;
use App\Models\SatuansModel;
use Exception;

class Barang extends BaseController
{
    protected $this_company_id;
    protected $satuanModel;
    protected $barangMasterSalesModel;
    protected $metaDataModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->satuanModel = new SatuansModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->metaDataModel = new MetadataModel();
    }

    public function bahanJadiView()
    {
        $data = [
            'satuan' => $this->satuanModel->findAll(),
        ];

        return view('SalesInternasional/barangMaster/index', $data);
    }

    public function create()
    {
        $kodeBarang = $this->barangMasterSalesModel->where('kode_barang', $this->request->getVar('barang_name'))->where('company_id', $this->this_company_id)->where('type_barang_sales', "EKSPOR")->first();
        if ($kodeBarang != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kode barang " . $kodeBarang['kode_barang'] . " sudah ada"
            ]);
        }

        $namaBarang = $this->barangMasterSalesModel->where('barang_name', strtoupper($this->request->getVar('barang_name')))->where('company_id', $this->this_company_id)->where('type_barang_sales', "EKSPOR")->first();
        if ($namaBarang != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Nama barang " . $namaBarang['barang_name'] . " sudah ada"
            ]);
        }

        $this->barangMasterSalesModel->insert([
            'company_id' => $this->this_company_id,
            'kode_barang' => $this->request->getVar('kode_barang'),
            'barang_name' => strtoupper($this->request->getVar('barang_name')),
            'type_barang_sales' => "EKSPOR",
            'type_barang' => "bahan_jadi",
            'satuan_id' => $this->request->getVar('satuan_id'),
            'harga_pokok' => repairDouble($this->request->getVar('harga_pokok')),
            'harga_jual' => repairDouble($this->request->getVar('harga_jual'))
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Barang berhasil ditambahkan"
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));

        $check = $this->barangMasterSalesModel
            ->where('barang_name', strtoupper($this->request->getVar('barang_name')))
            ->where('company_id', $this->this_company_id)->where('type_barang_sales', "EKSPOR")
            ->where('id !=', $id)
            ->first();


        if ($check) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nama barang sudah ada.",
                'token' => csrf_hash()
            ]);
        }


        $this->barangMasterSalesModel->update($id, [
            'company_id' => $this->this_company_id,
            'kode_barang' => $this->request->getVar('kode_barang'),
            'barang_name' => strtoupper($this->request->getVar('barang_name')),
            'type_barang_sales' => "EKSPOR",
            'satuan_id' => $this->request->getVar('satuan_id'),
            'harga_pokok' => repairDouble($this->request->getVar('harga_pokok')),
            'harga_jual' => repairDouble($this->request->getVar('harga_jual'))
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Barang berhasil diupdate"
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->barangMasterSalesModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Barang berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $data = $this->barangMasterSalesModel->find($id);
        $data['id'] = encrypt($data['id']);
        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $data
        ]);
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
            "barang_master_sales.company_id"  => $this->this_company_id,
            "barang_master_sales.type_barang_sales" => "EKSPOR",
            "barang_master_sales.type_barang" => "bahan_jadi",
            "barang_master_sales.deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "type_barang"   => $this->request->getGet("type_barang"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataResult = $this->barangMasterSalesModel->getList($condition, $addCondition, $limit, $offset);

        $barangResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataResult['data'] as $data) {
            array_push($barangResult, [
                "no"                => $no++,
                "id"                => encrypt($data['id']),
                "kode_barang"       => $data['kode_barang'],
                "barang_name"       => $data['barang_name'],
                "type_barang_sales" => $data['type_barang_sales'],
                'kode_satuan'       => $data['kode_satuan'],
                "type_barang"       => strtoupper(str_replace('_', ' ', $data['type_barang'])),
                "harga_pokok"       => number_format($data['harga_pokok']),
                "harga_jual"        => number_format($data['harga_jual']),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $dataResult['totalData'],
            "recordsFiltered"   => $dataResult['totalFilteredData'],
            "data"              => $barangResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function generateNewCode()
    {
        $type = $this->request->getVar('type_barang');
        if (empty($type)) {
            return response()->setJSON([
                'codeNew' => "",
                'token' => csrf_hash(),
            ]);
        }

        $codeName = $this->metaDataModel->where('name', "Tipe Barang Sales Ekspor")->where('value', $type)->first()['description'];

        $lastBarang = $this->barangMasterSalesModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', $type)
            ->where('type_barang_sales', "EKSPOR")
            ->where('deletedAt', null)
            ->like('kode_barang', $codeName . '-____')
            ->orderBy('kode_barang', 'DESC')
            ->first();

        if (empty($lastBarang)) {
            return response()->setJSON([
                'codeNew' => "$codeName-0001",
                'token' => csrf_hash(),
            ]);
        }
        try {

            $lastCode = $lastBarang->kode_barang;
            $lastCodeExp = explode('-', $lastCode);
            $lastIncrement = (int)$lastCodeExp[1];
            $newIncrement = str_pad(($lastIncrement + 1), 4, '0', STR_PAD_LEFT);

            return response()->setJSON([
                'codeNew' => $codeName . "-" . $newIncrement,
                'token' => csrf_hash(),
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $codeName . "-????",
                'token' => csrf_hash()
            ]);
        }
    }
}
