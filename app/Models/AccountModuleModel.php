<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModuleModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'account_module';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'type',
        'kategori',
        'module',
        'ap_id',
        'ar_id'
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
    protected $afterDelete    = [];

    public function getAccountModuleList($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'name'              => 'account_module.name',
            'type'              => 'account_module.type',
            'kategori'       => 'account_module.kategori',
            'ap_id'              => 'account_module.ap_id',
            'ar_id'       => 'account_module.ar_id',
            'createdAt'         => 'account_module.createdAt',
            'updatedAt'         => 'account_module.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'account_module.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $accountModuleDataQry = $this->asObject()
            ->select("*")
            ->orderBy($sort, $sortType);

        $totalData = $accountModuleDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $accountModuleDataQry->groupStart()
                ->like('name', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $accountModuleDataQry->countAllResults(false);
        $data = $accountModuleDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAccountModuleById($id)
    {
        $accountModuleData = $this->asObject()
            ->select('account_module.*')
            ->find($id);

        return $accountModuleData;
    }

    public function getAccountModuleForJurnal()
    {
        $select =   "account_module.*";
        return $this->asObject()
            ->select($select)
            ->where('account_module.deletedAt', null)
            ->findAll();
    }
}
