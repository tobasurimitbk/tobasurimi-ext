<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanBarangDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_barang_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'purchase_order_details_id',
        'penerimaan_barang_id',
        'doc_qty',
        'selisih',
        'konversi',
        'harga',
        'penyerahan',
        'keterangan',
        'createdAt',
        'updatedAt',
        'deletedAt',
        'barang_id',
        'warehouse',
        'qty',
        'ppn', 
        'pph',
        'unit',
        'nama_barang_dok',
        'jml_masuk'
    ];

    // Dates
    protected $useTimestamps = true;
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

    public function getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, $status_penerimaan)
    {
        $arrCondition = [
            'penerimaan_barang_detail.deletedAt' => null,
            'penerimaan_barang_detail.penerimaan_barang_id' => $id
        ];

        $builder = $this->db->table('penerimaan_barang_detail');

        if($status_penerimaan === "LOKAL")
        {
            if($tipe_bahan === "BAKU")
            {
                $builder->select('penerimaan_barang_detail.*, satuans.id as id_satuan, 
                satuans.nama_satuan, 
                barangs.kode_barang, 
                barangs.nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                rm_purchase_order_details.spec'
                )
                ->where($arrCondition)
                ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id')
                ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                ->join('rm_purchase_order_details', 'rm_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT');
                $query = $builder->get();
            }
            if($tipe_bahan === "PENOLONG")
            {
                $builder->select('penerimaan_barang_detail.*, satuans.id as id_satuan, 
                satuans.nama_satuan, 
                barangs.kode_barang, 
                barangs.nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                am_purchase_order_details.spec'
                )
                ->where($arrCondition)
                ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id')
                ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT');
                $query = $builder->get();
            }
        }

        if($status_penerimaan === "IMPORT")
        {
            if($tipe_bahan === "BAKU")
            {
                $builder->select('penerimaan_barang_detail.*, satuans.id as id_satuan, 
                satuans.nama_satuan, 
                barangs.kode_barang, 
                barangs.nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                rm_import_po_details.spec'
                )
                ->where($arrCondition)
                ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id')
                ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                ->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT');
                $query = $builder->get();
            }
            if($tipe_bahan === "PENOLONG")
            {   
                $builder->select('penerimaan_barang_detail.*, satuans.id as id_satuan, 
                satuans.nama_satuan, 
                barangs.kode_barang, 
                barangs.nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                am_purchase_order_details.spec'
                )
                ->where($arrCondition)
                ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id')
                ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT');
                $query = $builder->get();
            }
        }
        
        return $query->getResultArray();
    }
}