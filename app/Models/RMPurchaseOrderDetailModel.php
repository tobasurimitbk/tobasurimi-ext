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
    protected $allowedFields    = [
        'rm_purchase_order_id',
        'supplier_harga_id',
        'satuan_id',
        'bagian',
        'peti',
        'quality',
        'note',
        'qty',
        'qty_diterima',
        'remaining_qty',
        'general_price',
        'daily_price',
        'monthly_price',
        'createdAt',
        'updatedAt',
        'deletedAt',
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

    public function getPurchaseOrderDetailByPurchaseOrderId($id)
    {
        $arrCondition = [
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.rm_purchase_order_id' => $id
        ];

        $builder = $this->db->table('rm_purchase_order_details')
            ->select('rm_purchase_orders.po_no,
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, supplier_harga.spesifikasi, bagian.nama_bagian, 
            barang_master.kode_barang, barang_master.barang_name as nama_barang, barang_master.id as barang_id, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('barang_master', 'supplier_harga.bahan_baku_id = barang_master.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left')
            ->join('bagian', 'rm_purchase_order_details.bagian = bagian.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailById($id)
    {
        $arrCondition = [
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.id' => $id
        ];

        $builder = $this->db->table('rm_purchase_order_details')
            ->select('rm_purchase_orders.po_no,
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, supplier_harga.spesifikasi, bagian.nama_bagian, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left')
            ->join('bagian', 'rm_purchase_order_details.bagian = bagian.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getPoBBLokalDetailById($id)
    {
        $selectQry = "rm_purchase_order_details.*, supplier_harga.spesifikasi, bagian.nama_bagian, satuans.id as id_satuan, satuans.nama_satuan";

        $condition = [
            "rm_purchase_order_id" => $id,
        ];

        $poBBLokalDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left')
            ->join('bagian', 'rm_purchase_order_details.bagian = bagian.id', 'left')
            ->findAll();

        return $poBBLokalDetailData;
    }
}
