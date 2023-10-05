<?php

namespace App\Models;

use CodeIgniter\Model;

class BeaCukaiDokumenModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bea_cukai_dokumen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'bea_cukai_id',
        'dokumen_id',
        'no_dokumen',
        'date',
        'note'
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
            'bea_cukai_dokumen.deletedAt' => null,
            'bea_cukai_dokumen.bea_cukai_id' => $id
        ];

        $builder = $this->db->table('bea_cukai_dokumen')
        ->select('bea_cukai_dokumen.*, metadata.value, metadata.description')
        ->join('metadata', 'bea_cukai_dokumen.dokumen_id = metadata.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}