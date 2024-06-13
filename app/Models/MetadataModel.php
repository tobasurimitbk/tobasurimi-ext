<?php

namespace App\Models;

use CodeIgniter\Model;

class MetadataModel extends Model
{
    protected $table = 'metadata';
    protected $primaryKey = 'id';
    protected $useSoftDeletes   = true;
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

    // Dates
    protected $useTimestamps = false;
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
        $requete = "SELECT * FROM metadata WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_name($name)
    {
        $requete = "SELECT * FROM metadata WHERE name='" . $name . "' ORDER BY value ASC";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_name_bc($name)
    {
        $requete = "SELECT * FROM metadata WHERE name='" . $name . "' ORDER BY id ASC";
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

        $requete .= "AND deletedAt IS NULL ";
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

    public function getByName(string $name): array
    {
        return $this->asObject()
            ->where('name', $name)->findAll();
    }

    public function getBCUsed($po_used)
    {
        return $this->asArray()->where('name', 'jenis_dok_aju')->like('description', '%' . $po_used . '%')->findAll();
    }

    public function getBCFirst($bcName)
    {
        return $this->asArray()->where('name', 'jenis_dok_aju')->where('value', $bcName)->first();
    }

    public function getKodeSatuanBarang($search)
    {
        $data = [];
        foreach ($this->asArray()->where('name', "Kode Satuan BC")->like('value', '%' . $search . '%')->orderBy('value', "ASC")->limit(10)->get()
            ->getResultArray() as $d) {
            $data[] = [
                'id' => encrypt($d['value']),
                'text' => '' . $d['value'] . ' - ' . strtoupper($d['description'])
            ];
        }
        return $data;
    }

    public function bcMetaDataHelper($name, $value = null, $description = null)
    {
        if ($value == null) {
            return $this->where('name', $name)->where('description', $description)->first();
        } else {
            return $this->where('name', $name)->where('value', $value)->first();
        }
    }
}
