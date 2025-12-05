<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendancesUnitOutsourceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'attendance_unit_outsource';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'name',
        'ip',
        'unit_key',
        'master',
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
    protected $afterDelete    = [];

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'name'              => 'attendance_unit_outsource.name',
            'ip'              => 'attendance_unit_outsource.ip',
            'createdAt'         => 'attendance_unit_outsource.createdAt',
            'updatedAt'         => 'attendance_unit_outsource.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'attendance_unit_outsource.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "attendance_unit_outsource.*";
        $DataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $DataQry->countAllResults(false);

        if ($addCondition['search']) {
            $DataQry->groupStart()
                ->like('name', $addCondition['search'])
                ->orLike('ip', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $DataQry->countAllResults(false);
        $data = $DataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getById($id)
    {
        $temp = $this->asObject()
            ->select('attendance_unit_outsource.*')
            ->find($id);

        return $temp;
    }

    public function getByCompany_id_and_master($company_id, $master)
    {
        $arrCondition = [
            'company_id' => $company_id,
            'master' => $master
        ];

        $builder = $this->db->table('attendance_unit_outsource');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getByCompany_id($company_id)
    {
        $arrCondition = [
            'company_id' => $company_id,
            'deletedAt' => NULL,

        ];

        $builder = $this->db->table('attendance_unit_outsource');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getByMaster($master)
    {
        $arrCondition = [
            'master' => $master,
            'deletedAt' => NULL,
        ];

        $builder = $this->db->table('attendance_unit_outsource');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}
