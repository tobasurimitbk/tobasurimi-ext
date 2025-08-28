<?php

namespace App\Models;

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


    public function getJasaVendorOutDetail2New($jasaVendorOutID)
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
                    $barangName = ($barangMaster && $barangMasterSpesifikasi)
                        ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi']
                        : '';
                    // $barangName = ($barangMaster && $barangMasterSpesifikasi)
                    //     ? $barangMaster['barang_name'] : '';
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
}
