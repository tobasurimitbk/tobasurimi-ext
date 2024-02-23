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
    protected $protectFields    = false;
    protected $allowedFields    = [
        'stock_id',
        'qty',
        'status',
        'stock_date',
        'created_by',
        'sumber',
        'no_dokumen',
        'keterangan',

        'barang_id',
        'warehouse_id',
        'stock_type',
        'spesifikasi',
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

    public function addOrReduceStock($barangID, $warehouseID, $stockType = 'New', $qty, $status = 'IN', $spesifikasi)
    {
        $this->insert([
            'barang_id' => $barangID,
            'warehouse_id' => $warehouseID,
            'stock_type' => $stockType,
            'qty' => $qty,
            'status' => $status,
            'spesifikasi' => $spesifikasi,
            'stock_date' => date('Y-m-d')
        ]);
    }

    public function insertStokDetail(
        $stok_id,
        $qty,
        $status, // IN, OUT
        $stok_date,
        $created_by,
        $sumber, // [ADJUSMENT,INISIASI,LPB,MUTASI,PRODUKSI,PENJUALAN]
        $no_dokumen, // NO PO, NO LPB, NO PRODUKSI
        $keterangan
    ) {
        $stokDetail = $this->insert([
            'stock_id' => $stok_id,
            'qty' => $qty,
            'status' => $status,
            'stock_date' => $stok_date,
            'created_by' => $created_by,
            'sumber' => $sumber,
            'no_dokumen' => $no_dokumen,
            'keterangan' => $keterangan
        ]);

        return $stokDetail;
    }
}
