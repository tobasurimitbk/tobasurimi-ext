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
            'divisi_id'               => 'sales_order_export.divisi_id',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_export.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_export.*, 
                        sales_contract.customer_po_no,
                        sales_contract.dicharge_port,
                        sales_contract.shipment_date,
                        customers.name AS customer_name,
                        divisis.divisi";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_export.divisi_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $salesDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['status']) {
            $salesDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $salesDataQry
                ->like('sales_order_export.sales_order_export_no', $addCondition['search'])
                ->orLike('sales_contract.customer_po_no', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search']);
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
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getById($id)
    {
        $selectQry = "
            sales_order_export.*, 
            customers.name as customer_name,
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
                        'qty_convertion' => 0,
                        'satuan_convertion_id' => null,
                        'satuan_convertion_kode' => "",
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
                            // EDIT NING FORM
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
                                'qty_convertion' => 0,
                                'satuan_convertion_id' => null,
                                'satuan_convertion_kode' => "",
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
}
