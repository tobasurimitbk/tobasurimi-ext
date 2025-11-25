<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\SatuansModel;
use App\Models\StockRevampModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class MutasiLokal extends BaseController
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
    protected $dompdf;

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
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('Warehouse/mutasi/index_lokal');
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

        return view('Warehouse/mutasi/form_lokal', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $mutasi = $this->mutasiModel->where('id', $id)->first();
        if ($mutasi == null) {
            return redirect()->to('mutasi/lokal');
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

        return view('Warehouse/mutasi/form_lokal', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $mutasi = $this->mutasiModel
            ->select('mutasi.*,divisis.divisi')
            ->join('divisis', 'divisis.id = mutasi.divisi_asal_id', 'left')
            ->where('mutasi.id', $id)
            ->first();

        if ($mutasi == null) {
            return redirect()->to('mutasi/lokal');
        }
        $dataMutasiDetail = $this->mutasiDetailModel->getDetail($id);

        $data = [
            'mutasi' => $mutasi,
            'mutasiDetail' => $dataMutasiDetail,
        ];

        $this->dompdf->loadHtml(view('Warehouse/mutasi/print', $data));
        $this->dompdf->setPaper('A4', '');
        $this->dompdf->render();
        $this->dompdf->stream($mutasi['no_mutasi'], array("Attachment" => false));
        exit(0);
    }
}
