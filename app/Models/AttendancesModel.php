<?php

namespace App\Models;

use CodeIgniter\Model;

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
        'employee_id',
        'periode',
        'status',
        'reason',
        'checkin',
        'checkout',
        'isApproved',
        'isPosting'
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
        $statusArr = ['IJIN', 'ALPHA', 'CUTI', 'SAKIT', 'LIBUR', 'HADIR'];
        $date = $year . "-" . $month;

        $resultTotal = [];
        // cek form perizinan
        foreach ($statusArr as $sa) {
            $query = $this->asObject()
                ->select("COUNT(DISTINCT DATE(periode)) as count")
                ->where('employee_id', $employeeID)
                ->where('status', $sa)
                ->where('deletedAt', null)
                ->like('periode', $date)
                ->groupBy('status')
                ->get();

            $row = $query->getRow();

            $resultTotal[$sa] = $row ? $row->count : 0;
        }

        return $resultTotal;
    }
}
