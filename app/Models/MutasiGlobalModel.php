<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiGlobalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'mutasi_global';
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
            'tanggal'                             => 'mutasi_global.tanggal',
            'no_mutasi'                           => 'mutasi_global.no_mutasi',
            'divisi_asal_id'                      => 'mutasi_global.divisi_asal_id',
            'warehouse_asal_id'                   => 'mutasi_global.warehouse_asal_id',
            'warehouse_tujuan_id'                 => 'mutasi_global.warehouse_tujuan_id',
            'company_tujuan_id'                   => 'mutasi_global.company_tujuan_id',
            'bc_27.no_aju'                        => 'bc_27.no_aju',
            'status_posting'                      => 'mutasi_global.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'no_mutasi'] ?? 'no_mutasi';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "mutasi_global.*,
            divisis.divisi,
            warehouses.warehouse_name,
            companies.company AS company_tujuan_name,
            bc_27.no_aju,
            bc_27.no_daftar
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('companies', 'companies.id = mutasi_global.company_tujuan_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id', 'left')
            ->join('bc_27', 'bc_27.mutasi_global_id = mutasi_global.id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('mutasi_global.tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('mutasi_global.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search']) {
            $dataQry->like('mutasi_global.no_mutasi', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->orLike('bc_27.no_aju', $addCondition['search'])
                ->orLike('bc_27.no_daftar', $addCondition['search'])
                ->orLike('companies.company', $addCondition['search']);
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
        ];
    }

    public function getMutasiGlobalList($companyTujuanId, $companyAsalId)
    {
        $bc27Model = new BC27Model();
        $result = array();
        $resultMutasiPosted = $this->asArray()
            ->select('mutasi_global.*,divisis.divisi AS divisiName, warehouses.warehouse_name AS warehouseName')
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id', 'left')
            ->where('company_tujuan_id', $companyTujuanId)
            ->where('company_asal_id', $companyAsalId)
            ->where('status_posting', '1')
            ->where('mutasi_global.deletedAt', null)
            ->findAll();

        foreach ($resultMutasiPosted as $r) {
            $first = $bc27Model->where('mutasi_global_id', $r['id'])->first();
            if ($first == null) {
                array_push($result, $r);
            }
        }

        return $result;
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
                $numberTemplate = "/F/BC 2.7/$romanMonth/" . substr($year, -2);
                break;
            case 2: // KIM 2
                $numberTemplate = "/BC 2.7/$romanMonth/" . substr($year, -2);
                break;
            case 15: // GLOBAL
                $numberTemplate = "/G/BC 2.7/$romanMonth/" . substr($year, -2);
                break;
            default: // OCS atau lainnya
                $numberTemplate = "/BC 2.7/$romanMonth/" . substr($year, -2);
                break;
        }

        // Cari nomor terakhir berdasarkan template
        $lastData = $this->asArray()
            ->select('no_mutasi')
            ->where('mutasi_global.company_asal_id', $companyId)
            ->like('no_mutasi', $numberTemplate, 'before')
            ->where('deletedAt', null)
            ->orderBy('no_mutasi', 'DESC')
            ->first();

        // Nomor awal default
        $invNumber = '001' . $numberTemplate;

        if ($lastData && !empty($lastData['no_mutasi'])) {
            // Ambil angka urutan terakhir
            $parts = explode('/', $lastData['no_mutasi']);
            $lastIncrement = isset($parts[0]) ? (int)$parts[0] : 0;
            $newIncrement = $lastIncrement + 1;
            $paddedNumber = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }
        return $invNumber;
    }
}
