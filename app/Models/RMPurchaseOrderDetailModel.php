<?php

namespace App\Models;

use CodeIgniter\Model;

class RMPurchaseOrderDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_purchase_order_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rm_purchase_order_id',
        'supplier_harga_id',
        'barang1_id',
        'barang2_id',
        'satuan_id',
        'peti',
        'quality',
        'note',
        'qty',
        'qty_diterima',
        'remaining_qty',
        'general_price',
        'daily_price',
        'monthly_price',
        'createdAt',
        'updatedAt',
        'deletedAt',
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
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.rm_purchase_order_id' => $id
        ];

        $builder = $this->db->table('rm_purchase_order_details')
            ->select('rm_purchase_orders.po_no,
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, supplier_harga.spesifikasi, 
            barang_master.kode_barang, barang_master.barang_name as nama_barang, barang_master.id as barang_id, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('barang_master', 'supplier_harga.bahan_baku_id = barang_master.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailById($id)
    {
        $arrCondition = [
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.id' => $id
        ];

        $builder = $this->db->table('rm_purchase_order_details')
            ->select('rm_purchase_orders.po_no,
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, supplier_harga.spesifikasi, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getPoBBLokalDetailById($id)
    {
        $selectQry = "rm_purchase_order_details.*, 
        supplier_harga.spesifikasi, 
        satuans.id as id_satuan, 
        satuans.nama_satuan,
        satuans.kode_satuan,
        barang_master_spesifikasi.spesifikasi";

        $condition = [
            "rm_purchase_order_id" => $id,
        ];

        $poBBLokalDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_purchase_order_details.barang2_id', 'left')
            ->findAll();

        return $poBBLokalDetailData;
    }

    public function getListLPBBahanBaku($rmPurchaseOrderID, $statusPenerimaan, $tipeBahan, $penerimaanBarangID = null)
    {
        $res = [];
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null
        ];

        $selectQry = "
            rm_purchase_orders.po_no,
            rm_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            satuans.kode_satuan,
            supplier_harga.spesifikasi
        ";

        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $barangs = $rmPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            ->whereIn('rm_purchase_orders.id', $rmPurchaseOrderID)
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->findAll();


        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaHarianTotal = 0;
        $hargaBulananTotal = 0;
        $hargaUmumTotal = 0;
        $hargaSumTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {

            if ($penerimaanBarangID == null) {
                $allLPB = $penerimaanBarangModel
                    ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                    ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                    ->where('purchase_order_id', $b['rm_purchase_order_id'])
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
                    ->where('purchase_order_id', $b['rm_purchase_order_id'])
                    ->where('purchase_order_details_id', $b['id'])
                    ->where('tipe_bahan', $tipeBahan)
                    ->where('status_penerimaan', $statusPenerimaan)
                    ->where('penerimaan_barang.deletedAt', null)
                    ->where('penerimaan_barang_detail.deletedAt', null)
                    ->first();

                $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
                $sisaDiterima = $b['qty'] - $jmlMasukAll;
                // CREATE
                if ($sisaDiterima != 0) {
                    $res[] = [
                        'penerimaan_barang_detail_id' => isset($firstLPB['penerimaan_barang_detail_id']) ? $firstLPB['penerimaan_barang_detail_id'] : "",
                        'pengembalian_barang_detail_id' => isset($firstLPB['pengembalian_barang_detail_id']) ? $firstLPB['pengembalian_barang_detail_id'] : "",
                        'jumlah_return' => isset($firstLPB['jumlah_return']) ? $firstLPB['jumlah_return'] : "",
                        'keterangan_return' => isset($firstLPB['keterangan_return']) ? $firstLPB['keterangan_return'] : "",
                        'rm_purchase_order_details_id' => $b['id'],
                        'rm_purchase_order_id' => $b['rm_purchase_order_id'],
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
                        'harga_umum' => $b['general_price'],
                        'harga_harian' => $b['daily_price'],
                        'harga_bulanan' => $b['monthly_price'],
                        'harga_sum' => ($b['general_price'] + $b['daily_price'] + $b['monthly_price']),
                        'sub_total' => ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price'])),
                        'keterangan' => $b['note']
                    ];

                    $jmlOrderTotal += $b['qty'];
                    $jmlDiterimaInTotal += $inLPB;
                    $jmlDiterimaTotal +=   $jmlMasukAll;
                    $sisaDiterimaTotal += $sisaDiterima;
                    $hargaHarianTotal += $b['daily_price'];
                    $hargaBulananTotal += $b['monthly_price'];
                    $hargaUmumTotal += $b['general_price'];
                    $hargaSumTotal +=  ($b['general_price'] + $b['daily_price'] + $b['monthly_price']);
                    $subTotal += ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price']));
                }
            } else {
                $allLPB = $penerimaanBarangModel
                    ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                    ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                    ->where('purchase_order_id', $b['rm_purchase_order_id'])
                    ->where('purchase_order_details_id', $b['id'])
                    ->where('penerimaan_barang_id', $penerimaanBarangID)
                    ->where('tipe_bahan', $tipeBahan)
                    ->where('status_penerimaan', $statusPenerimaan)
                    ->where('penerimaan_barang.deletedAt', null)
                    ->where('penerimaan_barang_detail.deletedAt', null)
                    ->groupBy('purchase_order_id', 'purchase_order_details_id', 'penerimaan_barang_id')
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
                    ->where('purchase_order_id', $b['rm_purchase_order_id'])
                    ->where('purchase_order_details_id', $b['id'])
                    ->where('tipe_bahan', $tipeBahan)
                    ->where('status_penerimaan', $statusPenerimaan)
                    ->where('penerimaan_barang.deletedAt', null)
                    ->where('penerimaan_barang_detail.deletedAt', null)
                    ->where('pengembalian_barang_detail.deletedAt', null)
                    ->first();
                // var_dump($firstLPB);

                $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
                $sisaDiterima = $b['qty'] - $jmlMasukAll;
                // UPDATE
                if ($inLPB != 0) {
                    $res[] = [
                        'penerimaan_barang_detail_id' => $firstLPB['penerimaan_barang_detail_id'],
                        'pengembalian_barang_detail_id' => $firstLPB['pengembalian_barang_detail_id'],
                        'jumlah_return' => isset($firstLPB['jumlah_return']) ? $firstLPB['jumlah_return'] : "",
                        'keterangan_return' => isset($firstLPB['keterangan_return']) ? $firstLPB['keterangan_return'] : "",
                        'rm_purchase_order_details_id' => $b['id'],
                        'rm_purchase_order_id' => $b['rm_purchase_order_id'],
                        'kode_barang' => $b['kode_barang'],
                        'nama_barang' => strtoupper($b['nama_barang'] . ' - ' . $b['spesifikasi'] . ''),
                        'spesifikasi_name' => $b['spesifikasi'],
                        'nama_barang_master' => $b['nama_barang'],
                        'po_no' => $b['po_no'],
                        'satuan' => $b['kode_satuan'],
                        'jml_order' => $b['qty'],
                        'jml_diterima_lpb' => $inLPB,
                        'jml_diterima_total' => $jmlMasukAll,
                        'sisa_total' => $sisaDiterima,
                        'harga_umum' => $b['general_price'],
                        'harga_harian' => $b['daily_price'],
                        'harga_bulanan' => $b['monthly_price'],
                        'harga_sum' => ($b['general_price'] + $b['daily_price'] + $b['monthly_price']),
                        'sub_total' => ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price'])),
                        'keterangan' => $b['note']
                    ];

                    $jmlOrderTotal += $b['qty'];
                    $jmlDiterimaInTotal += $inLPB;
                    $jmlDiterimaTotal +=   $jmlMasukAll;
                    $sisaDiterimaTotal += $sisaDiterima;
                    $hargaHarianTotal += $b['daily_price'];
                    $hargaBulananTotal += $b['monthly_price'];
                    $hargaUmumTotal += $b['general_price'];
                    $hargaSumTotal +=  ($b['general_price'] + $b['daily_price'] + $b['monthly_price']);
                    $subTotal += ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price']));
                }
            }
        }
        // exit;

        return [
            'result' => $res,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_umum_total' => $hargaUmumTotal,
            'harga_harian_total' => $hargaHarianTotal,
            'harga_bulanan_total' => $hargaBulananTotal,
            'harga_sum_total' => $hargaSumTotal,
            'sub_total' => $subTotal
        ];
    }


    public function getListLPBBahanBakuReport($condition,  $addCondition, $statusPenerimaan, $tipeBahan, $limit = 10, $offset = 0)
    {
        $res = [];

        $availableSort = [
            'po_no'          => 'rm_purchase_orders.po_no',
            'po_date'        => 'rm_purchase_orders.po_date',



        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "
            rm_purchase_orders.po_no,
            rm_purchase_orders.po_date,
            rm_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            satuans.kode_satuan,
            supplier_harga.spesifikasi,
            suppliers.name AS nama_supplier,
        ";

        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();


        $barangs = $rmPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.multiple_po_id = rm_purchase_orders.id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->orderBy($sort, $sortType);


        $totalData = $barangs->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupStart();
        }

        if ($addCondition['dateStart']) {
            $barangs->where('rm_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $barangs->where('rm_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupEnd();
        }

        $totalFilteredData = $barangs->countAllResults(false);
        $barangs = $barangs->findAll($limit, $offset);


        // var_dump($barangs);
        // die();

        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaHarianTotal = 0;
        $hargaBulananTotal = 0;
        $hargaUmumTotal = 0;
        $hargaSumTotal = 0;
        $subTotal = 0;


        foreach ($barangs as $b) {


            $allLPB = $penerimaanBarangModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['rm_purchase_order_id'])
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
                ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['rm_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;
            // CREATE

            $res[] = [
                'rm_purchase_order_details_id' => $b['id'],
                'rm_purchase_order_id' => $b['rm_purchase_order_id'],
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
                'harga_umum' => $b['general_price'],
                'harga_harian' => $b['daily_price'],
                'harga_bulanan' => $b['monthly_price'],
                'harga_sum' => ($b['general_price'] + $b['daily_price'] + $b['monthly_price']),
                'sub_total' => ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price'])),
                'keterangan' => $b['note']
            ];

            $jmlOrderTotal += $b['qty'];
            $jmlDiterimaInTotal += $inLPB;
            $jmlDiterimaTotal +=   $jmlMasukAll;
            $sisaDiterimaTotal += $sisaDiterima;
            $hargaHarianTotal += $b['daily_price'];
            $hargaBulananTotal += $b['monthly_price'];
            $hargaUmumTotal += $b['general_price'];
            $hargaSumTotal +=  ($b['general_price'] + $b['daily_price'] + $b['monthly_price']);
            $subTotal += ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price']));
        }


        return [
            'result' => $res,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_umum_total' => $hargaUmumTotal,
            'harga_harian_total' => $hargaHarianTotal,
            'harga_bulanan_total' => $hargaBulananTotal,
            'harga_sum_total' => $hargaSumTotal,
            'sub_total' => $subTotal
        ];
    }

    public function getListLPBBahanBakuReportPDF($condition,  $addCondition, $statusPenerimaan, $tipeBahan)
    {
        $res = [];

        $availableSort = [
            'no_faktur'          => 'sales_order_invoice.no_faktur',
            'tanggal_faktur'          => 'sales_order_invoice.tanggal_faktur',
            'nama_pelanggan'            => 'customers.name',


        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "
            rm_purchase_orders.po_no,
            rm_purchase_orders.po_date,
            rm_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            satuans.kode_satuan,
            supplier_harga.spesifikasi,
            suppliers.name AS nama_supplier
        ";

        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();


        $barangs = $rmPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.multiple_po_id = rm_purchase_orders.id', 'left')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->orderBy($sort, $sortType);


        $totalData = $barangs->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $barangs->groupStart();
        }

        if ($addCondition['dateStart']) {
            $barangs->where('rm_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $barangs->where('rm_purchase_orders.po_date <=', $addCondition['dateEnd']);
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
        $hargaHarianTotal = 0;
        $hargaBulananTotal = 0;
        $hargaUmumTotal = 0;
        $hargaSumTotal = 0;
        $subTotal = 0;


        foreach ($barangs as $b) {


            $allLPB = $penerimaanBarangModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['rm_purchase_order_id'])
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
                ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang.multiple_po_id', 'left')
                ->where('purchase_order_id', $b['rm_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('tipe_bahan', $tipeBahan)
                ->where('penerimaan_barang.status_penerimaan', $statusPenerimaan)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;
            // CREATE

            $res[] = [
                'rm_purchase_order_details_id' => $b['id'],
                'rm_purchase_order_id' => $b['rm_purchase_order_id'],
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
                'harga_umum' => $b['general_price'],
                'harga_harian' => $b['daily_price'],
                'harga_bulanan' => $b['monthly_price'],
                'harga_sum' => ($b['general_price'] + $b['daily_price'] + $b['monthly_price']),
                'sub_total' => ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price'])),
                'keterangan' => $b['note']
            ];

            $jmlOrderTotal += $b['qty'];
            $jmlDiterimaInTotal += $inLPB;
            $jmlDiterimaTotal +=   $jmlMasukAll;
            $sisaDiterimaTotal += $sisaDiterima;
            $hargaHarianTotal += $b['daily_price'];
            $hargaBulananTotal += $b['monthly_price'];
            $hargaUmumTotal += $b['general_price'];
            $hargaSumTotal +=  ($b['general_price'] + $b['daily_price'] + $b['monthly_price']);
            $subTotal += ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price']));
        }


        return [
            'result' => $res,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_umum_total' => $hargaUmumTotal,
            'harga_harian_total' => $hargaHarianTotal,
            'harga_bulanan_total' => $hargaBulananTotal,
            'harga_sum_total' => $hargaSumTotal,
            'sub_total' => $subTotal
        ];
    }
}
