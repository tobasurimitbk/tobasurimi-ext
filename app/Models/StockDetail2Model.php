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
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['bc_id'] || $addCondition['no_aju']) {
            $dataQry->groupStart();
            if ($addCondition['bc_id']) {
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
        $no_aju
    ) {
        $stokDetail2 = $this->insert([
            'bc_id' => $bc_id,
            'stock_id' => $stok_id,
            'stock_detail_id' => $stok_detail_id,
            'qty' => $qty,
            'no_aju' => $no_aju
        ]);

        return $stokDetail2;
    }
}
