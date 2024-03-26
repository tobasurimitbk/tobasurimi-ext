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

        $result = array();
        $prosesRebusDetail = $this->asArray()->where('proses_rebus_id', $prosesRebusID)->findAll();

        foreach ($prosesRebusDetail as $m) {
            $stockList = $stockDetail2Model->getStockListDetail(
                $m['stock_rebus_id'],
                $m['bc_rebus_id'],
                $m['no_aju_rebus']
            );
            $stock = $stockModel->find($m['stock_rebus_id']);
            $stockOutput = $stockModel->find($m['stock_hasil_rebus_id']);

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

            if ($stockOutput['kemasan_id'] == 0) {
                $barangMaster = $barangMasterModel->find($stockOutput['barang1_id']);
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stockOutput['barang2_id']);
                $barangNameOutput = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
                $satuanOutput = $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
            } else {
                $kemasan = $kemasanModel->find($stockOutput['kemasan_id']);
                $barangNameOutput = $kemasan['name'];
                $satuanOutput = $satuanModel->find($kemasan['id']);
                $satuanOutputName = $satuanOutput == null ? "-" : $satuanOutput['kode_satuan'];
            }

            $stockList['qty'] = $m['qty_rebus'];
            $bcType = $metaDataModel->find($stockList['bc_id']);
            $stockList['no_aju'] =  $stockList['no_aju'] == "-" ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['satuan'] = $satuan['kode_satuan'];
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['type_barang'] = $stock['tipe_barang'];
            $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['stok_total'] = number_format($stockList['stok_total']);
            $stockList['stock_date'] = date('d/m/Y', strtotime($stockList['stock_date']));
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
}
