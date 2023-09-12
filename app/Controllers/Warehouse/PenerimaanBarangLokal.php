<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use App\Models\BeaCukaiModel;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\IOFactory;

use Dompdf\Dompdf;

class PenerimaanBarangLokal extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $barangModel;
    protected $metadataModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $rmPurchaseOrderModel;
    protected $rmPurchaseOrderDetailModel;
    protected $supplierModel;
    protected $warehousesModel;
    protected $satuanModel;
    private $stockDetailModel;
    protected $beaCukaiModel;

    protected $dompdf;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->barangModel = new BarangModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->beaCukaiModel = new BeaCukaiModel();

        $this->dompdf = new Dompdf();
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

        //Get Warehouse
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $data = [
            "dataSatuan" => $dataSatuan,
            "dataWarehouse" => $dataWarehouse,
            "dataSupplier" => $dataSupplier,
            "dataAJU" => $dataAJU
        ];

        return view('Warehouse/penerimaanBarangLokal/form', $data);
    }

    public function getByIdPenerimaanBarangLokal($id = null)
    {
        //Get AJU
        $dataAJU = $this->metadataModel->get_by_name('jenis_dok_aju');

        //Get Warehouse
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $data["dataAJU"] = $dataAJU;

        $data["dataWarehouse"] = $dataWarehouse;

        $data["dataSatuan"] = $dataSatuan;

        if (!empty($id)) {
            $dataPenerimaanBarang = $this->penerimaanBarangModel->asObject()->find($id);

            if($dataPenerimaanBarang)
            {
                $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;
                $status_penerimaan = $dataPenerimaanBarang->status_penerimaan;

                if($status_penerimaan === "LOKAL")
                {
                    $data["dataPenerimaanBarang"] = $dataPenerimaanBarang;
                    $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "LOKAL");

                    // var_dump($dataPenerimaanBarangDetail);
                    // die;

                    if($dataPenerimaanBarangDetail)
                    {
                        $data["dataPenerimaanBarangDetail"] = $dataPenerimaanBarangDetail;
                    }

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

                        $dataNo = $this->amPurchaseOrderModel->getNoPenerimaanBarang("Lokal", $supplier_id, $this->this_company_id);

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
            // "status_bc" => $this->request->getGet("status_bc"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            "penerimaan_barang.company_id"        => $this->this_company_id,
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
            $multiple_po_no = json_decode($data->multiple_po_no);
            // $status_bc = "WAITING";
            // $condition = false;
            // $conditionSecond = [];

            // foreach($multiple_po_no as $item)
            // {
            //     if($data->tipe_bahan === "PENOLONG")
            //     {
            //         $check = $this->beaCukaiModel->checkPostingBeaCukaiByPONo($item, $this->this_company_id);

            //         if($check)
            //         {
            //             $arr = [];
            //             foreach($check as $secondItem)
            //             {
            //                 if($secondItem["tipe_bahan"] === "PENOLONG")
            //                 {
            //                     array_push($arr, $secondItem["po_no"]);
            //                 }
            //             }

            //             if(strpos(implode($arr), $item))
            //             {
            //                 array_push($conditionSecond, true);
            //             }
            //             else
            //             {
            //                 array_push($conditionSecond, false);
            //             }
            //         }
            //         else
            //         {
            //             array_push($conditionSecond, false);
            //         }
            //     }
            //     if($data->tipe_bahan === "BAKU")
            //     {
            //         $check = $this->beaCukaiModel->checkPostingBeaCukai($this->this_company_id);

            //         if($check)
            //         {
            //             $arr = [];
            //             foreach($check as $secondItem)
            //             {
            //                 if($secondItem["tipe_bahan"] === "BAKU")
            //                 {
            //                     array_push($arr, implode(json_decode($secondItem["multiple_po_no"])));
            //                 }
            //             }

            //             if(strpos(implode($arr), $item))
            //             {
            //                 array_push($conditionSecond, true);
            //             }
            //             else
            //             {
            //                 array_push($conditionSecond, false);
            //             }
            //         }
            //         else
            //         {
            //             array_push($conditionSecond, false);
            //         }
            //     }
            // }

            // if(sizeof($conditionSecond) !== 0)
            // {
            //     if(!in_array(false, $conditionSecond))
            //     {
            //         $condition = true;
            //     }
            // }

            // if($condition)
            // {
            //     $status_bc = "FINISH";
            // }

            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "warehouse_name"        => $data->warehouse_name,
                "tipe_bahan"            => $data->tipe_bahan,
                "createdAt"             => $data->createdAt ? date("d/m/Y", strtotime($data->createdAt)) : "",
                // "validation_date"       => $data->validation_date ? date("d/m/Y", strtotime($data->validation_date)) : "",
                "supplier_name"         => $data->supplier_name,
                "itemCount"             => $data->itemCount,
                "multiple_po_no"        => json_decode($data->multiple_po_no),
                "status_post"           => $data->status_post,
                // "status_bc"             => $status_bc
            ]);
        }

        // filter status bc
        // $no = 1;
        // if($this->request->getGet("status_bc") === "waiting")
        // {
        //     $newDataPenerimaanBarang = [];
        //     foreach($dataPenerimaanBarang as $item)
        //     {
        //         if($item["status_bc"] === "WAITING")
        //         {
        //             array_push($newDataPenerimaanBarang, [
        //                 "no"                    => $no++,
        //                 "id"                    => $item["id"],
        //                 "no_penerimaan_barang"  => $item["no_penerimaan_barang"],
        //                 "warehouse_name"        => $item["warehouse_name"],
        //                 "tipe_bahan"            => $item["tipe_bahan"],
        //                 "createdAt"             => $item["createdAt"],
        //                 "supplier_name"         => $item["supplier_name"],
        //                 "itemCount"             => $item["itemCount"],
        //                 "multiple_po_no"        => $item["multiple_po_no"],
        //                 "status_post"           => $item["status_post"],
        //                 "status_bc"             => $item["status_bc"]
        //             ]);
        //         }
        //     }  
        //     $penerimaanBarangData['totalFilteredData'] = sizeof($newDataPenerimaanBarang);
        //     $dataPenerimaanBarang = $newDataPenerimaanBarang;
        // }

        // if($this->request->getGet("status_bc") === "finish")
        // {
        //     $newDataPenerimaanBarang = [];
        //     foreach($dataPenerimaanBarang as $item)
        //     {
        //         if($item["status_bc"] === "FINISH")
        //         {
        //             array_push($newDataPenerimaanBarang, [
        //                 "no"                    => $no++,
        //                 "id"                    => $item["id"],
        //                 "no_penerimaan_barang"  => $item["no_penerimaan_barang"],
        //                 "warehouse_name"        => $item["warehouse_name"],
        //                 "tipe_bahan"            => $item["tipe_bahan"],
        //                 "createdAt"             => $item["createdAt"],
        //                 "supplier_name"         => $item["supplier_name"],
        //                 "itemCount"             => $item["itemCount"],
        //                 "multiple_po_no"        => $item["multiple_po_no"],
        //                 "status_post"           => $item["status_post"],
        //                 "status_bc"             => $item["status_bc"]
        //             ]);
        //         }
        //     }  
        //     $penerimaanBarangData['totalFilteredData'] = sizeof($newDataPenerimaanBarang);
        //     $dataPenerimaanBarang = $newDataPenerimaanBarang;
        // }

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
                "no_penerimaan_barang" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. Penerimaan Barang tidak boleh kosong'
                    ]
                ],
                "supplier_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Supplier tidak boleh kosong'
                    ]
                ],
                "warehouse_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Warehouse tidak boleh kosong'
                    ]
                ],
                "tipe_bahan" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tipe Bahan tidak boleh kosong'
                    ]
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
                $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                $no = $this->penerimaanBarangModel->get_no(date('m'), date('Y'), $last_day);
                $status_post = $this->request->getPost("status_post");
                $tipe_bahan = $this->request->getPost("tipe_bahan");
                $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");

                $payload = [
                    "company_id" => $this->this_company_id,
                    "no_penerimaan_barang" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("no_penerimaan_barang"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                    "acceptance_type" => $this->request->getPost("acceptance_type"),
                    "multiple_po_id" => json_encode($multiple_po_id),
                    "multiple_po_no" => $this->request->getPost("multiple_po_no"),
                    "tipe_bahan" => $tipe_bahan,
                    "status_post" => "WAITING",
                    "status_penerimaan" => "LOKAL",
                ];

                $items = json_decode($this->request->getPost("items"));

                $detailPayload = [];

                $this->penerimaanBarangModel->db->transException(true)->transStart();
                $response =  $this->penerimaanBarangModel->insert($payload);

                foreach($items as $data) {

                    $detailPayload[] = [
                        'purchase_order_details_id' => $data->purchase_order_details_id,
                        'penerimaan_barang_id' => $response,
                        'harga' => $data->harga,
                        'sub_total' => $data->sub_total,
                        'keterangan' => $data->keterangan,
                        'barang_id' => $data->barang_id,
                        'qty' => $data->qty,
                        'unit' => $data->unit,
                        'nama_barang_dok' => $data->nama_barang_dok,
                        'jml_masuk' => $data->jml_masuk,
                        'packaging' => $data->packaging,
                        'packaging_qty' => $data->packaging_qty
                    ];

                    // insert to stock
                    // $this->stockDetailModel->addStock($data->barang_id, $payload['warehouse_id'], $data->qty);
                }

                $this->penerimaanBarangDetailModel->insertBatch($detailPayload);

                $this->penerimaanBarangModel->db->transComplete();

                $data = [
                    "id"        => $response,
                    "status"    => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => $payload,
                    "response"  => $response,
                    'token'     => csrf_hash()
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
                "no_penerimaan_barang" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. Penerimaan Barang tidak boleh kosong'
                    ]
                ],
                "supplier_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Supplier tidak boleh kosong'
                    ]
                ],
                "warehouse_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Warehouse tidak boleh kosong'
                    ]
                ],
                "tipe_bahan" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tipe Bahan tidak boleh kosong'
                    ]
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
                $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                $no = $this->penerimaanBarangModel->get_no(date('m'), date('Y'), $last_day);
                $tipe_bahan = $this->request->getPost("tipe_bahan");
                $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");

                $payload = [
                    "company_id" => $this->this_company_id,
                    "no_penerimaan_barang" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("no_penerimaan_barang"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "warehouse_id" => formatter($this->request->getPost("warehouse_id"), "STR_TO_INT"),
                    "acceptance_type" => $this->request->getPost("acceptance_type"),
                    "multiple_po_id" => json_encode($multiple_po_id),
                    "multiple_po_no" => $this->request->getPost("multiple_po_no"),
                    "tipe_bahan" => $tipe_bahan
                ];

                $items = json_decode($this->request->getPost("items"));
                
                $condition = [
                    'id' => $id
                ];

                $response = $this->penerimaanBarangModel->where($condition)->set($payload)->update();

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $detailPayload = [
                            'purchase_order_details_id' => $data->purchase_order_details_id,
                            'penerimaan_barang_id' => $id,
                            'harga' => $data->harga,
                            'sub_total' => $data->sub_total,
                            'keterangan' => $data->keterangan,
                            'barang_id' => $data->barang_id,
                            'qty' => $data->qty,
                            'unit' => $data->unit,
                            'nama_barang_dok' => $data->nama_barang_dok,
                            'jml_masuk' => $data->jml_masuk,
                            'packaging' => $data->packaging,
                            'packaging_qty' => $data->packaging_qty
                        ];

                        // kalau hapus
                        if($data->is_delete)
                        {
                            $responseDetail = $this->penerimaanBarangDetailModel->delete($data->id);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Dihapus';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }

                         // kalau update
                        if($data->id)
                        {
                            $conditionDetail = [
                                'id' => $data->id
                            ];

                            $responseDetail = $this->penerimaanBarangDetailModel->where($conditionDetail)->set($detailPayload)->update();

                            if(!$responseDetail) {
                                $message =  'Data Gagal Disimpan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }

                        // kalau create
                        else
                        {
                            $responseDetail = $this->penerimaanBarangDetailModel->insert($detailPayload);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Diubah';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                    
                    $data = [
                        "id" => "",
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        "response" => $response,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                        'code' => $response["code"]
                    ];
                    echo json_encode($data);
                }
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
        try {
            $id = $this->request->getPost("id");

            $dataPenerimaanBarang = $this->penerimaanBarangModel->getById($id);

            if (empty($dataPenerimaanBarang)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data penerimaan barang tidak ada",
                    "payload"   => "",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $multiple_po_id = json_decode($dataPenerimaanBarang->multiple_po_id);
            $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;
            
            $detail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "LOKAL");

            $this->penerimaanBarangModel->db->transException(true)->transStart();

            foreach ($detail as $item) {
                // check po already closed or not
                if ($item["status_penerimaan"] === "0") {
                    $jml_masuk = $item["jml_masuk"] ? formatter($item["jml_masuk"], "STR_TO_FLOAT") : 0;
                    $qty_diterima = $item["qty_diterima"] ? formatter($item["qty_diterima"], "STR_TO_FLOAT") : 0;
                    $remaining_qty = $item["remaining_qty"] ? formatter($item["remaining_qty"], "STR_TO_FLOAT") : 0;
                    $barang_id = $item["barang_id"] ? formatter($item["barang_id"], "STR_TO_INT") : 0;
                    $purchase_order_details_id = $item["purchase_order_details_id"] ? formatter($item["purchase_order_details_id"], "STR_TO_INT") : 0;

                    // kemasan
                    $packaging = $item["packaging"] ? formatter($item["packaging"], "STR_TO_INT") : 0;
                    $packaging_qty = $item["packaging_qty"] ? formatter($item["packaging_qty"], "STR_TO_FLOAT") : 0;

                    $conditionRemain = [
                        'id' => $purchase_order_details_id
                    ];

                    $payloadRemain = [
                        'qty_diterima' => $qty_diterima + $jml_masuk,
                        'remaining_qty' => $remaining_qty - $jml_masuk
                    ];

                    // UPDATE REMAINING QTY AND JML DITERIMA
                    if ($tipe_bahan === "BAKU") {
                        $responseDet = $this->rmPurchaseOrderDetailModel->where($conditionRemain)
                            ->set($payloadRemain)
                            ->update();

                        if(!$responseDet) {
                            $message =  'Gagal Ubah Remaining';
                            $data = [
                                "status"    => false,
                                "message"   => $message,
                                "payload"   => "",
                                'token'     => csrf_hash()
                            ];
                            echo json_encode($data);
                            return;
                        }
                    } elseif ($tipe_bahan === "PENOLONG") {
                        $responseDet = $this->amPurchaseOrderDetailModel->where($conditionRemain)
                            ->set($payloadRemain)
                            ->update();

                        if (!$responseDet) {
                            $message =  'Gagal Ubah Remaining';
                            $data = [
                                "status"    => false,
                                "message"   => $message,
                                "payload"   => "",
                                'token'     => csrf_hash()
                            ];
                            echo json_encode($data);
                            return;
                        }
                    }

                    // ADD STOK BARANG
                    $find = $this->barangModel->find($barang_id);

                    // ADD STOK KEMASAN
                    $find_packaging = $this->barangModel->find($packaging);

                    if ($find) {
                        $stok = $find["stok"] ? formatter($find["stok"], "STR_TO_FLOAT") : 0;

                        $payloadupdateStok = [
                            'stok' => $stok + $jml_masuk
                        ];
        
                        $responseStok = $this->barangModel->where('id', $barang_id)
                            ->set($payloadupdateStok)
                            ->update();    

                        if (!$responseStok) {
                            $message =  'Gagal Tambah Stok';
                            $data = [
                                "status"    => false,
                                "message"   => $message,
                                "payload"   => "",
                                'token'     => csrf_hash()
                            ];
                            echo json_encode($data);
                            return;
                        }
                    }

                    if ($find_packaging) {
                        $stok = $find_packaging["stok"] ? formatter($find_packaging["stok"], "STR_TO_FLOAT") : 0;

                        $payloadupdateStok = [
                            'stok' => $stok + $packaging_qty
                        ];
        
                        $responseStok = $this->barangModel->where('id', $packaging)
                            ->set($payloadupdateStok)
                            ->update();    

                        if (!$responseStok) {
                            $message =  'Gagal Tambah Stok';
                            $data = [
                                "status"    => false,
                                "message"   => $message,
                                "payload"   => "",
                                'token'     => csrf_hash()
                            ];
                            echo json_encode($data);
                            return;
                        }
                    }

                    // add stock detail barang
                    $this->stockDetailModel->addStock($barang_id, $dataPenerimaanBarang->warehouse_id, $jml_masuk, 'New');

                    // add stock detail barang kemasan
                    $this->stockDetailModel->addStock($packaging, $dataPenerimaanBarang->warehouse_id, $packaging_qty, 'Scrap');
                }
            }

            // automate close po check item by check ech po number
            foreach ($multiple_po_id as $item) {
                $check_close = true;
                
                if ($tipe_bahan === "BAKU") {
                    $responseDetail = $this->rmPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($item);

                    if ($responseDetail) {
                        foreach ($responseDetail as $itemDetail) {
                            // check if each item must 0 remaining qty to close
                            if ($itemDetail["remaining_qty"] !== "0.00") {
                                $check_close = false;
                            }
                        }

                        if ($check_close) {
            
                            $responseStatusPenerimaan = $this->rmPurchaseOrderModel
                                ->where('id', $item)
                                ->set('status_penerimaan', 1)
                                ->update();

                            if (!$responseStatusPenerimaan) {
                                $message =  'Gagal Ubah Status Penerimaan';
                                $data = [
                                    "status"    => false,
                                    "message"   => $message,
                                    "payload"   => "",
                                    'token'     => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                } elseif ($tipe_bahan === "PENOLONG") {
                    $responseDetail = $this->amPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($item);

                    if ($responseDetail) {
                        
                        foreach ($responseDetail as $itemDetail) {
                            // check if each item must 0 remaining qty to close
                            if ($itemDetail["remaining_qty"] !== "0.00") {
                                $check_close = false;
                            }  
                        }

                        if ($check_close) {
            
                            $responseStatusPenerimaan = $this->amPurchaseOrderModel
                                ->where('id', $item)
                                ->set('status_penerimaan', 1)
                                ->update();

                            if (!$responseStatusPenerimaan) {
                                $message =  'Gagal Ubah Status Penerimaan';
                                $data = [
                                    "status"    => false,
                                    "message"   => $message,
                                    "payload"   => "",
                                    'token'     => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }
            }

            $response = $this->penerimaanBarangModel
                ->where(['id' => $id])
                ->set(['status_post' => 'FINISH'])
                ->update();

            $this->penerimaanBarangModel->db->transComplete();

            if ($response) {
                $data = [
                    "status"    => true,
                    "message"   => "Data berhasil di posting",
                    "payload"   => "",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data gagal di posting",
                    "payload"   => "",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            }
        }
        catch(\Exception $e)
        {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
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
                $findPenerimaanBarang = $this->penerimaanBarangModel->find($id);
                if ($findPenerimaanBarang) {
                    $response =  $this->penerimaanBarangModel->delete($id);
                    if ($response) {
                        $data = [
                            "status"            => true,
                            "message"   => "Data Berhasil dihapus",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    } else {
                        $message = 'Data Gagal Dihapus';
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
                        "message"    => "Data Tidak Ditemukan",
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

    public function exportTable()
    {
        // $dateStart = $this->request->getGet("dateStart") ? date("d-m-Y", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "-";
        // $dateEnd = $this->request->getGet("dateEnd") ? date("d-m-Y", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "-";

        // $spreadsheet = new Spreadsheet();
        // // tulis header/nama kolom 
        // $spreadsheet->setActiveSheetIndex(0)
        //             ->setCellValue('A1', 'Laporan Penerimaan Barang Lokal');

        // $spreadsheet->setActiveSheetIndex(0)
        //             ->setCellValue('A3', 'Tanggal')
        //             ->setCellValue('B3', ':')
        //             ->setCellValue('C3', $dateStart . " S/D " . $dateEnd);

        // $spreadsheet->setActiveSheetIndex(0)
        //             ->setCellValue('A4', 'No.')
        //             ->setCellValue('B4', 'Dok. Penerimaan')
        //             ->setCellValue('F4', 'Divisi')
        //             ->setCellValue('G4', 'Gudang')
        //             ->setCellValue('H4', 'Invoice')
        //             ->setCellValue('J4', 'Supplier')
        //             ->setCellValue('K4', 'kode Barang')
        //             ->setCellValue('L4', 'Nama Barang')
        //             ->setCellValue('M4', 'Satuan')
        //             ->setCellValue('N4', 'Jumlah')
        //             ->setCellValue('O4', 'Nilai')
        //             ->setCellValue('P4', 'Keterangan')
        //             ->setCellValue('Q4', 'Keterangan 2');

        // $spreadsheet->setActiveSheetIndex(0)
        //             ->setCellValue('B5', 'Nomor PR')
        //             ->setCellValue('C5', 'Nomor PO')
        //             ->setCellValue('D5', 'Nomor')
        //             ->setCellValue('E5', 'Tanggal')
        //             ->setCellValue('H5', 'Nomor')
        //             ->setCellValue('I5', 'Tanggal');

        // $addCondition = [
        //     "search"            => $this->request->getGet("search"),
        //     "status"            => $this->request->getGet("status"),
        //     "statuspenerimaan"  => "LOKAL",
        //     "sort"              => $this->request->getGet("sort"),
        //     "sortType"          => $this->request->getGet("sortType"),
        //     "startdate"         => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
        //     "lastdate"          => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        // ];

        // $condition = [
        //     "status_penerimaan" => "LOKAL"
        // ];

        // $penerimaanBarangData = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailList($condition, $addCondition, 100000000, 0);

        // $no = 1;
        // $column = 6;

        // // var_dump($penerimaanBarangData);
        // // die;

        // // tulis data mobil ke cell
        // foreach($penerimaanBarangData['data'] as $data) {
        //     $tipe_bahan = $data->tipe_bahan;
        //     $dataLokalDetail = "";
        //     if($tipe_bahan === "BAKU")
        //     {
        //         $dataLokalDetail = $this->rmPurchaseOrderDetailModel->getPurchaseOrderDetailById($data->purchase_order_details_id);
        //     }

        //     if($tipe_bahan === "PENOLONG")
        //     {
        //         $dataLokalDetail = $this->amPurchaseOrderDetailModel->getPurchaseOrderDetailById($data->purchase_order_details_id);
        //     }

        //     $spreadsheet->setActiveSheetIndex(0)
        //                 ->setCellValue('A' . $column, $no++)
        //                 ->setCellValue('B' . $column, $dataLokalDetail ? $dataLokalDetail->spp_no : "")
        //                 ->setCellValue('C' . $column, $dataLokalDetail ? $dataLokalDetail->po_no : "")
        //                 ->setCellValue('D' . $column, $data->no_penerimaan_barang)
        //                 ->setCellValue('E' . $column, $data->validation_date)
        //                 ->setCellValue('F' . $column, '')
        //                 ->setCellValue('G' . $column, $data->warehouse_name)
        //                 ->setCellValue('H' . $column, '0')
        //                 ->setCellValue('I' . $column, date("Y-m-d", strtotime($data->createdAt)))
        //                 ->setCellValue('J' . $column, $data->supplier_name)
        //                 ->setCellValue('K' . $column, $data->kode_barang)
        //                 ->setCellValue('L' . $column, $data->nama_barang)
        //                 ->setCellValue('M' . $column, $data->kode_satuan)
        //                 ->setCellValue('N' . $column, $data->jml_masuk)
        //                 ->setCellValue('O' . $column, '')
        //                 ->setCellValue('P' . $column, '')
        //                 ->setCellValue('Q' . $column, '');

        //     $spreadsheet->getActiveSheet()->getStyle('A' . $column)->getAlignment()->setHorizontal('center');
        //     $spreadsheet->getActiveSheet()->getStyle('E' . $column)->getAlignment()->setHorizontal('center');
        //     $spreadsheet->getActiveSheet()->getStyle('G' . $column)->getAlignment()->setHorizontal('left');

        //     $column++;
        // }

        // $spreadsheet->setActiveSheetIndex(0)
        //             ->setCellValue('N' . $column, 'Total')
        //             ->setCellValue('O' . $column, '');

        // $spreadsheet->getActiveSheet()->getStyle('N' . $column . ':' . 'O' . $column)->getFont()->setBold(true);

        // $spreadsheet->getActiveSheet()->getStyle('A4:Q4')->getFont()->setBold(true);
        // $spreadsheet->getActiveSheet()->getStyle('A5:Q5')->getFont()->setBold(true);

        // $spreadsheet->getActiveSheet()->MergeCells('A4:A5');
        // $spreadsheet->getActiveSheet()->MergeCells('B4:E4');
        // $spreadsheet->getActiveSheet()->MergeCells('F4:F5');
        // $spreadsheet->getActiveSheet()->MergeCells('G4:G5');
        // $spreadsheet->getActiveSheet()->MergeCells('H4:I4');
        // $spreadsheet->getActiveSheet()->MergeCells('J4:J5');
        // $spreadsheet->getActiveSheet()->MergeCells('K4:K5');
        // $spreadsheet->getActiveSheet()->MergeCells('L4:L5');
        // $spreadsheet->getActiveSheet()->MergeCells('M4:M5');
        // $spreadsheet->getActiveSheet()->MergeCells('N4:N5');
        // $spreadsheet->getActiveSheet()->MergeCells('O4:O5');
        // $spreadsheet->getActiveSheet()->MergeCells('P4:P5');
        // $spreadsheet->getActiveSheet()->MergeCells('Q4:Q5');

        // $spreadsheet->getActiveSheet()->getStyle('A4:A5')->getAlignment()->setVertical('center')->setHorizontal('right');
        // $spreadsheet->getActiveSheet()->getStyle('B4:E4')->getAlignment()->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('F4:F5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('G4:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('H4:I4')->getAlignment()->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('J4:J5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('K4:K5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('L4:L5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('M4:M5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('N4:N5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('O4:O5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('P4:P5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('Q4:Q5')->getAlignment()->setVertical('center')->setHorizontal('center');

        // $spreadsheet->getActiveSheet()->getStyle('B5:E5')->getAlignment()->setHorizontal('center');
        // $spreadsheet->getActiveSheet()->getStyle('H5:I5')->getAlignment()->setHorizontal('center');

        // foreach (range('B', 'Q') as $letra) {  
        //     $spreadsheet->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
        // }
        
        // // tulis dalam format .xlsx
        // $writer = new Xlsx($spreadsheet);
        // $fileName = 'Penerimaan Barang Lokal';

        // // Redirect hasil generate xlsx ke web client
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename='.$fileName.'.xlsx');
        // header('Cache-Control: max-age=0');

        // $writer->save('php://output');
    }

    public function print($id = null) 
    {
        if($id)
        {
            $filename = "Penerimaan Barang Lokal";

            $data = [];
            $dataPenerimaanBarang = $this->penerimaanBarangModel->getById($id);

            if($dataPenerimaanBarang)
            {
                $status_penerimaan = $dataPenerimaanBarang->status_penerimaan;
                $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;

                if($status_penerimaan === "LOKAL")
                {
                    $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "LOKAL");

                    // var_dump($dataPenerimaanBarang);
                    // die;

                    if($dataPenerimaanBarangDetail)
                    {
                        $data["dataPenerimaanBarang"] = $dataPenerimaanBarang;
                        $data["dataPenerimaanBarangDetail"] = $dataPenerimaanBarangDetail;

                        // var_dump(json_decode($dataPenerimaanBarang->multiple_po_no));
                        // die;
                    }
                }
            }

            // load HTML content
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangLokal/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'portrait');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Warehouse/penerimaanBarangLokal/print', $data);
        }
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

    public function getReceivedItemsBySupplier($supplierId)
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "kategori"      => "LOKAL",
            "type"          => "BAHAN BAKU"
        ];

        $condition = [
            // "suppliers.company_id"  => $this->this_company_id,
            "penerimaan_barang.status_penerimaan"       => "LOKAL",
            "penerimaan_barang.tipe_bahan"              => "BAKU",
            // "penerimaan_barang_detail.summarized_qty <" => 'penerimaan_barang_detail.qty'

            // "search"                                => $this->request->getGet("search"),
            // "sort"                                  => $this->request->getGet("sort"),
            // "sortType"                              => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $itemData = $this->penerimaanBarangModel
            ->getReceivedItemsBySupplier($supplierId, $condition, $limit, $offset);

        $receivedData = [];

        foreach ($itemData['data'] as $data) {
            array_push($receivedData, [
                "id"                    => $data->id,
                "no_po"                 => "jugijagiju",
                "lpb_date"              => $data->lpb_date,
                "no_lpb"                => $data->no_lpb,
                "item_name"             => $data->item_name,
                "lpb_qty"               => $data->lpb_qty,
                "price"                 => floatval($data->price),
                "return_qty"            => 0, 
                "received_qty"          => 0,
                "qty_will_be_received"  => $data->lpb_qty,
                "unit"                  => $data->unit
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $itemData['totalData'],
            "recordsFiltered"   => $itemData['totalFilteredData'],
            "data"              => $receivedData,
            // "response" => $response,
            // "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function getReceivedNoBySupplier($supplierId)
    {
        $condition = [
            "penerimaan_barang.company_id"          => $this->this_company_id,
            "penerimaan_barang.status_post"         => "FINISH",
            "penerimaan_barang.status_penerimaan"   => "LOKAL",
            "penerimaan_barang.is_summarized"       => 0
        ];

        $itemData = $this->penerimaanBarangModel
            ->getReceivedNoBySupplier($supplierId, $condition);

        $data = [
            "data"  => $itemData
        ];

        echo json_encode($data);
        return;
    }
}