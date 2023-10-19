<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModuleModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'account_module';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'type',
        'kategori',
        'module',
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

    public function getAccountModuleList($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'name'              => 'account_module.name',
            'type'              => 'account_module.type',
            'kategori'       => 'account_module.kategori',
            'ap_id'              => 'account_module.ap_id',
            'ar_id'       => 'account_module.ar_id',
            'createdAt'         => 'account_module.createdAt',
            'updatedAt'         => 'account_module.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'account_module.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        // $selectQry = "supplier_tipes.*, 
        //               cities.city_name AS city_name, 
        //               provinces.province_name AS province_name";
        $accountModuleDataQry = $this->asObject()
            ->select("*")
            ->orderBy($sort, $sortType);

        // $selectQry = "suppliers.*, 
        //               cities.city_name AS city_name, 
        //               provinces.province_name AS province_name,
        //               ap.nama_sub AS ap_name,
        //               ar.nama_sub AS ar_name";
        // $supplierDataQry = $this->asObject()
        //     ->select($selectQry)
        //     ->where($condition)
        //     ->join('cities', 'suppliers.city_id = cities.id', 'left')
        //     ->join('provinces', 'suppliers.province_id = provinces.id', 'left')
        //     ->join('sub_akuns AS ap', 'suppliers.ap_id = ap.id', 'left')
        //     ->join('sub_akuns AS ar', 'suppliers.ar_id = ar.id', 'left')
        //     ->orderBy($sort, $sortType);

        $totalData = $accountModuleDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $accountModuleDataQry->groupStart()
                ->like('name', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $accountModuleDataQry->countAllResults(false);
        $data = $accountModuleDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAccountModuleById($id)
    {
        $accountModuleData = $this->asObject()
            ->select('account_module.*')
            ->find($id);

        return $accountModuleData;
    }

    // public function getSupplierByKategoriAndType($kategori, $type, $company_id)
    // {
    //     $arrCondition = [
    //         'deletedAt' => null,
    //         'kategori' => $kategori,
    //         'type' => $type,
    //         'company_id' => $company_id
    //     ];

    //     $builder = $this->db->table('suppliers');
    //     $builder->where($arrCondition);
    //     $query = $builder->get();

    //     return $query->getResultArray();
    // }

    // public function generateSupplierCode(): string
    // {
    //     $month = idate('m');
    //     $year = date('y');
    //     $romanMonth = romanMonthNumber($month);
    //     $numberTemplate = "/SUP/$romanMonth/$year";

    //     $lastData = $this->asObject()
    //         ->like('kode', $numberTemplate, 'before')
    //         ->orderBy('createdAt', 'DESC')
    //         ->first();

    //     $invNumber = '001' . $numberTemplate;

    //     if (!empty($lastData)) {
    //         $asd = explode('/', $lastData->kode);
    //         $lastIncrement = intval($asd[0]) + 1;
    //         $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

    //         $invNumber = $paddedNumber . $numberTemplate;
    //     }

    //     return $invNumber;
    // }
}
