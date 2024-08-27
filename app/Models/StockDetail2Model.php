<?php

namespace App\Models;

use CodeIgniter\Model;

class StockDetail2Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_details2';
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

    public function getListStokPerDokumen($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'bc_id' => 'stock_details2.bc_id',
            'no_aju' => 'stock_details2.no_aju',
            'stok_total' => 'stok_total'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_details.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = '
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details.stock_date,
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,        
        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->where($condition)
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.stock_id')
            ->groupBy('stock_details2.no_aju')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['bc_id'] != "" || $addCondition['no_aju'] != "" || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
            if ($addCondition['bc_id'] || $addCondition['bc_id'] == 0 && $addCondition['bc_id'] != "") {
                $dataQry->where('stock_details2.bc_id', $addCondition['bc_id']);
            }

            if ($addCondition['no_aju']) {
                $dataQry->where('stock_details2.no_aju', $addCondition['no_aju']);
            }

            if ($addCondition['dateStart']) {
                $dataQry->where('stock_details.stock_date >=',  $addCondition['dateStart']);
            }
            if ($addCondition['dateEnd']) {
                $dataQry->where('stock_details.stock_date <=', $addCondition['dateEnd']);
            }

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

    public function getListStokPerSupplier($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_id' => 'stock_details2.supplier_id',
            'no_aju' => 'stock_details2.no_aju',
            'stok_total' => 'stok_total'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_details.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = '
        suppliers.name AS supplier_name,
        warehouses.warehouse_name,
        divisis.divisi,
        stock.kemasan_id,
        stock_details2.bc_id,
        stock_details2.supplier_id,
        stock_details2.stock_detail_id,
        stock_details2.no_aju,
        stock_details2.stock_dokumen,
        stock_details.stock_date,
        stock_details.sumber,
        (SUM(CASE WHEN stock_details.status = "In" 
        THEN stock_details2.qty ELSE 0 END) - 
        SUM(CASE WHEN stock_details.status = "Out" 
        THEN stock_details2.qty ELSE 0 END)) 
        AS stok_total,        
    ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id')
            ->join('divisis', 'divisis.id = stock.divisi_id')
            ->where($condition)
            ->groupBy('stock_details2.supplier_id')
            ->groupBy('stock_details2.stock_id')
            ->groupBy('stock_details2.no_aju')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['supplier_id'] != "" || $addCondition['no_aju'] != "" || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
            if ($addCondition['supplier_id'] || $addCondition['supplier_id'] == 0 && $addCondition['supplier_id'] != "") {
                $dataQry->where('stock_details2.supplier_id', $addCondition['supplier_id']);
            }

            if ($addCondition['no_aju']) {
                $dataQry->where('stock_details2.no_aju', $addCondition['no_aju']);
            }

            if ($addCondition['dateStart']) {
                $dataQry->where('stock_details.stock_date >=',  $addCondition['dateStart']);
            }
            if ($addCondition['dateEnd']) {
                $dataQry->where('stock_details.stock_date <=', $addCondition['dateEnd']);
            }

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

    public function insertStokDetail2(
        $bc_id,
        $stok_id,
        $stok_detail_id,
        $qty,
        $no_aju,
        $no_dokumen,
        $stock_dokumen = "-",
        $supplier_id = null,
        $harga_umum = null,
        $harga_harian = null,
        $harga_bulanan = null,
        $no_po = null
    ) {
        $stokDetail2 = $this->insert([
            'bc_id' => $bc_id,
            'stock_id' => $stok_id,
            'stock_detail_id' => $stok_detail_id,
            'qty' => $qty,
            'no_aju' => $no_aju,
            'no_dokumen' => $no_dokumen,
            'stock_dokumen' => $stock_dokumen,
            'supplier_id' => $supplier_id,
            'harga_umum' => $harga_umum,
            'harga_harian' => $harga_harian,
            'harga_bulanan' => $harga_bulanan,
            'no_po' => $no_po
        ]);

        return $stokDetail2;
    }

    public function getListStokLog($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'stock_details2.no_dokumen' => 'stock_details2.no_dokumen',
            'stock_details.no_dokumen' => 'stock_details.no_dokumen',
            'stock_details2.stock_dokumen' => 'stock_details2.stock_dokumen',
            'stock_details2.bc_id' => 'stock_details2.bc_id',
            'stock_details2.supplier_id' => 'stock_details2.supplier_id',
            'stock.barang1_id' => 'stock.barang1_id',
            'stock_details2.qty' => 'stock_details2.qty',
            'stock_details2.no_aju' => 'stock_details2.no_aju',
            'stock_details.tanggal' => 'stock_details.stock_date'

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_details2.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        // no_dokumen1 => LPB / NO_PRODUKSI
        // no_dokumen2 => PO

        $selectQry = '
            suppliers.name AS supplier_name,
            stock_details2.no_dokumen AS no_dokumen2, 
            stock_details.no_dokumen AS no_dokumen1,
            stock.barang1_id,
            stock.barang2_id,
            stock.kemasan_id,
            stock.divisi_id,
            stock.warehouse_id,
            stock_details.status,
            stock_details.sumber,
            stock_details2.stock_dokumen,
            stock_details2.qty AS stok_total,
            stock_details2.bc_id,
            stock_details2.no_aju,
            stock_details2.no_po,
            stock_details.stock_date,
            stock_details.keterangan,
            stock_details2.createdAt
        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('stock', 'stock.id = stock_details2.stock_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->join('kemasan', 'kemasan.id = stock.kemasan_id', 'left')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['bc_id'] != "" || $addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
        }

        if ($addCondition['bc_id'] || $addCondition['bc_id'] != "") {
            $dataQry->where('stock_details2.bc_id', $addCondition['bc_id']);
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('stock.divisi_id', $addCondition['divisi_id']);
        }


        if ($addCondition['search']) {
            $dataQry->like('stock_details2.no_dokumen', $addCondition['search'])
                ->orLike('stock_details.no_dokumen', $addCondition['search'])
                ->orLike('stock_details2.no_aju', $addCondition['search'])
                ->orLike('stock_details.sumber', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('kemasan.name', $addCondition['search']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->where('stock.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('stock_details.stock_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('stock_details.stock_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['bc_id'] != "" || $addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
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

    public function getTotalStockLog($condition)
    {
        $selectQry = '
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,   
    ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('stock', 'stock.id = stock_details.stock_id')
            ->where($condition)
            ->findAll();

        if (count($dataQry) == 0) {
            return 0;
        } else {
            return $dataQry[0]['stok_total'];
        }
    }

    public function getStockListWithBCDoc($stockID, $isAdjusment = false)
    {
        // JIKA $isAdjusment = true MAKA STOK < 0 MUNCUL
        // JIKA $isAdjusment = false MAKA STOK > 0 YANG MUNCUL
        $selectQry = '
            suppliers.name AS supplier_name,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            stock_details2.stock_dokumen,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_po,
            stock_details.no_dokumen AS no_dokumen_1,
            stock_details.stock_date,
            stock_details.sumber,
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,        
        ';

        if ($isAdjusment == true) {
            $dataQry = $this->asArray()
                ->select($selectQry)
                ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
                ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
                ->where('stock_details2.stock_id', $stockID)
                ->groupBy('stock_details2.stock_dokumen')
                ->groupBy('stock_details2.bc_id')
                ->groupBy('stock_details2.no_aju')
                ->orderBy('stock_details.createdAt', "ASC")
                ->findAll();
        } else {
            $dataQry = $this->asArray()
                ->select($selectQry)
                ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
                ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
                ->where('stock_details2.stock_id', $stockID)
                ->groupBy('stock_details2.stock_dokumen')
                ->groupBy('stock_details2.bc_id')
                ->groupBy('stock_details2.no_aju')
                ->having('stok_total >', 0)
                ->orderBy('stock_details.createdAt', "ASC")
                ->findAll();
        }

        return $dataQry;
    }

    public function getStockListWithBCDocNoGroup($stockID, $isAdjusment = false)
    {
        $selectQry = '
            stock.barang1_id,
            stock.barang2_id,
            suppliers.name AS supplier_name,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            stock_details2.stock_dokumen,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_po,
            stock_details.no_dokumen AS no_dokumen_1,
            stock_details.stock_date,
            stock_details.sumber,
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,  
            GROUP_CONCAT(stock_details2.no_aju) AS group_no_aju,     
        ';

        if ($isAdjusment == true) {
            $dataQry = $this->asArray()
                ->select($selectQry)
                ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
                ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
                ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
                ->where('stock_details2.stock_id', $stockID)
                ->groupBy('stock_details2.stock_id')
                ->orderBy('stock_details.createdAt', "ASC")
                ->findAll();
        } else {
            $dataQry = $this->asArray()
                ->select($selectQry)
                ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
                ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
                ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
                ->where('stock_details2.stock_id', $stockID)
                ->groupBy('stock_details2.stock_id')
                ->having('stok_total >', 0)
                ->orderBy('stock_details.createdAt', "ASC")
                ->findAll();
        }
        return $dataQry;
    }

    public function getStockListDetail($stockID, $bcID, $noAju, $stockDokumen = "-")
    {

        $selectQry = '
            suppliers.name AS supplier_name,
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
            ->where('stock_details2.stock_id', $stockID)
            ->where('stock_details2.bc_id', $bcID)
            ->where('stock_details2.no_aju', $noAju)
            ->where('stock_details2.stock_dokumen', $stockDokumen)
            ->groupBy('stock_details2.stock_dokumen')
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.no_aju')
            ->first();

        return $dataQry;
    }

    public function getStockListWithAddCondition($stockID, $condition)
    {
        // JIKA $isAdjusment = true MAKA STOK < 0 MUNCUL
        // JIKA $isAdjusment = false MAKA STOK > 0 YANG MUNCUL
        $selectQry = '
            suppliers.name AS supplier_name,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            stock_details2.stock_dokumen,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_po,
            stock_details.no_dokumen AS no_dokumen_1,
            stock_details.stock_date,
            stock_details.sumber,
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,        
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->where('stock_details2.stock_id', $stockID)
            ->where($condition)
            ->groupBy('stock_details2.stock_dokumen')
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.no_aju')
            ->having('stok_total >', 0)
            ->orderBy('stock_details.createdAt', "ASC")
            ->findAll();

        return $dataQry;
    }

    public function getAverageHargaStockList($stockID)
    {
        // JIKA $isAdjusment = true MAKA STOK < 0 MUNCUL
        // JIKA $isAdjusment = false MAKA STOK > 0 YANG MUNCUL
        $selectQry = '
        stock_details2.stock_id,
        AVG(stock_details2.harga_umum) AS avg_harga_umum,
        AVG(stock_details2.harga_harian) AS avg_harga_harian,
        AVG(stock_details2.harga_bulanan) AS avg_harga_bulanan
    ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->where('stock_details2.stock_id', $stockID)
            ->groupBy('stock_details2.stock_id')
            ->first();

        return $dataQry;
    }
}
