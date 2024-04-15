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
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

        if ($addCondition['search'] || $addCondition['status_posting']) {
            $salesDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $salesDataQry->like('sales_contract.sales_contract_no', $addCondition['search'])
                ->orLike('sales_contract.customer_po_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search']);
        }

        if ($addCondition['status_posting']) {
            $salesDataQry
                ->where('status_posting', $addCondition['status_posting']);
        }

        if ($addCondition['search'] || $addCondition['status_posting']) {
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
        $selectQry = "sales_contract.*, customers.name as customer_name";

        $salesData = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_contract.customer_id', 'LEFT')
            ->find($id);

        return $salesData;
    }

    public function get_no($thn, $thn2)
    {
        $lastStr =  "/TOBA/CN/EM/" . $thn2;

        $builder = $this->db->table('sales_contract');
        $builder->select('sales_contract_no');
        $builder->orderBy('sales_contract_no', 'desc')
            ->where('createdAt >=', $thn . "-01-01 00:00:00")
            ->where('createdAt <=', $thn . "-12-31 23:59:59");
        $builder->like('sales_contract_no', $lastStr);
        $query = $builder->get();

        $lastSO = '001';
        if ($query->getResultArray()) {
            $lastFirst = explode('/', $query->getResultArray()[0]['sales_contract_no']);
            $lastSO = explode('-', $lastFirst[0]);
            $lastSO = intval($lastSO[0]) + 1;
            $lastSO = sprintf("%03d", $lastSO);
        };

        $generatedNo =  $lastSO . $lastStr;

        return $generatedNo;
    }

    public function getSalesKontrak($userID)
    {
        $salesKontrak = $this->asArray()
            ->select('sales_contract.*, sales_contract_detail.id AS idContractDetail, sales_contract_detail.qty AS qtyContract, customers.name AS customerName, CONCAT(metadata.value, " - ", metadata.description) AS currencyName')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
            // ->join('sales_order_export', 'sales_order_export.sales_contract_id = sales_contract.id', 'left')
            // ->join('sales_order_detail_export', 'sales_order_detail_export.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
            ->join('sales_contract_detail', 'sales_contract_detail.sales_contract_id = sales_contract.id', 'left')
            ->where('sales_contract.deletedAt', null)
            ->where('sales_contract.createdBy', $userID)
            ->orderBy('sales_contract.createdAt', "DESC")
            ->findAll();
        // foreach ($salesKontrak as &$value) {
        //     $salesOrderExport = $this->asArray()->
        //     if ($value->qtyExport ) {
        //         # code...
        //     }
        // }
        // $salesKontrak = $this->asArray()
        //     ->select('sales_contract.*, customers.name AS customerName, CONCAT(metadata.value, " - ", metadata.description) AS currencyName')
        //     ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
        //     ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
        //     ->join('sales_order_export', 'sales_order_export.sales_contract_id = sales_contract.id', 'left')
        //     ->join('sales_order_detail_export', 'sales_order_detail_export.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
        //     ->join('sales_contract_detail', 'sales_contract_detail.id = sales_order_detail_export.sales_contract_detail_id', 'left')
        //     ->where('sales_contract.deletedAt', null)
        //     ->where('sales_contract.createdBy', $userID)
        //     ->groupBy('sales_contract.id') // Group by sales contract ID
        //     ->having('SUM(sales_order_detail_export.qty) > sales_contract_detail.qty') // Batasi jika jumlah qty pada sales order export detail lebih besar dari jumlah qty pada sales contract detail
        //     ->orderBy('createdAt', "DESC")
        //     ->findAll();
        // var_dump($salesKontrak);
        // exit;
        return $salesKontrak;
    }
}
