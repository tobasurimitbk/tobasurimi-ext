<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriAkunsModel extends Model
{
    protected $table = 'kategori_akuns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'kelompok_id',
        'no_kategori',
        'nama_kategori',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM " . $this->table . " WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT " . $this->table . ".*,metadata.value as kelompok_akun FROM " . $this->table . " ";
        $requete .= "LEFT JOIN metadata ON (kategori_akuns.kelompok_id=metadata.id) ";
        $requete .= "WHERE kategori_akuns.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND " . $this->table . ".company_id ='" . $values["company_id"] . "' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(no_kategori) like '%" . strtoupper($values["search"]) . "%' OR UPPER(nama_kategori) like '%" . strtoupper($values["search"]) . "%')");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM " . $this->table . " ";
        $requete .= "WHERE kategori_akuns.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND " . $this->table . ".company_id ='" . $values["company_id"] . "' ");
        if (isset($values["nama_sub"]))
            $requete .= ($values["nama_sub"] == "") ? "" : ("AND UPPER(nama_sub) like '%" . strtoupper($values["nama_sub"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
