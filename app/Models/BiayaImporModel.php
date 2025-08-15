<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaImporModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_impor';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
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

    public function getDropdownPo($supplierId, $tipePo)
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $rmImportPosModel = new RMImportPOModel();


        if ($tipePo == "IMPORT BAKU") {
            // PO IMPORT BAHAN BAKU
            $poList = $rmImportPosModel
                ->select('rm_import_pos.*,suppliers.name as supplier_name, metadata.value as valas_name')
                ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
                ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                ->where('supplier_id', $supplierId)
                ->where('rm_import_pos.deletedAt', null)
                ->orderBy('rm_import_pos.id', "desc")
                ->findAll();
        } else {
            // PO IMPORT BAHAN PENOLONG
            $poList = $amPurchaseOrderModel
                ->select('am_purchase_orders.*,suppliers.name as supplier_name, metadata.value as valas_name')
                ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                ->where('supplier_id', $supplierId)
                ->where('am_purchase_orders.deletedAt', null)
                ->orderBy('am_purchase_orders.id', "desc")
                ->findAll();
        }

        // List
        $resultArr = array();
        foreach ($poList as $p) {
            array_push($resultArr, [
                'id' => $p['id'],
                'po_no' => $p['po_no'],
                'consigne' => $p['consigne'],
                'port_origin' => $p['port_origin'],
                'port_destination' => $p['port_destination'],
                'valas_name' => $p['valas_name']
            ]);
        }

        return $resultArr;
    }

    public function getDetailBarangPo($poId, $tipePo)
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $rmImportPosModel = new RMImportPOModel();
        $rmImportPosDetailModel = new RMImportPODetailModel();


        if ($tipePo == "IMPORT BAKU") {
            // PO IMPORT BAHAN BAKU
            $poList = $rmImportPosModel
                ->select('rm_import_pos.*,suppliers.name as supplier_name, metadata.value as valas_name')
                ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
                ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                ->where('rm_import_pos.id', $poId)
                ->first();

            $poBarang = $rmImportPosDetailModel
                ->select('
                    rm_import_po_details.*,
                    barang_master_spesifikasi.spesifikasi,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan
                ')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_import_po_details.spesifikasi_id', 'left')
                ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
                ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
                ->where('rm_import_po_details.deletedAt', null)
                ->where('rm_import_po_details.rm_import_po_id', $poId)
                ->findAll();
        } else {
            // PO IMPORT BAHAN PENOLONG
            $poList = $amPurchaseOrderModel
                ->select('am_purchase_orders.*,suppliers.name as supplier_name, metadata.value as valas_name')
                ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                ->where('am_purchase_orders.id', $poId)
                ->first();

            $poBarang = $amPurchaseOrderDetailModel
                ->select('
                    am_purchase_order_details.*,
                    barang_master_spesifikasi.spesifikasi,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan
                ')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
                ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
                ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
                ->where('am_purchase_order_details.deletedAt', null)
                ->where('am_purchase_order_details.am_purchase_order_id', $poId)
                ->findAll();
        }

        // PO LIST
        $poDetail = [
            'id' => $poList['id'],
            'po_no' => $poList['po_no'],
            'consigne' => $poList['consigne'],
            'port_origin' => $poList['port_origin'],
            'port_destination' => $poList['port_destination'],
            'valas_name' => $poList['valas_name'],
            'shipper' => $poList['shipper']
        ];

        $poBarangList = array();
        foreach ($poBarang as $b) {
            array_push($poBarangList, [
                'kode_barang' => $b['kode_barang'],
                'barang_name' => $b['barang_name'],
                'spesifikasi' => $b['spesifikasi'],
                'qty' => (float)$b['qty'],
                'kode_satuan' => $b['kode_satuan'],
                'harga_satuan' => (float)$b['price'],
                'total_harga' => (float)$b['total']
            ]);
        }


        return [
            'po_detail' => $poDetail,
            'po_barang' => $poBarangList
        ];
    }
}
