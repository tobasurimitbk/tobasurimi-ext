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
    

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->hrOutSourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->AttendancesUnitOutsourceModel = new AttendancesUnitOutsourceModel();
        $this->hrOutSourcingEmployeModel = new HROutsourcingEmployeeModel();
        $this->Attendance = new AttendancesUnit();
    }

    public function index()
    {
        $data = [
            "company" => $this->hrOutSourcingCompanyModel->where('deletedAt', NULL)->findAll(),
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


            // Process and join with employee data
            $processedData = [];
            $counter = 1;
            
            foreach ($attendanceData as $att) {

    
                $datetime = $att['datetime'];
                $datePart = date('d/m/Y', strtotime($datetime));
                $timePart = date('H:i:s', strtotime($datetime));
                
                // Get employee data by PIN (assuming PIN is employee ID/badge number)
                $employee = $this->hrOutSourcingEmployeModel->where('id', $att['pin'])->first();
                
                // Prepare row data
                $row = [
                    'no' => $counter++,
                    'user_id' => $att['pin'],
                    'name' => $employee ? $employee['nama'] : 'Unknown Employee',
                    'badge_no' => $employee ? $employee['badge'] : '-',
                    'date' => $datePart,
                    'time' => $timePart,
                    'type' => $this->getAttendanceType($att['status']),
                    'verified' => $att['verified'] == '1' ? 'Verified' : 'Not Verified',
                    'company_name' => $company['name'],
                    'company_id' => $companyId,
                    'raw_datetime' => $datetime,
                    'actions' => $this->getActionButtons($att['pin'], $datetime)
                ];
                
                $processedData[] = $row;
            }
            
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

    private function getActionButtons($pin, $datetime)
    {
        return '
            <div class="btn-group">
                <button class="btn btn-sm btn-info view-btn" data-pin="' . $pin . '" data-datetime="' . $datetime . '">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-warning edit-btn" data-pin="' . $pin . '" data-datetime="' . $datetime . '">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-btn" data-pin="' . $pin . '" data-datetime="' . $datetime . '">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }
}
