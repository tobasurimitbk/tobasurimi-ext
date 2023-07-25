<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

use App\Models\AMPurchaseOrderModel;
use App\Models\BarangModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\RMImportPOModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use App\Models\SatuanModel;
use Dompdf\Dompdf;

class PenerimaanBarangImport extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $barangModel;
    protected $metadataModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $rmImportPOModel;
    protected $supplierModel;
    protected $warehousesModel;
    protected $satuanModel;
    protected $dompdf;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->barangModel = new BarangModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuanModel();
        $this->dompdf = new Dompdf();
    }

    public function penerimaanBarangImport()
    {
        return view('Warehouse/penerimaanBarangImport/index');
    }

    public function createPenerimaanBarangImport()
    {
        //Get AJU
        $dataAJU = $this->metadataModel->get_by_name('jenis_dok_aju');

        //Get Supplier
        $dataSupplier = $this->supplierModel->getSupplierByKategoriAndType('IMPORT', 'BAHAN BAKU', $this->this_company_id);

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

        return view('Warehouse/penerimaanBarangImport/form', $data);
    }

    public function getByIdPenerimaanBarangImport($id = null)
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
                $status_penerimaan = $dataPenerimaanBarang->status_penerimaan;
                $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;

                if($status_penerimaan === "IMPORT")
                {
                    $data["dataPenerimaanBarang"] = $dataPenerimaanBarang;
                    $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "IMPORT");

                    // var_dump($dataPenerimaanBarangDetail);
                    // die;

                    if($dataPenerimaanBarangDetail)
                    {
                        $data["dataPenerimaanBarangDetail"] = $dataPenerimaanBarangDetail;
                    }

                    if($tipe_bahan === "BAKU")
                    {
                        $supplier_id = $dataPenerimaanBarang->supplier_id;

                        $dataNo = $this->rmImportPOModel->getNoPenerimaanBarang($supplier_id, $this->this_company_id);

                        //Get Supplier
                        $dataSupplier = $this->supplierModel->getSupplierByKategoriAndType('IMPORT', 'BAHAN BAKU', $this->this_company_id);

                        $data["dataNo"] = $dataNo;
                        $data["dataSupplier"] = $dataSupplier;
                    }
                    if($tipe_bahan === "PENOLONG")
                    {
                        $supplier_id = $dataPenerimaanBarang->supplier_id;

                        $dataNo = $this->amPurchaseOrderModel->getNoPenerimaanBarang("Import", $supplier_id, $this->this_company_id);

                        //Get Supplier
                        $dataSupplier = $this->supplierModel->getSupplierByKategoriAndType('IMPORT', 'BAHAN PENOLONG', $this->this_company_id);

                        $data["dataNo"] = $dataNo;
                        $data["dataSupplier"] = $dataSupplier;
                    }
                } 
            }
        }

        return view('Warehouse/penerimaanBarangImport/form', $data);
    }

    public function allPenerimaanBarangImport()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sorttype" => $this->request->getGet("sortType"),
            "statuspenerimaan" => "IMPORT",
            "status" => $this->request->getGet("status"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            "penerimaan_barang.company_id"        => $this->this_company_id,
            "status_penerimaan" => "IMPORT"
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

    public function savePenerimaanBarangImport()
    {
        try{
            $rules = [
                "no_penerimaan_barang" => [
                    "rules" => "required"
                ],
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
                    "packaging" => $this->request->getPost("packaging"),
                    "total_weight" => $this->request->getPost("total_weight"),
                    "shipping_cost" => formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT"),
                    "biaya_masuk" => formatter($this->request->getPost("biaya_masuk"), "CURR_TO_INT"),
                    "ppnbm" => formatter($this->request->getPost("ppnbm"), "CURR_TO_INT"),
                    "status_post" => $status_post,
                    "status_penerimaan" => "IMPORT",
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
            
                            $responseStatusPenerimaan = $this->rmImportPOModel->where($conditionUpdate)->set($payloadupdate)->update();

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
                }
                
                $response =  $this->penerimaanBarangModel->insert($payload);
                // $response =  '';

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $detailPayload = [
                            'purchase_order_details_id' => $data->purchase_order_details_id,
                            'penerimaan_barang_id' => $response,
                            'doc_qty' => $data->doc_qty,
                            'selisih' => $data->selisih,
                            'konversi' => $data->konversi,
                            'harga' => $data->harga,
                            'penyerahan' => $data->penyerahan,
                            'keterangan' => $data->keterangan,
                            'warehouse' => $data->warehouse,
                            'barang_id' => $data->barang_id,
                            'qty' => $data->qty,
                            'pph' => $data->ppn,
                            'ppn' => $data->pph,
                            'unit' => $data->unit,
                            'nama_barang_dok' => $data->nama_barang_dok,
                            'jml_masuk' => $data->jml_masuk
                        ];

                        // $data = [
                        //     "status"            => false,
                        //     "message"    => $detailPayload,
                        //     "payload"   => $detailPayload,
                        //     'token' => csrf_hash()
                        // ];
                        // echo json_encode($data);

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

                        // ADD STOK
                        if($status_post === "FINISH")
                        {
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
                        }
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

    public function updatePenerimaanBarangImport()
    {
        try{
            $rules = [
                "no_penerimaan_barang" => [
                    "rules" => "required"
                ],
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
                $no = $this->penerimaanBarangModel->get_no(date('d'), date('m'), date('Y'), $last_day);
                $status_post = $this->request->getPost("status_post");
                $tipe_bahan = $this->request->getPost("tipe_bahan");
                $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");

                $payload = [
                    "company_id" => $this->this_company_id,
                    "no_penerimaan_barang" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("no_penerimaan_barang"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
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
                    "packaging" => $this->request->getPost("packaging"),
                    "total_weight" => $this->request->getPost("total_weight"),
                    "shipping_cost" => formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT"),
                    "biaya_masuk" => formatter($this->request->getPost("biaya_masuk"), "CURR_TO_INT"),
                    "ppnbm" => formatter($this->request->getPost("ppnbm"), "CURR_TO_INT"),
                    "status_post" => $status_post,
                    "status_penerimaan" => "IMPORT",
                ];

                $items = json_decode($this->request->getPost("items"));

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
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
            
                            $responseStatusPenerimaan = $this->rmImportPOModel->where($conditionUpdate)->set($payloadupdate)->update();
                            
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
                }
                
                $condition = [
                    'id' => $id
                ];

                $response = $this->penerimaanBarangModel->where($condition)->set($payload)->update();
                // $response = '';

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $detailPayload = [
                            'purchase_order_details_id' => $data->purchase_order_details_id,
                            'penerimaan_barang_id' => $id,
                            'doc_qty' => $data->doc_qty,
                            'selisih' => $data->selisih,
                            'konversi' => $data->konversi,
                            'harga' => $data->harga,
                            'penyerahan' => $data->penyerahan,
                            'keterangan' => $data->keterangan,
                            'warehouse' => $data->warehouse,
                            'barang_id' => $data->barang_id,
                            'qty' => $data->qty,
                            'pph' => $data->ppn,
                            'ppn' => $data->pph,
                            'unit' => $data->unit,
                            'nama_barang_dok' => $data->nama_barang_dok,
                            'jml_masuk' => $data->jml_masuk
                        ];

                        // $data = [
                        //     "status"            => false,
                        //     "message"    => $detailPayload,
                        //     "payload"   => $detailPayload,
                        //     'token' => csrf_hash()
                        // ];
                        // echo json_encode($data);

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
                                $message =  'Data Gagal Diubah';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                            }

                            // ADD STOK
                            if($status_post === "FINISH")
                            {
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
                            }
                        }

                        // kalau create
                        else
                        {
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

    public function print($id = null) 
    {
        if($id)
        {
            $filename = "Penerimaan Barang Import";

            $data = [];
            $dataPenerimaanBarang = $this->penerimaanBarangModel->getById($id);

            if($dataPenerimaanBarang)
            {
                $status_penerimaan = $dataPenerimaanBarang->status_penerimaan;
                $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;

                if($status_penerimaan === "IMPORT")
                {
                    $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "IMPORT");

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
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangImport/print', $data));

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

       

    public function deletePenerimaanBarangImport()
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

    public function dropdownPenerimaanBarangImport()
    {
        $payload = [
            "idsupplier" => $this->request->getGet("id"),
            "statuspenerimaan" => "IMPORT",
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