<?php

namespace App\Models;

use CodeIgniter\Model;

class AMPurchaseOrderDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'am_purchase_order_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'am_purchase_order_id',
        'spesifikasi_id',
        'barang_id',
        'item_desc',
        'note',
        'unit',
        'qty',
        'price',
        'disc',
        'additional_cost',
        'ppn',
        'pph',
        'remaining_qty',
        'qty_diterima',
        'total'
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
            'am_purchase_order_details.deletedAt' => null,
            'am_purchase_order_details.am_purchase_order_id' => $id
        ];

        $selectQry = "am_purchase_order_details.*,
            am_purchase_orders.po_no,
            am_purchase_orders.status_penerimaan,
            (am_purchase_order_details.qty * am_purchase_order_details.price) AS totalPriceWithoutAdditional,
            (am_purchase_order_details.qty * am_purchase_order_details.price + am_purchase_order_details.additional_cost) AS totalPrice,
            am_purchase_order_details.price AS price,
            am_purchase_order_details.additional_cost AS additional_cost,
            barang_master.barang_name as nama_barang, 
            barang_master_spesifikasi.spesifikasi,
            am_purchase_order_details.note AS spp_note,
            barang_master.kode_barang,
            taxppn.tax_value as ppnValue, 
            taxpph.tax_value as pphValue, 
            satuans.id as id_satuan, 
            satuans.kode_satuan,
            satuans.nama_satuan";

        $builder = $this->db->table('am_purchase_order_details')
            ->select($selectQry)
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('taxes AS taxppn', 'taxppn.id = am_purchase_order_details.ppn', 'left')
            ->join('taxes AS taxpph', 'taxpph.id = am_purchase_order_details.pph', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailById($id)
    {
        $arrCondition = [
            'am_purchase_order_details.deletedAt' => null,
            'am_purchase_order_details.id' => $id
        ];

        $selectQry = "am_purchase_order_details.*,
            am_purchase_orders.po_no,
            purchase_requests.spp_no,
            am_purchase_orders.status_penerimaan,
            (am_purchase_order_details.qty * am_purchase_order_details.price) AS totalPriceWithoutAdditional,
            (am_purchase_order_details.qty * am_purchase_order_details.price + am_purchase_order_details.additional_cost) AS totalPrice,
            (am_purchase_order_details.price) AS price,
            (am_purchase_order_details.additional_cost) AS additional_cost,
            barang_master.barang_name as nama_barang, 
            barang_master.kode_barang,
            am_purchase_order_details.note AS spp_note,
            taxppn.tax_value as ppnValue, 
            taxpph.tax_value as pphValue, 
            satuans.id as id_satuan, 
            satuans.nama_satuan";

        $builder = $this->db->table('am_purchase_order_details')
            ->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('taxes AS taxppn', 'taxppn.id = am_purchase_order_details.ppn', 'left')
            ->join('taxes AS taxpph', 'taxpph.id = am_purchase_order_details.pph', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getListLPBBahanPenolong($amPurchaseOrderID, $statusPenerimaan, $tipeBahan, $penerimaanBarangID = null)
    {
        $res = [];
        $condition = [
            'am_purchase_orders.deletedAt' => null,
            'am_purchase_order_details.deletedAt' => null
        ];

        $selectQry = "
            am_purchase_orders.po_no,
            am_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.id AS spesifikasi_id,
            satuans.kode_satuan
        ";

        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $barangs = $amPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            ->whereIn('am_purchase_orders.id', $amPurchaseOrderID)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'am_purchase_order_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
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
                ->where('purchase_order_id', $b['am_purchase_order_id'])
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
                ->where('purchase_order_id', $b['am_purchase_order_id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('status_penerimaan', $statusPenerimaan)
                ->where('purchase_order_details_id', $b['id'])
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            $diskonHarga = ($b['disc'] / 100) * ($b['price']);
            $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

            if ($penerimaanBarangID == null) {
                // CREATE
                if ($sisaDiterima != 0) {
                    // TAMPILKAN YANG MASIH ADA SISA AJA
                    $res[] = [
                        'penerimaan_barang_detail_id' => $firstLPB['penerimaan_barang_detail_id'],
                        'pengembalian_barang_detail_id' => $firstLPB['pengembalian_barang_detail_id'],
                        'jumlah_return' => isset($firstLPB['jumlah_return']) ? $firstLPB['jumlah_return'] : "",
                        'keterangan_return' => isset($firstLPB['keterangan_return']) ? $firstLPB['keterangan_return'] : "",
                        'am_purchase_order_details_id' => $b['id'],
                        'am_purchase_order_id' => $b['am_purchase_order_id'],
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
                    // TAMPILKAN YANG MASIH ADA SISA SAJA
                    $res[] = [
                        'penerimaan_barang_detail_id' => $firstLPB['penerimaan_barang_detail_id'],
                        'pengembalian_barang_detail_id' => $firstLPB['pengembalian_barang_detail_id'],
                        'jumlah_return' => isset($firstLPB['jumlah_return']) ? $firstLPB['jumlah_return'] : "",
                        'keterangan_return' => isset($firstLPB['keterangan_return']) ? $firstLPB['keterangan_return'] : "",
                        'am_purchase_order_details_id' => $b['id'],
                        'am_purchase_order_id' => $b['am_purchase_order_id'],
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

    public function getListLPBBahanPenolongReport($condition, $addCondition, $statusPenerimaan, $tipeBahan, $limit = 10, $offset = 0)
    {
        $res = [];

        $availableSort = [
            'po_no'          => 'am_purchase_orders.po_no',
            'po_date'        => 'am_purchase_orders.po_date',

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'am_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';



        $selectQry = "
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            am_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.id AS spesifikasi_id,
            satuans.kode_satuan,
            suppliers.name AS nama_supplier
        ";

        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $barangs = $amPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            // ->whereIn('am_purchase_orders.id', $amPurchaseOrderID)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.multiple_po_id = am_purchase_orders.id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'am_purchase_order_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $barangs->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupStart();
        }

        if ($addCondition['dateStart']) {
            $barangs->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $barangs->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
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

        // var_dump($barangs);
        // die();

        foreach ($barangs as $b) {

            $allLPB = $penerimaanBarangModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['am_purchase_order_id'])
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
                ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['am_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)

                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];


            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            $diskonHarga = ($b['disc'] / 100) * ($b['price']);
            $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

            $res[] = [
                'am_purchase_order_details_id' => $b['id'],
                'am_purchase_order_id' => $b['am_purchase_order_id'],
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

    public function getListLPBBahanPenolongReportPDF($condition, $addCondition, $statusPenerimaan, $tipeBahan)
    {
        $res = [];

        $availableSort = [
            'po_no'          => 'am_purchase_orders.po_no',
            'po_date'        => 'am_purchase_orders.po_date',

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'am_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';



        $selectQry = "
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            am_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.id AS spesifikasi_id,
            satuans.kode_satuan,
            suppliers.name AS nama_supplier
        ";

        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $barangs = $amPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            // ->whereIn('am_purchase_orders.id', $amPurchaseOrderID)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'am_purchase_order_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $barangs->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupStart();
        }

        if ($addCondition['dateStart']) {
            $barangs->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $barangs->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
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
                ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['am_purchase_order_id'])
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
                ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['am_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)
                ->where('am_purchase_orders.po_type', 'Lokal')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            $diskonHarga = ($b['disc'] / 100) * ($b['price']);
            $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

            $res[] = [
                'am_purchase_order_details_id' => $b['id'],
                'am_purchase_order_id' => $b['am_purchase_order_id'],
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
                'sub_total' => $b['total'],
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

    public function getPoBPDetailById($id)
    {
        $selectQry = "am_purchase_order_details.*";

        $condition = [
            "am_purchase_order_id" => $id,
        ];

        $poBPDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->findAll();

        return $poBPDetailData;
    }
}
