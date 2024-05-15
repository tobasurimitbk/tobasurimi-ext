<?php

namespace App\Models;

use CodeIgniter\Model;

class PengusahaTPBModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pengusaha_tpb';
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'npwp' => 'npwp',
            'nama_pengusaha' => 'nama_pengusaha',
            'alamat' => 'alamat',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "pengusaha_tpb.*";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry->like('npwp', $addCondition['search'])->orLike('nama_pengusaha', $addCondition['search'])->orLike('alamat', $addCondition['search']);
        }

        if ($addCondition['search']) {
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
