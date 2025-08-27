<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasterSpesifikasiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'barang_master_spesifikasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'barang_master_id',
        'spesifikasi',
        'is_primer',
        'satuan_1',
        'satuan_2',
        'konversi_satuan_2',
        'satuan_3',
        'konversi_satuan_3',
        'harga_pokok',
        'harga_jual',
        'supplier_terakhir',
        'harga_terakhir',
        'createdAt',
        'updatedAt',
        'deletedAt',
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

    public function getBarangSpesifikasiByBarangMasterID($barangMasterID)
    {
        $arrCondition = [
            'barang_master_spesifikasi.deletedAt' => null,
            'barang_master_spesifikasi.barang_master_id' => $barangMasterID
        ];

        $selectQry = "barang_master_spesifikasi.*";
        $data = $this->select($selectQry)
            ->where($arrCondition)
            ->findAll();

        return $data;
    }
}
