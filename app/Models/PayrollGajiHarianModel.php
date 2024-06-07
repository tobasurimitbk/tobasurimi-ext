<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollGajiHarianModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payroll_gaji_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    public function generateGajiHarianPegawai($payrollID, $companyID, $employeeID, $yearMonth, $startDate, $endDate)
    {
        // declare model
        $AttendancesModel = new AttendancesModel();
        // delete first
        $this->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->delete();

        $attendancesInMonth = $AttendancesModel->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->groupStart()
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupEnd()
            ->findAll();

        foreach ($attendancesInMonth as $p) {
            if ($p['status'] == "HADIR_H") {
            }
        }
    }

    static function totalJamKerja($attendanceId)
    {
        // Jika plg diatas jam 12 hitungannya :
        // Jam plg - jam msk -    pause = hasil
        // Kemudian u upah gaji 1 hr dibagi 7 jam krja di kali hasil dari pengurangan tsb.
        // jika plg dibawah jm 12 atau batas spi jm 12 hitungannya
        // Jam plg - jam msk = hasil
        // Kemudian u upah gaji 1 hr dibagi 7 jam krja di kali hasil dari pengurangan tsb.
        // declare model
        $attendancesModel = new AttendancesModel();
        $employeeJamKerjaModel = new EmployeeJamKerjaModel();

        $attendance = $attendancesModel->find($attendanceId);
        $jamKerjaDetail = $employeeJamKerjaModel->getJamKerjaDetailByEmployeeId($attendance['id'], $attendance['employee_id']);

        $checkIn = $attendance['checkin'];
        $jamIstirahatMulai = $jamKerjaDetail['jam_istirahat_mulai'];
        $jamIstirahatSelesai = $jamKerjaDetail['jam_istirahat_selesai'];
        $checkOut = $attendance['checkout']; // SAMPAI DIA CHECKOUT NGAB


    }
}
