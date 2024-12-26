<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderLainDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_order_lain_detail';
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

    public function detail($salesOrderLainId)
    {
        $salesOrderLainModel = new SalesOrderLainModel();
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();

        $result = array();
        $salesOrderLain = $salesOrderLainModel->select('sales_order_lain.*,divisis.divisi,warehouses.warehouse_name,metadata.value as valas_name')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->join('metadata', 'metadata.id = customers.currency', 'left')
            ->where('sales_order_lain.id', $salesOrderLainId)
            ->first();

        $salesOrderLainList = $this->asArray()->where('sales_order_lain_id', $salesOrderLainId)->findAll();

        foreach ($salesOrderLainList as $s) {
            $stockList = $stockDetail2Model->getStockListDetail(
                $s['stock_id'],
                $s['bc_id'],
                $s['no_aju'],
                $s['stock_dokumen']
            );
            $stock = $stockModel->find($s['stock_id']);

            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $satuanId = $barangMasterSpesifikasi['satuan_1'];
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
                $barangMasterName = $barangMaster['barang_name'];
                $kodeBarang = $barangMaster['kode_barang'];
            } else {
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id']);
                $satuanId = $kemasan['satuan_id'];
                $barangName = $kemasan['name'];
                $barangMasterName = $kemasan['name'];
                $kodeBarang = $kemasan['kode'];
            }

            $bcType = $metaDataModel->find($stockList['bc_id']);
            $satuanOrder = $satuanModel->find($s['satuan_order_id']);

            $stockList['stock_dokumen'] =  $stockList['stock_dokumen'] == "-" ? "-" : $stockList['stock_dokumen'];
            $stockList['no_aju'] =  $stockList['no_aju'] == "-" ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['satuan'] = $satuan['kode_satuan'];
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_date'] = date('d/m/Y', strtotime($stockList['stock_date']));
            $stockList['type_barang'] = $stock['tipe_barang'];
            $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['stok_total'] = ($stockList['stok_total']);
            $stockList['satuan_id'] = $satuanId;
            // TAMBAHAN
            $stockList['kode_barang'] = $kodeBarang;
            $stockList['barang_master_name'] = $barangMasterName;
            $stockList['satuan_order_id'] = $s['satuan_order_id'];
            $stockList['satuan_order_text'] = $satuanOrder['kode_satuan'];
            $stockList['qty_order'] = $s['qty_order'];
            $stockList['qty_konversi'] = $s['qty_konversi'];
            $stockList['harga_satuan'] = $s['harga_satuan'];
            $stockList['potongan_harga'] = $s['potongan_harga'];
            $stockList['biaya_tambahan'] = $s['biaya_tambahan'];
            $stockList['total_harga'] = $s['total_harga'];
            $stockList['divisi'] = $salesOrderLain['divisi'];
            $stockList['warehouse_name'] = $salesOrderLain['warehouse_name'];
            $stockList['valas_name'] = $salesOrderLain['valas_name'] == null ? "IDR" :  $salesOrderLain['valas_name'];
            $stockList['barang1_id'] = $stock['barang1_id'];
            $stockList['kemasan_id'] = $stock['kemasan_id'];

            array_push($result, $stockList);
        }

        return $result;
    }
}
