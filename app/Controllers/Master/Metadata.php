<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\MetadataModel;

class Metadata extends BaseController
{
    protected $token;
    protected $MetadataModel;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->MetadataModel = new MetadataModel();
    }

    public function dropdownMetadata()
    {
        $name = $this->request->getGet("name");

        $values = [
            "name" => str_replace("_", " ", $name),
            "company_id"    => $this->this_company_id
        ];
        $dataMetadata = $this->MetadataModel->search_list($values, "value");

        //        $responseMetadata = curl_request("GET", "/metadata/all?name=$name", $this->token);

        $data = [
            "data" => $dataMetadata
        ];

        echo json_encode($data);
        return;
    }
}
