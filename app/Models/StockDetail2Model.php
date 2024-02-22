<?php

namespace App\Models;

use CodeIgniter\Model;

class StockDetail2Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_details2';
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

    public function insertStokDetail2(
        $bc_id,
        $stok_id,
        $stok_detail_id,
        $qty,
        $no_aju
    ) {
        $stokDetail2 = $this->insert([
            'bc_id' => $bc_id,
            'stock_id' => $stok_id,
            'stock_detail_id' => $stok_detail_id,
            'qty' => $qty,
            'no_aju' => $no_aju
        ]);

        return $stokDetail2;
    }
}
