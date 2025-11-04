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
            ->orderBy('rm_purchase_orders.po_date', 'ASC')
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

    public function getStockListPOWithCondition($condition)
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
                rm_purchase_orders.po_no AS stock_dokumen,
                UPPER(suppliers.name) AS supplier_name,
                bc_purchase_order.no_aju,
                rm_purchase_order_details.general_price as harga_umum,
                rm_purchase_order_details.daily_price as harga_harian,
                rm_purchase_order_details.monthly_price as harga_bulanan,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'PROSES REBUS' THEN proses_rebus.tanggal
                    ELSE penerimaan_barang.tanggal
                END AS stock_date,
                penerimaan_barang.no_penerimaan_barang as no_penerimaan_barang,
                stock_revamp_detail.type_bc as type_bc,
                bc_purchase_order.no_daftar
            ")
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('proses_rebus', 'proses_rebus.id = stock_revamp_detail.reference_id', 'left')
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
            ->orderBy('stock_date', 'ASC')
            ->findAll();
    }

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
                UPPER(concat(vendors.name, ' / ', suppliers.name)) AS supplier_name,
                bc_purchase_order.no_aju,
                vendors.id AS vendor_id,
                UPPER(vendors.name) AS vendor_name,
                rm_purchase_order_details.general_price as harga_umum,
                rm_purchase_order_details.daily_price as harga_harian,
                rm_purchase_order_details.monthly_price as harga_bulanan,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'PROSES REBUS' THEN concat(jasa_vendor_in.no_penerimaan_surat_jalan, ' (', rm_purchase_orders.po_no, ')')
                    WHEN stock_revamp_detail.reference_type = 'JASA VENDOR' THEN concat(jasa_vendor_in.no_penerimaan_surat_jalan, ' (', COALESCE (rm_purchase_orders.po_no, proses_rebus.no_rebus), ')')
                    ELSE penerimaan_barang.tanggal
                END AS stock_dokumen,
                CASE 
                    WHEN stock_revamp_detail.reference_type = 'PROSES REBUS' THEN proses_rebus.tanggal
                    WHEN stock_revamp_detail.reference_type = 'JASA VENDOR' THEN jasa_vendor_in.tanggal
                    ELSE penerimaan_barang.tanggal
                END AS stock_date,
                stock_revamp_detail.type_bc as type_bc,
                bc_purchase_order.no_daftar
            ")
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = stock_revamp_detail.reference_id', 'left')
            ->join('proses_rebus', 'proses_rebus.id = stock_revamp_detail.reference_id', 'left')
            ->join('jasa_vendor_in_detail', 'jasa_vendor_in_detail.jasa_vendor_in_id = jasa_vendor_in.id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->join('stock_revamp_history', 'stock_revamp_history.stock_detail_akhir = stock_revamp_detail.id', 'left')

            //get asal po stock
            ->join('stock_revamp_detail as stock_revamp_detail2', 'stock_revamp_detail2.id = stock_revamp_history.stock_detail_asal', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail2.po_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail2.reference_id', 'left')
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
            ->orderBy('stock_date', 'ASC')
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
}
