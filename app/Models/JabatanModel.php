<?php

namespace App\Models;

use CodeIgniter\Model;

class JabatanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jabatans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'jabatan_name'
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

    public function getJabatanList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'jabatan_name'          => 'jabatans.jabatan_name',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'jabatans.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "jabatans.* ";

        $jabatansDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $jabatansDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $jabatansDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $jabatansDataQry
                ->like('jabatan_name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $jabatansDataQry->groupEnd();
        }

        $totalFilteredData = $jabatansDataQry->countAllResults(false);
        $data = $jabatansDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getJabatanDropdown()
    {
        $selectQry = "jabatans.* ";

        $jabatansDataQry = $this->asObject()
            ->select($selectQry);

        $data = $jabatansDataQry->findAll();

        return [
            'data' => $data,
        ];
    }
}
