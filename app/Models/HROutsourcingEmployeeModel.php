<?php

namespace App\Models;

use CodeIgniter\Model;

class HROutsourcingEmployeeModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'hr_outsourcing_employee';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'badge',
        'company_id',
        'nama',
        'tanggal_masuk_kerja',
        'createdAt',
        'updatedAt',
        'deletedAt'
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'hr_outsourcing_employee.id' => 'hr_outsourcing_employee.id',
            'hr_outsourcing_employee.tanggal_masuk_kerja' => 'hr_outsourcing_employee.tanggal_masuk_kerja',
            'hr_outsourcing_employee.nama' => 'hr_outsourcing_employee.nama',
            'hr_outsourcing_employee.badge' => 'hr_outsourcing_employee.badge'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'hr_outsourcing_employee.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'asc'] ?? 'ASC';

        $selectQry = "hr_outsourcing_employee.*";
        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->where('hr_outsourcing_employee.deletedAt', null)
            ->orderBy($sort, $sortType);

        if ($addCondition['search']) {
            $dataQry->groupStart()
                ->like('hr_outsourcing_employee.nama', $addCondition['search'])
                ->orLike('hr_outsourcing_employee.badge', $addCondition['search'])
                ->orLike('hr_outsourcing_employee.tanggal_masuk_kerja', $addCondition['search'])
            ->groupEnd();
        }


        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalFilteredData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
