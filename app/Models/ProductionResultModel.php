<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductionResultModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'production_results';
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

    public function getProductResultList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'productionCode' => 'production_results.pr_no',
            'workOrderCode' => 'suppliers.kode',
            'barangCode'    => 'barangs.kode_barang',
            'barangName'    => 'barangs.nama_barang',
            'warehouseName' => 'warehouses.warehouse_name',
            'createdAt'     => 'production_results.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'production_results.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "production_results.*, 
                      DATE_FORMAT(production_results.receive_date, '%d/%m/%Y') AS receives_date,
                      work_orders.wo_no AS wo_no,
                      barang_master.kode_barang AS barangCode,
                      work_order_details.nama_barang AS barangName
                      ";
        $productionResDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('work_orders', 'work_orders.id = production_results.work_order_id', 'left')
            ->join('work_order_details', 'work_order_details.work_order_id = production_results.work_order_id', 'left')
            ->join('barang_master', 'barang_master.id = work_order_details.barang1_id', 'left')
            // ->groupBy('production_results.work_order_id')
            ->orderBy($sort, $sortType);

        $totalData = $productionResDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $productionResDataQry->groupStart()
                ->like('production_results.pr_no', $addCondition['search'], 'after')
                ->orLike('work_orders.wo_no', $addCondition['search'], 'after')
                ->orLike('barang_master.nama_barang', $addCondition['search'], 'after')
                ->groupEnd();
        }

        $totalFilteredData = $productionResDataQry->countAllResults(false);
        $data = $productionResDataQry->findAll($limit, $offset);
        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getDataProductionResultWithDetail($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
        barang_master.barang_name, 
        barang_master.kode_barang, 
        barang_master_spesifikasi.spesifikasi,
        production_result_details.id as production_result_detail_id,
        production_result_details.production_result_id as production_result_id,
        production_result_details.barang1_id as barang1_id,
        production_result_details.barang2_id as barang2_id,
        production_result_details.bc_id as bc_id,
        production_result_details.stock_id as stock_id,
        production_result_details.no_aju as no_aju,
        production_result_details.stock_dokumen as stock_dokumen,
        production_result_details.qty as qty,
        production_result_details.qty2 as qty2,
        production_result_details.qty_isi as qty_isi,
        SUM(production_result_details.qty) as qtyTotal,
        satuans.kode_satuan,
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('production_result_details', 'production_result_details.production_result_id = production_results.id', 'left')
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('work_orders', 'work_orders.id = production_results.work_order_id', 'left')
            // ->join('account_barang', 'account_barang.barang_master_id = production_result_details.barang1_id', 'left')
            ->like('production_results.receive_date', $where['tanggal_jurnal'])
            ->where('production_result_details.type', 'JADI')
            ->where('work_orders.divisi_id', $where['divisi_id'])
            // ->where('account_barang.divisi_id', $where['divisi_id'])
            // ->where('account_barang.kategori_id', $where['kategori_id'])
            ->where('production_result_details.deletedAt', $where['deletedAt'])
            ->where('production_results.deletedAt', $where['deletedAt'])
            ->groupBy('production_result_details.barang1_id, production_result_details.barang2_id')
            ->findAll();

        return $dataQry;
    }

    public function getDataProductionResultBahanBakuWithDetail($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
        barang_master.barang_name, 
        barang_master_spesifikasi.spesifikasi,
        production_result_details.barang1_id,
        production_result_details.barang2_id,
        production_result_details.stock_id,
        stock_details2.stock_dokumen AS stock_dokumen2,
        stock_details.no_dokumen,
        production_result_details.stock_dokumen AS stock_dokumen
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('production_result_details', 'production_result_details.production_result_id = production_results.id', 'left')
            ->join('stock_details2', 'stock_details2.stock_dokumen = production_result_details.stock_dokumen', 'left')
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id AND stock_details.sumber = "LPB"', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.no_penerimaan_barang = stock_details.no_dokumen', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.barang_id = production_result_details.barang1_id AND penerimaan_barang_detail.spesifikasi_id = production_result_details.barang2_id', 'left')
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('work_orders', 'work_orders.id = production_results.work_order_id', 'left')
            ->like('production_results.receive_date', $where['tanggal_jurnal'])
            ->where('work_orders.divisi_id', $where['divisi_id'])
            ->where('production_result_details.type', 'DIGUNAKAN')
            ->where('production_results.is_posted', '1')
            ->where('production_result_details.barang_type', 'bahan_baku')
            ->where('production_result_details.deletedAt', $where['deletedAt'])
            ->where('production_results.deletedAt', $where['deletedAt'])
            ->groupBy('production_result_details.stock_dokumen, production_result_details.stock_id')
            ->findAll();

        return $dataQry;
    }

    public function getDataProductionResultBahanBakuJadiWithDetail($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
        barang_master.barang_name, 
        barang_master_spesifikasi.spesifikasi,
        production_result_details.barang1_id,
        production_result_details.barang2_id,
        stock_details2.stock_dokumen AS stock_dokumen2,
        stock_details2.harga_umum AS harga_umum,
        stock_details2.harga_harian AS harga_harian,
        stock_details2.harga_bulanan AS harga_bulanan,
        stock_details.no_dokumen,
        production_result_details.stock_dokumen AS stock_dokumen
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('production_result_details', 'production_result_details.production_result_id = production_results.id', 'left')
            ->join('stock_details2', 'stock_details2.stock_dokumen = production_result_details.stock_dokumen', 'left')
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('work_orders', 'work_orders.id = production_results.work_order_id', 'left')
            ->like('production_results.receive_date', $where['tanggal_jurnal'])
            ->where('work_orders.divisi_id', $where['divisi_id'])
            ->where('production_result_details.type', 'DIGUNAKAN')
            ->where('production_results.is_posted', '1')
            ->where('production_result_details.barang_type', 'bahan_jadi')
            ->where('production_result_details.deletedAt', $where['deletedAt'])
            ->where('production_results.deletedAt', $where['deletedAt'])
            ->groupBy('production_result_details.stock_dokumen, production_result_details.stock_id')
            ->findAll();

        return $dataQry;
    }

    public function getDataProductionResultBahanPenolongWithDetail($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
        barang_master.kode_barang, 
        barang_master.barang_name, 
        barang_master.parent_type_id, 
        barang_master_spesifikasi.spesifikasi,
        production_result_details.production_result_id,
        production_result_details.barang1_id,
        production_result_details.barang2_id,
        production_result_details.qty as qty_produksi,
        stock_details2.stock_dokumen,
        stock_details.no_dokumen,
        parent_barang.parent_name
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('production_result_details', 'production_result_details.production_result_id = production_results.id', 'left')
            ->join('stock_details2', 'stock_details2.stock_id = production_result_details.stock_id AND stock_details2.stock_dokumen = production_result_details.stock_dokumen', 'left')
            ->join('stock_details', 'stock_details.stock_id = production_result_details.stock_id AND stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.no_penerimaan_barang = stock_details.no_dokumen', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.barang_id = production_result_details.barang1_id AND penerimaan_barang_detail.spesifikasi_id = production_result_details.barang2_id', 'left')
            ->join('barang_master', 'barang_master.id = production_result_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = production_result_details.barang2_id', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('work_orders', 'work_orders.id = production_results.work_order_id', 'left')
            ->like('production_results.receive_date', $where['tanggal_jurnal'])
            ->where('work_orders.divisi_id', $where['divisi_id'])
            ->where('production_result_details.type', 'DIGUNAKAN')
            ->where('production_result_details.barang_type', 'bahan_penolong')
            ->where('production_result_details.deletedAt', $where['deletedAt'])
            ->where('production_results.deletedAt', $where['deletedAt'])
            ->findAll();

        return $dataQry;
    }
}
