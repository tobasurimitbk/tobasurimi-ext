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
        'company_id',
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

    public function update_status_by_id($id, $status)
    {
        $requete = "UPDATE sub_akuns SET status='" . $status . "' where id='" . $id . "'";
        return $this->db->query($requete);
    }


    public function get_by_id($id)
    {
        $requete = "SELECT sub_akuns.*,header_akuns.nama_header FROM sub_akuns ";
        $requete .= "LEFT JOIN header_akuns ON (header_akuns.id=sub_akuns.header_id) ";
        $requete .= "WHERE sub_akuns.id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT sub_akuns.*,kategori_akuns.nama_kategori,m1.value as kelompok_akun,m2.value as akun_coa,header_akuns.no_header,header_akuns.nama_header FROM sub_akuns ";
        $requete .= "LEFT JOIN kategori_akuns ON (kategori_akuns.id=sub_akuns.kategori_id) ";
        $requete .= "LEFT JOIN header_akuns ON (sub_akuns.header_id=header_akuns.id) ";
        $requete .= "LEFT JOIN metadata m1 ON (kategori_akuns.kelompok_id=m1.id) ";
        $requete .= "LEFT JOIN metadata m2 ON (sub_akuns.coa_id=m2.id) ";
        $requete .= "WHERE sub_akuns.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND sub_akuns.company_id ='" . $values["company_id"] . "' ");
        if (isset($values["nama_sub"]))
            $requete .= ($values["nama_sub"] == "") ? "" : ("AND UPPER(sub_akuns.nama_sub) like '%" . strtoupper($values["nama_sub"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(sub_akuns.no_sub) like '%" . strtoupper($values["search"]) . "%' OR UPPER(sub_akuns.nama_sub) like '%" . strtoupper($values["search"]) . "%') ");
        if (isset($values["status_sub"]))
            $requete .= ($values["status_sub"] == "") ? "" : ("AND sub_akuns.status ='" . $values["status_sub"] . "' ");
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
        $requete .= "WHERE sub_akuns.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND sub_akuns.company_id ='" . $values["company_id"] . "' ");
        if (isset($values["nama_sub"]))
            $requete .= ($values["nama_sub"] == "") ? "" : ("AND UPPER(sub_akuns.nama_sub) like '%" . strtoupper($values["nama_sub"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(sub_akuns.no_sub) like '%" . strtoupper($values["search"]) . "%' OR UPPER(sub_akuns.nama_sub) like '%" . strtoupper($values["search"]) . "%') ");
        if (isset($values["status_sub"]))
            $requete .= ($values["status_sub"] == "") ? "" : ("AND sub_akuns.status ='" . $values["status_sub"] . "' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }

    public function getAPAR($company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('sub_akuns');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResult();
    }
}
