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
use App\Models\BanksModel;
use App\Models\BarangMasterSalesModel;
use App\Models\EmployeesModel;
use App\Models\MetadataModel;
use App\Models\ProvincesModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SatuansModel;
use App\Models\SuratJalanModel;
use Error;
use ErrorException;
use Exception;

class OrderForm extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $is_admin;
    protected $encrypter;

    private $companyModel;
    protected $SalesOrderModel;
    protected $CustomerModel;
    protected $BarangMasterSalesModel;
    protected $WarehousesModel;
    private $stockDetailModel;
    protected $SalesOrderDetailModel;
    protected $db;
    protected $AllNoModel;
    protected $MetaDataModel;
    protected $employeeModel;
    protected $ProvincesModel;
    protected $BanksModel;
    protected $satuanModel;
    protected $suratJalanModel;
    protected $salesOrderInvoiceModel;

    private $userId;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();

        $this->companyModel = new CompaniesModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->CustomerModel = new CustomerModel();
        $this->BarangMasterSalesModel = new BarangMasterSalesModel();
        $this->WarehousesModel = new WarehousesModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
        $this->AllNoModel = new AllNoModel();
        $this->MetaDataModel = new MetadataModel();
        $this->employeeModel = new EmployeesModel();
        $this->ProvincesModel = new ProvincesModel();
        $this->BanksModel = new BanksModel();
        $this->satuanModel = new SatuansModel();
        $this->db = \Config\Database::connect();
        $this->suratJalanModel = new SuratJalanModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();

        $this->userId = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
    }

    public function index()
    {
        $data = [
            'getCustomers' => $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin),
        ];
        return view('SalesLokal/OrderForm/index', $data);
    }

    public function createView()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataBanks = $this->BanksModel->search_list(array(), 'name');
        $dataSatuan = $this->satuanModel->findAll();
        //Get Customers
        $customers = $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin);
        $condition = [
            'jabatan_name' => "MARKETING LOKAL"
        ];

        $sales = $this->employeeModel->getEmployeesComplete($condition);
        $dataCompany = $this->companyModel->where('deletedAt', NULL)->asObject()->findAll();

        $dataTermin = $this->MetaDataModel
            ->where('metadata.deletedAt', null)
            ->where('metadata.name', 'Termin')
            ->orderBy('CAST(metadata.value AS DECIMAL)', 'ASC')
            ->findAll();

        $dataTipePelanggan = $this->MetaDataModel
            ->where('deletedAt', null)
            ->where('name', "Tipe Pelanggan")
            ->findAll();

        $dataValuta = $this->MetaDataModel
            ->where('deletedAt', null)
            ->where('name', "Valuta")
            ->where('value', "IDR")
            ->findAll();

        $data = [
            "dataCustomers" => $customers,
            "dataSales" => $sales,
            "companies" => $dataCompany,
            "dataTermin" => $dataTermin,
            "dataProvinces" => $dataProvinces,
            "dataBanks" => $dataBanks,
            'dataSatuan' => $dataSatuan,
            "id_user" => session()->get('login')->user_id,
            "seller_name" => session()->get('login')->name,
            "dataTipePelanggan" => $dataTipePelanggan,
            "dataValuta" => $dataValuta
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

        if ($this->is_admin == '1') {
            $condition = [
                // "sales_order.id_company" => $this->this_company_id,
                "sales_order.deletedAt" => null,
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                // "sales_order.id_company" => $this->this_company_id,
                "sales_order.deletedAt" => null,
                'sales_order.id_user' => $this->userId
            ];
        }

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "filter_customer"        => $this->request->getGet("filter_customer"),
            "filter_invoice"        => $this->request->getGet("filter_invoice"),
            "filter_surat_jalan"        => $this->request->getGet("filter_surat_jalan"),
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
                "id" => encrypt($data->id),
                "no_sales_order" => $data->no_sales_order,
                "createdAt" => date("d/m/Y", strtotime($data->createdAt)),
                "order_date" => date("d/m/Y", strtotime($data->order_date)),
                "shipping_date" => $data->shipping_date == "0000-00-00" ? "" : date("d/m/Y", strtotime($data->shipping_date)),
                "company_name" => $data->company_name,
                "nama_customer" => $customerName,
                "destination" => $data->destination,
                "qty_barang" => count($this->SalesOrderDetailModel->where('id_sales_order', $data->id)->where('deletedAt', null)->where('tipe_input', "order_form")->findAll()),
                "total_harga" => ($data->estimated_freight + $data->total_harga),
                "keterangan" => $data->keterangan,
                "surat_jalan_so_id" => $data->surat_jalan_so_id,
                "sales_order_invoice_id" => $data->sales_order_invoice_id,
                "counter_print" => $data->counter_print,
                "posting" => $data->posting,
                "salesName" => $data->salesName,
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
                    'required' => 'Tanggal pemesananan tidak boleh kosong',
                ]
            ],
            "shipping_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            // "estimated_freight" => [
            //     "rules" => "permit_empty",
            //     'errors' => [
            //         // 'required' => 'tanggal pengiriman tidak boleh kosong',
            //     ]
            // ],
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
                "rules" => "permit_empty",
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
            "items.*.discUnit" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Discount unit tidak boleh kosong',
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

        $initialPPNStatus = $items[0]->statusppn;

        if ($initialPPNStatus == "0") {
            $status_ppn = "0";
        } else {
            $status_ppn = "1";
        }
        foreach ($items as $row) {
            if ($row->statusppn != $initialPPNStatus) {
                $data = [
                    "status"    => false,
                    "message"   => "Status PPN Barang Tidak Boleh Berbeda",
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }
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
        try {
            $this->SalesOrderModel->db->transException(true)->transStart();
            $orderDate = date('Y-m-d', strtotime(str_replace('/', '-', $postData['order_date'])));
            $shippingDate = date('Y-m-d', strtotime(str_replace('/', '-', $postData['shipping_date'])));

            $values = [
                "no_sales_order"        => strtoupper($postData['no_sales_order']),
                "id_user"               => $this->userId,
                "id_customer"           => $postData['id_customer'],
                "shipping_date"           => $shippingDate,
                "destination"           => $postData['destination'],
                "jenis_penjualan"           => $postData['jenis_penjualan'],
                "sales_id"              => isset($postData['id_sales']) ? $postData['id_sales'] : NULL,
                "nama_ecommerce"           => $postData['nama_ecommerce'] ? $postData['nama_ecommerce'] : "",
                "order_date"            => $orderDate,
                "total_harga"           => $postData['total'],
                "keterangan" => $postData['parent_keterangan'],
                "id_company"            => $this->this_company_id != 16 ? $this->request->getVar('company_id') : $this->this_company_id,
                "tipe_sales_order"      => 'LOKAL',
                "ppn"      => $status_ppn
            ];

            $checkSO = $this->SalesOrderModel->where('UPPER(no_sales_order)', strtoupper($this->request->getVar('no_sales_order')))->findAll();
            if ($checkSO) {
                $data = [
                    "status"    => false,
                    "message"   => "No Sales Order Sudah Digunakan",
                    "payload"   => $values,
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }

            // Create a new validation instance
            $dataSalesOrder =  $this->SalesOrderModel->insert($values);

            $totalQty = 0;
            foreach ($items as $row) {

                // $this->BarangModel->builder()->decrement('stok', $row->qty);
                // $this->stockDetailModel->addOrReduceStock($row->id_barang, $row->warehouse_id, 'New', $row->qty, 'Out', '');

                $totalQty = $totalQty + $row->qty;
                $amountValue = $row->amount ? (float) str_replace(",", "", $row->amount) : 0;
                // var_dump($row->amount);
                // var_dump($amountValue);
                // exit;
                $valueBarang = [
                    "id_sales_order"        => $dataSalesOrder,
                    "id_barang"             => $row->id_barang,
                    "qty"                   => number_format($row->qty, 2, '.', ''),
                    "qty_sekarang"          => number_format($row->qty, 2, '.', ''),
                    "harga_barang"          => str_replace(',', '', $row->harga_barang),
                    "amount"                => number_format($amountValue, 2, '.', ''),
                    "keterangan"            => $row->keterangan,
                    "discount_percentage"   => number_format($row->disc, 2, '.', ''),
                    "tipe_input"            => "order_form",
                    "status_ppn"            => $row->statusppn,
                    "discount_unit"            => $row->discUnit,
                    // "dept"                  => $row->dept,
                    // "id_warehouse"          => $row->warehouse_id,
                ];
                $this->SalesOrderDetailModel->save($valueBarang);
            }

            $this->SalesOrderModel->update($dataSalesOrder, ['qty_barang' => $totalQty]);

            $this->SalesOrderModel->db->transComplete();

            $data = [
                "id"        => encrypt($dataSalesOrder),
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
        $id = decrypt($id);
        $dataSalesOrder = $this->SalesOrderModel->getSalesOrderLokalById(($id));
        if ($dataSalesOrder == null) {
            $session = session();
            $session->setFlashdata('error', "Data Sales tidak ditemukan");
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
            ->select('customers.*, customers.sales_id AS salesName, metadata.value AS termin')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->findAll();

        $condition = [
            'jabatan_name' => "MARKETING LOKAL"
        ];

        $sales = $this->employeeModel->getEmployeesComplete($condition);
        $dataTermin = $this->MetaDataModel
            ->where('metadata.deletedAt', null)
            ->where('metadata.name', 'Termin')
            ->orderBy('CAST(metadata.value AS DECIMAL)', 'ASC')
            ->findAll();

        $metadatas = $this->MetaDataModel->findAll();
        $dataCompany = $this->companyModel->where('deletedAt', NULL)->asObject()->findAll();
        $dataTipePelanggan = $this->MetaDataModel
            ->where('deletedAt', null)
            ->where('name', "Tipe Pelanggan")
            ->findAll();

        $dataValuta = $this->MetaDataModel
            ->where('deletedAt', null)
            ->where('name', "Valuta")
            ->where('value', "IDR")
            ->findAll();

        // dd($dataSalesOrder->detail);
        $dataSalesOrder->order_date = $dataSalesOrder->order_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataSalesOrder->order_date)) : "";
        $dataSalesOrder->shipping_date = $dataSalesOrder->shipping_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataSalesOrder->shipping_date)) : "";
        $dataSalesOrder->id = encrypt($dataSalesOrder->id);
        $data = [
            "data" => $dataSalesOrder,
            "dataCustomers" => $customers,
            "dataSales" => $sales,
            "companies" => $dataCompany,
            "dataMetaData"  => $metadatas,
            "id_user" => $dataSalesOrder->id_user,
            "dataTermin" => $dataTermin,
            "dataTipePelanggan" => $dataTipePelanggan,
            "dataValuta" => $dataValuta
        ];

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
        $id = decrypt($this->request->getPost("id"));

        $postData = $this->request->getPost();
        $postData["items"] = json_decode($postData["items"], true);

        $rules = [
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

        // Validate PPN status consistency
        $initialPPNStatus = $items[0]->statusppn;

        if ($initialPPNStatus == "0") {
            $status_ppn = "0";
        } else {
            $status_ppn = "1";
        }


        foreach ($items as $row) {
            if ($row->statusppn != $initialPPNStatus) {
                $data = [
                    "status"    => false,
                    "message"   => "Status PPN Barang Tidak Boleh Berbeda",
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

            $totalQty = 0;
            $total_harga = 0;
            foreach ($items as $row) {
                $totalQty = $totalQty + $row->qty;

                $amountValue = $row->amount ? (float) str_replace(",", "", $row->amount) : 0;
                $total_harga +=  $amountValue - ($amountValue * ($row->disc / 100));
                if ($row->id == 0 || $row->id == null || $row->id == "") {
                    $valueBarang = [
                        "id_sales_order"        => $id,
                        "id_barang"             => $row->id_barang,
                        "qty"                   => $row->qty,
                        "qty_sekarang"                   => $row->qty,
                        "harga_barang"          => str_replace(',', '', $row->harga_barang),
                        "amount"                => number_format($amountValue, 2, '.', ''),
                        "keterangan"            => $row->keterangan,
                        "tax"                   => $row->statusppn,
                        "discount_percentage"   => $row->disc,
                        "tipe_input"            => "order_form",
                        "status_ppn"            => $row->statusppn,

                        // "dept"                  => $row->dept,
                        // "id_warehouse"          => $row->warehouse_id,
                    ];
                    $this->SalesOrderDetailModel->save($valueBarang);
                } else {
                    $valueBarang = [
                        "id_sales_order"        => $id,
                        "id_barang"             => $row->id_barang,
                        "qty"                   => $row->qty,
                        "qty_sekarang"                   => $row->qty,
                        "harga_barang"          => str_replace(',', '', $row->harga_barang),
                        "amount"                => number_format($amountValue, 2, '.', ''),
                        "keterangan"            => $row->keterangan,
                        "tax"                   => $row->statusppn,
                        "discount_percentage"   => $row->disc,
                        "tipe_input"            => "order_form",
                        "status_ppn"            => $row->statusppn,

                        // "dept"                  => $row->dept,
                        // "id_warehouse"          => $row->warehouse_id,
                    ];
                    $this->SalesOrderDetailModel->update($row->id, $valueBarang);
                }
            }

            $this->SalesOrderModel->update(
                $id,
                [
                    "no_sales_order"        => strtoupper($postData['no_sales_order']),
                    "id_customer"           => $postData['id_customer'],
                    'shipping_date' => $shippingDate,
                    'destination' =>  $postData['destination'],
                    "jenis_penjualan"           => $postData['jenis_penjualan'],
                    "sales_id"              => isset($postData['id_sales']) ? $postData['id_sales'] : NULL,
                    "nama_ecommerce"           => $postData['nama_ecommerce'] ? $postData['nama_ecommerce'] : "",
                    "order_date"            => $orderDate,
                    'total_harga' => $total_harga,
                    "keterangan" => $postData['parent_keterangan'],
                    "id_company"            => $this->this_company_id != 16 ? $this->request->getVar('company_id') : $this->this_company_id,
                    'qty_barang' => $totalQty,
                    'ppn' => $status_ppn,
                    'no_po' => $this->request->getVar('no_po'),
                    'estimated_freight' => $this->request->getVar('estimated_freight')
                ]
            );

            $this->SalesOrderModel->db->transComplete();

            $data = [
                "id"        => encrypt($id),
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
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

    public function delete()
    {
        try {
            // $id = $this->request->getPost("id");
            $id = decrypt($this->request->getPost("id"));
            // var_dump($id);
            // die();

            if (!empty($id)) {



                $checkSJ = $this->SalesOrderModel
                    ->where('id', $id)
                    ->where('surat_jalan_so_id !=', null)
                    ->orWhere('sales_order_invoice_id !=', null)
                    ->where('id', $id)
                    ->orWhere('used !=', "NOT USED")
                    ->where('id', $id)
                    ->first();



                if ($checkSJ) {
                    $data = [
                        "status"     => false,
                        "message"    => "Data Order sudah digunakan tidak dapat dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }
                $this->SalesOrderModel->delete($id);
                $dataDetail = $this->SalesOrderDetailModel->where('id_sales_order', $id)->findAll();

                foreach ($dataDetail as $item) {
                    $this->SalesOrderDetailModel->delete($item['id']);
                }

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

    public function deleteOrderForm()
    {
        $id = ($this->request->getVar('id'));

        $getBarangSalesOrderDetail = $this->SalesOrderDetailModel->select('id_sales_order, id_barang')->where('id', $id)->first();

        if ($getBarangSalesOrderDetail['id_barang'] == "85") {
            return response()->setJSON([
                'message' => "Kemasan Default Tidak Boleh Dihapus",
                'token' => csrf_hash(),
                'status' => false
            ]);
        }

        $this->SalesOrderDetailModel->delete($id);

        $getAllBarangSalesOrderDetail = $this->SalesOrderDetailModel->select('qty, harga_barang, discount_percentage, amount')->where('id_sales_order', $getBarangSalesOrderDetail['id_sales_order'])->where('id_barang !=', 85)->findAll();

        $totalQty = 0;
        $total_harga = 0;
        foreach ($getAllBarangSalesOrderDetail as $row) {
            $totalQty = $totalQty + $row['qty'];
            $amountValue = $row['amount'] ? (float) str_replace(",", "", $row['amount']) : 0;
            $total_harga +=  $row['discount_unit'] == "percent" ? ($amountValue - ($amountValue * ($row['discount_percentage'] / 100))) : ($amountValue - $row['discount_percentage']);
        }
        //qty barang ditambah jumlah kemasan
        $this->SalesOrderModel->update($getBarangSalesOrderDetail['id_sales_order'], ['qty_barang' => ($totalQty * 2), 'total_harga' => $total_harga]);




        // $this->workOrderDetailsModel->where('work_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "Detail Barang Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getAllBarang()
    {
        //Get Barang
        /*$responseBarang = curl_request("GET", "/barang/all?idCompany=$this->this_company_id", $this->token);

        $dataBarang = [];
        if ($responseBarang["code"] === 200) {
            $dataBarang = json_decode($responseBarang["body"])->data;
        }*/
        $dataBarang = $this->BarangMasterSalesModel
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'left')
            ->select('barang_master_sales.*')
            ->select('barang_master_sales.id as id_barang')
            ->select('barang_master_sales.kode_barang as kode_barang')
            ->select('barang_master_sales.barang_name as nama_barang')
            ->select('barang_master_sales.harga_pokok as harga_pokok')
            ->select('barang_master_sales.harga_jual as harga_jual')
            ->select('barang_master_sales.status_ppn as statusppn')
            ->select('satuans.kode_satuan as kode_satuan')
            ->select('satuans.nama_satuan as nama_satuan')
            ->where('type_barang_sales', 'LOKAL')
            ->where('barang_master_sales.deletedAt', null)
            // ->where('barang_master_sales.company_id', $this->this_company_id)
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

        $id = decrypt($id);

        $soSelectQry = "sales_order.*,
                        DATE_FORMAT(sales_order.order_date, '%d %b %Y') AS order_date, 
                        DATE_FORMAT(sales_order.shipping_date, '%d %b %Y') AS shipping_date, 
                        customers.name AS customerName, 
                        customers.phone AS customerPhone, 
                        customers.address AS customerAddress,
                        metadata.value AS termin,
                        companies.company";
        $salesOrderData = $this->SalesOrderModel->asObject()
            ->select($soSelectQry)
            ->join('customers', 'customers.id = sales_order.id_customer', 'left')
            ->join('metadata', 'metadata.id = sales_order.payment_terms', 'left')
            ->join('companies', 'companies.id = sales_order.id_company', 'left')
            ->find($id);

        $soDet = $this->SalesOrderDetailModel->asObject()
            ->select('barang_master_sales.barang_name AS namaBarang, barang_master_sales.kode_barang AS kodeBarang, sales_order_detail.qty AS qty, satuans.kode_satuan AS kodeSatuan')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id')
            ->where('tipe_input', "order_form")
            ->where('id_sales_order', $id)
            ->findAll();

        $data = [
            'companyName'   => $salesOrderData->company,
            'soData'        => $salesOrderData,
            'soDet'         => $soDet
        ];


        $this->SalesOrderModel->update($id, ['counter_print' => $salesOrderData->counter_print + 1]);






        // return view('SalesLokal/OrderForm/print', $data);

        // load HTML content
        $domPdf->loadHtml(view('SalesLokal/OrderForm/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('A4', 'landscape');

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
    }

    public function generateNomorSalesOrder()
    {
        $code = "TSI";
        $currentYear = date('y'); // 2 digit
        $currentMonth = date('n'); // 1-12
        $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $numberTemplate = $code . "/" . $romawi[$currentMonth] . "/" . $currentYear . "/";

        $listData = $this->SalesOrderModel->asObject()
            ->like('no_sales_order', $numberTemplate)
            ->orderBy('no_sales_order', 'ASC') // penting: ASC buat gap detect
            ->findAll();

        $existingNumbers = [];

        foreach ($listData as $data) {
            $parts = explode('/', $data->no_sales_order);
            if (isset($parts[3]) && is_numeric($parts[3])) {
                $existingNumbers[] = intval($parts[3]);
            }
        }

        sort($existingNumbers);

        $nextNumber = 1;
        $foundGap = false;

        foreach ($existingNumbers as $num) {
            if ($num != $nextNumber) {
                $foundGap = true;
                break;
            }
            $nextNumber++;
        }

        if (!$foundGap) {
            $nextNumber = empty($existingNumbers) ? 1 : end($existingNumbers) + 1;
        }

        $paddedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $invNumber = $numberTemplate . $paddedNumber;

        return response()->setJSON([
            'data' => $invNumber,
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

    public function HistoriHargaBarang()
    {
        $id = decrypt($this->request->getVar('id'));
        $id_user = $this->userId;
        $listHargaBarang = [];
        $dataSalesOrder = $this->SalesOrderModel
            ->join('customers', 'customers.id = sales_order.id_customer', 'left')
            ->find($id);
        $listBarang = $this->SalesOrderDetailModel
            ->select('sales_order_detail.id AS id_sales_order_detail, sales_order_detail.*, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang', 'left')
            ->where('sales_order_detail.id_sales_order', $id)
            ->where('sales_order_detail.deletedAt', null)
            ->findAll();

        foreach ($listBarang as $key => $value) {
            $listBarang2 = $this->SalesOrderDetailModel
                ->select('sales_order_detail.id AS id_sales_order_detail, sales_order_detail.*, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang')
                ->join('sales_order', 'sales_order.id = sales_order_detail.id_sales_order', 'left')
                ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang', 'left')
                ->where('sales_order.id_user', $id_user)
                ->where('sales_order.id_customer', $dataSalesOrder['id_customer'])
                ->where('sales_order_detail.id_barang', $value['id_barang'])
                ->where('sales_order_detail.deletedAt', null)
                ->findAll();
            // Menggabungkan array $listBarang2 ke $listHargaBarang
            $listHargaBarang = array_merge($listHargaBarang, $listBarang2);
        }

        return response()->setJSON([
            'sales_order' => $dataSalesOrder,
            'listBarang' => $listBarang,
            'list_barang' => $listHargaBarang,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownCustomer()
    {
        $dataCustomer = $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin);
        return response()->setJSON([
            'data' => $dataCustomer,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function posting()
    {
        try {

            $id = $this->request->getVar('id');
            if (is_numeric($id)) {
                $id = $id;
            } else {
                $id = decrypt($id);
            }

            // po posting
            $payload = [
                "posting" => $this->request->getVar('status_posting'),
            ];

            $this->SalesOrderModel->update($id, $payload);

            $data = [
                "status"    => true,
                "message"   => "Order Form Berhasil Diperbaruhi",
                "payload"   => json_encode($payload),
                'token'     => csrf_hash()
            ];

            echo json_encode($data);
        } catch (Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function checkPiutang($id)
    {
        $dataCustomer = $this->CustomerModel
            ->find($id);

        $dataSalesOrder = $this->salesOrderInvoiceModel
            ->where('id_customer', $id)
            ->findAll();

        $totalNotPay = 0;
        foreach ($dataSalesOrder as $key => $value) {
            $totalNotPay += $value['total_invoice'] - $value['pay_amount'];
        }

        $data = [
            'id' => $id,
            'status' => true
        ];

        if ($dataCustomer['piutang'] == '0' || $dataCustomer['piutang'] == null) {
            $data['status'] = true;
        } else {
            if ($dataCustomer['piutang'] == $totalNotPay) {
                $data['status'] = false;
            } else {
                $data['status'] = true;
            }
        }
        echo json_encode($data);
        return;
    }
}
