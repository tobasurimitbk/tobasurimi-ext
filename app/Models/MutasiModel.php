<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'mutasi';
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
            'tanggal'                             => 'mutasi.tanggal',
            'no_mutasi'                           => 'mutasi.no_mutasi',
            'divisi_asal_id'                      => 'mutasi.divisi_asal_id',
            'divisi_tujuan_id'                    => 'mutasi.divisi_tujuan_id',
            'warehouse_asal_id'                   => 'mutasi.warehouse_asal_id',
            'warehouse_tujuan_id'                 => 'mutasi.warehouse_tujuan_id',
            'ppbkb.no_ppbkb'                      => 'ppbkb.no_ppbkb',
            'status_posting'                      => 'mutasi.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'no_mutasi'] ?? 'no_mutasi';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            mutasi.*,
            tb_divisi_asal.divisi AS divisi_asal,
            tb_warehouse_asal.warehouse_name AS warehouse_name_asal,
            tb_divisi_tujuan.divisi AS divisi_tujuan,
            tb_warehouse_tujuan.warehouse_name AS warehouse_name_tujuan,
            ppbkb.no_ppbkb
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('divisis AS tb_divisi_asal', 'tb_divisi_asal.id = mutasi.divisi_asal_id', 'left')
            ->join('divisis AS tb_divisi_tujuan', 'tb_divisi_tujuan.id = mutasi.divisi_tujuan_id', 'left')
            ->join('warehouses AS tb_warehouse_asal', 'tb_warehouse_asal.id = mutasi.warehouse_asal_id', 'left')
            ->join('warehouses AS tb_warehouse_tujuan', 'tb_warehouse_tujuan.id = mutasi.warehouse_tujuan_id', 'left')
            ->join('ppbkb', 'ppbkb.mutasi_id = mutasi.id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('mutasi.tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('mutasi.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search']) {
            $dataQry->like('mutasi.no_mutasi', $addCondition['search'])
                ->orLike('tb_divisi_asal.divisi', $addCondition['search'])
                ->orLike('tb_divisi_tujuan.divisi', $addCondition['search'])
                ->orLike('tb_warehouse_asal.warehouse_name', $addCondition['search'])
                ->orLike('tb_warehouse_tujuan.warehouse_name', $addCondition['search'])
                ->orLike('ppbkb.no_ppbkb', $addCondition['search']);
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

    public function getMutasiList($divisiAsalId)
    {
        $ppbkbModel = new PPBKBModel();
        $warehousesModel = new WarehousesModel();

        $result = array();
        $resultMutasiPosted = $this->asArray()
            ->select('mutasi.*,divisis.divisi AS divisi_tujuan_name, 
            warehouses.warehouse_name AS warehouse_tujuan_name')
            ->join('divisis', 'divisis.id = mutasi.divisi_tujuan_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi.warehouse_tujuan_id', 'left')
            ->where('mutasi.divisi_asal_id', $divisiAsalId)
            ->where('status_posting', '1')
            ->where('tipe_mutasi', "PPBKB")
            ->where('mutasi.deletedAt', null)
            ->findAll();

        $result = array();
        for ($i = 0; $i < count($resultMutasiPosted); $i++) {
            $first = $ppbkbModel->where('mutasi_id', $resultMutasiPosted[$i]['id'])->first();
            if ($first == null) {
                $warehouse = $warehousesModel->find($resultMutasiPosted[$i]['warehouse_asal_id']);
                $resultMutasiPosted[$i]['warehouse_asal_name'] = $warehouse == null ? "" : $warehouse['warehouse_name'];
                array_push($result, $resultMutasiPosted[$i]);
            }
        }
        return $result;
    }



    public function get_no(
        $month,
        $year,
        $companyId,
        $tipe_mutasi
    ) {
        $romanMonth = romanMonthNumber((int)$month);
        // Tentukan template berdasarkan company
        if ($tipe_mutasi == "PPBKB") {
            // MUTASI PPBKB
            switch ($companyId) {
                case 1: // KIM 1 (FRZ)
                    $numberTemplate = "/F/PPBKB/$romanMonth/" . substr($year, -2);
                    break;
                case 2: // KIM 2
                    $numberTemplate = "/PPBKB/$romanMonth/" . substr($year, -2);
                    break;
                case 15: // GLOBAL
                    $numberTemplate = "/G/PPBKB/$romanMonth/" . substr($year, -2);
                    break;
                default: // OCS atau lainnya
                    $numberTemplate = "/PPBKB/$romanMonth/" . substr($year, -2);
                    break;
            }
        } else {
            // MUTASI LOKAL
            switch ($companyId) {
                case 1: // KIM 1 (FRZ)
                    $numberTemplate = "/F/MUT/$romanMonth/" . substr($year, -2);
                    break;
                case 2: // KIM 2
                    $numberTemplate = "/MUT/$romanMonth/" . substr($year, -2);
                    break;
                case 15: // GLOBAL
                    $numberTemplate = "/G/MUT/$romanMonth/" . substr($year, -2);
                    break;
                default: // OCS atau lainnya
                    $numberTemplate = "/MUT/$romanMonth/" . substr($year, -2);
                    break;
            }
        }


        // Cari nomor terakhir berdasarkan template
        $lastData = $this->asArray()
            ->select('no_mutasi')
            ->where('company_id', $companyId)
            ->like('no_mutasi', $numberTemplate, 'before')
            ->where('deletedAt', null)
            ->where('tipe_mutasi', $tipe_mutasi)
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
