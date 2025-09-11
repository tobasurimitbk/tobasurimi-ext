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
        'additional_cost_type',
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

    public function getListLPBBahanPenolong($amPurchaseOrderID, $statusPenerimaan, $tipeBahan, $isInitEdit, $penerimaanBarangID = null)
    {
        // dd($amPurchaseOrderID);

        $res = [];
        $condition = [
            'am_purchase_orders.deletedAt' => null,
            'am_purchase_order_details.deletedAt' => null
        ];

        $selectQry = "
            purchase_requests.spp_no,
            am_purchase_orders.po_no,
            am_purchase_orders.status_penerimaan,
            am_purchase_orders.purchase_request_id,
            am_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.id AS spesifikasi_id,
            barang_master_spesifikasi.satuan_1,
            barang_master_spesifikasi.satuan_2,
            barang_master_spesifikasi.satuan_3,
            barang_master_spesifikasi.konversi_satuan_2,
            barang_master_spesifikasi.konversi_satuan_3,
            satuans.kode_satuan
        ";

        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $satuanModel = new SatuansModel();

        $barangs = $amPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            ->whereIn('am_purchase_orders.id', $amPurchaseOrderID)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'am_purchase_order_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->orderBy('am_purchase_orders.purchase_request_id', "asc")
            ->findAll();


        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaPerBarangTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {

            // GET NILAI KONVERSI
            $nilaiKonversi = 1;
            $satuanKonversiId = $b['satuan_1'];
            $kodeSatuanKonversi = "";
            if ($b['unit'] == $b['satuan_1']) {
                $nilaiKonversi = 1;
            } elseif ($b['unit'] == $b['satuan_2']) {
                $nilaiKonversi = $b['konversi_satuan_2'];
            } elseif ($b['unit'] == $b['satuan_3']) {
                $nilaiKonversi = $b['konversi_satuan_3'];
            }

            // GET KODE SATUAN KONVERSI
            $satuanKonversi = $satuanModel->where('id', $satuanKonversiId)->first();
            $kodeSatuanKonversi = $satuanKonversi == null ? "" : $satuanKonversi['kode_satuan'];

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
                        'penerimaan_barang_detail_id' => isset($firstLPB['penerimaan_barang_detail_id']) ? $firstLPB['penerimaan_barang_detail_id'] : "",
                        'pengembalian_barang_detail_id' => isset($firstLPB['pengembalian_barang_detail_id']) ? $firstLPB['pengembalian_barang_detail_id'] : "",
                        'jumlah_return' => isset($firstLPB['jumlah_return']) ? $firstLPB['jumlah_return'] : "",
                        'keterangan_return' => isset($firstLPB['keterangan_return']) ? $firstLPB['keterangan_return'] : "",
                        'am_purchase_order_details_id' => $b['id'],
                        'am_purchase_order_id' => $b['am_purchase_order_id'],
                        'kode_barang' => $b['kode_barang'],
                        'nama_barang' => $b['nama_barang'] . ' - ' . $b['spesifikasi'] . '',
                        'spesifikasi_name' => $b['spesifikasi'],
                        'nama_barang_master' => $b['nama_barang'],
                        'po_no' => $b['po_no'],
                        'spp_no' => $b['spp_no'],
                        'satuan' => $b['kode_satuan'],
                        'jml_order' => $b['qty'],
                        'jml_diterima_lpb' => $inLPB,
                        'jml_diterima_total' => $jmlMasukAll,
                        'sisa_total' => round($sisaDiterima, 4),
                        'harga' => $harga,
                        'sub_total' => round($inLPB * $harga),
                        'keterangan' => $b['note'],
                        // TAMBAHAN
                        'satuan_id' => $b['unit'],
                        'satuan_konversi_id' => $satuanKonversiId,
                        'satuan_konversi' => $kodeSatuanKonversi,
                        'nilai_konversi' => $nilaiKonversi,
                        'jml_diterima_lpb_konversi' => ($inLPB * $nilaiKonversi),
                        'purchase_request_id' => $b['purchase_request_id'],
                        'status_penerimaan' => $b['status_penerimaan']
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
                        'spp_no' => $b['spp_no'],
                        'satuan' => $b['kode_satuan'],
                        'jml_order' => $b['qty'],
                        'jml_diterima_lpb' => $inLPB,
                        'jml_diterima_total' => $jmlMasukAll,
                        'sisa_total' => round($sisaDiterima, 4),
                        'harga' => $harga,
                        'sub_total' => round($inLPB * $harga),
                        'keterangan' => $b['note'],
                        // TAMBAHAN
                        'satuan_id' => $b['unit'],
                        'satuan_konversi_id' => $satuanKonversiId,
                        'satuan_konversi' => $kodeSatuanKonversi,
                        'nilai_konversi' => $nilaiKonversi,
                        'jml_diterima_lpb_konversi' => ($inLPB * $nilaiKonversi),
                        'purchase_request_id' => $b['purchase_request_id'],
                        'status_penerimaan' => $b['status_penerimaan']
                    ];

                    $jmlOrderTotal += $b['qty'];
                    $jmlDiterimaInTotal += $inLPB;
                    $jmlDiterimaTotal +=   $jmlMasukAll;
                    $sisaDiterimaTotal += $sisaDiterima;
                    $hargaPerBarangTotal += $harga;
                    $subTotal += ($inLPB * $harga);
                } else if ($isInitEdit == 0) {
                    // TAMPILKAN JIKA DI CHANGE ULANG SPP NYA
                    // TAMPILKAN YANG MASIH ADA SISA AJA
                    $res[] = [
                        'penerimaan_barang_detail_id' => isset($firstLPB['penerimaan_barang_detail_id']) ? $firstLPB['penerimaan_barang_detail_id'] : "",
                        'pengembalian_barang_detail_id' => isset($firstLPB['pengembalian_barang_detail_id']) ? $firstLPB['pengembalian_barang_detail_id'] : "",
                        'jumlah_return' => isset($firstLPB['jumlah_return']) ? $firstLPB['jumlah_return'] : "",
                        'keterangan_return' => isset($firstLPB['keterangan_return']) ? $firstLPB['keterangan_return'] : "",
                        'am_purchase_order_details_id' => $b['id'],
                        'am_purchase_order_id' => $b['am_purchase_order_id'],
                        'kode_barang' => $b['kode_barang'],
                        'nama_barang' => $b['nama_barang'] . ' - ' . $b['spesifikasi'] . '',
                        'spesifikasi_name' => $b['spesifikasi'],
                        'nama_barang_master' => $b['nama_barang'],
                        'po_no' => $b['po_no'],
                        'spp_no' => $b['spp_no'],
                        'satuan' => $b['kode_satuan'],
                        'jml_order' => $b['qty'],
                        'jml_diterima_lpb' => $inLPB,
                        'jml_diterima_total' => $jmlMasukAll,
                        'sisa_total' => round($sisaDiterima, 4),
                        'harga' => $harga,
                        'sub_total' => round($inLPB * $harga),
                        'keterangan' => $b['note'],
                        // TAMBAHAN
                        'satuan_id' => $b['unit'],
                        'satuan_konversi_id' => $satuanKonversiId,
                        'satuan_konversi' => $kodeSatuanKonversi,
                        'nilai_konversi' => $nilaiKonversi,
                        'jml_diterima_lpb_konversi' => ($inLPB * $nilaiKonversi),
                        'purchase_request_id' => $b['purchase_request_id'],
                        'status_penerimaan' => $b['status_penerimaan']
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
            'po_no'   => 'am_purchase_orders.po_no',
            'po_date' => 'am_purchase_orders.po_date',
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

        // ==== Query Data Utama ====
        $barangsQuery = $amPurchaseOrderModel
            ->select($selectQry)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.multiple_po_id = am_purchase_orders.id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'am_purchase_order_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->where($condition);

        if ($addCondition['dateStart']) {
            $barangsQuery->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $barangsQuery->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        // Order dan Pagination
        $barangs = $barangsQuery->orderBy($sort, $sortType)->findAll($limit, $offset);

        // ==== Query Total Data (tanpa filter tanggal) ====
        $totalDataQuery = $amPurchaseOrderModel
            ->select('am_purchase_order_details.id')
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->where($condition);
        $totalData = $totalDataQuery->countAllResults();

        // ==== Query Total Filtered Data (dengan filter tanggal) ====
        $filteredDataQuery = $amPurchaseOrderModel
            ->select('am_purchase_order_details.id')
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->where($condition);

        if ($addCondition['dateStart']) {
            $filteredDataQuery->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $filteredDataQuery->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        $totalFilteredData = $filteredDataQuery->countAllResults();

        // ==== Proses Loop ====
        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaPerBarangTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {
            $allLPB = $penerimaanBarangModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk, group_concat(penerimaan_barang.no_penerimaan_barang) AS no_penerimaan_barang')
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
            $noLpbList = [];
            foreach ($allLPB as $a) {
                $jmlMasukAll += $a['jmlMasuk'];
                if (!empty($a['no_penerimaan_barang'])) {
                    $noLpbList[] = $a['no_penerimaan_barang'];
                }
            }

            $firstLPB = $penerimaanBarangModel
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['am_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = $firstLPB['jml_masuk'] ?? 0;

            $sisaDiterima = $b['qty'] - $jmlMasukAll;
            $diskonHarga = ($b['disc'] / 100) * $b['price'];
            $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];
            $subtotal = $inLPB * $harga;

            $res[] = [
                'am_purchase_order_details_id' => $b['id'],
                'am_purchase_order_id'         => $b['am_purchase_order_id'],
                'kode_barang'                  => $b['kode_barang'],
                'nama_barang'                  => $b['nama_barang'] . ' - ' . $b['spesifikasi'],
                'spesifikasi_name'             => $b['spesifikasi'],
                'nama_barang_master'           => $b['nama_barang'],
                'po_no'                        => $b['po_no'],
                'po_date'                      => $b['po_date'],
                'nama_supplier'                => $b['nama_supplier'],
                'satuan'                       => $b['kode_satuan'],
                'jml_order'                    => $b['qty'],
                'jml_diterima_lpb'             => $inLPB,
                'jml_diterima_total'           => $jmlMasukAll,
                'sisa_total'                   => round($sisaDiterima, 4),
                'harga'                        => $harga,
                'sub_total'                    => $subtotal,
                'keterangan'                   => $b['note'],
                'no_lpb' => implode(', ', $noLpbList),
            ];

            $jmlOrderTotal += $b['qty'];
            $jmlDiterimaInTotal += $inLPB;
            $jmlDiterimaTotal += $jmlMasukAll;
            $sisaDiterimaTotal += $sisaDiterima;
            $hargaPerBarangTotal += $harga;
            $subTotal += $subtotal;
        }

        return [
            'result'                    => $res,
            'totalData'                 => $totalData,
            'totalFilteredData'         => $totalFilteredData,
            'jml_order_total'           => $jmlOrderTotal,
            'jml_diterima_in_total'     => $jmlDiterimaInTotal,
            'jml_diterima_total'        => $jmlDiterimaTotal,
            'sisa_diterima_total'       => $sisaDiterimaTotal,
            'harga_per_barang_total'    => $hargaPerBarangTotal,
            'sub_total'                 => $subTotal
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
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk, group_concat(penerimaan_barang.no_penerimaan_barang) AS no_penerimaan_barang')
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
            $noLpbList = [];
            foreach ($allLPB as $a) {
                $jmlMasukAll += $a['jmlMasuk'];
                if (!empty($a['no_penerimaan_barang'])) {
                    $noLpbList[] = $a['no_penerimaan_barang'];
                }
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
                'sisa_total' => round($sisaDiterima, 4),
                'harga' => $harga,
                'sub_total' => $b['total'],
                'keterangan' => $b['note'],
                'no_lpb' => implode(', ', $noLpbList),
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

    public function getSpesifikasiBarangAsString($amPurchaseOrderId)
    {
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();

        $selectQry = "
            barang_master.barang_name,
            GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', ') as spesifikasi_tergabung,
            SUM(am_purchase_order_details.qty) as qty_diterima_total,
            satuans.kode_satuan,
            suppliers.name as nama_supplier,
            am_purchase_orders.po_no
        ";

        $rmPurchaseOrderDetail = $amPurchaseOrderDetailModel->select($selectQry)
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->where('am_purchase_order_details.am_purchase_order_id', $amPurchaseOrderId)
            ->where('am_purchase_order_details.deletedAt IS NULL') // Pastikan ini berfungsi
            ->groupBy('barang_master.barang_name, am_purchase_orders.po_no')
            ->findAll();

        // Format output sesuai permintaan
        $output = [];
        $namaSupplier = "";
        $noPo = "";

        foreach ($rmPurchaseOrderDetail as $detail) {
            $namaSupplier = $detail['nama_supplier'];
            $noPo = $detail['po_no'];

            $output[] = "{$detail['barang_name']} {$detail['spesifikasi_tergabung']}; {$detail['qty_diterima_total']} {$detail['kode_satuan']}";
        }

        return "PEMB. " . implode('; ', $output) . "; " . $namaSupplier . "; " . $noPo; // Jika ada banyak barang, pisahkan dengan titik koma
    }

    public function getListHistoryHargaByBarang($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'am_purchase_orders.purchase_request_id' => 'am_purchase_orders.purchase_request_id',
            'am_purchase_orders.po_no' => 'am_purchase_orders.po_no',
            'am_purchase_orders.divisi_id' => 'penerimaan_barang.divisi_id',
            'am_purchase_order_details.total'    => 'am_purchase_order_details.total',
            'am_purchase_order_details.qty'    => 'am_purchase_order_details.qty',
            'am_purchase_order_details.price'    => 'am_purchase_order_details.price',
            'am_purchase_order_details.unit' => 'am_purchase_order_details.unit',
            'am_purchase_order_details.note' => 'am_purchase_order_details.note',
            'suppliers.name'  => 'suppliers.name',
            'barang_master_spesifikasi.spesifikasi' => 'barang_master_spesifikasi.spesifikasi',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_order_details.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang, 
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            am_purchase_order_details.note,
            suppliers.name as nama_supplier,
            am_purchase_order_details.price as harga, 
            am_purchase_order_details.qty,
            am_purchase_order_details.total as sub_total, 
            am_purchase_order_details.spesifikasi_id,
            divisis.divisi,
            satuans.kode_satuan,
            purchase_requests.spp_no
        ";

        $dataPO = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('am_purchase_orders', 'am_purchase_orders.id  = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $dataPO->countAllResults(false);

        if ($addCondition['search']) {
            $dataPO->groupStart();
        }

        if ($addCondition['start_date'] && $addCondition['end_date']) {
            $startDate = date('Y-m-d', strtotime($addCondition['start_date']));
            $endDate = date('Y-m-d', strtotime($addCondition['end_date']));

            $dataPO->where('am_purchase_orders.po_date >=', $startDate)
                ->where('am_purchase_orders.po_date <=', $endDate);
        }

        if ($addCondition['search']) {
            $dataPO->groupStart();
            $dataPO->like('purchase_requests.spp_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('am_purchase_order_details.note', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('satuans.kode_satuan', $addCondition['search']);
            $dataPO->groupEnd();
        }

        if ($addCondition['search']) {
            $dataPO->groupEnd();
        }

        $totalFilteredData = $dataPO->countAllResults(false);
        $data = $dataPO->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }


    public function historiHargaPOBahanPenolongFirst(
        $spesifikasiId,
        $companyId
    ) {
        $dataLPB = $this->asArray()
            ->select('am_purchase_order_details.*,supplier_id')
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->where('am_purchase_orders.company_id', $companyId)
            ->where('am_purchase_order_details.spesifikasi_id', $spesifikasiId)
            ->orderBy('am_purchase_orders.po_date', "desc")
            ->first();

        return $dataLPB;
    }
}
