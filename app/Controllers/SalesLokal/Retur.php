<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;

use App\Models\AllNoMOdel;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceDetailModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderReturnModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderReturnDetailModel;

class Retur extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;

    private $customerModel;
    private $soModel;
    private $soReturnModel;
    private $soReturnDetailModel;
    private $soInvModel;
    private $soInvDetailModel;
    private $userId;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->userId = session()->get("login")->user_id;

        $this->customerModel = new CustomerModel();
        $this->soModel = new SalesOrderModel();
        $this->soReturnModel = new SalesOrderReturnModel();
        $this->soReturnDetailModel = new SalesOrderReturnDetailModel();
        $this->soInvModel = new SalesOrderInvoiceModel();
        $this->soInvDetailModel = new SalesOrderInvoiceDetailModel();
    }

    public function index()
    {
        return view('SalesLokal/Retur/index');
    }

    public function createView()
    {
        $customerList = $this->customerModel->asObject()
            ->findAll();
        $invoiceList = $this->soInvModel->asObject()
            ->select('sales_order_invoice.*, customers.id as customer_id, customers.name as customer_name, customers.kode as customer_kode, customers.address as customer_address')
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->where('id_sales_order_return', null)
            ->findAll();

        $noReturn = $this->soReturnModel->generateNoReturn();

        foreach ($invoiceList as &$value) {
            $value->id = encrypt($value->id);
        }

        $data = [
            'dataCustomers' => $customerList,
            'dataInvoice' => $invoiceList,
            'noReturn' => $noReturn,
        ];
        return view('SalesLokal/Retur/form', $data);
    }

    public function all()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = [
            "sales_order_return.deletedAt" => null,
            "sales_order_return.id_company" => $this->this_company_id,
            'sales_order_return.id_user' => $this->userId
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];


        $returnData = $this->soReturnModel
            ->getAllReturn($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataReturn = [];

        foreach ($returnData['data'] as $data) {
            array_push($dataReturn, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "returnNo"      => $data->returnNo,
                "customerName"  => $data->customerName,
                "invNo"         => $data->invNo,
                "returnDate"    => date("d/m/Y", strtotime($data->returnDate))
            ]);
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $returnData['totalData'],
            "recordsFiltered" => $returnData['totalFilteredData'],
            "data"            => $dataReturn,
            "payload"         => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        $postData = $this->request->getPost();
        $returnData = json_decode($postData["returnedItems"], true);

        $rules = [
            "id_customer" => [
                "rules" => "required|numeric",
                'errors' => [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "id_invoice" => [
                "rules" => "required",
                "errors" => [
                    "required" => 'Invoice tidak boleh kosong!'
                ]
            ],
            "return_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'Tanggal return tidak boleh kosong',
                ]
            ],
            "note" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "returnedItems" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Barang tidak boleh kosong',
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

        $returnDate = $postData['return_date'];
        $idInvoice = decrypt($postData['id_invoice']);

        try {

            $values = [
                "id_user"             => $this->userId,
                "id_invoice"             => $idInvoice,
                "no_return"             => $postData['no_surat_retur'],
                "note"             => $postData['note'],
                "id_company"        => $this->this_company_id,
                "tanggal_return"           => date("Y-m-d", strtotime(str_replace("/", "-", $returnDate))),
            ];
            $id =  $this->soReturnModel->insert($values);

            $this->soInvModel->update($idInvoice, ['id_sales_order_return' =>  $id]);

            foreach ($returnData as $value) {
                $valueDetail = [
                    'id_sales_order_return'         => $id,
                    'id_barang_return'              => $value['id_barang'],
                    'qty_return'                    => $value['qtyReturn'],
                    'keterangan_return'             => "-",
                    'discount_percentage_return'    => $value['disc'],
                    'harga_barang_return'           => $value['harga_barang'],
                    'tax_return'                    => isset($value['tax']) ? $value['tax'] : 0,
                    'amount_return'                 => $value['amount'],
                ];
                $this->soReturnDetailModel->insert($valueDetail);
            }

            $data = [
                "id"        => $id,
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
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                "payload"   => $values,
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function getById($id)
    {
        $id = decrypt($id);
        $no = 1;
        $customerList = $this->customerModel->asObject()
            ->findAll();

        $invoiceList = $this->soInvModel->asObject()
            ->select('sales_order_invoice.*, customers.id as customer_id, customers.name as customer_name, customers.kode as customer_kode, customers.address as customer_address')
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->findAll();

        $selectQry = "sales_order_return.*, 
                      DATE_FORMAT(sales_order_return.tanggal_return, '%d/%m/%Y') AS return_date,
                      customers.address AS customerAddress,
                      customers.id AS customer_id,";

        $returnData = $this->soReturnModel->asObject()
            ->select($selectQry)
            ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice', 'left')
            ->join('customers', 'customers.id = sales_order_invoice.id_customer', 'left')
            ->find($id);

        $returnDataDetail = $this->soReturnDetailModel->asObject()
            ->select('sales_order_return_detail.qty_return as qtyReturn, sales_order_return_detail.id_barang_return as id_barang, sales_order_return_detail.id as id, sales_order_invoice_detail.qty_invoice as qty, sales_order_return_detail.amount_return as amount, sales_order_return_detail.discount_percentage_return as disc, sales_order_return_detail.harga_barang_return as harga_barang, barang_master_sales.kode_barang as kode_barang, barang_master_sales.barang_name as nama_barang, satuans.kode_satuan as satuan')
            ->join('sales_order_return',  'sales_order_return.id = sales_order_return_detail.id_sales_order_return', 'left')
            ->join('sales_order_invoice_detail',  'sales_order_invoice_detail.id_sales_order_invoice = sales_order_return.id_invoice AND sales_order_invoice_detail.id_barang_invoice = sales_order_return_detail.id_barang_return', 'left')
            ->join('barang_master_sales',  'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice', 'left')
            ->join('satuans',  'satuans.id = barang_master_sales.satuan_id', 'left')
            ->where('sales_order_return_detail.id_sales_order_return',  $id)
            ->where('sales_order_invoice_detail.id_sales_order_invoice',  $returnData->id_invoice)
            ->findAll();

        $invData = $this->soInvModel->asObject()
            ->find($returnData->id_invoice);

        foreach ($returnDataDetail as &$value) {
            $value->no = $no++;
            $value->id_return = encrypt($value->id);
        }

        $returnData->id_invoice = encrypt($returnData->id_invoice);
        $returnData->id = encrypt($returnData->id);

        foreach ($invoiceList as &$value) {
            $value->id = encrypt($value->id);
        }
        $data = [
            'data'          => $returnData,
            'dataDetail'          => $returnDataDetail,
            'dataCustomers' => $customerList,
            'invData'       => $invData,
            'dataInvoice' => $invoiceList,
        ];
        return view('SalesLokal/Retur/form', $data);
    }

    public function update()
    {
        $postData = $this->request->getPost();
        $returnData = json_decode($postData["returnedItems"], true);

        $rules = [
            "id_customer" => [
                "rules" => "required|numeric",
                'errors' => [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "id_invoice" => [
                "rules" => "required",
                "errors" => [
                    "required" => 'Invoice tidak boleh kosong!'
                ]
            ],
            "return_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'Tanggal return tidak boleh kosong',
                ]
            ],
            "note" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "returnedItems" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Barang tidak boleh kosong',
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

        $returnDate = $postData['return_date'];
        $idInvoice = decrypt($postData['id_invoice']);

        try {

            foreach ($returnData as $value) {
                if ($value['id_return']) {
                    $valueDetail = [
                        'id_barang_return'              => $value['id_barang'],
                        'qty_return'                    => $value['qtyReturn'],
                        'keterangan_return'             => "-",
                        'discount_percentage_return'    => $value['disc'],
                        'harga_barang_return'           => $value['harga_barang'],
                        'tax_return'                    => isset($value['tax']) ? $value['tax'] : 0,
                        'amount_return'                 => $value['amount'],
                    ];
                    $this->soReturnDetailModel->update(decrypt($value['id_return']), $valueDetail);
                } else {
                    $valueDetail = [
                        'id_sales_order_return'         => $idInvoice,
                        'id_barang_return'              => $value['id_barang'],
                        'qty_return'                    => $value['qtyReturn'],
                        'keterangan_return'             => "-",
                        'discount_percentage_return'    => $value['disc'],
                        'harga_barang_return'           => $value['harga_barang'],
                        'tax_return'                    => isset($value['tax']) ? $value['tax'] : 0,
                        'amount_return'                 => $value['amount'],
                    ];
                    $this->soReturnDetailModel->insert($valueDetail);
                }
            }

            $data = [
                "id"        => $postData['id_invoice'],
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function delete()
    {
        try {
            $id = decrypt($this->request->getPost("id"));
            if (!empty($id)) {
                $dataDetail = $this->soInvDetailModel->where('id_sales_order_return', $id)->findAll();

                foreach ($dataDetail as $item) {
                    $this->soInvDetailModel->delete($item['id']);
                }
                $this->soInvModel->delete($id);

                $data = [
                    "status"            => true,
                    "message"    => "Data Return Berhasil Dihapus",
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

    public function getInvoiceNumberList($documentId = null)
    {
        $documentList = [];
        $id = decrypt($documentId);
        $no = 1;
        // $documentList = $this->soInvModel->asObject()
        //     ->find($documentId);
        $documentList = $this->soInvDetailModel->asObject()
            ->select('sales_order_invoice_detail.id_barang_invoice as id_barang, sales_order_invoice_detail.id as id, sales_order_invoice_detail.qty_invoice as qty, sales_order_invoice_detail.amount_invoice as amount, sales_order_invoice_detail.discount_percentage_invoice as disc, sales_order_invoice_detail.harga_barang_invoice as harga_barang, barang_master_sales.kode_barang as kode_barang, barang_master_sales.barang_name as nama_barang, satuans.kode_satuan as satuan')
            ->join('barang_master_sales',  'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice')
            ->join('satuans',  'satuans.id = barang_master_sales.satuan_id')
            ->where('id_sales_order_invoice',  $id)
            ->findAll();
        foreach ($documentList as &$value) {
            $value->no = $no++;
        }
        return json_encode($documentList);
    }

    public function getNomorSuratReturn()
    {
        $noReturn = $this->soReturnModel->generateNoReturn();
        return json_encode($noReturn);
    }
}
