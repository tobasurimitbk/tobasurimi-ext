<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\EmployeeJamKerjaModel;
use App\Models\EmployeesModel;
use App\Models\JamKerjaDetailModel;
use App\Models\JamKerjaModel;
use Exception;

class EmployeeJamKerja extends BaseController
{
    protected $this_company_id;
    protected $employeesModel;
    protected $employeeJamKerjaModel;
    protected $jamKerjaModel;
    protected $jamKerjaDetailModel;
    protected $divisiModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->employeesModel = new EmployeesModel();
        $this->employeeJamKerjaModel = new EmployeeJamKerjaModel();
        $this->jamKerjaModel = new JamKerjaModel();
        $this->jamKerjaDetailModel = new JamKerjaDetailModel();
        $this->divisiModel = new DivisisModel();
    }

    public function index($id)
    {
        $id = decrypt($id);
        $employee = $this->employeesModel->getSingleEmployee($id);

        if ($employee == null) {
            return redirect()->to('employee');
        }

        $data = [
            'detail' => [],
            'employee' => $employee,
            'jamKerja' => $this->jamKerjaModel->where('company_id', $this->this_company_id)->where('divisi_id', $employee['division_id'])->findAll(),
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

    public function setting()
    {
        $divisi = $this->divisiModel->getDivisiAccess();
        $data = [
            'divisi' => $divisi
        ];
        return view('Master/jamKerja/setting', $data);
    }

    public function dropdownJamKerja()
    {
        $divisiId = $this->request->getVar('divisi_id');
        $jamKerja = $this->jamKerjaModel
            ->where('divisi_id', $divisiId)
            ->where('deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $jamKerja,
            'status' => true
        ]);
    }

    public function getJamKerjaKaryawan()
    {
        try {
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $bagianId = $this->request->getVar('bagian_id');

            $data = $this->employeeJamKerjaModel->getJamKerjaKaryawan(
                $bagianId,
                $tanggal
            );

            return response()->setJSON([
                'status' => true,
                'token' => csrf_token(),
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function updateJamKerjaKaryawan()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $jamKerjaId = $this->request->getVar('jam_kerja_id');
            $divisiId = $this->request->getVar('divisi_id');
            $listData = json_decode($_POST['listData']);

            foreach ($listData as $l) {
                $first = $this->employeeJamKerjaModel->where('tanggal', $tanggal)
                    ->where('employee_id', $l->id)
                    ->where('deletedAt', null)
                    ->first();

                if ($first == null) {
                    $this->employeeJamKerjaModel->insert([
                        'company_id' => $this->this_company_id,
                        'divisi_id' => $divisiId,
                        'jam_kerja_id' => $jamKerjaId,
                        'employee_id' => $l->id,
                        'tanggal' => $tanggal
                    ]);
                } else {
                    $this->employeeJamKerjaModel->update($first['id'], [
                        'company_id' => $this->this_company_id,
                        'divisi_id' => $divisiId,
                        'jam_kerja_id' => $jamKerjaId,
                        'employee_id' => $l->id,
                        'tanggal' => $tanggal
                    ]);
                }
            }

            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Jam kerja karyawan berhasil diupdate"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }
}
