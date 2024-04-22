<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Controllers\Master\Satuan;
use App\Models\AccountBarangModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\ParentBarangModel;
use App\Models\SatuansModel;
use App\Models\DivisisModel;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
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
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
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
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
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
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
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
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
        ];

        return view('Warehouse/barangMaster/bahanModal', $data);
    }

    public function create()
    {
        $barangModel = new BarangMasterModel();
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $result = array();
        $type = $this->request->getVar('type');
        $spek = json_decode($this->request->getVar("items"));

        $barang = $barangModel->where('kode_barang', $this->request->getVar('kode_barang'))
            ->where('company_id', $this->this_company_id)
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

        $barangName = $barangModel->where('UPPER(barang_name)', strtoupper($this->request->getVar('barang_name')))
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', $type)
            ->where('deletedAt', null)
            ->first();

        if ($barangName != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Nama barang sudah ada"
            ]);
        }

        $barangMasterID = $barangModel->insert([
            'company_id' => $this->this_company_id,
            'parent_type_id' => decrypt($this->request->getVar('parent_type_id')) == 0 ? $this->request->getVar('parent_type_id') : decrypt($this->request->getVar('parent_type_id')),
            // 'divisi_id' => decrypt($this->request->getVar('divisi_id')),
            'kode_barang' => $this->request->getVar('kode_barang'),
            'barang_name' => strtoupper($this->request->getVar('barang_name')),
            'type_barang' => $type,
            'minimum_stock' => str_replace('.', '', $this->request->getVar('minimum_stock')),
        ]);

        foreach ($spek as $value) {
            // var_dump($_POST['primer'][$key]);
            $harga_jual = 0.0;
            $harga_pokok = 0.0;
            if (isset($value->harga_pokok) && $value->harga_pokok) {
                $harga_pokok = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_pokok));
            }

            if (isset($value->harga_jual) && $value->harga_jual) {
                $harga_jual = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_jual));
            }
            $result[] = array(
                'barang_master_id' => $barangMasterID,
                'spesifikasi' => strtoupper($value->spesifikasi),
                'satuan_1' => $value->satuan_1,
                'satuan_2' => $value->satuan_2 != "" ? $value->satuan_2 : 0,
                'konversi_satuan_2' => $value->konversi_satuan_2 ? $value->konversi_satuan_2 : 1,
                'satuan_3' => $value->satuan_3 != "" ? $value->satuan_3 : 0,
                'konversi_satuan_3' => $value->konversi_satuan_3 ? $value->konversi_satuan_3 : 1,
                'harga_pokok' => $harga_pokok,
                'harga_jual' => $harga_jual,
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
        $spek = json_decode($this->request->getVar("items"));

        $barangModel->update($id, [
            'company_id' => $this->this_company_id,
            'parent_type_id' => decrypt($this->request->getVar('parent_type_id')),
            // 'divisi_id' => decrypt($this->request->getVar('divisi_id')),
            'barang_name' => strtoupper($this->request->getVar('barang_name')),
            'type_barang' => $type,
            'minimum_stock' => str_replace('.', '', $this->request->getVar('minimum_stock')),
        ]);
        if ($spek) {
            foreach ($spek as $key => $value) {
                if ($value->spesifikasi_id) {
                    $harga_jual = 0.0;
                    $harga_pokok = 0.0;
                    if (isset($value->harga_pokok) && $value->harga_pokok) {
                        $harga_pokok = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_pokok));
                    }

                    if (isset($value->harga_jual) && $value->harga_jual) {
                        $harga_jual = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_jual));
                    }
                    $barangSpesifikasiModel->update($value->spesifikasi_id, [
                        'spesifikasi' => strtoupper($value->spesifikasi),
                        'satuan_1' => $value->satuan_1,
                        'satuan_2' => $value->satuan_2 != "" ? $value->satuan_2 : 0,
                        'konversi_satuan_2' => $value->konversi_satuan_2 ? $value->konversi_satuan_2 : 1,
                        'satuan_3' => $value->satuan_3 != "" ? $value->satuan_3 : 0,
                        'konversi_satuan_3' => $value->konversi_satuan_3 ? $value->konversi_satuan_3 : 1,
                        'harga_pokok' => $harga_pokok,
                        'harga_jual' => $harga_jual,
                    ]);
                } else {
                    $harga_jual = 0.0;
                    $harga_pokok = 0.0;
                    if (isset($value->harga_pokok) && $value->harga_pokok) {
                        $harga_pokok = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_pokok));
                    }

                    if (isset($value->harga_jual) && $value->harga_jual) {
                        $harga_jual = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_jual));
                    }
                    $result = [
                        'barang_master_id' => $id,
                        'spesifikasi' => strtoupper($value->spesifikasi),
                        'satuan_1' => $value->satuan_1,
                        'satuan_2' => $value->satuan_2 != "" ? $value->satuan_2 : 0,
                        'konversi_satuan_2' => $value->konversi_satuan_2,
                        'satuan_3' => $value->satuan_3 != "" ? $value->satuan_3 : 0,
                        'konversi_satuan_3' => $value->konversi_satuan_3,
                        'harga_pokok' => $harga_pokok,
                        'harga_jual' => $harga_jual,
                    ];
                    $barangSpesifikasiModel->insert($result);
                }
            }
        }

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

    public function deleteSpek()
    {
        $id = ($this->request->getVar('id'));
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $barangSpesifikasiModel->update($id, [
            'deletedAt' => date('Y-m-d H:i:s')
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Spesifikasi berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get()
    {
        $barangModel = new BarangMasterModel();
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $id = decrypt($this->request->getVar('id'));
        $res = $barangModel->where('id', $id)->where('deletedAt', null)->first();
        $res['id'] = encrypt($res['id']);
        $res['company_id'] = encrypt($res['company_id']);
        $res['parent_type_id'] = encrypt($res['parent_type_id']);
        $spekDetail = $barangSpesifikasiModel->getBarangSpesifikasiByBarangMasterID($id);
        $satuan = $satuanModel->getSatuanAll();

        foreach ($spekDetail as &$value) {
            foreach ($satuan as $valueSatuan) {
                if ($value['satuan_1'] == $valueSatuan['id']) {
                    $value['satuan1_text'] = $valueSatuan['kode_satuan'];
                }
                if ($value['satuan_2'] != 0) {
                    if ($value['satuan_2'] == $valueSatuan['id']) {
                        $value['satuan2_text'] = $valueSatuan['kode_satuan'];
                    }
                } else {
                    $value['satuan2_text'] = "";
                }
                if ($value['satuan_3'] != 0) {
                    if ($value['satuan_3'] == $valueSatuan['id']) {
                        $value['satuan3_text'] = $valueSatuan['kode_satuan'];
                    }
                } else {
                    $value['satuan3_text'] = "";
                }
            }
        }

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
            "barang_master.deletedAt" => NULL,
            "barang_master_spesifikasi.deletedAt" => NULL,
        ];

        $addCondition = [
            'search'            => $this->request->getGet('search'),
            "filter_coa"        => $this->request->getGet("filter_coa"),
            "sort"              => $this->request->getGet("sort"),
            "sortType"          => $this->request->getGet("sortType")
        ];

        $barangMasterModel = new BarangMasterModel();
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $satuanModel = new SatuansModel();
        $accountBarangModel = new AccountBarangModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $barangMasterModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $lokalDetail = $amPurchaseOrderModel->historiHargaPOBahanPenolongFirst($data['id'], "", "Lokal", $this->this_company_id);
            $importDetail = $amPurchaseOrderModel->historiHargaPOBahanPenolongFirst($data['id'], "", "Import", $this->this_company_id);
            $satuan1 = $satuanModel->asObject()->where('id', $data['satuan_1'])->where('deletedAt', null)->first();
            $satuan2 = $satuanModel->asObject()->where('id', $data['satuan_2'])->where('deletedAt', null)->first();
            $satuan3 = $satuanModel->asObject()->where('id', $data['satuan_3'])->where('deletedAt', null)->first();

            $satuan1_kode = isset($satuan1) ? $satuan1->kode_satuan : "-";
            $satuan2_kode = (isset($satuan2) && $data['satuan_2'] != 0) ? $satuan2->kode_satuan : "-";
            $satuan3_kode = (isset($satuan3) && $data['satuan_3'] != 0) ? $satuan3->kode_satuan : "-";
            $accountBarang = $accountBarangModel->asObject()->where('barang_master_id', $data['id'])->where('deleted_at', null)->first();
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "kelompok_barang"       => strtoupper($data['kelompok_barang']),
                "kode_barang"           => $data['kode_barang'],
                "barang_name"           => strtoupper($data['barang_name'] . " - " . $data['spesifikasi']),
                "satuan"                => $satuan1_kode, // Adjust 'some_property' to the actual property you want to display
                "satuan2"               => $satuan2_kode == "-" ? "-" : $satuan2_kode . " (" . $data['konversi_satuan_2'] . " " . $satuan1_kode . ")",
                "satuan3"               => $satuan3_kode == "-" ? "-" : $satuan3_kode . " (" . $data['konversi_satuan_3'] . " " . $satuan1_kode . ")",
                "akun_coa"               => $accountBarang ? $accountBarang : "",
                "harga_terakhir_lokal"  => $lokalDetail['hargaTerakhir'],
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

    public function dropdownBarangTypeWithoutSpec()
    {
        $barangModel = new BarangMasterModel();

        $type = $this->request->getGet("type");
        $dataBarang = $barangModel->getBarangByType($type);

        for ($i = 0; $i < count($dataBarang); $i++) {
            $dataBarang[$i]['id'] = encrypt($dataBarang[$i]['id']);
            $dataBarang[$i]['parent_type_id'] = encrypt($dataBarang[$i]['parent_type_id']);
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

    public function import()
    {
        $parentBarangModel = new ParentBarangModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();

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
            $type_barang = $this->request->getVar('type_barang');

            $file = $this->request->getFile('file');

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

            $gagalArr = [];
            $berhasilTotal = 0;

            // INSERT MASTER BARANG
            for ($i = 0; $i < count($data); $i++) {
                // VALIDASI KODE BARANG
                $kodeBarang = $barangMasterModel->where('kode_barang', trim($data[$i][1]))
                    ->where('company_id', $this->this_company_id)
                    ->where('type_barang', $type_barang)
                    ->where('deletedAt', null)
                    ->first();

                // VALIDASI NAMA BARANG
                $barangName = $barangMasterModel->where('UPPER(barang_name)', trim($data[$i][2]))
                    ->where('company_id', $this->this_company_id)
                    ->where('type_barang', $type_barang)
                    ->where('deletedAt', null)
                    ->first();

                // VALIDASI KATEGORI BARANG (PARENT BARANG)
                $parentBarang = $parentBarangModel->where('parent_name', trim($data[$i][0]))
                    ->where('company_id', $this->this_company_id)
                    ->where('parent_type', $type_barang)
                    ->where('deletedAt', null)
                    ->first();

                // if excel null
                if ($data[$i][1] != null) {
                    if ($kodeBarang == null && $barangName == null && $parentBarang != null) {
                        // MASTER BARANG INSERTED
                        $barangMasterModel->insert([
                            'company_id' => $this->this_company_id,
                            'parent_type_id' => $parentBarang['id'],
                            'kode_barang' => trim($data[$i][1]),
                            'barang_name' => strtoupper(trim($data[$i][2])),
                            'type_barang' => $type_barang,
                            'minimum_stock' => 0,
                        ]);
                    }
                }
            }

            // INSERT SPESIFIKASI
            for ($i = 0; $i < count($data); $i++) {
                // VALIDASI SATUAN
                $satuan = $satuanModel->where('kode_satuan', trim($data[$i][4]))->first();
                // CARI BARANG MASTER NYA 
                $barangMaster = $barangMasterModel->where('kode_barang', trim($data[$i][1]))
                    ->where('company_id', $this->this_company_id)
                    ->where('type_barang', $type_barang)
                    ->where('deletedAt', null)
                    ->first();
                if ($data[$i][1] != null) {
                    if ($satuan != null && $barangMaster != null) {
                        // CHECK BARANG MASTER SPESIFIKASI NAME
                        $barangMasterSpesifikasi = $barangMasterSpesifikasiModel
                            ->where('barang_master_id', $barangMaster['id'])
                            ->where('spesifikasi', strtoupper(trim($data[$i][3])))
                            ->first();

                        if ($barangMasterSpesifikasi == null) {
                            $barangMasterSpesifikasiModel->insert([
                                'barang_master_id' => $barangMaster['id'],
                                'spesifikasi' => strtoupper(trim($data[$i][3])),
                                'satuan_1' => $satuan['id'],
                                'satuan_2' => 0,
                                'konversi_satuan_2' => 1,
                                'satuan_3' => 0,
                                'konversi_satuan_3' =>  1,
                                'harga_pokok' => 0,
                                'harga_jual' => 0,
                            ]);
                            $berhasilTotal++;
                        } else {
                            array_push($gagalArr, $data[$i]);
                        }
                    } else {
                        array_push($gagalArr, $data[$i]);
                    }
                }
            }

            $gagalTotal = count($gagalArr);

            return response()->setJSON([
                'message' => "Berhasil Import : $berhasilTotal Data, Gagal Import : $gagalTotal",
                'status' => true,
                'gagal' => $gagalArr,
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
}
