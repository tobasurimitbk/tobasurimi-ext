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

            // PENERIMAAN BARANG DETAIL (FIRST)
            $penerimaanBarangDetail = "";

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
                        'qty_barang_diterima' => "-",
                        'satuan_diterima' => "-",
                        'tipe_barang' => $m['tipe_barang']
                    ]);
                }
            } else {
                if (count($penerimaanTotal) != 0 &&  count($penerimaanTotalCurrent) != 0) {
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
                        'kode_barang_diterima' => "-",
                        'barang_diterima' => "-",
                        'qty_barang_diterima' => "-",
                        'satuan_diterima' => "-",
                        'tipe_barang' => $m['tipe_barang']
                    ]);
                }
            }
        }

        return $barangResult;
    }

    // public function getDetailBarangDiterima($penerimaanBarangDetailId)
    // {
    //     $penerimaanMutasiGlobalDetailModel = new PenerimaanBara
    // }


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
}
