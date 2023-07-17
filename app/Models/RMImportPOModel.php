<?php

namespace App\Models;

use CodeIgniter\Model;

class RMImportPOModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_import_pos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'status_penerimaan'];

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

    public function getPOList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'           => 'rm_import_pos.po_date',
            'poNo'             => 'rm_import_pos.po_no',
            'supplierName'      => 'suppliers.name',
            'total'             => 'rm_import_pos.total',
            'currency'          => 'metadata.value',
            'createdAt'         => 'rm_import_pos.createdAt',
            'updatedAt'         => 'rm_import_pos.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'rm_import_pos.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rm_import_pos.*, 
                      suppliers.name AS supplierName, 
                      metadata.value AS currency";
        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id')
            ->join('metadata', 'metadata.id = rm_import_pos.currency')
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $poDataQry->like('rm_import_pos.po_no', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $poDataQry->where('rm_import_pos.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $poDataQry->where('rm_import_pos.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupEnd();
        }
        
        $totalFilteredData = $poDataQry->countAllResults(false);
        $data = $poDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getNoPenerimaanBarang($supplier_id, $company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('rm_import_pos');
        $builder->where($arrCondition);
        $query = $builder->get();
        
        return $query->getResultArray();
    }
}