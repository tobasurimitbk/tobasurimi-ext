<?php

namespace App\Models;

use CodeIgniter\Model;

class ProsesRebusDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'proses_rebus_detail';
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

    public function getProsesRebusDetail($prosesRebusID)
    {
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $supplierModel = new SupplierModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();

        $result = array();
        $prosesRebusDetail = $this->asArray()->where('proses_rebus_id', $prosesRebusID)->findAll();

        foreach ($prosesRebusDetail as $m) {
            $stockList = $stockDetail2Model->getStockListDetail(
                $m['stock_rebus_id'],
                $m['bc_rebus_id'],
                $m['no_aju_rebus'],
                $m['stock_dokumen']
            );
            $stock = $stockModel->find($m['stock_rebus_id']);
            $stockOutput = $stockModel->find($m['stock_hasil_rebus_id']);

            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $barangMasterSpesifikasi == null ? null : $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster != null && $barangMasterSpesifikasi != null ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'] : null;
            } else {
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }

            if ($stockOutput['kemasan_id'] == 0) {
                $barangMaster = $barangMasterModel->find($stockOutput['barang1_id']);
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stockOutput['barang2_id']);
                $barangNameOutput = $barangMaster != null && $barangMasterSpesifikasi != null ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'] : '';
                $satuanOutput = $barangMasterSpesifikasi != null ? $satuanModel->find($barangMasterSpesifikasi['satuan_1']) : null;
                $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
            } else {
                $kemasan = $kemasanModel->find($stockOutput['kemasan_id']);
                $barangNameOutput = $kemasan['name'];
                $satuanOutput = $satuanModel->find($kemasan['id']);
                $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
            }


            $supplier = $supplierModel->select('suppliers.*')
                ->join('penerimaan_barang', 'penerimaan_barang.supplier_id = suppliers.id')
                ->where('penerimaan_barang.no_penerimaan_barang', $stockList['no_dokumen_1'])
                ->first();

            $rmPurchaseOrder = $rmPurchaseOrderModel->where('po_no', $m['stock_dokumen'])->where('company_id', $stockList['company_id'])->first();

            $stockList['qty'] = $m['qty_rebus'];
            $bcType = $metaDataModel->find($stockList['bc_id']);
            $stockList['no_aju'] =  $stockList['no_aju'] == "-" ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['satuan'] = $satuan == null ? '' : $satuan['kode_satuan'];
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['type_barang'] = $stock['tipe_barang'];
            $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['stok_total'] = ($stockList['stok_total']);
            $stockList['stock_date'] = $rmPurchaseOrder == null ? "" :  date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));
            $stockList['supplier_name'] = $supplier != null ? strtoupper($supplier['name']) : "-";
            $stockList['output'] = [
                'barang' => $barangNameOutput,
                'kode_satuan' => $satuanOutputName,
                'stock_id' => $stockOutput['id'],
                'qty' => $m['qty_hasil_rebus']
            ];

            array_push($result, $stockList);
        }

        return $result;
    }

    public function getProsesRebusDetail2($prosesRebusID)
    {
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $jasaVendorInModel = new JasaVendorInModel();
        $vendorModel = new VendorModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $supplierModel = new SupplierModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $stockRevampModel = new StockRevampModel();
        $stockRevampDetailModel = new StockRevampDetailModel();

        $result = array();
        $prosesRebusDetail = $this->asArray()->where('proses_rebus_id', $prosesRebusID)->where('deletedAt', null)->findAll();

        // var_dump($prosesRebusDetail);
        // die;

        foreach ($prosesRebusDetail as $m) {
            $stockList = $stockRevampDetailModel->where('id', $m['stock_detail_rebus_id'])->first();
            $stock = $stockRevampModel->find($m['stock_rebus_id']);
            $stockOutput = $stockRevampModel->select('stock_revamp.id')
                                                        ->where('stock_revamp.spesifikasi_id', $m['barang_out_spesifikasi_id'])
                                                        ->first();

                $dataBarangIn = $stockRevampModel->select('stock_revamp.spesifikasi_id, barang_master.barang_name')
                                                        ->where('stock_revamp.id', $m['stock_rebus_id'])
                                                        ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
                                                        ->first();          
                $barangMasterSpesifikasiIn = $barangMasterSpesifikasiModel->find($dataBarangIn['spesifikasi_id']);
                $satuanIn = $barangMasterSpesifikasiIn != null ? $satuanModel->find($barangMasterSpesifikasiIn['satuan_1']) : null;
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($m['barang_out_spesifikasi_id']);
                $barangMaster = $barangMasterModel->find($barangMasterSpesifikasi['barang_master_id']);
                $barangNameOutput = $barangMaster != null && $barangMasterSpesifikasi != null ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'] : '';
                $barangIdOutput = $barangMasterSpesifikasi['id'];
                $satuanOutput = $barangMasterSpesifikasi != null ? $satuanModel->find($barangMasterSpesifikasi['satuan_1']) : null;
                $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
            

            // === Supplier ===

            // === PO RM ===
            if ($m['po_id'] != null) {
                $rmPurchaseOrder = $rmPurchaseOrderModel
                    ->where('id', $stockList['po_id'])
                    ->first();

                $supplier = $supplierModel->select('suppliers.*')
                            ->where('id',  $rmPurchaseOrder['supplier_id'])
                            ->first();
            } else {
                $jasaVendorin = $jasaVendorInModel->where('id', $m['jasa_vendor_id'])->first();

                $vendor = $vendorModel->where('id', $jasaVendorin['vendor_id'])->first();
            }

            // === Susun hasil ===
            $bcType = $metaDataModel->find($stockList['bc_id']);
            $dataResult['bc_type'] = (!empty($bcType) && isset($bcType['value']))
            ? $bcType['value']
            : "NON PABEAN";
            $stockList['satuan'] = $satuanIn['kode_satuan'];
            $stockList['satuan_id'] = $satuanIn['id'];
            $stockList['barang'] = $dataBarangIn['barang_name'] . ' - ' . strtoupper($barangMasterSpesifikasiIn['spesifikasi']);
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['po_no'] =  $rmPurchaseOrder['po_no'] ?? NULL;
            $stockList['po_id'] =  $rmPurchaseOrder['id'] ?? NULL;
            $stockList['jasa_vendor_id'] =  $jasaVendorin['id'] ?? NULL;
            $stockList['no_penerimaan_surat_jalan'] =  $jasaVendorin['no_penerimaan_surat_jalan'] ?? NULL;
            $stockList['stock_detail_id'] = $stockList['id'];
            $stockList['reference_type'] = $stockList['reference_type'];
            $stockList['qty']         = round((float)$m['qty_rebus'], 2);
            $stockList['qty_bersih']  = round((float)$stockList['qty_bersih'], 2);
            $stockList['stock_total'] = round((float)$stockList['qty_diterima'], 2);
            $stockList['qty_kotor']   = round((float)$stockList['qty_diterima'] - (float)$stockList['qty_bersih'], 2);
            $stockList['stock_date'] = (isset($rmPurchaseOrder['po_date']) && $rmPurchaseOrder['po_date'])
                    ? date('d/m/Y', strtotime($rmPurchaseOrder['po_date']))
                    : NULL;
            $stockList['tanggal'] = (isset($jasaVendorin['tanggal']) && $jasaVendorin['tanggal'])
                    ? date('d/m/Y', strtotime($jasaVendorin['tanggal']))
                    : NULL;
            $stockList['supplier_name'] = (isset($supplier['name']) && $supplier['name'])
                ? strtoupper($supplier['name'])
                : NULL;

            $stockList['vendor_name'] = (isset($vendor['name']) && $vendor['name'])
                ? strtoupper($vendor['name'])
                : NULL;
            $stockList['total_penerimaan'] = $stockList['qty_bersih'] ?? 0;
            $stockList['output'] = [
                'barang' => $barangNameOutput,
                'barang_id' => $barangIdOutput,
                'spesifikasi_id' => $m['barang_out_spesifikasi_id'],
                'kode_satuan' => $satuanOutputName,
                'satuan_id' => $satuanOutput['id'],
                'stock_id' => !empty($stockOutput['id']) ? $stockOutput['id'] : 0,
                'qty' => round((float)$m['qty_hasil_rebus'], 2)
            ];

            array_push($result, $stockList);
        }

        return $result;
    }
}
