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
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT employees.*,divisis.divisi as divisionName FROM employees ";
        $requete .= "LEFT JOIN divisis ON (employees.division_id=divisis.id) ";
        // $requete = "SELECT customers.*,provinces.province_name,cities.city_name,s1.nama_sub as ap_name,s2.nama_sub as ar_name FROM customers ";
        // $requete .= "LEFT JOIN provinces ON (customers.province_id=provinces.id) ";
        // $requete .= "LEFT JOIN cities ON (customers.city_id=cities.id) ";
        // $requete .= "LEFT JOIN sub_akuns s1 ON (customers.ap_id=s1.id) ";
        // $requete .= "LEFT JOIN sub_akuns s2 ON (customers.ar_id=s2.id) ";
        $requete .= "WHERE employees.deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(employees.name) like '%" . strtoupper($values["name"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(employees.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.nik) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.nip) like '%" . strtoupper($values["search"]) . "%' OR UPPER(divisis.divisi) like '%" . strtoupper($values["search"]) . "%') ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM employees ";
        $requete .= "WHERE employees.deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(employees.name) like '%" . strtoupper($values["name"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(employees.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.nik) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.nip) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.jabatan_id) like '%" . strtoupper($values["search"]) . "%') ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }

    public function getEmployees($company_id)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $company_id,
            'users.id' => null,
            'employees.status' => "Aktif"
        ];

        $builder = $this->db->table('employees')
            ->select("employees.*, users.id as users_id, users.name as users_name, bagian.nama_bagian")
            ->join('users', 'users.employee_id = employees.id', 'left')
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

    public function getEmployeesByDivisionID($company_id, $divisionID)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $company_id,
            'divisis.id' => $divisionID,
            'divisis.deletedAt' => null,
            'employees.status' => "Aktif"
        ];

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

    public function getEmployeesWithPagination($companyID, $employeesID = null, $divisiID = null, $perPage = 10)
    {
        $arrCondition = [
            'employees.deletedAt' => null,
            'employees.company_id' => $companyID,
            'users.id' => null,
            'employees.status' => "Aktif"
        ];

        if ($employeesID !== null) {
            $arrCondition['employees.id'] = $employeesID;
        }

        if ($divisiID !== null) {
            $arrCondition['employees.division_id'] = $divisiID;
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
}
