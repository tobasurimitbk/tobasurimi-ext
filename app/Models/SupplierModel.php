<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'suppliers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id', 
        'kode', 
        'name', 
        'address', 
        'province_id', 
        'city_id', 
        'no_npwp', 
        'phone', 
        'contact_person',
        'email',
        'no_rekening',
        'supplier_buyer',
        'type',
        'kategori',
        'ap_id',
        'ar_id'
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

    public function getSupplierList($condition, $addCondition, $limit = 10, $offset = 0)
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
            'createdAt'         => 'suppliers.createdAt',
            'updatedAt'         => 'suppliers.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "suppliers.*, 
                      cities.city_name AS city_name, 
                      provinces.province_name AS province_name,
                      cities.postal_code AS postal_code,
                      ap.nama_sub AS ap_name,
                      ar.nama_sub AS ar_name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('cities', 'suppliers.city_id = cities.id')
            ->join('provinces', 'suppliers.province_id = provinces.id')
            ->join('sub_akuns AS ap', 'suppliers.ap_id = ap.id')
            ->join('sub_akuns AS ar', 'suppliers.ar_id = ar.id')
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $supplierDataQry->groupStart()
                ->like('name', $addCondition['search'])
                ->orLike('kode', $addCondition['search'])
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
