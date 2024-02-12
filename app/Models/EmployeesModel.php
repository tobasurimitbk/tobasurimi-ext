<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeesModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $DBGroup          = 'default';
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'division_id',
        'jabatan_id',
        'join_date',
        'nik',
        'pin',
        'name',
        'pendidikan',
        'nip',
        'gender',
        'dob',
        'phone_no',
        'acc_no',
        'bank_name',
        'owner_name',
        'email',
        'address',
        'province_id',
        'city_id',
        'postal_code',
        'religion_id',
        'marriage_id',
        'child',
        'employee_img',
        'status',
        'tipe',
        'bagian_id',
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

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM employees WHERE id='" . $id . "'";
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'employees.nip' => 'employees.nip',
            'employees.name' => 'employees.name',
            'employees.division_id' => 'employees.division_id',
            'employees.bagian_id' => 'employees.bagian_id',
            'employees.tipe' => 'employees.tipe',
            'employees.dob' => 'employees.dob',
            'employees.gender' => 'employees.gender',
            'employees.status' => 'employees.status'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'employees.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "employees.*,
            divisis.divisi,
            bagian.nama_bagian";

        $employeeQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = employees.division_id')
            ->join('bagian', 'bagian.id = employees.bagian_id')
            ->orderBy($sort, $sortType);

        $totalData = $employeeQry->countAllResults(false);

        if ($addCondition['division_id'] || $addCondition['bagian_id'] || $addCondition['tipe'] || $addCondition['search']) {
            $employeeQry->groupStart();
        }

        if ($addCondition['division_id']) {
            $employeeQry->where('employees.division_id', $addCondition['division_id']);
        }

        if ($addCondition['bagian_id']) {
            $employeeQry->where('employees.bagian_id', $addCondition['bagian_id']);
        }

        if ($addCondition['tipe']) {
            $employeeQry->where('employees.tipe', $addCondition['tipe']);
        }

        if ($addCondition['search']) {
            $employeeQry->like('employees.name', $addCondition['search'])->orLike('employees.nip', $addCondition['search']);
        }


        if ($addCondition['division_id'] || $addCondition['bagian_id'] || $addCondition['tipe'] || $addCondition['search']) {
            $employeeQry->groupEnd();
        }

        $totalFilteredData = $employeeQry->countAllResults(false);
        $data = $employeeQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT employees.*, divisis.divisi as divisionName FROM employees ";
        $requete .= "LEFT JOIN divisis ON (employees.division_id = divisis.id) ";
        $requete .= "WHERE employees.deletedAt IS NULL AND divisis.deletedAt IS NULL";

        if (isset($values["name"])) {
            $requete .= ($values["name"] == "") ? "" : " AND UPPER(employees.name) LIKE '%" . strtoupper($values["name"]) . "%'";
        }

        if (isset($values["search"])) {
            $searchCondition = (
                "UPPER(employees.name) LIKE '%" . strtoupper($values["search"]) . "%' OR " .
                "UPPER(employees.nik) LIKE '%" . strtoupper($values["search"]) . "%' OR " .
                "UPPER(employees.nip) LIKE '%" . strtoupper($values["search"]) . "%' OR " .
                "UPPER(divisis.divisi) LIKE '%" . strtoupper($values["search"]) . "%'"
            );
            $requete .= ($values["search"] == "") ? "" : " AND ($searchCondition)";
        }

        if (isset($values["company_id"])) {
            $requete .= ($values["company_id"] == "") ? "" : " AND employees.company_id = " . (int)$values["company_id"];
        }

        if ($sortby != '') {
            $requete .= " ORDER BY $sortby ";
        }

        if ($limit >= 0) {
            $requete .= " LIMIT $limit OFFSET $offset";
        }

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }


    public function total_list($values)
    {
        $requete = "SELECT COUNT(*) as total FROM employees WHERE employees.deletedAt IS NULL ";

        if (isset($values["name"])) {
            $requete .= ($values["name"] == "") ? "" : "AND UPPER(employees.name) LIKE '%" . strtoupper($values["name"]) . "%' ";
        }

        if (isset($values["search"])) {
            $searchCondition = (
                "UPPER(employees.name) LIKE '%" . strtoupper($values["search"]) . "%' OR " .
                "UPPER(employees.nik) LIKE '%" . strtoupper($values["search"]) . "%' OR " .
                "UPPER(employees.nip) LIKE '%" . strtoupper($values["search"]) . "%' OR " .
                "UPPER(employees.jabatan_id) LIKE '%" . strtoupper($values["search"]) . "%'"
            );
            $requete .= ($values["search"] == "") ? "" : " AND ($searchCondition)";
        }

        if (isset($values["company_id"])) {
            $requete .= ($values["company_id"] == "") ? "" : " AND employees.company_id = " . (int)$values["company_id"];
        }

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }


    public function getEmployees($company_id)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $company_id,
            'divisis.deletedAt' => null,
            'bagian.deletedAt' => null,
            'employees.status' => "Aktif"
        ];

        $builder = $this->db->table('employees')
            ->select("employees.*, users.id as users_id, users.name as users_name, bagian.nama_bagian")
            ->join('users', 'users.employee_id = employees.id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->join('bagian', 'employees.bagian_id = bagian.id', 'left');
        $builder->groupStart()->where($arrCondition)->groupEnd();
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getEmployeesUserDelete($company_id)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $company_id,
            'users.deletedAt !=' => null
        ];

        $builder = $this->db->table('employees')
            ->select("employees.*, users.name as users_name, users.deletedAt as userDeletedAt, users.id as user_id")
            ->join('users', 'users.employee_id = employees.id');
        $builder->groupStart()->where($arrCondition)->groupEnd();
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getEmployeesByDivision($company_id, $division)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $company_id,
            'divisis.divisi' => $division,
            'divisis.deletedAt' => null
        ];

        $builder = $this->db->table('employees')
            ->select("employees.*")
            ->join('divisis', 'employees.division_id = divisis.id', 'left');
        $builder->groupStart()->where($arrCondition)->groupEnd();
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getEmployeesByDivisionID($company_id, $divisionID, $golongan = null)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $company_id,
            'divisis.deletedAt' => null,
            'employees.status' => "Aktif"
        ];

        if ($golongan != null && !empty($golongan)) {
            $arrCondition['employees.tipe'] = $golongan;
        }

        if ($divisionID != null && !empty($divisionID)) {
            $arrCondition['divisis.id'] = $divisionID;
        }

        $builder = $this->db->table('employees')
            ->select("employees.*, divisis.divisi, bagian.nama_bagian")
            ->join('divisis', 'employees.division_id = divisis.id', 'left')
            ->join('bagian', 'employees.bagian_id = bagian.id', 'left');
        $builder->groupStart()->where($arrCondition)->groupEnd();
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getEmployeesNotSyncAttendances($company_id)
    {
        $arrCondition = [
            //            'employees.deletedAt' => null,
            'employees.company_id' => $company_id,
            //            'users.deletedAt' => null
        ];

        $builder = $this->db->table('employees')
            ->select("employees.id,employees.attendance_sync,employees.name")
            //->where('employees.attendance_sync', 0);
            ->where('employees.id >', 0);
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getEmployeesWithPagination($companyID, $employeesID = null, $divisiID = null, $tipe = null,  $perPage = 10)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $companyID,
            //'users.id' => null,
            'employees.status' => "Aktif"
        ];

        if ($employeesID !== null && $employeesID !== "null") {
            $arrCondition['employees.id'] = $employeesID;
        }

        if ($divisiID !== null && $divisiID !== "") {
            $arrCondition['employees.division_id'] = $divisiID;
        }

        if ($tipe !== null && $tipe !== "") {
            $arrCondition['tipe'] = $tipe;
        }

        $this->builder()
            ->select("employees.*, users.id as users_id, users.name as users_name, divisis.divisi, bagian.nama_bagian")
            ->join('users', 'users.employee_id = employees.id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->join('bagian', 'bagian.id = employees.bagian_id', 'left')
            ->groupStart()->where($arrCondition)->groupEnd();


        $result = [
            'data' => $this->paginate($perPage),
            'pager' => $this->pager,
        ];

        return $result;
    }

    public function getEmployeesAndDivisi($company_id)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $company_id,
            'employees.status' => "Aktif"
        ];

        $builder = $this->db->table('employees')
            ->select("employees.*, divisis.divisi, bagian.nama_bagian AS namaBagian")
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->join('bagian', 'bagian.id = employees.bagian_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getSingleEmployee($employeesID)
    {
        return $this->asArray()->select("employees.*, divisis.divisi, bagian.nama_bagian")
            ->join('divisis', 'divisis.id = employees.division_id')
            ->join('bagian', 'bagian.division_id = divisis.id')
            ->where('employees.id', $employeesID)
            ->first();
    }

    public function getEmployeeSales()
    {
        $res = $this->asArray()->select('employees.id, employees.name, employees.nip')->join('bagian', 'bagian.id = employees.bagian_id')
            ->where('employees.deletedAt', null)
            // ->where('bagian.nama_bagian', "SALES")
            ->findAll();

        return $res;
    }
}
