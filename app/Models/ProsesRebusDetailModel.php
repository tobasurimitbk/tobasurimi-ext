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

            // === Barang input ===
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

            // === Barang output ===
            if ($stockOutput) {
                if ($stockOutput['kemasan_id'] == 0) {
                    $barangMaster = $barangMasterModel->find($stockOutput['barang1_id']);
                    $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stockOutput['barang2_id']);
                    $barangIdOutput = $barangMasterSpesifikasi['id'];
                    $barangNameOutput = $barangMaster != null && $barangMasterSpesifikasi != null ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'] : '';
                    $satuanOutput = $barangMasterSpesifikasi != null ? $satuanModel->find($barangMasterSpesifikasi['satuan_1']) : null;
                    $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
                } else {
                    $kemasan = $kemasanModel->find($stockOutput['kemasan_id']);
                    $barangNameOutput = $kemasan['name'];
                    $satuanOutput = $satuanModel->find($kemasan['id']);
                    $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
                }
            } else {
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($m['barang_out_id']);
                $barangMaster = $barangMasterModel->find($barangMasterSpesifikasi['barang_master_id']);
                $barangNameOutput = $barangMaster != null && $barangMasterSpesifikasi != null ? $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'] : '';
                $barangIdOutput = $barangMasterSpesifikasi['id'];
                $satuanOutput = $barangMasterSpesifikasi != null ? $satuanModel->find($barangMasterSpesifikasi['satuan_1']) : null;
                $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
            }

            // === Supplier ===
            $supplier = $supplierModel->select('suppliers.*')
                ->join('penerimaan_barang', 'penerimaan_barang.supplier_id = suppliers.id')
                ->where('penerimaan_barang.no_penerimaan_barang', $stockList['no_dokumen_1'])
                ->first();

            // === Total penerimaan by spesifikasi ===
            $totalPenerimaan = $supplierModel->db->table('penerimaan_barang')
                ->selectSum('penerimaan_barang_detail.qty', 'total_penerimaan')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
                ->where('penerimaan_barang.no_penerimaan_barang', $stockList['no_dokumen_1'])
                ->where('penerimaan_barang_detail.spesifikasi_id', $stock['barang2_id'])
                ->get()
                ->getRow('total_penerimaan');

            // === PO RM ===
            $rmPurchaseOrder = $rmPurchaseOrderModel
                ->where('po_no', $m['stock_dokumen'])
                ->where('company_id', $stockList['company_id'])
                ->first();

            // === Susun hasil ===
            $stockList['qty'] = $m['qty_rebus'];
            $bcType = $metaDataModel->find($stockList['bc_id']);
            $stockList['no_aju'] =  $stockList['no_aju'] == "-" ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['satuan'] = $satuan == null ? '' : $satuan['kode_satuan'];
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['type_barang'] = $stock['tipe_barang'];
            $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['stok_total'] = $stockList['stok_total'];
            $stockList['qty_kotor'] = $m['qty_kotor'];
            $stockList['stock_date'] = $rmPurchaseOrder == null ? "" : date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));
            $stockList['supplier_name'] = $supplier != null ? strtoupper($supplier['name']) : "-";
            $stockList['total_penerimaan'] = $totalPenerimaan ?? 0;
            $stockList['output'] = [
                'barang' => $barangNameOutput,
                'barang_id' => $barangIdOutput,
                'kode_satuan' => $satuanOutputName,
                'stock_id' => !empty($stockOutput['id']) ? $stockOutput['id'] : 0,
                'qty' => $m['qty_hasil_rebus']
            ];

            array_push($result, $stockList);
        }

        return $result;
    }
}
