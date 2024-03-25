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

        if ($addCondition['bc_id'] != "" || $addCondition['no_aju'] != "") {
            $dataQry->groupStart();
            if ($addCondition['bc_id'] || $addCondition['bc_id'] == 0 && $addCondition['bc_id'] != "") {
                $dataQry->where('stock_details2.bc_id', $addCondition['bc_id']);
            }

            if ($addCondition['no_aju']) {
                $dataQry->where('stock_details2.no_aju', $addCondition['no_aju']);
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
        $no_dokumen
    ) {
        $stokDetail2 = $this->insert([
            'bc_id' => $bc_id,
            'stock_id' => $stok_id,
            'stock_detail_id' => $stok_detail_id,
            'qty' => $qty,
            'no_aju' => $no_aju,
            'no_dokumen' => $no_dokumen
        ]);

        return $stokDetail2;
    }

    public function getListStokLog($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'stock_details2.no_dokumen' => 'stock_details2.no_dokumen',
            'stock_details.no_dokumen' => 'stock_details.no_dokumen',
            'stock_details2.bc_id' => 'stock_details2.bc_id',
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
            stock_details2.no_dokumen AS no_dokumen2, 
            stock_details.no_dokumen AS no_dokumen1,
            stock.barang1_id,
            stock.barang2_id,
            stock.kemasan_id,
            stock.divisi_id,
            stock.warehouse_id,
            stock_details.status,
            stock_details.sumber,
            stock_details2.qty AS stok_total,
            stock_details2.bc_id,
            stock_details2.no_aju,
            stock_details.stock_date,
            stock_details.keterangan,
            stock_details2.createdAt
        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->join('stock', 'stock.id = stock_details2.stock_id')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['bc_id'] != "" || $addCondition['divisi_id'] || $addCondition['warehouse_id']) {
            $dataQry->groupStart();
        }

        if ($addCondition['bc_id'] || $addCondition['bc_id'] != "") {
            $dataQry->where('stock_details2.bc_id', $addCondition['bc_id']);
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('stock.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->where('stock.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['search']) {
            $dataQry->like('stock_details2.no_dokumen', $addCondition['search'])
                ->orLike('stock_details.no_dokumen', $addCondition['search'])
                ->orLike('stock_details2.no_aju', $addCondition['search'])
                ->orLike('stock_details.sumber', $addCondition['search']);
        }

        if ($addCondition['search'] || $addCondition['bc_id'] != "" || $addCondition['divisi_id'] || $addCondition['warehouse_id']) {
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
            ->groupBy('stock_details.stock_id')
            ->findAll();

        if (count($dataQry) == 0) {
            return 0;
        } else {
            return $dataQry[0]['stok_total'];
        }
    }

    public function getStockListWithBCDoc($stockID)
    {
        $selectQry = '
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details.no_dokumen AS no_dokumen_1,
            stock_details.stock_date,
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,        
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->where('stock_details2.stock_id', $stockID)
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.no_aju')
            ->findAll();

        return $dataQry;
    }

    public function getStockListDetail($stockID, $bcID, $noAju)
    {

        $selectQry = '
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,        
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->where('stock_details2.stock_id', $stockID)
            ->where('stock_details2.bc_id', $bcID)
            ->where('stock_details2.no_aju', $noAju)
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.no_aju')
            ->first();

        return $dataQry;
    }
}
