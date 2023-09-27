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
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'employee_id',
        'attendances_id',
        'periode',
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
        $this->db->table('form_perizinan_not_approved')
            ->where('employee_id', $employeeID)
            ->where('LEFT(periode, 7)', $yearMonth)
            ->delete();

        $attendancesInMonth = $AttendancesModel->where('employee_id', $employeeID)
            ->where('LEFT(periode, 7)', $yearMonth)
            ->findAll();

        foreach ($attendancesInMonth as $p) {

            if ($p['status'] != "HADIR_H" && !$p['isApproved']) {

                $this->db->table('form_perizinan_not_approved')->insert([
                    'company_id' => $companyID,
                    'employee_id' => $employeeID,
                    'attendances_id' => $p['id'],
                    'payroll_id' => $payrollID,
                    'periode' => $p['periode'],
                    'nominal_pengurangan' => 0
                ]);
            }
        }
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
