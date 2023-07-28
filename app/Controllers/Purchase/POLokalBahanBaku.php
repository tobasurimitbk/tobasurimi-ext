<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\SppModel;

class POLokalBahanBaku extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $RMPurchaseOrderModel;
    protected $RMPurchaseOrderDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
    }

    public function poLokalBahanBaku()
    {
        return view('Purchase/poLokalBahanBaku/index');
    }

    public function createPOLokalBahanBaku()
    {
        //Get SPP Number
        $SppModel = new SppModel();
        $dataSPP = $SppModel->getNoSPP('Bahan Baku Lokal');

        foreach (array_keys($dataSPP) as $key) {
            $dataSPP[$key] = (object)$dataSPP[$key];
        }

        //Get Supplier
        $supplierModel = new SupplierModel();
        $dataSupplier = $supplierModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN BAKU', $this->this_company_id);

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        $data = [
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier
        ];

        dd($data);

        return view('Purchase/poLokalBahanBaku/form', $data);
    }

    public function getByIdPOLokalBahanBaku($id = null)
    {
        //Get Supplier
        $supplierModel = new SupplierModel();
        $dataSupplier = $supplierModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN BAKU', $this->this_company_id);

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        $data = [
            "dataSupplier" => $dataSupplier
        ];

        $RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();

        if (!empty($id)) {
            $dataBBLokal = $RMPurchaseOrderModel->getPoBBLokalById($id);
            $dataBBLokalDetail = $RMPurchaseOrderDetailModel->getPoBBLokalDetailById($id);
            $data["dataPOLokal"] = $dataBBLokal;
            $data["dataPOLokal"]->rm_purchase_order_details = $dataBBLokalDetail;
        }

        return view('Purchase/poLokalBahanBaku/form', $data);
        return;
    }

    public function getByIdPOLokalBahanBakuAjax()
    {
        $id = $this->request->getGet("id");

        if (!empty($id)) {
            $response = curl_request("GET", "/purchaseOrder/$id", $this->token);
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

    public function allPOLokalBahanBaku()
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

        $BBLokalModel = new RMPurchaseOrderModel();

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
        $poData = $BBLokalModel->getPoBBList($condition, $addCondition, $limit, $offset);

        $dataPOLokal = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poData['data'] as $data) {
            array_push($dataPOLokal, [
                "no"            => $no++,
                "id"            => $data->id,
                "po_date"       => date('Y-m-d', strtotime($data->po_date)),
                "po_no"         => $data->po_no,
                "supplierName"  => $data->supplierName,
                "itemCount"     => $data->itemCount
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

    public function savePOLokalBahanBaku()
    {
        try {
            $RMPurchaseOrderModel = new RMPurchaseOrderModel();
            $RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();

            $rules = [
                "po_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $insertData = [
                    "company_id" => $this->this_company_id,
                    "purchase_request_id"   => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "pph" => $this->request->getPost("pph"),
                    "potong_kg" => !empty($this->request->getPost("potong_kg")) ? true : false,
                    "is_posted" => false,
                    "cong_sebenarnya" => $this->request->getPost("cong_sebenarnya") ? formatter($this->request->getPost("cong_sebenarnya"), "STR_TO_INT") : 0,
                    "cong_batasan" => $this->request->getPost("cong_batasan") ? formatter($this->request->getPost("cong_batasan"), "STR_TO_INT") : 0,
                    "subsidi_langsung" => $this->request->getPost("subsidi_langsung") ? formatter($this->request->getPost("subsidi_langsung"), "STR_TO_INT") : 0,
                    "createdBy" => session()->get("login")->user_id,
                    "items" =>  json_decode($this->request->getPost("items"))
                ];

                $insertData["po_no"] = $RMPurchaseOrderModel->generateNoPo();

                $payload = json_encode($insertData);

                $insert = $RMPurchaseOrderModel->insert($insertData);

                foreach ($insertData["items"] as $value) {
                    $value->barang_id = $value->item_id;
                    $value->rm_purchase_order_id = $insert;
                }

                $RMPurchaseOrderDetailModel->insertBatch($insertData["items"]);

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

    public function updatePOLokalBahanBaku()
    {
        try {
            $RMPurchaseOrderModel = new RMPurchaseOrderModel();
            $RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();

            $rules = [
                "po_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $insertData = [
                    "company_id" => $this->this_company_id,
                    "purchase_request_id"   => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "pph" => $this->request->getPost("pph"),
                    "potong_kg" => !empty($this->request->getPost("potong_kg")) ? true : false,
                    "is_posted" => false,
                    "cong_sebenarnya" => $this->request->getPost("cong_sebenarnya") ? formatter($this->request->getPost("cong_sebenarnya"), "STR_TO_INT") : 0,
                    "cong_batasan" => $this->request->getPost("cong_batasan") ? formatter($this->request->getPost("cong_batasan"), "STR_TO_INT") : 0,
                    "subsidi_langsung" => $this->request->getPost("subsidi_langsung") ? formatter($this->request->getPost("subsidi_langsung"), "STR_TO_INT") : 0,
                    "createdBy" => session()->get("login")->user_id,
                    "items" =>  json_decode($this->request->getPost("items"))
                ];


                if ($insertData) {
                    $RMPurchaseOrderModel->update($id, $insertData);

                    foreach ($insertData["items"] as $value) {
                        $value->barang_id = $value->item_id;
                        $value->rm_purchase_order_id = $id;

                        if (!empty($value->isDeleted)) {
                            $RMPurchaseOrderDetailModel->where('id', $value->id)->delete();
                        }

                        $dataDetail = [
                            "id" => $value->id ?? null,
                            "rm_purchase_order_id" => $this->request->getPost("id"),
                            "barang_id" => $value->item_id,
                            "spec" => $value->spec,
                            "bagian" => $value->bagian,
                            "peti" => $value->peti,
                            "quality" => $value->quality,
                            "note" => $value->note,
                            "qty" => $value->qty,
                            "general_price" => $value->general_price,
                            "daily_price" => $value->daily_price,
                            "monthly_price" => $value->monthly_price,
                        ];

                        $RMPurchaseOrderDetailModel->upsert($dataDetail);
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

    public function updateStatusPOLokalBahanBaku()
    {
        try {
            $id = $this->request->getPost("id");
            $RMPurchaseOrderModel = new RMPurchaseOrderModel();

            $payload = [
                "is_posted" => "1"
            ];

            if (!empty($id)) {
                $RMPurchaseOrderModel->update($id, $payload);

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
        return;
    }

    public function deletePOLokalBahanBaku()
    {
        try {
            $RMPurchaseOrderModel = new RMPurchaseOrderModel();
            $RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
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

            $RMPurchaseOrderModel->delete($id);
            $RMPurchaseOrderDetailModel->where('rm_purchase_order_id', $id)->delete();

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

    public function dropdownPOLokalBahanBaku()
    {
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");

        $dataPOLokal = $this->RMPurchaseOrderModel->getNoPenerimaanBarang($id, $this->this_company_id);

        $data = [
            "data" => $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOLokalBahanBaku()
    {
        $id = $this->request->getGet("id");

        $dataPOLokal = $this->RMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }
}
