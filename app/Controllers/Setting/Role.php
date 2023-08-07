<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;
use App\Models\RolesModel;

class Role extends BaseController
{
    protected $token;
    protected $RolesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
    }

    public function role()
    {
        return view('Setting/role/index');
    }

    public function allRole()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $RolesModel = new RolesModel();

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $roleData = $RolesModel->getRoleList($condition, $addCondition, $limit, $offset);

        $dataRole = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($roleData['data'] as $data) {
            array_push($dataRole, [
                "no"            => $no++,
                "id"            => $data->id,
                "name"          => $data->name,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $roleData['totalData'],
            "recordsFiltered"   => $roleData['totalFilteredData'],
            "data"              => $dataRole,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveRole()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required"
                ]
            ];

            $RolesModel = new RolesModel();

            if ($this->validate($rules)) {
                $insertData = [
                    "name" => $this->request->getPost("name")
                ];

                $payload = json_encode($insertData);

                $insert = $RolesModel->insert($insertData);

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

    public function updateRole()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required"
                ]
            ];

            $RolesModel = new RolesModel();

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = [
                    "name" => $this->request->getPost("name")
                ];

                $RolesModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Diubah",
                    'token'     => csrf_hash()
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

    public function getByIdRole($id = null)
    {
        $RolesModel = new RolesModel();

        if (!empty($id)) {
            $dataRole = $RolesModel->find($id);
            $data = [
                "status"  => true,
                "data"    => $dataRole,
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteRole()
    {
        try {
            $id = $this->request->getPost("id");
            $RolesModel = new RolesModel();

            if (empty($id)) {
                $data = [
                    "status"     => false,
                    "message"    => "Data Gagal Dihapus",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $RolesModel->delete($id);

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

    public function dropdownRole()
    {
        $RolesModel = new RolesModel();

        $dataRole = $RolesModel->getRoleDropdown();

        $data = [
            "data" => $dataRole
        ];

        echo json_encode($data);
        return;
    }
}
