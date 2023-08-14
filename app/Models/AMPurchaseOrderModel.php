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
    protected $allowedFields    = [
        'id',
        'company_id',
        'purchase_request_id',
        'po_no',
        'po_date',
        'warehouse_id',
        'po_type',
        'currency',
        'supplier_id',
        'total',
        'payment_term',
        'payment_date',
        'dpp',
        'note',
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

    public function getPOList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'           => 'am_purchase_orders.po_date',
            'poNo'             => 'am_purchase_orders.po_no',
            'supplierName'      => 'suppliers.name',
            'total'             => 'am_purchase_orders.total',
            'currencyName'      => 'metadata.value',
            'createdAt'         => 'am_purchase_orders.createdAt',
            'updatedAt'         => 'am_purchase_orders.updatedAt',
            'statusPenerimaan'  => 'am_purchase_orders.status_penerimaan'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "am_purchase_orders.*, 
                      suppliers.name AS supplierName, 
                      metadata.value AS currencyName,
                      COUNT(am_purchase_order_details.id) AS itemCount";
        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency')
            ->join('am_purchase_order_details', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->groupBy(('am_purchase_orders.id'))
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
        suppliers.address AS supplierAddress,
        suppliers.phone AS supplierPhone,
        suppliers.no_npwp AS supplierNPWP,
        users.name AS createdByName,
        companies.company as companyName,
        metadata.value as currencyName,
        FORMAT(CEILING(am_purchase_orders.dpp), 'N', 'en-us') AS dpp,
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('warehouses', 'warehouses.id = am_purchase_orders.warehouse_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('users', 'users.id = purchase_requests.createdBy', 'left')
            ->join('companies', 'companies.id = am_purchase_orders.company_id', 'left')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
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
        $lastStr =  $tgl . $bln . $thn;

        $builder = $this->db->table('am_purchase_orders');
        $builder->select('po_no');
        $builder->orderBy('po_no', 'desc')
            ->where('warehouse_id', $warehouse_id)
            ->where('createdAt >=', $thn . "-" . $bln . "-" . $tgl . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('po_no', $lastStr);
        $query = $builder->get();

        $lastPO = '01';
        if ($query->getResultArray()) {
            $lastFirst = explode('/', $query->getResultArray()[0]['po_no']);
            $lastPO = explode('-', $lastFirst[0]);
            $lastPO = intval($lastPO[1]) + 1;
            $lastPO = sprintf("%02d", $lastPO);
        };

        $generatedNo =  $lastStr . '-' . $lastPO . '/' . $warehouse . '/TOBA/' . $thn2;

        return $generatedNo;
    }
}
