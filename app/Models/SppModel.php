<?php

namespace App\Models;

use CodeIgniter\I18n\Time;

use CodeIgniter\Model;

class SppModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'purchase_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'request_date',
        'spp_no',
        'spp_type',
        'warehouse_id',
        'total',
        'note',
        'is_posted',
        'request_status',
        'createdBy'
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

    public function getSppList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'spp_type'          => 'purchase_requests.spp_type',
            'spp_no'            => 'purchase_requests.spp_no',
            'warehouse_name'    => 'warehouse.warehouse_name',
            'total'             => 'purchase_requests.total',
            'request_date'      => 'purchase_requests.request_date',
            'createdAt'         => 'purchase_requests.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'purchase_requests.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "purchase_requests.*,
                    warehouses.warehouse_name AS warehouseName, 
                    COUNT(purchase_request_details.id) AS itemCount";

        $purchaseRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('warehouses', 'purchase_requests.warehouse_id = warehouses.id')
            ->join('purchase_request_details', 'purchase_requests.id = purchase_request_details.purchase_request_id', 'left')
            ->groupBy(('purchase_request_details.id'))
            ->orderBy($sort, $sortType);

        $totalData = $purchaseRequestsDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $purchaseRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $purchaseRequestsDataQry
                ->like('spp_no', $addCondition['search']);
            // ->orLike('spp_type', $addCondition['search']);
        }

        if ($addCondition['spp_type']) {
            $purchaseRequestsDataQry
                ->like('spp_type', $addCondition['spp_type']);
        }

        if ($addCondition['dateStart']) {
            $purchaseRequestsDataQry->where('purchase_requests.request_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $purchaseRequestsDataQry->where('purchase_requests.request_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $purchaseRequestsDataQry->groupEnd();
        }

        $totalFilteredData = $purchaseRequestsDataQry->countAllResults(false);
        $data = $purchaseRequestsDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSppById($id)
    {
        $selectQry = "purchase_requests.*,
        warehouses.warehouse_name AS warehouseName,
        createdBy.name AS createdByName
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('warehouses', 'purchase_requests.warehouse_id = warehouses.id')
            ->join('users AS createdBy', 'purchase_requests.createdBy = createdBy.id', 'left')
            ->find($id);

        return $sppData;
    }

    public function getNoSPP($type)
    {
        $arrCondition = [
            'deletedAt' => null,
            'spp_type' => $type,
            'is_posted' => 1,
            'request_status' => 'waiting'
        ];

        $builder = $this->db->table('purchase_requests');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function generateNoSpp($warehouse)
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

        $warehouse = str_replace(' ', '', $warehouse);

        $lastStr =  $warehouse . '/' . $romanNumb[$month] . '/' . $year;

        $builder = $this->db->table('purchase_requests');
        $builder->select('spp_no');
        $builder->orderBy('spp_no', 'desc');
        $builder->like('spp_no', $lastStr);
        $query = $builder->get();

        $increment = '01';

        if ($query->getResultArray()) {
            $lastSpp = explode('/', $query->getResultArray()[0]['spp_no']);
            $lastSpp = intval($lastSpp[0]) + 1;

            if ($lastSpp < 10) {
                $lastSpp = "0" . $lastSpp . "";
            } else {
                $lastSpp = strval($lastSpp);
            }

            $increment = $lastSpp;
        };



        $generatedSppNo = $increment . '/' . $lastStr;

        return $generatedSppNo;
    }
}
