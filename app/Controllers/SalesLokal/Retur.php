<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use Config\Services;

use App\Models\AllNoMOdel;
use App\Models\CustomerModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderReturnModel;

class Retur extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;

    private $AllNoModel;
    private $customerModel;
    private $soModel;
    private $soReturnModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();

        $this->AllNoModel = new AllNoMOdel();
        $this->customerModel = new CustomerModel();
        $this->soModel = new SalesOrderModel();
        $this->soReturnModel = new SalesOrderReturnModel();
    }

    public function index()
    {
        return view('SalesLokal/Retur/index');
    }

    public function createView()
    {
        $customerList = $this->customerModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->findAll();
        
        $data = [
            'dataCustomers' => $customerList
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

        $condition = ['sales_order_returns.deletedAt' => null];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];


        $returnData = $this->soReturnModel
            ->getAllSOReturn($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataReturn = [];

        foreach ($returnData['data'] as $data) {
            array_push($dataReturn, [
                "no"            => $no++,
                "id"            => $data->id,
                "returnNo"      => $data->returnNo,
                "customerName"  => $data->customerName,
                "salesOrderNo"  => $data->salesOrderNo,
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
        $returnedItems = json_decode($this->request->getPost("returnedItems"));

        $postData = $this->request->getPost();
        $postData["returnedItems"] = json_decode($postData["returnedItems"], true);

        $rules = [
            "id_customer" => [
                "rules" => "required|numeric",
                'errors' => [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "id_so" => [
                "rules" => "required|numeric",
                "errors" => [
                    "required" => 'Sales Order tidak boleh kosong!'
                ]
            ],
            "return_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
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
                    'required' => 'barang tidak boleh kosong',
                ],
            ],
            "returnedItems.*.id" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'id barang tidak boleh kosong',
                ],
            ],
            "returnedItems.*.returnQty" => [
                "rules" => "required|numeric|greater_than_equal_to[0]",
                'errors' => [
                    'required' => 'Qty barang tidak boleh kosong',
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

        $returnedItem = [];

        foreach ($returnedItems as $item) {

            if ($item->returnQty > 0) {
                $returnedItem[] = $item;
            }
        }

        $code = "SR";
        $currentYear = date('Y');
        $currentMonth = date('m');
        $monthName = date("F", mktime(0, 0, 0, $currentMonth, 10));
        $number = $this->AllNoModel->getNumber($code, $monthName . " " . $currentYear);
        $noReturn = "TSI/$code/$currentMonth/$currentYear/$number";

        $returnDate = $postData['return_date'];

        try {

            $values = [
                "return_no"     => $noReturn,
                "return_date"   => date("Y-m-d", strtotime(str_replace("/", "-", $returnDate))),
                "customer_id"   => $postData['id_customer'],
                "sales_order_id"=> $postData['id_so'],
                "note"          => $postData['note'],
                "returned_item" => json_encode($returnedItem)
            ];
            $id =  $this->soReturnModel->insert($values);

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
        $customerList = $this->customerModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->findAll();

        $selectQry = "sales_order_returns.*, 
                      DATE_FORMAT(sales_order_returns.return_date, '%d/%m/%Y') AS return_date,
                      customers.address AS customerAddress";
        $returnData = $this->soReturnModel->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_returns.customer_id')
            ->find($id);

        $salesOrderData = $this->soModel->asObject()
            ->find($returnData->sales_order_id);

        $returnData->itemList = json_decode($returnData->returned_item);
        
        $data = [
            'data'          => $returnData,
            'dataCustomers' => $customerList,
            'salesOrderData'=> $salesOrderData
        ];
        return view('SalesLokal/Retur/form', $data);
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
