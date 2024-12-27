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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'bc_30.tipe_pengeluaran' => 'bc_30.tipe_sales_order',
            'bc_30.sales_order_id' => 'bc_30.sales_order_id',
            'bc_30.no_aju' => 'bc_30.no_aju',
            'bc_30.createdAt' => 'bc_30.createdAt',
            'bc_30.status_posting' => 'bc_30.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'bc_30.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_30.*";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusPosting'] || $addCondition['noAju'] || $addCondition['tipeSalesOrder'] && (empty($addCondition['mulaiTanggalBC30']) && empty($addCondition['selesaiTanggalBC30']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusPosting']) {
            if ($addCondition['statusPosting'] == "ALL") {
                $bcDataQry->whereIn('bc_30.status_posting', ['1', '0']);
            } elseif ($addCondition['statusPosting'] == "SUDAH POSTING") {
                $bcDataQry->where('bc_30.status_posting', "1");
            } else if ($addCondition['statusPosting'] == "BELUM POSTING") {
                $bcDataQry->where('bc_30.status_POSTING', "0");
            }
        }

        if ($addCondition['tipeSalesOrder']) {
            if ($addCondition['tipeSalesOrder'] == "ALL") {
                $bcDataQry->whereIn('bc_30.tipe_sales_order', ['RETUR PEMBELIAN', 'ORDER FORM LAIN', 'ORDER FORM EKSPOR']);
            } else {
                $bcDataQry->whereIn('bc_30.tipe_sales_order', [$addCondition['tipeSalesOrder']]);
            }
        }

        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju'])->orLike('no_daftar', $addCondition['noAju']);
        }

        if ($addCondition['statusPosting'] || $addCondition['noAju'] || $addCondition['tipeSalesOrder'] && (empty($addCondition['mulaiTanggalBC30']) && empty($addCondition['selesaiTanggalBC30']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC30'] && $addCondition['selesaiTanggalBC30']) {
            $bcDataQry->groupStart();
            $mulaiTanggalBC30Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalBC30']), "Y-m-d");
            $selesaiTanggalBC30Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalBC30']), "Y-m-d");

            if ($addCondition['mulaiTanggalBC30']) {
                $bcDataQry->where('bc_30.createdAt >=', $mulaiTanggalBC30Timestamp);
            }

            if ($addCondition['selesaiTanggalBC30']) {
                $bcDataQry->where('bc_30.createdAt <=', $selesaiTanggalBC30Timestamp);
            }

            $bcDataQry->groupEnd();
        }

        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
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
                'tanggal_reference' => date('d/m/Y', strtotime('tanggal_sales_order')),
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

        $first = $this->find($id);

        if ($first == null) {
            return null;
        }

        if ($first['sales_order_id'] != null) {
            $selectQry = "
                bc_30.*,
                sales_order_export.sales_order_export_no AS no_sales_order,
                stuffing_internasional.no_stuffing,
                customers.name AS nama_customer,
                customers.address AS alamat,
                country.country_name,
            ";
            $result = $this->select($selectQry)
                ->join('sales_order_export', 'sales_order_export.sales_order_export_id = bc_30.sales_order_id', 'left')
                ->join('stuffing_internasional', 'stuffing_internasional.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
                ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
                ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
                ->join('country', 'country.id = customers.country_id', 'left')
                ->where('bc_30.id', $id)
                ->first();
        } elseif ($first['pengembalian_barang_id'] != null) {
            $selectQry = "
                bc_30.*,
                pengembalian_barang.no_surat_jalan AS no_sales_order,
                suppliers.name AS nama_customer,
                suppliers.address AS alamat,
            ";
            $result = $this->select($selectQry)
                ->join('pengembalian_barang', 'pengembalian_barang.id = bc_30.pengembalian_barang_id', 'left')
                ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
                ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                ->where('bc_30.id', $id)
                ->first();

            //  Tidak Ada Country
            $result['country_name'] = null;
        } elseif ($first['sales_order_lain_id'] != null) {
            $selectQry = "
                bc_30.*,
                sales_order_lain.no_sales_order AS no_sales_order,
                customers.name AS nama_customer,
                customers.address AS alamat,
                country.country_name,
            ";
            $result = $this->select($selectQry)
                ->join('sales_order_lain', 'sales_order_lain.id = bc_30.sales_order_lain_id', 'left')
                ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
                ->join('country', 'country.id = customers.country_id', 'left')
                ->where('bc_30.id', $id)
                ->first();
        }

        $result['country_name'] = $result['country_name'] == null ? "-" : $result['country_name'];
        return $result;
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
