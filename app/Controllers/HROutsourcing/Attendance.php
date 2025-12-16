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

            // var_dump($attendanceData);
            // die;

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
                $pin = $att['pin'];
                
                // Initialize jika belum ada
                if (!isset($employeeAttendanceMap[$pin])) {
                    $employeeAttendanceMap[$pin] = [];
                }
                
                $employeeAttendanceMap[$pin][] = [
                    'datetime' => $att['datetime'],
                    'timestamp' => strtotime($att['datetime']),
                    'type' => $this->getAttendanceType($att['status']),
                    'verified' => $att['verified']
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

}
