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
                update_stock_purchase_detail.qty_po AS total_penerimaan,
                update_stock_purchase_detail.qty_po as stok_total,
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

    public function getStockListWithAddConditionForStuffingById($id)
    {
        $builder = $this->asArray()
            ->select('
                stock_revamp_detail.id AS id,
                stock_revamp.spesifikasi_id,
                suppliers.name AS supplier_name,
                stock_revamp_detail.createdAt,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                rm_purchase_orders.supplier_id,
                stock_revamp_detail.reference_type,
                rm_purchase_orders.po_date,
                rm_purchase_orders.po_no,
                rm_purchase_orders.id as rm_purchase_order_id,
                rm_purchase_order_details.id as rm_purchase_order_detail_id,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
                barang_master.type_barang,
                satuans.kode_satuan,
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

        return $builder
            ->groupBy('
                stock_revamp_detail.id,
            ')
            ->where('stock_revamp_detail.id', $id)
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->first();
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
            ->where('stock_revamp_detail.qty_bersih >', 0)
            ->where('stock_revamp_detail.reference_type !=', "PROSES REBUS")
            ->where('stock_revamp.spesifikasi_id', $spesifikasiId)
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }


    public function getStockListWithAddConditionForStuffing($condition)
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
                barang_master.type_barang,
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
            // ->where('stock_revamp_detail.reference_type !=', "PROSES REBUS")
            // ->where('stock_revamp.spesifikasi_id', $spesifikasiId)
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }

    public function getStockListWithAddConditionForProsesRebusFromVendor($condition, $spesifikasiId)
    {
        $builder = $this->asArray()
            ->select('
                stock_revamp_detail.id AS id,
                stock_revamp.spesifikasi_id,
                vendors.name AS vendor_name,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                jasa_vendor_in.vendor_id,
                stock_revamp_detail.reference_type,
                jasa_vendor_in.tanggal,
                jasa_vendor_in.no_penerimaan_surat_jalan,
                jasa_vendor_in.id as jasa_vendor_in_id,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                satuans.id as satuan_id,
                stock_revamp_detail.qty_bersih AS total_penerimaan,
                stock_revamp_detail.qty_bersih as stok_total_bersih,
                stock_revamp_detail.qty_diterima as stok_total_diterima,
            ')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('jasa_vendor_in', 'stock_revamp_detail.reference_id = jasa_vendor_in.id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
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
            ->where('stock_revamp_detail.qty_bersih >', 0)
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
            ->orderBy('rm_purchase_orders.po_no', 'ASC')
            ->findAll();
    }

    public function getStockListWithAddConditionForJasaVendorOutTapak($condition)
    {
        $builder = $this->asArray()
            ->select('
                stock_revamp_detail.id AS id,
                stock_revamp.spesifikasi_id,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.type_bc,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.reference_id,
                production_results.receive_date,
                production_results.pr_no,
                production_results.id as production_result_id,
                production_result_details.id as production_result_detail_id,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                satuans.id as satuan_id,
                stock_revamp_detail.qty_bersih AS total_penerimaan,
                stock_revamp_detail.qty_bersih as stok_total,
                stock_revamp_detail.qty_diterima as stok_total_diterima,
            ')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('production_results', 'production_results.id = stock_revamp_detail.reference_id', 'left')
            ->join('production_result_details', 'production_result_details.production_result_id = production_results.id', 'left')
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
            ->orderBy('stock_revamp_detail.createdAt', 'DESC')
            ->findAll();
    }

    public function getStockListWithAddConditionForJasaVendorOutByVendor($condition)
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
                jasa_vendor_in.no_penerimaan_surat_jalan AS no_penerimaan_vendor
            ')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = stock_revamp_detail.reference_id', 'left');

        // Kondisi dinamis
        foreach ($condition as $field => $value) {
            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }

        return $builder
            ->groupBy('stock_revamp_detail.id')
            ->orderBy('rm_purchase_orders.po_date', 'ASC')
            ->findAll();
    }

    public function getListStockDetailByPoLokalBb($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'rm_purchase_orders.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'po_id'         => 'stock_revamp_detail.po_id',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'tanggal_po'    => 'rm_purchase_orders.po_date',
            'no_daftar'     => 'bc_purchase_order.no_daftar',
            'no_aju'        => 'bc_purchase_order.no_aju',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            suppliers.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            rm_purchase_orders.po_no,
            rm_purchase_orders.po_date,
            penerimaan_barang.no_penerimaan_barang AS ref_no,
            bc_purchase_order.no_aju,
            bc_purchase_order.no_daftar,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('bc_purchase_order.no_aju', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByPoBp($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'am_purchase_orders.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'po_id'         => 'stock_revamp_detail.po_id',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'tanggal_po'    => 'am_purchase_orders.po_date',
            'no_daftar'     => 'bc_purchase_order.no_daftar',
            'no_aju'        => 'bc_purchase_order.no_aju',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            suppliers.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            penerimaan_barang.no_penerimaan_barang AS ref_no,
            bc_purchase_order.no_aju,
            bc_purchase_order.no_daftar,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('bc_purchase_order.no_aju', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByPoImportBb($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'am_purchase_orders.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'po_id'         => 'stock_revamp_detail.po_id',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'tanggal_po'    => 'rm_import_pos.po_date',
            'no_daftar'     => 'bc_purchase_order.no_daftar',
            'no_aju'        => 'bc_purchase_order.no_aju',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            suppliers.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            rm_import_pos.po_no,
            rm_import_pos.po_date,
            penerimaan_barang.no_penerimaan_barang AS ref_no,
            bc_purchase_order.no_aju,
            bc_purchase_order.no_daftar,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('rm_import_pos', 'rm_import_pos.id = stock_revamp_detail.po_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('rm_import_pos.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('bc_purchase_order.no_aju', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByProsesRebus($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'rm_purchase_orders.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'po_id'         => 'stock_revamp_detail.po_id',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'tanggal_po'    => 'rm_purchase_orders.po_date',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            suppliers.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            rm_purchase_orders.po_no,
            rm_purchase_orders.po_date,
            proses_rebus.no_rebus AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('proses_rebus', 'proses_rebus.id = stock_revamp_detail.reference_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('proses_rebus.no_rebus', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByJasaVendor($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'jasa_vendor_in.vendor_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'po_id'         => 'stock_revamp_detail.po_id',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            vendors.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            jasa_vendor_in.no_penerimaan_surat_jalan AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = stock_revamp_detail.reference_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('jasa_vendor_in.no_penerimaan_surat_jalan', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('vendors.name', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByHasilProduksi($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            production_results.pr_no AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('production_results', 'production_results.id = stock_revamp_detail.reference_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('production_results.pr_no', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByMaterialRequestBaku($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            material_requests.req_no AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('material_requests', 'material_requests.id = stock_revamp_detail.reference_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('material_requests.req_no', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByMaterialRequestPenolong($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            material_requests_penolong.req_no AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('material_requests_penolong', 'material_requests_penolong.id = stock_revamp_detail.reference_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('material_requests_penolong.req_no', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByInisiasi($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan,
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('barang_master.kode_barang', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByAdjusment($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            adjusment.no_adjusment AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('adjusment', 'adjusment.id = stock_revamp_detail.reference_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('adjusment.no_adjusment', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByPenerimaanMutasi($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            penerimaan_mutasi.penerimaan_mutasi_no AS ref_no,
            satuans.kode_satuan,
            ppbkb.no_daftar AS no_daftar,
            ppbkb.no_ppbkb AS no_aju
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('penerimaan_mutasi', 'penerimaan_mutasi.id = stock_revamp_detail.reference_id', 'left')
            ->join('ppbkb', 'ppbkb.id = penerimaan_mutasi.ppbkb_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('penerimaan_mutasi.penerimaan_mutasi_no', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListStockDetailByPenerimaanMutasiGlobal($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'qty_diterima'  => 'stock_revamp_detail.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp_detail.id'] ?? 'stock_revamp_detail.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp_detail.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            penerimaan_mutasi_global.penerimaan_mutasi_no AS ref_no,
            satuans.kode_satuan,
            bc_27.no_aju,
            bc_27.no_daftar
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('penerimaan_mutasi_global', 'penerimaan_mutasi_global.id = stock_revamp_detail.reference_id', 'left')
            ->join('bc_27', 'bc_27.id = penerimaan_mutasi_global.bc27_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('penerimaan_mutasi_global.penerimaan_mutasi_no', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getStockListPOWithCondition(array $condition)
    {
        /* ============================================================
        * LOKAL BAKU
        * ============================================================ */
        $lokal = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,

                rmpo.po_date,
                rmpo.po_no,
                rmpo.po_no AS stock_dokumen,
                rmpo.id AS rm_purchase_order_id,
                rmpod.id  AS rm_purchase_order_detail_id,
                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
                s.kode_satuan,
                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,
                UPPER(sup.name) AS supplier_name,

                pb.no_penerimaan_barang,
                bc.no_aju,

                rmpod.general_price AS harga_umum,

                rmpod.daily_price AS harga_harian,
                rmpod.monthly_price AS harga_bulanan,

                rmpo.po_date AS stock_date,
                srd.type_bc,
                bc.no_daftar AS no_daftar
            ")
            ->join('stock_revamp sr', 'sr.id = srd.stock_id')
            ->join('rm_purchase_orders rmpo', 'rmpo.id = srd.po_id')
            ->join('rm_purchase_order_details rmpod', '
                rmpod.rm_purchase_order_id = rmpo.id
                AND rmpod.barang1_id = sr.barang_master_id
                AND rmpod.barang2_id = sr.spesifikasi_id
            ')
            ->join('suppliers sup', 'sup.id = rmpo.supplier_id')
            ->join('penerimaan_barang pb', 'pb.id = srd.reference_id')
            ->join('bc_purchase_order_lpb bcl', 'bcl.penerimaan_barang_id = pb.id', 'left')
            ->join('bc_purchase_order bc', 'bc.id = bcl.bc_purchase_order_id', 'left')
            ->join('barang_master bm', 'bm.id = sr.barang_master_id')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id')
            ->join('satuans s', 's.id = bms.satuan_1')
            ->where('srd.po_type', 'LOKAL BAKU');

        /* ============================================================
        * IMPORT BAKU
        * ============================================================ */
        $import = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,

                ripo.po_date,
                ripo.po_no,
                ripo.po_no AS stock_dokumen,
                ripo.id AS rm_purchase_order_id,
                ripod.id AS rm_purchase_order_detail_id,
                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
                s.kode_satuan,
                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,
                UPPER(sup.name) AS supplier_name,

                pb.no_penerimaan_barang,
                bc.no_aju,

                ripod.price AS harga_umum,

                NULL AS harga_harian,
                NULL AS harga_bulanan,

                ripo.po_date AS stock_date,
                srd.type_bc,
                bc.no_daftar AS no_daftar
            ")
            ->join('stock_revamp sr', 'sr.id = srd.stock_id')
            ->join('rm_import_pos ripo', 'ripo.id = srd.po_id')
            ->join('rm_import_po_details ripod', '
                ripod.rm_import_po_id = ripo.id
                AND ripod.barang_id = sr.barang_master_id
                AND ripod.spesifikasi_id = sr.spesifikasi_id
            ')
            ->join('suppliers sup', 'sup.id = ripo.supplier_id')
            ->join('penerimaan_barang pb', 'pb.id = srd.reference_id')
            ->join('bc_purchase_order_lpb bcl', 'bcl.penerimaan_barang_id = pb.id', 'left')
            ->join('bc_purchase_order bc', 'bc.id = bcl.bc_purchase_order_id', 'left')
            ->join('barang_master bm', 'bm.id = sr.barang_master_id')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id')
            ->join('satuans s', 's.id = bms.satuan_1')
            ->where('srd.po_type', 'IMPORT BAKU');

        /* ============================================================
        * FILTER UMUM
        * ============================================================ */
        foreach (['sr.company_id', 'sr.barang_master_id', 'sr.divisi_id', 'sr.warehouse_id'] as $f) {
            if (!empty($condition[$f])) {
                $lokal->where($f, $condition[$f]);
                $import->where($f, $condition[$f]);
            }
        }

        if (!empty($condition['stock_date_between'])) {
            [$start, $end] = $condition['stock_date_between'];
            $lokal->where('pb.tanggal >=', $start)->where('pb.tanggal <=', $end);
            $import->where('pb.tanggal >=', $start)->where('pb.tanggal <=', $end);
        }

        /* ============================================================
        * UNION FINAL
        * ============================================================ */
        $sql = $lokal->getCompiledSelect()
            . " UNION ALL "
            . $import->getCompiledSelect()
            . " ORDER BY stock_date ASC, po_no ASC";

        return $this->db->query($sql)->getResultArray();
    }

    // public function getStockListPOWithCondition($condition)
    // {
    //     $builder = $this->asArray()
    //         ->select("
    //             srd.id,
    //             srd.id AS stock_detail_id,
    //             sr.spesifikasi_id,
    //             sr.barang_master_id,
    //             sr.unit_id,
    //             sr.divisi_id,
    //             sr.warehouse_id,
    //             srd.stock_id,
    //             srd.bc_id,
    //             srd.keterangan,
    //             srd.reference_type,

    //             /* ========= PO ========= */
    //             COALESCE(rmpo.po_date, ripo.po_date) AS po_date,
    //             COALESCE(rmpo.po_no, ripo.po_no) AS po_no,
    //             COALESCE(rmpo.po_no, ripo.po_no) AS stock_dokumen,
    //             COALESCE(rmpo.id, ripo.id) AS rm_purchase_order_id,
    //             COALESCE(rmpod.id,ripod.id) AS rm_purchase_order_detail_id,
    //             bm.barang_name,
    //             bms.spesifikasi,
    //             CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
    //             s.kode_satuan,
    //             srd.qty_bersih AS stok_total,
    //             srd.qty_diterima AS stok_total_diterima,
    //             UPPER(sup.name) AS supplier_name,

    //             /* ========= PENERIMAAN ========= */
    //             COALESCE(pb_lokal.no_penerimaan_barang, pb_import.no_penerimaan_barang) AS no_penerimaan_barang,
    //             COALESCE(bc_lokal.no_aju, bc_import.no_aju) AS no_aju,

    //             /* ========= HARGA ========= */
    //             COALESCE(rmpod.general_price, ripod.price) AS harga_umum,

    //             rmpod.daily_price AS harga_harian,
    //             rmpod.monthly_price AS harga_bulanan,

    //             /* ========= TANGGAL STOCK ========= */
    //             COALESCE(pb_lokal.tanggal, pb_import.tanggal) AS stock_date,
    //             srd.type_bc,
    //             COALESCE(bc_lokal.no_daftar, bc_import.no_daftar) AS no_daftar
    //         ")

    //         ->from('stock_revamp_detail srd')
    //         ->join('stock_revamp sr', 'sr.id = srd.stock_id', 'left')

    //         /* ===================== PO DARI SRD ===================== */
    //         ->join('rm_purchase_orders rmpo',
    //                 "
    //                     rmpo.id = srd.po_id AND srd.po_type = 'LOKAL BAKU'
    //                 ", 'left')
    //         ->join('rm_purchase_order_details rmpod', 
    //                 "
    //                     rmpod.rm_purchase_order_id = srd.po_id AND srd.po_type = 'LOKAL BAKU'
    //                     AND rmpod.barang1_id = sr.barang_master_id
    //                     AND rmpod.barang2_id = sr.spesifikasi_id
    //                 ", 'left')
    //         ->join('suppliers sup', 'sup.id = rmpo.supplier_id', 'left')

    //         ->join('rm_import_pos ripo', "ripo.id = srd.po_id AND srd.po_type = 'IMPORT BAKU'", 'left')
    //         ->join('rm_import_po_details ripod', 
    //                 "
    //                     ripod.rm_import_po_id = srd.po_id AND srd.po_type = 'IMPORT BAKU'
    //                     AND ripod.barang_id = sr.barang_master_id
    //                     AND ripod.spesifikasi_id = sr.spesifikasi_id
    //                 ", 'left')
    //         /* ===================== PENERIMAAN BARANG ===================== */
    //         ->join('penerimaan_barang pb_lokal', "pb_lokal.id = srd.reference_id AND srd.po_type = 'LOKAL BAKU'", 'left')
    //         ->join('penerimaan_barang pb_import', "pb_import.id = srd.reference_id AND srd.po_type = 'IMPORT BAKU'", 'left')

    //         ->join('bc_purchase_order_lpb bcl_lokal', "bcl_lokal.penerimaan_barang_id = pb_lokal.id AND srd.po_type = 'LOKAL BAKU'", 'left')
    //         ->join('bc_purchase_order bc_lokal', "bc_lokal.id = bcl_lokal.bc_purchase_order_id AND srd.po_type = 'LOKAL BAKU'", 'left')

    //         ->join('bc_purchase_order_lpb bcl_import', "bcl_import.penerimaan_barang_id = pb_import.id AND srd.po_type = 'IMPORT BAKU'", 'left')
    //         ->join('bc_purchase_order bc_import', "bc_import.id = bcl_import.bc_purchase_order_id AND srd.po_type = 'IMPORT BAKU'", 'left')

    //         /* ===================== MASTER ===================== */
    //         ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
    //         ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
    //         ->join('satuans s', 's.id = bms.satuan_1', 'left');

    //     if (!empty($condition['stock_date_between'])) {
    //         [$startDate, $endDate] = $condition['stock_date_between'];

    //         if ($startDate && $endDate) {
    //             $builder->where("COALESCE(pb_lokal.tanggal, pb_import.tanggal) >= ", $startDate)
    //                 ->where("COALESCE(pb_lokal.tanggal, pb_import.tanggal) <= ", $endDate);
    //         }

    //         // PENTING: hapus supaya tidak ikut foreach
    //         unset($condition['stock_date_between']);
    //     }

    //     foreach ($condition as $field => $value) {
    //         if (is_array($value)) {
    //             $builder->whereIn($field, $value);
    //         } elseif ($value === 'IS NOT NULL') {
    //             $builder->where("$field IS NOT NULL", null, false);
    //         } elseif ($value !== null && $value !== '') {
    //             $builder->where($field, $value);
    //         }
    //     }

    //     return $builder
    //         ->groupBy('srd.id')
    //         ->orderBy('stock_date, po_no', 'ASC')
    //         ->findAll();
    // }

    public function getStockListProsesRebusWithCondition(array $condition)
    {
        /* ============================================================
        * 1️⃣ LOKAL BAKU
        * ============================================================ */
        $lokal = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,
                srd.type_bc,

                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,

                rmpo.po_date,
                rmpo.po_no,
                rmpo.po_no AS stock_dokumen,
                rmpo.id AS rm_purchase_order_id,
                rmpod.id AS rm_purchase_order_detail_id,

                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
                s.kode_satuan,

                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,

                UPPER(sup.name) AS supplier_name,

                pb.no_penerimaan_barang,
                bc.no_aju,

                rmpod.general_price AS harga_umum,
                rmpod.daily_price AS harga_harian,
                rmpod.monthly_price AS harga_bulanan,

                rmpo.po_date AS stock_date,
                bc.no_daftar
            ")
            ->join('stock_revamp sr', 'sr.id = srd.stock_id')
            ->join('rm_purchase_orders rmpo', 'rmpo.id = srd.po_id', 'left')
            ->join('rm_purchase_order_details rmpod', '
                rmpod.rm_purchase_order_id = rmpo.id
                AND rmpod.barang1_id = sr.barang_master_id
                AND rmpod.barang2_id = sr.spesifikasi_id
            ', 'left')
            ->join('suppliers sup', 'sup.id = rmpo.supplier_id', 'left')
            ->join('penerimaan_barang pb', 'pb.id = srd.reference_id', 'left')
            ->join('bc_purchase_order_lpb bcl', 'bcl.penerimaan_barang_id = pb.id', 'left')
            ->join('bc_purchase_order bc', 'bc.id = bcl.bc_purchase_order_id', 'left')
            ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
            ->join('satuans s', 's.id = bms.satuan_1', 'left')

            ->where('srd.po_type', 'LOKAL BAKU')
            ->where('srd.reference_type', 'PROSES REBUS');

        /* ============================================================
        * 2️⃣ IMPORT BAKU
        * ============================================================ */
        $import = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,
                srd.type_bc,

                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,

                ripo.po_date,
                ripo.po_no,
                ripo.po_no AS stock_dokumen,
                ripo.id AS rm_purchase_order_id,
                ripod.id AS rm_purchase_order_detail_id,

                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
                s.kode_satuan,

                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,

                UPPER(sup.name) AS supplier_name,

                pb.no_penerimaan_barang,
                bc.no_aju,

                ripod.price AS harga_umum,
                NULL AS harga_harian,
                NULL AS harga_bulanan,

                ripo.po_date AS stock_date,
                bc.no_daftar
            ")
            ->join('stock_revamp sr', 'sr.id = srd.stock_id')
            ->join('rm_import_pos ripo', 'ripo.id = srd.po_id', 'left')
            ->join('rm_import_po_details ripod', '
                ripod.rm_import_po_id = ripo.id
                AND ripod.barang_id = sr.barang_master_id
                AND ripod.spesifikasi_id = sr.spesifikasi_id
            ', 'left')
            ->join('suppliers sup', 'sup.id = ripo.supplier_id', 'left')
            ->join('penerimaan_barang pb', 'pb.id = srd.reference_id', 'left')
            ->join('bc_purchase_order_lpb bcl', 'bcl.penerimaan_barang_id = pb.id', 'left')
            ->join('bc_purchase_order bc', 'bc.id = bcl.bc_purchase_order_id', 'left')
            ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
            ->join('satuans s', 's.id = bms.satuan_1', 'left')

            ->where('srd.po_type', 'IMPORT BAKU')
            ->where('srd.reference_type', 'PROSES REBUS');

        /* ============================================================
        * 3️⃣ PROSES REBUS / INISIASI
        * ============================================================ */
        $rebus = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,
                srd.type_bc,

                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,

                pr.tanggal AS po_date,
                'INISIASI' AS po_no,
                'INISIASI' AS stock_dokumen,
                NULL AS rm_purchase_order_id,
                NULL AS rm_purchase_order_detail_id,

                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
                s.kode_satuan,

                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,

                '-' AS supplier_name,

                NULL AS no_penerimaan_barang,
                NULL AS no_aju,

                0 AS harga_umum,
                0 AS harga_harian,
                0 AS harga_bulanan,

                pr.tanggal AS stock_date,
                NULL AS no_daftar
            ")
            ->join('stock_revamp sr', 'sr.id = srd.stock_id')
            ->join('proses_rebus_detail prd', 'prd.stock_detail_hasil_rebus_id = srd.id', 'left')
            ->join('proses_rebus pr', 'pr.id = prd.proses_rebus_id', 'left')
            ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
            ->join('satuans s', 's.id = bms.satuan_1', 'left')
            
            ->where('srd.po_id', NULL)
            ->where('srd.reference_type', 'PROSES REBUS');

        /* ============================================================
        * FILTER UMUM (index-friendly)
        * ============================================================ */
        foreach (['sr.company_id', 'sr.barang_master_id', 'sr.divisi_id', 'sr.warehouse_id'] as $f) {
            if (!empty($condition[$f])) {
                $lokal->where($f, $condition[$f]);
                $import->where($f, $condition[$f]);
                $rebus->where($f, $condition[$f]);
            }
        }

        if (!empty($condition['stock_date_between'])) {
            [$start, $end] = $condition['stock_date_between'];
            if (!empty($start) && !empty($end)) {
                $lokal->where('pb.tanggal >=', $start)->where('pb.tanggal <=', $end);
                $import->where('pb.tanggal >=', $start)->where('pb.tanggal <=', $end);
                $rebus->where('pr.tanggal >=', $start)->where('pr.tanggal <=', $end);
            }
        }

        /* ============================================================
        * UNION FINAL
        * ============================================================ */
        $sql = "
            {$lokal->getCompiledSelect()}
            UNION ALL
            {$import->getCompiledSelect()}
            UNION ALL
            {$rebus->getCompiledSelect()}
            ORDER BY stock_date ASC, po_no ASC
        ";

        return $this->db->query($sql)->getResultArray();
    }

    // public function getStockListProsesRebusWithCondition($condition)
    // {
    //     $builder = $this->asArray()
    //         ->select("
    //             srd.id,
    //             srd.id AS stock_detail_id,
    //             sr.spesifikasi_id,
    //             sr.barang_master_id,
    //             sr.unit_id,
    //             sr.divisi_id,
    //             sr.warehouse_id,
    //             srd.stock_id,
    //             srd.bc_id,
    //             srd.keterangan,
    //             srd.reference_type,

    //             /* ========= PO ========= */
    //             CASE
    //                 WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
    //                     THEN srd.createdAt
    //                 ELSE COALESCE(
    //                     rmpo.po_date,
    //                     ripo.po_date
    //                 )
    //             END AS po_date,
    //             COALESCE(
    //                 rmpo.po_no,
    //                 ripo.po_no
    //             ) AS po_no,

    //             CASE
    //                 WHEN srd.reference_type = 'INISIASI'
    //                     THEN 'INISIASI'
    //                 ELSE COALESCE(
    //                     rmpo.po_no,
    //                     ripo.po_no
    //                 )
    //             END AS stock_dokumen,

    //             COALESCE(
    //                 rmpo.id,
    //                 ripo.id
    //             ) AS rm_purchase_order_id,

    //             COALESCE(
    //                 rmpod.id,
    //                 ripod.id
    //             ) AS rm_purchase_order_detail_id,

    //             bm.barang_name,
    //             bms.spesifikasi,
    //             CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
    //             s.kode_satuan,

    //             srd.qty_bersih AS stok_total,
    //             srd.qty_diterima AS stok_total_diterima,

    //             UPPER(sup.name) AS supplier_name,

    //             /* ========= PENERIMAAN ========= */
    //             COALESCE(
    //                 pb_lokal.no_penerimaan_barang,
    //                 pb_import.no_penerimaan_barang
    //             ) AS no_penerimaan_barang,

    //             COALESCE(
    //                 bc_lokal.no_aju,
    //                 bc_import.no_aju
    //             ) AS no_aju,

    //             /* ========= HARGA ========= */
    //             COALESCE(
    //                 rmpod.general_price,
    //                 ripod.price
    //             ) AS harga_umum,

    //             rmpod.daily_price AS harga_harian,
    //             rmpod.monthly_price AS harga_bulanan,

    //             /* ========= TANGGAL STOCK ========= */
    //             CASE
    //                 WHEN srd.reference_type = 'PROSES REBUS'
    //                     THEN pr.tanggal
    //                 WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
    //                     THEN srd.createdAt
    //                 ELSE COALESCE(
    //                     pb_lokal.tanggal,
    //                     pb_import.tanggal
    //                 )
    //             END AS stock_date,

    //             srd.type_bc,

    //             COALESCE(
    //                 bc_lokal.no_daftar,
    //                 bc_import.no_daftar
    //             ) AS no_daftar
    //         ")

    //         ->from('stock_revamp_detail srd')
    //         ->join('stock_revamp sr', 'sr.id = srd.stock_id', 'left')

    //         /* ===================== PO DARI SRD ===================== */
    //         ->join('rm_purchase_orders rmpo', "rmpo.id = srd.po_id AND srd.po_type = 'LOKAL BAKU'", 'left')
    //         ->join('rm_purchase_order_details rmpod', 'rmpod.rm_purchase_order_id = rmpo.id', 'left')
    //         ->join('suppliers sup', 'sup.id = rmpo.supplier_id', 'left')

    //         ->join('rm_import_pos ripo', "ripo.id = srd.po_id AND srd.po_type = 'IMPORT BAKU'", 'left')
    //         ->join('rm_import_po_details ripod', 'ripod.rm_import_po_id = ripo.id', 'left')

    //         /* ===================== PROSES REBUS SRD ===================== */
    //         ->join(
    //             'proses_rebus_detail prd',
    //             "prd.stock_detail_hasil_rebus_id = srd.id AND srd.reference_type = 'PROSES REBUS'",
    //             'left'
    //         )
    //         ->join('proses_rebus pr', 'pr.id = prd.proses_rebus_id', 'left')

    //         /* ===================== PENERIMAAN BARANG ===================== */
    //         ->join('penerimaan_barang pb_lokal', 'pb_lokal.id = srd.reference_id', 'left')
    //         ->join('penerimaan_barang pb_import', 'pb_import.id = srd.reference_id', 'left')

    //         ->join('bc_purchase_order_lpb bcl_lokal', 'bcl_lokal.penerimaan_barang_id = pb_lokal.id', 'left')
    //         ->join('bc_purchase_order bc_lokal', 'bc_lokal.id = bcl_lokal.bc_purchase_order_id', 'left')

    //         ->join('bc_purchase_order_lpb bcl_import', 'bcl_import.penerimaan_barang_id = pb_import.id', 'left')
    //         ->join('bc_purchase_order bc_import', 'bc_import.id = bcl_import.bc_purchase_order_id', 'left')

    //         /* ===================== MASTER ===================== */
    //         ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
    //         ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
    //         ->join('satuans s', 's.id = bms.satuan_1', 'left');

    //     if (!empty($condition['stock_date_between'])) {
    //         [$startDate, $endDate] = $condition['stock_date_between'];

    //         if ($startDate && $endDate) {
    //             $builder->where("
    //         (
    //             CASE
    //                 WHEN srd.reference_type = 'PROSES REBUS'
    //                     THEN pr.tanggal
    //                 WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
    //                     THEN srd.createdAt
    //                 ELSE COALESCE(
    //                     pb_lokal.tanggal,
    //                     pb_import.tanggal
    //                 )
    //             END
    //         ) BETWEEN '{$startDate}' AND '{$endDate}'
    //     ", null, false);
    //         }

    //         // PENTING: hapus supaya tidak ikut foreach
    //         unset($condition['stock_date_between']);
    //     }

    //     if (!empty($condition['supplier_filter'])) {
    //         $supplierId = $condition['supplier_filter'];

    //         $builder->groupStart()
    //             ->where('rmpo.supplier_id', $supplierId)
    //             // ->orWhere('rmpo2.supplier_id', $supplierId)
    //             ->groupEnd();

    //         unset($condition['supplier_filter']); // penting
    //     }

    //     foreach ($condition as $field => $value) {
    //         if (is_array($value)) {
    //             $builder->whereIn($field, $value);
    //         } elseif ($value === 'IS NOT NULL') {
    //             $builder->where("$field IS NOT NULL", null, false);
    //         } elseif ($value !== null && $value !== '') {
    //             $builder->where($field, $value);
    //         }
    //     }

    //     $builder->groupStart()
    //         ->where('srd.reference_type', 'PENERIMAAN MUTASI')
    //         ->orWhere('srd.reference_type', 'INISIASI')
    //         ->orWhere('srd.po_id IS NOT NULL', null, false)
    //         ->groupEnd();

    //     return $builder
    //         ->groupBy('srd.id')
    //         ->orderBy('stock_date, po_no', 'ASC')
    //         ->findAll();
    // }

    public function getStockListPenerimaanMutasiWithCondition(array $condition)
    {
        /*
        ============================================================
        1. MUTASI → LPB → LOKAL BAKU
        ============================================================
        */
        $lpbLokal = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,

                rmpo.po_date,
                rmpo.po_no,
                rmpo.po_no AS stock_dokumen,
                rmpo.id AS rm_purchase_order_id,
                rmpod.id AS rm_purchase_order_detail_id,

                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name,' ',bms.spesifikasi) AS barang,
                s.kode_satuan,

                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,

                UPPER(sup.name) AS supplier_name,
                pb.no_penerimaan_barang,
                bc.no_aju,

                rmpod.general_price AS harga_umum,
                rmpod.daily_price AS harga_harian,
                rmpod.monthly_price AS harga_bulanan,

                rmpo.po_date AS stock_date,
                srd.type_bc,
                bc.no_daftar
            ")
            ->join('penerimaan_mutasi_detail pmd', 'pmd.penerimaan_mutasi_id = srd.reference_id', 'left')
            ->join('mutasi_detail md', 'md.id = pmd.mutasi_detail_id', 'left')
            ->join('stock_revamp_detail srd2', "srd2.id = md.stock_detail_id AND srd2.reference_type = 'LPB'", 'left')
            ->join('stock_revamp sr', 'sr.id = srd.stock_id', 'left')

            ->join('rm_purchase_orders rmpo', "rmpo.id = srd2.po_id AND srd2.po_type = 'LOKAL BAKU'", 'left')
            ->join('rm_purchase_order_details rmpod', 'rmpod.rm_purchase_order_id = rmpo.id', 'left')
            ->join('suppliers sup', 'sup.id = rmpo.supplier_id', 'left')

            ->join('penerimaan_barang pb', 'pb.id = srd2.reference_id', 'left')
            ->join('bc_purchase_order_lpb bcl', 'bcl.penerimaan_barang_id = pb.id', 'left')
            ->join('bc_purchase_order bc', 'bc.id = bcl.bc_purchase_order_id', 'left')

            ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
            ->join('satuans s', 's.id = bms.satuan_1', 'left')

            ->where('srd.reference_type', 'PENERIMAAN MUTASI')
            ->where('srd2.reference_type', 'LPB')
            ->where('srd2.po_type', 'LOKAL BAKU');

        /*
        ============================================================
        2. MUTASI → LPB → IMPORT BAKU
        ============================================================
        */
        $lpbImport = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,

                ripo.po_date,
                ripo.po_no,
                ripo.po_no AS stock_dokumen,
                ripo.id AS rm_purchase_order_id,
                ripod.id AS rm_purchase_order_detail_id,

                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name,' ',bms.spesifikasi) AS barang,
                s.kode_satuan,

                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,

                UPPER(sup.name) AS supplier_name,
                pb.no_penerimaan_barang,
                bc.no_aju,

                ripod.price AS harga_umum,
                NULL AS harga_harian,
                NULL AS harga_bulanan,

                ripo.po_date AS stock_date,
                srd.type_bc,
                bc.no_daftar
            ")
            ->join('penerimaan_mutasi_detail pmd', 'pmd.penerimaan_mutasi_id = srd.reference_id', 'left')
            ->join('mutasi_detail md', 'md.id = pmd.mutasi_detail_id', 'left')
            ->join('stock_revamp_detail srd2', "srd2.id = md.stock_detail_id AND srd2.reference_type = 'LPB'", 'left')
            ->join('stock_revamp sr', 'sr.id = srd.stock_id', 'left')

            ->join('rm_import_pos ripo', "ripo.id = srd2.po_id AND srd2.po_type = 'IMPORT BAKU'", 'left')
            ->join('rm_import_po_details ripod', 'ripod.rm_import_po_id = ripo.id', 'left')
            ->join('suppliers sup', 'sup.id = ripo.supplier_id', 'left')

            ->join('penerimaan_barang pb', 'pb.id = srd2.reference_id', 'left')
            ->join('bc_purchase_order_lpb bcl', 'bcl.penerimaan_barang_id = pb.id', 'left')
            ->join('bc_purchase_order bc', 'bc.id = bcl.bc_purchase_order_id', 'left')

            ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
            ->join('satuans s', 's.id = bms.satuan_1', 'left')

            ->where('srd.reference_type', 'PENERIMAAN MUTASI')
            ->where('srd2.reference_type', 'LPB')
            ->where('srd2.po_type', 'IMPORT BAKU');

        /*
        ============================================================
        3. MUTASI → PROSES REBUS → PO (DIANGGAP LPB)
        ============================================================
        */
        $rebusPo = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,

                pr.tanggal AS po_date,
                rmpo.po_no,
                rmpo.po_no AS stock_dokumen,
                rmpo.id AS rm_purchase_order_id,
                NULL AS rm_purchase_order_detail_id,

                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name,' ',bms.spesifikasi) AS barang,
                s.kode_satuan,

                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,

                UPPER(sup.name) AS supplier_name,
                NULL AS no_penerimaan_barang,
                NULL AS no_aju,

                NULL AS harga_umum,
                NULL AS harga_harian,
                NULL AS harga_bulanan,

                pr.tanggal AS stock_date,
                srd.type_bc,
                NULL AS no_daftar
            ")
            ->join('penerimaan_mutasi_detail pmd', 'pmd.penerimaan_mutasi_id = srd.reference_id', 'left')
            ->join('mutasi_detail md', 'md.id = pmd.mutasi_detail_id', 'left')
            ->join('stock_revamp_detail srd2', "
                srd2.id = md.stock_detail_id
                AND srd2.reference_type = 'PROSES REBUS'
                AND srd2.po_id IS NOT NULL
            ", 'left')
            ->join('proses_rebus_detail prd', 'prd.stock_detail_hasil_rebus_id = srd2.id', 'left')
            ->join('proses_rebus pr', 'pr.id = prd.proses_rebus_id', 'left')
            ->join('rm_purchase_orders rmpo', 'rmpo.id = srd2.po_id', 'left')
            ->join('suppliers sup', 'sup.id = rmpo.supplier_id', 'left')
            ->join('stock_revamp sr', 'sr.id = srd.stock_id', 'left')
            ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
            ->join('satuans s', 's.id = bms.satuan_1', 'left')

            ->where('srd.reference_type', 'PENERIMAAN MUTASI')
            ->where('srd2.reference_type', 'PROSES REBUS');

        /*
        ============================================================
        4. MUTASI → INISIASI
        ============================================================
        */
        $inisiasi = $this->db->table('stock_revamp_detail srd')
            ->select("
                srd.id,
                srd.id AS stock_detail_id,
                sr.spesifikasi_id,
                sr.barang_master_id,
                sr.unit_id,
                sr.divisi_id,
                sr.warehouse_id,
                srd.stock_id,
                srd.bc_id,
                srd.keterangan,
                srd.reference_type,

                pm.tanggal AS po_date,
                'INISIASI' AS po_no,
                'INISIASI' AS stock_dokumen,
                NULL AS rm_purchase_order_id,
                NULL AS rm_purchase_order_detail_id,

                bm.barang_name,
                bms.spesifikasi,
                CONCAT(bm.barang_name,' ',bms.spesifikasi) AS barang,
                s.kode_satuan,

                srd.qty_bersih AS stok_total,
                srd.qty_diterima AS stok_total_diterima,

                '-' AS supplier_name,
                NULL AS no_penerimaan_barang,
                NULL AS no_aju,

                0 AS harga_umum,
                0 AS harga_harian,
                0 AS harga_bulanan,

                pm.tanggal AS stock_date,
                srd.type_bc,
                NULL AS no_daftar
            ")
            ->join('penerimaan_mutasi_detail pmd', 'pmd.penerimaan_mutasi_id = srd.reference_id', 'left')
            ->join('penerimaan_mutasi pm', 'pm.id = pmd.penerimaan_mutasi_id', 'left')
            ->join('mutasi_detail md', 'md.id = pmd.mutasi_detail_id', 'left')
            ->join('stock_revamp_detail srd2', "
                srd2.id = md.stock_detail_id
                AND srd2.reference_type = 'INISIASI'
                AND srd2.po_id IS NULL
            ", 'left')
            ->join('stock_revamp sr', 'sr.id = srd.stock_id', 'left')
            ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
            ->join('satuans s', 's.id = bms.satuan_1', 'left')

            ->where('srd.reference_type', 'PENERIMAAN MUTASI')
            ->where('srd2.reference_type', 'INISIASI');

        /* ==============================
       FILTER GLOBAL (FIXED)
       ============================== */

        $builders = [
            $lpbLokal,
            $lpbImport,
            $rebusPo,
            $inisiasi
        ];

        // FILTER COMPANY / BARANG / DIVISI / WAREHOUSE
        foreach ([
            'sr.company_id',
            'sr.barang_master_id',
            'sr.divisi_id',
            'sr.warehouse_id'
        ] as $field) {
            if (!empty($condition[$field])) {
                foreach ($builders as $b) {
                    $b->where($field, $condition[$field]);
                }
            }
        }

        // FILTER TANGGAL (pakai alias stock_date)
        if (!empty($condition['stock_date_between'])) {
            [$start, $end] = $condition['stock_date_between'];

            if (!empty($start) && !empty($end)) {
                foreach ($builders as $b) {
                    $b->where('stock_date >=', $start)
                    ->where('stock_date <=', $end);
                }
            }
        }

        // FILTER SUPPLIER
        if (!empty($condition['supplier_id'])) {
            foreach ([$lpbLokal, $lpbImport, $rebusPo] as $b) {
                $b->where('sup.id', $condition['supplier_id']);
            }
        }

        // FILTER PO NO
        if (!empty($condition['po_no'])) {
            foreach ([$lpbLokal, $lpbImport, $rebusPo] as $b) {
                $b->like('po_no', $condition['po_no']);
            }
        }

        /* ==============================
        UNION FINAL
        ============================== */
        $sql =
            $lpbLokal->getCompiledSelect()
            . " UNION ALL " . $lpbImport->getCompiledSelect()
            . " UNION ALL " . $rebusPo->getCompiledSelect()
            . " UNION ALL " . $inisiasi->getCompiledSelect()
            . " ORDER BY stock_date ASC, po_no ASC";

        return $this->db->query($sql)->getResultArray();
    }

    // public function getStockListPenerimaanMutasiWithCondition($condition)
    // {
    //     $builder = $this->asArray()
    //         ->select("
    //             srd.id,
    //             srd.id AS stock_detail_id,
    //             sr.spesifikasi_id,
    //             sr.barang_master_id,
    //             sr.unit_id,
    //             sr.divisi_id,
    //             sr.warehouse_id,
    //             srd.stock_id,
    //             srd.bc_id,
    //             srd.keterangan,
    //             srd.reference_type,

    //             /* ========= PO ========= */
    //             CASE
    //                 WHEN srd2.reference_type = 'INISIASI' AND srd2.po_id IS NULL
    //                     THEN COALESCE(
    //                     m.tanggal,
    //                     srd2.createdAt
    //                 )
    //                 WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
    //                     THEN srd.createdAt
    //                 ELSE COALESCE(
    //                     rmpo.po_date,
    //                     ripo.po_date,
    //                     rmpo2.po_date,
    //                     ripo2.po_date,
    //                     m.tanggal
    //                 )
    //             END AS po_date,
    //             m.tanggal,
    //             COALESCE(
    //                 rmpo.po_no,
    //                 ripo.po_no,
    //                 rmpo2.po_no,
    //                 ripo2.po_no
    //             ) AS po_no,

    //             CASE
    //                 WHEN srd2.reference_type = 'INISIASI'
    //                     THEN 'INISIASI'
    //                 WHEN srd.reference_type = 'INISIASI'
    //                     THEN 'INISIASI'
    //                 ELSE COALESCE(
    //                     rmpo.po_no,
    //                     ripo.po_no,
    //                     rmpo2.po_no,
    //                     ripo2.po_no
    //                 )
    //             END AS stock_dokumen,

    //             COALESCE(
    //                 rmpo.id,
    //                 ripo.id,
    //                 rmpo2.id,
    //                 ripo2.id
    //             ) AS rm_purchase_order_id,

    //             COALESCE(
    //                 rmpod.id,
    //                 ripod.id,
    //                 rmpod2.id,
    //                 ripod2.id
    //             ) AS rm_purchase_order_detail_id,

    //             bm.barang_name,
    //             bms.spesifikasi,
    //             CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
    //             s.kode_satuan,

    //             srd.qty_bersih AS stok_total,
    //             srd.qty_diterima AS stok_total_diterima,

    //             UPPER(COALESCE(sup.name, sup2.name)) AS supplier_name,

    //             /* ========= PENERIMAAN ========= */
    //             COALESCE(
    //                 pb_lokal.no_penerimaan_barang,
    //                 pb_import.no_penerimaan_barang,
    //                 pb_mutasi.no_penerimaan_barang
    //             ) AS no_penerimaan_barang,

    //             COALESCE(
    //                 bc_lokal.no_aju,
    //                 bc_import.no_aju,
    //                 bc_mutasi.no_aju
    //             ) AS no_aju,

    //             /* ========= HARGA ========= */
    //             COALESCE(
    //                 rmpod.general_price,
    //                 ripod.price,
    //                 rmpod2.general_price,
    //                 ripod2.price
    //             ) AS harga_umum,

    //             COALESCE(rmpod.daily_price, rmpod2.daily_price) AS harga_harian,
    //             COALESCE(rmpod.monthly_price, rmpod2.monthly_price) AS harga_bulanan,

    //             /* ========= TANGGAL STOCK ========= */
    //             CASE
    //                 WHEN srd2.reference_type = 'INISIASI' AND srd2.po_id IS NULL
    //                     THEN COALESCE(
    //                     m.tanggal,
    //                     srd2.createdAt
    //                 )
    //                 WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
    //                     THEN srd.createdAt
    //                 ELSE COALESCE(
    //                     pm.tanggal,
    //                     pb_mutasi.tanggal,
    //                     pb_lokal.tanggal,
    //                     pb_import.tanggal
    //                 )
    //             END AS stock_date,

    //             srd.type_bc,

    //             COALESCE(
    //                 bc_lokal.no_daftar,
    //                 bc_import.no_daftar,
    //                 bc_mutasi.no_daftar
    //             ) AS no_daftar
    //         ")

    //         ->from('stock_revamp_detail srd')
    //         ->join('stock_revamp sr', 'sr.id = srd.stock_id', 'left')

    //         /* ===================== PO DARI SRD ===================== */
    //         ->join('rm_purchase_orders rmpo', "rmpo.id = srd.po_id AND srd.po_type = 'LOKAL BAKU'", 'left')
    //         ->join('rm_purchase_order_details rmpod', 'rmpod.rm_purchase_order_id = rmpo.id', 'left')
    //         ->join('suppliers sup', 'sup.id = rmpo.supplier_id', 'left')

    //         ->join('rm_import_pos ripo', "ripo.id = srd.po_id AND srd.po_type = 'IMPORT BAKU'", 'left')
    //         ->join('rm_import_po_details ripod', 'ripod.rm_import_po_id = ripo.id', 'left')

    //         /* ===================== PROSES REBUS SRD ===================== */
    //         ->join(
    //             'proses_rebus_detail prd',
    //             "prd.stock_detail_hasil_rebus_id = srd.id AND srd.reference_type = 'PROSES REBUS'",
    //             'left'
    //         )
    //         ->join('proses_rebus pr', 'pr.id = prd.proses_rebus_id', 'left')

    //         /* ===================== MUTASI ===================== */
    //         ->join(
    //             'penerimaan_mutasi_detail pmd',
    //             "pmd.penerimaan_mutasi_id = srd.reference_id
    //             AND srd.reference_type = 'PENERIMAAN MUTASI'",
    //             'left'
    //         )
    //         ->join('penerimaan_mutasi pm', 'pm.id = pmd.penerimaan_mutasi_id', 'left')
    //         ->join('mutasi_detail md', 'md.id = pmd.mutasi_detail_id', 'left')
    //         ->join('mutasi m', 'm.id = md.mutasi_id', 'left')
    //         ->join('stock_revamp_detail srd2', "srd2.id = md.stock_detail_id AND srd2.reference_type = 'LPB'", 'left')

    //         /* ===================== PO DARI SRD2 ===================== */
    //         ->join('rm_purchase_orders rmpo2', "rmpo2.id = srd2.po_id AND srd2.po_type = 'LOKAL BAKU'", 'left')
    //         ->join('rm_purchase_order_details rmpod2', 'rmpod2.rm_purchase_order_id = rmpo2.id', 'left')
    //         ->join('suppliers sup2', 'sup2.id = rmpo2.supplier_id', 'left')

    //         ->join('rm_import_pos ripo2', "ripo2.id = srd2.po_id AND srd2.po_type = 'IMPORT BAKU'", 'left')
    //         ->join('rm_import_po_details ripod2', 'ripod2.rm_import_po_id = ripo2.id', 'left')

    //         /* ===================== PENERIMAAN BARANG ===================== */
    //         ->join('penerimaan_barang pb_lokal', 'pb_lokal.id = srd.reference_id', 'left')
    //         ->join('penerimaan_barang pb_import', 'pb_import.id = srd.reference_id', 'left')
    //         ->join('penerimaan_barang pb_mutasi', 'pb_mutasi.id = srd2.reference_id', 'left')

    //         ->join('bc_purchase_order_lpb bcl_lokal', 'bcl_lokal.penerimaan_barang_id = pb_lokal.id', 'left')
    //         ->join('bc_purchase_order bc_lokal', 'bc_lokal.id = bcl_lokal.bc_purchase_order_id', 'left')

    //         ->join('bc_purchase_order_lpb bcl_import', 'bcl_import.penerimaan_barang_id = pb_import.id', 'left')
    //         ->join('bc_purchase_order bc_import', 'bc_import.id = bcl_import.bc_purchase_order_id', 'left')

    //         ->join('bc_purchase_order_lpb bcl_mutasi', 'bcl_mutasi.penerimaan_barang_id = pb_mutasi.id', 'left')
    //         ->join('bc_purchase_order bc_mutasi', 'bc_mutasi.id = bcl_mutasi.bc_purchase_order_id', 'left')

    //         /* ===================== MASTER ===================== */
    //         ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
    //         ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
    //         ->join('satuans s', 's.id = bms.satuan_1', 'left');

    //     if (!empty($condition['stock_date_between'])) {
    //         [$startDate, $endDate] = $condition['stock_date_between'];

    //         if ($startDate && $endDate) {
    //             $builder->where("
    //         (
    //             CASE
    //                 WHEN srd2.reference_type = 'INISIASI' AND srd2.po_id IS NULL
    //                     THEN COALESCE(m.tanggal, srd2.createdAt)
    //                 WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
    //                     THEN srd.createdAt
    //                 ELSE COALESCE(
    //                     pb_lokal.tanggal,
    //                     pb_import.tanggal,
    //                     pb_mutasi.tanggal
    //                 )
    //             END
    //         ) BETWEEN '{$startDate}' AND '{$endDate}'
    //     ", null, false);
    //         }

    //         // PENTING: hapus supaya tidak ikut foreach
    //         unset($condition['stock_date_between']);
    //     }

    //     if (!empty($condition['supplier_filter'])) {
    //         $supplierId = $condition['supplier_filter'];

    //         $builder->groupStart()
    //             ->where('rmpo.supplier_id', $supplierId)
    //             // ->orWhere('rmpo2.supplier_id', $supplierId)
    //             ->groupEnd();

    //         unset($condition['supplier_filter']); // penting
    //     }

    //     foreach ($condition as $field => $value) {
    //         if (is_array($value)) {
    //             $builder->whereIn($field, $value);
    //         } elseif ($value === 'IS NOT NULL') {
    //             $builder->where("$field IS NOT NULL", null, false);
    //         } elseif ($value !== null && $value !== '') {
    //             $builder->where($field, $value);
    //         }
    //     }

    //     return $builder
    //         ->groupBy('srd.id')
    //         ->orderBy('stock_date, po_no', 'ASC')
    //         ->findAll();
    // }

    public function getStockListVendorWithCondition($condition)
    {
        $builder = $this->asArray()
            ->select("
                stock_revamp_detail.id AS id,
                stock_revamp_detail.id AS stock_detail_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.barang_master_id,
                stock_revamp.unit_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.keterangan,
                rm_purchase_orders.supplier_id,
                stock_revamp_detail.reference_type,
                rm_purchase_orders.po_date,
                rm_purchase_orders.po_no,
                rm_purchase_orders.id as rm_purchase_order_id,
                rm_purchase_order_details.id as rm_purchase_order_detail_id,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                stock_revamp_detail.qty_bersih as stok_total,
                stock_revamp_detail.qty_diterima as stok_total_diterima,
                jasa_vendor_in.no_penerimaan_surat_jalan,
                COALESCE(bpo.no_aju, bpoi.no_aju) AS no_aju,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'PROSES REBUS' AND proses_rebus_detail.po_id IS NULL THEN UPPER(vendors_rebus.name)
                    WHEN stock_revamp_detail.reference_type = 'JASA VENDOR' THEN UPPER(vendors.name)
                    ELSE UPPER(vendors.name)
                END AS vendor_name,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'PROSES REBUS' AND proses_rebus_detail.po_id IS NULL THEN UPPER(vendors_rebus.name)
                    WHEN stock_revamp_detail.reference_type = 'JASA VENDOR' THEN UPPER(vendors.name)
                    ELSE UPPER(vendors.name)
                END AS supplier_name,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'PROSES REBUS' AND proses_rebus_detail.po_id IS NULL THEN vendors.id
                    WHEN stock_revamp_detail.reference_type = 'JASA VENDOR' THEN vendors.id
                    ELSE UPPER(vendors.name)
                END AS vendor_id,
                vendors.id AS vendor_id,
                rm_purchase_order_details.general_price as harga_umum,
                rm_purchase_order_details.daily_price as harga_harian,
                rm_purchase_order_details.monthly_price as harga_bulanan,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'PROSES REBUS' AND proses_rebus_detail.jasa_vendor_id IS NOT NULL THEN concat(proses_rebus.no_rebus, ' (', jasa_vendor_in_rebus.no_penerimaan_surat_jalan, ')')
                    WHEN stock_revamp_detail.reference_type = 'JASA VENDOR' THEN jasa_vendor_in.no_penerimaan_surat_jalan
                    ELSE 'test'
                END AS stock_dokumen,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'PROSES REBUS' THEN proses_rebus.tanggal
                    WHEN stock_revamp_detail.reference_type = 'JASA VENDOR' THEN jasa_vendor_in.tanggal
                    ELSE COALESCE(pb.tanggal, pbi.tanggal)
                END AS stock_date,
                stock_revamp_detail.type_bc as type_bc,
                COALESCE(bpo.no_daftar, bpoi.no_daftar) AS no_daftar
            ")

            // from Proses Rebus
            ->join('proses_rebus_detail', "proses_rebus_detail.stock_detail_hasil_rebus_id = stock_revamp_detail.id AND stock_revamp_detail.reference_type = 'PROSES REBUS'", 'left')
            ->join('proses_rebus', "proses_rebus.id = proses_rebus_detail.proses_rebus_id", 'left')

            // from Proses Rebus -> JasVen
            ->join('jasa_vendor_in as jasa_vendor_in_rebus', "jasa_vendor_in_rebus.id = proses_rebus_detail.jasa_vendor_id  AND stock_revamp_detail.reference_type = 'PROSES REBUS'", 'left')
            ->join('vendors as vendors_rebus', 'vendors_rebus.id = jasa_vendor_in_rebus.vendor_id', 'left')

            // from Jasa Vendor
            ->join('jasa_vendor_in', "jasa_vendor_in.id = stock_revamp_detail.reference_id AND stock_revamp_detail.reference_type = 'JASA VENDOR'", 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')

            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('stock_revamp_history', "stock_revamp_history.stock_detail_akhir = stock_revamp_detail.id AND stock_revamp_detail.reference_type = 'JASA VENDOR'", 'left')

            //get asal po stock
            ->join('stock_revamp_detail as stock_revamp_detail2', 'stock_revamp_detail2.id = stock_revamp_history.stock_detail_asal', 'left')

            // case dari pembelian baku lokal
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail2.po_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang as pb', 'pb.id = stock_revamp_detail2.reference_id', 'left')
            ->join('bc_purchase_order_lpb as bpol', 'bpol.penerimaan_barang_id = pb.id', 'left')
            ->join('bc_purchase_order as bpo', 'bpo.id = bpol.bc_purchase_order_id', 'left')

            // case dari penerimaan mutasi ke pembelian baku import
            ->join('rm_import_pos', "rm_import_pos.id = stock_revamp_detail2.po_id AND stock_revamp_detail2.reference_type = 'LPB' AND stock_revamp_detail2.po_type = 'IMPORT BAKU'", 'left')
            ->join('rm_import_po_details', 'rm_import_po_details.rm_import_po_id = rm_import_pos.id', 'left')
            ->join('penerimaan_barang as pbi', 'pbi.id = stock_revamp_detail2.reference_id', 'left')
            ->join('bc_purchase_order_lpb as bpoli', 'bpoli.penerimaan_barang_id = pbi.id', 'left')
            ->join('bc_purchase_order as bpoi', 'bpoi.id = bpoli.bc_purchase_order_id', 'left')

            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
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
            ->groupBy('stock_revamp_detail.id')
            ->orderBy('stock_date, keterangan', 'ASC')
            ->findAll();
    }


    public function getStockIdentity($id)
    {
        $selectQry = "
            stock_revamp_detail.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.satuan_1,
            barang_master_spesifikasi.satuan_2,
            barang_master_spesifikasi.satuan_3,
            barang_master_spesifikasi.konversi_satuan_2,
            barang_master_spesifikasi.konversi_satuan_3,
            barang_master.type_barang,
            parent_barang.parent_name,
            satuans.kode_satuan,
            warehouses.warehouse_name,
            divisis.divisi
        ";

        $dataResult = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id')
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->where('stock_revamp_detail.id', $id)
            ->first();

        return $dataResult;
    }
    public function getBarangAndStockCondition($type_barang, $divisi_id, $warehouse_id, $addCondition = null)
    {
        if ($type_barang) {
            // LIST BARANG
            $selectQry = "
                stock_revamp.id AS stock_id,
                stock_revamp.barang_master_id AS barang_id,
                stock_revamp.spesifikasi_id AS spesifikasi_id,
                CONCAT(UPPER(barang_master.barang_name), '-', UPPER(barang_master_spesifikasi.spesifikasi)) AS barang,
                barang_master.kode_barang,
                satuans.kode_satuan,
                parent_barang.parent_name
            ";

            $dataResult = $this->asArray()->select($selectQry)
                ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id')
                ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id')
                ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
                ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
                ->where('barang_master.type_barang', $type_barang)
                ->where('stock_revamp.divisi_id', $divisi_id)
                ->where('stock_revamp.warehouse_id', $warehouse_id)
                ->where('stock_revamp.deletedAt', null)
                ->where('barang_master_spesifikasi.deletedAt', null)
                ->where('barang_master.deletedAt', null)
                ->where($addCondition)
                ->orderBy('barang_master.kode_barang', "ASC")
                ->groupBy('stock_revamp.id')
                ->findAll();
        }

        return $dataResult;
    }

    public function getStockListWithCondition($condition)
    {
        $builder = $this->asArray()
            ->select("
                stock_revamp_detail.id AS id,
                stock_revamp_detail.id AS stock_detail_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.barang_master_id,
                stock_revamp.unit_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_type,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                stock_revamp_detail.qty_bersih as stok_total,
                stock_revamp_detail.qty_diterima as stok_total_diterima,
                production_result_details.no_aju,
                production_result_details.harga_umum as harga_umum,
                production_result_details.harga_harian as harga_harian,
                production_result_details.harga_bulanan as harga_bulanan,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'LPB' THEN concat(penerimaan_barang.no_penerimaan_barang, ' (', rm_purchase_orders.po_no, ')')
                    WHEN stock_revamp_detail.reference_type = 'HASIL PRODUKSI' THEN production_results.pr_no
                    ELSE penerimaan_barang.no_penerimaan_barang
                END AS stock_dokumen,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'LPB' THEN penerimaan_barang.tanggal
                    WHEN stock_revamp_detail.reference_type = 'HASIL PRODUKSI' THEN production_results.receive_date
                    ELSE penerimaan_barang.tanggal
                END AS stock_date,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'LPB' THEN bc_purchase_order.no_daftar
                    WHEN stock_revamp_detail.reference_type = 'HASIL PRODUKSI' THEN production_result_details.no_ref
                    ELSE bc_purchase_order.no_daftar
                END AS no_daftar,
                stock_revamp_detail.type_bc as type_bc,
                production_result_details.no_ref as no_daftar,
                barang_master.type_barang,
                COALESCE (UPPER(suppliers.name), '-') AS supplier_name

            ")
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('production_results', 'production_results.id = stock_revamp_detail.reference_id', 'left')
            ->join('production_result_details', 'production_result_details.stock_detail_id = stock_revamp_detail.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
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
            ->groupBy('stock_revamp_detail.id')
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }

    public function getStockListPenolongWithCondition($condition)
    {
        $builder = $this->asArray()
            ->select("
                stock_revamp_detail.id AS id,
                stock_revamp_detail.id AS stock_detail_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.barang_master_id,
                stock_revamp.unit_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.stock_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_type,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                stock_revamp_detail.qty_bersih as stok_total,
                stock_revamp_detail.qty_diterima as stok_total_diterima,
                production_result_details.no_aju,
                production_result_details.harga_umum as harga_umum,
                production_result_details.harga_harian as harga_harian,
                production_result_details.harga_bulanan as harga_bulanan,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'LPB' THEN concat(penerimaan_barang.no_penerimaan_barang, ' (', am_purchase_orders.po_no, ')')
                    WHEN stock_revamp_detail.reference_type = 'HASIL PRODUKSI' THEN production_results.pr_no
                    ELSE penerimaan_barang.no_penerimaan_barang
                END AS stock_dokumen,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'LPB' THEN penerimaan_barang.tanggal
                    WHEN stock_revamp_detail.reference_type = 'HASIL PRODUKSI' THEN production_results.receive_date
                    ELSE penerimaan_barang.tanggal
                END AS stock_date,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'LPB' THEN bc_purchase_order.no_daftar
                    WHEN stock_revamp_detail.reference_type = 'HASIL PRODUKSI' THEN production_result_details.no_ref
                    ELSE bc_purchase_order.no_daftar
                END AS no_daftar,
                stock_revamp_detail.type_bc as type_bc,
                production_result_details.no_ref as no_daftar,
                barang_master.type_barang,
                COALESCE (UPPER(suppliers.name), '-') AS supplier_name
            ")
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('production_results', 'production_results.id = stock_revamp_detail.reference_id', 'left')
            ->join('production_result_details', 'production_result_details.stock_detail_id = stock_revamp_detail.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')

            ->join('am_purchase_orders', 'am_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')

            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
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
            ->groupBy('stock_revamp_detail.id')
            ->orderBy('stock_revamp_detail.createdAt', 'ASC')
            ->findAll();
    }

    // StockRevampDetailModel.php

    public function getStockListForMaterialRequestServerSide($params)
    {
        $builder = $this->asArray()
            ->select("
            srd.id,
            srd.id AS stock_detail_id,
            sr.spesifikasi_id,
            sr.barang_master_id,
            sr.divisi_id,
            sr.warehouse_id,
            srd.stock_id,
            srd.bc_id,
            srd.keterangan,
            srd.reference_type,
            
            CASE
                WHEN srd2.reference_type = 'INISIASI' AND srd2.po_id IS NULL
                    THEN COALESCE(m.tanggal, srd2.createdAt)
                WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
                    THEN srd.createdAt
                ELSE COALESCE(rmpo.po_date, ripo.po_date, rmpo2.po_date, ripo2.po_date, m.tanggal)
            END AS po_date,
            
            COALESCE(rmpo.po_no, ripo.po_no, rmpo2.po_no, ripo2.po_no) AS po_no,
            
            CASE
                WHEN srd2.reference_type = 'INISIASI'
                    THEN 'INISIASI'
                WHEN srd.reference_type = 'INISIASI'
                    THEN 'INISIASI'
                ELSE COALESCE(rmpo.po_no, ripo.po_no, rmpo2.po_no, ripo2.po_no)
            END AS stock_dokumen,
            
            bm.barang_name,
            bms.spesifikasi,
            CONCAT(bm.barang_name, ' ', bms.spesifikasi) AS barang,
            s.kode_satuan,
            
            srd.qty_bersih AS stok_total,
            srd.qty_diterima AS stok_total_diterima,
            
            UPPER(COALESCE(sup.name, sup2.name)) AS supplier_name,
            
            COALESCE(bc_lokal.no_aju, bc_import.no_aju, bc_mutasi.no_aju) AS no_aju,
            
            COALESCE(rmpod.general_price, ripod.price, rmpod2.general_price, ripod2.price) AS harga_umum,
            
            CASE
                WHEN srd.reference_type = 'PROSES REBUS'
                    THEN pr.tanggal
                WHEN srd2.reference_type = 'PROSES REBUS'
                    THEN pr2.tanggal
                WHEN srd2.reference_type = 'INISIASI' AND srd2.po_id IS NULL
                    THEN COALESCE(m.tanggal, srd2.createdAt)
                WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
                    THEN srd.createdAt
                ELSE COALESCE(pb_lokal.tanggal, pb_import.tanggal, pb_mutasi.tanggal)
            END AS stock_date,
            
            srd.type_bc,
            
            COALESCE(bc_lokal.no_daftar, bc_import.no_daftar, bc_mutasi.no_daftar) AS no_daftar,
            
            'LPB' AS sumber,
            'bahan_baku' AS type_barang,
            'BAHAN BAKU' AS type_barang_text
        ")
            ->from('stock_revamp_detail srd')
            ->join('stock_revamp sr', 'sr.id = srd.stock_id', 'left')
            ->join('rm_purchase_orders rmpo', "rmpo.id = srd.po_id AND srd.po_type = 'LOKAL BAKU'", 'left')
            ->join('rm_purchase_order_details rmpod', 'rmpod.rm_purchase_order_id = rmpo.id', 'left')
            ->join('suppliers sup', 'sup.id = rmpo.supplier_id', 'left')
            ->join('rm_import_pos ripo', "ripo.id = srd.po_id AND srd.po_type = 'IMPORT BAKU'", 'left')
            ->join('rm_import_po_details ripod', 'ripod.rm_import_po_id = ripo.id', 'left')
            ->join('proses_rebus_detail prd', "prd.stock_detail_hasil_rebus_id = srd.id AND srd.reference_type = 'PROSES REBUS'", 'left')
            ->join('proses_rebus pr', 'pr.id = prd.proses_rebus_id', 'left')
            ->join('penerimaan_mutasi_detail pmd', "pmd.penerimaan_mutasi_id = srd.reference_id AND srd.reference_type = 'PENERIMAAN MUTASI'", 'left')
            ->join('mutasi_detail md', 'md.id = pmd.mutasi_detail_id', 'left')
            ->join('mutasi m', 'm.id = md.mutasi_id', 'left')
            ->join('stock_revamp_detail srd2', 'srd2.id = md.stock_detail_id', 'left')
            ->join('rm_purchase_orders rmpo2', "rmpo2.id = srd2.po_id AND srd2.po_type = 'LOKAL BAKU'", 'left')
            ->join('rm_purchase_order_details rmpod2', 'rmpod2.rm_purchase_order_id = rmpo2.id', 'left')
            ->join('suppliers sup2', 'sup2.id = rmpo2.supplier_id', 'left')
            ->join('rm_import_pos ripo2', "ripo2.id = srd2.po_id AND srd2.po_type = 'IMPORT BAKU'", 'left')
            ->join('rm_import_po_details ripod2', 'ripod2.rm_import_po_id = ripo2.id', 'left')
            ->join('proses_rebus_detail prd2', "prd2.stock_detail_hasil_rebus_id = srd2.id AND srd2.reference_type = 'PROSES REBUS'", 'left')
            ->join('proses_rebus pr2', 'pr2.id = prd2.proses_rebus_id', 'left')
            ->join('penerimaan_barang pb_lokal', 'pb_lokal.id = srd.reference_id', 'left')
            ->join('penerimaan_barang pb_import', 'pb_import.id = srd.reference_id', 'left')
            ->join('penerimaan_barang pb_mutasi', 'pb_mutasi.id = srd2.reference_id', 'left')
            ->join('bc_purchase_order_lpb bcl_lokal', 'bcl_lokal.penerimaan_barang_id = pb_lokal.id', 'left')
            ->join('bc_purchase_order bc_lokal', 'bc_lokal.id = bcl_lokal.bc_purchase_order_id', 'left')
            ->join('bc_purchase_order_lpb bcl_import', 'bcl_import.penerimaan_barang_id = pb_import.id', 'left')
            ->join('bc_purchase_order bc_import', 'bc_import.id = bcl_import.bc_purchase_order_id', 'left')
            ->join('bc_purchase_order_lpb bcl_mutasi', 'bcl_mutasi.penerimaan_barang_id = pb_mutasi.id', 'left')
            ->join('bc_purchase_order bc_mutasi', 'bc_mutasi.id = bcl_mutasi.bc_purchase_order_id', 'left')
            ->join('barang_master bm', 'bm.id = sr.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = sr.spesifikasi_id', 'left')
            ->join('satuans s', 's.id = bms.satuan_1', 'left')
            ->where('sr.company_id', session()->get("login")->this_company_id);

        // Apply filters
        if (!empty($params['barang_master_id'])) {
            $builder->where('sr.barang_master_id', $params['barang_master_id']);
        }

        if (!empty($params['divisi_id'])) {
            $builder->where('sr.divisi_id', $params['divisi_id']);
        }

        if (!empty($params['warehouse_id'])) {
            $builder->where('sr.warehouse_id', $params['warehouse_id']);
        }

        if (!empty($params['type_asal_barang']) && $params['type_asal_barang'] == 'SUPPLIER' && !empty($params['supplier_id'])) {
            $builder->where('rmpo.supplier_id', $params['supplier_id']);
        }

        if (!empty($params['startDate']) && !empty($params['endDate'])) {
            $builder->where("DATE(CASE
            WHEN srd.reference_type = 'PROSES REBUS'
                THEN pr.tanggal
            WHEN srd2.reference_type = 'PROSES REBUS'
                THEN pr2.tanggal
            WHEN srd2.reference_type = 'INISIASI' AND srd2.po_id IS NULL
                THEN COALESCE(m.tanggal, srd2.createdAt)
            WHEN srd.reference_type = 'INISIASI' AND srd.po_id IS NULL
                THEN srd.createdAt
            ELSE COALESCE(pb_lokal.tanggal, pb_import.tanggal, pb_mutasi.tanggal)
        END) BETWEEN '{$params['startDate']}' AND '{$params['endDate']}'", null, false);
        }

        // Apply search
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('bm.barang_name', $params['search'])
                ->orLike('bms.spesifikasi', $params['search'])
                ->orLike('rmpo.po_no', $params['search'])
                ->orLike('sup.name', $params['search'])
                ->orLike('bc_lokal.no_aju', $params['search'])
                ->groupEnd();
        }

        // Get total records
        $totalRecords = clone $builder;
        $totalRecords = $totalRecords->countAllResults(false);

        // Apply ordering
        $orderColumns = [
            1 => 'stock_dokumen',
            2 => 'supplier_name',
            3 => 'stock_date',
            4 => 'barang',
            5 => 'keterangan',
            6 => 'kode_satuan',
            7 => 'stok_total'
        ];

        if (!empty($params['order_column']) && isset($orderColumns[$params['order_column']])) {
            $builder->orderBy($orderColumns[$params['order_column']], $params['order_dir']);
        } else {
            $builder->orderBy('stock_date', 'ASC');
        }

        // Apply limit for pagination
        if ($params['length'] != -1) {
            $builder->limit($params['length'], $params['start']);
        }

        $data = $builder->get()->getResultArray();

        // Format data for response
        foreach ($data as &$row) {
            $row['id'] = encrypt($row['stock_id']) . '-' . encrypt($row['id']);
            $row['checkbox'] = '<div class="form-check" style="margin-top: -12px; padding-left: 0px;">
            <input data-id="' . $row['id'] . '" data-stok_total="' . $row['stok_total'] . '" 
                   autocomplete="one-time-code" class="form-check-input child" 
                   type="checkbox" style="width:30px; height:30px;">
        </div>';
        }

        return [
            'totalRecords' => $totalRecords,
            'totalFiltered' => $totalRecords, // In a real scenario, you'd have different filtered count
            'data' => $data
        ];
    }
}
