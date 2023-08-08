<?php

namespace App\Controllers\Setting;

use App\Controllers\BaseController;
use App\Models\RolesModel;
use App\Models\CompaniesModel;
use App\Models\AccessListsModel;
use App\Models\MenuUrlsModel;

class Akses extends BaseController
{
    protected $token;
    protected $RolesModel;
    protected $CompaniesModel;
    protected $AccessListsModel;
    protected $MenuUrlsModel;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->RolesModel = new RolesModel();
        $this->CompaniesModel = new CompaniesModel();
        $this->AccessListsModel = new AccessListsModel();
        $this->MenuUrlsModel = new MenuUrlsModel();
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

        if($res_access_list)
        {
            foreach($res_access_list as $parent)
            {
                $arr_child = [];
                $res_access_child = $this->MenuUrlsModel->get_menu_url($parent["id"]);

                if($res_access_child)
                {
                    foreach($res_access_child as $child)
                    {
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
        try{
            $role_id = formatter($this->request->getPost("role_id"), "STR_TO_INT");
            $company_id = formatter($this->request->getPost("company_id"), "STR_TO_INT");

            $result = array();

            //Get Access List
            $res_access_list = $this->MenuUrlsModel->get_menu_url(null);

            if($res_access_list)
            {
                foreach($res_access_list as $parent)
                {
                    $res_access_child = $this->MenuUrlsModel->get_menu_url($parent["id"]);

                    if($res_access_child)
                    {
                        foreach($res_access_child as $child)
                        {
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

                foreach($result as $item)
                {
                    $payloadLoop = [
                        "company_id" => $this->request->getPost("company_id"),
                        "role_id" => $this->request->getPost("role_id"),
                        "menu_url_id" => $item["menu_url_id"],
                        "action" => json_encode($item["action"])
                    ];

                    // search menu url exist
                    $exist_access = $this->AccessListsModel->get_access($payloadLoop);

                    // update
                    if($exist_access)
                    {
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
                    else
                    {
                        if(sizeof($item["action"]) !== 0)
                        {
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

                $data = [
                    "status"            => true,
                    "payload"   => $payloadFinal,
                    "message"    => "Data berhasil disimpan",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
            else
            {
                $message = 'Menu berdasarkan role tidak ditemukan';

                $data = [
                    "status"            => false,
                    "message"    => $message,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        }
        catch(\Exception $e)
        {
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