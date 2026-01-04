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
use App\Models\MetadataModel;
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
    protected $metaDataModel;
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
        $this->metadataModel = new MetadataModel();
    }

    public function index()
    {
        $data = [
            "dataDivisi" => $this->divisiModel->getDivisiAccess(),
            "metadata" => $this->metadataModel->where('name', "tipe_karyawan_outsource")->where('deletedAt', NULL)->findAll(),
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
                    'work_minutes'     => 0,      // Total menit (setelah pembulatan)
                    'decimal_hours'    => 0,      // Jam desimal (setelah pembulatan)
                    'verified_in'      => 'Not Verified',
                    'verified_out'     => 'Not Verified',
                    'status'           => 'Tidak Masuk',
                    'status_class'     => 'danger',
                    'company_name'     => $company['name'],
                    'company_id'       => $companyId,
                    'attendance_count' => 0,
                    'check_in_rounded' => '00:00 → 0.00',
                    'check_out_rounded'=> '00:00 → 0.00'
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

                        // HITUNG TOTAL JAM KERJA DENGAN RUMUS PEMBULATAN SS
                        $checkInStr = $firstRecord['datetime'];
                        $checkOutStr = $lastRecord['datetime'];
                        
                        // Gunakan rumus pembulatan SS
                        $hoursCalculation = $this->calculateHoursWithRounding($checkInStr, $checkOutStr);
                        
                        $empData['work_minutes'] = $hoursCalculation['total_minutes'];
                        $empData['work_hours'] = $hoursCalculation['formatted'];
                        $empData['decimal_hours'] = $hoursCalculation['decimal_hours'];
                        $empData['check_in_rounded'] = date('H:i', strtotime($checkInStr)) . ' → ' . number_format($hoursCalculation['jam_masuk_decimal'], 2);
                        $empData['check_out_rounded'] = date('H:i', strtotime($checkOutStr)) . ' → ' . number_format($hoursCalculation['jam_keluar_decimal'], 2);

                        // Tentukan status berdasarkan jam kerja (dengan pembulatan)
                        // Konversi jam desimal ke menit untuk pengecekan
                        $roundedMinutes = $empData['work_minutes'];
                        
                        if ($roundedMinutes >= 480) { // 8 jam = 480 menit
                            $empData['status'] = 'Hadir';
                            $empData['status_class'] = 'success';
                        } elseif ($roundedMinutes >= 300) { // 5 jam = 300 menit
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
                        
                        // Hitung jam masuk saja (untuk display)
                        $checkInStr = $firstRecord['datetime'];
                        $jamMasukDecimal = $this->convertJamMasuk($this->formatToHourMinute($checkInStr));
                        $empData['check_in_rounded'] = date('H:i', strtotime($checkInStr)) . ' → ' . number_format($jamMasukDecimal, 2);
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
            // 8. Statistik (GUNAKAN WORK_MINUTES YANG SUDAH DIBULATKAN)
            // =============================
            $totalWorkMinutes = array_sum(array_column($processedData, 'work_minutes'));
            $avgHours = count($processedData) > 0 ? floor($totalWorkMinutes / count($processedData) / 60) : 0;
            $avgMinutes = count($processedData) > 0 ? floor(($totalWorkMinutes / count($processedData)) % 60) : 0;

            $stats = [
                'total_employees' => count($allEmployees),
                'present'         => count(array_filter($processedData, fn ($d) => $d['status'] === 'Hadir')),
                'absent'          => count(array_filter($processedData, fn ($d) => $d['status'] === 'Tidak Masuk')),
                'late'            => count(array_filter($processedData, fn ($d) => $d['status'] === 'Kurang Jam')),
                'total_hours'     => floor($totalWorkMinutes / 60) . ' jam ' . ($totalWorkMinutes % 60) . ' menit',
                'avg_hours'       => sprintf("%d:%02d", $avgHours, $avgMinutes),
                'note'            => 'Perhitungan menggunakan rumus pembulatan SS'
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

    // ============================================================================
    // HELPER FUNCTIONS UNTUK RUMUS PEMBULATAN SS
    // ============================================================================

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

    /**
     * Untuk JAM KELUAR (Pulang) - rumus dari "Jam kelvar"
     * @param string $timeFormat HH:MM
     * @return float Jam dalam desimal
     */
    /**
     * Untuk JAM KELUAR (Pulang) - rumus dari "Jam kelvar" YANG BENAR
     * @param string $timeFormat HH:MM
     * @return float Jam dalam desimal (DENGAN PEMBULATAN JAM)
     */
    private function convertJamKeluar($timeFormat)
    {
        $timeParts = explode(':', $timeFormat);
        if (count($timeParts) < 2) return 0;
        
        $hours = (int)$timeParts[0];
        $minutes = (int)$timeParts[1];
        
        // Logika pembulatan menit sesuai SS, dan JAM ikut berubah!
        if ($minutes >= 45 && $minutes <= 60) {
            return $hours + 0.75;  // Contoh: 18:49 → 18.75
        } elseif ($minutes >= 30 && $minutes <= 44) {
            return $hours + 0.5;   // Contoh: 18:40 → 18.5
        } elseif ($minutes >= 15 && $minutes <= 29) {
            return $hours + 0.25;  // Contoh: 18:17 → 18.25
        } else { // 0-14 menit
            return $hours + 0;     // Contoh: 18:09 → 18.00
        }
    }

    /**
     * Untuk JAM MASUK - rumus dari "Jam masuke" YANG BENAR
     * @param string $timeFormat HH:MM
     * @return float Jam dalam desimal (DENGAN PEMBULATAN JAM)
     */
    private function convertJamMasuk($timeFormat)
    {
        $timeParts = explode(':', $timeFormat);
        if (count($timeParts) < 2) return 0;
        
        $hours = (int)$timeParts[0];
        $minutes = (int)$timeParts[1];
        
        // Logika pembulatan menit (ASIMETRIS) sesuai SS, JAM ikut berubah!
        if ($minutes >= 46 && $minutes <= 60) {
            return $hours + 1;     // Contoh: 08:46 → 9.00 (8 + 1)
        } elseif ($minutes >= 31 && $minutes <= 45) {
            return $hours + 0.75;  // Contoh: 08:31 → 8.75
        } elseif ($minutes >= 15 && $minutes <= 30) {
            return $hours + 0.5;   // Contoh: 08:15 → 8.50
        } else { // 0-14 menit
            return $hours + 0.25;  // Contoh: 08:07 → 8.25
        }
    }

    /**
     * Konversi format 24 jam (H:i:s) ke format HH:MM
     */
    private function formatToHourMinute($timeString)
    {
        $time = strtotime($timeString);
        if (!$time) return '00:00';
        return date('H:i', $time);
    }

    /**
     * Hitung jam kerja dengan rumus pembulatan SS
     * @param string $checkIn Waktu check-in (format H:i:s)
     * @param string $checkOut Waktu check-out (format H:i:s)
     * @return array [total_jam_desimal, total_menit_aktual]
     */
    private function calculateHoursWithRounding($checkIn, $checkOut)
    {
        // Validasi input
        if (empty($checkIn) || empty($checkOut)) {
            return [
                'decimal_hours' => 0,
                'total_minutes' => 0,
                'formatted' => "0:00",
                'jam_masuk_decimal' => 0,
                'jam_keluar_decimal' => 0
            ];
        }
        
        // Format ke HH:MM
        $checkInFormatted = $this->formatToHourMinute($checkIn);
        $checkOutFormatted = $this->formatToHourMinute($checkOut);
        
        // Konversi dengan rumus SS
        $jamMasukDecimal = $this->convertJamMasuk($checkInFormatted);
        $jamKeluarDecimal = $this->convertJamKeluar($checkOutFormatted);
        
        // Hitung selisih jam desimal
        $totalDecimalHours = $jamKeluarDecimal - $jamMasukDecimal;
        
        // Pastikan tidak negatif
        if ($totalDecimalHours < 0) {
            $totalDecimalHours = 0;
        }
        
        // Konversi ke menit untuk reporting
        $totalMinutes = round($totalDecimalHours * 60);
        
        // Format jam:menit untuk display
        $hours = floor($totalDecimalHours);
        $minutes = round(($totalDecimalHours - $hours) * 60);
        
        return [
            'decimal_hours' => $totalDecimalHours,
            'total_minutes' => $totalMinutes,
            'formatted' => sprintf("%d:%02d", $hours, $minutes),
            'jam_masuk_decimal' => $jamMasukDecimal,
            'jam_keluar_decimal' => $jamKeluarDecimal
        ];
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
            // 1. AMBIL PARAMETER
            // ===============================
            $companyId = $this->request->getPost('company_id');
            $date      = $this->request->getPost('date');    // Tanggal referensi untuk ambil bulan
            $period    = $this->request->getPost('period'); // 1-15 / 16-31
            $type      = $this->request->getPost('type');   // harian / minggu

            if (!$companyId || !$date || !$period || !$type) {
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
            // 3. TENTUKAN TANGGAL PERIODE BERDASARKAN BULAN DARI $date
            // ===============================
            $referenceDate = strtotime($date);
            $year = date('Y', $referenceDate);
            $month = date('m', $referenceDate);
            
            // Tentukan start dan end date berdasarkan periode
            if ($period === '1-15') {
                $startDate = $year . '-' . $month . '-01';
                $endDate = $year . '-' . $month . '-15';
            } else {
                // Periode 16-akhir bulan
                $startDate = $year . '-' . $month . '-16';
                $lastDay = date('t', strtotime($startDate)); // Tanggal terakhir bulan
                $endDate = $year . '-' . $month . '-' . $lastDay;
            }

            // ===============================
            // 4. DATA KARYAWAN BERDASARKAN TIPE
            // ===============================
            // Perhatikan: $type dari parameter harus sesuai dengan nilai di database
            // Misal: $type = 'harian' atau $type = 'minggu' 
            $allEmployees = $this->hrOutSourcingEmployeModel
                ->where('tipe_karyawan', $type)
                // ->where('company_id', $companyId)
                ->where('deletedAt', NULL)
                ->findAll();

            // var_dump($allEmployees);
            // die;

            // ===============================
            // 5. AMBIL ABSENSI DARI DATABASE (SELURUH PERIODE)
            // ===============================
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
                ->where('hr_outsourcing_employee.tipe_karyawan', $type)
                ->where("DATE(attendance_log_outsource.datetime) >= '{$startDate}'")
                ->where("DATE(attendance_log_outsource.datetime) <= '{$endDate}'")
                ->orderBy('attendance_log_outsource.datetime', 'ASC')
                ->findAll();

            // ===============================
            // 6. FORMAT DATA ABSENSI (ADAPTER)
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
            // 7. GROUP ABSENSI PER KARYAWAN PER TANGGAL
            // ===============================
            $employeeAttendanceMap = [];

            foreach ($formattedAttendance as $att) {
                $pin = (int)$att['pin'];
                $attDate = $att['date'];
                
                if ($pin <= 0) continue;

                if (!isset($employeeAttendanceMap[$pin])) {
                    $employeeAttendanceMap[$pin] = [];
                }

                if (!isset($employeeAttendanceMap[$pin][$attDate])) {
                    $employeeAttendanceMap[$pin][$attDate] = [];
                }

                $employeeAttendanceMap[$pin][$attDate][] = [
                    'datetime'  => $att['datetime'],
                    'timestamp' => strtotime($att['datetime']),
                    'verified'  => (int)$att['verified'],
                    'status'    => (int)$att['status'],
                    'workcode'  => 0
                ];
            }

            // Sort log per tanggal per karyawan
            foreach ($employeeAttendanceMap as $pin => $dateMap) {
                foreach ($dateMap as $date => $records) {
                    usort($employeeAttendanceMap[$pin][$date], fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);
                }
            }

            // ===============================
            // 8. PROSES GAJI UNTUK SETIAP KARYAWAN (SELURUH PERIODE)
            // ===============================
            $processedEmployees = [];

            foreach ($allEmployees as $employee) {
                $employeeId = (int)$employee['id'];
                
                $employeeAttendanceDays = [];
                $workDays = 0;

                // Cek apakah karyawan ini punya absensi dalam periode ini
                if (isset($employeeAttendanceMap[$employeeId])) {
                    foreach ($employeeAttendanceMap[$employeeId] as $date => $records) {
                        if (!empty($records)) {
                            // Ambil check in pertama dan check out terakhir untuk tanggal ini
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

                            $employeeAttendanceDays[$date] = [
                                'check_in'  => $firstRecord['datetime'],
                                'check_out' => $finalCheckOut,
                                'records'   => $records,
                                'date'      => $date
                            ];

                            $workDays++; // Hitung hari kerja
                        }
                    }
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
            // 9. GENERATE PDF
            // ===============================
            $html = $this->generateHtmlContent(
                $processedEmployees,
                $period,
                $type,
                $startDate,
                $endDate,
                $company
            );

            $pdfContent = $this->generatePdf($html);

            // ===============================
            // 10. RESPONSE
            // ===============================
            return $this->response->setJSON([
                'success'          => true,
                'data'             => base64_encode($pdfContent),
                'filename'         => "Payroll_{$company['name']}_{$year}{$month}_{$period}_{$type}_" . date('Ymd_His') . ".pdf",
                'total_employees'  => count($processedEmployees),
                'period'           => $period,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'present_count'    => count(array_filter($processedEmployees, fn($e) => !empty($e['attendance']))),
                'absent_count'     => count(array_filter($processedEmployees, fn($e) => empty($e['attendance']))),
                'total_salary'     => array_sum(array_column(array_column($processedEmployees, 'calculations'), 'total_salary'))
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
            'work_days' => 0, // Hanya hari dengan check in & check out
            'valid_attendance_days' => 0, // Hari hadir yang valid (≥8 jam = hadir penuh)
            'partial_days' => 0, // Hari dengan jam kurang
            'daily_details' => [] // Detail per hari
        ];

        // Tentukan rate berdasarkan jenis (harian/minggu)
        if ($type === 'harian') {
            // GAJI HARIAN (PER 15 HARI)
            // Rate sesuai permintaan
            $hourlyRate = 67166.67 / 8; // = 8,395.83375 per jam
            $dailyRate = 67166.67; // Rate harian penuh
            $overtimeRate = $hourlyRate * 1.5; // Lembur 1.5x = 12,593.750625
            $longshiftRate = 120000.00; // Longshift flat
            
        } else if ($type === 'minggu') {
            // GAJI MINGGUAN
            $hourlyRate = 15000.00;
            $dailyRate = 120000.00; // 8 jam x 15,000
            $overtimeRate = $hourlyRate * 1.5; // 22,500 per jam
            $longshiftRate = 120000.00; // Longshift flat
            
        } else {
            // GAJI BULANAN (default)
            $hourlyRate = 20000.00;
            $dailyRate = 160000.00; // 8 jam x 20,000
            $overtimeRate = $hourlyRate * 1.5; // 30,000 per jam
            $longshiftRate = 150000.00; // Longshift flat
        }

        $result['daily_rate'] = $dailyRate;
        $result['overtime_rate'] = $overtimeRate;
        $result['longshift_rate'] = $longshiftRate;

        // Hitung jam kerja untuk setiap hari hadir
        foreach ($attendanceDays as $date => $dayData) {
            // HITUNG JAM KERJA HARIAN DENGAN RUMUS YANG SAMA DENGAN FE
            $dailyHours = $this->calculateDailyHours($dayData);
            
            if ($dailyHours <= 0) {
                continue; // Lewati hari tanpa jam kerja
            }
            
            $dailyDetail = [
                'date' => $date,
                'check_in' => $dayData['check_in'] ?? null,
                'check_out' => $dayData['check_out'] ?? null,
                'total_hours' => $dailyHours,
                'regular_hours' => 0,
                'overtime_hours' => 0,
                'is_longshift' => false,
                'is_valid_day' => false // Hari yang valid untuk gaji (≥8 jam)
            ];
            
            // PEMBAGIAN JAM REGULAR vs OVERTIME
            if ($dailyHours <= 8) {
                $regularHours = $dailyHours;
                $overtimeHours = 0;
            } else {
                $regularHours = 8;
                $overtimeHours = $dailyHours - 8;
            }
            
            $dailyDetail['regular_hours'] = $regularHours;
            $dailyDetail['overtime_hours'] = $overtimeHours;
            
            // TANDAI JIKA HARI INI VALID (HADIR PENUH = ≥8 JAM)
            if ($dailyHours >= 8) {
                $dailyDetail['is_valid_day'] = true;
                $result['valid_attendance_days']++;
            } else {
                $result['partial_days']++;
            }
            
            // HITUNG GAJI REGULAR
            $regularSalary = $regularHours * $hourlyRate;
            $result['regular_hours'] += $regularHours;
            $result['regular_salary'] += $regularSalary;
            
            // HITUNG GAJI OVERTIME
            if ($overtimeHours > 0) {
                $overtimeSalary = $overtimeHours * $overtimeRate;
                $result['overtime_hours'] += $overtimeHours;
                $result['overtime_salary'] += $overtimeSalary;
            }
            
            // CEK LONGSHIFT (KERJA > 14 JAM)
            if ($dailyHours > 14) {
                $dailyDetail['is_longshift'] = true;
                $result['longshift_hours'] += 1;
                $result['longshift_salary'] += $longshiftRate;
            }
            
            $result['daily_details'][] = $dailyDetail;
            $result['work_days']++; // Total hari dengan check in & check out
        }

        // TOTAL SEMUA JAM
        $result['total_hours'] = $result['regular_hours'] + $result['overtime_hours'];
        
        // PERHITUNGAN TOTAL GAJI BERDASARKAN JENIS GAJI
        if ($type === 'harian') {
            // GAJI HARIAN: Total berdasarkan jumlah hari valid
            $baseSalary = $result['valid_attendance_days'] * $dailyRate;
            $result['total_salary'] = $baseSalary + $result['overtime_salary'] + $result['longshift_salary'];
            
        } else if ($type === 'minggu') {
            // GAJI MINGGUAN: Total berdasarkan jam kerja
            $result['total_salary'] = $result['regular_salary'] + $result['overtime_salary'] + $result['longshift_salary'];
            
        } else {
            // GAJI BULANAN: Tetap berdasarkan jam kerja
            $result['total_salary'] = $result['regular_salary'] + $result['overtime_salary'] + $result['longshift_salary'];
        }

        return $result;
    }

    /**
     * HITUNG JAM KERJA HARIAN DARI CHECK-IN DAN CHECK-OUT
     * (SAMA DENGAN RUMUS DI FRONTEND)
     */
    private function calculateDailyHours($dayData)
    {
        if (empty($dayData['check_in']) || empty($dayData['check_out'])) {
            return 0;
        }
        
        // Format waktu dari database
        $checkIn = strtotime($dayData['check_in']);
        $checkOut = strtotime($dayData['check_out']);
        
        if ($checkIn === false || $checkOut === false || $checkIn >= $checkOut) {
            return 0;
        }
        
        // Hitung selisih dalam detik
        $diff = $checkOut - $checkIn;
        $hours = $diff / 3600; // Konversi ke jam
        
        // Format dengan 2 desimal
        return round($hours, 2);
    }

    /**
     * GENERATE HTML CONTENT UNTUK PDF (DIPERBAIKI)
     */
    private function generateHtmlContent($data, $period, $type, $startDate, $endDate, $company)
    {
        $periodText = $period === '1-15' ? '1-15' : '16-' . date('t', strtotime($startDate));
        $title = $type === 'harian' ? "SLIP GAJI HARIAN PERIODE {$periodText}" : 
                ($type === 'minggu' ? "SLIP GAJI MINGGUAN" : "SLIP GAJI BULANAN");
        
        // Hitung total keseluruhan
        $totalEmployees = count($data);
        $totalWorkDays = array_sum(array_column(array_column($data, 'calculations'), 'work_days'));
        $totalValidDays = array_sum(array_column(array_column($data, 'calculations'), 'valid_attendance_days'));
        $totalPartialDays = array_sum(array_column(array_column($data, 'calculations'), 'partial_days'));
        $totalRegularHours = array_sum(array_column(array_column($data, 'calculations'), 'regular_hours'));
        $totalOvertimeHours = array_sum(array_column(array_column($data, 'calculations'), 'overtime_hours'));
        $totalLongshift = array_sum(array_column(array_column($data, 'calculations'), 'longshift_hours'));
        $totalSalary = array_sum(array_column(array_column($data, 'calculations'), 'total_salary'));
        
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
                .rate-info { background-color: #fff8e1; padding: 8px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #ffd54f; }
                @media print {
                    body { font-size: 9px; padding: 10px; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>';

        // HEADER
        $html .= '<div class="header">
            <h2>' . $title . '</h2>
            <p>Perusahaan: <strong>' . ($company['name'] ?? 'N/A') . '</strong></p>
            <p>Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) . '</p>
            <p>Tanggal Cetak: ' . date('d/m/Y H:i:s') . '</p>
        </div>';

        // COMPANY INFO
        $html .= '<div class="company-info">
            <p><strong>Informasi Perusahaan:</strong></p>
            <p>Nama: ' . ($company['name'] ?? 'N/A') . '</p>
            <p>Alamat: ' . ($company['address'] ?? 'N/A') . '</p>
            <p>Telepon: ' . ($company['phone'] ?? 'N/A') . '</p>
        </div>';

        // INFORMASI RATE
        $hourlyRate = 0;
        $dailyRate = 0;
        $overtimeRate = 0;
        $longshiftRate = 0;
        
        if ($type === 'harian') {
            $hourlyRate = 67166.67 / 8;
            $dailyRate = 67166.67;
            $overtimeRate = $hourlyRate * 1.5;
            $longshiftRate = 120000.00;
            
            $html .= '<div class="rate-info">
                <p><strong>Rate Gaji Harian:</strong></p>
                <p>• Gaji Harian Penuh (8 jam): Rp ' . number_format($dailyRate, 2) . '</p>
                <p>• Rate per Jam: Rp ' . number_format($hourlyRate, 2) . '</p>
                <p>• Rate Lembur (1.5x): Rp ' . number_format($overtimeRate, 2) . ' per jam</p>
                <p>• Longshift (>14 jam): Rp ' . number_format($longshiftRate, 2) . ' flat per hari</p>
            </div>';
        }

        // SUMMARY TOTAL
        $html .= '<div class="summary-box">
            <h3 style="margin-top: 0; color: #0056b3;">RINGKASAN TOTAL</h3>
            <div class="summary-item"><span class="summary-label">Jumlah Karyawan:</span> ' . $totalEmployees . '</div>
            <div class="summary-item"><span class="summary-label">Total Hari Absen:</span> ' . $totalWorkDays . '</div>
            <div class="summary-item"><span class="summary-label">Hari Valid (≥8 jam):</span> ' . $totalValidDays . '</div>
            <div class="summary-item"><span class="summary-label">Hari Kurang Jam:</span> ' . $totalPartialDays . '</div>
            <div class="summary-item"><span class="summary-label">Total Jam Kerja:</span> ' . number_format($totalRegularHours + $totalOvertimeHours, 2) . ' jam</div>
            <div class="summary-item"><span class="summary-label">Total Longshift:</span> ' . $totalLongshift . ' hari</div>
            <div class="summary-item"><span class="summary-label">TOTAL GAJI:</span> <strong>Rp ' . number_format($totalSalary, 2) . '</strong></div>
        </div>';

        // TABEL DETAIL GAJI
        $html .= '<table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>BADGE</th>
                    <th>NAMA KARYAWAN</th>
                    <th>HARI ABSEN</th>
                    <th>HARI VALID</th>
                    <th>JAM NORMAL</th>
                    <th>JAM LEMBUR</th>
                    <th>LONGSHIFT</th>';
        
        if ($type === 'harian') {
            $html .= '<th>GAJI HARIAN</th>';
        } else {
            $html .= '<th>GAJI NORMAL</th>';
        }
        
        $html .= '<th>GAJI LEMBUR</th>
                    <th>GAJI LONGSHIFT</th>
                    <th>TOTAL GAJI</th>
                </tr>
            </thead>
            <tbody>';

        $no = 1;
        foreach ($data as $item) {
            $calc = $item['calculations'];
            $emp = $item['employee'];
            
            // Hitung gaji harian jika type harian
            $dailySalary = 0;
            if ($type === 'harian') {
                $dailySalary = $calc['valid_attendance_days'] * $calc['daily_rate'];
            } else {
                $dailySalary = $calc['regular_salary'];
            }
            
            $html .= '<tr class="employee-row">
                <td>' . $no++ . '</td>
                <td>' . ($emp['badge'] ?? $emp['badge_no'] ?? $emp['id'] ?? '-') . '</td>
                <td class="text-left">' . $emp['nama'] . '</td>
                <td>' . $calc['work_days'] . '</td>
                <td>' . $calc['valid_attendance_days'] . '</td>
                <td>' . number_format($calc['regular_hours'], 2) . '</td>
                <td>' . number_format($calc['overtime_hours'], 2) . '</td>
                <td>' . number_format($calc['longshift_hours'], 0) . '</td>';
            
            if ($type === 'harian') {
                $html .= '<td class="text-right">Rp ' . number_format($dailySalary, 2) . '</td>';
            } else {
                $html .= '<td class="text-right">Rp ' . number_format($calc['regular_salary'], 2) . '</td>';
            }
            
            $html .= '<td class="text-right">Rp ' . number_format($calc['overtime_salary'], 2) . '</td>
                <td class="text-right">Rp ' . number_format($calc['longshift_salary'], 2) . '</td>
                <td class="text-right"><strong>Rp ' . number_format($calc['total_salary'], 2) . '</strong></td>
            </tr>';
        }

        // TOTAL ROW
        $html .= '<tr class="total-row">
            <td colspan="3"><strong>TOTAL KESELURUHAN</strong></td>
            <td><strong>' . $totalWorkDays . '</strong></td>
            <td><strong>' . $totalValidDays . '</strong></td>
            <td><strong>' . number_format($totalRegularHours, 2) . '</strong></td>
            <td><strong>' . number_format($totalOvertimeHours, 2) . '</strong></td>
            <td><strong>' . number_format($totalLongshift, 0) . '</strong></td>';
        
        if ($type === 'harian') {
            $totalDailySalary = $totalValidDays * ($data[0]['calculations']['daily_rate'] ?? 0);
            $html .= '<td class="text-right"><strong>Rp ' . number_format($totalDailySalary, 2) . '</strong></td>';
        } else {
            $html .= '<td class="text-right"><strong>Rp ' . number_format(array_sum(array_column(array_column($data, 'calculations'), 'regular_salary')), 2) . '</strong></td>';
        }
        
        $html .= '<td class="text-right"><strong>Rp ' . number_format(array_sum(array_column(array_column($data, 'calculations'), 'overtime_salary')), 2) . '</strong></td>
            <td class="text-right"><strong>Rp ' . number_format(array_sum(array_column(array_column($data, 'calculations'), 'longshift_salary')), 2) . '</strong></td>
            <td class="text-right"><strong>Rp ' . number_format($totalSalary, 2) . '</strong></td>
        </tr>';

        $html .= '</tbody></table>';

        // FOOTER & SIGNATURE
        $html .= '<div style="margin-top: 20px; padding: 15px; background-color: #e6f7ff; border: 2px solid #1890ff; border-radius: 5px;">
            <h3 style="margin: 0; color: #0050b3; text-align: center;">
                TOTAL SELURUH KARYAWAN: Rp ' . number_format($totalSalary, 2) . '
            </h3>';
        
        if ($type === 'harian') {
            $html .= '<p style="text-align: center; margin: 5px 0 0 0;">*Perhitungan: (' . $totalValidDays . ' hari valid × Rp ' . number_format($dailyRate, 2) . ') + Lembur + Longshift</p>';
        } else {
            $html .= '<p style="text-align: center; margin: 5px 0 0 0;">*Termasuk gaji normal, lembur, dan longshift</p>';
        }
        
        $html .= '</div>';

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

        $html .= '</body></html>';

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
