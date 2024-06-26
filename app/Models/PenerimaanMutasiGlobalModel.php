<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanMutasiGlobalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_mutasi_global';
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
            'penerimaan_mutasi_no'                           => 'penerimaan_mutasi_no',
            'penerimaan_mutasi_global.multiple_mutasi_no'    => 'penerimaan_mutasi_global.multiple_no_mutasi',
            'penerimaan_mutasi_global.tanggal'               => 'penerimaan_mutasi_global.tanggal',
            'penerimaan_mutasi_global.divisi_penerima_id'    => 'penerimaan_mutasi_global.divisi_penerima_id',
            'penerimaan_mutasi_global.warehouse_penerima_id' => 'penerimaan_mutasi_global.warehouse_penerima_id',
            'penerimaan_mutasi_global.company_pengirim_id'   => 'penerimaan_mutasi_global.company_pengirim_id'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi_global.*,
        divisis.divisi AS divisi_penerima,
        warehouses.warehouse_name AS warehouse_penerima,
        companies.company AS company_pengirim";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id', 'left')
            ->join('companies', 'companies.id = penerimaan_mutasi_global.company_pengirim_id', 'left')
            ->where($condition)
            ->whereIn('penerimaan_mutasi_global.divisi_penerima_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['company_pengirim_id'] || $addCondition['status'] || $addCondition['penerimaan_mutasi_no'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
        }

        if ($addCondition['company_pengirim_id']) {
            $dataQry->where('penerimaan_mutasi_global.company_pengirim_id', $addCondition['company_pengirim_id']);
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
            $dataQry->like('multiple_no_mutasi', $addCondition['multiple_mutasi_no']);
        }

        if ($addCondition['company_pengirim_id'] || $addCondition['status'] || $addCondition['penerimaan_mutasi_no'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
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


    public function getListBarangMutasi($mutasiGlobalArrID, $penerimaanMutasiGlobalID = null)
    {
        $stockModel = new StockModel();
        $kemasanModel = new KemasanModel();
        $satuanModel = new SatuansModel();
        $barangMasterModel = new BarangMasterModel();
        $mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();
        $stockDetail2Model = new StockDetail2Model();
        $metaDataModel = new MetadataModel();
        $bc27Model = new BC27Model();

        $selectQry = "
            mutasi_global.no_mutasi, 
            mutasi_global_detail.*, 
            stock.tipe_barang, 
            metadata.value AS bc_name,
            warehouses.warehouse_name AS warehouse_asal_name,
            divisis.divisi AS divisi_asal_name
        ";

        $mutasiDetailList = $mutasiGlobalDetailModel
            ->select($selectQry)
            ->join('mutasi_global', 'mutasi_global.id = mutasi_global_detail.mutasi_global_id', 'left')
            ->join('stock', 'stock.id = mutasi_global_detail.stock_id', 'left')
            ->join('metadata', 'metadata.id = mutasi_global_detail.bc_id', 'left')
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id', 'left')
            ->whereIn('mutasi_global_id', $mutasiGlobalArrID)
            ->where('mutasi_global_detail.deletedAt', null)
            ->findAll();

        $barangResult = [];

        $bcMutasiId = $metaDataModel->getBCFirst('BC 2.7');

        foreach ($mutasiDetailList as $m) {
            // PENERIMAAN TOTAL
            $penerimaanTotal = $penerimaanMutasiGlobalDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_global_id', $m['mutasi_global_id'])
                ->where('mutasi_global_detail_id', $m['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_global_detail_id')
                ->findAll();

            // PENERIMAAN GLOBAL BY PENERIMAAN MUTASI ID
            $penerimaanTotalCurrent = $penerimaanMutasiGlobalDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_global_id', $m['mutasi_global_id'])
                ->where('mutasi_global_detail_id', $m['id'])
                ->where('penerimaan_mutasi_global_id', $penerimaanMutasiGlobalID)
                ->where('deletedAt', null)
                ->groupBy('mutasi_global_detail_id')
                ->findAll();

            $stock = $stockModel->find($m['stock_id']);

            $stockListDetailAsal = $stockDetail2Model->getStockListDetail(
                $m['stock_id'],
                $m['bc_id'],
                $m['no_aju'],
                $m['stock_dokumen']
            );

            $bc27 = $bc27Model->where('mutasi_global_id', $m['mutasi_global_id'])->first();

            if ($m['tipe_barang'] == 'kemasan') {
                // Kemasan
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                $barang = $kemasan['name'];
                $kodeBarang = $kemasan['kode'];
            } else {
                // Barang
                $barangSpesifikasi = $barangMasterModel
                    ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                    ->where('barang_master_spesifikasi.id', $stock['barang2_id'])
                    ->where('barang_master_spesifikasi.barang_master_id', $stock['barang1_id'])
                    ->first();
                $satuan = $satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                $barang = $barangSpesifikasi['barang'];
                $kodeBarang = $barangSpesifikasi['kode_barang'];
            }

            if ($penerimaanMutasiGlobalID == null) {
                if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $m['qty']) {
                    $qtyDiterima =  (count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima']);
                    array_push($barangResult, [
                        'mutasi_global_id' => $m['mutasi_global_id'],
                        'mutasi_global_detail_id' => $m['id'],
                        'stock_asal_id' => $m['stock_id'],
                        'bc_asal_id' => $m['bc_id'],
                        'no_aju_asal' => $m['no_aju'],
                        'stock_dokumen_asal' => $m['stock_dokumen'],
                        'stock_date_asal' => $stockListDetailAsal != null ? date('d/m/Y', strtotime($stockListDetailAsal['stock_date'])) : "-",
                        'bc_asal_name' => $m['bc_name'] == null ? "NON PABEAN" : $m['bc_name'],
                        'divisi_asal_name' => $m['divisi_asal_name'],
                        'warehouse_asal_name' => $m['warehouse_asal_name'],
                        // ---
                        'stock_mutasi_id' => '',
                        'bc_mutasi_id' => $bcMutasiId['id'],
                        'bc_mutasi_name' => $bcMutasiId['value'],
                        'no_aju_mutasi' => $bc27['no_aju'],
                        // -----
                        'qty' => $m['qty'],
                        'qty_diterima_all' => count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima'],
                        'qty_diterima_current' => count($penerimaanTotalCurrent) == 0 ? 0 : $penerimaanTotalCurrent[0]['qty_diterima'],
                        'qty_sisa' => $m['qty'] - $qtyDiterima,
                        'no_mutasi' => $m['no_mutasi'],
                        'tipe_barang_text' => strtoupper(str_replace('_', ' ', $m['tipe_barang'])),
                        'barang' => $barang,
                        'satuan' => $satuan,
                        'kode_barang' => $kodeBarang,
                        'supplier_name' => $stockListDetailAsal['supplier_name'],
                        'no_po' => $stockListDetailAsal['no_po'],
                        // ---
                        'kode_barang_diterima' => "-",
                        'barang_diterima' => "-",
                        'satuan_diterima' => "-",
                        'tipe_barang' => $m['tipe_barang']
                    ]);
                }
            } else {
                if (count($penerimaanTotal) != 0 &&  count($penerimaanTotalCurrent) != 0) {
                    $qtyDiterima =  (count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima']);
                    $penerimaanBarangDetail = $penerimaanMutasiGlobalDetailModel
                        ->where('penerimaan_mutasi_global_id', $penerimaanMutasiGlobalID)
                        ->where('mutasi_global_id', $m['mutasi_global_id'])
                        ->where('mutasi_global_detail_id', $m['id'])
                        ->first();

                    $stockMutasi = $stockModel->find($penerimaanBarangDetail['stock_mutasi_id']);

                    if ($m['tipe_barang'] == 'kemasan') {
                        // Kemasan
                        $kemasan = $kemasanModel->find($stockMutasi['kemasan_id']);
                        $satuanMutasi = $satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                        $barangMutasi = $kemasan['name'];
                        $kodeBarangMutasi = $kemasan['kode'];
                    } else {
                        // Barang
                        $barangSpesifikasi = $barangMasterModel
                            ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang, barang_master.kode_barang")
                            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                            ->where('barang_master_spesifikasi.id', $stockMutasi['barang2_id'])
                            ->where('barang_master_spesifikasi.barang_master_id', $stockMutasi['barang1_id'])
                            ->first();

                        $satuanMutasi = $satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                        $barangMutasi = $barangSpesifikasi['barang'];
                        $kodeBarangMutasi = $barangSpesifikasi['kode_barang'];
                    }

                    array_push($barangResult, [
                        'mutasi_global_id' => $m['mutasi_global_id'],
                        'mutasi_global_detail_id' => $m['id'],
                        'stock_asal_id' => $m['stock_id'],
                        'bc_asal_id' => $m['bc_id'],
                        'no_aju_asal' => $m['no_aju'],
                        'stock_dokumen_asal' => $m['stock_dokumen'],
                        'stock_date_asal' => $stockListDetailAsal != null ? date('d/m/Y', strtotime($stockListDetailAsal['stock_date'])) : "-",
                        'bc_asal_name' => $m['bc_name'] == null ? "NON PABEAN" : $m['bc_name'],
                        'divisi_asal_name' => $m['divisi_asal_name'],
                        'warehouse_asal_name' => $m['warehouse_asal_name'],
                        // ---
                        'stock_mutasi_id' => $penerimaanBarangDetail['stock_mutasi_id'],
                        'bc_mutasi_id' => $bcMutasiId['id'],
                        'bc_mutasi_name' => $bcMutasiId['value'],
                        'no_aju_mutasi' => $bc27['no_aju'],
                        // -----                       
                        'qty' => $m['qty'],
                        'qty_diterima_all' => count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima'],
                        'qty_diterima_current' => count($penerimaanTotalCurrent) == 0 ? 0 : $penerimaanTotalCurrent[0]['qty_diterima'],
                        'qty_sisa' => $m['qty'] - $qtyDiterima,
                        'no_mutasi' => $m['no_mutasi'],
                        'tipe_barang_text' => strtoupper(str_replace('_', ' ', $m['tipe_barang'])),
                        'barang' => $barang,
                        'satuan' => $satuan,
                        'kode_barang' => $kodeBarang,
                        'supplier_name' => $stockListDetailAsal['supplier_name'],
                        'no_po' => $stockListDetailAsal['no_po'],
                        'kode_barang_diterima' => $kodeBarangMutasi,
                        'barang_diterima' => $barangMutasi,
                        'satuan_diterima' => $satuanMutasi,
                        'tipe_barang' => $m['tipe_barang']
                    ]);
                }
            }
        }

        return $barangResult;
    }

    public function getMutasiNo($mutasiIDArr)
    {
        $mutasiGlobalModel = new MutasiGlobalModel();
        $result = $mutasiGlobalModel->whereIn('id', $mutasiIDArr)->findAll();
        $response = array();
        foreach ($result as $r) {
            array_push($response, $r['no_mutasi']);
        }
        return $response;
    }


    public function getListNomorMutasi($companyPengirimId)
    {
        $mutasiGlobalModel = new MutasiGlobalModel();
        $penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();

        $listMutasi = $mutasiGlobalModel
            ->select('mutasi_global.id, mutasi_global.no_mutasi, SUM(qty) AS qty_mutasi')
            ->join('mutasi_global_detail', 'mutasi_global_detail.mutasi_global_id = mutasi_global.id', 'left')
            ->where('mutasi_global.company_asal_id', $companyPengirimId)
            ->where('mutasi_global.deletedAt', null)
            ->where('mutasi_global_detail.deletedAt', null)
            ->groupBy('mutasi_global_detail.mutasi_global_id')
            ->orderBy('mutasi_global.no_mutasi', "ASC")
            ->findAll();

        $mutasiResult = [];

        foreach ($listMutasi as $mutasi) {
            $penerimaanTotal = $penerimaanMutasiGlobalDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_global_id', $mutasi['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_global_id')
                ->findAll();

            if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $mutasi['qty_mutasi']) {
                array_push($mutasiResult, $mutasi);
            }
        }

        return $mutasiResult;
    }

    public function get_no($bln, $thn, $last_day, $divisiName, $divisiID)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('penerimaan_mutasi_global');
        $builder->select('penerimaan_mutasi_no');
        $builder->orderBy('penerimaan_mutasi_no', 'desc');
        $builder->where('penerimaan_mutasi_global.divisi_penerima_id', $divisiID);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('penerimaan_mutasi_no', $lastStr);
        $query = $builder->get();

        $kode = 'PMG/' . $divisiName;

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



    public function getPenerimaanBarangListReportBc27($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_aju'      => 'bc_27.no_aju'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_mutasi_global.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi_global.penerimaan_mutasi_no,
        penerimaan_mutasi_global.multiple_mutasi_id,
        penerimaan_mutasi_global_detail.stock_mutasi_id,
        penerimaan_mutasi_global.tanggal,
        penerimaan_mutasi_global_detail.mutasi_global_id,

        mutasi_global_detail.stock_id AS stock_id_asal,
        mutasi_global_detail.bc_id AS bc_id_asal,
        mutasi_global_detail.no_aju AS no_aju_asal,
        mutasi_global_detail.stock_dokumen AS stock_dokumen_asal,
        
        divisis.divisi AS divisi_penerima,
        warehouses.warehouse_name AS warehouse_penerima,    
           
        mutasi_global_detail.qty AS qty,    
        penerimaan_mutasi_global_detail.qty AS jml_masuk,

        mutasi_global.tanggal AS tanggal_bc, 

        bc_27.no_aju AS no_aju,    
        bc_27.no_daftar AS no_daftar,    
          
        stock.tipe_barang,    
        stock.barang1_id,    
        stock.barang2_id,    
        stock.kemasan_id,    
          
        ";

        $penerimaanMutasiGlobal = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id', 'left')
            ->join('penerimaan_mutasi_global_detail', 'penerimaan_mutasi_global_detail.penerimaan_mutasi_global_id = penerimaan_mutasi_global.id', 'left')
            ->join('mutasi_global', 'mutasi_global.id = penerimaan_mutasi_global_detail.mutasi_global_id', 'left')
            ->join('mutasi_global_detail', 'mutasi_global_detail.id = penerimaan_mutasi_global_detail.mutasi_global_detail_id', 'left')
            ->join('bc_27', 'bc_27.mutasi_global_id = penerimaan_mutasi_global_detail.mutasi_global_id', 'left')
            ->join('stock', 'stock.id = mutasi_global_detail.stock_id', 'left')

            ->where('penerimaan_mutasi_global.company_penerima_id', $addCondition['company_id'])
            // ->where('bc_27.company_asal_id', $addCondition['company_id'])
            ->where('penerimaan_mutasi_global.deletedAt', null)
            ->where('penerimaan_mutasi_global_detail.deletedAt', null)
            ->where('penerimaan_mutasi_global.status_posting', '1')

            ->orderBy($sort, $sortType);

        $totalData = $penerimaanMutasiGlobal->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanMutasiGlobal->where('mutasi_global.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->where('mutasi_global.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->groupEnd();
        }

        $totalFilteredData = $penerimaanMutasiGlobal->countAllResults(false);
        $data = $penerimaanMutasiGlobal->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportBc27PDF($addCondition)
    {
        $availableSort = [
            'no_aju'      => 'bc_27.no_aju'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_mutasi_global.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi_global.penerimaan_mutasi_no,
        penerimaan_mutasi_global.multiple_mutasi_id,
        penerimaan_mutasi_global_detail.stock_mutasi_id,
        penerimaan_mutasi_global.tanggal,
        penerimaan_mutasi_global_detail.mutasi_global_id,

        mutasi_global_detail.stock_id AS stock_id_asal,
        mutasi_global_detail.bc_id AS bc_id_asal,
        mutasi_global_detail.no_aju AS no_aju_asal,
        mutasi_global_detail.stock_dokumen AS stock_dokumen_asal,
        
        divisis.divisi AS divisi_penerima,
        warehouses.warehouse_name AS warehouse_penerima,    
           
        mutasi_global_detail.qty AS qty,    
        penerimaan_mutasi_global_detail.qty AS jml_masuk,

        mutasi_global.tanggal AS tanggal_bc,

        bc_27.no_aju AS no_aju,    
        bc_27.no_daftar AS no_daftar,    
           
        stock.tipe_barang,    
        stock.barang1_id,    
        stock.barang2_id,    
        stock.kemasan_id,    
          
        ";

        $penerimaanMutasiGlobal = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id', 'left')
            ->join('penerimaan_mutasi_global_detail', 'penerimaan_mutasi_global_detail.penerimaan_mutasi_global_id = penerimaan_mutasi_global.id', 'left')
            ->join('mutasi_global', 'mutasi_global.id = penerimaan_mutasi_global_detail.mutasi_global_id', 'left')
            ->join('mutasi_global_detail', 'mutasi_global_detail.id = penerimaan_mutasi_global_detail.mutasi_global_detail_id', 'left')
            ->join('bc_27', 'bc_27.mutasi_global_id = mutasi_global.id', 'left')
            ->join('stock', 'stock.id = mutasi_global_detail.stock_id', 'left')

            ->where('penerimaan_mutasi_global.company_penerima_id', $addCondition['company_id'])
            ->where('penerimaan_mutasi_global.deletedAt', null)
            ->where('penerimaan_mutasi_global_detail.deletedAt', null)
            ->where('penerimaan_mutasi_global.status_posting', '1')

            ->orderBy($sort, $sortType);

        $totalData = $penerimaanMutasiGlobal->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanMutasiGlobal->where('mutasi_global.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->where('mutasi_global.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->groupEnd();
        }

        $totalFilteredData = $penerimaanMutasiGlobal->countAllResults(false);
        $data = $penerimaanMutasiGlobal->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }
}
