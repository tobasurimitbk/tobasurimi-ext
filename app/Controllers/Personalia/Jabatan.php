<?php

namespace App\Controllers\Personalia;

use App\Controllers\BaseController;
use App\Models\JabatanModel;

class Jabatan extends BaseController
{
    protected $token;
    protected $JabatanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
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

        $condition = [];

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
        $jabatan_name = $this->request->getPost('jabatan_name');

        $getJabatanNull = $JabatanModel->select('id')
            ->where('jabatan_name', $jabatan_name)
            ->where('deletedAt', null)
            ->findAll();

        //cek nama jabatan duplikatnya sama yang ada? jika ada is_unique, jika tidak ada lolosin
        if (!empty($getJabatanNull)) {
            $rule_is_unique = 'required|is_unique[jabatans.jabatan_name]';
        } else {
            $rule_is_unique = 'required';
        }

        try {
            $rules = [
                "jabatan_name" => [
                    "rules" => $rule_is_unique,
                    'errors' => [
                        'required' => 'Nama Jabatan harus diisi',
                        'is_unique' => 'Nama Jabatan sudah ada'
                    ]
                ]
            ];



            if ($this->validate($rules)) {
                $insertData = [
                    "jabatan_name" => $this->request->getPost("jabatan_name")
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

        $jabatan_name = $this->request->getPost('jabatan_name');

        $getJabatanNull = $JabatanModel->select('id')
            ->where('jabatan_name', $jabatan_name)
            ->where('deletedAt', null)
            ->where('id !=', $id)
            ->findAll();

        //cek jabatan duplikatnya sama yang ada? jika ada is_unique, jika tidak ada lolosin
        if (!empty($getJabatanNull)) {

            //cek jabatan yg diedit masih sama dengan yg di ID?
            $getJabatanNow = $JabatanModel->select('id')
                ->where('jabatan_name', $jabatan_name)
                ->where('deletedAt', null)
                ->where('id', $id)
                ->first();

            //jika sama
            if (!empty($getJabatanNow)) {
                $rule_is_unique = 'required';
            } else {
                $rule_is_unique = 'required|is_unique[jabatans.jabatan_name]';
            }
        } else {
            $rule_is_unique = 'required';
        }

        try {
            $rules = [
                "jabatan_name" => [
                    "rules" => $rule_is_unique,
                    'errors' => [
                        'required' => 'Nama Jabatan harus diisi',
                        'is_unique' => 'Nama Jabatan sudah ada'
                    ]
                ]
            ];



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
