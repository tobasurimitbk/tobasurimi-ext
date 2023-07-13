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
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "required"
                ],
                "phone" => [
                    "rules" => "required"
                ],
                "zip_code" => [
                    "rules" => "required"
                ],
                "province_id" => [
                    "rules" => "required"
                ],
                "city_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = '';

                $file = $this->request->getFile("logo");

                $logo = "";

                $values = [
                    "company" => $this->request->getPost("company"),
                    "holding_company" => $this->request->getPost("holding_company"),
                    "address" => $this->request->getPost("address"),
                    "phone" => $this->request->getPost("phone"),
                    "email" => $this->request->getPost("email"),
                    "zip_code" => $this->request->getPost("zip_code"),
                    "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                    "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT")
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

    public function updateCompany()
    {
        try {
            $rules = [
                "holding_company" => [
                    "rules" => "required"
                ],
                "company" => [
                    "rules" => "required"
                ],
                "address" => [
                    "rules" => "required"
                ],
                "phone" => [
                    "rules" => "required"
                ],
                "zip_code" => [
                    "rules" => "required"
                ],
                "province_id" => [
                    "rules" => "required"
                ],
                "city_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = '';

                $id = $this->request->getPost("id");

                $file = $this->request->getFile("logo");
                if (!empty($file->getName())) {
                    $mime = $file->getMimeType();
                    if (in_array($mime, ["image/png", "image/jpg", "image/jpeg"])) {
                        $logo = "data:$mime;base64, " . base64_encode(file_get_contents($file));

                        $payload = json_encode([
                            "logo" => $logo,
                            "company" => $this->request->getPost("company"),
                            "holding_company" => $this->request->getPost("holding_company"),
                            "address" => $this->request->getPost("address"),
                            "phone" => $this->request->getPost("phone"),
                            "email" => $this->request->getPost("email"),
                            "zip_code" => $this->request->getPost("zip_code"),
                            "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                            "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT")
                        ]);
                    }
                } else {
                    $payload = json_encode([
                        "company" => $this->request->getPost("company"),
                        "holding_company" => $this->request->getPost("holding_company"),
                        "address" => $this->request->getPost("address"),
                        "phone" => $this->request->getPost("phone"),
                        "email" => $this->request->getPost("email"),
                        "zip_code" => $this->request->getPost("zip_code"),
                        "province_id" => formatter($this->request->getPost("province_id"), "STR_TO_INT"),
                        "city_id" => formatter($this->request->getPost("city_id"), "STR_TO_INT")
                    ]);
                }

                if ($payload) {
                    $response = curl_request("PATCH", "/companies/$id", $this->token, $payload);

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
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Diubah",
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

    public function getByIdCompany($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/companies/$id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"  => true,
                    "data"  => json_decode($response["body"])->data,
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditemukan';
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
                $response = curl_request("DELETE", "/companies/$id", $this->token);
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

    public function dropdownCompany()
    {
        $responseCompany = curl_request("GET", "/companies/all", $this->token);

        $dataCompany = [];
        if ($responseCompany["code"] === 200) {
            $dataCompany = json_decode($responseCompany["body"])->data;
        }

        $data = [
            "data" => $dataCompany
        ];

        echo json_encode($data);
        return;
    }
}
