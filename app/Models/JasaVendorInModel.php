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
        $supplierModel = new SupplierModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $jasaVendorInModel = new JasaVendorInModel();

        $jasaVendorOutData = $jasaVendorOutDetailModel->whereIn('jasa_vendor_out_id', $jasaVendorOutArr)->where('deletedAt', null)->findAll();
        $result = array();

        // var_dump($jasaVendorOutArr, $jasaVendorInID);
        // die;

        foreach ($jasaVendorOutData as $j) {
            $stockDetail = $stockDetail2Model->find($j['stock_out_id']);
            $stockBarangOut = $stockModel->find($stockDetail['stock_id']);
            // $stockListOutDetail = $stockDetail2Model->getStockListDetail(
            //     $j['stock_out_id'],
            //     $j['bc_out_id'],
            //     $j['no_aju_out'],
            //     $j['stock_dokumen']
            // );

            $stockListOutDetail = $stockDetail2Model->getStockListDetailNew(
                $j['stock_out_id']
            );

            $barangOut = self::getDetailBarang($stockBarangOut);

            if ($stockListOutDetail) {
                $bc = $metaDataModel->find($j['bc_out_id']);
                $jasaVendorInDetail = $jasaVendorInDetailModel
                    ->where('jasa_vendor_in_id', $jasaVendorInID)
                    ->where('jasa_vendor_out_id', $j['jasa_vendor_out_id'])
                    ->where('jasa_vendor_out_detail_id', $j['id'])
                    ->findAll();


                $noLpb = $stockListOutDetail != null ? $stockListOutDetail['no_dokumen_1'] : '';

                $supplier = $supplierModel->select('suppliers.*')
                    ->join('penerimaan_barang', 'penerimaan_barang.supplier_id = suppliers.id')
                    ->where('penerimaan_barang.no_penerimaan_barang', $noLpb)
                    ->first();
                
                $rmPurchaseOrder = $rmPurchaseOrderModel->where('po_no', $j['stock_dokumen'])
                    ->where('company_id', $stockListOutDetail['company_id'])
                    ->first();


                $resultNoJasaVendorIn = strstr($j['stock_dokumen'], '(', true);
                $noJasaVendorIn = trim($resultNoJasaVendorIn);
                $supplierName = $stockListOutDetail['supplier_name'];
                $stockDate = $rmPurchaseOrder == null ? "" :  date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));

                $jasaVendorIn = $jasaVendorInModel
                    ->select('jasa_vendor_in.*,vendors.name as nama_vendor')
                    ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                    ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                    ->where('jasa_vendor_in.company_id',  session()->get("login")->this_company_id)
                    ->first();

                // $result[] = [
                //         'jasa_vendor_out_detail_id' => $j['id'],
                //         'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                //         'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0,
                //         'stock_out_id' => $j['stock_out_id'],
                //         'stock_date' =>  $jasaVendorIn == null ? $stockDate :  date('d/m/Y', strtotime($jasaVendorIn['tanggal'])),
                //         'tipe_barang' => $stockBarangOut != null ? strtoupper(str_replace('_', ' ', $stockBarangOut['tipe_barang'])) : "",
                //         'supplier_name' =>  $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'],
                //         'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                //         'bc_id' => $j['bc_out_id'],
                //         'no_aju' => $j['no_aju_out'],
                //         'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                //         'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                //         'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                //         'qty_out' => $j['qty'],
                //         'stock_dokumen' => $j['stock_dokumen'],
                //         'sumber' => $stockListOutDetail == null ?: $stockListOutDetail['sumber'],
                //         'list_barang_masuk' => []
                //     ];    

                // if ($jasaVendorInID == null) {
                //     if (count($jasaVendorInDetail) != 0) {
                //             $result[] = [
                //                 'jasa_vendor_out_detail_id' => $j['id'],
                //                 'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                //                 'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0,
                //                 'stock_out_id' => (string)$j['stock_out_id'],
                //                 'stock_date' =>  $jasaVendorIn == null ? $stockDate :  date('d/m/Y', strtotime($jasaVendorIn['tanggal'])),
                //                 'tipe_barang' => $stockBarangOut != null ? strtoupper(str_replace('_', ' ', $stockBarangOut['tipe_barang'])) : "",
                //                 'supplier_name' =>  $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'],
                //                 'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                //                 'bc_id' => $j['bc_out_id'],
                //                 'no_aju' => $j['no_aju_out'],
                //                 'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                //                 'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                //                 'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                //                 'qty_out' => $j['qty'],
                //                 'stock_dokumen' => $j['stock_dokumen'],
                //                 'sumber' => $stockListOutDetail == null ?: $stockListOutDetail['sumber'],
                //                 'list_barang_masuk' => []
                //             ];

                //             for ($i = 0; $i < count($result); $i++) {
                //                 foreach ($jasaVendorInDetail as $k) {
                //                     if ($result[$i]['jasa_vendor_out_detail_id'] == $k['jasa_vendor_out_detail_id']) {
                //                         $stockBarangIn = $stockModel->find($k['stock_in_id']);
                //                         $barangIn = self::getDetailBarang($stockBarangIn);
                //                         array_push($result[$i]['list_barang_masuk'], [
                //                             'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0, // YANG OUT
                //                             'barang_name_in' => $barangIn != null ? strtoupper($barangIn['barang']) : '-',
                //                             'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                //                             'kode_barang_in' => $barangIn != null ? strtoupper($barangIn['kode_barang']) : '-',
                //                             'kode_satuan_in' => $barangIn != null ? $barangIn['kode_satuan'] : '-',
                //                             'stock_dokumen' => $k['stock_dokumen'],
                //                             'qty_bersih' => $k['qty_bersih'],
                //                             'qty_kotor' => $k['qty_kotor'],
                //                             'stock_in_id' => $k['stock_in_id'],
                //                             'stock_out_id' => $result[$i]['stock_out_id'],
                //                             'spesifikasi_in_id' => $k['spesifikasi_in_id'],
                //                         ]);
                //                     }
                //                 }
                //             }    
                //     }
                // }   




                if ($jasaVendorInID == null) {
                    $result[] = [
                        'jasa_vendor_out_detail_id' => $j['id'],
                        'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                        'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0,
                        'stock_out_id' => $j['stock_out_id'],
                        'stock_date' =>  $jasaVendorIn == null ? $stockDate :  date('d/m/Y', strtotime($jasaVendorIn['tanggal'])),
                        'tipe_barang' => $stockBarangOut != null ? strtoupper(str_replace('_', ' ', $stockBarangOut['tipe_barang'])) : "",
                        'supplier_name' =>  $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'],
                        'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                        'bc_id' => $j['bc_out_id'],
                        'no_aju' => $j['no_aju_out'],
                        'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                        'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                        'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                        'qty_out' => $j['qty'],
                        'stock_dokumen' => $j['stock_dokumen'],
                        'sumber' => $stockListOutDetail == null ?: $stockListOutDetail['sumber'],
                        'list_barang_masuk' => []
                    ];
                } else {

                    $result[] = [
                        'jasa_vendor_out_detail_id' => $j['id'],
                        'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                        'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0,
                        'stock_out_id' => $j['stock_out_id'],
                        'stock_date' =>  $jasaVendorIn == null ? $stockDate :  date('d/m/Y', strtotime($jasaVendorIn['tanggal'])),
                        'tipe_barang' => $stockBarangOut != null ? strtoupper(str_replace('_', ' ', $stockBarangOut['tipe_barang'])) : "",
                        'supplier_name' =>  $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'],
                        'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                        'bc_id' => $j['bc_out_id'],
                        'no_aju' => $j['no_aju_out'],
                        'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                        'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                        'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                        'qty_out' => $j['qty'],
                        'stock_dokumen' => $j['stock_dokumen'],
                        'sumber' => $stockListOutDetail == null ?: $stockListOutDetail['sumber'],
                        'list_barang_masuk' => []
                    ];

                    if (count($jasaVendorInDetail) != 0) {
                        for ($i = 0; $i < count($result); $i++) {
                            foreach ($jasaVendorInDetail as $k) {
                                if ($result[$i]['jasa_vendor_out_detail_id'] == $k['jasa_vendor_out_detail_id']) {
                                    $stockBarangIn = $stockModel->find($k['stock_in_id']);
                                    $barangIn = self::getDetailBarang($stockBarangIn);
                                    array_push($result[$i]['list_barang_masuk'], [
                                        'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0, // YANG OUT
                                        'barang_name_in' => $barangIn != null ? strtoupper($barangIn['barang']) : '-',
                                        'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                                        'kode_barang_in' => $barangIn != null ? strtoupper($barangIn['kode_barang']) : '-',
                                        'kode_satuan_in' => $barangIn != null ? $barangIn['kode_satuan'] : '-',
                                        'stock_dokumen' => $k['stock_dokumen'],
                                        'qty_bersih' => $k['qty_bersih'],
                                        'qty_kotor' => $k['qty_kotor'],
                                        'stock_in_id' => $k['stock_in_id'],
                                        'stock_out_id' => $result[$i]['stock_out_id'],
                                        'spesifikasi_in_id' => $k['spesifikasi_in_id'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }


        $listBarangMasukGrouped = [];
        foreach ($result as $r) {
            foreach ($r['list_barang_masuk'] as $k) {
                $barang1Id = $k['barang1_id'];
                $stockInId = $k['stock_in_id'];
                if (!isset($listBarangMasukGrouped[$barang1Id][$stockInId])) {
                    $listBarangMasukGrouped[$barang1Id][$stockInId] = [
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
                $listBarangMasukGrouped[$barang1Id][$stockInId]['qty_kotor'] += $k['qty_kotor'];
                $listBarangMasukGrouped[$barang1Id][$stockInId]['qty_bersih'] += $k['qty_bersih'];
            }
        }

        $resultGroup = [];

        foreach ($result as $item) {
            $barang1Id = $item['barang1_id'];
            if (!isset($resultGroup[$barang1Id])) {
                $resultGroup[$barang1Id] = [];
            }
            $resultGroup[$barang1Id][] = $item;
        }

        $dataGroup = [];

        foreach ($resultGroup as $barang1Id => $items) {
            // SUM QTY
            $qtyTotal = 0;
            foreach ($items as $i) {
                $qtyTotal += $i['qty_out'];
            }
            $barangArr = explode("-", $items[0]['barang_out']);
            $groupData = [
                "barang1_id" => $barang1Id,
                "tipe_barang" => $items[0]['tipe_barang'],
                "kode_barang_out" => $items[0]['kode_barang_out'],
                "barang_out" => count($barangArr) == 0 ? "-" : $barangArr[0],
                "qty_out" => $qtyTotal,
                "satuan_out" => $items[0]['satuan_out'],
                "list_barang_masuk" => [],
            ];
            $dataGroup[] = $groupData;

            if (isset($listBarangMasukGrouped[$barang1Id])) {
                foreach ($listBarangMasukGrouped[$barang1Id] as $barang1Id => $listBarangMasuk) {
                    $dataGroup[count($dataGroup) - 1]['list_barang_masuk'][] = $listBarangMasuk;
                }
            }
        }

        return [
            'dataDetail' => $result,
            'dataGroup' => $dataGroup
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
        

        $jasaVendorOutData = $jasaVendorOutDetailModel->whereIn('jasa_vendor_out_id', $jasaVendorOutArr)->where('deletedAt', null)->findAll();
        $result = array();

        foreach ($jasaVendorOutData as $j) {
            $stockDetail = $stockDetail2Model->find($j['stock_out_id']);
            $stockBarangOut = $stockModel->find($stockDetail['stock_id']);

            $stockListOutDetail = $stockDetail2Model->getStockListDetailNew(
                $j['stock_out_id']
            );

            $barangOut = self::getDetailBarang($stockBarangOut);

            if ($stockListOutDetail) {
                $bc = $metaDataModel->find($j['bc_out_id']);
                $jasaVendorInDetail = $jasaVendorInDetailModel
                    ->where('jasa_vendor_in_id', $jasaVendorInID)
                    ->where('jasa_vendor_out_id', $j['jasa_vendor_out_id'])
                    ->where('jasa_vendor_out_detail_id', $j['id'])
                    ->findAll();


                $noLpb = $stockListOutDetail != null ? $stockListOutDetail['no_dokumen_1'] : '';

                $supplier = $supplierModel->select('suppliers.*')
                    ->join('penerimaan_barang', 'penerimaan_barang.supplier_id = suppliers.id')
                    ->where('penerimaan_barang.no_penerimaan_barang', $noLpb)
                    ->first();
                
                $rmPurchaseOrder = $rmPurchaseOrderModel->where('po_no', $j['stock_dokumen'])
                    ->where('company_id', $stockListOutDetail['company_id'])
                    ->first();


                $resultNoJasaVendorIn = strstr($j['stock_dokumen'], '(', true);
                $noJasaVendorIn = trim($resultNoJasaVendorIn);
                $supplierName = $stockListOutDetail['supplier_name'];
                $supplierId = $stockListOutDetail['supplier_id'];
                $stockDate = $rmPurchaseOrder == null ? "" :  date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));

                $jasaVendorIn = $jasaVendorInModel
                    ->select('jasa_vendor_in.*,vendors.name as nama_vendor')
                    ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                    ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                    ->where('jasa_vendor_in.company_id',  session()->get("login")->this_company_id)
                    ->first();




                if ($jasaVendorInID == null) {
                    $result[] = [
                        'jasa_vendor_out_detail_id' => $j['id'],
                        'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                        'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0,
                        'stock_out_id' => $j['stock_out_id'],
                        'stock_date' =>  $jasaVendorIn == null ? $stockDate :  date('d/m/Y', strtotime($jasaVendorIn['tanggal'])),
                        'tipe_barang' => $stockBarangOut != null ? strtoupper(str_replace('_', ' ', $stockBarangOut['tipe_barang'])) : "",
                        'supplier_id' => $jasaVendorIn == null ? $supplierId : $supplierId . ' / ' . $jasaVendorIn['id'],
                        'supplier_name' =>  $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'],
                        'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                        'bc_id' => $j['bc_out_id'],
                        'no_aju' => $j['no_aju_out'],
                        'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                        'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                        'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                        'qty_out' => $j['qty'],
                        'keterangan' => !empty($j['keterangan']) ? $j['keterangan'] : '-', // ⬅️ handle kosong
                        'stock_dokumen' => $j['stock_dokumen'],
                        'sumber' => $stockListOutDetail == null ?: $stockListOutDetail['sumber'],
                        'list_barang_masuk' => []
                    ];
                } else {

                    $result[] = [
                        'jasa_vendor_out_detail_id' => $j['id'],
                        'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                        'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0,
                        'stock_out_id' => $j['stock_out_id'],
                        'stock_date' =>  $jasaVendorIn == null ? $stockDate :  date('d/m/Y', strtotime($jasaVendorIn['tanggal'])),
                        'tipe_barang' => $stockBarangOut != null ? strtoupper(str_replace('_', ' ', $stockBarangOut['tipe_barang'])) : "",
                        'supplier_id' => $jasaVendorIn == null ? $supplierId : $supplierId . ' / ' . $jasaVendorIn['id'],
                        'supplier_name' =>  $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'],
                        'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                        'bc_id' => $j['bc_out_id'],
                        'no_aju' => $j['no_aju_out'],
                        'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                        'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                        'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                        'qty_out' => $j['qty'],
                        'keterangan' => !empty($j['keterangan']) ? $j['keterangan'] : '-', // ⬅️ handle kosong
                        'stock_dokumen' => $j['stock_dokumen'],
                        'sumber' => $stockListOutDetail == null ?: $stockListOutDetail['sumber'],
                        'list_barang_masuk' => []
                    ];

                    if (count($jasaVendorInDetail) != 0) {
                        for ($i = 0; $i < count($result); $i++) {
                            foreach ($jasaVendorInDetail as $k) {
                                if ($result[$i]['jasa_vendor_out_detail_id'] == $k['jasa_vendor_out_detail_id']) {
                                    $stockBarangIn = $stockModel->find($k['stock_in_id']);
                                    $barangIn = self::getDetailBarang($stockBarangIn);
                                    array_push($result[$i]['list_barang_masuk'], [
                                        'barang1_id' => $stockListOutDetail != null ? $stockListOutDetail['barang1_id'] : 0, // YANG OUT
                                        'barang_name_in' => $barangIn != null ? strtoupper($barangIn['barang']) : '-',
                                        'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                                        'kode_barang_in' => $barangIn != null ? strtoupper($barangIn['kode_barang']) : '-',
                                        'kode_satuan_in' => $barangIn != null ? $barangIn['kode_satuan'] : '-',
                                        'stock_dokumen' => $k['stock_dokumen'],
                                        'qty_bersih' => $k['qty_bersih'],
                                        'qty_kotor' => $k['qty_kotor'],
                                        'stock_in_id' => $k['stock_in_id'],
                                        'stock_out_id' => $result[$i]['stock_out_id'],
                                        'spesifikasi_in_id' => $k['spesifikasi_in_id'],
                                    ]);
                                }
                            }
                        }
                    }

                }
            }
        }

        $listBarangMasukGrouped = [];
        foreach ($result as $r) {
            foreach ($r['list_barang_masuk'] as $k) {
                $stockDokumen = $r['stock_dokumen']; // hanya stock_dokumen yg dipakai group key

                if (!isset($listBarangMasukGrouped[$stockDokumen])) {
                    $listBarangMasukGrouped[$stockDokumen] = [];
                }

                $listBarangMasukGrouped[$stockDokumen][] = [
                    'barang_name_in'          => $k['barang_name_in'],
                    'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                    'kode_barang_in'          => $k['kode_barang_in'],
                    'kode_satuan_in'          => $k['kode_satuan_in'],
                    'stock_dokumen'           => $stockDokumen,
                    'stock_in_id'             => $k['stock_in_id'],
                    'stock_out_id'            => $k['stock_out_id'],
                    'spesifikasi_in_id'       => $k['spesifikasi_in_id'],
                    'qty_kotor'               => (float)$k['qty_kotor'],
                    'qty_bersih'              => (float)$k['qty_bersih']
                ];
            }
        }

        $resultGroup = [];

        // 👉 grouping HANYA berdasarkan stock_dokumen
        foreach ($result as $item) {
            $groupKey = $item['stock_dokumen'];
            if (!isset($resultGroup[$groupKey])) {
                $resultGroup[$groupKey] = [];
            }
            $resultGroup[$groupKey][] = $item;
        }

        $dataGroup = [];

        foreach ($resultGroup as $stock_dokumen => $items) {
            // SUM QTY
            $qtyTotal = 0;
            foreach ($items as $i) {
                $qtyTotal += (float)$i['qty_out'];
            }

            $barangArr = explode("-", $items[0]['barang_out']);

            $groupData = [
                "supplier_id"     => $items[0]['supplier_id'],
                "supplier_name"   => $items[0]['supplier_name'],
                "keterangan"      => $items[0]['keterangan'],
                "barang1_id"      => $items[0]['barang1_id'],
                "tipe_barang"     => $items[0]['tipe_barang'],
                "stock_dokumen"   => $stock_dokumen,
                "kode_barang_out" => $items[0]['kode_barang_out'],
                "barang_out"      => count($barangArr) == 0 ? "-" : $barangArr[0],
                "qty_out"         => $qtyTotal,
                "satuan_out"      => $items[0]['satuan_out'],
                "list_barang_masuk" => [],
            ];
            
            // Ambil list_barang_masuk sesuai stock_dokumen ini
            if (isset($listBarangMasukGrouped[$stock_dokumen])) {
                $groupData['list_barang_masuk'] = $listBarangMasukGrouped[$stock_dokumen];
            }
            
            $dataGroup[] = $groupData;
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
            ->where('barang_master_spesifikasi.id', $stock['barang2_id'])
            ->where('barang_master_spesifikasi.barang_master_id', $stock['barang1_id'])
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
