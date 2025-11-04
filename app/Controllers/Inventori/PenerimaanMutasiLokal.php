<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\PenerimaanMutasiModel;
use App\Models\SatuansModel;
use App\Models\StockRevampModel;
use App\Models\WarehousesModel;

class PenerimaanMutasiLokal extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $divisiModel;
    protected $metaDataModel;
    protected $satuanModel;
    protected $stockRevampModel;
    protected $mutasiModel;
    protected $mutasiDetailModel;
    protected $warehouseModel;
    protected $penerimaanMutasiModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->metaDataModel = new MetadataModel();
        $this->satuanModel = new SatuansModel();
        $this->stockRevampModel = new StockRevampModel();
        $this->mutasiModel = new MutasiModel();
        $this->mutasiDetailModel = new MutasiDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->penerimaanMutasiModel = new PenerimaanMutasiModel();
    }

    public function index()
    {
        return view('Warehouse/penerimaanMutasi/index_lokal');
    }

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('Warehouse/penerimaanMutasi/form_lokal', $data);
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            'penerimaan_mutasi.company_id' => $this->this_company_id,
            'penerimaan_mutasi.tipe_mutasi' => "LOKAL",
            'penerimaan_mutasi.deletedAt' => null
        ];

        $dataQry = $this->penerimaanMutasiModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataResult = array();

        foreach ($dataQry['data'] as $data) {
            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_mutasi_no"  => $data->penerimaan_mutasi_no,
                "divisi"       => $data->divisi,
                "multiple_no_mutasi"    => str_replace(['"', ']', '['], " ", $data->multiple_no_mutasi),
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
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

    public function detail($id)
    {
        $id = decrypt($id);
        $penerimaanMutasi = $this->penerimaanMutasiModel->where('id', $id)->first();

        if ($penerimaanMutasi == null) {
            return redirect()->to('penerimaan-mutasi');
        }

        $penerimaanMutasiDetail = $this->penerimaanMutasiModel->getListBarangMutasi(
            json_decode($penerimaanMutasi['multiple_mutasi_id']),
            $id,
            true
        );

        $data = [
            'tanggal' => date('Y-m-d'),
            'penerimaanMutasi' => $penerimaanMutasi,
            'penerimaanMutasiDetail' => $penerimaanMutasiDetail,
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('Warehouse/penerimaanMutasi/form_lokal', $data);
    }
}
