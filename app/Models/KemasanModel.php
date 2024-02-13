<?php

namespace App\Models;

use CodeIgniter\Model;

class KemasanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'kemasan';
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
            'kemasan.kode'              => 'kemasan.kode',
            'kemasan.name'              => 'kemasan.name',
            'parent_barang.parent_name' => 'parent_barang.parent_name',
            'satuans.kode_satuan'            => 'satuans.kode_satuan'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'kemasan.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            kemasan.*,
            parent_barang.parent_name,
            satuans.kode_satuan
        ";

        $DataQry = $this->asArray()
            ->select($selectQry)
            ->join('satuans', 'kemasan.satuan_id = satuans.id', 'left')
            ->join('parent_barang', 'parent_barang.id = kemasan.parent_type_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $DataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['parent_type_id']) {
            $DataQry->groupStart();
        }

        if ($addCondition['parent_type_id']) {
            $DataQry->where('kemasan.parent_type_id', $addCondition['parent_type_id']);
        }

        if ($addCondition['search']) {
            $DataQry->like('satuans.kode_satuan', $addCondition['search'])
                ->orLike('kemasan.name', $addCondition['search'])
                ->orLike('parent_name', $addCondition['search']);
        }

        if ($addCondition['search'] || $addCondition['parent_type_id']) {
            $DataQry->groupEnd();
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
