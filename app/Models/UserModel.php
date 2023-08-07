<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $DBGroup          = 'default';
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'name',
        'username',
        'user_pass',
        'company_role',
        'current_company_id',
        'employee_id',
        'status',
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
            'username'          => 'users.username',
            'name'              => 'users.name',
            'employeeName'      => 'employees.name',
            'status'            => 'users.status',
            'createdAt'         => 'users.createdAt',
            'updatedAt'         => 'users.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'kurs.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "users.*, employees.name as employeeName";
        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = users.employee_id', 'LEFT')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry->like('users.username', $addCondition['search'])->orLike('users.name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $dataQry->groupEnd();
        }

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

    public function getUserDropdown()
    {
        $arrCondition = [
            'deletedAt' => null
        ];

        $builder = $this->asObject()
        ->select("users.*");
        $builder->where($arrCondition);

        $data = $builder->findAll();

        return [
            'data' => $data,
        ];
    }

    public function check_current_name($id, $name)
    {
        $selectQry = "users.*";


        $data = $this->select($selectQry)
        ->where('id !=', $id)
        ->where('name', $name)
        ->where('deletedAt', NULL)       
        ->countAllResults();

        return $data;
    }

    public function check_current_username($id, $username)
    {
        $selectQry = "users.*";


        $data = $this->select($selectQry)
        ->where('id !=', $id)
        ->where('username', $username)
        ->where('deletedAt', NULL)       
        ->countAllResults();

        return $data;
    }

    public function getByEmployeeId($id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'employee_id' => $id
        ];

        $selectQry = "users.*
        ";

        $builder = $this->asObject()
            ->select($selectQry);
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResult();
    }

    public function getUser($id)
    {
        $arrCondition = [
            'users.deletedAt' => null,
            'users.id' => $id
        ];

        $selectQry = "users.*,
        employees.nip AS nip
        ";

        $builder = $this->asObject()
            ->select($selectQry)
            ->join('employees', 'employees.id = users.employee_id');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResult();
    }

    public function update_status($data)
    {
        $requete = "UPDATE user_ SET status='" . $data['status'] . "', date_update='" . $data['date_update'] . "', user_update='" . $data['user_update'] . "' where user_id='" . $data['id'] . "'";
        return $this->db->query($requete);
    }

    public function get_by_username($username)
    {
        $requete = "SELECT * FROM users ";
        $requete .= "WHERE deletedAt is null and username='" . $username . "' and users.status='Aktif' limit 1";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM users ";
        $requete .= "WHERE users.id='" . $id . "' ";
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT users.* FROM user_ ";
        $requete .= "WHERE deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(users.name) like '%" . strtoupper($values["name"]) . "%' ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM users ";
        $requete .= "WHERE deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(users.name) like '%" . strtoupper($values["name"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
