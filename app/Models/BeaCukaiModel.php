<?php

namespace App\Models;

use CodeIgniter\Model;

class BeaCukaiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bea_cukai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'user_id',
        'tipe_bahan',
        'status_po',
        'no_bea_cukai',
        'multiple_po_id',
        'multiple_po_no',
        'po_id',
        'po_no',
        'aju_document_type',
        'aju_no',
        'validation_date',
        'shipping_cost',
        'bea_masuk',
        'ppn',
        'pph',
        'invoice_no',
        'status_post'
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
            'no_bea_cukai'         => 'no_bea_cukai',
            'tipe_bahan'           => 'tipe_bahan',
            'status_po'            => 'status_po',
            'createdAt'            => 'createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bea_cukai.*";
        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->groupBy(('id'))
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['status']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry->like('no_bea_cukai', $addCondition['search']);
        }

        if ($addCondition['status']) {
            $dataQry->where('status_post', $addCondition['status']);
        }

        if ($addCondition['search'] || $addCondition['status']) {
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

    public function getNoPOBeaCukai($status_po, $tipe_bahan, $company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'company_id' => $company_id,
            'status_po' => $status_po,
            'tipe_bahan' => $tipe_bahan,
        ];

        $builder = $this->db->table('bea_cukai')
        ->select('bea_cukai.*');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function checkPostingBeaCukai($company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'company_id' => $company_id,
            'status_post' => 'FINISH'
        ];

        $builder = $this->db->table('bea_cukai')
        ->select('bea_cukai.*');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function get_no($tgl, $bln, $thn, $thn2, $last_day)
    {
        $lastStr =  $tgl . $bln . $thn;

        $builder = $this->db->table('bea_cukai');
        $builder->select('no_bea_cukai');
        $builder->orderBy('no_bea_cukai', 'desc')
            ->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_bea_cukai', $lastStr);
        $query = $builder->get();

        $last = '01';
        if ($query->getResultArray()) {
            $lastFirst = explode('/', $query->getResultArray()[0]['no_bea_cukai']);
            $last = explode('-', $lastFirst[0]);
            $last = intval($last[1]) + 1;
            $last = sprintf("%02d", $last);
        };

        $generatedNo =  $lastStr . '-' . $last . '/TOBA/' . $thn2;

        return $generatedNo;
    }
}