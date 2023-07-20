<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;


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
        //Get Supplier
        $responseSupplier = curl_request("GET", "/suppliers/all?kategori=LOKAL&type=BAHAN%20BAKU&idCompany=$this->this_company_id", $this->token);

        $dataSupplier = [];
        if ($responseSupplier["code"] === 200) {
            $dataSupplier = json_decode($responseSupplier["body"])->data;
        }

        $data = [
            "dataSupplier" => $dataSupplier
        ];

        return view('Purchase/poLokalBahanBaku/form', $data);
    }

    public function getByIdPOLokalBahanBaku($id = null)
    {
        //Get Supplier
        $supplierModel = new SupplierModel();
        $dataSupplier = $supplierModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN BAKU', $this->this_company_id);

        $data = [
            "dataSupplier" => $dataSupplier
        ];

        $RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();

        if (!empty($id)) {
            $dataBBLokal = $RMPurchaseOrderModel->getPoBBLokalById($id);
            $dataBBLokalDetail = $RMPurchaseOrderDetailModel->getPoBBLokalDetailById($id);
            $data["dataPOLokal"] = $dataBBLokal;
            // $data["dataPOLokal"]->rm_purchase_order_details = $dataBBLokalDetail;
            $data["dataPODetailLokal"] = $dataBBLokalDetail;
        }


        // if (!empty($id)) {
        //     $responsePOLokal = curl_request("GET", "/rawMaterialPO/$id", $this->token);
        //     $dataPOLokal = [];
        //     if ($responsePOLokal["code"] === 200) {
        //         $dataPOLokal = json_decode($responsePOLokal["body"])->data;
        //     }
        //     $data["dataPOLokal"] = $dataPOLokal;
        // }

        dd($data);
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
            $rules = [
                "po_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = json_encode([
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "pph" => $this->request->getPost("pph"),
                    "potong_kg" => !empty($this->request->getPost("potong_kg")) ? true : false,
                    "is_posted" => false,
                    "cong_sebenarnya" => $this->request->getPost("cong_sebenarnya") ? formatter($this->request->getPost("cong_sebenarnya"), "STR_TO_INT") : 0,
                    "cong_batasan" => $this->request->getPost("cong_batasan") ? formatter($this->request->getPost("cong_batasan"), "STR_TO_INT") : 0,
                    "subsidi_langsung" => $this->request->getPost("subsidi_langsung") ? formatter($this->request->getPost("subsidi_langsung"), "STR_TO_INT") : 0,
                    "items" =>  json_decode($this->request->getPost("items"))
                ]);

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);

                $response = curl_request("POST", "/rawMaterialPO", $this->token, $payload);

                if ($response["code"] === 201) {
                    $data = [
                        "id" => json_decode($response["body"])->createdId,
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        'code' => $response["code"]
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        'code' => $response["code"]
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

                $payload = json_encode([
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "pph" => $this->request->getPost("pph"),
                    "potong_kg" => !empty($this->request->getPost("potong_kg")) ? true : false,
                    "cong_sebenarnya" => $this->request->getPost("cong_sebenarnya") ? formatter($this->request->getPost("cong_sebenarnya"), "STR_TO_INT") : 0,
                    "cong_batasan" => $this->request->getPost("cong_batasan") ? formatter($this->request->getPost("cong_batasan"), "STR_TO_INT") : 0,
                    "subsidi_langsung" => $this->request->getPost("subsidi_langsung") ? formatter($this->request->getPost("subsidi_langsung"), "STR_TO_INT") : 0,
                    "items" =>  json_decode($this->request->getPost("items"))
                ]);

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);

                $response = curl_request("PATCH", "/rawMaterialPO/$id", $this->token, $payload);

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

    public function updateStatusPOLokalBahanBaku()
    {
        try {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "is_posted" => true
            ]);

            $response = curl_request("PATCH", "/rawMaterialPO/$id", $this->token, $payload);

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

    public function deletePOLokalBahanBaku()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/rawMaterialPO/$id", $this->token);
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
