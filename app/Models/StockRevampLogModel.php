<?php

namespace App\Models;

use CodeIgniter\Model;

class StockRevampLogModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_revamp_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
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

    public function getListLogPoLokalBb($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'rm_purchase_orders.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'     => 'stock_revamp.divisi_id',
            'warehouse_id'  => 'stock_revamp.warehouse_id',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'po_id'         => 'stock_revamp_detail.po_id',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'tanggal_po'    => 'rm_purchase_orders.tanggal',
            'createdAt'     => 'stock_revamp_log.createdAt',
            'qty_diterima'  => 'stock_revamp_log.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id',

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_revamp_log.createdAt';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            stock_revamp_log.*,
            suppliers.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            stock_revamp_detail.type_bc,
            penerimaan_barang.no_penerimaan_barang AS ref_no,
            rm_purchase_orders.po_no AS po_no,
            rm_purchase_orders.po_date AS tanggal_po,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
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

    public function getListLogPoLokalBp($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'am_purchase_orders.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'     => 'stock_revamp.divisi_id',
            'warehouse_id'  => 'stock_revamp.warehouse_id',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'po_id'         => 'stock_revamp_detail.po_id',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'tanggal_po'    => 'am_purchase_orders.tanggal',
            'createdAt'     => 'stock_revamp_log.createdAt',
            'qty_diterima'  => 'stock_revamp_log.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id',
            'keterangan'    => 'stock_revamp_log.keterangan'

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_revamp_log.createdAt';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            stock_revamp_log.*,
            suppliers.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            stock_revamp_detail.type_bc,
            penerimaan_barang.no_penerimaan_barang AS ref_no,
            am_purchase_orders.po_no AS po_no,
            am_purchase_orders.po_date AS tanggal_po,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
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

    public function getListLogPoImportBb($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'rm_import_pos.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'     => 'stock_revamp.divisi_id',
            'warehouse_id'  => 'stock_revamp.warehouse_id',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'po_id'         => 'stock_revamp_detail.po_id',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'tanggal_po'    => 'rm_import_pos.tanggal',
            'createdAt'     => 'stock_revamp_log.createdAt',
            'qty_diterima'  => 'stock_revamp_log.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id',

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_revamp_log.createdAt';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            stock_revamp_log.*,
            suppliers.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            stock_revamp_detail.type_bc,
            penerimaan_barang.no_penerimaan_barang AS ref_no,
            rm_import_pos.po_no AS po_no,
            rm_import_pos.po_date AS tanggal_po,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('rm_import_pos', 'rm_import_pos.id = stock_revamp_detail.po_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('rm_import_pos.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
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

    public function getListLogProsesRebus($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'rm_purchase_orders.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'     => 'stock_revamp.divisi_id',
            'warehouse_id'  => 'stock_revamp.warehouse_id',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'tanggal_po'    => 'rm_import_pos.tanggal',
            'createdAt'     => 'stock_revamp_log.createdAt',
            'qty_diterima'  => 'stock_revamp_log.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id',

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_revamp_log.createdAt';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            stock_revamp_log.*,
            suppliers.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            rm_purchase_orders.po_no,
            rm_purchase_orders.po_date AS tanggal_po,
            stock_revamp_detail.type_bc,
            proses_rebus.no_rebus AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('proses_rebus', 'proses_rebus.id = stock_revamp_detail.reference_id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = stock_revamp_detail.po_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('proses_rebus.no_rebus', $addCondition['search'])
                ->orLike('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
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

    public function getListLogJasaVendor($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id'   => 'jasa_vendor_in.vendor_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'     => 'stock_revamp.divisi_id',
            'warehouse_id'  => 'stock_revamp.warehouse_id',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'createdAt'     => 'stock_revamp_log.createdAt',
            'qty_diterima'  => 'stock_revamp_log.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id',

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_revamp_log.createdAt';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            stock_revamp_log.*,
            vendors.name AS supplier_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            stock_revamp_detail.type_bc,
            jasa_vendor_in.no_penerimaan_surat_jalan AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = stock_revamp_detail.reference_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('jasa_vendor_in.no_penerimaan_surat_jalan', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
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

    public function getListLogHasilProduksi($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'     => 'stock_revamp.divisi_id',
            'warehouse_id'  => 'stock_revamp.warehouse_id',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'createdAt'     => 'stock_revamp_log.createdAt',
            'qty_diterima'  => 'stock_revamp_log.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_revamp_log.createdAt';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            stock_revamp_log.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            stock_revamp_detail.type_bc,
            production_results.pr_no AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('production_results', 'production_results.id = stock_revamp_detail.reference_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('production_results.pr_no', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
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

    public function getListLogMaterialRequestBaku($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'     => 'stock_revamp.divisi_id',
            'warehouse_id'  => 'stock_revamp.warehouse_id',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'createdAt'     => 'stock_revamp_log.createdAt',
            'qty_diterima'  => 'stock_revamp_log.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_revamp_log.createdAt';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            stock_revamp_log.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            stock_revamp_detail.type_bc,
            material_requests.req_no AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('material_requests', 'material_requests.id = stock_revamp_detail.reference_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('material_requests.req_no', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
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

    public function getListLogMaterialRequestPenolong($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'     => 'stock_revamp.divisi_id',
            'warehouse_id'  => 'stock_revamp.warehouse_id',
            'type_bc'       => 'stock_revamp_detail.type_bc',
            'reference_id'  => 'stock_revamp_detail.reference_id',
            'createdAt'     => 'stock_revamp_log.createdAt',
            'qty_diterima'  => 'stock_revamp_log.qty_diterima',
            'unit_id'       => 'stock_revamp.unit_id',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_revamp_log.createdAt';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            stock_revamp_log.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            stock_revamp_detail.type_bc,
            material_requests_penolong.req_no AS ref_no,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('material_requests_penolong', 'material_requests_penolong.id = stock_revamp_detail.reference_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('DATE(stock_revamp_log.createdAt) <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('material_requests_penolong.req_no', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
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
}
