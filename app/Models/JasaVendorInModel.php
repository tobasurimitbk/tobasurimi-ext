<?php

namespace App\Models;

use CodeIgniter\Model;

class JasaVendorInModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jasa_vendor_in';
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


    public function getList($condition, $conditionArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_penerimaan_surat_jalan' => 'no_penerimaan_surat_jalan',
            'jasa_vendor_in.createdAt' => 'jasa_vendor_in.createdAt',
            'jasa_vendor_in.divisi_id' => 'jasa_vendor_in.divisi_id',
            'jasa_vendor_in.warehouse_id' => 'jasa_vendor_in.warehouse_id',
            'vendor_id' => 'vendor_id',
            'no_surat_jalan_vendor' => 'no_surat_jalan_vendor',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "jasa_vendor_in.*,
        divisis.divisi,
        warehouses.warehouse_name,
        vendors.name as vendor_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = jasa_vendor_in.warehouse_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where($condition)
            ->whereIn('jasa_vendor_in.divisi_id', $conditionArr)
            ->where('divisis.divisi !=', 'PTS')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_penerimaan_surat_jalan'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('jasa_vendor_in.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->like('jasa_vendor_in.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['no_penerimaan_surat_jalan']) {
            $dataQry->like('no_penerimaan_surat_jalan', $addCondition['no_penerimaan_surat_jalan']);
        }

        if ($addCondition['start_date']) {
            $dataQry->where('tanggal >=', $addCondition['start_date']);
        }

        if ($addCondition['end_date']) {
            $dataQry->where('tanggal <=', $addCondition['end_date']);
        }

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_penerimaan_surat_jalan'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }


     public function getListKepiting($condition, $conditionArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_penerimaan_surat_jalan' => 'no_penerimaan_surat_jalan',
            'jasa_vendor_in.createdAt' => 'jasa_vendor_in.createdAt',
            // 'jasa_vendor_in.divisi_id' => 'jasa_vendor_in.divisi_id',
            'jasa_vendor_in.warehouse_id' => 'jasa_vendor_in.warehouse_id',
            'vendor_id' => 'vendor_id',
            'no_surat_jalan_vendor' => 'no_surat_jalan_vendor',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "jasa_vendor_in.*,
        divisis.divisi,
        warehouses.warehouse_name,
        vendors.name as vendor_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = jasa_vendor_in.warehouse_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where($condition)
            ->whereIn('jasa_vendor_in.divisi_id', $conditionArr)
            ->where('divisis.divisi', 'PTS')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_penerimaan_surat_jalan'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupStart();
        }

        // if ($addCondition['divisi_id']) {
        //     $dataQry->where('jasa_vendor_in.divisi_id', $addCondition['divisi_id']);
        // }

        if ($addCondition['warehouse_id']) {
            $dataQry->like('jasa_vendor_in.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['no_penerimaan_surat_jalan']) {
            $dataQry->like('no_penerimaan_surat_jalan', $addCondition['no_penerimaan_surat_jalan']);
        }

        if ($addCondition['start_date']) {
            $dataQry->where('tanggal >=', $addCondition['start_date']);
        }

        if ($addCondition['end_date']) {
            $dataQry->where('tanggal <=', $addCondition['end_date']);
        }

        if ($addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_penerimaan_surat_jalan'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }


    public function listBarang($jasaVendorOutArr, $jasaVendorInID)
    {
        $jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $stockModel = new StockModel();
        $metaDataModel = new MetadataModel();
        $stockDetail2Model = new StockDetail2Model();
        $stockRevampModel = new StockRevampModel();
        $stockRevampDetailModel = new StockRevampDetailModel();
        $supplierModel = new SupplierModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $jasaVendorInModel = new JasaVendorInModel();
        $barangMasterModel = new BarangMasterModel();

        $jasaVendorOutData = $jasaVendorOutDetailModel
            ->whereIn('jasa_vendor_out_id', $jasaVendorOutArr)
            ->where('deletedAt', null)
            ->findAll();

        $result = [];

        foreach ($jasaVendorOutData as $j) {
            $stockListOutDetail = $stockRevampDetailModel->where('id', $j['stock_out_detail_id'])->first();
            $stockBarangOut = $stockRevampModel->where('id', $stockListOutDetail['stock_id'])->first();

            $barangOut = $barangMasterModel->select("
                CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang,
                satuans.kode_satuan,
                barang_master.kode_barang,
                barang_master_spesifikasi.barang_master_id AS barang1_id
            ")
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->where('barang_master_spesifikasi.id', $stockBarangOut['spesifikasi_id'])
                ->where('barang_master_spesifikasi.barang_master_id', $stockBarangOut['barang_master_id'])
                ->first();

            $bc = $metaDataModel->find($j['bc_out_id']);

            $jasaVendorInDetail = $jasaVendorInDetailModel
                ->where('jasa_vendor_in_id', $jasaVendorInID)
                ->where('deletedAt', NULL)
                ->findAll();

            $rmPurchaseOrder = $rmPurchaseOrderModel->where('id', $stockListOutDetail['po_id'])->first();
            $supplier = $supplierModel->where('id', $rmPurchaseOrder['supplier_id'])->first();

            $resultNoJasaVendorIn = strstr($j['stock_dokumen'], '(', true);
            $noJasaVendorIn = trim($resultNoJasaVendorIn);
            $supplierName = $supplier['name'];
            $stockDate = $rmPurchaseOrder == null ? "" : date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));

            $jasaVendorIn = $jasaVendorInModel
                ->select('jasa_vendor_in.*,vendors.name as nama_vendor')
                ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                ->where('jasa_vendor_in.company_id', session()->get("login")->this_company_id)
                ->first();

            $result[] = [
                'jasa_vendor_out_detail_id' => $j['id'],
                'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                'barang1_id' => $stockBarangOut != null ? $stockBarangOut['barang_master_id'] : 0,
                'stock_out_id' => $j['stock_out_id'],
                'stock_date' => $jasaVendorIn == null ? $stockDate : date('d/m/Y', strtotime($jasaVendorIn['tanggal'])),
                'tipe_barang' => "BAHAN BAKU",
                'supplier_name' => $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'],
                'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                'bc_id' => $j['bc_out_id'],
                'no_aju' => $j['no_aju_out'],
                'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                'qty_out' => $j['qty'],
                'stock_dokumen' => $j['stock_dokumen'],
                'sumber' => $stockListOutDetail == null ?: $stockListOutDetail['reference_type'],
                'list_barang_masuk' => []
            ];

            $lastIndex = count($result) - 1;

            if (count($jasaVendorInDetail) != 0) {
                foreach ($jasaVendorInDetail as $k) {
                    $barangIn = $barangMasterModel->select("
                        CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang,
                        satuans.kode_satuan,
                        barang_master.kode_barang
                    ")
                        ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = ' . $k['spesifikasi_in_id'], 'left')
                        ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                        ->first();

                    $result[$lastIndex]['list_barang_masuk'][] = [
                        'barang1_id' => $stockBarangOut['barang_master_id'],
                        'barang_name_in' => strtoupper($barangIn['barang']),
                        'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                        'kode_barang_in' => strtoupper($barangIn['kode_barang']),
                        'kode_satuan_in' => $barangIn['kode_satuan'],
                        'stock_dokumen' => $k['stock_dokumen'],
                        'qty_bersih' => $k['qty_bersih'],
                        'qty_kotor' => $k['qty_kotor'],
                        'stock_in_id' => $k['stock_in_id'],
                        'stock_out_id' => $result[$lastIndex]['stock_out_id'],
                        'spesifikasi_in_id' => $k['spesifikasi_in_id'],
                    ];
                }
            }
        }
        
        // ========== FIX: GROUP BY BARANG1_ID YANG BENAR =========
        $resultGroup = [];

        foreach ($result as $item) {
            $barang1Id = $item['barang1_id'];

            // Jika belum ada group, buat 1 baris group
            if (!isset($resultGroup[$barang1Id])) {
                $resultGroup[$barang1Id] = [
                    "barang1_id" => $barang1Id,
                    "tipe_barang" => $item['tipe_barang'],
                    "kode_barang_out" => $item['kode_barang_out'],
                    "barang_out" => explode("-", $item['barang_out'])[0],
                    "satuan_out" => $item['satuan_out'],
                    "qty_out" => 0,
                    "jasa_vendor_out_id" => null, // Tetap null
                    "jasa_vendor_out_detail_id" => null, // Tetap null
                    "bc_id" => $item['bc_id'],
                    "no_aju" => $item['no_aju'],
                    "stock_dokumen" => $item['stock_dokumen'],
                    "supplier_name" => $item['supplier_name'],
                    "stock_date" => $item['stock_date'],
                    "list_barang_masuk" => []
                ];
            }

            // SUM qty_out dari semua OUT yang sama barang1_id
            $resultGroup[$barang1Id]['qty_out'] += $item['qty_out'];

            // HANYA tambahkan list_barang_masuk JIKA BELUM ADA di group
            if (!empty($item['list_barang_masuk'])) {
                foreach ($item['list_barang_masuk'] as $barangMasuk) {
                    // CEK DUPLIKAT berdasarkan spesifikasi_in_id
                    $isDuplicate = false;
                    foreach ($resultGroup[$barang1Id]['list_barang_masuk'] as $existing) {
                        if ($existing['spesifikasi_in_id'] == $barangMasuk['spesifikasi_in_id']) {
                            $isDuplicate = true;
                            break;
                        }
                    }
                    
                    // Hanya tambahkan jika belum ada
                    if (!$isDuplicate) {
                        $resultGroup[$barang1Id]['list_barang_masuk'][] = $barangMasuk;
                    }
                }
            }
        }

        // Convert associative array ke indexed array
        $resultGroup = array_values($resultGroup);

        return [
            'dataDetail' => $result,
            'dataGroup' => $resultGroup,
        ];
    }
    
    public function listBarangKepiting($jasaVendorOutArr, $jasaVendorInID)
    {
        $jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $stockModel = new StockModel();
        $metaDataModel = new MetadataModel();
        $stockDetail2Model = new StockDetail2Model();
        $supplierModel = new SupplierModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $jasaVendorInModel = new JasaVendorInModel();
        $stockRevampModel = new StockRevampModel();
        $stockRevampDetailModel = new StockRevampDetailModel();
        $barangMasterModel = new BarangMasterModel();
        

         // Mulai: ambil data jasa vendor out berdasarkan array id
        $jasaVendorOutData = $jasaVendorOutDetailModel
            ->whereIn('jasa_vendor_out_id', $jasaVendorOutArr)
            ->where('deletedAt', null)
            ->findAll();

        $result = [];

        // Loop setiap detail keluar
        foreach ($jasaVendorOutData as $j) {
            // Ambil stock out detail dengan aman
            $stockListOutDetail = $stockRevampDetailModel->where('id', $j['stock_out_detail_id'])->first();
            $stockBarangOut = $stockListOutDetail && isset($stockListOutDetail['stock_id'])
                ? $stockRevampModel->where('id', $stockListOutDetail['stock_id'])->first()
                : null;

            // Ambil data barangOut hanya jika stockBarangOut ada dan memiliki fields yang dibutuhkan
            $barangOut = null;
            if ($stockBarangOut && isset($stockBarangOut['spesifikasi_id'])) {
                $barangOut = $barangMasterModel
                    ->select("CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, satuans.kode_satuan, barang_master.kode_barang, barang_master_spesifikasi.barang_master_id AS barang1_id")
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                    ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                    ->where('barang_master_spesifikasi.id', $stockBarangOut['spesifikasi_id'])
                    ->where('barang_master_spesifikasi.barang_master_id', $stockBarangOut['barang_master_id'] ?? null)
                    ->first();
            }

            // Ambil bc metadata (boleh null)
            $bc = null;
            if (!empty($j['bc_out_id'])) {
                $bc = $metaDataModel->find($j['bc_out_id']);
            }

            // Ambil jasaVendorInDetail untuk jasaVendorInID jika diberikan
            $jasaVendorInDetail = [];
            if (!empty($jasaVendorInID)) {
                $jasaVendorInDetail = $jasaVendorInDetailModel
                    ->where('jasa_vendor_in_id', $jasaVendorInID)
                    ->where('deletedAt', NULL)
                    ->findAll() ?: [];
            }

            // Ambil PO -> RM PO -> supplier (semua aman jika null)
            $rmPurchaseOrder = null;
            $supplier = null;
            if ($stockListOutDetail && !empty($stockListOutDetail['po_id'])) {
                $rmPurchaseOrder = $rmPurchaseOrderModel->where('id', $stockListOutDetail['po_id'])->first();
                if ($rmPurchaseOrder && !empty($rmPurchaseOrder['supplier_id'])) {
                    $supplier = $supplierModel->where('id', $rmPurchaseOrder['supplier_id'])->first();
                }
            }

            // Parsel nomor jasa vendor in dari stock_dokumen (aman jika format beda)
            $resultNoJasaVendorIn = false;
            if (!empty($j['stock_dokumen'])) {
                $resultNoJasaVendorIn = strstr($j['stock_dokumen'], '(', true);
            }
            $noJasaVendorIn = $resultNoJasaVendorIn !== false && $resultNoJasaVendorIn !== null
                ? trim($resultNoJasaVendorIn)
                : ($j['stock_dokumen'] ?? '');

            $supplierName = $supplier['name'] ?? '-';
            $supplierId = $supplier['id'] ?? 0;

            // Ambil data jasa_vendor_in (jika ada id)
            $jasaVendorIn = null;
            if (!empty($jasaVendorInID)) {
                $jasaVendorIn = $jasaVendorInModel
                    ->select('jasa_vendor_in.*,vendors.name as nama_vendor')
                    ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                    ->where('jasa_vendor_in.id',  $jasaVendorInID)
                    ->where('jasa_vendor_in.deletedAt', NULL)
                    ->first();
            }

            $isOneRaw = (!empty($jasaVendorIn) && isset($jasaVendorIn['one_raw']) && $jasaVendorIn['one_raw'] == 1) ? true : false;

            // Siapkan base item result
            $baseItem = [
                'jasa_vendor_out_detail_id' => $j['id'] ?? null,
                'jasa_vendor_out_id' => $j['jasa_vendor_out_id'] ?? null,
                'barang1_id' => $stockBarangOut['barang_master_id'] ?? 0,
                'stock_out_id' => $j['stock_out_id'] ?? null,
                'stock_date' => !empty($jasaVendorIn['tanggal']) ? date('d/m/Y', strtotime($jasaVendorIn['tanggal'])) : ($rmPurchaseOrder ? date('d/m/Y', strtotime($rmPurchaseOrder['po_date'])) : ""),
                'tipe_barang' => "BAHAN BAKU",
                'supplier_id' => !empty($jasaVendorIn) ? ($supplierId . ' / ' . ($jasaVendorIn['id'] ?? '')) : $supplierId,
                'supplier_name' => !empty($jasaVendorIn) ? ($supplierName . ' / ' . ($jasaVendorIn['nama_vendor'] ?? '')) : $supplierName,
                'bc_name' => $bc['value'] ?? 'NON PABEAN',
                'bc_id' => $j['bc_out_id'] ?? null,
                'no_aju' => $j['no_aju_out'] ?? null,
                'kode_barang_out' => $barangOut['kode_barang'] ?? "-",
                'barang_out' => isset($barangOut['barang']) ? strtoupper($barangOut['barang']) : "-",
                'satuan_out' => $barangOut['kode_satuan'] ?? "-",
                'qty_out' => $j['qty'] ?? 0,
                'keterangan' => !empty($j['keterangan']) ? $j['keterangan'] : '-',
                'stock_dokumen' => $j['stock_dokumen'] ?? null,
                'sumber' => $stockListOutDetail ? ($stockListOutDetail['reference_type'] ?? null) : null,
                'list_barang_masuk' => [],
                'is_one_raw' => $isOneRaw
            ];

            // Push base item ke result
            $result[] = $baseItem;
        }

        // Jika ada jasaVendorInDetail (yang berkaitan), kita ingin menyusun list_barang_masuk ke tiap result item.
        // Untuk efisiensi: index result by jasa_vendor_out_detail_id
        if (!empty($result)) {
            // Buat map id -> index di array result
            $resultIndexMap = [];
            foreach ($result as $idx => $r) {
                $key = $r['jasa_vendor_out_detail_id'];
                $resultIndexMap[$key] = $idx;
                // pastikan list_barang_masuk array ada
                if (!isset($result[$idx]['list_barang_masuk']) || !is_array($result[$idx]['list_barang_masuk'])) {
                    $result[$idx]['list_barang_masuk'] = [];
                }
            }

            // Kalau ada $jasaVendorInID, ambil semua detail terkait sekali (lebih efisien)
            if (!empty($jasaVendorInID)) {
                $allInDetails = $jasaVendorInDetailModel
                    ->where('jasa_vendor_in_id', $jasaVendorInID)
                    ->where('deletedAt', NULL)
                    ->findAll() ?: [];

                foreach ($allInDetails as $k) {
                    $outDetailId = $k['jasa_vendor_out_detail_id'] ?? null;
                    if ($outDetailId !== null && isset($resultIndexMap[$outDetailId])) {
                        // Ambil barangIn dengan aman
                        $barangIn = null;
                        if (!empty($k['spesifikasi_in_id'])) {
                            $barangIn = $barangMasterModel
                                ->select("CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, satuans.kode_satuan, barang_master.kode_barang")
                                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                                ->where('barang_master_spesifikasi.id', $k['spesifikasi_in_id'])
                                ->first();
                        }

                        $pushItem = [
                            'id' => $k['id'] ?? null,
                            'jasa_vendor_in_id' => $k['jasa_vendor_in_id'] ?? null,
                            'barang1_id' => $k['barang_master_id'] ?? ($result[$resultIndexMap[$outDetailId]]['barang1_id'] ?? 0),
                            'barang_name_in' => $barangIn ? strtoupper($barangIn['barang']) : '-',
                            'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'] ?? null,
                            'kode_barang_in' => $barangIn ? strtoupper($barangIn['kode_barang']) : '-',
                            'kode_satuan_in' => $barangIn['kode_satuan'] ?? '-',
                            'stock_dokumen' => $k['stock_dokumen'] ?? null,
                            'qty_bersih' => $k['qty_bersih'] ?? 0,
                            'qty_kotor' => $k['qty_kotor'] ?? 0,
                            'stock_in_id' => $k['stock_in_id'] ?? null,
                            'stock_out_id' => $result[$resultIndexMap[$outDetailId]]['stock_out_id'] ?? null,
                            'spesifikasi_in_id' => $k['spesifikasi_in_id'] ?? null,
                        ];

                        $result[$resultIndexMap[$outDetailId]]['list_barang_masuk'][] = $pushItem;
                    }
                }
            }
        }

        $listBarangMasukGrouped = [];
        foreach ($result as $r) {
            foreach ($r['list_barang_masuk'] as $k) {
                $barang1Id = $k['barang1_id'];
                $stockDokumen = $r['stock_dokumen'];
                $stockInId = $k['stock_in_id'];
                
                // Jika one_raw = 1, jangan gunakan group key (langsung append tanpa grouping)
                if ($r['is_one_raw']) {
                    $listBarangMasukGrouped[] = [
                        'barang_name_in' => $k['barang_name_in'],
                        'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                        'kode_barang_in' => $k['kode_barang_in'],
                        'kode_satuan_in' => $k['kode_satuan_in'],
                        'stock_dokumen' => $k['stock_dokumen'],
                        'stock_in_id' => $k['stock_in_id'],
                        'stock_out_id' => $k['stock_out_id'],
                        'spesifikasi_in_id' => $k['spesifikasi_in_id'],
                        'qty_kotor' => (float)$k['qty_kotor'],
                        'qty_bersih' => (float)$k['qty_bersih']
                    ];
                } else {
                    // Untuk non one_raw, gunakan group key seperti semula
                    $groupKey = $barang1Id . '|' . $stockDokumen . '|' . $stockInId;
                    
                    if (!isset($listBarangMasukGrouped[$groupKey])) {
                        $listBarangMasukGrouped[$groupKey] = [
                            'barang_name_in' => $k['barang_name_in'],
                            'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                            'kode_barang_in' => $k['kode_barang_in'],
                            'kode_satuan_in' => $k['kode_satuan_in'],
                            'stock_dokumen' => $k['stock_dokumen'],
                            'stock_in_id' => $k['stock_in_id'],
                            'stock_out_id' => $k['stock_out_id'],
                            'spesifikasi_in_id' => $k['spesifikasi_in_id'],
                            'qty_kotor' => 0,
                            'qty_bersih' => 0
                        ];
                    }
                    $listBarangMasukGrouped[$groupKey]['qty_kotor'] += (float)$k['qty_kotor'];
                    $listBarangMasukGrouped[$groupKey]['qty_bersih'] += (float)$k['qty_bersih'];
                }
            }
        }

        $resultGroup = [];


        // FIXED GROUPING LOGIC
        foreach ($result as $item) {
            if ($item['is_one_raw']) {
                // 🔥 GROUP BY KETERANGAN DOANG untuk one_raw
                $groupKey = $item['keterangan'];
            } else {
                $groupKey = $item['supplier_id'] . '|' . $item['keterangan'] . '|' . $item['stock_dokumen'];
            }
            
            if (!isset($resultGroup[$groupKey])) {
                $resultGroup[$groupKey] = [];
            }
            $resultGroup[$groupKey][] = $item;
        }

        $dataGroup = [];

        if (!empty($result) && $result[0]['is_one_raw']) {
            foreach ($resultGroup as $groupKey => $items) {
        
                // Cek apakah group ini one_raw
                $isOneRawGroup = !empty($items[0]['is_one_raw']) && $items[0]['is_one_raw'];
                
                if ($isOneRawGroup) {
                    // 🔹 ONE RAW PROCESSING - PER KETERANGAN GROUP
                    $keterangan = $groupKey; // Karena groupKey = keterangan
                    
                    $allListBarangMasuk = [];
                    $groupedBarangMasuk = [];

                    // Ambil semua jasa_vendor_in_detail terkait jasaVendorInID
                    $allInDetails = $jasaVendorInDetailModel
                        ->where('jasa_vendor_in_id', $jasaVendorInID)
                        ->where('deletedAt', NULL)
                        ->findAll() ?: [];

                    foreach ($allInDetails as $k) {
                        // Filter hanya yang keterangan sama dengan group
                        if ($k['keterangan'] !== $keterangan) continue;
                        
                        // Ambil barangIn dengan aman
                        $barangIn = null;
                        if (!empty($k['spesifikasi_in_id'])) {
                            $barangIn = $barangMasterModel
                                ->select("CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, satuans.kode_satuan, barang_master.kode_barang, barang_master_spesifikasi.barang_master_id AS barang1_id")
                                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                                ->where('barang_master_spesifikasi.id', $k['spesifikasi_in_id'])
                                ->first();
                        }

                        $barang1Id = $barangIn ? $barangIn['barang1_id'] : ($k['barang_master_id'] ?? 0);
                        $groupKeyBarang = $barang1Id . '|' . ($k['spesifikasi_in_id'] ?? 0);

                        if (!isset($groupedBarangMasuk[$groupKeyBarang])) {
                            $groupedBarangMasuk[$groupKeyBarang] = [
                                'barang1_id' => $barang1Id,
                                'barang_name_in' => $barangIn ? strtoupper($barangIn['barang']) : '-',
                                'kode_barang_in' => $barangIn ? strtoupper($barangIn['kode_barang']) : '-',
                                'kode_satuan_in' => $barangIn['kode_satuan'] ?? '-',
                                'stock_dokumen' => $k['stock_dokumen'] ?? null,
                                'qty_kotor' => 0,
                                'qty_bersih' => 0,
                                'stock_in_id' => $k['stock_in_id'] ?? null,
                                'stock_out_id' => $k['stock_out_id'] ?? null,
                                'spesifikasi_in_id' => $k['spesifikasi_in_id'] ?? null,
                                'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'] ?? null
                            ];
                        }

                        // Tambahkan qty-nya
                        $groupedBarangMasuk[$groupKeyBarang]['qty_kotor'] += (float)$k['qty_kotor'];
                        $groupedBarangMasuk[$groupKeyBarang]['qty_bersih'] += (float)$k['qty_bersih'];
                    }

                    // Konversi hasil grouped ke array final
                    $allListBarangMasuk = array_values($groupedBarangMasuk);

                    // Hitung total qty_out untuk SEMUA ITEM dalam group keterangan ini
                    $qtyTotal = array_sum(array_map(fn($i) => (float)$i['qty_out'], $items));

                    // Ambil 1 data utama (bisa dari item mana aja)
                    $first = $items[0];
                    $barangArr = explode("-", $first['barang_out']);

                    $dataGroup[] = [
                        "supplier_id" => "ON GROUP",
                        "supplier_name" => "ON GROUP", 
                        "keterangan" => $keterangan,
                        "barang1_id" => $first['barang1_id'],
                        "tipe_barang" => $first['tipe_barang'],
                        "stock_dokumen" => "ON GROUP",
                        "kode_barang_out" => $first['kode_barang_out'],
                        "barang_out" => count($barangArr) > 0 ? trim($barangArr[0]) : "-",
                        "qty_out" => $qtyTotal,
                        "satuan_out" => $first['satuan_out'],
                        "list_barang_masuk" => $allListBarangMasuk,
                        "is_one_raw" => true
                    ];
                }
            }
        } else {
            // 🔹 Kondisi NORMAL (non one_raw) - TETAP SAMA
            foreach ($resultGroup as $groupKey => $items) {
                list($supplierId, $keterangan, $stock_dokumen) = explode('|', $groupKey);
                
                $qtyTotal = 0;
                $allListBarangMasuk = [];

                foreach ($items as $item) {
                    $qtyTotal += (float)$item['qty_out'];
                    
                    // Kumpulkan semua list_barang_masuk dari semua item dalam group
                    foreach ($item['list_barang_masuk'] as $barangMasuk) {
                        $allListBarangMasuk[] = $barangMasuk;
                    }
                }

                $barangArr = explode("-", $items[0]['barang_out']);

                $groupData = [
                    "supplier_id" => $supplierId,
                    "supplier_name" => $items[0]['supplier_name'],
                    "keterangan" => $keterangan,
                    "barang1_id" => $items[0]['barang1_id'],
                    "tipe_barang" => $items[0]['tipe_barang'],
                    "stock_dokumen" => $stock_dokumen,
                    "kode_barang_out" => $items[0]['kode_barang_out'],
                    "barang_out" => count($barangArr) == 0 ? "-" : $barangArr[0],
                    "qty_out" => $qtyTotal,
                    "satuan_out" => $items[0]['satuan_out'],
                    "list_barang_masuk" => $allListBarangMasuk, // Langsung pakai semua data
                    "is_one_raw" => false
                ];

                $dataGroup[] = $groupData;
            }
        }

        return [
            'dataDetail' => $result,
            'dataGroup' => $dataGroup
        ];

    }

    static function getDetailBarang($stock)
    {
        $barangMasterModel = new BarangMasterModel();

        $barang = $barangMasterModel
            ->select("CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, satuans.kode_satuan, barang_master.kode_barang, barang_master_spesifikasi.barang_master_id AS barang1_id")
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('barang_master_spesifikasi.id', $stock['spesifikasi_id'])
            ->where('barang_master_spesifikasi.barang_master_id', $stock['barang_master_id'])
            ->first();

        return $barang;
    }

    public function getJasaVendorOutNo($jasaVendorOutID)
    {
        $jasaVendorOutModel = new JasaVendorOutModel();
        $result = array();
        $dataQry = $jasaVendorOutModel->whereIn('id', $jasaVendorOutID)->where('deletedAt', null)->findAll();

        foreach ($dataQry as $d) {
            array_push($result, $d['no_surat_jalan']);
        }

        return $result;
    }

    public function get_no($bln, $thn, $divisi)
    {
        $lastStr = convertBulanToAngkaRomawi($bln) . '/' . $thn;
        $first_day = "$thn-$bln-01";
        $last_day = date("Y-m-t", strtotime($first_day));

        $builder = $this->db->table('jasa_vendor_in');
        $builder->select('no_penerimaan_surat_jalan');
        $builder->orderBy('id', 'desc');
        $builder->where('company_id', session()->get("login")->this_company_id);
        $builder->where('tanggal >=', $first_day);
        $builder->where('tanggal <=', $last_day);
        $builder->like('no_penerimaan_surat_jalan', $lastStr);
        $query = $builder->get();

        $kode = 'TOBA-VBM/' . $divisi;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_penerimaan_surat_jalan']);
                $number = intval($explode[2]);

                if ($number > $lastPenerimaan) {
                    $lastPenerimaan = $number;
                }
            }
            $lastPenerimaan++;
        }

        $formattedLastPenerimaan = sprintf("%02d", $lastPenerimaan);
        $generatedNo = $kode . '/' . $formattedLastPenerimaan . '/' . $lastStr;

        return $generatedNo;
    }
}
