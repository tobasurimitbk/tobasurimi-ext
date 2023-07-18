<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\SppModel;
use App\Models\SppDetailModel;
use App\Models\MetadataModel;
use App\Models\WarehousesModel;

class SPP extends BaseController
{
    protected $token;
    protected $role_id;
    protected $SppModel;
    protected $SppDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->role_id = session()->get("login")->this_role_id;
        $this->SppModel = new SppModel();
        $this->SppDetailModel = new SppDetailModel();
    }

    public function spp()
    {
        return view('Purchase/spp/index');
    }

    public function createSPP()
    {
        //Get Order Type By Metadata
        $MetadataModel = new MetadataModel();
        $dataOrderType =  $MetadataModel->get_by_name("Tipe PO");

        //Get Warehouse
        $WarehousesModel = new WarehousesModel();
        $dataWarehouse = $WarehousesModel->asObject()->findAll();

        $data = [
            "dataOrderType" => $dataOrderType,
            "dataWarehouse" => $dataWarehouse
        ];

        return view('Purchase/spp/form', $data);
    }

    public function getByIdSPP($id = null)
    {
        //Get Order Type By Metadata
        $MetadataModel = new MetadataModel();
        $dataOrderType =  $MetadataModel->get_by_name("Tipe PO");

        //Get Warehouse
        $WarehousesModel = new WarehousesModel();
        $dataWarehouse = $WarehousesModel->asObject()->findAll();

        $data = [
            "dataOrderType" => $dataOrderType,
            "dataWarehouse" => $dataWarehouse
        ];

        $SppModel = new SppModel();
        $SppDetailModel = new SppDetailModel();

        if (!empty($id)) {
            $dataSPP = $SppModel->getSppById($id);
            $dataSppDetail = $SppDetailModel->getSppDetailById($id);
            $data["dataSPP"] = $dataSPP;
            $data["dataSPP"]->purchase_request_details = $dataSppDetail;
        }

        return view('Purchase/spp/form', $data);
    }

    public function getByIdSPPAjax()
    {
        $id = $this->request->getGet("id");

        if (!empty($id)) {
            $response = $this->SppModel->getSppById($id);
            $responseDetail = $this->SppDetailModel->getSppDetailById($id);
            if ($response) {
                $data = [
                    "status"  => true,
                    "data"  => $response,
                    "detail" => $responseDetail
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditemukan';
                $data = [
                    "status" => false,
                    "message"  => $message
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Tidak Ada Id"
            ];
            echo json_encode($data);
        }

        return;
    }

    public function allSPP()
    {
        $payload = [
            "pageSize"         => $this->request->getGet("length"),
            "currentPage"      => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"           => $this->request->getGet("search"),
            "sort"             => $this->request->getGet("sort"),
            "sortType"         => $this->request->getGet("sortType"),
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"          => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $SppModel = new SppModel();

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $sppData = $SppModel->getSppList($condition, $addCondition, $limit, $offset);

        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($sppData['data'] as $data) {
            array_push($dataSPP, [
                "no" => $no++,
                "id" => $data->id,
                "spp_type" => $data->spp_type,
                "spp_no" => $data->spp_no,
                "warehouseName" => $data->warehouseName,
                "total" => $data->total,
                "request_date" => $data->request_date,
                "approvedByHeadwarehouseName" => $data->approvedByHeadwarehouseName ?? "-",
                "approvedByHeadofPurchasingName" => $data->approvedByHeadofPurchasingName,
                "approvedByDirectorName" => $data->approvedByDirectorName,
                "is_posted" => $data->is_posted,
                "createdAt" => $data->createdAt,
                "isApproveWarehouse" => ($this->role_id === '22' || $this->role_id === 22) ? ($data->is_posted === false && $data->approvedByHeadwarehouseName === "false" ? true : false) : false,
                "isApprovePurchasing" => ($this->role_id === '23' || $this->role_id === 23) ? ($data->is_posted === false && $data->approvedByHeadofPurchasingName === "false" ? true : false) : false,
                "isApproveDirector" => ($this->role_id === '21' || $this->role_id === 21) ? ($data->is_posted === false && $data->approvedByDirectorName === "false" ? true : false) : false
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $sppData['totalData'],
            "recordsFiltered"   => $sppData['totalFilteredData'],
            "data"              => $dataSPP,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSPP()
    {
        try {
            $SppModel = new SppModel();
            $SppDetailModel = new SppDetailModel();

            $rules = [
                "request_date" => [
                    "rules" => "required"
                ],
                "spp_type" => [
                    "rules" => "required"
                ],
                "spp_no" => [
                    "rules" => "required"
                ],
                "warehouse_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $insertData = [
                    "request_date" => $this->request->getPost("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("request_date")))) : "",
                    "spp_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("spp_no"),
                    "spp_type" => $this->request->getPost("spp_type"),
                    "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                    "note" => $this->request->getPost("note"),
                    "is_posted" => false,
                    "createdBy" => session()->get("login")->user_id,
                    "items" =>  json_decode($this->request->getPost("items")),
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    $totalPrice += $value->qty * $value->price;
                };

                $insertData["total"] = $totalPrice;


                $payload = json_encode($insertData);

                $insert = $SppModel->insert($insertData);
                foreach ($insertData["items"] as $value) {
                    $value->barang_id = $value->item_id;
                    $value->purchase_request_id = $insert;
                }
                $SppDetailModel->insertBatch($insertData["items"]);
                // $response = curl_request("POST", "/purchaseRequest", $this->token, $payload);

                if ($insert) {
                    $data = [
                        "id" => $insert,
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Data Gagal Disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Disimpan",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateSPP()
    {
        try {
            $SppModel = new SppModel();
            $SppDetailModel = new SppDetailModel();

            $rules = [
                "request_date" => [
                    "rules" => "required"
                ],
                "spp_type" => [
                    "rules" => "required"
                ],
                "spp_no" => [
                    "rules" => "required"
                ],
                "warehouse_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $insertData = [
                    "request_date" => $this->request->getPost("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("request_date")))) : "",
                    "spp_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("spp_no"),
                    "spp_type" => $this->request->getPost("spp_type"),
                    "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                    "note" => $this->request->getPost("note"),
                    "is_posted" => false,
                    "createdBy" => session()->get("login")->user_id,
                    "items" =>  json_decode($this->request->getPost("items")),
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    $totalPrice += $value->qty * $value->price;
                };

                $insertData["total"] = $totalPrice;


                // $response = curl_request("PATCH", "/purchaseRequest/$id", $this->token, $payload);

                if ($insertData) {
                    $SppModel->update($id, $insertData);

                    foreach ($insertData["items"] as $value) {
                        $value->barang_id = $value->item_id;
                        $value->purchase_request_id = $id;

                        $dataDetail = [
                            "id" => $value->id ?? null,
                            "purchase_request_id" => $this->request->getPost("id"),
                            "barang_id" => $value->item_id,
                            "spec" => $value->spec,
                            "qty" => $value->qty,
                            "unit" => $value->unit,
                            "price" => $value->price,
                            "note" => $value->note,
                        ];

                        // $sql = $SppDetailModel->setData($value)->getCompiledUpsert();
                        // echo $sql;
                        $SppDetailModel->upsert($dataDetail);
                    }

                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   =>  json_encode($insertData),
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => 'Data Gagal Diubah',
                        "payload"   =>  json_encode($insertData),
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Diubah",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateStatusSPP()
    {
        try {
            $id = $this->request->getPost("id");
            $SppModel = new SppModel();

            $payload = [
                "is_posted" => "1"
            ];

            if (!empty($id)) {
                $SppModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "message"   => "Data Berhasil diposting",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Disimpan",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
    }

    public function approveSPP()
    {
        try {
            $id = $this->request->getPost("id");

            $payload = json_encode([]);

            $response = curl_request("PATCH", "/purchaseRequest/approve/$id", $this->token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Approve Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteSPP()
    {
        try {
            $SppModel = new SppModel();
            $SppDetailModel = new SppDetailModel();
            $id = $this->request->getPost("id");

            if (empty($id)) {
                $data = [
                    "status"     => false,
                    "message"    => "Data Gagal Dihapus",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $SppModel->delete($id);
            $SppDetailModel->where('purchase_request_id', $id)->delete();

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
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
