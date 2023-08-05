<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportPOPaymentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'import_po_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'payment_no',
        'payment_type',
        'supplier_id',
        'po_type',
        'po_id',
        'penerimaan_barang_id',
        'voucher_no',
        'currency',
        'payment_amt',
        'current_exchange_rate',
        'payment_date',
        'termin',
        'payment_method',
        'note'
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
            'kode'              => 'import_po_payments.payment_no',
            'name'              => 'suppliers.name',
            'postal_code'       => 'suppliers.postal_code',
            'createdAt'         => 'import_po_payments.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'import_po_payments.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "import_po_payments.*, 
                      DATE_FORMAT(import_po_payments.payment_date, '%d/%m/%Y') AS payment_date,
                      metadata.value AS currency,
                      suppliers.name AS supplier_name";
        $paymentDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'metadata.id = import_po_payments.currency')
            ->join('suppliers', 'suppliers.id = import_po_payments.supplier_id')
            ->orderBy($sort, $sortType);

        $totalData = $paymentDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $paymentDataQry->groupStart()
                ->like('suppliers.name', $addCondition['search'])
                ->orLike('import_po_payments.payment_no', $addCondition['search'])
                ->orLike('metadata.value', $addCondition['search'])
            ->groupEnd();
        }

        // date filter start
        if ($addCondition['startDate']) {
            $paymentDataQry->where('import_po_payments.payment_date >=', $addCondition['startDate']);
        }

        if ($addCondition['lastDate']) {
            $paymentDataQry->where('import_po_payments.payment_date <=', $addCondition['lastDate']);
        }
        // date filter end
        
        $totalFilteredData = $paymentDataQry->countAllResults(false);
        $data = $paymentDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
