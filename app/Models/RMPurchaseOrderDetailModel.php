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
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, barang_master.barang_name as nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('barang_master', 'barang_master.id = rm_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = barang_master.satuan_id', 'left');
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
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, barang_master.barang_name as nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('barang_master', 'barang_master.id = rm_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = barang_master.satuan_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getPoBBLokalDetailById($id)
    {
        $selectQry = "rm_purchase_order_details.*,
                        barang_master.kode_barang AS kodeBarang,
                        barang_master.barang_name AS barangName,
                        divisis.divisi AS divisiName,
                        satuans.id as id_satuan, 
                        satuans.nama_satuan
                        ";

        $condition = [
            "rm_purchase_order_id" => $id,
        ];

        $poBBLokalDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barang_master', 'rm_purchase_order_details.barang_id = barang_master.id', 'left')
            ->join('divisis', 'rm_purchase_order_details.bagian = divisis.id', 'left')
            ->join('satuans', 'satuans.id = barang_master.satuan_id', 'left')
            ->findAll();

        return $poBBLokalDetailData;
    }
}
