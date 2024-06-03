<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BC27Model;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\MutasiGlobalDetailModel;
use App\Models\MutasiGlobalModel;
use App\Models\WarehousesModel;

class MutasiGlobal extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $companyModel;
    protected $mutasiGlobalModel;
    protected $mutasiGlobalDetailModel;
    protected $warehouseModel;
    protected $bc27Model;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->companyModel = new CompaniesModel();
        $this->mutasiGlobalModel = new MutasiGlobalModel();
        $this->mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->bc27Model = new BC27Model();
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
            "divisi_id"         => $this->request->getVar("divisi_id"),
            "company_tujuan_id" => $this->request->getVar("company_tujuan_id"),
            "status"            => $this->request->getVar("status"),
            "no_mutasi"         => $this->request->getVar("no_mutasi"),
            "dateStart"         => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"           => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'mutasi_global.company_asal_id' => $this->this_company_id,
            'mutasi_global.deletedAt' => null
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->mutasiGlobalModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            $listItem = $this->mutasiGlobalDetailModel->where('mutasi_global_id', $data->id)->findAll();
            $totalItem = count($listItem);
            $warehouseAsal = $this->warehouseModel->find($data->warehouse_asal_id);
            $warehouseAsalName = $warehouseAsal == null ? '-' : $warehouseAsal['warehouse_name'];
            $bc27 = $this->bc27Model->where('mutasi_global_id', $data->id)->first();

            $totalDiterima = 0;
            $totalMutasi = 1;

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_mutasi"             => $data->no_mutasi,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "warehouse_asal"        => strtoupper($data->divisi . ' - ' . $warehouseAsalName),
                "company_tujuan"        => strtoupper($data->company_tujuan_name),
                "no_aju"                => $bc27 == null ? "BELUM DIBUAT" : $bc27['no_aju'],
                "total_item"            => $totalItem,
                "state"                 => $totalDiterima == $totalMutasi ? '1' : '0',
                "status_posting"        => $data->status_posting,
                "is_used"               => $bc27 == null ? '1' : '0'
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
            'companyAsalName' => session()->get('login')->this_company,
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id)
        ];

        return view('Warehouse/mutasi/form_global', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $mutasiGlobal = $this->mutasiGlobalModel->find($id);
        if ($mutasiGlobal == null) {
            return redirect()->to('mutasi/global');
        }
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'mutasiGlobal' => $mutasiGlobal,
            'mutasiGlobalDetail' => $this->mutasiGlobalDetailModel->getMutasiDetail($id),
            'warehouseAsal' => $this->warehouseModel->where('divisi_id', $mutasiGlobal['divisi_asal_id'])->findAll(),
            'companyAsalName' => session()->get('login')->this_company,
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id)
        ];

        return view('Warehouse/mutasi/form_global', $data);
    }

    public function createAction()
    {
        $first = $this->mutasiGlobalModel->where('company_asal_id', $this->this_company_id)->where('no_mutasi', $this->request->getVar('no_mutasi'))->first();

        if ($first != null) {
            return response()->setJSON([
                'message' => "Nomor Mutasi Sudah Ada",
                'token' => csrf_hash(),
                'status' => false,
            ]);
        }

        $id = $this->mutasiGlobalModel->insert([
            'no_mutasi' => $this->request->getVar('no_mutasi'),
            'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
            'company_asal_id' => $this->this_company_id,
            'tanggal' => date('Y-m-d'),
            'divisi_asal_id' => $this->request->getVar('divisi_asal_id'),
            'warehouse_asal_id' => $this->request->getVar('warehouse_asal_id'),
            'keterangan' => $this->request->getVar('keterangan'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'createdBy' => $this->this_user_id,
            'status_posting' => '0'
        ]);

        foreach (json_decode($_POST['listMutasi']) as $l) {
            $this->mutasiGlobalDetailModel->insert([
                'mutasi_global_id' => $id,
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

        $this->mutasiGlobalModel->update($id, [
            'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
            'company_asal_id' => $this->this_company_id,
            'divisi_asal_id' => $this->request->getVar('divisi_asal_id'),
            'warehouse_asal_id' => $this->request->getVar('warehouse_asal_id'),
            'keterangan' => $this->request->getVar('keterangan'),
            'createdBy' => $this->this_user_id,
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'status_posting' => '0'
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach (json_decode($_POST['listMutasi']) as $l) {
            // CHECK
            $check = $this->mutasiGlobalDetailModel
                ->where('mutasi_global_id', $id)
                ->where('stock_id', $l->stock_id)
                ->where('bc_id', $l->bc_id)
                ->where('no_aju', $l->no_aju)
                ->where('stock_dokumen', $l->stock_dokumen)
                ->first();

            if ($check != null) {
                $this->mutasiGlobalDetailModel->update($check['id'], [
                    'mutasi_global_id' => $id,
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
                $this->mutasiGlobalDetailModel
                    ->where('mutasi_global_id', $id)
                    ->where('stock_id', $l->stock_id)
                    ->where('bc_id', $l->bc_id)
                    ->where('no_aju', $l->no_aju)
                    ->where('stock_dokumen', $l->stock_dokumen)
                    ->delete();

                // INSERT
                $id_detail_new = $this->mutasiGlobalDetailModel->insert([
                    'mutasi_global_id' => $id,
                    'stock_id' => $l->stock_id,
                    'bc_id' => $l->bc_id,
                    'no_aju' => $l->no_aju,
                    'stock_dokumen' => $l->stock_dokumen,
                    'qty' => $l->qty
                ]);

                array_push($id_detail_all,  $id_detail_new);
            }
        }

        $this->mutasiGlobalDetailModel->where('mutasi_global_id', $id)->whereNotIn('id', $id_detail_all)->delete();
        return response()->setJSON([
            'message' => "Mutasi berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
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
        $id = decrypt($this->request->getVar('id'));
        $this->mutasiGlobalModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'message' => "Mutasi berhasil diposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function unPosting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->mutasiGlobalModel->update($id, ['status_posting' => '0']);
        return response()->setJSON([
            'message' => "Mutasi berhasil diunposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getMutasiNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisi_id = $this->request->getVar('divisi_id');

        if (empty($divisi_id)) {
            $no = $this->mutasiGlobalModel->get_no(date('m'), date('Y'), $last_day, "", $divisi_id);
        } else {
            $divisi = $this->divisiModel->where('id', $divisi_id)->first();
            $no = $this->mutasiGlobalModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisi_id);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
