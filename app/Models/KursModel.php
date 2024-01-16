<?php

namespace App\Models;

use CodeIgniter\Model;

class KursModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'kurs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'metadata_id',
        'nilai_kurs',
        'start_date',
        'end_date',
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'valas'             => 'metadata.value',
            'nilai_kurs'        => 'kurs.nilai_kurs',
            'start_date'        => 'kurs.start_date',
            'end_date'          => 'kurs.end_date',
            'createdAt'         => 'kurs.createdAt',
            'updatedAt'         => 'kurs.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'kurs.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "kurs.*, metadata.value as valas";
        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'metadata.id = kurs.metadata_id', 'LEFT')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry->like('metadata.value', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('kurs.start_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $dataQry->where('kurs.end_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getById($id)
    {
        $selectQry = "kurs.*";

        $data = $this->asObject()
            ->select($selectQry)
            ->find($id);

        return $data;
    }

    public function check_current($id, $metadata_id, $end_date)
    {
        $selectQry = "kurs.*";

        if ($id) {
            $data = $this->select($selectQry)
                ->where('id !=', $id)
                ->where('metadata_id', $metadata_id)
                ->where('end_date >=', $end_date)
                ->where('deletedAt', NULL)
                ->countAllResults();
        } else {
            $data = $this->select($selectQry)
                ->where('metadata_id', $metadata_id)
                ->where('end_date >=', $end_date)
                ->where('deletedAt', NULL)
                ->countAllResults();
        }

        return $data;
    }

    public function getByMetaId($id, $date)
    {
        $selectQry = "kurs.*";

        $data = $this->asObject()
            ->select($selectQry)
            ->where('start_date <=', $date)
            ->where('end_date >=', $date)
            ->where('metadata_id', $id)
            ->where('deletedAt', NULL)
            ->first();

        return $data;
    }
}
