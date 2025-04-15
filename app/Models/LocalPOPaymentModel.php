<?php

namespace App\Models;

use App\Controllers\Pembayaran\PanjarSupplier;
use App\Controllers\Supplier\SupplierHarga;
use App\Models\LocalPOPaymentPinjamanModel;
use CodeIgniter\Model;

class LocalPOPaymentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'divisi_id',
        'supplier_id',

        'bank_id',
        'payment_no',

        'payment_date',

        'payment_method',
        'type_bayar',

        'bulan',

        'multiple_lpb_no',
        'multiple_lpb_id',
        'multiple_po_no',
        'multiple_po_id',
        'pembayaran_oleh',

        'potongan_harga',
        'amount',
        'status_posting',
        'akun_kas',
        'akun_selisih',

        'deletedAt'
    ];

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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'payment_no'        => 'local_po_payments.payment_no',
            'suppliers.name'    => 'suppliers.name',
            // 'tanda_terima_faktur.faktur_no' => 'tanda_terima_faktur.faktur_no',
            // 'due_date'          => 'local_po_payments.due_date',
            'payment_date'      => 'local_po_payments.payment_date',
            'payment_method'    => 'local_po_payments.payment_method',
            'amount'            => 'local_po_payments.amount',
            'createdAt'         => 'local_po_payments.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "local_po_payments.id AS id,
                local_po_payments.status_posting,
                local_po_payments.multiple_po_no,
                local_po_payments.payment_no AS payment_no, 
                local_po_payments.type_bayar,
                DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y') AS payment_date, 
                local_po_payments.amount AS amount,
                local_po_payments.payment_method AS payment_method,
                suppliers.name AS supplierName,
                COALESCE(SUM(local_po_payment_pinjaman.bayar_pinjaman), 0) AS total_pinjaman,
                COALESCE(SUM(local_po_payment_panjar.bayar_panjar), 0) AS total_panjar,
                (local_po_payments.amount - 
                COALESCE(SUM(local_po_payment_pinjaman.bayar_pinjaman), 0) - 
                COALESCE(SUM(local_po_payment_panjar.bayar_panjar), 0)) AS sisa_pembayaran,
                SUM(local_po_payments.amount) - 
                COALESCE(SUM(local_po_payment_pinjaman.bayar_pinjaman), 0) - 
                COALESCE(SUM(local_po_payment_panjar.bayar_panjar), 0) AS total_sum_amount";

        $supplierDataQry = $this->asObject()
        ->select($selectQry)
        ->where($condition)
        ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id')
        ->join('local_po_payment_pinjaman', 'local_po_payment_pinjaman.local_po_payment_id = local_po_payments.id', 'left')
        ->join('local_po_payment_panjar', 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id', 'left')
        ->groupBy('local_po_payments.id')
        ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);
        if ($addCondition['search'] != "" || $addCondition['paymentDate'] != "" || $addCondition['startDate'] != "") {
            $supplierDataQry->groupStart();
        }

        if ($addCondition['startDate']) {
            $supplierDataQry->where('payment_date >=',  $addCondition['startDate']);
        }

        if ($addCondition['paymentDate']) {
            $supplierDataQry->where('payment_date <=', $addCondition['paymentDate']);
        }



        if ($addCondition['typeBayar']) {
            $supplierDataQry
                ->whereIn('local_po_payments.type_bayar', $addCondition['typeBayar']);
        }



        if ($addCondition['search'] != "") {
            $supplierDataQry
                ->like('payment_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('local_po_payments.payment_no', $addCondition['search'])
                // ->orLike('tanda_terima_faktur.faktur_no', $addCondition['search'])
                ->orLike('local_po_payments.payment_method', $addCondition['search'])
                ->orLike('local_po_payments.amount', $addCondition['search']);
        }



        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] != "ALL") {
                $addCondition['status_posting'] = $addCondition['status_posting'] == "SUDAH POSTING" ? '1' : '0';
                $supplierDataQry->where('local_po_payments.status_posting', $addCondition['status_posting']);
            }
        }

        if ($addCondition['search'] != "" ||  $addCondition['paymentDate'] != "" || $addCondition['startDate'] != "") {
            $supplierDataQry->groupEnd();
        }

        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);



        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function get($pembayaranID)
    {
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $supplierModel = new SupplierModel();
        $companyModel = new CompaniesModel();

        $result = [
            'pembayaranDetail' => null,
            'tandaTerimaSupplier' => null,
            'itemLpbList' => null,
            'supplierDetail' => null,
            'company' => null
        ];
        $result['pembayaranDetail'] = $this->where('id', $pembayaranID)->first();
        $result['itemLpbList'] = $tandaTerimaFakturDetailModel->getListTandaTerimaItemFaktur($result['pembayaranDetail']['tanda_terima_faktur_id']);
        $result['tandaTerimaSupplier'] = $tandaTerimaFakturModel->getByID($result['pembayaranDetail']['tanda_terima_faktur_id']);
        $result['supplierDetail'] = $supplierModel->where('id', $result['pembayaranDetail']['supplier_id'])->first();
        $result['company'] = $companyModel->select('companies.company')
            ->join('users', 'users.current_company_id = companies.id')
            ->join('tanda_terima_faktur', 'tanda_terima_faktur.user_id = users.id')
            ->where('tanda_terima_faktur.user_id', $result['tandaTerimaSupplier']['user_id'])
            ->first();
        return $result;
    }

    public function getBB($pembayaranID, $companyID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $supplierHargaModel = new SupplierHargaModel();
        $supplierModel = new SupplierModel();
        $companyModel = new CompaniesModel();
        $localPOPaymentModel = new LocalPOPaymentModel();
        $res = [];

        $result = [
            'pembayaranDetail' => null,
            'itemList' => null,
            'company' => null,
            'supplierDetail' => null,
        ];

        $result['pembayaranDetail'] = $this->where('id', $pembayaranID)->first();
        $result['supplierDetail'] = $supplierModel->where('id', $result['pembayaranDetail']['supplier_id'])->first();
        $result['company'] = $companyModel->select('companies.company')
            ->where('id', $companyID)
            ->first();

        $rmDetail = $rmPurchaseOrderDetailModel
            ->whereIn('rm_purchase_order_details.rm_purchase_order_id', json_decode($result['pembayaranDetail']['multiple_lpb_id']))
            ->where('rm_purchase_order_details.deletedAt', null)
            ->findAll();

        $hargaTotal = 0;
        $totalOrder = 0;
        $totalDiterima = 0;

        $conditionLpb = [
            "supplier_id" => $result['pembayaranDetail']['supplier_id'],
            "tipe_bahan" => "BAKU",
            "status_penerimaan" => "LOKAL",
            "status_post" => "FINISH"
        ];

        foreach ($rmDetail as $rm) {
            $rmPurchaseOrder = $rmPurchaseOrderModel->where('id', $rm['rm_purchase_order_id'])->first();
            $supplierHarga = $supplierHargaModel->select('barang_master_spesifikasi.spesifikasi, barang_master.barang_name')
                ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = supplier_harga.spesifikasi_id')
                ->where('supplier_harga.id', $rm['supplier_harga_id'])
                ->first();

            $lpbDetail = $penerimaanBarangModel
                ->where($conditionLpb)
                ->like('multiple_lpb_id', $rm['rm_purchase_order_id'])->first();

            $harga = ($rm['general_price'] + $rm['daily_price'] + $rm['monthly_price']) *  $rm['qty_diterima'];
            $hargaTotal += $harga;
            $totalOrder += $rm['qty'];
            $totalDiterima += $rm['qty_diterima'];

            $res[] = [
                'poID' => $rm['rm_purchase_order_id'],
                'tanggalLpb' => date('d/m/Y', strtotime($lpbDetail['createdAt'])),
                'lpbNo' => $lpbDetail['no_penerimaan_barang'],
                'tanggalPo' => date('d/m/Y', \strtotime($rmPurchaseOrder['po_date'])),
                'poNo' => $rmPurchaseOrder['po_no'],
                'barang' => $supplierHarga['barang_name'] . " - " . $supplierHarga['spesifikasi'] . "",
                'totalOrder' => $rm['qty'],
                'totalDiterima' => $rm['qty_diterima'],
                'akun_kas' => "-",
                'akun_selisih' => "-",
                'totalHarga' => $harga,
                'totalHargaNumber' => $harga
            ];
        }

        $itemList = [
            'detail' => $res,
            'totalOrder' => $totalOrder,
            'totalDiterima' => $totalDiterima,
            'totalHarga' => $hargaTotal,
            'totalHargaNumber' => $hargaTotal
        ];

        $result['itemList'] = $itemList;
        return $result;
    }

    public function getBahanBaku($pembayaranId, $companyId)
    {
        $localPOPaymentModel = new LocalPOPaymentModel();
        $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $localPOPaymentPanjarModel = new localPOPaymentPanjarModel();
        $companyModel = new CompaniesModel();
        $supplierModel = new SupplierModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $condition = [
            'id' => $pembayaranId,
            'company_id' => $companyId,
            'deletedAt' => null
        ];

        $result = [
            'pembayaranDetail' => null,
            'itemList' => null,
            'company' => null,
            'supplierDetail' => null,
        ];


        $resultPaymentBB = $localPOPaymentModel
            ->select('*')
            ->where($condition)
            ->first();


       // Ambil multiple_lpb_id dari local_po_payments
        $dataMultiplePOPaid = $localPOPaymentModel
        ->select('id, multiple_lpb_id')
        ->where('id', $pembayaranId)
        ->first();

        if ($dataMultiplePOPaid) {
        // Decode multiple_lpb_id (karena masih string JSON)
        $lpbIDs = json_decode($dataMultiplePOPaid['multiple_lpb_id'], true);

        // Ambil data dari penerimaan_barang dengan whereIn()
        $penerimaanBarang = [];
        if (!empty($lpbIDs) && is_array($lpbIDs)) {
            $penerimaanBarang = $penerimaanBarangModel
                ->whereIn('id', $lpbIDs)
                ->select('id as lpb_id, multiple_po_id, multiple_po_no')
                ->findAll();

            // Ubah format data supaya multiple_po_no bisa di-loop dengan value dari lpb_id
            $formattedData = [];
                foreach ($penerimaanBarang as $penerimaan) {
                    $multiplePoIDs = json_decode($penerimaan['multiple_po_id'], true);
                    $multiplePoNos = json_decode($penerimaan['multiple_po_no'], true);

                    if (is_array($multiplePoNos)) {
                        foreach ($multiplePoNos as $poNo) {
                            $formattedData[] = [
                                'lpb_id' => $penerimaan['lpb_id'], // Value untuk option
                                'po_no'  => $poNo, // Label untuk option
                            ];
                        }
                    }
                }
            $dataMultiplePOPaid['penerimaan_barang'] = $formattedData;
            }
        }

        $selectQry = "penerimaan_barang_detail.id AS penerimaan_barang_detail_id,penerimaan_barang.id AS penerimaan_barang_id, 
        penerimaan_barang.tanggal AS tanggal_LPB, 
        penerimaan_barang.no_penerimaan_barang,
        rm_purchase_orders.po_date AS tanggal_PO, rm_purchase_orders.po_no, rm_purchase_orders.id as rm_purchase_orders_id, 
        rm_purchase_order_details.id as rm_purchase_order_details_id,
        CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, 
        penerimaan_barang_detail.qty AS total_order, penerimaan_barang_detail.jml_masuk AS total_diterima, penerimaan_barang_detail.harga_harian,
        penerimaan_barang_detail.harga, local_po_payment_details.total, local_po_payments.type_bayar, penerimaan_barang_detail.harga_bulanan,
        local_po_payments.type_bayar";
        $paymentDetail = $localPOPaymentDetailModel
            ->select($selectQry)
            ->join('local_po_payments', 'local_po_payment_details.local_po_payment_id = local_po_payments.id')
            ->join('penerimaan_barang', 'penerimaan_barang.id = local_po_payment_details.penerimaan_barang_id')
            ->join('rm_purchase_orders', 'local_po_payment_details.rm_purchase_order_id = rm_purchase_orders.id')
            ->join('rm_purchase_order_details', 'local_po_payment_details.rm_purchase_order_details_id = rm_purchase_order_details.id')
            ->join('penerimaan_barang_detail', 'local_po_payment_details.penerimaan_barang_detail_id = penerimaan_barang_detail.id')
            ->join('barang_master', 'penerimaan_barang_detail.barang_id = barang_master.id')
            ->join('barang_master_spesifikasi', 'penerimaan_barang_detail.spesifikasi_id = barang_master_spesifikasi.id')
            ->where('local_po_payment_details.local_po_payment_id',  $pembayaranId)
            ->where('local_po_payments.company_id', $companyId)
            ->findAll();


        $result['company'] = $companyModel->select('companies.company')
            ->where('id', $companyId)
            ->first();
        $result['pembayaranDetail'] = $resultPaymentBB;
        $result['supplierDetail'] = $supplierModel->where('id', $result['pembayaranDetail']['supplier_id'])->first();

        $result['pembayaranDetail']['multiple_lpb_no'] = json_decode(
            $result['pembayaranDetail']['multiple_lpb_no'],
            true
        );

        $result['pembayaranDetail']['multiple_lpb_id'] = json_decode(
            $result['pembayaranDetail']['multiple_lpb_id'],
            true
        );

        $total_order = 0;
        $total_diterima = 0;
        $total_tagihan = 0;
        $total_pembayaran = 0;

        foreach ($paymentDetail as $i => $p) {
            $selectQryLocalPO = "SUM(total) as total_dibayar";
            $totalPOPayment = $localPOPaymentDetailModel
                ->select($selectQryLocalPO)
                ->join('local_po_payments', 'local_po_payments.id = local_po_payment_details.local_po_payment_id', 'left')
                ->where('penerimaan_barang_id', $p['penerimaan_barang_id'])
                ->where('penerimaan_barang_detail_id', $p['penerimaan_barang_detail_id'])
                ->where('type_bayar', $p['type_bayar'])
                ->groupBy('penerimaan_barang_detail_id')
                ->groupBy('penerimaan_barang_id')
                ->first();


            if ($p['type_bayar'] == 'Harian') {
                $totalTagihan = ($p['harga_harian'] + $p['harga']) * $p['total_diterima'];
                $paymentDetail[$i]['total_tagihan'] = $totalTagihan;

                if ($totalPOPayment == null) {
                    $paymentDetail[$i]['sisa_pembayaran'] = $totalTagihan;
                } else {
                    $paymentDetail[$i]['sisa_pembayaran'] =  $totalTagihan - $totalPOPayment['total_dibayar'];
                }

                $total_order += intval($p['total_order']);
                $total_diterima += intval($p['total_diterima']);
                $total_tagihan += intval($paymentDetail[$i]['total_tagihan']);
                $total_pembayaran += intval($p['total']);
            } elseif ($p['type_bayar'] == 'Bulanan') {
                $totalTagihan = $p['harga_bulanan'] * $p['total_diterima'];
                $paymentDetail[$i]['total_tagihan'] = $totalTagihan;
                if ($totalPOPayment == null) {
                    $paymentDetail[$i]['sisa_pembayaran'] = $totalTagihan;
                } else {
                    $paymentDetail[$i]['sisa_pembayaran'] =  $totalTagihan - $totalPOPayment['total_dibayar'];
                }
                $total_order += intval($p['total_order']);
                $total_diterima += intval($p['total_diterima']);
                $total_tagihan += intval($paymentDetail[$i]['total_tagihan']);
                $total_pembayaran += intval($p['total']);
            }
        }


        $panjarList = $localPOPaymentPanjarModel
            ->select('*, no_panjar')
            ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
            ->where('local_po_payments.deletedAt', null)
            ->where('local_po_payments.id', $pembayaranId)
            ->where('type', 'BB')
            ->findAll();

        $total_bayar_panjar = 0;
        foreach ($panjarList as $p) {
            $total_bayar_panjar += intval($p['bayar_panjar']);
        }

        $total_akhir = $total_pembayaran - $total_bayar_panjar;

        $itemList = [
            'detail' =>  $paymentDetail,
            'total_order' => $total_order,
            'total_diterima' => $total_diterima,
            'total_tagihan' => $total_tagihan,
            'total_pembayaran' => $total_pembayaran,
            'total_bayar_panjar' => $total_bayar_panjar,
            'total_akhir'   => $total_akhir,
            'panjar_detail' => $panjarList
        ];



        $result['itemList'] = $itemList;
        $result['panjar'] = $panjarList;
        $result['dataMultiplePOPaid'] = $dataMultiplePOPaid;

        return $result;
    }

    public function getLocalPOPayment($pembayaranId, $companyId)
    {
        $localPOPaymentModel = new LocalPOPaymentModel();
        $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $companyModel = new CompaniesModel();
        $supplierModel = new SupplierModel();

        $condition = [
            'id' => $pembayaranId,
            'company_id' => $companyId,
            'deletedAt' => null
        ];

        $result = [
            'pembayaranDetail' => null,
            'company' => null,
            'supplierDetail' => null,
            'panjar' => null
        ];

        // Ambil detail pembayaran dari local_po_payments
        $resultPayment = $localPOPaymentModel->select('*')->where($condition)->first();

        if (!$resultPayment) {
            return null; // Return null jika tidak ditemukan
        }

        // Decode multiple_po_id & multiple_lpb_no jika ada
        $resultPayment['multiple_po_id'] = json_decode($resultPayment['multiple_po_id'], true);
        $resultPayment['multiple_po_no'] = json_decode($resultPayment['multiple_po_no'], true);

        // Ambil detail perusahaan
        $result['company'] = $companyModel->select('company')->where('id', $companyId)->first();

        // Ambil detail supplier
        $result['supplierDetail'] = $supplierModel->where('id', $resultPayment['supplier_id'])->first();

        // Ambil total pembayaran dari local_po_payment_details
        $totalPembayaran = $localPOPaymentDetailModel
            ->select('SUM(total) as total_pembayaran')
            ->where('local_po_payment_id', $pembayaranId)
            ->first();

        $resultPayment['total_pembayaran'] = $totalPembayaran['total_pembayaran'] ?? 0;

        // Ambil detail panjar
        $panjarList = $localPOPaymentPanjarModel
            ->select('*, no_panjar')
            ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
            ->where('local_po_payments.deletedAt', null)
            ->where('local_po_payments.id', $pembayaranId)
            ->findAll();

        // Hitung total panjar
        $totalBayarPanjar = array_sum(array_column($panjarList, 'bayar_panjar'));

        // Hitung total akhir setelah panjar
        $totalAkhir = $resultPayment['total_pembayaran'] - $totalBayarPanjar;

        $result['pembayaranDetail'] = $resultPayment;
        $result['panjar'] = [
            'list' => $panjarList,
            'total_bayar_panjar' => $totalBayarPanjar,
            'total_akhir' => $totalAkhir
        ];

        return $result;
    }


    public function getListPONotPaidByMonth($supplierID, $month)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $localPaymentModel = new LocalPOPaymentModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $supplierHargaModel = new SupplierHargaModel();
        $barangMasterModel = new BarangMasterModel();

        $query = "
        SELECT *
            FROM penerimaan_barang
            WHERE deletedAt IS NULL
                AND DATE_FORMAT(createdAt, '%Y-%m') = '" . $month . "'
                AND supplier_id = '" . $supplierID . "'
                AND tipe_bahan = 'BAKU'
                AND status_penerimaan = 'LOKAL'
                AND status_post = 'FINISH'
        ";

        $lpb = $this->db->query($query)->getResultArray();
        if (count($lpb) == 0) {
            return [
                'detail' => 0,
                'totalOrder' => 0,
                'totalDiterima' => 0,
                'totalHarga' => "Rp 0.0",
            ];
        }

        $poAll = [];

        foreach ($lpb as $l) {
            $poarr = json_decode(($l['multiple_po_id']));
            foreach ($poarr as $p) {
                $poAll[] = $p;
            }
        }

        $conditionLocalPay = [
            'deletedAt' => null,
            'supplier_id' => $supplierID,
            // 'type_po' => "Bahan Baku"
        ];

        $poIsPay = [];
        $payLpbLatest = $localPaymentModel->where($conditionLocalPay)->findAll();

        foreach ($payLpbLatest as $p) {
            foreach (json_decode(($p['multiple_lpb_id'])) as $pm) {
                array_push($poIsPay, $pm);
            }
        }

        $poNotPay = array_diff($poAll, $poIsPay);
        $res = [];

        if (count($poNotPay) == 0) {
            return [
                'detail' => 0,
                'totalOrder' => 0,
                'totalDiterima' => 0,
                'totalHarga' => "Rp 0.0",
                'totalHargaNumber' => 0,
                'totalSudahDibayarNumber' => 0,
                'sisaNumber' => 0
            ];
        }

        $rmDetail = $rmPurchaseOrderDetailModel
            ->whereIn('rm_purchase_order_details.rm_purchase_order_id', $poNotPay)
            ->where('rm_purchase_order_details.deletedAt', null)
            ->findAll();

        $hargaTotal = 0;
        $totalOrder = 0;
        $totalDiterima = 0;
        $totalSudahDibayar = 0;

        // cari nominal sudah dibayar
        if (count($poIsPay) == 0) {
            $localPoPayment = [];
        } else {
            $localPoPayment = $localPaymentModel->whereIn('multiple_lpb_id', $poIsPay)->findAll();
        }

        foreach ($localPoPayment as $l) {
            $totalSudahDibayar += $l['harga_sebelum_diskon'];
        }

        $conditionLpb = [
            "DATE_FORMAT(createdAt, '%Y-%m')" => $month,
            "supplier_id" => $supplierID,
            "tipe_bahan" => "BAKU",
            "status_penerimaan" => "LOKAL",
            "status_post" => "FINISH"
        ];

        foreach ($rmDetail as $rm) {
            $rmPurchaseOrder = $rmPurchaseOrderModel->where('id', $rm['rm_purchase_order_id'])->first();
            $supplierHarga = $barangMasterModel->select('barang_master_spesifikasi.spesifikasi, barang_master.barang_name')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id')
                ->where('barang_master_spesifikasi.barang_master_id', $rm['barang1_id'])
                ->where('barang_master_spesifikasi.id', $rm['barang2_id'])
                ->first();

            $lpbDetail = $penerimaanBarangModel->where($conditionLpb)->like('multiple_lpb_id', $rm['rm_purchase_order_id'])->first();

            $harga = ($rm['monthly_price']) *  $rm['qty_diterima'];
            $hargaTotal += $harga;
            $totalOrder += $rm['qty'];
            $totalDiterima += $rm['qty_diterima'];

            $res[] = [
                'poID' => $rm['rm_purchase_order_id'],
                'tanggalLpb' => date('d/m/Y', strtotime($lpbDetail['createdAt'])),
                'lpbNo' => $lpbDetail['no_penerimaan_barang'],
                'tanggalPo' => date('d/m/Y', \strtotime($rmPurchaseOrder['po_date'])),
                'poNo' => $rmPurchaseOrder['po_no'],
                'barang' => $supplierHarga != null ?  $supplierHarga['barang_name'] . " - " . $supplierHarga['spesifikasi'] . "" : "-",
                'totalOrder' => $rm['qty'],
                'totalDiterima' => $rm['qty_diterima'],
                'totalHarga' => $harga,
                'totalHargaNumber' => $harga,
            ];
        }

        return [
            'detail' => $res,
            'totalOrder' => $totalOrder,
            'totalDiterima' => $totalDiterima,
            'totalHarga' => $hargaTotal,
            'lpb' => $penerimaanBarangModel->where($conditionLpb)->findAll(),
            'totalHargaNumber' => $hargaTotal,
            'totalSudahDibayarNumber' => $totalSudahDibayar,
            'sisaNumber' => $hargaTotal - $totalSudahDibayar
        ];
    }

    public function getListBulananPONotPaidByLPB($supplierID, $month)
    {
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $localPoPaymentDetail = new LocalPOPaymentDetailModel();

        $selectQry = "penerimaan_barang_detail.id AS penerimaan_barang_detail_id,penerimaan_barang.id AS penerimaan_barang_id, 
        penerimaan_barang.tanggal AS tanggal_LPB, penerimaan_barang.no_penerimaan_barang,
        rm_purchase_orders.po_date AS tanggal_PO, rm_purchase_orders.po_no, rm_purchase_orders.id as rm_purchase_orders_id, 
        rm_purchase_order_details.id as rm_purchase_order_details_id,
        CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, 
        penerimaan_barang_detail.qty AS total_order, penerimaan_barang_detail.jml_masuk AS total_diterima, penerimaan_barang_detail.harga_bulanan";

        $condition = [
            'penerimaan_barang.supplier_id' => $supplierID,
            'penerimaan_barang_detail.deletedAt' => null,
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'tipe_bahan'    => 'BAKU',
            'status_post'   => 'FINISH',
            "DATE_FORMAT(penerimaan_barang.tanggal, '%Y-%m')" => $month
        ];

        $penerimaanBulanAll = $penerimaanBarangDetailModel
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
            ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id')
            ->join('rm_purchase_order_details', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id')
            ->join('barang_master', 'penerimaan_barang_detail.barang_id = barang_master.id')
            ->join('barang_master_spesifikasi', 'penerimaan_barang_detail.spesifikasi_id = barang_master_spesifikasi.id')
            ->where($condition)
            ->findAll();



        $result = [];


        foreach ($penerimaanBulanAll as $i => $p) {
            $selectQryLocalPO = "SUM(total) as total_dibayar";
            $totalPoPayment = $localPoPaymentDetail
                ->select($selectQryLocalPO)
                ->join('local_po_payments', 'local_po_payments.id = local_po_payment_details.local_po_payment_id', 'left')
                ->where('penerimaan_barang_id', $p['penerimaan_barang_id'])
                ->where('penerimaan_barang_detail_id', $p['penerimaan_barang_detail_id'])
                ->where('type_bayar', 'Bulanan')
                ->groupBy('penerimaan_barang_detail_id')
                ->groupBy('penerimaan_barang_id')
                ->first();

            $totalTagihan = $p['harga_bulanan'] * $p['total_diterima'];
            $p['total_tagihan'] = $totalTagihan;
            if ($totalPoPayment != null) {
                if ($p['total_tagihan'] > $totalPoPayment['total_dibayar']) {
                    $penerimaanBulanAll[$i]['sisa_pembayaran'] = $p['total_tagihan'] - $totalPoPayment['total_dibayar'];
                    if ($penerimaanBulanAll[$i]['sisa_pembayaran'] > 0) {

                        $penerimaanBulanAll[$i]['total_tagihan'] = $p['total_tagihan'];
                        $penerimaanBulanAll[$i]['total_tagihan_number'] = $p['total_tagihan'];

                        array_push($result, $penerimaanBulanAll[$i]);
                    }
                }
            } else {
                $penerimaanBulanAll[$i]['sisa_pembayaran'] = $totalTagihan;
                $penerimaanBulanAll[$i]['tanggal_LPB'] = date('d/m/Y', \strtotime($p['tanggal_LPB']));
                $penerimaanBulanAll[$i]['total_tagihan'] = $p['total_tagihan'];
                $penerimaanBulanAll[$i]['total_tagihan_number'] = $p['total_tagihan'];
                $penerimaanBulanAll[$i]['tanggal_PO'] = date('d/m/Y', \strtotime($p['tanggal_PO']));
                array_push($result, $penerimaanBulanAll[$i]);
            }
        }



        return $result;
    }

    public function getListHarianPOPaidByLPB($pembayaranId, $companyId)
    {

        $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $localPOPinjamanModel = new LocalPOPaymentPinjamanModel();
        $panjarSupplierModel = new PanjarSupplierModel();
        $pinjamanSupplierModel = new PinjamanSupplierModel();


        $condition = [
            'local_po_payments.id' => $pembayaranId,
            'local_po_payments.company_id' => $companyId,
            'local_po_payments.deletedAt'  => null,
            'local_po_payment_details.deletedAt ' => null
        ];

        $selectQry = " penerimaan_barang_detail.id AS penerimaan_barang_detail_id,penerimaan_barang.id AS penerimaan_barang_id, 
        penerimaan_barang.tanggal AS tanggal_LPB, 
        penerimaan_barang.no_penerimaan_barang,
        rm_purchase_orders.po_date AS tanggal_PO, rm_purchase_orders.po_no, rm_purchase_orders.id as rm_purchase_orders_id, 
        rm_purchase_order_details.id as rm_purchase_order_details_id,
        CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, 
        penerimaan_barang_detail.qty AS total_order, penerimaan_barang_detail.jml_masuk AS total_diterima, penerimaan_barang_detail.harga_harian,
        penerimaan_barang_detail.harga, local_po_payment_details.total, local_po_payments.type_bayar, penerimaan_barang_detail.harga_bulanan,
        local_po_payments.type_bayar, local_po_payment_details.id as local_po_payment_details_id";
        $paymentDetail = $this
            ->select($selectQry)
            ->join('local_po_payment_details', 'local_po_payment_details.local_po_payment_id = local_po_payments.id')
            ->join('penerimaan_barang', 'penerimaan_barang.id = local_po_payment_details.penerimaan_barang_id')
            ->join('rm_purchase_orders', 'local_po_payment_details.rm_purchase_order_id = rm_purchase_orders.id')
            ->join('rm_purchase_order_details', 'local_po_payment_details.rm_purchase_order_details_id = rm_purchase_order_details.id')
            ->join('penerimaan_barang_detail', 'local_po_payment_details.penerimaan_barang_detail_id = penerimaan_barang_detail.id')
            ->join('barang_master', 'penerimaan_barang_detail.barang_id = barang_master.id')
            ->join('barang_master_spesifikasi', 'penerimaan_barang_detail.spesifikasi_id = barang_master_spesifikasi.id')
            ->where($condition)
            ->findAll();

        $result = [];

        $total_order = 0;
        $total_diterima = 0;
        $total_tagihan = 0;
        $total_pembayaran = 0;

        foreach ($paymentDetail as $i => $p) {
            $paymentDetail[$i]['total_number'] = intval($p['total']);
            $paymentDetail[$i]['tanggal_LPB'] = date('d/m/Y', \strtotime($p['tanggal_LPB']));
            $paymentDetail[$i]['tanggal_PO'] = date('d/m/Y', \strtotime($p['tanggal_PO']));

            $selectQryLocalPO = "SUM(total) as total_dibayar";
            $totalPOPayment = $localPOPaymentDetailModel
                ->select($selectQryLocalPO)
                ->join('local_po_payments', 'local_po_payments.id = local_po_payment_details.local_po_payment_id', 'left')
                ->where('penerimaan_barang_id', $p['penerimaan_barang_id'])
                ->where('penerimaan_barang_detail_id', $p['penerimaan_barang_detail_id'])
                ->where('type_bayar', $p['type_bayar'])
                ->where('local_po_payment_details.deletedAt', null)
                ->groupBy('penerimaan_barang_detail_id')
                ->groupBy('penerimaan_barang_id')
                ->first();

            if ($p['type_bayar'] == 'Harian') {
                $totalTagihan = ($p['harga_harian'] + $p['harga']) * $p['total_diterima'];
                $paymentDetail[$i]['total_tagihan'] = $totalTagihan;

                if ($totalPOPayment == null) {
                    $paymentDetail[$i]['sisa_pembayaran'] = $totalTagihan;
                } else {
                    $paymentDetail[$i]['sisa_pembayaran'] =  $totalTagihan - $totalPOPayment['total_dibayar'];
                }

                $total_order += intval($p['total_order']);
                $total_diterima += intval($p['total_diterima']);
                $total_tagihan += intval($paymentDetail[$i]['total_tagihan']);
                $total_pembayaran += intval($p['total']);
            } elseif ($p['type_bayar'] == 'Bulanan') {
                $totalTagihan = $p['harga_bulanan'] * $p['total_diterima'];
                $paymentDetail[$i]['total_tagihan'] = $totalTagihan;
                if ($totalPOPayment == null) {
                    $paymentDetail[$i]['sisa_pembayaran'] = $totalTagihan;
                } else {
                    $paymentDetail[$i]['sisa_pembayaran'] =  $totalTagihan - $totalPOPayment['total_dibayar'];
                }
                $total_order += intval($p['total_order']);
                $total_diterima += intval($p['total_diterima']);
                $total_tagihan += intval($paymentDetail[$i]['total_tagihan']);
                $total_pembayaran += intval($p['total']);
            }
        }


        $panjarList = $localPOPaymentPanjarModel
            ->select('local_po_payment_panjar.*, panjar_supplier.payment_date, panjar_supplier.no_panjar, panjar_supplier.total_panjar, local_po_payments.potongan_harga')
            ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
            ->where('panjar_supplier.jenis_panjar', "PANJAR")
            ->where('local_po_payments.deletedAt', null)
            ->where('local_po_payments.id', $pembayaranId)
            ->where('type', 'BB')
            ->findAll();

        foreach ($panjarList as $i => $p) {
            $panjarList[$i]['bayar_panjar_number'] = intval($p['bayar_panjar']);
            $panjarList[$i]['total_panjar_number'] = intval($p['total_panjar']);
            $panjarList[$i]['potongan__harga_number'] = intval($p['potongan_harga']);
            $panjarList[$i]['sisa_panjar'] = $panjarList[$i]['total_panjar_number'] - $localPOPaymentPanjarModel
            ->getTotalPembayaranPanjar($p['panjar_id'], "BB")['total_bayar_panjar'];
        }

        if (empty($panjarList)) {
            $panjarList = $localPOPaymentPanjarModel->select('local_po_payment_panjar.*, panjar_supplier.payment_date, panjar_supplier.no_panjar, panjar_supplier.total_panjar, local_po_payments.potongan_harga')
                                ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
                                ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
                                ->where('panjar_supplier.jenis_panjar', "PANJAR")
                                ->where('local_po_payments.deletedAt', null)
                                ->where('type', 'BB')
                                ->findAll();

            foreach ($panjarList as $i => $p) {
                $panjarList[$i]['bayar_panjar'] = 0; // Tetapkan langsung ke 0
                $panjarList[$i]['bayar_panjar_number'] = 0; // Tetapkan langsung ke 0
                $panjarList[$i]['total_panjar_number'] = intval($p['total_panjar']);
                $panjarList[$i]['potongan__harga_number'] = intval($p['potongan_harga']);
                $panjarList[$i]['sisa_panjar'] = $panjarList[$i]['total_panjar_number'] - $localPOPaymentPanjarModel
                ->getTotalPembayaranPanjar($p['panjar_id'], "BB")['total_bayar_panjar'];
            }
        }


        $panjarTBList = $localPOPaymentPanjarModel
            ->select('local_po_payment_panjar.*, panjar_supplier.no_panjar,panjar_supplier.payment_date, panjar_supplier.total_panjar, local_po_payments.potongan_harga')
            ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
            ->where('panjar_supplier.jenis_panjar', "PANJAR_TB")
            ->where('local_po_payments.deletedAt', null)
            ->where('local_po_payments.id', $pembayaranId)
            ->where('type', 'BB')
            ->findAll();

        foreach ($panjarTBList as $i => $p) {
            $panjarTBList[$i]['bayar_panjar_number'] = intval($p['bayar_panjar']);
            $panjarTBList[$i]['total_panjar_number'] = intval($p['total_panjar']);
            $panjarTBList[$i]['potongan__harga_number'] = intval($p['potongan_harga']);
            $panjarTBList[$i]['sisa_panjar'] = $panjarTBList[$i]['total_panjar_number'] - $localPOPaymentPanjarModel
            ->getTotalPembayaranPanjar($p['panjar_id'], "BB")['total_bayar_panjar'];
        }


        if (empty($panjarTBList)) {
            $panjarTBList = $localPOPaymentPanjarModel->select('local_po_payment_panjar.*, panjar_supplier.payment_date, panjar_supplier.no_panjar, panjar_supplier.total_panjar, local_po_payments.potongan_harga')
                                ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
                                ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
                                ->where('panjar_supplier.jenis_panjar', "PANJAR_TB")
                                ->where('local_po_payments.deletedAt', null)
                                ->where('type', 'BB')
                                ->findAll();

            foreach ($panjarTBList as $i => $p) {
                $panjarTBList[$i]['bayar_panjar'] = 0; // Tetapkan langsung ke 0
                $panjarTBList[$i]['bayar_panjar_number'] = 0; // Tetapkan langsung ke 0
                $panjarTBList[$i]['bayar_panjar_number'] = intval($p['bayar_panjar']);
                $panjarTBList[$i]['total_panjar_number'] = intval($p['total_panjar']);
                $panjarTBList[$i]['potongan__harga_number'] = intval($p['potongan_harga']);
                $panjarTBList[$i]['sisa_panjar'] = $panjarTBList[$i]['total_panjar_number'] - $localPOPaymentPanjarModel
                ->getTotalPembayaranPanjar($p['panjar_id'], "BB")['total_bayar_panjar'];
            }
        }

        $pinjamanList = $localPOPinjamanModel
            ->select('local_po_payment_pinjaman.*, pinjaman_supplier.payment_date, pinjaman_supplier.no_pinjaman, pinjaman_supplier.total_pinjaman, local_po_payments.potongan_harga')    
            ->join("local_po_payments", 'local_po_payment_pinjaman.local_po_payment_id = local_po_payments.id')
            ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id')
            ->where('local_po_payments.deletedAt', null)
            ->where('local_po_payments.id', $pembayaranId)
            ->where('type', 'BB')
            ->findAll();

        foreach ($pinjamanList as $i => $p) {
            $pinjamanList[$i]['bayar_pinjaman_number'] = intval($p['bayar_pinjaman']);
            $pinjamanList[$i]['total_pinjaman_number'] = intval($p['total_pinjaman']);
            $pinjamanList[$i]['potongan__harga_number'] = intval($p['potongan_harga']);
            $pinjamanList[$i]['sisa_pinjaman'] = $pinjamanList[$i]['total_pinjaman_number'] - $localPOPinjamanModel
            ->getTotalPembayaranPinjaman($p['pinjaman_id'], "BB")['total_bayar_pinjaman'];
        } 

        if (empty($pinjamanList)) {
            $pinjamanList = $localPOPinjamanModel->select('local_po_payment_pinjaman.*, pinjaman_supplier.payment_date, pinjaman_supplier.no_pinjaman, pinjaman_supplier.total_pinjaman, local_po_payments.potongan_harga')    
                        ->join("local_po_payments", 'local_po_payment_pinjaman.local_po_payment_id = local_po_payments.id')
                        ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id')
                        ->where('local_po_payments.deletedAt', null)
                        ->where('type', 'BB')
                        ->findAll();

            foreach ($pinjamanList as $i => $p) {
                $pinjamanList[$i]['bayar_pinjaman'] = 0; // Tetapkan langsung ke 0
                $pinjamanList[$i]['bayar_pinjaman_number'] = 0; // Tetapkan langsung ke 0
                $pinjamanList[$i]['bayar_pinjaman_number'] = intval($p['bayar_pinjaman']);
                $pinjamanList[$i]['total_pinjaman_number'] = intval($p['total_pinjaman']);
                $pinjamanList[$i]['potongan__harga_number'] = intval($p['potongan_harga']);
                $pinjamanList[$i]['sisa_pinjaman'] = $pinjamanList[$i]['total_pinjaman_number'] - $localPOPinjamanModel
                ->getTotalPembayaranPinjaman($p['pinjaman_id'], "BB")['total_bayar_pinjaman'];
                      
            } 
        }



        $total_bayar_panjar = 0;
        foreach ($panjarList as $p) {
            $total_bayar_panjar += intval($p['bayar_panjar']);
        }

        $total_bayar_panjar_tb = 0;
        foreach ($panjarTBList as $p) {
            $total_bayar_panjar_tb += intval($p['bayar_panjar']);
        }

        $total_bayar_pinjaman = 0;
        foreach ($pinjamanList as $p) {
            $total_bayar_pinjaman += intval($p['bayar_pinjaman']);
        }

        $total_akhir = $total_pembayaran - $total_bayar_panjar - $total_bayar_panjar_tb - $total_bayar_pinjaman;
        $result = [
            'detail'  => $paymentDetail,
            'panjar'    => $panjarList,
            'panjar_tb'    => $panjarTBList,
            'pinjaman'    => $pinjamanList,
            'total_order'   => $total_order,
            'total_bayar_panjar' => $total_bayar_panjar,
            'total_bayar_panjar_tb' => $total_bayar_panjar_tb,
            'total_bayar_pinjaman' => $total_bayar_pinjaman,
            'total_diterima' => $total_diterima,
            'total_tagihan' => $total_tagihan,
            'total_pembayaran'  => $total_pembayaran,
            'total_akhir'   => $total_akhir
        ];


        return $result;
    }

    public function getListHarianPONotPaidByLPB($penerimaanBarangIdArr, $supplierID)
    {
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $localPoPaymentDetail = new LocalPOPaymentDetailModel();
        $purchaseOrderModel = new RMPurchaseOrderModel();
    
        $selectQry = "penerimaan_barang.no_penerimaan_barang, 
            penerimaan_barang.id as penerimaan_barang_id, 
            penerimaan_barang.tanggal AS tanggal_LPB,
            rm_purchase_orders.po_date AS tanggal_PO,
            rm_purchase_orders.po_no AS po_no,
            rm_purchase_orders.id AS rm_purchase_order_id,
            barang_master.barang_name AS barang, 
            GROUP_CONCAT(DISTINCT rm_purchase_order_details.id ORDER BY rm_purchase_order_details.id ASC) AS purchase_order_detail_ids,
            GROUP_CONCAT(DISTINCT penerimaan_barang_detail.id ORDER BY penerimaan_barang_detail.id ASC) AS penerimaan_barang_detail_ids,
            GROUP_CONCAT(DISTINCT penerimaan_barang_detail.harga ORDER BY penerimaan_barang_detail.harga ASC) AS penerimaan_barang_detail_harga,
            SUM(penerimaan_barang_detail.qty) AS total_order, 
            SUM(penerimaan_barang_detail.jml_masuk) AS total_diterima,
            SUM(penerimaan_barang_detail.harga) AS total_tagihan_number";

        $condition = [
            'penerimaan_barang.supplier_id' => $supplierID,
            'penerimaan_barang_detail.deletedAt' => null,
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'tipe_bahan' => 'BAKU'
        ];

        $penerimaanAll = $penerimaanBarangDetailModel
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
            ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id')
            ->join('rm_purchase_order_details', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id')
            ->join('barang_master', 'penerimaan_barang_detail.barang_id = barang_master.id')
            ->whereIn('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangIdArr)
            ->where($condition)
            ->groupBy('penerimaan_barang.no_penerimaan_barang, barang_master.barang_name')
            ->findAll();
    
        // Tambahin total_before_pph & total_after_pph ke sub_total
        foreach ($penerimaanAll as $i => $p) {
            $totalPPH = $purchaseOrderModel->getTotalPPH($p['rm_purchase_order_id']);
    
            $penerimaanAll[$i]['tanggal_LPB'] = date('d/m/Y', strtotime($p['tanggal_LPB']));
            $penerimaanAll[$i]['tanggal_PO'] = date('d/m/Y', strtotime($p['tanggal_PO']));
            $penerimaanAll[$i]['total_tagihan'] = $totalPPH['total_after_pph'];
            $penerimaanAll[$i]['total_tagihan_number'] = $totalPPH['total_after_pph'];
        }

        $result = $penerimaanAll;
        // foreach ($penerimaanAll as $i => $p) {
        //     $selectQryLocalPO = "SUM(total) as total_dibayar";
        //     $totalPoPayment = $localPoPaymentDetail
        //         ->select($selectQryLocalPO)
        //         ->where('penerimaan_barang_id', $p['penerimaan_barang_id'])
        //         ->where('penerimaan_barang_detail_id', $p['penerimaan_barang_detail_id'])
        //         ->groupBy('penerimaan_barang_detail_id')
        //         ->groupBy('penerimaan_barang_id')
        //         ->first();

        //     $totalTagihan = ($p['harga_harian'] + $p['harga']) * $p['total_diterima'];
        //     $p['total_tagihan'] = $totalTagihan;
        //     if ($totalPoPayment != null) {

        //         if ($p['total_tagihan'] > $totalPoPayment['total_dibayar']) {
        //             $penerimaanAll[$i]['sisa_pembayaran'] = $p['total_tagihan'] - $totalPoPayment['total_dibayar'];
        //             if ($penerimaanAll[$i]['sisa_pembayaran'] > 0) {
        //                 $penerimaanAll[$i]['tanggal_LPB'] = date('d/m/Y', \strtotime($p['tanggal_LPB']));
        //                 $penerimaanAll[$i]['total_tagihan'] =$p['total_tagihan'];
        //                 $penerimaanAll[$i]['total_tagihan_number'] = $p['total_tagihan'];
        //                 $penerimaanAll[$i]['tanggal_PO'] = date('d/m/Y', \strtotime($p['tanggal_PO']));
        //                 $penerimaanAll[$i]['total_tagihan'] =$p['total_tagihan'];
        //                 $penerimaanAll[$i]['total_tagihan_number'] = $p['total_tagihan'];

        //                 array_push($result, $penerimaanAll[$i]);
        //             }
        //         }
        //     } else {

        //         $penerimaanAll[$i]['sisa_pembayaran'] = $totalTagihan;
        //         $penerimaanAll[$i]['tanggal_LPB'] = date('d/m/Y', \strtotime($p['tanggal_LPB']));
        //         $penerimaanAll[$i]['total_tagihan'] = $p['total_tagihan'];
        //         $penerimaanAll[$i]['total_tagihan_number'] = $p['total_tagihan'];
        //         $penerimaanAll[$i]['tanggal_PO'] = date('d/m/Y', \strtotime($p['tanggal_PO']));
        //         array_push($result, $penerimaanAll[$i]);
        //     }
        // }

        return $result;
    }

    public function getListHarianPONotPaid($poIdArr, $supplierID)
    {
        $purchaseOrderModel = new RMPurchaseOrderModel();
        $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();

        // Ambil data PO dengan LEFT JOIN ke barang_master & rm_purchase_order_detail
        $purchaseOrders = $purchaseOrderModel
            ->select("rm_purchase_orders.po_date AS tanggal_PO,
                    rm_purchase_orders.po_no AS no_po,
                    rm_purchase_orders.id AS rm_purchase_order_id,
                    rm_purchase_orders.total AS total_tagihan_number,
                    barang_master.barang_name AS barang,
                    COALESCE(SUM(rm_purchase_order_details.qty_diterima), 0) AS total_qty_diterima")
            ->join('barang_master', 'barang_master.id = rm_purchase_orders.barang_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->whereIn('rm_purchase_orders.id', $poIdArr)
            ->where([
                'rm_purchase_orders.supplier_id' => $supplierID,
                'rm_purchase_orders.deletedAt' => null
            ])
            ->groupBy('rm_purchase_orders.id') // SUM qty_diterima per PO
            ->findAll();

        // Ambil semua pembayaran yang terkait dengan PO yang dipilih
        $payments = $localPOPaymentDetailModel
            ->select('local_po_payment_details.id, local_po_payment_details.local_po_payment_id as po_payment_id, local_po_payment_details.rm_purchase_order_id, local_po_payment_details.total as total_paid, local_po_payment_panjar.bayar_panjar as total_panjar,  local_po_payment_pinjaman.bayar_pinjaman as total_pinjaman, local_po_payment_panjar.id as panjar_payment_id')
            ->whereIn('rm_purchase_order_id', $poIdArr)
            ->groupBy('rm_purchase_order_id')
            ->join("local_po_payment_panjar", 'local_po_payment_panjar.local_po_payment_id = local_po_payment_details.local_po_payment_id', 'left')
            ->join("local_po_payment_pinjaman", 'local_po_payment_pinjaman.local_po_payment_id = local_po_payment_details.local_po_payment_id', 'left')
            ->findAll();

        // Konversi hasil pembayaran ke dalam array dengan ID PO sebagai key
        $paymentsMap = [];
        foreach ($payments as $pay) {
            $paymentsMap[$pay['rm_purchase_order_id']] = [
                'total_paid'    => (float) ($pay['total_paid'] ?? 0),
                'total_panjar'  => (float) ($pay['total_panjar'] ?? 0),
                'total_pinjaman'=> (float) ($pay['total_pinjaman'] ?? 0)
            ];
        }   

        foreach ($purchaseOrders as &$p) {
            $totalWithPPH = $purchaseOrderModel->getTotalwithPPH($p['rm_purchase_order_id']);

            // Ambil data pembayaran, panjar, dan pinjaman dengan casting ke float
            $totalPaid    = floatval($paymentsMap[$p['rm_purchase_order_id']]['total_paid'] ?? 0);
            $totalPanjar  = floatval($paymentsMap[$p['rm_purchase_order_id']]['total_panjar'] ?? 0);
            $totalPinjaman = floatval($paymentsMap[$p['rm_purchase_order_id']]['total_pinjaman'] ?? 0);

            // Hitung total tagihan
            $remainingTotal = floatval($totalPaid - ( $totalPanjar + $totalPinjaman));

            // Format tanggal & update data PO
            $p['tanggal_PO'] = date('d/m/Y', strtotime($p['tanggal_PO']));
            $p['total_tagihan'] = number_format($totalWithPPH['total_after_pph'], 2, '.', '');
            $p['total_tagihan_number'] = number_format($totalWithPPH['total_after_pph'], 2, '.', '');
            $p['total_paid'] = number_format($totalPaid, 2, '.', '');
            $p['sisa_tagihan'] = number_format($totalWithPPH['total_after_pph'] - $totalPaid, 2, '.', '');
            $p['total_qty_diterima'] = number_format($p['total_qty_diterima'], 2, '.', '');
        }

        return $purchaseOrders;
 
    }

    
    public function getListLPBNotPaid($lpbSelected, $supplierID, $divisiID, $companyID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $resLPB = [];
        $addedLPB = []; // Track LPB yang sudah masuk

        $conditionPenerimaanBarang = [
            'penerimaan_barang.deletedAt' => null,
            'status_post' => 'FINISH',
            'tipe_bahan' => 'BAKU',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.supplier_id' => $supplierID,
            'penerimaan_barang.divisi_id' => $divisiID,
            'penerimaan_barang.company_id'  => $companyID
        ];

        $lpbList = $penerimaanBarangModel
            ->select('penerimaan_barang.id as lpbID, no_penerimaan_barang as lpbNO, purchase_order_id as poID, penerimaan_barang_detail.id, harga, harga_harian, jml_masuk, rm_purchase_orders.po_no')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
            ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id', 'left')
            ->where($conditionPenerimaanBarang)
            ->groupBy('penerimaan_barang.id') // <<<< Tambahkan ini biar tidak dobel
            ->findAll();

        $poPayed = static::summaryArrPOIsPayed($supplierID, $divisiID, "Bahan Baku");
        $poPayedId = array_column($poPayed, 'penerimaan_barang_detail_id');

        $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();

        // Konversi $lpbSelected menjadi array jika tidak kosong
        $lpbSelectedArray = json_decode($lpbSelected, true) ?: [];
        $lpbSelectedIds = array_column($lpbSelectedArray, 'lpb_id'); // Ambil ID saja untuk perbandingan cepat

        foreach ($lpbList as $l) {
            // Cek apakah LPB ini sudah dibayar di local_po_payment_detail
            $checkPayPo = $localPOPaymentDetailModel->where('penerimaan_barang_id', $l['lpbID'])->first();
        
            // Jika sudah dibayar dan tidak ada di $lpbSelected, maka skip LPB ini
            if ($checkPayPo && !in_array($l['lpbID'], $lpbSelectedIds)) {
                continue;
            }

            // Jika LPB sudah ada di hasil, skip (biar tidak duplicate)
            if (in_array($l['lpbID'], $addedLPB)) {
                continue;
            }

            // Jika LPB ini termasuk dalam selected, langsung tambahkan ke hasil
            if (in_array($l['lpbID'], $lpbSelectedIds) || !in_array($l['id'], $poPayedId)) {
                $resLPB[] = $l;
                $addedLPB[] = $l['lpbID']; // Tandai sebagai sudah dimasukkan
                continue;
            }

            // Jika LPB ini belum dibayar, cek apakah sudah masuk ke pembayaran
            foreach ($poPayed as $p) {
                if ($l['id'] == $p['penerimaan_barang_detail_id']) {
                    if ($p['dibayar'] < ($l['harga'] + $l['harga_harian']) * $l['jml_masuk']) {
                        $resLPB[] = $l;
                        $addedLPB[] = $l['lpbID']; // Tandai sebagai sudah dimasukkan
                    }
                }
            }
        }

        return array_values($resLPB);
    }   


    public function getListPONotPaid($poSelected, $supplierID, $divisiID, $companyID)
{
    $rmPurchaseOrderModel = new rmPurchaseOrderModel();
    $localPOPaymentModel = new LocalPOPaymentModel();
    $localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
    $resPO = [];
    $addedPO = []; // Track PO yang sudah masuk

    $conditionPO = [
        'rm_purchase_orders.deletedAt' => null,
        'rm_purchase_orders.status_penerimaan' => '1',
        'rm_purchase_orders.supplier_id' => $supplierID,
        'rm_purchase_orders.divisi_id' => $divisiID,
        'rm_purchase_orders.company_id'  => $companyID
    ];

    // Ambil semua data PO sesuai kondisi
    $poList = $rmPurchaseOrderModel
        ->select('id as poID, po_no, total as total_harga_po')
        ->where($conditionPO)
        ->findAll();

    // Ambil data pembayaran yang sudah ada
    $poPayed = static::summaryArrPOIsPayed($supplierID, $divisiID, "Bahan Baku");
    $poPayedId = array_column($poPayed, 'purchase_order_id'); // ID PO yang sudah ada pembayaran

    // Konversi $poSelected menjadi array jika tidak kosong
    $poSelectedArray = json_decode($poSelected, true) ?: [];

    // Jika $poSelectedArray adalah array of IDs (misalnya [21, 22]), langsung gunakan
    if (is_array($poSelectedArray) && !empty($poSelectedArray)) {
        $poSelectedIds = $poSelectedArray; // Gunakan langsung
    } else {
        $poSelectedIds = []; // Default ke array kosong
    }

    foreach ($poList as $po) {
        // Jika PO sudah ada di hasil, skip (biar tidak duplicate)
        if (in_array($po['poID'], $addedPO)) {
            continue;
        }

        // Jika PO ini termasuk dalam selected, langsung tambahkan ke hasil
        if (in_array($po['poID'], $poSelectedIds)) {
            $resPO[] = $po;
            $addedPO[] = $po['poID']; // Tandai sebagai sudah dimasukkan
            continue;
        }

        // Ambil total pembayaran yang sudah dilakukan
        $totalPaid = 0;
        $payments = $localPOPaymentDetailModel
            ->where("rm_purchase_order_id", $po['poID'])
            ->findAll();

        foreach ($payments as $pay) {
            $totalPaid += $pay['total'];
        }

        // Ambil total harga setelah PPH
        $totalPPHData = $rmPurchaseOrderModel->getTotalwithPPH($po['poID']);
        $totalAfterPPH = $totalPPHData['total_after_pph'] ?? $po['total_harga_po'];

        // Jika total yang dibayar sudah sama dengan total setelah PPH, skip PO ini
        if ($totalPaid >= $totalAfterPPH) {
            continue;
        }

        // Tambahkan PO yang belum lunas ke hasil
        $resPO[] = $po;
        $addedPO[] = $po['poID']; // Tandai sebagai sudah dimasukkan
    }

    return array_values($resPO);
}


    static function summaryArrPOIsPayed($supplierID, $divisiID)
    {
        $localPaymentModel = new LocalPOPaymentModel();

        $conditionLocalPayment = [
            'local_po_payments.deletedAt' => null,
            'type_bayar'    => 'Harian',
            'supplier_id' => $supplierID,
            'divisi_id' => $divisiID
        ];

        $paymentList = $localPaymentModel
            ->select('sum(local_po_payment_details.total) AS dibayar, penerimaan_barang_id, penerimaan_barang_detail_id')
            ->join('local_po_payment_details', 'local_po_payments.id = local_po_payment_details.local_po_payment_id', 'left')
            ->where($conditionLocalPayment)
            ->groupBy('local_po_payment_details.penerimaan_barang_detail_id')
            ->findAll();



        return $paymentList;
    }

    public function getlistLPBmonth() {}


    public function get_new_no_po($divisi, $bank, $bln, $thn, $last_day, $companyID)
    {
        // Format header dari parameter yang diterima
        $headParts = array_filter([$divisi, $bank]); // Hapus elemen kosong
        $head = !empty($headParts) ? implode('/', $headParts) . '/' : ''; // Gabungkan dengan "/" jika ada data
    
        // Tambahkan bulan dan tahun
        $head .= $bln . $thn . '/';
    
        // Ambil nomor terakhir berdasarkan format yang sesuai
        $lastPO = $this->select('payment_no')
            ->like('payment_no', $head) // Cari dengan prefix yang sudah terbentuk
            ->where('local_po_payments.createdAt >=', "{$thn}-{$bln}-01 00:00:00")
            ->where('local_po_payments.createdAt <=', "{$last_day} 23:59:59")
            ->where('local_po_payments.company_id', $companyID)
            ->orderBy('payment_no', "DESC")
            ->first();
    
        // Nomor urut awal
        $counterFirst = '000001';
    
        if ($lastPO == null) {
            return $head . $counterFirst;
        } else {
            try {
                $last = explode('/', $lastPO['payment_no']);
                $poLastDigit = isset($last[count($last) - 1]) ? (int) $last[count($last) - 1] : 0;
                $counterNext = str_pad($poLastDigit + 1, strlen($counterFirst), '0', STR_PAD_LEFT);
                return $head . $counterNext;
            } catch (Exception $e) {
                return 'ERROR GENERATE NUMBER ' . date('Y-m-d');
            }
        }
    }
    

}
