<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesLogModel;
use App\Models\AttendancesModel;
use App\Models\BigDaysModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\EmployeesModel;
use App\Models\FormPerijinanModel;
use App\Models\JamKerjaModel;
use App\Models\MetadataModel;
use App\Models\PayrollsModel;
use CodeIgniter\I18n\Time;
use Dompdf\Dompdf;
use Locale;

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

        // get data $_GET
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        // get data from model
        $dataEmployeePager = $EmployeesModel->getEmployeesWithPagination($this->this_company_id, $this->request->getGet('employeesID'), $this->request->getGet('divisiID'));
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
        $payrollModel = new PayrollsModel();
        $DivisiModel = new DivisisModel();
        $metaDataModel = new MetadataModel();

        // get attendance Total (ngecek apakah sudah digenerate belum)
        $totalAttendances = $AttendanceModel->where('LEFT(periode, 7)', $year . "-" . $month)
            ->where('company_id', $this->this_company_id)
            ->countAllResults();
        // check is posting
        $isPosting = $AttendanceModel->where('LEFT(periode, 7)', $year . "-" . $month)
            ->where('company_id', $this->this_company_id)
            ->where('isPosting', 1)
            ->countAllResults();

        $isPostingPayroll = $payrollModel->where('company_id', $this->this_company_id)
            ->where('year_month', $year . "-" . $month)
            ->where('isPosted', 1)
            ->countAllResults();

        $dataEmployeePager = $EmployeesModel->getEmployeesWithPagination($this->this_company_id, $this->request->getGet('employeesID'), $this->request->getGet('divisiID'));
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
            'isPosting' =>  $isPosting,
            'isPostingPayroll' => $isPostingPayroll,
            'divisi' => $DivisiModel->get_by_company_id($this->this_company_id),
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->orderBy('name', "ASC")
                ->findAll(),
        ];

        $data['pager'] = $pager;

        return \view('hr/attendance/attendance-generate', $data);
    }

    public function generateAttendanceAction()
    {
        // declare variable
        $month = $this->request->getVar('month');
        $year = $this->request->getVar('year');
        // declare model
        $AttendancesLogModel = new AttendancesLogModel();
        $EmployeesModel = new EmployeesModel();
        $AttendanceModel = new AttendancesModel();
        $FormPerijinanModel = new FormPerijinanModel();
        $hariLiburModel = new BigDaysModel();

        $employeeData = $EmployeesModel->getEmployees($this->this_company_id);
        // check employee
        if (count($employeeData) == 0) {
            return $this->response->setJSON([
                'message' => "Employee tidak ditemukan di company ini",
                'code' => 422
            ]);
        }

        // remove all if exist and insert again
        $AttendanceModel->where([
            'MONTH(periode)' => $month,
            'YEAR(periode)' => $year,
            'company_id' => $this->this_company_id
        ])->delete();

        // generate yyyy-mm-dd per tahun-bulan
        // Menghitung jumlah hari dalam bulan yang diberikan
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        // Membuat array untuk menyimpan semua tanggal
        $allDates = array();
        // Menghasilkan semua tanggal dalam bulan dan tahun yang diberikan
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $allDates[] = $date;
        }

        // loop employee
        foreach ($employeeData as $e) {
            // loop date
            foreach ($allDates as $dates) {
                // chek apakah data izin
                $formPerizinan = $FormPerijinanModel->where('periode', $dates)
                    ->where('employee_id', $e['id'])
                    ->first();
                // check adakah data 
                $hariLibur = $hariLiburModel->where('date', $dates)->first();
                $selectQry = "DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
                DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout";

                $logAttandance = $AttendancesLogModel
                    ->select($selectQry)
                    ->where('employees_id', $e['id'])
                    ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $dates)
                    ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
                    ->limit(2)
                    ->get()
                    ->getResult();

                if ($hariLibur != null || date('l', strtotime($dates)) == "Sunday" && $formPerizinan == null && \count($logAttandance) == 0) {
                    // ada hari libur
                    $AttendanceModel->insert([
                        'company_id' => $this->this_company_id,
                        'employee_id' => $e['id'],
                        'periode' => $dates,
                        'status' => "LIBUR_L",
                        'reason' => ''
                    ]);
                } elseif ($formPerizinan != null) {
                    // ada perizinan 
                    $AttendanceModel->insert([
                        'company_id' => $this->this_company_id,
                        'employee_id' => $e['id'],
                        'periode' => $dates,
                        'status' => $formPerizinan['status'],
                        'reason' => $formPerizinan['reason'],
                        'isApproved' => $formPerizinan['is_approval']
                    ]);
                } elseif ($formPerizinan == null) {
                    // tidak ada data perizinan jadi
                    // get attendance by date and employee by log

                    if (\count($logAttandance) == 0) {
                        // rekap absen tidak ditemukan
                        // set jadi ALPHA
                        $AttendanceModel->insert([
                            'company_id' => $this->this_company_id,
                            'employee_id' => $e['id'],
                            'periode' => $dates,
                            'status' => 'ALPHA_A',
                        ]);
                    } else {
                        // data absen ada di log
                        if ($logAttandance[0]->checkout != $logAttandance[0]->checkin) {
                            // ada attandance (in dan out)
                            // create in
                            $AttendanceModel->insert([
                                'company_id' => $this->this_company_id,
                                'employee_id' => $e['id'],
                                'periode' => $dates,
                                'checkin' => \date('H:i:s', \strtotime($logAttandance[0]->checkin)), // in
                                'checkout' => \date('H:i:s', \strtotime($logAttandance[0]->checkout)), // out
                                'status' => 'HADIR_H'
                            ]);
                        } else {
                            // ada attandance only(in)
                            $AttendanceModel->insert([
                                'company_id' => $this->this_company_id,
                                'employee_id' => $e['id'],
                                'periode' => $dates,
                                'checkin' => \date('H:i:s', \strtotime($logAttandance[0]->checkin)), // in
                                'checkout' => \date('H:i:s', \strtotime($logAttandance[0]->checkout)), // out
                                'status' => 'HADIR_H',
                            ]);
                        }
                    }
                }
            }
        }

        return $this->response->setJSON([
            'message' => "Attendance berhasil digenerate",
            'code' => 200
        ]);
    }

    public function getDetailAttendance()
    {
        $employeeID = $this->request->getVar('employeeID');
        $tanggal = $this->request->getVar('tanggal');
        $time = Time::createFromFormat('Y-m-d', $tanggal);

        $AttendanceModel = new AttendancesModel();
        $EmployeesModel = new EmployeesModel();

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

        $keterangan = static::ketelambatanCheck(
            $this->this_company_id,
            $attendanceDetail['checkin']
        );

        $resultData['keterangan'] = $keterangan[0];
        $resultData['jamTerlambat'] = $keterangan[1];

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

    public function updatePostAttendance()
    {
        $year = $this->request->getVar('year');
        $month = $this->request->getVar('month');
        $status = $this->request->getVar('statusPosting');

        $query = "UPDATE attendances SET isPosting = ? WHERE DATE_FORMAT(periode, '%Y-%m') = ? AND company_id = ?";
        $params = [$status, "$year-$month", "$this->this_company_id"];

        $db = \Config\Database::connect();
        $db->query($query, $params);

        return $this->response->setJSON([
            'message' => "Status posting presensi diperbaruhi"
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
            'users.id' => null
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
                    // ada attandance (in dan out)
                    $keterangan = static::ketelambatanCheck(
                        $this->this_company_id,
                        $result['checkIn']
                    );
                    $result['checkIn'] = \date('H:i:s', \strtotime($logAttandance[0]->checkin));
                    $result['checkOut'] = \date('H:i:s', \strtotime($logAttandance[0]->checkout));

                    $result['keterangan'] = $keterangan[0];
                    $result['jamTerlambat'] = $keterangan[1];
                } else {
                    // ada attandance only(in)
                    $result['checkIn'] = \date('H:i:s', \strtotime($logAttandance[0]->checkin));
                    // $result['checkOut'] = \date('H:i:s', \strtotime($logAttandance[0]->checkout));
                    $keterangan = static::ketelambatanCheck(
                        $this->this_company_id,
                        $result['checkIn']
                    );
                    $result['keterangan'] = $keterangan[0];
                    $result['jamTerlambat'] = $keterangan[1];
                }
            }
        }

        return \response()->setJSON([
            'status' => true,
            'data' => $result,
            'token' => \csrf_hash(),
        ]);
    }

    public function exportPDFLogPresensi($yearMonth)
    {
        $divisiID = $this->request->getGet('divisiID');

        $employeesModel = new EmployeesModel();
        $divisiModel = new DivisisModel();
        $companyModel = new CompaniesModel();
        $metaDataModel = new MetadataModel();
        $AttendancesLogModel = new AttendancesLogModel();
        $FormPerijinanModel = new FormPerijinanModel();
        $divisiModel = new DivisisModel();

        $dompdf = new Dompdf();

        if (!empty($divisiID)) {
            $employeeData = $employeesModel->getEmployeesByDivisionID($this->this_company_id, $divisiID);
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

        $employeesModel = new EmployeesModel();
        $divisiModel = new DivisisModel();
        $companyModel = new CompaniesModel();
        $metaDataModel = new MetadataModel();
        $AttendancesLogModel = new AttendancesLogModel();
        $FormPerijinanModel = new FormPerijinanModel();
        $divisiModel = new DivisisModel();

        if (!empty($divisiID)) {
            $employeeData = $employeesModel->getEmployeesByDivisionID($this->this_company_id, $divisiID);
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

        $dompdf = new Dompdf();

        $employeesModel = new EmployeesModel();
        $metaDataModel = new MetadataModel();
        $companyModel = new CompaniesModel();
        $divisiModel = new DivisisModel();

        if (!empty($divisiID)) {
            $employeeData = $employeesModel->getEmployeesByDivisionID($this->this_company_id, $divisiID);
        } else {
            $employeeData = $employeesModel->getEmployeesAndDivisi($this->this_company_id);
        }

        $splitYearMonth = \explode("-", $yearMonth);

        $data = [
            'yearMonth' => $yearMonth,
            'company' => $companyModel->where('id', $this->this_company_id)->first(),
            'divisi' => $divisiModel->where('id', $divisiID)->first(),
            'employeesData' => $employeeData,
            'month' => $splitYearMonth[1],
            'year' => $splitYearMonth[0],
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->orderBy('name', "ASC")
                ->findAll(),
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

        $employeesModel = new EmployeesModel();
        $metaDataModel = new MetadataModel();
        $companyModel = new CompaniesModel();
        $divisiModel = new DivisisModel();

        if (!empty($divisiID)) {
            $employeeData = $employeesModel->getEmployeesByDivisionID($this->this_company_id, $divisiID);
        } else {
            $employeeData = $employeesModel->getEmployeesAndDivisi($this->this_company_id);
        }

        $splitYearMonth = \explode("-", $yearMonth);

        $data = [
            'yearMonth' => $yearMonth,
            'company' => $companyModel->where('id', $this->this_company_id)->first(),
            'divisi' => $divisiModel->where('id', $divisiID)->first(),
            'employeesData' => $employeeData,
            'month' => $splitYearMonth[1],
            'year' => $splitYearMonth[0],
            'statusPerizinan' => $metaDataModel->where('name', "Status Perizinan")
                ->orderBy('name', "ASC")
                ->findAll(),
        ];

        return \view('hr/attendance/excel-attendance', $data);
    }

    // helper
    static function ketelambatanCheck($companyID, $checkIN)
    {
        $jamKerjaModel = new JamKerjaModel();
        $result = "-";

        $jamKerjaDetail = $jamKerjaModel->where('company_id', $companyID)->first();

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
}
