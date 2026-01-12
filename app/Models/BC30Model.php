<?php

namespace App\Models;

use CodeIgniter\Model;

class BC30Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_30';
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

    public function getList(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0

    ) {
        $db = \Config\Database::connect();
        $where = [];
        $whereDate = "";
        $searchOrderForm = "";

        if (!empty($condition['company_id'])) {
            $where[] = "bc_30.company_id ='$condition[company_id]'";
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDate = "AND bc_30.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['status_posting'])) {
            if ($condition['status_posting'] == "ALL") {
                $where[] = "(bc_30.status_posting='1' OR bc_30.status_posting='0')";
            } elseif ($condition['status_posting'] == "SUDAH POSTING") {
                $where[] = "bc_30.status_posting='1'";
            } else {
                $where[] = "bc_30.status_posting='0'";
            }
        }

        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));
            $searchOrderForm = "
              AND (
                    customers.name LIKE '%{$search}%'
                    OR bc_30.no_daftar LIKE '%{$search}%'
                    OR bc_30.no_aju LIKE '%{$search}%'     
                    OR bc_30.multiple_reference_no LIKE '%{$search}%'   
                )
            ";
        }


        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'id',
            'jenis_pengeluaran',
            'reference_penerima',
            'reference_pengeluaran_id',
            'multiple_reference_no',
            'no_aju',
            'tanggal',
            'status_posting',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- BC 30
            SELECT
                bc_30.*,
                customers.name AS reference_penerima
            FROM bc_30
            LEFT JOIN customers ON customers.id = bc_30.reference_penerima_id
            WHERE bc_30.deletedAt IS NULL
            $filterCondition
            $whereDate
            $searchOrderForm
        )
        ";
        // ============================
        // 📊 COUNT + PAGINATION
        // ============================
        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = "
            SELECT * FROM ($baseQuery) AS x
            $orderBy
            LIMIT $limit OFFSET $offset
        ";

        $data = $db->query($mainQuery)->getResultArray();

        // ============================
        // 📦 RETURN RESULT
        // ============================

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getListOutstanding(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {

        $bc30All = $this->where('jenis_pengeluaran', "ORDER FORM EKSPOR")->where('deletedAt', null)->findAll();
        $multipleRefBc30 = array_column($bc30All, 'multiple_reference_id');

        $idUsed = array();
        foreach ($multipleRefBc30 as $m) {
            if (is_array(json_decode($m))) {
                foreach (json_decode($m) as $mx) {
                    array_push($idUsed, $mx);
                }
            }
        }

        $notIn = "";
        if (!empty($idUsed)) {
            $idUsedEscaped = implode(",", array_map('intval', $idUsed));
            $notIn = " AND sales_order_export.sales_order_export_id NOT IN ($idUsedEscaped) ";
        }

        $db = \Config\Database::connect();
        $where = [];
        $whereDateSalesOrder = "";
        $searchSalesOrder = "";

        // DARI BC 30
        if (!empty($condition['company_id'])) {
            $where[] = "sales_order_export.company_id = '$condition[company_id]'";
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDateSalesOrder = "AND sales_order_export.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));
            $searchSalesOrder = "
                AND(
                    barang_master_sales.kode_barang LIKE '%{$search}%'
                    OR barang_master_sales.barang_name LIKE '%{$search}%'
                    OR customers.name LIKE '%{$search}%'
                    OR sales_order_export.sales_order_export_no LIKE '%{$search}%'
                    OR sales_order_export.no_invoice LIKE '%{$search}%'
                )
            ";
        }

        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'sales_order_export_id',
            'tujuan_pengeluaran',
            'tanggal',
            'tanggal_invoice',
            'no_invoice',
            'reference_no',
            'customer_name',
            'kode_barang',
            'barang_name',
            'qty',
            'kode_satuan',
            'valas_name',
            'total_harga_barang'
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
            SELECT
                sales_order_detail_export.sales_order_export_id,
                'ORDER FORM EKSPOR' AS tujuan_pengeluaran,
                sales_order_export.tanggal,
                sales_order_export.tanggal_invoice,
                sales_order_export.no_invoice,
                sales_order_export.sales_order_export_no AS reference_no,
                customers.name AS customer_name,
                barang_master_sales.kode_barang,
                barang_master_sales.barang_name,
                sales_order_detail_export.qty,
                satuans.kode_satuan,
                metadata.value AS valas_name,
                sales_order_detail_export.total_harga_barang
            FROM
                sales_order_detail_export
            LEFT JOIN sales_order_export ON sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id
            LEFT JOIN sales_contract_detail ON sales_contract_detail.id = sales_order_detail_export.sales_contract_detail_id
            LEFT JOIN barang_master_sales ON barang_master_sales.id = sales_contract_detail.barang_master_sales_id
            LEFT JOIN satuans ON satuans.id = sales_order_detail_export.satuan_id 
            LEFT JOIN sales_contract ON sales_contract.id = sales_order_export.sales_contract_id
            LEFT JOIN customers ON customers.id = sales_contract.customer_id
            LEFT JOIN metadata ON metadata.id = sales_contract.currency
            WHERE sales_order_detail_export.deletedAt IS NULL
            AND sales_order_detail_export.qty != 0
            $notIn
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

    public function getReferencePengeluaranOrderForm(
        $customerId,
        $companyId
    ) {
        $salesOrderExportModel = new SalesOrderExportModel();

        $dataBc30All = $this->asArray()->where('company_id', $companyId)->where('jenis_pengeluaran', "ORDER FORM EKSPOR")->where('deletedAt', null)->findAll();
        $multipleReferenceId = array_column($dataBc30All, 'multiple_reference_id');
        $idUsed = [];
        foreach ($multipleReferenceId as $m) {
            if (is_array(json_decode($m))) {
                foreach (json_decode($m) as $id) {
                    array_push($idUsed, $id);
                }
            }
        }

        $selectQry = "
            sales_order_export.sales_order_export_id AS id, 
            sales_order_export.sales_order_export_no AS no_reference,
            sales_order_export.no_invoice
        ";
        $dataQry = $salesOrderExportModel
            ->select($selectQry)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->where('sales_contract.customer_id', $customerId)
            ->where('sales_order_export.company_id', $companyId);
        if (count($idUsed) > 0) {
            $dataQry->whereNotIn('sales_order_export_id', $idUsed);
        }
        $dataResult = $dataQry->where('sales_order_export.deletedAt', null)->findAll();
        return $dataResult;
    }

    public function getReferencePengeluaranOrderFormSelected(
        $bc30Id,
        $companyId
    ) {
        $salesOrderExportModel = new SalesOrderExportModel();

        $dataBc30All = $this->asArray()->where('id', $bc30Id)->findAll();
        $multipleReferenceId = array_column($dataBc30All, 'multiple_reference_id');

        $idUsed = [];
        foreach ($multipleReferenceId as $m) {
            if (is_array(json_decode($m))) {
                foreach (json_decode($m) as $id) {
                    array_push($idUsed, $id);
                }
            }
        }

        $selectQry = "
            sales_order_export.sales_order_export_id AS id, 
            sales_order_export.sales_order_export_no AS no_reference,
            sales_order_export.no_invoice
        ";
        $dataQry = $salesOrderExportModel
            ->select($selectQry)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->where('sales_order_export.company_id', $companyId);
        if (count($idUsed) > 0) {
            $dataQry->whereIn('sales_order_export_id', $idUsed);
        }
        $dataResult = $dataQry->where('sales_order_export.deletedAt', null)->findAll();
        return $dataResult;
    }


    public function getReferencePengeluaranSample(
        $customerId,
        $companyId
    ) {
        $sampleModel = new SampleModel();

        $dataBc30All = $this->asArray()->where('company_id', $companyId)->where('jenis_pengeluaran', "LAINNYA")->where('deletedAt', null)->findAll();
        $multipleReferenceId = array_column($dataBc30All, 'multiple_reference_id');
        $idUsed = [];
        foreach ($multipleReferenceId as $m) {
            if (is_array(json_decode($m))) {
                foreach (json_decode($m) as $id) {
                    array_push($idUsed, $id);
                }
            }
        }

        $selectQry = "
            sample.id, 
            sample.no_sample AS no_reference, 
            sample.no_invoice
        ";

        $dataQry = $sampleModel
            ->select($selectQry)
            ->where('sample.customer_id', $customerId)
            ->where('sample.company_id', $companyId);
        if (count($idUsed) > 0) {
            $dataQry->whereNotIn('id', $idUsed);
        }
        $dataResult = $dataQry->where('sample.deletedAt', null)->findAll();
        return $dataResult;
    }


    public function getReferencePengeluaranSampleSelected(
        $bc30Id,
        $companyId
    ) {
        $sampleModel = new SampleModel();

        $dataBc30All = $this->asArray()->where('id', $bc30Id)->findAll();
        $multipleReferenceId = array_column($dataBc30All, 'multiple_reference_id');

        $idUsed = [];
        foreach ($multipleReferenceId as $m) {
            if (is_array(json_decode($m))) {
                foreach (json_decode($m) as $id) {
                    array_push($idUsed, $id);
                }
            }
        }

        $selectQry = "
            sample.id, 
            sample.no_sample AS no_reference, 
            sample.no_invoice
        ";

        $dataQry = $sampleModel
            ->select($selectQry)
            ->where('sample.company_id', $companyId);
        if (count($idUsed) > 0) {
            $dataQry->whereIn('id', $idUsed);
        }
        $dataResult = $dataQry->where('sample.deletedAt', null)->findAll();
        return $dataResult;
    }

    public function getListBarangSalesOrderEkspor($multipleReferenceIds)
    {
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();

        $selectQry = "
            sales_contract_detail.barang_master_sales_id,
            barang_master_sales.barang_master_id,
            barang_master_sales.barang_name,
            barang_master.barang_name AS barang_name_inventori,
            barang_master.kode_barang,
            SUM(sales_order_detail_export.qty) AS qty,
            satuans.kode_satuan,
            sales_order_export.valas_id,
            metadata.value AS valas_name,
            SUM(sales_order_detail_export.total_harga_barang) AS total_harga_barang
        ";

        $dataResult = $salesOrderExportDetailModel->select($selectQry)
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
            ->join('sales_contract_detail', 'sales_contract_detail.id = sales_order_detail_export.sales_contract_detail_id', 'left')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_contract_detail.barang_master_sales_id', 'left')
            ->join('satuans', 'satuans.id = sales_order_detail_export.satuan_id', 'left')
            ->join('metadata', 'metadata.id = sales_order_export.valas_id', 'left')
            ->join('barang_master', 'barang_master.id = barang_master_sales.barang_master_id', 'left')
            ->where('sales_order_detail_export.deletedAt', null)
            ->whereIn('sales_order_detail_export.sales_order_export_id', $multipleReferenceIds)
            ->groupBy(['barang_name', 'kode_satuan', 'metadata.value'])
            ->findAll();

        return $dataResult;
    }

    public function getListBarangSample($multipleReferenceIds)
    {
        $sampleDetailModel = new SampleDetailModel();

        $selectQry = "
            sample_detail.barang_master_sales_id,
            barang_master_sales.barang_master_id,
            barang_master_sales.barang_name,
            barang_master.barang_name AS barang_name_inventori,
            barang_master.kode_barang,
            SUM(sample_detail.qty) AS qty,
            satuans.kode_satuan,
            '30' AS valas_id,
            '' AS valas_name,
            '' AS total_harga_barang
        ";

        $dataResult = $sampleDetailModel->select($selectQry)
            ->join('barang_master_sales', 'barang_master_sales.id = sample_detail.barang_master_sales_id', 'left')
            ->join('satuans', 'satuans.id = sample_detail.satuan_id', 'left')
            ->join('barang_master', 'barang_master.id = barang_master_sales.barang_master_id', 'left')
            ->where('sample_detail.deletedAt', null)
            ->whereIn('sample_detail.sample_id', $multipleReferenceIds)
            ->groupBy(['barang_name', 'kode_satuan'])
            ->findAll();

        return $dataResult;
    }


    public function getListBarang($referenceId, $typeReference)
    {
        $stuffingLokalDetailModel = new StuffingLokalDetailModel();
        $stuffingInternasionalDetailModel = new StuffingInternasionalDetailModel();
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $metaDataModel = new MetadataModel();
        $salesOrderDetailModel = new SalesOrderDetailModel();
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $satuanModel = new SatuansModel();

        if ($typeReference == "ORDER FORM LOKAL") {
            $selectQry = "
                barang_master_sales.satuan_id AS satuan_sales_id,
                barang_master_sales.kode_barang AS kode_barang_sales,
                barang_master_sales.barang_name AS nama_barang_sales,
                barang_master.kode_barang AS kode_barang_internal,
                barang_master_spesifikasi.satuan_1 AS satuan_internal_id,
                barang_master.id as barang_master_id,
                barang_master.barang_name as nama_barang,
                CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang_internal,
                stuffing_lokal_detail.stock_id_warehouse,
                stuffing_lokal_detail.bc_id_warehouse,
                stuffing_lokal_detail.no_aju_warehouse,
                stuffing_lokal_detail.stock_dokumen,
                stuffing_lokal_detail.barang_id_order,
                stuffing_lokal_detail.qty AS qty_keluar,
                stuffing_lokal.no_stuffing,
                stuffing_lokal.tanggal AS tanggal_keluar,
                divisis.divisi,
                warehouses.warehouse_name";

            $dataResult = $stuffingLokalDetailModel->select($selectQry)
                ->join('barang_master_sales', 'barang_master_sales.id = stuffing_lokal_detail.barang_id_order', 'left')
                ->join('barang_master', 'barang_master.id = stuffing_lokal_detail.barang1_id_warehouse', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stuffing_lokal_detail.barang2_id_warehouse', 'left')
                ->join('stuffing_lokal', 'stuffing_lokal.id = stuffing_lokal_detail.stuffing_lokal_id', 'left')
                ->join('divisis', 'divisis.id = stuffing_lokal_detail.divisi_id', 'left')
                ->join('warehouses', 'warehouses.id = stuffing_lokal_detail.warehouse_id', 'left')
                ->where('stuffing_lokal.sales_order_id', $referenceId)
                ->where('stuffing_lokal_detail.deletedAt', null)
                ->findAll();

            for ($i = 0; $i < count($dataResult); $i++) {
                $stockList = $stockDetail2Model->getStockListDetail(
                    $dataResult[$i]['stock_id_warehouse'],
                    $dataResult[$i]['bc_id_warehouse'],
                    $dataResult[$i]['no_aju_warehouse'],
                    $dataResult[$i]['stock_dokumen']
                );

                $stock = $stockModel->find($dataResult[$i]['stock_id_warehouse']);
                $bcType = isset($stockList['bc_id']) ? $metaDataModel->find($stockList['bc_id']) : null;
                $bcTypeText = $bcType == null ? "NON PABEAN" : $bcType['value'];
                $salesOrderDetail = $salesOrderDetailModel->where('id_barang', $dataResult[$i]['barang_id_order'])->where('id_sales_order', $referenceId)->first();
                $satuanSales = $satuanModel->find($dataResult[$i]['satuan_sales_id']);
                $satuanInternal = $satuanModel->find($dataResult[$i]['satuan_internal_id']);

                // Untuk di Form Ceisa
                $dataResult[$i]['barang1_id'] = $stockList['barang1_id'];
                $dataResult[$i]['kemasan_id'] = $stockList['kemasan_id'];
                $dataResult[$i]['total_harga'] = ($dataResult[$i]['qty_keluar'] * $salesOrderDetail['harga_barang']);
                $dataResult[$i]['barang_master_name'] = $dataResult[$i]['nama_barang'];

                $dataResult[$i]['tanggal_keluar'] = date('d/m/Y', strtotime($dataResult[$i]['tanggal_keluar']));
                $dataResult[$i]['tipe_barang'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                $dataResult[$i]['dokumen_asal'] = ($bcTypeText != "NON PABEAN") ? $bcTypeText . " / " . $stockList['no_aju'] : "NON PABEAN";
                $dataResult[$i]['harga'] =  number_format(($dataResult[$i]['qty_keluar'] * $salesOrderDetail['harga_barang']), 2);
                $dataResult[$i]['harga_number'] = ($dataResult[$i]['qty_keluar'] * $salesOrderDetail['harga_barang']);
                $dataResult[$i]['mata_uang'] = "IDR";
                $dataResult[$i]['kode_satuan_sales'] = $satuanSales == null ? "-" : $satuanSales['kode_satuan'];
                $dataResult[$i]['kode_satuan_internal'] = $satuanInternal == null ? "-" : $satuanInternal['kode_satuan'];
            }
        } elseif ($typeReference == "ORDER FORM EKSPOR") {
            $selectQry = "
                barang_master_sales.satuan_id AS satuan_sales_id,
                barang_master_sales.kode_barang AS kode_barang_sales,
                barang_master_sales.barang_name AS nama_barang_sales,
                barang_master.kode_barang AS kode_barang_internal,
                barang_master.id as barang_master_id,
                barang_master.barang_name as nama_barang,
                barang_master_spesifikasi.satuan_1 AS satuan_internal_id,
                CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang_internal,
                stuffing_internasional_detail.stock_id_warehouse,
                stuffing_internasional_detail.bc_id_warehouse,
                stuffing_internasional_detail.no_aju_warehouse,
                stuffing_internasional_detail.stock_dokumen,
                stuffing_internasional_detail.barang_id_order,
                stuffing_internasional_detail.qty AS qty_keluar,
                stuffing_internasional.no_stuffing,
                stuffing_internasional.tanggal AS tanggal_keluar,
                divisis.divisi,
                warehouses.warehouse_name";

            $dataResult = $stuffingInternasionalDetailModel->select($selectQry)
                ->join('barang_master_sales', 'barang_master_sales.id = stuffing_internasional_detail.barang_id_order', 'left')
                ->join('barang_master', 'barang_master.id = stuffing_internasional_detail.barang1_id_warehouse', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stuffing_internasional_detail.barang2_id_warehouse', 'left')
                ->join('stuffing_internasional', 'stuffing_internasional.id = stuffing_internasional_detail.stuffing_internasional_id', 'left')
                ->join('divisis', 'divisis.id = stuffing_internasional_detail.divisi_id', 'left')
                ->join('warehouses', 'warehouses.id = stuffing_internasional_detail.warehouse_id', 'left')
                ->where('stuffing_internasional.sales_order_export_id', $referenceId)
                ->where('stuffing_internasional_detail.deletedAt', null)
                ->findAll();

            for ($i = 0; $i < count($dataResult); $i++) {
                $stockList = $stockDetail2Model->getStockListDetail(
                    $dataResult[$i]['stock_id_warehouse'],
                    $dataResult[$i]['bc_id_warehouse'],
                    $dataResult[$i]['no_aju_warehouse'],
                    $dataResult[$i]['stock_dokumen']
                );

                $stock = $stockModel->find($dataResult[$i]['stock_id_warehouse']);
                $bcType = isset($stockList['bc_id']) ? $metaDataModel->find($stockList['bc_id']) : null;
                $bcTypeText = $bcType == null ? "NON PABEAN" : $bcType['value'];
                $salesOrderDetail = $salesOrderExportDetailModel
                    ->select('sales_order_detail_export.harga_barang, metadata.value')
                    ->join('sales_contract_detail', 'sales_contract_detail.id = sales_order_detail_export.sales_contract_detail_id', 'left')
                    ->join('sales_contract', 'sales_contract.id = sales_contract_detail.sales_contract_id', 'left')
                    ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
                    ->where('barang_id', $dataResult[$i]['barang_id_order'])
                    ->where('sales_order_export_id', $referenceId)
                    ->first();
                $satuanSales = $satuanModel->find($dataResult[$i]['satuan_sales_id']);
                $satuanInternal = $satuanModel->find($dataResult[$i]['satuan_internal_id']);

                $dataResult[$i]['tanggal_keluar'] = date('d/m/Y', strtotime($dataResult[$i]['tanggal_keluar']));
                $dataResult[$i]['tipe_barang'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                $dataResult[$i]['dokumen_asal'] = ($bcTypeText != "NON PABEAN") ? $bcTypeText . " / " . $stockList['no_aju'] : "NON PABEAN";
                $dataResult[$i]['harga'] = number_format(($dataResult[$i]['qty_keluar'] * $salesOrderDetail['harga_barang']), 2);
                $dataResult[$i]['harga_number'] = ($dataResult[$i]['qty_keluar'] * $salesOrderDetail['harga_barang']);
                $dataResult[$i]['mata_uang'] = $salesOrderDetail['value'];
                $dataResult[$i]['kode_satuan_sales'] = $satuanSales == null ? "-" : $satuanSales['kode_satuan'];
                $dataResult[$i]['kode_satuan_internal'] = $satuanInternal == null ? "-" : $satuanInternal['kode_satuan'];
            }
        } elseif ($typeReference == "ORDER FORM LAIN") {
            $dataResult =  $this->getListBarangOrderFormLain($referenceId);
        } elseif ($typeReference == "RETUR PEMBELIAN") {
            $dataResult = $this->getListBarangReturEkspor($referenceId);
        }

        return $dataResult;
    }

    public function getListBarangReturEkspor($pengembalianBarangId)
    {
        $pengembalianBarangModel = new PengembalianBarangModel();
        $resultRetur = $pengembalianBarangModel->getReturBeaCukaiDetail($pengembalianBarangId);
        $result = array();

        foreach ($resultRetur as $r) {
            array_push($result, [
                'barang_master_id' => $r['barang1_id'],
                'nama_barang' => $r['barang_master_name'],
                'tipe_barang' =>  $r['tipe_barang'],
                'dokumen_asal' => $r['bc_type'],
                'no_aju_warehouse' => $r['no_aju'],
                'kode_barang_internal' => $r['kode_barang'],
                'nama_barang_internal' => $r['barang'],
                'divisi' => $r['divisi'],
                'warehouse_name' => $r['warehouse_name'],
                'qty_keluar' => $r['qty_konversi'],
                'kode_satuan_internal' => $r['satuan'],
                'mata_uang' => $r['valas_name'],
                'harga' => number_format($r['total_harga'], 2),
                'harga_number' => $r['total_harga']
            ]);
        }

        return $result;
    }

    public function getListBarangOrderFormLain($salesOrderLainId)
    {
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $resultSalesOrderDetail = $salesOrderLainDetailModel->detail($salesOrderLainId);
        $result = array();
        foreach ($resultSalesOrderDetail as $r) {
            array_push($result, [
                'barang_master_id' => $r['barang1_id'] == 0 ? $r['kemasan_id'] : $r['barang1_id'],
                'nama_barang' => $r['barang_master_name'],
                'tipe_barang' =>  $r['type_barang_text'],
                'dokumen_asal' => $r['bc_type'],
                'no_aju_warehouse' => $r['no_aju'],
                'kode_barang_internal' => $r['kode_barang'],
                'nama_barang_internal' => $r['barang'],
                'divisi' => $r['divisi'],
                'warehouse_name' => $r['warehouse_name'],
                'qty_keluar' => $r['qty_konversi'],
                'kode_satuan_internal' => $r['satuan_order_text'],
                'mata_uang' => $r['valas_name'],
                'harga' => number_format($r['total_harga'], 2),
                'harga_number' => $r['total_harga']
            ]);
        }
        return $result;
    }

    public function getListSalesOrder($companyId)
    {
        $salesOrderExportModel = new SalesOrderExportModel();
        // INTERNASIONAL
        $selectQry = "
            sales_order_export.sales_order_export_id AS sales_order_id,
            sales_order_export.sales_order_export_no AS no_sales_order,
            customers.name AS nama_customer,
            customers.address AS alamat_customer,
            country.country_name
        ";

        $salesOrder = $salesOrderExportModel
            ->select($selectQry)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->where('used', "USED")
            ->where('sales_contract.company_id', $companyId)
            ->where('sales_contract.deletedAt', null)
            ->findAll();
        $result = array();
        foreach ($salesOrder as $i => $s) {
            $bc23 = $this->where('sales_order_id', $s['sales_order_id'])->where('tipe_sales_order', "ORDER FORM EKSPOR")->first();
            if ($bc23 == null) {
                $salesOrder[$i]['alamat_customer'] =  $salesOrder[$i]['alamat_customer'] == "" ? "-" :  $salesOrder[$i]['alamat_customer'];
                $salesOrder[$i]['country_name'] =  $salesOrder[$i]['country_name'] == "" ? "-" :  $salesOrder[$i]['alamat_customer'];
                array_push($result, $salesOrder[$i]);
            }
        }

        return $result;
    }

    public function getListReturPembelian($companyId, $pengembalianBarangId = null)
    {
        // KHUSUS LPB IMPORT
        $pengembalianBarangModel = new PengembalianBarangModel();
        $selectQry = "
            pengembalian_barang.id as sales_order_id,
            pengembalian_barang.no_surat_jalan as no_sales_order,
            suppliers.name as nama_customer,
            suppliers.address as alamat_customer,
        ";

        $salesOrder = $pengembalianBarangModel
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->where('pengembalian_barang.bc_pengeluaran_id', null)
            ->where('pengembalian_barang.deletedAt', null)
            ->where('pengembalian_barang.company_id', $companyId)
            ->where('penerimaan_barang.status_penerimaan', "IMPORT")
            ->findAll();

        $result = array();
        foreach ($salesOrder as $i => $s) {
            $bc30 = $this->where('pengembalian_barang_id', $s['sales_order_id'])->first();

            $salesOrder[$i]['alamat_customer'] =  $salesOrder[$i]['alamat_customer'] == "" ? "-" :  $salesOrder[$i]['alamat_customer'];
            $salesOrder[$i]['country_name'] = "-";

            if ($pengembalianBarangId != null) {
                if ($bc30 == null || $pengembalianBarangId == $s['sales_order_id']) {
                    array_push($result, $salesOrder[$i]); // Ketika Edit
                }
            } else {
                if ($bc30 == null) {
                    array_push($result, $salesOrder[$i]); // Ketika Create
                }
            }
        }

        return $result;
    }

    public function getListSalesOrderLain($companyId, $salesOrderLainId = null)
    {
        $metaDataModel = new MetadataModel();
        $salesOrderLainModel = new SalesOrderLainModel();

        $bcFirst = $metaDataModel->getBCFirst("BC 3.0");
        $selectQry = "
            sales_order_lain.*,
            customers.name AS nama_penerima,
            customers.address AS alamat_penerima,
            country.country_name,
            divisis.divisi,
            warehouses.warehouse_name
        ";

        $salesOrderList = $salesOrderLainModel->select($selectQry)
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
            ->where('status_posting', '1')
            ->where('sales_order_lain.bc_id', $bcFirst['id'])
            ->where('sales_order_lain.deletedAt', null)
            ->where('sales_order_lain.company_id', $companyId)
            ->findAll();

        $result = array();

        foreach ($salesOrderList as $s) {
            $bc30 = $this->where('sales_order_lain_id', $s['id'])->first();

            if ($salesOrderLainId != null) {
                if ($bc30 == null || $salesOrderLainId == $s['id']) {
                    array_push($result, $s);
                }
            } else {
                if ($bc30 == null) {
                    array_push($result, $s);
                }
            }
        }

        $resultData = array();
        foreach ($result as $r) {
            $resultData[] = [
                'sales_order_id' => ($r['id']),
                'no_sales_order' => $r['no_sales_order'],
                'nama_customer' => $r['nama_penerima'],
                'alamat_customer' => $r['alamat_penerima'],
                'tanggal_reference' => date('d/m/Y', strtotime($r['tanggal'])),
                'divisi' => $r['divisi'],
                'warehouse_name' => $r['warehouse_name'],
                'keterangan' => $r['keterangan'],
                'country_name' => $r['country_name']
            ];
        }

        return $resultData;
    }

    public function detail($id)
    {
        $selectQry = "
            bc_30.*,
            customers.name AS customer_name
        ";
        $dataResult = $this->asArray()->select($selectQry)
            ->join('customers', 'customers.id = bc_30.reference_penerima_id', 'left')
            ->where('bc_30.id', $id)
            ->first();
        return $dataResult;
    }

    public function detailBarang($bcId, $kodeBarang)
    {

        $bc30 = $this->find($bcId);
        if ($bc30['tipe_sales_order'] == "ORDER FORM EKSPOR") {
            $referenceId = $bc30['sales_order_id'];
        } elseif ($bc30['tipe_sales_order']  == "ORDER FORM LAIN") {
            $referenceId = $bc30['sales_order_lain_id'];
        } else {
            $referenceId = $bc30['pengembalian_barang_id'];
        }

        $listBarang = $this->barang($referenceId, $bc30['tipe_sales_order']);
        $result = [];
        $payload = json_decode($this->find($bcId)['payload']);


        foreach ($listBarang as $l) {
            if ($kodeBarang == $l['kode_barang_internal']) {
                $result = [
                    'barangDetail' => $l,
                    'bcDetail' => null
                ];
            }
        }

        foreach ($payload->barang as $b) {
            if ($b->kodeBarang == $result['barangDetail']['kode_barang_internal']) {
                $result['bcDetail'] = $b;
            }
        }


        return $result;
    }

    public function barang($referenceId, $typeReference)
    {
        $detailBarang =  $this->getListBarang(
            $referenceId,
            $typeReference
        );

        $result = [];

        foreach ($detailBarang as $item) {
            $key = $item['barang_master_id'];

            if (!isset($result[$key])) {
                $result[$key] = $item;
                $result[$key]['harga_number'] = $item['harga_number'];
                $result[$key]['qty_keluar'] = (int)$item['qty_keluar'];
            } else {
                $result[$key]['harga_number'] += $item['harga_number'];

                $result[$key]['qty_keluar'] += (int)$item['qty_keluar'];
            }
        }
        return array_values($result);
    }

    public function isCompleteFormHeader($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kodeKantor != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }
    public function isCompleteFormEntitas($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->entitas) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormDokumen($id)
    {

        //hjarus ad packing list

        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->dokumen) != 0) {
                $isCompleteForm = false;
                $foundInvoice = false;
                $foundPackingList = false;
                foreach ($payload->dokumen as $d) {
                    if ($d->kodeDokumen == '380') {
                        $foundInvoice = true;
                    }
                    if ($d->kodeDokumen == '217') {
                        $foundPackingList = true;
                    }
                    if ($foundInvoice && $foundPackingList) {
                        $isCompleteForm = true;
                    }
                }
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPengangkut($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->pengangkut) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPetiKemas($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->kontainer) != 0 && count($payload->kemasan) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormTransaksi($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kodeValuta) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormBarang($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->barang) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPernyataan($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kotaTtd != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }
    public function iscompleteFormKesiapanBarang($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->kesiapanBarang) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }
}
