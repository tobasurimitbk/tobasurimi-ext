<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanMutasiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_mutasi';
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
            'penerimaan_mutasi_no'                 => 'penerimaan_mutasi_no',
            'penerimaan_mutasi.multiple_mutasi_no' => 'penerimaan_mutasi.multiple_mutasi_no',
            'penerimaan_mutasi.tanggal'            => 'penerimaan_mutasi.tanggal',
            'penerimaan_mutasi.divisi_id'          => 'penerimaan_mutasi.divisi_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi.*,
        divisis.divisi AS divisi_penerima";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->where($condition)
            ->whereIn('penerimaan_mutasi.divisi_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['status'] || $addCondition['penerimaan_mutasi_no'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('penerimaan_mutasi.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['penerimaan_mutasi_no']) {
            $dataQry->like('penerimaan_mutasi_no', $addCondition['penerimaan_mutasi_no']);
        }

        if ($addCondition['multiple_mutasi_no']) {
            $dataQry->like('multiple_mutasi_no', $addCondition['multiple_mutasi_no']);
        }

        if ($addCondition['divisi_id'] || $addCondition['status'] || $addCondition['penerimaan_mutasi_no'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
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

    public function getListWarehouse($bcID, $divisiTujuanID)
    {
        $mutasiModel = new MutasiModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $warehouseModel = new WarehousesModel();

        $listMutasi = $mutasiModel
            ->select('mutasi.id, divisi_tujuan_id, warehouse_tujuan_id, SUM(qty) AS qty_mutasi')
            ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
            ->whereIn('divisi_tujuan_id', $divisiTujuanID)
            ->where('mutasi.deletedAt', null)
            ->where('mutasi_detail.deletedAt', null)
            ->where('mutasi.bc_id', $bcID)
            ->groupBy('mutasi_detail.mutasi_id')
            ->findAll();

        $warehouseResult = [];

        foreach ($listMutasi as $mutasi) {
            $penerimaanTotal = $penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $mutasi['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_id')
                ->findAll();

            if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $mutasi['qty_mutasi']) {
                $warehouseResult[] = $mutasi['warehouse_tujuan_id'];
            }
        }

        if (count($warehouseResult) == 0) {
            return [];
        } else {
            $warehouseList = $warehouseModel
                ->select('warehouses.id, warehouses.divisi_id, warehouses.warehouse_name, divisis.divisi')
                ->join('divisis', 'divisis.id = warehouses.divisi_id')
                ->whereIn('warehouses.id', $warehouseResult)
                ->where('warehouses.deletedAt', null)
                ->where('divisis.deletedAt', null)
                ->orderBy('divisis.divisi', "ASC")
                ->findAll();

            return $warehouseList;
        }
    }

    public function getListNomorMutasi($divisiId)
    {
        $mutasiModel = new MutasiModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();

        $listMutasi = $mutasiModel
            ->select('mutasi.id, mutasi.no_mutasi, SUM(qty) AS qty_mutasi')
            ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
            ->where('mutasi.divisi_tujuan_id', $divisiId)
            ->where('mutasi.deletedAt', null)
            ->where('mutasi_detail.deletedAt', null)
            ->groupBy('mutasi_detail.mutasi_id')
            ->orderBy('mutasi.no_mutasi', "ASC")
            ->findAll();

        $mutasiResult = [];

        foreach ($listMutasi as $mutasi) {
            $penerimaanTotal = $penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $mutasi['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_id')
                ->findAll();

            if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $mutasi['qty_mutasi']) {
                array_push($mutasiResult, $mutasi);
            }
        }

        return $mutasiResult;
    }

    public function getListBarangMutasi($mutasiArrID, $penerimaanMutasiID = null)
    {
        $stockModel = new StockModel();
        $kemasanModel = new KemasanModel();
        $satuanModel = new SatuansModel();
        $barangMasterModel = new BarangMasterModel();
        $mutasiDetailModel = new MutasiDetailModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $stockDetail2Model = new StockDetail2Model();
        $metaDataModel = new MetadataModel();
        $ppbkbModel = new PPBKBModel();
        $mutasiModel = new MutasiModel();

        $selectQry = "
            mutasi.no_mutasi, 
            mutasi_detail.*, 
            stock.tipe_barang, 
            metadata.value AS bc_name,
            warehouses.warehouse_name
        ";

        $mutasiDetailList = $mutasiDetailModel
            ->select($selectQry)
            ->join('mutasi', 'mutasi.id = mutasi_detail.mutasi_id', 'left')
            ->join('stock', 'stock.id = mutasi_detail.stock_id', 'left')
            ->join('metadata', 'metadata.id = mutasi_detail.bc_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi.warehouse_tujuan_id', 'left')
            ->whereIn('mutasi_id', $mutasiArrID)
            ->where('mutasi_detail.deletedAt', null)
            ->findAll();

        $barangResult = [];

        $bcMutasiId = $metaDataModel->getBCFirst('PPB-KB');

        foreach ($mutasiDetailList as $m) {
            $penerimaanTotal = $penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $m['mutasi_id'])
                ->where('mutasi_detail_id', $m['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_detail_id')
                ->findAll();

            $penerimaanTotalCurrent = $penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $m['mutasi_id'])
                ->where('mutasi_detail_id', $m['id'])
                ->where('penerimaan_mutasi_id', $penerimaanMutasiID)
                ->where('deletedAt', null)
                ->groupBy('mutasi_detail_id')
                ->findAll();

            $stock = $stockModel->find($m['stock_id']);

            $stockListDetail = $stockDetail2Model->getStockListDetail(
                $m['stock_id'],
                $m['bc_id'],
                $m['no_aju'],
                $m['stock_dokumen']
            );

            $ppbkb = $ppbkbModel->where('mutasi_id', $m['mutasi_id'])->first();

            $divisiWarehouseAsal = $mutasiModel->select('divisis.divisi, warehouses.warehouse_name')
                ->join('divisis', 'divisis.id = mutasi.divisi_asal_id', 'left')
                ->join('warehouses', 'warehouses.id = mutasi.warehouse_asal_id', 'left')
                ->where('mutasi.id', $m['mutasi_id'])
                ->first();

            if ($m['tipe_barang'] == 'kemasan') {
                // Kemasan
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                $barang = $kemasan['name'];
                $kodeBarang = $kemasan['kode'];
            } else {
                // Barang
                $barangSpesifikasi = $barangMasterModel
                    ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                    ->where('barang_master_spesifikasi.id', $stock['barang2_id'])
                    ->where('barang_master_spesifikasi.barang_master_id', $stock['barang1_id'])
                    ->first();
                $satuan = $satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                $barang = $barangSpesifikasi['barang'];
                $kodeBarang = $barangSpesifikasi['kode_barang'];
            }

            if ($penerimaanMutasiID == null) {
                if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $m['qty']) {
                    $qtyDiterima =  (count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima']);
                    array_push($barangResult, [
                        'mutasi_id' => $m['mutasi_id'],
                        'mutasi_detail_id' => $m['id'],
                        'stock_asal_id' => $m['stock_id'],
                        'bc_asal_id' => $m['bc_id'],
                        'no_aju_asal' => $m['no_aju'],
                        'stock_dokumen_asal' => $m['stock_dokumen'],
                        'stock_date_asal' => $stockListDetail != null ? date('d/m/Y', strtotime($stockListDetail['stock_date'])) : "-",
                        'bc_asal_name' => $m['bc_name'] == null ? "NON PABEAN" : $m['bc_name'],
                        'divisi_asal_name' => $divisiWarehouseAsal == null ? '-' : $divisiWarehouseAsal['divisi'],
                        'warehouse_asal_name' => $divisiWarehouseAsal == null ? '-' : $divisiWarehouseAsal['warehouse_name'],
                        // ---
                        'bc_mutasi_id' => $bcMutasiId['id'],
                        'bc_mutasi_name' => $bcMutasiId['value'],
                        'no_aju_mutasi' => $ppbkb['no_ppbkb'],
                        // -----
                        'qty' => $m['qty'],
                        'qty_diterima_all' => count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima'],
                        'qty_diterima_current' => count($penerimaanTotalCurrent) == 0 ? 0 : $penerimaanTotalCurrent[0]['qty_diterima'],
                        'qty_sisa' => $m['qty'] - $qtyDiterima,
                        'no_mutasi' => $m['no_mutasi'],
                        'tipe_barang' => strtoupper(str_replace('_', ' ', $m['tipe_barang'])),
                        'barang' => $barang,
                        'satuan' => $satuan,
                        'kode_barang' => $kodeBarang,
                        'warehouse_name' => $m['warehouse_name'],
                        'supplier_name' => $stockListDetail['supplier_name'],
                        'no_po' => $stockListDetail['no_po']
                    ]);
                }
            } else {
                if (count($penerimaanTotal) != 0 &&  count($penerimaanTotalCurrent) != 0) {
                    $qtyDiterima =  (count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima']);
                    array_push($barangResult, [
                        'mutasi_id' => $m['mutasi_id'],
                        'mutasi_detail_id' => $m['id'],
                        'stock_asal_id' => $m['stock_id'],
                        'bc_asal_id' => $m['bc_id'],
                        'no_aju_asal' => $m['no_aju'],
                        'stock_dokumen_asal' => $m['stock_dokumen'],
                        'stock_date_asal' => $stockListDetail != null ? date('d/m/Y', strtotime($stockListDetail['stock_date'])) : "-",
                        'bc_asal_name' => $m['bc_name'] == null ? "NON PABEAN" : $m['bc_name'],
                        'divisi_asal_name' => $divisiWarehouseAsal == null ? '-' : $divisiWarehouseAsal['divisi'],
                        'warehouse_asal_name' => $divisiWarehouseAsal == null ? '-' : $divisiWarehouseAsal['warehouse_name'],
                        // ---
                        'bc_mutasi_id' => $bcMutasiId['id'],
                        'bc_mutasi_name' => $bcMutasiId['value'],
                        'no_aju_mutasi' => $ppbkb['no_ppbkb'],
                        // -----                       
                        'qty' => $m['qty'],
                        'qty_diterima_all' => count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima'],
                        'qty_diterima_current' => count($penerimaanTotalCurrent) == 0 ? 0 : $penerimaanTotalCurrent[0]['qty_diterima'],
                        'qty_sisa' => $m['qty'] - $qtyDiterima,
                        'no_mutasi' => $m['no_mutasi'],
                        'tipe_barang' => strtoupper(str_replace('_', ' ', $m['tipe_barang'])),
                        'barang' => $barang,
                        'satuan' => $satuan,
                        'kode_barang' => $kodeBarang,
                        'warehouse_name' => $m['warehouse_name'],
                        'supplier_name' => $stockListDetail['supplier_name'],
                        'no_po' => $stockListDetail['no_po']
                    ]);
                }
            }
        }

        return $barangResult;
    }

    public function getMutasiNo($mutasiIDArr)
    {
        $mutasiModel = new MutasiModel();
        $result = $mutasiModel->whereIn('id', $mutasiIDArr)->findAll();
        $response = array();
        foreach ($result as $r) {
            array_push($response, $r['no_mutasi']);
        }
        return $response;
    }

    public function get_no($bln, $thn, $last_day, $divisiName, $divisiID)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('penerimaan_mutasi');
        $builder->select('penerimaan_mutasi_no');
        $builder->orderBy('penerimaan_mutasi_no', 'desc');
        $builder->where('penerimaan_mutasi.divisi_id', $divisiID);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('penerimaan_mutasi_no', $lastStr);
        $query = $builder->get();

        $kode = 'PMU/' . $divisiName;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['penerimaan_mutasi_no']);
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

    public function getPenerimaanBarangListReportPPBKB($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_ppbkb'      => 'ppbkb.no_ppbkb'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_mutasi.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi.penerimaan_mutasi_no,
        penerimaan_mutasi.multiple_mutasi_id,
        penerimaan_mutasi_detail.stock_asal_id as stock_id,
        penerimaan_mutasi.tanggal,
        penerimaan_mutasi_detail.mutasi_id,

        mutasi_detail.stock_id AS stock_id_asal,
        mutasi_detail.bc_id AS bc_id_asal,
        mutasi_detail.no_aju AS no_aju_asal,
        mutasi_detail.stock_dokumen AS stock_dokumen_asal,
        
        divisis.divisi AS divisi_penerima,
           
        
        mutasi_detail.qty AS qty,    
        penerimaan_mutasi_detail.qty AS jml_masuk,

        mutasi.tanggal AS tanggal_bc, 

        ppbkb.no_ppbkb AS no_ppbkb,    
        ppbkb.no_daftar AS no_daftar,    
          
        stock.tipe_barang,    
        stock.barang1_id,    
        stock.barang2_id,    
        stock.kemasan_id,    
          
        ";

        $penerimaanMutasi = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->join('penerimaan_mutasi_detail', 'penerimaan_mutasi_detail.penerimaan_mutasi_id = penerimaan_mutasi.id', 'left')
            ->join('mutasi', 'mutasi.id = penerimaan_mutasi_detail.mutasi_id', 'left')
            ->join('mutasi_detail', 'mutasi_detail.id = penerimaan_mutasi_detail.mutasi_detail_id', 'left')
            ->join('ppbkb', 'ppbkb.mutasi_id = penerimaan_mutasi_detail.mutasi_id', 'left')
            ->join('stock', 'stock.id = mutasi_detail.stock_id', 'left')

            ->where('penerimaan_mutasi.company_id', $addCondition['company_id'])

            ->where('penerimaan_mutasi.deletedAt', null)
            ->where('penerimaan_mutasi_detail.deletedAt', null)
            ->where('penerimaan_mutasi.status_posting', '1')

            ->orderBy($sort, $sortType);

        $totalData = $penerimaanMutasi->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasi->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanMutasi->where('mutasi.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanMutasi->where('mutasi.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasi->groupEnd();
        }

        $totalFilteredData = $penerimaanMutasi->countAllResults(false);
        $data = $penerimaanMutasi->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportPPBKBPDF($addCondition)
    {
        $availableSort = [
            'no_ppbkb'      => 'ppbkb.no_ppbkb'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_mutasi.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi.penerimaan_mutasi_no,
        penerimaan_mutasi.multiple_mutasi_id,
        penerimaan_mutasi_detail.stock_asal_id as stock_id,
        penerimaan_mutasi.tanggal,
        penerimaan_mutasi_detail.mutasi_id,

        mutasi_detail.stock_id AS stock_id_asal,
        mutasi_detail.bc_id AS bc_id_asal,
        mutasi_detail.no_aju AS no_aju_asal,
        mutasi_detail.stock_dokumen AS stock_dokumen_asal,
        
        divisis.divisi AS divisi_penerima,
           
        
        mutasi_detail.qty AS qty,    
        penerimaan_mutasi_detail.qty AS jml_masuk,

        mutasi.tanggal AS tanggal_bc, 

        ppbkb.no_ppbkb AS no_ppbkb,    
        ppbkb.no_daftar AS no_daftar,    
          
        stock.tipe_barang,    
        stock.barang1_id,    
        stock.barang2_id,    
        stock.kemasan_id,    
          
        ";

        $penerimaanMutasi = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->join('penerimaan_mutasi_detail', 'penerimaan_mutasi_detail.penerimaan_mutasi_id = penerimaan_mutasi.id', 'left')
            ->join('mutasi', 'mutasi.id = penerimaan_mutasi_detail.mutasi_id', 'left')
            ->join('mutasi_detail', 'mutasi_detail.id = penerimaan_mutasi_detail.mutasi_detail_id', 'left')
            ->join('ppbkb', 'ppbkb.mutasi_id = penerimaan_mutasi_detail.mutasi_id', 'left')
            ->join('stock', 'stock.id = mutasi_detail.stock_id', 'left')

            ->where('penerimaan_mutasi.company_id', $addCondition['company_id'])

            ->where('penerimaan_mutasi.deletedAt', null)
            ->where('penerimaan_mutasi_detail.deletedAt', null)
            ->where('penerimaan_mutasi.status_posting', '1')

            ->orderBy($sort, $sortType);

        $totalData = $penerimaanMutasi->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasi->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanMutasi->where('mutasi.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanMutasi->where('mutasi.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasi->groupEnd();
        }

        $totalFilteredData = $penerimaanMutasi->countAllResults(false);
        $data = $penerimaanMutasi->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }
}
