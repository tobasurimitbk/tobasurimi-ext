<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderExportModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_order_export';
    protected $primaryKey       = 'sales_order_export_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sales_order_export_id',
        'sales_order_export_no',
        'sales_contract_id',
        'bc_type',
        'company_id',
        'status',
        'keterangan_unpost',
        'jumlah_unpost',
        'used',
        'user_id',
        'documents_required',
        'special_instructions',
        'createdAt',
        'updatedAt',
        'deletedAt'
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $db = \Config\Database::connect();
        $availableSort = [
            'sales_order_export_no' => 'sales_order_export.sales_order_export_no',
            'customer_name'         => 'customers.name',
            'due_date'              => 'sales_contract.due_date',
            'shipment_date'         => 'sales_contract.shipment_date',
            'createdAt'             => 'sales_order_export.createdAt',
            'updatedAt'             => 'sales_order_export.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, sales_contract.*, 
                      customers.name AS customer_name";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['status']) {
            $salesDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $salesDataQry->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('sales_contract.customer_po_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search']);
        }

        if ($addCondition['status']) {
            if ($addCondition['status'] == "SUDAH POSTING") {
                $salesDataQry
                    ->where('sales_order_export.status', 'POSTED');
            } else {
                $salesDataQry
                    ->where('sales_order_export.status', 'NEW');
            }
        }

        if ($addCondition['search'] || $addCondition['status']) {
            $salesDataQry->groupEnd();
        }

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $salesDataQry->groupStart(); //
            if (!empty($addCondition['dateStart'])) {
                $salesDataQry->where('DATE(sales_order_export.createdAt) >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $salesDataQry->where('DATE(sales_order_export.createdAt) <=', $addCondition['dateEnd']);
            }
            $salesDataQry->groupEnd();
        }


        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll($limit, $offset);
        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getById($id)
    {
        $selectQry = "sales_order_export.*, customers.name as customer_name,sales_contract.customer_po_no,sales_contract.*";

        $salesData = $this->asObject()
            ->select($selectQry)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'LEFT')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'LEFT')
            ->where('sales_order_export.deletedAt', NULL)
            ->find($id);

        return $salesData;
    }

    public function getBySalesContractId($id)
    {
        $selectQry = "sales_order_export.*, customers.name as customer_name";

        $builder = $this->select($selectQry)
            ->join('customers', 'customers.id = sales_order_export.customer_id', 'LEFT')
            ->where('sales_contract_id', $id)->where('sales_order_export.deletedAt', NULL);
        $query = $builder->get();

        return $query->getResult();
    }

    public function getAllSalesOrderExportReport($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_order_export_no' => 'sales_order_export.sales_order_export_no',
            'customer_name'         => 'customers.name',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, 
                      customers.name AS customer_name,
                      barang_master.barang_name as nama_barang, barang_master.kode_barang,
                      sales_order_detail_export.*,  satuans.id as id_satuan, satuans.nama_satuan as satuan";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('sales_order_detail_export', 'sales_order_detail_export.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
            ->join('barang_master', 'barang_master.id = sales_order_detail_export.barang_id', 'left')
            ->join('satuans', 'satuans.id = sales_order_detail_export.satuan_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesDataQry->groupStart();
        }



        if ($addCondition['dateStart']) {
            $salesDataQry->where('DATE(sales_order_export.updatedAt) >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $salesDataQry->where('DATE(sales_order_export.updatedAt) <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesDataQry->groupEnd();
        }

        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }


    public function getAllSalesOrderExportReportPDF($condition, $addCondition)
    {
        $availableSort = [
            'sales_order_export_no' => 'sales_order_export.sales_order_export_no',
            'customer_name'         => 'customers.name',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, 
                      customers.name AS customer_name,
                      barang_master.barang_name as nama_barang, barang_master.kode_barang,
                      sales_order_detail_export.*,  satuans.id as id_satuan, satuans.nama_satuan as satuan";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('sales_order_detail_export', 'sales_order_detail_export.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
            ->join('barang_master', 'barang_master.id = sales_order_detail_export.barang_id', 'left')
            ->join('satuans', 'satuans.id = sales_order_detail_export.satuan_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesDataQry->groupStart();
        }



        if ($addCondition['dateStart']) {
            $salesDataQry->where('DATE(sales_order_export.updatedAt) >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $salesDataQry->where('DATE(sales_order_export.updatedAt) <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $salesDataQry->groupEnd();
        }

        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getDetailSalesKontrakInOrderForm($salesContractId, $salesOrderExportId = null)
    {
        $salesKontrakDetailModel = new SalesKontrakDetailModel();
        $salesContractSizeBreakdownModel = new SalesContractSizeBreakdownModel();
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $salesKontrakModel = new SalesKontrakModel();
        $salesOrderExportModel = new SalesOrderExportModel();

        $salesContractDetailList = [];

        $selectQryDetail = "
            sales_contract_detail.*,
            barang_master_sales.kode_barang,
            barang_master_sales.barang_name
        ";

        $salesKontrakDetail = $salesKontrakDetailModel
            ->select($selectQryDetail)
            ->join('barang_master_sales', 'barang_master_sales.id = sales_contract_detail.barang_master_sales_id', 'left')
            ->where('sales_contract_detail.deletedAt', null)
            ->where('sales_contract_id', $salesContractId)
            ->findAll();

        foreach ($salesKontrakDetail as $sd) {

            $sizeBreakdown = [];

            $salesContractSize = $salesContractSizeBreakdownModel
                ->select('sales_contract_size_breakdown.*,satuans.kode_satuan')
                ->join('satuans', 'satuans.id = sales_contract_size_breakdown.satuan_size_id', 'left')
                ->where('sales_contract_detail_id', $sd['id'])
                ->findAll();

            $qtySisa = 0;
            $totalSisa = 0;

            foreach ($salesContractSize as $s) {

                $result = $salesOrderExportDetailModel
                    ->select('SUM(qty) as total_qty')
                    ->where('sales_contract_size_breakdown_id', $s['id'])
                    ->where('deletedAt', null)
                    ->first(); // Gunakan first() karena SUM akan kembalikan satu baris saja

                $totalQtySalesOrder = $result['total_qty'] ?? 0;
                $totalQtySisa = $s['qty'] - $totalQtySalesOrder;

                if ($totalQtySisa < $s['total'] && $salesOrderExportId == null) {
                    // MASIH ADA SISA BRO 
                    // PAS CREATE
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
                        'remark' => $s['remark'],
                        'satuan_size_id' => $s['satuan_size_id'],
                        'satuan_size_code' => $s['kode_satuan'],
                        'palet' => $s['palet'],
                        'qty' => $s['qty'],
                        'harga' => $s['harga'],
                        'total' => $s['total'],
                        //-------------------------------
                        'qty_sisa' => $totalQtySisa,
                        'total_sisa' => $s['harga'] * $totalQtySisa
                    ]);

                    $qtySisa += $totalQtySisa;
                    $totalSisa += $s['harga'] * $totalQtySisa;
                } else {
                    // PAS EDIT
                    $salesOrderDetailExport = $salesOrderExportDetailModel
                        ->where('sales_order_export_id', $salesOrderExportId)
                        ->where('sales_contract_size_breakdown_id', $s['id'])
                        ->first();

                    if ($salesOrderDetailExport != null) {
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
                            'remark' => $s['remark'],
                            'satuan_size_id' => $s['satuan_size_id'],
                            'satuan_size_code' => $s['kode_satuan'],
                            'palet' => $s['palet'],
                            'qty' => $s['qty'],
                            'harga' => $s['harga'],
                            'total' => $s['total'],
                            //-------------------------------
                            'qty_sisa' => $salesOrderDetailExport['qty'],
                            'total_sisa' => $s['harga'] * $salesOrderDetailExport['qty']
                        ]);

                        $qtySisa += $salesOrderDetailExport['qty'];
                        $totalSisa += $s['harga'] * $salesOrderDetailExport['qty'];
                    }
                }
            }

            array_push($salesContractDetailList, [
                'id' => $sd['id'],
                'kode_barang' => $sd['kode_barang'],
                'barang_name' => $sd['barang_name'],
                'brand' => $sd['brand'],
                'specs' => $sd['specs'],
                'species' => $sd['species'],
                'packing' => $sd['kemasan'],
                'qty' => $sd['qty'],
                'harga' => $sd['harga'],
                'total_harga' => $sd['total_harga'],
                //--------------------------
                'qty_sisa' => $qtySisa,
                'total_sisa' => $totalSisa,
                'size_breakdown' => $sizeBreakdown
            ]);
        }

        $salesKontrak = $salesKontrakModel
            ->select('sales_contract.*,customers.name AS customer_name')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->where('sales_contract.id', $salesContractId)
            ->first();

        // HARGA ORI
        $royaltyPrice = $salesKontrak['royalty_price'];
        $rebatePrice = $salesKontrak['rebate_price'];
        $canDeductionPrice  = $salesKontrak['can_deduction_price'];
        $estimatedFreightPrice = $salesKontrak['estimated_freight_price'];
        $othersPrice = $salesKontrak['others_price'];
        $othersType = $salesKontrak['others_type'];

        // SUM AN DATA
        $selectQryAdditionalSalesOrder = "
            SUM(royalty_price),
            SUM(rebate_price),
            SUM(can_deduction_price),
            SUM(estimated_freight_price),
            SUM(others_price),
            others_type
        ";

        $resultAdditionalSalesOrder = $salesOrderExportModel
            ->select($selectQryAdditionalSalesOrder)
            ->where('sales_contract_id', $salesContractId)
            ->where('deletedAt', null)
            ->first();

        $royaltyPriceFinal = $royaltyPrice - ($resultAdditionalSalesOrder['royalt_price'] ?? 0);
        $rebatePriceFinal = $rebatePrice - ($resultAdditionalSalesOrder['rebate_price'] ?? 0);
        $canDeductionPriceFinal = $canDeductionPrice - ($resultAdditionalSalesOrder['can_deduction_price'] ?? 0);
        $estimatedFreightPriceFinal = $estimatedFreightPrice - ($resultAdditionalSalesOrder['estimated_freight_price'] ?? 0);
        $othersPriceFinal = $othersPrice - ($resultAdditionalSalesOrder['others_price'] ?? 0);
        $othersTypeFinal = $othersType;

        if ($salesOrderExportId != null) {
            // PAS UPDATE PAKAI DEFAULT
            $salesOrderExport = $salesOrderExportModel
                ->where('sales_order_export_id', $salesOrderExportId)
                ->first();

            $royaltyPriceFinal = $salesOrderExport['royalt_price'];
            $rebatePriceFinal = $salesOrderExport['rebate_price'];
            $canDeductionPriceFinal = $salesOrderExport['can_deduction_price'];
            $estimatedFreightPriceFinal = $salesOrderExport['estimated_freight_price'];
            $othersPriceFinal = $salesOrderExport['others_price'];
            $othersTypeFinal = $salesOrderExport['others_type'];
        }


        $finalResultList = [
            'royaltyPriceFinal' => $royaltyPriceFinal,
            'rebatePriceFinal' => $rebatePriceFinal,
            'canDeductionPriceFinal' => $canDeductionPriceFinal,
            'estimatedFreightPriceFinal' => $estimatedFreightPriceFinal,
            'othersPriceFinal' => $othersPriceFinal,
            'othersTypeFinal' => $othersTypeFinal,
            'salesContractDetailList' => $salesContractDetailList,
            'salesContract' => $salesKontrak
        ];

        return $finalResultList;
    }
}
