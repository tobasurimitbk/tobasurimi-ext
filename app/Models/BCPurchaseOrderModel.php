<?php

namespace App\Models;

use CodeIgniter\Model;

class BCPurchaseOrderModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_purchase_order';
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

    public function findByNoAjuOrDaftar($tipe, $search, $companyId)
    {
        $data = [];
        if ($tipe == "IMPORT") {
            // JOIN BC 2.3
            $dataResult = $this->select('bc_purchase_order.id, bc_purchase_order.no_daftar, bc_23.no_aju')
                ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
                ->where('bc_purchase_order.company_id', $companyId)
                ->like('bc_purchase_order.no_daftar', '%' . $search . '%')
                ->orLike('bc_23.no_aju', '%' . $search . '%')
                ->limit(10)
                ->findAll();
        } else {
            // JOIN BC 4.0
            $dataResult = $this->select('bc_purchase_order.id, bc_purchase_order.no_daftar, bc_40.no_aju')
                ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
                ->where('bc_purchase_order.company_id', $companyId)
                ->like('bc_purchase_order.no_daftar', '%' . $search . '%')
                ->orLike('bc_40.no_aju', '%' . $search . '%')
                ->limit(10)
                ->findAll();
        }

        foreach ($dataResult as $d) {
            $noDaftar = $d['no_daftar'] == null ? "-" : $d['no_daftar'];
            array_push($data, [
                'id' => $d['id'],
                'text' => $d['no_aju'] . " / " . $noDaftar
            ]);
        }

        return $data;
    }

    public function findDetail($bcPurchaseOrderID)
    {
        $data = $this->select('suppliers.name AS supplier_name, bc_purchase_order.*')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->where('bc_purchase_order.id', $bcPurchaseOrderID)
            ->first();
        return $data;
    }

    public function findDetailBarangWithSpek($bcPurchaseOrderID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $first = $this->find($bcPurchaseOrderID);
        $bcPenerimaanBarangIDArr = json_decode($first['multiple_lpb_id']);

        if ($first['po_type'] == "LOKAL BAKU") {
            // PO LOKAL BAHAN BAKU
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                penerimaan_barang_detail.barang_id,
                penerimaan_barang_detail.spesifikasi_id,
                rm_purchase_orders.po_no,
                rm_purchase_orders.po_date,
                rm_purchase_orders.total_before_pph as sub_total,
                CONCAT(
                    barang_master.barang_name, " ", 
                    IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ", "), "")
                ) AS barang_name, 
                barang_master.kode_barang,
                satuan_lpb.kode_satuan as kode_satuan_lpb,
                satuan_po.kode_satuan as kode_satuan_po
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                ->join('satuans satuan_lpb', 'satuan_lpb.id = penerimaan_barang_detail.unit_konversi', 'left')
                ->join('satuans satuan_po', 'satuan_po.id = penerimaan_barang_detail.unit', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                // ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                ->groupBy('penerimaan_barang_detail.penerimaan_barang_id')
                ->groupBy('penerimaan_barang_detail.barang_id')
                ->findAll();
        } else if ($first['po_type'] == "LOKAL PENOLONG") {
            // PO LOKAL BAHAN PENOLONG
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                penerimaan_barang_detail.spesifikasi_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                purchase_requests.spp_no,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang_name,
                barang_master.kode_barang,
                barang_master.kode_barang,
                barang_master_spesifikasi.spesifikasi,
                satuan_lpb.kode_satuan as kode_satuan_lpb,
                satuan_po.kode_satuan as kode_satuan_po
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
                ->join('satuans satuan_lpb', 'satuan_lpb.id = penerimaan_barang_detail.unit_konversi', 'left')
                ->join('satuans satuan_po', 'satuan_po.id = penerimaan_barang_detail.unit', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                // ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                ->groupBy('penerimaan_barang_id')
                ->groupBy('penerimaan_barang_detail.spesifikasi_id')
                ->findAll();
        } elseif ($first['po_type'] == "IMPORT BAKU") {
            // PO IMPORT BAHAN BAKU
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                penerimaan_barang_detail.spesifikasi_id,
                rm_import_pos.po_no,
                rm_import_pos.po_date,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang_name,
                barang_master.kode_barang,
                satuan_lpb.kode_satuan as kode_satuan_lpb,
                satuan_po.kode_satuan as kode_satuan_po,
                metadata.value as valas
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_import_pos', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                ->join('satuans satuan_lpb', 'satuan_lpb.id = penerimaan_barang_detail.unit_konversi', 'left')
                ->join('satuans satuan_po', 'satuan_po.id = penerimaan_barang_detail.unit', 'left')
                ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                // ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                ->groupBy('penerimaan_barang_id')
                ->groupBy('penerimaan_barang_detail.spesifikasi_id')
                ->findAll();
        } elseif ($first['po_type'] == "IMPORT PENOLONG") {
            // PO IMPORT BAHAN PENOLONG
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                penerimaan_barang_detail.spesifikasi_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi) AS barang_name,
                barang_master.kode_barang,
                satuan_lpb.kode_satuan as kode_satuan_lpb,
                satuan_po.kode_satuan as kode_satuan_po,
                metadata.value as valas,
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                ->join('satuans satuan_lpb', 'satuan_lpb.id = penerimaan_barang_detail.unit_konversi', 'left')
                ->join('satuans satuan_po', 'satuan_po.id = penerimaan_barang_detail.unit', 'left')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                // ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                ->groupBy('penerimaan_barang_id')
                ->groupBy('penerimaan_barang_detail.spesifikasi_id')
                ->findAll();
        }

        $result = array();

        foreach ($po as $p) {
            $result[] = [
                'penerimaan_barang_id' => $p['penerimaan_barang_id'],
                'penerimaan_barang_detail_id' => $p['penerimaan_barang_detail_id'],
                'barang1_id' => $p['barang_id'],
                'spesifikasi_id' => $p['spesifikasi_id'],
                'lpb_date' => date('d/m/Y', strtotime($p['lpb_date'])),
                'lpb_no' => $p['no_penerimaan_barang'],
                'spp_no' => isset($p['spp_no']) ? $p['spp_no'] : "",
                'purchase_order_id' => $p['purchase_order_id'],
                'qty_lpb' => $p['qty_lpb'],
                'qty_lpb_konversi' => $p['qty_lpb_konversi'],
                'qty_po' => $p['qty_po'],
                'barang_id' => $p['barang_id'],
                'po_no' => $p['po_no'],
                'po_date' => date('d/m/Y', strtotime($p['po_date'])),
                'barang_name' => $p['barang_name'],
                'kode_barang' => $p['kode_barang'],
                'harga' => $p['sub_total'],
                'kode_satuan_lpb' => $p['kode_satuan_lpb'],
                'kode_satuan_po' => $p['kode_satuan_po'],
                'valas' => isset($p['valas']) ? $p['valas'] : ""
            ];
        }

        return $result;
    }


    public function findDetailBarang($bcPurchaseOrderID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $first = $this->find($bcPurchaseOrderID);
        $bcPenerimaanBarangIDArr = json_decode($first['multiple_lpb_id']);
        $bcPurchaseOrderIDArr = json_decode($first['multiple_po_id']);

        if ($first['po_type'] == "LOKAL BAKU") {
            // PO LOKAL BAHAN BAKU
            $po = $penerimaanBarangModel
                ->select('
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    rm_purchase_orders.total_before_pph AS sub_total,
                    penerimaan_barang_detail.barang_id,
                    rm_purchase_orders.po_no,
                    rm_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang
                ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->findAll();
        } else if ($first['po_type'] == "LOKAL PENOLONG") {
            // PO LOKAL BAHAN PENOLONG
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                barang_master.barang_name,
                barang_master.kode_barang
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->findAll();
        } elseif ($first['po_type'] == "IMPORT BAKU") {
            // PO IMPORT BAHAN BAKU
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                rm_import_pos.po_no,
                rm_import_pos.po_date,
                barang_master.barang_name,
                barang_master.kode_barang,
                satuans.kode_satuan AS kode_satuan_lpb,
                metadata.value AS valas
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_import_pos', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
                ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->findAll();
        } elseif ($first['po_type'] == "IMPORT PENOLONG") {
            // PO IMPORT BAHAN PENOLONG
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                barang_master.barang_name,
                barang_master.kode_barang,
                satuans.kode_satuan AS kode_satuan_lpb,
                metadata.value AS valas
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->whereIn('penerimaan_barang_id', $bcPenerimaanBarangIDArr)
                ->whereIn('penerimaan_barang_detail.purchase_order_id', $bcPurchaseOrderIDArr)
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->findAll();
        }

        $result = array();

        foreach ($po as $p) {
            $result[] = [
                'penerimaan_barang_id' => $p['penerimaan_barang_id'],
                'penerimaan_barang_detail_id' => $p['penerimaan_barang_detail_id'],
                'barang1_id' => $p['barang_id'],
                'lpb_date' => date('d/m/Y', strtotime($p['lpb_date'])),
                'lpb_no' => $p['no_penerimaan_barang'],
                'purchase_order_id' => $p['purchase_order_id'],
                'qty_lpb' => $p['qty_lpb'],
                'qty_lpb_konversi' => $p['qty_lpb_konversi'],
                'qty_po' => $p['qty_po'],
                'barang_id' => $p['barang_id'],
                'po_no' => $p['po_no'],
                'po_date' => date('d/m/Y', strtotime($p['po_date'])),
                'barang_name' => $p['barang_name'],
                'kode_barang' => $p['kode_barang'],
                'harga' => $p['sub_total'],
                'kode_satuan_lpb' => isset($p['kode_satuan_lpb']) ? $p['kode_satuan_lpb'] : "",
                'valas' => isset($p['valas']) ? $p['valas'] : ""
            ];
        }

        return $result;
    }

    public function findDetailDokumenBarang($bcPurchaseOrderID, $penerimaanBarangID, $barang1ID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $rmPurchaseOrderDetailModel = new RMImportPODetailModel();

        $first = $this->find($bcPurchaseOrderID);

        if ($first['po_type'] == "LOKAL BAKU") {
            // PO LOKAL BAHAN BAKU
            $po = $penerimaanBarangModel
                ->select('
                    kemasan.name AS kemasan_name,
                    penerimaan_barang.kemasan_id,
                    penerimaan_barang.jumlah_kemasan,
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    rm_purchase_orders.total_before_pph AS sub_total,
                    penerimaan_barang_detail.barang_id,
                    rm_purchase_orders.po_no,
                    rm_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang
                ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('kemasan', 'kemasan.id = penerimaan_barang.kemasan_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangID)
                ->where('penerimaan_barang_detail.barang_id', $barang1ID)
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->first();

            // CARI BIAYA TAMBAHAN (Karena Purchase Bahan Baku Tidak Ada Diskon dan Biaya Tambahan)
            $biayaTambahan = 0;
            $diskon = 0;
            $hargaSebelumDiskon = $po['sub_total'];
        } else if ($first['po_type'] == "LOKAL PENOLONG") {
            // PO LOKAL BAHAN PENOLONG
            $po = $penerimaanBarangModel
                ->select('
                kemasan.name AS kemasan_name,
                penerimaan_barang.kemasan_id,
                penerimaan_barang.jumlah_kemasan,
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                barang_master.barang_name,
                barang_master.kode_barang
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('kemasan', 'kemasan.id = penerimaan_barang.kemasan_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangID)
                ->where('penerimaan_barang_detail.barang_id', $barang1ID)
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->first();

            // CARI BIAYA TAMBAHAN 
            $poDetail = $amPurchaseOrderDetailModel->select(
                '
                SUM(price) AS total_harga_sebelum_diskon,
                SUM(disc) AS total_diskon, 
                SUM(additional_cost) AS total_biaya_tambahan'
            )
                ->where('barang_id', $po['barang_id'])
                ->where('am_purchase_order_id', $po['purchase_order_id'])
                ->groupBy('barang_id')
                ->first();

            $biayaTambahan = $poDetail == null ? 0 : $poDetail['total_biaya_tambahan'];
            $diskon = $poDetail == null ? 0 : $poDetail['total_harga_sebelum_diskon'] * ($poDetail['total_diskon'] / 100);
            $hargaSebelumDiskon = $poDetail == null ? 0 : $poDetail['total_harga_sebelum_diskon'];
        } elseif ($first['po_type'] == "IMPORT BAKU") {
            // IMPORT BAHAN BAKU
            $po = $penerimaanBarangModel
                ->select('
                kemasan.name AS kemasan_name,
                penerimaan_barang.kemasan_id,
                penerimaan_barang.jumlah_kemasan,                
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                rm_import_pos.po_no,
                rm_import_pos.po_date,
                barang_master.barang_name,
                barang_master.kode_barang
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_import_pos', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('kemasan', 'kemasan.id = penerimaan_barang.kemasan_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangID)
                ->where('penerimaan_barang_detail.barang_id', $barang1ID)
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->first();

            // CARI BIAYA TAMBAHAN 
            $poDetail = $rmPurchaseOrderDetailModel->select(
                '
                SUM(price) AS total_harga_sebelum_diskon,
                SUM(disc) AS total_diskon, 
                SUM(additional_cost) AS total_biaya_tambahan'
            )
                ->where('barang_id', $po['barang_id'])
                ->where('rm_import_po_id', $po['purchase_order_id'])
                ->groupBy('barang_id')
                ->first();

            $biayaTambahan = $poDetail == null ? 0 : $poDetail['total_biaya_tambahan'];
            $diskon = $poDetail == null ? 0 : $poDetail['total_harga_sebelum_diskon'] * ($poDetail['total_diskon'] / 100);
            $hargaSebelumDiskon = $poDetail == null ? 0 : $poDetail['total_harga_sebelum_diskon'];
        } elseif ($first['po_type'] == "IMPORT PENOLONG") {
            $po = $penerimaanBarangModel
                ->select('
                    kemasan.name AS kemasan_name,
                    penerimaan_barang.kemasan_id,
                    penerimaan_barang.jumlah_kemasan,                    
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                    penerimaan_barang_detail.barang_id,
                    am_purchase_orders.po_no,
                    am_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang
                ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('kemasan', 'kemasan.id = penerimaan_barang.kemasan_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.supplier_id', $first['supplier_id'])
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangID)
                ->where('penerimaan_barang_detail.barang_id', $barang1ID)
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->first();

            // CARI BIAYA TAMBAHAN 
            $poDetail = $amPurchaseOrderDetailModel->select(
                '
                SUM(price) AS total_harga_sebelum_diskon,
                SUM(disc) AS total_diskon, 
                SUM(additional_cost) AS total_biaya_tambahan'
            )
                ->where('barang_id', $po['barang_id'])
                ->where('am_purchase_order_id', $po['purchase_order_id'])
                ->groupBy('barang_id')
                ->first();

            $biayaTambahan = $poDetail == null ? 0 : $poDetail['total_biaya_tambahan'];
            $diskon = $poDetail == null ? 0 : $poDetail['total_harga_sebelum_diskon'] * ($poDetail['total_diskon'] / 100);
            $hargaSebelumDiskon = $poDetail == null ? 0 : $poDetail['total_harga_sebelum_diskon'];
        }

        $result = array();

        $result = [
            'kemasan_name' => strtoupper($po['kemasan_name']),
            'jumlah_kemasan' => $po['jumlah_kemasan'],
            'penerimaan_barang_id' => $po['penerimaan_barang_id'],
            'penerimaan_barang_detail_id' => $po['penerimaan_barang_detail_id'],
            'barang1_id' => $po['barang_id'],
            'lpb_date' => date('d/m/Y', strtotime($po['lpb_date'])),
            'lpb_no' => $po['no_penerimaan_barang'],
            'purchase_order_id' => $po['purchase_order_id'],
            'qty_lpb' => $po['qty_lpb'],
            'qty_po' => $po['qty_po'],
            'barang_id' => $po['barang_id'],
            'po_no' => $po['po_no'],
            'po_date' => date('d/m/Y', strtotime($po['po_date'])),
            'barang_name' => $po['barang_name'],
            'kode_barang' => $po['kode_barang'],
            'harga' => $po['sub_total'],
            'biaya_tambahan' => $biayaTambahan,
            'diskon' => $diskon,
            'harga_sebelum_diskon' => $hargaSebelumDiskon
        ];

        return $result;
    }

    public function dropdownKemasan($bcPurchaseOrderID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $first = $this->find($bcPurchaseOrderID);
        $penerimaanBarangIdArr = json_decode($first['multiple_lpb_id']);

        $result = $penerimaanBarangModel->select(
            '
            penerimaan_barang.kemasan_id AS kemasan_id, 
            SUM(penerimaan_barang.jumlah_kemasan) AS jumlah_kemasan, 
            kemasan.name AS kemasan_name,
            kemasan.kode AS kode_kemasan'
        )
            ->join('kemasan', 'kemasan.id = penerimaan_barang.kemasan_id', 'left')
            ->whereIn('penerimaan_barang.id', $penerimaanBarangIdArr)
            ->where('penerimaan_barang.deletedAt', null)
            ->where('kemasan.deletedAt', null)
            ->groupBy('penerimaan_barang.kemasan_id')
            ->findAll();

        $uniqueResults = [];
        $kemasanIds = [];
        foreach ($result as $item) {
            if (!in_array($item['kemasan_id'], $kemasanIds)) {
                $uniqueResults[] = $item;
                $kemasanIds[] = $item['kemasan_id'];
            }
        }

        return $uniqueResults;
    }

    public function getPenerimaanBarangListReportBc23($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_aju'      => 'bc_23.no_aju'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'bc_purchase_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "bc_purchase_order.*, 
        bc_23.no_aju";
        $bcPurchaseOrderDataQry = $this->asObject()
            ->select($selectQry)
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->where('bc_purchase_order.status_posting', '1')
            ->where('bc_purchase_order.company_id', $addCondition['company_id'])
            ->where('bc_purchase_order.deletedAt', null)
            ->groupStart()
            ->where('bc_purchase_order.po_type', 'IMPORT BAKU')
            ->orWhere('bc_purchase_order.po_type', 'IMPORT PENOLONG')
            ->groupEnd()
            ->orderBy($sort, $sortType);

        $totalData = $bcPurchaseOrderDataQry->countAllResults(false);

        // if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->groupStart();
        // }


        // if ($addCondition['dateStart']) {
        //     $bcPurchaseOrderDataQry->where('DATE(bc_purchase_order.createdAt) >=', $addCondition['dateStart']);
        // }

        // if ($addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->where('DATE(bc_purchase_order.createdAt) <=', $addCondition['dateEnd']);
        // }

        // if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->groupEnd();
        // }

        $totalFilteredData = $bcPurchaseOrderDataQry->countAllResults(false);
        if ($limit != 10) {
            $data = $bcPurchaseOrderDataQry->findAll($limit, $offset);
        } else {
            $data = $bcPurchaseOrderDataQry->findAll();
        }


        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportBc40($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_aju'      => 'bc_40.no_aju'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'bc_purchase_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "bc_purchase_order.*, 
        bc_40.no_aju";
        $bcPurchaseOrderDataQry = $this->asObject()
            ->select($selectQry)
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->where('bc_purchase_order.status_posting', '1')
            ->where('bc_purchase_order.company_id', $addCondition['company_id'])
            ->where('bc_purchase_order.deletedAt', null)
            ->groupStart()
            ->where('bc_purchase_order.po_type', 'LOKAL BAKU')
            ->orWhere('bc_purchase_order.po_type', 'LOKAL PENOLONG')
            ->groupEnd()
            ->orderBy($sort, $sortType);

        $totalData = $bcPurchaseOrderDataQry->countAllResults(false);

        $totalFilteredData = $bcPurchaseOrderDataQry->countAllResults(false);
        if ($limit != 10) {
            $data = $bcPurchaseOrderDataQry->findAll($limit, $offset);
        } else {
            $data = $bcPurchaseOrderDataQry->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportNoPabean($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'lpb_date'      => 'penerimaan_barang.tanggal'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $penerimaanBarangModel = new PenerimaanBarangModel();

        $selectQry = "penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang.multiple_po_id,
                    penerimaan_barang.status_penerimaan,
                    penerimaan_barang.tipe_bahan,
                    penerimaan_barang.status_post,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi";
        $penerimaanBarangDataQry = $penerimaanBarangModel->asObject()
            ->select($selectQry)
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->where('penerimaan_barang.company_id', $addCondition['company_id'])
            ->where('penerimaan_barang.bc_type', '0')
            ->where('penerimaan_barang.status_post', 'FINISH')
            ->where('penerimaan_barang.deletedAt', null)
            ->where('penerimaan_barang_detail.deletedAt', null)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_divisi'] || $addCondition['filter_supplier'] || $addCondition['filter_barang']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['dateStart']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['filter_divisi']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.divisi_id', $addCondition['filter_divisi']);
        }

        if ($addCondition['filter_supplier']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.supplier_id', $addCondition['filter_supplier']);
        }

        if ($addCondition['filter_barang']) {
            $penerimaanBarangDataQry->where('penerimaan_barang_detail.spesifikasi_id', $addCondition['filter_barang']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_divisi'] || $addCondition['filter_supplier'] || $addCondition['filter_barang']) {
            $penerimaanBarangDataQry->groupEnd();
        }

        $totalFilteredData = $penerimaanBarangDataQry->countAllResults(false);
        if ($limit != 10) {
            $data = $penerimaanBarangDataQry->findAll($limit, $offset);
        } else {
            $data = $penerimaanBarangDataQry->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportBc23PDF($addCondition)
    {
        $availableSort = [
            'no_aju'      => 'bc_23.no_aju'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'bc_purchase_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "bc_purchase_order.*, 
        bc_23.no_aju";
        $bcPurchaseOrderDataQry = $this->asObject()
            ->select($selectQry)
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->where('bc_purchase_order.status_posting', '1')
            ->where('bc_purchase_order.company_id', $addCondition['company_id'])
            ->groupStart()
            ->where('bc_purchase_order.po_type', 'IMPORT BAKU')
            ->orWhere('bc_purchase_order.po_type', 'IMPORT PENOLONG')
            ->groupEnd()
            ->orderBy($sort, $sortType);

        $totalData = $bcPurchaseOrderDataQry->countAllResults(false);

        // if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->groupStart();
        // }


        // if ($addCondition['dateStart']) {
        //     $bcPurchaseOrderDataQry->where('DATE(bc_purchase_order.createdAt) >=', $addCondition['dateStart']);
        // }

        // if ($addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->where('DATE(bc_purchase_order.createdAt) <=', $addCondition['dateEnd']);
        // }

        // if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->groupEnd();
        // }

        $totalFilteredData = $bcPurchaseOrderDataQry->countAllResults(false);
        $data = $bcPurchaseOrderDataQry->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportBc40PDF($addCondition)
    {
        $availableSort = [
            'no_aju'      => 'bc_40.no_aju'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'bc_purchase_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "bc_purchase_order.*, 
        bc_40.no_aju";
        $bcPurchaseOrderDataQry = $this->asObject()
            ->select($selectQry)
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->where('bc_purchase_order.status_posting', '1')
            ->where('bc_purchase_order.company_id', $addCondition['company_id'])
            ->groupStart()
            ->where('bc_purchase_order.po_type', 'LOKAL BAKU')
            ->orWhere('bc_purchase_order.po_type', 'LOKAL PENOLONG')
            ->groupEnd()
            ->orderBy($sort, $sortType);

        $totalData = $bcPurchaseOrderDataQry->countAllResults(false);

        // if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->groupStart();
        // }


        // if ($addCondition['dateStart']) {
        //     $bcPurchaseOrderDataQry->where('DATE(bc_purchase_order.createdAt) >=', $addCondition['dateStart']);
        // }

        // if ($addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->where('DATE(bc_purchase_order.createdAt) <=', $addCondition['dateEnd']);
        // }

        // if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
        //     $bcPurchaseOrderDataQry->groupEnd();
        // }

        $totalFilteredData = $bcPurchaseOrderDataQry->countAllResults(false);
        $data = $bcPurchaseOrderDataQry->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportNoPabeanPDF($addCondition)
    {
        $availableSort = [
            'lpb_date'      => 'penerimaan_barang.tanggal'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $penerimaanBarangModel = new PenerimaanBarangModel();

        $selectQry = "penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    penerimaan_barang.multiple_po_id,
                    penerimaan_barang.status_penerimaan,
                    penerimaan_barang.tipe_bahan,
                    penerimaan_barang.status_post,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.nama_barang_dok,
                    penerimaan_barang_detail.qty,
                    penerimaan_barang_detail.jml_masuk,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan,
                    warehouses.warehouse_name, 
                    divisis.divisi";
        $penerimaanBarangDataQry = $penerimaanBarangModel->asObject()
            ->select($selectQry)
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('satuans', 'penerimaan_barang_detail.unit = satuans.id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->where('penerimaan_barang.company_id', $addCondition['company_id'])
            ->where('penerimaan_barang.bc_type', '0')
            ->where('penerimaan_barang.status_post', 'FINISH')
            ->where('penerimaan_barang.deletedAt', null)
            ->where('penerimaan_barang_detail.deletedAt', null)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanBarangDataQry->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanBarangDataQry->groupEnd();
        }

        $totalFilteredData = $penerimaanBarangDataQry->countAllResults(false);
        $data = $penerimaanBarangDataQry->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getListOutstandingBC40($condition, $addCondition, $limit = 10, $offset = 0) {}
}
