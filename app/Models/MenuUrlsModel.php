<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuUrlsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'menu_urls';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'name',
        'parent_id',
        'url',
        'sort_no',
        'icon',
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

    public function get_menu_url($id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'parent_id' => $id ? $id : 0
        ];

        $builder = $this->db->table('menu_urls');
        $builder->where($arrCondition)
        ->orderBy('sort_no', 'ASC');
        $query = $builder->get();
        
        return $query->getResultArray();
    }
}