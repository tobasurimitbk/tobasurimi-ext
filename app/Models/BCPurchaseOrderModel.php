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
                ->groupBy('penerimaan_barang_detail.penerimaan_barang_id')
                ->groupBy('penerimaan_barang_detail.spesifikasi_id')
                ->groupBy('penerimaan_barang_detail.purchase_order_id')
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
                    GROUP_CONCAT(DISTINCT penerimaan_barang.tanggal ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS lpb_date,
                    GROUP_CONCAT(DISTINCT penerimaan_barang.no_penerimaan_barang ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS no_penerimaan_barang,
                    MIN(penerimaan_barang_detail.penerimaan_barang_id) AS penerimaan_barang_id,
                    penerimaan_barang_detail.barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    GROUP_CONCAT(DISTINCT penerimaan_barang_detail.purchase_order_id) AS purchase_order_ids,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    GROUP_CONCAT(DISTINCT rm_purchase_orders.po_no ORDER BY rm_purchase_orders.po_date ASC SEPARATOR ", ") AS po_no,
                    GROUP_CONCAT(DISTINCT rm_purchase_orders.po_date ORDER BY rm_purchase_orders.po_date ASC SEPARATOR ", ") AS po_date
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
                ->orderBy('penerimaan_barang_detail.id', 'asc')
                ->groupBy('penerimaan_barang_detail.barang_id')
                ->findAll();

            foreach ($po as &$row) {
                // Pecah semua purchase_order_id dari GROUP_CONCAT
                $purchaseOrderIds = explode(',', $row['purchase_order_ids']);

                // Query SUM total_before_pph dari semua PO terkait barang ini
                $sum = $this->db->table('rm_purchase_orders')
                    ->selectSum('total_before_pph')
                    ->whereIn('id', $purchaseOrderIds)
                    ->get()
                    ->getRow()
                    ->total_before_pph;

                $row['sub_total'] = $sum ?? 0;
            }
        } else if ($first['po_type'] == "LOKAL PENOLONG") {
            // PO LOKAL BAHAN PENOLONG
            $po = $penerimaanBarangModel
                ->select('
                GROUP_CONCAT(DISTINCT penerimaan_barang.tanggal ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS lpb_date,
                GROUP_CONCAT(DISTINCT penerimaan_barang.no_penerimaan_barang ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS no_penerimaan_barang,
                MIN(penerimaan_barang_detail.penerimaan_barang_id) AS penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                penerimaan_barang_detail.qty AS qty_po,
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
                ->orderBy('penerimaan_barang_detail.id', 'asc')
                ->groupBy('penerimaan_barang_detail.barang_id')
                ->findAll();
        } elseif ($first['po_type'] == "IMPORT BAKU") {
            // PO IMPORT BAHAN BAKU
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                MIN(penerimaan_barang_detail.penerimaan_barang_id) AS penerimaan_barang_id,
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
                ->orderBy('penerimaan_barang_detail.id', 'asc')
                ->groupBy('barang_id')
                ->groupBy('penerimaan_barang_id')
                ->findAll();
        } elseif ($first['po_type'] == "IMPORT PENOLONG") {
            // PO IMPORT BAHAN PENOLONG
            $po = $penerimaanBarangModel
                ->select('
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                MIN(penerimaan_barang_detail.penerimaan_barang_id) AS penerimaan_barang_id,
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
                ->orderBy('penerimaan_barang_detail.id', 'asc')
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
                'lpb_date' => $p['lpb_date'],
                'lpb_no' => $p['no_penerimaan_barang'],
                'purchase_order_id' => $p['purchase_order_id'],
                'qty_lpb' => $p['qty_lpb'],
                'qty_lpb_konversi' => $p['qty_lpb_konversi'],
                'qty_po' => $p['qty_po'],
                'barang_id' => $p['barang_id'],
                'po_no' => $p['po_no'],
                'po_date' => $p['po_date'],
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
        $bcPurchaseOrderModel = new BCPurchaseOrderModel();

        $first = $this->find($bcPurchaseOrderID);
        $bcPurchaseOrder = $bcPurchaseOrderModel
            ->where('id', $bcPurchaseOrderID)
            ->first();
        $penerimaanBarangIdArr = \json_decode($bcPurchaseOrder['multiple_lpb_id']);

        if ($first['po_type'] == "LOKAL BAKU") {
            // PO LOKAL BAHAN BAKU
            $po = $penerimaanBarangModel
                ->select('
                    kemasan.name AS kemasan_name,
                    penerimaan_barang.kemasan_id,
                    SUM(penerimaan_barang.jumlah_kemasan) AS jumlah_kemasan,
                    GROUP_CONCAT(DISTINCT penerimaan_barang.tanggal ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS lpb_date,
                    GROUP_CONCAT(DISTINCT penerimaan_barang.no_penerimaan_barang ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS no_penerimaan_barang,
                    GROUP_CONCAT(DISTINCT penerimaan_barang_detail.purchase_order_id) AS purchase_order_ids,
                    GROUP_CONCAT(DISTINCT penerimaan_barang_detail.penerimaan_barang_id) AS penerimaan_barang_ids,
                    penerimaan_barang_detail.penerimaan_barang_id,
                    penerimaan_barang_detail.purchase_order_id,
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    0 AS sub_total,
                    penerimaan_barang_detail.barang_id,
                    GROUP_CONCAT(DISTINCT rm_purchase_orders.po_no ORDER BY rm_purchase_orders.po_date ASC SEPARATOR ", ") AS po_no,
                    GROUP_CONCAT(DISTINCT rm_purchase_orders.po_date ORDER BY rm_purchase_orders.po_date ASC SEPARATOR ", ") AS po_date,
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
                ->where('penerimaan_barang_detail.barang_id', $barang1ID)
                ->whereIn('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangIdArr)
                ->groupBy('barang_id')
                ->first();

            if ($po) {
                $purchaseOrderIds = explode(',', $po['purchase_order_ids']);
                $penerimaanBarangIds = explode(',', $po['penerimaan_barang_ids']);

                if (!empty($purchaseOrderIds)) {
                    $sum = $this->db->table('rm_purchase_orders')
                        ->selectSum('total_before_pph')
                        ->whereIn('id', $purchaseOrderIds)
                        ->get()
                        ->getRow()
                        ->total_before_pph;

                    $sumKemasan = $this->db->table('penerimaan_barang')
                        ->selectSum('jumlah_kemasan')
                        ->whereIn('id', $penerimaanBarangIds)
                        ->get()
                        ->getRow()
                        ->jumlah_kemasan;

                    $po['jumlah_kemasan'] = $sumKemasan ?? 0;
                    $po['sub_total'] = $sum ?? 0;
                }
            }


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
                SUM(penerimaan_barang.jumlah_kemasan) AS jumlah_kemasan,
                GROUP_CONCAT(DISTINCT penerimaan_barang.tanggal ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS lpb_date,
                GROUP_CONCAT(DISTINCT penerimaan_barang.no_penerimaan_barang ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS no_penerimaan_barang,
                penerimaan_barang_detail.penerimaan_barang_id,
                penerimaan_barang_detail.purchase_order_id,
                penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                GROUP_CONCAT(DISTINCT am_purchase_orders.po_no ORDER BY am_purchase_orders.po_date ASC SEPARATOR ", ") AS po_no,
                GROUP_CONCAT(DISTINCT am_purchase_orders.po_date ORDER BY am_purchase_orders.po_date ASC SEPARATOR ", ") AS po_date,
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
                ->whereIn('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangIdArr)
                ->where('penerimaan_barang_detail.barang_id', $barang1ID)
                ->groupBy('barang_id')
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
                barang_master.kode_barang,
                metadata.value as valas
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_import_pos', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('kemasan', 'kemasan.id = penerimaan_barang.kemasan_id', 'left')
                ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
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
                    barang_master.kode_barang,
                    metadata.value as valas
                ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('kemasan', 'kemasan.id = penerimaan_barang.kemasan_id', 'left')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
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
            'lpb_date' => $po['lpb_date'],
            'lpb_no' => $po['no_penerimaan_barang'],
            'purchase_order_id' => $po['purchase_order_id'],
            'qty_lpb' => $po['qty_lpb'],
            'qty_po' => $po['qty_po'],
            'barang_id' => $po['barang_id'],
            'po_no' => $po['po_no'],
            'po_date' => $po['po_date'],
            'barang_name' => $po['barang_name'],
            'kode_barang' => $po['kode_barang'],
            'harga' => $po['sub_total'],
            'biaya_tambahan' => $biayaTambahan,
            'diskon' => $diskon,
            'harga_sebelum_diskon' => $hargaSebelumDiskon,
            'valas' => isset($po['valas']) ? $po['valas'] : ""
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

    public function getListLapPemasukanBarang(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        $db = \Config\Database::connect();
        $where = [];
        $whereBc27In = [];

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $where[] = "DATE(bc_purchase_order.createdAt) BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereBc27In[] = "DATE(bc_27.createdAt) BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['dateStartLpb']) && !empty($condition['dateEndLpb'])) {
            $where[] = "penerimaan_barang.tanggal BETWEEN '$condition[dateStartLpb]' AND '$condition[dateEndLpb]'";
        }

        if (!empty($condition['barang_master_id'])) {
            $where[] = "penerimaan_barang_detail.barang_id = '$condition[barang_master_id]'";
            $whereBc27In[] = "stock_revamp.barang_master_id = '$condition[barang_master_id]'";
        }

        if (!empty($condition['company_id'])) {
            $where[] = "penerimaan_barang.company_id = '$condition[company_id]' AND barang_master.company_id = '$condition[company_id]'";
            $whereBc27In[] = "penerimaan_mutasi_global.company_penerima_id = '$condition[company_id]'";
        }
        if (!empty($condition['divisi_id'])) {
            $where[] = "penerimaan_barang.divisi_id = '$condition[divisi_id]'";
        }
        if (!empty($condition['warehouse_id'])) {
            $where[] = "penerimaan_barang.warehouse_id = '$condition[warehouse_id]'";
        }
        if (!empty($condition['sumber'])) {
            $sumberExplode = explode(" ", $condition['sumber']);
            $statusPenerimaan = $sumberExplode[0];
            $tipeBahan = $sumberExplode[1];
            $where[] = "penerimaan_barang.status_penerimaan = '$statusPenerimaan' AND penerimaan_barang.tipe_bahan = '$tipeBahan'";
        }

        $searchBc27In = "";
        $search = $db->escapeLikeString($condition['search']);
        if (!empty($condition['search'])) {
            $where[] = "(
                bc_purchase_order.no_daftar LIKE '%{$search}%' 
                OR bc_purchase_order.no_aju LIKE '%{$search}%' 
                OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
            )";

            $searchBc27In = "AND (
                bc_27.no_daftar LIKE '%{$search}%' 
                OR bc_27.no_aju LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                OR penerimaan_mutasi_global.penerimaan_mutasi_no LIKE '%{$search}%' 
                OR mutasi_global.no_mutasi LIKE '%{$search}%' 
            )";
        }

        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";
        $filterConditionBc27In = !empty($whereBc27In) ? " AND " . implode(" AND ", $whereBc27In) : "";

        $columns = [
            'id',
            'jenis_doc',
            'no_aju',
            'no_daftar',
            'tanggal_daftar',
            'no_penerimaan_barang',
            'tanggal_lpb',
            'no_order',
            'divisi',
            'warehouse_name',
            'supplier_name',
            'kode_barang',
            'barang_name',
            'spesifikasi',
            'qty_order',
            'qty_diterima',
            'kode_satuan',
            'valas',
            'total_harga',
            'keterangan'
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        SELECT *
            FROM (
                (
                    SELECT 
                        penerimaan_barang.bc_type AS bc_type,
                        metadata.value AS jenis_doc,
                        bc_purchase_order.no_aju,
                        bc_purchase_order.no_daftar,
                        DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                        penerimaan_barang.no_penerimaan_barang,
                        penerimaan_barang.tanggal AS tanggal_lpb,
                        rm_purchase_orders.po_no AS no_order,
                        divisis.divisi AS divisi,
                        warehouses.warehouse_name,
                        suppliers.name AS supplier_name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                        SUM(penerimaan_barang_detail.qty) AS qty_order,
                        SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
                        satuans.kode_satuan,
                        'IDR' AS valas,
                        rm_purchase_orders.total_before_pph AS total_harga,
                        '' AS keterangan
                    FROM penerimaan_barang
                    JOIN penerimaan_barang_detail ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL
                    LEFT JOIN metadata ON metadata.id = penerimaan_barang.bc_type
                    LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
                    LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
                    LEFT JOIN divisis ON divisis.id = penerimaan_barang.divisi_id 
                    LEFT JOIN warehouses ON warehouses.id = penerimaan_barang.warehouse_id
                    LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
                    LEFT JOIN barang_master ON barang_master.id = penerimaan_barang_detail.barang_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id
                    LEFT JOIN satuans ON satuans.id = penerimaan_barang_detail.unit_konversi
                    LEFT JOIN rm_purchase_orders ON rm_purchase_orders.id = penerimaan_barang_detail.purchase_order_id
                    WHERE penerimaan_barang.deletedAt IS NULL
                    AND penerimaan_barang_detail.deletedAt IS NULL
                    AND penerimaan_barang.status_post = 'FINISH'
                    AND penerimaan_barang.status_penerimaan='LOKAL'
                    AND penerimaan_barang.tipe_bahan='BAKU'
                    AND bc_purchase_order.status_posting='1'
                    $filterCondition
                    GROUP BY penerimaan_barang.id, penerimaan_barang_detail.barang_id
                )
                UNION ALL
                (
                    SELECT
                        penerimaan_barang.bc_type AS bc_type,
                        metadata_jenisdoc.value AS jenis_doc,
                        bc_purchase_order.no_aju,
                        bc_purchase_order.no_daftar,
                        DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                        penerimaan_barang.no_penerimaan_barang,
                        penerimaan_barang.tanggal AS tanggal_lpb,
                        am_purchase_orders.po_no AS no_order,
                        divisis.divisi AS divisi,
                        warehouses.warehouse_name,
                        suppliers.name AS supplier_name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                        SUM(penerimaan_barang_detail.qty) AS qty_order,
                        SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
                        satuans.kode_satuan,
                        'IDR' AS valas,
                        SUM(penerimaan_barang_detail.sub_total) AS total_harga,
                        penerimaan_barang_detail.keterangan
                    FROM penerimaan_barang
                    JOIN penerimaan_barang_detail ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL
                    LEFT JOIN metadata AS metadata_jenisdoc ON metadata_jenisdoc.id = penerimaan_barang.bc_type
                    LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
                    LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
                    LEFT JOIN divisis ON divisis.id = penerimaan_barang.divisi_id 
                    LEFT JOIN warehouses ON warehouses.id = penerimaan_barang.warehouse_id
                    LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
                    LEFT JOIN barang_master ON barang_master.id = penerimaan_barang_detail.barang_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id
                    LEFT JOIN satuans ON satuans.id = penerimaan_barang_detail.unit_konversi
                    LEFT JOIN am_purchase_orders ON am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id
                    LEFT JOIN metadata AS  metadata_valas ON metadata_valas.id = am_purchase_orders.currency
                    WHERE penerimaan_barang.deletedAt IS NULL
                    AND penerimaan_barang_detail.deletedAt IS NULL
                    AND penerimaan_barang.status_post = 'FINISH'       
                    AND penerimaan_barang.tipe_bahan='PENOLONG'
                    AND penerimaan_barang.status_penerimaan='LOKAL'  
                    AND bc_purchase_order.status_posting='1'
                    $filterCondition
                    GROUP BY penerimaan_barang_detail.id
                )
                UNION ALL
                (
                    SELECT
                        penerimaan_barang.bc_type AS bc_type,
                        metadata_jenisdoc.value AS jenis_doc,
                        bc_purchase_order.no_aju,
                        bc_purchase_order.no_daftar,
                        DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                        penerimaan_barang.no_penerimaan_barang,
                        penerimaan_barang.tanggal AS tanggal_lpb,
                        am_purchase_orders.po_no AS no_order,
                        divisis.divisi AS divisi,
                        warehouses.warehouse_name,
                        suppliers.name AS supplier_name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                        SUM(penerimaan_barang_detail.qty) AS qty_order,
                        SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
                        satuans.kode_satuan,
                        metadata_valas.value AS valas,
                        SUM(penerimaan_barang_detail.sub_total) AS total_harga,
                        penerimaan_barang_detail.keterangan
                    FROM penerimaan_barang
                    JOIN penerimaan_barang_detail ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL
                    LEFT JOIN metadata AS metadata_jenisdoc ON metadata_jenisdoc.id = penerimaan_barang.bc_type
                    LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
                    LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
                    LEFT JOIN divisis ON divisis.id = penerimaan_barang.divisi_id 
                    LEFT JOIN warehouses ON warehouses.id = penerimaan_barang.warehouse_id
                    LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
                    LEFT JOIN barang_master ON barang_master.id = penerimaan_barang_detail.barang_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id
                    LEFT JOIN satuans ON satuans.id = penerimaan_barang_detail.unit_konversi
                    LEFT JOIN am_purchase_orders ON am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id
                    LEFT JOIN metadata AS  metadata_valas ON metadata_valas.id = am_purchase_orders.currency
                    WHERE penerimaan_barang.deletedAt IS NULL
                    AND penerimaan_barang_detail.deletedAt IS NULL
                    AND penerimaan_barang.status_post = 'FINISH'    
                    AND penerimaan_barang.tipe_bahan='PENOLONG'
                    AND penerimaan_barang.status_penerimaan='IMPORT'     
                    AND bc_purchase_order.status_posting='1'  
                    $filterCondition
                    GROUP BY penerimaan_barang_detail.id
                )
                UNION ALL
                (
                    SELECT
                        penerimaan_barang.bc_type AS bc_type,
                        metadata_jenisdoc.value AS jenis_doc,
                        bc_purchase_order.no_aju,
                        bc_purchase_order.no_daftar,
                        DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                        penerimaan_barang.no_penerimaan_barang,
                        penerimaan_barang.tanggal AS tanggal_lpb,
                        rm_import_pos.po_no AS no_order,
                        divisis.divisi AS divisi,
                        warehouses.warehouse_name,
                        suppliers.name AS supplier_name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                        SUM(penerimaan_barang_detail.qty) AS qty_order,
                        SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
                        satuans.kode_satuan,
                        metadata_valas.value AS valas,
                        SUM(penerimaan_barang_detail.sub_total) AS total_harga,
                        penerimaan_barang_detail.keterangan
                    FROM penerimaan_barang
                    JOIN penerimaan_barang_detail ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL
                    LEFT JOIN metadata AS metadata_jenisdoc ON metadata_jenisdoc.id = penerimaan_barang.bc_type
                    LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
                    LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
                    LEFT JOIN divisis ON divisis.id = penerimaan_barang.divisi_id 
                    LEFT JOIN warehouses ON warehouses.id = penerimaan_barang.warehouse_id
                    LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
                    LEFT JOIN barang_master ON barang_master.id = penerimaan_barang_detail.barang_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id
                    LEFT JOIN satuans ON satuans.id = penerimaan_barang_detail.unit_konversi
                    LEFT JOIN rm_import_pos ON rm_import_pos.id = penerimaan_barang_detail.purchase_order_id
                    LEFT JOIN metadata AS metadata_valas ON metadata_valas.id = rm_import_pos.currency
                    WHERE penerimaan_barang.deletedAt IS NULL
                    AND penerimaan_barang_detail.deletedAt IS NULL
                    AND penerimaan_barang.status_post = 'FINISH'   
                    AND penerimaan_barang.status_penerimaan='IMPORT'
                    AND penerimaan_barang.tipe_bahan='BAKU'
                    AND bc_purchase_order.status_posting='1'      
                    $filterCondition
                    GROUP BY penerimaan_barang_detail.id
                )
                UNION ALL
                (
                    SELECT
                        52 AS bc_type,
                        'BC 2.7 In' AS jenis_doc,
                        bc_27.no_aju,
                        bc_27.no_daftar,
                        DATE(bc_27.createdAt) AS tanggal_daftar,
                        penerimaan_mutasi_global.penerimaan_mutasi_no AS no_penerimaan_barang,
                        DATE(bc_27.createdAt) AS tanggal_lpb,
                        '' AS no_order,
                        divisis.divisi AS divisi,
                        warehouses.warehouse_name,
                        '' AS supplier_name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        barang_master_spesifikasi.spesifikasi,
                        '' AS qty_order,
                        penerimaan_mutasi_global_detail.qty AS qty_diterima,
                        satuans.kode_satuan,
                        metadata.value AS valas,
                        mutasi_global_detail.sub_total AS total_harga,
                        '' AS keterangan
                    FROM penerimaan_mutasi_global_detail
                    LEFT JOIN penerimaan_mutasi_global ON penerimaan_mutasi_global.id = penerimaan_mutasi_global_detail.penerimaan_mutasi_global_id
                    LEFT JOIN bc_27 ON bc_27.id = penerimaan_mutasi_global.bc27_id
                    LEFT JOIN mutasi_global ON mutasi_global.id = penerimaan_mutasi_global_detail.mutasi_global_id
                    LEFT JOIN divisis ON divisis.id = penerimaan_mutasi_global.divisi_penerima_id 
                    LEFT JOIN warehouses ON warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id
                    LEFT JOIN satuans ON satuans.id = penerimaan_mutasi_global_detail.unit_hasil_id
                    LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = penerimaan_mutasi_global_detail.stock_detail_id
                    LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                    LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                    LEFT JOIN mutasi_global_detail ON mutasi_global_detail.id = penerimaan_mutasi_global_detail.mutasi_global_detail_id
                    LEFT JOIN metadata ON metadata.id = mutasi_global_detail.valas_id
                    WHERE penerimaan_mutasi_global_detail.deletedAt IS NULL
                    AND bc_27.status_posting='1'
                    $filterConditionBc27In
                    $searchBc27In
                )
            ) x
        ";

        if (!empty($condition['bc_id'])) {
            $baseQuery .= " WHERE x.bc_type = " . (int)$condition['bc_id'];
        }

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS t";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;
        $data = $db->query(
            $baseQuery . $orderBy . " LIMIT $limit OFFSET $offset"
        )->getResultArray();
        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getListLapPengeluaranBarang(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        $db = \Config\Database::connect();

        $searchBc27Out = "";
        $searchBc41 = "";
        $searchBc25 = "";
        $searchBc30 = "";

        $whereBc27Out = [];
        $whereBc41 = [];
        $whereBc25 = [];
        $whereBc30 = [];

        /* ================= DATE FILTER ================= */
        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereBc27Out[] = "DATE(bc_27.createdAt) BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereBc41[] = "bc_41.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereBc25[] = "bc_25.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereBc30[] = "bc_30.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        /* ================= COMPANY FILTER ================= */
        if (!empty($condition['company_id'])) {
            $whereBc27Out[] = "bc_27.company_asal_id='$condition[company_id]'";
            $whereBc41[] = "bc_41.company_id='$condition[company_id]'";
            $whereBc25[] = "bc_25.company_id='$condition[company_id]'";
            $whereBc30[] = "bc_30.company_id='$condition[company_id]'";
        }

        if (!empty($condition['divisi_id'])) {
            $whereBc27Out[] = "mutasi_global.divisi_asal_id='$condition[divisi_id]'";
            $whereBc41[] = "stock_revamp.divisi_id='$condition[divisi_id]'";
            $whereBc25[] = "stock_revamp.divisi_id='$condition[divisi_id]'";
            $whereBc30[] = "stock_revamp.divisi_id='$condition[divisi_id]'";
        }

        if (!empty($condition['warehouse_id'])) {
            $whereBc27Out[] = "mutasi_global.warehouse_asal_id='$condition[warehouse_id]'";
            $whereBc41[] = "stock_revamp.warehouse_id='$condition[warehouse_id]'";
            $whereBc25[] = "stock_revamp.warehouse_id='$condition[warehouse_id]'";
            $whereBc30[] = "stock_revamp.warehouse_id='$condition[warehouse_id]'";
        }

        /* ================= SEARCH ================= */
        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString($condition['search']);

            $searchBc27Out = "AND (
                bc_27.no_aju LIKE '%{$search}%'
                OR bc_27.no_daftar LIKE '%{$search}%'
                OR barang_master.kode_barang LIKE '%{$search}%'
                OR barang_master.barang_name LIKE '%{$search}%'
                OR barang_master_spesifikasi.spesifikasi LIKE '%{$search}%'
                OR divisis.divisi LIKE '%{$search}%'
                OR warehouses.warehouse_name LIKE '%{$search}%'
            )";

            $searchBc41 = $searchBc27Out;
            $searchBc25 = $searchBc27Out;
            $searchBc30 = $searchBc27Out;
        }

        $filterConditionBc27Out = !empty($whereBc27Out) ? " AND " . implode(" AND ", $whereBc27Out) : "";
        $filterConditionBc41 = !empty($whereBc41) ? " AND " . implode(" AND ", $whereBc41) : "";
        $filterConditionBc25 = !empty($whereBc25) ? " AND " . implode(" AND ", $whereBc25) : "";
        $filterConditionBc30 = !empty($whereBc30) ? " AND " . implode(" AND ", $whereBc30) : "";

        /* ================= ORDER ================= */
        $columns = [
            'id',
            'jenis_doc',
            'no_aju',
            'no_daftar',
            'tanggal_dokumen',
            'no_keluar',
            'tanggal_keluar',
            'divisi',
            'warehouse_name',
            'customer_name',
            'kode_barang',
            'barang_name',
            'spesifikasi',
            'jml_pengeluaran',
            'kode_satuan',
            'valas_name',
            'sub_total',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY {$columns[$orderColumnIndex]} $dir ";
        }

        /* ================= MAIN QUERY ================= */
        $baseQuery = "
        SELECT *
            FROM (
                (
                    SELECT 
                        mutasi_global_detail.id,
                        52 AS bc_id,
                        'BC 2.7 Out' AS jenis_doc,
                        bc_27.no_aju,
                        bc_27.no_daftar,
                        DATE(bc_27.createdAt) AS tanggal_dokumen,
                        mutasi_global.no_mutasi AS no_keluar,
                        DATE(bc_27.createdAt) AS tanggal_keluar,
                        divisis.divisi,
                        warehouses.warehouse_name,
                        companies.company AS customer_name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        barang_master_spesifikasi.spesifikasi,
                        mutasi_global_detail.qty_mutasi AS jml_pengeluaran,
                        satuans.kode_satuan,
                        metadata.value AS valas_name,
                        mutasi_global_detail.sub_total
                    FROM mutasi_global_detail
                    LEFT JOIN mutasi_global ON mutasi_global.id = mutasi_global_detail.mutasi_global_id
                    LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = mutasi_global_detail.stock_detail_id
                    LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                    LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                    LEFT JOIN bc_27 ON bc_27.mutasi_global_id = mutasi_global.id
                    LEFT JOIN satuans ON satuans.id = mutasi_global_detail.unit_id_mutasi
                    LEFT JOIN metadata ON metadata.id = mutasi_global_detail.valas_id
                    LEFT JOIN divisis ON divisis.id = mutasi_global.divisi_asal_id
                    LEFT JOIN warehouses ON warehouses.id = mutasi_global.warehouse_asal_id
                    LEFT JOIN companies ON companies.id = bc_27.company_tujuan_id
                    WHERE mutasi_global_detail.deletedAt IS NULL
                    AND bc_27.status_posting = '1'
                    $filterConditionBc27Out
                    $searchBc27Out
                )
                UNION ALL
                (
                    SELECT 
                        bc_pengeluaran_barang.id,
                        54 AS bc_id,
                        'BC 4.1',
                        bc_41.no_aju,
                        bc_41.no_daftar,
                        bc_41.tanggal,
                        bc_41.multiple_reference_no,
                        bc_41.tanggal,
                        divisis.divisi,
                        warehouses.warehouse_name,
                        customers.name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        barang_master_spesifikasi.spesifikasi,
                        bc_pengeluaran_barang.qty_keluar,
                        satuans.kode_satuan,
                        metadata.value AS valas_name,
                        bc_pengeluaran_barang.sub_total
                    FROM bc_pengeluaran_barang
                    LEFT JOIN bc_41 ON bc_41.id = bc_pengeluaran_barang.bc_pengeluaran_id
                    LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = bc_pengeluaran_barang.stock_detail_id
                    LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                    LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                    LEFT JOIN satuans ON satuans.id = bc_pengeluaran_barang.unit_id_keluar
                    LEFT JOIN metadata ON metadata.id = bc_pengeluaran_barang.valas_id
                    LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
                    LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
                    LEFT JOIN customers ON customers.id = bc_41.reference_penerima_id
                    WHERE bc_pengeluaran_barang.deletedAt IS NULL
                    AND bc_41.status_posting = '1'
                    $filterConditionBc41
                    $searchBc41
                )
                UNION ALL
                (
                    SELECT 
                        bc_pengeluaran_barang.id,
                        49 AS bc_id,
                        'BC 2.5',
                        bc_25.no_aju,
                        bc_25.no_daftar,
                        bc_25.tanggal,
                        bc_25.multiple_reference_no,
                        bc_25.tanggal,
                        divisis.divisi,
                        warehouses.warehouse_name,
                        customers.name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        barang_master_spesifikasi.spesifikasi,
                        bc_pengeluaran_barang.qty_keluar,
                        satuans.kode_satuan,
                        metadata.value AS valas_name,
                        bc_pengeluaran_barang.sub_total
                    FROM bc_pengeluaran_barang
                    LEFT JOIN bc_25 ON bc_25.id = bc_pengeluaran_barang.bc_pengeluaran_id
                    LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = bc_pengeluaran_barang.stock_detail_id
                    LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                    LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                    LEFT JOIN satuans ON satuans.id = bc_pengeluaran_barang.unit_id_keluar
                    LEFT JOIN metadata ON metadata.id = bc_pengeluaran_barang.valas_id
                    LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
                    LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
                    LEFT JOIN customers ON customers.id = bc_25.reference_penerima_id
                    WHERE bc_pengeluaran_barang.deletedAt IS NULL
                    AND bc_25.status_posting = '1'
                    $filterConditionBc25
                    $searchBc25
                )
                UNION ALL
                (
                    SELECT 
                        bc_pengeluaran_barang.id,
                        1445 AS bc_id,
                        'BC 3.0',
                        bc_30.no_aju,
                        bc_30.no_daftar,
                        bc_30.tanggal,
                        bc_30.multiple_reference_no,
                        bc_30.tanggal,
                        divisis.divisi,
                        warehouses.warehouse_name,
                        customers.name,
                        barang_master.kode_barang,
                        barang_master.barang_name,
                        barang_master_spesifikasi.spesifikasi,
                        bc_pengeluaran_barang.qty_keluar,
                        satuans.kode_satuan,
                        metadata.value AS valas_name,
                        bc_pengeluaran_barang.sub_total
                    FROM bc_pengeluaran_barang
                    LEFT JOIN bc_30 ON bc_30.id = bc_pengeluaran_barang.bc_pengeluaran_id
                    LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = bc_pengeluaran_barang.stock_detail_id
                    LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                    LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                    LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                    LEFT JOIN satuans ON satuans.id = bc_pengeluaran_barang.unit_id_keluar
                    LEFT JOIN metadata ON metadata.id = bc_pengeluaran_barang.valas_id
                    LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
                    LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
                    LEFT JOIN customers ON customers.id = bc_30.reference_penerima_id
                    WHERE bc_pengeluaran_barang.deletedAt IS NULL
                    AND bc_30.status_posting = '1'
                    $filterConditionBc30
                    $searchBc30
                )
            ) x
        ";

        /* ================= FILTER bc_id (OUTER) ================= */
        if (!empty($condition['bc_id'])) {
            $baseQuery .= " WHERE x.bc_id = " . (int)$condition['bc_id'];
        }

        $countQuery = "SELECT COUNT(*) cnt FROM ($baseQuery) t";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $data = $db->query(
            $baseQuery . $orderBy . " LIMIT $limit OFFSET $offset"
        )->getResultArray();

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getListLapWip(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        $db = \Config\Database::connect();
        $whereBahanBaku = [];
        $whereBahanPenolong = [];
        $searchBahanBaku = "";
        $searchBahanPenolong = "";

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereBahanBaku[] = "material_requests.production_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereBahanPenolong[] = "material_requests_penolong.production_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['company_id'])) {
            $whereBahanBaku[] = "material_requests.company_id = '$condition[company_id]'";
            $whereBahanPenolong[] = "material_requests_penolong.company_id = '$condition[company_id]'";
        }

        if (!empty($condition['divisi_id'])) {
            $whereBahanBaku[] = "material_request_details.divisi_tujuan_id = '$condition[divisi_id]'";
            $whereBahanPenolong[] = "material_request_penolong_details.divisi_tujuan_id = '$condition[divisi_id]'";
        }

        if (!empty($condition['warehouse_id'])) {
            $whereBahanBaku[] = "material_request_details.warehouse_tujuan_id = '$condition[warehouse_id]'";
            $whereBahanPenolong[] = "material_request_penolong_details.warehouse_tujuan_id = '$condition[warehouse_id]'";
        }

        $search = $db->escapeLikeString($condition['search']);
        if (!empty($condition['search'])) {
            $searchBahanBaku = "AND (
                bc_purchase_order.no_daftar LIKE '%{$search}%' 
                OR bc_purchase_order.no_aju LIKE '%{$search}%' 
                OR material_requests.req_no LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
            )";

            $searchBahanPenolong = "AND (
                bc_purchase_order.no_daftar LIKE '%{$search}%' 
                OR bc_purchase_order.no_aju LIKE '%{$search}%' 
                OR material_requests_penolong.req_no LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
            )";
        }

        $filterConditionBahanBaku = !empty($whereBahanBaku) ? " AND " . implode(" AND ", $whereBahanBaku) : "";
        $filterConditionBahanPenolong = !empty($whereBahanPenolong) ? " AND " . implode(" AND ", $whereBahanPenolong) : "";

        $columns = [
            'id',
            'production_date',
            'work_order_id',
            'work_order_id', // barang jadi
            'divisi',
            'warehouse_name',
            'req_no',
            'no_aju',
            'no_daftar',
            'tanggal_daftar',
            'kode_barang',
            'barang_name',
            'spesifikasi',
            'qty',
            'kode_satuan',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- BAHAN BAKU LOKAL & IMPORT
            SELECT 
                material_request_details.id,
                material_requests.production_date,
                divisis.divisi,
                warehouses.warehouse_name,
                material_requests.req_no,
                bc_purchase_order.no_aju,
                bc_purchase_order.no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                barang_master.kode_barang,
                barang_master.barang_name,
                IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                SUM(material_request_details.qty) AS qty,
                material_request_details.satuan AS kode_satuan,
                material_requests.work_order_id
            FROM material_request_details
            LEFT JOIN material_requests ON material_requests.id = material_request_details.material_request_id
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = material_request_details.stock_detail_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            LEFT JOIN divisis ON divisis.id = material_request_details.divisi_tujuan_id 
            LEFT JOIN warehouses ON warehouses.id = material_request_details.warehouse_tujuan_id
            LEFT JOIN barang_master ON barang_master.id = material_request_details.barang1_id
            LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = material_request_details.barang2_id
            WHERE material_request_details.deletedAt IS NULL
            AND stock_revamp_detail.type_bc != 'NON PABEAN' -- KECUALIKAN YANG NON PABEAN
            AND material_request_details.kondisi_barang='request'
            AND penerimaan_barang.tipe_bahan='BAKU'
            AND stock_revamp_detail.reference_type='LPB'
            $filterConditionBahanBaku
            $searchBahanBaku
            GROUP BY bc_purchase_order.no_aju, barang_master.barang_name
        )
        UNION ALL
        (
            -- BAHAN PENOLONG LOKAL & IMPORT
            SELECT 
                material_request_penolong_details.id,
                material_requests_penolong.production_date,
                divisis.divisi,
                warehouses.warehouse_name,
                material_requests_penolong.req_no,
                bc_purchase_order.no_aju,
                bc_purchase_order.no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                barang_master.kode_barang,
                barang_master.barang_name,
                IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                SUM(material_request_penolong_details.qty) AS qty,
                material_request_penolong_details.satuan AS kode_satuan,
                material_requests_penolong.work_order_id
            FROM material_request_penolong_details
            LEFT JOIN material_requests_penolong ON material_requests_penolong.id = material_request_penolong_details.material_request_id
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = material_request_penolong_details.stock_detail_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            LEFT JOIN divisis ON divisis.id = material_request_penolong_details.divisi_tujuan_id 
            LEFT JOIN warehouses ON warehouses.id = material_request_penolong_details.warehouse_tujuan_id
            LEFT JOIN barang_master ON barang_master.id = material_request_penolong_details.barang1_id
            LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = material_request_penolong_details.barang2_id
            WHERE material_request_penolong_details.deletedAt IS NULL
            AND stock_revamp_detail.type_bc != 'NON PABEAN' -- KECUALIKAN YANG NON PABEAN
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND stock_revamp_detail.reference_type='LPB'
            $filterConditionBahanPenolong
            $searchBahanPenolong
            GROUP BY bc_purchase_order.no_aju, barang_master.barang_name

        )
    ";

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = $baseQuery . $orderBy . " LIMIT $limit OFFSET $offset";
        $data = $db->query($mainQuery)->getResultArray();

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getFisikPemasukkanBarang(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        // Bc 40, Bc 23, Bc 27 In
        $db = \Config\Database::connect();
        $where = [];
        $whereBc27In = [];
        $searchPoLokalBahanBaku = "";
        $searchPoBahanPenolong = "";
        $searchPoImportBahanBaku = "";
        $searchBc27In = "";

        $where = [];

        if (!empty($condition['dateStartLpb']) && !empty($condition['dateEndLpb'])) {
            $where[] = "penerimaan_barang.tanggal BETWEEN '$condition[dateStartLpb]' AND '$condition[dateEndLpb]'";
            $whereBc27In[] = "mutasi_global.tanggal BETWEEN '$condition[dateStartLpb]' AND '$condition[dateEndLpb]'";
        }

        if (!empty($condition['barang_master_id'])) {
            $where[] = "penerimaan_barang_detail.barang_id = '$condition[barang_master_id]'";
            $whereBc27In[] = "stock_revamp.barang_master_id = '$condition[barang_master_id]'";
        }

        if (!empty($condition['spesifikasi_id'])) {
            $where[] = "penerimaan_barang_detail.spesifikasi_id='$condition[spesifikasi_id]'";
            $whereBc27In[] = "stock_revamp.spesifiksi_id='$condition[spesifikasi_id]'";
        }

        if (!empty($condition['company_id'])) {
            $where[] = "penerimaan_barang.company_id = '$condition[company_id]'";
            $whereBc27In[] = "penerimaan_mutasi_global.company_penerima_id = '$condition[company_id]'";
        }

        $search = $db->escapeLikeString($condition['search']);
        if (!empty($condition['search'])) {
            $searchPoLokalBahanBaku = "AND (
                bc_purchase_order.no_daftar LIKE '%{$search}%' 
                OR bc_purchase_order.no_aju LIKE '%{$search}%' 
                OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                OR rm_purchase_orders.po_no LIKE '%{$search}%' 
            )";

            $searchPoBahanPenolong = "AND (
                bc_purchase_order.no_daftar LIKE '%{$search}%' 
                OR bc_purchase_order.no_aju LIKE '%{$search}%' 
                OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                OR am_purchase_orders.po_no LIKE '%{$search}%' 
            )";

            $searchPoImportBahanBaku = "AND (
                bc_purchase_order.no_daftar LIKE '%{$search}%' 
                OR bc_purchase_order.no_aju LIKE '%{$search}%' 
                OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                OR rm_import_pos.po_no LIKE '%{$search}%' 
            )";

            $searchBc27In = "AND (
                bc_27.no_daftar LIKE '%{$search}%' 
                OR bc_27.no_aju LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                OR penerimaan_mutasi_global.penerimaan_mutasi_no LIKE '%{$search}%' 
                OR mutasi_global.no_mutasi LIKE '%{$search}%' 
            )";
        }

        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";
        $filterConditionBc27In = !empty($whereBc27In) ? " AND " . implode(" AND ", $whereBc27In) : "";

        $columns = [
            'tanggal_lpb',
            'divisi',
            'warehouse_name',
            'supplier_name',
            'kode_barang',
            'barang_name',
            'spesifikasi',
            'jenis_doc',
            'no_order',
            'no_penerimaan_barang',
            'tanggal_lpb',
            'no_daftar',
            'no_aju',
            'qty_diterima',
            'kode_satuan',
            'valas',
            'total_harga',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- BAHAN BAKU LOKAL
            SELECT 
                penerimaan_barang_detail.spesifikasi_id,
                penerimaan_barang_detail.barang_id,
                metadata.value AS jenis_doc,
                bc_purchase_order.no_aju,
                bc_purchase_order.no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang.tanggal AS tanggal_lpb,
                rm_purchase_orders.po_no AS no_order,
                divisis.divisi AS divisi,
                warehouses.warehouse_name,
                suppliers.name AS supplier_name,
                barang_master.kode_barang,
                barang_master.barang_name,
                IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                SUM(penerimaan_barang_detail.qty) AS qty_order,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
                satuans.kode_satuan,
                'IDR' AS valas,
                rm_purchase_orders.total_before_pph AS total_harga,
                '' AS keterangan
            FROM penerimaan_barang
            JOIN penerimaan_barang_detail ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL
            LEFT JOIN metadata ON metadata.id = penerimaan_barang.bc_type
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            LEFT JOIN divisis ON divisis.id = penerimaan_barang.divisi_id 
            LEFT JOIN warehouses ON warehouses.id = penerimaan_barang.warehouse_id
            LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
            LEFT JOIN barang_master ON barang_master.id = penerimaan_barang_detail.barang_id
            LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id
            LEFT JOIN satuans ON satuans.id = penerimaan_barang_detail.unit_konversi
            LEFT JOIN rm_purchase_orders ON rm_purchase_orders.id = penerimaan_barang_detail.purchase_order_id
            WHERE penerimaan_barang.deletedAt IS NULL
            AND penerimaan_barang_detail.deletedAt IS NULL
            AND penerimaan_barang.status_post = 'FINISH'
            AND penerimaan_barang.status_penerimaan='LOKAL'
            AND penerimaan_barang.tipe_bahan='BAKU'
            $filterCondition
            $searchPoLokalBahanBaku
            GROUP BY penerimaan_barang.id, penerimaan_barang_detail.barang_id
        )
        UNION ALL
        (
            -- BAHAN PENOLONG LOKAL
            SELECT
                penerimaan_barang_detail.spesifikasi_id,
                penerimaan_barang_detail.barang_id,
                metadata_jenisdoc.value AS jenis_doc,
                bc_purchase_order.no_aju,
                bc_purchase_order.no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang.tanggal AS tanggal_lpb,
                am_purchase_orders.po_no AS no_order,
                divisis.divisi AS divisi,
                warehouses.warehouse_name,
                suppliers.name AS supplier_name,
                barang_master.kode_barang,
                barang_master.barang_name,
                IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                SUM(penerimaan_barang_detail.qty) AS qty_order,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
                satuans.kode_satuan,
                'IDR' AS valas,
                SUM(penerimaan_barang_detail.sub_total) AS total_harga,
                penerimaan_barang_detail.keterangan
            FROM penerimaan_barang
            JOIN penerimaan_barang_detail ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL
            LEFT JOIN metadata AS metadata_jenisdoc ON metadata_jenisdoc.id = penerimaan_barang.bc_type
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            LEFT JOIN divisis ON divisis.id = penerimaan_barang.divisi_id 
            LEFT JOIN warehouses ON warehouses.id = penerimaan_barang.warehouse_id
            LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
            LEFT JOIN barang_master ON barang_master.id = penerimaan_barang_detail.barang_id
            LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id
            LEFT JOIN satuans ON satuans.id = penerimaan_barang_detail.unit_konversi
            LEFT JOIN am_purchase_orders ON am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id
            LEFT JOIN metadata AS  metadata_valas ON metadata_valas.id = am_purchase_orders.currency
            WHERE penerimaan_barang.deletedAt IS NULL
            AND penerimaan_barang_detail.deletedAt IS NULL
            AND penerimaan_barang.status_post = 'FINISH'       
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND penerimaan_barang.status_penerimaan='LOKAL'  
            $filterCondition
            $searchPoBahanPenolong
            GROUP BY penerimaan_barang_detail.id
        )
        UNION ALL
        (
            -- BAHAN PENOLONG IMPORT
            SELECT
                penerimaan_barang_detail.spesifikasi_id,
                penerimaan_barang_detail.barang_id,
                metadata_jenisdoc.value AS jenis_doc,
                bc_purchase_order.no_aju,
                bc_purchase_order.no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang.tanggal AS tanggal_lpb,
                am_purchase_orders.po_no AS no_order,
                divisis.divisi AS divisi,
                warehouses.warehouse_name,
                suppliers.name AS supplier_name,
                barang_master.kode_barang,
                barang_master.barang_name,
                IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                SUM(penerimaan_barang_detail.qty) AS qty_order,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
                satuans.kode_satuan,
                metadata_valas.value AS valas,
                SUM(penerimaan_barang_detail.sub_total) AS total_harga,
                penerimaan_barang_detail.keterangan
            FROM penerimaan_barang
            JOIN penerimaan_barang_detail ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL
            LEFT JOIN metadata AS metadata_jenisdoc ON metadata_jenisdoc.id = penerimaan_barang.bc_type
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            LEFT JOIN divisis ON divisis.id = penerimaan_barang.divisi_id 
            LEFT JOIN warehouses ON warehouses.id = penerimaan_barang.warehouse_id
            LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
            LEFT JOIN barang_master ON barang_master.id = penerimaan_barang_detail.barang_id
            LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id
            LEFT JOIN satuans ON satuans.id = penerimaan_barang_detail.unit_konversi
            LEFT JOIN am_purchase_orders ON am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id
            LEFT JOIN metadata AS  metadata_valas ON metadata_valas.id = am_purchase_orders.currency
            WHERE penerimaan_barang.deletedAt IS NULL
            AND penerimaan_barang_detail.deletedAt IS NULL
            AND penerimaan_barang.status_post = 'FINISH'    
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND penerimaan_barang.status_penerimaan='IMPORT'     
            $filterCondition
            $searchPoBahanPenolong
            GROUP BY penerimaan_barang_detail.id
        )
        UNION ALL
        (
            -- BAHAN BAKU IMPORT
            SELECT
                penerimaan_barang_detail.spesifikasi_id,
                penerimaan_barang_detail.barang_id,
                metadata_jenisdoc.value AS jenis_doc,
                bc_purchase_order.no_aju,
                bc_purchase_order.no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_daftar,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang.tanggal AS tanggal_lpb,
                rm_import_pos.po_no AS no_order,
                divisis.divisi AS divisi,
                warehouses.warehouse_name,
                suppliers.name AS supplier_name,
                barang_master.kode_barang,
                barang_master.barang_name,
                IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
                SUM(penerimaan_barang_detail.qty) AS qty_order,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
                satuans.kode_satuan,
                metadata_valas.value AS valas,
                SUM(penerimaan_barang_detail.sub_total) AS total_harga,
                penerimaan_barang_detail.keterangan
            FROM penerimaan_barang
            JOIN penerimaan_barang_detail ON penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL
            LEFT JOIN metadata AS metadata_jenisdoc ON metadata_jenisdoc.id = penerimaan_barang.bc_type
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            LEFT JOIN divisis ON divisis.id = penerimaan_barang.divisi_id 
            LEFT JOIN warehouses ON warehouses.id = penerimaan_barang.warehouse_id
            LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
            LEFT JOIN barang_master ON barang_master.id = penerimaan_barang_detail.barang_id
            LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id
            LEFT JOIN satuans ON satuans.id = penerimaan_barang_detail.unit_konversi
            LEFT JOIN rm_import_pos ON rm_import_pos.id = penerimaan_barang_detail.purchase_order_id
            LEFT JOIN metadata AS metadata_valas ON metadata_valas.id = rm_import_pos.currency
            WHERE penerimaan_barang.deletedAt IS NULL
            AND penerimaan_barang_detail.deletedAt IS NULL
            AND penerimaan_barang.status_post = 'FINISH'   
            AND penerimaan_barang.status_penerimaan='IMPORT'
            AND penerimaan_barang.tipe_bahan='BAKU'
            $filterCondition
            $searchPoImportBahanBaku
            GROUP BY penerimaan_barang_detail.id
        )
        UNION ALL
        (
            -- BC 2.7 IN
            SELECT
                stock_revamp.spesifikasi_id AS spesifikasi_id,
                stock_revamp.barang_master_id AS barang_id,
                'BC 2.7 In' AS jenis_doc,
                bc_27.no_aju,
                bc_27.no_daftar,
                penerimaan_mutasi_global.tanggal AS tanggal_daftar,
                penerimaan_mutasi_global.penerimaan_mutasi_no AS no_penerimaan_barang,
                mutasi_global.tanggal AS tanggal_lpb,
                '' AS no_order,
                divisis.divisi AS divisi,
                warehouses.warehouse_name,
                '' AS supplier_name,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS qty_order,
                penerimaan_mutasi_global_detail.qty AS qty_diterima,
                satuans.kode_satuan,
                metadata.value AS valas,
                mutasi_global_detail.sub_total AS total_harga,
                '' AS keterangan
            FROM penerimaan_mutasi_global_detail
            LEFT JOIN penerimaan_mutasi_global ON penerimaan_mutasi_global.id = penerimaan_mutasi_global_detail.penerimaan_mutasi_global_id
            LEFT JOIN bc_27 ON bc_27.id = penerimaan_mutasi_global.bc27_id
            LEFT JOIN mutasi_global ON mutasi_global.id = penerimaan_mutasi_global_detail.mutasi_global_id
            LEFT JOIN divisis ON divisis.id = penerimaan_mutasi_global.divisi_penerima_id 
            LEFT JOIN warehouses ON warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id
            LEFT JOIN satuans ON satuans.id = penerimaan_mutasi_global_detail.unit_hasil_id
            LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = penerimaan_mutasi_global_detail.stock_detail_id
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
            LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
            LEFT JOIN mutasi_global_detail ON mutasi_global_detail.id = penerimaan_mutasi_global_detail.mutasi_global_detail_id
            LEFT JOIN metadata ON metadata.id = mutasi_global_detail.valas_id
            WHERE penerimaan_mutasi_global_detail.deletedAt IS NULL
            AND bc_27.status_posting='1'
            $filterConditionBc27In
            $searchBc27In
        )
    ";

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = $baseQuery . $orderBy . " LIMIT $limit OFFSET $offset";
        $data = $db->query($mainQuery)->getResultArray();

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getFisikPemasukkanProduksi(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        $db = \Config\Database::connect();
        $where = [];
        $search = "";
        $where = [];

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $where[] = "production_results.receive_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['company_id'])) {
            $where[] = "production_results.company_id = '$condition[company_id]'";
        }

        if (!empty($condition['barang1_id'])) {
            $where[] = "production_result_details.barang1_id = '$condition[barang1_id]'";
        }

        $search = $db->escapeLikeString($condition['search']);
        if (!empty($condition['search'])) {
            $search = "AND (
                production_results.pr_no LIKE '%{$search}%'
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                OR divisis.divisi LIKE '%{$search}%' 
                OR warehouses.warehouse_name LIKE '%{$search}%'
            )";
        }

        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'id',
            'divisi',
            'warehouse_name',
            'receive_date',
            'pr_no',
            'barang_name',
            'spesifikasi',
            'qty',
            'kode_satuan',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- HASIL PRODUKSI (WAJIB BARANG JADI)
            SELECT 
                production_result_details.id,
                divisis.divisi AS divisi,
                warehouses.warehouse_name,
                production_results.receive_date,
                production_results.pr_no,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                production_result_details.qty AS qty_diterima,
                satuans.kode_satuan,
                production_result_details.barang1_id
            FROM production_result_details
            LEFT JOIN production_results ON production_results.id = production_result_details.production_result_id
            LEFT JOIN barang_master ON barang_master.id = production_result_details.barang1_id
            LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = production_result_details.barang2_id
            LEFT JOIN stock_revamp ON stock_revamp.id = production_result_details.stock_id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN divisis ON divisis.id = production_result_details.divisi_id
            LEFT JOIN warehouses ON warehouses.id = production_result_details.warehouse_id
            WHERE production_result_details.deletedAt IS NULL
            AND production_result_details.barang_type='bahan_jadi'
            $filterCondition
            $search
        )
    ";

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = $baseQuery . $orderBy . " LIMIT $limit OFFSET $offset";
        $data = $db->query($mainQuery)->getResultArray();

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getFisikPengeluaranProduksi(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        $db = \Config\Database::connect();
        $searchMrAll = ""; // bahan baku, .. bahan scrap, dari lpb
        $searchMrBp = ""; // bp
        $whereMrAll = []; // bahan baku, .. bahan scrap dari lpb
        $whereMrBp = []; // bp

        // Untuk Mr Biasa
        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereMrAll[] = "material_requests.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereMrBp[] = "material_requests_penolong.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['company_id'])) {
            $whereMrAll[] = "material_requests.company_id = '$condition[company_id]'";
            $whereMrBp[] = "material_requests_penolong.company_id ='$condition[company_id]'";
        }

        if (!empty($condition['barang1_id'])) {
            $whereMrAll[] = "material_request_details.barang1_id = '$condition[barang1_id]'";
            $whereMrBp[] = "material_request_penolong_details.barang1_id = '$condition[barang1_id]'";
        }

        $search = $db->escapeLikeString($condition['search']);
        if (!empty($condition['search'])) {
            $searchMrAll = "AND (
                material_requests.req_no LIKE '%{$search}%'
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR barang_master.barang_name LIKE '%{$search}%'
                OR divisis.divisi LIKE '%{$search}%' 
                OR warehouses.warehouse_name LIKE '%{$search}%'
                OR suppliers.name LIKE '%{$search}%'
                OR bc_purchase_order.no_aju LIKE '%{$search}%'
                OR bc_purchase_order.no_daftar LIKE '%{$search}%'
            )";
            $searchMrBp = "AND (
                material_requests_penolong.req_no LIKE '%{$search}%'
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR barang_master.barang_name LIKE '%{$search}%'
                OR divisis.divisi LIKE '%{$search}%' 
                OR warehouses.warehouse_name LIKE '%{$search}%'
                OR suppliers.name LIKE '%{$search}%'
                OR bc_purchase_order.no_aju LIKE '%{$search}%'
                OR bc_purchase_order.no_daftar LIKE '%{$search}%'
            )";
        }

        $filterConditionMrAll = !empty($whereMrAll) ? " AND " . implode(" AND ", $whereMrAll) : "";
        $filterConditionMrBp = !empty($whereMrBp) ? " AND " . implode(" AND ", $whereMrBp) : "";

        $columns = [
            'id',
            'divisi',
            'warehouse_name',
            'supplier_name',
            'req_no',
            'request_date',
            'tanggal_dokumen',
            'type_bc',
            'no_aju',
            'no_daftar',
            'kode_barang',
            'barang_name',
            'spesifikasi',
            'qty_diterima',
            'kode_satuan',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
            (
                -- MR BAKU, SCRAP, DLL
                SELECT 
                    material_request_details.id,
                    divisis.divisi AS divisi,
                    warehouses.warehouse_name,
                    suppliers.name AS supplier_name,
                    material_requests.req_no,
                    material_requests.request_date,
                    DATE(bc_purchase_order.createdAt) AS tanggal_dokumen,
                    stock_revamp_detail.type_bc,
                    bc_purchase_order.no_aju,
                    bc_purchase_order.no_daftar,
                    barang_master.kode_barang,
                    barang_master.barang_name,
                    barang_master_spesifikasi.spesifikasi,
                    material_request_details.qty AS qty_diterima,
                    material_request_details.satuan AS kode_satuan,
                    material_request_details.barang1_id
                FROM material_request_details
                LEFT JOIN material_requests ON material_requests.id = material_request_details.material_request_id
                LEFT JOIN barang_master ON barang_master.id = material_request_details.barang1_id
                LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = material_request_details.barang2_id
                LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = material_request_details.stock_detail_id
                LEFT JOIN divisis ON divisis.id = material_request_details.divisi_tujuan_id
                LEFT JOIN warehouses ON warehouses.id = material_request_details.warehouse_tujuan_id
                LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
                LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
                LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
                LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
                WHERE material_request_details.deletedAt IS NULL
                AND stock_revamp_detail.reference_type='LPB'
                $filterConditionMrAll
                $searchMrAll
            )
            UNION ALL
            (
                -- MR PENOLONG
                SELECT 
                    material_request_penolong_details.id,
                    divisis.divisi AS divisi,
                    warehouses.warehouse_name,
                    suppliers.name AS supplier_name,
                    material_requests_penolong.req_no,
                    material_requests_penolong.request_date,
                    DATE(bc_purchase_order.createdAt) AS tanggal_dokumen,
                    stock_revamp_detail.type_bc,
                    bc_purchase_order.no_aju,
                    bc_purchase_order.no_daftar,
                    barang_master.kode_barang,
                    barang_master.barang_name,
                    barang_master_spesifikasi.spesifikasi,
                    material_request_penolong_details.qty AS qty_diterima,
                    material_request_penolong_details.satuan AS kode_satuan,
                    material_request_penolong_details.barang1_id
                FROM material_request_penolong_details
                LEFT JOIN material_requests_penolong ON material_requests_penolong.id = material_request_penolong_details.material_request_id
                LEFT JOIN barang_master ON barang_master.id = material_request_penolong_details.barang1_id
                LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = material_request_penolong_details.barang2_id
                LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = material_request_penolong_details.stock_detail_id
                LEFT JOIN divisis ON divisis.id = material_request_penolong_details.divisi_tujuan_id
                LEFT JOIN warehouses ON warehouses.id = material_request_penolong_details.warehouse_tujuan_id
                LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
                LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
                LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
                LEFT JOIN suppliers ON suppliers.id = penerimaan_barang.supplier_id
                WHERE material_request_penolong_details.deletedAt IS NULL
                AND stock_revamp_detail.reference_type='LPB'
                $filterConditionMrBp
                $searchMrBp
            )
        ";

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = $baseQuery . $orderBy . " LIMIT $limit OFFSET $offset";
        $data = $db->query($mainQuery)->getResultArray();

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getFisikPengeluaranBarangPerDokumen(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        // BC 2.7 Out, BC 4.1, BC 2.5, BC 3.0, 
        $db = \Config\Database::connect();
        $searchBc27Out = "";
        $searchBc41 = "";
        $searchBc25 = "";
        $searchBc30 = "";
        $whereBc27Out = [];
        $whereBc41 = [];
        $whereBc25 = [];
        $whereBc30 = [];


        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereBc27Out[] = "mutasi_global.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereBc41[] = "bc_41.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereBc25[] = "bc_25.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereBc30[] = "bc_30.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['company_id'])) {
            $whereBc27Out[] = "bc_27.company_asal_id='$condition[company_id]'";
            $whereBc41[] = "bc_41.company_id='$condition[company_id]'";
            $whereBc25[] = "bc_25.company_id='$condition[company_id]'";
            $whereBc30[] = "bc_30.company_id='$condition[company_id]'";
        }

        if (!empty($condition['barang1_id'])) {
            $whereBc27Out[] = "stock_revamp.barang_master_id='$condition[barang1_id]'";
            $whereBc41[] = "bc_pengeluaran_barang.barang_master_id='$condition[barang1_id]'";
            $whereBc25[] = "bc_pengeluaran_barang.barang_master_id='$condition[barang1_id]'";
            $whereBc30[] = "bc_pengeluaran_barang.barang_master_id='$condition[barang1_id]'";
        }

        $search = $db->escapeLikeString($condition['search']);
        if (!empty($condition['search'])) {
            $searchBc27Out = "AND (
                bc_27.no_aju LIKE '%{$search}%'
                OR bc_27.no_daftar LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%'
                OR barang_master.barang_name LIKE '%{$search}%'
                OR barang_master_spesifikasi.spesifikasi LIKE '%{$search}%'
                OR divisis.divisi LIKE '%{$search}%' 
                OR warehouses.warehouse_name LIKE '%{$search}%'
            )";
            $searchBc41 = "AND (
                bc_41.no_aju LIKE '%{$search}%'
                OR LIKE bc_41.no_daftar LIKE '%{$search}%'
                OR barang_master.kode_barang LIKE '%{$search}%'
                OR barang_master.barang_name LIKE '%{$search}%'
                OR barang_master_spesifikasi.spesifikasi LIKE '%{$search}%'
                OR divisis.divisi LIKE '%{$search}%' 
                OR warehouses.warehouse_name LIKE '%{$search}%'
            )";
            $searchBc25 = "AND (
                bc_25.no_aju LIKE '%{$search}%'
                OR LIKE bc_25.no_daftar LIKE '%{$search}%'
                OR barang_master.kode_barang LIKE '%{$search}%'
                OR barang_master.barang_name LIKE '%{$search}%'
                OR barang_master_spesifikasi.spesifikasi LIKE '%{$search}%'
                OR divisis.divisi LIKE '%{$search}%' 
                OR warehouses.warehouse_name LIKE '%{$search}%'
            )";
            $searchBc30 = "AND (
                bc_30.no_aju LIKE '%{$search}%'
                OR LIKE bc_30.no_daftar LIKE '%{$search}%'
                OR barang_master.kode_barang LIKE '%{$search}%'
                OR barang_master.barang_name LIKE '%{$search}%'
                OR barang_master_spesifikasi.spesifikasi LIKE '%{$search}%'
                OR divisis.divisi LIKE '%{$search}%' 
                OR warehouses.warehouse_name LIKE '%{$search}%'
            )";
        }

        $filterConditionBc27Out = !empty($whereBc27Out) ? " AND " . implode(" AND ", $whereBc27Out) : "";
        $filterConditionBc41 = !empty($whereBc41) ? " AND " . implode(" AND ", $whereBc41) : "";
        $filterConditionBc25 = !empty($whereBc25) ? " AND " . implode(" AND ", $whereBc25) : "";
        $filterConditionBc30 = !empty($whereBc30) ? " AND " . implode(" AND ", $whereBc30) : "";

        $columns = [
            'id',
            'divisi',
            'warehouse_name',
            'reference_no',
            'kode_barang',
            'barang_name',
            'spesifikasi',
            'tanggal_dokumen',
            'type_bc',
            'no_aju',
            'no_daftar',
            'qty_diterima',
            'kode_satuan',
            'valas_name',
            'sub_total',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
            (
                -- BC 2.7 OUT
                SELECT 
                    mutasi_global_detail.id,
                    divisis.divisi AS divisi_asal,
                    warehouses.warehouse_name AS warehouse_asal,
                    mutasi_global.no_mutasi AS reference_no,
                    barang_master.kode_barang,
                    barang_master.barang_name,
                    barang_master_spesifikasi.spesifikasi,
                    mutasi_global.tanggal AS tanggal_dokumen,
                    'BC 2.7 Out' AS type_bc,
                    bc_27.no_aju,
                    bc_27.no_daftar,
                    mutasi_global_detail.qty_mutasi AS qty_diterima,
                    satuans.kode_satuan,
                    metadata.value AS valas_name,
                    mutasi_global_detail.sub_total,
                    stock_revamp.barang_master_id AS barang1_id
                FROM mutasi_global_detail
                LEFT JOIN mutasi_global ON mutasi_global.id = mutasi_global_detail.mutasi_global_id
                LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = mutasi_global_detail.stock_detail_id
                LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                LEFT JOIN bc_27 ON bc_27.mutasi_global_id = mutasi_global.id
                LEFT JOIN satuans ON satuans.id = mutasi_global_detail.unit_id_mutasi
                LEFT JOIN metadata ON metadata.id = mutasi_global_detail.valas_id
                LEFT JOIN divisis ON divisis.id = mutasi_global.divisi_asal_id
                LEFT JOIN warehouses ON warehouses.id = mutasi_global.warehouse_asal_id
                WHERE mutasi_global_detail.deletedAt IS NULL
                AND bc_27.status_posting='1'
                $filterConditionBc27Out
                $searchBc27Out
            )
            UNION ALL
            (
                -- BC 4.1
                SELECT 
                    bc_pengeluaran_barang.id,
                    divisis.divisi AS divisi_asal,
                    warehouses.warehouse_name AS warehouse_asal,
                    bc_41.multiple_reference_no AS reference_no,
                    barang_master.kode_barang,
                    barang_master.barang_name,
                    barang_master_spesifikasi.spesifikasi,
                    bc_41.tanggal AS tanggal_dokumen,
                    'BC 4.1' AS type_bc,
                    bc_41.no_aju,
                    bc_41.no_daftar,
                    bc_pengeluaran_barang.qty_keluar AS qty_diterima,
                    satuans.kode_satuan,
                    metadata.value AS valas_name,
                    bc_pengeluaran_barang.sub_total,
                    stock_revamp.barang_master_id AS barang1_id
                FROM bc_pengeluaran_barang
                LEFT JOIN bc_41 ON bc_41.id = bc_pengeluaran_barang.bc_pengeluaran_id
                LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = bc_pengeluaran_barang.stock_detail_id
                LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                LEFT JOIN satuans ON satuans.id = bc_pengeluaran_barang.unit_id_keluar
                LEFT JOIN metadata ON metadata.id = bc_pengeluaran_barang.valas_id
                LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
                LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
                WHERE bc_pengeluaran_barang.deletedAt IS NULL
                AND bc_pengeluaran_barang.tipe_bc='BC 4.1'
                AND bc_41.status_posting='1'
                $filterConditionBc41
                $searchBc41
            )
            UNION ALL
            (
                -- BC 2.5
                SELECT 
                    bc_pengeluaran_barang.id,
                    divisis.divisi AS divisi_asal,
                    warehouses.warehouse_name AS warehouse_asal,
                    bc_25.multiple_reference_no AS reference_no,
                    barang_master.kode_barang,
                    barang_master.barang_name,
                    barang_master_spesifikasi.spesifikasi,
                    bc_25.tanggal AS tanggal_dokumen,
                    'BC 2.5' AS type_bc,
                    bc_25.no_aju,
                    bc_25.no_daftar,
                    bc_pengeluaran_barang.qty_keluar AS qty_diterima,
                    satuans.kode_satuan,
                    metadata.value AS valas_name,
                    bc_pengeluaran_barang.sub_total,
                    stock_revamp.barang_master_id AS barang1_id
                FROM bc_pengeluaran_barang
                LEFT JOIN bc_25 ON bc_25.id = bc_pengeluaran_barang.bc_pengeluaran_id
                LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = bc_pengeluaran_barang.stock_detail_id
                LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                LEFT JOIN satuans ON satuans.id = bc_pengeluaran_barang.unit_id_keluar
                LEFT JOIN metadata ON metadata.id = bc_pengeluaran_barang.valas_id
                LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
                LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
                WHERE bc_pengeluaran_barang.deletedAt IS NULL
                AND bc_25.status_posting='1'
                AND bc_pengeluaran_barang.tipe_bc='BC 2.5'
                $filterConditionBc25
                $searchBc25
            )
            UNION ALL
            (
                -- BC 3.0
                SELECT 
                    bc_pengeluaran_barang.id,
                    divisis.divisi AS divisi_asal,
                    warehouses.warehouse_name AS warehouse_asal,
                    bc_30.multiple_reference_no AS reference_no,
                    barang_master.kode_barang,
                    barang_master.barang_name,
                    barang_master_spesifikasi.spesifikasi,
                    bc_30.tanggal AS tanggal_dokumen,
                    'BC 3.0' AS type_bc,
                    bc_30.no_aju,
                    bc_30.no_daftar,
                    bc_pengeluaran_barang.qty_keluar AS qty_diterima,
                    satuans.kode_satuan,
                    metadata.value AS valas_name,
                    bc_pengeluaran_barang.sub_total,
                    stock_revamp.barang_master_id AS barang1_id
                FROM bc_pengeluaran_barang
                LEFT JOIN bc_30 ON bc_30.id = bc_pengeluaran_barang.bc_pengeluaran_id
                LEFT JOIN stock_revamp_detail ON stock_revamp_detail.id = bc_pengeluaran_barang.stock_detail_id
                LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
                LEFT JOIN barang_master ON barang_master.id = stock_revamp.barang_master_id
                LEFT JOIN barang_master_spesifikasi ON barang_master_spesifikasi.id = stock_revamp.spesifikasi_id
                LEFT JOIN satuans ON satuans.id = bc_pengeluaran_barang.unit_id_keluar
                LEFT JOIN metadata ON metadata.id = bc_pengeluaran_barang.valas_id
                LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
                LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
                WHERE bc_pengeluaran_barang.deletedAt IS NULL
                AND bc_30.status_posting='1'
                AND bc_pengeluaran_barang.tipe_bc='BC 3.0'
                $filterConditionBc30
                $searchBc30
            )
        ";

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = $baseQuery . $orderBy . " LIMIT $limit OFFSET $offset";
        $data = $db->query($mainQuery)->getResultArray();

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }
}
