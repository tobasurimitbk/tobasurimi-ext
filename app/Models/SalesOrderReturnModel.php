<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderReturnModel extends Model
{

    protected $table      = 'sales_order_return';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;

    protected $allowedFields = [
        'id_user',
        'id_invoice',
        'no_return',
        'tanggal_return',
        'note',
        'id_company',
        'is_approved',
        'id_warehouse',
        'already_paid',
        'sumber',
        'id_customer'
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

    public function getAllReturn($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_return'         => 'sales_order_return.no_return',
            'kode_customer'    => 'customers.kode',
            'nama_customer'    => 'customers.name',
            'shipping_date'     => 'sales_order_return.tanggal_return',
            'createdAt'         => 'sales_order_return.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_return.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_return.id AS id,
                      sales_order_return.no_return AS returnNo,
                      sales_order_return.tanggal_return AS returnDate,
                      sales_order_return.already_paid AS already_paid,
                      sales_order_return.is_approved AS is_approved,
                        CASE 
                            WHEN sales_order_return.sumber = 'invoice' THEN sales_order_invoice.no_faktur
                            WHEN sales_order_return.sumber = 'surat_jalan' THEN surat_jalan_so.no_surat_jalan
                            WHEN sales_order_return.sumber = 'order_form' THEN sales_order.no_sales_order
                            ELSE '-'
                        END AS refNo,
                        CASE 
                            WHEN sales_order_return.sumber = 'invoice' THEN csoi.name
                            WHEN sales_order_return.sumber = 'surat_jalan' THEN csj.name
                            WHEN sales_order_return.sumber = 'order_form' THEN cso.name
                            ELSE '-'
                        END AS customerName";

        $soReturn = $this->asObject()
            ->select($selectQry)
            ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice', 'left')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_return.id_invoice', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_return.id_invoice', 'left')
            ->join('customers cso', 'cso.id = sales_order.id_customer', 'left')
            ->join('customers csj', 'csj.id = surat_jalan_so.id_customer', 'left')
            ->join('customers csoi', 'csoi.id = sales_order_invoice.id_customer', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $soReturn->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $soReturn->groupStart();
        }
        if ($addCondition['search']) {
            $soReturn
                ->like('sales_order_return.no_return', $addCondition['search'])
                ->orLike('sales_order_invoice.no_faktur', $addCondition['search']);
        }
        if ($addCondition['dateStart']) {
            $soReturn->where('sales_order_return.tanggal_return >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $soReturn->where('sales_order_return.tanggal_return <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $soReturn->groupEnd();
        }

        $totalFilteredData = $soReturn->countAllResults(false);

        if ($limit && $offset) {
            $data = $soReturn->findAll($limit, $offset);
        } else {
            $data = $soReturn->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    // public function generateNoReturn(): string
    // {
    //     $format = "RETURN";
    //     $month = idate('m');
    //     $year = date('Y');
    //     $formatMonth = str_pad($month, 2, 0, STR_PAD_LEFT);
    //     $numberTemplate = "/$year/$formatMonth/";

    //     $lastData = $this->asObject()
    //         ->where("no_return LIKE '%$numberTemplate%'")
    //         ->orderBy('createdAt', 'DESC')
    //         ->first();

    //     $dummyNum = 0;
    //     if (!empty($lastData)) {
    //         $asd = explode('/', $lastData->no_return);
    //         foreach ($asd as $key => $item) {
    //             if ($key === 3) {
    //                 if (preg_match('/^(.*?)(\d+)$/', $item, $matches)) {
    //                     $prefix = $matches[1]; // "inv"
    //                     $number = $matches[2]; // "nomer invoice"
    //                 }
    //             }
    //         }
    //         $numbers = $number + 1; // increment nomer invoice

    //         $invNumber = $format . $numberTemplate . $numbers;
    //     } else {
    //         $numbers = 1; // nomer invoice awal jika tidak ada data

    //         $invNumber = $format . $numberTemplate . $numbers;
    //     }

    //     return $invNumber;
    // }

    public function generateNoReturn(): string
    {
        $format = "RETURN";
        $month = idate('m');
        $year = date('Y');
        $formatMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
        $numberTemplate = "/" . $year . "/" . $formatMonth . "/";

        // Ambil data terakhir
        $lastData = $this->asObject()
            ->orderBy('createdAt', 'DESC')
            ->first();

        if ($lastData && !empty($lastData->no_return)) {
            $parts = explode('/', $lastData->no_return);
            $lastNumber = isset($parts[3]) && is_numeric($parts[3]) ? intval($parts[3]) : 0;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // format nomor jadi 3 digit
        $paddedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $invNumber = $format . $numberTemplate . $paddedNumber;

        return $invNumber;
    }

    public function getAllSalesOrderReturnLokal($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_faktur'          => 'sales_order_return.no_return',
            'nama_pelanggan'     => 'customers.name',
            'kode_pelanggan'     => 'customers.kode',
            'total_invoice'      => 'sales_order_return.total_invoice',
            'keterangan'         => 'sales_order_return.keterangan',
            'createdAt'          => 'sales_order_return.createdAt',
            'updatedAt'          => 'sales_order_return.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_return.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
        sales_order_return.id,
        sales_order_return.no_return,
        CASE 
            WHEN sales_order_return.sumber = 'invoice' THEN sales_order_invoice.no_faktur
            WHEN sales_order_return.sumber = 'surat_jalan' THEN surat_jalan_so.no_surat_jalan
            WHEN sales_order_return.sumber = 'order_form' THEN sales_order.no_sales_order
            ELSE '-'
        END AS no_dokumen,
        DATE_FORMAT(sales_order_return.tanggal_return, '%d/%m/%Y') AS tanggal_return,
        sales_order_return.note,
        SUM(sales_order_return_detail.amount_return) AS sum_amount_return,
        customers.name AS nama_pelanggan
        ";

        $salesOrderReturnLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_return.id_customer')
            ->join('sales_order', 'sales_order.id = sales_order_return.id_invoice AND sales_order_return.sumber = "order_form"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_return.id_invoice AND sales_order_return.sumber = "surat_jalan"', 'LEFT')
            ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice AND sales_order_return.sumber = "invoice"', 'LEFT')
            ->join('sales_order_return_detail', 'sales_order_return_detail.id_sales_order_return = sales_order_return.id', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_return_detail.id_barang_return', 'LEFT')
            ->where($condition)
            ->groupBy('sales_order_return.id')
            ->orderBy('sales_order_return.id_customer')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderReturnLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_customer']) {
            $salesOrderReturnLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderReturnLokal
                ->like('no_return', $addCondition['search']);
        }

        if ($addCondition['filter_customer']) {
            $salesOrderReturnLokal->where('sales_order_return.id_customer', $addCondition['filter_customer']);
        }

        if ($addCondition['dateStart']) {
            $salesOrderReturnLokal->where('sales_order_return.tanggal_return >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderReturnLokal->where('sales_order_return.tanggal_return <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_customer']) {
            $salesOrderReturnLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderReturnLokal->countAllResults(false);

        if ($limit !== null && $offset !== null) {
            $data = $salesOrderReturnLokal->findAll((int)$limit, (int)$offset);
        } else {
            $data = $salesOrderReturnLokal->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAllSalesOrderReturnLokalBarang($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_faktur'          => 'sales_order_return.no_return',
            'nama_barang'        => 'barang_master_sales.barang_name',
            'kode_barang'        => 'barang_master_sales.kode_barang',
            'total_invoice'      => 'sales_order_return.total_invoice',
            'keterangan'         => 'sales_order_return.keterangan',
            'createdAt'          => 'sales_order_return.createdAt',
            'updatedAt'          => 'sales_order_return.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_return.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            sales_order_return.id,
            sales_order_return.no_return,
            CASE 
                WHEN sales_order_return.sumber = 'invoice' THEN sales_order_invoice.no_faktur
                WHEN sales_order_return.sumber = 'surat_jalan' THEN surat_jalan_so.no_surat_jalan
                WHEN sales_order_return.sumber = 'order_form' THEN sales_order.no_sales_order
                ELSE '-'
            END AS no_dokumen,
            DATE_FORMAT(sales_order_return.tanggal_return, '%d/%m/%Y') AS tanggal_return,
            sales_order_return.note,
            SUM(sales_order_return_detail.amount_return) AS sum_amount_return,
            SUM(sales_order_return_detail.qty_return) AS sum_qty_return,
            customers.name AS nama_pelanggan,
            barang_master_sales.barang_name AS nama_barang,
            barang_master_sales.kode_barang AS kode_barang,
            satuans.kode_satuan
        ";

        $salesOrderReturnLokal = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_return.id_customer')
            ->join('sales_order', 'sales_order.id = sales_order_return.id_invoice AND sales_order_return.sumber = "order_form"', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_return.id_invoice AND sales_order_return.sumber = "surat_jalan"', 'LEFT')
            ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice AND sales_order_return.sumber = "invoice"', 'LEFT')
            ->join('sales_order_return_detail', 'sales_order_return_detail.id_sales_order_return = sales_order_return.id', 'LEFT')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_return_detail.id_barang_return', 'LEFT')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            ->where($condition)
            ->groupBy('barang_master_sales.id')
            ->orderBy('barang_master_sales.barang_name')
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderReturnLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_customer']) {
            $salesOrderReturnLokal->groupStart();
        }

        if ($addCondition['search']) {
            $salesOrderReturnLokal
                ->like('no_return', $addCondition['search']);
        }

        if ($addCondition['filter_customer']) {
            $salesOrderReturnLokal->where('sales_order_return.id_customer', $addCondition['filter_customer']);
        }

        if ($addCondition['dateStart']) {
            $salesOrderReturnLokal->where('sales_order_return.tanggal_return >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderReturnLokal->where('sales_order_return.tanggal_return <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_customer']) {
            $salesOrderReturnLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderReturnLokal->countAllResults(false);

        if ($limit !== null && $offset !== null) {
            $data = $salesOrderReturnLokal->findAll((int)$limit, (int)$offset);
        } else {
            $data = $salesOrderReturnLokal->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
