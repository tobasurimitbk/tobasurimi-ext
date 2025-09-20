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
use App\Models\MetadataModel;
use CodeIgniter\I18n\Time;
use DateTime;
use Dompdf\Dompdf;
use Locale;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Attendance extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $AttendancesLogModel;
    protected $EmployeesModel;
    protected $FormPerijinanModel;
    protected $DivisiModel;
    protected $MetadataModel;
    protected $GolonganModel;
    protected $BigDaysModel;
    protected $EmployeeJamKerjaModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->AttendancesLogModel = new AttendancesLogModel();
        $this->DivisiModel = new DivisisModel();
        $this->MetadataModel = new MetadataModel();
        $this->GolonganModel = new GolonganModel();
        $this->EmployeesModel = new EmployeesModel();
        $this->FormPerijinanModel = new FormPerijinanModel();
        $this->BigDaysModel = new BigDaysModel();
        $this->EmployeeJamKerjaModel = new EmployeeJamKerjaModel();
    }

    public function indexLog()
    {
        $dataDivisi = $this->DivisiModel->getDivisiAccess();
        $dataStatusPerizinanAll = $this->MetadataModel
            ->where('name', "Status Perizinan")
            ->whereNotIn('value', ['LIBUR_L'])
            ->orderBy('name', "ASC")
            ->findAll();
        $dataGolongan = $this->GolonganModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        $data = [
            'divisi' => $dataDivisi,
            'statusPerizinanAll' => $dataStatusPerizinanAll,
            'golongan' => $dataGolongan

        ];
        return view('hr/attendance/log-attendance', $data);
    }


    public function allLog()
    {
        $monthReq = $this->request->getVar('month');
        if (empty($monthReq)) {
            $monthReq = date('Y-m');
        }

        $monthSplit = explode('-', $monthReq);
        $year = $monthSplit[0];
        $month = $monthSplit[1];

        $length = empty($this->request->getGet("length")) ? 25 : $this->request->getGet("length");
        $limit = empty($this->request->getVar('length')) ? 25 : $this->request->getGet("length");
        $offset = empty($this->request->getVar('start')) ? 0 : $this->request->getGet("start");
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $length) + 1,

        ];

        $condition = [
            "employees.company_id" => $this->this_company_id,
            "employees.deletedAt"  => null,
        ];

        $addCondition = [
            "order"        => $this->request->getVar('order')[0] ?? null,
            "columns"    => $this->request->getVar('columns') ?? [],
            "divisi_id"   => $this->request->getVar('divisi_id'),
            "tipe"        => $this->request->getVar('tipe'),
            "employee_id" => $this->request->getVar("employee_id")
        ];

        // ambil list karyawan (sudah paginate)
        $employees    = $this->EmployeesModel->getEmployeeListAttendances($condition, $addCondition, $limit, $offset);
        $employeeData = $employees['data'];
        $employeeIds  = array_column($employeeData, 'id');


        // ambil log absensi
        if (count($employeeIds) != 0) {
            $logData = $this->AttendancesLogModel->getLogAmts($employeeIds, $year, $month);
        } else {
            $logData = [];
        }
        // mapping log -> fix: pakai $mapLog bukan $mapsLog
        $mapLog = [];
        foreach ($logData as $l) {
            $mapLog[$l['employees_id']][$l['periode']] = [
                'in'  => $l['check_in'],
                'out' => $l['check_out'],
                'status' => $l['status']
            ];
        }

        $bigDays = $this->BigDaysModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        $tanggalBigDays = array_column($bigDays, 'date');
        $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $resultData = [];
        foreach ($employeeData as $e) {
            $row = [];
            $row['no']     = $no++;
            $row['id']     = $e['id'];
            $row['nip']    = $e['nip'];
            $row['name']   = $e['name'];
            $row['divisi'] = $e['divisi'];
            $row['bagian'] = $e['bagian'];

            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                $tanggal = sprintf("%04d-%02d-%02d", $year, $month, $d);
                $dayName = date('D', strtotime($tanggal));

                $statusLibur = false;
                if (in_array($tanggal, $tanggalBigDays)) {
                    $statusLibur = true;
                }

                if (isset($mapLog[$e['id']][$tanggal])) {
                    $in  = $mapLog[$e['id']][$tanggal]['in'];
                    $out = $mapLog[$e['id']][$tanggal]['out'];
                    $statusIzin =  $mapLog[$e['id']][$tanggal]['status'];
                } else {
                    $in  = '';
                    $out = '';
                    $statusIzin = '';
                }


                // isi data
                $row['day_' . $d . '_in']  = $in;
                $row['day_' . $d . '_out'] = $out;
                $row['day_' . $d . '_date'] = $tanggal;


                // Kalau Libur
                if (($dayName === 'Sun' || $statusLibur) && empty($in)) {
                    $row['day_' . $d . '_in_class']  = 'bg-libur';
                    $row['day_' . $d . '_out_class'] = 'bg-libur';
                }

                // Kalau Alpha Masuk
                if (empty($in) && $dayName != 'Sun' && !$statusLibur) {
                    $row['day_' . $d . '_in_class']  = 'bg-alpha';
                }

                // Kalau Alpha Pulang
                if (empty($in) && $dayName != 'Sun' && !$statusLibur) {
                    $row['day_' . $d . '_out_class'] = 'bg-alpha';
                }

                if (!empty($statusIzin)) {
                    $mapping = [
                        'CUTI TAHUNAN_CT' => 'bg-cuti-tahunan',
                        'CUTI HAID_CHD' => 'bg-cuti-haid',
                        'CUTI HAMIL_CHL' => 'bg-cuti-hamil',
                        'CUTI MELAHIRKAN_CM' => 'bg-cuti-melahirkan',
                        'IJIN_I' => 'bg-ijin',
                        'SAKIT_S' => 'bg-sakit',
                        'RL_RL' => 'bg-rl',
                    ];

                    $row['day_' . $d . '_in_class']  = $mapping[$statusIzin];
                    $row['day_' . $d . '_out_class']  = $mapping[$statusIzin];
                    $row['day_' . $d . '_in']  = explode('_', $statusIzin)[1];
                    $row['day_' . $d . '_out']  = explode('_', $statusIzin)[1];
                }

                // Kalau memang hadir
                if (!empty($in)) {
                    $row['day_' . $d . '_in_class']  = 'bg-hadir';
                }

                if (!empty($out)) {
                    $row['day_' . $d . '_out_class']  = 'bg-hadir';
                }
            }

            $resultData[] = $row;
        }

        // Kolom tabel
        $columns = [];
        $columns[] = ["data" => "no", "title" => "No", "className" => "text-center", "sortable" => true];
        $columns[] = ["data" => "nip", "title" => "Nip", "sortable" => true];
        $columns[] = ["data" => "name", "title" => "Karyawan", "sortable" => true];
        $columns[] = ["data" => "divisi", "title" => "Dept", "sortable" => true];
        $columns[] = ["data" => "bagian", "title" => "Bagian", "sortable" => true];

        for ($d = 1; $d <= $totalDaysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayName = date('D', strtotime($dateStr));

            $columns[] = [
                "data"      => "day_" . $d . "_in",
                "title"     => "IN <br>" . $d,
                "className" => "text-center", // cukup ini saja
                "sortable"  => false
            ];
            $columns[] = [
                "data"      => "day_" . $d . "_out",
                "title"     => "OUT <br>" . $d,
                "className" => "text-center", // cukup ini saja
                "sortable"  => false
            ];
        }


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $employees['totalData'],
            "recordsFiltered" => $employees['totalFilteredData'],
            "columns"         => $columns,
            "data"            => $resultData,
            "payload"         => $payload
        ];

        return $this->response->setJSON($data);
    }

    public function allLogTotal()
    {
        // Ambil parameter bulan (default bulan sekarang)
        $monthReq = $this->request->getVar('month') ?: date('Y-m');
        [$year, $month] = explode('-', $monthReq);

        // DataTable pagination
        $length  = $this->request->getGet("length") ?: 25;
        $offset  = $this->request->getVar('start') ?: 0;
        $payload = [
            "pageSize"    => $length,
            "currentPage" => ($offset / $length) + 1,
        ];

        // Filter default
        $condition = [
            "employees.company_id" => $this->this_company_id,
            "employees.deletedAt"  => null,
        ];

        // Filter tambahan
        $addCondition = [
            "order"       => $this->request->getVar('order')[0] ?? null,
            "columns"     => $this->request->getVar('columns') ?? [],
            "divisi_id"   => $this->request->getVar('divisi_id'),
            "tipe"        => $this->request->getVar('tipe'),
            "employee_id" => $this->request->getVar("employee_id"),
        ];

        // Ambil list karyawan (sudah dipaginate)
        $employees    = $this->EmployeesModel->getEmployeeListAttendances($condition, $addCondition, $length, $offset);
        $employeeData = $employees['data'];
        $employeeIds  = array_column($employeeData, 'id');

        // Ambil log absensi per karyawan
        $logData = !empty($employeeIds)
            ? $this->AttendancesLogModel->getLogAmts($employeeIds, $year, $month)
            : [];

        // Map log absensi -> per karyawan + tanggal
        $mapLog = [];
        foreach ($logData as $l) {
            $mapLog[$l['employees_id']][$l['periode']] = [
                'in'     => $l['check_in'],
                'out'    => $l['check_out'],
                'status' => $l['status'],
            ];
        }

        // Ambil tanggal hari besar (libur)
        $bigDays       = $this->BigDaysModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        $tanggalBigDay = array_column($bigDays, 'date');

        // Total hari dalam bulan
        $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $no         = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $resultData = [];

        foreach ($employeeData as $e) {
            $row = [
                'no'     => $no++,
                'id'     => $e['id'],
                'nip'    => $e['nip'],
                'name'   => $e['name'],
                'divisi' => $e['divisi'],
                'bagian' => $e['bagian'],
            ];

            // Inisialisasi counter
            $totalCutiTahunan    = 0;
            $totalCutiHaid       = 0;
            $totalCutiHamil      = 0;
            $totalCutiMelahirkan = 0;
            $totalIjin           = 0;
            $totalSakit          = 0;
            $totalRl             = 0;
            $totalHadir          = 0;
            $totalAlpha          = 0;
            $totalLibur          = 0;

            // Loop setiap tanggal dalam bulan
            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                $tanggal   = sprintf("%04d-%02d-%02d", $year, $month, $d);
                $dayName   = date('D', strtotime($tanggal));
                $statusLibur = in_array($tanggal, $tanggalBigDay);

                // Ambil log per tanggal
                $in        = $mapLog[$e['id']][$tanggal]['in']     ?? '';
                $out       = $mapLog[$e['id']][$tanggal]['out']    ?? '';
                $statusIzin = $mapLog[$e['id']][$tanggal]['status'] ?? '';

                // Jika libur (Minggu / hari besar) dan tidak ada absen
                if (($dayName === 'Sun' || $statusLibur) && empty($in)) {
                    $totalLibur++;
                }

                // Jika alpha (tidak hadir, bukan Minggu/hari besar)
                if (empty($in) && empty($out) && $dayName !== 'Sun' && !$statusLibur) {
                    $totalAlpha++;
                }

                // Hitung status izin
                switch ($statusIzin) {
                    case 'CUTI TAHUNAN_CT':
                        $totalCutiTahunan++;
                        break;
                    case 'CUTI HAID_CHD':
                        $totalCutiHaid++;
                        break;
                    case 'CUTI HAMIL_CHL':
                        $totalCutiHamil++;
                        break;
                    case 'CUTI MELAHIRKAN_CM':
                        $totalCutiMelahirkan++;
                        break;
                    case 'IJIN_I':
                        $totalIjin++;
                        break;
                    case 'SAKIT_S':
                        $totalSakit++;
                        break;
                    case 'RL_RL':
                        $totalRl++;
                        break;
                }

                // Jika hadir (ada in/out)
                if (!empty($in) || !empty($out)) {
                    $totalHadir++;
                }
            }

            // Simpan hasil per karyawan
            $row['total_cuti_tahunan']    = $totalCutiTahunan;
            $row['total_cuti_haid']       = $totalCutiHaid;
            $row['total_cuti_hamil']      = $totalCutiHamil;
            $row['total_cuti_melahirkan'] = $totalCutiMelahirkan;
            $row['total_ijin']            = $totalIjin;
            $row['total_sakit']           = $totalSakit;
            $row['total_rl']              = $totalRl;
            $row['total_hadir']           = $totalHadir;
            $row['total_alpha']           = $totalAlpha;
            $row['total_libur']           = $totalLibur;

            $resultData[] = $row;
        }

        // tinggal return untuk datatable
        return $this->response->setJSON([
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $employees['totalData'],
            "recordsFiltered" => $employees['totalFilteredData'],
            "data"            => $resultData,
        ]);
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

        $employessModel = new EmployeesModel();

        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $this->this_company_id,
            //'users.id' => null
        ];

        $result = $employessModel->select("employees.name, employees.id")
            ->join('users', 'users.employee_id = employees.id', 'left')
            ->where($arrCondition)
            ->groupStart()
            ->like('employees.name', $employeesName)
            ->orLike('employees.nip', $employeesName)
            ->groupEnd()
            ->findAll();

        return \response()->setJSON([
            'data' => $result
        ]);
    }

    public function getLogAttendanceDetail()
    {
        Locale::setDefault('id_ID');

        $tanggal = $this->request->getVar('tanggal');
        $employeeID = $this->request->getVar('employee_id');
        $time = Time::createFromFormat('Y-m-d', $tanggal);

        $result = [
            'tanggal' => static::getDayIndonesia($time->format('l')) . ", " . $time->format('d F Y'),
            'keterangan' => "-",
            'checkIn' => "-",
            'checkOut' => "-",
            'status' => "-",
            'employee' => $this->EmployeesModel->where('id', $employeeID)->first(),
            'jamTerlambat' => "-"
        ];

        $formPerizinan = $this->FormPerijinanModel->where('periode', $tanggal)
            ->where('employee_id', $employeeID)
            ->first();
        $hariLibur = $this->BigDaysModel->where('date', $tanggal)
            ->first();

        $selectQry = "
        DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
        DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout,
        attendances_unit.name as nama_unit
        ";

        $logAttandance = $this->AttendancesLogModel
            ->select($selectQry)
            ->join('attendances_unit', 'attendances_unit.id = attendances_log.attendances_unit_id', 'left')
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
        $jamKerja = $this->EmployeeJamKerjaModel->getJamKerjaUsedByEmployeeId($tanggal, $employeeID);
        // APPEND TO RESULT
        $result['jamKerja'] = $jamKerja;
        $result['namaUnit'] = count($logAttandance) == 0 ? "" : $logAttandance[0]->nama_unit;

        return response()->setJSON([
            'status' => true,
            'data' => $result,
            'token' => csrf_hash(),
        ]);
    }

    public function exportExcelLogPresensi()
    {
        $monthReq = $this->request->getVar('month') ?: date('Y-m');
        [$year, $month] = explode('-', $monthReq);

        $monthName = strtoupper(date('F Y', strtotime("$year-$month-01"))); // contoh: SEPTEMBER 2025

        // ambil data employees
        $condition = [
            "employees.company_id" => $this->this_company_id,
            "employees.deletedAt"  => null,
        ];
        $addCondition = [
            "divisi_id"   => $this->request->getVar('divisi_id'),
            "tipe"        => $this->request->getVar('tipe'),
            "employee_id" => $this->request->getVar("employee_id"),
        ];
        $employees    = $this->EmployeesModel->getEmployeeListAttendances($condition, $addCondition, 0, 10000000);
        $employeeData = $employees['data'];
        $employeeIds  = array_column($employeeData, 'id');

        // log attendance
        $logData = !empty($employeeIds)
            ? $this->AttendancesLogModel->getLogAmts($employeeIds, $year, $month)
            : [];

        $mapLog = [];
        foreach ($logData as $l) {
            $mapLog[$l['employees_id']][$l['periode']] = [
                'in'     => $l['check_in'],
                'out'    => $l['check_out'],
                'status' => $l['status'],
            ];
        }

        $bigDays = $this->BigDaysModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $tanggalBigDay = array_column($bigDays, 'date');
        $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // ==============
        // BUAT EXCEL
        // ==============
        $spreadsheet = new Spreadsheet();

        // ================= Sheet 1 : Detil =================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle("Detail Presensi");

        // Judul
        $sheet1->mergeCells('A1:Z1');
        $sheet1->setCellValue('A1', "DETIL PRESENSI BULAN $monthName");
        $sheet1->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Header mulai baris ke-3
        $headers = ['No', 'NIP', 'Nama', 'Divisi', 'Bagian'];
        $colIndex = 1;
        $rowHeader = 3;
        foreach ($headers as $h) {
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowHeader, $h);
        }
        for ($d = 1; $d <= $totalDaysInMonth; $d++) {
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowHeader, "IN $d");
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowHeader, "OUT $d");
        }

        // Style header
        $lastCol = $colIndex - 1;
        $sheet1->getStyle("A{$rowHeader}:" . $sheet1->getCellByColumnAndRow($lastCol, $rowHeader)->getCoordinate())
            ->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        // Isi data
        $rowIndex = $rowHeader + 1;
        $no = 1;
        foreach ($employeeData as $e) {
            $colIndex = 1;
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $no++);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['nip']);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['name']);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['divisi']);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['bagian']);

            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                $tanggal = sprintf("%04d-%02d-%02d", $year, $month, $d);
                $dayLog  = $mapLog[$e['id']][$tanggal] ?? null;

                $in  = $dayLog['in'] ?? '';
                $out = $dayLog['out'] ?? '';
                $status = $dayLog['status'] ?? '';

                if ($status) {
                    $val = explode("_", $status)[1];
                    $in = $out = $val;
                }

                $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $in);
                $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $out);
            }
            $rowIndex++;
        }

        // Autosize kolom
        foreach (range('A', $sheet1->getCellByColumnAndRow($lastCol, 1)->getColumn()) as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // Border isi
        $sheet1->getStyle("A" . ($rowHeader) . ":" . $sheet1->getCellByColumnAndRow($lastCol, $rowIndex - 1)->getCoordinate())
            ->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        // ================= Sheet 2 : Rekap =================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle("Rekap Total");

        $sheet2->mergeCells('A1:O1');
        $sheet2->setCellValue('A1', "REKAPAN TOTAL PRESENSI BULAN $monthName");
        $sheet2->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Header
        $headers2 = [
            'No',
            'NIP',
            'Nama',
            'Divisi',
            'Bagian',
            'Cuti Tahunan',
            'Cuti Haid',
            'Cuti Hamil',
            'Cuti Melahirkan',
            'Ijin',
            'Sakit',
            'RL',
            'Hadir',
            'Alpha',
            'Libur'
        ];
        $colIndex = 1;
        $rowHeader2 = 3;
        foreach ($headers2 as $h) {
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowHeader2, $h);
        }

        $lastCol2 = $colIndex - 1;
        $sheet2->getStyle("A{$rowHeader2}:" . $sheet2->getCellByColumnAndRow($lastCol2, $rowHeader2)->getCoordinate())
            ->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        // Isi data rekap
        $rowIndex = $rowHeader2 + 1;
        $no = 1;
        foreach ($employeeData as $e) {
            $colIndex = 1;
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $no++);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['nip']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['name']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['divisi']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['bagian']);

            // hitung rekap
            $total = [
                'ct' => 0,
                'chd' => 0,
                'chl' => 0,
                'cm' => 0,
                'ijin' => 0,
                'sakit' => 0,
                'rl' => 0,
                'hadir' => 0,
                'alpha' => 0,
                'libur' => 0
            ];

            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                $tanggal = sprintf("%04d-%02d-%02d", $year, $month, $d);
                $dayName = date('D', strtotime($tanggal));
                $log     = $mapLog[$e['id']][$tanggal] ?? null;
                $in      = $log['in'] ?? '';
                $out     = $log['out'] ?? '';
                $status  = $log['status'] ?? '';

                if (($dayName === 'Sun' || in_array($tanggal, $tanggalBigDay)) && empty($in)) {
                    $total['libur']++;
                } elseif (empty($in) && empty($out) && $dayName !== 'Sun') {
                    $total['alpha']++;
                }

                switch ($status) {
                    case 'CUTI TAHUNAN_CT':
                        $total['ct']++;
                        break;
                    case 'CUTI HAID_CHD':
                        $total['chd']++;
                        break;
                    case 'CUTI HAMIL_CHL':
                        $total['chl']++;
                        break;
                    case 'CUTI MELAHIRKAN_CM':
                        $total['cm']++;
                        break;
                    case 'IJIN_I':
                        $total['ijin']++;
                        break;
                    case 'SAKIT_S':
                        $total['sakit']++;
                        break;
                    case 'RL_RL':
                        $total['rl']++;
                        break;
                }

                if (!empty($in) || !empty($out)) {
                    $total['hadir']++;
                }
            }

            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['ct']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['chd']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['chl']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['cm']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['ijin']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['sakit']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['rl']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['hadir']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['alpha']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['libur']);

            $rowIndex++;
        }

        // Autosize kolom
        foreach (range('A', $sheet2->getCellByColumnAndRow($lastCol2, 1)->getColumn()) as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // Border isi
        $sheet2->getStyle("A" . ($rowHeader2) . ":" . $sheet2->getCellByColumnAndRow($lastCol2, $rowIndex - 1)->getCoordinate())
            ->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        // ================= Download =================
        $filename = "Log_Presensi_{$monthReq}.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
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
        $attandanceModel = new AttendancesModel();

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
        $statusPerizinan = $metaDataModel->where('name', "Status Perizinan")
            ->orderBy('name', "ASC")
            ->findAll();
        $startMonth =  $resStartEndMonth[0];
        $endMonth = $resStartEndMonth[1];

        function num2alpha($n)
        {
            for ($r = ""; $n >= 0; $n = intval($n / 26) - 1)
                $r = chr($n % 26 + 0x41) . $r;
            return $r;
        }
        function getColumnLetters($start, $end)
        {
            $letters = [];
            $start = strtoupper($start);
            $end = strtoupper($end);
            $startIndex = Coordinate::columnIndexFromString($start);
            $endIndex = Coordinate::columnIndexFromString($end);

            for ($i = $startIndex; $i <= $endIndex; $i++) {
                $letters[] = Coordinate::stringFromColumnIndex($i);
            }

            return $letters;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(0);

        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Karyawan')
            ->setCellValue('C1', 'Department')
            ->setCellValue('D1', 'Bagian')
            ->setCellValue('E1', $startMonth['firstMonthName']);


        $startMonthLastColumn = $startMonth['totalDay'] + 3;

        $sheet->mergeCells('E1:' .  num2alpha($startMonthLastColumn) . '1');
        $sheet->setCellValue(num2alpha($startMonthLastColumn + 1) . '1',  $endMonth['secondMonthName']);
        foreach (getColumnLetters('E', num2alpha($startMonthLastColumn + $endMonth['totalDay'])) as $col) {
            $sheet->getColumnDimension($col)->setWidth(5);
        }
        $sheet->mergeCells(num2alpha($startMonthLastColumn  + 1) . '1:' . num2alpha($startMonthLastColumn + $endMonth['totalDay']) . '1');
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');
        $column = 4;
        foreach ($allDates as $a) {
            if (date("N", strtotime($a)) == 7) {
                $sheet->setCellValue(num2alpha($column) . "2", date('d', strtotime($a)));
                $sheet->getStyle(num2alpha($column) . "2")->getFont()->getColor()->setARGB(Color::COLOR_RED);
            } else {
                $sheet->setCellValue(num2alpha($column) . "2", date('d', strtotime($a)));
            }
            $column++;
        }
        $no = 1;
        $row = 3;
        $column = 4;
        foreach ($employeeData as $i => $e) {
            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, strtoupper($e['name']))
                ->setCellValue('C' . $row, strtoupper($e["divisi"]))
                ->setCellValue('D' . $row, strtoupper($e["namaBagian"]));

            $j = 1;
            foreach ($allDates as $a) {

                $attandance = $attandanceModel->getAttendances($a, $e['id']);
                if ($attandance == null) {
                    $sheet->setCellValue(num2alpha($column) . $row, 'A')
                        ->getStyle(num2alpha($column)  . $row)
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('e7323a');
                } else {
                    $statusKode = explode("_", $attandance->status)[1];
                    if ($statusKode == "A") {
                        $sheet->setCellValue(num2alpha($column) . $row, 'A')
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('e7323a');
                    } elseif ($statusKode == "H") {
                        $sheet->setCellValue(num2alpha($column) . $row, 'H')
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('304de2');
                    } elseif ($statusKode == "I") {
                        $sheet->setCellValue(num2alpha($column) . $row, $statusKode)
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('17a2b8');
                    } elseif ($statusKode == "CT") {
                        $sheet->setCellValue(num2alpha($column) . $row, $statusKode)
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('ffc107');
                    } elseif ($statusKode == "CHD") {
                        $sheet->setCellValue(num2alpha($column) . $row, $statusKode)
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('242120');
                    } elseif ($statusKode == "CHL") {
                        $sheet->setCellValue(num2alpha($column) . $row, $statusKode)
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('C34A36');
                    } elseif ($statusKode == "CM") {
                        $sheet->setCellValue(num2alpha($column) . $row, $statusKode)
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('C34A36');
                    } elseif ($statusKode == "S") {
                        $sheet->setCellValue(num2alpha($column) . $row, $statusKode)
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('28a745');
                    } elseif ($statusKode == "L") {
                        $sheet->setCellValue(num2alpha($column) . $row, $statusKode)
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('845EC2');
                    } elseif ($statusKode == "RL") {
                        $sheet->setCellValue(num2alpha($column) . $row, $statusKode)
                            ->getStyle(num2alpha($column)  . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('ff7b00');
                    }
                }
                $column++;
            }
            $column = 4;
            $row++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->setTitle('Absensi Final');
        $spreadsheet->createSheet();

        $sheet2 = $spreadsheet->setActiveSheetIndex(1);
        $sheet2->setTitle('Rekap Absensi Final');
        $sheet2->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Karyawan')
            ->setCellValue('C1', 'Department')
            ->setCellValue('D1', 'Bagian');
        $column = 4;
        $keteranganStr = "";
        foreach ($statusPerizinan as $s) {
            $sheet2->setCellValue(num2alpha($column) . "1", explode("_", $s['value'])[1]);
            $column++;
            $keteranganStr .= " " . explode("_", $s['value'])[0] . "(" . explode("_", $s['value'])[1] . ")";
        }
        foreach (range('B', 'D') as $columnID) {
            $sheet2->getColumnDimension($columnID)->setAutoSize(true);
        }
        foreach (range('E', num2alpha($column)) as $rekapCol) {
            $sheet2->getColumnDimension($rekapCol)->setWidth(4);
        }

        $sheet2->getColumnDimension('A')->setWidth(4);
        //rekap sheet
        $row2 = 2;
        $no2 = 1;
        foreach ($employeeData as $i => $e) {
            $status = $attandanceModel->getStatusAttendances($year, $month, $e['id']);
            $sheet2->setCellValue('A' . $row2,  $no2++)
                ->setCellValue('B' . $row2,  $e['name'])
                ->setCellValue('C' . $row2, $e['divisi'])
                ->setCellValue('D' . $row2,  $e["namaBagian"]);
            //column for rekap
            $column2 = 4;
            foreach ($statusPerizinan as $s) {
                $sheet2->setCellValue(num2alpha($column2) . $row2,  $status[$s['value']]);
                $column2++;
            }
            $row2++;
        }


        $sheet2->setCellValue('A' . ($row2 + 2), 'Keteranagan: ' . $keteranganStr);

        $writer = new Xlsx($spreadsheet);
        $filename = " AbsensiFinal_" . $yearMonth;

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;

        // return \view('hr/attendance/excel-attendance', $data);
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
            $startMonth,
            $endMonth
        ];
    }
}
