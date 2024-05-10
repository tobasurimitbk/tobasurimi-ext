<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

use App\Models\ProvincesModel;
use App\Models\CustomerModel;
use App\Models\BanksModel;
use App\Models\CompaniesModel;
use App\Models\CountryModel;
use App\Models\EmployeesModel;
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
    protected $employessModel;
    protected $soInvModel;
    protected $CompanyModel;
    protected $countryModel;

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
        $this->employessModel = new EmployeesModel();
        $this->countryModel = new CountryModel();
        $this->CompanyModel = new CompaniesModel();
    }

    public function customer()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataBanks = $this->BanksModel->search_list(array(), 'name');
        $dataCountry = $this->countryModel->findAll();

        $data = [
            "dataProvinces" => $dataProvinces,
            "dataBanks" => $dataBanks,
            "dataCountry" => $dataCountry,
            "dataCompany" => $this->CompanyModel->where('deletedAt', null)->orderBy('company', "ASC")->findAll()
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

        $condition = [
            'tipe_customer' => $this->request->getGet('customerType'),
            'customers.deletedAt' => null,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->request->getGet("company_id"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $customerData = $this->CustomerModel->getList($condition, $addCondition, $limit, $offset);

        $dataCustomer = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($customerData['data'] as $data) {
            array_push($dataCustomer, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "kode"          => $data->kode,
                "companyName"   => $data->companyName,
                "namaSales"     => $data->namaSales,
                "name"          => $data->name,
                "phone"         => $data->phone,
                "contact_person" => $data->contact_person,
                "saldo"         => number_format($data->saldo),
                "currencyName"  => $data->currencyName,
                "countryName"   => $data->countryName,
                "address"       => $data->address
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $customerData['totalData'],
            "recordsFiltered"   => $customerData['totalFilteredData'],
            "data"              => $dataCustomer,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveCustomer()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required|min_length[3]",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong',
                        'min_length' => 'Nama harus memiliki minimal 3 karakter'
                    ]
                ],

                "email" => [
                    "rules" => "permit_empty|valid_email",
                    'errors' => [
                        'valid_email' => 'Email harus valid'
                    ]
                ],
                "no_npwp" => [
                    'rules' => 'permit_empty|min_length[15]',
                    'errors' => [
                        'min_length' => 'Nomor NPWP harus diisi minimal 15 digit'
                    ]
                ],
                "phone" => [
                    'rules' => 'permit_empty|min_length[12]',
                    'errors' => [
                        'min_length' => 'Nomor HP harus diisi minimal 12 digit'
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
                $piutangValue = $this->request->getPost("piutang") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $this->request->getPost("piutang"))) : 0;
                $values = [
                    "company_id" => $this->this_company_id,
                    "kode" => $kode,
                    "nik" => $this->request->getPost("nik"),
                    "name" => strtoupper($this->request->getVar("name")),
                    "address" => strtoupper($this->request->getVar("address")),
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
                    "piutang" => number_format($piutangValue, 2, '.', ''),
                    "currency" => $this->request->getPost("currency"),
                    "country_id" => $this->request->getPost('country_id'),
                    "tipe_customer" => $this->request->getPost("tipe_customer"),
                    "jenis_penjualan" => $this->request->getPost("jenis_penjualan"),
                    "sales_id" => session()->get('login')->user_id
                ];
                // var_dump($values);
                // exit;

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
                    "rules" => "required|min_length[3]",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong',
                        'min_length' => 'Nama harus memiliki minimal 3 karakter'
                    ]
                ],

                "email" => [
                    "rules" => "permit_empty|valid_email",
                    'errors' => [
                        'valid_email' => 'Email harus valid'
                    ]
                ],
                "no_npwp" => [
                    'rules' => 'permit_empty|min_length[15]',
                    'errors' => [
                        'min_length' => 'Nomor NPWP harus diisi minimal 15 digit'
                    ]
                ],
                "phone" => [
                    'rules' => 'permit_empty|min_length[12]',
                    'errors' => [
                        'min_length' => 'Nomor HP harus diisi minimal 12 digit'
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

                $id = decrypt($this->request->getPost("id"));

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => strtoupper($this->request->getVar("name")),
                    "address" => strtoupper($this->request->getVar("address")),
                    "nik" => $this->request->getPost("nik"),
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
                    "country_id" => $this->request->getPost('country_id'),
                    "tipe_customer" => $this->request->getPost("tipe_customer"),
                    "jenis_penjualan" => $this->request->getPost("jenis_penjualan"),
                    "sales_id" => session()->get('login')->user_id
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
            $id = decrypt($id);
            $res = $this->CustomerModel->get_by_id($id, '1');
            $country =  $this->countryModel->where('id', $res[0]['country_id'])->first();
            $res[0]['id'] = encrypt($res[0]['id']);
            $res[0]['country_id'] = $country == null ? 0 : $country['id'];

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
            $id = decrypt($this->request->getPost("id"));

            if (!empty($id)) {
                $res_list = $this->ListAddressesModel->get_by_customer_id($id);
                for ($i = 0; $i < count($res_list); $i++) {
                    $values = [
                        "deletedAt" => date("Y-m-d H:i:s")
                    ];
                    $this->ListAddressesModel->update($res_list[$i]["id"], $values);
                }
                // handle if null
                if (count($res_list) == 0) {
                    $values = [
                        "deletedAt" => date("Y-m-d H:i:s")
                    ];
                    $this->ListAddressesModel->update($id, $values);
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
