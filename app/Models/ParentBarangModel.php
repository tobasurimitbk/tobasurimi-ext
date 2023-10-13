<?php

namespace App\Models;

use CodeIgniter\Model;

class ParentBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'parent_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'parent_type',
        'parent_name',
        'kategori',
        'deletedAt'
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
            'parent_name'       => 'parent_barang.parent_name',
            'createdAt'         => 'big_days.createdAt',
            'updatedAt'         => 'big_days.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'parent_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "parent_barang.*";
        $DataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $DataQry->countAllResults(false);

        if ($addCondition['search']) {
            $DataQry->groupStart()
                ->like('parent_name', $addCondition['search'])
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
}
