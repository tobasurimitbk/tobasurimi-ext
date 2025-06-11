<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\HROutsourcingCompanyModel;
use App\Models\HROutsourcingEmployeeModel;

class Company extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $hrOutsourcingCompanyModel;
    protected $hrOutsourcingEmployeeModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->hrOutsourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->hrOutsourcingEmployeeModel = new HROutsourcingEmployeeModel();
    }

    public function index()
    {
        $data = [
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('HROutsourcing/company/index', $data);
    }


    public function all()
    {
        $payload = [
            "pageSize"         => $this->request->getVar("length"),
            "currentPage"      => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort"             => $this->request->getVar("sort"),
            "sortType"         => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "divisi_id"     => $this->request->getVar('divisi_id'),
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
        ];

        $condition = [
            'hr_outsourcing_company.company_id' => $this->this_company_id
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $sppData = $this->hrOutsourcingCompanyModel->getList($condition, $addCondition, $limit, $offset);

        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($sppData['data'] as $data) {
            $totalEmployee = count($this->hrOutsourcingEmployeeModel->where('company_id', $data->id)->findAll());
            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "divisi"      => $data->divisi,
                "name" => $data->name,
                "total" => $totalEmployee,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $sppData['totalData'],
            "recordsFiltered"   => $sppData['totalFilteredData'],
            "data"              => $dataSPP,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function store()
    {
        $name = $this->request->getVar('name');
        $divisiId = $this->request->getVar('divisi_id');
        $address = $this->request->getVar('address');

        // $check = $this->hrOutsourcingCompanyModel->where('company_id', $this->this_company_id)
        //     ->where('name', $name)
        //     ->first();

        // if ($check != null) {
        //     return response()->setJSON([
        //         'message' => "Data Company Sudah ada",
        //         'status' => false,
        //         'token' => csrf_hash()
        //     ]);
        // }

        $this->hrOutsourcingCompanyModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $divisiId,
            'name' => $name,
            'address' => $address
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Company Outsourcing Berhasil Disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $name = $this->request->getVar('name');
        $divisiId = $this->request->getVar('divisi_id');
        $address = $this->request->getVar('address');

        // $check = $this->hrOutsourcingCompanyModel
        //     ->where('company_id', $this->this_company_id)
        //     ->where('name', $name)
        //     ->where('id !=', $id)
        //     ->first();

        // if ($check != null) {
        //     return response()->setJSON([
        //         'message' => "Data Company Sudah ada",
        //         'status' => false,
        //         'token' => csrf_hash()
        //     ]);
        // }

        $this->hrOutsourcingCompanyModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $divisiId,
            'name' => $name,
            'address' => $address
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Company Outsourcing Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function destroy()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->hrOutsourcingCompanyModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Company Outsourcing Berhasil Dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get($id)
    {
        $id = decrypt($id);
        $hrOutsourcingCompany = $this->hrOutsourcingCompanyModel->find($id);
        return response()->setJSON([
            'status' => true,
            'data' => $hrOutsourcingCompany,
            'token' => csrf_hash()
        ]);
    }
}
