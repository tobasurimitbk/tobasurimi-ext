<?php

namespace App\Models;

use CodeIgniter\Model;

class BeaCukaiBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bea_cukai_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'bea_cukai_id',
        'barang_id',
        'kategori_id'
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

    public function getByBeaCukaiId($id)
    {
        $arrCondition = [
            'bea_cukai_barang.deletedAt' => null,
            'bea_cukai_barang.bea_cukai_id' => $id
        ];

        $builder = $this->db->table('bea_cukai_barang')
        ->select('bea_cukai_barang.*, barangs.kode_barang, barangs.nama_barang, hs_codes.code as kode_hs, 
        kategori_barang.value as nama_kategori_barang, kategori_barang.description as code')
        ->join('barangs', 'bea_cukai_barang.barang_id = barangs.id', 'left')
        ->join('hs_codes', 'barangs.hs_id = hs_codes.id', 'left')
        ->join('metadata as kategori_barang', 'bea_cukai_barang.kategori_id = kategori_barang.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}