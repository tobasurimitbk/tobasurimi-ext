<?php

namespace App\Models;

use CodeIgniter\Model;

class JamKerjaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jam_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis',
        'jam_terlambat',
        'company_id'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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
            'jenis'    => 'jenis',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'id'] ?? 'id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $dataQry = $this->asObject()
            ->select('*')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['jenis']) {
            $dataQry->groupStart();
        }

        if ($addCondition['jenis']) {
            $dataQry->like('jam_kerja.jenis', $addCondition['jenis']);
        }

        if ($addCondition['jenis']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }
}
