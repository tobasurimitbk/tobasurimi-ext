<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;
use App\Models\SalesOrderModel;
use App\Models\CustomerModel;
use App\Models\SuratJalanModel;

class SuratJalan extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $CustomerModel;
    protected $SalesOrderModel;
    protected $encrypter;
    protected $SuratJalanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->CustomerModel = new CustomerModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->SuratJalanModel = new SuratJalanModel();
    }

    public function index()
    {
        return view('SalesLokal/SuratJalan/index');
    }

    public function createView()
    {
        //Get Customers
        $customers = $this->CustomerModel->asObject()->where('company_id', $this->this_company_id)->findAll();
        $data = [
            "dataCustomers" => $customers,
            "id_user" => session()->get('login')->user_id,
            "seller_name" => session()->get('login')->name,

        ];

        return view('SalesLokal/SuratJalan/form', $data);
    }

    public function all()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize" => $pageSize,
            "currentPage" => $currentPage,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = ['surat_jalan_so.deletedAt' => null];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];


        $dataSuratJalan = $this->SuratJalanModel
            ->getAllSuratJalan($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        //dd($dataSuratJalan);
        $dataAllSuratJalan = [];
        foreach ($dataSuratJalan['data'] as $data) {
            $dataNo = json_decode($data->multiple_no_so, true);
            array_push($dataAllSuratJalan, [
                "no"            => $no++,
                "id"            => $data->id,
                "no_surat_jalan"      => $data->no_surat_jalan,
                "no_so"      => implode(', ', $dataNo),
                "kode_pelanggan"        => $data->kode_pelanggan,
                "nama_pelanggan" => $data->nama_pelanggan,
                "shipping_date"         => date("d-m-Y", strtotime($data->shipping_date))
            ]);
        }
        //dd($dataAllSuratJalan);


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataSuratJalan['totalData'],
            "recordsFiltered" => $dataSuratJalan['totalFilteredData'],
            "data"              => $dataAllSuratJalan,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        $payload = $this->request->getVar();
        $items = json_decode($this->request->getPost("items"));


        $data = [
            "payload" => $payload,
            "items" => $items,
            'token'   => csrf_hash()
        ];
        echo json_encode($data);
    }

    public function getById()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
        try {
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

    public function dropDownSalesOrder($idCustomer)
    {
        $data = $this->SalesOrderModel
            ->asObject()
            ->where(['id_customer' => $idCustomer, 'tipe_sales_order' => 'LOKAL', 'deletedAt' => null])
            ->select(['id', 'no_sales_order'])
            ->findAll();


        echo json_encode($data);
        return;
    }
}
