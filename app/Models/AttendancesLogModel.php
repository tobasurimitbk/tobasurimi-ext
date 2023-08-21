<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendancesLogModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'attendances_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'employees_id',
        'attendances_unit_id',
        'date_create',
    ];

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

    public function get_by_company_employee_unit_date($company_id, $employee_id, $unit_id, $date)
    {
        $arrCondition = [
            //            'employees.deletedAt' => null,
            'attendances_log.company_id' => $company_id,
            'attendances_log.employees_id' => $employee_id,
            'attendances_log.attendances_unit_id' => $unit_id,
            'attendances_log.date_create' => $date,
            //            'users.deletedAt' => null
        ];

        $builder = $this->db->table('attendances_log')
            ->select("attendances_log.id")
            //->where('employees.attendance_sync', 0);
            ->where('attendances_log.id >', 0);
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function get_all($year, $month)
    {
        $selectQry = "attendances_log.* ,
            employees.name AS employeeName
            ";

        $attendancesDataQry = $this->asObject()
            ->select($selectQry)
            ->like('date_create', $year . "-" . $month)
            ->join("employees", 'attendances_log.employees_id = employees.id')
            ->findAll();


        return $attendancesDataQry;
    }
}
