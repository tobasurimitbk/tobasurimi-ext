<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock';
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


    public function getStockListBarang($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'parent_barang.parent_type' => 'parent_barang.parent_type',
            'parent_barang.parent_name' => 'parent_barang.parent_name',
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
            $dataQry->groupStart()
                ->like("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->groupEnd()
                ->orWhere('barang_master.kode_barang', $addCondition['search']);
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

    public function getStockListKemasan($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'parent_barang.parent_type' => 'parent_barang.parent_type',
            'parent_barang.parent_name' => 'parent_barang.parent_name',
            'barang_master.kode_barang' => 'kemasan.kode',
            'barang_master.barang_name' => 'kemasan.name',
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
            kemasan.kode,
            kemasan.name,
            kemasan.satuan_id,
            divisis.divisi,
            warehouses.warehouse_name AS warehouse,
        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('kemasan', 'kemasan.id = stock.kemasan_id')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id')
            ->join('divisis', 'divisis.id = stock.divisi_id')
            ->join('parent_barang', 'parent_barang.id = kemasan.parent_type_id')
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
            $dataQry->where('parent_barang.parent_name', $addCondition['parent_name']);
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('stock.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->where('stock.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['search']) {
            $dataQry->where('kemasan.kode', $addCondition['kode']);
            $dataQry->orLike('kemasan.name', $addCondition['kode_barang']);
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

    public function getListMasterBarang($company_id, $type_barang)
    {
        $kemasanModel = new KemasanModel();
        $barangMasterModel = new BarangMasterModel();
        $result = [];

        if ($type_barang == "kemasan") {
            $condition = [
                'kemasan.deletedAt' => null,
                'kemasan.company_id' => $company_id,
            ];

            $qryKemasanRes = $kemasanModel
                ->select('kemasan.*, satuans.kode_satuan, satuans.nama_satuan')
                ->join('satuans', 'satuans.id = kemasan.satuan_id')
                ->where($condition)
                ->findAll();

            foreach ($qryKemasanRes as $k) {

                $result[] = [
                    'barang_id'  => 0,
                    'spesifikasi_id' => $k['id'], // spesifikasi_id = kemasan_id (jika kemasan)
                    'type_barang' => $type_barang,
                    'barang' => strtoupper($k['name']),
                    'spesifikasi_name' => strtoupper($k['name']),
                    'kode_barang' => $k['kode'],
                    'nama_satuan' => $k['nama_satuan'],
                ];
            }
        } else {
            $condition = [
                'barang_master.company_id' => $company_id,
                'barang_master.type_barang' => $type_barang,
                'barang_master.deletedAt' => null,
                'barang_master_spesifikasi.deletedAt' => null
            ];

            $selectQryBarang = "
                barang_master.id,
                barang_master.barang_name,
                barang_master.kode_barang,
                barang_master_spesifikasi.id AS spesifikasi_id,
                barang_master_spesifikasi.spesifikasi,
                satuans.nama_satuan
            ";

            $qryBarangMaster = $barangMasterModel
                ->select($selectQryBarang)
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
                ->where($condition)
                ->findAll();

            foreach ($qryBarangMaster as $b) {
                $result[] = [
                    'barang_id'  => $b['id'],
                    'spesifikasi_id' => $b['spesifikasi_id'], // spesifikasi_id = kemasan_id (jika kemasan)
                    'type_barang' => $type_barang,
                    'barang' => strtoupper($b['barang_name'] . "-" . $b['spesifikasi']),
                    'spesifikasi_name' => strtoupper($b['spesifikasi']),
                    'kode_barang' => $b['kode_barang'],
                    'nama_satuan' => $b['nama_satuan'],
                ];
            }
        }

        return $result;
    }

    public function isDefinedStockMaster(
        $company_id,
        $warehouse_id,
        $divisi_id,
        $type_barang,
        $barang_id,
        $spesifikasi_id
    ) {
        if ($type_barang == "kemasan") {
            $stockFirst = $this->asArray()
                ->where('company_id', $company_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('divisi_id', $divisi_id)
                ->where('kemasan_id', $spesifikasi_id)
                ->where('deletedAt', null)
                ->first();

            if ($stockFirst == null) {
                return false;
            } else {
                return true;
            }
        } else {
            $stockFirst = $this->asArray()
                ->where('company_id', $company_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('divisi_id', $divisi_id)
                ->where('barang1_id', $barang_id)
                ->where('barang2_id', $spesifikasi_id)
                ->where('deletedAt', null)
                ->first();

            if ($stockFirst == null) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function isDefinedStockSubDetail(
        $company_id,
        $warehouse_id,
        $divisi_id,
        $type_barang,
        $barang_id,
        $spesifikasi_id,
        $bc_id,
        $no_aju,
        $stock_id
    ) {
        if ($type_barang == "kemasan") {
            $stockFirst = $this->asArray()
                ->join('stock_details2', 'stock_details2.stock_id = stock.id')
                ->where('company_id', $company_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('divisi_id', $divisi_id)
                ->where('kemasan_id', $spesifikasi_id)
                ->where('bc_id', $bc_id)
                ->where('no_aju', $no_aju)
                ->where('stock_id', $stock_id)
                ->where('stock.deletedAt', null)
                ->where('stock_details2.deletedAt', null)
                ->first();

            if ($stockFirst == null) {
                return null;
            } else {
                return $stockFirst;
            }
        } else {
            $stockFirst = $this->asArray()
                ->join('stock_details2', 'stock_details2.stock_id = stock.id')
                ->where('company_id', $company_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('divisi_id', $divisi_id)
                ->where('barang1_id', $barang_id)
                ->where('barang2_id', $spesifikasi_id)
                ->where('bc_id', $bc_id)
                ->where('no_aju', $no_aju)
                ->where('stock_id', $stock_id)
                ->where('stock.deletedAt', null)
                ->where('stock_details2.deletedAt', null)
                ->first();

            if ($stockFirst == null) {
                return null;
            } else {
                return $stockFirst;
            }
        }
    }

    public function getStokMaster(
        $company_id,
        $warehouse_id,
        $divisi_id,
        $type_barang,
        $barang_id,
        $spesifikasi_id
    ) {
        if ($type_barang == "kemasan") {
            $stockFirst = $this->asArray()
                ->where('company_id', $company_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('divisi_id', $divisi_id)
                ->where('kemasan_id', $spesifikasi_id)
                ->where('deletedAt', null)
                ->first();

            return $stockFirst;
        } else {
            $stockFirst = $this->asArray()
                ->where('company_id', $company_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('divisi_id', $divisi_id)
                ->where('barang1_id', $barang_id)
                ->where('barang2_id', $spesifikasi_id)
                ->where('deletedAt', null)
                ->first();

            return $stockFirst;
        }
    }

    public function insertStok(
        $company_id,
        $warehouse_id,
        $divisi_id,
        $type_barang,
        $barang_id,
        $spesifikasi_id,
        $qtyTotal
    ) {
        if ($this->isDefinedStockMaster(
            $company_id,
            $warehouse_id,
            $divisi_id,
            $type_barang,
            $barang_id,
            $spesifikasi_id
        )) {
            // SUDAH DIDEFINISIKAN SEBELUMNYA
            if ($type_barang != "kemasan") {
                // BARANG
                $stok =  $this->asArray()
                    ->where('company_id', $company_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('divisi_id', $divisi_id)
                    ->where('barang1_id', $barang_id)
                    ->where('barang2_id', $spesifikasi_id)
                    ->where('deletedAt', null)
                    ->first();

                // var_dump($stok);

                // UPDATE QTY
                $this->update($stok['id'], [
                    'qty' => $stok['qty'] + $qtyTotal
                ]);
                // REPAIR STOK
                $detailStok = $this->detailStock($stok['id']);
                $this->update($stok['id'], [
                    'qty' => $detailStok['stok']['stokSekarang']
                ]);
            } else {
                // KEMASAN
                $stok =  $this->asArray()
                    ->where('company_id', $company_id)
                    ->where('warehouse_id', $warehouse_id)
                    ->where('divisi_id', $divisi_id)
                    ->where('barang1_id', $barang_id)
                    ->where('kemasan_id', $spesifikasi_id)
                    ->where('deletedAt', null)
                    ->first();

                // UPDATE QTY
                $this->update($stok['id'], [
                    'qty' => $stok['qty'] + $qtyTotal
                ]);
                // REPAIR STOK
                $detailStok = $this->detailStock($stok['id']);
                $this->update($stok['id'], [
                    'qty' => $detailStok['stok']['stokSekarang']
                ]);
            }

            return $stok['id'];
        } else {
            // BELUM ADA
            $stok = $this->insert([
                'company_id' => $company_id,
                'warehouse_id' => $warehouse_id,
                'divisi_id' => $divisi_id,
                'barang1_id' => $barang_id,
                'barang2_id' => $type_barang != "kemasan" ? $spesifikasi_id : 0,
                'kemasan_id' => $type_barang == "kemasan" ? $spesifikasi_id : 0,
                'qty' => $qtyTotal,
                'tipe_barang' => $type_barang
            ]);
            return $stok;
        }
    }

    public function detailStock($stock_id)
    {
        $stockDetailModel = new StockDetailModel();

        $stock = $this->find($stock_id);
        if ($stock['kemasan_id'] != 0) {
            // DETAIL BARANG STOCK KEMASAN
            $selectQry = "
                kemasan.kode AS kode,
                kemasan.name AS barang,
                satuans.kode_satuan,
                parent_barang.parent_type,
                parent_barang.parent_name,
                divisis.divisi,
                warehouses.warehouse_name AS warehouse,
                stock.qty
            
            ";
            $detailBarang = $this->select($selectQry)
                ->join('kemasan', 'kemasan.id = stock.kemasan_id')
                ->join('satuans', 'satuans.id = kemasan.satuan_id')
                ->join('parent_barang', 'parent_barang.id = kemasan.parent_type_id')
                ->join('divisis', 'divisis.id = stock.divisi_id')
                ->join('warehouses', 'warehouses.id = stock.warehouse_id')
                ->where('kemasan.id', $stock['kemasan_id'])
                ->first();
        } else {
            // DETAIL BARANG STOCK BARANG
            $selectQry = "
            CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            barang_master.kode_barang AS kode,
            satuans.kode_satuan,
            parent_barang.parent_type,
            parent_barang.parent_name,
            divisis.divisi,
            warehouses.warehouse_name AS warehouse,
            stock.qty
        ";

            $detailBarang = $this->select($selectQry)
                ->join('barang_master', 'barang_master.id = stock.barang1_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
                ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
                ->join('divisis', 'divisis.id = stock.divisi_id')
                ->join('warehouses', 'warehouses.id = stock.warehouse_id')
                ->where('barang_master_spesifikasi.id', $stock['barang2_id'])
                ->first();
        }

        $selectQry = '
        (SUM(CASE WHEN stock_details.status = "In" 
        THEN stock_details.qty ELSE 0 END) - 
        SUM(CASE WHEN stock_details.status = "Out" 
        THEN stock_details.qty ELSE 0 END)) 
        AS stokSekarang, 
        SUM(CASE WHEN stock_details.status = "In" 
        THEN stock_details.qty ELSE 0 END)
        AS stokMasuk,
        SUM(CASE WHEN stock_details.status = "Out" 
        THEN stock_details.qty ELSE 0 END)
        AS stokKeluar';

        $detailStock = $stockDetailModel->select($selectQry)
            ->where('deletedAt', null)
            ->where('stock_details.stock_id', $stock_id)
            ->groupBy('stock_details.stock_id')
            ->findAll();

        $selectQry = '
            SUM(stock_details.qty) AS qty
        ';

        $stokInisiasi = $stockDetailModel->select($selectQry)
            ->where('deletedAt', null)
            ->where('stock_details.sumber', "INISIASI")
            ->where('stock_details.stock_id', $stock_id)
            ->groupBy('stock_details.stock_id')
            ->findAll();

        $stokInisiasi = count($stokInisiasi) == 0 ? 0 : $stokInisiasi[0];
        return [
            'barang' => $detailBarang,
            'stok' => $detailStock[0],
            'stokInisiasi' => $stokInisiasi
        ];
    }

    public function getBarangAndStock($type_barang, $divisi_id, $warehouse_id)
    {
        if ($type_barang == "kemasan") {
            // LIST KEMASAN
            $selectQry = "
                stock.id AS stock_id,
                kemasan.id AS spesifikasi_id,
                UPPER(kemasan.name) AS barang,
                kemasan.kode AS kode_barang,
                satuans.kode_satuan
            ";

            $dataResult = $this->asArray()->select($selectQry)
                ->join('kemasan', 'kemasan.id = stock.kemasan_id')
                ->join('satuans', 'satuans.id = kemasan.satuan_id', 'left')
                ->where('stock.tipe_barang', $type_barang)
                ->where('stock.divisi_id', $divisi_id)
                ->where('stock.warehouse_id', $warehouse_id)
                ->where('stock.deletedAt', null)
                ->where('kemasan.deletedAt', null)
                ->orderBy('kemasan.kode', "ASC")
                ->findAll();
        } else {
            // LIST BARANG
            $selectQry = "
                stock.id AS stock_id,
                stock.barang2_id AS spesifikasi_id,
                CONCAT(UPPER(barang_master.barang_name), '-', UPPER(barang_master_spesifikasi.spesifikasi)) AS barang,
                barang_master.kode_barang,
                satuans.kode_satuan
            ";

            $dataResult = $this->asArray()->select($selectQry)
                ->join('barang_master', 'barang_master.id = stock.barang1_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->where('stock.tipe_barang', $type_barang)
                ->where('stock.divisi_id', $divisi_id)
                ->where('stock.warehouse_id', $warehouse_id)
                ->where('stock.deletedAt', null)
                ->where('barang_master_spesifikasi.deletedAt', null)
                ->where('barang_master.deletedAt', null)
                ->orderBy('barang_master.kode_barang', "ASC")
                ->findAll();
        }

        return $dataResult;
    }

    public function getBarangRebusAndStock($type_barang, $divisi_id, $warehouse_id)
    {
        $selectQry = "
        stock.id AS stock_id,
        stock.barang2_id AS spesifikasi_id,
        CONCAT(UPPER(barang_master.barang_name), '-', UPPER(barang_master_spesifikasi.spesifikasi)) AS barang,
        barang_master.kode_barang,
        satuans.kode_satuan
    ";

        $dataResult1 = $this->asArray()->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('stock.deletedAt', null)
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            ->where('stock.tipe_barang', $type_barang)
            ->where('stock.divisi_id', $divisi_id)
            ->where('stock.warehouse_id', $warehouse_id)
            ->like('barang_master.barang_name', '%' . "UDANG" . '%')
            ->orderBy('barang_master.kode_barang', "ASC")
            ->findAll();

        $dataResult2 = $this->asArray()->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('stock.deletedAt', null)
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            ->where('stock.tipe_barang', $type_barang)
            ->where('stock.divisi_id', $divisi_id)
            ->where('stock.warehouse_id', $warehouse_id)
            ->like('barang_master.barang_name', '%' . "KEPITING" . '%')
            ->orderBy('barang_master.kode_barang', "ASC")
            ->findAll();

        $dataResult = array_merge($dataResult1, $dataResult2);

        return $dataResult;
    }

    public function getBarangAndStockCondition($type_barang, $divisi_id, $warehouse_id, $addCondition = null)
    {
        if ($type_barang == "kemasan") {
            // LIST KEMASAN
            $selectQry = "
                stock.id AS stock_id,
                kemasan.id AS spesifikasi_id,
                UPPER(kemasan.name) AS barang,
                kemasan.kode AS kode_barang,
                satuans.kode_satuan
            ";

            $dataResult = $this->asArray()->select($selectQry)
                ->join('kemasan', 'kemasan.id = stock.kemasan_id')
                ->join('satuans', 'satuans.id = kemasan.satuan_id', 'left')
                ->where('stock.tipe_barang', $type_barang)
                ->where('stock.divisi_id', $divisi_id)
                ->where('stock.warehouse_id', $warehouse_id)
                ->where('stock.deletedAt', null)
                ->where('kemasan.deletedAt', null)
                ->where($addCondition)
                ->orderBy('kemasan.kode', "ASC")
                ->findAll();
        } else {
            // LIST BARANG
            $selectQry = "
                stock.id AS stock_id,
                stock.barang2_id AS spesifikasi_id,
                CONCAT(UPPER(barang_master.barang_name), '-', UPPER(barang_master_spesifikasi.spesifikasi)) AS barang,
                barang_master.kode_barang,
                satuans.kode_satuan,
                parent_barang.parent_name
            ";

            $dataResult = $this->asArray()->select($selectQry)
                ->join('barang_master', 'barang_master.id = stock.barang1_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
                ->where('stock.tipe_barang', $type_barang)
                ->where('stock.divisi_id', $divisi_id)
                ->where('stock.warehouse_id', $warehouse_id)
                ->where('stock.deletedAt', null)
                ->where('barang_master_spesifikasi.deletedAt', null)
                ->where('barang_master.deletedAt', null)
                ->where($addCondition)
                ->orderBy('barang_master.kode_barang', "ASC")
                ->findAll();
        }

        return $dataResult;
    }
}
