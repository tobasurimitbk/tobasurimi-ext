<?php

namespace App\Models;

use CodeIgniter\Model;

class BCDokumenModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_dokumen';
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

        $sort = 'bc_dokumen.createdAt';
        $sortType = 'DESC';

        $selectQry = "bc_dokumen.*";

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

    public function get($bcPurchaseOrderID)
    {
        return $this->asArray()->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->first();
    }
}
