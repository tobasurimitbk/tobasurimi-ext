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
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\Sub_AkunsModel;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Barang extends BaseController
{
    protected $this_company_id, $isAccounting;
    private $kodeBahanBaku, $kodeBahanPenolong, $kodeBahanJadi, $kodeBahanScrap, $kodeBahanModal, $kodeBahanSetengahJadi, $divisiModel, $metaDataModel, $accountBarangModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->isAccounting = session()->get("login")->is_admin == '1' || session()->get("login")->this_role_name == 'ACCOUNTING' ? true : false;
        $this->divisiModel = new DivisisModel();
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->metaDataModel = new MetadataModel();
        $this->accountBarangModel = new AccountBarangModel();

        $this->kodeBahanBaku = "BL-BB";
        $this->kodeBahanPenolong = "BL-BP";
        $this->kodeBahanJadi = "BL-BJ";
        $this->kodeBahanScrap = "BL-BS";
        $this->kodeBahanModal = "BL-BM";
        $this->kodeBahanSetengahJadi = "BL-BSJ";
    }

    public function bahanBakuView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $subAkunsModel = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $data = [
            'type' => "bahan_baku",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_baku")->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll(),
            'isAccounting' => $this->isAccounting,
            'divisis' => $this->divisiModel->getDivisiAccess(),
            'kategoriBarangAkun' => $this->metaDataModel->asObject()->where('name', 'kategori_barang_akun')->findAll(),
            "subAkuns" => $subAkunsModel
        ];

        return view('Warehouse/barangMaster/bahanBaku', $data);
    }

    public function bahanPenolongView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $data = [
            'type' => "bahan_penolong",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_penolong")->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
        ];

        return view('Warehouse/barangMaster/bahanPenolong', $data);
    }

    public function bahanJadiView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();

        $data = [
            'type' => "bahan_jadi",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_jadi")->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
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
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_scrap")->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
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
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_modal")->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
        ];

        return view('Warehouse/barangMaster/bahanModal', $data);
    }

    public function bahanSetengahJadiView()
    {
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();
        $data = [
            'type' => "bahan_setengah_jadi",
            'kelompokBarang' => $parentBarangModel->where('parent_type', "bahan_setengah_jadi")->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
        ];

        return view('Warehouse/barangMaster/bahanSetengahJadi', $data);
    }

    public function create()
    {
        $barangModel = new BarangMasterModel();
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $accountBarangModel = new AccountBarangModel(); // pastikan sudah ada model ini

        $result = [];
        $type = $this->request->getVar('type');
        $spek = json_decode($this->request->getVar("items"));
        $akun_barang = json_decode($this->request->getVar("akun_barang"));

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

        $barangMasterID = $barangModel->insert([
            'company_id' => $this->this_company_id,
            'parent_type_id' => decrypt($this->request->getVar('parent_type_id')) == 0
                ? $this->request->getVar('parent_type_id')
                : decrypt($this->request->getVar('parent_type_id')),
            'kode_barang' => $this->request->getVar('kode_barang'),
            'barang_name' => $this->request->getVar('barang_name'),
            'type_barang' => $type,
            'minimum_stock' => str_replace('.', '', $this->request->getVar('minimum_stock')),
        ]);

        // Loop spesifikasi
        foreach ($spek as $value) {
            $harga_jual = 0.0;
            $harga_pokok = 0.0;
            if (!empty($value->harga_pokok)) {
                $harga_pokok = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_pokok));
            }
            if (!empty($value->harga_jual)) {
                $harga_jual = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_jual));
            }

            // insert spesifikasi
            $spesifikasiID = $barangSpesifikasiModel->insert([
                'barang_master_id' => $barangMasterID,
                'spesifikasi' => $value->spesifikasi,
                'satuan_1' => $value->satuan_1,
                'satuan_2' => $value->satuan_2 != "" ? $value->satuan_2 : 0,
                'konversi_satuan_2' => $value->konversi_satuan_2 ? $value->konversi_satuan_2 : 1,
                'satuan_3' => $value->satuan_3 != "" ? $value->satuan_3 : 0,
                'konversi_satuan_3' => $value->konversi_satuan_3 ? $value->konversi_satuan_3 : 1,
                'harga_pokok' => $harga_pokok,
                'harga_jual' => $harga_jual,
            ]);

            if ($type == 'bahan_baku') {
                // ambil akun barang sesuai spek_id
                foreach ($akun_barang as $akun) {
                    if ($akun->spek_id == $value->spek_id) {
                        $checkAccount = $accountBarangModel->checkAccountBarang(
                            $this->this_company_id,
                            $akun->divisi_id,
                            $barangMasterID,
                            $spesifikasiID,
                            $akun->keterangan
                        );
                        if (!$checkAccount) {
                            $dataAkunBarang = [
                                'divisi_id' => $akun->divisi_id,
                                'barang_master_id' => $barangMasterID,
                                'barang_master_spesifikasi_id' => $spesifikasiID,
                                'company_id' => $this->this_company_id,
                                'ap_id' => $akun->akun_ap_id,
                                'ar_id' => $akun->akun_ar_id,
                                'pemakaian_id' => $akun->akun_pemakaian_id,
                                'kategori_id' => $akun->kategori_id,
                                'keterangan' => $akun->keterangan,
                            ];
                            $accountBarangModel->insert($dataAkunBarang);
                        }
                    }
                }
            }
        }

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
        $accountBarangModel = new AccountBarangModel();

        $spek = json_decode($this->request->getVar("items"));
        $akun_barang = json_decode($this->request->getVar("akun_barang"));

        $barangModel->update($id, [
            'company_id' => $this->this_company_id,
            'parent_type_id' => decrypt($this->request->getVar('parent_type_id')),
            'barang_name' => $this->request->getVar('barang_name'),
            'kode_barang' => $this->request->getVar('kode_barang'),
            'type_barang' => $this->request->getVar('type'),
            'minimum_stock' => str_replace('.', '', $this->request->getVar('minimum_stock')),
        ]);

        if ($spek) {
            foreach ($spek as $value) {
                $harga_jual = 0.0;
                $harga_pokok = 0.0;
                if (!empty($value->harga_pokok)) {
                    $harga_pokok = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_pokok));
                }
                if (!empty($value->harga_jual)) {
                    $harga_jual = (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $value->harga_jual));
                }

                if (!empty($value->spesifikasi_id)) {
                    // update spesifikasi
                    $barangSpesifikasiModel->update($value->spesifikasi_id, [
                        'spesifikasi' => $value->spesifikasi,
                        'satuan_1' => $value->satuan_1,
                        'satuan_2' => $value->satuan_2 != "" ? $value->satuan_2 : 0,
                        'konversi_satuan_2' => $value->konversi_satuan_2 ?: 1,
                        'satuan_3' => $value->satuan_3 != "" ? $value->satuan_3 : 0,
                        'konversi_satuan_3' => $value->konversi_satuan_3 ?: 1,
                        'harga_pokok' => $harga_pokok,
                        'harga_jual' => $harga_jual,
                    ]);
                    $spesifikasiID = $value->spesifikasi_id;
                } else {
                    // insert spesifikasi baru
                    $spesifikasiID = $barangSpesifikasiModel->insert([
                        'barang_master_id' => $id,
                        'spesifikasi' => $value->spesifikasi,
                        'satuan_1' => $value->satuan_1,
                        'satuan_2' => $value->satuan_2 != "" ? $value->satuan_2 : 0,
                        'konversi_satuan_2' => $value->konversi_satuan_2 ?: 1,
                        'satuan_3' => $value->satuan_3 != "" ? $value->satuan_3 : 0,
                        'konversi_satuan_3' => $value->konversi_satuan_3 ?: 1,
                        'harga_pokok' => $harga_pokok,
                        'harga_jual' => $harga_jual,
                    ]);
                }

                // hapus akun barang lama untuk spesifikasi ini
                // $accountBarangModel->where('barang_master_spesifikasi_id', $spesifikasiID)->delete();


                if ($this->request->getVar('type') == 'bahan_baku') {
                    // insert akun barang baru
                    foreach ($akun_barang as $akun) {
                        if ($akun->spek_id == $value->spek_id) {
                            $checkAccount = $accountBarangModel->checkAccountBarang(
                                $this->this_company_id,
                                $akun->divisi_id,
                                $id,
                                $spesifikasiID,
                                $akun->keterangan
                            );
                            if (!$checkAccount) {
                                $dataAkunBarang = [
                                    'divisi_id' => $akun->divisi_id,
                                    'barang_master_id' => $id,
                                    'barang_master_spesifikasi_id' => $spesifikasiID,
                                    'company_id' => $this->this_company_id,
                                    'ap_id' => $akun->akun_ap_id,
                                    'ar_id' => $akun->akun_ar_id,
                                    'pemakaian_id' => $akun->akun_pemakaian_id,
                                    'kategori_id' => $akun->kategori_id,
                                    'keterangan' => $akun->keterangan,
                                ];
                                $accountBarangModel->insert($dataAkunBarang);
                            }
                        }
                    }
                }
            }
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Barang berhasil diupdate"
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
        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $barangMasterModel = new BarangMasterModel();

        try {
            $id = ($this->request->getVar('id'));

            // First yang akan didelete
            $barangMasterSpesifikasi = $barangSpesifikasiModel
                ->where('id', $id)
                ->first();
            // Hapus spek
            $barangSpesifikasiModel->update($id, [
                'deletedAt' => date('Y-m-d H:i:s')
            ]);

            // Cek Total Spek
            // Jika Total Spek Kosong Hapus Parent Barang
            $barangMasterSpesifikasiAll = $barangSpesifikasiModel
                ->where('deletedAt', null)
                ->where('barang_master_id', $barangMasterSpesifikasi['barang_master_id'])
                ->findAll();

            if (count($barangMasterSpesifikasiAll) == 0) {
                // Tinggal Parent Aja Maka Hapus Parent nya
                $barangMasterModel->delete($barangMasterSpesifikasi['barang_master_id']);
            }

            return response()->setJSON([
                'status' => true,
                'message' => "Barang spesifikasi berhasil dihapus",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'token' => \csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
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

        $aksesSupplierLokalBP = true; // tampilkan harga terakhir
        $aksesSupplierImportBP = can('Pembelian', 'PO Import BP', 'r');

        $res = $barangMasterModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $satuan1 = $satuanModel->asObject()->where('id', $data['satuan_1'])->where('deletedAt', null)->first();
            $satuan2 = $satuanModel->asObject()->where('id', $data['satuan_2'])->where('deletedAt', null)->first();
            $satuan3 = $satuanModel->asObject()->where('id', $data['satuan_3'])->where('deletedAt', null)->first();

            $satuan1_kode = isset($satuan1) ? $satuan1->kode_satuan : "-";
            $satuan2_kode = (isset($satuan2) && $data['satuan_2'] != 0) ? $satuan2->kode_satuan : "-";
            $satuan3_kode = (isset($satuan3) && $data['satuan_3'] != 0) ? $satuan3->kode_satuan : "-";
            $accountBarang = $accountBarangModel->asObject()->where('company_id', $this->this_company_id)->where('barang_master_id', $data['id'])->where('deleted_at', null)->first();

            if ($aksesSupplierLokalBP) {
                // LOKAL 
                $hargaTerakhir = $data['harga_terakhir'];
                $supplierTerakhir = $data['supplier_terakhir_name'];
            } elseif ($aksesSupplierImportBP) {
                // IMPORT 
                $hargaTerakhir = $data['harga_terakhir'];
                $supplierTerakhir = $data['supplier_terakhir_name'];
            } else {
                $hargaTerakhir = "-";
                $supplierTerakhir = "-";
            }
            // HARGA TERAKHIR
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "spesifikasi_id"        => $data['spesifikasi_id'],
                "kelompok_barang"       => $data['kelompok_barang'],
                "kode_barang"           => $data['kode_barang'],
                "barang_name"           => $data['barang_name'] . " - " . $data['spesifikasi'],
                "satuan"                => $satuan1_kode, // Adjust 'some_property' to the actual property you want to display
                "satuan2"               => $satuan2_kode == "-" ? "-" : $satuan2_kode,
                "satuan3"               => $satuan3_kode == "-" ? "-" : $satuan3_kode,
                "akun_coa"              => $accountBarang ? $accountBarang : "",
                "harga_terakhir"        => $hargaTerakhir == null ? "" : (float)$hargaTerakhir,
                "supplier_terakhir"     => $supplierTerakhir,
                "satuan_terakhir"       => $data['kode_satuan_terakhir']
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
        } elseif ($type == "bahan_setengah_jadi") {
            $codeName = $this->kodeBahanSetengahJadi;
        } else {
            $codeName = $this->kodeBahanModal;
        }

        $aksesSupplierLokalBP = can('Pembelian', 'PO Lokal BP', 'r');
        $aksesSupplierImportBP = can('Pembelian', 'PO Import BP', 'r');

        if ($aksesSupplierLokalBP && !$aksesSupplierImportBP && $type == 'bahan_penolong') {
            // PO LOKAL BP — sembunyikan kode barang yang diawali 'BI-'
            $codeName = $this->kodeBahanPenolong;
        } elseif (!$aksesSupplierLokalBP && $aksesSupplierImportBP && $type == 'bahan_penolong') {
            // PO IMPORT BP — hanya tampilkan kode barang yang diawali 'BI-'
            $codeName = "BI-BP";
        }

        $lastBarang = $barangModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', $type)
            ->where('deletedAt', null)
            ->like('kode_barang', $codeName . '-____')
            ->orderBy('kode_barang', 'DESC')
            ->findAll();


        if (empty($lastBarang)) {
            return response()->setJSON([
                'codeNew' => "$codeName-0001",
                'token' => csrf_hash(),
            ]);
        }
        try {
            foreach ($lastBarang as $value) {
                $lastCode = $value->kode_barang;
                $lastCodeExp = explode('-', $lastCode);
                $length = strlen($lastCodeExp[2]);
                if ($length == 4) {
                    $lastIncrement = (int)$lastCodeExp[2];

                    $newIncrement = str_pad(($lastIncrement + 1), 4, '0', STR_PAD_LEFT);

                    return response()->setJSON([
                        'codeNew' => $codeName . "-" . $newIncrement,
                        'token' => csrf_hash(),

                    ]);
                } else {
                    // KODE LAIN BUAT YANG BARU
                    return response()->setJSON([
                        'codeNew' => "$codeName-0001",
                        'token' => csrf_hash(),
                    ]);
                }
            }
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $codeName . "-????",
                'token' => csrf_hash()
            ]);
        }
    }

    public function historiHargaPOBahanPenolong()
    {
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "am_purchase_orders.company_id"  => $this->this_company_id,
            "am_purchase_order_details.spesifikasi_id" => $this->request->getVar('id'),
            "am_purchase_order_details.deletedAt" => NULL,
            "am_purchase_orders.po_type" => $this->request->getVar('po_type')
        ];

        $addCondition = [
            'search' => $this->request->getGet('search'),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "start_date" => $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "",
            "end_date" => $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "",
        ];

        $statusPenerimaan = strtoupper($this->request->getVar('po_type'));
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $amPurchaseOrderDetailModel->getListHistoryHargaByBarang(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $rdata = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($res['data'] as $data) {
            $penerimaanBarang = $penerimaanBarangDetailModel
                ->getHistoriLpbBahanPenolong(
                    strtoupper(trim($statusPenerimaan)),
                    "PENOLONG",
                    $data['id']
                );

            array_push($rdata, [
                "no"                    => $no++,
                "po_no"                 => $data['po_no'],
                "no_lpb"                => $penerimaanBarang == null ? "" : $penerimaanBarang['no_penerimaan_barang'],
                "spp_no"                => $data['spp_no'],
                "po_date"               => date('d/m/Y', strtotime($data['po_date'])),
                "nama_supplier"         => $data['nama_supplier'],
                "nama_barang"           => $data['nama_barang'],
                'divisi'                => $data['divisi'],
                'note'                  => $data['note'],
                'qty'                   => floatval($data['qty']),
                'kode_satuan'           => $data['kode_satuan'],
                "price"                 => (float)$data['harga'],
                "sub_total"             => (float)$data['sub_total'],
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

    public function historiHargaPOBahanPenolongBySupplier()
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "am_purchase_orders.company_id"  => $this->this_company_id,
            "am_purchase_orders.supplier_id" => $this->request->getVar('supplier_id'),
            "am_purchase_orders.deletedAt" => NULL,
            "am_purchase_order_details.deletedAt" => NULL,
            "am_purchase_orders.po_type" => "Lokal"
        ];

        $addCondition = [
            'search' => $this->request->getGet('search'),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "start_date" => $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "",
            "end_date" => $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : ""
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        // var_dump($addCondition);
        // die;

        $res = $amPurchaseOrderModel->historiHargaPOBahanPenolong($condition, $addCondition, $limit, $offset);

        $rdata = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($res['data'] as $data) {
            $penerimaanBarang = $penerimaanBarangDetailModel
                ->getHistoriLpbBahanPenolong(
                    "LOKAL",
                    "PENOLONG",
                    $data['id']
                );

            array_push($rdata, [
                "no"                    => $no++,
                "po_no"                 => $data['po_no'],
                "spp_no"                => $data['spp_no'],
                "no_penerimaan_barang"  => $penerimaanBarang == null ? "" : $penerimaanBarang['no_penerimaan_barang'],
                "po_date"               => date('d/m/Y', strtotime($data['po_date'])),
                "nama_supplier"         => $data['nama_supplier'],
                "nama_barang"           => $data['nama_barang'],
                'divisi'                => $data['divisi'],
                'note'                  => $data['note'],
                'qty'                   => floatval($data['qty']),
                'kode_satuan'           => $data['kode_satuan'],
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

    public function exportHistoriHargaPOBahanPenolongBySupplier()
    {
        $supplierId = $this->request->getGet('supplier_id');
        $poDate     = $this->request->getGet('po_date');
        $search     = $this->request->getGet('search');
        $sort       = $this->request->getGet('sort');
        $sortType   = $this->request->getGet('sortType');
        $start_date = $this->request->getGet('start_date');
        $end_date   = $this->request->getGet('end_date');

        $condition = [
            "am_purchase_orders.company_id"  => $this->this_company_id,
            "am_purchase_orders.deletedAt" => null,
            "am_purchase_order_details.deletedAt" => null,
            "am_purchase_orders.po_type" => "Lokal"
        ];
        if ($supplierId && $supplierId !== "all") {
            $condition["am_purchase_orders.supplier_id"] = $supplierId;
        }

        $addCondition = [
            "search" => $search !== "all" ? $search : "",
            "sort" => $sort ?? "am_purchase_orders.id",
            "sortType" => $sortType ?? "desc",
            "po_date" => $poDate ? date("Y-m-d", strtotime($poDate)) : "",
            "start_date" => $start_date,
            "end_date" => $end_date
        ];

        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $res = $amPurchaseOrderModel->historiHargaPOBahanPenolong($condition, $addCondition, null, null);

        // --- Export to Excel ---
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No SPP');
        $sheet->setCellValue('C1', 'No LPB');
        $sheet->setCellValue('D1', 'Tanggal PO');
        $sheet->setCellValue('E1', 'Supplier');
        $sheet->setCellValue('F1', 'Barang');
        $sheet->setCellValue('G1', 'Keterangan');
        $sheet->setCellValue('H1', 'Departemen');
        $sheet->setCellValue('I1', 'Qty');
        $sheet->setCellValue('J1', 'Satuan');
        $sheet->setCellValue('K1', 'Harga');

        $row = 2;
        $no = 1;
        foreach ($res['data'] as $data) {
            $penerimaanBarang = $penerimaanBarangDetailModel
                ->getHistoriLpbBahanPenolong(
                    "LOKAL",
                    "PENOLONG",
                    $this->this_company_id,
                    $data['spesifikasi_id']
                );

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['spp_no']);
            $sheet->setCellValue('C' . $row, $penerimaanBarang == null ? "" : $penerimaanBarang['no_penerimaan_barang']);
            $sheet->setCellValue('D' . $row, date("d/m/Y", strtotime($data['po_date'])));
            $sheet->setCellValue('E' . $row, $data['nama_supplier']);
            $sheet->setCellValue('F' . $row, $data['nama_barang']);
            $sheet->setCellValue('G' . $row, $data['note']);
            $sheet->setCellValue('H' . $row, $data['divisi']);
            $sheet->setCellValue('I' . $row, $data['qty']);
            $sheet->setCellValue('J' . $row, $data['kode_satuan']);
            $sheet->setCellValue('K' . $row, $data['price']);
            $row++;
        }

        $filename = 'Histori_PO_Bahan_Penolong_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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
            $dataBarang[$i]['barang_name'] =
                str_replace(['"'], "'", $dataBarang[$i]['barang_name_master'] . ' ' . $dataBarang[$i]['spesifikasi']);
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
        $type2 = $this->request->getGet("type2");
        $dataBarang = $barangModel->getBarangByType($type, $type2);

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

    public function dropdownBarangTypeWithoutSpecWO()
    {
        $barangModel = new BarangMasterModel();

        $type = $this->request->getGet("type");
        $type2 = $this->request->getGet("type2");
        $companyId = $this->this_company_id;
        $dataBarang = $barangModel->getBarangByTypeCondition($companyId, $type, $type2);

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
                    if ($kodeBarang == null && $parentBarang != null) {
                        // MASTER BARANG INSERTED
                        $barangMasterModel->insert([
                            'company_id' => $this->this_company_id,
                            'parent_type_id' => $parentBarang['id'],
                            'kode_barang' => trim($data[$i][1]),
                            'barang_name' => trim($data[$i][2]),
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
                            ->where('spesifikasi', trim($data[$i][3]))
                            ->first();

                        if ($barangMasterSpesifikasi == null) {
                            $barangMasterSpesifikasiModel->insert([
                                'barang_master_id' => $barangMaster['id'],
                                'spesifikasi' => trim($data[$i][3]),
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

    public function exportExcel()
    {
        $search       = $this->request->getVar("search");
        $sort         = $this->request->getVar("sort");
        $sortType     = $this->request->getVar("sortType");
        $parent_type  = $this->request->getVar("parent_type");
        $filter_coa   = $this->request->getVar("filter_coa");

        $filename = "EXPORT_BARANG_" . strtoupper($parent_type);

        $condition = [
            "barang_master.company_id"  => $this->this_company_id,
            "barang_master.type_barang" => $parent_type,
            "barang_master.deletedAt"   => NULL,
        ];


        $aksesSupplierLokalBP = can('Pembelian', 'PO Lokal BP', 'r');
        $aksesSupplierImportBP = can('Pembelian', 'PO Import BP', 'r');

        $selectQry = "barang_master.*, 
        barang_master_spesifikasi.spesifikasi, 
        barang_master_spesifikasi.satuan_1, 
        barang_master_spesifikasi.satuan_2, 
        barang_master_spesifikasi.konversi_satuan_2, 
        barang_master_spesifikasi.satuan_3, 
        barang_master_spesifikasi.konversi_satuan_3,
        parent_barang.parent_name AS kelompok_barang,
        barang_master_spesifikasi.harga_terakhir,
        suppliers.name AS supplier_terakhir_name,
        satuans.kode_satuan AS kode_satuan_terakhir";

        $barangMasterModel = new BarangMasterModel();
        $satuanModel = new SatuansModel();

        $barangDataQry = $barangMasterModel->select($selectQry)
            ->where($condition)
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->join('suppliers', 'suppliers.id = barang_master_spesifikasi.supplier_terakhir', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.unit_terakhir', 'left')
            ->orderBy($sort, $sortType);

        if ($aksesSupplierLokalBP && !$aksesSupplierImportBP) {
            // PO LOKAL BP — sembunyikan kode barang yang diawali 'BI-'
            $barangDataQry->notLike('barang_master.kode_barang', 'BI-', 'after');
        } elseif (!$aksesSupplierLokalBP && $aksesSupplierImportBP) {
            // PO IMPORT BP — hanya tampilkan kode barang yang diawali 'BI-'
            $barangDataQry->like('barang_master.kode_barang', 'BI-', 'after');
        }

        if ($search || $filter_coa) {
            $barangDataQry->groupStart();
        }

        if ($search) {
            $barangDataQry->like('barang_master.barang_name', $search)
                ->orLike('barang_master.kode_barang', $search)
                ->orLike('parent_barang.parent_name', $search);
        }

        if ($filter_coa) {
            $barangDataQry->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left');
            if ($filter_coa == "belum") {
                $barangDataQry->where('account_barang.ap_id', NULL);
            } elseif ($filter_coa == "sudah") {
                $barangDataQry->where('account_barang.ap_id !=', NULL);
            }
        }

        if ($search || $filter_coa) {
            $barangDataQry->groupEnd();
        }

        $getAllBarangData = $barangDataQry->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'A1' => 'NO',
            'B1' => 'KATEGORI',
            'C1' => 'KODE BARANG',
            'D1' => 'BARANG',
            'E1' => 'SPESIFIKASI',
            'F1' => 'SATUAN 1',
            'G1' => 'SATUAN 2',
            'H1' => 'SATUAN 3',
            'I1' => 'HARGA TERAKHIR',
            'J1' => 'SUPPLIER TERAKHIR',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        if (empty($getAllBarangData)) {
            $sheet->setCellValue('A2', 'Tidak Ada Data Barang');
        } else {
            $no = 1;
            $numRow = 2;

            foreach ($getAllBarangData as $row) {
                $satuan1 = $satuanModel->asObject()->where('id', $row['satuan_1'])->where('deletedAt', null)->first();
                $satuan2 = $satuanModel->asObject()->where('id', $row['satuan_2'])->where('deletedAt', null)->first();
                $satuan3 = $satuanModel->asObject()->where('id', $row['satuan_3'])->where('deletedAt', null)->first();

                $satuan1_kode = $satuan1->kode_satuan ?? "-";
                $satuan2_kode = (isset($satuan2) && $row['satuan_2'] != 0) ? $satuan2->kode_satuan : "-";
                $satuan3_kode = (isset($satuan3) && $row['satuan_3'] != 0) ? $satuan3->kode_satuan : "-";

                $sheet->setCellValue('A' . $numRow, $no);
                $sheet->setCellValue('B' . $numRow, $row['kelompok_barang']);
                $sheet->setCellValue('C' . $numRow, $row['kode_barang']);
                $sheet->setCellValue('D' . $numRow, $row['barang_name']);
                $sheet->setCellValue('E' . $numRow, $row['spesifikasi']);
                $sheet->setCellValue('F' . $numRow, $satuan1_kode);
                $sheet->setCellValue('G' . $numRow, $satuan2_kode == "-" ? "-" : $satuan2_kode . " (" . $row['konversi_satuan_2'] . " " . $satuan1_kode . ")");
                $sheet->setCellValue('H' . $numRow, $satuan3_kode == "-" ? "-" : $satuan3_kode . " (" . $row['konversi_satuan_3'] . " " . $satuan1_kode . ")");
                $sheet->setCellValue('I' . $numRow, $row['harga_terakhir'] == null ? "" : number_format($row['harga_terakhir'], 2) . " / " . $row['kode_satuan_terakhir']);
                $sheet->setCellValue('J' . $numRow, $row['supplier_terakhir_name']);

                $no++;
                $numRow++;
            }

            // Rata kiri isi data
            $sheet->getStyle('A2:J' . ($numRow - 1))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Auto-size semua kolom (A–J)
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Output ke browser
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . strlen($excelOutput));

        echo $excelOutput;
        exit();
    }


    public function exportExcelHistory()
    {
        $search = $this->request->getVar("search");
        $sort = $this->request->getVar("sort") ?? 'am_purchase_orders.po_date';
        $sortType = $this->request->getVar("sortType") ?? 'DESC';
        $start_date = $this->request->getVar("start_date");
        $end_date = $this->request->getVar("end_date");
        $po_type = $this->request->getVar("po_type");
        $id = $this->request->getVar("id");

        $filename = "HISTORI Penerimaan per Barang" . strtoupper($po_type) . "_" . date('YmdHis');

        $condition = [
            "am_purchase_orders.company_id" => $this->this_company_id,
            "am_purchase_order_details.spesifikasi_id" => $id,
            "am_purchase_orders.deletedAt" => NULL,
            "am_purchase_order_details.deletedAt" => NULL,
            "am_purchase_orders.po_type" => $po_type
        ];


        $amPurchaseOrderModel = new AMPurchaseOrderModel();


        $res = $amPurchaseOrderModel->historiHargaPOBahanPenolongByLpb(
            $condition,
            [
                'search' => $search,
                "sort" => $sort,
                "sortType" => $sortType,
                "start_date" => $start_date ? date("Y/m/d", strtotime(str_replace("/", "-", $start_date))) : "",
                "end_date" => $end_date ? date("Y/m/d", strtotime(str_replace("/", "-", $end_date))) : ""
            ],
            0, // tanpa limit
            0  // tanpa offset
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NO SPP');
        $sheet->setCellValue('C1', 'NO PO');
        $sheet->setCellValue('D1', 'NO LPB');
        $sheet->setCellValue('E1', 'TANGGAL PO');
        $sheet->setCellValue('F1', 'SUPPLIER');
        $sheet->setCellValue('G1', 'BARANG');
        $sheet->setCellValue('H1', 'DEPARTEMEN');
        $sheet->setCellValue('I1', 'KETERANGAN');
        $sheet->setCellValue('J1', 'QTY');
        $sheet->setCellValue('K1', 'SATUAN');
        $sheet->setCellValue('L1', 'HARGA');
        $sheet->setCellValue('M1', 'SUB TOTAL');

        // Style untuk header
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Isi data
        $no = 1;
        $rowNum = 2;
        foreach ($res['data'] as $data) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $data['spp_no']);
            $sheet->setCellValue('C' . $rowNum, $data['po_no']);
            $sheet->setCellValue('D' . $rowNum, $data['no_penerimaan_barang']);
            $sheet->setCellValue('E' . $rowNum, date('d/m/Y', strtotime($data['po_date'])));
            $sheet->setCellValue('F' . $rowNum, $data['nama_supplier']);
            $sheet->setCellValue('G' . $rowNum, $data['nama_barang']);
            $sheet->setCellValue('H' . $rowNum, $data['divisi']);
            $sheet->setCellValue('I' . $rowNum, $data['note']);
            $sheet->setCellValue('J' . $rowNum, floatval($data['total_qty']));
            $sheet->setCellValue('K' . $rowNum, $data['kode_satuan']);
            $sheet->setCellValue('L' . $rowNum, $data['total_harga']);
            $sheet->setCellValue('M' . $rowNum, $data['total_sub_total']);

            // Format angka untuk kolom harga dan sub total
            $sheet->getStyle('L' . $rowNum . ':M' . $rowNum)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

            $rowNum++;
        }

        // Auto size semua kolom
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output file Excel
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . strlen($excelOutput));

        echo $excelOutput;
        exit();
    }

    public function saveAkunBarang()
    {
        // $barangModel = new BarangMasterModel();
        // $barangAkunModel = new BarangAkunModel(); // Pastikan model ini ada

        $result = array();
        $barangId = $this->request->getVar('barang_id');
        $items = $this->request->getVar('items');

        // var_dump($items);
        // die;

        // Decode items dari string JSON ke array
        $akunBarang = json_decode($items);

        // Validasi
        if (empty($barangId) || empty($akunBarang)) {
            return $this->response->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Data tidak valid"
            ]);
        }

        // Cek apakah barang ada
        $barang = $this->barangModel->find($barangId);
        if (!$barang) {
            return $this->response->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Barang tidak ditemukan"
            ]);
        }

        // Mulai transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Hapus akun barang yang lama (jika ada)
        // $this->bara->where('barang_id', $barangId)->delete();

        // Simpan setiap akun barang
        foreach ($akunBarang as $item) {
            $data = [
                'barang_id' => $barangId,
                'divisi_id' => $item->divisi_id,
                'akun_ap_id' => $item->akun_ap_id,
                'akun_ar_id' => $item->akun_ar_id,
                'akun_pemakaian_id' => $item->akun_pemakaian_id,
                'kategori_barang_id' => $item->kategori,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->accountBarangModel->insert($data);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return $this->response->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Gagal menyimpan data akun barang"
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Akun barang berhasil disimpan"
        ]);
    }
}
