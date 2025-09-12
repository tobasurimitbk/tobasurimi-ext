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
use Dompdf\Dompdf;

class BiayaKepiting extends BaseController
{

    protected $this_company_id;
    protected $this_user_id;
    protected $divisiModel;
    protected $warehouseModel;
    protected $biayaKepitingModel;
    protected $biayaKepitingDetailModel;
    protected $biayaKepitingGajiModel;
    protected $jasaVendorInModel;
    protected $jasaVendorInDetailModel;
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
        $this->vendorModel = new VendorModel();
        $this->metaDataModel = new MetadataModel();
        $this->biayaKepitingBonusModel = new BiayaKepitingBonusModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->dompdf = new Dompdf();
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
            'tanggal' => date('Y-m-d'),
            'jasaVendorIn' => $this->biayaKepitingModel->dropdownJasaVendorKepitingKukusIn(),
        ];

        return view('jasaVendor/biayaKepiting/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $biayaKepiting = $this->biayaKepitingModel->find($id);

        if ($biayaKepiting == null) {
            return redirect()->to('biaya-kepiting');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'biayaKepiting' => $this->biayaKepitingModel->find($id),
            'jasaVendorInDetail' => $this->biayaKepitingModel->getPenerimaanSuratJalanDetail($id),
        ];

        return view('jasaVendor/biayaKepiting/form', $data);
    }


    // public function createAction()
    // {

    //     $listBarang = json_decode($_POST['listBarang']);
    //     $listPerolehanGaji = json_decode($_POST['listPerolehanGaji']);
    //     $listBonus = json_decode($_POST['listBonus']);
    //     $jasaVendorIn = $this->jasaVendorInModel->find($this->request->getVar('jasa_vendor_in_id'));
    //     $biayaKepiting = $this->biayaKepitingModel->where('company_id', $this->this_company_id)->where('no_pembayaran', $this->request->getVar('no_pembayaran'))->first();

    //     if ($biayaKepiting != null) {
    //         return response()->setJSON([
    //             'message' => "No pembayaran sudah ada !",
    //             'status' => false,
    //             'token' => csrf_hash()
    //         ]);
    //     }

    //     if (count($listBarang) == 0) {
    //         return response()->setJSON([
    //             'message' => "Barang tidak boleh kosong",
    //             'status' => false,
    //             'token' => csrf_hash()
    //         ]);
    //     }

    //     if (count($listBonus) == 0) {
    //         return response()->setJSON([
    //             'message' => "Bonus tidak boleh kosong",
    //             'status' => false,
    //             'token' => csrf_hash()
    //         ]);
    //     }

    //     $id = $this->biayaKepitingModel->insert([
    //         'company_id' => $this->this_company_id,
    //         'divisi_id' => $jasaVendorIn['divisi_id'],
    //         'jasa_vendor_in_id' => $jasaVendorIn['id'],
    //         'vendor_id' => $jasaVendorIn['vendor_id'],
    //         'warehouse_id' => $jasaVendorIn['warehouse_id'],
    //         'no_pembayaran' => $this->request->getVar('no_pembayaran'),
    //         "tanggal" => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
    //         'keterangan' => $this->request->getVar('keterangan'),
    //         'status_posting' => '0'
    //     ]);

    //     foreach ($listBarang as $b) {
    //         $this->biayaKepitingDetailModel->insert([
    //             'biaya_kepiting_id' => $id,
    //             'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
    //             // 'barang_master_id' => $b->barang_master_id,
    //             // 'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
    //             'jumbo' => $b->jumbo,
    //             'ex_lump' => $b->ex_lump,
    //             'lump' => $b->lump,
    //             'special' => $b->special,
    //             'claw' => $b->claw,
    //             'mh' => $b->mh,
    //             'cf' => $b->cf,
    //         ]);
    //     }

    //     foreach ($listPerolehanGaji as $b) {
    //         $this->biayaKepitingGajiModel->insert([
    //             'biaya_kepiting_id' => $id,
    //             'jumbo' => $b->jumbo,
    //             'ex_lump' => $b->ex_lump,
    //             'lump' => $b->lump,
    //             'special' => $b->special,
    //             'claw' => $b->claw,
    //             'mh' => $b->mh,
    //             'cf' => $b->cf,
    //             'jenis' => $b->description
    //         ]);
    //     }

    //     foreach ($listBonus as $b) {
    //         if ($b->kg_bonus) {
    //             $this->biayaKepitingBonusModel->insert([
    //                 'biaya_kepiting_id' => $id,
    //                 'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
    //                 // 'barang_master_id' => $b->barang_master_id,
    //                 // 'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
    //                 'kg_bonus' => $b->kg_bonus,
    //                 'bonus_nominal' => $b->bonus_nominal
    //             ]);
    //         }
    //     }

    //     return response()->setJSON([
    //         'message' => "Biaya kepiting berhasil disimpan",
    //         'token' => csrf_token(),
    //         'id' => encrypt($id),
    //         'status' => true
    //     ]);
    // }


    public function createAction()
    {
        // Decode data JSON dengan parameter true untuk mendapatkan array assosiatif
        $listBarang = json_decode($this->request->getPost('listBarang'), true);
        $listPerolehanGaji = json_decode($this->request->getPost('listPerolehanGaji'), true);
        $listBonus = json_decode($this->request->getPost('listBonus'), true);
        
        $jasaVendorIn = $this->jasaVendorInModel->find($this->request->getVar('jasa_vendor_in_id'));
        $biayaKepiting = $this->biayaKepitingModel->where('company_id', $this->this_company_id)
            ->where('no_pembayaran', $this->request->getVar('no_pembayaran'))
            ->first();

        if ($biayaKepiting != null) {
            return $this->response->setJSON([
                'message' => "No pembayaran sudah ada!",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if (count($listBarang) == 0) {
            return $this->response->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Validasi bonus - ubah menjadi tidak wajib (opsional)
        // if (count($listBonus) == 0) {
        //     return $this->response->setJSON([
        //         'message' => "Bonus tidak boleh kosong",
        //         'status' => false,
        //         'token' => csrf_hash()
        //     ]);
        // }

        // Format tanggal
        $tanggal = $this->request->getVar("tanggal");
        if ($tanggal) {
            $date = \DateTime::createFromFormat("d/m/Y", $tanggal);
            $tanggalFormatted = $date ? $date->format("Y-m-d") : date("Y-m-d");
        } else {
            $tanggalFormatted = date("Y-m-d");
        }

        $id = $this->biayaKepitingModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $jasaVendorIn['divisi_id'],
            'jasa_vendor_in_id' => $jasaVendorIn['id'],
            'vendor_id' => $jasaVendorIn['vendor_id'],
            'warehouse_id' => $jasaVendorIn['warehouse_id'],
            'no_pembayaran' => $this->request->getVar('no_pembayaran'),
            "tanggal" => $tanggalFormatted,
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0',
        ]);

        // Jika insert gagal
        if (!$id) {
            return $this->response->setJSON([
                'message' => "Gagal menyimpan data biaya kepiting",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Simpan data barang
        foreach ($listBarang as $b) {
            $this->biayaKepitingDetailModel->insert([
                'biaya_kepiting_id' => $id,
                'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
                'barang_master_id' => $b['barang_master_id'] ?? null,
                'barang_master_spesifikasi_id' => $b['barang_master_spesifikasi_id'] ?? null,
                // 'tanggal_masuk' => $b['tanggal_masuk'] ?? null,
                // 'qty_sebelum_kopek' => $b['qty_sebelum_kopek'] ?? 0,
                'rasio' => $b['rasio'] ?? 0,
                'jumbo' => $b['spek']['JB'] ?? 0,
                'ex_lump' => $b['spek']['SP LUMP'] ?? 0,
                'lump' => $b['spek']['BF'] ?? 0,
                'special' => $b['spek']['SPL'] ?? 0,
                'claw' => $b['spek']['CLAW'] ?? 0,
                'mh' => $b['spek']['MH'] ?? 0,
                'cf' => $b['spek']['CF'] ?? 0,
            ]);
        }

        // Simpan data perolehan gaji
        foreach ($listPerolehanGaji as $b) {
            $this->biayaKepitingGajiModel->insert([
                'biaya_kepiting_id' => $id,
                'jenis' => $b['description'] ?? $b['value'] ?? 'unknown',
                'jumbo' => $b['jumbo'] ?? 0,
                'ex_lump' => $b['ex_lump'] ?? 0,
                'lump' => $b['lump'] ?? 0,
                'special' => $b['special'] ?? 0,
                'claw' => $b['claw'] ?? 0,
                'mh' => $b['mh'] ?? 0,
                'cf' => $b['cf'] ?? 0,
            ]);
        }

        // Simpan data bonus (jika ada)
        if (!empty($listBonus)) {
            foreach ($listBonus as $b) {
                if (!empty($b['kg_bonus']) && $b['kg_bonus'] > 0) {
                    $this->biayaKepitingBonusModel->insert([
                        'biaya_kepiting_id' => $id,
                        'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
                        'barang_master_id' => $b['barang_master_id'] ?? null,
                        'barang_master_spesifikasi_id' => $b['barang_master_spesifikasi_id'] ?? null,
                        // 'tanggal_masuk' => $b['tanggal_masuk'] ?? null,
                        // 'nama_barang' => $b['nama_barang'] ?? null,
                        // 'spesifikasi' => $b['spesifikasi'] ?? null,
                        'kg_bonus' => $b['kg_bonus'] ?? 0,
                        'bonus_nominal' => $b['bonus_nominal'] ?? 0,
                    ]);
                }
            }
        }

        return $this->response->setJSON([
            'message' => "Biaya kepiting berhasil disimpan",
            'token' => csrf_hash(),
            'id' => encrypt($id),
            'status' => true
        ]);
    }

    public function updateAction()
    {
        $listBarang = json_decode($_POST['listBarang']);
        $listPerolehanGaji = json_decode($_POST['listPerolehanGaji']);
        $listBonus = json_decode($_POST['listBonus']);
        $id = decrypt($this->request->getVar('id'));

        if (count($listBarang) == 0) {
            return response()->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if (count($listBonus) == 0) {
            return response()->setJSON([
                'message' => "Bonus tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $this->biayaKepitingModel->update($id, [
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0'
        ]);

        // get all id detail
        $id_detail_all = [];
        $id_detail_all_bonus = [];

        foreach ($listBarang as $b) {
            $check = $this->biayaKepitingDetailModel
                ->where('biaya_kepiting_id', $id)
                ->where('barang_master_id', $b->barang_master_id)
                ->where('barang_master_spesifikasi_id', $b->barang_master_spesifikasi_id)
                ->first();

            if ($check != null) {
                $this->biayaKepitingDetailModel->update($check['id'], [
                    'biaya_kepiting_id' => $id,
                    'barang_master_id' => $b->barang_master_id,
                    'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                    'jumbo' => $b->jumbo,
                    'ex_lump' => $b->ex_lump,
                    'lump' => $b->lump,
                    'special' => $b->special,
                    'claw' => $b->claw,
                    'mh' => $b->mh,
                    'cf' => $b->cf,
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // DELETE
                $this->biayaKepitingDetailModel
                    ->where('biaya_kepiting_id', $id)
                    ->where('barang_master_id', $b->barang_master_id)
                    ->where('barang_master_spesifikasi_id', $b->barang_master_spesifikasi_id)
                    ->delete();
                // INSERT
                $id_detail_new =  $this->biayaKepitingDetailModel->insert([
                    'biaya_kepiting_id' => $id,
                    'barang_master_id' => $b->barang_master_id,
                    'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
                    'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                    'jumbo' => $b->jumbo,
                    'ex_lump' => $b->ex_lump,
                    'lump' => $b->lump,
                    'special' => $b->special,
                    'claw' => $b->claw,
                    'mh' => $b->mh,
                    'cf' => $b->cf,
                ]);
                array_push($id_detail_all,  $id_detail_new);
            }
        }

        foreach ($listBonus as $b) {
            $check = $this->biayaKepitingBonusModel
                ->where('biaya_kepiting_id', $id)
                ->where('barang_master_id', $b->barang_master_id)
                ->where('barang_master_spesifikasi_id', $b->barang_master_spesifikasi_id)
                ->first();

            if ($check != null) {
                $this->biayaKepitingBonusModel->update($check['id'], [
                    'biaya_kepiting_id' => $id,
                    'barang_master_id' => $b->barang_master_id,
                    'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                    'kg_bonus' => $b->kg_bonus,
                    'bonus_nominal' => $b->bonus_nominal
                ]);
                array_push($id_detail_all_bonus, $check['id']);
            } else {
                // DELETE
                $this->biayaKepitingBonusModel
                    ->where('biaya_kepiting_id', $id)
                    ->where('barang_master_id', $b->barang_master_id)
                    ->where('barang_master_spesifikasi_id', $b->barang_master_spesifikasi_id)
                    ->delete();
                // INSERT
                $id_detail_new =  $this->biayaKepitingBonusModel->insert([
                    'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
                    'biaya_kepiting_id' => $id,
                    'barang_master_id' => $b->barang_master_id,
                    'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                    'kg_bonus' => $b->kg_bonus,
                    'bonus_nominal' => $b->bonus_nominal
                ]);
                array_push($id_detail_all_bonus,  $id_detail_new);
            }
        }

        $this->biayaKepitingBonusModel->where('biaya_kepiting_id', $id)->whereNotIn('id', $id_detail_all_bonus)->delete();
        $this->biayaKepitingDetailModel->where('biaya_kepiting_id', $id)->whereNotIn('id', $id_detail_all)->delete();

        foreach ($listPerolehanGaji as $b) {
            $check = $this->biayaKepitingGajiModel->where('biaya_kepiting_id', $id)->where('jenis', $b->description)->first();
            $this->biayaKepitingGajiModel->update($check['id'], [
                'biaya_kepiting_id' => $id,
                'jumbo' => $b->jumbo,
                'ex_lump' => $b->ex_lump,
                'lump' => $b->lump,
                'special' => $b->special,
                'claw' => $b->claw,
                'mh' => $b->mh,
                'cf' => $b->cf,
                'jenis' => $b->description
            ]);
        }

        return response()->setJSON([
            'message' => "Biaya kepiting berhasil diupdate",
            'token' => csrf_token(),
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
        $biayaKepiting = $this->biayaKepitingModel->find($id);
        if ($biayaKepiting == null) {
            return redirect()->to('biaya-kepiting');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'biayaKepiting' => $this->biayaKepitingModel->find($id),
            'biayaKepitingBonus' => $this->biayaKepitingBonusModel->dropdownBarang($biayaKepiting['jasa_vendor_in_id'], $id),
            'biayaKepitingDetail' => $this->biayaKepitingModel->dropdownBarangPrint($biayaKepiting['jasa_vendor_in_id'], $id),
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
        $jasaVendorInID = $this->request->getVar('jasa_vendor_in_id');
        $id = $this->request->getVar('id');
        if (empty($id)) {
            // $data = $this->biayaKepitingModel->dropdownBarang($jasaVendorInID);
            $data = $this->biayaKepitingModel->dropdownBarangKepitingKukus($jasaVendorInID);
            $dataPerolehanGaji = $this->biayaKepitingModel->dropdownPerolehanGaji();
            $dataBonus = $this->biayaKepitingBonusModel->dropdownBarang($jasaVendorInID);
        } else {
            $id = decrypt($id);
            $data = $this->biayaKepitingModel->dropdownBarang($jasaVendorInID, $id);
            $dataPerolehanGaji = $this->biayaKepitingModel->dropdownPerolehanGaji($id);
            $dataBonus = $this->biayaKepitingBonusModel->dropdownBarang($jasaVendorInID, $id);
        }

        $dataVendorIn = $this->jasaVendorInModel->where('id', $jasaVendorInID)->first();
        $dataVendor =  $this->vendorModel->where('id', $dataVendorIn['vendor_id'])->first();

        return response()->setJSON([
            'data' => $data,
            'dataPerolehanGaji' => $dataPerolehanGaji,
            'dataBonus' => $dataBonus,
            'dataVendor' => $dataVendor,
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
