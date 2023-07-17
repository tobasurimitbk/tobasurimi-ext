<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;
use App\Models\SalesOrder;
use App\Models\CustomerModel;
use App\Models\BarangModel;

class OrderForm extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $SalesOrder;
    protected $CustomerModel;
    protected $BarangModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->SalesOrder = new SalesOrder();
        $this->CustomerModel = new CustomerModel();
        $this->BarangModel = new BarangModel();
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

        $validate = $this->validate([
            "id_user" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'User tidak boleh kosong',
                ]
            ],
            "id_po" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'po tidak boleh kosong',
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
            "payment_terms" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'payment terms tidak boleh kosong',
                ]
            ],
            "keterangan" => [
                "rules" => "",
                'errors' => []
            ],
            "discount_rupiah" => [
                "rules" => "",
                'errors' => []
            ],
            "discount_percentage" => [
                "rules" => "",
                'errors' => []
            ],
            "ppn" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'PPN  tidak boleh kosong',
                ]
            ],
            "estimated_freight" => [
                "rules" => "",
                'errors' => []
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
            "total_harga" => [
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
            ]
        ]);



        if (!$validate) {
            return redirect()->back()->withInput();
        }
        $barang = $this->request->getPost('barang');

        $rulesBarang = [
            "id_barang" => [
                "rules" => "required"
            ],
            "qty" => [
                "rules" => "required"
            ],
            "keterangan" => [
                "rules" => "required"
            ],
            "discount_percentage" => [
                "rules" => "required"
            ],
            "tax" => [
                "rules" => "required"
            ],
            "amount" => [
                "rules" => "required"
            ],
            "id_warehouse" => [
                "rules" => "required"
            ],
            "dept" => [
                "rules" => "required"
            ],
        ];

        $values = [
            "id_user" => $this->request->getPost('id_user'),
            "id_po" => $this->request->getPost('id_po'),
            "id_customer" => $this->request->getPost('id_customer'),
            "destination" => $this->request->getPost('destination'),
            "order_date" => $this->request->getPost('order_date'),
            "shipping_date" => $this->request->getPost('shipping_date'),
            "payment_terms" => $this->request->getPost('payment_terms'),
            "keterangan" => $this->request->getPost('keterangan'),
            "discount_rupiah" => $this->request->getPost('discount_rupiah'),
            "discount_percentage" => $this->request->getPost('discount_percentage'),
            "ppn" => $this->request->getPost('ppn'),
            "estimated_freight" => $this->request->getPost('estimated_freight'),
            "tax_status" => $this->request->getPost('tax_status'),
            "include_pa" => $this->request->getPost('include_pa'),
            "total_harga" => $this->request->getPost('total_harga'),
            "total_harga" => $this->request->getPost('total_harga'),
            "tipe_sales_order" => $this->request->getPost('tipe_sales_order'),
            "barang" => $this->request->getPost('barang'),
        ];


        // Create a new validation instance



        foreach ($barang as $row) {
        }


        $dataSalesOrder =  $this->SalesOrder->save($values);
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
        $dataBarang = $this->BarangModel->where('kategori_id', 29)->findAll();

        $data = [
            "dataBarang" => $dataBarang,
        ];


        echo json_encode($data);
        return;
    }
}
