<?php

namespace App\Controllers\Accounting\AccountSupplier;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\AccountSupplierModel;
use App\Models\Sub_AkunsModel;

class AccountSupplierController extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $AccountSupplierModel;
    protected $SupplierModel;
    protected $Sub_AkunsModel;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->AccountSupplierModel = new AccountSupplierModel();
        $this->SupplierModel = new SupplierModel();
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->encrypter = \Config\Services::encrypter();
    }
    public function index()
    {
        $supplierModel = $this->SupplierModel->where('deletedAt', NULL)->findAll();
        $subAkunsModel = $this->Sub_AkunsModel->getAPAR("");
        foreach ($subAkunsModel as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }

        foreach ($supplierModel as &$val) {
            $val['hexid'] = bin2hex($this->encrypter->encrypt($val['id']));
        }

        $data = [
            "supplierModel" => $supplierModel,
            "subAkuns" => $subAkunsModel
        ];
        return view('Accounting/accountSupplier/index', $data);
    }

    public function allAccountSupplier()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "company_id"  => $this->this_company_id,
            "deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->AccountSupplierModel->getList($condition, $addCondition, $limit, $offset);
        $subAkunsModel = $this->Sub_AkunsModel->asObject()->findAll();

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($res['data'] as $data) {
            // var_dump($data);
            // exit;
            foreach ($subAkunsModel as $datas) {
                if ($data->ap_id == $datas->id) {
                    $dataNamaAP = $datas->no_sub;
                } elseif ($data->ap_id == NULL) {
                    $dataNamaAP = "-";
                }
                if ($data->ar_id == $datas->id) {
                    $dataNamaAR = $datas->no_sub;
                } elseif ($data->ar_id == NULL) {
                    $dataNamaAR = "-";
                }
            }

            if ($data->name) {
                $dataNamaSupplier = $data->name;
            } else {
                if ($data->type == "BAHAN BAKU") {
                    $dataNamaSupplier = "DEFAULT BAHAN BAKU";
                } else if ($data->type == "BAHAN PENOLONG") {
                    $dataNamaSupplier = "DEFAULT BAHAN PENOLONG";
                } else if ($data->type == "INTERNASIONAL") {
                    $dataNamaSupplier = "DEFAULT INTERNASIONAL";
                }
            }

            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "customer_name"           => $dataNamaSupplier,
                "ap_id"                 => $dataNamaAP,
                "ar_id"                 => $dataNamaAR,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }

    public function get()
    {
        $id = $this->request->getVar('id');
        $res = $this->AccountSupplierModel->where('id', $id)->first();
        // var_dump($this->request->getVar('id'));

        return response()->setJSON([
            'data' => $res,
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function saveAccountSupplier()
    {

        $this->AccountSupplierModel->insert([
            'supplier_id' => $this->request->getVar('supplier_id'),
            'ap_id' => $this->request->getVar('akun_ap_id'),
            'ar_id' => $this->request->getVar('akun_ar_id')
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Account Supplier Berhasil Ditambahkan"
        ]);
    }

    public function updateAccountSupplier()
    {
        $id = $this->request->getVar('id');

        $this->AccountSupplierModel->update($id, [
            'supplier_id' => $this->request->getVar('supplier_id'),
            'ap_id' => $this->request->getVar('akun_ap_id'),
            'ar_id' => $this->request->getVar('akun_ar_id')
        ]);

        return response()->setJSON([
            'token' => \csrf_hash(),
            'status' => true,
            'message' => "Account Supplier Berhasil Diupdate"
        ]);
    }

    public function deleteAccountSupplier()
    {
        $id = $this->request->getVar('id');

        $this->AccountSupplierModel->update($id, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Account Supplier Berhasil Dihapus"
        ]);
    }
}
