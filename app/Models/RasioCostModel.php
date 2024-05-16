<?php

namespace App\Models;

use CodeIgniter\Model;

class RasioCostModel extends Model
{
    protected $table = 'rasio_cost';
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

    public function getDataNameCostRasioCost($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
        rasio_cost.setting_costing_id,
        rasio_cost.setting_costing_parent_id,
        SUM(rasio_cost.total_cost) AS total_cost,
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('rasio', 'rasio.id = rasio_cost.rasio_id', 'left')
            ->like('rasio.bulan', $where['tanggal_jurnal'])
            ->where('rasio.company_id', $where['company_id'])
            ->where('rasio.department_id', $where['divisi_id'])
            ->where('rasio.deletedAt', $where['deletedAt'])
            ->where('rasio_cost.deletedAt', $where['deletedAt'])
            ->groupBy('rasio_cost.setting_costing_id')
            ->findAll();

        return $dataQry;
    }

    public function getDataRasioCost($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
        rasio_cost.*
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('rasio', 'rasio.id = rasio_cost.rasio_id', 'left')
            ->like('rasio.bulan', $where['tanggal_jurnal'])
            ->where('rasio.company_id', $where['company_id'])
            ->where('rasio.department_id', $where['divisi_id'])
            ->where('rasio.deletedAt', $where['deletedAt'])
            ->where('rasio_cost.deletedAt', $where['deletedAt'])
            ->findAll();

        return $dataQry;
    }
}
