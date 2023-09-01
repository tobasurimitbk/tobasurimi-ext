<?php

namespace App\Models;

use CodeIgniter\Model;

class BeaCukaiDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bea_cukai_detail';
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
        'po_no',
        'spec',
        'note',
        'unit',
        'qty',
        'price',
        'disc',
        'additional_cost',
        'berat',
        'total_price'
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

    public function getBeaCukaiDetailByBeaCukaiId($id)
    {
        $arrCondition = [
            'bea_cukai_detail.deletedAt' => null,
            'bea_cukai_detail.bea_cukai_id' => $id
        ];

        $builder = $this->db->table('bea_cukai_detail')
        ->select("bea_cukai_detail.*, 
        FORMAT(CEILING(bea_cukai_detail.qty) * CEILING(bea_cukai_detail.price) + CEILING(bea_cukai_detail.additional_cost), 'N', 'en-us') AS totalPrice,
        barangs.nama_barang, barangs.kode_barang, satuans.id as id_satuan, satuans.nama_satuan")
        ->join('barangs', 'barangs.id = bea_cukai_detail.barang_id', 'left')
        ->join('satuans', 'satuans.id = bea_cukai_detail.unit', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();
        
        return $query->getResultArray();
    }
}