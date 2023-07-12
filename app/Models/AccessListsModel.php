<?php

namespace App\Models;

use CodeIgniter\Model;

class AccessListsModel extends Model
{
    protected $table = 'access_lists';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'role_id',
        'company_id',
        'menu_url_id',
        'action',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];


    public function get_by_role_id_and_company_id_join_menu_url_parent($role_id, $company_id)
    {
        $requete = "SELECT menu_url_id,icon,menu_urls.name as menuName, url, parent_id,access_lists.action FROM access_lists ";
        $requete .= "INNER JOIN menu_urls ON (menu_urls.id=access_lists.menu_url_id) ";
        $requete .= "WHERE access_lists.deletedAt is null ";
        $requete .= "and menu_urls.deletedAt is null ";
        $requete .= "and access_lists.role_id='" . $role_id . "' ";
        $requete .= "and access_lists.company_id='" . $company_id . "' ";
        $requete .= "and menu_urls.parent_id='0' ";
        $requete .= "order by menu_urls.sort_no";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_role_id_and_company_id_join_menu_url_not_parent($role_id, $company_id)
    {
        $requete = "SELECT menu_url_id,icon,menu_urls.name as menuName, url, parent_id,access_lists.action FROM access_lists ";
        $requete .= "INNER JOIN menu_urls ON (menu_urls.id=access_lists.menu_url_id) ";
        $requete .= "WHERE access_lists.deletedAt is null ";
        $requete .= "and menu_urls.deletedAt is null ";
        $requete .= "and access_lists.role_id='" . $role_id . "' ";
        $requete .= "and access_lists.company_id='" . $company_id . "' ";
        $requete .= "and menu_urls.parent_id!='0' ";
        $requete .= "order by menu_urls.sort_no";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }
}
