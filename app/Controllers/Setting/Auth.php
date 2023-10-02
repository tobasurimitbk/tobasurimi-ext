<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CompaniesModel;
use App\Models\RolesModel;
use App\Models\AccessListsModel;
use App\Models\MenuUrlsModel;

use DateTime;

class Auth extends BaseController
{
    protected $userModel;
    protected $CompaniesModel;
    protected $RolesModel;
    protected $AccessListsModel;
    protected $MenuUrlsModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->CompaniesModel = new CompaniesModel();
        $this->RolesModel = new RolesModel();
        $this->AccessListsModel = new AccessListsModel();
        $this->MenuUrlsModel = new MenuUrlsModel();
    }

    public function login()
    {
        if (!empty(is_login())) {
            return redirect()->to("/dashboard");
        }

        return view('Setting/login/index');
    }

    public function doLogin()
    {
        try {
            $rules = [
                "username" => [
                    "rules" => "required"
                ],
                "password" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $token = "";

                $username = $this->request->getPost('username');
                $password = $this->request->getPost('password');

                $res_user = $this->userModel->get_by_username($username);
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

                    $pass = $res_user[0]['user_pass'];
                    $authenticatePassword = password_verify($password, $pass);
                    if ($authenticatePassword) {
                        //$data = json_encode([
                        //    "username" => $username,
                        //    "password" => $password
                        //]);
                        //$response = curl_request("POST", "/auth/login", $token, $data);
                        //$data = json_decode($response["body"]);
                        //(object)

                        $res_access_list = $this->MenuUrlsModel->get_menu_url(null);
                        // $res_access_list = $this->AccessListsModel->get_by_role_id_and_company_id_join_menu_url_parent($res_roles[0]["id"], $res_company[0]["id"]);
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

                        $this_company_id = $res_company[0]["id"];
                        $this_company = $res_company[0]["company"];
                        $this_access = $arr;
                        $this_role_id = $res_roles[0]["id"];
                        $this_role_name = $res_roles[0]["name"];

                        // token add bearer
                        $session = (object) [
                            "isLogin" => true,
                            "token" => $token,
                            "name" => $res_user[0]["employee_name"],
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

                        // $data = [
                        //     "status"            => false,
                        //     "message"    => json_encode($res_company),
                        //     'token' => csrf_hash()
                        // ];
                        // return json_encode($data);

                        session()->setTempdata("login", $session, 36000);

                        $data = [
                            "status"            => true,
                            "message"   => "Berhasil Login",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    } else {
                        $message = 'Login Gagal, Coba Lagi';
                        $data = [
                            "status"            => false,
                            "message"    => $message,
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    }
                } else {
                    $message = 'Login Gagal, Coba Lagi';
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
                    "message"    => "Gagal Login, Coba Lagi",
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

    public function doLogout()
    {
        session()->destroy();

        return redirect()->to("/");
    }
}
