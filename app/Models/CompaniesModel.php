<?php

namespace App\Models;

use CodeIgniter\Model;

class CompaniesModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'logo',
        'company',
        'holding_company',
        'address',
        'province_id',
        'city_id',
        'zip_code',
        'phone',
        'email',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];


    public function get_by_in_id($id)
    {
        $requete = "SELECT * FROM companies WHERE deletedAt is null and id in (" . $id . ")";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM companies WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT companies.*,provinces.province_name,cities.city_name FROM companies ";
        $requete .= "LEFT JOIN provinces ON (companies.province_id=provinces.id) ";
        $requete .= "LEFT JOIN cities ON (companies.city_id=cities.id) ";
        $requete .= "WHERE companies.deletedAt is null ";
        if (isset($values["company"]))
            $requete .= ($values["company"] == "") ? "" : ("AND UPPER(company) like '%" . strtoupper($values["company"]) . "%' ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM companies ";
        $requete .= "WHERE companies.deletedAt is null ";
        if (isset($values["company"]))
            $requete .= ($values["company"] == "") ? "" : ("AND UPPER(company) like '%" . strtoupper($values["company"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
