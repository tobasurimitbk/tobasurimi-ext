<?php

namespace App\Models;

use CodeIgniter\Model;

class InvPackingCustomerModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inv_packing_customer';
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
            'id'     => 'inv_packing_customer.id',
            'no_container'     => 'no_container',
            'no_seal'     => 'no_seal',
            'vessels_name'               => 'vessels_name',
            'departure_date'               => 'departure_date',
            'total_packing'               =>  'total_packing',
            'total_berat_bersih'               =>  'total_berat_bersih',
            'total_berat_kotor'               =>  'total_berat_kotor',
            'total_nilai_invoice'               =>  'total_nilai_invoice',
            'valas_id'               =>  'valas_id',
            'status_posting'               =>  'status_posting',
            'status_bayar'               =>  'status_bayar',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'inv_packing_customer.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "inv_packing_customer.*,
        metadata.value as valas_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('metadata', 'metadata.id = inv_packing_customer.valas_id', 'left')
            ->where($condition);

        $totalData = $dataQry->countAllResults(false);

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $dataQry->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $dataQry->where('departure_date >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $dataQry->where('departure_date <=', $addCondition['dateEnd']);
            }
            $dataQry->groupEnd();
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] == "SUDAH POSTING") {
                $dataQry
                    ->where('inv_packing_customer.status_posting', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING") {
                $dataQry
                    ->where('inv_packing_customer.status_posting', 0);
            }
        }

        if (isset($addCondition['search']) && !empty($addCondition['search'])) {
            $dataQry->groupStart()
                ->like('inv_packing_customer.no_container', $addCondition['search'])
                ->orLike('inv_packing_customer.no_seal', $addCondition['search'])
                ->orLike('inv_packing_customer.vessels_name', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);

        $data = $dataQry->orderBy($sort, $sortType)
            ->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }
}
