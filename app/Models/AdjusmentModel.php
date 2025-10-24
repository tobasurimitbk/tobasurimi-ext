<?php

namespace App\Models;

use CodeIgniter\Model;

class AdjusmentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'adjusment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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
            'adjusment.divisi_id' => 'adjusment.divisi_id',
            'adjusment.no_adjusment' => 'adjusment.no_adjusment',
            'adjusment.tanggal' => 'adjusment.tanggal',
            'adjusment.keterangan' => 'adjusment.keterangan',
            'adjusment.tipe_adjusment' => 'adjusment.tipe_adjusment',
            'adjusment.createdBy' => 'adjusment.createdAt',
            'adjusment.status_posting' => 'adjusment.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'adjusment.no_adjusment'] ?? 'adjusment.no_adjusment';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "adjusment.*,
            divisis.divisi,
            users.name AS user_name,
            metadata.value AS tipe_adjusment_text";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'adjusment.divisi_id = divisis.id', 'left')
            ->join('users', 'users.id = adjusment.createdBy', 'left')
            ->join('metadata', 'metadata.id = adjusment.tipe_adjusment')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if (
            $addCondition['divisi_id'] ||
            $addCondition['tipe_adjusment'] ||
            $addCondition['search'] ||
            $addCondition['dateStart'] ||
            $addCondition['dateEnd']
        ) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('adjusment.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['tipe_adjusment']) {
            $dataQry->where('adjusment.tipe_adjusment', $addCondition['tipe_adjusment']);
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('adjusment.tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('adjusment.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search']) {
            $dataQry->like('users.name', $addCondition['search'])
                ->orLike('adjusment.no_adjusment', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search']);
        }

        if (
            $addCondition['divisi_id'] ||
            $addCondition['tipe_adjusment'] ||
            $addCondition['search'] ||
            $addCondition['dateStart'] ||
            $addCondition['dateEnd']
        ) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }


    public function get_no(
        $month,
        $year,
        $companyId
    ) {
        $romanMonth = romanMonthNumber((int)$month);
        // Tentukan template berdasarkan company
        switch ($companyId) {
            case 1: // KIM 1 (FRZ)
                $numberTemplate = "/F/ADJ/$romanMonth/" . substr($year, -2);
                break;
            case 2: // KIM 2
                $numberTemplate = "/ADJ/$romanMonth/" . substr($year, -2);
                break;
            case 15: // GLOBAL
                $numberTemplate = "/G/ADJ/$romanMonth/" . substr($year, -2);
                break;
            default: // OCS atau lainnya
                $numberTemplate = "/ADJ/$romanMonth/" . substr($year, -2);
                break;
        }

        // Cari nomor terakhir berdasarkan template
        $lastData = $this->asArray()
            ->select('no_adjusment')
            ->where('company_id', $companyId)
            ->like('no_adjusment', $numberTemplate, 'before')
            ->where('deletedAt', null)
            ->orderBy('no_adjusment', 'DESC')
            ->first();

        // Nomor awal default
        $invNumber = '001' . $numberTemplate;

        if ($lastData && !empty($lastData['no_adjusment'])) {
            // Ambil angka urutan terakhir
            $parts = explode('/', $lastData['no_adjusment']);
            $lastIncrement = isset($parts[0]) ? (int)$parts[0] : 0;
            $newIncrement = $lastIncrement + 1;
            $paddedNumber = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }
        return $invNumber;
    }
}
