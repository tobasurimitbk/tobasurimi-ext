<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\PanjarSupplierModel;
use App\Models\SupplierModel;
use App\Models\LocalPOPaymentPanjarModel;



class PanjarTbSupplier extends BaseController
{

    protected $token;
    protected $this_company_id;

    protected $panjarSupplierModel;

    protected $supplierModel;

    protected $localPOPaymentPanjarModel;
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->panjarSupplierModel = new PanjarSupplierModel();
        $this->supplierModel = new SupplierModel();
        $this->localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
    }

    public function index()
    {

        $data = [
            // 'noPanjar' => $this->panjarSupplierModel->getNumber($this->this_company_id)
        ];

        return view('Pembayaran/pembayaranPanjarSupplier/index', $data);
    }

}
