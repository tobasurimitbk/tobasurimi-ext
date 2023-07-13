<?php

namespace App\Models;

use CodeIgniter\Model;

class HsCodesModel extends Model
{
    protected $table = 'hs_codes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'komoditi',
        'code',
        'uraian_barang',
        'satuan_barang',
        'uraian_satuan',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM hs_codes WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT * FROM hs_codes ";
        $requete .= "WHERE hs_codes.deletedAt is null ";
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(komoditi) like '%" . strtoupper($values["search"]) . "%' OR UPPER(code) like '%" . strtoupper($values["search"]) . "%' OR UPPER(uraian_barang) like '%" . strtoupper($values["search"]) . "%')");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM hs_codes ";
        $requete .= "WHERE hs_codes.deletedAt is null ";
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(komoditi) like '%" . strtoupper($values["search"]) . "%' OR UPPER(code) like '%" . strtoupper($values["search"]) . "%' OR UPPER(uraian_barang) like '%" . strtoupper($values["search"]) . "%')");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
