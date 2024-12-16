<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiGlobalDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'mutasi_global_detail';
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

    public function getMutasiDetail($mutasiID)
    {
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $supplierModel = new SupplierModel();
        $bc27Model = new BC27Model();

        $result = array();
        $bcMutasiId = $metaDataModel->getBCFirst('BC 2.7');
        $mutasiDetail = $this->asArray()->where('mutasi_global_id', $mutasiID)->findAll();
        $bc27 = $bc27Model->where('mutasi_global_id', $mutasiID)->first();
        foreach ($mutasiDetail as $m) {
            $stockList = $stockDetail2Model->getStockListDetail(
                $m['stock_id'],
                $m['bc_id'],
                $m['no_aju'],
                $m['stock_dokumen']
            );
            $stock = $stockModel->find($m['stock_id']);


            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
                $kodeBarang = $barangMaster['kode_barang'];
            } else {
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
                $kodeBarang = $kemasan['kode'];
            }

            $supplier = $supplierModel->select('suppliers.*')
                ->join('penerimaan_barang', 'penerimaan_barang.supplier_id = suppliers.id')
                ->where('penerimaan_barang.no_penerimaan_barang', $stockList['no_dokumen_1'])
                ->first();


            $bcType = $metaDataModel->find($stockList['bc_id']);

            $stockList['mutasi_global_id'] = $m['mutasi_global_id'];
            $stockList['mutasi_global_detail_id'] = $m['id'];
            $stockList['qty'] = $m['qty'];
            $stockList['no_aju'] =  $stockList['no_aju'] == "-" ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['stock_date'] = $stockList != null ? date('d/m/Y', strtotime($stockList['stock_date'])) : "-";
            $stockList['satuan'] = $satuan['kode_satuan'];
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['type_barang'] = $stock['tipe_barang'];
            $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['supplier_name'] = $supplier != null ? strtoupper($supplier['name']) : "-";
            $stockList['stok_total'] = $stockList['stok_total'];
            $stockList['kode_barang'] = $kodeBarang;
            $stockList['satuan_id'] = $satuan['id'];
            $stockList['satuan'] = $satuan['kode_satuan'];
            $stockList['total_harga'] = $stockList['harga_umum'] + $stockList['harga_harian'] + $stockList['harga_bulanan'];
            // ------------
            $stockList['bc_mutasi_id'] = $bcMutasiId['id'];
            $stockList['bc_mutasi_name'] = $bcMutasiId['value'];
            $stockList['no_aju_mutasi'] = $bc27['no_aju'];

            array_push($result, $stockList);
        }


        return $result;
    }
}
