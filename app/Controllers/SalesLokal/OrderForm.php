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
use App\Models\AllNoModel;
use Error;


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
    protected $db;
    protected $AllNoModel;

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
        $this->AllNoModel = new AllNoModel();
        $this->db = \Config\Database::connect();
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
            "seller_name" => session()->get('login')->name,

        ];

        return view('SalesLokal/OrderForm/form', $data);
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


        $condition = ['deletedAt' => null];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataOrderForm = $this->SalesOrderModel
            ->getAllSalesOrderLokal($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $dataSalesOrder = [];
        foreach ($dataOrderForm['data'] as $data) {
            array_push($dataSalesOrder, [
                "no"            => $no++,
                "id"            => $data->id,
                "no_sales_order"      => $data->no_sales_order,
                "destination"        => $data->destination,
                "qty_barang" => $data->qty_barang,
                "total_harga"         => number_format($data->total_harga),
                "keterangan"  => $data->keterangan
            ]);
        }


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataOrderForm['totalData'],
            "recordsFiltered" => $dataOrderForm['totalFilteredData'],
            "data"              => $dataSalesOrder,
            "payload"           => $payload
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



        try {
            $this->db->transBegin();

            $code = "SLL";
            $currentYear = date('Y');
            $currentMonth = date('m');
            $monthName = date("F", mktime(0, 0, 0, $currentMonth, 10));
            $number = $this->AllNoModel->getNumber($code, $monthName . " " . $currentYear);
            $noSalesOrder = "SLL/" . $number . "/" . $currentYear . "/" . $currentMonth;
            $orderDate = $this->request->getPost('order_date');
            $shippingDate = $this->request->getPost('shipping_date');

            $values = [
                "no_sales_order" => $noSalesOrder,
                "id_user" => $this->request->getPost('id_user'),
                "id_po" => $this->request->getPost('id_po'),
                "id_customer" => $this->request->getPost('id_customer'),
                "destination" => $this->request->getPost('destination'),
                "order_date" => $orderDate ? date("Y/m/d", strtotime(str_replace("/", "-", $orderDate))) : "",
                "shipping_date" => $shippingDate ? date("Y/m/d", strtotime(str_replace("/", "-", $shippingDate))) : "",
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
            $totalQty = 0;
            foreach ($items as $row) {
                $item = $this->DetailStockBarang
                    ->where('barang_id', $row->id_barang)
                    ->where('warehouse_id', $row->warehouse_id)
                    ->first();

                if ($item['stok'] < $row->qty) {
                    throw new Error('barang tidak boleh kurang dari stock');
                    return;
                }
                $totalQty = $totalQty + $row->qty;
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
                $stok = [
                    "stok" => ($item['stok'] - $row->qty),
                ];
                $this->DetailStockBarang->update($item['id'], $stok);
            }

            $this->SalesOrderModel->update($dataSalesOrder, ['qty_barang' => $totalQty]);
            $this->db->transCommit();

            $data = [
                "id" => $dataSalesOrder,
                "status"            => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $this->db->transRollback();
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
        //Get data sales order
        $dataSalesOrder = $this->SalesOrderModel->getSalesOrderLokalById(($id));
        $customers = $this->CustomerModel->where('company_id', $this->this_company_id)->findAll();

        $dataSalesOrder->order_date = date("d-m-Y", strtotime($dataSalesOrder->order_date));
        $dataSalesOrder->shipping_date = date("d-m-Y", strtotime($dataSalesOrder->shipping_date));
        $data = [
            "data" => $dataSalesOrder,
            "dataCustomers" => $customers,
            "id_user" => $dataSalesOrder->id_user,
            "seller_name" => $dataSalesOrder->seller_name,

        ];
        //echo json_encode($data);

        return view('SalesLokal/OrderForm/form', $data);
    }

    public function update()
    {
        $payload = $this->request->getVar();
        $items = json_decode($this->request->getPost("items"));


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

        $orderDate = $this->request->getPost('order_date');
        $shippingDate = $this->request->getPost('shipping_date');

        $values = [
            "id_user" => $this->request->getPost('id_user'),
            "id_po" => $this->request->getPost('id_po'),
            "id_customer" => $this->request->getPost('id_customer'),
            "destination" => $this->request->getPost('destination'),
            "order_date" => $orderDate ? date("Y/m/d", strtotime(str_replace("/", "-", $orderDate))) : "",
            "shipping_date" => $shippingDate ? date("Y/m/d", strtotime(str_replace("/", "-", $shippingDate))) : "",
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


        $this->db->transBegin();
        try {
            // Create a new validation instance
            $dataSalesOrder =  $this->SalesOrderModel->update($payload['id'], $values);

            $totalQty = 0;
            foreach ($items as $row) {
                $item = $this->DetailStockBarang
                    ->where('barang_id', $row->id_barang)
                    ->where('warehouse_id', $row->warehouse_id)
                    ->first();



                if ($row->id && $row->isDeleted === false) {
                    $dataBefore = $this->SalesOrderDetailModel->find($row->id);
                    if ($dataBefore->qty > $row->qty) {
                        $dataItems = $dataBefore->qty - $row->qty;
                        $stok = [
                            "stok" => ($item['stok'] + $dataItems),
                        ];
                    } else {
                        $dataItems =  $row->qty - $dataBefore->qty;
                        $checkItems = $item['stok'] - $dataItems;


                        if ($checkItems < 0) {
                            throw new Error('barang tidak boleh kurang dari stock');
                        }
                        $stok = [
                            "stok" => ($item['stok'] - $dataItems),
                        ];
                    }
                    $totalQty = $totalQty + $row->qty;

                    $valueBarang = [
                        "id_barang" => $row->id_barang,
                        "qty" => $row->qty,
                        "amount" => $row->amount,
                        "keterangan" => $row->keterangan,
                        "tax" => $row->tax,
                        "discount_percentage" => $row->discount_percentage,
                        "dept" => $row->dept,
                        "id_warehouse" => $row->warehouse_id,
                    ];

                    $this->DetailStockBarang->update($item['id'], $stok);

                    $this->SalesOrderDetailModel->update($row->id, $valueBarang);
                } else if ($row->id && $row->isDeleted === true) {
                    $this->SalesOrderDetailModel->delete($row->id);
                    $stok = [
                        "stok" => ($item['stok'] + $row->qty),
                    ];
                    $this->DetailStockBarang->update($item['id'], $stok);
                } else {
                    if ($item['stok'] > $row->qty) {
                        throw new Error('barang tidak boleh kurang dari stock');
                    }
                    $totalQty = $totalQty + $row->qty;

                    $valueBarang = [
                        "id_sales_order" => $payload['id'],
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
                    $stok = [
                        "stok" => ($item['stok'] - $row->qty),
                    ];
                    $this->DetailStockBarang->update($item['id'], $stok);
                }
            }

            $this->SalesOrderModel->update($payload['id'], ['qty_barang' => $totalQty]);

            $this->db->transCommit();
            $data = [
                "id" => $dataSalesOrder,
                "status"            => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $this->db->transRollback();
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"            => false,
                "message"    => "Data Gagal Disimpan",
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        }
    }

    public function delete()
    {
        try {
            $id = $this->request->getPost("id");


            if (!empty($id)) {
                $this->db->transBegin();

                $this->SalesOrderModel->delete($id);
                $dataDetail = $this->SalesOrderDetailModel->where('id_sales_order', $id)->findAll();

                foreach ($dataDetail as $item) {
                    $itemStock = $this->DetailStockBarang
                        ->asObject()
                        ->where('barang_id', $item['id_barang'])
                        ->where('warehouse_id', $item['id_warehouse'])
                        ->first();
                    $stok = ['stok' => $itemStock->stok + $item['qty']];
                    $this->DetailStockBarang->update($item['id'], $stok);
                    $this->SalesOrderDetailModel->delete($item['id']);
                }
                $this->db->transCommit();

                $data = [
                    "status"            => true,
                    "message"    => "Data Success Dihapus",
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
            $this->db->transRollback();

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
