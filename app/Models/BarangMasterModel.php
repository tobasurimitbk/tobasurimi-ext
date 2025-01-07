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
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'barang_master.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        // Hitung total data tanpa filter
        $totalData = $this->where($condition)
            ->select([
                'barang_master.id',
            ])
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->countAllResults();

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
                'parent_barang.parent_name AS kelompok_barang',
            ])
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->where($condition);

        if ($addCondition['search']) {
            $barangDataQry->groupStart()
                ->like('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('parent_barang.parent_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->groupEnd();
        }

        if ($addCondition['filter_coa']) {
            $barangDataQry->join('account_barang', 'barang_master.id = account_barang.barang_master_id', 'left');

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
}
