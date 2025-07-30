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
        //disini
        $availableSort = [
            'no_sales_order'          => 'sales_order.no_sales_order',
            'order_date'          => 'sales_order.order_date',
            'destination'            => 'sales_order.destination',
            'qty_barang'             => 'sales_order.qty_barang',
            'total_harga'             => 'sales_order.total_harga',
            'salesName'             => 'employees.name',
            'company'             => 'companies.company',
            'keterangan'      => 'sales_order.keterangan',
            'createdAt'         => 'sales_order.createdAt',
            'updatedAt'         => 'sales_order.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order.*, 
        employees.name as salesName, 
        companies.company AS company_name";

        $salesOrderLokal = $this->asObject()
            ->select($selectQry)
            ->join('employees', 'employees.id = sales_order.sales_id', 'left')
            ->join('companies', 'companies.id = sales_order.id_company', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_invoice'] || $addCondition['filter_surat_jalan'] || $addCondition['filter_customer']) {
            $salesOrderLokal->groupStart();
        }
        if ($addCondition['search']) {
            $salesOrderLokal
                ->like('no_sales_order', $addCondition['search']);
        }

        $salesOrderLokal->where('tipe_sales_order', 'LOKAL');

        if ($addCondition['dateStart']) {
            $salesOrderLokal->where('sales_order.order_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderLokal->where('sales_order.order_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['filter_customer']) {
            $salesOrderLokal->where('sales_order.id_customer', $addCondition['filter_customer']);
        }

        if ($addCondition['filter_invoice'] == "belum") {
            $salesOrderLokal->where('sales_order.sales_order_invoice_id', NULL);
        }

        if ($addCondition['filter_invoice'] == "sudah") {
            $salesOrderLokal->where('sales_order.sales_order_invoice_id !=', NULL);
        }

        if ($addCondition['filter_surat_jalan'] == "belum") {
            $salesOrderLokal->where('sales_order.surat_jalan_so_id', NULL);
        }

        if ($addCondition['filter_surat_jalan'] == "sudah") {
            $salesOrderLokal->where('sales_order.surat_jalan_so_id !=', NULL);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_invoice'] || $addCondition['filter_surat_jalan'] || $addCondition['filter_customer']) {
            $salesOrderLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderLokal->countAllResults(false);

        if ($limit && $offset) {
            $data = $salesOrderLokal->findAll($limit, $offset);
        } else {
            $data = $salesOrderLokal->findAll();
        }

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
}
