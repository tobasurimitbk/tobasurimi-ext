<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BiayaKepitingBonusModel;
use App\Models\BiayaKepitingDetailModel;
use App\Models\BiayaKepitingGajiModel;
use App\Models\BiayaKepitingModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\JasaVendorInModel;
use App\Models\MetadataModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\JasaVendorInKepitingKukusDetailModel;
use App\Models\JasaVendorInKepitingKukusModel;
use App\Models\JasaVendorOutKepitingKukusDetailModel;
use App\Models\JasaVendorOutKepitingKukusModel;
use Config\Database;
use Dompdf\Dompdf;

class BiayaKepiting extends BaseController
{
    protected $db;
    protected $this_company_id;
    protected $this_user_id;
    protected $divisiModel;
    protected $warehouseModel;
    protected $biayaKepitingModel;
    protected $biayaKepitingDetailModel;
    protected $biayaKepitingGajiModel;
    protected $jasaVendorInModel;
    protected $jasaVendorInDetailModel;
    protected $jasaVendorInKepitingKukusModel;
    protected $jasaVendorInKepitingKukusDetailModel;
    protected $jasaVendorOutKepitingKukusModel;
    protected $jasaVendorOutKepitingKukusDetailModel;
    protected $vendorModel;
    protected $metaDataModel;
    protected $biayaKepitingBonusModel;
    protected $barangMasterSpesifikasiModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->warehouseModel = new WarehousesModel();
        $this->biayaKepitingModel = new BiayaKepitingModel();
        $this->biayaKepitingDetailModel = new BiayaKepitingDetailModel();
        $this->biayaKepitingGajiModel = new BiayaKepitingGajiModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $this->jasaVendorInKepitingKukusDetailModel = new JasaVendorInKepitingKukusDetailModel();
        $this->jasaVendorInKepitingKukusModel = new JasaVendorInKepitingKukusModel();
        $this->jasaVendorOutKepitingKukusDetailModel = new JasaVendorOutKepitingKukusDetailModel();
        $this->jasaVendorOutKepitingKukusModel = new JasaVendorOutKepitingKukusModel();
        $this->vendorModel = new VendorModel();
        $this->metaDataModel = new MetadataModel();
        $this->biayaKepitingBonusModel = new BiayaKepitingBonusModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->dompdf = new Dompdf();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('jasaVendor/biayaKepiting/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
            "status" => $this->request->getVar("status"),
            "start_date" => $this->request->getVar('start_date'),
            "end_date" => $this->request->getVar('end_date'),
            "no_pembayaran" => $this->request->getVar("no_pembayaran"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'biaya_kepiting.company_id' => $this->this_company_id,
            'biaya_kepiting.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->biayaKepitingModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $biayaKepitingDetailModel = $this->biayaKepitingDetailModel
                ->where('biaya_kepiting_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_pembayaran"        => $data->no_pembayaran,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($biayaKepitingDetailModel),
                "vendor_name"           => $data->vendor_name,
                "status_posting"        => $data->status_posting
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create()
    {
        $data = [
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
        ];
        return view('jasaVendor/biayaKepiting/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);

        $data = [
            'tanggal' => date('Y-m-d'),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'biayaKepiting' => $this->biayaKepitingModel->find($id),
            'jasaVendorInDetail' => $this->biayaKepitingModel->getPenerimaanSuratJalanDetail($id),
        ];

        return view('jasaVendor/biayaKepiting/form', $data);
    }


    public function getSuratJalan($vendorId)
    {
        $data = $this->jasaVendorInKepitingKukusModel
            ->select('id, no_penerimaan_surat_jalan, tanggal')
            ->where('vendor_id', $vendorId)
            ->where('company_id', $this->this_company_id)
            ->where('status_posting', '1')
            ->where('deletedAt', null)
            ->findAll();

        // FE expects: response (array langsung)
        return $this->response->setJSON($data);
    }

   public function createAction()
{
    try {
        // Debug semua input data
        $debugData = [
            'listBarang_count' => count(json_decode($this->request->getPost('listBarang'), true) ?? []),
            'listPerolehanGaji_count' => count(json_decode($this->request->getPost('listPerolehanGaji'), true) ?? []),
            'listBonus_count' => count(json_decode($this->request->getPost('listBonus'), true) ?? []),
            'jasa_vendor_in_ids' => $this->request->getPost('jasa_vendor_in_id'),
            'no_pembayaran' => $this->request->getVar('no_pembayaran'),
            'tanggal' => $this->request->getVar('tanggal'),
            'keterangan' => $this->request->getVar('keterangan'),
        ];
        
        log_message('error', 'DEBUG CREATE ACTION: ' . print_r($debugData, true));

        // Decode data JSON dengan parameter true untuk mendapatkan array assosiatif
        $listBarang = json_decode($this->request->getPost('listBarang'), true) ?? [];
        $listPerolehanGaji = json_decode($this->request->getPost('listPerolehanGaji'), true) ?? [];
        $listBonus = json_decode($this->request->getPost('listBonus'), true) ?? [];
        
        // Handle multiple jasa_vendor_in_id - PASTIKAN INI ARRAY
        $jasaVendorInIds = $this->request->getPost('jasa_vendor_in_id');
        
        log_message('error', 'JASA VENDOR IN IDS RAW: ' . print_r($jasaVendorInIds, true));
        
        // Jika bukan array, konversi ke array
        if (!is_array($jasaVendorInIds)) {
            $jasaVendorInIds = [$jasaVendorInIds];
        }
        
        log_message('error', 'JASA VENDOR IN IDS PROCESSED: ' . print_r($jasaVendorInIds, true));
        
        // Validasi minimal ada satu jasa vendor yang dipilih
        if (empty($jasaVendorInIds) || empty($jasaVendorInIds[0])) {
            log_message('error', 'VALIDASI GAGAL: jasa_vendor_in_ids kosong');
            return $this->response->setJSON([
                'message' => "Pilih minimal satu surat jalan!",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Ambil data semua jasa vendor yang dipilih
        $jasaVendorInList = [];
        foreach ($jasaVendorInIds as $jasaVendorInId) {
            $jasaVendor = $this->jasaVendorInKepitingKukusModel->find($jasaVendorInId);
            if ($jasaVendor) {
                $jasaVendorInList[] = $jasaVendor;
            }
        }
        
        log_message('error', 'JASA VENDOR LIST COUNT: ' . count($jasaVendorInList));
        
        // Gunakan yang pertama sebagai referensi utama
        $mainJasaVendorIn = $jasaVendorInList[0] ?? null;
        
        if (!$mainJasaVendorIn) {
            log_message('error', 'JASA VENDOR TIDAK DITEMUKAN');
            return $this->response->setJSON([
                'message' => "Data jasa vendor tidak ditemukan!",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Cek duplikasi no pembayaran
        $biayaKepiting = $this->biayaKepitingModel
            ->where('company_id', $this->this_company_id)
            ->where('no_pembayaran', $this->request->getVar('no_pembayaran'))
            ->first();

        if ($biayaKepiting != null) {
            log_message('error', 'DUPLIKASI NO PEMBAYARAN: ' . $this->request->getVar('no_pembayaran'));
            return $this->response->setJSON([
                'message' => "No pembayaran sudah ada!",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if (empty($listBarang)) {
            log_message('error', 'LIST BARANG KOSONG');
            return $this->response->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Format tanggal
        $tanggal = $this->request->getVar("tanggal");
        if ($tanggal) {
            $date = \DateTime::createFromFormat("d/m/Y", $tanggal);
            $tanggalFormatted = $date ? $date->format("Y-m-d") : date("Y-m-d");
        } else {
            $tanggalFormatted = date("Y-m-d");
        }

        log_message('error', 'MEMULAI TRANSACTION');

        // Start transaction
        $this->db->transStart();

        // Data untuk insert biaya kepiting utama
        $biayaKepitingData = [
            'company_id' => $this->this_company_id,
            'divisi_id' => $mainJasaVendorIn['divisi_id'],
            'jasa_vendor_in_kepiting_kukus_id' => implode(',', $jasaVendorInIds), // Simpan sebagai string comma separated
            'vendor_id' => $mainJasaVendorIn['vendor_id'],
            'warehouse_id' => $mainJasaVendorIn['warehouse_id'],
            'no_pembayaran' => $this->request->getVar('no_pembayaran'),
            "tanggal" => $tanggalFormatted,
            'keterangan' => $this->request->getVar('keterangan'),
        ];

        log_message('error', 'DATA BIYA KEPITING: ' . print_r($biayaKepitingData, true));

        // Simpan data biaya kepiting utama
        $id = $this->biayaKepitingModel->insert($biayaKepitingData);

        log_message('error', 'INSERT BIYA KEPITING ID: ' . $id);

        // Jika insert gagal
        if (!$id) {
            $error = $this->biayaKepitingModel->errors();
            log_message('error', 'ERROR INSERT BIYA KEPITING: ' . print_r($error, true));
            
            $this->db->transRollback();
            return $this->response->setJSON([
                'message' => "Gagal menyimpan data biaya kepiting: " . implode(', ', $error),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        log_message('error', 'MENYIMPAN DATA BARANG - COUNT: ' . count($listBarang));

        // Simpan data barang - GUNAKAN FOREACH untuk semua jasa_vendor_in_id
        $barangSaved = 0;
        foreach ($listBarang as $b) {
            // Untuk setiap barang, simpan untuk semua jasa vendor yang dipilih
            foreach ($jasaVendorInIds as $jasaVendorInId) {
                $barangData = [
                    'biaya_kepiting_id' => $id,
                    'jasa_vendor_in_id' => $jasaVendorInId,
                    'barang_master_id' => $b['barang_master_id'] ?? null,
                    'barang_master_spesifikasi_id' => $b['barang_master_spesifikasi_id'] ?? null,
                    'supplier_id' => $b['supplier_id'] ?? null,
                    'keterangan' => $b['keterangan'] ?? null,
                    'qty_kopek' => $b['qty_sebelum_kopek'] ?? 0,
                    'rasio' => $b['rasio'] ?? 0,
                    'jumbo' => $b['spek']['JB'] ?? ($b['spek']['JUMBO'] ?? 0),
                    'ex_lump' => $b['spek']['SP LUMP'] ?? ($b['spek']['EX LUMP'] ?? 0),
                    'lump' => $b['spek']['BF'] ?? ($b['spek']['LUMP'] ?? 0),
                    'special' => $b['spek']['SPL'] ?? ($b['spek']['SPESIAL'] ?? 0),
                    'claw' => $b['spek']['CLAW'] ?? 0,
                    'mh' => $b['spek']['MH'] ?? 0,
                    'cf' => $b['spek']['CF'] ?? 0,
                ];
                
                $result = $this->biayaKepitingDetailModel->insert($barangData);
                if ($result) {
                    $barangSaved++;
                } else {
                    $error = $this->biayaKepitingDetailModel->errors();
                    log_message('error', 'ERROR INSERT BARANG: ' . print_r($error, true));
                    log_message('error', 'BARANG DATA: ' . print_r($barangData, true));
                }
            }
        }

        log_message('error', 'BARANG BERHASIL DISIMPAN: ' . $barangSaved);

        // Simpan data perolehan gaji
        $gajiSaved = 0;
        foreach ($listPerolehanGaji as $b) {
            $gajiData = [
                'biaya_kepiting_id' => $id,
                'jenis' => $b['description'] ?? $b['value'] ?? 'unknown',
                'jumbo' => $b['jumbo'] ?? 0,
                'ex_lump' => $b['ex_lump'] ?? 0,
                'lump' => $b['lump'] ?? 0,
                'special' => $b['special'] ?? 0,
                'claw' => $b['claw'] ?? 0,
                'mh' => $b['mh'] ?? 0,
                'cf' => $b['cf'] ?? 0,
            ];
            
            $result = $this->biayaKepitingGajiModel->insert($gajiData);
            if ($result) {
                $gajiSaved++;
            } else {
                $error = $this->biayaKepitingGajiModel->errors();
                log_message('error', 'ERROR INSERT GAJI: ' . print_r($error, true));
            }
        }

        log_message('error', 'GAJI BERHASIL DISIMPAN: ' . $gajiSaved);

        // Simpan data bonus (jika ada) - GUNAKAN FOREACH untuk semua jasa_vendor_in_id
        $bonusSaved = 0;
        if (!empty($listBonus)) {
            foreach ($listBonus as $b) {
                if ((!empty($b['kg_bonus']) && $b['kg_bonus'] > 0) || (!empty($b['bonus_nominal']) && $b['bonus_nominal'] > 0)) {
                    foreach ($jasaVendorInIds as $jasaVendorInId) {
                        $bonusData = [
                            'biaya_kepiting_id' => $id,
                            'jasa_vendor_in_id' => $jasaVendorInId,
                            'barang_master_id' => $b['barang_master_id'] ?? null,
                            'barang_master_spesifikasi_id' => $b['barang_master_spesifikasi_id'] ?? null,
                            'kg_bonus' => $b['kg_bonus'] ?? 0,
                            'bonus_nominal' => $b['bonus_nominal'] ?? 0,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $result = $this->biayaKepitingBonusModel->insert($bonusData);
                        if ($result) {
                            $bonusSaved++;
                        } else {
                            $error = $this->biayaKepitingBonusModel->errors();
                            log_message('error', 'ERROR INSERT BONUS: ' . print_r($error, true));
                        }
                    }
                }
            }
        }

        log_message('error', 'BONUS BERHASIL DISIMPAN: ' . $bonusSaved);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            log_message('error', 'TRANSACTION FAILED');
            return $this->response->setJSON([
                'message' => "Gagal menyimpan data biaya kepiting - Transaction Failed",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        log_message('error', 'SEMUA DATA BERHASIL DISIMPAN - ID: ' . $id);

        return $this->response->setJSON([
            'message' => "Biaya kepiting berhasil disimpan",
            'token' => csrf_hash(),
            'id' => encrypt($id),
            'status' => true
        ]);

    } catch (\Exception $e) {
        log_message('error', 'EXCEPTION: ' . $e->getMessage());
        log_message('error', 'EXCEPTION TRACE: ' . $e->getTraceAsString());
        
        $this->db->transRollback();
        return $this->response->setJSON([
            'message' => "Terjadi kesalahan: " . $e->getMessage(),
            'status' => false,
            'token' => csrf_hash()
        ]);
    }
}

    public function updateAction()
    {
        $listBarang = json_decode($this->request->getPost('listBarang'), true);
        $listPerolehanGaji = json_decode($this->request->getPost('listPerolehanGaji'), true);
        $listBonus = json_decode($this->request->getPost('listBonus'), true);
        $id = decrypt($this->request->getVar('id'));

        if (empty($listBarang)) {
            return $this->response->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if (empty($listBonus)) {
            return $this->response->setJSON([
                'message' => "Bonus tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // update header
        $this->biayaKepitingModel->update($id, [
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0'
        ]);

        $id_detail_all = [];
        $id_detail_all_bonus = [];

        // === UPDATE/INSERT DETAIL BARANG ===
        foreach ($listBarang as $b) {
            $check = $this->biayaKepitingDetailModel
                ->where('biaya_kepiting_id', $id)
                ->where('barang_master_id', $b['barang_master_id'])
                ->where('barang_master_spesifikasi_id', $b['barang_master_spesifikasi_id'])
                ->first();

            if ($check) {
                $this->biayaKepitingDetailModel->update($check['id'], [
                    'jumbo' => $b['jumbo'],
                    'ex_lump' => $b['ex_lump'],
                    'lump' => $b['lump'],
                    'special' => $b['special'],
                    'claw' => $b['claw'],
                    'mh' => $b['mh'],
                    'cf' => $b['cf'],
                ]);
                $id_detail_all[] = $check['id'];
            } else {
                $id_detail_new = $this->biayaKepitingDetailModel->insert([
                    'biaya_kepiting_id' => $id,
                    'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
                    'barang_master_id' => $b['barang_master_id'],
                    'barang_master_spesifikasi_id' => $b['barang_master_spesifikasi_id'],
                    'jumbo' => $b['jumbo'],
                    'ex_lump' => $b['ex_lump'],
                    'lump' => $b['lump'],
                    'special' => $b['special'],
                    'claw' => $b['claw'],
                    'mh' => $b['mh'],
                    'cf' => $b['cf'],
                ]);
                $id_detail_all[] = $id_detail_new;
            }
        }

        // === UPDATE/INSERT BONUS ===
        foreach ($listBonus as $b) {
            $check = $this->biayaKepitingBonusModel
                ->where('biaya_kepiting_id', $id)
                ->where('barang_master_id', $b['barang_master_id'])
                ->where('barang_master_spesifikasi_id', $b['barang_master_spesifikasi_id'])
                ->first();

            if ($check) {
                $this->biayaKepitingBonusModel->update($check['id'], [
                    'kg_bonus' => $b['kg_bonus'],
                    'bonus_nominal' => $b['bonus_nominal']
                ]);
                $id_detail_all_bonus[] = $check['id'];
            } else {
                $id_detail_new = $this->biayaKepitingBonusModel->insert([
                    'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
                    'biaya_kepiting_id' => $id,
                    'barang_master_id' => $b['barang_master_id'],
                    'barang_master_spesifikasi_id' => $b['barang_master_spesifikasi_id'],
                    'kg_bonus' => $b['kg_bonus'],
                    'bonus_nominal' => $b['bonus_nominal']
                ]);
                $id_detail_all_bonus[] = $id_detail_new;
            }
        }

        // DELETE yang tidak ada di list terbaru
        $this->biayaKepitingDetailModel->where('biaya_kepiting_id', $id)->whereNotIn('id', $id_detail_all)->delete();
        $this->biayaKepitingBonusModel->where('biaya_kepiting_id', $id)->whereNotIn('id', $id_detail_all_bonus)->delete();

        // === UPDATE GAJI ===
        foreach ($listPerolehanGaji as $b) {
            $check = $this->biayaKepitingGajiModel
                ->where('biaya_kepiting_id', $id)
                ->where('jenis', $b['description'])
                ->first();

            if ($check) {
                $this->biayaKepitingGajiModel->update($check['id'], [
                    'jumbo' => $b['jumbo'],
                    'ex_lump' => $b['ex_lump'],
                    'lump' => $b['lump'],
                    'special' => $b['special'],
                    'claw' => $b['claw'],
                    'mh' => $b['mh'],
                    'cf' => $b['cf'],
                ]);
            } else {
                $this->biayaKepitingGajiModel->insert([
                    'biaya_kepiting_id' => $id,
                    'jenis' => $b['description'],
                    'jumbo' => $b['jumbo'],
                    'ex_lump' => $b['ex_lump'],
                    'lump' => $b['lump'],
                    'special' => $b['special'],
                    'claw' => $b['claw'],
                    'mh' => $b['mh'],
                    'cf' => $b['cf'],
                ]);
            }
        }

        return $this->response->setJSON([
            'message' => "Biaya kepiting berhasil diupdate",
            'token' => csrf_hash(),
            'id' => encrypt($id),
            'status' => true
        ]);
    }


    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->biayaKepitingModel->delete($id);
        $this->biayaKepitingDetailModel->where('biaya_kepiting_id', $id)->delete();
        $this->biayaKepitingGajiModel->where('biaya_kepiting_id', $id)->delete();
        $this->biayaKepitingBonusModel->where('biaya_kepiting_id', $id)->delete();

        return response()->setJSON([
            'token' => csrf_token(),
            'status' => true,
            'message' => "Biaya kepiting berhasil dihapus"
        ]);
    }


    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->biayaKepitingModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'token' => csrf_token(),
            'status' => true,
            'message' => "Biaya kepiting berhasil diposting"
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        // var_dump($id);
        // die;
        $biayaKepiting = $this->biayaKepitingModel->find($id);
        if ($biayaKepiting == null) {
            return redirect()->to('biaya-kepiting');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'biayaKepiting' => $this->biayaKepitingModel
                                    ->select('biaya_kepiting.*, jasa_vendor_out_kepiting_kukus.jenis_barang')
                                    ->join('jasa_vendor_in_kepiting_kukus', 'jasa_vendor_in_kepiting_kukus.id = biaya_kepiting.jasa_vendor_in_id', 'left')
                                    ->join('jasa_vendor_in_kepiting_kukus_detail', 'jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_in_kepiting_kukus_id = jasa_vendor_in_kepiting_kukus.id', 'left')
                                    ->join('jasa_vendor_out_kepiting_kukus', 'jasa_vendor_out_kepiting_kukus.id = jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_id', 'left')
                                    ->where('biaya_kepiting.id', $id)
                                    ->first(),
            'biayaKepitingBonus' => $this->biayaKepitingBonusModel->dropdownBarang($biayaKepiting['jasa_vendor_in_id'], $id),
            'biayaKepitingDetail' => $this->biayaKepitingModel->dropdownBarangKepitingKukusPrint($biayaKepiting['jasa_vendor_in_id'], $id),
            'dataPerolehanGaji' => $this->biayaKepitingModel->dropdownPerolehanGaji($id)
        ];

        $data['vendor'] = $this->vendorModel->find($biayaKepiting['vendor_id']);
        $data['gajiBiayaKepiting'] = $this->biayaKepitingGajiModel->where('biaya_kepiting_id', $biayaKepiting['id'])->findAll();

        $this->dompdf->loadHtml(view('jasaVendor/biayaKepiting/print', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream("Biaya Kepiting", array("Attachment" => false));
    }

    public function getNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouseID = $this->request->getVar('warehouse_id');

        if (empty($warehouseID)) {
            $no = $this->biayaKepitingModel->get_no(date('m'), date('Y'), $last_day, "", $warehouseID);
        } else {
            $warehouse = $this->warehouseModel->where('id', $warehouseID)->first();
            $no = $this->biayaKepitingModel->get_no(date('m'), date('Y'), $last_day, strtoupper($warehouse['code_warehouse']), $warehouseID);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    public function searchBarang()
    {
        $term = $this->request->getGet('q');

        $data = $this->barangMasterSpesifikasiModel
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
            ->groupStart()
                ->like('barang_master.barang_name', $term)
            ->orLike('barang_master_spesifikasi.spek_name', $term)
            ->groupEnd()
            ->where('barang_master.deleted_at', null)
            ->where('barang_master_spesifikasi.deleted_at', null)
            ->where('barang_master.company_id', $this->this_company_id)
            ->select('
                barang_master_spesifikasi.id,
                barang_master.barang_name,
                barang_master_spesifikasi.spek_name,
            ')
            ->findAll(10); // limit biar ringan

        $results = array_map(function ($row) {
            return [
                'id'     => $row['id'], // ID spek
                'barang_name'   => $row['barang_name'] . ' - ' . $row['spek_name'], // tampil di select2
            ];
        }, $data);

        return $this->response->setJSON($results);
    }


    public function dropdownBarang()
    {
        $jasaVendorInID = $this->request->getVar('jasa_vendor_in_id'); // bisa array
        $id = $this->request->getVar('id'); // bisa array atau null

        if (empty($jasaVendorInID)) {
            return response()->setJSON([
                'message' => "Jasa Vendor In ID kosong",
                'token' => csrf_hash(),
                'status' => false
            ]);
        }

        // Normalisasi ke array
        $jviIDs = is_array($jasaVendorInID) ? $jasaVendorInID : [$jasaVendorInID];
        $ids = !empty($id) ? (is_array($id) ? $id : [$id]) : [];

        // Ambil data barang, perolehan gaji, bonus
        if (!empty($ids)) {
            $data = $this->biayaKepitingModel->dropdownBarangKepitingKukus($jviIDs, $ids);
            $dataPerolehanGaji = $this->biayaKepitingModel->dropdownPerolehanGaji($ids);
            $dataBonus = $this->biayaKepitingBonusModel->dropdownBarang($jviIDs, $ids);
        } else {
            $data = $this->biayaKepitingModel->dropdownBarangKepitingKukus($jviIDs);
            $dataPerolehanGaji = $this->biayaKepitingModel->dropdownPerolehanGaji();
            $dataBonus = $this->biayaKepitingBonusModel->dropdownBarang($jviIDs);
        }

        // ============================
        //  AMBIL DETAIL VENDOR PER JVI
        // ============================

        $vendorList = [];
        $vendorInList = [];

        foreach ($jviIDs as $jviId) {

            $vendorIn = $this->jasaVendorInModel
                ->where('id', $jviId)
                ->first();

            if (!$vendorIn) {
                continue;
            }

            $vendor = $this->vendorModel
                ->where('id', $vendorIn['vendor_id'])
                ->first();

            $vendorInList[$jviId] = $vendorIn;
            $vendorList[$jviId] = $vendor;
        }

        return response()->setJSON([
            'data' => $data,
            'dataPerolehanGaji' => $dataPerolehanGaji,
            'dataBonus' => $dataBonus,

            // PENTING → per VendorIn ID
            'dataVendorIn' => $vendorInList,
            'dataVendor' => $vendorList,

            'token' => csrf_hash(),
            'status' => true
        ]);
    }


    public function autoComplete()
    {
        $listBarang = json_decode($_POST['listBarang']);
        $listPerolehanGaji = json_decode($_POST['listPerolehanGaji']);
        $listBonus = json_decode($_POST['listBonus']);
        $listDataVendor = json_decode($_POST['listDataVendor']);

        return response()->setJSON([
            'data' => $listBarang,
            'dataPerolehanGaji' => $listPerolehanGaji,
            'dataBonus' => $listBonus,
            'dataVendor' => $listDataVendor,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
}
