<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\StockModel;

class StokAdjusment extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $stockModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->stockModel = new StockModel();
    }


    public function index()
    {
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('Warehouse/stockAdjusment/index', $data);
    }

    public function create()
    {
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'tanggal' => date('Y-m-d'),
            'jenisDokAju' => $this->metaDataModel->getByName("jenis_dok_aju")
        ];

        return view('Warehouse/stockAdjusment/form', $data);
    }

    public function getListBarangIsInit()
    {
        $data = $this->stockModel->getBarangAndStock(
            $this->request->getVar('type_barang'),
            $this->request->getVar('divisi_id'),
            $this->request->getVar('warehouse_id')
        );
        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
}
