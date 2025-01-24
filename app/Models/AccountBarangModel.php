<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'account_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'barang_master_id',
        'barang_master_spesifikasi_id',
        'keterangan',
        'company_id',
        'ap_id',
        'ar_id',
        'pemakaian_id',
        'kategori_id',
        'divisi_id',
        'deleted_at',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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

    public function getAccountBarangForJurnal($condition = [])
    {
        $select =   "account_barang.*";
        return $this->asObject()
            ->select($select)
            ->where($condition)
            ->where('account_barang.deleted_at', null)
            ->findAll();
    }

    public function getListForAccount($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode_barang'       => 'barang_master.kode_barang',
            'barang_name'       => 'barang_master.barang_name'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'account_barang.id, divisis.divisi';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "barang_master.kode_barang,
                  barang_master.barang_name,
                  account_barang.*,
                  divisis.divisi,
                  barang_master_spesifikasi.spesifikasi";

        $barangDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->where('account_barang.deleted_at', null)
            ->join('barang_master', 'barang_master.id = account_barang.barang_master_id', 'left')
            ->join('divisis', 'divisis.id = account_barang.divisi_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = account_barang.barang_master_spesifikasi_id', 'left')
            ->groupBy('account_barang.id') // Pastikan hasil spesifik untuk setiap account_barang
            ->orderBy($sort, $sortType);

        $totalData = $barangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['filter_divisi']) {
            $barangDataQry->groupStart();
        }

        if ($addCondition['filter_divisi'] && $addCondition['filter_divisi'] != "") {
            $barangDataQry->where('divisis.id', $addCondition['filter_divisi']);
        }

        if ($addCondition['search'] && $addCondition['search'] != "") {
            $barangDataQry->like('barang_master.barang_name', $addCondition['search']);
        }

        if ($addCondition['search'] && $addCondition['search'] != "") {
            $barangDataQry->orLike('barang_master.kode_barang', $addCondition['search']);
        }

        if ($addCondition['search'] || $addCondition['filter_divisi']) {
            $barangDataQry->groupEnd();
        }

        $totalFilteredData = $barangDataQry->countAllResults(false);
        $data = $barangDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function checkAccountBarangCOA($company_id, $divisi_id, $barang_id)
    {
        $accountBarang = $this->asArray()
            ->where('company_id', $company_id)
            ->where('divisi_id', $divisi_id)
            ->where('barang_master_id', $barang_id)
            ->where('deleted_at', null)
            ->where('ap_id', null)
            ->where('ar_id', null)
            ->first();

        if ($accountBarang) {
            return true;
        } else {
            return false;
        }
    }

    public function checkAccountBarang($company_id, $divisi_id, $barang_id, $spesifikasi_id, $keterangan)
    {
        $accountBarang = $this->asArray()
            ->where('company_id', $company_id)
            ->where('divisi_id', $divisi_id)
            ->where('barang_master_id', $barang_id)
            ->where('barang_master_spesifikasi_id', $spesifikasi_id)
            ->where('keterangan', $keterangan)
            ->where('deleted_at', null)
            ->first();

        if ($accountBarang == null) {
            return false;
        } else {
            return true;
        }
    }

    public function insertAccountBarang($company_id, $divisi_id, $barang_id, $spesifikasi_id, $keterangan = null)
    {
        if ($this->checkAccountBarang($company_id, $divisi_id, $barang_id, $spesifikasi_id, $keterangan)) {
        } else {
            // BELUM ADA
            $accountBarang = $this->insert([
                'company_id' => $company_id,
                'divisi_id' => $divisi_id,
                'barang_master_id' => $barang_id,
                'barang_master_spesifikasi_id' => $spesifikasi_id,
                'keterangan' => $keterangan,
            ]);
            return $accountBarang;
        }
    }
}
