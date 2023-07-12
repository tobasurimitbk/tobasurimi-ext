<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'name',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_in_id($id)
    {
        $requete = "SELECT * FROM roles WHERE deletedAt is null and id in (" . $id . ")";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }


    public function get_by_id($id)
    {
        $requete = "SELECT * FROM roles WHERE deletedAt is null and id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }
}
