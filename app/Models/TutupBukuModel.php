<?php

namespace App\Models;

use CodeIgniter\Model;

class TutupBukuModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tutup_buku';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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
            'bulan'              => 'tutup_buku.bulan',
        ];
        $availableSortType = ['asc' => 'ASC=', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'id'] ?? 'bulan';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "tutup_buku.*, divisis.divisi";
        $tutupBukuDataQry = $this->asObject()
            ->select($selectQry)
            ->join("divisis", "divisis.id = tutup_buku.divisi_id")
            ->where("tutup_buku.deletedAt", NULL)
            ->where("divisis.deletedAt", NULL)
            ->orderBy($sort, $sortType);

        $totalData = $tutupBukuDataQry->countAllResults(false);

        // if ($addCondition['search']) {
        //     $tutupBukuDataQry->groupStart();
        // }

        // if ($addCondition['search']) {
        //     $tutupBukuDataQry->like('suppliers.name', $addCondition['search']);
        // }

        // if ($addCondition['search']) {
        //     $tutupBukuDataQry->groupEnd();
        // }

        $totalFilteredData = $tutupBukuDataQry->countAllResults(false);
        $data = $tutupBukuDataQry->findAll($limit, $offset);

        // var_dump($data);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }
}
