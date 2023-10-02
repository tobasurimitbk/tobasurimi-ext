<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalPOPaymentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payment_no',
        'supplier_id',
        'local_po_inv_summary_id',
        'due_date',
        'amount',
        'payment_date',
        'payment_method'
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

    public function getPaymentList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'payment_no'        => 'local_po_payments.payment_no',
            'due_date'          => 'local_po_payments.due_date',
            'payment_date'      => 'local_po_payments.payment_date',
            'payment_method'    => 'local_po_payments.payment_method',
            'amount'            => 'local_po_payments.amount',
            'createdAt'         => 'local_po_payments.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "local_po_payments.id AS id,
                      local_po_payments.payment_no AS payment_no, 
                      DATE_FORMAT(local_po_payments.due_date, '%d/%m/%Y') AS due_date, 
                      DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y') AS payment_date, 
                      local_po_payments.amount AS amount,
                      local_po_payments.payment_method AS payment_method,
                      suppliers.name AS supplierName";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id')
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] != "" || $addCondition['dateStart'] != "" || $addCondition['dateEnd'] != "") {
            $supplierDataQry->groupStart();
        }

        if ($addCondition['search'] != "") {
            $supplierDataQry
                ->where('payment_no', $addCondition['search'])
                ->orWhere('suppliers.name', $addCondition['search']);
        }

        if ($addCondition['dateStart'] != "" || $addCondition['dateEnd'] != "") {
            $supplierDataQry
                ->where("DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y')", $addCondition['dateStart'])
                ->orWhere("DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y')", $addCondition['dateEnd']);
        }

        if ($addCondition['search'] != "" || $addCondition['dateStart'] != "" || $addCondition['dateEnd'] != "") {
            $supplierDataQry->groupEnd();
        }

        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
