<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
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
        'postal_code',
        'tipe_pelanggan',
        'termin',
        'currency',
        'piutang',
        'saldo',
        'nik',
        'sales_id',
        'country_id',
        'tipe_customer',
        'jenis_penjualan',
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

    public function get_by_id($id)
    {
        $requete = "SELECT customers.* FROM customers ";
        $requete .= "WHERE customers.deletedAt is null and customers.id='" . $id . "'";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function getList($condition, $companyAccessArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'companyName'       => 'companies.company',
            'namaSales'         => 'users.name',
            'kode'              => 'customers.kode',
            'name'              => 'customers.name',
            'contact_person'    => 'customers.contact_person',
            'phone'             => 'customers.phone',
            'saldo'             => 'customers.saldo',
            'currencyName'      => 'metadata.value',
            'createdAt'         => 'customers.createdAt',
            'updatedAt'         => 'customers.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'customers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "customers.*, 
                    users.name as namaSales,
                    companies.company as companyName,
                      metadata.value AS currencyName,
                      country.country_name AS countryName";

        $customerDataQry = $this->asObject()
            ->select($selectQry)
            ->whereIn('customers.company_id', $companyAccessArr)
            ->where($condition)
            ->join('metadata', 'customers.currency = metadata.id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('users', 'users.id = customers.sales_id', 'LEFT')
            ->join('companies', 'companies.id = customers.company_id', 'LEFT')
            // ->groupBy(('customers.id'))
            ->orderBy($sort, $sortType);

        $totalData = $customerDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['company_id']) {
            $customerDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $customerDataQry->like('customers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
                ->orLike('customers.kode', $addCondition['search']);
        }

        if ($addCondition['company_id']) {
            $customerDataQry->where('company_id', $addCondition['company_id']);
        }

        if ($addCondition['search'] || $addCondition['company_id']) {
            $customerDataQry->groupEnd();
        }

        $totalFilteredData = $customerDataQry->countAllResults(false);
        $data = $customerDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    // public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    // {
    //     $requete = "SELECT customers.*,provinces.province_name,cities.city_name FROM customers ";
    //     $requete .= "LEFT JOIN provinces ON (customers.province_id=provinces.id) ";
    //     $requete .= "LEFT JOIN cities ON (customers.city_id=cities.id) ";
    //     $requete .= "WHERE customers.deletedAt is null ";
    //     if (isset($values["name"]))
    //         $requete .= ($values["name"] == "") ? "" : ("AND UPPER(customers.name) like '%" . strtoupper($values["name"]) . "%' ");
    //     if (isset($values["search"]))
    //         $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(customers.kode) like '%" . strtoupper($values["search"]) . "%' OR UPPER(customers.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(customers.address) like '%" . strtoupper($values["search"]) . "%') ");

    //     if ($sortby != '')
    //         $requete .= "ORDER BY $sortby ";
    //     if ($limit >= 0)
    //         $requete .= "LIMIT $limit OFFSET $offset";
    //     //echo $requete;

    //     $query = $this->db->query($requete);
    //     return $query->getResultArray();
    // }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM customers ";
        $requete .= "WHERE customers.deletedAt is null ";
        if (isset($values["name"]))
            $requete .= ($values["name"] == "") ? "" : ("AND UPPER(customers.name) like '%" . strtoupper($values["name"]) . "%' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(customers.kode) like '%" . strtoupper($values["search"]) . "%' OR UPPER(customers.name) like '%" . strtoupper($values["search"]) . "%' OR UPPER(customers.address) like '%" . strtoupper($values["search"]) . "%') ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }

    public function getCustomer()
    {
        $arrCondition = [
            'deletedAt' => null
        ];

        $builder = $this->db->table('customers');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getCustomerEkspor($user_id, $companyID)
    {
        return $this->asArray()->where('company_id', $companyID)->where('tipe_customer', "INTERNASIONAL")->where('deletedAt', null)->where('sales_id', $user_id)->orderBy('createdAt', "DESC")->findAll();
    }

    public function getCustomerLokal()
    {
        return $this->asArray()
            ->select('customers.*, CONCAT(employees.nip , " - ", employees.name) AS salesName')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->where('customers.deletedAt', null)
            ->where('customers.tipe_customer', 'LOKAL')
            ->where('employees.deletedAt', null)
            ->orderBy('createdAt', "DESC")
            ->findAll();
    }

    public function getCustomerWithMetaData($idCustomer)
    {
        $selectQry = "customers.*,
                      metadata.value AS tipe_pelanggan";

        $arrCondition = [
            'id'    => $idCustomer,
            'deletedAt' => null
        ];

        // $builder = $this->db->table('customers');
        // $builder->join('metadata', 'metadata.id = customers.tipe_pelanggan');
        // $builder->where($arrCondition);
        // $query = $builder->get();

        $customerMeta = $this->asObject()
            ->join('metadata', 'metadata.id = customers.tipe_pelanggan')
            ->select($selectQry)
            ->where($arrCondition);

        return $customerMeta;
    }

    public function get_kode($bln, $thn, $thn2, $last_year)
    {
        $lastStr =  $thn2;

        $builder = $this->db->table('customers');
        $builder->select('kode');
        $builder->orderBy('kode', 'desc');
        $builder->where('createdAt >=', $thn . "-01-01" . " 00:00:00")
            ->where('createdAt <=', $last_year . " 23:59:59");
        $builder->like('kode', $lastStr);
        $query = $builder->get();

        $kode = 'CS';

        $lastKode = '0001';
        if ($query->getResultArray()) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['kode']);
                $number = intval($explode[3]);
                if ($number > $lastKode) {
                    $lastKode = $number;
                }
            }
            $lastKode = sprintf("%04d", $lastKode + 1);
        };

        $generatedNo = $kode . '/' . $bln . '/' . $thn2 . '/' . $lastKode;

        return $generatedNo;
    }
}
