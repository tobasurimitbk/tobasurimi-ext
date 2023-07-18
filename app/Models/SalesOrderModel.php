<?php

namespace App\models;

use CodeIgniter\Model;

class SalesOrderModel extends Model
{

    protected $table      = 'sales_order';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;

    protected $allowedFields = [
        'id_user',
        'id_po',
        'id_customer',
        'no_sales_order',
        'no_po',
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
        'include_pa'
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

    public function getAllSalesOrderLokal($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_sales_order'          => 'sales_order.spp_type',
            'destination'            => 'sales_order.destination',
            'qty_barang'             => 'sales_order.qty_barang',
            'total_harga'             => 'sales_order.total_harga',
            'keterangan'      => 'sales_order.keterangan',
            'createdAt'         => 'sales_order.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'sales_order.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order.*";

        $salesOrderLokal = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $salesOrderLokal->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesOrderLokal->groupStart();
        }
        if ($addCondition['search']) {
            $salesOrderLokal
                ->like('no_sales_order', $addCondition['search']);
        }

        $salesOrderLokal->where('tipe_sales_order', 'LOKAL');

        if ($addCondition['dateStart']) {
            $salesOrderLokal->where('purchase_requests.order_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $salesOrderLokal->where('purchase_requests.order_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesOrderLokal->groupEnd();
        }

        $totalFilteredData = $salesOrderLokal->countAllResults(false);
        $data = $salesOrderLokal->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
