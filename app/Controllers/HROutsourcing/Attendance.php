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
        
        // Get attendance data from device
        $attendanceData = $this->Attendance->getAttendanceFromDevice(
            $device['ip'],
            0,
            date('y-m-d')
        );
        

        if (empty($attendanceData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tidak ada data attendance pada tanggal tersebut',
                'data' => [],
                'total' => 0,
                'date' => $date
            ]);
        }

        // =============================
        // 1. Kumpulkan semua PIN
        // =============================
        $pins = array_column($attendanceData, 'pin');

        // =============================
        // 2. Ambil employee berdasarkan company_id dan PIN
        // =============================
        $employees = $this->hrOutSourcingEmployeModel
            ->where('company_id', $companyId)
            ->whereIn('id', $pins)
            ->findAll();

        // =============================
        // 3. Buat employee map untuk akses cepat
        // =============================
        $employeeMap = [];
        foreach ($employees as $e) {
            $employeeMap[$e['id']] = $e;
        }

        // =============================
        // 4. Group attendance by employee untuk Check In/Check Out
        // =============================
        $employeeAttendanceMap = [];
        
        foreach ($attendanceData as $att) {
            // hanya ambil log yang punya employee di company ini
            if (!isset($employeeMap[$att['pin']])) {
                continue; // skip
            }

            $pin = $att['pin'];
            $type = $this->getAttendanceType($att['status']);
            $datetime = $att['datetime'];
            $datePart = date('d/m/Y', strtotime($datetime));
            $timePart = date('H:i:s', strtotime($datetime));

            // Initialize jika belum ada
            if (!isset($employeeAttendanceMap[$pin])) {
                $employeeAttendanceMap[$pin] = [
                    'user_id' => $pin,
                    'name' => $employeeMap[$pin]['nama'],
                    'badge_no' => $employeeMap[$pin]['badge'],
                    'company_name' => $company['name'],
                    'company_id' => $companyId,
                    'check_in' => null,
                    'check_in_time' => null,
                    'check_out' => null,
                    'check_out_time' => null,
                    'verified_in' => 'Not Verified',
                    'verified_out' => 'Not Verified',
                    'status' => 'Belum Absen',
                    'status_class' => 'danger'
                ];
            }

            // Update Check In atau Check Out
            if ($type === 'Check In' || $type === 'Overtime In') {
                $employeeAttendanceMap[$pin]['check_in'] = $datePart;
                $employeeAttendanceMap[$pin]['check_in_time'] = $timePart;
                $employeeAttendanceMap[$pin]['verified_in'] = $att['verified'] == '1' ? 'Verified' : 'Not Verified';
            } else if ($type === 'Check Out' || $type === 'Overtime Out') {
                $employeeAttendanceMap[$pin]['check_out'] = $datePart;
                $employeeAttendanceMap[$pin]['check_out_time'] = $timePart;
                $employeeAttendanceMap[$pin]['verified_out'] = $att['verified'] == '1' ? 'Verified' : 'Not Verified';
            }
        }

        // =============================
        // 5. Update status untuk setiap employee
        // =============================
        $processedData = [];
        $counter = 1;
        
        foreach ($employeeAttendanceMap as $pin => $empData) {
            // Determine status
            if ($empData['check_in'] && $empData['check_out']) {
                $empData['status'] = 'Complete';
                $empData['status_class'] = 'success';
            } else if ($empData['check_in']) {
                $empData['status'] = 'Check In Only';
                $empData['status_class'] = 'warning';
            } else if ($empData['check_out']) {
                $empData['status'] = 'Check Out Only';
                $empData['status_class'] = 'info';
            }

            // Format untuk view
            $processedData[] = [
                'no' => $counter++,
                'user_id' => $empData['user_id'],
                'name' => $empData['name'],
                'badge_no' => $empData['badge_no'],
                'check_in' => $empData['check_in_time'],
                'check_out' => $empData['check_out_time'],
                'verified_in' => $empData['verified_in'],
                'verified_out' => $empData['verified_out'],
                'status' => $empData['status'],
                'status_class' => $empData['status_class'],
                'company_name' => $empData['company_name'],
                'company_id' => $empData['company_id']
            ];
        }

        // =============================
        // 6. Sort by badge number
        // =============================
        usort($processedData, function($a, $b) {
            return strcmp($a['badge_no'], $b['badge_no']);
        });
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $processedData,
            'total' => count($processedData),
            'date' => $date
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
