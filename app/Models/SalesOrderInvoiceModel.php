<?php

namespace App\models;

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

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'sales_order_invoice.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_invoice.*,
                      DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
                      customers.name AS nama_pelanggan,
                      customers.kode AS kode_pelanggan,
                      IFNULL(sales_order.no_sales_order, surat_jalan_so.no_surat_jalan) AS document_no";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal->groupStart();
        }
        if ($addCondition['search']) {
            $salesOrderInvoiceLokal
                ->like('no_faktur', $addCondition['search']);
        }

        $salesOrderInvoiceLokal->where('tipe_invoice', 'LOKAL');

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);
        $data = $salesOrderInvoiceLokal->findAll($limit, $offset);

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
                      sales_order_invoice.termasuk_pa AS termasuk_pa";

        $dataSalesOrderInvoice = $this->asObject()
            ->join('users', 'users.id = sales_order_invoice.id_user')
            ->join('customers', 'customers.id = sales_order_invoice.id_customer ')
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
                      customers.kode AS kode_pelanggan,
                      IFNULL(sales_order.no_sales_order, surat_jalan_so.no_surat_jalan) AS document_no";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pesanan"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_invoice.document_id AND sales_order_invoice.document_type = "pengiriman"', 'LEFT')
            ->where($condition)
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

        $totalFilteredData = $salesOrderInvoiceLokal->countAllResults(false);
        $data = $salesOrderInvoiceLokal->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
