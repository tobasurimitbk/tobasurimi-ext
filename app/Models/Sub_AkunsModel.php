<?php

namespace App\Models;

use CodeIgniter\Model;

class Sub_AkunsModel extends Model
{
    protected $table = 'sub_akuns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'kategori_id',
        'header_id',
        'no_sub',
        'nama_sub',
        'coa_id',
        'status',
        'createdAt',
        'updatedAt',
        'deletedAt'
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
        $requete = "SELECT * FROM sub_akuns ";
        $requete .= "WHERE 1 ";
        if (isset($values["nama_sub"]))
            $requete .= ($values["nama_sub"] == "") ? "" : ("AND UPPER(nama_sub) like '%" . strtoupper($values["nama_sub"]) . "%' ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM sub_akuns ";
        $requete .= "WHERE 1 ";
        if (isset($values["nama_sub"]))
            $requete .= ($values["nama_sub"] == "") ? "" : ("AND UPPER(nama_sub) like '%" . strtoupper($values["nama_sub"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
