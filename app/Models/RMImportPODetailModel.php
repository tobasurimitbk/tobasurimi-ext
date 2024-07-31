<?php

namespace App\Models;

use CodeIgniter\Model;

class RMImportPODetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_import_po_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'rm_import_po_id', 'barang_id', 'note', 'unit', 'qty', 'price',
        'disc', 'additional_cost', 'remaining_qty', 'qty_diterima', 'total', 'spesifikasi_id'
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

    public function getPurchaseOrderDetailByPurchaseOrderId($id)
    {
        $arrCondition = [
            'rm_import_po_details.deletedAt' => null,
            'rm_import_po_details.rm_import_po_id' => $id
        ];

        $builder = $this->db->table('rm_import_po_details')
            ->select("rm_import_po_details.*, rm_import_pos.po_no,
        FORMAT(CEILING(rm_import_po_details.qty) * CEILING(rm_import_po_details.price) + CEILING(rm_import_po_details.additional_cost), 'N', 'en-us') AS totalPrice,
        rm_import_pos.status_penerimaan, barang_master.barang_name as nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan, satuans.kode_satuan,barang_master_spesifikasi.spesifikasi")
            ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_import_po_details.spesifikasi_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailById($id)
    {
        $arrCondition = [
            'rm_import_po_details.deletedAt' => null,
            'rm_import_po_details.id' => $id
        ];

        $builder = $this->db->table('rm_import_po_details')
            ->select("rm_import_po_details.*, rm_import_pos.po_no,
        FORMAT(CEILING(rm_import_po_details.qty) * CEILING(rm_import_po_details.price) + CEILING(rm_import_po_details.additional_cost), 'N', 'en-us') AS totalPrice,
        rm_import_pos.status_penerimaan, barang_master.barang_name as nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan")
            ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getListLPBBahanBaku($rmImportPoID, $statusPenerimaan, $tipeBahan, $penerimaanBarangID = null)
    {
        $res = [];
        $condition = [
            'rm_import_pos.deletedAt' => null,
            'rm_import_po_details.deletedAt' => null
        ];

        $selectQry = "
            rm_import_pos.po_no,
            rm_import_po_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.id AS spesifikasi_id,
            satuans.kode_satuan
        ";

        $rmImportPoModel = new RMImportPOModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $barangs = $rmImportPoModel
            ->select($selectQry)
            ->where($condition)
            ->whereIn('rm_import_pos.id', $rmImportPoID)
            ->join('rm_import_po_details', 'rm_import_po_details.rm_import_po_id = rm_import_pos.id', 'left')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'rm_import_po_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->findAll();


        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaPerBarangTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {

            $allLPB = $penerimaanBarangModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->where('purchase_order_id', $b['rm_import_po_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->groupBy('purchase_order_id', 'purchase_order_details_id')
                ->findAll();

            $jmlMasukAll = 0;
            foreach ($allLPB as $a) {
                $jmlMasukAll = $a['jmlMasuk'];
            }

            $firstLPB =  $penerimaanBarangModel
                ->select('penerimaan_barang.*, penerimaan_barang_detail.*, pengembalian_barang_detail.* , penerimaan_barang_detail.id AS penerimaan_barang_detail_id, pengembalian_barang_detail.id AS pengembalian_barang_detail_id')
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('pengembalian_barang_detail', 'pengembalian_barang_detail.penerimaan_barang_detail_id = penerimaan_barang_detail.id AND pengembalian_barang_detail.deletedAt IS NULL', 'left')
                ->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('purchase_order_id', $b['rm_import_po_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            if ($penerimaanBarangID == null) {
                // CREATE
                if ($sisaDiterima != 0) {
                    $diskonHarga = ($b['disc'] / 100) * ($b['price']);
                    $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

                    $res[] = [
                        'penerimaan_barang_detail_id' => $firstLPB['penerimaan_barang_detail_id'],
                        'pengembalian_barang_detail_id' => $firstLPB['pengembalian_barang_detail_id'],
                        'jumlah_return' => isset($firstLPB['jumlah_return']) ? $firstLPB['jumlah_return'] : "",
                        'keterangan_return' => isset($firstLPB['keterangan_return']) ? $firstLPB['keterangan_return'] : "",
                        'rm_import_po_details_id' => $b['id'],
                        'rm_import_po_id' => $b['rm_import_po_id'],
                        'kode_barang' => $b['kode_barang'],
                        'nama_barang' => $b['nama_barang'] . ' - ' . $b['spesifikasi'] . '',
                        'spesifikasi_name' => $b['spesifikasi'],
                        'nama_barang_master' => $b['nama_barang'],
                        'po_no' => $b['po_no'],
                        'satuan' => $b['kode_satuan'],
                        'jml_order' => $b['qty'],
                        'jml_diterima_lpb' => $inLPB,
                        'jml_diterima_total' => $jmlMasukAll,
                        'sisa_total' => $sisaDiterima,
                        'harga' => $harga,
                        'sub_total' => ($inLPB * $harga),
                        'keterangan' => $b['note']
                    ];

                    $jmlOrderTotal += $b['qty'];
                    $jmlDiterimaInTotal += $inLPB;
                    $jmlDiterimaTotal +=   $jmlMasukAll;
                    $sisaDiterimaTotal += $sisaDiterima;
                    $hargaPerBarangTotal += $harga;
                    $subTotal += ($inLPB * $harga);
                }
            } else {
                // UPDATE
                if ($inLPB != 0) {
                    // TAMPILKAN YANG MASIH ADA SISA AJA
                    $diskonHarga = ($b['disc'] / 100) * ($b['price']);
                    $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

                    $res[] = [
                        'penerimaan_barang_detail_id' => $firstLPB['penerimaan_barang_detail_id'],
                        'pengembalian_barang_detail_id' => $firstLPB['pengembalian_barang_detail_id'],
                        'jumlah_return' => isset($firstLPB['jumlah_return']) ? $firstLPB['jumlah_return'] : "",
                        'keterangan_return' => isset($firstLPB['keterangan_return']) ? $firstLPB['keterangan_return'] : "",
                        'rm_import_po_details_id' => $b['id'],
                        'rm_import_po_id' => $b['rm_import_po_id'],
                        'kode_barang' => $b['kode_barang'],
                        'nama_barang' => $b['nama_barang'] . ' - ' . $b['spesifikasi'] . '',
                        'spesifikasi_name' => $b['spesifikasi'],
                        'nama_barang_master' => $b['nama_barang'],
                        'po_no' => $b['po_no'],
                        'satuan' => $b['kode_satuan'],
                        'jml_order' => $b['qty'],
                        'jml_diterima_lpb' => $inLPB,
                        'jml_diterima_total' => $jmlMasukAll,
                        'sisa_total' => $sisaDiterima,
                        'harga' => $harga,
                        'sub_total' => ($inLPB * $harga),
                        'keterangan' => $b['note']
                    ];

                    $jmlOrderTotal += $b['qty'];
                    $jmlDiterimaInTotal += $inLPB;
                    $jmlDiterimaTotal +=   $jmlMasukAll;
                    $sisaDiterimaTotal += $sisaDiterima;
                    $hargaPerBarangTotal += $harga;
                    $subTotal += ($inLPB * $harga);
                }
            }
        }

        return [
            'result' => $res,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_per_barang_total' => $hargaPerBarangTotal,
            'sub_total' => $subTotal
        ];
    }

    public function getListLPBBahanBakuReport($condition,  $addCondition, $statusPenerimaan, $tipeBahan, $limit = 10, $offset = 0)
    {
        $res = [];

        $availableSort = [
            'po_no'          => 'rm_import_pos.po_no',
            'po_date'        => 'rm_import_pos.po_date',



        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_import_pos.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "
            rm_import_pos.po_no,
            rm_import_pos.po_date,
            rm_import_po_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.id AS spesifikasi_id,
            satuans.kode_satuan,
            suppliers.name AS nama_supplier
        ";

        $rmImportPoModel = new RMImportPOModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $barangs = $rmImportPoModel
            ->select($selectQry)
            ->where($condition)
            ->join('rm_import_po_details', 'rm_import_po_details.rm_import_po_id = rm_import_pos.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.multiple_po_id = rm_import_pos.id', 'left')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'rm_import_po_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->orderBy($sort, $sortType);


        $totalData = $barangs->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupStart();
        }

        if ($addCondition['dateStart']) {
            $barangs->where('rm_import_pos.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $barangs->where('rm_import_pos.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupEnd();
        }

        $totalFilteredData = $barangs->countAllResults(false);
        $barangs = $barangs->findAll($limit, $offset);


        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaPerBarangTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {

            $allLPB = $penerimaanBarangModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['rm_import_po_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->groupBy('purchase_order_id', 'purchase_order_details_id')
                ->findAll();

            $jmlMasukAll = 0;
            foreach ($allLPB as $a) {
                $jmlMasukAll = $a['jmlMasuk'];
            }

            $firstLPB =  $penerimaanBarangModel
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['rm_import_po_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            // TAMPILKAN YANG MASIH ADA SISA AJA
            $diskonHarga = ($b['disc'] / 100) * ($b['price']);
            $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

            $res[] = [
                'rm_import_po_details_id' => $b['id'],
                'rm_import_po_id' => $b['rm_import_po_id'],
                'kode_barang' => $b['kode_barang'],
                'nama_barang' => $b['nama_barang'] . ' - ' . $b['spesifikasi'] . '',
                'spesifikasi_name' => $b['spesifikasi'],
                'nama_barang_master' => $b['nama_barang'],
                'po_no' => $b['po_no'],
                'po_date' => $b['po_date'],
                'nama_supplier' => $b['nama_supplier'],
                'satuan' => $b['kode_satuan'],
                'jml_order' => $b['qty'],
                'jml_diterima_lpb' => $inLPB,
                'jml_diterima_total' => $jmlMasukAll,
                'sisa_total' => $sisaDiterima,
                'harga' => $harga,
                'sub_total' => ($inLPB * $harga),
                'keterangan' => $b['note']
            ];

            $jmlOrderTotal += $b['qty'];
            $jmlDiterimaInTotal += $inLPB;
            $jmlDiterimaTotal +=   $jmlMasukAll;
            $sisaDiterimaTotal += $sisaDiterima;
            $hargaPerBarangTotal += $harga;
            $subTotal += ($inLPB * $harga);
        }

        return [
            'result' => $res,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_per_barang_total' => $hargaPerBarangTotal,
            'sub_total' => $subTotal
        ];
    }

    public function getListLPBBahanBakuReportPDF($condition,  $addCondition, $statusPenerimaan, $tipeBahan)
    {
        $res = [];

        $availableSort = [
            'po_no'          => 'rm_import_pos.po_no',
            'po_date'        => 'rm_import_pos.po_date',



        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_import_pos.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "
            rm_import_pos.po_no,
            rm_import_pos.po_date,
            rm_import_po_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.id AS spesifikasi_id,
            satuans.kode_satuan,
            suppliers.name AS nama_supplier
        ";

        $rmImportPoModel = new RMImportPOModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $barangs = $rmImportPoModel
            ->select($selectQry)
            ->where($condition)
            ->join('rm_import_po_details', 'rm_import_po_details.rm_import_po_id = rm_import_pos.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.multiple_po_id = rm_import_pos.id', 'left')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'rm_import_po_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->orderBy($sort, $sortType);


        $totalData = $barangs->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupStart();
        }

        if ($addCondition['dateStart']) {
            $barangs->where('rm_import_pos.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $barangs->where('rm_import_pos.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupEnd();
        }

        $totalFilteredData = $barangs->countAllResults(false);
        $barangs = $barangs->findAll();


        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaPerBarangTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {

            $allLPB = $penerimaanBarangModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['rm_import_po_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->groupBy('purchase_order_id', 'purchase_order_details_id')
                ->findAll();

            $jmlMasukAll = 0;
            foreach ($allLPB as $a) {
                $jmlMasukAll = $a['jmlMasuk'];
            }

            $firstLPB =  $penerimaanBarangModel
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['rm_import_po_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            // TAMPILKAN YANG MASIH ADA SISA AJA
            $diskonHarga = ($b['disc'] / 100) * ($b['price']);
            $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

            $res[] = [
                'rm_import_po_details_id' => $b['id'],
                'rm_import_po_id' => $b['rm_import_po_id'],
                'kode_barang' => $b['kode_barang'],
                'nama_barang' => $b['nama_barang'] . ' - ' . $b['spesifikasi'] . '',
                'spesifikasi_name' => $b['spesifikasi'],
                'nama_barang_master' => $b['nama_barang'],
                'po_no' => $b['po_no'],
                'po_date' => $b['po_date'],
                'nama_supplier' => $b['nama_supplier'],
                'satuan' => $b['kode_satuan'],
                'jml_order' => $b['qty'],
                'jml_diterima_lpb' => $inLPB,
                'jml_diterima_total' => $jmlMasukAll,
                'sisa_total' => $sisaDiterima,
                'harga' => $harga,
                'sub_total' => ($inLPB * $harga),
                'keterangan' => $b['note']
            ];

            $jmlOrderTotal += $b['qty'];
            $jmlDiterimaInTotal += $inLPB;
            $jmlDiterimaTotal +=   $jmlMasukAll;
            $sisaDiterimaTotal += $sisaDiterima;
            $hargaPerBarangTotal += $harga;
            $subTotal += ($inLPB * $harga);
        }

        return [
            'result' => $res,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_per_barang_total' => $hargaPerBarangTotal,
            'sub_total' => $subTotal
        ];
    }

    public function getPoBBImportDetailById($id)
    {
        $selectQry = "rm_import_po_details.*";

        $condition = [
            "rm_import_po_id" => $id,
        ];

        $poBBImportDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->findAll();

        return $poBBImportDetailData;
    }
}
