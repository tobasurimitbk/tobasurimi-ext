<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaImporBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_impor_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
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

    public function getListBarang($biayaImporId)
    {
        $selectQry = "
            biaya_impor_barang.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            metadata.value as valas_name,
            satuans.kode_satuan
        ";
        $resQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = biaya_impor_barang.spesifikasi_id', 'left')
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
            ->join('metadata', 'metadata.id = biaya_impor_barang.valas_id', 'left')
            ->join('satuans', 'satuans.id = biaya_impor_barang.satuan_id', 'left')
            ->where('biaya_impor_barang.biaya_impor_id', $biayaImporId)
            ->where('biaya_impor_barang.deletedAt', null)
            ->findAll();

        return $resQry;
    }
}
