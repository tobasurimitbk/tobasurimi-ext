<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderInvoiceDetailModel extends Model
{

    protected $table      = 'sales_order_invoice_detail';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;

    protected $allowedFields = [
        'id_sales_order_invoice',
        'id_barang_invoice',
        'qty_invoice',
        'qty_invoice_awal',
        'qty_invoice_sisa',
        'keterangan_invoice',
        'discount_percentage_invoice',
        'harga_barang_invoice',
        'tax_invoice',
        'amount_invoice',
        'id_sales_order',
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
}
