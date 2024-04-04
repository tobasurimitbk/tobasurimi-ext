<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaUdangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_udang';
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

    public function dropdownPenerimaanSuratJalan()
    {
        $divisiModel = new DivisisModel();
        $divisiArr = array();

        foreach ($divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $result = $this
            ->select('jasa_vendor_in.*,divisis.divisi,vendors.name')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = biaya_udang.jasa_vendor_in_id', 'right')
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where('biaya_udang.jasa_vendor_in_id', null)
            ->whereIn('divisi_id', $divisiArr)
            ->findAll();

        return $result;
    }

    public function dropdownBarang($jasaVendorInID, $id = null)
    {
        $jasaVendorInDetailModel = new JasaVendorInDetailModel();

        if ($id == null) {
            // CREATE
            $selectQryJasaVendorDetail = "
            barang_master.id AS barang_master_id,
            barang_master_spesifikasi.id AS barang_master_spesifikasi_id,
            jasa_vendor_in.tanggal AS tanggal_masuk,
            jasa_vendor_out.tanggal AS tanggal_keluar,
            SUM(jasa_vendor_in_detail.qty_bersih) as qty_bersih,
            SUM(jasa_vendor_out_detail.qty) as qty_rebus,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi
        ";

            $jasaVendorInDetail = $jasaVendorInDetailModel
                ->select($selectQryJasaVendorDetail)
                ->join('jasa_vendor_out_detail', 'jasa_vendor_out_detail.id = jasa_vendor_in_detail.jasa_vendor_out_detail_id')
                ->join('jasa_vendor_in', 'jasa_vendor_in.id = jasa_vendor_in_detail.jasa_vendor_in_id')
                ->join('jasa_vendor_out', 'jasa_vendor_out.id = jasa_vendor_out_detail.jasa_vendor_out_id')
                ->join('stock', 'stock.id = jasa_vendor_in_detail.stock_in_id')
                ->join('barang_master', 'barang_master.id = stock.barang1_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
                ->where('jasa_vendor_in_id', $jasaVendorInID)
                ->groupBy('jasa_vendor_in_detail.stock_in_id')
                ->findAll();

            for ($i = 0; $i < count($jasaVendorInDetail); $i++) {
                $jasaVendorInDetail[$i]['tanggal_masuk'] = date('d/m/Y', strtotime($jasaVendorInDetail[$i]['tanggal_masuk']));
                $jasaVendorInDetail[$i]['tanggal_keluar'] = date('d/m/Y', strtotime($jasaVendorInDetail[$i]['tanggal_keluar']));
            }

            return $jasaVendorInDetail;
        } else {
            // UPDATE
        }
    }
}
