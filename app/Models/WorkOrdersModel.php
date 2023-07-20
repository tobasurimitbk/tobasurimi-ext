<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrdersModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'work_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
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

    public function getWorkOrderList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'wo_no' => 'work_orders.wo_no',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'work_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "work_orders.*,
            barangs.nama_barang
        ";

        $workOrdersDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barangs', 'barangs.id = work_orders.barang_id')
            ->orderBy($sort, $sortType);

        $totalData = $workOrdersDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $workOrdersDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $workOrdersDataQry
                ->like('wo_no', $addCondition['search']);
        }
        if ($addCondition['search']) {
            $workOrdersDataQry->groupEnd();
        }

        $totalFilteredData = $workOrdersDataQry->countAllResults(false);
        $data = $workOrdersDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}