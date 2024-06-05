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

    public function getList($condition, $conditionArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_mutasi'                           => 'no_mutasi',
            'tanggal'                             => 'tanggal',
            'warehouse_asal_id'                   => 'warehouse_asal_id',
            'warehouse_tujuan_id'                 => 'warehouse_tujuan_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "mutasi.*,
        divisis.divisi
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = mutasi.divisi_asal_id', 'left')
            ->where($condition)
            ->whereIn('divisi_asal_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['status'] || $addCondition['no_mutasi'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('divisi_asal_id', $addCondition['divisi_id']);
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['no_mutasi']) {
            $dataQry->like('no_mutasi', $addCondition['no_mutasi']);
        }

        if ($addCondition['divisi_id'] || $addCondition['status'] || $addCondition['no_mutasi'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
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



    public function get_no($bln, $thn, $last_day, $divisiName, $divisi_id)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('mutasi');
        $builder->select('no_mutasi');
        $builder->orderBy('no_mutasi', 'desc');
        $builder->where('mutasi.divisi_asal_id', $divisi_id);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_mutasi', $lastStr);
        $query = $builder->get();

        $kode = 'PPBKB/' . $divisiName;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_mutasi']);
                $number = intval($explode[2]);

                if ($number > $lastPenerimaan) {
                    $lastPenerimaan = $number;
                }
            }
            $lastPenerimaan++;
        }

        $formattedLastPenerimaan = sprintf("%02d", $lastPenerimaan);
        $generatedNo = $kode . '/' . $formattedLastPenerimaan . '/' . $lastStr;

        return $generatedNo;
    }
}
