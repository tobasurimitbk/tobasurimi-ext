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
                $bcDataQry->whereIn('bc_30.tipe_sales_order', ['LOKAL', 'INTERNASIONAL']);
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

    public function getListBarang($salesOrderId, $tipeSalesOrder)
    {
        $stuffingLokalDetailModel = new StuffingLokalDetailModel();
        $stuffingInternasionalDetailModel = new StuffingInternasionalDetailModel();
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $metaDataModel = new MetadataModel();
        $salesOrderDetailModel = new SalesOrderDetailModel();
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $satuanModel = new SatuansModel();

        if ($tipeSalesOrder == "LOKAL") {

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
                ->where('stuffing_lokal.sales_order_id', $salesOrderId)
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
                $salesOrderDetail = $salesOrderDetailModel->where('id_barang', $dataResult[$i]['barang_id_order'])->where('id_sales_order', $salesOrderId)->first();
                $satuanSales = $satuanModel->find($dataResult[$i]['satuan_sales_id']);
                $satuanInternal = $satuanModel->find($dataResult[$i]['satuan_internal_id']);

                $dataResult[$i]['tanggal_keluar'] = date('d/m/Y', strtotime($dataResult[$i]['tanggal_keluar']));
                $dataResult[$i]['tipe_barang'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                $dataResult[$i]['dokumen_asal'] = ($bcTypeText != "NON PABEAN") ? $bcTypeText . " / " . $stockList['no_aju'] : "NON PABEAN";
                $dataResult[$i]['harga'] =  number_format(($dataResult[$i]['qty_keluar'] * $salesOrderDetail['harga_barang']), 2);
                $dataResult[$i]['harga_number'] = ($dataResult[$i]['qty_keluar'] * $salesOrderDetail['harga_barang']);
                $dataResult[$i]['mata_uang'] = "IDR";
                $dataResult[$i]['kode_satuan_sales'] = $satuanSales == null ? "-" : $satuanSales['kode_satuan'];
                $dataResult[$i]['kode_satuan_internal'] = $satuanInternal == null ? "-" : $satuanInternal['kode_satuan'];
            }
        } else {
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
                ->where('stuffing_internasional.sales_order_export_id', $salesOrderId)
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
                    ->where('sales_order_export_id', $salesOrderId)
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
        }

        return $dataResult;
    }

    public function getListSalesOrder($companyId, $tipeSalesOrder)
    {
        $salesOrderLokalModel = new SalesOrderModel();
        $salesOrderExportModel = new SalesOrderExportModel();

        if ($tipeSalesOrder == "LOKAL") {
            // LOKAL
            $selectQry = "
                sales_order.id AS sales_order_id,
                sales_order.no_sales_order,
                customers.name AS nama_customer,
                customers.address AS alamat_customer,
                country.country_name
            ";
            $salesOrder = $salesOrderLokalModel
                ->select($selectQry)
                ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                ->join('country', 'country.id = customers.country_id', 'left')
                ->where('used', "USED")
                ->where('sales_order.id_company', $companyId)
                ->where('sales_order.deletedAt', null)
                ->findAll();
            $result = array();
            foreach ($salesOrder as $i => $s) {
                $bc23 = $this->where('sales_order_id', $s['sales_order_id'])->where('tipe_sales_order', "LOKAL")->first();
                if ($bc23 == null) {
                    $salesOrder[$i]['alamat_customer'] =  $salesOrder[$i]['alamat_customer'] == "" ? "-" :  $salesOrder[$i]['alamat_customer'];
                    $salesOrder[$i]['country_name'] =  $salesOrder[$i]['country_name'] == "" ? "-" :  $salesOrder[$i]['alamat_customer'];
                    array_push($result, $salesOrder[$i]);
                }
            }
        } else {
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
                $bc23 = $this->where('sales_order_id', $s['sales_order_id'])->where('tipe_sales_order', "INTERNASIONAL")->first();
                if ($bc23 == null) {
                    $salesOrder[$i]['alamat_customer'] =  $salesOrder[$i]['alamat_customer'] == "" ? "-" :  $salesOrder[$i]['alamat_customer'];
                    $salesOrder[$i]['country_name'] =  $salesOrder[$i]['country_name'] == "" ? "-" :  $salesOrder[$i]['alamat_customer'];
                    array_push($result, $salesOrder[$i]);
                }
            }
        }

        return $result;
    }

    public function detail($id)
    {

        $first = $this->find($id);

        if ($first == null) {
            return null;
        }

        if ($first['tipe_sales_order'] == "LOKAL") {
            $selectQry = "
                bc_30.*,
                sales_order.no_sales_order,
                stuffing_lokal.no_stuffing,
                customers.name AS nama_customer,
                customers.address AS alamat,
                country.country_name,
            ";
            $result = $this->select($selectQry)
                ->join('sales_order', 'sales_order.id = bc_30.sales_order_id', 'left')
                ->join('stuffing_lokal', 'stuffing_lokal.sales_order_id = sales_order.id', 'left')
                ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                ->join('country', 'country.id = customers.country_id', 'left')
                ->where('bc_30.id', $id)
                ->first();
        } else {
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
        }

        $result['country_name'] = $result['country_name'] == null ? "-" : $result['country_name'];
        return $result;
    }

    public function detailBarang($bcId, $kodeBarang)
    {

        $bc30 = $this->find($bcId);
        $listBarang = $this->barang($bc30['sales_order_id'], $bc30['tipe_sales_order']);
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

    public function barang($salesOrderId, $tipeSalesOrder)
    {
        $detailBarang =  $this->getListBarang(
            $salesOrderId,
            $tipeSalesOrder
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
