<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderInvoiceModel extends Model
{

    protected $table      = 'sales_order_invoice';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;
    protected $allowedFields = [
        'id_user',
        'id_po',
        'id_sales_order_return',
        'document_type',
        'document_id',
        'id_customer',
        'id_surat_jalan',
        'no_surat_jalan',
        'no_faktur',
        'tanggal_faktur',
        'terms',
        'ship_via_id',
        'keterangan',
        'dpp',
        'ppn',
        'total_invoice',
        'status_tax',
        'termasuk_pa',
        'tipe_invoice',
        'status_pelunasan',
        'counter_print',
        'id_company',
        'document_no',
        'status_posting',
        'tax_id',
        'tax_value',
        'jenis_penjualan',
        'nama_ecommerce',
        'pay_amount',
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

    public function getAllSalesOrderInvoiceLokal($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_faktur'          => 'sales_order_invoice.no_faktur',
            'nama_pelanggan'            => 'customers.name',
            'kode_pelanggan'             => 'customers.kode',
            'no_faktur'             => 'sales_order_invoice.no_faktur',
            'total_invoice'      => 'sales_order_invoice.total_invoice',
            'keterangan'      => 'sales_order_invoice.keterangan',
            'createdAt'         => 'sales_order_invoice.createdAt',
            'updatedAt'         => 'sales_order_invoice.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_invoice.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
        sales_order_invoice.id,
        sales_order_invoice.no_faktur,
        sales_order_invoice.keterangan,
        sales_order_invoice.total_invoice,
        sales_order_invoice.tipe_invoice,
        sales_order_invoice.status_posting,
        sales_order_invoice.counter_print,
        sales_order_invoice.status_pelunasan,
        sales_order_invoice.document_no AS doc_no,
        sales_order_invoice.document_type AS doc_type,
                      DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
                      customers.name AS nama_pelanggan,
                      customers.kode AS kode_pelanggan,
                      CONCAT(employees.nip , ' - ', employees.name) AS salesName,
                      IFNULL(sales_order.no_sales_order, surat_jalan_so.no_surat_jalan) AS document_no,
                      SUM(barang_master_sales.harga_pokok) AS sum_harga_pokok,
                      SUM(barang_master_sales.harga_pokok * sales_order_invoice_detail.qty_invoice) AS amt_harga_pokok,
                      SUM(sales_order_invoice_detail.qty_invoice) AS sum_qty_invoice,
                      SUM(sales_order_invoice_detail.amount_invoice) AS sum_amount_invoice";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice', 'LEFT')
            ->where($condition)
            ->groupBy('sales_order_invoice.id')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal
                ->like('no_faktur', $addCondition['search']);
        }

        if ($addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.id_customer', $addCondition['filter_customer']);
        }

        if ($addCondition['filter_jenis_dokumen'] == "pengiriman") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pengiriman');
        }

        if ($addCondition['filter_jenis_dokumen'] == "pesanan") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pesanan');
        }

        if ($addCondition['dateStart']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur <=', $addCondition['dateEnd']);
        }

        $salesOrderInvoiceLokal->where('tipe_invoice', 'LOKAL');

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($limit !== null && $offset !== null) {
            $data = $salesOrderInvoiceLokal->findAll((int)$limit, (int)$offset);
        } else {
            $data = $salesOrderInvoiceLokal->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getLaporanPenjualanPerPelanggan($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'nama_pelanggan'   => 'customers.name',
            'kode_pelanggan'   => 'customers.kode',
            'count_invoice'    => 'count_invoice',
            'total_invoice'    => 'sum_amount_invoice',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'total_invoice'] ?? 'sum_amount_invoice';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'asc'] ?? 'ASC';

        $selectQry = "
                    sales_order_invoice.id,
                    customers.name AS nama_pelanggan,
                    customers.kode AS kode_pelanggan,
                    SUM(barang_master_sales.harga_pokok) AS sum_harga_pokok,
                    SUM(barang_master_sales.harga_pokok * sales_order_invoice_detail.qty_invoice) AS amt_harga_pokok,
                    SUM(sales_order_invoice_detail.qty_invoice) AS sum_qty_invoice,
                    SUM(sales_order_invoice_detail.amount_invoice) AS sum_amount_invoice,
                    COUNT(DISTINCT sales_order_invoice.id) AS count_invoice";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice', 'LEFT')
            ->where($condition)
            ->groupBy('sales_order_invoice.id_customer')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal
                ->like('customers.name', $addCondition['search'])
                ->orLike('customers.kode', $addCondition['search']);
        }

        if ($addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.id_customer', $addCondition['filter_customer']);
        }

        if ($addCondition['filter_jenis_dokumen'] == "pengiriman") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pengiriman');
        }

        if ($addCondition['filter_jenis_dokumen'] == "pesanan") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pesanan');
        }

        if ($addCondition['dateStart']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur <=', $addCondition['dateEnd']);
        }

        $salesOrderInvoiceLokal->where('tipe_invoice', 'LOKAL');

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($limit !== null && $offset !== null) {
            $data = $salesOrderInvoiceLokal->findAll((int)$limit, (int)$offset);
        } else {
            $data = $salesOrderInvoiceLokal->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAllSalesOrderInvoiceLokalForPembayaran($company_id)
    {
        $selectQry = "sales_order_invoice.*,
        sales_order_invoice.document_no AS doc_no,
        sales_order_invoice.document_type AS doc_type,
                      DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
                      metadata.value AS terms_customer,
                      customers.name AS nama_pelanggan,
                      customers.kode AS kode_pelanggan,
                      CONCAT(employees.nip , ' - ', employees.name) AS salesName,
                      IFNULL(sales_order.no_sales_order, surat_jalan_so.no_surat_jalan) AS document_no";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->where('sales_order_invoice.status_pelunasan', "UNPAID")
            ->where('customers.company_id', $company_id)
            ->where('sales_order_invoice.status_posting', 1);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);
        $data = $salesOrderInvoiceLokal->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAllSalesOrderInvoiceLokalBarang($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_faktur'          => 'sales_order_invoice.no_faktur',
            'nama_pelanggan'     => 'customers.name',
            'kode_pelanggan'     => 'customers.kode',
            'no_faktur'          => 'sales_order_invoice.no_faktur',
            'total_invoice'      => 'sales_order_invoice.total_invoice',
            'keterangan'         => 'sales_order_invoice.keterangan',
            'createdAt'          => 'sales_order_invoice.createdAt',
            'updatedAt'          => 'sales_order_invoice.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_invoice.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            sales_order_invoice.id,
            sales_order_invoice.no_faktur,
            sales_order_invoice.keterangan,
            sales_order_invoice.total_invoice,
            sales_order_invoice.tipe_invoice,
            sales_order_invoice.status_posting,
            sales_order_invoice.counter_print,
            sales_order_invoice.status_pelunasan,
            sales_order_invoice.document_no AS doc_no,
            sales_order_invoice.document_type AS doc_type,
            sales_order_invoice.jenis_penjualan AS jenis_penjualan,
            sales_order_invoice.nama_ecommerce AS nama_ecommerce,
            sales_order_invoice_detail.id_barang_invoice AS id_barang_invoice,
            barang_master_sales.kode_barang AS kode_barang,
            barang_master_sales.barang_name AS barang_name,
            SUM(sales_order_invoice_detail.qty_invoice) AS qty_invoice,
            satuans.kode_satuan AS kode_satuan,
            DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
            customers.name AS nama_pelanggan,
            customers.kode AS kode_pelanggan,
            employees.name AS salesName,
            IFNULL(sales_order.no_sales_order, surat_jalan_so.no_surat_jalan) AS document_no,
            SUM(barang_master_sales.harga_pokok) AS sum_harga_pokok,
            SUM(barang_master_sales.harga_pokok * sales_order_invoice_detail.qty_invoice) AS amt_harga_pokok,
            SUM(sales_order_invoice_detail.qty_invoice) AS sum_qty_invoice,
            SUM(sales_order_invoice_detail.amount_invoice) AS sum_amount_invoice
        ";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'RIGHT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id')
            ->where($condition)
            ->groupBy('sales_order_invoice_detail.id_barang_invoice')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_barang']) {
            $salesOrderInvoiceLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal
                ->like('no_faktur', $addCondition['search']);
        }

        if ($addCondition['filter_barang']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice_detail.id_barang_invoice', $addCondition['filter_customer']);
        }

        if ($addCondition['filter_jenis_dokumen'] == "pengiriman") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pengiriman');
        }

        if ($addCondition['filter_jenis_dokumen'] == "pesanan") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pesanan');
        }

        if ($addCondition['dateStart']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur <=', $addCondition['dateEnd']);
        }

        $salesOrderInvoiceLokal->where('tipe_invoice', 'LOKAL');

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_barang']) {
            $salesOrderInvoiceLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);
        if ($limit !== null && $offset !== null) {
            $data = $salesOrderInvoiceLokal->findAll((int)$limit, (int)$offset);
        } else {
            $data = $salesOrderInvoiceLokal->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSalesOrderInvoiceLokalPerBarang($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_faktur'          => 'sales_order_invoice.no_faktur',
            'nama_pelanggan'            => 'customers.name',
            'kode_pelanggan'             => 'customers.kode',
            'no_faktur'             => 'sales_order_invoice.no_faktur',
            'total_invoice'      => 'sales_order_invoice.total_invoice',
            'keterangan'      => 'sales_order_invoice.keterangan',
            'createdAt'         => 'sales_order_invoice.createdAt',
            'updatedAt'         => 'sales_order_invoice.updatedAt',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_invoice.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
                    sales_order_invoice.id,
                    barang_master_sales.barang_name AS barang_name,
                    SUM(sales_order_invoice_detail.qty_invoice) AS sum_qty_invoice,
                    satuans.kode_satuan AS kode_satuan,
                    SUM(sales_order_invoice_detail.amount_invoice) AS sum_amount_invoice,
                    SUM(barang_master_sales.harga_pokok * sales_order_invoice_detail.qty_invoice) AS amt_harga_pokok,
                    COUNT(DISTINCT sales_order_invoice.id) AS count_invoice,
                    barang_master_sales.kode_barang AS kode_barang";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice', 'LEFT')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            ->where($condition)
            ->groupBy('sales_order_invoice_detail.id_barang_invoice')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_barang']) {
            $salesOrderInvoiceLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal
                ->like('no_faktur', $addCondition['search']);
        }

        if ($addCondition['filter_barang']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice_detail.id_barang_invoice', $addCondition['filter_barang']);
        }

        if ($addCondition['filter_jenis_dokumen'] == "pengiriman") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pengiriman');
        }

        if ($addCondition['filter_jenis_dokumen'] == "pesanan") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pesanan');
        }

        if ($addCondition['dateStart']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur <=', $addCondition['dateEnd']);
        }

        $salesOrderInvoiceLokal->where('tipe_invoice', 'LOKAL');

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_barang']) {
            $salesOrderInvoiceLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($limit !== null && $offset !== null) {
            $data = $salesOrderInvoiceLokal->findAll((int)$limit, (int)$offset);
        } else {
            $data = $salesOrderInvoiceLokal->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAllSalesOrderInvoiceLokalBarangWithoutLimit($condition, $addCondition)
    {
        $availableSort = [
            'no_faktur'          => 'sales_order_invoice.no_faktur',
            'nama_pelanggan'     => 'customers.name',
            'kode_pelanggan'     => 'customers.kode',
            'no_faktur'          => 'sales_order_invoice.no_faktur',
            'total_invoice'      => 'sales_order_invoice.total_invoice',
            'keterangan'         => 'sales_order_invoice.keterangan',
            'createdAt'          => 'sales_order_invoice.createdAt',
            'updatedAt'          => 'sales_order_invoice.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_invoice.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_invoice.*,
        sales_order_invoice.document_no AS doc_no,
        sales_order_invoice.document_type AS doc_type,
        sales_order_invoice_detail.id_barang_invoice AS id_barang_invoice,
        barang_master_sales.kode_barang AS kode_barang,
        barang_master_sales.barang_name AS barang_name,
        sales_order_invoice_detail.qty_invoice AS qty_invoice,
        satuans.kode_satuan AS kode_satuan,
                      DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
                      customers.name AS nama_pelanggan,
                      customers.kode AS kode_pelanggan,
                      CONCAT(employees.nip , ' - ', employees.name) AS salesName,
                      IFNULL(sales_order.no_sales_order, surat_jalan_so.no_surat_jalan) AS document_no";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'RIGHT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id')
            ->where($condition)
            ->orderBy("sales_order_invoice_detail.id_barang_invoice") // Tambahkan ini
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal
                ->like('no_faktur', $addCondition['search']);
        }

        if ($addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.id_customer', $addCondition['filter_customer']);
        }

        if ($addCondition['filter_jenis_dokumen'] == "pengiriman") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pengiriman');
        }

        if ($addCondition['filter_jenis_dokumen'] == "pesanan") {
            $salesOrderInvoiceLokal->where('sales_order_invoice.document_type', 'pesanan');
        }

        if ($addCondition['dateStart']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderInvoiceLokal->where('sales_order_invoice.tanggal_faktur <=', $addCondition['dateEnd']);
        }

        $salesOrderInvoiceLokal->where('tipe_invoice', 'LOKAL');

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_customer']) {
            $salesOrderInvoiceLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);
        $data = $salesOrderInvoiceLokal->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSalesOrderInvoiceLokalById($id)
    {
        $selectQry = "sales_order_invoice.*,
                      DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
                      users.name AS seller_name,
                      customers.name AS customer_name,
                      sales_order_invoice.status_tax AS status_tax,
                      sales_order_invoice.termasuk_pa AS termasuk_pa,
                      sales_order_invoice.jenis_penjualan AS jenis_penjualan,
                      sales_order.no_po, 
                      sales_order.nama_ecommerce";

        $dataSalesOrderInvoice = $this->asObject()
            ->join('users', 'users.id = sales_order_invoice.id_user', 'left')
            ->join('customers', 'customers.id = sales_order_invoice.id_customer', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id', 'left')
            ->join('employees', 'employees.id = sales_order.sales_id', 'left')
            ->select($selectQry)
            ->find($id);

        return $dataSalesOrderInvoice;
    }

    public function generateNoFaktur(): string
    {
        $format = "LKL/INV";
        $month = idate('m');
        $year = date('Y');
        $formatMonth = str_pad($month, 2, 0, STR_PAD_LEFT);
        $numberTemplate = "/$year/$formatMonth";

        // $lastData = $this->asObject()
        //     ->like('no_faktur', $numberTemplate, 'before')
        //     ->orderBy('createdAt', 'DESC')
        //     ->first();

        $lastData = $this->asObject()
            // ->where('id_company', $id_company)
            ->where("no_faktur LIKE '%$numberTemplate%'")
            ->orderBy('createdAt', 'DESC')
            ->first();

        $dummyNum = 0;
        if (!empty($lastData)) {
            $asd = explode('/', $lastData->no_faktur);
            foreach ($asd as $key => $item) {
                if ($key === 1) {
                    if (preg_match('/^(.*?)(\d+)$/', $item, $matches)) {
                        $prefix = $matches[1]; // "inv"
                        $number = $matches[2]; // "nomer invoice"
                    }
                }
            }
            $numbers = $number + 1; // increment nomer invoice

            $invNumber = $format . $numbers . $numberTemplate;
        } else {
            $numbers = 1; // nomer invoice awal jika tidak ada data

            $invNumber = $format . $numbers . $numberTemplate;
        }

        return $invNumber;
    }

    public function getAllSalesOrderInvoiceReport($condition, $addCondition, $limit = 10, $offset = 0)
    {

        $availableSort = [
            'tgl_invoice'          => 'sales_order_invoice.tgl_faktur',
            'no_sales_order'          => 'sales_order.no_sales_order',
            'no_faktur'          => 'sales_order_invoice.no_faktur',
            'nama_pelanggan'            => 'customers.name',
            'keterangan'      => 'sales_order_invoice.keterangan',
            'createdAt'         => 'sales_order_invoice.createdAt',
            'updatedAt'         => 'sales_order_invoice.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'sales_order_invoice.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_invoice.*,
                      DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
                      customers.name AS nama_pelanggan,
                      customers.kode AS kode_pelanggan";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer', 'LEFT')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->where($condition)
            ->where('sales_order_invoice.document_type !=', 'import')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $salesOrderInvoiceLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal
                ->like('no_faktur', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('customers.kode', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $salesOrderInvoiceLokal->where('customers.id', $addCondition['filter']);
        }

        if ($addCondition['startdate']) {
            $salesOrderInvoiceLokal->where('tanggal_faktur >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $salesOrderInvoiceLokal->where('tanggal_faktur <=', $addCondition['lastdate']);
        }

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $salesOrderInvoiceLokal->groupEnd();
        }

        if (isset($addCondition['tipe_penjualan']) && !empty($addCondition['tipe_penjualan'])) {
            if ($addCondition['tipe_penjualan'] !== 'semua') {
                $salesOrderInvoiceLokal->where('tipe_invoice', $addCondition['tipe_penjualan']);
            }
        }

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);
        $data = $salesOrderInvoiceLokal->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAllSalesOrderInvoiceReportLaporan($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_faktur'         => 'sales_order_invoice.no_faktur',
            'tanggal_faktur'    => 'sales_order_invoice.tanggal_faktur',
            'nama_pelanggan'    => 'customers.name',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'sales_order_invoice.no_faktur';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_invoice.*,
                      barang_master_sales.kode_barang AS kode_barang,
                      barang_master_sales.barang_name AS nama_barang,
                       satuans.kode_satuan AS satuan,
                       sales_order_invoice_detail.qty_invoice AS qty_invoice,
                        sales_order_invoice_detail.amount_invoice AS amount_invoice,
                        sales_order_invoice_detail.qty_invoice_awal AS qty,
                       sales_order_invoice_detail.qty_invoice_sisa AS qty_sekarang,
                      DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
                      customers.name AS nama_pelanggan,
                      customers.kode AS kode_pelanggan,
                      ";

        $salesOrderInvoice = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice', 'LEFT')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoice->countAllResults(false);

        if ($addCondition['filter_jenis_dokumen'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesOrderInvoice->groupStart();
        }

        if ($addCondition['filter_jenis_dokumen']) {
            $salesOrderInvoice->where('sales_order_invoice.document_type', $addCondition['filter_jenis_dokumen']);
        }

        // if ($addCondition['filter_status']) {

        //     if ($addCondition['filter_status'] == 1) {
        //         $salesOrderInvoice->where('sales_order_invoice.status_posting', "1");
        //     } else {
        //         $salesOrderInvoice->where('sales_order_invoice.status_posting', "0");
        //     }
        // }

        if ($addCondition['dateStart']) {
            $salesOrderInvoice->where('sales_order_invoice.tanggal_faktur >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $salesOrderInvoice->where('sales_order_invoice.tanggal_faktur <=', $addCondition['dateEnd']);
        }

        if ($addCondition['filter_jenis_dokumen'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesOrderInvoice->groupEnd();
        }

        $totalFilteredData = $salesOrderInvoice->countAllResults(false);
        $data = $salesOrderInvoice->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getFirstLikeByDocumentNo($documentNo)
    {
        return $this->asArray()->like('document_no', $documentNo)->first();
    }
}
