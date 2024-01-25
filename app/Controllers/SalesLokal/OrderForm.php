<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;
use Dompdf\Dompdf;

use App\Models\CompaniesModel;
use App\Models\SalesOrderModel;
use App\Models\CustomerModel;
use App\Models\BarangMasterModel;
use App\Models\WarehousesModel;
use App\Models\DetailStockBarang;
use App\Models\StockDetailModel;
use App\Models\SalesOrderDetailModel;
use App\Models\AllNoModel;
use App\Models\MetadataModel;

use Error;
use ErrorException;

class OrderForm extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;

    private $companyModel;
    protected $SalesOrderModel;
    protected $CustomerModel;
    protected $BarangModel;
    protected $WarehousesModel;
    private $stockDetailModel;
    protected $SalesOrderDetailModel;
    protected $db;
    protected $AllNoModel;
    protected $MetaDataModel;

    private $userId;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();

        $this->companyModel = new CompaniesModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->CustomerModel = new CustomerModel();
        $this->BarangModel = new BarangMasterModel();
        $this->WarehousesModel = new WarehousesModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
        $this->AllNoModel = new AllNoModel();
        $this->MetaDataModel = new MetadataModel();
        $this->db = \Config\Database::connect();

        $this->userId = session()->get("login")->user_id;
    }

    public function index()
    {
        return view('SalesLokal/OrderForm/index');
    }

    public function createView()
    {
        //Get Customers
        $customers = $this->CustomerModel
            ->select('customers.*, CONCAT(employees.nip , " - ", employees.name) AS salesName, metadata.value AS termin')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->where('customers.deletedAt', null)
            ->where('employees.deletedAt', null)
            ->orderBy('customers.name', "ASC")
            //->where('customers.company_id', $this->this_company_id)
            ->findAll();

        $dataCompany = $this->companyModel->where('deletedAt', NULL)->asObject()->findAll();


        $data = [
            "dataCustomers" => $customers,
            "companies" => $dataCompany,
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


        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $dataOrderForm = $this->SalesOrderModel
            ->getAllSalesOrderLokal($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $dataSalesOrder = [];
        foreach ($dataOrderForm['data'] as $data) {
            $customerName = "";
            $dataCustomer = $this->CustomerModel->get_by_id($data->id_customer);

            foreach ($dataCustomer as $datasC) {
                $customerName = $datasC["name"];
            }


            array_push($dataSalesOrder, [
                "no" => $no++,
                "id" => bin2hex($this->encrypter->encrypt($data->id)),
                "no_sales_order" => $data->no_sales_order,
                "nama_customer" => $customerName,
                "destination" => $data->destination,
                "qty_barang" => count($this->SalesOrderDetailModel->where('id_sales_order', $data->id)->where('deletedAt', null)->findAll()),
                "total_harga" => formatRupiah($data->estimated_freight + $data->total_harga),
                "keterangan" => $data->keterangan
            ]);
        }

        // var_dump($dataSalesOrder);
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
        $items = json_decode($this->request->getVar("items"));

        $postData = $this->request->getPost();
        $postData["items"] = json_decode($postData["items"], true);

        $rules = [
            "id_customer" => [
                "rules" => "required|is_natural_no_zero",
                'errors' => [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "order_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'tanggal pemesananan tidak boleh kosong',
                ]
            ],
            "shipping_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "estimated_freight" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "tax_status" => [
                "rules" => "permit_empty|in_list[true,false]",
                'errors' => [
                    'required' => 'tax status tidak boleh kosong',
                ]
            ],
            "include_tax" => [
                "rules" => "permit_empty|in_list[true,false]",
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
                'errors' => [
                    'required' => 'tipe sales order tidak boleh kosong',
                ],
            ],
            "items" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'barang tidak boleh kosong',
                ],
            ],
            "items.*.id_barang" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'id barang tidak boleh kosong',
                ],
            ],
            "items.*.qty" => [
                "rules" => "required|numeric|greater_than[0]",
                'errors' => [
                    'required' => 'Qty barang tidak boleh kosong',
                ],
            ],
            "items.*.discount_percentage" => [
                "rules" => "permit_empty|numeric|greater_than_equal_to[0]",
                'errors' => [
                    // 'required' => 'barang tidak boleh kosong',
                ],
            ],
            "items.*.harga_barang" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Harga Barang tidak boleh kosong',
                ],
            ],
            "items.*.warehouse_id" => [
                "rules" => "required|numeric|greater_than_equal_to[0]",
                'errors' => [
                    'required' => 'Gudang barang tidak boleh kosong',
                ],
            ],
        ];

        if (!$this->validateData($postData, $rules)) {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        }

        // check customer
        $customerData = $this->CustomerModel->asObject()
            ->find($postData['id_customer']);

        if (empty($customerData)) {
            $data = [
                "status"    => false,
                "message"   => 'Customer tidak ditemukan!',
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        }

        // check stock
        foreach ($items as $row) {
            $barangData = $this->BarangModel->asObject()
                ->select('barang_master.*')
                ->select('stock_details.qty as stok_detail')
                ->join('stock_details', 'stock_details.barang_id = barang_master.id', 'left')
                ->where('barang_master.id', $row->id_barang)
                ->first();

            if (empty($barangData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Barang tidak ditemukan',
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }

            if ($barangData->stok_detail < $row->qty) {
                $data = [
                    "status"    => false,
                    "message"   => 'Stock tidak cukup',
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }
        }

        try {
            $this->SalesOrderModel->db->transException(true)->transStart();
            $orderDate = date('Y-m-d', strtotime(str_replace('/', '-', $postData['order_date'])));
            $shippingDate = date('Y-m-d', strtotime(str_replace('/', '-', $postData['shipping_date'])));
            $estimatedFreight = str_replace(',', '', $postData['estimated_freight']);

            $values = [
                "no_sales_order"        => $postData['no_sales_order'],
                "id_user"               => $this->userId,
                "id_customer"           => $postData['id_customer'],
                "sales_id"              => $customerData->sales_id,
                "order_date"            => $orderDate,
                "shipping_date"         => $shippingDate,
                "payment_terms"         => $postData['termin'],
                "keterangan"            => $postData['parent_keterangan'],
                "destination"           => $customerData->address,
                // "discount_rupiah"       => $postData('discount_rupiah'),
                // "discount_percentage"   => $postData('discount_percentage'),
                // "ppn"                   => $postData['taxAmt'],
                "estimated_freight"     => $estimatedFreight,
                // "tax_status"            => $postData['tax_status'],
                // "include_pa"            => $postData['include_tax'],
                "total_harga"           => $postData['total'],
                "id_company"            => $this->this_company_id,
                "tipe_sales_order"      => 'LOKAL'
            ];

            // Create a new validation instance
            $dataSalesOrder =  $this->SalesOrderModel->insert($values);

            $totalQty = 0;
            foreach ($items as $row) {

                // $this->BarangModel->builder()->decrement('stok', $row->qty);
                $this->stockDetailModel->addOrReduceStock($row->id_barang, $row->warehouse_id, 'New', $row->qty, 'Out', '');

                $totalQty = $totalQty + $row->qty;
                $amountValue = $row->amount ? (float) str_replace(",", "", $row->amount) : 0;
                // var_dump($row->amount);
                // var_dump($amountValue);
                // exit;
                $valueBarang = [
                    "id_sales_order"        => $dataSalesOrder,
                    "id_barang"             => $row->id_barang,
                    "qty"                   => $row->qty,
                    "harga_barang"          => str_replace(',', '', $row->harga_barang),
                    "amount"                => number_format($amountValue, 2, '.', ''),
                    "keterangan"            => $row->keterangan,
                    // "tax"                   => $row->tax,
                    "discount_percentage"   => $row->disc,
                    // "dept"                  => $row->dept,
                    "id_warehouse"          => $row->warehouse_id,
                ];
                $this->SalesOrderDetailModel->save($valueBarang);
            }

            $this->SalesOrderModel->update($dataSalesOrder, ['qty_barang' => $totalQty]);

            $this->SalesOrderModel->db->transComplete();

            $data = [
                "id"        => $dataSalesOrder,
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                // "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }


    public function getById($id = null)
    {
        //Get data sales order
        $id = $this->encrypter->decrypt(hex2bin($id));
        $dataSalesOrder = $this->SalesOrderModel->getSalesOrderLokalById(($id));

        // var_dump($dataSalesOrder);
        // exit;

        // validation
        if ($dataSalesOrder == null) {
            $session = session();
            $session->setFlashdata('error', "Nama Sales tidak ditemukan");
            return redirect()->to('order-form-lokal');
        }

        foreach ($dataSalesOrder->detail as &$detail) {
            $detail['harga_barang'] = ($detail['harga_barang']);
            $detail['amount'] = ($detail['amount']);

            $detail['barangTotal'] = $detail['harga_barang'] * $detail['qty'];
            $detail['discAmt'] = $detail['barangTotal'] * $detail['discount_percentage'] / 100;
            $detail['taxAmt'] = $detail['barangTotal'] * $detail['tax'] / 100;
        }

        $customers = $this->CustomerModel
            ->select('customers.*, CONCAT(employees.nip , " - ", employees.name) AS salesName, metadata.value AS termin')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->findAll();

        $metadatas = $this->MetaDataModel->findAll();

        $dataCompany = $this->companyModel->where('deletedAt', NULL)->asObject()->findAll();

        // dd($dataSalesOrder->detail);
        $dataSalesOrder->order_date = $dataSalesOrder->order_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataSalesOrder->order_date)) : "";
        $dataSalesOrder->shipping_date = $dataSalesOrder->shipping_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataSalesOrder->shipping_date)) : "";
        $data = [
            "data" => $dataSalesOrder,
            "dataCustomers" => $customers,
            "companies" => $dataCompany,
            "dataMetaData"  => $metadatas,
            "id_user" => $dataSalesOrder->id_user,
            // "seller_name" => $dataSalesOrder->seller_name,

        ];


        //echo json_encode($data);

        return view('SalesLokal/OrderForm/form', $data);
    }

    public function getByCustomerId($customerId)
    {
        $select = "no_sales_order AS invoice_no,
                   total_harga AS amt,
                   order_date AS date,
                   (total_harga - paid_amt) AS owing,
                   discount_rupiah AS total_disc";
        $condition = [
            'id_customer'       => $customerId,
            'tipe_sales_order'  => 'LOKAL'
        ];

        $dataSalesOrder = $this->SalesOrderModel->asObject()
            ->select($select)
            ->where($condition)
            ->findAll();

        $data = [
            "data" => $dataSalesOrder

        ];
        echo json_encode($data);
    }

    public function update()
    {
        $items = json_decode($this->request->getVar("items"));

        $payload = $this->request->getVar();
        $postData["items"] = json_decode($payload["items"], true);

        $data = [
            "payload" => $payload,
            //"items" => $items,
            'token'   => csrf_hash()
        ];
        //echo json_encode($data);

        $validate = [
            "id_customer" => [
                "rules" => "required|is_natural_no_zero",
                'errors' => [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "order_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'tanggal pemesananan tidak boleh kosong',
                ]
            ],
            "shipping_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "estimated_freight" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "tax_status" => [
                "rules" => "permit_empty|in_list[true,false]",
                'errors' => [
                    'required' => 'tax status tidak boleh kosong',
                ]
            ],
            "include_tax" => [
                "rules" => "permit_empty|in_list[true,false]",
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
                'errors' => [
                    'required' => 'tipe sales order tidak boleh kosong',
                ],
            ],
            "items" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'barang tidak boleh kosong',
                ],
            ],
        ];
        $customerData = $this->CustomerModel->asObject()
            ->find($this->request->getPost('id_customer'));

        if (empty($customerData)) {
            $data = [
                "status"    => false,
                "message"   => 'Customer tidak ditemukan!',
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        }

        if (!$this->validateData($payload, $validate)) {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        }

        $orderDate = $this->request->getPost('order_date');
        $shippingDate = $this->request->getPost('shipping_date');

        $values = [
            "id_user" => $this->request->getPost('id_user'),
            "id_po" => $this->request->getPost('id_po'),
            "id_customer" => $this->request->getPost('id_customer'),
            "destination"           => $customerData->address,
            "order_date" => $orderDate ? date("Y/m/d", strtotime(str_replace("/", "-", $orderDate))) : "",
            "shipping_date" => $shippingDate ? date("Y/m/d", strtotime(str_replace("/", "-", $shippingDate))) : "",
            "payment_terms" => $this->request->getPost('termin'),
            "keterangan" => $this->request->getPost('parent_keterangan'),
            "discount_rupiah" => $this->request->getPost('discount_rupiah'),
            "discount_percentage" => $this->request->getPost('discount_percentage'),
            // "ppn" => $this->request->getPost('ppn'),
            "estimated_freight" => formatter($this->request->getPost('estimated_freight'), "STR_TO_INT"),
            // "tax_status" => $this->request->getPost('tax_status'),
            // "include_pa" => $this->request->getPost('include_pa'),
            "total_harga" => $this->request->getPost('total'),
            "tipe_sales_order" => $this->request->getPost('tipe_sales_order'),
        ];

        $this->db->transBegin();
        try {
            // Create a new validation instance
            $dataSalesOrder =  $this->SalesOrderModel->update($payload['id'], $values);

            $totalQty = 0;

            // codingan baru
            foreach ($items as $row) {
                if ($row->id === "") {
                    $barangData = $this->BarangModel->asObject()
                        ->select('barang_master.*')
                        ->select('stock_details.qty as stok_detail')
                        ->join('stock_details', 'stock_details.barang_id = barang_master.id', 'left')
                        ->where('barang_master.id', $row->id_barang)
                        ->first();

                    if (empty($barangData)) {
                        $data = [
                            "status"    => false,
                            "message"   => 'Barang tidak ditemukan',
                            'token'     => csrf_hash(),
                        ];
                        echo json_encode($data);
                        return;
                    }

                    if ($barangData->stok_detail < $row->qty) {
                        $data = [
                            "status"    => false,
                            "message"   => 'Stock tidak cukup',
                            'token'     => csrf_hash(),
                        ];
                        echo json_encode($data);
                        return;
                    }

                    $this->BarangModel->builder()->decrement('stok', $row->qty);
                    $this->stockDetailModel->addOrReduceStock($row->id_barang, $row->warehouse_id, 'New', $row->qty, 'Out', '');;

                    $totalQty = $totalQty + $row->qty;
                    $valueBarang = [
                        "id_sales_order"        => $payload['id'],
                        "id_barang"             => $row->id_barang,
                        "qty"                   => $row->qty,
                        "harga_barang"          => $row->harga_barang,
                        "amount"                => formatter($row->amount, "CURR_TO_INT"),
                        "keterangan"            => $row->keterangan,
                        // "tax"                   => $row->tax,
                        "discount_percentage"   => $row->disc,
                        // "dept"                  => $row->dept,
                        "id_warehouse"          => $row->warehouse_id,
                    ];
                    $this->SalesOrderDetailModel->save($valueBarang);
                } else {
                    // $data = [
                    //     "status"            => false,
                    //     "message"    => json_encode($row),
                    //     'token' => csrf_hash(),
                    // ];
                    // return json_encode($data);

                    if ($row->isDeleted === true) {
                        $this->SalesOrderDetailModel->delete($row->id);
                        $this->stockDetailModel->addOrReduceStock($row->id_barang, $row->warehouse_id, 'New', $row->qty, 'In', '');;
                    } else {
                        $item = $this->stockDetailModel
                            ->where('barang_id', $row->id_barang)
                            ->where('warehouse_id', $row->warehouse_id)
                            ->first();

                        $dataBefore = $this->SalesOrderDetailModel->asObject()->find($row->id);
                        if ($dataBefore->qty > $row->qty) {
                            $dataItems = $dataBefore->qty - $row->qty;
                            $stok = [
                                "stok" => ($item['qty'] + $dataItems),
                            ];

                            $this->stockDetailModel->update($item['id'], $stok);
                        }

                        if ($dataBefore->qty < $row->qty) {

                            $dataItems =  $row->qty - $dataBefore->qty;
                            $checkItems = $item['stok'] - $dataItems;


                            if ($checkItems < 0) {
                                throw new ErrorException('barang tidak boleh kurang dari stock');
                            }
                            $stok = [
                                "stok" => ($item['qty'] - $dataItems)
                            ];

                            $this->stockDetailModel->update($item['id'], $stok);
                        }
                        $totalQty = $totalQty + $row->qty;

                        $valueBarang = [
                            "id_barang" => $row->id_barang,
                            "qty" => $row->qty,
                            "amount" => formatter($row->amount, "CURR_TO_INT"),
                            "keterangan" => $row->keterangan,
                            // "tax" => $row->tax,
                            "discount_percentage" => $row->disc,
                            "dept" => $row->dept,
                            "id_warehouse" => $row->warehouse_id,
                        ];

                        $this->SalesOrderDetailModel->update($row->id, $valueBarang);
                    }
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
                "items" => $items,
                "error" => $e->getMessage(),
                "error-line" => $e->getLine(),
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
            // $id = $this->request->getPost("id");
            $id = $this->encrypter->decrypt(hex2bin($this->request->getPost("id")));


            if (!empty($id)) {
                $this->db->transBegin();

                $this->SalesOrderModel->delete($id);
                $dataDetail = $this->SalesOrderDetailModel->where('id_sales_order', $id)->findAll();

                foreach ($dataDetail as $item) {
                    $itemStock = $this->stockDetailModel
                        ->asObject()
                        ->where('barang_id', $item['id_barang'])
                        ->where('warehouse_id', $item['id_warehouse'])
                        ->first();
                    // $stok = ['qty' => $itemStock->qty + $item['qty']];
                    $this->stockDetailModel->addOrReduceStock($item['id_barang'], $item['id_warehouse'], 'New', $item['qty'], 'In', '');;
                    $this->SalesOrderDetailModel->delete($item['id']);
                }
                $this->db->transCommit();

                $data = [
                    "status"            => true,
                    "message"    => "Data Order Berhasil Dihapus",
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
            ->join('satuans', 'satuans.id = barang_master.satuan_id', 'left')
            ->join('stock_details', 'stock_details.barang_id = barang_master.id', 'left')
            ->join('warehouses', 'warehouses.id = stock_details.warehouse_id', 'left')
            ->select('barang_master.*')
            ->select('barang_master.id as id_barang')
            ->select('barang_master.kode_barang as kode_barang')
            ->select('barang_master.id as id_barang')
            ->select('barang_master.barang_name as nama_barang')
            ->select('warehouses.warehouse_name as warehouse_name')
            ->select('stock_details.qty as qty_stock')
            ->select('satuans.nama_satuan as nama_satuan')
            ->where('type_barang', 'bahan_jadi')
            ->where('barang_master.deletedAt', null)
            ->where('stock_details.qty >', 0)
            ->groupBy('id_barang')
            ->findAll();

        $data = [
            "dataBarang" => $dataBarang,
        ];

        echo json_encode($data);
        return;
    }

    public function getAllWarehouse($id_barang)
    {

        /* $dataWarehouse = $this->DetailStockBarang
            ->join('warehouses', 'warehouses.id = detail_stok_barang.warehouse_id')
            ->where('barang_id', $id_barang)
            ->where('stok >', 0)
            ->findAll(); */

        $dataWarehouse = $this->stockDetailModel
            // ->select()
            ->join('warehouses', 'warehouses.id = stock_details.warehouse_id')
            ->where('barang_id', $id_barang)
            ->where('stock_details.qty >', 0)
            ->groupBy(['stock_details.barang_id', 'stock_details.warehouse_id'])
            ->findAll();

        $data = [
            "dataWarehouse" => $dataWarehouse,
        ];

        echo json_encode($data);
        return;
    }

    public function getStockDetail($id_barang, $id_warehouse)
    {

        /* $dataWarehouse = $this->DetailStockBarang
            ->join('warehouses', 'warehouses.id = detail_stok_barang.warehouse_id')
            ->where('barang_id', $id_barang)
            ->where('stok >', 0)
            ->findAll(); */

        $dataDetailStock = $this->stockDetailModel
            ->checkStock($id_barang, $id_warehouse);

        $data = [
            "dataDetailStock" => $dataDetailStock,
        ];

        echo json_encode($data);
        return;
    }

    public function getItemListbyId($id)
    {
        $SOData = $this->SalesOrderModel->asObject()
            ->select('tax_status, include_pa')
            ->find($id);
        $itemList = $this->SalesOrderDetailModel->getItemListByIds($id);

        $taxStatus = filter_var($SOData->tax_status, FILTER_VALIDATE_BOOLEAN);
        $includeTax = filter_var($SOData->include_pa, FILTER_VALIDATE_BOOLEAN);

        $data = [
            'SOData'    => [
                'taxStatus' => $taxStatus,
                'includeTax' => $includeTax
            ],
            'itemList'  => $itemList
        ];
        echo json_encode($data);
    }

    public function getItemListbyIds()
    {
        $ids = $this->request->getGet('ids');

        if (empty($ids)) {
            echo json_encode('[]');
            return;
        }

        $datas = $this->SalesOrderDetailModel->getItemListByIds($ids);

        echo json_encode($datas);
    }

    public function printOrder($id)
    {
        $domPdf = new Dompdf();

        $fileName = 'Order Form';

        $companyData = $this->companyModel->asObject()
            ->find($this->this_company_id);

        $soSelectQry = "sales_order.*,
                        DATE_FORMAT(sales_order.order_date, '%d %b %Y') AS order_date, 
                        DATE_FORMAT(sales_order.shipping_date, '%d %b %Y') AS shipping_date, 
                        customers.name AS customerName, 
                        customers.address AS customerAddress,
                        metadata.value AS termin";
        $salesOrderData = $this->SalesOrderModel->asObject()
            ->select($soSelectQry)
            ->join('customers', 'customers.id = sales_order.id_customer', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->find($id);

        $soDet = $this->SalesOrderDetailModel->asObject()
            ->select('barang_master.barang_name AS namaBarang, barang_master.kode_barang AS kodeBarang, sales_order_detail.qty AS qty, satuans.kode_satuan AS kodeSatuan')
            ->join('barang_master', 'barang_master.id = sales_order_detail.id_barang')
            ->join('satuans', 'satuans.id = barang_master.satuan_id')
            ->where('id_sales_order', $id)
            ->findAll();

        $data = [
            'companyName'   => $companyData->company,
            'soData'        => $salesOrderData,
            'soDet'         => $soDet
        ];

        // return view('SalesLokal/OrderForm/print', $data);

        // load HTML content
        $domPdf->loadHtml(view('SalesLokal/OrderForm/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper([0, 0, 792.96, 528]);

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
    }

    public function generateNomorSalesOrder()
    {
        $code = "SLL";
        $currentYear = date('Y');
        $currentMonth = date('m');
        $monthName = date("F", mktime(0, 0, 0, $currentMonth, 10));
        $number = $this->AllNoModel->getNumber($code, $monthName . " " . $currentYear);
        $noSalesOrder = "SLL/" . $number . "/" . $currentYear . "/" . $currentMonth;

        return response()->setJSON([
            'data' => $noSalesOrder,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getMetaData($id)
    {

        $metadatas = $this->MetaDataModel
            ->where('metadata.id', $id)
            ->findAll();

        $data = [
            "dataMetaData" => $metadatas,
        ];

        echo json_encode($data);
        return;
    }
}
