<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehousesModel extends Model
{
    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'divisi_id',
        'kawasan_id',
        'code_warehouse',
        'warehouse_name',
        'address',
        'province_id',
        'city_id',
        'zip_code',
        'phone',
        'email',
        'pic_id',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM warehouses WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_company_id($company_id)
    {
        $requete = "SELECT * FROM warehouses WHERE warehouses.deletedAt is null and divisi_id is not null and company_id='" . $company_id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_divisi_id($company_id, $divisi_id)
    {
        $requete = "SELECT * FROM warehouses WHERE warehouses.deletedAt is null and divisi_id='" . $divisi_id . "' and company_id='" . $company_id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT warehouses.*,kawasan.name AS kawasan_name,provinces.province_name,cities.city_name,employees.name as pic_name, divisis.divisi AS divisi_name FROM warehouses ";
        $requete .= "LEFT JOIN provinces ON (warehouses.province_id=provinces.id) ";
        $requete .= "LEFT JOIN cities ON (warehouses.city_id=cities.id) ";
        $requete .= "LEFT JOIN employees ON (warehouses.pic_id=employees.id) ";
        $requete .= "LEFT JOIN divisis ON (warehouses.divisi_id=divisis.id) ";
        $requete .= "LEFT JOIN kawasan ON (warehouses.kawasan_id=kawasan.id) ";
        $requete .= "WHERE warehouses.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND warehouses.company_id ='" . $values["company_id"] . "' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(code_warehouse) like '%" . strtoupper($values["search"]) . "%' OR UPPER(warehouse_name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(warehouses.address) like '%" . strtoupper($values["search"]) . "%' OR UPPER(warehouses.phone) like '%" . strtoupper($values["search"]) . "%')");
        if (isset($values["divisi_id"]))
            $requete .= ($values["divisi_id"] == "") ? "" : ("AND warehouses.divisi_id ='" . $values["divisi_id"] . "' ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM warehouses ";
        $requete .= "WHERE warehouses.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND warehouses.company_id ='" . $values["company_id"] . "' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(code_warehouse) like '%" . strtoupper($values["search"]) . "%' OR UPPER(warehouse_name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(warehouses.address) like '%" . strtoupper($values["search"]) . "%' OR UPPER(warehouses.phone) like '%" . strtoupper($values["search"]) . "%')");
        if (isset($values["divisi_id"]))
            $requete .= ($values["divisi_id"] == "") ? "" : ("AND warehouses.divisi_id ='" . $values["divisi_id"] . "' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
