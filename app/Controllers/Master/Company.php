<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\CompaniesModel;
use App\Models\ProvincesModel;

class Company extends BaseController
{
    protected $token;
    protected $CompaniesModel;
    protected $ProvincesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->CompaniesModel = new CompaniesModel();
        $this->ProvincesModel = new ProvincesModel();
    }

    public function company()
    {
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $data = [
            "dataProvinces" => $dataProvinces,
        ];

        return view('Master/company/index', $data);
    }

    public function allCompany()
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
            "search"        => $search
        ];

        $totalRecords = $this->CompaniesModel->total_list(array());
        $totalRecordwithFilter = $this->CompaniesModel->total_list($values);

        $res = $this->CompaniesModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "company" => $res[$i]["company"],
                "holding_company" => $res[$i]["holding_company"],
                "address" => $res[$i]["address"],
                "phone" => $res[$i]["phone"],
                "email" => $res[$i]["email"],
                "province_name" => $res[$i]["province_name"],
                "city_name" => $res[$i]["city_name"],
                "zip_code" => $res[$i]["zip_code"],
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

    public function saveCompany()
    {
        try {
            $rules = [
                "holding_company" => [
                    "rules" => "required"
                ],
                "company" => [
                    "rules" => "required|is_unique[companies.company]",
                    'errors' => [
                        'required' => 'Nama Company harus diisi',
                        'is_unique' => 'Nama Company sudah ada'
                    ]
                ],
                "address" => [
                    "rules" => "required"
                ],
                // "phone" => [
                //     "rules" => "required|min_length[10]|max_length[16]",
                //     'errors' => [
                //         'min_length' => 'Nomor HP harus memiliki panjang minimal 10 digit',
                //         'max_length' => 'Nomor HP tidak boleh lebih dari 16 digit',
                //         'required' => 'Nomor telpon harus diisi',
                //     ]
                // ],
                // "zip_code" => [
                //     "rules" => "required|exact_length[5]",
                //     'errors' => [
                //         'exact_length' => 'Kode pos wajib diisi tepat 5 digit',
                //         'required' => 'Kode pos harus diisi',

                //     ]
                // ],
                "province_id" => [
                    "rules" => "required"
                ],
                "city_id" => [
                    "rules" => "required"
                ],
                // "email" => [
                //     "rules" => "permit_empty|valid_email",
                //     'errors' => [
                //         'valid_email' => 'Email harus valid'
                //     ]
                // ],
            ];

            if ($this->validate($rules)) {
                $payload = '';

                $file = $this->request->getFile("logo");

                $logo = "";

                $values = [
                    "company" => strtoupper($this->request->getVar("company")),
                    "holding_company" => $this->request->getPost("holding_company"),
                    "address" => $this->request->getPost("address"),
                    "phone" => $this->request->getPost("phone"),
                    "email" => $this->request->getPost("email"),
                    "zip_code" => $this->request->getPost("zip_code"),
                    "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                    "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT"),
                    "website" => $this->request->getPost("website"),
                    "fax" => $this->request->getPost("fax"),
                    "factory" => $this->request->getPost("factory"),
                    "office_kop" => $this->request->getPost("office_kop")

                ];

                if (!empty($file->getName())) {
                    $mime = $file->getMimeType();
                    if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                        $logo = "data:$mime;base64, " . base64_encode(file_get_contents($file));
                        $values["logo"] = $logo;
                    }
                }

                if (isset($values)) {
                    if ($this->CompaniesModel->insert($values)) {
                        $data = [
                            "status"            => true,
                            "message"   => "Data Berhasil disimpan",
                            "payload"   => $payload,
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    } else {
                        $message = 'Data Gagal Disimpan';
                        $data = [
                            "status"            => false,
                            "message"    => $message,
                            "payload"   => $payload,
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    }
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Format gambar harus bertipe png, jpg, jpeg",
                        "payload"   => '',
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

    public function updateCompany()
    {
        try {
            $rules = [
                "holding_company" => [
                    "rules" => "required"
                ],
                "company" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama Company harus diisi',
                    ]
                ],
                "address" => [
                    "rules" => "required"
                ],
                // "phone" => [
                //     "rules" => "required|min_length[10]|max_length[16]",
                //     'errors' => [
                //         'min_length' => 'Nomor HP harus memiliki panjang minimal 10 digit',
                //         'max_length' => 'Nomor HP tidak boleh lebih dari 16 digit',
                //         'required' => 'Nomor telpon harus diisi',
                //     ]
                // ],
                // "zip_code" => [
                //     "rules" => "required|exact_length[5]",
                //     'errors' => [
                //         'exact_length' => 'Kode pos wajib diisi tepat 5 digit',
                //         'required' => 'Kode pos harus diisi',

                //     ]
                // ],
                "province_id" => [
                    "rules" => "required"
                ],
                "city_id" => [
                    "rules" => "required"
                ],
                // "email" => [
                //     "rules" => "permit_empty|valid_email",
                //     'errors' => [
                //         'valid_email' => 'Email harus valid'
                //     ]
                // ],
            ];

            if ($this->validate($rules)) {
                $payload = '';

                $id = $this->request->getPost("id");

                $file = $this->request->getFile("logo");

                $values = [
                    "company" => strtoupper($this->request->getVar("company")),
                    "holding_company" => $this->request->getPost("holding_company"),
                    "address" => $this->request->getPost("address"),
                    "phone" => $this->request->getPost("phone"),
                    "email" => $this->request->getPost("email"),
                    "zip_code" => $this->request->getPost("zip_code"),
                    "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                    "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT"),
                    "website" => $this->request->getPost("website"),
                    "fax" => $this->request->getPost("fax"),
                    "factory" => $this->request->getPost("factory"),
                    "office_kop" => $this->request->getPost("office_kop")
                ];

                $companySameName = $this->CompaniesModel
                    ->where('company', $values['company'])
                    ->where('id !=', $id)
                    ->first();

                if ($companySameName) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Nama Company sudah digunakan.",
                        'token' => csrf_hash()
                    ]);
                }

                if (!empty($file->getName())) {
                    $mime = $file->getMimeType();
                    if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                        $logo = "data:$mime;base64, " . base64_encode(file_get_contents($file));
                        $values["logo"] = $logo;
                    }
                }

                if (isset($values)) {
                    if ($this->CompaniesModel->update($id, $values)) {
                        $data = [
                            "status"            => true,
                            "message"   => "Data Berhasil diubah",
                            "payload"   => $payload,
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
                    $data = [
                        "status"            => false,
                        "message"    => "Format gambar harus bertipe png, jpg, jpeg",
                        "payload"   => '',
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

    public function getByIdCompany($id = null)
    {
        if (!empty($id)) {
            $res = $this->CompaniesModel->get_by_id($id);
            if (count($res)) {
                $data = [
                    "status"  => true,
                    "data"  => (object) $res[0],
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditemukan';
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

    public function deleteCompany()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->CompaniesModel->update($id, $values)) {
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

    public function dropdownCompany()
    {
        //$responseCompany = curl_request("GET", "/companies/all", $this->token);
        $dataCompany = [];
        $dataCompany = $this->CompaniesModel->getCompanies();

        $data = [
            "data" => $dataCompany
        ];

        echo json_encode($data);
        return;
    }
}
