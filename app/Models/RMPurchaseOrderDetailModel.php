<?php

namespace App\Models;

use CodeIgniter\Model;

class RMPurchaseOrderDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_purchase_order_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    public function getPurchaseOrderDetailByPurchaseOrderId($id)
    {
        $arrCondition = [
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.rm_purchase_order_id' => $id
        ];

        $builder = $this->db->table('rm_purchase_order_details')
            ->select('rm_purchase_order_details.*, barangs.nama_barang, barangs.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('barangs', 'barangs.id = rm_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = barangs.satuan_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}
