<?php

namespace App\Models;

use CodeIgniter\Model;

class UpdateStockPurchaseDetail extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'update_stock_purchase_detail';
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


    public function getDetailByUpdateStockId($id)
    {
        $builder = $this->asArray()
            ->select('
                usp_detail.id,
                usp_detail.update_stock_purchase_id,
                usp_detail.rm_purchase_order_id,
                usp_detail.rm_purchase_order_detail_id,
                usp_detail.stock_id,
                usp_detail.stock_detail2_id,
                usp_detail.spesifikasi_id,
                usp_detail.qty_po as stok_total,
                usp_detail.qty_diterima as stok_total_diterima,
                usp_detail.qty_kotor as stok_total_kotor,
                usp_detail.createdAt,
                usp_detail.updatedAt,
                rm_purchase_orders.po_no,
                rm_purchase_orders.po_date,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                suppliers.name as supplier_name,
                stock_details2.stock_dokumen,
                stock_details2.bc_id,
                stock_details2.no_aju,
                stock_details2.supplier_id,
                stock_details.stock_date,
                stock_details.sumber,
                satuans.kode_satuan,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
      
            ')
            ->from('update_stock_purchase_detail usp_detail')
            ->join('update_stock_purchase usp', 'usp.id = usp_detail.update_stock_purchase_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = usp_detail.rm_purchase_order_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.id = usp_detail.rm_purchase_order_detail_id', 'left')
            ->join('stock', 'stock.id = usp_detail.stock_id', 'left')
            ->join('stock_details2', 'stock_details2.id = usp_detail.stock_detail2_id', 'left')
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->where('usp_detail.update_stock_purchase_id', decrypt($id))
             ->groupBy('
                stock_details2.stock_dokumen,
                stock.id
            ');

        return $builder->findAll();
    }

}
