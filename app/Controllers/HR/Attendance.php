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
use App\Models\UangMakanHarianModel;
use CodeIgniter\I18n\Time;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Locale;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
    protected $AttendanceModel;
    protected $CompanyModel;
    protected $UangMakanHarianModel;

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
        $this->AttendanceModel = new AttendancesModel();
        $this->CompanyModel = new CompaniesModel();
        $this->UangMakanHarianModel = new UangMakanHarianModel();
    }

    public function indexLog()
    {
        $dataDivisi = $this->DivisiModel->getDivisiAccess();
        $dataStatusPerizinanAll = $this->MetadataModel
            ->where('name', "Status Perizinan")
            ->whereNotIn('value', ['LIBUR_L'])
            ->orderBy("FIELD(value, 'HADIR_H') DESC", '', false) // biar HADIR_H duluan
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

        $length = empty($this->request->getVar("length")) ? 25 : $this->request->getVar("length");
        $limit = empty($this->request->getVar('length')) ? 25 : $this->request->getVar("length");
        $offset = empty($this->request->getVar('start')) ? 0 : $this->request->getVar("start");
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $length) + 1,

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
            "draw"            => intval($this->request->getVar("draw")),
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
        $length  = $this->request->getVar("length") ?: 25;
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
            "draw"            => intval($this->request->getVar("draw")),
            "recordsTotal"    => $employees['totalData'],
            "recordsFiltered" => $employees['totalFilteredData'],
            "data"            => $resultData,
        ]);
    }

    public function indexAttendance()
    {
        $dataDivisi = $this->DivisiModel->getDivisiAccess();
        $dataStatusPerizinanAll = $this->MetadataModel
            ->where('name', "Status Perizinan")
            ->whereNotIn('value', ['LIBUR_L'])
            ->orderBy("FIELD(value, 'HADIR_H') DESC", '', false) // biar HADIR_H duluan
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

        return view('hr/attendance/attendance-generate', $data);
    }

    public function allAttendance()
    {
        $monthReq = $this->request->getVar('month');
        if (empty($monthReq)) {
            $monthReq = date('Y-m');
        }

        $monthSplit = explode('-', $monthReq);
        $year = $monthSplit[0];
        $month = $monthSplit[1];

        $length = empty($this->request->getVar("length")) ? 25 : $this->request->getVar("length");
        $limit = empty($this->request->getVar('length')) ? 25 : $this->request->getVar("length");
        $offset = empty($this->request->getVar('start')) ? 0 : $this->request->getVar("start");
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $length) + 1,

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

        // ambil data absensi
        if (count($employeeIds) != 0) {
            $attendanceData = $this->AttendanceModel->getAttendanceAmt(
                $employeeIds,
                $year,
                $month
            );
        } else {
            $attendanceData = [];
        }

        $mapAttendance = [];
        foreach ($attendanceData as $l) {
            $mapAttendance[$l['employee_id']][$l['periode']] = [
                'in'  => $l['checkin'],
                'out' => $l['checkout'],
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

                if (isset($mapAttendance[$e['id']][$tanggal])) {
                    $in  = $mapAttendance[$e['id']][$tanggal]['in'];
                    $out = $mapAttendance[$e['id']][$tanggal]['out'];
                    $statusIzin =  $mapAttendance[$e['id']][$tanggal]['status'];
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

                if (!empty($statusIzin) && empty($in)) {
                    $mapping = [
                        'CUTI TAHUNAN_CT' => 'bg-cuti-tahunan',
                        'CUTI HAID_CHD' => 'bg-cuti-haid',
                        'CUTI HAMIL_CHL' => 'bg-cuti-hamil',
                        'CUTI MELAHIRKAN_CM' => 'bg-cuti-melahirkan',
                        'IJIN_I' => 'bg-ijin',
                        'SAKIT_S' => 'bg-sakit',
                        'RL_RL' => 'bg-rl',
                        'ALPHA_A' => 'bg-alpha',
                        'LIBUR_L' => 'bg-libur'
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
            "draw"            => intval($this->request->getVar("draw")),
            "recordsTotal"    => $employees['totalData'],
            "recordsFiltered" => $employees['totalFilteredData'],
            "columns"         => $columns,
            "data"            => $resultData,
            "payload"         => $payload
        ];

        return $this->response->setJSON($data);
    }

    public function allAttendanceTotal()
    {
        $monthReq = $this->request->getVar('month');
        if (empty($monthReq)) {
            $monthReq = date('Y-m');
        }

        $monthSplit = explode('-', $monthReq);
        $year = $monthSplit[0];
        $month = $monthSplit[1];

        $length = empty($this->request->getVar("length")) ? 25 : $this->request->getVar("length");
        $limit = empty($this->request->getVar('length')) ? 25 : $this->request->getVar("length");
        $offset = empty($this->request->getVar('start')) ? 0 : $this->request->getVar("start");
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $length) + 1,

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

        // ambil data absensi
        if (count($employeeIds) != 0) {
            $attendanceData = $this->AttendanceModel->getAttendanceAmt(
                $employeeIds,
                $year,
                $month
            );
        } else {
            $attendanceData = [];
        }

        $mapAttendance = [];
        foreach ($attendanceData as $l) {
            $mapAttendance[$l['employee_id']][$l['periode']] = [
                'in'  => $l['checkin'],
                'out' => $l['checkout'],
                'status' => $l['status']
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
                $in        = $mapAttendance[$e['id']][$tanggal]['in']     ?? '';
                $out       = $mapAttendance[$e['id']][$tanggal]['out']    ?? '';
                $statusIzin = $mapAttendance[$e['id']][$tanggal]['status'] ?? '';

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
            "draw"            => intval($this->request->getVar("draw")),
            "recordsTotal"    => $employees['totalData'],
            "recordsFiltered" => $employees['totalFilteredData'],
            "data"            => $resultData,
        ]);
    }

    public function generateAttendanceGlobalAction()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            // declare variable
            $monthYear = explode('-', $this->request->getVar('month_year_global'));
            $month = $monthYear[1];
            $year = $monthYear[0];
            $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('start_date_global'))));
            $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finish_date_global'))));

            $employeeData = $this->EmployeesModel->getEmployees($this->this_company_id);

            if (count($employeeData) == 0) {
                return $this->response->setJSON([
                    'message' => "Employee tidak ditemukan di company ini",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            // remove all if exist and insert again
            $this->AttendanceModel->where('periode >=', $startDate)
                ->where('periode <=', $endDate)
                ->where('company_id', $this->this_company_id)
                ->delete();

            $res = $this->AttendanceModel->generate(
                $employeeData,
                $startDate,
                $endDate,
                $year,
                $month,
                $this->this_company_id
            );

            $db->transCommit();

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
        } catch (Exception $e) {

            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function generateAttendancePersonalAction()
    {
        $db = \Config\Database::connect();

        try {
            $db->transBegin();
            $monthYear = explode('-', $this->request->getVar('month_year_personal'));
            $month = $monthYear[1];
            $year = $monthYear[0];
            $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('start_date_personal'))));
            $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finish_date_personal'))));
            $employeeID = $this->request->getVar('employee_id_filter');

            $employeeData = $this->EmployeesModel->where('id', $employeeID)->findAll();
            $employeeStatus = $this->EmployeesModel->where('id', $employeeID)->first();

            if ($employeeStatus['status'] != "Aktif") {
                return $this->response->setJSON([
                    'message' => "Status karyawan " . $employeeStatus['name'] . " adalah " . $employeeStatus['status'],
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            $this->AttendanceModel->where('periode >=', $startDate)
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

            $res = $this->AttendanceModel->generate(
                $employeeData,
                $startDate,
                $endDate,
                $year,
                $month,
                $this->this_company_id
            );

            $db->transCommit();

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
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function getDetailAttendance()
    {
        $employeeID = $this->request->getVar('employee_id');
        $tanggal = $this->request->getVar('tanggal');
        $time = Time::createFromFormat('Y-m-d', $tanggal);

        Locale::setDefault('id_ID');
        $attendanceDetail = $this->AttendanceModel->where('periode', $tanggal)
            ->where('employee_id', $employeeID)
            ->first();

        if ($attendanceDetail == null) {
            return response()->setJSON([
                'message' => "Data absensi belum digenerate / tidak ada, silahkan generate data personal karyawan tersebut",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }


        $employee = $this->EmployeesModel->where('id', $employeeID)->first();

        $resultData = [
            'tanggal' => static::getDayIndonesia($time->format('l')) . ", " . $time->format('d F Y'),
            'attendance' => $attendanceDetail,
            'employee' => $employee,
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
                $bigDays = $this->BigDaysModel
                    ->where('date', $attendanceDetail['periode'])
                    ->where('company_id', $this->this_company_id)
                    ->first();
                if ($bigDays != null) {
                    $resultData['keterangan'] = $bigDays['name'];
                }
            }
        }

        // GET JAM KERJA USED
        $jamKerja = $this->EmployeeJamKerjaModel->getJamKerjaUsedByEmployeeId($tanggal, $employeeID);
        // APPEND TO RESULT
        $resultData['jamKerja'] = $jamKerja;

        return $this->response->setJSON([
            'data' => $resultData,
            'token' => csrf_hash(),
            'status' => true
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

        $this->AttendanceModel->update($attendenceID, [
            'checkin' => $checkIN, // in
            'checkout' => $checkOut, // out
            'status' => $statusKehadiran,
            'reason' => $reason,
            'isApproved' => $isApproved
        ]);

        return $this->response->setJSON([
            'message' => "Attendence diperbaruhi",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getEmployeesLike()
    {
        $employeesName = $this->request->getVar('employeesName');

        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $this->this_company_id,
        ];

        $result = $this->EmployeesModel->select("employees.name, employees.id")
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
            'jamTerlambat' => "-",
            'uangMakanHarian' => null
        ];

        $formPerizinan = $this->FormPerijinanModel->where('periode', $tanggal)
            ->where('employee_id', $employeeID)
            ->first();
        $hariLibur = $this->BigDaysModel->where('date', $tanggal)
            ->first();

        $uangMakanHarian = $this->UangMakanHarianModel->where('tanggal', $tanggal)->where('employee_id', $employeeID)->first();

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
        $result['uangMakanHarian'] = $uangMakanHarian;

        return response()->setJSON([
            'status' => true,
            'data' => $result,
            'token' => csrf_hash(),
        ]);
    }

    public function exportExcelLogPresensiBulanan()
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
            $sheet1->mergeCellsByColumnAndRow($colIndex, $rowHeader, $colIndex, $rowHeader + 1);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowHeader, $h);
        }

        // Header tanggal
        for ($d = 1; $d <= $totalDaysInMonth; $d++) {
            $startCol = $colIndex;
            $sheet1->mergeCellsByColumnAndRow($startCol, $rowHeader, $startCol + 1, $rowHeader);
            $sheet1->setCellValueByColumnAndRow($startCol, $rowHeader, $d);

            $sheet1->setCellValueByColumnAndRow($startCol, $rowHeader + 1, "IN");
            $sheet1->setCellValueByColumnAndRow($startCol + 1, $rowHeader + 1, "OUT");

            $colIndex += 2;
        }

        $lastCol = $colIndex - 1;

        // Style header (2 baris)
        $sheet1->getStyle("A{$rowHeader}:" . $sheet1->getCellByColumnAndRow($lastCol, $rowHeader + 1)->getCoordinate())
            ->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        // Isi data mulai baris ke-5
        $rowIndex = $rowHeader + 2;
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

        // Auto width untuk kolom No, NIP, Nama, Divisi, Bagian
        foreach (range('A', 'E') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // ================= Sheet 2 : Rekap =================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle("Rekap Total");

        $sheet2->mergeCells('A1:O1');
        $sheet2->setCellValue('A1', "REKAPAN TOTAL PRESENSI BULAN $monthName");
        $sheet2->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Header rekap
        $headers2 = [
            'No',
            'NIP',
            'Nama',
            'Divisi',
            'Bagian',
            'Hadir',
            'Cuti Tahunan',
            'Cuti Haid',
            'Cuti Hamil',
            'Cuti Melahirkan',
            'Ijin',
            'Sakit',
            'RL',
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

            $total = ['ct' => 0, 'chd' => 0, 'chl' => 0, 'cm' => 0, 'ijin' => 0, 'sakit' => 0, 'rl' => 0, 'hadir' => 0, 'alpha' => 0, 'libur' => 0];

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

            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['hadir']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['ct']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['chd']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['chl']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['cm']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['ijin']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['sakit']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['rl']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['alpha']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['libur']);

            $rowIndex++;
        }

        // Autosize kolom rekap
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

    public function exportExcelLogPresensiHarian()
    {
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('start_date'))));
        $endDate   = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('end_date'))));

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
            ? $this->AttendancesLogModel->getLogByDateRangeAmts(
                $employeeIds,
                $startDate,
                $endDate
            )
            : [];

        // mapping data presensi
        $mapLog = [];
        foreach ($logData as $l) {
            if (!empty($l['check_in']) && !empty($l['check_out'])) {
                $status = "H";
            } else {
                $status = $l['status'] != null ? explode("_", $l['status'])[1] : "";
            }
            $mapLog[$l['employees_id']][$l['periode']] = [
                'in'     => $l['check_in'],
                'out'    => $l['check_out'],
                'status' => $status,
            ];
        }

        // mapping data uang harian
        $uangMakanData = !empty($employeeIds) ? $this->UangMakanHarianModel->getUangMakanHarianByDateRangeAmt(
            $employeeIds,
            $startDate,
            $endDate
        ) : [];
        $mapUangMakanHarian = [];
        foreach ($uangMakanData as $u) {
            $mapUangMakanHarian[$u['employee_id']][$u['tanggal']] = [
                'nominal' => $u['nominal']
            ];
        }


        // ambil big days
        $bigDays = $this->BigDaysModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $tanggalBigDay = array_column($bigDays, 'date');

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Presensi Harian");

        // date range
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day')
        );

        $row = 1;

        // array rekap total per karyawan
        $rekapKaryawan = [];

        foreach ($period as $date) {
            $tgl = $date->format('Y-m-d');
            $dayName = date('D', strtotime($tgl));

            // Header per tanggal
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", "Tanggal: {$tgl}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DDDDDD']
                ]
            ]);
            $row++;

            // Header kolom
            $headers = ['No', 'Nip', 'Nama', 'Divisi', 'Bagian', 'IN', 'OUT', 'Status', 'Uang Makan', 'Terlambat (Menit)'];
            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue("{$col}{$row}", $h);
                $sheet->getStyle("{$col}{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);
                $col++;
            }
            $row++;

            // isi data per karyawan
            $no = 1;
            foreach ($employeeData as $emp) {
                $nip    = $emp['nip'] ?? '';
                $nama   = $emp['name'] ?? '';
                $divisi = $emp['divisi'] ?? '';
                $bagian = $emp['bagian'] ?? '';

                $in     = $mapLog[$emp['id']][$tgl]['in'] ?? '';
                $out    = $mapLog[$emp['id']][$tgl]['out'] ?? '';
                $status = $mapLog[$emp['id']][$tgl]['status'] ?? '';
                $uangMakan = $mapUangMakanHarian[$emp['id']][$tgl]['nominal'] ?? 0;
                $keterlambatanMenit = "";

                // cek jika tanggal masuk big day
                if (in_array($tgl, $tanggalBigDay)) {
                    $status = "L";
                }

                if ($in == '' && $out == '' && $dayName == 'Sun' && $status == '') {
                    $status = "L";
                }

                if ($in == '' && $out == '' && $status == '') {
                    $status = "A";
                }

                if ($in != '' && $out != '') {
                    $status = "H";
                }

                if ($in != '') {
                    $keterlambatanCheck = static::keterlambatanCheck(
                        $tgl,
                        $in,
                        $emp['id']
                    );

                    $jamTerlambat = $keterlambatanCheck[1];
                    $inTime = new DateTime($in);
                    $lateTime = new DateTime($jamTerlambat);

                    $diff = $lateTime->diff($inTime);
                    $keterlambatanMenit = ($diff->h * 60) + $diff->i;

                    if ($inTime < $lateTime) {
                        $keterlambatanMenit = "";
                    }
                }

                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValue("B{$row}", $nip);
                $sheet->setCellValue("C{$row}", $nama);
                $sheet->setCellValue("D{$row}", $divisi);
                $sheet->setCellValue("E{$row}", $bagian);
                $sheet->setCellValue("F{$row}", $in);
                $sheet->setCellValue("G{$row}", $out);
                $sheet->setCellValue("H{$row}", $status);
                $sheet->setCellValue("I{$row}", $uangMakan);
                $sheet->setCellValue("J{$row}", !empty($keterlambatanMenit) ? $keterlambatanMenit : '');

                // border untuk isi
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                $sheet->setCellValue("I{$row}", $uangMakan);
                $sheet->getStyle("I{$row}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                $sheet->getStyle("I{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $row++;

                // Hitung total status per karyawan
                if (!isset($rekapKaryawan[$emp['id']])) {
                    $rekapKaryawan[$emp['id']] = [
                        'nip'            => $nip,
                        'nama'           => $nama,
                        'hadir'          => 0,
                        'alpa'           => 0,
                        'libur'          => 0,
                        'cuti_tahunan'   => 0,
                        'cuti_haid'      => 0,
                        'cuti_hamil'     => 0,
                        'cuti_melahirkan' => 0,
                        'ijin'           => 0,
                        'sakit'          => 0,
                        'rl'             => 0,
                    ];
                }

                switch ($status) {
                    case 'H':
                        $rekapKaryawan[$emp['id']]['hadir']++;
                        break;
                    case 'A':
                        $rekapKaryawan[$emp['id']]['alpa']++;
                        break;
                    case 'L':
                        $rekapKaryawan[$emp['id']]['libur']++;
                        break;
                    case 'CT':
                        $rekapKaryawan[$emp['id']]['cuti_tahunan']++;
                        break;
                    case 'CHD':
                        $rekapKaryawan[$emp['id']]['cuti_haid']++;
                        break;
                    case 'CHL':
                        $rekapKaryawan[$emp['id']]['cuti_hamil']++;
                        break;
                    case 'CM':
                        $rekapKaryawan[$emp['id']]['cuti_melahirkan']++;
                        break;
                    case 'I':
                        $rekapKaryawan[$emp['id']]['ijin']++;
                        break;
                    case 'S':
                        $rekapKaryawan[$emp['id']]['sakit']++;
                        break;
                    case 'RL':
                        $rekapKaryawan[$emp['id']]['rl']++;
                        break;
                }
            }

            // kasih spasi 2 baris antar tanggal
            $row += 2;
        }


        // === REKAP TOTAL DI BAWAH ===
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet->setCellValue("A{$row}", "REKAP KEHADIRAN");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // Header rekap
        $rekapHeaders = ['No', 'Nip', 'Nama', 'Hadir', 'Alpa', 'Libur', 'Cuti Tahunan', 'Cuti Haid', 'Cuti Hamil', 'Cuti Melahirkan', 'Ijin', 'Sakit', 'RL'];
        $col = 'A';
        foreach ($rekapHeaders as $h) {
            $sheet->setCellValue("{$col}{$row}", $h);
            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $col++;
        }
        $row++;

        // Isi data rekap
        $no = 1;
        foreach ($rekapKaryawan as $r) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $r['nip']);
            $sheet->setCellValue("C{$row}", $r['nama']);
            $sheet->setCellValue("D{$row}", $r['hadir']);
            $sheet->setCellValue("E{$row}", $r['alpa']);
            $sheet->setCellValue("F{$row}", $r['libur']);
            $sheet->setCellValue("G{$row}", $r['cuti_tahunan']);
            $sheet->setCellValue("H{$row}", $r['cuti_haid']);
            $sheet->setCellValue("I{$row}", $r['cuti_hamil']);
            $sheet->setCellValue("J{$row}", $r['cuti_melahirkan']);
            $sheet->setCellValue("K{$row}", $r['ijin']);
            $sheet->setCellValue("L{$row}", $r['sakit']);
            $sheet->setCellValue("M{$row}", $r['rl']);

            $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $row++;
        }

        // auto size kolom
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // output excel
        $filename = "Presensi_Harian_{$startDate}_sd_{$endDate}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function exportExcelPresensi()
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
        ];
        $employees    = $this->EmployeesModel->getEmployeeListAttendances($condition, $addCondition, 0, 10000000);
        $employeeData = $employees['data'];
        $employeeIds  = array_column($employeeData, 'id');

        // log attendance
        $logData = !empty($employeeIds)
            ? $this->AttendanceModel->getAttendanceAmt($employeeIds, $year, $month)
            : [];

        $mapLog = [];
        foreach ($logData as $l) {
            $mapLog[$l['employee_id']][$l['periode']] = [
                'in'     => $l['checkin'],
                'out'    => $l['checkout'],
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
        $sheet1->setTitle("Real Detail Presensi");

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
            $sheet1->mergeCellsByColumnAndRow($colIndex, $rowHeader, $colIndex, $rowHeader + 1);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowHeader, $h);
        }

        // Header tanggal
        for ($d = 1; $d <= $totalDaysInMonth; $d++) {
            $startCol = $colIndex;
            $sheet1->mergeCellsByColumnAndRow($startCol, $rowHeader, $startCol + 1, $rowHeader);
            $sheet1->setCellValueByColumnAndRow($startCol, $rowHeader, $d);

            $sheet1->setCellValueByColumnAndRow($startCol, $rowHeader + 1, "IN");
            $sheet1->setCellValueByColumnAndRow($startCol + 1, $rowHeader + 1, "OUT");

            $colIndex += 2;
        }

        $lastCol = $colIndex - 1;

        // Style header (2 baris)
        $sheet1->getStyle("A{$rowHeader}:" . $sheet1->getCellByColumnAndRow($lastCol, $rowHeader + 1)->getCoordinate())
            ->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        // Isi data mulai baris ke-5
        $rowIndex = $rowHeader + 2;
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

        // Auto width untuk kolom No, NIP, Nama, Divisi, Bagian
        foreach (range('A', 'E') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

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
            'Hadir',
            'Cuti Tahunan',
            'Cuti Haid',
            'Cuti Hamil',
            'Cuti Melahirkan',
            'Ijin',
            'Sakit',
            'RL',
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

            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['hadir']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['ct']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['chd']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['chl']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['cm']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['ijin']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['sakit']);
            $sheet2->setCellValueByColumnAndRow($colIndex++, $rowIndex, $total['rl']);
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
        $filename = "Real_Presensi_{$monthReq}.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    public function exportTriwulanPresensi()
    {
        $startMonth = $this->request->getVar('start_month');
        $endMonth   = $this->request->getVar('end_month');
        $divisiId   = $this->request->getVar('divisi_id');

        $startDateTime = DateTime::createFromFormat('Y-m', $startMonth);
        $endDateTime   = DateTime::createFromFormat('Y-m', $endMonth);

        if ($startDateTime > $endDateTime) {
            return redirect()->back()->with('error', 'Selesai Bulan tidak boleh lebih kecil dari mulai Bulan.');
        } elseif ($startDateTime->diff($endDateTime)->m != 2) {
            return redirect()->back()->with('error', 'Rentang mulai Bulan dan selesai Bulan adalah 3 bulan.');
        }

        $middleMonthDt = clone $startDateTime;
        $middleMonthDt->modify('+1 month');
        $middleMonth = $middleMonthDt->format('Y-m');

        $divisi  = $this->DivisiModel->where('id', $divisiId)->first();
        $company = $this->CompanyModel->where('id', $this->this_company_id)->first();

        $data = $this->AttendanceModel->getTriwulan(
            [$startMonth, $middleMonth, $endMonth],
            $divisiId,
            $this->this_company_id
        );

        $data = [
            'middleMonth' => $middleMonth,
            'startMonth'  => $startMonth,
            'endMonth'    => $endMonth,
            'unit'        => $company, // contoh, bisa ambil dari DB
            'divisi'      => $divisi,
            'data'        => $data
        ];

        // ✅ Mulai bikin Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Atas
        $sheet->setCellValue('A1', 'Hal : 1');
        $sheet->setCellValue('A2', 'Tgl : ' . date('d/m/Y'));

        $sheet->mergeCells('C4:H4');
        $sheet->setCellValue('C4', $data['unit']['holding_company']);
        $sheet->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('C5:H5');
        $sheet->setCellValue('C5', 'DATA ABSENSI KARYAWAN');
        $sheet->getStyle('C5')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('C6:H6');
        $sheet->setCellValue('C6', 'Unit ' . $data['unit']['company'] . ' - Departemen ' . $data['divisi']['divisi']);
        $sheet->getStyle('C6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ✅ Header Table
        $header = [
            'No',
            'Nip',
            'Nama Karyawan',
            convertToIndonesianMonth($data['startMonth']),
            convertToIndonesianMonth($data['middleMonth']),
            convertToIndonesianMonth($data['endMonth']),
            'Jumlah Absensi A+P+H',
            'Total Kupon (LBR)',
            'Tanda Tangan'
        ];

        $sheet->fromArray($header, null, 'A8');

        // Styling header
        $sheet->getStyle('A8:I8')->getFont()->setBold(true);
        $sheet->getStyle('A8:I8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A8:I8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // ✅ Isi data
        $row = 9;
        $no = 1;
        $totalKupon = 0;

        if (count($data['data']['res']) == 0) {
            $sheet->mergeCells("A{$row}:I{$row}");
            $sheet->setCellValue("A{$row}", 'Tidak ada data');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            foreach ($data['data']['res'] as $d) {
                $colData = [
                    $no++,
                    $d['nip'],
                    $d['name'],
                    static::getPresensiByMonth($d['kehadiran'], $data['startMonth']),
                    static::getPresensiByMonth($d['kehadiran'], $data['middleMonth']),
                    static::getPresensiByMonth($d['kehadiran'], $data['endMonth']),
                    $d['totalAPH'],
                    $d['totalKupon'],
                    '' // tanda tangan kosong
                ];
                $sheet->fromArray($colData, null, "A{$row}");

                // Border tiap row
                $sheet->getStyle("A{$row}:I{$row}")
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $totalKupon += $d['totalKupon'];
                $row++;
            }

            // ✅ Baris Total
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->setCellValue("A{$row}", 'Total');
            $sheet->setCellValue("H{$row}", $totalKupon);
            $sheet->getStyle("A{$row}:I{$row}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Autosize
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ✅ Download
        $fileName = 'Triwulan_Presensi_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function updateUangMakanHarian()
    {
        try {
            $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggal'))));
            $employeeId = $this->request->getVar('employee_id');
            $nominal = $this->request->getVar('nominal');

            $uangMakanHarian = $this->UangMakanHarianModel
                ->where('employee_id', $employeeId)
                ->where('tanggal', $tanggal)
                ->first();

            if ($uangMakanHarian == null) {
                $this->UangMakanHarianModel->insert([
                    'employee_id'  => $employeeId,
                    'tanggal' => $tanggal,
                    'nominal' => $nominal,
                ]);
            } else {
                $this->UangMakanHarianModel->update($uangMakanHarian['id'], [
                    'nominal' => $nominal
                ]);
            }

            return response()->setJSON([
                'status' => true,
                'message' => "Uang makan harian berhasil disimpan",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    // Helper: ambil data presensi per bulan
    static function getPresensiByMonth($kehadiran, $month)
    {
        foreach ($kehadiran as $k) {
            if ($k['yearMonth'] == $month) {
                return "{$k['A']} A {$k['P']} P {$k['H']} H";
            }
        }
        return "-";
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
