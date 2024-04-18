<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderExportModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_order_export';
    protected $primaryKey       = 'sales_order_export_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sales_order_export_id',
        'sales_order_export_no',
        'sales_contract_id',
        'bc_type',
        'company_id',
        'status',
        'keterangan_unpost',
        'jumlah_unpost',
        'used',
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
            'sales_order_export_no' => 'sales_order_export.sales_order_export_no',
            'customer_name'         => 'customers.name',
            'due_date'              => 'sales_contract.due_date',
            'shipment_date'         => 'sales_contract.shipment_date',
            'createdAt'             => 'sales_order_export.createdAt',
            'updatedAt'             => 'sales_order_export.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, sales_contract.*, 
                      customers.name AS customer_name";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['status']) {
            $salesDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $salesDataQry->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('sales_contract.customer_po_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search']);
        }

        if ($addCondition['status']) {
            $salesDataQry
                ->where('status', $addCondition['status']);
        }

        if ($addCondition['search'] || $addCondition['status']) {
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

    public function getById($id)
    {
        $selectQry = "sales_order_export.*, customers.name as customer_name";

        $salesData = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_export.customer_id', 'LEFT')
            ->where('sales_order_export.deletedAt', NULL)
            ->find($id);

        return $salesData;
    }

    public function getBySalesContractId($id)
    {
        $selectQry = "sales_order_export.*, customers.name as customer_name";

        $builder = $this->select($selectQry)
            ->join('customers', 'customers.id = sales_order_export.customer_id', 'LEFT')
            ->where('sales_contract_id', $id)->where('sales_order_export.deletedAt', NULL);
        $query = $builder->get();

        return $query->getResult();
    }
}
