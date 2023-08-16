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
        'purchase_request_detail_id',
        'barang_id',
        'spec',
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
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, barangs.nama_barang, barangs.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('barangs', 'barangs.id = rm_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = barangs.satuan_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailByPurchaseRequestDetailId($id)
    {
        $arrCondition = [
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.purchase_request_detail_id' => $id
        ];

        $builder = $this->db->table('rm_purchase_order_details')
            ->select('rm_purchase_orders.po_no,
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, barangs.nama_barang, barangs.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('barangs', 'barangs.id = rm_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = barangs.satuan_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();
        
        return $query->getRow();
    }

    public function getPoBBLokalDetailById($id)
    {
        $selectQry = "rm_purchase_order_details.*,
                        FORMAT(CEILING(rm_purchase_order_details.daily_price), 'N', 'en-us') AS daily_price,
                        FORMAT(CEILING(rm_purchase_order_details.general_price), 'N', 'en-us') AS general_price,
                        FORMAT(CEILING(rm_purchase_order_details.monthly_price), 'N', 'en-us') AS monthly_price,
                        barangs.kode_barang AS kodeBarang,
                        barangs.nama_barang AS barangName,
                        warehouses.warehouse_name AS warehouseName,
                        satuans.id as id_satuan, 
                        satuans.nama_satuan
                        ";

        $condition = [
            "rm_purchase_order_id" => $id,
        ];

        $poBBLokalDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barangs', 'rm_purchase_order_details.barang_id = barangs.id', 'left')
            ->join('warehouses', 'rm_purchase_order_details.bagian = warehouses.id', 'left')
            ->join('satuans', 'satuans.id = barangs.satuan_id', 'left')
            ->findAll();

        return $poBBLokalDetailData;
    }
}
