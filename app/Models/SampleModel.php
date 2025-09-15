<?php

namespace App\Models;

use CodeIgniter\Model;

class SampleModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sample';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
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
            'no_sample'             => 'sample.no_sample',
            'divisi_id'             => 'sample.divisi_id',
            'tanggal'               => 'sample.tanggal',
            'delivery'              => 'sample.delivery',
            'attn'                  => 'sample.attn_no',
            'total_berat_kotor'           => 'sample.total_berat_kotor',
            'total_berat_bersih'          => 'sample.total_berat_bersih',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sample.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sample.*, 
                      divisis.divisi";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = sample.divisi_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $salesDataQry
                ->groupStart()
                ->like('sample.sample_no', $addCondition['search'])
                ->orLike('sample.delivery', $addCondition['search'])
                ->orLike('sample.attn_no', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->groupEnd();
        }

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $salesDataQry->groupStart(); //
            if (!empty($addCondition['dateStart'])) {
                $salesDataQry->where('DATE(sample.tanggal) >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $salesDataQry->where('DATE(sample.tanggal) <=', $addCondition['dateEnd']);
            }
            $salesDataQry->groupEnd();
        }


        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }
}
