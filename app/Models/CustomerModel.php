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

        // ===== BASE QUERY (untuk totalData) =====
        $baseQuery = $this->asObject()
            ->select($selectQry)
            ->join('metadata', 'customers.currency = metadata.id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('employees', 'employees.id = customers.sales_id', 'LEFT')
            ->join('companies', 'companies.id = customers.company_id', 'left')
            ->where($condition);

        if ($condition['tipe_customer'] == "LOKAL") {
            if (session()->get("login")->this_company_id == 16) {
                $baseQuery->where('customers.company_id', 16);
            } else {
                $baseQuery->whereIn('customers.company_id', [1, 2, 15]);
            }
            // $baseQuery->where('customers.address !=', '')
            //     ->where('customers.address IS NOT NULL');
        }

        // total data tanpa filter
        $totalData = $baseQuery->countAllResults();

        // ===== FILTER QUERY (untuk totalFiltered + data) =====
        $customerDataQry = $this->asObject()
            ->select($selectQry)
            ->join('metadata', 'customers.currency = metadata.id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('employees', 'employees.id = customers.sales_id', 'LEFT')
            ->join('companies', 'companies.id = customers.company_id', 'left')
            ->where($condition);

        if ($condition['tipe_customer'] == "LOKAL") {
            if (session()->get("login")->this_company_id == 16) {
                $customerDataQry->where('customers.company_id', 16);
            } else {
                $customerDataQry->whereIn('customers.company_id', [1, 2, 15]);
            }
            // $customerDataQry->where('customers.address !=', '')
            //     ->where('customers.address IS NOT NULL');
        }

        if (!empty($addCondition['search'])) {
            $customerDataQry->groupStart()
                ->like('customers.name', $addCondition['search'])
                ->orLike('customers.kode', $addCondition['search'])
                ->groupEnd();
        }

        // hitung data setelah filter
        $totalFilteredData = $customerDataQry->countAllResults(false);

        // ORDER dan LIMIT
        $customerDataQry->orderBy($sort, $sortType);
        $customerDataQry->limit($limit, $offset);

        $data = $customerDataQry->get()->getResult();

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
        // Bentuk prefix sesuai tipe
        $prefix = ($tipe_customer == "LOKAL") ? "CS" : "IN";

        if ($prefix == "CS") {
            // Bentuk pola pencarian yang spesifik untuk bulan & tahun
            $kodePrefix = $prefix . '/' . $bln . '/' . $thn2;

            $builder = $this->db->table('customers');
            $builder->select('kode');
            $builder->where('tipe_customer', $tipe_customer);
            $builder->where('deletedAt', null); // hanya ambil yang prefix-nya cocok

            if ($tipe_customer == "LOKAL") {
                if (session()->get("login")->this_company_id == 16) {
                    $builder->where('company_id', 16);
                } else {
                    $builder->whereIn('company_id', [1, 2, 15]);
                }
            } else {
                $builder->whereIn('company_id', [1, 2, 15, 16]);
            }

            // ambil kode terbesar
            $builder->orderBy('id', 'desc');
            $builder->limit(1);
            $query = $builder->get();
            $row = $query->getRowArray();

            // Jika belum ada data di bulan/tahun ini
            if (!$row) {
                $lastKode = 1;
            } else {
                // contoh kode: CS/10/25/0117
                $parts = explode('/', $row['kode']);
                $lastNumber = intval(end($parts)); // ambil angka terakhir (0117 → 117)
                $lastKode = $lastNumber + 1;
            }

            // Format ke 4 digit
            $formattedKode = sprintf("%04d", $lastKode);

            $prefix = implode('/', array_slice($parts, 0, -1));
            // Gabungkan kembali
            $generatedNo = $prefix . '/' . $formattedKode;
        } else {
            // Bentuk pola pencarian yang spesifik untuk bulan & tahun
            $kodePrefix = $prefix . '/' . $bln . '/' . $thn2;

            $builder = $this->db->table('customers');
            $builder->select('kode');
            $builder->where('tipe_customer', $tipe_customer);
            $builder->where('deletedAt', null);
            $builder->like('kode', $kodePrefix, 'after'); // hanya ambil yang prefix-nya cocok

            if ($tipe_customer == "LOKAL") {
                if (session()->get("login")->this_company_id == 16) {
                    $builder->where('company_id', 16);
                } else {
                    $builder->whereIn('company_id', [1, 2, 15]);
                }
            } else {
                $builder->whereIn('company_id', [1, 2, 15, 16]);
            }

            // ambil kode terbesar
            $builder->orderBy('id', 'desc');
            $builder->limit(1);
            $query = $builder->get();
            $row = $query->getRowArray();

            // Jika belum ada data di bulan/tahun ini
            if (!$row) {
                $lastKode = 1;
            } else {
                // contoh kode: CS/10/25/0117
                $parts = explode('/', $row['kode']);
                $lastNumber = intval(end($parts)); // ambil angka terakhir (0117 → 117)
                $lastKode = $lastNumber + 1;
            }

            // Format ke 4 digit
            $formattedKode = sprintf("%04d", $lastKode);

            // Gabungkan kembali
            $generatedNo = $kodePrefix . '/' . $formattedKode;
        }

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

    public function getCustomerByCompany($companyId, $tipeCustomer)
    {
        $resQry = $this->asArray()
            ->where('tipe_customer', $tipeCustomer)
            ->where('company_id', $companyId)
            ->where('deletedAt', null)
            ->orderBy('name', "asc")
            ->findAll();
        return $resQry;
    }
}
