<?php

namespace App\Models;

use CodeIgniter\Model;

class HROutsourcingSallaryPaymentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'hr_outsourcing_sallary_payment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'divisi_id',
        'company_id',
        'payment_data',
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
            'hr_outsourcing_sallary_payment.id' => 'hr_outsourcing_sallary_payment.id',
            'hr_outsourcing_sallary_payment.divisi_id' => 'hr_outsourcing_sallary_payment.divisi_id',
            'hr_outsourcing_sallary_payment.company_id' => 'hr_outsourcing_sallary_payment.company_id',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'hr_outsourcing_sallary_payment.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'asc'] ?? 'ASC';

        $selectQry = "hr_outsourcing_sallary_payment.*, divisis.divisi as department, hr_outsourcing_company.name as company";
        $dataQry = $this->asObject()
            ->join('divisis', 'hr_outsourcing_sallary_payment.divisi_id = divisis.id', 'left')
            ->join('hr_outsourcing_company', 'hr_outsourcing_sallary_payment.company_id = hr_outsourcing_company.id', 'left')
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        // if (isset($addCondition['search']) && !empty($addCondition['search'])) {
        //     $dataQry->groupStart()->like('divisis.divisi', $addCondition['search'])
        //     ->orLike('hr_outsourcing_company.name', $addCondition['search']) ->groupEnd();
        // }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalFilteredData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
