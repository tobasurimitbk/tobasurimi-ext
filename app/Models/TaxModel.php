<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'taxes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function getTaxByType($type)
    {
        $arrCondition = [
            'deletedAt' => null,
            'company_id' => session()->get("login")->this_company_id,
            'type' => $type
        ];

        $builder = $this->db->table('taxes');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'tax_name'        => 'taxes.name',
            'tax_type'        => 'taxes.type',
            'tax_value'          => 'taxes.tax_value',
            'createdAt'         => 'taxes.createdAt',
            'updatedAt'         => 'taxes.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'taxes.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "taxes.*";
        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);
        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getById($id)
    {
        $selectQry = "taxes.*";

        $data = $this->asObject()
            ->select($selectQry)
            ->find($id);

        return $data;
    }
}
