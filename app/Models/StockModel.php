<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

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
            barang_master.id as barang1_id,
            barang_master.kode_barang,
            barang_master.barang_name,
            divisis.divisi,
            warehouses.warehouse_name AS warehouse,
            barang_master_spesifikasi.id as barang2_id,
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
                ->select('kemasan.*, satuans.kode_satuan, satuans.nama_satuan, satuans.kode_satuan')
                ->join('satuans', 'satuans.id = kemasan.satuan_id', 'left')
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
                    'kode_satuan' => $k['kode_satuan']
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
                satuans.nama_satuan,
                satuans.kode_satuan
            ";

            $qryBarangMaster = $barangMasterModel
                ->select($selectQryBarang)
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
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
                    'kode_satuan' => $b['kode_satuan']
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

        if (count($detailStock) == 0) {
            $detailStock = [
                'stokSekarang' => 0,
                'stokMasuk' => 0,
                'stokKeluar' => 0
            ];
        } else {
            $detailStock = $detailStock[0];
        }

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
            'stok' => $detailStock,
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
            // ->like('barang_master.barang_name', '%' . "UDANG" . '%')
            ->orderBy('barang_master.kode_barang', "ASC")
            ->findAll();

        // $dataResult2 = $this->asArray()->select($selectQry)
        //     ->join('barang_master', 'barang_master.id = stock.barang1_id')
        //     ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
        //     ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
        //     ->where('stock.deletedAt', null)
        //     ->where('barang_master_spesifikasi.deletedAt', null)
        //     ->where('barang_master.deletedAt', null)
        //     ->where('stock.tipe_barang', $type_barang)
        //     ->where('stock.divisi_id', $divisi_id)
        //     ->where('stock.warehouse_id', $warehouse_id)
        //     // ->like('barang_master.barang_name', '%' . "KEPITING" . '%')
        //     ->orderBy('barang_master.kode_barang', "ASC")
        //     ->findAll();

        // $dataResult = array_merge($dataResult1, $dataResult2);

        return $dataResult1;
    }

    public function getBarangAndStockCondition($type_barang, $divisi_id, $warehouse_id, $addCondition = null)
    {
        if ($type_barang == "kemasan") {
            // LIST KEMASAN
            $selectQry = "
                stock.id AS stock_id,
                kemasan.id AS barang_id,
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
                stock.barang1_id AS barang_id,
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

    public function getBarangAndStockConditionWithoutWarehouse($type_barang, $divisi_id, $addCondition = null)
    {
        if ($type_barang == "kemasan") {
            // LIST KEMASAN
            $selectQry = "
                stock.id AS stock_id,
                kemasan.id AS barang_id,
                kemasan.id AS spesifikasi_id,
                UPPER(kemasan.name) AS barang,
                kemasan.kode AS kode_barang,
                satuans.kode_satuan
            ";

            $dataResult = $this->asArray()->select($selectQry)
                ->join('kemasan', 'kemasan.id = stock.kemasan_id')
                ->join('satuans', 'satuans.id = kemasan.satuan_id', 'left')
                ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
                ->where('stock.tipe_barang', $type_barang)
                ->where('stock.divisi_id', $divisi_id)
                // ->where('stock.warehouse_id', $warehouse_id)
                ->where('stock.deletedAt', null)
                ->where('kemasan.deletedAt', null)
                // ->where($addCondition)
                ->orderBy('kemasan.kode', "ASC")
                ->findAll();
        } else {
            // LIST BARANG
            $selectQry = "
                stock.id AS stock_id,
                stock.barang1_id AS barang_id,
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
                // ->where('stock.warehouse_id', $warehouse_id)
                ->where('stock.deletedAt', null)
                ->where('barang_master_spesifikasi.deletedAt', null)
                ->where('barang_master.deletedAt', null)
                // ->where($addCondition)
                ->orderBy('barang_master.kode_barang', "ASC")
                ->findAll();
        }

        return $dataResult;
    }

    public function getBarangAndStockConditionWithoutWarehouseAndType($divisi_id, $addCondition = null)
    {
        // if ($type_barang == "kemasan") {
        //     // LIST KEMASAN
        //     $selectQry = "
        //         stock.id AS stock_id,
        //         kemasan.id AS barang_id,
        //         kemasan.id AS spesifikasi_id,
        //         UPPER(kemasan.name) AS barang,
        //         kemasan.kode AS kode_barang,
        //         satuans.kode_satuan
        //     ";

        //     $dataResult = $this->asArray()->select($selectQry)
        //         ->join('kemasan', 'kemasan.id = stock.kemasan_id')
        //         ->join('satuans', 'satuans.id = kemasan.satuan_id', 'left')
        //         ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
        //         // ->where('stock.tipe_barang', $type_barang)
        //         ->where('stock.divisi_id', $divisi_id)
        //         // ->where('stock.warehouse_id', $warehouse_id)
        //         ->where('stock.deletedAt', null)
        //         ->where('kemasan.deletedAt', null)
        //         // ->where($addCondition)
        //         ->orderBy('kemasan.kode', "ASC")
        //         ->findAll();
        // } else {
        // LIST BARANG
        $selectQry = "
                stock.id AS stock_id,
                stock.barang1_id AS barang_id,
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
            // ->where('stock.tipe_barang', $type_barang)
            ->where('stock.divisi_id', $divisi_id)
            // ->where('stock.warehouse_id', $warehouse_id)
            ->where('stock.deletedAt', null)
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            // ->where($addCondition)
            ->orderBy('barang_master.kode_barang', "ASC")
            ->findAll();
        // }

        return $dataResult;
    }

    public function initStockBarang($companyId, $divisiId, $warehouseId, $tipeBarang, $barang2Id)
    {
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($barang2Id);

        $stok = $this->getStokMaster(
            $companyId,
            $warehouseId,
            $divisiId,
            $tipeBarang,
            $barangMasterSpesifikasi['barang_master_id'],
            $barang2Id
        );


        if ($stok == null) {
            $stok = $this->insertStok(
                $companyId,
                $warehouseId,
                $divisiId,
                $tipeBarang,
                $barangMasterSpesifikasi['barang_master_id'],
                $barang2Id,
                0
            );
            return $stok;
        } else {
            return $stok['id'];
        }
    }

    public function getStockForTutupBuku($condition)
    {
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
            ->findAll();

        return $dataQry;
    }


    public function updateNoAju($noAjuNew, $noAjuOld, $stockDateNew)
    {
        $stockDetailModel = new StockDetailModel();
        $prosesRebusDetailModel = new ProsesRebusDetailModel();
        $jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $materialRequestPenolongDetailModel = new MaterialRequestPenolongDetailsModel();
        $materialRequestDetailModel = new MaterialRequestDetailsModel();
        $mutasiDetailModel = new MutasiDetailModel();
        $mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $adjusmentDetailModel = new AdjusmentDetailModel();
        $rasioBarangJadiModel = new RasioBarangJadiModel();
        $rasioSaldoAkhirModel = new RasioSaldoAkhirModel();
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $salesOrderReturnDetailModel = new SalesOrderReturnDetailModel();
        $penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();
        $stockDetail2Model = new StockDetail2Model();
        $stockDetailModel = new StockDetailModel();

        $db = \Config\Database::connect();

        try {
            // Update di Proses Rebus Detail
            $prosesRebusDetailModel->where('no_aju_rebus', $noAjuOld)
                ->set('no_aju_rebus', $noAjuNew)
                ->update();

            // Update di Jasa Vendor Out Detail
            $jasaVendorOutDetailModel->where('no_aju_out', $noAjuOld)
                ->set('no_aju_out', $noAjuNew)
                ->update();

            // Update Jasa Vendor In Detail
            $jasaVendorInDetailModel->where('no_aju_in', $noAjuOld)
                ->set('no_aju_in', $noAjuNew)
                ->update();

            // Update Material Request Penolong & Kimia Detail
            $materialRequestPenolongDetailModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Update Material Request Detail
            $materialRequestDetailModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Update Mutasi Detail 
            $mutasiDetailModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Update Global Detail
            $mutasiGlobalDetailModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Update Penerimaan Mutasi Detail
            $penerimaanMutasiDetailModel->where('no_aju_asal', $noAjuOld)
                ->set('no_aju_asal', $noAjuNew)
                ->update();

            $penerimaanMutasiDetailModel->where('no_aju_mutasi', $noAjuOld)
                ->set('no_aju_mutasi', $noAjuNew)
                ->update();

            // Adjusment Detail 
            $adjusmentDetailModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Rasio Barang Jadi
            $rasioBarangJadiModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Rasio Saldo Akhir
            $rasioSaldoAkhirModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Sales Order Lain Detail
            $salesOrderLainDetailModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Sales Order Return Detail
            $salesOrderReturnDetailModel->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Penerimaan Mutasi Global Detail
            $penerimaanMutasiGlobalDetailModel->where('no_aju_mutasi', $noAjuOld)
                ->set('no_aju_mutasi', $noAjuNew)
                ->update();

            // Update Stock nya
            $stockDetail2Model->where('no_aju', $noAjuOld)
                ->set('no_aju', $noAjuNew)
                ->update();

            // Update Stock Date (Hanya Incoming LPB)
            $stockDetail2 = $stockDetailModel
                ->select('stock_details.id')
                ->join('stock_details2', 'stock_details2.stock_detail_id = stock_details.id', 'left')
                ->where('stock_details2.no_aju', $noAjuNew)
                ->where('stock_details.sumber', "LPB")
                ->findAll();

            $stockDetailsId = [];
            foreach ($stockDetail2 as $s) {
                array_push($stockDetailsId, $s['id']);
            }

            if (count($stockDetailsId) != 0) {
                $stockDetailModel->whereIn('id', $stockDetailsId)->set('stock_date', $stockDateNew)->update();
            }

            $db->transCommit();

            return true;
        } catch (Exception $e) {
            $db->transRollback();
            var_dump($e->getMessage(), $e->getLine(), $e->getFile(), $e->getTraceAsString());
            die;
            return false;
        }
    }

    public function checkStockLpbUsed($penerimaanBarangId)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $stockDetailModel = new StockDetailModel();
        $prosesRebusModel = new ProsesRebusModel();
        $jasaVendorOutModel = new JasaVendorOutModel();
        $materialRequestPenolongModel = new MaterialRequestsPenolongModel();
        $materialRequestModel = new MaterialRequestsModel();
        $mutasiModel = new MutasiModel();
        $mutasiGlobalModel = new MutasiGlobalModel();

        $message = null;
        $penerimaanBarang = $penerimaanBarangModel->where('id', $penerimaanBarangId)->first();
        if ($penerimaanBarang != null) {
            $stockDetail = $stockDetailModel->select('
                stock_details2.stock_id,
                stock_details2.bc_id,
                stock_details2.no_aju,
                stock_details2.stock_dokumen,
            ')
                ->join('stock_details2', 'stock_details2.stock_detail_id = stock_details.id', 'left')
                ->join('stock', 'stock_details2.stock_id = stock.id', 'left')
                ->where('stock.company_id', $penerimaanBarang['company_id'])
                ->where('stock.divisi_id', $penerimaanBarang['divisi_id'])
                ->where('stock.warehouse_id', $penerimaanBarang['warehouse_id'])
                ->where('stock_details.sumber', "LPB")
                ->where('stock_details.status', "In")
                ->where('stock_details.no_dokumen', $penerimaanBarang['no_penerimaan_barang'])
                ->findAll();


            foreach ($stockDetail as $s) {
                // CEK APAKAH STOK DIGUNAKAN PROSES REBUS
                $prosesRebusData = $prosesRebusModel->select('
                    proses_rebus.no_rebus
                ')
                    ->join('proses_rebus_detail', 'proses_rebus_detail.proses_rebus_id = proses_rebus.id', 'left')
                    ->where('proses_rebus.company_id', $penerimaanBarang['company_id'])
                    ->where('proses_rebus_detail.stock_rebus_id', $s['stock_id'])
                    ->where('proses_rebus_detail.bc_rebus_id', $s['bc_id'])
                    ->where('proses_rebus_detail.no_aju_rebus', $s['no_aju'])
                    ->where('proses_rebus_detail.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($prosesRebusData != null) {
                    $message = "Stok sudah digunakan di modul proses rebus dengan nomor rebus " . $prosesRebusData['no_rebus'];
                    break;
                }

                // CEK APAKAH STOK DIGUNAKAN DI JASA VENDOR
                $jasaVendorOutData = $jasaVendorOutModel->select('
                    jasa_vendor_out.no_surat_jalan
                ')
                    ->join('jasa_vendor_out_detail', 'jasa_vendor_out_detail.jasa_vendor_out_id = jasa_vendor_out.id', 'left')
                    ->where('jasa_vendor_out.company_id', $penerimaanBarang['company_id'])
                    ->where('jasa_vendor_out_detail.stock_out_id', $s['stock_id'])
                    ->where('jasa_vendor_out_detail.bc_out_id', $s['bc_id'])
                    ->where('jasa_vendor_out_detail.no_aju_out', $s['no_aju'])
                    ->where('jasa_vendor_out_detail.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($jasaVendorOutData != null) {
                    $message = "Stok sudah digunakan di modul jasa vendor out dengan nomor surat jalan " . $jasaVendorOutData['no_surat_jalan'];
                    break;
                }

                // CEK APAKAH SUDAH DIGUNAKAN DI MATERIAL REQUEST PENOLONG
                $materialRequestPenolongData = $materialRequestPenolongModel
                    ->select('material_requests_penolong.req_no')
                    ->join(
                        'material_request_penolong_details',
                        'material_request_penolong_details.material_request_id = material_requests_penolong.id',
                        'left'
                    )
                    ->where('material_requests_penolong.company_id', $penerimaanBarang['company_id'])
                    ->where('material_request_penolong_details.stock_id', $s['stock_id'])
                    ->where('material_request_penolong_details.bc_id', $s['bc_id'])
                    ->where('material_request_penolong_details.no_aju', $s['no_aju'])
                    ->where('material_request_penolong_details.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($materialRequestPenolongData != null) {
                    $message = "Stok sudah digunakan di material request bahan penolong dengan nomor " . $materialRequestPenolongData['req_no'];
                }

                // CEK APAKAH SUDAH DIGUNAKAN DI MATERIAL REQUEST BIASA
                $materialRequestData = $materialRequestModel->select('
                    material_requests.req_no
                ')
                    ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'left')
                    ->where('material_requests.company_id', $penerimaanBarang['company_id'])
                    ->where('material_request_details.stock_id', $s['stock_id'])
                    ->where('material_request_details.bc_id', $s['bc_id'])
                    ->where('material_request_details.no_aju', $s['no_aju'])
                    ->where('material_request_details.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($materialRequestData != null) {
                    $message = "Stok sudah digunakan di material request dengan nomor " . $materialRequestData['req_no'];
                }


                // CEK APAKAH STOK SUDAH DIGUNAKAN DI MUTASI PPBKB
                $mutasiData = $mutasiModel->select('
                    mutasi.no_mutasi
                ')
                    ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
                    ->where('mutasi.company_id', $penerimaanBarang['company_id'])
                    ->where('mutasi_detail.stock_id', $s['stock_id'])
                    ->where('mutasi_detail.bc_id', $s['bc_id'])
                    ->where('mutasi_detail.no_aju', $s['no_aju'])
                    ->where('mutasi_detail.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($mutasiData != null) {
                    $message = "Stok sudah digunakan di modul mutasi PPBKB " . $mutasiData['no_mutasi'];
                }



                // CEK APAKAH STOK SUDAH DIGUNAKAN DI MUTASI GLOBAL
                $mutasiGlobalData = $mutasiGlobalModel->select('
                    mutasi_global.no_mutasi
                ')
                    ->join('mutasi_global_detail', 'mutasi_global_detail.mutasi_global_id = mutasi_global.id', 'left')
                    ->where('mutasi_global.company_asal_id', $penerimaanBarang['company_id'])
                    ->where('mutasi_global_detail.stock_id', $s['stock_id'])
                    ->where('mutasi_global_detail.bc_id', $s['bc_id'])
                    ->where('mutasi_global_detail.no_aju', $s['no_aju'])
                    ->where('mutasi_global_detail.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($mutasiGlobalData != null) {
                    $message = "Stok sudah digunakan di modul mutasi BC 2.7 " . $mutasiGlobalData['no_mutasi'];
                }
            }
        }

        return $message;
    }

    public function unPostingStockLPB($penerimaanBarangId)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $stockModel = new StockModel();
        $stockDetailModel = new StockDetailModel();
        $stockDetail2Model = new StockDetail2Model();

        $penerimaanBarang = $penerimaanBarangModel->where('id', $penerimaanBarangId)->first();
        if ($penerimaanBarang != null) {
            $stockLPBDetail = $stockDetailModel->select('
                stock_details2.stock_id,
                stock_details2.bc_id,
                stock_details2.no_aju,
                stock_details2.stock_dokumen,
                stock_details2.qty,
                stock_details2.no_po,
                stock_details2.harga_umum,
                stock_details2.harga_harian,
                stock_details2.harga_bulanan,
                stock_details.no_dokumen,
                stock.tipe_barang,
                stock.barang1_id,
                stock.barang2_id,
                stock.kemasan_id
            ')
                ->distinct()
                ->join('stock_details2', 'stock_details2.stock_detail_id = stock_details.id', 'left')
                ->join('stock', 'stock_details2.stock_id = stock.id', 'left')
                ->where('stock.company_id', $penerimaanBarang['company_id'])
                ->where('stock.divisi_id', $penerimaanBarang['divisi_id'])
                ->where('stock.warehouse_id', $penerimaanBarang['warehouse_id'])
                ->where('stock_details.sumber', "LPB")
                ->where('stock_details.status', "In")
                ->where('stock_details.no_dokumen', $penerimaanBarang['no_penerimaan_barang'])
                ->findAll();

            foreach ($stockLPBDetail as $s) {

                if ($s['tipe_barang'] != "kemasan") {
                    // INI BARANG
                    $stok = $stockModel->insertStok(
                        $penerimaanBarang['company_id'],
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        $s['tipe_barang'],
                        $s['barang1_id'],
                        $s['barang2_id'],
                        $s['qty']
                    );
                } else {
                    // INI KEMASAN
                    $stok = $stockModel->insertStok(
                        $penerimaanBarang['company_id'],
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        $s['tipe_barang'],
                        $s['barang1_id'],
                        $s['kemasan_id'],
                        $s['qty']
                    );
                }

                // DETAIL
                $stokDetail = $stockDetailModel->insertStokDetail(
                    $stok,
                    $s['qty'],
                    "Out",
                    date('Y-m-d'),
                    session()->get("login")->user_id,
                    "LPB",
                    $penerimaanBarang['no_penerimaan_barang'],
                    "UNPOSTING STOK LPB"
                );

                // SUB DETAIL
                $stockDetail2Model->insertStokDetail2(
                    $s['bc_id'],
                    $s['stock_id'],
                    $stokDetail,
                    $s['qty'],
                    $s['no_aju'],
                    $s['stock_dokumen'],
                    $s['stock_dokumen'],
                    $penerimaanBarang['supplier_id'],
                    $s['harga_umum'],
                    $s['harga_harian'],
                    $s['harga_bulanan'],
                    $s['no_po']
                );
            }
        }
    }

    public function unPostingStockJasaVendorOut($jasaVendorOutId)
    {
        try {
            $jasaVendorOutModel = new JasaVendorOutModel();
            $jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
            $stockModel = new StockModel();
            $stockDetailModel = new StockDetailModel();
            $stockDetail2Model = new StockDetail2Model();

            // BARANG OUT KE VENDOR
            // Insert To Inventori (+)
            $jasaVendorOut = $jasaVendorOutModel->find($jasaVendorOutId);
            $jasaVendorOutDetail = $jasaVendorOutDetailModel->where('jasa_vendor_out_id', $jasaVendorOutId)->where('deletedAt', null)->findAll();


            foreach ($jasaVendorOutDetail as $j) {
                $stockDetail = $stockDetail2Model->find($j['stock_out_id']);
                $stock = $stockModel->find($stockDetail['stock_id']);

                $qty = $j['qty'];

                if ($stock['tipe_barang'] == "kemasan") {
                    $barang2_id = $stock['kemasan_id'];
                } else {
                    $barang2_id = $stock['barang2_id'];
                }

                // BARANG LAMA
                $stockOldDetail = $stockDetail2Model->getStockListDetail(
                    $j['stock_out_id'],
                    $j['bc_out_id'],
                    $j['no_aju_out'],
                    $j['stock_dokumen']
                );

                $stok = $stockModel->insertStok(
                    $jasaVendorOut['company_id'],
                    $jasaVendorOut['warehouse_id'],
                    $jasaVendorOut['divisi_id'],
                    $stock['tipe_barang'],
                    $stock['barang1_id'],
                    $barang2_id,
                    $qty
                );

                // DETAIL
                $stokDetail = $stockDetailModel->insertStokDetail(
                    $stok,
                    $qty,
                    "In",
                    $jasaVendorOut['tanggal'],
                    session()->get("login")->user_id,
                    "JASA VENDOR",
                    "-",
                    "UNPOSTING STOK BARANG KELUAR"
                );

                // SUB DETAIL
                $stockDetail2Model->insertStokDetail2(
                    $j['bc_out_id'],
                    $j['stock_out_id'],
                    $stokDetail,
                    $qty,
                    $j['no_aju_out'],
                    $jasaVendorOut['no_surat_jalan'],
                    $j['stock_dokumen'],
                    $stockOldDetail['supplier_id'],
                    $stockOldDetail['harga_umum'],
                    $stockOldDetail['harga_harian'],
                    $stockOldDetail['harga_bulanan'],
                    $stockOldDetail['no_po']
                );
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function unPostingStockJasaVendorIn($jasaVendorInId)
    {
        $jasaVendorInModel = new JasaVendorInModel();
        $jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $stockModel = new StockModel();
        $jasaVendorOutModel = new JasaVendorOutModel();
        $jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $stockDetail2Model = new StockDetail2Model();
        $stockDetailModel = new StockDetailModel();

        $jasaVendorIn = $jasaVendorInModel->find($jasaVendorInId);
        $jasaVendorInDetail = $jasaVendorInDetailModel->where('jasa_vendor_in_id', $jasaVendorInId)->findAll();

        foreach ($jasaVendorInDetail as $j) {
            $stock = $stockModel->find($j['stock_in_id']);
            $qty = $j['qty_bersih'];

            $stok = $stockModel->insertStok(
                $jasaVendorIn['company_id'],
                $jasaVendorIn['warehouse_id'],
                $jasaVendorIn['divisi_id'],
                "bahan_baku",
                $stock['barang1_id'],
                $stock['barang2_id'],
                $qty
            );

            $jasaVendorOut = $jasaVendorOutModel->find($j['jasa_vendor_out_id']);
            $jasaVendorOutDetail = $jasaVendorOutDetailModel->find($j['jasa_vendor_out_detail_id']);

            $stockOldDetail = $stockDetail2Model->getStockListDetail(
                $jasaVendorOutDetail['stock_out_id'],
                $jasaVendorOutDetail['bc_out_id'],
                $jasaVendorOutDetail['no_aju_out'],
                $jasaVendorOutDetail['stock_dokumen']
            );

            // DETAIL
            $stokDetail = $stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                "Out",
                date('Y-m-d'),
                session()->get("login")->user_id,
                "JASA VENDOR",
                $jasaVendorOut['no_surat_jalan'],
                $jasaVendorIn['keterangan']
            );

            // SUB DETAIL
            $stockDetail2Model->insertStokDetail2(
                $j['bc_in_id'],
                $j['stock_in_id'],
                $stokDetail,
                $qty,
                $j['no_aju_in'],
                $jasaVendorIn['no_penerimaan_surat_jalan'],
                $jasaVendorIn['no_penerimaan_surat_jalan'] . " (" . $stockOldDetail['no_po'] . ")",
                $stockOldDetail['supplier_id'],
                $stockOldDetail['harga_umum'],
                $stockOldDetail['harga_harian'],
                $stockOldDetail['harga_bulanan'],
                $stockOldDetail['no_po']
            );
        }
    }
    public function checkStockJasaVendorInUsed($jasaVendorInId)
    {
        $jasaVendorInModel = new JasaVendorInModel();
        $stockDetailModel = new StockDetailModel();
        $prosesRebusModel = new ProsesRebusModel();
        $jasaVendorOutModel = new JasaVendorOutModel();
        $materialRequestPenolongModel = new MaterialRequestsPenolongModel();
        $materialRequestModel = new MaterialRequestsModel();
        $mutasiModel = new MutasiModel();
        $mutasiGlobalModel = new MutasiGlobalModel();

        $message = null;
        $jasaVendorIn = $jasaVendorInModel->where('id', $jasaVendorInId)->first();
        if ($jasaVendorIn != null) {
            $stockDetail = $stockDetailModel->select('
                stock_details2.stock_id,
                stock_details2.bc_id,
                stock_details2.no_aju,
                stock_details2.stock_dokumen,
            ')
                ->join('stock_details2', 'stock_details2.stock_detail_id = stock_details.id', 'left')
                ->join('stock', 'stock_details2.stock_id = stock.id', 'left')
                ->where('stock.company_id', $jasaVendorIn['company_id'])
                ->where('stock.divisi_id', $jasaVendorIn['divisi_id'])
                ->where('stock.warehouse_id', $jasaVendorIn['warehouse_id'])
                ->where('stock_details.sumber', "JASA VENDOR")
                ->where('stock_details.status', "In")
                ->where('stock_details2.no_dokumen', $jasaVendorIn['no_penerimaan_surat_jalan'])
                ->findAll();


            foreach ($stockDetail as $s) {
                // CEK APAKAH STOK DIGUNAKAN PROSES REBUS
                $prosesRebusData = $prosesRebusModel->select('
                    proses_rebus.no_rebus
                ')
                    ->join('proses_rebus_detail', 'proses_rebus_detail.proses_rebus_id = proses_rebus.id', 'left')
                    ->where('proses_rebus.company_id', $jasaVendorIn['company_id'])
                    ->where('proses_rebus_detail.stock_rebus_id', $s['stock_id'])
                    ->where('proses_rebus_detail.bc_rebus_id', $s['bc_id'])
                    ->where('proses_rebus_detail.no_aju_rebus', $s['no_aju'])
                    ->where('proses_rebus_detail.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($prosesRebusData != null) {
                    $message = "Stok sudah digunakan di modul proses rebus dengan nomor rebus " . $prosesRebusData['no_rebus'];
                    break;
                }

                // CEK APAKAH STOK DIGUNAKAN DI JASA VENDOR
                $jasaVendorOutData = $jasaVendorOutModel->select('
                    jasa_vendor_out.no_surat_jalan
                ')
                    ->join('jasa_vendor_out_detail', 'jasa_vendor_out_detail.jasa_vendor_out_id = jasa_vendor_out.id', 'left')
                    ->where('jasa_vendor_out.company_id', $jasaVendorIn['company_id'])
                    ->where('jasa_vendor_out_detail.stock_out_id', $s['stock_id'])
                    ->where('jasa_vendor_out_detail.bc_out_id', $s['bc_id'])
                    ->where('jasa_vendor_out_detail.no_aju_out', $s['no_aju'])
                    ->where('jasa_vendor_out_detail.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($jasaVendorOutData != null) {
                    $message = "Stok sudah digunakan di modul jasa vendor out dengan nomor surat jalan " . $jasaVendorOutData['no_surat_jalan'];
                    break;
                }

                // CEK APAKAH SUDAH DIGUNAKAN DI MATERIAL REQUEST PENOLONG
                $materialRequestPenolongData = $materialRequestPenolongModel
                    ->select('material_requests_penolong.req_no')
                    ->join(
                        'material_request_penolong_details',
                        'material_request_penolong_details.material_request_id = material_requests_penolong.id',
                        'left'
                    )
                    ->where('material_requests_penolong.company_id', $jasaVendorIn['company_id'])
                    ->where('material_request_penolong_details.stock_id', $s['stock_id'])
                    ->where('material_request_penolong_details.bc_id', $s['bc_id'])
                    ->where('material_request_penolong_details.no_aju', $s['no_aju'])
                    ->where('material_request_penolong_details.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($materialRequestPenolongData != null) {
                    $message = "Stok sudah digunakan di material request bahan penolong dengan nomor " . $materialRequestPenolongData['req_no'];
                }

                // CEK APAKAH SUDAH DIGUNAKAN DI MATERIAL REQUEST BIASA
                $materialRequestData = $materialRequestModel->select('
                    material_requests.req_no
                ')
                    ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'left')
                    ->where('material_requests.company_id', $jasaVendorIn['company_id'])
                    ->where('material_request_details.stock_id', $s['stock_id'])
                    ->where('material_request_details.bc_id', $s['bc_id'])
                    ->where('material_request_details.no_aju', $s['no_aju'])
                    ->where('material_request_details.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($materialRequestData != null) {
                    $message = "Stok sudah digunakan di material request dengan nomor " . $materialRequestData['req_no'];
                }


                // CEK APAKAH STOK SUDAH DIGUNAKAN DI MUTASI PPBKB
                $mutasiData = $mutasiModel->select('
                    mutasi.no_mutasi
                ')
                    ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
                    ->where('mutasi.company_id', $jasaVendorIn['company_id'])
                    ->where('mutasi_detail.stock_id', $s['stock_id'])
                    ->where('mutasi_detail.bc_id', $s['bc_id'])
                    ->where('mutasi_detail.no_aju', $s['no_aju'])
                    ->where('mutasi_detail.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($mutasiData != null) {
                    $message = "Stok sudah digunakan di modul mutasi PPBKB " . $mutasiData['no_mutasi'];
                }



                // CEK APAKAH STOK SUDAH DIGUNAKAN DI MUTASI GLOBAL
                $mutasiGlobalData = $mutasiGlobalModel->select('
                    mutasi_global.no_mutasi
                ')
                    ->join('mutasi_global_detail', 'mutasi_global_detail.mutasi_global_id = mutasi_global.id', 'left')
                    ->where('mutasi_global.company_asal_id', $jasaVendorIn['company_id'])
                    ->where('mutasi_global_detail.stock_id', $s['stock_id'])
                    ->where('mutasi_global_detail.bc_id', $s['bc_id'])
                    ->where('mutasi_global_detail.no_aju', $s['no_aju'])
                    ->where('mutasi_global_detail.stock_dokumen', $s['stock_dokumen'])
                    ->first();

                if ($mutasiGlobalData != null) {
                    $message = "Stok sudah digunakan di modul mutasi BC 2.7 " . $mutasiGlobalData['no_mutasi'];
                }
            }
        }

        return $message;
    }
}
