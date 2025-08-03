<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderExportDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_order_detail_export';
    protected $primaryKey       = 'sales_order_export_detail_id';
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


    public function getListExportByItems($condition, $addCondition, $limit = 10, $offset = 0)
    {

        $availableSort = [
            'sales_order_export.sales_order_export_id'      => 'sales_order_export_id',
            'sales_order_export.user_id'                    => 'sales_order_export.user_id',
            'sales_order_export.sales_order_export_no'      => 'sales_order_export.sales_order_export_no',
            'sales_contract.customer_id'                    => 'sales_contract.customer_id',
            'sales_order_export.container'                  => 'sales_order_export.container',
            'sales_order_export.actualy_shipment_date'      => 'sales_order_export.actualy_shipment_date',
            'sales_contract.dicharge_port'                  => 'sales_contract.dicharge_port',
            'sales_contract_detail.barang_master_sales_id'  => 'sales_contract_detail.barang_master_sales_id',
            'sales_order_export.valas_id'                   => 'sales_order_export.valas_id',
            'sales_contract.tipe_harga'                     => 'sales_contract.tipe_harga',
            'sales_order_export.deadline'                   => 'sales_order_export.deadline',
            'sales_order_export.company_id'                 => 'sales_order_export.company_id',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.sales_order_export_id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            sales_contract_detail.*, 
            sales_order_export.sales_order_export_no,
            sales_order_export.container,
            sales_order_export.actualy_shipment_date,
            sales_order_export.deadline,
            sales_contract.dicharge_port,                        
            sales_contract.tipe_harga,
            sales_order_detail_export.sales_order_export_id,
            companies.company,
            metadata.value AS valas_name,
            users.name AS acc_holder,
            customers.name AS customer_name,
            barang_master_sales.barang_name,
            satuans.kode_satuan,
            SUM(sales_order_detail_export.qty) AS total_qty,
            SUM(sales_order_detail_export.qty_convertion) AS total_qty_convertion,
            SUM(sales_order_detail_export.total_harga_barang) AS total_harga_barang
        ";

        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract_detail', 'sales_contract_detail.id = sales_order_detail_export.sales_contract_detail_id', 'inner')
            ->join('sales_contract', 'sales_contract.id = sales_contract_detail.sales_contract_id', 'left')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_contract_detail.barang_master_sales_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
            ->join('metadata', 'metadata.id = sales_order_export.valas_id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->join('companies', 'companies.id = sales_order_export.company_id', 'left')
            ->join('satuans', 'satuans.id = sales_order_detail_export.satuan_id', 'left')
            ->groupBy('sales_order_detail_export.sales_order_export_detail_id')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $salesDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $salesDataQry
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
                ->orLike('sales_order_export.container', $addCondition['search'])
                ->orLike('sales_order_export.deadline', $addCondition['search'])
                ->orLike('barang_master_sales.barang_name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $salesDataQry->groupEnd();
        }

        if ($addCondition['customer_id']) {
            $salesDataQry->groupStart();
            $salesDataQry->where('sales_contract.customer_id', $addCondition['customer_id']);
            $salesDataQry->groupEnd();
        }

        if ($addCondition['barang_master_sales_id']) {
            $salesDataQry->groupStart();
            $salesDataQry->where('sales_contract_detail.barang_master_sales_id', $addCondition['barang_master_sales_id']);
            $salesDataQry->groupEnd();
        }

        if ($addCondition['user_id']) {
            $salesDataQry->groupStart();
            $salesDataQry->where('sales_order_export.user_id', $addCondition['user_id']);
            $salesDataQry->groupEnd();
        }

        if ($addCondition['company_id']) {
            $salesDataQry->groupStart();
            $salesDataQry->where('sales_order_export.company_id', $addCondition['company_id']);
            $salesDataQry->groupEnd();
        }

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $salesDataQry->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $salesDataQry->where('actualy_shipment_date >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $salesDataQry->where('actualy_shipment_date <=', $addCondition['dateEnd']);
            }
            $salesDataQry->groupEnd();
        }


        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll($limit, $offset);


        // 7. Grand total query (tanpa group dan limit)
        $grandTotalQry = $this->db->table('sales_contract_detail')
            ->select("
            SUM(sales_order_detail_export.qty_convertion) AS grand_total_qty_convertion,
            SUM(sales_order_detail_export.total_harga_barang) AS grand_total_harga_barang
        ")
            ->join('sales_order_detail_export', 'sales_order_detail_export.sales_contract_detail_id = sales_contract_detail.id', 'inner')
            ->join('sales_contract', 'sales_contract.id = sales_contract_detail.sales_contract_id', 'left')
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->join('companies', 'companies.id = sales_order_export.company_id', 'left')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_contract_detail.barang_master_sales_id', 'left')
            ->where($condition);

        if (!empty($addCondition['search'])) {
            $grandTotalQry->groupStart()
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
                ->orLike('sales_order_export.container', $addCondition['search'])
                ->orLike('barang_master_sales.barang_name', $addCondition['search'])
                ->groupEnd();
        }

        if (!empty($addCondition['customer_id'])) {
            $grandTotalQry->where('sales_contract.customer_id', $addCondition['customer_id']);
        }

        if (!empty($addCondition['barang_master_sales_id'])) {
            $grandTotalQry->where('sales_contract_detail.barang_master_sales_id', $addCondition['barang_master_sales_id']);
        }

        if (!empty($addCondition['dateStart'])) {
            $grandTotalQry->where('actualy_shipment_date >=', $addCondition['dateStart']);
        }

        if (!empty($addCondition['dateEnd'])) {
            $grandTotalQry->where('actualy_shipment_date <=', $addCondition['dateEnd']);
        }

        if (!empty($addCondition['user_id'])) {
            $grandTotalQry->where('sales_order_export.user_id', $addCondition['user_id']);
        }

        if (!empty($addCondition['company_id'])) {
            $grandTotalQry->where('sales_order_export.company_id', $addCondition['company_id']);
        }

        $grandTotal = $grandTotalQry->get()->getRowArray();

        // 8. Return final data
        return [
            'data'               => $data,
            'totalData'          => $totalData,
            'totalFilteredData'  => $totalFilteredData,
            'totalQtyConvertion' => $grandTotal['grand_total_qty_convertion'] ?? 0,
            'amountValue'        => $grandTotal['grand_total_harga_barang'] ?? 0,
            'sort'               => $sort,
            'sortType'           => $sortType,
        ];
    }

    public function getListExportByAccountHolder($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_order_export.user_id' => 'sales_order_export.user_id',
            'total_qty_convertion' => 'total_qty_convertion',
            'total_harga_barang' => 'total_harga_barang'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'sales_order_export.user_id'] ?? 'sales_order_export.user_id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
        sales_order_export.user_id,
        users.name AS acc_holder,
        SUM(sales_order_detail_export.qty_convertion) AS total_qty_convertion,
        SUM(sales_order_detail_export.total_harga_barang) AS total_harga_barang
    ";

        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->where($condition)
            ->groupBy('sales_order_export.user_id');

        if (!empty($addCondition['search'])) {
            $salesDataQry->groupStart();
            $salesDataQry->like('users.name', $addCondition['search']);
            $salesDataQry->groupEnd();
        }

        if (!empty($addCondition['year'])) {
            $salesDataQry->groupStart();
            $salesDataQry->where('YEAR(actualy_shipment_date)', $addCondition['year']);
            $salesDataQry->groupEnd();
        }

        // Hitung total sebelum orderBy agar tidak error
        $totalData = $salesDataQry->countAllResults(false);
        $totalFilteredData = $salesDataQry->countAllResults(false);

        // Tambahkan urutan sort setelah count
        $salesDataQry->orderBy($sort, $sortType);

        $data = $salesDataQry->findAll($limit, $offset);

        // GRAND TOTAL
        $grandTotalQry = $this->db->table('sales_order_detail_export')
            ->select("
            SUM(sales_order_detail_export.qty_convertion) AS grand_total_qty_convertion,
            SUM(sales_order_detail_export.total_harga_barang) AS grand_total_harga_barang
        ")
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->groupBy('sales_order_export.user_id')
            ->where($condition);

        if (!empty($addCondition['search'])) {
            $grandTotalQry->groupStart();
            $grandTotalQry->like('users.name', $addCondition['search']);
            $grandTotalQry->groupEnd();
        }

        if (!empty($addCondition['year'])) {
            $grandTotalQry->groupStart();
            $grandTotalQry->where('YEAR(actualy_shipment_date)', $addCondition['year']);
            $grandTotalQry->groupEnd();
        }

        $grandTotal = $grandTotalQry->get()->getRowArray();

        return [
            'data'               => $data,
            'totalData'          => $totalData,
            'totalFilteredData'  => $totalFilteredData,
            'sort'               => $sort,
            'sortType'           => $sortType,
            'totalQtyConvertion' => $grandTotal['grand_total_qty_convertion'] ?? 0,
            'amountValue'        => $grandTotal['grand_total_harga_barang'] ?? 0,
        ];
    }



    public function getSalesOrderExportDetailBySalesOrderExportId($id)
    {
        $arrCondition = [
            'sales_order_detail_export.deletedAt' => null,
            'sales_order_export_id' => $id
        ];

        $builder = $this->db->table('sales_order_detail_export')
            ->select('sales_order_detail_export.*, barang_master.barang_name as nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('barang_master', 'barang_master.id = sales_order_detail_export.barang_id', 'left')
            ->join('satuans', 'satuans.id = sales_order_detail_export.satuan_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}
