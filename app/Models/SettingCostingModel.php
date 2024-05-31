<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingCostingModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'setting_costing';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'name',
        'parent_id',
        'coa',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

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

    public function getSettingCosting($where = [])
    {
        $select =   "setting_costing.*, setting_costing_details.*";
        return $this->asArray()
            ->select($select)
            ->where($where)
            ->where('setting_costing.deletedAt', null)
            ->join('setting_costing_details', 'setting_costing.id = setting_costing_details.setting_costing_id', 'left')
            ->findAll();
    }

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi_name' => 'divisis.divisi',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'setting_costing.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        // Ensure all selected columns have unique aliases
        $selectQry = "
        setting_costing.id AS id_setting_costing, 
        setting_costing.*, 
        setting_costing_details.id AS id_setting_costing_detail,
        setting_costing_details.setting_costing_id,
        setting_costing_details.company_id,
        setting_costing_details.divisi_id,
        setting_costing_details.coa_id,
        ";

        // Base query
        $settingCosting = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->where('setting_costing.id >', 10)
            ->join('setting_costing_details', 'setting_costing.id = setting_costing_details.setting_costing_id', 'left')
            ->groupBy('id_setting_costing')
            ->orderBy($sort, $sortType);

        // Total data count before any additional conditions
        $totalData = $settingCosting->countAllResults(false);

        // Apply conditional join and additional filters if divisi_id or company_id is present
        if (!empty($addCondition['divisi_id']) && !empty($addCondition['company_id'])) {
            if (!empty($addCondition['divisi_id'])) {
                $settingCosting->orWhere('setting_costing_details.divisi_id', $addCondition['divisi_id']);
            }

            if (!empty($addCondition['company_id'])) {
                $settingCosting->orWhere('setting_costing_details.company_id', $addCondition['company_id']);
            }
        }

        // Total filtered data count after additional conditions
        $totalFilteredData = $settingCosting->countAllResults(false);
        $data = $settingCosting->findAll($limit, $offset);

        return [
            'data' => $data,
            'totalData' => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort' => $sort,
            'sortType' => $sortType
        ];
    }
}
