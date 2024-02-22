<?php

namespace App\Models;

use CodeIgniter\Model;

class StockDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [
        'stock_id',
        'qty',
        'status',
        'stock_date',
        'created_by',
        'sumber',
        'no_dokumen',
        'keterangan',

        'barang_id',
        'warehouse_id',
        'stock_type',
        'spesifikasi',
    ];

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

    public function addOrReduceStock($barangID, $warehouseID, $stockType = 'New', $qty, $status = 'IN', $spesifikasi)
    {
        $this->insert([
            'barang_id' => $barangID,
            'warehouse_id' => $warehouseID,
            'stock_type' => $stockType,
            'qty' => $qty,
            'status' => $status,
            'spesifikasi' => $spesifikasi,
            'stock_date' => date('Y-m-d')
        ]);
    }

    public function insertStokDetail(
        $stok_id,
        $qty,
        $status, // IN, OUT
        $stok_date,
        $created_by,
        $sumber, // [ADJUSMENT,INISIASI,LPB,MUTASI,PRODUKSI,PENJUALAN]
        $no_dokumen, // NO PO, NO LPB, NO PRODUKSI
        $keterangan,
    ) {
        $stokDetail = $this->insert([
            'stock_id' => $stok_id,
            'qty' => $qty,
            'status' => $status,
            'stock_date' => $stok_date,
            'created_by' => $created_by,
            'sumber' => $sumber,
            'no_dokumen' => $no_dokumen,
            'keterangan' => $keterangan
        ]);

        return $stokDetail;
    }

    public function getListStokPerDokumen($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'stock_details2.bc_id' => 'stock_details2.bc_id',
            'stock_details2.no_aju' => 'stock_details2.no_aju',
            'barang_master.kode_barang' => 'barang_master.kode_barang',
            'barang_master.barang_name' => 'barang_master.barang_name',
            'divisis.divisi' => 'divisis.divisi',
            'warehouses.warehouse_name' => 'warehouses.warehouse_name',
            'stock.qty' => 'stock.qty'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'parent_barang.parent_type';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = '
            stock.id,
            stock.qty,
            parent_barang.parent_type,
            parent_barang.parent_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            divisis.divisi,
            warehouses.warehouse_name AS warehouse,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.satuan_1,
            barang_master_spesifikasi.satuan_2,
            barang_master_spesifikasi.satuan_3,
            barang_master_spesifikasi.konversi_satuan_2,
            barang_master_spesifikasi.konversi_satuan_3
        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id')
            ->join('divisis', 'divisis.id = stock.divisi_id')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if (
            $addCondition['parent_type'] ||
            $addCondition['parent_name'] ||
            $addCondition['divisi_id'] ||
            $addCondition['warehouse_id'] ||
            $addCondition['search']
        ) {
            $dataQry->groupStart();
        }

        if ($addCondition['parent_type']) {
            $dataQry->where('parent_barang.parent_type', $addCondition['parent_type']);
        }

        if ($addCondition['parent_name']) {
            $dataQry->where('parent_barang.id', $addCondition['parent_name']);
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('stock.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->where('stock.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['search']) {
            $dataQry->where('barang_master.barang_name', $addCondition['barang_name']);
            $dataQry->orWhere('barang_master.kode_barang', $addCondition['kode_barang']);
            $dataQry->orWhere('barang_master_spesifikasi.spesifikasi', $addCondition['spesifikasi']);
        }

        if (
            $addCondition['parent_type'] ||
            $addCondition['parent_name'] ||
            $addCondition['divisi_id'] ||
            $addCondition['warehouse_id'] ||
            $addCondition['search']
        ) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
