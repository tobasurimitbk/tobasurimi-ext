<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'surat_jalan_so';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_po',
        'id_customer',
        'multiple_id_so',
        'multiple_no_so',
        'sales_order_invoice_id',
        'no_po',
        'shipping_date',
        'no_surat_jalan',
        'counter_print',
        'note',
        'id_company',
        'posting',
        'terms'
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

    public function getAllSuratJalan($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_surat_jalan'   => 'surat_jalan_so.no_surat_jalan',
            'tipe_sales_order' => 'sales_order.tipe_sales_order',
            'no_so'            => 'surat_jalan_so.multiple_no_so',
            'kode_pelanggan'   => 'customers.kode',
            'nama_pelanggan'   => 'customers.name',
            'shipping_date'    => 'surat_jalan_so.shipping_date',
            'createdAt'        => 'surat_jalan_so.createdAt',
            'updatedAt'        => 'surat_jalan_so.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'surat_jalan_so.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "surat_jalan_so.*,  
        sales_order.jenis_penjualan, 
        sales_order.nama_ecommerce, 
        sales_order.sales_id,
        customers.name as nama_pelanggan,
        customers.kode as kode_pelanggan, 
        companies.company AS company_name,
        SUM(sales_order.total_harga) as total_harga, 
        SUM(sales_order.estimated_freight) as estimated_freight, 
        SUM(surat_jalan_so_detail.amount) as sum_amount_sj_detail, 
        sales_order.tipe_sales_order,
        employees.name AS customerSales,
        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(surat_jalan_so.no_surat_jalan, '/', -2), '/', 1) AS UNSIGNED) AS tahun_so,
        CAST(
            FIELD(
                SUBSTRING_INDEX(SUBSTRING_INDEX(surat_jalan_so.no_surat_jalan, '/', -3), '/', 1),
                'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'
            ) AS UNSIGNED
        ) AS bulan_so,
        CAST(SUBSTRING_INDEX(surat_jalan_so.no_surat_jalan, '/', -1) AS UNSIGNED) AS nomor_so
    ";

        $SuratJalan = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = surat_jalan_so.id_customer')
            ->join('sales_order', 'sales_order.surat_jalan_so_id = surat_jalan_so.id', 'left')
            ->join('surat_jalan_so_detail', 'surat_jalan_so_detail.id_surat_jalan = surat_jalan_so.id AND surat_jalan_so_detail.deletedAt IS NULL', 'left')
            ->join('employees', 'employees.id = sales_order.sales_id', 'left')
            ->join('companies', 'companies.id = surat_jalan_so.id_company', 'left')
            ->where($condition)
            ->groupBy('surat_jalan_so.no_surat_jalan');

        $totalData = $SuratJalan->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_invoice'] || $addCondition['filter_customer'] || $addCondition['filter_company']) {
            $SuratJalan->groupStart();
        }
        if ($addCondition['search']) {
            $SuratJalan->like('no_surat_jalan', $addCondition['search']);
        }
        if ($addCondition['filter_customer']) {
            $SuratJalan->where('surat_jalan_so.id_customer', $addCondition['filter_customer']);
        }
        if ($addCondition['filter_company']) {
            $SuratJalan->where('surat_jalan_so.id_company', $addCondition['filter_company']);
        }
        if ($addCondition['filter_invoice'] == "belum") {
            $SuratJalan->where('surat_jalan_so.sales_order_invoice_id', NULL);
        }
        if ($addCondition['filter_invoice'] == "sudah") {
            $SuratJalan->where('surat_jalan_so.sales_order_invoice_id !=', NULL);
        }
        if ($addCondition['dateStart']) {
            $SuratJalan->where('surat_jalan_so.shipping_date >=', $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $SuratJalan->where('surat_jalan_so.shipping_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_invoice'] || $addCondition['filter_customer'] || $addCondition['filter_company']) {
            $SuratJalan->groupEnd();
        }

        $totalFilteredData = $SuratJalan->countAllResults(false);

        // Sorting
        if (($addCondition['sort'] ?? '') === 'no_surat_jalan') {
            $SuratJalan->orderBy("tahun_so", $sortType)
                ->orderBy("bulan_so", $sortType)
                ->orderBy("nomor_so", $sortType);
        } else {
            $SuratJalan->orderBy($sort, $sortType);
        }

        if ($limit !== null && $offset !== null) {
            $data = $SuratJalan->findAll((int)$limit, (int)$offset);
        } else {
            $data = $SuratJalan->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSuratJalanById($id)
    {
        $selectQry = "surat_jalan_so.*,
                      sales_order.jenis_penjualan,
                      surat_jalan_so.no_po,
                      sales_order.nama_ecommerce,
                      users.name as seller_name,
                      customers.name as customer_name ,
                      customers.address,customers.phone,
                      customers.address AS customerAddress,
                      customers.phone AS customerPhone,
                      customers.termin,
                      metadata.value AS customerTermin,
                      employees.name AS customerSales";

        $dataSuratJalan = $this->asObject()
            ->join('users', 'users.id = surat_jalan_so.id_user', 'left')
            ->join('customers', 'customers.id = surat_jalan_so.id_customer')
            ->join('sales_order', 'sales_order.surat_jalan_so_id = surat_jalan_so.id', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->join('employees', 'employees.id = sales_order.sales_id', 'left')
            ->select($selectQry)
            ->find($id);

        if (empty($dataSuratJalan)) return null;

        $dataMultpleid = json_decode($dataSuratJalan->multiple_id_so);
        $dataMultpleNo = json_decode($dataSuratJalan->multiple_no_so);

        $dataSuratJalan->multiple_id_so = $dataMultpleid;
        $dataSuratJalan->multiple_no_so = $dataMultpleNo;

        return $dataSuratJalan;
    }

    public function getNumber($periode, $id_company)
    {

        $no = 0;
        $dummyNum = 0;
        $data = $this->asObject()->where('id_company', $id_company)->where("no_surat_jalan LIKE '%$periode%'")->orderBy('id', 'DESC')->first();
        if ($data) {
            $pecah = explode("/", $data->no_surat_jalan);
            foreach ($pecah as $key => $item) {
                if ($key === 4) {
                    $dummyNum += $item;
                }
            }
        }

        $number = $dummyNum + 1;
        // $check = $number % 99999;
        // if ($check === 0) {
        //     $this->update($data->id, ['no' => 99999]);
        //     return 99999;
        // } else {
        //     if ($data) {
        //         $this->update($data->id, ['no' => $check]);
        //     } else {
        //         $this->update($idNewCreate, ['no' => $check]);
        //     }
        //     return $check;
        // }
        return $number;
    }
    
    public function getAllSalesOrderLokalBarang($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'createdAt'          => 'surat_jalan_so.createdAt',
            'updatedAt'          => 'surat_jalan_so.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'surat_jalan_so.shipping_date';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            surat_jalan_so.id,
            surat_jalan_so.no_surat_jalan as no_sales_order,
            surat_jalan_so.note as keterangan,
            surat_jalan_so.posting,
            surat_jalan_so.counter_print,
            surat_jalan_so_detail.id_barang AS id_barang,
            barang_master_sales.kode_barang AS kode_barang,
            barang_master_sales.barang_name AS barang_name,
            SUM(surat_jalan_so_detail.qty) AS qty_order,
            satuans.kode_satuan AS kode_satuan,
            DATE_FORMAT(surat_jalan_so.shipping_date, '%d/%m/%Y') AS tanggal_order,
            customers.name AS nama_pelanggan,
            customers.kode AS kode_pelanggan,
            employees.name AS salesName,
            sales_order_invoice.id AS id_sales_order_invoice,
            sales_order_invoice.no_faktur AS document_no,
            DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
            sales_order_invoice_detail.qty_invoice AS qty_faktur
        ";

        $salesOrderInvoiceLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = surat_jalan_so.id_customer')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('surat_jalan_so_detail', 'surat_jalan_so_detail.id_surat_jalan = surat_jalan_so.id', 'RIGHT')
            ->join('sales_order_invoice_detail', 'sales_order_invoice_detail.id_surat_jalan = surat_jalan_so.id AND sales_order_invoice_detail.id_barang_invoice = surat_jalan_so_detail.id_barang', 'LEFT')
            ->join('sales_order_invoice', "sales_order_invoice.id = sales_order_invoice_detail.id_sales_order_invoice AND sales_order_invoice.document_type = 'pengiriman'", 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = surat_jalan_so_detail.id_barang')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id')
            ->where($condition)
            ->groupBy('surat_jalan_so_detail.id')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderInvoiceLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_jenis_dokumen'] || $addCondition['filter_barang']) {
            $salesOrderInvoiceLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderInvoiceLokal
                ->like('no_surat_jalan', $addCondition['search']);
        }

        if ($addCondition['filter_barang']) {
            $salesOrderInvoiceLokal->where('surat_jalan_so_detail.id_barang', $addCondition['filter_customer']);
        }

        if ($addCondition['dateStart']) {
            $salesOrderInvoiceLokal->where('surat_jalan_so.shipping_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderInvoiceLokal->where('surat_jalan_so.shipping_date <=', $addCondition['dateEnd']);
        }

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
