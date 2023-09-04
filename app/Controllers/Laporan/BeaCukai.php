<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\BeaCukaiModel;
use App\Models\BeaCukaiDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderModel;

class BeaCukai extends BaseController
{
    protected $this_company_id;
    protected $user_id;
    protected $MetadataModel;
    protected $BeaCukaiModel;
    protected $BeaCukaiDetailModel;
    protected $AMPurchaseOrderModel;
    protected $RMPurchaseOrderModel;
    protected $RMImportPOModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->user_id = session()->get("login")->user_id;
        $this->MetadataModel = new MetadataModel();
        $this->BeaCukaiModel = new BeaCukaiModel();
        $this->BeaCukaiDetailModel = new BeaCukaiDetailModel();
        $this->AMPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RMImportPOModel = new RMImportPOModel();
    }

    public function index()
    {
        return view('Laporan/BeaCukai/index');
    }

    public function createView()
    {
        //Get AJU
        $dataAJU = $this->MetadataModel->get_by_name('jenis_dok_aju');

        $data = [
            'dataAJU' => $dataAJU,
            'dropdownPO' => []
        ];
        
        return view('Laporan/BeaCukai/form', $data);
    }

    public function getById($id = null)
    {
        //Get AJU
        $dataAJU = $this->MetadataModel->get_by_name('jenis_dok_aju');

        $data = [
            'dataAJU' => $dataAJU
        ];

        if (!empty($id)) {
            $dataBeaCukai = $this->BeaCukaiModel->asObject()->find($id);
            $data["dataBeaCukai"] = $dataBeaCukai;
            $dropdownPO = [];
            if($dataBeaCukai)
            {
                if($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "LOKAL BAKU")
                {
                    $dropdownPO = $this->RMPurchaseOrderModel->getNoPOBeaCukai($this->this_company_id);
                }
                if($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "LOKAL PENOLONG")
                {
                    $dropdownPO = $this->AMPurchaseOrderModel->getNoPOBeaCukai("LOKAL", $this->this_company_id);
                }
                if($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "IMPORT BAKU")
                {
                    $dropdownPO = $this->RMImportPOModel->getNoPOBeaCukai($this->this_company_id);
                }
                if($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "IMPORT PENOLONG")
                {
                    $dropdownPO = $this->AMPurchaseOrderModel->getNoPOBeaCukai("IMPORT", $this->this_company_id);
                }
            }

            $data["dropdownPO"] = $dropdownPO;

            $dataBeaCukaiDetail = $this->BeaCukaiDetailModel->getBeaCukaiDetailByBeaCukaiId($id);

            if($dataBeaCukaiDetail)
            {
                $data["dataBeaCukaiDetail"] = $dataBeaCukaiDetail;
            }
        }

        return view('Laporan/BeaCukai/form', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "status" => $this->request->getGet("status")
        ];

        $condition = [
            "company_id"        => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status"      => $this->request->getGet("status"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $beaData = $this->BeaCukaiModel->getList($condition, $addCondition, $limit, $offset);

        $dataBea = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaData['data'] as $data) {
            array_push($dataBea, [
                "no"            => $no++,
                "id"            => $data->id,
                "no_bea_cukai" => $data->no_bea_cukai,
                "tipe_bahan" => $data->tipe_bahan,
                "status_po" => $data->status_po,
                "po_no" => $data->tipe_bahan ==="BAKU" ? implode(", ",json_decode($data->multiple_po_no)) : $data->po_no,
                "createdAt" => $data->createdAt ? date("d/m/Y", strtotime($data->createdAt)) : "",
                "status_post" => $data->status_post,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaData['totalData'],
            "recordsFiltered"   => $beaData['totalFilteredData'],
            "data"              => $dataBea,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        try{
            $rules = [
                "aju_document_type" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Jenis Dokumen tidak boleh kosong'
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
                "bea_masuk" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Bea Masuk tidak boleh kosong'
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
                // $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");
                $po_type = $this->request->getPost("po_type");
                $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                $no = $this->BeaCukaiModel->get_no(date('d'), date('m'), date('Y'), date('y'), $last_day);

                if($po_type === "LOKAL BAKU")
                {
                    $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");

                    $payload = [
                        "company_id" => $this->this_company_id,
                        "no_bea_cukai" => $no,
                        "user_id" => $this->user_id,
                        "multiple_po_id" => json_encode($multiple_po_id),
                        "multiple_po_no" => $this->request->getPost("multiple_po_no"),
                        "tipe_bahan" => "BAKU",
                        "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                        "aju_no" => $this->request->getPost("aju_no"),
                        "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                        "invoice_no" => $this->request->getPost("invoice_no"),
                        "shipping_cost" => $this->request->getPost("shipping_cost") ? formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT") : 0,
                        "bea_masuk" => formatter($this->request->getPost("bea_masuk"), "CURR_TO_INT"),
                        "ppn" => $this->request->getPost("ppn"),
                        "pph" => $this->request->getPost("pph"),
                        "status_post" => "WAITING",
                        "status_po" => "LOKAL"
                    ];

                    // validate already exist
                    $check = $this->BeaCukaiModel->checkPostingBeaCukai($this->this_company_id);

                    if($check)
                    {
                        foreach($check as $item)
                        {
                            if($item["multiple_po_no"])
                            {
                                $no = json_decode($item["multiple_po_no"]);
                                $list = json_decode($this->request->getPost("multiple_po_no"));
                                foreach($no as $secondItem)
                                {
                                    foreach($list as $thirdItem)
                                    {
                                        if($thirdItem === $secondItem)
                                        {
                                            $data = [
                                                "status"            => false,
                                                "message"    => $thirdItem . " Sudah terdaftar di bea cukai",
                                                "payload"   => $payload,
                                                'token' => csrf_hash()
                                            ];
                                            echo json_encode($data);
                                            return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if($po_type === "LOKAL PENOLONG")
                {
                    $payload = [
                        "company_id" => $this->this_company_id,
                        "no_bea_cukai" => $no,
                        "user_id" => $this->user_id,"supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                        // "multiple_po_id" => json_encode($multiple_po_id),
                        // "multiple_po_no" => $this->request->getPost("multiple_po_no"),
                        "po_id" => formatter($this->request->getPost("po_id"), "STR_TO_INT"),
                        "po_no" => $this->request->getPost("po_no"),
                        "tipe_bahan" => "PENOLONG",
                        "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                        "aju_no" => $this->request->getPost("aju_no"),
                        "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                        "invoice_no" => $this->request->getPost("invoice_no"),
                        "shipping_cost" => $this->request->getPost("shipping_cost") ? formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT") : 0,
                        "bea_masuk" => formatter($this->request->getPost("bea_masuk"), "CURR_TO_INT"),
                        "ppn" => $this->request->getPost("ppn"),
                        "pph" => $this->request->getPost("pph"),
                        "status_post" => "WAITING",
                        "status_po" => "LOKAL"
                    ];

                    // validate already exist
                    $check = $this->BeaCukaiModel->checkPostingBeaCukai($this->this_company_id);

                    if($check)
                    {
                        foreach($check as $item)
                        {
                            if($item["po_no"] === $this->request->getPost("po_no"))
                            {
                                $data = [
                                    "status"            => false,
                                    "message"    => $item["po_no"] . " Sudah terdaftar di bea cukai",
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }

                if($po_type === "IMPORT BAKU")
                {
                    $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");

                    $payload = [
                        "company_id" => $this->this_company_id,
                        "no_bea_cukai" => $no,
                        "user_id" => $this->user_id,
                        "multiple_po_id" => json_encode($multiple_po_id),
                        "multiple_po_no" => $this->request->getPost("multiple_po_no"),
                        "tipe_bahan" => "BAKU",
                        "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                        "aju_no" => $this->request->getPost("aju_no"),
                        "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                        "invoice_no" => $this->request->getPost("invoice_no"),
                        "shipping_cost" => $this->request->getPost("shipping_cost") ? formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT") : 0,
                        "bea_masuk" => formatter($this->request->getPost("bea_masuk"), "CURR_TO_INT"),
                        "ppn" => $this->request->getPost("ppn"),
                        "pph" => $this->request->getPost("pph"),
                        "status_post" => "WAITING",
                        "status_po" => "IMPORT"
                    ];

                    // validate already exist
                    $check = $this->BeaCukaiModel->checkPostingBeaCukai($this->this_company_id);

                    if($check)
                    {
                        foreach($check as $item)
                        {
                            if($item["multiple_po_no"])
                            {
                                $no = json_decode($item["multiple_po_no"]);
                                $list = json_decode($this->request->getPost("multiple_po_no"));
                                foreach($no as $secondItem)
                                {
                                    foreach($list as $thirdItem)
                                    {
                                        if($thirdItem === $secondItem)
                                        {
                                            $data = [
                                                "status"            => false,
                                                "message"    => $thirdItem . " Sudah terdaftar di bea cukai",
                                                "payload"   => $payload,
                                                'token' => csrf_hash()
                                            ];
                                            echo json_encode($data);
                                            return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if($po_type === "IMPORT PENOLONG")
                {
                    $payload = [
                        "company_id" => $this->this_company_id,
                        "no_bea_cukai" => $no,
                        "user_id" => $this->user_id,
                        "po_id" => formatter($this->request->getPost("po_id"), "STR_TO_INT"),
                        "po_no" => $this->request->getPost("po_no"),
                        "tipe_bahan" => "PENOLONG",
                        "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                        "aju_no" => $this->request->getPost("aju_no"),
                        "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                        "invoice_no" => $this->request->getPost("invoice_no"),
                        "shipping_cost" => $this->request->getPost("shipping_cost") ? formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT") : 0,
                        "bea_masuk" => formatter($this->request->getPost("bea_masuk"), "CURR_TO_INT"),
                        "ppn" => $this->request->getPost("ppn"),
                        "pph" => $this->request->getPost("pph"),
                        "status_post" => "WAITING",
                        "status_po" => "IMPORT"
                    ];

                    // validate already exist
                    $check = $this->BeaCukaiModel->checkPostingBeaCukai($this->this_company_id);

                    if($check)
                    {
                        foreach($check as $item)
                        {
                            if($item["po_no"] === $this->request->getPost("po_no"))
                            {
                                $data = [
                                    "status"            => false,
                                    "message"    => $item["po_no"] . " Sudah terdaftar di bea cukai",
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }

                $items = json_decode($this->request->getPost("items"));

                // $data = [
                //     "status"            => false,
                //     "message"    => json_encode($payload),
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);
                // return;
                
                $response =  $this->BeaCukaiModel->insert($payload);

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $detailPayload = [
                            'bea_cukai_id' => $response,
                            'barang_id' =>$data->barang_id,
                            'po_no' => $data->po_no,
                            'spec' => $data->spec,
                            'note' => $data->note,
                            'unit' => $data->unit,
                            'qty' => $data->qty,
                            'price' => $data->price,
                            'disc' => $data->disc,
                            'additional_cost' => $data->additional_cost,
                            'berat' => $data->berat,
                            'total_price' => $data->total_price
                        ];

                        $responseDetail = $this->BeaCukaiDetailModel->insert($detailPayload);

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

    public function update()
    {
        try{
            $rules = [
                "aju_document_type" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Jenis Dokumen tidak boleh kosong'
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
                "bea_masuk" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Bea Masuk tidak boleh kosong'
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
                // $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");
                $po_type = $this->request->getPost("po_type");

                $dataBeaCukai = $this->BeaCukaiModel->asObject()->find($id);

                if($po_type === "LOKAL BAKU")
                {
                    $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");

                    $payload = [
                        "company_id" => $this->this_company_id,
                        "user_id" => $this->user_id,
                        "multiple_po_id" => json_encode($multiple_po_id),
                        "multiple_po_no" => $this->request->getPost("multiple_po_no"),
                        "tipe_bahan" => "BAKU",
                        "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                        "aju_no" => $this->request->getPost("aju_no"),
                        "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                        "invoice_no" => $this->request->getPost("invoice_no"),
                        "shipping_cost" => $this->request->getPost("shipping_cost") ? formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT") : 0,
                        "bea_masuk" => formatter($this->request->getPost("bea_masuk"), "CURR_TO_INT"),
                        "ppn" => $this->request->getPost("ppn"),
                        "pph" => $this->request->getPost("pph"),
                    ];

                    // validate already exist
                    $check = $this->BeaCukaiModel->checkPostingBeaCukai($this->this_company_id);

                    if($check)
                    {
                        foreach($check as $item)
                        {
                            if($item["multiple_po_no"])
                            {
                                $default = json_decode($dataBeaCukai->multiple_po_no);
                                $no = json_decode($item["multiple_po_no"]);
                                $list = json_decode($this->request->getPost("multiple_po_no"));

                                foreach($no as $secondItem)
                                {
                                    foreach($list as $thirdItem)
                                    {
                                        if($thirdItem === $secondItem)
                                        {
                                            foreach($default as $fourItem)
                                            {
                                                if($thirdItem !== $fourItem)
                                                {
                                                    $data = [
                                                        "status"            => false,
                                                        "message"    => $thirdItem . " Sudah terdaftar di bea cukai",
                                                        "payload"   => $payload,
                                                        'token' => csrf_hash()
                                                    ];
                                                    echo json_encode($data);
                                                    return;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if($po_type === "LOKAL PENOLONG")
                {
                    $payload = [
                        "company_id" => $this->this_company_id,
                        "user_id" => $this->user_id,"supplier_id" => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                        // "multiple_po_id" => json_encode($multiple_po_id),
                        // "multiple_po_no" => $this->request->getPost("multiple_po_no"),
                        "po_id" => formatter($this->request->getPost("po_id"), "STR_TO_INT"),
                        "po_no" => $this->request->getPost("po_no"),
                        "tipe_bahan" => "PENOLONG",
                        "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                        "aju_no" => $this->request->getPost("aju_no"),
                        "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                        "invoice_no" => $this->request->getPost("invoice_no"),
                        "shipping_cost" => $this->request->getPost("shipping_cost") ? formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT") : 0,
                        "bea_masuk" => formatter($this->request->getPost("bea_masuk"), "CURR_TO_INT"),
                        "ppn" => $this->request->getPost("ppn"),
                        "pph" => $this->request->getPost("pph"),
                    ];

                    // validate already exist
                    $check = $this->BeaCukaiModel->checkPostingBeaCukai($this->this_company_id);

                    if($check)
                    {
                        foreach($check as $item)
                        {
                            if($dataBeaCukai->po_no !== $item["po_no"])
                            {
                                if($item["po_no"] === $this->request->getPost("po_no"))
                                {
                                    $data = [
                                        "status"            => false,
                                        "message"    => $item["po_no"] . " Sudah terdaftar di bea cukai",
                                        "payload"   => $payload,
                                        'token' => csrf_hash()
                                    ];
                                    echo json_encode($data);
                                    return;
                                }
                            }
                        }
                    }
                }

                if($po_type === "IMPORT BAKU")
                {
                    $multiple_po_id = formatter(json_decode($this->request->getPost("multiple_po_id")), "ARR_TO_INT");

                    $payload = [
                        "company_id" => $this->this_company_id,
                        "user_id" => $this->user_id,
                        "multiple_po_id" => json_encode($multiple_po_id),
                        "multiple_po_no" => $this->request->getPost("multiple_po_no"),
                        "tipe_bahan" => "BAKU",
                        "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                        "aju_no" => $this->request->getPost("aju_no"),
                        "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                        "invoice_no" => $this->request->getPost("invoice_no"),
                        "shipping_cost" => $this->request->getPost("shipping_cost") ? formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT") : 0,
                        "bea_masuk" => formatter($this->request->getPost("bea_masuk"), "CURR_TO_INT"),
                        "ppn" => $this->request->getPost("ppn"),
                        "pph" => $this->request->getPost("pph"),
                    ];

                    // validate already exist
                    $check = $this->BeaCukaiModel->checkPostingBeaCukai($this->this_company_id);

                    if($check)
                    {
                        foreach($check as $item)
                        {
                            if($item["multiple_po_no"])
                            {
                                $default = json_decode($dataBeaCukai->multiple_po_no);
                                $no = json_decode($item["multiple_po_no"]);
                                $list = json_decode($this->request->getPost("multiple_po_no"));

                                foreach($no as $secondItem)
                                {
                                    foreach($list as $thirdItem)
                                    {
                                        if($thirdItem === $secondItem)
                                        {
                                            foreach($default as $fourItem)
                                            {
                                                if($thirdItem !== $fourItem)
                                                {
                                                    $data = [
                                                        "status"            => false,
                                                        "message"    => $thirdItem . " Sudah terdaftar di bea cukai",
                                                        "payload"   => $payload,
                                                        'token' => csrf_hash()
                                                    ];
                                                    echo json_encode($data);
                                                    return;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if($po_type === "IMPORT PENOLONG")
                {
                    $payload = [
                        "company_id" => $this->this_company_id,
                        "user_id" => $this->user_id,
                        "po_id" => formatter($this->request->getPost("po_id"), "STR_TO_INT"),
                        "po_no" => $this->request->getPost("po_no"),
                        "tipe_bahan" => "PENOLONG",
                        "aju_document_type" => formatter($this->request->getPost("aju_document_type"), "STR_TO_INT"),
                        "aju_no" => $this->request->getPost("aju_no"),
                        "validation_date" => $this->request->getPost("validation_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("validation_date")))) : "",
                        "invoice_no" => $this->request->getPost("invoice_no"),
                        "shipping_cost" => $this->request->getPost("shipping_cost") ? formatter($this->request->getPost("shipping_cost"), "CURR_TO_INT") : 0,
                        "bea_masuk" => formatter($this->request->getPost("bea_masuk"), "CURR_TO_INT"),
                        "ppn" => $this->request->getPost("ppn"),
                        "pph" => $this->request->getPost("pph"),
                    ];

                    // validate already exist
                    $check = $this->BeaCukaiModel->checkPostingBeaCukai($this->this_company_id);

                    if($check)
                    {
                        foreach($check as $item)
                        {
                            if($dataBeaCukai->po_no !== $item["po_no"])
                            {
                                if($item["po_no"] === $this->request->getPost("po_no"))
                                {
                                    $data = [
                                        "status"            => false,
                                        "message"    => $item["po_no"] . " Sudah terdaftar di bea cukai",
                                        "payload"   => $payload,
                                        'token' => csrf_hash()
                                    ];
                                    echo json_encode($data);
                                    return;
                                }
                            }
                        }
                    }
                }

                $items = json_decode($this->request->getPost("items"));

                // $data = [
                //     "status"            => false,
                //     "message"    => json_encode($items),
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);
                // return;
                
                $response =  $this->BeaCukaiModel->where(['id' => $id])->set($payload)->update();

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $detailPayload = [
                            'bea_cukai_id' => $id,
                            'barang_id' =>$data->barang_id,
                            'po_no' => $data->po_no,
                            'spec' => $data->spec,
                            'note' => $data->note,
                            'unit' => $data->unit,
                            'qty' => $data->qty,
                            'price' => $data->price,
                            'disc' => $data->disc,
                            'additional_cost' => $data->additional_cost,
                            'berat' => $data->berat,
                            'total_price' => $data->total_price
                        ];

                        // kalau hapus
                        if($data->isDeleted)
                        {
                            $responseDetail = $this->BeaCukaiDetailModel->delete($data->id);

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

                            $responseDetail = $this->BeaCukaiDetailModel->where($conditionDetail)->set($detailPayload)->update();

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
                            $responseDetail = $this->BeaCukaiDetailModel->insert($detailPayload);

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

    public function updateStatus()
    {
        try{
            $id = $this->request->getPost("id");

            $response = $this->BeaCukaiModel->where(['id' => $id])->set(['status_post' => 'FINISH'])->update();

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diposting",
                    "payload"   => "",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Diposting';
                $data = [
                    "status"            => false,
                    "message"    => $message,
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

    public function delete()
    {
        try{
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $find = $this->BeaCukaiModel->find($id);
                if ($find) {
                    $response =  $this->BeaCukaiModel->delete($id);
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
}