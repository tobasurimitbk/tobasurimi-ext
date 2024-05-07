<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\PenerimaanMutasiDetailModel;
use App\Models\WarehousesModel;

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
    }


    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('Warehouse/mutasi/index', $data);
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
            "divisi_id" => $this->request->getVar("divisi_id"),
            "status" => $this->request->getVar("status"),
            "no_mutasi" => $this->request->getVar("no_mutasi"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'mutasi.company_id' => $this->this_company_id,
            'divisis.deletedAt' => null,
            'mutasi.deletedAt' => null
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->mutasiModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            $listItem = $this->mutasiDetailModel->where('mutasi_id', $data->id)->findAll();
            $totalItem = count($listItem);
            $divisiTujuan = $this->divisiModel->find($data->divisi_tujuan_id);
            $warehouseTujuan = $this->warehouseModel->find($data->warehouse_tujuan_id);
            $warehouseAsal = $this->warehouseModel->find($data->warehouse_asal_id);

            $dokumenMutasi = $this->metaDataModel->find($data->bc_id);
            $divisiTujuanName = $divisiTujuan == null ? '-' : $divisiTujuan['divisi'];
            $warehouseTujuanName = $warehouseTujuan == null ? '-' : $warehouseTujuan['warehouse_name'];
            $warehouseAsalName = $warehouseAsal == null ? '-' : $warehouseAsal['warehouse_name'];

            $penerimaanTotalDetail =   $this->penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $data->id)
                ->where('deletedAt', null)
                ->groupBy('mutasi_id')
                ->findAll();

            $totalQtyMutasi = $this->mutasiDetailModel
                ->select('SUM(qty) AS qty_mutasi')
                ->where('mutasi_id', $data->id)
                ->where('deletedAt', null)
                ->groupBy('mutasi_id')
                ->findAll();

            $totalDiterima = count($penerimaanTotalDetail) == 0 ? 0 : $penerimaanTotalDetail[0]['qty_diterima'];
            $totalMutasi = count($totalQtyMutasi) == 0 ? 0 : $totalQtyMutasi[0]['qty_mutasi'];

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_mutasi"             => $data->no_mutasi,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "warehouse_asal"        =>  $data->divisi . ' - ' . $warehouseAsalName,
                "warehouse_tujuan"      => $divisiTujuanName . ' - ' . $warehouseTujuanName,
                "dokumen_mutasi"        => $dokumenMutasi['value'],
                "total_item"            => $totalItem,
                "state"                 => $totalDiterima == $totalMutasi ? '1' : '0',
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
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'tanggal' => date('Y-m-d'),
        ];

        return view('Warehouse/mutasi/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $mutasi = $this->mutasiModel->find($id);
        if ($mutasi == null) {
            return redirect()->to('mutasi');
        }
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'mutasi' => $mutasi,
            'mutasiDetail' => $this->mutasiDetailModel->getMutasiDetail($id),
            'warehouseAsal' => $this->warehouseModel->where('divisi_id', $mutasi['divisi_asal_id'])->findAll(),
            'warehouseTujuan' => $this->warehouseModel->where('divisi_id', $mutasi['divisi_tujuan_id'])->findAll(),
            'divisiTujuan' => $this->divisiModel->getDivisiExcept($mutasi['divisi_asal_id']),
        ];

        return view('Warehouse/mutasi/form', $data);
    }

    public function createAction()
    {
        $first = $this->mutasiModel->where('company_id', $this->this_company_id)->where('no_mutasi', $this->request->getVar('no_mutasi'))->first();

        if ($first != null) {
            return response()->setJSON([
                'message' => "Nomor Mutasi Sudah Ada",
                'token' => csrf_hash(),
                'status' => false,
            ]);
        }

        $bc_id = $this->warehouseModel->getDokumenMutasiBarang(
            $this->request->getVar('warehouse_asal_id'),
            $this->request->getVar('warehouse_tujuan_id')
        );

        $id = $this->mutasiModel->insert([
            'company_id' => $this->this_company_id,
            'bc_id' => $bc_id,
            'tanggal' => date('Y-m-d'),
            'no_mutasi' => $this->request->getVar('no_mutasi'),
            'divisi_asal_id' => $this->request->getVar('divisi_asal_id'),
            'divisi_tujuan_id' => $this->request->getVar('divisi_tujuan_id'),
            'warehouse_asal_id' => $this->request->getVar('warehouse_asal_id'),
            'warehouse_tujuan_id' => $this->request->getVar('warehouse_tujuan_id'),
            'keterangan' => $this->request->getVar('keterangan'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'createdBy' => $this->this_user_id,
            'status_posting' => '0'
        ]);

        foreach (json_decode($_POST['listMutasi']) as $l) {
            $this->mutasiDetailModel->insert([
                'mutasi_id' => $id,
                'stock_id' => $l->stock_id,
                'bc_id' => $l->bc_id,
                'no_aju' => $l->no_aju,
                'stock_dokumen' => $l->stock_dokumen,
                'qty' => $l->qty
            ]);
        }

        return response()->setJSON([
            'id' => encrypt($id),
            'message' => "Mutasi berhasil disimpan",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $bc_id = $this->warehouseModel->getDokumenMutasiBarang(
            $this->request->getVar('warehouse_asal_id'),
            $this->request->getVar('warehouse_tujuan_id')
        );

        $this->mutasiModel->update($id, [
            'company_id' => $this->this_company_id,
            'bc_id' => $bc_id,
            // 'no_mutasi' => $this->request->getVar('no_mutasi'),
            'divisi_asal_id' => $this->request->getVar('divisi_asal_id'),
            'divisi_tujuan_id' => $this->request->getVar('divisi_tujuan_id'),
            'warehouse_asal_id' => $this->request->getVar('warehouse_asal_id'),
            'warehouse_tujuan_id' => $this->request->getVar('warehouse_tujuan_id'),
            'keterangan' => $this->request->getVar('keterangan'),
            'createdBy' => $this->this_user_id,
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'status_posting' => '0'
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach (json_decode($_POST['listMutasi']) as $l) {
            // CHECK
            $check = $this->mutasiDetailModel
                ->where('mutasi_id', $id)
                ->where('stock_id', $l->stock_id)
                ->where('bc_id', $l->bc_id)
                ->where('no_aju', $l->no_aju)
                ->where('stock_dokumen', $l->stock_dokumen)
                ->first();

            if ($check != null) {
                $this->mutasiDetailModel->update($check['id'], [
                    'mutasi_id' => $id,
                    'stock_id' => $l->stock_id,
                    'bc_id' => $l->bc_id,
                    'no_aju' => $l->no_aju,
                    'stock_dokumen' => $l->stock_dokumen,
                    'qty' => $l->qty
                ]);

                array_push($id_detail_all, $check['id']);
            } else {
                // NEW
                // DELETE
                $this->mutasiDetailModel
                    ->where('mutasi_id', $id)
                    ->where('stock_id', $l->stock_id)
                    ->where('bc_id', $l->bc_id)
                    ->where('no_aju', $l->no_aju)
                    ->where('stock_dokumen', $l->stock_dokumen)
                    ->delete();

                // INSERT
                $id_detail_new = $this->mutasiDetailModel->insert([
                    'mutasi_id' => $id,
                    'stock_id' => $l->stock_id,
                    'bc_id' => $l->bc_id,
                    'no_aju' => $l->no_aju,
                    'stock_dokumen' => $l->stock_dokumen,
                    'qty' => $l->qty
                ]);

                array_push($id_detail_all,  $id_detail_new);
            }
        }

        $this->mutasiDetailModel->where('mutasi_id', $id)->whereNotIn('id', $id_detail_all)->delete();
        return response()->setJSON([
            'message' => "Mutasi berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
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
        $id = decrypt($this->request->getVar('id'));
        $this->mutasiModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'message' => "Mutasi berhasil diposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
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
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisi_id = $this->request->getVar('divisi_id');

        if (empty($divisi_id)) {
            $no = $this->mutasiModel->get_no(date('m'), date('Y'), $last_day, "", $divisi_id);
        } else {
            $divisi = $this->divisiModel->where('id', $divisi_id)->first();
            $no = $this->mutasiModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisi_id);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
