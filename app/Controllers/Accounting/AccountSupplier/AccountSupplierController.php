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
    protected $is_admin;
    protected $this_user_id;
    protected $AccountSupplierModel;
    protected $SupplierModel;
    protected $Sub_AkunsModel;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->AccountSupplierModel = new AccountSupplierModel();
        $this->SupplierModel = new SupplierModel();
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->encrypter = \Config\Services::encrypter();
    }
    public function index()
    {
        if ($this->is_admin == 1) {
            $supplierModel = $this->SupplierModel->where('deletedAt', NULL)->findAll();
        } else {
            $supplierModel = $this->SupplierModel->where('deletedAt', NULL)->where('user_id', $this->this_user_id)->findAll();
        }
        $subAkunsModel = $this->Sub_AkunsModel->getAPAR($this->this_company_id);
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


        if ($this->is_admin == 1) {
            $condition = [
                "suppliers.company_id"  => $this->this_company_id,
                "account_supplier.company_id"  => $this->this_company_id,
                "suppliers.deletedAt" => NULL
            ];
        } else {
            $condition = [
                "suppliers.company_id"  => $this->this_company_id,
                // "account_supplier.company_id"  => $this->this_company_id,
                "suppliers.deletedAt" => NULL,
                "suppliers.user_id" => $this->this_user_id
            ];
        }

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->AccountSupplierModel->getList($condition, $addCondition, $limit, $offset);
        $subAkunsModel = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataNamaAP = "-";
        $dataNamaAR = "-";


        foreach ($res['data'] as $data) {
            // var_dump($data);
            $dataNamaAP = $data->ap_id ? $this->Sub_AkunsModel->find($data->ap_id)['no_sub'] : '-';
            $dataNamaAR = $data->ar_id ? $this->Sub_AkunsModel->find($data->ar_id)['no_sub'] : '-';
            // foreach ($subAkunsModel as $datas) {
            //     if ($data->ap_id == $datas->id) {
            //     } elseif ($data->ap_id == NULL) {
            //         $dataNamaAP = "-";
            //     }
            //     if ($data->ar_id == $datas->id) {
            //         $dataNamaAR = $datas->no_sub;
            //     } elseif ($data->ar_id == NULL) {
            //         $dataNamaAR = "-";
            //     }
            // }

            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "supplier_name"         => $data->name,
                "id_supplier"           => $data->id_supplier,
                "ap_id"                 => $dataNamaAP,
                "ar_id"                 => $dataNamaAR,
            ]);
        }
        // exit;

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
            'company_id' => $this->this_company_id,
            'ap_id' => $this->request->getVar('akun_ap_id')
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
            'company_id' => $this->this_company_id,
            'ap_id' => $this->request->getVar('akun_ap_id')
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
