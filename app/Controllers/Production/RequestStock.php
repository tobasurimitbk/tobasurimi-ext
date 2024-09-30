<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MaterialRequestsModel;
use App\Models\MaterialRequestsPenolongModel;

class RequestStock extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $satuanModel;
    protected $materialRequestModel;
    protected $materialRequestPenolongModel;
    protected $materialRequestDetailsModel;
    protected $this_user_id;


    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->materialRequestModel = new MaterialRequestsModel();
        $this->materialRequestPenolongModel = new MaterialRequestsPenolongModel();
        $this->materialRequestDetailsModel = new MaterialRequestDetailsModel();
    }

    public function index()
    {
        return view('Production/requestStock/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "material_type" => $this->request->getGet("material_type")
        ];
    
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
    
        // Buat condition berdasarkan material_type
        if ($payload["material_type"] === "material_request") {
            $condition = [
                'material_requests.company_id' => $this->this_company_id,
                'material_requests.is_posted' => 1
            ];
        } elseif ($payload["material_type"] === "material_kimia") {
            $condition = [
                'material_requests_penolong.company_id' => $this->this_company_id,
                'parent_barang.parent_name' => "KIMIA",
                'material_requests_penolong.is_posted' => 1
            ];
        } elseif ($payload["material_type"] === "material_penolong") {
            $condition = [
                'material_requests_penolong.company_id' => $this->this_company_id,
                'parent_barang.parent_name' => "",
                'material_requests_penolong.is_posted' => 1
            ];
        } else {
            // Default jika material_type tidak sesuai
            $condition = [
                'material_requests.company_id' => $this->this_company_id,
                'material_requests.is_posted' => 1
            ];
        }
    
        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];
    
        // Ambil data sesuai kondisi yang telah dibuat
        if ($payload["material_type"] === "material_request" ||  empty($payload["material_type"])) {
            $materialRequestData = $this->materialRequestModel->getMaterialRequestList($condition, $addCondition, $limit, $offset);
        } elseif ($payload["material_type"] === "material_kimia") {
            $materialRequestData = $this->materialRequestPenolongModel->getMaterialRequestList($condition, $addCondition, $limit, $offset);
        } elseif ($payload["material_type"] === "material_penolong") {
            $materialRequestData = $this->materialRequestPenolongModel->getMaterialRequestList($condition, $addCondition, $limit, $offset);
        }

        
        $dataMaterialRequest = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
    
        foreach ($materialRequestData['data'] as $data) {
            array_push($dataMaterialRequest, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "req_no" => $data->req_no,
                "nama_barang" => $data->nama_barang,
                "wo_no" => $data->wo_no,
                "is_posted" => $data->is_posted,
                "is_approve" => $data->is_approve,
                "request_status" => $data->request_status,
            ]);
        }
    
        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $materialRequestData['totalData'],
            "recordsFiltered" => $materialRequestData['totalFilteredData'],
            "data" => $dataMaterialRequest,
            "payload" => $payload
        ];
    
        echo json_encode($data);
        return;
    }
    


    public function approve()
    {
        try {

            $id = $this->request->getVar('id');
            $id = decrypt($id);

            $data = [
                'is_approve' => $this->request->getVar('status_approve'),
            ];
            $result = $this->materialRequestModel->update($id, $data);

            echo json_encode($result);

        } catch (Exception $e) {
            $data = [
                "status"     => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }

        return;
    }

    public function approvePenolong()
    {
        try {

            $id = $this->request->getVar('id');
            $id = decrypt($id);

            $data = [
                'is_approve' => $this->request->getVar('status_approve'),
            ];
            $result = $this->materialRequestPenolongModel->update($id, $data);
               
            echo json_encode($result);

        } catch (Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }

        return;
    }

}
