<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

use App\Models\ProvincesModel;
use App\Models\CustomerModel;
use App\Models\BanksModel;
use App\Models\ListAddressesModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderInvoiceModel;

class Customer extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $ProvincesModel;
    protected $CustomerModel;
    protected $BanksModel;
    protected $ListAddressesModel;
    protected $SalesOrderModel;
    private $soInvModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->ProvincesModel = new ProvincesModel();
        $this->CustomerModel = new CustomerModel();
        $this->BanksModel = new BanksModel();
        $this->ListAddressesModel = new ListAddressesModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->soInvModel = new SalesOrderInvoiceModel();
    }

    public function customer()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataBanks = $this->BanksModel->search_list(array(), 'name');

        $data = [
            "dataProvinces" => $dataProvinces,
            "dataBanks" => $dataBanks,
        ];



        return view('Master/customer/index', $data);
    }

    public function allCustomer()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $customerData = $this->CustomerModel->getList($condition, $addCondition, $limit, $offset);

        $dataCustomer = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($customerData['data'] as $data) {
            array_push($dataCustomer, [
                "no"            => $no++,
                "id"            => $data->id,
                "kode"          => $data->kode,
                "name"          => $data->name,
                "phone"         => $data->phone,
                "contact_person" => $data->contact_person,
                "saldo"         => number_format($data->saldo),
                "currencyName"  => $data->currencyName,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $customerData['totalData'],
            "recordsFiltered"   => $customerData['totalFilteredData'],
            "data"              => $dataCustomer,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    // public function allCustomer()
    // {

    //     $draw = $this->request->getVar('draw');
    //     $row = $this->request->getVar('start');
    //     $rowperpage = $this->request->getVar('length');
    //     $temp = $this->request->getVar('order');
    //     $columnIndex = $temp[0]['column']; // Column index

    //     $temp = $this->request->getVar('columns');
    //     $columnName = $temp[$columnIndex]['data']; // Column index

    //     $temp = $this->request->getVar('order');
    //     $columnSortOrder = $temp[0]['dir']; // Column index

    //     $search = $this->request->getVar('search');
    //     //$searchValue = $temp['value']; // Column index

    //     $values = [
    //         "company_id"    => $this->this_company_id,
    //         "search"        => $search
    //     ];

    //     $totalRecords = $this->CustomerModel->total_list(array());
    //     $totalRecordwithFilter = $this->CustomerModel->total_list($values);

    //     $res = $this->CustomerModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

    //     $number = $row * $rowperpage;

    //     $data = [];

    //     for ($i = 0; $i < count($res); $i++) {

    //         $data[] = array(
    //             "number" => ($row + $i + 1),
    //             "no" => ($row + $i + 1),
    //             "id" => $res[$i]["id"],
    //             "kode" => $res[$i]["kode"],
    //             "name" => $res[$i]["name"],
    //             "phone" => $res[$i]["phone"],
    //             "contact_person" => $res[$i]["contact_person"],
    //             "saldo" => $res[$i]["saldo"],
    //             "currencyName" => $res[$i]["currencyName"],
    //         );
    //     }

    //     ## Response
    //     $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecordwithFilter,
    //         "aaData" => $data
    //     );

    //     return $this->response->setJSON($response);
    // }

    public function saveCustomer()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong'
                    ]
                ],
                "address" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Alamat tidak boleh kosong'
                    ]
                ],
                "email" => [
                    "rules" => "permit_empty|valid_email",
                    'errors' => [
                        'valid_email' => 'Email harus valid'
                    ]
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
                $last_year = date("Y-m-t", strtotime(date('Y') . "-12-31"));
                $kode = $this->CustomerModel->get_kode(date('m'), date('Y'), date('y'), $last_year);
                $values = [
                    "company_id" => $this->this_company_id,
                    "kode" => $kode,
                    "nik" => $this->request->getPost("nik"),
                    "name" => $this->request->getPost("name"),
                    "address" => $this->request->getPost("address"),
                    "no_npwp" => $this->request->getPost("no_npwp"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
                    "email" => $this->request->getPost("email"),
                    "postal_code" => $this->request->getPost("parent_postal_code"),
                    "province_id" => $this->request->getPost("province_parent_id"),
                    "city_id" => $this->request->getPost("city_parent_id"),
                    "tipe_pelanggan" => $this->request->getPost("tipe_pelanggan"),
                    "nik" => $this->request->getPost("nik"),
                    "termin" => $this->request->getPost("termin"),
                    "currency" => $this->request->getPost("currency"),
                    "sales_id" => $this->request->getPost("sales_id")
                ];

                $id = $this->CustomerModel->insert($values);
                if ($id > 0) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
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

    public function updateCustomer()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong'
                    ]
                ],
                "address" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Alamat tidak boleh kosong'
                    ]
                ],
                "email" => [
                    "rules" => "valid_email",
                    'errors' => [
                        'valid_email' => 'Email harus valid'
                    ]
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
                $payload = '';

                $id = $this->request->getPost("id");

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("name"),
                    "nik" => $this->request->getPost("nik"),
                    "address" => $this->request->getPost("address"),
                    "no_npwp" => $this->request->getPost("no_npwp"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
                    "email" => $this->request->getPost("email"),
                    "postal_code" => $this->request->getPost("parent_postal_code"),
                    "province_id" => $this->request->getPost("province_parent_id"),
                    "city_id" => $this->request->getPost("city_parent_id"),
                    "tipe_pelanggan" => $this->request->getPost("tipe_pelanggan"),
                    "nik" => $this->request->getPost("nik"),
                    "termin" => $this->request->getPost("termin"),
                    "currency" => $this->request->getPost("currency"),
                    "sales_id" => $this->request->getPost("sales_id")
                ];
                if ($this->CustomerModel->update($id, $values)) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
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

    public function getByIdCustomer($id = null)
    {
        if (!empty($id)) {
            $res = $this->CustomerModel->get_by_id($id, '1');

            if (count($res) > 0) {
                $data = [
                    "status"  => true,
                    "data"    => $res[0]
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditampilkan';
                $data = [
                    "status" => false,
                    "message"  => $message
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Tidak Ada Id"
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteCustomer()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $res_list = $this->ListAddressesModel->get_by_customer_id($id);
                for ($i = 0; $i < count($res_list); $i++) {
                    $values = [
                        "deletedAt" => date("Y-m-d H:i:s")
                    ];
                    $this->ListAddressesModel->update($res_list[$i]["id"], $values);
                }
                if ($this->CustomerModel->update($id, $values)) {
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

    public function getLocalInvoiceList($id)
    {
        // $soList = $this->SalesOrderModel
        //     ->select('id, no_sales_order')
        //     ->where('id_customer', $id)
        //     ->where('tipe_sales_order', 'LOKAL')
        //     ->findAll();

        $invList = $this->soInvModel->select("id, no_faktur")
            ->where('id_customer', $id)
            ->where('tipe_invoice', 'LOKAL')
            ->findAll();

        $customerData = $this->CustomerModel->asObject()
            ->select('customers.address, CONCAT(employees.nip , " - ", employees.name) AS salesName')
            ->join('employees', 'employees.id = customers.sales_id ')
            ->find($id);

        $data = [
            'invList'   => $invList,
            'address'   => $customerData->address,
            'salesName' => $customerData->salesName
        ];
        echo json_encode($data);
    }
}
