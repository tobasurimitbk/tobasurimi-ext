<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\HsCodesModel;

use App\Models\HSCodeModel;

class HSCode extends BaseController
{
    protected $token;
    protected $HSCodeModel;
    protected $HsCodesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->HSCodeModel = new HSCodeModel();
        $this->HsCodesModel = new HsCodesModel();
    }

    public function hsCode()
    {
        return view('Master/hsCode/index');
    }

    public function allHSCode()
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

        $totalRecords = $this->HsCodesModel->total_list(array());
        $totalRecordwithFilter = $this->HsCodesModel->total_list($values);

        $res = $this->HsCodesModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "komoditi" => $res[$i]["komoditi"],
                "code" => $res[$i]["code"],
                "uraian_barang" => $res[$i]["uraian_barang"],
                "satuan_barang" => $res[$i]["satuan_barang"],
                "uraian_satuan" => $res[$i]["uraian_satuan"],
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
                "satuan_barang" => [
                    "rules" => "required"
                ],
                "uraian_satuan" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "komoditi" => $this->request->getPost("komoditi"),
                    "code" => $this->request->getPost("code"),
                    "uraian_barang" => $this->request->getPost("uraian_barang"),
                    "satuan_barang" => $this->request->getPost("satuan_barang"),
                    "uraian_satuan" => $this->request->getPost("uraian_satuan")
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
                "satuan_barang" => [
                    "rules" => "required"
                ],
                "uraian_satuan" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $values = [
                    "komoditi" => $this->request->getPost("komoditi"),
                    "code" => $this->request->getPost("code"),
                    "uraian_barang" => $this->request->getPost("uraian_barang"),
                    "satuan_barang" => $this->request->getPost("satuan_barang"),
                    "uraian_satuan" => $this->request->getPost("uraian_satuan")
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
        $dataKodeHS = $this->HSCodeModel->asObject()->find();

        $data = [
            "data" => $dataKodeHS
        ];

        echo json_encode($data);
        return;
    }
}
