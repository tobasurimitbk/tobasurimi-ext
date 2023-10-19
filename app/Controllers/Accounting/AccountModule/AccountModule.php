<?php

namespace App\Controllers\Accounting\AccountModule;

use App\Controllers\BaseController;
use App\Models\AccountModuleModel;
use App\Models\Sub_AkunsModel;

class AccountModule extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
    }

    public function index()
    {
        $accountModuleModel = new AccountModuleModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $accountModuleData = $accountModuleModel->asObject()->findAll();
        $subAkunsModel = $Sub_AkunsModel->asObject()->findAll();

        $data = [
            "dataAccountModule" => $accountModuleData,
            "subAkuns" => $subAkunsModel
        ];

        return view('Accounting/accountModule/index', $data);
    }

    public function allAccountModule()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $accountModuleModel = new AccountModuleModel();
        $Sub_AkunsModel = new Sub_AkunsModel();
        $dataNamaAP = "";
        $dataNamaAR = "";

        $accountModuleData = $accountModuleModel->asObject()->findAll();
        $subAkunsModel = $Sub_AkunsModel->asObject()->findAll();
        // $condition = [
        //     "barangs.company_id"  => $this->this_company_id,
        //     "kategori"              => "LOKAL",
        //     "barangs.type"        => "BAHAN BAKU"
        // ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $accountModuleData = $accountModuleModel->getAccountModuleList($addCondition, $limit, $offset);

        $dataAccountModule = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($accountModuleData['data'] as $data) {
            foreach ($subAkunsModel as $datas) {
                if ($data->ap_id == $datas->id) {
                    $dataNamaAP = $datas->no_sub;
                }
                if ($data->ar_id == $datas->id) {
                    $dataNamaAR = $datas->no_sub;
                }
            }
            array_push($dataAccountModule, [
                "no"            => $no++,
                "id"            => $data->id,
                "name"          => $data->name,
                "ap_id"       => $dataNamaAP,
                "ar_id" => $dataNamaAR,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $accountModuleData['totalData'],
            "recordsFiltered"   => $accountModuleData['totalFilteredData'],
            "data"              => $dataAccountModule,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveAccountModule()
    {
        try {
            $accountModuleModel = new AccountModuleModel();

            $accountModuleData = $accountModuleModel->asObject()->findAll();

            $rules = [
                "name" => [
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

            $payload = json_encode([
                "name"              => $this->request->getPost("name"),
                "type"           => $this->request->getPost("tipe"),
                "kategori"           => $this->request->getPost("kategori"),
                "module"           => $this->request->getPost("module"),
                "ap_id"           => $this->request->getPost("akun_ap_id"),
                "ar_id"           => $this->request->getPost("akun_ar_id")
            ]);

            $insertData = [
                "name"              => $this->request->getPost("name"),
                "type"           => $this->request->getPost("tipe"),
                "kategori"           => $this->request->getPost("kategori"),
                "module"           => $this->request->getPost("module"),
                "ap_id"           => $this->request->getPost("akun_ap_id"),
                "ar_id"           => $this->request->getPost("akun_ar_id")
            ];
            $insert = $accountModuleModel->insert($insertData);

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => $payload,
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $payload,
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function updateAccountModule()
    {
        try {
            $accountModuleModel = new AccountModuleModel();

            $accountModuleData = $accountModuleModel->asObject()->findAll();

            $rules = [
                "name" => [
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
                $id = $this->request->getPost("id");

                $payload = [
                    "name"              => $this->request->getPost("name"),
                    "type"           => $this->request->getPost("tipe"),
                    "kategori"           => $this->request->getPost("kategori"),
                    "module"           => $this->request->getPost("module"),
                    "ap_id"           => $this->request->getPost("akun_ap_id"),
                    "ar_id"           => $this->request->getPost("akun_ar_id")
                ];
            }

            if ($payload) {
                $accountModuleModel->update($id, $payload);

                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function getByIdAccountModule($id)
    {
        $accountModuleModel = new AccountModuleModel();
        $accountModuleData = $accountModuleModel->getAccountModuleById($id);

        if (!$accountModuleData) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }

        // $response = curl_request("GET", "/barangs/$id?idCompany=$this->this_company_id", $this->token);

        $accountModuleData->list_address = []; // cek nanti
        $data = [
            "status"    => true,
            "data"      => $accountModuleData,
        ];
        echo json_encode($data);

        return;
    }

    public function deleteAccountModule()
    {
        try {
            $accountModuleModel = new AccountModuleModel();
            $id = $this->request->getPost("id");

            if (empty($id)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $accountModuleModel->delete($id);
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
            return;
        }
    }

    // public function dropdownSupplier()
    // {
    //     $supplierTipeModel = new SupplierTipeModel();
    //     $dataSupplier = $supplierTModel->getSupplierByKategoriAndType('LOKAL', 'BAHAN BAKU', $this->this_company_id);

    //     $data = [
    //         "data" => $dataSupplier
    //     ];

    //     echo json_encode($data);
    //     return;
    // }

    // public function supplierAjax()
    // {
    //     $supplierModel = new SupplierModel();
    //     $id = $this->request->getGet("id");

    //     if (!empty($id)) {
    //         $response = $supplierModel->getSupplierById($id);
    //         if ($response) {
    //             $data = [
    //                 "status"  => true,
    //                 "data"  => $response
    //             ];
    //             echo json_encode($data);
    //         } else {
    //             $message = 'Data Gagal Ditemukan';
    //             $data = [
    //                 "status" => false,
    //                 "message"  => $message
    //             ];
    //             echo json_encode($data);
    //         }
    //     } else {
    //         $data = [
    //             "status"            => false,
    //             "message"    => "Tidak Ada Id"
    //         ];
    //         echo json_encode($data);
    //     }

    //     return;
    // }
}
