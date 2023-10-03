<?php

namespace App\Models;

use CodeIgniter\Model;

class BeaCukaiKemasanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bea_cukai_kemasan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'bea_cukai_id',
        'jumlah',
        'kemasan_id',
        'uraian',
        'merk'
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
            'bea_cukai_kemasan.deletedAt' => null,
            'bea_cukai_kemasan.bea_cukai_id' => $id
        ];

        $builder = $this->db->table('bea_cukai_kemasan')
        ->select('bea_cukai_kemasan.*, metadata.value, metadata.description')
        ->join('metadata', 'bea_cukai_kemasan.kemasan_id = metadata.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}