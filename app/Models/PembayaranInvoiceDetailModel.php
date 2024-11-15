<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranInvoiceDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pembayaran_invoice_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pembayaran_invoice_id',
        'sales_order_invoice_id',
        'sales_order_invoice_detail_id',
        'type_invoice',
        'nama_barang',
        'qty',
        'harga_satuan',
        'harga_total',
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
            'no_pembayaran'     => 'pembayaran_invoice_detail.no_pembayaran',
            'tanggal'   => 'pembayaran_invoice_detail.tanggal',
            'type_invoice'  => 'pembayaran_invoice_detail.type_invoice',
            'createdAt'     => 'pembayaran_invoice_detail.createdAt',
            'updatedAt'     => 'pembayaran_invoice_detail.updatedAt'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'pembayaran_invoice_detail.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';
        $selectQry = "pembayaran_invoice_detail.*";
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
                $dataQry->where('pembayaran_invoice_detail.status_posting', $addCondition['status_posting']);
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
            ->select('pembayaran_invoice_detail.*')
            ->where('pembayaran_invoice_detail.id', $id)
            ->first();

        if ($detail['type_invoice'] == "LOKAL") {
            $namaCustomer = $salesOrderInvoiceModel
                ->select("name")
                ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                ->where('sales_order_invoice.id', $detail['invoice_id'])
                ->first();
            $detail['customer_name'] = $namaCustomer['name'];
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
