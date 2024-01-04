<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\cities;
use App\Models\CitiesModel;

class City extends BaseController
{
    protected $token;
    protected $CitiesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->CitiesModel = new CitiesModel();
    }

    public function getCityByProvince($id = null)
    {
        $dataCity = [];

        if (is_numeric($id)) {
            $id = $id;
        } else {
            $id = decrypt($id);
        }

        $res = $this->CitiesModel->get_by_province_id($id);

        $data = [
            "data" => $res
        ];

        echo json_encode($data);
        return;
    }
}
