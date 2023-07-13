<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeesModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'division_id',
        'jabatan',
        'join_date',
        'nik',
        'pin',
        'name',
        'shift_id',
        'SOT',
        'EOT',
        'BSOT',
        'BEOT',
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
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

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
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(employees.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.nik) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.nip) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.jabatan) like '%" . strtoupper($values["search"]) . "%') ");

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
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(employees.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.nik) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.nip) like '%" . strtoupper($values["search"]) . "%' OR UPPER(employees.jabatan) like '%" . strtoupper($values["search"]) . "%') ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
