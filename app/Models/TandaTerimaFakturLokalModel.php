<?php

namespace App\Models;

use CodeIgniter\Model;

class TandaTerimaFakturLokalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tanda_terima_faktur_lokal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'inv_no',
        'supplier_id',
        'inv_total',
        'receive_date',
        'due_date',
        'information',
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

    public function getInvList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'inv_no'        => 'tanda_terima_faktur_lokal.inv_no',
            'supplier'      => 'suppliers.name',
            'inv_total'     => 'tanda_terima_faktur_lokal.inv_total',
            'receive_date'  => 'tanda_terima_faktur_lokal.receive_date',
            'due_date'      => 'tanda_terima_faktur_lokal.due_date',
            'createdBy'     => 'users.name',
            'createdAt'     => 'tanda_terima_faktur_lokal.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'tanda_terima_faktur_lokal.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "tanda_terima_faktur_lokal.id AS id, 
                      tanda_terima_faktur_lokal.inv_no AS inv_no, 
                      tanda_terima_faktur_lokal.inv_total AS inv_total, 
                      DATE_FORMAT(tanda_terima_faktur_lokal.receive_date, '%d/%m/%Y') AS receive_date, 
                      DATE_FORMAT(tanda_terima_faktur_lokal.due_date, '%d/%m/%Y') AS due_date, 
                      suppliers.name AS supplierName,
                      users.name AS createdBy";
        $invDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = tanda_terima_faktur_lokal.supplier_id')
            ->join('users', 'users.id = tanda_terima_faktur_lokal.createdBy')
            ->orderBy($sort, $sortType);

        $totalData = $invDataQry->countAllResults(false);

        if (!empty($addCondition['dateStart'])) {
            $invDataQry->where('tanda_terima_faktur_lokal.receive_date >=', $addCondition['dateStart']);
        }

        if (!empty($addCondition['dateEnd'])) {
            $invDataQry->where('tanda_terima_faktur_lokal.receive_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search']) {
            $invDataQry->groupStart()
                ->like('inv_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
            ->groupEnd();
        }
        
        $totalFilteredData = $invDataQry->countAllResults(false);
        $data = $invDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
