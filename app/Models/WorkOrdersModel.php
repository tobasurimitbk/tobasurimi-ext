<?php

namespace App\Models;

use CodeIgniter\I18n\Time;

use CodeIgniter\Model;

class WorkOrdersModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'work_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'wo_no',
        'barang_id',
        'production_amt',
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

    public function getWorkOrderList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'wo_no'             => 'work_orders.wo_no',
            'kode_barang'       => 'barangs.kode_barang',
            'nama_barang'       => 'barangs.nama_barang',
            'production_amt'    => 'work_orders.production_amt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'work_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "work_orders.*,
            barangs.nama_barang,
            barangs.kode_barang
        ";

        $workOrdersDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barangs', 'barangs.id = work_orders.barang_id')
            ->orderBy($sort, $sortType);

        $totalData = $workOrdersDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $workOrdersDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $workOrdersDataQry
                ->like('work_orders.wo_no', $addCondition['search'])
                ->orLike('barangs.kode_barang', $addCondition['search'])
                ->orLike('barangs.nama_barang', $addCondition['search']);
        }
        if ($addCondition['search']) {
            $workOrdersDataQry->groupEnd();
        }

        $totalFilteredData = $workOrdersDataQry->countAllResults(false);
        $data = $workOrdersDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function get_no()
    {
        $romanNumb = [
            'I',
            'II',
            'III',
            'IV',
            'V',
            'VI',
            'VII',
            'VIII',
            'IX',
            'X',
            'XI',
            'XII',
        ];

        $today = Time::today('America/Chicago', 'en_US');

        $year = $today->getYear();
        $month = $today->getMonth() - 1;

        $lastStr =  $romanNumb[$month] . '/' . $year;

        $builder = $this->db->table('work_orders');
        $builder->select('*');
        $builder->orderBy('wo_no', 'desc');
        $builder->like('wo_no', $lastStr);
        $query = $builder->get();

        $kode = 'PRD';

        $lastWO = '0001';
        if ($query->getResultArray()) {
            $lastWO = explode('/', $query->getResultArray()[0]['wo_no']);
            $lastWO = intval($lastWO[0]) + 1;

            $lastWO = sprintf("%04d", $lastWO);
        };

        $generatedNo = $kode . '/' . $lastStr . '/' . $lastWO;

        return $generatedNo;
    }
}