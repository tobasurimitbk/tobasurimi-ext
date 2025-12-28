<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasterSpesifikasiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'barang_master_spesifikasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'barang_master_id',
        'spesifikasi',
        'spesifikasi_alias',
        'is_primer',
        'satuan_1',
        'satuan_2',
        'konversi_satuan_2',
        'satuan_3',
        'konversi_satuan_3',
        'harga_pokok',
        'harga_jual',
        'supplier_terakhir',
        'harga_terakhir',
        'unit_terakhir',
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

    public function getBarangSpesifikasiByBarangMasterID($barangMasterID)
    {
        $arrCondition = [
            'barang_master_spesifikasi.deletedAt' => null,
            'barang_master_spesifikasi.barang_master_id' => $barangMasterID
        ];

        $selectQry = "barang_master_spesifikasi.*";
        $data = $this->select($selectQry)
            ->where($arrCondition)
            ->findAll();

        return $data;
    }

    public function getListBarangSpesifikasi($typeBarang)
    {
        $selectQry = "
            barang_master_spesifikasi.id,
            barang_master.kode_barang,
            barang_master.barang_name as barang,
            barang_master_spesifikasi.spesifikasi
        ";

        $dataResult1 = $this->asArray()->select($selectQry)
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
            ->where('barang_master.deletedAt', null)
            ->where('barang_master.type_barang', $typeBarang)
            ->where('barang_master.company_id', session()->get("login")->this_company_id)
            ->orderBy('barang_master.kode_barang', "ASC")
            ->findAll();

        return $dataResult1;
    }

    public function getListBarangSpesifikasiWithAccount($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'barang_master_spesifikasi.id' => 'barang_master_spesifikasi.id',
            'barang_master_spesifikasi.spesifikasi' => 'barang_master_spesifikasi.spesifikasi',
            'barang_master_spesifikasi.createdAt' => 'barang_master_spesifikasi.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'barang_master_spesifikasi.spesifikasi'] ?? 'barang_master_spesifikasi.spesifikasi';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            barang_master_spesifikasi.id,
            barang_master_spesifikasi.spesifikasi,

            account_barang.id as account_barang_id,
            account_barang.ap_id,
            account_barang.ar_id,
            account_barang.pemakaian_id,
            account_barang.kategori_id,

            akun_pembelian.no_sub as no_sub_pembelian,
            akun_penjualan.no_sub as no_sub_penjualan,
            akun_pemakaian.no_sub as no_sub_pemakaian,

            IFNULL(sab_pembelian.saldo_awal, 0) as saldo_awal_barang_pembelian,
            IFNULL(sab_penjualan.saldo_awal, 0) as saldo_awal_barang_penjualan,
            IFNULL(sab_pemakaian.saldo_awal, 0) as saldo_awal_barang_pemakaian
        ";

        $dataQry = $this->asObject()->select($selectQry)
            ->join('account_barang', 'account_barang.barang_master_spesifikasi_id = barang_master_spesifikasi.id AND account_barang.deleted_at IS NULL', 'left')

            // Join akun
            ->join('sub_akuns as akun_pembelian', 'akun_pembelian.id = account_barang.ap_id AND account_barang.deleted_at IS NULL', 'left')
            ->join('sub_akuns as akun_penjualan', 'akun_penjualan.id = account_barang.ar_id AND account_barang.deleted_at IS NULL', 'left')
            ->join('sub_akuns as akun_pemakaian', 'akun_pemakaian.id = account_barang.pemakaian_id AND account_barang.deleted_at IS NULL', 'left')

            // Join saldo awal PENTING!
            ->join('saldo_awal_barang as sab_pembelian', 'sab_pembelian.coa_id = account_barang.ap_id AND sab_pembelian.barang_master_spesifikasi_id = barang_master_spesifikasi.id AND sab_pembelian.deletedAt IS NULL', 'left')
            ->join('saldo_awal_barang as sab_penjualan', 'sab_penjualan.coa_id = account_barang.ar_id AND sab_penjualan.barang_master_spesifikasi_id = barang_master_spesifikasi.id AND sab_penjualan.deletedAt IS NULL', 'left')
            ->join('saldo_awal_barang as sab_pemakaian', 'sab_pemakaian.coa_id = account_barang.pemakaian_id AND sab_pemakaian.barang_master_spesifikasi_id = barang_master_spesifikasi.id AND sab_pemakaian.deletedAt IS NULL', 'left')

            ->where($condition)
            ->groupBy('barang_master_spesifikasi.id');

        if (!empty($addCondition['kode_barang'])) {
            $dataQry->where('barang_master_spesifikasi.barang_master_id', $addCondition['kode_barang']);
        }

        if (!empty($addCondition['company_id'])) {
            $dataQry->groupStart()
                ->where('account_barang.company_id', $addCondition['company_id'])
                ->orWhere('account_barang.id', null) // artinya belum punya account
                ->groupEnd();
        }

        if (!empty($addCondition['kode_department'])) {
            $dataQry->groupStart()
                ->where('account_barang.divisi_id', $addCondition['kode_department'])
                ->orWhere('account_barang.id', null) // artinya belum punya account
                ->groupEnd();
        }

        if (!empty($addCondition['transaksi_date'])) {
            // var_dump($addCondition['transaksi_date'], $addCondition['company_id'], $addCondition['kode_department'], $addCondition['kode_barang']);
            $dataQry->groupStart()
                ->groupStart()
                ->where('sab_pembelian.tanggal_transaksi', $addCondition['transaksi_date'])
                ->where('sab_pembelian.company_id', $addCondition['company_id'])
                ->where('sab_pembelian.divisi_id', $addCondition['kode_department'])
                ->where('sab_pembelian.barang_master_id', $addCondition['kode_barang'])
                ->groupEnd()
                ->orWhere('sab_pembelian.id', null)
                ->groupEnd();

            $dataQry->groupStart()
                ->groupStart()
                ->where('sab_penjualan.tanggal_transaksi', $addCondition['transaksi_date'])
                ->where('sab_penjualan.company_id', $addCondition['company_id'])
                ->where('sab_penjualan.divisi_id', $addCondition['kode_department'])
                ->where('sab_penjualan.barang_master_id', $addCondition['kode_barang'])
                ->groupEnd()
                ->orWhere('sab_penjualan.id', null)
                ->groupEnd();

            $dataQry->groupStart()
                ->groupStart()
                ->where('sab_pemakaian.tanggal_transaksi', $addCondition['transaksi_date'])
                ->where('sab_pemakaian.company_id', $addCondition['company_id'])
                ->where('sab_pemakaian.divisi_id', $addCondition['kode_department'])
                ->where('sab_pemakaian.barang_master_id', $addCondition['kode_barang'])
                ->groupEnd()
                ->orWhere('sab_pemakaian.id', null)
                ->groupEnd();
        }

        // $filteredBuilder = clone $dataQry;
        $totalFilteredData = $dataQry->countAllResults(false);

        // === Total Data Keseluruhan ===
        $totalData = $this->db->table('barang_master_spesifikasi')
            ->select('barang_master_spesifikasi.id')
            ->countAllResults();

        // === Get Data ===
        $data = $dataQry
            ->limit($limit, $offset)
            ->orderBy($sort, $sortType)
            ->get()
            ->getResult();

        return [
            'data' => $data,
            'totalFilteredData' => $totalFilteredData,
            'totalData' => $totalData
        ];
    }
}
