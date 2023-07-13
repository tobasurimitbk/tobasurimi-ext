<?php

namespace App\Models;

use CodeIgniter\Model;

class SppModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'purchase_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
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

    public function getSppList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'spp_type'          => 'purchase_requests.spp_type',
            'spp_no'            => 'purchase_requests.spp_no',
            'warehouse_name'    => 'warehouse.warehouse_name',
            'total'             => 'purchase_requests.total',
            'request_date'      => 'purchase_requests.request_date',
            'createdAt'         => 'purchase_requests.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'purchase_requests.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "purchase_requests.*,
                        warehouses.warehouse_name AS warehouseName, 
                        headwarehouse.name AS approvedByHeadwarehouseName,
                        headpurchasing.name AS approvedByHeadofPurchasingName,
                        director.name AS approvedByDirectorName
                        ";

        $purchaseRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('warehouses', 'purchase_requests.warehouse_id = warehouses.id')
            ->join('users AS headwarehouse', 'purchase_requests.approved_by_headwarehouse = headwarehouse.id')
            ->join('users AS headpurchasing', 'purchase_requests.approved_by_head_of_purchasing = headpurchasing.id')
            ->join('users AS director', 'purchase_requests.approved_by_director = director.id')
            ->orderBy($sort, $sortType);

        $totalData = $purchaseRequestsDataQry->countAllResults(false);

        // $purchaseRequestsDataQry->groupStart();
        // if ($addCondition['search']) {
        //     $purchaseRequestsDataQry
        //         ->like('spp_no', $addCondition['search'])
        //         ->orLike('spp_type', $addCondition['search']);
        // }

        // if ($addCondition['startDate']) {
        //     $purchaseRequestsDataQry->where('purchase_requests.request_date >=',  $addCondition['dateStart'] . '00:00:00');
        // }
        // if ($addCondition['endDate']) {
        //     $purchaseRequestsDataQry->where('purchase_requests.request_date <=', $addCondition['dateEnd'] . '23:59:59');
        // }
        // $purchaseRequestsDataQry->groupEnd();

        if ($addCondition['search']) {
            $purchaseRequestsDataQry->groupStart()
                ->like('spp_no', $addCondition['search'])
                ->orLike('spp_type', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $purchaseRequestsDataQry->countAllResults(false);
        $data = $purchaseRequestsDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
