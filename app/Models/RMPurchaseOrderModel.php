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
}
