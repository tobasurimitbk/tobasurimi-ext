<?php

namespace App\Models;

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
        'company_id',
        'divisi_id',
        'warehouse_id',
        'request_date',
        'standart_production',
        'note',
        'is_posted',
        'request_status',
        'createdBy',
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
            'department'       => 'divisis.divisi',
            'nama_barang'       => 'work_order_details.nama_barang',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'work_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "work_orders.*,
            work_order_details.nama_barang,
            divisis.divisi
        ";

        $workOrdersDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = work_orders.divisi_id')
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id')
            ->groupBy('work_orders.id')
            ->orderBy($sort, $sortType);

        $totalData = $workOrdersDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $workOrdersDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $workOrdersDataQry
                ->like('work_orders.wo_no', $addCondition['search'])
                ->orLike('work_order_details.nama_barang', $addCondition['search']);
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

    public function get_no($tgl, $bln, $thn, $last_day)
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

        $lastStr =  $romanNumb[$bln - 1] . '/' . $thn;

        $builder = $this->db->table('work_orders');
        $builder->select('wo_no');
        $builder->orderBy('wo_no', 'desc')
            ->where('deletedAt', null);
        $builder->like('wo_no', $lastStr);
        $query = $builder->get();

        $kode = 'PRD';

        $lastWO = '0001';
        if ($query->getResultArray()) {
            $lastWO = explode('/', $query->getResultArray()[0]['wo_no']);
            $lastWO = intval($lastWO[3]) + 1;

            $lastWO = sprintf("%04d", $lastWO);
        };

        $generatedNo = $kode . '/' . $lastStr . '/' . $lastWO;

        return $generatedNo;
    }
}
