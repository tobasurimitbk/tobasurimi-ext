<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23BarangDokumenModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23_barang_dokumen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
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

    public function get($bc23BarangID)
    {
        $result = [];
        foreach ($this->where('bc_23_barang_id', $bc23BarangID)->where('deletedAt', null)->findAll() as $r) {
            $result[] = [
                'id' => $r['id'],
                'no_seri_dokumen' => $r['no_seri_dokumen']
            ];
        }
        return $result;
    }
}
