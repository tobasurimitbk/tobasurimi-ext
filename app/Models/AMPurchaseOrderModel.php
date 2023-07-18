<?php

namespace App\Models;

use CodeIgniter\Model;

class AMPurchaseOrderModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'am_purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'company_id', 'purchase_request_id', 'po_no', 'po_date', 'warehouse_id',
    'currency', 'supplier_id', 'total', 'payment_term', 'payment_date', 'dpp', 'note', 'is_posted', 'createdBy', 'status_penerimaan'];

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

    public function getPOList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'           => 'am_purchase_orders.po_date',
            'poNo'             => 'am_purchase_orders.po_no',
            'supplierName'      => 'suppliers.name',
            'total'             => 'am_purchase_orders.total',
            'currency'          => 'metadata.value',
            'createdAt'         => 'am_purchase_orders.createdAt',
            'updatedAt'         => 'am_purchase_orders.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "am_purchase_orders.*, 
                      suppliers.name AS supplierName, 
                      metadata.value AS currency";
        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency')
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $poDataQry->like('am_purchase_orders.po_no', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $poDataQry->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $poDataQry->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupEnd();
        }
        
        $totalFilteredData = $poDataQry->countAllResults(false);
        $data = $poDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPOById($id)
    {
        $selectQry = "am_purchase_orders.*,
        purchase_requests.spp_no AS spp_no,
        warehouses.warehouse_name AS warehouseName,
        suppliers.name AS supplierName,
        users.name AS createdByName
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id')
            ->join('warehouses', 'warehouses.id = am_purchase_orders.warehouse_id')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->join('users', 'users.id = purchase_requests.createdBy')
            ->find($id);

        return $sppData;
    }

    public function getNoPenerimaanBarang($po_type, $supplier_id, $company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'po_type' => $po_type,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('am_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();
        
        return $query->getResultArray();
    }

    public function get_no($tgl, $bln, $thn, $warehouse, $thn2, $warehouse_id, $last_day)
    {
        $filt_no = $tgl . $bln . $thn . "-01/" . $warehouse . "/TOBA/" . $thn2;

        $conditions = [
            'warehouse_id' => $warehouse_id,
            'deletedAt' => null
        ];

        $no = $this->db->table('am_purchase_orders')
        ->where('am_purchase_orders.createdAt >=', $thn . "-" . $bln . "-" . $tgl . " 00:00:00")
        ->where('am_purchase_orders.createdAt <=', $last_day . " 23:59:59")
        ->where($conditions)->countAllResults(false) + 1;

        if ($no != '') {
            $filt_no = $tgl . $bln . $thn . "-". sprintf("%02d", $no). "/" . $warehouse . "/TOBA/" . $thn2;
        }
        return $filt_no;
    }
}