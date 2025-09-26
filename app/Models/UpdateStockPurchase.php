<?php

namespace App\Models;

use CodeIgniter\Model;

class UpdateStockPurchase extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'update_stock_purchase';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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
            // 'supplier_id' => 'stock_details2.supplier_id',
            // 'no_aju' => 'stock_details2.no_aju',
            // 'stok_total' => 'stok_total'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'update_stock_purchase.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = '
            update_stock_purchase.*,
            suppliers.name as supplier_name,
            warehouses.warehouse_name,
            divisis.divisi,      
        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = update_stock_purchase.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = update_stock_purchase.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = update_stock_purchase.divisi_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);


        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

}
