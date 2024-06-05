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

    public function getList($condition, $conditionArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_mutasi'                           => 'no_mutasi',
            'tanggal'                             => 'tanggal',
            'warehouse_asal_id'                   => 'warehouse_asal_id',
            'company_tujuan_id'                   => 'company_tujuan_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "mutasi_global.*,
        divisis.divisi,
        companies.company AS company_tujuan_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('companies', 'companies.id = mutasi_global.company_tujuan_id', 'left')
            ->where($condition)
            ->whereIn('divisi_asal_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['company_tujuan_id'] || $addCondition['divisi_id'] || $addCondition['status'] || $addCondition['no_mutasi'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
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

        if ($addCondition['company_tujuan_id']) {
            $dataQry->where('company_tujuan_id', $addCondition['company_tujuan_id']);
        }

        if ($addCondition['no_mutasi']) {
            $dataQry->like('no_mutasi', $addCondition['no_mutasi']);
        }

        if ($addCondition['company_tujuan_id'] || $addCondition['divisi_id'] || $addCondition['status'] || $addCondition['no_mutasi'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
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

    public function get_no($bln, $thn, $last_day, $divisiName, $divisi_id)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('mutasi_global');
        $builder->select('no_mutasi');
        $builder->orderBy('no_mutasi', 'desc');
        $builder->where('mutasi_global.divisi_asal_id', $divisi_id);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_mutasi', $lastStr);
        $query = $builder->get();

        $kode = 'BC27/' . $divisiName;

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
