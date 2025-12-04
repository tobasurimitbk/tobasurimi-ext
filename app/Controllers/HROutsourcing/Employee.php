<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Controllers\Master\AttendancesUnit;
use App\Models\AttendancesUnitOutsourceModel;
use App\Models\DivisisModel;
use App\Models\HROutsourcingCompanyModel;
use App\Models\HROutsourcingEmployeeModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Exception;

class Employee extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $hrOutsourcingCompanyModel;
    protected $hrOutsourcingEmployeeModel;
    protected $hrOutsourcingAttendanceModel;
    protected $attendanceUnit;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id ?? NULL;
        $this->divisiModel = new DivisisModel();
        $this->hrOutsourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->hrOutsourcingEmployeeModel = new HROutsourcingEmployeeModel();
        $this->hrOutsourcingAttendanceModel = new AttendancesUnitOutsourceModel();
        $this->attendanceUnit = new AttendancesUnit();
    }


    public function index() {}

    public function create($id)
    {
        $data = [
            'company' => $this->hrOutsourcingCompanyModel->where('id', decrypt($id))->first(),
        ];

        return view('HROutsourcing/employee/form', $data);
    }

    public function getAllEmployeeByCompany()
    {
        $payload = [
            "pageSize"         => $this->request->getVar("length"),
            "currentPage"      => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort"             => $this->request->getVar("sort"),
            "sortType"         => $this->request->getVar("sortType"),
            "company_id"       => $this->this_company_id,
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
        ];

        $condition = [
            'hr_outsourcing_employee.company_id' => $this->request->getVar('company_id'),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $employee = $this->hrOutsourcingEmployeeModel->getList($condition, $addCondition, $limit, $offset);
        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($employee['data'] as $data) {
            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "nama" => $data->nama,
                "tanggal_masuk_kerja" => $data->tanggal_masuk_kerja,
                "badge" => $data->badge,
                "total" => 0,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $employee['totalData'],
            "recordsFiltered"   => $employee['totalFilteredData'],
            "data"              => $dataSPP,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function generateKode()
    {
        $companyId = $this->request->getVar('company_id');
        $month = date('m'); // Bulan saat ini (format: 01-12)
        $year = date('Y'); // Tahun saat ini (format: 2023)
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d'))); // Tanggal terakhir bulan ini

        $lastStr = convertBulanToAngkaRomawi($month) . '/' . $year; // Format: III/2023

        // Ambil hr_outsourcing_employee terakhir di bulan & tahun ini
        $builder = $this->hrOutsourcingEmployeeModel->asArray()->select('badge')
            ->orderBy('badge', "DESC")
            ->where('company_id', $companyId)
            ->where('createdAt >=', $year . "-" . $month . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59")
            ->first();

        $badge = 'KAROSRC'; // badge awal: PJR
        $lastNumber = 1; // Nomor awal: 1

        if ($builder != null && isset($builder['badge'])) {
            $explode = explode('/', $builder['badge']); // Pecah hr_outsourcing_employee menjadi array

            // Pastikan format hr_outsourcing_employee sesuai: PJR/X/2023/00001
            if (count($explode) == 4) {
                $numberStr = $explode[3]; // Ambil bagian nomor (00001)
                $number = intval($numberStr); // Konversi ke integer
                if ($number >= $lastNumber) {
                    $lastNumber = $number + 1; // Increment nomor terakhir
                }
            }
        }

        $formattedlastNumber = sprintf("%05d", $lastNumber); // Format nomor menjadi 5 digit (00001)
        $generatedNo = $badge . '/' . $lastStr . '/' . $formattedlastNumber; // Gabungkan semua bagian

        return json_encode($generatedNo);
    }

    public function store()
    {
        $companyId = $this->request->getVar('company_id');
        $badge = $this->request->getVar('badge_karyawan');
        $nama = $this->request->getVar('nama');
        $tanggal_masuk_kerja = $this->request->getVar('tanggal_masuk_kerja');

        $this->hrOutsourcingEmployeeModel->insert([
            'company_id' => $companyId,
            'badge' => $badge,
            'nama' => $nama,
            'tanggal_masuk_kerja' => $tanggal_masuk_kerja
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Employee Outsourcing Berhasil Disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $companyId = $this->request->getVar('company_id');
        $badge = $this->request->getVar('badge_karyawan');
        $nama = $this->request->getVar('nama');
        $tanggal_masuk_kerja = $this->request->getVar('tanggal_masuk_kerja');

        $this->hrOutsourcingEmployeeModel->update($id, [
            'company_id' => $companyId,
            'badge' => $badge,
            'nama' => $nama,
            'tanggal_masuk_kerja' => $tanggal_masuk_kerja
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Employee Outsourcing Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function destroy()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->hrOutsourcingEmployeeModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Employee Outsourcing Berhasil Dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function generateQrCode($employeeId)
    {
        try {
            $empId = decrypt($employeeId);

            $employee = $this->hrOutsourcingEmployeeModel
                ->select('id, nama, badge, company_id')
                ->where('id', $empId)
                ->first();

            if (!$employee) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Employee tidak ditemukan.'
                ]);
            }

            $company = $this->hrOutsourcingCompanyModel
                ->select('name')
                ->where('id', $employee['company_id'])
                ->first();

            // === Generate type dan encoded id ===
            $type = 'EMP';
            $encryptedId = weakEncrypt("{$type}-{$empId}"); // konsisten kayak barang
            $qrText = "{$type}-{$encryptedId}";

            // === Generate QR Code ===
            $qrCode = new QrCode($qrText);
            $qrCode->setSize(350);
            $qrCode->setMargin(10);
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
            $dataUri = $qrCode->writeDataUri();

            // === HTML tampilan clean ===
            $html = "
            <div style='width:100%; text-align:center; margin-top:20px;'>
                <div style='display:inline-block; border:1px solid #ddd; padding:20px; border-radius:12px; box-shadow:0 0 10px rgba(0,0,0,0.1);'>
                    <h3 style='margin:0; font-weight:600;'>{$company['name']}</h3>
                    <h4 style='margin:5px 0 15px 0;'>{$employee['nama']} ({$employee['badge']})</h4>
                    <img src='{$dataUri}' alt='QR Code' style='width:250px; height:250px;'>
                </div>
            </div>";

            return $this->response->setJSON([
                'status' => 'ok',
                'html'   => $html
            ]);

        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function getEmployeeByIdQr($encryptedId)
    {
        // Set CORS header
        $this->response->setHeader('Access-Control-Allow-Origin', '*');

        try {
            // --- Step 1: decode balik dari Base64 URL-Safe ke raw encrypted ---
            $base64 = strtr($encryptedId, '-_', '+/'); // balik simbol
            $padded = str_pad($base64, strlen($base64) % 4 === 0 ? strlen($base64) : strlen($base64) + (4 - strlen($base64) % 4), '='); // padding "="
            $decodedEncrypted = base64_decode($padded);

            // --- Step 2: decrypt hasil decode ---
            $decoded = decrypt($decodedEncrypted);

            // --- Step 3: ambil data employee ---
            $employee = $this->hrOutsourcingEmployeeModel
                ->select('id, nama, badge')
                ->where('id', $decoded)
                ->first();

            if (!$employee) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data employee tidak ditemukan.'
                ]);
            }

            return $this->response->setJSON([
                'status'   => 'ok',
                'employee' => [
                    'id'     => $employee['id'],
                    'nama'   => $employee['nama'],
                    'badge'  => $employee['badge'],
                    'status' => $employee['status'] ?? 'Aktif',
                ]
            ]);

        } catch (Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal membaca QR: ' . $e->getMessage()
            ]);
        }
    }

    public function syncEmployeeFinger()
    {
        try {
            $companyId = $this->request->getVar('company_id');

            if (empty($companyId)) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Company ID tidak ditemukan",
                    'token' => csrf_hash()
                ]);
            }

            // Ambil data company
            $companyData = $this->hrOutsourcingCompanyModel
                ->where('id', $companyId)
                ->first();

            if (!$companyData) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Data company tidak ditemukan",
                    'token' => csrf_hash()
                ]);
            }

            // Validasi IP finger
            if (empty($companyData['ip_finger'])) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "IP Finger tidak ditemukan untuk company ini",
                    'token' => csrf_hash()
                ]);
            }

            // Ambil attendance unit
            $attendanceUnit = $this->hrOutsourcingAttendanceModel
                ->where('id', $companyData['ip_finger'])
                ->first();

            if (!$attendanceUnit) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Attendance unit tidak ditemukan untuk IP: {$companyData['ip_finger']}",
                    'token' => csrf_hash()
                ]);
            }

            if (empty($attendanceUnit['ip'])) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "IP mesin finger tidak ditemukan di attendance unit",
                    'token' => csrf_hash()
                ]);
            }

            // TEST KONEKSI
            $timeout = max(1, min(5, 2)); // fix range
            $isAlive = icmpPing($attendanceUnit['ip'], $timeout);

            if (!$isAlive) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Mesin finger tidak dapat dihubungi",
                    'data' => [
                        'ip' => $attendanceUnit['ip'],
                        'timeout' => $timeout
                    ],
                    'token' => csrf_hash()
                ]);
            }

            // Ambil karyawan outsourcing
            $outsourcingEmployees = $this->hrOutsourcingEmployeeModel
                ->where('company_id', $companyId)
                ->where('deletedAt', null)
                ->findAll();

            if (empty($outsourcingEmployees)) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Tidak ada karyawan untuk company ini",
                    'token' => csrf_hash()
                ]);
            }

            // ==========================================
            // PROSES SINKRONISASI KE MESIN FINGER
            // ==========================================
            $successCount = 0;
            $failedCount = 0;
            $successDetails = [];
            $failedDetails = [];

            foreach ($outsourcingEmployees as $emp) {
                $pin  = $emp['id'];
                $name = trim($emp['nama']);

                try {
                    $result = $this->attendanceUnit->insert_finger_user_by_rian(
                        $pin,
                        $attendanceUnit['ip'],
                        0,
                        $name
                    );

                    if ($result === true) {
                        $successCount++;
                        $successDetails[] = [
                            'id'   => $pin,
                            'nama' => $name,
                            'status' => "Berhasil"
                        ];
                    } else {
                        $failedCount++;
                        $failedDetails[] = [
                            'id'   => $pin,
                            'nama' => $name,
                            'status' => "Gagal",
                            'reason' => $result ?: "Unknown"
                        ];
                    }
                } catch (Exception $e) {
                    $failedCount++;
                    $failedDetails[] = [
                        'id'   => $pin,
                        'nama' => $name,
                        'status' => "Error",
                        'reason' => $e->getMessage()
                    ];

                    log_message('error', "Sync Employee Finger Error [ID $pin]: {$e->getMessage()}");
                }
            }

            // ==========================================
            // MESSAGE RESULT
            // ==========================================
            $total = count($outsourcingEmployees);

            if ($successCount === $total) {
                $message = "Semua karyawan berhasil disinkronisasi ke mesin finger.";
            } elseif ($successCount > 0 && $failedCount > 0) {
                $message = "Sinkronisasi selesai, beberapa karyawan gagal.";
            } else {
                $message = "Semua sinkronisasi gagal.";
            }

            return response()->setJSON([
                'status'  => $successCount > 0,
                'message' => $message,
                'data' => [
                    'attendance_unit_ip' => $attendanceUnit['ip'],
                    'company_id' => $companyId,
                    'company_name' => $companyData['name'] ?? '-',
                    'sync_date' => date("Y-m-d H:i:s"),

                    'summary' => [
                        'total' => $total,
                        'success' => $successCount,
                        'failed' => $failedCount,
                        'percentage' => round(($successCount / $total) * 100, 2)
                    ],

                    'success_details' => $successDetails,
                    'failed_details' => $failedDetails
                ],
                'token' => csrf_hash()
            ]);

        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => "System Error",
                'data' => [
                    'error' => $e->getMessage()
                ],
                'token' => csrf_hash()
            ]);
        }
    }

    public function checkSyncStatus()
    {
        try {
            $companyId = $this->request->getVar('company_id');

            if (!$companyId) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Company ID tidak ditemukan"
                ]);
            }

            // Ambil IP mesin
            $company = $this->hrOutsourcingCompanyModel->where('id', $companyId)->first();
            if (!$company || empty($company['ip_finger'])) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "IP Finger tidak ditemukan"
                ]);
            }

            $attendanceUnit = $this->hrOutsourcingAttendanceModel->where('id', $company['ip_finger'])->first();
            if (!$attendanceUnit) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Attendance unit tidak valid"
                ]);
            }

            // Ambil user dari DB
            $employees = $this->hrOutsourcingEmployeeModel
                ->select('id, nama')
                ->where('company_id', $companyId)
                ->where('deletedAt', null)
                ->findAll();

            // Ambil user dari mesin
            $machineUsers = $this->attendanceUnit->getAllUsersFromMachine($attendanceUnit['ip']);

            if (!is_array($machineUsers)) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal mengambil data user dari mesin"
                ]);
            }

            // ==========================================
            // HITUNG MISMATCH / DATA BELUM TER-SYNC
            // ==========================================
            $dbIds = array_map('strval', array_column($employees, 'id'));
            $machineIds = array_map('strval', array_column($machineUsers, 'PIN2'));

            $missingInMachine = array_diff($dbIds, $machineIds);
            $extraInMachine   = array_diff($machineIds, $dbIds);

            return response()->setJSON([
                'status' => true,
                'message' => "Status sync berhasil diambil",
                'data' => [
                    'missing_in_machine' => array_values($missingInMachine),
                    'extra_in_machine'   => array_values($extraInMachine),
                    'need_sync_count'    => count($missingInMachine) + count($extraInMachine)
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->setJSON([
                'status' => false,
                'message' => "Error: " . $e->getMessage()
            ]);
        }
    }

}
