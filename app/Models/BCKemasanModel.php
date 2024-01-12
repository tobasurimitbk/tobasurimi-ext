<?php

namespace App\Models;

use CodeIgniter\Model;

class BCKemasanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_kemasan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
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

    public function getList($condition, $limit = 10, $offset = 0)
    {
        $sort = 'bc_kemasan.createdAt';
        $sortType = 'DESC';

        $selectQry = "bc_kemasan.*";

        $pinjamanQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $pinjamanQry->countAllResults(false);

        $totalFilteredData = $pinjamanQry->countAllResults(false);
        $data = $pinjamanQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getLast($penerimaanBarangID)
    {
        return $this->asArray()->where('penerimaan_barang_id', $penerimaanBarangID)->orderBy('createdAt', "DESC")->first();
    }
}
