<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangSupplierModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'barang_suppliers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'barang_id',
        'supplier_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

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

    public function deleteByBarangId($id)
    {
        $arrCondition = [
            'barang_id' => $id
        ];

        $builder = $this->db->table('barang_suppliers');
        $builder->where($arrCondition);
        $query = $builder->delete();

        return $query;
    }

    public function getByBarangId($id)
    {
        $arrCondition = [
            'barang_id' => $id
        ];

        $builder = $this->db->table('barang_suppliers');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}