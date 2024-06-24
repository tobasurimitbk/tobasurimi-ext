<?php

namespace App\Models;

use App\Controllers\Supplier\SupplierHarga;
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
                      local_po_payments.payment_no AS payment_no, 
                      local_po_payments.type_bayar,
                      DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y') AS payment_date, 
                      local_po_payments.amount AS amount,
                      local_po_payments.payment_method AS payment_method,
                      suppliers.name AS supplierName,
           
                      ";
                    //   tanda_terima_faktur.faktur_no
                      //   DATE_FORMAT(local_po_payments.due_date, '%d/%m/%Y') AS due_date, 

        // if ($condition['local_po_payments.type_po'] == "Bahan Baku") {
        //     $supplierDataQry = $this->asObject()
        //         ->select($selectQry)
        //         ->where($condition)
        //         ->whereIn('type_bayar', $addCondition['typeBayar'])
        //         ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id')
        //         ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = local_po_payments.tanda_terima_faktur_id', 'left')
        //         ->orderBy($sort, $sortType);
        // } else {
            $supplierDataQry = $this->asObject()
                ->select($selectQry)
                ->where($condition)
                ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id')
                // ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = local_po_payments.tanda_terima_faktur_id', 'left')
                ->orderBy($sort, $sortType);
        // }
        


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

    

        if($addCondition['typeBayar']){
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
                'totalHarga' => toRupiah($harga),
                'totalHargaNumber' => $harga
            ];
        }

        $itemList = [
            'detail' => $res,
            'totalOrder' => $totalOrder,
            'totalDiterima' => $totalDiterima,
            'totalHarga' => toRupiah($hargaTotal),
            'totalHargaNumber' => $hargaTotal
        ];

        $result['itemList'] = $itemList;
        return $result;
    }

    public function getBahanBaku($pembayaranId, $companyId){
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
        ->where('local_po_payment_details.local_po_payment_id' ,  $pembayaranId)
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


            if($p['type_bayar'] == 'Harian'){
                $totalTagihan = ($p['harga_harian'] + $p['harga']) * $p['total_diterima'];
                $paymentDetail[$i]['total_tagihan'] = $totalTagihan;
                
                if($totalPOPayment == null){
                    $paymentDetail[$i]['sisa_pembayaran'] = $totalTagihan;
                }else{
                    $paymentDetail[$i]['sisa_pembayaran'] =  $totalTagihan - $totalPOPayment['total_dibayar']; 
                }
             
                $total_order += intval($p['total_order']);
                $total_diterima += intval($p['total_diterima']);
                $total_tagihan += intval($paymentDetail[$i]['total_tagihan'] );
                $total_pembayaran += intval($p['total']);
            }
            elseif($p['type_bayar'] == 'Bulanan'){
                $totalTagihan = $p['harga_bulanan'] * $p['total_diterima'];
                $paymentDetail[$i]['total_tagihan'] = $totalTagihan;
                if($totalPOPayment == null){
                    $paymentDetail[$i]['sisa_pembayaran'] = $totalTagihan;
                }else{
                    $paymentDetail[$i]['sisa_pembayaran'] =  $totalTagihan-$totalPOPayment['total_dibayar']; 
                }
                $total_order += intval($p['total_order']);
                $total_diterima += intval($p['total_diterima']);
                $total_tagihan += intval($paymentDetail[$i]['total_tagihan']    );
                $total_pembayaran += intval($p['total']);
            }
        }
        

        $panjarList = $localPOPaymentPanjarModel
        ->select('*, no_panjar')
        ->join("local_po_payments", 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id')
        ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
        ->where('local_po_payments.deletedAt', null)
        ->where('local_po_payments.id', $pembayaranId)
        ->findAll();

        $total_bayar_panjar = 0;
        foreach ($panjarList as $p ) {
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
                'totalHarga' => toRupiah($harga),
                'totalHargaNumber' => $harga,
            ];
        }

        return [
            'detail' => $res,
            'totalOrder' => $totalOrder,
            'totalDiterima' => $totalDiterima,
            'totalHarga' => toRupiah($hargaTotal),
            'lpb' => $penerimaanBarangModel->where($conditionLpb)->findAll(),
            'totalHargaNumber' => $hargaTotal,
            'totalSudahDibayarNumber' => $totalSudahDibayar,
            'sisaNumber' => $hargaTotal - $totalSudahDibayar
        ];
    }

    public function getListBulananPONotPaidByLPB($supplierID, $month){
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


        foreach($penerimaanBulanAll as $i => $p){
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
            if($totalPoPayment != null){
                if($p['total_tagihan'] > $totalPoPayment['total_dibayar']){
                    $p['total_tagihan'] -= $totalPoPayment['total_dibayar'];
                    if($p['total_tagihan'] > 0){

                        $penerimaanBulanAll[$i]['total_tagihan'] = toRupiah($p['total_tagihan']);
                        $penerimaanBulanAll[$i]['total_tagihan_number'] = $p['total_tagihan']; 
                  
                        array_push($result, $penerimaanBulanAll[$i]);
                    }
                }

            }else{
                
                $penerimaanBulanAll[$i]['tanggal_LPB'] = date('d/m/Y', \strtotime($p['tanggal_LPB']));
                $penerimaanBulanAll[$i]['total_tagihan'] = toRupiah($p['total_tagihan']);
                $penerimaanBulanAll[$i]['total_tagihan_number'] = $p['total_tagihan'];
                $penerimaanBulanAll[$i]['tanggal_PO'] = date('d/m/Y', \strtotime($p['tanggal_PO']));
                array_push($result, $penerimaanBulanAll[$i]);

            }

            
        }

        

        return $result;
    }


    public function getListHarianPONotPaidByLPB($penerimaanBarangIdArr, $supplierID)
    {
        
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $localPoPaymentDetail = new LocalPOPaymentDetailModel();

        $selectQry = "penerimaan_barang_detail.id AS penerimaan_barang_detail_id,penerimaan_barang.id AS penerimaan_barang_id, penerimaan_barang.tanggal AS tanggal_LPB, penerimaan_barang.no_penerimaan_barang,
        rm_purchase_orders.po_date AS tanggal_PO, rm_purchase_orders.po_no, rm_purchase_orders.id as rm_purchase_orders_id, 
        rm_purchase_order_details.id as rm_purchase_order_details_id,
        CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, 
        penerimaan_barang_detail.qty AS total_order, penerimaan_barang_detail.jml_masuk AS total_diterima, penerimaan_barang_detail.harga_harian,
        penerimaan_barang_detail.harga";

        $condition = [
            'penerimaan_barang.supplier_id' => $supplierID,
            'penerimaan_barang_detail.deletedAt' => null,
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'tipe_bahan'    => 'BAKU'
        ];

        $penerimaanAll = $penerimaanBarangDetailModel
        ->select($selectQry)
        ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
        ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id')
        ->join('rm_purchase_order_details', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id')
        ->join('barang_master', 'penerimaan_barang_detail.barang_id = barang_master.id')
        ->join('barang_master_spesifikasi', 'penerimaan_barang_detail.spesifikasi_id = barang_master_spesifikasi.id')
        ->whereIn('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangIdArr)
        ->where($condition)
        ->findAll();

        $result = [];
        
   

        foreach($penerimaanAll as $i => $p){
            $selectQryLocalPO = "SUM(total) as total_dibayar";
            $totalPoPayment = $localPoPaymentDetail
            ->select($selectQryLocalPO)
            ->where('penerimaan_barang_id', $p['penerimaan_barang_id'])
            ->where('penerimaan_barang_detail_id', $p['penerimaan_barang_detail_id'])
            ->groupBy('penerimaan_barang_detail_id')
            ->groupBy('penerimaan_barang_id')
            ->first();

            $totalTagihan = ($p['harga_harian'] + $p['harga']) * $p['total_diterima'];   
            $p['total_tagihan'] = $totalTagihan;
            if($totalPoPayment != null){
                if($p['total_tagihan'] > $totalPoPayment['total_dibayar']){
                    $p['total_tagihan'] -= $totalPoPayment['total_dibayar'];
                    if($p['total_tagihan'] > 0){
                        $penerimaanAll[$i]['tanggal_LPB'] = date('d/m/Y', \strtotime($p['tanggal_LPB']));
                        $penerimaanAll[$i]['total_tagihan'] = toRupiah($p['total_tagihan']);
                        $penerimaanAll[$i]['total_tagihan_number'] = $p['total_tagihan'];
                        $penerimaanAll[$i]['tanggal_PO'] = date('d/m/Y', \strtotime($p['tanggal_PO']));
                        $penerimaanAll[$i]['total_tagihan'] = toRupiah($p['total_tagihan']);
                        $penerimaanAll[$i]['total_tagihan_number'] = $p['total_tagihan']; 
                        
                        array_push($result, $penerimaanAll[$i]);
                    }
                }

            }else{
                
                $penerimaanAll[$i]['tanggal_LPB'] = date('d/m/Y', \strtotime($p['tanggal_LPB']));
                $penerimaanAll[$i]['total_tagihan'] = toRupiah($p['total_tagihan']);
                $penerimaanAll[$i]['total_tagihan_number'] = $p['total_tagihan'];
                $penerimaanAll[$i]['tanggal_PO'] = date('d/m/Y', \strtotime($p['tanggal_PO']));
                array_push($result, $penerimaanAll[$i]);

            }

            
        }

     
        return $result;

    }


    public function getListLPBNotPaid($supplierID, $divisiID, $companyID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $resLPB = [];

        $conditionPenerimaanBarang = [
            'penerimaan_barang.deletedAt' => null,
            'status_post' => 'FINISH',
            'tipe_bahan' => 'BAKU',
            'status_penerimaan' => 'LOKAL',
            'supplier_id' => $supplierID,
            'divisi_id' => $divisiID,
            'penerimaan_barang.company_id'  => $companyID
        ];

        $lpbList = $penerimaanBarangModel
        ->select('penerimaan_barang.id as lpbID, no_penerimaan_barang as lpbNO, purchase_order_id as poID, penerimaan_barang_detail.id, harga, harga_harian, jml_masuk')
        ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
        ->where($conditionPenerimaanBarang)
        ->findAll();

     
        $poPayed = static::summaryArrPOIsPayed($supplierID, $divisiID, "Bahan Baku");
        $poPayedId = [];
        foreach($poPayed as $p){
            array_push($poPayedId ,$p['penerimaan_barang_detail_id']);
        }


        foreach ($lpbList as $l) {
            if(in_array($l['id'] , $poPayedId)){
                foreach($poPayed as $p){
                    if($l['id'] == $p['penerimaan_barang_detail_id']){        
                        if($p['dibayar'] < ($l['harga'] + $l['harga_harian']) * $l['jml_masuk']){
                            $resLPB[] = $l;
                        }
                    }
                }
            }
            else{
                $resLPB[] = $l;
            }
        }
        
    
        $uniqueData = array();
        foreach ($resLPB as $entry) {
            $uniqueData[$entry['lpbID']] = $entry;
        }

        // Reset the array keys to be sequential
        $uniqueData = array_values($uniqueData);
       
 
        return $uniqueData;
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

    public function getlistLPBmonth(){
        
    }
}



