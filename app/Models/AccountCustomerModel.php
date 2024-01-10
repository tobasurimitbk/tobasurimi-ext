<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountCustomerModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'account_customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_id',
        'ap_id',
        'ar_id',
        'deleted_at',
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
            'name'              => 'customers.name',
            'ap'                => 'account_customers.ap_id',
            'ar'                => 'account_customers.ar_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'id'] ?? 'account_customers.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "account_customers.*, 
        customers.name AS name";
        $accountCustomerDataQry = $this->asObject()
            ->select($selectQry)
            ->where("deleted_at", NULL)
            ->join('customers', 'account_customers.customer_id = customers.id', 'left')
            // ->groupBy(('customers.id'))
            ->orderBy($sort, $sortType);

        $totalData = $accountCustomerDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $accountCustomerDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $accountCustomerDataQry->like('customers.name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $accountCustomerDataQry->groupEnd();
        }

        $totalFilteredData = $accountCustomerDataQry->countAllResults(false);
        $data = $accountCustomerDataQry->findAll($limit, $offset);

        // var_dump($data);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }
}
