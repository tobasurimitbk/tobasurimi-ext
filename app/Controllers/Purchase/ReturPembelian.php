<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\ReturAmPoModel;
use App\Models\SupplierModel;
use CodeIgniter\I18n\Time;

class ReturPembelian extends BaseController
{
    protected $token;
    protected $user_id;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        return view('Purchase/returPembelian/index');
    }

    public function create()
    {
        $supplierModel = new SupplierModel();
        $data = [
            'supplier' => $supplierModel->getSupplierByType("BAHAN PENOLONG")
        ];
        return view('Purchase/returPembelian/form', $data);
    }

    public function generateNo()
    {
        $returAmPoModel = new ReturAmPoModel();
        return response()->setJSON([
            'data' => $returAmPoModel->generateNoRetur()
        ]);
    }
}
