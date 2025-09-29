<?php

namespace App\Models;

use CodeIgniter\Model;

class StockRevampDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_revamp_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
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

    public function getStockListWithAddConditionForUpdateStock($condition, $spesifikasiId)
    {
        $builder = $this->asArray()
            ->select('
                stock_revamp_detail.id AS id,
                stock_revamp.spesifikasi_id,
                suppliers.name AS supplier_name,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                rm_purchase_orders.supplier_id,
                stock_revamp_detail.reference_type,
                rm_purchase_orders.po_date,
                rm_purchase_orders.po_no,
                rm_purchase_orders.id as rm_purchase_order_id,
                rm_purchase_order_details.id as rm_purchase_order_detail_id,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                stock_revamp_detail.qty_bersih AS total_penerimaan,
                stock_revamp_detail.qty_bersih as stok_total,
                stock_revamp_detail.qty_diterima as stok_total_diterima,
            ')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left');

        // Kondisi dinamis
        foreach ($condition as $field => $value) {
            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }

        return $builder
            ->groupBy('
                stock_revamp_detail.po_id,
                stock_revamp.id
            ')
            ->whereIn('stock_revamp.spesifikasi_id', $spesifikasiId)
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }

    public function getStockListWithAddConditionForUpdateStocWithId($id)
    {
        $builder = $this->asArray()
            ->select('
                stock_revamp_detail.id AS id,
                stock_revamp.spesifikasi_id,
                suppliers.name AS supplier_name,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                rm_purchase_orders.supplier_id,
                stock_revamp_detail.reference_type,
                rm_purchase_orders.po_date,
                rm_purchase_orders.po_no,
                rm_purchase_orders.id as rm_purchase_order_id,
                rm_purchase_order_details.id as rm_purchase_order_detail_id,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                stock_revamp_detail.qty_bersih AS total_penerimaan,
                stock_revamp_detail.qty_bersih as stok_total,
                stock_revamp_detail.qty_diterima as stok_total_diterima,
            ')
            ->join('update_stock_purchase_detail', 'update_stock_purchase_detail.stock_detail_id = stock_revamp_detail.id')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left');

        return $builder
            ->groupBy('
                stock_revamp_detail.po_id,
                stock_revamp.id
            ')
            ->where('update_stock_purchase_detail.update_stock_purchase_id', $id)
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }

    public function getStockListWithAddConditionForProsesRebus($condition, $spesifikasiId)
    {
        $builder = $this->asArray()
            ->select('
                stock_revamp_detail.id AS id,
                stock_revamp.spesifikasi_id,
                suppliers.name AS supplier_name,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                rm_purchase_orders.supplier_id,
                stock_revamp_detail.reference_type,
                rm_purchase_orders.po_date,
                rm_purchase_orders.po_no,
                rm_purchase_orders.id as rm_purchase_order_id,
                rm_purchase_order_details.id as rm_purchase_order_detail_id,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                stock_revamp_detail.qty_bersih AS total_penerimaan,
                stock_revamp_detail.qty_bersih as stok_total,
                stock_revamp_detail.qty_diterima as stok_total_diterima,
            ')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left');

        // Kondisi dinamis
        foreach ($condition as $field => $value) {
            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }

        return $builder
            ->groupBy('
                stock_revamp_detail.po_id,
                stock_revamp.id
            ')
            ->where('stock_revamp.spesifikasi_id', $spesifikasiId)
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }
}
