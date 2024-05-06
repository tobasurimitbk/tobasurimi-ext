<?php

namespace App\Models;

use CodeIgniter\Model;

class AdjusmentDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'adjusment_detail';
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

    public function getDetail($adjusmentID)
    {
        $stockModel = new StockModel();
        $adjusmentModel = new AdjusmentModel();
        $metaDataModel = new MetadataModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $kemasanModel = new KemasanModel();
        $satuanModel = new SatuansModel();
        $warehouseModel = new WarehousesModel();
        $stockDetail2Model = new StockDetail2Model();
        $supplierModel = new SupplierModel();

        $result = $this->asArray()->where('adjusment_id', $adjusmentID)->findAll();
        $adjusment = $adjusmentModel->find($adjusmentID);

        $response = array();
        foreach ($result as $r) {
            $spesifikasi_id = ($r['kemasan_id'] == 0) ? $r['barang2_id'] : $r['kemasan_id'];
            $bc = $metaDataModel->find($r['bc_id']);
            $bc_name = $bc == null ? "NON PABEAN" : $bc['value'];
            $barangMaster = $barangMasterModel->find($r['barang1_id']);
            $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($r['barang2_id']);
            $kemasan = $kemasanModel->find($r['kemasan_id']);
            $warehouse = $warehouseModel->find($r['warehouse_id']);

            $barang = $r['kemasan_id'] == 0 ? $barangMaster['barang_name'] . '-' . $barangMasterSpesifikasi['spesifikasi'] : $kemasan['name'];
            $kode_barang = $r['kemasan_id'] == 0 ? $barangMaster['kode_barang'] : $kemasan['kode'];
            $satuan = $r['kemasan_id'] == 0 ? $satuanModel->find($barangMasterSpesifikasi['satuan_1']) : $satuanModel->find($kemasan['satuan_id']);
            $no_aju = ($r['no_aju'] == "") ? "-" : $r['no_aju'];

            $stock = $stockModel->getStokMaster(
                $adjusment['company_id'],
                $r['warehouse_id'],
                $adjusment['divisi_id'],
                $r['tipe_barang'],
                $r['barang1_id'],
                $spesifikasi_id
            );

            $stockDetail2 = $stockDetail2Model->select('stock_details2.*, stock_details.sumber')
                ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
                ->where('stock_details2.stock_id', $stock['id'])
                ->where('bc_id', $r['bc_id'])
                ->where('no_aju', $no_aju)
                ->first();

            $supplier = $supplierModel->select('suppliers.*')
                ->join('penerimaan_barang', 'penerimaan_barang.supplier_id = suppliers.id')
                ->where('penerimaan_barang.no_penerimaan_barang', $stockDetail2['no_dokumen'])
                ->first();

            $stockDetail2GroubBy = $stockDetail2Model->getStockListDetail($stock['id'], $r['bc_id'], $r['no_aju']);

            $response[] = array(
                'id' => $stockDetail2['id'],
                'stock_id' => $stock['id'],
                'spesifikasi_id' => $spesifikasi_id,
                'sumber' => $stockDetail2['sumber'],
                'stock_dokumen' => $r['stock_dokumen'],
                'supplier_name' => $supplier != null ? strtoupper($supplier['name']) : "-",
                'bc_id' => $r['bc_id'],
                'no_aju' => $r['no_aju'],
                'dokumen_text' =>  $bc_name . " / " . $r['no_aju'],
                'barang' => $barang,
                'kode_barang' => $kode_barang,
                'type_barang' => $r['tipe_barang'],
                'type_barang_text' => strtoupper(str_replace('_', ' ', $r['tipe_barang'])),
                'satuan' => $satuan == null ? "" : $satuan['kode_satuan'],
                'warehouse_text' => $warehouse == null ? "" : $warehouse['warehouse_name'],
                'warehouse_id' => $warehouse == null ? 0 : $warehouse['id'],
                'stok_total' => $stockDetail2GroubBy == null ? 0 : $stockDetail2GroubBy['stok_total'],
                'type_adjusment' => $r['operasi'],
                'qty_adjusment' => $r['qty'],
            );
        }

        return $response;
    }
}
