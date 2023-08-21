<?php

namespace App\Models;

use CodeIgniter\Model;

class StockDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'barang_id',
        'warehouse_id',
        'stock_type',
        'qty',
        'stock_date'
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

    public function addStock(int $barangId, int $warehouseId, float $qty, string $type = 'New')
    {
        $data = [
            'barang_id'     => $barangId,
            'warehouse_id'  => $warehouseId,
            'stock_type'    => $type,
            'stock_date'    => date('Y-m-d')
        ];
        $checkStock = $this->asObject()
            ->where($data)
            ->orderBy('stock_date', 'DESC')
            ->first();

        if (!empty($checkStock)) {
            // increment stock
            $this->builder()->where($data)
                ->increment('qty', $qty);
        } else {
            // insert new stock
            $insertData = array_merge($data, ['qty' => $qty]);
            $this->insert($insertData);
        }

    }

    public function reduceStock(int $barangId, int $warehouseId, float $qty, string $type = 'New')
    {
        $data = [
            'barang_id'     => $barangId,
            'warehouse_id'  => $warehouseId,
            'stock_type'    => $type,
            'qty >'         => 0
        ];
        $stockList = $this->asObject()
            ->where($data)
            ->orderBy('stock_date', 'ASC')
            ->findAll();

        foreach ($stockList as $stock) {

            if ($qty > $stock->qty) {
                $qty -= $stock->qty;

                // update stock qty to zero
                $this->where('id', $stock->id)->set('qty', 0)
                    ->update();

            } else {

                // decrement stock qty
                $this->builder()->where('id', $stock->id)
                    ->decrement('qty', $qty);

                $qty = 0;

                break;
            }

        }

    }
}
