<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use Config\Services;
use App\Models\FormPerijinanModel;
use App\Models\EmployeesModel;

class Perijinan extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $FormPerijinanModel;
    protected $EmployeesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
    }

    public function perijinan()
    {
        return view('hr/perijinan/index');
    }

    public function createView()
    {
        $DivisiModel = new DivisisModel();

        $data = [
            "status" => ["IJIN", "CUTI", "SAKIT", "RL"],
            "divisi" => $DivisiModel->get_by_company_id($this->this_company_id),
        ];

        return view('hr/perijinan/form', $data);
    }

    public function getEmployeeByDivision()
    {
        $EmployeesModel = new EmployeesModel();
        return \response()->setJSON([
            'data' => $EmployeesModel->where('deletedAt', null)
                ->where('division_id', $this->request->getVar('divisionID'))
                ->orderBy('name', "ASC")
                ->findAll(),
            'token' => \csrf_hash(),
        ]);
    }

    public function getById($id)
    {
        $FormPerijinanModel = new FormPerijinanModel();
        $DivisiModel = new DivisisModel();

        $formPerijinanDate = $FormPerijinanModel
            ->selectMax('periode', 'max_tanggal')
            ->selectMin('periode', 'min_tanggal')
            ->where('kode', $id)
            ->first();

        $data = [
            "status" => ["IJIN", "CUTI", "SAKIT"],
            "divisi" => $DivisiModel->get_by_company_id($this->this_company_id),
            "formPerijinan" => $FormPerijinanModel->select('form_perijinan.*, employees.division_id, employees.name')->where('kode', $id)
                ->join('employees', 'employees.id = form_perijinan.employee_id')
                ->first(),
            "mulai" => $formPerijinanDate['min_tanggal'],
            "selesai" => $formPerijinanDate['max_tanggal']
        ];

        return view('hr/perijinan/form', $data);
    }

    public function allPerijinan()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $FormPerijinanModel = new FormPerijinanModel();

        $condition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $this->this_company_id
        ];

        $addCondition = [
            "nip"        => $this->request->getGet("nip"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $dataEmployee = [];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $FormData = $FormPerijinanModel->getPerijinanList($condition, $addCondition, $limit, $offset);

        foreach ($FormData['data'] as $data) {
            $formPerijinan = $FormPerijinanModel
                ->selectMax('periode', 'max_tanggal')
                ->selectMin('periode', 'min_tanggal')
                ->where('kode', $data->kode)
                ->first();

            array_push($dataEmployee, [
                "no" => $no++,
                "id" =>  $data->id,
                "kode" => $data->kode,
                "employeeName" => $data->name,
                "employeeNip" => $data->nip,
                "divisionName" => $data->divisi,
                "mulai" => date_format(date_create($formPerijinan['min_tanggal']), "d-m-Y"),
                "selesai" => date_format(date_create($formPerijinan['max_tanggal']), "d-m-Y"),
                "keterangan" => $data->status,
                "approval" => ($data->is_approval) ? "Approved" : "Not Approved"
            ]);
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $FormData['totalData'],
            "recordsFiltered" => $FormData['totalFilteredData'],
            "data" => $dataEmployee,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        helper('text');

        // declare model
        $FormPerijinanModel = new FormPerijinanModel();
        $kode = random_string(20);

        $tglAkhir = strtotime($this->request->getVar('end_date'));
        $tglAwal = strtotime($this->request->getVar('start_date'));

        for ($currentDate = $tglAwal; $currentDate <= $tglAkhir; $currentDate += 86400) {
            $currentDateFormatted = date('Y-m-d', $currentDate);
            $insertData['periode'] = $currentDateFormatted;
            // delete if sudah ada (menghindari duplikasi)
            $FormPerijinanModel->where('periode', $currentDateFormatted)
                ->where('employee_id', $this->request->getPost("employee_id"))
                ->delete();
            // insert data
            $insertData = [
                "company_id" => $this->this_company_id,
                "employee_id" => $this->request->getPost("employee_id"),
                "start_date" => $this->request->getPost("start_date"),
                "end_date" => $this->request->getPost("end_date"),
                "status" => $this->request->getPost("status"),
                "reason" => $this->request->getPost("reason"),
                "is_approval" => $this->request->getPost('is_approval') == "on" ? '1' : '0',
                "kode" => $kode,
                "periode" => $currentDateFormatted
            ];
            // insert again
            $FormPerijinanModel->insert($insertData);
        }

        return \response()->setJSON([
            'message' => "Form Perijinan berhasil disimpan",
            'status' => true
        ]);
    }

    public function update()
    {
        helper('text');

        // declare model
        $FormPerijinanModel = new FormPerijinanModel();
        $kode = random_string(20);

        $tglAkhir = strtotime($this->request->getVar('end_date'));
        $tglAwal = strtotime($this->request->getVar('start_date'));

        // deleted first
        $FormPerijinanModel->where('kode', $this->request->getVar('kode'))->delete();

        // insert again
        for ($currentDate = $tglAwal; $currentDate <= $tglAkhir; $currentDate += 86400) {
            $currentDateFormatted = date('Y-m-d', $currentDate);
            $insertData['periode'] = $currentDateFormatted;
            // delete if sudah ada (menghindari duplikasi)
            $FormPerijinanModel->where('periode', $currentDateFormatted)
                ->where('employee_id', $this->request->getPost("employee_id"))
                ->delete();
            // insert data
            $insertData = [
                "company_id" => $this->this_company_id,
                "employee_id" => $this->request->getPost("employee_id"),
                "start_date" => $this->request->getPost("start_date"),
                "end_date" => $this->request->getPost("end_date"),
                "status" => $this->request->getPost("status"),
                "reason" => $this->request->getPost("reason"),
                "is_approval" => $this->request->getPost('is_approval') == "on" ? '1' : '0',
                "kode" => $kode,
                "periode" => $currentDateFormatted
            ];
            // insert again
            $FormPerijinanModel->insert($insertData);
        }

        return \response()->setJSON([
            'message' => "Form Perijinan berhasil diupdate",
            'kode' => $kode,
            'status' => true
        ]);
    }

    public function delete()
    {
        $kode = $this->request->getPost("kode");

        $FormPerijinanModel = new FormPerijinanModel();

        $FormPerijinanModel->where('kode', $kode)->delete();

        return \response()->setJSON([
            'status' => true,
            'message' => "Form perizinan berhasil dihapus"
        ]);
    }
}
