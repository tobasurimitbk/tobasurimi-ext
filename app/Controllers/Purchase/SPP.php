<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\SppModel;
use App\Models\SppDetailModel;
use App\Models\MetadataModel;
use App\Models\DivisisModel;

use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;

use Dompdf\Dompdf;

class SPP extends BaseController
{
    protected $token;
    protected $role_id;
    protected $SppModel;
    protected $SppDetailModel;

    protected $MetadataModel;
    protected $DivisisModel;

    protected $RmImportPOModel;
    protected $RmImportPODetailModel;
    protected $AmPurchaseOrderModel;
    protected $AmPurchaseOrderDetailModel;
    protected $RmPurchaseOrderModel;
    protected $RmPurchaseOrderDetailModel;

    protected $this_company_id;

    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->role_id = session()->get("login")->this_role_id;
        $this->SppModel = new SppModel();
        $this->SppDetailModel = new SppDetailModel();

        $this->MetadataModel = new MetadataModel();
        $this->DivisisModel = new DivisisModel();

        $this->RmImportPOModel = new RMImportPOModel();
        $this->RmImportPODetailModel = new RMImportPODetailModel();
        $this->AmPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->AmPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->RmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();

        $this->this_company_id = session()->get("login")->this_company_id;

        $this->dompdf = new Dompdf();
    }

    public function spp()
    {
        return view('Purchase/spp/index');
    }

    public function createSPP()
    {
        //Get Order Type By Metadata
        $dataOrderType =  $this->MetadataModel->get_by_name("Tipe PO");

        //Get Divisi
        $dataDivisi = $this->DivisisModel->asObject()->findAll();

        $data = [
            "dataOrderType" => $dataOrderType,
            "dataDivisi" => $dataDivisi
        ];

        return view('Purchase/spp/form', $data);
    }

    public function getByIdSPP($id = null)
    {
        //Get Order Type By Metadata
        $dataOrderType =  $this->MetadataModel->get_by_name("Tipe PO");

        //Get Divisi
        $dataDivisi = $this->DivisisModel->asObject()->findAll();

        $data = [
            "dataOrderType" => $dataOrderType,
            "dataDivisi" => $dataDivisi
        ];

        if (!empty($id)) {
            $dataSPP = $this->SppModel->getSppById($id);
            $dataSppDetail = $this->SppDetailModel->getSppDetailById($id);
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
            "spp_type"         => $this->request->getGet("spp_type"),
            "sort"             => $this->request->getGet("sort"),
            "sortType"         => $this->request->getGet("sortType"),
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"          => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            "purchase_requests.company_id"        => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "spp_type"      => $this->request->getGet("spp_type"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $sppData = $this->SppModel->getSppList($condition, $addCondition, $limit, $offset);

        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($sppData['data'] as $data) {
            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => $data->id,
                "spp_type"      => $data->spp_type,
                "spp_no"        => $data->spp_no,
                "divisiName" => $data->divisiName,
                "spp_type"      => $data->spp_type,
                "total"         => number_format(formatter($data->total, "STR_TO_FLOAT"), 2, '.', ','),
                "request_date"  => date('d/m/Y', strtotime($data->request_date)),
                "is_posted"     => $data->is_posted,
                "itemCount"     => $data->itemCount,
                "createdAt"     => date('d/m/Y', strtotime($data->createdAt)),
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
                "divisi_id" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $insertData = [
                    "company_id" => formatter($this->this_company_id, "STR_TO_INT"),
                    "request_date" => $this->request->getPost("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("request_date")))) : "",
                    "spp_no" => $this->request->getPost("spp_no"),
                    "spp_type" => $this->request->getPost("spp_type"),
                    "divisi_id" => formatter($this->request->getPost("divisi_id"), "STR_TO_INT"),
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

                // $dataWarehouse = $this->WarehousesModel->find($insertData["warehouse_id"]);

                // if ($insertData["spp_no"] === "") {
                //     $insertData["spp_no"] = $SppModel->generateNoSpp($dataWarehouse["warehouse_name"]);
                // }


                $payload = json_encode($insertData);

                $insert = $this->SppModel->insert($insertData);
                foreach ($insertData["items"] as $value) {
                    $value->barang_id = $value->item_id;
                    $value->purchase_request_id = $insert;
                }

                $this->SppDetailModel->insertBatch($insertData["items"]);
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
                "divisi_id" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $insertData = [
                    "request_date" => $this->request->getPost("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("request_date")))) : "",
                    "spp_no" => $this->request->getPost("spp_no"),
                    "spp_type" => $this->request->getPost("spp_type"),
                    "divisi_id" => formatter($this->request->getPost("divisi_id"), "STR_TO_INT"),
                    "note" => $this->request->getPost("note"),
                    "is_posted" => false,
                    "items" =>  json_decode($this->request->getPost("items")),
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    $totalPrice += $value->qty * $value->price;
                };

                $insertData["total"] = $totalPrice;

                // $dataWarehouse = $this->WarehousesModel->find($insertData["warehouse_id"]);

                // if ($insertData["spp_no"] === "") {
                //     $insertData["spp_no"] = $SppModel->genereteNoSpp($dataWarehouse["warehouse_name"]);
                // }

                if ($insertData) {
                    $this->SppModel->update($id, $insertData);

                    foreach ($insertData["items"] as $value) {
                        // create and update spp
                        $value->barang_id = $value->item_id;
                        $value->purchase_request_id = $id;

                        if (!empty($value->isDeleted)) {
                            $this->SppDetailModel->where('id', $value->id)->delete();
                        }

                        $id_detail = $value->id ?? null;
                        $purchase_request_detail_id = "";

                        if($id_detail)
                        {
                            $dataDetail = [
                                "id" => $id_detail,
                                "purchase_request_id" => $this->request->getPost("id"),
                                "barang_id" => $value->item_id,
                                "spec" => $value->spec,
                                "qty" => $value->qty,
                                "unit" => $value->unit,
                                "price" => $value->price,
                                "note" => $value->note,
                            ];
    
                            $purchase_request_detail_id = $this->SppDetailModel->upsert($dataDetail);
                        }
                        else
                        {
                            $dataDetail = [
                                "purchase_request_id" => $this->request->getPost("id"),
                                "barang_id" => $value->item_id,
                                "spec" => $value->spec,
                                "qty" => $value->qty,
                                "unit" => $value->unit,
                                "price" => $value->price,
                                "note" => $value->note,
                            ];
    
                            $purchase_request_detail_id = $this->SppDetailModel->insert($dataDetail);
                        }

                        // update item po bb lokal
                        // if($this->request->getPost("spp_type") === "Bahan Baku Lokal")
                        // {
                        //     $responsePO = $this->RmPurchaseOrderModel->getByPurchaseRequestId($this->request->getPost("id"));

                        //     if($responsePO)
                        //     {
                        //         $responseDetailPO = $this->RmPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseRequestDetailId($value->id ?? null);
                                
                        //         // edit and delete po items
                        //         if($responseDetailPO)
                        //         {
                        //             // delete po
                        //             if (!empty($value->isDeleted)) {
                        //                 $this->RmPurchaseOrderDetailModel->where('id', $responseDetailPO->id)->delete();
                        //             }

                        //             // edit po
                        //             $dataDetail = [
                        //                 "id" => $responseDetailPO->id,
                        //                 "barang_id" => $value->item_id,
                        //                 "spec" => $value->spec,
                        //                 "note" => $value->note,
                        //                 "qty" => $value->qty,
                        //                 "remaining_qty" => $value->qty,
                        //                 "general_price" => $value->price,
                        //             ];
            
                        //             $this->RmPurchaseOrderDetailModel->upsert($dataDetail);
                        //         }
                        //         // create po items
                        //         else
                        //         {
                        //             $dataDetail = [
                        //                 "id" => null,
                        //                 "rm_purchase_order_id" =>  $responsePO->id,
                        //                 "purchase_request_detail_id" => $purchase_request_detail_id,
                        //                 "barang_id" => $value->item_id,
                        //                 "spec" => $value->spec,
                        //                 "note" => $value->note,
                        //                 "qty" => $value->qty,
                        //                 "remaining_qty" => $value->qty,
                        //                 "general_price" => $value->price,
                        //             ];
            
                        //             $this->RmPurchaseOrderDetailModel->upsert($dataDetail);
                        //         }
                        //     }
                        // }
                        // update item po bp lokal
                        if($this->request->getPost("spp_type") === "Lokal")
                        {
                            $responsePO = $this->AmPurchaseOrderModel->getByPurchaseRequestId($this->request->getPost("id"));

                            if($responsePO)
                            {
                                $responseDetailPO = $this->AmPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseRequestDetailId($value->id ?? null);

                                // edit and delete po items
                                if($responseDetailPO)
                                {
                                    // delete po
                                    if (!empty($value->isDeleted)) {
                                        $this->AmPurchaseOrderDetailModel->where('id', $responseDetailPO->id)->delete();
                                    }

                                    // edit po
                                    $dataDetail = [
                                        "id" => $responseDetailPO->id,
                                        "barang_id" => $value->item_id,
                                        "spec" => $value->spec,
                                        "note" => $value->note,
                                        "qty" => $value->qty,
                                        "remaining_qty" => $value->qty,
                                        "price" => $value->price,
                                        "unit" => $value->unit,
                                    ];
            
                                    $this->AmPurchaseOrderDetailModel->upsert($dataDetail);
                                }
                                // create po items
                                else
                                {
                                    $dataDetail = [
                                        "id" => null,
                                        "am_purchase_order_id" =>  $responsePO->id,
                                        "purchase_request_detail_id" => $purchase_request_detail_id,
                                        "barang_id" => $value->item_id,
                                        "spec" => $value->spec,
                                        "note" => $value->note,
                                        "qty" => $value->qty,
                                        "remaining_qty" => $value->qty,
                                        "price" => $value->price,
                                        "unit" => $value->unit,
                                    ];
            
                                    $this->AmPurchaseOrderDetailModel->upsert($dataDetail);
                                }
                            }
                        }
                        // update item po bb import
                        // if($this->request->getPost("spp_type") === "Bahan Baku Import")
                        // {
                        //     $responsePO = $this->RmImportPOModel->getByPurchaseRequestId($this->request->getPost("id"));

                        //     if($responsePO)
                        //     {
                        //         $responseDetailPO = $this->RmImportPODetailModel->getPurchaseOrderDetailByPurchaseRequestDetailId($value->id ?? null);

                        //         // edit and delete po items
                        //         if($responseDetailPO)
                        //         {
                        //             // delete po
                        //             if (!empty($value->isDeleted)) {
                        //                 $this->RmImportPODetailModel->where('id', $responseDetailPO->id)->delete();
                        //             }

                        //             // edit po
                        //             $dataDetail = [
                        //                 "id" => $responseDetailPO->id,
                        //                 "barang_id" => $value->item_id,
                        //                 "spec" => $value->spec,
                        //                 "note" => $value->note,
                        //                 "qty" => $value->qty,
                        //                 "remaining_qty" => $value->qty,
                        //                 "price" => $value->price,
                        //                 "unit" => $value->unit,
                        //             ];
            
                        //             $this->RmImportPODetailModel->upsert($dataDetail);
                        //         }
                        //         // create po items
                        //         else
                        //         {
                        //             $dataDetail = [
                        //                 "id" => null,
                        //                 "rm_import_po_id" =>  $responsePO->id,
                        //                 "purchase_request_detail_id" => $purchase_request_detail_id,
                        //                 "barang_id" => $value->item_id,
                        //                 "spec" => $value->spec,
                        //                 "note" => $value->note,
                        //                 "qty" => $value->qty,
                        //                 "remaining_qty" => $value->qty,
                        //                 "price" => $value->price,
                        //                 "unit" => $value->unit,
                        //             ];
            
                        //             $this->RmImportPODetailModel->upsert($dataDetail);
                        //         }
                        //     }
                        // }
                        // update item po bp import
                        if($this->request->getPost("spp_type") === "Import")
                        {
                            $responsePO = $this->AmPurchaseOrderModel->getByPurchaseRequestId($this->request->getPost("id"));

                            if($responsePO)
                            {
                                $responseDetailPO = $this->AmPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseRequestDetailId($value->id ?? null);

                                // edit and delete po items
                                if($responseDetailPO)
                                {
                                    // delete po
                                    if (!empty($value->isDeleted)) {
                                        $this->AmPurchaseOrderDetailModel->where('id', $responseDetailPO->id)->delete();
                                    }

                                    // edit po
                                    $dataDetail = [
                                        "id" => $responseDetailPO->id,
                                        "barang_id" => $value->item_id,
                                        "spec" => $value->spec,
                                        "note" => $value->note,
                                        "qty" => $value->qty,
                                        "remaining_qty" => $value->qty,
                                        "price" => $value->price,
                                        "unit" => $value->unit,
                                    ];
            
                                    $this->AmPurchaseOrderDetailModel->upsert($dataDetail);
                                }
                                // create po items
                                else
                                {
                                    $dataDetail = [
                                        "id" => null,
                                        "am_purchase_order_id" =>  $responsePO->id,
                                        "purchase_request_detail_id" => $purchase_request_detail_id,
                                        "barang_id" => $value->item_id,
                                        "spec" => $value->spec,
                                        "note" => $value->note,
                                        "qty" => $value->qty,
                                        "remaining_qty" => $value->qty,
                                        "price" => $value->price,
                                        "unit" => $value->unit,
                                    ];
            
                                    $this->AmPurchaseOrderDetailModel->upsert($dataDetail);
                                }
                            }
                        }
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
                        "status"    => false,
                        "message"   => 'Data Gagal Diubah',
                        "payload"   =>  json_encode($insertData),
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } 
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateStatusSPP()
    {
        try {
            $id = $this->request->getPost("id");

            $payload = [
                "is_posted" => "1"
            ];

            if (!empty($id)) {
                $this->SppModel->update($id, $payload);

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
        // try {
        //     $id = $this->request->getPost("id");

        //     $payload = json_encode([]);

        //     $response = curl_request("PATCH", "/purchaseRequest/approve/$id", $this->token, $payload);

        //     if ($response["code"] === 200) {
        //         $data = [
        //             "status"            => true,
        //             "message"   => "Approve Berhasil diubah",
        //             "payload"   => $payload,
        //             'token' => csrf_hash()
        //         ];
        //         echo json_encode($data);
        //     } else {
        //         $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
        //         $data = [
        //             "status"            => false,
        //             "message"    => $message,
        //             "payload"   => $payload,
        //             'token' => csrf_hash()
        //         ];
        //         echo json_encode($data);
        //     }
        // } catch (\Exception $e) {
        //     $data = [
        //         "status"            => false,
        //         "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
        //         'token' => csrf_hash()
        //     ];
        //     echo json_encode($data);
        // }
        // return;
    }

    public function generateSPP()
    {
        $divisi_name = $this->request->getGet("divisi_name");
        $response = $this->SppModel->generateNoSpp($divisi_name);
        if ($response) {
            $data = [
                "status"  => true,
                "data"  => $response,
            ];
            echo json_encode($data);
        } else {
            $message = 'Gagal Auto Generate';
            $data = [
                "status" => false,
                "message"  => $message
            ];
            echo json_encode($data);
        }

        return;
    }

    public function deleteSPP()
    {
        try {
            $id = $this->request->getPost("id");
            $tipe = $this->request->getPost("tipe");

            if (empty($id)) {
                $data = [
                    "status"     => false,
                    "message"    => "Data Gagal Dihapus",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $this->SppModel->delete($id);
            $deleteSPP = $this->SppDetailModel->where('purchase_request_id', $id)->delete();

            if(!$deleteSPP)
            {
                $data = [
                    "status"     => false,
                    "message"    => "Data Gagal Dihapus",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            // if($tipe === "Bahan Baku Lokal")
            // {
            //     $responsePO = $this->RmPurchaseOrderModel->getByPurchaseRequestId($id);

            //     if($responsePO)
            //     {
            //         $deletePO = $this->RmPurchaseOrderModel->where('id', $responsePO->id)->delete();

            //         if(!$deletePO)
            //         {
            //             $data = [
            //                 "status"     => false,
            //                 "message"    => "Data PO Gagal Dihapus",
            //                 'token'      => csrf_hash()
            //             ];
            //             echo json_encode($data);
            //             return;
            //         }
            //     }
            // }
            if($tipe === "Lokal")
            {
                $responsePO = $this->AmPurchaseOrderModel->getByPurchaseRequestId($id);

                if($responsePO)
                {
                    $deletePO = $this->AmPurchaseOrderModel->where('id', $responsePO->id)->delete();

                    if(!$deletePO)
                    {
                        $data = [
                            "status"     => false,
                            "message"    => "Data PO Gagal Dihapus",
                            'token'      => csrf_hash()
                        ];
                        echo json_encode($data);
                        return;
                    }    
                }
            }
            // if($tipe === "Bahan Baku Import")
            // {
            //     $responsePO = $this->RmImportPOModel->getByPurchaseRequestId($id);

            //     if($responsePO)
            //     {
            //         $deletePO = $this->RmImportPOModel->where('id', $responsePO->id)->delete();

            //         if(!$deletePO)
            //         {
            //             $data = [
            //                 "status"     => false,
            //                 "message"    => "Data PO Gagal Dihapus",
            //                 'token'      => csrf_hash()
            //             ];
            //             echo json_encode($data);
            //             return;
            //         }
            //     }
            // }
            if($tipe === "Import")
            {
                $responsePO = $this->AmPurchaseOrderModel->getByPurchaseRequestId($id);

                if($responsePO)
                {
                    $deletePO = $this->AmPurchaseOrderModel->where('id', $responsePO->id)->delete();

                    if(!$deletePO)
                    {
                        $data = [
                            "status"     => false,
                            "message"    => "Data PO Gagal Dihapus",
                            'token'      => csrf_hash()
                        ];
                        echo json_encode($data);
                        return;
                    }
                }
            }

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

    public function printTable()
    {
        $filename = "Data SPP";

        $payload = [
            "search"           => $this->request->getGet("search"),
            "spp_type"         => $this->request->getGet("spp_type"),
            "sort"             => $this->request->getGet("sort"),
            "sortType"         => $this->request->getGet("sortType"),
            "dateStart"        => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"          => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "spp_type"      => $this->request->getGet("spp_type"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $sppData = $this->SppModel->getSppList($condition, $addCondition, 100000000, 0);

        $dataSPP = [];

        $no = 1;

        foreach ($sppData['data'] as $data) {
            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => $data->id,
                "spp_type"      => $data->spp_type,
                "spp_no"        => $data->spp_no,
                "divisiName"    => $data->divisiName,
                "total"         => number_format(formatter($data->total, "STR_TO_FLOAT"), 2, '.', ','),
                "request_date"  => date('d/m/Y', strtotime($data->request_date)),
                "is_posted"     => $data->is_posted,
                "createdAt"     => date('d/m/Y', strtotime($data->createdAt)),
            ]);
        }

        $data = [
            "dataSPP"  => $dataSPP,
        ];

        // load HTML content
        $this->dompdf->loadHtml(view('Purchase/spp/print-table', $data));

        // (optional) setup the paper size and orientation
        $this->dompdf->setPaper('A4', 'landscape');

        // render html as PDF
        $this->dompdf->render();

        // output the generated pdf
        $this->dompdf->stream($filename, array("Attachment" => false));

        exit(0);

        // return view('Purchase/poImportBahanPenolong/print', $data);
    }

    public function print($id = null)
    {
        if ($id) {
            $filename = "SPP";

            if (!empty($id)) {
                $dataSPP = $this->SppModel->getSppById($id);
                $dataSppDetail = $this->SppDetailModel->getSppDetailById($id);

                $no = 0;
                $totalPrice = 0;
                $totalQty = 0;
                $totalAll = 0;

                foreach ($dataSppDetail as $value) {
                    $no++;
                    $value->no = $no;
                    $totalPrice += formatter($value->price, "CURR_TO_INT");
                    $totalQty += formatter($value->qty, "STR_TO_FLOAT");
                    $totalAll += formatter($value->totalPrice, "CURR_TO_INT");
                }
                $dataSPP->totalPrice = number_format($totalPrice);
                $dataSPP->totalQty = number_format($totalQty);
                $dataSPP->totalAll = number_format($totalAll);

                $data["dataSPP"] = $dataSPP;
                $data["dataSPP"]->purchase_request_details = $dataSppDetail;
            }

            // dd($data);

            // load HTML content
            $this->dompdf->loadHtml(view('Purchase/spp/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'landscape');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Purchase/poImportBahanPenolong/print', $data);
        }
    }
}
