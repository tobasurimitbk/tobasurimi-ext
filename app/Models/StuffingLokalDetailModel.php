<?php

namespace App\Models;

use CodeIgniter\Model;

class StuffingLokalDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stuffing_lokal_detail';
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
        $stockRevampModel = new StockRevampModel();
        $stockRevampDetailModel = new StockRevampDetailModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $barangMasterSalesModel = new BarangMasterSalesModel();
        $barangMasterSalesModel = new BarangMasterSalesModel();
        $metaDataModel = new MetadataModel();
        $salesOrderDetailModel = new SalesOrderDetailModel();

        $result = array();
        $stuffingLokalDetail = $this->asArray()
            ->select('stuffing_lokal_detail.*, stuffing_lokal.sales_order_id')
            ->join('stuffing_lokal', 'stuffing_lokal.id = stuffing_lokal_detail.stuffing_lokal_id', 'left')
            ->where('stuffing_lokal_id', $stuffingLokalID)
            ->findAll();

        foreach ($stuffingLokalDetail as $m) {
            $stockList = $stockRevampDetailModel->getStockListWithAddConditionForStuffingById(
                $m['stock_detail_id'],
            );

            $stock = $stockRevampModel->find($m['stock_id_warehouse']);

                $barangMaster = $barangMasterModel->find($stock['barang_master_id']);
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['spesifikasi_id']);
                $satuan = $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
           
            $stockOutput = $salesOrderDetailModel
                ->select('barang_master_sales.*, sales_order_detail.qty')
                ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang', 'left')
                ->where('barang_master_sales.id', $m['barang_id_order'])
                ->where('sales_order_detail.id_sales_order', $m['sales_order_id'])
                ->first();
           
                $barangNameOutput = $stockOutput['barang_name'];
                $barangKodeOutput = $stockOutput['id'];
                $barangQtyOutput = $stockOutput['qty'];
            
            $stockList['id_stuffing_detail'] = $m['id'];
            $stockList['divisi_id'] = $m['divisi_id'];
            $stockList['warehouse_id'] = $m['warehouse_id'];
            $stockList['qty'] = $m['qty'];
            $bcType = isset($stockList['bc_id']) ? $metaDataModel->find($stockList['bc_id']) : null;
            $stockList['no_aju'] =  !isset($stockList['no_aju']) ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['satuan'] = $satuan['kode_satuan'];
            $stockList['barang'] = strtoupper($barangName);
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['stock_detail_id'] = $m['stock_detail_id'];
            $stockList['type_barang'] = ucwords(str_replace('_', ' ', $stockList['type_barang']));;
            // $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['stok_total'] = $stockList['stok_total_diterima'];
            $stockList['stock_date'] = date('d/m/Y', strtotime($stockList['createdAt']));
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
