<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\SatuansModel;
use App\Models\WorkOrdersModel;

class WorkOrder extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $barangModel;
    protected $satuanModel;
    protected $workOrdersModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangModel = new BarangModel();
        $this->satuanModel = new SatuansModel();
        $this->workOrdersModel = new WorkOrdersModel();
    }

    public function index()
    {
        return view('Production/workOrder/index');
    }

    public function createView()
    {
        //Get Barang
        $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $data = [
            "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan
        ];

        return view('Production/workOrder/form', $data);
    }

    public function getById($id = null)
    {
        //Get Barang
        $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $data = [
            "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan
        ];

        if (!empty($id)) {
            $dataWorkOrders = $this->workOrdersModel->asObject()->find($id);
            $data["dataWorkOrders"] = $dataWorkOrders;
        }

        return view('Production/workOrder/form', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")  
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $workOrdersData = $this->workOrdersModel->getWorkOrderList($condition, $addCondition, $limit, $offset);

        $dataWorkOrders = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($workOrdersData['data'] as $data) {
            array_push($dataWorkOrders, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "wo_no"                 => $data->wo_no,
                "kode_barang"           => $data->kode_barang,
                "nama_barang"           => $data->nama_barang,
                "production_amt"        => formatter($data->production_amt, "STR_TO_INT"),
                "nama_satuan"           => $data->nama_satuan,
                "target"                => formatter($data->target, "STR_TO_INT")
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $workOrdersData['totalData'],
            "recordsFiltered"   => $workOrdersData['totalFilteredData'],
            "data"              => $dataWorkOrders,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function create()
    {
        try {
            $rules = [
                "barang_id" => [
                    "rules" => "required"
                ],
                "production_amt" => [
                    "rules" => "required"
                ],
                "satuan_id" => [
                    "rules" => "required"
                ],
                "target" => [
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
                $no = $this->workOrdersModel->get_no(date('d'), date('m'), date('Y'), $last_day);
                // $no = $this->workOrdersModel->get_no();
                $payload = [
                    "wo_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("wo_no"),
                    "barang_id" => formatter($this->request->getPost("barang_id"), "STR_TO_INT"),
                    "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                    "target" => $this->request->getPost("target"),
                    "production_amt" => $this->request->getPost("production_amt")
                ];

                $data = [
                    "status"     => false,
                    "message"    => $payload,
                    "payload"    => $payload,
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);

                // $response =  $this->workOrdersModel->insert($payload);

                // if ($response) {
                //     $data = [
                //         "id"        => $response,
                //         "status"    => true,
                //         "message"   => "Data Berhasil disimpan",
                //         "payload"   => $payload,
                //         'token'     => csrf_hash()
                //     ];
                //     echo json_encode($data);
                // } else {
                //     $message =  'Data Gagal Disimpan';
                //     $data = [
                //         "status"     => false,
                //         "message"    => $message,
                //         "payload"    => $payload,
                //         'token'      => csrf_hash()
                //     ];
                //     echo json_encode($data);
                // }
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

    public function update()
    {
        try {
            $rules = [
                "barang_id" => [
                    "rules" => "required"
                ],
                "production_amt" => [
                    "rules" => "required"
                ],
                "satuan_id" => [
                    "rules" => "required"
                ],
                "target" => [
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
                $no = $this->workOrdersModel->get_no(date('d'), date('m'), date('Y'), $last_day);
                // $no = $this->workOrdersModel->get_no();
                $payload = [
                    "wo_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("wo_no"),
                    "barang_id" => formatter($this->request->getPost("barang_id"), "STR_TO_INT"),
                    "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                    "target" => $this->request->getPost("target"),
                    "production_amt" => $this->request->getPost("production_amt")
                ];

                $condition = [
                    'id' => $id
                ];

                $response = $this->workOrdersModel->where($condition)->set($payload)->update();

                if ($response) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
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
}