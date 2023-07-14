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

    public function dropdownMetadata1()
    {
        $name = $this->request->getGet("name");
        $search = $this->request->getGet('search');
        $page = $this->request->getGet('page') ?? 1;
        $limit = 10;
        $offset = ($page - 1) * 10;

        $dataQry = $this->MetadataModel;

        if (!empty($search)) {
            $dataQry->like('value', $search);
        }

        $totalData = $dataQry->countAllResults(false);
        $subAccData = $dataQry->select('id, value AS text')
            ->where('name', $name)
            ->orderBy('value', 'asc')
            ->findAll($limit, $offset);

        $data = [
            "results"   => $subAccData,
            "pagination" => [
                "more"  => $offset < $totalData
            ]
        ];

        echo json_encode($data);
        return;
    }
}
