<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'name',
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

    public function get_by_in_id($id)
    {
        $requete = "SELECT * FROM roles WHERE deletedAt is null and id in (" . $id . ")";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM roles WHERE deletedAt is null and id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function getRoleList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'name'          => 'roles.name',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'roles.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "roles.* ";

        $rolesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $rolesDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $rolesDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $rolesDataQry
                ->like('name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $rolesDataQry->groupEnd();
        }

        $totalFilteredData = $rolesDataQry->countAllResults(false);
        $data = $rolesDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getRoleDropdown()
    {
        $selectQry = "roles.* ";

        $rolesDataQry = $this->asObject()
            ->select($selectQry);

        $data = $rolesDataQry->findAll();

        return [
            'data'              => $data,
        ];
    }
}
