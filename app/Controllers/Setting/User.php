<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;
use App\Models\CompaniesModel;
use App\Models\AccessListsModel;

use DateTime;

class User extends BaseController
{
    protected $company_role;
    protected $token;
    protected $this_company_id;
    protected $session;
    protected $CompaniesModel;
    protected $AccessListsModel;

    public function __construct()
    {
        //$this->company_role = session()->get("login")->company_role;
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->session = session()->get("login");
        $this->CompaniesModel = new CompaniesModel();
        $this->AccessListsModel = new AccessListsModel();
    }

    public function changeCompany()
    {
        $id = $this->request->getGet("id");
        //$res = $this->CompaniesModel->get_by_id($id);
        $ses = session()->get("login")->arr_company;
        $name = "";
        $role_id = "";
        $role_name = "";

        for ($i = 0; $i < count($ses); $i++) {
            if ($ses[$i]["id"] == $id) {
                $name = $ses[$i]["company"];
                $role_id = $ses[$i]["role_id"];
                $role_name = $ses[$i]["role_name"];
                break;
            }
        }

        if ($id) {
            $this_access = "";

            $res_access_list = $this->AccessListsModel->get_by_role_id_and_company_id_join_menu_url_parent($role_id, $id);
            $res_child_access = $this->AccessListsModel->get_by_role_id_and_company_id_join_menu_url_not_parent($role_id, $id);
            $arr = [];
            for ($i = 0; $i < count($res_access_list); $i++) {
                $arr_child = [];
                for ($j = 0; $j < count($res_child_access); $j++) {
                    if ($res_child_access[$j]["parent_id"] == $res_access_list[$i]["menu_url_id"]) {
                        $access = json_decode($res_child_access[$j]["action"]);
                        $values = [
                            "name"  => $res_child_access[$j]["menuName"],
                            "menu_url_id"   => $res_child_access[$j]["menu_url_id"],
                            "url"   => $res_child_access[$j]["url"],
                            "access"    => $access
                        ];
                        array_push($arr_child, (object) $values);
                    }
                }
                $values = [
                    "menu_url_id"   => $res_access_list[$i]["menu_url_id"],
                    "icon"          => $res_access_list[$i]["icon"],
                    "menuName"      => $res_access_list[$i]["menuName"],
                    "url"           => $res_access_list[$i]["url"],
                    "isParent"      => $res_access_list[$i]["parent_id"],
                    "child"         => $arr_child
                ];
                array_push($arr, (object) $values);
            }

            $this->session->this_company_id = $id;
            $this->session->this_company = $name;
            $this->session->this_role_id = $role_id;
            $this->session->this_role_name = $role_name;
            $this->session->this_access = $arr;

            session()->setTempdata("login", $this->session, 36000);

            $data = [
                "status"            => true
            ];
            echo json_encode($data);
        } else {
            return redirect()->to("/dashboard");
        }
        return;
    }

    public function user()
    {
        return view('Setting/user/index');
    }

    public function allUser()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "idCompany" => $this->this_company_id
        ];

        $response = curl_request("GET", "/users", $this->token, $payload);
        $dataUser = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataUser, [
                    "no" => $no++,
                    "id" => $data->id,
                    "username" => $data->username,
                    "name" => $data->name,
                    "employeeName" => $data->employeeName,
                    "status" => $data->status,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataUser,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveUser()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required"
                ],
                "username" => [
                    "rules" => "required"
                ],
                "password" => [
                    "rules" => "required"
                ],
                "employee_id" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $payload = json_encode([
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("name"),
                    "username" => $this->request->getPost("username"),
                    "password" => $this->request->getPost("password"),
                    "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT"),
                    "company_role" => json_decode($this->request->getPost("company_role")),
                    "status" => $this->request->getPost("status")
                ]);

                $response = curl_request("POST", "/users", $this->token, $payload);

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

    public function updateUser()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required"
                ],
                "username" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                if ($this->request->getPost("employee_id")) {
                    $payload = json_encode([
                        "company_id" => $this->this_company_id,
                        "name" => $this->request->getPost("name"),
                        "username" => $this->request->getPost("username"),
                        "password" => $this->request->getPost("password"),
                        "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT"),
                        "company_role" => json_decode($this->request->getPost("company_role")),
                        "status" => $this->request->getPost("status")
                    ]);
                } else {
                    $payload = json_encode([
                        "company_id" => $this->this_company_id,
                        "name" => $this->request->getPost("name"),
                        "username" => $this->request->getPost("username"),
                        "password" => $this->request->getPost("password"),
                        "company_role" => json_decode($this->request->getPost("company_role")),
                        "status" => $this->request->getPost("status")
                    ]);
                }


                $response = curl_request("PATCH", "/users/$id", $this->token, $payload);

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

    public function getByIdUser($id = null)
    {
        if (!empty($id)) {
            $response = curl_request("GET", "/users/$id?idCompany=$this->this_company_id", $this->token);
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

    public function deleteUser()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/users/$id", $this->token);
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

    public function dropdownUser()
    {
        $responseUser = curl_request("GET", "/users/selectOption", $this->token);

        $dataUser = [];
        if ($responseUser["code"] === 200) {
            $dataUser = json_decode($responseUser["body"])->data;
        }

        $data = [
            "data" => $dataUser
        ];

        echo json_encode($data);
        return;
    }
}
