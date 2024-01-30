<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;
use App\Models\RolesModel;
use App\Models\CompaniesModel;
use App\Models\AccessListsModel;
use App\Models\MenuUrlsModel;
use App\Models\UserModel;

class Akses extends BaseController
{
    protected $token;
    protected $RolesModel;
    protected $CompaniesModel;
    protected $AccessListsModel;
    protected $MenuUrlsModel;
    protected $UserModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->RolesModel = new RolesModel();
        $this->CompaniesModel = new CompaniesModel();
        $this->AccessListsModel = new AccessListsModel();
        $this->MenuUrlsModel = new MenuUrlsModel();
        $this->UserModel = new UserModel();
    }

    public function akses()
    {
        //Get Role
        $dataRole = [];
        $dataRole = $this->RolesModel->getRoleDropdown();

        //Get Company
        $dataCompany = [];
        $dataCompany = $this->CompaniesModel->getCompanies();

        $data = [
            "dataRole" => $dataRole,
            "dataCompany" => $dataCompany
        ];

        return view('Setting/akses/index', $data);
    }

    public function getAkses()
    {
        //Get Access List
        $res_access_list = $this->MenuUrlsModel->get_menu_url(null);
        $arr = [];

        if ($res_access_list) {
            foreach ($res_access_list as $parent) {
                $arr_child = [];
                $res_access_child = $this->MenuUrlsModel->get_menu_url($parent["id"]);

                if ($res_access_child) {
                    foreach ($res_access_child as $child) {
                        $payload = [
                            "company_id" => $this->request->getGet("company_id"),
                            "role_id" => $this->request->getGet("role_id"),
                            "menu_url_id" => $child["id"]
                        ];

                        $access = $this->AccessListsModel->get_access($payload);
                        array_push($arr_child, [
                            "menu_url_id"   => $child["id"],
                            "name"      => $child["name"],
                            "access"      => $access ? json_decode($access->action) : []
                        ]);
                    }
                }

                array_push($arr, [
                    "menu_url_id"   => $parent["id"],
                    "menuName"      => $parent["name"],
                    "isParent"      => $parent["parent_id"],
                    "child"         => $arr_child
                ]);
            }
        }

        $data = [
            "status"  => true,
            "data"  => $arr,
        ];
        echo json_encode($data);
        return;
    }

    public function saveAkses()
    {
        try {
            $role_id = formatter($this->request->getPost("role_id"), "STR_TO_INT");
            $company_id = formatter($this->request->getPost("company_id"), "STR_TO_INT");

            $result = array();

            //Get Access List
            $res_access_list = $this->MenuUrlsModel->get_menu_url(null);

            if ($res_access_list) {
                foreach ($res_access_list as $parent) {
                    $res_access_child = $this->MenuUrlsModel->get_menu_url($parent["id"]);

                    if ($res_access_child) {
                        foreach ($res_access_child as $child) {
                            $access = array();
                            if ($this->request->getPost("create_" . $child["id"]) !== null) {
                                array_push($access, 'c');
                            }
                            if ($this->request->getPost("read_" . $child["id"]) !== null) {
                                array_push($access, 'r');
                            }
                            if ($this->request->getPost("update_" . $child["id"]) !== null) {
                                array_push($access, 'u');
                            }
                            if ($this->request->getPost("delete_" . $child["id"]) !== null) {
                                array_push($access, 'd');
                            }
                            if ($this->request->getPost("print_" . $child["id"]) !== null) {
                                array_push($access, 'p');
                            }
                            if ($this->request->getPost("approve_" . $child["id"]) !== null) {
                                array_push($access, 'a');
                            }
                            if ($this->request->getPost("unposting_" . $child["id"]) !== null) {
                                array_push($access, 'u');
                            }
                            array_push(
                                $result,
                                [
                                    "parent_id" => $this->request->getPost("parent_" . $child["id"]),
                                    "menu_url_id" => $child["id"],
                                    "action" => $access
                                ]
                            );
                        }
                    }
                }

                $payloadFinal = [];

                // $data = [
                //     "status"            => false,
                //     "message"    => json_encode($payload),
                //     "payload"   => json_encode($payload),
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);

                foreach ($result as $item) {
                    $payloadLoop = [
                        "company_id" => $this->request->getPost("company_id"),
                        "role_id" => $this->request->getPost("role_id"),
                        "menu_url_id" => $item["menu_url_id"],
                        "action" => json_encode($item["action"])
                    ];

                    // search menu url exist
                    $exist_access = $this->AccessListsModel->get_access($payloadLoop);

                    // update
                    if ($exist_access) {
                        array_push($payloadFinal, $payloadLoop);

                        $updateAccess = $this->AccessListsModel->where(['id' => $exist_access->id])->set($payloadLoop)->update();

                        if (!$updateAccess) {
                            $data = [
                                "status"            => false,
                                "message"    => $exist_access->menu_url_name . " Gagal diubah",
                                "payload"   => $payloadLoop,
                                'token' => csrf_hash()
                            ];
                            echo json_encode($data);
                        }
                    }
                    // create
                    else {
                        if (sizeof($item["action"]) !== 0) {
                            array_push($payloadFinal, $payloadLoop);

                            $createAccess = $this->AccessListsModel->insert($payloadLoop);

                            if (!$createAccess) {
                                $data = [
                                    "status"            => false,
                                    "message"    => $exist_access->menu_url_name . " Gagal disimpan",
                                    "payload"   => $payloadLoop,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                            }
                        }
                    }
                }

                // set new session if this account login have same access edited
                $change_session = false;
                foreach (session()->get("login")->arr_company as $allCompany) {

                    if (formatter($this->request->getPost("company_id"), "STR_TO_INT") === formatter($allCompany["id"], "STR_TO_INT")) {
                        if (formatter($this->request->getPost("role_id"), "STR_TO_INT") === formatter($allCompany["role_id"], "STR_TO_INT")) {
                            $change_session = true;
                        }
                    }
                }

                if ($change_session) {
                    $res_user = $this->UserModel->get_by_username(session()->get("login")->username);
                    if (count($res_user) > 0) {
                        $arr_companies = json_decode($res_user[0]["company_role"], true);
                        $in_company_id = implode(', ', array_column($arr_companies, 'company_id'));
                        $in_roles_id = implode(', ', array_column($arr_companies, 'role_id'));

                        $res_company = $this->CompaniesModel->get_by_in_id($in_company_id);
                        $res_roles = $this->RolesModel->get_by_in_id($in_roles_id);

                        for ($i = 0; $i < count($res_company); $i++) {
                            $check = 1;
                            for ($j = 0; $j < count($res_roles); $j++) {
                                for ($k = 0; $k < count($arr_companies); $k++) {
                                    if ($res_company[$i]["id"] == $arr_companies[$k]["company_id"] && $res_roles[$j]["id"] == $arr_companies[$k]["role_id"]) {
                                        $res_company[$i]["role_id"] = $res_roles[$j]["id"];
                                        $res_company[$i]["role_name"] = $res_roles[$j]["name"];
                                        $check = 0;
                                        break;
                                    }
                                }
                                if ($check == 0) break;
                            }
                        }

                        $res_access_list = $this->MenuUrlsModel->get_menu_url(null);
                        //$res_access_list = $this->AccessListsModel->get_by_role_id_and_company_id_join_menu_url_parent($res_roles[0]["id"], $res_company[0]["id"]);
                        $res_child_access = $this->AccessListsModel->get_by_role_id_and_company_id_join_menu_url_not_parent($res_roles[0]["id"], $res_company[0]["id"]);
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

                                    if (sizeof($access) !== 0) {
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

                            if (sizeof($arr_child) !== 0) {
                                array_push($arr, (object) $values);
                            }
                        }

                        $this_company_id = $res_company[0]["id"];
                        $this_company = $res_company[0]["company"];
                        $this_access = $arr;
                        $this_role_id = $res_roles[0]["id"];
                        $this_role_name = $res_roles[0]["name"];

                        // token add bearer
                        $session = (object) [
                            "isLogin" => true,
                            "token" => $this->token,
                            "name" => $res_user[0]["name"],
                            "username" => $res_user[0]["username"],
                            "this_role_id" => $this_role_id,
                            "this_role_name" => $this_role_name,
                            //"company_role" => $data->company_role,
                            //"company_role" => $data->company_role,
                            "arr_company"   => $res_company,
                            "this_company_id" => $this_company_id,
                            "this_company" => $this_company,
                            "this_access" => $this_access,
                            "user_id" => $res_user[0]["id"],
                            "employee_id" => $res_user[0]["employee_id"],
                            "status" => $res_user[0]["status"],
                        ];

                        session()->setTempdata("login", $session, 36000);
                    }

                    $data = [
                        'refresh'   => true,
                        "status"            => true,
                        "payload"   => $payloadFinal,
                        "message"    => "Data berhasil disimpan",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        'refresh'   => false,
                        "status"            => true,
                        "payload"   => $payloadFinal,
                        "message"    => "Data berhasil disimpan",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $message = 'Menu berdasarkan role tidak ditemukan';

                $data = [
                    'refresh'   => false,
                    "status"            => false,
                    "message"    => $message,
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
