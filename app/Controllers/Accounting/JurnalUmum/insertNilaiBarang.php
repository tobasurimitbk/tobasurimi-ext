<?php

namespace App\Controllers\Accounting\JurnalUmum;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\Sub_AkunsModel;
use App\Models\WarehousesModel;

class insertNilaiBarang extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_company;
    protected $this_user_id;

    protected $divisiModel;
    protected $warehouseModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;

    protected $db;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_company = session()->get("login")->this_company;
        $this->this_user_id = session()->get("login")->user_id;

        $this->divisiModel = new DivisisModel;
        $this->warehouseModel = new WarehousesModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $metaDataModel = new MetadataModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $subAkunsModel = $Sub_AkunsModel->getAPAR($this->this_company_id);

        $data = [
            'kategoriBarangAkun' => $metaDataModel->asObject()->where('name', 'kategori_barang_akun')->findAll(),
            "subAkuns" => $subAkunsModel,
        ];
        return view('Accounting/jurnalUmum/indexInsert', $data);
    }

    public function dropdownDivisi()
    {
        $dataDivisi = $this->divisiModel->get_by_company_id($this->this_company_id);

        $data = [
            "data" => $dataDivisi
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownWarehouse()
    {
        $divisiID = $this->request->getVar('department_id');
        $dataWarehouse = $this->warehouseModel->get_by_divisi_id($this->this_company_id, $divisiID);

        $data = [
            "data" => $dataWarehouse
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarang()
    {
        $typeBarang = $this->request->getVar('type_barang');
        $dataBarang = $this->barangMasterModel->getBarangByTypeCondition($this->this_company_id, $typeBarang);

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }

    public function getData()
    {
        $payload = [
            "pageSize"          => $this->request->getVar("length"),
            "currentPage"       => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort"              => $this->request->getVar("sort"),
            "sorttype"          => $this->request->getVar("sortType"),
            "search"            => $this->request->getVar("search"),
        ];

        $addCondition = [
            "sort"              => $this->request->getVar("sort"),
            "sortType"          => $this->request->getVar("sortType"),
            "kode_department"   => $this->request->getVar("kode_department"),
            "kode_warehouse"    => $this->request->getVar("kode_warehouse"),
            "type_barang"       => $this->request->getVar('type_barang'),
            "kode_barang"       => $this->request->getVar("kode_barang"),
            "company_id"        => $this->this_company_id,
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            'barang_master_spesifikasi.deletedAt' => null,
        ];

        $dataQry = $this->barangMasterSpesifikasiModel->getListBarangSpesifikasiWithAccount($condition, $addCondition, $limit, $offset);

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataQry['data'],
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }
}
