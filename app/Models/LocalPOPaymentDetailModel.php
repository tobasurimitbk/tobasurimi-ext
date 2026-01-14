<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalPOPaymentDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_payment_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'local_po_payment_id',
        'penerimaan_barang_id',
        'tanda_terima_faktur_id',
        'penerimaan_barang_detail_id',
        'rm_purchase_order_id',
        'rm_purchase_order_details_id',
        'total',
        'total_pay_pph',
        'tipe',
        'potongan',
        'tambahan',
        'potongan_debit_account_id',
        'potongan_credit_account_id',
        'tambahan_debit_account_id',
        'tambahan_credit_account_id'
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
}
