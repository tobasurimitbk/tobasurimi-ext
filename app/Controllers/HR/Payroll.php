<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesModel;
use App\Models\EmployeesModel;
use App\Models\GajiConjunctionModel;
use App\Models\PayrollsModel;

class Payroll extends BaseController
{
    protected $token;
    protected $this_company_id, $userID;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userID = session()->get('login')->user_id;
    }

    public function payroll()
    {
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        $payrollModel = new PayrollsModel();

        $isPosted = $payrollModel->where('company_id', $this->this_company_id)
            ->where('year_month', $year . "-" . $month)
            ->where('isPosted', 1)
            ->countAllResults();

        $isGenerate = $payrollModel->where('company_id', $this->this_company_id)
            ->where('year_month', $year . "-" . $month)
            ->where('isPosted', 0)
            ->countAllResults();

        $data = [
            'year' => $year,
            'month' => $month,
            'isPosted' => ($isPosted == 0) ? \false : \true,
            'isGenerate' => ($isGenerate == 0) ? \false : \true,
        ];

        return view('hr/payroll/index', $data);
    }

    public function getAllPayRoll()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "nip"           => $this->request->getGet("nip"),
            "name"          => $this->request->getGet("name"),
            "divisi"        => $this->request->getGet("divisi"),
        ];

        // Bulan, Tahun
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');

        $condition = [
            'employees.company_id' => $this->this_company_id,
            "employees.deletedAt" => null,
            "employees.company_id" => $this->this_company_id,
            "employees.id != " => $this->userID, // kecualikan admin yg akses
            "year_month" => $year . "-" . $month,
        ];

        $addCondition = [
            "name"          => $this->request->getGet("name"),
            "divisi"        => $this->request->getGet("divisi"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $payrollModel = new PayrollsModel();

        $payrollData = $payrollModel->getList($condition, $addCondition, $limit, $offset);
        $dataPayRolls = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($payrollData['data'] as $p) {
            array_push($dataPayRolls, [
                "no" => $no++,
                "id" => $p->id,
                "nip" => $p->employeesNIP,
                "name"  => $p->employeesName,
                "divisi" => $p->divisiName,
                "alpha" => $p->alpha,
                "hadir" => $p->hadir,
                "ijin" => $p->ijin,
                "cuti" => $p->cuti,
                "sakit" => $p->sakit,
                "libur" => $p->libur
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $payrollData['totalData'],
            "recordsFiltered"   => $payrollData['totalFilteredData'],
            "data"              => $dataPayRolls,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function generatePayroll()
    {
        $year = $this->request->getVar("year");
        $month = $this->request->getVar("month");

        $attendanceModel = new AttendancesModel();
        $payrollModel = new PayrollsModel();
        $employeesModel = new EmployeesModel();

        $dataAbsensiPosted = $attendanceModel
            ->where('LEFT(periode, 7)', $year . "-" . $month)
            ->where('company_id', $this->this_company_id)
            ->where('isPosting', 1)
            ->countAllResults();

        if ($dataAbsensiPosted != 0) {

            // delete firts if ada
            $payrollModel->where('company_id', $this->this_company_id)
                ->where('year_month', $year . "-" . $month)
                ->delete();

            // get employees
            $employeesData = $employeesModel->getEmployees($this->this_company_id);

            // Insert Again
            foreach ($employeesData as $e) {
                $status = $attendanceModel->getStatusAttendances($year, $month, $e['id']);
                $payrollModel->insert([
                    'company_id' => $e['company_id'],
                    'employee_id' => $e['id'],
                    'year_month' => $year . "-" . $month,
                    "alpha" => $status['ALPHA'],
                    "hadir" => $status['HADIR'],
                    "izin" => $status['IJIN'],
                    "cuti" => $status['CUTI'],
                    "sakit" => $status['SAKIT'],
                    "libur" => $status['LIBUR']
                ]);
            }

            return \response()->setJSON([
                'message' => "Data Payroll berhasil digenerate",
                'status' => true
            ]);
        } else {
            return \response()->setJSON([
                'message' => "Data absensi bulan " . $year . "-" . $month . " belum diposting",
                'status' => false
            ]);
        }
    }

    public function postingPayroll()
    {
        $year = $this->request->getVar("year");
        $month = $this->request->getVar("month");

        $payrollModel = new PayrollsModel();

        $payrollModel->where('company_id', $this->this_company_id)
            ->where('year_month', $year . "-" . $month)
            ->set('isPosted', 1) // Menggunakan set() untuk mengubah kolom
            ->update();

        return \response()->setJSON([
            'message' => "Data Payroll berhasil diposting",
            'status' => true
        ]);
    }

    public function getComponentGaji()
    {
        $payrollID = $this->request->getVar('payrollID');

        $payrollModel = new PayrollsModel();
        $gajiModel = new GajiConjunctionModel();

        $selectQryGaji = "
            tunjangan.name,
            gaji_conjunction.nominal
        ";

        $selectQryPayroll = "
            payrolls.*,
            employees.name,
            divisis.divisi
        ";

        $payroll = $payrollModel->select($selectQryPayroll)
            ->join('employees', 'employees.id = payrolls.employee_id', 'INNER')
            ->join('divisis', 'divisis.id = employees.division_id', 'INNER')
            ->where('payrolls.id', $payrollID)
            ->first();

        $gaji = $gajiModel->select($selectQryGaji)
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
            ->where('gaji_conjunction.employee_id', $payroll['employee_id'])
            ->findAll();


        $data = [
            'gaji' => $gaji,
            'payroll' => $payroll
        ];

        return \response()->setJSON([
            'data' => $data,
            'status' => true
        ]);
    }
}
