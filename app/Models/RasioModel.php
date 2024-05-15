<?php

namespace App\Models;

use CodeIgniter\Model;

class RasioModel extends Model
{
    protected $table = 'rasio';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $DBGroup          = 'default';
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields = [];

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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi_name'       => 'divisis.divisi',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'rasio.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rasio.*, divisis.divisi";

        $divisisDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = rasio.department_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $divisisDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['month'] || $addCondition['department']) {
            $divisisDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $divisisDataQry->like('divisis.divisi', $addCondition['search']);
        }

        if ($addCondition['month']) {
            $divisisDataQry->like('rasio.bulan', $addCondition['month']);
        }

        if ($addCondition['department']) {
            $divisisDataQry->where('rasio.department_id', $addCondition['department']);
        }

        if ($addCondition['search'] || $addCondition['month'] || $addCondition['department']) {
            $divisisDataQry->groupEnd();
        }

        $totalFilteredData = $divisisDataQry->countAllResults(false);
        $data = $divisisDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }
}
