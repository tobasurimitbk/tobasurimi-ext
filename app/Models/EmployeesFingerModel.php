<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeesFingerModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'employees_finger';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employees_id',
        'finger'
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;


    public function getTotalByEmployeesId($employees_id)
    {
        $arrCondition = [
            //            'employees.deletedAt' => null,
            'employees_finger.employees_id' => $employees_id,
            //            'users.deletedAt' => null
        ];

        $builder = $this->db->table('employees_finger')
            ->select("employees_finger.employees_id")
            //->where('employees.attendance_sync', 0);
            ->where('employees_id >', 0);
        $builder->where($arrCondition);
        $query = $builder->get();

        $total = count($query->getResultArray());

        return $total;
    }
}
