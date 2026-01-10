<?php

namespace App\Models;

use CodeIgniter\Model;

class BanksModel extends Model
{
    protected $table = 'banks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'kode_bank',
        'name',
        'pay_code',
        'atas_nama',
        'no_rekening'
    ];

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_bank' => 'kode_bank',
            'pay_code' => 'pay_code',
            'name' => 'name',
            'atas_nama' => 'atas_nama',
            'no_rekening' => 'no_rekening'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            banks.*
        ";

        $DataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $DataQry->countAllResults(false);

        if ($addCondition['search']) {
            $DataQry->groupStart();
        }

        if ($addCondition['search']) {
            $DataQry
                ->like('kode_bank', $addCondition['search'])
                ->orLike('name', $addCondition['search'])
                ->orLike('atas_nama', $addCondition['search'])
                ->orLike('no_rekening', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $DataQry->groupEnd();
        }
        $totalFilteredData = $DataQry->countAllResults(false);
        $data = $DataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM banks WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT * FROM banks ";
        $requete .= "WHERE 1 ";
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
        $requete  = "SELECT count(*) as total FROM banks ";
        $requete .= "WHERE 1 ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(name) like '%" . strtoupper($values["name"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
