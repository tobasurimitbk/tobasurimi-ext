<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasterModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'barang_master';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'satuan_id',
        'parent_type_id',
        'divisi_id',
        'barang_name',
        'barang_name_alias',
        'kode_barang',
        'type_barang',
        'minimum_stock',
        'harga_pokok',
        'harga_jual',
        'createdAt',
        'updatedAt',
        'deletedAt',
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kelompok_barang'   => 'parent_barang.parent_name',
            'kode_barang'       => 'barang_master.kode_barang',
            'barang_name'       => 'barang_master.barang_name',
            'createdAt'         => 'barang_master.createdAt',
            'supplier_terakhir' => 'barang_master_spesifikasi.supplier_terakhir',
            'harga_terakhir'    => 'barang_master_spesifikasi.harga_terakhir',
            'satuan_1'          => 'barang_master_spesifikasi.satuan_1'

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'barang_master.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $aksesSupplierLokalBP = can('Pembelian', 'PO Lokal BP', 'r');
        $aksesSupplierImportBP = can('Pembelian', 'PO Import BP', 'r');

        // Hitung total data tanpa filter
        $totalDataQry = $this->select([
            'barang_master.id',
        ]);
        $totalDataQry->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left');
        $totalDataQry->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left');
        if ($aksesSupplierLokalBP && !$aksesSupplierImportBP) {
            // PO LOKAL BP — sembunyikan kode barang yang diawali 'BI-'
            $totalDataQry->notLike('barang_master.kode_barang', 'BI-', 'after');
        } elseif (!$aksesSupplierLokalBP && $aksesSupplierImportBP) {
            // PO IMPORT BP — hanya tampilkan kode barang yang diawali 'BI-'
            $totalDataQry->like('barang_master.kode_barang', 'BI-', 'after');
        }
        $totalData =  $totalDataQry->where($condition)->countAllResults();

        $barangDataQry = $this->asArray()
            ->select([
                'barang_master.id',
                'barang_master.kode_barang',
                'barang_master.barang_name',
                'barang_master_spesifikasi.id as spesifikasi_id',
                'barang_master_spesifikasi.spesifikasi',
                'barang_master_spesifikasi.satuan_1',
                'barang_master_spesifikasi.satuan_2',
                'barang_master_spesifikasi.konversi_satuan_2',
                'barang_master_spesifikasi.satuan_3',
                'barang_master_spesifikasi.konversi_satuan_3',
                'barang_master_spesifikasi.supplier_terakhir',
                'parent_barang.parent_name AS kelompok_barang',
                'suppliers.name as supplier_terakhir_name',
                'barang_master_spesifikasi.harga_terakhir',
                'satuans.kode_satuan AS kode_satuan_terakhir'
            ])
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->join('suppliers', 'suppliers.id = barang_master_spesifikasi.supplier_terakhir', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.unit_terakhir', 'left')
            ->where($condition);

        if ($aksesSupplierLokalBP && !$aksesSupplierImportBP) {
            // PO LOKAL BP — sembunyikan kode barang yang diawali 'BI-'
            $barangDataQry->notLike('barang_master.kode_barang', 'BI-', 'after');
        } elseif (!$aksesSupplierLokalBP && $aksesSupplierImportBP) {
            // PO IMPORT BP — hanya tampilkan kode barang yang diawali 'BI-'
            $barangDataQry->like('barang_master.kode_barang', 'BI-', 'after');
        }

        if ($addCondition['search']) {
            $search = strtolower($addCondition['search'] . " ");

            $keywords = explode(" ", strtolower($search)); // Pisahkan kata-kata dalam query
            $barangDataQry->groupStart();

            foreach ($keywords as $word) {
                $barangDataQry->groupStart()
                    ->like('LOWER(barang_master.barang_name)', $word)
                    ->orLike('LOWER(barang_master.kode_barang)', $word)
                    ->orLike('LOWER(parent_barang.parent_name)', $word)
                    ->orLike('LOWER(barang_master_spesifikasi.spesifikasi)', $word)
                    ->orLike('LOWER(suppliers.name)', $word)
                    ->groupEnd();
            }

            $barangDataQry->groupEnd();
        }

        if ($addCondition['filter_coa']) {
            $barangDataQry->join('account_barang', 'barang_master.id = account_barang.barang_master_id AND barang_master_spesifikasi.id = account_barang.barang_master_spesifikasi_id', 'left');

            if ($addCondition['filter_coa'] == "belum") {
                $barangDataQry->where('account_barang.ap_id', NULL);
            } elseif ($addCondition['filter_coa'] == "sudah") {
                $barangDataQry->where('account_barang.ap_id !=', NULL);
            }
        }

        $totalFilteredData = $barangDataQry->countAllResults(false);
        $data = $barangDataQry->orderBy($sort, $sortType)->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getBarangByType($type, $type2 = null)
    {
        $arrCondition = [
            'barang_master.deletedAt' => null,
            'barang_master.type_barang' => $type,
            'barang_master.company_id' => session()->get('login')->this_company_id
        ];

        $selectQry = "barang_master.*, satuans.nama_satuan, parent_barang.parent_name";

        $this->select($selectQry)
            ->join('satuans', 'barang_master.satuan_id = satuans.id', 'left')
            ->join('parent_barang', 'barang_master.parent_type_id = parent_barang.id', 'left')
            ->where($arrCondition);

        if ($type2 !== null) {
            $this->orWhere('barang_master.type_barang', $type2);
        }

        $this->orderBy('barang_master.barang_name', "ASC");

        $data = $this->findAll();

        return $data;
    }

    public function getBarangByTypeCondition($companyId, $type, $type2 = null)
    {
        $selectQry = "barang_master.*, satuans.nama_satuan, parent_barang.parent_name";

        $this->select($selectQry)
            ->join('satuans', 'barang_master.satuan_id = satuans.id', 'left')
            ->join('parent_barang', 'barang_master.parent_type_id = parent_barang.id', 'left')
            ->where('barang_master.company_id', $companyId)
            ->groupStart()  // Start grouping
            ->where('barang_master.type_barang', $type);

        if ($type2 !== null) {
            $this->orWhere('barang_master.type_barang', $type2);
        }

        $this->groupEnd()  // End grouping
            ->orderBy('barang_master.barang_name', "ASC");

        $data = $this->findAll();

        return $data;
    }


    public function getBarangByTypeWithSpec($condition)
    {
        $selectQry = "barang_master.id, barang_master.kode_barang, barang_master.barang_name AS barang_name_master, barang_master.parent_type_id, 
                    satuans.nama_satuan,
                    satuans.kode_satuan, 
                    parent_barang.parent_name,
                    barang_master_spesifikasi.id AS barang_master_spesifikasi_id, 
                    barang_master_spesifikasi.spesifikasi,
                    barang_master_spesifikasi.satuan_1,
                    barang_master_spesifikasi.satuan_2,
                    barang_master_spesifikasi.satuan_3";

        $data = $this->select($selectQry)
            ->join('parent_barang', 'barang_master.parent_type_id = parent_barang.id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->join('satuans', 'barang_master_spesifikasi.satuan_1 = satuans.id', 'left')
            ->where('barang_master.deletedAt', null)
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where($condition)
            ->findAll();

        return $data;
    }

    public function getBySupplier($id)
    {
        $arrCondition = [
            'barang_master.deletedAt' => null,
            'supplier_harga.deletedAt' => null,
            'supplier_harga.supplier_id' => $id
        ];

        $selectQry = "barang_master.*";
        $data = $this->select($selectQry)
            ->join('supplier_harga', 'barang_master.id = supplier_harga.bahan_baku_id', 'left')
            ->where($arrCondition)
            ->groupBy('id')
            ->orderBy('barang_name', 'asc')
            ->findAll();

        return $data;
    }

    public function getBarang($barangID)
    {
        return $this->where('id', $barangID)->first();
    }

    public function getListForAccount($condition, $conditionArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'       => 'barang_master.kode_barang',
            'barang_name'       => 'barang_master.barang_name',
            'createdAt'         => 'barang_master.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'barang_master.id, divisis.divisi';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "barang_master.*,
                    divisis.id AS divisi_id,
                    divisis.divisi";

        $barangDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->whereIn('divisis.id', $conditionArr)
            ->join('divisis', '1=1', 'CROSS')
            // ->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $barangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['filter_divisi']) {
            $barangDataQry->groupStart();
        }

        if ($addCondition['filter_divisi']) {
            $barangDataQry->where('divisis.id', $addCondition['filter_divisi']);
        }

        if ($addCondition['search']) {
            $barangDataQry->like('barang_master.barang_name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $barangDataQry->orLike('barang_master.kode_barang', $addCondition['search']);
        }

        if ($addCondition['search'] || $addCondition['filter_divisi']) {
            $barangDataQry->groupEnd();
        }

        $totalFilteredData = $barangDataQry->countAllResults(false);
        $data = $barangDataQry->findAll($limit, $offset);

        // var_dump($data);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getAccountBarangForJurnal($barangID)
    {
        $select =   "barang_master.*,
                    account_barang.ap_id,
                    account_barang.ar_id,";
        return $this->asObject()
            ->select($select)
            ->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left')
            ->where('barang_master.id', $barangID)
            ->where('barang_master.deletedAt', null)
            ->findAll();
    }

    public function getListBarangmaster($typeBarang)
    {
        $selectQry = "
            barang_master.id,
            barang_master.kode_barang,
            barang_master.barang_name as barang
        ";

        $dataResult1 = $this->asArray()->select($selectQry)
            ->where('barang_master.deletedAt', null)
            ->where('barang_master.type_barang', $typeBarang)
            ->where('barang_master.company_id', session()->get("login")->this_company_id)
            ->orderBy('barang_master.kode_barang', "ASC")
            ->findAll();

        return $dataResult1;
    }

    public function dropdownBarangType($type, $companyId, $search, $spesifikasiId = null)
    {

        $condition = [
            'barang_master.company_id' => $companyId,
            'barang_master.type_barang' => $type,
        ];

        $selectQry = "barang_master.id, 
                    barang_master.kode_barang, 
                    barang_master.barang_name AS barang_name_master, 
                    barang_master.parent_type_id, 
                    satuans.nama_satuan,
                    satuans.kode_satuan, 
                    parent_barang.parent_name,
                    barang_master_spesifikasi.id AS barang_master_spesifikasi_id, 
                    barang_master_spesifikasi.spesifikasi,
                    barang_master_spesifikasi.satuan_1,
                    barang_master_spesifikasi.satuan_2,
                    barang_master_spesifikasi.satuan_3,,
                    barang_master_spesifikasi.barang_master_id AS barang_id";

        $dataQry = $this->select($selectQry)
            ->join('parent_barang', 'barang_master.parent_type_id = parent_barang.id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->join('satuans', 'barang_master_spesifikasi.satuan_1 = satuans.id', 'left')
            ->where('barang_master.deletedAt', null)
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where($condition);


        if ($spesifikasiId != null) {
            $dataBarang = $dataQry->where('barang_master_spesifikasi.id', $spesifikasiId)->findAll();
        } else {
            $dataQry
                ->groupStart()
                ->like('CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi)', $search)
                ->orLike('barang_master.kode_barang', $search)
                ->groupEnd();

            $dataBarang = $dataQry->findAll(100);
        }

        for ($i = 0; $i < count($dataBarang); $i++) {
            $dataBarang[$i]['id'] = encrypt($dataBarang[$i]['barang_master_spesifikasi_id']);
            $dataBarang[$i]['parent_type_id'] = encrypt($dataBarang[$i]['parent_type_id']);
            $dataBarang[$i]['barang_master_spesifikasi_id'] = encrypt($dataBarang[$i]['barang_master_spesifikasi_id']);
            $dataBarang[$i]['barang_name'] = trim(
                str_replace(
                    ["\"", "\t"], // hapus tanda " dan tab
                    "'",           // ganti " dengan ', tab jadi hilang
                    $dataBarang[$i]['barang_name_master'] . ' ' . $dataBarang[$i]['spesifikasi']
                )
            );
        }

        return $dataBarang;
    }

    public function dropdownBarangStock(
        $type,
        $companyId,
        $search
    ) {
        $condition = [
            'barang_master.company_id' => $companyId,
            'barang_master.type_barang' => $type,
        ];

        $selectQry = "barang_master_spesifikasi.id AS spesifikasi_id,
                    barang_master.kode_barang, 
                    barang_master.barang_name, 
                    barang_master_spesifikasi.spesifikasi";

        $dataQry = $this->select($selectQry)
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->where('barang_master.deletedAt', null)
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where($condition);

        $dataQry
            ->groupStart()
            ->like('CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi)', $search)
            ->orLike('barang_master.kode_barang', $search)
            ->groupEnd();

        $dataBarang = $dataQry->findAll(100);

        return $dataBarang;
    }

    public function dropdownBarangMaster(
        $type,
        $companyId,
        $search
    ) {
        $condition = [
            'barang_master.company_id' => $companyId,
            'barang_master.type_barang' => $type,
        ];

        $selectQry = "
            barang_master.id,
            barang_master.kode_barang,
            barang_master.barang_name
        ";

        $dataQry = $this->select($selectQry)->where('barang_master.deletedAt', null)->where($condition);
        $dataQry->groupStart();
        $dataQry->like('barang_master.kode_barang', $search);
        $dataQry->orLike('barang_master.barang_name', $search);
        $dataQry->groupEnd();

        $dataBarang = $dataQry->findAll(100);
        return $dataBarang;
    }

    public function getListBarangAlias(
        array $condition,
        array $addCondition,
        int $limit = 10,
        int $offset = 0
    ) {
        // SORTABLE COLUMN (harus REAL column / alias valid)
        $availableSort = [
            'barang_impor' => 'barang_impor',
            'barang_alias' => 'barang_alias',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'barang_impor']
            ?? 'barang_impor';

        $sortType = $availableSortType[$addCondition['sortType'] ?? 'asc']
            ?? 'ASC';

        /* ===============================
     * TOTAL DATA (TANPA SEARCH)
     * =============================== */
        $totalDataQry = $this->builder();
        $totalDataQry
            ->select('barang_master_spesifikasi.id')
            ->join(
                'barang_master_spesifikasi',
                'barang_master_spesifikasi.barang_master_id = barang_master.id',
                'left'
            )
            ->where($condition)
            ->like('barang_master.kode_barang', 'BI-', 'after');

        $totalData = $totalDataQry->countAllResults();

        /* ===============================
     * DATA QUERY
     * =============================== */
        $barangDataQry = $this->asArray()
            ->select([
                'barang_master.type_barang',
                'barang_master_spesifikasi.id AS id',
                "CONCAT(barang_master.barang_name,' - ',barang_master_spesifikasi.spesifikasi) AS barang_impor",
                "CONCAT(barang_master.barang_name_alias,' - ',barang_master_spesifikasi.spesifikasi_alias) AS barang_alias",
                'barang_master.barang_name_alias',
                'barang_master_spesifikasi.spesifikasi_alias'
            ])
            ->join(
                'barang_master_spesifikasi',
                'barang_master_spesifikasi.barang_master_id = barang_master.id',
                'left'
            )
            ->where($condition)
            ->like('barang_master.kode_barang', 'BI-', 'after');

        /* ===============================
     * SEARCH
     * =============================== */
        if (!empty($addCondition['search'])) {
            $search = trim($addCondition['search']);

            $barangDataQry
                ->groupStart()
                ->like(
                    "CONCAT(barang_master.barang_name,' ',barang_master_spesifikasi.spesifikasi)",
                    $search,
                    'both',
                    null,
                    true // 🔥 RAW
                )
                ->orLike(
                    "CONCAT(barang_master.barang_name_alias,' ',barang_master_spesifikasi.spesifikasi_alias)",
                    $search,
                    'both',
                    null,
                    true // 🔥 RAW
                )
                ->groupEnd();
        }

        /* ===============================
     * TOTAL FILTERED
     * =============================== */
        $totalFilteredData = $barangDataQry->countAllResults(false);

        /* ===============================
     * FINAL DATA
     * =============================== */
        $data = $barangDataQry
            ->orderBy($sort, $sortType)
            ->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType,
        ];
    }
}
