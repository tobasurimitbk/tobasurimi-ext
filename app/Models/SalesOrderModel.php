<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\SalesOrderDetailModel;

class SalesOrderModel extends Model
{
    protected $SalesOrderDetailModel;

    protected $table      = 'sales_order';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;

    protected $allowedFields = [
        'id_company',
        'id_user',
        'id_po',
        'id_customer',
        'jenis_penjualan',
        'sales_id',
        'bc_type',
        'nama_ecommerce',
        'no_sales_order',
        'no_po',
        'surat_jalan_so_id',
        'sales_order_invoice_id',
        'destination',
        'due_date',
        'keterangan_dokumen',
        'keterangan',
        'payment_terms',
        'no_aju',
        'no_pendaftaran',
        'tanggal_pendaftaran',
        'qty_barang',
        'nilai_fob',
        'dokumen_bea_cukai',
        'dokumen_sertifikasi_kesehatan',
        'total_harga',
        'tipe_sales_order',
        'order_date',
        'shipping_date',
        'discount_percentage',
        'ppn',
        'estimated_freight',
        'tax_status',
        'include_pa',
        'paid_amt',
        'used',
        'counter_print',
        'posting',
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

    public function __construct()
    {
        parent::__construct();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
    }

    public function getAllSalesOrderLokal($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_sales_order'        => 'sales_order.no_sales_order',
            'order_date'            => 'sales_order.order_date',
            'shipping_date'         => 'sales_order.shipping_date',
            'destination'           => 'sales_order.destination',
            'company'               => 'companies.company',
            'salesName'             => 'employees.name',
            'qty_barang'            => 'sales_order.qty_barang',
            'qty_barang'            => 'sales_order.qty_barang',
            'total_harga'           => 'sales_order.total_harga',
            'keterangan'            => 'sales_order.keterangan',
            'createdAt'             => 'sales_order.createdAt',
            'updatedAt'             => 'sales_order.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sortField = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $builder = $this->db->table('sales_order')
            ->select("sales_order.*, 
            employees.name as salesName, 
            companies.company AS company_name,
            CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no_sales_order, '/', -2), '/', 1) AS UNSIGNED) AS tahun_so,
            FIELD(SUBSTRING_INDEX(SUBSTRING_INDEX(no_sales_order, '/', -3), '/', 1),
                'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII') AS bulan_so,
            CAST(SUBSTRING_INDEX(no_sales_order, '/', -1) AS UNSIGNED) AS nomor_so")
            ->join('employees', 'employees.id = sales_order.sales_id', 'left')
            ->join('companies', 'companies.id = sales_order.id_company', 'left')
            ->where($condition)
            ->where('tipe_sales_order', 'LOKAL');

        // Filtering
        if ($addCondition['search']) {
            $builder->like('no_sales_order', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $builder->where('sales_order.order_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $builder->where('sales_order.order_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['filter_customer']) {
            $builder->where('sales_order.id_customer', $addCondition['filter_customer']);
        }

        if ($addCondition['filter_company']) {
            $builder->where('sales_order.id_company', $addCondition['filter_company']);
        }

        if ($addCondition['filter_invoice'] == "belum") {
            $builder->where('sales_order.sales_order_invoice_id', null);
        } elseif ($addCondition['filter_invoice'] == "sudah") {
            $builder->where('sales_order.sales_order_invoice_id IS NOT', null);
        }

        if ($addCondition['filter_surat_jalan'] == "belum") {
            $builder->where('sales_order.surat_jalan_so_id', null);
        } elseif ($addCondition['filter_surat_jalan'] == "sudah") {
            $builder->where('sales_order.surat_jalan_so_id IS NOT', null);
        }

        // Clone builder untuk count data
        $totalBuilder = clone $builder;
        $totalFilteredData = $totalBuilder->countAllResults();

        // Sorting
        if (($addCondition['sort'] ?? '') === 'no_sales_order') {
            $builder->orderBy("tahun_so", $sortType)
                ->orderBy("bulan_so", $sortType)
                ->orderBy("nomor_so", $sortType);
        } else {
            $builder->orderBy($sortField, $sortType);
        }

        // Pagination
        if ($limit && $offset >= 0) {
            $builder->limit($limit, $limit * $offset);
        }

        $data = $builder->get()->getResult();

        // Total tanpa filter
        $totalData = $this->db->table('sales_order')
            ->where($condition)
            ->where('tipe_sales_order', 'LOKAL')
            ->countAllResults();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSalesOrderLokalById($id)
    {
        $selectQry = "sales_order.*,
                      users.name as seller_name,
                      customers.termin as termin_id,
                      customers.name as customer_name ,
                      customers.address, customers.phone AS customerPhone,
                      customers.tipe_pelanggan as tipe_pelanggan,
                      CONCAT(employees.nip , ' - ', employees.name) AS salesName,
                      termin.value AS termin,
                      tipe_pelanggan.value AS tipe_pelanggan_value";

        $dataSalesOrder = $this->asObject()
            ->join('users', 'users.id = sales_order.id_user', 'left')
            ->join('customers', 'customers.id = sales_order.id_customer ', 'left')
            ->join('metadata as termin', 'termin.id = sales_order.payment_terms', 'left')
            ->join('metadata as tipe_pelanggan', 'tipe_pelanggan.id = customers.tipe_pelanggan', 'left')
            ->join('employees', 'employees.id = sales_order.sales_id ', 'left')
            ->select($selectQry)
            ->find($id);

        $selectQueryDetail = "sales_order_detail.*,
                              sales_order_detail.discount_percentage AS disc, 
                              warehouses.warehouse_name, 
                              barang_master_sales.kode_barang AS kode_barang,
                              barang_master_sales.barang_name AS nama_barang,
                              sales_order_detail.harga_barang AS harga_barang,
                              barang_master_sales.satuan_id,
                              satuans.kode_satuan AS satuan";
        $detail = $this->SalesOrderDetailModel
            ->where('tipe_input', "order_form")
            ->where('id_sales_order', $id)
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang', 'left')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_detail.id_warehouse', 'left')
            ->select($selectQueryDetail)
            ->findAll();

        if ($dataSalesOrder) {
            $dataSalesOrder->detail = $detail;
        }

        return $dataSalesOrder;
    }

    public function getLaporanPesananPenjualanPerPelanggan($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_sales_order'     => 'sales_order.no_sales_order',
            'nama_pelanggan'     => 'customers.name',
            'amount'             => 'sales_order_detail.amount',
            'createdAt'          => 'sales_order.createdAt',
            'updatedAt'          => 'sales_order.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
                    sales_order.id,
                    sales_order.no_sales_order,
                    sales_order.surat_jalan_so_id,
                    sales_order.sales_order_invoice_id,
                    DATE_FORMAT(sales_order.order_date, '%d/%m/%Y') AS order_date,
                    DATE_FORMAT(sales_order.shipping_date, '%d/%m/%Y') AS shipping_date,
                    customers.name AS nama_pelanggan,
                    SUM(sales_order_detail.qty) AS sum_qty,
                    SUM(sales_order_detail.amount) AS sum_amount";

        $salesOrderLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('sales_order_detail', 'sales_order_detail.id_sales_order = sales_order.id', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang', 'LEFT')
            ->where($condition)
            ->groupBy('sales_order.id')
            ->orderBy('customers.name', 'ASC')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['dateStartShip'] || $addCondition['dateEndShip'] || $addCondition['filter_customer'] || $addCondition['filter_status']) {
            $salesOrderLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderLokal
                ->like('no_sales_order', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search']);
        }

        if ($addCondition['filter_customer']) {
            $salesOrderLokal->where('sales_order.id_customer', $addCondition['filter_customer']);
        }

        if ($addCondition['dateStart']) {
            $salesOrderLokal->where('sales_order.order_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderLokal->where('sales_order.order_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStartShip']) {
            $salesOrderLokal->where('sales_order.shipping_date >=',  $addCondition['dateStartShip']);
        }
        if ($addCondition['dateEndShip']) {
            $salesOrderLokal->where('sales_order.shipping_date <=', $addCondition['dateEndShip']);
        }

        if ($addCondition['filter_status'] == "belum") {
            $salesOrderLokal->where('sales_order.surat_jalan_so_id', null)
                ->where('sales_order.sales_order_invoice_id', null);
        } elseif ($addCondition['filter_status'] == "selesai") {
            $salesOrderLokal->groupStart();
            $salesOrderLokal->where('sales_order.surat_jalan_so_id IS NOT', null);
            $salesOrderLokal->orWhere('sales_order.sales_order_invoice_id IS NOT', null);
            $salesOrderLokal->groupEnd();
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['dateStartShip'] || $addCondition['dateEndShip'] || $addCondition['filter_customer'] || $addCondition['filter_status']) {
            $salesOrderLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderLokal->countAllResults(false);

        if ($limit !== null && $offset !== null) {
            $data = $salesOrderLokal->findAll((int)$limit, (int)$offset);
        } else {
            $data = $salesOrderLokal->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAllSalesOrderLokalBarang($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'createdAt'          => 'sales_order.createdAt',
            'updatedAt'          => 'sales_order.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order.order_date';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            sales_order.id,
            sales_order.no_sales_order,
            sales_order.keterangan,
            sales_order.total_harga,
            sales_order.tipe_sales_order,
            sales_order.posting,
            sales_order.counter_print,
            sales_order.jenis_penjualan AS jenis_penjualan,
            sales_order.nama_ecommerce AS nama_ecommerce,
            sales_order_detail.id_barang AS id_barang,
            barang_master_sales.kode_barang AS kode_barang,
            barang_master_sales.barang_name AS barang_name,
            SUM(sales_order_detail.qty) AS qty_order,
            satuans.kode_satuan AS kode_satuan,
            DATE_FORMAT(sales_order.order_date, '%d/%m/%Y') AS tanggal_order,
            customers.name AS nama_pelanggan,
            customers.kode AS kode_pelanggan,
            employees.name AS salesName,
            sales_order_invoice.id AS id_sales_order_invoice,
            surat_jalan_so.id AS id_surat_jalan_so,
            COALESCE(sales_order_invoice.no_faktur, surat_jalan_so.no_surat_jalan) AS document_no,
            DATE_FORMAT(COALESCE(sales_order_invoice.tanggal_faktur, surat_jalan_so.shipping_date), '%d/%m/%Y') AS tanggal_faktur,
            COALESCE(sales_order_invoice_detail.qty_invoice, surat_jalan_so_detail.qty) AS qty_faktur
        ";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            // ->join(
            //     'sales_order_invoice',
            //     "FIND_IN_SET(
            //         sales_order.id,
            //         REPLACE(REPLACE(REPLACE(sales_order_invoice.document_id, '[', ''), ']', ''), CHAR(34), '')
            //     )
            //     AND sales_order_invoice.document_type = 'pesanan'",
            //     'LEFT',
            //     false
            // )
            // ->join(
                //     'surat_jalan_so',
            //     "FIND_IN_SET(
            //         sales_order.id,
            //         REPLACE(REPLACE(REPLACE(surat_jalan_so.multiple_id_so, '[', ''), ']', ''), CHAR(34), '')
            //     )",
            //     'LEFT',
            //     false
            // )
            ->join('sales_order_detail', 'sales_order_detail.id_sales_order = sales_order.id', 'RIGHT')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_sales_order = sales_order.id AND sales_order_invoice_detail.id_barang_invoice = sales_order_detail.id_barang', 'LEFT')
            ->join('sales_order_invoice', "sales_order_invoice.id = sales_order_invoice_detail.id_sales_order_invoice AND sales_order_invoice.document_type = 'pesanan'", 'LEFT')
            ->join('surat_jalan_so_detail', 'surat_jalan_so_detail.id_sales_order_detail = sales_order_detail.id', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = surat_jalan_so_detail.id_surat_jalan', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id')
            ->where($condition)
            ->groupBy('sales_order_detail.id')
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
            $salesOrderInvoiceLokal->where('sales_order_detail.id_barang', $addCondition['filter_customer']);
        }

        if ($addCondition['dateStart']) {
            $salesOrderInvoiceLokal->where('sales_order.order_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderInvoiceLokal->where('sales_order.order_date <=', $addCondition['dateEnd']);
        }

        $salesOrderInvoiceLokal->where('tipe_sales_order', 'LOKAL');

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
}
