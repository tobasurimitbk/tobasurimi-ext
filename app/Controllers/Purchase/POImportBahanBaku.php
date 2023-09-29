<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\MetadataModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\SppModel;
use App\Models\SupplierModel;
use App\Models\BeaCukaiModel;
use Dompdf\Dompdf;

class POImportBahanBaku extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $user_id;
    protected $barangModel;
    protected $metadataModel;
    protected $rmImportPOModel;
    protected $rmImportPODetailModel;
    protected $sppModel;
    protected $supplierModel;
    protected $beaCukaiModel;
    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->user_id = session()->get("login")->user_id;
        $this->barangModel = new BarangModel();
        $this->metadataModel = new MetadataModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();
        $this->sppModel = new SppModel();
        $this->supplierModel = new SupplierModel();
        $this->beaCukaiModel = new BeaCukaiModel();
        $this->dompdf = new Dompdf();
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

        //Get Valuta By Metadata
        $dataValuta = $this->metadataModel->get_by_name('Valuta');
        
        $data = [
            "today" => date("d/m/Y"),
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

        //Get Valuta By Metadata
        $dataValuta = $this->metadataModel->get_by_name('Valuta');
        
        $data = [
            "today" => date("d/m/Y"),
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta
        ];

        if (!empty($id)) {
            $dataPOImport = $this->rmImportPOModel->getPOById($id);
            $data["dataPOImport"] = $dataPOImport;

            $dataPOImportDetail = $this->rmImportPODetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

            if($dataPOImportDetail)
            {
                $data["dataPOImportDetail"] = $dataPOImportDetail;
            }

            // var_dump($dataPOImport);
            // die;
        }

        return view('Purchase/poImportBahanBaku/form', $data);
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
                "purchase_request_id" => $data->purchase_request_id,
                "po_no"         => $data->po_no,
                "supplierName"  => $data->supplierName,
                "total"         => number_format($data->total),
                "currencyName"  => $data->currencyName,
                "itemCount"     => $data->itemCount,
                "is_posted"     => $data->is_posted,
                "status_penerimaan" => $data->status_penerimaan === "0" ? "OPEN" : "CLOSED",
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
                    "rules" => "required",
                    'errors' => [
                        'required' => 'SPP tidak boleh kosong'
                    ]
                ],
                "po_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. PO tidak boleh kosong'
                    ]
                ],
                "po_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal tidak boleh kosong'
                    ]
                ],
                "supplier_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Supplier tidak boleh kosong'
                    ]
                ],
                "payment_term" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Termin Pembayaran tidak boleh kosong'
                    ]
                ],
                "currency" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Valas tidak boleh kosong'
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
                $divisi_id = formatter($this->request->getPost("divisi_id"), "STR_TO_INT");
                $divisi = $this->request->getPost("divisi");
                $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                $no = $this->rmImportPOModel->get_no(date('d'), date('m'), date('Y'), $divisi, date('y'), $divisi_id, $last_day);
                $purchase_request_id = formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT");
                
                $payload = [
                    "company_id" => formatter($this->this_company_id, "STR_TO_INT"),
                    "purchase_request_id" => $purchase_request_id,
                    "po_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("po_no"),
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "divisi_id" => $divisi_id,
                    "currency" => formatter($this->request->getPost("currency"), "STR_TO_INT"),
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "total" => $this->request->getPost("total"),
                    "payment_term" => $this->request->getPost("payment_term"),
                    "note" => $this->request->getPost("note"),
                    "createdBy" => $this->user_id,
                    "is_posted" => 0,
                ];

                $items = json_decode($this->request->getPost("items"));

                // spp number cannot be used again
                $responsespp = $this->sppModel->where(['id' => $purchase_request_id])->set(['request_status' => 'finished'])->update();

                if(!$responsespp)
                {
                    $data = [
                        "status"            => false,
                        "message"    => "No. SPP gagal di close",
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }
                
                $response =  $this->rmImportPOModel->insert($payload);

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $barang_id = $data->item_id;
                        // buat barang baru jika id kosong
                        if(!$barang_id)
                        {
                            // $payloadBarang = [
                            //     "company_id" => $this->this_company_id,
                            //     "kode_barang" => $data->item_code,
                            //     "nama_barang" => $data->item_name,
                            //     "harga_barang" => $data->price,
                            //     "satuan_id" => $data->unit,
                            //     "kategori_id" => 26,
                            //     "hs_id" => 1,
                            //     "ap_id" => 1,
                            //     "ar_id" => 1,
                            //     "stok" => 0,
                            //     "status" => "Aktif",
                            //     "spek" => "[]"
                            // ];
            
                            // $responseBarang =  $this->barangModel->insert($payloadBarang);

                            // $barang_id = $responseBarang;

                            // if(!$responseBarang) {
                            //     $message =  'Data Gagal Disimpan';
                            //     $data = [
                            //         "status"            => false,
                            //         "message"    => $message,
                            //         "payload"   => $payload,
                            //         'token' => csrf_hash()
                            //     ];
                            //     echo json_encode($data);
                            // }
                        }

                        $detailPayload = [
                            'rm_import_po_id' => $response,
                            'purchase_request_detail_id' => $data->purchase_request_detail_id,
                            'barang_id' =>$barang_id,
                            'spec' => $data->spec,
                            'note' => $data->note,
                            'unit' => $data->unit,
                            'qty' => $data->qty,
                            'remaining_qty' => $data->qty,
                            'qty_diterima' => 0,
                            'price' => $data->price,
                            'disc' => $data->disc,
                            'additional_cost' => $data->additional_cost
                        ];

                        // $data = [
                        //     "status"            => false,
                        //     "message"    => $detailPayload,
                        //     "payload"   => $detailPayload,
                        //     'token' => csrf_hash()
                        // ];
                        // echo json_encode($data);

                        $responseDetail = $this->rmImportPODetailModel->insert($detailPayload);

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

    public function updatePOImportBahanBaku()
    {
        try{
            $rules = [
                "po_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. PO tidak boleh kosong'
                    ]
                ],
                "po_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal tidak boleh kosong'
                    ]
                ],
                "supplier_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Supplier tidak boleh kosong'
                    ]
                ],
                "payment_term" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Termin Pembayaran tidak boleh kosong'
                    ]
                ],
                "currency" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Valas tidak boleh kosong'
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
                $divisi_id = formatter($this->request->getPost("divisi_id"), "STR_TO_INT");
                $divisi_name = $this->request->getPost("divisi_name");
                $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                $no = $this->rmImportPOModel->get_no(date('d'), date('m'), date('Y'), $divisi_name, date('y'), $divisi_id, $last_day);

                $payload = [
                    "company_id" => formatter($this->this_company_id, "STR_TO_INT"),
                    "po_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("po_no"),
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "divisi_id" => $divisi_id,
                    "supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "payment_term" => $this->request->getPost("payment_term"),
                    "currency" => formatter($this->request->getPost("currency"), "STR_TO_INT"),
                    "note" => $this->request->getPost("note"),
                    "createdBy" => $this->user_id,
                    "total" => $this->request->getPost("total")
                ];

                $items = json_decode($this->request->getPost("items"));

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);

                $condition = [
                    'id' => $id
                ];

                $response = $this->rmImportPOModel->where($condition)->set($payload)->update();

                if ($response) {
                    foreach($items as $data)
                    {
                        $barang_id = $data->item_id;
                        // buat barang baru jika id kosong
                        if(!$barang_id)
                        {
                            // $payloadBarang = [
                            //     "company_id" => $this->this_company_id,
                            //     "kode_barang" => $data->item_code,
                            //     "nama_barang" => $data->item_name,
                            //     "harga_barang" => $data->price,
                            //     "satuan_id" => $data->unit,
                            //     "kategori_id" => 26,
                            //     "hs_id" => 1,
                            //     "ap_id" => 1,
                            //     "ar_id" => 1,
                            //     "stok" => 0,
                            //     "status" => "Aktif",
                            //     "spek" => "[]"
                            // ];
            
                            // $responseBarang =  $this->barangModel->insert($payloadBarang);

                            // $barang_id = $responseBarang;

                            // if(!$responseBarang) {
                            //     $message =  'Data Gagal Disimpan';
                            //     $data = [
                            //         "status"            => false,
                            //         "message"    => $message,
                            //         "payload"   => $payload,
                            //         'token' => csrf_hash()
                            //     ];
                            //     echo json_encode($data);
                            // }
                        }

                        $detailPayload = [];

                        $detailPayload = [
                            'rm_import_po_id' => $id,
                            // 'barang_id' =>$barang_id,
                            // 'spec' => $data->spec,
                            // 'note' => $data->note,
                            // 'unit' => $data->unit,
                            // 'qty' => $data->qty,
                            // 'remaining_qty' => $data->qty,
                            // 'qty_diterima' => 0,
                            // 'price' => $data->price,
                            'disc' => $data->disc,
                            'additional_cost' => $data->additional_cost
                        ];

                        // $data = [
                        //     "status"            => false,
                        //     "message"    => $detailPayload,
                        //     "payload"   => $detailPayload,
                        //     'token' => csrf_hash()
                        // ];
                        // echo json_encode($data);

                        // kalau hapus
                        if($data->isDeleted)
                        {
                            $responseDetail = $this->rmImportPODetailModel->delete($data->id);

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

                            $responseDetail = $this->rmImportPODetailModel->where($conditionDetail)->set($detailPayload)->update();

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
                            $responseDetail = $this->rmImportPODetailModel->insert($detailPayload);

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

    public function updateStatusPOImportBahanBaku()
    {
        try{
            $id = $this->request->getPost("id");
            $spp = $this->request->getPost("spp");

            // spp close
            $responsespp = $this->sppModel->where(['id' => $spp])->set(['is_posted' => 1])->update();

            if(!$responsespp)
            {
                $data = [
                    "status"            => false,
                    "message"    => "Gagal close SPP",
                    "payload"   => "",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            // po posting

            $payload = [
                "is_posted" => 1
            ];
            
            $condition = [
                'id' => $id
            ];

            $response = $this->rmImportPOModel->where($condition)->set($payload)->update();

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diposting",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Diposting';
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

    public function closePOImportBahanBaku()
    {
        try{
            $id = $this->request->getPost("id");
            
            $payload = [
                "status_penerimaan" => 1
            ];
            
            $condition = [
                'id' => $id
            ];

            $response = $this->rmImportPOModel->where($condition)->set($payload)->update();

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => "PO Berhasil di Close",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = 'PO Gagal di Close';
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
                $findBarang = $this->rmImportPOModel->find($id);
                if ($findBarang) {
                    $response =  $this->rmImportPOModel->delete($id);
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
            $filename = "PO Import Bahan Baku";

            $data = [];
            $dataPO = $this->rmImportPOModel->getPOById($id);

            if($dataPO)
            {
                $dataPODetail = $this->rmImportPODetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

                if($dataPODetail)
                {
                    $data["dataPO"] = $dataPO;
                    $data["dataPODetail"] = $dataPODetail;
                }
            }

            // var_dump($dataPODetail);
            // die;

            // load HTML content
            $this->dompdf->loadHtml(view('Purchase/poImportBahanBaku/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'portrait');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Purchase/poImportBahanBaku/print', $data);
        }
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

    public function purchaseOrderPaymentDropdown($id)
    {
        $condition = [
            'company_id'    => $this->this_company_id,
            'supplier_id'   => $id,
            'is_posted'     => 1
        ];

        $selectQry = "rm_import_pos.*,
                      metadata.value AS currency";
        $dataPOImport = $this->rmImportPOModel->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'metadata.id = rm_import_pos.currency')
            ->findAll();

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