<?php

namespace App\Controllers\Personalia;

use App\Controllers\BaseController;
use App\Models\JabatanModel;

class Jabatan extends BaseController
{
    protected $this_company_id;
    protected $token;
    protected $JabatanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function jabatan()
    {
        return view('Personalia/jabatan/index');
    }

    public function allJabatan()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $JabatanModel = new JabatanModel();

        $condition = [
            'company_id' => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $JabatanData = $JabatanModel->getJabatanList($condition, $addCondition, $limit, $offset);

        $dataJabatan = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($JabatanData['data'] as $data) {
            array_push($dataJabatan, [
                "no"            => $no++,
                "id"            => $data->id,
                "jabatan_name"  => $data->jabatan_name,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $JabatanData['totalData'],
            "recordsFiltered"   => $JabatanData['totalFilteredData'],
            "data"              => $dataJabatan,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveJabatan()

    {
        $JabatanModel = new JabatanModel();

        try {
            $rules = [
                'jabatan_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama Jabatan harus diisi',
                    ]
                ]
            ];

            // Validasi Apakah Jabatan Name sudah ada di company
            $jabatanFirst = $JabatanModel->where('company_id', $this->this_company_id)
                ->where('deletedAt', null)
                ->where('jabatan_name', $this->request->getPost('jabatan_name'))
                ->first();

            if ($jabatanFirst != null) {
                return response()->setJSON([
                    "status"            => false,
                    "message"    => "'Nama Jabatan sudah ada di perusahaan ini",
                    'token' => csrf_hash()
                ]);
            }

            if ($this->validate($rules)) {
                $insertData = [
                    "jabatan_name" => $this->request->getPost("jabatan_name"),
                    "company_id" => $this->this_company_id
                ];

                $payload = json_encode($insertData);

                $insert = $JabatanModel->insert($insertData);

                if ($insert) {
                    $data = [
                        "id" => $insert,
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Data Gagal Disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                    ];
                    echo json_encode($data);
                }
            } else {
                $errors = '';
                foreach ($this->validator->getErrors() as $key => $row) {
                    $errors .= $row . '. ';
                }
                $data = [
                    "status"            => false,
                    "message"    => $errors,
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

    public function updateJabatan()
    {
        $JabatanModel = new JabatanModel();
        $id = $this->request->getPost("id");


        try {
            $rules = [
                "jabatan_name" => [
                    "rules" => 'required',
                    'errors' => [
                        'required' => 'Nama Jabatan harus diisi',
                    ]
                ]
            ];

            // Validasi Apakah Jabatan Name sudah ada di company
            $jabatanFirst = $JabatanModel->where('company_id', $this->this_company_id)
                ->where('deletedAt', null)
                ->where('jabatan_name', $this->request->getPost('jabatan_name'))
                ->where('id !=', $id)
                ->first();

            if ($jabatanFirst != null) {
                return response()->setJSON([
                    "status"            => false,
                    "message"    => "'Nama Jabatan sudah ada di perusahaan ini",
                    'token' => csrf_hash()
                ]);
            }


            if ($this->validate($rules)) {


                $payload = [
                    "jabatan_name" => $this->request->getPost("jabatan_name")
                ];

                $JabatanModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $errors = '';
                foreach ($this->validator->getErrors() as $key => $row) {
                    $errors .= $row . '. ';
                }
                $data = [
                    "status"     => false,
                    "message"    => $errors,
                    'token'      => csrf_hash()
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

    public function getByIdJabatan($id = null)
    {
        $JabatanModel = new JabatanModel();

        if (!empty($id)) {
            $dataJabatan = $JabatanModel->find($id);
            $data = [
                "status"  => true,
                "data"    => $dataJabatan,
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteJabatan()
    {
        try {
            $id = $this->request->getPost("id");
            $JabatanModel = new JabatanModel();

            if (empty($id)) {
                $data = [
                    "status"     => false,
                    "message"    => "Data Gagal Dihapus",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $JabatanModel->delete($id);

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
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

    public function dropdownJabatan()
    {
        $JabatanModel = new JabatanModel();

        $dataJabatan = $JabatanModel->getJabatanDropdown();

        $data = $dataJabatan;

        echo json_encode($data);
        return;
    }
}
