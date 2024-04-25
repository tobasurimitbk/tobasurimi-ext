<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\WarehousesModel;
use App\Models\ProvincesModel;
use App\Models\EmployeesModel;
use App\Models\KawasanModel;

class Warehouse extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $WarehousesModel;
    protected $ProvincesModel;
    protected $EmployeesModel;
    protected $DivisisModel;
    protected $KawasanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->WarehousesModel = new WarehousesModel();
        $this->ProvincesModel = new ProvincesModel();
        $this->EmployeesModel = new EmployeesModel();
        $this->DivisisModel = new DivisisModel();
        $this->KawasanModel = new KawasanModel();
    }

    public function warehouse()
    {
        //$dataWarehouses = $this->WarehousesModel->search_list(array("company_id" => $this->this_company_id), 'warehouse_name');
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataPic = $this->EmployeesModel->search_list(array(), 'name');
        $dataDivisi = $this->DivisisModel->getDivisiAccess();
        $dataKawasan = $this->KawasanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll();

        $data = [
            "dataPic" => $dataPic,
            "dataProvinces" => $dataProvinces,
            "dataDivisi" => $dataDivisi,
            "dataKawasan" => $dataKawasan
        ];

        return view('Master/warehouse/index', $data);
    }

    public function allWarehouse()
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
        $divisiID = $this->request->getVar('divisi_search');
        //$searchValue = $temp['value']; // Column index

        $values = [
            "company_id"    => $this->this_company_id,
            "search"        => $search,
            "divisi_id"     => $divisiID
        ];

        $totalRecords = $this->WarehousesModel->total_list(array());
        $totalRecordwithFilter = $this->WarehousesModel->total_list($values);

        $res = $this->WarehousesModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => encrypt($res[$i]["id"]),
                "code_warehouse" => $res[$i]["code_warehouse"],
                "warehouse_name" => $res[$i]["warehouse_name"],
                "divisi_name" => $res[$i]["divisi_name"],
                "address" => $res[$i]["address"],
                "province_id" => $res[$i]["province_id"],
                "city_id" => $res[$i]["city_id"],
                "zip_code" => $res[$i]["zip_code"],
                "phone" => $res[$i]["phone"],
                "email" => $res[$i]["email"],
                "province_name" => $res[$i]["province_name"],
                "city_name" => $res[$i]["city_name"],
                "pic_name" => $res[$i]["pic_name"],
                "kawasan_name" => $res[$i]["kawasan_name"]
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

    public function saveWarehouse()
    {
        try {
            $rules = [
                "code_warehouse" => [
                    "rules" => "required|is_unique[warehouses.code_warehouse]",
                    'errors' => [
                        'required' => 'Warehouse wajib diisi',
                        'is_unique' => 'Kode warehouse sudah ada'
                    ]
                ],
                "warehouse_name" => [
                    "rules" => "required"
                ],
                "phone" => [
                    "rules" => "permit_empty|min_length[10]|max_length[16]",
                    'errors' => [
                        'min_length' => 'Nomor HP harus memiliki panjang minimal 10 digit',
                        'max_length' => 'Nomor HP tidak boleh lebih dari 16 digit'
                    ]
                ],
                "zip_code" => [
                    "rules" => "permit_empty|exact_length[5]",
                    'errors' => [
                        'exact_length' => 'Kode pos wajib diisi tepat 5 digit'
                    ]
                ],
                // "address" => [
                //     "rules" => "required"
                // ],
                // "province_id" => [
                //     "rules" => "required"
                // ],
                // "city_id" => [
                //     "rules" => "required"
                // ],
                // "zip_code" => [
                //     "rules" => "required"
                // ],

                // "email" => [
                //     "rules" => "required"
                // ],
                // "pic_id" => [
                //     "rules" => "required"
                // ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "company_id"    => $this->this_company_id,
                    "code_warehouse" => $this->request->getPost("code_warehouse"),
                    "warehouse_name" => $this->request->getPost("warehouse_name"),
                    "address" => $this->request->getPost("address"),
                    "province_id" => $this->request->getPost("province_id"),
                    "city_id" => $this->request->getPost("city_id"),
                    "zip_code" => $this->request->getPost("zip_code"),
                    "phone" => $this->request->getPost("phone"),
                    "email" => $this->request->getPost("email"),
                    "pic_id" => $this->request->getPost("pic_id"),
                    "divisi_id" => $this->request->getPost("divisi_id"),
                    "kawasan_id" => $this->request->getPost("kawasan_id")
                ];

                if ($this->WarehousesModel->insert($values)) {
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
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
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

    public function updateWarehouse()
    {
        try {
            $rules = [
                "code_warehouse" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Warehouse wajib diisi',
                    ]
                ],
                "warehouse_name" => [
                    "rules" => "required"
                ],
                "phone" => [
                    "rules" => "permit_empty|min_length[10]|max_length[16]",
                    'errors' => [
                        'min_length' => 'Nomor HP harus memiliki panjang minimal 10 digit',
                        'max_length' => 'Nomor HP tidak boleh lebih dari 16 digit'
                    ]
                ],
                "zip_code" => [
                    "rules" => "permit_empty|exact_length[5]",
                    'errors' => [
                        'exact_length' => 'Kode pos wajib diisi tepat 5 digit'
                    ]
                ],
                // "address" => [
                //     "rules" => "required"
                // ],
                // "province_id" => [
                //     "rules" => "required"
                // ],
                // "city_id" => [
                //     "rules" => "required"
                // ],
                // "phone" => [
                //     "rules" => "required"
                // ],
                // "email" => [
                //     "rules" => "required"
                // ],
                // "pic_id" => [
                //     "rules" => "required"
                // ]
            ];

            if ($this->validate($rules)) {
                $id = decrypt($this->request->getPost("id"));

                $values = [
                    "code_warehouse" => $this->request->getPost("code_warehouse"),
                    "warehouse_name" => $this->request->getPost("warehouse_name"),
                    "address" => $this->request->getPost("address"),
                    "province_id" => $this->request->getPost("province_id"),
                    "city_id" => $this->request->getPost("city_id"),
                    "zip_code" => $this->request->getPost("zip_code"),
                    "phone" => $this->request->getPost("phone"),
                    "email" => $this->request->getPost("email"),
                    "pic_id" => $this->request->getPost("pic_id"),
                    "divisi_id" => $this->request->getPost("divisi_id"),
                    "kawasan_id" => $this->request->getPost("kawasan_id")
                ];

                $warehouseSameName = $this->WarehousesModel
                    ->where('code_warehouse', $values['code_warehouse'])
                    ->where('id !=', $id)
                    ->first();

                if ($warehouseSameName) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Kode warehouse sudah digunakan.",
                        'token' => csrf_hash()
                    ]);
                }

                if ($this->WarehousesModel->update($id, $values)) {
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
            } else {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
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

    public function getByIdWarehouse($id = null)
    {
        if (!empty($id)) {
            $id = decrypt($id);
            $res = $this->WarehousesModel->get_by_id($id);
            if (count($res)) {
                $res[0]['id'] = encrypt($res[0]['id']);
                $data = [
                    "status"  => true,
                    "data"  => (object) $res[0],
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

    public function deleteWarehouse()
    {
        try {
            $id = decrypt($this->request->getPost("id"));

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->WarehousesModel->update($id, $values)) {
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

    public function dropdownWarehouse()
    {
        $dataWarehouse = $this->WarehousesModel->get_by_company_id($this->this_company_id);

        $data = [
            "data" => $dataWarehouse
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownWarehouseByDivisiId($divisiID)
    {
        $dataWarehouse = $this->WarehousesModel->get_by_divisi_id($this->this_company_id, $divisiID);

        $data = [
            "data" => $dataWarehouse
        ];

        echo json_encode($data);
        return;
    }
}
