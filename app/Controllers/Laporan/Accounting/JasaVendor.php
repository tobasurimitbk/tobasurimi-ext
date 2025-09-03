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
use App\Models\JasaVendorInModel;
use App\Models\JasaVendorOutModel;
use App\Models\ProsesRebusModel;
use App\Models\BarangMasterModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class JasaVendor extends BaseController
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
    protected $jasaVendorInModel;
    protected $jasaVendorOutModel;
    protected $prosesRebusModel;
    protected $barangModel;

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
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->jasaVendorOutModel = new JasaVendorOutModel();
        $this->prosesRebusModel = new ProsesRebusModel();
        $this->barangModel = new BarangMasterModel();
    }

    public function index()
    {
        $data = [
            'dataBarang' => $this->barangModel->getListBarangmaster("BAHAN_BAKU", $this->this_company_id),
        ];
        return view('Laporan/LaporanJasaVendor/index', $data);
    }

    public function getVendorData()
    {
        $rawFilter = $this->request->getGet("filter") == "all" ? "" : $this->request->getGet("filter");

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        } else {
            $filter = "";
        }
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "divisi"        => $this->request->getGet("filter_divisi") == "all" ? "" : $this->request->getGet("filter_divisi"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            // "deletedAt" => NULL,
            // "companyId" => $this->this_company_id,
            // "master_barang.id" => $this->request->getGet("list_barang"),
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "barang"        => $this->request->getGet("list_barang") == "all" ? "" : $this->request->getGet("list_barang"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        if ($this->this_company_id != "16" && $this->this_company_id != "15") {
            $addCondition['companyId'] = [1, 2];
        } else if ($this->this_company_id == "15") {
            $addCondition['companyId'] = [15];
        } else if ($this->this_company_id == "16") {
            $addCondition['companyId'] = [16];
        } else {
            $addCondition['companyId'] = [];
        }

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->prosesRebusModel->getListReport($condition, $addCondition, $limit, $offset);
        var_dump($res);
        die;

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $totalRemaining = $data->total - $data->remaining;
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "tanggal_invoice"       => $data->tanggal_invoice,
                "no_invoice"            => $data->no_invoice,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "divisi_invoice"        => $data->divisi,
                "nominal_invoice"       => number_format($data->total, 2, '.', ''),
                "remaining_invoice"     => number_format($totalRemaining, 2, '.', ''),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }
}
