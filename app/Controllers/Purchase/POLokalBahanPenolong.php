<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\SppModel;
use App\Models\MetadataModel;
use App\Models\WarehousesModel;

class POLokalBahanPenolong extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $AMPurchaseOrderModel;
    protected $AMPurchaseOrderDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->AMPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->AMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
    }

    public function poLokalBahanPenolong()
    {
        return view('Purchase/poLokalBahanPenolong/index');
    }

    public function createPOLokalBahanPenolong()
    {
        //Get SPP Number
        $SppModel = new SppModel();
        $dataSPP = $SppModel->getNoSPP('Bahan Penolong Lokal');

        foreach (array_keys($dataSPP) as $key) {
            $dataSPP[$key] = (object)$dataSPP[$key];
        }

        //Get Supplier
        $supplierModel = new SupplierModel();
        $dataSupplier = $supplierModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN BAKU', $this->this_company_id);

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        //Get Valuta Asing By Metadata
        $MetadataModel = new MetadataModel();
        $dataValuta = $MetadataModel->get_by_name('Valuta Asing');

        foreach (array_keys($dataValuta) as $key) {
            $dataValuta[$key] = (object)$dataValuta[$key];
        }

        $data = [
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        return view('Purchase/poLokalBahanPenolong/form', $data);
    }

    public function getByIdPOLokalBahanPenolong($id = null)
    {
        //Get SPP Number
        $SppModel = new SppModel();
        $dataSPP = $SppModel->getNoSPP('Bahan Penolong Lokal');

        foreach (array_keys($dataSPP) as $key) {
            $dataSPP[$key] = (object)$dataSPP[$key];
        }

        //Get Supplier
        $supplierModel = new SupplierModel();
        $dataSupplier = $supplierModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN BAKU', $this->this_company_id);

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        //Get Valuta Asing By Metadata
        $MetadataModel = new MetadataModel();
        $dataValuta = $MetadataModel->get_by_name('Valuta Asing');

        foreach (array_keys($dataValuta) as $key) {
            $dataValuta[$key] = (object)$dataValuta[$key];
        }

        $data = [
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        $AMPurchaseOrderModel = new AMPurchaseOrderModel();
        $AMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();


        if (!empty($id)) {
            $dataBPLokal = $AMPurchaseOrderModel->getPOById($id);
            $dataBPLokalDetail = $AMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);
            foreach (array_keys($dataBPLokalDetail) as $key) {
                $dataBPLokalDetail[$key] = (object)$dataBPLokalDetail[$key];
            }
            $data["dataPOLokal"] = $dataBPLokal;
            $data["dataPOLokal"]->am_purchase_order_details = $dataBPLokalDetail;
        }

        return view('Purchase/poLokalBahanPenolong/form', $data);

        return;
    }

    public function getByIdPOLokalBahanPenolongAjax()
    {
        $id = $this->request->getGet("id");

        if (!empty($id)) {
            $response = curl_request("GET", "/auxiliaryMaterialPO/lokal/$id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                    "message" => $response
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditemukan';
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

    public function allPOLokalBahanPenolong()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $BPLokalModel = new AMPurchaseOrderModel();

        $condition = [
            'po_type' => "Lokal"
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $poData = $BPLokalModel->getPoList($condition, $addCondition, $limit, $offset);

        $dataPOLokal = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poData['data'] as $data) {
            array_push($dataPOLokal, [
                "no" => $no++,
                "id" => $data->id,
                "po_date" => $data->po_date,
                "po_no" => $data->po_no,
                "supplierName" => $data->supplierName,
                "total" => $data->total,
                "currency" => $data->currency,
            ]);
        }


        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $poData['totalData'],
            "recordsFiltered"   => $poData['totalFilteredData'],
            "data"              => $dataPOLokal,
            "payload"           => $payload
        ];


        echo json_encode($data);
        return;
    }

    public function savePOLokalBahanPenolong()
    {
        try {
            $AMPurchaseOrderModel = new AMPurchaseOrderModel();
            $AMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();

            $rules = [
                "purchase_request_id" => [
                    "rules" => "required"
                ],
                "po_no" => [
                    "rules" => "required"
                ],
                "po_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "payment_term" => [
                    "rules" => "required"
                ],
                "currency" => [
                    "rules" => "required"
                ],
                "payment_date" => [
                    "rules" => "required"
                ],
                "dpp" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $insertData = [
                    "company_id"            => $this->this_company_id,
                    "purchase_request_id"   => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                    "po_no"                 => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                    "po_date"               => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "warehouse_id"          => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                    "po_type"               => 'Lokal',
                    "supplier_id"           => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "payment_term"          => $this->request->getPost("payment_term") ? formatter($this->request->getPost("payment_term"), "STR_TO_INT") : 0,
                    "currency"              => formatter($this->request->getPost("currency"), "STR_TO_INT"),
                    "payment_date"          => $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))) : "",
                    "dpp"                   => formatter($this->request->getPost("dpp"), "CURR_TO_INT"),
                    "note"                  => $this->request->getPost("note"),
                    "isPosted"              => false,
                    "createdBy"             => session()->get("login")->user_id,
                    "items"                 => json_decode($this->request->getPost("items"))
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    $totalPrice += $value->qty * $value->price;
                };

                $insertData["total"] = $totalPrice;

                if ($insertData["po_no"] === "") {
                    $WarehousesModel = new WarehousesModel();
                    $dataWarehouse = $WarehousesModel->find($insertData["warehouse_id"]);
                    $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                    $insertData["po_no"] = $AMPurchaseOrderModel->get_no(date('d'), date('m'), date('Y'), $dataWarehouse["warehouse_name"], date('y'), $insertData["warehouse_id"], $last_day);
                };

                $insert = $AMPurchaseOrderModel->insert($insertData);

                foreach ($insertData["items"] as $value) {
                    $value->barang_id = $value->item_id;
                    $value->am_purchase_order_id = $insert;
                }

                $AMPurchaseOrderDetailModel->insertBatch($insertData["items"]);

                $payload = json_encode($insertData);

                if ($insert) {
                    $data = [
                        "id"        => $insert,
                        "status"    => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"    => false,
                        "message"   => "Data Gagal Disimpan",
                        "payload"   => $payload,
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Disimpan",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
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

    public function updatePOLokalBahanPenolong()
    {
        try {
            $rules = [
                "po_no" => [
                    "rules" => "required"
                ],
                "po_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "payment_term" => [
                    "rules" => "required"
                ],
                "currency" => [
                    "rules" => "required"
                ],
                "payment_date" => [
                    "rules" => "required"
                ],
                "dpp" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = json_encode([
                    "po_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "payment_term" => $this->request->getPost("payment_term") ? formatter($this->request->getPost("payment_term"), "STR_TO_INT") : 0,
                    "currency" => formatter($this->request->getPost("currency"), "STR_TO_INT"),
                    "payment_date" => $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))) : "",
                    "dpp" => formatter($this->request->getPost("dpp"), "CURR_TO_INT"),
                    "note" => $this->request->getPost("note"),
                    "items" =>  json_decode($this->request->getPost("items"))
                ]);

                $response = curl_request("PATCH", "/auxiliaryMaterialPO/lokal/$id", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
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

    public function updateStatusPOLokalBahanPenolong()
    {
        try {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "is_posted" => true
            ]);

            $response = curl_request("PATCH", "/auxiliaryMaterialPO/lokal/$id", $this->token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diposting",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diposting';
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

    public function deletePOLokalBahanPenolong()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/auxiliaryMaterialPO/lokal/$id", $this->token);
                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Dihapus';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Dihapus",
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

    public function dropdownPOLokalBahanPenolong()
    {
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");

        $dataPOLokal = $this->AMPurchaseOrderModel->getNoPenerimaanBarang("LOKAL", $id, $this->this_company_id);

        $data = [
            "data" => $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOLokalBahanPenolong()
    {
        $id = $this->request->getGet("id");

        $dataPOLokal = $this->AMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }
}
