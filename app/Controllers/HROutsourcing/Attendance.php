<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\API\Attendances;
use App\Controllers\BaseController;
use App\Controllers\Master\AttendancesUnit;
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
    

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->hrOutSourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->AttendancesUnitOutsourceModel = new AttendancesUnitOutsourceModel();
        $this->hrOutSourcingEmployeModel = new HROutsourcingEmployeeModel();
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
        // Cek input
        $companyId = $this->request->getVar('company_id');
        $date = $this->request->getVar('date');
        
        if (!$companyId || !$date) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Company ID dan Tanggal harus diisi',
                'data' => [],
                'total' => 0
            ]);
        }
        
        try {
            // Get company data
            $company = $this->hrOutSourcingCompanyModel
                ->where('id', $companyId)
                ->where('deletedAt', NULL)
                ->first();
                
            if (!$company) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Perusahaan tidak ditemukan',
                    'data' => [],
                    'total' => 0
                ]);
            }
            
            // Get device IP from AttendancesUnitOutsourceModel
            $device = $this->AttendancesUnitOutsourceModel
                ->where('id', $company['ip_finger'])
                ->first();
                
            if (!$device) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Device fingerprint tidak ditemukan',
                    'data' => [],
                    'total' => 0
                ]);
            }
            
            // Parse tanggal dari input
            $selectedDate = date('Y-m-d', strtotime($date));
            
            // Get attendance data from device dengan tanggal yang difilter
            $attendanceData = $this->Attendance->getAttendanceFromDevice(
                $device['ip'],
                0,
                $selectedDate // Gunakan tanggal dari input
            );

            // =============================
            // 1. Get semua karyawan perusahaan
            // =============================
            $allEmployees = $this->hrOutSourcingEmployeModel
                ->where('company_id', $companyId)
                ->findAll();
            
            if (empty($allEmployees)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tidak ada karyawan di perusahaan ini',
                    'data' => [],
                    'total' => 0,
                    'date' => $date
                ]);
            }

            // =============================
            // 2. Filter attendance data berdasarkan tanggal yang dipilih
            // =============================
            $filteredAttendance = [];
            
            if (!empty($attendanceData)) {
                foreach ($attendanceData as $att) {
                    $attDate = date('Y-m-d', strtotime($att['datetime']));
                    
                    // Hanya ambil data pada tanggal yang dipilih
                    if ($attDate === $selectedDate) {
                        $filteredAttendance[] = $att;
                    }
                }
            }

            // =============================
            // 3. Group attendance by employee dan sort by time
            // =============================
            $employeeAttendanceMap = [];
            
            foreach ($filteredAttendance as $att) {
                $pin = (int)$att['pin']; // 🔥 FIX PENTING

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

            
            // Sort attendance untuk setiap employee berdasarkan waktu
            foreach ($employeeAttendanceMap as $pin => $attRecords) {
                usort($employeeAttendanceMap[$pin], function($a, $b) {
                    return $a['timestamp'] - $b['timestamp'];
                });
            }

            // =============================
            // 4. Process semua karyawan (termasuk yang tidak absen)
            // =============================
            $processedData = [];
            $counter = 1;
            
            foreach ($allEmployees as $employee) {
                $pin = $employee['id'];
                $hasAttendance = isset($employeeAttendanceMap[$pin]);
                
                // Default data untuk karyawan yang tidak absen
                $empData = [
                    'no' => $counter++,
                    'user_id' => $pin,
                    'name' => $employee['nama'],
                    'badge_no' => $employee['badge'],
                    'check_in' => null,
                    'check_out' => null,
                    'verified_in' => 'Not Verified',
                    'verified_out' => 'Not Verified',
                    'status' => 'Tidak Masuk',
                    'status_class' => 'danger',
                    'company_name' => $company['name'],
                    'company_id' => $companyId,
                    'attendance_count' => 0
                ];
                
                if ($hasAttendance) {
                    $attRecords = $employeeAttendanceMap[$pin];
                    $empData['attendance_count'] = count($attRecords);
                    
                    // Ambil check-in pertama
                    $firstRecord = $attRecords[0];
                    $empData['check_in'] = date('H:i:s', $firstRecord['timestamp']);
                    $empData['verified_in'] = $firstRecord['verified'] == '1' ? 'Verified' : 'Not Verified';
                    
                    // Tentukan check-out berdasarkan beberapa skenario:
                    // 1. Jika ada record check-out/Overtime Out
                    // 2. Jika hanya ada 1 record, cek apakah sudah lebih dari 8 jam
                    // 3. Ambil record terakhir jika ada multiple records
                    
                    $checkOutTime = null;
                    
                    // Cari record check-out/Overtime Out
                    foreach ($attRecords as $record) {
                        if ($record['type'] === 'Check Out' || $record['type'] === 'Overtime Out') {
                            $checkOutTime = $record['timestamp'];
                            $empData['verified_out'] = $record['verified'] == '1' ? 'Verified' : 'Not Verified';
                            break;
                        }
                    }
                    
                    // Jika tidak ada check-out yang eksplisit
                    if ($checkOutTime === null) {
                        $lastRecord = end($attRecords);
                        
                        // Jika hanya ada 1 record dan sudah lebih dari 8 jam, anggap sudah check-out
                        if (count($attRecords) === 1) {
                            $hoursWorked = (time() - $firstRecord['timestamp']) / 3600;
                            if ($hoursWorked >= 8) {
                                $checkOutTime = $firstRecord['timestamp'] + (8 * 3600); // Tambah 8 jam
                            }
                        } else {
                            // Ambil record terakhir sebagai check-out
                            $checkOutTime = $lastRecord['timestamp'];
                            $empData['verified_out'] = $lastRecord['verified'] == '1' ? 'Verified' : 'Not Verified';
                        }
                    }
                    
                    if ($checkOutTime) {
                        $empData['check_out'] = date('H:i:s', $checkOutTime);
                        
                        // Tentukan status
                        if ($empData['check_in'] && $empData['check_out']) {
                            $empData['status'] = 'Complete';
                            $empData['status_class'] = 'success';
                        }
                    } else {
                        $empData['status'] = 'Check In Only';
                        $empData['status_class'] = 'warning';
                    }
                    
                    // Tambahkan informasi tambahan jika ada multiple records
                    if (count($attRecords) > 2) {
                        $empData['status'] = 'Multiple Records (' . count($attRecords) . ')';
                        $empData['status_class'] = 'info';
                    }
                }
                
                $processedData[] = $empData;
            }

            // =============================
            // 5. Sort by badge number
            // =============================
            usort($processedData, function($a, $b) {
                return strcmp($a['badge_no'], $b['badge_no']);
            });
            
            // Reset numbering setelah sort
            foreach ($processedData as $key => $data) {
                $processedData[$key]['no'] = $key + 1;
            }
            
            // Hitung statistik
            $stats = [
                'total_employees' => count($allEmployees),
                'present' => count(array_filter($processedData, function($item) {
                    return $item['status'] !== 'Tidak Masuk';
                })),
                'absent' => count(array_filter($processedData, function($item) {
                    return $item['status'] === 'Tidak Masuk';
                })),
                'complete' => count(array_filter($processedData, function($item) {
                    return $item['status'] === 'Complete';
                }))
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $processedData,
                'total' => count($processedData),
                'date' => $date,
                'stats' => $stats
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

    private function getAttendanceType($status)
    {
        $types = [
            '0' => 'Check In',
            '1' => 'Check Out',
            '255' => 'Overtime In',
            '256' => 'Overtime Out'
        ];
        
        return isset($types[$status]) ? $types[$status] : 'Unknown';
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
        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');
        $period    = $this->request->getPost('period'); // 1-15 / 16-31
        $type      = $this->request->getPost('type');   // harian / minggu

        if (!$companyId || !$startDate || !$endDate || !$period) {
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
        // 3. DEVICE FINGERPRINT
        // ===============================
        $device = $this->AttendancesUnitOutsourceModel
            ->where('id', $company['ip_finger'])
            ->first();

        if (!$device) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Device fingerprint tidak ditemukan'
            ]);
        }

        // ===============================
        // 4. DATA KARYAWAN
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
        // 5. AMBIL ABSENSI (1x SAJA)
        // ===============================
        $rawAttendance = $this->Attendance->getAttendanceFromDevice(
    $device['ip'],
    0,
    null
);

$attendanceData = [];

foreach ($rawAttendance as $att) {

    $pin = (int)$att['pin'];
    if ($pin <= 0) continue;

    $attDate = date('Y-m-d', strtotime($att['datetime']));

    if ($attDate < $startDate || $attDate > $endDate) {
        continue;
    }

    if (!isset($attendanceData[$attDate])) {
        $attendanceData[$attDate] = [];
    }

    if (!isset($attendanceData[$attDate][$pin])) {
        $attendanceData[$attDate][$pin] = [];
    }

    $attendanceData[$attDate][$pin][] = [
        'datetime'  => $att['datetime'],
        'timestamp' => strtotime($att['datetime']),
        'verified'  => (int)$att['verified'],
        'status'    => (int)$att['status'],
        'workcode'  => (int)$att['workcode'],
    ];
}

// sort log
foreach ($attendanceData as $date => $employees) {
    foreach ($employees as $pin => $records) {
        usort($attendanceData[$date][$pin], fn($a,$b) => $a['timestamp'] <=> $b['timestamp']);
    }
}


        // ===============================
        // 6. PROSES GAJI
        // ===============================
        $processedEmployees = [];

        foreach ($allEmployees as $employee) {

    // 🔥 PIN == EMPLOYEE.ID
    $employeeId = (int)$employee['id'];

    $employeeAttendanceDays = [];
    $workDays = 0;

    foreach ($attendanceData as $date => $dailyData) {

        if (!isset($dailyData[$employeeId])) {
            continue;
        }

        $records = $dailyData[$employeeId];
        if (empty($records)) continue;

        $checkIn  = $records[0]['datetime'];
        $checkOut = end($records)['datetime'];

        // cari checkout valid
        $finalCheckOut = $checkOut;
        foreach ($records as $r) {
            if ($r['status'] === 1) {
                $finalCheckOut = $r['datetime'];
                break;
            }
        }

        $employeeAttendanceDays[$date] = [
            'check_in'  => $checkIn,
            'check_out' => $finalCheckOut,
            'records'   => $records
        ];

        $workDays++;
    }

    if (!empty($employeeAttendanceDays)) {
        $processedEmployees[] = [
            'employee'   => $employee,
            'attendance' => $employeeAttendanceDays,
            'work_days'  => $workDays
        ];
    }
}


        // ===============================
        // 7. GENERATE PDF
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
        // 8. RESPONSE
        // ===============================
        return $this->response->setJSON([
            'success'          => true,
            'data'             => base64_encode($pdfContent),
            'filename'         => "Payroll_{$company['name']}_{$period}_{$type}_" . date('Ymd_His') . ".pdf",
            'total_employees'  => count($processedEmployees),
            'period'           => date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate))
        ]);

    } catch (\Throwable $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


/**
 * FUNGSI PERHITUNGAN GAJI BERDASARKAN JENIS
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
        'work_days' => count($attendanceDays)
    ];

    // Tentukan rate berdasarkan jenis (harian/minggu)
    if ($type === 'harian') {
        // GAJI HARIAN (PER 15 HARI)
        $daysInPeriod = $this->getWorkingDaysInPeriod($period);
        
        // Rate statis per jam
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
            
            if ($hours > 0) {
                if ($hours <= 8) {
                    $result['regular_hours'] += $hours;
                    $result['regular_salary'] += ($hours * $hourlyRate);
                } else {
                    $result['regular_hours'] += 8;
                    $result['regular_salary'] += (8 * $hourlyRate);
                    
                    $overtime = $hours - 8;
                    $result['overtime_hours'] += $overtime;
                    $result['overtime_salary'] += ($overtime * $overtimeRate);
                }
                
                // Cek apakah longshift (kerja > 14 jam total)
                if ($hours > 14) {
                    $result['longshift_hours'] += 1;
                    $result['longshift_salary'] += $longshiftRate;
                }
            }
        }
    }

    // Total semua
    $result['total_hours'] = $result['regular_hours'] + $result['overtime_hours'];
    $result['total_salary'] = $result['regular_salary'] + $result['overtime_salary'] + $result['longshift_salary'];

    return $result;
}

/**
 * HITUNG JAM KERJA HARIAN DARI CHECK-IN DAN CHECK-OUT
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
 * GENERATE HTML CONTENT UNTUK PDF
 */
private function generateHtmlContent($data, $period, $type, $startDate, $endDate, $company)
{
    $periodText = $period === '1-15' ? '1-15' : '16-' . date('t', strtotime($startDate));
    $title = $type === 'harian' ? "SLIP GAJI HARIAN PERIODE {$periodText}" : "SLIP GAJI MINGGUAN";
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Laporan Penggajian</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .header h2 { margin: 0; padding: 0; color: #333; }
            .header p { margin: 3px 0; padding: 0; }
            .company-info { text-align: left; margin-bottom: 15px; padding: 10px; background-color: #f9f9f9; border-radius: 5px; border: 1px solid #ddd; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            table, th, td { border: 1px solid #000; }
            th, td { padding: 4px 6px; text-align: center; }
            th { background-color: #f2f2f2; font-weight: bold; }
            .text-left { text-align: left; }
            .text-right { text-align: right; }
            .total-row { font-weight: bold; background-color: #e8e8e8; }
            .employee-row:hover { background-color: #f5f5f5; }
            .signature { width: 100%; margin-top: 40px; }
            .signature td { border: none; text-align: center; padding-top: 40px; }
            .summary-box { background-color: #f0f8ff; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #ccc; }
            .summary-item { display: inline-block; margin-right: 20px; }
            .summary-label { font-weight: bold; }
            .page-break { page-break-after: always; }
            @media print {
                body { font-size: 9px; }
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

    // SUMMARY
    $totalEmployees = count($data);
    $totalRegularHours = array_sum(array_column(array_column($data, 'calculations'), 'regular_hours'));
    $totalOvertimeHours = array_sum(array_column(array_column($data, 'calculations'), 'overtime_hours'));
    $totalSalary = array_sum(array_column(array_column($data, 'calculations'), 'total_salary'));
    $totalWorkDays = array_sum(array_column(array_column($data, 'calculations'), 'work_days'));
    
    $html .= '<div class="summary-box">
        <div class="summary-item"><span class="summary-label">Jumlah Karyawan:</span> ' . $totalEmployees . '</div>
        <div class="summary-item"><span class="summary-label">Total Hari Kerja:</span> ' . $totalWorkDays . '</div>
        <div class="summary-item"><span class="summary-label">Total Jam Kerja:</span> ' . number_format($totalRegularHours + $totalOvertimeHours, 2) . ' jam</div>
        <div class="summary-item"><span class="summary-label">Total Gaji:</span> Rp ' . number_format($totalSalary, 2) . '</div>
    </div>';

    // TABEL GAJI
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

    $grandTotalHours = 0;
    $grandTotalSalary = 0;
    $no = 1;

    foreach ($data as $item) {
        $calc = $item['calculations'];
        $emp = $item['employee'];
        
        $html .= '<tr class="employee-row">
            <td>' . $no++ . '</td>
            <td>' . ($emp['badge_no'] ?? $emp['id'] ?? '-') . '</td>
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

        $grandTotalHours += $calc['total_hours'];
        $grandTotalSalary += $calc['total_salary'];
    }

    // TOTAL ROW
    $html .= '<tr class="total-row">
        <td colspan="3"><strong>GRAND TOTAL</strong></td>
        <td><strong>' . $totalWorkDays . '</strong></td>
        <td><strong>' . number_format($totalRegularHours, 2) . '</strong></td>
        <td><strong>' . number_format($totalOvertimeHours, 2) . '</strong></td>
        <td><strong>' . number_format(array_sum(array_column(array_column($data, 'calculations'), 'longshift_hours')), 0) . '</strong></td>
        <td class="text-right"><strong>Rp ' . number_format(array_sum(array_column(array_column($data, 'calculations'), 'regular_salary')), 2) . '</strong></td>
        <td class="text-right"><strong>Rp ' . number_format(array_sum(array_column(array_column($data, 'calculations'), 'overtime_salary')), 2) . '</strong></td>
        <td class="text-right"><strong>Rp ' . number_format(array_sum(array_column(array_column($data, 'calculations'), 'longshift_salary')), 2) . '</strong></td>
        <td class="text-right"><strong>Rp ' . number_format($grandTotalSalary, 2) . '</strong></td>
    </tr>';

    $html .= '</tbody></table>';

    // SUMMARY BOTTOM
    $html .= '<div style="margin-top: 20px; padding: 15px; background-color: #e6f7ff; border: 2px solid #1890ff; border-radius: 5px;">
        <h3 style="margin: 0; color: #0050b3; text-align: center;">
            TOTAL: ' . number_format($grandTotalHours, 2) . ' Jam Kerja | Rp ' . number_format($grandTotalSalary, 2) . '
        </h3>
        <p style="text-align: center; margin: 5px 0 0 0;">*Termasuk jam normal, lembur, dan longshift</p>
    </div>';

    // FOOTER & SIGNATURE
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
 * GENERATE PDF DARI HTML
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
