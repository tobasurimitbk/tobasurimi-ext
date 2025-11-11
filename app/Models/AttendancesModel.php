<?php

namespace App\Models;

use CodeIgniter\Model;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;

class AttendancesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'attendances';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'company_id',
        'division_id',
        'employee_id',
        'periode',
        'checkin',
        'checkout',
        'status',
        'reason',
        'year_month',
        'isApproved',
        'abaikan_sync_log'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getAttendances($periode, $employeeID)
    {
        $builder = $this->asObject()
            ->select('*')
            ->where('attendances.employee_id', $employeeID)
            ->where('attendances.periode', $periode)
            ->orderBy('id', "DESC")
            ->first();
        return $builder;
    }

    public function getStatusAttendances($year, $month, $employeeID)
    {
        $modelMetaData = new MetadataModel();
        $statusArr = [];
        foreach ($modelMetaData->where('name', "Status Perizinan")->findAll() as $s) {
            array_push($statusArr, $s['value']);
        }
        $yearMonth = $year . "-" . $month;

        $resultTotal = [];
        // cek form perizinan
        foreach ($statusArr as $sa) {
            $query = $this->asObject()
                ->select("COUNT(DISTINCT DATE(periode)) as count")
                ->where('employee_id', $employeeID)
                ->where('status', $sa)
                ->where('deletedAt', null)
                ->where('year_month', $yearMonth)
                ->groupBy('status')
                ->get();

            $row = $query->getRow();

            $resultTotal[$sa] = $row ? $row->count : 0;
        }

        return $resultTotal;
    }

    public function getStatusAttendancesInRange($startDate, $endDate, $employeeID)
    {
        $modelMetaData = new MetadataModel();
        $statusArr = [];
        foreach ($modelMetaData->where('name', "Status Perizinan")->findAll() as $s) {
            array_push($statusArr, $s['value']);
        }

        $resultTotal = [];
        // cek form perizinan
        foreach ($statusArr as $sa) {
            $query = $this->asObject()
                ->select("COUNT(DISTINCT DATE(periode)) as count")
                ->where('employee_id', $employeeID)
                ->where('status', $sa)
                ->where('deletedAt', null)
                ->groupStart()
                ->where('periode >=', $startDate)
                ->where('periode <=', $endDate)
                ->groupEnd()
                ->groupBy('status')
                ->get();

            $row = $query->getRow();

            $resultTotal[$sa] = $row ? $row->count : 0;
        }

        return $resultTotal;
    }

    public function getStatusAttendancesInRangeAmt($startDate, $endDate, $employeeIds)
    {
        $query = $this->asArray()
            ->select("COUNT(DISTINCT DATE(periode)) as total, status, employee_id")
            ->whereIn('employee_id', $employeeIds)
            ->where('deletedAt', null)
            ->groupStart()
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupEnd()
            ->groupBy(['employee_id', 'status']);

        return $query->findAll();
    }

    public function getTotalHariLiburEmployeeHadir(
        $startDate,
        $endDate,
        $companyId,
        $employeeIds
    ) {
        // 1️⃣ Ambil semua hari libur dari tabel BigDays
        $bigDaysModel = new BigDaysModel();
        $bigDays = $bigDaysModel
            ->where('company_id', $companyId)
            ->where('deletedAt', null)
            ->findAll();

        $mapBigDays = [];
        foreach ($bigDays as $b) {
            $mapBigDays[$b['date']] = true;
        }

        // 2️⃣ Buat daftar tanggal hari libur (termasuk Minggu)
        $begin = new DateTime($startDate);
        $end   = new DateTime($endDate);
        $end->modify('+1 day');

        $interval = new DateInterval('P1D');
        $period   = new DatePeriod($begin, $interval, $end);

        $mapHariLibur = [];
        foreach ($period as $date) {
            $current = $date->format('Y-m-d');
            $dayOfWeek = $date->format('w'); // 0 = Minggu

            // Tambah Minggu sebagai hari libur
            if ($dayOfWeek == 0) {
                $mapHariLibur[$current] = true;
            }

            // Tambah dari BigDays
            if (isset($mapBigDays[$current])) {
                $mapHariLibur[$current] = true;
            }
        }

        // 3️⃣ Ambil data kehadiran karyawan dalam rentang tanggal
        $attendanceList = $this->getAttendanceByDateRangeAmt(
            $employeeIds,
            $startDate,
            $endDate
        );

        // 4️⃣ Inisialisasi hasil
        $mapTotalLiburMasuk = [];
        foreach ($employeeIds as $eid) {
            $mapTotalLiburMasuk[$eid] = 0;
        }

        // 5️⃣ Loop kehadiran
        foreach ($attendanceList as $e) {
            $eid = $e['employee_id'];
            $tanggal = $e['periode'];

            // ✅ Cek apakah hari libur (Minggu atau BigDay)
            $isHariLibur = isset($mapHariLibur[$tanggal]);

            // ✅ Cek apakah karyawan hadir dan disetujui
            $isHadir = !in_array($e['status'], ['ALPHA_A', 'LIBUR_L']);
            $isApproved = !empty($e['isApproved']);

            // Hitung hanya jika: hari libur + hadir + disetujui
            if ($isHariLibur && $isHadir && $isApproved) {
                $mapTotalLiburMasuk[$eid]++;
            }
        }

        return $mapTotalLiburMasuk;
    }

    public function getTotalEmployeeHadirNotApproved(
        $startDate,
        $endDate,
        $employeeIds
    ) {
        $attendanceList = $this->getAttendanceByDateRangeAmt(
            $employeeIds,
            $startDate,
            $endDate
        );

        $mapTotalMasukNotApproved = [];
        foreach ($employeeIds as $eid) {
            $mapTotalMasukNotApproved[$eid] = 0;
        }

        foreach ($attendanceList as $e) {
            $eid = $e['employee_id'];

            // ✅ Cek apakah karyawan hadir dan disetujui
            $isHadir = !in_array($e['status'], ['ALPHA_A', 'LIBUR_L']);
            $isApproved = !empty($e['isApproved']);

            if ($isHadir && !$isApproved) {
                $mapTotalMasukNotApproved[$eid]++;
            }
        }

        return $mapTotalMasukNotApproved;
    }


    // public function generate($employeeData, $startDate, $endDate, $year, $month, $companyID)
    // {
    //     $AttendanceModel = new AttendancesModel();
    //     $FormPerijinanModel = new FormPerijinanModel();
    //     $hariLiburModel = new BigDaysModel();
    //     $AttendancesLogModel = new AttendancesLogModel();

    //     try {
    //         $startDateTimestamp = strtotime($startDate);
    //         $endDateTimestamp = strtotime($endDate);

    //         $allDates = array();
    //         while ($startDateTimestamp <= $endDateTimestamp) {
    //             $currentDate = date('Y-m-d', $startDateTimestamp);
    //             $allDates[] = $currentDate;
    //             $startDateTimestamp += 86400;
    //         }

    //         // loop employee
    //         foreach ($employeeData as $e) {
    //             // loop date
    //             foreach ($allDates as $dates) {
    //                 // chek apakah data izin
    //                 $formPerizinan = $FormPerijinanModel->where('periode', $dates)
    //                     ->where('employee_id', $e['id'])
    //                     ->where('deletedAt', null)
    //                     ->first();
    //                 // check adakah data 
    //                 $hariLibur = $hariLiburModel->where('date', $dates)->first();
    //                 $selectQry = "DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
    //                     DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout";

    //                 $logAttandance = $AttendancesLogModel
    //                     ->select($selectQry)
    //                     ->where('employees_id', $e['id'])
    //                     ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $dates)
    //                     ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
    //                     ->limit(2)
    //                     ->get()
    //                     ->getResult();

    //                 if ($hariLibur != null || date('l', strtotime($dates)) == "Sunday" && $formPerizinan == null && \count($logAttandance) == 0) {
    //                     // ada hari libur
    //                     $AttendanceModel->insert([
    //                         'company_id' => $companyID,
    //                         'division_id' => $e['division_id'],
    //                         'employee_id' => $e['id'],
    //                         'periode' => $dates,
    //                         'status' => "LIBUR_L",
    //                         'reason' => '',
    //                         'year_month' => $year . "-" . $month
    //                     ]);
    //                 } elseif ($formPerizinan != null) {
    //                     // ada perizinan 
    //                     $AttendanceModel->insert([
    //                         'company_id' => $companyID,
    //                         'division_id' => $e['division_id'],
    //                         'employee_id' => $e['id'],
    //                         'periode' => $dates,
    //                         'status' => $formPerizinan['status'],
    //                         'reason' => $formPerizinan['reason'],
    //                         'year_month' => $year . "-" . $month,
    //                         'isApproved' => $formPerizinan['is_approval']
    //                     ]);
    //                 } elseif ($formPerizinan == null) {
    //                     // tidak ada data perizinan jadi
    //                     // get attendance by date and employee by log

    //                     if (\count($logAttandance) == 0) {
    //                         // rekap absen tidak ditemukan
    //                         // set jadi ALPHA
    //                         $AttendanceModel->insert([
    //                             'company_id' => $companyID,
    //                             'division_id' => $e['division_id'],
    //                             'employee_id' => $e['id'],
    //                             'periode' => $dates,
    //                             'status' => 'ALPHA_A',
    //                             'year_month' => $year . "-" . $month
    //                         ]);
    //                     } else {
    //                         // data absen ada di log
    //                         if ($logAttandance[0]->checkout != $logAttandance[0]->checkin) {
    //                             // ada attandance (in dan out)
    //                             // create in
    //                             $AttendanceModel->insert([
    //                                 'company_id' => $companyID,
    //                                 'division_id' => $e['division_id'],
    //                                 'employee_id' => $e['id'],
    //                                 'periode' => $dates,
    //                                 'checkin' => \date('H:i:s', \strtotime($logAttandance[0]->checkin)), // in
    //                                 'checkout' => \date('H:i:s', \strtotime($logAttandance[0]->checkout)), // out
    //                                 'status' => 'HADIR_H',
    //                                 'year_month' => $year . "-" . $month
    //                             ]);
    //                         } else {
    //                             // ada attandance only(in)
    //                             $AttendanceModel->insert([
    //                                 'company_id' => $companyID,
    //                                 'division_id' => $e['division_id'],
    //                                 'employee_id' => $e['id'],
    //                                 'periode' => $dates,
    //                                 'checkin' => \date('H:i:s', \strtotime($logAttandance[0]->checkin)), // in
    //                                 'checkout' => \date('H:i:s', \strtotime($logAttandance[0]->checkout)), // out
    //                                 'status' => 'HADIR_H',
    //                                 'year_month' => $year . "-" . $month
    //                             ]);
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         return [
    //             'status' => true,
    //             'message' => ''
    //         ];
    //     } catch (Exception $e) {
    //         return [
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    public function generate($employeeData, $startDate, $endDate, $year, $month, $companyID)
    {
        $AttendanceModel     = new AttendancesModel();
        $FormPerijinanModel  = new FormPerijinanModel();
        $hariLiburModel      = new BigDaysModel();
        $AttendancesLogModel = new AttendancesLogModel();

        try {
            // Siapkan yang diabaikan dulu
            $attendanceAbaikan =  $AttendanceModel
                ->where('periode >=', $startDate)
                ->where('periode <=', $endDate)
                ->where('company_id', $this->this_company_id)
                ->where('abaikan_sync_log', "yes")
                ->findAll();

            $abaikanMap = [];
            foreach ($attendanceAbaikan as $a) {
                $abaikanMap[$a['employee_id']][$a['periode']] = true;
            }

            // --- 1. siapkan range tanggal
            $allDates = [];
            $startDateTimestamp = strtotime($startDate);
            $endDateTimestamp   = strtotime($endDate);

            while ($startDateTimestamp <= $endDateTimestamp) {
                $allDates[] = date('Y-m-d', $startDateTimestamp);
                $startDateTimestamp += 86400;
            }

            $employeeIds = array_column($employeeData, 'id');

            // --- 2. load semua perizinan (sekali query)
            $formPerizinanAll = $FormPerijinanModel
                ->whereIn('employee_id', $employeeIds)
                ->where('deletedAt', null)
                ->where('periode >=', $startDate)
                ->where('periode <=', $endDate)
                ->findAll();

            $mapPerizinan = [];
            foreach ($formPerizinanAll as $izin) {
                $mapPerizinan[$izin['employee_id']][$izin['periode']] = $izin;
            }

            // --- 3. load semua hari libur (sekali query)
            $hariLiburAll = $hariLiburModel
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->findAll();

            $setHariLibur = array_column($hariLiburAll, 'date');
            $setHariLibur = array_flip($setHariLibur); // untuk cepat cek isset()

            // --- 4. load semua log attendance (sekali query)
            $logs = $AttendancesLogModel
                ->select("
                employees_id,
                DATE(date_create) as tgl,
                DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
                DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout
            ")
                ->whereIn('employees_id', $employeeIds)
                ->where('date_create >=', $startDate . ' 00:00:00')
                ->where('date_create <=', $endDate . ' 23:59:59')
                ->groupBy('employees_id, DATE(date_create)')
                ->findAll();

            $mapLog = [];
            foreach ($logs as $l) {
                $mapLog[$l['employees_id']][$l['tgl']] = [
                    'checkin'  => $l['checkin'],
                    'checkout' => $l['checkout']
                ];
            }



            // --- 5. loop employee × tanggal (tanpa query)
            $batchInsert = [];

            foreach ($employeeData as $e) {
                foreach ($allDates as $dates) {
                    $izin  = $mapPerizinan[$e['id']][$dates] ?? null;
                    $log   = $mapLog[$e['id']][$dates] ?? null;
                    $libur = isset($setHariLibur[$dates]) || date('w', strtotime($dates)) == 0;

                    if ($libur && !$izin && !$log) {
                        $status = "LIBUR_L";
                        $reason = "";
                        $checkin = $checkout = null;
                    } elseif ($izin) {

                        $status  = $izin['status'];
                        $reason  = $izin['reason'];
                        $checkin = $checkout = null;
                    } elseif (!$log) {
                        $status = "ALPHA_A";
                        $reason = "";
                        $checkin = $checkout = null;
                    } else {
                        $status  = "HADIR_H";
                        $reason  = "";
                        $checkin = $log['checkin'];
                        $checkout = $log['checkout'];
                    }

                    if (empty($abaikanMap[$e['id']][$dates])) {
                        // yang 
                        $batchInsert[] = [
                            'company_id'  => $companyID,
                            'division_id' => $e['division_id'],
                            'employee_id' => $e['id'],
                            'periode'     => $dates,
                            'checkin'     => $checkin,
                            'checkout'    => $checkout,
                            'status'      => $status,
                            'reason'      => $reason,
                            'year_month'  => $year . "-" . $month,
                            'isApproved'  => $izin['is_approval'] ?? 1
                        ];
                    }
                }
            }

            // --- 6. insert batch biar cepat
            if ($batchInsert) {
                $AttendanceModel->insertBatch($batchInsert, 500);
            }

            return ['status' => true, 'message' => ''];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }


    public function getTriwulan($yearMonth, $divisionID, $companyID)
    {
        $res = [];
        $employeeModel = new EmployeesModel();

        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $companyID,
            'employees.division_id' => $divisionID,
            'employees.gender' => "Wanita"
        ];

        // Ambil semua employee
        $employeeData = $employeeModel->select("employees.*, divisis.divisi, bagian.nama_bagian")
            ->join('divisis', 'employees.division_id = divisis.id', 'left')
            ->join('bagian', 'employees.bagian_id = bagian.id', 'left')
            ->where($arrCondition)
            ->get()
            ->getResultArray();

        if (!$employeeData) {
            return ['res' => []];
        }

        $employeeIDs = array_column($employeeData, 'id');

        // ✅ Ambil semua attendance sekaligus
        $AttendanceModel = new AttendancesModel();
        $attendanceData = $AttendanceModel
            ->select('employee_id, year_month, status, COUNT(DISTINCT DATE(periode)) as count')
            ->whereIn('employee_id', $employeeIDs)
            ->whereIn('year_month', $yearMonth)
            ->where('deletedAt', null)
            ->groupBy('employee_id, year_month, status')
            ->get()
            ->getResultArray();

        // Buat index agar lookup cepat
        $attendanceIndex = [];
        foreach ($attendanceData as $row) {
            $attendanceIndex[$row['employee_id']][$row['year_month']][$row['status']] = $row['count'];
        }

        // Mulai proses tiap employee
        foreach ($employeeData as $ed) {
            $totalAPH = 0;
            $maxCupon = 15;
            $totalCutiHaid = 0;
            $kehadiran = [];

            foreach ($yearMonth as $ym) {
                $A = static::sumStatus($attendanceIndex, $ed['id'], $ym, ['ALPHA_A', 'LIBUR_L']);
                $P = static::sumStatus($attendanceIndex, $ed['id'], $ym, ['CUTI TAHUNAN_CT', 'CUTI HAID_CHD', 'CUTI HAMIL_CHL', 'CUTI MELAHIRKAN_CM', 'POTONG GAJI_PG', 'SAKIT_S', 'DINAS_D']);
                $H = static::sumStatus($attendanceIndex, $ed['id'], $ym, ['RL_RL', 'HADIR_H']);

                $kehadiran[] = [
                    'yearMonth' => $ym,
                    'A' => $A,
                    'P' => $P,
                    'H' => $H,
                ];

                $totalCutiHaid += static::sumStatus($attendanceIndex, $ed['id'], $ym, ['CUTI HAID_CHD']);
                $totalAPH += $P; // sesuai kode lama
            }

            // Hitung max coupon
            if ($totalCutiHaid == 1) {
                $maxCupon -= 8;
            } elseif ($totalCutiHaid == 2) {
                $maxCupon -= 10;
            } elseif ($totalCutiHaid == 3) {
                $maxCupon -= 12;
            } elseif ($totalCutiHaid >= 4) {
                $maxCupon = 0;
            }

            $res[] = [
                'id' => $ed['id'],
                'name' => $ed['name'],
                'nip' => $ed['nip'],
                'kehadiran' => $kehadiran,
                'totalAPH' => $totalAPH,
                'totalKupon' => $maxCupon
            ];
        }

        return ['res' => $res];
    }

    // Helper untuk lookup data attendance
    static function sumStatus($attendanceIndex, $employeeID, $yearMonth, $statuses)
    {
        $count = 0;
        foreach ($statuses as $s) {
            if (isset($attendanceIndex[$employeeID][$yearMonth][$s])) {
                $count += $attendanceIndex[$employeeID][$yearMonth][$s];
            }
        }
        return $count;
    }


    public function getAttendanceAmt($employeeIds, $year, $month)
    {
        $yearMonth = $year . "-" . $month;
        $selectQry = "attendances.*";
        $dataQry = $this->asArray()
            ->select($selectQry)
            ->whereIn('employee_id', $employeeIds)
            ->where('deletedAt', null)
            ->where("DATE_FORMAT(periode, '%Y-%m')", $yearMonth)
            ->groupBy('periode')
            ->groupBy('employee_id')
            ->orderBy('periode')
            ->findAll();

        return $dataQry;
    }

    public function getAttendanceByDateRangeAmt(
        $employeeIds,
        $startDate,
        $endDate
    ) {
        return $this->asArray()
            ->select('attendances.*')
            ->whereIn('employee_id', $employeeIds)
            ->where('deletedAt', null)
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupBy('periode')
            ->groupBy('employee_id')
            ->orderBy('periode')
            ->findAll();
    }
}
