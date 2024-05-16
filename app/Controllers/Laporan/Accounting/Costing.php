<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JurnalUmumModel;
use App\Models\ProductionResultDetailModel;
use App\Models\ProductionResultModel;
use App\Models\RasioBahanPenolongModel;
use App\Models\RasioBarangJadiModel;
use App\Models\RasioCostModel;
use App\Models\SettingCostingModel;
use App\Models\Sub_AkunsModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class Costing extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $settingCosting;
    protected $productionResultModel;
    protected $productionResultDetailModel;
    protected $jurnalUmumModel;
    protected $rasioBarangJadiModel;
    protected $rasioBahanPenolongModel;
    protected $rasioCostModel;
    protected $divisisModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->divisisModel = new DivisisModel();
        $this->settingCosting = new SettingCostingModel();
        $this->productionResultModel = new ProductionResultModel();
        $this->productionResultDetailModel = new ProductionResultDetailModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->rasioBarangJadiModel = new RasioBarangJadiModel();
        $this->rasioBahanPenolongModel = new RasioBahanPenolongModel();
        $this->rasioCostModel = new RasioCostModel();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisisModel->getDivisiAccess()
        ];
        return view('Laporan/LaporanCosting/index', $data);
    }

    public function getCostingData()
    {
        if (!empty($this->request->getVar('month'))) {
            $monthData = $this->request->getVar('month');
            list($month, $year) = explode('/', $monthData);
            $convertedDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $settingCosting = $this->settingCosting->getSettingCosting();
            $conditionProduction = [
                'tanggal_jurnal' => date('Y-m', strtotime($convertedDate)),
                'divisi_id' => $this->request->getVar('divisi_id'),
                'company_id' => $this->this_company_id,
            ];
            foreach ($settingCosting as &$valueSetting) {
                if ($valueSetting['name'] == "RAW MATERIAL I") {
                    $rasioMaterialI = $this->rasioBarangJadiModel->getDataRasioMaterialI($conditionProduction);
                    $valueSetting['rawMaterial'] = $rasioMaterialI;
                } else if ($valueSetting['name'] == "RAW MATERIAL II") {
                    $nameRasioMaterialII = $this->rasioBahanPenolongModel->getDataNameParentRasioMaterialII($conditionProduction);
                    $rasioMaterialII = $this->rasioBahanPenolongModel->getDataRasioMaterialII($conditionProduction);
                    $valueSetting['nameRawMaterialPenolong'] = $nameRasioMaterialII;
                    $valueSetting['rawMaterialPenolong'] = $rasioMaterialII;
                } else {
                    $nameRasioCost = $this->rasioCostModel->getDataNameCostRasioCost($conditionProduction);
                    $rasioCost = $this->rasioCostModel->getDataRasioCost($conditionProduction);
                    $valueSetting['nameCost'] = $nameRasioCost;
                    $valueSetting['rawCost'] = $rasioCost;
                }
            }
            $productionResultDataTitle = $this->productionResultModel->getDataProductionResultWithDetail($conditionProduction);
            // var_dump($settingCosting);
            // exit;

            $dataResult = [
                'settingCosting' => $settingCosting,
                'productionResultDataTitle' => $productionResultDataTitle,
            ];

            return response()->setJSON([
                'data' => $dataResult,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }
}
