<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\HsCodesModel;

class HSCode extends BaseController
{
    protected $token;
    protected $HsCodesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->HsCodesModel = new HsCodesModel();
    }

    public function hsCode()
    {
        return view('Master/hsCode/index');
    }

    public function allHSCode()
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
        $hsData = $this->HsCodesModel->getList($condition, $addCondition, $limit, $offset);

        $dataHS = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($hsData['data'] as $data) {
            array_push($dataHS, [
                "no"                => $no++,
                "id"                => $data->id,
                "komoditi"          => $data->komoditi,
                "code"              => $data->code,
                "uraian_barang"     => $data->uraian_barang,
                "kode_satuan"       => $data->kode_satuan,
                "nama_satuan"       => $data->nama_satuan
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $hsData['totalData'],
            "recordsFiltered"   => $hsData['totalFilteredData'],
            "data"              => $dataHS,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    // public function allHSCode()
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
    //         "search"        => $search
    //     ];

    //     $totalRecords = $this->HsCodesModel->total_list(array());
    //     $totalRecordwithFilter = $this->HsCodesModel->total_list($values);

    //     $res = $this->HsCodesModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

    //     $number = $row * $rowperpage;

    //     $data = [];

    //     for ($i = 0; $i < count($res); $i++) {

    //         $data[] = array(
    //             "no" => ($row + $i + 1),
    //             "id" => $res[$i]["id"],
    //             "komoditi" => $res[$i]["komoditi"],
    //             "code" => $res[$i]["code"],
    //             "uraian_barang" => $res[$i]["uraian_barang"],
    //             "kode_satuan" => $res[$i]["kode_satuan"],
    //             "nama_satuan" => $res[$i]["nama_satuan"]
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

    public function saveHSCode()
    {
        try {
            $rules = [
                "komoditi" => [
                    "rules" => "required"
                ],
                "code" => [
                    "rules" => "required"
                ],
                "uraian_barang" => [
                    "rules" => "required"
                ],
                "unit" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "komoditi" => $this->request->getPost("komoditi"),
                    "code" => $this->request->getPost("code"),
                    "uraian_barang" => $this->request->getPost("uraian_barang"),
                    "unit" => $this->request->getPost("unit")
                ];

                if ($this->HsCodesModel->insert($values)) {
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

    public function updateHSCode()
    {
        try {
            $rules = [
                "komoditi" => [
                    "rules" => "required"
                ],
                "code" => [
                    "rules" => "required"
                ],
                "uraian_barang" => [
                    "rules" => "required"
                ],
                "unit" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $values = [
                    "komoditi" => $this->request->getPost("komoditi"),
                    "code" => $this->request->getPost("code"),
                    "uraian_barang" => $this->request->getPost("uraian_barang"),
                    "unit" => $this->request->getPost("unit")
                ];

                if ($this->HsCodesModel->update($id, $values)) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
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

    public function getByIdHSCode($id = null)
    {
        if (!empty($id)) {
            $res = $this->HsCodesModel->get_by_id($id);

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

    public function deleteHSCode()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->HsCodesModel->update($id, $values)) {
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

    public function dropdownHSCode()
    {
        $dataKodeHS = $this->HsCodesModel->asObject()->find();

        $data = [
            "data" => $dataKodeHS
        ];

        echo json_encode($data);
        return;
    }
}
