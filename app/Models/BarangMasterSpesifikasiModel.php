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

        $sort = $availableSort[$addCondition['sort'] ?? 'barang_master_spesifikasi.createdAt'] ?? 'barang_master_spesifikasi.createdAt';
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
            akun_pembelian.nama_sub as nama_sub_pembelian,
            akun_penjualan.no_sub as no_sub_penjualan,
            akun_penjualan.nama_sub as nama_sub_penjualan,
            akun_pemakaian.no_sub as no_sub_pemakaian,
            akun_pemakaian.nama_sub as nama_sub_pemakaian,
        ";

        $dataQry = $this->asObject()->select($selectQry)
            ->join('account_barang', 'account_barang.barang_master_spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('sub_akuns as akun_pembelian', 'akun_pembelian.id = account_barang.ar_id', 'left')
            ->join('sub_akuns as akun_penjualan', 'akun_penjualan.id = account_barang.ap_id', 'left')
            ->join('sub_akuns as akun_pemakaian', 'akun_pemakaian.id = account_barang.pemakaian_id', 'left')
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
