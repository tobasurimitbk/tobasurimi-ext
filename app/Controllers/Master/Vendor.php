<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ProvincesModel;
use App\Models\VendorModel;
use App\Models\BanksModel;
use App\Models\ListAddressesModel;

class Vendor extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $ProvincesModel;
    protected $VendorModel;
    protected $BanksModel;
    protected $ListAddressesModel;


    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->ProvincesModel = new ProvincesModel();
        $this->VendorModel = new VendorModel();
        $this->BanksModel = new BanksModel();
        $this->ListAddressesModel = new ListAddressesModel();
    }

    public function vendor()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');

        $data = [
            "dataProvinces" => $dataProvinces,
        ];


        return view('Master/vendors/index', $data);
    }

    public function allVendor()
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

        $totalRecords = $this->VendorModel->total_list(array());
        $totalRecordwithFilter = $this->VendorModel->total_list($values);

        $res = $this->VendorModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

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

        return $this->response->setJSON($response);
    }

    public function saveVendor()
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
                // "province_parent_id" => [
                //     "rules" => "required"
                // ],
                // "city_parent_id" => [
                //     "rules" => "required"
                // ],
                "ap_id" => [
                    "rules" => "required"
                ],
                "ar_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "company_id" => $this->this_company_id,
                    "kode" => $this->request->getPost("kode"),
                    "name" => $this->request->getPost("name"),
                    "address" => $this->request->getPost("address"),
                    "no_npwp" => $this->request->getPost("no_npwp"),
                    "phone" => $this->request->getPost("phone"),
                    "contact_person" => $this->request->getPost("contact_person"),
                    "email" => $this->request->getPost("email"),
                    "bank_id" => $this->request->getPost("bank_id"),
                    "nama_rekening" => $this->request->getPost("nama_rekening"),
                    "no_rekening" => $this->request->getPost("no_rekening"),
                    "supplier_buyer" => $this->request->getPost("supplier_buyer"),
                    "province_id" => $this->request->getPost("province_parent_id"),
                    "city_id" => $this->request->getPost("city_parent_id"),
                    "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    "list_address" => json_decode($this->request->getPost("list_address"))
                ];
                $id = $this->VendorModel->insert($values);
                if ($id > 0) {
                    $dlist_address = json_decode($this->request->getPost("list_address"), true);
                    for ($i = 0; $i < count($dlist_address); $i++) {
                        $values = [
                            "vendor_id"   => $id,
                            "address"       => $dlist_address[$i]["address"],
                            "province_id"   => isset($dlist_address[$i]["province_id"]) ? $dlist_address[$i]["province_id"] : "",
                            "city_id"       => isset($dlist_address[$i]["city_id"]) ? $dlist_address[$i]["city_id"] : "",
                            "postal_code"   => isset($dlist_address[$i]["postal_code"]) ? $dlist_address[$i]["postal_code"] : "",
                            "main_address"  => isset($dlist_address[$i]["main_address"]) ? $dlist_address[$i]["main_address"] : "0",
                        ];
                        $this->ListAddressesModel->insert($values);
                    }

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

    public function updateVendor()
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
                // "province_parent_id" => [
                //     "rules" => "required"
                // ],
                // "city_parent_id" => [
                //     "rules" => "required"
                // ],
                "ap_id" => [
                    "rules" => "required"
                ],
                "ar_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

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

                if ($this->VendorModel->update($id, $values)) {
                    $dlist_address = json_decode($this->request->getPost("list_address"), true);

                    for ($i = 0; $i < count($dlist_address); $i++) {

                        $values = [
                            "vendor_id"   => $id,
                            "address"       => $dlist_address[$i]["address"],
                            "province_id"   => isset($dlist_address[$i]["province_id"]) ? $dlist_address[$i]["province_id"] : "",
                            "city_id"       => isset($dlist_address[$i]["city_id"]) ? $dlist_address[$i]["city_id"] : "",
                            "postal_code"   => isset($dlist_address[$i]["postal_code"]) ? $dlist_address[$i]["postal_code"] : "",
                            "main_address"  => isset($dlist_address[$i]["main_address"]) ? $dlist_address[$i]["main_address"] : "0",
                        ];

                        if (isset($dlist_address[$i]["isDelete"])) {
                            if ($dlist_address[$i]["isDelete"] == 1) {
                                $values = [
                                    "deletedAt" => date("Y-m-d H:i:s")
                                ];

                                $this->ListAddressesModel->update($dlist_address[$i]["id"], $values);
                            }
                        } else {
                            if (isset($dlist_address[$i]["id"])) {
                                $this->ListAddressesModel->update($dlist_address[$i]["id"], $values);
                            } else {
                                $this->ListAddressesModel->insert($values);
                            }
                        }
                    }
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

    public function getByIdVendor($id = null)
    {
        if (!empty($id)) {
            $res = $this->VendorModel->get_by_id($id, '1');
            if (count($res) > 0) {
                $res_list = $this->ListAddressesModel->get_by_vendor_id($id, '1');
                for ($i = 0; $i < count($res_list); $i++) {
                    $res_list[$i]->province_id = ($res_list[$i]->province_id == null) ? "" : $res_list[$i]->province_id;
                    $res_list[$i]->province_name = ($res_list[$i]->province_name == null) ? "" : $res_list[$i]->province_name;
                    $res_list[$i]->city_id = ($res_list[$i]->city_id == null) ? "" : $res_list[$i]->city_id;
                    $res_list[$i]->city_name = ($res_list[$i]->city_name == null) ? "" : $res_list[$i]->city_name;
                    $res_list[$i]->postal_code = ($res_list[$i]->postal_code == null) ? "" : $res_list[$i]->postal_code;
                }
                $res[0]["list_address"] = $res_list;
                $data = [
                    "status"  => true,
                    "data"    => $res[0]
                    //"data"  => json_decode($response["body"])->data,
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

    public function deleteVendor()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $res_list = $this->ListAddressesModel->get_by_vendor_id($id);
                for ($i = 0; $i < count($res_list); $i++) {
                    $values = [
                        "deletedAt" => date("Y-m-d H:i:s")
                    ];
                    $this->ListAddressesModel->update($res_list[$i]["id"], $values);
                }
                if ($this->VendorModel->update($id, $values)) {
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
}
