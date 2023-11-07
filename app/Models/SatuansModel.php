<?php

namespace App\Models;

use CodeIgniter\Model;

class SatuansModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'satuans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'kode_satuan',
        'nama_satuan',
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


    public function get_by_id($id)
    {
        $requete = "SELECT * FROM satuans WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_company_id($company_id)
    {
        $requete = "SELECT * FROM satuans WHERE satuans.deletedAt is null";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function getSatuanAll()
    {
        $requete = "SELECT * FROM satuans WHERE satuans.deletedAt is null";
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT satuans.* FROM satuans ";
        $requete .= "WHERE satuans.deletedAt is null ";
        if (isset($values["nama_satuan"]))
            $requete .= ($values["nama_satuan"] == "") ? "" : ("AND UPPER(satuans.nama_satuan) like '%" . strtoupper($values["nama_satuan"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(satuans.nama_satuan) like '%" . strtoupper($values["search"]) . "%' OR UPPER(satuans.kode_satuan) like '%" . strtoupper($values["search"]) . "%') ");


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
        $requete  = "SELECT count(*) as total FROM satuans ";
        $requete .= "WHERE satuans.deletedAt is null ";
        if (isset($values["nama_satuan"]))
            $requete .= ($values["nama_satuan"] == "") ? "" : ("AND UPPER(satuans.nama_satuan) like '%" . strtoupper($values["nama_satuan"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(satuans.nama_satuan) like '%" . strtoupper($values["search"]) . "%' OR UPPER(satuans.kode_satuan) like '%" . strtoupper($values["search"]) . "%') ");


        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
