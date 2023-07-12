<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ProvincesModel;
use App\Models\CustomerModel;

class Customer extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $ProvincesModel;
    protected $CustomerModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->ProvincesModel = new ProvincesModel();
        $this->CustomerModel = new CustomerModel();
    }

    public function customer()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');

        $data = [
            "dataProvinces" => $dataProvinces,
        ];

        return view('Master/customer/index', $data);
    }

    public function allCustomer()
    {

        $draw = $this->request->getVar('draw');
        $row = $this->request->getVar('start');
        $rowperpage = $this->request->getVar('length');
        $temp = $this->request->getVar('order');
        $columnIndex = $temp[0]['column']; // Column index

        $temp = $this->request->getVar('columns');
        $columnName = $temp[$columnIndex]['data']; // Column index

        $temp = $this->request->getVar('order');
        $columnSortOrder = $temp[0]['dir']; // Column index

        $search = $this->request->getVar('search');
        //$searchValue = $temp['value']; // Column index

        $values = [
            "company_id"    => $this->this_company_id,
            "search"        => $search
        ];

        $totalRecords = $this->CustomerModel->total_list(array());
        $totalRecordwithFilter = $this->CustomerModel->total_list($values);

        $res = $this->CustomerModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "number" => ($row + $i + 1),
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "kode" => $res[$i]["kode"],
                "name" => $res[$i]["name"],
                "address" => $res[$i]["address"],
                "province_name" => $res[$i]["province_name"],
                "city_name" => $res[$i]["city_name"],
                "postal_code" => $res[$i]["postal_code"],
                "no_npwp" => $res[$i]["no_npwp"],
                "phone" => $res[$i]["phone"],
                "contact_person" => $res[$i]["contact_person"],
                "email" => $res[$i]["email"],
                "no_rekening" => $res[$i]["no_rekening"],
                "supplier_buyer" => $res[$i]["supplier_buyer"],
                "ap_name" => $res[$i]["ap_name"],
                "ar_name" => $res[$i]["ar_name"]
            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        // header('Access-Control-Allow-Origin: *');
        // header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        // header('Access-Control-Max-Age: 86400');
        // header("Access-Control-Expose-Headers: Content-Length, X-JSON");
        // header("Access-Control-Allow-Headers: *");

        //echo json_encode($response);
        //return;
        return $this->response->setJSON($response);

        /*
        print_r($this->request);
        exit;
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this->this_company_id
        ];

        $response = curl_request("GET", "/customers", $this->token, $payload);
        $dataCustomer = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataCustomer, [
                    "no" => $no++,
                    "id" => $data->id,
                    "kode" => $data->kode,
                    "name" => $data->name,
                    "address" => $data->address,
                    "province_name" => $data->province_name,
                    "city_name" => $data->city_name,
                    "postal_code" => $data->postal_code,
                    "no_npwp" => $data->no_npwp,
                    "phone" => $data->phone,
                    "contact_person" => $data->contact_person,
                    "email" => $data->email,
                    "no_rekening" => $data->no_rekening,
                    "supplier_buyer" => $data->supplier_buyer,
                    "ap_name" => $data->ap_name,
                    "ar_name" => $data->ar_name
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataCustomer,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
        */
    }

    public function saveCustomer()
    {
        try {
            $rules = [
                "kode" => [
                    "rules" => "required"
                ],
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "required"
                ],
                "no_npwp" => [
                    "rules" => "required"
                ],
                "phone" => [
                    "rules" => "required"
                ],
                "contact_person" => [
                    "rules" => "required"
                ],
                "email" => [
                    "rules" => "required"
                ],
                "no_rekening" => [
                    "rules" => "required"
                ],
                "supplier_buyer" => [
                    "rules" => "required"
                ],
                "province_parent_id" => [
                    "rules" => "required"
                ],
                "city_parent_id" => [
                    "rules" => "required"
                ],
                "ap_id" => [
                    "rules" => "required"
                ],
                "ar_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                /*
                $payload = json_encode([
                    "company_id" => $this->this_company_id,
                    "kode" => $this->request->getPost("kode"),
                    "name" => $this->request->getPost("name"),
                    "address" => $this->request->getPost("address"),
                    "no_npwp" => $this->request->getPost("no_npwp"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
                    "email" => $this->request->getPost("email"),
                    "no_rekening" => $this->request->getPost("no_rekening"),
                    "supplier_buyer" => $this->request->getPost("supplier_buyer"),
                    "province_id" => $this->request->getPost("province_parent_id"),
                    "city_id" => $this->request->getPost("city_parent_id"),
                    "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    "list_address" => json_decode($this->request->getPost("list_address"))
                ]);

                $response = curl_request("POST", "/customers", $this->token, $payload);
                */
                $values = [
                    "company_id" => $this->this_company_id,
                    "kode" => $this->request->getPost("kode"),
                    "name" => $this->request->getPost("name"),
                    "address" => $this->request->getPost("address"),
                    "no_npwp" => $this->request->getPost("no_npwp"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
                    "email" => $this->request->getPost("email"),
                    "no_rekening" => $this->request->getPost("no_rekening"),
                    "supplier_buyer" => $this->request->getPost("supplier_buyer"),
                    "province_id" => $this->request->getPost("province_parent_id"),
                    "city_id" => $this->request->getPost("city_parent_id"),
                    "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    "list_address" => json_decode($this->request->getPost("list_address"))
                ];

                if ($this->CustomerModel->insert($values)) {
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
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Disimpan",
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

    public function updateCustomer()
    {
        try {
            $rules = [
                "kode" => [
                    "rules" => "required"
                ],
                "name" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "required"
                ],
                "no_npwp" => [
                    "rules" => "required"
                ],
                "phone" => [
                    "rules" => "required"
                ],
                "contact_person" => [
                    "rules" => "required"
                ],
                "email" => [
                    "rules" => "required"
                ],
                "no_rekening" => [
                    "rules" => "required"
                ],
                "supplier_buyer" => [
                    "rules" => "required"
                ],
                "province_parent_id" => [
                    "rules" => "required"
                ],
                "city_parent_id" => [
                    "rules" => "required"
                ],
                "ap_id" => [
                    "rules" => "required"
                ],
                "ar_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = '';

                $id = $this->request->getPost("id");


                $payload = json_encode([
                    "company_id" => $this->this_company_id,
                    "kode" => $this->request->getPost("kode"),
                    "name" => $this->request->getPost("name"),
                    "address" => $this->request->getPost("address"),
                    "no_npwp" => $this->request->getPost("no_npwp"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
                    "email" => $this->request->getPost("email"),
                    "no_rekening" => $this->request->getPost("no_rekening"),
                    "supplier_buyer" => $this->request->getPost("supplier_buyer"),
                    "province_id" => $this->request->getPost("province_parent_id"),
                    "city_id" => $this->request->getPost("city_parent_id"),
                    "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    "list_address" => json_decode($this->request->getPost("list_address"))
                ]);
            }

            if ($payload) {
                $response = curl_request("PATCH", "/customers/$id", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
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
            $response = curl_request("GET", "/customers/$id?idCompany=$this->this_company_id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditampilkan';
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
                $response = curl_request("DELETE", "/customers/$id", $this->token);
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
}
