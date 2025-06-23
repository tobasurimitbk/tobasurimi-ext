<?php

namespace App\Models;

use CodeIgniter\Model;

class JasaVendorInDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jasa_vendor_in_detail';
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

    public function getJasaVendorInDetail($condition)
    {
        $selectQry = "
            satuans.kode_satuan,
            vendors.name as nama_vendor,
            jasa_vendor_in.tanggal,
            jasa_vendor_in.no_penerimaan_surat_jalan,
            jasa_vendor_in_detail.stock_in_id,
            jasa_vendor_in_detail.bc_in_id,
            jasa_vendor_in_detail.no_aju_in,
            jasa_vendor_in_detail.stock_dokumen
        ";

        $dataResult = $this->asArray()->select($selectQry)
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = jasa_vendor_in_detail.jasa_vendor_in_id', 'left')
            ->join('stock', 'stock.id = jasa_vendor_in_detail.stock_in_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where($condition)
            ->findAll();

        return $dataResult;
    }
}
