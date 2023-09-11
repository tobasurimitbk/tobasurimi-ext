<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payrolls';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'employee_id',
        'year_month',
        'hadir',
        'ijin',
        'alpha',
        'cuti',
        'sakit',
        'libur',
        'isPosted'
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'nip'    => 'employees.nip',
            'name'   => 'employees.name',
            'divisi' => 'divisis.divisi',
            'hadir' => 'payrolls.hadir',
            'ijin' => 'payrolls.ijin',
            'alpha' => 'payrolls.alpha',
            'cuti' => 'payrolls.cuti',
            'sakit' => 'payrolls.sakit',
            'libur' => 'payrolls.libur'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        // Ensure the sort and sortType values are valid
        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'payrolls.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            payrolls.*,
            employees.name AS employeesName,
            employees.nip AS employeesNIP,
            divisis.divisi AS divisiName
            "; // Corrected column names and aliases

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = payrolls.employee_id', 'INNER')
            ->join('divisis', 'divisis.id = employees.division_id', 'LEFT') // Corrected the join condition
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['name'] || $addCondition['divisi']) {
            $dataQry->groupStart();
        }

        if ($addCondition['name']) {
            $dataQry->like('employees.name', $addCondition['name']);
        }

        if ($addCondition['divisi']) {
            $dataQry->like('divisis.divisi', $addCondition['divisi']);
        }

        if ($addCondition['name'] || $addCondition['divisi']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }
}
