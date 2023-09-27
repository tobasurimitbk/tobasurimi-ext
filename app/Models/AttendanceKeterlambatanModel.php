<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceKeterlambatanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'attendances_keterlambatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'employee_id',
        'attendances_id',
        'periode',
        'total_jam_keterlambatan',
        'nominal_pengurangan'
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

    public function generate($payrollID, $employeeID, $companyID, $yearMonth)
    {
        // declare model
        $AttendancesModel = new AttendancesModel();

        // delete first
        $this->db->table('attendances_keterlambatan')
            ->where('employee_id', $employeeID)
            ->where('LEFT(periode, 7)', $yearMonth)
            ->delete();

        $attendancesInMonth = $AttendancesModel->where('employee_id', $employeeID)
            ->where('LEFT(periode, 7)', $yearMonth)
            ->findAll();

        foreach ($attendancesInMonth as $p) {

            if ($p['status'] == "HADIR_H") {
                $keterlambatanCheck = static::ketelambatanCheck(
                    $companyID,
                    $p['checkin']
                );

                if ($keterlambatanCheck[0]) {
                    $this->db->table('attendances_keterlambatan')->insert([
                        'company_id' => $companyID,
                        'employee_id' => $employeeID,
                        'attendances_id' => $p['id'],
                        'payroll_id' => $payrollID,
                        'periode' => $p['periode'],
                        'total_jam_keterlambatan' => $keterlambatanCheck[1],
                        'nominal_pengurangan' => 0
                    ]);
                }
            }
        }
    }

    static function ketelambatanCheck($companyID, $checkIN)
    {
        $jamKerjaModel = new JamKerjaModel();
        $result = "-";
        $totalJamTerlambat = "-";

        $jamKerjaDetail = $jamKerjaModel->where('company_id', $companyID)->first();

        if ($jamKerjaDetail !== null && $checkIN != null) {
            $checkInTimestamp = strtotime($checkIN);
            $jamTerlambatTimestamp = strtotime($jamKerjaDetail['jam_terlambat']);

            if ($checkInTimestamp > $jamTerlambatTimestamp) {
                $selisihDetik = $checkInTimestamp - $jamTerlambatTimestamp;
                $jamTerlambat = floor($selisihDetik / 3600); // Jam
                $menitTerlambat = floor(($selisihDetik % 3600) / 60); // Menit

                $totalJamTerlambat = sprintf("%02d Jam %02d Menit", $jamTerlambat, $menitTerlambat);
                $result = 1; // Terlambat
            } else {
                $result = 0; // Tidak terlambat
            }
        }

        return [$result, $totalJamTerlambat];
    }

    public function rekap($payrollID)
    {
        return $this->asArray()->select('attendances_keterlambatan.*, attendances.checkin')
            ->join('attendances', 'attendances.id = attendances_keterlambatan.attendances_id')
            ->where('payroll_id', $payrollID)
            ->findAll();
    }

    public function getTotalRekap($payrollID)
    {
        $total = $this->asArray()->select('SUM(attendances_keterlambatan.nominal_pengurangan) as total')
            ->join('attendances', 'attendances.id = attendances_keterlambatan.attendances_id')
            ->where('payroll_id', $payrollID)
            ->findAll();

        return $total[0]['total'];
    }
}
