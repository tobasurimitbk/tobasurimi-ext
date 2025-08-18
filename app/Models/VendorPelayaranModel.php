<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorPelayaranModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'vendor_pelayaran';
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
            'id'                  => 'id',
            'nama_vendor'         => 'nama_vendor',
            'alamat'              => 'alamat',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'nama_vendor';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "vendor_pelayaran.*";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition);

        $totalData = $dataQry->countAllResults(false);

        if (isset($addCondition['search']) && !empty($addCondition['search'])) {
            $dataQry->groupStart()
                ->like('nama_vendor', $addCondition['search'])
                ->orLike('alamat', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);

        $data = $dataQry->orderBy($sort, $sortType)
            ->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }
}
