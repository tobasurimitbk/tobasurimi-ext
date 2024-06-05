<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\EmployeeJamKerjaModel;
use App\Models\EmployeesModel;
use App\Models\JamKerjaDetailModel;
use App\Models\JamKerjaModel;

class EmployeeJamKerja extends BaseController
{
    protected $this_company_id;
    protected $employeesModel;
    protected $employeeJamKerjaModel;
    protected $jamKerjaModel;
    protected $jamKerjaDetailModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->employeesModel = new EmployeesModel();
        $this->employeeJamKerjaModel = new EmployeeJamKerjaModel();
        $this->jamKerjaModel = new JamKerjaModel();
        $this->jamKerjaDetailModel = new JamKerjaDetailModel();
    }

    public function index($id)
    {
        $id = decrypt($id);
        $employee = $this->employeesModel->getSingleEmployee($id);

        if ($employee == null) {
            return redirect()->to('employee');
        }

        $data = [
            'detail' => null,
            'employee' => $employee,
            'jamKerja' => $this->jamKerjaModel->where('company_id', $this->this_company_id)->findAll(),
        ];

        if (!empty($this->request->getGet('bulan'))) {
            $data['detail'] = $this->employeeJamKerjaModel->getDetailJamKerjaByEmployee($id, $this->request->getVar('bulan'));
        }

        return view('hr/jamKerjaEmployee/index', $data);
    }

    public function createOrUpdate()
    {
        $tanggal = $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "";
        $employeeId = $this->request->getVar('employee_id');
        $divisiId = $this->request->getVar('divisi_id');
        $jamKerjaId = $this->request->getVar('jam_kerja_id');

        $first = $this->employeeJamKerjaModel->where('tanggal', $tanggal)
            ->where('employee_id', $employeeId)
            ->where('deletedAt', null)
            ->first();

        if ($first == null) {
            $this->employeeJamKerjaModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $divisiId,
                'jam_kerja_id' => $jamKerjaId,
                'employee_id' => $employeeId,
                'tanggal' => $tanggal
            ]);
        } else {
            $this->employeeJamKerjaModel->update($first['id'], [
                'company_id' => $this->this_company_id,
                'divisi_id' => $divisiId,
                'jam_kerja_id' => $jamKerjaId,
                'employee_id' => $employeeId,
                'tanggal' => $tanggal
            ]);
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Jam kerja karyawan berhasil diupdate"
        ]);
    }

    public function getDetailJamKerja()
    {
        $jamKerjaId = $this->request->getVar('jam_kerja_id');
        $result = $this->jamKerjaDetailModel->where('jam_kerja_id', $jamKerjaId)->where('deletedAt', null)->findAll();
        return response()->setJSON([
            'status' => true,
            'data' => $result,
            'token' => csrf_hash()
        ]);
    }
}
