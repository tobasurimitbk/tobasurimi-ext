<?php

namespace App\Models;

use CodeIgniter\Model;
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
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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

    public function generate($employeeData, $startDate, $endDate, $year, $month, $companyID)
    {
        $AttendanceModel = new AttendancesModel();
        $FormPerijinanModel = new FormPerijinanModel();
        $hariLiburModel = new BigDaysModel();
        $AttendancesLogModel = new AttendancesLogModel();

        try {
            $startDateTimestamp = strtotime($startDate);
            $endDateTimestamp = strtotime($endDate);

            $allDates = array();
            while ($startDateTimestamp <= $endDateTimestamp) {
                $currentDate = date('Y-m-d', $startDateTimestamp);
                $allDates[] = $currentDate;
                $startDateTimestamp += 86400;
            }

            // loop employee
            foreach ($employeeData as $e) {
                // loop date
                foreach ($allDates as $dates) {
                    // chek apakah data izin
                    $formPerizinan = $FormPerijinanModel->where('periode', $dates)
                        ->where('employee_id', $e['id'])
                        ->where('deletedAt', null)
                        ->first();
                    // check adakah data 
                    $hariLibur = $hariLiburModel->where('date', $dates)->first();
                    $selectQry = "DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
                        DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout";

                    $logAttandance = $AttendancesLogModel
                        ->select($selectQry)
                        ->where('employees_id', $e['id'])
                        ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $dates)
                        ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
                        ->limit(2)
                        ->get()
                        ->getResult();

                    if ($hariLibur != null || date('l', strtotime($dates)) == "Sunday" && $formPerizinan == null && \count($logAttandance) == 0) {
                        // ada hari libur
                        $AttendanceModel->insert([
                            'company_id' => $companyID,
                            'division_id' => $e['division_id'],
                            'employee_id' => $e['id'],
                            'periode' => $dates,
                            'status' => "LIBUR_L",
                            'reason' => '',
                            'year_month' => $year . "-" . $month
                        ]);
                    } elseif ($formPerizinan != null) {
                        // ada perizinan 
                        $AttendanceModel->insert([
                            'company_id' => $companyID,
                            'division_id' => $e['division_id'],
                            'employee_id' => $e['id'],
                            'periode' => $dates,
                            'status' => $formPerizinan['status'],
                            'reason' => $formPerizinan['reason'],
                            'year_month' => $year . "-" . $month,
                            'isApproved' => $formPerizinan['is_approval']
                        ]);
                    } elseif ($formPerizinan == null) {
                        // tidak ada data perizinan jadi
                        // get attendance by date and employee by log

                        if (\count($logAttandance) == 0) {
                            // rekap absen tidak ditemukan
                            // set jadi ALPHA
                            $AttendanceModel->insert([
                                'company_id' => $companyID,
                                'division_id' => $e['division_id'],
                                'employee_id' => $e['id'],
                                'periode' => $dates,
                                'status' => 'ALPHA_A',
                                'year_month' => $year . "-" . $month
                            ]);
                        } else {
                            // data absen ada di log
                            if ($logAttandance[0]->checkout != $logAttandance[0]->checkin) {
                                // ada attandance (in dan out)
                                // create in
                                $AttendanceModel->insert([
                                    'company_id' => $companyID,
                                    'division_id' => $e['division_id'],
                                    'employee_id' => $e['id'],
                                    'periode' => $dates,
                                    'checkin' => \date('H:i:s', \strtotime($logAttandance[0]->checkin)), // in
                                    'checkout' => \date('H:i:s', \strtotime($logAttandance[0]->checkout)), // out
                                    'status' => 'HADIR_H',
                                    'year_month' => $year . "-" . $month
                                ]);
                            } else {
                                // ada attandance only(in)
                                $AttendanceModel->insert([
                                    'company_id' => $companyID,
                                    'division_id' => $e['division_id'],
                                    'employee_id' => $e['id'],
                                    'periode' => $dates,
                                    'checkin' => \date('H:i:s', \strtotime($logAttandance[0]->checkin)), // in
                                    'checkout' => \date('H:i:s', \strtotime($logAttandance[0]->checkout)), // out
                                    'status' => 'HADIR_H',
                                    'year_month' => $year . "-" . $month
                                ]);
                            }
                        }
                    }
                }
            }
            return [
                'status' => true,
                'message' => ''
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function detectIfGenerate($yearMonth, $employeeID)
    {
        $res = $this->asArray()->where('employee_id', $employeeID)->where('year_month', $yearMonth)->findAll();
        return (count($res) == 0) ? false : true;
    }

    public function triwulanPDF($yearMonth, $divisionID, $companyID)
    {
        $res = [];
        $employeeModel = new EmployeesModel();
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $companyID,
            'divisis.id' => $divisionID,
            'divisis.deletedAt' => null,
            'employees.status' => "Aktif",
            'employees.gender' => "Wanita"
        ];

        $employeeData = $employeeModel->select('*')
            ->select("employees.*, divisis.divisi, bagian.nama_bagian")
            ->join('divisis', 'employees.division_id = divisis.id', 'left')
            ->join('bagian', 'employees.bagian_id = bagian.id', 'left')
            ->groupStart()->where($arrCondition)->groupEnd()
            ->get()
            ->getResultArray();

        foreach ($employeeData as $ed) {
            $totalAPH = 0;
            $maxCupon = 15;
            $totalCutiHaid = 0;
            $kehadiran = [];
            foreach ($yearMonth as $ym) {
                $kehadiran[] = [
                    'yearMonth' => $ym,
                    'A' => static::hitungKehadiranSebulan($ym, $ed['id'], ['ALPHA_A', 'LIBUR_L']),
                    'P' => static::hitungKehadiranSebulan($ym, $ed['id'], ['CUTI TAHUNAN_CT', 'CUTI HAID_CHD', 'CUTI HAMIL_CHL', 'CUTI MELAHIRKAN_CM', 'IJIN_I', 'SAKIT_S']),
                    'H' => static::hitungKehadiranSebulan($ym, $ed['id'], ['RL_RL', 'HADIR_H'])
                ];

                $totalCutiHaid += static::hitungKehadiranSebulan($ym, $ed['id'], ['CUTI HAID_CHD']);
            }

            foreach ($kehadiran as $k) {
                // $totalAPH += ($k['A'] + $k['P'] + $k['H']);
                $totalAPH += ($k['P']);
            }

            if ($totalCutiHaid == 1) {
                $maxCupon = $maxCupon - 8;
            } elseif ($totalCutiHaid == 2) {
                $maxCupon = $maxCupon - 10;
            } elseif ($totalCutiHaid == 3) {
                $maxCupon = $maxCupon - 12;
            } elseif ($totalCutiHaid >= 4) {
                $maxCupon = 0;
            }

            $res[] = [
                'id' => $ed['id'],
                'name' => $ed['name'],
                'kehadiran' => $kehadiran,
                'totalAPH' => $totalAPH,
                'totalKupon' => $maxCupon
            ];
        }

        return [
            'res' => $res
        ];
    }


    static function hitungKehadiranSebulan($yearMonth, $employeeID, $status)
    {
        $AttendanceModel = new AttendancesModel();
        $res = $AttendanceModel
            ->select('status, COUNT(DISTINCT DATE(periode)) as count')
            ->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->whereIn('status', $status)
            ->where('deletedAt', null)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $resultCount = 0;

        foreach ($res as $row) {
            $count = $row['count'];
            $resultCount += $count;
        }

        return $resultCount;
    }
}
