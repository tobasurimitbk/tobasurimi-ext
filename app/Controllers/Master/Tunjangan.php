<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\TunjanganModel;

class Tunjangan extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $TunjanganModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->TunjanganModel = new TunjanganModel();
    }

    public function ListTunjangan()
    {
        return view('Master/Tunjangan/index');
    }

    public function allTunjangan()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "company_id"    => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $res = $this->TunjanganModel->getTunjanganList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "name"                  => $data->name,
                "tipe"                  => ($data->tipe == "PLUS") ? "Penambahan Gaji" : "Pengurangan Gaji"
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveTunjangan()
    {
        try {
            $rules = [
                "nama" => [
                    "rules" => "required"
                ],
                "tipe" => [
                    "rules" => "required"
                ]

            ];

            if ($this->validate($rules)) {

                $isGajiPokokPerHari = $this->request->getPost('isGajiPokokPerHari') ?? 0;
                $isCadangan = $this->request->getPost('isCadangan') ?? 0;

                if ($isGajiPokokPerHari) {
                    $this->TunjanganModel->set('is_gaji_harian', 0)->where('company_id', $this->this_company_id)->update();
                }

                if ($isCadangan) {
                    $this->TunjanganModel->set('is_cadangan', 0)->where('company_id', $this->this_company_id)->update();
                }

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("nama"),
                    "tipe" => $this->request->getPost("tipe"),
                    "is_gaji_harian" => $isGajiPokokPerHari,
                    "is_cadangan" => $isCadangan
                ];
                if ($this->TunjanganModel->insert($values)) {
                    $data = [
                        "status"    => true,
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

    public function updateTunjangan()
    {
        try {
            $rules = [
                "nama" => [
                    "rules" => "required"
                ],
                "tipe" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {

                $id = $this->request->getPost("id");
                $isGajiPokokPerHari = $this->request->getPost('isGajiPokokPerHari') ?? 0;
                $isCadangan = $this->request->getPost('isCadangan') ?? 0;

                if ($isGajiPokokPerHari) {
                    $this->TunjanganModel->set('is_gaji_harian', 0)->where('company_id', $this->this_company_id)->update();
                }

                if ($isCadangan) {
                    $this->TunjanganModel->set('is_cadangan', 0)->where('company_id', $this->this_company_id)->update();
                }

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("nama"),
                    "tipe" => $this->request->getPost("tipe"),
                    "is_gaji_harian" => $isGajiPokokPerHari,
                    "is_cadangan" => $isCadangan
                ];

                if ($this->TunjanganModel->update($id, $values)) {
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

    public function getById($id = null)
    {
        if (!empty($id)) {
            $res = $this->TunjanganModel->getById($id);
            if ($res) {
                $data = [
                    "status"  => true,
                    "data"  => $res,
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

    public function deleteTunjangan()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->TunjanganModel->update($id, $values)) {
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

    public function dropdownTunjangan()
    {
        $TunjanganModel = new TunjanganModel();

        $dataJabatan = $TunjanganModel->getTunjanganDropdown();

        $data = $dataJabatan;

        echo json_encode($data);
        return;
    }
}
