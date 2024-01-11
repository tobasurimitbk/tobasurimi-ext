<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountSupplierModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'account_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'supplier_id',
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
            'name'              => 'suppliers.name',
            'ap'                => 'account_supplier.ap_id',
            'ar'                => 'account_supplier.ar_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'id'] ?? 'account_supplier.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "account_supplier.*, 
        suppliers.name AS name";
        $accountSupplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where("deleted_at", NULL)
            ->join('suppliers', 'account_supplier.supplier_id = suppliers.id', 'left')
            // ->groupBy(('customers.id'))
            ->orderBy($sort, $sortType);

        $totalData = $accountSupplierDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $accountSupplierDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $accountSupplierDataQry->like('suppliers.name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $accountSupplierDataQry->groupEnd();
        }

        $totalFilteredData = $accountSupplierDataQry->countAllResults(false);
        $data = $accountSupplierDataQry->findAll($limit, $offset);

        // var_dump($data);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getAccountSupplierForJurnal()
    {
        $select =   "account_supplier.*";
        return $this->asObject()
            ->select($select)
            ->where('account_supplier.deleted_at', null)
            ->findAll();
    }
}
