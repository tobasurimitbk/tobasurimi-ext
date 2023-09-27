<?php

namespace App\Models;

use CodeIgniter\Model;

class DivisisModel extends Model
{
    protected $table = 'divisis';
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
        'jam_kerja_id',
        'divisi',
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
        $requete = "SELECT * FROM divisis WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_company_id($company_id)
    {
        $requete = "SELECT * FROM divisis WHERE divisis.deletedAt is null and company_id='" . $company_id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT divisis.*, jam_kerja.jenis FROM divisis ";
        $requete .= "LEFT JOIN jam_kerja ON jam_kerja.id = divisis.jam_kerja_id ";
        $requete .= "WHERE divisis.deletedAt IS NULL ";

        if (isset($values["company_id"]) && $values["company_id"] !== "") {
            $requete .= "AND divisis.company_id = '" . $values["company_id"] . "' ";
        }

        if (isset($values["divisi"]) && $values["divisi"] !== "") {
            $requete .= "AND UPPER(divisis.divisi) LIKE '%" . strtoupper($values["divisi"]) . "%' ";
        }

        if (isset($values["search"]) && $values["search"] !== "") {
            $requete .= "AND (UPPER(divisis.divisi) LIKE '%" . strtoupper($values["search"]) . "%') ";
        }

        if ($sortby !== '') {
            $requete .= "ORDER BY $sortby ";
        }

        if ($limit >= 0) {
            $requete .= "LIMIT $limit OFFSET $offset";
        }

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM divisis ";
        $requete .= "WHERE divisis.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND divisis.company_id ='" . $values["company_id"] . "' ");
        if (isset($values["divisi"]))
            $requete .= ($values["divisi"] == "") ? "" : ("AND UPPER(divisis.divisi) like '%" . strtoupper($values["divisi"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(divisis.divisi) like '%" . strtoupper($values["search"]) . "%') ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }

    public function getTunjanganByDivisi($divisionID)
    {
        $select = "
            tunjangan.*, gaji_divisi.nominal
        ";
        return $this->asObject()
            ->select($select)
            ->join('gaji_divisi', 'gaji_divisi.division_id = divisis.id', 'INNER')
            ->join('tunjangan', 'tunjangan.id = gaji_divisi.tunjangan_id', 'INNER')
            ->where('gaji_divisi.division_id', $divisionID)
            ->where('tunjangan.deletedAt', null)
            ->findAll();
    }
}
