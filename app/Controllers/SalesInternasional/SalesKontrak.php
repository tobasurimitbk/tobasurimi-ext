<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use Config\Services;

use App\Models\CustomerModel;
use App\Models\SalesKontrakModel;
use App\Models\SalesKontrakDetailModel;
use Dompdf\Dompdf;

class SalesKontrak extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $customerModel;
    protected $salesKontrakModel;
    protected $salesKontrakDetailModel;
    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->customerModel = new CustomerModel();
        $this->salesKontrakModel = new SalesKontrakModel();
        $this->salesKontrakDetailModel = new SalesKontrakDetailModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('SalesInternasional/SalesKontrak/index');
    }

    public function createView()
    {
        //Get Buyer From Customer
        $dataCustomer = $this->customerModel->getCustomer();
        
        $data = [
            "dataCustomer" => $dataCustomer
        ];

        return view('SalesInternasional/SalesKontrak/form', $data);
    }

    public function getById($id = null)
    {
        //Get Buyer From Customer
        $dataCustomer = $this->customerModel->getCustomer();
        
        $data = [
            "dataCustomer" => $dataCustomer
        ];

        if (!empty($id)) {
            $dataSO = $this->salesKontrakModel->getById($id);
            $data["dataSO"] = $dataSO;

            $dataSODetail = $this->salesKontrakDetailModel->getSalesContractDetailBySalesContractId($id);

            if($dataSODetail)
            {
                $data["dataSODetail"] = $dataSODetail;
            }

            // var_dump($dataPOImport);
            // die;
        }

        return view('SalesInternasional/SalesKontrak/form', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "status"      => $this->request->getGet("status")
        ];

        $condition = [
            "sales_contract.company_id"    => $this->this_company_id
        ];
        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status"      => $this->request->getGet("status")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesKontrakData = $this->salesKontrakModel->getList($condition, $addCondition, $limit, $offset);

        $dataSalesKontrak = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesKontrakData['data'] as $data) {
            array_push($dataSalesKontrak, [
                "no"                    => $no++,
                "id"                    => $data->sales_contract_id,
                "sales_contract_no"      => $data->sales_contract_no,
                "customer_po_no"        => $data->customer_po_no,
                "customer_name"         => $data->customer_name,
                "dicharge_port"         => $data->dicharge_port,
                "shipment_date"         => $data->shipment_date,
                "createdAt"             => date('Y-m-d', strtotime($data->createdAt))
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesKontrakData['totalData'],
            "recordsFiltered"   => $salesKontrakData['totalFilteredData'],
            "data"              => $dataSalesKontrak,
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
                "sales_contract_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. SC tidak boleh kosong',
                    ]
                ],
                "customer_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Buyer tidak boleh kosong',
                    ]
                ],
                "customer_po_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. PO boleh kosong',
                    ]
                ],
                "loading_port" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Loading Port tidak boleh kosong',
                    ]
                ],
                "dicharge_port" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Dicharge Port tidak boleh kosong',
                    ]
                ],
                "due_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Due Date tidak boleh kosong',
                    ]
                ],
                "tolerance" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tolerance tidak boleh kosong',
                    ]
                ],
                "shipment_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Shipment Date tidak boleh kosong',
                    ]
                ],
                "documents_required" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Document Required tidak boleh kosong',
                    ]
                ],
                "special_instructions" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Special Instructions tidak boleh kosong',
                    ]
                ],
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
                $no = $this->salesKontrakModel->get_no(date('Y'), date('y'));
                
                $payload = [
                    "company_id" => formatter($this->this_company_id, "STR_TO_INT"),
                    "sales_contract_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("sales_contract_no"),
                    "customer_id" => formatter($this->request->getPost("customer_id"), "STR_TO_INT"),
                    "customer_po_no" => $this->request->getPost("customer_po_no"),
                    "loading_port" => $this->request->getPost("loading_port"),
                    "dicharge_port" => $this->request->getPost("dicharge_port"),
                    "due_date" => $this->request->getPost("due_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))) : "",
                    "total_amount" => $this->request->getPost("total_amount") ? formatter($this->request->getPost("total_amount"), "CURR_TO_INT") : 0,
                    "tolerance" => $this->request->getPost("tolerance"),
                    "shipment_date" => $this->request->getPost("shipment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("shipment_date")))) : "",
                    "payment_term" => $this->request->getPost("payment_term"),
                    "documents_required" => $this->request->getPost("documents_required"),
                    "special_instructions" => $this->request->getPost("special_instructions"),
                    "status" => "NEW"
                ];

                $items = json_decode($this->request->getPost("items"));

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);
                
                $response =  $this->salesKontrakModel->insert($payload);

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $detailPayload = [
                            'sales_contract_id' => $response,
                            'barang_id' =>$data->barang_id,
                            'unit' => $data->unit,
                            'qty' => $data->qty,
                            'remark' => $data->remark,
                            'price' => $data->price,
                            'total_price' => $data->total_price
                        ];

                        // $data = [
                        //     "status"            => false,
                        //     "message"    => $detailPayload,
                        //     "payload"   => $detailPayload,
                        //     'token' => csrf_hash()
                        // ];
                        // echo json_encode($data);

                        $responseDetail = $this->salesKontrakDetailModel->insert($detailPayload);

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
                "sales_contract_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. SC tidak boleh kosong',
                    ]
                ],
                "customer_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Buyer tidak boleh kosong',
                    ]
                ],
                "customer_po_no" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. PO boleh kosong',
                    ]
                ],
                "loading_port" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Loading Port tidak boleh kosong',
                    ]
                ],
                "dicharge_port" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Dicharge Port tidak boleh kosong',
                    ]
                ],
                "due_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Due Date tidak boleh kosong',
                    ]
                ],
                "tolerance" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tolerance tidak boleh kosong',
                    ]
                ],
                "shipment_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Shipment Date tidak boleh kosong',
                    ]
                ],
                "documents_required" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Document Required tidak boleh kosong',
                    ]
                ],
                "special_instructions" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Special Instructions tidak boleh kosong',
                    ]
                ],
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
                $no = $this->salesKontrakModel->get_no(date('Y'), date('y'));

                $payload = [
                    "company_id" => formatter($this->this_company_id, "STR_TO_INT"),
                    "sales_contract_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("sales_contract_no"),
                    "customer_id" => formatter($this->request->getPost("customer_id"), "STR_TO_INT"),
                    "customer_po_no" => $this->request->getPost("customer_po_no"),
                    "loading_port" => $this->request->getPost("loading_port"),
                    "dicharge_port" => $this->request->getPost("dicharge_port"),
                    "due_date" => $this->request->getPost("due_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))) : "",
                    "total_amount" => $this->request->getPost("total_amount") ? formatter($this->request->getPost("total_amount"), "CURR_TO_INT") : 0,
                    "tolerance" => $this->request->getPost("tolerance"),
                    "shipment_date" => $this->request->getPost("shipment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("shipment_date")))) : "",
                    "payment_term" => $this->request->getPost("payment_term"),
                    "documents_required" => $this->request->getPost("documents_required"),
                    "special_instructions" => $this->request->getPost("special_instructions"),
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
                    'sales_contract_id' => $id
                ];

                $response = $this->salesKontrakModel->where($condition)->set($payload)->update();

                if ($response) {
                    foreach($items as $data)
                    {
                        $detailPayload = [];

                        $detailPayload = [
                            'sales_contract_id' => $id,
                            'barang_id' =>$data->barang_id,
                            'unit' => $data->unit,
                            'qty' => $data->qty,
                            'remark' => $data->remark,
                            'price' => $data->price,
                            'total_price' => $data->total_price
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
                            $responseDetail = $this->salesKontrakDetailModel->delete($data->id);

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
                                'sales_contract_detail_id' => $data->id
                            ];

                            $responseDetail = $this->salesKontrakDetailModel->where($conditionDetail)->set($detailPayload)->update();

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
                        }

                        // kalau create
                        else
                        {
                            $responseDetail = $this->salesKontrakDetailModel->insert($detailPayload);

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

    public function updateStatus()
    {
        try{
            $id = $this->request->getPost("id");
            $status = $this->request->getPost("status");

            $payload = [
                "status" => $status
            ];
            
            $condition = [
                'sales_contract_id' => $id
            ];

            $response = $this->salesKontrakModel->where($condition)->set($payload)->update();

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => $status === "POSTED" ? "Data Berhasil diposting" : "Data Berhasil diunposting",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = $status === "POSTED" ? "Data Gagal diposting" : "Data Gagal diunposting";
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

    public function delete()
    {
        try{
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $find = $this->salesKontrakModel->find($id);
                if ($find) {
                    $response =  $this->salesKontrakModel->delete($id);
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
            $filename = "Sales Kontrak";

            $data = [];
            $dataSO = $this->salesKontrakModel->getById($id);

            if($dataSO)
            {
                $dataSODetail = $this->salesKontrakDetailModel->getSalesContractDetailBySalesContractId($id);

                // var_dump($dataSO);
                // die;

                if($dataSODetail)
                {
                    $data["dataSO"] = $dataSO;
                    $data["dataSODetail"] = $dataSODetail;
                }
            }

            // load HTML content
            $this->dompdf->loadHtml(view('SalesInternasional/SalesKontrak/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'portrait');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Purchase/poImportBahanPenolong/print', $data);
        }
    }

    public function dropdownSC()
    {
        $dataSO = $this->salesKontrakModel->getNo($this->this_company_id);

        $data = [
            "data" => $dataSO
        ];

        echo json_encode($data);
        return;
    }
}