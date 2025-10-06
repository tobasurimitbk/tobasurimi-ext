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
                stock_revamp_detail.id,
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
                update_stock_purchase_detail.qty_diterima as stok_total_diterima,
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
                stock_revamp_detail.id,
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
                satuans.id as satuan_id,
                stock_revamp_detail.qty_bersih AS total_penerimaan,
                stock_revamp_detail.qty_bersih as stok_total_bersih,
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
                stock_revamp_detail.id,
            ')
             ->where('stock_revamp_detail.reference_type !=', "PROSES REBUS")
            ->where('stock_revamp.spesifikasi_id', $spesifikasiId)
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }


    public function getStockListWithAddConditionForJasaVendorIn($condition, $stock_detail_id)
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
                satuans.id as satuan_id,
                stock_revamp_detail.qty_bersih AS total_penerimaan,
                stock_revamp_detail.qty_bersih as stok_total_bersih,
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
                stock_revamp_detail.id,
            ')
            ->where('stock_revamp.stock_detail_id', $stock_detail_id)
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }


    public function getStockListWithAddConditionForJasaVendorOut($condition)
    {
        $builder = $this->asArray()
            ->select('
                stock_revamp_detail.id AS id,
                stock_revamp.spesifikasi_id,
                suppliers.name AS supplier_name,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.type_bc,
                rm_purchase_orders.supplier_id,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.reference_id,
                rm_purchase_orders.po_date,
                rm_purchase_orders.po_no,
                rm_purchase_orders.id as rm_purchase_order_id,
                rm_purchase_order_details.id as rm_purchase_order_detail_id,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                satuans.id as satuan_id,
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
                stock_revamp_detail.id,
            ')
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }

    public function getStockListDetail($stockID, $po_id)
    {

        $selectQry = '
            CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
            barang_master.barang_name AS barang_master,
            suppliers.name AS supplier_name,
            stock.company_id,
            stock.barang1_id,
            stock.barang2_id,
            stock.kemasan_id,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            stock_details2.stock_dokumen,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_po,
            stock_details.stock_date,
            stock_details.sumber,
            stock_details.no_dokumen AS no_dokumen_1,
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,        
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('stock', 'stock.id = stock_details.stock_id', 'left')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->groupBy('stock_details2.stock_dokumen')
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.no_aju')
            ->first();

        return $dataQry;
    }
}
