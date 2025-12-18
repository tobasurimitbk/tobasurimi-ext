<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BC27Model;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\MutasiGlobalDetailModel;
use App\Models\MutasiGlobalModel;
use App\Models\PenerimaanMutasiGlobalDetailModel;
use App\Models\SatuansModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampLogModel;
use App\Models\StockRevampModel;
use App\Models\WarehousesModel;
use Exception;

class MutasiGlobal extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $companyModel;
    protected $mutasiGlobalModel;
    protected $mutasiGlobalDetailModel;
    protected $penerimaanMutasiGlobalDetailModel;
    protected $warehouseModel;
    protected $bc27Model;
    protected $stockRevampModel;
    protected $satuanModel;
    protected $stockRevampDetailModel;
    protected $stockRevampLogModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->companyModel = new CompaniesModel();
        $this->mutasiGlobalModel = new MutasiGlobalModel();
        $this->mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $this->penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->bc27Model = new BC27Model();
        $this->satuanModel = new SatuansModel();
        $this->stockRevampModel = new StockRevampModel();
        $this->stockRevampDetailModel = new StockRevampDetailModel();
        $this->stockRevampLogModel = new StockRevampLogModel();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id)
        ];

        return view('Warehouse/mutasi/index_global', $data);
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
            "sort"              => $this->request->getVar("sort"),
            "sortType"          => $this->request->getVar("sortType"),
            "search"            => $this->request->getVar("search"),
            "dateStart"         => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"           => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $dataResult = array();

        $condition = [
            'mutasi_global.company_asal_id' => $this->this_company_id,
            'mutasi_global.deletedAt' => null
        ];

        $dataQry = $this->mutasiGlobalModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "no_mutasi"             => $data['no_mutasi'],
                "tanggal"               => date('d/m/Y', strtotime($data['tanggal'])),
                "divisi"                => $data['divisi'],
                "warehouse_name"        => $data['warehouse_name'],
                "company_tujuan_name"   => $data['company_tujuan_name'],
                "bc_doc"                => $data['no_aju'] == null ? "" : $data['no_aju'] . " / " . $data['no_daftar'],
                "status_posting"        => $data['status_posting']
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

        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description !=', "kemasan")
            ->findAll();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->where('deletedAt', null)->findAll();

        $data = [
            'tipeBarang' => $dataTipeBarang,
            'divisi' => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'tanggal' => date('Y-m-d'),
            'companyAsalName' => session()->get('login')->this_company,
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'dataValuta' => $dataValuta
        ];

        return view('Warehouse/mutasi/form_global', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $mutasiGlobal = $this->mutasiGlobalModel->where('id', $id)->first();
        if ($mutasiGlobal == null) {
            return redirect()->to('mutasi/global');
        }

        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description !=', "kemasan")
            ->findAll();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataWarehouseAsal = $this->warehouseModel->where('id', $mutasiGlobal['warehouse_asal_id'])->findAll();
        $dataMutasiGlobalDetail = $this->mutasiGlobalDetailModel->getDetail($id);
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->where('deletedAt', null)->findAll();

        $data = [
            'tipeBarang' => $dataTipeBarang,
            'divisi' => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'mutasiGlobal' => $mutasiGlobal,
            'mutasiGlobalDetail' => $dataMutasiGlobalDetail,
            'warehouseAsal' => $dataWarehouseAsal,
            'companyAsalName' => session()->get('login')->this_company,
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'dataValuta' => $dataValuta
        ];

        return view('Warehouse/mutasi/form_global', $data);
    }

    public function createAction()
    {
        // return response()->setJSON([
        //     'listMutasi' => json_decode($_POST['listMutasi']),
        //     '$_POST' => $_POST,
        //     'token' => csrf_hash()
        // ]);
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $noMutasi = $this->request->getVar('no_mutasi');
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));

            $first = $this->mutasiGlobalModel
                ->where('company_asal_id', $this->this_company_id)
                ->where('no_mutasi', $noMutasi)
                ->where('deletedAt', null)
                ->first();

            if ($first != null) {
                $noMutasi = $this->get_no_str($tanggal);
            }

            $id = $this->mutasiGlobalModel->insert([
                'no_mutasi' => $this->request->getVar('no_mutasi'),
                'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
                'company_asal_id' => $this->this_company_id,
                'tanggal' => $tanggal,
                'divisi_asal_id' => $this->request->getVar('divisi_asal_id'),
                'warehouse_asal_id' => $this->request->getVar('warehouse_asal_id'),
                'keterangan' => $this->request->getVar('keterangan'),
                'createdBy' => $this->this_user_id,
                'status_posting' => 0
            ]);


            foreach (json_decode($_POST['listMutasi']) as $l) {
                $this->mutasiGlobalDetailModel->insert([
                    'mutasi_global_id' => $id,
                    'stock_detail_id' => $l->id,
                    'qty_mutasi' => $l->mutasi->qty_mutasi,
                    'unit_id_mutasi' => $l->mutasi->unit_id_mutasi,
                    'qty_konversi' => $l->mutasi->qty_konversi,
                    'unit_id_konversi' => $l->mutasi->unit_id_konversi,
                    'hasil_mutasi' => $l->mutasi->hasil_mutasi,
                    'harga_satuan' => $l->mutasi->harga_satuan,
                    'nilai_tukar' => $l->mutasi->nilai_tukar,
                    'sub_total' => $l->mutasi->sub_total,
                    'valas_id' => $l->mutasi->valas_id
                ]);
            }

            $db->transCommit();
            return response()->setJSON([
                'message' => "Mutasi berhasil disimpan",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $id = decrypt($this->request->getVar('id'));
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $noMutasi = $this->request->getVar('no_mutasi');

            $first = $this->mutasiGlobalModel
                ->where('company_asal_id', $this->this_company_id)
                ->where('no_mutasi', $noMutasi)
                ->where('deletedAt', null)
                ->where('id !=', $id)
                ->first();

            if ($first != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false,
                    'message' => "nomor mutasi sudah digunakan",
                ]);
            }

            $this->mutasiGlobalModel->update($id, [
                'no_mutasi' => $this->request->getVar('no_mutasi'),
                'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
                'company_asal_id' => $this->this_company_id,
                'tanggal' => $tanggal,
                'divisi_asal_id' => $this->request->getVar('divisi_asal_id'),
                'warehouse_asal_id' => $this->request->getVar('warehouse_asal_id'),
                'keterangan' => $this->request->getVar('keterangan'),
                'createdBy' => $this->this_user_id,
                'status_posting' => 0
            ]);

            // get all id detail
            $id_detail_all = [];

            foreach (json_decode($_POST['listMutasi']) as $l) {


                // CHECK
                $check = $this->mutasiGlobalDetailModel
                    ->where('mutasi_global_id', $id)
                    ->where('stock_detail_id', $l->id)
                    ->where('deletedAt', null)
                    ->first();

                if ($check != null) {
                    $this->mutasiGlobalDetailModel->update($check['id'], [
                        'mutasi_global_id' => $id,
                        'stock_detail_id' => $l->id,
                        'qty_mutasi' => $l->mutasi->qty_mutasi,
                        'unit_id_mutasi' => $l->mutasi->unit_id_mutasi,
                        'qty_konversi' => $l->mutasi->qty_konversi,
                        'unit_id_konversi' => $l->mutasi->unit_id_konversi,
                        'hasil_mutasi' => $l->mutasi->hasil_mutasi,
                        'harga_satuan' => $l->mutasi->harga_satuan,
                        'nilai_tukar' => $l->mutasi->nilai_tukar,
                        'sub_total' => $l->mutasi->sub_total,
                        'valas_id' => $l->mutasi->valas_id
                    ]);

                    array_push($id_detail_all, $check['id']);
                } else {
                    // NEW
                    // DELETE
                    $this->mutasiGlobalDetailModel
                        ->where('mutasi_global_id', $id)
                        ->where('stock_detail_id', $l->id)
                        ->delete();

                    // INSERT
                    $id_detail_new = $this->mutasiGlobalDetailModel->insert([
                        'mutasi_global_id' => $id,
                        'stock_detail_id' => $l->id,
                        'qty_mutasi' => $l->mutasi->qty_mutasi,
                        'unit_id_mutasi' => $l->mutasi->unit_id_mutasi,
                        'qty_konversi' => $l->mutasi->qty_konversi,
                        'unit_id_konversi' => $l->mutasi->unit_id_konversi,
                        'hasil_mutasi' => $l->mutasi->hasil_mutasi,
                        'harga_satuan' => $l->mutasi->harga_satuan,
                        'nilai_tukar' => $l->mutasi->nilai_tukar,
                        'sub_total' => $l->mutasi->sub_total,
                        'valas_id' => $l->mutasi->valas_id
                    ]);

                    array_push($id_detail_all,  $id_detail_new);
                }
            }

            $this->mutasiGlobalDetailModel->where('mutasi_id', $id)->whereNotIn('id', $id_detail_all)->delete();
            $db->transCommit();
            return response()->setJSON([
                'message' => "Mutasi berhasil diupdate",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->mutasiGlobalModel->delete($id);
        $this->mutasiGlobalDetailModel->where('mutasi_global_id', $id)->delete();

        return response()->setJSON([
            'message' => "Mutasi berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function posting()
    {

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $mutasi = $this->mutasiGlobalModel->where('id', $id)->first();
            $mutasiDetail = $this->mutasiGlobalDetailModel->where('mutasi_global_id', $id)->where('deletedAt', null)->findAll();

            foreach ($mutasiDetail as $m) {
                $data = [
                    'stock_detail_id' => $m['stock_detail_id'],
                    'qty_digunakan' => $m['qty_konversi'],
                    'keterangan' => $mutasi['no_mutasi'],
                    'reference_tujuan_id' => $mutasi['id'],
                    'reference_tujuan_type' => "MUTASI GLOBAL"
                ];

                $this->stockRevampModel->outStockRevamp(
                    $db,
                    $data
                );
            }
            $this->mutasiGlobalModel->update($id, ['status_posting' => 1]);
            $db->transCommit();
            return response()->setJSON([
                'message' => "Mutasi berhasil diposting",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unPosting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            // $mutasi = $this->mutasiModel->where('id', $id)->first();
            // $mutasiDetail = $this->mutasiDetailModel->where('mutasi_id', $id)->where('deletedAt', null)->findAll();
            $res = $this->unposting_mutasi_revamp(
                $id
            );
            if (!$res) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal UnPosting : Stock Barang Sudah Digunakan",
                    'token' => csrf_hash()
                ]);
            }

            $this->mutasiGlobalModel->update($id, ['status_posting' => 0]);
            $db->transCommit();
            return response()->setJSON([
                'message' => "Mutasi berhasil diunposting",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unposting_mutasi_revamp($mutasiId)
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $mutasiDetail = $this->mutasiGlobalDetailModel
                ->where('mutasi_global_id', $mutasiId)
                ->where('deletedAt', null)->findAll();

            $isFailed = false;
            foreach ($mutasiDetail as $m) {
                $stockDetail = $this->stockRevampDetailModel
                    ->where('id', $m['stock_detail_id'])
                    ->first();

                if ($stockDetail['qty_diterima'] != $m['hasil_mutasi']) {
                    // sudah ga sama dengan hasil mutasi gagal unpost
                    $isFailed = true;
                    break;
                }
            }

            if ($isFailed) {
                $db->transRollback();
                return false;
            }

            // Aman Stock Belum Digunakan
            foreach ($mutasiDetail as $m) {
                $stockDetail = $this->stockRevampDetailModel
                    ->where('id', $m['stock_detail_id'])
                    ->first();

                $stock = $this->stockRevampModel->where('id', $stockDetail['stock_id'])->first();

                if ($stock) {
                    $qtyNow = $stock['qty_diterima'] + $m['qty_konversi'];
                    $this->stockRevampModel->update($stock['id'], ['qty_bersih' => $qtyNow, 'qty_diterima' => $qtyNow]);
                }

                if ($stockDetail) {
                    $qtyNow = $stockDetail['qty_diterima'] + $m['qty_konversi'];
                    $this->stockRevampDetailModel->update($stockDetail['id'], [
                        'qty_bersih' => $qtyNow,
                        'qty_diterima' => $qtyNow
                    ]);
                }

                $this->stockRevampLogModel
                    ->where('stock_detail_id', $stockDetail['id'])
                    ->where('reference_tujuan_id', $mutasiId)
                    ->where('reference_tujuan_type', "MUTASI GLOBAL")
                    ->delete(null, true);
            }

            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            log_message('error', 'Unposting Stock Failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getMutasiNo()
    {
        $tanggal = $this->request->getVar('tanggal');
        if (empty($tanggal)) {
            return response()->setJSON([
                'status' => true,
                'data' => '',
                'token' => csrf_hash()
            ]);
        }

        $tanggal = formatDMYtoYMD($tanggal); // 2025-09-21
        $tanggalParts = explode('-', $tanggal);
        if (count($tanggalParts) !== 3) {
            return $this->response->setJSON([
                'data' => '',
                'status' => false,
                'message' => 'Format tanggal tidak valid. Gunakan dd/mm/yyyy.'
            ]);
        }

        $no = $this->get_no_str($tanggal);
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    private function get_no_str($tanggal)
    {
        $tanggalParts = explode('-', $tanggal);
        $month = $tanggalParts[1];
        $year = $tanggalParts[0];

        $no = $this->mutasiGlobalModel->get_no(
            $month,
            $year,
            $this->this_company_id
        );

        return $no;
    }
}
