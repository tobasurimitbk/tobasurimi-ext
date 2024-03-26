<?php

namespace App\Controllers\Accounting\NoBukti;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\TransaksiJurnalModel;
use Exception;

class NoBukti extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $metadataModel;
    protected $transaksiJurnalModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = \Config\Services::encrypter();
        $this->metadataModel = new MetadataModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
    }

    public function index()
    {
        return view('Accounting/NoBukti/bukti');
    }

    public function allNoBukti()
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
            "search"        => $search,
            "name"        => "tipe_transaksi",
        ];

        $totalRecords = $this->metadataModel->total_list(array());
        $totalRecordwithFilter = $this->metadataModel->total_list($values);

        $res = $this->metadataModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "value" => $res[$i]["value"],
                "description" => $res[$i]["description"],
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

    public function getByIdNoBukti($id = null)
    {
        if (!empty($id)) {
            $res = $this->metadataModel->get_by_id($id);
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

    public function saveNoBukti()
    {
        try {
            $rules = [
                "nama_tipe_transaksi" => [
                    "rules" => "required"
                ],
                "no_bukti" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $nama = strtoupper($this->request->getPost("nama_tipe_transaksi"));
                $namaTanpaSpasi = str_replace(' ',  '', $nama);
                $no_bukti = strtoupper($this->request->getPost("no_bukti"));
                $check = $this->metadataModel->where('UPPER(REPLACE(value, " ", ""))', $namaTanpaSpasi)->find();
                // var_dump($check);
                // exit;
                if ($check) {
                    $message = 'Data Gagal Disimpan, Tipe Transaksi Sudah Ada';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }

                $values = [
                    "name"    => "tipe_transaksi",
                    "value" => $nama,
                    "description" => $no_bukti,
                ];

                //$response = curl_request("POST", "/kategoriAkun", $this->token, $payload);

                if ($this->metadataModel->insert($values)) {
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
                    return;
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Disimpan",
                    'token' => csrf_hash()
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
            return;
        }
        return;
    }

    public function updateNoBukti()
    {
        try {
            $rules = [
                "nama_tipe_transaksi" => [
                    "rules" => "required"
                ],
                "no_bukti" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id_nobukti");

                $nama = strtoupper($this->request->getPost("nama_tipe_transaksi"));
                // $namaTanpaSpasi = str_replace(' ',  '', $nama);
                $no_bukti = strtoupper($this->request->getPost("no_bukti"));
                // $check = $this->metadataModel->where('UPPER(REPLACE(value, " ", ""))', $namaTanpaSpasi)->find();
                // // var_dump($check);
                // // exit;
                // if ($check) {
                //     $message = 'Data Gagal Disimpan, Tipe Transaksi Sudah Ada';
                //     $data = [
                //         "status"            => false,
                //         "message"    => $message,
                //         "payload"   => "",
                //         'token' => csrf_hash()
                //     ];
                //     echo json_encode($data);
                //     return;
                // }
                $values = [
                    "name"    => "tipe_transaksi",
                    "value" => $nama,
                    "description" => $no_bukti,
                ];

                if ($this->metadataModel->update($id, $values)) {
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
                    return;
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Diubah",
                    'token' => csrf_hash()
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
            return;
        }
        return;
    }

    public function deleteNoBukti()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $check = $this->transaksiJurnalModel->where('type_transaksi', $id)->where('deleted_at', null)->find();
                // var_dump($check);
                // exit;
                if ($check) {
                    $message = 'Data Gagal Dihapus, Tipe Transaksi Sudah Digunakan Jurnal';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->metadataModel->update($id, $values)) {
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
