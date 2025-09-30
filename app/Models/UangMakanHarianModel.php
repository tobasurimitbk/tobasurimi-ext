<?php

namespace App\Models;

use CodeIgniter\Model;

class UangMakanHarianModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'uang_makan_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
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

    public function getUangMakanHarianByDateRangeAmt(
        $employeeIds,
        $startDate,
        $endDate
    ) {
        $uangMakanHarianAll = $this->asArray()
            ->whereIn('employee_id', $employeeIds)
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->where('deletedAt', null)
            ->findAll();

        return $uangMakanHarianAll;
    }

    public function generateUangMakanAmt(
        $employeeIds,
        $startDate,
        $endDate
    ) {
        $uangMakanQry = $this->asArray()
            ->select('SUM(nominal) AS total_nominal, employee_id')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->whereIn('employee_id', $employeeIds)
            ->groupBy('uang_makan_harian.employee_id')
            ->findAll();
        return $uangMakanQry;
    }
}
