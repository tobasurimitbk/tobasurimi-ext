<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'name',
        'username',
        'user_pass',
        'company_role',
        'current_company_id',
        'employee_id',
        'status',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function update_status($data)
    {
        $requete = "UPDATE user_ SET status='" . $data['status'] . "', date_update='" . $data['date_update'] . "', user_update='" . $data['user_update'] . "' where user_id='" . $data['id'] . "'";
        return $this->db->query($requete);
    }

    public function get_by_username($username)
    {
        $requete = "SELECT * FROM users ";
        $requete .= "WHERE deletedAt is null and username='" . $username . "' and users.status='Aktif' limit 1";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM users ";
        $requete .= "WHERE users.id='" . $id . "' ";
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT users.* FROM user_ ";
        $requete .= "WHERE deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(users.name) like '%" . strtoupper($values["name"]) . "%' ");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM users ";
        $requete .= "WHERE deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(users.name) like '%" . strtoupper($values["name"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
