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
                    GROUP_CONCAT(DISTINCT penerimaan_barang.tanggal ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS lpb_date,
                    GROUP_CONCAT(DISTINCT penerimaan_barang.no_penerimaan_barang ORDER BY penerimaan_barang.tanggal ASC SEPARATOR ", ") AS no_penerimaan_barang,
                    penerimaan_barang_detail.penerimaan_barang_id,
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
                ->orderBy('penerimaan_barang.id', 'asc')
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
                penerimaan_barang_detail.penerimaan_barang_id,
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
                ->orderBy('penerimaan_barang.id', 'asc')
                ->groupBy('penerimaan_barang_detail.barang_id')
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
                ->orderBy('penerimaan_barang.id', 'asc')
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
                ->orderBy('penerimaan_barang.id', 'asc')
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

        // ============================
        // 🔍 FILTER KONDISI
        // ============================

        $where = [];
        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $where[] = "DATE(bc_purchase_order.createdAt) BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['dateStartLpb']) && !empty($condition['dateEndLpb'])) {
            $where[] = "penerimaan_barang.tanggal BETWEEN '$condition[dateStartLpb]' AND '$condition[dateEndLpb]'";
        }

        if ($condition['bc_id'] != "") {
            $where[] = "penerimaan_barang.bc_type = '$condition[bc_id]'";
            $where[] = "bc_purchase_order.no_daftar IS NOT NULL AND bc_purchase_order.no_aju IS NOT NULL";
        }

        if (!empty($condition['company_id'])) {
            $where[] = "penerimaan_barang.company_id = '$condition[company_id]' AND barang_master.company_id = '$condition[company_id]'";
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

        $search = $db->escapeLikeString($condition['search']);
        if (!empty($condition['search'])) {
            $where[] = "(
                bc_purchase_order.no_daftar LIKE '%{$search}%' 
                OR bc_purchase_order.no_aju LIKE '%{$search}%' 
                OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%' 
                OR barang_master.kode_barang LIKE '%{$search}%' 
                OR CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
            )";
        }



        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        // ============================
        // 🧾 MAPPING KOLOM UNTUK SORT
        // ============================

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
        // ============================
        // 🧩 BASE QUERY 3 UNION
        // ============================

        $baseQuery = "
        (
            -- BAHAN BAKU LOKAL
            SELECT 
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
            -- BAHAN PENOLONG LOKAL
            SELECT
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
            -- BAHAN PENOLONG IMPORT
            SELECT
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
            -- BAHAN BAKU IMPORT
            SELECT
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
            AND penerimaan_barang.status_penerimaan='BAKU'
            AND penerimaan_barang.tipe_bahan='IMPORT'
            AND bc_purchase_order.status_posting='1'      
            $filterCondition
            GROUP BY penerimaan_barang_detail.id
        )
    ";


        // ============================
        // 📊 COUNT + PAGINATION
        // ============================

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = $baseQuery . $orderBy . " LIMIT $limit OFFSET $offset";


        // var_dump($mainQuery);
        // die;
        $data = $db->query($mainQuery)->getResultArray();

        // ============================
        // 📦 RETURN RESULT
        // ============================

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }
}
