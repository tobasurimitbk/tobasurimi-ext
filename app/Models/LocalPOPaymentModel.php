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
        'tanda_terima_faktur_id',
        'bank_id',
        'payment_no',
        'due_date',
        'payment_date',
        'type_po',
        'payment_method',
        'type_bayar',
        'status_lunas',
        'month',
        'lpb_no',
        'multiple_po_no',
        'multiple_po_id',
        'pembayaran_oleh',
        'harga_sebelum_diskon',
        'potongan_harga',
        'amount',
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
            'tanda_terima_faktur.faktur_no' => 'tanda_terima_faktur.faktur_no',
            'due_date'          => 'local_po_payments.due_date',
            'payment_date'      => 'local_po_payments.payment_date',
            'payment_method'    => 'local_po_payments.payment_method',
            'amount'            => 'local_po_payments.amount',
            'createdAt'         => 'local_po_payments.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "local_po_payments.id AS id,
                      local_po_payments.payment_no AS payment_no, 
                      local_po_payments.type_bayar,
                      DATE_FORMAT(local_po_payments.due_date, '%d/%m/%Y') AS due_date, 
                      DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y') AS payment_date, 
                      local_po_payments.amount AS amount,
                      local_po_payments.payment_method AS payment_method,
                      suppliers.name AS supplierName,
                      tanda_terima_faktur.faktur_no
                      ";

        if ($condition['local_po_payments.type_po'] == "Bahan Baku") {
            $supplierDataQry = $this->asObject()
                ->select($selectQry)
                ->where($condition)
                ->whereIn('type_bayar', $addCondition['typeBayar'])
                ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id')
                ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = local_po_payments.tanda_terima_faktur_id', 'left')
                ->orderBy($sort, $sortType);
        } else {
            $supplierDataQry = $this->asObject()
                ->select($selectQry)
                ->where($condition)
                ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id')
                ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = local_po_payments.tanda_terima_faktur_id', 'left')
                ->orderBy($sort, $sortType);
        }



        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] != "" || $addCondition['dueDate'] != "" || $addCondition['paymentDate'] != "") {
            $supplierDataQry->groupStart();
        }

        if ($addCondition['search'] != "") {
            $supplierDataQry
                ->like('payment_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('local_po_payments.payment_no', $addCondition['search'])
                ->orLike('tanda_terima_faktur.faktur_no', $addCondition['search'])
                ->orLike('local_po_payments.payment_method', $addCondition['search'])
                ->orLike('local_po_payments.amount', $addCondition['search']);
        }

        if ($addCondition['dueDate'] != "") {
            $supplierDataQry->where("DATE_FORMAT(local_po_payments.due_date, '%d/%m/%Y')", $addCondition['dueDate']);
        }

        if ($addCondition['paymentDate'] != "") {
            $supplierDataQry->where("DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y')", $addCondition['paymentDate']);
        }

        if ($addCondition['search'] != "" || $addCondition['dueDate'] != "" || $addCondition['paymentDate'] != "") {
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
            ->whereIn('rm_purchase_order_details.rm_purchase_order_id', json_decode($result['pembayaranDetail']['multiple_po_id']))
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

            $lpbDetail = $penerimaanBarangModel->where($conditionLpb)->like('multiple_po_id', $rm['rm_purchase_order_id'])->first();

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
                'totalHarga' => toRupiah($harga)
            ];
        }

        $itemList = [
            'detail' => $res,
            'totalOrder' => $totalOrder,
            'totalDiterima' => $totalDiterima,
            'totalHarga' => toRupiah($hargaTotal),
        ];

        $result['itemList'] = $itemList;
        return $result;
    }

    public function getListPONotPaidByMonth($supplierID, $month)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $localPaymentModel = new LocalPOPaymentModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $supplierHargaModel = new SupplierHargaModel();

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
            'status_lunas' => '0',
            'type_po' => "Bahan Baku"
        ];

        $poIsPay = [];
        $payLpbLatest = $localPaymentModel->where($conditionLocalPay)->findAll();

        foreach ($payLpbLatest as $p) {
            foreach (json_decode(\json_decode($p['multiple_po_id'])) as $pm) {
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
        $localPoPayment = $localPaymentModel->whereIn('multiple_po_id', $poNotPay)->findAll();

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
            $supplierHarga = $supplierHargaModel->select('barang_master_spesifikasi.spesifikasi, barang_master.barang_name, ')
                ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = supplier_harga.spesifikasi_id')
                ->where('supplier_harga.id', $rm['supplier_harga_id'])
                ->first();

            $lpbDetail = $penerimaanBarangModel->where($conditionLpb)->like('multiple_po_id', $rm['rm_purchase_order_id'])->first();

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
                'barang' => $supplierHarga['barang_name'] . " - " . $supplierHarga['spesifikasi'] . "",
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

    public function getListPONotPaidByLPB($lpbID, $supplierID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $localPaymentModel = new LocalPOPaymentModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $supplierHargaModel = new SupplierHargaModel();

        $conditionLpb = [
            'penerimaan_barang.deletedAt' => null,
            'id' => $lpbID
        ];

        $lpb = $penerimaanBarangModel->where($conditionLpb)->first();
        $poAll = json_decode(($lpb['multiple_po_id']));

        $conditionLocalPay = [
            'deletedAt' => null,
            'status_lunas' => '0',
            'supplier_id' => $supplierID,
            'type_po' => "Bahan Baku"
        ];

        $poIsPay = [];
        $payLpbLatest = $localPaymentModel->where($conditionLocalPay)->findAll();

        foreach ($payLpbLatest as $p) {
            foreach (json_decode($p['multiple_po_id']) as $pm) {
                array_push($poIsPay, $pm);
            }
        }

        $poNotPay = array_diff($poAll, $poIsPay);

        $res = [];

        $rmDetail = $rmPurchaseOrderDetailModel
            ->whereIn('rm_purchase_order_details.rm_purchase_order_id', $poNotPay)
            ->where('rm_purchase_order_details.deletedAt', null)
            ->findAll();

        $hargaTotal = 0;
        $totalOrder = 0;
        $totalDiterima = 0;
        $totalSudahDibayar = 0;

        // cari nominal sudah dibayar
        $localPoPayment = $localPaymentModel->whereIn('multiple_po_id', $poIsPay)->findAll();

        foreach ($localPoPayment as $l) {
            $totalSudahDibayar += $l['harga_sebelum_diskon'];
        }

        foreach ($rmDetail as $rm) {
            $rmPurchaseOrder = $rmPurchaseOrderModel->where('id', $rm['rm_purchase_order_id'])->first();
            $supplierHarga = $supplierHargaModel->select('barang_master_spesifikasi.spesifikasi, barang_master.barang_name, ')
                ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = supplier_harga.spesifikasi_id')
                ->where('supplier_harga.id', $rm['supplier_harga_id'])
                ->first();

            $harga = ($rm['general_price'] + $rm['daily_price']) *  $rm['qty_diterima'];
            $hargaTotal += $harga;
            $totalOrder += $rm['qty'];
            $totalDiterima += $rm['qty_diterima'];

            $res[] = [
                'poID' => $rm['rm_purchase_order_id'],
                'tanggalLpb' => date('d/m/Y', strtotime($lpb['createdAt'])),
                'lpbNo' => $lpb['no_penerimaan_barang'],
                'tanggalPo' => date('d/m/Y', \strtotime($rmPurchaseOrder['po_date'])),
                'poNo' => $rmPurchaseOrder['po_no'],
                'barang' => $supplierHarga['barang_name'] . " - " . $supplierHarga['spesifikasi'] . "",
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
            'totalHargaNumber' => $hargaTotal,
            'totalSudahDibayarNumber' => $totalSudahDibayar,
            'sisaNumber' => $hargaTotal - $totalSudahDibayar
        ];
    }

    public function getListLPBNotPaid($supplierID, $divisiID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $resLPB = [];

        $conditionPenerimaanBarang = [
            'deletedAt' => null,
            'status_post' => 'FINISH',
            'tipe_bahan' => 'BAKU',
            'status_penerimaan' => 'LOKAL',
            'supplier_id' => $supplierID,
            'divisi_id' => $divisiID
        ];

        $lpbList = $penerimaanBarangModel->where($conditionPenerimaanBarang)->findAll();
        $poPayed = static::summaryArrPOIsPayed($supplierID, "Bahan Baku");

        foreach ($lpbList as $l) {
            $poID = array_diff((json_decode($l['multiple_po_id'])), $poPayed['po_id']);
            // $poNo = array_diff(json_decode($l['multiple_po_no']), $poPayed['po_no']);
            if (count($poID) != 0) {
                $resLPB[] = [
                    'lpbID' => $l['id'],
                    'lpbNO' => $l['no_penerimaan_barang'],
                    // 'poNO' => $poNo,
                    'poID' => $poID
                ];
            }
        }

        return $resLPB;
    }

    static function summaryArrPOIsPayed($supplierID, $typePO)
    {
        $localPaymentModel = new LocalPOPaymentModel();

        $conditionLocalPayment = [
            'deletedAt' => null,
            'type_po' => $typePO,
            'supplier_id' => $supplierID,
            'status_lunas' => '0'
        ];

        $paymentList = $localPaymentModel->where($conditionLocalPayment)->findAll();
        $lpbIDArr = [];
        $noPoArr = [];

        foreach ($paymentList as $pl) {
            foreach (json_decode($pl['multiple_po_id']) as $id) {
                $lpbIDArr[] = $id;
            }
            // foreach (json_decode($pl['multiple_po_no']) as $po) {
            //     $noPoArr[] = $po;
            // }
        }

        return [
            'po_id' => $lpbIDArr,
            'po_no' => $noPoArr
        ];
    }
}
