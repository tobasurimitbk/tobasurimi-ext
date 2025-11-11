<?php

namespace App\Models;

use CodeIgniter\Model;

class JamKerjaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jam_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [
        'jenis',
        'jam_terlambat',
        'company_id',
        'lintas_hari'
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
            'id'    => 'jam_kerja.id',
            'divisi_id' => 'jam_kerja.divisi_id',
            'jenis'    => 'jam_kerja.jenis',
            'shift' => 'jam_kerja.shift',
            'jam_terlambat' => 'jam_kerja.jam_terlambat',
            'lintas_hari' => 'jam_kerja.lintas_hari'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'id'] ?? 'jam_kerja.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $dataQry = $this->asObject()
            ->select('jam_kerja.*,divisis.divisi')
            ->join('divisis', 'divisis.id = jam_kerja.divisi_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id']) {
            $dataQry->where('divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['search']) {
            $dataQry->groupStart();
            $dataQry->like('jam_kerja.jenis', $addCondition['search'])
                ->orLike('jam_kerja.shift', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search']);
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }
}
