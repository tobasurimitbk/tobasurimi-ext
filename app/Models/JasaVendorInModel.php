<?php

namespace App\Models;

use CodeIgniter\Model;

class JasaVendorInModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jasa_vendor_in';
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


    public function getList($condition, $conditionArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_penerimaan_surat_jalan' => 'no_penerimaan_surat_jalan',
            'jasa_vendor_in.createdAt' => 'jasa_vendor_in.createdAt',
            'jasa_vendor_in.divisi_id' => 'jasa_vendor_in.divisi_id',
            'jasa_vendor_in.warehouse_id' => 'jasa_vendor_in.warehouse_id',
            'vendor_id' => 'vendor_id',
            'no_surat_jalan_vendor' => 'no_surat_jalan_vendor',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "jasa_vendor_in.*,
        divisis.divisi,
        warehouses.warehouse_name,
        vendors.name as vendor_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = jasa_vendor_in.warehouse_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where($condition)
            ->whereIn('jasa_vendor_in.divisi_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_penerimaan_surat_jalan'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('jasa_vendor_in.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->like('jasa_vendor_in.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['no_penerimaan_surat_jalan']) {
            $dataQry->like('no_penerimaan_surat_jalan', $addCondition['no_penerimaan_surat_jalan']);
        }

        if ($addCondition['start_date']) {
            $dataQry->where('tanggal >=', $addCondition['start_date']);
        }

        if ($addCondition['end_date']) {
            $dataQry->where('tanggal <=', $addCondition['end_date']);
        }

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_penerimaan_surat_jalan'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }


    public function listBarang($jasaVendorOutArr, $jasaVendorInID)
    {
        $jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $stockModel = new StockModel();
        $metaDataModel = new MetadataModel();
        $stockDetail2Model = new StockDetail2Model();
        $supplierModel = new SupplierModel();

        $jasaVendorOutData = $jasaVendorOutDetailModel->whereIn('jasa_vendor_out_id', $jasaVendorOutArr)->where('deletedAt', null)->findAll();
        $result = array();

        foreach ($jasaVendorOutData as $j) {
            $stockBarangOut = $stockModel->find($j['stock_out_id']);
            $stockListOutDetail = $stockDetail2Model->getStockListDetail(
                $j['stock_out_id'],
                $j['bc_out_id'],
                $j['no_aju_out']
            );

            $barangOut = self::getDetailBarang($stockBarangOut);

            $bc = $metaDataModel->find($j['bc_out_id']);
            $jasaVendorInDetail = $jasaVendorInDetailModel
                ->where('jasa_vendor_in_id', $jasaVendorInID)
                ->where('jasa_vendor_out_id', $j['jasa_vendor_out_id'])
                ->where('jasa_vendor_out_detail_id', $j['id'])
                ->findAll();

            $noLpb = $stockListOutDetail != null ? $stockListOutDetail['no_dokumen_1'] : '';

            $supplier = $supplierModel->select('suppliers.*')
                ->join('penerimaan_barang', 'penerimaan_barang.supplier_id = suppliers.id')
                ->where('penerimaan_barang.no_penerimaan_barang', $noLpb)
                ->first();

            if ($jasaVendorInID == null) {
                $result[] = [
                    'jasa_vendor_out_detail_id' => $j['id'],
                    'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                    'stock_out_id' => $j['stock_out_id'],
                    'stock_date' => $stockListOutDetail != null ? date('d/m/Y', strtotime($stockListOutDetail['stock_date'])) : "-",
                    'tipe_barang' => $stockBarangOut != null ? strtoupper(str_replace('_', ' ', $stockBarangOut['tipe_barang'])) : "",
                    'supplier_name' => $supplier != null ? strtoupper($supplier['name']) : '-',
                    'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                    'bc_id' => $j['bc_out_id'],
                    'no_aju' => $j['no_aju_out'],
                    'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                    'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                    'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                    'qty_out' => $j['qty'],
                    'list_barang_masuk' => []
                ];
            } else {
                if (count($jasaVendorInDetail) != 0) {
                    $result[] = [
                        'jasa_vendor_out_detail_id' => $j['id'],
                        'jasa_vendor_out_id' => $j['jasa_vendor_out_id'],
                        'stock_out_id' => $j['stock_out_id'],
                        'stock_date' => $stockListOutDetail != null ? date('d/m/Y', strtotime($stockListOutDetail['stock_date'])) : "-",
                        'tipe_barang' => $stockBarangOut != null ? strtoupper(str_replace('_', ' ', $stockBarangOut['tipe_barang'])) : "",
                        'supplier_name' => $supplier != null ? strtoupper($supplier['name']) : '-',
                        'bc_name' => $bc != null ? $bc['value'] : 'NON PABEAN',
                        'bc_id' => $j['bc_out_id'],
                        'no_aju' => $j['no_aju_out'],
                        'kode_barang_out' => $barangOut != null ? $barangOut['kode_barang'] : "-",
                        'barang_out' => $barangOut != null ? strtoupper($barangOut['barang']) : "-",
                        'satuan_out' => $barangOut != null ? $barangOut['kode_satuan'] : "-",
                        'qty_out' => $j['qty'],
                        'list_barang_masuk' => []
                    ];

                    for ($i = 0; $i < count($result); $i++) {
                        foreach ($jasaVendorInDetail as $k) {
                            if ($result[$i]['jasa_vendor_out_detail_id'] == $k['jasa_vendor_out_detail_id']) {
                                $stockBarangIn = $stockModel->find($k['stock_in_id']);
                                $barangIn = self::getDetailBarang($stockBarangIn);
                                array_push($result[$i]['list_barang_masuk'], [
                                    'barang_name_in' => $barangIn != null ? strtoupper($barangIn['barang']) : '-',
                                    'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                                    'kode_barang_in' => $barangIn != null ? strtoupper($barangIn['kode_barang']) : '-',
                                    'kode_satuan_in' => $barangIn != null ? $barangIn['kode_satuan'] : '-',
                                    'qty_bersih' => $k['qty_bersih'],
                                    'qty_kotor' => $k['qty_kotor'],
                                    'stock_in_id' => $k['stock_in_id'],
                                    'stock_out_id' => $result[$i]['stock_out_id']
                                ]);
                            }
                        }
                    }
                }
            }
        }




        return $result;
    }

    static function getDetailBarang($stock)
    {
        $barangMasterModel = new BarangMasterModel();

        $barang = $barangMasterModel
            ->select("CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, satuans.kode_satuan, barang_master.kode_barang")
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('barang_master_spesifikasi.id', $stock['barang2_id'])
            ->where('barang_master_spesifikasi.barang_master_id', $stock['barang1_id'])
            ->first();

        return $barang;
    }

    public function getJasaVendorOutNo($jasaVendorOutID)
    {
        $jasaVendorOutModel = new JasaVendorOutModel();
        $result = array();
        $dataQry = $jasaVendorOutModel->whereIn('id', $jasaVendorOutID)->where('deletedAt', null)->findAll();

        foreach ($dataQry as $d) {
            array_push($result, $d['no_surat_jalan']);
        }

        return $result;
    }

    public function get_no($bln, $thn, $last_day, $warehouseKode, $warehouse_id)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('jasa_vendor_in');
        $builder->select('no_penerimaan_surat_jalan');
        $builder->orderBy('no_penerimaan_surat_jalan', 'desc');
        $builder->where('jasa_vendor_in.warehouse_id', $warehouse_id);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_penerimaan_surat_jalan', $lastStr);
        $query = $builder->get();

        $kode = 'TOBA-VBM/' . $warehouseKode;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_penerimaan_surat_jalan']);
                $number = intval($explode[2]);

                if ($number > $lastPenerimaan) {
                    $lastPenerimaan = $number;
                }
            }
            $lastPenerimaan++;
        }

        $formattedLastPenerimaan = sprintf("%02d", $lastPenerimaan);
        $generatedNo = $kode . '/' . $formattedLastPenerimaan . '/' . $lastStr;

        return $generatedNo;
    }
}
