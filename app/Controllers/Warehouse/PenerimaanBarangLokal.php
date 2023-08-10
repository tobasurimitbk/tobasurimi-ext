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
            $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;

            if($dataPenerimaanBarang)
            {
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
            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "tipe_bahan"            => $data->tipe_bahan,
                "warehouse_name"        => $data->warehouse_name,
                "validation_date"       => $data->validation_date ? date("d/m/Y", strtotime($data->validation_date)) : "",
                "supplier_name"         => $data->supplier_name,
                "itemCount"             => $data->itemCount
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
                "aju_document_type" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Jenis Dokumen tidak boleh kosong'
                    ]
                ],
                "tipe_bahan" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tipe Bahan tidak boleh kosong'
                    ]
                ],
                "aju_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. AJU tidak boleh kosong'
                    ]
                ],
                "validation_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal Pendaftaran tidak boleh kosong'
                    ]
                ],
                "no_registration" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. Pendaftaran tidak boleh kosong'
                    ]
                ],
                "invoice_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. Invoice tidak boleh kosong'
                    ]
                ],
                "total_weight" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Berat tidak boleh kosong'
                    ]
                ],
                "biaya_masuk" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Biaya Masuk tidak boleh kosong'
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
                $no = $this->penerimaanBarangModel->get_no(date('d'), date('m'), date('Y'), $last_day);
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
                    "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                    "aju_no" => $this->request->getPost("aju_no"),
                    "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                    "no_registration" => $this->request->getPost("no_registration"),
                    "letter_no" => $this->request->getPost("letter_no"),
                    "invoice_no" => $this->request->getPost("invoice_no"),
                    "total_weight" => $this->request->getPost("total_weight"),
                    "shipping_cost" => formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT"),
                    "biaya_masuk" => formatter($this->request->getPost("biaya_masuk"), "CURR_TO_INT"),
                    "ppnbm" => formatter($this->request->getPost("ppnbm"), "CURR_TO_INT"),
                    "status_post" => "FINISH",
                    "status_po" => $status_post === "WAITING" ? "OPEN" : "CLOSED",
                    "status_penerimaan" => "LOKAL",
                ];

                $items = json_decode($this->request->getPost("items"));

                // $data = [
                //     "status"            => false,
                //     "message"    => $detailPayload,
                //     "payload"   => $detailPayload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);

                if($status_post === "FINISH")
                {
                    if($tipe_bahan === "BAKU")
                    {
                        foreach($multiple_po_id as $po_id)
                        {
                            $conditionUpdate = [
                                'id' => $po_id
                            ];

                            $payloadupdate = [
                                'status_penerimaan' => 1
                            ];
            
                            $responseStatusPenerimaan = $this->rmPurchaseOrderModel->where($conditionUpdate)->set($payloadupdate)->update();

                            if(!$responseStatusPenerimaan) {
                                $message =  'Data Ubah Status Penerimaan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                            }
                        }
                    }
                    if($tipe_bahan === "PENOLONG")
                    {
                        foreach($multiple_po_id as $po_id)
                        {
                            $conditionUpdate = [
                                'id' => $po_id
                            ];

                            $payloadupdate = [
                                'status_penerimaan' => 1
                            ];
            
                            $responseStatusPenerimaan = $this->amPurchaseOrderModel->where($conditionUpdate)->set($payloadupdate)->update();

                            if(!$responseStatusPenerimaan) {
                                $message =  'Gagal Ubah Status Penerimaan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                            }
                        }
                    }
                }
                
                $response =  $this->penerimaanBarangModel->insert($payload);

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $detailPayload = [
                            'purchase_order_details_id' => $data->purchase_order_details_id,
                            'penerimaan_barang_id' => $response,
                            'harga' => $data->harga,
                            'sub_total' => $data->sub_total,
                            'keterangan' => $data->keterangan,
                            'barang_id' => $data->barang_id,
                            'qty' => $data->qty,
                            'pph' => $data->ppn,
                            'ppn' => $data->pph,
                            'unit' => $data->unit,
                            'nama_barang_dok' => $data->nama_barang_dok,
                            'jml_masuk' => $data->jml_masuk
                        ];

                        $responseDetail = $this->penerimaanBarangDetailModel->insert($detailPayload);

                        if(!$responseDetail) {
                            $message =  'Data Gagal Disimpan';
                            $data = [
                                "status"            => false,
                                "message"    => $message,
                                "payload"   => $payload,
                                'token' => csrf_hash()
                            ];
                            echo json_encode($data);
                        }

                        // UPDATE REMAINING QTY AND JML DITERIMA
                        if($tipe_bahan === "BAKU")
                        {
                            $conditionRemain = [
                                'id' => $data->purchase_order_details_id
                            ];

                            $payloadRemain = [
                                'qty_diterima' => $data->qty_diterima,
                                'remaining_qty' => $data->remaining_qty
                            ];
            
                            $responseDet = $this->rmPurchaseOrderDetailModel->where($conditionRemain)->set($payloadRemain)->update();

                            if(!$responseDet) {
                                $message =  'Gagal Ubah Remaining';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                            }
                        }
                        if($tipe_bahan === "PENOLONG")
                        {
                            $conditionRemain = [
                                'id' => $data->purchase_order_details_id
                            ];

                            $payloadRemain = [
                                'qty_diterima' => $data->qty_diterima,
                                'remaining_qty' => $data->remaining_qty
                            ];
            
                            $responseDet = $this->amPurchaseOrderDetailModel->where($conditionRemain)->set($payloadRemain)->update();

                            if(!$responseDet) {
                                $message =  'Gagal Ubah Remaining';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                            }
                        }

                        // ADD STOK
                        // if($status_post === "FINISH")
                        // {
                            $find = $this->barangModel->find(formatter($data->barang_id, "STR_TO_INT"));

                            if($find)
                            {
                                $conditionUpdateStok = [
                                    'id' => formatter($data->barang_id, "STR_TO_INT")
                                ];
    
                                $payloadupdateStok = [
                                    'stok' => formatter($find["stok"], "STR_TO_INT") + formatter($data->jml_masuk, "STR_TO_INT")
                                ];
                
                                $responseStok = $this->barangModel->where($conditionUpdateStok)->set($payloadupdateStok)->update();    

                                if(!$responseStok) {
                                    $message =  'Gagal Tambah Stok';
                                    $data = [
                                        "status"            => false,
                                        "message"    => $message,
                                        "payload"   => $payload,
                                        'token' => csrf_hash()
                                    ];
                                    echo json_encode($data);
                                }
                            }
                        // }
                    }

                    $data = [
                        "id" => $response,
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        "response" => $response,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message =  'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
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

    public function updatePenerimaanBarangLokal()
    {
        try{
            $id = $this->request->getPost("id");
            $status_post = $this->request->getPost("status_post");
            $tipe_bahan = $this->request->getPost("tipe");
            $get = $this->penerimaanBarangModel->asObject()->find($id);

            if($get)
            {
                $multiple_po_id = json_decode($get->multiple_po_id);

                if($status_post === "FINISH")
                {
                    if($tipe_bahan === "BAKU")
                    {
                       
                        foreach($multiple_po_id as $po_id)
                        {
                            $conditionUpdate = [
                                'id' => $po_id
                            ];

                            $payloadupdate = [
                                'status_penerimaan' => 1
                            ];
            
                            $responseStatusPenerimaan = $this->rmPurchaseOrderModel->where($conditionUpdate)->set($payloadupdate)->update();
                            
                            if(!$responseStatusPenerimaan) {
                                $message =  'Data Ubah Status Penerimaan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => "",
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                            }
                        }
                    }
                    if($tipe_bahan === "PENOLONG")
                    {
                        foreach($multiple_po_id as $po_id)
                        {
                            $conditionUpdate = [
                                'id' => $po_id
                            ];

                            $payloadupdate = [
                                'status_penerimaan' => 1
                            ];
            
                            $responseStatusPenerimaan = $this->amPurchaseOrderModel->where($conditionUpdate)->set($payloadupdate)->update();
                        
                            if(!$responseStatusPenerimaan) {
                                $message =  'Data Ubah Status Penerimaan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => "",
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                            }
                        }
                    }

                    $condition = [
                        'id' => $id
                    ];
    
                    $response = $this->penerimaanBarangModel->where($condition)->set(['status_po' => 'CLOSED'])->update();

                    if($response)
                    {
                        $data = [
                            "id" => "",
                            "status"            => true,
                            "message"   => "Data Berhasil diubah",
                            "payload"   => "",
                            "response" => $response,
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    } else {
                        $message =  'Data Gagal Diubah';
                        $data = [
                            "status"            => false,
                            "message"    => $message,
                            "payload"   => "",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    }
                }
            }
            else
            {
                $data = [
                    "status"            => false,
                    "message"    => "Penerimaan Barang Tidak Ditemukan",
                    "payload"   => "",
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

    // public function updateStatusPenerimaanBarangLokal()
    // {
    //     try{
    //         $id = $this->request->getPost("id");

    //         $payload = [
    //             "status_post" => "FINISH"
    //         ];
            
    //         $condition = [
    //             'id' => $id
    //         ];

    //         $response = $this->penerimaanBarangModel->where($condition)->set($payload)->update();

    //         if ($response) {
    //             $data = [
    //                 "status"            => true,
    //                 "message"   => "Data Berhasil diposting",
    //                 "payload"   => $payload,
    //                 'token' => csrf_hash()
    //             ];
    //             echo json_encode($data);
    //         } else {
    //             $message = 'Data Gagal Diposting';
    //             $data = [
    //                 "status"            => false,
    //                 "message"    => $message,
    //                 "payload"   => $payload,
    //                 'token' => csrf_hash()
    //             ];
    //             echo json_encode($data);
    //         }
    //     }
    //     catch(\Exception $e)
    //     {
    //         $data = [
    //             "status"            => false,
    //             "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
    //             'token' => csrf_hash()
    //         ];
    //         echo json_encode($data);
    //     }
    //     return;
    // }

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