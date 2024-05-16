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
        $supplierModel = new SupplierModel();

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
                $satuan = $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
            } else {
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }

            $supplier = $supplierModel->select('suppliers.*')
                ->join('penerimaan_barang', 'penerimaan_barang.supplier_id = suppliers.id')
                ->where('penerimaan_barang.no_penerimaan_barang', $stockList['no_dokumen_1'])
                ->first();

            $stockList['qty'] = $m['qty'];
            $bcType = $metaDataModel->find($stockList['bc_id']);
            $stockList['no_aju'] =  $stockList['no_aju'] == "-" ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['satuan'] = $satuan['kode_satuan'];
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['type_barang'] = $stock['tipe_barang'];
            $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['stok_total'] = ($stockList['stok_total']);
            $stockList['stock_date'] = date('d/m/Y', strtotime($stockList['stock_date']));
            $stockList['supplier_name'] = $supplier != null ? strtoupper($supplier['name']) : "-";

            array_push($result, $stockList);
        }

        return $result;
    }

    public function getStockListWithBCDoc($stockID)
    {

        $stockDetail2Model = new StockDetail2Model();

        $selectQry = '
            stock_details2.id,
            stock_details2.bc_id,
            stock_details2.stock_detail_id,
            stock_details2.no_aju,
            stock_details2.stock_id,
            stock_details2.stock_dokumen,
            stock_details2.no_dokumen AS no_dokumen_2,
            stock_details.no_dokumen AS no_dokumen_1,
            stock_details.stock_date,
            stock_details.sumber,
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details2.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details2.qty ELSE 0 END)) 
            AS stok_total,        
        ';


        $dataQry = $stockDetail2Model
            ->select($selectQry)
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
            ->where('stock_details2.stock_id', $stockID)
            ->groupBy('stock_details2.stock_dokumen')
            ->groupBy('stock_details2.bc_id')
            ->groupBy('stock_details2.no_aju')
            ->having('stok_total >', 0)
            ->orderBy('stock_details.createdAt', "ASC")
            ->findAll();

        return $dataQry;
    }
}
