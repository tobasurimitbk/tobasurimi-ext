<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use Config\Services;
use App\Models\FormPerijinanModel;
use App\Models\EmployeesModel;
use App\Models\MetadataModel;
use Exception;

class Perijinan extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $encrypter;
    protected $FormPerijinanModel;
    protected $EmployeesModel;
    protected $DivisiModel;
    protected $MetadataModel;
    protected $EmployeeModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->DivisiModel = new DivisisModel();
        $this->MetadataModel = new MetadataModel();
        $this->EmployeeModel = new EmployeesModel();
        $this->FormPerijinanModel = new FormPerijinanModel();
    }

    public function perijinan()
    {
        return view('hr/perijinan/index', [
            'year' => date("Y"),
            'month' => date("m")
        ]);
    }

    public function createView()
    {
        $data = [
            "status" => $this->MetadataModel->where('name', "Status Perizinan")
                ->whereNotIn('value', ['HADIR_H', 'LIBUR_L', 'ALPHA_A'])
                ->orderBy('name', "ASC")
                ->findAll(),
            "divisi" => $this->DivisiModel->get_by_company_id($this->this_company_id),
        ];

        return view('hr/perijinan/form', $data);
    }

    public function getEmployeeByDivision()
    {
        $divisiId = $this->request->getVar('divisionID');

        return response()->setJSON([
            'data' => $this->EmployeeModel->where('deletedAt', null)
                ->where('division_id', $divisiId)
                ->orderBy('name', "ASC")
                ->findAll(),
            'token' => \csrf_hash(),
        ]);
    }

    public function getById($id)
    {
        $formPerijinanDate = $this->FormPerijinanModel
            ->selectMax('periode', 'max_tanggal')
            ->selectMin('periode', 'min_tanggal')
            ->where('kode', $id)
            ->first();

        $data = [
            "status" => $this->MetadataModel
                ->where('name', "Status Perizinan")
                ->whereNotIn('value', ['HADIR_H', 'LIBUR_L', 'ALPHA_A'])
                ->orderBy('name', "ASC")
                ->findAll(),
            "divisi" => $this->DivisiModel->get_by_company_id($this->this_company_id),
            "formPerijinan" => $this->FormPerijinanModel
                ->select('form_perijinan.*, employees.division_id, employees.name')->where('kode', $id)
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

        $monthYear = explode('-', $this->request->getVar('month'));

        $condition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $this->this_company_id,
            'MONTH(form_perijinan.periode)' => $monthYear[1],
            'YEAR(form_perijinan.periode)' => $monthYear[0]
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $dataEmployee = [];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $FormData = $this->FormPerijinanModel->getPerijinanList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        foreach ($FormData['data'] as $data) {
            $formPerijinan = $this->FormPerijinanModel
                ->selectMax('periode', 'max_tanggal')
                ->selectMin('periode', 'min_tanggal')
                ->where('kode', $data->kode)
                ->first();

            $startDate = date_format(date_create($formPerijinan['min_tanggal']), "d/m/Y");
            $endDate = date_format(date_create($formPerijinan['max_tanggal']), "d-m-Y");
            $periode = $startDate . " S.D " . $endDate;

            $status = explode('_', $data->status)[0];

            array_push($dataEmployee, [
                "no" => $no++,
                "id" =>  $data->id,
                "kode" => $data->kode,
                "name" => $data->name,
                "nip" => $data->nip,
                "divisi" => $data->divisi,
                "periode" => $periode,
                "status" => $status,
                "is_approval" => (float)$data->is_approval,
                "reason" => $data->reason
            ]);
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $FormData['totalData'],
            "recordsFiltered" => $FormData['totalFilteredData'],
            "data" => $dataEmployee,
        ];

        return response()->setJSON($data);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            helper('text');
            $kode = random_string(20);

            $employeeId = $this->request->getPost("employee_id");
            $status = $this->request->getPost('status');
            $reason = $this->request->getPost('reason');
            $isApproval = $this->request->getPost('is_approval');
            $tglAwalFormated = $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "";
            $tglAkhirFormated = $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "";

            $tglAwal = strtotime($tglAwalFormated);
            $tglAkhir = strtotime($tglAkhirFormated);

            if ($tglAkhir < $tglAwal) {
                return response()->setJSON([
                    'message' => "Tanggal mulai dan tanggal selesai tidak valid",
                    'token' => csrf_hash(),
                    'status' => false,
                ]);
            }

            // Hapus data sebelumnya jika ada
            // Data range
            $this->FormPerijinanModel
                ->where('employee_id', $employeeId)
                ->where('periode >=', $tglAwal)
                ->where('periode <=', $tglAkhir)
                ->delete();

            $insertData = array();

            for ($currentDate = $tglAwal; $currentDate <= $tglAkhir; $currentDate += 86400) {
                $currentDateFormatted = date('Y-m-d', $currentDate);

                array_push($insertData, [
                    "company_id" => $this->this_company_id,
                    "employee_id" => $employeeId,
                    "periode" => $currentDateFormatted,
                    "status" => $status,
                    "reason" => $reason,
                    "kode" => $kode,
                    "is_approval" => $isApproval == "on" ? '1' : '0',
                ]);
            }
            $this->FormPerijinanModel->insertBatch($insertData);
            $db->transCommit();

            return response()->setJSON([
                'message' => "Form Perijinan berhasil disimpan",
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function update()
    {
        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            helper('text');
            $kode = random_string(20);

            $employeeId = $this->request->getPost("employee_id");
            $status = $this->request->getPost('status');
            $reason = $this->request->getPost('reason');
            $isApproval = $this->request->getPost('is_approval');
            $tglAwalFormated = $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "";
            $tglAkhirFormated = $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "";

            $tglAwal = strtotime($tglAwalFormated);
            $tglAkhir = strtotime($tglAkhirFormated);

            // deleted first
            $this->FormPerijinanModel->where('kode', $this->request->getVar('kode'))->delete();
            // Data range hindari duplikasi
            $this->FormPerijinanModel
                ->where('employee_id', $employeeId)
                ->where('periode >=', $tglAwal)
                ->where('periode <=', $tglAkhir)
                ->delete();

            $insertData = array();
            // insert again
            for ($currentDate = $tglAwal; $currentDate <= $tglAkhir; $currentDate += 86400) {
                $currentDateFormatted = date('Y-m-d', $currentDate);

                array_push($insertData, [
                    "company_id" => $this->this_company_id,
                    "employee_id" => $employeeId,
                    "status" => $status,
                    "reason" => $reason,
                    "is_approval" => $isApproval == "on" ? '1' : '0',
                    "kode" => $kode,
                    "periode" => $currentDateFormatted
                ]);
            }
            $this->FormPerijinanModel->insertBatch($insertData);
            $db->transCommit();

            return response()->setJSON([
                'message' => "Form Perijinan berhasil diupdate",
                'kode' => $kode,
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function delete()
    {
        try {
            $kode = $this->request->getPost("kode");
            $this->FormPerijinanModel->where('kode', $kode)->delete();
            return response()->setJSON([
                'status' => true,
                'message' => "Form perizinan berhasil dihapus"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }
}
