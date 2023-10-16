<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierHargaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'supplier_harga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'supplier_id',
        'bahan_baku_id',
        'bagian_id',
        'spesifikasi',
        'harga_umum',
        'harga_harian',
        'harga_bulanan',
        'createdAt',
        'updatedAt',
        'deletedAt'
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

    public function getBySupplierId($id)
    {
        $arrCondition = [
            'supplier_harga.deletedAt' => null,
            'supplier_harga.supplier_id' => $id
        ];

        $builder = $this->db->table('supplier_harga')->select('supplier_harga.*, warehouses.warehouse_name as bagian_name, barang_master.barang_name');
        $builder->join('warehouses', 'supplier_harga.bagian_id = warehouses.id', 'left')
        ->join('barang_master', 'supplier_harga.bahan_baku_id = barang_master.id', 'left')
        ->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}