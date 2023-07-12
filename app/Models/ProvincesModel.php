<?php

namespace App\Models;

use CodeIgniter\Model;

class ProvincesModel extends Model
{
    protected $table = 'provinces';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'province_name'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM provinces WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT * FROM provinces ";
        $requete .= "WHERE 1 ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(provinces_name) like '%" . strtoupper($values["name"]) . "%' ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM provinces ";
        $requete .= "WHERE 1 ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(provinces_name) like '%" . strtoupper($values["name"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
