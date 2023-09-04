<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderReturnModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_order_returns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'return_no',
        'customer_id',
        'sales_order_id',
        'return_date',
        'note',
        'returned_item'
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

    public function getAllSOReturn($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_surat_jalan'    => 'surat_jalan_so.no_surat_jalan',
            'no_so'             => 'surat_jalan_so.multiple_no_so',
            'kode_pelanggan'    => 'customers.kode',
            'nama_pelanggan'    => 'customers.name',
            'shipping_date'     => 'sales_order_returns.shipping_date',
            'createdAt'         => 'sales_order_returns.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_returns.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_returns.id AS id,
                      sales_order_returns.return_no AS returnNo,
                      sales_order_returns.return_date AS returnDate,
                      sales_order.no_sales_order AS salesOrderNo,
                      customers.name AS customerName";

        $soReturn = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order_returns.customer_id')
            ->join('sales_order', 'sales_order.id = sales_order_returns.sales_order_id')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $soReturn->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $soReturn->groupStart();
        }
        if ($addCondition['search']) {
            $soReturn
                ->like('no_surat_jalan', $addCondition['search']);
        }
        if ($addCondition['dateStart']) {
            $soReturn->where('surat_jalan_so.shipping_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $soReturn->where('surat_jalan_so.shipping_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $soReturn->groupEnd();
        }

        $totalFilteredData = $soReturn->countAllResults(false);
        $data = $soReturn->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
