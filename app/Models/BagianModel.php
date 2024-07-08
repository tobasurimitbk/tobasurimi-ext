<?php

namespace App\Models;

use CodeIgniter\Model;

class BagianModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bagian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'kode_bagian',
        'division_id',
        'nama_bagian',
        'deletedAt'
    ];

    // Dates
    protected $useTimestamps = false;
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_bagian'       => 'bagian.kode_bagian',
            'nama_bagian'       => 'bagian.nama_bagian',
            'id'                => 'bagian.id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'bagian.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $bagianQry = $this->asArray()->select('*')->where($condition)->orderBy($sort, $sortType);

        $totalData = $bagianQry->countAllResults(false);

        if ($addCondition['search']) {
            $bagianQry->groupStart();
        }

        if ($addCondition['search']) {
            $bagianQry->like('nama_bagian', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $bagianQry->orLike('kode_bagian', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $bagianQry->groupEnd();
        }

        $totalFilteredData = $bagianQry->countAllResults(false);
        $data = $bagianQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function get_by_company_id($company_id)
    {
        $requete = "SELECT * FROM bagian WHERE bagian.deletedAt is null and company_id='" . $company_id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }
}
