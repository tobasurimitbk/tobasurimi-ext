<?php

namespace App\Controllers\Production;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\DivisisModel;
use App\Models\SatuansModel;
use App\Models\WorkOrderDetailsModel;
use App\Models\WorkOrdersModel;

class WorkOrder extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $barangModel;
    protected $satuanModel;
    protected $workOrdersModel;
    protected $workOrderDetailsModel;
    protected $divisiModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangModel = new BarangModel();
        $this->satuanModel = new SatuansModel();
        $this->workOrdersModel = new WorkOrdersModel();
        $this->workOrderDetailsModel = new WorkOrderDetailsModel();
        $this->divisiModel = new DivisisModel();
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

        $dataDivisi = $this->divisiModel->asObject()->where('company_id', $this->this_company_id)->find();

        $data = [
            "dataBarang" => $dataBarang,
            "dataSatuan" => $dataSatuan,
            "dataDivisi" => $dataDivisi,
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
                "nama_barang"           => $data->nama_barang,
                "nama_divisi"           => $data->divisi,
                "standart_production"   => $data->standart_production,
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
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            $no = $this->workOrdersModel->get_no(date('d'), date('m'), date('Y'), $last_day);
            $id = $this->workOrdersModel->insert([
                "wo_no" => !empty($this->request->getPost("auto_generate")) ? $no : $this->request->getPost("wo_no"),
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar("department_id"),
                "request_date" => $this->request->getVar("date_production") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_production")))) : "",
                'standart_production' => $this->request->getVar('standart_production'),
                'note' => $this->request->getVar('note'),
                'is_posted' => 0,
                'request_status' => "waiting",
                'createdBy' =>  session()->get("login")->user_id,
            ]);

            $wo_detail = json_decode($this->request->getVar("items"));

            foreach ($wo_detail as $s) {
                $this->workOrderDetailsModel->insert([
                    'work_order_id' => $id,
                    'barang1_id' => decrypt($s->barang_id),
                    'barang2_id' => decrypt($s->barang_spesifikasi_id),
                    'nama_barang' => $s->nama_barang,
                    'qty' => $s->qty,
                    'unit' => $s->satuan_id,
                    'note' => $s->keterangan,
                ]);
            }

            return response()->setJSON([
                "id"      => encrypt($id),
                "status"  => true,
                "message" => "Data Berhasil disimpan",
                'token'   => csrf_hash(),
            ]);
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
