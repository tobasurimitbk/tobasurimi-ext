<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\SppModel;
use App\Models\SupplierModel;

class POImportBahanBaku extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $metadataModel;
    protected $rmImportPOModel;
    protected $rmImportPODetailModel;
    protected $sppModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metadataModel = new MetadataModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();
        $this->sppModel = new SppModel();
        $this->supplierModel = new SupplierModel();
    }

    public function poImportBahanBaku()
    {
        return view('Purchase/poImportBahanBaku/index');
    }

    public function createPOImportBahanBaku()
    {
        //Get SPP Number
        $dataSPP = $this->sppModel->getNoSPP("Bahan Baku Import");

        //Get Supplier
        $dataSupplier = $this->supplierModel->getSupplierByKategoriAndType('IMPORT', 'BAHAN BAKU', $this->this_company_id);

        //Get Valuta Asing By Metadata
        $dataValuta = $this->metadataModel->get_by_name('Valuta Asing');
        
        $data = [
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        return view('Purchase/poImportBahanBaku/form', $data);
    }

    public function getByIdPOImportBahanBaku($id = null)
    {
        //Get SPP Number
        $dataSPP = $this->sppModel->getNoSPP("Bahan Baku Import");

        //Get Supplier
        $dataSupplier = $this->supplierModel->getSupplierByKategoriAndType('IMPORT', 'BAHAN BAKU', $this->this_company_id);

        //Get Valuta Asing By Metadata
        $dataValuta = $this->metadataModel->get_by_name('Valuta Asing');
        
        $data = [
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        if (!empty($id)) {
            $dataPOImport = $this->rmImportPOModel->asObject()->find($id);
            $data["dataPOImport"] = $dataPOImport;

            // var_dump($dataPOImport);
            // die;
        }

        return view('Purchase/poImportBahanBaku/form', $data);
        
        return;
    }

    public function allPOImportBahanBaku()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            // "requestStatus" => $this->request->getGet("status"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            "rm_import_pos.company_id"        => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $poImportData = $this->rmImportPOModel->getPOList($condition, $addCondition, $limit, $offset);

        $dataPOImport = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poImportData['data'] as $data) {
            array_push($dataPOImport, [
                "no"            => $no++,
                "id"            => $data->id,
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "po_no"         => $data->po_no,
                "supplierName"  => $data->supplierName,
                "total"         => number_format($data->total),
                "currency"      => $data->currency,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $poImportData['totalData'],
            "recordsFiltered"   => $poImportData['totalFilteredData'],
            "data"              => $dataPOImport,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePOImportBahanBaku()
    {
        try{
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
                $payload = json_encode([
                    "purchase_request_id" => formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT"),
                    "po_no" => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "payment_term" => $this->request->getPost("payment_term") ? formatter($this->request->getPost("payment_term"), "STR_TO_INT") : 0,
                    "currency" => formatter($this->request->getPost("currency"), "STR_TO_INT"),
                    "payment_date" => $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))) : "",
                    "dpp" => formatter($this->request->getPost("dpp"), "CURR_TO_INT"),
                    "note" => $this->request->getPost("note"),
                    "isPosted" => false,
                    "items" =>  json_decode($this->request->getPost("items"))
                ]);

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);
                
                $response = curl_request("POST", "/rawMaterialImportPO", $this->token, $payload);

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

    public function updatePOImportBahanBaku()
    {
        try{
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
                    "items" => json_decode($this->request->getPost("items"))
                ]);

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);

                $response = curl_request("PATCH", "/rawMaterialImportPO/$id", $this->token, $payload);

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

    public function updateStatusPOImportBahanBaku()
    {
        try{
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "is_posted" => true
            ]);
            
            $response = curl_request("PATCH", "/rawMaterialImportPO/$id", $this->token, $payload);

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

    public function deletePOImportBahanBaku()
    {
        try{
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/rawMaterialImportPO/$id", $this->token);
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

    public function dropdownPOImportBahanBaku()
    {
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");

        $dataPOImport = $this->rmImportPOModel->getNoPenerimaanBarang($id, $this->this_company_id);

        $data = [
            "data" => $dataPOImport
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOImportBahanBaku()
    {
        $id = $this->request->getGet("id");

        $dataPOImport = $this->rmImportPODetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOImport
        ];

        echo json_encode($data);
        return;
    }
}