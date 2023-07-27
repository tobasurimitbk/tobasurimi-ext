<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'kode',
        'name',
        'address',
        'province_id',
        'city_id',
        'no_npwp',
        'phone',
        'contact_person',
        'email',
        'no_rekening',
        'bank_id',
        'nama_rekening',
        'supplier_buyer',
        'postal_code',
        'ap_id',
        'ar_id',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT customers.*,ap.nama_sub as ap_name, ar.nama_sub as ar_name FROM customers ";
        $requete .= "LEFT JOIN sub_akuns ap ON (ap.id=customers.ap_id) ";
        $requete .= "LEFT JOIN sub_akuns ar ON (ar.id=customers.ar_id) ";
        $requete .= "WHERE customers.deletedAt is null and customers.id='" . $id . "'";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT customers.*,provinces.province_name,cities.city_name,s1.nama_sub as ap_name,s2.nama_sub as ar_name FROM customers ";
        $requete .= "LEFT JOIN provinces ON (customers.province_id=provinces.id) ";
        $requete .= "LEFT JOIN cities ON (customers.city_id=cities.id) ";
        $requete .= "LEFT JOIN sub_akuns s1 ON (customers.ap_id=s1.id) ";
        $requete .= "LEFT JOIN sub_akuns s2 ON (customers.ar_id=s2.id) ";
        $requete .= "WHERE customers.deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(customers.name) like '%" . strtoupper($values["name"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(customers.kode) like '%" . strtoupper($values["search"]) . "%' OR UPPER(customers.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(customers.address) like '%" . strtoupper($values["search"]) . "%') ");

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
        $requete  = "SELECT count(*) as total FROM customers ";
        $requete .= "WHERE customers.deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(customers.name) like '%" . strtoupper($values["name"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(customers.kode) like '%" . strtoupper($values["search"]) . "%' OR UPPER(customers.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(customers.address) like '%" . strtoupper($values["search"]) . "%') ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }

    public function getCustomer()
    {
        $arrCondition = [
            'deletedAt' => null
        ];

        $builder = $this->db->table('customers');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}
