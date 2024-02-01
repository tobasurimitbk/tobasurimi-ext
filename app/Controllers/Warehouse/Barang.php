<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\ParentBarangModel;
use App\Models\SatuansModel;
use App\Models\DivisisModel;
use Exception;

class Barang extends BaseController
{
    protected $this_company_id;
    private $kodeBahanBaku, $kodeBahanPenolong, $kodeBahanJadi, $kodeBahanScrap, $kodeBahanModal;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->kodeBahanBaku = "BB";
        $this->kodeBahanPenolong = "BP";
        $this->kodeBahanJadi = "BJ";
        $this->kodeBahanScrap = "BS";
        $this->kodeBahanModal = "BM";
    }

    public function bahanBakuView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $data = [
            'type' => "bahan_baku",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_baku")->where('deletedAt', null)->findAll(),
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
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_penolong")->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->findAll()
        ];

        return view('Warehouse/barangMaster/bahanPenolong', $data);
    }

    public function bahanJadiView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $divisisModel = new DivisisModel();
        $divisisModelData = $divisisModel->asObject()->where('deletedAt', null)->findAll();

        $data = [
            'type' => "bahan_jadi",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_jadi")->where('deletedAt', null)->findAll(),
            'divisi' => $divisisModelData,
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
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_scrap")->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->findAll()
        ];

        return view('Warehouse/barangMaster/bahanScrap', $data);
    }

    public function bahanModalView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $data = [
            'type' => "bahan_modal",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_modal")->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->findAll()
        ];

        return view('Warehouse/barangMaster/bahanModal', $data);
    }

    public function create()
    {
        $barangModel = new BarangMasterModel();
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $result = array();
        $type = $this->request->getVar('type');
        $spek = $this->request->getPost('spek');

        $barang = $barangModel->where('kode_barang', $this->request->getVar('kode_barang'))
            ->where('type_barang', $type)
            ->where('deletedAt', null)
            ->first();

        if ($barang != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kode barang sudah ada"
            ]);
        }

        $barangMasterID = $barangModel->insert([
            'company_id' => $this->this_company_id,
            'satuan_id' => decrypt($this->request->getVar('satuan_id')),
            'parent_type_id' => decrypt($this->request->getVar('parent_type_id')),
            // 'divisi_id' => decrypt($this->request->getVar('divisi_id')),
            'kode_barang' => $this->request->getVar('kode_barang'),
            'barang_name' => $this->request->getVar('barang_name'),
            'type_barang' => $type,
            'minimum_stock' => str_replace('.', '', $this->request->getVar('minimum_stock')),
            'harga_pokok' => $this->request->getVar('harga_pokok') ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $this->request->getVar('harga_pokok'))) : 0,
            'harga_jual' => $this->request->getVar('harga_jual') ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $this->request->getVar('harga_jual'))) : 0,
        ]);

        foreach ($spek as $key => $value) {
            // var_dump($_POST['primer'][$key]);
            $result[] = array(
                'barang_master_id' => $barangMasterID,
                'spesifikasi' => $_POST['spek'][$key],
                'satuan_1' => decrypt($_POST['satuan1_id'][$key]),
                'satuan_2' => decrypt($_POST['satuan2_id'][$key]),
                'konversi_satuan_2' => $_POST['konversi_satuan_2'][$key],
                'satuan_3' => decrypt($_POST['satuan3_id'][$key]),
                'konversi_satuan_3' => $_POST['konversi_satuan_3'][$key],
            );
        }
        $barangSpesifikasiModel->insertBatch($result);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Barang baru berhasil ditambahkan"
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $barangModel = new BarangMasterModel();
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $result = array();
        $type = $this->request->getVar('type');
        $spek = $this->request->getPost('spek');

        $barangModel->update($id, [
            'company_id' => $this->this_company_id,
            'satuan_id' => decrypt($this->request->getVar('satuan_id')),
            'parent_type_id' => decrypt($this->request->getVar('parent_type_id')),
            // 'divisi_id' => decrypt($this->request->getVar('divisi_id')),
            'barang_name' => $this->request->getVar('barang_name'),
            'type_barang' => $type,
            'minimum_stock' => str_replace('.', '', $this->request->getVar('minimum_stock')),
            'harga_pokok' => $this->request->getVar('harga_pokok') ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $this->request->getVar('harga_pokok'))) : 0,
            'harga_jual' => $this->request->getVar('harga_jual') ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $this->request->getVar('harga_jual'))) : 0,
        ]);
        $check = $barangSpesifikasiModel->asObject()->where('barang_master_id', $id)->findAll();
        if ($check) {
            foreach ($check as $value) {
                $barangSpesifikasiModel->delete($value->id);
            }
        }

        foreach ($spek as $key => $value) {
            $result[] = array(
                'barang_master_id' => $id,
                'spesifikasi' => $_POST['spek'][$key],
                'satuan_1' => decrypt($_POST['satuan1_id'][$key]),
                'satuan_2' => decrypt($_POST['satuan2_id'][$key]),
                'konversi_satuan_2' => $_POST['konversi_satuan_2'][$key],
                'satuan_3' => decrypt($_POST['satuan3_id'][$key]),
                'konversi_satuan_3' => $_POST['konversi_satuan_3'][$key],
            );
        }
        $barangSpesifikasiModel->insertBatch($result);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Barang baru berhasil diupdate"
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $barangModel = new BarangMasterModel();
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();

        $barangModel->update($id, [
            'deletedAt' => date('Y-m-d H:i:s')
        ]);

        $check = $barangSpesifikasiModel->asObject()->where('barang_master_id', $id)->findAll();
        if ($check) {
            foreach ($check as $value) {
                $barangSpesifikasiModel->update($value->id, [
                    'deletedAt' => date('Y-m-d H:i:s')
                ]);
            }
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Barang berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get()
    {
        $barangModel = new BarangMasterModel();
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $id = decrypt($this->request->getVar('id'));
        $res = $barangModel->where('id', $id)->where('deletedAt', null)->first();
        $res['id'] = encrypt($res['id']);
        $res['company_id'] = encrypt($res['company_id']);
        $res['parent_type_id'] = encrypt($res['parent_type_id']);
        $spekDetail = $barangSpesifikasiModel->getBarangSpesifikasiByBarangMasterID($id);

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $res,
            'dataSpekDetail' => $spekDetail,
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
        $amPurchaseOrderModel = new AMPurchaseOrderModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $barangMasterModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $lokalDetail = $amPurchaseOrderModel->historiHargaPOBahanPenolongFirst($data['id'], "Lokal", $this->this_company_id);
            $importDetail = $amPurchaseOrderModel->historiHargaPOBahanPenolongFirst($data['id'], "Import", $this->this_company_id);
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "kelompok_barang"       => $data['kelompok_barang'],
                "kode_barang"           => $data['kode_barang'],
                "barang_name"           => $data['barang_name'],
                "satuan"                => $data['satuan'],
                "harga_terakhir_lokal"   => $lokalDetail['hargaTerakhir'],
                "supplier_terakhir_lokal" => $lokalDetail['supplierTerakhir'],
                "harga_terakhir_import" => $importDetail['hargaTerakhir'],
                "supplier_terakhir_import" => $importDetail['supplierTerakhir']
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
        } elseif ($type == "bahan_scrap") {
            $codeName = $this->kodeBahanScrap;
        } else {
            $codeName = $this->kodeBahanModal;
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
                'token' => csrf_hash(),

            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $codeName . "-????",
                'token' => csrf_hash()
            ]);
        }
    }

    public function historiHargaPOBahanPenolong()
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "am_purchase_orders.company_id"  => $this->this_company_id,
            "am_purchase_order_details.barang_id" => $this->request->getVar('id'),
            "am_purchase_orders.deletedAt" => NULL,
            "am_purchase_order_details.deletedAt" => NULL,
            "am_purchase_orders.po_type" => $this->request->getVar('po_type')
        ];

        $addCondition = [
            'search' => $this->request->getGet('search'),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $amPurchaseOrderModel->historiHargaPOBahanPenolong($condition, $addCondition, $limit, $offset);

        $rdata = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "po_no"                 => $data['po_no'],
                "po_date"               => date('d/m/Y', strtotime($data['po_date'])),
                "nama_supplier"         => $data['nama_supplier'],
                "nama_barang"           => $data['nama_barang'],
                "price"                 => number_format($data['price'], 2, ',', '.'),
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

    public function dropdownBarangType()
    {
        $barangModel = new BarangMasterModel();

        $type = $this->request->getGet("type");
        $condition = [
            'barang_master.company_id' => $this->this_company_id,
            'barang_master.type_barang' => $type,
        ];
        $dataBarang = $barangModel->getBarangByTypeWithSpec($condition);

        for ($i = 0; $i < count($dataBarang); $i++) {
            $dataBarang[$i]['id'] = encrypt($dataBarang[$i]['id']);
            $dataBarang[$i]['parent_type_id'] = encrypt($dataBarang[$i]['parent_type_id']);
            $dataBarang[$i]['barang_master_spesifikasi_id'] = encrypt($dataBarang[$i]['barang_master_spesifikasi_id']);
            $dataBarang[$i]['barang_name'] = strtoupper($dataBarang[$i]['barang_name_master'] . ' ' . $dataBarang[$i]['spesifikasi']);
        }

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }

    public function getBySupplier($id)
    {
        $barangModel = new BarangMasterModel();

        $dataBarang = $barangModel->getBySupplier($id);

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }
}
