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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_order_export_no' => 'sales_order_export.sales_order_export_no',
            'customer_name'         => 'customers.name',
            'due_date'              => 'sales_contract.due_date',
            'shipment_date'         => 'sales_contract.shipment_date',
            'tanggal'               => 'sales_order_export.tanggal',
            'divisi_id'             => 'sales_order_export.divisi_id',
            'po_no'                 => 'sales_order_export.po_no',
            'deadline'              => 'sales_order_export.deadline',
            "consigne"              => 'sales_order_export.consigne'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'sales_order_export.tanggal'] ?? 'sales_order_export.tanggal';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, 
                        sales_contract.customer_po_no,
                        sales_contract.dicharge_port,
                        sales_contract.shipment_date,
                        customers.name AS customer_name,
                        companies.company,
                        divisis.divisi";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_export.divisi_id', 'left')
            ->join('companies', 'companies.id = sales_order_export.user_id', 'left')
            ->orderBy($sort, $sortType)
            ->orderBy('sales_order_export.updatedAt', 'desc');

        $totalData = $salesDataQry->countAllResults(false);


        if ($addCondition['search']) {
            $salesDataQry
                ->groupStart()
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('sales_contract.customer_po_no', $addCondition['search'])
                ->orLike('sales_order_export.consigne', $addCondition['search'])
                ->orLike('sales_order_export.destination', $addCondition['search'])
                ->orLike('sales_order_export.deadline', $addCondition['search'])
                ->groupEnd();
        }

        if ($addCondition['status']) {
            if ($addCondition['status'] == "SUDAH POSTING") {
                $salesDataQry
                    ->where('sales_order_export.status', 'POSTED');
            } else if ($addCondition['status'] == "BELUM POSTING") {
                $salesDataQry
                    ->where('sales_order_export.status', 'NEW');
            }
        }

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $salesDataQry->groupStart(); //
            if (!empty($addCondition['dateStart'])) {
                $salesDataQry->where('DATE(sales_order_export.tanggal) >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $salesDataQry->where('DATE(sales_order_export.tanggal) <=', $addCondition['dateEnd']);
            }
            $salesDataQry->groupEnd();
        }


        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll($limit, $offset);
        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getListExportByCustomer($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_order_export.sales_order_export_id'  => 'sales_order_export_id',
            'sales_order_export.user_id'                => 'sales_order_export.user_id',
            'sales_order_export.sales_order_export_no'  => 'sales_order_export.sales_order_export_no',
            'sales_contract.customer_id'                => 'sales_contract.customer_id',
            'sales_order_export.container'              => 'sales_order_export.container',
            'sales_order_export.actualy_shipment_date'  => 'sales_order_export.actualy_shipment_date',
            'sales_order_export.shipment_value'         => 'sales_order_export.shipment_value',
            'sales_order_export.shipment_value_net'     => 'sales_order_export.shipment_value_net',
            'sales_order_export.deadline'               => 'sales_order_export.deadline',
            'sales_order_export.company_id'             => 'sales_order_export.company_id',
            'sales_contract.tipe_harga'                 => 'sales_contract.tipe_harga'

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.sales_order_export_id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, 
                        companies.company,
                        sales_contract.tipe_harga,
                        metadata.value AS valas_name,
                        users.name AS acc_holder,
                        customers.name AS customer_name";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('companies', 'sales_order_export.company_id = companies.id', 'left')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'sales_order_export.valas_id = metadata.id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->orderBy($sort, $sortType)
            ->groupBy('sales_order_export.sales_order_export_id');

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $salesDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $salesDataQry
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
                ->orLike('sales_order_export.deadline', $addCondition['search'])
                ->orLike('sales_order_export.container', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $salesDataQry->groupEnd();
        }

        if ($addCondition['customer_id']) {
            $salesDataQry->groupStart();
            $salesDataQry->where('sales_contract.customer_id', $addCondition['customer_id']);
            $salesDataQry->groupEnd();
        }

        if ($addCondition['company_id']) {
            $salesDataQry->groupStart();
            $salesDataQry->where('sales_order_export.company_id', $addCondition['company_id']);
            $salesDataQry->groupEnd();
        }

        if ($addCondition['user_id']) {
            $salesDataQry->groupStart();
            $salesDataQry->where('sales_order_export.user_id', $addCondition['user_id']);
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


        // Hitung shipment_value dan shipment_value_net tanpa join ke detail
        $shipmentSum = $this->db->table('sales_order_export')
            ->select('
            SUM(sales_order_export.shipment_value) AS total_shipment_value_all,
            SUM(sales_order_export.shipment_value_net) AS total_shipment_value_net_all
        ')
            ->join('companies', 'sales_order_export.company_id = companies.id', 'left')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'sales_order_export.valas_id = metadata.id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->where($condition);

        if ($addCondition['customer_id']) {
            $shipmentSum->where('sales_contract.customer_id', $addCondition['customer_id']);
        }
        if ($addCondition['search']) {
            $shipmentSum->groupStart()
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
                ->orLike('sales_order_export.deadline', $addCondition['search'])
                ->orLike('sales_order_export.container', $addCondition['search'])
                ->groupEnd();
        }

        if ($addCondition['company_id']) {
            $shipmentSum->where('sales_order_export.company_id', $addCondition['company_id']);
        }

        if ($addCondition['user_id']) {
            $shipmentSum->where('sales_order_export.user_id', $addCondition['user_id']);
        }
        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $shipmentSum->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $shipmentSum->where('actualy_shipment_date >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $shipmentSum->where('actualy_shipment_date <=', $addCondition['dateEnd']);
            }
            $shipmentSum->groupEnd();
        }

        $shipmentSummary = $shipmentSum->get()->getRowArray();

        // Hitung total_qty_convertion (boleh join detail)
        $qtySum = $this->db->table('sales_order_detail_export')
            ->select('SUM(sales_order_detail_export.qty_convertion) AS total_qty_convertion_all')
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
            ->join('companies', 'sales_order_export.company_id = companies.id', 'left')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'sales_order_export.valas_id = metadata.id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->where($condition)
            ->where('sales_order_detail_export.deletedAt', null);

        if ($addCondition['customer_id']) {
            $qtySum->where('sales_contract.customer_id', $addCondition['customer_id']);
        }
        if ($addCondition['search']) {
            $qtySum->groupStart()
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
                ->orLike('sales_order_export.deadline', $addCondition['search'])
                ->orLike('sales_order_export.container', $addCondition['search'])
                ->groupEnd();
        }

        if ($addCondition['company_id']) {
            $qtySum->where('sales_order_export.company_id', $addCondition['company_id']);
        }

        if ($addCondition['user_id']) {
            $qtySum->where('sales_order_export.user_id', $addCondition['user_id']);
        }
        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $qtySum->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $qtySum->where('actualy_shipment_date >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $qtySum->where('actualy_shipment_date <=', $addCondition['dateEnd']);
            }
            $qtySum->groupEnd();
        }

        $qtySummary = $qtySum->get()->getRowArray();

        // Gabungkan hasil
        $summary = array_merge($shipmentSummary, $qtySummary);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType,
            'totalQtyConvertion' => (float) ($qtySummary['total_qty_convertion_all'] ?? 0),
            'totalShipmentValue' => (float) ($summary['total_shipment_value_all'] ?? 0),
            'totalShipmentValueNet' => (float) ($summary['total_shipment_value_net_all'] ?? 0),
        ];
    }

    public function generateTotalPrice($salesOrderExportId)
    {
        $salesOrderExportAdditionalModel = new SalesOrderExportAdditionalModel();

        $dataSO = $this->getById($salesOrderExportId);
        $dataSODetail =  $this
            ->getDetailSalesKontrakInOrderForm(
                $dataSO->sales_contract_id,
                $salesOrderExportId,
                true
            );

        $dataSalesExportAdditional = $salesOrderExportAdditionalModel
            ->where('sales_order_export_id', $salesOrderExportId)
            ->findAll();

        $totalAdjusment = 0;
        $shipmentValue = 0; // Include Rebate dll
        $shipmentValueNet = 0; // Original
        foreach ($dataSODetail['salesContractDetailList'] as $detail) {
            $shipmentValueNet += formatter($detail["total_input"], "STR_TO_FLOAT");
        }

        // DARI SALES KONTRAK YANG DIINPUT
        if ($dataSODetail['royaltyPriceFinal'] > 0) {
            $totalAdjusment -= $dataSODetail['royaltyPriceFinal'];
        }
        if ($dataSODetail['rebatePriceFinal'] > 0) {
            $totalAdjusment -= $dataSODetail['rebatePriceFinal'];
        }
        if ($dataSODetail['canDeductionPriceFinal'] > 0) {
            $totalAdjusment -= $dataSODetail['canDeductionPriceFinal'];
        }
        if ($dataSODetail['estimatedFreightPriceFinal'] > 0) {
            $totalAdjusment += $dataSODetail['estimatedFreightPriceFinal'];
        }
        if ($dataSODetail['othersPriceFinal'] > 0) {
            $others_value = (float) $dataSODetail['othersPriceFinal'];
            if ($dataSODetail['othersTypeFinal'] == "PLUS") {
                $totalAdjusment += $others_value;
            } else {
                $totalAdjusment -= $others_value;
            }
        }
        // DARI SALES ORDER
        foreach ($dataSalesExportAdditional as $d) {
            if ($d['additional_detail_type'] == "PLUS") {
                $totalAdjusment += $d['additional_detail_price'];
            } else {
                $totalAdjusment -= $d['additional_detail_price'];
            }
        }
        if ($dataSO->palet_fumigation > 0) {
            $totalAdjusment += $dataSO->palet_fumigation_price;
        }

        $shipmentValue = $shipmentValueNet + $totalAdjusment;

        return [
            'shipment_value' => $shipmentValue,
            'shipment_value_net' => $shipmentValueNet,
            'valas_id' => $dataSODetail['salesContract']['currency']
        ];
    }

    public function getById($id)
    {
        $selectQry = "
            sales_order_export.*, 
            customers.name as customer_name,
            customers.address,
            sales_contract.sales_contract_no,
            sales_contract.customer_po_no,
            sales_contract.total_container,
            sales_contract.dicharge_port,
            sales_contract.shipment_date,
            sales_contract.tipe_harga,
            sales_contract.loading_port,
            sales_contract.royalty,
            sales_contract.rebate,
            sales_contract.can_deduction,
            sales_contract.estimated_freight,
            sales_contract.others,
            metadata.value as mata_uang,
            companies.holding_company,
            companies.company
        ";

        $salesData = $this->asObject()
            ->select($selectQry)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('companies', 'companies.id = sales_order_export.company_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
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

    public function getDetailSalesKontrakInOrderForm($salesContractId, $salesOrderExportId = null, $isPrint = false)
    {
        $salesKontrakDetailModel = new SalesKontrakDetailModel();
        $salesContractSizeBreakdownModel = new SalesContractSizeBreakdownModel();
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $salesKontrakModel = new SalesKontrakModel();
        $salesOrderExportModel = new SalesOrderExportModel();
        $salesOrderExportSubtitleModel = new SalesOrderExportSubtitleModel();

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

            $qtyInput = 0;
            $qtySisa = 0;
            $totalInput = 0;

            foreach ($salesContractSize as $s) {

                $result = $salesOrderExportDetailModel
                    ->select('SUM(qty) as total_qty')
                    ->where('sales_contract_size_breakdown_id', $s['id'])
                    ->where('deletedAt', null)
                    ->first(); // Gunakan first() karena SUM akan kembalikan satu baris saja

                $totalQtySalesOrder = $result['total_qty'] ?? 0;
                $totalQtySisa = $s['qty'] - $totalQtySalesOrder;

                if ($totalQtySisa > 0 && $salesOrderExportId == null) {
                    // MASIH ADA SISA BRO 
                    // PAS CREATE
                    array_push($sizeBreakdown, [
                        'id_detail_breakdown' => $s['id'],
                        'size' => $s['size'],
                        'grade' => $s['grade'],
                        'packing' => $s['packing'],
                        'can' => $s['can'],
                        'case' => $s['cased'],
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
                        'cup' => $s['cup'],
                        'qty' => $s['qty'],
                        'harga' => $s['harga'],
                        'total' => $s['total'],
                        //----------------------------
                        'qty_convertion' => $s['kode_satuan'] == "KG" && $s['qty'] != 0  ? $s['qty'] : 0, // Jika Kg Otomatis Ambil Aja
                        'satuan_convertion_id' =>  $s['kode_satuan'] == "KG" && $s['qty'] != 0  ?  $s['satuan_size_id'] : "29", // KG
                        'satuan_convertion_kode' => $s['kode_satuan'] == "KG" && $s['qty'] != 0 ?  $s['kode_satuan'] : "KG",
                        //------------------------------
                        'note_size' => "",
                        'note_grade' => "",
                        'note_packing' => "",
                        'note_can' => "",
                        'note_case' => "",
                        'note_kg' => "",
                        'note_lb' => "",
                        'note_inner_box' => "",
                        'note_pc' => "",
                        'note_bag' => "",
                        'note_persen' => "",
                        'note_cup' => "",
                        'note_palet' => "",
                        //-----------------------------
                        'qty_sisa' => $totalQtySisa,
                        'qty_input' => $totalQtySisa,
                        'total_sisa' => $s['harga'] * $totalQtySisa,
                        'total_input' => $s['harga'] * $totalQtySisa
                    ]);

                    $qtyInput += $totalQtySisa;
                    $qtySisa += $totalQtySisa;
                    $totalInput += $s['harga'] * $totalQtySisa;
                } else if ($salesOrderExportId != null) {

                    $salesOrderDetailExport = $salesOrderExportDetailModel
                        ->select('sales_order_detail_export.*, satuans.kode_satuan as kode_satuan_konversi')
                        ->join('satuans', 'satuans.id = sales_order_detail_export.satuan_convertion_id', 'left')
                        ->where('sales_order_export_id', $salesOrderExportId)
                        ->where('sales_contract_size_breakdown_id', $s['id'])
                        ->first();

                    if ($salesOrderDetailExport != null && $isPrint == false) {

                        // EDIT NING FORM
                        array_push($sizeBreakdown, [
                            'id_detail_breakdown' => $s['id'],
                            'size' => $salesOrderDetailExport['size'],
                            'grade' => $salesOrderDetailExport['grade'],
                            'packing' => $salesOrderDetailExport['packing'],
                            'can' => $salesOrderDetailExport['can'],
                            'case' => $salesOrderDetailExport['case'],
                            'cased' => $salesOrderDetailExport['case'],
                            'kg' => $salesOrderDetailExport['kg'],
                            'lb' => $salesOrderDetailExport['lb'],
                            'inner_box' => $salesOrderDetailExport['inner_box'],
                            'pc' => $salesOrderDetailExport['pc'],
                            'bag' => $salesOrderDetailExport['bag'],
                            'persen' => $salesOrderDetailExport['persen'],
                            'remark' => $s['remark'],
                            'satuan_size_id' => $s['satuan_size_id'],
                            'satuan_size_code' => $s['kode_satuan'],
                            'palet' => $salesOrderDetailExport['palet'],
                            'cup' => $salesOrderDetailExport['cup'],
                            'qty' => $s['qty'],
                            'harga' => $s['harga'],
                            'total' => $s['total'],
                            //-------------------------------------------------
                            'qty_convertion' => $salesOrderDetailExport['qty_convertion'],
                            'satuan_convertion_id' => $salesOrderDetailExport['satuan_convertion_id'],
                            'satuan_convertion_kode' => $salesOrderDetailExport['kode_satuan_konversi'],
                            //---------------------------------------------------
                            'note_size' => $salesOrderDetailExport['note_size'],
                            'note_grade' => $salesOrderDetailExport['note_grade'],
                            'note_packing' => $salesOrderDetailExport['note_packing'],
                            'note_can' =>  $salesOrderDetailExport['note_can'],
                            'note_case' => $salesOrderDetailExport['note_case'],
                            'note_kg' => $salesOrderDetailExport['note_kg'],
                            'note_lb' => $salesOrderDetailExport['note_lb'],
                            'note_inner_box' => $salesOrderDetailExport['note_inner_box'],
                            'note_pc' => $salesOrderDetailExport['note_pc'],
                            'note_bag' => $salesOrderDetailExport['note_bag'],
                            'note_persen' => $salesOrderDetailExport['note_persen'],
                            'note_cup' => $salesOrderDetailExport['note_cup'],
                            'note_palet' => $salesOrderDetailExport['note_palet'],
                            //-------------------------------
                            'qty_sisa' => $salesOrderDetailExport['qty'] + $totalQtySisa,
                            'qty_input' => $salesOrderDetailExport['qty'],
                            'total_sisa' => $s['harga'] * ($salesOrderDetailExport['qty'] + $totalQtySisa),
                            'total_input' => \floatval($s['harga'] * $salesOrderDetailExport['qty'])

                        ]);


                        $qtyInput +=  $salesOrderDetailExport['qty'];
                        $qtySisa += $salesOrderDetailExport['qty'] + $totalQtySisa;
                        $totalInput += floatval($s['harga'] * $salesOrderDetailExport['qty']);
                    } else {
                        if ($salesOrderDetailExport != null) {
                            if ($salesOrderDetailExport['qty'] != 0 && $isPrint == true) {
                                // INI PAS PRINT
                                array_push($sizeBreakdown, [
                                    'id_detail_breakdown' => $s['id'],
                                    'size' => $salesOrderDetailExport['size'],
                                    'grade' => $salesOrderDetailExport['grade'],
                                    'packing' => $salesOrderDetailExport['packing'],
                                    'can' => $salesOrderDetailExport['can'],
                                    'case' => $salesOrderDetailExport['case'],
                                    'cased' =>  $salesOrderDetailExport['case'],
                                    'kg' => $salesOrderDetailExport['kg'],
                                    'lb' => $salesOrderDetailExport['lb'],
                                    'inner_box' => $salesOrderDetailExport['inner_box'],
                                    'pc' => $salesOrderDetailExport['pc'],
                                    'bag' => $salesOrderDetailExport['bag'],
                                    'persen' => $salesOrderDetailExport['persen'],
                                    'remark' => $s['remark'],
                                    'satuan_size_id' => $s['satuan_size_id'],
                                    'satuan_size_code' => $s['kode_satuan'],
                                    'palet' => $salesOrderDetailExport['palet'],
                                    'cup' => $salesOrderDetailExport['cup'],
                                    'qty' => $s['qty'],
                                    'harga' => $s['harga'],
                                    'total' => $s['total'],
                                    //-------------------------------------------------
                                    'qty_convertion' => $salesOrderDetailExport['qty_convertion'],
                                    'satuan_convertion_id' => $salesOrderDetailExport['satuan_convertion_id'],
                                    'satuan_convertion_kode' => $salesOrderDetailExport['kode_satuan_konversi'],
                                    //---------------------------------------------------
                                    'note_size' => $salesOrderDetailExport['note_size'],
                                    'note_grade' => $salesOrderDetailExport['note_grade'],
                                    'note_packing' => $salesOrderDetailExport['note_packing'],
                                    'note_can' =>  $salesOrderDetailExport['note_can'],
                                    'note_case' => $salesOrderDetailExport['note_case'],
                                    'note_kg' => $salesOrderDetailExport['note_kg'],
                                    'note_lb' => $salesOrderDetailExport['note_lb'],
                                    'note_inner_box' => $salesOrderDetailExport['note_inner_box'],
                                    'note_pc' => $salesOrderDetailExport['note_pc'],
                                    'note_bag' => $salesOrderDetailExport['note_bag'],
                                    'note_persen' => $salesOrderDetailExport['note_persen'],
                                    'note_cup' => $salesOrderDetailExport['note_cup'],
                                    'note_palet' => $salesOrderDetailExport['note_palet'],
                                    //-------------------------------
                                    'qty_sisa' => $salesOrderDetailExport['qty'],
                                    'qty_input' => \floatval($salesOrderDetailExport['qty']),
                                    'total_sisa' => $s['harga'] * ($salesOrderDetailExport['qty']),
                                    'total_input' => \floatval($s['harga'] * $salesOrderDetailExport['qty'])

                                ]);

                                $qtyInput +=  $salesOrderDetailExport['qty'];
                                $qtySisa += $salesOrderDetailExport['qty'];
                                $totalInput += floatval($s['harga'] *  $salesOrderDetailExport['qty']);
                            }
                        } else {
                            // MASIH ADA SISA BRO 
                            array_push($sizeBreakdown, [
                                'id_detail_breakdown' => $s['id'],
                                'size' => $s['size'],
                                'grade' => $s['grade'],
                                'packing' => $s['packing'],
                                'can' => $s['can'],
                                'case' => $s['cased'],
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
                                'cup' => $s['cup'],
                                'qty' => $s['qty'],
                                'harga' => $s['harga'],
                                'total' => $s['total'],
                                //----------------------------
                                'qty_convertion' => ($s['kode_satuan'] == "KG" && $totalQtySisa != 0) ? $totalQtySisa : 0, // Jika Kg Otomatis Ambil Aja
                                'satuan_convertion_id' => ($s['kode_satuan'] == "KG" && $totalQtySisa != 0) ?  $s['satuan_size_id'] : "29",
                                'satuan_convertion_kode' => ($s['kode_satuan'] == "KG" && $totalQtySisa != 0) ?  $s['kode_satuan'] : "KG",
                                //------------------------------
                                'note_size' => "",
                                'note_grade' => "",
                                'note_packing' => "",
                                'note_can' => "",
                                'note_case' => "",
                                'note_kg' => "",
                                'note_lb' => "",
                                'note_inner_box' => "",
                                'note_pc' => "",
                                'note_bag' => "",
                                'note_persen' => "",
                                'note_cup' => "",
                                'note_palet' => "",
                                //-----------------------------
                                'qty_sisa' => $totalQtySisa,
                                'qty_input' => $totalQtySisa,
                                'total_sisa' => $s['harga'] * $totalQtySisa,
                                'total_input' => $s['harga'] * $totalQtySisa
                            ]);

                            $qtyInput += $totalQtySisa;
                            $qtySisa += $totalQtySisa;
                            $totalInput += $s['harga'] * $totalQtySisa;
                        }
                    }
                }
            }

            $subTitle = null;
            // PAS EDIT
            if ($salesOrderExportId != null) {
                $subTitle = $salesOrderExportSubtitleModel
                    ->select('sales_order_export_subtitle.*,divisis.divisi')
                    ->join('divisis', 'divisis.id = sales_order_export_subtitle.divisi_id', 'left')
                    ->where('sales_order_export_id', $salesOrderExportId)
                    ->where('sales_contract_detail_id', $sd['id'])
                    ->first();
            }

            array_push($salesContractDetailList, [
                'id' => $sd['id'],
                'barang_id' => $sd['barang_master_sales_id'],
                'kode_barang' => $sd['kode_barang'],
                'barang_name' => $sd['barang_name'],
                'brand' => $subTitle == null ? $sd['brand'] : $subTitle['brand'],
                'specs' => $subTitle == null ? $sd['specs'] : $subTitle['specs'],
                'species' => $subTitle == null ? $sd['species'] : $subTitle['species'],
                'packing' =>  $subTitle == null ?  $sd['kemasan'] : $subTitle['packing'],
                'divisi_id' => $subTitle == null ? null : $subTitle['divisi_id'],
                'divisi_name' => $subTitle == null ? "" : $subTitle['divisi'] ?? '',
                'qty' => $sd['qty'],
                'harga' => $sd['harga'],
                'total_harga' => $sd['total_harga'],
                //--------------------------
                'qty_sisa' => $qtySisa,
                'qty_input' => $qtyInput,
                'total_input' => \floatval($totalInput),
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
            SUM(royalty_price) AS royalty_price,
            SUM(rebate_price) AS rebate_price,
            SUM(can_deduction_price) AS can_deduction_price,
            SUM(estimated_freight_price) AS estimated_freight_price,
            SUM(others_price) AS others_price,
            others_type
        ";

        $resultAdditionalSalesOrder = $salesOrderExportModel
            ->select($selectQryAdditionalSalesOrder)
            ->where('sales_contract_id', $salesContractId)
            ->where('deletedAt', null)
            ->first();

        $royaltyPriceFinal = $royaltyPrice - ($resultAdditionalSalesOrder['royalty_price'] ?? 0);
        $rebatePriceFinal = $rebatePrice - ($resultAdditionalSalesOrder['rebate_price'] ?? 0);
        $canDeductionPriceFinal = $canDeductionPrice - ($resultAdditionalSalesOrder['can_deduction_price'] ?? 0);
        $estimatedFreightPriceFinal = $estimatedFreightPrice - ($resultAdditionalSalesOrder['estimated_freight_price'] ?? 0);
        $othersPriceFinal = $othersPrice - ($resultAdditionalSalesOrder['others_price'] ?? 0);
        $othersTypeFinal = $othersType;

        $royaltyPriceMax = $royaltyPriceFinal;
        $rebatepriceMax = $rebatePriceFinal;
        $canDeductionPriceMax = $canDeductionPriceFinal;
        $estimatedFreightPriceMax = $estimatedFreightPriceFinal;
        $othersPriceMax = $othersTypeFinal;


        if ($salesOrderExportId != null) {
            // PAS UPDATE PAKAI DEFAULT
            $salesOrderExport = $salesOrderExportModel
                ->where('sales_order_export_id', $salesOrderExportId)
                ->first();

            $royaltyPriceMax  = $royaltyPriceMax + $salesOrderExport['royalty_price'];
            $rebatepriceMax = $rebatePriceFinal + $salesOrderExport['rebate_price'];
            $canDeductionPriceMax = $canDeductionPriceFinal + $salesOrderExport['can_deduction_price'];
            $estimatedFreightPriceMax = $estimatedFreightPriceFinal + $salesOrderExport['estimated_freight_price'];
            $othersPriceMax = $othersPriceFinal +  $salesOrderExport['others_price'];

            $royaltyPriceFinal = $salesOrderExport['royalty_price'];
            $rebatePriceFinal = $salesOrderExport['rebate_price'];
            $canDeductionPriceFinal = $salesOrderExport['can_deduction_price'];
            $estimatedFreightPriceFinal = $salesOrderExport['estimated_freight_price'];
            $othersPriceFinal = $salesOrderExport['others_price'];
            $othersTypeFinal = $salesOrderExport['others_type'];
        }

        // Repair
        $salesContractDetailList = array_values(array_filter($salesContractDetailList, function ($item) {
            if (empty($item['size_breakdown'])) {
                // Drop jika size_breakdown kosong
                return false;
            }

            // Cek jika semua qty_input dalam size_breakdown adalah 0
            $allQtyInputZero = array_reduce($item['size_breakdown'], function ($carry, $sb) {
                return $carry && ($sb['qty_input'] == 0);
            }, true);

            if ($allQtyInputZero) {
                // Hide size_breakdown jika semua qty_input 0
                unset($item['size_breakdown']);
            }

            return true;
        }));


        $finalResultList = [
            // Max
            'royaltyPriceMax' => \floatval($royaltyPriceMax),
            'rebatepriceMax' => \floatval($rebatepriceMax),
            'canDeductionPriceMax' => \floatval($canDeductionPriceMax),
            'estimatedFreightPriceMax' => \floatval($estimatedFreightPriceMax),
            'othersPriceMax' => \floatval($othersPriceMax),
            // Final Dibayar
            'royaltyPriceFinal' => \floatval($royaltyPriceFinal),
            'rebatePriceFinal' => \floatval($rebatePriceFinal),
            'canDeductionPriceFinal' => \floatval($canDeductionPriceFinal),
            'estimatedFreightPriceFinal' => \floatval($estimatedFreightPriceFinal),
            'othersPriceFinal' => \floatval($othersPriceFinal),
            'othersTypeFinal' => $othersTypeFinal,
            'salesContractDetailList' => $salesContractDetailList,
            'salesContract' => $salesKontrak
        ];

        return $finalResultList;
    }


    public function getDetailSalesKontrakInOrderFormForStuffing($salesContractId, $salesOrderExportId = null, $isPrint = false)
    {
        $salesKontrakDetailModel = new SalesKontrakDetailModel();
        $salesContractSizeBreakdownModel = new SalesContractSizeBreakdownModel();
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $salesKontrakModel = new SalesKontrakModel();
        $salesOrderExportModel = new SalesOrderExportModel();
        $salesOrderExportSubtitleModel = new SalesOrderExportSubtitleModel();

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

            $qtyInput = 0;
            $qtySisa = 0;
            $totalInput = 0;

            foreach ($salesContractSize as $s) {

                $result = $salesOrderExportDetailModel
                    ->select('SUM(qty) as total_qty')
                    ->where('sales_contract_size_breakdown_id', $s['id'])
                    ->where('deletedAt', null)
                    ->first(); // Gunakan first() karena SUM akan kembalikan satu baris saja

                $totalQtySalesOrder = $result['total_qty'] ?? 0;
                $totalQtySisa = $s['qty'] - $totalQtySalesOrder;

                if ($totalQtySisa > 0 && $salesOrderExportId == null) {
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
                        //----------------------------
                        'qty_convertion' => $s['kode_satuan'] == "KG" && $s['qty'] != 0  ? $s['qty'] : 0, // Jika Kg Otomatis Ambil Aja
                        'satuan_convertion_id' =>  $s['kode_satuan'] == "KG" && $s['qty'] != 0  ?  $s['satuan_size_id'] : null,
                        'satuan_convertion_kode' => $s['kode_satuan'] == "KG" && $s['qty'] != 0 ?  $s['kode_satuan'] : "",
                        //------------------------------
                        'note_size' => "",
                        'note_grade' => "",
                        'note_packing' => "",
                        'note_can' => "",
                        'note_case' => "",
                        'note_kg' => "",
                        'note_lb' => "",
                        'note_inner_box' => "",
                        'note_pc' => "",
                        'note_bag' => "",
                        'note_persen' => "",
                        'note_cup' => "",
                        'note_palet' => "",
                        //-----------------------------
                        'qty_sisa' => $totalQtySisa,
                        'qty_input' => $totalQtySisa,
                        'total_sisa' => $s['harga'] * $totalQtySisa,
                        'total_input' => $s['harga'] * $totalQtySisa
                    ]);

                    $qtyInput += $totalQtySisa;
                    $qtySisa += $totalQtySisa;
                    $totalInput += $s['harga'] * $totalQtySisa;
                } else if ($salesOrderExportId != null) {

                    $salesOrderDetailExport = $salesOrderExportDetailModel
                        ->select('sales_order_detail_export.*, satuans.kode_satuan as kode_satuan_konversi')
                        ->join('satuans', 'satuans.id = sales_order_detail_export.satuan_convertion_id', 'left')
                        ->where('sales_order_export_id', $salesOrderExportId)
                        ->where('sales_contract_size_breakdown_id', $s['id'])
                        ->first();

                    if ($salesOrderDetailExport != null && $isPrint == false) {

                        // EDIT NING FORM
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
                            //-------------------------------------------------
                            'qty_convertion' => $salesOrderDetailExport['qty_convertion'],
                            'satuan_convertion_id' => $salesOrderDetailExport['satuan_convertion_id'],
                            'satuan_convertion_kode' => $salesOrderDetailExport['kode_satuan_konversi'],
                            //---------------------------------------------------
                            'note_size' => $salesOrderDetailExport['note_size'],
                            'note_grade' => $salesOrderDetailExport['note_grade'],
                            'note_packing' => $salesOrderDetailExport['note_packing'],
                            'note_can' =>  $salesOrderDetailExport['note_can'],
                            'note_case' => $salesOrderDetailExport['note_case'],
                            'note_kg' => $salesOrderDetailExport['note_kg'],
                            'note_lb' => $salesOrderDetailExport['note_lb'],
                            'note_inner_box' => $salesOrderDetailExport['note_inner_box'],
                            'note_pc' => $salesOrderDetailExport['note_pc'],
                            'note_bag' => $salesOrderDetailExport['note_bag'],
                            'note_persen' => $salesOrderDetailExport['note_persen'],
                            'note_cup' => $salesOrderDetailExport['note_cup'],
                            'note_palet' => $salesOrderDetailExport['note_palet'],
                            //-------------------------------
                            'qty_sisa' => $salesOrderDetailExport['qty'] + $totalQtySisa,
                            'qty_input' => $salesOrderDetailExport['qty'],
                            'total_sisa' => $s['harga'] * ($salesOrderDetailExport['qty'] + $totalQtySisa),
                            'total_input' => \floatval($s['harga'] * $salesOrderDetailExport['qty'])

                        ]);


                        $qtyInput +=  $salesOrderDetailExport['qty'];
                        $qtySisa += $salesOrderDetailExport['qty'] + $totalQtySisa;
                        $totalInput += floatval($s['harga'] * $salesOrderDetailExport['qty']);
                    } else {
                        if ($salesOrderDetailExport != null) {
                            if ($salesOrderDetailExport['qty'] != 0 && $isPrint == true) {
                                // INI PAS PRINT
                                array_push($sizeBreakdown, [
                                    'id_detail_breakdown' => $s['id'],
                                    'size' => $salesOrderDetailExport['size'],
                                    'grade' => $salesOrderDetailExport['grade'],
                                    'packing' => $salesOrderDetailExport['packing'],
                                    'can' => $salesOrderDetailExport['can'],
                                    'cased' => $salesOrderDetailExport['cased'],
                                    'kg' => $salesOrderDetailExport['kg'],
                                    'lb' => $salesOrderDetailExport['lb'],
                                    'inner_box' => $salesOrderDetailExport['inner_box'],
                                    'pc' => $salesOrderDetailExport['pc'],
                                    'bag' => $salesOrderDetailExport['bag'],
                                    'persen' => $salesOrderDetailExport['persen'],
                                    'remark' => $s['remark'],
                                    'satuan_size_id' => $s['satuan_size_id'],
                                    'satuan_size_code' => $s['kode_satuan'],
                                    'palet' => $salesOrderDetailExport['palet'],
                                    'qty' => $s['qty'],
                                    'harga' => $s['harga'],
                                    'total' => $s['total'],
                                    //-------------------------------------------------
                                    'qty_convertion' => $salesOrderDetailExport['qty_convertion'],
                                    'satuan_convertion_id' => $salesOrderDetailExport['satuan_convertion_id'],
                                    'satuan_convertion_kode' => $salesOrderDetailExport['kode_satuan_konversi'],
                                    //---------------------------------------------------
                                    'note_size' => $salesOrderDetailExport['note_size'],
                                    'note_grade' => $salesOrderDetailExport['note_grade'],
                                    'note_packing' => $salesOrderDetailExport['note_packing'],
                                    'note_can' =>  $salesOrderDetailExport['note_can'],
                                    'note_case' => $salesOrderDetailExport['note_case'],
                                    'note_kg' => $salesOrderDetailExport['note_kg'],
                                    'note_lb' => $salesOrderDetailExport['note_lb'],
                                    'note_inner_box' => $salesOrderDetailExport['note_inner_box'],
                                    'note_pc' => $salesOrderDetailExport['note_pc'],
                                    'note_bag' => $salesOrderDetailExport['note_bag'],
                                    'note_persen' => $salesOrderDetailExport['note_persen'],
                                    'note_cup' => $salesOrderDetailExport['note_cup'],
                                    'note_palet' => $salesOrderDetailExport['note_palet'],
                                    //-------------------------------
                                    'qty_sisa' => $salesOrderDetailExport['qty'],
                                    'qty_input' => \floatval($salesOrderDetailExport['qty']),
                                    'total_sisa' => $s['harga'] * ($salesOrderDetailExport['qty']),
                                    'total_input' => \floatval($s['harga'] * $salesOrderDetailExport['qty'])

                                ]);

                                $qtyInput +=  $salesOrderDetailExport['qty'];
                                $qtySisa += $salesOrderDetailExport['qty'];
                                $totalInput += floatval($s['harga'] *  $salesOrderDetailExport['qty']);
                            }
                        } else {
                            // MASIH ADA SISA BRO 
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
                                //----------------------------
                                'qty_convertion' => ($s['kode_satuan'] == "KG" && $totalQtySisa != 0) ? $totalQtySisa : 0, // Jika Kg Otomatis Ambil Aja
                                'satuan_convertion_id' => ($s['kode_satuan'] == "KG" && $totalQtySisa != 0) ?  $s['satuan_size_id'] : null,
                                'satuan_convertion_kode' => ($s['kode_satuan'] == "KG" && $totalQtySisa != 0) ?  $s['kode_satuan'] : "",
                                //------------------------------
                                'note_size' => "",
                                'note_grade' => "",
                                'note_packing' => "",
                                'note_can' => "",
                                'note_case' => "",
                                'note_kg' => "",
                                'note_lb' => "",
                                'note_inner_box' => "",
                                'note_pc' => "",
                                'note_bag' => "",
                                'note_persen' => "",
                                'note_cup' => "",
                                'note_palet' => "",
                                //-----------------------------
                                'qty_sisa' => $totalQtySisa,
                                'qty_input' => $totalQtySisa,
                                'total_sisa' => $s['harga'] * $totalQtySisa,
                                'total_input' => $s['harga'] * $totalQtySisa
                            ]);

                            $qtyInput += $totalQtySisa;
                            $qtySisa += $totalQtySisa;
                            $totalInput += $s['harga'] * $totalQtySisa;
                        }
                    }
                }
            }

            $subTitle = null;
            // PAS EDIT
            if ($salesOrderExportId != null) {
                $subTitle = $salesOrderExportSubtitleModel
                    ->select('sales_order_export_subtitle.*,divisis.divisi')
                    ->join('divisis', 'divisis.id = sales_order_export_subtitle.divisi_id', 'left')
                    ->where('sales_order_export_id', $salesOrderExportId)
                    ->where('sales_contract_detail_id', $sd['id'])
                    ->first();
            }

            array_push($salesContractDetailList, [
                'id' => $sd['id'],
                'barang_id' => $sd['barang_master_sales_id'],
                'kode_barang' => $sd['kode_barang'],
                'barang_name' => $sd['barang_name'],
                'brand' => $subTitle == null ? $sd['brand'] : $subTitle['brand'],
                'specs' => $subTitle == null ? $sd['specs'] : $subTitle['specs'],
                'species' => $subTitle == null ? $sd['species'] : $subTitle['species'],
                'packing' =>  $subTitle == null ?  $sd['kemasan'] : $subTitle['packing'],
                'divisi_id' => $subTitle == null ? null : $subTitle['divisi_id'],
                'divisi_name' => $subTitle == null ? "" : $subTitle['divisi'],
                'qty' => $sd['qty'],
                'harga' => $sd['harga'],
                'total_harga' => $sd['total_harga'],
                //--------------------------
                'qty_sisa' => $qtySisa,
                'qty_input' => $qtyInput,
                'total_input' => \floatval($totalInput),
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
            SUM(royalty_price) AS royalty_price,
            SUM(rebate_price) AS rebate_price,
            SUM(can_deduction_price) AS can_deduction_price,
            SUM(estimated_freight_price) AS estimated_freight_price,
            SUM(others_price) AS others_price,
            others_type
        ";

        $resultAdditionalSalesOrder = $salesOrderExportModel
            ->select($selectQryAdditionalSalesOrder)
            ->where('sales_contract_id', $salesContractId)
            ->where('deletedAt', null)
            ->first();

        $royaltyPriceFinal = $royaltyPrice - ($resultAdditionalSalesOrder['royalty_price'] ?? 0);
        $rebatePriceFinal = $rebatePrice - ($resultAdditionalSalesOrder['rebate_price'] ?? 0);
        $canDeductionPriceFinal = $canDeductionPrice - ($resultAdditionalSalesOrder['can_deduction_price'] ?? 0);
        $estimatedFreightPriceFinal = $estimatedFreightPrice - ($resultAdditionalSalesOrder['estimated_freight_price'] ?? 0);
        $othersPriceFinal = $othersPrice - ($resultAdditionalSalesOrder['others_price'] ?? 0);
        $othersTypeFinal = $othersType;

        $royaltyPriceMax = $royaltyPriceFinal;
        $rebatepriceMax = $rebatePriceFinal;
        $canDeductionPriceMax = $canDeductionPriceFinal;
        $estimatedFreightPriceMax = $estimatedFreightPriceFinal;
        $othersPriceMax = $othersTypeFinal;


        if ($salesOrderExportId != null) {
            // PAS UPDATE PAKAI DEFAULT
            $salesOrderExport = $salesOrderExportModel
                ->where('sales_order_export_id', $salesOrderExportId)
                ->first();

            $royaltyPriceMax  = $royaltyPriceMax + $salesOrderExport['royalty_price'];
            $rebatepriceMax = $rebatePriceFinal + $salesOrderExport['rebate_price'];
            $canDeductionPriceMax = $canDeductionPriceFinal + $salesOrderExport['can_deduction_price'];
            $estimatedFreightPriceMax = $estimatedFreightPriceFinal + $salesOrderExport['estimated_freight_price'];
            $othersPriceMax = $othersPriceFinal +  $salesOrderExport['others_price'];

            $royaltyPriceFinal = $salesOrderExport['royalty_price'];
            $rebatePriceFinal = $salesOrderExport['rebate_price'];
            $canDeductionPriceFinal = $salesOrderExport['can_deduction_price'];
            $estimatedFreightPriceFinal = $salesOrderExport['estimated_freight_price'];
            $othersPriceFinal = $salesOrderExport['others_price'];
            $othersTypeFinal = $salesOrderExport['others_type'];
        }

        // Repair
        $salesContractDetailList = array_values(array_filter($salesContractDetailList, function ($item) {

            // Filter size_breakdown yg qty_input > 0
            $item['size_breakdown'] = array_values(array_filter($item['size_breakdown'], function ($sb) {
                return floatval($sb['qty_input']) > 0;
            }));

            // Kalau setelah difilter size_breakdown kosong → jangan ikut
            if (empty($item['size_breakdown'])) {
                return false;
            }

            return true;
        }));



        $finalResultList = [
            // Max
            'royaltyPriceMax' => \floatval($royaltyPriceMax),
            'rebatepriceMax' => \floatval($rebatepriceMax),
            'canDeductionPriceMax' => \floatval($canDeductionPriceMax),
            'estimatedFreightPriceMax' => \floatval($estimatedFreightPriceMax),
            'othersPriceMax' => \floatval($othersPriceMax),
            // Final Dibayar
            'royaltyPriceFinal' => \floatval($royaltyPriceFinal),
            'rebatePriceFinal' => \floatval($rebatePriceFinal),
            'canDeductionPriceFinal' => \floatval($canDeductionPriceFinal),
            'estimatedFreightPriceFinal' => \floatval($estimatedFreightPriceFinal),
            'othersPriceFinal' => \floatval($othersPriceFinal),
            'othersTypeFinal' => $othersTypeFinal,
            'salesContractDetailList' => $salesContractDetailList,
            'salesContract' => $salesKontrak
        ];

        return $finalResultList;
    }

    public function getSalesOrderSpecs($salesOrderExportId)
    {

        $salesOrderExportSpecsModel = new SalesOrderExportSpecsModel();
        $salesOrderExportSpecsDetailModel = new SalesOrderExportSpecsDetailModel();

        $salesOrderExportSpecs = $salesOrderExportSpecsModel
            ->where('sales_order_export_id', $salesOrderExportId)
            ->findAll();

        $result = array();

        foreach ($salesOrderExportSpecs as $s) {

            $gradeSpecs = array();
            $salesOrderExportSpecsDetail = $salesOrderExportSpecsDetailModel
                ->where('sales_order_export_specs_id', $s['id'])
                ->findAll();

            foreach ($salesOrderExportSpecsDetail as $so) {
                array_push($gradeSpecs, [
                    'id_grade_specs' => $so['id'],
                    'grade' => $so['grade'],
                    'specification' => $so['specification']
                ]);
            }

            array_push($result, [
                'id_detail_specs_list' => $s['id'],
                'size_packing' => $s['size_packing'],
                'grade_specs' => $gradeSpecs,
            ]);
        }

        return $result;
    }

    public function getAccHolder()
    {
        $usersModel = new UserModel();
        $userId = [];

        $salesOrderExport = $this->asArray()
            ->where('deletedAt', null)
            ->findAll();

        foreach ($salesOrderExport as $s) {
            array_push($userId, $s['user_id']);
        }

        $user = $usersModel->whereIn('id', $userId)->orderBy('name', "asc")->findAll();
        return $user;
    }

    public function getSalesOrderExportByCustomerId($customerId)
    {
        $resultQry = $this->asArray()
            ->select('sales_order_export.*')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->where('sales_contract.customer_id', $customerId)
            ->where('sales_order_export.deletedAt', null)
            ->orderBy('sales_order_export.sales_order_export_id', "desc")
            ->findAll();

        return $resultQry;
    }

    public function getListPeb($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_order_export.sales_order_export_id' => 'sales_order_export.sales_order_export_id',
            'sales_order_export.no_invoice'         => 'sales_order_export.no_invoice',
            'sales_order_export.tanggal_invoice'         => 'sales_order_export.tanggal_invoice',
            'sales_order_export.sales_order_export_no' => 'sales_order_export.sales_order_export_no',
            'sales_contract.customer_id'         => 'sales_contract.customer_id',
            'sales_contract.dicharge_port'               => 'sales_contract.dicharge_port',
            'sales_order_export.shipment_value' => 'sales_order_export.shipment_value',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, 
                        sales_contract.dicharge_port,
                        customers.name AS customer_name,
                        valas_peb.value as valas_peb_name";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata as valas_peb', 'valas_peb.id = sales_order_export.valas_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $salesDataQry
                ->groupStart()
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('sales_contract.dicharge_port', $addCondition['search'])
                ->orLike('sales_order_export.no_invoice', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->groupEnd();
        }

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $salesDataQry->groupStart(); //
            if (!empty($addCondition['dateStart'])) {
                $salesDataQry->where('DATE(sales_order_export.tanggal_invoice) >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $salesDataQry->where('DATE(sales_order_export.tanggal_invoice) <=', $addCondition['dateEnd']);
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

    public function generateCodePI($companyId)
    {
        $metaDataModel = new MetadataModel();
        $salesOrderExportModel = new SalesOrderExportModel();

        $year = date('y');
        if ($companyId == 1) {
            $codeInv = "TSI2";
        } elseif ($companyId == 2) {
            $codeInv = "TSI";
        } elseif ($companyId == 15) {
            $codeInv = "GPS";
        } else {
            $codeInv = "OCS";
        }

        // Template Invoice
        $invTempleate = $codeInv . "/" . $year;

        // Ambil Format Code dari Metadata
        $formatCodeFirst = $metaDataModel
            ->where('name', "inv_pi_peb")
            ->first();

        $formatCode = explode(',', $formatCodeFirst['value']); // misal: [C,H,N,M,E,I,J,U,L,O]
        $base = count($formatCode);

        // Cari invoice terakhir
        $salesOrderExport = $salesOrderExportModel
            ->where('company_id', $companyId)
            ->where('deletedAt', null)
            ->orderBy('sales_order_export_id', "desc")
            ->first();

        // Hitung index berikutnya
        $lastCode = null;
        if ($salesOrderExport && $salesOrderExport['no_invoice']) {
            // Ambil kode setelah template, misal "TSI/2025/CN" → ambil "CN"
            $parts = explode('/', $salesOrderExport['no_invoice']);
            $lastCode = end($parts);
        }

        $nextCode = $this->getNextCode($lastCode, $formatCode);

        return $invTempleate . "/" . $nextCode;
    }

    public function getNextCode($lastCode,  $formatCode)
    {
        $base = count($formatCode);

        // kalau belum ada kode → pakai pertama
        if (!$lastCode) {
            return $formatCode[0];
        }

        // mapping huruf ke index
        $map = array_flip($formatCode);

        // ubah kode huruf ke angka (basis-N, Excel style)
        $index = 0;
        $len = strlen($lastCode);
        for ($i = 0; $i < $len; $i++) {
            $char = $lastCode[$i];
            if (!isset($map[$char])) {
                throw new \Exception("Invalid code: " . $lastCode);
            }
            $index = $index * $base + ($map[$char] + 1); // +1 supaya mirip Excel
        }

        // increment
        $index++;

        // convert balik ke huruf
        $result = '';
        while ($index > 0) {
            $index--; // offset Excel style
            $rem = $index % $base;
            $result = $formatCode[$rem] . $result;
            $index = intdiv($index, $base);
        }

        return $result;
    }

    public function getAllSalesOrderInvoiceExportForPembayaran($company_id)
    {
        $selectQry = "sales_order_export.no_invoice as no_faktur, 
                        sales_order_export.shipment_value as total_invoice,
                        sales_order_export.tanggal as tanggal_faktur,
                        companies.company,
                        metadata.value AS valas_name,
                        users.name AS acc_holder,
                        customers.name AS customer_name";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->join('companies', 'sales_order_export.company_id = companies.id', 'left')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'sales_order_export.valas_id = metadata.id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->where('sales_order_export.status', 'POSTED')
            ->where('sales_order_export.deletedAt', null)
            ->where('sales_order_export.company_id', $company_id)
            ->groupBy('sales_order_export.sales_order_export_id');

        $totalData = $salesDataQry->countAllResults(false);

        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getAllSalesOrderInvoiceReport($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_order_export_no' => 'sales_order_export.sales_order_export_no',
            'customer_name'         => 'customers.name',
            'due_date'              => 'sales_contract.due_date',
            'shipment_date'         => 'sales_contract.shipment_date',
            'tanggal'               => 'sales_order_export.tanggal',
            'divisi_id'             => 'sales_order_export.divisi_id',
            'po_no'                 => 'sales_order_export.po_no',
            'deadline'              => 'sales_order_export.deadline',
            "consigne"              => 'sales_order_export.consigne'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'sales_order_export.tanggal'] ?? 'sales_order_export.tanggal';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, 
                        sales_contract.customer_po_no,
                        sales_contract.dicharge_port,
                        sales_contract.shipment_date,
                        customers.name AS customer_name,
                        companies.company,
                        divisis.divisi,
                        valas.value as valas_name";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_export.divisi_id', 'left')
            ->join('companies', 'companies.id = sales_order_export.user_id', 'left')
            ->join('metadata as valas', 'valas.id = sales_order_export.valas_id', 'left')
            ->orderBy($sort, $sortType)
            ->orderBy('sales_order_export.updatedAt', 'desc');

        $totalData = $salesDataQry->countAllResults(false);


        if ($addCondition['search']) {
            $salesDataQry
                ->groupStart()
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('sales_contract.customer_po_no', $addCondition['search'])
                ->orLike('sales_order_export.consigne', $addCondition['search'])
                ->orLike('sales_order_export.destination', $addCondition['search'])
                ->orLike('sales_order_export.deadline', $addCondition['search'])
                ->groupEnd();
        }

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $salesDataQry->groupStart(); //
            if (!empty($addCondition['dateStart'])) {
                $salesDataQry->where('DATE(sales_order_export.tanggal) >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $salesDataQry->where('DATE(sales_order_export.tanggal) <=', $addCondition['dateEnd']);
            }
            $salesDataQry->groupEnd();
        }


        $totalFilteredData = $salesDataQry->countAllResults(false);

        if ($limit == null && $offset == null) {
            $data = $salesDataQry->findAll();
        } else {
            $data = $salesDataQry->findAll($limit, $offset);
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getDataInvoiceReportAccounting($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'createdAt'         => 'sales_order_export.createdAt',
            'updatedAt'         => 'sales_order_export.updatedAt'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        if (isset($addCondition['summary']) && $addCondition['summary'] == "summary") {
            $selectQry = "
                GROUP_CONCAT(
                    DISTINCT customers.id
                    ORDER BY customers.id
                    SEPARATOR ', '
                ) AS id,
                customers.name AS customer_name,
                metadata.value AS valas_name,
                kurs.nilai_kurs AS nilai_kurs,

                SUM(sales_order_export.shipment_value_net) AS sum_amount_invoice, 
                SUM(pembayaran_invoice.total_bayar) AS sum_harga_dibayar
            ";
        } else {
            $selectQry = " 
                    sales_order_export.sales_order_export_id AS id, 
                    sales_order_export.tanggal AS tanggal_invoice, 
                    sales_order_export.sales_order_export_no AS no_invoice, 
                    sales_order_export.company_id, 
                    customers.name AS customer_name,
                    metadata.value AS valas_name,
                    kurs.nilai_kurs AS nilai_kurs,
                    sales_order_export.shipment_value_net AS total, 
                    pembayaran_invoice.total_bayar AS remaining";
        }
        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->where('sales_order_export.status', 'POSTED')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id')
            ->join('metadata', 'metadata.id = sales_order_export.valas_id')
            ->join('kurs', 'kurs.metadata_id = metadata.id AND DATE(kurs.start_date) <= DATE(sales_order_export.tanggal_invoice) AND DATE(kurs.end_date) >= DATE(sales_order_export.tanggal_invoice)', 'left')
            ->join('pembayaran_invoice', "FIND_IN_SET(sales_order_export.sales_order_export_id, REPLACE(REPLACE(pembayaran_invoice.invoice_id, '[', ''), ']', '')) AND pembayaran_invoice.type_invoice = 'EKSPOR'", 'left');
        if (isset($addCondition['summary']) && $addCondition['summary'] == "summary") {
            $poDataQry->groupBy('customers.id');
        } else {
            $poDataQry->groupBy('sales_order_export.sales_order_export_id');
        }
        $poDataQry->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if (!empty($addCondition['search']) || !empty($addCondition['dateStart']) || !empty($addCondition['dateEnd']) || !empty($addCondition['filter']) || !empty($addCondition['divisi']) || !empty($addCondition['companyId'])) {
            $poDataQry->groupStart();
        }

        if (!empty($addCondition['companyId']) && $addCondition['companyId'] != []) {
            $poDataQry->whereIn('sales_order_export.company_id', $addCondition['companyId']);
        }

        if (!empty($addCondition['search'])) {
            $poDataQry->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search']);
        }

        if (!empty($addCondition['dateStart'])) {
            $poDataQry->where('sales_order_export.tanggal_invoice >=', $addCondition['dateStart']);
        }

        if (!empty($addCondition['dateEnd'])) {
            $poDataQry->where('sales_order_export.tanggal_invoice <=', $addCondition['dateEnd']);
        }

        if (!empty($addCondition['filter'])) {
            $poDataQry->whereIn('customers.id', $addCondition['filter']);
        }

        if (!empty($addCondition['search']) || !empty($addCondition['dateStart']) || !empty($addCondition['dateEnd']) || !empty($addCondition['filter']) || !empty($addCondition['divisi']) || !empty($addCondition['companyId'])) {
            $poDataQry->groupEnd();
        }

        $totalFilteredData = $poDataQry->countAllResults(false);

        if ($limit != null && $offset != null) {
            $data = $poDataQry->findAll($limit, $offset);
        } else {
            $data = $poDataQry->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }
}
