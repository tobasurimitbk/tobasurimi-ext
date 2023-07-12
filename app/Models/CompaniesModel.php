<?php

namespace App\Models;

use CodeIgniter\Model;

class CompaniesModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'logo',
        'company',
        'holding_company',
        'address',
        'province_id',
        'city_id',
        'zip_code',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];


    public function get_by_in_id($id)
    {
        $requete = "SELECT * FROM companies WHERE deletedAt is null and id in (" . $id . ")";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }
}
