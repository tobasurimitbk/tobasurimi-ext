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

    public function getListLogInisiasi($condition = [], $addCondition = [], $limit = 10, $offset = 0)
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
                ->like('divisis.divisi', $addCondition['search'])
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

    public function getListLogAdjusment($condition = [], $addCondition = [], $limit = 10, $offset = 0)
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
            satuans.kode_satuan,
            adjusment.no_adjusment AS ref_no,
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
            ->join('adjusment', 'adjusment.id = stock_revamp_detail.reference_id', 'left')
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
                ->like('divisis.divisi', $addCondition['search'])
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

    public function getListLogPenerimaanMutasi($condition = [], $addCondition = [], $limit = 10, $offset = 0)
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
            satuans.kode_satuan,
            penerimaan_mutasi.penerimaan_mutasi_no AS ref_no,
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
            ->join('penerimaan_mutasi', 'penerimaan_mutasi.id = stock_revamp_detail.reference_id', 'left')
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
                ->like('divisis.divisi', $addCondition['search'])
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

    public function getListLogPenerimaanMutasiGlobal($condition = [], $addCondition = [], $limit = 10, $offset = 0)
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
            satuans.kode_satuan,
            penerimaan_mutasi_global.penerimaan_mutasi_no AS ref_no,
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
            ->join('penerimaan_mutasi_global', 'penerimaan_mutasi_global.id = stock_revamp_detail.reference_id', 'left')
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
                ->like('divisis.divisi', $addCondition['search'])
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

    public function getKartuStockMasuk(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {

        $db = \Config\Database::connect();

        // ============================
        // 🔍 FILTER KONDISI
        // ============================

        $where = [];
        $whereDatePenerimaanBarang = "";
        $whereDateProsesRebus = "";
        $whereDateJasaVendor = "";
        $whereDateHasilProduksi = "";
        $whereDateInisiasi = "";
        $whereDateAdjusment = "";
        $whereDatePenerimaanMutasi = "";
        $whereDatePenerimaanMutasiGlobal = "";
        $whereDateMaterialRequest = "";
        $whereDateMaterialRequestPenolong = "";

        $searchPoLokalBb = "";
        $searchPoBp = "";
        $searchPoImportBb = "";
        $searchProsesRebus = "";
        $searchJasaVendor = "";
        $searchHasilProduksi = "";
        $searchAdjusment = "";
        $searchPenerimaanMutasi = "";
        $searchPenerimaanMutasiGlobal = "";
        $searchMaterialRequest = "";
        $searchMaterialRequestPenolong = "";

        if (!empty($condition['id'])) {
            $where[] = "stock_revamp_detail.id = '$condition[id]'";
        }

        if (!empty($condition['company_id'])) {
            $where[] = "stock_revamp.company_id = '$condition[company_id]'";
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDatePenerimaanBarang = "AND penerimaan_barang.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateProsesRebus = "AND proses_rebus.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateJasaVendor = "AND jasa_vendor_in.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateHasilProduksi = "AND production_results.receive_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateInisiasi = "AND DATE(stock_revamp_log.createdAt) BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateAdjusment = "AND adjusment.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDatePenerimaanMutasi = "AND penerimaan_mutasi.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDatePenerimaanMutasiGlobal = "AND penerimaan_mutasi_global.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequest = "AND material_requests.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequestPenolong = "AND material_requests_penolong.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['stock_id'])) {
            $where[] = "stock_revamp.id = '$condition[stock_id]'";
        }
        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));
            $searchPoLokalBb = "
                AND
                    (
                        suppliers.name LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                        OR rm_purchase_orders.po_no LIKE '%{$search}%'
                    )

            ";
            $searchPoBp = "
                AND 
                    (
                        am_purchase_orders.po_no LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                        OR suppliers.name LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                        OR purchase_requests.spp_no LIKE '%{$search}%'
                    )
            ";
            $searchPoImportBb = "
                AND 
                    (
                        rm_import_pos.po_no LIKE '%{$search}%'
                        OR suppliers.name LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                    )
            ";
            $searchProsesRebus = "
                AND 
                    (
                        proses_rebus.no_rebus LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                        OR rm_purchase_orders.po_no LIKE '%{$search}%'
                    )
            ";
            $searchJasaVendor = "
                AND
                    ( 
                       jasa_vendor_in.no_penerimaan_surat_jalan LIKE '%{$search}%'
                    )
            ";
            $searchHasilProduksi = "
                AND 
                    (
                       production_results.pr_no LIKE '%{$search}%'
                    )
            ";
            $searchAdjusment = "
                AND 
                    (
                       adjusment.no_adjusment LIKE '%{$search}%'
                    )
            ";
            $searchPenerimaanMutasi = "
                AND 
                    (
                       penerimaan_mutasi.penerimaan_mutasi_no LIKE '%{$search}%'
                    )
            ";
            $searchPenerimaanMutasiGlobal = "
                AND 
                    (
                        penerimaan_mutasi_global.penerimaan_mutasi_no LIKE '%{$search}%'
                    )
            ";
            $searchMaterialRequest = "
                AND 
                    (
                        material_requests.req_no LIKE '%{$search}%'
                    )
            ";
            $searchMaterialRequestPenolong = "
                AND 
                    (
                        material_requests_penolong.req_no LIKE '%{$search}%'
                    )
            ";
        }


        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'id',
            'reference_type',
            'supplier_name',
            'spp_no',
            'po_no',
            'reference_no',
            'po_date',
            'lpb_date',
            'kode_satuan',
            'qty_diterima',
            'keterangan'
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- STOK DARI PO LOKAL BAKU
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                suppliers.name AS supplier_name,
                '' AS spp_no,
                rm_purchase_orders.po_no AS po_no,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                rm_purchase_orders.po_date,
                penerimaan_barang.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_purchase_orders ON rm_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = rm_purchase_orders.supplier_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='BAKU'
            AND penerimaan_barang.status_penerimaan='LOKAL'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoLokalBb
        )
        UNION ALL
        (
            -- STOK DARI PO LOKAL BAHAN PENOLONG
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                suppliers.name AS supplier_name,
                purchase_requests.spp_no AS spp_no,
                am_purchase_orders.po_no AS po_no,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                am_purchase_orders.po_date,
                penerimaan_barang.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN am_purchase_orders ON am_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = am_purchase_orders.supplier_id
            LEFT JOIN purchase_requests ON purchase_requests.id = am_purchase_orders.purchase_request_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND penerimaan_barang.status_penerimaan='LOKAL'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoBp
        )
        UNION ALL
        (
            -- STOK DARI PO IMPORT BAHAN PENOLONG
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                suppliers.name AS supplier_name,
                '' AS spp_no,
                am_purchase_orders.po_no AS po_no,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                am_purchase_orders.po_date,
                penerimaan_barang.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN am_purchase_orders ON am_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = am_purchase_orders.supplier_id
            LEFT JOIN purchase_requests ON purchase_requests.id = am_purchase_orders.purchase_request_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND penerimaan_barang.status_penerimaan='IMPORT'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoBp
        )
        UNION ALL
        (
            -- STOK DARI PO IMPORT BAHAN BAKU
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                suppliers.name AS supplier_name,
                '' AS spp_no,
                rm_import_pos.po_no AS po_no,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                rm_import_pos.po_date,
                penerimaan_barang.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_import_pos ON rm_import_pos.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = rm_import_pos.supplier_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='BAKU'
            AND penerimaan_barang.status_penerimaan='IMPORT'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoImportBb
        )
        UNION ALL
        (
            -- STOK DARI PROSES REBUS
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                suppliers.name AS supplier_name,
                '' AS spp_no,
                rm_purchase_orders.po_no AS po_no,
                proses_rebus.no_rebus AS reference_no,
                rm_purchase_orders.po_date,
                proses_rebus.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_purchase_orders ON rm_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN proses_rebus ON proses_rebus.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = rm_purchase_orders.supplier_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PROSES REBUS'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDateProsesRebus
            $searchProsesRebus
        )
        UNION ALL
        (
            -- STOK DARI JASA VENDOR
             SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                vendors.name AS supplier_name,
                '' AS spp_no,
                '' AS po_no,
                jasa_vendor_in.no_penerimaan_surat_jalan AS reference_no,
                '' AS po_date,
                jasa_vendor_in.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN jasa_vendor_in ON jasa_vendor_in.id = stock_revamp_detail.reference_id
            LEFT JOIN vendors ON vendors.id = jasa_vendor_in.vendor_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='JASA VENDOR'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDateJasaVendor
            $searchJasaVendor
        )
        UNION ALL
        (
            -- STOK DARI HASIL PRODUKSI
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                '' AS supplier_name,
                '' AS spp_no,
                '' AS po_no,
                production_results.pr_no AS reference_no,
                '' AS po_date,
                production_results.receive_date AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN production_results ON production_results.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='HASIL PRODUKSI'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDateHasilProduksi
            $searchHasilProduksi
        )
        UNION ALL
        (
            -- STOK DARI INISIASI
             SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                '' AS supplier_name,
                '' AS spp_no,
                '' AS po_no,
                '' AS reference_no,
                '' AS po_date,
                DATE(stock_revamp_log.createdAt) AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='INISIASI'
            AND stock_revamp_log.status='IN'
            $whereDateInisiasi
            $filterCondition
        )
        UNION ALL
        (
            -- STOK DARI ADJUSMENT
             SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                '' AS supplier_name,
                '' AS spp_no,
                '' AS po_no,
                adjusment.no_adjusment AS reference_no,
                '' AS po_date,
                adjusment.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                adjusment.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN adjusment ON adjusment.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='ADJUSMENT'
            AND stock_revamp_log.status='IN'
            $whereDateAdjusment
            $filterCondition
            $searchAdjusment
        )
        UNION ALL
        (
            -- STOK DARI PENERIMAAN MUTASI
             SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                '' AS supplier_name,
                '' AS spp_no,
                '' AS po_no,
                penerimaan_mutasi.penerimaan_mutasi_no AS reference_no,
                '' AS po_date,
                penerimaan_mutasi.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN penerimaan_mutasi ON penerimaan_mutasi.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PENERIMAAN MUTASI'
            AND stock_revamp_log.status='IN'
            $whereDatePenerimaanMutasi
            $filterCondition
            $searchPenerimaanMutasi
        )
        UNION ALL
        (
            -- STOK DARI PENERIMAAN MUTASI GLOBAL
             SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                '' AS supplier_name,
                '' AS spp_no,
                '' AS po_no,
                penerimaan_mutasi_global.penerimaan_mutasi_no AS reference_no,
                '' AS po_date,
                penerimaan_mutasi_global.tanggal AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN penerimaan_mutasi_global ON penerimaan_mutasi_global.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PENERIMAAN MUTASI GLOBAL'
            AND stock_revamp_log.status='IN'
            $whereDatePenerimaanMutasiGlobal
            $filterCondition
            $searchPenerimaanMutasiGlobal
        )
        UNION ALL
        (
            -- STOK DARI MATERIAL REQUEST BAKU
             SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                '' AS supplier_name,
                '' AS spp_no,
                '' AS po_no,
                material_requests.req_no AS reference_no,
                '' AS po_date,
                material_requests.request_date AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests ON material_requests.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='MATERIAL REQUEST BAKU'
            AND stock_revamp_log.status='IN'
            $whereDateMaterialRequest
            $filterCondition
            $searchMaterialRequest
        )
         UNION ALL
        (
            -- STOK DARI MATERIAL REQUEST PENOLONG
             SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                '' AS supplier_name,
                '' AS spp_no,
                '' AS po_no,
                material_requests_penolong.req_no AS reference_no,
                '' AS po_date,
                material_requests_penolong.request_date AS lpb_date,
                stock_revamp_log.qty_diterima AS qty_diterima,
                stock_revamp_log.keterangan,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests_penolong ON material_requests_penolong.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='MATERIAL REQUEST PENOLONG'
            AND stock_revamp_log.status='IN'
            $whereDateMaterialRequestPenolong
            $filterCondition
            $searchMaterialRequestPenolong
        )
        ";


        // ============================
        // 📊 COUNT + PAGINATION
        // ============================

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = "
            SELECT * FROM ($baseQuery) AS x
            $orderBy
            LIMIT $limit OFFSET $offset
        ";

        $data = $db->query($mainQuery)->getResultArray();

        // ============================
        // 📦 RETURN RESULT
        // ============================

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getKartuStockKeluar(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {

        $db = \Config\Database::connect();

        // ============================
        // 🔍 FILTER KONDISI
        // ============================

        $where = [];
        $whereDateProsesRebus = "";
        $whereDateJasaVendor = "";
        $whereDateMaterialRequest = "";
        $whereDateMaterialRequestPenolong = "";
        $whereDateMutasi = "";
        $whereDateMutasiGlobal = "";
        $whereDateStuffingLokal = "";
        $whereDateStuffingEkspor = "";
        $whereDateAdjusment = "";

        $searchProsesRebus = "";
        $searchJasaVendor = "";
        $searchMaterialRequest = "";
        $searchMaterialRequestPenolong = "";
        $searchMutasi = "";
        $searchMutasiGlobal = "";
        $searchStuffingLokal = "";
        $searchStuffingEkspor = "";
        $searchAdjusment = "";

        $filterAdjusment = "";

        if (!empty($condition['id'])) {
            $where[] = "stock_revamp_detail.id = '$condition[id]'";
        }

        if (!empty($condition['company_id'])) {
            $where[] = "stock_revamp.company_id = '$condition[company_id]'";
        }

        if (!empty($condition['filter_pengeluaran'])) {
            if ($condition['filter_pengeluaran'] == "MATERIAL REQUEST") {
                $where[] = "(stock_revamp_log.reference_tujuan_type='MATERIAL REQUEST BAKU' OR stock_revamp_log.reference_tujuan_type='MATERIAL REQUEST PENOLONG')";
            } elseif ($condition['filter_pengeluaran'] == "MUTASI") {
                $where[] = "(stock_revamp_log.reference_tujuan_type='MUTASI' OR stock_revamp_log.reference_tujuan_type='MUTASI GLOBAL')";
            } elseif ($condition['filter_pengeluaran'] == "STUFFING") {
                $where[] = "(stock_revamp_log.reference_tujuan_type='STUFFING LOKAL' OR stock_revamp_log.reference_tujuan_type='STUFFING EKSPOR')";
            } elseif (in_array($condition['filter_pengeluaran'], ["ANALISA", "SAMPLE", "LAINNYA", "BONUS", "JUAL LOKAL"])) {
                $where[] = "stock_revamp_log.reference_tujuan_type='ADJUSMENT'";
                $filterAdjusment = "AND metadata.value='$condition[filter_pengeluaran]'";
            } elseif ($condition['filter_pengeluaran'] != "ALL") {
                $where[] = "stock_revamp_log.reference_tujuan_type='$condition[filter_pengeluaran]'";
            }
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDateProsesRebus = "AND proses_rebus.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateJasaVendor = "AND jasa_vendor_out.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequest = "AND material_requests.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequestPenolong = "AND material_requests_penolong.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMutasi = "AND mutasi.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMutasiGlobal = "AND mutasi_global.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateStuffingLokal = "AND stuffing_lokal.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateStuffingEkspor = "AND stuffing_internasional.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateAdjusment = "AND adjusment.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['stock_id'])) {
            $where[] = "stock_revamp.id = '$condition[stock_id]'";
        }
        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));

            $searchProsesRebus = "
                AND (
                    proses_rebus.no_rebus LIKE '%{$search}%'
                    OR divisis.divisi LIKE '%{$search}%'
                    OR warehouses.warehouse_name LIKE '%{$search}%'    
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                    )
            ";

            $searchJasaVendor = "
                AND (
                    jasa_vendor_out.no_surat_jalan LIKE '%{$search}%'
                    OR divisis.divisi LIKE '%{$search}%'
                    OR warehouses.warehouse_name LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'
                )
            ";

            $searchMaterialRequest = "
                AND (
                    material_requests.req_no LIKE '%{$search}%'
                    OR divisis.divisi LIKE '%{$search}%'
                    OR warehouses.warehouse_name LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'
                )
            ";

            $searchMaterialRequestPenolong = "
                AND (
                    material_requests_penolong.req_no LIKE '%{$search}%'
                    OR divisis.divisi LIKE '%{$search}%'
                    OR warehouses.warehouse_name LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchMutasi = "
                AND (
                    mutasi.no_mutasi LIKE '%{$search}%'
                    OR divisis.divisi LIKE '%{$search}%'
                    OR warehouses.warehouse_name LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchMutasiGlobal = "
                AND (
                    mutasi_global.no_mutasi LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchStuffingLokal = "
                AND (
                    stuffing_lokal.no_stuffing LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchStuffingEkspor = "
                AND (
                    stuffing_internasional.no_stuffing LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchAdjusment = "
                AND (
                    adjusment.no_adjusment LIKE '%{$search}%'
                    OR adjusment.keterangan LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'
                    OR metadata.value LIKE '%{$search}%'
                )
            ";
        }


        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'id',
            'reference_tujuan_type',
            'divisi_tujuan',
            'warehouse_tujuan',
            'reference_no',
            'tanggal_keluar',
            'keterangan',
            'qty_diterima',
            'kode_satuan',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- STOK KELUAR KE PROSES REBUS
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                divisis.divisi AS divisi_tujuan,
                warehouses.warehouse_name AS warehouse_tujuan,
                proses_rebus.no_rebus AS reference_no,
                proses_rebus.tanggal AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN proses_rebus ON proses_rebus.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN divisis ON divisis.id = proses_rebus.divisi_id
            LEFT JOIN warehouses ON warehouses.id = proses_rebus.warehouse_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='PROSES REBUS'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateProsesRebus
            $searchProsesRebus
        )
        UNION ALL
        (
            -- STOK KELUAR TUJUAN JASA VENDOR
             SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                divisis.divisi AS divisi_tujuan,
                warehouses.warehouse_name AS warehouse_tujuan,
                jasa_vendor_out.no_surat_jalan AS reference_no,
                jasa_vendor_out.tanggal AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN jasa_vendor_out ON jasa_vendor_out.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN divisis ON divisis.id = jasa_vendor_out.divisi_id
            LEFT JOIN warehouses ON warehouses.id = jasa_vendor_out.warehouse_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='JASA VENDOR'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateJasaVendor
            $searchJasaVendor
        )
        UNION ALL
        (
            -- STOK KELUAR MATERIAL REQUEST BAKU
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                divisis.divisi AS divisi_tujuan,
                warehouses.warehouse_name AS warehouse_tujuan,
                material_requests.req_no AS reference_no,
                material_requests.request_date AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests ON material_requests.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN material_request_details ON material_request_details.material_request_id = stock_revamp_log.reference_tujuan_id AND material_request_details.stock_detail_id=stock_revamp_detail.id
            LEFT JOIN divisis ON divisis.id = material_request_details.divisi_tujuan_id
            LEFT JOIN warehouses ON warehouses.id = material_request_details.warehouse_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='MATERIAL REQUEST BAKU'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateMaterialRequest
            $searchMaterialRequest
        )
        UNION ALL
        (
            -- STOK KELUAR MATERIAL REQUEST PENOLONG
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                divisis.divisi AS divisi_tujuan,
                warehouses.warehouse_name AS warehouse_tujuan,
                material_requests_penolong.req_no AS reference_no,
                material_requests_penolong.request_date AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests_penolong ON material_requests_penolong.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN material_request_penolong_details ON material_requests_penolong.id = stock_revamp_log.reference_tujuan_id AND material_request_penolong_details.stock_detail_id=stock_revamp_detail.id
            LEFT JOIN divisis ON divisis.id = material_request_penolong_details.divisi_id
            LEFT JOIN warehouses ON warehouses.id = material_request_penolong_details.warehouse_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='MATERIAL REQUEST PENOLONG'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateMaterialRequestPenolong
            $searchMaterialRequestPenolong
        )
        UNION ALL
        (
            -- STOK KELUAR MUTASI
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                divisis.divisi AS divisi_tujuan,
                warehouses.warehouse_name AS warehouse_name,
                mutasi.no_mutasi AS reference_no,
                mutasi.tanggal AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN mutasi ON mutasi.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN divisis ON divisis.id = mutasi.divisi_tujuan_id
            LEFT JOIN warehouses ON warehouses.id = mutasi.warehouse_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='MUTASI'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateMutasi
            $searchMutasi
        )
        UNION ALL
        (
            -- STOK KELUAR MUTASI GLOBAL
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                '' AS divisi_tujuan,
                '' AS warehouse_name,
                mutasi_global.no_mutasi AS reference_no,
                mutasi_global.tanggal AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN mutasi_global ON mutasi_global.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='MUTASI GLOBAL'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateMutasiGlobal
            $searchMutasiGlobal
        )
        UNION ALL
        (
            -- STOK KELUAR STUFFING LOKAL
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                '' AS divisi_tujuan,
                '' AS warehouse_name,
                stuffing_lokal.no_stuffing AS reference_no,
                stuffing_lokal.tanggal AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN stuffing_lokal ON stuffing_lokal.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='STUFFING LOKAL'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateStuffingLokal
            $searchStuffingLokal
        )
        UNION ALL
        (
            -- STOK KELUAR STUFFING EKSPOR
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                '' AS divisi_tujuan,
                '' AS warehouse_name,
                stuffing_internasional.no_stuffing AS reference_no,
                stuffing_internasional.tanggal AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN stuffing_internasional ON stuffing_internasional.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='STUFFING EKSPOR'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateStuffingEkspor
            $searchStuffingEkspor
        )
        UNION ALL
        (
            -- STOK KELUAR ADJUSMENT
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                divisis.divisi AS divisi_tujuan,
                '' AS warehouse_name,
                adjusment.no_adjusment AS reference_no,
                adjusment.tanggal AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN adjusment ON adjusment.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN divisis ON divisis.id = adjusment.divisi_id
            LEFT JOIN metadata ON metadata.id = adjusment.tipe_adjusment
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='ADJUSMENT'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $filterAdjusment
            $whereDateAdjusment
            $searchAdjusment
        )
        UNION ALL
        (
            -- STOK KELUAR NULL
            SELECT
                stock_revamp_detail.stock_id,
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type,
                '' AS divisi_tujuan,
                '' AS warehouse_name,
                '' AS reference_no,
                DATE(stock_revamp_log.createdAt) AS tanggal_keluar,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_diterima,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type IS NULL
            AND stock_revamp_log.status='OUT'
            $filterCondition
        )
        ";

        // ============================
        // 📊 COUNT + PAGINATION
        // ============================

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = "
            SELECT * FROM ($baseQuery) AS x
            $orderBy
            LIMIT $limit OFFSET $offset
        ";



        // var_dump($mainQuery);
        // die;
        $data = $db->query($mainQuery)->getResultArray();

        // ============================
        // 📦 RETURN RESULT
        // ============================

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getDetailSaldoAkhir(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        $db = \Config\Database::connect();

        $columns = [
            'id',
            'reference_type',
            'tanggal',
            'po_no',
            'reference_no',
            'keterangan',
            'qty_masuk',
            'qty_keluar',
            'kode_satuan'
        ];


        // ============================
        // 🔍 OUT
        // ============================

        $where = [];
        $whereDateProsesRebus = "";
        $whereDateJasaVendor = "";
        $whereDateMaterialRequest = "";
        $whereDateMaterialRequestPenolong = "";
        $whereDateMutasi = "";
        $whereDateMutasiGlobal = "";
        $whereDateStuffingLokal = "";
        $whereDateStuffingEkspor = "";
        $whereDateAdjusment = "";

        $searchProsesRebus = "";
        $searchJasaVendor = "";
        $searchMaterialRequest = "";
        $searchMaterialRequestPenolong = "";
        $searchMutasi = "";
        $searchMutasiGlobal = "";
        $searchStuffingLokal = "";
        $searchStuffingEkspor = "";
        $searchAdjusment = "";

        $filterAdjusment = "";

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDateProsesRebus = "AND proses_rebus.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateJasaVendor = "AND jasa_vendor_out.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequest = "AND material_requests.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequestPenolong = "AND material_requests_penolong.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMutasi = "AND mutasi.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMutasiGlobal = "AND mutasi_global.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateStuffingLokal = "AND stuffing_lokal.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateStuffingEkspor = "AND stuffing_internasional.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateAdjusment = "AND adjusment.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['stock_id'])) {
            $where[] = "stock_revamp.id = '$condition[stock_id]'";
        }
        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));

            $searchProsesRebus = "
                AND (
                    proses_rebus.no_rebus LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                    )
            ";

            $searchJasaVendor = "
                AND (
                    jasa_vendor_out.no_surat_jalan LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'
                )
            ";

            $searchMaterialRequest = "
                AND (
                    material_requests.req_no LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'
                )
            ";

            $searchMaterialRequestPenolong = "
                AND (
                    material_requests_penolong.req_no LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchMutasi = "
                AND (
                    mutasi.no_mutasi LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchMutasiGlobal = "
                AND (
                    mutasi_global.no_mutasi LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchStuffingLokal = "
                AND (
                    stuffing_lokal.no_stuffing LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchStuffingEkspor = "
                AND (
                    stuffing_internasional.no_stuffing LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'     
                )
            ";

            $searchAdjusment = "
                AND (
                    adjusment.no_adjusment LIKE '%{$search}%'
                    OR adjusment.keterangan LIKE '%{$search}%'
                    OR stock_revamp_log.reference_tujuan_type LIKE '%{$search}%'
                    OR metadata.value LIKE '%{$search}%'
                )
            ";
        }

        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- STOK KELUAR KE PROSES REBUS
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                proses_rebus.no_rebus AS reference_no,
                proses_rebus.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN proses_rebus ON proses_rebus.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='PROSES REBUS'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateProsesRebus
            $searchProsesRebus
        )
        UNION ALL
        (
            -- STOK KELUAR TUJUAN JASA VENDOR
             SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                jasa_vendor_out.no_surat_jalan AS reference_no,
                jasa_vendor_out.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN jasa_vendor_out ON jasa_vendor_out.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='JASA VENDOR'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateJasaVendor
            $searchJasaVendor
        )
        UNION ALL
        (
            -- STOK KELUAR MATERIAL REQUEST BAKU
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                material_requests.req_no AS reference_no,
                material_requests.request_date AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests ON material_requests.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN material_request_details ON material_request_details.material_request_id = stock_revamp_log.reference_tujuan_id AND material_request_details.stock_detail_id=stock_revamp_detail.id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='MATERIAL REQUEST BAKU'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateMaterialRequest
            $searchMaterialRequest
        )
        UNION ALL
        (
            -- STOK KELUAR MATERIAL REQUEST PENOLONG
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                material_requests_penolong.req_no AS reference_no,
                material_requests_penolong.request_date AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests_penolong ON material_requests_penolong.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN material_request_penolong_details ON material_requests_penolong.id = stock_revamp_log.reference_tujuan_id AND material_request_penolong_details.stock_detail_id=stock_revamp_detail.id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='MATERIAL REQUEST PENOLONG'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateMaterialRequestPenolong
            $searchMaterialRequestPenolong
        )
        UNION ALL
        (
            -- STOK KELUAR MUTASI
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                mutasi.no_mutasi AS reference_no,
                mutasi.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN mutasi ON mutasi.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='MUTASI'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateMutasi
            $searchMutasi
        )
        UNION ALL
        (
            -- STOK KELUAR MUTASI GLOBAL
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                mutasi_global.no_mutasi AS reference_no,
                mutasi_global.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN mutasi_global ON mutasi_global.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='MUTASI GLOBAL'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateMutasiGlobal
            $searchMutasiGlobal
        )
        UNION ALL
        (
            -- STOK KELUAR STUFFING LOKAL
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                stuffing_lokal.no_stuffing AS reference_no,
                stuffing_lokal.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN stuffing_lokal ON stuffing_lokal.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='STUFFING LOKAL'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateStuffingLokal
            $searchStuffingLokal
        )
        UNION ALL
        (
            -- STOK KELUAR STUFFING EKSPOR
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                stuffing_internasional.no_stuffing AS reference_no,
                stuffing_internasional.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN stuffing_internasional ON stuffing_internasional.id = stock_revamp_log.reference_tujuan_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='STUFFING EKSPOR'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $whereDateStuffingEkspor
            $searchStuffingEkspor
        )
        UNION ALL
        (
            -- STOK KELUAR ADJUSMENT
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                adjusment.no_adjusment AS reference_no,
                adjusment.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN adjusment ON adjusment.id = stock_revamp_log.reference_tujuan_id
            LEFT JOIN metadata ON metadata.id = adjusment.tipe_adjusment
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type='ADJUSMENT'
            AND stock_revamp_log.status='OUT'
            $filterCondition
            $filterAdjusment
            $whereDateAdjusment
            $searchAdjusment
        )
        UNION ALL
        (
            -- STOK KELUAR NULL
            SELECT
                stock_revamp_log.id,
                stock_revamp_log.reference_tujuan_type AS reference_type,
                '' AS reference_no,
                DATE(stock_revamp_log.createdAt) AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                '' AS qty_masuk,
                stock_revamp_log.qty_diterima AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_log.reference_tujuan_type IS NULL
            AND stock_revamp_log.status='OUT'
            $filterCondition
        )
        ";

        // ============================
        // 📊 IN
        // ============================

        $where = [];
        $whereDatePenerimaanBarang = "";
        $whereDateProsesRebus = "";
        $whereDateJasaVendor = "";
        $whereDateHasilProduksi = "";
        $whereDateInisiasi = "";
        $whereDateAdjusment = "";
        $whereDatePenerimaanMutasi = "";
        $whereDatePenerimaanMutasiGlobal = "";
        $whereDateMaterialRequest = "";
        $whereDateMaterialRequestPenolong = "";

        $searchPoLokalBb = "";
        $searchPoBp = "";
        $searchPoImportBb = "";
        $searchProsesRebus = "";
        $searchJasaVendor = "";
        $searchHasilProduksi = "";
        $searchAdjusment = "";
        $searchPenerimaanMutasi = "";
        $searchPenerimaanMutasiGlobal = "";
        $searchMaterialRequest = "";
        $searchMaterialRequestPenolong = "";

        if (!empty($condition['id'])) {
            $where[] = "stock_revamp_detail.id = '$condition[id]'";
        }

        if (!empty($condition['company_id'])) {
            $where[] = "stock_revamp.company_id = '$condition[company_id]'";
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDatePenerimaanBarang = "AND penerimaan_barang.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateProsesRebus = "AND proses_rebus.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateJasaVendor = "AND jasa_vendor_in.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateHasilProduksi = "AND production_results.receive_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateInisiasi = "AND DATE(stock_revamp_log.createdAt) BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateAdjusment = "AND adjusment.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDatePenerimaanMutasi = "AND penerimaan_mutasi.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDatePenerimaanMutasiGlobal = "AND penerimaan_mutasi_global.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequest = "AND material_requests.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequestPenolong = "AND material_requests_penolong.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['stock_id'])) {
            $where[] = "stock_revamp.id = '$condition[stock_id]'";
        }
        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));
            $searchPoLokalBb = "
                AND
                    (
                        stock_revamp_detail.reference_type LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                        OR rm_purchase_orders.po_no LIKE '%{$search}%'
                    )

            ";
            $searchPoBp = "
                AND 
                    (
                        am_purchase_orders.po_no LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                    )
            ";
            $searchPoImportBb = "
                AND 
                    (
                        rm_import_pos.po_no LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                    )
            ";
            $searchProsesRebus = "
                AND 
                    (
                        proses_rebus.no_rebus LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                        OR rm_purchase_orders.po_no LIKE '%{$search}%'
                    )
            ";
            $searchJasaVendor = "
                AND
                    ( 
                       jasa_vendor_in.no_penerimaan_surat_jalan LIKE '%{$search}%'
                       OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                    )
            ";
            $searchHasilProduksi = "
                AND 
                    (
                       production_results.pr_no LIKE '%{$search}%'
                       OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                    )
            ";
            $searchAdjusment = "
                AND 
                    (
                       adjusment.no_adjusment LIKE '%{$search}%'
                       OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                    )
            ";
            $searchPenerimaanMutasi = "
                AND 
                    (
                       penerimaan_mutasi.penerimaan_mutasi_no LIKE '%{$search}%'
                       OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                    )
            ";
            $searchPenerimaanMutasiGlobal = "
                AND 
                    (
                        penerimaan_mutasi_global.penerimaan_mutasi_no LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                    )
            ";
            $searchMaterialRequest = "
                AND 
                    (
                        material_requests.req_no LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                    )
            ";
            $searchMaterialRequestPenolong = "
                AND 
                    (
                        material_requests_penolong.req_no LIKE '%{$search}%'
                        OR stock_revamp_detail.reference_type LIKE '%{$search}%'
                    )
            ";
        }


        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";


        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery .= "
        UNION ALL
        (
            -- STOK DARI PO LOKAL BAKU
            SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                penerimaan_barang.tanggal AS tanggal,
                rm_purchase_orders.po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_purchase_orders ON rm_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='BAKU'
            AND penerimaan_barang.status_penerimaan='LOKAL'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoLokalBb
        )
        UNION ALL
        (
            -- STOK DARI PO LOKAL BAHAN PENOLONG
            SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                penerimaan_barang.tanggal,
                am_purchase_orders.po_no AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN am_purchase_orders ON am_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND penerimaan_barang.status_penerimaan='LOKAL'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoBp
        )
        UNION ALL
        (
            -- STOK DARI PO IMPORT BAHAN PENOLONG
            SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                penerimaan_barang.tanggal AS tanggal,
                am_purchase_orders.po_no AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,                
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN am_purchase_orders ON am_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND penerimaan_barang.status_penerimaan='IMPORT'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoBp
        )
        UNION ALL
        (
            -- STOK DARI PO IMPORT BAHAN BAKU
            SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                penerimaan_barang.tanggal AS tanggal,
                rm_import_pos.po_no AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_import_pos ON rm_import_pos.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='BAKU'
            AND penerimaan_barang.status_penerimaan='IMPORT'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoImportBb
        )
        UNION ALL
        (
            -- STOK DARI PROSES REBUS
            SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                proses_rebus.no_rebus AS reference_no,
                proses_rebus.tanggal,
                rm_purchase_orders.po_no AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_purchase_orders ON rm_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN proses_rebus ON proses_rebus.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PROSES REBUS'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDateProsesRebus
            $searchProsesRebus
        )
        UNION ALL
        (
            -- STOK DARI JASA VENDOR
             SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                jasa_vendor_in.no_penerimaan_surat_jalan AS reference_no,
                jasa_vendor_in.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN jasa_vendor_in ON jasa_vendor_in.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='JASA VENDOR'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDateJasaVendor
            $searchJasaVendor
        )
        UNION ALL
        (
            -- STOK DARI HASIL PRODUKSI
            SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                production_results.pr_no AS reference_no,
                production_results.receive_date AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN production_results ON production_results.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='HASIL PRODUKSI'
            AND stock_revamp_log.status='IN'
            $filterCondition
            $whereDateHasilProduksi
            $searchHasilProduksi
        )
        UNION ALL
        (
            -- STOK DARI INISIASI
             SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                '' AS reference_no,
                DATE(stock_revamp_log.createdAt) AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='INISIASI'
            AND stock_revamp_log.status='IN'
            $whereDateInisiasi
            $filterCondition
        )
        UNION ALL
        (
            -- STOK DARI ADJUSMENT
             SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                adjusment.no_adjusment AS reference_no,
                adjusment.tanggal AS tanggal,
                '' AS po_no,
                adjusment.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN adjusment ON adjusment.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='ADJUSMENT'
            AND stock_revamp_log.status='IN'
            $whereDateAdjusment
            $filterCondition
            $searchAdjusment
        )
        UNION ALL
        (
            -- STOK DARI PENERIMAAN MUTASI
             SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                penerimaan_mutasi.penerimaan_mutasi_no AS reference_no,
                penerimaan_mutasi.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN penerimaan_mutasi ON penerimaan_mutasi.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PENERIMAAN MUTASI'
            AND stock_revamp_log.status='IN'
            $whereDatePenerimaanMutasi
            $filterCondition
            $searchPenerimaanMutasi
        )
        UNION ALL
        (
            -- STOK DARI PENERIMAAN MUTASI GLOBAL
             SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                penerimaan_mutasi_global.penerimaan_mutasi_no AS reference_no,
                penerimaan_mutasi_global.tanggal AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN penerimaan_mutasi_global ON penerimaan_mutasi_global.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PENERIMAAN MUTASI GLOBAL'
            AND stock_revamp_log.status='IN'
            $whereDatePenerimaanMutasiGlobal
            $filterCondition
            $searchPenerimaanMutasiGlobal
        )
        UNION ALL
        (
            -- STOK DARI MATERIAL REQUEST BAKU
             SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                material_requests.req_no AS reference_no,
                material_requests.request_date AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests ON material_requests.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='MATERIAL REQUEST BAKU'
            AND stock_revamp_log.status='IN'
            $whereDateMaterialRequest
            $filterCondition
            $searchMaterialRequest
        )
         UNION ALL
        (
            -- STOK DARI MATERIAL REQUEST PENOLONG
             SELECT
                stock_revamp_log.id,
                stock_revamp_detail.reference_type,
                material_requests_penolong.req_no AS reference_no,
                material_requests_penolong.request_date AS tanggal,
                '' AS po_no,
                stock_revamp_log.keterangan,
                stock_revamp_log.qty_diterima AS qty_masuk,
                '' AS qty_keluar,
                satuans.kode_satuan
            FROM stock_revamp_log
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = stock_revamp_log.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests_penolong ON material_requests_penolong.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_log.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='MATERIAL REQUEST PENOLONG'
            AND stock_revamp_log.status='IN'
            $whereDateMaterialRequestPenolong
            $filterCondition
            $searchMaterialRequestPenolong
        )
        ";


        // ============================
        // 📊 COUNT + PAGINATION
        // ============================

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = "
            SELECT * FROM ($baseQuery) AS x
            $orderBy
            LIMIT $limit OFFSET $offset
        ";

        $data = $db->query($mainQuery)->getResultArray();

        // ============================
        // 📦 RETURN RESULT
        // ============================

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }
}
