<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportPOPaymentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'import_po_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'divisi_id',
        'payment_no',
        'payment_type',
        'supplier_id',
        'po_type',
        'po_id',
        'no_invoice',
        'invoice_emkl',
        'no_aju',
        'pembayaran_oleh',
        'voucher_no',
        'currency',
        'payment_amt',
        'current_exchange_rate',
        'payment_date',
        'termin',
        'payment_method',
        'akun_kas',
        'akun_selisih',
        'note'
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

    public function getPaymentList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode'              => 'import_po_payments.payment_no',
            'name'              => 'suppliers.name',
            'postal_code'       => 'suppliers.postal_code',
            'createdAt'         => 'import_po_payments.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'import_po_payments.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "import_po_payments.*, 
                      DATE_FORMAT(import_po_payments.payment_date, '%d/%m/%Y') AS payment_date,
                      suppliers.name AS supplier_name";
        $paymentDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = import_po_payments.supplier_id')
            ->orderBy($sort, $sortType);

        $totalData = $paymentDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $paymentDataQry->groupStart()
                ->like('suppliers.name', $addCondition['search'])
                ->orLike('import_po_payments.payment_no', $addCondition['search'])
                ->orLike('import_po_payments.currency', $addCondition['search'])
                ->groupEnd();
        }

        // date filter start
        if ($addCondition['startDate']) {
            $paymentDataQry->where('import_po_payments.payment_date >=', $addCondition['startDate']);
        }

        if ($addCondition['lastDate']) {
            $paymentDataQry->where('import_po_payments.payment_date <=', $addCondition['lastDate']);
        }
        // date filter end

        $totalFilteredData = $paymentDataQry->countAllResults(false);
        $data = $paymentDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getPuchaseOrderList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $selectQry = "barang_master.kode_barang, barang_master.barang_name, barang_master_spesifikasi.spesifikasi";
        $joinTable = $addCondition['po_type'] == "BAKU" ? 'rm_import_po_details' : 'am_purchase_order_details';
        $orderField = $addCondition['po_type'] == "BAKU" ? 'rm_import_po_details.createdAt' : 'am_purchase_order_details.createdAt';
        $table = $addCondition['po_type'] == "BAKU" ? 'rm_import_po_details' : 'am_purchase_order_details';

        $poDataQry = $this->db->table($table)
            ->select("$joinTable.*, $selectQry")
            ->where($condition)
            ->join('barang_master', "$joinTable.barang_id = barang_master.id", 'left')
            ->join('barang_master_spesifikasi', "$joinTable.spesifikasi_id = barang_master_spesifikasi.id", 'left')
            ->orderBy($orderField, "DESC");

        $totalData = $poDataQry->countAllResults(false);
        $totalFilteredData = $poDataQry->countAllResults(false);

        $data = $poDataQry->get($limit, $offset)->getResult();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $orderField,
            'sortType'          => "DESC"
        ];
    }

    public function getRiwayatPembayaranList($condition, $limit = 10, $offset = 0)
    {
        $poDataQry = $this->asObject()
            ->where($condition)
            ->orderBy("createdAt", "ASC");

        $totalData = $poDataQry->countAllResults(false);
        $totalFilteredData = $poDataQry->countAllResults(false);

        $data = $poDataQry->get($limit, $offset)->getResult();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => "createdAt",
            'sortType'          => "DESC"
        ];
    }

    public function getTotalPembayaran($poID)
    {
        $totalDibayar = 0;
        foreach ($this->asArray()->where('po_id', $poID)->where('deletedAt', null)->findAll() as $p) {
            $totalDibayar += $p['payment_amt'];
        }
        return $totalDibayar;
    }
}
