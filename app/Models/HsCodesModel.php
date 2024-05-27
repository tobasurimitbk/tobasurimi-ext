<?php

namespace App\Models;

use CodeIgniter\Model;

class HsCodesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'hs_codes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'komoditi',
        'code',
        'uraian_barang',
        'unit',
        'nilai_tarif',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    // Dates
    protected $useTimestamps = true;
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
        $requete = "SELECT * FROM hs_codes WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'komoditi'          => 'hs_codes.komoditi',
            'code'              => 'hs_codes.code',
            'uraian_barang'     => 'hs_codes.uraian_barang',
            'kode_satuan'       => 'satuans.kode_satuan',
            'nama_satuan'       => 'satuans.nama_satuan',
            'createdAt'         => 'hs_codes.createdAt',
            'updatedAt'         => 'hs_codes.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'hs_codes.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "hs_codes.*, 
                      satuans.kode_satuan, satuans.nama_satuan";
        $hsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('satuans', 'hs_codes.unit = satuans.id', 'left')
            // ->groupBy(('customers.id'))
            ->orderBy($sort, $sortType);

        $totalData = $hsDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $hsDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $hsDataQry->like('uraian_barang', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $hsDataQry->groupEnd();
        }

        $totalFilteredData = $hsDataQry->countAllResults(false);
        $data = $hsDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    // public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    // {
    //     $requete = "SELECT * FROM hs_codes ";
    //     $requete .= "WHERE hs_codes.deletedAt is null ";
    //     if (isset($values["search"]))
    //         $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(komoditi) like '%" . strtoupper($values["search"]) . "%' OR UPPER(code) like '%" . strtoupper($values["search"]) . "%' OR UPPER(uraian_barang) like '%" . strtoupper($values["search"]) . "%')");

    //     if ($sortby != '')
    //         $requete .= "ORDER BY $sortby ";
    //     if ($limit >= 0)
    //         $requete .= "LIMIT $limit OFFSET $offset";

    //     $query = $this->db->query($requete);
    //     return $query->getResultArray();
    // }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM hs_codes ";
        $requete .= "WHERE hs_codes.deletedAt is null ";
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(komoditi) like '%" . strtoupper($values["search"]) . "%' OR UPPER(code) like '%" . strtoupper($values["search"]) . "%' OR UPPER(uraian_barang) like '%" . strtoupper($values["search"]) . "%')");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }
}
