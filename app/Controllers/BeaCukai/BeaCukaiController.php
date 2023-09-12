<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\CountryModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\SupplierModel;
use App\Models\MetadataModel;

class BeaCukaiController extends BaseController
{

    private $modelKantorBeaCukai, $modelSupplier, $modelCountry, $modelMetadata, $this_company_id;

    public function __construct()
    {
        $this->modelKantorBeaCukai = new KantorBeaCukaiModel();
        $this->modelSupplier = new SupplierModel();
        $this->modelCountry = new CountryModel();
        $this->modelMetadata = new MetadataModel();
    }


    public function bc23View()
    {

        return view('BeaCukai/bc-23/index');
    }

    public function bc23CreateFormView()
    {
        //Get Jenis TPB
        $jenisTPB = $this->modelMetadata->get_by_name('jenis_tpb');

        //Get Pengangkutan
        $pengangkutan = $this->modelMetadata->get_by_name('pengangkutan');

        $data = [
            'jenisTPB' => $jenisTPB,
            'pengangkutan' => $pengangkutan,
            'kantorBeaCukai' => $this->modelKantorBeaCukai->asObject()->findAll(),
            'supplier' => $this->modelSupplier->asObject()->findAll(),
            'country' => $this->modelCountry->asObject()->findAll()
        ];
        return view('BeaCukai/bc-23/create', $data);
    }

    public function bc25View()
    {
        return view('BeaCukai/bc-25/index');
    }

    public function bc261View()
    {
        return view('BeaCukai/bc-261/index');
    }

    public function bc262View()
    {
        return view('BeaCukai/bc-262/index');
    }

    public function bc27View()
    {
        return view('BeaCukai/bc-27/index');
    }

    public function bc30View()
    {
        return view('BeaCukai/bc-30/index');
    }

    public function bc40View()
    {
        return view('BeaCukai/bc-40/index');
    }

    public function bc41View()
    {
        return view('BeaCukai/bc-41/index');
    }
}
