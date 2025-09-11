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
        'purchase_order_id',
        'purchase_order_details_id',
        'penerimaan_barang_id',
        'harga',
        'harga_harian',
        'harga_bulanan',
        'sub_total',
        'keterangan',
        'barang_id',
        'qty',
        'ppn',
        'pph',
        'unit',
        'nama_barang_dok',
        'jml_masuk',
        'spesifikasi_id',
        'jml_masuk_konversi',
        'unit_konversi',
        'keterangan',
        // 'packaging',
        // 'packaging_qty',
        // 'summarized_qty',
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
        barang_master.kode_barang, 
        barang_master.barang_name nama_barang, 
        penerimaan_barang.tipe_bahan,
        satuans.nama_satuan,
        satuans.kode_satuan,
        penerimaan_barang.no_penerimaan_barang, penerimaan_barang.multiple_po_no";
        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
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
                    "penerimaan_barang_detail.*, satuans.id as id_satuan, 
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                satuans.kode_satuan, 
                barang_master.kode_barang, 
                barang_master.barang_name as nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                rm_purchase_order_details.qty_diterima,
                rm_purchase_order_details.remaining_qty,
                rm_purchase_order_details.note as keterangan,
                rm_purchase_orders.po_no, 
                rm_purchase_orders.status_penerimaan,
                supplier_harga.spesifikasi,
                SUM(penerimaan_barang_detail.jml_masuk) as jml_masuk,
                GROUP_CONCAT(IF(penerimaan_barang_detail.keterangan IS NOT NULL AND penerimaan_barang_detail.keterangan != '', penerimaan_barang_detail.keterangan, NULL)) as keterangan_lpb"
                )
                    ->where($arrCondition)
                    ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'LEFT')
                    ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                    ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                    ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                    ->join('rm_purchase_order_details', 'rm_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT')
                    ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'LEFT')
                    ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'LEFT')
                    ->groupBy('penerimaan_barang_detail.penerimaan_barang_id');
                $query = $builder->get();
            }
            if ($tipe_bahan === "PENOLONG") {
                $builder->select(
                    'penerimaan_barang_detail.*, satuans.id as id_satuan, 
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                satuans.nama_satuan, 
                barang_master.kode_barang, 
                barang_master.barang_name as nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                am_purchase_order_details.*,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                am_purchase_order_details.qty_diterima,
                am_purchase_order_details.remaining_qty,
                am_purchase_orders.po_no,
                am_purchase_orders.status_penerimaan,
                purchase_requests.spp_no'
                )
                    ->where($arrCondition)
                    ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id')
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
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                satuans.nama_satuan, 
                barang_master.kode_barang, 
                barang_master.barang_name as nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                rm_import_po_details.qty_diterima,
                rm_import_po_details.remaining_qty,
                rm_import_pos.po_no,
                rm_import_pos.status_penerimaan'
                )
                    ->where($arrCondition)
                    ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id')
                    ->join('taxes as ppn', 'ppn.id = penerimaan_barang_detail.ppn', 'LEFT')
                    ->join('taxes as pph', 'pph.id = penerimaan_barang_detail.pph', 'LEFT')
                    ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'LEFT')
                    ->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_detail.purchase_order_details_id', 'LEFT')
                    ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'LEFT');
                $query = $builder->get();
            }
            if ($tipe_bahan === "PENOLONG") {
                $builder->select(
                    'penerimaan_barang_detail.*, satuans.id as id_satuan, 
                    penerimaan_barang_detail.id AS penerimaan_barang_detail_id,
                satuans.nama_satuan, 
                barang_master.kode_barang, 
                barang_master.barang_name as nama_barang,
                penerimaan_barang_detail.ppn as id_ppn,
                penerimaan_barang_detail.pph as id_pph,
                ppn.tax_value as ppn,
                pph.tax_value as pph,
                am_purchase_order_details.qty_diterima,
                am_purchase_order_details.remaining_qty,
                am_purchase_orders.po_no,
                am_purchase_orders.status_penerimaan,
                purchase_requests.spp_no'
                )
                    ->where($arrCondition)
                    ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id')
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

    public function getPenerimaanBarangPenolongDetail($id)
    {
        return $this->asArray()->select('
                    penerimaan_barang_detail.*,
                    penerimaan_barang_detail.sub_total AS total_penerimaan_detail,
                    barang_master.barang_name as nama_barang, 
                    satuans.kode_satuan, am_purchase_orders.po_no, 
                    am_purchase_order_details.note as keterangan, 
                    am_purchase_order_details.total as total_po, 
                    am_purchase_orders.currency as currency, 
                    barang_master_spesifikasi.spesifikasi, 
                    metadata.value as currencyValue,
                    purchase_requests.spp_no,
                    sum(penerimaan_barang_detail.sub_total) as total_penerimaan_detail
                ')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->where('penerimaan_barang_detail.penerimaan_barang_id', $id)
            ->where('penerimaan_barang_detail.deletedAt', null)
            ->findAll();
    }

    public function getPenerimaanBarangPenolongDetail2($id)
    {
        return $this->asArray()->select('
                    penerimaan_barang_detail.*,
                    penerimaan_barang_detail.sub_total AS total_penerimaan_detail,
                    barang_master.barang_name as nama_barang, 
                    satuans.kode_satuan, am_purchase_orders.po_no, 
                    am_purchase_order_details.note as keterangan, 
                    am_purchase_order_details.total as total_po, 
                    am_purchase_orders.currency as currency, 
                    barang_master_spesifikasi.spesifikasi, 
                    metadata.value as currencyValue,
                    purchase_requests.spp_no
                ')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->where('penerimaan_barang_detail.penerimaan_barang_id', $id)
            ->where('penerimaan_barang_detail.deletedAt', null)
            ->findAll();
    }

    public function getPenerimaanBarangBakuDetail($id)
    {
        return $this->asArray()->select('penerimaan_barang_detail.*,barang_master.barang_name as nama_barang, satuans.kode_satuan, rm_purchase_orders.po_no, rm_purchase_order_details.note as keterangan, supplier_harga.spesifikasi, rm_purchase_order_details.qty as qty_barang_po, rm_purchase_order_details.general_price as total_barang_po, barang_master_spesifikasi.spesifikasi, SUM(rm_purchase_order_details.general_price) as sum_total_barang_po, SUM(rm_purchase_order_details.qty) as sum_qty_barang_po, sum(penerimaan_barang_detail.sub_total) as total_penerimaan_detail')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->where('penerimaan_barang_detail.penerimaan_barang_id', $id)
            ->where('penerimaan_barang_detail.deletedAt', null)
            ->findAll();
    }

    public function getPenerimaanBarangImportBakuDetail($id)
    {
        return $this->asArray()->select('penerimaan_barang_detail.*,barang_master.barang_name as nama_barang, satuans.kode_satuan, rm_import_pos.po_no, rm_import_po_details.note as keterangan, rm_import_po_details.total as total_po, rm_import_pos.currency as currency, barang_master_spesifikasi.spesifikasi, metadata.value as currencyValue, SUM(rm_import_po_details.total) as sum_total_po, sum(penerimaan_barang_detail.sub_total) as total_penerimaan_detail')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
            ->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->where('penerimaan_barang_detail.penerimaan_barang_id', $id)
            ->where('penerimaan_barang_detail.deletedAt', null)
            ->findAll();
    }

    public function getHargaTotalPenerimaan($penerimaan_barang_id)
    {
        return $this->asArray()->select('SUM(penerimaan_barang_detail.sub_total) AS harga')
            ->where('deletedAt', null)
            ->where('penerimaan_barang_id', $penerimaan_barang_id)
            ->groupBy('penerimaan_barang_id')
            ->findAll();
    }

    public function historiHargaPOBahanPenolongByLpb($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'am_purchase_orders.purchase_request_id' => 'am_purchase_orders.purchase_request_id',
            'am_purchase_orders.po_no' => 'am_purchase_orders.po_no',
            'penerimaan_barang.tanggal' => 'penerimaan_barang.tanggal',
            'suppliers.name'  => 'suppliers.name',
            'barang_master_spesifikasi.spesifikasi' => 'barang_master_spesifikasi.spesifikasi',
            'penerimaan_barang_detail.sub_total'    => 'penerimaan_barang_detail.sub_total',
            'penerimaan_barang_detail.qty'    => 'penerimaan_barang_detail.qty',
            'penerimaan_barang_detail.harga'    => 'penerimaan_barang_detail.harga',
            'penerimaan_barang_detail.unit' => 'penerimaan_barang_detail.unit',
            'penerimaan_barang.divisi_id' => 'penerimaan_barang.divisi_id',
            'penerimaan_barang.no_penerimaan_barang' => 'penerimaan_barang.no_penerimaan_barang',
            'am_purchase_orders.note' => 'am_purchase_orders.note',

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang, 
            am_purchase_orders.po_no,
            penerimaan_barang.tanggal,
            am_purchase_order_details.note,
            suppliers.name as nama_supplier,
            penerimaan_barang_detail.harga, 
            penerimaan_barang_detail.qty,
            penerimaan_barang_detail.sub_total, 
            divisis.divisi,
            satuans.kode_satuan,
            purchase_requests.spp_no,
            penerimaan_barang.no_penerimaan_barang
        ";

        $dataLPB = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id  = penerimaan_barang_detail.purchase_order_id')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id  = penerimaan_barang_detail.purchase_order_details_id')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->groupBy('penerimaan_barang.id, nama_barang, po_no, tanggal, note, nama_supplier, divisi, kode_satuan, spp_no, no_penerimaan_barang')
            ->orderBy($sort, $sortType);

        $totalData = $dataLPB->countAllResults(false);

        if ($addCondition['search']) {
            $dataLPB->groupStart();
        }

        if ($addCondition['start_date'] && $addCondition['end_date']) {
            $startDate = date('Y-m-d', strtotime($addCondition['start_date']));
            $endDate = date('Y-m-d', strtotime($addCondition['end_date']));

            $dataLPB->where('penerimaan_barang.tanggal >=', $startDate)
                ->where('penerimaan_barang.tanggal <=', $endDate);
        }

        if ($addCondition['search']) {
            $dataLPB->groupStart();
            $dataLPB->like('purchase_requests.spp_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('am_purchase_orders.note', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('penerimaan_barang_detail.qty', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('satuans.kode_satuan', $addCondition['search'])
                ->orLike('penerimaan_barang_detail.harga', $addCondition['search']);
            $dataLPB->groupEnd();
        }

        if ($addCondition['search']) {
            $dataLPB->groupEnd();
        }

        $totalFilteredData = $dataLPB->countAllResults(false);
        $data = $dataLPB->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function historiHargaPOBahanPenolongByLpbFirst(
        $statusPenerimaan,
        $tipeBahan,
        $spesifikasiId,
        $companyId
    ) {
        $condition = [
            "penerimaan_barang.status_post" => "FINISH",
            "penerimaan_barang.tipe_bahan" => $tipeBahan,
            "penerimaan_barang.company_id"  => $companyId,
            "penerimaan_barang_detail.spesifikasi_id" => $spesifikasiId,
            "penerimaan_barang.deletedAt" => NULL,
            "penerimaan_barang_detail.deletedAt" => NULL,
            "penerimaan_barang.status_penerimaan" => $statusPenerimaan
        ];

        $selectQry = "
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang, 
            am_purchase_orders.po_no,
            penerimaan_barang.tanggal,
            penerimaan_barang.supplier_id,
            am_purchase_order_details.note,
            suppliers.name as nama_supplier,
            penerimaan_barang_detail.harga, 
            penerimaan_barang_detail.qty,
            penerimaan_barang_detail.sub_total, 
            divisis.divisi,
            satuans.kode_satuan,
            purchase_requests.spp_no,
            penerimaan_barang.no_penerimaan_barang
        ";

        $dataLPB = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id  = penerimaan_barang_detail.purchase_order_id')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id  = penerimaan_barang_detail.purchase_order_details_id')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->first();

        return $dataLPB;
    }

    public function getHistoriLpbBahanPenolong($statusPenerimaaan, $tipeBahan, $poDetailId)
    {
        $penerimaanBarang = $this->asArray()
            ->select('penerimaan_barang.*')
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
            ->where('penerimaan_barang.status_penerimaan', $statusPenerimaaan)
            ->where('penerimaan_barang.tipe_bahan', $tipeBahan)
            ->where('penerimaan_barang_detail.purchase_order_details_id', $poDetailId)
            ->orderBy('penerimaan_barang.tanggal', "desc")
            ->first();

        return $penerimaanBarang;
    }
}
