<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesKontrakDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_contract_detail';
    protected $primaryKey       = 'sales_contract_detail_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sales_contract_detail_id',
        'sales_contract_id',
        'barang_id',
        'qty',
        'unit',
        'remark',
        'price',
        'total_price',
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

    public function getSalesContractDetailBySalesContractId($id)
    {
        $arrCondition = [
            'sales_contract_detail.deletedAt' => null
        ];

        $builder = $this->db->table('sales_contract_detail')
        ->select('sales_contract_detail.*, barangs.nama_barang, barangs.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
        ->join('barangs', 'barangs.id = sales_contract_detail.barang_id', 'left')
        ->join('satuans', 'satuans.id = sales_contract_detail.unit', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();
        
        return $query->getResultArray();
    }
}