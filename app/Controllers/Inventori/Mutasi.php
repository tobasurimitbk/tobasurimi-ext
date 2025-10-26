<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\PenerimaanMutasiDetailModel;
use App\Models\PPBKBModel;
use App\Models\SatuansModel;
use App\Models\StockRevampModel;
use App\Models\WarehousesModel;
use Exception;

class Mutasi extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $divisiModel;
    protected $metaDataModel;
    protected $mutasiModel;
    protected $mutasiDetailModel;
    protected $warehouseModel;
    protected $penerimaanMutasiDetailModel;
    protected $ppbkbModel;
    protected $satuanModel;
    protected $stockRevampModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->metaDataModel = new MetadataModel();
        $this->mutasiModel = new MutasiModel();
        $this->mutasiDetailModel = new MutasiDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $this->ppbkbModel = new PPBKBModel();
        $this->satuanModel = new SatuansModel();
        $this->stockRevampModel = new StockRevampModel();
    }

    public function index()
    {
        return view('Warehouse/mutasi/index');
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
            "search" => $this->request->getVar("search"),
            "status" => $this->request->getVar("status"),
            "dateStart" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"  => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $dataResult = array();

        $condition = [
            'mutasi.company_id' => $this->this_company_id,
            'mutasi.deletedAt' => null
        ];

        $dataQry = $this->mutasiModel->getList(
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
                "divisi_asal"           => $data['divisi_asal'],
                "divisi_tujuan"         => $data['divisi_tujuan'],
                "warehouse_asal"        => $data['warehouse_name_asal'],
                "warehouse_tujuan"      => $data['warehouse_name_tujuan'],
                "no_ppbkb"              => $data['no_ppbkb'],
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

        $data = [
            'tipeBarang' => $dataTipeBarang,
            'divisi' => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'tanggal' => date('Y-m-d'),
        ];

        return view('Warehouse/mutasi/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $mutasi = $this->mutasiModel->where('id', $id)->first();
        if ($mutasi == null) {
            return redirect()->to('mutasi');
        }

        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description !=', "kemasan")
            ->findAll();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataMutasiDetail = $this->mutasiDetailModel->getDetail($id);
        $dataWarehouseAsal = $this->warehouseModel->where('id', $mutasi['warehouse_asal_id'])->findAll();
        $dataWarehouseTujuan = $this->warehouseModel->where('id', $mutasi['warehouse_tujuan_id'])->findAll();

        $data = [
            'tipeBarang' => $dataTipeBarang,
            'divisi' => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'mutasi' => $mutasi,
            'mutasiDetail' => $dataMutasiDetail,
            'warehouseAsal' => $dataWarehouseAsal,
            'warehouseTujuan' => $dataWarehouseTujuan
        ];

        return view('Warehouse/mutasi/form', $data);
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

            $first = $this->mutasiModel
                ->where('company_id', $this->this_company_id)
                ->where('no_mutasi', $noMutasi)
                ->where('deletedAt', null)
                ->first();

            if ($first != null) {
                $noMutasi = $this->get_no_str($tanggal);
            }

            $id = $this->mutasiModel->insert([
                'company_id' => $this->this_company_id,
                'tanggal' => $tanggal,
                'no_mutasi' => $noMutasi,
                'divisi_asal_id' => $this->request->getVar('divisi_asal_id'),
                'divisi_tujuan_id' => $this->request->getVar('divisi_tujuan_id'),
                'warehouse_asal_id' => $this->request->getVar('warehouse_asal_id'),
                'warehouse_tujuan_id' => $this->request->getVar('warehouse_tujuan_id'),
                'keterangan' => $this->request->getVar('keterangan'),
                'createdBy' => $this->this_user_id,
                'status_posting' => 0
            ]);

            foreach (json_decode($_POST['listMutasi']) as $l) {
                $this->mutasiDetailModel->insert([
                    'mutasi_id' => $id,
                    'stock_detail_id' => $l->id,
                    'qty_mutasi' => $l->mutasi->qty_mutasi,
                    'unit_id_mutasi' => $l->mutasi->unit_id_mutasi,
                    'qty_konversi' => $l->mutasi->qty_konversi,
                    'unit_id_konversi' => $l->mutasi->unit_id_konversi,
                    'hasil_mutasi' => $l->mutasi->hasil_mutasi
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

            $first = $this->mutasiModel
                ->where('company_id', $this->this_company_id)
                ->where('no_mutasi', $noMutasi)
                ->where('id !=', $id)
                ->where('deletedAt', null)
                ->first();

            if ($first != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false,
                    'message' => "nomor mutasi sudah digunakan",
                ]);
            }

            $this->mutasiModel->update($id, [
                'company_id' => $this->this_company_id,
                'tanggal' => $tanggal,
                'no_mutasi' => $noMutasi,
                'divisi_asal_id' => $this->request->getVar('divisi_asal_id'),
                'divisi_tujuan_id' => $this->request->getVar('divisi_tujuan_id'),
                'warehouse_asal_id' => $this->request->getVar('warehouse_asal_id'),
                'warehouse_tujuan_id' => $this->request->getVar('warehouse_tujuan_id'),
                'keterangan' => $this->request->getVar('keterangan'),
                'createdBy' => $this->this_user_id,
                'status_posting' => 0
            ]);

            // get all id detail
            $id_detail_all = [];

            foreach (json_decode($_POST['listMutasi']) as $l) {
                // CHECK
                $check = $this->mutasiDetailModel
                    ->where('mutasi_id', $id)
                    ->where('stock_detail_id', $l->id)
                    ->where('deletedAt', null)
                    ->first();

                if ($check != null) {
                    $this->mutasiDetailModel->update($check['id'], [
                        'mutasi_id' => $id,
                        'stock_detail_id' => $l->id,
                        'qty_mutasi' => $l->mutasi->qty_mutasi,
                        'unit_id_mutasi' => $l->mutasi->unit_id_mutasi,
                        'qty_konversi' => $l->mutasi->qty_konversi,
                        'unit_id_konversi' => $l->mutasi->unit_id_konversi,
                        'hasil_mutasi' => $l->mutasi->hasil_mutasi
                    ]);

                    array_push($id_detail_all, $check['id']);
                } else {
                    // NEW
                    // DELETE
                    $this->mutasiDetailModel
                        ->where('mutasi_id', $id)
                        ->where('stock_detail_id', $l->id)
                        ->delete();

                    // INSERT
                    $id_detail_new = $this->mutasiDetailModel->insert([
                        'mutasi_id' => $id,
                        'stock_detail_id' => $l->id,
                        'qty_mutasi' => $l->mutasi->qty_mutasi,
                        'unit_id_mutasi' => $l->mutasi->unit_id_mutasi,
                        'qty_konversi' => $l->mutasi->qty_konversi,
                        'unit_id_konversi' => $l->mutasi->unit_id_konversi,
                        'hasil_mutasi' => $l->mutasi->hasil_mutasi
                    ]);

                    array_push($id_detail_all,  $id_detail_new);
                }
            }

            $this->mutasiDetailModel->where('mutasi_id', $id)->whereNotIn('id', $id_detail_all)->delete();
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
        $this->mutasiModel->delete($id);
        $this->mutasiDetailModel->where('mutasi_id', $id)->delete();

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
            // $mutasi = $this->mutasiModel->where('id', $id)->first();
            // $mutasiDetail = $this->mutasiDetailModel->where('mutasi_id', $id)->where('deletedAt', null)->findAll();

            // foreach ($mutasiDetail as $m) {
            //     // OUT STOCK REVAMP
            //     $data = [
            //         'stock_detail_id' => $m['stock_detail_id'],
            //         'qty_digunakan' => $m['qty_konversi'],
            //         'keterangan' => $mutasi['no_mutasi']
            //     ];

            //     $this->stockRevampModel->outStockRevamp(
            //         $db,
            //         $data
            //     );
            // }

            $this->mutasiModel->update($id, ['status_posting' => 1]);
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

            $this->mutasiModel->update($id, ['status_posting' => 0]);
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

    public function listDivisiExcept()
    {
        $divisi_id = $this->request->getVar('divisi_id');
        $result = $this->divisiModel->getDivisiExcept($divisi_id);

        return response()->setJSON([
            'status' => true,
            'data' => $result,
            'token' => csrf_hash()
        ]);
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

        $no = $this->mutasiModel->get_no(
            $month,
            $year,
            $this->this_company_id
        );

        return $no;
    }
}
