<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalPOInvSummaryModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_inv_summaries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'summary_no',
        'supplier_id',
        'due_date',
        'total',
        'summary_status',
        'is_posted'
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

    public function getSummaryList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplierName'  => 'suppliers.name',
            'summaryNo'     => 'local_po_inv_summaries.summary_no',
            'createdAt'     => 'local_po_inv_summaries.createdAt',
            // 'updatedAt'         => 'local_po_inv_summaries.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'local_po_inv_summaries.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "local_po_inv_summaries.*, 
                      DATE_FORMAT(local_po_inv_summaries.due_date, '%d/%m/%Y') AS due_date, 
                      suppliers.name AS supplierName";
        $summaryDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = local_po_inv_summaries.supplier_id')
            // ->join('users', 'users.id = tanda_terima_faktur.user_id')
            ->orderBy($sort, $sortType);

        $totalData = $summaryDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $summaryDataQry->groupStart()
                ->like('suppliers.name', $addCondition['search'])
                ->orLike('summary_no', $addCondition['search'])
            ->groupEnd();
        }
        
        $totalFilteredData = $summaryDataQry->countAllResults(false);
        $data = $summaryDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
