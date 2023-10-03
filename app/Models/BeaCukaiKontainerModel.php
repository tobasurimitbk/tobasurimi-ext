<?php

namespace App\Models;

use CodeIgniter\Model;

class BeaCukaiKontainerModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bea_cukai_kontainer';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'bea_cukai_id',
        'jenis_id',
        'ukuran_id',
        'tipe_id',
        'keterangan'
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
            'bea_cukai_kontainer.deletedAt' => null,
            'bea_cukai_kontainer.bea_cukai_id' => $id
        ];

        $builder = $this->db->table('bea_cukai_kontainer')
        ->select('bea_cukai_kontainer.*, jenis.value as jenis_value, jenis.description as jenis_description
        , ukuran.value as ukuran_value, ukuran.description as ukuran_description, tipe.value as tipe_value, tipe.description as tipe_description')
        ->join('metadata as jenis', 'bea_cukai_kontainer.jenis_id = jenis.id', 'left')
        ->join('metadata as ukuran', 'bea_cukai_kontainer.ukuran_id = ukuran.id', 'left')
        ->join('metadata as tipe', 'bea_cukai_kontainer.tipe_id = tipe.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}