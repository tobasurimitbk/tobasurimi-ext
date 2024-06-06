<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesLogModel;
use App\Models\AttendancesModel;
use App\Models\BigDaysModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\EmployeeJamKerjaModel;
use App\Models\EmployeesModel;
use App\Models\FormPerijinanModel;
use App\Models\GolonganModel;
use App\Models\JamKerjaModel;
use App\Models\MetadataModel;
use CodeIgniter\I18n\Time;
use DateTime;
use Dompdf\Dompdf;
use Locale;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Attendance extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $AttendancesLogModel;
    protected $EmployeesModel;
    protected $FormPerijinanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function LogAttendance()
    {
        // declare model
        $AttendancesLogModel = new AttendancesLogModel();
        $EmployeesModel = new EmployeesModel();
        $FormPerijinanModel = new FormPerijinanModel();
        $DivisiModel = new DivisisModel();
        $metaDataModel = new MetadataModel();
        $golonganModel = new GolonganModel();

        // get data $_GET
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        // get data from model
        $dataEmployeePager = $EmployeesModel->getEmployeesWithPagination(
            $this->this_company_id,
            $this->request->getGet('employeesID'),
            $this->request->getGet('divisiID'),
            $this->request->getGet('golongan')
        );
        $pager = \Config\Services::pager();
        $employeeDetailFilter = $EmployeesModel->where('id', $this->request->getGet('employeesID'))->first();

        // declare variable for store data
        $dataResult = array();

        // set data attendance
        foreach ($dataEmployeePager['data'] as $value) {
            // get log attendance by employee and $year-$month
            $dataLog = $AttendancesLogModel->getLogAmt($value["id"], $year, $month);
            // store data
            $dataResult[] = [
                "employeeID" => $value['id'],
                "divisi" => $value['divisi'],
                "namaBagian" => $value['nama_bagian'],
                "employeeName" => $value['name'],
                "list_attendance" => $dataLog,
                'statusAttendances' => [
                    'CT' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI TAHUNAN_CT", $year, $month),
                    'CHD' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI HAID_CHD", $year, $month),
                    'CHL' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI HAMIL_CHL", $year, $month),
                    'CM' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI MELAHIRKAN_CM", $year, $month),
                    'I' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "IJIN_I", $year, $month),
                    'S' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "SAKIT_S", $year, $month),
                    'RL' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "RL_RL", $year, $month),
                ]
            ];
        }

        // final data
        $data = [
            'year' => $year,
            'month' => $month,
            'divisi' => $DivisiModel->get_by_company_id($this->this_company_id),
            'res_user'  => $dataResult,
            'employeesData' => $dataEmployeePager['data'],
            'pager' => $dataEmployeePager['pager'],
            'employeeDetailFilter' => $employeeDetailFilter,
            'pager' => $dataEmployeePager['pager'],
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->whereNotIn('value', ['HADIR_H', 'LIBUR_L', 'ALPHA_A'])
                ->orderBy('name', "ASC")
                ->findAll(),
            'statusPerizinanAll' => $metaDataModel->where('name', "Status Perizinan")
                ->whereNotIn('value', ['LIBUR_L'])
                ->orderBy('name', "ASC")
                ->findAll(),
            'golongan' => $golonganModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),

        ];
        return view('hr/attendance/log-attendance', $data);
    }

    public function generateAttendanceView()
    {
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        // declare model
        $AttendanceModel = new AttendancesModel();
        $EmployeesModel = new EmployeesModel();
        $DivisiModel = new DivisisModel();
        $metaDataModel = new MetadataModel();
        $golonganModel = new GolonganModel();

        $startDate = date('d/m/Y', strtotime("{$year}-{$month}-01 -1 month +22 days"));
        $endDate = date('d/m/Y', strtotime("{$year}-{$month}-01  +20 days"));

        $startDates = date('Y-m-d', strtotime(str_replace('/', '-', $startDate)));
        $endDates = date('Y-m-d', strtotime(str_replace('/', '-', $endDate)));

        $startDateTimestamp = strtotime($startDates);
        $endDateTimestamp = strtotime($endDates);

        $allDates = array();
        while ($startDateTimestamp <= $endDateTimestamp) {
            $currentDate = date('Y-m-d', $startDateTimestamp);
            $allDates[] = $currentDate;
            $startDateTimestamp += 86400;
        }

        // Menghitung $startMonth dan $endMonth
        $resStartEndMonth = static::getTotalDatesAndGroubMonth($allDates);

        // get attendance Total (ngecek apakah sudah digenerate belum)
        $totalAttendances = $AttendanceModel->where('year_month', $year . "-" . $month)
            ->join('employees', 'employees.id = attendances.employee_id')
            ->where('employees.deletedAt', null)
            ->where('attendances.company_id', $this->this_company_id)
            ->countAllResults();

        $dataEmployeePager = $EmployeesModel->getEmployeesWithPagination(
            $this->this_company_id,
            $this->request->getGet('employeesID'),
            $this->request->getGet('divisiID'),
            $this->request->getGet('golongan')
        );
        $pager = \Config\Services::pager();
        $employeeDetailFilter = $EmployeesModel->where('id', $this->request->getGet('employeesID'))->first();

        // Data Send To View
        $data = [
            'year' => $year,
            'month' => $month,
            'totalAttendances' => $totalAttendances,
            'employeesData' => $dataEmployeePager['data'],
            'pager' => $dataEmployeePager['pager'],
            'employeeDetailFilter' => $employeeDetailFilter,
            'divisi' => $DivisiModel->get_by_company_id($this->this_company_id),
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->orderBy('name', "ASC")
                ->findAll(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'allDates' => $allDates,
            'startMonth' => $resStartEndMonth[0],
            'endMonth' => $resStartEndMonth[1],
            'golongan' => $golonganModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
        ];

        $data['pager'] = $pager;

        return view('hr/attendance/attendance-generate', $data);
    }

    public function generateAttendanceGlobalAction()
    {
        // declare variable
        $monthYear = explode('-', $this->request->getVar('monthYear'));
        $month = $monthYear[1];
        $year = $monthYear[0];
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
        $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));

        // declare model
        $EmployeesModel = new EmployeesModel();
        $AttendanceModel = new AttendancesModel();

        $employeeData = $EmployeesModel->getEmployees($this->this_company_id);

        if (count($employeeData) == 0) {
            return $this->response->setJSON([
                'message' => "Employee tidak ditemukan di company ini",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // remove all if exist and insert again
        $AttendanceModel->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->where('company_id', $this->this_company_id)
            ->delete();

        $res = $AttendanceModel->generate(
            $employeeData,
            $startDate,
            $endDate,
            $year,
            $month,
            $this->this_company_id
        );

        if ($res['status']) {
            return $this->response->setJSON([
                'message' => "Attendance seluruh karyawan berhasil digenerate",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'message' => $res['message'],
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function generateAttendancePersonalAction()
    {
        $monthYear = explode('-', $this->request->getVar('monthYear'));
        $month = $monthYear[1];
        $year = $monthYear[0];
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
        $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));
        $employeeID = $this->request->getVar('employeeID');

        // declare model
        $EmployeesModel = new EmployeesModel();
        $AttendanceModel = new AttendancesModel();

        $employeeData = $EmployeesModel->where('id', $employeeID)->findAll();
        $employeeStatus = $EmployeesModel->where('id', $employeeID)->first();

        if ($employeeStatus['status'] != "Aktif") {
            return $this->response->setJSON([
                'message' => "Status karyawan " . $employeeStatus['name'] . " adalah " . $employeeStatus['status'],
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $AttendanceModel->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->where('company_id', $this->this_company_id)
            ->where('employee_id', $employeeID)
            ->delete();

        if (count($employeeData) == 0) {
            return $this->response->setJSON([
                'message' => "Employee tidak ditemukan di company ini",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $res = $AttendanceModel->generate(
            $employeeData,
            $startDate,
            $endDate,
            $year,
            $month,
            $this->this_company_id
        );

        if ($res['status']) {
            return $this->response->setJSON([
                'message' => "Attendance personal karyawan berhasil digenerate",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'message' => $res['message'],
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function getDetailAttendance()
    {
        $employeeID = $this->request->getVar('employeeID');
        $tanggal = $this->request->getVar('tanggal');
        $time = Time::createFromFormat('Y-m-d', $tanggal);

        $AttendanceModel = new AttendancesModel();
        $EmployeesModel = new EmployeesModel();
        $bigDaysModel = new BigDaysModel();
        $employeeJamKerjaModel = new EmployeeJamKerjaModel();

        Locale::setDefault('id_ID');
        $attendanceDetail = $AttendanceModel->where('periode', $tanggal)
            ->where('employee_id', $employeeID)
            ->first();

        $resultData = [
            'tanggal' => static::getDayIndonesia($time->format('l')) . ", " . $time->format('d F Y'),
            'attendance' => $attendanceDetail,
            'employee' => $EmployeesModel->where('id', $employeeID)->first(),
            'keterangan' => "-",
            'jamTerlambat' => "-"
        ];

        $keterangan = static::keterlambatanCheck(
            $tanggal,
            $attendanceDetail['checkin'],
            $employeeID
        );

        $resultData['keterangan'] = $keterangan[0];
        $resultData['jamTerlambat'] = $keterangan[1];

        if ($attendanceDetail != null) {
            if ($attendanceDetail['status'] == "LIBUR_L") {
                // cek apakah big days
                $bigDays = $bigDaysModel->where('date', $attendanceDetail['periode'])
                    ->where('company_id', $this->this_company_id)
                    ->first();
                if ($bigDays != null) {
                    $resultData['keterangan'] = $bigDays['name'];
                }
            }
        }

        // GET JAM KERJA USED
        $jamKerja = $employeeJamKerjaModel->getJamKerjaUsedByEmployeeId($tanggal, $employeeID);
        // APPEND TO RESULT
        $resultData['jamKerja'] = $jamKerja;

        return $this->response->setJSON([
            'data' => $resultData,
        ]);
    }

    public function updateAttendance()
    {
        $attendenceID = $this->request->getVar('attendenceID');
        $checkIN = $this->request->getVar('checkIn');
        $checkOut = $this->request->getVar('checkOut');
        $statusKehadiran = $this->request->getVar('statusKehadiran');
        $reason = $this->request->getVar('reason');
        $isApproved = $this->request->getVar('isApproved');

        $AttendanceModel = new AttendancesModel();

        $AttendanceModel->update($attendenceID, [
            'checkin' => $checkIN, // in
            'checkout' => $checkOut, // out
            'status' => $statusKehadiran,
            'reason' => $reason,
            'isApproved' => $isApproved
        ]);

        return $this->response->setJSON([
            'message' => "Attendence diperbaruhi"
        ]);
    }

    public function getEmployeesLike()
    {
        $employeesName = $this->request->getVar('employeesName');
        $divisionID = $this->request->getVar('divisiID');

        $employessModel = new EmployeesModel();

        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $this->this_company_id,
            //'users.id' => null
        ];

        if ($divisionID != "") {
            $arrCondition['division_id'] = $divisionID;
        }

        $result = $employessModel->select("employees.name, employees.id")
            ->join('users', 'users.employee_id = employees.id', 'left')
            ->groupStart()
            ->where($arrCondition)
            ->like('employees.name', $employeesName)
            ->groupEnd()
            ->findAll();

        return \response()->setJSON([
            'data' => $result
        ]);
    }

    public function getLogAttendanceDetail()
    {
        // model declare
        $AttendancesLogModel = new AttendancesLogModel();
        $FormPerijinanModel = new FormPerijinanModel();
        $hariLiburModel = new BigDaysModel();
        $EmployeesModel = new EmployeesModel();
        $employeeJamKerjaModel = new EmployeeJamKerjaModel();

        Locale::setDefault('id_ID');

        $tanggal = $this->request->getVar('tanggal');
        $employeeID = $this->request->getVar('employeeID');
        $time = Time::createFromFormat('Y-m-d', $tanggal);

        $result = [
            'tanggal' => static::getDayIndonesia($time->format('l')) . ", " . $time->format('d F Y'),
            'keterangan' => "-",
            'checkIn' => "-",
            'checkOut' => "-",
            'status' => "-",
            'employee' => $EmployeesModel->where('id', $employeeID)->first(),
            'jamTerlambat' => "-"
        ];

        $formPerizinan = $FormPerijinanModel->where('periode', $tanggal)
            ->where('employee_id', $employeeID)
            ->first();
        $hariLibur = $hariLiburModel->where('date', $tanggal)
            ->first();

        $selectQry = "DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
        DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout";

        $logAttandance = $AttendancesLogModel
            ->select($selectQry)
            ->where('employees_id', $employeeID)
            ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $tanggal)
            ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
            ->limit(2)
            ->get()
            ->getResult();

        if ($hariLibur != null || date('l', strtotime($tanggal)) == "Sunday" && \count($logAttandance) == 0 && $formPerizinan == null) {
            $result['status'] = "Hari Libur";
            $result['keterangan'] = $hariLibur != null ? $hariLibur['name'] : "Hari Minggu";
        } elseif ($formPerizinan != null) {
            $result['status'] = explode("_", $formPerizinan['status'])[0];
            $result['keterangan'] = "-";
        } else {
            // tidak ada data di log absen
            if (count($logAttandance) == 0) {
                $result['status'] = "ALPHA";
            } else {
                // ada absen di log
                $result['status'] = "Hadir";
                if ($logAttandance[0]->checkout != $logAttandance[0]->checkin) {

                    $result['checkIn'] = \date('H:i:s', \strtotime($logAttandance[0]->checkin));
                    $result['checkOut'] = \date('H:i:s', \strtotime($logAttandance[0]->checkout));

                    // ada attandance (in dan out)
                    $keterangan = static::keterlambatanCheck(
                        $tanggal,
                        $result['checkIn'],
                        $employeeID
                    );

                    $result['keterangan'] = $keterangan[0];
                    $result['jamTerlambat'] = $keterangan[1];
                } else {
                    // ada attandance only(in)
                    $result['checkIn'] = \date('H:i:s', \strtotime($logAttandance[0]->checkin));
                    // $result['checkOut'] = \date('H:i:s', \strtotime($logAttandance[0]->checkout));
                    $keterangan = static::keterlambatanCheck(
                        $tanggal,
                        $result['checkIn'],
                        $employeeID
                    );
                    $result['keterangan'] = $keterangan[0];
                    $result['jamTerlambat'] = $keterangan[1];
                }
            }
        }

        // GET JAM KERJA USED
        $jamKerja = $employeeJamKerjaModel->getJamKerjaUsedByEmployeeId($tanggal, $employeeID);
        // APPEND TO RESULT
        $result['jamKerja'] = $jamKerja;

        return \response()->setJSON([
            'status' => true,
            'data' => $result,
            'token' => \csrf_hash(),
        ]);
    }

    public function exportPDFLogPresensi($yearMonth)
    {
        $divisiID = $this->request->getGet('divisiID');
        $golongan = $this->request->getGet('golongan');

        $employeesModel = new EmployeesModel();
        $divisiModel = new DivisisModel();
        $companyModel = new CompaniesModel();
        $metaDataModel = new MetadataModel();
        $AttendancesLogModel = new AttendancesLogModel();
        $FormPerijinanModel = new FormPerijinanModel();
        $divisiModel = new DivisisModel();
        $golonganModel = new GolonganModel();

        $dompdf = new Dompdf();

        if (!empty($divisiID) || !empty($golongan)) {
            $employeeData = $employeesModel->getEmployeesByDivisionID(
                $this->this_company_id,
                $divisiID,
                $golongan
            );
        } else {
            $employeeData = $employeesModel->getEmployees($this->this_company_id);
        }

        $splitYearMonth = \explode("-", $yearMonth);

        // declare variable for store data
        $dataResult = array();

        // set data attendance
        foreach ($employeeData as $value) {
            // get log attendance by employee and $year-$month
            $dataLog = $AttendancesLogModel->getLogAmt($value["id"], $splitYearMonth[0], $splitYearMonth[1]);
            // store data
            $dataResult[] = [
                "employeeID" => $value['id'],
                "employeeName" => $value['name'],
                "list_attendance" => $dataLog,
                "namaBagian" => $value['nama_bagian'],
                "divisi" => $divisiModel->where('id', $value['division_id'])->first()['divisi'],
                'statusAttendances' => [
                    'CT' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI TAHUNAN_CT", $splitYearMonth[0], $splitYearMonth[1]),
                    'CHD' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI HAID_CHD", $splitYearMonth[0], $splitYearMonth[1]),
                    'CHL' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI HAMIL_CHL", $splitYearMonth[0], $splitYearMonth[1]),
                    'CM' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI MELAHIRKAN_CM", $splitYearMonth[0], $splitYearMonth[1]),
                    'I' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "IJIN_I", $splitYearMonth[0], $splitYearMonth[1]),
                    'S' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "SAKIT_S", $splitYearMonth[0], $splitYearMonth[1]),
                    'RL' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "RL_RL", $splitYearMonth[0], $splitYearMonth[1]),
                ]
            ];
        }

        $data = [
            'res_user'  => $dataResult,
            'yearMonth' => $yearMonth,
            'divisi' => $divisiModel->where('id', $divisiID)->first(),
            'golongan' => $golonganModel->where('golongan_name', $golongan)->first(),
            'company' => $companyModel->where('id', $this->this_company_id)->first(),
            'month' => $splitYearMonth[1],
            'year' => $splitYearMonth[0],
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->whereNotIn('value', ['HADIR_H', 'LIBUR_L', 'ALPHA_A'])
                ->orderBy('name', "ASC")
                ->findAll(),
            'statusPerizinanAll' => $metaDataModel->where('name', "Status Perizinan")
                ->whereNotIn('value', ['LIBUR_L'])
                ->orderBy('name', "ASC")
                ->findAll(),
        ];

        $dompdf->loadHtml(view('hr/attendance/print-log', $data));
        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();
        $dompdf->stream("Log Absensi $yearMonth", array("Attachment" => false));

        exit(0);
    }

    public function exportExcelLogPresensi($yearMonth)
    {
        $divisiID = $this->request->getGet('divisiID');
        $golongan = $this->request->getGet('golongan');

        $employeesModel = new EmployeesModel();
        $divisiModel = new DivisisModel();
        $companyModel = new CompaniesModel();
        $metaDataModel = new MetadataModel();
        $AttendancesLogModel = new AttendancesLogModel();
        $FormPerijinanModel = new FormPerijinanModel();
        $divisiModel = new DivisisModel();
        $golonganModel = new GolonganModel();

        if (!empty($divisiID) || !empty($golongan)) {
            $employeeData = $employeesModel->getEmployeesByDivisionID(
                $this->this_company_id,
                $divisiID,
                $golongan
            );
        } else {
            $employeeData = $employeesModel->getEmployees($this->this_company_id);
        }

        $splitYearMonth = \explode("-", $yearMonth);

        // declare variable for store data
        $dataResult = array();

        // set data attendance
        foreach ($employeeData as $value) {
            // get log attendance by employee and $year-$month
            $dataLog = $AttendancesLogModel->getLogAmt($value["id"], $splitYearMonth[0], $splitYearMonth[1]);
            // store data
            $dataResult[] = [
                "employeeID" => $value['id'],
                "employeeName" => $value['name'],
                "namaBagian" => $value["nama_bagian"],
                "divisi" => $divisiModel->where('id', $value['division_id'])->first()['divisi'],
                "list_attendance" => $dataLog,
                "divisi" => $divisiModel->where('id', $value['division_id'])->first()['divisi'],
                'statusAttendances' => [
                    'CT' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI TAHUNAN_CT", $splitYearMonth[0], $splitYearMonth[1]),
                    'CHD' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI HAID_CHD", $splitYearMonth[0], $splitYearMonth[1]),
                    'CHL' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI HAMIL_CHL", $splitYearMonth[0], $splitYearMonth[1]),
                    'CM' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI MELAHIRKAN_CM", $splitYearMonth[0], $splitYearMonth[1]),
                    'I' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "IJIN_I", $splitYearMonth[0], $splitYearMonth[1]),
                    'S' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "SAKIT_S", $splitYearMonth[0], $splitYearMonth[1]),
                    'RL' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "RL_RL", $splitYearMonth[0], $splitYearMonth[1]),
                ]
            ];
        }

        $data = [
            'res_user'  => $dataResult,
            'yearMonth' => $yearMonth,
            'divisi' => $divisiModel->where('id', $divisiID)->first(),
            'golongan' => $golonganModel->where('golongan_name', $golongan)->first(),
            'company' => $companyModel->where('id', $this->this_company_id)->first(),
            'month' => $splitYearMonth[1],
            'year' => $splitYearMonth[0],
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->whereNotIn('value', ['HADIR_H', 'LIBUR_L', 'ALPHA_A'])
                ->orderBy('name', "ASC")
                ->findAll(),
            'statusPerizinanAll' => $metaDataModel->where('name', "Status Perizinan")
                ->whereNotIn('value', ['LIBUR_L'])
                ->orderBy('name', "ASC")
                ->findAll(),
        ];


        return view('hr/attendance/excel-log', $data);
    }

    public function exportPDFPresensi($yearMonth)
    {
        $divisiID = $this->request->getGet('divisiID');
        $golongan = $this->request->getGet('golongan');

        $dompdf = new Dompdf();

        $employeesModel = new EmployeesModel();
        $metaDataModel = new MetadataModel();
        $companyModel = new CompaniesModel();
        $divisiModel = new DivisisModel();
        $golonganModel = new GolonganModel();

        if (!empty($divisiID) || !empty($golongan)) {
            $employeeData = $employeesModel->getEmployeesByDivisionID(
                $this->this_company_id,
                $divisiID,
                $golongan
            );
        } else {
            $employeeData = $employeesModel->getEmployeesAndDivisi($this->this_company_id);
        }

        $splitYearMonth = explode("-", $yearMonth);

        $year = $splitYearMonth[0];
        $month = $splitYearMonth[1];

        $startDate = date('d/m/Y', strtotime("{$year}-{$month}-01 -1 month +22 days"));
        $endDate = date('d/m/Y', strtotime("{$year}-{$month}-01  +20 days"));

        $startDates = date('Y-m-d', strtotime(str_replace('/', '-', $startDate)));
        $endDates = date('Y-m-d', strtotime(str_replace('/', '-', $endDate)));

        $startDateTimestamp = strtotime($startDates);
        $endDateTimestamp = strtotime($endDates);

        $allDates = array();
        while ($startDateTimestamp <= $endDateTimestamp) {
            $currentDate = date('Y-m-d', $startDateTimestamp);
            $allDates[] = $currentDate;
            $startDateTimestamp += 86400;
        }

        // Menghitung $startMonth dan $endMonth
        $resStartEndMonth = static::getTotalDatesAndGroubMonth($allDates);

        $data = [
            'yearMonth' => $yearMonth,
            'company' => $companyModel->where('id', $this->this_company_id)->first(),
            'divisi' => $divisiModel->where('id', $divisiID)->first(),
            'year' => $year,
            'month' => $month,
            'employeesData' => $employeeData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'allDates' => $allDates,
            'startMonth' => $resStartEndMonth[0],
            'endMonth' => $resStartEndMonth[1],
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->orderBy('name', "ASC")
                ->findAll(),
            'golongan' => $golonganModel->where('golongan_name', $golongan)->first(),
        ];

        $dompdf->loadHtml(view('hr/attendance/print-attendance', $data));
        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();
        $dompdf->stream("Data Absensi Final $yearMonth", array("Attachment" => false));

        exit(0);
    }

    public function exportExcelPresensi($yearMonth)
    {
        $divisiID = $this->request->getGet('divisiID');
        $golongan = $this->request->getGet('golongan');

        $employeesModel = new EmployeesModel();
        $metaDataModel = new MetadataModel();
        $companyModel = new CompaniesModel();
        $divisiModel = new DivisisModel();

        if (!empty($divisiID) || !empty($golongan)) {
            $employeeData = $employeesModel->getEmployeesByDivisionID($this->this_company_id, $divisiID, $golongan);
        } else {
            $employeeData = $employeesModel->getEmployeesAndDivisi($this->this_company_id);
        }

        $splitYearMonth = explode("-", $yearMonth);

        $year = $splitYearMonth[0];
        $month = $splitYearMonth[1];

        $startDate = date('d/m/Y', strtotime("{$year}-{$month}-01 -1 month +22 days"));
        $endDate = date('d/m/Y', strtotime("{$year}-{$month}-01  +20 days"));

        $startDates = date('Y-m-d', strtotime(str_replace('/', '-', $startDate)));
        $endDates = date('Y-m-d', strtotime(str_replace('/', '-', $endDate)));

        $startDateTimestamp = strtotime($startDates);
        $endDateTimestamp = strtotime($endDates);

        $allDates = array();
        while ($startDateTimestamp <= $endDateTimestamp) {
            $currentDate = date('Y-m-d', $startDateTimestamp);
            $allDates[] = $currentDate;
            $startDateTimestamp += 86400;
        }

        $resStartEndMonth = static::getTotalDatesAndGroubMonth($allDates);

        $data = [
            'yearMonth' => $yearMonth,
            'company' => $companyModel->where('id', $this->this_company_id)->first(),
            'divisi' => $divisiModel->where('id', $divisiID)->first(),
            'employeesData' => $employeeData,
            'month' => $month,
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'allDates' => $allDates,
            'startMonth' => $resStartEndMonth[0],
            'endMonth' => $resStartEndMonth[1],
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->orderBy('name', "ASC")
                ->findAll(),
        ];

        return \view('hr/attendance/excel-attendance', $data);
    }

    public function exportTriwulanAbsensi($startMonth, $endMonth, $divisionID)
    {
        $startDateTime = DateTime::createFromFormat('Y-m', $startMonth);
        $endDateTime = DateTime::createFromFormat('Y-m', $endMonth);

        $companyModel = new CompaniesModel();
        $divisiModel = new DivisisModel();
        $attendanceModel = new AttendancesModel();

        if ($startDateTime > $endDateTime) {
            return redirect()->back()->with('error', 'Selesai Bulan tidak boleh lebih kecil dari mulai Bulan.');
        } elseif ($startDateTime->diff($endDateTime)->m != 2) {
            return redirect()->back()->with('error', 'Rentang mulai Bulan dan selesai Bulan adalah 3 bulan.');
        } else {

            $middleMonthDt = clone $startDateTime;
            $middleMonthDt->modify('+1 month');
            $middleMonth = $middleMonthDt->format('Y-m');

            $data = [
                'middleMonth' => $middleMonth,
                'startMonth' => $startMonth,
                'endMonth' => $endMonth,
                'unit' => $companyModel->where('id', $this->this_company_id)->first(),
                'divisi' => $divisiModel->where('id', $divisionID)->first(),
                'data' => $attendanceModel->triwulanPDF([$startMonth, $middleMonth, $endMonth], $divisionID, $this->this_company_id)
            ];

            $dompdf = new Dompdf();

            $dompdf->loadHtml(view('hr/attendance/triwulan-attendance', $data));
            $dompdf->setPaper('legal', 'portrait');
            $dompdf->render();
            $dompdf->stream("Data Absensi Karyawan", array("Attachment" => false));

            exit(0);
        }
    }

    // helper
    static function keterlambatanCheck($date, $checkIN, $employeeID)
    {
        $employeeJamKerjaModel = new EmployeeJamKerjaModel();
        $result = "-";

        $jamKerjaDetail = $employeeJamKerjaModel->getJamKerjaUsedByEmployeeId($date, $employeeID);

        if ($jamKerjaDetail !== null && $checkIN != null) {
            $checkInTimestamp = strtotime($checkIN);
            $jamTerlambatTimestamp = strtotime($jamKerjaDetail['jam_terlambat']);

            if ($checkInTimestamp > $jamTerlambatTimestamp) {
                $result  = "Terlambat";
            } else {
                $result = "Tepat Waktu";
            }
        }

        return [$result, ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_terlambat'] : "-"];
    }

    static function getDayIndonesia($day)
    {
        $translations = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];

        return isset($translations[$day]) ? $translations[$day] : $day;
    }

    static function getTotalDatesAndGroubMonth($allDates)
    {
        $startMonth = [
            'firstMonthName' => date('F', strtotime($allDates[0])),
            'totalDay' => 0
        ];

        $endMonth = [
            'secondMonthName' => date('F', strtotime(end($allDates))),
            'totalDay' => 0
        ];

        foreach ($allDates as $date) {
            $month = date('F', strtotime($date));
            if ($month === $startMonth['firstMonthName']) {
                $startMonth['totalDay']++;
            }
            if ($month === $endMonth['secondMonthName']) {
                $endMonth['totalDay']++;
            }
        }

        return [
            $startMonth, $endMonth
        ];
    }
}
