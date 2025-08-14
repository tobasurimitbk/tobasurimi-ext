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
            // ->groupBy('stock_details2.supplier_id')
            ->groupBy('stock_details2.stock_dokumen')
            ->groupBy('stock_details2.bc_id')
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
            'stock_details.tanggal' => 'stock_details.stock_date',
            'stock_details.keterangan' => 'stock_details.keterangan'

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

        if (isset($addCondition['where_in_sumber'])) {
            $dataQry->whereIn('sumber', $addCondition['where_in_sumber']);
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

    public function getStockListWithBCDoc($stockID, $isAdjusment = false, $supplierID = null)
    {
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
        AS stok_total
    ';

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->where('stock_details2.stock_id', $stockID)
            ->groupBy('stock_details2.stock_dokumen')
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.no_aju');

        // Tambahkan filter supplier jika diberikan
        if ($supplierID !== null) {
            $builder->where('stock_details2.supplier_id', $supplierID);
        }

        // Filter stok > 0 jika bukan untuk adjustment
        if ($isAdjusment === false) {
            $builder->having('stok_total >', 0);
        }

        $builder->orderBy('stock_details.createdAt', "ASC");

        return $builder->findAll();
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
            SUM(stock_details2.harga_umum) AS price1,
            SUM(stock_details2.harga_harian) AS price2,
            SUM(stock_details2.harga_bulanan) AS price3,
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
            CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
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


    public function getStockListDetailNew($stockID)
    {
        $selectQry = '
            CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
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
            ->where('stock_details2.id', $stockID)
            ->groupBy('stock_details2.stock_dokumen')
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.no_aju')
            ->first();

        return $dataQry;
    }

    public function getStockListWithAddCondition($stockID, $condition)
    {
        return $this->asArray()
            ->select('
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang,
                suppliers.name AS supplier_name,
                stock_details2.id,
                stock_details2.bc_id,
                stock_details2.no_aju,
                stock_details2.stock_id,
                stock_details2.stock_dokumen,
                stock_details2.supplier_id,
                stock_details.stock_date,
                stock_details.sumber,
                satuans.kode_satuan,
                SUM(CASE WHEN stock_details.status = "In" THEN stock_details2.qty ELSE 0 END) - 
                SUM(CASE WHEN stock_details.status = "Out" THEN stock_details2.qty ELSE 0 END) AS stok_total
            ')
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left') ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('stock.id', $stockID)
            ->where($condition)
            ->groupBy('stock_details2.stock_dokumen, stock_details2.bc_id, stock_details2.no_aju')
            ->having('stok_total >', 0)
            ->orderBy('stock_details.createdAt', 'ASC')
            ->findAll();
    }


    public function getStockListWithAddConditionNew($stockID, $spesifikasiId, $condition)
    {
        $spesifikasiFilter = '';
        if (!empty($spesifikasiId)) {
            $spesifikasiFilter = 'WHERE penerimaan_barang_detail.spesifikasi_id = ' . (int) $spesifikasiId;
        }

        $select = "
            MIN(suppliers.name) AS supplier_name,
            MIN(stock_details2.id) AS id,
            stock_details2.bc_id,
            stock_details2.no_aju,
            MIN(stock_details2.stock_id) AS stock_id,
            stock_details2.stock_dokumen,
            MIN(stock_details2.supplier_id) AS supplier_id,
            MIN(stock_details.stock_date) AS stock_date,
            MIN(stock_details.sumber) AS sumber,
            SUM(CASE WHEN stock_details.status = 'In' THEN stock_details2.qty ELSE 0 END)
            - SUM(CASE WHEN stock_details.status = 'Out' THEN stock_details2.qty ELSE 0 END) AS stok_total,
            COALESCE(total_penerimaan_subquery.total_penerimaan, 0) AS total_penerimaan
        ";

        return $this->asArray()
            ->select($select, false) // <-- penting: jangan di-escape
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('(
                SELECT 
                    penerimaan_barang.no_penerimaan_barang, 
                    SUM(penerimaan_barang_detail.qty) AS total_penerimaan
                FROM penerimaan_barang
                LEFT JOIN penerimaan_barang_detail 
                    ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id
                ' . $spesifikasiFilter . '
                GROUP BY penerimaan_barang.no_penerimaan_barang
            ) AS total_penerimaan_subquery', 
            'total_penerimaan_subquery.no_penerimaan_barang = stock_details.no_dokumen', 
            'left')
            ->where('stock_details2.stock_id', $stockID)
            ->where($condition)
            ->groupBy('stock_details2.stock_dokumen, stock_details2.bc_id, stock_details2.no_aju')
            ->having('stok_total >', 0)
            ->orderBy('MIN(stock_details.createdAt)', 'ASC', false) // jangan di-escape
            ->findAll();
    }


    public function getStockListJasaVendorOut($condition)
    {
        $builder = $this->asArray()
            ->select('
                suppliers.name AS supplier_name,
                stock_details2.id,
                stock_details2.bc_id,
                stock_details2.no_aju,
                stock_details2.stock_id,
                stock_details2.stock_dokumen,
                stock_details2.supplier_id,
                stock_details.stock_date,
                stock_details.sumber,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                satuans.kode_satuan,
                SUM(CASE WHEN stock_details.status = "In" THEN stock_details2.qty ELSE 0 END) - 
                SUM(CASE WHEN stock_details.status = "Out" THEN stock_details2.qty ELSE 0 END) AS stok_total
            ')
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left');

        // Pecah kondisi
        foreach ($condition as $field => $value) {
            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }
        
        return $builder
            ->groupBy('stock_details2.id, stock_details2.stock_dokumen, stock_details2.bc_id, stock_details2.no_aju')
            ->having('stok_total >', 0)
            ->orderBy('stock_details.createdAt', 'ASC')
            ->findAll();
    }


    public function getStockListJasaVendorOutNew($condition)
    {
        $builder = $this->asArray()
            ->select('
                stock_details2.id AS id,
                suppliers.name AS supplier_name,
                stock_details2.stock_id,
                stock_details2.stock_dokumen,
                stock_details2.bc_id,
                stock_details2.no_aju,
                stock_details2.supplier_id,
                stock_details.stock_date,
                stock_details.sumber,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                satuans.kode_satuan,
                SUM(CASE WHEN stock_details.status = "In" THEN stock_details2.qty ELSE 0 END)
                - SUM(CASE WHEN stock_details.status = "Out" THEN stock_details2.qty ELSE 0 END) AS stok_total
            ')
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
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
                stock_details2.stock_id,
                stock_details2.stock_dokumen,
                stock_details2.bc_id,
                stock_details2.no_aju,
                stock_details2.supplier_id,
                suppliers.name,
                stock_details.stock_date,
                stock_details.sumber,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                satuans.kode_satuan
            ')
            ->having('stok_total >', 0)
            ->orderBy('stock_details.createdAt', 'ASC')
            ->findAll();
    }

    public function getStockListMaterialRequestFromJasaVendor($condition)
    {
        $selectQry = '
            stock_details2.stock_id,
            suppliers.name AS supplier_name,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.no_aju,
            stock_details2.stock_dokumen,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_po,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan,
            jasa_vendor_in.tanggal as stock_date,
            vendors.name as nama_vendor,
            (
                SELECT value FROM metadata WHERE id = stock_details2.bc_id
            ) AS bc_type,
            (
                SELECT no_daftar FROM bc_23 
                JOIN bc_purchase_order ON bc_purchase_order.id = bc_23.bc_purchase_order_id
                WHERE bc_23.no_aju = stock_details2.no_aju AND stock_details2.bc_id = 48
            ) AS no_daftar_bc23,
            (
                SELECT no_daftar FROM bc_27
                WHERE bc_27.no_aju = stock_details2.no_aju AND stock_details2.bc_id = 52
            ) AS no_daftar_bc27,
            (
                SELECT no_daftar FROM bc_40 
                JOIN bc_purchase_order ON bc_purchase_order.id = bc_40.bc_purchase_order_id
                WHERE bc_40.no_aju = stock_details2.no_aju AND stock_details2.bc_id = 53
            ) AS no_daftar_bc40,
            (
                SELECT no_daftar FROM ppbkb
                WHERE ppbkb.no_ppbkb = stock_details2.no_aju AND stock_details2.bc_id = 1426
            ) AS no_daftar_ppbkb,
            (
                SELECT 
                    SUM(CASE WHEN sd.status = "In" THEN sd2.qty ELSE 0 END) -
                    SUM(CASE WHEN sd.status = "Out" THEN sd2.qty ELSE 0 END)
                FROM stock_details2 sd2
                LEFT JOIN stock_details sd ON sd.id = sd2.stock_detail_id
                WHERE 
                    sd2.stock_id = stock_details2.stock_id AND
                    sd2.bc_id = stock_details2.bc_id AND
                    sd2.no_aju = stock_details2.no_aju AND
                    sd2.stock_dokumen = stock_details2.stock_dokumen
            ) AS stok_total
        ';


        $dataQry = $this->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('jasa_vendor_in', 'jasa_vendor_in.no_penerimaan_surat_jalan = stock_details2.no_dokumen', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where($condition)
            ->groupBy([
                'stock_details2.stock_dokumen',
                'stock_details2.bc_id',
                'stock_details2.no_aju',
                // 'stock_details2.id',
                'stock_details2.stock_id'
            ])
            ->orderBy('stock_details2.createdAt', 'ASC')
            ->findAll();

        return $dataQry;
    }

    public function getStockListMaterialRequestFromLpb($condition)
    {
        $selectQry = '
            stock_details2.stock_id,
            suppliers.name AS supplier_name,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.no_aju,
            stock_details2.stock_dokumen,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_po,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan,
            stock_details.sumber,
            rm_purchase_orders.po_date AS stock_date,
            rm_purchase_orders.po_no,
            (
                SELECT value FROM metadata WHERE id = stock_details2.bc_id
            ) AS bc_type,
            (
                SELECT no_daftar FROM bc_23 
                JOIN bc_purchase_order ON bc_purchase_order.id = bc_23.bc_purchase_order_id
                WHERE bc_23.no_aju = stock_details2.no_aju AND stock_details2.bc_id = 48
            ) AS no_daftar_bc23,
            (
                SELECT no_daftar FROM bc_27
                WHERE bc_27.no_aju = stock_details2.no_aju AND stock_details2.bc_id = 52
            ) AS no_daftar_bc27,
            (
                SELECT no_daftar FROM bc_40 
                JOIN bc_purchase_order ON bc_purchase_order.id = bc_40.bc_purchase_order_id
                WHERE bc_40.no_aju = stock_details2.no_aju AND stock_details2.bc_id = 53
            ) AS no_daftar_bc40,
            (
                SELECT no_daftar FROM ppbkb
                WHERE ppbkb.no_ppbkb = stock_details2.no_aju AND stock_details2.bc_id = 1426
            ) AS no_daftar_ppbkb,
            (
                SELECT 
                    SUM(CASE WHEN sd.status = "In" THEN sd2.qty ELSE 0 END) -
                    SUM(CASE WHEN sd.status = "Out" THEN sd2.qty ELSE 0 END)
                FROM stock_details2 sd2
                LEFT JOIN stock_details sd ON sd.id = sd2.stock_detail_id
                WHERE 
                    sd2.stock_id = stock_details2.stock_id AND
                    sd2.bc_id = stock_details2.bc_id AND
                    sd2.no_aju = stock_details2.no_aju AND
                    sd2.stock_dokumen = stock_details2.stock_dokumen
            ) AS stok_total
        ';


        $dataQry = $this->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.po_no = stock_details2.no_po', 'left')
            ->where($condition)
            ->groupBy([
                'stock_details2.stock_dokumen',
                'stock_details2.bc_id',
                'stock_details2.no_aju',
                // 'stock_details2.id',
                'stock_details2.stock_id'
            ])
            ->orderBy('stock_details2.createdAt', 'ASC')
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

    // LAPORAN BEA CUKAI
    public function getListStokMasukKeluar($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'stock_details2.createdAt' => 'stock_details2.createdAt',
            'stock_details2.bc_id' => 'stock_details2.bc_id',
            'stock_details.sumber' => 'stock_details.sumber',
            'stock_details2.no_po' => 'stock_details2.no_po',
            'stock.divisi_id' => 'stock.divisi_id',
            'stock.warehouse_id' => 'stock.warehouse_id',
            'stock_details2.supplier_id' => 'stock_details2.supplier_id',
            'stock_details2.no_aju' => 'stock_details2.no_aju',
            'stock_details.keterangan' => 'stock_details.keterangan',
            'stock.tipe_barang' => 'stock.tipe_barang'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_details2.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = '
            divisis.divisi as divisiName,
            warehouses.warehouse_name as warehouseName,
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.no_aju,
            stock_details2.no_po,
            stock_details2.qty,
            stock_details2.supplier_id,
            stock_details2.harga_umum,
            stock_details2.harga_harian,
            stock_details2.harga_bulanan,
            stock_details2.no_dokumen as no_dokumen2,
            stock_details.no_dokumen,
            stock_details.stock_date,
            stock_details.keterangan,
            stock_details.sumber,
            stock.company_id,
            stock.divisi_id,
            stock.warehouse_id ,
            stock.barang1_id,
            stock.barang2_id,
            stock.kemasan_id,
            stock.tipe_barang

        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('stock', 'stock.id = stock_details2.stock_id', 'left')
            ->join('divisis', 'divisis.id = stock.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id', 'left')
            ->where($condition)
            ->whereIn('sumber', $addCondition['sumber'])
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] != "" || $addCondition['warehouse_id'] != "" || $addCondition['tipe_barang'] != "" || $addCondition['supplier_id'] || $addCondition['bc_id'] != "" || $addCondition['no_aju'] != "" || $addCondition['date_start'] != "" || $addCondition['date_end'] != "") {
            $dataQry->groupStart();
        }

        if (isset($addCondition['bc_id']) && $addCondition['bc_id'] !== "" || $addCondition['bc_id'] === '0') {
            $dataQry->where('stock_details2.bc_id', $addCondition['bc_id']);
        }

        if (isset($addCondition['no_aju']) && $addCondition['no_aju'] !== "") {
            $dataQry->where('stock_details2.no_aju', $addCondition['no_aju']);
        }

        if (isset($addCondition['supplier_id']) && $addCondition['supplier_id'] !== "") {
            $dataQry->where('stock_details2.supplier_id', $addCondition['supplier_id']);
        }

        if (isset($addCondition['tipe_barang']) && $addCondition['tipe_barang'] !== "") {
            $dataQry->where('stock.tipe_barang', $addCondition['tipe_barang']);
        }

        if (isset($addCondition['divisi_id']) && $addCondition['divisi_id'] !== "") {
            $dataQry->where('stock.divisi_id', $addCondition['divisi_id']);
        }


        if (isset($addCondition['warehouse_id']) && $addCondition['warehouse_id'] !== "") {
            $dataQry->where('stock.warehouse_id', $addCondition['warehouse_id']);
        }

        if (isset($addCondition['date_start']) && $addCondition['date_start'] !== "") {
            $dataQry->where('stock_details.stock_date >=', $addCondition['date_start']);
        }

        if (isset($addCondition['date_end']) && $addCondition['date_end'] !== "") {
            $dataQry->where('stock_details.stock_date <=', $addCondition['date_end']);
        }

        if ($addCondition['divisi_id'] != "" || $addCondition['warehouse_id'] != "" || $addCondition['tipe_barang'] != "" || $addCondition['supplier_id'] || $addCondition['bc_id'] != "" || $addCondition['no_aju'] != "" || $addCondition['date_start'] != "" || $addCondition['date_end'] != "") {
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


    public function getListLaporanMutasiBarang($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang' => 'barang_master.kode_barang',
            'nama_barang' => 'barang_master.barang_name',
            'satuan' => '  barang_master_spesifikasi.satuan_1',
            'kategori_barang' =>  'parent_barang.parent_type',
            'jenis_kategori'    => 'parent_barang.parent_name',
            'divisi' => 'divisis.divisi',
            'warehouse' => 'warehouses.warehouse_name',
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

        $dataQry = $this->db->table('stock')
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id')
            ->join('divisis', 'divisis.id = stock.divisi_id')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
            ->whereIn('parent_barang.parent_type', $addCondition['parent_type'])
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if (
            $addCondition['divisi_id'] ||
            $addCondition['warehouse_id'] ||
            $addCondition['search']
        ) {
            $dataQry->groupStart();
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
                ->orWhere('barang_master.kode_barang', $addCondition['search'])
                ->orWhere('parent_name', $addCondition['search']);
        }

        if (
            $addCondition['divisi_id'] ||
            $addCondition['warehouse_id'] ||
            $addCondition['search']
        ) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->get($limit, $offset)->getResult();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getListLaporanMutasiKemasan($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang' => 'kemasan.kode',
            'nama_barang' => 'kemasan.name',
            'satuan' => 'kemasan.satuan_id',
            'kategori_barang' =>  'parent_barang.parent_type',
            'divisi' => 'divisis.divisi',
            'warehouse' => 'warehouses.warehouse_name',
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

        $dataQry = $this->db->table('stock')
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
            $addCondition['divisi_id'] ||
            $addCondition['warehouse_id'] ||
            $addCondition['search']
        ) {
            $dataQry->groupStart();
        }

        if ($addCondition['parent_type']) {
            $dataQry->whereIn('parent_barang.parent_type', $addCondition['parent_type']);
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
            $addCondition['divisi_id'] ||
            $addCondition['warehouse_id'] ||
            $addCondition['search']
        ) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->get($limit, $offset)->getResult();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getTotalStokLogLaporanMutasi($condition, $addCondition)
    {

        if (isset($addCondition['stock_sum'])) {
            // SUM KHUSUS OUTOR IN DI SUM
            $selectQry = '
            SUM(stock_details2.qty)
            AS stok_total,   
    ';
        } else {
            // SUM IN - OUT
            $selectQry = '
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,   
    ';
        }



        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('stock', 'stock.id = stock_details.stock_id')
            ->where($condition)
            ->where('stock.deletedAt', null)
            ->where('stock_details.deletedAt', null)
            ->where('stock_details2.deletedAt', null);

        if (isset($addCondition['stock_awal'])) {
            $dataQry->where('stock_details.stock_date <=', $addCondition['date_start']);
        } else {

            if (isset($addCondition['date_start']) && $addCondition['date_start'] !== "") {
                $dataQry->where('stock_details.stock_date >=', $addCondition['date_start']);
            }

            if (isset($addCondition['date_end']) && $addCondition['date_end'] !== "") {
                $dataQry->where('stock_details.stock_date <=', $addCondition['date_end']);
            }
        }


        $resultData = $dataQry->findAll();

        if (count($resultData) == 0) {
            return 0;
        } else {
            return $resultData[0]['stok_total'];
        }
    }

    public function getStockDetailByStockDokumen($stockDokumen, $sumber, $companyId, $divisiId, $warehouseId, $barangId, $spesifikasiId, $kemasanId)
    {
        $selectQry = '
            suppliers.name AS supplier_name,
            stock.barang1_id,
            stock.barang2_id,
            stock.kemasan_id,
            stock.tipe_barang,
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

        if ($kemasanId == null) {
            $dataQry = $this->asArray()
                ->select($selectQry)
                ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
                ->join('stock', 'stock.id = stock_details.stock_id', 'left')
                ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
                ->where('stock_details.sumber', $sumber)
                ->where('stock_details2.stock_dokumen', $stockDokumen)
                ->where('stock.barang1_id', $barangId)
                ->where('stock.barang2_id', $spesifikasiId)
                ->where('stock.company_id', $companyId)
                ->where('stock.divisi_id', $divisiId)
                ->where('stock.warehouse_id', $warehouseId)
                ->groupBy('stock_details2.stock_dokumen')
                ->first();
        } else {
            $dataQry = $this->asArray()
                ->select($selectQry)
                ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
                ->join('stock', 'stock.id = stock_details.stock_id', 'left')
                ->join('suppliers', 'suppliers.id = stock_details2.supplier_id', 'left')
                ->where('stock_details.sumber', $sumber)
                ->where('stock_details2.stock_dokumen', $stockDokumen)
                ->where('stock.kemasan_id', $kemasanId)
                ->where('stock.company_id', $companyId)
                ->where('stock.divisi_id', $divisiId)
                ->where('stock.warehouse_id', $warehouseId)
                ->groupBy('stock_details2.stock_dokumen')
                ->first();
        }

        return $dataQry;
    }

    public function getNomorDaftar($noAju, $bcId)
    {
        $bc23Model = new BC23Model();
        $bc40Model = new BC40Model();
        $bc27Model = new BC27Model();
        $ppbkbModel = new PPBKBModel();
        $metaDataModel = new MetadataModel();

        $bcType = $metaDataModel->find($bcId);
        $noDaftar = "";
        if ($bcType == "NON PABEAN") {
            // NON PABEAN
            $noDaftar = "-";
        } elseif ($bcId == 48) {
            // BC 2.3
            $bcDetail = $bc23Model->select('no_daftar, bc_purchase_order.createdAt as tanggal_dokumen')
                ->join('bc_purchase_order', 'bc_purchase_order.id = bc_23.bc_purchase_order_id', 'left')
                ->where('no_aju', $noAju)
                ->first();
            $noDaftar = $bcDetail != null ? $bcDetail['no_daftar'] : "-";
        } elseif ($bcId == 52) {
            // BC 2.7
            $bcDetail = $bc27Model->where('no_aju', $noAju)->first();
            $noDaftar = $bcDetail != null ? $bcDetail['no_daftar'] : "-";
        } elseif ($bcId == 53) {
            // BC 4.0
            $bcDetail = $bc40Model->select('no_daftar, bc_purchase_order.createdAt as tanggal_dokumen')
                ->join('bc_purchase_order', 'bc_purchase_order.id = bc_40.bc_purchase_order_id', 'left')
                ->where('no_aju', $noAju)
                ->first();
            $noDaftar = $bcDetail != null ? $bcDetail['no_daftar'] : "-";
        } elseif ($bcId == 1426) {
            // PPBKB
            $bcDetail = $ppbkbModel->where('no_ppbkb', $noAju)->first();
            $noDaftar = $bcDetail != null ? $bcDetail['no_daftar'] : "-";
        }

        return $noDaftar;
    }
}
