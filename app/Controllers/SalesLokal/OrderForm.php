<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;
use App\Models\SalesOrderModel;
use App\Models\CustomerModel;
use App\Models\BarangModel;
use App\Models\WarehousesModel;
use App\Models\DetailStockBarang;
use App\Models\SalesOrderDetailModel;

class OrderForm extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $SalesOrderModel;
    protected $CustomerModel;
    protected $BarangModel;
    protected $WarehousesModel;
    protected $DetailStockBarang;
    protected $SalesOrderDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->CustomerModel = new CustomerModel();
        $this->BarangModel = new BarangModel();
        $this->WarehousesModel = new WarehousesModel();
        $this->DetailStockBarang = new DetailStockBarang();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
    }

    public function index()
    {
        return view('SalesLokal/OrderForm/index');
    }

    public function createView()
    {
        //Get Customers
        $customers = $this->CustomerModel->where('company_id', $this->this_company_id)->findAll();
        $data = [
            "dataCustomers" => $customers,
            "id_user" => session()->get('login')->user_id,
            "name" => session()->get('login')->name,

        ];

        return view('SalesLokal/OrderForm/form', $data);
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

        $response = curl_request("GET", "/salesOrderLokal", $this->token, $payload);
        $dataOrderForm = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataOrderForm, [
                    "no" => $no++,
                    "id" =>  bin2hex($this->encrypter->encrypt($data->id)),
                    "no_sales_order" => $data->no_sales_order,
                    "destination" => $data->destination,
                    "qty_barang" => $data->qty_barang,
                    "total_harga" => $data->total_harga,
                    "keterangan" => $data->keterangan,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataOrderForm,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        $payload = $this->request->getVar();
        $items = json_decode($this->request->getPost("items"));

        /*
        $data = [
            "payload" => $payload,
            //"items" => $items,
            'token'   => csrf_hash()
        ];
        echo json_encode($data);
        */

        $validate = $this->validate([
            "id_user" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'User tidak boleh kosong',
                ]
            ],
            "id_customer" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "destination" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tujuan pengiriman tidak boleh kosong',
                ]
            ],
            "order_date" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pemesanan ID tidak boleh kosong',
                ]
            ],
            "shipping_date" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "tax_status" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'tax status tidak boleh kosong',
                ]
            ],
            "include_pa" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'include pa tidak boleh kosong',
                ]
            ],
            "total" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'total harga tidak boleh kosong',
                ]
            ],
            "tipe_sales_order" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tipe sales order tidak boleh kosong',
                ],
            ],
            "items" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'barang tidak boleh kosong',
                ],
            ],
            /*
            "items" => 'is_array',
            "items.id_barang" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Barang tidak boleh kosong',
                ]
            ],
            "items.qty" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'qty tidak boleh kosong',
                ]
            ],
            "items.amount" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'total harga tidak boleh kosong',
                ]
            ],
            "items.warehouse_id" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'warehouse tidak boleh kosong',
                ]
            ],*/

        ]);
        if (!$validate) {
            echo json_encode($payload);
            return;
            //return redirect()->to('/order-form-lokal/create')->back()->withInput();
        }

        $noSalesOrder = null;
        $orderDate = $this->request->getPost('order_date');
        $shippingDate = $this->request->getPost('shipping_date');

        dd($orderDate, $shippingDate);
        $values = [
            "id_user" => $this->request->getPost('id_user'),
            "id_po" => $this->request->getPost('id_po'),
            "id_customer" => $this->request->getPost('id_customer'),
            "destination" => $this->request->getPost('destination'),
            "order_date" => $this->request->getPost('order_date'),
            "shipping_date" => $this->request->getPost('shipping_date'),
            "payment_terms" => $this->request->getPost('payment_terms'),
            "keterangan" => $this->request->getPost('parent_keterangan'),
            "discount_rupiah" => $this->request->getPost('discount_rupiah'),
            "discount_percentage" => $this->request->getPost('discount_percentage'),
            "ppn" => $this->request->getPost('ppn'),
            "estimated_freight" => $this->request->getPost('estimated_freight'),
            "tax_status" => $this->request->getPost('tax_status'),
            "include_pa" => $this->request->getPost('include_pa'),
            "total_harga" => $this->request->getPost('total'),
            "tipe_sales_order" => $this->request->getPost('tipe_sales_order'),
        ];


        // Create a new validation instance
        $dataSalesOrder =  $this->SalesOrderModel->insert($values);
        foreach ($items as $row) {
            $valueBarang = [
                "id_sales_order" => $dataSalesOrder,
                "id_barang" => $row->id_barang,
                "qty" => $row->qty,
                "amount" => $row->amount,
                "keterangan" => $row->keterangan,
                "tax" => $row->tax,
                "discount_percentage" => $row->discount_percentage,
                "dept" => $row->dept,
                "id_warehouse" => $row->warehouse_id,
            ];
            $this->SalesOrderDetailModel->save($valueBarang);
        }
        echo json_encode($dataSalesOrder);
    }


    public function getById($id = null)
    {
        //Get Customers
        $responseEmployee = curl_request("GET", "/customers/all?idCompany=$this->this_company_id", $this->token);

        $dataCustomers = [];
        if ($responseEmployee["code"] === 200) {
            $dataCustomers = json_decode($responseEmployee["body"])->data;
        }

        $data = [
            "dataCustomers" => $dataCustomers,
        ];

        //Get Detail
        if (!empty($id)) {
            $id = $this->encrypter->decrypt(hex2bin($id));
            $responseDetail = curl_request("GET", "/salesOrderLokal/$id", $this->token);

            $dataDetail = [];
            if ($responseDetail["code"] === 200) {
                $dataDetail = json_decode($responseDetail["body"])->data;
            }

            $data["data"] = $dataDetail;
        }

        // var_dump($data);
        // exit;


        return view('SalesLokal/OrderForm/form', $data);
    }

    public function update()
    {
    }

    public function delete()
    {
        try {
            $id = $this->request->getPost("id");


            if (!empty($id)) {
                $id = $this->encrypter->decrypt(hex2bin($id));


                $response = curl_request("DELETE", "/salesOrderLokal/$id", $this->token);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Dihapus';
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


    public function getAllBarang()
    {
        //Get Barang
        /*$responseBarang = curl_request("GET", "/barang/all?idCompany=$this->this_company_id", $this->token);

        $dataBarang = [];
        if ($responseBarang["code"] === 200) {
            $dataBarang = json_decode($responseBarang["body"])->data;
        }*/
        $dataBarang = $this->BarangModel
            ->join('warehouses', 'warehouses.id = barangs.warehouse_id', 'left')
            ->join('satuans', 'satuans.id = barangs.satuan_id', 'left')
            ->select('barangs.*')
            ->select('warehouses.warehouse_name')
            ->select('satuans.nama_satuan')
            ->where('kategori_id', 29)
            ->findAll();

        $data = [
            "dataBarang" => $dataBarang,
        ];

        echo json_encode($data);
        return;
    }

    public function getAllWarehouse($id_barang)
    {

        $dataWarehouse = $this->DetailStockBarang
            ->join('warehouses', 'warehouses.id = detail_stok_barang.warehouse_id')
            ->where('barang_id', $id_barang)
            ->where('stok >', 0)
            ->findAll();

        $data = [
            "dataWarehouse" => $dataWarehouse,
        ];

        echo json_encode($data);
        return;
    }
}
