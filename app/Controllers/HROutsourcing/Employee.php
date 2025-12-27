<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Controllers\Master\AttendancesUnit;
use App\Models\AttendancesUnitOutsourceModel;
use App\Models\DivisisModel;
use App\Models\HROutsourcingCompanyModel;
use App\Models\HROutsourcingEmployeeModel;
use App\Models\MetadataModel;
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
    protected $metaDataModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id ?? NULL;
        $this->divisiModel = new DivisisModel();
        $this->hrOutsourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->hrOutsourcingEmployeeModel = new HROutsourcingEmployeeModel();
        $this->hrOutsourcingAttendanceModel = new AttendancesUnitOutsourceModel();
        $this->attendanceUnit = new AttendancesUnit();
        $this->metaDataModel = new MetadataModel();
    }


    public function index() {}

    public function create($id)
    {
        $data = [
            'company' => $this->hrOutsourcingCompanyModel->where('id', decrypt($id))->first(),
            'tipeKaryawan' => $this->metaDataModel->where('name', 'tipe_karyawan_outsource')->findAll(),
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
                "tipe_karyawan" => $data->tipe_karyawan,
                "tipe_karyawan_name" => $data->tipe_karyawan_name ?? "",
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
        $tipe_karyawan = $this->request->getVar('tipe_karyawan');

        $this->hrOutsourcingEmployeeModel->insert([
            'company_id' => $companyId,
            'badge' => $badge,
            'nama' => $nama,
            'tanggal_masuk_kerja' => $tanggal_masuk_kerja,
            'tipe_karyawan' => $tipe_karyawan
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
        $tipe_karyawan = $this->request->getVar('tipe_karyawan');
        $tanggal_masuk_kerja = $this->request->getVar('tanggal_masuk_kerja');

        $this->hrOutsourcingEmployeeModel->update($id, [
            'company_id' => $companyId,
            'badge' => $badge,
            'nama' => $nama,
            'tipe_karyawan' => $tipe_karyawan,
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
                    'message' => "Attendance unit tidak ditemukan",
                    'token' => csrf_hash()
                ]);
            }

            // ==========================================
            // CEK DATA YANG PERLU DI-SYNC
            // ==========================================
            
            // 1. Ambil employee AKTIF di database
            $activeEmployees = $this->hrOutsourcingEmployeeModel
                ->where('company_id', $companyId)
                ->where('deletedAt', null)
                ->findAll();
            
            // Cek badge yang kosong
            $employeesWithoutBadge = [];
            $employeesWithBadge = [];
            
            foreach ($activeEmployees as $emp) {
                if (empty($emp['badge']) || trim($emp['badge']) === '') {
                    $employeesWithoutBadge[] = [
                        'id' => $emp['id'],
                        'nama' => $emp['nama']
                    ];
                } else {
                    $employeesWithBadge[] = $emp;
                }
            }
            
            $activeEmployeeIds = array_column($activeEmployees, 'id');
            
            // 2. Ambil employee yang sudah DIHAPUS (soft delete)
            $builder = $this->hrOutsourcingEmployeeModel->builder();
            $deletedEmployees = $builder
                ->select('id, nama, badge')
                ->where('company_id', $companyId)
                ->where('deletedAt IS NOT NULL')
                ->get()
                ->getResultArray();
            
            $deletedEmployeeIds = array_column($deletedEmployees, 'id');

            // 3. Ambil data dari mesin
            $machineUsers = $this->attendanceUnit->getAllUsersFromMachine($attendanceUnit['ip']);
            
            // 4. Identifikasi data yang perlu di-sync
            $machineUserIds = !empty($machineUsers) ? array_column($machineUsers, 'PIN2') : [];
            $machineUserNames = [];
            $machineUserPrivileges = [];
            
            // Buat mapping untuk cek update
            if (!empty($machineUsers)) {
                foreach ($machineUsers as $user) {
                    $pin = $user['PIN2'];
                    $machineUserNames[$pin] = $user['Name'] ?? '';
                    $machineUserPrivileges[$pin] = $user['Privilege'] ?? 0;
                }
            }
            
            // 5. User yang AKTIF di database tapi belum ada di mesin (INSERT)
            $missingInMachine = array_diff($activeEmployeeIds, $machineUserIds);
            
            // 6. User yang sudah DIHAPUS di CI4 tapi masih ada di mesin (DELETE)
            $toDeleteFromMachine = [];
            foreach ($deletedEmployeeIds as $deletedId) {
                if (in_array($deletedId, $machineUserIds)) {
                    $toDeleteFromMachine[] = $deletedId;
                }
            }
            
            // 7. User yang ADA di database dan ADA di mesin tapi NAMA berbeda (UPDATE)
            $toUpdateOnMachine = [];
            foreach ($employeesWithBadge as $emp) {
                $empId = $emp['id'];
                if (in_array($empId, $machineUserIds)) {
                    $expectedName = trim($emp['nama']) . ' ' . trim($emp['badge']);
                    $currentName = $machineUserNames[$empId] ?? '';
                    
                    // Cek jika nama berbeda (case insensitive)
                    if (strtolower(trim($currentName)) !== strtolower(trim($expectedName))) {
                        $toUpdateOnMachine[] = [
                            'id' => $empId,
                            'nama' => $emp['nama'],
                            'badge' => $emp['badge'],
                            'current_name_on_machine' => $currentName,
                            'expected_name_on_machine' => $expectedName
                        ];
                    }
                }
            }

            // ==========================================
            // VALIDASI BADGE SEBELUM SYNC
            // ==========================================
            if (!empty($employeesWithoutBadge)) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Terdapat karyawan tanpa badge number",
                    'data' => [
                        'employees_without_badge' => $employeesWithoutBadge,
                        'count' => count($employeesWithoutBadge)
                    ],
                    'token' => csrf_hash()
                ]);
            }

            // ==========================================
            // PROSES DELETE DULU (user yang sudah di-soft delete di CI4)
            // ==========================================
            $deleteSuccess = [];
            $deleteFailed = [];

            foreach ($toDeleteFromMachine as $userId) {
                try {
                    $result = $this->attendanceUnit->delete_finger_user_by_rian(
                        $userId,
                        $attendanceUnit['ip'],
                        0
                    );

                    if ($result === true || stripos($result ?? '', 'success') !== false) {
                        $deleteSuccess[] = $userId;
                    } else {
                        $deleteFailed[] = [
                            'id' => $userId,
                            'reason' => is_string($result) ? $result : 'Unknown error'
                        ];
                    }
                } catch (\Exception $e) {
                    $deleteFailed[] = [
                        'id' => $userId,
                        'reason' => $e->getMessage()
                    ];
                }
            }

            // ==========================================
            // PROSES UPDATE (user yang ADA tapi NAMA berubah)
            // ==========================================
            $updateSuccess = [];
            $updateFailed = [];

            foreach ($toUpdateOnMachine as $emp) {
                try {
                    // Gunakan fungsi UPDATE (atau SetUserInfo untuk update)
                    $result = $this->attendanceUnit->update_finger_user_by_rian(
                        $emp['id'],
                        $attendanceUnit['ip'],
                        0,
                        $emp['expected_name_on_machine']
                    );

                    if ($result === true || stripos($result ?? '', 'success') !== false) {
                        $updateSuccess[] = [
                            'id' => $emp['id'],
                            'nama' => $emp['nama'],
                            'badge' => $emp['badge'],
                            'old_name' => $emp['current_name_on_machine'],
                            'new_name' => $emp['expected_name_on_machine']
                        ];
                    } else {
                        $updateFailed[] = [
                            'id' => $emp['id'],
                            'nama' => $emp['nama'],
                            'badge' => $emp['badge'],
                            'reason' => $result,
                            'old_name' => $emp['current_name_on_machine'],
                            'new_name' => $emp['expected_name_on_machine']
                        ];
                    }
                } catch (\Exception $e) {
                    $updateFailed[] = [
                        'id' => $emp['id'],
                        'nama' => $emp['nama'],
                        'badge' => $emp['badge'],
                        'reason' => $e->getMessage(),
                        'old_name' => $emp['current_name_on_machine'],
                        'new_name' => $emp['expected_name_on_machine']
                    ];
                }
            }

            // ==========================================
            // PROSES INSERT (user yang AKTIF tapi belum ada di mesin)
            // ==========================================
            $insertSuccess = [];
            $insertFailed = [];

            // Filter: hanya insert user yang belum ada di mesin DAN belum diupdate
            $alreadyProcessed = array_merge(
                array_column($updateSuccess, 'id'),
                array_column($updateFailed, 'id')
            );
            
            $toInsert = array_diff($missingInMachine, $alreadyProcessed);

            if (!empty($toInsert)) {
                // Ambil detail employee yang perlu di-insert
                $employeesToInsert = $this->hrOutsourcingEmployeeModel
                    ->where('company_id', $companyId)
                    ->where('deletedAt', null)
                    ->whereIn('id', $toInsert)
                    ->findAll();

                foreach ($employeesToInsert as $emp) {
                    try {
                        // Format: "Nama - Badge Number"
                        $nameForMachine = trim($emp['nama']) . ' ' . trim($emp['badge']);
                        
                        $result = $this->attendanceUnit->insert_finger_user_by_rian(
                            $emp['id'],
                            $attendanceUnit['ip'],
                            0, // privilege biasa (bukan admin)
                            $nameForMachine
                        );

                        if ($result === true || stripos($result ?? '', 'success') !== false) {
                            $insertSuccess[] = [
                                'id' => $emp['id'],
                                'nama' => $emp['nama'],
                                'badge' => $emp['badge'],
                                'name_on_machine' => $nameForMachine
                            ];
                        } else {
                            $insertFailed[] = [
                                'id' => $emp['id'],
                                'nama' => $emp['nama'],
                                'badge' => $emp['badge'],
                                'reason' => $result
                            ];
                        }
                    } catch (\Exception $e) {
                        $insertFailed[] = [
                            'id' => $emp['id'],
                            'nama' => $emp['nama'],
                            'badge' => $emp['badge'],
                            'reason' => $e->getMessage()
                        ];
                    }
                }
            }

            // ==========================================
            // RESULT
            // ==========================================
            $totalDelete = count($toDeleteFromMachine);
            $totalUpdate = count($toUpdateOnMachine);
            $totalInsert = count($toInsert);
            $successDelete = count($deleteSuccess);
            $successUpdate = count($updateSuccess);
            $successInsert = count($insertSuccess);

            $message = "Sinkronisasi selesai. ";
            if ($successDelete > 0) $message .= "Berhasil hapus {$successDelete}/{$totalDelete} user yang sudah dihapus di sistem. ";
            if ($successUpdate > 0) $message .= "Berhasil update {$successUpdate}/{$totalUpdate} user. ";
            if ($successInsert > 0) $message .= "Berhasil tambah {$successInsert}/{$totalInsert} user baru.";
            if ($successDelete == 0 && $successUpdate == 0 && $successInsert == 0) {
                $message = "Tidak ada yang perlu disinkronisasi.";
            }

            return response()->setJSON([
                'status' => ($successDelete + $successUpdate + $successInsert) > 0,
                'message' => $message,
                'data' => [
                    'summary' => [
                        'delete_total' => $totalDelete,
                        'delete_success' => $successDelete,
                        'delete_failed' => count($deleteFailed),
                        'update_total' => $totalUpdate,
                        'update_success' => $successUpdate,
                        'update_failed' => count($updateFailed),
                        'insert_total' => $totalInsert,
                        'insert_success' => $successInsert,
                        'insert_failed' => count($insertFailed),
                        'total_active_employees' => count($activeEmployees),
                        'total_deleted_employees' => count($deletedEmployees),
                        'total_machine_users' => count($machineUserIds)
                    ],
                    'delete_failed_details' => $deleteFailed,
                    'update_failed_details' => $updateFailed,
                    'insert_failed_details' => $insertFailed
                ],
                'token' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => "System Error: " . $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function checkSyncStatus()
    {
        try {
            $companyId = $this->request->getVar('company_id');

            if (!$companyId) {
                return $this->fail("Company ID tidak ditemukan");
            }

            // ============================
            // 1. VALIDASI MESIN & KONEKSI
            // ============================
            $company = $this->hrOutsourcingCompanyModel
                ->where('id', $companyId)->first();

            if (!$company || empty($company['ip_finger'])) {
                return $this->fail("IP Finger tidak ditemukan");
            }

            $attendanceUnit = $this->hrOutsourcingAttendanceModel
                ->where('id', $company['ip_finger'])->first();

            if (!$attendanceUnit) {
                return $this->fail("Attendance unit tidak valid");
            }

            // ============================
            // 2. AMBIL DATA USER CI4 (AKTIF) dan CEK BADGE
            // ============================
            $employees = $this->hrOutsourcingEmployeeModel
                ->select('id, nama, badge')
                ->where('company_id', $companyId)
                ->where('deletedAt', null)
                ->findAll();

            // Cek badge yang kosong
            $employeesWithoutBadge = [];
            $employeesWithBadge = [];
            
            foreach ($employees as $emp) {
                if (empty($emp['badge']) || trim($emp['badge']) === '') {
                    $employeesWithoutBadge[] = [
                        'id' => $emp['id'],
                        'nama' => $emp['nama']
                    ];
                } else {
                    $employeesWithBadge[] = $emp;
                }
            }
            
            $dbIds = array_map('strval', array_column($employees, 'id'));

            // ============================
            // 3. AMBIL USER YANG DIHAPUS (SOFT DELETE)
            // ============================
            $builder = $this->hrOutsourcingEmployeeModel->builder();
            $deletedEmployees = $builder
                ->select('id, nama, badge')
                ->where('company_id', $companyId)
                ->where('deletedAt IS NOT NULL')
                ->get()
                ->getResultArray();

            $deletedIds = array_map('strval', array_column($deletedEmployees, 'id'));

            // ============================
            // 4. AMBIL USER DI MESIN
            // ============================
            $machineUsers = [];
            $machineIds = [];
            $machinePrivileges = [];
            $machineNames = [];
            
            try {
                $machineUsers = $this->attendanceUnit->getAllUsersFromMachine($attendanceUnit['ip']);
                
                if (!is_array($machineUsers)) {
                    throw new \Exception("Format data mesin tidak valid");
                }
                
                foreach ($machineUsers as $u) {
                    $pin = strval($u['PIN2']);
                    $machineIds[] = $pin;
                    $machinePrivileges[$pin] = $u['Privilege'] ?? 0;
                    $machineNames[$pin] = $u['Name'] ?? '';
                }
                
            } catch (\Exception $e) {
                return response()->setJSON([
                    'status' => false,
                    'machine_connected' => false,
                    'has_missing_badge' => !empty($employeesWithoutBadge),
                    'missing_badge_count' => count($employeesWithoutBadge),
                    'message' => "Tidak dapat terhubung ke mesin fingerprint",
                    'data' => [
                        'missing_in_machine' => [],
                        'deleted_in_ci4_but_exist_in_machine' => [],
                        'insert_count' => 0,
                        'deleted_ci4_count' => 0,
                        'total_action' => 0,
                        'employees_without_badge' => $employeesWithoutBadge
                    ]
                ]);
            }

            // ============================
            // 5. missing_in_machine
            // User ada di CI4 tapi tidak ada di mesin
            // ============================
            $missingInMachine = [];
            foreach ($employeesWithBadge as $emp) {
                $empId = strval($emp['id']);
                if (!in_array($empId, $machineIds)) {
                    $missingInMachine[] = [
                        'id' => $empId,
                        'nama' => $emp['nama'],
                        'badge' => $emp['badge'],
                        'expected_name_on_machine' => trim($emp['nama']) . ' ' . trim($emp['badge'])
                    ];
                }
            }

            // ============================
            // 6. deleted_in_ci4_but_exist_in_machine
            // User dihapus di CI4 (soft delete) tapi masih ada di mesin
            // ============================
            $deletedInCi4ButExistInMachine = [];
            foreach ($deletedEmployees as $delEmp) {
                $did = strval($delEmp['id']);
                if (in_array($did, $machineIds)) {
                    $deletedInCi4ButExistInMachine[] = [
                        'id' => $did,
                        'nama' => $delEmp['nama'],
                        'badge' => $delEmp['badge'],
                        'current_name_on_machine' => $machineNames[$did] ?? ''
                    ];
                }
            }

            // ============================
            // 7. CEK USER YANG SUDAH ADA DI MESIN TAPI NAMA DI MESIN BELUM PAKE FORMAT BADGE
            // ============================
            $needsUpdateOnMachine = [];
            foreach ($employeesWithBadge as $emp) {
                $empId = strval($emp['id']);
                if (in_array($empId, $machineIds)) {
                    $expectedName = trim($emp['nama']) . ' ' . trim($emp['badge']);
                    $currentName = $machineNames[$empId] ?? '';
                    
                    // Cek jika nama di mesin tidak sesuai format "Nama - Badge"
                    if ($currentName !== $expectedName) {
                        $needsUpdateOnMachine[] = [
                            'id' => $empId,
                            'nama' => $emp['nama'],
                            'badge' => $emp['badge'],
                            'current_name' => $currentName,
                            'expected_name' => $expectedName
                        ];
                    }
                }
            }

            // ============================
            // 8. HITUNG USER LIAR (TANPA TAMPILKAN LIST)
            // ============================
            $liarCount = 0;
            foreach ($machineIds as $mid) {
                // Skip admin mesin
                if (($machinePrivileges[$mid] ?? 0) == 1) {
                    continue;
                }
                
                // Jika bukan employee aktif kita DAN bukan employee yang di-delete kita
                if (!in_array($mid, $dbIds) && !in_array($mid, $deletedIds)) {
                    $liarCount++;
                }
            }

            // ============================
            // 9. TOTAL ACTION & STATS
            // ============================
            $insertCount = count($missingInMachine);
            $deletedCi4Count = count($deletedInCi4ButExistInMachine);
            $updateCount = count($needsUpdateOnMachine);
            $totalAction = $insertCount + $deletedCi4Count + $updateCount;
            $syncedCount = count($employeesWithBadge) - $insertCount;
            $missingBadgeCount = count($employeesWithoutBadge);

            // ============================
            // 10. PRIORITAS WARNING
            // ============================
            $warningLevel = 'success';
            if ($missingBadgeCount > 0) {
                $warningLevel = 'danger'; // Prioritas tertinggi: badge kosong
            } elseif ($deletedCi4Count > 0) {
                $warningLevel = 'danger'; // Harus dihapus dari mesin
            } elseif ($insertCount > 0) {
                $warningLevel = 'warning'; // Harus ditambahkan ke mesin
            } elseif ($updateCount > 0) {
                $warningLevel = 'info'; // Perlu update nama di mesin
            } elseif ($liarCount > 0) {
                $warningLevel = 'info'; // Ada user company lain
            }

            return response()->setJSON([
                'status' => true,
                'machine_connected' => true,
                'warning_level' => $warningLevel,
                'has_missing_badge' => $missingBadgeCount > 0,
                'missing_badge_count' => $missingBadgeCount,
                'data' => [
                    'missing_in_machine' => $missingInMachine, // Array dengan detail
                    'deleted_in_ci4_but_exist_in_machine' => $deletedInCi4ButExistInMachine, // Array dengan detail
                    'needs_update_on_machine' => $needsUpdateOnMachine, // Array dengan detail
                    
                    'insert_count' => $insertCount,
                    'update_count' => $updateCount,
                    'liar_count' => $liarCount,
                    'deleted_ci4_count' => $deletedCi4Count,
                    'synced_count' => $syncedCount,
                    'missing_badge_count' => $missingBadgeCount,
                    
                    'total_action' => $totalAction,
                    'employees_count' => count($employees),
                    'employees_without_badge' => $employeesWithoutBadge,
                    
                    'machine_info' => [
                        'ip' => $attendanceUnit['ip'],
                        'total_users' => count($machineIds),
                        'our_users' => $syncedCount,
                        'other_company_users' => $liarCount
                    ]
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->setJSON([
                'status' => false,
                'machine_connected' => false,
                'message' => "Error: " . $e->getMessage()
            ]);
        }
    }

    private function fail($msg)
    {
        return response()->setJSON([
            'status' => false,
            'message' => $msg
        ]);
    }


}
