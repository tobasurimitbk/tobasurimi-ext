<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table = 'vendors';
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
        // 'supplier_buyer',
        'postal_code',
        // 'ap_id',
        // 'ar_id',

        // ===== Tambahan Upah =====
        'upah_vendor',
        'upah_jb',
        'upah_xl',
        'upah_lp',
        'upah_cl',

        // ===== Tambahan Komisi =====
        'komisi_vendor',

        // ===== Tambahan Bonus =====
        'bonus_vendor',
        'bonus_jb',
        'bonus_xl',
        'bonus_lp',
        'bonus_cl',

        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT vendors.* FROM vendors ";
        // $requete = "SELECT vendors.*,ap.nama_sub as ap_name, ar.nama_sub as ar_name FROM vendors ";
        // $requete .= "LEFT JOIN sub_akuns ap ON (ap.id=vendors.ap_id) ";
        // $requete .= "LEFT JOIN sub_akuns ar ON (ar.id=vendors.ar_id) ";
        $requete .= "WHERE vendors.deletedAt is null and vendors.id='" . $id . "'";

        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT vendors.*,provinces.province_name,cities.city_name FROM vendors ";
        // $requete = "SELECT vendors.*,provinces.province_name,cities.city_name,s1.nama_sub as ap_name,s2.nama_sub as ar_name FROM vendors ";
        $requete .= "LEFT JOIN provinces ON (vendors.province_id=provinces.id) ";
        $requete .= "LEFT JOIN cities ON (vendors.city_id=cities.id) ";
        // $requete .= "LEFT JOIN sub_akuns s1 ON (vendors.ap_id=s1.id) ";
        // $requete .= "LEFT JOIN sub_akuns s2 ON (vendors.ar_id=s2.id) ";
        $requete .= "WHERE vendors.deletedAt is null ";
        if (isset($values['company_id']))
            $requete .= "AND company_id=" . $values['company_id'] . " ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(vendors.name) like '%" . strtoupper($values["name"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(vendors.kode) like '%" . strtoupper($values["search"]) . "%' OR UPPER(vendors.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(vendors.address) like '%" . strtoupper($values["search"]) . "%') ");

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
        $requete  = "SELECT count(*) as total FROM vendors ";
        $requete .= "WHERE vendors.deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(vendors.name) like '%" . strtoupper($values["name"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(vendors.kode) like '%" . strtoupper($values["search"]) . "%' OR UPPER(vendors.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(vendors.address) like '%" . strtoupper($values["search"]) . "%') ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
