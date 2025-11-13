<?php

namespace App\Models;

use CodeIgniter\Model;

class FormPerizinanNotApprovedModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'form_perizinan_not_approved';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [
        'company_id',
        'employee_id',
        'attendances_id',
        'periode',
        'nominal_pengurangan',
        'year_month'
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

    // return total approved and not approved
    public function generate($payrollID, $employeeID, $companyID, $yearMonth, $startDate, $endDate)
    {
        // declare return type
        $res = [
            'total_perizinan_not_approved' => 0,
            'total_perizinan_approved' => 0
        ];

        // declare model
        $attendancesModel = new AttendancesModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();

        // delete first
        $this->db->table('form_perizinan_not_approved')
            ->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->delete();

        $attendancesInMonth = $attendancesModel->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->groupStart()
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupEnd()
            ->findAll();

        // nominal pengurangan adalah gajiharian + cadangan yha misal ga di acc
        // get gaji harian first
        $gajiHarian = $payrollGajiModel->select('payroll_gaji_conjunction.nominal')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_gaji_harian', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->first();

        // get nominal uang cadangan
        $gajiCadangan = $payrollGajiModel->select('payroll_gaji_conjunction.nominal')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_cadangan', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->first();

        $nominalGajiHarian = ($gajiHarian != null) ? $gajiHarian['nominal'] : 0;
        $nominalGajiCadangan = ($gajiCadangan != null) ? $gajiCadangan['nominal'] : 0;

        foreach ($attendancesInMonth as $p) {

            if ($p['status'] != "HADIR_H" && !$p['isApproved']) {
                // not approved
                $this->db->table('form_perizinan_not_approved')->insert([
                    'company_id' => $companyID,
                    'employee_id' => $employeeID,
                    'attendances_id' => $p['id'],
                    'payroll_id' => $payrollID,
                    'periode' => $p['periode'],
                    'year_month' => $yearMonth,
                    'nominal_pengurangan' => ($nominalGajiHarian + $nominalGajiCadangan)
                ]);
                $res['total_perizinan_not_approved']++;
            } elseif ($p['status'] != "HADIR_H" && $p['isApproved'] && $p['status'] != "LIBUR_L" && $p['status'] != "ALPHA_A") {
                // approved
                $res['total_perizinan_approved']++;
            }
        }

        return $res;
    }

    public function generateAmt(
        $mapStatusAttendance,
        $mapEmployeePayroll,
        $mapGajiHarian,
        $mapGajiCadangan,
        $employeeIds,
        $companyId,
        $yearMonth,
        $startDate,
        $endDate
    ) {
        $attendancesModel = new AttendancesModel();

        // Hapus data lama dulu
        $this->db->table('form_perizinan_not_approved')
            ->whereIn('employee_id', $employeeIds)
            ->where('year_month', $yearMonth)
            ->delete();

        // Ambil absensi dalam range
        $attendancesInMonth = $attendancesModel
            ->whereIn('employee_id', $employeeIds)
            ->where('year_month', $yearMonth)
            ->groupStart()
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupEnd()
            ->findAll();

        $dataResult = [];       // untuk insert batch ke form_perizinan_not_approved
        $dataResultTotal = [];  // kumpulan total per employee

        // Init data total per employee
        foreach ($employeeIds as $eid) {
            $dataResultTotal[$eid] = [
                'id' => $mapEmployeePayroll[$eid] ?? null,
                'employee_id' => $eid,
                'total_perizinan_not_approved' => 0,
                'total_perizinan_approved' => 0,
                'hadir_final' => $mapStatusAttendance[$eid]["HADIR_H"] ?? 0
            ];
        }

        foreach ($attendancesInMonth as $p) {
            $eid = $p['employee_id'];

            // Not approved → simpan ke table form_perizinan_not_approved
            if ($p['status'] !== "HADIR_H" && !$p['isApproved']) {
                $dataResult[] = [
                    'company_id' => $companyId,
                    'employee_id' => $eid,
                    'attendances_id' => $p['id'],
                    'payroll_id' => $mapEmployeePayroll[$eid] ?? null,
                    'periode' => $p['periode'],
                    'year_month' => $yearMonth,
                    'nominal_pengurangan' => ($mapGajiHarian[$eid] ?? 0) + ($mapGajiCadangan[$eid] ?? 0)
                ];
                $dataResultTotal[$eid]['total_perizinan_not_approved']++;
            }

            // Approved → hitung total approved
            if ($p['isApproved'] == 1 && $p['status'] !== "ALPHA_A" && $p['status'] !== "LIBUR_L" && $p['status'] !== "HADIR_H") {
                $dataResultTotal[$eid]['hadir_final']++;
                $dataResultTotal[$eid]['total_perizinan_approved']++;
                // Hadir_final hanya tambah jika bukan hari Minggu
                // if ($dayOfWeek != 0) {
                //     $dataResultTotal[$eid]['hadir_final']++;
                // }
            }
        }

        // Tambahkan total_perizinan_approved ke hadir_final
        // foreach ($employeeIds as $eid) {
        //     $dataResultTotal[$eid]['hadir_final'] += $dataResultTotal[$eid]['total_perizinan_approved'];
        // }

        return [
            'dataFormPerizinan' => $dataResult,                // insert batch
            'dataFormPerizinanTotal' => array_values($dataResultTotal) // kumpulan per employee
        ];
    }

    public function rekap($payrollID)
    {
        return $this->asArray()->select('form_perizinan_not_approved.*, attendances.status')
            ->where('payroll_id', $payrollID)
            ->join('attendances', 'attendances.id = form_perizinan_not_approved.attendances_id')
            ->findAll();
    }

    public function getTotalRekap($payrollID)
    {
        $data = $this->asArray()->select('SUM(form_perizinan_not_approved.nominal_pengurangan) as total')
            ->where('payroll_id', $payrollID)
            ->join('attendances', 'attendances.id = form_perizinan_not_approved.attendances_id')
            ->findAll();

        return $data[0]['total'];
    }
}
