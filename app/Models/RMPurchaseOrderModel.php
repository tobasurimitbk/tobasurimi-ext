<?php

namespace App\Models;

use CodeIgniter\Model;

class RMPurchaseOrderModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'po_no',
        'po_date',
        'supplier_id',
        'pph',
        'potong_kg',
        'cong_sebenarnya',
        'cong_batasan',
        'subsidi_langsung',
        'is_posted',
        'createdBy',
        'status_penerimaan'
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

    public function getNoPenerimaanBarang($supplier_id, $company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('rm_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPoBBList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'po_date'          => 'rm_purchase_orders.po_date',
            'po_no'            => 'rm_purchase_orders.po_no',
            'supplier_name'    => 'suppliers.supplier_name',
            'createdAt'         => 'rm_purchase_orders.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rm_purchase_orders.*, 
            suppliers.name AS supplierName, 
            COUNT(rm_purchase_order_details.id) AS itemCount";

        $bbLokalDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'rm_purchase_orders.supplier_id = suppliers.id')
            ->join('rm_purchase_order_details', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'right')
            ->groupBy(('rm_purchase_orders.id'))
            ->orderBy($sort, $sortType);

        $totalData = $bbLokalDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $bbLokalDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $bbLokalDataQry
                ->like('po_no', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $bbLokalDataQry->where('rm_purchase_orders.request_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $bbLokalDataQry->where('rm_purchase_orders.request_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $bbLokalDataQry->groupEnd();
        }

        $totalFilteredData = $bbLokalDataQry->countAllResults(false);
        $data = $bbLokalDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
