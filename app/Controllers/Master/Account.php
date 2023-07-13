<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;

class Account extends BaseController
{
    protected $token;
    protected $Sub_AkunsModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->KategoriAkunsModel = new KategoriAkunsModel();
        $this->HeaderAkunsModel = new HeaderAkunsModel();
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function account()
    {
        return view('Master/account/index');
    }

    public function dropdownKategoriAccount()
    {
        $responseKelompokAkunKategori = curl_request("GET", "/kategoriAkun/all", $this->token);

        $dataKelompokAkunKategori = [];
        if ($responseKelompokAkunKategori["code"] === 200) {
            $dataKelompokAkunKategori = json_decode($responseKelompokAkunKategori["body"])->data;
        }

        $data = [
            "data" => $dataKelompokAkunKategori
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownHeaderAccount()
    {
        $responseKelompokAkunKategori = curl_request("GET", "/headerAkun/all", $this->token);

        $dataKelompokAkunKategori = [];
        if ($responseKelompokAkunKategori["code"] === 200) {
            $dataKelompokAkunKategori = json_decode($responseKelompokAkunKategori["body"])->data;
        }

        $data = [
            "data" => $dataKelompokAkunKategori
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownSubAccount()
    {
        $search = $this->request->getGet('search');
        $page = $this->request->getGet('page') ?? 1;
        $limit = 10;
        $offset = ($page - 1) * 10;

        $dataQry = $this->Sub_AkunsModel;

        if (!empty($search)) {
            $dataQry->like('nama_sub', $search);
        }

        $totalData = $dataQry->countAllResults(false);
        $subAccData = $dataQry->select('id, nama_sub AS text')
            ->where('company_id', $this->this_company_id)
            ->orderBy('nama_sub', 'asc')
            ->findAll($limit, $offset);


        $data = [
            "results"   => $subAccData,
            "pagination" => [
                "more"  => $offset < $totalData
            ]
        ];

        echo json_encode($data);
        return;
    }

    public function allKategoriAccount()
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

        $totalRecords = $this->KategoriAkunsModel->total_list(array());
        $totalRecordwithFilter = $this->KategoriAkunsModel->total_list($values);

        $res = $this->KategoriAkunsModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);
        //        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "kelompok_akun" => $res[$i]["kelompok_akun"],
                "no_kategori" => $res[$i]["no_kategori"],
                "nama_kategori" => $res[$i]["nama_kategori"],
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

    public function saveKategoriAccount()
    {
        try {
            $rules = [
                "kelompok_akun_id_kategori" => [
                    "rules" => "required"
                ],
                "kode_akun_kategori" => [
                    "rules" => "required"
                ],
                "nama_akun_kategori" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "company_id"    => $this->this_company_id,
                    "kelompok_id" => formatter($this->request->getPost("kelompok_akun_id_kategori"), "STR_TO_INT"),
                    "no_kategori" => $this->request->getPost("kode_akun_kategori"),
                    "nama_kategori" => $this->request->getPost("nama_akun_kategori"),
                ];

                //$response = curl_request("POST", "/kategoriAkun", $this->token, $payload);

                if ($this->KategoriAkunsModel->insert($values)) {
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

    public function updateKategoriAccount()
    {
        try {
            $rules = [
                "kelompok_akun_id_kategori" => [
                    "rules" => "required"
                ],
                "kode_akun_kategori" => [
                    "rules" => "required"
                ],
                "nama_akun_kategori" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id_kategori");

                $values = [
                    "kelompok_id" => formatter($this->request->getPost("kelompok_akun_id_kategori"), "STR_TO_INT"),
                    "no_kategori" => $this->request->getPost("kode_akun_kategori"),
                    "nama_kategori" => $this->request->getPost("nama_akun_kategori"),
                ];

                if ($this->KategoriAkunsModel->update($id, $values)) {
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

    public function getByIdKategoriAccount($id = null)
    {
        if (!empty($id)) {
            $res = $this->KategoriAkunsModel->get_by_id($id);
            $response = curl_request("GET", "/kategoriAkun/$id", $this->token);
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

    public function deleteKategoriAccount()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->KategoriAkunsModel->update($id, $values)) {
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

    public function allHeaderAccount()
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

        $totalRecords = $this->HeaderAkunsModel->total_list(array());
        $totalRecordwithFilter = $this->HeaderAkunsModel->total_list($values);

        $res = $this->HeaderAkunsModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);
        //        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "nama_kategori" => $res[$i]["nama_kategori"],
                "no_header" => $res[$i]["no_header"],
                "nama_header" => $res[$i]["nama_header"],
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

    public function saveHeaderAccount()
    {
        try {
            $rules = [
                "category_id_header" => [
                    "rules" => "required"
                ],
                "kode_akun_header" => [
                    "rules" => "required"
                ],
                "nama_akun_header" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "company_id"    => $this->this_company_id,
                    "kategori_id" => formatter($this->request->getPost("category_id_header"), "STR_TO_INT"),
                    "no_header" => $this->request->getPost("kode_akun_header"),
                    "nama_header" => $this->request->getPost("nama_akun_header"),
                ];

                if ($this->HeaderAkunsModel->insert($values)) {
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

    public function updateHeaderAccount()
    {
        try {
            $rules = [
                "category_id_header" => [
                    "rules" => "required"
                ],
                "kode_akun_header" => [
                    "rules" => "required"
                ],
                "nama_akun_header" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id_header");

                $values = [
                    "kategori_id" => formatter($this->request->getPost("category_id_header"), "STR_TO_INT"),
                    "no_header" => $this->request->getPost("kode_akun_header"),
                    "nama_header" => $this->request->getPost("nama_akun_header"),
                ];

                if ($this->HeaderAkunsModel->update($id, $values)) {
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

    public function getByIdHeaderAccount($id = null)
    {
        if (!empty($id)) {
            $res = $this->HeaderAkunsModel->get_by_id($id);
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

    public function deleteHeaderAccount()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->HeaderAkunsModel->update($id, $values)) {
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

    public function allSubAccount()
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

        $totalRecords = $this->Sub_AkunsModel->total_list(array());
        $totalRecordwithFilter = $this->Sub_AkunsModel->total_list($values);

        $res = $this->Sub_AkunsModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "kategori_id" => $res[$i]["kategori_id"],
                "header_id" => $res[$i]["header_id"],
                "coa_id" => $res[$i]["coa_id"],
                "nama_kategori" => $res[$i]["nama_kategori"],
                "no_header" => $res[$i]["no_header"],
                "no_sub" => $res[$i]["no_sub"],
                "nama_sub" => $res[$i]["nama_sub"],
                "nama_header" => $res[$i]["nama_header"],
                "akun_coa" => $res[$i]["akun_coa"],
                "status" => $res[$i]["status"],
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

    public function saveSubAccount()
    {
        try {
            $rules = [
                "header_id_sub" => [
                    "rules" => "required"
                ],
                "coa_id_sub" => [
                    "rules" => "required"
                ],
                "kode_akun_sub" => [
                    "rules" => "required"
                ],
                "nama_akun_sub" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = json_encode([
                    "header_id" => formatter($this->request->getPost("header_id_sub"), "STR_TO_INT"),
                    "kategori_id" => formatter($this->request->getPost("category_id_sub"), "STR_TO_INT"),
                    "coa_id" => formatter($this->request->getPost("coa_id_sub"), "STR_TO_INT"),
                    "no_sub" => $this->request->getPost("kode_akun_sub"),
                    "nama_sub" => $this->request->getPost("nama_akun_sub"),
                    "status" => !empty($this->request->getPost("status_sub")) ? "Aktif" : "Void"
                ]);

                $response = curl_request("POST", "/subAkun", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
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

    public function updateSubAccount()
    {
        try {
            $rules = [
                "header_id_sub" => [
                    "rules" => "required"
                ],
                "coa_id_sub" => [
                    "rules" => "required"
                ],
                "kode_akun_sub" => [
                    "rules" => "required"
                ],
                "nama_akun_sub" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id_sub");

                $payload = json_encode([
                    "header_id" => formatter($this->request->getPost("header_id_sub"), "STR_TO_INT"),
                    "kategori_id" => formatter($this->request->getPost("category_id_sub"), "STR_TO_INT"),
                    "coa_id" => formatter($this->request->getPost("coa_id_sub"), "STR_TO_INT"),
                    "no_sub" => $this->request->getPost("kode_akun_sub"),
                    "nama_sub" => $this->request->getPost("nama_akun_sub"),
                    "status" => !empty($this->request->getPost("status_sub")) ? "Aktif" : "Void"
                ]);

                $response = curl_request("PATCH", "/subAkun/$id", $this->token, $payload);

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

    public function updateStatusSubAccount()
    {
        try {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Void"
            ]);

            $response = curl_request("PATCH", "/subAkun/$id", $this->token, $payload);

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

    public function getByIdSubAccount($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/subAkun/$id", $this->token);
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

    public function deleteSubAccount()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/subAkun/$id", $this->token);
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
