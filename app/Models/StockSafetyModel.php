<?php

namespace App\Models;

use CodeIgniter\Model;

class StockSafetyModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_safety';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode', 'tipe_barang', 'safety_number', 're_stock_number', 'deletedAt'
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'stock_safety.tipe_barang' => 'stock_safety.tipe_barang',
            'stock_safety.safety_number' => 'stock_safety.safety_number',
            'stock_safety.re_stock_number' => 'stock_safety.re_stock_number',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_safety.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "stock_safety.*";
        $DataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $DataQry->countAllResults(false);

        if ($addCondition['search']) {
            $DataQry->groupStart()
                ->like('stock_safety.tipe_barang', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $DataQry->countAllResults(false);
        $data = $DataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function tipeBarangInArray()
    {
        $res = [];
        foreach ($this->where('deletedAt', null)->findAll() as $row) {
            array_push($res, $row['kode']);
        }
        return $res;
    }
}
