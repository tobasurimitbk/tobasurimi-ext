<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\KantorBeaCukaiModel;
use App\Models\SupplierModel;

class BeaCukaiController extends BaseController
{

    private $modelKantorBeaCuai, $modelSupplier, $this_company_id;

    public function __construct()
    {
        $this->modelKantorBeaCuai = new KantorBeaCukaiModel();
        $this->modelSupplier = new SupplierModel();
    }


    public function bc23View()
    {

        return \view('BeaCukai/bc-23/index');
    }

    public function bc23CreateFormView()
    {
        $data = [
            'kantorBeaCukai' => $this->modelKantorBeaCuai->asObject()->findAll(),
            'supplier' => $this->modelSupplier->asObject()->findAll()
        ];
        return \view('BeaCukai/bc-23/create', $data);
    }

    public function bc25View()
    {
        return \view('BeaCukai/bc-25/index');
    }

    public function bc261View()
    {
        return \view('BeaCukai/bc-261/index');
    }

    public function bc262View()
    {
        return \view('BeaCukai/bc-262/index');
    }

    public function bc27View()
    {
        return \view('BeaCukai/bc-27/index');
    }

    public function bc30View()
    {
        return \view('BeaCukai/bc-30/index');
    }

    public function bc40View()
    {
        return \view('BeaCukai/bc-40/index');
    }

    public function bc41View()
    {
        return \view('BeaCukai/bc-41/index');
    }
}
