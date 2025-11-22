<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceKeteranganModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'attendance_keterangan';
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

    public function insertKeteranganBatch($batchKeterangan)
    {
        foreach ($batchKeterangan as $b) {
            $dataKeterangan = $this->asArray()
                ->where('employee_id', $b['employee_id'])
                ->where('tanggal', $b['tanggal'])
                ->first();

            if ($dataKeterangan != null) {
                $this->update($dataKeterangan['id'], [
                    'reason' => $b['reason']
                ]);
            } else {
                $this->insert($b);
            }
        }
    }

    public function getKeteranganByDateRangeAmt(
        $employeeIds,
        $startDate,
        $endDate
    ) {
        $keteranganAll = $this->asArray()
            ->whereIn('employee_id', $employeeIds)
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->where('deletedAt', null)
            ->findAll();

        return $keteranganAll;
    }
}
