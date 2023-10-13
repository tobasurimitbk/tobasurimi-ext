<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\ParentBarangModel;
use App\Models\SatuansModel;
use Exception;

class Barang extends BaseController
{
    protected $this_company_id;
    private $kodeBahanBaku, $kodeBahanPenolong, $kodeBahanJadi, $kodeBahanScrap;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->kodeBahanBaku = "BB";
        $this->kodeBahanPenolong = "BP";
        $this->kodeBahanJadi = "BJ";
        $this->kodeBahanScrap = "BS";
    }

    public function bahanBakuView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $data = [
            'type' => "bahan_baku",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_baku")->findAll(),
            'satuanBarang' => $satuanModel->findAll()
        ];

        return view('Warehouse/barangMaster/bahanBaku', $data);
    }

    public function bahanPenolongView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $data = [
            'type' => "bahan_penolong",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_penolong")->findAll(),
            'satuanBarang' => $satuanModel->findAll()
        ];

        return view('Warehouse/barangMaster/bahanPenolong', $data);
    }

    public function bahanJadiView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $data = [
            'type' => "bahan_jadi",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_jadi")->findAll(),
            'satuanBarang' => $satuanModel->findAll()
        ];

        return view('Warehouse/barangMaster/bahanJadi', $data);
    }

    public function bahanScrapView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $data = [
            'type' => "bahan_scrap",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_scrap")->findAll(),
            'satuanBarang' => $satuanModel->findAll()
        ];

        return view('Warehouse/barangMaster/bahanScrap', $data);
    }

    public function create()
    {
        $barangModel = new BarangMasterModel();
        $type = $this->request->getVar('type');

        $barangModel->insert([
            'company_id' => $this->this_company_id,
            'satuan_id' => $this->request->getVar('satuan_id'),
            'parent_type_id' => $this->request->getVar('parent_type_id'),
            'kode_barang' => $this->request->getVar('kode_barang'),
            'barang_name' => $this->request->getVar('barang_name'),
            'type_barang' => $type,
            'stok' => 0
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => \csrf_hash(),
            'message' => "Barang baru berhasil ditambahkan"
        ]);
    }

    public function update()
    {
        $id = $this->request->getVar('id');
        $barangModel = new BarangMasterModel();
        $type = $this->request->getVar('type');

        $barangModel->update($id, [
            'company_id' => $this->this_company_id,
            'satuan_id' => $this->request->getVar('satuan_id'),
            'parent_type' => $this->request->getVar('parent_type'),
            'barang_name' => $this->request->getVar('barang_name'),
            'type_barang' => $type,
        ]);

        return response()->setJSON([
            'status' => \true,
            'token' => \csrf_hash(),
            'message' => "Barang baru berhasil diupdate"
        ]);
    }

    public function delete()
    {
        $id = $this->request->getVar('id');
        $barangModel = new BarangMasterModel();

        $barangModel->update($id, [
            'deletedAt' => date('Y-m-d H:i:s')
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Barang berhasil dihapus",
            'token' => \csrf_hash()
        ]);
    }

    public function get()
    {
        $barangModel = new BarangMasterModel();
        $id = $this->request->getVar('id');

        return \response()->setJSON([
            'token' => \csrf_hash(),
            'data' => $barangModel->where('id', $id)->where('deletedAt', null)->first()
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
            "barang_master.company_id"  => $this->this_company_id,
            "barang_master.type_barang" => $this->request->getGet('parent_type'),
            "barang_master.deletedAt" => NULL
        ];

        $addCondition = [
            'search' => $this->request->getGet('search'),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $barangMasterModel = new BarangMasterModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $barangMasterModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data['id'],
                "kelompok_barang"       => $data['kelompok_barang'],
                "kode_barang"           => $data['kode_barang'],
                "barang_name"           => $data['barang_name'],
                "satuan"                => $data['satuan'],
                "stok"                  => $data['stok'],
                "harga_terakhir"        => "Rp. 0.00", // belum selesai (khusus master data bahan penolong)
                "supplier_terakhir"     => "-" // belum selesai (khusus master data bahan penolong)
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }

    public function generateNewCode()
    {
        $barangModel = new BarangMasterModel();
        $type = $this->request->getVar('type');
        $codeName = "";

        if ($type == "bahan_baku") {
            $codeName = $this->kodeBahanBaku;
        } elseif ($type == "bahan_penolong") {
            $codeName = $this->kodeBahanPenolong;
        } elseif ($type == "bahan_jadi") {
            $codeName = $this->kodeBahanJadi;
        } else {
            $codeName = $this->kodeBahanScrap;
        }

        $lastBarang = $barangModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', $type)
            ->where('deletedAt', null)
            ->like('kode_barang', $codeName . '-____')
            ->orderBy('createdAt', 'DESC')
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
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $codeName . "-????",
                'token' => csrf_hash()
            ]);
        }
    }
}
