<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderReturnDetailModel extends Model
{

    protected $table      = 'sales_order_return_detail';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;

    protected $allowedFields = [
        'id_sales_order_return',
        'id_barang_return',
        'qty_return',
        'keterangan_return',
        'discount_percentage_return',
        'harga_barang_return',
        'amount_return',
        'tax_return',
        'stock_id',
        'bc_id',
        'no_aju',
        'stock_dokumen',
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
