<?php

namespace App\Models;

use CodeIgniter\Model;

class MetadataModel extends Model
{
    protected $table = 'metadata';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'name',
        'value',
        'description',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM metadata WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_name($name)
    {
        $requete = "SELECT * FROM metadata WHERE name='" . $name . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT * FROM metadata ";
        $requete .= "WHERE 1 ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND name = '" . $values["name"] . "' ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM metadata ";
        $requete .= "WHERE 1 ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND name = '" . $values["name"] . "' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
