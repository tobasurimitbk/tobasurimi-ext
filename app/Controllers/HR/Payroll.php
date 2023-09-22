<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendanceKeterlambatanModel;
use App\Models\AttendancesModel;
use App\Models\DivisisModel;
use App\Models\EmployeesModel;
use App\Models\FormLemburModel;
use App\Models\FormPerizinanNotApprovedModel;
use App\Models\PayrollGajiConjunctionModel;
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
        $divisiModel = new DivisisModel();

        $isGenerate = $payrollModel->where('company_id', $this->this_company_id)
            ->where('year_month', $year . "-" . $month)
            ->where('isPosted', 0)
            ->countAllResults();

        $data = [
            'year' => $year,
            'month' => $month,
            'divisi' => $divisiModel->get_by_company_id($this->this_company_id),
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
            "divisi_id"          => $this->request->getGet("divisi_id"),
            "employee_id"        => $this->request->getGet("employee_id"),
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
            "divisi_id"          => $this->request->getGet("divisi_id"),
            "employee_id"        => $this->request->getGet("employee_id"),
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
                "employee_id" => $p->employee_id,
                "nip" => $p->employeesNIP,
                "name"  => $p->employeesName,
                "divisi" => $p->divisiName,
                "hariKerja" => $p->hadir . " Hari",
                "totalGajiLembur" => "Rp " . number_format($p->nominal_uang_gaji + $p->nominal_uang_lembur, 0, ',', '.'),
                "totalPenguranganGaji" => "Rp " . number_format($p->nominal_pengurangan_gaji, 0, ',', '.'),
                "sisaGaji" => "Rp " . number_format($p->nominal_gaji_diterima, 0, ',', '.'),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $payrollData['totalData'],
            "recordsFiltered"   => $payrollData['totalFilteredData'],
            "data"              => $dataPayRolls,
            "payload"           => $payload,
            'test' => $addCondition
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
        $FormPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $AttendanceKeterlambatanModel = new AttendanceKeterlambatanModel();
        $PayrollGajiModel = new PayrollGajiConjunctionModel();

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
                // payroll insert
                $payrollID = $payrollModel->insert([
                    "company_id" => $e['company_id'],
                    "employee_id" => $e['id'],
                    "year_month" => $year . "-" . $month,
                    "alpha" => $status['ALPHA'],
                    "hadir" => $status['HADIR'],
                    "izin" => $status['IJIN'],
                    "cuti" => $status['CUTI'],
                    "sakit" => $status['SAKIT'],
                    "libur" => $status['LIBUR'],
                    "nominal_uang_gaji" => 0,
                    "nominal_uang_lembur" => 0,
                    "nominal_pengurangan_gaji" => 0,
                    "nominal_gaji_diterima" => 0
                ]);

                // generate keterlambatan
                $AttendanceKeterlambatanModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $year . "-" . $month
                );

                // generate form perijinan not approved
                $FormPerizinanNotApprovedModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $year . "-" . $month
                );

                // generate payroll gaji 
                $PayrollGajiModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $year . "-" . $month
                );

                // update payroll
                $payrollFinal = $payrollModel->generate(
                    $this->this_company_id,
                    $e['id'],
                    $year . "-" . $month,
                    $payrollID,
                    $status['HADIR'],
                    0
                );

                $payrollModel->set('nominal_uang_gaji', $payrollFinal['nominal_uang_gaji'])
                    ->set('nominal_uang_lembur', $payrollFinal['nominal_uang_lembur'])
                    ->set('nominal_pengurangan_gaji', $payrollFinal['nominal_pengurangan_gaji'])
                    ->set('nominal_gaji_diterima', $payrollFinal['nominal_gaji_diterima'])
                    ->where('id', $payrollID)
                    ->update();
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

    public function repeatGeneratePayroll()
    {
        $year = $this->request->getVar("year");
        $month = $this->request->getVar("month");
        $employeeID = $this->request->getVar("employeeID");

        $attendanceModel = new AttendancesModel();
        $payrollModel = new PayrollsModel();
        $employeesModel = new EmployeesModel();
        $FormPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $AttendanceKeterlambatanModel = new AttendanceKeterlambatanModel();
        $PayrollGajiModel = new PayrollGajiConjunctionModel();

        // delete first
        $payrollModel->where('company_id', $this->this_company_id)
            ->where('employee_id', $employeeID)
            ->delete();

        // get employee
        $employeesData = $employeesModel->where('id', $employeeID)->first();

        $status = $attendanceModel->getStatusAttendances($year, $month, $employeesData['id']);
        // payroll insert
        $payrollID = $payrollModel->insert([
            "company_id" => $employeesData['company_id'],
            "employee_id" => $employeesData['id'],
            "year_month" => $year . "-" . $month,
            "alpha" => $status['ALPHA'],
            "hadir" => $status['HADIR'],
            "izin" => $status['IJIN'],
            "cuti" => $status['CUTI'],
            "sakit" => $status['SAKIT'],
            "libur" => $status['LIBUR'],
            "nominal_uang_gaji" => 0,
            "nominal_uang_lembur" => 0,
            "nominal_pengurangan_gaji" => 0,
            "nominal_gaji_diterima" => 0
        ]);

        // generate keterlambatan
        $AttendanceKeterlambatanModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $year . "-" . $month
        );

        // generate form perijinan not approved
        $FormPerizinanNotApprovedModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $year . "-" . $month
        );

        // generate payroll gaji 
        $PayrollGajiModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $year . "-" . $month
        );

        // update payroll
        $payrollFinal = $payrollModel->generate(
            $this->this_company_id,
            $employeesData['id'],
            $year . "-" . $month,
            $payrollID,
            $status['HADIR'],
            0
        );

        $payrollModel->set('nominal_uang_gaji', $payrollFinal['nominal_uang_gaji'])
            ->set('nominal_uang_lembur', $payrollFinal['nominal_uang_lembur'])
            ->set('nominal_pengurangan_gaji', $payrollFinal['nominal_pengurangan_gaji'])
            ->set('nominal_gaji_diterima', $payrollFinal['nominal_gaji_diterima'])
            ->set('nominal_penambahan_gaji', $payrollFinal['nominal_penambahan_gaji'])
            ->where('id', $payrollID)
            ->update();


        return \response()->setJSON([
            'message' => "Data Payroll atas nama " . $employeesData['name'] . " berhasil digenerate ulang",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function detailPayrollView($id)
    {
        $payrollModel = new PayrollsModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();
        $formLemburModel = new FormLemburModel();
        $rekapPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();

        $payroll = $payrollModel->where('id', $id)->first();

        $data = [
            'payrollDetail' => $payrollModel->detailPayroll($id),
            'gajiPerHari' => $payrollGajiModel->getNominalGajiPerHariPayroll($id),
            'nominalUangCadangan' => $payrollGajiModel->getNominalUangCadanganPayroll($id),
            'perhitunganGaji' => $payrollGajiModel->getPerhitunganKomponenGajiPayroll($id),
            'totalPerhitunganGaji' => $payrollGajiModel->getTotalKomponenGajiPayroll($id),
            'rekapKeterlambatanPresensi' => $attendanceTerlambatModel->rekap($id),
            'totalNominalKeterlambatanPresensi' => $attendanceTerlambatModel->getTotalRekap($id),
            'rekapLembur' => $formLemburModel->rekap($payroll['employee_id'], $payroll['year_month']),
            'rekapPerizinanNotApproved' => $rekapPerizinanNotApprovedModel->rekap($id),
            'totalNominalRekapPerizinanNotApproved' => $rekapPerizinanNotApprovedModel->getTotalRekap($id)

        ];

        return view('hr/payroll/form', $data);
    }

    public function updateNominalKomponenGaji()
    {
        $id = $this->request->getVar('komponenGajiID');
        $payrollID = $this->request->getVar('payrollID');
        $nominal = (int) preg_replace("/[^0-9]/", "", $this->request->getVar('nominal'));

        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $payrollModel = new PayrollsModel();

        $payrollGajiModel->update($id, [
            'nominal' => $nominal
        ]);

        $payrollModel->generateIfPayrollChanged($payrollID);

        return \response()->setJSON([
            'message' => "Nominal komponen tunjangan berhasil diperbaruhi",
            'location' => "nilaiKomponenGaji",
            'id' => $payrollID
        ]);
    }

    public function updateNominalKeterlambatanPresensi()
    {
        $id = $this->request->getVar('rekapKeterlambatanPresensiID');
        $payrollID = $this->request->getVar('payrollID');
        $nominal = (int) preg_replace("/[^0-9]/", "", $this->request->getVar('nominal'));

        $payrollModel = new PayrollsModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();

        $attendanceTerlambatModel->update($id, [
            'nominal_pengurangan' => $nominal,
        ]);

        $payrollModel->generateIfPayrollChanged($payrollID);

        return \response()->setJSON([
            'message' => "Nominal pengurangan keterlambatan presensi berhasil diperbaruhi",
            'location' => "rekapKeterlambatanPresensi",
            'id' => $payrollID
        ]);
    }

    public function updateNominalPerizinanNotApproved()
    {
        $id = $this->request->getVar('perizinanID');
        $payrollID = $this->request->getVar('payrollID');
        $nominal = (int) preg_replace("/[^0-9]/", "", $this->request->getVar('nominal'));

        $payrollModel = new PayrollsModel();
        $formPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();

        $formPerizinanNotApprovedModel->update($id, [
            'nominal_pengurangan' => $nominal,
        ]);

        $payrollModel->generateIfPayrollChanged($payrollID);

        return \response()->setJSON([
            'message' => "Nominal pengurangan dari perizinan tidak disetujui berhasil diperbaruhi",
            'location' => "rekapPerizinanTidakDisetujui",
            'id' => $payrollID
        ]);
    }

    public function updateNominalGajiPerHariAndCadangan()
    {
        $payrollID = $this->request->getVar('payrollID');
        $nominalGajiPerHari = (int) preg_replace("/[^0-9]/", "", $this->request->getVar('nominalGajiPerHari'));
        $nominalCadangan = (int) preg_replace("/[^0-9]/", "", $this->request->getVar('nominalCadangan'));
        $gajiPerHariID = $this->request->getVar('gajiPerHariID');
        $cadanganID = $this->request->getVar('cadanganID');

        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $payrollModel = new PayrollsModel();

        $payrollGajiModel->update($gajiPerHariID, [
            'nominal' => $nominalGajiPerHari
        ]);

        $payrollGajiModel->update($cadanganID, [
            'nominal' => $nominalCadangan
        ]);

        $payrollModel->generateIfPayrollChanged($payrollID);

        return \response()->setJSON([
            'message' => "Nominal Gaji Per Hari dan Nominal Cadangan Berhasil Diperbaruhi",
            'payrollDetail' => $payrollModel->detailPayroll($payrollID),
            'token' => \csrf_hash(),
            'test' => $_POST
        ]);
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
}
