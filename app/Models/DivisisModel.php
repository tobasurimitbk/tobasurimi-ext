<?php

namespace App\Models;

use CodeIgniter\Model;

class DivisisModel extends Model
{
    protected $table = 'divisis';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'divisi',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM divisis WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_company_id($company_id)
    {
        $requete = "SELECT * FROM divisis WHERE company_id='" . $company_id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT divisis.* FROM divisis ";
        $requete .= "WHERE divisis.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND divisis.company_id ='" . $values["company_id"] . "' ");
        if (isset($values["name"]))
            $requete .= ($values["divisi"] == "") ? "" : ("AND UPPER(divisis.divisi) like '%" . strtoupper($values["divisi"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(divisis.divisi) like '%" . strtoupper($values["search"]) . "%') ");


        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";
        //echo $requete;

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM divisis ";
        $requete .= "WHERE divisis.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND divisis.company_id ='" . $values["company_id"] . "' ");
        if (isset($values["name"]))
            $requete .= ($values["divisi"] == "") ? "" : ("AND UPPER(divisis.divisi) like '%" . strtoupper($values["divisi"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(divisis.divisi) like '%" . strtoupper($values["search"]) . "%') ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
