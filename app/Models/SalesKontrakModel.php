<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesKontrakModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_contract';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sales_contract_id',
        'sales_contract_no',
        'company_id',
        'customer_id',
        'customer_po_no',
        'loading_port',
        'dicharge_port',
        'due_date',
        'total_amount',
        'payment_term',
        'tolerance',
        'shipment_date',
        'documents_required',
        'special_instructions',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_contract_no'     => 'sales_contract.sales_contract_no',
            'customer_name'         => 'customers.name',
            'due_date'              => 'sales_contract.due_date',
            'shipment_date'         => 'sales_contract.shipment_date',
            'createdAt'             => 'sales_contract.createdAt',
            'updatedAt'             => 'sales_contract.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_contract.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_contract.*, 
                      customers.name AS customer_name";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $salesDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $salesDataQry->like('sales_contract.sales_contract_no', $addCondition['search'])
                ->orLike('sales_contract.customer_po_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $salesDataQry->groupEnd();
        }

        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }
}