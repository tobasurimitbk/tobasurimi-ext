<?php

namespace App\Models;

use CodeIgniter\Model;

class StuffingInternasionalDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stuffing_internasional_detail';
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

    public function getStuffingDetail($stuffingLokalID)
    {
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $barangMasterSalesModel = new BarangMasterSalesModel();
        $metaDataModel = new MetadataModel();
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();

        $result = array();
        $stuffingLokalDetail = $this->asArray()
            ->select('stuffing_internasional_detail.*, stuffing_internasional.sales_order_export_id')
            ->join('stuffing_internasional', 'stuffing_internasional.id = stuffing_internasional_detail.stuffing_internasional_id', 'left')
            ->where('stuffing_internasional_id', $stuffingLokalID)
            ->findAll();

        foreach ($stuffingLokalDetail as $m) {
            $stockList = $stockDetail2Model->getStockListDetail(
                $m['stock_id_warehouse'],
                $m['bc_id_warehouse'],
                $m['no_aju_warehouse']
            );
            $stock = $stockModel->find($m['stock_id_warehouse']);

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

            $stockOutput = $salesOrderExportDetailModel
                ->select('barang_master_sales.*, sales_order_detail_export.qty')
                ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail_export.barang_id', 'left')
                ->where('barang_master_sales.id', $m['barang_id_order'])
                ->where('sales_order_detail_export.sales_order_export_id', $m['sales_order_export_id'])
                ->first();

            if ($stockOutput) {
                $barangNameOutput = $stockOutput['barang_name'];
                $barangKodeOutput = $stockOutput['id'];
                $barangQtyOutput = $stockOutput['qty'];
            } else {
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }

            $stockList['id_stuffing_detail'] = $m['id'];
            $stockList['divisi_id'] = $m['divisi_id'];
            $stockList['warehouse_id'] = $m['warehouse_id'];
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
            $stockList['output'] = [
                'id_barang' => $barangKodeOutput,
                'barang' => $barangNameOutput,
                'qty' => $barangQtyOutput
            ];
            array_push($result, $stockList);
        }
        return $result;
    }
}
