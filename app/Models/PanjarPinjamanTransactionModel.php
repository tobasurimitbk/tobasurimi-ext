<?php

namespace App\Models;

use CodeIgniter\Model;

class PanjarPinjamanTransactionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'panjar_pinjaman_transaction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'type',
        'supplier_id',
        'company_id',
        'no_transaction',
        'is_posted'
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


    public function getPanjarPinjamanSupplierList($addCondition, $condition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_transaction'     => 'panjar_pinjaman_transaction.no_transaction',
            'createdAt'  => 'panjar_pinjaman_transaction.createdAt',
            'type'  => 'panjar_pinjaman_transaction.type',
            'updatedAt'     => 'panjar_pinjaman_transaction.updatedAt'

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'panjar_pinjaman_transaction.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "panjar_pinjaman_transaction.*,suppliers.name as supplier_name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_pinjaman_transaction.supplier_id = suppliers.id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);
        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['panjar_status'])) {
            $supplierDataQry->groupStart();
        }


        if (!empty($addCondition['status'])) {
            if ($addCondition['status'] == "ALL") {
                // JIKA ALL
                $supplierDataQry->whereIn('is_posted', ['1', '0']);
            } else {
                $status = $addCondition['status'] == "NOT_POSTING" ? '0' : '1';
                $supplierDataQry->where('is_posted', $status);
            }
        }



        // $supplierDataQry->where('is_posted', '0');


        if ($addCondition['search']) {
            $supplierDataQry->like('no_transaction', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $supplierDataQry->where('createdAt >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $supplierDataQry->where('createdAt <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['panjar_status'])) {
            $supplierDataQry->groupEnd();
        }



        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getPanjarPinjamanSupplierbyID($id)
    {
        $selectQry = "panjar_pinjaman_transaction.*,suppliers.name as supplier_name, suppliers.type as supplier_type";
        $panjarPinjamanSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_pinjaman_transaction.supplier_id = suppliers.id', 'left')
            ->find($id);
        return $panjarPinjamanSupplierData;
    }

}

