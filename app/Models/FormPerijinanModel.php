<?php

namespace App\Models;

use CodeIgniter\Model;

class FormPerijinanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'form_perijinan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'employee_id',
        'periode',
        'status',
        'reason',
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

    public function getPerijinanList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'employeeName'          => 'employees.name',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'form_perijinan.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "form_perijinan.* ,
            employees.name AS employeeName
            ";

        $formPerijinanQry = $this->asObject()
            ->select($selectQry)
            ->join('employees', 'form_perijinan.employee_id = employees.id')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $formPerijinanQry->countAllResults(false);

        if ($addCondition['search']) {
            $formPerijinanQry->groupStart();
        }

        // if ($addCondition['search']) {
        //     $formPerijinanQry
        //         ->like('employees.name', $addCondition['search']);
        // }

        if ($addCondition['search']) {
            $formPerijinanQry->groupEnd();
        }

        $totalFilteredData = $formPerijinanQry->countAllResults(false);
        $data = $formPerijinanQry->findAll($limit, $offset);

        // var_dump($data);
        // die;

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
