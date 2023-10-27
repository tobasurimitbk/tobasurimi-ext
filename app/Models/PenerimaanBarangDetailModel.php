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
        'harga',
        'sub_total',
        'keterangan',
        'barang_id',
        'qty',
        'ppn',
        'pph',
        'unit',
        'nama_barang_dok',
        'jml_masuk',
        'packaging',
        'packaging_type',
        'packaging_qty',
        'summarized_qty',
        'createdAt',
        'updatedAt',
        'deletedAt'
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

    public function getPenerimaanBarangDetailList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $selectQry = "penerimaan_barang_detail.*, warehouses.warehouse_name, metadata.value as aju_type, 
        suppliers.name as supplier_name, penerimaan_barang.validation_date,
        barangs.kode_barang, 
        barangs.nama_barang, 
        packaging.nama_barang as nama_packaging,
        penerimaan_barang.tipe_bahan,
        satuans.nama_satuan,
        satuans.kode_satuan,
        penerimaan_barang.no_penerimaan_barang, penerimaan_barang.multiple_po_no";
        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('barangs as packaging', 'packaging.id = penerimaan_barang_detail.packaging', 'left')
            ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang.aju_document_type', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
            ->where($condition);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['status'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangDataQry->like('penerimaan_barang.no_penerimaan', $addCondition['search']);
        }

        if ($addCondition['status']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.status_post', $addCondition['status']);
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.validation_date >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.validation_date <=', $addCondition['lastdate']);
        }

        if ($addCondition['search'] || $addCondition['status'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupEnd();
        }

        $totalFilteredData = $penerimaanBarangDataQry->countAllResults(false);
        $data = $penerimaanBarangDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => '',
            'sortType'  => ''
        ];
    }

    public function getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, $status_penerimaan)
    {
        $arrCondition = [
            'penerimaan_barang_detail.deletedAt' => null,
            'penerimaan_barang_detail.penerimaan_barang_id' => $id
        ];

        $builder = $this->db->table('penerimaan_barang_detail');

        if ($status_penerimaan === "LOKAL") {
            if ($tipe_bahan === "BAKU") {
                $builder->select(
                    'penerimaan_barang_detail.*, satuans.id as id_satuan, 
                satuans.nama_satuan, 
                barangs.kode_barang, 
                barangs.nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                rm_purchase_order_details.qty_diterima,
                rm_purchase_order_details.remaining_qty,
                packaging.nama_barang as nama_packaging,
                rm_purchase_orders.po_no, 
                rm_purchase_orders.status_penerimaan,
                purchase_requests.spp_no'
                )
                    ->where($arrCondition)
                    ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id')
                    ->join('barangs as packaging', 'packaging.id = penerimaan_barang_detail.packaging', 'LEFT')
                    ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                    ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                    ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                    ->join('rm_purchase_order_details', 'rm_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT')
                    ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'LEFT')
                    ->join('purchase_requests', 'purchase_requests.id = rm_purchase_orders.purchase_request_id', 'LEFT');
                $query = $builder->get();
            }
            if ($tipe_bahan === "PENOLONG") {
                $builder->select(
                    'penerimaan_barang_detail.*, satuans.id as id_satuan, 
                satuans.nama_satuan, 
                barangs.kode_barang, 
                barangs.nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                am_purchase_order_details.qty_diterima,
                am_purchase_order_details.remaining_qty,
                packaging.nama_barang as nama_packaging,
                am_purchase_orders.po_no,
                am_purchase_orders.status_penerimaan,
                purchase_requests.spp_no'
                )
                    ->where($arrCondition)
                    ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id')
                    ->join('barangs as packaging', 'packaging.id = penerimaan_barang_detail.packaging', 'LEFT')
                    ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                    ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                    ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                    ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT')
                    ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'LEFT')
                    ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'LEFT');
                $query = $builder->get();
            }
        }

        if ($status_penerimaan === "IMPORT") {
            if ($tipe_bahan === "BAKU") {
                $builder->select(
                    'penerimaan_barang_detail.*, satuans.id as id_satuan, 
                satuans.nama_satuan, 
                barangs.kode_barang, 
                barangs.nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                rm_import_po_details.qty_diterima,
                rm_import_po_details.remaining_qty,
                packaging.nama_barang as nama_packaging,
                rm_import_pos.po_no,
                rm_import_pos.status_penerimaan,
                purchase_requests.spp_no'
                )
                    ->where($arrCondition)
                    ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id')
                    ->join('barangs as packaging', 'packaging.id = penerimaan_barang_detail.packaging', 'LEFT')
                    ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                    ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                    ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                    ->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT')
                    ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'LEFT')
                    ->join('purchase_requests', 'purchase_requests.id = rm_import_pos.purchase_request_id', 'LEFT');
                $query = $builder->get();
            }
            if ($tipe_bahan === "PENOLONG") {
                $builder->select(
                    'penerimaan_barang_detail.*, satuans.id as id_satuan, 
                satuans.nama_satuan, 
                barangs.kode_barang, 
                barangs.nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                am_purchase_order_details.qty_diterima,
                am_purchase_order_details.remaining_qty,
                packaging.nama_barang as nama_packaging,
                am_purchase_orders.po_no,
                am_purchase_orders.status_penerimaan,
                purchase_requests.spp_no'
                )
                    ->where($arrCondition)
                    ->join('barangs', 'barangs.id = penerimaan_barang_detail.barang_id')
                    ->join('barangs as packaging', 'packaging.id = penerimaan_barang_detail.packaging', 'LEFT')
                    ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                    ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                    ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                    ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT')
                    ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'LEFT')
                    ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'LEFT');
                $query = $builder->get();
            }
        }

        return $query->getResultArray();
    }
}
