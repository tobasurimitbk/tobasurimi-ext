<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\API\Attendances;
use App\Controllers\BaseController;
use App\Controllers\Master\AttendancesUnit;
use App\Models\AttendancesLogOutsourcingModel;
use App\Models\AttendancesUnitOutsourceModel;
use App\Models\DivisisModel;
use App\Models\HROutsourcingCompanyModel;
use App\Models\HROutsourcingEmployeeModel;
use CodeIgniter\I18n\Time;
use DateInterval;
use DatePeriod;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Locale;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

use function PHPSTORM_META\map;

class Attendance extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $DivisiModel;
    protected $hrOutSourcingCompanyModel;
    protected $hrOutSourcingEmployeModel;
    protected $AttendancesUnitOutsourceModel;
    protected $Attendance;
    protected $divisiModel;
    protected $AttendancesLogOutsourcingModel;
    

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->hrOutSourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->AttendancesUnitOutsourceModel = new AttendancesUnitOutsourceModel();
        $this->hrOutSourcingEmployeModel = new HROutsourcingEmployeeModel();
        $this->AttendancesLogOutsourcingModel = new AttendancesLogOutsourcingModel();
        $this->Attendance = new AttendancesUnit();
        $this->divisiModel = new DivisisModel();
    }

    public function index()
    {
        $data = [
            "dataDivisi" => $this->divisiModel->getDivisiAccess(),
        ];
       
        return view('HROutsourcing/Attendance/index', $data);
    }

    public function allData()
    {
        // =============================
        // 0. Validasi input
        // =============================
        $companyId = $this->request->getVar('company_id');
        $date      = $this->request->getVar('date');

        if (!$companyId || !$date) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Company ID dan Tanggal harus diisi',
                'data'    => [],
                'total'   => 0
            ]);
        }

        try {
            // =============================
            // 1. Ambil company
            // =============================
            $company = $this->hrOutSourcingCompanyModel
                ->where('id', $companyId)
                ->where('deletedAt', null)
                ->first();

            if (!$company) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Perusahaan tidak ditemukan',
                    'data'    => [],
                    'total'   => 0
                ]);
            }

            $selectedDate = date('Y-m-d', strtotime($date));

            // =============================
            // 2. AMBIL PRESENSI DARI DATABASE
            // =============================
            $rawAttendance = $this->AttendancesLogOutsourcingModel
                    ->select('
                        attendance_log_outsource.employee_id AS pin,
                        attendance_log_outsource.datetime,
                        hr_outsourcing_employee.nama,
                        hr_outsourcing_employee.badge
                    ')
                    ->join(
                        'attendance_unit_outsource',
                        'attendance_unit_outsource.id = attendance_log_outsource.attendance_unit',
                        'left'
                    )
                    ->join(
                        'hr_outsourcing_employee',
                        'hr_outsourcing_employee.id = attendance_log_outsource.employee_id',
                        'left'
                    )
                    ->where('hr_outsourcing_employee.company_id', $companyId)
                    ->where('DATE(attendance_log_outsource.datetime)', $selectedDate)
                    ->orderBy('attendance_log_outsource.datetime', 'ASC')
                    ->findAll();

            /**
             * =====================================================
             * 3. ADAPTER → SAMAKAN FORMAT DENGAN DATA MESIN
             * =====================================================
             */
            $attendanceData = array_map(function ($row) {
                return [
                    'pin'      => (string) $row['pin'],
                    'datetime' => $row['datetime'],
                    'status'   => '0',   // dummy (logic kita yg nentuin)
                    'verified' => '1'    // trusted karena dari DB
                ];
            }, $rawAttendance);

            // =============================
            // 4. Ambil SEMUA karyawan
            // =============================
            $allEmployees = $this->hrOutSourcingEmployeModel
                ->where('company_id', $companyId)
                ->findAll();

            if (empty($allEmployees)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tidak ada karyawan',
                    'data'    => [],
                    'total'   => 0,
                    'date'    => $date
                ]);
            }

            // =============================
            // 5. Group attendance per employee
            // =============================
            $employeeAttendanceMap = [];

            foreach ($attendanceData as $att) {
                $pin = (int) $att['pin'];

                if (!isset($employeeAttendanceMap[$pin])) {
                    $employeeAttendanceMap[$pin] = [];
                }

                $employeeAttendanceMap[$pin][] = [
                    'datetime'  => $att['datetime'],
                    'timestamp' => strtotime($att['datetime']),
                    'type'      => $this->getAttendanceType($att['status']),
                    'verified'  => $att['verified']
                ];
            }

            // Sort waktu
            foreach ($employeeAttendanceMap as $pin => $records) {
                usort($employeeAttendanceMap[$pin], fn ($a, $b) => $a['timestamp'] <=> $b['timestamp']);
            }

            // =============================
            // 6. Proses SEMUA karyawan dengan perhitungan jam kerja
            // =============================
            $processedData = [];
            $counter = 1;

            foreach ($allEmployees as $employee) {
                $pin = (int) $employee['id'];

                // Default data
                $empData = [
                    'no'               => $counter++,
                    'user_id'          => $pin,
                    'name'             => $employee['nama'],
                    'badge_no'         => $employee['badge'],
                    'check_in'         => null,
                    'check_out'        => null,
                    'work_hours'       => '0:00', // Format jam:menit
                    'work_minutes'     => 0,      // Total menit
                    'verified_in'      => 'Not Verified',
                    'verified_out'     => 'Not Verified',
                    'status'           => 'Tidak Masuk',
                    'status_class'     => 'danger',
                    'company_name'     => $company['name'],
                    'company_id'       => $companyId,
                    'attendance_count' => 0
                ];

                if (isset($employeeAttendanceMap[$pin])) {
                    $records = $employeeAttendanceMap[$pin];
                    $empData['attendance_count'] = count($records);

                    // Ambil check in pertama dan check out terakhir
                    $firstRecord = $records[0];
                    $lastRecord = end($records);

                    // Format waktu
                    $empData['check_in'] = date('H:i:s', $firstRecord['timestamp']);
                    $empData['verified_in'] = $firstRecord['verified'] == '1' ? 'Verified' : 'Not Verified';

                    if (count($records) > 1) {
                        $empData['check_out'] = date('H:i:s', $lastRecord['timestamp']);
                        $empData['verified_out'] = $lastRecord['verified'] == '1' ? 'Verified' : 'Not Verified';

                        // Hitung total jam kerja
                        $checkInTime = $firstRecord['timestamp'];
                        $checkOutTime = $lastRecord['timestamp'];
                        
                        // Hitung selisih dalam detik
                        $diffSeconds = $checkOutTime - $checkInTime;
                        
                        // Konversi ke menit
                        $totalMinutes = floor($diffSeconds / 60);
                        $empData['work_minutes'] = $totalMinutes;
                        
                        // Format jam:menit
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        $empData['work_hours'] = sprintf("%d:%02d", $hours, $minutes);

                        // Tentukan status berdasarkan jam kerja
                        if ($totalMinutes >= 480) { // 8 jam = 480 menit
                            $empData['status'] = 'Hadir';
                            $empData['status_class'] = 'success';
                        } elseif ($totalMinutes >= 300) { // 5 jam
                            $empData['status'] = 'Kurang Jam';
                            $empData['status_class'] = 'warning';
                        } else {
                            $empData['status'] = 'Jam Tidak Cukup';
                            $empData['status_class'] = 'danger';
                        }
                    } else {
                        // Hanya check in
                        $empData['status'] = 'Hanya Check In';
                        $empData['status_class'] = 'warning';
                    }

                    // Multiple records
                    if (count($records) > 2) {
                        $empData['status'] = 'Multiple Records (' . count($records) . ')';
                        $empData['status_class'] = 'info';
                    }
                }

                $processedData[] = $empData;
            }

            // =============================
            // 7. Sort by badge
            // =============================
            usort($processedData, fn ($a, $b) => strcmp($a['badge_no'], $b['badge_no']));
            foreach ($processedData as $i => $row) {
                $processedData[$i]['no'] = $i + 1;
            }

            // =============================
            // 8. Statistik
            // =============================
            $totalWorkHours = array_sum(array_column($processedData, 'work_minutes'));
            $avgHours = count($processedData) > 0 ? floor($totalWorkHours / count($processedData) / 60) : 0;
            $avgMinutes = count($processedData) > 0 ? floor(($totalWorkHours / count($processedData)) % 60) : 0;

            $stats = [
                'total_employees' => count($allEmployees),
                'present'         => count(array_filter($processedData, fn ($d) => $d['status'] === 'Hadir')),
                'absent'          => count(array_filter($processedData, fn ($d) => $d['status'] === 'Tidak Masuk')),
                'late'            => count(array_filter($processedData, fn ($d) => $d['status'] === 'Kurang Jam')),
                'total_hours'     => floor($totalWorkHours / 60) . ' jam ' . ($totalWorkHours % 60) . ' menit',
                'avg_hours'       => sprintf("%d:%02d", $avgHours, $avgMinutes)
            ];

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data presensi berhasil diambil dari database',
                'data'    => $processedData,
                'total'   => count($processedData),
                'date'    => $date,
                'stats'   => $stats
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => [],
                'total'   => 0
            ]);
        }
    }

    private function getAttendanceType($status)
    {
        $types = [
            '0'   => 'Check In',
            '1'   => 'Check Out',
            '255' => 'Overtime In',
            '256' => 'Overtime Out'
        ];

        return $types[$status] ?? 'Unknown';
    }

    public function pullFromFingerprint()
    {
        // WAJIB buat proses besar
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $companyId = $this->request->getVar('company_id');
        $date      = $this->request->getVar('date');

        if (!$companyId || !$date) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Company ID dan Tanggal harus diisi'
            ]);
        }

        try {
            /* ===============================
            * COMPANY
            * =============================== */
            $company = $this->hrOutSourcingCompanyModel
                ->where('id', $companyId)
                ->where('deletedAt', null)
                ->first();

            if (!$company) {
                throw new \Exception('Perusahaan tidak ditemukan');
            }

            /* ===============================
            * DEVICE
            * =============================== */
            $device = $this->AttendancesUnitOutsourceModel
                ->where('id', $company['ip_finger'])
                ->first();

            if (!$device) {
                throw new \Exception('Device fingerprint tidak ditemukan');
            }

            /* ===============================
            * PULL FROM MACHINE
            * =============================== */
            $attendanceData = $this->Attendance->getAttendanceFromDevice(
                $device['ip'],
                0,
                $date
            );

            if (empty($attendanceData)) {
                return $this->response->setJSON([
                    'success'       => true,
                    'message'       => 'Tidak ada data dari mesin',
                    'from_machine'  => 0,
                    'inserted'      => 0
                ]);
            }

            /* ===============================
            * PREPARE BATCH DATA
            * =============================== */
            $batchData = [];

            foreach ($attendanceData as $att) {
                $batchData[] = [
                    'employee_id'       => (int) $att['pin'],
                    'attendance_unit'  => $device['id'],
                    'datetime'          => $att['datetime'],
                ];
            }

            /* ===============================
            * INSERT BATCH (CHUNK)
            * =============================== */
            $chunkSize = 500; // AMAN buat DB & mesin
            $chunks    = array_chunk($batchData, $chunkSize);

            $inserted = 0;

            foreach ($chunks as $chunk) {
                $this->AttendancesLogOutsourcingModel->insertBatch($chunk);
                $inserted += count($chunk);
            }

            return $this->response->setJSON([
                'success'       => true,
                'message'       => 'Data berhasil ditarik dari mesin',
                'from_machine'  => count($attendanceData),
                'inserted'      => $inserted
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Function untuk mengambil semua karyawan dari perusahaan
    public function getAllEmployees($companyId)
    {
        try {
            $employees = $this->hrOutSourcingEmployeModel
                ->where('company_id', $companyId)
                ->where('deletedAt', NULL)
                ->findAll();
                
            return $this->response->setJSON([
                'success' => true,
                'data' => $employees,
                'total' => count($employees)
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => [],
                'total' => 0
            ]);
        }
    }

    public function getCompanyByDivisi($divisiId) 
    {
        $company = $this->hrOutSourcingCompanyModel
                    ->where('divisi_id', $divisiId)
                    ->where('deletedAt', NULL)
                    ->findAll();

        return $this->response->setJSON($company);
    }

    public function generatePayrollPdf()
    {
        try {
            // ===============================
            // 1. AMBIL PARAMETER (SINGLE DATE)
            // ===============================
            $companyId = $this->request->getPost('company_id');
            $date      = $this->request->getPost('date');    // Changed from start_date
            $period    = $this->request->getPost('period'); // 1-15 / 16-31
            $type      = $this->request->getPost('type');   // harian / minggu

            if (!$companyId || !$date || !$period) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parameter tidak lengkap'
                ]);
            }

            // ===============================
            // 2. DATA COMPANY
            // ===============================
            $company = $this->hrOutSourcingCompanyModel
                ->where('id', $companyId)
                ->where('deletedAt', null)
                ->first();

            if (!$company) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Perusahaan tidak ditemukan'
                ]);
            }

            // ===============================
            // 3. DATA KARYAWAN
            // ===============================
            $allEmployees = $this->hrOutSourcingEmployeModel
                ->where('company_id', $companyId)
                ->findAll();

            if (empty($allEmployees)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada karyawan'
                ]);
            }

            // ===============================
            // 4. AMBIL ABSENSI DARI DATABASE (SINGLE DATE)
            // ===============================
            $selectedDate = date('Y-m-d', strtotime($date));
            
            $rawAttendance = $this->AttendancesLogOutsourcingModel
                ->select('
                    attendance_log_outsource.employee_id AS pin,
                    attendance_log_outsource.datetime,
                    hr_outsourcing_employee.nama,
                    hr_outsourcing_employee.badge,
                    DATE(attendance_log_outsource.datetime) as attendance_date
                ')
                ->join(
                    'attendance_unit_outsource',
                    'attendance_unit_outsource.id = attendance_log_outsource.attendance_unit',
                    'left'
                )
                ->join(
                    'hr_outsourcing_employee',
                    'hr_outsourcing_employee.id = attendance_log_outsource.employee_id',
                    'left'
                )
                ->where('hr_outsourcing_employee.company_id', $companyId)
                ->where('DATE(attendance_log_outsource.datetime)', $selectedDate)
                ->orderBy('attendance_log_outsource.datetime', 'ASC')
                ->findAll();

            // ===============================
            // 5. FORMAT DATA ABSENSI (ADAPTER)
            // ===============================
            $formattedAttendance = array_map(function ($row) {
                return [
                    'pin'      => (string) $row['pin'],
                    'datetime' => $row['datetime'],
                    'status'   => '0',   // default status
                    'verified' => '1',   // trusted dari DB
                    'date'     => $row['attendance_date']
                ];
            }, $rawAttendance);

            // ===============================
            // 6. GROUP ABSENSI PER KARYAWAN
            // ===============================
            $employeeAttendanceMap = [];

            foreach ($formattedAttendance as $att) {
                $pin = (int)$att['pin'];
                
                if ($pin <= 0) continue;

                if (!isset($employeeAttendanceMap[$pin])) {
                    $employeeAttendanceMap[$pin] = [];
                }

                $employeeAttendanceMap[$pin][] = [
                    'datetime'  => $att['datetime'],
                    'timestamp' => strtotime($att['datetime']),
                    'verified'  => (int)$att['verified'],
                    'status'    => (int)$att['status'],
                    'workcode'  => 0,
                    'date'      => $att['date']
                ];
            }

            // Sort log per karyawan
            foreach ($employeeAttendanceMap as $pin => $records) {
                usort($employeeAttendanceMap[$pin], fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);
            }

            // ===============================
            // 7. PROSES GAJI UNTUK SETIAP KARYAWAN
            // ===============================
            $processedEmployees = [];

            foreach ($allEmployees as $employee) {
                $employeeId = (int)$employee['id'];
                
                // Default: Tidak masuk
                $employeeAttendanceDays = [];
                $workDays = 0;

                // Cek apakah karyawan ini punya absensi
                if (isset($employeeAttendanceMap[$employeeId]) && !empty($employeeAttendanceMap[$employeeId])) {
                    $records = $employeeAttendanceMap[$employeeId];
                    
                    // Ambil check in pertama dan check out terakhir
                    $firstRecord = $records[0];
                    $lastRecord = end($records);
                    
                    // Cari check out dengan status = 1 (pulang) jika ada
                    $finalCheckOut = $lastRecord['datetime'];
                    foreach ($records as $r) {
                        if ($r['status'] === 1) {
                            $finalCheckOut = $r['datetime'];
                            break;
                        }
                    }

                    $employeeAttendanceDays[$selectedDate] = [
                        'check_in'  => $firstRecord['datetime'],
                        'check_out' => $finalCheckOut,
                        'records'   => $records,
                        'date'      => $selectedDate
                    ];

                    $workDays = 1; // Hanya 1 hari untuk single date
                }

                // Hitung gaji (meskipun tidak masuk, untuk ditampilkan)
                $calculations = $this->calculateSalary($employee, $employeeAttendanceDays, $type, $period);
                
                $processedEmployees[] = [
                    'employee'     => $employee,
                    'attendance'   => $employeeAttendanceDays,
                    'work_days'    => $workDays,
                    'calculations' => $calculations,
                    'attendance_status' => empty($employeeAttendanceDays) ? 'Tidak Masuk' : 'Hadir'
                ];
            }

            // ===============================
            // 8. GENERATE PDF
            // ===============================
            $html = $this->generateHtmlContent(
                $processedEmployees,
                $period,
                $type,
                $date, // Single date
                $date, // Start dan end sama
                $company
            );

            $pdfContent = $this->generatePdf($html);

            // ===============================
            // 9. RESPONSE
            // ===============================
            return $this->response->setJSON([
                'success'          => true,
                'data'             => base64_encode($pdfContent),
                'filename'         => "Payroll_{$company['name']}_{$selectedDate}_{$type}_" . date('Ymd_His') . ".pdf",
                'total_employees'  => count($processedEmployees),
                'date'             => $selectedDate,
                'present_count'    => count(array_filter($processedEmployees, fn($e) => !empty($e['attendance']))),
                'absent_count'     => count(array_filter($processedEmployees, fn($e) => empty($e['attendance'])))
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * PERHITUNGAN GAJI YANG LEBIH DETAIL
     */
    private function calculateSalary($employee, $attendanceDays, $type, $period)
    {
        $result = [
            'total_hours' => 0,
            'regular_hours' => 0,
            'overtime_hours' => 0,
            'longshift_hours' => 0,
            'regular_salary' => 0,
            'overtime_salary' => 0,
            'longshift_salary' => 0,
            'total_salary' => 0,
            'daily_rate' => 0,
            'overtime_rate' => 0,
            'longshift_rate' => 0,
            'work_days' => count($attendanceDays),
            'daily_details' => [] // Detail per hari
        ];

        // Tentukan rate berdasarkan jenis (harian/minggu)
        if ($type === 'harian') {
            // GAJI HARIAN (PER 15 HARI)
            $daysInPeriod = $this->getWorkingDaysInPeriod($period);
            
            // Rate sesuai permintaan
            $hourlyRate = 67166.67 / 8; // = 8,395.83375 per jam
            $dailyRate = 67166.67; // Rate harian penuh
            $overtimeRate = $hourlyRate * 1.5; // Lembur 1.5x
            $longshiftRate = 120000.00; // Longshift flat
            
        } else {
            // GAJI MINGGUAN
            $hourlyRate = 15000.00;
            $dailyRate = 120000.00;
            $overtimeRate = $hourlyRate * 1.5; // 22,500 per jam
            $longshiftRate = 120000.00; // Longshift flat
        }

        $result['daily_rate'] = $dailyRate;
        $result['overtime_rate'] = $overtimeRate;
        $result['longshift_rate'] = $longshiftRate;

        // Hitung jam kerja untuk setiap hari hadir
        foreach ($attendanceDays as $date => $dayData) {
            if (isset($dayData['check_in']) && isset($dayData['check_out'])) {
                $hours = $this->calculateDailyHours($dayData);
                
                $dailyDetail = [
                    'date' => $date,
                    'check_in' => $dayData['check_in'],
                    'check_out' => $dayData['check_out'],
                    'total_hours' => $hours,
                    'regular_hours' => 0,
                    'overtime_hours' => 0,
                    'is_longshift' => false
                ];
                
                if ($hours > 0) {
                    if ($hours <= 8) {
                        $regularHours = $hours;
                        $overtimeHours = 0;
                    } else {
                        $regularHours = 8;
                        $overtimeHours = $hours - 8;
                    }
                    
                    $dailyDetail['regular_hours'] = $regularHours;
                    $dailyDetail['overtime_hours'] = $overtimeHours;
                    
                    $result['regular_hours'] += $regularHours;
                    $result['regular_salary'] += ($regularHours * $hourlyRate);
                    
                    if ($overtimeHours > 0) {
                        $result['overtime_hours'] += $overtimeHours;
                        $result['overtime_salary'] += ($overtimeHours * $overtimeRate);
                    }
                    
                    // Cek apakah longshift (kerja > 14 jam total)
                    if ($hours > 14) {
                        $dailyDetail['is_longshift'] = true;
                        $result['longshift_hours'] += 1;
                        $result['longshift_salary'] += $longshiftRate;
                    }
                }
                
                $result['daily_details'][] = $dailyDetail;
            }
        }

        // Total semua
        $result['total_hours'] = $result['regular_hours'] + $result['overtime_hours'];
        $result['total_salary'] = $result['regular_salary'] + $result['overtime_salary'] + $result['longshift_salary'];

        return $result;
    }

    /**
     * HITUNG JAM KERJA HARIAN DARI CHECK-IN DAN CHECK-OUT
     * (Diperbaiki untuk menghitung dengan benar)
     */
    private function calculateDailyHours($dayData)
    {
        if (empty($dayData['check_in']) || empty($dayData['check_out'])) {
            return 0;
        }
        
        $checkIn = strtotime($dayData['check_in']);
        $checkOut = strtotime($dayData['check_out']);
        
        if ($checkIn >= $checkOut) {
            return 0;
        }
        
        $diff = $checkOut - $checkIn;
        $hours = $diff / 3600;
        
        // Kurangi istirahat 1 jam jika kerja > 6 jam
        if ($hours > 6) {
            $hours -= 1;
        }
        
        return round($hours, 2);
    }

    /**
     * GENERATE HTML CONTENT UNTUK PDF (DIPERBAIKI)
     */
    private function generateHtmlContent($data, $period, $type, $startDate, $endDate, $company)
    {
        $periodText = $period === '1-15' ? '1-15' : '16-' . date('t', strtotime($startDate));
        $title = $type === 'harian' ? "SLIP GAJI HARIAN PERIODE {$periodText}" : "SLIP GAJI MINGGUAN";
        
        // WARNING: Jika data terlalu banyak, buat per page
        $perPage = 30;
        $totalPages = ceil(count($data) / $perPage);
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Penggajian</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 20px; }
                .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
                .header h2 { margin: 0; padding: 0; color: #333; }
                .header p { margin: 3px 0; padding: 0; }
                .company-info { text-align: left; margin-bottom: 15px; padding: 10px; background-color: #f9f9f9; border-radius: 5px; border: 1px solid #ddd; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
                table, th, td { border: 1px solid #000; }
                th, td { padding: 4px 6px; text-align: center; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .text-left { text-align: left; }
                .text-right { text-align: right; }
                .total-row { font-weight: bold; background-color: #e8e8e8; }
                .employee-row:hover { background-color: #f5f5f5; }
                .signature { width: 100%; margin-top: 20px; }
                .signature td { border: none; text-align: center; padding-top: 40px; }
                .summary-box { background-color: #f0f8ff; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #ccc; }
                .summary-item { display: inline-block; margin-right: 20px; }
                .summary-label { font-weight: bold; }
                .page-break { page-break-after: always; }
                .page-number { text-align: center; margin-top: 10px; font-size: 9px; color: #666; }
                @media print {
                    body { font-size: 9px; padding: 10px; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>';

        // Jika banyak data, buat per page
        if ($totalPages > 1) {
            for ($page = 0; $page < $totalPages; $page++) {
                $start = $page * $perPage;
                $pageData = array_slice($data, $start, $perPage);
                
                $html .= $this->generatePageContent(
                    $pageData, 
                    $title, 
                    $company, 
                    $startDate, 
                    $endDate, 
                    $page + 1, 
                    $totalPages,
                    ($page == 0) // first page
                );
                
                if ($page < $totalPages - 1) {
                    $html .= '<div class="page-break"></div>';
                }
            }
        } else {
            $html .= $this->generatePageContent(
                $data, 
                $title, 
                $company, 
                $startDate, 
                $endDate, 
                1, 
                1,
                true
            );
        }

        $html .= '</body></html>';

        return $html;
    }

    /**
     * GENERATE CONTENT PER PAGE
     */
    private function generatePageContent($pageData, $title, $company, $startDate, $endDate, $currentPage, $totalPages, $isFirstPage = false)
    {
        $html = '';
        
        if ($isFirstPage) {
            // HEADER hanya di page pertama
            $html .= '<div class="header">
                <h2>' . $title . '</h2>
                <p>Perusahaan: <strong>' . ($company['name'] ?? 'N/A') . '</strong></p>
                <p>Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) . '</p>
                <p>Tanggal Cetak: ' . date('d/m/Y H:i:s') . '</p>
            </div>';

            // COMPANY INFO hanya di page pertama
            $html .= '<div class="company-info">
                <p><strong>Informasi Perusahaan:</strong></p>
                <p>Nama: ' . ($company['name'] ?? 'N/A') . '</p>
                <p>Alamat: ' . ($company['address'] ?? 'N/A') . '</p>
                <p>Telepon: ' . ($company['phone'] ?? 'N/A') . '</p>
            </div>';

            // SUMMARY hanya di page pertama
            $totalEmployees = count($pageData) * $totalPages; // Estimasi
            $totalRegularHours = array_sum(array_column(array_column($pageData, 'calculations'), 'regular_hours'));
            $totalOvertimeHours = array_sum(array_column(array_column($pageData, 'calculations'), 'overtime_hours'));
            $totalSalary = array_sum(array_column(array_column($pageData, 'calculations'), 'total_salary'));
            $totalWorkDays = array_sum(array_column(array_column($pageData, 'calculations'), 'work_days'));
            
            $html .= '<div class="summary-box">
                <div class="summary-item"><span class="summary-label">Jumlah Karyawan:</span> ' . $totalEmployees . '</div>
                <div class="summary-item"><span class="summary-label">Total Hari Kerja:</span> ' . $totalWorkDays . '</div>
                <div class="summary-item"><span class="summary-label">Total Jam Kerja:</span> ' . number_format($totalRegularHours + $totalOvertimeHours, 2) . ' jam</div>
                <div class="summary-item"><span class="summary-label">Total Gaji:</span> Rp ' . number_format($totalSalary, 2) . '</div>
            </div>';
        }

        // TABEL GAJI untuk page ini
        $html .= '<table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>BADGE</th>
                    <th>NAMA KARYAWAN</th>
                    <th>HARI KERJA</th>
                    <th>JAM NORMAL</th>
                    <th>JAM LEMBUR</th>
                    <th>LONGSHIFT</th>
                    <th>GAJI NORMAL</th>
                    <th>GAJI LEMBUR</th>
                    <th>GAJI LONGSHIFT</th>
                    <th>TOTAL GAJI</th>
                </tr>
            </thead>
            <tbody>';

        $no = (($currentPage - 1) * 30) + 1;
        $pageTotalHours = 0;
        $pageTotalSalary = 0;

        foreach ($pageData as $item) {
            $calc = $item['calculations'];
            $emp = $item['employee'];
            
            $html .= '<tr class="employee-row">
                <td>' . $no++ . '</td>
                <td>' . ($emp['badge'] ?? $emp['badge_no'] ?? $emp['id'] ?? '-') . '</td>
                <td class="text-left">' . $emp['nama'] . '</td>
                <td>' . $calc['work_days'] . '</td>
                <td>' . number_format($calc['regular_hours'], 2) . '</td>
                <td>' . number_format($calc['overtime_hours'], 2) . '</td>
                <td>' . number_format($calc['longshift_hours'], 0) . '</td>
                <td class="text-right">Rp ' . number_format($calc['regular_salary'], 2) . '</td>
                <td class="text-right">Rp ' . number_format($calc['overtime_salary'], 2) . '</td>
                <td class="text-right">Rp ' . number_format($calc['longshift_salary'], 2) . '</td>
                <td class="text-right"><strong>Rp ' . number_format($calc['total_salary'], 2) . '</strong></td>
            </tr>';

            $pageTotalHours += $calc['total_hours'];
            $pageTotalSalary += $calc['total_salary'];
        }

        // TOTAL ROW untuk page ini
        if (!empty($pageData)) {
            $pageRegularHours = array_sum(array_column(array_column($pageData, 'calculations'), 'regular_hours'));
            $pageOvertimeHours = array_sum(array_column(array_column($pageData, 'calculations'), 'overtime_hours'));
            $pageWorkDays = array_sum(array_column(array_column($pageData, 'calculations'), 'work_days'));
            $pageLongshift = array_sum(array_column(array_column($pageData, 'calculations'), 'longshift_hours'));
            $pageRegularSalary = array_sum(array_column(array_column($pageData, 'calculations'), 'regular_salary'));
            $pageOvertimeSalary = array_sum(array_column(array_column($pageData, 'calculations'), 'overtime_salary'));
            $pageLongshiftSalary = array_sum(array_column(array_column($pageData, 'calculations'), 'longshift_salary'));
            
            $html .= '<tr class="total-row">
                <td colspan="3"><strong>PAGE TOTAL (Halaman ' . $currentPage . ')</strong></td>
                <td><strong>' . $pageWorkDays . '</strong></td>
                <td><strong>' . number_format($pageRegularHours, 2) . '</strong></td>
                <td><strong>' . number_format($pageOvertimeHours, 2) . '</strong></td>
                <td><strong>' . number_format($pageLongshift, 0) . '</strong></td>
                <td class="text-right"><strong>Rp ' . number_format($pageRegularSalary, 2) . '</strong></td>
                <td class="text-right"><strong>Rp ' . number_format($pageOvertimeSalary, 2) . '</strong></td>
                <td class="text-right"><strong>Rp ' . number_format($pageLongshiftSalary, 2) . '</strong></td>
                <td class="text-right"><strong>Rp ' . number_format($pageTotalSalary, 2) . '</strong></td>
            </tr>';
        }

        $html .= '</tbody></table>';

        // Page number
        $html .= '<div class="page-number">Halaman ' . $currentPage . ' dari ' . $totalPages . '</div>';

        // FOOTER & SIGNATURE hanya di halaman terakhir
        if ($currentPage == $totalPages) {
            $html .= '<div style="margin-top: 20px; padding: 15px; background-color: #e6f7ff; border: 2px solid #1890ff; border-radius: 5px;">
                <h3 style="margin: 0; color: #0050b3; text-align: center;">
                    TOTAL SELURUH KARYAWAN: Rp ' . number_format($pageTotalSalary, 2) . '
                </h3>
                <p style="text-align: center; margin: 5px 0 0 0;">*Termasuk jam normal, lembur, dan longshift</p>
            </div>';

            $html .= '<table class="signature">
                <tr>
                    <td>KARYAWAN</td>
                    <td>HRD</td>
                    <td>MANAJER</td>
                    <td>DIREKTUR</td>
                </tr>
                <tr>
                    <td style="padding-top: 60px;">(___________________)</td>
                    <td style="padding-top: 60px;">(___________________)</td>
                    <td style="padding-top: 60px;">(___________________)</td>
                    <td style="padding-top: 60px;">(___________________)</td>
                </tr>
                <tr>
                    <td>Nama & Tanda Tangan</td>
                    <td>Verifikasi HRD</td>
                    <td>Persetujuan Manager</td>
                    <td>Persetujuan Direktur</td>
                </tr>
            </table>';
        }

        return $html;
    }

    /**
     * GENERATE PDF DARI HTML (DOMpdf)
     */
    private function generatePdf($html)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('isPhpEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        return $dompdf->output();
    }

    /**
     * GET WORKING DAYS IN PERIOD
     */
    private function getWorkingDaysInPeriod($period)
    {
        if ($period === '1-15') {
            return 15;
        } else {
            $currentMonth = date('m');
            $currentYear = date('Y');
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
            return $daysInMonth - 15;
        }
    }

}
