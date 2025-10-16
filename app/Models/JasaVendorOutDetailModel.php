<?php

namespace App\Models;

use App\Controllers\Supplier\Supplier;
use CodeIgniter\Model;

class JasaVendorOutDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jasa_vendor_out_detail';
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

    public function getJasaVendorOutDetail($jasaVendorOutID)
    {
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $jasaVendorInModel = new JasaVendorInModel();

        $result = array();
        $jasaVendorOutDetail = $this->asArray()->where('jasa_vendor_out_id', $jasaVendorOutID)->findAll();

        foreach ($jasaVendorOutDetail as $m) {
            $stockList = $stockDetail2Model->getStockListDetail(
                $m['stock_out_id'],
                $m['bc_out_id'],
                $m['no_aju_out'],
                $m['stock_dokumen']
            );
            $stock = $stockModel->find($m['stock_out_id']);

            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $barangMasterSpesifikasi == null ? null : $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMasterSpesifikasi != null && $barangMaster != null ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'] : '';
            } else {
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }

            $rmPurchaseOrder = $rmPurchaseOrderModel->where('po_no', $m['stock_dokumen'])->where('company_id', $stockList['company_id'])->first();

            $resultNoJasaVendorIn = strstr($m['stock_dokumen'], '(', true);
            $noJasaVendorIn = trim($resultNoJasaVendorIn);
            $supplierName = $stockList['supplier_name'];
            $stockDate = $rmPurchaseOrder == null ? "" :  date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));

            $jasaVendorIn = $jasaVendorInModel
                ->select('jasa_vendor_in.*,vendors.name as nama_vendor')
                ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                ->where('jasa_vendor_in.company_id',  session()->get("login")->this_company_id)
                ->first();

            $stockList['qty'] = $m['qty'];
            $bcType = $metaDataModel->find($stockList['bc_id']);
            $stockList['no_aju'] =  $stockList['no_aju'] == "-" ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['satuan'] = $satuan == null ? '' : $satuan['kode_satuan'];
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['type_barang'] = $stock['tipe_barang'];
            $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['stok_total'] = ($stockList['stok_total']);
            $stockList['stock_date'] = $jasaVendorIn == null ? $stockDate :  date('d/m/Y', strtotime($jasaVendorIn['tanggal']));
            $stockList['supplier_name'] = $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'];

            array_push($result, $stockList);
        }

        return $result;
    }

    public function getJasaVendorOutDetail2($jasaVendorOutID)
    {
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $jasaVendorInModel = new JasaVendorInModel();

        $jasaVendorIn = null;
        $result = [];

        $jasaVendorOutDetail = $this->asArray()
            ->where('jasa_vendor_out_id', $jasaVendorOutID)
            ->findAll();

        foreach ($jasaVendorOutDetail as $m) {
            // $stockList = $stockDetail2Model->getStockListDetail(
            //     $m['stock_out_id'] ?? null,
            //     $m['bc_out_id'] ?? null,
            //     $m['no_aju_out'] ?? null,
            //     $m['stock_dokumen'] ?? null
            // );

            $stockList = $stockDetail2Model->getStockListDetailNew(
                $m['stock_out_id']
            );

            $stockDetail = $stockDetail2Model->find($m['stock_out_id']);

            if (!$stockList) {
                // Kalau stockList kosong, skip aja
                continue;
            }

            $stock = $stockModel->find($stockDetail['stock_id'] ?? null);

            $barangName = '';
            $satuan = null;

            if ($stock) {
                if (isset($stock['kemasan_id']) && $stock['kemasan_id'] == 0) {
                    $barangMaster = $barangMasterModel->find($stock['barang1_id'] ?? null);
                    $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['barang2_id'] ?? null);
                    $satuan = $barangMasterSpesifikasi ? $satuanModel->find($barangMasterSpesifikasi['satuan_1'] ?? null) : null;
                    $barangName = ($barangMaster && $barangMasterSpesifikasi)
                        ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi']
                        : '';
                } else {
                    $kemasan = $kemasanModel->find($stock['kemasan_id'] ?? null);
                    $satuan = $kemasan ? $satuanModel->find($kemasan['satuan_id'] ?? null) : null;
                    $barangName = $kemasan['name'] ?? '';
                }
            }

            $rmPurchaseOrder = null;
            if (isset($stockList['company_id'])) {
                $rmPurchaseOrder = $rmPurchaseOrderModel
                    ->where('po_no', $m['stock_dokumen'] ?? null)
                    ->where('company_id', $stockList['company_id'])
                    ->first();
            }

            $resultNoJasaVendorIn = strstr($m['stock_dokumen'] ?? '', '(', true);
            $noJasaVendorIn = trim($resultNoJasaVendorIn ?: '');
            $supplierName = $stockList['supplier_name'] ?? '';
            $stockDate = $rmPurchaseOrder
                ? date('d/m/Y', strtotime($rmPurchaseOrder['po_date']))
                : '';

            $jasaVendorIn = $jasaVendorInModel
                ->select('jasa_vendor_in.*, vendors.name as nama_vendor')
                ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                ->where('jasa_vendor_in.company_id', session()->get("login")->this_company_id)
                ->first();

            $stockList['qty'] = $m['qty'] ?? 0;
            $bcType = isset($stockList['bc_id']) ? $metaDataModel->find($stockList['bc_id']) : null;
            $stockList['no_aju'] = isset($stockList['no_aju']) && $stockList['no_aju'] !== "-" ? $stockList['no_aju'] : "-";
            $stockList['bc_type'] = $bcType['value'] ?? "NON PABEAN";
            $stockList['satuan'] = $satuan['kode_satuan'] ?? '';
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'] ?? null;
            $stockList['type_barang'] = $stock['tipe_barang'] ?? '';
            $stockList['type_barang_text'] = isset($stock['tipe_barang']) ? strtoupper(str_replace('_', ' ', $stock['tipe_barang'])) : '';
            $stockList['stok_total'] = $stockList['stok_total'] ?? 0;
            $stockList['stock_date'] = $jasaVendorIn
                ? date('d/m/Y', strtotime($jasaVendorIn['tanggal']))
                : $stockDate;
            $stockList['supplier_name'] = $jasaVendorIn
                ? $supplierName . ' / ' . ($jasaVendorIn['nama_vendor'] ?? '')
                : $supplierName;

            $result[] = $stockList;
        }

        // Grouping data
        if ($jasaVendorIn == null) {
            $grouped = [];

            foreach ($result as $item) {
                $key = ($item['stock_dokumen'] ?? '') . '|' . ($item['stock_date'] ?? '');

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'id' => [],
                        'bc_id' => $item['bc_id'] ?? '',
                        'stock_dokumen' => $item['stock_dokumen'] ?? '',
                        'sumber' => $item['sumber'] ?? '',
                        'supplier_name' => $item['supplier_name'] ?? '',
                        'stock_detail_id' => $item['stock_detail_id'] ?? '',
                        'no_aju' => $item['no_aju'] ?? '',
                        'stock_id' => $item['stock_id'] ?? '',
                        'stok_total' => 0,
                        'bc_type' => $item['bc_type'] ?? '',
                        'satuan' => $item['satuan'] ?? '',
                        'type_barang' => $item['type_barang'] ?? '',
                        'type_barang_text' => $item['type_barang_text'] ?? '',
                        'stock_date' => $item['stock_date'] ?? '',
                        'qty' => 0,
                        'barang' => [],
                    ];
                }

                $grouped[$key]['qty'] += floatval($item['qty'] ?? 0);
                $grouped[$key]['stok_total'] += floatval($item['stok_total'] ?? 0);
                if (!empty($item['stock_id'])) {
                    $grouped[$key]['id'][] = $item['stock_id'];
                }
                if (!in_array($item['barang'], $grouped[$key]['barang'])) {
                    $grouped[$key]['barang'][] = $item['barang'];
                }
            }

            $result = [];
            foreach ($grouped as $item) {
                $item['barang'] = implode(', ', $item['barang']);
                $item['id'] = encrypt(json_encode($item['id']));
                $result[] = $item;
            }
        } else {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['id'] = encrypt($result[$i]['stock_id']);
            }
        }

        return $result;
    }


    // public function getJasaVendorOutDetail2New($jasaVendorOutID)
    // {
    //     $stockDetail2Model = new StockDetail2Model();
    //     $stockModel = new StockModel();
    //     $barangMasterModel = new BarangMasterModel();
    //     $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
    //     $satuanModel = new SatuansModel();
    //     $kemasanModel = new KemasanModel();
    //     $metaDataModel = new MetadataModel();
    //     $rmPurchaseOrderModel = new RMPurchaseOrderModel();
    //     $jasaVendorInModel = new JasaVendorInModel();

    //     $result = [];

    //     $jasaVendorOutDetail = $this->asArray()
    //         ->where('jasa_vendor_out_id', $jasaVendorOutID)
    //         ->findAll();

    //     foreach ($jasaVendorOutDetail as $m) {

    //         $stockList = $stockDetail2Model->getStockListDetailNew(
    //             $m['stock_out_id']
    //         );

    //         $stockDetail = $stockDetail2Model->find($m['stock_out_id']);

    //         if (!$stockList) continue;

    //         $stock = $stockModel->find($stockDetail['stock_id'] ?? null);

    //         $barangName = '';
    //         $satuan = null;

    //         if ($stock) {
    //             if (isset($stock['kemasan_id']) && $stock['kemasan_id'] == 0) {
    //                 $barangMaster = $barangMasterModel->find($stock['barang1_id'] ?? null);
    //                 $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['barang2_id'] ?? null);
    //                 $satuan = $barangMasterSpesifikasi ? $satuanModel->find($barangMasterSpesifikasi['satuan_1'] ?? null) : null;
    //                 $barangName = ($barangMaster && $barangMasterSpesifikasi)
    //                     ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi']
    //                     : '';
    //                 // $barangName = ($barangMaster && $barangMasterSpesifikasi)
    //                 //     ? $barangMaster['barang_name'] : '';
    //             } else {
    //                 $kemasan = $kemasanModel->find($stock['kemasan_id'] ?? null);
    //                 $satuan = $kemasan ? $satuanModel->find($kemasan['satuan_id'] ?? null) : null;
    //                 $barangName = $kemasan['name'] ?? '';
    //             }
    //         }

    //         $rmPurchaseOrder = null;
    //         if (isset($stockList['company_id'])) {
    //             $rmPurchaseOrder = $rmPurchaseOrderModel
    //                 ->where('po_no', $m['stock_dokumen'] ?? null)
    //                 ->where('company_id', $stockList['company_id'])
    //                 ->first();
    //         }

    //         $resultNoJasaVendorIn = strstr($m['stock_dokumen'] ?? '', '(', true);
    //         $noJasaVendorIn = trim($resultNoJasaVendorIn ?: '');
    //         $supplierName = $stockList['supplier_name'] ?? '';
    //         $stockDate = $rmPurchaseOrder
    //             ? date('d/m/Y', strtotime($rmPurchaseOrder['po_date']))
    //             : '';

    //         $jasaVendorIn = $jasaVendorInModel
    //             ->select('jasa_vendor_in.*, vendors.name as nama_vendor')
    //             ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
    //             ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
    //             ->where('jasa_vendor_in.company_id', session()->get("login")->this_company_id)
    //             ->first();

    //         $stockList['qty'] = $m['qty'] ?? 0;
    //         $stockList['qty_kotor'] = $m['qty_kotor'] ?? 0;
    //         $bcType = isset($stockList['bc_id']) ? $metaDataModel->find($stockList['bc_id']) : null;
    //         $stockList['no_aju'] = isset($stockList['no_aju']) && $stockList['no_aju'] !== "-" ? $stockList['no_aju'] : "-";
    //         $stockList['bc_type'] = $bcType['value'] ?? "NON PABEAN";
    //         $stockList['satuan'] = $satuan['kode_satuan'] ?? '';
    //         $stockList['barang'] = strtoupper($barangName);
    //         $stockList['stock_id'] = $stockList['stock_id'] ?? null;
    //         $stockList['type_barang'] = $stock['tipe_barang'] ?? '';
    //         $stockList['type_barang_text'] = isset($stock['tipe_barang']) ? strtoupper(str_replace('_', ' ', $stock['tipe_barang'])) : '';
    //         $stockList['stok_total'] = $stockList['stok_total'] ?? 0;
    //         $stockList['stock_date'] = $jasaVendorIn
    //             ? date('d/m/Y', strtotime($jasaVendorIn['tanggal']))
    //             : $stockDate;
    //         $stockList['supplier_name'] = $jasaVendorIn
    //             ? $supplierName . ' / ' . ($jasaVendorIn['nama_vendor'] ?? '')
    //             : $supplierName;

    //         // id langsung encrypt tanpa grouping
    //         $stockList['id'] = $stockList['id'];

    //         $result[] = $stockList;
    //     }

    //     return $result;
    // }

    public function getJasaVendorOutDetailNew($jasaVendorOutID)
    {
        
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $metaDataModel = new MetadataModel();
        $supplierModel = new SupplierModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $stockRevampModel = new StockRevampModel();
        $stockRevampDetailModel = new StockRevampDetailModel();
        $jasaVendorInModel = new JasaVendorInModel();
        $prosesRebusModel = new ProsesRebusModel();
        $result = [];

        $jasaVendorOutDetail = $this->asArray()
            ->where('jasa_vendor_out_id', $jasaVendorOutID)
            ->where('deletedAt', null)
            ->findAll();



        foreach ($jasaVendorOutDetail as $m) {
            $stockList = $stockRevampDetailModel->where('id', $m['stock_out_detail_id'])->first();
            $stock = $stockRevampModel->where('id', $stockList['stock_id'])->first();
                // $stockOutput = $stockRevampModel->select('stock_revamp.id')
                //                                         ->where('stock_revamp.spesifikasi_id', $stock['spesifikasi_id'])
                //                                         ->first();

                $dataBarangIn = $stockRevampModel->select('stock_revamp.spesifikasi_id, barang_master.barang_name')
                                                        ->where('stock_revamp.id', $stockList['stock_id'])
                                                        ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
                                                        ->first();    

                $barangMasterSpesifikasiIn = $barangMasterSpesifikasiModel->find($dataBarangIn['spesifikasi_id']);
                $satuanIn = $barangMasterSpesifikasiIn != null ? $satuanModel->find($barangMasterSpesifikasiIn['satuan_1']) : null;
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($barangMasterSpesifikasiIn['id']);
                $barangMaster = $barangMasterModel->find($barangMasterSpesifikasi['barang_master_id']);
                $barangNameOutput = $barangMaster != null && $barangMasterSpesifikasi != null ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'] : '';
                $barangIdOutput = $barangMasterSpesifikasi['id'];
                $satuanOutput = $barangMasterSpesifikasi != null ? $satuanModel->find($barangMasterSpesifikasi['satuan_1']) : null;
                $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
    
         
                $rmPurchaseOrder = $rmPurchaseOrderModel
                    ->where('id', $stockList['po_id'])
                    ->where('deletedAt', null)
                    ->first();

            $resultNoJasaVendorIn = strstr($m['stock_dokumen'] ?? '', '(', true);
            $noJasaVendorIn = trim($resultNoJasaVendorIn ?: '');
            $supplierName = $supplierModel->select('name as supplier_name')
                            ->where('id', $rmPurchaseOrder['supplier_id'])
                            ->first();

            $jasaVendorIn = $jasaVendorInModel
                ->select('jasa_vendor_in.*, vendors.name as nama_vendor')
                ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                ->where('jasa_vendor_in.company_id', session()->get("login")->this_company_id)
                ->first();

                
               
                    if ($stockList['reference_type'] == "PROSES REBUS") {
                        $doc = $prosesRebusModel
                            ->select("no_rebus")
                            ->where("id", $stockList['reference_id'])
                            ->first();
                        $stock_dokumen = $doc ? $doc['no_rebus'] : null;
                    } else {
                        $doc = $rmPurchaseOrderModel
                            ->select("po_no")
                            ->where("id", $stockList['po_id'])
                            ->first();
                        $stock_dokumen = $doc ? $doc['po_no'] : null;
                    }

            $stockList['qty'] = $m['qty'] ?? 0;
            $bcType = isset($stockList['bc_id']) ? $metaDataModel->find($stockList['bc_id']) : null;
            $stockList['no_aju'] = isset($stockList['no_aju']) && $stockList['no_aju'] !== "-" ? $stockList['no_aju'] : "-";
            $stockList['bc_type'] = $bcType['value'] ?? "NON PABEAN";
            $stockList['satuan'] = $satuanOutput['kode_satuan'] ?? '';
            $stockList['sumber'] = $stockList['reference_type'] ?? '';
            $stockList['barang'] = strtoupper($barangNameOutput);
            $stockList['stock_dokumen'] = $stock_dokumen;
            $stockList['stock_id'] = $stockList['stock_id'] ?? null;
            $stockList['po_id'] = $stockList['po_id'] ?? null;
            $stockList['satuan_id'] = $m['satuan_id'] ?? null;
            $stockList['stock_detail_id'] = $stockList['id'] ?? null;
            $stockList['type_barang'] = $stock['tipe_barang'] ?? '';
            $stockList['type_barang_text'] = isset($stock['tipe_barang']) ? strtoupper(str_replace('_', ' ', $stock['tipe_barang'])) : '';
            $stockList['stok_total'] = $stockList['qty_diterima'] ?? 0;
            $stockList['stock_date'] =  $rmPurchaseOrder['po_date'];
            $stockList['keterangan'] =  $m['keterangan'];
            $stockList['supplier_name'] = $jasaVendorIn
                ? $supplierName['supplier_name'] . ' / ' . ($jasaVendorIn['nama_vendor'] ?? '')
                : $supplierName['supplier_name'];

            // id langsung encrypt tanpa grouping
            $stockList['id'] = $stockList['id'];

            $result[] = $stockList;
        }

        return $result;
    }

    public function getJasaVendorOutDetailForIndex($jasaVendorOutID)
    {
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $jasaVendorInModel = new JasaVendorInModel();

        $result = [];

        $jasaVendorOutDetail = $this->asArray()
            ->where('jasa_vendor_out_id', $jasaVendorOutID)
            ->findAll();

        foreach ($jasaVendorOutDetail as $m) {

            $stockList = $stockDetail2Model->getStockListDetailNew(
                $m['stock_out_id']
            );

            $stockDetail = $stockDetail2Model->find($m['stock_out_id']);

            if (!$stockList) continue;

            $stock = $stockModel->find($stockDetail['stock_id'] ?? null);

            $barangName = '';
            $satuan = null;

            if ($stock) {
                if (isset($stock['kemasan_id']) && $stock['kemasan_id'] == 0) {
                    $barangMaster = $barangMasterModel->find($stock['barang1_id'] ?? null);
                    $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['barang2_id'] ?? null);
                    $satuan = $barangMasterSpesifikasi ? $satuanModel->find($barangMasterSpesifikasi['satuan_1'] ?? null) : null;
                    // $barangName = ($barangMaster && $barangMasterSpesifikasi)
                    //     ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi']
                    //     : '';
                    $barangName = ($barangMaster && $barangMasterSpesifikasi)
                        ? $barangMaster['barang_name'] : '';
                } else {
                    $kemasan = $kemasanModel->find($stock['kemasan_id'] ?? null);
                    $satuan = $kemasan ? $satuanModel->find($kemasan['satuan_id'] ?? null) : null;
                    $barangName = $kemasan['name'] ?? '';
                }
            }

            $rmPurchaseOrder = null;
            if (isset($stockList['company_id'])) {
                $rmPurchaseOrder = $rmPurchaseOrderModel
                    ->where('po_no', $m['stock_dokumen'] ?? null)
                    ->where('company_id', $stockList['company_id'])
                    ->first();
            }

            $resultNoJasaVendorIn = strstr($m['stock_dokumen'] ?? '', '(', true);
            $noJasaVendorIn = trim($resultNoJasaVendorIn ?: '');
            $supplierName = $stockList['supplier_name'] ?? '';
            $stockDate = $rmPurchaseOrder
                ? date('d/m/Y', strtotime($rmPurchaseOrder['po_date']))
                : '';

            $jasaVendorIn = $jasaVendorInModel
                ->select('jasa_vendor_in.*, vendors.name as nama_vendor')
                ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                ->where('jasa_vendor_in.company_id', session()->get("login")->this_company_id)
                ->first();

            $stockList['qty'] = $m['qty'] ?? 0;
            $stockList['qty_kotor'] = $m['qty_kotor'] ?? 0;
            $bcType = isset($stockList['bc_id']) ? $metaDataModel->find($stockList['bc_id']) : null;
            $stockList['no_aju'] = isset($stockList['no_aju']) && $stockList['no_aju'] !== "-" ? $stockList['no_aju'] : "-";
            $stockList['bc_type'] = $bcType['value'] ?? "NON PABEAN";
            $stockList['satuan'] = $satuan['kode_satuan'] ?? '';
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'] ?? null;
            $stockList['type_barang'] = $stock['tipe_barang'] ?? '';
            $stockList['type_barang_text'] = isset($stock['tipe_barang']) ? strtoupper(str_replace('_', ' ', $stock['tipe_barang'])) : '';
            $stockList['stok_total'] = $stockList['stok_total'] ?? 0;
            $stockList['stock_date'] = $jasaVendorIn
                ? date('d/m/Y', strtotime($jasaVendorIn['tanggal']))
                : $stockDate;
            $stockList['supplier_name'] = $jasaVendorIn
                ? $supplierName . ' / ' . ($jasaVendorIn['nama_vendor'] ?? '')
                : $supplierName;

            // id langsung encrypt tanpa grouping
            $stockList['id'] = encrypt($stockList['stock_id']);

            $result[] = $stockList;
        }

        return $result;
    }

    public function getJasaVendorOutDetailForIndexNew($jasaVendorOutID)
    {
        $stockRevampModel = new StockRevampModel();
        $stockRevampDetailModel = new StockRevampDetailModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();

        $result = [];

        $jasaVendorOutDetail = $this->asArray()
            ->where('jasa_vendor_out_id', $jasaVendorOutID)
            ->where('deletedAt', null)
            ->findAll();

        foreach ($jasaVendorOutDetail as $m) {

            $stockList = $stockRevampDetailModel->select('stock_id')->where('id', $m['stock_out_detail_id'])->where('deletedAt', null)->first();

            if (!$stockList) continue;

            $stock = $stockRevampModel->select('barang_master_id, spesifikasi_id')->where('id', $stockList['stock_id'])->first();

            if ($stock) {
                    $barangMaster = $barangMasterModel->find($stock['barang_master_id'] ?? null);
                    $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['spesifikasi_id'] ?? null);
                    $barangName = ($barangMaster && $barangMasterSpesifikasi)
                        ? $barangMaster['barang_name'] : '';
            }

            $stockList['barang'] = strtoupper($barangName);
            $result[] = $stockList['barang'];
        }

        return $result;
    }
}
