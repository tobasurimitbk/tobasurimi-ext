<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesKontrakDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_contract_detail';
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

    public function getSalesContractDetailBySalesContractId($id)
    {
        $arrCondition = [
            'sales_contract_detail.deletedAt' => null,
            'sales_contract_id' => $id
        ];

        $builder = $this->db->table('sales_contract_detail')
            ->select('sales_contract_detail.*, barang_master.barang_name AS nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('barang_master', 'barang_master.id = sales_contract_detail.barang_id', 'left')
            ->join('satuans', 'satuans.id = sales_contract_detail.unit', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function detail($salesContractID)
    {

        $salesContractSizeBreakdownModel = new SalesContractSizeBreakdownModel();

        $qryResult = $this->asArray()
            ->select('barang_master_sales.kode_barang,barang_master_sales.barang_name,sales_contract_detail.*,satuans.kode_satuan')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_contract_detail.barang_master_sales_id', 'left')
            ->join('satuans', 'satuans.id = sales_contract_detail.satuan_order_id', 'left')
            ->where('sales_contract_id', $salesContractID)->findAll();

        $dataResult = [];

        foreach ($qryResult as $q) {
            $sizeBreakdown = [];

            $salesContractSize = $salesContractSizeBreakdownModel
                ->select('sales_contract_size_breakdown.*,satuans.kode_satuan')
                ->join('satuans', 'satuans.id = sales_contract_size_breakdown.satuan_size_id', 'left')
                ->where('sales_contract_detail_id', $q['id'])
                ->findAll();

            foreach ($salesContractSize as $s) {
                array_push($sizeBreakdown, [
                    'id_detail_breakdown' => $s['id'],
                    'size' => $s['size'],
                    'grade' => $s['grade'],
                    'packing' => $s['packing'],
                    'can' => $s['can'],
                    'cased' => $s['cased'],
                    'kg' => $s['kg'],
                    'lb' => $s['lb'],
                    'inner_box' => $s['inner_box'],
                    'pc' => $s['pc'],
                    'bag' => $s['bag'],
                    'persen' => $s['persen'],
                    'qty' => $s['qty'],
                    'harga' => $s['harga'],
                    'total' => $s['total'],
                    'remark' => $s['remark'],
                    'satuan_size_id' => $s['satuan_size_id'],
                    'satuan_size_code' => $s['kode_satuan'],
                    'palet' => $s['palet']
                ]);
            }

            $dataResult[] = [
                'id_detail' => $q['id'],
                'barang_master_sales_id' => $q['barang_master_sales_id'],
                'kode_barang' => $q['kode_barang'],
                'barang_name' => $q['barang_name'],
                'satuan_order_id' => $q['satuan_order_id'],
                'satuan_order_name' => $q['kode_satuan'],
                'kemasan' => $q['kemasan'],
                'qty' => $q['qty'],
                'harga' => $q['harga'],
                'remark' => $q['remark'],
                'total' => $q['total_harga'],
                'persen' => $q['persen'],
                // print
                'total_harga' => $q['total_harga'],
                'nama_barang' => $q['barang_name'],
                'size' => $q['size'],
                'brand' => $q['brand'],
                'species' => $q['species'],
                'specs' => $q['specs'],
                'size_breakdown' => $sizeBreakdown
            ];
        }

        return $dataResult;
    }

    public function getListReportByContract($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_contract.id'      => 'sales_contract.id',
            'sales_contract.createdBy'                    => 'sales_contract.createdBy',
            'sales_contract.sales_contract_no'      => 'sales_contract.sales_contract_no',
            'sales_contract.customer_id'                    => 'sales_contract.customer_id',
            'sales_contract.no_container'                  => 'sales_contract.no_container',
            'sales_contract.shipment_date'                  => 'sales_contract.shipment_date',
            'sales_contract.dicharge_port'                  => 'sales_contract.dicharge_port',
            'sales_contract.loading_port'  => 'sales_contract.loading_port',
            'sales_contract.company_id'                   => 'sales_contract.company_id',
            'sales_contract_detail.barang_master_sales_id'  => 'sales_contract_detail.barang_master_sales_id',
            'sales_contract_detail.qty'                   => 'sales_contract_detail.qty',
            'sales_contract.currency'                 => 'sales_contract.currency',
            'sales_contract_detail.total_harga'         => 'sales_contract_detail.total_harga',
            'sales_contract.tipe_harga'                 => 'sales_contract.tipe_harga',

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_contract.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            sales_contract_detail.*, 
            sales_contract.sales_contract_no,
            sales_contract.no_container,
            sales_contract.shipment_date,
            sales_contract.loading_port,
            sales_contract.dicharge_port,
            sales_contract.tipe_harga,
            users.name AS acc_holder,
            customers.name AS customer_name,
            companies.company,
            barang_master_sales.barang_name,
            metadata.value AS valas_name
        ";

        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_contract_detail.sales_contract_id', 'left')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_contract_detail.barang_master_sales_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
            ->join('users', 'users.id = sales_contract.createdBy', 'left')
            ->join('companies', 'companies.id = sales_contract.company_id', 'left')
            ->groupBy('sales_contract_detail.barang_master_sales_id')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $salesDataQry
                ->groupStart()
                ->like('sales_contract.sales_contract_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
                ->orLike('sales_contract.no_container', $addCondition['search'])
                ->orLike('sales_contract.shipment_date', $addCondition['search'])
                ->orLike('barang_master_sales.barang_name', $addCondition['search'])
                ->groupEnd();
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
            $salesDataQry->where('sales_contract.createdBy', $addCondition['user_id']);
            $salesDataQry->groupEnd();
        }

        if ($addCondition['company_id']) {
            $salesDataQry->groupStart();
            $salesDataQry->where('sales_contract.company_id', $addCondition['company_id']);
            $salesDataQry->groupEnd();
        }

        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll($limit, $offset);


        // 7. Grand total query (tanpa group dan limit)
        // $grandTotalQry = $this->db->table('sales_contract_detail')
        //     ->select("
        //     SUM(sales_contract_detail.total_harga) AS grand_total_harga_barang,
        // ")
        //     ->join('sales_contract', 'sales_contract.id = sales_contract_detail.sales_contract_id', 'left')
        //     ->join('barang_master_sales', 'barang_master_sales.id = sales_contract_detail.barang_master_sales_id', 'left')
        //     ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
        //     ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
        //     ->join('users', 'users.id = sales_contract.createdBy', 'left')
        //     ->join('companies', 'companies.id = sales_contract.company_id', 'left')
        //     ->where('sales_contract_detail.deletedAt', null)
        //     ->where($condition);

        // if ($addCondition['search']) {
        //     $grandTotalQry
        //         ->groupStart()
        //         ->like('sales_contract.sales_contract_no', $addCondition['search'])
        //         ->orLike('customers.name', $addCondition['search'])
        //         ->orLike('users.name', $addCondition['search'])
        //         ->orLike('sales_contract.no_container', $addCondition['search'])
        //         ->orLike('sales_contract.shipment_date', $addCondition['search'])
        //         ->orLike('barang_master_sales.barang_name', $addCondition['search'])
        //         ->groupEnd();
        // }

        // if ($addCondition['customer_id']) {
        //     $grandTotalQry->groupStart();
        //     $grandTotalQry->where('sales_contract.customer_id', $addCondition['customer_id']);
        //     $grandTotalQry->groupEnd();
        // }

        // if ($addCondition['barang_master_sales_id']) {
        //     $grandTotalQry->groupStart();
        //     $grandTotalQry->where('sales_contract_detail.barang_master_sales_id', $addCondition['barang_master_sales_id']);
        //     $grandTotalQry->groupEnd();
        // }

        // if ($addCondition['user_id']) {
        //     $grandTotalQry->groupStart();
        //     $grandTotalQry->where('sales_contract.createdBy', $addCondition['user_id']);
        //     $grandTotalQry->groupEnd();
        // }

        // if ($addCondition['company_id']) {
        //     $grandTotalQry->groupStart();
        //     $grandTotalQry->where('sales_contract.company_id', $addCondition['company_id']);
        //     $grandTotalQry->groupEnd();
        // }

        // $grandTotal = $grandTotalQry->get()->getRowArray();

        // 8. Return final data
        return [
            'data'               => $data,
            'totalData'          => $totalData,
            'totalFilteredData'  => $totalFilteredData,
            // 'amountValue'        => $grandTotal['grand_total_harga_barang'] ?? 0,
            'sort'               => $sort,
            'sortType'           => $sortType,
        ];
    }
}
