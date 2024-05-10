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
            'penerimaan_mutasi_no'                => 'penerimaan_mutasi_no',
            'penerimaan_mutasi.tanggal'           => 'penerimaan_mutasi.tanggal',
            'penerimaan_mutasi.warehouse_id'      => 'penerimaan_mutasi.warehouse_id',
            'penerimaan_mutasi.jenis_mutasi'      => 'penerimaan_mutasi.jenis_mutasi',
            'penerimaan_mutasi.bc_no'             => 'penerimaan_mutasi.bc_no'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi.*,
        warehouses.warehouse_name AS warehouse_tujuan,
        divisis.divisi AS divisi_tujuan
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->where($condition)
            ->whereIn('penerimaan_mutasi.divisi_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['status'] || $addCondition['penerimaan_mutasi_no']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('penerimaan_mutasi.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['penerimaan_mutasi_no']) {
            $dataQry->like('penerimaan_mutasi_no', $addCondition['penerimaan_mutasi_no']);
        }

        if ($addCondition['divisi_id'] || $addCondition['status'] || $addCondition['penerimaan_mutasi_no']) {
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

    public function getListNomorMutasi($warehouseID)
    {
        $mutasiModel = new MutasiModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();

        $listMutasi = $mutasiModel
            ->select('mutasi.id, mutasi.no_mutasi, SUM(qty) AS qty_mutasi')
            ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
            ->where('mutasi.warehouse_tujuan_id', $warehouseID)
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

        $selectQry = "
            mutasi.no_mutasi, 
            mutasi_detail.*, 
            stock.tipe_barang, 
            metadata.value AS bc_name
        ";

        $mutasiDetailList = $mutasiDetailModel
            ->select($selectQry)
            ->join('mutasi', 'mutasi.id = mutasi_detail.mutasi_id')
            ->join('stock', 'stock.id = mutasi_detail.stock_id', 'left')
            ->join('metadata', 'metadata.id = mutasi_detail.bc_id', 'left')
            ->whereIn('mutasi_id', $mutasiArrID)
            ->where('mutasi_detail.deletedAt', null)
            ->findAll();

        $barangResult = [];

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

            if ($m['tipe_barang'] == 'kemasan') {
                // Kemasan
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id'])['kode_satuan'];
                $barang = $kemasan['name'];
            } else {
                // Barang
                $barangSpesifikasi = $barangMasterModel
                    ->select("barang_master_spesifikasi.satuan_1, CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang")
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                    ->where('barang_master_spesifikasi.id', $stock['barang2_id'])
                    ->where('barang_master_spesifikasi.barang_master_id', $stock['barang1_id'])
                    ->first();
                $satuan = $satuanModel->find($barangSpesifikasi['satuan_1'])['kode_satuan'];
                $barang = $barangSpesifikasi['barang'];
            }

            if ($penerimaanMutasiID == null) {
                if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $m['qty']) {
                    $qtyDiterima =  (count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima']);
                    array_push($barangResult, [
                        'mutasi_id' => $m['mutasi_id'],
                        'mutasi_detail_id' => $m['id'],
                        'stock_id' => $m['stock_id'],
                        'bc_id' => $m['bc_id'],
                        'qty' => $m['qty'],
                        'qty_diterima_all' => count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima'],
                        'qty_diterima_current' => count($penerimaanTotalCurrent) == 0 ? 0 : $penerimaanTotalCurrent[0]['qty_diterima'],
                        'qty_sisa' => $m['qty'] - $qtyDiterima,
                        'stock_date' => $stockListDetail != null ? date('d/m/Y', strtotime($stockListDetail['stock_date'])) : "-",
                        // --
                        'no_mutasi' => $m['no_mutasi'],
                        'tipe_barang' => strtoupper(str_replace('_', ' ', $m['tipe_barang'])),
                        'bc_name' => $m['bc_name'] == null ? "NON PABEAN" : $m['bc_name'],
                        'no_aju' => $m['no_aju'],
                        'barang' => $barang,
                        'satuan' => $satuan
                    ]);
                }
            } else {
                if (count($penerimaanTotal) != 0 &&  count($penerimaanTotalCurrent) != 0) {
                    $qtyDiterima =  (count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima']);
                    array_push($barangResult, [
                        'mutasi_id' => $m['mutasi_id'],
                        'mutasi_detail_id' => $m['id'],
                        'stock_id' => $m['stock_id'],
                        'bc_id' => $m['bc_id'],
                        'qty' => $m['qty'],
                        'qty_diterima_all' => count($penerimaanTotal) == 0 ? 0 : $penerimaanTotal[0]['qty_diterima'],
                        'qty_diterima_current' => count($penerimaanTotalCurrent) == 0 ? 0 : $penerimaanTotalCurrent[0]['qty_diterima'],
                        'qty_sisa' => $m['qty'] - $qtyDiterima,
                        'stock_date' => $stockListDetail != null ? date('d/m/Y', strtotime($stockListDetail['stock_date'])) : "-",
                        // --
                        'no_mutasi' => $m['no_mutasi'],
                        'tipe_barang' => strtoupper(str_replace('_', ' ', $m['tipe_barang'])),
                        'bc_name' => $m['bc_name'] == null ? "NON PABEAN" : $m['bc_name'],
                        'no_aju' => $m['no_aju'],
                        'barang' => $barang,
                        'satuan' => $satuan
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

    public function get_no($bln, $thn, $last_day, $warehouseKode, $warehouseID)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('penerimaan_mutasi');
        $builder->select('penerimaan_mutasi_no');
        $builder->orderBy('penerimaan_mutasi_no', 'desc');
        $builder->where('penerimaan_mutasi.warehouse_id', $warehouseID);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('penerimaan_mutasi_no', $lastStr);
        $query = $builder->get();

        $kode = 'PMU/' . $warehouseKode;

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
}
