<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductionResultModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'production_results';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'work_order_id',
        'warehouse_id',
        'pr_no',
        'receive_date'
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

    public function getProductResultList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'productionCode' => 'production_results.pr_no',
            'workOrderCode' => 'suppliers.kode',
            'barangCode'    => 'barangs.kode_barang',
            'barangName'    => 'barangs.nama_barang',
            'warehouseName' => 'warehouses.warehouse_name',
            'createdAt'     => 'production_results.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'production_results.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "production_results.*, 
                      DATE_FORMAT(production_results.receive_date, '%d/%m/%Y') AS receive_date,
                      warehouses.warehouse_name AS warehouseName, 
                      work_orders.wo_no AS wo_no,
                      barang_master.kode_barang AS barangCode,
                      barang_master.barang_name AS barangName
                      ";
        $productionResDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('warehouses', 'warehouses.id = production_results.warehouse_id')
            ->join('work_orders', 'work_orders.id = production_results.work_order_id')
            ->join('work_order_details', 'work_order_details.work_order_id = production_results.work_order_id')
            ->join('barang_master', 'barang_master.id = work_order_details.barang1_id')
            ->orderBy($sort, $sortType);

        $totalData = $productionResDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $productionResDataQry->groupStart()
                ->like('production_results.pr_no', $addCondition['search'], 'after')
                ->orLike('work_orders.wo_no', $addCondition['search'], 'after')
                ->orLike('barangs.nama_barang', $addCondition['search'], 'after')
                ->groupEnd();
        }

        $totalFilteredData = $productionResDataQry->countAllResults(false);
        $data = $productionResDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
