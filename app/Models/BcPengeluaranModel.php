<?php

namespace App\Models;

use CodeIgniter\Model;

class BcPengeluaranModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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


    public function getListOutstansingPengeluaran(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        $db = \Config\Database::connect();
        $whereSalesOrder = [];
        $whereDateSalesOrder = "";
        $searchSalesOrder = "";

        if (!empty($condition['company_id'])) {
            $whereSalesOrder[] = "sales_order.id_company = '$condition[company_id]'";
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDateSalesOrder = "AND sales_order.order_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));
            $searchSalesOrder = "
                AND(
                    barang_master_sales.kode_barang LIKE '%{$search}%'
                    OR barang_master_sales.barang_name LIKE '%{$search}%'
                    OR customers.name LIKE '%{$search}%'
                    OR sales_order.no_sales_order LIKE '%{$search}%'
                )
            ";
        }

        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'id',
            'tujuan_pengeluaran',
            'tanggal',
            'reference_no',
            'customer_name',
            'kode_barang',
            'barang_name',
            'qty',
            'kode_satuan',
            'amount'
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
            SELECT
                sales_order_detail.id,
                'ORDER FORM LOKAL' AS tujuan_pengeluaran,
                sales_order.order_date AS tanggal,
                sales_order.no_sales_order AS reference_no,
                customers.name AS customer_name,
                barang_master_sales.kode_barang,
                barang_master_sales.barang_name,
                sales_order_detail.qty,
                satuans.kode_satuan,
                sales_order_detail.amount
            FROM
                sales_order_detail
            LEFT JOIN sales_order ON sales_order.id = sales_order_detail.id_sales_order
            LEFT JOIN barang_master_sales ON barang_master_sales.id = sales_order_detail.id_barang
            LEFT JOIN satuans ON satuans.id = barang_master_sales.satuan_id 
            LEFT JOIN customers ON customers.id = sales_order.id_customer
            WHERE sales_order_detail.deletedAt IS NULL
            $filterCondition
            $whereDateSalesOrder
            $searchSalesOrder
        ";

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = "
            SELECT * FROM ($baseQuery) AS x
            $orderBy
            LIMIT $limit OFFSET $offset
        ";

        $data = $db->query($mainQuery)->getResultArray();

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function referensiPengeluaranOrderFormLokal($companyId)
    {
        $bc25Model = new BC25Model();
        $bc25List = $bc25Model->where('company_id', $companyId)->where('jenis_pengeluaran', "ORDER FORM LOKAL")->where('deletedAt', null)->findAll();

        $build = $this->db->table('sales_order')
            ->where('id_company', $companyId)
            ->where('deletedAt', null);

        if (count($bc25List) != 0) {
            $orderFormIdArr = array_column($bc25List, 'multiple_reference_id');
            $build->whereNotIn('id', $orderFormIdArr);
        }
        $orderForm = $build->orderBy('id', "desc")->get()->getResultArray();
        $dataResult = array();

        foreach ($orderForm as $o) {
            array_push($dataResult, [
                'id' => $o['id'],
                'reference_no' => $o['no_sales_order']
            ]);
        }

        return $dataResult;
    }
}
