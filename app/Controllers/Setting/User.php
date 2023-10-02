<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;
use App\Models\CompaniesModel;
use App\Models\AccessListsModel;
use App\Models\RolesModel;
use App\Models\UserModel;
use App\Models\MenuUrlsModel;

use DateTime;

class User extends BaseController
{
    protected $company_role;
    protected $token;
    protected $this_company_id;
    protected $session;
    protected $CompaniesModel;
    protected $AccessListsModel;
    protected $RolesModel;
    protected $UserModel;
    protected $MenuUrlsModel;

    public function __construct()
    {
        //$this->company_role = session()->get("login")->company_role;
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->session = session()->get("login");
        $this->CompaniesModel = new CompaniesModel();
        $this->AccessListsModel = new AccessListsModel();
        $this->RolesModel = new RolesModel();
        $this->UserModel = new UserModel();
        $this->MenuUrlsModel = new MenuUrlsModel();
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

            // $res_access_list = $this->AccessListsModel->get_by_role_id_and_company_id_join_menu_url_parent($role_id, $id);
            $res_child_access = $this->AccessListsModel->get_by_role_id_and_company_id_join_menu_url_not_parent($role_id, $id);

            $res_access_list = $this->MenuUrlsModel->get_menu_url(null);

            $arr = [];
            for ($i = 0; $i < count($res_access_list); $i++) {
                $arr_child = [];
                for ($j = 0; $j < count($res_child_access); $j++) {
                    if ($res_child_access[$j]["parent_id"] == $res_access_list[$i]["id"]) {
                        $access = json_decode($res_child_access[$j]["action"]);
                        $values = [
                            "name"  => $res_child_access[$j]["menuName"],
                            "menu_url_id"   => $res_child_access[$j]["menu_url_id"],
                            "url"   => $res_child_access[$j]["url"],
                            "access"    => $access
                        ];
                        if(sizeof($access) !== 0)
                        {
                            array_push($arr_child, (object) $values);
                        }
                    }
                }
                $values = [
                    "menu_url_id"   => $res_access_list[$i]["id"],
                    "icon"          => $res_access_list[$i]["icon"],
                    "menuName"      => $res_access_list[$i]["name"],
                    "url"           => $res_access_list[$i]["url"],
                    "isParent"      => $res_access_list[$i]["parent_id"],
                    "child"         => $arr_child
                ];
                if(sizeof($arr_child) !== 0)
                {
                    array_push($arr, (object) $values);
                }
            }

            // $data = [
            //     "status"            => true,
            //     "message" => $arr
            // ];
            // return json_encode($data);

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

        $condition = [
            "company_id" => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $userData = $this->UserModel->getList($condition, $addCondition, $limit, $offset);

        $dataUser = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($userData['data'] as $data) {
            array_push($dataUser, [
                "no" => $no++,
                "id" => $data->id,
                "username" => $data->username,
                "employeeName" => $data->employeeName,
                "status" => $data->status,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $userData['totalData'],
            "recordsFiltered"   => $userData['totalFilteredData'],
            "data"              => $dataUser,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveUser()
    {
        try {
            $rules = [
                "username" => [
                    "rules" => "required|is_unique[users.username]",
                    'errors' => [
                        'required' => 'Username tidak boleh kosong',
                        'is_unique' => 'Username sudah ada!'
                    ]
                ],
                "password" => [
                    "rules" => "required"
                ],
                "employee_id" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $payload = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("name"),
                    "username" => $this->request->getPost("username"),
                    "user_pass" => password_hash($this->request->getPost("password"), PASSWORD_BCRYPT),
                    "employee_id" => formatter($this->request->getPost("employee_id"), "STR_TO_INT"),
                    "company_role" => $this->request->getPost("company_role"),
                    "status" => $this->request->getPost("status"),
                    "current_company_id" => $this->request->getPost("current_company_id")
                ];

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);
                $response =  $this->UserModel->insert($payload);

                if ($response) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message =  'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
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
                "username" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Username tidak boleh kosong'
                    ]
                ]
            ];

             if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $password = $this->request->getPost("password");

                // check username exist except id
                $check_current_username = $this->UserModel->check_current_username($id, $this->request->getPost("username"));

                if($check_current_username == 0)
                {
                    if($password)
                    {
                        $payload = [
                            "company_id" => $this->this_company_id,
                            "username" => $this->request->getPost("username"),
                            "user_pass" => password_hash($this->request->getPost("password"), PASSWORD_BCRYPT),
                            "company_role" => $this->request->getPost("company_role"),
                            "status" => $this->request->getPost("status"),
                            "current_company_id" => $this->request->getPost("current_company_id")
                        ];
                    }
                    else
                    {
                        $payload = [
                            "company_id" => $this->this_company_id,
                            "username" => $this->request->getPost("username"),
                            "company_role" => $this->request->getPost("company_role"),
                            "status" => $this->request->getPost("status"),
                            "current_company_id" => $this->request->getPost("current_company_id")
                        ];
                    }

                    $condition = [
                        'id' => $id
                    ];

                    $response = $this->UserModel->where($condition)->set($payload)->update();

                    if ($response) {
                        $data = [
                            "status"            => true,
                            "message"   => "Data Berhasil diubah",
                            "payload"   => $payload,
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    } else {
                        $message = 'Data Gagal Diubah';
                        $data = [
                            "status"            => false,
                            "message"    => $message,
                            "payload"   => $payload,
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    }
                }
                else
                {
                    $data = [
                        "status"            => false,
                        "message"    => "Username sudah ada!",
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
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
            $response =  $this->UserModel->getUser($id);

            if ($response) {
                $response = $response;
                $company_role = $response ? json_decode($response->company_role) : [];
                $new_company_role = [];

                foreach($company_role as $item)
                {
                    $company_name = $this->CompaniesModel->find($item->company_id);
                    $role_name = $this->RolesModel->find($item->role_id);

                    array_push($new_company_role, [
                        "company_id" => $item->company_id,
                        "role_id" => $item->role_id,
                        "company_name" => $company_name ? $company_name["company"] : "",
                        "role_name" => $role_name ? $role_name["name"] : ""
                    ]);
                }
                $data = [
                    "status"  => true,
                    "data"  => $response,
                    "company_role" => $new_company_role
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

    public function deleteUser()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $find = $this->UserModel->find($id);
                if ($find) {
                    $response =  $this->UserModel->delete($id);
                    if ($response) {
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
                        "message"    => "Data Tidak Ditemukan",
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
        $dataUser = [];
        $dataUser = $this->UserModel->getUserDropdown();

        $data = [
            "data" => $dataUser
        ];

        echo json_encode($data);
        return;
    }
}
