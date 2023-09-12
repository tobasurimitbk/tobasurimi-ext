<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesLogModel;
use App\Models\AttendancesModel;
use App\Models\BigDaysModel;
use App\Models\EmployeesModel;
use App\Models\FormPerijinanModel;
use App\Models\PayrollsModel;

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

    public function attendance()
    {
        $payload = [
            "idCompany" => $this->this_company_id
        ];

        $res_employee = curl_request("GET", "/employees/all?idCompany=" . $this->this_company_id, $this->token);
        $dataEmployee = [];
        if ($res_employee["code"] === 200) {
            $dataEmployee = json_decode($res_employee["body"])->data;
        }

        $data = [
            "dataEmployee" => $dataEmployee,
        ];

        //return view('employee/index');
        return view('hr/attendance/index', $data);

        /*
        // Get Kategori
        $responseKategori = curl_request("GET", "/metadata/all?name=kategori_barang", $this->token);

        $dataKategori = [];

        $data = [
            "dataKategori" => $dataKategori
        ];

        return view('barang/index', $data);
        */
    }

    public function ListAttendance()
    {
        $AttendancesLogModel = new AttendancesLogModel();
        $EmployeesModel = new EmployeesModel();
        $FormPerijinanModel = new FormPerijinanModel();

        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        $dataEmployee = $EmployeesModel->getEmployees($this->this_company_id,);

        $data = array();
        $hadir = 0;
        $alpha = 0;
        $sakit = 0;
        $ijin = 0;
        $cuti = 0;
        $libur = 0;

        foreach ($dataEmployee as $value) {
            $dataPerijinan = $FormPerijinanModel->getPerijinanAmt($value["id"], $year, $month);
            $dataLog = $AttendancesLogModel->getLogAmt($value["id"], $year, $month);

            foreach ($dataPerijinan as $val1) {
                switch ($val1->status) {
                    case "HADIR":
                        $hadir++;
                        break;
                    case "ALPHA":
                        $alpha++;
                        break;
                    case "SAKIT":
                        $sakit++;
                        break;
                    case "IJIN":
                        $ijin++;
                        break;
                    case "CUTI":
                        $cuti++;
                        break;
                    case "LIBUR":
                        $libur++;
                        break;
                    default:
                }
            }

            $list_att = array();

            $constructor = [
                "employeeName" => $value['name'],
                "hadir" => $hadir,
                "alpha" => $alpha,
                "sakit" => $sakit,
                "ijin" => $ijin,
                "cuti" => $cuti,
                "libur" => $libur,
            ];

            foreach ($dataLog as $val2) {
                $dat = strtotime($val2->date_create);
                $att = [
                    "periode" => date('d-m-Y', $dat),
                    "checkin" => date('H:i:s', $dat),
                    "checkout" => date('H:i:s', $dat),
                ];
                $list_att[] = $att;
            };

            $groupList = array();

            foreach ($list_att as $element) {
                $groupList[$element["periode"]][] = $element;
            };

            $constructor['list_attendance'] = $groupList;

            $data[] = $constructor;
        }

        $data = [
            'year' => $year,
            'month' => $month,
            'res_user'  => $data

        ];

        return view('hr/attendance/list-attendance', $data);
    }

    public function LogAttendance()
    {
        // declare model
        $AttendancesLogModel = new AttendancesLogModel();
        $EmployeesModel = new EmployeesModel();
        $FormPerijinanModel = new FormPerijinanModel();

        // get data $_GET
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        // get data from model
        $dataEmployeePager = $EmployeesModel->getEmployeesWithPagination($this->this_company_id, $this->request->getGet('employeesID'));
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
                "employeeName" => $value['name'],
                "list_attendance" => $dataLog,
                'statusAttendances' => [
                    'IJIN' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "IJIN", $year, $month),
                    'CUTI' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "CUTI", $year, $month),
                    'SAKIT' => $FormPerijinanModel->getTotalPerijinanByStatus($value['id'], "SAKIT", $year, $month),
                ]
            ];
        }

        // final data
        $data = [
            'year' => $year,
            'month' => $month,
            'res_user'  => $dataResult,
            'employeesData' => $dataEmployeePager['data'],
            'pager' => $dataEmployeePager['pager'],
            'employeeDetailFilter' => $employeeDetailFilter,
            'pager' => $dataEmployeePager['pager']

        ];
        return view('hr/attendance/log-attendance', $data);
    }

    public function SaveAttendance()
    {
        try {
            $img = $this->request->getVar("pic");
            //$img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);

            $payload = json_encode([
                "company_id" => formatter($this->this_company_id, "STR_TO_INT"),
                "employee_id"   => formatter($this->request->getVar("employee_id"), "STR_TO_INT"),
                "checkin"   => date("Y-m-d H:i:s"),
                "status"       => "HADIR",
                "checkStatus"   => $this->request->getVar("state"),
                "checkin_image" => $img,
            ]);
            $response = curl_request("POST", "/attendance", $this->token, $payload);
            $dataAttendance = [];
            if ($response["code"] === 200) {
                $message = json_decode($response["body"]);
                $data = [
                    "status"    => true,
                    "message"   => $message->message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
            }
            echo json_encode($data);
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }

        /*
        $file = uniqid() . '.png';
        $success = file_put_contents($file, $data);
        */
        return;
    }

    public function CheckPinEmployee()
    {
        try {
            $employee_id = $this->request->getVar("employee_id");
            $pin = $this->request->getVar("pin");

            $payload = json_encode([
                "employee_id"   => formatter($this->request->getVar("employee_id"), "STR_TO_INT"),
                "pin"   => $this->request->getVar("pin"),
            ]);

            $response = curl_request("POST", "/attendance/pinValidation", $this->token, $payload);

            if ($response["code"] === 200) {
                $message = json_decode($response["body"]);
                $data = [
                    "status"    => true,
                    "message"   => $message->message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
            }
            echo json_encode($data);
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }

        return;
    }

    public function get_employee_by_company($employee_id)
    {
        $dataEmployee = [];

        $responseEmployee = curl_request("GET", "/attendance/$employee_id", $this->token);
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

        $data = [
            "data" => $dataEmployee
        ];

        echo json_encode($data);
        return;
    }


    public function generateAttendanceView()
    {
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        // declare model
        $AttendanceModel = new AttendancesModel();
        $EmployeesModel = new EmployeesModel();
        $payrollModel = new PayrollsModel();

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

        $dataEmployeePager = $EmployeesModel->getEmployeesWithPagination($this->this_company_id, $this->request->getGet('employeesID'));
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
            'isPostingPayroll' => $isPostingPayroll
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
                    ->whereNotIn('status', ["LEMBUR"])
                    ->first();
                // check adakah data 
                $hariLibur = $hariLiburModel->where('date', $dates)->first();

                if ($hariLibur != null || date('l', strtotime($dates)) == "Sunday") {
                    // ada hari libur
                    $AttendanceModel->insert([
                        'company_id' => $this->this_company_id,
                        'employee_id' => $e['id'],
                        'periode' => $dates,
                        'status' => "LIBUR",
                        'reason' => ''
                    ]);
                } elseif ($formPerizinan != null) {
                    // ada perizinan 
                    $AttendanceModel->insert([
                        'company_id' => $this->this_company_id,
                        'employee_id' => $e['id'],
                        'periode' => $dates,
                        'status' => $formPerizinan['status'],
                        'reason' => $formPerizinan['reason']
                    ]);
                } elseif ($formPerizinan == null) {
                    // tidak ada data perizinan jadi
                    // get attendance by date and employee
                    $logAttandance = $AttendancesLogModel->where('employees_id', $e['id'])
                        ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $dates)
                        ->orderBy('id', "DESC") // ambil terbaru
                        ->limit(2) // get 2 date in log attandance 
                        ->get()
                        ->getResult();

                    if (\count($logAttandance) == 0) {
                        // rekap absen tidak ditemukan
                        // set jadi ALPHA
                        $AttendanceModel->insert([
                            'company_id' => $this->this_company_id,
                            'employee_id' => $e['id'],
                            'periode' => $dates,
                            'status' => 'ALPHA',
                        ]);
                    } else {
                        // data absen ada di log
                        if (\count($logAttandance) == 2) {
                            // ada attandance (in dan out)
                            // create in
                            $AttendanceModel->insert([
                                'company_id' => $this->this_company_id,
                                'employee_id' => $e['id'],
                                'periode' => $dates,
                                'checkin' => \date('H:i:s', \strtotime($logAttandance[1]->date_create)), // in
                                'checkout' => \date('H:i:s', \strtotime($logAttandance[0]->date_create)), // out
                                'status' => 'HADIR'
                            ]);
                        } else {
                            // ada attandance only(in)
                            $AttendanceModel->insert([
                                'company_id' => $this->this_company_id,
                                'employee_id' => $e['id'],
                                'periode' => $dates,
                                'checkin' => \date('H:i:s', \strtotime($logAttandance[0]->date_create)), // in
                                'checkout' => \date('H:i:s', \strtotime($logAttandance[0]->date_create)), // out
                                'status' => 'HADIR',
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

        $AttendanceModel = new AttendancesModel();
        $EmployeesModel = new EmployeesModel();

        $resultData = [
            'attendance' => $AttendanceModel->where('periode', $tanggal)
                ->where('employee_id', $employeeID)
                ->first(),
            'employee' => $EmployeesModel->where('id', $employeeID)->first()
        ];

        return $this->response->setJSON([
            'data' => $resultData
        ]);
    }

    public function updateAttendance()
    {
        $attendenceID = $this->request->getVar('attendenceID');
        $checkIN = $this->request->getVar('checkIn');
        $checkOut = $this->request->getVar('checkOut');
        $statusKehadiran = $this->request->getVar('statusKehadiran');
        $reason = $this->request->getVar('reason');

        $AttendanceModel = new AttendancesModel();

        $AttendanceModel->update($attendenceID, [
            'checkin' => $checkIN, // in
            'checkout' => $checkOut, // out
            'status' => $statusKehadiran,
            'reason' => $reason
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

        $employessModel = new EmployeesModel();

        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $this->this_company_id,
            'users.id' => null
        ];

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
}
