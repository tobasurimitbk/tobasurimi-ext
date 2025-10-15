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

    public function insertIgnoreAttendanceLog($company_id, $employee_id, $unit_id, $date)
    {
        $builder = $this->db->table('attendances_log');

        $exists = $builder->where([
            'company_id' => $company_id,
            'employees_id' => $employee_id,
            'attendances_unit_id' => $unit_id,
            'date_create' => $date,
        ])->countAllResults();

        if ($exists == 0) {
            return $builder->insert([
                'company_id' => $company_id,
                'employees_id' => $employee_id,
                'attendances_unit_id' => $unit_id,
                'date_create' => $date,
            ]);
        }

        return false;
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

    public function getLogAmt($id, $year, $month)
    {
        $selectQry = "attendances_log.employees_id ,
            DATE_FORMAT(date_create, '%Y-%m-%d') as periode, 
            DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin, 
            DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout, 
            employees.name AS employeeName
            ";

        $attendancesDataQry = $this->asObject()
            ->select($selectQry)
            ->like('date_create', $year . "-" . $month)
            ->join("employees", 'attendances_log.employees_id = employees.id')
            ->where('employees_id', $id)
            ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
            ->findAll();

        return $attendancesDataQry;
    }

    public function getLogAmts($employeeIds, $year, $month)
    {
        $employeeIds = implode(',', $employeeIds); // biar gampang dipakai di IN()

        $sql = "
        SELECT 
            attendances_log.employees_id,
            DATE(attendances_log.date_create) AS periode,
            DATE_FORMAT(MIN(attendances_log.date_create), '%H:%i:%s') AS check_in,
            DATE_FORMAT(MAX(attendances_log.date_create), '%H:%i:%s') AS check_out,
            employees.name,
            form_perijinan.status
        FROM attendances_log
        JOIN employees ON attendances_log.employees_id = employees.id
        LEFT JOIN form_perijinan 
            ON form_perijinan.employee_id = employees.id
            AND form_perijinan.periode = DATE(attendances_log.date_create)
            AND form_perijinan.deletedAt IS NULL
        WHERE DATE_FORMAT(attendances_log.date_create, '%Y-%m') = ?
        AND attendances_log.employees_id IN ($employeeIds)
        GROUP BY attendances_log.employees_id, DATE(attendances_log.date_create)

        UNION

        SELECT 
            form_perijinan.employee_id AS employees_id,
            form_perijinan.periode,
            NULL AS check_in,
            NULL AS check_out,
            employees.name,
            form_perijinan.status
        FROM form_perijinan
        JOIN employees ON form_perijinan.employee_id = employees.id
        WHERE DATE_FORMAT(form_perijinan.periode, '%Y-%m') = ?
        AND form_perijinan.employee_id IN ($employeeIds)
        AND form_perijinan.deletedAt IS NULL

        ORDER BY employees_id, periode
    ";

        return $this->db->query($sql, [$year . '-' . $month, $year . '-' . $month])->getResultArray();
    }

    public function getLogByDateRangeAmts($employeeIds, $startDate, $endDate)
    {
        // pastikan array -> string biar bisa dipakai di IN()
        $employeeIds = implode(',', $employeeIds);

        $sql = "
        SELECT 
            attendances_log.employees_id,
            DATE(attendances_log.date_create) AS periode,
            DATE_FORMAT(MIN(attendances_log.date_create), '%H:%i:%s') AS check_in,
            DATE_FORMAT(MAX(attendances_log.date_create), '%H:%i:%s') AS check_out,
            employees.name,
            form_perijinan.status
        FROM attendances_log
        JOIN employees ON attendances_log.employees_id = employees.id
        LEFT JOIN form_perijinan 
            ON form_perijinan.employee_id = employees.id
            AND form_perijinan.periode = DATE(attendances_log.date_create)
            AND form_perijinan.deletedAt IS NULL
        WHERE DATE(attendances_log.date_create) BETWEEN ? AND ?
        AND attendances_log.employees_id IN ($employeeIds)
        GROUP BY attendances_log.employees_id, DATE(attendances_log.date_create)

        UNION

        SELECT 
            form_perijinan.employee_id AS employees_id,
            form_perijinan.periode,
            NULL AS check_in,
            NULL AS check_out,
            employees.name,
            form_perijinan.status
        FROM form_perijinan
        JOIN employees ON form_perijinan.employee_id = employees.id
        WHERE form_perijinan.periode BETWEEN ? AND ?
        AND form_perijinan.employee_id IN ($employeeIds)
        AND form_perijinan.deletedAt IS NULL

        ORDER BY employees_id, periode
        ";

        return $this->db->query($sql, [$startDate, $endDate, $startDate, $endDate])->getResultArray();
    }
}
