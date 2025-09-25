<?php

namespace App\Models;

use CodeIgniter\Model;

class BCBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function totalHargaPenyerahan($bcPurchaseOrderID)
    {
        $total = 0;
        foreach ($this->asArray()->where('bc_purchase_order_id', $bcPurchaseOrderID)->findAll() as $t) {
            $total += $t['harga_ekspor'];
        }
        return $total;
    }

    public function totalBeratBersih($bcPurchaseOrderID)
    {
        $total = 0;
        foreach ($this->asArray()->where('bc_purchase_order_id', $bcPurchaseOrderID)->findAll() as $t) {
            $total += $t['netto'];
        }
        return $total;
    }

    public function totalVolume($bcPurchaseOrderID)
    {
        $total = 0;
        foreach ($this->asArray()->where('bc_purchase_order_id', $bcPurchaseOrderID)->findAll() as $t) {
            $total += $t['volume'];
        }
        return $total;
    }
}
