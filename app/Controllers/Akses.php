<?php

namespace App\Controllers;

class Akses extends BaseController
{

    public function __construct()
    {

    }

    public function akses()
    {
        $token = session()->get("login")->token;

         //Get Role
         $responseRole = curl_request("GET", "/roles/selectOption", $token);

         $dataRole = [];
         if ($responseRole["code"] === 200) {
             $dataRole = json_decode($responseRole["body"])->data;
         }
         
        //Get Company
        $responseCompany = curl_request("GET", "/companies/all", $token);

        $dataCompany = [];
        if ($responseCompany["code"] === 200) {
            $dataCompany = json_decode($responseCompany["body"])->data;
        }

        $data = [
            "dataRole" => $dataRole,
            "dataCompany" => $dataCompany
        ];

        return view('akses/index', $data);
    }

    public function getAkses()
    {
        $token = session()->get("login")->token;

        $payload = [
            "idCompany" => $this->request->getGet("company_id"),
            "idRole" => $this->request->getGet("role_id")
        ];

        $response = curl_request("GET", "/accessLists", $token, $payload);
        if ($response["code"] === 200) {
            $data = [
                "status"  => true,
                "data"  => json_decode($response["body"])->data,
            ];
            echo json_encode($data);
        } else {
            $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Ditampilkan';
            $data = [
                "status" => false,
                "message"  => $message
            ];
            echo json_encode($data);
        }
        return;
    }

    public function saveAkses()
    {
        $token = session()->get("login")->token;

        //Get Menu By Role Id
        $dataAkses = array();
        $role_id = formatter($this->request->getPost("role_id"), "STR_TO_INT");
        $company_id = formatter($this->request->getPost("company_id"), "STR_TO_INT");

        $payload = [
            "idCompany" => $company_id,
            "idRole" => $role_id
        ];

        $responseAkses = curl_request("GET", "/accessLists", $token, $payload);

        if ($responseAkses["code"] === 200) {
            $dataAkses = json_decode($responseAkses["body"])->data;

            $result = array();
            $child = array();
            for ($i = 0; $i < count($dataAkses); $i++) {
                $child = $dataAkses[$i]->child;
                for ($j = 0; $j < count($child); $j++) {
                    $access = array();
                    if ($this->request->getPost("create_" . $child[$j]->menu_url_id) !== null) {
                        array_push($access, 'c');
                    }
                    if ($this->request->getPost("read_" . $child[$j]->menu_url_id) !== null) {
                        array_push($access, 'r');
                    }
                    if ($this->request->getPost("update_" . $child[$j]->menu_url_id) !== null) {
                        array_push($access, 'u');
                    }
                    if ($this->request->getPost("delete_" . $child[$j]->menu_url_id) !== null) {
                        array_push($access, 'd');
                    }
                    if ($this->request->getPost("print_" . $child[$j]->menu_url_id) !== null) {
                        array_push($access, 'p');
                    }
                    if ($this->request->getPost("approve_" . $child[$j]->menu_url_id) !== null) {
                        array_push($access, 'a');
                    }
                    array_push(
                        $result,
                        (object) [
                            "parent_id" => $this->request->getPost("parent_" . $child[$j]->menu_url_id),
                            "menu_url_id" => $child[$j]->menu_url_id,
                            "action" => $access,
                        ]
                    );
                }
            }

            $payload = json_encode([
                "data" => $result,
                "role_id" => $role_id,
                "company_id" => $company_id,
            ]);

            $response = curl_request("POST", "/accessLists", $token, $payload);

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
        }
        else
        {
            $message = is_object(json_decode($responseAkses["body"])) ? json_decode($responseAkses["body"])->message : 'Menu berdasarkan role tidak ditemukan';

            $data = [
                "status"            => false,
                "message"    => $message,
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }
}