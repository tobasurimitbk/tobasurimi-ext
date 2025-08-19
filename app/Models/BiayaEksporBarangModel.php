<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaEksporBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_ekspor_barang';
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

    public function getListBarang($biayaEksporId)
    {
        $resQry = $this->asArray()
            ->select(
                '
                    biaya_ekspor_barang.*,
                    metadata.value as valas_name,
                    satuans.kode_satuan,
                    barang_master_sales.kode_barang,
                    barang_master_sales.barang_name
                '
            )
            ->join('metadata', 'metadata.id = biaya_ekspor_barang.valas_id', 'left')
            ->join('satuans', 'satuans.id = biaya_ekspor_barang.satuan_id', 'left')
            ->join('barang_master_sales', 'barang_master_sales.id = biaya_ekspor_barang.barang_master_sales_id', 'left')
            ->where('biaya_ekspor_barang.biaya_ekspor_id', $biayaEksporId)
            ->where('biaya_ekspor_barang.deletedAt', null)
            ->findAll();

        return $resQry;
    }
}
