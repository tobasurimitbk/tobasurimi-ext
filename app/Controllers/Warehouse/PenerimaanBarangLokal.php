<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

use App\Models\AMPurchaseOrderModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SupplierModel;

class PenerimaanBarangLokal extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $metadataModel;
    protected $penerimaanBarangModel;
    protected $rmPurchaseOrderModel;
    protected $supplierModel;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->supplierModel = new SupplierModel();
    }

    public function penerimaanBarangLokal()
    {
        return view('Warehouse/penerimaanBarangLokal/index');
    }

    public function createPenerimaanBarangLokal()
    {
        //Get AJU
        $dataAJU = $this->metadataModel->get_by_name('jenis_dok_aju');

        //Get Supplier
        $dataSupplier = $this->supplierModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN BAKU', $this->this_company_id);

        $data = [
            "dataSupplier" => $dataSupplier,
            "dataAJU" => $dataAJU
        ];

        return view('Warehouse/penerimaanBarangLokal/form', $data);
    }

    public function getByIdPenerimaanBarangLokal($id = null)
    {
        //Get AJU
        $dataAJU = $this->metadataModel->get_by_name('jenis_dok_aju');

        $data["dataAJU"] = $dataAJU;

        if (!empty($id)) {
            $dataPenerimaanBarang = $this->penerimaanBarangModel->asObject()->find($id);
            if($dataPenerimaanBarang)
            {
                $status_penerimaan = $dataPenerimaanBarang->status_penerimaan;

                if($status_penerimaan === "LOKAL")
                {
                    $data["dataPenerimaanBarang"] = $dataPenerimaanBarang;

                    $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;

                    if($tipe_bahan === "BAKU")
                    {
                        $supplier_id = $dataPenerimaanBarang->supplier_id;

                        $dataNo = $this->rmPurchaseOrderModel->getNoPenerimaanBarang($supplier_id, $this->this_company_id);

                        //Get Supplier
                        $dataSupplier = $this->supplierModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN BAKU', $this->this_company_id);

                        $data["dataNo"] = $dataNo;
                        $data["dataSupplier"] = $dataSupplier;
                    }
                    if($tipe_bahan === "PENOLONG")
                    {
                        $supplier_id = $dataPenerimaanBarang->supplier_id;

                        $dataNo = $this->amPurchaseOrderModel->getNoPenerimaanBarangPenolong("Lokal", $supplier_id, $this->this_company_id);

                        //Get Supplier
                        $dataSupplier = $this->supplierModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN PENOLONG', $this->this_company_id);

                        $data["dataNo"] = $dataNo;
                        $data["dataSupplier"] = $dataSupplier;
                    }
                } 
            }
        }

        return view('Warehouse/penerimaanBarangLokal/form', $data);
    }

    public function allPenerimaanBarangLokal()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sorttype" => $this->request->getGet("sortType"),
            "statuspenerimaan" => "LOKAL",
            "status" => $this->request->getGet("status"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            "company_id"        => $this->this_company_id,
            "status_penerimaan" => "LOKAL"
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status" => $this->request->getGet("status"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $penerimaanBarangData = $this->penerimaanBarangModel->getPenerimaanBarangList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($penerimaanBarangData['data'] as $data) {
            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "invoice_no"            => $data->invoice_no,
                "multiple_po_no"        => json_decode($data->multiple_po_no),
                "acceptance_type"       => $data->acceptance_type,
                "aju_type"              => $data->aju_type,
                "aju_no"                => $data->aju_no,
                "validation_date"       => $data->validation_date ? date("d/m/Y", strtotime($data->validation_date)) : "",
                "sender_name"           => $data->sender_name,
                "status_post"           => $data->status_post
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $penerimaanBarangData['totalData'],
            "recordsFiltered"   => $penerimaanBarangData['totalFilteredData'],
            "data"              => $dataPenerimaanBarang,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePenerimaanBarangLokal()
    {
        try{
            $rules = [
                "supplier_id" => [
                    "rules" => "required"
                ],
                "aju_document_type" => [
                    "rules" => "required"
                ],
                "tipe_bahan" => [
                    "rules" => "required"
                ],
                "aju_no" => [
                    "rules" => "required"
                ],
                "validation_date" => [
                    "rules" => "required"
                ],
                "no_registration" => [
                    "rules" => "required"
                ],
                "letter_no" => [
                    "rules" => "required"
                ],
                "invoice_no" => [
                    "rules" => "required"
                ],
                "packaging" => [
                    "rules" => "required"
                ],
                "total_weight" => [
                    "rules" => "required"
                ],
                "shipping_cost" => [
                    "rules" => "required"
                ],
                "biaya_masuk" => [
                    "rules" => "required"
                ],
                "ppnbm" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = json_encode([
                    "no_penerimaan_barang" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("no_penerimaan_barang"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "multiple_po_id" => formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT"),
                    "multiple_po_no" => json_decode($this->request->getPost("multiple_po_no")),
                    "tipe_bahan" => $this->request->getPost("tipe_bahan"),
                    "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                    "aju_no" => $this->request->getPost("aju_no"),
                    "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                    "no_registration" => $this->request->getPost("no_registration"),
                    "letter_no" => $this->request->getPost("letter_no"),
                    "invoice_no" => $this->request->getPost("invoice_no"),
                    "packaging" => $this->request->getPost("packaging"),
                    "total_weight" => $this->request->getPost("total_weight"),
                    "shipping_cost" => formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT"),
                    "biaya_masuk" => formatter($this->request->getPost("biaya_masuk"), "CURR_TO_INT"),
                    "ppnbm" => formatter($this->request->getPost("ppnbm"), "CURR_TO_INT"),
                    "status_post" => "WAITING",
                    "status_penerimaan" => "LOKAL",
                    "penerimaan_barang_detail" => json_decode($this->request->getPost("items"))
                ]);

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);
                
                $response = curl_request("POST", "/penerimaanBarang", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "id" => json_decode($response["body"])->data->id,
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        "response" => $response,
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
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updatePenerimaanBarangLokal()
    {
        try{
            $rules = [
                "supplier_id" => [
                    "rules" => "required"
                ],
                "aju_document_type" => [
                    "rules" => "required"
                ],
                "tipe_bahan" => [
                    "rules" => "required"
                ],
                "aju_no" => [
                    "rules" => "required"
                ],
                "validation_date" => [
                    "rules" => "required"
                ],
                "no_registration" => [
                    "rules" => "required"
                ],
                "letter_no" => [
                    "rules" => "required"
                ],
                "invoice_no" => [
                    "rules" => "required"
                ],
                "packaging" => [
                    "rules" => "required"
                ],
                "total_weight" => [
                    "rules" => "required"
                ],
                "shipping_cost" => [
                    "rules" => "required"
                ],
                "biaya_masuk" => [
                    "rules" => "required"
                ],
                "ppnbm" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = json_encode([
                    "no_penerimaan_barang" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("no_penerimaan_barang"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "multiple_po_id" => formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT"),
                    "multiple_po_no" => json_decode($this->request->getPost("multiple_po_no")),
                    "tipe_bahan" => $this->request->getPost("tipe_bahan"),
                    "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                    "aju_no" => $this->request->getPost("aju_no"),
                    "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                    "no_registration" => $this->request->getPost("no_registration"),
                    "letter_no" => $this->request->getPost("letter_no"),
                    "invoice_no" => $this->request->getPost("invoice_no"),
                    "packaging" => $this->request->getPost("packaging"),
                    "total_weight" => $this->request->getPost("total_weight"),
                    "shipping_cost" => formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT"),
                    "biaya_masuk" => formatter($this->request->getPost("biaya_masuk"), "CURR_TO_INT"),
                    "ppnbm" => formatter($this->request->getPost("ppnbm"), "CURR_TO_INT"),
                    "status_post" => "WAITING",
                    "status_penerimaan" => "LOKAL",
                    "penerimaan_barang_detail" => json_decode($this->request->getPost("items"))
                ]);

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);
                
                $response = curl_request("PATCH", "/penerimaanBarang/$id", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "id" => "",
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        'code' => $response["code"]
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
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
                    "message"    => "Data Gagal Diubah",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateStatusPenerimaanBarangLokal()
    {
        try{
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "status_post" => "FINISH"
            ]);
            
            $response = curl_request("PATCH", "/penerimaanBarang/status/$id", $this->token, $payload);

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
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deletePenerimaanBarangLokal()
    {
        try{
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/penerimaanBarang/$id", $this->token);
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
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function dropdownPenerimaanBarangLokal()
    {
        $payload = [
            "idsupplier" => $this->request->getGet("id"),
            "statuspenerimaan" => "LOKAL",
            "tipebahan" => $this->request->getGet("tipe")
        ];

        $dataPenerimaanBarang = [];
        $responsePenerimaanBarang = curl_request("GET", "/penerimaanBarang/dropdown-tandaTerimaFaktur", $this->token, $payload);
        if ($responsePenerimaanBarang["code"] === 200) {
            $dataPenerimaanBarang = json_decode($responsePenerimaanBarang["body"])->data;
        }

        $data = [
            "data" =>  $dataPenerimaanBarang,
            "response" => $responsePenerimaanBarang,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }
}