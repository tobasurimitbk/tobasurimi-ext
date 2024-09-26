<?php

namespace App\Models;

use CodeIgniter\Model;

class StockTutupBukuModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_tutup_buku';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function getStockTutupBukuWithAddCondition($condition)
    {
        // Extract the month and year from the date conditions
        $tanggal_awal = explode('-', $condition['tanggal_awal']);
        $bulan_awal = $tanggal_awal[2] . '/' . $tanggal_awal[1];
        $tanggal_akhir = explode('-', $condition['tanggal_akhir']);
        $bulan_akhir = $tanggal_akhir[2] . '/' . $tanggal_akhir[1];

        $conditionTutupBuku = [
            'tutup_buku.bulan >=' => $bulan_awal,
            'tutup_buku.bulan <=' => $bulan_akhir,
            'stock_tutup_buku.divisi_id' => $condition['divisi_id'],
        ];

        $selectQry = ' 
            tutup_buku.bulan,    
            UPPER(CONCAT(barang_master.barang_name, " - ", barang_master_spesifikasi.spesifikasi)) AS barang,    
            stock_tutup_buku.divisi_id,    
            stock_tutup_buku.stock_id,    
            stock_tutup_buku.barang1_id,    
            stock_tutup_buku.barang2_id,    
            satuans.kode_satuan AS satuan,    
            SUM(stock_tutup_buku.avg_harga_umum) AS harga_umum,    
            SUM(stock_tutup_buku.avg_harga_harian) AS harga_harian,    
            SUM(stock_tutup_buku.avg_harga_bulanan) AS harga_bulanan,    
            SUM(stock_tutup_buku.qty) AS stok_total    
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('tutup_buku', 'tutup_buku.id = stock_tutup_buku.tutup_buku_id')
            ->join('barang_master', 'barang_master.id = stock_tutup_buku.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_tutup_buku.barang2_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
            ->where($conditionTutupBuku)
            ->groupBy('stock_tutup_buku.stock_id')
            ->findAll();

        return $dataQry;
    }
}
