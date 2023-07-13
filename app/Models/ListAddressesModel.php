<?php

namespace App\Models;

use CodeIgniter\Model;

class ListAddressesModel extends Model
{
    protected $table = 'list_addresses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'customer_id',
        'supplier_id',
        'vendor_id',
        'address',
        'province_id',
        'city_id',
        'main_address',
        'postal_code',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM list_addresses WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function  get_by_customer_id($customer_id, $type = '')
    {
        $requete = "SELECT list_addresses.*,provinces.province_name,cities.city_name FROM list_addresses ";
        $requete .= "LEFT JOIN provinces ON (provinces.id=list_addresses.province_id) ";
        $requete .= "LEFT JOIN cities ON (cities.id=list_addresses.city_id) ";
        $requete .= "WHERE deletedAt is null and customer_id='" . $customer_id . "' ";
        $query = $this->db->query($requete);
        if ($type == '1')
            return $query->getResultObject();
        else
            return $query->getResultArray();
    }

    public function  get_by_vendor_id($vendor_id, $type = '')
    {
        $requete = "SELECT list_addresses.*,provinces.province_name,cities.city_name FROM list_addresses ";
        $requete .= "LEFT JOIN provinces ON (provinces.id=list_addresses.province_id) ";
        $requete .= "LEFT JOIN cities ON (cities.id=list_addresses.city_id) ";
        $requete .= "WHERE deletedAt is null and vendor_id='" . $vendor_id . "' ";
        $query = $this->db->query($requete);
        if ($type == '1')
            return $query->getResultObject();
        else
            return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT * FROM list_addresses ";
        $requete .= "WHERE deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(name) like '%" . strtoupper($values["name"]) . "%' ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM list_addresses ";
        $requete .= "WHERE deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(name) like '%" . strtoupper($values["name"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
