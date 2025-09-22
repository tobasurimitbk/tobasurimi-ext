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
        'id_surat_jalan',
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

    public function getSumAmount($id_invoice)
    {
        $selectQry = "sales_order_invoice_detail.*,
                      SUM(sales_order_invoice_detail.amount_invoice) AS sum_amount_invoice";

        $dataSumAmount = $this->asObject()
            ->select($selectQry)
            ->where('id_sales_order_invoice', $id_invoice)
            ->groupBy('id_sales_order_invoice')
            ->first();

        return $dataSumAmount;
    }
}
