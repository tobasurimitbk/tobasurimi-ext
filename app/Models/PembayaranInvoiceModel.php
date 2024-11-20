<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranInvoiceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pembayaran_invoice';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'divisi_id',
        'user_id',
        'invoice_id',
        'customer_id',
        'valas_id',
        'no_pembayaran',
        'keterangan',
        'type_invoice',
        'tanggal',
        'total_invoice',
        'potongan',
        'total_bayar',
        'status_posting',
        'akun_kas',
        'akun_selisih',
        'akun_kas_lain',
        'akun_kredit_lain',
        'payment_method'

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

    public function getList($addCondition, $condition, $limit = 10, $offset = 0)
    {

        $availableSort = [
            'no_pembayaran'     => 'pembayaran_invoice.no_pembayaran',
            'tanggal'   => 'pembayaran_invoice.tanggal',
            'type_invoice'  => 'pembayaran_invoice.type_invoice',
            'createdAt'     => 'pembayaran_invoice.createdAt',
            'updatedAt'     => 'pembayaran_invoice.updatedAt'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'pembayaran_invoice.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';
        $selectQry = "pembayaran_invoice.*";
        $dataQry = $this
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] != ""  || $addCondition['dateStart'] != "" || $addCondition['dateEnd'] != "" || $addCondition['type_invoice'] != "" || $addCondition['status_posting']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search'] != "") {
            $dataQry->like('no_pembayaran', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['type_invoice']) {
            $dataQry->whereIn('type_invoice', $addCondition['type_invoice']);
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] != "ALL") {
                $addCondition['status_posting'] = $addCondition['status_posting'] == "SUDAH POSTING" ? '1' : '0';
                $dataQry->where('pembayaran_invoice.status_posting', $addCondition['status_posting']);
            }
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['type_invoice']) {
            $dataQry->groupEnd();
        }


        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);


        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getTipeInvoice($id)
    {
        $tipe_invoice = $this
            ->select('type_invoice')
            ->where('deletedAt', null)
            ->where('id', $id)
            ->first();

        return $tipe_invoice['type_invoice'];
    }

    public function getPembayaranInvoiceDetail($id)
    {
        $salesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $salesOrderExportModel = new SalesOrderExportModel();
        $salesOrderLainModel = new SalesOrderLainModel();
        $salesOrderReturnModel = new SalesOrderReturnModel();

        $customer_name = "";

        $data = [];
        $detail = $this
            ->select('pembayaran_invoice.*')
            ->where('pembayaran_invoice.id', $id)
            ->first();

        if ($detail['type_invoice'] == "LOKAL") {
            // Pastikan invoice_id adalah array (jika tidak, ubah ke array)
            $invoiceIds = is_array($detail['invoice_id']) ? $detail['invoice_id'] : explode(',', $detail['invoice_id']);  // Jika string, pisahkan berdasarkan koma

            // Ambil nama pelanggan berdasarkan invoice_id
            $namaCustomer = $salesOrderInvoiceModel
                ->select("customers.name")
                ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                ->whereIn('sales_order_invoice.id', $invoiceIds)  // Gunakan whereIn untuk beberapa ID invoice
                ->findAll();  // Ambil semua hasil yang sesuai

            // Gabungkan semua nama pelanggan yang ditemukan (jika ada lebih dari satu)
            $customerNames = array_map(function($item) {
                return $item['name'];  // Ambil nama pelanggan dari setiap hasil query
            }, $namaCustomer);

            // Gabungkan nama pelanggan dengan koma jika ada lebih dari satu
            $detail['customer_name'] = implode(', ', $customerNames);  // Gabungkan nama-nama pelanggan

        } elseif ($detail['type_invoice'] == "EKSPOR") {
            $namaCustomer = $salesOrderExportModel
                ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                ->join('customers', 'customers.id = sales_contract.customer_id')
                ->where('sales_order_export_id', $detail['invoice_id'])
                ->first();
            $detail['customer_name'] = $namaCustomer['name'];
        } elseif ($detail['type_invoice'] == "LAIN-LAIN") {
            $namaCustomer = $salesOrderLainModel
                ->select("name")
                ->join('customers', 'customers.id = sales_order_lain.customer_id')
                ->where('sales_order_lain.id', $detail['invoice_id'])
                ->first();
            $detail['customer_name'] = $namaCustomer['name'];
        } elseif ($detail['type_invoice'] == "RETURN") {
            $namaCustomer = $salesOrderReturnModel
                ->select("name")
                ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice')
                ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                ->where('sales_order_return.id', $detail['invoice_id'])
                ->first();
            $detail['customer_name'] = $namaCustomer['name'];
        }
        return $detail;
    }
}
