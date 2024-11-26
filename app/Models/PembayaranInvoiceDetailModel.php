<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranInvoiceDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pembayaran_invoice_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'pembayaran_invoice_id',
        'sales_order_invoice_id',
        'sales_order_invoice_detail_id',
        'type_invoice',
        'nama_barang',
        'qty',
        'harga_satuan',
        'harga_total',
        'akun_kas_lain',
        'akun_selisih_lain',
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
