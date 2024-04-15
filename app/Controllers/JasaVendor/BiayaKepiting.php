<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BiayaKepitingDetailModel;
use App\Models\BiayaKepitingModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\JasaVendorInModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class BiayaKepiting extends BaseController
{

    protected $this_company_id;
    protected $this_user_id;
    protected $divisiModel;
    protected $warehouseModel;
    protected $biayaKepitingModel;
    protected $biayaKepitingDetailModel;
    protected $jasaVendorInModel;
    protected $jasaVendorInDetailModel;
    protected $vendorModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->warehouseModel = new WarehousesModel();
        $this->biayaKepitingModel = new BiayaKepitingModel();
        $this->biayaKepitingDetailModel = new BiayaKepitingDetailModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $this->vendorModel = new VendorModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('jasaVendor/biayaKepiting/index', $data);
    }

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
        ];

        return view('jasaVendor/biayaKepiting/form', $data);
    }
}
