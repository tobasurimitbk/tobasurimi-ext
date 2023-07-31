<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;
use App\Models\SalesOrderModel;
use App\Models\CustomerModel;
use App\Models\AllNoModel;
use App\Models\SuratJalanModel;

class SuratJalan extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $CustomerModel;
    protected $SalesOrderModel;
    protected $encrypter;
    protected $SuratJalanModel;
    protected $AllNoModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->CustomerModel = new CustomerModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->AllNoModel = new AllNoModel();
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
        $data = [
            "payload" => $payload,
            'token'   => csrf_hash()
        ];
        //echo json_encode($payload);


        $validate = $this->validate([
            "id_customer" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "id_so" => 'is_array',
            "shipping_date" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            /*
            "no_surat_jalan" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'no surat jalan tidak boleh kosong',
                ]
            ],*/
        ]);
        if (!$validate) {
            // echo json_encode($payload);
            //return;
            return redirect()->to('/surat-jalan/create')->back()->withInput();
        }

        $dataSo = $this->request->getPost('id_so');

        $idArray = array();
        $noArray = array();

        foreach ($dataSo as $payload) {
            $delimiter = ",";
            $parts = explode($delimiter, $payload);
            array_push($idArray, $parts[0]);
            array_push($noArray, $parts[1]);
        }


        $code = "SJ";
        $currentYear = date('Y');
        $currentMonth = date('m');
        $monthName = date("F", mktime(0, 0, 0, $currentMonth, 10));
        $number = $this->AllNoModel->getNumber($code, $monthName . " " . $currentYear);
        $noSuratJalan = "TSI/" . $code . "/" . $currentMonth . "/" . $currentYear . "/" . $number;

        $shippingDate = $this->request->getPost('shipping_date');

        $values = [
            "id_user" => $this->request->getPost('id_user'),
            "id_customer" => $this->request->getPost('id_customer'),
            "id_customer" => $this->request->getPost('id_customer'),
            "shipping_date" =>  $shippingDate ? date("Y/m/d", strtotime(str_replace("/", "-", $shippingDate))) : "",
            "no_surat_jalan" => $noSuratJalan,
            "no_po" => $this->request->getPost('no_po'),
            'multiple_id_so' => json_encode($idArray),
            'multiple_no_so' => json_encode($noArray),
        ];
        try {

            // Create a new validation instance
            $dataSuratJalan =  $this->SuratJalanModel->insert($values);

            $data = [
                "id" => $dataSuratJalan,
                "status"            => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function getById($id = null)
    {
        $dataSuratJalan = $this->SuratJalanModel->getSuratJalanById(($id));
        $customers = $this->CustomerModel->asObject()->where('company_id', $this->this_company_id)->findAll();

        $dataSuratJalan->shipping_date = date("m/d/Y", strtotime($dataSuratJalan->shipping_date));
        $dataSo = $this->SalesOrderModel
            ->asObject()
            ->where(['id_customer' => $dataSuratJalan->id_customer, 'tipe_sales_order' => 'LOKAL', 'deletedAt' => null])
            ->select(['id', 'no_sales_order'])
            ->findAll();
        $data = [
            "data" => $dataSuratJalan,
            "dataCustomers" => $customers,
            "id_user" => $dataSuratJalan->id_user,
            "dataSo" => $dataSo

        ];
        //echo json_encode($data);
        return view('SalesLokal/SuratJalan/form', $data);
    }

    public function update()
    {

        $payload = $this->request->getVar();

        $data = [
            "payload" => $payload,
            //"items" => $items,
            'token'   => csrf_hash()
        ];
        //echo json_encode($data);

        $validate = $this->validate([
            "id" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'id tidak boleh kosong',
                ]
            ],
            "id_customer" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "id_so" => 'is_array',
            "shipping_date" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "no_surat_jalan" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'tax status tidak boleh kosong',
                ]
            ],
        ]);

        $id = $this->request->getPost('id');
        if (!$validate) {
            // echo json_encode($payload);
            //return;
            return redirect()->to('/surat-jalan/id/' . $id)->back()->withInput();
        }

        $dataSo = $this->request->getPost('id_so');

        $idArray = array();
        $noArray = array();

        foreach ($dataSo as $payload) {
            $delimiter = ",";
            $parts = explode($delimiter, $payload);
            array_push($idArray, $parts[0]);
            array_push($noArray, $parts[1]);
        }

        $shippingDate = $this->request->getPost('shipping_date');

        $values = [
            "id_user" => $this->request->getPost('id_user'),
            "id_customer" => $this->request->getPost('id_customer'),
            "id_customer" => $this->request->getPost('id_customer'),
            "shipping_date" =>  $shippingDate ? date("Y/m/d", strtotime(str_replace("/", "-", $shippingDate))) : "",
            "no_surat_jalan" => $this->request->getPost('no_surat_jalan'),
            "no_po" => $this->request->getPost('no_po'),
            'multiple_id_so' => json_encode($idArray),
            'multiple_no_so' => json_encode($noArray),
        ];
        try {

            // Create a new validation instance
            $dataSuratJalan =  $this->SuratJalanModel->update($id, $values);

            $data = [
                "id" => $dataSuratJalan,
                "status"            => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function delete()
    {

        $id = $this->request->getPost("id");
        //echo json_encode($id);


        try {
            if (!empty($id)) {
                $this->SuratJalanModel->delete($id);
                $data = [
                    "status"            => true,
                    "message"    => "Data success Dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
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
    public function dropDownSuratJalan($id_customer)
    {
        $data = $this->SuratJalanModel
            ->asObject()
            ->where(['id_customer' => $id_customer])
            ->select(['id', 'no_surat_jalan'])
            ->findAll();


        echo json_encode($data);
        return;
    }
    public function dataSuratJalanDetail($id_surat_jalan)
    {
        $data = $this->SuratJalanModel
            ->asObject()
            ->select(['id', 'no_surat_jalan', 'multiple_id_so'])
            ->find($id_surat_jalan);

        $dataIdSo = json_decode($data->multiple_id_so);

        $detailSo = array();
        $detailBarang = array();

        foreach ($dataIdSo as $id) {
            $dataSo = $this->SalesOrderModel->getSalesOrderLokalById($id);
            $detailSo[] = ["id" => $dataSo->id, "no_so" => $dataSo->no_sales_order];
            foreach ($dataSo->detail as $detail) {
                $detail['no_so'] = $dataSo->no_sales_order;
                $detail['no_surat_jalan'] = $data->no_surat_jalan;
                $detailBarang[] = $detail;
            }
        }

        $allData = [
            "detail_so" => $detailSo,
            "detail_barang" => $detailBarang
        ];
        echo json_encode($allData);
        return;
    }
}
