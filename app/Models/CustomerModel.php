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
        'user_id',
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
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
                    employees.name as namaSales,
                    metadata.value AS currencyName,
                    country.country_name AS countryName,
                    companies.company as companyName";

        $customerDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'customers.currency = metadata.id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('companies', 'companies.id = customers.company_id', 'left');

        if ($condition['tipe_customer'] == "LOKAL") {
            $customerDataQry->where('customers.address !=', '')
                ->where('customers.address IS NOT NULL');
        }

        if (isset($addCondition['customers.company_id'])) {
            if (!empty($addCondition['customers.company_id'])) {
                $customerDataQry->where('customers.company_id', $addCondition['customers.company_id']);
            }
        }

        if (isset($addCondition['search']) && !empty($addCondition['search'])) {
            $customerDataQry->groupStart()
                ->like('customers.name', $addCondition['search'])
                ->orLike('customers.kode', $addCondition['search'])
                ->groupEnd();
        }

        $totalData = $customerDataQry->countAllResults(false);
        $totalFilteredData = $customerDataQry->countAllResults(false);
        if ($limit && $offset) {
            $data = $customerDataQry->orderBy($sort, $sortType)
                ->findAll($limit, $offset);
        } else {
            $data = $customerDataQry->orderBy($sort, $sortType)
                ->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getListCustomerDetail($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'namaSales'         => 'employees.name',
            'kode'              => 'customers.kode',
            'name'              => 'customers.name',
            'address'           => 'customers.address',
            'contact_person'    => 'customers.contact_person',
            'phone'             => 'customers.phone',
            'saldo'             => 'customers.saldo',
            'currencyName'      => 'metadata.value',
            'createdAt'         => 'customers.createdAt',
            'updatedAt'         => 'customers.updatedAt',
            'termin'            => 'customers.termin',
            'piutang'           => 'customers.piutang',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'customers.kode';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "customers.*, 
                    employees.name as namaSales,
                    metadata.value AS currencyName,
                    country.country_name AS countryName,
                    companies.company as companyName";

        $customerDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'customers.currency = metadata.id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('employees', 'employees.id = customers.sales_id', 'LEFT')
            ->join('companies', 'companies.id = customers.company_id', 'left');

        if ($condition['tipe_customer'] == "LOKAL") {
            // LOKAL
            if (session()->get("login")->this_company_id == 16) {
                // OCS PUNYA COUNTER LOKAL SENDIRI
                $customerDataQry->where('customers.company_id', 16);
            } else {
                // KIM 1, KIM 2, GLOBAL CUSTOMER LOKAL NYA DIGABUNG
                $customerDataQry->whereIn('customers.company_id', [1, 2, 15]);
            }
        }

        if ($condition['tipe_customer'] == "LOKAL") {
            $customerDataQry->where('customers.address !=', '')
                ->where('customers.address IS NOT NULL');
        }

        $totalData = $customerDataQry->countAllResults(false);

        if (isset($addCondition['search']) && !empty($addCondition['search'])) {
            $customerDataQry->groupStart()
                ->like('customers.name', $addCondition['search'])
                ->orLike('customers.kode', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $customerDataQry->countAllResults(false);

        if ($limit && $offset) {
            $data = $customerDataQry->orderBy($sort, $sortType)
                ->findAll($limit, $offset);
        } else {
            $data = $customerDataQry->orderBy($sort, $sortType)
                ->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getCustomerPiutangList($condition, $addCondition, $limit = 10, $offset = 0, $companyId)
    {
        $availableSort = [
            'kode'              => 'customers.kode',
            'name'              => 'customers.name',
            'address'           => 'customers.address',
            'createdAt'         => 'customers.createdAt',
            'updatedAt'         => 'customers.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'customers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "customers.*, 
        CASE 
            WHEN customers.tipe_customer = 'LOKAL' 
            THEN SUM(sales_order_invoice.total_invoice)
            ELSE SUM(sales_order_invoice.total_invoice)
        END AS total, 
        CASE 
            WHEN customers.tipe_customer = 'LOKAL'
            THEN (import_po_payments.payment_amt * import_po_payments.current_exchange_rate)
            ELSE (local_po_payments.amount)
        END AS remaining";

        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->join('sales_order_invoice', 'sales_order_invoice.id_customer = customers.id AND sales_order_invoice.status_posting = 1', 'left')
            ->where($condition)
            ->whereIn('suppliers.company_id', $companyId)
            ->groupBy('suppliers.id')
            ->having("total > 0")
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['divisi'] || $addCondition['type_barang']) {
            $supplierDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $supplierDataQry->like('name', $addCondition['search'])
                ->orLike('kode', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $supplierDataQry->where('suppliers.id', $addCondition['filter']);
        }

        if ($addCondition['divisi']) {
            $supplierDataQry->where('rm_purchase_orders.divisi_id', $addCondition['divisi'])
                ->orwhere('am_purchase_orders.division_id', $addCondition['divisi'])
                ->orwhere('rm_import_pos.division_id', $addCondition['divisi']);
        }

        if ($addCondition['type_barang']) {
            $supplierDataQry->where('suppliers.type', $addCondition['type_barang']);
        }

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['divisi'] || $addCondition['type_barang']) {
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

    public function getCustomer($company_id, $is_admin, $user_id)
    {
        if ($is_admin == 1) {
            if ($company_id != "") {
                $arrCondition = [
                    'deletedAt' => null,
                    'company_id' => $company_id
                ];
            } else {
                $arrCondition = [
                    'deletedAt' => null
                ];
            }
        } else {
            if ($company_id != "") {
                $arrCondition = [
                    'deletedAt' => null,
                    'company_id' => $company_id,
                    'user_id' => $user_id
                ];
            } else {
                $arrCondition = [
                    'deletedAt' => null
                ];
            }
        }

        $builder = $this->db->table('customers');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getCustomerEkspor($user_id, $is_admin)
    {
        $query = $this->asArray()
            // ->where('company_id', $companyID) KIM 1, KIM 2, GLOBAL DAN OCS DIGABUNG
            ->where('tipe_customer', "INTERNASIONAL")
            ->where('deletedAt', null);

        if (!$is_admin) {
            $query->where('user_id', $user_id);
        }

        return $query->orderBy('name', "ASC")->findAll();
    }

    public function getCustomerLokal($user_id, $is_admin)
    {
        $query = $this->asArray()
            ->select('customers.*, customers.sales_id AS salesName, employees.name as salesNama')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->where('customers.deletedAt', null)
            ->where('customers.tipe_customer', 'LOKAL');

        if (!$is_admin) {
            $query->where('customers.user_id', $user_id);
        }

        return $query->orderBy('createdAt', "DESC")->findAll();
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

    public function get_kode($bln, $thn2, $tipe_customer)
    {
        $lastStr = $thn2;

        $builder = $this->db->table('customers');
        $builder->select('kode');
        $builder->orderBy('kode', 'asc');
        $builder->where('tipe_customer', $tipe_customer);
        $builder->where('deletedAt', null);

        if ($tipe_customer == "LOKAL") {
            // LOKAL
            if (session()->get("login")->this_company_id == 16) {
                // OCS PUNYA COUNTER NOMOR SENDIRI
                $builder->where('company_id', 16);
            } else {
                // KIM 1, KIM 2, GLOBAL COUNTER NYA DIGABUNG
                $builder->whereIn('company_id', [1, 2, 15]);
            }
        } else {
            // INTERNASIONAL (JADI SATU)
            $builder->whereIn('company_id', [1, 2, 15, 16]);
        }

        $builder->groupStart()->like('kode', $lastStr)->groupEnd();

        $query = $builder->get();

        if ($tipe_customer == "LOKAL") {
            // TIPE CUSTOMER LOKAL
            $kodePrefix = 'CS/' . $bln . '/' . $thn2;
        } else {
            // TIPE CUSTOMER EKSPOR
            $kodePrefix = 'IN/' . $bln . '/' . $thn2;
        }

        $existingNumbers = [];

        // Ambil semua nomor urut yang sudah ada
        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['kode']);
                if (isset($explode[3]) && is_numeric($explode[3])) {
                    $existingNumbers[] = intval($explode[3]);
                }
            }
        }

        // Sort dan cari celah nomor
        $lastKode = 1;
        sort($existingNumbers);
        $foundGap = false;

        foreach ($existingNumbers as $number) {
            if ($number != $lastKode) {
                $foundGap = true;
                break;
            }
            $lastKode++;
        }

        if (!$foundGap) {
            $lastKode = empty($existingNumbers) ? 1 : end($existingNumbers) + 1;
        }

        $formattedKode = sprintf("%04d", $lastKode);
        $generatedNo = $kodePrefix . '/' . $formattedKode;

        return $generatedNo;
    }

    public function getCustomerList($tipe_customer)
    {
        $resQry = $this->asArray()
            ->where('tipe_customer', $tipe_customer)
            ->where('deletedAt', null)
            // ->where('company_id', session()->get("login")->this_company_id)
            ->orderBy('name', "asc")
            ->findAll();
        return $resQry;
    }
}
