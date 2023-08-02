<?php

namespace App\Models;

use CodeIgniter\Model;

class TandaTerimaFakturModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tanda_terima_faktur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'supplier_id',
        'faktur_no',
        'nominal_faktur',
        'invoice_date',
        'receive_date',
        'potongan',
        'tambahan',
        'faktur_type',
        'information',
        'tipe_bahan',
        'user_id',
        'status_update'
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

    public function getInvoiceList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode'              => 'suppliers.kode',
            'name'              => 'suppliers.name',
            'address'           => 'suppliers.address',
            'no_npwp'           => 'suppliers.no_npwp',
            'phone'             => 'suppliers.phone',
            'contact_person'    => 'suppliers.contact_person',
            'no_rekening'       => 'suppliers.no_rekening',
            'supplier_buyer'    => 'suppliers.supplier_buyer',
            'province'          => 'provinces.province_name',
            'city'              => 'cities.city_name',
            'postal_code'       => 'cities.postal_code',
            'createdAt'         => 'tanda_terima_faktur.createdAt',
            'updatedAt'         => 'tanda_terima_faktur.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'tanda_terima_faktur.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "tanda_terima_faktur.*, 
                      DATE_FORMAT(tanda_terima_faktur.invoice_date, '%d/%m/%Y') AS invoice_date, 
                      DATE_FORMAT(tanda_terima_faktur.receive_date, '%d/%m/%Y') AS receive_date, 
                      suppliers.name AS supplierName,
                      users.name AS userName";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = tanda_terima_faktur.supplier_id')
            ->join('users', 'users.id = tanda_terima_faktur.user_id')
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $supplierDataQry->groupStart()
                ->like('suppliers.name', $addCondition['search'])
                ->orLike('faktur_no', $addCondition['search'])
            ->groupEnd();
        }
        
        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
